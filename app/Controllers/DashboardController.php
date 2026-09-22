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

        $userId   = (int)session('user_id');
        $userRole = strtolower(session('role_name') ?? session('role') ?? 'user');
        // 'user' (pakar) hanya melihat rekod sendiri; semua role lain boleh melihat semua rekod
        $isOnlySelf = ($userRole === 'user');

        // Helper untuk tapis permohonan milik pemohon jika role adalah 'user'
        $applyFilter = function($builder) use ($isOnlySelf, $userId) {
            if ($isOnlySelf) {
                $builder->where('submitted_by', $userId);
            }
            return $builder;
        };

        // 1. KPI Metrik Utama Sistem Tuntutan
        $totalClaims        = (int) $applyFilter($db->table('new_applications'))->countAllResults();
        $pendingClaims      = (int) $applyFilter($db->table('new_applications'))->whereIn('status', ['submitted', 'under_review'])->countAllResults();
        $approvedClaims     = (int) $applyFilter($db->table('new_applications'))->where('status', 'approved')->countAllResults();
        $rejectedClaims     = (int) $applyFilter($db->table('new_applications'))->where('status', 'rejected')->countAllResults();
        $draftClaims        = (int) $applyFilter($db->table('new_applications'))->where('status', 'draft')->countAllResults();

        // Jumlah Kewangan Tuntutan
        $financialTotals = $applyFilter($db->table('new_applications'))
            ->selectSum('total_gross', 'gross')
            ->selectSum('total_claim', 'claim')
            ->selectSum('total_welfare', 'welfare')
            ->get()
            ->getRowArray();

        $totalGrossAmount   = (float) ($financialTotals['gross'] ?? 0);
        $totalClaimAmount   = (float) ($financialTotals['claim'] ?? 0);
        $totalWelfareAmount = (float) ($financialTotals['welfare'] ?? 0);

        // 2. Trend Bulanan Tahun Semasa (Statistik Tuntutan Bulanan)
        $currentYear = date('Y');
        $monthlyQuery = $applyFilter($db->table('new_applications'))
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
            'submitted'    => (int) $applyFilter($db->table('new_applications'))->where('status', 'submitted')->countAllResults(),
            'under_review' => (int) $applyFilter($db->table('new_applications'))->where('status', 'under_review')->countAllResults(),
            'approved'     => $approvedClaims,
            'rejected'     => $rejectedClaims,
        ];

        // 4. Permohonan Tuntutan Terkini (Recent Claims)
        $recentAppBuilder = $db->table('new_applications na')
            ->select('na.*, u.fullname as creator_name')
            ->join('users u', 'u.id = na.submitted_by', 'left');

        if ($isOnlySelf) {
            $recentAppBuilder->where('na.submitted_by', $userId);
        }

        $recentApplications = $recentAppBuilder->orderBy('na.created_at', 'DESC')
            ->limit(6)
            ->get()
            ->getResultArray();

        return view('dashboard', [
            'pageTitle'          => 'Dashboard Tuntutan Pakar',
            'userRole'           => $userRole,
            'isOnlySelf'         => $isOnlySelf,
            'isAdmin'            => !$isOnlySelf,
            'totalClaims'        => $totalClaims,
            'pendingClaims'      => $pendingClaims,
            'approvedClaims'     => $approvedClaims,
            'rejectedClaims'     => $rejectedClaims,
            'draftClaims'        => $draftClaims,
            'totalGrossAmount'   => $totalGrossAmount,
            'totalClaimAmount'   => $totalClaimAmount,
            'totalWelfareAmount' => $totalWelfareAmount,
            'monthlyLabels'      => $monthlyLabels,
            'monthlyCounts'      => $monthlyCounts,
            'monthlyAmounts'     => $monthlyAmounts,
            'statusCounts'       => $statusCounts,
            'recentApplications' => $recentApplications,
        ]);
    }
}

