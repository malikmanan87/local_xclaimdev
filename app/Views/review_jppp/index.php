<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-clipboard2-pulse-fill text-primary me-2"></i>Semakan Permohonan JPPP
        </h4>
        <p class="text-muted small mb-0">
            Modul pengesahan dan penilaian klinikal oleh Jawatankuasa Penilaian & Pengesahan Perkhidmatan Pakar (JPPP).
        </p>
    </div>
    <div>
        <span class="badge bg-white text-dark border px-3 py-2 shadow-sm rounded-pill small">
            <i class="bi bi-calendar3 text-primary me-1"></i> <?= date('d M Y') ?>
        </span>
    </div>
</div>

<!-- 3 KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card stat-card-warning h-100">
            <div class="stat-info">
                <span class="stat-label">Menunggu Semakan JPPP</span>
                <span class="stat-value text-warning-emphasis"><?= number_format($stats['pending']) ?></span>
                <span class="stat-change text-warning-emphasis">
                    <i class="bi bi-hourglass-split me-1"></i>Tindakan segera diperlukan
                </span>
            </div>
            <div class="stat-icon">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card-success h-100">
            <div class="stat-info">
                <span class="stat-label">Disahkan & Disokong</span>
                <span class="stat-value text-success"><?= number_format($stats['approved']) ?></span>
                <span class="stat-change text-success">
                    <i class="bi bi-check2-circle me-1"></i>Disahkan & disokong ke Pengarah
                </span>
            </div>
            <div class="stat-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card-danger h-100">
            <div class="stat-info">
                <span class="stat-label">Ditolak oleh JPPP</span>
                <span class="stat-value text-danger"><?= number_format($stats['rejected']) ?></span>
                <span class="stat-change text-danger">
                    <i class="bi bi-x-circle me-1"></i>Permohonan tidak menepati kriteria
                </span>
            </div>
            <div class="stat-icon">
                <i class="bi bi-exclamation-octagon-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<div class="card-panel">
    <div class="card-panel-header p-0 bg-transparent border-bottom">
        <ul class="nav nav-tabs border-0 px-3 pt-2" id="jpppTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-pane" type="button" role="tab">
                    <i class="bi bi-inbox-fill me-1 text-warning"></i> Menunggu Semakan
                    <span class="badge bg-warning text-dark ms-1 rounded-pill"><?= count($pendingList) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab">
                    <i class="bi bi-archive-fill me-1 text-secondary"></i> Sejarah Semakan JPPP
                    <span class="badge bg-secondary ms-1 rounded-pill"><?= count($processedList) ?></span>
                </button>
            </li>
        </ul>
    </div>

    <div class="card-panel-body p-3">
        <div class="tab-content" id="jpppTabsContent">

            <!-- Tab 1: Pending JPPP Review -->
            <div class="tab-pane fade show active" id="pending-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable small" id="pendingTable">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">No. Permohonan</th>
                                <th>Pakar / Pemohon</th>
                                <th>Pesakit & RN</th>
                                <th width="12%">Tarikh Hantar</th>
                                <th width="14%" class="text-end">Jumlah Bersih (RM)</th>
                                <th width="12%" class="text-center">Status JPPP</th>
                                <th width="12%" class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pendingList)): ?>
                                <?php foreach ($pendingList as $idx => $row): ?>
                                    <tr>
                                        <td class="text-center text-muted"><?= $idx + 1 ?></td>
                                        <td>
                                            <a href="<?= base_url('review-jppp/show/' . $row['id']) ?>" class="font-monospace fw-bold text-primary text-decoration-none">
                                                <?= esc($row['application_no']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc($row['specialist_name']) ?></div>
                                            <small class="text-muted">No. Staf: <?= esc($row['staff_number']) ?> &bull; <?= esc($row['department']) ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc($row['patient_name'] ?? '-') ?></div>
                                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.72rem;">
                                                RN: <?= esc($row['patient_rn'] ?? '-') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= $row['submitted_at'] ? date('d/m/Y h:i A', strtotime($row['submitted_at'])) : date('d/m/Y h:i A', strtotime($row['created_at'])) ?>
                                        </td>
                                        <td class="text-end font-monospace fw-bold text-success fs-6">
                                            RM <?= number_format($row['total_claim'] ?? 0, 2) ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-status badge-status-warning">Menunggu</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('review-jppp/show/' . $row['id']) ?>" class="btn btn-primary btn-sm px-3 shadow-sm">
                                                <i class="bi bi-pencil-square me-1"></i> Semak
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Processed History -->
            <div class="tab-pane fade" id="history-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable small" id="historyTable">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">No. Permohonan</th>
                                <th>Pakar</th>
                                <th>Pesakit</th>
                                <th width="12%" class="text-end">Jumlah Bersih (RM)</th>
                                <th width="12%" class="text-center">Keputusan JPPP</th>
                                <th>Pegawai Penyemak & Tarikh</th>
                                <th width="10%" class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($processedList)): ?>
                                <?php foreach ($processedList as $idx => $row): ?>
                                    <?php $isApp = ($row['jppp_status'] === 'approved'); ?>
                                    <tr>
                                        <td class="text-center text-muted"><?= $idx + 1 ?></td>
                                        <td>
                                            <a href="<?= base_url('review-jppp/show/' . $row['id']) ?>" class="font-monospace fw-bold text-primary text-decoration-none">
                                                <?= esc($row['application_no']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc($row['specialist_name']) ?></div>
                                            <small class="text-muted">Staf: <?= esc($row['staff_number']) ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc($row['patient_name'] ?? '-') ?></div>
                                            <small class="text-muted font-monospace">RN: <?= esc($row['patient_rn'] ?? '-') ?></small>
                                        </td>
                                        <td class="text-end font-monospace fw-bold text-dark">
                                            RM <?= number_format($row['total_claim'] ?? 0, 2) ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-status <?= $isApp ? 'badge-status-success' : 'badge-status-danger' ?>">
                                                <?= $isApp ? 'Disahkan & Disokong' : 'Ditolak' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc($row['jppp_reviewer_name'] ?? 'Pegawai JPPP') ?></div>
                                            <small class="text-muted"><?= $row['jppp_verified_at'] ? date('d/m/Y h:i A', strtotime($row['jppp_verified_at'])) : '-' ?></small>
                                            <?php if (!empty($row['jppp_remarks'])): ?>
                                                <div class="text-muted fst-italic text-truncate" style="max-width: 250px;" title="<?= esc($row['jppp_remarks']) ?>">
                                                    Nota: <?= esc($row['jppp_remarks']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('review-jppp/show/' . $row['id']) ?>" class="btn btn-light btn-sm border px-2.5">
                                                <i class="bi bi-eye me-1"></i> Papar
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
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('.datatable').DataTable({
        pageLength: 10,
        language: {
            emptyTable: '<div class="py-3 text-muted fst-italic"><i class="bi bi-info-circle me-1"></i> Tiada rekod permohonan dijumpai.</div>',
            zeroRecords: "Tiada rekod padanan ditemui",
            search: "_INPUT_",
            searchPlaceholder: "Cari permohonan...",
            lengthMenu: "Papar _MENU_ rekod",
            info: "Memaparkan _START_ hingga _END_ daripada _TOTAL_ permohonan",
            infoEmpty: "Memaparkan 0 hingga 0 daripada 0 permohonan",
            infoFiltered: "(ditapis daripada _MAX_ jumlah rekod)",
            paginate: {
                previous: '<i class="bi bi-chevron-left"></i>',
                next: '<i class="bi bi-chevron-right"></i>'
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
