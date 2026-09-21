<?php

namespace App\Models;

use CodeIgniter\Model;

class NewApplicationModel extends Model
{
    protected $table         = 'new_applications';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps  = true;

    protected $allowedFields = [
        'application_no',
        'specialist_name',
        'staff_ic',
        'staff_number',
        'email',
        'phone',
        'department',
        'position',
        'grade',
        'claim_month',
        'claim_year',
        'patient_rn',
        'patient_name',
        'patient_ic',
        'visit_id',
        'status',
        'penyemak_status',
        'penyemak_verified_by',
        'penyemak_verified_at',
        'penyemak_remarks',
        'perkhidmatan_status',
        'perkhidmatan_verified_by',
        'perkhidmatan_verified_at',
        'perkhidmatan_remarks',
        'j3p_status',
        'j3p_verified_by',
        'j3p_verified_at',
        'j3p_remarks',
        'pengarah_status',
        'pengarah_verified_by',
        'pengarah_verified_at',
        'pengarah_remarks',
        'jppp_status',
        'jppp_verified_by',
        'jppp_verified_at',
        'jppp_remarks',
        'finance_status',
        'finance_verified_by',
        'finance_verified_at',
        'finance_remarks',
        'finance_voucher_no',
        'total_gross',
        'total_claim',
        'total_welfare',
        'procedures_data',
        'remarks',
        'user_declaration',
        'user_declared_at',
        'submitted_by',
        'submitted_at',
    ];

    protected $validationRules = [
        'specialist_name' => 'required|min_length[3]|max_length[150]',
        'staff_number'    => 'required|max_length[50]',
        'email'           => 'required|valid_email|max_length[150]',
    ];

    // ---------------------------------------------------------------
    // Get all with creator and reviewer info
    // ---------------------------------------------------------------
    public function getAll(): array
    {
        return $this->select('new_applications.*, 
                              u_sub.fullname AS creator_name,
                              u_penyemak.fullname AS penyemak_reviewer_name,
                              u_perkhidmatan.fullname AS perkhidmatan_reviewer_name,
                              u_j3p.fullname AS j3p_reviewer_name,
                              u_pengarah.fullname AS pengarah_reviewer_name,
                              u_jppp.fullname AS jppp_reviewer_name,
                              u_fin.fullname AS finance_reviewer_name')
                    ->join('users u_sub', 'u_sub.id = new_applications.submitted_by', 'left')
                    ->join('users u_penyemak', 'u_penyemak.id = new_applications.penyemak_verified_by', 'left')
                    ->join('users u_perkhidmatan', 'u_perkhidmatan.id = new_applications.perkhidmatan_verified_by', 'left')
                    ->join('users u_j3p', 'u_j3p.id = new_applications.j3p_verified_by', 'left')
                    ->join('users u_pengarah', 'u_pengarah.id = new_applications.pengarah_verified_by', 'left')
                    ->join('users u_jppp', 'u_jppp.id = new_applications.jppp_verified_by', 'left')
                    ->join('users u_fin', 'u_fin.id = new_applications.finance_verified_by', 'left')
                    ->orderBy('new_applications.created_at', 'DESC')
                    ->findAll();
    }

    // ---------------------------------------------------------------
    // Get single application with reviewers
    // ---------------------------------------------------------------
    public function getWithReviewers(int $id): ?array
    {
        return $this->select('new_applications.*, 
                              u_sub.fullname AS creator_name,
                              u_penyemak.fullname AS penyemak_reviewer_name,
                              u_perkhidmatan.fullname AS perkhidmatan_reviewer_name,
                              u_j3p.fullname AS j3p_reviewer_name,
                              u_pengarah.fullname AS pengarah_reviewer_name,
                              u_jppp.fullname AS jppp_reviewer_name,
                              u_fin.fullname AS finance_reviewer_name')
                    ->join('users u_sub', 'u_sub.id = new_applications.submitted_by', 'left')
                    ->join('users u_penyemak', 'u_penyemak.id = new_applications.penyemak_verified_by', 'left')
                    ->join('users u_perkhidmatan', 'u_perkhidmatan.id = new_applications.perkhidmatan_verified_by', 'left')
                    ->join('users u_j3p', 'u_j3p.id = new_applications.j3p_verified_by', 'left')
                    ->join('users u_pengarah', 'u_pengarah.id = new_applications.pengarah_verified_by', 'left')
                    ->join('users u_jppp', 'u_jppp.id = new_applications.jppp_verified_by', 'left')
                    ->join('users u_fin', 'u_fin.id = new_applications.finance_verified_by', 'left')
                    ->where('new_applications.id', $id)
                    ->first();
    }

    // ---------------------------------------------------------------
    // Pending counts for 4-tier workflow notifications & badges
    // ---------------------------------------------------------------
    public function getPendingPenyemakCount(): int
    {
        return $this->where('status', 'submitted')
                    ->where('penyemak_status', 'pending')
                    ->countAllResults();
    }

    public function getPendingPerkhidmatanCount(): int
    {
        return $this->where('penyemak_status', 'verified')
                    ->where('perkhidmatan_status', 'pending')
                    ->countAllResults();
    }

    public function getPendingJ3pCount(): int
    {
        return $this->where('perkhidmatan_status', 'verified')
                    ->where('j3p_status', 'pending')
                    ->countAllResults();
    }

    public function getPendingPengarahCount(): int
    {
        return $this->where('j3p_status', 'verified')
                    ->where('pengarah_status', 'pending')
                    ->countAllResults();
    }

    // ---------------------------------------------------------------
    // Generate unique application number: APP-YYYYMMDD-XXXX
    // ---------------------------------------------------------------
    public function generateAppNo(): string
    {
        $prefix = 'APP-' . date('Ymd') . '-';
        $last   = $this->like('application_no', $prefix, 'after')
                       ->orderBy('id', 'DESC')
                       ->first();

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last['application_no']);
            $seq   = (int) end($parts) + 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    // ---------------------------------------------------------------
    // Report Helper Methods
    // ---------------------------------------------------------------
    public function getUniqueDepartments(): array
    {
        $rows = $this->select('department')
            ->distinct()
            ->where('department IS NOT NULL')
            ->where('department !=', '')
            ->orderBy('department', 'ASC')
            ->findAll();

        return array_column($rows, 'department');
    }

    public function getUniqueSpecialists(): array
    {
        return $this->select('specialist_name, staff_number')
            ->distinct()
            ->where('specialist_name IS NOT NULL')
            ->where('specialist_name !=', '')
            ->orderBy('specialist_name', 'ASC')
            ->findAll();
    }

    public function getReportData(array $filters = [], ?int $userId = null): array
    {
        $builder = $this->select('new_applications.*, 
                                  u_sub.fullname AS creator_name,
                                  u_jppp.fullname AS jppp_reviewer_name,
                                  u_fin.fullname AS finance_reviewer_name')
                        ->join('users u_sub', 'u_sub.id = new_applications.submitted_by', 'left')
                        ->join('users u_jppp', 'u_jppp.id = new_applications.jppp_verified_by', 'left')
                        ->join('users u_fin', 'u_fin.id = new_applications.finance_verified_by', 'left');

        if ($userId !== null) {
            $builder->where('new_applications.submitted_by', $userId);
        }

        if (!empty($filters['start_date'])) {
            $builder->where('DATE(new_applications.created_at) >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $builder->where('DATE(new_applications.created_at) <=', $filters['end_date']);
        }

        if (!empty($filters['department'])) {
            $builder->where('new_applications.department', $filters['department']);
        }

        if (!empty($filters['status'])) {
            $builder->where('new_applications.status', $filters['status']);
        }

        if (!empty($filters['jppp_status'])) {
            $builder->where('new_applications.jppp_status', $filters['jppp_status']);
        }

        if (!empty($filters['finance_status'])) {
            $builder->where('new_applications.finance_status', $filters['finance_status']);
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $builder->groupStart()
                ->like('new_applications.application_no', $search)
                ->orLike('new_applications.specialist_name', $search)
                ->orLike('new_applications.staff_number', $search)
                ->orLike('new_applications.patient_rn', $search)
                ->orLike('new_applications.patient_name', $search)
                ->orLike('new_applications.finance_voucher_no', $search)
            ->groupEnd();
        }

        $records = $builder->orderBy('new_applications.created_at', 'DESC')->findAll();

        $summary = [
            'total_records'      => count($records),
            'total_gross'        => 0.0,
            'total_welfare'      => 0.0,
            'total_claim'        => 0.0,
            'count_submitted'    => 0,
            'count_under_review' => 0,
            'count_approved'     => 0,
            'count_rejected'     => 0,
            'count_draft'        => 0,
        ];

        foreach ($records as $r) {
            $summary['total_gross']   += (float)($r['total_gross'] ?? 0);
            $summary['total_welfare'] += (float)($r['total_welfare'] ?? 0);
            $summary['total_claim']   += (float)($r['total_claim'] ?? 0);

            $st = $r['status'] ?? 'draft';
            if ($st === 'submitted') {
                $summary['count_submitted']++;
            } elseif ($st === 'under_review') {
                $summary['count_under_review']++;
            } elseif ($st === 'approved') {
                $summary['count_approved']++;
            } elseif ($st === 'rejected') {
                $summary['count_rejected']++;
            } else {
                $summary['count_draft']++;
            }
        }

        return [
            'records' => $records,
            'summary' => $summary,
        ];
    }
}
