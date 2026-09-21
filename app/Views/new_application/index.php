<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card-panel">
    <div class="card-panel-header py-3">
        <h5 class="card-panel-title">
            <i class="bi bi-file-earmark-medical-fill me-2 text-primary"></i>New Application
        </h5>
        <a href="<?= base_url('new-application/create') ?>" class="btn btn-primary btn-sm shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> New Application
        </a>
    </div>
    <div class="card-panel-body">
        <div class="table-responsive">
            <table id="applicationsTable" class="table table-hover table-align-middle w-100">
                <thead class="table-light text-secondary">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th>App No.</th>
                        <th>Specialist</th>
                        <th>Patient</th>
                        <th class="text-end">Total Claim (RM)</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th width="80" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($applications)): ?>
                        <?php foreach ($applications as $i => $app): ?>
                        <tr>
                            <td class="text-center text-muted fw-medium"><?= $i + 1 ?></td>
                            <td>
                                <span class="fw-medium text-primary font-monospace"><?= esc($app['application_no']) ?></span>
                            </td>
                            <td>
                                <div class="fw-medium text-dark"><?= esc($app['specialist_name']) ?></div>
                                <div class="text-muted small"><?= esc($app['position']) ?> &bull; <?= esc($app['department']) ?></div>
                            </td>
                            <td>
                                <div class="fw-medium text-dark"><?= esc($app['patient_name'] ?? '-') ?></div>
                                <div class="text-muted small font-monospace"><?= esc($app['patient_rn'] ?? '-') ?></div>
                            </td>
                            <td class="text-end font-monospace fw-bold text-success">
                                RM <?= number_format($app['total_claim'] ?? 0, 2) ?>
                            </td>
                            <td>
                                <?php
                                $statusMap = [
                                    'draft'        => ['badge-status-secondary', 'Draft'],
                                    'submitted'    => ['badge-status-warning',   'Submitted'],
                                    'under_review' => ['badge-status-info',      'Under Review'],
                                    'approved'     => ['badge-status-success',   'Approved'],
                                    'rejected'     => ['badge-status-danger',    'Rejected'],
                                ];
                                $s = $statusMap[$app['status']] ?? ['badge-status-secondary', ucfirst($app['status'])];
                                ?>
                                <span class="badge-status <?= $s[0] ?> py-1"><?= $s[1] ?></span>
                            </td>
                            <td class="text-secondary small">
                                <?= $app['submitted_at'] ? date('d/m/Y h:i A', strtotime($app['submitted_at'])) : '<span class="text-muted fst-italic">—</span>' ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <a href="<?= base_url('new-application/show/' . $app['id']) ?>"
                                       class="btn-action btn-action-view" title="Lihat Perincian">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php 
                                    $isEditable = ($app['status'] === 'submitted' && 
                                                   ($app['jppp_status'] ?? 'pending') === 'pending' && 
                                                   ($app['finance_status'] ?? 'pending') === 'pending');
                                    if ($isEditable): 
                                    ?>
                                    <a href="<?= base_url('new-application/edit/' . $app['id']) ?>"
                                       class="btn-action text-warning" title="Edit Permohonan">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function () {
    $('#applicationsTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 10,
        columnDefs: [{ orderable: false, targets: [0, 7] }]
    });
});
</script>
<?= $this->endSection() ?>
