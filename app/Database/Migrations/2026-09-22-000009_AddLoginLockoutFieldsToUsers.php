<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLoginLockoutFieldsToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'failed_attempts' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
                'after'      => 'is_active',
            ],
            'locked_until' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
                'after'      => 'failed_attempts',
            ],
        ];

        // Pastikan kolum belum wujud sebelum ditambah
        if (!$this->db->fieldExists('failed_attempts', 'users')) {
            $this->forge->addColumn('users', ['failed_attempts' => $fields['failed_attempts']]);
        }

        if (!$this->db->fieldExists('locked_until', 'users')) {
            $this->forge->addColumn('users', ['locked_until' => $fields['locked_until']]);
        }

        // Tambah lockout_time ke dalam jadual settings jika belum ada
        if ($this->db->tableExists('settings')) {
            $existing = $this->db->table('settings')->where('key', 'lockout_time')->countAllResults();
            if ($existing === 0) {
                $this->db->table('settings')->insert([
                    'key'        => 'lockout_time',
                    'value'      => '300', // 300 saat = 5 minit
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('locked_until', 'users')) {
            $this->forge->dropColumn('users', 'locked_until');
        }

        if ($this->db->fieldExists('failed_attempts', 'users')) {
            $this->forge->dropColumn('users', 'failed_attempts');
        }

        if ($this->db->tableExists('settings')) {
            $this->db->table('settings')->where('key', 'lockout_time')->delete();
        }
    }
}
