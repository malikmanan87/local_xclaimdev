<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$isEdit = !empty($isEdit) && !empty($application);
?>

<div class="card-panel">

    <!-- ═══════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════ -->
    <div class="card-panel-header py-3">
        <h5 class="card-panel-title">
            <?php if ($isEdit): ?>
                <i class="bi bi-pencil-square me-2 text-warning"></i>
                Kemaskini Permohonan: <span class="font-monospace text-primary"><?= esc($application['application_no']) ?></span>
                <span class="badge bg-warning-subtle text-warning-emphasis ms-2 fs-6">Submitted</span>
            <?php else: ?>
                <i class="bi bi-file-earmark-medical-fill me-2 text-primary"></i>New Application
            <?php endif; ?>
        </h5>
        <div class="d-flex align-items-center gap-2">
            <?php if ($isEdit): ?>
                <a href="<?= base_url('new-application/show/' . $application['id']) ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </a>
            <?php else: ?>
                <a href="<?= base_url('new-application') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════
         STEP INDICATOR
    ═══════════════════════════════════════════════ -->
    <div class="px-4 pt-4 pb-2">
        <div class="d-flex align-items-center gap-0" id="stepIndicator">

            <!-- Step 1 -->
            <div class="step-item active" id="step-indicator-1">
                <div class="step-circle">1</div>
                <div class="step-label">Specialist<br>Identification</div>
            </div>
            <div class="step-line" id="step-line-1"></div>

            <!-- Step 2 -->
            <div class="step-item" id="step-indicator-2">
                <div class="step-circle">2</div>
                <div class="step-label">Search<br>Patient</div>
            </div>
            <div class="step-line" id="step-line-2"></div>

            <!-- Step 3 (placeholder — boleh tambah nanti) -->
            <div class="step-item" id="step-indicator-3">
                <div class="step-circle">3</div>
                <div class="step-label">Claim<br>Details</div>
            </div>

        </div>
    </div>

    <!-- ═══════════════════════════════════════════════
         TAB NAV (hidden — dikontrol oleh JS)
    ═══════════════════════════════════════════════ -->
    <div class="card-panel-body pt-2">

        <!-- ─── TAB 1: Maklumat Pegawai Yang Menuntut (Bahagian A) ─── -->
        <div class="tab-pane-custom" id="tab1">
            <div class="tab-pane-title d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-person-badge-fill text-primary me-2"></i>
                    <strong>BAHAGIAN A: MAKLUMAT PEGAWAI YANG MENUNTUT</strong>
                    <span class="badge bg-primary-subtle text-primary ms-2">HoSZA-MGT-J3P (PE)-F-003-01</span>
                </div>
            </div>

            <form id="formSpecialist" novalidate>
                <?= csrf_field() ?>

                <!-- Claim Period Box (Bagi Bulan ___ Tahun ___) -->
                <div class="card border-primary border-opacity-25 bg-primary bg-opacity-10 rounded-3 p-3 my-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <label for="claim_month" class="form-label fw-bold text-dark small mb-1">
                                <i class="bi bi-calendar-month me-1 text-primary"></i> BAGI BULAN <span class="text-danger">*</span>
                            </label>
                            <?php
                            $months = [
                                '01' => 'Januari', '02' => 'Februari', '03' => 'Mac',
                                '04' => 'April',   '05' => 'Mei',      '06' => 'Jun',
                                '07' => 'Julai',   '08' => 'Ogos',     '09' => 'September',
                                '10' => 'Oktober', '11' => 'November', '12' => 'Disember'
                            ];
                            $curMonth = $userData['claim_month'] ?? date('m');
                            $curYear  = $userData['claim_year'] ?? date('Y');
                            ?>
                            <select class="form-select" id="claim_month" name="claim_month" required>
                                <?php foreach ($months as $mVal => $mName): ?>
                                    <option value="<?= $mVal ?>" <?= $curMonth == $mVal ? 'selected' : '' ?>><?= $mVal ?> - <?= $mName ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="claim_year" class="form-label fw-bold text-dark small mb-1">
                                <i class="bi bi-calendar me-1 text-primary"></i> TAHUN <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control font-monospace fw-bold" id="claim_year" name="claim_year" value="<?= esc($curYear) ?>" min="2020" max="2035" required>
                        </div>
                    </div>
                </div>

                <div class="row g-3">

                    <!-- Specialist Name -->
                    <div class="col-md-6">
                        <label for="specialist_name" class="form-label fw-medium">
                            NAMA PEGAWAI <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person-fill text-muted"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0 bg-light"
                                   id="specialist_name"
                                   name="specialist_name"
                                   value="<?= esc($userData['specialist_name'] ?? session('name') ?? '') ?>"
                                   placeholder="Nama Pakar / Pegawai"
                                   maxlength="150"
                                   readonly
                                   required>
                        </div>
                        <div class="invalid-feedback-custom" id="err_specialist_name"></div>
                    </div>

                    <!-- No. Kad Pengenalan -->
                    <div class="col-md-6">
                        <label for="staff_ic" class="form-label fw-medium">
                            NO. KAD PENGENALAN
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person-vcard-fill text-muted"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0"
                                   id="staff_ic"
                                   name="staff_ic"
                                   value="<?= esc($userData['staff_ic'] ?? '') ?>"
                                   placeholder="Contoh: 850101-11-1234"
                                   maxlength="25">
                        </div>
                        <div class="invalid-feedback-custom" id="err_staff_ic"></div>
                    </div>

                    <!-- Staff Number -->
                    <div class="col-md-6">
                        <label for="staff_number" class="form-label fw-medium">
                            NO. PEKERJA <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-card-text text-muted"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0 bg-light"
                                   id="staff_number"
                                   name="staff_number"
                                   value="<?= esc($userData['staff_number'] ?? session('staffno') ?? '') ?>"
                                   placeholder="No. Staf"
                                   maxlength="50"
                                   readonly
                                   required>
                        </div>
                        <div class="invalid-feedback-custom" id="err_staff_number"></div>
                    </div>

                    <!-- Jawatan & Gred -->
                    <div class="col-md-6">
                        <label for="grade" class="form-label fw-medium">
                            JAWATAN & GRED
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-award-fill text-muted"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0"
                                   id="grade"
                                   name="grade"
                                   value="<?= esc($userData['grade'] ?? '') ?>"
                                   placeholder="Contoh: Pakar Perubatan UD54 / JUSA C"
                                   maxlength="50">
                        </div>
                        <div class="invalid-feedback-custom" id="err_grade"></div>
                    </div>

                    <!-- No Telefon -->
                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-medium">
                            NO. TELEFON
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-telephone-fill text-muted"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0"
                                   id="phone"
                                   name="phone"
                                   value="<?= esc($userData['phone'] ?? '') ?>"
                                   placeholder="Contoh: 012-3456789"
                                   maxlength="30">
                        </div>
                        <div class="invalid-feedback-custom" id="err_phone"></div>
                    </div>

                    <!-- Email Address -->
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-medium">
                            E-MEL <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-envelope-fill text-muted"></i>
                            </span>
                            <input type="email"
                                   class="form-control border-start-0 bg-light"
                                   id="email"
                                   name="email"
                                   value="<?= esc($userData['email'] ?? session('email') ?? '') ?>"
                                   placeholder="Emel Rasmi"
                                   maxlength="150"
                                   readonly
                                   required>
                        </div>
                        <div class="invalid-feedback-custom" id="err_email"></div>
                    </div>

                    <!-- Department -->
                    <div class="col-md-6">
                        <label for="department" class="form-label fw-medium">
                            JABATAN
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-building-fill text-muted"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0 bg-light"
                                   id="department"
                                   name="department"
                                   value="<?= esc($userData['department'] ?? session('department') ?? '') ?>"
                                   placeholder="Jabatan / Lokasi"
                                   maxlength="100"
                                   readonly>
                        </div>
                        <div class="invalid-feedback-custom" id="err_department"></div>
                    </div>

                    <!-- Position -->
                    <div class="col-md-6">
                        <label for="position" class="form-label fw-medium">
                            JAWATAN HAKIKI
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-briefcase-fill text-muted"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0 bg-light"
                                   id="position"
                                   name="position"
                                   value="<?= esc($userData['position'] ?? session('position') ?? '') ?>"
                                   placeholder="Jawatan"
                                   maxlength="100"
                                   readonly>
                        </div>
                        <div class="invalid-feedback-custom" id="err_position"></div>
                    </div>

                </div><!-- /.row -->

                <!-- Action -->
                <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary px-4" id="btnNextTab1">
                        Seterusnya: Carian Pesakit & Prosedur
                        <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>

            </form>
        </div><!-- /#tab1 -->

        <!-- ─── TAB 2: Search Patient ─── -->
        <div class="tab-pane-custom d-none" id="tab2">
            <div class="tab-pane-title">
                <i class="bi bi-search text-primary me-2"></i>
                <strong>Tab 2 — Search Patient</strong>
            </div>

            <!-- Search Bar Section -->
            <div class="card border-0 bg-light rounded-3 p-3 mt-3 shadow-sm">
                <label for="search_rn" class="form-label fw-semibold text-secondary small">
                    <i class="bi bi-person-vcard me-1 text-primary"></i> Masukkan Nombor Record Number (RN) Pesakit
                </label>
                <div class="row g-2 align-items-center">
                    <div class="col-md-7 col-lg-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="bi bi-hash text-muted"></i>
                            </span>
                            <input type="text"
                                   class="form-control border-start-0"
                                   id="search_rn"
                                   name="search_rn"
                                   value="<?= $isEdit ? esc($application['patient_rn']) : '' ?>"
                                   placeholder="Contoh: 10000001"
                                   autocomplete="off">
                            <button class="btn btn-primary px-4 fw-medium shadow-sm" type="button" id="btnSearchPatient">
                                <i class="bi bi-search me-1" id="iconSearchPatient"></i>
                                <span id="textSearchPatient">Cari Pesakit</span>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-5 col-lg-6">
                        <span class="text-muted small">
                            <i class="bi bi-info-circle me-1 text-primary"></i> Data pesakit disemak secara terus melalui integrasi API Pesakit.
                        </span>
                    </div>
                </div>
                <div id="patientSearchAlert" class="mt-3" style="display:none;"></div>
            </div>

            <!-- Patient Details Result Card -->
            <div id="patientResultContainer" class="mt-4" style="display:none;">
                <div class="card border-success-subtle shadow-sm rounded-3 overflow-hidden">
                    <div class="card-header bg-success-subtle py-2 px-3 border-0 d-flex align-items-center justify-content-between">
                        <span class="fw-semibold text-success small">
                            <i class="bi bi-check-circle-fill me-1"></i> Rekod Pesakit Ditemui & Disahkan
                        </span>
                        <span class="badge bg-success">API Verified</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-3">
                                <div class="text-muted small fw-medium">Nombor RN</div>
                                <div class="fs-5 fw-bold text-dark font-monospace" id="res_patient_rn">-</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted small fw-medium">Nama Pesakit</div>
                                <div class="fs-6 fw-bold text-primary text-uppercase" id="res_patient_name">-</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted small fw-medium">No. MyKad / MyKid</div>
                                <div class="fs-6 fw-bold text-secondary font-monospace" id="res_patient_ic">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ─── REKOD LAWATAN PESAKIT (VISIT TABS) ─── -->
                <div class="card border-0 shadow-sm rounded-3 mt-4 overflow-hidden" id="visitCardWrapper">
                    <div class="card-header bg-white py-3 px-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark">
                            <i class="bi bi-calendar2-week text-primary me-2"></i> Rekod Lawatan Pesakit (Patient Visits)
                        </span>
                        <span class="badge bg-light text-muted border small">API: /hrs/api/patient-visit</span>
                    </div>
                    <div class="card-body p-3 bg-light">
                        <!-- Tab Pills for Outpatient, Inpatient, Emergency -->
                        <ul class="nav nav-pills nav-fill mb-3 bg-white p-1 rounded-3 border shadow-sm" id="visitTabs">
                            <li class="nav-item">
                                <button type="button" class="nav-link active py-2 fw-semibold" data-type="outpatient" onclick="showVisit('outpatient')">
                                    <i class="bi bi-door-open me-1"></i> OUTPATIENT <span class="badge bg-secondary ms-1" id="count_outpatient">0</span>
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link py-2 fw-semibold" data-type="inpatient" onclick="showVisit('inpatient')">
                                    <i class="bi bi-hospital me-1"></i> INPATIENT <span class="badge bg-secondary ms-1" id="count_inpatient">0</span>
                                </button>
                            </li>
                            <li class="nav-item">
                                <button type="button" class="nav-link py-2 fw-semibold" data-type="emergency" onclick="showVisit('emergency')">
                                    <i class="bi bi-shield-exclamation me-1"></i> EMERGENCY <span class="badge bg-secondary ms-1" id="count_emergency">0</span>
                                </button>
                            </li>
                        </ul>

                        <!-- Visit Cards Container -->
                        <div id="visitContent">
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-info-circle me-1"></i> Sila masukkan nombor RN untuk memuatkan senarai lawatan.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ─── PATIENT VISIT & BILLING CONTEXT ─── -->
                <div id="billingContextWrapper" class="mt-4" style="display:none;">
                    <!-- Visit Summary Card -->
                    <div class="card border-primary-subtle shadow-sm rounded-3 mb-3 overflow-hidden">
                        <div class="card-header bg-primary text-white py-2 px-3 fw-semibold small d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-person-lines-fill me-2"></i> Patient Visit Summary</span>
                            <span class="badge bg-light text-primary">Selected Visit</span>
                        </div>
                        <div class="card-body p-3">
                            <table class="table table-bordered table-sm mb-0 small">
                                <tbody>
                                    <tr>
                                        <th class="bg-light" width="15%">RN</th>
                                        <td width="35%" id="ctx_rn" class="fw-bold font-monospace">-</td>
                                        <th class="bg-light" width="15%">Invc. No</th>
                                        <td width="35%" id="ctx_invc_no" class="font-monospace fw-semibold">-</td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Name</th>
                                        <td id="ctx_name" class="fw-semibold text-uppercase">-</td>
                                        <th class="bg-light">Visit Type</th>
                                        <td id="ctx_visit_type"><span class="badge bg-info text-dark" id="ctx_badge_type">-</span></td>
                                    </tr>
                                    <tr>
                                        <th class="bg-light">Location</th>
                                        <td id="ctx_location">-</td>
                                        <th class="bg-light">Invc. Date</th>
                                        <td id="ctx_invc_date">-</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Side-by-Side: Patient Billing Summary & Select Procedures Performed -->
                    <div class="row g-3">
                        <!-- Left: Patient Billing Summary Card -->
                        <div class="col-lg-6">
                            <div class="card shadow-sm border-0 rounded-3 overflow-hidden h-100">
                                <div class="card-header bg-dark text-white py-2 px-3 d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold small">
                                        <i class="bi bi-file-invoice-dollar me-2 text-warning"></i> Patient Billing Summary
                                    </span>
                                    <div>
                                        <span class="badge bg-primary me-2">
                                            Total Items: <span class="billTotalItem">0</span>
                                        </span>
                                        <span class="badge bg-success">
                                            Net Bill: RM <span class="billNetTotal">0.00</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                                        <table class="table table-hover table-sm table-bordered mb-0 align-middle small">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th width="6%" class="text-center">#</th>
                                                    <th>Item Code & Description</th>
                                                    <th width="8%" class="text-center">Qty</th>
                                                    <th width="15%" class="text-end">Unit (RM)</th>
                                                    <th width="15%" class="text-end text-warning">Adj (RM)</th>
                                                    <th width="15%" class="text-end">Total (RM)</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bill-table-body">
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-3">Tiada rekod bil.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Select Procedures Performed Card -->
                        <div class="col-lg-6">
                            <div class="card shadow-sm border-0 rounded-3 overflow-hidden h-100">
                                <div class="card-header bg-dark text-white py-2 px-3 d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold small">
                                        <i class="bi bi-clipboard2-pulse me-2 text-info"></i> Select Procedures Performed
                                    </span>
                                    <span class="badge bg-info text-dark">
                                        Selected: <span id="procTotalCount">0</span>
                                    </span>
                                </div>
                                <div class="card-body p-3">
                                    <!-- Procedure Selection & Add to List -->
                                    <div class="row g-2 mb-2">
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold text-secondary mb-1">
                                                <i class="bi bi-search me-1"></i> Pilih Prosedur / Perkhidmatan (MMA Master):
                                            </label>
                                            <select class="form-select form-select-sm" id="procSelect">
                                                <option value="" selected disabled>Pilih Prosedur / Perkhidmatan...</option>
                                                <?php if (!empty($masterProcedures)): ?>
                                                    <?php foreach ($masterProcedures as $p): ?>
                                                        <option value="<?= $p['id'] ?>"
                                                            data-code="<?= esc($p['code']) ?>"
                                                            data-name="<?= esc($p['name']) ?>"
                                                            data-category="<?= esc($p['category'] ?? '') ?>"
                                                            data-surgeon-fee="<?= $p['surgeon_fee'] ?>"
                                                            data-anaesthetist-fee="<?= $p['anaesthetist_fee'] ?>">
                                                            <?= esc($p['code']) ?> - <?= esc($p['name']) ?> (RM <?= number_format($p['surgeon_fee'], 2) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row g-2 mb-3 bg-light p-2 rounded-2 border">
                                        <div class="col-sm-5 col-12">
                                            <label class="form-label small fw-semibold text-secondary mb-1">Kategori Caj (Format Borang):</label>
                                            <select class="form-select form-select-sm" id="procChargeType">
                                                <option value="tatacara" selected>Caj Tatacara (Kadar 75%)</option>
                                                <option value="rundingan">Caj Rundingan (Kadar 75%)</option>
                                                <option value="pelaporan">Caj Pelaporan Perubatan (Kadar 70%)</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 col-6">
                                            <label class="form-label small fw-semibold text-secondary mb-1">Tarikh Bil:</label>
                                            <input type="date" class="form-control form-control-sm" id="procBillDate" value="<?= date('Y-m-d') ?>">
                                        </div>
                                        <div class="col-sm-4 col-6">
                                            <label class="form-label small fw-semibold text-secondary mb-1">No. Resit:</label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" class="form-control form-control-sm" id="procReceiptNo" placeholder="No. Resit">
                                                <button type="button" class="btn btn-dark fw-semibold" id="addProcBtn">
                                                    <i class="bi bi-plus-circle me-1"></i> Tambah
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bulk Claim % Setting Bar (Apply to All Procedures) -->
                                    <div class="d-flex flex-wrap align-items-center justify-content-between p-2 bg-light border rounded-2 mb-3">
                                        <div class="d-flex align-items-center mb-1 mb-sm-0">
                                            <span class="small fw-semibold text-secondary me-2">
                                                <i class="bi bi-sliders text-primary me-1"></i> Tetapkan % Tuntutan:
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="input-group input-group-sm" style="width: 95px;">
                                                <input type="number" id="bulkClaimPct" class="form-control form-control-sm text-center font-monospace" min="0" max="100" value="75" placeholder="75">
                                                <span class="input-group-text px-1 small text-muted">%</span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-primary px-2" id="btnApplyAllPct" title="Set claim % for all procedures">
                                                Guna
                                            </button>
                                            <div class="btn-group btn-group-sm ms-1">
                                                <button type="button" class="btn btn-sm btn-outline-primary px-2 fw-semibold" onclick="setAllClaimPct(75)">75% (Piawai PE)</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary px-2" onclick="setAllClaimPct(70)">70% (Laporan)</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary px-2" onclick="setAllClaimPct(100)">100%</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Table: Code | Procedure Name | Price (RM) | Claim (%) | Claim (RM) | Remove -->
                                    <div class="table-responsive" style="max-height: 255px; overflow-y: auto;">
                                        <table class="table table-hover table-sm table-bordered mb-0 align-middle small">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th width="12%">Kod</th>
                                                    <th>Nama Prosedur & Kategori</th>
                                                    <th width="14%" class="text-center">Tarikh/Resit</th>
                                                    <th width="15%" class="text-end">Harga (RM)</th>
                                                    <th width="14%" class="text-center">%</th>
                                                    <th width="16%" class="text-end">Tuntutan (RM)</th>
                                                    <th width="6%" class="text-center"><i class="bi bi-trash"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody id="selectedProcTable">
                                                <tr id="emptyProcRow">
                                                    <td colspan="6" class="text-center text-muted py-3">
                                                        <i class="bi bi-info-circle me-1"></i> No procedures added.
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <tfoot id="selectedProcFooter" class="table-light d-none">
                                                <tr>
                                                    <th colspan="3" class="text-end">Jumlah:</th>
                                                    <th class="text-end font-monospace" id="procTotalPrice">0.00</th>
                                                    <th class="text-center text-muted small">Tuntutan:</th>
                                                    <th class="text-end text-success fw-bold font-monospace" id="procTotalClaim">0.00</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /#patientResultContainer -->

            <!-- Hidden input form for selected patient -->
            <form id="formPatientSelected">
                <?= csrf_field() ?>
                <input type="hidden" id="selected_patient_rn" name="patient_rn" value="<?= $isEdit ? esc($application['patient_rn']) : '' ?>">
                <input type="hidden" id="selected_patient_name" name="patient_name" value="<?= $isEdit ? esc($application['patient_name']) : '' ?>">
                <input type="hidden" id="selected_patient_ic" name="patient_ic" value="<?= $isEdit ? esc($application['patient_ic'] ?? '') : '' ?>">
                <input type="hidden" id="selected_visit_id" name="visit_id" value="<?= $isEdit ? esc($application['visit_id'] ?? '') : '' ?>">
                <input type="hidden" id="selected_procedures_json" name="procedures" value="<?= $isEdit ? esc($application['procedures_data'] ?? '[]') : '[]' ?>">
            </form>

            <!-- Navigation Buttons -->
            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary px-4" onclick="goToTab(1)">
                    <i class="bi bi-arrow-left me-2"></i> Kembali: Maklumat Pakar
                </button>
                <button type="button" class="btn btn-primary px-4 shadow-sm" id="btnNextTab2" disabled title="Sila pilih salah satu episod lawatan pesakit terlebih dahulu">
                    Seterusnya: Butiran Tuntutan <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div><!-- /#tab2 -->

        <!-- ─── TAB 3: Bahagian B & C (Butiran Tuntutan & Maklumat Pengesahan) ─── -->
        <div class="tab-pane-custom d-none" id="tab3">
            <div class="tab-pane-title d-flex justify-content-between align-items-center mb-3">
                <div>
                    <i class="bi bi-file-earmark-ruled-fill text-primary me-2"></i>
                    <strong>BAHAGIAN B: BUTIRAN TUNTUTAN & BAHAGIAN C: MAKLUMAT PENGESAHAN</strong>
                    <span class="badge bg-primary-subtle text-primary font-monospace ms-2">HoSZA-MGT-J3P (PE)-F-003-01</span>
                </div>
                <div class="badge bg-dark text-white px-3 py-2 font-monospace">
                    BULAN: <span id="tab3_badge_month"><?= esc($userData['claim_month'] ?? date('m')) ?></span> / <span id="tab3_badge_year"><?= esc($userData['claim_year'] ?? date('Y')) ?></span>
                </div>
            </div>

            <!-- Context Info Cards: Specialist & Patient Summary -->
            <div class="row g-3 mb-3">
                <!-- Specialist Info Card (Bahagian A Summary) -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-3 h-100 bg-light">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="badge bg-primary p-2 me-2 rounded-circle">
                                    <i class="bi bi-person-badge text-white"></i>
                                </div>
                                <span class="fw-bold small text-uppercase text-secondary">Bahagian A: Maklumat Pegawai Yang Menuntut</span>
                            </div>
                            <div class="small">
                                <div><strong>Nama Pegawai:</strong> <span id="tab3_spec_name"><?= esc($userData['specialist_name'] ?? '-') ?></span></div>
                                <div><strong>No. KP / No. Pekerja:</strong> <span id="tab3_spec_ic"><?= esc($userData['staff_ic'] ?? '-') ?></span> / <span id="tab3_spec_staffno" class="font-monospace"><?= esc($userData['staff_number'] ?? '-') ?></span></div>
                                <div><strong>Jawatan & Gred:</strong> <span id="tab3_spec_grade"><?= esc($userData['grade'] ?? $userData['position'] ?? '-') ?></span></div>
                                <div><strong>No. Telefon / Emel:</strong> <span id="tab3_spec_phone"><?= esc($userData['phone'] ?? '-') ?></span> / <span id="tab3_spec_email"><?= esc($userData['email'] ?? '-') ?></span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patient & Visit Info Card -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-3 h-100 bg-light">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="badge bg-success p-2 me-2 rounded-circle">
                                    <i class="bi bi-person-wheelchair text-white"></i>
                                </div>
                                <span class="fw-bold small text-uppercase text-secondary">Maklumat Pesakit & Lawatan Terpilih</span>
                            </div>
                            <div class="small">
                                <div><strong>Nama Pesakit:</strong> <span id="tab3_patient_name" class="fw-semibold text-uppercase">-</span></div>
                                <div><strong>Nombor RN:</strong> <span id="tab3_patient_rn" class="font-monospace fw-bold text-primary">-</span> | <strong>No. KP:</strong> <span id="tab3_patient_ic">-</span></div>
                                <div><strong>Jenis Lawatan:</strong> <span id="tab3_visit_type">-</span> | <strong>ID Lawatan:</strong> <span id="tab3_visit_id">-</span></div>
                                <div><strong>No. Invois Bil:</strong> <span id="tab3_invc_no">-</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Smart Action Banner: AI Auto-Suggest (80/20) & Tabung Kebajikan (Pilihan A) -->
            <input class="d-none" type="checkbox" id="toggleWelfareFund">
            <div class="card border border-primary-subtle bg-light shadow-sm rounded-3 mb-3 p-3 transition-all" id="aiBannerContainer">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="badge bg-primary bg-gradient p-2 me-3 rounded-3 shadow-sm text-white fs-5">
                            <i class="bi bi-robot"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                <span>Skim Agihan Pintar (AI 80/20 & Tabung Kebajikan)</span>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size: 0.72rem;">Syor Automatik</span>
                            </div>
                            <div class="text-muted small mt-1">
                                Selaraskan prosedur bernilai tinggi (> RM 500) kepada <strong>80% tuntutan pakar</strong> dan salurkan baki <strong>20% ke Tabung Kebajikan Hospital</strong>.
                            </div>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-primary btn-sm px-3 py-2 shadow-sm rounded-2 fw-semibold" id="btnAIToggle" onclick="toggleAISuggestion()">
                            <i class="bi bi-stars me-1 text-warning"></i> Aktifkan AI 80/20 & Tabung Kebajikan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Financial Summary KPI Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 bg-secondary bg-opacity-10 text-center p-3">
                        <div class="text-muted small fw-semibold text-uppercase">Jumlah Kadar Caj Kasar</div>
                        <div class="fs-4 fw-bold font-monospace text-dark mt-1" id="tab3_kpi_gross">RM 0.00</div>
                        <div class="small text-muted">Kadar Caj Keseluruhan Prosedur</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 bg-success bg-opacity-10 text-center p-3 border-start border-success border-4">
                        <div class="text-success small fw-semibold text-uppercase">Jumlah Tuntutan Pakar (RM)</div>
                        <div class="fs-4 fw-bold font-monospace text-success mt-1" id="tab3_kpi_claim">RM 0.00</div>
                        <div class="small text-success">Kadar Agihan Bersih Pakar</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 bg-warning bg-opacity-10 text-center p-3 border-start border-warning border-4">
                        <div class="text-warning-emphasis small fw-semibold text-uppercase">Tabung Kebajikan (Pilihan)</div>
                        <div class="fs-4 fw-bold font-monospace text-warning-emphasis mt-1" id="tab3_kpi_welfare">RM 0.00</div>
                        <div class="small text-muted" id="tab3_kpi_welfare_status"><i class="bi bi-dash-circle me-1"></i>Tidak Diaktifkan</div>
                    </div>
                </div>
            </div>

            <!-- Table Bahagian B: Butiran Tuntutan Format Rasmi HoSZA-MGT-J3P (PE)-F-003-01 -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-dark text-white py-2 px-3 d-flex justify-content-between align-items-center">
                    <span class="fw-semibold small">
                        <i class="bi bi-table me-2 text-warning"></i> BAHAGIAN B: BUTIRAN TUNTUTAN (BORANG HoSZA-MGT-J3P (PE)-F-003-01)
                    </span>
                    <span class="badge bg-primary">
                        Bil. Item: <span id="tab3_proc_count">0</span>
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0 align-middle small">
                            <thead class="table-light">
                                <tr class="text-center align-middle">
                                    <th rowspan="2" width="3%">BIL.</th>
                                    <th rowspan="2" width="14%">NAMA PESAKIT</th>
                                    <th rowspan="2" width="8%">NO. R/N</th>
                                    <th rowspan="2">PROSEDUR / PERKHIDMATAN</th>
                                    <th rowspan="2" width="8%">TARIKH BIL</th>
                                    <th rowspan="2" width="8%">NO. RESIT</th>
                                    <th colspan="2" width="15%" class="bg-primary-subtle text-primary fw-bold">CAJ RUNDINGAN</th>
                                    <th colspan="2" width="15%" class="bg-info-subtle text-dark fw-bold">CAJ TATACARA</th>
                                    <th colspan="2" width="15%" class="bg-secondary-subtle fw-bold">CAJ PELAPORAN</th>
                                    <th rowspan="2" width="10%" class="bg-success-subtle text-success fw-bold">JUMLAH TUNTUTAN (RM)</th>
                                </tr>
                                <tr class="text-center" style="font-size: 0.72rem;">
                                    <th class="bg-primary-subtle text-muted">Kadar Caj (RM)</th>
                                    <th class="bg-primary-subtle text-primary">Agihan (75%)</th>
                                    <th class="bg-info-subtle text-muted">Kadar Caj (RM)</th>
                                    <th class="bg-info-subtle text-dark">Agihan (75%)</th>
                                    <th class="bg-secondary-subtle text-muted">Kadar Caj (RM)</th>
                                    <th class="bg-secondary-subtle">Agihan (70%)</th>
                                </tr>
                            </thead>
                            <tbody id="tab3ClaimTableBody">
                                <tr>
                                    <td colspan="13" class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle me-1"></i> Tiada maklumat prosedur. Sila kembali ke Tab 2 untuk memilih prosedur.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-dark text-center fw-bold" id="tab3ClaimTableFooter">
                                <tr>
                                    <td colspan="6" class="text-end text-uppercase">JUMLAH KESELURUHAN:</td>
                                    <td class="text-end font-monospace" id="tab3_total_rundingan_kadar">0.00</td>
                                    <td class="text-end font-monospace text-warning" id="tab3_total_rundingan_agihan">0.00</td>
                                    <td class="text-end font-monospace" id="tab3_total_tatacara_kadar">0.00</td>
                                    <td class="text-end font-monospace text-warning" id="tab3_total_tatacara_agihan">0.00</td>
                                    <td class="text-end font-monospace" id="tab3_total_pelaporan_kadar">0.00</td>
                                    <td class="text-end font-monospace text-warning" id="tab3_total_pelaporan_agihan">0.00</td>
                                    <td class="text-end text-success font-monospace fs-6" id="tab3_total_keseluruhan_tuntutan">RM 0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- BAHAGIAN C: Maklumat Pengesahan (Pegawai Yang Menuntut) -->
            <div class="card shadow-sm border border-primary-subtle rounded-3 mb-4 bg-light">
                <div class="card-header bg-primary bg-opacity-10 py-2 px-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-primary small text-uppercase">
                        <i class="bi bi-shield-check me-1"></i> BAHAGIAN C: MAKLUMAT PENGESAHAN (PEGAWAI YANG MENUNTUT)
                    </span>
                    <span class="badge bg-primary text-white font-monospace">Pengesahan Wajib</span>
                </div>
                <div class="card-body p-3">
                    <div class="p-3 bg-white border border-primary border-opacity-25 rounded-2 shadow-sm mb-3">
                        <div class="form-check">
                            <input class="form-check-input ms-0 me-2" type="checkbox" id="declarationCheck" <?= $isEdit ? 'checked' : '' ?> onchange="checkDeclarationState()">
                            <label class="form-check-label fw-bold text-dark fs-6" for="declarationCheck">
                                "Dengan ini saya mengesahkan bahawa Tuntutan Bayaran Pakar di bawah Perkhidmatan Eksekutif seperti maklumat yang disediakan adalah betul dan dilaksanakan oleh saya."
                            </label>
                        </div>
                    </div>
                    <div class="row g-2 small text-muted">
                        <div class="col-md-6">
                            <strong>Pegawai Yang Mengesahkan:</strong> <span class="text-dark fw-semibold" id="tab3_decl_name"><?= esc($userData['specialist_name']) ?></span>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <strong>Tarikh Pengesahan:</strong> <span class="text-dark fw-semibold"><?= date('d/m/Y') ?></span>
                        </div>
                    </div>
                    <div class="mt-3 pt-2 border-top">
                        <label class="form-label fw-semibold small text-secondary">
                            <i class="bi bi-chat-left-text me-1"></i> Catatan Tambahan Pemohon (Pilihan):
                        </label>
                        <textarea class="form-control form-control-sm" id="claim_remarks" rows="2" placeholder="Masukkan sebarang nota atau rujukan tambahan jika perlu..."><?= $isEdit ? esc($application['remarks'] ?? '') : '' ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Workflow Preview Notice (Aliran 5-Peringkat) -->
            <div class="alert alert-info border-0 shadow-sm py-2 px-3 d-flex align-items-center mb-4 rounded-3 small">
                <i class="bi bi-info-circle-fill me-2 fs-5 text-primary"></i>
                <div>
                    <strong>Aliran Kelulusan Selepas Dihantar:</strong> Permohonan ini akan disalurkan mengikut giliran: 
                    <span class="badge bg-white text-dark border ms-1">1. Pegawai Penyemak PE</span> &rarr;
                    <span class="badge bg-white text-dark border ms-1">2. Pegawai Perkhidmatan PE</span> &rarr;
                    <span class="badge bg-white text-dark border ms-1">3. Ketua J3P</span> &rarr;
                    <span class="badge bg-white text-dark border ms-1">4. Pengarah Hospital</span>.
                </div>
            </div>

            <!-- Navigation & Submission Buttons -->
            <div class="d-flex justify-content-between pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary px-4" onclick="goToTab(2)">
                    <i class="bi bi-arrow-left me-2"></i> Kembali: Carian Pesakit & Prosedur
                </button>
                <button type="button" class="btn <?= $isEdit ? 'btn-warning' : 'btn-success' ?> px-5 shadow-sm fw-semibold" id="btnSubmitClaimApp" <?= $isEdit ? '' : 'disabled' ?>>
                    <?php if ($isEdit): ?>
                        <i class="bi bi-check-circle-fill me-2"></i> Kemaskini Permohonan Tuntutan
                    <?php else: ?>
                        <i class="bi bi-send-check me-2"></i> Hantar Permohonan Tuntutan
                    <?php endif; ?>
                </button>
            </div>
        </div><!-- /#tab3 -->

    </div><!-- /.card-panel-body -->
</div><!-- /.card-panel -->

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ──────────────────────────────────────────────────────────────────
// Tab navigation helpers
// ──────────────────────────────────────────────────────────────────
let currentTab = 1;
const TOTAL_TABS = 3;

function goToTab(num) {
    // Hide all panes
    for (let i = 1; i <= TOTAL_TABS; i++) {
        document.getElementById('tab' + i).classList.add('d-none');
        const ind = document.getElementById('step-indicator-' + i);
        if (ind) ind.classList.remove('active', 'completed');
    }

    // Mark completed steps
    for (let i = 1; i < num; i++) {
        const ind = document.getElementById('step-indicator-' + i);
        if (ind) ind.classList.add('completed');
        const line = document.getElementById('step-line-' + i);
        if (line) line.classList.add('completed');
    }

    // Activate current step
    const activeInd = document.getElementById('step-indicator-' + num);
    if (activeInd) activeInd.classList.add('active');

    // Show target pane
    document.getElementById('tab' + num).classList.remove('d-none');
    currentTab = num;

    if (num === 3) {
        renderTab3ClaimDetails();
    }

    // Scroll to top of card
    document.querySelector('.card-panel').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ──────────────────────────────────────────────────────────────────
// Tab 1 — Specialist form submission
// ──────────────────────────────────────────────────────────────────
$('#formSpecialist').on('submit', function (e) {
    e.preventDefault();

    // Clear previous errors
    clearErrors();

    const btn = $('#btnNextTab1');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Validating...');

    $.ajax({
        url:    BASE_URL + 'new-application/store-specialist',
        method: 'POST',
        data:   $(this).serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success') {
                goToTab(2);
            } else {
                displayErrors(res.errors);
            }
        },
        error: function () {
            Swal.fire({ icon: 'error', title: 'Server Error', text: 'Please try again.' });
        },
        complete: function () {
            btn.prop('disabled', false).html('Next: Search Patient <i class="bi bi-arrow-right ms-2"></i>');
        }
    });
});

// ──────────────────────────────────────────────────────────────────
// Tab 2 — Search Patient, Visits & Billing via API
// ──────────────────────────────────────────────────────────────────
const isEditMode  = <?= $isEdit ? 'true' : 'false' ?>;
const editAppId   = <?= $isEdit ? (int)$application['id'] : 'null' ?>;
const editAppData = <?= $isEdit ? json_encode($application) : 'null' ?>;

let selectedPatient    = null;
let currentPatient     = { rn: null, name: null, nric: null };
let visitData          = { outpatient: [], inpatient: [], emergency: [] };
let selectedVisit      = null;
let patientContext     = null;
let selectedProcedures = <?= $isEdit ? (!empty($application['procedures_data']) ? $application['procedures_data'] : '[]') : json_encode(session('new_app_procedures') ?? []) ?>;

function performPatientSearch(onSuccess) {
    const rn = $('#search_rn').val().trim();
    const alertBox = $('#patientSearchAlert');
    const resultBox = $('#patientResultContainer');
    const btnNext = $('#btnNextTab2');
    const btnSearch = $('#btnSearchPatient');
    const iconSearch = $('#iconSearchPatient');
    const textSearch = $('#textSearchPatient');

    alertBox.hide().empty();

    if (!rn) {
        alertBox.html('<div class="alert alert-warning py-2 px-3 small mb-0 rounded-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Sila masukkan nombor RN pesakit terlebih dahulu.</div>').slideDown();
        $('#search_rn').focus();
        return;
    }

    // Set loading state
    btnSearch.prop('disabled', true);
    iconSearch.removeClass('bi-search').addClass('spinner-border spinner-border-sm me-1');
    textSearch.text('Menyemak...');

    $.ajax({
        url: BASE_URL + 'new-application/search-patient',
        method: 'POST',
        data: {
            '<?= csrf_token() ?>': $('[name="<?= csrf_token() ?>"]').val() || '<?= csrf_hash() ?>',
            rn: rn
        },
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success' && res.patient) {
                const p = res.patient;
                selectedPatient = p;
                currentPatient = { rn: p.rn, name: p.name, nric: p.ic };

                const isSameEditPatient = isEditMode && editAppData && editAppData.patient_rn === p.rn;
                if (!isSameEditPatient) {
                    selectedVisit  = null;
                    patientContext = null;
                    $('#billingContextWrapper').hide();
                    $('#selected_visit_id').val('');
                }

                // Populate display card
                $('#res_patient_rn').text(p.rn);
                $('#res_patient_name').text(p.name);
                $('#res_patient_ic').text(p.ic || 'N/A');

                // Populate hidden inputs
                $('#selected_patient_rn').val(p.rn);
                $('#selected_patient_name').val(p.name);
                $('#selected_patient_ic').val(p.ic || '');

                resultBox.slideDown();
                checkTab2NextButtonState();

                alertBox.html('<div class="alert alert-success py-2 px-3 small mb-0 rounded-2"><i class="bi bi-check-circle-fill me-1"></i> Rekod pesakit ditemui. Sila pilih salah satu episod lawatan di bawah.</div>').slideDown();

                // Load all visits from HRS API
                loadAllVisits(p.rn, onSuccess);
            } else {
                selectedPatient = null;
                resultBox.slideUp();
                btnNext.prop('disabled', true);
                alertBox.html('<div class="alert alert-danger py-2 px-3 small mb-0 rounded-2"><i class="bi bi-x-circle-fill me-1"></i> ' + (res.message || 'Rekod pesakit tidak dijumpai.') + '</div>').slideDown();
            }
        },
        error: function () {
            // Fallback for edit mode if API is unreachable
            if (isEditMode && editAppData && editAppData.patient_rn === rn) {
                selectedPatient = { rn: editAppData.patient_rn, name: editAppData.patient_name, ic: editAppData.patient_ic };
                currentPatient = { ...selectedPatient };
                $('#res_patient_rn').text(selectedPatient.rn);
                $('#res_patient_name').text(selectedPatient.name);
                $('#res_patient_ic').text(selectedPatient.ic || 'N/A');
                resultBox.slideDown();
                checkTab2NextButtonState();
                alertBox.html('<div class="alert alert-info py-2 px-3 small mb-0 rounded-2"><i class="bi bi-info-circle-fill me-1"></i> Menggunakan rekod pesakit sedia ada dari permohonan.</div>').slideDown();
                if (typeof onSuccess === 'function') {
                    onSuccess(visitData);
                }
            } else {
                selectedPatient = null;
                resultBox.slideUp();
                btnNext.prop('disabled', true);
                alertBox.html('<div class="alert alert-danger py-2 px-3 small mb-0 rounded-2"><i class="bi bi-exclamation-octagon-fill me-1"></i> Gagal menghubungi pelayan API Pesakit. Sila cuba lagi.</div>').slideDown();
            }
        },
        complete: function () {
            btnSearch.prop('disabled', false);
            iconSearch.removeClass('spinner-border spinner-border-sm me-1').addClass('bi-search me-1');
            textSearch.text('Cari Pesakit');
        }
    });
}

$('#btnSearchPatient').on('click', performPatientSearch);

$('#search_rn').on('keypress', function (e) {
    if (e.which === 13) {
        e.preventDefault();
        performPatientSearch();
    }
});

// Load all visits (Outpatient, Inpatient, Emergency) from HRS API
function loadAllVisits(rn, onSuccess) {
    const container = document.getElementById('visitContent');
    container.innerHTML = '<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2 text-primary"></span>Memuatkan rekod lawatan dari API...</div>';

    $.ajax({
        url: BASE_URL + 'new-application/getVisitsAll',
        method: 'GET',
        data: { rn: rn },
        dataType: 'json',
        success: function (result) {
            if (result.status === 'success') {
                visitData = result.data || { outpatient: [], inpatient: [], emergency: [] };

                // Update badge counts
                $('#count_outpatient').text((visitData.outpatient || []).length);
                $('#count_inpatient').text((visitData.inpatient || []).length);
                $('#count_emergency').text((visitData.emergency || []).length);

                // Default show outpatient
                showVisit('outpatient');

                if (typeof onSuccess === 'function') {
                    onSuccess(visitData);
                }
            } else {
                container.innerHTML = '<div class="alert alert-warning py-2 px-3 small mb-0"><i class="bi bi-exclamation-circle me-1"></i> ' + (result.message || 'Tiada rekod lawatan.') + '</div>';
            }
        },
        error: function () {
            container.innerHTML = '<div class="alert alert-danger py-2 px-3 small mb-0"><i class="bi bi-exclamation-triangle me-1"></i> Ralat sambungan semasa memuatkan rekod lawatan.</div>';
        }
    });
}

function showVisit(type) {
    // Remove active class from all tabs
    document.querySelectorAll('#visitTabs .nav-link').forEach(btn => {
        btn.classList.remove('active');
    });

    // Set active on clicked tab
    const activeBtn = document.querySelector(`#visitTabs .nav-link[data-type="${type}"]`);
    if (activeBtn) {
        activeBtn.classList.add('active');
    }

    const data = (visitData && visitData[type]) ? visitData[type] : [];
    renderVisitCards(data, type);
}

function renderVisitCards(data, type) {
    const container = document.getElementById('visitContent');

    if (!data || data.length === 0) {
        container.innerHTML = `<div class="text-center text-muted py-4"><i class="bi bi-inbox fs-3 d-block mb-1 opacity-50"></i> Tiada rekod lawatan <strong>${type.toUpperCase()}</strong> dijumpai untuk pesakit ini.</div>`;
        return;
    }

    let html = '<div class="row g-2">';

    data.forEach((v, index) => {
        let details = '';
        if (v.visit_type === 'OUTPATIENT') {
            details = `<div><small class="text-muted">Masa Tiba:</small> <strong>${v.time?.arrive || '-'}</strong></div>
                       <div><small class="text-muted">Masa Berlepas:</small> <strong>${v.time?.depart || '-'}</strong></div>`;
        } else if (v.visit_type === 'INPATIENT') {
            details = `<div><small class="text-muted">Kemasukan:</small> <strong>${v.time?.admission || '-'}</strong></div>
                       <div><small class="text-muted">Dikeluarkan:</small> <strong>${v.time?.discharge || '-'}</strong></div>`;
        } else if (v.visit_type === 'EMERGENCY') {
            details = `<div><small class="text-muted">Masa Tiba:</small> <strong>${v.time?.arrive || '-'}</strong></div>
                       <div><small class="text-muted">Dikeluarkan:</small> <strong>${v.time?.discharge || '-'}</strong></div>`;
        }

        const isSelected = selectedVisit && selectedVisit.visit && selectedVisit.visit.visit_id == v.visit_id;
        const borderClass = isSelected ? 'border-primary shadow' : 'border';
        const cardBg = isSelected ? 'bg-primary-subtle' : 'bg-white';

        html += `
        <div class="col-md-6">
            <div class="card ${cardBg} ${borderClass} rounded-3 p-3 h-100 transition-all visit-card" id="visit_card_${v.visit_id}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">${v.visit_type || type.toUpperCase()}</span>
                        <h6 class="fw-bold mb-0 mt-1 text-dark">${v.location || 'Hospital Location'}</h6>
                    </div>
                    <span class="font-monospace small text-muted">ID: ${v.visit_id || index + 1}</span>
                </div>
                <div class="small mb-3">
                    ${details}
                </div>
                <div class="mt-auto text-end">
                    <button type="button" class="btn btn-sm ${isSelected ? 'btn-success' : 'btn-outline-primary'} px-3 rounded-pill fw-medium"
                            onclick='selectVisit(${JSON.stringify(v)})'>
                        <i class="bi ${isSelected ? 'bi-check-circle-fill' : 'bi-cursor'} me-1"></i>
                        ${isSelected ? 'Lawatan Dipilih' : 'Pilih Lawatan'}
                    </button>
                </div>
            </div>
        </div>`;
    });

    html += '</div>';
    container.innerHTML = html;
}

// Select a visit and check for existing claims (Pendekatan 2)
function selectVisit(visit) {
    if (!visit || !visit.visit_id) return;

    const checkData = { visit_id: visit.visit_id };
    if (isEditMode && editAppId) {
        checkData.exclude_id = editAppId;
    }

    // Semak sama ada permohonan telah wujud bagi visit_id ini
    $.ajax({
        url: BASE_URL + 'new-application/check-visit-claim',
        method: 'GET',
        data: checkData,
        dataType: 'json',
        success: function (res) {
            if (res && res.status === 'exists' && res.claims && res.claims.length > 0) {
                // Bina jadual senarai permohonan sedia ada
                let claimsRows = '';
                res.claims.forEach(c => {
                    let stBadge = '<span class="badge bg-warning text-dark">Dihantar</span>';
                    if (c.status === 'approved') {
                        stBadge = '<span class="badge bg-success">Diluluskan</span>';
                    } else if (c.status === 'under_review') {
                        stBadge = '<span class="badge bg-info">Dalam Semakan</span>';
                    }

                    claimsRows += `
                        <tr>
                            <td class="font-monospace fw-bold text-primary">
                                <a href="${BASE_URL}new-application/show/${c.id}" target="_blank" class="text-decoration-none" title="Buka Permohonan">
                                    ${escapeHtml(c.application_no)} <i class="bi bi-box-arrow-up-right small"></i>
                                </a>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">${escapeHtml(c.specialist_name)}</div>
                                <small class="text-muted">${escapeHtml(c.department || '')}</small>
                            </td>
                            <td class="text-end font-monospace fw-bold text-success">
                                RM ${Number(c.total_claim || 0).toFixed(2)}
                            </td>
                            <td class="text-center">${stBadge}</td>
                        </tr>
                    `;
                });

                const modalHtml = `
                    <div class="text-start mb-3">
                        <div class="alert alert-warning border-warning p-2.5 small mb-3 text-dark">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-1 fs-6 align-middle"></i>
                            Terdapat <strong>${res.count} permohonan tuntutan</strong> sedia ada yang telah didaftarkan bagi episod lawatan ini (ID: <code>${escapeHtml(visit.visit_id)}</code>).
                        </div>
                        <p class="text-muted small mb-2">
                            <strong>Makluman:</strong> Satu episod lawatan boleh mempunyai tuntutan berasingan oleh pakar berbeza bagi prosedur masing-masing (cth: Pakar Surgeri & Pakar Bius). Sila pastikan prosedur yang bakal anda tuntut tidak bertindih dengan tuntutan sedia ada di bawah:
                        </p>
                        <div class="table-responsive border rounded" style="max-height: 200px; overflow-y: auto;">
                            <table class="table table-sm table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Permohonan</th>
                                        <th>Pakar Pemohon</th>
                                        <th class="text-end">Jumlah Bersih</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${claimsRows}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;

                Swal.fire({
                    title: 'Tuntutan Terdahulu Dijumpai',
                    html: modalHtml,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Teruskan Tuntutan Baharu',
                    cancelButtonText: '<i class="bi bi-x-circle me-1"></i> Batal & Pilih Lawatan Lain',
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    width: '650px',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        proceedWithVisitSelection(visit);
                    }
                });
            } else {
                proceedWithVisitSelection(visit);
            }
        },
        error: function () {
            // Jika semakan ralat, teruskan pemilihan lawatan seperti biasa
            proceedWithVisitSelection(visit);
        }
    });
}

function proceedWithVisitSelection(visit) {
    selectedVisit = {
        rn: currentPatient.rn,
        patient_name: currentPatient.name,
        nric: currentPatient.nric,
        visit: visit
    };

    $('#selected_visit_id').val(visit.visit_id || '');

    // Re-render visit cards to show active selection
    const activeType = document.querySelector('#visitTabs .nav-link.active')?.getAttribute('data-type') || 'outpatient';
    renderVisitCards(visitData[activeType] || [], activeType);

    // Semak status butang seterusnya (perlu lawatan + sekurang-kurangnya 1 prosedur)
    checkTab2NextButtonState();

    // Load billing context
    loadPatientContext(visit.visit_id);
}

function loadPatientContext(visitId) {
    const contextBox = $('#billingContextWrapper');
    contextBox.slideDown();

    // Show loading indicators
    $('#ctx_rn').text(currentPatient.rn || '-');
    $('#ctx_name').text(currentPatient.name || '-');
    $('#ctx_visit_type').html('<span class="badge bg-primary text-white">' + (selectedVisit?.visit?.visit_type || '-') + '</span>');
    $('#ctx_location').text(selectedVisit?.visit?.location || '-');
    $('#ctx_invc_no').html('<span class="spinner-border spinner-border-sm"></span>');
    $('#ctx_invc_date').html('<span class="spinner-border spinner-border-sm"></span>');

    document.querySelectorAll('.bill-table-body').forEach(tb => {
        tb.innerHTML = '<tr><td colspan="6" class="text-center py-3 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>Memuatkan maklumat bil...</td></tr>';
    });

    $.ajax({
        url: BASE_URL + 'new-application/getPatientContext',
        method: 'GET',
        data: { visit_id: visitId },
        dataType: 'json',
        success: function (result) {
            if (result && result.status === 'success' && result.data) {
                patientContext = result.data;
                renderPatientSummary();
                renderBillingSummary();
            } else {
                $('#ctx_invc_no').text(result?.message || 'N/A');
                $('#ctx_invc_date').text('-');
                document.querySelectorAll('.bill-table-body').forEach(tb => {
                    tb.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">' + (result?.message || 'Tiada rekod bil bagi lawatan ini.') + '</td></tr>';
                });
                document.querySelectorAll('.billTotalItem').forEach(x => { x.textContent = '0'; });
                document.querySelectorAll('.billNetTotal').forEach(x => { x.textContent = '0.00'; });
            }
        },
        error: function () {
            $('#ctx_invc_no').text('Ralat API');
            $('#ctx_invc_date').text('Ralat API');
            document.querySelectorAll('.bill-table-body').forEach(tb => {
                tb.innerHTML = '<tr><td colspan="6" class="text-center text-danger py-3">Gagal menghubungi API Bil Pesakit.</td></tr>';
            });
        }
    });

    // Smooth scroll to billing section
    document.getElementById('billingContextWrapper').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function renderPatientSummary() {
    if (!patientContext || !patientContext.summary) return;
    const s = patientContext.summary;

    $('#ctx_rn').text(selectedVisit?.rn || currentPatient.rn);
    $('#ctx_name').text(selectedVisit?.patient_name || currentPatient.name);
    $('#ctx_visit_type').html('<span class="badge bg-primary text-white">' + (selectedVisit?.visit?.visit_type || '-') + '</span>');
    $('#ctx_location').text(selectedVisit?.visit?.location || '-');
    $('#ctx_invc_no').text(s.invc_no || '-');
    $('#ctx_invc_date').text(s.invc_date || '-');
}

function renderBillingSummary() {
    if (!patientContext) return;
    const summary = patientContext.summary || {};
    const items   = patientContext.items || [];

    let rows = '';

    if (items.length === 0) {
        rows = '<tr><td colspan="6" class="text-center text-muted py-3">Tiada item bil direkodkan.</td></tr>';
    } else {
        items.forEach((r, i) => {
            rows += `
            <tr>
                <td class="text-center text-muted">${i + 1}</td>
                <td>
                    <code class="text-primary fw-semibold">${r.item_code || ''}</code><br>
                    <span class="text-dark">${r.item_desc || 'Pemeriksaan / Prosedur'}</span>
                </td>
                <td class="text-center">${r.quantity || 1}</td>
                <td class="text-end font-monospace">${Number(r.unit_price || 0).toFixed(2)}</td>
                <td class="text-end font-monospace text-warning">${Number(r.adjustment || 0).toFixed(2)}</td>
                <td class="text-end font-monospace fw-bold">${Number(r.total_price || 0).toFixed(2)}</td>
            </tr>`;
        });
    }

    document.querySelectorAll('.bill-table-body').forEach(tb => {
        tb.innerHTML = rows;
    });

    document.querySelectorAll('.billTotalItem').forEach(x => {
        x.textContent = items.length;
    });

    document.querySelectorAll('.billNetTotal').forEach(x => {
        x.textContent = Number(summary.amount || 0).toFixed(2);
    });
}

// ──────────────────────────────────────────────────────────────────
// Tab 2 — Select Procedures Performed Logic & Claim Percentage
// ──────────────────────────────────────────────────────────────────
$('#addProcBtn').on('click', function () {
    const sel = document.getElementById('procSelect');
    if (!sel || sel.selectedIndex < 0) return;
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Sila pilih prosedur dari senarai terlebih dahulu.'
        });
        return;
    }

    const procId = opt.value;
    const code = opt.getAttribute('data-code');
    const name = opt.getAttribute('data-name');
    const surgeonFee = parseFloat(opt.getAttribute('data-surgeon-fee')) || 0;
    const anaesthetistFee = parseFloat(opt.getAttribute('data-anaesthetist-fee')) || 0;

    // Default fee selection: surgeon fee, or anaesthetist fee if role is anaesthetist or surgeon fee is 0
    const userRole = "<?= strtolower(session('role_name') ?? session('role') ?? '') ?>";
    let price = surgeonFee;
    if (userRole === 'anaesthetist') {
        price = anaesthetistFee;
    } else if (price === 0 && anaesthetistFee > 0) {
        price = anaesthetistFee;
    }

    // Check duplicate
    const exists = selectedProcedures.some(p => String(p.proc_id) === String(procId));
    if (exists) {
        Swal.fire({
            icon: 'info',
            title: 'Makluman',
            text: 'Prosedur ini telah berada di dalam senarai.'
        });
        return;
    }

    const chargeType = $('#procChargeType').val() || 'tatacara';
    const billDate = $('#procBillDate').val() || '';
    const receiptNo = $('#procReceiptNo').val().trim() || '';

    // Standard rate based on official form: Pelaporan is 70%, Tatacara & Rundingan is 75%
    let defaultPct = chargeType === 'pelaporan' ? 70 : 75;
    const currentBulk = parseFloat($('#bulkClaimPct').val());
    if (!isNaN(currentBulk) && currentBulk !== 75 && currentBulk !== 70) {
        defaultPct = Math.min(100, Math.max(0, currentBulk));
    }

    selectedProcedures.push({
        id: procId + '_' + Date.now(),
        proc_id: procId,
        code: code,
        name: name,
        price: price,
        surgeon_fee: surgeonFee,
        anaesthetist_fee: anaesthetistFee,
        charge_type: chargeType,
        bill_date: billDate,
        receipt_no: receiptNo,
        claimPct: defaultPct
    });

    renderSelectedProcedures();
    sel.selectedIndex = 0;
    $('#procReceiptNo').val('');
});

function removeProcedure(id) {
    selectedProcedures = selectedProcedures.filter(p => p.id !== id);
    renderSelectedProcedures();
}

// Set claim percentage for all procedures at once
function setAllClaimPct(pct) {
    let num = parseFloat(pct);
    if (isNaN(num)) num = 75;
    num = Math.min(100, Math.max(0, num));
    $('#bulkClaimPct').val(num);

    if (selectedProcedures.length === 0) return;

    selectedProcedures.forEach(p => {
        p.claimPct = num;
    });

    renderSelectedProcedures();
}

$('#btnApplyAllPct').on('click', function () {
    const val = $('#bulkClaimPct').val();
    setAllClaimPct(val);
});

// Update claim percentage for a single procedure
function updateProcedurePct(id, val) {
    const p = selectedProcedures.find(x => x.id === id);
    if (!p) return;

    let num = parseFloat(val);
    if (isNaN(num)) num = 0;
    num = Math.min(100, Math.max(0, num));
    p.claimPct = num;

    const fee = parseFloat(p.price) || 0;
    const claimAmt = fee * (num / 100);

    const amtEl = document.getElementById('proc_claim_amt_' + id);
    if (amtEl) {
        amtEl.textContent = claimAmt.toFixed(2);
    }

    recalculateProcedureTotals();
    updateProceduresHiddenInput();
}

function recalculateProcedureTotals() {
    let totalPrice = 0;
    let totalClaim = 0;

    selectedProcedures.forEach(p => {
        const fee = parseFloat(p.price) || 0;
        const cType = p.charge_type || 'tatacara';
        const defaultPct = cType === 'pelaporan' ? 70 : 75;
        const pct = (typeof p.claimPct !== 'undefined' && p.claimPct !== null) ? parseFloat(p.claimPct) : defaultPct;
        totalPrice += fee;
        totalClaim += fee * (pct / 100);
    });

    const priceEl = document.getElementById('procTotalPrice');
    const claimEl = document.getElementById('procTotalClaim');
    if (priceEl) priceEl.textContent = totalPrice.toFixed(2);
    if (claimEl) claimEl.textContent = totalClaim.toFixed(2);
}

function renderSelectedProcedures() {
    const tbody = document.getElementById('selectedProcTable');
    const totalCountEl = document.getElementById('procTotalCount');
    const tfoot = document.getElementById('selectedProcFooter');

    if (!tbody) return;

    if (!selectedProcedures || selectedProcedures.length === 0) {
        tbody.innerHTML = `<tr id="emptyProcRow"><td colspan="7" class="text-center text-muted py-3"><i class="bi bi-info-circle me-1"></i> Tiada prosedur ditambah. Sila pilih prosedur di atas.</td></tr>`;
        if (totalCountEl) totalCountEl.textContent = '0';
        if (tfoot) tfoot.classList.add('d-none');
        updateProceduresHiddenInput();
        checkTab2NextButtonState();
        return;
    }

    if (totalCountEl) totalCountEl.textContent = selectedProcedures.length;
    let rows = '';

    selectedProcedures.forEach((p) => {
        const fee = parseFloat(p.price) || 0;
        const cType = p.charge_type || 'tatacara';
        const defaultPct = cType === 'pelaporan' ? 70 : 75;
        const pct = (typeof p.claimPct !== 'undefined' && p.claimPct !== null) ? parseFloat(p.claimPct) : defaultPct;
        p.claimPct = pct;
        const claimAmt = fee * (pct / 100);

        let typeBadge = '<span class="badge bg-info text-dark font-monospace" style="font-size: 0.68rem;">TATACARA (75%)</span>';
        if (cType === 'rundingan') {
            typeBadge = '<span class="badge bg-primary text-white font-monospace" style="font-size: 0.68rem;">RUNDINGAN (75%)</span>';
        } else if (cType === 'pelaporan') {
            typeBadge = '<span class="badge bg-secondary text-white font-monospace" style="font-size: 0.68rem;">PELAPORAN (70%)</span>';
        }

        const dateStr = p.bill_date ? escapeHtml(p.bill_date) : '-';
        const resitStr = p.receipt_no ? escapeHtml(p.receipt_no) : '-';

        rows += `
            <tr id="proc_row_${p.id}">
                <td class="font-monospace fw-semibold text-primary align-middle">${escapeHtml(p.code)}</td>
                <td class="align-middle">
                    <div class="fw-semibold text-dark">${escapeHtml(p.name)}</div>
                    <div class="mt-1">${typeBadge}</div>
                </td>
                <td class="text-center small text-muted align-middle">
                    <div class="text-nowrap"><i class="bi bi-calendar-event me-1"></i>${dateStr}</div>
                    <div class="font-monospace text-dark fw-semibold"><i class="bi bi-receipt me-1"></i>${resitStr}</div>
                </td>
                <td class="text-end font-monospace align-middle">${fee.toFixed(2)}</td>
                <td class="text-center align-middle">
                    <div class="input-group input-group-sm justify-content-center mx-auto" style="max-width: 88px;">
                        <input type="number" class="form-control form-control-sm text-center px-1 font-monospace"
                               min="0" max="100" step="1"
                               value="${pct}"
                               oninput="updateProcedurePct('${p.id}', this.value)"
                               onchange="updateProcedurePct('${p.id}', this.value)">
                        <span class="input-group-text px-1 small text-muted">%</span>
                    </div>
                </td>
                <td class="text-end font-monospace fw-bold text-success align-middle" id="proc_claim_amt_${p.id}">
                    ${claimAmt.toFixed(2)}
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" onclick="removeProcedure('${p.id}')" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = rows;
    recalculateProcedureTotals();
    if (tfoot) tfoot.classList.remove('d-none');

    updateProceduresHiddenInput();
    checkTab2NextButtonState();
}

function updateProceduresHiddenInput() {
    let input = document.getElementById('selected_procedures_json');
    if (!input) {
        input = document.createElement('input');
        input.type = 'hidden';
        input.id = 'selected_procedures_json';
        input.name = 'procedures';
        const form = document.getElementById('formPatientSelected');
        if (form) form.appendChild(input);
    }
    input.value = JSON.stringify(selectedProcedures);
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Function to check and update the enabled/disabled state of btnNextTab2
function checkTab2NextButtonState() {
    const hasPatient = !!(selectedPatient && selectedPatient.rn);
    const hasVisit = !!(selectedVisit && $('#selected_visit_id').val());
    const hasProcedures = !!(selectedProcedures && selectedProcedures.length > 0);

    const btn = $('#btnNextTab2');
    if (hasPatient && hasVisit && hasProcedures) {
        btn.prop('disabled', false).removeAttr('title');
    } else {
        let reason = '';
        if (!hasPatient) {
            reason = 'Sila buat carian pesakit terlebih dahulu';
        } else if (!hasVisit) {
            reason = 'Sila pilih salah satu episod lawatan pesakit';
        } else if (!hasProcedures) {
            reason = 'Sila tambah sekurang-kurangnya satu prosedur yang dituntut';
        }
        btn.prop('disabled', true).attr('title', reason);
    }
}

$(document).ready(function () {
    renderSelectedProcedures();
    checkTab2NextButtonState();
    checkDeclarationState();

    if (isEditMode && editAppData) {
        // Automatically restore patient & visits
        if (editAppData.patient_rn) {
            $('#search_rn').val(editAppData.patient_rn);
            performPatientSearch(function (vData) {
                if (editAppData.visit_id) {
                    for (let type of ['outpatient', 'inpatient', 'emergency']) {
                        const found = (vData[type] || []).find(v => v.visit_id == editAppData.visit_id);
                        if (found) {
                            showVisit(type);
                            proceedWithVisitSelection(found);
                            break;
                        }
                    }
                }
            });
        }

        // Restore remarks
        if (editAppData.remarks) {
            $('#claim_remarks').val(editAppData.remarks);
        }

        // Restore welfare toggle state if total_welfare > 0
        if (parseFloat(editAppData.total_welfare || 0) > 0) {
            isAISuggestionActive = true;
            $('#toggleWelfareFund').prop('checked', true);
            $('#btnAIToggle')
                .removeClass('btn-outline-primary')
                .addClass('btn-success')
                .html('<i class="bi bi-check-circle-fill me-1"></i> AI 80/20 & Kebajikan Aktif <span class="badge bg-white text-success ms-1 small">Klik Reset</span>');
            $('#aiBannerContainer')
                .removeClass('border-primary-subtle bg-light')
                .addClass('border-success bg-success bg-opacity-10');
        }
    }
});

// Proceed from Tab 2 to Tab 3
$('#btnNextTab2').on('click', function () {
    if (!selectedPatient) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Sila buat carian pesakit terlebih dahulu.' });
        return;
    }

    const visitId = $('#selected_visit_id').val() || selectedVisit?.visit?.visit_id;
    if (!selectedVisit || !visitId) {
        Swal.fire({
            icon: 'warning',
            title: 'Lawatan Belum Dipilih',
            text: 'Sila pilih salah satu episod lawatan pesakit (Outpatient / Inpatient / Emergency) sebelum meneruskan ke butiran tuntutan.'
        });
        return;
    }

    if (!selectedProcedures || selectedProcedures.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Prosedur Diperlukan',
            text: 'Sila pilih dan tambah sekurang-kurangnya satu prosedur yang dituntut pada ruangan Select Procedures Performed sebelum meneruskan ke butiran tuntutan.'
        });
        return;
    }

    const btn = $(this);
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...');

    $.ajax({
        url: BASE_URL + 'new-application/store-patient',
        method: 'POST',
        data: $('#formPatientSelected').serialize(),
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success') {
                goToTab(3);
            } else {
                Swal.fire({ icon: 'error', title: 'Ralat', text: res.message || 'Gagal menyimpan maklumat pesakit.' });
            }
        },
        error: function () {
            Swal.fire({ icon: 'error', title: 'Ralat Pelayan', text: 'Sila cuba lagi sebentar lagi.' });
        },
        complete: function () {
            btn.html('Seterusnya: Butiran Tuntutan <i class="bi bi-arrow-right ms-2"></i>');
            checkTab2NextButtonState();
        }
    });
});

// ──────────────────────────────────────────────────────────────────
// Tab 3 — Claim Details Calculations & Submission (Bahagian B & C)
// ──────────────────────────────────────────────────────────────────
let tab3Totals = { gross: 0, claim: 0, welfare: 0 };

function renderTab3ClaimDetails() {
    // 1. Context Information
    const pName  = selectedVisit?.patient_name || currentPatient.name || $('#selected_patient_name').val() || '-';
    const pRn    = selectedVisit?.rn || currentPatient.rn || $('#selected_patient_rn').val() || '-';
    const pIc    = selectedVisit?.nric || currentPatient.nric || $('#selected_patient_ic').val() || '-';
    const vType  = selectedVisit?.visit?.visit_type || (patientContext?.summary ? 'OUTPATIENT' : '-');
    const invcNo = patientContext?.summary?.invc_no || '-';

    $('#tab3_patient_name').text(pName);
    $('#tab3_patient_rn').text(pRn);
    $('#tab3_patient_ic').text(pIc);
    $('#tab3_visit_type').text(vType);
    $('#tab3_invc_no').text(invcNo);
    $('#tab3_decl_name').text($('#specialist_name').val() || '<?= esc($userData['specialist_name'] ?? '') ?>');

    // 2. Procedures Detail Table (Bahagian B: 13 Columns matching official form)
    const tbody   = document.getElementById('tab3ClaimTableBody');
    const countEl = document.getElementById('tab3_proc_count');

    if (!tbody) return;

    if (!selectedProcedures || selectedProcedures.length === 0) {
        tbody.innerHTML = `<tr><td colspan="13" class="text-center text-muted py-4"><i class="bi bi-info-circle me-1"></i> Tiada maklumat prosedur dipilih. Sila kembali ke Tab 2 untuk memilih prosedur.</td></tr>`;
        if (countEl) countEl.textContent = '0';
        updateTab3KPIs(0, 0, 0);
        return;
    }

    if (countEl) countEl.textContent = selectedProcedures.length;

    let totRundKadar = 0, totRundAgihan = 0;
    let totTataKadar = 0, totTataAgihan = 0;
    let totPelaKadar = 0, totPelaAgihan = 0;
    let grossTotal   = 0;
    let claimTotal   = 0;
    let rows         = '';

    selectedProcedures.forEach((p, idx) => {
        const fee        = parseFloat(p.price) || 0;
        const cType      = p.charge_type || 'tatacara';
        const defaultPct = cType === 'pelaporan' ? 70 : 75;
        const pct        = (typeof p.claimPct !== 'undefined' && p.claimPct !== null) ? parseFloat(p.claimPct) : defaultPct;
        const claimAmt   = fee * (pct / 100);

        grossTotal += fee;
        claimTotal += claimAmt;

        let rundKadarStr = '-', rundAgihanStr = '-';
        let tataKadarStr = '-', tataAgihanStr = '-';
        let pelaKadarStr = '-', pelaAgihanStr = '-';

        if (cType === 'rundingan') {
            totRundKadar += fee;
            totRundAgihan += claimAmt;
            rundKadarStr = fee.toFixed(2);
            rundAgihanStr = claimAmt.toFixed(2);
        } else if (cType === 'pelaporan') {
            totPelaKadar += fee;
            totPelaAgihan += claimAmt;
            pelaKadarStr = fee.toFixed(2);
            pelaAgihanStr = claimAmt.toFixed(2);
        } else {
            // Default: Tatacara
            totTataKadar += fee;
            totTataAgihan += claimAmt;
            tataKadarStr = fee.toFixed(2);
            tataAgihanStr = claimAmt.toFixed(2);
        }

        const dateStr = p.bill_date ? escapeHtml(p.bill_date) : '-';
        const resitStr = p.receipt_no ? escapeHtml(p.receipt_no) : '-';

        rows += `
            <tr class="align-middle">
                <td class="text-center text-muted">${idx + 1}</td>
                <td class="fw-semibold text-dark">${escapeHtml(pName)}</td>
                <td class="text-center font-monospace">${escapeHtml(pRn)}</td>
                <td>
                    <span class="font-monospace text-primary fw-semibold">${escapeHtml(p.code)}</span> - ${escapeHtml(p.name)}
                </td>
                <td class="text-center small">${dateStr}</td>
                <td class="text-center font-monospace small">${resitStr}</td>
                <td class="text-end font-monospace ${cType === 'rundingan' ? 'bg-primary bg-opacity-10 fw-semibold' : 'text-muted'}">${rundKadarStr}</td>
                <td class="text-end font-monospace text-primary fw-bold ${cType === 'rundingan' ? 'bg-primary bg-opacity-10' : 'text-muted'}">${rundAgihanStr}</td>
                <td class="text-end font-monospace ${cType === 'tatacara' ? 'bg-info bg-opacity-10 fw-semibold' : 'text-muted'}">${tataKadarStr}</td>
                <td class="text-end font-monospace text-dark fw-bold ${cType === 'tatacara' ? 'bg-info bg-opacity-10' : 'text-muted'}">${tataAgihanStr}</td>
                <td class="text-end font-monospace ${cType === 'pelaporan' ? 'bg-secondary bg-opacity-10 fw-semibold' : 'text-muted'}">${pelaKadarStr}</td>
                <td class="text-end font-monospace text-dark fw-bold ${cType === 'pelaporan' ? 'bg-secondary bg-opacity-10' : 'text-muted'}">${pelaAgihanStr}</td>
                <td class="text-end font-monospace fw-bold text-success bg-success bg-opacity-10 fs-6">
                    ${claimAmt.toFixed(2)}
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = rows;

    const isWelfareEnabled = $('#toggleWelfareFund').is(':checked');
    const welfareTotal = isWelfareEnabled ? (grossTotal - claimTotal) : 0;
    tab3Totals = { gross: grossTotal, claim: claimTotal, welfare: welfareTotal };
    updateTab3KPIs(grossTotal, claimTotal, welfareTotal);

    // Update Footer Totals (Bahagian B Table)
    $('#tab3_total_rundingan_kadar').text(totRundKadar.toFixed(2));
    $('#tab3_total_rundingan_agihan').text(totRundAgihan.toFixed(2));
    $('#tab3_total_tatacara_kadar').text(totTataKadar.toFixed(2));
    $('#tab3_total_tatacara_agihan').text(totTataAgihan.toFixed(2));
    $('#tab3_total_pelaporan_kadar').text(totPelaKadar.toFixed(2));
    $('#tab3_total_pelaporan_agihan').text(totPelaAgihan.toFixed(2));
    $('#tab3_total_keseluruhan_tuntutan').text('RM ' + claimTotal.toFixed(2));

    checkDeclarationState();
}

// Toggle listener for Welfare Fund switch
$('#toggleWelfareFund').on('change', function () {
    renderTab3ClaimDetails();
});

function updateTab3KPIs(gross, claim, welfare) {
    const isWelfareEnabled = $('#toggleWelfareFund').is(':checked');
    $('#tab3_kpi_gross').text('RM ' + gross.toFixed(2));
    $('#tab3_kpi_claim').text('RM ' + claim.toFixed(2));
    $('#tab3_kpi_welfare').text('RM ' + welfare.toFixed(2));
    $('#tab3_kpi_welfare_status').html(isWelfareEnabled 
        ? '<span class="text-success fw-medium"><i class="bi bi-check-circle-fill me-1"></i>Diaktifkan (AI 80/20)</span>' 
        : '<span class="text-muted"><i class="bi bi-dash-circle me-1"></i>Tidak Diaktifkan</span>'
    );
}

// AI Auto Suggest & Welfare Fund Toggle (Pilihan A)
let isAISuggestionActive = false;

function toggleAISuggestion() {
    if (!selectedProcedures || selectedProcedures.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Sila pilih sekurang-kurangnya satu prosedur terlebih dahulu di Tab 2.'
        });
        return;
    }

    isAISuggestionActive = !isAISuggestionActive;

    if (isAISuggestionActive) {
        // High-value fees (> RM 500) suggest 80% claim (20% to welfare fund)
        selectedProcedures.forEach(p => {
            const fee = parseFloat(p.price) || 0;
            p.claimPct = fee > 500 ? 80 : 100;
        });
        $('#toggleWelfareFund').prop('checked', true);

        // Update button & banner styling
        $('#btnAIToggle')
            .removeClass('btn-outline-primary')
            .addClass('btn-success')
            .html('<i class="bi bi-check-circle-fill me-1"></i> AI 80/20 & Kebajikan Aktif <span class="badge bg-white text-success ms-1 small">Klik Reset</span>');

        $('#aiBannerContainer')
            .removeClass('border-primary-subtle bg-light')
            .addClass('border-success bg-success bg-opacity-10');

        Swal.fire({
            icon: 'success',
            title: 'AI 80/20 & Tabung Kebajikan Diaktifkan',
            text: 'Prosedur bernilai tinggi (>RM 500) diselaraskan kepada 80% tuntutan pakar. Baki 20% disalurkan ke Tabung Kebajikan Hospital.',
            timer: 2200,
            showConfirmButton: false
        });
    } else {
        // Reset procedures to standard form rates: Pelaporan 70%, Tatacara/Rundingan 75%
        selectedProcedures.forEach(p => {
            const cType = p.charge_type || 'tatacara';
            p.claimPct = cType === 'pelaporan' ? 70 : 75;
        });
        $('#toggleWelfareFund').prop('checked', false);

        // Reset button & banner styling
        $('#btnAIToggle')
            .removeClass('btn-success')
            .addClass('btn-outline-primary')
            .html('<i class="bi bi-stars me-1 text-warning"></i> Aktifkan AI 80/20 & Tabung Kebajikan');

        $('#aiBannerContainer')
            .removeClass('border-success bg-success bg-opacity-10')
            .addClass('border-primary-subtle bg-light');

        Swal.fire({
            icon: 'info',
            title: 'Ditetapkan Semula (Reset)',
            text: 'Semua prosedur ditetapkan semula mengikut kadar borang PE (75% / 70%).',
            timer: 2000,
            showConfirmButton: false
        });
    }

    renderSelectedProcedures();
    renderTab3ClaimDetails();
}

function runAISuggestions() {
    toggleAISuggestion();
}

// Declaration Checkbox Handler
function checkDeclarationState() {
    const isChecked = $('#declarationCheck').is(':checked');
    $('#btnSubmitClaimApp').prop('disabled', !isChecked);
}

$('#declarationCheck').on('change', function () {
    checkDeclarationState();
});

// Final Claim Submission / Update Handler
$('#btnSubmitClaimApp').on('click', function () {
    if (!selectedProcedures || selectedProcedures.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Sila pilih sekurang-kurangnya satu prosedur untuk permohonan.' });
        return;
    }

    if (!$('#declarationCheck').is(':checked')) {
        Swal.fire({ icon: 'warning', title: 'Pengesahan Diperlukan', text: 'Sila tandakan pengesahan deklarasi pakar (Bahagian C) terlebih dahulu.' });
        return;
    }

    const isWelfare = $('#toggleWelfareFund').is(':checked');

    const promptTitle = isEditMode ? 'Kemaskini Permohonan Tuntutan?' : 'Hantar Permohonan Tuntutan?';
    const promptText  = isEditMode ? 'Adakah anda pasti untuk mengemaskini maklumat tuntutan ini?' : 'Adakah anda pasti untuk menghantar tuntutan ini?';
    const confirmText = isEditMode ? '<i class="bi bi-check-circle me-1"></i> Ya, Kemaskini Sekarang' : '<i class="bi bi-check-circle me-1"></i> Ya, Hantar Sekarang';

    Swal.fire({
        title: promptTitle,
        html: `<p class="mb-2">${promptText}</p>
               <div class="text-start p-3 bg-light rounded small border">
                   <div><strong>Jumlah Kasar:</strong> RM ${tab3Totals.gross.toFixed(2)}</div>
                   <div><strong>Jumlah Bersih Tuntutan:</strong> <span class="text-success fw-bold">RM ${tab3Totals.claim.toFixed(2)}</span></div>
                   <div><strong>Tabung Kebajikan:</strong> ${isWelfare ? 'RM ' + tab3Totals.welfare.toFixed(2) : '<span class="text-muted">Tiada (Pilihan tidak aktif)</span>'}</div>
               </div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: 'Batal',
        confirmButtonColor: '#198754'
    }).then((result) => {
        if (result.isConfirmed) {
            submitFinalClaim();
        }
    });
});

function submitFinalClaim() {
    const btn = $('#btnSubmitClaimApp');
    btn.prop('disabled', true).html(isEditMode 
        ? '<span class="spinner-border spinner-border-sm me-2"></span>Mengemaskini Permohonan...' 
        : '<span class="spinner-border spinner-border-sm me-2"></span>Menghantar Permohonan...');

    const isWelfare = $('#toggleWelfareFund').is(':checked');

    const payload = {
        '<?= csrf_token() ?>': $('[name="<?= csrf_token() ?>"]').val() || '<?= csrf_hash() ?>',
        specialist_name: $('#specialist_name').val(),
        staff_ic: $('#staff_ic').val(),
        staff_number: $('#staff_number').val(),
        grade: $('#grade').val(),
        phone: $('#phone').val(),
        email: $('#email').val(),
        department: $('#department').val(),
        position: $('#position').val(),
        claim_month: $('#claim_month').val(),
        claim_year: $('#claim_year').val(),
        patient_rn: selectedVisit?.rn || currentPatient.rn || $('#selected_patient_rn').val(),
        patient_name: selectedVisit?.patient_name || currentPatient.name || $('#selected_patient_name').val(),
        patient_ic: selectedVisit?.nric || currentPatient.nric || $('#selected_patient_ic').val(),
        visit_id: selectedVisit?.visit?.visit_id || $('#selected_visit_id').val(),
        procedures: JSON.stringify(selectedProcedures),
        remarks: $('#claim_remarks').val().trim(),
        include_welfare: isWelfare ? 1 : 0,
        user_declaration: $('#declarationCheck').is(':checked') ? 1 : 0
    };

    const targetUrl = isEditMode 
        ? BASE_URL + 'new-application/update/' + editAppId 
        : BASE_URL + 'new-application/submit-claim';

    $.ajax({
        url: targetUrl,
        method: 'POST',
        data: payload,
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success') {
                const titleSuccess = isEditMode ? 'Permohonan Berjaya Dikemaskini!' : 'Permohonan Berjaya Dihantar!';
                const msgSuccess   = isEditMode ? 'Maklumat permohonan tuntutan anda telah berjaya dikemaskini.' : 'Permohonan tuntutan anda telah berjaya disimpan dan dihantar.';
                const listBtnText  = isEditMode ? '<i class="bi bi-eye me-1"></i> Lihat Permohonan' : '<i class="bi bi-list-ul me-1"></i> Senarai Permohonan';

                Swal.fire({
                    icon: 'success',
                    title: titleSuccess,
                    html: `
                        <div class="text-center mb-3">
                            <p class="mb-2">${msgSuccess}</p>
                            <div class="p-2 bg-light rounded border d-inline-block">
                                <span class="text-muted small">No. Rujukan:</span><br>
                                <strong class="fs-5 text-primary font-monospace">${res.application_no}</strong>
                            </div>
                        </div>
                        <p class="small text-muted mb-0">Adakah anda ingin mencetak borang permohonan ini sekarang?</p>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-printer-fill me-1"></i> Cetak Permohonan',
                    cancelButtonText: listBtnText,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = BASE_URL + 'new-application/show/' + res.id + '?print=1';
                    } else {
                        if (isEditMode) {
                            window.location.href = BASE_URL + 'new-application/show/' + res.id;
                        } else {
                            window.location.href = BASE_URL + 'new-application';
                        }
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Ralat',
                    text: res.message || (isEditMode ? 'Gagal mengemaskini permohonan.' : 'Gagal menghantar permohonan.')
                });
                btn.prop('disabled', false).html(isEditMode 
                    ? '<i class="bi bi-check-circle-fill me-2"></i> Kemaskini Permohonan Tuntutan' 
                    : '<i class="bi bi-send-check me-2"></i> Hantar Permohonan Tuntutan');
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Ralat Pelayan',
                text: 'Sila cuba lagi sebentar lagi.'
            });
            btn.prop('disabled', false).html(isEditMode 
                ? '<i class="bi bi-check-circle-fill me-2"></i> Kemaskini Permohonan Tuntutan' 
                : '<i class="bi bi-send-check me-2"></i> Hantar Permohonan Tuntutan');
        }
    });
}

function clearErrors() {
    document.querySelectorAll('.invalid-feedback-custom').forEach(el => {
        el.textContent = '';
        el.style.display = 'none';
    });
    document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));
}

function displayErrors(errors) {
    Object.keys(errors).forEach(function (field) {
        const errEl = document.getElementById('err_' + field);
        const input = document.getElementById(field);
        if (errEl) { errEl.textContent = errors[field]; errEl.style.display = 'block'; }
        if (input) input.classList.add('is-invalid');
    });
}
</script>

<style>
/* ── Step Indicator ─────────────────────────────── */
#stepIndicator {
    display: flex;
    align-items: flex-start;
}
.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    min-width: 80px;
    text-align: center;
}
.step-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid #dee2e6;
    background: #fff;
    color: #adb5bd;
    font-weight: 700;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all .3s;
    z-index: 1;
}
.step-label {
    font-size: 11px;
    color: #adb5bd;
    margin-top: 6px;
    line-height: 1.3;
    font-weight: 500;
    transition: color .3s;
}
.step-line {
    flex: 1;
    height: 2px;
    background: #dee2e6;
    margin-top: 18px;
    margin-bottom: auto;
    transition: background .3s;
}
/* Active */
.step-item.active .step-circle {
    border-color: var(--bs-primary);
    background: var(--bs-primary);
    color: #fff;
    box-shadow: 0 0 0 4px rgba(var(--bs-primary-rgb), .15);
}
.step-item.active .step-label { color: var(--bs-primary); }
/* Completed */
.step-item.completed .step-circle {
    border-color: #198754;
    background: #198754;
    color: #fff;
}
.step-item.completed .step-circle::before { content: '✓'; }
.step-item.completed .step-label { color: #198754; }
.step-line.completed { background: #198754; }

/* ── Tab Pane ───────────────────────────────────── */
.tab-pane-custom {
    padding: 0 0.25rem;
    animation: fadeInTab .25s ease;
}
@keyframes fadeInTab {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.tab-pane-title {
    font-size: 15px;
    padding-bottom: 14px;
    border-bottom: 1px solid #f0f0f0;
    margin-bottom: 4px;
    color: #495057;
}

/* ── Inline validation ──────────────────────────── */
.invalid-feedback-custom {
    display: none;
    color: #dc3545;
    font-size: 12px;
    margin-top: 4px;
}
.form-control.is-invalid { border-color: #dc3545 !important; }
</style>
<?= $this->endSection() ?>
