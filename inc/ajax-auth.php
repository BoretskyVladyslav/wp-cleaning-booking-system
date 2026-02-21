<?php
if (!defined('ABSPATH')) {
    exit;
}
function cleaning_ajax_login() {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $transient_key = 'login_attempts_' . md5($ip_address);
    $attempts = get_transient($transient_key);
    if ($attempts !== false && $attempts >= 5) {
        wp_send_json_error([
            'message' => 'Too many failed attempts. Please try again in 15 minutes.',
            'code'    => 'rate_limited'
        ]);
    }
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cleaning_auth_nonce')) {
        wp_send_json_error([
            'message' => 'Security check failed. Please refresh the page and try again.',
            'code'    => 'invalid_nonce'
        ]);
    }
    $email    = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) && $_POST['remember'] === 'true';
    if (empty($email) || empty($password)) {
        wp_send_json_error([
            'message' => 'Please enter both email and password.',
            'code'    => 'missing_fields'
        ]);
    }
    $user = get_user_by('email', $email);
    if (!$user) {
        $user = get_user_by('login', $email);
    }
    if (!$user) {
        cleaning_increment_login_attempts($transient_key, $attempts);
        wp_send_json_error([
            'message' => 'No account found with this email address.',
            'code'    => 'user_not_found'
        ]);
    }
    $creds = array(
        'user_login'    => $user->user_login,
        'user_password' => $password,
        'remember'      => $remember
    );
    $login_result = wp_signon($creds, is_ssl());
    if (is_wp_error($login_result)) {
        cleaning_increment_login_attempts($transient_key, $attempts);
        wp_send_json_error([
            'message' => 'Incorrect password. Please try again.',
            'code'    => 'incorrect_password'
        ]);
    }
    delete_transient($transient_key);
    wp_set_current_user($login_result->ID);
    wp_set_auth_cookie($login_result->ID, $remember);
    $user_data = array(
        'id'           => $login_result->ID,
        'display_name' => $login_result->display_name,
        'email'        => $login_result->user_email,
        'first_name'   => get_user_meta($login_result->ID, 'first_name', true),
        'last_name'    => get_user_meta($login_result->ID, 'last_name', true),
        'phone'        => get_user_meta($login_result->ID, 'billing_phone', true),
        'address'      => get_user_meta($login_result->ID, 'billing_address', true),
    );
    $redirect_url = home_url('/cabinet/');
    if (in_array('administrator', $login_result->roles)) {
        $redirect_url = admin_url();
    }
    wp_send_json_success([
        'message'      => 'Login successful!',
        'user'         => $user_data,
        'redirect_url' => $redirect_url
    ]);
}
add_action('wp_ajax_nopriv_cleaning_login', 'cleaning_ajax_login');
add_action('wp_ajax_cleaning_login', 'cleaning_ajax_login');
function cleaning_increment_login_attempts($transient_key, $current_attempts) {
    $new_count = ($current_attempts !== false) ? $current_attempts + 1 : 1;
    set_transient($transient_key, $new_count, 15 * MINUTE_IN_SECONDS);
}
function cleaning_ajax_register() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cleaning_auth_nonce')) {
        wp_send_json_error([
            'message' => 'Security check failed. Please refresh the page and try again.',
            'code'    => 'invalid_nonce'
        ]);
    }
    $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
    $last_name  = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
    $email      = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $phone      = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $password   = isset($_POST['password']) ? $_POST['password'] : '';
    $address    = isset($_POST['address']) ? sanitize_textarea_field($_POST['address']) : '';
    $errors = array();
    if (empty($first_name)) {
        $errors[] = 'First name is required.';
    }
    if (empty($email)) {
        $errors[] = 'Email address is required.';
    } elseif (!is_email($email)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (empty($phone)) {
        $errors[] = 'Phone number is required.';
    }
    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }
    if (!empty($errors)) {
        wp_send_json_error([
            'message' => implode(' ', $errors),
            'code'    => 'validation_error'
        ]);
    }
    if (email_exists($email)) {
        wp_send_json_error([
            'message' => 'This email address is already registered. Please log in instead.',
            'code'    => 'email_exists'
        ]);
    }
    $username = sanitize_user(current(explode('@', $email)), true);
    $original_username = $username;
    $counter = 1;
    while (username_exists($username)) {
        $username = $original_username . $counter;
        $counter++;
    }
    $user_id = wp_create_user($username, $password, $email);
    if (is_wp_error($user_id)) {
        wp_send_json_error([
            'message' => 'Registration failed: ' . $user_id->get_error_message(),
            'code'    => 'registration_failed'
        ]);
    }
    wp_update_user(array(
        'ID'           => $user_id,
        'first_name'   => $first_name,
        'last_name'    => $last_name,
        'display_name' => $first_name . ($last_name ? ' ' . $last_name : ''),
    ));
    update_user_meta($user_id, 'billing_phone', $phone);
    update_user_meta($user_id, 'billing_address', $address);
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);
    $user = get_user_by('ID', $user_id);
    $user_data = array(
        'id'           => $user_id,
        'display_name' => $first_name . ($last_name ? ' ' . $last_name : ''),
        'email'        => $email,
        'first_name'   => $first_name,
        'last_name'    => $last_name,
        'phone'        => $phone,
        'address'      => $address,
    );
    wp_send_json_success([
        'message'      => 'Account created successfully! You are now logged in.',
        'user'         => $user_data,
        'redirect_url' => home_url('/cabinet/')
    ]);
}
add_action('wp_ajax_nopriv_cleaning_register', 'cleaning_ajax_register');
add_action('wp_ajax_cleaning_register', 'cleaning_ajax_register');
function cleaning_ajax_logout() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cleaning_auth_nonce')) {
        wp_send_json_error([
            'message' => 'Security check failed.',
            'code'    => 'invalid_nonce'
        ]);
    }
    wp_logout();
    wp_send_json_success([
        'message'      => 'You have been logged out.',
        'redirect_url' => home_url('/')
    ]);
}
add_action('wp_ajax_cleaning_logout', 'cleaning_ajax_logout');
function cleaning_ajax_get_user() {
    if (!is_user_logged_in()) {
        wp_send_json_error([
            'message' => 'Not logged in.',
            'code'    => 'not_logged_in'
        ]);
    }
    $user = wp_get_current_user();
    $user_data = array(
        'id'           => $user->ID,
        'display_name' => $user->display_name,
        'email'        => $user->user_email,
        'first_name'   => get_user_meta($user->ID, 'first_name', true),
        'last_name'    => get_user_meta($user->ID, 'last_name', true),
        'phone'        => get_user_meta($user->ID, 'billing_phone', true),
        'address'      => get_user_meta($user->ID, 'billing_address', true),
    );
    wp_send_json_success([
        'user' => $user_data
    ]);
}
add_action('wp_ajax_cleaning_get_user', 'cleaning_ajax_get_user');
function cleaning_ajax_update_profile() {
    if (!is_user_logged_in()) {
        wp_send_json_error([
            'message' => 'You must be logged in to update your profile.',
            'code'    => 'not_logged_in'
        ]);
    }
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cleaning_auth_nonce')) {
        wp_send_json_error([
            'message' => 'Security check failed.',
            'code'    => 'invalid_nonce'
        ]);
    }
    $user_id = get_current_user_id();
    $user = get_userdata($user_id);
    $first_name   = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
    $last_name    = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
    $phone        = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $address      = isset($_POST['address']) ? sanitize_textarea_field($_POST['address']) : '';
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    wp_update_user(array(
        'ID'           => $user_id,
        'first_name'   => $first_name,
        'last_name'    => $last_name,
        'display_name' => $first_name . ($last_name ? ' ' . $last_name : ''),
    ));
    update_user_meta($user_id, 'billing_phone', $phone);
    update_user_meta($user_id, 'billing_address', $address);
    $password_changed = false;
    if (!empty($new_password)) {
        if (strlen($new_password) < 8) {
            wp_send_json_error([
                'message' => 'Password must be at least 8 characters long.',
                'code'    => 'password_too_short'
            ]);
        }
        wp_set_password($new_password, $user_id);
        $password_changed = true;
    }
    $message = 'Profile updated successfully!';
    if ($password_changed) {
        $message .= ' Your password has been changed. Please log in again.';
    }
    wp_send_json_success([
        'message'          => $message,
        'password_changed' => $password_changed
    ]);
}
add_action('wp_ajax_cleaning_update_profile', 'cleaning_ajax_update_profile');