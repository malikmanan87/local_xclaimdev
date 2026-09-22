<?php

namespace App\Controllers;

use App\Models\NewApplicationModel;

class ReviewJpppController extends BaseController
{
    protected NewApplicationModel $model;

    public function __construct()
    {
        $this->model = new NewApplicationModel();
    }

    /**
     * Semak hak akses peranan pegawai penyemak & kelulusan
     */
    protected function checkAccess()
    {
        $role = strtolower(session('role_name') ?? session('role') ?? 'user');
        if (!in_array($role, ['admin', 'manager', 'jppp', 'pegawai_penyemak_pe', 'pegawai_perkhidmatan_pe', 'ketua_j3p', 'pengarah'])) {
            return redirect()->to('dashboard')->with('error', 'Akses tidak dibenarkan. Modul ini khusus untuk pegawai semakan & kelulusan.');
        }
        return null;
    }

    /**
     * Tentukan peringkat semasa permohonan mengikut hirarki rasmi 5-peringkat:
     * 1. Dihantar oleh Pakar (submitted)
     * 2. Pegawai Menyemak PE (penyemak)
     * 3. Pegawai Perkhidmatan PE (perkhidmatan)
     * 4. Ketua J3P (j3p)
     * 5. Pengarah / KPTj (pengarah)
     */
    public static function getApplicationStage(array $app): array
    {
        $penyemak     = $app['penyemak_status'] ?? 'pending';
        $perkhidmatan = $app['perkhidmatan_status'] ?? 'pending';
        $j3p          = $app['j3p_status'] ?? $app['jppp_status'] ?? 'pending';
        $pengarah     = $app['pengarah_status'] ?? 'pending';
        $overall      = $app['status'] ?? 'draft';

        // Jika mana-mana pegawai menolak atau status permohonan ditolak
        if ($overall === 'rejected' || in_array('rejected', [$penyemak, $perkhidmatan, $j3p, $pengarah])) {
            $rejectedBy = 'Pegawai';
            if ($penyemak === 'rejected') $rejectedBy = 'Pegawai Menyemak PE';
            elseif ($perkhidmatan === 'rejected') $rejectedBy = 'Pegawai Perkhidmatan PE';
            elseif ($j3p === 'rejected') $rejectedBy = 'Ketua J3P';
            elseif ($pengarah === 'rejected') $rejectedBy = 'Pengarah Hospital';

            return [
                'stage'        => 'rejected',
                'label'        => 'Ditolak (' . $rejectedBy . ')',
                'badgeClass'   => 'badge-status-danger',
                'requiredRole' => [],
                'officerTitle' => $rejectedBy,
                'nextOfficer'  => null,
                'description'  => 'Permohonan ditolak oleh ' . $rejectedBy,
            ];
        }

        // Jika telah diluluskan penuh oleh Pengarah
        if ($overall === 'approved' || in_array($pengarah, ['approved', 'verified'])) {
            return [
                'stage'        => 'approved',
                'label'        => 'Lulus Penuh (Pengarah)',
                'badgeClass'   => 'badge-status-success',
                'requiredRole' => [],
                'officerTitle' => 'Pengarah Hospital / KPTj',
                'nextOfficer'  => null,
                'description'  => 'Permohonan telah diluluskan penuh oleh Pengarah Hospital / KPTj',
            ];
        }

        // Peringkat 2: Pegawai Menyemak PE
        if ($penyemak === 'pending' || empty($penyemak)) {
            return [
                'stage'        => 'penyemak',
                'stepNumber'   => 2,
                'label'        => 'Pegawai Menyemak PE',
                'badgeClass'   => 'badge-status-warning',
                'requiredRole' => ['admin', 'pegawai_penyemak_pe'],
                'officerTitle' => 'Pegawai Menyemak PE',
                'nextOfficer'  => 'Pegawai Perkhidmatan PE',
                'description'  => 'Menunggu semakan oleh Pegawai Menyemak PE',
            ];
        }

        // Peringkat 3: Pegawai Perkhidmatan PE
        if ($perkhidmatan === 'pending' || empty($perkhidmatan)) {
            return [
                'stage'        => 'perkhidmatan',
                'stepNumber'   => 3,
                'label'        => 'Pegawai Perkhidmatan PE',
                'badgeClass'   => 'badge-status-info',
                'requiredRole' => ['admin', 'pegawai_perkhidmatan_pe'],
                'officerTitle' => 'Pegawai Perkhidmatan PE',
                'nextOfficer'  => 'Ketua J3P',
                'description'  => 'Disemak PE & Menunggu pengesahan Pegawai Perkhidmatan PE',
            ];
        }

        // Peringkat 4: Ketua J3P
        if ($j3p === 'pending' || empty($j3p)) {
            return [
                'stage'        => 'j3p',
                'stepNumber'   => 4,
                'label'        => 'Ketua J3P',
                'badgeClass'   => 'badge-status-primary',
                'requiredRole' => ['admin', 'ketua_j3p', 'jppp'],
                'officerTitle' => 'Ketua J3P',
                'nextOfficer'  => 'Pengarah Hospital / KPTj',
                'description'  => 'Disahkan PE & Menunggu perakuan Ketua J3P',
            ];
        }

        // Peringkat 5: Pengarah / KPTj
        if ($pengarah === 'pending' || empty($pengarah)) {
            return [
                'stage'        => 'pengarah',
                'stepNumber'   => 5,
                'label'        => 'Pengarah Hospital / KPTj',
                'badgeClass'   => 'badge-status-warning',
                'requiredRole' => ['admin', 'pengarah'],
                'officerTitle' => 'Pengarah Hospital / KPTj',
                'nextOfficer'  => 'Selesai (Lulus Penuh)',
                'description'  => 'Disokong Ketua J3P & Menunggu kelulusan akhir Pengarah Hospital',
            ];
        }

        return [
            'stage'        => 'approved',
            'label'        => 'Lulus Penuh',
            'badgeClass'   => 'badge-status-success',
            'requiredRole' => [],
            'officerTitle' => 'Pengarah Hospital',
            'nextOfficer'  => null,
            'description'  => 'Permohonan telah selesai diproses',
        ];
    }

    /**
     * Senarai Permohonan untuk Semakan JPPP & Aliran Hirarki
     */
    public function index()
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $userRole = strtolower(session('role_name') ?? session('role') ?? 'user');

        // Mengambil semua permohonan yang telah dihantar oleh pemohon
        $allApplications = $this->model->select('new_applications.*, 
                                                users.fullname AS creator_name,
                                                u_penyemak.fullname AS penyemak_reviewer_name,
                                                u_perkhidmatan.fullname AS perkhidmatan_reviewer_name,
                                                u_j3p.fullname AS j3p_reviewer_name,
                                                u_pengarah.fullname AS pengarah_reviewer_name')
            ->join('users', 'users.id = new_applications.submitted_by', 'left')
            ->join('users u_penyemak', 'u_penyemak.id = new_applications.penyemak_verified_by', 'left')
            ->join('users u_perkhidmatan', 'u_perkhidmatan.id = new_applications.perkhidmatan_verified_by', 'left')
            ->join('users u_j3p', 'u_j3p.id = new_applications.j3p_verified_by', 'left')
            ->join('users u_pengarah', 'u_pengarah.id = new_applications.pengarah_verified_by', 'left')
            ->whereIn('new_applications.status', ['submitted', 'under_review', 'approved', 'rejected'])
            ->orderBy('new_applications.submitted_at', 'DESC')
            ->findAll();

        $pendingList   = [];
        $processedList = [];

        foreach ($allApplications as $app) {
            $stageInfo = self::getApplicationStage($app);
            $app['stageInfo'] = $stageInfo;
            $app['isMyTurn']  = in_array($userRole, $stageInfo['requiredRole'] ?? []);

            if (in_array($stageInfo['stage'], ['approved', 'rejected'])) {
                $processedList[] = $app;
            } else {
                $pendingList[] = $app;
            }
        }

        $stats = [
            'pending'   => count($pendingList),
            'approved'  => count(array_filter($processedList, fn($x) => ($x['stageInfo']['stage'] ?? '') === 'approved')),
            'rejected'  => count(array_filter($processedList, fn($x) => ($x['stageInfo']['stage'] ?? '') === 'rejected')),
            'myTurn'    => count(array_filter($pendingList, fn($x) => !empty($x['isMyTurn']))),
            'total'     => count($allApplications),
        ];

        return view('review_jppp/index', [
            'pageTitle'     => 'Semakan Permohonan JPPP',
            'breadcrumb'    => [
                ['label' => 'Semakan & Kelulusan', 'url' => '#'],
                'Semakan JPPP',
            ],
            'pendingList'   => $pendingList,
            'processedList' => $processedList,
            'stats'         => $stats,
            'currentUserRole' => $userRole,
        ]);
    }

    /**
     * Paparan Terperinci & Tindakan Hirarki
     */
    public function show(int $id)
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $application = $this->model->getWithReviewers($id);
        if (!$application) {
            return redirect()->to('review-jppp')->with('error', 'Permohonan tidak dijumpai.');
        }

        $procedures = !empty($application['procedures_data']) 
            ? json_decode($application['procedures_data'], true) 
            : [];

        $stageInfo = self::getApplicationStage($application);
        $userRole  = strtolower(session('role_name') ?? session('role') ?? 'user');
        $canAct    = ($userRole === 'admin') || in_array($userRole, $stageInfo['requiredRole'] ?? []);

        return view('review_jppp/show', [
            'pageTitle'       => 'Semakan Permohonan: ' . $application['application_no'],
            'breadcrumb'      => [
                ['label' => 'Semakan JPPP', 'url' => base_url('review-jppp')],
                $application['application_no'],
            ],
            'application'     => $application,
            'procedures'      => $procedures,
            'stageInfo'       => $stageInfo,
            'currentUserRole' => $userRole,
            'canAct'          => $canAct,
        ]);
    }

    /**
     * Proses Kelulusan Mengikut Hirarki:
     * Pegawai Menyemak PE -> Pegawai Perkhidmatan PE -> Ketua J3P -> Pengarah
     */
    public function process(int $id)
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $application = $this->model->find($id);
        if (!$application) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permohonan tidak dijumpai.']);
        }

        $stageInfo = self::getApplicationStage($application);
        $stage     = $stageInfo['stage'];
        $userRole  = strtolower(session('role_name') ?? session('role') ?? 'user');

        if (in_array($stage, ['approved', 'rejected'])) {
            return $this->response->setJSON([
                'status'  => 'error', 
                'message' => 'Permohonan ini telah selesai diproses (' . $stageInfo['label'] . ').'
            ]);
        }

        // Penguatkuasaan Hirarki: Hanya pegawai peringkat semasa atau admin yang dibenarkan
        if ($userRole !== 'admin' && !in_array($userRole, $stageInfo['requiredRole'] ?? [])) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => "Tindakan disekat mengikut hirarki. Permohonan ini kini dalam peringkat [{$stageInfo['officerTitle']}]. Sila tunggu pegawai peringkat tersebut menyelesaikan semakan."
            ]);
        }

        $action  = $this->request->getPost('action'); // 'approve' atau 'reject'
        $remarks = trim($this->request->getPost('remarks') ?? '');
        $userId  = session('user_id');
        $now     = date('Y-m-d H:i:s');

        if (!in_array($action, ['approve', 'reject'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tindakan semakan tidak sah.']);
        }

        if ($action === 'reject' && empty($remarks)) {
            return $this->response->setJSON([
                'status'  => 'error', 
                'message' => 'Sila masukkan catatan atau sebab penolakan bagi permohonan ini.'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $updateData  = [];
        $actionTitle = '';
        $logMsg      = '';
        $respMessage = '';

        if ($action === 'approve') {
            if ($stage === 'penyemak') {
                $updateData = [
                    'status'               => 'under_review',
                    'penyemak_status'      => 'approved',
                    'penyemak_verified_by' => $userId,
                    'penyemak_verified_at' => $now,
                    'penyemak_remarks'     => $remarks,
                ];
                $actionTitle = 'Semakan Pegawai Menyemak PE';
                $logMsg      = "Permohonan {$application['application_no']} telah disemak oleh Pegawai Menyemak PE. Disalurkan kepada Pegawai Perkhidmatan PE.";
                $respMessage = 'Permohonan berjaya disemak dan kini disalurkan kepada Pegawai Perkhidmatan PE.';
            } elseif ($stage === 'perkhidmatan') {
                $updateData = [
                    'status'                    => 'under_review',
                    'perkhidmatan_status'       => 'approved',
                    'perkhidmatan_verified_by'  => $userId,
                    'perkhidmatan_verified_at'  => $now,
                    'perkhidmatan_remarks'      => $remarks,
                ];
                $actionTitle = 'Pengesahan Pegawai Perkhidmatan PE';
                $logMsg      = "Permohonan {$application['application_no']} telah disahkan oleh Pegawai Perkhidmatan PE. Disalurkan kepada Ketua J3P.";
                $respMessage = 'Permohonan berjaya disahkan dan kini disalurkan kepada Ketua J3P.';
            } elseif ($stage === 'j3p') {
                $updateData = [
                    'status'           => 'under_review',
                    'j3p_status'       => 'approved',
                    'j3p_verified_by'  => $userId,
                    'j3p_verified_at'  => $now,
                    'j3p_remarks'      => $remarks,
                    'jppp_status'      => 'approved',
                    'jppp_verified_by' => $userId,
                    'jppp_verified_at' => $now,
                    'jppp_remarks'     => $remarks,
                ];
                $actionTitle = 'Pengesahan & Sokongan Ketua J3P';
                $logMsg      = "Permohonan {$application['application_no']} telah disahkan dan disokong oleh Ketua J3P. Disalurkan kepada Pengarah Hospital untuk kelulusan akhir.";
                $respMessage = 'Permohonan berjaya disahkan & disokong dan kini disalurkan kepada Pengarah Hospital untuk kelulusan akhir.';
            } elseif ($stage === 'pengarah') {
                $updateData = [
                    'status'               => 'approved',
                    'pengarah_status'      => 'approved',
                    'pengarah_verified_by' => $userId,
                    'pengarah_verified_at' => $now,
                    'pengarah_remarks'     => $remarks,
                ];
                $actionTitle = 'Kelulusan Pengarah Hospital';
                $logMsg      = "Permohonan {$application['application_no']} telah diluluskan penuh oleh Pengarah Hospital / KPTj.";
                $respMessage = 'Permohonan berjaya diluluskan penuh oleh Pengarah Hospital / KPTj.';
            }
        } else {
            // Penolakan pada peringkat semasa
            $stageOfficer = $stageInfo['officerTitle'];
            $updateData = [
                'status' => 'rejected',
            ];

            if ($stage === 'penyemak') {
                $updateData['penyemak_status']      = 'rejected';
                $updateData['penyemak_verified_by'] = $userId;
                $updateData['penyemak_verified_at'] = $now;
                $updateData['penyemak_remarks']     = $remarks;
            } elseif ($stage === 'perkhidmatan') {
                $updateData['perkhidmatan_status']      = 'rejected';
                $updateData['perkhidmatan_verified_by'] = $userId;
                $updateData['perkhidmatan_verified_at'] = $now;
                $updateData['perkhidmatan_remarks']     = $remarks;
            } elseif ($stage === 'j3p') {
                $updateData['j3p_status']          = 'rejected';
                $updateData['j3p_verified_by']     = $userId;
                $updateData['j3p_verified_at']     = $now;
                $updateData['j3p_remarks']         = $remarks;
                $updateData['jppp_status']         = 'rejected';
                $updateData['jppp_verified_by']    = $userId;
                $updateData['jppp_verified_at']    = $now;
                $updateData['jppp_remarks']        = $remarks;
            } elseif ($stage === 'pengarah') {
                $updateData['pengarah_status']      = 'rejected';
                $updateData['pengarah_verified_by'] = $userId;
                $updateData['pengarah_verified_at'] = $now;
                $updateData['pengarah_remarks']     = $remarks;
            }

            $actionTitle = "Penolakan Permohonan ({$stageOfficer})";
            $logMsg      = "Permohonan {$application['application_no']} telah ditolak oleh {$stageOfficer}. Sebab: {$remarks}";
            $respMessage = "Permohonan telah ditolak oleh {$stageOfficer}.";
        }

        $this->model->update($id, $updateData);

        // Rekod jejak audit ke activity_logs
        try {
            $db->table('activity_logs')->insert([
                'user_id'     => $userId,
                'username'    => session('name') ?? session('fullname') ?? 'Pegawai Semakan',
                'action'      => $actionTitle,
                'description' => $logMsg,
                'ip_address'  => $this->request->getIPAddress(),
                'user_agent'  => $this->request->getUserAgent()->getAgentString(),
                'created_at'  => $now,
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Gagal mencatat log aktiviti semakan: ' . $e->getMessage());
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status'  => 'error', 
                'message' => 'Gagal mengemaskini status permohonan. Sila cuba lagi.'
            ]);
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'message'  => $respMessage,
            'redirect' => base_url('review-jppp'),
        ]);
    }
}
