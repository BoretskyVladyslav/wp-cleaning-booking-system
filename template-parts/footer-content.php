<?php
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col about-col">
                <a href="<?php echo home_url('/'); ?>" class="footer-logo">O-La-La Cleaning</a>
                <p>Professional cleaning for your home and office in Chicago & Suburbs. We take care of cleanliness so you can focus on what matters.</p>
            </div>
            <div class="footer-col nav-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="<?php echo home_url('/'); ?>">Home</a></li>
                    <li><a href="<?php echo get_permalink(get_page_by_path('services')); ?>">Services</a></li>
                    <li><a href="<?php echo get_permalink(get_page_by_path('about')); ?>">About Us</a></li>
                    <li><a href="<?php echo home_url('/contact/'); ?>">Contact</a></li>
                    <li><a href="https://g.co/kgs/reviews" target="_blank" rel="noopener">Reviews</a></li>
                </ul>
            </div>
            <div class="footer-col contact-col">
                <h4>Contacts</h4>
                <ul class="contact-list">
                    <li><i class="fa-solid fa-phone"></i> <a href="tel:+12244919701">+1 (224) 491-9701</a></li>
                    <li><i class="fa-solid fa-envelope"></i> <a href="mailto:olgavyspianska805@gmail.com">olgavyspianska805@gmail.com</a></li>
                    <li><i class="fa-solid fa-clock"></i> Mon-Sat: 08:00 AM - 06:00 PM</li>
                </ul>
            </div>
            <div class="footer-col social-col">
                <h4>Follow Us</h4>
                <div class="social-icons footer-social">
                    <a href="https://www.facebook.com/profile.php?id=61570140180706" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                        <i class="fa-brands fa-facebook"></i>
                    </a>
                    <a href="https://www.instagram.com/olalacleaning.skokie/" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> O-La-La Cleaning Service. All rights reserved.</p>
            <div class="footer-legal-links">
                <a href="<?php echo home_url('/privacy-policy/'); ?>">Privacy Policy</a>
                <span class="divider">|</span>
                <a href="<?php echo home_url('/terms-of-service/'); ?>">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>