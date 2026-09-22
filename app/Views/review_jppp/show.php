<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$penyemakStatus     = $application['penyemak_status'] ?? 'pending';
$perkhidmatanStatus = $application['perkhidmatan_status'] ?? 'pending';
$j3pStatus          = $application['j3p_status'] ?? $application['jppp_status'] ?? 'pending';
$pengarahStatus     = $application['pengarah_status'] ?? 'pending';

$stage       = $stageInfo['stage'] ?? 'pending';
$isCompleted = in_array($stage, ['approved', 'rejected']);
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-clipboard2-pulse-fill text-primary me-2"></i>Semakan Permohonan
        </h4>
        <div class="text-muted small">
            No. Rujukan: <strong class="font-monospace text-primary"><?= esc($application['application_no']) ?></strong>
            &bull; Dihantar pada: <?= $application['submitted_at'] ? date('d/m/Y h:i A', strtotime($application['submitted_at'])) : date('d/m/Y h:i A', strtotime($application['created_at'])) ?>
            &bull; Peringkat Semasa: <span class="badge <?= $stageInfo['badgeClass'] ?? 'bg-secondary' ?> ms-1"><?= $stageInfo['label'] ?? '-' ?></span>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('review-jppp') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Senarai Permohonan
        </a>
        <a href="<?= base_url('new-application/show/' . $application['id'] . '?print=1') ?>" class="btn btn-outline-primary btn-sm" target="_blank">
            <i class="bi bi-printer me-1"></i> Cetak Permohonan
        </a>
    </div>
</div>

<!-- Top Row: Maklumat Pakar, Maklumat Pesakit, and Aliran Status Permohonan (5 Peringkat) -->
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

    <!-- 3. Aliran Status Permohonan (5 Peringkat Rasmi) -->
    <div class="col-lg-4 col-md-12">
        <div class="card border-0 shadow-sm rounded-3 bg-light h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-2">
                    <div class="badge bg-info p-2 me-2 rounded-circle">
                        <i class="bi bi-diagram-3-fill text-white"></i>
                    </div>
                    <span class="fw-bold small text-uppercase text-secondary">Aliran Status Hirarki</span>
                </div>
                <ul class="list-unstyled mb-0 small position-relative ps-1">
                    <!-- 1. Dihantar oleh Pakar -->
                    <li class="mb-2 d-flex align-items-start">
                        <i class="bi bi-check-circle-fill text-success fs-6 me-2 mt-0.5"></i>
                        <div>
                            <strong>1. Dihantar oleh Pakar</strong>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                <?= $application['submitted_at'] ? date('d/m/Y h:i A', strtotime($application['submitted_at'])) : date('d/m/Y h:i A', strtotime($application['created_at'])) ?>
                            </div>
                        </div>
                    </li>

                    <!-- 2. Pegawai Menyemak PE -->
                    <li class="mb-2 d-flex align-items-start">
                        <i class="bi <?= in_array($penyemakStatus, ['approved', 'verified']) ? 'bi-check-circle-fill text-success' : ($penyemakStatus === 'rejected' ? 'bi-x-circle-fill text-danger' : 'bi-hourglass-split text-warning') ?> fs-6 me-2 mt-0.5"></i>
                        <div>
                            <strong>2. Pegawai Menyemak PE</strong>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                <?= in_array($penyemakStatus, ['approved', 'verified']) ? '<span class="badge bg-success">Disemak</span>' : ($penyemakStatus === 'rejected' ? '<span class="badge bg-danger">Ditolak</span>' : '<span class="badge bg-warning text-dark">Menunggu</span>') ?>
                                <?php if (!empty($application['penyemak_verified_at'])): ?>
                                    &bull; <?= date('d/m/Y', strtotime($application['penyemak_verified_at'])) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>

                    <!-- 3. Pegawai Perkhidmatan PE -->
                    <li class="mb-2 d-flex align-items-start">
                        <i class="bi <?= in_array($perkhidmatanStatus, ['approved', 'verified']) ? 'bi-check-circle-fill text-success' : ($perkhidmatanStatus === 'rejected' ? 'bi-x-circle-fill text-danger' : (in_array($penyemakStatus, ['approved', 'verified']) ? 'bi-hourglass-split text-warning' : 'bi-circle text-muted')) ?> fs-6 me-2 mt-0.5"></i>
                        <div>
                            <strong>3. Pegawai Perkhidmatan PE</strong>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                <?= in_array($perkhidmatanStatus, ['approved', 'verified']) ? '<span class="badge bg-success">Disahkan</span>' : ($perkhidmatanStatus === 'rejected' ? '<span class="badge bg-danger">Ditolak</span>' : (in_array($penyemakStatus, ['approved', 'verified']) ? '<span class="badge bg-warning text-dark">Menunggu</span>' : '<span class="badge bg-secondary">Giliran</span>')) ?>
                                <?php if (!empty($application['perkhidmatan_verified_at'])): ?>
                                    &bull; <?= date('d/m/Y', strtotime($application['perkhidmatan_verified_at'])) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>

                    <!-- 4. Ketua J3P -->
                    <li class="mb-2 d-flex align-items-start">
                        <i class="bi <?= in_array($j3pStatus, ['approved', 'verified']) ? 'bi-check-circle-fill text-success' : ($j3pStatus === 'rejected' ? 'bi-x-circle-fill text-danger' : (in_array($perkhidmatanStatus, ['approved', 'verified']) ? 'bi-hourglass-split text-warning' : 'bi-circle text-muted')) ?> fs-6 me-2 mt-0.5"></i>
                        <div>
                            <strong>4. Ketua J3P</strong>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                <?= in_array($j3pStatus, ['approved', 'verified']) ? '<span class="badge bg-success">Disahkan & Disokong</span>' : ($j3pStatus === 'rejected' ? '<span class="badge bg-danger">Ditolak</span>' : (in_array($perkhidmatanStatus, ['approved', 'verified']) ? '<span class="badge bg-warning text-dark">Menunggu</span>' : '<span class="badge bg-secondary">Giliran</span>')) ?>
                                <?php if (!empty($application['j3p_verified_at'])): ?>
                                    &bull; <?= date('d/m/Y', strtotime($application['j3p_verified_at'])) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>

                    <!-- 5. Pengarah Hospital / KPTj -->
                    <li class="d-flex align-items-start">
                        <i class="bi <?= in_array($pengarahStatus, ['approved', 'verified']) ? 'bi-check-circle-fill text-success' : ($pengarahStatus === 'rejected' ? 'bi-x-circle-fill text-danger' : (in_array($j3pStatus, ['approved', 'verified']) ? 'bi-hourglass-split text-warning' : 'bi-circle text-muted')) ?> fs-6 me-2 mt-0.5"></i>
                        <div>
                            <strong>5. Pengarah / KPTj</strong>
                            <div class="text-muted" style="font-size: 0.72rem;">
                                <?= in_array($pengarahStatus, ['approved', 'verified']) ? '<span class="badge bg-success">Diluluskan Penuh</span>' : ($pengarahStatus === 'rejected' ? '<span class="badge bg-danger">Ditolak</span>' : (in_array($j3pStatus, ['approved', 'verified']) ? '<span class="badge bg-warning text-dark">Menunggu</span>' : '<span class="badge bg-secondary">Giliran</span>')) ?>
                                <?php if (!empty($application['pengarah_verified_at'])): ?>
                                    &bull; <?= date('d/m/Y', strtotime($application['pengarah_verified_at'])) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>

<!-- Maklumat Tuntutan & Senarai Prosedur -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold text-dark mb-0">
            <i class="bi bi-list-check text-primary me-2"></i>Senarai Prosedur Yang Dituntut
        </h6>
        <span class="badge bg-light text-dark border font-monospace">
            Bulan: <?= esc($application['claim_month'] ?? date('m')) ?>/<?= esc($application['claim_year'] ?? date('Y')) ?>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light">
                    <tr class="text-center">
                        <th width="4%">BIL.</th>
                        <th class="text-start">PROSEDUR / PERKHIDMATAN</th>
                        <th width="14%">KATEGORI</th>
                        <th width="15%">KADAR CAJ (RM)</th>
                        <th width="15%">KADAR AGIHAN (RM)</th>
                        <th width="15%" class="text-end">TUNTUTAN PAKAR (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($procedures)): ?>
                        <?php foreach ($procedures as $i => $item): 
                            $fee = (float)($item['price'] ?? 0);
                            $cType = $item['charge_type'] ?? 'tatacara';
                            $defaultPct = $cType === 'pelaporan' ? 70 : 75;
                            $pct = isset($item['claimPct']) ? (float)$item['claimPct'] : $defaultPct;
                            $claimAmt = $fee * ($pct / 100);
                        ?>
                            <tr>
                                <td class="text-center text-muted"><?= $i + 1 ?></td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($item['name'] ?? '-') ?></div>
                                    <span class="font-monospace text-muted" style="font-size: 0.75rem;"><?= esc($item['code'] ?? '') ?></span>
                                    <?php if (!empty($item['receipt_no'])): ?>
                                        <span class="badge bg-light text-secondary border font-monospace ms-1" style="font-size: 0.7rem;">Resit: <?= esc($item['receipt_no']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border text-uppercase" style="font-size: 0.72rem;">
                                        <?= esc($cType) ?>
                                    </span>
                                </td>
                                <td class="text-center font-monospace">
                                    RM <?= number_format($fee, 2) ?>
                                </td>
                                <td class="text-center font-monospace">
                                    <?= $pct ?>%
                                    <span class="text-muted" style="font-size: 0.72rem;">
                                        (RM <?= number_format($claimAmt, 2) ?>)
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

<!-- ================================================================= -->
<!-- KAD TINDAKAN SEMAKAN MENGIKUT HIRARKI KELULUSAN                    -->
<!-- ================================================================= -->
<?php if (!$isCompleted): ?>
    <?php if ($canAct): ?>
        <!-- Form Tindakan Bagi Pegawai Pada Peringkat Semasa -->
        <div class="card border-0 shadow-sm rounded-3 border-top border-primary border-4 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-clipboard2-check-fill text-primary me-2"></i>Tindakan Semakan: <?= esc($stageInfo['officerTitle']) ?>
                    </h6>
                    <small class="text-muted">Permohonan kini berada pada giliran anda mengikut hirarki kelulusan.</small>
                </div>
                <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill">
                    <i class="bi bi-hourglass-split me-1"></i> Giliran Tindakan Anda
                </span>
            </div>
            <div class="card-body p-4">
                <form id="formJpppAction">
                    <?= csrf_field() ?>

                    <div class="row g-4">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small text-secondary">Keputusan Tindakan:</label>
                            <div class="d-flex flex-column gap-2">
                                <label class="form-check-label border rounded p-3 d-flex align-items-center gap-3 cursor-pointer bg-light hover-shadow" for="actionApprove">
                                    <input class="form-check-input mt-0" type="radio" name="action" id="actionApprove" value="approve" checked>
                                    <div>
                                        <div class="fw-bold text-success"><i class="bi bi-check-circle me-1"></i> Sahkan Permohonan</div>
                                        <small class="text-muted">
                                            Permohonan disahkan teratur dan <?= esc($stageInfo['nextOfficer'] ? 'disalurkan kepada ' . $stageInfo['nextOfficer'] : 'diluluskan penuh') ?>.
                                        </small>
                                    </div>
                                </label>

                                <label class="form-check-label border rounded p-3 d-flex align-items-center gap-3 cursor-pointer bg-light hover-shadow" for="actionReject">
                                    <input class="form-check-input mt-0" type="radio" name="action" id="actionReject" value="reject">
                                    <div>
                                        <div class="fw-bold text-danger"><i class="bi bi-x-circle me-1"></i> Tolak Permohonan</div>
                                        <small class="text-muted">Permohonan tidak menepati kriteria / terdapat kuiri.</small>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <label for="jpppRemarks" class="form-label fw-semibold small text-secondary">
                                Catatan / Ulasan Semakan (<?= esc($stageInfo['officerTitle']) ?>):
                            </label>
                            <textarea class="form-control" id="jpppRemarks" name="remarks" rows="4" placeholder="Masukkan sebarang catatan pengesahan atau justifikasi jika menolak..."></textarea>
                            <small class="text-muted d-block mt-1">Catatan ini akan direkodkan dalam jejak audit dan dokumen rasmi.</small>

                            <div class="mt-3 text-end">
                                <button type="button" class="btn btn-primary px-4 fw-semibold shadow-sm" id="btnSubmitJppp">
                                    <i class="bi bi-send-check me-1"></i> Hantar Keputusan Semakan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <!-- Peringatan Hirarki: Permohonan Belum Sampai Giliran Pegawai Ini -->
        <div class="card border-0 shadow-sm rounded-3 border-top border-warning border-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="fs-2 text-warning"><i class="bi bi-shield-exclamation"></i></div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Menunggu Semakan Mengikut Hirarki</h6>
                        <p class="text-secondary small mb-2">
                            Permohonan ini kini sedang dalam peringkat semakan: 
                            <strong class="text-primary"><?= esc($stageInfo['officerTitle']) ?></strong> (<?= esc($stageInfo['description']) ?>).
                        </p>
                        <div class="alert alert-light border small text-muted mb-0">
                            <i class="bi bi-info-circle me-1 text-primary"></i>
                            <strong>Hirarki Rasmi:</strong> Pegawai Menyemak PE &rarr; Pegawai Perkhidmatan PE &rarr; Ketua J3P &rarr; Pengarah Hospital.
                            Tindakan kelulusan anda hanya akan diaktifkan setelah pegawai pada peringkat sebelumnya melengkapkan semakan mereka.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php else: ?>
    <!-- Keputusan Semakan Yang Telah Selesai (Lulus Penuh / Ditolak) -->
    <div class="card border-0 shadow-sm rounded-3 <?= $stage === 'approved' ? 'border-top border-success border-4' : 'border-top border-danger border-4' ?> mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0">
                <i class="bi <?= $stage === 'approved' ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' ?> me-2"></i>
                Status Akhir Permohonan
            </h6>
            <span class="badge <?= $stage === 'approved' ? 'bg-success' : 'bg-danger' ?> fs-6 px-3 py-1.5 rounded-pill">
                <?= $stage === 'approved' ? 'LULUS PENUH (PENGARAH)' : 'DITOLAK' ?>
            </span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="p-3 rounded bg-light border">
                        <div class="text-muted small">1. Pegawai Menyemak PE:</div>
                        <div class="fw-bold text-dark small mt-1"><?= esc($application['penyemak_reviewer_name'] ?? '-') ?></div>
                        <div class="text-muted" style="font-size: 0.72rem;"><?= !empty($application['penyemak_verified_at']) ? date('d/m/Y h:i A', strtotime($application['penyemak_verified_at'])) : '-' ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded bg-light border">
                        <div class="text-muted small">2. Pegawai Perkhidmatan PE:</div>
                        <div class="fw-bold text-dark small mt-1"><?= esc($application['perkhidmatan_reviewer_name'] ?? '-') ?></div>
                        <div class="text-muted" style="font-size: 0.72rem;"><?= !empty($application['perkhidmatan_verified_at']) ? date('d/m/Y h:i A', strtotime($application['perkhidmatan_verified_at'])) : '-' ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded bg-light border">
                        <div class="text-muted small">3. Ketua J3P:</div>
                        <div class="fw-bold text-dark small mt-1"><?= esc($application['j3p_reviewer_name'] ?? $application['jppp_reviewer_name'] ?? '-') ?></div>
                        <div class="text-muted" style="font-size: 0.72rem;"><?= !empty($application['j3p_verified_at']) ? date('d/m/Y h:i A', strtotime($application['j3p_verified_at'])) : (!empty($application['jppp_verified_at']) ? date('d/m/Y h:i A', strtotime($application['jppp_verified_at'])) : '-') ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded bg-light border">
                        <div class="text-muted small">4. Pengarah Hospital:</div>
                        <div class="fw-bold text-dark small mt-1"><?= esc($application['pengarah_reviewer_name'] ?? '-') ?></div>
                        <div class="text-muted" style="font-size: 0.72rem;"><?= !empty($application['pengarah_verified_at']) ? date('d/m/Y h:i A', strtotime($application['pengarah_verified_at'])) : '-' ?></div>
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

    const nextTarget = '<?= esc($stageInfo['nextOfficer'] ?? 'peringkat seterusnya') ?>';
    const currentOfficer = '<?= esc($stageInfo['officerTitle'] ?? 'Pegawai') ?>';

    const titleText = action === 'approve' 
        ? 'Sahkan Permohonan Ini?' 
        : 'Tolak Permohonan Tuntutan Ini?';

    const descText = action === 'approve' 
        ? (nextTarget ? 'Permohonan akan disahkan dan disalurkan kepada ' + nextTarget + '.' : 'Permohonan akan diluluskan penuh.')
        : 'Permohonan ini akan ditolak pada peringkat ' + currentOfficer + ' dan dimaklumkan kepada pemohon.';

    Swal.fire({
        title: titleText,
        text: descText,
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
                            text: res.message || 'Gagal memproses keputusan semakan.'
                        });
                        btn.prop('disabled', false).html('<i class="bi bi-send-check me-1"></i> Hantar Keputusan Semakan');
                    }
                },
                error: function (xhr) {
                    let errMsg = 'Sila cuba lagi sebentar lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Ralat Pelayan',
                        text: errMsg
                    });
                    btn.prop('disabled', false).html('<i class="bi bi-send-check me-1"></i> Hantar Keputusan Semakan');
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>
