<?php

namespace App\Controllers;

use App\Models\ActivityLogModel;

class ActivityLogsController extends BaseController
{
    protected $logModel;

    public function __construct()
    {
        $this->logModel = new ActivityLogModel();
    }

    // ----------------------------------------------------------------
    // GET /activity-logs (Admin Sahaja)
    // ----------------------------------------------------------------
    public function index()
    {
        $totalLogs = $this->logModel->countAllResults();

        return view('activity_logs/index', [
            'pageTitle'  => 'Activity Logs',
            'breadcrumb' => ['Activity Logs'],
            'totalLogs'  => $totalLogs
        ]);
    }

    // ----------------------------------------------------------------
    // GET /activity-logs/data (AJAX DataTables Server-Side Processing)
    // ----------------------------------------------------------------
    public function data()
    {
        $request = $this->request;

        $draw   = (int) ($request->getGet('draw') ?? 1);
        $start  = (int) ($request->getGet('start') ?? 0);
        $length = (int) ($request->getGet('length') ?? 15);
        if ($length < 1) {
            $length = 15;
        }

        $searchArray = $request->getGet('search');
        $searchValue = is_array($searchArray) ? trim($searchArray['value'] ?? '') : '';

        // Pemetaan susunan kolum
        $orderArray  = $request->getGet('order');
        $orderColIdx = is_array($orderArray) && isset($orderArray[0]['column']) ? (int) $orderArray[0]['column'] : 1;
        $orderDir    = is_array($orderArray) && isset($orderArray[0]['dir']) && strtolower($orderArray[0]['dir']) === 'asc' ? 'ASC' : 'DESC';

        $columnMap = [
            0 => 'activity_logs.id',
            1 => 'activity_logs.created_at',
            2 => 'u.fullname',
            3 => 'activity_logs.action',
            4 => 'activity_logs.description',
            5 => 'activity_logs.ip_address'
        ];
        $orderColumn = $columnMap[$orderColIdx] ?? 'activity_logs.created_at';

        // Jumlah keseluruhan rekod tanpa sebarang filter
        $totalRecords = $this->logModel->countAllResults();

        // Bina kueri dengan JOIN
        $builder = $this->logModel
            ->select('activity_logs.*, u.fullname, u.username as user_staffno, u.email as user_email, r.display_name as role_name')
            ->join('users u', 'u.id = activity_logs.user_id', 'left')
            ->join('roles r', 'r.id = u.role_id', 'left');

        // Carian pantas jika ada input
        if ($searchValue !== '') {
            $builder->groupStart()
                ->like('activity_logs.action', $searchValue)
                ->orLike('activity_logs.description', $searchValue)
                ->orLike('activity_logs.ip_address', $searchValue)
                ->orLike('activity_logs.username', $searchValue)
                ->orLike('u.fullname', $searchValue)
                ->orLike('u.username', $searchValue)
                ->orLike('r.display_name', $searchValue)
                ->groupEnd();
        }

        // Jumlah rekod selepas ditapis
        $recordsFiltered = $builder->countAllResults(false);

        // Ambil data untuk muka surat semasa
        $logs = $builder->orderBy($orderColumn, $orderDir)
                        ->findAll($length, $start);

        // Formatkan data HTML untuk DataTables
        $data = [];
        $no = $start + 1;

        foreach ($logs as $log) {
            // Tentukan nama paparan pengguna
            $userName = !empty($log['fullname']) 
                ? esc($log['fullname']) 
                : (!empty($log['username']) && $log['username'] !== 'Guest' ? esc($log['username']) : 'Sistem / Tetamu');
            
            $staffCode = !empty($log['user_staffno']) 
                ? esc($log['user_staffno']) 
                : (!empty($log['username']) && $log['username'] !== 'Guest' ? esc($log['username']) : null);
            
            $roleName = !empty($log['role_name']) ? esc($log['role_name']) : null;

            // Klasifikasi warna lencana tindakan
            $action = esc($log['action']);
            $badgeClass = 'bg-secondary text-white';
            if (stripos($action, 'Masuk') !== false || stripos($action, 'Login') !== false) {
                $badgeClass = 'bg-success text-white';
            } elseif (stripos($action, 'Padam') !== false || stripos($action, 'Delete') !== false || stripos($action, 'Reject') !== false) {
                $badgeClass = 'bg-danger text-white';
            } elseif (stripos($action, 'Kemaskini') !== false || stripos($action, 'Update') !== false) {
                $badgeClass = 'bg-warning text-dark';
            } elseif (stripos($action, 'Tambah') !== false || stripos($action, 'Create') !== false || stripos($action, 'Add') !== false || stripos($action, 'Approve') !== false) {
                $badgeClass = 'bg-info text-dark';
            } elseif (stripos($action, 'Kelulusan') !== false || stripos($action, 'Sokongan') !== false || stripos($action, 'Semakan') !== false) {
                $badgeClass = 'bg-primary text-white';
            }

            // Kolum 1: Tarikh & Masa
            $timeStr = strtotime($log['created_at']);
            $colDate = '<div class="fw-semibold text-dark">' . date('d/m/Y', $timeStr) . '</div>' .
                       '<div class="text-muted" style="font-size: 0.75rem;">' . date('h:i:s A', $timeStr) . '</div>';

            // Kolum 2: Pengguna
            $userHtml = '<div class="fw-semibold text-dark text-truncate" style="max-width: 170px;" title="' . $userName . '">' . $userName . '</div>';
            $metaBadges = '<div class="d-flex align-items-center gap-1 mt-0.5">';
            if ($staffCode) {
                $metaBadges .= '<span class="text-muted small font-monospace" style="font-size: 0.72rem;">@' . $staffCode . '</span>';
            }
            if ($roleName) {
                $metaBadges .= '<span class="badge bg-light text-secondary border px-1.5 py-0.5" style="font-size: 0.65rem;">' . esc($roleName) . '</span>';
            }
            $metaBadges .= '</div>';
            $colUser = $userHtml . $metaBadges;

            // Kolum 3: Tindakan
            $colAction = '<span class="badge ' . $badgeClass . ' small px-2 py-1">' . $action . '</span>';

            // Kolum 4: Butiran
            $colDesc = '<div style="line-height: 1.45;">' . esc($log['description']) . '</div>';

            // Kolum 5: IP & Peranti
            $ip = esc($log['ip_address'] ?? '127.0.0.1');
            $colIp = '<div class="font-monospace small text-dark fw-semibold" style="font-size: 0.78rem;">' . $ip . '</div>';
            if (!empty($log['user_agent'])) {
                $ua = esc($log['user_agent']);
                $uaShort = esc(substr($log['user_agent'], 0, 30));
                $colIp .= '<div class="text-muted small text-truncate d-inline-block" style="max-width: 130px; font-size: 0.68rem;" title="' . $ua . '">' .
                          '<i class="bi bi-laptop me-1"></i>' . $uaShort . '...</div>';
            }

            $data[] = [
                $no++,
                $colDate,
                $colUser,
                $colAction,
                $colDesc,
                $colIp
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $data
        ]);
    }
}
