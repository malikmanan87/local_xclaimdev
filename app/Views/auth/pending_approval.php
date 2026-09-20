<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Pending | <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/app.css') ?>" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0f4ff 0%, #fafafa 100%);
            padding: 20px;
        }
        .pending-card {
            max-width: 520px;
            width: 100%;
            border: none;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 8px 40px rgba(0,0,0,.08);
        }
        .pending-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #fff3cd, #ffe69c);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            color: #f59e0b;
            margin: 0 auto 20px;
        }
        .status-steps {
            display: flex;
            justify-content: center;
            gap: 0;
            margin: 24px 0;
        }
        .status-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
        }
        .status-step::before {
            content: '';
            position: absolute;
            top: 16px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #dee2e6;
            z-index: 0;
        }
        .status-step:last-child::before { display: none; }
        .step-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #dee2e6;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #adb5bd;
            z-index: 1;
            position: relative;
        }
        .step-dot.done { border-color: #198754; background: #198754; color: #fff; }
        .step-dot.active { border-color: #f59e0b; background: #fff3cd; color: #d97706; }
        .step-text { font-size: 11px; color: #adb5bd; margin-top: 6px; text-align: center; }
        .step-text.active { color: #d97706; font-weight: 600; }
        .step-text.done { color: #198754; font-weight: 600; }
    </style>
</head>
<body>

<div class="pending-card p-4 p-md-5 text-center">

    <div class="pending-icon">
        <i class="bi bi-hourglass-split"></i>
    </div>

    <h4 class="fw-bold text-dark mb-2">Access Request Submitted</h4>
    <p class="text-muted">
        Your request to access <strong><?= APP_NAME ?></strong> has been successfully submitted
        and is currently <strong class="text-warning">pending admin approval</strong>.
    </p>

    <?php if (session()->getFlashdata('info')): ?>
    <div class="alert alert-info d-flex align-items-center gap-2 py-2 small text-start" role="alert">
        <i class="bi bi-info-circle-fill flex-shrink-0"></i>
        <div><?= session()->getFlashdata('info') ?></div>
    </div>
    <?php endif; ?>

    <!-- Step Progress -->
    <div class="status-steps">
        <div class="status-step">
            <div class="step-dot done"><i class="bi bi-check"></i></div>
            <div class="step-text done">Submitted</div>
        </div>
        <div class="status-step">
            <div class="step-dot active"><i class="bi bi-clock"></i></div>
            <div class="step-text active">Under Review</div>
        </div>
        <div class="status-step">
            <div class="step-dot">3</div>
            <div class="step-text">Decision</div>
        </div>
        <div class="status-step">
            <div class="step-dot">4</div>
            <div class="step-text">Access Granted</div>
        </div>
    </div>

    <div class="bg-light rounded-3 p-3 mb-4 text-start">
        <div class="small text-muted mb-1"><i class="bi bi-info-circle me-1"></i>What happens next?</div>
        <ul class="small text-secondary mb-0 ps-3">
            <li>Admin will review your request</li>
            <li>You will be assigned an appropriate access role</li>
            <li>Log in again after approval to access the system</li>
        </ul>
    </div>

    <a href="<?= base_url('login') ?>" class="btn btn-outline-primary w-100">
        <i class="bi bi-arrow-left me-2"></i>Back to Login
    </a>

</div>

</body>
</html>
