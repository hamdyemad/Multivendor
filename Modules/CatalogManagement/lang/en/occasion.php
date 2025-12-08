<?php

return [
    // Page titles
    'occasions_management' => 'Occasions Management',
    'add_occasion' => 'Add Occasion',
    'edit_occasion' => 'Edit Occasion',
    'view_occasion' => 'View Occasion',
    'delete_occasion' => 'Delete Occasion',

    // Fields
    'name' => 'Name',
    'title' => 'Title',
    'sub_title' => 'Sub Title',
    'vendor' => 'Vendor',
    'image' => 'Image',
    'start_date' => 'Start Date',
    'end_date' => 'End Date',
    'status' => 'Status',
    'activation' => 'Activation',
    'active' => 'Active',
    'inactive' => 'Inactive',
    'all_status' => 'All Status',
    'created_at' => 'Created At',
    'created_from' => 'Created From',
    'created_until' => 'Created Until',
    'no_image' => 'No Image',
    'occasion_information' => 'Occasion Information',
    'slug' => 'Slug',
    'occasion_details' => 'Occasion Details',
    'basic_information' => 'Basic Information',
    'quick_info' => 'Quick Info',
    'total_variants' => 'Total Variants',
    'duration' => 'Duration',
    'product' => 'Product',
    'position' => 'Position',
    'no_variants' => 'No product variants added yet',

    // Product Variants
    'product_variants' => 'Product Variants',
    'search_products' => 'Search Products',
    'search_products_placeholder' => 'Type to search products...',
    'search_products_help' => 'Search and select products to add to this occasion',
    'searching_products' => 'Searching products',
    'no_products_found' => 'No products found',
    'product_name' => 'Product Name',
    'variant_name' => 'Variant Name',
    'sku' => 'SKU',
    'original_price' => 'Original Price',
    'special_price' => 'Special Price',
    'no_products_selected' => 'No products selected yet',
    'product_already_added' => 'This product is already added',
    'selected_products' => 'Selected Products',
    'products_selected' => 'products selected',
    'custom_price' => 'Custom Price',
    'existing_products' => 'Existing Products',
    'existing_products_info' => 'These are the products currently associated with this occasion. You can manage them from the occasion details page.',

    // SEO Fields
    'seo_information' => 'SEO Information',
    'seo_title' => 'SEO Title',
    'seo_description' => 'SEO Description',
    'seo_keywords' => 'SEO Keywords',

    // Messages
    'occasion_created' => 'Occasion created successfully',
    'occasion_updated' => 'Occasion updated successfully',
    'occasion_deleted' => 'Occasion deleted successfully',
    'products_added' => 'products added',
    'error_creating_occasion' => 'Error creating occasion',
    'error_updating_occasion' => 'Error updating occasion',
    'error_deleting_occasion' => 'Error deleting occasion',
    'error_loading_data' => 'Error loading data',
    'status_changed_successfully' => 'Status changed successfully',
    'error_changing_status' => 'Error changing status',
    'product_deleted_successfully' => 'Product removed from occasion successfully',
    'error_deleting_product' => 'Error removing product from occasion',

    // Validation
    'translations_required' => 'Translations are required',
    'image_must_be_image' => 'The file must be an image',
    'image_invalid_format' => 'Invalid image format. Allowed formats: jpeg, png, jpg, gif, webp',
    'image_max_size' => 'Image size must not exceed 2MB',
    'active_must_be_boolean' => 'Active field must be boolean',
    'start_date_must_be_date' => 'Start date must be a valid date',
    'end_date_must_be_date' => 'End date must be a valid date',
    'end_date_after_start' => 'End date must be after or equal to start date',

    'name_required' => 'Name is required',
    'name_must_be_string' => 'Name must be a string',
    'name_max_length' => 'Name must not exceed 255 characters',

    'title_must_be_string' => 'Title must be a string',
    'title_max_length' => 'Title must not exceed 255 characters',

    'sub_title_must_be_string' => 'Sub title must be a string',
    'sub_title_max_length' => 'Sub title must not exceed 255 characters',

    'seo_title_must_be_string' => 'SEO title must be a string',
    'seo_title_max_length' => 'SEO title must not exceed 255 characters',

    'seo_description_must_be_string' => 'SEO description must be a string',
    'seo_description_max_length' => 'SEO description must not exceed 500 characters',

    'seo_keywords_must_be_string' => 'SEO keywords must be a string',
    'seo_keywords_max_length' => 'SEO keywords must not exceed 500 characters',

    // Variants validation
    'variants_required' => 'At least one product variant is required',
    'variants_must_be_array' => 'Variants must be an array',
    'variant_id_required' => 'Variant ID is required',
    'variant_id_invalid' => 'Selected variant is invalid',
    'special_price_must_be_number' => 'Special price must be a valid number',

    // Additional translations for form
    'must_be_greater_than_zero' => 'must be greater than 0',

    // Form labels
    'enter_occasion_name' => 'Enter occasion name',
    'enter_occasion_title' => 'Enter occasion title',
    'enter_occasion_sub_title' => 'Enter occasion sub title',
    'recommended_size' => 'Recommended size: 1538×402px',
    'select_vendor' => 'Select Vendor',
    'type_to_search_products' => 'Type to search products...',
    'enter_seo_title' => 'Enter SEO title',
    'enter_seo_description' => 'Enter SEO description',
    'type_keyword_press_enter' => 'Type a keyword and press Enter...',
];
