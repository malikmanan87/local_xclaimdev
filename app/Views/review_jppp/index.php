<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">
            <i class="bi bi-clipboard2-pulse-fill text-primary me-2"></i>Semakan Permohonan JPPP
        </h4>
        <p class="text-muted small mb-0">
            Modul pengesahan dan semakan berperingkat mengikut hirarki rasmi: 
            <strong>Pegawai Menyemak PE &rarr; Pegawai Perkhidmatan PE &rarr; Ketua J3P &rarr; Pengarah Hospital</strong>.
        </p>
    </div>
    <div>
        <span class="badge bg-white text-dark border px-3 py-2 shadow-sm rounded-pill small">
            <i class="bi bi-calendar3 text-primary me-1"></i> <?= date('d M Y') ?>
        </span>
    </div>
</div>

<!-- 4 KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card stat-card-warning h-100">
            <div class="stat-info">
                <span class="stat-label">Menunggu Semakan</span>
                <span class="stat-value text-warning-emphasis"><?= number_format($stats['pending']) ?></span>
                <span class="stat-change text-warning-emphasis">
                    <i class="bi bi-hourglass-split me-1"></i>Dalam aliran kelulusan
                </span>
            </div>
            <div class="stat-icon">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card stat-card-primary h-100">
            <div class="stat-info">
                <span class="stat-label">Tindakan Giliran Anda</span>
                <span class="stat-value text-primary"><?= number_format($stats['myTurn']) ?></span>
                <span class="stat-change text-primary">
                    <i class="bi bi-bell-fill me-1"></i>Perlu tindakan peranan anda
                </span>
            </div>
            <div class="stat-icon">
                <i class="bi bi-person-check-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card stat-card-success h-100">
            <div class="stat-info">
                <span class="stat-label">Lulus Penuh (Pengarah)</span>
                <span class="stat-value text-success"><?= number_format($stats['approved']) ?></span>
                <span class="stat-change text-success">
                    <i class="bi bi-check2-circle me-1"></i>Kelulusan muktamad
                </span>
            </div>
            <div class="stat-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card stat-card-danger h-100">
            <div class="stat-info">
                <span class="stat-label">Ditolak</span>
                <span class="stat-value text-danger"><?= number_format($stats['rejected']) ?></span>
                <span class="stat-change text-danger">
                    <i class="bi bi-x-circle me-1"></i>Permohonan dikueri / ditolak
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
                    <i class="bi bi-inbox-fill me-1 text-warning"></i> Menunggu Tindakan Semakan
                    <span class="badge bg-warning text-dark ms-1 rounded-pill"><?= count($pendingList) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab">
                    <i class="bi bi-archive-fill me-1 text-secondary"></i> Sejarah Selesai Diproses
                    <span class="badge bg-secondary ms-1 rounded-pill"><?= count($processedList) ?></span>
                </button>
            </li>
        </ul>
    </div>

    <div class="card-panel-body p-3">
        <div class="tab-content" id="jpppTabsContent">

            <!-- Tab 1: Pending Review (Mengikut Peringkat Hirarki) -->
            <div class="tab-pane fade show active" id="pending-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable small" id="pendingTable">
                        <thead class="table-light">
                            <tr>
                                <th width="4%">#</th>
                                <th width="14%">No. Permohonan</th>
                                <th>Pakar / Pemohon</th>
                                <th>Pesakit & RN</th>
                                <th width="11%">Tarikh Hantar</th>
                                <th width="12%" class="text-end">Tuntutan (RM)</th>
                                <th width="18%" class="text-center">Peringkat Semasa (Hirarki)</th>
                                <th width="10%" class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pendingList)): ?>
                                <?php foreach ($pendingList as $idx => $row): 
                                    $stInfo = $row['stageInfo'] ?? [];
                                ?>
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
                                            <span class="badge-status <?= $stInfo['badgeClass'] ?? 'badge-status-warning' ?> d-inline-block">
                                                <?= esc($stInfo['label'] ?? 'Menunggu') ?>
                                            </span>
                                            <?php if (!empty($row['isMyTurn'])): ?>
                                                <div class="mt-1">
                                                    <span class="badge bg-danger animate__animated animate__pulse animate__infinite text-white" style="font-size: 0.65rem;">
                                                        <i class="bi bi-exclamation-circle me-1"></i>Tindakan Anda!
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('review-jppp/show/' . $row['id']) ?>" class="btn <?= !empty($row['isMyTurn']) ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm px-3 shadow-sm">
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
                                <th width="4%">#</th>
                                <th width="14%">No. Permohonan</th>
                                <th>Pakar / Pemohon</th>
                                <th>Pesakit & RN</th>
                                <th width="12%" class="text-end">Tuntutan (RM)</th>
                                <th width="16%" class="text-center">Status Akhir</th>
                                <th width="15%">Tarikh Akhir</th>
                                <th width="10%" class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($processedList)): ?>
                                <?php foreach ($processedList as $idx => $row): 
                                    $stInfo = $row['stageInfo'] ?? [];
                                    $isApproved = ($stInfo['stage'] ?? '') === 'approved';
                                ?>
                                    <tr>
                                        <td class="text-center text-muted"><?= $idx + 1 ?></td>
                                        <td>
                                            <a href="<?= base_url('review-jppp/show/' . $row['id']) ?>" class="font-monospace fw-bold text-primary text-decoration-none">
                                                <?= esc($row['application_no']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc($row['specialist_name']) ?></div>
                                            <small class="text-muted">No. Staf: <?= esc($row['staff_number']) ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= esc($row['patient_name'] ?? '-') ?></div>
                                            <small class="text-muted">RN: <?= esc($row['patient_rn'] ?? '-') ?></small>
                                        </td>
                                        <td class="text-end font-monospace fw-bold text-success">
                                            RM <?= number_format($row['total_claim'] ?? 0, 2) ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-status <?= $stInfo['badgeClass'] ?? 'badge-status-secondary' ?>">
                                                <?= esc($stInfo['label'] ?? '-') ?>
                                            </span>
                                        </td>
                                        <td class="small text-muted">
                                            <?= !empty($row['updated_at']) ? date('d/m/Y h:i A', strtotime($row['updated_at'])) : '-' ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('review-jppp/show/' . $row['id']) ?>" class="btn btn-outline-secondary btn-sm px-2 shadow-sm" title="Lihat Rekod">
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
