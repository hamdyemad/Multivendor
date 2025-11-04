<?php

return [
    // Currency Management
    'currencies_management' => 'Currencies Management',
    'add_currency' => 'Add Currency',
    'create_currency' => 'Create Currency',
    'edit_currency' => 'Edit Currency',
    'view_currency' => 'View Currency',
    'update_currency' => 'Update Currency',
    'delete_currency' => 'Delete Currency',
    'currency_details' => 'Currency Details',
    'no_currencies_found' => 'No currencies found',
    
    // Fields
    'name' => 'Name',
    'name_english' => 'Name (English)',
    'name_arabic' => 'Name (Arabic)',
    'currency_code' => 'Currency Code',
    'currency_symbol' => 'Currency Symbol',
    'active' => 'Active',
    'inactive' => 'Inactive',
    'status' => 'Status',
    'all_status' => 'All Status',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
    'dates' => 'Dates',
    
    // Placeholders
    'search_placeholder' => 'Search by name, code, or symbol...',
    
    // Messages
    'created_successfully' => 'Currency created successfully',
    'updated_successfully' => 'Currency updated successfully',
    'deleted_successfully' => 'Currency deleted successfully',
    'error_creating' => 'Error creating currency',
    'error_updating' => 'Error updating currency',
    'error_deleting' => 'Error deleting currency',
    'not_found' => 'Currency not found',
    
    // Actions
    'back_to_list' => 'Back to List',
    'confirm_delete' => 'Confirm Delete',
    'delete_confirmation' => 'Are you sure you want to delete this currency?',
    'cancel' => 'Cancel',
    
    // Additional
    'basic_information' => 'Basic Information',
    'translations' => 'Translations',
    'validation_errors' => 'Validation Errors',
    
    // Validation Messages
    'validation' => [
        'code_required' => 'Currency code is required',
        'code_unique' => 'This currency code already exists',
        'code_max' => 'Currency code must not exceed 3 characters',
        'symbol_required' => 'Currency symbol is required',
        'symbol_max' => 'Currency symbol must not exceed 10 characters',
        'name_en_required' => 'Name (English) is required',
        'name_ar_required' => 'Name (Arabic) is required',
    ],
];
