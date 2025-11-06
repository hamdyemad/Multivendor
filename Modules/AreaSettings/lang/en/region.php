<?php

return [
    // Page Titles
    'regions_management' => 'Regions Management',
    'create_region' => 'Create Region',
    'edit_region' => 'Edit Region',
    'view_region' => 'View Region',
    'region_details' => 'Region Details',

    // Form Fields
    'name' => 'Name',
    'name_english' => 'Name',
    'name_arabic' => 'Name',
    'country' => 'Country',
    'city' => 'City',
    'activation' => 'Activation',
    'status' => 'Status',
    'active' => 'Active',
    'inactive' => 'Inactive',
    'subregions_count' => 'Number of Sub-Regions',
    'subregions' => 'Sub-Regions',

    // Placeholders
    'enter_region_name_english' => 'Enter region name in English',
    'enter_region_name_arabic' => 'Enter region name in Arabic',
    'search_by_name' => 'Search by name',
    'search_placeholder' => 'Search regions by name...',
    'select_city' => 'Select City',
    'all_cities' => 'All Cities',
    'all_status' => 'All Status',

    // Table Headers
    'name_en' => 'Name (English)',
    'name_ar' => 'الاسم باللغه العربيه',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',

    // Buttons
    'add_region' => 'Add Region',
    'update_region' => 'Update Region',
    'cancel' => 'Cancel',
    'save' => 'Save',
    'back_to_list' => 'Back to List',

    // Table
    'all' => 'All',
    'no_regions_found' => 'No regions found',

    // Messages
    'region_created' => 'Region created successfully',
    'region_updated' => 'Region updated successfully',
    'region_deleted' => 'Region deleted successfully',
    'error_creating_region' => 'Error creating region',
    'error_updating_region' => 'Error updating region',
    'error_deleting_region' => 'Error deleting region',
    'error_occurred' => 'An error occurred. Please try again.',
    'region_not_found' => 'Region not found',
    'processing' => 'Processing...',

    // Delete Modal
    'confirm_delete' => 'Confirm Delete',
    'delete_confirmation' => 'Are you sure you want to delete this region?',
    'delete_region' => 'Delete Region',

    // Validation
    'validation_errors' => 'Validation Errors',

    // Validation Messages
    'validation' => [
        'name_en_required' => 'The English name is required',
        'name_en_unique' => 'This region name already exists in the selected city',
        'name_ar_required' => 'The Arabic name is required',
        'name_ar_unique' => 'This region name already exists in the selected city',
        'name_unique' => 'This region name already exists in the selected city',
        'city_required' => 'The city is required',
        'city_exists' => 'The selected city does not exist',
    ],

    // Info
    'basic_information' => 'Basic Information',
    'regions_list' => 'Regions List',
];
