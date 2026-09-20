<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        // Dapatkan admin role_id
        $adminRole = $this->db->table('roles')->where('name', 'admin')->get()->getRow();
        $adminRoleId = $adminRole ? $adminRole->id : 1;

        // Semak sama ada user dah wujud
        $existing = $this->db->table('users')->where('email', 'malikmanan@unisza.edu.my')->get()->getRow();

        if ($existing) {
            // Kemaskini sahaja
            $this->db->table('users')->where('email', 'malikmanan@unisza.edu.my')->update([
                'role_id'       => $adminRoleId,
                'is_active'     => 1,
                'access_status' => 'approved',
                'updated_at'    => $now,
            ]);
            echo "✔ User updated to admin: malikmanan@unisza.edu.my\n";
        } else {
            // Insert baru
            $this->db->table('users')->insert([
                'fullname'       => 'Malik Manan',
                'username'       => 'malikmanan',
                'email'          => 'malikmanan@unisza.edu.my',
                'phone'          => null,
                'password'       => password_hash('Admin@1234', PASSWORD_DEFAULT),
                'role_id'        => $adminRoleId,
                'is_active'      => 1,
                'access_status'  => 'approved',
                'created_at'     => $now,
                'deleted_at'     => null,
            ]);
            echo "✔ Admin user created: malikmanan@unisza.edu.my\n";
        }

        echo "-----------------------------------\n";
        echo "Email   : malikmanan@unisza.edu.my\n";
        echo "Password: Admin@1234\n";
        echo "Role    : Administrator\n";
        echo "-----------------------------------\n";
        echo "⚠ Sila tukar password selepas login pertama!\n";
    }
}
