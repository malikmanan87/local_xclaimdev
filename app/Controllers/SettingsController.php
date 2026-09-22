<?php

namespace App\Controllers;

use App\Traits\LoggableTrait;

class SettingsController extends BaseController
{
    use LoggableTrait;

    public function __construct()
    {
        helper(['form', 'url']);
    }

    // ----------------------------------------------------------------
    // GET /settings (Display Settings Form)
    // ----------------------------------------------------------------
    public function index()
    {
        $db = \Config\Database::connect();
        
        $settingsData = $db->table('settings')->get()->getResultArray();
        
        $settings = [];
        foreach ($settingsData as $row) {
            $settings[$row['key']] = $row['value'];
        }

        return view('settings/index', [
            'pageTitle'  => 'System Settings',
            'breadcrumb' => ['Settings'],
            'settings'   => $settings
        ]);
    }

    // ----------------------------------------------------------------
    // POST /settings/update (Process Save Settings with DB Transaction)
    // ----------------------------------------------------------------
    public function update()
    {
        $db = \Config\Database::connect();

        $allowedKeys = [
            'app_name', 'app_tagline', 'company_name', 'timezone',
            'system_email', 'email_protocol', 'smtp_host', 'smtp_port',
            'login_attempts', 'session_timeout'
        ];

        $db->transStart();

        foreach ($allowedKeys as $key) {
            $value = $this->request->getPost($key);
            
            $db->table('settings')
               ->where('key', $key)
               ->update(['value' => $value, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        $maintenanceMode = $this->request->getPost('maintenance_mode') ?? '0';
        $db->table('settings')
           ->where('key', 'maintenance_mode')
           ->update(['value' => $maintenanceMode, 'updated_at' => date('Y-m-d H:i:s')]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to('settings')->with('error', 'Gagal mengemaskini tetapan sistem disebabkan ralat pangkalan data.');
        }

        $this->logActivity('Kemaskini Tetapan', 'Pentadbir mengemaskini konfigurasi dan parameter tetapan sistem.');

        return redirect()->to('settings')->with('success', 'Tetapan sistem telah berjaya disimpan.');
    }
}