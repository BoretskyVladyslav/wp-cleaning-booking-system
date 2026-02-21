<?php
/**
 * Template Part: Infinite Reviews Marquee (Wall of Love)
 * Displays Google reviews in a 3-row infinite scrolling marquee
 */

$reviews = [
    ['name' => 'Lisbeth Lynn', 'text' => 'We have been having Olga and her service for several years now. They are extremely conscientious, reliable and thorough. House is always spotless.'],
    ['name' => 'J D', 'text' => 'Olga and her team are professional, very thorough, and reliable. A pleasure to work with, highly recommend.'],
    ['name' => 'Ulyana Hubenko', 'text' => 'Cleaners are professional, kind, and detail-oriented. Scheduling is easy, and the quality of cleaning is great.'],
    ['name' => 'Iryna Vitkovych', 'text' => 'I\'ve been using this service for six months. Especially appreciate Natalia, she\'s reliable and does a great job.'],
    ['name' => 'Scott Ortes', 'text' => 'Olga and her team are wonderful! Hard working and dependable. I always get many compliments on my home!'],
    ['name' => 'Kandace', 'text' => 'Best decision! She does an amazing job, has great communication, is super punctual, and has great rates.'],
    ['name' => 'Amy Munson', 'text' => 'Consistently exceeds my expectations! Thorough, timely, and flexible with special requests.'],
    ['name' => 'ALLA FASTYK', 'text' => 'Fantastic! They\'re very reliable and consistently do great work. Appreciate the care they put into every visit.'],
    ['name' => 'Rianna Larson', 'text' => 'Wonderful to work with. Dependable and excellent results. The level of detail stands out.'],
    ['name' => 'Alia Valeor', 'text' => 'Professional, friendly, and wonderful job. Everything looked spotless, paid attention to small details.'],
    ['name' => 'Jeff Walters', 'text' => 'Impressive results! Very thorough cleaning from sparkling mirrors to shiny hardwood floors.'],
    ['name' => 'Linda Ross', 'text' => 'Trustworthy, thorough and quick to adapt to requests. Been using O La La Cleaning Service for years.'],
    ['name' => 'Jason S.', 'text' => 'Cleaning our condo building for over a year. Friendly, fast, thorough, and affordable. Good communication.'],
    ['name' => 'Uliana Tyma', 'text' => 'Professional, reliable, pays great attention to detail. Always on time and leave my home spotless.'],
    ['name' => 'Delisca', 'text' => 'Amazing service! Professional, reliable, and very detail-oriented. Highly recommend!'],
    ['name' => 'Becky Levine', 'text' => 'Olga and her team do a great job. They bring their own supplies. Timely and professional.'],
    ['name' => 'Roman Vyspianskyi', 'text' => 'Great service! Used for new construction and happy with the results!'],
    ['name' => 'Wendy Campos', 'text' => 'Attention to detail is amazing, my house always looks spotless. Honestly a breath of fresh air.'],
    ['name' => 'Donald M Rankin', 'text' => 'Extremely reliable, arrive on time, do a thoroughly great job.'],
    ['name' => 'Cathie Winnie', 'text' => 'I\'ve used this service for over a year. Great job! As an older person I never worry about who\'s in my house.'],
    ['name' => 'Mary S', 'text' => 'Friendly, efficient, and thorough cleaning service. Highly recommended!'],
    ['name' => 'Daniela Salas', 'text' => 'Total recommend them, super professional work!'],
];

// Split reviews into 3 rows (roughly equal distribution)
$total = count($reviews);
$chunk1 = array_slice($reviews, 0, ceil($total / 3));
$chunk2 = array_slice($reviews, ceil($total / 3), ceil($total / 3));
$chunk3 = array_slice($reviews, ceil($total / 3) * 2);

/**
 * Render a review card
 */
function render_review_card($review) {
    ?>
    <div class="review-card">
        <div class="review-stars">
            <span class="star">★</span>
            <span class="star">★</span>
            <span class="star">★</span>
            <span class="star">★</span>
            <span class="star">★</span>
        </div>
        <p class="review-text"><?php echo esc_html($review['text']); ?></p>
        <p class="review-author">— <?php echo esc_html($review['name']); ?></p>
    </div>
    <?php
}
?>

<section class="reviews-marquee-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">What Our Clients Say</h2>
            <p class="section-subtitle">Real reviews from real customers</p>
        </div>
    </div>

    <div class="marquee-container">
        <!-- Row 1: Scroll Left -->
        <div class="marquee-row">
            <div class="marquee-track scroll-left">
                <?php 
                // Duplicate content for seamless infinite loop
                foreach ($chunk1 as $review) { render_review_card($review); }
                foreach ($chunk1 as $review) { render_review_card($review); }
                ?>
            </div>
        </div>

        <!-- Row 2: Scroll Right -->
        <div class="marquee-row">
            <div class="marquee-track scroll-right">
                <?php 
                // Duplicate content for seamless infinite loop
                foreach ($chunk2 as $review) { render_review_card($review); }
                foreach ($chunk2 as $review) { render_review_card($review); }
                ?>
            </div>
        </div>

        <!-- Row 3: Scroll Left (Desktop only) -->
        <div class="marquee-row marquee-row--desktop-only">
            <div class="marquee-track scroll-left">
                <?php 
                // Duplicate content for seamless infinite loop
                foreach ($chunk3 as $review) { render_review_card($review); }
                foreach ($chunk3 as $review) { render_review_card($review); }
                ?>
            </div>
        </div>
    </div>
</section>
