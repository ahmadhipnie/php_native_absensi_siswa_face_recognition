<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_absensi_siswa');

// Create connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// Base URL
define('BASE_URL', 'http://localhost/php_native_absensi_siswa_face_recognition/');

// Upload paths
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('FACE_DATA_PATH', __DIR__ . '/../face_data/');

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
