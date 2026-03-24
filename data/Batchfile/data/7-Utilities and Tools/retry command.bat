@echo off
setlocal

set "tries=0"
set "max=3"

:retry
set /a tries+=1
echo Attempt %tries% of %max%
ping 127.0.0.1 -n 2 >nul

if errorlevel 1 (
  if %tries% lss %max% goto retry
  echo Command failed after %max% attempts.
  exit /b 1
)

echo Command succeeded.
