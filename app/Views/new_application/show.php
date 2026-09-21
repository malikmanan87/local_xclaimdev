<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$statusMap = [
    'draft'        => ['badge-status-secondary', 'Draft'],
    'submitted'    => ['badge-status-warning',   'Submitted'],
    'under_review' => ['badge-status-info',      'Under Review'],
    'approved'     => ['badge-status-success',   'Approved'],
    'rejected'     => ['badge-status-danger',    'Rejected'],
];
$s = $statusMap[$application['status']] ?? ['badge-status-secondary', ucfirst($application['status'])];
$procedures = !empty($application['procedures_data']) ? json_decode($application['procedures_data'], true) : [];
?>

<div class="card-panel">
    <div class="card-panel-header py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-panel-title mb-1">
                <i class="bi bi-file-earmark-medical me-2 text-primary"></i>
                Butiran Permohonan: <span class="font-monospace text-primary"><?= esc($application['application_no']) ?></span>
            </h5>
            <div class="text-muted small">
                Dihantar pada: <?= $application['submitted_at'] ? date('d/m/Y h:i A', strtotime($application['submitted_at'])) : '-' ?>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge-status <?= $s[0] ?> fs-6 py-1 px-3"><?= $s[1] ?></span>
            <?php 
            $isEditable = ($application['status'] === 'submitted' && 
                           ($application['jppp_status'] ?? 'pending') === 'pending' && 
                           ($application['finance_status'] ?? 'pending') === 'pending');
            $currentUserRole = session('role_name') ?? session('role') ?? '';
            $isOwnerOrStaff  = ($application['submitted_by'] == session('user_id')) || in_array($currentUserRole, ['admin', 'manager', 'pegawai_penyemak_pe', 'pegawai_perkhidmatan_pe', 'ketua_j3p', 'pengarah']);
            if ($isEditable && $isOwnerOrStaff): 
            ?>
            <a href="<?= base_url('new-application/edit/' . $application['id']) ?>" class="btn btn-warning btn-sm shadow-sm">
                <i class="bi bi-pencil-square me-1"></i> Edit Permohonan
            </a>
            <?php endif; ?>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Cetak
            </button>
            <a href="<?= base_url('new-application') ?>" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card-panel-body">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <!-- Official Print Header (Print Only) -->
        <div class="d-none d-print-block text-center border-bottom pb-3 mb-4">
            <h4 class="fw-bold mb-1">HOSPITAL PENGAJAR UNIVERSITI SULTAN ZAINAL ABIDIN (HPUniSZA)</h4>
            <h6 class="text-secondary mb-1">BORANG PERMOHONAN TUNTUTAN PROSEDUR PAKAR</h6>
            <div class="small text-muted">No. Rujukan: <strong><?= esc($application['application_no']) ?></strong> &bull; Tarikh Cetakan: <?= date('d/m/Y h:i A') ?></div>
        </div>

        <!-- Info Cards: Specialist & Patient -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 bg-light h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="badge bg-primary p-2 me-2 rounded-circle">
                                <i class="bi bi-person-badge text-white"></i>
                            </div>
                            <span class="fw-bold small text-uppercase text-secondary">Maklumat Pakar</span>
                        </div>
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr>
                                <th width="35%" class="text-muted">Nama Pakar:</th>
                                <td class="fw-semibold"><?= esc($application['specialist_name']) ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">No. Staf:</th>
                                <td class="font-monospace"><?= esc($application['staff_number']) ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Emel:</th>
                                <td><?= esc($application['email']) ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Jabatan / Jawatan:</th>
                                <td><?= esc($application['department']) ?> &bull; <?= esc($application['position']) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 bg-light h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-2">
                            <div class="badge bg-success p-2 me-2 rounded-circle">
                                <i class="bi bi-person-wheelchair text-white"></i>
                            </div>
                            <span class="fw-bold small text-uppercase text-secondary">Maklumat Pesakit</span>
                        </div>
                        <table class="table table-sm table-borderless mb-0 small">
                            <tr>
                                <th width="35%" class="text-muted">Nama Pesakit:</th>
                                <td class="fw-bold text-uppercase"><?= esc($application['patient_name'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Nombor RN:</th>
                                <td class="font-monospace fw-bold text-primary"><?= esc($application['patient_rn'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">No. Kad Pengenalan:</th>
                                <td class="font-monospace"><?= esc($application['patient_ic'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">ID Lawatan:</th>
                                <td class="font-monospace"><?= esc($application['visit_id'] ?? '-') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3 Financial KPI Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 bg-secondary bg-opacity-10 text-center p-3">
                    <div class="text-muted small fw-semibold text-uppercase">Jumlah Kasar (Gross)</div>
                    <div class="fs-4 fw-bold font-monospace text-dark mt-1">
                        RM <?= number_format($application['total_gross'] ?? 0, 2) ?>
                    </div>
                    <div class="small text-muted">Nilai Asal Semua Prosedur</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 bg-success bg-opacity-10 text-center p-3 border-start border-success border-4">
                    <div class="text-success small fw-semibold text-uppercase">Jumlah Bersih Tuntutan (Pakar)</div>
                    <div class="fs-4 fw-bold font-monospace text-success mt-1">
                        RM <?= number_format($application['total_claim'] ?? 0, 2) ?>
                    </div>
                    <div class="small text-success">Mengikut Peratusan Tuntutan</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3 bg-warning bg-opacity-10 text-center p-3 border-start border-warning border-4">
                    <div class="text-warning-emphasis small fw-semibold text-uppercase">Tabung Kebajikan (Pilihan)</div>
                    <div class="fs-4 fw-bold font-monospace text-warning-emphasis mt-1">
                        RM <?= number_format($application['total_welfare'] ?? 0, 2) ?>
                    </div>
                    <div class="small text-muted">
                        <?= ($application['total_welfare'] ?? 0) > 0 ? 'Sumbangan Diaktifkan' : 'Tidak Diaktifkan (Pilihan)' ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Procedures Claim Table -->
        <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
            <div class="card-header bg-dark text-white py-2 px-3 d-flex justify-content-between align-items-center">
                <span class="fw-semibold small">
                    <i class="bi bi-table me-2 text-warning"></i> Perincian Tuntutan Prosedur Selepas Jumlah Bersih
                </span>
                <span class="badge bg-primary">
                    Bil. Prosedur: <?= count($procedures) ?>
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0 align-middle small">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th width="5%">#</th>
                                <th width="12%">Kod Prosedur</th>
                                <th class="text-start">Nama Prosedur</th>
                                <th width="16%" class="text-end">Harga Asal (RM)</th>
                                <th width="14%">Tuntutan (%)</th>
                                <th width="18%" class="text-end text-success fw-bold">Jumlah Bersih Tuntutan (RM)</th>
                                <th width="18%" class="text-end text-warning-emphasis">Tabung Kebajikan (Pilihan) (RM)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($procedures)): ?>
                                <?php 
                                $hasWelfare = ($application['total_welfare'] ?? 0) > 0;
                                foreach ($procedures as $idx => $p): 
                                    $fee = (float) ($p['price'] ?? 0);
                                    $pct = isset($p['claimPct']) ? (float) $p['claimPct'] : 100;
                                    $claimAmt = $fee * ($pct / 100);
                                    $welfareAmt = $hasWelfare ? ($fee - $claimAmt) : 0;
                                ?>
                                    <tr>
                                        <td class="text-center text-muted"><?= $idx + 1 ?></td>
                                        <td class="text-center font-monospace fw-semibold text-primary"><?= esc($p['code'] ?? '-') ?></td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc($p['name'] ?? '-') ?></div>
                                        </td>
                                        <td class="text-end font-monospace"><?= number_format($fee, 2) ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= $pct == 100 ? 'bg-success' : 'bg-primary' ?> px-2 py-1 font-monospace">
                                                <?= $pct ?>%
                                            </span>
                                        </td>
                                        <td class="text-end font-monospace fw-bold text-success">
                                            RM <?= number_format($claimAmt, 2) ?>
                                        </td>
                                        <td class="text-end font-monospace <?= $hasWelfare ? 'text-warning-emphasis fw-semibold' : 'text-muted' ?>">
                                            <?= $hasWelfare ? 'RM ' . number_format($welfareAmt, 2) : '<span class="fst-italic text-muted">—</span>' ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">Tiada rekod prosedur disimpan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-dark text-center fw-bold">
                            <tr>
                                <td colspan="3" class="text-end text-uppercase">JUMLAH KESELURUHAN:</td>
                                <td class="text-end font-monospace">RM <?= number_format($application['total_gross'] ?? 0, 2) ?></td>
                                <td>-</td>
                                <td class="text-end text-success font-monospace fs-6">RM <?= number_format($application['total_claim'] ?? 0, 2) ?></td>
                                <td class="text-end text-warning font-monospace">RM <?= number_format($application['total_welfare'] ?? 0, 2) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Aliran Status & Keputusan Semakan (JPPP & Kewangan) -->
        <div class="row g-3 mb-4 d-print-none">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100 <?= ($application['jppp_status'] ?? '') === 'approved' ? 'border-start border-success border-4' : (($application['jppp_status'] ?? '') === 'rejected' ? 'border-start border-danger border-4' : 'border-start border-warning border-4') ?>">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small text-uppercase text-secondary">
                                <i class="bi bi-clipboard2-pulse me-1 text-primary"></i> 1. Semakan JPPP
                            </span>
                            <?php if (($application['jppp_status'] ?? '') === 'approved'): ?>
                                <span class="badge-status badge-status-success">Disahkan & Disokong</span>
                            <?php elseif (($application['jppp_status'] ?? '') === 'rejected'): ?>
                                <span class="badge-status badge-status-danger">Ditolak</span>
                            <?php else: ?>
                                <span class="badge-status badge-status-warning">Menunggu Semakan</span>
                            <?php endif; ?>
                        </div>
                        <div class="small">
                            <?php if (!empty($application['jppp_verified_at'])): ?>
                                <div><strong>Pegawai JPPP:</strong> <?= esc($application['jppp_reviewer_name'] ?? 'Pegawai JPPP') ?></div>
                                <div class="text-muted">Tarikh: <?= date('d/m/Y h:i A', strtotime($application['jppp_verified_at'])) ?></div>
                                <?php if (!empty($application['jppp_remarks'])): ?>
                                    <div class="mt-1 text-dark fst-italic">Catatan: "<?= esc($application['jppp_remarks']) ?>"</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted fst-italic">Dalam giliran semakan Jawatankuasa JPPP.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100 <?= ($application['finance_status'] ?? '') === 'approved' ? 'border-start border-success border-4' : (($application['finance_status'] ?? '') === 'rejected' ? 'border-start border-danger border-4' : 'border-start border-secondary border-4') ?>">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold small text-uppercase text-secondary">
                                <i class="bi bi-cash-coin me-1 text-success"></i> 2. Kelulusan Kewangan
                            </span>
                            <?php if (($application['finance_status'] ?? '') === 'approved'): ?>
                                <span class="badge-status badge-status-success">Diluluskan Bayaran</span>
                            <?php elseif (($application['finance_status'] ?? '') === 'rejected'): ?>
                                <span class="badge-status badge-status-danger">Ditolak</span>
                            <?php else: ?>
                                <span class="badge-status badge-status-warning">Menunggu</span>
                            <?php endif; ?>
                        </div>
                        <div class="small">
                            <?php if (!empty($application['finance_verified_at'])): ?>
                                <div><strong>Pegawai Kewangan:</strong> <?= esc($application['finance_reviewer_name'] ?? 'Pegawai Kewangan') ?></div>
                                <?php if (!empty($application['finance_voucher_no'])): ?>
                                    <div><strong>No. Baucar:</strong> <span class="font-monospace text-primary fw-bold"><?= esc($application['finance_voucher_no']) ?></span></div>
                                <?php endif; ?>
                                <div class="text-muted">Tarikh: <?= date('d/m/Y h:i A', strtotime($application['finance_verified_at'])) ?></div>
                                <?php if (!empty($application['finance_remarks'])): ?>
                                    <div class="mt-1 text-dark fst-italic">Catatan: "<?= esc($application['finance_remarks']) ?>"</div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted fst-italic">Menunggu tindakan kelulusan Bahagian Kewangan.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($application['remarks'])): ?>
            <div class="card shadow-sm border-0 rounded-3 bg-light mb-4">
                <div class="card-body p-3 small">
                    <span class="fw-semibold text-secondary"><i class="bi bi-chat-left-text me-1"></i> Catatan Pemohon:</span>
                    <p class="mb-0 text-dark mt-1"><?= nl2br(esc($application['remarks'])) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Signatures & Verification Section (Print Only) -->
        <div class="d-none d-print-block print-signature-area pt-3">
            <div class="row g-3">
                <div class="col-4">
                    <div class="border rounded p-2.5 text-center h-100 d-flex flex-column justify-content-between" style="min-height: 170px;">
                        <div>
                            <div class="fw-bold small text-uppercase text-decoration-underline mb-1">1. Pengesahan Pemohon (Pakar)</div>
                            <div class="small text-muted fst-italic" style="font-size: 0.72rem;">Saya mengesahkan bahawa segala butiran tuntutan ini adalah tepat dan benar.</div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="fw-bold small"><?= esc($application['specialist_name']) ?></div>
                            <div class="small text-muted" style="font-size: 0.7rem;">No. Staf: <?= esc($application['staff_number']) ?> &bull; Tarikh: <?= date('d/m/Y') ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border rounded p-2.5 text-center h-100 d-flex flex-column justify-content-between" style="min-height: 170px;">
                        <div>
                            <div class="fw-bold small text-uppercase text-decoration-underline mb-1">2. Perakuan JPPP</div>
                            <div class="small text-muted fst-italic" style="font-size: 0.72rem;">Disahkan & disokong untuk kelulusan Bahagian Kewangan.</div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <?php if (!empty($application['jppp_verified_at'])): ?>
                                <div class="fw-bold small"><?= esc($application['jppp_reviewer_name'] ?? 'Pegawai JPPP') ?></div>
                                <div class="small text-muted" style="font-size: 0.7rem;">Tarikh: <?= date('d/m/Y', strtotime($application['jppp_verified_at'])) ?> (DISOKONG)</div>
                            <?php else: ?>
                                <div class="small text-muted mb-1">(Tandatangan & Cop JPPP)</div>
                                <div class="small text-muted" style="font-size: 0.7rem;">Tarikh: ..............................</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border rounded p-2.5 text-center h-100 d-flex flex-column justify-content-between" style="min-height: 170px;">
                        <div>
                            <div class="fw-bold small text-uppercase text-decoration-underline mb-1">3. Kelulusan Kewangan</div>
                            <div class="small text-muted fst-italic" style="font-size: 0.72rem;">Diluluskan untuk pembayaran baucar ke akaun pakar.</div>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <?php if (!empty($application['finance_verified_at'])): ?>
                                <div class="fw-bold small"><?= esc($application['finance_reviewer_name'] ?? 'Pegawai Kewangan') ?></div>
                                <div class="small text-muted" style="font-size: 0.7rem;">Baucar: <?= esc($application['finance_voucher_no'] ?? '-') ?> &bull; <?= date('d/m/Y', strtotime($application['finance_verified_at'])) ?></div>
                            <?php else: ?>
                                <div class="small text-muted mb-1">(Tandatangan & Cop Kewangan)</div>
                                <div class="small text-muted" style="font-size: 0.7rem;">Tarikh: ..............................</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center text-muted small mt-3 pt-2 border-top" style="font-size: 0.72rem;">
                Sistem Pengurusan Tuntutan Pakar (X-Claim) &bull; Hospital Pengajar Universiti Sultan Zainal Abidin (HPUniSZA)
            </div>
        </div>

    </div>
</div>

<style>
@media print {
    /* Hide layout elements not needed when printing */
    .sidebar,
    .topbar,
    .page-header,
    .page-footer,
    .btn,
    nav[aria-label="breadcrumb"] {
        display: none !important;
    }
    .main-wrapper,
    .page-content {
        margin: 0 !important;
        padding: 0 !important;
    }
    body {
        background-color: #fff !important;
        font-size: 11pt !important;
        color: #000 !important;
    }
    .card-panel {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .card-panel-header {
        display: none !important;
    }
    .card-panel-body {
        padding: 0 !important;
    }
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
    .table {
        border-color: #dee2e6 !important;
    }
    .table-dark {
        background-color: #212529 !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .badge {
        border: 1px solid #000 !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .print-signature-area {
        page-break-inside: avoid;
        margin-top: 2rem;
    }
    @page {
        size: A4 portrait;
        margin: 1.2cm 1.5cm;
    }
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('print') === '1') {
        setTimeout(function () {
            window.print();
        }, 600);
    }
});
</script>
<?= $this->endSection() ?>
