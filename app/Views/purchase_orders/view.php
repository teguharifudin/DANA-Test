<?= $this->extend('layouts/main') ?>

<?= $this->section('navbar') ?>
    <?= $this->include('layouts/navbar') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="po-container">
    <div class="po-card">
        <div class="po-header">
            <div class="po-title">
                <h5>Exception Paper</h5>
                <div class="po-subtitle">
                    <?= esc($po['title']) . (!empty($po['po_number']) ? ' / <span class="po-number">' . esc($po['po_number']) . '</span>' : '') ?>
                </div>
            </div>
            <div class="status-badge <?= strtolower($po['status']) ?>">
                <?= ucfirst(esc($po['status'])) ?>
            </div>
        </div>

        <div class="po-content">
            <div class="info-grid">
                <div class="info-section">
                    <div class="info-item">
                        <i class="fas fa-user"></i>
                        <div class="info-details">
                            <label>Requestor</label>
                            <span><?= esc($po['requestor_name']) ?></span>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <div class="info-details">
                            <label>Date of Request</label>
                            <span><?= date('d M Y', strtotime($po['created_at'])) ?></span>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <div class="info-item">
                        <i class="fas fa-money-bill-wave"></i>
                        <div class="info-details">
                            <label>Cost</label>
                            <span class="cost">
                                <?= esc($po['currency']) ?><?= number_format($po['total_amount'], 2) ?>
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div class="info-details">
                            <label>Due Date</label>
                            <span><?= date('d M Y', strtotime($po['due_date'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($po['notes'])): ?>
            <div class="notes-section">
                <h6>
                    <i class="fas fa-exclamation-circle"></i>
                    The reason for not following tender/pitching process
                </h6>
                <div class="notes-content">
                    <?php echo html_entity_decode($po['notes']); ?>
                </div>
                
                <?php if (!empty($po['attachment'])): ?>
                <div class="attachment-section">
                    <?php if (strpos($po['attachment_type'], 'image/') === 0): ?>
                        <div class="image-preview">
                            <img src="<?= base_url('uploads/purchase_orders/' . $po['attachment']) ?>" 
                                 alt="Attachment 1">
                        </div>
                    <?php else: ?>
                        <a href="<?= base_url('uploads/purchase_orders/' . $po['attachment']) ?>" 
                           class="attachment-link" 
                           target="_blank">
                            <i class="fas fa-file-alt"></i>
                            <span>View Attachment 1</span>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($po['notes2'])): ?>
            <div class="notes-section">
                <h6>
                    <i class="fas fa-exclamation-triangle"></i>
                    Impact to DANA as (Organization)
                </h6>
                <div class="notes-content">
                    <?php echo html_entity_decode($po['notes2']); ?>
                </div>
                
                <?php if (!empty($po['attachment2'])): ?>
                <div class="attachment-section">
                    <?php if (strpos($po['attachment_type2'], 'image/') === 0): ?>
                        <div class="image-preview">
                            <img src="<?= base_url('uploads/purchase_orders/' . $po['attachment2']) ?>" 
                                 alt="Attachment 2">
                        </div>
                    <?php else: ?>
                        <a href="<?= base_url('uploads/purchase_orders/' . $po['attachment2']) ?>" 
                           class="attachment-link" 
                           target="_blank">
                            <i class="fas fa-file-alt"></i>
                            <span>View Attachment 2</span>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if ($po['status'] === 'rejected'): ?>
            <div class="approval-section rejected">
                <div class="approval-header">
                    <i class="fas fa-times-circle"></i>
                    <h6>Rejected Information</h6>
                </div>
                <div class="approval-timeline">
                    <?php if (!empty($po['approved_by_manager']) && empty($po['approved_by_management']) && empty($po['approved_by_executive'])): ?>
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Line Manager</h6>
                            <p><?= esc($po['manager_name']) ?></p>
                            <span class="timeline-date">
                                <i class="fas fa-calendar-alt"></i>
                                <?= date('d M Y', strtotime($po['approved_manager_at'])) ?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($po['approved_by_management'])): ?>
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Excom I</h6>
                            <p><?= esc($po['management_name']) ?></p>
                            <span class="timeline-date">
                                <i class="fas fa-calendar-alt"></i>
                                <?= date('d M Y', strtotime($po['approved_management_at'])) ?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($po['approved_by_executive'])): ?>
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Excom II</h6>
                            <p><?= esc($po['executive_name']) ?></p>
                            <span class="timeline-date">
                                <i class="fas fa-calendar-alt"></i>
                                <?= date('d M Y', strtotime($po['approved_executive_at'])) ?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($po['status'] === 'approved' || $po['status'] === 'management' || $po['status'] === 'completed'): ?>
            <div class="approval-section approved">
                <div class="approval-header">
                    <i class="fas fa-check-circle"></i>
                    <h6>Approval Information</h6>
                </div>
                <div class="approval-timeline">
                    <?php if (!empty($po['approved_by_manager'])): ?>
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Line Manager</h6>
                            <p><?= esc($po['manager_name']) ?></p>
                            <span class="timeline-date">
                                <i class="fas fa-calendar-alt"></i>
                                <?= date('d M Y', strtotime($po['approved_manager_at'])) ?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($po['approved_by_management'])): ?>
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Excom I</h6>
                            <p><?= esc($po['management_name']) ?></p>
                            <span class="timeline-date">
                                <i class="fas fa-calendar-alt"></i>
                                <?= date('d M Y', strtotime($po['approved_management_at'])) ?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($po['approved_by_executive'])): ?>
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Excom II</h6>
                            <p><?= esc($po['executive_name']) ?></p>
                            <span class="timeline-date">
                                <i class="fas fa-calendar-alt"></i>
                                <?= date('d M Y', strtotime($po['approved_executive_at'])) ?>
                            </span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-12">
                    <?php
                    $showPdfButton = false;

                    if (
                        $is_value_purchase &&
                        $po['status'] === 'approved'
                    ) {
                        $showPdfButton = true;
                    } elseif (
                        !$is_value_purchase &&
                        $po['status'] === 'completed'
                    ) {
                        $showPdfButton = true;
                    }

                    if ($showPdfButton): ?>
                            <a href="<?= base_url('uploads/purchase_orders/PO_' . $po['id'] . '.pdf') ?>" 
                            class="pdf-button" 
                            target="_blank">
                                <i class="fas fa-file-pdf"></i>
                                <span>Download PDF</span>
                            </a>
                    <?php endif; ?>

                    <?php if ($is_manager && $po['status'] === 'pending'): ?>
                        <div class="btn-group" role="group">
                            <button type="button" 
                                    class="btn btn-success" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#acceptModal">
                                <i class="bi bi-check-circle"></i> Accept
                            </button>
                            <button type="button" 
                                    class="btn btn-warning" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (
                        !$is_value_purchase &&
                        $is_management && $po['status'] === 'approved' ): ?>
                        <div class="btn-group" role="group">
                            <button type="button" 
                                    class="btn btn-success" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#managementModal">
                                <i class="bi bi-check-circle"></i> Accept
                            </button>
                            <button type="button" 
                                    class="btn btn-warning" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if (
                        !$is_value_purchase &&
                        $is_executive && $po['status'] === 'management' ): ?>
                        <div class="btn-group" role="group">
                            <button type="button" 
                                    class="btn btn-success" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#completeModal">
                                <i class="bi bi-check-circle"></i> Accept
                            </button>
                            <button type="button" 
                                    class="btn btn-warning" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Accept Modal -->
<div class="modal fade" id="acceptModal" tabindex="-1" aria-labelledby="acceptModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('purchase-orders/accept/' . $po['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="acceptModalLabel">Accept</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to accept this purchase?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Accept</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('purchase-orders/reject/' . $po['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to reject this purchase?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Management Modal -->
<div class="modal fade" id="managementModal" tabindex="-1" aria-labelledby="managementModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('purchase-orders/management/' . $po['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="managementModalLabel">Management</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to accept this purchase?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Accept</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Complete Modal -->
<div class="modal fade" id="completeModal" tabindex="-1" aria-labelledby="completeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('purchase-orders/complete/' . $po['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="completeModalLabel">Complete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to accept this purchase?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Accept</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
