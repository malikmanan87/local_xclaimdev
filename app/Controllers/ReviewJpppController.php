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
     * Semak hak akses (Admin, Manager, atau JPPP)
     */
    protected function checkAccess()
    {
        $role = strtolower(session('role_name') ?? session('role') ?? 'user');
        if (!in_array($role, ['admin', 'manager', 'jppp', 'pegawai_penyemak_pe', 'ketua_j3p', 'pengarah'])) {
            return redirect()->to('dashboard')->with('error', 'Akses tidak dibenarkan. Modul ini khusus untuk Jawatankuasa JPPP.');
        }
        return null;
    }

    /**
     * Senarai Permohonan untuk Semakan JPPP
     */
    public function index()
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        // Permohonan Menunggu Semakan JPPP
        $pendingList = $this->model->select('new_applications.*, users.fullname AS creator_name')
            ->join('users', 'users.id = new_applications.submitted_by', 'left')
            ->where('status', 'submitted')
            ->groupStart()
                ->where('jppp_status', 'pending')
                ->orWhere('jppp_status IS NULL', null, false)
            ->groupEnd()
            ->orderBy('new_applications.submitted_at', 'ASC')
            ->findAll();

        // Sejarah Permohonan yang telah disemak oleh JPPP
        $processedList = $this->model->select('new_applications.*, 
                                              u_sub.fullname AS creator_name,
                                              u_jppp.fullname AS jppp_reviewer_name')
            ->join('users u_sub', 'u_sub.id = new_applications.submitted_by', 'left')
            ->join('users u_jppp', 'u_jppp.id = new_applications.jppp_verified_by', 'left')
            ->whereIn('jppp_status', ['approved', 'rejected'])
            ->orderBy('new_applications.jppp_verified_at', 'DESC')
            ->findAll();

        $stats = [
            'pending'  => count($pendingList),
            'approved' => $this->model->where('jppp_status', 'approved')->countAllResults(),
            'rejected' => $this->model->where('jppp_status', 'rejected')->countAllResults(),
            'total'    => count($pendingList) + count($processedList),
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
        ]);
    }

    /**
     * Paparan Terperinci & Borang Tindakan JPPP
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

        return view('review_jppp/show', [
            'pageTitle'   => 'Semakan Permohonan: ' . $application['application_no'],
            'breadcrumb'  => [
                ['label' => 'Semakan JPPP', 'url' => base_url('review-jppp')],
                $application['application_no'],
            ],
            'application' => $application,
            'procedures'  => $procedures,
        ]);
    }

    /**
     * Proses Pengesahan / Penolakan oleh JPPP
     */
    public function process(int $id)
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $application = $this->model->find($id);
        if (!$application) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permohonan tidak dijumpai.']);
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
                'message' => 'Sila masukkan catatan/sebab penolakan bagi permohonan ini.'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        if ($action === 'approve') {
            $updateData = [
                'status'              => 'under_review',
                'jppp_status'         => 'approved',
                'jppp_verified_by'    => $userId,
                'jppp_verified_at'    => $now,
                'jppp_remarks'        => $remarks,
                'j3p_status'          => 'approved',
                'j3p_verified_by'     => $userId,
                'j3p_verified_at'     => $now,
                'j3p_remarks'         => $remarks,
            ];
            $actionTitle = 'Sahkan Permohonan JPPP';
            $logMsg = "Permohonan tuntutan {$application['application_no']} telah disahkan dan disokong oleh Ketua J3P/JPPP. Disalurkan kepada Pengarah Hospital untuk kelulusan akhir.";
        } else {
            $updateData = [
                'status'              => 'rejected',
                'jppp_status'         => 'rejected',
                'jppp_verified_by'    => $userId,
                'jppp_verified_at'    => $now,
                'jppp_remarks'        => $remarks,
                'j3p_status'          => 'rejected',
                'j3p_verified_by'     => $userId,
                'j3p_verified_at'     => $now,
                'j3p_remarks'         => $remarks,
            ];
            $actionTitle = 'Tolak Permohonan JPPP';
            $logMsg = "Permohonan tuntutan {$application['application_no']} telah ditolak oleh JPPP. Sebab: {$remarks}";
        }

        $this->model->update($id, $updateData);

        // Rekod ke activity_logs
        try {
            $db->table('activity_logs')->insert([
                'user_id'     => $userId,
                'username'    => session('name') ?? session('fullname') ?? 'Pegawai JPPP',
                'action'      => $actionTitle,
                'description' => $logMsg,
                'ip_address'  => $this->request->getIPAddress(),
                'user_agent'  => $this->request->getUserAgent()->getAgentString(),
                'created_at'  => $now,
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Gagal mencatat log aktiviti semakan JPPP: ' . $e->getMessage());
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status'  => 'error', 
                'message' => 'Gagal mengemaskini status permohonan. Sila cuba lagi.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $action === 'approve' 
                ? 'Permohonan berjaya disahkan dan disalurkan ke Bahagian Kewangan.' 
                : 'Permohonan telah ditolak oleh JPPP.',
            'redirect' => base_url('review-jppp'),
        ]);
    }
}
