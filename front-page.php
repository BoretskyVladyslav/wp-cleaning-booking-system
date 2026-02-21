<?php
get_header();
?>
<main id="primary" class="site-main">
    <?php get_template_part('template-parts/content', 'hero-split'); ?>

    <!-- Benefits Section -->
    <section class="benefits-section">
        <div class="container">
            <div class="benefits-wrapper">
                <div class="benefits-content">
                    <h2 class="section-title">Why Choose OlaLa Cleaning?</h2>
                    <p class="benefits-desc">Professional cleaning service you can trust with your home</p>
                    <ul class="benefits-list">
                        <li class="benefit-item">
                            <div class="check-icon">✓</div>
                            <div class="benefit-text">
                                <strong>Vetted Professionals</strong>
                                <p>Background-checked, trained, and insured team members</p>
                            </div>
                        </li>
                        <li class="benefit-item">
                            <div class="check-icon">✓</div>
                            <div class="benefit-text">
                                <strong>Eco-Friendly Products</strong>
                                <p>Safe, proven products that are gentle on your home and family</p>
                            </div>
                        </li>
                        <li class="benefit-item">
                            <div class="check-icon">✓</div>
                            <div class="benefit-text">
                                <strong>Flexible Scheduling</strong>
                                <p>Recurring cleanings at your convenience</p>
                            </div>
                        </li>
                        <li class="benefit-item">
                            <div class="check-icon">✓</div>
                            <div class="benefit-text">
                                <strong>100% Satisfaction Guaranteed</strong>
                                <p>Not happy? We'll re-clean for free</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="benefits-image">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/why-olala.jpg" alt="Why Choose OlaLa Cleaning Service" class="benefits-photo">
                </div>
            </div>
        </div>
    </section>

    <!-- How We Work (Video Section) -->
    <?php get_template_part('template-parts/section', 'video'); ?>

    <!-- See Our Results (Before/After Gallery) -->
    <?php get_template_part('template-parts/results', 'gallery'); ?>

    <!-- Reviews Marquee -->
    <?php get_template_part('template-parts/reviews', 'marquee'); ?>
</main>
<?php get_footer(); ?>