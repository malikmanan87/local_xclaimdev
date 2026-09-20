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
        'staff_number',
        'email',
        'department',
        'position',
        'patient_rn',
        'patient_name',
        'patient_ic',
        'visit_id',
        'status',
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
        'submitted_by',
        'submitted_at',
    ];

    protected $validationRules = [
        'specialist_name' => 'required|min_length[3]|max_length[150]',
        'staff_number'    => 'required|max_length[50]',
        'email'           => 'required|valid_email|max_length[150]',
        'department'      => 'required|max_length[100]',
        'position'        => 'required|max_length[100]',
    ];

    // ---------------------------------------------------------------
    // Get all with creator and reviewer info
    // ---------------------------------------------------------------
    public function getAll(): array
    {
        return $this->select('new_applications.*, 
                              u_sub.fullname AS creator_name,
                              u_jppp.fullname AS jppp_reviewer_name,
                              u_fin.fullname AS finance_reviewer_name')
                    ->join('users u_sub', 'u_sub.id = new_applications.submitted_by', 'left')
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
                              u_jppp.fullname AS jppp_reviewer_name,
                              u_fin.fullname AS finance_reviewer_name')
                    ->join('users u_sub', 'u_sub.id = new_applications.submitted_by', 'left')
                    ->join('users u_jppp', 'u_jppp.id = new_applications.jppp_verified_by', 'left')
                    ->join('users u_fin', 'u_fin.id = new_applications.finance_verified_by', 'left')
                    ->where('new_applications.id', $id)
                    ->first();
    }

    // ---------------------------------------------------------------
    // Pending counts for notifications & badges
    // ---------------------------------------------------------------
    public function getPendingJpppCount(): int
    {
        return $this->where('status', 'submitted')
                    ->groupStart()
                        ->where('jppp_status', 'pending')
                        ->orWhere('jppp_status IS NULL', null, false)
                    ->groupEnd()
                    ->countAllResults();
    }

    public function getPendingFinanceCount(): int
    {
        return $this->where('jppp_status', 'approved')
                    ->where('finance_status', 'pending')
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
}
