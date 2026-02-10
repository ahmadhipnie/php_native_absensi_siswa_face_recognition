<?php
$page_title = 'Data Kelas';
require_once __DIR__ . '/../includes/header.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM kelas WHERE id = $id");
    echo "<script>window.location.href='kelas.php?msg=deleted';</script>";
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kelas = mysqli_real_escape_string($conn, $_POST['nama_kelas']);
    $jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);
    $tahun_ajaran = mysqli_real_escape_string($conn, $_POST['tahun_ajaran']);

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        mysqli_query($conn, "UPDATE kelas SET nama_kelas='$nama_kelas', jurusan='$jurusan', tahun_ajaran='$tahun_ajaran' WHERE id=$id");
        echo "<script>window.location.href='kelas.php?msg=updated';</script>";
    } else {
        mysqli_query($conn, "INSERT INTO kelas (nama_kelas, jurusan, tahun_ajaran) VALUES ('$nama_kelas', '$jurusan', '$tahun_ajaran')");
        echo "<script>window.location.href='kelas.php?msg=added';</script>";
    }
    exit;
}

// Get all classes with student count
$classes = mysqli_query($conn, "
    SELECT k.*, COUNT(s.id) as jumlah_siswa 
    FROM kelas k 
    LEFT JOIN siswa s ON k.id = s.kelas_id 
    GROUP BY k.id 
    ORDER BY k.nama_kelas
");
?>

<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <?php
    switch ($_GET['msg']) {
        case 'added': echo 'Data kelas berhasil ditambahkan!'; break;
        case 'updated': echo 'Data kelas berhasil diperbarui!'; break;
        case 'deleted': echo 'Data kelas berhasil dihapus!'; break;
    }
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="table-card">
    <div class="card-header">
        <h5><i class="fas fa-school me-2 text-primary"></i>Data Kelas</h5>
        <button class="btn btn-primary-gradient" data-bs-toggle="modal" data-bs-target="#modalKelas" onclick="resetForm()">
            <i class="fas fa-plus me-1"></i>Tambah Kelas
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kelas</th>
                        <th>Jurusan</th>
                        <th>Tahun Ajaran</th>
                        <th>Jumlah Siswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($classes)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><span class="fw-bold"><?= $row['nama_kelas'] ?></span></td>
                        <td><?= $row['jurusan'] ?: '-' ?></td>
                        <td><?= $row['tahun_ajaran'] ?></td>
                        <td><span class="badge bg-primary rounded-pill"><?= $row['jumlah_siswa'] ?> siswa</span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="editKelas(<?= htmlspecialchars(json_encode($row)) ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('kelas.php?delete=<?= $row['id'] ?>')">
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

<!-- Modal -->
<div class="modal fade" id="modalKelas" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header" style="background: var(--primary-gradient); color:white; border-radius:15px 15px 0 0;">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-plus me-2"></i>Tambah Kelas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="kelas.php">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="kelas_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Kelas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kelas" id="nama_kelas" required placeholder="Contoh: X-RPL 1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jurusan</label>
                        <input type="text" class="form-control" name="jurusan" id="jurusan" placeholder="Contoh: Rekayasa Perangkat Lunak">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="tahun_ajaran" id="tahun_ajaran" required placeholder="Contoh: 2025/2026">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-gradient"><i class="fas fa-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus me-2"></i>Tambah Kelas';
    document.getElementById('kelas_id').value = '';
    document.getElementById('nama_kelas').value = '';
    document.getElementById('jurusan').value = '';
    document.getElementById('tahun_ajaran').value = '';
}

function editKelas(data) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Kelas';
    document.getElementById('kelas_id').value = data.id;
    document.getElementById('nama_kelas').value = data.nama_kelas;
    document.getElementById('jurusan').value = data.jurusan || '';
    document.getElementById('tahun_ajaran').value = data.tahun_ajaran;
    
    var modal = new bootstrap.Modal(document.getElementById('modalKelas'));
    modal.show();
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
