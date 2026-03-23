@echo off
setlocal

rem Create a test folder structure
mkdir "demo" 2>nul
mkdir "demo\logs" 2>nul

echo Sample log line> "demo\logs\app.log"
copy /y "demo\logs\app.log" "demo\logs\app.backup.log" >nul

echo Files in demo\logs:
dir /b "demo\logs"

echo Cleaning up demo folder...
rmdir /s /q "demo"

echo Done.
