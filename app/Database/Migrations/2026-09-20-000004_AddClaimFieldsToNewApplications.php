<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddClaimFieldsToNewApplications extends Migration
{
    public function up(): void
    {
        $fields = [
            'patient_rn' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'position',
            ],
            'patient_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'patient_rn',
            ],
            'patient_ic' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'patient_name',
            ],
            'visit_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'patient_ic',
            ],
            'total_gross' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'after'      => 'status',
            ],
            'total_claim' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'after'      => 'total_gross',
            ],
            'total_welfare' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'after'      => 'total_claim',
            ],
            'procedures_data' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'total_welfare',
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'procedures_data',
            ],
        ];

        $this->forge->addColumn('new_applications', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('new_applications', [
            'patient_rn',
            'patient_name',
            'patient_ic',
            'visit_id',
            'total_gross',
            'total_claim',
            'total_welfare',
            'procedures_data',
            'remarks',
        ]);
    }
}
