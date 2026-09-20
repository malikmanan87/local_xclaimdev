<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
// Bina parameter URL untuk butang Eksport & Cetak
$queryParams = http_build_query(array_filter($filters ?? []));
$exportUrl   = base_url('reports/export' . ($queryParams ? '?' . $queryParams : ''));
$printUrl    = base_url('reports/print'  . ($queryParams ? '?' . $queryParams : ''));
?>

<!-- Halaman Utama Laporan -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-file-earmark-bar-graph-fill text-primary me-2"></i>Laporan Tuntutan Perkhidmatan Pakar
        </h4>
        <div class="text-muted small">
            Penyata analisis dan rekod kewangan tuntutan prosedur perubatan Hospital Pengajar UniSZA (HPUniSZA).
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= $exportUrl ?>" class="btn btn-outline-success btn-sm px-3 shadow-sm rounded-2" title="Muat Turun Excel/CSV">
            <i class="bi bi-file-earmark-excel-fill me-1"></i> Eksport CSV
        </a>
        <a href="<?= $printUrl ?>" target="_blank" class="btn btn-outline-secondary btn-sm px-3 shadow-sm rounded-2" title="Cetak Format Rasmi">
            <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
        </a>
    </div>
</div>

<!-- Panel Penapis (Filter Bar) -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <span class="fw-bold text-dark small">
            <i class="bi bi-funnel-fill text-primary me-1"></i> Penapis Data Laporan
        </span>
        <?php if (!empty(array_filter($filters))): ?>
            <span class="badge bg-primary bg-opacity-10 text-primary small">Penapis Aktif</span>
        <?php endif; ?>
    </div>
    <div class="card-body p-3 bg-light">
        <form action="<?= base_url('reports') ?>" method="GET" autocomplete="off" id="reportFilterForm">
            <div class="row g-2.5">

                <!-- Tarikh Mula -->
                <div class="col-md-3 col-sm-6">
                    <label for="start_date" class="form-label text-muted small fw-semibold mb-1">Tarikh Mula</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted"><i class="bi bi-calendar-event"></i></span>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="<?= esc($filters['start_date'] ?? '') ?>">
                    </div>
                </div>

                <!-- Tarikh Akhir -->
                <div class="col-md-3 col-sm-6">
                    <label for="end_date" class="form-label text-muted small fw-semibold mb-1">Tarikh Akhir</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted"><i class="bi bi-calendar-check"></i></span>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="<?= esc($filters['end_date'] ?? '') ?>">
                    </div>
                </div>

                <!-- Jabatan / Disiplin -->
                <div class="col-md-3 col-sm-6">
                    <label for="department" class="form-label text-muted small fw-semibold mb-1">Jabatan / Disiplin</label>
                    <select class="form-select form-select-sm" id="department" name="department">
                        <option value="">-- Semua Jabatan --</option>
                        <?php if (!empty($departments)): ?>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= esc($dept) ?>" <?= (isset($filters['department']) && $filters['department'] === $dept) ? 'selected' : '' ?>>
                                    <?= esc($dept) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Status Permohonan -->
                <div class="col-md-3 col-sm-6">
                    <label for="status" class="form-label text-muted small fw-semibold mb-1">Status Permohonan</label>
                    <select class="form-select form-select-sm" id="status" name="status">
                        <option value="">-- Semua Status --</option>
                        <option value="submitted" <?= (isset($filters['status']) && $filters['status'] === 'submitted') ? 'selected' : '' ?>>Menunggu Semakan</option>
                        <option value="under_review" <?= (isset($filters['status']) && $filters['status'] === 'under_review') ? 'selected' : '' ?>>Dalam Semakan (JPPP Sokong)</option>
                        <option value="approved" <?= (isset($filters['status']) && $filters['status'] === 'approved') ? 'selected' : '' ?>>Diluluskan (Sedia Bayar)</option>
                        <option value="rejected" <?= (isset($filters['status']) && $filters['status'] === 'rejected') ? 'selected' : '' ?>>Ditolak</option>
                        <option value="draft" <?= (isset($filters['status']) && $filters['status'] === 'draft') ? 'selected' : '' ?>>Draf</option>
                    </select>
                </div>

                <!-- Status Semakan JPPP -->
                <div class="col-md-3 col-sm-6">
                    <label for="jppp_status" class="form-label text-muted small fw-semibold mb-1">Semakan JPPP</label>
                    <select class="form-select form-select-sm" id="jppp_status" name="jppp_status">
                        <option value="">-- Semua Status JPPP --</option>
                        <option value="pending" <?= (isset($filters['jppp_status']) && $filters['jppp_status'] === 'pending') ? 'selected' : '' ?>>Menunggu Perakuan</option>
                        <option value="approved" <?= (isset($filters['jppp_status']) && $filters['jppp_status'] === 'approved') ? 'selected' : '' ?>>Disokong</option>
                        <option value="rejected" <?= (isset($filters['jppp_status']) && $filters['jppp_status'] === 'rejected') ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>

                <!-- Status Semakan Kewangan -->
                <div class="col-md-3 col-sm-6">
                    <label for="finance_status" class="form-label text-muted small fw-semibold mb-1">Kelulusan Kewangan</label>
                    <select class="form-select form-select-sm" id="finance_status" name="finance_status">
                        <option value="">-- Semua Status Kewangan --</option>
                        <option value="pending" <?= (isset($filters['finance_status']) && $filters['finance_status'] === 'pending') ? 'selected' : '' ?>>Menunggu Kelulusan</option>
                        <option value="approved" <?= (isset($filters['finance_status']) && $filters['finance_status'] === 'approved') ? 'selected' : '' ?>>Diluluskan (Ada Baucar)</option>
                        <option value="rejected" <?= (isset($filters['finance_status']) && $filters['finance_status'] === 'rejected') ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>

                <!-- Carian Kata Kunci / No. Rujukan / Pakar / RN -->
                <div class="col-md-4 col-sm-8">
                    <label for="search" class="form-label text-muted small fw-semibold mb-1">Carian Terperinci</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="<?= esc($filters['search'] ?? '') ?>" 
                               placeholder="No. Permohonan, Pakar, No. Staf, RN...">
                    </div>
                </div>

                <!-- Butang Tindakan -->
                <div class="col-md-2 col-sm-4 d-flex align-items-end gap-1">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1 shadow-sm">
                        <i class="bi bi-funnel me-1"></i> Tapis
                    </button>
                    <a href="<?= base_url('reports') ?>" class="btn btn-outline-secondary btn-sm" title="Set Semula Penapis">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- 4 Kad Metrik Ringkasan Kewangan (Financial KPI) -->
<div class="row g-3 mb-4">
    <!-- Bilangan Permohonan -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-primary fw-semibold small">Bilangan Permohonan</span>
                <div class="rounded-circle bg-primary bg-opacity-10 p-2 text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-files fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-0"><?= number_format($summary['total_records'] ?? 0) ?></h3>
            <small class="text-muted mt-1">Keseluruhan Kes Tuntutan</small>
        </div>
    </div>

    <!-- Jumlah Kasar Tuntutan -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-purple fw-semibold small" style="color: #7c3aed;">Jumlah Kasar Dituntut</span>
                <div class="rounded-circle bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                    <i class="bi bi-calculator-fill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-0">RM <?= number_format($summary['total_gross'] ?? 0, 2) ?></h3>
            <small class="text-muted mt-1">Nilai Fi Surgeri/Bius Penuh</small>
        </div>
    </div>

    <!-- Tabung Kebajikan Hospital -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-warning fw-semibold small" style="color: #d97706;">Tabung Kebajikan (5%)</span>
                <div class="rounded-circle bg-warning bg-opacity-10 p-2 text-warning d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-heart-pulse-fill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-0">RM <?= number_format($summary['total_welfare'] ?? 0, 2) ?></h3>
            <small class="text-muted mt-1">Sumbangan Hospital</small>
        </div>
    </div>

    <!-- Jumlah Bersih Bayaran Pakar -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-success fw-semibold small">Jumlah Bersih Pakar</span>
                <div class="rounded-circle bg-success bg-opacity-10 p-2 text-success d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-cash-stack fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-success mb-0">RM <?= number_format($summary['total_claim'] ?? 0, 2) ?></h3>
            <small class="text-muted mt-1">Amaun Bersih Layak Bayar</small>
        </div>
    </div>
</div>

<!-- Pecahan Status Permohonan (Breakdown Badges) -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body py-2 px-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <span class="text-muted small fw-semibold">Pecahan Status:</span>
        <div class="d-flex flex-wrap align-items-center gap-2 small">
            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-50 px-2.5 py-1.5">
                <i class="bi bi-hourglass-split me-1 text-warning"></i> Menunggu JPPP: <strong><?= $summary['count_submitted'] ?? 0 ?></strong>
            </span>
            <span class="badge bg-info bg-opacity-10 text-info-emphasis border border-info border-opacity-50 px-2.5 py-1.5">
                <i class="bi bi-shield-check me-1 text-info"></i> Dalam Semakan Kewangan: <strong><?= $summary['count_under_review'] ?? 0 ?></strong>
            </span>
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-50 px-2.5 py-1.5">
                <i class="bi bi-check2-circle me-1 text-success"></i> Diluluskan Bayaran: <strong><?= $summary['count_approved'] ?? 0 ?></strong>
            </span>
            <?php if (!empty($summary['count_rejected'])): ?>
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-50 px-2.5 py-1.5">
                    <i class="bi bi-x-circle me-1 text-danger"></i> Ditolak: <strong><?= $summary['count_rejected'] ?></strong>
                </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Jadual Perincian Laporan (DataTable) -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="card-title fw-bold mb-0 text-dark">
            <i class="bi bi-table text-primary me-2"></i>Perincian Rekod Tuntutan
        </h6>
        <span class="badge bg-light text-secondary border small">
            <?= count($reportData ?? []) ?> Rekod Ditemui
        </span>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table id="reportsTable" class="table table-hover align-middle w-100" style="font-size: 0.84rem;">
                <thead class="table-light text-secondary">
                    <tr>
                        <th width="40" class="text-center">#</th>
                        <th width="140">No. Permohonan</th>
                        <th width="110">Tarikh</th>
                        <th>Pakar & Jabatan</th>
                        <th>Pesakit (RN)</th>
                        <th width="110" class="text-end">Kasar (RM)</th>
                        <th width="100" class="text-end">Tabung (RM)</th>
                        <th width="110" class="text-end">Bersih (RM)</th>
                        <th width="110" class="text-center">Semakan</th>
                        <th width="120" class="text-center">Status</th>
                        <th width="60" class="text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reportData) && is_array($reportData)): ?>
                        <?php foreach ($reportData as $idx => $row): ?>
                            <tr>
                                <td class="text-center text-muted fw-medium"><?= $idx + 1 ?></td>
                                <td>
                                    <a href="<?= base_url('new-application/show/' . $row['id']) ?>" 
                                       class="font-monospace fw-bold text-primary text-decoration-none"
                                       title="Lihat Perincian Permohonan">
                                        <?= esc($row['application_no']) ?>
                                    </a>
                                    <?php if (!empty($row['finance_voucher_no'])): ?>
                                        <div class="text-success small font-monospace mt-0.5" style="font-size: 0.72rem;">
                                            <i class="bi bi-receipt me-0.5"></i><?= esc($row['finance_voucher_no']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-muted">
                                    <?= !empty($row['created_at']) ? date('d/m/Y', strtotime($row['created_at'])) : '-' ?>
                                    <div class="small text-muted" style="font-size: 0.7rem;">
                                        <?= !empty($row['created_at']) ? date('h:i A', strtotime($row['created_at'])) : '' ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($row['specialist_name']) ?></div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">
                                        No. Staf: <code><?= esc($row['staff_number']) ?></code> &bull; <?= esc($row['department']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($row['patient_name']) ?></div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">
                                        RN: <code><?= esc($row['patient_rn']) ?></code> &bull; Visit: <?= esc($row['visit_id']) ?>
                                    </div>
                                </td>
                                <td class="text-end font-monospace text-secondary">
                                    <?= number_format((float)($row['total_gross'] ?? 0), 2) ?>
                                </td>
                                <td class="text-end font-monospace text-warning">
                                    <?= number_format((float)($row['total_welfare'] ?? 0), 2) ?>
                                </td>
                                <td class="text-end font-monospace fw-bold text-success">
                                    <?= number_format((float)($row['total_claim'] ?? 0), 2) ?>
                                </td>
                                <td class="text-center">
                                    <!-- JPPP Badge -->
                                    <?php if (($row['jppp_status'] ?? '') === 'approved'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.65rem;" title="Disokong JPPP">JPPP: OK</span>
                                    <?php elseif (($row['jppp_status'] ?? '') === 'rejected'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" style="font-size: 0.65rem;" title="Ditolak JPPP">JPPP: Tolak</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25" style="font-size: 0.65rem;" title="Menunggu JPPP">JPPP: Menunggu</span>
                                    <?php endif; ?>

                                    <!-- Kewangan Badge -->
                                    <div class="mt-0.5">
                                        <?php if (($row['finance_status'] ?? '') === 'approved'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.65rem;" title="Diluluskan Kewangan">KEW: Lulus</span>
                                        <?php elseif (($row['finance_status'] ?? '') === 'rejected'): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25" style="font-size: 0.65rem;" title="Ditolak Kewangan">KEW: Tolak</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25" style="font-size: 0.65rem;" title="Menunggu Kewangan">KEW: Menunggu</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $st = $row['status'] ?? 'draft';
                                    if ($st === 'approved') {
                                        echo '<span class="badge bg-success px-2 py-1"><i class="bi bi-check2-circle me-1"></i>Diluluskan</span>';
                                    } elseif ($st === 'under_review') {
                                        echo '<span class="badge bg-info text-white px-2 py-1"><i class="bi bi-shield-check me-1"></i>Dalam Semakan</span>';
                                    } elseif ($st === 'submitted') {
                                        echo '<span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-hourglass-split me-1"></i>Dihantar</span>';
                                    } elseif ($st === 'rejected') {
                                        echo '<span class="badge bg-danger px-2 py-1"><i class="bi bi-x-circle me-1"></i>Ditolak</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary px-2 py-1">Draf</span>';
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('new-application/show/' . $row['id']) ?>" 
                                       class="btn btn-outline-primary btn-sm py-0.5 px-2" 
                                       title="Buka Permohonan">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#reportsTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        order: [[2, 'desc']], // Susun mengikut Tarikh terkini
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Cari dalam jadual...",
            lengthMenu: "Papar _MENU_ rekod",
            info: "Memaparkan _START_ hingga _END_ daripada _TOTAL_ rekod",
            infoEmpty: "Tiada rekod laporan dijumpai",
            zeroRecords: "Tiada rekod sepadan dengan tapisan",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            }
        },
        columnDefs: [
            { orderable: false, targets: [0, 8, 10] }
        ]
    });
});
</script>

<?= $this->endSection() ?>