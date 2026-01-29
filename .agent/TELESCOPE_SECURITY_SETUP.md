# Laravel Telescope Security Setup

## ✅ Security Measures Implemented

### 1. Authorization Gate
The `viewTelescope` gate now checks multiple conditions:

- **Local Environment:** Always allowed for development
- **Email Whitelist:** Specific admin emails can access
- **User Type Check:** Users with `user_type_id = 1` (admin) can access
- **Role Check:** Users with 'admin' role can access
- **Permission Check:** Users with 'view-telescope' permission can access

### 2. Production Protection
- Telescope recording is **completely disabled** in production environment
- Only critical entries (exceptions, failed requests, failed jobs) are logged in non-local environments

### 3. Sensitive Data Protection
Hidden parameters and headers:
- Passwords (all variations)
- API keys and secrets
- Tokens (access, refresh, CSRF)
- Authorization headers
- Cookies

---

## 🔧 Configuration

### Option 1: Email Whitelist (Current Setup)

Edit `app/Providers/TelescopeServiceProvider.php`:

```php
$allowedEmails = [
    'super_admin@gmail.com',
    'admin@eramo.com',
    'your-email@example.com',
];
```

### Option 2: User Type Check

If your users table has `user_type_id`:

```php
// In gate() method, this is already added:
if (isset($user->user_type_id) && $user->user_type_id == 1) {
    return true;
}
```

### Option 3: Role-Based (Recommended)

If using Spatie Laravel Permission or similar:

```php
// Already added in gate():
if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
    return true;
}
```

### Option 4: Permission-Based (Most Flexible)

Create a specific permission for Telescope:

```bash
# In your seeder or migration
php artisan tinker
```

```php
// Create permission
$permission = Permission::create(['name' => 'view-telescope']);

// Assign to admin role
$adminRole = Role::findByName('admin');
$adminRole->givePermissionTo('view-telescope');

// Or assign directly to user
$user = User::find(1);
$user->givePermissionTo('view-telescope');
```

---

## 🚀 Testing Access

### Test as Admin:
1. Login with admin account
2. Visit: `https://your-domain.com/telescope`
3. Should see Telescope dashboard

### Test as Regular User:
1. Login with regular customer account
2. Visit: `https://your-domain.com/telescope`
3. Should see **403 Forbidden** error

### Test Unauthenticated:
1. Logout
2. Visit: `https://your-domain.com/telescope`
3. Should redirect to login or show 403

---

## 🔒 Additional Security Recommendations

### 1. Disable Telescope in Production (Recommended)

In `.env`:
```env
TELESCOPE_ENABLED=false
```

In `config/telescope.php`:
```php
'enabled' => env('TELESCOPE_ENABLED', true),
```

### 2. Use Different Route in Production

In `config/telescope.php`:
```php
'path' => env('TELESCOPE_PATH', 'telescope'),
```

In `.env`:
```env
# Use a secret path in production
TELESCOPE_PATH=secret-admin-telescope-panel-xyz123
```

Access via: `https://your-domain.com/secret-admin-telescope-panel-xyz123`

### 3. IP Whitelist (Extra Security)

Add to `gate()` method:

```php
protected function gate(): void
{
    Gate::define('viewTelescope', function ($user) {
        // Check IP address
        $allowedIPs = [
            '127.0.0.1',
            '::1',
            'your-office-ip',
        ];
        
        if (!in_array(request()->ip(), $allowedIPs)) {
            return false;
        }
        
        // ... rest of your checks
    });
}
```

### 4. HTTP Basic Auth (Quick Protection)

In `routes/web.php` or create middleware:

```php
Route::middleware(['auth.basic'])->group(function () {
    // Telescope routes
});
```

---

## 🐛 Troubleshooting

### Issue: "403 Forbidden" for Admin Users

**Solution 1:** Clear cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

**Solution 2:** Check user authentication
```php
// In tinker
php artisan tinker
auth()->user(); // Should return user object
auth()->user()->email; // Check email
```

**Solution 3:** Verify gate logic
```php
// In tinker
Gate::allows('viewTelescope', auth()->user()); // Should return true
```

### Issue: Telescope Not Recording

**Solution:** Check environment
```bash
# In .env
APP_ENV=local  # or staging
TELESCOPE_ENABLED=true
```

### Issue: Sensitive Data Still Visible

**Solution:** Add to `hideSensitiveRequestDetails()`:
```php
Telescope::hideRequestParameters(['your-sensitive-field']);
```

---

## 📝 Current Configuration Summary

✅ **Authorization:** Multiple methods (email, user_type, role, permission)  
✅ **Production:** Recording disabled  
✅ **Sensitive Data:** Passwords, tokens, keys hidden  
✅ **Local Development:** Full access  
✅ **Default Behavior:** Deny access (secure by default)

---

## 🔄 Recommended Setup for Your Project

Based on your Eramo platform structure:

```php
protected function gate(): void
{
    Gate::define('viewTelescope', function ($user) {
        // Allow in local
        if (app()->environment('local')) {
            return true;
        }

        // Check if user exists
        if (!$user) {
            return false;
        }

        // Check if user is admin (user_type_id = 1)
        if (isset($user->user_type_id) && $user->user_type_id == 1) {
            return true;
        }

        // Check specific admin emails
        return in_array($user->email, [
            'admin@eramo.com',
            'super_admin@gmail.com',
        ]);
    });
}
```

This setup:
- ✅ Works with your existing user_type system
- ✅ Allows specific admin emails
- ✅ Secure by default
- ✅ Easy to maintain

---

**Last Updated:** January 29, 2026  
**Status:** Secured ✅
