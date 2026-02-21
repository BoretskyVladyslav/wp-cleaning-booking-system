<?php
if (!defined('ABSPATH')) exit;

add_action('cleaning_send_booking_notification', 'cleaning_dispatch_booking_emails', 10, 1);

function cleaning_dispatch_booking_emails(int $order_id): void {
    $order = get_post($order_id);
    if (!$order || $order->post_type !== 'cleaning_order') {
        error_log("[CleaningEmail] Order #{$order_id} not found or wrong type. Aborting.");
        return;
    }

    $data = [
        'order_id'     => $order_id,
        'customer'     => get_post_meta($order_id, 'customer_name',   true),
        'email'        => get_post_meta($order_id, 'customer_email',  true),
        'phone'        => get_post_meta($order_id, 'customer_phone',  true),
        'address'      => get_post_meta($order_id, 'service_address', true),
        'service_type' => get_post_meta($order_id, 'service_type',    true),
        'bedrooms'     => get_post_meta($order_id, 'bedrooms',        true),
        'bathrooms'    => get_post_meta($order_id, 'bathrooms',       true),
        'sqft'         => get_post_meta($order_id, 'sqft',            true),
        'frequency'    => get_post_meta($order_id, 'frequency',       true),
        'extras'       => get_post_meta($order_id, 'extras',          true),
        'total'        => get_post_meta($order_id, 'total_price',     true),
        'date'         => get_post_meta($order_id, 'service_date',    true),
        'time'         => get_post_meta($order_id, 'service_time',    true),
        'created_at'   => get_post_field('post_date', $order_id),
    ];

    $admin_email = get_option('admin_email');

    $admin_sent = cleaning_send_email(
        $admin_email,
        "New Booking #{$order_id} — {$data['customer']}",
        cleaning_build_admin_email($data)
    );

    if (!$admin_sent) {
        error_log("[CleaningEmail] FAILED: Admin email for order #{$order_id} to {$admin_email}.");
    } else {
        error_log("[CleaningEmail] OK: Admin email for order #{$order_id} sent to {$admin_email}.");
    }

    if (!empty($data['email']) && is_email($data['email'])) {
        $customer_sent = cleaning_send_email(
            $data['email'],
            "Your Cleaning Booking Confirmation — Order #{$order_id}",
            cleaning_build_customer_email($data)
        );

        if (!$customer_sent) {
            error_log("[CleaningEmail] FAILED: Customer email for order #{$order_id} to {$data['email']}.");
        } else {
            error_log("[CleaningEmail] OK: Customer email for order #{$order_id} sent to {$data['email']}.");
        }
    } else {
        error_log("[CleaningEmail] SKIP: Invalid or empty customer email for order #{$order_id}.");
    }
}

function cleaning_send_email(string $to, string $subject, string $html_body): bool {
    $site_name  = get_bloginfo('name');
    $from_email = apply_filters('cleaning_from_email', 'noreply@' . wp_parse_url(home_url(), PHP_URL_HOST));

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        "From: {$site_name} <{$from_email}>",
        "Reply-To: {$from_email}",
        'X-Mailer: WordPress/' . get_bloginfo('version'),
    ];

    $mail_error    = '';
    $error_handler = function($wp_error) use (&$mail_error) {
        $mail_error = $wp_error->get_error_message();
    };
    add_action('wp_mail_failed', $error_handler);

    $result = wp_mail($to, $subject, $html_body, $headers);

    remove_action('wp_mail_failed', $error_handler);

    if (!$result && $mail_error) {
        error_log("[CleaningEmail] wp_mail error: {$mail_error}");
    }

    return $result;
}

function cleaning_email_service_label(string $type): string {
    return [
        'reg'  => 'Regular Cleaning',
        'deep' => 'Deep Cleaning',
        'move' => 'Move In / Move Out',
        'post' => 'Post-Construction',
    ][$type] ?? ucfirst($type);
}

function cleaning_parse_extras_html(string $extras_raw): string {
    if (empty(trim($extras_raw))) {
        return '<em style="color:#888">None</em>';
    }

    $label_map = [
        'fridge'   => 'Fridge',
        'oven'     => 'Oven',
        'cabinets' => 'Cabinets',
        'windows'  => 'Windows',
        'laundry'  => 'Laundry',
    ];

    $rows = [];
    foreach (explode(',', $extras_raw) as $item) {
        $parts = explode(':', trim($item));
        if (count($parts) === 2) {
            $key    = sanitize_key($parts[0]);
            $qty    = intval($parts[1]);
            $label  = $label_map[$key] ?? ucfirst($key);
            $rows[] = "<li style='margin-bottom:4px'>{$label} &times; {$qty}</li>";
        }
    }

    return '<ul style="margin:0;padding-left:18px">' . implode('', $rows) . '</ul>';
}

function cleaning_email_wrap(string $inner_html, string $title): string {
    $site_name = esc_html(get_bloginfo('name'));
    $site_url  = esc_url(home_url());
    $year      = date('Y');

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{$title}</title>
</head>
<body style="margin:0;padding:0;background:#f4f7fb;font-family:'Segoe UI',Arial,sans-serif;color:#1a202c">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fb;padding:32px 0">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%">
        <tr>
          <td style="background:#1e3a8a;border-radius:12px 12px 0 0;padding:28px 36px;text-align:center">
            <a href="{$site_url}" style="text-decoration:none">
              <span style="font-size:22px;font-weight:700;color:#ffffff;letter-spacing:0.5px">{$site_name}</span>
            </a>
          </td>
        </tr>
        <tr>
          <td style="background:#ffffff;padding:36px 36px 28px;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0">
            {$inner_html}
          </td>
        </tr>
        <tr>
          <td style="background:#f0f4ff;border-radius:0 0 12px 12px;border:1px solid #e2e8f0;border-top:none;padding:20px 36px;text-align:center">
            <p style="margin:0;font-size:12px;color:#64748b">
              &copy; {$year} {$site_name} &mdash; <a href="{$site_url}" style="color:#1e3a8a;text-decoration:none">{$site_url}</a>
            </p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}

function cleaning_build_admin_email(array $d): string {
    $service     = esc_html(cleaning_email_service_label($d['service_type']));
    $customer    = esc_html($d['customer']);
    $email       = esc_html($d['email']);
    $phone       = esc_html($d['phone']);
    $address     = esc_html($d['address']);
    $bedrooms    = esc_html($d['bedrooms']);
    $bathrooms   = esc_html($d['bathrooms']);
    $sqft        = esc_html($d['sqft']);
    $frequency   = esc_html($d['frequency']);
    $total       = esc_html($d['total']);
    $date        = esc_html($d['date']);
    $time        = esc_html($d['time']);
    $order_id    = intval($d['order_id']);
    $extras_html = cleaning_parse_extras_html($d['extras']);
    $admin_url   = esc_url(admin_url("post.php?post={$order_id}&action=edit"));
    $created     = esc_html($d['created_at']);

    $inner = <<<HTML
<h2 style="margin:0 0 6px;font-size:20px;color:#1e3a8a">&#128276; New Booking Received</h2>
<p style="margin:0 0 24px;color:#64748b;font-size:14px">Submitted on {$created}</p>
<div style="background:#eff6ff;border-left:4px solid #1e3a8a;border-radius:6px;padding:14px 18px;margin-bottom:28px">
  <strong style="color:#1e3a8a">Order #{$order_id}</strong> &mdash; awaiting confirmation &amp; payment.
  <a href="{$admin_url}" style="display:inline-block;margin-top:10px;background:#1e3a8a;color:#fff;padding:8px 18px;border-radius:6px;text-decoration:none;font-size:13px;font-weight:600">View in Admin Panel &rarr;</a>
</div>
<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px">
  <tr><td colspan="2" style="padding:8px 0 4px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;border-bottom:2px solid #e2e8f0">Customer</td></tr>
  <tr><td style="padding:10px 0;color:#64748b;width:38%">Full Name</td><td style="padding:10px 0;color:#1a202c;font-weight:600">{$customer}</td></tr>
  <tr><td style="padding:10px 0;color:#64748b">Email</td><td style="padding:10px 0"><a href="mailto:{$email}" style="color:#1e3a8a">{$email}</a></td></tr>
  <tr><td style="padding:10px 0;color:#64748b">Phone</td><td style="padding:10px 0;color:#1a202c">{$phone}</td></tr>
  <tr><td style="padding:10px 0;color:#64748b">Address</td><td style="padding:10px 0;color:#1a202c">{$address}</td></tr>
  <tr><td colspan="2" style="padding:16px 0 4px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;border-bottom:2px solid #e2e8f0">Service Details</td></tr>
  <tr><td style="padding:10px 0;color:#64748b">Service Type</td><td style="padding:10px 0;color:#1a202c;font-weight:600">{$service}</td></tr>
  <tr><td style="padding:10px 0;color:#64748b">Date &amp; Time</td><td style="padding:10px 0;color:#1a202c">{$date} at {$time}</td></tr>
  <tr><td style="padding:10px 0;color:#64748b">Bedrooms</td><td style="padding:10px 0;color:#1a202c">{$bedrooms}</td></tr>
  <tr><td style="padding:10px 0;color:#64748b">Bathrooms</td><td style="padding:10px 0;color:#1a202c">{$bathrooms}</td></tr>
  <tr><td style="padding:10px 0;color:#64748b">Square Feet</td><td style="padding:10px 0;color:#1a202c">{$sqft}</td></tr>
  <tr><td style="padding:10px 0;color:#64748b">Frequency</td><td style="padding:10px 0;color:#1a202c">{$frequency}</td></tr>
  <tr><td style="padding:10px 0;color:#64748b;vertical-align:top">Add-Ons</td><td style="padding:10px 0;color:#1a202c">{$extras_html}</td></tr>
  <tr><td colspan="2" style="padding:16px 0 4px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;border-bottom:2px solid #e2e8f0">Pricing</td></tr>
  <tr>
    <td style="padding:14px 0;color:#1a202c;font-size:18px;font-weight:700">Total Estimate</td>
    <td style="padding:14px 0;color:#1e3a8a;font-size:22px;font-weight:800">\${$total}</td>
  </tr>
</table>
HTML;

    return cleaning_email_wrap($inner, "New Booking #{$order_id}");
}

function cleaning_build_customer_email(array $d): string {
    $service     = esc_html(cleaning_email_service_label($d['service_type']));
    $customer    = esc_html($d['customer']);
    $address     = esc_html($d['address']);
    $bedrooms    = esc_html($d['bedrooms']);
    $bathrooms   = esc_html($d['bathrooms']);
    $frequency   = esc_html($d['frequency']);
    $total       = esc_html($d['total']);
    $date        = esc_html($d['date']);
    $time        = esc_html($d['time']);
    $order_id    = intval($d['order_id']);
    $extras_html = cleaning_parse_extras_html($d['extras']);
    $site_name   = esc_html(get_bloginfo('name'));
    $cabinet_url = esc_url(home_url('/cabinet/'));
    $first_name  = esc_html(explode(' ', $customer)[0]);

    $inner = <<<HTML
<h2 style="margin:0 0 8px;font-size:22px;color:#1e3a8a">&#10003; Booking Confirmed!</h2>
<p style="margin:0 0 24px;color:#475569;font-size:15px;line-height:1.6">
  Hi <strong>{$first_name}</strong>, thank you for booking with <strong>{$site_name}</strong>!
  Your appointment is confirmed and our team will be ready for you. Below is your summary.
</p>
<div style="background:#f0f6ff;border:1px solid #c7d9f5;border-radius:10px;padding:20px 24px;margin-bottom:28px">
  <p style="margin:0 0 4px;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:0.7px;font-weight:700">Booking Reference</p>
  <p style="margin:0;font-size:20px;font-weight:800;color:#1e3a8a">#ORDER{$order_id}</p>
</div>
<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px">
  <tr><td colspan="2" style="padding:8px 0 4px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;border-bottom:2px solid #e2e8f0">Appointment</td></tr>
  <tr><td style="padding:10px 0;color:#64748b;width:38%">Service</td><td style="padding:10px 0;color:#1a202c;font-weight:600">{$service}</td></tr>
  <tr><td style="padding:10px 0;color:#64748b">Date</td><td style="padding:10px 0;color:#1a202c">{$date}</td></tr>
  <tr><td style="padding:10px 0;color:#64748b">Time</td><td style="padding:10px 0;color:#1a202c">{$time}</td></tr>
  <tr><td style="padding:10px 0;color:#64748b">Address</td><td style="padding:10px 0;color:#1a202c">{$address}</td></tr>
  <tr><td style="padding:10px 0;color:#64748b">Frequency</td><td style="padding:10px 0;color:#1a202c">{$frequency}</td></tr>
  <tr><td colspan="2" style="padding:16px 0 4px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;border-bottom:2px solid #e2e8f0">Property</td></tr>
  <tr><td style="padding:10px 0;color:#64748b">Bedrooms</td><td style="padding:10px 0;color:#1a202c">{$bedrooms}</td></tr>
  <tr><td style="padding:10px 0;color:#64748b">Bathrooms</td><td style="padding:10px 0;color:#1a202c">{$bathrooms}</td></tr>
  <tr><td style="padding:10px 0;color:#64748b;vertical-align:top">Add-Ons</td><td style="padding:10px 0;color:#1a202c">{$extras_html}</td></tr>
  <tr><td colspan="2" style="padding:16px 0 4px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;border-bottom:2px solid #e2e8f0">Total</td></tr>
  <tr>
    <td style="padding:14px 0;color:#1a202c;font-size:17px;font-weight:700">Estimated Price</td>
    <td style="padding:14px 0;color:#1e3a8a;font-size:22px;font-weight:800">\${$total}</td>
  </tr>
</table>
<div style="text-align:center;margin-top:32px">
  <a href="{$cabinet_url}" style="display:inline-block;background:#1e3a8a;color:#ffffff;padding:14px 32px;border-radius:8px;text-decoration:none;font-size:15px;font-weight:700;letter-spacing:0.3px">
    View My Bookings &rarr;
  </a>
</div>
<p style="margin:28px 0 0;font-size:13px;color:#94a3b8;text-align:center;line-height:1.6">
  Questions? Reply to this email or visit your <a href="{$cabinet_url}" style="color:#1e3a8a">customer portal</a>.
</p>
HTML;

    return cleaning_email_wrap($inner, "Booking Confirmation #ORDER{$order_id}");
}