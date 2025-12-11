@echo off
SET APP_DIR=%~dp0

echo ===== Laravel Migrate Runner =====

echo.
echo Checking MySQL on port 3306...

:mysql_wait
    powershell -command "exit (!(Test-NetConnection -ComputerName '127.0.0.1' -Port 3306).TcpTestSucceeded)"
    IF %ERRORLEVEL% NEQ 0 (
        echo MySQL not ready, waiting...
        timeout /t 2 >nul
        goto mysql_wait
    )

echo MySQL is UP! Starting migration...
cd /d "%APP_DIR%"

php artisan migrate --force
