<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateNewApplicationsForOfficialFormAndWorkflow extends Migration
{
    public function up(): void
    {
        $db = \Config\Database::connect();

        // 1. Tambah maklumat Bahagian A & Pengesahan Bahagian C
        $fieldsA = [
            'staff_ic' => [
                'type'       => 'VARCHAR',
                'constraint' => 25,
                'null'       => true,
                'after'      => 'specialist_name',
            ],
            'grade' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'position',
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'after'      => 'email',
            ],
            'claim_month' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'phone',
            ],
            'claim_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'claim_month',
            ],
            'user_declaration' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'remarks',
            ],
            'user_declared_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'user_declaration',
            ],
        ];

        $this->forge->addColumn('new_applications', $fieldsA);

        // 2. Tambah medan kelulusan 4-peringkat (Bahagian D & E)
        $fieldsWorkflow = [
            // D1: Pegawai Penyemak PE
            'penyemak_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'verified', 'rejected'],
                'default'    => 'pending',
                'after'      => 'status',
            ],
            'penyemak_verified_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'penyemak_status',
            ],
            'penyemak_verified_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'penyemak_verified_by',
            ],
            'penyemak_remarks' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'penyemak_verified_at',
            ],

            // D2: Pegawai Perkhidmatan PE
            'perkhidmatan_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'verified', 'rejected'],
                'default'    => 'pending',
                'after'      => 'penyemak_remarks',
            ],
            'perkhidmatan_verified_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'perkhidmatan_status',
            ],
            'perkhidmatan_verified_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'perkhidmatan_verified_by',
            ],
            'perkhidmatan_remarks' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'perkhidmatan_verified_at',
            ],

            // E1: Ketua J3P
            'j3p_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'verified', 'rejected'],
                'default'    => 'pending',
                'after'      => 'perkhidmatan_remarks',
            ],
            'j3p_verified_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'j3p_status',
            ],
            'j3p_verified_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'j3p_verified_by',
            ],
            'j3p_remarks' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'j3p_verified_at',
            ],

            // E2: Pengarah
            'pengarah_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
                'after'      => 'j3p_remarks',
            ],
            'pengarah_verified_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'pengarah_status',
            ],
            'pengarah_verified_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'pengarah_verified_by',
            ],
            'pengarah_remarks' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'pengarah_verified_at',
            ],
        ];

        $this->forge->addColumn('new_applications', $fieldsWorkflow);

        // 3. Selaraskan data sedia ada (Sync existing rows)
        $db->query("
            UPDATE new_applications 
            SET 
                claim_month = COALESCE(claim_month, DATE_FORMAT(COALESCE(submitted_at, created_at), '%m')),
                claim_year  = COALESCE(claim_year, DATE_FORMAT(COALESCE(submitted_at, created_at), '%Y')),
                user_declared_at = COALESCE(user_declared_at, submitted_at, created_at),
                j3p_status = CASE 
                    WHEN jppp_status = 'approved' THEN 'verified'
                    WHEN jppp_status = 'rejected' THEN 'rejected'
                    ELSE 'pending'
                END,
                j3p_verified_by = jppp_verified_by,
                j3p_verified_at = jppp_verified_at,
                j3p_remarks     = jppp_remarks,
                pengarah_status = CASE 
                    WHEN finance_status = 'approved' THEN 'approved'
                    WHEN finance_status = 'rejected' THEN 'rejected'
                    ELSE 'pending'
                END,
                pengarah_verified_by = finance_verified_by,
                pengarah_verified_at = finance_verified_at,
                pengarah_remarks     = finance_remarks,
                penyemak_status = CASE 
                    WHEN jppp_status = 'approved' THEN 'verified' 
                    ELSE 'pending' 
                END,
                perkhidmatan_status = CASE 
                    WHEN jppp_status = 'approved' THEN 'verified' 
                    ELSE 'pending' 
                END
            WHERE id > 0
        ");
    }

    public function down(): void
    {
        $this->forge->dropColumn('new_applications', [
            'staff_ic',
            'grade',
            'phone',
            'claim_month',
            'claim_year',
            'user_declaration',
            'user_declared_at',
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
        ]);
    }
}
