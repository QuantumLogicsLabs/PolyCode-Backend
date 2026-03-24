@echo off
:: Database Backup Utility
:: Automated database backup with rotation

setlocal enabledelayedexpansion

:: Configuration
set "db_name=production_db"
set "backup_dir=D:\Backups"
set "max_backups=7"
set "log_file=%backup_dir%\backup.log"

:: Create backup directory if not exists
if not exist "%backup_dir%" mkdir "%backup_dir%"

:: Generate timestamp
for /f "tokens=2 delims==" %%a in ('wmic OS Get localdatetime /value') do set "dt=%%a"
set "timestamp=%dt:~0,8%_%dt:~8,6%"

:: Backup filename
set "backup_file=%backup_dir%\%db_name%_%timestamp%.bak"

echo Starting backup at %date% %time% >> "%log_file%"
echo Backup file: %backup_file% >> "%log_file%"

:: Perform backup (example for SQL Server)
sqlcmd -S localhost -E -Q "BACKUP DATABASE [%db_name%] TO DISK = '%backup_file%' WITH INIT"

if %errorlevel% equ 0 (
    echo Backup completed successfully >> "%log_file%"
    
    :: Cleanup old backups
    for /f "skip=%max_backups% delims=" %%f in ('dir /b /o-d "%backup_dir%\%db_name%_*.bak" 2^>nul') do (
        echo Deleting old backup: %%f >> "%log_file%"
        del "%backup_dir%\%%f"
    )
) else (
    echo Backup failed with error %errorlevel% >> "%log_file%"
)

echo Backup process finished at %date% %time% >> "%log_file%"
echo. >> "%log_file%"

pause
