@echo off
setlocal

set "source=%~1"
set "dest=%~2"

if "%source%"=="" (
  echo Usage: %~nx0 "source-folder" "backup-folder"
  exit /b 1
)
if "%dest%"=="" (
  echo Usage: %~nx0 "source-folder" "backup-folder"
  exit /b 1
)

if not exist "%source%" (
  echo Source folder not found: %source%
  exit /b 1
)
if not exist "%dest%" mkdir "%dest%"

set "stamp=%date:~-4%%date:~4,2%%date:~7,2%_%time:~0,2%%time:~3,2%"
set "stamp=%stamp: =0%"
set "backup_dir=%dest%\\backup_%stamp%"

mkdir "%backup_dir%"
robocopy "%source%" "%backup_dir%" /e /r:1 /w:1 >nul

if errorlevel 8 (
  echo Backup failed.
  exit /b 1
)

echo Backup complete: %backup_dir%
endlocal
