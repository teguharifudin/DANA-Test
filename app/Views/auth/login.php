<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="login-container">
    <div class="login-card">
        <!-- Card Header -->
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-lock"></i>
            </div>
            <h4>Welcome Back</h4>
            <p>Please sign in to continue</p>
        </div>

        <!-- Alert Messages -->
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo session()->getFlashdata('success'); ?></span>
                <button type="button" class="alert-close" data-bs-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo session()->getFlashdata('error'); ?></span>
                <button type="button" class="alert-close" data-bs-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?= validation_list_errors() ?>

        <!-- Login Form -->
        <?= form_open('login', ['class' => 'login-form']); ?>
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i>
                    Email Address
                </label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       placeholder="Enter your email"
                       required>
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-key"></i>
                    Password
                </label>
                <div class="password-input">
                    <input type="password" 
                           name="password" 
                           id="password" 
                           placeholder="Enter your password"
                           required>
                    <button type="button" class="password-toggle">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="login-button">
                <i class="fas fa-sign-in-alt"></i>
                Sign In
            </button>
        <?= form_close(); ?>

        <!-- Register Link -->
        <div class="register-link">
            <span>Don't have an account?</span>
            <a href="<?php echo base_url('register'); ?>">Register Now</a>
        </div>
    </div>
</div>

<script>
// Password Toggle
document.querySelector('.password-toggle').addEventListener('click', function() {
    const input = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// Alert Close
document.querySelectorAll('.alert-close').forEach(button => {
    button.addEventListener('click', function() {
        this.closest('.alert').remove();
    });
});
</script>
<?= $this->endSection() ?>
