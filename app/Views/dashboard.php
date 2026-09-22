<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$statusBadgeMap = [
    'draft'        => ['badge-secondary text-secondary-emphasis bg-secondary-subtle border border-secondary-subtle', 'Draf'],
    'submitted'    => ['badge-status-warning',   'Dihantar'],
    'under_review' => ['badge-status-info',      'Dalam Semakan'],
    'approved'     => ['badge-status-success',   'Diluluskan'],
    'rejected'     => ['badge-status-danger',    'Ditolak'],
];

$userDisplayName = esc(session('fullname') ?? session('name') ?? 'Pakar Perubatan');
$userRoleName    = esc(ucfirst(session('role_name') ?? session('role') ?? 'Pakar'));
?>

<!-- Header Banner -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-speedometer2 text-primary me-2"></i>Dashboard Pengurusan Tuntutan (X-Claim)
            </h4>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small">
                <?= $userRoleName ?>
            </span>
        </div>
        <p class="text-muted small mb-0">
            Selamat kembali, <strong><?= $userDisplayName ?></strong>. Berikut adalah ringkasan status & statistik tuntutan perkhidmatan pakar Hospital Sultan Zainal Abidin (HoSZA)<?= !empty($isOnlySelf) ? ' bagi rekod peribadi anda.' : ' (Keseluruhan Hospital).' ?>
        </p>
    </div>

    <!-- Date Info -->
    <div class="d-flex align-items-center">
        <span class="badge bg-white text-dark border px-3 py-2 shadow-sm rounded-pill small">
            <i class="bi bi-calendar3 text-primary me-1"></i> <?= date('d M Y') ?>
        </span>
    </div>
</div>

<!-- Row 1: 4 KPI Summary Cards -->
<div class="row g-3 mb-4">

    <!-- Card 1: Total Applications -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card-primary h-100">
            <div class="stat-info">
                <span class="stat-label">Jumlah Permohonan</span>
                <span class="stat-value text-primary"><?= number_format($totalClaims) ?></span>
                <span class="stat-change positive">
                    <i class="bi bi-check2-all me-1"></i><?= $approvedClaims ?> Diluluskan &bull; <?= $draftClaims ?> Draf
                </span>
            </div>
            <div class="stat-icon">
                <i class="bi bi-file-earmark-medical-fill"></i>
            </div>
        </div>
    </div>

    <!-- Card 2: Net Claim Amount -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card-success h-100">
            <div class="stat-info">
                <span class="stat-label">Jumlah Bersih Dituntut</span>
                <span class="stat-value text-success font-monospace" style="font-size: 1.65rem;">
                    RM <?= number_format($totalClaimAmount, 2) ?>
                </span>
                <span class="stat-change text-muted">
                    <i class="bi bi-cash me-1"></i>Nilai Kasar: RM <?= number_format($totalGrossAmount, 2) ?>
                </span>
            </div>
            <div class="stat-icon">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>

    <!-- Card 3: Pending Review -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card-warning h-100">
            <div class="stat-info">
                <span class="stat-label">Menunggu Kelulusan</span>
                <span class="stat-value text-warning-emphasis"><?= number_format($pendingClaims) ?></span>
                <span class="stat-change text-warning-emphasis">
                    <i class="bi bi-hourglass-split me-1"></i>Dalam Aliran Semakan Pegawai
                </span>
            </div>
            <div class="stat-icon">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
    </div>

    <!-- Card 4: Welfare Fund -->
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card-danger h-100">
            <div class="stat-info">
                <span class="stat-label">Tabung Kebajikan (Pilihan)</span>
                <span class="stat-value text-danger font-monospace" style="font-size: 1.65rem;">
                    RM <?= number_format($totalWelfareAmount, 2) ?>
                </span>
                <span class="stat-change text-muted">
                    <i class="bi bi-heart-pulse-fill text-danger me-1"></i>Sumbangan Kebajikan Hospital
                </span>
            </div>
            <div class="stat-icon">
                <i class="bi bi-heart-fill"></i>
            </div>
        </div>
    </div>

</div>

<!-- Row 2: Visual Analytics & Charts -->
<div class="row g-4 mb-4">

    <!-- Monthly Claims Trend (Bar & Line Chart) -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
                <div>
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-graph-up text-primary me-2"></i>Trend Tuntutan Bulanan
                    </h6>
                    <small class="text-muted">Statistik jumlah tuntutan (RM) dan bilangan permohonan tahun <?= date('Y') ?></small>
                </div>
                <span class="badge bg-light text-dark border px-2.5 py-1.5 small">
                    Tahun <?= date('Y') ?>
                </span>
            </div>
            <div class="card-body p-3">
                <div style="position: relative; height: 320px; width: 100%;">
                    <canvas id="monthlyClaimsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Breakdown (Donut Chart) -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
                <div>
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-pie-chart-fill text-info me-2"></i>Status Permohonan
                    </h6>
                    <small class="text-muted">Pecahan status permohonan tuntutan</small>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                    <?= $totalClaims ?> Jumlah
                </span>
            </div>
            <div class="card-body p-3 d-flex flex-column justify-content-between">
                <div style="position: relative; height: 210px; width: 100%;">
                    <canvas id="statusDonutChart"></canvas>
                </div>
                <div class="mt-3 pt-2 border-top">
                    <div class="row g-2 text-center small">
                        <div class="col-4">
                            <div class="p-1 rounded bg-light">
                                <div class="text-muted" style="font-size: 0.72rem;">Dihantar</div>
                                <span class="fw-bold text-warning"><?= $statusCounts['submitted'] ?? 0 ?></span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-1 rounded bg-light">
                                <div class="text-muted" style="font-size: 0.72rem;">Diluluskan</div>
                                <span class="fw-bold text-success"><?= $statusCounts['approved'] ?? 0 ?></span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-1 rounded bg-light">
                                <div class="text-muted" style="font-size: 0.72rem;">Draf / Lain</div>
                                <span class="fw-bold text-secondary"><?= ($statusCounts['draft'] ?? 0) + ($statusCounts['under_review'] ?? 0) + ($statusCounts['rejected'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Row 3: Recent Claims Table & Activity Logs -->
<div class="row g-4">

    <!-- Recent Claims Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-bottom">
                <div>
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-clock-history text-primary me-2"></i>Permohonan Tuntutan Terkini
                    </h6>
                    <small class="text-muted"><?= !empty($isOnlySelf) ? 'Senarai permohonan terkini yang telah anda hantar' : 'Senarai permohonan terkini yang telah dihantar oleh pakar' ?></small>
                </div>
                <a href="<?= base_url('new-application') ?>" class="btn btn-outline-primary btn-sm px-3">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>No. Permohonan</th>
                                <th>Pakar / Pemohon</th>
                                <th>Pesakit & RN</th>
                                <th class="text-end">Jumlah Bersih (RM)</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentApplications)): ?>
                                <?php foreach ($recentApplications as $app): ?>
                                    <?php 
                                    $st = $statusBadgeMap[$app['status']] ?? ['badge-status-secondary', ucfirst($app['status'])];
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?= base_url('new-application/show/' . $app['id']) ?>" class="font-monospace fw-bold text-primary text-decoration-none">
                                                <?= esc($app['application_no']) ?>
                                            </a>
                                            <div class="text-muted" style="font-size: 0.72rem;">
                                                <?= date('d/m/Y h:i A', strtotime($app['created_at'])) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark text-truncate" style="max-width: 170px;">
                                                <?= esc($app['specialist_name']) ?>
                                            </div>
                                            <small class="text-muted" style="font-size: 0.72rem;">
                                                Staf: <?= esc($app['staff_number']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark text-truncate" style="max-width: 180px;">
                                                <?= esc($app['patient_name'] ?? '-') ?>
                                            </div>
                                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.72rem;">
                                                RN: <?= esc($app['patient_rn'] ?? '-') ?>
                                            </span>
                                        </td>
                                        <td class="text-end font-monospace fw-bold text-success">
                                            RM <?= number_format($app['total_claim'] ?? 0, 2) ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-status <?= $st[0] ?>"><?= $st[1] ?></span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <a href="<?= base_url('new-application/show/' . $app['id']) ?>" class="btn btn-light border py-1 px-2" title="Lihat Terperinci">
                                                    <i class="bi bi-eye text-primary"></i>
                                                </a>
                                                <a href="<?= base_url('new-application/show/' . $app['id'] . '?print=1') ?>" class="btn btn-light border py-1 px-2" title="Cetak Permohonan" target="_blank">
                                                    <i class="bi bi-printer text-dark"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted fst-italic">
                                        <i class="bi bi-info-circle me-1"></i> Tiada rekod permohonan tuntutan dijumpai.
                                        <div class="mt-2">
                                            <a href="<?= base_url('new-application/create') ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-plus-circle me-1"></i> Buat Permohonan Pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    
    // ──────────────────────────────────────────────────────────────────
    // 1. Monthly Claims Trend Chart (Bar: Amount RM, Line: Count)
    // ──────────────────────────────────────────────────────────────────
    const ctxMonthly = document.getElementById('monthlyClaimsChart');
    if (ctxMonthly) {
        const monthlyLabels  = <?= json_encode($monthlyLabels) ?>;
        const monthlyAmounts = <?= json_encode($monthlyAmounts) ?>;
        const monthlyCounts  = <?= json_encode($monthlyCounts) ?>;

        new Chart(ctxMonthly.getContext('2d'), {
            data: {
                labels: monthlyLabels,
                datasets: [
                    {
                        type: 'bar',
                        label: 'Nilai Bersih Tuntutan (RM)',
                        data: monthlyAmounts,
                        backgroundColor: 'rgba(13, 110, 253, 0.75)',
                        borderColor: '#0d6efd',
                        borderRadius: 6,
                        borderWidth: 1,
                        yAxisID: 'yAmount',
                        order: 2
                    },
                    {
                        type: 'line',
                        label: 'Bil. Permohonan',
                        data: monthlyCounts,
                        borderColor: '#198754',
                        backgroundColor: '#198754',
                        pointBackgroundColor: '#198754',
                        pointBorderColor: '#fff',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.3,
                        borderWidth: 2,
                        yAxisID: 'yCount',
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    yAmount: {
                        type: 'linear',
                        position: 'left',
                        beginAtZero: true,
                        ticks: {
                            callback: function(val) {
                                return 'RM ' + Number(val).toLocaleString('en-US', { minimumFractionDigits: 0 });
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    yCount: {
                        type: 'linear',
                        position: 'right',
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1
                        },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.yAxisID === 'yAmount') {
                                    return ` Nilai Tuntutan: RM ${Number(context.raw).toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
                                } else {
                                    return ` Bilangan Permohonan: ${context.raw}`;
                                }
                            }
                        }
                    }
                }
            }
        });
    }

    // ──────────────────────────────────────────────────────────────────
    // 2. Status Distribution Donut Chart
    // ──────────────────────────────────────────────────────────────────
    const ctxStatus = document.getElementById('statusDonutChart');
    if (ctxStatus) {
        const statusData = [
            <?= (int) ($statusCounts['submitted'] ?? 0) ?>,
            <?= (int) ($statusCounts['under_review'] ?? 0) ?>,
            <?= (int) ($statusCounts['approved'] ?? 0) ?>,
            <?= (int) ($statusCounts['rejected'] ?? 0) ?>,
            <?= (int) ($statusCounts['draft'] ?? 0) ?>
        ];

        new Chart(ctxStatus.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Dihantar', 'Dalam Semakan', 'Diluluskan', 'Ditolak', 'Draf'],
                datasets: [{
                    data: statusData,
                    backgroundColor: [
                        '#ffc107', // Warning (submitted)
                        '#0dcaf0', // Info (under review)
                        '#198754', // Success (approved)
                        '#dc3545', // Danger (rejected)
                        '#6c757d'  // Secondary (draft)
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ` ${context.label}: ${context.raw} permohonan`;
                            }
                        }
                    }
                }
            }
        });
    }

});
</script>
<?= $this->endSection() ?>