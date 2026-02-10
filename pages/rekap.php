<?php
$page_title = 'Rekap Absensi';
require_once __DIR__ . '/../includes/header.php';

$classes = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas");

$filter_kelas = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : '';
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('Y-m');

$rekap = [];
if ($filter_kelas) {
    $bulan_start = $filter_bulan . '-01';
    $bulan_end = date('Y-m-t', strtotime($bulan_start));

    $query = "
        SELECT s.id, s.nis, s.nama_lengkap,
            SUM(CASE WHEN a.status = 'hadir' THEN 1 ELSE 0 END) as hadir,
            SUM(CASE WHEN a.status = 'izin' THEN 1 ELSE 0 END) as izin,
            SUM(CASE WHEN a.status = 'sakit' THEN 1 ELSE 0 END) as sakit,
            SUM(CASE WHEN a.status = 'alpha' THEN 1 ELSE 0 END) as alpha,
            COUNT(a.id) as total
        FROM siswa s
        LEFT JOIN absensi a ON s.id = a.siswa_id AND a.tanggal BETWEEN '$bulan_start' AND '$bulan_end'
        WHERE s.kelas_id = $filter_kelas AND s.status = 'aktif'
        GROUP BY s.id
        ORDER BY s.nama_lengkap
    ";
    $rekap = mysqli_query($conn, $query);
}
?>

<div class="table-card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-filter me-2 text-primary"></i>Filter Rekap</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="rekap.php">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Kelas <span class="text-danger">*</span></label>
                    <select class="form-select" name="kelas_id" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php while ($k = mysqli_fetch_assoc($classes)): ?>
                        <option value="<?= $k['id'] ?>" <?= $filter_kelas == $k['id'] ? 'selected' : '' ?>><?= $k['nama_kelas'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Bulan</label>
                    <input type="month" class="form-control" name="bulan" value="<?= $filter_bulan ?>">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary-gradient w-100">
                        <i class="fas fa-search me-1"></i>Tampilkan Rekap
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($filter_kelas && $rekap): ?>
<div class="table-card">
    <div class="card-header">
        <h5><i class="fas fa-file-alt me-2 text-primary"></i>Rekap Absensi - <?= date('F Y', strtotime($filter_bulan . '-01')) ?></h5>
        <button class="btn btn-success btn-sm" onclick="window.print()">
            <i class="fas fa-print me-1"></i>Cetak
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="table-light">
                    <tr class="text-center">
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th class="text-success">Hadir</th>
                        <th class="text-warning">Izin</th>
                        <th class="text-info">Sakit</th>
                        <th class="text-danger">Alpha</th>
                        <th>Total</th>
                        <th>Persentase Hadir</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($rekap)): 
                        $persen = $row['total'] > 0 ? round(($row['hadir'] / $row['total']) * 100, 1) : 0;
                    ?>
                    <tr class="text-center">
                        <td><?= $no++ ?></td>
                        <td><?= $row['nis'] ?></td>
                        <td class="text-start"><?= $row['nama_lengkap'] ?></td>
                        <td><span class="badge badge-hadir px-3 py-2"><?= $row['hadir'] ?></span></td>
                        <td><span class="badge badge-izin px-3 py-2"><?= $row['izin'] ?></span></td>
                        <td><span class="badge badge-sakit px-3 py-2"><?= $row['sakit'] ?></span></td>
                        <td><span class="badge badge-alpha px-3 py-2"><?= $row['alpha'] ?></span></td>
                        <td><strong><?= $row['total'] ?></strong></td>
                        <td>
                            <div class="progress" style="height:20px; border-radius:10px;">
                                <div class="progress-bar <?= $persen >= 75 ? 'bg-success' : ($persen >= 50 ? 'bg-warning' : 'bg-danger') ?>" 
                                     style="width:<?= $persen ?>%"><?= $persen ?>%</div>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php elseif (!$filter_kelas): ?>
<div class="table-card">
    <div class="card-body text-center py-5">
        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
        <p class="text-muted">Pilih kelas dan bulan untuk melihat rekap absensi</p>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
