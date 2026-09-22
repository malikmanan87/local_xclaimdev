<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>


<!-- Card Panel Berasaskan 3 Tab -->
<div class="card-panel">
    <div class="card-panel-header py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
        <h5 class="card-panel-title mb-0">
            <i class="bi bi-person-check-fill me-2 text-primary"></i>Pengurusan Permohonan Akses
        </h5>

        <!-- Nav Pills Tab Header -->
        <ul class="nav nav-pills" id="accessRequestTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active px-3 py-1.5 fw-semibold small" id="pending-tab" data-bs-toggle="pill" data-bs-target="#tab-pending" type="button" role="tab" aria-selected="true">
                    <i class="bi bi-hourglass-split me-1 text-warning"></i> Pending
                    <span class="badge bg-warning text-dark rounded-pill ms-1"><?= $counts['pending'] ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-3 py-1.5 fw-semibold small" id="approved-tab" data-bs-toggle="pill" data-bs-target="#tab-approved" type="button" role="tab" aria-selected="false">
                    <i class="bi bi-check-circle-fill me-1 text-success"></i> Approved
                    <span class="badge bg-success rounded-pill ms-1"><?= $counts['approved'] ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-3 py-1.5 fw-semibold small" id="rejected-tab" data-bs-toggle="pill" data-bs-target="#tab-rejected" type="button" role="tab" aria-selected="false">
                    <i class="bi bi-x-circle-fill me-1 text-danger"></i> Rejected
                    <span class="badge bg-danger rounded-pill ms-1"><?= $counts['rejected'] ?></span>
                </button>
            </li>
        </ul>
    </div>

    <div class="card-panel-body p-3">
        <div class="tab-content" id="accessRequestTabContent">

            <!-- ══════════════════════════════════════════════
                 TAB 1: PENDING REQUESTS
            ══════════════════════════════════════════════ -->
            <div class="tab-pane fade show active" id="tab-pending" role="tabpanel" aria-labelledby="pending-tab">
                <div class="table-responsive">
                    <table id="tablePending" class="table table-hover table-align-middle w-100">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th width="50" class="text-center">#</th>
                                <th>Nama / Emel</th>
                                <th>No. Staf</th>
                                <th>Telefon / Jabatan</th>
                                <th>Tarikh Mohon</th>
                                <th width="80" class="text-center">Status</th>
                                <th width="170" class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pendingRequests)): ?>
                                <?php foreach ($pendingRequests as $i => $req): ?>
                                <tr id="row-<?= $req['id'] ?>">
                                    <td class="text-center text-muted fw-medium"><?= $i + 1 ?></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= esc($req['fullname']) ?></div>
                                        <div class="text-muted small"><?= esc($req['email']) ?></div>
                                    </td>
                                    <td class="text-secondary small font-monospace"><?= esc($req['username']) ?></td>
                                    <td class="text-secondary small"><?= esc($req['phone'] ?? '—') ?></td>
                                    <td class="text-secondary small">
                                        <?= date('d/m/Y h:i A', strtotime($req['created_at'])) ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-status badge-status-warning py-1" id="status-<?= $req['id'] ?>">Pending</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-success btn-sm px-2.5 py-1 shadow-sm"
                                                    onclick="openApprove(<?= $req['id'] ?>, '<?= esc($req['fullname']) ?>', false)"
                                                    title="Luluskan Permohonan">
                                                <i class="bi bi-check-lg me-1"></i>Approve
                                            </button>
                                            <button class="btn btn-danger btn-sm px-2.5 py-1 shadow-sm"
                                                    onclick="openReject(<?= $req['id'] ?>, '<?= esc($req['fullname']) ?>')"
                                                    title="Tolak Permohonan">
                                                <i class="bi bi-x-lg me-1"></i>Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════
                 TAB 2: APPROVED REQUESTS
            ══════════════════════════════════════════════ -->
            <div class="tab-pane fade" id="tab-approved" role="tabpanel" aria-labelledby="approved-tab">
                <div class="table-responsive">
                    <table id="tableApproved" class="table table-hover table-align-middle w-100">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th width="50" class="text-center">#</th>
                                <th>Nama / Emel</th>
                                <th>No. Staf</th>
                                <th>Peranan Ditetapkan</th>
                                <th>Diluluskan Oleh</th>
                                <th>Tarikh Kelulusan</th>
                                <th width="80" class="text-center">Status</th>
                                <th width="150" class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($approvedRequests)): ?>
                                <?php foreach ($approvedRequests as $i => $req): ?>
                                <tr id="row-<?= $req['id'] ?>">
                                    <td class="text-center text-muted fw-medium"><?= $i + 1 ?></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= esc($req['fullname']) ?></div>
                                        <div class="text-muted small"><?= esc($req['email']) ?></div>
                                    </td>
                                    <td class="text-secondary small font-monospace"><?= esc($req['username']) ?></td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 small">
                                            <?= esc($req['role_display'] ?? ucfirst($req['role_name'] ?? 'User')) ?>
                                        </span>
                                    </td>
                                    <td class="text-secondary small">
                                        <?= esc($req['reviewed_by_name'] ?? 'Admin') ?>
                                    </td>
                                    <td class="text-secondary small">
                                        <?= !empty($req['access_reviewed_at']) ? date('d/m/Y h:i A', strtotime($req['access_reviewed_at'])) : '—' ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-status badge-status-success py-1">Approved</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-outline-primary btn-sm px-2 py-1"
                                                    onclick="openApprove(<?= $req['id'] ?>, '<?= esc($req['fullname']) ?>', false, 'Kemaskini Peranan')"
                                                    title="Kemaskini Peranan">
                                                <i class="bi bi-shield-lock me-1"></i>Peranan
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm px-2 py-1"
                                                    onclick="openReject(<?= $req['id'] ?>, '<?= esc($req['fullname']) ?>')"
                                                    title="Tarik Balik Akses (Reject)">
                                                <i class="bi bi-slash-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════
                 TAB 3: REJECTED REQUESTS
            ══════════════════════════════════════════════ -->
            <div class="tab-pane fade" id="tab-rejected" role="tabpanel" aria-labelledby="rejected-tab">
                <div class="table-responsive">
                    <table id="tableRejected" class="table table-hover table-align-middle w-100">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th width="50" class="text-center">#</th>
                                <th>Nama / Emel</th>
                                <th>No. Staf</th>
                                <th>Sebab Penolakan (Catatan)</th>
                                <th>Ditolak Oleh</th>
                                <th>Tarikh Ditolak</th>
                                <th width="80" class="text-center">Status</th>
                                <th width="170" class="text-center">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rejectedRequests)): ?>
                                <?php foreach ($rejectedRequests as $i => $req): ?>
                                <tr id="row-<?= $req['id'] ?>">
                                    <td class="text-center text-muted fw-medium"><?= $i + 1 ?></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= esc($req['fullname']) ?></div>
                                        <div class="text-muted small"><?= esc($req['email']) ?></div>
                                    </td>
                                    <td class="text-secondary small font-monospace"><?= esc($req['username']) ?></td>
                                    <td>
                                        <?php if (!empty($req['access_note'])): ?>
                                            <div class="text-danger small fst-italic">
                                                <i class="bi bi-info-circle me-1"></i><?= esc($req['access_note']) ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted small fst-italic">Tiada catatan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-secondary small">
                                        <?= esc($req['reviewed_by_name'] ?? 'Admin') ?>
                                    </td>
                                    <td class="text-secondary small">
                                        <?= !empty($req['access_reviewed_at']) ? date('d/m/Y h:i A', strtotime($req['access_reviewed_at'])) : '—' ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-status badge-status-danger py-1">Rejected</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-outline-success btn-sm px-2.5 py-1"
                                                    onclick="openApprove(<?= $req['id'] ?>, '<?= esc($req['fullname']) ?>', true)"
                                                    title="Luluskan Semula (Re-Approve)">
                                                <i class="bi bi-arrow-clockwise me-1"></i>Re-Approve
                                            </button>
                                            <button class="btn btn-outline-secondary btn-sm px-2 py-1"
                                                    onclick="resetToPending(<?= $req['id'] ?>, '<?= esc($req['fullname']) ?>')"
                                                    title="Set Semula ke Pending">
                                                <i class="bi bi-arrow-counterclockwise" title="Reset ke Pending"></i>
                                            </button>
                                        </div>
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

<!-- ══════════════════════════════════════════════════
     MODAL APPROVE
══════════════════════════════════════════════════ -->
<div class="modal fade" id="modalApprove" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h6 class="modal-title" id="modalApproveTitle"><i class="bi bi-check-circle-fill me-2"></i>Approve Access Request</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3" id="approveUserDesc">
                    Meluluskan akses untuk: <strong id="approveUserName"></strong>
                </p>
                <div class="mb-3">
                    <label class="form-label fw-medium">Assign Role <span class="text-danger">*</span></label>
                    <select class="form-select" id="approveRoleId" name="role_id" required>
                        <option value="">— Pilih Peranan —</option>
                        <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= esc($role['display_name']) ?> (<?= esc($role['name']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label fw-medium">Catatan <span class="text-muted small">(pilihan)</span></label>
                    <textarea class="form-control" id="approveNote" rows="2" placeholder="cth: Diluluskan untuk akses pakar perubatan"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success px-4" id="btnApproveConfirm">
                    <i class="bi bi-check-lg me-1"></i> Sahkan Kelulusan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════
     MODAL REJECT
══════════════════════════════════════════════════ -->
<div class="modal fade" id="modalReject" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title"><i class="bi bi-x-circle-fill me-2"></i>Reject Access Request</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">
                    Menolak permohonan akses untuk: <strong id="rejectUserName"></strong>
                </p>
                <div class="mb-1">
                    <label class="form-label fw-medium">Sebab Penolakan <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="rejectNote" rows="3" placeholder="cth: Bukan staf klinikal / pakar atau maklumat tidak sah"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger px-4" id="btnRejectConfirm">
                    <i class="bi bi-x-lg me-1"></i> Sahkan Penolakan
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let targetUserId   = null;
const modalApprove = new bootstrap.Modal(document.getElementById('modalApprove'));
const modalReject  = new bootstrap.Modal(document.getElementById('modalReject'));

// ── Open Approve Modal ─────────────────────────────
function openApprove(id, name, isReapprove = false, customTitle = null) {
    targetUserId = id;
    document.getElementById('approveUserName').textContent = name;
    document.getElementById('approveRoleId').value = '';
    document.getElementById('approveNote').value = '';

    const titleEl = document.getElementById('modalApproveTitle');
    const descEl  = document.getElementById('approveUserDesc');
    
    if (titleEl) {
        if (customTitle) {
            titleEl.innerHTML = '<i class="bi bi-shield-lock me-2"></i>' + customTitle;
        } else if (isReapprove) {
            titleEl.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Re-Approve Access Request';
        } else {
            titleEl.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Approve Access Request';
        }
    }
    
    if (descEl) {
        if (isReapprove) {
            descEl.innerHTML = 'Meluluskan semula akses untuk: <strong>' + name + '</strong>';
        } else if (customTitle) {
            descEl.innerHTML = 'Kemaskini peranan untuk: <strong>' + name + '</strong>';
        } else {
            descEl.innerHTML = 'Meluluskan akses untuk: <strong>' + name + '</strong>';
        }
    }

    modalApprove.show();
}

// ── Open Reject Modal ──────────────────────────────
function openReject(id, name) {
    targetUserId = id;
    document.getElementById('rejectUserName').textContent = name;
    document.getElementById('rejectNote').value = '';
    modalReject.show();
}

// ── Confirm Approve ────────────────────────────────
document.getElementById('btnApproveConfirm').addEventListener('click', function () {
    const roleId = document.getElementById('approveRoleId').value;
    if (!roleId) {
        Swal.fire({ icon: 'warning', title: 'Peranan Diperlukan', text: 'Sila pilih peranan (role) sebelum meluluskan.' });
        return;
    }

    $.post(BASE_URL + 'access-requests/approve/' + targetUserId, {
        role_id: roleId,
        note:    document.getElementById('approveNote').value,
        [CSRF_TOKEN_NAME]: CSRF_HASH
    }, function (res) {
        modalApprove.hide();
        if (res.status === 'success') {
            Swal.fire({ icon: 'success', title: 'Berjaya!', text: res.message, timer: 1500, showConfirmButton: false });
            setTimeout(function() { location.reload(); }, 1200);
        } else {
            Swal.fire({ icon: 'error', title: 'Ralat', text: res.message || 'Operasi gagal.' });
        }
    }, 'json');
});

// ── Confirm Reject ─────────────────────────────────
document.getElementById('btnRejectConfirm').addEventListener('click', function () {
    const note = document.getElementById('rejectNote').value.trim();
    if (!note) {
        Swal.fire({ icon: 'warning', title: 'Sebab Diperlukan', text: 'Sila nyatakan sebab penolakan permohonan ini.' });
        return;
    }

    $.post(BASE_URL + 'access-requests/reject/' + targetUserId, {
        note: note,
        [CSRF_TOKEN_NAME]: CSRF_HASH
    }, function (res) {
        modalReject.hide();
        if (res.status === 'success') {
            Swal.fire({ icon: 'info', title: 'Permohonan Ditolak', text: res.message, timer: 1500, showConfirmButton: false });
            setTimeout(function() { location.reload(); }, 1200);
        } else {
            Swal.fire({ icon: 'error', title: 'Ralat', text: res.message || 'Operasi gagal.' });
        }
    }, 'json');
});

// ── Set Semula ke Pending ──────────────────────────
function resetToPending(id, name) {
    Swal.fire({
        title: 'Set Semula ke Pending?',
        text: 'Adakah anda pasti mahu menetapkan semula status permohonan ' + name + ' kepada Pending?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-arrow-counterclockwise me-1"></i> Ya, Set Semula',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(BASE_URL + 'access-requests/reset/' + id, {
                [CSRF_TOKEN_NAME]: CSRF_HASH
            }, function (res) {
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Berjaya!', text: res.message, timer: 1500, showConfirmButton: false });
                    setTimeout(function() { location.reload(); }, 1200);
                } else {
                    Swal.fire({ icon: 'error', title: 'Ralat', text: res.message || 'Gagal menetapkan semula status.' });
                }
            }, 'json');
        }
    });
}

// ── DataTables Initialization ──────────────────────
$(document).ready(function () {
    const dtConfig = {
        pageLength: 10,
        responsive: true,
        autoWidth: false,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Cari rekod...",
            lengthMenu: "Papar _MENU_ rekod",
            info: "Memaparkan _START_ hingga _END_ daripada _TOTAL_ rekod",
            infoEmpty: "Tiada rekod",
            paginate: { first: "Pertama", last: "Terakhir", next: "Seterusnya", previous: "Sebelumnya" },
            emptyTable: "Tiada rekod dalam kategori ini."
        }
    };

    $('#tablePending').DataTable(dtConfig);
    $('#tableApproved').DataTable(dtConfig);
    $('#tableRejected').DataTable(dtConfig);

    // Laraskan semula lebar kolum DataTables apabila tab bertukar
    $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function () {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
});
</script>
<?= $this->endSection() ?>
