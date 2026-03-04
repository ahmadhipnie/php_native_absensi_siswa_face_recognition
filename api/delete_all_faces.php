<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get all students with face registration
$students = mysqli_query($conn, "SELECT * FROM siswa WHERE face_registered = 1");

if (mysqli_num_rows($students) == 0) {
    echo json_encode(['success' => false, 'message' => 'Tidak ada data wajah yang terdaftar']);
    exit;
}

$deleted_count = 0;
$failed_count = 0;
$face_data_dir = __DIR__ . '/../python/face_data';
$deleted_students = [];

// Update all students in database first
$update_query = "UPDATE siswa SET face_encoding = NULL, face_registered = 0 WHERE face_registered = 1";
$update_result = mysqli_query($conn, $update_query);

if ($update_result) {
    $deleted_count = mysqli_affected_rows($conn);
    
    // Delete all face encoding files
    if (is_dir($face_data_dir)) {
        $files = glob($face_data_dir . '/student_*.pkl');
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
    
    // Log activity
    $log_message = "All face registrations deleted. Total: $deleted_count students";
    error_log($log_message);
    
    echo json_encode([
        'success' => true,
        'message' => 'Semua data wajah berhasil dihapus',
        'deleted_count' => $deleted_count,
        'files_deleted' => count($files ?? [])
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menghapus data wajah: ' . mysqli_error($conn)
    ]);
}
?>
