<?php

namespace App\Controllers;

use App\Models\NewApplicationModel;

class NewApplicationController extends BaseController
{
    protected NewApplicationModel $model;

    public function __construct()
    {
        $this->model = new NewApplicationModel();
    }

    // ---------------------------------------------------------------
    // List all applications
    // ---------------------------------------------------------------
    public function index(): string
    {
        $userRole     = strtolower(session('role_name') ?? session('role') ?? 'user');
        $isPrivileged = ($userRole !== 'user');
        $userId       = $isPrivileged ? null : (int)session('user_id');

        $data = [
            'pageTitle'    => 'New Application',
            'breadcrumb'   => ['New Application', 'List'],
            'applications' => $this->model->getAll($userId),
            'isPrivileged' => $isPrivileged,
        ];

        return view('new_application/index', $data);
    }

    // ---------------------------------------------------------------
    // Show multi-tab form (create new application)
    // ---------------------------------------------------------------
    public function create(): string
    {
        $userId = session('user_id');
        $userModel = new \App\Models\UserModel();
        $currentUser = $userId ? $userModel->getUserWithRole($userId) : null;

        // Semak data pakar secara dinamik daripada API Inpersonel UniSZA (HMAC-SHA1)
        $identifier = session('email') ?? (session('staffno') ?? ($currentUser['email'] ?? ($currentUser['username'] ?? '')));
        $pakarApi   = \App\Libraries\InpersonelService::findSpecialist((string)$identifier);

        $pos = trim($pakarApi['jawatan'] ?? (session('position') ?? ''));
        $grd = trim($pakarApi['gred'] ?? (session('grade') ?? ''));

        if (!empty($pos) && !empty($grd)) {
            $posGradeDisplay = (stripos($pos, $grd) !== false) ? $pos : trim($pos . ' ' . $grd);
        } else {
            $posGradeDisplay = $pakarApi['jawatan_gred'] ?? (session('position_grade') ?? ($pos ?: $grd));
        }

        $userData = [
            'specialist_name' => $pakarApi['nama'] ?? (session('name') ?? ($currentUser['fullname'] ?? '')),
            'staff_ic'        => $pakarApi['nopengenalan'] ?? (session('icno') ?? (session('ic_no') ?? (session('ic') ?? ''))),
            'staff_number'    => $pakarApi['nostaf'] ?? (session('staffno') ?? ($currentUser['username'] ?? '')),
            'grade'           => $posGradeDisplay,
            'phone'           => $pakarApi['telpejabat'] ?? (session('phone') ?? ($currentUser['phone'] ?? '')),
            'email'           => $pakarApi['emel'] ?? (session('email') ?? ($currentUser['email'] ?? '')),
            'department'      => $pakarApi['lokasi'] ?? (session('department') ?? ''),
            'position'        => $pos,
            'claim_month'     => date('m'),
            'claim_year'      => date('Y'),
        ];

        if ($draft = session('new_app_specialist')) {
            $userData = array_merge($userData, array_filter($draft));
        }

        $mmaModel = new \App\Models\MmaProcedureModel();
        $masterProcedures = $mmaModel->getAllProcedures();

        $data = [
            'pageTitle'        => 'New Application (Borang HoSZA-MGT-J3P (PE)-F-003-01)',
            'breadcrumb'       => [
                ['label' => 'New Application', 'url' => base_url('new-application')],
                'Create',
            ],
            'userData'         => $userData,
            'masterProcedures' => $masterProcedures,
        ];

        return view('new_application/create', $data);
    }

    // ---------------------------------------------------------------
    // Store specialist identification (Tab 1) — via AJAX / session
    // ---------------------------------------------------------------
    public function storeSpecialist(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rules = [
            'specialist_name' => 'required|min_length[2]|max_length[150]',
            'staff_ic'        => 'permit_empty|max_length[25]',
            'staff_number'    => 'required|min_length[2]|max_length[50]',
            'grade'           => 'permit_empty|max_length[150]',
            'phone'           => 'permit_empty|max_length[30]',
            'email'           => 'required|valid_email|max_length[150]',
            'department'      => 'permit_empty|max_length[150]',
            'position'        => 'permit_empty|max_length[150]',
            'claim_month'     => 'permit_empty|max_length[20]',
            'claim_year'      => 'permit_empty|max_length[10]',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        // Simpan dalam session untuk digunakan pada tab seterusnya
        session()->set('new_app_specialist', [
            'specialist_name' => $this->request->getPost('specialist_name'),
            'staff_ic'        => $this->request->getPost('staff_ic'),
            'staff_number'    => $this->request->getPost('staff_number'),
            'grade'           => $this->request->getPost('grade'),
            'phone'           => $this->request->getPost('phone'),
            'email'           => $this->request->getPost('email'),
            'department'      => $this->request->getPost('department'),
            'position'        => $this->request->getPost('position'),
            'claim_month'     => $this->request->getPost('claim_month') ?: date('m'),
            'claim_year'      => $this->request->getPost('claim_year') ?: date('Y'),
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Maklumat Bahagian A berjaya disimpan. Sila teruskan ke carian pesakit & prosedur.',
        ]);
    }

    // ---------------------------------------------------------------
    // Search for Patient via External ASP API
    // Uses Bearer Token from .env file
    // ---------------------------------------------------------------
    public function searchPatient(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rn = $this->request->getPost('rn');
        if (!$rn) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'RN is required.']);
        }

        $client = \Config\Services::curlrequest();
        $apiToken = env('API_TOKEN');

        try {
            $response = $client->get("http://10.0.20.64:8081/api_patient_rn_testing.asp", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Accept'        => 'application/json',
                ],
                'query'   => ['rn' => $rn],
                'timeout' => 15,
                'proxy'   => ''
            ]);

            $result = json_decode($response->getBody(), true);

            // Standardizing the response for your new_application.php JS
            if (isset($result['status']) && $result['status'] === 'success' && isset($result['data'])) {
                $p = $result['data'];
                return $this->response->setJSON([
                    'status' => 'success',
                    'patient' => [
                        'rn'   => $p['RNmajor'] ?? $rn,
                        'name' => strtoupper($p['namapesakit'] ?? 'Unknown'),
                        'ic'   => $p['mykad_mykid'] ?? 'N/A'
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error', // Changed from 'not_found' to match your JS 'if'
                    'message' => $result['message'] ?? 'Patient record not found.'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Internal API Error: ' . $e->getMessage()
            ]);
        }
    }

    // ---------------------------------------------------------------
    // Get all visits (Outpatient, Inpatient, Emergency) via HRS API
    // ---------------------------------------------------------------
    public function getVisitsAll(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rn = $this->request->getGet('rn');

        if (!$rn) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'RN is required'
            ]);
        }

        $client = \Config\Services::curlrequest();
        $apiToken = env('API_TOKEN');

        try {

            $response = $client->get("http://10.0.20.105/hrs/api/patient-visit", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Accept'        => 'application/json',
                ],
                'query' => [
                    'rn'   => $rn,
                    'mode' => 'ALL'
                ],
                'timeout' => 20
            ]);

            $result = json_decode($response->getBody(), true);

            if (!isset($result['status']) || $result['status'] !== 'success') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $result['message'] ?? 'No visit data'
                ]);
            }

            $data = $result['data'] ?? [];

            // get all visit
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'outpatient' => $data['outpatient'] ?? [],
                    'inpatient'  => $data['inpatient'] ?? [],
                    'emergency'  => $data['emergency'] ?? []
                ]
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'API Error: ' . $e->getMessage()
            ]);
        }
    }

    // ---------------------------------------------------------------
    // Get patient billing & items context via HRS API
    // ---------------------------------------------------------------
    public function getPatientContext(): \CodeIgniter\HTTP\ResponseInterface
    {
        $visitId = $this->request->getGet('visit_id');

        if (empty($visitId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Visit ID is required'
            ]);
        }

        $client = \Config\Services::curlrequest();
        $apiToken = env('API_TOKEN');

        try {

            $response = $client->get(
                'http://10.0.20.105/hrs/api/patient-bill',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiToken,
                        'Accept'        => 'application/json',
                    ],
                    'query' => [
                        'visit_id' => $visitId,
                        'include'  => 'items'
                    ],
                    'http_errors' => false,
                    'timeout' => 30
                ]
            );

            $body = json_decode($response->getBody(), true);

            return $this->response->setJSON($body);

        } catch (\Throwable $e) {

            log_message('error', $e->getMessage());

            return $this->response->setStatusCode(500)
                ->setJSON([
                    'status' => 'error',
                    'message' => 'Unable to connect HRS API'
                ]);

        }
    }

    // ---------------------------------------------------------------
    // Semak sama ada permohonan tuntutan pernah wujud bagi visit_id ini
    // ---------------------------------------------------------------
    public function checkVisitClaim(): \CodeIgniter\HTTP\ResponseInterface
    {
        $visitId = $this->request->getGet('visit_id') ?? $this->request->getPost('visit_id');
        $excludeId = $this->request->getGet('exclude_id') ?? $this->request->getPost('exclude_id');

        if (empty($visitId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Visit ID diperlukan.']);
        }

        $query = $this->model
            ->select('id, application_no, specialist_name, staff_number, department, total_gross, total_claim, status, created_at')
            ->where('visit_id', $visitId)
            ->whereIn('status', ['submitted', 'under_review', 'approved']);

        if (!empty($excludeId)) {
            $query->where('id !=', $excludeId);
        }

        $existingClaims = $query->orderBy('created_at', 'DESC')->findAll();

        if (!empty($existingClaims)) {
            return $this->response->setJSON([
                'status' => 'exists',
                'count'  => count($existingClaims),
                'claims' => $existingClaims
            ]);
        }

        return $this->response->setJSON([
            'status' => 'none',
            'count'  => 0,
            'claims' => []
        ]);
    }

    // ---------------------------------------------------------------
    // Store selected patient in session (Tab 2)
    // ---------------------------------------------------------------
    public function storePatient(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rules = [
            'patient_rn'   => 'required|min_length[1]|max_length[50]',
            'patient_name' => 'required|min_length[2]|max_length[150]',
            'patient_ic'   => 'permit_empty|max_length[50]',
            'visit_id'     => 'required|min_length[1]|max_length[50]',
        ];

        $messages = [
            'visit_id' => [
                'required' => 'Sila pilih salah satu episod lawatan pesakit terlebih dahulu.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => $this->validator->getError('visit_id') ?: 'Sila lengkapkan maklumat pesakit dan pilih episod lawatan.',
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        session()->set('new_app_patient', [
            'patient_rn'   => $this->request->getPost('patient_rn'),
            'patient_name' => $this->request->getPost('patient_name'),
            'patient_ic'   => $this->request->getPost('patient_ic'),
            'visit_id'     => $this->request->getPost('visit_id'),
        ]);

        $proceduresJson = $this->request->getPost('procedures');
        $procedures = [];
        if (!empty($proceduresJson)) {
            $procedures = is_string($proceduresJson) ? json_decode($proceduresJson, true) : $proceduresJson;
        }

        if (empty($procedures) || !is_array($procedures)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Sila pilih dan tambah sekurang-kurangnya satu prosedur yang dituntut.',
            ]);
        }

        session()->set('new_app_procedures', $procedures);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Maklumat pesakit, lawatan, dan prosedur berjaya disimpan.',
        ]);
    }

    // ---------------------------------------------------------------
    // Show application detail
    // ---------------------------------------------------------------
    public function show(int $id)
    {
        $application = $this->model->getWithReviewers($id);

        if (! $application) {
            session()->setFlashdata('error', 'Permohonan tidak dijumpai.');
            return redirect()->to(base_url('new-application'));
        }

        // Kawalan Akses: Pakar (role: user) hanya dibenarkan melihat permohonannya sendiri
        $userRole     = strtolower(session('role_name') ?? session('role') ?? 'user');
        $isPrivileged = ($userRole !== 'user');
        $currentUserId = (int)session('user_id');

        if (!$isPrivileged && (int)$application['submitted_by'] !== $currentUserId) {
            session()->setFlashdata('error', 'Akses tidak dibenarkan. Anda hanya boleh melihat permohonan tuntutan anda sendiri.');
            return redirect()->to(base_url('new-application'));
        }

        $data = [
            'pageTitle'   => 'Borang Tuntutan HoSZA-MGT-J3P (PE)-F-003-01: ' . $application['application_no'],
            'breadcrumb'  => [
                ['label' => 'New Application', 'url' => base_url('new-application')],
                $application['application_no'],
            ],
            'application' => $application,
        ];

        return view('new_application/show', $data);
    }

    // ---------------------------------------------------------------
    // Edit application (Only allowed if status is 'submitted' and unchanged)
    // ---------------------------------------------------------------
    public function edit(int $id)
    {
        $application = $this->model->find($id);

        if (! $application) {
            session()->setFlashdata('error', 'Permohonan tidak dijumpai.');
            return redirect()->to(base_url('new-application'));
        }

        // Semak status: hanya 'submitted' dibenarkan selagi status tidak berubah oleh mana-mana pegawai penyemak/kelulusan
        $penyemakStatus     = $application['penyemak_status'] ?? 'pending';
        $perkhidmatanStatus = $application['perkhidmatan_status'] ?? 'pending';
        $j3pStatus          = $application['j3p_status'] ?? 'pending';
        $pengarahStatus     = $application['pengarah_status'] ?? 'pending';
        $jpppStatus         = $application['jppp_status'] ?? 'pending';
        $financeStatus      = $application['finance_status'] ?? 'pending';

        if ($application['status'] !== 'submitted' || 
            $penyemakStatus !== 'pending' || 
            $perkhidmatanStatus !== 'pending' || 
            $j3pStatus !== 'pending' || 
            $pengarahStatus !== 'pending' || 
            $jpppStatus !== 'pending' || 
            $financeStatus !== 'pending') {
            session()->setFlashdata('error', 'Permohonan ini tidak boleh diedit kerana status telah berubah (' . ucfirst($application['status']) . ').');
            return redirect()->to(base_url('new-application/show/' . $id));
        }

        // Semak kebenaran: hanya pemohon atau admin/manager/pegawai PE/J3P/Pengarah dibenarkan edit
        $userId   = session('user_id');
        $userRole = session('role_name') ?? session('role') ?? '';
        if ($application['submitted_by'] != $userId && !in_array($userRole, ['admin', 'manager', 'pegawai_penyemak_pe', 'pegawai_perkhidmatan_pe', 'ketua_j3p', 'pengarah'])) {
            session()->setFlashdata('error', 'Anda tidak mempunyai kebenaran untuk mengemaskini permohonan ini.');
            return redirect()->to(base_url('new-application/show/' . $id));
        }

        $userData = [
            'specialist_name' => $application['specialist_name'],
            'staff_ic'        => $application['staff_ic'] ?? '',
            'staff_number'    => $application['staff_number'],
            'grade'           => $application['grade'] ?? '',
            'phone'           => $application['phone'] ?? '',
            'email'           => $application['email'],
            'department'      => $application['department'],
            'position'        => $application['position'],
            'claim_month'     => $application['claim_month'] ?? date('m'),
            'claim_year'      => $application['claim_year'] ?? date('Y'),
        ];

        $mmaModel = new \App\Models\MmaProcedureModel();
        $masterProcedures = $mmaModel->getAllProcedures();

        $data = [
            'pageTitle'        => 'Kemaskini Borang Tuntutan: ' . $application['application_no'],
            'breadcrumb'       => [
                ['label' => 'New Application', 'url' => base_url('new-application')],
                ['label' => $application['application_no'], 'url' => base_url('new-application/show/' . $id)],
                'Kemaskini',
            ],
            'userData'         => $userData,
            'masterProcedures' => $masterProcedures,
            'application'      => $application,
            'isEdit'           => true,
        ];

        return view('new_application/create', $data);
    }

    // ---------------------------------------------------------------
    // Update submitted claim application
    // ---------------------------------------------------------------
    public function update(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $application = $this->model->find($id);

        if (! $application) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Permohonan tidak dijumpai.',
            ]);
        }

        // Semak status: hanya 'submitted' dibenarkan selagi status tidak berubah oleh mana-mana pegawai penyemak/kelulusan
        $penyemakStatus     = $application['penyemak_status'] ?? 'pending';
        $perkhidmatanStatus = $application['perkhidmatan_status'] ?? 'pending';
        $j3pStatus          = $application['j3p_status'] ?? 'pending';
        $pengarahStatus     = $application['pengarah_status'] ?? 'pending';
        $jpppStatus         = $application['jppp_status'] ?? 'pending';
        $financeStatus      = $application['finance_status'] ?? 'pending';

        if ($application['status'] !== 'submitted' || 
            $penyemakStatus !== 'pending' || 
            $perkhidmatanStatus !== 'pending' || 
            $j3pStatus !== 'pending' || 
            $pengarahStatus !== 'pending' || 
            $jpppStatus !== 'pending' || 
            $financeStatus !== 'pending') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Permohonan ini tidak boleh dikemaskini kerana status telah berubah (' . ucfirst($application['status']) . ').',
            ]);
        }

        // Semak kebenaran: role 'user' hanya boleh kemaskini rekod sendiri
        $userId   = session('user_id');
        $userRole = strtolower(session('role_name') ?? session('role') ?? 'user');
        if ($application['submitted_by'] != $userId && $userRole === 'user') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak mempunyai kebenaran untuk mengemaskini permohonan ini.',
            ]);
        }

        // Ambil data prosedur
        $postProcedures = $this->request->getPost('procedures');
        $procedures = [];
        if (!empty($postProcedures)) {
            $procedures = is_string($postProcedures) ? (json_decode($postProcedures, true) ?: []) : $postProcedures;
        }

        $patientRn   = $this->request->getPost('patient_rn') ?: $application['patient_rn'];
        $patientName = $this->request->getPost('patient_name') ?: $application['patient_name'];
        $patientIc   = $this->request->getPost('patient_ic') ?: $application['patient_ic'];
        $visitId     = $this->request->getPost('visit_id') ?: $application['visit_id'];
        $remarks     = $this->request->getPost('remarks') !== null ? trim($this->request->getPost('remarks')) : $application['remarks'];

        if (empty($patientRn) || empty($patientName)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Maklumat pesakit tidak lengkap.',
            ]);
        }

        if (empty($visitId)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Sila pilih salah satu episod lawatan pesakit.',
            ]);
        }

        if (empty($procedures) || !is_array($procedures)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Sila pilih dan tambah sekurang-kurangnya satu prosedur yang dituntut.',
            ]);
        }

        // Specialist info jika diubah
        $specialistName = $this->request->getPost('specialist_name') ?: $application['specialist_name'];
        $staffIc        = $this->request->getPost('staff_ic') ?: ($application['staff_ic'] ?? null);
        $staffNumber    = $this->request->getPost('staff_number') ?: $application['staff_number'];
        $grade          = $this->request->getPost('grade') ?: ($application['grade'] ?? null);
        $phone          = $this->request->getPost('phone') ?: ($application['phone'] ?? null);
        $email          = $this->request->getPost('email') ?: $application['email'];
        $department     = $this->request->getPost('department') ?: $application['department'];
        $position       = $this->request->getPost('position') ?: $application['position'];
        $claimMonth     = $this->request->getPost('claim_month') ?: ($application['claim_month'] ?? date('m'));
        $claimYear      = $this->request->getPost('claim_year') ?: ($application['claim_year'] ?? date('Y'));

        // Kira semula jumlah kewangan
        $totalGross     = 0;
        $totalClaim     = 0;
        $totalWelfare   = 0;
        $includeWelfare = (bool) $this->request->getPost('include_welfare');

        foreach ($procedures as $p) {
            $price      = (float) ($p['price'] ?? 0);
            $pct        = isset($p['claimPct']) ? (float) $p['claimPct'] : 100;
            $claimAmt   = $price * ($pct / 100);
            $welfareAmt = $includeWelfare ? ($price - $claimAmt) : 0.00;

            $totalGross   += $price;
            $totalClaim   += $claimAmt;
            $totalWelfare += $welfareAmt;
        }

        $updateData = [
            'specialist_name' => $specialistName,
            'staff_ic'        => $staffIc,
            'staff_number'    => $staffNumber,
            'grade'           => $grade,
            'phone'           => $phone,
            'email'           => $email,
            'department'      => $department,
            'position'        => $position,
            'claim_month'     => $claimMonth,
            'claim_year'      => $claimYear,
            'patient_rn'      => $patientRn,
            'patient_name'    => $patientName,
            'patient_ic'      => $patientIc,
            'visit_id'        => $visitId,
            'total_gross'     => $totalGross,
            'total_claim'     => $totalClaim,
            'total_welfare'   => $totalWelfare,
            'procedures_data' => json_encode($procedures),
            'remarks'         => $remarks,
        ];

        try {
            $this->model->update($id, $updateData);

            // Log activity
            try {
                $db = \Config\Database::connect();
                $db->table('activity_logs')->insert([
                    'user_id'     => $userId,
                    'username'    => session('name') ?? $specialistName,
                    'action'      => 'Kemaskini Tuntutan',
                    'description' => "Permohonan tuntutan {$application['application_no']} dikemaskini. Jumlah bersih: RM " . number_format($totalClaim, 2),
                    'ip_address'  => $this->request->getIPAddress(),
                    'user_agent'  => $this->request->getUserAgent()->getAgentString(),
                    'created_at'  => date('Y-m-d H:i:s'),
                ]);
            } catch (\Throwable $logEx) {
                log_message('warning', 'Gagal merekod log aktiviti: ' . $logEx->getMessage());
            }

            return $this->response->setJSON([
                'status'         => 'success',
                'message'        => 'Permohonan tuntutan berjaya dikemaskini!',
                'application_no' => $application['application_no'],
                'id'             => $id,
            ]);
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Ralat sistem semasa mengemaskini permohonan: ' . $e->getMessage(),
            ]);
        }
    }

    // ---------------------------------------------------------------
    // Submit complete claim application (Tab 3)
    // ---------------------------------------------------------------
    public function submitClaim(): \CodeIgniter\HTTP\ResponseInterface
    {
        $specialist = session('new_app_specialist') ?? [];
        $patient    = session('new_app_patient') ?? [];
        $procedures = session('new_app_procedures') ?? [];

        // Check if procedures or patient passed in POST directly
        $postProcedures = $this->request->getPost('procedures');
        if (!empty($postProcedures)) {
            $procedures = is_string($postProcedures) ? (json_decode($postProcedures, true) ?: []) : $postProcedures;
        }

        $patientRn   = $this->request->getPost('patient_rn') ?: ($patient['patient_rn'] ?? null);
        $patientName = $this->request->getPost('patient_name') ?: ($patient['patient_name'] ?? null);
        $patientIc   = $this->request->getPost('patient_ic') ?: ($patient['patient_ic'] ?? null);
        $visitId     = $this->request->getPost('visit_id') ?: ($patient['visit_id'] ?? null);
        $remarks     = $this->request->getPost('remarks') ?: '';

        if (empty($patientRn) || empty($patientName)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Maklumat pesakit tidak lengkap. Sila kembali ke Tab 2.',
            ]);
        }

        if (empty($visitId)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Episod lawatan pesakit tidak dipilih. Sila kembali ke Tab 2 dan pilih salah satu lawatan pesakit.',
            ]);
        }

        if (empty($procedures)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Tiada prosedur dipilih. Sila pilih sekurang-kurangnya satu prosedur.',
            ]);
        }

        // Calculate totals
        $totalGross     = 0;
        $totalClaim     = 0;
        $totalWelfare   = 0;
        $includeWelfare = (bool) $this->request->getPost('include_welfare');

        foreach ($procedures as $p) {
            $price      = (float) ($p['price'] ?? 0);
            $pct        = isset($p['claimPct']) ? (float) $p['claimPct'] : 100;
            $claimAmt   = $price * ($pct / 100);
            $welfareAmt = $includeWelfare ? ($price - $claimAmt) : 0.00;

            $totalGross   += $price;
            $totalClaim   += $claimAmt;
            $totalWelfare += $welfareAmt;
        }

        $userId = session('user_id');
        $userModel = new \App\Models\UserModel();
        $currentUser = $userId ? $userModel->getUserWithRole($userId) : null;

        $specialistName = $this->request->getPost('specialist_name') ?: ($specialist['specialist_name'] ?? session('name') ?? ($currentUser['fullname'] ?? 'Specialist'));
        $staffIc        = $this->request->getPost('staff_ic') ?: ($specialist['staff_ic'] ?? null);
        $staffNumber    = $this->request->getPost('staff_number') ?: ($specialist['staff_number'] ?? session('staffno') ?? ($currentUser['username'] ?? ''));
        $grade          = $this->request->getPost('grade') ?: ($specialist['grade'] ?? null);
        $phone          = $this->request->getPost('phone') ?: ($specialist['phone'] ?? ($currentUser['phone'] ?? null));
        $email          = $this->request->getPost('email') ?: ($specialist['email'] ?? session('email') ?? ($currentUser['email'] ?? ''));
        $department     = $this->request->getPost('department') ?: ($specialist['department'] ?? session('department') ?? '');
        $position       = $this->request->getPost('position') ?: ($specialist['position'] ?? session('position') ?? '');
        $claimMonth     = $this->request->getPost('claim_month') ?: ($specialist['claim_month'] ?? date('m'));
        $claimYear      = $this->request->getPost('claim_year') ?: ($specialist['claim_year'] ?? date('Y'));

        $applicationNo = $this->model->generateAppNo();

        $saveData = [
            'application_no'      => $applicationNo,
            'specialist_name'     => $specialistName,
            'staff_ic'            => $staffIc,
            'staff_number'        => $staffNumber,
            'grade'               => $grade,
            'phone'               => $phone,
            'email'               => $email,
            'department'          => $department,
            'position'            => $position,
            'claim_month'         => $claimMonth,
            'claim_year'          => $claimYear,
            'patient_rn'          => $patientRn,
            'patient_name'        => $patientName,
            'patient_ic'          => $patientIc,
            'visit_id'            => $visitId,
            'status'              => 'submitted',
            'penyemak_status'     => 'pending',
            'perkhidmatan_status' => 'pending',
            'j3p_status'          => 'pending',
            'pengarah_status'     => 'pending',
            'jppp_status'         => 'pending',
            'finance_status'      => 'pending',
            'total_gross'         => $totalGross,
            'total_claim'         => $totalClaim,
            'total_welfare'       => $totalWelfare,
            'procedures_data'     => json_encode($procedures),
            'remarks'             => $remarks,
            'user_declaration'    => 1,
            'user_declared_at'    => date('Y-m-d H:i:s'),
            'submitted_by'        => $userId,
            'submitted_at'        => date('Y-m-d H:i:s'),
        ];

        try {
            $insertedId = $this->model->insert($saveData);
            if (!$insertedId) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal menyimpan permohonan ke dalam pangkalan data.',
                ]);
            }

            // Clear temporary draft session
            session()->remove(['new_app_specialist', 'new_app_patient', 'new_app_procedures']);

            // Catat log aktiviti ke pangkalan data
            try {
                $db = \Config\Database::connect();
                $db->table('activity_logs')->insert([
                    'user_id'     => $userId,
                    'username'    => session('name') ?? $specialistName,
                    'action'      => 'Hantar Tuntutan',
                    'description' => "Permohonan tuntutan {$applicationNo} dihantar untuk pesakit {$patientName} (RN: {$patientRn}) berjumlah RM " . number_format($totalClaim, 2),
                    'ip_address'  => $this->request->getIPAddress(),
                    'user_agent'  => $this->request->getUserAgent()->getAgentString(),
                    'created_at'  => date('Y-m-d H:i:s'),
                ]);
            } catch (\Throwable $logEx) {
                // Jangan sekat permohonan jika log gagal
                log_message('warning', 'Gagal merekod log aktiviti: ' . $logEx->getMessage());
            }

            return $this->response->setJSON([
                'status'         => 'success',
                'message'        => 'Permohonan tuntutan berjaya dihantar!',
                'application_no' => $applicationNo,
                'id'             => $insertedId,
            ]);
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Ralat sistem semasa memproses permohonan: ' . $e->getMessage(),
            ]);
        }
    }
}
