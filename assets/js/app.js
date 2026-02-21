/**
 * File: assets/js/app.js
 * Complete Cleaning Calculator Logic
 */

jQuery(document).ready(function ($) {

    // Configuration
    const config = {
        extras: {
            cleaning_fridge_price: parseFloat(cleaning_vars.cleaning_fridge_price) || 0,
            cleaning_oven_price: parseFloat(cleaning_vars.cleaning_oven_price) || 0,
            cleaning_cabinets_price: parseFloat(cleaning_vars.cleaning_cabinets_price) || 0,
            cleaning_windows_price: parseFloat(cleaning_vars.cleaning_windows_price) || 0,
            cleaning_laundry_price: parseFloat(cleaning_vars.cleaning_laundry_price) || 0
        },
        discounts: {
            weekly: parseFloat(cleaning_vars.cleaning_discount_weekly) || 0,
            biweekly: parseFloat(cleaning_vars.cleaning_discount_biweekly) || 0,
            monthly: parseFloat(cleaning_vars.cleaning_discount_monthly) || 0,
            triweekly: parseFloat(cleaning_vars.frequency_3_weeks_multiplier) || 0
        },
        sqft: {
            threshold: parseFloat(cleaning_vars.price_sqft_threshold) || 1000,
            step: parseFloat(cleaning_vars.price_sqft_step) || 500,
            increment: parseFloat(cleaning_vars.price_sqft_increment) || 0
        },
        baths: {
            extra: Number(cleaning_vars.price_per_bath) || 0,
            deep_fallback: Number(cleaning_vars.deep_extra_bath_fallback) || 0,
            move_in_fallback: Number(cleaning_vars.move_in_extra_bath_fallback) || 0,
            post_const: Number(cleaning_vars.post_construction_bath_price) || 0,

            half_reg: Number(cleaning_vars.price_half_bath_reg) || 0,
            half_deep: Number(cleaning_vars.price_half_bath_deep) || 0,
            move_in_half: Number(cleaning_vars.move_in_half_bath_price) || 0,
            post_const_half: Number(cleaning_vars.post_construction_half_bath_price) || 0
        },
        ajaxUrl: cleaning_vars.ajaxUrl,
        nonce: cleaning_vars.nonce,
        isLoggedIn: cleaning_vars.isLoggedIn,
        loginUrl: cleaning_vars.loginUrl,
        bookingUrl: cleaning_vars.bookingUrl
    };

    const PRICING_MATRIX = {
        "regular": {
            "1": { "1": parseFloat(cleaning_vars.price_1bd_reg) || 0 },
            "2": { "1": parseFloat(cleaning_vars.price_2bd_reg) || 0 },
            "3": { "1": parseFloat(cleaning_vars.price_3bd_reg) || 0 },
            "4": { "1": parseFloat(cleaning_vars.price_4bd_reg) || 0 },
            "5": { "1": parseFloat(cleaning_vars.price_5bd_reg) || 0 },
            "6": { "1": parseFloat(cleaning_vars.price_6bd_reg) || 0 }
        },
        "deep": {
            "1": { "1": parseFloat(cleaning_vars.price_1bd_deep) || 0 },
            "2": { "1": parseFloat(cleaning_vars.price_2bd_deep) || 0 },
            "3": { "1": parseFloat(cleaning_vars.price_3bd_deep) || 0 },
            "4": { "1": parseFloat(cleaning_vars.price_4bd_deep) || 0 },
            "5": { "1": parseFloat(cleaning_vars.price_5bd_deep) || 0 },
            "6": { "1": parseFloat(cleaning_vars.price_6bd_deep) || 0 }
        },
        "movein": {
            "1": { "1": parseFloat(cleaning_vars.price_1bd_move) || 0 },
            "2": { "1": parseFloat(cleaning_vars.price_2bd_move) || 0 },
            "3": { "1": parseFloat(cleaning_vars.price_3bd_move) || 0 },
            "4": { "1": parseFloat(cleaning_vars.price_4bd_move) || 0 },
            "5": { "1": parseFloat(cleaning_vars.price_5bd_move) || 0 },
            "6": { "1": parseFloat(cleaning_vars.price_6bd_move) || 0 }
        },
        "post": {
            "1": { "1": parseFloat(cleaning_vars.price_1bd_post) || 0 },
            "2": { "1": parseFloat(cleaning_vars.price_2bd_post) || 0 },
            "3": { "1": parseFloat(cleaning_vars.price_3bd_post) || 0 },
            "4": { "1": parseFloat(cleaning_vars.price_4bd_post) || 0 },
            "5": { "1": parseFloat(cleaning_vars.price_5bd_post) || 0 },
            "6": { "1": parseFloat(cleaning_vars.price_6bd_post) || 0 }
        }
    };

    // Replace strict matrix with dynamic data we passed
    if (cleaning_vars.matrix) {
        PRICING_MATRIX.regular = cleaning_vars.matrix.regular;
        PRICING_MATRIX.deep = cleaning_vars.matrix.deep;
        PRICING_MATRIX.movein = cleaning_vars.matrix.move_in;
        PRICING_MATRIX.post = cleaning_vars.matrix.post_construction;
    }

    function updateVisualPrices() {
        $('input[data-price-key="cleaning_fridge_price"]').closest('.extra-item').find('.extra-price').text('$' + (config.extras.cleaning_fridge_price) + ' each');
        $('input[data-price-key="cleaning_oven_price"]').closest('.extra-item').find('.extra-price').text('$' + (config.extras.cleaning_oven_price) + ' each');
        $('input[data-price-key="cleaning_cabinets_price"]').closest('.extra-item').find('.extra-price').text('$' + (config.extras.cleaning_cabinets_price) + ' each');
        $('input[data-price-key="cleaning_windows_price"]').closest('.extra-item').find('.extra-price').text('$' + (config.extras.cleaning_windows_price) + ' each');
        $('input[data-price-key="cleaning_laundry_price"]').closest('.extra-item').find('.extra-price').text('$' + (config.extras.cleaning_laundry_price) + ' each');

        const weeklyPct = config.discounts.weekly;
        const biweeklyPct = config.discounts.biweekly;
        const monthlyPct = config.discounts.monthly;

        $('#frequency option').each(function () {
            const txt = $(this).text().toLowerCase();
            if (txt.includes('weekly') && !txt.includes('bi')) {
                $(this).text(`Weekly (${weeklyPct}% off)`);
            } else if (txt.includes('bi-weekly') || txt.includes('biweekly')) {
                $(this).text(`Bi-weekly (${biweeklyPct}% off)`);
            } else if (txt.includes('monthly')) {
                $(this).text(`Monthly (${monthlyPct}% off)`);
            }
        });
    }

    function getMatrixPrice(serviceType, bedrooms, bathrooms) {
        let type = 'regular';
        if (serviceType === 'reg') type = 'regular';
        else if (serviceType === 'deep') type = 'deep';
        else if (serviceType === 'move') type = 'movein';
        else if (serviceType === 'post') type = 'post';

        if (!PRICING_MATRIX[type]) type = 'regular';

        const bedroomsInt = parseInt(bedrooms) || 0;
        const bathroomsFloat = parseFloat(bathrooms) || 0;

        // Use available keys to cap bedrooms if needed, though matrix usually covers 1-6
        // Let's assume matrix has keys 1-6.
        const safeBeds = Math.max(1, bedroomsInt);
        const bedroomData = PRICING_MATRIX[type][safeBeds] || PRICING_MATRIX[type]["6"];
        // If bedroom count > 6, fallback to 6? Or linear add-on? For now, fallback to max defined (6).

        // Determine Max defined baths for this bedroom row
        // bedroomData is { "1": price, "2": price ... }
        const definedBaths = Object.keys(bedroomData).map(k => parseInt(k));
        const maxDefinedBath = Math.max(...definedBaths);

        let baseCost = 0;
        const fullBaths = Math.floor(bathroomsFloat);
        const safeFullBaths = Math.max(1, fullBaths);

        if (fullBaths <= maxDefinedBath) {
            // Direct Lookup
            baseCost = parseFloat(bedroomData[safeFullBaths]) || 0;
        } else {
            // Fallback: Max Defined Price + Extra Bath Fees
            const maxPrice = parseFloat(bedroomData[maxDefinedBath]) || 0;
            const excessBaths = fullBaths - maxDefinedBath;

            let fallbackRate = config.baths.extra; // Default regular
            if (type === 'deep') fallbackRate = config.baths.deep_fallback;
            else if (type === 'movein') fallbackRate = config.baths.move_in_fallback;
            else if (type === 'post') fallbackRate = config.baths.post_const;

            baseCost = maxPrice + (excessBaths * fallbackRate);
        }

        // Add Half Bath
        let halfBathCost = 0;
        if (bathroomsFloat % 1 !== 0 && bathroomsFloat > 1) {
            if (type === 'regular') halfBathCost = config.baths.half_reg;
            else if (type === 'deep') halfBathCost = config.baths.half_deep;
            else if (type === 'movein') halfBathCost = config.baths.move_in_half;
            else if (type === 'post') halfBathCost = config.baths.post_const_half;
        }

        let totalCost = baseCost + halfBathCost;

        return totalCost;
    }

    function updateFrequencyOptions() {
        const serviceType = $('input[name="service_type"]:checked').val() || 'reg';
        const $freqSelect = $('#frequency');
        const $allOptions = $freqSelect.find('option');
        const $onetime = $allOptions.filter('[data-freq-type="onetime"]');
        const $recurring = $allOptions.filter('[data-freq-type="recurring"]');

        $allOptions.prop('disabled', false).show();

        if (serviceType === 'reg') {
            $onetime.prop('disabled', true).hide();
            const currentVal = $freqSelect.val();
            const currentOption = $freqSelect.find('option[value="' + currentVal + '"]');
            if (currentOption.data('freq-type') === 'onetime' || !currentOption.length || currentOption.prop('disabled')) {
                const $firstRecurring = $recurring.filter(':not(:disabled)').first();
                if ($firstRecurring.length) $freqSelect.val($firstRecurring.val());
            }
        } else if (serviceType === 'move' || serviceType === 'post') {
            $recurring.prop('disabled', true).hide();
            const currentVal = $freqSelect.val();
            const currentOption = $freqSelect.find('option[value="' + currentVal + '"]');
            if (currentOption.data('freq-type') === 'recurring' || !currentOption.length || currentOption.prop('disabled')) {
                const $firstOnetime = $onetime.filter(':not(:disabled)').first();
                if ($firstOnetime.length) $freqSelect.val($firstOnetime.val());
            }
        } else if (serviceType === 'deep') {
            $allOptions.prop('disabled', true).hide();
            $onetime.prop('disabled', false).show();
            $recurring.each(function () {
                const txt = $(this).text().toLowerCase();
                if (txt.includes('monthly')) {
                    $(this).prop('disabled', false).show();
                }
            });
            const currentVal = $freqSelect.val();
            const currentOption = $freqSelect.find('option[value="' + currentVal + '"]');
            if (!currentOption.length || currentOption.prop('disabled')) {
                if ($onetime.length) $freqSelect.val($onetime.val());
            }
        }

        const finalVal = $freqSelect.val();
        const finalOption = $freqSelect.find('option[value="' + finalVal + '"]');
        if (!finalOption.length || finalOption.prop('disabled') || finalOption.css('display') === 'none') {
            const $firstValid = $allOptions.filter(':not(:disabled)').filter(function () {
                return $(this).css('display') !== 'none';
            }).first();
            if ($firstValid.length) {
                $freqSelect.val($firstValid.val());
            }
        }
    }

    function calculateTotal() {
        if ($('#cleaning-form').length === 0) return;
        try {
            const serviceType = $('input[name="service_type"]:checked').val() || 'reg';
            let bedrooms = parseInt($('#bedrooms').val()) || 0;
            let bathrooms = parseFloat($('#bathrooms').val()) || 0;

            // Enforce limits
            bedrooms = Math.min(6, Math.max(0, bedrooms));
            bathrooms = Math.min(7.5, Math.max(0, bathrooms));

            // STEP 1: Get raw Base Cost from matrix (Beds + Baths) — the only part subject to discount
            let baseCost = getMatrixPrice(serviceType, bedrooms, bathrooms);

            // STEP 2: Calculate EXACT SqFt Surcharge — HARDCODED, NEVER DISCOUNTED
            // Safely parse the select value (handles "1000", "1500", etc.)
            const $sqftSelect = $('#sqft');
            let sqftString = String($sqftSelect.val() || '0');
            let sqftValue = parseInt(sqftString.replace(/\D/g, '')) || 0;

            let sqftCost = 0;
            if (sqftValue > 1000) {
                let extraSqft = sqftValue - 1000;
                let steps = Math.ceil(extraSqft / 500);
                sqftCost = Math.max(0, steps * 10); // $10 per 500 sqft step
            }

            // STEP 3: Build discount multiplier — Move In / Post Con always = 1.0
            let discountMultiplier = 1;
            if (serviceType === 'move' || serviceType === 'post') {
                discountMultiplier = 1;
            } else {
                const freqText = $('#frequency option:selected').text().toLowerCase();
                if (freqText.includes('weekly') && !freqText.includes('bi')) {
                    discountMultiplier = 1 - (config.discounts.weekly / 100);
                } else if (freqText.includes('bi')) {
                    discountMultiplier = 1 - (config.discounts.biweekly / 100);
                } else if (freqText.includes('3 weeks') || freqText.includes('every 3 weeks')) {
                    discountMultiplier = 1 - (config.discounts.triweekly / 100);
                } else if (freqText.includes('monthly')) {
                    discountMultiplier = 1 - (config.discounts.monthly / 100);
                }
            }
            if (discountMultiplier < 0) discountMultiplier = 0;

            // Apply discount STRICTLY to Base Cost ONLY
            let discountedBaseCost = baseCost * discountMultiplier;

            // STEP 4: Extras — also NEVER discounted, added flat
            let extrasTotal = 0;
            $('input[data-price-key]').each(function () {
                const qty = parseInt($(this).val()) || 0;
                if (qty > 0) {
                    const priceKey = $(this).data('price-key');
                    const price = parseFloat(config.extras[priceKey]) || 0;
                    extrasTotal += qty * price;
                }
            });

            // STEP 5: Final Total = discounted base + untouched sqft + untouched extras
            let finalEstimate = discountedBaseCost + sqftCost + extrasTotal;

            // STEP 6: Update the DOM
            $('#final-price').text('$' + Math.round(finalEstimate));
        } catch (e) {
            console.error('Calculate Total Error:', e);
        }
    }

    $(document).on('click', '.counter-btn', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const targetId = $btn.data('target');
        const $input = $('#' + targetId);
        if (!$input.length) return;

        let val = parseFloat($input.val()) || 0;
        const min = parseFloat($input.attr('min')) || 0;
        const max = parseFloat($input.attr('max')) || 999;

        let step = 1;
        if (targetId === 'bathrooms') {
            step = 0.5;
        }

        if ($btn.hasClass('plus')) {
            if (val < max) {
                val += step;
            }
        } else if ($btn.hasClass('minus')) {
            if (val > min) {
                val -= step;
            }
        }

        val = Math.round(val * 10) / 10;

        if (targetId === 'bathrooms') {
            $input.val(val.toFixed(1)).trigger('change');
        } else {
            $input.val(Math.round(val)).trigger('change');
        }
    });

    $(document).on('click', '.btn-mini', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const targetId = $btn.data('target');
        const $input = $('#' + targetId);

        if (!$input.length) return;

        let val = parseInt($input.val()) || 0;

        if ($btn.hasClass('plus')) {
            val = parseInt(val) + 1;
        } else if ($btn.hasClass('minus') && val > 0) {
            val = parseInt(val) - 1;
        }

        $input.val(Math.max(0, parseInt(val))).trigger('change');
    });

    if ($('#cleaning-form').length > 0) {
        updateVisualPrices();

        const triggers = 'input[name="service_type"], #bedrooms, #bathrooms, #sqft, #frequency, input[data-price-key]';

        $(document).on('change input', triggers, function () {
            calculateTotal();
        });

        $('input[name="service_type"]').on('change', function () {
            updateFrequencyOptions();
            calculateTotal();
        });

        updateFrequencyOptions();
        calculateTotal();
    }

    $(document).on('click', '.mobile-menu-toggle', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const $btn = $(this);
        const $nav = $('.main-navigation');
        const $body = $('body');
        const isExpanded = $btn.attr('aria-expanded') === 'true';

        $btn.toggleClass('is-active');
        $nav.toggleClass('is-active');
        $body.toggleClass('no-scroll');

        $btn.attr('aria-expanded', !isExpanded);
    });

    $(document).on('click', '.main-navigation a', function () {
        $('.mobile-menu-toggle').removeClass('is-active').attr('aria-expanded', 'false');
        $('.main-navigation').removeClass('is-active');
        $('body').removeClass('no-scroll');
    });

    $(window).on('resize', function () {
        if ($(window).width() > 991) {
            $('.mobile-menu-toggle').removeClass('is-active').attr('aria-expanded', 'false');
            $('.main-navigation').removeClass('is-active');
            $('body').removeClass('no-scroll');
        }
    });

    const dateInput = document.querySelector('#service-date');
    if (dateInput && typeof flatpickr !== 'undefined') {
        flatpickr(dateInput, {
            minDate: "today",
            disableMobile: false,
            dateFormat: "d.m.Y"
        });
    }

    $(document).on('click', '#real-submit-btn', function (e) {
        e.preventDefault();

        const $btn = $(this);

        if (!config.isLoggedIn && config.loginUrl) {
            window.location.href = config.loginUrl;
            return;
        }

        if ($btn.prop('disabled')) return;

        const requiredFields = [
            { id: 'bedrooms' },
            { name: 'firstname' },
            { name: 'lastname' },
            { name: 'email' },
            { name: 'phone' },
            { name: 'address' },
            { id: 'service-date' },
            { id: 'service-time' }
        ];

        let hasError = false;
        let firstErrorEl = null;
        $('.text-input, .select-input').removeClass('border-danger');

        requiredFields.forEach(field => {
            let $el;
            if (field.id) $el = $('#' + field.id);
            else $el = $(`input[name="${field.name}"]`);

            if (!$el.length) return;

            const val = $el.val();
            if (!val || val.trim() === '') {
                hasError = true;
                $el.addClass('border-danger');
                if (!firstErrorEl) firstErrorEl = $el;
            }
        });

        if (hasError) {
            if (firstErrorEl) firstErrorEl.focus();
            return;
        }

        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');

        const extras = [];
        $('input[data-price-key]').each(function () {
            const qty = parseInt($(this).val()) || 0;
            if (qty > 0) {
                const name = $(this).attr('name').replace('extra_', '');
                extras.push(name + ':' + qty);
            }
        });

        const formData = {
            action: 'cleaning_create_order',
            nonce: config.nonce,
            service_type: $('input[name="service_type"]:checked').val(),
            bedrooms: $('#bedrooms').val(),
            bathrooms: $('#bathrooms').val(),
            sqft: $('#sqft').val(),
            frequency: $('#frequency').val(),
            date: $('#service-date').val(),
            time: $('#service-time').val(),
            total: $('#final-price').text().replace('$', ''),
            name: $('input[name="firstname"]').val() + ' ' + $('input[name="lastname"]').val(),
            email: $('input[name="email"]').val(),
            phone: $('input[name="phone"]').val(),
            address: $('input[name="address"]').val(),
            extras: extras.join(',')
        };

        $.ajax({
            url: config.ajaxUrl,
            type: 'POST',
            data: $.param(formData),
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            timeout: 60000
        }).then(function (response) {
            console.log('Server Response:', response);

            let isSuccess = false;
            let redirectUrl = config.bookingUrl || '/cabinet/';

            try {
                let data = (typeof response === 'object') ? response : JSON.parse(response);

                if (data && data.success === true) {
                    isSuccess = true;
                    if (data.data && data.data.redirect_url) {
                        redirectUrl = data.data.redirect_url;
                    }
                } else if (data && data.success === false) {
                    throw new Error(data.data.message || 'Order could not be created.');
                }
            } catch (e) {
                const responseStr = String(response);
                if (responseStr.includes('"success":true') ||
                    responseStr.includes('"order_id"') ||
                    responseStr.includes('order_created')) {
                    console.warn('JSON parse failed but success detected. Redirecting.');
                    isSuccess = true;
                } else {
                    throw e; // Rethrow to catch block
                }
            }

            if (isSuccess) {
                window.location.href = redirectUrl + (redirectUrl.includes('?') ? '&' : '?') + 't=' + Date.now();
            }

        }).catch(function (xhr, status, error) {
            console.error('AJAX Error:', xhr.status, xhr.statusText, xhr.responseText);

            if (xhr.status === 403) {
                alert('Session expired. Please refresh the page and try again.');
            } else if (xhr.status === 200 && status === 'parsererror') {
                console.warn('Caught 200 error, possibly successful order despite parse error.');
                window.location.href = (config.bookingUrl || '/cabinet/') + '?t=' + Date.now();
            } else {
                const errorMsg = (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message)
                    ? xhr.responseJSON.data.message
                    : 'Connection error. Please check your internet or contact support.';
                alert(errorMsg);
            }
            $btn.prop('disabled', false).html(originalText);
        });
    });

    $(document).on('click', '.js-pay-order', function (e) {
        e.preventDefault();
        const $btn = $(this);
        const orderId = $btn.data('order-id');
        const originalText = $btn.html();

        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: config.ajaxUrl,
            type: 'POST',
            data: $.param({
                action: 'cleaning_create_payment_intent',
                nonce: config.nonce,
                order_id: orderId
            }),
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            dataType: 'json'
        }).then(function (response) {
            if (response && response.success && response.data && response.data.checkout_url) {
                window.location.href = response.data.checkout_url;
            } else {
                const errorMsg = (response && response.data && response.data.message)
                    ? response.data.message
                    : 'Payment initialization failed.';
                alert(errorMsg);
                $btn.prop('disabled', false).html(originalText);
            }
        }).catch(function (xhr) {
            console.error('Payment Error:', xhr.status, xhr.responseText);
            if (xhr.status === 403) {
                alert('Session expired. Please refresh.');
            } else {
                alert('Connection error.');
            }
            $btn.prop('disabled', false).html(originalText);
        });
    });

    $(document).on('click', '.btn-cancel-order', function (e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to cancel this order?')) return;

        const $btn = $(this);
        const orderId = $btn.data('order-id');
        const originalText = $btn.html();

        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Cancelling...');

        $.ajax({
            url: config.ajaxUrl,
            type: 'POST',
            data: $.param({
                action: 'cleaning_cancel_order',
                nonce: config.nonce,
                order_id: orderId
            }),
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            dataType: 'json'
        }).then(function (response) {
            if (response && response.success) {
                window.location.reload();
            } else {
                const errorMsg = (response && response.data && response.data.message)
                    ? response.data.message
                    : 'Failed to cancel order.';
                alert(errorMsg);
                $btn.prop('disabled', false).html(originalText);
            }
        }).catch(function (xhr) {
            console.error('Cancellation Error:', xhr.status, xhr.responseText);
            alert('Server error.');
            $btn.prop('disabled', false).html(originalText);
        });
    });

    const tabs = document.querySelectorAll('.login-tab');
    if (tabs.length) {
        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                if (this.dataset.tab === 'login') {
                    $('#login-form').addClass('active');
                    $('#register-form').removeClass('active');
                } else {
                    $('#register-form').addClass('active');
                    $('#login-form').removeClass('active');
                }
            });
        });
    }

    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = document.getElementById('login-submit');
            const messageEl = document.getElementById('login-message');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="fa-solid fa-spinner fa-spin"></span> Signing in...';
            if (messageEl) messageEl.style.display = 'none';

            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: $.param({
                    action: 'cleaning_login',
                    nonce: config.nonce,
                    email: document.getElementById('login-email').value,
                    password: document.getElementById('login-password').value,
                    remember: document.getElementById('login-remember').checked ? 'true' : 'false'
                }),
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                dataType: 'json'
            }).then(function (data) {
                if (data.success) {
                    if (messageEl) {
                        messageEl.className = 'login-message success';
                        messageEl.textContent = data.data.message || 'Login successful!';
                        messageEl.style.display = 'block';
                    }
                    setTimeout(() => {
                        window.location.href = data.data.redirect_url || '/cabinet/';
                    }, 500);
                } else {
                    if (messageEl) {
                        messageEl.className = 'login-message error';
                        messageEl.textContent = data.data?.message || 'Login failed.';
                        messageEl.style.display = 'block';
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Sign In';
                }
            }).catch(function (xhr) {
                console.error('Login Error:', xhr.status, xhr.responseText);
                if (messageEl) {
                    messageEl.className = 'login-message error';
                    messageEl.textContent = 'Server error.';
                    messageEl.style.display = 'block';
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Sign In';
            });
        });
    }

    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const submitBtn = document.getElementById('register-submit');
            const messageEl = document.getElementById('register-message');

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="fa-solid fa-spinner fa-spin"></span> Creating account...';
            if (messageEl) messageEl.style.display = 'none';

            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: $.param({
                    action: 'cleaning_register',
                    nonce: config.nonce,
                    first_name: document.getElementById('reg-first-name').value,
                    last_name: document.getElementById('reg-last-name').value,
                    email: document.getElementById('reg-email').value,
                    phone: document.getElementById('reg-phone').value,
                    password: document.getElementById('reg-password').value
                }),
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                dataType: 'json'
            }).then(function (data) {
                if (data.success) {
                    if (messageEl) {
                        messageEl.className = 'login-message success';
                        messageEl.textContent = data.data.message || 'Account created!';
                        messageEl.style.display = 'block';
                    }
                    setTimeout(() => {
                        window.location.href = data.data.redirect_url || '/cabinet/';
                    }, 500);
                } else {
                    if (messageEl) {
                        messageEl.className = 'login-message error';
                        messageEl.textContent = data.data?.message || 'Registration failed.';
                        messageEl.style.display = 'block';
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Create Account';
                }
            }).catch(function (xhr) {
                console.error('Registration Error:', xhr.status, xhr.responseText);
                if (messageEl) {
                    messageEl.className = 'login-message error';
                    messageEl.textContent = 'Server error.';
                    messageEl.style.display = 'block';
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Create Account';
            });
        });
    }

    if (typeof Swiper !== 'undefined') {
        new Swiper('.results-swiper', {
            loop: true,
            slidesPerView: 1,
            spaceBetween: 20,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    }

});