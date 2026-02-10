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
- ✅ **Absensi Face Recognition** - Absensi otomatis via webcam
- ✅ **Absensi Manual** - Input absensi manual per kelas
- ✅ **Register Wajah** - Pendaftaran wajah siswa (5 foto)
- ✅ **Data Siswa** - CRUD data siswa
- ✅ **Data Kelas** - CRUD data kelas
- ✅ **Mata Pelajaran** - CRUD data mapel
- ✅ **Laporan** - Laporan absensi dengan filter
- ✅ **Rekap** - Rekap absensi per bulan dengan persentase
- ✅ **Pengaturan** - Setting sekolah & ubah password

## ⚙️ Cara Kerja Face Recognition

1. **Registrasi**: Ambil 5 foto wajah siswa → dikirim ke FastAPI `/api/register` → MediaPipe mendeteksi wajah →
   `face_recognition` menghasilkan encoding 128-dimensi → disimpan di database MySQL
2. **Pengenalan**: Foto dari webcam → dikirim ke FastAPI `/api/recognize` → MediaPipe validasi ada wajah →
   `face_recognition` menghasilkan encoding → dibandingkan dengan semua encoding tersimpan →
   jika distance < 0.6 (threshold) maka dikenali → PHP mencatat absensi ke database

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
