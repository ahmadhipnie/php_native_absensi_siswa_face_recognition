<?php
// API: Register face for a student via FastAPI
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

// ============================================================
// Kirim ke FastAPI untuk register wajah
// ============================================================
$fastapi_url = defined('FASTAPI_URL') ? FASTAPI_URL : 'http://localhost:8000';

$payload = json_encode([
    'student_id' => $student_id,
    'images' => $images
]);

$ch = curl_init($fastapi_url . '/api/register');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 60,       // Register bisa lebih lama (proses banyak gambar)
    CURLOPT_CONNECTTIMEOUT => 5,
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Cek error koneksi ke FastAPI
if ($response === false || $http_code !== 200) {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menghubungi server Face Recognition. Pastikan FastAPI server sedang berjalan. Error: ' . ($curl_error ?: "HTTP $http_code")
    ]);
    exit;
}

// Parse response dari FastAPI
$result = json_decode($response, true);

if ($result && $result['success']) {
    echo json_encode([
        'success' => true,
        'message' => $result['message'] ?? 'Wajah berhasil didaftarkan!'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $result['message'] ?? 'Gagal memproses wajah. Pastikan FastAPI server berjalan.'
    ]);
}
