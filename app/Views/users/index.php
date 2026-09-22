<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card-panel">
    <div class="card-panel-header py-3">
        <h5 class="card-panel-title mb-0">
            <i class="bi bi-people-fill me-2 text-primary"></i>User Management
        </h5>
    </div>
    <div class="card-panel-body">
        <div class="table-responsive">
            <table id="usersTable" class="table table-hover table-align-middle w-100">
                <thead class="table-light text-secondary">
                    <tr>
                        <th width="50" class="text-center">#</th>
                        <th>User</th>
                        <th>Email & Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th width="150" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $i => $user): ?>
                        <tr>
                            <td class="text-center text-muted fw-medium"><?= $i + 1 ?></td>
                            
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?php if (!empty($user['avatar']) && file_exists(FCPATH . 'uploads/avatars/' . $user['avatar'])): ?>
                                        <img src="<?= base_url('uploads/avatars/' . $user['avatar']) ?>" 
                                             alt="Avatar" 
                                             class="rounded-circle border" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" 
                                             style="width: 40px; height: 40px; font-size: 1.1rem; min-width: 40px;">
                                            <?= strtoupper(substr($user['fullname'] ?? 'U', 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <div class="fw-semibold text-dark"><?= esc($user['fullname']) ?></div>
                                        <div class="text-muted small">@<?= esc($user['username']) ?></div>
                                    </div>
                                </div>
                            </td>
                            
                            <td>
                                <div class="text-dark small mb-0.5"><i class="bi bi-envelope text-muted me-1"></i><?= esc($user['email']) ?></div>
                                <?php if (!empty($user['phone'])): ?>
                                    <div class="text-secondary small"><i class="bi bi-telephone text-muted me-1"></i><?= esc($user['phone']) ?></div>
                                <?php else: ?>
                                    <div class="text-muted small italic">No Phone Number</div>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php
                                $roleName = esc($user['role_name'] ?? 'user');
                                $roleClasses = [
                                    'admin'                   => 'badge-role-admin',
                                    'user'                    => 'badge-role-user',
                                    'pegawai_penyemak_pe'     => 'bg-primary text-white',
                                    'pegawai_perkhidmatan_pe' => 'bg-info text-dark',
                                    'ketua_j3p'               => 'bg-warning text-dark',
                                    'pengarah'                => 'bg-success text-white',
                                ];
                                $displayName = esc($user['role_display'] ?? ucfirst(str_replace('_', ' ', $roleName)));
                                ?>
                                <span class="badge-role <?= $roleClasses[$roleName] ?? 'bg-secondary text-white' ?>">
                                    <?= $displayName ?>
                                </span>
                            </td>
                            
                            <td>
                                <?php
                                $isLocked = !empty($user['locked_until']) && strtotime($user['locked_until']) > time();
                                ?>
                                <?php if ($isLocked): ?>
                                    <?php $remMinutes = ceil((strtotime($user['locked_until']) - time()) / 60); ?>
                                    <span class="badge bg-danger text-white py-1 px-2 shadow-sm rounded-pill" title="Akaun disekat sehingga <?= date('d/m/Y h:i:s A', strtotime($user['locked_until'])) ?>">
                                        <i class="bi bi-lock-fill me-1"></i>Disekat (<?= $remMinutes ?>m)
                                    </span>
                                <?php elseif ((int)$user['is_active'] === 1): ?>
                                    <span class="badge-status badge-status-success py-1">Active</span>
                                    <?php if (!empty($user['failed_attempts']) && (int)$user['failed_attempts'] > 0): ?>
                                        <span class="badge bg-warning text-dark py-0.5 px-1.5 ms-1 rounded-pill" style="font-size: 0.7rem;" title="<?= $user['failed_attempts'] ?> percubaan gagal">
                                            <i class="bi bi-exclamation-circle-fill me-0.5"></i><?= $user['failed_attempts'] ?>
                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge-status badge-status-danger py-1">Inactive</span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="text-secondary small">
                                <?= !empty($user['last_login']) ? date('d/m/Y h:i A', strtotime($user['last_login'])) : '<span class="text-muted italic">Never</span>' ?>
                            </td>
                            
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <?php if ($isLocked || (!empty($user['failed_attempts']) && (int)$user['failed_attempts'] > 0)): ?>
                                        <a href="<?= base_url('users/reset-throttle/' . $user['id']) ?>" 
                                           class="btn-action bg-danger text-white border-danger shadow-sm" 
                                           title="Akaun disekat: Klik untuk Nyahsekat (Unlock)" 
                                           onclick="return confirm('Adakah anda pasti ingin menyahsekat akaun <?= esc(addslashes($user['fullname'])) ?>?')">
                                            <i class="bi bi-unlock-fill"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('users/reset-throttle/' . $user['id']) ?>" 
                                           class="btn-action btn-action-view" 
                                           title="Reset Sekatan Log Masuk" 
                                           onclick="return confirm('Reset sebarang rekod sekatan log masuk untuk akaun <?= esc(addslashes($user['fullname'])) ?>?')">
                                            <i class="bi bi-unlock"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <a href="<?= base_url('users/edit/' . $user['id']) ?>" class="btn-action btn-action-edit" title="Edit Profile">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <?php if ((int)$user['id'] !== (int)session('user_id')): ?>
                                        <button class="btn-action btn-action-delete" 
                                                title="Delete" 
                                                onclick="confirmDelete('<?= base_url('users/delete/' . $user['id']) ?>', '<?= esc($user['fullname']) ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php endif; ?>
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/en-GB.json',
            search: 'Search Users:',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ users',
            paginate: { previous: 'Previous', next: 'Next' }
        },
        order: [[0, 'asc']], 
        pageLength: 10,
        columnDefs: [{ orderable: false, targets: [1, 2, 6] }]
    });
});
</script>
<?= $this->endSection() ?>