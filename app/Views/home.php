<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<section class="hero">
    <div class="hero-container">
        <div class="hero-content">
            <div class="hero-badge">Welcome to</div>
            <h1 class="hero-title">DANA TEST</h1>
            <p class="hero-description">
                Exception Paper uses PHP CodeIgniter 4, JavaScript, Bootstrap 5, CKEditor, TCPDF, and MySQL. The system fills the input on the form, then signs based on role, department, and division, and provides email notifications and PDF attachments via Mailtrap.
            </p>
            <div class="hero-buttons">
                <a href="<?php echo base_url('login'); ?>" class="btn-login">
                    <span class="btn-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </span>
                    <span class="btn-text">Login</span>
                </a>
                <a href="<?php echo base_url('register'); ?>" class="btn-register">
                    <span class="btn-icon">
                        <i class="fas fa-user-plus"></i>
                    </span>
                    <span class="btn-text">Register</span>
                </a>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
