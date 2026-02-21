<?php
if (!defined('ABSPATH')) {
    exit;
}
function cleaning_send_email($to, $subject, $message, $options = []) {
    // HARDCODED for GoDaddy cache bypass
    $api_key = 're_VgdJ34Ff_KDha37PVGqnaUJBX53kZuSQu';
    if (empty($api_key) || $api_key === 're_PLACEHOLDER') {
        error_log('Resend API Key not configured. Email not sent to: ' . $to);
        return wp_mail($to, $subject, $message);
    }
    $html_content = cleaning_build_email_html($subject, $message);
    $from_name = isset($options['from_name']) ? $options['from_name'] : 'Olala Cleaning';
    $from_email = isset($options['from_email']) ? $options['from_email'] : 'booking@olalacleaning.com';
    $reply_to = isset($options['reply_to']) ? $options['reply_to'] : 'alex.strilets@gmail.com';
    $api_url = 'https://api.resend.com/emails';
    $email_data = [
        'from'     => $from_name . ' <' . $from_email . '>',
        'to'       => [$to],
        'subject'  => $subject,
        'html'     => $html_content,
        'reply_to' => $reply_to,
    ];
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($email_data),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    if ($curl_error) {
        error_log('Resend cURL Error: ' . $curl_error);
        return new WP_Error('curl_error', $curl_error);
    }
    $result = json_decode($response, true);
    if ($http_code !== 200) {
        $error_message = isset($result['message']) ? $result['message'] : 'Email sending failed.';
        error_log('Resend API Error: ' . print_r($result, true));
        return new WP_Error('resend_error', $error_message);
    }
    error_log('Email sent successfully via Resend to: ' . $to . ' | ID: ' . ($result['id'] ?? 'unknown'));
    return true;
}
function cleaning_build_email_html($subject, $content) {
    $logo_url = get_template_directory_uri() . '/assets/images/logo.png';
    $site_url = home_url();
    $year = date('Y');
    if (strpos($content, '<') === false) {
        $content = nl2br(esc_html($content));
    }
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$subject}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <div style="display: none; max-height: 0; overflow: hidden;">
        {$subject}
    </div>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width: 600px; width: 100%;">
                    <tr>
                        <td align="center" style="background: linear-gradient(135deg, #3b82f6, #2563eb); padding: 30px 40px; border-radius: 16px 16px 0 0;">
                            <a href="{$site_url}" style="text-decoration: none;">
                                <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700;">
                                    ✨ OlaLa Cleaning
                                </h1>
                            </a>
                            <p style="margin: 8px 0 0; color: rgba(255,255,255,0.85); font-size: 14px;">
                                Professional Cleaning Services
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #ffffff; padding: 40px; border-left: 1px solid #e5e7eb; border-right: 1px solid #e5e7eb;">
                            <div style="color: #374151; font-size: 16px; line-height: 1.6;">
                                {$content}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px 40px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 16px 16px;">
                            <p style="margin: 0 0 10px; color: #6b7280; font-size: 14px; text-align: center;">
                                Need help? Reply to this email or call us at <strong>(312) 555-0123</strong>
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 12px; text-align: center;">
                                © {$year} OlaLa Cleaning. All rights reserved.<br>
                                Chicago, IL & Suburbs
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    return $html;
}
function cleaning_send_order_confirmation($order_id) {
    $order = get_post($order_id);
    if (!$order) return false;
    $user = get_user_by('ID', $order->post_author);
    if (!$user) return false;
    $total = get_post_meta($order_id, 'order_total', true);
    $service_type = get_post_meta($order_id, 'service_type', true);
    $cleaning_date = get_post_meta($order_id, 'cleaning_date', true);
    $cleaning_time = get_post_meta($order_id, 'cleaning_time', true);
    $service_labels = [
        'reg'  => 'Regular Cleaning',
        'deep' => 'Deep Cleaning',
        'move' => 'Move In/Out Cleaning',
        'post' => 'Post Construction Cleaning',
    ];
    $service_label = $service_labels[$service_type] ?? ucfirst($service_type);
    $subject = 'Order Confirmed - OlaLa Cleaning #' . $order_id;
    $message = <<<MSG
<h2 style="color: #1e293b; margin-top: 0;">Thank you for your order!</h2>
<p>Hi <strong>{$user->display_name}</strong>,</p>
<p>Your cleaning service has been confirmed. Here are the details:</p>
<table role="presentation" width="100%" style="margin: 20px 0; border-collapse: collapse;">
    <tr>
        <td style="padding: 12px; background: #f8fafc; border-bottom: 1px solid #e5e7eb;"><strong>Order 
        <td style="padding: 12px; background: #f8fafc; border-bottom: 1px solid #e5e7eb;">{$order_id}</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;"><strong>Service</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">{$service_label}</td>
    </tr>
    <tr>
        <td style="padding: 12px; background: #f8fafc; border-bottom: 1px solid #e5e7eb;"><strong>Date</strong></td>
        <td style="padding: 12px; background: #f8fafc; border-bottom: 1px solid #e5e7eb;">{$cleaning_date}</td>
    </tr>
    <tr>
        <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;"><strong>Time</strong></td>
        <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">{$cleaning_time}</td>
    </tr>
    <tr>
        <td style="padding: 12px; background: #f8fafc;"><strong>Total</strong></td>
        <td style="padding: 12px; background: #f8fafc; color: #16a34a; font-weight: bold;">$" . number_format($total, 2) . "</td>
    </tr>
</table>
<p>We'll be there on time! If you need to reschedule or have any questions, please don't hesitate to contact us.</p>
<p style="margin-top: 30px;">
    <a href="{site_url}/cabinet/" style="display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: 600;">View Your Order</a>
</p>
MSG;
    $message = str_replace('{site_url}', home_url(), $message);
    return cleaning_send_email($user->user_email, $subject, $message);
}
function cleaning_send_test_email() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Unauthorized.']);
        return;
    }
    $current_user = wp_get_current_user();
    $to = $current_user->user_email;
    $subject = 'Test Email from OlaLa Cleaning';
    $message = <<<MSG
<h2 style="color: #1e293b; margin-top: 0;">🎉 Test Email Successful!</h2>
<p>Hi <strong>{$current_user->display_name}</strong>,</p>
<p>This is a test email from your OlaLa Cleaning website. If you're seeing this, your email system is working correctly!</p>
<p><strong>Sent at:</strong> " . current_time('F j, Y \a\t g:i A') . "</p>
<p style="padding: 20px; background: #dcfce7; border-radius: 8px; color: #166534;">
    ✅ Email configuration is working properly.
</p>
MSG;
    $result = cleaning_send_email($to, $subject, $message);
    if (is_wp_error($result)) {
        wp_send_json_error(['message' => $result->get_error_message()]);
    } else {
        wp_send_json_success(['message' => 'Test email sent to ' . $to]);
    }
}
add_action('wp_ajax_cleaning_send_test_email', 'cleaning_send_test_email');