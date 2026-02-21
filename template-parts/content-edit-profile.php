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
$first_name = get_user_meta($user_id, 'first_name', true);
$last_name = get_user_meta($user_id, 'last_name', true);
$phone = get_user_meta($user_id, 'billing_phone', true);
$address = get_user_meta($user_id, 'billing_address', true);
$email = $current_user->user_email;
?>

<section class="cabinet-section">
    <div class="container">
        <!-- Header matching cabinet page -->
        <div class="dashboard-header">
            <div class="user-welcome">
                <h1><i class="fa-solid fa-user-pen"></i> Edit Profile</h1>
                <p>Update your personal information</p>
            </div>
            <div class="dashboard-actions">
                <a href="<?php echo esc_url(home_url('/cabinet/')); ?>" class="action-btn">
                    <i class="fa-solid fa-arrow-left"></i> Back to Cabinet
                </a>
            </div>
        </div>

        <!-- Message Display Area -->
        <div id="profile-message" class="profile-message" style="display: none;"></div>

        <!-- Profile Form Card -->
        <div class="profile-form-wrapper">
            <form id="profile-form" class="profile-form">
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('cleaning_auth_nonce'); ?>">
                
                <!-- Personal Information Section -->
                <div class="profile-section">
                    <h3 class="section-title"><i class="fa-solid fa-user"></i> Personal Information</h3>
                    
                    <!-- Name Fields Row -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="<?php echo esc_attr($first_name); ?>" 
                                   class="form-input" 
                                   required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="<?php echo esc_attr($last_name); ?>" 
                                   class="form-input">
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?php echo esc_attr($email); ?>" 
                               class="form-input" 
                               disabled>
                        <small class="field-note">Email cannot be changed. Contact support if needed.</small>
                    </div>

                    <!-- Phone Field -->
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               value="<?php echo esc_attr($phone); ?>" 
                               class="form-input" 
                               placeholder="+1 (555) 123-4567">
                    </div>

                    <!-- Address Field -->
                    <div class="form-group">
                        <label for="address">Default Address</label>
                        <textarea id="address" 
                                  name="address" 
                                  class="form-input form-textarea" 
                                  rows="3" 
                                  placeholder="123 Main St, City, State, ZIP"><?php echo esc_textarea($address); ?></textarea>
                    </div>
                </div>

                <!-- Security Section -->
                <div class="profile-section">
                    <h3 class="section-title"><i class="fa-solid fa-lock"></i> Change Password</h3>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               class="form-input" 
                               placeholder="Leave blank to keep current password" 
                               minlength="8" 
                               autocomplete="new-password">
                        <small class="field-note">Minimum 8 characters</small>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" class="btn btn--primary btn-submit">
                        <i class="fa-solid fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>