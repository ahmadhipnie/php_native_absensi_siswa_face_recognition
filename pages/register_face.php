<?php
$page_title = 'Register Wajah';
require_once __DIR__ . '/../includes/header.php';

// Get students without face registration
$students_no_face = mysqli_query($conn, "
    SELECT s.*, k.nama_kelas 
    FROM siswa s 
    JOIN kelas k ON s.kelas_id = k.id 
    WHERE s.face_registered = 0 AND s.status = 'aktif'
    ORDER BY k.nama_kelas, s.nama_lengkap
");

$students_with_face = mysqli_query($conn, "
    SELECT s.*, k.nama_kelas 
    FROM siswa s 
    JOIN kelas k ON s.kelas_id = k.id 
    WHERE s.face_registered = 1 AND s.status = 'aktif'
    ORDER BY k.nama_kelas, s.nama_lengkap
");
?>

<div class="row">
    <!-- Register Section -->
    <div class="col-lg-7 mb-4">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="fas fa-camera me-2 text-primary"></i>Registrasi Wajah Siswa</h5>
            </div>
            <div class="card-body">
                <!-- Select Student -->
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Siswa</label>
                    <select class="form-select" id="selectStudent">
                        <option value="">-- Pilih Siswa --</option>
                        <?php while ($s = mysqli_fetch_assoc($students_no_face)): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['nis'] ?> - <?= $s['nama_lengkap'] ?> (<?= $s['nama_kelas'] ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Webcam -->
                <div class="webcam-container mb-3" style="max-height:400px;">
                    <video id="video" autoplay playsinline></video>
                    <div class="webcam-overlay"></div>
                    <canvas id="canvas" style="display:none;"></canvas>
                </div>

                <div class="d-flex justify-content-center gap-3 mb-3">
                    <button class="btn btn-primary-gradient" id="btnStart" onclick="startCamera()">
                        <i class="fas fa-video me-1"></i>Nyalakan Kamera
                    </button>
                    <button class="btn btn-success" id="btnCapture" onclick="captureMultiple()" disabled>
                        <i class="fas fa-camera me-1"></i>Ambil Foto (0/5)
                    </button>
                    <button class="btn btn-danger" id="btnStop" onclick="stopCamera()" disabled>
                        <i class="fas fa-video-slash me-1"></i>Stop
                    </button>
                </div>

                <!-- Captured Photos Preview -->
                <div id="photoPreview" class="d-flex gap-2 flex-wrap justify-content-center mb-3"></div>

                <button class="btn btn-primary-gradient w-100" id="btnRegister" onclick="registerFace()" disabled>
                    <i class="fas fa-save me-1"></i>Register Wajah
                </button>

                <!-- Progress -->
                <div class="progress mt-3 d-none" id="progressBar" style="height:25px; border-radius:12px;">
                    <div class="progress-bar bg-gradient" role="progressbar" style="width:0%; background: var(--primary-gradient);">0%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registered Faces List -->
    <div class="col-lg-5 mb-4">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="fas fa-id-card me-2 text-primary"></i>Wajah Terdaftar</h5>
                <span class="badge bg-success rounded-pill"><?= mysqli_num_rows($students_with_face) ?> siswa</span>
            </div>
            <div class="card-body" style="max-height:600px; overflow-y:auto;">
                <?php if (mysqli_num_rows($students_with_face) > 0): ?>
                    <?php while ($s = mysqli_fetch_assoc($students_with_face)): ?>
                    <div class="d-flex align-items-center p-3 mb-2 rounded" style="background:#f8f9fa;">
                        <div class="me-3">
                            <?php if ($s['foto']): ?>
                                <img src="../uploads/<?= $s['foto'] ?>" class="rounded-circle" width="45" height="45" style="object-fit:cover;">
                            <?php else: ?>
                                <div style="width:45px; height:45px; background:var(--primary-gradient); border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:600;">
                                    <?= strtoupper(substr($s['nama_lengkap'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold" style="font-size:14px;"><?= $s['nama_lengkap'] ?></div>
                            <div style="font-size:12px; color:#636e72;"><?= $s['nis'] ?> • <?= $s['nama_kelas'] ?></div>
                        </div>
                        <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i></span>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-id-card fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada wajah terdaftar</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
let stream = null;
let capturedPhotos = [];
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const MAX_PHOTOS = 5;

function startCamera() {
    navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480 } })
    .then(s => {
        stream = s;
        video.srcObject = stream;
        document.getElementById('btnCapture').disabled = false;
        document.getElementById('btnStop').disabled = false;
        document.getElementById('btnStart').disabled = true;
    })
    .catch(err => Swal.fire('Error', 'Tidak dapat mengakses kamera', 'error'));
}

function stopCamera() {
    if (stream) {
        stream.getTracks().forEach(t => t.stop());
        video.srcObject = null;
        stream = null;
    }
    document.getElementById('btnCapture').disabled = true;
    document.getElementById('btnStop').disabled = true;
    document.getElementById('btnStart').disabled = false;
}

function captureMultiple() {
    if (capturedPhotos.length >= MAX_PHOTOS) return;

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    
    const imageData = canvas.toDataURL('image/jpeg', 0.8);
    capturedPhotos.push(imageData);

    // Update preview
    const preview = document.getElementById('photoPreview');
    const img = document.createElement('img');
    img.src = imageData;
    img.style.cssText = 'width:80px; height:80px; object-fit:cover; border-radius:10px; border:3px solid #667eea;';
    preview.appendChild(img);

    // Update button text
    document.getElementById('btnCapture').innerHTML = `<i class="fas fa-camera me-1"></i>Ambil Foto (${capturedPhotos.length}/${MAX_PHOTOS})`;

    if (capturedPhotos.length >= MAX_PHOTOS) {
        document.getElementById('btnCapture').disabled = true;
        document.getElementById('btnRegister').disabled = false;
        Swal.fire('Siap!', 'Semua foto sudah diambil. Klik "Register Wajah" untuk mendaftarkan.', 'success');
    }
}

function registerFace() {
    const studentId = document.getElementById('selectStudent').value;
    if (!studentId) {
        Swal.fire('Error', 'Pilih siswa terlebih dahulu!', 'error');
        return;
    }
    if (capturedPhotos.length < MAX_PHOTOS) {
        Swal.fire('Error', `Ambil ${MAX_PHOTOS} foto terlebih dahulu!`, 'error');
        return;
    }

    const progressBar = document.getElementById('progressBar');
    progressBar.classList.remove('d-none');

    fetch('../api/register_face.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            student_id: studentId,
            images: capturedPhotos
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            progressBar.querySelector('.progress-bar').style.width = '100%';
            progressBar.querySelector('.progress-bar').textContent = '100%';
            Swal.fire('Berhasil!', 'Wajah berhasil didaftarkan!', 'success')
            .then(() => location.reload());
        } else {
            Swal.fire('Gagal', data.message || 'Gagal mendaftarkan wajah', 'error');
        }
    })
    .catch(err => {
        Swal.fire('Error', 'Gagal menghubungi server', 'error');
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
