<?= $this->extend('layouts/main') ?>

<?= $this->section('navbar') ?>
    <?= $this->include('layouts/navbar') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-card">
                <div class="card-content">
                    <div class="welcome-header">
                        <div class="user-avatar">
                            <?= strtoupper(substr($user['name'], 0, 2)) ?>
                        </div>
                        <div class="welcome-text">
                            <h5>Welcome back,</h5>
                            <h4><?= esc($user['name']) ?></h4>
                        </div>
                    </div>
                    <div class="user-info">
                        <span class="badge role">
                            <i class="fas fa-user-shield"></i>
                            <?= esc(ucfirst($user['role'])) ?>
                        </span>
                        <span class="badge department">
                            <i class="fas fa-building"></i>
                            <?= esc($user['department']) ?>
                        </span>
                        <span class="badge division">
                            <i class="fas fa-sitemap"></i>
                            <?= esc($user['division']) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="dashboard-container">
        <?php if (session()->get('role') === 'requestor') : ?>
        <div class="dashboard-header" style="display: flex; justify-content: flex-end;">
            <a href="<?= base_url('purchase-orders/new') ?>" class="add-button">
                <i class="fas fa-plus"></i>
                <span>Add New</span>
            </a>
        </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon total">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Total</span>
                    <h3 class="stat-value"><?= $request_status['total'] ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Pending</span>
                    <h3 class="stat-value"><?= $request_status['pending'] ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon approved">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Approved/Management/Completed</span>
                    <h3 class="stat-value"><?= $request_status['approved'] ?></h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon rejected">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Rejected</span>
                    <h3 class="stat-value"><?= $request_status['rejected'] ?></h3>
                </div>
            </div>
        </div>

        <div class="table-section">
            <div class="filter-section">
                <h5 class="section-title">Request List</h5>
                <form action="<?= current_url() ?>" method="get" class="search-filters">
                    <div class="input-group">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" 
                            name="search" 
                            id="searchInput" 
                            placeholder="Search..." 
                            class="search-input"
                            value="<?= esc($search ?? '') ?>">
                    </div>
                    
                    <div class="filter-group">
                        <select name="currency" id="currencyFilter" class="filter-select">
                            <option value="">All Currencies</option>
                            <?php foreach ($currencies as $curr): ?>
                                <option value="<?= esc($curr['currency']) ?>" 
                                    <?= ($selectedCurrency ?? '') === $curr['currency'] ? 'selected' : '' ?>>
                                    <?= esc($curr['currency']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <select name="status" id="statusFilter" class="filter-select">
                            <option value="">All Status</option>
                            <?php foreach ($statuses as $stat): ?>
                                <option value="<?= esc($stat['status']) ?>"
                                    <?= ($selectedStatus ?? '') === $stat['status'] ? 'selected' : '' ?>>
                                    <?= ucfirst(esc($stat['status'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="button-group">
                        <button type="submit" class="filter-button">Apply Filters</button>
                        <a href="<?= current_url() ?>" class="reset-button">Reset</a>
                    </div>
                </form>
            </div>

            <div class="table-container">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Purchase Title</th>
                            <th>Date</th>
                            <th>Currency</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($purchase_orders) && !empty($purchase_orders)) : ?>
                            <?php foreach ($purchase_orders as $po) : ?>
                                <tr>
                                    <td>
                                        <span class="po-title"><?= esc($po['title']) ?></span>
                                    </td>
                                    <td><?= date('d M Y', strtotime($po['created_at'])) ?></td>
                                    <td><?= esc($po['currency']) ?></td>
                                    <td class="amount">
                                        <?= number_format($po['total_amount'], 2) ?>
                                    </td>
                                    <td><?= date('d M Y', strtotime($po['due_date'])) ?></td>
                                    <td>
                                        <span class="status-badge <?= strtolower($po['status']) ?>">
                                            <?= ucfirst(esc($po['status'])) ?>
                                            <?php 
                                                $is_value_purchase = ($po['currency'] === 'IDR' && $po['total_amount'] < 200000001) || 
                                                                    ($po['currency'] === 'USD' && $po['total_amount'] < 12368);
                                            ?>
                                            <?= !$is_value_purchase ? '- EXECUTIVE' : '' ?>
                                        </span>
                                        
                                    </td>
                                    <td>
                                        <a href="<?= site_url('purchase-orders/view/' . $po['id']) ?>" 
                                        class="action-button">
                                            <i class="fas fa-eye"></i>
                                            <span>View</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <div class="empty-state-content">
                                        <i class="fas fa-inbox"></i>
                                        <p>No purchase orders found</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                <div class="pagination-info">
                    <i class="fas fa-list"></i>
                    <span>
                        Showing 
                        <strong><?= ($pager->getCurrentPage() - 1) * $pager->getPerPage() + 1 ?></strong> 
                        to 
                        <strong><?= min($pager->getCurrentPage() * $pager->getPerPage(), $pager->getTotal()) ?></strong> 
                        of 
                        <strong><?= $pager->getTotal() ?></strong> 
                        entries
                    </span>
                </div>
                <?php if ($pager->getPageCount() > 1) : ?>
                    <?= $pager->links() ?>
                <?php endif; ?>
                
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.search-filters');
    const filterButton = form.querySelector('.filter-button');
    const inputs = form.querySelectorAll('input, select');

    form.addEventListener('submit', function() {
        filterButton.style.position = 'relative';
        filterButton.innerHTML = `
            <span style="opacity: 0">Apply Filters</span>
            <div class="button-loader"></div>
        `;
    });

    let initialState = Array.from(inputs).map(input => input.value);
    
    function checkFormChanges() {
        let currentState = Array.from(inputs).map(input => input.value);
        let hasChanges = initialState.some((value, index) => value !== currentState[index]);
        
        filterButton.disabled = !hasChanges;
        filterButton.style.opacity = hasChanges ? '1' : '0.5';
    }

    inputs.forEach(input => {
        input.addEventListener('input', checkFormChanges);
        input.addEventListener('change', checkFormChanges);
    });
});
</script>

<?= $this->endSection() ?>
