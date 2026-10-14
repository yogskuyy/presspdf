@echo off
title PressPDF - Cloudflare Public Tunnel
echo ============================================================
echo   PressPDF - Menghubungkan Server Lokal ke Internet
echo ============================================================
echo.
echo Pastikan server Laravel sedang berjalan (php artisan serve).
echo Membuka jalur tunnel Cloudflare...
echo.
"C:\Program Files (x86)\cloudflared\cloudflared.exe" tunnel --url http://127.0.0.1:8000
pause
