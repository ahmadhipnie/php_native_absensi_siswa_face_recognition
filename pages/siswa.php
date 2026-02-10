<?php
$page_title = 'Data Siswa';
require_once __DIR__ . '/../includes/header.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM siswa WHERE id = $id");
    echo "<script>window.location.href='siswa.php?msg=deleted';</script>";
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nis = mysqli_real_escape_string($conn, $_POST['nis']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $jk = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $kelas_id = (int)$_POST['kelas_id'];
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_telepon = mysqli_real_escape_string($conn, $_POST['no_telepon']);

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $id = (int)$_POST['id'];
        $query = "UPDATE siswa SET nis='$nis', nama_lengkap='$nama', jenis_kelamin='$jk', 
                  kelas_id=$kelas_id, alamat='$alamat', no_telepon='$no_telepon' WHERE id=$id";
        mysqli_query($conn, $query);
        echo "<script>window.location.href='siswa.php?msg=updated';</script>";
    } else {
        // Insert
        $query = "INSERT INTO siswa (nis, nama_lengkap, jenis_kelamin, kelas_id, alamat, no_telepon) 
                  VALUES ('$nis', '$nama', '$jk', $kelas_id, '$alamat', '$no_telepon')";
        mysqli_query($conn, $query);
        echo "<script>window.location.href='siswa.php?msg=added';</script>";
    }
    exit;
}

// Get all students
$students = mysqli_query($conn, "
    SELECT s.*, k.nama_kelas 
    FROM siswa s 
    JOIN kelas k ON s.kelas_id = k.id 
    ORDER BY s.nama_lengkap ASC
");

// Get all classes for dropdown
$classes = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas");
?>

<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <?php
    switch ($_GET['msg']) {
        case 'added': echo 'Data siswa berhasil ditambahkan!'; break;
        case 'updated': echo 'Data siswa berhasil diperbarui!'; break;
        case 'deleted': echo 'Data siswa berhasil dihapus!'; break;
    }
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="table-card">
    <div class="card-header">
        <h5><i class="fas fa-user-graduate me-2 text-primary"></i>Data Siswa</h5>
        <button class="btn btn-primary-gradient" data-bs-toggle="modal" data-bs-target="#modalSiswa" onclick="resetForm()">
            <i class="fas fa-plus me-1"></i>Tambah Siswa
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Lengkap</th>
                        <th>L/P</th>
                        <th>Kelas</th>
                        <th>No. Telepon</th>
                        <th>Face ID</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($students)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><span class="fw-bold"><?= $row['nis'] ?></span></td>
                        <td><?= $row['nama_lengkap'] ?></td>
                        <td>
                            <span class="badge <?= $row['jenis_kelamin'] === 'L' ? 'bg-primary' : 'bg-danger' ?> rounded-pill">
                                <?= $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
                            </span>
                        </td>
                        <td><?= $row['nama_kelas'] ?></td>
                        <td><?= $row['no_telepon'] ?: '-' ?></td>
                        <td>
                            <?php if ($row['face_registered']): ?>
                                <span class="badge bg-success rounded-pill"><i class="fas fa-check me-1"></i>Terdaftar</span>
                            <?php else: ?>
                                <span class="badge bg-warning rounded-pill"><i class="fas fa-exclamation me-1"></i>Belum</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="editSiswa(<?= htmlspecialchars(json_encode($row)) ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('siswa.php?delete=<?= $row['id'] ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add/Edit Siswa -->
<div class="modal fade" id="modalSiswa" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header" style="background: var(--primary-gradient); color:white; border-radius:15px 15px 0 0;">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-user-plus me-2"></i>Tambah Siswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="siswa.php">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="siswa_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">NIS <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nis" id="nis" required placeholder="Masukkan NIS">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" required placeholder="Masukkan nama lengkap">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jenis Kelamin <span class="text-danger">*</span></label>
                            <select class="form-select" name="jenis_kelamin" id="jenis_kelamin" required>
                                <option value="">-- Pilih --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Kelas <span class="text-danger">*</span></label>
                            <select class="form-select" name="kelas_id" id="kelas_id" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php 
                                mysqli_data_seek($classes, 0);
                                while ($kelas = mysqli_fetch_assoc($classes)): ?>
                                <option value="<?= $kelas['id'] ?>"><?= $kelas['nama_kelas'] ?> - <?= $kelas['jurusan'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">No. Telepon</label>
                            <input type="text" class="form-control" name="no_telepon" id="no_telepon" placeholder="Masukkan no. telepon">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Alamat</label>
                            <textarea class="form-control" name="alamat" id="alamat" rows="1" placeholder="Masukkan alamat"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-gradient">
                        <i class="fas fa-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-plus me-2"></i>Tambah Siswa';
    document.getElementById('siswa_id').value = '';
    document.getElementById('nis').value = '';
    document.getElementById('nama_lengkap').value = '';
    document.getElementById('jenis_kelamin').value = '';
    document.getElementById('kelas_id').value = '';
    document.getElementById('no_telepon').value = '';
    document.getElementById('alamat').value = '';
}

function editSiswa(data) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Siswa';
    document.getElementById('siswa_id').value = data.id;
    document.getElementById('nis').value = data.nis;
    document.getElementById('nama_lengkap').value = data.nama_lengkap;
    document.getElementById('jenis_kelamin').value = data.jenis_kelamin;
    document.getElementById('kelas_id').value = data.kelas_id;
    document.getElementById('no_telepon').value = data.no_telepon || '';
    document.getElementById('alamat').value = data.alamat || '';
    
    var modal = new bootstrap.Modal(document.getElementById('modalSiswa'));
    modal.show();
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
