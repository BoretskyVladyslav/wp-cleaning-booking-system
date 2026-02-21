<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_cleaning_create_order',        'cleaning_ajax_create_order');
add_action('wp_ajax_nopriv_cleaning_create_order', 'cleaning_ajax_create_order');

function cleaning_ajax_create_order(): void {
    set_time_limit(60);
    ignore_user_abort(true);

    try {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cleaning_auth_nonce')) {
            error_log('[CleaningOrder] Security check failed.');
            wp_send_json_error(['message' => 'Security check failed. Please refresh the page.', 'code' => 'invalid_nonce'], 403);
        }

        if (!is_user_logged_in()) {
            error_log('[CleaningOrder] User not logged in.');
            wp_send_json_error(['message' => 'Please log in to place an order.', 'code' => 'auth_required', 'login_url' => home_url('/login/')], 401);
        }

        $user    = wp_get_current_user();
        $user_id = $user->ID;
        error_log('[CleaningOrder] Authenticated User ID: ' . $user_id);

        $service_type     = sanitize_key($_POST['service_type'] ?? 'reg');
        $allowed_types    = ['reg', 'deep', 'move', 'post'];
        if (!in_array($service_type, $allowed_types, true)) $service_type = 'reg';

        $bedrooms         = max(0, min(10, intval($_POST['bedrooms']  ?? 1)));
        $bathrooms        = max(1.0, min(7.5, round(floatval($_POST['bathrooms'] ?? 1), 1)));
        $sqft             = sanitize_text_field($_POST['sqft']      ?? '1000');
        $frequency_raw    = sanitize_text_field($_POST['frequency'] ?? '');
        $extras_raw       = sanitize_text_field($_POST['extras']    ?? '');
        $cleaning_date    = sanitize_text_field($_POST['date']      ?? '');
        $cleaning_time    = sanitize_text_field($_POST['time']      ?? '');
        $customer_name    = sanitize_text_field($_POST['name']      ?? '');
        $customer_email   = sanitize_email($_POST['email']          ?? $user->user_email);
        $customer_phone   = sanitize_text_field($_POST['phone']     ?? '');
        $customer_address = sanitize_textarea_field($_POST['address'] ?? '');

        $total_raw    = preg_replace('/[^\d.]/', '', $_POST['total'] ?? '0');
        $order_total  = round(floatval($total_raw), 2);

        $receipt_details = '';
        if (isset($_POST['receipt_details'])) {
            $receipt_details = is_array($_POST['receipt_details'])
                ? wp_json_encode($_POST['receipt_details'])
                : sanitize_textarea_field($_POST['receipt_details']);
        }

        $normalised_date = cleaning_normalise_date($cleaning_date);

        $errors = [];
        if (empty($normalised_date))    $errors[] = 'Please select a cleaning date.';
        if (empty($customer_phone))     $errors[] = 'Phone number is required.';
        if (empty($customer_address))   $errors[] = 'Service address is required.';
        if ($order_total <= 0)          $errors[] = 'Invalid order total.';
        if (!is_email($customer_email)) $errors[] = 'A valid email address is required.';

        if (!empty($errors)) {
            error_log('[CleaningOrder] Validation errors: ' . implode(', ', $errors));
            wp_send_json_error(['message' => implode(' ', $errors), 'code' => 'validation_error'], 422);
        }

        $frequency_map = [
            '1'    => 'one-time',
            '0.85' => 'weekly',
            '0.90' => 'bi-weekly',
            '0.95' => 'monthly',
        ];
        $frequency_label = $frequency_map[$frequency_raw] ?? ($frequency_raw ?: 'one-time');

        $service_labels = ['reg' => 'Regular', 'deep' => 'Deep', 'move' => 'Move In/Out', 'post' => 'Post-Construction'];
        $service_label  = $service_labels[$service_type] ?? $service_type;

        $order_number = time() . '-' . $user_id;
        $order_title  = sprintf('Order #%s - %s (%s)', $order_number, $customer_name ?: $user->display_name, $service_label);

        error_log('[CleaningOrder] Inserting post...');

        $order_id = wp_insert_post([
            'post_type'   => 'cleaning_order',
            'post_title'  => $order_title,
            'post_status' => 'publish',
            'post_author' => $user_id,
        ], true);

        if (is_wp_error($order_id) || !$order_id) {
            error_log('[CleaningOrder] Insert failed: ' . (is_wp_error($order_id) ? $order_id->get_error_message() : 'unknown'));
            wp_send_json_error(['message' => 'Failed to create order. Please try again.', 'code' => 'order_creation_failed'], 500);
        }

        error_log('[CleaningOrder] Post inserted. ID: ' . $order_id);

        $display_name = $customer_name ?: $user->display_name;

        update_post_meta($order_id, 'order_status',    'pending');
        update_post_meta($order_id, 'service_type',    $service_type);
        update_post_meta($order_id, 'bedrooms',        $bedrooms);
        update_post_meta($order_id, 'bathrooms',       $bathrooms);
        update_post_meta($order_id, 'sqft',            $sqft);
        update_post_meta($order_id, 'frequency',       $frequency_label);
        update_post_meta($order_id, 'customer_name',   $display_name);
        update_post_meta($order_id, 'customer_email',  $customer_email);
        update_post_meta($order_id, 'customer_phone',  $customer_phone);
        update_post_meta($order_id, 'customer_user_id', $user_id);
        update_post_meta($order_id, 'order_notes',     sprintf('[%s] Order created.', current_time('Y-m-d H:i:s')));
        update_post_meta($order_id, 'receipt_details', $receipt_details);

        update_post_meta($order_id, 'order_extras',    $extras_raw);
        update_post_meta($order_id, 'extras',          $extras_raw);

        update_post_meta($order_id, 'cleaning_date',   $normalised_date);
        update_post_meta($order_id, 'service_date',    $normalised_date);

        update_post_meta($order_id, 'cleaning_time',   $cleaning_time);
        update_post_meta($order_id, 'service_time',    $cleaning_time);

        update_post_meta($order_id, 'customer_address', $customer_address);
        update_post_meta($order_id, 'service_address',  $customer_address);

        update_post_meta($order_id, 'order_total',     $order_total);
        update_post_meta($order_id, 'total_price',     $order_total);

        update_user_meta($user_id, 'billing_address', $customer_address);
        update_user_meta($user_id, 'billing_phone',   $customer_phone);

        error_log('[CleaningOrder] Meta saved.');

        do_action('cleaning_send_booking_notification', $order_id);

        if (ob_get_length()) ob_clean();

        error_log('[CleaningOrder] Sending JSON success. Order ID: ' . $order_id);

        wp_send_json_success([
            'message'      => 'Your order has been placed successfully!',
            'order_id'     => $order_id,
            'order_number' => $order_number,
            'redirect_url' => home_url('/cabinet/?order=' . $order_id . '&status=pending'),
        ]);

    } catch (Exception $e) {
        error_log('[CleaningOrder] EXCEPTION: ' . $e->getMessage());
        if (ob_get_length()) ob_clean();
        wp_send_json_error(['message' => 'An error occurred while creating your order. Please try again.', 'code' => 'exception'], 500);
    } catch (Error $e) {
        error_log('[CleaningOrder] PHP ERROR: ' . $e->getMessage());
        if (ob_get_length()) ob_clean();
        wp_send_json_error(['message' => 'A server error occurred. Please try again.', 'code' => 'server_error'], 500);
    }
}

add_action('wp_ajax_cleaning_cancel_order', 'cleaning_ajax_cancel_order');

function cleaning_ajax_cancel_order(): void {
    try {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cleaning_auth_nonce')) {
            wp_send_json_error(['message' => 'Security check failed.', 'code' => 'invalid_nonce'], 403);
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in to cancel an order.', 'code' => 'auth_required'], 401);
        }

        $order_id = intval($_POST['order_id'] ?? 0);
        if (!$order_id) {
            wp_send_json_error(['message' => 'Invalid order ID.', 'code' => 'invalid_order'], 400);
        }

        $order = get_post($order_id);
        if (!$order || $order->post_type !== 'cleaning_order') {
            wp_send_json_error(['message' => 'Order not found.', 'code' => 'order_not_found'], 404);
        }

        if ((int) $order->post_author !== get_current_user_id() && !current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'You do not have permission to cancel this order.', 'code' => 'permission_denied'], 403);
        }

        $current_status = get_post_meta($order_id, 'order_status', true);
        if (!in_array($current_status, ['pending', 'confirmed'], true)) {
            wp_send_json_error([
                'message' => 'This order cannot be cancelled. Only pending or confirmed orders can be cancelled.',
                'code'    => 'not_cancellable',
            ], 409);
        }

        update_post_meta($order_id, 'order_status', 'cancelled');

        $notes       = get_post_meta($order_id, 'order_notes', true);
        $cancel_note = sprintf('[%s] Order cancelled by user #%d.', current_time('Y-m-d H:i:s'), get_current_user_id());
        update_post_meta($order_id, 'order_notes', $notes ? $notes . "\n" . $cancel_note : $cancel_note);

        wp_send_json_success(['message' => 'Order has been cancelled successfully.']);

    } catch (Exception $e) {
        error_log('[CleaningOrder] Cancel exception: ' . $e->getMessage());
        wp_send_json_error(['message' => 'An error occurred while cancelling your order.', 'code' => 'exception'], 500);
    } catch (Error $e) {
        error_log('[CleaningOrder] Cancel PHP error: ' . $e->getMessage());
        wp_send_json_error(['message' => 'A server error occurred.', 'code' => 'server_error'], 500);
    }
}

add_action('wp_ajax_cleaning_get_orders', 'cleaning_ajax_get_orders');

function cleaning_ajax_get_orders(): void {
    try {
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Please log in to view your orders.', 'code' => 'auth_required'], 401);
        }

        $user_id       = get_current_user_id();
        $status_filter = sanitize_text_field($_POST['status'] ?? 'all');

        $args = [
            'post_type'      => 'cleaning_order',
            'author'         => $user_id,
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        $status_map = [
            'active'    => ['pending', 'confirmed', 'in_progress'],
            'completed' => ['completed'],
            'cancelled' => ['cancelled'],
        ];

        if ($status_filter !== 'all' && isset($status_map[$status_filter])) {
            $args['meta_query'] = [[
                'key'     => 'order_status',
                'value'   => $status_map[$status_filter],
                'compare' => 'IN',
            ]];
        }

        $orders_query = new WP_Query($args);
        $orders = [];

        if ($orders_query->have_posts()) {
            while ($orders_query->have_posts()) {
                $orders_query->the_post();
                $oid      = get_the_ID();
                $orders[] = [
                    'id'             => $oid,
                    'title'          => get_the_title(),
                    'date'           => get_the_date('F j, Y'),
                    'status'         => get_post_meta($oid, 'order_status',    true),
                    'service_type'   => get_post_meta($oid, 'service_type',    true),
                    'bedrooms'       => get_post_meta($oid, 'bedrooms',        true),
                    'bathrooms'      => get_post_meta($oid, 'bathrooms',       true),
                    'sqft'           => get_post_meta($oid, 'sqft',            true),
                    'frequency'      => get_post_meta($oid, 'frequency',       true),
                    'extras'         => get_post_meta($oid, 'order_extras',    true),
                    'cleaning_date'  => get_post_meta($oid, 'cleaning_date',   true),
                    'cleaning_time'  => get_post_meta($oid, 'cleaning_time',   true),
                    'total'          => get_post_meta($oid, 'order_total',     true),
                    'address'        => get_post_meta($oid, 'customer_address', true),
                    'receipt_details'=> get_post_meta($oid, 'receipt_details', true),
                    'payment_intent' => get_post_meta($oid, 'payment_intent_id', true),
                ];
            }
            wp_reset_postdata();
        }

        wp_send_json_success(['orders' => $orders, 'count' => count($orders)]);

    } catch (Exception $e) {
        error_log('[CleaningOrder] Get orders exception: ' . $e->getMessage());
        wp_send_json_error(['message' => 'An error occurred while fetching orders.', 'code' => 'exception'], 500);
    } catch (Error $e) {
        error_log('[CleaningOrder] Get orders PHP error: ' . $e->getMessage());
        wp_send_json_error(['message' => 'A server error occurred.', 'code' => 'server_error'], 500);
    }
}

function cleaning_normalise_date(string $raw): string {
    if (empty($raw)) return '';
    if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $raw, $m)) {
        return "{$m[3]}-{$m[2]}-{$m[1]}";
    }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
        return $raw;
    }
    $ts = strtotime($raw);
    return $ts ? date('Y-m-d', $ts) : '';
}

function cleaning_service_label_short(string $type): string {
    return [
        'reg'  => 'Regular',
        'deep' => 'Deep Clean',
        'move' => 'Move In/Out',
        'post' => 'Post-Construction',
    ][$type] ?? ucfirst($type);
}