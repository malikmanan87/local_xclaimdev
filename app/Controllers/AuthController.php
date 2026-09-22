<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Traits\LoggableTrait;

class AuthController extends BaseController
{
    use LoggableTrait;

    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form', 'url']);
    }

    // ----------------------------------------------------------------
    // GET / (Halaman Utama / Root)
    // ----------------------------------------------------------------
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('dashboard');
        }
        return redirect()->to('login');
    }

    // ----------------------------------------------------------------
    // GET /login (Paparan Halaman Log Masuk)
    // ----------------------------------------------------------------
    public function login()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('dashboard');
        }
        return view('auth/login');
    }

    // ----------------------------------------------------------------
    // GET /pending-approval (Halaman Menunggu Kelulusan)
    // ----------------------------------------------------------------
    public function pendingApproval(): string
    {
        return view('auth/pending_approval');
    }

    // ----------------------------------------------------------------
    // GET /admin-login (Halaman Log Masuk Admin)
    // ----------------------------------------------------------------
    public function adminLogin()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('dashboard');
        }
        return view('auth/admin_login');
    }

    // ----------------------------------------------------------------
    // POST /admin-login (Proses Log Masuk Admin — Local DB)
    // ----------------------------------------------------------------
    public function adminLoginProcess()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Cari user dalam DB tempatan
        $user = $this->userModel->findByEmailOrUsername($email);

        // Semak sekatan akaun (Lockout Policy)
        if ($user) {
            $lockMsg = $this->checkAccountLock($user);
            if ($lockMsg) {
                return redirect()->back()->withInput()->with('error', $lockMsg);
            }
        }

        if (!$user || !password_verify($password, $user['password'])) {
            $failMsg = $this->handleFailedLogin($user, $email);
            return redirect()->back()->withInput()->with('error', $failMsg);
        }

        // Hanya role 'admin' dibenarkan
        if (($user['role_name'] ?? '') !== 'admin') {
            return redirect()->back()->withInput()
                ->with('error', 'Access denied. Admin credentials required.');
        }

        if (empty($user['is_active'])) {
            return redirect()->back()->withInput()
                ->with('error', 'Your account has been deactivated.');
        }

        // Set session (format sama seperti API login)
        session()->set([
            'logged_in'  => true,
            'user_id'    => $user['id'],
            'name'       => $user['fullname'],
            'email'      => $user['email'],
            'staffno'    => $user['username'],
            'position'   => null,
            'department' => null,
            'role'       => 'admin',
            'role_id'    => $user['role_id'],
            'avatar'     => $user['avatar'] ?? null,
        ]);

        $this->resetLoginLock($user['id']);
        $this->logActivity('Admin Login', 'Admin logged in via local credentials: ' . $email);

        return redirect()->to('dashboard')
            ->with('success', 'Welcome, ' . $user['fullname'] . '!');
    }


    public function loginProcess()
    {
        $rules = [
            'email'    => 'required',
            'password' => 'required|min_length[1]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $loginInput = trim($this->request->getPost('email'));
        $password   = $this->request->getPost('password');

        // Jika user memasukkan emel penuh (cth: malikmanan@unisza.edu.my),
        // ambil ID sebelum '@' kerana LDAP Bind UniSZA API memerlukan username sahaja.
        $apiUsername = strpos($loginInput, '@') !== false ? explode('@', $loginInput)[0] : $loginInput;

        // Semak sekatan akaun pengguna dalam rekod tempatan (Lockout Policy)
        $existingTargetUser = $this->userModel->findByEmailOrUsername($loginInput);
        if (!$existingTargetUser && !empty($apiUsername)) {
            $existingTargetUser = $this->userModel->findByEmailOrUsername($apiUsername);
        }

        if ($existingTargetUser) {
            $lockMsg = $this->checkAccountLock($existingTargetUser);
            if ($lockMsg) {
                return redirect()->back()->withInput()->with('error', $lockMsg);
            }
        }

        $client = \Config\Services::curlrequest();
        $apiSuccess = false;
        $apiData = null;

        try {
            $response = $client->request('POST', 'http://10.0.20.172/api/auth', [
                'form_params' => [
                    'username' => $apiUsername,
                    'password' => $password,
                ],
                'http_errors' => false,
                'timeout'     => 10,
            ]);

            $rawBody    = $response->getBody();
            $json       = json_decode($rawBody, true);
            $statusCode = $response->getStatusCode();

            // Debug mode jika parameter ?debug=1 diberikan
            if ($this->request->getGet('debug') === '1') {
                echo '<pre style="background:#1e1e1e;color:#d4d4d4;padding:20px;font-size:13px;font-family:monospace;">';
                echo '<strong style="color:#ce9178;font-size:15px;">══ XCLAIMDEV API DEBUG ══</strong>' . "\n\n";
                echo '<strong style="color:#4ec9b0;">📤 REQUEST SENT:</strong>' . "\n";
                echo '  URL          : POST http://10.0.20.172/api/auth' . "\n";
                echo '  Login Input  : ' . htmlspecialchars($loginInput) . "\n";
                echo '  API Username : ' . htmlspecialchars($apiUsername) . "\n";
                echo '  Password     : [hidden]' . "\n\n";
                echo '<strong style="color:#4ec9b0;">📥 RESPONSE RECEIVED:</strong>' . "\n";
                echo '  HTTP Status  : ' . $statusCode . "\n";
                echo '  Raw Body     : ' . htmlspecialchars($rawBody ?: '(empty)') . "\n\n";
                echo '<strong style="color:#4ec9b0;">🔍 DECODED JSON:</strong>' . "\n";
                print_r($json);
                echo '</pre>';
                exit;
            }

            if ($statusCode === 200 && isset($json['status']) && $json['status'] === 'success') {
                $apiSuccess = true;
                $apiData = $json['data'] ?? [];
            }
        } catch (\Exception $e) {
            log_message('error', 'UniSZA API connection error: ' . $e->getMessage());
        }

        // ================================================================
        // 1. JIKA API BERJAYA (UniSZA Staff / Admin)
        // ================================================================
        if ($apiSuccess && !empty($apiData)) {
            $emel    = $apiData['emel'] ?? ($apiUsername . '@unisza.edu.my');
            $nama    = $apiData['nama'] ?? 'Pengguna UniSZA';
            $staffno = $apiData['nostaff'] ?? null;
            $nokp    = $apiData['nokp'] ?? ($apiData['nopengenalan'] ?? null);
            $jawatan = $apiData['jawatan'] ?? null;
            $lokasi  = $apiData['lokasi'] ?? null;
            $phone   = $apiData['telpejabat'] ?? null;

            $db = \Config\Database::connect();

            // Dapatkan default role_id ('user')
            $defaultRole   = $db->table('roles')->where('name', 'user')->get()->getRow();
            $defaultRoleId = $defaultRole ? (int)$defaultRole->id : 3;

            // Cari pengguna dalam pangkalan data tempatan
            $user = $this->userModel->findByEmailOrUsername($emel);
            if (!$user && !empty($staffno)) {
                $user = $this->userModel->findByEmailOrUsername($staffno);
            }
            if (!$user) {
                $user = $this->userModel->findByEmailOrUsername($apiUsername);
            }

            // ── A. PENGGUNA BARU ─────────────────────────────────────────
            // Simpan data dalam table tanpa kata laluan, access_status = 'pending'
            if (!$user) {
                $this->userModel->insert([
                    'fullname'       => $nama,
                    'username'       => $apiUsername,
                    'email'          => $emel,
                    'phone'          => $phone,
                    'password'       => null,     // Tiada password disimpan (auth melalui API UniSZA)
                    'role_id'        => $defaultRoleId,
                    'is_active'      => 0,        // Tidak aktif sehingga diluluskan
                    'access_status'  => 'pending',// Menunggu kelulusan pentadbir
                ]);

                $this->logActivity('Permohonan Akses', 'Permohonan akaun baru didaftarkan melalui API UniSZA: ' . $emel);

                return redirect()->to('pending-approval')
                    ->with('info', 'Maklumat anda telah disahkan melalui UniSZA. Permohonan akses sistem telah dihantar dan sedang menunggu kelulusan Pentadbir.');
            }

            // ── B. PENGGUNA SEDIA ADA ────────────────────────────────────
            $accessStatus = $user['access_status'] ?? 'approved';

            if ($accessStatus === 'pending') {
                return redirect()->back()->withInput()
                    ->with('warning', 'Permohonan akses anda sedang diproses dan menunggu kelulusan daripada Pentadbir.');
            }

            if ($accessStatus === 'rejected') {
                $note = !empty($user['access_note']) ? ' (Sebab: ' . $user['access_note'] . ')' : '';
                return redirect()->back()->withInput()
                    ->with('error', 'Permohonan akses anda telah ditolak.' . $note . ' Sila hubungi Pentadbir Sistem.');
            }

            if (empty($user['is_active'])) {
                return redirect()->back()->withInput()
                    ->with('error', 'Akaun anda telah dinyahaktifkan. Sila hubungi Pentadbir Sistem.');
            }

            // Kemaskini maklumat terkini pengguna dari API dan tetapkan semula sekatan
            $this->resetLoginLock($user['id']);
            $this->userModel->update($user['id'], [
                'fullname'   => $nama,
                'phone'      => $phone ?: $user['phone'],
            ]);

            // Ambil maklumat peranan pengguna yang ditetapkan dalam dbtable
            $userWithRole = $this->userModel->getUserWithRole($user['id']);

            // Tetapkan sesi log masuk
            session()->set([
                'logged_in'  => true,
                'user_id'    => $user['id'],
                'username'   => $user['username'] ?? $apiUsername,
                'fullname'   => $nama,
                'name'       => $nama,
                'icno'       => $nokp,
                'staffno'    => $staffno ?: $user['username'],
                'position'   => $jawatan,
                'department' => $lokasi,
                'email'      => $emel,
                'extno'      => $phone,
                'role'       => $userWithRole['role_name'] ?? 'user',
                'role_id'    => $userWithRole['role_id'] ?? $defaultRoleId,
                'avatar'     => $user['avatar'] ?? null,
            ]);

            $this->logActivity('Log Masuk', 'Pengguna log masuk melalui API UniSZA: ' . $emel . ' [Peranan: ' . ($userWithRole['role_name'] ?? 'user') . ']');

            return redirect()->to('dashboard')
                ->with('success', 'Selamat kembali, ' . $nama . '!');
        }

        // ================================================================
        // 2. FALLBACK KELAYAKAN TEMPATAN (cth: Akaun Ujian / Super Admin)
        // ================================================================
        $localUser = $this->userModel->findByEmailOrUsername($loginInput);
        if ($localUser && !empty($localUser['password']) && password_verify($password, $localUser['password'])) {
            if (empty($localUser['is_active'])) {
                return redirect()->back()->withInput()
                    ->with('error', 'Akaun anda telah dinyahaktifkan.');
            }
            if (($localUser['access_status'] ?? 'approved') === 'pending') {
                return redirect()->back()->withInput()
                    ->with('warning', 'Permohonan akses anda sedang diproses dan menunggu kelulusan Pentadbir.');
            }
            if (($localUser['access_status'] ?? 'approved') === 'rejected') {
                return redirect()->back()->withInput()
                    ->with('error', 'Permohonan akses anda telah ditolak.');
            }

            $userWithRole = $this->userModel->getUserWithRole($localUser['id']);

            session()->set([
                'logged_in'  => true,
                'user_id'    => $localUser['id'],
                'name'       => $localUser['fullname'],
                'icno'       => null,
                'staffno'    => $localUser['username'],
                'position'   => null,
                'department' => null,
                'email'      => $localUser['email'],
                'extno'      => $localUser['phone'] ?? null,
                'role'       => $userWithRole['role_name'] ?? 'user',
                'role_id'    => $userWithRole['role_id'],
                'avatar'     => $localUser['avatar'] ?? null,
            ]);

            $this->resetLoginLock($localUser['id']);
            $this->logActivity('Log Masuk Tempatan', 'Log masuk akaun tempatan: ' . $localUser['email']);

            return redirect()->to('dashboard')
                ->with('success', 'Selamat kembali, ' . $localUser['fullname'] . '!');
        }

        // Respons API gagal atau rekod tidak sah (Kendalikan kegagalan log masuk & sekatan)
        $failUser = $existingTargetUser ?? ($localUser ?? null);
        $failMsg  = $this->handleFailedLogin($failUser, $loginInput);

        return redirect()->back()->withInput()->with('error', $failMsg);
    }

    // ----------------------------------------------------------------
    // GET /register (Display Registration Form)
    // ----------------------------------------------------------------
    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('dashboard');
        }
        return view('auth/register');
    }

    // ----------------------------------------------------------------
    // POST /register/process (Process Registration)
    // ----------------------------------------------------------------
    public function registerProcess()
    {
        $rules = [
            'fullname'         => 'required|min_length[3]|max_length[100]',
            'username'         => 'required|alpha_dash|min_length[3]|max_length[50]|is_unique[users.username]',
            'email'            => 'required|valid_email|max_length[150]|is_unique[users.email]',
            'password'         => 'required|min_length[6]|max_length[255]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Find default 'user' role ID from roles table
        $db = \Config\Database::connect();
        $roleQuery = $db->table('roles')->where('name', 'user')->get()->getRow();
        $userRoleId = $roleQuery ? $roleQuery->id : 3;

        $data = [
            'fullname'  => $this->request->getPost('fullname'),
            'username'  => strtolower($this->request->getPost('username')),
            'email'     => strtolower($this->request->getPost('email')),
            'phone'     => $this->request->getPost('phone'),
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role_id'   => $userRoleId,
            'is_active' => 1,
        ];

        $db->table('users')->insert($data);

        // Record Activity Log
        $this->logActivity('Public Registration', 'New guest account registered with username: @' . $data['username']);

        return redirect()->to('login')->with('success', 'Your account has been successfully created! Please log in with your email and password.');
    }

    public function profile()
    {
        return redirect()->to('users/show/' . session('user_id'));
    }

    // ----------------------------------------------------------------
    // Semak session key yang betul (helper)
    // ----------------------------------------------------------------
    public static function isLoggedIn(): bool
    {
        return (bool) session()->get('logged_in');
    }

    // ----------------------------------------------------------------
    // GET /logout (Logout)
    // ----------------------------------------------------------------
    public function logout()
    {
        if (session()->get('logged_in')) {
            $this->logActivity('User Logout', 'User logged out of the system.');
        }
        session()->destroy();
        return redirect()->to('login');
    }

    // ----------------------------------------------------------------
    // KAEDAH KESELAMATAN SEKATAN AKAUN (ACCOUNT LOCKOUT & AUTO-RELEASE)
    // ----------------------------------------------------------------

    /**
     * Semak sama ada akaun pengguna sedang disekat atau auto-release jika tempoh telah tamat.
     */
    protected function checkAccountLock(?array $user): ?string
    {
        if (!$user || empty($user['locked_until'])) {
            return null;
        }

        $now = time();
        $lockedTime = strtotime($user['locked_until']);

        if ($lockedTime > $now) {
            $diff = $lockedTime - $now;
            $minutes = ceil($diff / 60);
            $timeText = ($minutes > 1) ? "{$minutes} minit" : "{$diff} saat";
            return "Akaun anda telah disekat sementara kerana melebihi had kegagalan log masuk. Sila cuba lagi dalam masa {$timeText} (atau hubungi Pentadbir Sistem untuk bantuan segera).";
        }

        // Auto-Release: Tempoh sekatan 5 minit telah luput
        $this->userModel->update($user['id'], [
            'failed_attempts' => 0,
            'locked_until'    => null,
        ]);

        return null;
    }

    /**
     * Kendalikan cubaan log masuk yang gagal dan laksanakan sekatan 5 minit jika capai had.
     */
    protected function handleFailedLogin(?array $user, string $identifier): string
    {
        if (!$user) {
            return 'Maklumat log masuk tidak sah. Sila pastikan Emel/ID Staf dan kata laluan anda adalah betul.';
        }

        $db = \Config\Database::connect();
        $maxAttempts = 5;
        $lockoutSeconds = 300; // Lalai: 5 minit (300 saat)

        try {
            $maxRow = $db->table('settings')->where('key', 'login_attempts')->get()->getRow();
            if ($maxRow && is_numeric($maxRow->value)) {
                $maxAttempts = (int)$maxRow->value;
            }
            $lockRow = $db->table('settings')->where('key', 'lockout_time')->get()->getRow();
            if ($lockRow && is_numeric($lockRow->value)) {
                $lockoutSeconds = (int)$lockRow->value;
            }
        } catch (\Throwable $e) {}

        $newAttempts = (int)($user['failed_attempts'] ?? 0) + 1;

        if ($newAttempts >= $maxAttempts) {
            $lockedUntil = date('Y-m-d H:i:s', time() + $lockoutSeconds);
            $this->userModel->update($user['id'], [
                'failed_attempts' => $newAttempts,
                'locked_until'    => $lockedUntil,
            ]);

            $lockMinutes = ceil($lockoutSeconds / 60);
            $this->logActivity(
                'Akaun Disekat',
                'Akaun "' . ($user['email'] ?? $identifier) . '" disekat selama ' . $lockMinutes . ' minit kerana gagal log masuk sebanyak ' . $newAttempts . ' kali berturut-turut.'
            );

            return "Akaun anda telah disekat sementara selama {$lockMinutes} minit kerana gagal log masuk sebanyak {$maxAttempts} kali berturut-turut. Sila tunggu tempoh ini luput atau hubungi Pentadbir Sistem.";
        }

        $this->userModel->update($user['id'], [
            'failed_attempts' => $newAttempts,
        ]);

        $remaining = $maxAttempts - $newAttempts;
        return "Kata laluan tidak sah. Baki percubaan sebelum akaun disekat: {$remaining} kali.";
    }

    /**
     * Tetapkan semula kaunter kegagalan dan sekatan apabila log masuk berjaya.
     */
    protected function resetLoginLock(int $userId): void
    {
        $this->userModel->update($userId, [
            'failed_attempts' => 0,
            'locked_until'    => null,
            'last_login'      => date('Y-m-d H:i:s'),
        ]);
    }
}
