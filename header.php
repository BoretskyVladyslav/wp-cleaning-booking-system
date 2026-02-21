<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header class="site-header">
    <div class="container header-container">
        <a href="<?php echo home_url('/'); ?>" class="logo">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.jpg" alt="O-La-La Cleaning Service" width="150" height="50">
        </a>
        <button class="mobile-menu-toggle" aria-label="Toggle Menu" aria-expanded="false">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
        <nav class="main-navigation">
            <ul class="nav-list">
                <li><a href="<?php echo home_url('/'); ?>" class="nav-link <?php echo is_front_page() ? 'active' : ''; ?>">Home</a></li>
                <li><a href="<?php echo get_permalink(get_page_by_path('services')); ?>" class="nav-link <?php echo is_page('services') ? 'active' : ''; ?>">Services</a></li>
                <li><a href="<?php echo get_permalink(get_page_by_path('about')); ?>" class="nav-link <?php echo is_page('about') ? 'active' : ''; ?>">About Us</a></li>
                <li><a href="<?php echo home_url('/contact/'); ?>" class="nav-link <?php echo is_page('contact') ? 'active' : ''; ?>">Contacts</a></li>
            </ul>
        </nav>
        <div class="header-actions"> 
            
            <?php if (is_user_logged_in()) :   
                 $current_user = wp_get_current_user(); 
                $display_name = $current_user->display_name ?: $current_user->user_email;
                $is_admin = current_user_can('administrator');
                $cabinet_page = get_page_by_path('cabinet');
                $cabinet_link = $cabinet_page ? get_permalink($cabinet_page) : home_url('/cabinet/');
            ?>
                <div class="user-menu header-element-hover">
                    <a href="<?php echo $is_admin ? admin_url() : $cabinet_link; ?>" class="user-profile-link">
                        <span class="user-name"><?php echo esc_html($display_name); ?></span>
                        <i class="fa-solid fa-circle-user fa-xl"></i>  
                    </a> 
                    <div class="user-dropdown">   
                        <?php if ($is_admin) : ?> 
                            <a href="<?php echo admin_url(); ?>"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>
                        <?php else : ?>
                            <a href="<?php echo $cabinet_link; ?>"><i class="fa-solid fa-clipboard-list"></i> My Cabinet</a>
                            <a href="<?php echo home_url('/edit-profile/'); ?>"><i class="fa-solid fa-user-pen"></i> Edit Profile</a>
                        <?php endif; ?>
                        <a href="<?php echo wp_logout_url(home_url('/')); ?>" class="logout-link"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                    </div>
                </div>
            <?php else : ?>
                <a href="<?php echo home_url('/login/'); ?>" class="btn btn--small btn--outline header-element-hover">
                    <i class="fa-solid fa-right-to-bracket"></i> Login
                </a>
            <?php endif; ?> 
        </div> 
    </div>
</header>