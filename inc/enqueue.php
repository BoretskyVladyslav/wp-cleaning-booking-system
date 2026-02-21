<?php
/**
 * File: inc/enqueue.php
 * Enqueue Scripts and Localize Variables
 */

function cleaning_enqueue_calculator_assets() {
    $theme_uri = get_template_directory_uri();
    $theme_dir = get_template_directory();

    // CSS
    $main_css_path = $theme_dir . '/assets/css/main.css';
    $main_css_ver  = file_exists($main_css_path) ? filemtime($main_css_path) : time();
    wp_enqueue_style('cleaning-style', $theme_uri . '/assets/css/main.css', array(), $main_css_ver);
    wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), '4.6.13');
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0');

    // JS
    wp_enqueue_script('jquery');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true);
    wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', array(), '4.6.13', true);
    wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', array(), null, true);

    $app_js_path = $theme_dir . '/assets/js/app.js';
    $app_js_ver  = file_exists($app_js_path) ? filemtime($app_js_path) : time();
    wp_enqueue_script('cleaning-calculator', $theme_uri . '/assets/js/app.js', array('jquery', 'flatpickr-js', 'stripe-js', 'swiper-js'), $app_js_ver, true);

    // Localize script with all necessary variables
    $cleaning_vars = array(
        'ajaxUrl'    => admin_url('admin-ajax.php', 'https'),
        'nonce'      => wp_create_nonce('cleaning_auth_nonce'),
        'isLoggedIn' => is_user_logged_in(),
        'loginUrl'   => home_url('/login/'),
        'bookingUrl' => home_url('/cabinet/'),
        'stripe_publishable_key' => get_option('stripe_publishable_key', ''),
        
        // EXTRAS
        'cleaning_fridge_price'   => intval(get_option('cleaning_fridge_price', 40)),
        'cleaning_oven_price'     => intval(get_option('cleaning_oven_price', 40)),
        'cleaning_cabinets_price' => intval(get_option('cleaning_cabinets_price', 50)), 
        'cleaning_windows_price'  => intval(get_option('cleaning_windows_price', 5)),
        'cleaning_laundry_price'  => intval(get_option('cleaning_laundry_price', 25)),

        // DISCOUNTS
        'cleaning_discount_weekly'     => floatval(get_option('cleaning_discount_weekly', 20)),
        'cleaning_discount_biweekly'   => floatval(get_option('cleaning_discount_biweekly', 15)),
        'cleaning_discount_monthly'    => floatval(get_option('cleaning_discount_monthly', 10)),
        'frequency_3_weeks_multiplier' => floatval(get_option('frequency_3_weeks_multiplier', 5)),

        // CORE CONFIG
        'price_per_bath'       => intval(get_option('price_per_bath', 35)),
        'deep_extra_bath_fallback' => intval(get_option('deep_extra_bath_fallback', 40)),
        'move_in_extra_bath_fallback' => intval(get_option('move_in_extra_bath_fallback', 40)),
        'post_construction_bath_price' => intval(get_option('post_construction_bath_price', 40)),
        
        'price_half_bath_reg'  => intval(get_option('price_half_bath_reg', 10)),
        'price_half_bath_deep' => intval(get_option('price_half_bath_deep', 20)),
        'move_in_half_bath_price' => intval(get_option('move_in_half_bath_price', 20)),
        'post_construction_half_bath_price' => intval(get_option('post_construction_half_bath_price', 25)),
        
        'price_sqft_threshold' => intval(get_option('price_sqft_threshold', 1000)),
        'price_sqft_step'      => intval(get_option('price_sqft_step', 500)),
        'price_sqft_increment' => intval(get_option('price_sqft_increment', 10)),

        // POST CONSTRUCTION (Linear)
        'price_1bd_post' => intval(get_option('price_1bd_post', 210)),
        'price_2bd_post' => intval(get_option('price_2bd_post', 230)),
        'price_3bd_post' => intval(get_option('price_3bd_post', 260)),
        'price_4bd_post' => intval(get_option('price_4bd_post', 310)),
        'price_5bd_post' => intval(get_option('price_5bd_post', 360)),
        'price_6bd_post' => intval(get_option('price_6bd_post', 410)),
    );

    // Build the DYNAMIC MATRIX
    $matrix_config = function_exists('cleaning_get_matrix_config') ? cleaning_get_matrix_config() : [
        1 => 2, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 5 
    ];
    
    $services = ['regular', 'deep', 'move_in', 'post_construction'];
    $matrix_data = [];

    foreach ($services as $service) {
        $matrix_data[$service] = [];
        foreach ($matrix_config as $beds => $max_baths) {
            $matrix_data[$service][$beds] = [];
            for ($baths = 1; $baths <= $max_baths; $baths++) {
                $option_key = "matrix_{$service}_{$beds}bd_{$baths}ba";
                // Get value, default 0
                $matrix_data[$service][$beds][$baths] = intval(get_option($option_key, 0));
            }
        }
    }
    
    $cleaning_vars['matrix'] = $matrix_data;
    
    wp_localize_script('cleaning-calculator', 'cleaning_vars', $cleaning_vars);
}
add_action('wp_enqueue_scripts', 'cleaning_enqueue_calculator_assets');