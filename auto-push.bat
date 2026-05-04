@echo off
REM ============================================
REM Domain-TIK Git Auto-Push Script
REM ============================================
REM Script ini untuk push otomatis ke GitHub
REM Dapat dijadwalkan via Windows Task Scheduler

cd /d c:\laragon\www\Domain-TIK

REM Tampilkan status sebelum push
echo.
echo [INFO] Cek status git...
git status

REM Check if ada changes yang perlu di-commit
git diff-index --quiet HEAD
if %ERRORLEVEL% equ 0 (
    echo [INFO] Tidak ada perubahan yang perlu di-push
    exit /b 0
)

echo.
echo [INFO] Ada perubahan, melakukan add...
git add -A

echo.
echo [INFO] Melakukan commit...
REM Timestamp untuk commit message
for /f "tokens=2-4 delims=/ " %%a in ('date /t') do (set mydate=%%c-%%a-%%b)
for /f "tokens=1-2 delims=/:" %%a in ('time /t') do (set mytime=%%a%%b)
git commit -m "Auto-push: %mydate% %mytime%"

echo.
echo [INFO] Melakukan push ke origin main...
git push origin main

if %ERRORLEVEL% equ 0 (
    echo [SUCCESS] Push ke GitHub berhasil!
) else (
    echo [ERROR] Push ke GitHub gagal. Cek koneksi atau credentials.
    exit /b 1
)

echo.
echo [INFO] Selesai
pause
