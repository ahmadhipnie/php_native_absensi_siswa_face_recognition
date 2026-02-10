"""
Face Registration Script
Generates face encodings from captured photos and saves them.
Uses: face_recognition library (based on dlib) + MediaPipe for face detection validation
"""

import sys
import os
import json
import numpy as np

try:
    import face_recognition
    import mediapipe as mp
    import cv2
except ImportError as e:
    print(json.dumps({
        'success': False,
        'message': f'Library tidak ditemukan: {str(e)}. Jalankan: pip install face_recognition mediapipe opencv-python'
    }))
    sys.exit(1)


def register_face(student_id, face_dir):
    """
    Process face images and generate face encoding.
    
    Args:
        student_id: ID of the student
        face_dir: Directory containing face images
    
    Returns:
        JSON with success status and encoding data
    """
    mp_face_detection = mp.solutions.face_detection
    face_detection = mp_face_detection.FaceDetection(
        model_selection=1, 
        min_detection_confidence=0.5
    )
    
    encodings = []
    
    # Process each image in the directory
    image_files = sorted([f for f in os.listdir(face_dir) if f.endswith(('.jpg', '.jpeg', '.png'))])
    
    if not image_files:
        return {
            'success': False,
            'message': 'Tidak ada file gambar yang ditemukan'
        }
    
    for image_file in image_files:
        image_path = os.path.join(face_dir, image_file)
        
        # Load image
        image = cv2.imread(image_path)
        if image is None:
            continue
        
        # Validate face using MediaPipe
        rgb_image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
        results = face_detection.process(rgb_image)
        
        if not results.detections:
            continue
        
        # Generate face encoding using face_recognition library
        face_image = face_recognition.load_image_file(image_path)
        face_encs = face_recognition.face_encodings(face_image)
        
        if face_encs:
            encodings.append(face_encs[0])
    
    face_detection.close()
    
    if not encodings:
        return {
            'success': False,
            'message': 'Tidak dapat mendeteksi wajah pada gambar. Pastikan pencahayaan cukup dan wajah terlihat jelas.'
        }
    
    # Average all encodings for more robust recognition
    average_encoding = np.mean(encodings, axis=0)
    
    # Save encoding as JSON string
    encoding_str = json.dumps(average_encoding.tolist())
    
    # Also save encoding to file for backup
    encoding_file = os.path.join(face_dir, 'encoding.npy')
    np.save(encoding_file, average_encoding)
    
    return {
        'success': True,
        'encoding': encoding_str,
        'num_faces_processed': len(encodings),
        'message': f'Berhasil memproses {len(encodings)} wajah'
    }


if __name__ == '__main__':
    if len(sys.argv) < 3:
        print(json.dumps({
            'success': False,
            'message': 'Usage: python register_face.py <student_id> <face_dir>'
        }))
        sys.exit(1)
    
    student_id = sys.argv[1]
    face_dir = sys.argv[2]
    
    result = register_face(student_id, face_dir)
    print(json.dumps(result))
