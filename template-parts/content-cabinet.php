<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!is_user_logged_in()) {
    wp_redirect(home_url('/'));
    exit;
}

$current_user = wp_get_current_user();
$user_id = get_current_user_id();

// Separate orders by payment/completion status
$unpaid_orders = [];    // pending (awaiting payment)
$active_orders = [];    // confirmed, processing, in_progress (paid & scheduled)
$history_orders = [];   // completed, cancelled, failed, refunded

$orders_query = new WP_Query([ 
    'post_type'      => 'cleaning_order',
    'author'         => $user_id,
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post_status'    => array('publish', 'pending', 'draft', 'private'),
]);

if ($orders_query->have_posts()) {
    while ($orders_query->have_posts()) {
        $orders_query->the_post();
        $order_id = get_the_ID();
        $order_data = [
            'id'              => $order_id,
            'title'           => get_the_title(),
            'date'            => get_the_date('F j, Y'),
            'time'            => get_post_meta($order_id, 'cleaning_time', true),
            'status'          => get_post_meta($order_id, 'order_status', true) ?: 'pending',
            'service_type'    => get_post_meta($order_id, 'service_type', true) ?: 'reg',
            'bedrooms'        => get_post_meta($order_id, 'bedrooms', true) ?: 1,
            'bathrooms'       => get_post_meta($order_id, 'bathrooms', true) ?: 1,
            'sqft'            => get_post_meta($order_id, 'sqft', true) ?: 0,
            'extras'          => get_post_meta($order_id, 'order_extras', true) ?: '',
            'cleaning_date'   => get_post_meta($order_id, 'cleaning_date', true) ?: '',
            'cleaning_time'   => get_post_meta($order_id, 'cleaning_time', true) ?: '',
            'address'         => get_post_meta($order_id, 'customer_address', true) ?: '',
            'phone'           => get_post_meta($order_id, 'customer_phone', true) ?: '',
            'customer_name'   => get_post_meta($order_id, 'customer_name', true) ?: '',
            'total'           => floatval(get_post_meta($order_id, 'order_total', true)) ?: 0,
            'receipt_details' => get_post_meta($order_id, 'receipt_details', true) ?: '',
        ];

        // Professional Order Lifecycle Categorization
        switch ($order_data['status']) {
            case 'pending':
            case 'on-hold':
                $unpaid_orders[] = $order_data;
                break;
            case 'confirmed':
            case 'processing':
            case 'in_progress':
                $active_orders[] = $order_data;
                break;
            case 'completed':
            case 'cancelled':
            case 'failed':
            case 'refunded':
                $history_orders[] = $order_data;
                break;
            default:
                $unpaid_orders[] = $order_data;
        }
    }
    wp_reset_postdata();
}

function cabinet_get_service_label($key) {
    $labels = [
        'reg'  => 'Regular Cleaning',
        'deep' => 'Deep Cleaning',
        'move' => 'Move In/Out Cleaning',
        'post' => 'Post-Construction',
    ];
    return $labels[$key] ?? ucfirst($key);
}

function cabinet_get_status_badge($status) {
    $badges = [
        'pending'     => ['label' => 'Awaiting Payment', 'class' => 'status-pending',     'icon' => 'fa-clock'],
        'confirmed'   => ['label' => 'Paid & Confirmed','class' => 'status-confirmed',   'icon' => 'fa-check-circle'],
        'processing'  => ['label' => 'In Progress',     'class' => 'status-processing',  'icon' => 'fa-spinner fa-spin'],
        'in_progress' => ['label' => 'In Progress',     'class' => 'status-processing',  'icon' => 'fa-broom'],
        'on-hold'     => ['label' => 'On Hold',         'class' => 'status-pending',     'icon' => 'fa-pause'],
        'completed'   => ['label' => 'Completed',       'class' => 'status-completed',   'icon' => 'fa-check-circle'],
        'cancelled'   => ['label' => 'Cancelled',       'class' => 'status-canceled',    'icon' => 'fa-times-circle'],
        'failed'      => ['label' => 'Failed',          'class' => 'status-canceled',    'icon' => 'fa-exclamation-circle'],
        'refunded'    => ['label' => 'Refunded',        'class' => 'status-canceled',    'icon' => 'fa-undo'],
    ];
    $b = $badges[$status] ?? ['label' => ucfirst($status), 'class' => 'status-default', 'icon' => 'fa-question'];
    return sprintf(
        '<span class="status-badge %s"><i class="fa-solid %s"></i> %s</span>',
        esc_attr($b['class']),
        esc_attr($b['icon']),
        esc_html($b['label'])
    );
}

function cabinet_render_order_card($order, $show_actions = 'none') {
    $service_label = cabinet_get_service_label($order['service_type']);
    $status_badge = cabinet_get_status_badge($order['status']);
    $date_str = $order['cleaning_date'] ?: $order['date'];
    $timestamp = strtotime($date_str);
    $today = strtotime('today');
    $tomorrow = strtotime('tomorrow');

    if ($timestamp >= $today && $timestamp < $tomorrow) {
        $formatted_date = 'Today';
    } elseif ($timestamp >= $tomorrow && $timestamp < strtotime('+2 days')) {
        $formatted_date = 'Tomorrow';
    } else {
        $formatted_date = date('F j, Y', $timestamp);
    }

    $datetime = $formatted_date . ($order['time'] ? ', ' . esc_html($order['time']) : '');

    $info_parts = [];
    if ($order['bedrooms']) {
        $info_parts[] = $order['bedrooms'] . ' bed' . ($order['bedrooms'] > 1 ? 's' : '');
    }
    if ($order['bathrooms']) {
        $info_parts[] = $order['bathrooms'] . ' bath' . ($order['bathrooms'] > 1 ? 's' : '');
    }
    if ($order['sqft']) {
        $info_parts[] = number_format($order['sqft']) . ' sq ft';
    }
    $info_line = implode(' • ', $info_parts);
    ?>
    <div class="order-card" data-order-id="<?php echo esc_attr($order['id']); ?>">
        <div class="order-card__header">
            <div>
                <span class="order-date"><?php echo esc_html($datetime); ?></span>
                <?php echo $status_badge; ?>
            </div>
            <div class="order-price">
                $<?php echo number_format($order['total'], 2); ?>
            </div>
        </div>
        
        <div class="card-body">
            <div class="service-icon">
                <i class="fa-solid fa-broom"></i>
            </div>
            <div class="service-details">
                <h3><?php echo esc_html($service_label); ?></h3>
                <p class="service-info"><?php echo esc_html($info_line); ?></p>
                <?php if ($order['address']): ?>
                <p class="service-address"><i class="fa-solid fa-location-dot"></i> <?php echo esc_html($order['address']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($show_actions === 'unpaid'): ?>
        <div class="order-card__footer">
            <div class="order-buttons-row">
                <button class="btn-custom btn-pay js-pay-order" 
                        data-order-id="<?php echo esc_attr($order['id']); ?>"
                        data-total="<?php echo esc_attr($order['total']); ?>">
                    <i class="fa-solid fa-credit-card"></i> Pay Now
                </button>
                <button class="btn-custom btn-cancel btn-cancel-order" 
                        data-order-id="<?php echo esc_attr($order['id']); ?>">
                    <i class="fa-solid fa-times"></i> Cancel
                </button>
            </div>
            <button type="button" class="js-toggle-details">
                <i class="fa-solid fa-chevron-down"></i> View Details
            </button>
        </div>
        <?php elseif ($show_actions === 'paid'): ?>
        <div class="order-card__footer">
            <div class="paid-status-badge">
                <i class="fa-solid fa-check-circle"></i>
                <span><strong>Paid & Confirmed</strong> — Need to reschedule? Contact us.</span>
            </div>
            <button type="button" class="js-toggle-details">
                <i class="fa-solid fa-chevron-down"></i> View Details
            </button>
        </div>
        <?php else: ?>
        <div class="order-card__footer">
            <button type="button" class="js-toggle-details">
                <i class="fa-solid fa-chevron-down"></i> View Details
            </button>
        </div>
        <?php endif; ?>
        
        <!-- Order Details (Hidden by default) -->
        <div class="order-card__details" style="display: none;">
            <h4 class="details-title">📋 Order Details</h4>
            <div class="details-grid">
                <?php if ($order['customer_name']): ?>
                <p><strong>👤 Customer:</strong> <?php echo esc_html($order['customer_name']); ?></p>
                <?php endif; ?>
                <?php if ($order['phone']): ?>
                <p><strong>📞 Phone:</strong> <a href="tel:<?php echo esc_attr($order['phone']); ?>" class="phone-link"><?php echo esc_html($order['phone']); ?></a></p>
                <?php endif; ?>
                <?php if ($order['address']): ?>
                <p><strong>📍 Address:</strong> <?php echo esc_html($order['address']); ?></p>
                <?php endif; ?>
                <p><strong>🛏 Specifications:</strong> <?php echo esc_html($order['bedrooms']); ?> Bedroom(s), <?php echo esc_html($order['bathrooms']); ?> Bathroom(s), <?php echo number_format($order['sqft']); ?> sq ft</p>
                <?php if ($order['extras']): ?>
                <p><strong>✨ Extras:</strong> <?php echo esc_html($order['extras']); ?></p>
                <?php endif; ?>
                <?php if ($order['cleaning_date']): ?>
                <p><strong>📅 Scheduled:</strong> <?php echo esc_html($order['cleaning_date']); ?><?php if ($order['cleaning_time']): ?> at <?php echo esc_html($order['cleaning_time']); ?><?php endif; ?></p>
                <?php endif; ?>
                <p class="details-total"><strong>💵 Total:</strong> $<?php echo number_format($order['total'], 2); ?></p>
            </div>
        </div>
    </div>
    <?php
}
?>

<section class="cabinet-section">
    <div class="container">
        <div class="dashboard-header">
            <div class="user-welcome">
                <h1>My Orders</h1>
                <p>Manage your cleaning service orders</p>
            </div>
            
            <?php 
            // --- DRAFT BOOKING HANDLER ---
            // If user arrives with calculator params, show a "Complete Booking" card
            if (isset($_GET['service']) && isset($_GET['price'])): 
                $d_service = sanitize_text_field($_GET['service'] ?? '');
                $d_price   = sanitize_text_field($_GET['price'] ?? 0);
                $d_date    = sanitize_text_field($_GET['date'] ?? '');
                $d_time    = sanitize_text_field($_GET['time'] ?? '');
                $d_beds    = sanitize_text_field($_GET['beds'] ?? '');
                $d_baths   = sanitize_text_field($_GET['baths'] ?? '');
                $d_sqft    = sanitize_text_field($_GET['sqft'] ?? '');
                $d_extras  = sanitize_text_field($_GET['extras'] ?? '');
                
                // Get user defaults
                $u_phone   = get_user_meta($user_id, 'billing_phone', true);
                $u_address = get_user_meta($user_id, 'billing_address', true);
                $u_name    = $current_user->display_name;
                $u_email   = $current_user->user_email;
            ?>
            <div id="draft-booking-card" style="background:#fff; border:2px solid #3b82f6; border-radius:12px; padding:25px; margin-bottom:30px; box-shadow:0 4px 15px rgba(59,130,246,0.15);">
                <h3 style="margin-top:0; color:#1e40af; display:flex; align-items:center; gap:10px;">
                    <i class="fa-solid fa-clipboard-check"></i> Complete Your Booking
                </h3>
                <p style="margin-bottom:20px; color:#4b5563;">You're almost done! Please confirm your details below to place the order.</p>
                
                <form id="cabinet-booking-form" style="display:grid; gap:15px;">
                    <input type="hidden" name="action" value="cleaning_create_order">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('cleaning_auth_nonce'); ?>">
                    
                    <!-- Hidden Calc Data -->
                    <input type="hidden" name="service_type" value="<?php echo esc_attr($d_service); ?>">
                    <input type="hidden" name="total" value="<?php echo esc_attr($d_price); ?>">
                    <input type="hidden" name="bedrooms" value="<?php echo esc_attr($d_beds); ?>">
                    <input type="hidden" name="bathrooms" value="<?php echo esc_attr($d_baths); ?>">
                    <input type="hidden" name="sqft" value="<?php echo esc_attr($d_sqft); ?>">
                    <input type="hidden" name="extras" value="<?php echo esc_attr($d_extras); ?>">
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                        <div>
                            <label style="display:block; font-weight:600; margin-bottom:5px;">Date</label>
                            <input type="date" name="date" value="<?php echo esc_attr($d_date); ?>" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px;">
                        </div>
                        <div>
                            <label style="display:block; font-weight:600; margin-bottom:5px;">Time</label>
                            <select name="time" required style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px;">
                                <option value="08:00" <?php selected($d_time, '08:00'); ?>>08:00 AM</option>
                                <option value="10:00" <?php selected($d_time, '10:00'); ?>>10:00 AM</option>
                                <option value="12:00" <?php selected($d_time, '12:00'); ?>>12:00 PM</option>
                                <option value="14:00" <?php selected($d_time, '14:00'); ?>>02:00 PM</option>
                                <option value="16:00" <?php selected($d_time, '16:00'); ?>>04:00 PM</option>
                            </select>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                         <div>
                            <label style="display:block; font-weight:600; margin-bottom:5px;">Phone</label>
                            <input type="tel" name="phone" value="<?php echo esc_attr($u_phone); ?>" required placeholder="(555) 123-4567" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px;">
                        </div>
                        <div>
                            <label style="display:block; font-weight:600; margin-bottom:5px;">Address</label>
                            <input type="text" name="address" value="<?php echo esc_attr($u_address); ?>" required placeholder="123 Main St, Apt 4B" style="width:100%; padding:10px; border:1px solid #d1d5db; border-radius:6px;">
                        </div>
                    </div>
                    
                    <div style="background:#f3f4f6; padding:15px; border-radius:8px; margin-top:10px;">
                        <strong>Summary:</strong> <?php echo ucfirst($d_service); ?> Cleaning • <?php echo $d_beds; ?> Bed, <?php echo $d_baths; ?> Bath • $<?php echo $d_price; ?>
                    </div>

                    <div style="display:flex; gap:10px; margin-top:10px;">
                        <button type="submit" class="btn btn--primary" style="flex:1; padding:12px; background:#2563eb; color:white; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">
                            Confirm Booking ($<?php echo $d_price; ?>)
                        </button>
                        <a href="<?php echo home_url('/cabinet/'); ?>" class="btn" style="padding:12px 20px; background:#eff6ff; color:#1e40af; text-decoration:none; border-radius:6px; font-weight:600;">Cancel</a>
                    </div>
                    <div id="booking-msg" style="margin-top:10px;"></div>
                </form>
                
                <script>
                jQuery(document).ready(function($) {
                    $('#cabinet-booking-form').on('submit', function(e) {
                         e.preventDefault();
                         const $form = $(this);
                         const $btn = $form.find('button[type="submit"]');
                         const originalText = $btn.text();
                         
                         $btn.text('Processing...').prop('disabled', true);
                         $('#booking-msg').html('');
                         
                         $.post(cleaning_vars.ajaxUrl, $form.serialize(), function(res) {
                             if(res.success) {
                                  // Use the redirect URL provided by the server (likely /cabinet/?order_success=ID)
                                  window.location.href = res.data.redirect_url;
                             } else {
                                  $('#booking-msg').html('<div style="color:red; margin-top:5px;">Error: ' + (res.data.message || 'Unknown error') + '</div>');
                                  $btn.text(originalText).prop('disabled', false);
                             }
                         }).fail(function() {
                             alert('Server Error');
                             $btn.text(originalText).prop('disabled', false);
                         });
                    });
                });
                </script>
            </div>
            <?php endif; ?>

            <div class="dashboard-actions">
                <a href="<?php echo esc_url(home_url('/edit-profile')); ?>" class="action-btn">
                    <i class="fa-solid fa-user-pen"></i> Edit Profile
                </a>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="action-btn">
                    <i class="fa-solid fa-calendar-plus"></i> Book Service
                </a>
                <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="action-btn">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </div>
        </div>

        <!-- Payment Success Message -->
        <?php if (isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
        <div id="payment-success-banner" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 20px; border-radius: 6px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); animation: slideDown 0.5s ease-out;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <i class="fa-solid fa-check-circle" style="font-size: 2.5rem;"></i>
                <div>
                    <h3 style="margin: 0 0 5px 0; font-size: 1.3rem;">Payment Successful!</h3>
                    <p style="margin: 0; opacity: 0.95;">Your cleaning service is confirmed. We'll contact you shortly with details.</p>
                </div>
            </div>
        </div>
        <style>
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        </style>
        <?php endif; ?>

        <div class="dashboard-tabs">
            <button class="tab-link <?php echo !isset($_GET['payment']) ? 'active' : ''; ?>" data-tab="unpaid">
                <i class="fa-solid fa-credit-card"></i> Awaiting Payment
                <span class="tab-badge"><?php echo count($unpaid_orders); ?></span>
            </button>
            <button class="tab-link <?php echo isset($_GET['payment']) && $_GET['payment'] === 'success' ? 'active' : ''; ?>" data-tab="active">
                <i class="fa-solid fa-check-circle"></i> Active Orders
                <span class="tab-badge"><?php echo count($active_orders); ?></span>
            </button>
            <button class="tab-link" data-tab="history">
                <i class="fa-solid fa-history"></i> History
                <span class="tab-badge"><?php echo count($history_orders); ?></span>
            </button>
        </div>

        <!-- Unpaid Orders Tab -->
        <div id="tab-unpaid" class="tab-pane <?php echo !isset($_GET['payment']) ? 'active' : ''; ?>">
            <?php if (!empty($unpaid_orders)): ?>
                <?php foreach ($unpaid_orders as $order): ?>
                    <?php cabinet_render_order_card($order, 'unpaid'); ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa-solid fa-wallet"></i>
                    <h3>No pending payments</h3>
                    <p>All your orders are paid or completed</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Active/Paid Orders Tab -->
        <div id="tab-active" class="tab-pane <?php echo isset($_GET['payment']) && $_GET['payment'] === 'success' ? 'active' : ''; ?>">
            <?php if (!empty($active_orders)): ?>
                <?php foreach ($active_orders as $order): ?>
                    <?php cabinet_render_order_card($order, 'paid'); ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa-solid fa-calendar-check"></i>
                    <h3>No active orders</h3>
                    <p>Your upcoming scheduled cleanings will appear here</p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn--primary">
                        <i class="fa-solid fa-plus"></i> Book a Cleaning
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- History Tab -->
        <div id="tab-history" class="tab-pane">
            <?php if (!empty($history_orders)): ?>
                <?php foreach ($history_orders as $order): ?>
                    <?php cabinet_render_order_card($order, 'none'); ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa-solid fa-history"></i>
                    <h3>No order history yet</h3>
                    <p>Completed and cancelled orders will appear here</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* Modern UI Design for Cabinet */

/* Tab Badges */
.tab-badge {
    background: rgba(255,255,255,0.3);
    padding: 2px 8px;
    border-radius: 12px;
    margin-left: 8px;
    font-size: 0.85em;
}

/* Empty States */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
}
.empty-state i {
    font-size: 3rem;
    color: #d1d5db;
    margin-bottom: 15px;
    display: block;
}
.empty-state h3 {
    color: #1e293b;
    margin: 0 0 10px;
}
.empty-state p {
    color: #6b7280;
    margin: 0 0 20px;
}

/* Order Card Container */
.order-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s;
}
.order-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

/* Header with Price */
.order-card__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f3f4f6;
}

.order-date {
    font-size: 14px;
    color: #6b7280;
    margin-right: 10px;
}

.order-price {
    font-size: 24px;
    font-weight: 700;
    color: #111827;
}

/* Card Body */
.card-body {
    display: flex;
    gap: 16px;
    margin-bottom: 20px;
}

.service-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.service-icon i {
    color: white;
    font-size: 24px;
}

.service-details {
    flex: 1;
}

.service-details h3 {
    margin: 0 0 8px 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.service-info {
    margin: 0 0 8px 0;
    font-size: 14px;
    color: #6b7280;
}

.service-address {
    margin: 0;
    font-size: 14px;
    color: #6b7280;
}

.service-address i {
    color: #3b82f6;
    margin-right: 4px;
}

/* Footer Actions */
.order-card__footer {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
    margin-top: 20px;
    border-top: 1px solid #f3f4f6;
    padding-top: 20px;
}

.order-buttons-row {
    display: flex;
    gap: 12px;
}

/* Modern Pill Buttons */
.btn-custom {
    padding: 10px 24px;
    border-radius: 6px; /* Match homepage (was 50px) */
    font-weight: 600;
    font-size: 14px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-pay {
    background-color: #3b82f6;
    color: white;
}
.btn-pay:hover {
    background-color: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-cancel {
    background-color: #ef4444;
    color: white;
}
.btn-cancel:hover {
    background-color: #dc2626;
    opacity: 0.95;
}

/* Paid Status Badge */
.paid-status-badge {
    background: #f0fdf4;
    border: 1px solid #86efac;
    border-radius: 8px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #166534;
}

.paid-status-badge i {
    color: #16a34a;
    font-size: 16px;
}

/* View Details Link */
.js-toggle-details {
    color: #4b5563;
    font-size: 13px;
    background: none;
    border: none;
    cursor: pointer;
    text-decoration: underline;
    margin-top: 5px;
    padding: 4px 8px;
    transition: color 0.2s;
}
.js-toggle-details:hover {
    color: #111827;
}

/* Order Details Section */
.order-card__details {
    padding: 20px;
    border-top: 1px solid #e5e7eb;
    background: #f9fafb;
    border-radius: 0 0 8px 8px;
    margin-top: 16px;
}

.details-title {
    margin: 0 0 15px 0;
    font-size: 16px;
    color: #1f2937;
    font-weight: 600;
}

.details-grid {
    display: grid;
    gap: 10px;
}

.details-grid p {
    margin: 0;
    font-size: 14px;
    color: #374151;
}

.details-grid strong {
    color: #111827;
}

.phone-link {
    color: #3b82f6;
    text-decoration: none;
}
.phone-link:hover {
    text-decoration: underline;
}

.details-total {
    margin-top: 10px;
    font-size: 16px;
    font-weight: bold;
    color: #059669;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    // Tab switching
    tabLinks.forEach(link => {
        link.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            tabLinks.forEach(l => l.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));
            
            this.classList.add('active');
            const targetPane = document.getElementById('tab-' + targetTab);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });

    // Auto-hide success banner after 8 seconds
    const banner = document.getElementById('payment-success-banner');
    if (banner) {
        setTimeout(() => {
            banner.style.transition = 'opacity 0.5s ease-out';
            banner.style.opacity = '0';
            setTimeout(() => banner.remove(), 500);
        }, 8000);
    }

    // Order Details Toggle
    document.addEventListener('click', function(e) {
        if (e.target.closest('.js-toggle-details')) {
            const button = e.target.closest('.js-toggle-details');
            const orderCard = button.closest('.order-card');
            const detailsSection = orderCard.querySelector('.order-card__details');
            const icon = button.querySelector('i');
            
            if (detailsSection) {
                if (detailsSection.style.display === 'none' || !detailsSection.style.display) {
                    // Show details
                    detailsSection.style.display = 'block';
                    button.innerHTML = '<i class="fa-solid fa-chevron-up"></i> Hide Details';
                    
                    // Smooth scroll to details
                    setTimeout(() => {
                        detailsSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 100);
                } else {
                    // Hide details
                    detailsSection.style.display = 'none';
                    button.innerHTML = '<i class="fa-solid fa-chevron-down"></i> View Details';
                }
            }
        }
    });
});
</script>
