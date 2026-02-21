<?php
if (is_user_logged_in()) {
    $user = wp_get_current_user();
    if (in_array('administrator', $user->roles)) {
        wp_redirect(admin_url());
    } else {
        wp_redirect(home_url('/cabinet/')); 
    }
    exit;
}
$redirect_to = isset($_GET['redirect_to']) ? esc_url($_GET['redirect_to']) : home_url('/cabinet/');
get_header();
?>

<section class="login-page">
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fa-solid fa-broom"></i> Welcome Back</h1>
            <p>Sign in to manage your orders</p>
        </div>
        <div class="login-body">
            <div class="login-tabs">
                <button class="login-tab active" data-tab="login">Sign In</button>
                <button class="login-tab" data-tab="register">Create Account</button>
            </div>
            <form id="login-form" class="login-form active">
                <div id="login-message" class="login-message"></div>
                <div class="form-group">
                    <label for="login-email">Email Address</label>
                    <input type="email" id="login-email" name="email" class="form-input" placeholder="you@example.com" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" class="form-input" placeholder="••••••••" required>
                </div>
                <div class="remember-row">
                    <label>
                        <input type="checkbox" id="login-remember" name="remember" checked>
                        Remember me
                    </label>
                    <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="forgot-link">Forgot password?</a>
                </div>
                <input type="hidden" id="login-redirect" value="<?php echo esc_attr($redirect_to); ?>">
                <button type="submit" class="btn-login" id="login-submit">
                    <i class="fa-solid fa-sign-in-alt"></i> Sign In
                </button>
            </form>
            <form id="register-form" class="register-form">
                <div id="register-message" class="login-message"></div>
                <div class="form-group">
                    <label for="reg-first-name">First Name</label>
                    <input type="text" id="reg-first-name" name="first_name" class="form-input" placeholder="John" required>
                </div>
                <div class="form-group">
                    <label for="reg-last-name">Last Name</label>
                    <input type="text" id="reg-last-name" name="last_name" class="form-input" placeholder="Doe">
                </div>
                <div class="form-group">
                    <label for="reg-email">Email Address</label>
                    <input type="email" id="reg-email" name="email" class="form-input" placeholder="you@example.com" required>
                </div>
                <div class="form-group">
                    <label for="reg-phone">Phone Number</label>
                    <input type="tel" id="reg-phone" name="phone" class="form-input" placeholder="+1 (555) 123-4567" required>
                </div>
                <div class="form-group">
                    <label for="reg-password">Password</label>
                    <input type="password" id="reg-password" name="password" class="form-input" placeholder="Min. 6 characters" required minlength="6">
                </div>
                <button type="submit" class="btn-login" id="register-submit">
                    <i class="fa-solid fa-user-plus"></i> Create Account
                </button>
            </form>
        </div>
        <div class="login-footer">
            <a href="<?php echo esc_url(home_url('/')); ?>">
                <i class="fa-solid fa-arrow-left"></i> Back to Homepage
            </a>
        </div>
    </div>
</section>

<?php get_footer(); ?> 