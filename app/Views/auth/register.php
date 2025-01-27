<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="register-container">
    <div class="register-card">
        <div class="register-header">
            <div class="header-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <h4>Create Account</h4>
            <p>Join our platform</p>
        </div>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo session()->getFlashdata('error'); ?></span>
                <button type="button" class="alert-close" data-bs-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (validation_list_errors()): ?>
            <div class="validation-errors">
                <div class="validation-errors-header">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Please fix the following errors:</span>
                </div>
                <?= validation_list_errors() ?>
            </div>
        <?php endif; ?>

        <?= form_open('register', ['class' => 'register-form']); ?>
            <div class="form-group">
                <label for="name">
                    <i class="fas fa-user"></i>
                    Full Name
                </label>
                <input type="text" name="name" id="name" required placeholder="Enter your name">
            </div>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i>
                    Email Address
                </label>
                <input type="email" name="email" id="email" required placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i>
                    Password
                </label>
                <div class="password-input">
                    <input type="password" name="password" id="password" required placeholder="Create password">
                    <button type="button" class="password-toggle">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label for="role">
                    <i class="fas fa-user-tag"></i>
                    Role
                </label>
                <div class="select-wrapper">
                    <select name="role" id="role" required>
                        <option value="">Select Role</option>
                        <?php foreach ($roles as $key => $value): ?>
                            <option value="<?= $key ?>" <?= old('role') == $key ? 'selected' : '' ?>>
                                <?= $value ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down select-arrow"></i>
                </div>
            </div>

            <div class="form-group" id="department-group">
                <label for="department">
                    <i class="fas fa-building"></i>
                    Department
                </label>
                <div class="select-wrapper">
                    <select name="department" id="department" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $key => $value): ?>
                            <option value="<?= $key ?>" <?= old('department') == $key ? 'selected' : '' ?>>
                                <?= $value ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down select-arrow"></i>
                </div>
            </div>

            <div class="form-group" id="division-group">
                <label for="division">
                    <i class="fas fa-layer-group"></i>
                    Division
                </label>
                <div class="select-wrapper">
                    <select name="division" id="division" required>
                        <option value="">Select Division</option>
                        <?php foreach ($divisions as $key => $value): ?>
                            <option value="<?= $key ?>" <?= old('division') == $key ? 'selected' : '' ?>>
                                <?= $value ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down select-arrow"></i>
                </div>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-user-plus"></i>
                Create Account
            </button>
        <?= form_close(); ?>

        <div class="login-link">
            <span>Already have an account?</span>
            <a href="<?php echo base_url('login'); ?>">Sign In</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const departmentGroup = document.getElementById('department-group');
    const divisionGroup = document.getElementById('division-group');
    const departmentSelect = document.getElementById('department');
    const divisionSelect = document.getElementById('division');
    const passwordToggle = document.querySelector('.password-toggle');
    const passwordInput = document.getElementById('password');

    const divisionsByDepartment = {
        'Business': ['Sales', 'Marketing'],
        'General': ['HR', 'Finance'],
        'Operational': ['IT', 'Operations', 'Other']
    };

    passwordToggle.addEventListener('click', function() {
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });

    roleSelect.addEventListener('change', function() {
        handleFieldsVisibility(this.value);
    });

    departmentSelect.addEventListener('change', function() {
        updateDivisionOptions(this.value);
    });

    handleFieldsVisibility(roleSelect.value);
    updateDivisionOptions(departmentSelect.value);

    function handleFieldsVisibility(role) {
        switch(role) {
            case 'executive':
                hideFields();
                break;
            case 'management':
                showDepartment();
                hideDivision();
                break;
            case 'manager':
            case 'requestor':
            case 'procurement':
                showAllFields();
                break;
            default:
                hideFields();
        }
    }

    function hideFields() {
        departmentGroup.classList.add('hidden');
        divisionGroup.classList.add('hidden');
        departmentSelect.removeAttribute('required');
        divisionSelect.removeAttribute('required');
    }

    function showDepartment() {
        departmentGroup.classList.remove('hidden');
        departmentSelect.setAttribute('required', 'required');
    }

    function showAllFields() {
        departmentGroup.classList.remove('hidden');
        divisionGroup.classList.remove('hidden');
        departmentSelect.setAttribute('required', 'required');
        divisionSelect.setAttribute('required', 'required');
    }

    function hideDivision() {
        divisionGroup.classList.add('hidden');
        divisionSelect.removeAttribute('required');
    }

    function updateDivisionOptions(department) {
        divisionSelect.innerHTML = '<option value="">Select Division</option>';
        
        if (department && divisionsByDepartment[department]) {
            divisionsByDepartment[department].forEach(division => {
                const option = document.createElement('option');
                option.value = division;
                option.textContent = division;
                divisionSelect.appendChild(option);
            });
        }
    }
});
</script>

<?= $this->endSection() ?>
