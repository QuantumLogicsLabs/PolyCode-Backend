# Security Best Practices Guide

## Write Secure Scripts! 🔒

Essential security practices for batch scripting.

---

## Input Validation

### Never Trust User Input
```batch
@echo off
set /p filename=Enter filename: 

REM Remove dangerous characters
set "filename=%filename:<=%"
set "filename=%filename:>=%"
set "filename=%filename:|=%"
set "filename=%filename:&=%"

if exist "%filename%" (
    type "%filename%"
) else (
    echo Invalid or missing file
)
pause
```

### Validate Paths
```batch
@echo off
set /p path=Enter path: 

REM Check for path traversal
echo %path% | findstr /C:".." > nul
if not errorlevel 1 (
    echo ERROR: Invalid path!
    exit /b 1
)

echo Path accepted: %path%
pause
```

---

## Safe Operations

### Always Quote Paths
```batch
@echo off
copy "C:\My Documents\file.txt" "D:\Backup\"   REM Safe
copy C:\My Documents\file.txt D:\Backup\       REM Dangerous!
pause
```

### Check Before Delete
```batch
@echo off
set /p confirm=Type 'DELETE' to confirm: 
if not "%confirm%"=="DELETE" (
    echo Operation cancelled
    pause
    exit /b 0
)

del "important.txt"
pause
```

---

## Protect Sensitive Data

### Never Hardcode Passwords
```batch
@echo off
REM WRONG - Never do this!
set password=MySecretPassword123

REM RIGHT - Prompt for it
set /p password=Enter password: 
pause
```

### Clear Sensitive Variables
```batch
@echo off
set /p password=Password: 
echo %password%
set password=    REM Clear it!
pause
```

---

**Write secure, safe batch scripts!** 🎯
