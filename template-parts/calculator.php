<section class="calculator-section" id="calculator-section">
    <div class="calc-split-container">
        
        <div class="calc-form-side">
            <div class="calc-wrapper-internal">
                
                <div class="calc-header">
                    <h2>Get Your Instant Quote</h2>
                    <p>Professional cleaning with transparent pricing</p>
                </div>

                <form id="cleaning-form" class="cleaning-form">
                    
                    <div class="calc-section">
                        <label class="section-label">Select Service</label>
                        <div class="service-grid">
                            <label class="service-option">
                                <input type="radio" name="service_type" value="reg" checked>
                                <div class="option-card">
                                    <i class="fa-solid fa-broom"></i>
                                    <span>Regular</span>
                                </div>
                            </label>
                            <label class="service-option">
                                <input type="radio" name="service_type" value="deep">
                                <div class="option-card">
                                    <i class="fa-solid fa-hands-bubbles"></i>
                                    <span>Deep Clean</span>
                                </div>
                            </label>
                            <label class="service-option">
                                <input type="radio" name="service_type" value="move">
                                <div class="option-card">
                                    <i class="fa-solid fa-box-open"></i>
                                    <span>Move In/Out</span>
                                </div>
                            </label>
                            <label class="service-option">
                                <input type="radio" name="service_type" value="post">
                                <div class="option-card">
                                    <i class="fa-solid fa-helmet-safety"></i>
                                    <span>Post-Const</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="calc-section">
                        <label class="section-label">Property Details</label>
                        <div class="property-grid-2col">
                            <div class="property-input">
                                <label>Bedrooms</label>
                                <div class="counter-control">
                                    <button type="button" class="counter-btn minus" data-target="bedrooms">−</button>
                                    <input type="text" id="bedrooms" name="bedrooms" value="1" min="0" max="6" readonly>
                                    <button type="button" class="counter-btn plus" data-target="bedrooms">+</button>
                                </div>
                            </div>
                            <div class="property-input">
                                <label>Bathrooms</label>
                                <div class="counter-control">
                                    <button type="button" class="counter-btn minus" data-target="bathrooms">−</button>
                                    <input type="text" id="bathrooms" name="bathrooms" value="1.0" min="0" max="7.5" step="0.5" readonly>
                                    <button type="button" class="counter-btn plus" data-target="bathrooms">+</button>
                                </div>
                            </div>
                            <div class="property-input">
                                <label>Square Feet</label>
                                <select name="sqft" id="sqft" class="select-input">
                                    <option value="1000">Up to 1000</option>
                                    <option value="1500">1000-1500</option>
                                    <option value="2000">1500-2000</option>
                                    <option value="2500">2000-2500</option>
                                    <option value="3000">2500-3000</option>
                                    <option value="3500">3000-3500</option>
                                    <option value="4000">3500-4000</option>
                                    <option value="4500">4000-4500</option>
                                    <option value="5000">4500-5000</option>
                                    <option value="5500">5000-5500</option>
                                    <option value="6000">5500-6000</option>
                                    <option value="6500">6000+</option>
                                </select>
                            </div>
                            <div class="property-input">
                                <label>Frequency</label>
                                <select name="frequency" id="frequency" class="select-input">
                                    <option value="1" data-freq-type="onetime">One-time</option>
                                    <option value="0.85" data-freq-type="recurring">Weekly (15% off)</option>
                                    <option value="0.90" data-freq-type="recurring">Bi-weekly (10% off)</option>
                                    <option value="0.95" data-freq-type="recurring">Every 3 weeks (5% off)</option>
                                    <option value="0.95" data-freq-type="recurring">Monthly (5% off)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="calc-section">
                        <label class="section-label">Add-On Services</label>
                        <div class="extras-grid">
                            <div class="extra-item">
                                <div class="extra-info">
                                    <div class="extra-icon">
                                        <i class="fa-solid fa-snowflake"></i>
                                    </div>
                                    <div class="extra-details">
                                        <span class="extra-name">Fridge</span>
                                        <span class="extra-price">$<span class="price-val" data-key="cleaning_fridge_price"><?php echo get_option('cleaning_fridge_price', 40); ?></span></span>
                                    </div>
                                </div>
                                <div class="extra-counter">
                                    <button type="button" class="btn-mini minus" data-target="extra_fridge">−</button>
                                    <input type="text" name="extra_fridge" id="extra_fridge" value="0" min="0" data-price-key="cleaning_fridge_price" readonly>
                                    <button type="button" class="btn-mini plus" data-target="extra_fridge">+</button>
                                </div>
                            </div>
                            <div class="extra-item">
                                <div class="extra-info">
                                    <div class="extra-icon">
                                        <i class="fa-solid fa-temperature-high"></i>
                                    </div>
                                    <div class="extra-details">
                                        <span class="extra-name">Oven</span>
                                        <span class="extra-price">$<span class="price-val" data-key="cleaning_oven_price"><?php echo get_option('cleaning_oven_price', 40); ?></span></span>
                                    </div>
                                </div>
                                <div class="extra-counter">
                                    <button type="button" class="btn-mini minus" data-target="extra_oven">−</button>
                                    <input type="text" name="extra_oven" id="extra_oven" value="0" min="0" data-price-key="cleaning_oven_price" readonly>
                                    <button type="button" class="btn-mini plus" data-target="extra_oven">+</button>
                                </div>
                            </div>
                            <div class="extra-item">
                                <div class="extra-info">
                                    <div class="extra-icon">
                                        <i class="fa-solid fa-shirt"></i>
                                    </div>
                                    <div class="extra-details">
                                        <span class="extra-name">Laundry</span>
                                        <span class="extra-price">$<span class="price-val" data-key="cleaning_laundry_price"><?php echo get_option('cleaning_laundry_price', 25); ?></span></span>
                                    </div>
                                </div>
                                <div class="extra-counter">
                                    <button type="button" class="btn-mini minus" data-target="extra_laundry">−</button>
                                    <input type="text" name="extra_laundry" id="extra_laundry" value="0" min="0" data-price-key="cleaning_laundry_price" readonly>
                                    <button type="button" class="btn-mini plus" data-target="extra_laundry">+</button>
                                </div>
                            </div>
                            <div class="extra-item">
                                <div class="extra-info">
                                    <div class="extra-icon">
                                        <i class="fa-solid fa-window-maximize"></i>
                                    </div>
                                    <div class="extra-details">
                                        <span class="extra-name">Windows</span>
                                        <span class="extra-price">$<span class="price-val" data-key="cleaning_windows_price"><?php echo get_option('cleaning_windows_price', 5); ?></span>/ea</span>
                                    </div>
                                </div>
                                <div class="extra-counter">
                                    <button type="button" class="btn-mini minus" data-target="extra_windows">−</button>
                                    <input type="text" name="extra_windows" id="extra_windows" value="0" min="0" data-price-key="cleaning_windows_price" readonly>
                                    <button type="button" class="btn-mini plus" data-target="extra_windows">+</button>
                                </div>
                            </div>
                            <div class="extra-item">
                                <div class="extra-info">
                                    <div class="extra-icon">
                                        <i class="fa-solid fa-door-closed"></i>
                                    </div>
                                    <div class="extra-details">
                                        <span class="extra-name">Cabinets</span>
                                        <span class="extra-price">$<span class="price-val" data-key="cleaning_cabinets_price"><?php echo get_option('cleaning_cabinets_price', 50); ?></span></span>
                                    </div>
                                </div>
                                <div class="extra-counter">
                                    <button type="button" class="btn-mini minus" data-target="extra_cabinets">−</button>
                                    <input type="text" name="extra_cabinets" id="extra_cabinets" value="0" min="0" data-price-key="cleaning_cabinets_price" readonly>
                                    <button type="button" class="btn-mini plus" data-target="extra_cabinets">+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="calc-section">
                        <label class="section-label">Contact Information</label>
                        <div class="contact-grid-dense">
                            <input type="text" name="firstname" class="text-input" placeholder="First Name" required>
                            <input type="text" name="lastname" class="text-input" placeholder="Last Name" required>
                            <input type="tel" name="phone" class="text-input" placeholder="Phone Number" required>
                            <input type="email" name="email" class="text-input" placeholder="Email Address" required>
                            <input type="text" name="address" class="text-input address-wide" placeholder="Service Address" required>
                            <input type="date" name="date" id="service-date" class="text-input date-narrow" placeholder="dd.mm.yyyy" required>
                            <select name="time" id="service-time" class="text-input time-select" required>
                                <option value="">Preferred Time</option>
                                <option value="08:00">8:00 AM</option>
                                <option value="09:00">9:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="13:00">1:00 PM</option>
                                <option value="14:00">2:00 PM</option>
                                <option value="15:00">3:00 PM</option>
                                <option value="16:00">4:00 PM</option>
                                <option value="17:00">5:00 PM</option>
                            </select>
                        </div>
                    </div>

                    <div class="calc-summary-card">
                        <div class="summary-content">
                            <div class="summary-label">Total Estimate</div>
                            <div id="final-price" class="summary-price">$0</div>
                        </div>
                        <button type="submit" id="real-submit-btn" class="book-btn">
                            <span>Book Now</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>

                </form>
            </div>
        </div>
        
        <div class="calc-image-side" style="background-image: url('<?php echo get_template_directory_uri(); ?>/assets/images/owner.jpg');">
            <div class="calc-image-overlay">
                <div class="testimonial">
                    <div class="testimonial-author">
                        <span class="author-name">Olga</span>
                        <span class="author-title">Founder & Owner</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>