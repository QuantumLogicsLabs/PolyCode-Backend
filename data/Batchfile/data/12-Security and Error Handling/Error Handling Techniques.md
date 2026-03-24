# Error Handling Techniques

## Make Your Scripts Bulletproof! 🛡️

Good scripts handle errors gracefully. Let's learn how!

---

## Understanding ERRORLEVEL

### Check Command Success
```batch
@echo off
copy nonexistent.txt backup\ > nul
if errorlevel 1 (
    echo Copy failed!
) else (
    echo Copy successful!
)
pause
```

**ERRORLEVEL Values:**
- `0` = Success
- `1` or higher = Error

---

## Common Error Patterns

### File Not Found
```batch
@echo off
if not exist "important.txt" (
    echo ERROR: File not found!
    exit /b 1
)
echo Processing file...
pause
```

### Invalid Input
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

### Basic Error Handler
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

---

## Validation Functions

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
    echo ERROR: Admin rights required!
    exit /b 1
)
exit /b 0

:CheckFiles
if not exist "required.exe" (
    echo ERROR: required.exe missing!
    exit /b 1
)
exit /b 0

:CheckSpace
REM Check disk space here
exit /b 0
```

---

## Logging Errors

### Create Error Log
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

## Best Practices 💡

1. **Always check critical operations**
2. **Provide clear error messages**
3. **Clean up after errors**
4. **Log important failures**
5. **Give users helpful information**

---

**Good error handling = Professional scripts!** 🎯
