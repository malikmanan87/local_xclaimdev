<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$procedures = !empty($application['procedures_data']) ? json_decode($application['procedures_data'], true) : [];

$penyemakStatus     = $application['penyemak_status'] ?? 'pending';
$perkhidmatanStatus = $application['perkhidmatan_status'] ?? 'pending';
$j3pStatus          = $application['j3p_status'] ?? 'pending';
$pengarahStatus     = $application['pengarah_status'] ?? 'pending';

// Overall status computation
if ($application['status'] === 'rejected' || in_array('rejected', [$penyemakStatus, $perkhidmatanStatus, $j3pStatus, $pengarahStatus])) {
    $mainBadgeClass = 'badge-status-danger';
    $mainStatusText = 'Ditolak';
} elseif ($pengarahStatus === 'approved' || $application['status'] === 'approved') {
    $mainBadgeClass = 'badge-status-success';
    $mainStatusText = 'Lulus Penuh (Pengarah)';
} elseif ($j3pStatus === 'approved') {
    $mainBadgeClass = 'badge-status-info';
    $mainStatusText = 'Menunggu Kelulusan Pengarah';
} elseif ($perkhidmatanStatus === 'approved') {
    $mainBadgeClass = 'badge-status-info';
    $mainStatusText = 'Menunggu Pengesahan Ketua J3P';
} elseif ($penyemakStatus === 'approved') {
    $mainBadgeClass = 'badge-status-info';
    $mainStatusText = 'Menunggu Pegawai Perkhidmatan PE';
} elseif ($application['status'] === 'submitted') {
    $mainBadgeClass = 'badge-status-warning';
    $mainStatusText = 'Menunggu Semakan Pegawai PE';
} else {
    $mainBadgeClass = 'badge-status-secondary';
    $mainStatusText = ucfirst($application['status']);
}

$isEditable = ($application['status'] === 'submitted' && 
               $penyemakStatus === 'pending' && 
               $perkhidmatanStatus === 'pending' && 
               $j3pStatus === 'pending' && 
               $pengarahStatus === 'pending');

$currentUserRole = strtolower(session('role_name') ?? session('role') ?? '');
$isOwnerOrStaff  = ($application['submitted_by'] == session('user_id')) || 
                   in_array($currentUserRole, ['admin', 'manager', 'pegawai_penyemak_pe', 'pegawai_perkhidmatan_pe', 'ketua_j3p', 'pengarah']);

$claimMonth = $application['claim_month'] ?? date('m', strtotime($application['submitted_at'] ?? 'now'));
$claimYear  = $application['claim_year'] ?? date('Y', strtotime($application['submitted_at'] ?? 'now'));

$monthNames = [
    '01' => 'JANUARI', '02' => 'FEBRUARI', '03' => 'MAC', '04' => 'APRIL',
    '05' => 'MEI', '06' => 'JUN', '07' => 'JULAI', '08' => 'OGOS',
    '09' => 'SEPTEMBER', '10' => 'OKTOBER', '11' => 'NOVEMBER', '12' => 'DISEMBER',
    '1' => 'JANUARI', '2' => 'FEBRUARI', '3' => 'MAC', '4' => 'APRIL',
    '5' => 'MEI', '6' => 'JUN', '7' => 'JULAI', '8' => 'OGOS',
    '9' => 'SEPTEMBER'
];
$monthText = $monthNames[(string)$claimMonth] ?? (string)$claimMonth;
?>

<!-- Top Action & Navigation Bar (Screen Only) -->
<div class="card shadow-sm border-0 rounded-3 mb-4 d-print-none">
    <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark font-monospace">HoSZA-MGT-J3P (PE)-F-003-01</span>
                <span class="badge-status <?= $mainBadgeClass ?> py-1 px-3"><?= $mainStatusText ?></span>
                <span class="badge bg-secondary-subtle text-secondary border font-monospace">Format Landskap A4</span>
            </div>
            <h5 class="fw-bold mb-0 mt-1 text-dark">
                Borang Tuntutan Bayaran Pakar: <span class="font-monospace text-primary"><?= esc($application['application_no']) ?></span>
            </h5>
            <div class="text-muted small">
                Tarikh Dihantar: <?= $application['submitted_at'] ? date('d/m/Y h:i A', strtotime($application['submitted_at'])) : '-' ?>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <?php if ($isEditable && $isOwnerOrStaff): ?>
                <a href="<?= base_url('new-application/edit/' . $application['id']) ?>" class="btn btn-warning btn-sm shadow-sm fw-semibold">
                    <i class="bi bi-pencil-square me-1"></i> Edit Permohonan
                </a>
            <?php endif; ?>
            <button type="button" class="btn btn-primary btn-sm shadow-sm fw-semibold" onclick="window.print()">
                <i class="bi bi-printer-fill me-1"></i> Cetak Borang (Landskap)
            </button>
            <a href="<?= base_url('new-application') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</div>

<!-- 5-Stage Approval Workflow Stepper (Screen Only) -->
<div class="card shadow-sm border-0 rounded-3 mb-4 d-print-none">
    <div class="card-header bg-light py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
        <span class="fw-bold small text-uppercase text-secondary">
            <i class="bi bi-diagram-3-fill text-primary me-1"></i> Aliran Kelulusan Tuntutan (5 Peringkat)
        </span>
        <span class="small text-muted">Borang PE Hospital Sultan Zainal Abidin</span>
    </div>
    <div class="card-body p-3">
        <div class="row g-2 text-center position-relative">
            <!-- Step 1: User Submit -->
            <div class="col">
                <div class="p-2 rounded-2 border border-success bg-success bg-opacity-10 h-100">
                    <div class="text-success fs-5 mb-1"><i class="bi bi-check-circle-fill"></i></div>
                    <div class="fw-bold small text-dark">1. Permohonan</div>
                    <div class="badge bg-success text-white my-1" style="font-size: 0.68rem;">Dihantar</div>
                    <div class="small text-muted" style="font-size: 0.72rem;">
                        <?= esc($application['specialist_name']) ?><br>
                        <?= $application['submitted_at'] ? date('d/m/y H:i', strtotime($application['submitted_at'])) : '' ?>
                    </div>
                </div>
            </div>

            <!-- Step 2: Pegawai Menyemak PE -->
            <div class="col">
                <div class="p-2 rounded-2 border <?= $penyemakStatus === 'approved' ? 'border-success bg-success bg-opacity-10' : ($penyemakStatus === 'rejected' ? 'border-danger bg-danger bg-opacity-10' : 'border-warning bg-warning bg-opacity-10') ?> h-100">
                    <div class="<?= $penyemakStatus === 'approved' ? 'text-success' : ($penyemakStatus === 'rejected' ? 'text-danger' : 'text-warning') ?> fs-5 mb-1">
                        <i class="bi <?= $penyemakStatus === 'approved' ? 'bi-check-circle-fill' : ($penyemakStatus === 'rejected' ? 'bi-x-circle-fill' : 'bi-hourglass-split') ?>"></i>
                    </div>
                    <div class="fw-bold small text-dark">2. Pegawai Menyemak PE</div>
                    <div class="badge <?= $penyemakStatus === 'approved' ? 'bg-success' : ($penyemakStatus === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') ?> my-1" style="font-size: 0.68rem;">
                        <?= $penyemakStatus === 'approved' ? 'Disemak' : ($penyemakStatus === 'rejected' ? 'Ditolak' : 'Menunggu') ?>
                    </div>
                    <div class="small text-muted" style="font-size: 0.72rem;">
                        <?= esc($application['penyemak_reviewer_name'] ?? '-') ?><br>
                        <?= !empty($application['penyemak_verified_at']) ? date('d/m/y H:i', strtotime($application['penyemak_verified_at'])) : '—' ?>
                    </div>
                </div>
            </div>

            <!-- Step 3: Pegawai Perkhidmatan PE -->
            <div class="col">
                <div class="p-2 rounded-2 border <?= $perkhidmatanStatus === 'approved' ? 'border-success bg-success bg-opacity-10' : ($perkhidmatanStatus === 'rejected' ? 'border-danger bg-danger bg-opacity-10' : ($penyemakStatus === 'approved' ? 'border-warning bg-warning bg-opacity-10' : 'border-light bg-light')) ?> h-100">
                    <div class="<?= $perkhidmatanStatus === 'approved' ? 'text-success' : ($perkhidmatanStatus === 'rejected' ? 'text-danger' : ($penyemakStatus === 'approved' ? 'text-warning' : 'text-secondary')) ?> fs-5 mb-1">
                        <i class="bi <?= $perkhidmatanStatus === 'approved' ? 'bi-check-circle-fill' : ($perkhidmatanStatus === 'rejected' ? 'bi-x-circle-fill' : 'bi-hourglass-split') ?>"></i>
                    </div>
                    <div class="fw-bold small text-dark">3. Pegawai Perkhidmatan PE</div>
                    <div class="badge <?= $perkhidmatanStatus === 'approved' ? 'bg-success' : ($perkhidmatanStatus === 'rejected' ? 'bg-danger' : ($penyemakStatus === 'approved' ? 'bg-warning text-dark' : 'bg-secondary')) ?> my-1" style="font-size: 0.68rem;">
                        <?= $perkhidmatanStatus === 'approved' ? 'Disahkan' : ($perkhidmatanStatus === 'rejected' ? 'Ditolak' : 'Menunggu') ?>
                    </div>
                    <div class="small text-muted" style="font-size: 0.72rem;">
                        <?= esc($application['perkhidmatan_reviewer_name'] ?? '-') ?><br>
                        <?= !empty($application['perkhidmatan_verified_at']) ? date('d/m/y H:i', strtotime($application['perkhidmatan_verified_at'])) : '—' ?>
                    </div>
                </div>
            </div>

            <!-- Step 4: Ketua J3P -->
            <div class="col">
                <div class="p-2 rounded-2 border <?= $j3pStatus === 'approved' ? 'border-success bg-success bg-opacity-10' : ($j3pStatus === 'rejected' ? 'border-danger bg-danger bg-opacity-10' : ($perkhidmatanStatus === 'approved' ? 'border-warning bg-warning bg-opacity-10' : 'border-light bg-light')) ?> h-100">
                    <div class="<?= $j3pStatus === 'approved' ? 'text-success' : ($j3pStatus === 'rejected' ? 'text-danger' : ($perkhidmatanStatus === 'approved' ? 'text-warning' : 'text-secondary')) ?> fs-5 mb-1">
                        <i class="bi <?= $j3pStatus === 'approved' ? 'bi-check-circle-fill' : ($j3pStatus === 'rejected' ? 'bi-x-circle-fill' : 'bi-hourglass-split') ?>"></i>
                    </div>
                    <div class="fw-bold small text-dark">4. Ketua J3P</div>
                    <div class="badge <?= $j3pStatus === 'approved' ? 'bg-success' : ($j3pStatus === 'rejected' ? 'bg-danger' : ($perkhidmatanStatus === 'approved' ? 'bg-warning text-dark' : 'bg-secondary')) ?> my-1" style="font-size: 0.68rem;">
                        <?= $j3pStatus === 'approved' ? 'Disahkan' : ($j3pStatus === 'rejected' ? 'Ditolak' : 'Menunggu') ?>
                    </div>
                    <div class="small text-muted" style="font-size: 0.72rem;">
                        <?= esc($application['j3p_reviewer_name'] ?? '-') ?><br>
                        <?= !empty($application['j3p_verified_at']) ? date('d/m/y H:i', strtotime($application['j3p_verified_at'])) : '—' ?>
                    </div>
                </div>
            </div>

            <!-- Step 5: Pengarah Hospital -->
            <div class="col">
                <div class="p-2 rounded-2 border <?= $pengarahStatus === 'approved' ? 'border-success bg-success bg-opacity-10' : ($pengarahStatus === 'rejected' ? 'border-danger bg-danger bg-opacity-10' : ($j3pStatus === 'approved' ? 'border-warning bg-warning bg-opacity-10' : 'border-light bg-light')) ?> h-100">
                    <div class="<?= $pengarahStatus === 'approved' ? 'text-success' : ($pengarahStatus === 'rejected' ? 'text-danger' : ($j3pStatus === 'approved' ? 'text-warning' : 'text-secondary')) ?> fs-5 mb-1">
                        <i class="bi <?= $pengarahStatus === 'approved' ? 'bi-check-circle-fill' : ($pengarahStatus === 'rejected' ? 'bi-x-circle-fill' : 'bi-hourglass-split') ?>"></i>
                    </div>
                    <div class="fw-bold small text-dark">5. Pengarah / KPTj</div>
                    <div class="badge <?= $pengarahStatus === 'approved' ? 'bg-success' : ($pengarahStatus === 'rejected' ? 'bg-danger' : ($j3pStatus === 'approved' ? 'bg-warning text-dark' : 'bg-secondary')) ?> my-1" style="font-size: 0.68rem;">
                        <?= $pengarahStatus === 'approved' ? 'Diluluskan' : ($pengarahStatus === 'rejected' ? 'Ditolak' : 'Menunggu') ?>
                    </div>
                    <div class="small text-muted" style="font-size: 0.72rem;">
                        <?= esc($application['pengarah_reviewer_name'] ?? '-') ?><br>
                        <?= !empty($application['pengarah_verified_at']) ? date('d/m/y H:i', strtotime($application['pengarah_verified_at'])) : '—' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================= -->
<!-- OFFICIAL FORM CONTAINER (HoSZA-MGT-J3P (PE)-F-003-01) - LANDSCAPE  -->
<!-- ================================================================= -->
<div class="official-form-sheet bg-white p-3 p-md-4 rounded-3 shadow-sm border mb-4">

    <!-- ───────────────────────────────────────────────────────────── -->
    <!-- HELAIAN 1 (LANDSKAP): Maklumat Pegawai & Butiran Tuntutan      -->
    <!-- ───────────────────────────────────────────────────────────── -->
    <div class="sheet-page sheet-page-1">
        <!-- Form Top Bar: Logo, Hospital, Document Code (Mengikut Format Lampiran Gambar) -->
        <div class="d-flex justify-content-between align-items-end mb-3">
            <div class="text-muted small font-monospace">
                No. Rujukan: <strong><?= esc($application['application_no']) ?></strong>
            </div>
            <div class="text-end d-flex flex-column align-items-end">
                <img src="<?= base_url('assets/img/unisza_logo.png') ?>" alt="Logo UniSZA" style="height: 58px; max-width: 230px; object-fit: contain;" class="mb-1">
                <div class="fw-semibold text-dark" style="font-size: 0.84rem; line-height: 1.25;">
                    Hospital Sultan Zainal Abidin <span class="fw-normal" style="color: #555;">| Sultan Zainal Abidin Hospital</span>
                </div>
                <div class="mt-1" style="border: 1px solid #000; padding: 2px 10px; font-weight: bold; font-family: monospace; font-size: 0.78rem; background: #fff; display: inline-block;">
                    HoSZA-MGT-J3P (PE)-F-003-01
                </div>
            </div>
        </div>

        <!-- Official Title (Mengikut Susunan Dua Baris Lampiran) -->
        <div class="text-center my-3">
            <h5 class="fw-bold text-dark text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 1.05rem; line-height: 1.3;">
                BORANG TUNTUTAN BAYARAN PAKAR DI BAWAH PERKHIDMATAN EKSEKUTIF
            </h5>
            <div class="fw-bold text-dark text-uppercase" style="font-size: 0.95rem;">
                (PE) BAGI BULAN <span class="border-bottom border-dark px-3 fw-bold"><?= esc($monthText) ?></span> 
                TAHUN <span class="border-bottom border-dark px-3 fw-bold"><?= esc($claimYear) ?></span>
            </div>
        </div>

        <!-- BAHAGIAN A: Maklumat Pegawai Yang Menuntut -->
        <div class="form-section-header bg-dark text-white fw-bold px-2 py-1 text-uppercase small mb-0">
            BAHAGIAN A: MAKLUMAT PEGAWAI YANG MENUNTUT
        </div>
        <table class="table table-bordered table-sm align-middle small mb-3 border-dark" style="font-size: 0.8rem;">
            <tbody>
                <tr>
                    <td width="50%">
                        <strong>NAMA PEGAWAI :</strong> <?= esc($application['specialist_name']) ?>
                    </td>
                    <td width="50%">
                        <strong>NO. KAD PENGENALAN :</strong> <?= esc($application['staff_ic'] ?? '-') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>NO. PEKERJA :</strong> <?= esc($application['staff_number']) ?>
                    </td>
                    <td>
                        <strong>JAWATAN & GRED :</strong> <?= esc($application['position']) ?> <?= !empty($application['grade']) ? '(' . esc($application['grade']) . ')' : '' ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>NO. TELEFON :</strong> <?= esc($application['phone'] ?? '-') ?>
                    </td>
                    <td>
                        <strong>E-MEL :</strong> <?= esc($application['email']) ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- BAHAGIAN B: Butiran Tuntutan (13 Kolum Landskap) -->
        <div class="form-section-header bg-dark text-white fw-bold px-2 py-1 text-uppercase small mb-0">
            BAHAGIAN B: BUTIRAN TUNTUTAN
        </div>
        <div class="table-responsive mb-0 print-no-overflow">
            <table class="table table-bordered table-sm align-middle text-center small mb-0 border-dark form-claims-table" style="font-size: 0.74rem;">
                <thead class="align-middle">
                    <tr class="fw-bold">
                        <th rowspan="2" style="width: 3%;" class="bg-light">BIL.</th>
                        <th rowspan="2" style="width: 11%;" class="bg-light">NAMA PESAKIT</th>
                        <th rowspan="2" style="width: 6%;" class="bg-light">NO. R/N</th>
                        <th rowspan="2" style="width: 23%;" class="bg-light">
                            PROSEDUR / PERKHIDMATAN<br>
                            <span class="fw-normal text-muted fst-italic" style="font-size: 0.58rem;">** Sila Ke Lampiran 1 Jika Ruang Tidak Mencukupi</span>
                        </th>
                        <th rowspan="2" style="width: 6%;" class="bg-light">TARIKH BIL.</th>
                        <th rowspan="2" style="width: 6%;" class="bg-light">NO. RESIT</th>
                        <th colspan="2" style="width: 12.5%;" class="bg-light">CAJ RUNDINGAN</th>
                        <th colspan="2" style="width: 12.5%;" class="bg-light">CAJ TATACARA</th>
                        <th colspan="2" style="width: 12.5%;" class="bg-light">CAJ PELAPORAN PERUBATAN</th>
                        <th rowspan="2" style="width: 7.5%;" class="bg-light">JUMLAH TUNTUTAN (RM)</th>
                    </tr>
                    <tr class="fw-bold claims-subhead">
                        <th style="width: 6.25%;" class="bg-light">KADAR CAJ<br>(RM)</th>
                        <th style="width: 6.25%;" class="bg-light">AGIHAN PAKAR<br>(75%) (RM)</th>
                        <th style="width: 6.25%;" class="bg-light">KADAR CAJ<br>(RM)</th>
                        <th style="width: 6.25%;" class="bg-light">AGIHAN PAKAR<br>(75%) (RM)</th>
                        <th style="width: 6.25%;" class="bg-light">KADAR CAJ<br>(RM)</th>
                        <th style="width: 6.25%;" class="bg-light">AGIHAN PAKAR<br>(70%) (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totRundKadar = 0; $totRundAgihan = 0;
                    $totTataKadar = 0; $totTataAgihan = 0;
                    $totPelaKadar = 0; $totPelaAgihan = 0;
                    $totKeseluruhanTuntutan = 0;

                    // Baris mengikut kapasiti teks prosedur sebenar
                    $rowCount = max(count($procedures), 1);

                    for ($i = 0; $i < $rowCount; $i++):
                        $p = $procedures[$i] ?? null;

                        if ($p):
                            $fee = (float)($p['price'] ?? 0);
                            $cType = $p['charge_type'] ?? 'tatacara';
                            $defaultPct = $cType === 'pelaporan' ? 70 : 75;
                            $pct = isset($p['claimPct']) ? (float)$p['claimPct'] : $defaultPct;
                            $claimAmt = $fee * ($pct / 100);
                            $totKeseluruhanTuntutan += $claimAmt;

                            $rundKadar = '-'; $rundAgihan = '-';
                            $tataKadar = '-'; $tataAgihan = '-';
                            $pelaKadar = '-'; $pelaAgihan = '-';

                            if ($cType === 'rundingan') {
                                $totRundKadar += $fee; $totRundAgihan += $claimAmt;
                                $rundKadar = number_format($fee, 2); $rundAgihan = number_format($claimAmt, 2);
                            } elseif ($cType === 'pelaporan') {
                                $totPelaKadar += $fee; $totPelaAgihan += $claimAmt;
                                $pelaKadar = number_format($fee, 2); $pelaAgihan = number_format($claimAmt, 2);
                            } else {
                                $totTataKadar += $fee; $totTataAgihan += $claimAmt;
                                $tataKadar = number_format($fee, 2); $tataAgihan = number_format($claimAmt, 2);
                            }

                            $pName = $application['patient_name'] ?? '-';
                            $pRn   = $application['patient_rn'] ?? '-';
                            $billDate = !empty($p['bill_date']) ? date('d/m/Y', strtotime($p['bill_date'])) : '-';
                            $receiptNo = !empty($p['receipt_no']) ? $p['receipt_no'] : '-';
                    ?>
                        <tr>
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td class="text-start fw-semibold"><?= esc($pName) ?></td>
                            <td class="font-monospace"><?= esc($pRn) ?></td>
                            <td class="text-start">
                                <span class="font-monospace fw-semibold text-primary"><?= esc($p['code'] ?? '') ?></span>
                                <?= esc($p['name'] ?? '-') ?>
                            </td>
                            <td><?= esc($billDate) ?></td>
                            <td class="font-monospace"><?= esc($receiptNo) ?></td>
                            <td class="text-end font-monospace"><?= $rundKadar ?></td>
                            <td class="text-end font-monospace fw-semibold"><?= $rundAgihan ?></td>
                            <td class="text-end font-monospace"><?= $tataKadar ?></td>
                            <td class="text-end font-monospace fw-semibold"><?= $tataAgihan ?></td>
                            <td class="text-end font-monospace"><?= $pelaKadar ?></td>
                            <td class="text-end font-monospace fw-semibold"><?= $pelaAgihan ?></td>
                            <td class="text-end font-monospace fw-bold text-success"><?= number_format($claimAmt, 2) ?></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td class="text-center text-muted">1</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                            <td class="text-end">-</td>
                        </tr>
                    <?php endif; endfor; ?>
                </tbody>
                <tfoot class="fw-bold align-middle border-dark">
                    <tr class="bg-light">
                        <td colspan="6" class="text-end text-uppercase">JUMLAH (RM):</td>
                        <td class="text-end font-monospace"><?= number_format($totRundKadar, 2) ?></td>
                        <td class="text-end font-monospace text-primary"><?= number_format($totRundAgihan, 2) ?></td>
                        <td class="text-end font-monospace"><?= number_format($totTataKadar, 2) ?></td>
                        <td class="text-end font-monospace text-dark"><?= number_format($totTataAgihan, 2) ?></td>
                        <td class="text-end font-monospace"><?= number_format($totPelaKadar, 2) ?></td>
                        <td class="text-end font-monospace text-dark"><?= number_format($totPelaAgihan, 2) ?></td>
                        <td class="text-end font-monospace text-success fs-6 fw-bold">
                            <?= number_format($totKeseluruhanTuntutan > 0 ? $totKeseluruhanTuntutan : ($application['total_claim'] ?? 0), 2) ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- BAHAGIAN C: MAKLUMAT PENGESAHAN (Mengalir terus selepas Bahagian B mengikut kapasiti teks) -->
        <div class="form-section-header bg-dark text-white fw-bold px-2 py-1 text-uppercase small mb-0 mt-3">
            BAHAGIAN C: MAKLUMAT PENGESAHAN
        </div>
        <div class="border border-dark p-3 mb-0">
            <p class="mb-3 text-dark" style="font-size: 0.88rem;">
                Dengan ini saya mengesahkan bahawa Tuntutan Bayaran Pakar di bawah Perkhidmatan Eksekutif seperti maklumat yang disediakan adalah betul dan dilaksanakan oleh saya.
            </p>
            <div class="row">
                <div class="col-sm-6 offset-sm-3 text-center pt-2">
                    <div class="border-top border-dark pt-1">
                        <div class="fw-bold text-dark"><?= esc($application['specialist_name']) ?></div>
                        <div class="text-muted small">Tandatangan dan Cop Pegawai</div>
                        <div class="small mt-1">
                            <strong>Tarikh:</strong> <?= !empty($application['user_declared_at']) ? date('d/m/Y', strtotime($application['user_declared_at'])) : date('d/m/Y', strtotime($application['submitted_at'] ?? 'now')) ?>
                            <span class="badge bg-success-subtle text-success border border-success ms-1 font-monospace d-print-none">Disahkan Digital</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ───────────────────────────────────────────────────────────── -->
    <!-- HELAIAN BARU: Bahagian D & Bahagian E (Pengesahan Pegawai)    -->
    <!-- ───────────────────────────────────────────────────────────── -->
    <div class="print-page-break my-4"></div>

    <div class="sheet-page sheet-page-officers">
        <div class="d-none d-print-flex justify-content-between align-items-center border-bottom pb-1 mb-2">
            <div class="d-flex align-items-center gap-2">
                <img src="<?= base_url('assets/img/unisza_logo.png') ?>" alt="Logo UniSZA" style="height: 28px; object-fit: contain;">
                <span class="font-monospace small fw-bold">HoSZA-MGT-J3P (PE)-F-003-01 &bull; <?= esc($application['application_no']) ?></span>
            </div>
            <span class="small font-monospace">Helaian Pengesahan Pegawai</span>
        </div>

        <!-- BAHAGIAN D: Pengesahan Pegawai Penyemak dan Pegawai Perkhidmatan Eksekutif -->
        <div class="form-section-header bg-dark text-white fw-bold px-2 py-1 text-uppercase small mb-0">
            BAHAGIAN D: PENGESAHAN PEGAWAI PENYEMAK DAN PEGAWAI PERKHIDMATAN EKSEKUTIF
        </div>
        <div class="border border-dark p-3 mb-3">
            <p class="mb-3 text-dark" style="font-size: 0.86rem;">
                Dengan ini saya mengesahkan bahawa pegawai telah menjalankan perkhidmatan sebagaimana yang dituntut. Tuntutan berjumlah 
                <strong>RM <?= number_format($application['total_claim'] ?? 0, 2) ?></strong> dan dokumen yang disertakan adalah benar.
            </p>
            <div class="row g-3 text-center">
                <div class="col-6">
                    <div class="p-2 border rounded bg-light d-flex flex-column justify-content-between h-100 officer-sign-box" style="min-height: 125px;">
                        <div>
                            <div class="small fw-semibold text-secondary">PEGAWAI PENYEMAK PE</div>
                            <div class="officer-status-badge d-print-none">
                                <?php if ($penyemakStatus === 'approved'): ?>
                                    <span class="badge bg-success my-1">Disemak & Disahkan</span>
                                <?php elseif ($penyemakStatus === 'rejected'): ?>
                                    <span class="badge bg-danger my-1">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark my-1">Menunggu Semakan</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="border-top border-dark pt-1 mt-2">
                            <div class="fw-bold small text-dark"><?= esc($application['penyemak_reviewer_name'] ?? 'Tandatangan dan Cop Pegawai Penyemak') ?></div>
                            <div class="small text-muted">
                                Tarikh: <?= !empty($application['penyemak_verified_at']) ? date('d/m/Y', strtotime($application['penyemak_verified_at'])) : '................................' ?>
                            </div>
                            <?php if (!empty($application['penyemak_remarks'])): ?>
                                <div class="small text-muted fst-italic mt-1">Nota: "<?= esc($application['penyemak_remarks']) ?>"</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 border rounded bg-light d-flex flex-column justify-content-between h-100 officer-sign-box" style="min-height: 125px;">
                        <div>
                            <div class="small fw-semibold text-secondary">PEGAWAI PERKHIDMATAN EKSEKUTIF</div>
                            <div class="officer-status-badge d-print-none">
                                <?php if ($perkhidmatanStatus === 'approved'): ?>
                                    <span class="badge bg-success my-1">Disahkan</span>
                                <?php elseif ($perkhidmatanStatus === 'rejected'): ?>
                                    <span class="badge bg-danger my-1">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark my-1">Menunggu Tindakan</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="border-top border-dark pt-1 mt-2">
                            <div class="fw-bold small text-dark"><?= esc($application['perkhidmatan_reviewer_name'] ?? 'Tandatangan dan Cop Pegawai Perkhidmatan Eksekutif') ?></div>
                            <div class="small text-muted">
                                Tarikh: <?= !empty($application['perkhidmatan_verified_at']) ? date('d/m/Y', strtotime($application['perkhidmatan_verified_at'])) : '................................' ?>
                            </div>
                            <?php if (!empty($application['perkhidmatan_remarks'])): ?>
                                <div class="small text-muted fst-italic mt-1">Nota: "<?= esc($application['perkhidmatan_remarks']) ?>"</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BAHAGIAN E: Pengesahan Ketua J3P dan Pengarah -->
        <div class="form-section-header bg-dark text-white fw-bold px-2 py-1 text-uppercase small mb-0">
            BAHAGIAN E: PENGESAHAN KETUA JABATAN PEMBANGUNAN PERNIAGAAN DAN PELABURAN (J3P) DAN KETUA PUSAT TANGGUNGJAWAB (KPTj)/PENGARAH
        </div>
        <div class="border border-dark p-3 mb-0">
            <p class="mb-3 text-dark" style="font-size: 0.86rem;">
                Dengan ini saya mengesahkan bahawa pegawai telah menjalankan perkhidmatan sebagaimana yang dituntut. Tuntutan berjumlah 
                <strong>RM <?= number_format($application['total_claim'] ?? 0, 2) ?></strong> dan dokumen yang disertakan adalah benar sebagaimana yang disemak oleh Pegawai Penyemak dan Pegawai Perkhidmatan Eksekutif.
            </p>
            <div class="row g-3 text-center">
                <div class="col-6">
                    <div class="p-2 border rounded bg-light d-flex flex-column justify-content-between h-100 officer-sign-box" style="min-height: 125px;">
                        <div>
                            <div class="small fw-semibold text-secondary">KETUA JABATAN J3P</div>
                            <div class="officer-status-badge d-print-none">
                                <?php if ($j3pStatus === 'approved'): ?>
                                    <span class="badge bg-success my-1">Disahkan & Disokong</span>
                                <?php elseif ($j3pStatus === 'rejected'): ?>
                                    <span class="badge bg-danger my-1">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark my-1">Menunggu Tindakan</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="border-top border-dark pt-1 mt-2">
                            <div class="fw-bold small text-dark"><?= esc($application['j3p_reviewer_name'] ?? 'Tandatangan dan Cop Ketua J3P') ?></div>
                            <div class="small text-muted">
                                Tarikh: <?= !empty($application['j3p_verified_at']) ? date('d/m/Y', strtotime($application['j3p_verified_at'])) : '................................' ?>
                            </div>
                            <?php if (!empty($application['j3p_remarks'])): ?>
                                <div class="small text-muted fst-italic mt-1">Nota: "<?= esc($application['j3p_remarks']) ?>"</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 border rounded bg-light d-flex flex-column justify-content-between h-100 officer-sign-box" style="min-height: 125px;">
                        <div>
                            <div class="small fw-semibold text-secondary">KETUA PUSAT TANGGUNGJAWAB (KPTj) / PENGARAH</div>
                            <div class="officer-status-badge d-print-none">
                                <?php if ($pengarahStatus === 'approved'): ?>
                                    <span class="badge bg-success my-1">Diluluskan Penuh</span>
                                <?php elseif ($pengarahStatus === 'rejected'): ?>
                                    <span class="badge bg-danger my-1">Ditolak</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark my-1">Menunggu Kelulusan</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="border-top border-dark pt-1 mt-2">
                            <div class="fw-bold small text-dark"><?= esc($application['pengarah_reviewer_name'] ?? 'Tandatangan dan Cop KPTj/Pengarah') ?></div>
                            <div class="small text-muted">
                                Tarikh: <?= !empty($application['pengarah_verified_at']) ? date('d/m/Y', strtotime($application['pengarah_verified_at'])) : '................................' ?>
                            </div>
                            <?php if (!empty($application['pengarah_remarks'])): ?>
                                <div class="small text-muted fst-italic mt-1">Nota: "<?= esc($application['pengarah_remarks']) ?>"</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ───────────────────────────────────────────────────────────── -->
    <!-- HELAIAN BARU: LAMPIRAN 1 (Senarai Prosedur 1 - 16)            -->
    <!-- ───────────────────────────────────────────────────────────── -->
    <div class="print-page-break my-4"></div>

    <div class="sheet-page sheet-page-lampiran">
        <div class="d-none d-print-flex justify-content-between align-items-center border-bottom pb-1 mb-2">
            <div class="d-flex align-items-center gap-2">
                <img src="<?= base_url('assets/img/unisza_logo.png') ?>" alt="Logo UniSZA" style="height: 28px; object-fit: contain;">
                <span class="font-monospace small fw-bold">HoSZA-MGT-J3P (PE)-F-003-01 &bull; <?= esc($application['application_no']) ?></span>
            </div>
            <span class="small font-monospace">Lampiran 1</span>
        </div>

        <div class="form-section-header bg-dark text-white fw-bold px-2 py-1 text-uppercase small mb-0 d-flex justify-content-between align-items-center">
            <span>LAMPIRAN 1: SENARAI PROSEDUR / PERKHIDMATAN</span>
            <span class="font-monospace" style="font-size: 0.68rem;">HoSZA-MGT-J3P (PE)-F-003-01</span>
        </div>
        <table class="table table-bordered table-sm align-middle small mb-0 border-dark">
            <thead class="table-light">
                <tr>
                    <th width="6%" class="text-center">BIL.</th>
                    <th>PROSEDUR / PERKHIDMATAN</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($procedures)): ?>
                    <?php foreach ($procedures as $idx => $procItem): ?>
                        <tr>
                            <td class="text-center fw-semibold text-muted" style="width: 6%;"><?= $idx + 1 ?></td>
                            <td>
                                <span class="font-monospace fw-semibold text-primary"><?= esc($procItem['code'] ?? '') ?></span>
                                <?php if (!empty($procItem['code'])): ?> - <?php endif; ?>
                                <?= esc($procItem['name'] ?? '-') ?> 
                                <span class="text-muted small">
                                    (Kategori: <?= strtoupper(esc($procItem['charge_type'] ?? 'TATACARA')) ?> &bull; Nilai: RM <?= number_format($procItem['price'] ?? 0, 2) ?>)
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="text-center text-muted" style="width: 6%;">1</td>
                        <td class="text-muted fst-italic">Tiada maklumat prosedur direkodkan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- Additional Notes / Remarks (Screen Only) -->
<?php if (!empty($application['remarks'])): ?>
    <div class="card shadow-sm border-0 rounded-3 bg-light mb-4 d-print-none">
        <div class="card-body p-3 small">
            <span class="fw-semibold text-secondary"><i class="bi bi-chat-left-text me-1"></i> Catatan Pemohon:</span>
            <p class="mb-0 text-dark mt-1"><?= nl2br(esc($application['remarks'])) ?></p>
        </div>
    </div>
<?php endif; ?>

<!-- Print Stylesheet (LANDSCAPE ORIENTATION) -->
<style>
@media screen {
    .official-form-sheet {
        max-width: 1240px;
        margin: 0 auto;
    }
}

@media print {
    /* Set Page Orientation to LANDSCAPE */
    @page {
        size: A4 landscape;
        margin: 0.7cm 0.9cm;
    }

    /* Hide layout chrome */
    .sidebar,
    .topbar,
    .page-header,
    .page-footer,
    .btn,
    .d-print-none,
    nav[aria-label="breadcrumb"] {
        display: none !important;
    }

    .main-wrapper,
    .page-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }

    body {
        background-color: #fff !important;
        font-size: 8.5pt !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .official-form-sheet {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .form-section-header {
        background-color: #000 !important;
        color: #fff !important;
        font-size: 8pt !important;
        padding: 3px 6px !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    .table {
        border-color: #000 !important;
        margin-bottom: 0.25rem !important;
    }

    .table th,
    .table td {
        border-color: #000 !important;
        padding: 3px 4px !important;
    }

    .table thead th {
        background-color: #f0f0f0 !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Pastikan Bahagian B (13 Kolum) muat tepat dalam Landskap tanpa scroll / overflow */
    .table-responsive,
    .print-no-overflow {
        overflow: visible !important;
        display: block !important;
        width: 100% !important;
    }

    .form-claims-table {
        width: 100% !important;
        table-layout: fixed !important;
        font-size: 6.8pt !important;
    }

    .form-claims-table th,
    .form-claims-table td {
        vertical-align: middle !important;
    }

    .form-claims-table td {
        padding: 3px 2px !important;
        line-height: 1.2 !important;
        word-break: break-word !important;
    }

    /* Elak pertindihan teks pada tajuk kolum semasa cetakan */
    .form-claims-table thead th {
        line-height: 1.35 !important;
        word-break: normal !important;
        overflow-wrap: normal !important;
        white-space: normal !important;
        hyphens: none !important;
    }

    .form-claims-table thead tr:first-child th {
        font-size: 6.5pt !important;
        padding: 4px 1px !important;
        line-height: 1.25 !important;
    }

    .form-claims-table thead tr:last-child th,
    .form-claims-table thead .claims-subhead th {
        font-size: 5.6pt !important;
        padding: 3px 1px !important;
        line-height: 1.35 !important;
    }

    /* Sembunyikan badge status setiap pegawai pada borang cetakan fizikal */
    .officer-status-badge,
    .badge-status {
        display: none !important;
    }

    /* Kotak pengesahan pegawai kelihatan kemas seperti borang rasmi kerajaan */
    .officer-sign-box {
        background-color: transparent !important;
        border: 1px solid #000 !important;
        min-height: 110px !important;
    }

    .print-page-break {
        page-break-before: always;
        break-before: page;
        margin: 0 !important;
        padding: 0 !important;
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
