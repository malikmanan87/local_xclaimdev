<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card-panel">

    <!-- ═══════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════ -->
    <div class="card-panel-header py-3">
        <h5 class="card-panel-title">
            <i class="bi bi-file-earmark-medical-fill me-2 text-primary"></i>New Application
        </h5>
        <a href="<?= base_url('new-application') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
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

        <!-- ─── TAB 1: Specialist Identification ─── -->
        <div class="tab-pane-custom" id="tab1">
            <div class="tab-pane-title">
                <i class="bi bi-person-badge-fill text-primary me-2"></i>
                <strong>Tab 1 — Specialist Identification</strong>
            </div>

            <form id="formSpecialist" novalidate>
                <?= csrf_field() ?>

                <div class="alert alert-light border py-2 px-3 d-flex align-items-center mt-2 mb-3 text-secondary small rounded-3">
                    <i class="bi bi-shield-lock-fill text-primary me-2 fs-6"></i>
                    <div>
                        Maklumat identiti pakar / pegawai dijana secara automatik daripada akaun anda (<strong>Read Only</strong>).
                    </div>
                </div>

                <div class="row g-3">

                    <!-- Specialist Name -->
                    <div class="col-md-6">
                        <label for="specialist_name" class="form-label fw-medium">
                            Specialist Name <span class="text-danger">*</span>
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

                    <!-- Staff Number -->
                    <div class="col-md-6">
                        <label for="staff_number" class="form-label fw-medium">
                            Staff Number <span class="text-danger">*</span>
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

                    <!-- Email Address -->
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-medium">
                            Email Address <span class="text-danger">*</span>
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
                            Department
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
                            Position
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
                        Next: Search Patient
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
                                        <div class="col-sm-8 col-12">
                                            <select class="form-select form-select-sm" id="procSelect">
                                                <option value="" selected disabled>Choose Procedure...</option>
                                                <?php if (!empty($masterProcedures)): ?>
                                                    <?php foreach ($masterProcedures as $p): ?>
                                                        <option value="<?= $p['id'] ?>"
                                                            data-code="<?= esc($p['code']) ?>"
                                                            data-name="<?= esc($p['name']) ?>"
                                                            data-surgeon-fee="<?= $p['surgeon_fee'] ?>"
                                                            data-anaesthetist-fee="<?= $p['anaesthetist_fee'] ?>">
                                                            <?= esc($p['code']) ?> - <?= esc($p['name']) ?> (RM <?= number_format($p['surgeon_fee'], 2) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-4 col-12">
                                            <button type="button" class="btn btn-dark btn-sm w-100 fw-semibold" id="addProcBtn">
                                                <i class="bi bi-plus-circle me-1"></i> Add to List
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Bulk Claim % Setting Bar (Apply to All Procedures) -->
                                    <div class="d-flex flex-wrap align-items-center justify-content-between p-2 bg-light border rounded-2 mb-3">
                                        <div class="d-flex align-items-center mb-1 mb-sm-0">
                                            <span class="small fw-semibold text-secondary me-2">
                                                <i class="bi bi-sliders text-primary me-1"></i> Set All Claim %:
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <div class="input-group input-group-sm" style="width: 95px;">
                                                <input type="number" id="bulkClaimPct" class="form-control form-control-sm text-center font-monospace" min="0" max="100" value="100" placeholder="100">
                                                <span class="input-group-text px-1 small text-muted">%</span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-primary px-2" id="btnApplyAllPct" title="Set claim % for all procedures">
                                                Apply
                                            </button>
                                            <div class="btn-group btn-group-sm ms-1">
                                                <button type="button" class="btn btn-sm btn-outline-secondary px-2" onclick="setAllClaimPct(100)">100%</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary px-2" onclick="setAllClaimPct(80)">80%</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary px-2" onclick="setAllClaimPct(50)">50%</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Table: Code | Procedure Name | Price (RM) | Claim (%) | Claim (RM) | Remove -->
                                    <div class="table-responsive" style="max-height: 255px; overflow-y: auto;">
                                        <table class="table table-hover table-sm table-bordered mb-0 align-middle small">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th width="14%">Code</th>
                                                    <th>Procedure Name</th>
                                                    <th width="18%" class="text-end">Price (RM)</th>
                                                    <th width="20%" class="text-center">Claim (%)</th>
                                                    <th width="18%" class="text-end">Claim (RM)</th>
                                                    <th width="8%" class="text-center">Remove</th>
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
                                                    <th colspan="2" class="text-end">Total:</th>
                                                    <th class="text-end font-monospace" id="procTotalPrice">0.00</th>
                                                    <th class="text-center text-muted small">Total Claim:</th>
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
                <input type="hidden" id="selected_patient_rn" name="patient_rn" value="">
                <input type="hidden" id="selected_patient_name" name="patient_name" value="">
                <input type="hidden" id="selected_patient_ic" name="patient_ic" value="">
                <input type="hidden" id="selected_visit_id" name="visit_id" value="">
                <input type="hidden" id="selected_procedures_json" name="procedures" value="[]">
            </form>

            <!-- Navigation Buttons -->
            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary px-4" onclick="goToTab(1)">
                    <i class="bi bi-arrow-left me-2"></i> Kembali: Maklumat Pakar
                </button>
                <button type="button" class="btn btn-primary px-4 shadow-sm" id="btnNextTab2" disabled>
                    Seterusnya: Butiran Tuntutan <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </div>
        </div><!-- /#tab2 -->

        <!-- ─── TAB 3: Claim Details (Perincian & Jumlah Bersih Tuntutan) ─── -->
        <div class="tab-pane-custom d-none" id="tab3">
            <div class="tab-pane-title d-flex justify-content-between align-items-center mb-3">
                <div>
                    <i class="bi bi-receipt text-primary me-2"></i>
                    <strong>Tab 3 — Claim Details (Perincian & Jumlah Bersih Tuntutan)</strong>
                </div>
                <button type="button" class="btn btn-outline-info btn-sm shadow-sm" onclick="runAISuggestions()">
                    <i class="bi bi-robot me-1"></i> AI Auto-Suggest (80/20)
                </button>
            </div>

            <!-- Context Info Cards: Specialist & Patient Summary -->
            <div class="row g-3 mb-3">
                <!-- Specialist Info Card -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-3 h-100 bg-light">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="badge bg-primary p-2 me-2 rounded-circle">
                                    <i class="bi bi-person-badge text-white"></i>
                                </div>
                                <span class="fw-bold small text-uppercase text-secondary">Maklumat Pakar</span>
                            </div>
                            <div class="small">
                                <div><strong>Nama Pakar:</strong> <span id="tab3_spec_name"><?= esc($userData['specialist_name'] ?? '-') ?></span></div>
                                <div><strong>No. Staf:</strong> <span id="tab3_spec_staffno"><?= esc($userData['staff_number'] ?? '-') ?></span></div>
                                <div><strong>Jabatan:</strong> <span id="tab3_spec_dept"><?= esc($userData['department'] ?? '-') ?></span></div>
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
                                <span class="fw-bold small text-uppercase text-secondary">Maklumat Pesakit & Lawatan</span>
                            </div>
                            <div class="small">
                                <div><strong>Nama Pesakit:</strong> <span id="tab3_patient_name" class="fw-semibold text-uppercase">-</span></div>
                                <div><strong>RN:</strong> <span id="tab3_patient_rn" class="font-monospace fw-bold text-primary">-</span> | <strong>No. KP:</strong> <span id="tab3_patient_ic">-</span></div>
                                <div><strong>Jenis Lawatan:</strong> <span id="tab3_visit_type">-</span> | <strong>No. Invois:</strong> <span id="tab3_invc_no">-</span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Optional Welfare Fund Toggle Box -->
            <div class="card border-warning-subtle bg-warning bg-opacity-10 shadow-sm rounded-3 mb-3 p-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input fs-5" type="checkbox" role="switch" id="toggleWelfareFund">
                        <label class="form-check-label fw-semibold text-dark ms-2 pt-1" for="toggleWelfareFund">
                            <i class="bi bi-heart-pulse-fill text-danger me-1"></i> Sumbang Baki ke Tabung Kebajikan Hospital (Welfare Fund)
                            <span class="badge bg-secondary ms-1">Pilihan / Optional</span>
                        </label>
                    </div>
                    <div class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i> Jika tidak diaktifkan, baki potongan tidak disalurkan ke Tabung Kebajikan (RM 0.00).
                    </div>
                </div>
            </div>

            <!-- Financial Summary KPI Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 bg-secondary bg-opacity-10 text-center p-3">
                        <div class="text-muted small fw-semibold text-uppercase">Jumlah Harga Kasar (Gross)</div>
                        <div class="fs-4 fw-bold font-monospace text-dark mt-1" id="tab3_kpi_gross">RM 0.00</div>
                        <div class="small text-muted">Nilai Asal Semua Prosedur</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 bg-success bg-opacity-10 text-center p-3 border-start border-success border-4">
                        <div class="text-success small fw-semibold text-uppercase">Jumlah Bersih Tuntutan (Pakar)</div>
                        <div class="fs-4 fw-bold font-monospace text-success mt-1" id="tab3_kpi_claim">RM 0.00</div>
                        <div class="small text-success">Mengikut Peratusan Tuntutan</div>
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

            <!-- Table Detail Claim Selepas Jumlah Bersih (Ikut Percentage) -->
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-dark text-white py-2 px-3 d-flex justify-content-between align-items-center">
                    <span class="fw-semibold small">
                        <i class="bi bi-table me-2 text-warning"></i> Perincian Tuntutan Prosedur Selepas Jumlah Bersih
                    </span>
                    <span class="badge bg-primary">
                        Bil. Prosedur: <span id="tab3_proc_count">0</span>
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0 align-middle small">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th width="5%">#</th>
                                    <th width="12%">Kod Prosedur</th>
                                    <th class="text-start">Nama Prosedur</th>
                                    <th width="16%" class="text-end">Harga Asal (RM)</th>
                                    <th width="14%">Tuntutan (%)</th>
                                    <th width="18%" class="text-end text-success fw-bold">Jumlah Bersih Tuntutan (RM)</th>
                                    <th width="18%" class="text-end text-warning-emphasis">Tabung Kebajikan (Pilihan) (RM)</th>
                                </tr>
                            </thead>
                            <tbody id="tab3ClaimTableBody">
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle me-1"></i> Tiada maklumat prosedur. Sila kembali ke Tab 2 untuk memilih prosedur.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-dark text-center fw-bold" id="tab3ClaimTableFooter">
                                <tr>
                                    <td colspan="3" class="text-end text-uppercase">JUMLAH KESELURUHAN:</td>
                                    <td class="text-end font-monospace" id="tab3_footer_gross">RM 0.00</td>
                                    <td id="tab3_footer_avg_pct">-</td>
                                    <td class="text-end text-success font-monospace fs-6" id="tab3_footer_claim">RM 0.00</td>
                                    <td class="text-end text-warning font-monospace" id="tab3_footer_welfare">RM 0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Specialist Declaration & Remarks -->
            <div class="card shadow-sm border-0 rounded-3 mb-4 bg-light">
                <div class="card-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">
                            <i class="bi bi-chat-left-text me-1"></i> Catatan Tambahan (Pilihan):
                        </label>
                        <textarea class="form-control form-control-sm" id="claim_remarks" rows="2" placeholder="Masukkan sebarang nota atau rujukan tambahan jika perlu..."></textarea>
                    </div>
                    <div class="form-check p-3 bg-white border rounded-2 shadow-sm">
                        <input class="form-check-input ms-0 me-2" type="checkbox" id="declarationCheck">
                        <label class="form-check-label fw-semibold text-dark small" for="declarationCheck">
                            Saya mengesahkan bahawa segala butiran tuntutan prosedur perkhidmatan ini adalah tepat, benar, dan menepati peraturan yang ditetapkan oleh pihak Hospital Pengajar UniSZA.
                        </label>
                    </div>
                </div>
            </div>

            <!-- Navigation & Submission Buttons -->
            <div class="d-flex justify-content-between pt-3 border-top">
                <button type="button" class="btn btn-outline-secondary px-4" onclick="goToTab(2)">
                    <i class="bi bi-arrow-left me-2"></i> Kembali: Carian Pesakit & Prosedur
                </button>
                <button type="button" class="btn btn-success px-5 shadow-sm fw-semibold" id="btnSubmitClaimApp" disabled>
                    <i class="bi bi-send-check me-2"></i> Hantar Permohonan Tuntutan
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
let selectedPatient    = null;
let currentPatient     = { rn: null, name: null, nric: null };
let visitData          = { outpatient: [], inpatient: [], emergency: [] };
let selectedVisit      = null;
let patientContext     = null;
let selectedProcedures = <?= json_encode(session('new_app_procedures') ?? []) ?>;

function performPatientSearch() {
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

                // Reset visit & billing state
                selectedVisit  = null;
                patientContext = null;
                $('#billingContextWrapper').hide();

                // Populate display card
                $('#res_patient_rn').text(p.rn);
                $('#res_patient_name').text(p.name);
                $('#res_patient_ic').text(p.ic || 'N/A');

                // Populate hidden inputs
                $('#selected_patient_rn').val(p.rn);
                $('#selected_patient_name').val(p.name);
                $('#selected_patient_ic').val(p.ic || '');
                $('#selected_visit_id').val('');

                resultBox.slideDown();
                btnNext.prop('disabled', false);

                alertBox.html('<div class="alert alert-success py-2 px-3 small mb-0 rounded-2"><i class="bi bi-check-circle-fill me-1"></i> Rekod pesakit ditemui. Senarai lawatan dimuatkan di bawah.</div>').slideDown();

                // Load all visits from HRS API
                loadAllVisits(p.rn);
            } else {
                selectedPatient = null;
                resultBox.slideUp();
                btnNext.prop('disabled', true);
                alertBox.html('<div class="alert alert-danger py-2 px-3 small mb-0 rounded-2"><i class="bi bi-x-circle-fill me-1"></i> ' + (res.message || 'Rekod pesakit tidak dijumpai.') + '</div>').slideDown();
            }
        },
        error: function () {
            selectedPatient = null;
            resultBox.slideUp();
            btnNext.prop('disabled', true);
            alertBox.html('<div class="alert alert-danger py-2 px-3 small mb-0 rounded-2"><i class="bi bi-exclamation-octagon-fill me-1"></i> Gagal menghubungi pelayan API Pesakit. Sila cuba lagi.</div>').slideDown();
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
function loadAllVisits(rn) {
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

// Select a visit and load patient context & billing
function selectVisit(visit) {
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

    // Get current bulk % or default 100
    let defaultPct = parseFloat($('#bulkClaimPct').val());
    if (isNaN(defaultPct)) defaultPct = 100;
    defaultPct = Math.min(100, Math.max(0, defaultPct));

    selectedProcedures.push({
        id: procId + '_' + Date.now(),
        proc_id: procId,
        code: code,
        name: name,
        price: price,
        surgeon_fee: surgeonFee,
        anaesthetist_fee: anaesthetistFee,
        claimPct: defaultPct
    });

    renderSelectedProcedures();
    sel.selectedIndex = 0;
});

function removeProcedure(id) {
    selectedProcedures = selectedProcedures.filter(p => p.id !== id);
    renderSelectedProcedures();
}

// Set claim percentage for all procedures at once
function setAllClaimPct(pct) {
    let num = parseFloat(pct);
    if (isNaN(num)) num = 100;
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
        const pct = (typeof p.claimPct !== 'undefined' && p.claimPct !== null) ? parseFloat(p.claimPct) : 100;
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
        tbody.innerHTML = `<tr id="emptyProcRow"><td colspan="6" class="text-center text-muted py-3"><i class="bi bi-info-circle me-1"></i> No procedures added.</td></tr>`;
        if (totalCountEl) totalCountEl.textContent = '0';
        if (tfoot) tfoot.classList.add('d-none');
        updateProceduresHiddenInput();
        return;
    }

    if (totalCountEl) totalCountEl.textContent = selectedProcedures.length;
    let rows = '';

    selectedProcedures.forEach((p) => {
        const fee = parseFloat(p.price) || 0;
        const pct = (typeof p.claimPct !== 'undefined' && p.claimPct !== null) ? parseFloat(p.claimPct) : 100;
        p.claimPct = pct;
        const claimAmt = fee * (pct / 100);

        rows += `
            <tr id="proc_row_${p.id}">
                <td class="font-monospace fw-semibold text-primary">${escapeHtml(p.code)}</td>
                <td><span class="fw-semibold">${escapeHtml(p.name)}</span></td>
                <td class="text-end font-monospace">${fee.toFixed(2)}</td>
                <td class="text-center">
                    <div class="input-group input-group-sm justify-content-center mx-auto" style="max-width: 88px;">
                        <input type="number" class="form-control form-control-sm text-center px-1 font-monospace"
                               min="0" max="100" step="1"
                               value="${pct}"
                               oninput="updateProcedurePct('${p.id}', this.value)"
                               onchange="updateProcedurePct('${p.id}', this.value)">
                        <span class="input-group-text px-1 small text-muted">%</span>
                    </div>
                </td>
                <td class="text-end font-monospace fw-bold text-success" id="proc_claim_amt_${p.id}">
                    ${claimAmt.toFixed(2)}
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" onclick="removeProcedure('${p.id}')" title="Remove">
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

$(document).ready(function () {
    renderSelectedProcedures();
});

// Proceed from Tab 2 to Tab 3
$('#btnNextTab2').on('click', function () {
    if (!selectedPatient) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Sila buat carian pesakit terlebih dahulu.' });
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
            btn.prop('disabled', false).html('Seterusnya: Butiran Tuntutan <i class="bi bi-arrow-right ms-2"></i>');
        }
    });
});

// ──────────────────────────────────────────────────────────────────
// Tab 3 — Claim Details Calculations & Submission
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

    // 2. Procedures Detail Table
    const tbody   = document.getElementById('tab3ClaimTableBody');
    const countEl = document.getElementById('tab3_proc_count');

    if (!tbody) return;

    if (!selectedProcedures || selectedProcedures.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-info-circle me-1"></i> Tiada maklumat prosedur dipilih. Sila kembali ke Tab 2 untuk memilih prosedur.</td></tr>`;
        if (countEl) countEl.textContent = '0';
        updateTab3KPIs(0, 0, 0);
        return;
    }

    if (countEl) countEl.textContent = selectedProcedures.length;

    let grossTotal   = 0;
    let claimTotal   = 0;
    let welfareTotal = 0;
    let rows         = '';
    const isWelfareEnabled = $('#toggleWelfareFund').is(':checked');

    selectedProcedures.forEach((p, idx) => {
        const fee        = parseFloat(p.price) || 0;
        const pct        = (typeof p.claimPct !== 'undefined' && p.claimPct !== null) ? parseFloat(p.claimPct) : 100;
        const claimAmt   = fee * (pct / 100);
        const welfareAmt = isWelfareEnabled ? (fee - claimAmt) : 0;

        grossTotal   += fee;
        claimTotal   += claimAmt;
        welfareTotal += welfareAmt;

        rows += `
            <tr>
                <td class="text-center text-muted">${idx + 1}</td>
                <td class="text-center font-monospace fw-semibold text-primary">${escapeHtml(p.code)}</td>
                <td>
                    <div class="fw-semibold text-dark">${escapeHtml(p.name)}</div>
                </td>
                <td class="text-end font-monospace">${fee.toFixed(2)}</td>
                <td class="text-center">
                    <span class="badge ${pct === 100 ? 'bg-success' : 'bg-primary'} px-2 py-1 font-monospace">${pct}%</span>
                </td>
                <td class="text-end font-monospace fw-bold text-success">
                    RM ${claimAmt.toFixed(2)}
                </td>
                <td class="text-end font-monospace ${isWelfareEnabled ? 'text-warning-emphasis fw-semibold' : 'text-muted'}">
                    ${isWelfareEnabled ? 'RM ' + welfareAmt.toFixed(2) : '<span class="fst-italic text-muted">—</span>'}
                </td>
            </tr>
        `;
    });

    tbody.innerHTML = rows;

    tab3Totals = { gross: grossTotal, claim: claimTotal, welfare: welfareTotal };
    updateTab3KPIs(grossTotal, claimTotal, welfareTotal);

    // Update Footer
    $('#tab3_footer_gross').text('RM ' + grossTotal.toFixed(2));
    $('#tab3_footer_claim').text('RM ' + claimTotal.toFixed(2));
    $('#tab3_footer_welfare').text('RM ' + welfareTotal.toFixed(2));
    const avgPct = grossTotal > 0 ? ((claimTotal / grossTotal) * 100).toFixed(0) + '%' : '-';
    $('#tab3_footer_avg_pct').text(avgPct);
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
        ? '<span class="text-success fw-medium"><i class="bi bi-check-circle me-1"></i>Diaktifkan</span>' 
        : '<span class="text-muted"><i class="bi bi-dash-circle me-1"></i>Tidak Diaktifkan</span>'
    );
}

// AI Auto Suggest (80/20 rule)
function runAISuggestions() {
    if (!selectedProcedures || selectedProcedures.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Sila pilih sekurang-kurangnya satu prosedur terlebih dahulu.' });
        return;
    }

    selectedProcedures.forEach(p => {
        const fee = parseFloat(p.price) || 0;
        // High-value fees (> RM 500) suggest 80% claim (20% to welfare fund)
        p.claimPct = fee > 500 ? 80 : 100;
    });

    // Auto-enable welfare toggle when AI suggestion is chosen
    $('#toggleWelfareFund').prop('checked', true);

    renderSelectedProcedures();
    renderTab3ClaimDetails();

    Swal.fire({
        icon: 'info',
        title: 'AI Auto-Suggest Selesai',
        text: 'Prosedur bernilai tinggi (>RM 500) diselaraskan kepada 80% tuntutan. Sumbangan Tabung Kebajikan (20%) diaktifkan secara pilihan (boleh dinyahaktifkan bila-bila masa).'
    });
}

// Declaration Checkbox Handler
$('#declarationCheck').on('change', function () {
    $('#btnSubmitClaimApp').prop('disabled', !this.checked);
});

// Final Claim Submission Handler
$('#btnSubmitClaimApp').on('click', function () {
    if (!selectedProcedures || selectedProcedures.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Sila pilih sekurang-kurangnya satu prosedur untuk permohonan.' });
        return;
    }

    if (!$('#declarationCheck').is(':checked')) {
        Swal.fire({ icon: 'warning', title: 'Pengesahan Diperlukan', text: 'Sila tandakan pengesahan deklarasi pakar terlebih dahulu.' });
        return;
    }

    const isWelfare = $('#toggleWelfareFund').is(':checked');

    Swal.fire({
        title: 'Hantar Permohonan Tuntutan?',
        html: `<p class="mb-2">Adakah anda pasti untuk menghantar tuntutan ini?</p>
               <div class="text-start p-3 bg-light rounded small border">
                   <div><strong>Jumlah Kasar:</strong> RM ${tab3Totals.gross.toFixed(2)}</div>
                   <div><strong>Jumlah Bersih Tuntutan:</strong> <span class="text-success fw-bold">RM ${tab3Totals.claim.toFixed(2)}</span></div>
                   <div><strong>Tabung Kebajikan:</strong> ${isWelfare ? 'RM ' + tab3Totals.welfare.toFixed(2) : '<span class="text-muted">Tiada (Pilihan tidak aktif)</span>'}</div>
               </div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-check-circle me-1"></i> Ya, Hantar Sekarang',
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
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Menghantar Permohonan...');

    const isWelfare = $('#toggleWelfareFund').is(':checked');

    const payload = {
        '<?= csrf_token() ?>': $('[name="<?= csrf_token() ?>"]').val() || '<?= csrf_hash() ?>',
        patient_rn: selectedVisit?.rn || currentPatient.rn || $('#selected_patient_rn').val(),
        patient_name: selectedVisit?.patient_name || currentPatient.name || $('#selected_patient_name').val(),
        patient_ic: selectedVisit?.nric || currentPatient.nric || $('#selected_patient_ic').val(),
        visit_id: selectedVisit?.visit?.visit_id || $('#selected_visit_id').val(),
        procedures: JSON.stringify(selectedProcedures),
        remarks: $('#claim_remarks').val().trim(),
        include_welfare: isWelfare ? 1 : 0
    };

    $.ajax({
        url: BASE_URL + 'new-application/submit-claim',
        method: 'POST',
        data: payload,
        dataType: 'json',
        success: function (res) {
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berjaya Dihantar!',
                    html: `Permohonan tuntutan anda telah berjaya disimpan dan dihantar.<br><strong>No. Rujukan:</strong> <span class="badge bg-primary fs-6 font-monospace mt-2">${res.application_no}</span>`,
                    confirmButtonText: 'Lihat Senarai Permohonan',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = BASE_URL + 'new-application';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Ralat',
                    text: res.message || 'Gagal menghantar permohonan.'
                });
                btn.prop('disabled', false).html('<i class="bi bi-send-check me-2"></i> Hantar Permohonan Tuntutan');
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Ralat Pelayan',
                text: 'Sila cuba lagi sebentar lagi.'
            });
            btn.prop('disabled', false).html('<i class="bi bi-send-check me-2"></i> Hantar Permohonan Tuntutan');
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
