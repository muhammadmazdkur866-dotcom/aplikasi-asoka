@echo off
title Asoka - POS & Apriori MBA
echo ======================================================
echo   MENJALANKAN SERVER ASOKA
echo ======================================================
echo   Membuka peramban di http://127.0.0.1:8000 ...
echo   Tekan Ctrl+C untuk menghentikan server.
echo ======================================================
start http://127.0.0.1:8000
php -S 127.0.0.1:8000
pause

