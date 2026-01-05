<?php

namespace Modules\Order\app\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Order\app\Models\Order;
use Modules\Order\app\Models\Payment;

class PaymobService
{
    protected string $baseUrl;
    protected string $secretKey;
    protected string $publicKey;

    public function __construct()
    {
        $this->baseUrl = config('paymob.base_url');
        $this->secretKey = config('paymob.secret_key');
        $this->publicKey = config('paymob.public_key');
    }

    /**
     * Create a payment intent for an order
     */
    public function createPaymentIntent(array $data)
    {
        // Generate unique reference: order_id + timestamp to avoid duplicates
        $uniqueReference = ($data['order_id'] ?? 'ORD') . '_' . time() . '_' . uniqid();
        
        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/intention/', [
            'amount' => $data['amount'],
            'currency' => 'EGP',
            'payment_methods' => [$this->getIntegrationId($data['method'])],
            'billing_data' => [
                'first_name' => $data['billing_data']['first_name'],
                'last_name' => $data['billing_data']['last_name'] ?? 'N/A',
                'email' => $data['billing_data']['email'],
                'phone_number' => $data['billing_data']['phone_number'],
                'apartment' => 'N/A',
                'floor' => 'N/A',
                'street' => 'N/A',
                'building' => 'N/A',
                'shipping_method' => 'N/A',
                'postal_code' => 'N/A',
                'city' => 'N/A',
                'country' => 'EG',
                'state' => 'N/A',
            ],
            'special_reference' => $uniqueReference,
            'notification_url' => config('paymob.webhook_url'),
            'redirection_url' => config('paymob.callback_url'),
        ]);

        if ($response->failed()) {
            $responseData = $response->json();
            Log::error('Paymob create payment intent failed', [
                'response' => $responseData,
                'status' => $response->status(),
            ]);
            
            // Extract error details
            $errorMessage = $responseData['message'] ?? null;
            $errorDetails = $responseData['detail'] ?? null;
            $merchantOrderError = $responseData['merchant_order_id'] ?? null;
            
            throw new Exception(json_encode([
                'error' => 'Failed to create payment intent',
                'message' => $errorMessage,
                'detail' => $errorDetails,
                'merchant_order_id' => $merchantOrderError,
                'status' => $response->status(),
            ]));
        }

        return $response->json();
    }

    /**
     * Retrieve transaction details from Paymob
     */
    public function retrieveTransaction(string $transactionId): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->get($this->baseUrl . '/acceptance/transactions/' . $transactionId);

        if ($response->failed()) {
            Log::error('Paymob retrieve transaction failed', [
                'transaction_id' => $transactionId,
                'response' => $response->json(),
            ]);
            return null;
        }

        return $response->json();
    }

    /**
     * Get integration ID based on payment method
     */
    protected function getIntegrationId(string $method): int
    {
        $integrations = config('paymob.integrations');
        
        return match ($method) {
            'card' => (int) $integrations['card'],
            'wallet' => (int) $integrations['wallet'],
            'valu' => (int) $integrations['valu'],
            'souhola' => (int) $integrations['souhola'],
            'forsa' => (int) $integrations['forsa'],
            'bank_installment' => (int) $integrations['bank_installment'],
            default => (int) $integrations['card'],
        };
    }

    /**
     * Generate checkout URL
     */
    public function getCheckoutUrl(string $clientSecret): string
    {
        return config('paymob.checkout_url') . "/?publicKey={$this->publicKey}&clientSecret={$clientSecret}";
    }

    /**
     * Create payment record and return checkout URL
     */
    public function initiatePayment(Order $order, array $billingData, string $method)
    {
        $amountCents = (int) ($order->total_price * 100);

        $paymentIntent = $this->createPaymentIntent([
            'method' => $method,
            'amount' => $amountCents,
            'order_id' => $order->id,
            'billing_data' => $billingData,
        ]);

        // Create payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'paymob_payment_id' => $paymentIntent['id'] ?? null,
            'paymob_order_id' => $paymentIntent['intention_order_id'] ?? null,
            'payment_method' => $method,
            'amount_cents' => $amountCents,
            'status' => Payment::STATUS_PENDING,
            'payment_data' => $paymentIntent,
        ]);

        $checkoutUrl = $this->getCheckoutUrl($paymentIntent['client_secret']);

        return [
            'payment' => $payment,
            'checkout_url' => $checkoutUrl,
            'paymob_order_id' => $paymentIntent['intention_order_id'] ?? null,
            'client_secret' => $paymentIntent['client_secret'] ?? null,
        ];
    }

    /**
     * Handle successful payment
     */
    public function handlePaymentSuccess(Payment $payment, string $transactionId): void
    {
        $payment->update([
            'status' => Payment::STATUS_PAID,
            'transaction_id' => $transactionId,
        ]);

        // Update order payment status - load order if not loaded
        $order = $payment->order ?? Order::find($payment->order_id);
        
        Log::info('Updating order payment status', [
            'order_id' => $payment->order_id,
            'order_found' => $order ? true : false,
            'current_status' => $order?->payment_visa_status,
        ]);
        
        if ($order) {
            // Use withoutGlobalScopes to bypass any country filtering
            $updated = Order::withoutGlobalScopes()
                ->where('id', $payment->order_id)
                ->update([
                    'payment_reference' => $transactionId,
                    'payment_visa_status' => 'success',
                ]);
            
            Log::info('Order update result', [
                'order_id' => $payment->order_id,
                'rows_updated' => $updated,
            ]);
        }

        Log::info('Payment successful', [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
            'transaction_id' => $transactionId,
        ]);
    }

    /**
     * Handle failed payment
     */
    public function handlePaymentFailure(Payment $payment): void
    {
        $payment->update([
            'status' => Payment::STATUS_FAILED,
        ]);

        // Update order payment status - use withoutGlobalScopes
        if ($payment->order_id) {
            Order::withoutGlobalScopes()
                ->where('id', $payment->order_id)
                ->update([
                    'payment_visa_status' => 'fail',
                ]);
        }

        Log::info('Payment failed', [
            'payment_id' => $payment->id,
            'order_id' => $payment->order_id,
        ]);
    }

    /**
     * Find payment by Paymob order ID
     */
    public function findPaymentByPaymobOrderId(string $paymobOrderId): ?Payment
    {
        return Payment::where('paymob_order_id', $paymobOrderId)
            ->latest()
            ->first();
    }

    /**
     * Get payment status info
     */
    public function getPaymentStatusInfo(Payment $payment): array
    {
        $statusMessage = match ($payment->status) {
            Payment::STATUS_PAID => 'payment_success',
            Payment::STATUS_FAILED => 'payment_failed',
            Payment::STATUS_PENDING => 'payment_pending',
            default => 'payment_unknown',
        };

        return [
            'message' => $statusMessage,
            'order_id' => $payment->order_id,
            'status' => $payment->status,
            'amount' => $payment->amount,
        ];
    }

    /**
     * Process callback data and return result
     */
    public function processCallback(array $data): array
    {
        $transactionId = $data['obj']['id'] ?? $data['id'] ?? null;

        if (!$transactionId) {
            throw new Exception('Invalid callback data: missing transaction ID');
        }

        $transaction = $this->retrieveTransaction($transactionId);

        if (!$transaction) {
            throw new Exception('Transaction not found');
        }

        $success = $transaction['success'] ?? false;
        $paymobOrderId = $transaction['order']['id'] ?? null;

        $payment = $this->findPaymentByPaymobOrderId($paymobOrderId);

        if (!$payment) {
            throw new Exception('Payment not found');
        }

        if ($success) {
            $this->handlePaymentSuccess($payment, $transactionId);
            return [
                'success' => true,
                'order_id' => $payment->order_id,
                'status' => 'success',
            ];
        } else {
            $this->handlePaymentFailure($payment);
            return [
                'success' => false,
                'order_id' => $payment->order_id,
                'status' => 'failed',
            ];
        }
    }

    /**
     * Verify HMAC signature from webhook
     */
    public function verifyHmac(array $data, string $receivedHmac): bool
    {
        $hmacSecret = config('paymob.hmac_secret');
        
        // Build the string to hash based on Paymob's specification
        $concatenatedString = $data['amount_cents'] .
            $data['created_at'] .
            $data['currency'] .
            ($data['error_occured'] ? 'true' : 'false') .
            ($data['has_parent_transaction'] ? 'true' : 'false') .
            $data['id'] .
            $data['integration_id'] .
            ($data['is_3d_secure'] ? 'true' : 'false') .
            ($data['is_auth'] ? 'true' : 'false') .
            ($data['is_capture'] ? 'true' : 'false') .
            ($data['is_refunded'] ? 'true' : 'false') .
            ($data['is_standalone_payment'] ? 'true' : 'false') .
            ($data['is_voided'] ? 'true' : 'false') .
            $data['order']['id'] .
            $data['owner'] .
            ($data['pending'] ? 'true' : 'false') .
            $data['source_data']['pan'] .
            $data['source_data']['sub_type'] .
            $data['source_data']['type'] .
            ($data['success'] ? 'true' : 'false');

        $calculatedHmac = hash_hmac('sha512', $concatenatedString, $hmacSecret);

        return hash_equals($calculatedHmac, $receivedHmac);
    }
}
