<?php

namespace App\Controllers;

use App\Models\NewApplicationModel;

class ReviewKewanganController extends BaseController
{
    protected NewApplicationModel $model;

    public function __construct()
    {
        $this->model = new NewApplicationModel();
    }

    /**
     * Semak hak akses (Admin, Manager, atau Pegawai Kewangan)
     */
    protected function checkAccess()
    {
        $role = strtolower(session('role_name') ?? session('role') ?? 'user');
        if (!in_array($role, ['admin', 'manager', 'kewangan'])) {
            return redirect()->to('dashboard')->with('error', 'Akses tidak dibenarkan. Modul ini khusus untuk Bahagian Kewangan.');
        }
        return null;
    }

    /**
     * Senarai Permohonan untuk Semakan & Kelulusan Kewangan
     */
    public function index()
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        // Permohonan yang disahkan JPPP dan Menunggu Kelulusan Kewangan
        $pendingList = $this->model->select('new_applications.*, 
                                            u_sub.fullname AS creator_name,
                                            u_jppp.fullname AS jppp_reviewer_name')
            ->join('users u_sub', 'u_sub.id = new_applications.submitted_by', 'left')
            ->join('users u_jppp', 'u_jppp.id = new_applications.jppp_verified_by', 'left')
            ->where('jppp_status', 'approved')
            ->where('finance_status', 'pending')
            ->orderBy('new_applications.jppp_verified_at', 'ASC')
            ->findAll();

        // Sejarah Permohonan yang telah diproses oleh Kewangan
        $processedList = $this->model->select('new_applications.*, 
                                              u_sub.fullname AS creator_name,
                                              u_fin.fullname AS finance_reviewer_name')
            ->join('users u_sub', 'u_sub.id = new_applications.submitted_by', 'left')
            ->join('users u_fin', 'u_fin.id = new_applications.finance_verified_by', 'left')
            ->whereIn('finance_status', ['approved', 'rejected'])
            ->orderBy('new_applications.finance_verified_at', 'DESC')
            ->findAll();

        $db = \Config\Database::connect();

        // Kira jumlah wang yang menunggu & diluluskan
        $pendingSum = $db->table('new_applications')
            ->where('jppp_status', 'approved')
            ->where('finance_status', 'pending')
            ->selectSum('total_claim', 'total_pending')
            ->get()
            ->getRowArray();

        $approvedSum = $db->table('new_applications')
            ->where('finance_status', 'approved')
            ->selectSum('total_claim', 'total_approved')
            ->selectSum('total_welfare', 'total_welfare')
            ->get()
            ->getRowArray();

        $stats = [
            'pending_count'    => count($pendingList),
            'approved_count'   => $this->model->where('finance_status', 'approved')->countAllResults(),
            'rejected_count'   => $this->model->where('finance_status', 'rejected')->countAllResults(),
            'pending_amount'   => (float) ($pendingSum['total_pending'] ?? 0),
            'approved_amount'  => (float) ($approvedSum['total_approved'] ?? 0),
            'welfare_amount'   => (float) ($approvedSum['total_welfare'] ?? 0),
        ];

        return view('review_kewangan/index', [
            'pageTitle'     => 'Semakan & Kelulusan Kewangan',
            'breadcrumb'    => [
                ['label' => 'Semakan & Kelulusan', 'url' => '#'],
                'Semakan Kewangan',
            ],
            'pendingList'   => $pendingList,
            'processedList' => $processedList,
            'stats'         => $stats,
        ]);
    }

    /**
     * Paparan Terperinci & Pengesahan Baucar Pembayaran
     */
    public function show(int $id)
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $application = $this->model->getWithReviewers($id);
        if (!$application) {
            return redirect()->to('review-kewangan')->with('error', 'Permohonan tidak dijumpai.');
        }

        $procedures = !empty($application['procedures_data']) 
            ? json_decode($application['procedures_data'], true) 
            : [];

        return view('review_kewangan/show', [
            'pageTitle'   => 'Semakan Kewangan: ' . $application['application_no'],
            'breadcrumb'  => [
                ['label' => 'Semakan Kewangan', 'url' => base_url('review-kewangan')],
                $application['application_no'],
            ],
            'application' => $application,
            'procedures'  => $procedures,
        ]);
    }

    /**
     * Proses Kelulusan Bayaran / Penolakan oleh Bahagian Kewangan
     */
    public function process(int $id)
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $application = $this->model->find($id);
        if (!$application) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permohonan tidak dijumpai.']);
        }

        $action    = $this->request->getPost('action'); // 'approve' atau 'reject'
        $voucherNo = trim($this->request->getPost('voucher_no') ?? '');
        $remarks   = trim($this->request->getPost('remarks') ?? '');
        $userId    = session('user_id');
        $now       = date('Y-m-d H:i:s');

        if (!in_array($action, ['approve', 'reject'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tindakan semakan kewangan tidak sah.']);
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
                'status'              => 'approved', // Status muktamad diluluskan untuk bayaran
                'finance_status'      => 'approved',
                'finance_verified_by' => $userId,
                'finance_verified_at' => $now,
                'finance_voucher_no'  => !empty($voucherNo) ? $voucherNo : 'VCR-' . date('Ymd') . '-' . sprintf('%04d', $id),
                'finance_remarks'     => $remarks,
            ];
            $actionTitle = 'Luluskan Bayaran Tuntutan';
            $vcrStr = !empty($updateData['finance_voucher_no']) ? " (No. Baucar: {$updateData['finance_voucher_no']})" : '';
            $logMsg = "Permohonan tuntutan {$application['application_no']} telah diluluskan untuk pembayaran sebanyak RM " . number_format($application['total_claim'], 2) . "{$vcrStr}.";
        } else {
            $updateData = [
                'status'              => 'rejected',
                'finance_status'      => 'rejected',
                'finance_verified_by' => $userId,
                'finance_verified_at' => $now,
                'finance_remarks'     => $remarks,
            ];
            $actionTitle = 'Tolak Bayaran Tuntutan';
            $logMsg = "Permohonan tuntutan {$application['application_no']} telah ditolak oleh Bahagian Kewangan. Sebab: {$remarks}";
        }

        $this->model->update($id, $updateData);

        // Rekod ke activity_logs
        try {
            $db->table('activity_logs')->insert([
                'user_id'     => $userId,
                'username'    => session('name') ?? session('fullname') ?? 'Pegawai Kewangan',
                'action'      => $actionTitle,
                'description' => $logMsg,
                'ip_address'  => $this->request->getIPAddress(),
                'user_agent'  => $this->request->getUserAgent()->getAgentString(),
                'created_at'  => $now,
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Gagal mencatat log aktiviti semakan kewangan: ' . $e->getMessage());
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'status'  => 'error', 
                'message' => 'Gagal memproses kelulusan kewangan. Sila cuba lagi.'
            ]);
        }

        return $this->response->setJSON([
            'status'   => 'success',
            'message'  => $action === 'approve' 
                ? 'Permohonan tuntutan telah diluluskan untuk pembayaran.' 
                : 'Permohonan tuntutan telah ditolak oleh Bahagian Kewangan.',
            'redirect' => base_url('review-kewangan'),
        ]);
    }
}
