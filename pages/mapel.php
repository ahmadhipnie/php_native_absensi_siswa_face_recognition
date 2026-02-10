<?php
$page_title = 'Mata Pelajaran';
require_once __DIR__ . '/../includes/header.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM mata_pelajaran WHERE id = $id");
    echo "<script>window.location.href='mapel.php?msg=deleted';</script>";
    exit;
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = mysqli_real_escape_string($conn, $_POST['kode_mapel']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_mapel']);

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        mysqli_query($conn, "UPDATE mata_pelajaran SET kode_mapel='$kode', nama_mapel='$nama' WHERE id=$id");
        echo "<script>window.location.href='mapel.php?msg=updated';</script>";
    } else {
        mysqli_query($conn, "INSERT INTO mata_pelajaran (kode_mapel, nama_mapel) VALUES ('$kode', '$nama')");
        echo "<script>window.location.href='mapel.php?msg=added';</script>";
    }
    exit;
}

$subjects = mysqli_query($conn, "SELECT * FROM mata_pelajaran ORDER BY nama_mapel");
?>

<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <?php
    switch ($_GET['msg']) {
        case 'added': echo 'Mata pelajaran berhasil ditambahkan!'; break;
        case 'updated': echo 'Mata pelajaran berhasil diperbarui!'; break;
        case 'deleted': echo 'Mata pelajaran berhasil dihapus!'; break;
    }
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="table-card">
    <div class="card-header">
        <h5><i class="fas fa-book me-2 text-primary"></i>Mata Pelajaran</h5>
        <button class="btn btn-primary-gradient" data-bs-toggle="modal" data-bs-target="#modalMapel" onclick="resetForm()">
            <i class="fas fa-plus me-1"></i>Tambah Mapel
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($subjects)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><span class="badge bg-secondary rounded-pill"><?= $row['kode_mapel'] ?></span></td>
                        <td><?= $row['nama_mapel'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="editMapel(<?= htmlspecialchars(json_encode($row)) ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('mapel.php?delete=<?= $row['id'] ?>')">
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
<div class="modal fade" id="modalMapel" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:15px;">
            <div class="modal-header" style="background: var(--primary-gradient); color:white; border-radius:15px 15px 0 0;">
                <h5 class="modal-title" id="modalTitle"><i class="fas fa-plus me-2"></i>Tambah Mata Pelajaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="mapel.php">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="mapel_id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Mapel <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="kode_mapel" id="kode_mapel" required placeholder="Contoh: MTK">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_mapel" id="nama_mapel" required placeholder="Contoh: Matematika">
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
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus me-2"></i>Tambah Mata Pelajaran';
    document.getElementById('mapel_id').value = '';
    document.getElementById('kode_mapel').value = '';
    document.getElementById('nama_mapel').value = '';
}

function editMapel(data) {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Mata Pelajaran';
    document.getElementById('mapel_id').value = data.id;
    document.getElementById('kode_mapel').value = data.kode_mapel;
    document.getElementById('nama_mapel').value = data.nama_mapel;
    
    var modal = new bootstrap.Modal(document.getElementById('modalMapel'));
    modal.show();
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
