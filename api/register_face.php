<?php
// API: Register face for a student
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['student_id']) || !isset($data['images'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

$student_id = (int)$data['student_id'];
$images = $data['images'];

// Verify student exists
$student = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM siswa WHERE id = $student_id"));
if (!$student) {
    echo json_encode(['success' => false, 'message' => 'Siswa tidak ditemukan']);
    exit;
}

// Save images for face encoding
$face_dir = __DIR__ . '/../face_data/' . $student_id . '/';
if (!is_dir($face_dir)) {
    mkdir($face_dir, 0777, true);
}

$saved_files = [];
foreach ($images as $index => $image_data) {
    $image_data = str_replace('data:image/jpeg;base64,', '', $image_data);
    $image_data = str_replace(' ', '+', $image_data);
    $image_binary = base64_decode($image_data);
    
    $filename = $face_dir . 'face_' . ($index + 1) . '.jpg';
    file_put_contents($filename, $image_binary);
    $saved_files[] = $filename;
}

// Save first photo as profile photo
$foto_dir = __DIR__ . '/../uploads/siswa/';
if (!is_dir($foto_dir)) mkdir($foto_dir, 0777, true);
$foto_name = 'siswa_' . $student_id . '.jpg';
copy($saved_files[0], $foto_dir . $foto_name);

// Call Python to generate face encoding
$python_script = __DIR__ . '/../python/register_face.py';
$command = "python \"$python_script\" \"$student_id\" \"$face_dir\" 2>&1";
$output = shell_exec($command);

$result = json_decode($output, true);

if ($result && $result['success']) {
    // Update student record
    $encoding = mysqli_real_escape_string($conn, $result['encoding']);
    mysqli_query($conn, "UPDATE siswa SET face_registered = 1, face_encoding = '$encoding', foto = '$foto_name' WHERE id = $student_id");
    
    echo json_encode(['success' => true, 'message' => 'Wajah berhasil didaftarkan']);
} else {
    echo json_encode([
        'success' => false, 
        'message' => $result['message'] ?? 'Gagal memproses wajah. Pastikan Python dan library sudah terinstall.'
    ]);
}
