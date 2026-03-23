@echo off
:: File Compressor Utility
:: Compresses files using built-in Windows compression

setlocal enabledelayedexpansion

if "%~1"=="" (
    echo Usage: %~nx0 [file_or_folder]
    echo.
    echo This script compresses the specified file or folder using Windows compression.
    goto :eof
)

set "target=%~1"
set "compressed=%target%.compressed"

if not exist "%target%" (
    echo Error: File or folder "%target%" not found.
    goto :eof
)

echo Compressing %target%...
compact /c /s "%target%" >nul 2>&1

if %errorlevel% equ 0 (
    echo Successfully compressed %target%
    echo Original size: 
    dir "%target%" | find "bytes"
    echo.
    echo Compressed size:
    dir "%target%" | find "bytes"
) else (
    echo Compression failed or no compression possible.
)

pause
