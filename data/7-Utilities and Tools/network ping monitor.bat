@echo off
setlocal

set "host=%~1"
if "%host%"=="" set "host=8.8.8.8"
set "log=%~dp0ping monitor.log"

echo Monitoring %host%. Press Ctrl+C to stop.
:loop
ping -n 1 %host% | find "TTL=" >nul
if errorlevel 1 (
  >> "%log%" echo [%date% %time%] DOWN - %host%
  echo DOWN
) else (
  >> "%log%" echo [%date% %time%] UP - %host%
  echo UP
)
timeout /t 5 >nul
goto loop
