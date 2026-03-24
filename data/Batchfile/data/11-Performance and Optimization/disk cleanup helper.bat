@echo off
setlocal

set "target=%~1"
if "%target%"=="" set "target=%temp%"

if not exist "%target%" (
  echo Folder not found: %target%
  exit /b 1
)

echo Cleaning *.tmp and *.log from %target%
del /s /q "%target%\*.tmp" 2>nul
del /s /q "%target%\*.log" 2>nul

echo Cleanup done.
