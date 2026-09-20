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
    // Get all with creator info
    // ---------------------------------------------------------------
    public function getAll(): array
    {
        return $this->select('new_applications.*, users.fullname AS creator_name')
                    ->join('users', 'users.id = new_applications.submitted_by', 'left')
                    ->orderBy('new_applications.created_at', 'DESC')
                    ->findAll();
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
