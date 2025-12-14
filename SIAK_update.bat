@REM @echo off
set LOG=update.log

echo ===== SIAK Update Runner =====

:main_loop
    echo.
    echo Checking MySQL on port 3306...

    :mysql_wait
        powershell -command "exit (!(Test-NetConnection -ComputerName '127.0.0.1' -Port 3306).TcpTestSucceeded)"
        IF %ERRORLEVEL% NEQ 0 (
            echo MySQL not ready, waiting...
            powershell -NoProfile -Command "Add-Type -AssemblyName PresentationFramework;[System.Windows.MessageBox]::Show('Pastikan Laragon sudah START ALL sebelum update!','SIAK Updater',[System.Windows.MessageBoxButton]::OK,[System.Windows.MessageBoxImage]::Warning)"
            timeout /t 2 >nul
            goto mysql_wait
        )

    echo [%date% %time%] MySQL is UP! Starting Update...
    echo [%date% %time%] Memulai update... > %LOG%
    echo [%date% %time%] Jangan Tutup Window ini > %LOG%

    REM Popup sebelum mulai
    powershell -NoProfile -Command "Add-Type -AssemblyName PresentationFramework;[System.Windows.MessageBox]::Show('Pastikan Laragon sudah START ALL sebelum update!','SIAK Updater',[System.Windows.MessageBoxButton]::OK,[System.Windows.MessageBoxImage]::Warning)"

    echo [%date% %time%] STEP 1: Git pull >> %LOG%
    call git pull --rebase >> %LOG% 2>&1

    echo [%date% %time%] STEP 2: Composer install >> %LOG%
    call composer install --no-dev --prefer-dist --optimize-autoloader >> %LOG% 2>&1

    echo [%date% %time%] STEP 3: NPM install >> %LOG%
    call npm install >> %LOG% 2>&1

    echo [%date% %time%] STEP 4: NPM build >> %LOG%
    call npm run build >> %LOG% 2>&1

    echo [%date% %time%] STEP 5: Migrate DB >> %LOG%
    call php artisan migrate --force >> %LOG% 2>&1

    echo [%date% %time%] STEP 6: Laravel optimize >> %LOG%
    call php artisan filament:optimize >> %LOG% 2>&1
    call php artisan optimize >> %LOG% 2>&1

    REM Popup selesai
    powershell -NoProfile -Command "Add-Type -AssemblyName PresentationFramework;[System.Windows.MessageBox]::Show('Update selesai! Cek update.log','SIAK Updater',[System.Windows.MessageBoxButton]::OK,[System.Windows.MessageBoxImage]::Information)"

    echo [%date% %time%] STEP 7: Finish: Berhasil di Update >> %LOG%
    echo [%date% %time%] Window ini sudah bisa ditutup. >> %LOG%
    exit /b 0
