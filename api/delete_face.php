<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$student_id = $data['student_id'] ?? null;

if (!$student_id) {
    echo json_encode(['success' => false, 'message' => 'Student ID is required']);
    exit;
}

// Get student info first
$student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM siswa WHERE id = $student_id"));

if (!$student) {
    echo json_encode(['success' => false, 'message' => 'Siswa tidak ditemukan']);
    exit;
}

if ($student['face_registered'] == 0) {
    echo json_encode(['success' => false, 'message' => 'Siswa ini tidak memiliki data wajah terdaftar']);
    exit;
}

// Delete face encoding file if exists
$face_data_dir = __DIR__ . '/../python/face_data';
$face_file = $face_data_dir . '/student_' . $student_id . '.pkl';

$file_deleted = false;
if (file_exists($face_file)) {
    $file_deleted = unlink($face_file);
}

// Update database - clear face_encoding and set face_registered to 0
$update_query = "UPDATE siswa SET face_encoding = NULL, face_registered = 0 WHERE id = $student_id";
$update_result = mysqli_query($conn, $update_query);

if ($update_result) {
    // Log activity
    $log_message = "Face registration deleted for student: " . $student['nama_lengkap'] . " (NIS: " . $student['nis'] . ")";
    error_log($log_message);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Data wajah berhasil dihapus',
        'file_deleted' => $file_deleted,
        'student' => [
            'id' => $student['id'],
            'nama' => $student['nama_lengkap'],
            'nis' => $student['nis']
        ]
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Gagal menghapus data wajah: ' . mysqli_error($conn)
    ]);
}
?>
