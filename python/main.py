"""
FastAPI Server untuk Face Recognition
Menyediakan API endpoint untuk register wajah dan recognize wajah.
Jalankan: uvicorn main:app --host 0.0.0.0 --port 8000 --reload
"""

import os
import sys
import json
import base64
import shutil
import uuid
import numpy as np
from datetime import datetime

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List, Optional

try:
    import face_recognition
    import cv2
    import mysql.connector
except ImportError as e:
    print(f"[ERROR] Library tidak ditemukan: {e}")
    print("Jalankan: pip install face_recognition opencv-python mysql-connector-python")
    sys.exit(1)

# ============================================================
# Konfigurasi
# ============================================================

DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'db_absensi_siswa'
}

# Base directory project
BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
TEMP_DIR = os.path.join(BASE_DIR, 'temp')
FACE_DATA_DIR = os.path.join(BASE_DIR, 'face_data')
UPLOAD_DIR = os.path.join(BASE_DIR, 'uploads')

# Buat direktori jika belum ada
for d in [TEMP_DIR, FACE_DATA_DIR, UPLOAD_DIR,
          os.path.join(UPLOAD_DIR, 'siswa'),
          os.path.join(UPLOAD_DIR, 'absensi')]:
    os.makedirs(d, exist_ok=True)

# ============================================================
# FastAPI App
# ============================================================

app = FastAPI(
    title="Face Recognition API - Absensi Siswa",
    description="API untuk register dan recognize wajah siswa",
    version="1.0.0"
)

# CORS - izinkan akses dari PHP
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


# ============================================================
# Pydantic Models (Request/Response)
# ============================================================

class RecognizeRequest(BaseModel):
    image: str  # base64 encoded image (data:image/jpeg;base64,...)


class RegisterRequest(BaseModel):
    student_id: int
    images: List[str]  # list of base64 encoded images


class RecognizeResponse(BaseModel):
    success: bool
    student_id: Optional[int] = None
    nama: Optional[str] = None
    nis: Optional[str] = None
    confidence: Optional[float] = None
    distance: Optional[float] = None
    message: Optional[str] = None


class RegisterResponse(BaseModel):
    success: bool
    encoding: Optional[str] = None
    num_faces_processed: Optional[int] = None
    message: Optional[str] = None


# ============================================================
# Helper Functions
# ============================================================

def get_db_connection():
    """Buat koneksi ke database MySQL."""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        return conn
    except Exception as e:
        print(f"[ERROR] Database connection failed: {e}")
        return None


def decode_base64_image(base64_string: str) -> np.ndarray:
    """Decode base64 image string menjadi numpy array (OpenCV format)."""
    # Hapus header data URI jika ada
    if ',' in base64_string:
        base64_string = base64_string.split(',')[1]

    image_bytes = base64.b64decode(base64_string)
    np_array = np.frombuffer(image_bytes, np.uint8)
    image = cv2.imdecode(np_array, cv2.IMREAD_COLOR)
    return image


def save_temp_image(image: np.ndarray) -> str:
    """Simpan image ke file temporary, return path."""
    filename = f"capture_{uuid.uuid4().hex}.jpg"
    filepath = os.path.join(TEMP_DIR, filename)
    cv2.imwrite(filepath, image)
    return filepath


def get_registered_faces():
    """Ambil semua face encoding yang terdaftar dari database."""
    conn = get_db_connection()
    if not conn:
        return []

    try:
        cursor = conn.cursor(dictionary=True)
        cursor.execute(
            "SELECT id, nama_lengkap, nis, face_encoding "
            "FROM siswa WHERE face_registered = 1 AND status = 'aktif'"
        )
        students = cursor.fetchall()
        cursor.close()
        conn.close()
        return students
    except Exception as e:
        print(f"[ERROR] get_registered_faces: {e}")
        if conn:
            conn.close()
        return []


def detect_face(image: np.ndarray) -> bool:
    """Validasi apakah ada wajah terdeteksi menggunakan face_recognition."""
    # Simpan ke temp file untuk face_recognition
    temp_file = save_temp_image(image)
    try:
        face_image = face_recognition.load_image_file(temp_file)
        face_locations = face_recognition.face_locations(face_image)

        # Clean up temp file
        if os.path.exists(temp_file):
            os.remove(temp_file)

        return len(face_locations) > 0
    except Exception as e:
        print(f"[ERROR] detect_face: {e}")
        if os.path.exists(temp_file):
            os.remove(temp_file)
        return False


# ============================================================
# API Endpoints
# ============================================================

@app.get("/")
async def root():
    """Health check endpoint."""
    return {
        "status": "running",
        "service": "Face Recognition API - Absensi Siswa",
        "version": "1.0.0"
    }


@app.get("/api/health")
async def health_check():
    """Cek status API dan koneksi database."""
    db_status = "connected"
    conn = get_db_connection()
    if conn:
        conn.close()
    else:
        db_status = "disconnected"

    return {
        "status": "ok",
        "database": db_status,
        "face_recognition_library": True
    }


@app.post("/api/recognize", response_model=RecognizeResponse)
async def recognize_face_endpoint(request: RecognizeRequest):
    """
    Recognize wajah dari gambar base64.
    Mencocokkan dengan wajah yang sudah terdaftar di database.
    """
    temp_file = None

    try:
        # 1. Decode base64 image
        image = decode_base64_image(request.image)
        if image is None:
            return RecognizeResponse(
                success=False,
                message="Gagal membaca gambar. Format tidak valid."
            )

        # 2. Validasi wajah terdeteksi
        if not detect_face(image):
            return RecognizeResponse(
                success=False,
                message="Tidak ada wajah terdeteksi. Pastikan wajah terlihat jelas oleh kamera."
            )

        # 3. Simpan ke temp file untuk face_recognition library
        temp_file = save_temp_image(image)

        # 4. Generate face encoding
        face_image = face_recognition.load_image_file(temp_file)
        face_encodings = face_recognition.face_encodings(face_image)

        if not face_encodings:
            return RecognizeResponse(
                success=False,
                message="Gagal mengekstrak fitur wajah. Coba lagi dengan posisi yang berbeda."
            )

        captured_encoding = face_encodings[0]

        # 5. Ambil semua wajah terdaftar
        registered_students = get_registered_faces()

        if not registered_students:
            return RecognizeResponse(
                success=False,
                message="Belum ada wajah yang terdaftar di sistem."
            )

        # 6. Bandingkan dengan semua wajah terdaftar
        best_match = None
        best_distance = float('inf')

        # Minimum confidence threshold: 70%
        # Confidence = (1 - distance) * 100
        # Distance threshold = 1 - 0.70 = 0.30
        DISTANCE_THRESHOLD = 0.30
        MIN_CONFIDENCE = 70.0

        for student in registered_students:
            try:
                stored_encoding = np.array(json.loads(student['face_encoding']))
                distance = face_recognition.face_distance(
                    [stored_encoding], captured_encoding
                )[0]

                if distance < best_distance:
                    best_distance = distance
                    best_match = student
            except (json.JSONDecodeError, ValueError):
                continue

        # 7. Return hasil dengan threshold minimum 70% confidence
        if best_match:
            confidence = round((1 - best_distance) * 100, 1)

            # Cek apakah confidence memenuhi threshold minimum
            if confidence >= MIN_CONFIDENCE:
                return RecognizeResponse(
                    success=True,
                    student_id=best_match['id'],
                    nama=best_match['nama_lengkap'],
                    nis=best_match['nis'],
                    confidence=confidence,
                    distance=float(best_distance)
                )
            else:
                # Confidence di bawah 70%
                return RecognizeResponse(
                    success=False,
                    confidence=confidence,
                    message=f"Absensi gagal. Tingkat kecocokan hanya {confidence}%, minimum 70%. Wajah tidak cocok dengan data yang terdaftar."
                )
        else:
            return RecognizeResponse(
                success=False,
                message="Wajah tidak dikenali. Pastikan wajah sudah terdaftar di sistem."
            )

    except Exception as e:
        print(f"[ERROR] recognize: {e}")
        return RecognizeResponse(
            success=False,
            message=f"Terjadi kesalahan server: {str(e)}"
        )
    finally:
        # Cleanup temp file
        if temp_file and os.path.exists(temp_file):
            try:
                os.remove(temp_file)
            except:
                pass


@app.post("/api/register", response_model=RegisterResponse)
async def register_face_endpoint(request: RegisterRequest):
    """
    Register wajah siswa dari beberapa gambar base64.
    Menghasilkan face encoding rata-rata dari semua gambar.
    """
    student_id = request.student_id
    images_base64 = request.images
    face_dir = os.path.join(FACE_DATA_DIR, str(student_id))

    try:
        # 1. Buat direktori face data untuk siswa
        os.makedirs(face_dir, exist_ok=True)

        # 2. Simpan semua gambar ke disk
        saved_files = []
        for idx, img_base64 in enumerate(images_base64):
            image = decode_base64_image(img_base64)
            if image is None:
                continue
            filepath = os.path.join(face_dir, f"face_{idx + 1}.jpg")
            cv2.imwrite(filepath, image)
            saved_files.append(filepath)

        if not saved_files:
            return RegisterResponse(
                success=False,
                message="Tidak ada gambar valid yang diterima."
            )

        # 3. Simpan foto pertama sebagai foto profil
        foto_dir = os.path.join(UPLOAD_DIR, 'siswa')
        os.makedirs(foto_dir, exist_ok=True)
        foto_name = f"siswa_{student_id}.jpg"
        shutil.copy(saved_files[0], os.path.join(foto_dir, foto_name))

        # 4. Process face encodings menggunakan face_recognition
        encodings = []

        for filepath in saved_files:
            # Validasi wajah dengan face_recognition
            face_image = face_recognition.load_image_file(filepath)
            face_locations = face_recognition.face_locations(face_image)

            if not face_locations:
                continue  # Skip jika tidak ada wajah terdeteksi

            # Generate face encoding
            face_encs = face_recognition.face_encodings(face_image)

            if face_encs:
                encodings.append(face_encs[0])

        if not encodings:
            return RegisterResponse(
                success=False,
                message="Tidak dapat mendeteksi wajah pada gambar. "
                        "Pastikan pencahayaan cukup dan wajah terlihat jelas."
            )

        # 5. Rata-ratakan semua encoding
        average_encoding = np.mean(encodings, axis=0)
        encoding_str = json.dumps(average_encoding.tolist())

        # 6. Simpan encoding ke file backup
        encoding_file = os.path.join(face_dir, 'encoding.npy')
        np.save(encoding_file, average_encoding)

        # 7. Update database
        conn = get_db_connection()
        if conn:
            try:
                cursor = conn.cursor()
                cursor.execute(
                    "UPDATE siswa SET face_registered = 1, face_encoding = %s, foto = %s WHERE id = %s",
                    (encoding_str, foto_name, student_id)
                )
                conn.commit()
                cursor.close()
                conn.close()
            except Exception as e:
                print(f"[ERROR] DB update: {e}")
                if conn:
                    conn.close()
                # Tetap return success karena encoding sudah dibuat
                return RegisterResponse(
                    success=True,
                    encoding=encoding_str,
                    num_faces_processed=len(encodings),
                    message=f"Encoding berhasil dibuat ({len(encodings)} wajah), "
                            f"tapi gagal update database: {str(e)}"
                )

        return RegisterResponse(
            success=True,
            encoding=encoding_str,
            num_faces_processed=len(encodings),
            message=f"Berhasil memproses {len(encodings)} wajah dan mendaftarkan ke database."
        )

    except Exception as e:
        print(f"[ERROR] register: {e}")
        return RegisterResponse(
            success=False,
            message=f"Terjadi kesalahan server: {str(e)}"
        )


# ============================================================
# Run Server
# ============================================================

if __name__ == "__main__":
    import uvicorn
    print("=" * 50)
    print("  Face Recognition API Server")
    print("  URL: http://localhost:8000")
    print("  Docs: http://localhost:8000/docs")
    print("=" * 50)
    uvicorn.run(app, host="0.0.0.0", port=8000, reload=True)
