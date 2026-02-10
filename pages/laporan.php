<?php
$page_title = 'Laporan Absensi';
require_once __DIR__ . '/../includes/header.php';

$classes = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas");

// Filter
$filter_kelas = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : '';
$filter_dari = isset($_GET['dari']) ? $_GET['dari'] : date('Y-m-01');
$filter_sampai = isset($_GET['sampai']) ? $_GET['sampai'] : date('Y-m-d');

$where = "WHERE a.tanggal BETWEEN '$filter_dari' AND '$filter_sampai'";
if ($filter_kelas) {
    $where .= " AND a.kelas_id = $filter_kelas";
}

$attendance_data = mysqli_query($conn, "
    SELECT a.*, s.nis, s.nama_lengkap, k.nama_kelas, m.nama_mapel
    FROM absensi a
    JOIN siswa s ON a.siswa_id = s.id
    JOIN kelas k ON a.kelas_id = k.id
    LEFT JOIN mata_pelajaran m ON a.mapel_id = m.id
    $where
    ORDER BY a.tanggal DESC, a.jam_masuk DESC
");
?>

<div class="table-card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-filter me-2 text-primary"></i>Filter Laporan</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="laporan.php">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Kelas</label>
                    <select class="form-select" name="kelas_id">
                        <option value="">-- Semua Kelas --</option>
                        <?php while ($k = mysqli_fetch_assoc($classes)): ?>
                        <option value="<?= $k['id'] ?>" <?= $filter_kelas == $k['id'] ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Dari Tanggal</label>
                    <input type="date" class="form-control" name="dari" value="<?= $filter_dari ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-bold">Sampai Tanggal</label>
                    <input type="date" class="form-control" name="sampai" value="<?= $filter_sampai ?>">
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary-gradient w-100">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="table-card">
    <div class="card-header">
        <h5><i class="fas fa-chart-bar me-2 text-primary"></i>Data Laporan Absensi</h5>
        <button class="btn btn-success btn-sm" onclick="window.print()">
            <i class="fas fa-print me-1"></i>Cetak
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Mapel</th>
                        <th>Jam Masuk</th>
                        <th>Status</th>
                        <th>Metode</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($attendance_data)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                        <td><?= $row['nis'] ?></td>
                        <td><?= $row['nama_lengkap'] ?></td>
                        <td><?= $row['nama_kelas'] ?></td>
                        <td><?= $row['nama_mapel'] ?: '-' ?></td>
                        <td><?= $row['jam_masuk'] ?: '-' ?></td>
                        <td>
                            <span class="badge badge-<?= $row['status'] ?> px-3 py-2 rounded-pill">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['metode_absen'] === 'face_recognition'): ?>
                                <span class="text-primary"><i class="fas fa-camera me-1"></i>Face ID</span>
                            <?php else: ?>
                                <span class="text-secondary"><i class="fas fa-pen me-1"></i>Manual</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
