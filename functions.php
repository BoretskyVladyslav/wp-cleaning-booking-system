<?php
if (!defined('STRIPE_TEST_PUBLISHABLE_KEY')) {
    define('STRIPE_TEST_PUBLISHABLE_KEY', 'pk_test_51SvQg3BFBExMnI7PhknSBDw2Itc0RIGcmlF2HYWxXmlevu8pvTEYsudgYVn3QLGQm6fk7RlxJ0oDuKEHzHefkyAA00O1rJkqEO');
}
if (!defined('STRIPE_TEST_SECRET_KEY')) {
    define('STRIPE_TEST_SECRET_KEY', 'YOUR_STRIPE_SECRET_KEY');
}
if (!defined('STRIPE_PUBLISHABLE_KEY')) {
    define('STRIPE_PUBLISHABLE_KEY', STRIPE_TEST_PUBLISHABLE_KEY);
}

add_action('admin_menu', function() {
    remove_menu_page('calculator-settings');
    remove_menu_page('theme-general-settings');
    remove_menu_page('edit.php?post_type=acf-field-group');
}, 999);

add_action('template_redirect', function() {
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if (preg_match('#^/admin/?$#', $request_uri)) {
        if (is_user_logged_in() && current_user_can('manage_options')) {
            wp_redirect(admin_url());
        } else {
            wp_redirect(home_url('/login/?redirect_to=' . urlencode(admin_url())));
        }
        exit;
    }
});

add_action('admin_init', function() {
    if (!current_user_can('manage_options') && !wp_doing_ajax()) {
        wp_redirect(home_url('/cabinet/'));
        exit;
    }
});

add_action('init', function() {
    if (get_option('cleaning_login_page_created')) {
        return;
    }
    $login_page = get_page_by_path('login');
    if (!$login_page) {
        $page_id = wp_insert_post([
            'post_title'   => 'Login',
            'post_name'    => 'login',
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ]);
        if ($page_id && !is_wp_error($page_id)) {
            update_post_meta($page_id, '_wp_page_template', 'page-login.php');
        }
    }
    update_option('cleaning_login_page_created', true);
});

add_filter('login_redirect', function($redirect_to, $requested_redirect_to, $user) {
    if (!is_wp_error($user) && isset($user->roles)) {
        if (in_array('administrator', $user->roles)) {
            return admin_url();
        } else {
            return home_url('/cabinet/');
        }
    }
    return $redirect_to;
}, 10, 3);

add_action('template_redirect', function() {
    if (isset($_GET['author']) && !is_admin()) {
        wp_redirect(home_url('/404'), 301);
        exit;
    }
    if (is_author() && !current_user_can('administrator')) {
        wp_redirect(home_url('/'), 301);
        exit;
    }
});

add_filter('rest_endpoints', function($endpoints) {
    if (!is_user_logged_in()) {
        if (isset($endpoints['/wp/v2/users'])) {
            unset($endpoints['/wp/v2/users']);
        }
        if (isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
            unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
        }
    }
    return $endpoints;
});

add_action('wp_dashboard_setup', function() {
    global $wp_meta_boxes;
    $widgets_to_remove = [
        'normal' => [
            'dashboard_activity', 'dashboard_right_now', 'dashboard_recent_comments',
            'dashboard_incoming_links', 'dashboard_plugins', 'dashboard_site_health',
        ],
        'side' => [
            'dashboard_quick_press', 'dashboard_recent_drafts', 'dashboard_primary',
            'dashboard_secondary',
        ],
    ];
    foreach ($widgets_to_remove as $context => $widgets) {
        foreach ($widgets as $widget_id) {
            remove_meta_box($widget_id, 'dashboard', $context);
        }
    }
    remove_action('welcome_panel', 'wp_welcome_panel');
}, 999);

add_filter('admin_footer_text', function() {
    return '<span id="footer-thankyou">Cleaning Agency Management System v1.0</span>';
});

add_filter('update_footer', function() {
    return '';
}, 999);

add_action('wp_dashboard_setup', function() {
    wp_add_dashboard_widget(
        'cleaning_welcome_widget',
        '👋 Welcome to Your Dashboard',
        function() {
            $user = wp_get_current_user();
            ?>
            <div style="padding: 10px 0;">
                <p style="font-size: 16px; margin-bottom: 15px;">
                    Hello, <strong><?php echo esc_html($user->display_name); ?></strong>!
                    Here's your quick access panel.
                </p>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 10px;">
                    <a href="<?php echo admin_url('edit.php?post_type=cleaning_order'); ?>"
                       style="display: flex; align-items: center; gap: 8px; padding: 12px 16px; background: #f0f6fc; border-radius: 8px; text-decoration: none; color: #1e40af; font-weight: 500;">
                        <span class="dashicons dashicons-clipboard"></span>
                        View Orders
                    </a>
                    <a href="<?php echo admin_url('edit.php?post_type=page'); ?>"
                       style="display: flex; align-items: center; gap: 8px; padding: 12px 16px; background: #f0fdf4; border-radius: 8px; text-decoration: none; color: #166534; font-weight: 500;">
                        <span class="dashicons dashicons-admin-page"></span>
                        Edit Pages
                    </a>
                    <a href="<?php echo home_url('/'); ?>" target="_blank"
                       style="display: flex; align-items: center; gap: 8px; padding: 12px 16px; background: #fef3c7; border-radius: 8px; text-decoration: none; color: #92400e; font-weight: 500;">
                        <span class="dashicons dashicons-external"></span>
                        View Site
                    </a>
                </div>
            </div>
            <?php
        }
    );
}, 1);

// Cleanup function was removed, so we remove the hook.

require_once get_template_directory() . '/inc/acf-options.php';
require_once get_template_directory() . '/inc/cpt-order.php';
require_once get_template_directory() . '/inc/acf-order-fields.php';
require_once get_template_directory() . '/inc/enqueue.php'; // ADDED: Dedicated Enqueue File
require_once get_template_directory() . '/inc/ajax-auth.php';
require_once get_template_directory() . '/inc/ajax-orders.php';
require_once get_template_directory() . '/inc/payment-stripe.php';
require_once get_template_directory() . '/inc/send-lead.php';

function cleaning_emergency_cabinet_restore() {
    if (get_option('cleaning_cabinet_rescued_v1')) {
        return;
    }
    $cabinet_page = get_page_by_path('cabinet');
    if (!$cabinet_page) {
        $page_id = wp_insert_post(array(
            'post_title'   => 'Cabinet',
            'post_name'    => 'cabinet',
            'post_content' => '',
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ));
        if ($page_id && !is_wp_error($page_id)) {
            update_post_meta($page_id, '_wp_page_template', 'page-cabinet.php');
            update_option('cleaning_cabinet_rescued_v1', true);
        }
    } else {
        $tmpl = get_post_meta($cabinet_page->ID, '_wp_page_template', true);
        if ($tmpl !== 'page-cabinet.php') {
            update_post_meta($cabinet_page->ID, '_wp_page_template', 'page-cabinet.php');
        }
    }
}
add_action('init', 'cleaning_emergency_cabinet_restore');

function cleaning_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'cleaning-theme'),
    ));
}
add_action('after_setup_theme', 'cleaning_setup');

function cleaning_safe_setup() {
    if (get_option('cleaning_theme_setup_done_v3')) {
        return;
    }
    update_option('cleaning_theme_setup_done_v3', true);
    try {
        $default_pages = ['Sample Page', 'Privacy Policy'];
        foreach ($default_pages as $title) {
            $page = get_page_by_title($title);
            if ($page) wp_delete_post($page->ID, true);
        }
        $hello_world = get_page_by_path('hello-world', OBJECT, 'post');
        if ($hello_world) wp_delete_post($hello_world->ID, true);
        function cleaning_create_page_if_missing($slug, $title, $template = '') {
            if (!get_page_by_path($slug)) {
                $page_id = wp_insert_post(array(
                    'post_title'   => $title,
                    'post_name'    => $slug,
                    'post_content' => '',
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                ));
                if ($page_id && !is_wp_error($page_id) && $template) {
                    update_post_meta($page_id, '_wp_page_template', $template);
                }
                return $page_id;
            }
            return 0;
        }
        $home_id = cleaning_create_page_if_missing('home', 'Home');
        if ($home_id) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $home_id);
        }
        cleaning_create_page_if_missing('cabinet', 'Cabinet', 'page-cabinet.php');
        cleaning_create_page_if_missing('contact', 'Contact', 'page-contact.php');
        cleaning_create_page_if_missing('about', 'About Us', 'page-about.php');
        cleaning_create_page_if_missing('edit-profile', 'Edit Profile', 'page-edit-profile.php');
        cleaning_create_page_if_missing('billing', 'Billing Info', 'page-billing.php');
        cleaning_create_page_if_missing('services', 'Services', 'page-services.php');
    } catch (Exception $e) {}
}
add_action('admin_init', 'cleaning_safe_setup');

add_action('template_redirect', function() {
    $request_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    if ($request_path === 'contacts' || is_page('contacts')) {
        wp_redirect(home_url('/contact/'), 301);
        exit;
    }
});

add_action('init', function() {
    if (!get_option('contact_page_permalink_flushed_v3')) {
        flush_rewrite_rules(true);
        update_option('contact_page_permalink_flushed_v3', true);
    }
});

function cleaning_customize_register($wp_customize) {
    $wp_customize->add_section('cleaning_social_links', array(
        'title'    => __('Social Media Links', 'cleaning-theme'),
        'priority' => 30,
    ));
    $wp_customize->add_setting('social_facebook', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('social_facebook', array(
        'label'    => __('Facebook URL', 'cleaning-theme'),
        'section'  => 'cleaning_social_links',
        'type'     => 'url',
    ));
    $wp_customize->add_setting('social_instagram', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('social_instagram', array(
        'label'    => __('Instagram URL', 'cleaning-theme'),
        'section'  => 'cleaning_social_links',
        'type'     => 'url',
    ));
    $wp_customize->add_setting('social_tiktok', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('social_tiktok', array(
        'label'    => __('TikTok URL', 'cleaning-theme'),
        'section'  => 'cleaning_social_links',
        'type'     => 'url',
    ));
}
add_action('customize_register', 'cleaning_customize_register');

add_action('template_redirect', function() {
    if (!isset($_GET['payment_intent']) || !isset($_GET['redirect_status'])) {
        return;
    }
    $redirect_status = sanitize_text_field($_GET['redirect_status']);
    $payment_intent = sanitize_text_field($_GET['payment_intent']);
    if ($redirect_status !== 'succeeded') {
        return;
    }
    $order_id = 0;
    if (isset($_GET['order_id'])) {
        $order_id = intval($_GET['order_id']);
    } elseif (isset($_SESSION['pending_order_id'])) {
        $order_id = intval($_SESSION['pending_order_id']);
    }
    if (!$order_id || !get_post($order_id)) {
        return;
    }
    $order = get_post($order_id);
    if (!$order || $order->post_type !== 'cleaning_order') {
        return;
    }
    $current_status = get_post_meta($order_id, 'order_status', true);
    if ($current_status !== 'pending') {
        wp_safe_redirect(home_url('/cabinet/?payment=success&order=' . $order_id));
        exit;
    }
    update_post_meta($order_id, 'order_status', 'confirmed');
    update_post_meta($order_id, 'payment_intent_id', $payment_intent);
    update_post_meta($order_id, 'payment_confirmed_at', current_time('mysql'));
    $existing_notes = get_post_meta($order_id, 'order_notes', true);
    $note = sprintf('[%s] Payment confirmed', current_time('Y-m-d H:i:s'));
    if ($existing_notes) {
        update_post_meta($order_id, 'order_notes', $existing_notes . "\n" . $note);
    } else {
        update_post_meta($order_id, 'order_notes', $note);
    }
    if (isset($_SESSION['pending_order_id'])) {
        unset($_SESSION['pending_order_id']);
    }
    wp_safe_redirect(home_url('/cabinet/?payment=success&order=' . $order_id));
    exit;
});

// Register Leads Custom Post Type
function cleaning_register_leads_cpt() {
    register_post_type('cleaning_lead', array(
        'labels' => array(
            'name' => 'Leads',
            'singular_name' => 'Lead',
            'menu_name' => 'Leads',
            'all_items' => 'All Leads',
            'edit_item' => 'View Lead',
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'supports' => array('title', 'editor', 'custom-fields'),
        'menu_icon' => 'dashicons-email-alt',
        'capabilities' => array(
            'create_posts' => 'do_not_allow', // Leads are only created programmatically
        ),
        'map_meta_cap' => true,
    ));
}
add_action('init', 'cleaning_register_leads_cpt');