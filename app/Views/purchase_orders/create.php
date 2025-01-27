<?= $this->extend('layouts/main') ?>

<?= $this->section('navbar') ?>
    <?= $this->include('layouts/navbar') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="form-container">
    <form action="<?= site_url('purchase-orders/create') ?>" method="post" enctype="multipart/form-data" id="addPOForm">
        <?= csrf_field() ?>
        
        <div class="form-header">
            <h4>Exception Paper</h4>
            <p class="text-muted">Create a new purchase order exception</p>
        </div>

        <?php if (session()->has('error')) : ?>
            <div class="alert-box">
                <i class="fas fa-exclamation-circle"></i>
                <div class="alert-content">
                    <?= is_array(session('error')) ? implode('<br>', session('error')) : esc(session('error')) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-grid">
            <div class="form-group">
                <label for="title">
                    <i class="fas fa-shopping-cart"></i>
                    Purchase Title
                </label>
                <input type="text" 
                    class="form-input <?= session('errors.title') ? 'is-invalid' : '' ?>" 
                    id="title" 
                    name="title" 
                    required 
                    value="<?= old('title') ?>" 
                    placeholder="Enter purchase title">
                <?php if (session('errors.title')) : ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= session('errors.title') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="po_number">
                    <i class="fas fa-hashtag"></i>
                    PR Number
                </label>
                <input type="text" 
                    class="form-input <?= session('errors.po_number') ? 'is-invalid' : '' ?>" 
                    id="po_number" 
                    name="po_number" 
                    value="<?= old('po_number') ?>">
                <?php if (session('errors.po_number')) : ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= session('errors.po_number') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="notes">
                    <i class="fas fa-comment-alt"></i>
                    The reason for not following tender/pitching process:
                </label>
                <textarea 
                    class="form-input <?= session('errors.notes') ? 'is-invalid' : '' ?>" 
                    id="notes" 
                    name="notes"
                    rows="4"><?= old('notes') ?></textarea>
                <?php if (session('errors.notes')) : ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= session('errors.notes') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="attachment">
                    <i class="fas fa-file-upload"></i>
                    Upload Document/Image
                </label>
                <div class="file-upload-wrapper">
                    <input type="file" 
                        class="form-input file-input <?= session('errors.attachment') ? 'is-invalid' : '' ?>" 
                        id="attachment" 
                        name="attachment"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                    <div class="file-upload-info">
                        <i class="fas fa-info-circle"></i>
                        Accepted files: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG (Max size: 5MB)
                    </div>
                </div>
                <?php if (session('errors.attachment')) : ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= session('errors.attachment') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="notes">
                    <i class="fas fa-comment-alt"></i>
                    Impact to DANA as (Organization) if we are failed to perform the agreement as soon as possible:
                </label>
                <textarea 
                    class="form-input <?= session('errors.notes2') ? 'is-invalid' : '' ?>" 
                    id="notes2" 
                    name="notes2"
                    rows="4"><?= old('notes2') ?></textarea>
                <?php if (session('errors.notes2')) : ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= session('errors.notes2') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="attachment2">
                    <i class="fas fa-file-upload"></i>
                    Upload Document/Image
                </label>
                <div class="file-upload-wrapper">
                    <input type="file" 
                        class="form-input file-input <?= session('errors.attachment2') ? 'is-invalid' : '' ?>" 
                        id="attachment2" 
                        name="attachment2"
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                    <div class="file-upload-info">
                        <i class="fas fa-info-circle"></i>
                        Accepted files: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG (Max size: 5MB)
                    </div>
                </div>
                <?php if (session('errors.attachment2')) : ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= session('errors.attachment2') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="currency">
                    <i class="fas fa-money-bill-wave"></i>
                    Currency
                </label>
                <div class="select-wrapper">
                    <select class="form-input <?= session('errors.currency') ? 'is-invalid' : '' ?>" 
                            id="currency" 
                            name="currency" 
                            onchange="updateCurrencySymbol()" 
                            required>
                        <option value="IDR" <?= old('currency') == 'IDR' ? 'selected' : '' ?>>IDR</option>
                        <option value="USD" <?= old('currency') == 'USD' ? 'selected' : '' ?>>USD</option>
                    </select>
                    <i class="fas fa-chevron-down select-arrow"></i>
                </div>
                <?php if (session('errors.currency')) : ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= session('errors.currency') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="total_amount">
                    <i class="fas fa-coins"></i>
                    Cost
                </label>
                <div class="amount-wrapper">
                    <div class="currency-prefix" id="currencySymbol">Rp</div>
                    <input type="number" 
                        class="form-input amount-input <?= session('errors.total_amount') ? 'is-invalid' : '' ?>" 
                        id="total_amount" 
                        name="total_amount" 
                        step="0.01" 
                        min="0" 
                        required 
                        value="<?= old('total_amount') ?>">
                </div>
                <?php if (session('errors.total_amount')) : ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= session('errors.total_amount') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="due_date">
                    <i class="fas fa-calendar-alt"></i>
                    Due Date
                </label>
                <input type="date" 
                    class="form-input <?= session('errors.due_date') ? 'is-invalid' : '' ?>" 
                    id="due_date" 
                    name="due_date" 
                    required 
                    value="<?= old('due_date') ?>"
                    min="<?= date('Y-m-d') ?>">
                <?php if (session('errors.due_date')) : ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= session('errors.due_date') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group statement-group">
                <div class="statement-wrapper">
                    <label class="checkbox-container">
                        <input type="checkbox" 
                            id="statementAccepted" 
                            name="statement_accepted" 
                            required 
                            <?= old('statement_accepted') ? 'checked' : '' ?>>
                        <span class="checkmark"></span>
                        <span class="statement-text">
                            Hereby I declare that this request to justified the urgency, without any conflict of interest to the selected vendor.
                        </span>
                    </label>
                </div>
                <?php if (session('errors.statement_accepted')) : ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= session('errors.statement_accepted') ?>
                    </div>
                <?php endif; ?>
                <div id="statement-warning" class="error-message" style="display: none;">
                    <i class="fas fa-exclamation-circle"></i>
                    Please check the declaration statement before submitting
                </div>
            </div>
        </div>

        <div class="form-footer">
            <button type="submit" class="submit-po">
                <i class="fas fa-paper-plane"></i>
                Submit
            </button>
        </div>
    </form>
</div>

<script>
    function updateCurrencySymbol() {
        const currency = document.getElementById('currency').value;
        const symbolElement = document.getElementById('currencySymbol');
        
        if (currency === 'USD') {
            symbolElement.textContent = '$';
        } else {
            symbolElement.textContent = 'Rp';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateCurrencySymbol();

        // function generatePONumber() {
        //     const date = new Date();
        //     const year = date.getFullYear().toString().substr(-2);
        //     const month = (date.getMonth() + 1).toString().padStart(2, '0');
        //     const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
        //     return `PO${year}${month}${random}`;
        // }

        // const poNumber = document.getElementById('po_number');
        // if (poNumber && !poNumber.value) {
        //     poNumber.value = generatePONumber();
        // }

        const dueDateInput = document.getElementById('due_date');
        if (dueDateInput) {
            const today = new Date().toISOString().split('T')[0];
            dueDateInput.setAttribute('min', today);
        }

        if (document.getElementById('notes')) {
            CKEDITOR.replace('notes', {
                height: 300,
                removePlugins: 'elementspath',
                filebrowserUploadUrl: '<?= base_url('purchase-orders/upload-image') ?>',
                filebrowserUploadMethod: 'xhr',
                toolbar: [
                    ['Bold', 'Italic', 'Underline', 'Strike'],
                    ['NumberedList', 'BulletedList'],
                    // ['Link', 'Unlink', 'Image'],
                    ['Link', 'Unlink'],
                    // ['Source']
                ],
                image: {
                    toolbar: ['imageStyle:full', 'imageStyle:side', '|', 'imageTextAlternative'],
                    styles: ['full', 'side']
                },
                wordcount: {
                    maxCharCount: 1000
                },
                on: {
                    change: function(evt) {
                        this.updateElement();
                    }
                }
            });
        }

        if (document.getElementById('notes2')) {
            CKEDITOR.replace('notes2', {
                height: 300,
                removePlugins: 'elementspath',
                filebrowserUploadUrl: '<?= base_url('purchase-orders/upload-image') ?>',
                filebrowserUploadMethod: 'xhr',
                toolbar: [
                    ['Bold', 'Italic', 'Underline', 'Strike'],
                    ['NumberedList', 'BulletedList'],
                    // ['Link', 'Unlink', 'Image'],
                    ['Link', 'Unlink'],
                    // ['Source']
                ],
                image: {
                    toolbar: ['imageStyle:full', 'imageStyle:side', '|', 'imageTextAlternative'],
                    styles: ['full', 'side']
                },
                wordcount: {
                    maxCharCount: 1000
                },
                on: {
                    change: function(evt) {
                        this.updateElement();
                    }
                }
            });
        }

        CKEDITOR.on('fileUploadRequest', function(evt) {
            var xhr = evt.data.fileLoader.xhr;
            var data = evt.data.fileLoader.formData;
            
            data.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');
        });
    });

    document.querySelector('.submit-po').addEventListener('click', function(e) {
        const checkbox = document.getElementById('statementAccepted');
        const warningDiv = document.getElementById('statement-warning');
        
        if (!checkbox.checked) {
            e.preventDefault();
            warningDiv.style.display = 'block';
            checkbox.focus();
        }
    });

    document.getElementById('statementAccepted').addEventListener('change', function() {
        const warningDiv = document.getElementById('statement-warning');
        if (this.checked) {
            warningDiv.style.display = 'none';
        }
    });
</script>

<?= $this->endSection() ?>
