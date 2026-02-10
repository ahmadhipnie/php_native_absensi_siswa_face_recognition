# Sistem Absensi Siswa dengan Face Recognition

## 📋 Deskripsi
Sistem absensi siswa berbasis web menggunakan teknologi pengenalan wajah (Face Recognition). 
Dibangun dengan **PHP Native** untuk backend web, **Python** untuk pemrosesan face recognition, 
dan **MediaPipe + face_recognition** untuk deteksi dan pengenalan wajah.

## 🛠️ Tech Stack
| Komponen | Teknologi |
|----------|-----------|
| **Web Backend** | PHP Native |
| **Database** | MySQL (Laragon) |
| **Frontend** | Bootstrap 5, Chart.js, DataTables, SweetAlert2 |
| **Face Detection** | MediaPipe (Google) |
| **Face Recognition** | face_recognition (dlib) |
| **Image Processing** | OpenCV Python |
| **DB Connector (Python)** | mysql-connector-python |

## 📁 Struktur Folder
```
php_native_absensi_siswa_face_recognition/
├── api/                    # API endpoints (PHP)
│   ├── get_students.php    # Get students by class
│   ├── recognize.php       # Face recognition API
│   └── register_face.php   # Register face API
├── config/
│   └── database.php        # Database configuration
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
│   ├── register_face.py    # Script registrasi wajah
│   ├── recognize_face.py   # Script pengenalan wajah
│   └── requirements.txt    # Python dependencies
├── temp/                   # Temporary files
├── uploads/                # Uploaded files
│   ├── siswa/              # Foto profil siswa
│   └── absensi/            # Foto saat absensi
├── .htaccess               # Apache security config
├── index.php               # Entry point
├── login.php               # Halaman login
└── README.md               # Dokumentasi
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

### Langkah 3: Setup Python
```bash
# Install dependencies
cd python
pip install -r requirements.txt
```

> **Catatan untuk Windows:** Jika `face_recognition` gagal diinstall:
> 1. Install **CMake**: `pip install cmake`
> 2. Install **dlib**: `pip install dlib`
> 3. Lalu: `pip install face_recognition`
> 
> Atau gunakan prebuilt wheel:
> ```bash
> pip install https://github.com/jloh02/dlib/releases/download/v19.22/dlib-19.22.99-cp310-cp310-win_amd64.whl
> ```

### Langkah 4: Akses Website
Buka browser: **http://localhost/php_native_absensi_siswa_face_recognition/login.php**

## 🔐 Login Default
| Field | Value |
|-------|-------|
| **Username** | `admin` |
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
1. **Registrasi**: Ambil 5 foto wajah siswa → MediaPipe mendeteksi wajah → 
   `face_recognition` menghasilkan encoding 128-dimensi → disimpan di database
2. **Pengenalan**: Foto dari webcam → MediaPipe validasi ada wajah → 
   `face_recognition` menghasilkan encoding → dibandingkan dengan semua encoding tersimpan → 
   jika distance < 0.6 (threshold) maka dikenali

## 📝 Catatan
- MediaPipe digunakan untuk **deteksi wajah** (menentukan ada/tidaknya wajah)
- `face_recognition` (dlib) digunakan untuk **pengenalan wajah** (mengidentifikasi siapa)
- Keduanya saling melengkapi untuk hasil yang lebih akurat
- Threshold default: 0.6 (dapat disesuaikan di `python/recognize_face.py`)
