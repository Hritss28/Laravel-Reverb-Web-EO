@echo off
echo Starting Reverb Server and Queue Worker...

REM Start Reverb server in background
echo [INFO] Starting Laravel Reverb Server...
start "Reverb Server" cmd /k "php artisan reverb:start --host=localhost --port=8080"

REM Wait a moment for Reverb to start
timeout /t 3 /nobreak >nul

REM Start Queue Worker in background  
echo [INFO] Starting Queue Worker...
start "Queue Worker" cmd /k "php artisan queue:work --sleep=3 --tries=3"

echo.
echo [SUCCESS] Real-time services started!
echo - Reverb Server: http://localhost:8080
echo - Queue Worker: Running in background
echo.
echo Press any key to continue...
pause >nul
