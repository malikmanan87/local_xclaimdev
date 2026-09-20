<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .admin-card {
            max-width: 420px;
            width: 100%;
            border: none;
            border-radius: 16px;
            background: #1e293b;
            box-shadow: 0 24px 60px rgba(0,0,0,.5);
            border: 1px solid rgba(255,255,255,.06);
        }
        .admin-logo {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #fff;
            margin: 0 auto 16px;
            box-shadow: 0 8px 24px rgba(59,130,246,.4);
        }
        .admin-card h4 { color: #f1f5f9; }
        .admin-card p  { color: #94a3b8; }
        .admin-label   { color: #94a3b8; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
        .admin-input {
            background: #0f172a !important;
            border-color: rgba(255,255,255,.1) !important;
            color: #f1f5f9 !important;
            border-radius: 8px;
        }
        .admin-input::placeholder { color: #475569 !important; }
        .admin-input:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59,130,246,.2) !important;
        }
        .input-group-text {
            background: #0f172a !important;
            border-color: rgba(255,255,255,.1) !important;
            color: #475569 !important;
        }
        .admin-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(239,68,68,.12);
            color: #f87171;
            border: 1px solid rgba(239,68,68,.2);
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 10px;
            letter-spacing: .3px;
        }
        .back-link { color: #64748b; font-size: 12px; transition: color .2s; }
        .back-link:hover { color: #94a3b8; }
        .divider { border-color: rgba(255,255,255,.06); }
    </style>
</head>
<body>

<div class="admin-card p-4 p-md-5">

    <div class="text-center mb-4">
        <div class="admin-logo mb-3">
            <i class="bi bi-shield-fill-check"></i>
        </div>
        <div class="mb-2">
            <span class="admin-badge"><i class="bi bi-lock-fill"></i> Restricted Access</span>
        </div>
        <h4 class="fw-bold mb-1">Admin Portal</h4>
        <p class="small mb-0"><?= APP_NAME ?> — System Administration</p>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger d-flex align-items-center gap-2 py-2 small" role="alert"
             style="background:rgba(239,68,68,.1); border-color:rgba(239,68,68,.2); color:#f87171;">
            <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
            <div><?= session()->getFlashdata('error') ?></div>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert d-flex gap-2 py-2 small"
             style="background:rgba(239,68,68,.1); border-color:rgba(239,68,68,.2); color:#f87171;">
            <i class="bi bi-exclamation-circle-fill flex-shrink-0 mt-1"></i>
            <ul class="mb-0 ps-2">
                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                    <li><?= esc($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin-login') ?>" method="POST" autocomplete="off">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="email" class="admin-label mb-2 d-block">Email Address</label>
            <div class="input-group">
                <span class="input-group-text border-end-0">
                    <i class="bi bi-envelope-fill"></i>
                </span>
                <input type="email" class="form-control admin-input border-start-0"
                       id="email" name="email"
                       value="<?= old('email') ?>"
                       placeholder="admin@hospital.gov.my"
                       required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label for="admin_password" class="admin-label mb-2 d-block">Password</label>
            <div class="input-group">
                <span class="input-group-text border-end-0">
                    <i class="bi bi-lock-fill"></i>
                </span>
                <input type="password" class="form-control admin-input border-start-0"
                       id="admin_password" name="password"
                       placeholder="Enter admin password"
                       required>
                <button type="button" class="input-group-text border-start-0"
                        onclick="togglePwd()" title="Show/Hide Password">
                    <i class="bi bi-eye-fill" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-3"
                style="background:linear-gradient(135deg,#3b82f6,#6366f1); border:none; border-radius:10px;">
            <i class="bi bi-shield-lock-fill me-2"></i> Sign In as Admin
        </button>

        <hr class="divider my-3">

        <div class="text-center">
            <a href="<?= base_url('login') ?>" class="back-link text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Back to Staff Login
            </a>
        </div>
    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd() {
    const input = document.getElementById('admin_password');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash-fill';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye-fill';
    }
}
</script>
</body>
</html>
