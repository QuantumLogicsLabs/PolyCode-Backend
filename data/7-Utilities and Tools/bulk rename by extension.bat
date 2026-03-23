@echo off
setlocal

set "folder=%~1"
set "oldext=%~2"
set "newext=%~3"

if "%folder%"=="" (
  echo Usage: %~nx0 "folder" ".txt" ".log"
  exit /b 1
)
if "%oldext%"=="" (
  echo Usage: %~nx0 "folder" ".txt" ".log"
  exit /b 1
)
if "%newext%"=="" (
  echo Usage: %~nx0 "folder" ".txt" ".log"
  exit /b 1
)

if not exist "%folder%" (
  echo Folder not found: %folder%
  exit /b 1
)

pushd "%folder%"
for %%F in (*%oldext%) do (
  set "newname=%%~nF%newext%"
  call echo Renaming "%%F" to "%%newname%%"
  call ren "%%F" "%%newname%%"
)
popd

echo Done.
endlocal
