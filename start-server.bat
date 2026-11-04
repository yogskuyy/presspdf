@echo off
title PressPDF Server
cd /d "%~dp0"

echo ============================================================
echo   Menjalankan Server PressPDF (Laravel)
echo ============================================================
echo.
echo IP Jaringan Lokal Komputer Anda:
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4"') do (
    echo   -> http:%%a:8000
)
echo.
echo Akses Lokal (dari laptop ini): http://localhost:8000
echo.
echo Server dijalankan pada 0.0.0.0:8000 agar dapat diakses oleh HP/perangkat lain di Wi-Fi yang sama.
echo Tekan CTRL+C untuk mematikan server.
echo ============================================================
echo.

php artisan serve --host=0.0.0.0 --port=8000
pause

