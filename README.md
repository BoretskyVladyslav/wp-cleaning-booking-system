# Olala Cleaning — Dynamic Booking Engine

> Live: [olalacleaning.com](https://olalacleaning.com/)

<img width="1900" height="1088" alt="image" src="https://github.com/user-attachments/assets/3809b364-6643-470f-acf9-3102b703282b" />


## What it does
A custom WordPress booking system featuring a real-time dynamic pricing matrix, square feet calculation, and secure Stripe integration for the US market.

## Tech Stack
Custom WordPress Theme · PHP 8.x · AJAX · Advanced Custom Fields (ACF Pro) · SCSS · Stripe API

## Key Engineering Decisions
- **AJAX-Powered Pricing Engine:** Engineered a custom calculator that evaluates dozens of user-defined variables (square footage, extra services) via AJAX without page reloads, drastically reducing friction in the conversion funnel.
- **Zero Page Builders:** Built the WordPress theme completely from scratch. Avoiding heavy visual builders (like Elementor) ensured optimal Core Web Vitals and a highly maintainable codebase.
- **Ergonomic Admin Panel:** Structured admin controls via ACF Pro, allowing business owners to easily manage complex pricing matrices and logic just like an Excel spreadsheet.
