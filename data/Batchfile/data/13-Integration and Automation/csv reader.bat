@echo off
setlocal

set "csv=%~1"
if "%csv%"=="" (
  echo Usage: %~nx0 "data.csv"
  exit /b 1
)
if not exist "%csv%" (
  echo CSV file not found: %csv%
  exit /b 1
)

for /f "usebackq tokens=1,2,3 delims=," %%A in ("%csv%") do (
  echo Name=%%A  Role=%%B  City=%%C
)
