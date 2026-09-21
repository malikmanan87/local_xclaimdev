<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateSystemRolesToExecutiveStructure extends Migration
{
    public function up(): void
    {
        $db  = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // 6 Peranan rasmi sistem XClaim
        $targetRoles = [
            'admin' => [
                'display_name' => 'Administrator',
                'description'  => 'Pentadbir sistem dengan akses penuh kepada semua modul, pengguna, dan konfigurasi.',
            ],
            'user' => [
                'display_name' => 'User',
                'description'  => 'Pakar perubatan / pemohon tuntutan perkhidmatan hospital.',
            ],
            'pegawai_penyemak_pe' => [
                'display_name' => 'Pegawai Penyemak PE',
                'description'  => 'Pegawai penyemak bagi permohonan tuntutan Perkhidmatan Eksekutif (PE).',
            ],
            'pegawai_perkhidmatan_pe' => [
                'display_name' => 'Pegawai Perkhidmatan PE',
                'description'  => 'Pegawai pengurusan perkhidmatan Perkhidmatan Eksekutif (PE).',
            ],
            'ketua_j3p' => [
                'display_name' => 'Ketua J3P',
                'description'  => 'Ketua Jawatankuasa Penilaian Perkhidmatan Pakar (J3P).',
            ],
            'pengarah' => [
                'display_name' => 'Pengarah',
                'description'  => 'Pengarah Hospital Pengajar UniSZA.',
            ],
        ];

        // 1. Kemaskini role lama 'manager' -> 'pegawai_penyemak_pe'
        $managerRole = $db->table('roles')->where('name', 'manager')->get()->getRow();
        if ($managerRole) {
            $db->table('roles')->where('id', $managerRole->id)->update([
                'name'         => 'pegawai_penyemak_pe',
                'display_name' => $targetRoles['pegawai_penyemak_pe']['display_name'],
                'description'  => $targetRoles['pegawai_penyemak_pe']['description'],
                'updated_at'   => $now,
            ]);
        }

        // 2. Kemaskini role lama 'jppp' -> 'ketua_j3p'
        $jpppRole = $db->table('roles')->where('name', 'jppp')->get()->getRow();
        if ($jpppRole) {
            $db->table('roles')->where('id', $jpppRole->id)->update([
                'name'         => 'ketua_j3p',
                'display_name' => $targetRoles['ketua_j3p']['display_name'],
                'description'  => $targetRoles['ketua_j3p']['description'],
                'updated_at'   => $now,
            ]);
        }

        // 3. Kemaskini role lama 'kewangan' -> 'pengarah'
        $kewanganRole = $db->table('roles')->where('name', 'kewangan')->get()->getRow();
        if ($kewanganRole) {
            $db->table('roles')->where('id', $kewanganRole->id)->update([
                'name'         => 'pengarah',
                'display_name' => $targetRoles['pengarah']['display_name'],
                'description'  => $targetRoles['pengarah']['description'],
                'updated_at'   => $now,
            ]);
        }

        // 4. Pastikan setiap role daripada targetRoles wujud dalam pangkalan data
        foreach ($targetRoles as $name => $info) {
            $exists = $db->table('roles')->where('name', $name)->countAllResults();
            if ($exists === 0) {
                $db->table('roles')->insert([
                    'name'         => $name,
                    'display_name' => $info['display_name'],
                    'description'  => $info['description'],
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            } else {
                $db->table('roles')->where('name', $name)->update([
                    'display_name' => $info['display_name'],
                    'description'  => $info['description'],
                    'updated_at'   => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // No-op to prevent foreign key issues
    }
}
