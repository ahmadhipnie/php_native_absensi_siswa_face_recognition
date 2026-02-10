@echo off
echo =====================================================
echo   Face Recognition API Server - Absensi Siswa
echo =====================================================
echo.
echo Memulai FastAPI server di http://localhost:8000
echo Dokumentasi API: http://localhost:8000/docs
echo.
echo Tekan Ctrl+C untuk menghentikan server
echo =====================================================
echo.

cd /d "%~dp0python"
python -m uvicorn main:app --host 0.0.0.0 --port 8000 --reload

pause
