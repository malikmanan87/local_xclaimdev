<?php

namespace App\Models;

use CodeIgniter\Model;

class MmaProcedureModel extends Model
{
    protected $table            = 'mma_procedures';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'section',
        'category',
        'code',
        'name',
        'surgeon_fee',
        'anaesthetist_fee',
    ];

    public function getAllProcedures()
    {
        return $this->orderBy('code', 'ASC')->findAll();
    }
}
