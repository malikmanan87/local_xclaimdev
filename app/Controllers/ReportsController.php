<?php

namespace App\Controllers;

use App\Models\NewApplicationModel;

class ReportsController extends BaseController
{
    protected NewApplicationModel $appModel;

    public function __construct()
    {
        $this->appModel = new NewApplicationModel();
        helper(['form', 'url']);
    }

    /**
     * Semak hak akses peranan - terhad kepada semua peranan KECUALI user (pakar)
     */
    protected function checkAccess()
    {
        $role = strtolower(session('role_name') ?? session('role') ?? 'user');
        if ($role === 'user') {
            return redirect()->to('dashboard')->with('error', 'Akses tidak dibenarkan. Modul Laporan terhad kepada pihak pengurusan dan pegawai sahaja.');
        }
        return null;
    }

    // ----------------------------------------------------------------
    // GET/POST /reports (Paparan Laporan Tuntutan & Statistik)
    // ----------------------------------------------------------------
    public function index()
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $filters = [
            'start_date' => $this->request->getVar('start_date') ?: '',
            'end_date'   => $this->request->getVar('end_date') ?: '',
            'department' => $this->request->getVar('department') ?: '',
            'status'     => $this->request->getVar('status') ?: '',
            'stage'      => $this->request->getVar('stage') ?: '',
            'search'     => $this->request->getVar('search') ?: '',
        ];

        $reportResult = $this->appModel->getReportData($filters, null);
        $departments  = $this->appModel->getUniqueDepartments();
        $userRole     = strtolower(session('role_name') ?? session('role') ?? 'user');
        $isPrivileged = ($userRole !== 'user');

        return view('reports/index', [
            'pageTitle'    => 'Laporan Tuntutan Perkhidmatan Pakar',
            'breadcrumb'   => ['Laporan'],
            'departments'  => $departments,
            'reportData'   => $reportResult['records'],
            'summary'      => $reportResult['summary'],
            'filters'      => $filters,
            'isPrivileged' => $isPrivileged,
        ]);
    }

    // ----------------------------------------------------------------
    // POST /reports/generate (Alias penjanaan carian)
    // ----------------------------------------------------------------
    public function generate()
    {
        return $this->index();
    }

    // ----------------------------------------------------------------
    // GET /reports/export (Muat Turun Laporan CSV / Excel)
    // ----------------------------------------------------------------
    public function export()
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $filters = [
            'start_date' => $this->request->getGet('start_date') ?: '',
            'end_date'   => $this->request->getGet('end_date') ?: '',
            'department' => $this->request->getGet('department') ?: '',
            'status'     => $this->request->getGet('status') ?: '',
            'stage'      => $this->request->getGet('stage') ?: '',
            'search'     => $this->request->getGet('search') ?: '',
        ];

        $reportResult = $this->appModel->getReportData($filters, null);
        $records      = $reportResult['records'];

        $filename = 'Laporan_Tuntutan_XClaim_' . date('Ymd_His') . '.csv';

        // Set response headers for direct download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Output UTF-8 BOM for Microsoft Excel compatibility
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header Columns mengikut aliran rasmi 5-peringkat
        fputcsv($output, [
            'Bil',
            'No. Permohonan',
            'Tarikh Permohonan',
            'Nama Pakar',
            'No. Staf',
            'Jabatan / Disiplin',
            'Jawatan',
            'RN Pesakit',
            'Nama Pesakit',
            'No. K/P Pesakit',
            'ID Lawatan / Visit ID',
            'Jumlah Kasar (RM)',
            'Tabung Kebajikan (RM)',
            'Jumlah Bersih Dituntut (RM)',
            'Status Keseluruhan',
            'Peringkat Semasa (Hirarki)',
            'Pegawai Menyemak PE',
            'Status Semakan PE',
            'Tarikh Semakan PE',
            'Pegawai Perkhidmatan PE',
            'Status Perkhidmatan PE',
            'Tarikh Perkhidmatan PE',
            'Ketua J3P',
            'Status Perakuan J3P',
            'Tarikh Perakuan J3P',
            'Pengarah Hospital / KPTj',
            'Status Kelulusan Pengarah',
            'Tarikh Kelulusan Pengarah'
        ]);

        // Data Rows
        foreach ($records as $index => $row) {
            $stageInfo = $row['stageInfo'] ?? \App\Controllers\ReviewJpppController::getApplicationStage($row);

            fputcsv($output, [
                $index + 1,
                $row['application_no'] ?? '-',
                !empty($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-',
                $row['specialist_name'] ?? '-',
                $row['staff_number'] ?? '-',
                $row['department'] ?? '-',
                $row['position'] ?? '-',
                $row['patient_rn'] ?? '-',
                $row['patient_name'] ?? '-',
                $row['patient_ic'] ?? '-',
                $row['visit_id'] ?? '-',
                number_format((float)($row['total_gross'] ?? 0), 2, '.', ''),
                number_format((float)($row['total_welfare'] ?? 0), 2, '.', ''),
                number_format((float)($row['total_claim'] ?? 0), 2, '.', ''),
                strtoupper($row['status'] ?? '-'),
                $stageInfo['label'] ?? '-',
                $row['penyemak_reviewer_name'] ?? '-',
                strtoupper($row['penyemak_status'] ?? 'PENDING'),
                !empty($row['penyemak_verified_at']) ? date('d/m/Y H:i', strtotime($row['penyemak_verified_at'])) : '-',
                $row['perkhidmatan_reviewer_name'] ?? '-',
                strtoupper($row['perkhidmatan_status'] ?? 'PENDING'),
                !empty($row['perkhidmatan_verified_at']) ? date('d/m/Y H:i', strtotime($row['perkhidmatan_verified_at'])) : '-',
                $row['j3p_reviewer_name'] ?? '-',
                strtoupper($row['j3p_status'] ?? 'PENDING'),
                !empty($row['j3p_verified_at']) ? date('d/m/Y H:i', strtotime($row['j3p_verified_at'])) : '-',
                $row['pengarah_reviewer_name'] ?? '-',
                strtoupper($row['pengarah_status'] ?? 'PENDING'),
                !empty($row['pengarah_verified_at']) ? date('d/m/Y H:i', strtotime($row['pengarah_verified_at'])) : '-',
            ]);
        }

        fclose($output);
        exit;
    }

    // ----------------------------------------------------------------
    // GET /reports/print (Paparan Cetakan Rasmi Laporan)
    // ----------------------------------------------------------------
    public function print()
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $filters = [
            'start_date' => $this->request->getGet('start_date') ?: '',
            'end_date'   => $this->request->getGet('end_date') ?: '',
            'department' => $this->request->getGet('department') ?: '',
            'status'     => $this->request->getGet('status') ?: '',
            'stage'      => $this->request->getGet('stage') ?: '',
            'search'     => $this->request->getGet('search') ?: '',
        ];

        $reportResult = $this->appModel->getReportData($filters, null);

        return view('reports/print', [
            'pageTitle'  => 'Cetak Laporan Tuntutan Perkhidmatan Pakar',
            'reportData' => $reportResult['records'],
            'summary'    => $reportResult['summary'],
            'filters'    => $filters,
        ]);
    }
}