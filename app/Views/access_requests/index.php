<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card stat-card-warning">
            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= $counts['pending'] ?></div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card-success">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= $counts['approved'] ?></div>
                <div class="stat-label">Approved</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card-danger">
            <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
            <div class="stat-info">
                <div class="stat-value"><?= $counts['rejected'] ?></div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card-panel">
    <div class="card-panel-header py-3">
        <h5 class="card-panel-title">
            <i class="bi bi-person-check-fill me-2 text-primary"></i>Access Requests
        </h5>
    </div>
    <div class="card-panel-body">
        <div class="table-responsive">
            <table id="requestsTable" class="table table-hover table-align-middle w-100">
                <thead class="table-light text-secondary">
                    <tr>
                        <th width="50" class="text-center">#</th>
                        <th>Name / Email</th>
                        <th>Staff No.</th>
                        <th>Department</th>
                        <th>Requested</th>
                        <th>Status</th>
                        <th>Reviewed By</th>
                        <th width="130" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $i => $req): ?>
                    <tr id="row-<?= $req['id'] ?>">
                        <td class="text-center text-muted fw-medium"><?= $i + 1 ?></td>
                        <td>
                            <div class="fw-medium text-dark"><?= esc($req['fullname']) ?></div>
                            <div class="text-muted small"><?= esc($req['email']) ?></div>
                        </td>
                        <td class="text-secondary small"><?= esc($req['username']) ?></td>
                        <td class="text-secondary small"><?= esc($req['phone'] ?? '—') ?></td>
                        <td class="text-secondary small">
                            <?= date('d/m/Y H:i', strtotime($req['created_at'])) ?>
                        </td>
                        <td>
                            <?php
                            $sMap = [
                                'pending'  => ['badge-status-warning', 'Pending'],
                                'approved' => ['badge-status-success', 'Approved'],
                                'rejected' => ['badge-status-danger',  'Rejected'],
                            ];
                            $s = $sMap[$req['access_status']] ?? ['badge-status-secondary', ucfirst($req['access_status'])];
                            ?>
                            <span class="badge-status <?= $s[0] ?> py-1" id="status-<?= $req['id'] ?>">
                                <?= $s[1] ?>
                            </span>
                            <?php if (!empty($req['access_note'])): ?>
                                <div class="text-muted small mt-1 fst-italic"><?= esc($req['access_note']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-secondary small">
                            <?php if ($req['reviewed_by_name']): ?>
                                <div><?= esc($req['reviewed_by_name']) ?></div>
                                <div class="text-muted"><?= date('d/m/Y', strtotime($req['access_reviewed_at'])) ?></div>
                            <?php else: ?>
                                <span class="text-muted fst-italic">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($req['access_status'] === 'pending'): ?>
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-success btn-sm px-2 py-1"
                                        onclick="openApprove(<?= $req['id'] ?>, '<?= esc($req['fullname']) ?>')"
                                        title="Approve">
                                    <i class="bi bi-check-lg me-1"></i>Approve
                                </button>
                                <button class="btn btn-danger btn-sm px-2 py-1"
                                        onclick="openReject(<?= $req['id'] ?>, '<?= esc($req['fullname']) ?>')"
                                        title="Reject">
                                    <i class="bi bi-x-lg me-1"></i>Reject
                                </button>
                            </div>
                            <?php else: ?>
                                <span class="text-muted small fst-italic">Reviewed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
                <h6 class="modal-title"><i class="bi bi-check-circle-fill me-2"></i>Approve Access Request</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">
                    Approving access for: <strong id="approveUserName"></strong>
                </p>
                <div class="mb-3">
                    <label class="form-label fw-medium">Assign Role <span class="text-danger">*</span></label>
                    <select class="form-select" id="approveRoleId" name="role_id" required>
                        <option value="">— Select Role —</option>
                        <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>"><?= esc($role['display_name']) ?> (<?= esc($role['name']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label fw-medium">Note <span class="text-muted small">(optional)</span></label>
                    <textarea class="form-control" id="approveNote" rows="2" placeholder="e.g. Approved for specialist access"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success px-4" id="btnApproveConfirm">
                    <i class="bi bi-check-lg me-1"></i> Confirm Approve
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
                    Rejecting access for: <strong id="rejectUserName"></strong>
                </p>
                <div class="mb-1">
                    <label class="form-label fw-medium">Reason for Rejection <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="rejectNote" rows="3" placeholder="e.g. Not eligible for system access"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger px-4" id="btnRejectConfirm">
                    <i class="bi bi-x-lg me-1"></i> Confirm Reject
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
function openApprove(id, name) {
    targetUserId = id;
    document.getElementById('approveUserName').textContent = name;
    document.getElementById('approveRoleId').value = '';
    document.getElementById('approveNote').value = '';
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
        Swal.fire({ icon: 'warning', title: 'Role Required', text: 'Please select a role before approving.' });
        return;
    }

    $.post(BASE_URL + 'access-requests/approve/' + targetUserId, {
        role_id: roleId,
        note:    document.getElementById('approveNote').value,
        [CSRF_TOKEN_NAME]: CSRF_HASH
    }, function (res) {
        modalApprove.hide();
        if (res.status === 'success') {
            updateRowStatus(targetUserId, 'approved');
            Swal.fire({ icon: 'success', title: 'Approved!', text: res.message, timer: 2000, showConfirmButton: false });
        }
    }, 'json');
});

// ── Confirm Reject ─────────────────────────────────
document.getElementById('btnRejectConfirm').addEventListener('click', function () {
    const note = document.getElementById('rejectNote').value.trim();
    if (!note) {
        Swal.fire({ icon: 'warning', title: 'Reason Required', text: 'Please provide a reason for rejection.' });
        return;
    }

    $.post(BASE_URL + 'access-requests/reject/' + targetUserId, {
        note: note,
        [CSRF_TOKEN_NAME]: CSRF_HASH
    }, function (res) {
        modalReject.hide();
        if (res.status === 'success') {
            updateRowStatus(targetUserId, 'rejected', note);
            Swal.fire({ icon: 'info', title: 'Rejected', text: res.message, timer: 2000, showConfirmButton: false });
        }
    }, 'json');
});

// ── Update row status in table (no reload) ─────────
function updateRowStatus(id, status, note = '') {
    const statusMap = {
        approved: ['badge-status-success', 'Approved'],
        rejected: ['badge-status-danger',  'Rejected'],
    };
    const [cls, label] = statusMap[status] || ['badge-status-secondary', status];

    const statusEl = document.getElementById('status-' + id);
    if (statusEl) {
        statusEl.className = 'badge-status ' + cls + ' py-1';
        statusEl.textContent = label;
    }

    // Replace action buttons with "Reviewed"
    const row = document.getElementById('row-' + id);
    if (row) {
        const actionCell = row.querySelector('td:last-child');
        if (actionCell) actionCell.innerHTML = '<span class="text-muted small fst-italic">Reviewed</span>';
    }
}

// ── DataTable ──────────────────────────────────────
$(document).ready(function () {
    $('#requestsTable').DataTable({
        order: [[4, 'desc']],
        pageLength: 15,
        columnDefs: [{ orderable: false, targets: [0, 7] }]
    });
});
</script>
<?= $this->endSection() ?>
