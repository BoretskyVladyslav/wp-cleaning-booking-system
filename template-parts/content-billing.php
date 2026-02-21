<section class="billing-section">
    <div class="container">
        <div class="cabinet-greeting">
            <h1>Доброго вечора! <strong>Vlad Vladyslav</strong></h1>
        </div>
        <div class="profile-card billing-profile-card">
            <div class="profile-left">
                <div class="avatar-circle">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
            <div class="profile-middle">
                <h2 class="user-fullname">Vlad Vladyslav</h2>
                <p class="user-email">boretskyvladyslav@gmail.com</p>
            </div>
            <div class="profile-right">
                <a href="<?php echo site_url('/edit-profile'); ?>" class="profile-btn btn-blue" style="text-decoration:none;">
                    Редагувати профіль
                </a>
                <button class="profile-btn btn-grey btn-change-password" id="btn-change-password-billing">
                    Змінити пароль
                </button>
            </div>
        </div>
        <div class="billing-card address-card">
            <div class="card-header">
                <div class="card-title-wrap">
                    <i class="fa-solid fa-circle-info info-icon"></i>
                    <h2>Адреси для прибирання</h2>
                </div>
                <button class="btn btn--primary btn-add-new">
                    Додати нову
                </button>
            </div>
            <div class="card-content">
                <p class="placeholder-text">Адреси ще не додані.</p>
            </div>
        </div>
        <div class="billing-card payment-card">
            <div class="card-header">
                <div class="card-title-wrap">
                    <i class="fa-solid fa-circle-info info-icon"></i>
                    <h2>Платіжна інформація</h2>
                </div>
                <button class="btn btn--primary btn-add-new">
                    Додати картку
                </button>
            </div>
            <div class="card-content">
                <p class="placeholder-text">Способи оплати не прив'язані.</p>
            </div>
        </div>
    </div>
</section>