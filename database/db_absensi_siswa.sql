-- =====================================================
-- DATABASE: db_absensi_siswa
-- Sistem Absensi Siswa dengan Face Recognition
-- Created: 2026-02-10
-- =====================================================

DROP DATABASE IF EXISTS db_absensi_siswa;
CREATE DATABASE db_absensi_siswa CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE db_absensi_siswa;

-- =====================================================
-- TABEL: admin
-- Menyimpan data admin/operator sistem
-- =====================================================
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- TABEL: kelas
-- Menyimpan data kelas
-- =====================================================
CREATE TABLE kelas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kelas VARCHAR(50) NOT NULL,
    jurusan VARCHAR(100),
    tahun_ajaran VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- TABEL: siswa
-- Menyimpan data siswa
-- =====================================================
CREATE TABLE siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nis VARCHAR(20) NOT NULL UNIQUE,
    nama_lengkap VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('L', 'P') NOT NULL,
    kelas_id INT NOT NULL,
    alamat TEXT,
    no_telepon VARCHAR(20),
    foto VARCHAR(255) DEFAULT NULL,
    face_encoding TEXT DEFAULT NULL,
    face_registered TINYINT(1) DEFAULT 0,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- TABEL: mata_pelajaran
-- Menyimpan data mata pelajaran
-- =====================================================
CREATE TABLE mata_pelajaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_mapel VARCHAR(20) NOT NULL UNIQUE,
    nama_mapel VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- TABEL: absensi
-- Menyimpan data absensi siswa
-- =====================================================
CREATE TABLE absensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    siswa_id INT NOT NULL,
    kelas_id INT NOT NULL,
    mapel_id INT DEFAULT NULL,
    tanggal DATE NOT NULL,
    jam_masuk TIME DEFAULT NULL,
    jam_keluar TIME DEFAULT NULL,
    status ENUM('hadir', 'izin', 'sakit', 'alpha') DEFAULT 'alpha',
    metode_absen ENUM('face_recognition', 'manual') DEFAULT 'face_recognition',
    keterangan TEXT,
    foto_absen VARCHAR(255) DEFAULT NULL,
    confidence_score FLOAT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (kelas_id) REFERENCES kelas(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (mapel_id) REFERENCES mata_pelajaran(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- TABEL: pengaturan
-- Menyimpan pengaturan sistem
-- =====================================================
CREATE TABLE pengaturan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_sekolah VARCHAR(200) NOT NULL,
    alamat_sekolah TEXT,
    logo VARCHAR(255) DEFAULT NULL,
    jam_masuk TIME DEFAULT '07:00:00',
    jam_pulang TIME DEFAULT '15:00:00',
    batas_terlambat TIME DEFAULT '07:30:00',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- DATA AWAL (SEEDER)
-- =====================================================

-- Insert Admin Default (password: admin123)
INSERT INTO admin (username, password, nama_lengkap, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@sekolah.com');

-- Insert Pengaturan Default
INSERT INTO pengaturan (nama_sekolah, alamat_sekolah, jam_masuk, jam_pulang, batas_terlambat) VALUES
('SMK Negeri 1 Contoh', 'Jl. Pendidikan No. 1, Kota Contoh', '07:00:00', '15:00:00', '07:30:00');

-- Insert Kelas Sample
INSERT INTO kelas (nama_kelas, jurusan, tahun_ajaran) VALUES
('X-RPL 1', 'Rekayasa Perangkat Lunak', '2025/2026'),
('X-RPL 2', 'Rekayasa Perangkat Lunak', '2025/2026'),
('X-TKJ 1', 'Teknik Komputer dan Jaringan', '2025/2026'),
('XI-RPL 1', 'Rekayasa Perangkat Lunak', '2025/2026'),
('XI-RPL 2', 'Rekayasa Perangkat Lunak', '2025/2026'),
('XI-TKJ 1', 'Teknik Komputer dan Jaringan', '2025/2026'),
('XII-RPL 1', 'Rekayasa Perangkat Lunak', '2025/2026'),
('XII-RPL 2', 'Rekayasa Perangkat Lunak', '2025/2026'),
('XII-TKJ 1', 'Teknik Komputer dan Jaringan', '2025/2026');

-- Insert Mata Pelajaran Sample
INSERT INTO mata_pelajaran (kode_mapel, nama_mapel) VALUES
('MTK', 'Matematika'),
('BIN', 'Bahasa Indonesia'),
('BIG', 'Bahasa Inggris'),
('FIS', 'Fisika'),
('PBO', 'Pemrograman Berorientasi Objek'),
('BDT', 'Basis Data'),
('WEB', 'Pemrograman Web'),
('JAR', 'Jaringan Komputer');

-- Insert Siswa Sample
INSERT INTO siswa (nis, nama_lengkap, jenis_kelamin, kelas_id, alamat, no_telepon) VALUES
('2025001', 'Ahmad Rizky Pratama', 'L', 1, 'Jl. Merdeka No. 10', '081234567890'),
('2025002', 'Siti Nurhaliza', 'P', 1, 'Jl. Kemerdekaan No. 5', '081234567891'),
('2025003', 'Budi Santoso', 'L', 1, 'Jl. Pahlawan No. 3', '081234567892'),
('2025004', 'Dewi Anggraini', 'P', 2, 'Jl. Sudirman No. 15', '081234567893'),
('2025005', 'Eko Prasetyo', 'L', 2, 'Jl. Diponegoro No. 8', '081234567894'),
('2025006', 'Fitri Handayani', 'P', 3, 'Jl. Kartini No. 12', '081234567895'),
('2025007', 'Galang Ramadhan', 'L', 4, 'Jl. Ahmad Yani No. 20', '081234567896'),
('2025008', 'Hana Safitri', 'P', 4, 'Jl. Gatot Subroto No. 7', '081234567897'),
('2025009', 'Irfan Hakim', 'L', 5, 'Jl. MT Haryono No. 3', '081234567898'),
('2025010', 'Jasmine Putri', 'P', 5, 'Jl. Soekarno No. 11', '081234567899');

-- =====================================================
-- NOTE:
-- Password admin default: admin123
-- (di-hash menggunakan bcrypt / password_hash PHP)
-- =====================================================
