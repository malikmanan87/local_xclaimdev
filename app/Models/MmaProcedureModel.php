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
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat    = 'datetime';

    public function getAllProcedures()
    {
        return $this->orderBy('code', 'ASC')->findAll();
    }

    public function getDistinctSections(): array
    {
        $rows = $this->select('section')
            ->distinct()
            ->where('section IS NOT NULL')
            ->where('section !=', '')
            ->orderBy('section', 'ASC')
            ->findAll();

        return array_column($rows, 'section');
    }

    public function getDistinctCategories(): array
    {
        $rows = $this->select('category')
            ->distinct()
            ->where('category IS NOT NULL')
            ->where('category !=', '')
            ->orderBy('category', 'ASC')
            ->findAll();

        return array_column($rows, 'category');
    }

    public function getStatistics(): array
    {
        $builder = $this->builder();
        $stat = $builder->select('
            COUNT(id) as total_procedures,
            COUNT(DISTINCT section) as total_sections,
            COUNT(DISTINCT category) as total_categories,
            AVG(surgeon_fee) as avg_surgeon_fee,
            AVG(anaesthetist_fee) as avg_anaesthetist_fee,
            MAX(surgeon_fee) as max_surgeon_fee
        ')->get()->getRowArray();

        return [
            'total_procedures'     => (int)($stat['total_procedures'] ?? 0),
            'total_sections'       => (int)($stat['total_sections'] ?? 0),
            'total_categories'     => (int)($stat['total_categories'] ?? 0),
            'avg_surgeon_fee'      => (float)($stat['avg_surgeon_fee'] ?? 0),
            'avg_anaesthetist_fee' => (float)($stat['avg_anaesthetist_fee'] ?? 0),
            'max_surgeon_fee'      => (float)($stat['max_surgeon_fee'] ?? 0),
        ];
    }
}
