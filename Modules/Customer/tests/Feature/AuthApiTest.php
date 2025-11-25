<?php

namespace Modules\Customer\Tests\Feature;

use Tests\TestCase;
use Modules\Customer\app\Models\Customer;
use Modules\Customer\app\Models\CustomerOtp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /**
     * TEST 1: User Registration
     * POST /api/auth/register
     *
     * Expected: User created, OTP saved with verification token, email sent
     */
    public function test_user_can_register_with_valid_data()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '+1234567890',
            'lang' => 'en',
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'success',
            'data' => [
                'otp',
                'verification_token',
            ],
        ]);

        // Verify customer created
        $this->assertDatabaseHas('customers', [
            'email' => 'john@example.com',
            'first_name' => 'John',
        ]);

        // Verify OTP created with verification token
        $otp = CustomerOtp::where('email', 'john@example.com')->first();
        $this->assertNotNull($otp->verification_token);
        $this->assertNull($otp->verified_at);
    }

    /**
     * TEST 2: Registration Validation
     * POST /api/auth/register
     *
     * Expected: Validation errors for invalid data
     */
    public function test_registration_fails_with_invalid_email()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '+1234567890',
            'lang' => 'en',
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    public function test_registration_fails_with_mismatched_passwords()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'DifferentPassword123!',
            'phone' => '+1234567890',
            'lang' => 'en',
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    public function test_registration_fails_with_duplicate_email()
    {
        Customer::factory()->create(['email' => 'john@example.com']);

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '+1234567890',
            'lang' => 'en',
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /**
     * TEST 3: Email Verification via Token
     * GET /verify-email/{token}
     *
     * Expected: Email verified, user redirected to landing page
     */
    public function test_user_can_verify_email_with_valid_token()
    {
        // Create customer and OTP with verification token
        $customer = Customer::factory()->create(['email' => 'john@example.com']);
        $otp = CustomerOtp::create([
            'email' => 'john@example.com',
            'otp' => '123456',
            'type' => 'email_verification',
            'verification_token' => 'valid-token-123',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->get('/verify-email/valid-token-123');

        $response->assertRedirect('/');
        $response->assertSessionHas('success');

        // Verify email marked as verified
        $otp->refresh();
        $this->assertNotNull($otp->verified_at);
    }

    public function test_email_verification_fails_with_invalid_token()
    {
        $response = $this->get('/verify-email/invalid-token');

        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    public function test_email_verification_fails_with_expired_token()
    {
        $customer = Customer::factory()->create(['email' => 'john@example.com']);
        $otp = CustomerOtp::create([
            'email' => 'john@example.com',
            'otp' => '123456',
            'type' => 'email_verification',
            'verification_token' => 'expired-token',
            'expires_at' => now()->subMinutes(5),
        ]);

        $response = $this->get('/verify-email/expired-token');

        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    /**
     * TEST 4: Login
     * POST /api/auth/login
     *
     * Expected: User can only login if email is verified
     */
    public function test_user_can_login_with_verified_email()
    {
        $customer = Customer::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('Password123!'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'fcm_token' => 'fcm-token-123',
            'deviceId' => 'device-123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'access_token',
                'refresh_token',
            ],
        ]);
    }

    public function test_user_cannot_login_with_unverified_email()
    {
        $customer = Customer::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('Password123!'),
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'fcm_token' => 'fcm-token-123',
            'deviceId' => 'device-123',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_fails_with_wrong_password()
    {
        $customer = Customer::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('Password123!'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'john@example.com',
            'password' => 'WrongPassword123!',
            'fcm_token' => 'fcm-token-123',
            'deviceId' => 'device-123',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST 5: Resend OTP
     * POST /api/auth/resend-otp
     *
     * Expected: New OTP created with new verification token, email sent
     */
    public function test_user_can_resend_otp()
    {
        $customer = Customer::factory()->create(['email' => 'john@example.com']);

        $response = $this->postJson('/api/auth/resend-otp', [
            'email' => 'john@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'otp',
                'verification_token',
            ],
        ]);

        // Verify new OTP created
        $otp = CustomerOtp::where('email', 'john@example.com')
            ->where('type', 'email_verification')
            ->latest()
            ->first();
        $this->assertNotNull($otp->verification_token);
    }

    public function test_resend_otp_fails_for_nonexistent_email()
    {
        $response = $this->postJson('/api/auth/resend-otp', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST 6: Refresh Token
     * POST /api/auth/refresh
     *
     * Expected: New access token returned
     */
    public function test_user_can_refresh_token()
    {
        $customer = Customer::factory()->create([
            'email' => 'john@example.com',
            'email_verified_at' => now(),
        ]);

        // Create refresh token
        $tokens = $customer->createToken('auth_token');
        $refreshToken = $customer->createRefreshToken($tokens->plainTextToken);

        $response = $this->postJson('/api/auth/refresh', [
            'refresh_token' => $refreshToken,
            'deviceId' => 'device-123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'access_token',
                'refresh_token',
            ],
        ]);
    }

    public function test_refresh_token_fails_with_invalid_token()
    {
        $response = $this->postJson('/api/auth/refresh', [
            'refresh_token' => 'invalid-token',
            'deviceId' => 'device-123',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST 7: Request Password Reset
     * POST /api/auth/request-password-reset
     *
     * Expected: OTP created, email sent with reset link
     */
    public function test_user_can_request_password_reset()
    {
        $customer = Customer::factory()->create(['email' => 'john@example.com']);

        $response = $this->postJson('/api/auth/request-password-reset', [
            'email' => 'john@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'otp',
                'verification_token',
            ],
        ]);

        // Verify password reset OTP created
        $otp = CustomerOtp::where('email', 'john@example.com')
            ->where('type', 'password_reset')
            ->first();
        $this->assertNotNull($otp);
    }

    public function test_request_password_reset_fails_for_nonexistent_email()
    {
        $response = $this->postJson('/api/auth/request-password-reset', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST 8: Verify Reset OTP
     * POST /api/auth/verify-reset-otp
     *
     * Expected: Returns reset token if OTP valid
     */
    public function test_user_can_verify_reset_otp()
    {
        $customer = Customer::factory()->create(['email' => 'john@example.com']);
        $otp = CustomerOtp::create([
            'email' => 'john@example.com',
            'otp' => '123456',
            'type' => 'password_reset',
            'verification_token' => 'reset-token-123',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/auth/verify-reset-otp', [
            'email' => 'john@example.com',
            'otp' => '123456',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'reset_token',
            ],
        ]);
    }

    public function test_verify_reset_otp_fails_with_invalid_otp()
    {
        $customer = Customer::factory()->create(['email' => 'john@example.com']);
        $otp = CustomerOtp::create([
            'email' => 'john@example.com',
            'otp' => '123456',
            'type' => 'password_reset',
            'verification_token' => 'reset-token-123',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/auth/verify-reset-otp', [
            'email' => 'john@example.com',
            'otp' => '999999',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST 9: Reset Password
     * POST /api/auth/reset-password
     *
     * Expected: Password updated, tokens returned
     */
    public function test_user_can_reset_password()
    {
        $customer = Customer::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('OldPassword123!'),
        ]);

        $otp = CustomerOtp::create([
            'email' => 'john@example.com',
            'otp' => '123456',
            'type' => 'password_reset',
            'verification_token' => 'reset-token-123',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'john@example.com',
            'otp' => '123456',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
            'deviceId' => 'device-123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'access_token',
                'refresh_token',
            ],
        ]);

        // Verify password changed
        $this->assertTrue(
            Hash::check('NewPassword123!', $customer->fresh()->password)
        );
    }

    public function test_reset_password_fails_with_mismatched_passwords()
    {
        $customer = Customer::factory()->create(['email' => 'john@example.com']);

        $response = $this->postJson('/api/auth/reset-password', [
            'email' => 'john@example.com',
            'otp' => '123456',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'DifferentPassword123!',
            'deviceId' => 'device-123',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST 10: Change Language
     * POST /api/auth/change-language
     *
     * Expected: Customer language updated
     */
    public function test_user_can_change_language_to_arabic()
    {
        $customer = Customer::factory()->create([
            'email' => 'john@example.com',
            'language' => 'en',
        ]);

        $this->actingAs($customer, 'sanctum');

        $response = $this->postJson('/api/auth/change-language', [
            'language' => 'ar',
        ]);

        $response->assertStatus(200);

        $customer->refresh();
        $this->assertEquals('ar', $customer->language);
    }

    public function test_user_can_change_language_to_english()
    {
        $customer = Customer::factory()->create([
            'email' => 'john@example.com',
            'language' => 'ar',
        ]);

        $this->actingAs($customer, 'sanctum');

        $response = $this->postJson('/api/auth/change-language', [
            'language' => 'en',
        ]);

        $response->assertStatus(200);

        $customer->refresh();
        $this->assertEquals('en', $customer->language);
    }

    public function test_change_language_fails_with_invalid_language()
    {
        $customer = Customer::factory()->create(['email' => 'john@example.com']);

        $this->actingAs($customer, 'sanctum');

        $response = $this->postJson('/api/auth/change-language', [
            'language' => 'invalid',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST 11: Get Profile
     * GET /api/auth/profile
     *
     * Expected: Customer profile returned
     */
    public function test_user_can_get_profile()
    {
        $customer = Customer::factory()->create([
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->actingAs($customer, 'sanctum');

        $response = $this->getJson('/api/auth/profile');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'first_name',
                'last_name',
                'email',
                'phone',
            ],
        ]);
    }

    public function test_get_profile_fails_without_authentication()
    {
        $response = $this->getJson('/api/auth/profile');

        $response->assertStatus(401);
    }

    /**
     * TEST 12: Update Profile
     * PUT /api/auth/profile
     *
     * Expected: Customer profile updated
     */
    public function test_user_can_update_profile()
    {
        $customer = Customer::factory()->create([
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->actingAs($customer, 'sanctum');

        $response = $this->putJson('/api/auth/profile', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'phone' => '+9876543210',
        ]);

        $response->assertStatus(200);

        $customer->refresh();
        $this->assertEquals('Jane', $customer->first_name);
        $this->assertEquals('Smith', $customer->last_name);
        $this->assertEquals('+9876543210', $customer->phone);
    }

    public function test_update_profile_fails_with_duplicate_email()
    {
        $customer1 = Customer::factory()->create(['email' => 'john@example.com']);
        $customer2 = Customer::factory()->create(['email' => 'jane@example.com']);

        $this->actingAs($customer2, 'sanctum');

        $response = $this->putJson('/api/auth/profile', [
            'email' => 'john@example.com',
        ]);

        $response->assertStatus(422);
    }

    /**
     * TEST 13: Logout
     * POST /api/auth/logout
     *
     * Expected: Current device tokens revoked
     */
    public function test_user_can_logout()
    {
        $customer = Customer::factory()->create(['email' => 'john@example.com']);

        $this->actingAs($customer, 'sanctum');

        $response = $this->postJson('/api/auth/logout', [
            'deviceId' => 'device-123',
        ]);

        $response->assertStatus(200);
    }

    /**
     * TEST 14: Logout All Devices
     * POST /api/auth/logout-devices
     *
     * Expected: All device tokens revoked
     */
    public function test_user_can_logout_from_all_devices()
    {
        $customer = Customer::factory()->create(['email' => 'john@example.com']);

        $this->actingAs($customer, 'sanctum');

        $response = $this->postJson('/api/auth/logout-devices');

        $response->assertStatus(200);
    }

    /**
     * TEST 15: Transaction Rollback on Email Failure
     *
     * Expected: Customer not created if email sending fails
     */
    public function test_registration_rolls_back_on_email_failure()
    {
        Mail::shouldReceive('to')->andThrow(new \Exception('Mail sending failed'));

        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'phone' => '+1234567890',
            'lang' => 'en',
        ];

        $response = $this->postJson('/api/auth/register', $data);

        $response->assertStatus(500);

        // Verify customer NOT created
        $this->assertDatabaseMissing('customers', [
            'email' => 'john@example.com',
        ]);
    }
}
