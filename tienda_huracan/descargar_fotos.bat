@echo off
title Descargar fotos - Tienda Huracan
echo Descargando las fotos de las prendas en la carpeta img...
echo.
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0descargar_fotos.ps1"
echo.
pause
