@echo off
setlocal enabledelayedexpansion

set "cfg=%~dp0tasks.cfg"
if not exist "%cfg%" (
  echo # command list> "%cfg%"
  echo echo Running task 1>> "%cfg%"
  echo echo Running task 2>> "%cfg%"
)

for /f "usebackq delims=" %%L in ("%cfg%") do (
  set "line=%%L"
  if not "!line!"=="" if not "!line:~0,1!"=="#" (
    echo Executing: !line!
    cmd /c "!line!"
    if errorlevel 1 (
      echo Task failed: !line!
      exit /b 1
    )
  )
)

echo All tasks completed.
