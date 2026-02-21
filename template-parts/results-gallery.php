<?php
/**
 * Template Part: See Our Results (Carousel)
 * Uses Swiper.js for a responsive image slider
 */
?>
<section class="results-section">
    <div class="container">
        <div class="section-header text-center">
            <h2 class="section-title">See Our Results</h2>
            <p class="section-subtitle">Professional cleaning that transforms your space</p>
        </div>
    </div>
        
    <!-- Swiper Container (Full Width) -->
    <div class="swiper swiper-container results-swiper">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide">
                <img 
                    src="<?php echo get_template_directory_uri(); ?>/assets/images/before-after1.jpg" 
                    alt="Kitchen Transformation" 
                    class="result-img"
                    loading="lazy"
                >
            </div>
            <!-- Slide 2 -->
            <div class="swiper-slide">
                <img 
                    src="<?php echo get_template_directory_uri(); ?>/assets/images/before-after2.jpg" 
                    alt="Bathroom Deep Clean" 
                    class="result-img"
                    loading="lazy"
                >
            </div>
            <!-- Slide 3 -->
            <div class="swiper-slide">
                <img 
                    src="<?php echo get_template_directory_uri(); ?>/assets/images/before-after3.jpg" 
                    alt="Living Room Refresh" 
                    class="result-img"
                    loading="lazy"
                >
            </div>
            <!-- Slide 4 -->
            <div class="swiper-slide">
                <img 
                    src="<?php echo get_template_directory_uri(); ?>/assets/images/before-after4.jpg" 
                    alt="Bedroom Organization" 
                    class="result-img"
                    loading="lazy"
                >
            </div>
            <!-- Slide 5 (Duplicate for loop) -->
            <div class="swiper-slide">
                <img 
                    src="<?php echo get_template_directory_uri(); ?>/assets/images/before-after1.jpg" 
                    alt="Kitchen Sparkle" 
                    class="result-img"
                    loading="lazy"
                >
            </div>
            <!-- Slide 6 (Duplicate for loop) -->
            <div class="swiper-slide">
                <img 
                    src="<?php echo get_template_directory_uri(); ?>/assets/images/before-after2.jpg" 
                    alt="Bathroom Shine" 
                    class="result-img"
                    loading="lazy"
                >
            </div>
        </div>
        
        <!-- Navigation -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>
</section>
