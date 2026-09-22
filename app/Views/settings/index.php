<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-xl-11">

        <div class="card-panel">
            <div class="card-panel-header py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
                <div>
                    <h5 class="card-panel-title mb-1">
                        <i class="bi bi-sliders me-2 text-primary"></i>Tetapan Sistem (System Settings)
                    </h5>
                    <p class="text-muted small mb-0">Konfigurasi parameter global, identiti hospital, pelayan emel dan kawalan keselamatan sistem.</p>
                </div>
                <div>
                    <?php 
                        $currentMMode = $settings['maintenance_mode'] ?? ($sysSettings['maintenance_mode'] ?? '0');
                        if ($currentMMode === '1'): 
                    ?>
                        <span class="badge bg-danger text-white border px-3 py-1.5 shadow-sm rounded-pill small">
                            <i class="bi bi-wrench-adjustable me-1"></i> Mod Penyelenggaraan: AKTIF
                        </span>
                    <?php else: ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 shadow-sm rounded-pill small">
                            <i class="bi bi-check-circle-fill me-1"></i> Sistem Beroperasi Normal
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card-panel-body p-4">

                <form action="<?= base_url('settings/update') ?>" method="post" id="settingsForm">
                    <?= csrf_field() ?>

                    <!-- Tab Navigasi Tetapan -->
                    <ul class="nav nav-pills mb-4 gap-2 border-bottom pb-3" id="settingsTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active small fw-semibold px-3 py-2" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                <i class="bi bi-buildings me-1.5"></i> 1. Profil & Identiti Sistem
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link small fw-semibold px-3 py-2" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab">
                                <i class="bi bi-envelope-at me-1.5"></i> 2. Pelayan Emel & Notifikasi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link small fw-semibold px-3 py-2" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                                <i class="bi bi-shield-lock me-1.5"></i> 3. Keselamatan & Mod Sistem
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="settingsTabContent">
                        
                        <!-- TAB 1: UMUM / PROFIL SISTEM -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required fw-medium text-secondary">Nama Aplikasi / Sistem</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-app-indicator"></i></span>
                                        <input type="text" name="app_name" class="form-control" 
                                               value="<?= old('app_name', $settings['app_name'] ?? ($sysSettings['app_name'] ?? 'X-Claim HoSZA')) ?>" 
                                               placeholder="cth: X-Claim HoSZA" required>
                                    </div>
                                    <div class="form-text small text-muted">Dipaparkan pada tajuk halaman pelayar, sidebar, dan pengepala dokumen.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required fw-medium text-secondary">Organisasi / Pemilik Sistem</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-hospital"></i></span>
                                        <input type="text" name="company_name" class="form-control" 
                                               value="<?= old('company_name', $settings['company_name'] ?? ($sysSettings['company_name'] ?? 'Hospital Sultan Zainal Abidin (HoSZA)')) ?>" 
                                               placeholder="cth: Hospital Sultan Zainal Abidin (HoSZA)" required>
                                    </div>
                                    <div class="form-text small text-muted">Nama institusi atau hospital pemilik hak cipta perisian.</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label required fw-medium text-secondary">Tagline / Keterangan Rasmi</label>
                                    <input type="text" name="app_tagline" class="form-control" 
                                           value="<?= old('app_tagline', $settings['app_tagline'] ?? ($sysSettings['app_tagline'] ?? 'Sistem Pengurusan Tuntutan Pakar Perkhidmatan Eksekutif HoSZA')) ?>" 
                                           placeholder="Keterangan ringkas sistem..." required>
                                    <div class="form-text small text-muted">Keterangan yang memaparkan tujuan utama sistem dibangunkan.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">Zon Masa Pelayan (Timezone)</label>
                                    <?php $tz = old('timezone', $settings['timezone'] ?? ($sysSettings['timezone'] ?? 'Asia/Kuala_Lumpur')); ?>
                                    <select name="timezone" class="form-select">
                                        <option value="Asia/Kuala_Lumpur" <?= $tz === 'Asia/Kuala_Lumpur' ? 'selected' : '' ?>>Asia/Kuala_Lumpur (Waktu Piawai Malaysia - UTC+8)</option>
                                        <option value="Asia/Singapore" <?= $tz === 'Asia/Singapore' ? 'selected' : '' ?>>Asia/Singapore (UTC+8)</option>
                                        <option value="UTC" <?= $tz === 'UTC' ? 'selected' : '' ?>>UTC / GMT (Waktu Sejagat)</option>
                                    </select>
                                    <div class="form-text small text-muted">Digunakan untuk penanda masa audit, tarikh kelulusan, dan log aktiviti.</div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: PELAYAN EMEL -->
                        <div class="tab-pane fade" id="email" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required fw-medium text-secondary">Alamat Emel Rasmi Sistem (Sender)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="system_email" class="form-control" 
                                               value="<?= old('system_email', $settings['system_email'] ?? ($sysSettings['system_email'] ?? 'noreply.hosza@unisza.edu.my')) ?>" 
                                               placeholder="cth: noreply.hosza@unisza.edu.my" required>
                                    </div>
                                    <div class="form-text small text-muted">Alamat penghantar bagi sebarang notifikasi dan hebahan automatik.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">Protokol Penghantaran (Delivery Protocol)</label>
                                    <?php $proto = old('email_protocol', $settings['email_protocol'] ?? ($sysSettings['email_protocol'] ?? 'mail')); ?>
                                    <select name="email_protocol" class="form-select">
                                        <option value="mail" <?= $proto === 'mail' ? 'selected' : '' ?>>PHP Native Mail (Lalai Pelayan)</option>
                                        <option value="smtp" <?= $proto === 'smtp' ? 'selected' : '' ?>>SMTP (Disyorkan untuk persekitaran produksi)</option>
                                    </select>
                                    <div class="form-text small text-muted">Gunakan SMTP untuk integrasi pelayan emel berpusat institusi.</div>
                                </div>

                                <div class="col-md-8">
                                    <label class="form-label fw-medium text-secondary">Hos Pelayan SMTP (SMTP Host)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-hdd-network"></i></span>
                                        <input type="text" name="smtp_host" class="form-control" 
                                               value="<?= old('smtp_host', $settings['smtp_host'] ?? ($sysSettings['smtp_host'] ?? 'smtp.unisza.edu.my')) ?>" 
                                               placeholder="cth: smtp.unisza.edu.my atau smtp.gmail.com">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium text-secondary">Port SMTP (SMTP Port)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class="bi bi-plug"></i></span>
                                        <input type="number" name="smtp_port" class="form-control" 
                                               value="<?= old('smtp_port', $settings['smtp_port'] ?? ($sysSettings['smtp_port'] ?? '587')) ?>" 
                                               placeholder="cth: 587 atau 465">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: KESELAMATAN & MOD SISTEM -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0">
                                                    <i class="bi bi-wrench-adjustable me-1 text-warning"></i> Mod Penyelenggaraan Sistem (Maintenance Mode)
                                                </h6>
                                                <p class="text-muted small mb-0 mt-1">Kawal capaian pengguna semasa kerja-kerja naik taraf atau migrasi pangkalan data.</p>
                                            </div>
                                            <div class="form-check form-switch fs-4 mb-0">
                                                <?php $mMode = old('maintenance_mode', $settings['maintenance_mode'] ?? ($sysSettings['maintenance_mode'] ?? '0')); ?>
                                                <input class="form-check-input" type="checkbox" role="switch" name="maintenance_mode" id="maintenanceSwitch" value="1" <?= $mMode === '1' ? 'checked' : '' ?>>
                                            </div>
                                        </div>
                                        <div class="alert alert-warning mb-0 small py-2 d-flex align-items-center gap-2">
                                            <i class="bi bi-exclamation-triangle-fill fs-5 text-warning flex-shrink-0"></i>
                                            <div>
                                                <strong>Perhatian:</strong> Apabila mod ini diaktifkan, hanya pengguna dengan peranan <strong>Administrator</strong> dibenarkan mengakses sistem. Pengguna peranan lain akan dialihkan ke halaman log masuk dengan mesej pemakluman penyelenggaraan.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium text-secondary">Had Percubaan Log Masuk</label>
                                    <?php $attempts = old('login_attempts', $settings['login_attempts'] ?? ($sysSettings['login_attempts'] ?? '5')); ?>
                                    <select name="login_attempts" class="form-select">
                                        <option value="3" <?= $attempts === '3' ? 'selected' : '' ?>>3 Percubaan (Ketat / Strict)</option>
                                        <option value="5" <?= $attempts === '5' ? 'selected' : '' ?>>5 Percubaan (Standard Piawai)</option>
                                        <option value="10" <?= $attempts === '10' ? 'selected' : '' ?>>10 Percubaan (Longgar)</option>
                                    </select>
                                    <div class="form-text small text-muted">Maksimum percubaan gagal sebelum disekat.</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium text-secondary">Tempoh Sekatan Akaun (Lockout)</label>
                                    <?php $lockout = old('lockout_time', $settings['lockout_time'] ?? ($sysSettings['lockout_time'] ?? '300')); ?>
                                    <select name="lockout_time" class="form-select">
                                        <option value="300" <?= $lockout === '300' ? 'selected' : '' ?>>5 Minit (Standard Auto-Release)</option>
                                        <option value="600" <?= $lockout === '600' ? 'selected' : '' ?>>10 Minit</option>
                                        <option value="900" <?= $lockout === '900' ? 'selected' : '' ?>>15 Minit</option>
                                        <option value="1800" <?= $lockout === '1800' ? 'selected' : '' ?>>30 Minit</option>
                                    </select>
                                    <div class="form-text small text-muted">Akaun dilepaskan secara automatik selepas tempoh ini.</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-medium text-secondary">Tempoh Tamat Sesi (Timeout)</label>
                                    <div class="input-group">
                                        <input type="number" name="session_timeout" class="form-control" 
                                               value="<?= old('session_timeout', $settings['session_timeout'] ?? ($sysSettings['session_timeout'] ?? '7200')) ?>" min="300">
                                        <span class="input-group-text bg-light small">Saat</span>
                                    </div>
                                    <div class="form-text small text-muted">7200 saat bersamaan 2 jam aktif.</div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Tindakan Borang -->
                    <div class="form-actions mt-4 pt-3 border-top d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm fw-semibold">
                                <i class="bi bi-check-circle-fill me-1"></i> Simpan Tetapan
                            </button>
                            <a href="<?= base_url('dashboard') ?>" class="btn btn-light border px-3 py-2">
                                <i class="bi bi-x-lg me-1"></i> Batal
                            </a>
                        </div>
                        <span class="text-muted small">
                            <i class="bi bi-info-circle me-1"></i> Perubahan akan memberi kesan serta-merta ke seluruh sistem.
                        </span>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>