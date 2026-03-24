@echo off
:: Simple API Client
:: Demonstrates integration with web services

setlocal enabledelayedexpansion

if "%~1"=="" (
    echo Usage: %~nx0 [API_URL]
    echo Example: %~nx0 "https://api.example.com/data"
    goto :eof
)

set "api_url=%~1"
set "output_file=api_response.json"

echo API Client
echo ==========
echo URL: %api_url%
echo Output: %output_file%
echo.

:: Check if curl is available
where curl >nul 2>&1
if errorlevel 1 (
    echo Error: curl is not available. Please install curl or use Windows 10+.
    goto :eof
)

:: Make API request
echo Making request to %api_url%...
curl -s -o "%output_file%" "%api_url%"

if %errorlevel% equ 0 (
    echo Request successful!
    echo Response saved to: %output_file%
    
    :: Display first few lines of response
    if exist "%output_file%" (
        echo.
        echo Response preview:
        echo =================
        for /f "delims=" %%i in ('type "%output_file%" ^| findstr /n "."') do (
            if %%i leq 10 echo %%i
        )
    )
) else (
    echo Request failed with error %errorlevel%
)

echo.
pause
