<?php
/**
 * Template Part: How We Work (Video Section)
 * Shows the cleaning process in action
 */
?>
<section class="how-we-work-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">How We Work</h2>
            <p class="section-subtitle">Watch our professional team in action</p>
        </div>
        <div class="video-container">
            <video 
                id="how-we-work-video"
                class="work-video" 
                controls 
                muted
                playsinline
                loop
                preload="metadata"
            >
                <source src="<?php echo get_template_directory_uri(); ?>/assets/video/how-we-work.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </div>
</section>