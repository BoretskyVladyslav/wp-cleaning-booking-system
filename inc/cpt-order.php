<?php
if (!defined('ABSPATH')) {
    exit;
}
function cleaning_register_order_cpt() {
    $labels = array(
        'name'                  => _x('Orders', 'Post type general name', 'cleaning-theme'),
        'singular_name'         => _x('Order', 'Post type singular name', 'cleaning-theme'),
        'menu_name'             => _x('Orders', 'Admin Menu text', 'cleaning-theme'),
        'name_admin_bar'        => _x('Order', 'Add New on Toolbar', 'cleaning-theme'),
        'add_new'               => __('Add New', 'cleaning-theme'),
        'add_new_item'          => __('Add New Order', 'cleaning-theme'),
        'new_item'              => __('New Order', 'cleaning-theme'),
        'edit_item'             => __('Edit Order', 'cleaning-theme'),
        'view_item'             => __('View Order', 'cleaning-theme'),
        'all_items'             => __('All Orders', 'cleaning-theme'),
        'search_items'          => __('Search Orders', 'cleaning-theme'),
        'parent_item_colon'     => __('Parent Orders:', 'cleaning-theme'),
        'not_found'             => __('No orders found.', 'cleaning-theme'),
        'not_found_in_trash'    => __('No orders found in Trash.', 'cleaning-theme'),
        'featured_image'        => _x('Order Cover Image', 'Overrides the "Featured Image" phrase', 'cleaning-theme'),
        'set_featured_image'    => _x('Set cover image', 'Overrides the "Set featured image" phrase', 'cleaning-theme'),
        'remove_featured_image' => _x('Remove cover image', 'Overrides the "Remove featured image" phrase', 'cleaning-theme'),
        'use_featured_image'    => _x('Use as cover image', 'Overrides the "Use as featured image" phrase', 'cleaning-theme'),
        'archives'              => _x('Order archives', 'The post type archive label used in nav menus', 'cleaning-theme'),
        'insert_into_item'      => _x('Insert into order', 'Overrides the "Insert into post" phrase', 'cleaning-theme'),
        'uploaded_to_this_item' => _x('Uploaded to this order', 'Overrides the "Uploaded to this post" phrase', 'cleaning-theme'),
        'filter_items_list'     => _x('Filter orders list', 'Screen reader text for the filter links', 'cleaning-theme'),
        'items_list_navigation' => _x('Orders list navigation', 'Screen reader text for the pagination', 'cleaning-theme'),
        'items_list'            => _x('Orders list', 'Screen reader text for the items list', 'cleaning-theme'),
    );
    $args = array(
        'labels'              => $labels,
        'public'              => false,              
        'publicly_queryable'  => false,              
        'show_ui'             => true,               
        'show_in_menu'        => true,               
        'query_var'           => false,              
        'rewrite'             => false,              
        'capability_type'     => 'post',
        'has_archive'         => false,              
        'hierarchical'        => false,
        'menu_position'       => 25,                 
        'menu_icon'           => 'dashicons-clipboard', 
        'supports'            => array('title', 'author', 'custom-fields'),
        'show_in_rest'        => false,              
    );
    register_post_type('cleaning_order', $args);
}
add_action('init', 'cleaning_register_order_cpt');
function cleaning_order_admin_columns($columns) {
    $new_columns = array(
        'cb'             => $columns['cb'],
        'title'          => __('Order', 'cleaning-theme'),
        'order_status'   => __('Status', 'cleaning-theme'),
        'customer_info'  => __('Customer', 'cleaning-theme'),
        'service_info'   => __('Service', 'cleaning-theme'),
        'cleaning_date'  => __('Cleaning Date', 'cleaning-theme'),
        'order_total'    => __('Total', 'cleaning-theme'),
        'date'           => __('Order Date', 'cleaning-theme'),
    );
    return $new_columns;
}
add_filter('manage_cleaning_order_posts_columns', 'cleaning_order_admin_columns');
function cleaning_order_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'order_status':
            $status = get_post_meta($post_id, 'order_status', true);
            $status_config = array(
                'pending'     => array('label' => 'Pending',     'color' => '#f59e0b', 'icon' => '⏳'),
                'confirmed'   => array('label' => 'Confirmed',   'color' => '#06b6d4', 'icon' => '✓'),
                'in_progress' => array('label' => 'In Progress', 'color' => '#3b82f6', 'icon' => '🔄'),
                'completed'   => array('label' => 'Completed',   'color' => '#10b981', 'icon' => '✅'),
                'cancelled'   => array('label' => 'Cancelled',   'color' => '#ef4444', 'icon' => '✖'),
            );
            if (isset($status_config[$status])) {
                $cfg = $status_config[$status];
                printf(
                    '<span style="display:inline-block; padding:4px 10px; border-radius:12px; background:%s; color:#fff; font-size:12px; font-weight:600;">%s %s</span>',
                    esc_attr($cfg['color']),
                    $cfg['icon'],
                    esc_html($cfg['label'])
                );
            } else {
                echo '<span style="color:#6b7280;">—</span>';
            }
            break;
        case 'customer_info':
            $name  = get_post_meta($post_id, 'customer_name', true);
            $phone = get_post_meta($post_id, 'customer_phone', true);
            $email = get_post_meta($post_id, 'customer_email', true);
            echo '<strong>' . esc_html($name ?: '—') . '</strong>';
            if ($phone) {
                echo '<br><a href="tel:' . esc_attr($phone) . '" style="color:#3b82f6; text-decoration:none;">' . esc_html($phone) . '</a>';
            }
            if ($email) {
                echo '<br><small style="color:#6b7280;">' . esc_html($email) . '</small>';
            }
            break;
        case 'service_info':
            $service  = get_post_meta($post_id, 'service_type', true);
            $beds     = get_post_meta($post_id, 'bedrooms', true);
            $baths    = get_post_meta($post_id, 'bathrooms', true);
            $sqft     = get_post_meta($post_id, 'sqft', true);
            $service_labels = array(
                'reg'  => 'Regular',
                'deep' => 'Deep',
                'move' => 'Move In/Out',
            );
            $service_label = isset($service_labels[$service]) ? $service_labels[$service] : $service;
            echo '<strong>' . esc_html($service_label ?: '—') . '</strong>';
            if ($beds || $baths) {
                echo '<br><small style="color:#6b7280;">';
                echo $beds ? esc_html($beds) . ' bed' . ($beds > 1 ? 's' : '') : '';
                echo ($beds && $baths) ? ', ' : '';
                echo $baths ? esc_html($baths) . ' bath' . ($baths > 1 ? 's' : '') : '';
                echo '</small>';
            }
            if ($sqft) {
                echo '<br><small style="color:#9ca3af;">' . number_format((int)$sqft) . ' sq ft</small>';
            }
            break;
        case 'cleaning_date':
            $date = get_post_meta($post_id, 'cleaning_date', true);
            $time = get_post_meta($post_id, 'cleaning_time', true);
            if ($date) {
                $timestamp = strtotime($date);
                if ($timestamp) {
                    $formatted_date = date('M j, Y', $timestamp);
                    echo '<strong>' . esc_html($formatted_date) . '</strong>';
                } else {
                    echo '<strong>' . esc_html($date) . '</strong>';
                }
                if ($time) {
                    echo '<br><small style="color:#6b7280;">@ ' . esc_html($time) . '</small>';
                }
            } else {
                echo '<span style="color:#6b7280;">—</span>';
            }
            break;
        case 'order_total':
            $total = get_post_meta($post_id, 'order_total', true);
            if ($total) {
                echo '<strong style="color:#10b981; font-size:14px;">$' . esc_html(number_format((float)$total, 2)) . '</strong>';
            } else {
                echo '<span style="color:#6b7280;">—</span>';
            }
            break;
    }
}
add_action('manage_cleaning_order_posts_custom_column', 'cleaning_order_admin_column_content', 10, 2);
function cleaning_order_sortable_columns($columns) {
    $columns['order_status']  = 'order_status';
    $columns['order_total']   = 'order_total';
    $columns['cleaning_date'] = 'cleaning_date';
    $columns['customer_info'] = 'customer_name';
    return $columns;
}
add_filter('manage_edit-cleaning_order_sortable_columns', 'cleaning_order_sortable_columns');
function cleaning_order_sortable_query($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    if ($query->get('post_type') !== 'cleaning_order') {
        return;
    }
    $orderby = $query->get('orderby');
    switch ($orderby) {
        case 'order_status':
            $query->set('meta_key', 'order_status');
            $query->set('orderby', 'meta_value');
            break;
        case 'order_total':
            $query->set('meta_key', 'order_total');
            $query->set('orderby', 'meta_value_num');
            break;
        case 'cleaning_date':
            $query->set('meta_key', 'cleaning_date');
            $query->set('orderby', 'meta_value');
            break;
        case 'customer_name':
            $query->set('meta_key', 'customer_name');
            $query->set('orderby', 'meta_value');
            break;
    }
}
add_action('pre_get_posts', 'cleaning_order_sortable_query');
function cleaning_order_admin_filter_by_status() {
    global $typenow;
    if ($typenow !== 'cleaning_order') {
        return;
    }
    $statuses = array(
        'pending'     => '⏳ Pending',
        'confirmed'   => '✓ Confirmed',
        'in_progress' => '🔄 In Progress',
        'completed'   => '✅ Completed',
        'cancelled'   => '✖ Cancelled',
    );
    $current_status = isset($_GET['order_status_filter']) ? sanitize_text_field($_GET['order_status_filter']) : '';
    echo '<select name="order_status_filter" style="min-width:150px;">';
    echo '<option value="">All Statuses</option>';
    foreach ($statuses as $value => $label) {
        $selected = ($current_status === $value) ? ' selected="selected"' : '';
        echo '<option value="' . esc_attr($value) . '"' . $selected . '>' . esc_html($label) . '</option>';
    }
    echo '</select>';
}
add_action('restrict_manage_posts', 'cleaning_order_admin_filter_by_status');
function cleaning_order_filter_query($query) {
    global $pagenow, $typenow;
    if ($pagenow !== 'edit.php' || $typenow !== 'cleaning_order' || !is_admin()) {
        return;
    }
    if (!$query->is_main_query()) {
        return;
    }
    if (isset($_GET['order_status_filter']) && !empty($_GET['order_status_filter'])) {
        $meta_query = $query->get('meta_query') ?: array();
        $meta_query[] = array(
            'key'   => 'order_status',
            'value' => sanitize_text_field($_GET['order_status_filter']),
        );
        $query->set('meta_query', $meta_query);
    }
}
add_action('pre_get_posts', 'cleaning_order_filter_query');
function cleaning_order_admin_styles() {
    global $typenow;
    if ($typenow !== 'cleaning_order') {
        return;
    }
    echo '<style>
        /* Wider columns for better readability */
        .column-order_status { width: 120px; }
        .column-customer_info { width: 180px; }
        .column-service_info { width: 150px; }
        .column-cleaning_date { width: 130px; }
        .column-order_total { width: 90px; text-align: right !important; }
        /* Fix alignment */
        .column-order_total .row-actions { text-align: left; }
        /* Row hover effect */
        .wp-list-table tbody tr:hover {
            background: #f0f9ff !important;
        }
        /* Status badge hover */
        .column-order_status span:hover {
            opacity: 0.9;
        }
        /* Quick action links styling */
        .row-actions .confirm-action a { color: #10b981 !important; font-weight: 600; }
        .row-actions .complete-action a { color: #3b82f6 !important; font-weight: 600; }
        .row-actions .cancel-action a { color: #ef4444 !important; }
        .row-actions .confirm-action a:hover,
        .row-actions .complete-action a:hover,
        .row-actions .cancel-action a:hover { text-decoration: underline; }
    </style>';
}
add_action('admin_head', 'cleaning_order_admin_styles');
function cleaning_order_row_actions($actions, $post) {
    if ($post->post_type !== 'cleaning_order') {
        return $actions;
    }
    $current_status = get_post_meta($post->ID, 'order_status', true);
    $base_url = admin_url('edit.php?post_type=cleaning_order');
    if ($current_status === 'pending') {
        $confirm_url = wp_nonce_url(
            add_query_arg(array(
                'cleaning_action' => 'change_status',
                'new_status'      => 'confirmed',
                'order_id'        => $post->ID,
            ), $base_url),
            'cleaning_status_' . $post->ID
        );
        $actions['confirm'] = '<span class="confirm-action"><a href="' . esc_url($confirm_url) . '">✅ Confirm</a></span>';
    }
    if (in_array($current_status, array('confirmed', 'in_progress'))) {
        $complete_url = wp_nonce_url(
            add_query_arg(array(
                'cleaning_action' => 'change_status',
                'new_status'      => 'completed',
                'order_id'        => $post->ID,
            ), $base_url),
            'cleaning_status_' . $post->ID
        );
        $actions['complete'] = '<span class="complete-action"><a href="' . esc_url($complete_url) . '">🏁 Complete</a></span>';
    }
    if ($current_status !== 'cancelled') {
        $cancel_url = wp_nonce_url(
            add_query_arg(array(
                'cleaning_action' => 'change_status',
                'new_status'      => 'cancelled',
                'order_id'        => $post->ID,
            ), $base_url),
            'cleaning_status_' . $post->ID
        );
        $actions['cancel'] = '<span class="cancel-action"><a href="' . esc_url($cancel_url) . '" onclick="return confirm(\'Are you sure you want to cancel this order?\');">✖ Cancel</a></span>';
    }
    return $actions;
}
add_filter('post_row_actions', 'cleaning_order_row_actions', 10, 2);
function cleaning_handle_status_change() {
    if (!isset($_GET['cleaning_action']) || $_GET['cleaning_action'] !== 'change_status') {
        return;
    }
    if (!isset($_GET['order_id']) || !isset($_GET['new_status'])) {
        return;
    }
    $order_id   = intval($_GET['order_id']);
    $new_status = sanitize_text_field($_GET['new_status']);
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'cleaning_status_' . $order_id)) {
        wp_die('Security check failed. Please try again.');
    }
    if (!current_user_can('edit_post', $order_id)) {
        wp_die('You do not have permission to modify this order.');
    }
    $valid_statuses = array('pending', 'confirmed', 'in_progress', 'completed', 'cancelled');
    if (!in_array($new_status, $valid_statuses)) {
        wp_die('Invalid status.');
    }
    if (get_post_type($order_id) !== 'cleaning_order') {
        wp_die('Invalid order.');
    }
    update_post_meta($order_id, 'order_status', $new_status);
    $redirect_url = admin_url('edit.php?post_type=cleaning_order');
    if (isset($_GET['order_status_filter']) && !empty($_GET['order_status_filter'])) {
        $redirect_url = add_query_arg('order_status_filter', sanitize_text_field($_GET['order_status_filter']), $redirect_url);
    }
    $redirect_url = add_query_arg('status_updated', '1', $redirect_url);
    wp_redirect($redirect_url);
    exit;
}
add_action('admin_init', 'cleaning_handle_status_change');
function cleaning_status_change_notice() {
    global $typenow;
    if ($typenow !== 'cleaning_order') {
        return;
    }
    if (isset($_GET['status_updated']) && $_GET['status_updated'] === '1') {
        echo '<div class="notice notice-success is-dismissible"><p>✅ Order status updated successfully!</p></div>';
    }
}
add_action('admin_notices', 'cleaning_status_change_notice');
function cleaning_order_dashboard_stats() {
    $statuses = array('pending', 'confirmed', 'in_progress', 'completed', 'cancelled');
    $counts = array();
    foreach ($statuses as $status) {
        $args = array(
            'post_type'   => 'cleaning_order',
            'post_status' => 'publish',
            'meta_query'  => array(
                array(
                    'key'   => 'order_status',
                    'value' => $status,
                ),
            ),
            'fields' => 'ids',
        );
        $query = new WP_Query($args);
        $counts[$status] = $query->found_posts;
        wp_reset_postdata();
    }
    $status_config = array(
        'pending'     => array('bg' => '#fef3c7', 'color' => '#f59e0b', 'text' => '#92400e', 'label' => 'Pending'),
        'confirmed'   => array('bg' => '#cffafe', 'color' => '#06b6d4', 'text' => '#0e7490', 'label' => 'Confirmed'),
        'in_progress' => array('bg' => '#dbeafe', 'color' => '#3b82f6', 'text' => '#1e40af', 'label' => 'In Progress'),
        'completed'   => array('bg' => '#d1fae5', 'color' => '#10b981', 'text' => '#065f46', 'label' => 'Completed'),
        'cancelled'   => array('bg' => '#fee2e2', 'color' => '#ef4444', 'text' => '#991b1b', 'label' => 'Cancelled'),
    );
    ?>
    <style>
        .cleaning-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 12px;
            text-align: center;
        }
        .cleaning-stat-box {
            padding: 16px 10px;
            border-radius: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .cleaning-stat-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .cleaning-stat-number {
            font-size: 28px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 4px;
        }
        .cleaning-stat-label {
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cleaning-widget-footer {
            margin-top: 16px;
            text-align: center;
            padding-top: 12px;
            border-top: 1px solid 
        }
        @media screen and (max-width: 782px) {
            .cleaning-stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            .cleaning-stat-box {
                padding: 12px 8px;
            }
            .cleaning-stat-number {
                font-size: 22px;
            }
            .cleaning-stat-label {
                font-size: 10px;
            }
            .cleaning-widget-footer {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .cleaning-widget-footer .button {
                margin-left: 0 !important;
            }
        }
        @media screen and (max-width: 480px) {
            .cleaning-stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <div class="cleaning-stats-grid">
        <?php foreach ($status_config as $key => $cfg): ?>
        <a href="<?php echo esc_url(admin_url('edit.php?post_type=cleaning_order&order_status_filter=' . $key)); ?>" 
           class="cleaning-stat-box" 
           style="background:<?php echo $cfg['bg']; ?>; text-decoration:none;">
            <div class="cleaning-stat-number" style="color:<?php echo $cfg['color']; ?>;">
                <?php echo $counts[$key]; ?>
            </div>
            <div class="cleaning-stat-label" style="color:<?php echo $cfg['text']; ?>;">
                <?php echo $cfg['label']; ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <div class="cleaning-widget-footer">
        <a href="<?php echo admin_url('edit.php?post_type=cleaning_order'); ?>" class="button button-primary">
            View All Orders
        </a>
        <a href="<?php echo admin_url('post-new.php?post_type=cleaning_order'); ?>" class="button" style="margin-left:8px;">
            + New Order
        </a>
    </div>
    <?php
}
function cleaning_register_order_widget() {
    wp_add_dashboard_widget(
        'cleaning_orders_stats',
        '📋 Orders Overview',
        'cleaning_order_dashboard_stats'
    );
}
add_action('wp_dashboard_setup', 'cleaning_register_order_widget', 20);