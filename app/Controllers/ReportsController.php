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

    // ----------------------------------------------------------------
    // GET/POST /reports (Paparan Laporan Tuntutan & Statistik)
    // ----------------------------------------------------------------
    public function index()
    {
        $filters = [
            'start_date'     => $this->request->getVar('start_date') ?: '',
            'end_date'       => $this->request->getVar('end_date') ?: '',
            'department'     => $this->request->getVar('department') ?: '',
            'status'         => $this->request->getVar('status') ?: '',
            'jppp_status'    => $this->request->getVar('jppp_status') ?: '',
            'finance_status' => $this->request->getVar('finance_status') ?: '',
            'search'         => $this->request->getVar('search') ?: '',
        ];

        // Kawalan akses: Jika peranan adalah 'user' biasa, hadkan kepada tuntutan miliknya
        $userRole = strtolower(session('role_name') ?? session('role') ?? 'user');
        $isPrivileged = in_array($userRole, ['admin', 'manager', 'jppp', 'kewangan']);
        $userId = $isPrivileged ? null : (int)session('user_id');

        $reportResult = $this->appModel->getReportData($filters, $userId);
        $departments  = $this->appModel->getUniqueDepartments();

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
        $filters = [
            'start_date'     => $this->request->getGet('start_date') ?: '',
            'end_date'       => $this->request->getGet('end_date') ?: '',
            'department'     => $this->request->getGet('department') ?: '',
            'status'         => $this->request->getGet('status') ?: '',
            'jppp_status'    => $this->request->getGet('jppp_status') ?: '',
            'finance_status' => $this->request->getGet('finance_status') ?: '',
            'search'         => $this->request->getGet('search') ?: '',
        ];

        $userRole = strtolower(session('role_name') ?? session('role') ?? 'user');
        $isPrivileged = in_array($userRole, ['admin', 'manager', 'jppp', 'kewangan']);
        $userId = $isPrivileged ? null : (int)session('user_id');

        $reportResult = $this->appModel->getReportData($filters, $userId);
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

        // Header Columns
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
            'Status Permohonan',
            'Status Semakan JPPP',
            'Disemak Oleh JPPP',
            'Tarikh Semakan JPPP',
            'Status Semakan Kewangan',
            'Disemak Oleh Kewangan',
            'Tarikh Semakan Kewangan',
            'No. Baucar Bayaran',
            'Catatan JPPP',
            'Catatan Kewangan'
        ]);

        // Data Rows
        foreach ($records as $index => $row) {
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
                strtoupper($row['jppp_status'] ?? 'PENDING'),
                $row['jppp_reviewer_name'] ?? '-',
                !empty($row['jppp_verified_at']) ? date('d/m/Y H:i', strtotime($row['jppp_verified_at'])) : '-',
                strtoupper($row['finance_status'] ?? 'PENDING'),
                $row['finance_reviewer_name'] ?? '-',
                !empty($row['finance_verified_at']) ? date('d/m/Y H:i', strtotime($row['finance_verified_at'])) : '-',
                $row['finance_voucher_no'] ?? '-',
                $row['jppp_remarks'] ?? '-',
                $row['finance_remarks'] ?? '-'
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
        $filters = [
            'start_date'     => $this->request->getGet('start_date') ?: '',
            'end_date'       => $this->request->getGet('end_date') ?: '',
            'department'     => $this->request->getGet('department') ?: '',
            'status'         => $this->request->getGet('status') ?: '',
            'jppp_status'    => $this->request->getGet('jppp_status') ?: '',
            'finance_status' => $this->request->getGet('finance_status') ?: '',
            'search'         => $this->request->getGet('search') ?: '',
        ];

        $userRole = strtolower(session('role_name') ?? session('role') ?? 'user');
        $isPrivileged = in_array($userRole, ['admin', 'manager', 'jppp', 'kewangan']);
        $userId = $isPrivileged ? null : (int)session('user_id');

        $reportResult = $this->appModel->getReportData($filters, $userId);

        return view('reports/print', [
            'pageTitle'  => 'Cetak Laporan Tuntutan Perkhidmatan Pakar',
            'reportData' => $reportResult['records'],
            'summary'    => $reportResult['summary'],
            'filters'    => $filters,
        ]);
    }
}