@echo off
setlocal

call :step "Create temp folder" "mkdir temp_work"
call :step "Write sample file" "echo data>temp_work\\sample.txt"
call :step "Simulate command" "dir missing_file.txt"
call :step "Cleanup" "rmdir /s /q temp_work"

echo Script completed.
exit /b 0

:step
set "label=%~1"
set "command=%~2"
echo [STEP] %label%
cmd /c "%command%"
if errorlevel 1 (
  echo [ERROR] Step failed: %label%
  exit /b 1
)
exit /b 0
