<?php
// API: Get students by class with current attendance status
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$kelas_id = isset($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : 0;
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

if (!$kelas_id) {
    echo json_encode([]);
    exit;
}

$query = "
    SELECT s.id, s.nis, s.nama_lengkap, s.jenis_kelamin, s.face_registered,
           a.status as current_status, a.keterangan
    FROM siswa s
    LEFT JOIN absensi a ON s.id = a.siswa_id AND a.tanggal = '$tanggal'
    WHERE s.kelas_id = $kelas_id AND s.status = 'aktif'
    ORDER BY s.nama_lengkap
";

$result = mysqli_query($conn, $query);
$students = [];

while ($row = mysqli_fetch_assoc($result)) {
    $row['current_status'] = $row['current_status'] ?? 'hadir';
    $students[] = $row;
}

echo json_encode($students);
