<?php
$page_title = 'Pengaturan';
require_once __DIR__ . '/../includes/header.php';

// Handle settings update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_sekolah'])) {
        $nama = mysqli_real_escape_string($conn, $_POST['nama_sekolah']);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat_sekolah']);
        $jam_masuk = mysqli_real_escape_string($conn, $_POST['jam_masuk']);
        $jam_pulang = mysqli_real_escape_string($conn, $_POST['jam_pulang']);
        $batas_terlambat = mysqli_real_escape_string($conn, $_POST['batas_terlambat']);
        
        mysqli_query($conn, "UPDATE pengaturan SET nama_sekolah='$nama', alamat_sekolah='$alamat', 
                     jam_masuk='$jam_masuk', jam_pulang='$jam_pulang', batas_terlambat='$batas_terlambat' WHERE id=1");
        echo "<script>window.location.href='pengaturan.php?msg=saved';</script>";
        exit;
    }

    if (isset($_POST['update_password'])) {
        $old_pass = $_POST['old_password'];
        $new_pass = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];

        $admin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM admin WHERE id=" . $_SESSION['admin_id']));
        
        if (!password_verify($old_pass, $admin['password'])) {
            $pass_error = 'Password lama salah!';
        } elseif ($new_pass !== $confirm) {
            $pass_error = 'Konfirmasi password tidak cocok!';
        } elseif (strlen($new_pass) < 6) {
            $pass_error = 'Password baru minimal 6 karakter!';
        } else {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE admin SET password='$hashed' WHERE id=" . $_SESSION['admin_id']);
            echo "<script>window.location.href='pengaturan.php?msg=password_changed';</script>";
            exit;
        }
    }
}

// Refresh settings
$setting_query = mysqli_query($conn, "SELECT * FROM pengaturan LIMIT 1");
$pengaturan = mysqli_fetch_assoc($setting_query);
?>

<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    <?= $_GET['msg'] === 'saved' ? 'Pengaturan berhasil disimpan!' : 'Password berhasil diubah!' ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <!-- School Settings -->
    <div class="col-lg-7 mb-4">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="fas fa-school me-2 text-primary"></i>Pengaturan Sekolah</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Sekolah</label>
                        <input type="text" class="form-control" name="nama_sekolah" value="<?= $pengaturan['nama_sekolah'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alamat Sekolah</label>
                        <textarea class="form-control" name="alamat_sekolah" rows="2"><?= $pengaturan['alamat_sekolah'] ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Jam Masuk</label>
                            <input type="time" class="form-control" name="jam_masuk" value="<?= $pengaturan['jam_masuk'] ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Batas Terlambat</label>
                            <input type="time" class="form-control" name="batas_terlambat" value="<?= $pengaturan['batas_terlambat'] ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold">Jam Pulang</label>
                            <input type="time" class="form-control" name="jam_pulang" value="<?= $pengaturan['jam_pulang'] ?>">
                        </div>
                    </div>
                    <button type="submit" name="update_sekolah" class="btn btn-primary-gradient">
                        <i class="fas fa-save me-1"></i>Simpan Pengaturan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="col-lg-5 mb-4">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="fas fa-key me-2 text-primary"></i>Ubah Password</h5>
            </div>
            <div class="card-body">
                <?php if (isset($pass_error)): ?>
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= $pass_error ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password Lama</label>
                        <input type="password" class="form-control" name="old_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password Baru</label>
                        <input type="password" class="form-control" name="new_password" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Konfirmasi Password</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                    <button type="submit" name="update_password" class="btn btn-primary-gradient">
                        <i class="fas fa-key me-1"></i>Ubah Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
