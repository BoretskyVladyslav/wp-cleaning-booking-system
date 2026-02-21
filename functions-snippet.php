function cleaning_assets() {
    $theme_uri = get_template_directory_uri();
    $theme_dir = get_template_directory();

    $main_css_path = $theme_dir . '/assets/css/main.css';
    $main_css_ver  = file_exists($main_css_path) ? filemtime($main_css_path) : time();
    wp_enqueue_style('cleaning-main', $theme_uri . '/assets/css/main.css', [], $main_css_ver);
    wp_enqueue_style('flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', [], '4.6.13');
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', [], '11.0.0');

    wp_enqueue_script('jquery');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', [], '11.0.0', true);
    wp_enqueue_script('flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr', [], '4.6.13', true);
    wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', [], null, true);

    $app_js_path = $theme_dir . '/assets/js/app.js';
    $app_js_ver  = file_exists($app_js_path) ? filemtime($app_js_path) : time();
    wp_enqueue_script('cleaning-app', $theme_uri . '/assets/js/app.js', ['jquery', 'flatpickr-js', 'stripe-js', 'swiper-js'], $app_js_ver, true);

    $cleaning_vars = array(
        'ajaxUrl'    => admin_url('admin-ajax.php'),
        'nonce'      => wp_create_nonce('cleaning_auth_nonce'),
        'isLoggedIn' => is_user_logged_in(),
        'loginUrl'   => home_url('/login/'),
        'bookingUrl' => site_url('/cabinet/'),
    );

    $cleaning_vars['price_1bd_reg'] = intval(get_option('price_1bd_reg', 110));
    $cleaning_vars['price_2bd_reg'] = intval(get_option('price_2bd_reg', 132));
    $cleaning_vars['price_3bd_reg'] = intval(get_option('price_3bd_reg', 150));
    $cleaning_vars['price_4bd_reg'] = intval(get_option('price_4bd_reg', 170));
    $cleaning_vars['price_5bd_reg'] = intval(get_option('price_5bd_reg', 190));
    $cleaning_vars['price_6bd_reg'] = intval(get_option('price_6bd_reg', 230));

    $cleaning_vars['price_1bd_deep'] = intval(get_option('price_1bd_deep', 190));
    $cleaning_vars['price_2bd_deep'] = intval(get_option('price_2bd_deep', 190));
    $cleaning_vars['price_3bd_deep'] = intval(get_option('price_3bd_deep', 190));
    $cleaning_vars['price_4bd_deep'] = intval(get_option('price_4bd_deep', 210));
    $cleaning_vars['price_5bd_deep'] = intval(get_option('price_5bd_deep', 260));
    $cleaning_vars['price_6bd_deep'] = intval(get_option('price_6bd_deep', 320));

    $cleaning_vars['price_1bd_move'] = intval(get_option('price_1bd_move', 165));
    $cleaning_vars['price_2bd_move'] = intval(get_option('price_2bd_move', 170));
    $cleaning_vars['price_3bd_move'] = intval(get_option('price_3bd_move', 170));
    $cleaning_vars['price_4bd_move'] = intval(get_option('price_4bd_move', 210));
    $cleaning_vars['price_5bd_move'] = intval(get_option('price_5bd_move', 260));
    $cleaning_vars['price_6bd_move'] = intval(get_option('price_6bd_move', 320));

    $cleaning_vars['price_1bd_post'] = intval(get_option('price_1bd_post', 210));
    $cleaning_vars['price_2bd_post'] = intval(get_option('price_2bd_post', 230));
    $cleaning_vars['price_3bd_post'] = intval(get_option('price_3bd_post', 260));
    $cleaning_vars['price_4bd_post'] = intval(get_option('price_4bd_post', 310));
    $cleaning_vars['price_5bd_post'] = intval(get_option('price_5bd_post', 360));
    $cleaning_vars['price_6bd_post'] = intval(get_option('price_6bd_post', 410));

    $cleaning_vars['price_per_bath'] = intval(get_option('price_per_bath', 35));
    $cleaning_vars['price_fridge']   = intval(get_option('price_fridge', 40));
    $cleaning_vars['price_oven']     = intval(get_option('price_oven', 40));
    $cleaning_vars['price_laundry']  = intval(get_option('price_laundry', 15));
    $cleaning_vars['price_window']   = intval(get_option('price_window', 7));
    $cleaning_vars['price_cabinets'] = intval(get_option('price_cabinets', 35));

    $cleaning_vars['price_half_bath_reg']  = intval(get_option('price_half_bath_reg', 10));
    $cleaning_vars['price_half_bath_deep'] = intval(get_option('price_half_bath_deep', 20));

    $cleaning_vars['sqft_threshold'] = intval(get_option('price_sqft_threshold', 1000));
    $cleaning_vars['sqft_step']      = intval(get_option('price_sqft_step', 500));
    $cleaning_vars['sqft_increment'] = intval(get_option('price_sqft_increment', 10));

    wp_localize_script('cleaning-app', 'cleaning_vars', $cleaning_vars);
}
add_action('wp_enqueue_scripts', 'cleaning_assets'); 