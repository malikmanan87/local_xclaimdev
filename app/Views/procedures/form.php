<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$isEdit = !empty($procedure);
$formAction = $isEdit ? base_url('procedures/update/' . $procedure['id']) : base_url('procedures/store');
$pageTitleText = $isEdit ? 'Kemaskini Prosedur MMA' : 'Daftar Prosedur MMA Baharu';
?>

<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="bi bi-<?= $isEdit ? 'pencil-square text-warning' : 'plus-circle-fill text-primary' ?> me-2"></i>
                        <?= $pageTitleText ?>
                    </h5>
                    <small class="text-muted">
                        <?= $isEdit ? 'Kemaskini maklumat kod, nama, dan penetapan kadar fi prosedur.' : 'Masukkan maklumat prosedur baharu ke dalam senarai induk jadual MMA.' ?>
                    </small>
                </div>
                <a href="<?= base_url('procedures') ?>" class="btn btn-outline-secondary btn-sm px-3 rounded-2">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="card-body p-4">

                <!-- Alert Validation Errors -->
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                            <strong class="text-dark">Sila periksa ralat berikut:</strong>
                        </div>
                        <ul class="mb-0 ps-3 small">
                            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= $formAction ?>" method="POST" autocomplete="off" id="procedureForm">
                    <?= csrf_field() ?>

                    <div class="row g-3">

                        <!-- Kod Prosedur MMA -->
                        <div class="col-md-4">
                            <label for="code" class="form-label fw-semibold text-dark small">
                                Kod MMA <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light font-monospace small"><i class="bi bi-hash"></i></span>
                                <input type="text" 
                                       class="form-control font-monospace fw-bold text-primary <?= isset(session()->getFlashdata('errors')['code']) ? 'is-invalid' : '' ?>" 
                                       id="code" 
                                       name="code" 
                                       value="<?= old('code', $procedure['code'] ?? '') ?>" 
                                       placeholder="Cth: 4700" 
                                       maxlength="10" 
                                       required>
                            </div>
                            <div class="form-text text-xs">Kod unik rujukan MMA (maks. 10 aksara).</div>
                        </div>

                        <!-- Nama Prosedur -->
                        <div class="col-md-8">
                            <label for="name" class="form-label fw-semibold text-dark small">
                                Nama Prosedur <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control <?= isset(session()->getFlashdata('errors')['name']) ? 'is-invalid' : '' ?>" 
                                   id="name" 
                                   name="name" 
                                   value="<?= old('name', $procedure['name'] ?? '') ?>" 
                                   placeholder="Cth: Laparoscopic Cholecystectomy" 
                                   required>
                            <div class="form-text text-xs">Nama penuh klinikal prosedur perubatan.</div>
                        </div>

                        <!-- Seksyen (Section) -->
                        <div class="col-md-6">
                            <label for="section" class="form-label fw-semibold text-dark small">
                                Seksyen Disiplin <span class="text-muted fw-normal">(Pilihan)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-folder2"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="section" 
                                       name="section" 
                                       list="sectionList" 
                                       value="<?= old('section', $procedure['section'] ?? 'General Section') ?>" 
                                       placeholder="Pilih atau taip seksyen...">
                                <datalist id="sectionList">
                                    <?php if (!empty($sections)): ?>
                                        <?php foreach ($sections as $sec): ?>
                                            <option value="<?= esc($sec) ?>"></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </datalist>
                            </div>
                            <div class="form-text text-xs">Cth: General Surgery, Orthopaedic, Ophthalmology.</div>
                        </div>

                        <!-- Kategori (Category) -->
                        <div class="col-md-6">
                            <label for="category" class="form-label fw-semibold text-dark small">
                                Kategori / Sub-Disiplin <span class="text-muted fw-normal">(Pilihan)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-tag"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       id="category" 
                                       name="category" 
                                       list="categoryList" 
                                       value="<?= old('category', $procedure['category'] ?? 'Uncategorized') ?>" 
                                       placeholder="Pilih atau taip kategori...">
                                <datalist id="categoryList">
                                    <?php if (!empty($categories)): ?>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= esc($cat) ?>"></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </datalist>
                            </div>
                            <div class="form-text text-xs">Cth: Abdomen, Hernia, Joint, Endoscopy.</div>
                        </div>

                        <hr class="text-muted my-2">

                        <!-- Kadar Fi Surgeri -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <label for="surgeon_fee" class="form-label fw-bold text-success d-flex align-items-center justify-content-between mb-1">
                                    <span><i class="bi bi-currency-dollar me-1"></i> Fi Surgeri (RM) <span class="text-danger">*</span></span>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 small">Pakar Surgeri</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white fw-bold text-dark border-end-0">RM</span>
                                    <input type="number" 
                                           step="0.01" 
                                           min="0" 
                                           class="form-control form-control-lg fw-bold font-monospace text-success border-start-0 <?= isset(session()->getFlashdata('errors')['surgeon_fee']) ? 'is-invalid' : '' ?>" 
                                           id="surgeon_fee" 
                                           name="surgeon_fee" 
                                           value="<?= old('surgeon_fee', isset($procedure['surgeon_fee']) ? number_format((float)$procedure['surgeon_fee'], 2, '.', '') : '0.00') ?>" 
                                           placeholder="0.00" 
                                           required>
                                </div>
                                <div class="form-text text-xs mt-1">Kadar maksimum tuntutan fi surgeri pakar.</div>
                            </div>
                        </div>

                        <!-- Kadar Fi Bius -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <label for="anaesthetist_fee" class="form-label fw-bold text-info d-flex align-items-center justify-content-between mb-1">
                                    <span><i class="bi bi-capsule-pill me-1"></i> Fi Bius / Anaesthetist (RM)</span>
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 small">Pakar Bius</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white fw-bold text-dark border-end-0">RM</span>
                                    <input type="number" 
                                           step="0.01" 
                                           min="0" 
                                           class="form-control form-control-lg fw-bold font-monospace text-info border-start-0 <?= isset(session()->getFlashdata('errors')['anaesthetist_fee']) ? 'is-invalid' : '' ?>" 
                                           id="anaesthetist_fee" 
                                           name="anaesthetist_fee" 
                                           value="<?= old('anaesthetist_fee', isset($procedure['anaesthetist_fee']) ? number_format((float)$procedure['anaesthetist_fee'], 2, '.', '') : '0.00') ?>" 
                                           placeholder="0.00">
                                </div>
                                <div class="form-text text-xs mt-1">Kadar fi bius jika prosedur memerlukan bius (GA/LA).</div>
                            </div>
                        </div>

                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-4 pt-3 border-top">
                        <a href="<?= base_url('procedures') ?>" class="btn btn-light border px-4">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm fw-semibold">
                            <i class="bi bi-<?= $isEdit ? 'check2-circle' : 'save' ?> me-1"></i>
                            <?= $isEdit ? 'Kemaskini Prosedur' : 'Simpan Prosedur' ?>
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
