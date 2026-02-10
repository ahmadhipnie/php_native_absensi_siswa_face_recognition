<?php
$page_title = 'Absensi Manual';
require_once __DIR__ . '/../includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kelas_id = (int)$_POST['kelas_id'];
    $mapel_id = !empty($_POST['mapel_id']) ? (int)$_POST['mapel_id'] : 'NULL';
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);

    if (isset($_POST['status']) && is_array($_POST['status'])) {
        foreach ($_POST['status'] as $siswa_id => $status) {
            $siswa_id = (int)$siswa_id;
            $status = mysqli_real_escape_string($conn, $status);
            $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan'][$siswa_id] ?? '');
            $jam_masuk = ($status === 'hadir') ? date('H:i:s') : 'NULL';

            // Check if already exists
            $check = mysqli_query($conn, "SELECT id FROM absensi WHERE siswa_id=$siswa_id AND tanggal='$tanggal'");
            if (mysqli_num_rows($check) > 0) {
                $jam_val = ($jam_masuk === 'NULL') ? 'jam_masuk' : "'$jam_masuk'";
                $mapel_val = ($mapel_id === 'NULL') ? 'NULL' : $mapel_id;
                mysqli_query($conn, "UPDATE absensi SET status='$status', keterangan='$keterangan', metode_absen='manual', mapel_id=$mapel_val WHERE siswa_id=$siswa_id AND tanggal='$tanggal'");
            } else {
                $jam_val = ($jam_masuk === 'NULL') ? 'NULL' : "'$jam_masuk'";
                $mapel_val = ($mapel_id === 'NULL') ? 'NULL' : $mapel_id;
                mysqli_query($conn, "INSERT INTO absensi (siswa_id, kelas_id, mapel_id, tanggal, jam_masuk, status, metode_absen, keterangan) 
                             VALUES ($siswa_id, $kelas_id, $mapel_val, '$tanggal', $jam_val, '$status', 'manual', '$keterangan')");
            }
        }
        echo "<script>
            Swal.fire('Berhasil!', 'Data absensi berhasil disimpan.', 'success')
            .then(() => { window.location.href='absensi_manual.php?msg=saved'; });
        </script>";
    }
}

$classes = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas");
$subjects = mysqli_query($conn, "SELECT * FROM mata_pelajaran ORDER BY nama_mapel");
?>

<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>Data absensi berhasil disimpan!
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="table-card mb-4">
    <div class="card-header">
        <h5><i class="fas fa-filter me-2 text-primary"></i>Filter</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Kelas <span class="text-danger">*</span></label>
                <select class="form-select" id="selectKelas" onchange="loadStudents()">
                    <option value="">-- Pilih Kelas --</option>
                    <?php while ($kelas = mysqli_fetch_assoc($classes)): ?>
                    <option value="<?= $kelas['id'] ?>"><?= $kelas['nama_kelas'] ?> - <?= $kelas['jurusan'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Mata Pelajaran</label>
                <select class="form-select" id="selectMapel">
                    <option value="">-- Pilih Mapel --</option>
                    <?php while ($mapel = mysqli_fetch_assoc($subjects)): ?>
                    <option value="<?= $mapel['id'] ?>"><?= $mapel['nama_mapel'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Tanggal <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="selectTanggal" value="<?= date('Y-m-d') ?>">
            </div>
        </div>
    </div>
</div>

<div id="attendanceForm"></div>

<script>
function loadStudents() {
    const kelasId = document.getElementById('selectKelas').value;
    if (!kelasId) {
        document.getElementById('attendanceForm').innerHTML = '';
        return;
    }

    fetch('../api/get_students.php?kelas_id=' + kelasId)
    .then(r => r.json())
    .then(data => {
        if (data.length === 0) {
            document.getElementById('attendanceForm').innerHTML = `
                <div class="table-card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Tidak ada siswa di kelas ini</p>
                    </div>
                </div>`;
            return;
        }

        const mapelId = document.getElementById('selectMapel').value;
        const tanggal = document.getElementById('selectTanggal').value;

        let html = `
        <form method="POST" action="absensi_manual.php">
            <input type="hidden" name="kelas_id" value="${kelasId}">
            <input type="hidden" name="mapel_id" value="${mapelId}">
            <input type="hidden" name="tanggal" value="${tanggal}">
            <div class="table-card">
                <div class="card-header">
                    <h5><i class="fas fa-clipboard-check me-2 text-primary"></i>Daftar Siswa</h5>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-success me-1" onclick="setAll('hadir')">Semua Hadir</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="setAll('alpha')">Semua Alpha</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>NIS</th>
                                    <th>Nama Siswa</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>`;
        
        data.forEach((s, i) => {
            html += `
                <tr>
                    <td>${i + 1}</td>
                    <td><span class="fw-bold">${s.nis}</span></td>
                    <td>${s.nama_lengkap}</td>
                    <td>
                        <select class="form-select form-select-sm status-select" name="status[${s.id}]" style="width:120px;">
                            <option value="hadir" ${s.current_status === 'hadir' ? 'selected' : ''}>✅ Hadir</option>
                            <option value="izin" ${s.current_status === 'izin' ? 'selected' : ''}>📋 Izin</option>
                            <option value="sakit" ${s.current_status === 'sakit' ? 'selected' : ''}>🏥 Sakit</option>
                            <option value="alpha" ${s.current_status === 'alpha' ? 'selected' : ''}>❌ Alpha</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control form-control-sm" name="keterangan[${s.id}]" 
                               value="${s.keterangan || ''}" placeholder="Opsional" style="width:200px;">
                    </td>
                </tr>`;
        });

        html += `</tbody></table></div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary-gradient btn-lg">
                            <i class="fas fa-save me-1"></i>Simpan Absensi
                        </button>
                    </div>
                </div>
            </div>
        </form>`;

        document.getElementById('attendanceForm').innerHTML = html;
    });
}

function setAll(status) {
    document.querySelectorAll('.status-select').forEach(select => {
        select.value = status;
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
