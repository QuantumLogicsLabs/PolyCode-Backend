@echo off
setlocal

set /p target=Enter file path to check: 
if exist "%target%" (
  echo File exists.
) else (
  echo File not found.
)

pause
