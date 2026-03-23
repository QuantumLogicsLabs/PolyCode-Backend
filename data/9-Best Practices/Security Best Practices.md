# Security Best Practices

## Write Safe Scripts! 🔒

Protect yourself and users with these security practices.

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

REM Ensure path doesn't contain dangerous patterns
echo %path% | findstr /C:".." > nul
if not errorlevel 1 (
    echo ERROR: Invalid path traversal!
    exit /b 1
)

echo Path accepted: %path%
pause
```

---

## Safe File Operations

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

REM Use password
echo %password%

REM Clear it
set password=
pause
```

---

## Admin Rights Check

### Require Administrator
```batch
@echo off
net session >nul 2>&1
if errorlevel 1 (
    echo ERROR: Run as Administrator!
    echo Right-click and select "Run as Administrator"
    pause
    exit /b 1
)
echo Administrator access confirmed
pause
```

---

## Best Practices 💡

1. ✅ **Validate ALL inputs**
2. ✅ **Quote all paths**
3. ✅ **Check permissions**
4. ✅ **Don't hardcode secrets**
5. ✅ **Use error handling**
6. ✅ **Test thoroughly**
7. ✅ **Document warnings**

---

**Security first = Safe automation!** 🎯
