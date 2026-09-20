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
        $data = [
            'pageTitle'    => 'New Application',
            'breadcrumb'   => ['New Application', 'List'],
            'applications' => $this->model->getAll(),
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

        $userData = [
            'specialist_name' => session('name') ?? ($currentUser['fullname'] ?? ''),
            'staff_number'    => session('staffno') ?? ($currentUser['username'] ?? ''),
            'email'           => session('email') ?? ($currentUser['email'] ?? ''),
            'department'      => session('department') ?? '',
            'position'        => session('position') ?? '',
        ];

        if ($draft = session('new_app_specialist')) {
            $userData = array_merge($userData, array_filter($draft));
        }

        $mmaModel = new \App\Models\MmaProcedureModel();
        $masterProcedures = $mmaModel->getAllProcedures();

        $data = [
            'pageTitle'        => 'New Application',
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
            'staff_number'    => 'required|min_length[2]|max_length[50]',
            'email'           => 'required|valid_email|max_length[150]',
            'department'      => 'permit_empty|max_length[150]',
            'position'        => 'permit_empty|max_length[150]',
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
            'staff_number'    => $this->request->getPost('staff_number'),
            'email'           => $this->request->getPost('email'),
            'department'      => $this->request->getPost('department'),
            'position'        => $this->request->getPost('position'),
        ]);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Specialist information saved. Proceed to patient search.',
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
    // Store selected patient in session (Tab 2)
    // ---------------------------------------------------------------
    public function storePatient(): \CodeIgniter\HTTP\ResponseInterface
    {
        $rules = [
            'patient_rn'   => 'required|min_length[1]|max_length[50]',
            'patient_name' => 'required|min_length[2]|max_length[150]',
            'patient_ic'   => 'permit_empty|max_length[50]',
            'visit_id'     => 'permit_empty|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status'  => 'error',
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
        session()->set('new_app_procedures', $procedures ?: []);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Maklumat pesakit, lawatan, dan prosedur berjaya disimpan.',
        ]);
    }

    // ---------------------------------------------------------------
    // Show application detail
    // ---------------------------------------------------------------
    public function show(int $id): string
    {
        $application = $this->model->find($id);

        if (! $application) {
            session()->setFlashdata('error', 'Application not found.');
            return redirect()->to(base_url('new-application'));
        }

        $data = [
            'pageTitle'   => 'Application Detail',
            'breadcrumb'  => [
                ['label' => 'New Application', 'url' => base_url('new-application')],
                'Detail',
            ],
            'application' => $application,
        ];

        return view('new_application/show', $data);
    }
}
