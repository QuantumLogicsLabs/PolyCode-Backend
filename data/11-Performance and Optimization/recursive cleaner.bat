@echo off
setlocal enabledelayedexpansion

set "target=%~1"
if "%target%"=="" set "target=%cd%"

if not exist "%target%" (
  echo Target folder does not exist: %target%
  exit /b 1
)

echo Cleaning temporary files in: %target%
for /r "%target%" %%F in (*.tmp *.log *.bak) do (
  echo Deleting: %%F
  del /q "%%F"
)

echo Done.
endlocal
