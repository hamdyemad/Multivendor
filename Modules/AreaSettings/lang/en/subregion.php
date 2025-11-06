<?php

return [
    // Page Titles
    'subregions_management' => 'Sub-Regions Management',
    'create_subregion' => 'Create Sub-Region',
    'edit_subregion' => 'Edit Sub-Region',
    'view_subregion' => 'View Sub-Region',
    'subregion_details' => 'Sub-Region Details',

    // Form Fields
    'id' => 'ID',
    'name' => 'Name',
    'name_english' => 'Name',
    'name_arabic' => 'Name',
    'country' => 'Country',
    'region' => 'Region',
    'city' => 'City',
    'activation' => 'Activation',
    'status' => 'Status',
    'active' => 'Active',
    'inactive' => 'Inactive',
    'translations' => 'Translations',
    'hierarchy_navigation' => 'Hierarchy Navigation',

    // Placeholders
    'enter_subregion_name_english' => 'Enter sub-region name in English',
    'enter_subregion_name_arabic' => 'Enter sub-region name in Arabic',
    'search_by_name' => 'Search by name',
    'search_placeholder' => 'Search sub-regions by name...',
    'select_region' => 'Select Region',
    'select_city' => 'Select City',
    'all_regions' => 'All Regions',
    'all_status' => 'All Status',

    // Table Headers
    'name_en' => 'Name (English)',
    'name_ar' => 'الاسم باللغه العربيه',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
    'action' => 'Actions',

    // Buttons
    'add_subregion' => 'Add Sub-Region',
    'update_subregion' => 'Update Sub-Region',
    'cancel' => 'Cancel',
    'save' => 'Save',
    'back_to_list' => 'Back to List',

    // Table
    'all' => 'All',
    'no_subregions_found' => 'No sub-regions found',

    // Messages
    'subregion_created' => 'Sub-Region created successfully',
    'subregion_updated' => 'Sub-Region updated successfully',
    'subregion_deleted' => 'Sub-Region deleted successfully',
    'error_creating_subregion' => 'Error creating sub-region',
    'error_updating_subregion' => 'Error updating sub-region',
    'error_deleting_subregion' => 'Error deleting sub-region',
    'error_occurred' => 'An error occurred. Please try again.',
    'processing' => 'Processing...',
    'subregion_not_found' => 'Sub-Region not found',

    // Delete Modal
    'confirm_delete' => 'Confirm Delete',
    'delete_confirmation' => 'Are you sure you want to delete this subregion?',
    'delete_subregion' => 'Delete Subregion',

    // Validation
    'validation_errors' => 'Validation Errors',

    // Validation Messages
    'region_required' => 'The region is required',
    'region_exists' => 'The selected region does not exist',
    'translations_required' => 'Translations are required',
    'name_required' => 'The name is required',
    'name_string' => 'The name must be a string',
    'name_max' => 'The name may not be greater than 255 characters',
    'validation' => [
        'name_en_required' => 'The English name is required',
        'name_en_unique' => 'This sub-region name already exists in the selected region',
        'name_ar_required' => 'The Arabic name is required',
        'name_ar_unique' => 'This sub-region name already exists in the selected region',
        'name_unique' => 'This sub-region name already exists in the selected region',
        'region_required' => 'The region is required',
        'region_exists' => 'The selected region does not exist',
    ],

    // Info
    'basic_information' => 'Basic Information',
    'subregions_list' => 'Sub-Regions List',
];
