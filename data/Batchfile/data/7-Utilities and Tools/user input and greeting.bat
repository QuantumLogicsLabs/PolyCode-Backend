@echo off
setlocal

set /p name=Enter your name: 
if "%name%"=="" set "name=Friend"

echo Hello, %name%!

pause
