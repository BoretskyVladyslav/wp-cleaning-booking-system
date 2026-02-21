<?php
?>
<section class="page-hero">
    <div class="container">
        <h1>Зв'яжіться з Нами</h1>
        <p>Ми завжди раді відповісти на ваші запитання та прийняти замовлення.</p>
    </div>
</section>
<section class="contact-section">
    <div class="container">
        <div class="contact-layout">
            <!-- Left Column: Info Block -->
                <?php
                // Safe fetch
                $address = function_exists('get_field') ? get_field('contact_address', 'option') : '';
                $phone = function_exists('get_field') ? get_field('contact_phone', 'option') : '';
                $email = function_exists('get_field') ? get_field('contact_email', 'option') : '';
                $insta = function_exists('get_field') ? get_field('social_instagram', 'option') : '';
                $tg = function_exists('get_field') ? get_field('social_telegram', 'option') : '';
                ?>
                <div class="info-item">
                    <h3><i class="fa-solid fa-location-dot"></i> Адреса</h3>
                    <p><?php echo esc_html($address ?: 'м. Київ, вул. Хрещатик, 1'); ?></p>
                </div>
                <div class="info-item">
                    <h3><i class="fa-solid fa-phone"></i> Телефон</h3>
                    <p><?php echo esc_html($phone ?: '+38 (097) 111-22-33'); ?></p>
                </div>
                <div class="info-item">
                    <h3><i class="fa-solid fa-envelope"></i> Email</h3>
                    <p><?php echo esc_html($email ?: 'info@cleaning.ua'); ?></p>
                </div>
                <div class="info-item">
                    <h3><i class="fa-solid fa-clock"></i> Графік Роботи</h3>
                    <p>Пн-Нд: 08:00 - 20:00</p>
                    <p>Без вихідних</p>
                </div>
                <div class="social-links">
                    <?php if($insta): ?>
                        <a href="<?php echo esc_url($insta); ?>" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                    <?php endif; ?>
                    <?php if($tg): ?>
                        <a href="<?php echo esc_url($tg); ?>" target="_blank"><i class="fa-brands fa-telegram"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Right Column: Contact Form -->
            <div class="contact-form-card">
                <h2>Напишіть Нам</h2>
                <form id="contact-form">
                    <div class="form-group">
                        <label for="contact-name">Ім'я</label>
                        <input type="text" id="contact-name" name="contact-name" placeholder="Ваше ім'я" required>
                    </div>
                    <div class="form-group">
                        <label for="contact-email">Email</label>
                        <input type="email" id="contact-email" name="contact-email" placeholder="vas@example.com" required>
                    </div>
                    <div class="form-group">
                        <label for="contact-message">Повідомлення</label>
                        <textarea id="contact-message" name="contact-message" placeholder="Текст вашого повідомлення..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn--primary" style="width: 100%;">Надіслати</button>
                </form>
            </div>
        </div>
    </div>
</section>
<div class="map-placeholder-full">
    <div class="map-overlay-text">Google Map Placeholder</div>
</div>