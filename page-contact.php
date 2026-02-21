<?php 
/* Template Name: Contact Page */
get_header(); 
?>

<main class="contact-page-wrapper section-padding">
    <div class="container">
        <!-- Centered Contact Info (Map Removed) -->
        <div class="row g-5 justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="contact-header mb-5">
                    <h1 class="display-5 fw-bold mb-3">Get in Touch</h1>
                    <p class="lead text-muted">
                        Professional cleaning services in Chicago & Suburbs. 
                        <br>We are ready to make your space shine!
                    </p>
                </div>

                <div class="contact-grid-top mb-5" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; text-align: left;">
                    <a href="tel:+12244919701" class="text-decoration-none">
                        <div class="contact-card">
                            <div class="icon-box"><i class="fa-solid fa-phone"></i></div>
                            <div>
                                <span class="contact-label">Call or Text Us</span>
                                <span class="contact-value">+1 (224) 491-9701</span>
                            </div>
                        </div>
                    </a>

                    <a href="mailto:olgavyspianska805@gmail.com" class="text-decoration-none">
                        <div class="contact-card">
                            <div class="icon-box"><i class="fa-solid fa-envelope"></i></div>
                            <div>
                                <span class="contact-label">Email Us</span>
                                <span class="contact-value">olgavyspianska805@gmail.com</span>
                            </div>
                        </div>
                    </a>

                    <div class="contact-card">
                        <div class="icon-box"><i class="fa-solid fa-clock"></i></div>
                        <div>
                            <span class="contact-label">Working Hours</span>
                            <span class="contact-value">Mon–Sat: 8:00 AM – 6:00 PM</span>
                        </div>
                    </div>
                </div>

                <div class="contact-booking-box mt-4 max-w-600 mx-auto">
                    <h4 class="h5 mb-2">Ready to book?</h4>
                    <p class="text-muted small mb-3">Skip the call and get a price instantly using our online calculator.</p>
                    <a href="<?php echo site_url('/'); ?>" class="btn btn-primary w-100 py-2 fw-bold">
                        <i class="fa-solid fa-calendar-check me-2"></i> Book Online Now
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- NEW Dark Service Area Section -->
    <section class="service-area-dark">
        <div class="container">
            <h2 class="section-title text-center text-white mb-5">Our Service Area</h2>
            
            <div class="service-area-grid">
                <?php
                $cities = [
                    'Albany Park (Chicago)', 'Arlington Heights', 'Buffalo Grove', 'Chicago', 
                    'Deerfield', 'Des Plaines', 'Elmwood Park', 'Evanston', 'Glencoe', 
                    'Glenview', 'Harwood Heights', 'Highland Park', 'Lincoln Square (Chicago)', 
                    'Lincolnwood', 'Morton Grove', 'Mount Prospect', 'Niles', 'Norridge', 
                    'Northbrook', 'Park Ridge', 'Prospect Heights', 'River Grove', 
                    'Rogers Park (Chicago)', 'Rosemont', 'Skokie', 'West Ridge (Chicago)', 
                    'Wheeling', 'Wilmette', 'Winnetka'
                ];
                
                foreach ($cities as $city) : ?>
                    <div class="service-city-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span><?php echo $city; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div> 
    </section>
</main>

<?php get_footer(); ?>