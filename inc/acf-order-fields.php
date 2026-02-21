<?php
if (!defined('ABSPATH')) {
    exit;
}
function cleaning_register_order_acf_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    acf_add_local_field_group(array(
        'key'      => 'group_cleaning_order',
        'title'    => 'Order Details',
        'fields'   => array(
            array(
                'key'           => 'field_order_status',
                'label'         => 'Order Status',
                'name'          => 'order_status',
                'type'          => 'select',
                'instructions'  => 'Current status of this order.',
                'required'      => 1,
                'choices'       => array(
                    'pending'     => 'Pending',
                    'confirmed'   => 'Confirmed',
                    'in_progress' => 'In Progress',
                    'completed'   => 'Completed',
                    'cancelled'   => 'Cancelled',
                ),
                'default_value' => 'pending',
                'allow_null'    => 0,
                'return_format' => 'value',
                'wrapper'       => array(
                    'width' => '50',
                ),
            ),
            array(
                'key'           => 'field_service_type',
                'label'         => 'Service Type',
                'name'          => 'service_type',
                'type'          => 'select',
                'instructions'  => 'Type of cleaning service.',
                'required'      => 1,
                'choices'       => array(
                    'reg'  => 'Regular Cleaning',
                    'deep' => 'Deep Cleaning',
                    'move' => 'Move In/Out Cleaning',
                ),
                'default_value' => 'reg',
                'allow_null'    => 0,
                'return_format' => 'value',
                'wrapper'       => array(
                    'width' => '50',
                ),
            ),
            array(
                'key'           => 'field_bedrooms',
                'label'         => 'Bedrooms',
                'name'          => 'bedrooms',
                'type'          => 'number',
                'instructions'  => 'Number of bedrooms.',
                'required'      => 1,
                'default_value' => 1,
                'min'           => 1,
                'max'           => 10,
                'step'          => 1,
                'wrapper'       => array(
                    'width' => '33',
                ),
            ),
            array(
                'key'           => 'field_bathrooms',
                'label'         => 'Bathrooms',
                'name'          => 'bathrooms',
                'type'          => 'number',
                'instructions'  => 'Number of bathrooms (supports half baths like 1.5).',
                'required'      => 1,
                'default_value' => 1,
                'min'           => 1,
                'max'           => 10,
                'step'          => 0.5,
                'wrapper'       => array(
                    'width' => '33',
                ),
            ),
            array(
                'key'           => 'field_sqft',
                'label'         => 'Square Footage',
                'name'          => 'sqft',
                'type'          => 'number',
                'instructions'  => 'Total square footage of the home.',
                'required'      => 1,
                'default_value' => 1000,
                'min'           => 500,
                'max'           => 20000,
                'step'          => 100,
                'wrapper'       => array(
                    'width' => '34',
                ),
            ),
            array(
                'key'           => 'field_frequency',
                'label'         => 'Frequency',
                'name'          => 'frequency',
                'type'          => 'select',
                'instructions'  => 'How often the customer wants cleaning.',
                'required'      => 0,
                'choices'       => array(
                    'one-time'   => 'One-time',
                    'weekly'     => 'Weekly',
                    'bi-weekly'  => 'Bi-weekly',
                    'tri-weekly' => 'Tri-weekly',
                    'monthly'    => 'Monthly',
                ),
                'default_value' => 'one-time',
                'allow_null'    => 0,
                'return_format' => 'value',
                'wrapper'       => array(
                    'width' => '50',
                ),
            ),
            array(
                'key'          => 'field_order_extras',
                'label'        => 'Extras',
                'name'         => 'order_extras',
                'type'         => 'text',
                'instructions' => 'Comma-separated list of extras (e.g., "Fridge, Oven, Windows").',
                'required'     => 0,
                'placeholder'  => 'Fridge, Oven, Laundry',
                'wrapper'      => array(
                    'width' => '50',
                ),
            ),
            array(
                'key'           => 'field_cleaning_date',
                'label'         => 'Cleaning Date',
                'name'          => 'cleaning_date',
                'type'          => 'date_picker',
                'instructions'  => 'Scheduled date for cleaning.',
                'required'      => 1,
                'display_format'=> 'F j, Y',
                'return_format' => 'Y-m-d',
                'first_day'     => 0, 
                'wrapper'       => array(
                    'width' => '50',
                ),
            ),
            array(
                'key'          => 'field_cleaning_time',
                'label'        => 'Cleaning Time',
                'name'         => 'cleaning_time',
                'type'         => 'text',
                'instructions' => 'Preferred time slot (e.g., "9:00 AM - 12:00 PM").',
                'required'     => 0,
                'placeholder'  => '9:00 AM',
                'wrapper'      => array(
                    'width' => '50',
                ),
            ),
            array(
                'key'          => 'field_customer_name',
                'label'        => 'Customer Name',
                'name'         => 'customer_name',
                'type'         => 'text',
                'instructions' => 'Full name of the customer at time of order.',
                'required'     => 0,
                'wrapper'      => array(
                    'width' => '50',
                ),
            ),
            array(
                'key'          => 'field_customer_email',
                'label'        => 'Customer Email',
                'name'         => 'customer_email',
                'type'         => 'email',
                'instructions' => 'Email address of the customer.',
                'required'     => 0,
                'wrapper'      => array(
                    'width' => '50',
                ),
            ),
            array(
                'key'          => 'field_customer_phone',
                'label'        => 'Customer Phone',
                'name'         => 'customer_phone',
                'type'         => 'text',
                'instructions' => 'Phone number of the customer.',
                'required'     => 1,
                'placeholder'  => '+1 (555) 123-4567',
                'wrapper'      => array(
                    'width' => '50',
                ),
            ),
            array(
                'key'          => 'field_customer_address',
                'label'        => 'Service Address',
                'name'         => 'customer_address',
                'type'         => 'textarea',
                'instructions' => 'Full address where cleaning will be performed (snapshot for this order).',
                'required'     => 1,
                'rows'         => 3,
                'new_lines'    => 'br',
                'wrapper'      => array(
                    'width' => '50',
                ),
            ),
            array(
                'key'           => 'field_order_total',
                'label'         => 'Order Total ($)',
                'name'          => 'order_total',
                'type'          => 'number',
                'instructions'  => 'Final calculated price for this order.',
                'required'      => 1,
                'default_value' => 0,
                'min'           => 0,
                'step'          => 0.01,
                'prepend'       => '$',
                'wrapper'       => array(
                    'width' => '50',
                ),
            ),
            array(
                'key'          => 'field_receipt_details',
                'label'        => 'Receipt Details (JSON)',
                'name'         => 'receipt_details',
                'type'         => 'textarea',
                'instructions' => 'JSON string containing the full receipt breakdown. Do not edit manually.',
                'required'     => 0,
                'rows'         => 6,
                'new_lines'    => '',
                'wrapper'      => array(
                    'width' => '50',
                ),
            ),
            array(
                'key'          => 'field_admin_notes',
                'label'        => 'Admin Notes',
                'name'         => 'admin_notes',
                'type'         => 'textarea',
                'instructions' => 'Internal notes about this order (not visible to customer).',
                'required'     => 0,
                'rows'         => 4,
                'new_lines'    => 'br',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'cleaning_order',
                ),
            ),
        ),
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen'        => array(
            'permalink',
            'the_content',
            'excerpt',
            'discussion',
            'comments',
            'revisions',
            'slug',
            'format',
            'page_attributes',
            'featured_image',
            'categories',
            'tags',
            'send-trackbacks',
        ),
        'active'                => true,
        'description'           => 'All fields related to cleaning service orders.',
    ));
}
add_action('acf/init', 'cleaning_register_order_acf_fields');
function cleaning_get_order_data($order_id) {
    if (!$order_id || get_post_type($order_id) !== 'cleaning_order') {
        return array();
    }
    $post = get_post($order_id);
    return array(
        'id'              => $order_id,
        'title'           => $post->post_title,
        'author_id'       => $post->post_author,
        'created_at'      => $post->post_date,
        'status'          => get_post_meta($order_id, 'order_status', true),
        'service_type'    => get_post_meta($order_id, 'service_type', true),
        'bedrooms'        => get_post_meta($order_id, 'bedrooms', true),
        'bathrooms'       => get_post_meta($order_id, 'bathrooms', true),
        'sqft'            => get_post_meta($order_id, 'sqft', true),
        'frequency'       => get_post_meta($order_id, 'frequency', true),
        'extras'          => get_post_meta($order_id, 'order_extras', true),
        'cleaning_date'   => get_post_meta($order_id, 'cleaning_date', true),
        'cleaning_time'   => get_post_meta($order_id, 'cleaning_time', true),
        'customer_name'   => get_post_meta($order_id, 'customer_name', true),
        'customer_email'  => get_post_meta($order_id, 'customer_email', true),
        'customer_phone'  => get_post_meta($order_id, 'customer_phone', true),
        'customer_address'=> get_post_meta($order_id, 'customer_address', true),
        'total'           => get_post_meta($order_id, 'order_total', true),
        'receipt_json'    => get_post_meta($order_id, 'receipt_details', true),
        'admin_notes'     => get_post_meta($order_id, 'admin_notes', true),
    );
}
function cleaning_get_order_extras($order_id) {
    $extras_string = get_post_meta($order_id, 'order_extras', true);
    if (empty($extras_string)) {
        return array();
    }
    $extras = array_map('trim', explode(',', $extras_string));
    return array_filter($extras);
}
function cleaning_get_receipt_data($order_id) {
    $json_string = get_post_meta($order_id, 'receipt_details', true);
    if (empty($json_string)) {
        return array();
    }
    $decoded = json_decode($json_string, true);
    return is_array($decoded) ? $decoded : array();
}
function cleaning_get_service_label($service_key) {
    $labels = array(
        'reg'  => 'Regular Cleaning',
        'deep' => 'Deep Cleaning',
        'move' => 'Move In/Out Cleaning',
    );
    return isset($labels[$service_key]) ? $labels[$service_key] : $service_key;
}
function cleaning_get_status_label($status_key) {
    $labels = array(
        'pending'     => 'Pending',
        'confirmed'   => 'Confirmed',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
        'cancelled'   => 'Cancelled',
    );
    return isset($labels[$status_key]) ? $labels[$status_key] : $status_key;
}