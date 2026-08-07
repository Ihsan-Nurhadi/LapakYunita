@echo off
title Memulai POS Lapaknita...
cd /d "%~dp0"
echo Menjalankan instalasi mandiri dan portabel untuk Windows...
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0setup.ps1"
echo.
echo Selesai. Tekan tombol apa saja untuk menutup jendela ini.
pause
