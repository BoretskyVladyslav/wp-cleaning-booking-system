<div id="modal-login" class="modal-overlay auth-modal">
    <div class="modal-box">
        <button class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-header">
            <h3><i class="fa-solid fa-right-to-bracket"></i> Sign In</h3>
            <p>Welcome back! Please enter your details.</p>
        </div>
        <div class="modal-body">
            <form id="login-form" class="auth-form">
                <div class="form-group">
                    <label for="login-email">Email Address</label>
                    <input type="email" id="login-email" name="email" class="form-input" placeholder="your@email.com" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" class="form-input" placeholder="••••••••" required autocomplete="current-password">
                </div>
                <div class="form-row form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="<?php echo wp_lostpassword_url(); ?>" class="forgot-link">Forgot password?</a>
                </div>
                <button type="submit" class="btn btn--primary btn--block">Sign In</button>
            </form>
            <div class="auth-switch">
                <p>Don't have an account? <a href="#" data-switch-modal="register">Create one</a></p>
            </div>
        </div>
    </div>
</div>
<!-- REGISTER MODAL -->
<div id="modal-register" class="modal-overlay auth-modal">
    <div class="modal-box modal-box--wide">
        <button class="modal-close"><i class="fa-solid fa-xmark"></i></button>
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-plus"></i> Create Account</h3>
            <p>Join us to track your orders and get exclusive deals!</p>
        </div>
        <div class="modal-body">
            <form id="register-form" class="auth-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="reg-firstname">First Name *</label>
                        <input type="text" id="reg-firstname" name="first_name" class="form-input" placeholder="John" required autocomplete="given-name">
                    </div>
                    <div class="form-group">
                        <label for="reg-lastname">Last Name</label>
                        <input type="text" id="reg-lastname" name="last_name" class="form-input" placeholder="Doe" autocomplete="family-name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="reg-email">Email Address *</label>
                    <input type="email" id="reg-email" name="email" class="form-input" placeholder="your@email.com" required autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="reg-phone">Phone Number *</label>
                    <input type="tel" id="reg-phone" name="phone" class="form-input" placeholder="+1 (555) 123-4567" required autocomplete="tel">
                </div>
                <div class="form-group">
                    <label for="reg-password">Password *</label>
                    <input type="password" id="reg-password" name="password" class="form-input" placeholder="Minimum 6 characters" required minlength="6" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="reg-address">Default Service Address</label>
                    <input type="text" id="reg-address" name="address" class="form-input" placeholder="123 Main St, Chicago, IL" autocomplete="street-address">
                </div>
                <button type="submit" class="btn btn--primary btn--block">Create Account</button>
            </form>
            <div class="auth-switch">
                <p>Already have an account? <a href="#" data-switch-modal="login">Sign in</a></p>
            </div>
        </div>
    </div>
</div>
<!-- Order Summary Modal -->
<div id="order-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2>Ваше Замовлення</h2>
        <div id="modal-order-details"></div>
        <div class="modal-footer">
            <button class="btn btn--secondary close-btn">Скасувати</button>
            <button class="btn btn--primary confirm-btn">Підтвердити Замовлення</button>
        </div>
    </div>
</div>
<!-- Success Modal -->
<div id="order-success-modal" class="modal">
    <div class="modal-content text-center" style="max-width: 400px; padding: 50px 30px;">
        <span class="close-modal">&times;</span>
        <div class="success-icon" style="font-size: 60px; color: #28a745; margin-bottom: 20px;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h2>Дякуємо!</h2>
        <p id="success-message" style="margin-bottom: 30px; color: #555; font-size: 16px;">
            Ваше замовлення успішно прийнято.<br>
            Ми зв'яжемося з вами найближчим часом.
        </p>
        <button class="btn btn--primary close-btn btn--block" style="margin-top: 0;">Гаразд</button>
    </div>
</div>

<?php get_template_part('template-parts/footer', 'content'); ?>

<!-- Cache-resistant nonce injection for payment processing -->
<input type="hidden" id="cleaning_global_nonce" value="<?php echo wp_create_nonce('cleaning_payment_nonce'); ?>">

<!-- [NUCLEAR OPTION REMOVED] - Using standard app.js handler with dynamic keys -->


<?php wp_footer(); ?>
</body>
</html>