@echo off
:: Input Validation Utility
:: Demonstrates secure input handling techniques

setlocal enabledelayedexpansion

:main_menu
cls
echo Secure Input Validator
echo =====================
echo.
echo 1. Validate filename
echo 2. Validate number
echo 3. Validate path
echo 4. Validate email
echo 5. Exit
echo.
set /p "choice=Enter your choice (1-5): "

:: Validate choice
call :validate_number "%choice%" 1 5
if errorlevel 1 (
    echo Invalid choice. Please enter a number between 1 and 5.
    pause
    goto :main_menu
)

if "%choice%"=="1" goto :validate_filename
if "%choice%"=="2" goto :validate_number_input
if "%choice%"=="3" goto :validate_path
if "%choice%"=="4" goto :validate_email
if "%choice%"=="5" goto :exit

:validate_filename
set /p "input=Enter filename: "
call :sanitize_filename "%input%"
if errorlevel 1 (
    echo Invalid filename! Contains illegal characters.
) else (
    echo Filename is valid: !sanitized!
)
pause
goto :main_menu

:validate_number_input
set /p "input=Enter number: "
call :validate_number "%input%" -999999999 999999999
if errorlevel 1 (
    echo Invalid number!
) else (
    echo Valid number: %input%
)
pause
goto :main_menu

:validate_path
set /p "input=Enter file path: "
call :validate_path "%input%"
if errorlevel 1 (
    echo Invalid path!
) else (
    echo Valid path: %input%
)
pause
goto :main_menu

:validate_email
set /p "input=Enter email: "
call :validate_email "%input%"
if errorlevel 1 (
    echo Invalid email format!
) else (
    echo Valid email: %input%
)
pause
goto :main_menu

:: Validation Functions
:validate_number
set "input=%~1"
set "min=%~2"
set "max=%~3"

:: Check if input is numeric
echo %input%| findstr /r "^[0-9][0-9]*$" >nul
if errorlevel 1 exit /b 1

:: Check range
if %input% lss %min% exit /b 1
if %input% gtr %max% exit /b 1
exit /b 0

:sanitize_filename
set "input=%~1"
set "sanitized="

:: Remove illegal characters
for %%c in ("%input%") do (
    set "temp=%%~c"
    set "temp=!temp:<=!"
    set "temp=!temp:>=!"
    set "temp=!temp:|=!"
    set "temp=!temp:?=!"
    set "temp=!temp:*=!"
    set "temp=!temp:"=!"
    set "sanitized=!temp!"
)

:: Check if result is different (contained illegal chars)
if not "%input%"=="!sanitized!" exit /b 1
exit /b 0

:validate_path
set "input=%~1"

:: Basic path validation
if not exist "%input%" (
    echo Path does not exist.
    exit /b 1
)
exit /b 0

:validate_email
set "input=%~1"

:: Basic email validation
echo %input%| findstr /r "^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" >nul
if errorlevel 1 exit /b 1
exit /b 0

:exit
echo Goodbye!
exit /b 0
