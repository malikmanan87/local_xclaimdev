<?php
$currentUrl = current_url();
$uri = service('uri');
$segment1 = $uri->getSegment(1);
?>

<nav class="sidebar" id="sidebar">

    <div class="sidebar-brand">
        <a href="<?= base_url('dashboard') ?>" class="brand-link">
            <div class="brand-icon">
                <i class="bi bi-grid-3x3-gap-fill"></i>
            </div>
            <span class="brand-name"><?= esc($dynamicAppName ?? ($sysSettings['app_name'] ?? APP_NAME)) ?></span>
        </a>
        <button class="sidebar-toggle-btn" id="sidebarToggleBtn">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <div class="sidebar-menu">
        <ul class="menu-list">

            <li class="menu-item <?= $segment1 === 'dashboard' ? 'active' : '' ?>">
                <a href="<?= base_url('dashboard') ?>" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-speedometer2"></i></span>
                    <span class="menu-label">Dashboard</span>
                </a>
            </li>

            <li class="menu-separator"><span>User Menu</span></li>

            <li class="menu-item <?= $segment1 === 'new-application' ? 'active' : '' ?>">
                <a href="<?= base_url('new-application') ?>" class="menu-link">
                    <span class="menu-icon"><i class="bi bi-file-earmark-medical-fill"></i></span>
                    <span class="menu-label">New Application</span>
                </a>
            </li>

            <?php
            $userRole = strtolower(session('role_name') ?? session('role') ?? 'user');
            $canReviewJppp     = in_array($userRole, ['admin', 'manager', 'jppp', 'pegawai_penyemak_pe', 'pegawai_perkhidmatan_pe', 'ketua_j3p', 'pengarah']);
            $canViewProcedures = in_array($userRole, ['admin', 'manager', 'jppp', 'pegawai_penyemak_pe', 'pegawai_perkhidmatan_pe', 'ketua_j3p', 'pengarah']);

            $pendingJpppBadge = 0;
            if ($canReviewJppp) {
                try {
                    $sidebarAppModel = new \App\Models\NewApplicationModel();
                    $pendingJpppBadge = $sidebarAppModel->getPendingJpppCount($userRole);
                } catch (\Throwable $e) {}
            }
            ?>

            <?php
            $canViewReports = ($userRole !== 'user');

            $pendingAccessRequestsCount = 0;
            if ($userRole === 'admin' || session('role') === 'admin') {
                try {
                    $db = \Config\Database::connect();
                    $pendingAccessRequestsCount = (int)$db->table('users')
                        ->where('access_status', 'pending')
                        ->where('deleted_at', null)
                        ->countAllResults();
                } catch (\Throwable $e) {}
            }
            ?>

            <?php if ($canReviewJppp || $canViewProcedures || $canViewReports): ?>
                <li class="menu-separator"><span>Semakan & Kelulusan</span></li>

                <?php if ($canReviewJppp): ?>
                    <li class="menu-item <?= $segment1 === 'review-jppp' ? 'active' : '' ?>">
                        <a href="<?= base_url('review-jppp') ?>" class="menu-link">
                            <span class="menu-icon"><i class="bi bi-clipboard2-pulse-fill"></i></span>
                            <span class="menu-label">Senarai Permohonan</span>
                            <?php if ($pendingJpppBadge > 0): ?>
                                <span class="badge bg-warning text-dark rounded-pill ms-auto" style="font-size: 0.68rem;"><?= $pendingJpppBadge ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($canViewProcedures): ?>
                    <li class="menu-item <?= $segment1 === 'procedures' ? 'active' : '' ?>">
                        <a href="<?= base_url('procedures') ?>" class="menu-link">
                            <span class="menu-icon"><i class="bi bi-journal-medical"></i></span>
                            <span class="menu-label">Prosedur MMA</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($canViewReports): ?>
                    <li class="menu-item <?= $segment1 === 'reports' ? 'active' : '' ?>">
                        <a href="<?= base_url('reports') ?>" class="menu-link">
                            <span class="menu-icon"><i class="bi bi-file-earmark-bar-graph-fill"></i></span>
                            <span class="menu-label">Reports</span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (session('role') === 'admin' || $userRole === 'admin'): ?>
                <li class="menu-separator"><span>System Admin</span></li>

                <li class="menu-item <?= $segment1 === 'users' ? 'active' : '' ?>">
                    <a href="<?= base_url('users') ?>" class="menu-link">
                        <span class="menu-icon"><i class="bi bi-people-fill"></i></span>
                        <span class="menu-label">Users</span>
                    </a>
                </li>

                <li class="menu-item <?= $segment1 === 'access-requests' ? 'active' : '' ?>">
                    <a href="<?= base_url('access-requests') ?>" class="menu-link">
                        <span class="menu-icon"><i class="bi bi-person-check-fill"></i></span>
                        <span class="menu-label">Access Requests</span>
                        <?php if ($pendingAccessRequestsCount > 0): ?>
                            <span class="badge bg-warning text-dark rounded-pill ms-auto" style="font-size: 0.68rem;"><?= $pendingAccessRequestsCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <li class="menu-item <?= $segment1 === 'roles' ? 'active' : '' ?>">
                    <a href="<?= base_url('roles') ?>" class="menu-link">
                        <span class="menu-icon"><i class="bi bi-shield-lock-fill"></i></span>
                        <span class="menu-label">Roles Management</span>
                    </a>
                </li>

                <li class="menu-item <?= $segment1 === 'activity-logs' ? 'active' : '' ?>">
                    <a href="<?= base_url('activity-logs') ?>" class="menu-link">
                        <span class="menu-icon"><i class="bi bi-journal-text"></i></span>
                        <span class="menu-label">Activity Logs</span>
                    </a>
                </li>

                <li class="menu-item <?= $segment1 === 'settings' ? 'active' : '' ?>">
                    <a href="<?= base_url('settings') ?>" class="menu-link">
                        <span class="menu-icon"><i class="bi bi-gear-fill"></i></span>
                        <span class="menu-label">Settings</span>
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </div>

    <div class="sidebar-footer">
        <a href="<?= base_url('logout') ?>" class="logout-btn" onclick="confirmLogout(event, this)">
            <i class="bi bi-box-arrow-left"></i>
            <span>Logout</span>
        </a>
    </div>

</nav>

<div class="sidebar-overlay" id="sidebarOverlay"></div>