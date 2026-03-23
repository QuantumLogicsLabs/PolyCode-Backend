@echo off
setlocal

set "logfile=%~dp0app.log"

call :log "Script started"
call :log "Running checks"
call :log "Script finished"

echo Log written to: %logfile%
exit /b 0

:log
set "message=%~1"
>> "%logfile%" echo [%date% %time%] %message%
exit /b 0
