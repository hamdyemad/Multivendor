<?php

return [
    // General
    'order' => 'Order',
    'orders' => 'Orders',
    'order_id' => 'Order #:id',
    'order_date' => 'Order Date',
    'status' => 'Status',
    'total' => 'Total',
    'actions' => 'Actions',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',
    'view' => 'View',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'save' => 'Save',
    'cancel' => 'Cancel',
    'back' => 'Back',
    'search' => 'Search...',
    'no_records' => 'No records found',
    'confirm_delete' => 'Are you sure you want to delete this item?',
    'delete_success' => 'Item deleted successfully',
    'save_success' => 'Saved successfully',
    'error' => 'Error',
    'success' => 'Success',
    'warning' => 'Warning',
    'info' => 'Information',

    // Order Statuses
    'status_pending' => 'Pending',
    'status_processing' => 'Processing',
    'status_shipped' => 'Shipped',
    'status_delivered' => 'Delivered',
    'status_cancelled' => 'Cancelled',
    'status_refunded' => 'Refunded',

    // Order Creation
    'create_order' => 'Create Order',
    'edit_order' => 'Edit Order',
    'order_details' => 'Order Details',
    'customer_information' => 'Customer Information',
    'billing_information' => 'Billing Information',
    'shipping_information' => 'Shipping Information',
    'order_summary' => 'Order Summary',
    'add_product' => 'Add Product',
    'product' => 'Product',
    'products' => 'Products',
    'quantity' => 'Quantity',
    'price' => 'Price',
    'subtotal' => 'Subtotal',
    'shipping' => 'Shipping',
    'tax' => 'Tax',
    'discount' => 'Discount',
    'grand_total' => 'Grand Total',
    'payment_method' => 'Payment Method',
    'shipping_method' => 'Shipping Method',
    'notes' => 'Notes',
    'no_products_found' => 'No products found',
    'please_select_product' => 'Please select a product',
    'invalid_quantity' => 'Invalid quantity',
    'invalid_price' => 'Invalid price',
    'invalid_product_data' => 'Invalid product data',
    'product_added' => 'Product added to order',
    'product_removed' => 'Product removed from order',
    'order_created' => 'Order created successfully',
    'order_updated' => 'Order updated successfully',
    'order_deleted' => 'Order deleted successfully',
    'order_cancelled' => 'Order cancelled successfully',
    'order_status_updated' => 'Order status updated successfully',

    // Validation Messages
    'validation' => [
        'customer_required' => 'Please select a customer',
        'products_required' => 'Please add at least one product to the order',
        'shipping_required' => 'Shipping method is required',
        'payment_required' => 'Payment method is required',
        'quantity_required' => 'Quantity is required',
        'quantity_min' => 'Quantity must be at least 1',
        'price_required' => 'Price is required',
        'price_numeric' => 'Price must be a number',
        'price_min' => 'Price must be greater than 0',
    ],

    // Order Items
    'items' => [
        'name' => 'Name',
        'sku' => 'SKU',
        'price' => 'Price',
        'qty' => 'Qty',
        'total' => 'Total',
        'remove' => 'Remove',
    ],

    // Order Totals
    'totals' => [
        'subtotal' => 'Subtotal',
        'shipping' => 'Shipping',
        'tax' => 'Tax',
        'discount' => 'Discount',
        'grand_total' => 'Grand Total',
    ],

    // Order Status History
    'status_history' => 'Status History',
    'status_changed' => 'Status changed to :status',
    'status_changed_by' => 'by :name',
    'status_changed_at' => 'at :date',
    'no_status_history' => 'No status history available',

    // Order Notes
    'add_note' => 'Add Note',
    'notes' => 'Notes',
    'note_added' => 'Note added successfully',
    'note_deleted' => 'Note deleted successfully',
    'no_notes' => 'No notes available',
    'note_placeholder' => 'Add a note about this order...',

    // Order Emails
    'email' => [
        'subject' => 'Your Order #:order_id',
        'greeting' => 'Hello :name,',
        'thank_you' => 'Thank you for your order!',
        'order_details' => 'Order Details',
        'shipping_address' => 'Shipping Address',
        'billing_address' => 'Billing Address',
        'track_order' => 'Track Your Order',
        'contact_us' => 'Contact Us',
        'regards' => 'Regards,',
        'team' => 'The :app_name Team',
    ],

    // Order Fulfillment
    'fulfillment' => [
        'title' => 'Fulfillment',
        'tracking_number' => 'Tracking Number',
        'carrier' => 'Carrier',
        'date_shipped' => 'Date Shipped',
        'add_tracking' => 'Add Tracking',
        'tracking_added' => 'Tracking information added',
        'tracking_updated' => 'Tracking information updated',
        'tracking_removed' => 'Tracking information removed',
        'no_tracking' => 'No tracking information available',
    ],

    // Order Invoices
    'invoice' => 'Invoice',
    'invoice_number' => 'Invoice #',
    'invoice_date' => 'Invoice Date',
    'invoice_due_date' => 'Due Date',
    'download_invoice' => 'Download Invoice',
    'print_invoice' => 'Print Invoice',
    'email_invoice' => 'Email Invoice',
    'invoice_sent' => 'Invoice sent successfully',

    // Order Refunds
    'refund' => 'Refund',
    'refund_amount' => 'Refund Amount',
    'refund_reason' => 'Refund Reason',
    'refund_processed' => 'Refund processed successfully',
    'refund_failed' => 'Refund failed. Please try again.',
    'refund_history' => 'Refund History',
    'no_refunds' => 'No refunds found',

    // Order Alerts
    'alert' => [
        'order_created' => 'Order #:order_id has been created',
        'order_updated' => 'Order #:order_id has been updated',
        'order_deleted' => 'Order #:order_id has been deleted',
        'order_status_changed' => 'Order #:order_id status changed to :status',
        'payment_received' => 'Payment received for order #:order_id',
        'shipping_confirmed' => 'Shipping confirmed for order #:order_id',
        'delivery_confirmed' => 'Delivery confirmed for order #:order_id',
    ],

    // Order Exceptions
    'exception' => [
        'invalid_product' => 'Invalid product selected',
        'invalid_quantity' => 'Invalid quantity specified',
        'insufficient_stock' => 'Insufficient stock for :product',
        'order_not_found' => 'Order not found',
        'order_cannot_be_modified' => 'This order cannot be modified',
        'order_cannot_be_cancelled' => 'This order cannot be cancelled',
        'order_cannot_be_refunded' => 'This order cannot be refunded',
        'payment_failed' => 'Payment processing failed',
        'shipping_failed' => 'Shipping processing failed',
        'refund_failed' => 'Refund processing failed',
    ],
];
