<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTimestampsToMmaProcedures extends Migration
{
    public function up(): void
    {
        $fields = [
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'anaesthetist_fee',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'created_at',
            ],
        ];

        $this->forge->addColumn('mma_procedures', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('mma_procedures', ['created_at', 'updated_at']);
    }
}
