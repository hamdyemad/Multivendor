<?php

return [
    // Page titles
    'request_quotations' => 'Request Quotations',
    'all_requests' => 'All Requests',
    'archived_requests' => 'Archived Requests',
    'request_details' => 'Request Details',

    // Fields
    'name' => 'Name',
    'email' => 'Email',
    'phone' => 'Phone',
    'address' => 'Address',
    'notes' => 'Notes',
    'file' => 'File',
    'customer_info' => 'Customer Info',
    'contact_info' => 'Contact Info',
    'address_info' => 'Address Info',

    // Status
    'status_pending' => 'Pending',
    'status_sent_offer' => 'Offer Sent',
    'status_accepted_offer' => 'Accepted',
    'status_rejected_offer' => 'Rejected',
    'status_order_created' => 'Order Created',
    'status_archived' => 'Archived',

    // Offer
    'send_offer' => 'Send Offer',
    'send_quotation_offer' => 'Send Quotation Offer',
    'offer_details' => 'Offer Details',
    'offer_price' => 'Offer Price',
    'offer_notes' => 'Offer Notes',
    'offer_notes_placeholder' => 'Enter any notes about the offer...',
    'offer_sent_at' => 'Offer Sent At',
    'offer_responded_at' => 'Responded At',
    'offer_sent_successfully' => 'Offer sent successfully',
    'cannot_send_offer' => 'Cannot send offer for this quotation',

    // Actions
    'create_order' => 'Create Order',
    'view_order' => 'View Order',
    'from_quotation' => 'From Quotation',
    'creating_order_from_quotation' => 'Creating Order from Quotation Request',
    'download_file' => 'Download File',
    'archive' => 'Archive',
    'order_number' => 'Order Number',

    // Messages
    'created_successfully' => 'Request quotation submitted successfully',
    'archived_successfully' => 'Request quotation archived successfully',
    'deleted_successfully' => 'Request quotation deleted successfully',
    'confirm_archive' => 'Are you sure you want to archive this request?',
    'search_placeholder' => 'Search by name, email, phone...',

    // Notifications
    'notification_title' => 'New Quotation Offer',
    'notification_body' => 'You have received a new offer for your quotation request. Offer price: :price',
    'notification_accepted' => 'Accepted your offer',
    'notification_rejected' => 'Rejected your offer',
    'notification_new_request' => 'New quotation request submitted',
    'order_created_notification_title' => 'Order Created',
    'order_created_notification_body' => 'Your order #:order_number has been created from your quotation request',
    'quotation_offer_notification_title' => 'New Quotation Offer',
    'quotation_offer_notification_body' => 'You have received a quotation offer with total price: :price. Please review and approve or reject.',

    // API responses
    'cannot_respond_to_offer' => 'Cannot respond to this offer',
    'offer_accepted_successfully' => 'Offer accepted successfully',
    'offer_rejected_successfully' => 'Offer rejected successfully',
];
