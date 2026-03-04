# Sistem Absensi Siswa dengan Face Recognition

## 📋 Deskripsi

Sistem absensi siswa berbasis web menggunakan teknologi pengenalan wajah (Face Recognition).
Dibangun dengan **PHP Native** untuk backend web, **Python FastAPI** untuk API face recognition,
dan **MediaPipe + face_recognition** untuk deteksi dan pengenalan wajah.

## 🛠️ Tech Stack

| Komponen                  | Teknologi                                      |
| ------------------------- | ---------------------------------------------- |
| **Web Backend**           | PHP Native                                     |
| **Database**              | MySQL (Laragon / phpMyAdmin)                   |
| **Frontend**              | Bootstrap 5, Chart.js, DataTables, SweetAlert2 |
| **Face Recognition API**  | Python FastAPI                                 |
| **Face Recognition**      | face_recognition (dlib)                        |
| **Image Processing**      | OpenCV Python                                  |
| **DB Connector (Python)** | mysql-connector-python                         |

## 📁 Struktur Folder

```
php_native_absensi_siswa_face_recognition/
├── api/                    # API endpoints (PHP → memanggil FastAPI)
│   ├── get_students.php    # Get students by class
│   ├── recognize.php       # Face recognition (via FastAPI)
│   └── register_face.php   # Register face (via FastAPI)
├── config/
│   └── database.php        # Database + FastAPI URL configuration
├── database/
│   └── db_absensi_siswa.sql # SQL file (import ke phpMyAdmin)
├── face_data/              # Stored face images & encodings
├── includes/
│   ├── header.php          # Template header + sidebar
│   └── footer.php          # Template footer + scripts
├── pages/
│   ├── dashboard.php       # Dashboard utama
│   ├── siswa.php           # CRUD data siswa
│   ├── kelas.php           # CRUD data kelas
│   ├── mapel.php           # CRUD mata pelajaran
│   ├── absensi.php         # Absensi via face recognition
│   ├── absensi_manual.php  # Absensi manual
│   ├── register_face.php   # Registrasi wajah siswa
│   ├── laporan.php         # Laporan absensi
│   ├── rekap.php           # Rekap absensi per bulan
│   ├── pengaturan.php      # Pengaturan sistem
│   └── logout.php          # Logout
├── python/
│   ├── main.py             # ⭐ FastAPI Server (endpoint utama)
│   ├── register_face.py    # Script registrasi wajah (standalone)
│   ├── recognize_face.py   # Script pengenalan wajah (standalone)
│   └── requirements.txt    # Python dependencies
├── temp/                   # Temporary files
├── uploads/                # Uploaded files
│   ├── siswa/              # Foto profil siswa
│   └── absensi/            # Foto saat absensi
├── .htaccess               # Apache security config
├── index.php               # Entry point
├── login.php               # Halaman login
├── start_fastapi.bat       # ⭐ Script untuk start FastAPI server (Windows)
└── README.md               # Dokumentasi
```

## 🖥️ Menjalankan Aplikasi

### Metode 1: Dua Terminal (Recommended)

Buka **2 terminal** secara bersamaan:

| Terminal | Command | Port |
|----------|---------|------|
| Terminal 1 | `cd python && python -m uvicorn main:app --host 0.0.0.0 --port 8000 --reload` | 8000 (FastAPI) |
| Terminal 2 | `php -S localhost:8001` | 8001 (PHP Web) |

### Metode 2: Laragon + FastAPI Manual

1. **Laragon**: Start Apache (PHP otomatis berjalan di port 80)
2. **Terminal**: Jalankan FastAPI server di port 8000
3. Sesuaikan `BASE_URL` dan `FASTAPI_URL` di `config/database.php`

### Metode 3: Batch File (Windows Only)

Double-click `start_fastapi.bat` untuk menjalankan FastAPI server secara otomatis.
```

## 🏗️ Arsitektur Sistem

```
┌─────────────┐     HTTP/cURL      ┌──────────────────┐
│   Browser   │ ←──────────────→   │   PHP (Laragon)  │
│  (Webcam)   │                    │  Port 80/Apache  │
└─────────────┘                    └────────┬─────────┘
                                            │ cURL (JSON)
                                            ▼
                                   ┌──────────────────┐
                                   │  FastAPI (Python) │
                                   │   Port 8000      │
                                   │  - /api/recognize │
                                   │  - /api/register  │
                                   └────────┬─────────┘
                                            │
                          ┌─────────────────┼─────────────────┐
                          ▼                 ▼                 ▼
                    ┌───────────┐   ┌────────────┐   ┌──────────────┐
                    │ MediaPipe │   │    dlib/    │   │    MySQL     │
                    │  (detect) │   │face_recog. │   │  (database)  │
                    └───────────┘   └────────────┘   └──────────────┘
```

## 🚀 Cara Instalasi

### Prasyarat

- **PHP 7.4+** (Laragon / XAMPP / PHP built-in server)
- **Python 3.8+** (tested with Python 3.12)
- **MySQL** (phpMyAdmin untuk import database)
- **pip** (Python package manager)
- **C++ Build Tools** (Windows: Visual Studio Build Tools untuk compile dlib)

### Langkah 1: Setup Database

1. Buka **phpMyAdmin** (http://localhost/phpmyadmin)
2. Import file `database/db_absensi_siswa.sql`
   - Klik **Import** → Choose File → pilih `db_absensi_siswa.sql` → **Go**

### Langkah 2: Setup PHP

1. Letakkan folder project di web server directory:
   - **Laragon**: `C:\laragon\www\php_native_absensi_siswa_face_recognition\`
   - **XAMPP**: `C:\xampp\htdocs\php_native_absensi_siswa_face_recognition\`
   - Atau folder lainnya (sesuaikan `BASE_URL` di `config/database.php`)

2. Pastikan extension PHP berikut aktif di `php.ini`:
   - `extension=gd`
   - `extension=mysqli`
   - `extension=curl` ← **PENTING untuk koneksi ke FastAPI**

### Langkah 3: Setup Python & Dependencies

> **PENTING**: Gunakan Python 3.8 - 3.12 untuk kompatibilitas terbaik.

```bash
cd python
pip install -r requirements.txt
```

**Jika terjadi error saat instalasi:**

1. **Error `pkg_resources` tidak ditemukan:**
   ```bash
   pip install "setuptools<75"
   ```

2. **Error `face_recognition` atau `dlib` gagal install (Windows):**
   ```bash
   # Install Visual C++ Build Tools terlebih dahulu
   # Lalu jalankan:
   pip install cmake dlib
   pip install face_recognition
   ```

3. **Gunakan prebuilt wheel untuk dlib (Windows):**
   ```bash
   # Untuk Python 3.12
   pip install https://github.com/z-mahmud22/Dlib_Windows_Python3.x/raw/main/dlib-19.24.1-cp312-cp312-win_amd64.whl
   ```

### Langkah 4: Jalankan Kedua Server

Sistem ini memerlukan **2 server yang berjalan bersamaan**:

#### Terminal 1 - FastAPI Server (Python)
```bash
cd python
python -m uvicorn main:app --host 0.0.0.0 --port 8000 --reload
```

> Server berjalan di **http://localhost:8000**
> Dokumentasi API Swagger: **http://localhost:8000/docs**

#### Terminal 2 - PHP Web Server
```bash
# Opsi 1: PHP built-in server (untuk development)
php -S localhost:8001

# Opsi 2: Gunakan Laragon/XAMPP Apache (otomatis)
```

> PHP web berjalan di **http://localhost:8001** atau sesuai konfigurasi web server

### Langkah 5: Verifikasi & Akses

1. **Cek FastAPI:** Buka http://localhost:8000/docs
   - Harus muncul halaman Swagger UI

2. **Cek Health API:**
   ```bash
   curl http://localhost:8000/api/health
   ```
   Response: `{"status": "ok", "database": "connected", ...}`

3. **Akses Website:** Buka http://localhost:8001/login.php
   - Atau sesuai BASE_URL di konfigurasi

### Troubleshooting

| Masalah | Solusi |
| ------- | ------ |
| `Failed to connect to Face Recognition server` | Pastikan FastAPI server (port 8000) sedang berjalan |
| `Please install face_recognition_models` | Jalankan: `pip install "setuptools<75"` lalu `pip install -r requirements.txt` |
| `ModuleNotFoundError: No module named 'pkg_resources'` | Downgrade setuptools: `pip install "setuptools<75"` |
| `dlib install failed` | Install Visual C++ Build Tools, atau gunakan prebuilt wheel |
| Port 8000 already in use | Ganti port: `python -m uvicorn main:app --port 8001` dan sesuaikan `FASTAPI_URL` di config |

## 🔐 Login Default

| Field        | Value      |
| ------------ | ---------- |
| **Username** | `admin`    |
| **Password** | `admin123` |

## 📌 Fitur

- ✅ **Dashboard** - Statistik dan ringkasan absensi
- ✅ **Absensi Face Recognition** - Absensi otomatis via webcam dengan modal feedback
- ✅ **Absensi Manual** - Input absensi manual per kelas
- ✅ **Register Wajah** - Pendaftaran wajah siswa (5 foto) dengan modal feedback
- ✅ **Hapus Data Wajah** - Hapus registrasi wajah individu atau bulk delete semua
- ✅ **Data Siswa** - CRUD data siswa
- ✅ **Data Kelas** - CRUD data kelas
- ✅ **Mata Pelajaran** - CRUD data mapel
- ✅ **Laporan** - Laporan absensi dengan filter
- ✅ **Rekap** - Rekap absensi per bulan dengan persentase
- ✅ **Pengaturan** - Setting sekolah & ubah password

### 🆕 Fitur Terbaru (v1.1.0 - Feb 11, 2026)

#### **Enhanced Modal System**
- **Rich Feedback Modals**: Semua operasi absensi dan registrasi menggunakan modal popup yang informatif
- **Color-coded Badges**: Badge confidence dengan warna (hijau ≥80%, kuning 70-79%, merah <70%)
- **Detailed Information**: Menampilkan NIS, kelas, waktu, dan tips troubleshooting
- **Professional Design**: Gradient cards, icon indicators, responsive layout

#### **Face Registration Management**
- **Individual Delete**: Hapus data wajah per siswa dengan konfirmasi modal
- **Bulk Delete**: Hapus semua data wajah sekaligus dengan double confirmation
- **Smart Cleanup**: Otomatis hapus file .pkl dan update database
- **Statistics Display**: Menampilkan jumlah data terhapus dan status file

📖 **Dokumentasi Lengkap**: Lihat [`docs/FACE_DELETE_FEATURE.md`](docs/FACE_DELETE_FEATURE.md)

## ⚙️ Cara Kerja Face Recognition

1. **Registrasi**: Ambil 5 foto wajah siswa → dikirim ke FastAPI `/api/register` → MediaPipe mendeteksi wajah →
   `face_recognition` menghasilkan encoding 128-dimensi → disimpan di database MySQL
2. **Pengenalan**: Foto dari webcam → dikirim ke FastAPI `/api/recognize` → MediaPipe validasi ada wajah →
   `face_recognition` menghasilkan encoding → dibandingkan dengan semua encoding tersimpan →
   jika confidence >= 70% (minimum threshold) maka dikenali → PHP mencatat absensi ke database

> **Minimum Confidence Threshold: 70%**
> Absensi hanya akan berhasil jika tingkat kecocokan wajah minimal 70%. Jika di bawah 70%, sistem akan menolak dan memberikan pesan bahwa wajah tidak cocok.

## 🔌 FastAPI Endpoints

| Method | Endpoint         | Deskripsi                              |
| ------ | ---------------- | -------------------------------------- |
| GET    | `/`              | Health check                           |
| GET    | `/api/health`    | Status API + database                  |
| POST   | `/api/recognize` | Recognize wajah dari base64 image      |
| POST   | `/api/register`  | Register wajah siswa (multiple images) |

> Swagger UI: **http://localhost:8000/docs**

## 🔧 Konfigurasi

File konfigurasi utama: `config/database.php`

```php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_absensi_siswa');

// Base URL (sesuaikan dengan lokasi project)
define('BASE_URL', 'http://localhost:8001/');

// FastAPI Python Server URL
define('FASTAPI_URL', 'http://localhost:8000');
```

**Jika mengubah port FastAPI:**
1. Ubah port saat menjalankan uvicorn: `--port 8001`
2. Ubah `FASTAPI_URL` di `config/database.php`

## 📝 Catatan

- MediaPipe digunakan untuk **deteksi wajah** (menentukan ada/tidaknya wajah)
- `face_recognition` (dlib) digunakan untuk **pengenalan wajah** (mengidentifikasi siapa)
- Keduanya saling melengkapi untuk hasil yang lebih akurat
- Threshold default: 0.6 (dapat disesuaikan di `python/main.py`)
- PHP berkomunikasi dengan Python melalui **HTTP/cURL** ke FastAPI (bukan shell_exec)
- Pastikan **FastAPI server harus berjalan** sebelum menggunakan fitur face recognition

---

## 📖 Dokumentasi Proses

### 1. Autentikasi & Sesi

#### Login Process (`login.php`)
| Langkah | Deskripsi |
| ------- | --------- |
| 1 | User memasukkan username & password |
| 2 | PHP memquery tabel `admin` berdasarkan username |
| 3 | Password diverifikasi menggunakan `password_verify()` |
| 4 | Jika valid, buat session variables |
| 5 | Redirect ke `pages/dashboard.php` |

**Session Variables yang dibuat:**
```php
$_SESSION['admin_id']       // ID admin yang login
$_SESSION['admin_username'] // Username admin
$_SESSION['admin_nama']     // Nama lengkap admin
```

**File terkait:** `login.php`, `config/database.php` (session_start), `includes/header.php` (session check)

#### Logout Process (`pages/logout.php`)
| Langkah | Deskripsi |
| ------- | --------- |
| 1 | User klik tombol logout |
| 2 | Panggil `session_destroy()` untuk hapus semua session |
| 3 | Redirect ke halaman `login.php` |

#### Session Protection
Setiap halaman di `pages/` dilindungi oleh `includes/header.php` yang mengecek:
```php
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}
```

---

### 2. CRUD Data Siswa (`pages/siswa.php`)

**Tabel Database:** `siswa`

**Fields:**
- `id` - Primary key (auto increment)
- `nis` - Nomor Induk Siswa (unique)
- `nama_lengkap` - Nama lengkap siswa
- `jenis_kelamin` - L/P
- `kelas_id` - Foreign key ke tabel kelas
- `alamat` - Alamat siswa
- `no_telepon` - Nomor telepon
- `face_registered` - 0/1 (status registrasi wajah)
- `status` - aktif/tidak aktif
- `foto` - Nama file foto
- `face_encoding` - JSON encoding wajah

**Operasi CRUD:**

| Operasi | Method | Query | Validasi |
| ------- | ------ | ----- | -------- |
| **Create** | POST | INSERT INTO siswa | `mysqli_real_escape_string()` semua input |
| **Read** | GET | SELECT JOIN kelas | Menampilkan data dengan nama kelas |
| **Update** | POST | UPDATE siswa | Cek NIS duplikat (kecuali ID sama) |
| **Delete** | GET | DELETE FROM siswa | Hapus data berdasarkan ID |

**Fitur Tambahan:**
- Badge "Face Registered" jika `face_registered = 1`
- Dropdown pilihan kelas dari tabel `kelas`
- Upload foto profil (disimpan di `uploads/siswa/`)

---

### 3. CRUD Data Kelas (`pages/kelas.php`)

**Tabel Database:** `kelas`

**Fields:**
- `id` - Primary key
- `nama_kelas` - Nama kelas (mis: X IPA 1)
- `jurusan` - Jurusan
- `tahun_ajaran` - Tahun ajaran

**Query dengan JOIN:**
```sql
SELECT k.*, COUNT(s.id) as jumlah_siswa
FROM kelas k
LEFT JOIN siswa s ON k.id = s.kelas_id
GROUP BY k.id
```

---

### 4. CRUD Mata Pelajaran (`pages/mapel.php`)

**Tabel Database:** `mata_pelajaran`

**Fields:**
- `id` - Primary key
- `kode_mapel` - Kode unik mapel
- `nama_mapel` - Nama mata pelajaran

---

### 5. Absensi Face Recognition (`pages/absensi.php`)

**Flow Lengkap:**

```
┌─────────────────┐
│ User buka halaman│
│ absensi.php     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Pilih Kelas &   │
│ Mata Pelajaran  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Start Kamera    │
│ (Webcam Access) │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Klik "Absen     │
│ Sekarang"       │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Capture image   │
│ dari webcam     │
│ (base64)        │
└────────┬────────┘
         │
         ▼
┌─────────────────────────┐
│ Kirim ke api/recognize  │
│ (image, kelas_id,       │
│  mapel_id, tanggal)     │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ PHP → FastAPI (cURL)    │
│ POST /api/recognize     │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ FastAPI Process:        │
│ 1. Decode base64        │
│ 2. Detect face          │
│ 3. Generate encoding    │
│ 4. Compare all faces    │
│ 5. Return match + score │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Cek duplikasi absensi   │
│ (hari ini, siswa ini,   │
│  mapel ini)             │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Simpan foto & record:   │
│ - uploads/absensi/      │
│ - tabel absensi         │
│ metode='face_recognition'│
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Tampilkan hasil:        │
│ - Nama siswa            │
│ - Kelas                 │
│ - Confidence score      │
└─────────────────────────┘
```

**Database Insert:**
```sql
INSERT INTO absensi (
    siswa_id, kelas_id, mapel_id, tanggal, jam_masuk,
    status, metode_absen, foto_absen, confidence_score
) VALUES (?, ?, ?, ?, NOW(), 'hadir', 'face_recognition', ?, ?)
```

**API Endpoint:** `api/recognize.php`

---

### 6. Absensi Manual (`pages/absensi_manual.php`)

**Flow:**

| Langkah | Deskripsi |
| ------- | --------- |
| 1 | Pilih kelas dan tanggal |
| 2 | JavaScript panggil `api/get_students.php` |
| 3 | Tampilkan daftar siswa dengan dropdown status |
| 4 | User pilih status (Hadir/Izin/Sakit/Alpha) |
| 5 | Submit form (batch processing) |
| 6 | Loop setiap siswa dan simpan/update absensi |

**Status Options:**
- `hadir` - Hadir di sekolah
- `izin` - Izin (dengan keterangan)
- `sakit` - Sakit (dengan keterangan)
- `alpha` - Tanpa keterangan

**API Endpoint:** `api/get_students.php`

**Query untuk cek absensi existing:**
```sql
SELECT * FROM absensi
WHERE siswa_id = ? AND kelas_id = ? AND tanggal = ?
```

---

### 7. Registrasi Wajah (`pages/register_face.php`)

**Flow Lengkap:**

```
┌─────────────────┐
│ Pilih Siswa     │
│ dari dropdown   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Start Kamera    │
│ & preview       │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Capture 5 foto  │
│ (tombol Capture)│
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ Preview 5 foto  │
│ (bisa hapus &   │
│  ulang)         │
└────────┬────────┘
         │
         ▼
┌─────────────────────────┐
│ Kirim ke api/register_   │
│ face.php:               │
│ - student_id            │
│ - images[] (5 base64)   │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ PHP → FastAPI (cURL)    │
│ POST /api/register      │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ FastAPI Process:        │
│ 1. Decode 5 images      │
│ 2. Detect face each     │
│ 3. Generate encodings   │
│ 4. Average encodings    │
│ 5. Save to face_data/   │
│ 6. Update DB:           │
│    - face_registered=1  │
│    - face_encoding=JSON │
│    - foto=photo.jpg     │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│ Return success/failed   │
└─────────────────────────┘
```

**Penyimpanan File:**
- `face_data/{student_id}/face_1.jpg` s/d `face_5.jpg` - Foto mentah
- `face_data/{student_id}/encoding.npy` - Backup encoding (numpy)
- `uploads/siswa/siswa_{student_id}.jpg` - Foto profil

**Database Update (via Python):**
```sql
UPDATE siswa
SET face_registered = 1,
    face_encoding = ?,
    foto = ?
WHERE id = ?
```

---

### 8. Laporan Absensi (`pages/laporan.php`)

**Filter Options:**
- Kelas
- Rentang tanggal (dari - sampai)

**Query JOIN:**
```sql
SELECT a.*, s.nama_lengkap, s.nis, s.foto as foto_siswa,
       k.nama_kelas, m.nama_mapel
FROM absensi a
JOIN siswa s ON a.siswa_id = s.id
JOIN kelas k ON a.kelas_id = k.id
JOIN mata_pelajaran m ON a.mapel_id = m.id
WHERE k.id = ? AND a.tanggal BETWEEN ? AND ?
ORDER BY a.tanggal DESC, a.jam_masuk DESC
```

**Display Columns:**
- Tanggal & Jam
- Nama Siswa & NIS
- Kelas & Mapel
- Status (Hadir/Izin/Sakit/Alpha)
- Metode (Face Recognition / Manual)
- Confidence Score (untuk face recognition)

---

### 9. Rekap Absensi Bulanan (`pages/rekap.php`)

**Query Summary:**
```sql
SELECT s.id, s.nis, s.nama_lengkap, k.nama_kelas,
    SUM(CASE WHEN a.status = 'hadir' THEN 1 ELSE 0 END) as hadir,
    SUM(CASE WHEN a.status = 'izin' THEN 1 ELSE 0 END) as izin,
    SUM(CASE WHEN a.status = 'sakit' THEN 1 ELSE 0 END) as sakit,
    SUM(CASE WHEN a.status = 'alpha' THEN 1 ELSE 0 END) as alpha,
    COUNT(a.id) as total
FROM siswa s
LEFT JOIN kelas k ON s.kelas_id = k.id
LEFT JOIN absensi a ON s.id = a.siswa_id
    AND MONTH(a.tanggal) = ? AND YEAR(a.tanggal) = ?
WHERE s.kelas_id = ?
GROUP BY s.id
```

**Perhitungan Persentase:**
```
persentase_hadir = (hadir / total) * 100
```

**Visualisasi:**
- Progress bar untuk persentase kehadiran
- Color coding: Hijau (>80%), Kuning (60-80%), Merah (<60%)

---

### 10. Pengaturan Sistem (`pages/pengaturan.php`)

**Tabel Database:** `pengaturan` (single row, id=1)

**Fields:**
- `nama_sekolah` - Nama sekolah
- `alamat_sekolah` - Alamat lengkap
- `logo` - Nama file logo
- `jam_masuk` - Jam masuk default (mis: 07:00)
- `jam_pulang` - Jam pulang default (mis: 15:00)
- `batas_terlambat` - Batas waktu terlambat (menit)

**Update Data Sekolah:**
```sql
UPDATE pengaturan
SET nama_sekolah = ?, alamat_sekolah = ?, jam_masuk = ?,
    jam_pulang = ?, batas_terlambat = ?
WHERE id = 1
```

**Ganti Password Admin:**
```php
// 1. Verifikasi password lama
SELECT * FROM admin WHERE id = ?
password_verify($old_password, $db_password)

// 2. Hash password baru
$password_hash = password_hash($new_password, PASSWORD_DEFAULT);

// 3. Update database
UPDATE admin SET password = ? WHERE id = ?
```

**Validasi Password:**
- Minimum 6 karakter
- Password lama harus cocok
- Konfirmasi password baru harus sama

---

### 11. PHP API Endpoints

**`api/recognize.php`**
- Method: POST
- Parameters: `image` (base64), `kelas_id`, `mapel_id`, `tanggal`
- Process:
  1. Decode base64 image
  2. Send to FastAPI `/api/recognize`
  3. Check duplicate attendance
  4. Save attendance photo
  5. Insert into database
- Response: JSON `{success, message, data}`

**`api/register_face.php`**
- Method: POST
- Parameters: `student_id`, `images[]` (5 base64 images)
- Process:
  1. Send to FastAPI `/api/register`
  2. Python handles face detection & encoding
  3. Return result
- Response: JSON `{success, message}`

**`api/get_students.php`**
- Method: GET
- Parameters: `kelas_id`, `tanggal`
- Process:
  1. Query students by class
  2. Check existing attendance for date
  3. Return student list with status
- Response: JSON array of students

---

### 12. Security Measures

| Security Measure | Implementation |
| ---------------- | -------------- |
| **Password Hashing** | `password_hash()` dengan PASSWORD_DEFAULT (bcrypt) |
| **SQL Injection** | `mysqli_real_escape_string()` untuk semua input |
| **Session Protection** | Cek `$_SESSION['admin_id']` di setiap halaman |
| **Directory Protection** | `.htaccess` blokir akses ke config/, python/, face_data/, temp/ |
| **File Upload** | Base64 processing (no direct file upload) |
| **cURL Communication** | Timeout settings, error handling untuk FastAPI calls |

---

### 13. Database Schema

**Tabel Utama:**

| Tabel | Primary Key | Foreign Keys |
| ----- | ----------- | ------------ |
| `admin` | id | - |
| `kelas` | id | - |
| `siswa` | id | kelas_id → kelas.id |
| `mata_pelajaran` | id | - |
| `absensi` | id | siswa_id → siswa.id, kelas_id → kelas.id, mapel_id → mata_pelajaran.id |
| `pengaturan` | id | - |

**Relasi:**
```
kelas (1) ←→ (N) siswa
siswa (1) ←→ (N) absensi
kelas (1) ←→ (N) absensi
mata_pelajaran (1) ←→ (N) absensi
```
