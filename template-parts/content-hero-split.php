<?php
/**
 * Template part for displaying the hero split-screen section with Calculator LEFT
 */
?>

<section class="hero-split-section">
    <!-- Full width green bar -->
    <div class="hero-call-bar">
        Call O-La-La Cleaning Service <a href="tel:+12244919701" style="color:white; text-decoration:none;">(224) 491 9701</a>
    </div>

    <div class="container">
        <!-- ROW 1: HEADER has been replaced by the bar above -->

        <!-- ROW 2: SPLIT LAYOUT (Dynamic Include) -->
        <?php get_template_part('template-parts/calculator'); ?>
    </div>
</section>

<!-- Inline script for Cabinets Checkbox UI toggle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cabCheck = document.querySelector('input[name="extra_cabinets"]');
    if(cabCheck) {
        cabCheck.addEventListener('change', function() {
            const label = this.closest('label');
            if (!label) return;
            
            // Defensive: Check for icon existence
            const icon = label.querySelector('.check-indicator i');
            
            if(this.checked) {
                if (label.classList) label.classList.add('active');
                if (icon) {
                    icon.className = 'fa-solid fa-square-check';
                    if (icon.parentNode) icon.parentNode.style.color = '#2563eb';
                }
            } else {
                if (label.classList) label.classList.remove('active');
                if (icon) {
                    icon.className = 'fa-regular fa-square';
                    if (icon.parentNode) icon.parentNode.style.color = '#ccc';
                }
            }
            // Trigger calculation in app.js
            const event = new Event('change', { bubbles: true });
            this.dispatchEvent(event);
        });
    }
});
</script>
