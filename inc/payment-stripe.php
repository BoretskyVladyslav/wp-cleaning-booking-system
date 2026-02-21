<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Build a Stripe-compatible application/x-www-form-urlencoded body.
 *
 * Stripe's REST API requires literal bracket notation for nested arrays:
 *   line_items[0][price_data][currency]=usd
 *
 * PHP's http_build_query() encodes brackets as %5B/%5D — we decode ONLY those.
 * We must NOT use urldecode() because it would also decode %26 (&) inside values
 * like success_url, causing Stripe to treat query-string params inside the URL
 * as separate root-level API parameters (the "unknown parameter" error).
 */
function cleaning_stripe_build_body(array $data): string {
    return str_replace(['%5B', '%5D'], ['[', ']'], http_build_query($data));
}

function cleaning_create_payment_intent() {
    try {
        error_log('Cleaning Stripe: Process Started');

        // FIX 3: Verify against the single nonce action used site-wide (cleaning_auth_nonce).
        // Previously this checked cleaning_payment_nonce first (always failed) then fell back.
        $nonce = $_POST['nonce'] ?? '';
        if (!wp_verify_nonce($nonce, 'cleaning_auth_nonce')) {
            error_log('Cleaning Stripe Error: Invalid Nonce');
            wp_send_json_error(['message' => 'Security check failed.', 'code' => 'invalid_nonce']);
            return;
        }

        if (!is_user_logged_in()) {
            error_log('Cleaning Stripe Error: User not logged in');
            wp_send_json_error(['message' => 'Please log in to continue.', 'code' => 'not_logged_in']);
            return;
        }

        $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
        error_log('Cleaning Stripe: Order ID ' . $order_id);

        if (!$order_id) {
            wp_send_json_error(['message' => 'Invalid order ID.', 'code' => 'invalid_order']);
            return;
        }

        $order = get_post($order_id);
        $current_user_id = get_current_user_id();

        if (!$order || $order->post_type !== 'cleaning_order' || (int)$order->post_author !== $current_user_id) {
            error_log('Cleaning Stripe Error: Access Denied for Order ' . $order_id);
            wp_send_json_error(['message' => 'Order not found or access denied.', 'code' => 'access_denied']);
            return;
        }

        error_log('Cleaning Stripe: Order Data Validated');

        // Read the order total saved by ajax-orders.php into meta key 'order_total'
        $total = floatval(get_post_meta($order_id, 'order_total', true));
        if (!$total) {
            $total = floatval(get_post_meta($order_id, '_order_total_price', true));
        }

        if ($total <= 0) {
            error_log('Cleaning Stripe Error: Invalid Total ' . $total);
            wp_send_json_error(['message' => 'Invalid order total.', 'code' => 'invalid_total']);
            return;
        }

        // TASK 1: Amount must be integer cents — no decimals ever reach Stripe.
        $amount_cents = (int) round($total * 100);
        error_log('Cleaning Stripe: Amount in cents = ' . $amount_cents);

        // Retrieve Stripe secret key — try WP option first, fall back to PHP constant
        $secret_key = get_option('stripe_secret_key', '');
        error_log('Cleaning Stripe DEBUG: get_option(stripe_secret_key) length=' . strlen((string)$secret_key) . ', prefix="' . substr((string)$secret_key, 0, 10) . '"');

        // Fallback to hardcoded constant (covers local/test environments)
        if (empty($secret_key) && defined('STRIPE_TEST_SECRET_KEY')) {
            $secret_key = STRIPE_TEST_SECRET_KEY;
            error_log('Cleaning Stripe DEBUG: Fell back to STRIPE_TEST_SECRET_KEY constant');
        }

        if (empty($secret_key)) {
            error_log('Cleaning Stripe Error: Secret Key not found in options or constants');
            wp_send_json_error(['message' => 'Configuration error. Please contact support.', 'code' => 'missing_secret_key']);
            return;
        }


        $api_url = 'https://api.stripe.com/v1/checkout/sessions';

        // FIX 1: Build the array normally — cleaning_stripe_build_body() will
        // urldecode the encoded brackets so Stripe receives literal bracket notation.
        $post_data = [
            'mode'                  => 'payment',
            'success_url'           => home_url('/cabinet/?payment=success&order_id=' . $order_id . '&session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url'            => home_url('/cabinet/?payment=cancel'),
            'client_reference_id'   => $order_id,
            'customer_email'        => wp_get_current_user()->user_email,
            'metadata'              => [
                'order_id' => $order_id,
            ],
            'line_items'            => [
                [
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => [
                            'name' => 'Cleaning Service Order #' . $order_id,
                        ],
                        'unit_amount'  => $amount_cents,
                    ],
                    'quantity'   => 1,
                ],
            ],
            'payment_method_types'  => ['card'],
        ];

        // FIX 1 applied here: brackets decoded back to literal form before cURL send.
        $query_string = cleaning_stripe_build_body($post_data);

        error_log('Cleaning Stripe: Calling Stripe API');

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $query_string,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $secret_key,
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response   = curl_exec($ch);
        $http_code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($curl_error) {
            error_log('Cleaning Stripe cURL Error: ' . $curl_error);
            wp_send_json_error(['message' => 'Payment service unavailable. Please try again.', 'code' => 'curl_error']);
            return;
        }

        $result = json_decode($response, true);

        // FIX 2: Expose the real Stripe error message so debugging is possible.
        if ($http_code !== 200 || !isset($result['url'])) {
            $stripe_error_msg = isset($result['error']['message'])
                ? $result['error']['message']
                : 'Payment setup failed (HTTP ' . $http_code . ').';
            error_log('Cleaning Stripe API Error (HTTP ' . $http_code . '): ' . print_r($result, true));
            wp_send_json_error(['message' => $stripe_error_msg, 'code' => 'stripe_error']);
            return;
        }

        error_log('Cleaning Stripe: Session Created Successfully — ' . ($result['id'] ?? ''));
        update_post_meta($order_id, '_stripe_checkout_session_id', $result['id']);

        wp_send_json_success([
            'checkout_url' => $result['url'],
            'orderId'      => $order_id,
        ]);

    } catch (Exception $e) {
        // FIX 2: Return the real exception message, not a generic string.
        error_log('Cleaning Stripe Exception: ' . $e->getMessage());
        wp_send_json_error([
            'message' => 'Server error: ' . $e->getMessage(),
            'code'    => 'exception',
        ]);
    }
}
add_action('wp_ajax_cleaning_create_payment_intent', 'cleaning_create_payment_intent');

function cleaning_confirm_payment() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cleaning_auth_nonce')) {
        wp_send_json_error(['message' => 'Security check failed.']);
        return;
    }

    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    if ($order_id) {
        update_post_meta($order_id, 'order_status', 'confirmed');
        update_post_meta($order_id, '_payment_completed_at', current_time('mysql'));
        wp_send_json_success([
            'message'  => 'Order status updated.',
            'redirect' => home_url('/cabinet/'),
        ]);
    }
    wp_send_json_error(['message' => 'Invalid Request']);
}
add_action('wp_ajax_cleaning_confirm_payment', 'cleaning_confirm_payment');