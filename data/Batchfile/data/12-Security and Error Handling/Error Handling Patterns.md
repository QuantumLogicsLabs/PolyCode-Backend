# Error Handling Patterns

## Bulletproof Your Scripts! 🛡️

Professional error handling techniques.

---

## Basic Error Checking

### Check File Existence
```batch
@echo off
if not exist "important.txt" (
    echo ERROR: File not found!
    exit /b 1
)
echo Processing file...
pause
```

### Validate Input
```batch
@echo off
set /p age=Your age: 
if %age% lss 1 (
    echo ERROR: Invalid age!
    exit /b 1
)
if %age% gtr 150 (
    echo ERROR: Impossible age!
    exit /b 1
)
pause
```

---

## Try-Catch Pattern

### Basic Structure
```batch
@echo off
setlocal

call :MainLogic
if errorlevel 1 (
    echo Program failed with error %errorlevel%
    goto :ErrorHandler
)
goto :EOF

:MainLogic
REM Your code here
exit /b 0

:ErrorHandler
echo An error occurred!
pause
```

### Advanced Error Handler
```batch
@echo off
set logfile=errors.log

:LogError
echo [%date% %time%] ERROR: %1 >> %logfile%
echo ERROR: %1
goto :EOF

copy missing.txt backup\ > nul
if errorlevel 1 (
    call :LogError "Copy failed"
    exit /b 1
)
pause
```

---

## Recovery Strategies

### Retry Logic
```batch
@echo off
set retries=0
:maxretries
ping -n 1 google.com > nul
if errorlevel 1 (
    set /a retries+=1
    if %retries% lss 3 (
        echo Retrying... (%retries%/3)
        timeout /t 2 > nul
        goto maxretries
    )
    echo Failed after 3 attempts
    exit /b 1
)
echo Connection successful!
pause
```

---

**Make your scripts bulletproof!** 🎯
