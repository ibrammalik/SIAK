@echo off
SET APP_DIR=%~dp0

echo ===== Laravel Queue Runner =====

:main_loop
    echo.
    echo Checking MySQL on port 3306...

    :mysql_wait
        powershell -command "exit (!(Test-NetConnection -ComputerName '127.0.0.1' -Port 3306).TcpTestSucceeded)"
        IF %ERRORLEVEL% NEQ 0 (
            echo MySQL not ready, waiting...
            timeout /t 2 >nul
            goto mysql_wait
        )

    echo MySQL is UP! Starting PHP worker...
    cd /d "%APP_DIR%"

    php artisan queue:work --tries=3 --sleep=1

    echo.
    echo ============================================
    echo Worker crashed or MySQL stopped.
    echo Auto-restarting in 3 seconds...
    echo ============================================
    timeout /t 3 >nul

goto main_loop
