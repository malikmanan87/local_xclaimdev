<?php

namespace App\Controllers;

use App\Models\NewApplicationModel;
use App\Models\MmaProcedureModel;
use App\Models\UserModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $appModel = new NewApplicationModel();
        $mmaModel = new MmaProcedureModel();

        $userId   = session('user_id');
        $userRole = session('role_name') ?? session('role') ?? 'user';
        $isAdmin  = in_array(strtolower($userRole), ['admin', 'manager', 'pegawai_penyemak_pe', 'pegawai_perkhidmatan_pe', 'ketua_j3p', 'pengarah']);

        // 1. KPI Metrik Utama Sistem Tuntutan
        $totalClaims        = (int) $db->table('new_applications')->countAllResults();
        $pendingClaims      = (int) $db->table('new_applications')->whereIn('status', ['submitted', 'under_review'])->countAllResults();
        $approvedClaims     = (int) $db->table('new_applications')->where('status', 'approved')->countAllResults();
        $rejectedClaims     = (int) $db->table('new_applications')->where('status', 'rejected')->countAllResults();
        $draftClaims        = (int) $db->table('new_applications')->where('status', 'draft')->countAllResults();

        // Jumlah Kewangan Tuntutan
        $financialTotals = $db->table('new_applications')
            ->selectSum('total_gross', 'gross')
            ->selectSum('total_claim', 'claim')
            ->selectSum('total_welfare', 'welfare')
            ->get()
            ->getRowArray();

        $totalGrossAmount   = (float) ($financialTotals['gross'] ?? 0);
        $totalClaimAmount   = (float) ($financialTotals['claim'] ?? 0);
        $totalWelfareAmount = (float) ($financialTotals['welfare'] ?? 0);

        // Bilangan Prosedur MMA dalam Master
        $totalProcedures = (int) $db->table('mma_procedures')->countAllResults();

        // 2. Trend Bulanan Tahun Semasa (Statistik Tuntutan Bulanan)
        $currentYear = date('Y');
        $monthlyQuery = $db->table('new_applications')
            ->select('MONTH(created_at) as bulan, COUNT(id) as total_count, SUM(total_claim) as total_amt')
            ->where('YEAR(created_at)', $currentYear)
            ->groupBy('MONTH(created_at)')
            ->get()
            ->getResultArray();

        $monthsCountData  = array_fill(1, 12, 0);
        $monthsAmountData = array_fill(1, 12, 0.0);

        foreach ($monthlyQuery as $row) {
            $m = (int) $row['bulan'];
            $monthsCountData[$m]  = (int) $row['total_count'];
            $monthsAmountData[$m] = round((float) $row['total_amt'], 2);
        }

        $monthlyLabels = ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogo', 'Sep', 'Okt', 'Nov', 'Dis'];
        $monthlyCounts = array_values($monthsCountData);
        $monthlyAmounts = array_values($monthsAmountData);

        // 3. Taburan Status Permohonan (Pie / Donut)
        $statusCounts = [
            'draft'        => $draftClaims,
            'submitted'    => (int) $db->table('new_applications')->where('status', 'submitted')->countAllResults(),
            'under_review' => (int) $db->table('new_applications')->where('status', 'under_review')->countAllResults(),
            'approved'     => $approvedClaims,
            'rejected'     => $rejectedClaims,
        ];

        // 4. Permohonan Tuntutan Terkini (Recent Claims)
        $recentApplications = $db->table('new_applications na')
            ->select('na.*, u.fullname as creator_name')
            ->join('users u', 'u.id = na.submitted_by', 'left')
            ->orderBy('na.created_at', 'DESC')
            ->limit(6)
            ->get()
            ->getResultArray();

        // 5. Log Aktiviti Terkini
        $recentLogs = $db->table('activity_logs al')
            ->select('al.*, u.username')
            ->join('users u', 'u.id = al.user_id', 'left')
            ->orderBy('al.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return view('dashboard', [
            'pageTitle'          => 'Dashboard Tuntutan Pakar',
            'userRole'           => $userRole,
            'isAdmin'            => $isAdmin,
            'totalClaims'        => $totalClaims,
            'pendingClaims'      => $pendingClaims,
            'approvedClaims'     => $approvedClaims,
            'rejectedClaims'     => $rejectedClaims,
            'draftClaims'        => $draftClaims,
            'totalGrossAmount'   => $totalGrossAmount,
            'totalClaimAmount'   => $totalClaimAmount,
            'totalWelfareAmount' => $totalWelfareAmount,
            'totalProcedures'    => $totalProcedures,
            'monthlyLabels'      => $monthlyLabels,
            'monthlyCounts'      => $monthlyCounts,
            'monthlyAmounts'     => $monthlyAmounts,
            'statusCounts'       => $statusCounts,
            'recentApplications' => $recentApplications,
            'recentLogs'         => $recentLogs,
        ]);
    }
}

