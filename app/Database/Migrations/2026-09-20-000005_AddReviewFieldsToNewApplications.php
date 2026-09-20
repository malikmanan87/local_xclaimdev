<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReviewFieldsToNewApplications extends Migration
{
    public function up(): void
    {
        $fields = [
            'jppp_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
                'after'      => 'status',
            ],
            'jppp_verified_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
                'null'       => true,
                'after'      => 'jppp_status',
            ],
            'jppp_verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'jppp_verified_by',
            ],
            'jppp_remarks' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'jppp_verified_at',
            ],
            'finance_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
                'after'      => 'jppp_remarks',
            ],
            'finance_verified_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
                'null'       => true,
                'after'      => 'finance_status',
            ],
            'finance_verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'finance_verified_by',
            ],
            'finance_remarks' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'finance_verified_at',
            ],
            'finance_voucher_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 60,
                'null'       => true,
                'after'      => 'finance_remarks',
            ],
        ];

        $this->forge->addColumn('new_applications', $fields);

        // Add review roles to 'roles' table if not exists
        $db = \Config\Database::connect();
        $existingJppp = $db->table('roles')->where('name', 'jppp')->countAllResults();
        if ($existingJppp === 0) {
            $db->table('roles')->insert([
                'name'         => 'jppp',
                'display_name' => 'Jawatankuasa JPPP',
                'description'  => 'Akses semakan dan pengesahan permohonan tuntutan pakar.',
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
        }

        $existingFinance = $db->table('roles')->where('name', 'kewangan')->countAllResults();
        if ($existingFinance === 0) {
            $db->table('roles')->insert([
                'name'         => 'kewangan',
                'display_name' => 'Pegawai Kewangan',
                'description'  => 'Akses semakan kewangan dan kelulusan bayaran tuntutan pakar.',
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down(): void
    {
        $this->forge->dropColumn('new_applications', [
            'jppp_status',
            'jppp_verified_by',
            'jppp_verified_at',
            'jppp_remarks',
            'finance_status',
            'finance_verified_by',
            'finance_verified_at',
            'finance_remarks',
            'finance_voucher_no',
        ]);
    }
}
