<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$isPending = ($application['jppp_status'] === 'pending' || empty($application['jppp_status']));
$isApproved = ($application['jppp_status'] === 'approved');
$isRejected = ($application['jppp_status'] === 'rejected');
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-clipboard2-pulse-fill text-primary me-2"></i>Semakan Permohonan JPPP
        </h4>
        <div class="text-muted small">
            No. Rujukan: <strong class="font-monospace text-primary"><?= esc($application['application_no']) ?></strong>
            &bull; Dihantar pada: <?= $application['submitted_at'] ? date('d/m/Y h:i A', strtotime($application['submitted_at'])) : date('d/m/Y h:i A', strtotime($application['created_at'])) ?>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('review-jppp') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Senarai JPPP
        </a>
        <a href="<?= base_url('new-application/show/' . $application['id'] . '?print=1') ?>" class="btn btn-outline-primary btn-sm" target="_blank">
            <i class="bi bi-printer me-1"></i> Cetak Permohonan
        </a>
    </div>
</div>

<!-- Top Row: Maklumat Pakar, Maklumat Pesakit, and Aliran Status Permohonan (Disebelah kanan Maklumat Pesakit) -->
<div class="row g-3 mb-4">

    <!-- 1. Maklumat Pakar -->
    <div class="col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm rounded-3 bg-light h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="badge bg-primary p-2 me-2 rounded-circle">
                        <i class="bi bi-person-badge text-white"></i>
                    </div>
                    <span class="fw-bold small text-uppercase text-secondary">Maklumat Pakar (Pemohon)</span>
                </div>
                <table class="table table-sm table-borderless mb-0 small">
                    <tr>
                        <th width="35%" class="text-muted">Nama:</th>
                        <td class="fw-semibold text-dark"><?= esc($application['specialist_name']) ?></td>
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
                        <i class="bi <?= $isPending ? 'bi-hourglass-split text-warning' : ($isApproved ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger') ?> fs-6 me-2 mt-0.5"></i>
                        <div>
                            <strong>2. Pengesahan JPPP</strong>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <?= $isPending ? '<span class="badge bg-warning text-dark">Sedang Diproses</span>' : ($isApproved ? '<span class="badge bg-success">Disahkan & Disokong</span>' : '<span class="badge bg-danger">Ditolak</span>') ?>
                                <?php if (!empty($application['jppp_verified_at'])): ?>
                                    &bull; <?= date('d/m/Y', strtotime($application['jppp_verified_at'])) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                    <li class="d-flex align-items-start">
                        <i class="bi <?= $application['finance_status'] === 'approved' ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted' ?> fs-6 me-2 mt-0.5"></i>
                        <div>
                            <strong>3. Kelulusan Kewangan</strong>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <?= $application['finance_status'] === 'approved' ? '<span class="badge bg-success">Diluluskan Bayaran</span>' : '<span class="badge bg-secondary">Menunggu</span>' ?>
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
            <div class="small text-muted">Nilai Asal Prosedur</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 bg-success bg-opacity-10 text-center p-3 border-start border-success border-4">
            <div class="text-success small fw-semibold text-uppercase">Jumlah Bersih Tuntutan</div>
            <div class="fs-4 fw-bold font-monospace text-success mt-1">
                RM <?= number_format($application['total_claim'] ?? 0, 2) ?>
            </div>
            <div class="small text-success">Nilai Tuntutan Pakar</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 bg-warning bg-opacity-10 text-center p-3 border-start border-warning border-4">
            <div class="text-warning-emphasis small fw-semibold text-uppercase">Tabung Kebajikan</div>
            <div class="fs-4 fw-bold font-monospace text-warning-emphasis mt-1">
                RM <?= number_format($application['total_welfare'] ?? 0, 2) ?>
            </div>
            <div class="small text-muted">Sumbangan Sukarela Hospital</div>
        </div>
    </div>
</div>

<!-- Table: Senarai Prosedur Yang Dituntut -->
<div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
    <div class="card-header bg-dark text-white py-2 px-3 d-flex justify-content-between align-items-center">
        <span class="fw-semibold small">
            <i class="bi bi-table me-2 text-warning"></i> Senarai Prosedur Yang Dituntut
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
                        <th width="14%">Kod</th>
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
                            <td colspan="6" class="text-center text-muted py-3">Tiada rekod prosedur.</td>
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

<?php if (!empty($application['remarks'])): ?>
    <div class="card shadow-sm border-0 rounded-3 bg-light mb-4">
        <div class="card-body p-3 small">
            <span class="fw-semibold text-secondary"><i class="bi bi-chat-left-text me-1"></i> Catatan Pemohon:</span>
            <p class="mb-0 text-dark mt-1"><?= nl2br(esc($application['remarks'])) ?></p>
        </div>
    </div>
<?php endif; ?>

<!-- Status / Keputusan Semakan JPPP: berada DIBANGUN/DIBAWAH table Senarai Prosedur Yang Dituntut -->
<?php if ($isPending): ?>
    <!-- Form Tindakan JPPP -->
    <div class="card border-0 shadow-sm rounded-3 border-top border-primary border-4 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-clipboard2-check-fill text-primary me-2"></i>Status & Keputusan Semakan JPPP
                </h6>
                <small class="text-muted">Sila buat semakan klinikal dan perakuan prosedur perkhidmatan pakar di bawah.</small>
            </div>
            <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill">
                <i class="bi bi-hourglass-split me-1"></i> Menunggu Tindakan Semakan
            </span>
        </div>
        <div class="card-body p-4">
            <form id="formJpppAction">
                <?= csrf_field() ?>

                <div class="row g-4">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small text-secondary">Tindakan Pengesahan:</label>
                        <div class="d-flex flex-column gap-2">
                            <label class="form-check-label border rounded p-3 d-flex align-items-center gap-3 cursor-pointer bg-light hover-shadow" for="actionApprove">
                                <input class="form-check-input mt-0" type="radio" name="action" id="actionApprove" value="approve" checked>
                                <div>
                                    <div class="fw-bold text-success"><i class="bi bi-check-circle me-1"></i> Sahkan & Sokong</div>
                                    <small class="text-muted">Permohonan disahkan teratur dan disalurkan terus ke Bahagian Kewangan.</small>
                                </div>
                            </label>

                            <label class="form-check-label border rounded p-3 d-flex align-items-center gap-3 cursor-pointer bg-light hover-shadow" for="actionReject">
                                <input class="form-check-input mt-0" type="radio" name="action" id="actionReject" value="reject">
                                <div>
                                    <div class="fw-bold text-danger"><i class="bi bi-x-circle me-1"></i> Tolak Permohonan</div>
                                    <small class="text-muted">Permohonan tidak menepati kriteria / kuiri kepada pemohon.</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <label for="jpppRemarks" class="form-label fw-semibold small text-secondary">
                            Catatan / Ulasan Semakan JPPP:
                        </label>
                        <textarea class="form-control" id="jpppRemarks" name="remarks" rows="4" placeholder="Masukkan sebarang catatan, justifikasi atau sebab semakan/penolakan..."></textarea>
                        <small class="text-muted d-block mt-1">Catatan ini akan direkodkan dalam jejak audit dan dokumen pengesahan rasmi.</small>

                        <div class="mt-3 text-end">
                            <button type="button" class="btn btn-primary px-4 fw-semibold shadow-sm" id="btnSubmitJppp">
                                <i class="bi bi-send-check me-1"></i> Hantar Keputusan JPPP
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php else: ?>
    <!-- Keputusan Semakan JPPP Yang Telah Dibuat -->
    <div class="card border-0 shadow-sm rounded-3 <?= $isApproved ? 'border-top border-success border-4' : 'border-top border-danger border-4' ?> mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi <?= $isApproved ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?> me-2"></i>
                Status Semakan JPPP
            </h6>
            <span class="badge <?= $isApproved ? 'bg-success' : 'bg-danger' ?> fs-6 px-3 py-1.5 rounded-pill">
                <?= $isApproved ? 'DISAHKAN & DISOKONG' : 'DITOLAK OLEH JPPP' ?>
            </span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="p-3 rounded bg-light border">
                        <div class="text-muted small">Pegawai Penyemak JPPP:</div>
                        <div class="fw-bold text-dark fs-6 mt-1"><?= esc($application['jppp_reviewer_name'] ?? 'Pegawai JPPP') ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 rounded bg-light border">
                        <div class="text-muted small">Tarikh & Masa Semakan:</div>
                        <div class="fw-bold text-dark fs-6 mt-1"><?= $application['jppp_verified_at'] ? date('d/m/Y h:i A', strtotime($application['jppp_verified_at'])) : '-' ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 rounded bg-light border">
                        <div class="text-muted small">Keputusan Status:</div>
                        <div class="fw-bold fs-6 mt-1 <?= $isApproved ? 'text-success' : 'text-danger' ?>">
                            <?= $isApproved ? 'Disokong ke Kewangan' : 'Ditolak' ?>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3 rounded bg-light border">
                        <div class="text-muted small mb-1">Catatan / Ulasan JPPP:</div>
                        <div class="text-dark fst-italic">
                            <?= !empty($application['jppp_remarks']) ? nl2br(esc($application['jppp_remarks'])) : 'Tiada catatan dimasukkan.' ?>
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
$('#btnSubmitJppp').on('click', function () {
    const action = $('input[name="action"]:checked').val();
    const remarks = $('#jpppRemarks').val().trim();

    if (action === 'reject' && !remarks) {
        Swal.fire({
            icon: 'warning',
            title: 'Catatan Diperlukan',
            text: 'Sila masukkan catatan atau justifikasi bagi penolakan permohonan ini.'
        });
        return;
    }

    const titleText = action === 'approve' 
        ? 'Sahkan & Teruskan ke Bahagian Kewangan?' 
        : 'Tolak Permohonan Tuntutan Ini?';

    Swal.fire({
        title: titleText,
        text: action === 'approve' 
            ? 'Permohonan ini akan disokong dan dihantar kepada Bahagian Kewangan untuk proses kelulusan bayaran.' 
            : 'Permohonan ini akan ditolak dan dimaklumkan kepada pemohon.',
        icon: action === 'approve' ? 'question' : 'warning',
        showCancelButton: true,
        confirmButtonText: action === 'approve' ? 'Ya, Sahkan & Teruskan' : 'Ya, Tolak Permohonan',
        cancelButtonText: 'Batal',
        confirmButtonColor: action === 'approve' ? '#198754' : '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            const btn = $('#btnSubmitJppp');
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');

            $.ajax({
                url: BASE_URL + 'review-jppp/process/<?= $application['id'] ?>',
                method: 'POST',
                data: $('#formJpppAction').serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berjaya',
                            text: res.message
                        }).then(() => {
                            window.location.href = res.redirect || BASE_URL + 'review-jppp';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ralat',
                            text: res.message || 'Gagal memproses keputusan JPPP.'
                        });
                        btn.prop('disabled', false).html('<i class="bi bi-send-check me-1"></i> Hantar Keputusan JPPP');
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ralat Pelayan',
                        text: 'Sila cuba lagi sebentar lagi.'
                    });
                    btn.prop('disabled', false).html('<i class="bi bi-send-check me-1"></i> Hantar Keputusan JPPP');
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>
