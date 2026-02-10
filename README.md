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
| **Face Detection**        | MediaPipe (Google)                             |
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
├── start_fastapi.bat       # ⭐ Script untuk start FastAPI server
└── README.md               # Dokumentasi
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

- **Laragon** (PHP 7.4+ dan MySQL)
- **Python 3.8+**
- **pip** (Python package manager)
- **Visual C++ Build Tools** (untuk compile dlib di Windows)

### Langkah 1: Setup Database

1. Buka **Laragon** → Start All
2. Buka **phpMyAdmin** (http://localhost/phpmyadmin)
3. Import file `database/db_absensi_siswa.sql`
   - Klik **Import** → Choose File → pilih `db_absensi_siswa.sql` → **Go**

### Langkah 2: Setup PHP

1. Letakkan folder project di `C:\laragon\www\php_native_absensi_siswa_face_recognition\`
   (atau sesuaikan `BASE_URL` di `config/database.php`)
2. Pastikan extension PHP berikut aktif di `php.ini`:
   - `extension=gd`
   - `extension=mysqli`
   - `extension=curl` ← **PENTING untuk koneksi ke FastAPI**

### Langkah 3: Setup Python & FastAPI

```bash
# Install dependencies
cd python
pip install -r requirements.txt
```

> **Catatan untuk Windows:** Jika `face_recognition` gagal diinstall:
>
> 1. Install **CMake**: `pip install cmake`
> 2. Install **dlib**: `pip install dlib`
> 3. Lalu: `pip install face_recognition`
>
> Atau gunakan prebuilt wheel:
>
> ```bash
> pip install https://github.com/jloh02/dlib/releases/download/v19.22/dlib-19.22.99-cp310-cp310-win_amd64.whl
> ```

### Langkah 4: Jalankan FastAPI Server

```bash
# Cara 1: Jalankan via command line
cd python
python -m uvicorn main:app --host 0.0.0.0 --port 8001 --reload

# Cara 2: Klik start_fastapi.bat (Windows)
```

> Server berjalan di **http://localhost:8001**
> Dokumentasi API Swagger: **http://localhost:8001/docs**

### Langkah 5: Akses Website

Buka browser: **http://localhost/php_native_absensi_siswa_face_recognition/login.php**

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

## 📝 Catatan

- MediaPipe digunakan untuk **deteksi wajah** (menentukan ada/tidaknya wajah)
- `face_recognition` (dlib) digunakan untuk **pengenalan wajah** (mengidentifikasi siapa)
- Keduanya saling melengkapi untuk hasil yang lebih akurat
- Threshold default: 0.6 (dapat disesuaikan di `python/main.py`)
- PHP berkomunikasi dengan Python melalui **HTTP/cURL** ke FastAPI (bukan shell_exec)
- Pastikan **FastAPI server harus berjalan** sebelum menggunakan fitur face recognition
