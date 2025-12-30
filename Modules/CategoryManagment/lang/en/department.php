<?php

return [
    // Page Titles
    'departments_management' => 'Departments Management',
    'create_department' => 'Create Department',
    'edit_department' => 'Edit Department',
    'view_department' => 'View Department',
    'department_details' => 'Department Details',
    'activities' => 'activities',
    // Form Fields
    'name' => 'Name',
    'name_english' => 'Name in english',
    'name_arabic' => 'Name in arabic',
    'description' => 'Description',
    'description_english' => 'Description in english',
    'department_code' => 'Department Code',
    'activation' => 'Activation',
    'view_status' => 'View Status',
    'sort_number' => 'Sort Number',
    'status' => 'Status',
    'active' => 'Active',
    'inactive' => 'Inactive',
    'select_activities' => 'Select Activities',
    'commission' => 'Commission (%)',
    'image' => 'Department Image',
    'icon' => 'Department Icon',
    'click_to_upload_image' => 'Click to upload department image',
    "click_to_upload_icon" => 'Click to upload department icon',
    'recommended_size' => 'Recommended size: 1538x402px',
    'recommended_size_for_icon' => 'Recommended size for icon: 112x112px',

    // Placeholders
    'enter_department_name_english' => 'Enter department name in English',
    'enter_department_name_arabic' => 'Enter department name in Arabic',
    'enter_department_code' => 'e.g., IT, HR, SALES',
    'search_by_name_or_code' => 'Search by name or code',

    // Table Headers
    'name_en' => 'Name (English)',
    'name_ar' => 'الاسم باللغه العربيه',
    'code' => 'Code',
    'created_at' => 'Created At',
    'department_information' => 'Department Information',

    // Buttons
    'add_department' => 'Add Department',
    'update_department' => 'Update Department',
    'cancel' => 'Cancel',
    'save' => 'Save',
    'back_to_list' => 'Back to List',

    // Table
    'all' => 'All',
    'no_departments_found' => 'No departments found',

    // Messages
    'department_created' => 'Department created successfully',
    'department_updated' => 'Department updated successfully',
    'department_deleted' => 'Department deleted successfully',
    'error_creating_department' => 'Error creating department',
    'error_updating_department' => 'Error updating department',
    'error_deleting_department' => 'Error deleting department',
    'error_loading_departments' => 'Error loading departments',
    'error_loading_form' => 'Error loading form',
    'department_not_found' => 'Department not found',
    'search_by_name' => 'Search by name...',

    // Delete Modal
    'confirm_delete' => 'Confirm Delete',
    'delete_confirmation' => 'Are you sure you want to delete this department?',
    'delete_department' => 'Delete Department',

    // Validation
    'validation_errors' => 'Validation Errors',

    // Validation Messages
    'validation' => [
        'name_en_required' => 'The English name is required',
        'name_ar_required' => 'The Arabic name is required',
        'code_required' => 'The department code is required',
        'code_unique' => 'This department code already exists',
        'code_max' => 'The department code must not exceed 50 characters',
    ],

    // Filters
    'filter' => 'Filter',

    // Info
    'basic_information' => 'Basic Information',
    'departments_list' => 'Departments List',

    // Status Change
    'change_status' => 'Change Status',
    'status_changed_successfully' => 'Department status changed successfully',
    'error_changing_status' => 'Error changing department status',
    'status_already_set' => 'Department is already set to this status',

    // Validation Messages for Request
    'at_least_one_translation_required' => 'At least one translation is required.',
    'name_required' => 'The department name is required.',
    'name_max_255' => 'The department name may not be greater than 255 characters.',

    // Reorder
    'reorder_success' => 'Departments reordered successfully',
    'error_reordering' => 'Error reordering departments',
];
