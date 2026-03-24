@echo off
:: Memory Optimization Utility
:: Monitors and optimizes memory usage

setlocal enabledelayedexpansion

echo Memory Optimization Tool
echo ========================

:: Get current memory usage
for /f "skip=1" %%p in ('wmic OS get TotalVisibleMemorySize') do set "total_mem=%%p"
for /f "skip=1" %%a in ('wmic OS get FreePhysicalMemory') do set "free_mem=%%a"

:: Calculate used memory (convert KB to MB)
set /a "total_mb=%total_mem%/1024"
set /a "free_mb=%free_mem%/1024"
set /a "used_mb=%total_mb%-%free_mb%"

echo Total Memory: %total_mb% MB
echo Used Memory: %used_mb% MB
echo Free Memory: %free_mb% MB
echo.

:: Memory usage percentage
set /a "usage_pct=(%used_mb%*100)/%total_mb%"
echo Memory Usage: %usage_pct%%%

:: Optimization suggestions
if %usage_pct% gtr 80 (
    echo WARNING: High memory usage detected!
    echo.
    echo Optimization suggestions:
    echo 1. Close unnecessary applications
    echo 2. Clear temporary files
    echo 3. Restart memory-intensive services
    echo 4. Consider adding more RAM
    echo.
    
    choice /c YN /m "Do you want to clear temporary files? (Y/N)"
    if errorlevel 2 goto :skip_cleanup
    
    echo Cleaning temporary files...
    del /q /f /s "%TEMP%\*" 2>nul
    del /q /f /s "%WINDIR%\Temp\*" 2>nul
    echo Temporary files cleaned.
) else (
    echo Memory usage is within acceptable limits.
)

:skip_cleanup
echo.
echo Memory optimization completed.
pause
