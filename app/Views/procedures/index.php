<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- KPI / Metric Cards -->
<div class="row g-3 mb-4">
    <!-- Total Procedures -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-primary fw-semibold small">Jumlah Prosedur</span>
                <div class="rounded-circle bg-primary bg-opacity-10 p-2 text-primary d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-clipboard2-pulse-fill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-0"><?= number_format($stats['total_procedures']) ?></h3>
            <small class="text-muted mt-1">Kod MMA Aktif</small>
        </div>
    </div>

    <!-- Sections & Categories -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-success fw-semibold small">Klasifikasi</span>
                <div class="rounded-circle bg-success bg-opacity-10 p-2 text-success d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-diagram-3-fill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-0"><?= $stats['total_sections'] ?> <span class="fs-6 text-muted fw-normal">Seksyen</span></h3>
            <small class="text-muted mt-1"><?= $stats['total_categories'] ?> Kategori Disusun</small>
        </div>
    </div>

    <!-- Avg Surgeon Fee -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-purple fw-semibold small" style="color: #7c3aed;">Purata Fi Surgeri</span>
                <div class="rounded-circle bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: rgba(124, 58, 237, 0.1); color: #7c3aed;">
                    <i class="bi bi-currency-dollar fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-0">RM <?= number_format($stats['avg_surgeon_fee'], 2) ?></h3>
            <small class="text-muted mt-1">Maks: RM <?= number_format($stats['max_surgeon_fee'], 2) ?></small>
        </div>
    </div>

    <!-- Avg Anaesthetist Fee -->
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 h-100 p-3" style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-warning fw-semibold small" style="color: #d97706;">Purata Fi Bius</span>
                <div class="rounded-circle bg-warning bg-opacity-10 p-2 text-warning d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-capsule-pill fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-0">RM <?= number_format($stats['avg_anaesthetist_fee'], 2) ?></h3>
            <small class="text-muted mt-1">Anaesthetist Fee Rate</small>
        </div>
    </div>
</div>

<!-- Main Data Card -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h5 class="card-title fw-bold mb-0 text-dark">
                <i class="bi bi-clipboard2-pulse-fill text-primary me-2"></i>Senarai Prosedur MMA
            </h5>
            <small class="text-muted">Jadual Kadar Fi Mengikut Buku Panduan MMA (Malaysian Medical Association)</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="<?= base_url('procedures/create') ?>" class="btn btn-primary btn-sm px-3 shadow-sm rounded-2">
                <i class="bi bi-plus-circle-fill me-1"></i> Tambah Prosedur Baharu
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="card-body bg-light border-bottom py-2.5">
        <div class="row g-2 align-items-center">
            <div class="col-md-4 col-sm-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-funnel"></i> Seksyen</span>
                    <select id="filterSection" class="form-select form-select-sm">
                        <option value="">Semua Seksyen</option>
                        <?php foreach ($sections as $sec): ?>
                            <option value="<?= esc($sec) ?>"><?= esc($sec) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4 col-sm-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-tag"></i> Kategori</span>
                    <select id="filterCategory" class="form-select form-select-sm">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= esc($cat) ?>"><?= esc($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <button type="button" id="btnResetFilter" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Set Semula Penapis
                </button>
            </div>
        </div>
    </div>

    <!-- DataTable Container -->
    <div class="card-body p-3">
        <div class="table-responsive">
            <table id="proceduresTable" class="table table-hover align-middle w-100" style="font-size: 0.875rem;">
                <thead class="table-light text-secondary">
                    <tr>
                        <th width="45" class="text-center">#</th>
                        <th width="110">Kod MMA</th>
                        <th>Nama Prosedur</th>
                        <th width="180">Seksyen</th>
                        <th width="150">Kategori</th>
                        <th width="130" class="text-end">Fi Surgeri (RM)</th>
                        <th width="130" class="text-end">Fi Bius (RM)</th>
                        <th width="110" class="text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($procedures)): ?>
                        <?php foreach ($procedures as $idx => $row): ?>
                            <tr>
                                <td class="text-center text-muted fw-medium"><?= $idx + 1 ?></td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 font-monospace px-2 py-1 fs-7">
                                        <?= esc($row['code']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($row['name']) ?></div>
                                </td>
                                <td>
                                    <span class="text-secondary small">
                                        <i class="bi bi-folder2 text-muted me-1"></i><?= esc($row['section'] ?: 'General Section') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border small fw-normal">
                                        <?= esc($row['category'] ?: 'Uncategorized') ?>
                                    </span>
                                </td>
                                <td class="text-end font-monospace fw-bold text-success">
                                    RM <?= number_format((float)$row['surgeon_fee'], 2) ?>
                                </td>
                                <td class="text-end font-monospace fw-bold text-info">
                                    RM <?= number_format((float)$row['anaesthetist_fee'], 2) ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?= base_url('procedures/edit/' . $row['id']) ?>" 
                                           class="btn btn-outline-primary btn-sm py-0.5 px-2" 
                                           title="Kemaskini Prosedur">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-outline-danger btn-sm py-0.5 px-2" 
                                                title="Padam Prosedur" 
                                                onclick="confirmDelete(<?= $row['id'] ?>, '<?= esc($row['code'], 'js') ?>', '<?= esc($row['name'], 'js') ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = $('#proceduresTable').DataTable({
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        order: [[1, 'asc']], // Susun mengikut Kod MMA
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Cari kod atau nama prosedur...",
            lengthMenu: "Papar _MENU_ rekod",
            info: "Memaparkan _START_ hingga _END_ daripada _TOTAL_ prosedur",
            infoEmpty: "Tiada rekod dijumpai",
            zeroRecords: "Tiada prosedur sepadan dijumpai",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: '<i class="bi bi-chevron-right"></i>',
                previous: '<i class="bi bi-chevron-left"></i>'
            }
        },
        columnDefs: [
            { orderable: false, targets: [0, 7] }
        ]
    });

    // Penapis Seksyen (Lajur Indeks 3)
    $('#filterSection').on('change', function () {
        const val = this.value;
        table.column(3).search(val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '', true, false).draw();
    });

    // Penapis Kategori (Lajur Indeks 4)
    $('#filterCategory').on('change', function () {
        const val = this.value;
        table.column(4).search(val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '', true, false).draw();
    });

    // Butang Reset Filter
    $('#btnResetFilter').on('click', function () {
        $('#filterSection').val('');
        $('#filterCategory').val('');
        table.column(3).search('').column(4).search('').search('').draw();
    });
});

// Fungsi Pengesahan Padam dengan SweetAlert2
function confirmDelete(id, code, name) {
    Swal.fire({
        title: 'Padam Prosedur?',
        html: `Adakah anda pasti ingin memadam prosedur <strong>[${code}] ${name}</strong>?<br><small class="text-danger">Tindakan ini tidak boleh diundur!</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="bi bi-trash me-1"></i> Ya, Padam',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= base_url('procedures/delete/') ?>/' + id;
        }
    });
}
</script>

<?= $this->endSection() ?>
