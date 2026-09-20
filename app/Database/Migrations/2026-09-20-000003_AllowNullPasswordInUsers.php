<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AllowNullPasswordInUsers extends Migration
{
    public function up(): void
    {
        // Benarkan password NULL — staff login guna API, tiada password tempatan
        $this->forge->modifyColumn('users', [
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->modifyColumn('users', [
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
        ]);
    }
}
