<?php
$page_title = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';

// Get statistics
$total_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa WHERE status='aktif'"))['total'];
$total_kelas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kelas"))['total'];
$hadir_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM absensi WHERE tanggal = CURDATE() AND status='hadir'"))['total'];
$alpha_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM absensi WHERE tanggal = CURDATE() AND status='alpha'"))['total'];
$face_registered = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa WHERE face_registered=1"))['total'];

// Get today's attendance summary
$today_attendance = mysqli_query($conn, "
    SELECT s.nama_lengkap, s.nis, k.nama_kelas, a.jam_masuk, a.status, a.metode_absen
    FROM absensi a
    JOIN siswa s ON a.siswa_id = s.id
    JOIN kelas k ON a.kelas_id = k.id
    WHERE a.tanggal = CURDATE()
    ORDER BY a.jam_masuk DESC
    LIMIT 10
");

// Get attendance stats for chart (last 7 days)
$chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $label = date('d/m', strtotime("-$i days"));
    $hadir = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM absensi WHERE tanggal = '$date' AND status='hadir'"))['total'];
    $izin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM absensi WHERE tanggal = '$date' AND status='izin'"))['total'];
    $sakit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM absensi WHERE tanggal = '$date' AND status='sakit'"))['total'];
    $alpha_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM absensi WHERE tanggal = '$date' AND status='alpha'"))['total'];
    $chart_data[] = [
        'label' => $label,
        'hadir' => (int)$hadir,
        'izin' => (int)$izin,
        'sakit' => (int)$sakit,
        'alpha' => (int)$alpha_count
    ];
}
?>

<!-- Welcome Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stat-card" style="background: var(--primary-gradient); color: white;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-1" style="color:white; font-weight:700;">Selamat Datang, <?= $_SESSION['admin_nama'] ?? 'Admin' ?>! 👋</h4>
                    <p style="color:rgba(255,255,255,0.8); margin:0;">
                        <?= date('l, d F Y') ?> | <?= $pengaturan['nama_sekolah'] ?? 'Sekolah' ?>
                    </p>
                </div>
                <div class="d-none d-md-block">
                    <i class="fas fa-chart-line" style="font-size:60px; opacity:0.3;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-primary-gradient">
                <i class="fas fa-user-graduate"></i>
            </div>
            <h3><?= $total_siswa ?></h3>
            <p>Total Siswa Aktif</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-success-gradient">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3><?= $hadir_hari_ini ?></h3>
            <p>Hadir Hari Ini</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-danger-gradient">
                <i class="fas fa-times-circle"></i>
            </div>
            <h3><?= $alpha_hari_ini ?></h3>
            <p>Alpha Hari Ini</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon bg-info-gradient">
                <i class="fas fa-id-card"></i>
            </div>
            <h3><?= $face_registered ?></h3>
            <p>Wajah Terdaftar</p>
        </div>
    </div>
</div>

<!-- Charts & Recent Attendance -->
<div class="row">
    <!-- Chart -->
    <div class="col-lg-8 mb-4">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar me-2 text-primary"></i>Statistik Absensi 7 Hari Terakhir</h5>
            </div>
            <div class="card-body">
                <canvas id="attendanceChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Info -->
    <div class="col-lg-4 mb-4">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2 text-primary"></i>Info Singkat</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3 p-3 rounded" style="background:#f8f9fa;">
                    <div class="stat-icon bg-primary-gradient me-3" style="width:40px; height:40px; font-size:16px;">
                        <i class="fas fa-school"></i>
                    </div>
                    <div>
                        <div style="font-size:12px; color:#636e72;">Total Kelas</div>
                        <div style="font-weight:600; font-size:18px;"><?= $total_kelas ?></div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3 p-3 rounded" style="background:#f8f9fa;">
                    <div class="stat-icon bg-success-gradient me-3" style="width:40px; height:40px; font-size:16px;">
                        <i class="fas fa-face-smile"></i>
                    </div>
                    <div>
                        <div style="font-size:12px; color:#636e72;">Wajah Terdaftar</div>
                        <div style="font-weight:600; font-size:18px;"><?= $face_registered ?> / <?= $total_siswa ?></div>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3 p-3 rounded" style="background:#f8f9fa;">
                    <div class="stat-icon bg-warning-gradient me-3" style="width:40px; height:40px; font-size:16px;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div style="font-size:12px; color:#636e72;">Jam Masuk</div>
                        <div style="font-weight:600; font-size:18px;"><?= $pengaturan['jam_masuk'] ?? '07:00' ?></div>
                    </div>
                </div>
                <div class="d-flex align-items-center p-3 rounded" style="background:#f8f9fa;">
                    <div class="stat-icon bg-danger-gradient me-3" style="width:40px; height:40px; font-size:16px;">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div>
                        <div style="font-size:12px; color:#636e72;">Tanggal</div>
                        <div style="font-weight:600; font-size:14px;"><?= date('d M Y') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Attendance Table -->
<div class="row">
    <div class="col-12">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="fas fa-history me-2 text-primary"></i>Absensi Hari Ini</h5>
                <a href="absensi.php" class="btn btn-primary-gradient btn-sm">
                    <i class="fas fa-camera me-1"></i>Mulai Absensi
                </a>
            </div>
            <div class="card-body">
                <?php if (mysqli_num_rows($today_attendance) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jam Masuk</th>
                                <th>Status</th>
                                <th>Metode</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($today_attendance)): ?>
                            <tr>
                                <td><?= $row['nis'] ?></td>
                                <td><?= $row['nama_lengkap'] ?></td>
                                <td><?= $row['nama_kelas'] ?></td>
                                <td><?= $row['jam_masuk'] ?? '-' ?></td>
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
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada data absensi hari ini</p>
                    <a href="absensi.php" class="btn btn-primary-gradient">
                        <i class="fas fa-camera me-1"></i>Mulai Absensi Sekarang
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Wait for Chart.js to load
document.addEventListener('DOMContentLoaded', function() {
    const chartData = <?= json_encode($chart_data) ?>;
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.map(d => d.label),
            datasets: [
                {
                    label: 'Hadir',
                    data: chartData.map(d => d.hadir),
                    backgroundColor: 'rgba(17, 153, 142, 0.8)',
                    borderRadius: 5,
                },
                {
                    label: 'Izin',
                    data: chartData.map(d => d.izin),
                    backgroundColor: 'rgba(242, 201, 76, 0.8)',
                    borderRadius: 5,
                },
                {
                    label: 'Sakit',
                    data: chartData.map(d => d.sakit),
                    backgroundColor: 'rgba(33, 147, 176, 0.8)',
                    borderRadius: 5,
                },
                {
                    label: 'Alpha',
                    data: chartData.map(d => d.alpha),
                    backgroundColor: 'rgba(235, 51, 73, 0.8)',
                    borderRadius: 5,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
