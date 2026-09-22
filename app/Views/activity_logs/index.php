<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card-panel">
    <div class="card-panel-header py-3 d-flex flex-wrap justify-content-between align-items-center gap-2 border-bottom">
        <h5 class="card-panel-title mb-0">
            <i class="bi bi-journal-text me-2 text-primary"></i>Log Aktiviti Sistem (Audit Trail)
        </h5>
        <span class="badge bg-light text-dark border px-3 py-1.5 shadow-sm rounded-pill small">
            <i class="bi bi-clock-history text-primary me-1"></i> <span id="totalLogCount"><?= number_format($totalLogs ?? 0) ?></span> Rekod Log
        </span>
    </div>
    <div class="card-panel-body p-3">
        <div class="table-responsive">
            <table id="logsTable" class="table table-hover table-align-middle w-100">
                <thead class="table-light text-secondary">
                    <tr>
                        <th width="50" class="text-center">#</th>
                        <th width="150">Tarikh & Masa</th>
                        <th width="180">Pengguna</th>
                        <th width="150" class="text-center">Tindakan</th>
                        <th>Butiran / Keterangan</th>
                        <th width="140" class="text-center">IP & Peranti</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Baris dimuatkan secara dinamik melalui AJAX Server-Side Processing -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#logsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('activity-logs/data') ?>',
            type: 'GET'
        },
        language: {
            processing: '<div class="d-flex justify-content-center align-items-center py-2"><div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div><span class="text-muted small">Memuatkan data log...</span></div>',
            search: "Cari:",
            searchPlaceholder: "Cari sebarang log...",
            lengthMenu: "Papar _MENU_ rekod",
            info: "Memaparkan _START_ hingga _END_ daripada _TOTAL_ rekod",
            infoEmpty: "Tiada rekod log",
            infoFiltered: "(ditapis daripada _MAX_ jumlah rekod)",
            paginate: {
                previous: "Sebelumnya",
                next: "Seterusnya"
            },
            emptyTable: "Tiada log aktiviti direkodkan dalam sistem."
        },
        order: [[1, 'desc']],
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        columnDefs: [
            { orderable: false, targets: [0, 4, 5] },
            { className: "text-center text-muted fw-medium", targets: [0] },
            { className: "text-secondary small", targets: [1, 4] },
            { className: "text-center", targets: [3, 5] }
        ],
        drawCallback: function(settings) {
            if (settings.json && settings.json.recordsTotal !== undefined) {
                $('#totalLogCount').text(Number(settings.json.recordsTotal).toLocaleString());
            }
        }
    });
});
</script>
<?= $this->endSection() ?>