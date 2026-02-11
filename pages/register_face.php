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
    const studentSelect = document.getElementById('selectStudent');
    const studentName = studentSelect.options[studentSelect.selectedIndex].text;
    
    if (!studentId) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Pilih siswa terlebih dahulu!',
            confirmButtonColor: '#764ba2'
        });
        return;
    }
    if (capturedPhotos.length < MAX_PHOTOS) {
        Swal.fire({
            icon: 'warning',
            title: 'Foto Belum Lengkap',
            text: `Ambil ${MAX_PHOTOS} foto terlebih dahulu! (${capturedPhotos.length}/${MAX_PHOTOS})`,
            confirmButtonColor: '#764ba2'
        });
        return;
    }

    // Show processing modal
    Swal.fire({
        title: 'Memproses Registrasi...',
        html: `
            <div style="text-align:center; padding:20px;">
                <div style="margin-bottom:20px;">
                    <i class="fas fa-cog fa-spin" style="font-size:50px; color:#667eea;"></i>
                </div>
                <p style="margin:10px 0;"><strong>${studentName}</strong></p>
                <p style="color:#636e72; font-size:14px;">Sedang memproses ${MAX_PHOTOS} foto wajah...</p>
                <div class="progress mt-3" style="height:25px; border-radius:12px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" 
                         style="width:50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                         Memproses...
                    </div>
                </div>
            </div>
        `,
        allowOutsideClick: false,
        showConfirmButton: false
    });

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
            // Success Modal
            Swal.fire({
                icon: 'success',
                title: '<strong style="color:#28a745;">✅ Registrasi Berhasil!</strong>',
                html: `
                    <div style="text-align:center; padding:20px;">
                        <div style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white; padding:20px; border-radius:15px; margin-bottom:20px;">
                            <i class="fas fa-user-check" style="font-size:50px; margin-bottom:10px;"></i>
                            <h4 style="margin:10px 0; color:white;">${studentName}</h4>
                            <p style="margin:0; font-size:14px; opacity:0.9;">Wajah berhasil didaftarkan ke sistem</p>
                        </div>
                        
                        <div style="background:#d4edda; padding:15px; border-radius:12px; margin-bottom:15px; border-left:4px solid #28a745;">
                            <div style="color:#155724;">
                                <i class="fas fa-check-circle"></i> 
                                <strong>${MAX_PHOTOS} foto berhasil diproses</strong>
                            </div>
                            <div style="color:#155724; font-size:13px; margin-top:5px;">
                                Face encoding berhasil dibuat dan disimpan
                            </div>
                        </div>
                        
                        <div style="background:#e7f3ff; padding:12px; border-radius:10px; text-align:left; font-size:13px; color:#004085;">
                            <strong><i class="fas fa-info-circle"></i> Informasi:</strong>
                            <ul style="margin:8px 0 0 0; padding-left:20px;">
                                <li>Siswa sekarang dapat melakukan absensi dengan face recognition</li>
                                <li>Data wajah telah tersimpan dengan aman</li>
                                <li>Tingkat kecocokan minimum untuk absensi: <strong>70%</strong></li>
                            </ul>
                        </div>
                    </div>
                `,
                confirmButtonText: 'OK, Selesai',
                confirmButtonColor: '#764ba2',
                width: '550px',
                allowOutsideClick: false
            }).then(() => location.reload());
        } else {
            // Failure Modal
            Swal.fire({
                icon: 'error',
                title: '<strong style="color:#dc3545;">❌ Registrasi Gagal!</strong>',
                html: `
                    <div style="text-align:center; padding:20px;">
                        <div style="background:#f8d7da; color:#721c24; padding:20px; border-radius:12px; margin-bottom:15px; border-left:4px solid #dc3545;">
                            <i class="fas fa-exclamation-triangle" style="font-size:40px; margin-bottom:10px;"></i>
                            <p style="margin:0; font-size:15px; line-height:1.6;">
                                ${data.message || 'Gagal mendaftarkan wajah. Silakan coba lagi.'}
                            </p>
                        </div>
                        
                        <div style="background:#fff3cd; padding:12px; border-radius:10px; text-align:left; font-size:13px; color:#856404;">
                            <strong><i class="fas fa-lightbulb"></i> Kemungkinan Penyebab:</strong>
                            <ul style="margin:8px 0 0 0; padding-left:20px;">
                                <li>Wajah tidak terdeteksi dengan jelas pada foto</li>
                                <li>Pencahayaan kurang memadai</li>
                                <li>Library Python belum terinstall dengan benar</li>
                                <li>Server FastAPI tidak berjalan</li>
                            </ul>
                        </div>
                        
                        <div style="margin-top:15px; padding:10px; background:#e7f3ff; border-radius:8px; font-size:13px; color:#004085;">
                            <i class="fas fa-tools"></i> Pastikan FastAPI server sudah berjalan di port 8000
                        </div>
                    </div>
                `,
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#764ba2',
                width: '550px',
                allowOutsideClick: false
            }).then(() => {
                // Reset form untuk coba lagi
                capturedPhotos = [];
                document.getElementById('photoPreview').innerHTML = '';
                document.getElementById('btnCapture').innerHTML = '<i class="fas fa-camera me-1"></i>Ambil Foto (0/5)';
                document.getElementById('btnCapture').disabled = false;
                document.getElementById('btnRegister').disabled = true;
            });
        }
    })
    .catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'Error Koneksi',
            html: `
                <div style="padding:15px;">
                    <p>Gagal menghubungi server:</p>
                    <code style="background:#f8f9fa; padding:10px; display:block; border-radius:8px; color:#dc3545;">
                        ${err.message}
                    </code>
                    <p style="margin-top:15px; font-size:13px; color:#636e72;">
                        Pastikan server berjalan dengan baik.
                    </p>
                </div>
            `,
            confirmButtonColor: '#764ba2'
        });
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
