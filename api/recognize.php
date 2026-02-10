<?php
// API: Face Recognition - Recognize face and record attendance
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

// Save temporary image for Python processing
$image_data = str_replace('data:image/jpeg;base64,', '', $image_data);
$image_data = str_replace(' ', '+', $image_data);
$image_binary = base64_decode($image_data);

$temp_dir = __DIR__ . '/../temp/';
if (!is_dir($temp_dir)) {
    mkdir($temp_dir, 0777, true);
}

$temp_file = $temp_dir . 'capture_' . time() . '.jpg';
file_put_contents($temp_file, $image_binary);

// Call Python face recognition script
$python_script = __DIR__ . '/../python/recognize_face.py';
$command = "python \"$python_script\" \"$temp_file\" 2>&1";
$output = shell_exec($command);

// Parse Python output (JSON)
$result = json_decode($output, true);

if ($result && $result['success']) {
    $student_id = (int)$result['student_id'];
    $confidence = $result['confidence'];

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
                'confidence' => round($confidence * 100, 1),
                'message' => 'Siswa sudah absen hari ini'
            ]);
        } else {
            // Record attendance
            $kelas = $student['kelas_id'];
            $mapel_val = $mapel_id ? (int)$mapel_id : 'NULL';
            $jam = date('H:i:s');
            
            // Save capture photo
            $foto_name = 'absen_' . $student_id . '_' . date('Ymd_His') . '.jpg';
            $foto_dir = __DIR__ . '/../uploads/absensi/';
            if (!is_dir($foto_dir)) mkdir($foto_dir, 0777, true);
            copy($temp_file, $foto_dir . $foto_name);

            mysqli_query($conn, "
                INSERT INTO absensi (siswa_id, kelas_id, mapel_id, tanggal, jam_masuk, status, metode_absen, foto_absen, confidence_score)
                VALUES ($student_id, $kelas, $mapel_val, '$tanggal', '$jam', 'hadir', 'face_recognition', '$foto_name', $confidence)
            ");

            echo json_encode([
                'success' => true,
                'nama' => $student['nama_lengkap'],
                'nis' => $student['nis'],
                'kelas' => $student['nama_kelas'],
                'confidence' => round($confidence * 100, 1),
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

// Cleanup temp file
if (file_exists($temp_file)) {
    unlink($temp_file);
}
