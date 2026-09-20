<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$isPendingFin = ($application['finance_status'] === 'pending' || empty($application['finance_status']));
$isApprovedFin = ($application['finance_status'] === 'approved');
$isRejectedFin = ($application['finance_status'] === 'rejected');
$defaultVoucherNo = 'VCR-' . date('Ymd') . '-' . sprintf('%04d', $application['id']);
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-cash-coin text-success me-2"></i>Semakan & Kelulusan Kewangan
        </h4>
        <div class="text-muted small">
            No. Rujukan: <strong class="font-monospace text-primary"><?= esc($application['application_no']) ?></strong>
            &bull; Dihantar: <?= $application['submitted_at'] ? date('d/m/Y h:i A', strtotime($application['submitted_at'])) : date('d/m/Y h:i A', strtotime($application['created_at'])) ?>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('review-kewangan') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Senarai Kewangan
        </a>
        <a href="<?= base_url('new-application/show/' . $application['id'] . '?print=1') ?>" class="btn btn-outline-primary btn-sm" target="_blank">
            <i class="bi bi-printer me-1"></i> Cetak Baucar / Permohonan
        </a>
    </div>
</div>

<!-- JPPP Endorsement Banner -->
<div class="alert alert-success d-flex align-items-center mb-4 border-0 shadow-sm rounded-3">
    <i class="bi bi-check-circle-fill fs-3 me-3 text-success"></i>
    <div>
        <div class="fw-bold">Permohonan Telah Disahkan & Disokong oleh JPPP</div>
        <div class="small">
            Disemak oleh: <strong><?= esc($application['jppp_reviewer_name'] ?? 'Pegawai JPPP') ?></strong> 
            pada <?= $application['jppp_verified_at'] ? date('d/m/Y h:i A', strtotime($application['jppp_verified_at'])) : '-' ?>
            <?php if (!empty($application['jppp_remarks'])): ?>
                &bull; <em>"<?= esc($application['jppp_remarks']) ?>"</em>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Top Row: Maklumat Pakar, Maklumat Pesakit, and Aliran Status Permohonan (Disebelah kanan Maklumat Pesakit) -->
<div class="row g-3 mb-4">

    <!-- 1. Maklumat Penerima Bayaran (Pakar) -->
    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm rounded-3 bg-light h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="badge bg-primary p-2 me-2 rounded-circle">
                        <i class="bi bi-person-badge text-white"></i>
                    </div>
                    <span class="fw-bold small text-uppercase text-secondary">Penerima Bayaran (Pakar)</span>
                </div>
                <table class="table table-sm table-borderless mb-0 small">
                    <tr>
                        <th width="35%" class="text-muted">Nama:</th>
                        <td class="fw-bold text-dark"><?= esc($application['specialist_name']) ?></td>
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
                        <th class="text-muted">Jabatan:</th>
                        <td><?= esc($application['department']) ?> &bull; <?= esc($application['position']) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. Maklumat Pesakit -->
    <div class="col-lg-4 col-md-6">
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
                        <th width="35%" class="text-muted">Nama:</th>
                        <td class="fw-bold text-uppercase"><?= esc($application['patient_name'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Nombor RN:</th>
                        <td class="font-monospace fw-bold text-primary"><?= esc($application['patient_rn'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th class="text-muted">No. KP / ID:</th>
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

    <!-- 3. Aliran Status Permohonan: disebelah kanan Maklumat Pesakit -->
    <div class="col-lg-4 col-md-12">
        <div class="card border-0 shadow-sm rounded-3 bg-light h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="badge bg-info p-2 me-2 rounded-circle">
                        <i class="bi bi-signpost-split text-white"></i>
                    </div>
                    <span class="fw-bold small text-uppercase text-secondary">Aliran Status Permohonan</span>
                </div>
                <ul class="list-unstyled mb-0 small position-relative ps-1">
                    <li class="mb-2.5 d-flex align-items-start">
                        <i class="bi bi-check-circle-fill text-success fs-6 me-2 mt-0.5"></i>
                        <div>
                            <strong>1. Dihantar oleh Pakar</strong>
                            <div class="text-muted" style="font-size: 0.75rem;"><?= date('d/m/Y h:i A', strtotime($application['created_at'])) ?></div>
                        </div>
                    </li>
                    <li class="mb-2.5 d-flex align-items-start">
                        <i class="bi bi-check-circle-fill text-success fs-6 me-2 mt-0.5"></i>
                        <div>
                            <strong>2. Pengesahan JPPP</strong>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <span class="badge bg-success">Disahkan & Disokong</span>
                                <?php if (!empty($application['jppp_verified_at'])): ?>
                                    &bull; <?= date('d/m/Y', strtotime($application['jppp_verified_at'])) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                    <li class="d-flex align-items-start">
                        <i class="bi <?= $isApprovedFin ? 'bi-check-circle-fill text-success' : ($isRejectedFin ? 'bi-x-circle-fill text-danger' : 'bi-hourglass-split text-warning') ?> fs-6 me-2 mt-0.5"></i>
                        <div>
                            <strong>3. Kelulusan Kewangan</strong>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <?= $isPendingFin ? '<span class="badge bg-warning text-dark">Sedang Diproses</span>' : ($isApprovedFin ? '<span class="badge bg-success">Diluluskan Bayaran</span>' : '<span class="badge bg-danger">Ditolak</span>') ?>
                                <?php if (!empty($application['finance_verified_at'])): ?>
                                    &bull; <?= date('d/m/Y', strtotime($application['finance_verified_at'])) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>

<!-- 3 Financial Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 bg-secondary bg-opacity-10 text-center p-3">
            <div class="text-muted small fw-semibold text-uppercase">Jumlah Kasar (Gross)</div>
            <div class="fs-4 fw-bold font-monospace text-dark mt-1">
                RM <?= number_format($application['total_gross'] ?? 0, 2) ?>
            </div>
            <div class="small text-muted">Nilai Penuh Prosedur</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 bg-success bg-opacity-10 text-center p-3 border-start border-success border-4">
            <div class="text-success small fw-semibold text-uppercase">Jumlah Bayaran Bersih</div>
            <div class="fs-3 fw-bold font-monospace text-success mt-1">
                RM <?= number_format($application['total_claim'] ?? 0, 2) ?>
            </div>
            <div class="small text-success">Perlu Dibayar ke Akaun Pakar</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 bg-warning bg-opacity-10 text-center p-3 border-start border-warning border-4">
            <div class="text-warning-emphasis small fw-semibold text-uppercase">Tabung Kebajikan</div>
            <div class="fs-4 fw-bold font-monospace text-warning-emphasis mt-1">
                RM <?= number_format($application['total_welfare'] ?? 0, 2) ?>
            </div>
            <div class="small text-muted">
                <?= ($application['total_welfare'] ?? 0) > 0 ? 'Disalurkan ke Dana Kebajikan' : 'Tiada Potongan' ?>
            </div>
        </div>
    </div>
</div>

<!-- Table: Butiran Prosedur & Peratusan Tuntutan -->
<div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
    <div class="card-header bg-dark text-white py-2 px-3 d-flex justify-content-between align-items-center">
        <span class="fw-semibold small">
            <i class="bi bi-table me-2 text-warning"></i> Butiran Prosedur & Peratusan Tuntutan
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
                        <th width="14%">Kod Prosedur</th>
                        <th class="text-start">Nama Prosedur MMA</th>
                        <th width="16%" class="text-end">Harga Asal (RM)</th>
                        <th width="12%">Tuntutan (%)</th>
                        <th width="18%" class="text-end text-success fw-bold">Jumlah Bersih (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($procedures)): ?>
                        <?php foreach ($procedures as $idx => $p): 
                            $fee = (float) ($p['price'] ?? 0);
                            $pct = isset($p['claimPct']) ? (float) $p['claimPct'] : 100;
                            $claimAmt = $fee * ($pct / 100);
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
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">Tiada maklumat prosedur.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-dark text-center fw-bold">
                    <tr>
                        <td colspan="3" class="text-end text-uppercase">JUMLAH KESELURUHAN:</td>
                        <td class="text-end font-monospace">RM <?= number_format($application['total_gross'] ?? 0, 2) ?></td>
                        <td>-</td>
                        <td class="text-end text-success font-monospace fs-6">RM <?= number_format($application['total_claim'] ?? 0, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Status / Keputusan Semakan Kewangan: berada DIBANGUN/DIBAWAH table Senarai Prosedur Yang Dituntut -->
<?php if ($isPendingFin): ?>
    <!-- Form Tindakan Kewangan -->
    <div class="card border-0 shadow-sm rounded-3 border-top border-success border-4 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-cash-stack text-success me-2"></i>Status & Kelulusan Baucar Bayaran Kewangan
                </h6>
                <small class="text-muted">Sila buat pengesahan kewangan, semakan akaun, dan kelulusan baucar pembayaran di bawah.</small>
            </div>
            <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill">
                <i class="bi bi-hourglass-split me-1"></i> Menunggu Kelulusan Kewangan
            </span>
        </div>
        <div class="card-body p-4">
            <form id="formFinanceAction">
                <?= csrf_field() ?>

                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-secondary">Keputusan Kewangan:</label>
                        <div class="d-flex flex-column gap-2">
                            <label class="form-check-label border rounded p-3 d-flex align-items-center gap-3 cursor-pointer bg-light hover-shadow" for="actionApprove">
                                <input class="form-check-input mt-0" type="radio" name="action" id="actionApprove" value="approve" checked>
                                <div>
                                    <div class="fw-bold text-success"><i class="bi bi-check-circle me-1"></i> Luluskan Bayaran</div>
                                    <small class="text-muted">Permohonan diluluskan untuk pembayaran kepada pakar.</small>
                                </div>
                            </label>

                            <label class="form-check-label border rounded p-3 d-flex align-items-center gap-3 cursor-pointer bg-light hover-shadow" for="actionReject">
                                <input class="form-check-input mt-0" type="radio" name="action" id="actionReject" value="reject">
                                <div>
                                    <div class="fw-bold text-danger"><i class="bi bi-x-circle me-1"></i> Tolak Bayaran</div>
                                    <small class="text-muted">Masalah peruntukan / dokumen kewangan tidak sah.</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="mb-3" id="voucherFieldGroup">
                            <label for="voucherNo" class="form-label fw-semibold small text-secondary">
                                No. Baucar Bayaran / EFT:
                            </label>
                            <input type="text" class="form-control font-monospace fw-bold text-primary" id="voucherNo" name="voucher_no" value="<?= esc($defaultVoucherNo) ?>" placeholder="Contoh: VCR-20260920-0001">
                            <small class="text-muted" style="font-size: 0.72rem;">Nombor rujukan rasmi perakaunan HPUniSZA.</small>
                        </div>

                        <div class="mb-2">
                            <label for="financeRemarks" class="form-label fw-semibold small text-secondary">
                                Catatan / Rujukan Transaksi:
                            </label>
                            <textarea class="form-control" id="financeRemarks" name="remarks" rows="2" placeholder="Masukkan sebarang catatan transaksi, akaun atau cek jika ada..."></textarea>
                        </div>
                    </div>

                    <div class="col-md-3 d-flex flex-column justify-content-between">
                        <div class="p-3 bg-light rounded-3 border text-center mb-3">
                            <div class="small text-muted mb-1">Jumlah Bayaran Bersih:</div>
                            <div class="fs-4 fw-bold font-monospace text-success">
                                RM <?= number_format($application['total_claim'] ?? 0, 2) ?>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="button" class="btn btn-success fw-semibold shadow-sm py-2" id="btnSubmitFinance">
                                <i class="bi bi-check2-circle me-1"></i> Sahkan & Luluskan Bayaran
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php else: ?>
    <!-- Status Kelulusan Kewangan Yang Telah Dibuat -->
    <div class="card border-0 shadow-sm rounded-3 <?= $isApprovedFin ? 'border-top border-success border-4' : 'border-top border-danger border-4' ?> mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi <?= $isApprovedFin ? 'bi-check2-circle text-success' : 'bi-x-circle-fill text-danger' ?> me-2"></i>
                Status Kelulusan Kewangan
            </h6>
            <span class="badge <?= $isApprovedFin ? 'bg-success' : 'bg-danger' ?> fs-6 px-3 py-1.5 rounded-pill">
                <?= $isApprovedFin ? 'DILULUSKAN UNTUK BAYARAN' : 'DITOLAK OLEH KEWANGAN' ?>
            </span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <?php if ($isApprovedFin && !empty($application['finance_voucher_no'])): ?>
                    <div class="col-md-3">
                        <div class="p-3 rounded bg-light border">
                            <div class="text-muted small">No. Baucar Bayaran:</div>
                            <div class="font-monospace fw-bold text-primary fs-6 mt-1"><?= esc($application['finance_voucher_no']) ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="<?= ($isApprovedFin && !empty($application['finance_voucher_no'])) ? 'col-md-3' : 'col-md-4' ?>">
                    <div class="p-3 rounded bg-light border">
                        <div class="text-muted small">Pegawai Kewangan:</div>
                        <div class="fw-bold text-dark fs-6 mt-1"><?= esc($application['finance_reviewer_name'] ?? 'Pegawai Kewangan') ?></div>
                    </div>
                </div>
                <div class="<?= ($isApprovedFin && !empty($application['finance_voucher_no'])) ? 'col-md-3' : 'col-md-4' ?>">
                    <div class="p-3 rounded bg-light border">
                        <div class="text-muted small">Tarikh & Masa Kelulusan:</div>
                        <div class="fw-bold text-dark fs-6 mt-1"><?= $application['finance_verified_at'] ? date('d/m/Y h:i A', strtotime($application['finance_verified_at'])) : '-' ?></div>
                    </div>
                </div>
                <div class="<?= ($isApprovedFin && !empty($application['finance_voucher_no'])) ? 'col-md-3' : 'col-md-4' ?>">
                    <div class="p-3 rounded bg-light border">
                        <div class="text-muted small">Jumlah Bayaran Diluluskan:</div>
                        <div class="fw-bold font-monospace text-success fs-6 mt-1">RM <?= number_format($application['total_claim'] ?? 0, 2) ?></div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3 rounded bg-light border">
                        <div class="text-muted small mb-1">Catatan / Rujukan Kewangan:</div>
                        <div class="text-dark fst-italic">
                            <?= !empty($application['finance_remarks']) ? nl2br(esc($application['finance_remarks'])) : 'Tiada catatan dimasukkan.' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('input[name="action"]').on('change', function () {
    if (this.value === 'reject') {
        $('#voucherFieldGroup').slideUp();
    } else {
        $('#voucherFieldGroup').slideDown();
    }
});

$('#btnSubmitFinance').on('click', function () {
    const action = $('input[name="action"]:checked').val();
    const remarks = $('#financeRemarks').val().trim();

    if (action === 'reject' && !remarks) {
        Swal.fire({
            icon: 'warning',
            title: 'Catatan Diperlukan',
            text: 'Sila masukkan catatan atau justifikasi bagi penolakan bayaran permohonan ini.'
        });
        return;
    }

    const titleText = action === 'approve' 
        ? 'Luluskan Pembayaran Tuntutan Ini?' 
        : 'Tolak Pembayaran Permohonan Ini?';

    Swal.fire({
        title: titleText,
        text: action === 'approve' 
            ? 'Jumlah sebanyak RM <?= number_format($application['total_claim'] ?? 0, 2) ?> akan diluluskan untuk pembayaran kepada pakar.' 
            : 'Permohonan ini akan ditolak daripada proses pembayaran.',
        icon: action === 'approve' ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonText: action === 'approve' ? 'Ya, Luluskan Bayaran' : 'Ya, Tolak Permohonan',
        cancelButtonText: 'Batal',
        confirmButtonColor: action === 'approve' ? '#198754' : '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            const btn = $('#btnSubmitFinance');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');

            $.ajax({
                url: BASE_URL + 'review-kewangan/process/<?= $application['id'] ?>',
                method: 'POST',
                data: $('#formFinanceAction').serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berjaya',
                            text: res.message
                        }).then(() => {
                            window.location.href = res.redirect || BASE_URL + 'review-kewangan';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ralat',
                            text: res.message || 'Gagal memproses kelulusan kewangan.'
                        });
                        btn.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Sahkan & Luluskan Pembayaran');
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ralat Pelayan',
                        text: 'Sila cuba lagi sebentar lagi.'
                    });
                    btn.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Sahkan & Luluskan Pembayaran');
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>
