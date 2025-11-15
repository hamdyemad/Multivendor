<?php

return [
    // Actions
    'actions' => [
        'login' => 'Login',
        'logout' => 'Logout',
        'login_failed' => 'Login Failed',
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
        'restored' => 'Restored',
        'force_deleted' => 'Force Deleted',
        'password_reset_requested' => 'Password Reset Requested',
        'password_reset_success' => 'Password Reset Success',
        'password_reset_failed' => 'Password Reset Failed',
    ],

    // Descriptions
    'created_model' => 'Created :model: :identifier',
    'updated_model' => 'Updated :model: :identifier',
    'deleted_model' => 'Deleted :model: :identifier',
    'restored_model' => 'Restored :model: :identifier',
    'force_deleted_model' => 'Permanently deleted :model: :identifier',
    
    'login_success' => 'User logged in successfully',
    'logout_success' => 'User logged out',
    'login_failed_inactive' => 'Failed login attempt - Account inactive',
    'login_failed_blocked' => 'Failed login attempt - Account blocked',
    'login_failed_credentials' => 'Failed login attempt - Invalid credentials',
    
    'password_reset_sent' => 'Password reset code sent to email',
    'password_reset_email_failed' => 'Failed to send password reset email',
    'password_reset_invalid_code' => 'Failed password reset - Invalid reset code',
    'password_reset_expired_code' => 'Failed password reset - Expired reset code',
    'password_reset_completed' => 'Password reset successfully',

    // Model names
    'models' => [
        'Country' => 'Country',
        'City' => 'City',
        'Region' => 'Region',
        'SubRegion' => 'Sub Region',
        'User' => 'User',
        'Currency' => 'Currency',
        'Department' => 'Department',
        'Category' => 'Category',
        'SubCategory' => 'Sub Category',
        'Product' => 'Product',
        'Brand' => 'Brand',
        'Tax' => 'Tax',
        'Vendor' => 'Vendor',
        'Role' => 'Role',
        'Permission' => 'Permission',
    ],
];