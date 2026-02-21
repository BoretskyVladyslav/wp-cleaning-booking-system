<?php

if (!defined('ABSPATH')) {

    exit;

}



/**

 * Register Service Settings Menu

 */

add_action('admin_menu', function () {

    add_menu_page(

        'Service Settings',

        'Service Settings',

        'manage_options',

        'service-settings',

        'cleaning_service_settings_page',

        'dashicons-store',

        30

    );

});



/**

 * Register ALL settings

 */

add_action('admin_init', function () {

    $fields = cleaning_get_all_option_keys(); 

    foreach ($fields as $key => $default) {

        register_setting('service_settings_group', $key, array(

            'default' => $default,

            'sanitize_callback' => ($key === 'contact_email') ? 'sanitize_email' :

                (in_array($key, ['social_facebook','social_instagram','social_tiktok']) ? 'esc_url_raw' :

                (in_array($key, ['contact_address']) ? 'sanitize_textarea_field' : 'sanitize_text_field')),

        ));

    }

});



/**

 * Matrix Configuration: Beds and Maximum Default Baths

 */

function cleaning_get_matrix_config() {

    return [

        1 => 2, // 1 Bed max 2 Baths visible

        2 => 2, // 2 Beds max 2 Baths visible

        3 => 3, // 3 Beds max 3 Baths visible

        4 => 4, // 4 Beds max 4 Baths visible

        5 => 5, // 5 Beds max 5 Baths visible

        6 => 5  // 6 Beds max 5 Baths visible

    ];

}



/**

 * ALL Option Keys and Default Values

 */

function cleaning_get_all_option_keys() {

    return array(

        // --- REGULAR MATRIX ---

        'matrix_regular_1bd_1ba' => 100, 'matrix_regular_1bd_2ba' => 120,

        'matrix_regular_2bd_1ba' => 125, 'matrix_regular_2bd_2ba' => 150,

        'matrix_regular_3bd_1ba' => 140, 'matrix_regular_3bd_2ba' => 160, 'matrix_regular_3bd_3ba' => 200,

        'matrix_regular_4bd_1ba' => 160, 'matrix_regular_4bd_2ba' => 200, 'matrix_regular_4bd_3ba' => 220, 'matrix_regular_4bd_4ba' => 240,

        'matrix_regular_5bd_1ba' => 180, 'matrix_regular_5bd_2ba' => 220, 'matrix_regular_5bd_3ba' => 240, 'matrix_regular_5bd_4ba' => 260, 'matrix_regular_5bd_5ba' => 300,

        'matrix_regular_6bd_1ba' => 220, 'matrix_regular_6bd_2ba' => 240, 'matrix_regular_6bd_3ba' => 260, 'matrix_regular_6bd_4ba' => 300, 'matrix_regular_6bd_5ba' => 320,



        // --- DEEP MATRIX ---

        'matrix_deep_1bd_1ba' => 180, 'matrix_deep_1bd_2ba' => 195,

        'matrix_deep_2bd_1ba' => 180, 'matrix_deep_2bd_2ba' => 200,

        'matrix_deep_3bd_1ba' => 180, 'matrix_deep_3bd_2ba' => 220, 'matrix_deep_3bd_3ba' => 320,

        'matrix_deep_4bd_1ba' => 200, 'matrix_deep_4bd_2ba' => 240, 'matrix_deep_4bd_3ba' => 300, 'matrix_deep_4bd_4ba' => 340,

        'matrix_deep_5bd_1ba' => 240, 'matrix_deep_5bd_2ba' => 300, 'matrix_deep_5bd_3ba' => 350, 'matrix_deep_5bd_4ba' => 400, 'matrix_deep_5bd_5ba' => 450,

        'matrix_deep_6bd_1ba' => 300, 'matrix_deep_6bd_2ba' => 350, 'matrix_deep_6bd_3ba' => 400, 'matrix_deep_6bd_4ba' => 450, 'matrix_deep_6bd_5ba' => 500,



        // --- MOVE IN/OUT MATRIX ---

        'matrix_move_in_1bd_1ba' => 150, 'matrix_move_in_1bd_2ba' => 175,

        'matrix_move_in_2bd_1ba' => 160, 'matrix_move_in_2bd_2ba' => 175,

        'matrix_move_in_3bd_1ba' => 160, 'matrix_move_in_3bd_2ba' => 220, 'matrix_move_in_3bd_3ba' => 260,

        'matrix_move_in_4bd_1ba' => 200, 'matrix_move_in_4bd_2ba' => 240, 'matrix_move_in_4bd_3ba' => 300, 'matrix_move_in_4bd_4ba' => 340,

        'matrix_move_in_5bd_1ba' => 240, 'matrix_move_in_5bd_2ba' => 300, 'matrix_move_in_5bd_3ba' => 350, 'matrix_move_in_5bd_4ba' => 400, 'matrix_move_in_5bd_5ba' => 450,

        'matrix_move_in_6bd_1ba' => 300, 'matrix_move_in_6bd_2ba' => 350, 'matrix_move_in_6bd_3ba' => 400, 'matrix_move_in_6bd_4ba' => 450, 'matrix_move_in_6bd_5ba' => 500,



        // --- POST CONSTRUCTION MATRIX ---

        'matrix_post_construction_1bd_1ba' => 200, 'matrix_post_construction_1bd_2ba' => 240,

        'matrix_post_construction_2bd_1ba' => 220, 'matrix_post_construction_2bd_2ba' => 260,

        'matrix_post_construction_3bd_1ba' => 260, 'matrix_post_construction_3bd_2ba' => 300, 'matrix_post_construction_3bd_3ba' => 340,

        'matrix_post_construction_4bd_1ba' => 310, 'matrix_post_construction_4bd_2ba' => 350, 'matrix_post_construction_4bd_3ba' => 390, 'matrix_post_construction_4bd_4ba' => 430,

        'matrix_post_construction_5bd_1ba' => 360, 'matrix_post_construction_5bd_2ba' => 400, 'matrix_post_construction_5bd_3ba' => 440, 'matrix_post_construction_5bd_4ba' => 480, 'matrix_post_construction_5bd_5ba' => 520,

        'matrix_post_construction_6bd_1ba' => 410, 'matrix_post_construction_6bd_2ba' => 450, 'matrix_post_construction_6bd_3ba' => 490, 'matrix_post_construction_6bd_4ba' => 530, 'matrix_post_construction_6bd_5ba' => 570,



        // --- BATHROOM FALLBACKS & EXTRA FEES ---

        'price_per_bath'              => 35, // Regular fallback

        'deep_extra_bath_fallback'    => 40,

        'move_in_extra_bath_fallback' => 40,

        'post_construction_bath_price' => 40, // Post-Con fallback



        'price_half_bath_reg'           => 10,

        'price_half_bath_deep'          => 20,

        'move_in_half_bath_price'       => 20,

        'post_construction_half_bath_price' => 25,

        

        // --- EXTRAS ---

        'cleaning_fridge_price'    => 40,

        'cleaning_oven_price'      => 40,

        'cleaning_cabinets_price'  => 50,

        'cleaning_windows_price'   => 5,

        'cleaning_laundry_price'   => 25,



        // --- DISCOUNTS ---

        'cleaning_discount_weekly'    => 20,

        'cleaning_discount_biweekly'  => 15,

        'cleaning_discount_monthly'   => 10,

        'frequency_3_weeks_multiplier' => 5,



        // --- SQFT LOGIC ---

        'price_sqft_threshold'   => 1000,

        'price_sqft_step'        => 500,

        'price_sqft_increment'   => 10,



        // --- CONTACT ---

        'contact_phone'          => '+1 (224) 491-9701',

        'contact_email'          => 'info@cleaning.com',

        'contact_address'        => 'Chicago, IL',

        'social_facebook'        => '',

        'social_instagram'       => '',

        'social_tiktok'          => '',

        'booking_page_url'       => '/checkout/',

        

        'stripe_secret_key'      => '',

        'stripe_publishable_key' => '',

    );

}



/**

 * Render the Settings Page HTML

 */

function cleaning_service_settings_page() {

    if (!current_user_can('manage_options')) {

        return;

    }



    $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'pricing';

    $tabs = array(

        'pricing'      => '💰 Calculator Pricing',

        'contacts'     => '📋 Global Content & Links',

        'integrations' => '🔑 Integrations',

        'portfolio'    => '🖼️ Portfolio',

    );

    ?>

    <div class="wrap">

        <h1>🏪 Service Settings</h1>



        <style>

            .ss-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px 20px; margin-bottom: 20px; }

            .ss-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px 20px; margin-bottom: 20px; }

            .ss-field { display: flex; flex-direction: column; }

            .ss-field label { font-weight: 600; margin-bottom: 4px; font-size: 13px; color: #1d2327; }

            .ss-field input, .ss-field textarea { padding: 8px 10px; border: 1px solid #8c8f94; border-radius: 4px; }

            .ss-field input[type="number"] { width: 100%; }

            .ss-section-title { font-size: 15px; font-weight: 700; margin: 24px 0 10px; padding-bottom: 6px; border-bottom: 2px solid #2271b1; color: #1d2327; }

            .ss-section-title:first-child { margin-top: 8px; }

            .ss-info { background: #f0f6fc; border-left: 4px solid #2271b1; padding: 10px 14px; margin-bottom: 16px; font-size: 13px; color: #1d2327; }

            .ss-warning { background: #fef8ee; border-left: 4px solid #dba617; padding: 10px 14px; margin-bottom: 16px; font-size: 13px; }



            .ss-accordion { border: 1px solid #c3c4c7; border-radius: 6px; margin-bottom: 12px; background: #fff; overflow: hidden; }

            .ss-accordion-header { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; cursor: pointer; background: #f6f7f7; border-bottom: 1px solid transparent; transition: background 0.2s; user-select: none; }

            .ss-accordion-header:hover { background: #eef0f4; }

            .ss-accordion.open .ss-accordion-header { background: #fff; border-bottom-color: #e0e0e0; }

            .ss-accordion-title { font-size: 15px; font-weight: 700; color: #1d2327; display: flex; align-items: center; gap: 8px; }

            .ss-accordion-title .emoji { font-size: 18px; }

            .ss-accordion-arrow { font-size: 18px; color: #787c82; transition: transform 0.25s ease; }

            .ss-accordion.open .ss-accordion-arrow { transform: rotate(180deg); }

            .ss-accordion-body { display: none; padding: 18px; }

            .ss-accordion.open .ss-accordion-body { display: block; }



            .matrix-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }

            .matrix-table th, .matrix-table td { border: 1px solid #ddd; padding: 8px; text-align: center; }

            .matrix-table th { background-color: #f0f0f1; font-weight: 600; }

            .matrix-input { width: 100px !important; }

        </style>



        <h2 class="nav-tab-wrapper">

            <?php foreach ($tabs as $slug => $label) : ?>

                <a href="?page=service-settings&tab=<?php echo $slug; ?>"

                   class="nav-tab <?php echo ($active_tab === $slug) ? 'nav-tab-active' : ''; ?>">

                    <?php echo esc_html($label); ?>

                </a>

            <?php endforeach; ?>

        </h2>



        <form method="post" action="options.php">

            <?php settings_fields('service_settings_group'); ?>



            <?php if ($active_tab === 'pricing') : ?>

                <?php cleaning_render_tab_pricing(); ?>

            <?php elseif ($active_tab === 'contacts') : ?>

                <?php cleaning_render_tab_contacts(); ?>

            <?php elseif ($active_tab === 'integrations') : ?>

                <?php cleaning_render_tab_integrations(); ?>

            <?php elseif ($active_tab === 'portfolio') : ?>

                <?php cleaning_render_tab_portfolio(); ?>

            <?php endif; ?>



            <?php if ($active_tab !== 'portfolio') : ?>
                <?php submit_button('Save Settings'); ?>
            <?php endif; ?>

            <?php
            /**
             * CRITICAL: Preserve values for ALL tabs on every save.
             *
             * Each tab only renders its own <input> fields. When the admin clicks
             * "Save Settings" from (e.g.) the Pricing tab, the browser sends empty
             * strings for stripe_secret_key / stripe_publishable_key — because those
             * fields are not in the DOM — overwriting the saved Stripe keys with "".
             *
             * Fix: inject a hidden <input> for every registered option that is NOT
             * currently rendered by the active tab, carrying the current DB value.
             */
            $all_keys         = cleaning_get_all_option_keys();
            $integration_keys = ['stripe_publishable_key', 'stripe_secret_key'];
            $contact_keys     = [
                'contact_phone', 'contact_email', 'contact_address',
                'booking_page_url', 'social_facebook', 'social_instagram', 'social_tiktok',
            ];
            $pricing_keys     = array_diff(array_keys($all_keys), $integration_keys, $contact_keys);

            if ($active_tab === 'pricing') {
                $rendered_keys = $pricing_keys;
            } elseif ($active_tab === 'contacts') {
                $rendered_keys = $contact_keys;
            } elseif ($active_tab === 'integrations') {
                $rendered_keys = $integration_keys;
            } else {
                $rendered_keys = [];
            }

            foreach (array_keys($all_keys) as $option_key) {
                if (!in_array($option_key, $rendered_keys, true)) {
                    $existing = get_option($option_key, $all_keys[$option_key]);
                    echo '<input type="hidden" name="' . esc_attr($option_key) . '" value="' . esc_attr($existing) . '">' . "\n";
                }
            }
            ?>


        </form>

    </div>



    <script>

    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.ss-accordion-header').forEach(function(header) {

            header.addEventListener('click', function() {

                this.closest('.ss-accordion').classList.toggle('open');

            });

        });

    });

    </script>

    <?php

}



function cleaning_render_number_field($key, $label) {

    // Force default logic

    $all_defaults = cleaning_get_all_option_keys();

    $default_val = isset($all_defaults[$key]) ? $all_defaults[$key] : 0;

    

    $val = get_option($key);

    // Explicitly check for false or empty string

    if ($val === false || $val === '') {

        $val = $default_val;

    }

    ?>

    <div class="ss-field">

        <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>

        <input type="number" step="1" min="0"

               id="<?php echo esc_attr($key); ?>"

               name="<?php echo esc_attr($key); ?>"

               value="<?php echo esc_attr($val); ?>" />

    </div>

    <?php

}



function cleaning_render_text_field($key, $label, $type = 'text', $placeholder = '') {

    $all_defaults = cleaning_get_all_option_keys();

    $default_val = isset($all_defaults[$key]) ? $all_defaults[$key] : '';

    

    $val = get_option($key);

    if ($val === false || $val === '') {

        $val = $default_val;

    }

    ?>

    <div class="ss-field">

        <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>

        <?php if ($type === 'textarea') : ?>

            <textarea id="<?php echo esc_attr($key); ?>"

                      name="<?php echo esc_attr($key); ?>"

                      rows="3"

                      placeholder="<?php echo esc_attr($placeholder); ?>"><?php echo esc_textarea($val); ?></textarea>

        <?php else : ?>

            <input type="<?php echo esc_attr($type); ?>"

                   id="<?php echo esc_attr($key); ?>"

                   name="<?php echo esc_attr($key); ?>"

                   value="<?php echo esc_attr($val); ?>"

                   placeholder="<?php echo esc_attr($placeholder); ?>"

                   class="regular-text" />

        <?php endif; ?>

    </div>

    <?php

}



function cleaning_render_matrix_table($service, $title, $emoji) {

    $config = cleaning_get_matrix_config();

    $all_defaults = cleaning_get_all_option_keys();



    // Map frontend config services to DB slugs if needed, but here we use direct mapping

    // 'regular' -> 'matrix_regular_...'

    // 'deep' -> 'matrix_deep_...'

    // 'move_in' -> 'matrix_move_in_...'

    // 'post_construction' -> 'matrix_post_construction_...'

    

    ?>

    <div class="ss-accordion <?php echo ($service === 'regular') ? 'open' : ''; ?>">

        <div class="ss-accordion-header">

            <span class="ss-accordion-title"><span class="emoji"><?php echo $emoji; ?></span> <?php echo $title; ?> Matrix</span>

            <span class="ss-accordion-arrow">▼</span>

        </div>

        <div class="ss-accordion-body">

            <table class="matrix-table">

                <thead>

                    <tr>

                        <th>Bedrooms</th>

                        <?php 

                        $max_cols = 5; 

                        for($i=1; $i<=$max_cols; $i++) echo "<th>{$i} Bath</th>"; 

                        ?>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($config as $beds => $max_baths) : ?>

                    <tr>

                        <td><strong><?php echo $beds; ?> Bedroom</strong></td>

                        <?php for ($baths = 1; $baths <= 5; $baths++) : ?>

                            <td>

                                <?php if ($baths <= $max_baths) : 

                                    $key = "matrix_{$service}_{$beds}bd_{$baths}ba";

                                    $default_val = isset($all_defaults[$key]) ? $all_defaults[$key] : 0;

                                    

                                    $val = get_option($key);

                                    // FORCE DEFAULT IF EMPTY

                                    if ($val === false || $val === '') {

                                        $val = $default_val;

                                    }

                                ?>

                                    <input type="number" name="<?php echo $key; ?>" value="<?php echo esc_attr($val); ?>" class="matrix-input" min="0">

                                <?php else : ?>

                                    <span style="color: #ccc;">—</span>

                                <?php endif; ?>

                            </td>

                        <?php endfor; ?>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

            <p class="description">

                If user selects more bathrooms than defined, the calculator takes the <strong>MAX defined price</strong> plus the 

                <strong>Extra Bath Fee</strong> for each additional bathroom.

            </p>

        </div>

    </div>

    <?php

}



function cleaning_render_tab_pricing() {

    ?>

    <div class="ss-info" style="margin-top: 16px;">

        <strong>PRICING MATRIX MODE:</strong> Prices are defined by the exact combination of Bedrooms and Bathrooms.

    </div>



    <!-- Render Regular Matrix -->

    <?php cleaning_render_matrix_table('regular', 'Regular Cleaning', '🏠'); ?>



    <!-- Render Deep Matrix -->

    <?php cleaning_render_matrix_table('deep', 'Deep Cleaning', '✨'); ?>



    <!-- Render Move In/Out Matrix -->

    <?php cleaning_render_matrix_table('move_in', 'Move In / Move Out', '📦'); ?>



    <!-- Render Post-Construction Matrix -->

    <?php cleaning_render_matrix_table('post_construction', 'Post-Construction', '🔨'); ?>



    <div class="ss-accordion">

        <div class="ss-accordion-header">

            <span class="ss-accordion-title"><span class="emoji">➕</span> Add-ons & Bath Overflows</span>

            <span class="ss-accordion-arrow">▼</span>

        </div>

        <div class="ss-accordion-body">

            <div class="ss-section-title" style="margin-top: 0;">Bathroom Fallbacks & Half Baths</div>

            <div class="ss-grid">

                <?php

                cleaning_render_number_field('price_per_bath', 'Regular Extra Full Bath ($)');

                cleaning_render_number_field('deep_extra_bath_fallback', 'Deep Extra Full Bath ($)');

                cleaning_render_number_field('move_in_extra_bath_fallback', 'Move In Extra Full Bath ($)');

                cleaning_render_number_field('post_construction_bath_price', 'Post-Const Extra Full Bath ($)');

                

                cleaning_render_number_field('price_half_bath_reg', 'Half Bath — Regular ($)');

                cleaning_render_number_field('price_half_bath_deep', 'Half Bath — Deep ($)');

                cleaning_render_number_field('move_in_half_bath_price', 'Half Bath — Move In ($)');

                cleaning_render_number_field('post_construction_half_bath_price', 'Half Bath — Post-Const ($)');

                ?>

            </div>



            <div class="ss-section-title">Kitchen & Home Extras</div>

            <div class="ss-grid-4">

                <?php

                cleaning_render_number_field('cleaning_fridge_price', 'Fridge ($)');

                cleaning_render_number_field('cleaning_oven_price', 'Oven ($)');

                cleaning_render_number_field('cleaning_laundry_price', 'Laundry ($)');

                cleaning_render_number_field('cleaning_windows_price', 'Windows ($)');

                cleaning_render_number_field('cleaning_cabinets_price', 'Cabinets ($)');

                ?>

            </div>



            <div class="ss-section-title">Square Footage Rules</div>

            <div class="ss-grid">

                <?php

                cleaning_render_number_field('price_sqft_threshold', 'Threshold (Base SqFt)');

                cleaning_render_number_field('price_sqft_step', 'Step (Per X SqFt)');

                cleaning_render_number_field('price_sqft_increment', 'Price Per Step ($)');

                ?>

            </div>



            <div class="ss-section-title">Frequency Discounts</div>

            <div class="ss-warning">

                <strong>⚠️ Note:</strong> Discounts apply ONLY to Regular and Deep Cleaning.

            </div>

            <div class="ss-grid">

                <?php

                cleaning_render_number_field('cleaning_discount_weekly', 'Weekly Discount (%)');

                cleaning_render_number_field('cleaning_discount_biweekly', 'Bi-Weekly Discount (%)');

                cleaning_render_number_field('cleaning_discount_monthly', 'Monthly Discount (%)');

                cleaning_render_number_field('frequency_3_weeks_multiplier', 'Every 3 weeks Discount (%)');

                ?>

            </div>

        </div>

    </div>

    <?php

}



function cleaning_render_tab_contacts() {

    ?>

    <div class="ss-section-title">Contact Information</div>

    <div class="ss-grid">

        <?php

        cleaning_render_text_field('contact_phone', 'Phone Number', 'text', '+1 (224) 491-9701');

        cleaning_render_text_field('contact_email', 'Email Address', 'email', 'info@cleaning.com');

        cleaning_render_text_field('contact_address', 'Physical Address', 'textarea', 'Chicago, IL');

        ?>

    </div>



    <div class="ss-section-title">Site Links</div>

    <div class="ss-grid">

        <?php

        cleaning_render_text_field('booking_page_url', 'Booking Page URL', 'text', '/checkout/');

        ?>

    </div>



    <div class="ss-section-title">Social Media Links</div>

    <div class="ss-grid">

        <?php

        cleaning_render_text_field('social_facebook', 'Facebook URL', 'url', 'https://facebook.com/yourpage');

        cleaning_render_text_field('social_instagram', 'Instagram URL', 'url', 'https://instagram.com/username');

        cleaning_render_text_field('social_tiktok', 'TikTok URL', 'url', 'https://tiktok.com/@username');

        ?>

    </div>

    <?php

}



function cleaning_render_tab_integrations() {

    ?>

    <div class="ss-section-title">Stripe</div>

    <div class="ss-field" style="max-width: 500px; margin-bottom: 20px;">

        <label for="stripe_publishable_key">Publishable Key</label>

        <input type="text"

               id="stripe_publishable_key"

               name="stripe_publishable_key"

               value="<?php echo esc_attr(get_option('stripe_publishable_key', '')); ?>"

               placeholder="pk_test_..."

               class="regular-text" />

    </div>



    <div class="ss-field" style="max-width: 500px;">

        <label for="stripe_secret_key">Secret Key</label>

        <input type="text"

               id="stripe_secret_key"

               name="stripe_secret_key"

               value="<?php echo esc_attr(get_option('stripe_secret_key', '')); ?>"

               placeholder="sk_test_..."

               class="regular-text" />

    </div>

    <?php

}



function cleaning_render_tab_portfolio() {

    if (function_exists('get_field') && function_exists('the_repeater_field')) {

        echo '<p>ACF Repeater is available — fields will render here.</p>';

    }

}

