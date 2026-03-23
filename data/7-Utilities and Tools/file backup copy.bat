@echo off
setlocal

set "source=%~1"
if "%source%"=="" (
  echo Usage: %~nx0 "path-to-file"
  exit /b 1
)

if not exist "%source%" (
  echo File not found: %source%
  exit /b 1
)

for %%I in ("%source%") do (
  set "folder=%%~dpI"
  set "name=%%~nI"
  set "ext=%%~xI"
)

set "backup=%folder%%name%.backup%ext%"
copy /y "%source%" "%backup%" >nul

echo Backup created: %backup%
endlocal
