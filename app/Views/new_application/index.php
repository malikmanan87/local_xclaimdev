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
                        <th width="50" class="text-center">#</th>
                        <th>App No.</th>
                        <th>Specialist Name</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th width="100" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($applications)): ?>
                        <?php foreach ($applications as $i => $app): ?>
                        <tr>
                            <td class="text-center text-muted fw-medium"><?= $i + 1 ?></td>
                            <td>
                                <span class="fw-medium text-primary"><?= esc($app['application_no']) ?></span>
                            </td>
                            <td>
                                <div class="fw-medium text-dark"><?= esc($app['specialist_name']) ?></div>
                                <div class="text-muted small"><?= esc($app['position']) ?> &bull; <?= esc($app['department']) ?></div>
                            </td>
                            <td class="text-secondary small"><?= esc($app['department']) ?></td>
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
                                <a href="<?= base_url('new-application/show/' . $app['id']) ?>"
                                   class="btn-action btn-action-view" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
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
        columnDefs: [{ orderable: false, targets: [0, 6] }]
    });
});
</script>
<?= $this->endSection() ?>
