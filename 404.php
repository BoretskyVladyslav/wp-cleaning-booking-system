<?php
get_header();
?>
<section class="error-404-section">
    <div class="container">
        <div class="error-content">
            <div class="error-icon">
                <i class="fa-solid fa-broom"></i>
                <i class="fa-solid fa-sparkles sparkle-1"></i>
                <i class="fa-solid fa-sparkles sparkle-2"></i>
            </div>
            <h1>404</h1>
            <h2>Oops! This spot is cleaner than expected.</h2>
            <p>Looks like our cleaning crew was a little too thorough and swept this page away! The page you're looking for doesn't exist or has been moved.</p>
            <div class="error-actions">
                <a href="<?php echo home_url('/'); ?>" class="btn btn--primary btn--large">
                    <i class="fa-solid fa-house"></i> Back to Home
                </a>
                <a href="<?php echo home_url('/contacts/'); ?>" class="btn btn--outline btn--large">
                    <i class="fa-solid fa-phone"></i> Contact Us
                </a>
            </div>
            <div class="error-suggestions">
                <p><strong>You might want to:</strong></p>
                <ul>
                    <li><a href="<?php echo home_url('/services/'); ?>">View Our Services</a></li>
                    <li><a href="<?php echo home_url('/#calculator'); ?>">Get a Free Quote</a></li>
                    <li><a href="<?php echo home_url('/about/'); ?>">Learn About Us</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
<style>
.error-404-section {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    background: linear-gradient(135deg, 
}
.error-content {
    text-align: center;
    max-width: 600px;
}
.error-icon {
    position: relative;
    display: inline-block;
    margin-bottom: 30px;
}
.error-icon .fa-broom {
    font-size: 80px;
    color: 
    animation: sweep 2s ease-in-out infinite;
}
.error-icon .sparkle-1,
.error-icon .sparkle-2 {
    position: absolute;
    font-size: 20px;
    color: 
    animation: sparkle 1.5s ease-in-out infinite;
}
.sparkle-1 {
    top: -10px;
    right: -20px;
}
.sparkle-2 {
    bottom: 10px;
    left: -15px;
    animation-delay: 0.5s;
}
@keyframes sweep {
    0%, 100% { transform: rotate(-5deg); }
    50% { transform: rotate(5deg); }
}
@keyframes sparkle {
    0%, 100% { opacity: 0.3; transform: scale(0.8); }
    50% { opacity: 1; transform: scale(1.2); }
}
.error-content h1 {
    font-size: 120px;
    font-weight: 800;
    color: 
    margin: 0;
    line-height: 1;
    background: linear-gradient(135deg, 
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.error-content h2 {
    font-size: 1.8rem;
    color: 
    margin: 10px 0 20px;
}
.error-content > p {
    color: 
    font-size: 1.1rem;
    line-height: 1.7;
    margin-bottom: 35px;
}
.error-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
    margin-bottom: 40px;
}
.btn--large {
    padding: 14px 28px;
    font-size: 1rem;
}
.btn--outline {
    background: transparent;
    border: 2px solid 
    color: 
}
.btn--outline:hover {
    background: 
    border-color: 
    color: 
}
.error-suggestions {
    padding: 25px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.error-suggestions p {
    margin: 0 0 15px;
    color: 
}
.error-suggestions ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}
.error-suggestions a {
    color: 
    text-decoration: none;
    font-weight: 500;
}
.error-suggestions a:hover {
    text-decoration: underline;
}
@media (max-width: 576px) {
    .error-content h1 {
        font-size: 80px;
    }
    .error-content h2 {
        font-size: 1.4rem;
    }
    .error-icon .fa-broom {
        font-size: 60px;
    }
    .error-actions {
        flex-direction: column;
    }
    .error-suggestions ul {
        flex-direction: column;
        gap: 10px;
    }
}
</style>
<?php get_footer(); ?>