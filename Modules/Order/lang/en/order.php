<?php

return [
    // General
    'order_management' => 'Order Management',
    'orders_list' => 'Orders List',
    'order_details' => 'Order Details',
    'create_order' => 'Create Order',
    'edit_order' => 'Edit Order',
    'view_order' => 'View Order',
    'delete_order' => 'Delete Order',

    // Table Columns
    'order_id' => 'Order ID',
    'customer_name' => 'Customer Name',
    'customer_email' => 'Customer Email',
    'customer_phone' => 'Customer Phone',
    'customer_address' => 'Customer Address',
    'total_price' => 'Total Price',
    'items_count' => 'Items Count',
    'stage' => 'Stage',
    'payment_type' => 'Payment Type',
    'order_from' => 'Order From',
    'created_at' => 'Created At',
    'updated_at' => 'Updated At',

    // Filters
    'search_order_id_or_customer' => 'Search by order ID or customer...',
    'all_stages' => 'All Stages',
    'created_from' => 'Created From',
    'created_until' => 'Created Until',

    // Stage Management
    'change_order_stage' => 'Change Order Stage',
    'select_new_stage' => 'Select New Stage',
    'select_stage' => 'Select Stage',
    'update_stage' => 'Update Stage',
    'please_select_stage' => 'Please select a stage',
    'updating_stage' => 'Updating stage...',
    'stage_updated_successfully' => 'Order stage updated successfully',
    'error_updating_stage' => 'Error updating order stage',

    // Payment Types
    'cash_on_delivery' => 'Cash on Delivery',
    'online_payment' => 'Online Payment',

    // Order From
    'web' => 'Web',
    'ios' => 'iOS App',
    'android' => 'Android App',

    // Status Messages
    'order_created' => 'Order created successfully',
    'order_updated' => 'Order updated successfully',
    'order_deleted' => 'Order deleted successfully',
    'error_creating_order' => 'Error creating order',
    'error_updating_order' => 'Error updating order',
    'error_deleting_order' => 'Error deleting order',

    // Validation Messages
    'order_id_required' => 'Order ID is required',
    'customer_required' => 'Please select a customer',
    'customer_name_required' => 'Customer name is required',
    'customer_email_required' => 'Customer email is required',
    'customer_email_invalid' => 'Customer email must be a valid email address',
    'customer_phone_required' => 'Customer phone is required',
    'customer_address_required' => 'Customer address is required',
    'address_required' => 'Please select an address',
    'products_required' => 'Please add at least 3 products to the order',
    'total_price_required' => 'Total price is required',
    'total_price_numeric' => 'Total price must be a number',
    'stage_id_required' => 'Stage is required',
    'stage_id_exists' => 'Selected stage does not exist',

    // Pipeline Error Messages
    'product_not_found' => 'Product with ID :id not found or invalid data structure',
    'product_id_not_found' => 'Product ID not found for vendor product :id',
    'vendor_id_not_found' => 'Vendor ID not found for vendor product :id',

    // Actions
    'add_product' => 'Add Product',
    'remove_product' => 'Remove Product',
    'add_fee' => 'Add Fee',
    'add_discount' => 'Add Discount',
    'create_fulfillment' => 'Create Fulfillment',
    'print_invoice' => 'Print Invoice',
    'export_orders' => 'Export Orders',

    // Fulfillment
    'fulfillment' => 'Fulfillment',
    'fulfillments' => 'Fulfillments',
    'pending' => 'Pending',
    'processing' => 'Processing',
    'shipped' => 'Shipped',
    'delivered' => 'Delivered',
    'cancelled' => 'Cancelled',

    // Order Summary
    'order_summary' => 'Order Summary',
    'subtotal' => 'Subtotal',
    'shipping' => 'Shipping',
    'tax' => 'Tax',
    'discount' => 'Discount',
    'total' => 'Total',

    // Promo Code
    'promo_code' => 'Promo Code',
    'promo_code_applied' => 'Promo code applied',

    // Statistics
    'total_orders' => 'Total Orders',
    'total_product_price' => 'Total Product Price',
    'income' => 'Income',

    // Form Fields
    'reason' => 'Reason',
    'select' => 'Select',
    'product_name' => 'Product Name',
    'price' => 'Price',
    'please_select_product' => 'Please select a product',
    'customer_information' => 'Customer Information',
    'customer_type' => 'Customer Type',
    'existing_customer' => 'Existing Customer',
    'external_customer' => 'External Customer',
    'select_customer' => 'Select Customer',
    'no_products_found' => 'No products found',
    'no_customers_found' => 'No customers found',
    'loading_customers' => 'Loading customers...',
    'select_address' => 'Select Address',

    // Address Management
    'add_new_address' => 'Add New Address',
    'create_address' => 'Create Address',
    'customer_has_no_address' => 'This customer has no saved addresses. Please create one.',
    'address_title' => 'Address Title',
    'country' => 'Country',
    'city' => 'City',
    'region' => 'Region',
    'sub_region' => 'Sub Region',
    'set_as_primary' => 'Set as Primary Address',
    'address_created_successfully' => 'Address created successfully',
    'error_creating_address' => 'Error creating address',
    'please_select_customer' => 'Please select a customer first',
];
