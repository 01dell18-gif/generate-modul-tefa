@echo off
title GEMA FYJ - Generate Modul Pembelajaran Aktif
echo ========================================================
echo   GEMA FYJ - Generate Modul Pembelajaran Aktif
echo   Sistem Generator Modul Ajar & RPP TEFA SMK
echo ========================================================
echo.

:: 1. Cek MySQL XAMPP jika tersedia
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL Service terdeteksi aktif.
) else (
    echo [INFO] Menjalankan MySQL dari C:\xampp\mysql\bin\mysqld.exe...
    start /B "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
)

:: 2. Jalankan PHP Built-in Server
echo [INFO] Menjalankan Web Server di http://localhost:8080 ...
echo [INFO] Tekan Ctrl+C untuk menghentikan server.
echo.
start http://localhost:8080
"C:\xampp\php\php.exe" -S 127.0.0.1:8080 -t "%~dp0"
pause
