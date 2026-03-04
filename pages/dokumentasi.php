<?php
$page_title = "Dokumentasi Sistem";
require_once '../includes/header.php';
?>

<!-- Documentation Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="table-card">
            <div class="card-header">
                <div>
                    <h5 class="mb-1"><i class="fas fa-book me-2"></i>Dokumentasi Sistem Absensi</h5>
                    <small class="text-muted">Panduan lengkap penggunaan Sistem Absensi Siswa dengan Face Recognition</small>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="list-group" id="docNav">
                            <a href="#overview" class="list-group-item list-group-item-action"><i class="fas fa-info-circle me-2"></i>Overview Sistem</a>
                            <a href="#installation" class="list-group-item list-group-item-action"><i class="fas fa-download me-2"></i>Instalasi</a>
                            <a href="#login" class="list-group-item list-group-item-action"><i class="fas fa-sign-in-alt me-2"></i>Login & Logout</a>
                            <a href="#crud-siswa" class="list-group-item list-group-item-action"><i class="fas fa-user-graduate me-2"></i>Data Siswa</a>
                            <a href="#crud-kelas" class="list-group-item list-group-item-action"><i class="fas fa-school me-2"></i>Data Kelas</a>
                            <a href="#crud-mapel" class="list-group-item list-group-item-action"><i class="fas fa-book me-2"></i>Mata Pelajaran</a>
                            <a href="#register-face" class="list-group-item list-group-item-action"><i class="fas fa-id-card me-2"></i>Register Wajah</a>
                            <a href="#absensi-face" class="list-group-item list-group-item-action"><i class="fas fa-camera me-2"></i>Absensi Face ID</a>
                            <a href="#absensi-manual" class="list-group-item list-group-item-action"><i class="fas fa-clipboard-check me-2"></i>Absensi Manual</a>
                            <a href="#laporan" class="list-group-item list-group-item-action"><i class="fas fa-chart-bar me-2"></i>Laporan</a>
                            <a href="#rekap" class="list-group-item list-group-item-action"><i class="fas fa-file-alt me-2"></i>Rekap Bulanan</a>
                            <a href="#pengaturan" class="list-group-item list-group-item-action"><i class="fas fa-cog me-2"></i>Pengaturan</a>
                            <a href="#api" class="list-group-item list-group-item-action"><i class="fas fa-code me-2"></i>API Endpoints</a>
                            <a href="#troubleshooting" class="list-group-item list-group-item-action"><i class="fas fa-wrench me-2"></i>Troubleshooting</a>
                        </div>
                    </div>
                    <div class="col-md-9">

                        <!-- Overview -->
                        <div id="overview" class="doc-section mb-5">
                            <h4 class="mb-3 text-primary"><i class="fas fa-info-circle me-2"></i>Overview Sistem</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p>Sistem Absensi Siswa dengan Face Recognition adalah aplikasi berbasis web yang menggabungkan teknologi pengenalan wajah untuk mempermudah proses absensi siswa.</p>

                                    <h6 class="mt-3">Arsitektur Sistem:</h6>
                                    <div class="bg-white p-3 rounded border">
                                        <pre class="mb-0 text-muted">
┌─────────────┐     HTTP      ┌──────────────────┐
│   Browser   │ ←──────────→   │   PHP (Apache)   │
│  (Webcam)   │                │  Port 8001/80    │
└─────────────┘                └────────┬─────────┘
                                         │ cURL (JSON)
                                         ▼
                                ┌──────────────────┐
                                │  FastAPI (Python) │
                                │   Port 8000      │
                                └────────┬─────────┘
                                         │
                           ┌─────────────┼─────────────┐
                           ▼             ▼             ▼
                     ┌───────────┐ ┌──────────┐ ┌──────────┐
                     │face_recog.│ │   dlib   │ │  MySQL   │
                     └───────────┘ └──────────┘ └──────────┘
                                        </pre>
                                    </div>

                                    <h6 class="mt-3">Teknologi yang Digunakan:</h6>
                                    <table class="table table-sm table-bordered">
                                        <tr><th width="30%">Komponen</th><th>Teknologi</th></tr>
                                        <tr><td>Web Backend</td><td>PHP Native</td></tr>
                                        <tr><td>Database</td><td>MySQL</td></tr>
                                        <tr><td>Frontend</td><td>Bootstrap 5, DataTables, SweetAlert2</td></tr>
                                        <tr><td>Face Recognition API</td><td>Python FastAPI</td></tr>
                                        <tr><td>Face Detection</td><td>face_recognition (dlib)</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Installation -->
                        <div id="installation" class="doc-section mb-5">
                            <h4 class="mb-3 text-primary"><i class="fas fa-download me-2"></i>Instalasi & Setup</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Prasyarat:</h6>
                                    <ul>
                                        <li>PHP 7.4+ (Laragon / XAMPP / PHP built-in server)</li>
                                        <li>Python 3.8 - 3.12</li>
                                        <li>MySQL (phpMyAdmin)</li>
                                        <li>Visual C++ Build Tools (Windows untuk dlib)</li>
                                    </ul>

                                    <h6 class="mt-3">Langkah 1: Setup Database</h6>
                                    <ol>
                                        <li>Buka phpMyAdmin (http://localhost/phpmyadmin)</li>
                                        <li>Import file <code>database/db_absensi_siswa.sql</code></li>
                                    </ol>

                                    <h6 class="mt-3">Langkah 2: Setup Python</h6>
                                    <pre class="bg-dark text-light p-2 rounded">cd python
pip install -r requirements.txt</pre>

                                    <div class="alert alert-warning mt-2">
                                        <strong>Note:</strong> Jika terjadi error <code>pkg_resources</code>:
                                        <pre class="mb-0 mt-2">pip install "setuptools<75"</pre>
                                    </div>

                                    <h6 class="mt-3">Langkah 3: Jalankan Kedua Server</h6>
                                    <div class="alert alert-info">
                                        <strong>Port Configuration:</strong>
                                        <ul class="mb-0">
                                            <li><strong>Port 8000</strong> - FastAPI (Python Face Recognition API)</li>
                                            <li><strong>Port 8001</strong> - PHP Web Server</li>
                                        </ul>
                                    </div>
                                    <p>Buka 2 terminal:</p>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="border rounded p-2">
                                                <strong>Terminal 1 - FastAPI (Port 8000):</strong>
                                                <pre class="bg-dark text-light p-2 rounded mb-0">cd python
python -m uvicorn main:app --host 0.0.0.0 --port 8000 --reload</pre>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="border rounded p-2">
                                                <strong>Terminal 2 - PHP Web (Port 8001):</strong>
                                                <pre class="bg-dark text-light p-2 rounded mb-0">php -S localhost:8001</pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Login & Logout -->
                        <div id="login" class="doc-section mb-5">
                            <h4 class="mb-3 text-primary"><i class="fas fa-sign-in-alt me-2"></i>Login & Logout</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Login Default:</h6>
                                    <table class="table table-sm table-bordered w-50">
                                        <tr><th>Username</th><td><code>admin</code></td></tr>
                                        <tr><th>Password</th><td><code>admin123</code></td></tr>
                                    </table>

                                    <h6 class="mt-3">Proses Login:</h6>
                                    <ol>
                                        <li>User memasukkan username & password</li>
                                        <li>PHP memquery tabel <code>admin</code></li>
                                        <li>Password diverifikasi dengan <code>password_verify()</code></li>
                                        <li>Jika valid, buat session variables:
                                            <ul>
                                                <li><code>$_SESSION['admin_id']</code></li>
                                                <li><code>$_SESSION['admin_username']</code></li>
                                                <li><code>$_SESSION['admin_nama']</code></li>
                                            </ul>
                                        </li>
                                        <li>Redirect ke <code>dashboard.php</code></li>
                                    </ol>

                                    <h6 class="mt-3">Proses Logout:</h6>
                                    <p>Klik tombol Logout → <code>session_destroy()</code> → Redirect ke login page</p>

                                    <div class="alert alert-info mt-2">
                                        <strong>Security:</strong> Password di-hash menggunakan bcrypt (PASSWORD_DEFAULT). Setiap halaman dilindungi dengan pengecekan session di <code>includes/header.php</code>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CRUD Siswa -->
                        <div id="crud-siswa" class="doc-section mb-5">
                            <h4 class="mb-3 text-primary"><i class="fas fa-user-graduate me-2"></i>Manajemen Data Siswa</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Tabel: <code>siswa</code></h6>
                                    <table class="table table-sm table-bordered">
                                        <tr><th>Field</th><th>Tipe</th><th>Keterangan</th></tr>
                                        <tr><td>id</td><td>INT (PK)</td><td>Auto increment</td></tr>
                                        <tr><td>nis</td><td>VARCHAR</td><td>Nomor Induk Siswa (unique)</td></tr>
                                        <tr><td>nama_lengkap</td><td>VARCHAR</td><td>Nama lengkap siswa</td></tr>
                                        <tr><td>jenis_kelamin</td><td>ENUM</td><td>L / P</td></tr>
                                        <tr><td>kelas_id</td><td>INT (FK)</td><td>Relasi ke tabel kelas</td></tr>
                                        <tr><td>face_registered</td><td>TINYINT</td><td>0 = belum, 1 = sudah</td></tr>
                                        <tr><td>face_encoding</td><td>JSON</td><td>Encoding wajah 128-dimensi</td></tr>
                                        <tr><td>status</td><td>VARCHAR</td><td>aktif / tidak aktif</td></tr>
                                    </table>

                                    <h6 class="mt-3">Operasi CRUD:</h6>
                                    <table class="table table-sm">
                                        <tr><th class="bg-success text-white">Create</th><td>Form tambah siswa → INSERT ke database dengan validasi NIS duplikat</td></tr>
                                        <tr><th class="bg-info text-white">Read</th><td>Menampilkan semua siswa dengan JOIN ke tabel kelas</td></tr>
                                        <tr><th class="bg-warning text-white">Update</th><td>Edit data siswa, cek NIS duplikat (kecuali ID yang sama)</td></tr>
                                        <tr><th class="bg-danger text-white">Delete</th><td>Hapus data siswa berdasarkan ID</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- CRUD Kelas -->
                        <div id="crud-kelas" class="doc-section mb-5">
                            <h4 class="mb-3 text-primary"><i class="fas fa-school me-2"></i>Manajemen Data Kelas</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Tabel: <code>kelas</code></h6>
                                    <table class="table table-sm table-bordered w-50">
                                        <tr><th>Field</th><th>Keterangan</th></tr>
                                        <tr><td>id</td><td>Primary Key (auto increment)</td></tr>
                                        <tr><td>nama_kelas</td><td>Nama kelas (mis: X IPA 1)</td></tr>
                                        <tr><td>jurusan</td><td>Nama jurusan</td></tr>
                                        <tr><td>tahun_ajaran</td><td>Tahun ajaran aktif</td></tr>
                                    </table>
                                    <p class="mt-2 mb-0">Halaman kelas juga menampilkan jumlah siswa per kelas menggunakan LEFT JOIN query.</p>
                                </div>
                            </div>
                        </div>

                        <!-- CRUD Mapel -->
                        <div id="crud-mapel" class="doc-section mb-5">
                            <h4 class="mb-3 text-primary"><i class="fas fa-book me-2"></i>Manajemen Mata Pelajaran</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Tabel: <code>mata_pelajaran</code></h6>
                                    <table class="table table-sm table-bordered w-50">
                                        <tr><th>Field</th><th>Keterangan</th></tr>
                                        <tr><td>id</td><td>Primary Key (auto increment)</td></tr>
                                        <tr><td>kode_mapel</td><td>Kode unik mapel</td></tr>
                                        <tr><td>nama_mapel</td><td>Nama mata pelajaran</td></tr>
                                    </table>
                                    <p class="mt-2 mb-0">CRUD sederhana untuk mengelola data mata pelajaran.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Register Wajah -->
                        <div id="register-face" class="doc-section mb-5">
                            <h4 class="mb-3 text-primary"><i class="fas fa-id-card me-2"></i>Registrasi Wajah Siswa</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Flow Proses:</h6>
                                    <div class="bg-white p-3 rounded border">
                                        <ol>
                                            <li>Pilih siswa dari dropdown</li>
                                            <li>Start kamera (webcam access)</li>
                                            <li>Capture 5 foto wajah siswa</li>
                                            <li>Preview 5 foto (bisa hapus & ulang)</li>
                                            <li>Klik "Simpan Data Wajah"</li>
                                            <li>PHP mengirim 5 base64 images ke <code>api/register_face.php</code></li>
                                            <li>PHP → FastAPI <code>POST /api/register</code></li>
                                            <li>FastAPI Process:
                                                <ul>
                                                    <li>Decode 5 images</li>
                                                    <li>Detect face dengan face_recognition</li>
                                                    <li>Generate 128-d encodings</li>
                                                    <li>Average semua encodings</li>
                                                    <li>Simpan ke <code>face_data/{student_id}/</code></li>
                                                    <li>Update database: <code>face_registered=1</code></li>
                                                </ul>
                                            </li>
                                        </ol>
                                    </div>

                                    <h6 class="mt-3">Penyimpanan File:</h6>
                                    <ul>
                                        <li><code>face_data/{student_id}/face_1.jpg</code> s/d <code>face_5.jpg</code> - Foto mentah</li>
                                        <li><code>face_data/{student_id}/encoding.npy</code> - Backup encoding (numpy)</li>
                                        <li><code>uploads/siswa/siswa_{student_id}.jpg</code> - Foto profil</li>
                                    </ul>

                                    <div class="alert alert-warning">
                                        <strong>Penting:</strong> Pastikan pencahayaan cukup dan wajah terlihat jelas. Wajah harus menghadap ke kamera.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Absensi Face ID -->
                        <div id="absensi-face" class="doc-section mb-5">
                            <h4 class="mb-3 text-primary"><i class="fas fa-camera me-2"></i>Absensi Face Recognition</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Flow Proses:</h6>
                                    <div class="bg-white p-3 rounded border">
                                        <ol>
                                            <li>Pilih kelas dan mata pelajaran</li>
                                            <li>Start kamera (webcam access)</li>
                                            <li>Siswa memposisikan wajah di frame</li>
                                            <li>Klik "Absen Sekarang"</li>
                                            <li>Capture image dari webcam (base64)</li>
                                            <li>Kirim ke <code>api/recognize.php</code></li>
                                            <li>PHP → FastAPI <code>POST /api/recognize</code></li>
                                            <li>FastAPI Process:
                                                <ul>
                                                    <li>Decode base64 image</li>
                                                    <li>Detect face</li>
                                                    <li>Generate encoding</li>
                                                    <li>Compare dengan semua encoding tersimpan</li>
                                                    <li>Jika distance < 0.6 (threshold), return match</li>
                                                </ul>
                                            </li>
                                            <li>Cek duplikasi absensi (hari ini, siswa ini, mapel ini)</li>
                                            <li>Simpan foto ke <code>uploads/absensi/</code></li>
                                            <li>INSERT ke tabel <code>absensi</code> dengan <code>metode='face_recognition'</code></li>
                                            <li>Tampilkan hasil: nama, kelas, confidence score</li>
                                        </ol>
                                    </div>

                                    <div class="alert alert-info">
                                        <strong>Minimum Confidence Threshold: 70%</strong>
                                        <p class="mb-0">Absensi hanya akan berhasil jika tingkat kecocokan wajah minimal 70%. Jika confidence di bawah 70%, sistem akan menolak dan memberikan pesan error.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Absensi Manual -->
                        <div id="absensi-manual" class="doc-section mb-5">
                            <h4 class="mb-3 text-primary"><i class="fas fa-clipboard-check me-2"></i>Absensi Manual</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Flow Proses:</h6>
                                    <ol>
                                        <li>Pilih kelas dan tanggal</li>
                                        <li>JavaScript panggil <code>api/get_students.php</code></li>
                                        <li>Tampilkan daftar siswa dengan dropdown status</li>
                                        <li>User pilih status untuk setiap siswa:
                                            <ul>
                                                <li><span class="badge bg-success">Hadir</span> - Hadir di sekolah</li>
                                                <li><span class="badge bg-warning">Izin</span> - Izin (dengan keterangan)</li>
                                                <li><span class="badge bg-info">Sakit</span> - Sakit (dengan keterangan)</li>
                                                <li><span class="badge bg-danger">Alpha</span> - Tanpa keterangan</li>
                                            </ul>
                                        </li>
                                        <li>Submit form (batch processing)</li>
                                        <li>Loop setiap siswa dan simpan/update absensi</li>
                                    </ol>

                                    <h6 class="mt-3">Query Cek Existing:</h6>
                                    <pre class="bg-dark text-light p-2 rounded">SELECT * FROM absensi
WHERE siswa_id = ? AND kelas_id = ? AND tanggal = ?</pre>
                                </div>
                            </div>
                        </div>

                        <!-- Laporan -->
                        <div id="laporan" class="doc-section mb-5">
                            <h4 class="mb-3 text-primary"><i class="fas fa-chart-bar me-2"></i>Laporan Absensi</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Fitur:</h6>
                                    <ul>
                                        <li>Filter berdasarkan kelas</li>
                                        <li>Filter rentang tanggal</li>
                                        <li>Menampilkan semua kolom absensi dengan detail</li>
                                        <li>Distinguishes antara Face Recognition dan Manual method</li>
                                        <li>Fungsi print</li>
                                    </ul>

                                    <h6 class="mt-3">Query:</h6>
                                    <pre class="bg-dark text-light p-2 rounded">SELECT a.*, s.nama_lengkap, s.nis, k.nama_kelas, m.nama_mapel
FROM absensi a
JOIN siswa s ON a.siswa_id = s.id
JOIN kelas k ON a.kelas_id = k.id
JOIN mata_pelajaran m ON a.mapel_id = m.id
WHERE k.id = ? AND a.tanggal BETWEEN ? AND ?
ORDER BY a.tanggal DESC</pre>
                                </div>
                            </div>
                        </div>

                        <!-- Rekap -->
                        <div id="rekap" class="doc-section mb-5">
                            <h4 class="mb-3 text-primary"><i class="fas fa-file-alt me-2"></i>Rekap Absensi Bulanan</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Fitur:</h6>
                                    <ul>
                                        <li>Filter berdasarkan kelas, bulan, tahun</li>
                                        <li>Summary per siswa: hadir, izin, sakit, alpha</li>
                                        <li>Hitung persentase kehadiran</li>
                                        <li>Visual progress bar</li>
                                    </ul>

                                    <h6 class="mt-3">Perhitungan Persentase:</h6>
                                    <pre class="bg-dark text-light p-2 rounded">persentase_hadir = (hadir / total) * 100</pre>

                                    <h6 class="mt-3">Color Coding:</h6>
                                    <ul>
                                        <li><span class="badge bg-success">Hijau</span> - > 80% hadir</li>
                                        <li><span class="badge bg-warning">Kuning</span> - 60-80% hadir</li>
                                        <li><span class="badge bg-danger">Merah</span> - < 60% hadir</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Pengaturan -->
                        <div id="pengaturan" class="doc-section mb-5">
                            <h4 class="mb-3 text-primary"><i class="fas fa-cog me-2"></i>Pengaturan Sistem</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>Pengaturan Sekolah:</h6>
                                    <ul>
                                        <li>Nama sekolah</li>
                                        <li>Alamat sekolah</li>
                                        <li>Jam masuk default</li>
                                        <li>Jam pulang default</li>
                                        <li>Batas terlambat (menit)</li>
                                    </ul>

                                    <h6 class="mt-3">Ganti Password Admin:</h6>
                                    <ol>
                                        <li>Masukkan password lama (diverifikasi dengan <code>password_verify()</code>)</li>
                                        <li>Masukkan password baru (min 6 karakter)</li>
                                        <li>Konfirmasi password baru</li>
                                        <li>Password di-hash dengan <code>password_hash()</code></li>
                                        <li>Update ke tabel <code>admin</code></li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <!-- API Endpoints -->
                        <div id="api" class="doc-section mb-5">
                            <h4 class="mb-3 text-primary"><i class="fas fa-code me-2"></i>API Endpoints</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6>PHP API (<code>api/</code> folder):</h6>
                                    <table class="table table-sm table-bordered">
                                        <tr>
                                            <th>Endpoint</th>
                                            <th>Method</th>
                                            <th>Parameters</th>
                                            <th>Deskripsi</th>
                                        </tr>
                                        <tr>
                                            <td><code>api/recognize.php</code></td>
                                            <td>POST</td>
                                            <td>image, kelas_id, mapel_id, tanggal</td>
                                            <td>Face recognition & record attendance</td>
                                        </tr>
                                        <tr>
                                            <td><code>api/register_face.php</code></td>
                                            <td>POST</td>
                                            <td>student_id, images[] (5 base64)</td>
                                            <td>Register student face</td>
                                        </tr>
                                        <tr>
                                            <td><code>api/get_students.php</code></td>
                                            <td>GET</td>
                                            <td>kelas_id, tanggal</td>
                                            <td>Get students with attendance status</td>
                                        </tr>
                                    </table>

                                    <h6 class="mt-3">FastAPI Endpoints (Python):</h6>
                                    <table class="table table-sm table-bordered">
                                        <tr>
                                            <th>Endpoint</th>
                                            <th>Method</th>
                                            <th>Deskripsi</th>
                                        </tr>
                                        <tr>
                                            <td><code>GET /</code></td>
                                            <td>GET</td>
                                            <td>Health check</td>
                                        </tr>
                                        <tr>
                                            <td><code>GET /api/health</code></td>
                                            <td>GET</td>
                                            <td>Status API + database</td>
                                        </tr>
                                        <tr>
                                            <td><code>POST /api/recognize</code></td>
                                            <td>POST</td>
                                            <td>Recognize face from base64 image</td>
                                        </tr>
                                        <tr>
                                            <td><code>POST /api/register</code></td>
                                            <td>POST</td>
                                            <td>Register face (multiple images)</td>
                                        </tr>
                                    </table>

                                    <p class="mt-2 mb-0"><strong>Swagger UI:</strong> <a href="http://localhost:8000/docs" target="_blank">http://localhost:8000/docs</a></p>
                                </div>
                            </div>
                        </div>

                        <!-- Troubleshooting -->
                        <div id="troubleshooting" class="doc-section mb-5">
                            <h4 class="mb-3 text-primary"><i class="fas fa-wrench me-2"></i>Troubleshooting</h4>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Problem</th>
                                                <th>Solution</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><code>Failed to connect to Face Recognition server</code></td>
                                                <td>Pastikan FastAPI server (port 8000) sedang berjalan</td>
                                            </tr>
                                            <tr>
                                                <td><code>Please install face_recognition_models</code></td>
                                                <td>Jalankan: <code>pip install "setuptools<75"</code> lalu <code>pip install -r requirements.txt</code></td>
                                            </tr>
                                            <tr>
                                                <td><code>ModuleNotFoundError: No module named 'pkg_resources'</code></td>
                                                <td>Downgrade setuptools: <code>pip install "setuptools<75"</code></td>
                                            </tr>
                                            <tr>
                                                <td><code>dlib install failed</code></td>
                                                <td>Install Visual C++ Build Tools, atau gunakan prebuilt wheel</td>
                                            </tr>
                                            <tr>
                                                <td>Port 8000 already in use</td>
                                                <td>Ganti port uvicorn dan sesuaikan <code>FASTAPI_URL</code> di config</td>
                                            </tr>
                                            <tr>
                                                <td>Wajah tidak dikenali</td>
                                                <td>Pastikan pencahayaan cukup, wajah terlihat jelas, atau daftarkan ulang wajah</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <h6 class="mt-3">Security Measures:</h6>
                                    <ul>
                                        <li>Password hashing dengan bcrypt</li>
                                        <li>SQL Injection protection dengan <code>mysqli_real_escape_string()</code></li>
                                        <li>Session protection di setiap halaman</li>
                                        <li>Directory protection dengan <code>.htaccess</code></li>
                                        <li>Base64 image processing (no direct file upload)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.doc-section {
    scroll-margin-top: 100px;
}
#docNav .list-group-item {
    border: none;
    border-left: 3px solid transparent;
}
#docNav .list-group-item:hover {
    background: #f0f2f5;
    border-left-color: #667eea;
}
#docNav .list-group-item.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-left-color: #667eea;
}
</style>

<script>
// Smooth scroll for documentation nav
document.querySelectorAll('#docNav a').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// Active nav highlight on scroll
window.addEventListener('scroll', function() {
    const sections = document.querySelectorAll('.doc-section');
    const navItems = document.querySelectorAll('#docNav a');

    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        if (scrollY >= sectionTop - 150) {
            current = section.getAttribute('id');
        }
    });

    navItems.forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('href') === '#' + current) {
            item.classList.add('active');
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
