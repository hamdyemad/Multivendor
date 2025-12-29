<?php

return [
    // Page titles
    'push_notifications' => 'Push Notifications',
    'all_notifications' => 'All Notifications',
    'send_notification' => 'Send Notification',
    'notification_details' => 'Notification Details',
    'view_notification' => 'View Notification',
    'notification' => 'Notification',

    // Types
    'notification_type' => 'Notification Type',
    'type_all' => 'All Customers',
    'type_specific' => 'Specific Customers',
    'type_all_vendors' => 'All Vendors',
    'type_specific_vendors' => 'Specific Vendors',

    // Status
    'status_pending' => 'Pending',
    'status_sent' => 'Sent',
    'status_failed' => 'Failed',

    // Fields
    'title' => 'Title',
    'title_en' => 'Title (English)',
    'title_ar' => 'Title (Arabic)',
    'description' => 'Description',
    'description_en' => 'Description (English)',
    'description_ar' => 'Description (Arabic)',
    'image' => 'Image',
    'upload_image' => 'Click to upload image',
    'image_size' => 'Recommended: 800x400',
    'select_customers' => 'Select Customers',
    'search_customers' => 'Search customers...',
    'select_vendors' => 'Select Vendors',
    'search_vendors' => 'Search vendors...',
    'customers' => 'Customers',
    'vendors' => 'Vendors',
    'created_by' => 'Created By',
    'sent_at' => 'Sent At',
    'recipients' => 'Recipients',
    'views' => 'Views',
    'viewed_at' => 'Viewed At',

    // Stats
    'stats' => 'Statistics',
    'total_sent' => 'Total Sent',
    'success' => 'Success',
    'failed' => 'Failed',

    // Preview
    'preview' => 'Preview',
    'preview_note' => 'This is how the notification will appear on mobile devices.',
    'notification_title' => 'Notification Title',
    'notification_description' => 'Notification description will appear here.',

    // Actions
    'send' => 'Send Notification',
    'search_placeholder' => 'Search by title...',

    // Messages
    'sent_successfully' => 'Notification sent successfully',
    'send_failed' => 'Failed to send notification',
    'deleted_successfully' => 'Notification deleted successfully',
    'confirm_delete' => 'Are you sure you want to delete this notification?',

    // Validation
    'validation' => [
        'type_required' => 'Please select notification type',
        'type_invalid' => 'Invalid notification type',
        'customers_required' => 'Please select at least one customer',
        'vendors_required' => 'Please select at least one vendor',
        'translations_required' => 'Title and description are required',
        'title_required' => 'Title is required',
        'description_required' => 'Description is required',
        'image_invalid' => 'Please upload a valid image',
        'image_max' => 'Image size must not exceed 2MB',
    ],
];
