@echo off
echo ============================================================
echo   Memulai Layanan eLabora Monorepo
echo ============================================================
echo.
echo [1/3] Menjalankan Backend Express API di Port 5000...
start "eLabora - Backend API (Port 5000)" cmd /k "cd /d "%~dp0backend" && npm run dev"

echo [2/3] Menjalankan Vite Asset Compiler...
start "eLabora - Frontend Vite" cmd /k "cd /d "%~dp0frontend" && npm run dev"

echo [3/3] Menjalankan Laravel Development Server di Port 8000...
start "eLabora - Frontend Laravel (Port 8000)" cmd /k "cd /d "%~dp0frontend" && "C:\laragon\bin\php\php-8.2.35-Win32-vs16-x64\php.exe" artisan serve"

echo.
echo ============================================================
echo   Semua service sedang berjalan di jendela terminal masing-masing!
echo   - Backend API  : http://localhost:5000
echo   - Frontend Web : http://localhost:8000
echo ============================================================
pause
