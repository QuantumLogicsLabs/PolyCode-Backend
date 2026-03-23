@echo off
setlocal

:menu
cls
echo ======================
echo   Intermediate Menu
echo ======================
echo 1. Show date/time
echo 2. List current folder
echo 3. Show environment variable
echo 4. Exit
echo.
set /p choice=Choose an option [1-4]: 

if "%choice%"=="1" goto show_time
if "%choice%"=="2" goto list_files
if "%choice%"=="3" goto show_var
if "%choice%"=="4" goto end

echo Invalid choice.
pause
goto menu

:show_time
echo Date: %date%
echo Time: %time%
pause
goto menu

:list_files
dir /b
pause
goto menu

:show_var
set /p varname=Enter variable name (example: PATH): 
call echo Value of %varname%: %%%varname%%%
pause
goto menu

:end
echo Bye.
endlocal
