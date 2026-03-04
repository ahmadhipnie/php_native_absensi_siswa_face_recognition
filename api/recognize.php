<?php
// API: Face Recognition - Recognize face and record attendance via FastAPI
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['image'])) {
    echo json_encode(['success' => false, 'message' => 'Data gambar tidak ditemukan']);
    exit;
}

$image_data = $data['image'];
$kelas_id = $data['kelas_id'] ?? '';
$mapel_id = $data['mapel_id'] ?? '';
$tanggal = $data['tanggal'] ?? date('Y-m-d');

// ============================================================
// Kirim ke FastAPI untuk face recognition
// ============================================================
$fastapi_url = defined('FASTAPI_URL') ? FASTAPI_URL : 'http://localhost:8000';

$payload = json_encode(['image' => $image_data]);

$ch = curl_init($fastapi_url . '/api/recognize');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
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

// Debug: Log response untuk troubleshooting
error_log("FastAPI Response: " . $response);

// SAFETY CHECK: Validate confidence threshold (70% minimum)
$MIN_CONFIDENCE_THRESHOLD = 70.0;

if ($result && isset($result['success']) && $result['success'] === true) {
    // Double-check confidence level as safety measure
    $confidence_percent = isset($result['confidence']) ? floatval($result['confidence']) : 0;
    
    if ($confidence_percent < $MIN_CONFIDENCE_THRESHOLD) {
        // Reject if confidence is below threshold
        echo json_encode([
            'success' => false,
            'confidence' => $confidence_percent,
            'message' => "Absensi gagal! Tingkat kecocokan wajah hanya {$confidence_percent}% (minimum {$MIN_CONFIDENCE_THRESHOLD}%). Wajah tidak cocok dengan data yang terdaftar."
        ]);
        exit;
    }
    
    $student_id = (int)$result['student_id'];
    $confidence = $result['confidence'] / 100; // FastAPI returns percentage, convert back

    // Get student info
    $student = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT s.*, k.nama_kelas FROM siswa s 
        JOIN kelas k ON s.kelas_id = k.id 
        WHERE s.id = $student_id
    "));

    if ($student) {
        // Check if already marked attendance today
        $check = mysqli_query($conn, "SELECT id FROM absensi WHERE siswa_id=$student_id AND tanggal='$tanggal'");

        if (mysqli_num_rows($check) > 0) {
            echo json_encode([
                'success' => true,
                'already_marked' => true,
                'nama' => $student['nama_lengkap'],
                'nis' => $student['nis'],
                'kelas' => $student['nama_kelas'],
                'confidence' => round($result['confidence'], 1),
                'message' => 'Siswa sudah absen hari ini'
            ]);
        } else {
            // Record attendance
            $kelas = $student['kelas_id'];
            $mapel_val = $mapel_id ? (int)$mapel_id : 'NULL';
            $jam = date('H:i:s');

            // Simpan foto capture dari base64
            $foto_name = 'absen_' . $student_id . '_' . date('Ymd_His') . '.jpg';
            $foto_dir = __DIR__ . '/../uploads/absensi/';
            if (!is_dir($foto_dir)) mkdir($foto_dir, 0777, true);

            $img_data = str_replace('data:image/jpeg;base64,', '', $image_data);
            $img_data = str_replace(' ', '+', $img_data);
            file_put_contents($foto_dir . $foto_name, base64_decode($img_data));

            $confidence_db = $confidence;
            mysqli_query($conn, "
                INSERT INTO absensi (siswa_id, kelas_id, mapel_id, tanggal, jam_masuk, status, metode_absen, foto_absen, confidence_score)
                VALUES ($student_id, $kelas, $mapel_val, '$tanggal', '$jam', 'hadir', 'face_recognition', '$foto_name', $confidence_db)
            ");

            echo json_encode([
                'success' => true,
                'nama' => $student['nama_lengkap'],
                'nis' => $student['nis'],
                'kelas' => $student['nama_kelas'],
                'confidence' => round($result['confidence'], 1),
                'jam_masuk' => $jam
            ]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Data siswa tidak ditemukan']);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => $result['message'] ?? 'Wajah tidak dikenali. Pastikan wajah sudah terdaftar.'
    ]);
}
