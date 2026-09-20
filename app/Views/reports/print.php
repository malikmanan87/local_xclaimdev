<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penyata Laporan Tuntutan Pakar - HPUniSZA</title>
    <!-- Bootstrap 5 CSS for base styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            color: #212529;
            background-color: #f8f9fa;
        }

        .print-container {
            max-width: 1100px;
            margin: 20px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .report-header {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .table-report th {
            background-color: #f1f5f9 !important;
            font-size: 8.5pt;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-color: #cbd5e1;
        }

        .table-report td {
            font-size: 8.5pt;
            border-color: #e2e8f0;
            vertical-align: middle;
        }

        .summary-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .signature-box {
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .signature-line {
            border-top: 1px solid #64748b;
            width: 80%;
            margin-top: 60px;
            padding-top: 5px;
        }

        @media print {
            body {
                background: #fff !important;
                font-size: 9.5pt;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            @page {
                size: landscape;
                margin: 12mm 10mm;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Top Action Bar (Screen Only) -->
    <div class="no-print bg-dark text-white py-2 px-3 sticky-top shadow-sm d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-printer-fill text-info fs-5"></i>
            <span class="fw-semibold">Pratonton Cetakan Rasmi Laporan Tuntutan</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm px-3 fw-semibold">
                <i class="bi bi-printer me-1"></i> Cetak Sekarang
            </button>
            <button onclick="window.close()" class="btn btn-outline-light btn-sm px-3">
                Tutup
            </button>
        </div>
    </div>

    <div class="print-container">

        <!-- Kepala Surat Rasmi (Header) -->
        <div class="report-header text-center">
            <h5 class="fw-bold mb-0 text-uppercase letter-spacing-1">HOSPITAL PENGAJAR UNIVERSITI SULTAN ZAINAL ABIDIN (HPUniSZA)</h5>
            <div class="text-secondary small">Universiti Sultan Zainal Abidin, Kampus Gong Badak, 21300 Kuala Nerus, Terengganu</div>
            <h6 class="fw-bold mt-2 text-primary text-uppercase">PENYATA LAPORAN TUNTUTAN PERKHIDMATAN PAKAR PERUBATAN (XCLAIM)</h6>
        </div>

        <!-- Maklumat Parameter & Tarikh Jana -->
        <div class="row g-2 mb-3 small text-secondary">
            <div class="col-6">
                <div><strong>Tarikh Penyata Dijana:</strong> <?= date('d/m/Y h:i A') ?></div>
                <div><strong>Dijana Oleh:</strong> <?= esc(session('name') ?? session('username') ?? 'Pegawai Sistem') ?> (<?= strtoupper(session('role') ?? 'STAF') ?>)</div>
            </div>
            <div class="col-6 text-end">
                <div>
                    <strong>Tempoh:</strong> 
                    <?= !empty($filters['start_date']) ? date('d/m/Y', strtotime($filters['start_date'])) : 'Awal' ?> 
                    hingga 
                    <?= !empty($filters['end_date']) ? date('d/m/Y', strtotime($filters['end_date'])) : 'Kini' ?>
                </div>
                <div>
                    <strong>Jabatan:</strong> <?= !empty($filters['department']) ? esc($filters['department']) : 'Semua Jabatan' ?> 
                    &bull; <strong>Status:</strong> <?= !empty($filters['status']) ? strtoupper($filters['status']) : 'Semua Status' ?>
                </div>
            </div>
        </div>

        <!-- Ringkasan Eksekutif Kewangan -->
        <div class="summary-box">
            <div class="row g-3 text-center">
                <div class="col-3">
                    <div class="text-muted small fw-semibold">BILANGAN PERMOHONAN</div>
                    <div class="fs-5 fw-bold text-dark"><?= number_format($summary['total_records'] ?? 0) ?> Kes</div>
                </div>
                <div class="col-3 border-start">
                    <div class="text-muted small fw-semibold">JUMLAH KASAR (RM)</div>
                    <div class="fs-5 fw-bold text-dark font-monospace"><?= number_format($summary['total_gross'] ?? 0, 2) ?></div>
                </div>
                <div class="col-3 border-start">
                    <div class="text-muted small fw-semibold">TABUNG KEBAJIKAN (RM)</div>
                    <div class="fs-5 fw-bold text-warning font-monospace"><?= number_format($summary['total_welfare'] ?? 0, 2) ?></div>
                </div>
                <div class="col-3 border-start">
                    <div class="text-muted small fw-semibold">JUMLAH BERSIH LAYAK BAYAR (RM)</div>
                    <div class="fs-5 fw-bold text-success font-monospace"><?= number_format($summary['total_claim'] ?? 0, 2) ?></div>
                </div>
            </div>
        </div>

        <!-- Jadual Perincian Rekod -->
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-report align-middle mb-0">
                <thead>
                    <tr class="text-center">
                        <th width="30">#</th>
                        <th width="120">No. Permohonan</th>
                        <th width="80">Tarikh</th>
                        <th>Nama Pakar & Jabatan</th>
                        <th>Pesakit (RN)</th>
                        <th width="85" class="text-end">Kasar (RM)</th>
                        <th width="75" class="text-end">Tabung (RM)</th>
                        <th width="85" class="text-end">Bersih (RM)</th>
                        <th width="75" class="text-center">JPPP</th>
                        <th width="75" class="text-center">Kewangan</th>
                        <th width="90">No. Baucar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reportData) && is_array($reportData)): ?>
                        <?php foreach ($reportData as $idx => $row): ?>
                            <tr>
                                <td class="text-center text-muted"><?= $idx + 1 ?></td>
                                <td class="font-monospace fw-bold text-dark"><?= esc($row['application_no']) ?></td>
                                <td class="text-center"><?= !empty($row['created_at']) ? date('d/m/Y', strtotime($row['created_at'])) : '-' ?></td>
                                <td>
                                    <strong><?= esc($row['specialist_name']) ?></strong>
                                    <div class="text-muted" style="font-size: 7.5pt;"><?= esc($row['department']) ?> (Staf: <?= esc($row['staff_number']) ?>)</div>
                                </td>
                                <td>
                                    <?= esc($row['patient_name']) ?>
                                    <div class="text-muted font-monospace" style="font-size: 7.5pt;">RN: <?= esc($row['patient_rn']) ?></div>
                                </td>
                                <td class="text-end font-monospace"><?= number_format((float)($row['total_gross'] ?? 0), 2) ?></td>
                                <td class="text-end font-monospace"><?= number_format((float)($row['total_welfare'] ?? 0), 2) ?></td>
                                <td class="text-end font-monospace fw-bold"><?= number_format((float)($row['total_claim'] ?? 0), 2) ?></td>
                                <td class="text-center small">
                                    <?= strtoupper($row['jppp_status'] ?? 'PENDING') ?>
                                </td>
                                <td class="text-center small">
                                    <?= strtoupper($row['finance_status'] ?? 'PENDING') ?>
                                </td>
                                <td class="font-monospace small text-center"><?= esc($row['finance_voucher_no'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <!-- Baris Jumlah Keseluruhan -->
                        <tr class="table-light fw-bold">
                            <td colspan="5" class="text-end text-uppercase">Jumlah Keseluruhan:</td>
                            <td class="text-end font-monospace"><?= number_format($summary['total_gross'] ?? 0, 2) ?></td>
                            <td class="text-end font-monospace"><?= number_format($summary['total_welfare'] ?? 0, 2) ?></td>
                            <td class="text-end font-monospace"><?= number_format($summary['total_claim'] ?? 0, 2) ?></td>
                            <td colspan="3"></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">Tiada rekod tuntutan sepadan dijumpai.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Ruangan Tandatangan Perakuan & Kelulusan (3 Pihak) -->
        <div class="signature-box">
            <div class="row text-center">
                <div class="col-4">
                    <div class="signature-line mx-auto"></div>
                    <div class="fw-bold small text-dark">Disediakan Oleh</div>
                    <div class="text-muted small" style="font-size: 8pt;">Pegawai Penyedia Rekod HPUniSZA</div>
                </div>
                <div class="col-4">
                    <div class="signature-line mx-auto"></div>
                    <div class="fw-bold small text-dark">Disemak & Disokong Oleh</div>
                    <div class="text-muted small" style="font-size: 8pt;">Jawatankuasa Penilaian Perkhidmatan Pakar (JPPP)</div>
                </div>
                <div class="col-4">
                    <div class="signature-line mx-auto"></div>
                    <div class="fw-bold small text-dark">Diluluskan Untuk Pembayaran</div>
                    <div class="text-muted small" style="font-size: 8pt;">Bahagian Kewangan HPUniSZA</div>
                </div>
            </div>
        </div>

        <!-- Nota Kaki Dokumen -->
        <div class="mt-4 pt-2 border-top text-muted small d-flex justify-content-between" style="font-size: 7.5pt;">
            <span>Sistem Tuntutan Perkhidmatan Pakar (XClaim) &bull; HPUniSZA</span>
            <span>Dokumen ini dijana secara berkomputer dan sah tanpa tandatangan fizikal sekiranya disahkan melalui portal.</span>
        </div>

    </div>

</body>
</html>
