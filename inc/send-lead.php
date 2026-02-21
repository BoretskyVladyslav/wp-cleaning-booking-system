<?php
function cleaning_handle_lead() {
    // Allow 'cleaning_auth_nonce' (frontend) OR 'cleaning_lead_nonce' (legacy)
    $nonce = $_POST['nonce'] ?? '';
    if (!wp_verify_nonce($nonce, 'cleaning_auth_nonce') && !wp_verify_nonce($nonce, 'cleaning_lead_nonce')) {
        wp_send_json_error(['message' => 'Security check failed (Invalid Nonce)']);
        return;
    }

    // Map Frontend Fields (app.js) to Backend Variables
    $firstname = sanitize_text_field($_POST['firstname'] ?? '');
    $lastname = sanitize_text_field($_POST['lastname'] ?? '');
    
    // Fallback if 'name' is sent directly
    if (empty($firstname) && isset($_POST['name'])) {
        $name = sanitize_text_field($_POST['name']);
    } else {
        $name = trim("$firstname $lastname");
    }

    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    
    // 'service_type' from JS -> 'service'
    $service = sanitize_text_field($_POST['service_type'] ?? $_POST['service'] ?? '');
    
    // 'total_price' from JS -> 'total'
    $total = sanitize_text_field($_POST['total_price'] ?? $_POST['total'] ?? '');
    
    $date = sanitize_text_field($_POST['date'] ?? '');
    $time = sanitize_text_field($_POST['time'] ?? '');
    $address = sanitize_text_field($_POST['address'] ?? '');
    $details = isset($_POST['details']) ? $_POST['details'] : [];
    if (empty($phone) || empty($email)) {
        wp_send_json_error(['message' => 'Телефон та Email обов\'язкові']);
    }
    if (empty($date) || empty($time)) {
        wp_send_json_error(['message' => 'Будь ласка, оберіть дату та час прибирання']);
    }
    $admin_email = get_option('admin_email');
    if (function_exists('get_field')) {
        $acf_email = get_field('calc_lead_email', 'option');
        if ($acf_email) {
            $admin_email = $acf_email;
        }
    }
    $subject_admin = "Нова заявка: $name ($service)";
    $message_admin = '<html><body>';
    $message_admin .= '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">';
    $message_admin .= '<div style="background-color: #007bff; color: #ffffff; padding: 20px; text-align: center;">';
    $message_admin .= '<h2 style="margin:0;">Нове Замовлення</h2>';
    $message_admin .= '</div>';
    $message_admin .= '<div style="padding: 20px; background-color: #f9f9f9;">';
    $message_admin .= '<p><strong>Ім\'я:</strong> ' . esc_html($name) . '</p>';
    $message_admin .= '<p><strong>Телефон:</strong> <a href="tel:' . esc_attr($phone) . '">' . esc_html($phone) . '</a></p>';
    $message_admin .= '<p><strong>Email:</strong> <a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></p>';
    if (!empty($address)) {
        $message_admin .= '<p><strong>Адреса:</strong> ' . esc_html($address) . '</p>';
    }
    $message_admin .= '<hr style="border:0; border-top:1px dashed #ccc; margin:15px 0;">';
    $message_admin .= '<p><strong>Дата:</strong> ' . esc_html($date) . '</p>';
    $message_admin .= '<p><strong>Час:</strong> ' . esc_html($time) . '</p>';
    $message_admin .= '<hr style="border:0; border-top:1px dashed #ccc; margin:15px 0;">';
    if (!empty($service)) {
        $message_admin .= '<p><strong>Послуга:</strong> ' . esc_html($service) . '</p>';
    }
    if (!empty($total) && $total !== '0 ₴') {
        $message_admin .= '<p><strong>Сума:</strong> <span style="font-size: 1.2em; font-weight: bold; color: #28a745;">' . esc_html($total) . '</span></p>';
    }
    if (!empty($details) && is_array($details)) {
        $message_admin .= '<div style="background: #fff; padding: 15px; border-radius: 5px; border: 1px solid #eee;">';
        $message_admin .= '<h3 style="margin-top: 0; font-size: 16px;">Деталі розрахунку:</h3>';
        $message_admin .= '<ul style="padding-left: 20px; margin-bottom: 0;">';
        foreach ($details as $line) {
            $message_admin .= '<li>' . esc_html($line) . '</li>';
        }
        $message_admin .= '</ul></div>';
    }
    $message_admin .= '</div>';
    $message_admin .= '<div style="background-color: #eeeeee; padding: 10px; text-align: center; font-size: 12px; color: #666;">';
    $message_admin .= 'Надіслано з сайту CleaningPro';
    $message_admin .= '</div></div></body></html>';
    $message_client = '<html><body>';
    $message_client .= '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">';
    $message_client .= '<div style="background-color: #28a745; color: #ffffff; padding: 20px; text-align: center;">';
    $message_client .= '<h2 style="margin:0;">Ваше замовлення прийнято!</h2>';
    $message_client .= '</div>';
    $message_client .= '<div style="padding: 20px; background-color: #ffffff;">';
    $message_client .= '<p>Вітаємо, ' . esc_html($name) . '!</p>';
    $message_client .= '<p>Ми отримали ваше замовлення. Наш менеджер скоро зв\'яжеться з вами для підтвердження.</p>';
    $message_client .= '<div style="background-color: #f0f8ff; padding: 15px; border-radius: 6px; margin: 15px 0;">';
    $message_client .= '<p style="margin: 5px 0;"><strong>Дата прибирання:</strong> ' . esc_html($date) . '</p>';
    $message_client .= '<p style="margin: 5px 0;"><strong>Час:</strong> ' . esc_html($time) . '</p>';
    if (!empty($address)) {
        $message_client .= '<p style="margin: 5px 0;"><strong>Адреса:</strong> ' . esc_html($address) . '</p>';
    }
    $message_client .= '</div>';
    $message_client .= '<div style="background-color: #f8f9fa; border: 1px dashed #ced4da; padding: 15px; border-radius: 6px; margin-top: 20px;">';
    $message_client .= '<h3 style="margin-top: 0; border-bottom: 1px solid #ddd; padding-bottom: 10px;">Ваш чек:</h3>';
    if (!empty($details) && is_array($details)) {
        $message_client .= '<ul style="list-style: none; padding: 0; margin: 0;">';
        foreach ($details as $line) {
            $style = 'padding: 5px 0; border-bottom: 1px dashed #eee; display: flex; justify-content: space-between;';
            if (strpos($line, 'Знижка') !== false || strpos($line, 'Промокод') !== false) {
                 $style .= ' color: #28a745; font-weight: bold;';
            }
            if (strpos($line, 'Стан житла') !== false) {
                 $style .= ' color: #d9534f;';
            }
            $message_client .= '<li style="' . $style . '">' . esc_html($line) . '</li>';
        }
        $message_client .= '</ul>';
    }
    if (!empty($total)) {
         $message_client .= '<div style="margin-top: 15px; text-align: right; font-size: 18px; font-weight: bold; color: #333;">Разом: ' . esc_html($total) . '</div>';
    }
    $message_client .= '</div>'; 
    $message_client .= '<p style="margin-top: 20px; font-size: 13px; color: #777;">Якщо у вас виникли питання, зателефонуйте нам.</p>';
    $message_client .= '</div></div></body></html>';
    // --- EMAIL HEADERS (SMTP SECURITY FIX) ---
    // 1. FROM: Must be the Site Admin (Authenticated Domain)
    // 2. REPLY-TO: The Client's Email (So you can hit reply)
    $headers = array();
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: Cleaning Service <' . $admin_email . '>';
    $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';

    // --- DEBUG LOGGING ---
    error_log("📧 ATTEMPTING EMAIL SEND:");
    error_log("To: $admin_email");
    error_log("From: $admin_email");
    error_log("Reply-To: $email");

    // --- SAVE LEAD TO DB (CPT) ---
    $lead_title = $name . ' - ' . current_time('Y-m-d H:i');
    $lead_content = $message_admin; // Perform full HTML save
    
    $lead_id = wp_insert_post(array(
        'post_type'    => 'cleaning_lead',
        'post_title'   => $lead_title,
        'post_content' => $lead_content,
        'post_status'  => 'publish',
    ));
    
    if ($lead_id && !is_wp_error($lead_id)) {
        update_post_meta($lead_id, '_lead_phone', $phone);
        update_post_meta($lead_id, '_lead_email', $email);
        update_post_meta($lead_id, '_lead_service', $service);
        update_post_meta($lead_id, '_lead_total', $total);
    }

    // --- SEND EMAIL ---
    $sent_admin = wp_mail($admin_email, $subject_admin, $message_admin, $headers);

    // Client Confirmation (Auto-reply)
    if (is_email($email)) {
        $subject_client = "Ваше замовлення прийнято! - CleaningService";
        // Client headers: From Admin, Reply-To Admin
        $client_headers = array();
        $client_headers[] = 'Content-Type: text/html; charset=UTF-8';
        $client_headers[] = 'From: Cleaning Service <' . $admin_email . '>';
        
        wp_mail($email, $subject_client, $message_client, $client_headers);
    }

    if ($sent_admin) {
        wp_send_json_success(['message' => 'Дякуємо! Ваша заявка прийнята.']);
    } else {
        // --- FALLBACK LOGGING (SAFETY NET) ---
        error_log("❌ MAIL FAILED. LOGGING LEAD TO FILE/DB.");
        $log_entry = date('Y-m-d H:i:s') . " | $name | $phone | $email | $service | $total\n";
        // Append to specific log file if possible, or just error_log
        error_log("LEAD DATA: " . print_r($_POST, true));
        
        // Return generic error but tell user to call
        wp_send_json_error(['message' => 'Системна помилка відправки. Ми зберегли вашу заявку, але краще зателефонуйте нам для підтвердження.']);
    }
}
add_action('wp_ajax_send_lead', 'cleaning_handle_lead');
add_action('wp_ajax_nopriv_send_lead', 'cleaning_handle_lead');