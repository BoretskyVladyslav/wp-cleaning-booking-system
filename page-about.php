<?php
get_header();
?>
<main id="primary" class="site-main">
    <section class="about-hero-section">
        <div class="container">
            <div class="about-hero-grid"> 
                <div class="about-hero-content">
                    <h1 class="hero-title">We don't just clean. <br>We give you back your time.</h1>
                    <p class="hero-subtitle">A professional team using proven products and professional-grade equipment.</p>
                </div>
                <div class="about-hero-image">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/about-team-main.jpg" alt="Our Professional Cleaning Team" class="team-hero-photo">
                </div>
            </div>
        </div>
    </section>
    <!-- VALUES SECTION -->
    <section class="about-values-section"> 
        <div class="container">
            <div class="text-center" style="margin-bottom: 60px;">
                <h2 class="section-title">Why Trust Us With Your Home?</h2>
            </div>
            <div class="values-grid">
                <!-- Card 1 -->
                <div class="value-card">
                    <div class="value-icon"><i class="fa-solid fa-shield-halved"></i></div>
                    <h3>Security</h3>
                    <p>Every cleaner is background-checked and insured. Your property is always protected.</p>
                </div>
                <!-- Card 2 -->
                <div class="value-card">
                    <div class="value-icon"><i class="fa-solid fa-leaf"></i></div>
                    <h3>Safe Products</h3>
                    <p>We use safe and proven cleaning products that are effective yet gentle on your home.</p>
                </div>
                <!-- Card 3 -->
                <div class="value-card">
                    <div class="value-icon"><i class="fa-solid fa-plug"></i></div>
                    <h3>Professional Equipment</h3>
                    <p>We use professional-grade vacuums, steam cleaners, and modern equipment for superior results.</p>
                </div>
                <!-- Card 4 -->
                <div class="value-card">
                    <div class="value-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                    <h3>Trained Staff</h3>
                    <p>Our cleaners are professionally trained and know the best techniques for every surface.</p>
                </div>
            </div>
        </div>
    </section>
    <!-- STATS SECTION -->
    <section class="about-stats-section">
        <div class="container">
            <div class="stats-row">
                <div class="stat-item">
                    <span class="stat-number">3+</span>
                    <span class="stat-label">years of experience</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">2100+</span>
                    <span class="stat-label">homes cleaned</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">99.9%</span>
                    <span class="stat-label">satisfied customers</span>
                </div>
            </div>
        </div>
    </section>
    <!-- TEAM SECTION -->
    <section class="about-team-section">
        <div class="container">
            <div class="text-center" style="margin-bottom: 50px;">
                <h2 class="section-title">Our Team</h2>
            </div>
            <!-- TEAM GRID -->
            <!-- TEAM GRID -->
            <div class="team-grid">
                <?php
                $team_members = [
                    ['name' => 'Olga', 'title' => 'Founder', 'img' => 'team-member1.jpg'],
                    ['name' => 'Team Member', 'title' => 'Cleaning Specialist', 'img' => 'team-member2.jpg'],
                    ['name' => 'Team Member', 'title' => 'Cleaning Specialist', 'img' => 'team-member3.jpg'],
                    ['name' => 'Team Member', 'title' => 'Cleaning Specialist', 'img' => 'team-member4.jpg'],
                ];

                foreach ($team_members as $member) :
                    // Logic: Founder keeps title, everyone else is 'Cleaning Specialist'
                    $display_title = ($member['title'] === 'Founder') ? 'Founder' : 'Cleaning Specialist';
                    $role_class = ($member['title'] === 'Founder') ? 'team-role founder-role' : 'team-role';
                ?>
                    <div class="team-card">
                        <div class="team-avatar">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/<?php echo $member['img']; ?>" alt="<?php echo $member['name']; ?>" class="team-member-photo">
                        </div>
                        <h4><?php echo $member['name']; ?></h4>
                        <p class="<?php echo $role_class; ?>"><?php echo $display_title; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- CTA SECTION -->
    <section class="about-cta-section">
        <div class="container">
            <div class="cta-focused-container">
                <h2 class="cta-title">Ready to Get Started?</h2>
                <p class="cta-subtitle">Let us take care of the cleaning while you focus on what matters.</p>
                <div class="cta-action-wrapper">
                    <svg class="cta-arrow" width="80" height="50" viewBox="0 0 80 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 10 C 20 35, 45 40, 70 25" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                        <path d="M62 22 L 72 25 L 65 33" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                    </svg>
                    <a href="<?php echo home_url('/'); ?>" class="btn btn--primary btn--lg">
                        Get a Free Quote
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
get_footer();