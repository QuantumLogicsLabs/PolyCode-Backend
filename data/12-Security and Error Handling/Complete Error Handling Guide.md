# Complete Error Handling Guide

## Professional Error Handling! 🛡️

Comprehensive guide to robust error handling.

---

## Understanding ERRORLEVEL

### What is ERRORLEVEL?
- `0` = Success (no errors)
- `1` or higher = Error occurred

### Check ERRORLEVEL
```batch
@echo off
copy file.txt backup\ > nul
if errorlevel 1 (
    echo Copy failed!
) else (
    echo Copy successful!
)
pause
```

---

## Error Handling Patterns

### Basic Pattern
```batch
@echo off
call :MainLogic
if errorlevel 1 (
    echo ERROR: Script failed!
    exit /b %errorlevel%
)
goto :EOF

:MainLogic
if not exist "file.txt" (
    echo File missing!
    exit /b 1
)
exit /b 0
```

### Advanced with Logging
```batch
@echo off
set logfile=errors.log

:LogError
echo [%date% %time%] ERROR: %1 >> %logfile%
echo ERROR: %1
goto :EOF

REM Usage
if errorlevel 1 (
    call :LogError "Operation failed"
    exit /b 1
)
```

---

## Validation Strategies

### Check Prerequisites
```batch
@echo off
call :CheckAdmin
call :CheckFiles
call :CheckSpace
echo All checks passed!
pause
goto :EOF

:CheckAdmin
net session >nul 2>&1
if errorlevel 1 (
    echo ERROR: Admin required!
    exit /b 1
)
exit /b 0

:CheckFiles
if not exist "required.exe" (
    echo ERROR: File missing!
    exit /b 1
)
exit /b 0

:CheckSpace
REM Check disk space
exit /b 0
```

---

**Build bulletproof scripts!** 🎯
