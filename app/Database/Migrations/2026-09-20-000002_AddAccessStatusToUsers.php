<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAccessStatusToUsers extends Migration
{
    public function up(): void
    {
        // Tambah kolum access_status selepas kolum is_active
        $this->forge->addColumn('users', [
            'access_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'approved',       // Pengguna sedia ada = approved
                'null'       => false,
                'after'      => 'is_active',
            ],
            'access_note'   => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'access_status',
            ],
            'access_reviewed_by' => [
                'type'       => 'INT',
                'null'       => true,
                'after'      => 'access_note',
            ],
            'access_reviewed_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'access_reviewed_by',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', ['access_status', 'access_note', 'access_reviewed_by', 'access_reviewed_at']);
    }
}
