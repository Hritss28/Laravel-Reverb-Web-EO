@echo off
echo ===============================================
echo  Livewire Barang Real-time Testing Script
echo ===============================================
echo.

echo [1/4] Starting Reverb Server...
start "Reverb Server" cmd /k "php artisan reverb:start --host=0.0.0.0 --port=8080 --hostname=eventorganizer.local"
timeout /t 3

echo [2/4] Starting Queue Worker...
start "Queue Worker" cmd /k "php artisan queue:work"
timeout /t 2

echo [3/4] Building Vite Assets...
call npm run build
echo.

echo [4/4] Creating Test Data...
php test-realtime-barang.php
echo.

echo ===============================================
echo  Services Started Successfully!
echo ===============================================
echo.
echo  Reverb Server: http://127.0.0.1:8080
echo  Frontend URL:  http://eventorganizer.local/frontend/barang-livewire
echo  Admin Panel:   http://eventorganizer.local/admin
echo.
echo  To test real-time updates:
echo  1. Open Frontend URL in browser
echo  2. Open Admin Panel in another tab
echo  3. Create/edit/delete barang from admin
echo  4. Watch real-time updates on frontend
echo.
echo Press any key to create another test barang...
pause
php test-realtime-barang.php
echo.
echo Testing complete! Check the frontend for real-time updates.
pause
