"""
Face Recognition Script
Recognizes a face from a captured image by comparing with registered face encodings.
Uses: face_recognition library + MediaPipe for face detection validation
"""

import sys
import os
import json
import numpy as np

try:
    import face_recognition
    import mediapipe as mp
    import cv2
    import mysql.connector
except ImportError as e:
    print(json.dumps({
        'success': False,
        'message': f'Library tidak ditemukan: {str(e)}. Jalankan: pip install face_recognition mediapipe opencv-python mysql-connector-python'
    }))
    sys.exit(1)


# Database configuration (must match PHP config)
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'db_absensi_siswa'
}


def get_registered_faces():
    """Fetch all registered face encodings from database."""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        cursor.execute(
            "SELECT id, nama_lengkap, nis, face_encoding FROM siswa WHERE face_registered = 1 AND status = 'aktif'"
        )
        students = cursor.fetchall()
        cursor.close()
        conn.close()
        return students
    except Exception as e:
        return []


def recognize_face(image_path):
    """
    Recognize a face from an image.
    
    Args:
        image_path: Path to the captured image
    
    Returns:
        JSON with recognition result
    """
    # Validate image exists
    if not os.path.exists(image_path):
        return {
            'success': False,
            'message': 'File gambar tidak ditemukan'
        }
    
    # Load and validate image
    image = cv2.imread(image_path)
    if image is None:
        return {
            'success': False,
            'message': 'Gagal membaca file gambar'
        }
    
    # Use MediaPipe for initial face detection
    mp_face_detection = mp.solutions.face_detection
    face_detection = mp_face_detection.FaceDetection(
        model_selection=1,
        min_detection_confidence=0.5
    )
    
    rgb_image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
    results = face_detection.process(rgb_image)
    face_detection.close()
    
    if not results.detections:
        return {
            'success': False,
            'message': 'Tidak ada wajah terdeteksi. Pastikan wajah terlihat jelas oleh kamera.'
        }
    
    # Generate encoding for the captured face
    face_image = face_recognition.load_image_file(image_path)
    face_encodings = face_recognition.face_encodings(face_image)
    
    if not face_encodings:
        return {
            'success': False,
            'message': 'Gagal mengekstrak fitur wajah. Coba lagi dengan posisi yang berbeda.'
        }
    
    captured_encoding = face_encodings[0]
    
    # Get all registered faces from database
    registered_students = get_registered_faces()
    
    if not registered_students:
        return {
            'success': False,
            'message': 'Belum ada wajah yang terdaftar di sistem.'
        }
    
    # Compare with all registered faces
    best_match = None
    best_distance = float('inf')
    THRESHOLD = 0.6  # Lower = more strict matching
    
    for student in registered_students:
        try:
            stored_encoding = np.array(json.loads(student['face_encoding']))
            distance = face_recognition.face_distance([stored_encoding], captured_encoding)[0]
            
            if distance < best_distance:
                best_distance = distance
                best_match = student
        except (json.JSONDecodeError, ValueError):
            continue
    
    if best_match and best_distance < THRESHOLD:
        confidence = 1 - best_distance  # Convert distance to confidence
        return {
            'success': True,
            'student_id': best_match['id'],
            'nama': best_match['nama_lengkap'],
            'nis': best_match['nis'],
            'confidence': float(confidence),
            'distance': float(best_distance)
        }
    else:
        return {
            'success': False,
            'message': 'Wajah tidak dikenali. Pastikan wajah sudah terdaftar di sistem.',
            'best_distance': float(best_distance) if best_match else None
        }


if __name__ == '__main__':
    if len(sys.argv) < 2:
        print(json.dumps({
            'success': False,
            'message': 'Usage: python recognize_face.py <image_path>'
        }))
        sys.exit(1)
    
    image_path = sys.argv[1]
    result = recognize_face(image_path)
    print(json.dumps(result))
