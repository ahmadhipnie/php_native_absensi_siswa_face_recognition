<?php
$page_title = 'Absensi Face Recognition';
require_once __DIR__ . '/../includes/header.php';

// Get classes for filter
$classes = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas");
$subjects = mysqli_query($conn, "SELECT * FROM mata_pelajaran ORDER BY nama_mapel");
?>

<div class="row">
    <!-- Webcam Area -->
    <div class="col-lg-7 mb-4">
        <div class="table-card">
            <div class="card-header">
                <h5><i class="fas fa-camera me-2 text-primary"></i>Kamera Face Recognition</h5>
                <div>
                    <span class="badge bg-danger" id="statusIndicator">
                        <i class="fas fa-circle me-1" style="font-size:8px;"></i>Kamera Mati
                    </span>
                </div>
            </div>
            <div class="card-body text-center">
                <div class="webcam-container mb-3" id="webcamContainer">
                    <video id="video" autoplay playsinline></video>
                    <div class="webcam-overlay" id="faceOverlay"></div>
                    <canvas id="canvas" style="display:none;"></canvas>
                </div>
                
                <div class="d-flex justify-content-center gap-3 mb-3">
                    <button class="btn btn-primary-gradient" id="btnStartCamera" onclick="startCamera()">
                        <i class="fas fa-video me-1"></i>Nyalakan Kamera
                    </button>
                    <button class="btn btn-success" id="btnCapture" onclick="captureAndRecognize()" disabled>
                        <i class="fas fa-camera me-1"></i>Absen Sekarang
                    </button>
                    <button class="btn btn-danger" id="btnStopCamera" onclick="stopCamera()" disabled>
                        <i class="fas fa-video-slash me-1"></i>Matikan Kamera
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings & Info -->
    <div class="col-lg-5 mb-4">
        <!-- Filter -->
        <div class="table-card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-filter me-2 text-primary"></i>Pengaturan Absensi</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Kelas</label>
                    <select class="form-select" id="filterKelas">
                        <option value="">-- Semua Kelas --</option>
                        <?php while ($kelas = mysqli_fetch_assoc($classes)): ?>
                        <option value="<?= $kelas['id'] ?>"><?= $kelas['nama_kelas'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Mata Pelajaran</label>
                    <select class="form-select" id="filterMapel">
                        <option value="">-- Pilih Mapel --</option>
                        <?php while ($mapel = mysqli_fetch_assoc($subjects)): ?>
                        <option value="<?= $mapel['id'] ?>"><?= $mapel['nama_mapel'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Tanggal</label>
                    <input type="date" class="form-control" id="filterTanggal" value="<?= date('Y-m-d') ?>">
                </div>
            </div>
        </div>

        <!-- Today's Stats -->
        <div class="table-card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie me-2 text-primary"></i>Statistik Hari Ini</h5>
            </div>
            <div class="card-body">
                <?php
                $stats = [
                    'hadir' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM absensi WHERE tanggal=CURDATE() AND status='hadir'"))['t'],
                    'izin' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM absensi WHERE tanggal=CURDATE() AND status='izin'"))['t'],
                    'sakit' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM absensi WHERE tanggal=CURDATE() AND status='sakit'"))['t'],
                    'alpha' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM absensi WHERE tanggal=CURDATE() AND status='alpha'"))['t'],
                ];
                ?>
                <div class="row text-center">
                    <div class="col-3">
                        <div class="p-2 rounded" style="background:#d4edda;">
                            <h4 class="mb-0" style="color:#155724;"><?= $stats['hadir'] ?></h4>
                            <small style="color:#155724;">Hadir</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 rounded" style="background:#fff3cd;">
                            <h4 class="mb-0" style="color:#856404;"><?= $stats['izin'] ?></h4>
                            <small style="color:#856404;">Izin</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 rounded" style="background:#cce5ff;">
                            <h4 class="mb-0" style="color:#004085;"><?= $stats['sakit'] ?></h4>
                            <small style="color:#004085;">Sakit</small>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 rounded" style="background:#f8d7da;">
                            <h4 class="mb-0" style="color:#721c24;"><?= $stats['alpha'] ?></h4>
                            <small style="color:#721c24;">Alpha</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="table-card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2 text-primary"></i>Cara Penggunaan</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-3" style="font-size:13px;">
                    <i class="fas fa-exclamation-triangle me-2"></i><strong>Penting:</strong> 
                    Tingkat kecocokan wajah minimal <strong>70%</strong> untuk absensi berhasil.
                </div>
                <ol class="mb-0" style="font-size:13px; color:#636e72;">
                    <li class="mb-2">Pilih <strong>Kelas</strong> dan <strong>Mata Pelajaran</strong></li>
                    <li class="mb-2">Klik <strong>"Nyalakan Kamera"</strong> untuk memulai</li>
                    <li class="mb-2">Arahkan wajah siswa ke kamera dengan jelas</li>
                    <li class="mb-2">Pastikan pencahayaan cukup dan wajah terlihat penuh</li>
                    <li class="mb-2">Klik <strong>"Absen Sekarang"</strong> untuk mengenali wajah</li>
                    <li class="mb-2">Sistem memerlukan <strong>kecocokan ≥ 70%</strong> untuk absensi berhasil</li>
                    <li>Pastikan wajah siswa sudah terdaftar di menu <strong>Register Wajah</strong></li>
                </ol>
            </div>
        </div>
    </div>
</div>

<script>
let stream = null;
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');

function startCamera() {
    navigator.mediaDevices.getUserMedia({ 
        video: { width: 640, height: 480, facingMode: 'user' } 
    })
    .then(function(s) {
        stream = s;
        video.srcObject = stream;
        document.getElementById('btnCapture').disabled = false;
        document.getElementById('btnStopCamera').disabled = false;
        document.getElementById('btnStartCamera').disabled = true;
        document.getElementById('statusIndicator').className = 'badge bg-success';
        document.getElementById('statusIndicator').innerHTML = '<i class="fas fa-circle me-1" style="font-size:8px;"></i>Kamera Aktif';
    })
    .catch(function(err) {
        Swal.fire('Error', 'Tidak dapat mengakses kamera: ' + err.message, 'error');
    });
}

function stopCamera() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        video.srcObject = null;
        stream = null;
    }
    document.getElementById('btnCapture').disabled = true;
    document.getElementById('btnStopCamera').disabled = true;
    document.getElementById('btnStartCamera').disabled = false;
    document.getElementById('statusIndicator').className = 'badge bg-danger';
    document.getElementById('statusIndicator').innerHTML = '<i class="fas fa-circle me-1" style="font-size:8px;"></i>Kamera Mati';
}

function captureAndRecognize() {
    if (!stream) return;

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    
    const imageData = canvas.toDataURL('image/jpeg', 0.8);
    const kelasId = document.getElementById('filterKelas').value;
    const mapelId = document.getElementById('filterMapel').value;
    const tanggal = document.getElementById('filterTanggal').value;

    // Show loading
    Swal.fire({
        title: 'Memproses...',
        text: 'Sedang mengenali wajah',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    // Send to Python API for face recognition
    fetch('../api/recognize.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            image: imageData,
            kelas_id: kelasId,
            mapel_id: mapelId,
            tanggal: tanggal
        })
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        showResult(data);
    })
    .catch(error => {
        Swal.close();
        Swal.fire('Error', 'Gagal menghubungi server: ' + error.message, 'error');
    });
}

function showResult(data) {
    if (data.success) {
        // Show confidence with color coding
        let confidenceColor = '#28a745'; // green
        let confidenceIcon = 'success';
        if (data.confidence < 80) {
            confidenceColor = '#ffc107'; // yellow
            confidenceIcon = 'warning';
        }
        if (data.confidence < 70) {
            confidenceColor = '#dc3545'; // red
            confidenceIcon = 'error';
        }
        
        // Success Modal
        Swal.fire({
            icon: 'success',
            title: '<strong style="color:#28a745;">✅ Absensi Berhasil!</strong>',
            html: `
                <div style="text-align:center; padding:20px;">
                    <div style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:white; padding:15px; border-radius:15px; margin-bottom:20px;">
                        <h3 style="margin:0; color:white;">${data.nama}</h3>
                        <p style="margin:5px 0 0; font-size:14px; opacity:0.9;">NIS: ${data.nis}</p>
                    </div>
                    <div style="background:#f8f9fa; padding:15px; border-radius:12px; margin-bottom:15px;">
                        <div style="margin-bottom:10px;">
                            <i class="fas fa-school" style="color:#667eea; margin-right:8px;"></i>
                            <strong>Kelas:</strong> ${data.kelas}
                        </div>
                        <div style="margin-bottom:10px;">
                            <i class="fas fa-clock" style="color:#667eea; margin-right:8px;"></i>
                            <strong>Jam Masuk:</strong> ${data.jam_masuk || new Date().toLocaleTimeString('id-ID')}
                        </div>
                        <div>
                            <i class="fas fa-percentage" style="color:${confidenceColor}; margin-right:8px;"></i>
                            <strong>Tingkat Kecocokan:</strong> 
                            <span style="background:${confidenceColor}; color:white; padding:4px 12px; border-radius:20px; font-weight:600;">
                                ${data.confidence}%
                            </span>
                        </div>
                    </div>
                    <p style="color:#28a745; font-size:14px; margin:0;">
                        <i class="fas fa-check-circle"></i> Data absensi berhasil disimpan
                    </p>
                </div>
            `,
            confirmButtonText: 'OK',
            confirmButtonColor: '#764ba2',
            width: '500px',
            allowOutsideClick: false
        });
    } else {
        // Failure Modal
        let confidenceText = '';
        if (data.confidence !== undefined && data.confidence !== null) {
            confidenceText = `
                <div style="background:#fff3cd; padding:12px; border-radius:10px; margin-top:15px; border-left:4px solid #ffc107;">
                    <i class="fas fa-exclamation-triangle" style="color:#856404;"></i>
                    <strong>Tingkat Kecocokan:</strong> 
                    <span style="background:#dc3545; color:white; padding:3px 10px; border-radius:15px; font-weight:600;">
                        ${data.confidence}%
                    </span>
                    <div style="font-size:12px; color:#856404; margin-top:5px;">
                        Minimum yang diperlukan: <strong>70%</strong>
                    </div>
                </div>
            `;
        }
        
        Swal.fire({
            icon: 'error',
            title: '<strong style="color:#dc3545;">❌ Absensi Gagal!</strong>',
            html: `
                <div style="text-align:center; padding:20px;">
                    <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:12px; margin-bottom:15px; border-left:4px solid #dc3545;">
                        <i class="fas fa-times-circle" style="font-size:40px; margin-bottom:10px;"></i>
                        <p style="margin:0; font-size:15px; line-height:1.6;">
                            ${data.message || 'Wajah tidak dikenali. Pastikan wajah sudah terdaftar di sistem.'}
                        </p>
                    </div>
                    ${confidenceText}
                    <div style="background:#e7f3ff; padding:12px; border-radius:10px; margin-top:15px; text-align:left; font-size:13px; color:#004085;">
                        <strong><i class="fas fa-lightbulb"></i> Tips:</strong>
                        <ul style="margin:8px 0 0 0; padding-left:20px;">
                            <li>Pastikan wajah terlihat jelas dan tidak tertutup</li>
                            <li>Pastikan pencahayaan cukup terang</li>
                            <li>Posisikan wajah di tengah kamera</li>
                            <li>Wajah harus sudah terdaftar di menu Register Wajah</li>
                        </ul>
                    </div>
                </div>
            `,
            confirmButtonText: 'Coba Lagi',
            confirmButtonColor: '#764ba2',
            width: '550px',
            allowOutsideClick: false
        });
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
