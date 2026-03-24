@echo off
setlocal

set "mode="
set "input="

:parse
if "%~1"=="" goto done_parse
if /i "%~1"=="--mode" (
  set "mode=%~2"
  shift
  shift
  goto parse
)
if /i "%~1"=="--input" (
  set "input=%~2"
  shift
  shift
  goto parse
)

echo Unknown argument: %~1
exit /b 1

:done_parse
if "%mode%"=="" set "mode=demo"
if "%input%"=="" set "input=none"

echo Mode : %mode%
echo Input: %input%
endlocal
