# Debugging and Troubleshooting

## Fix Your Scripts! 🔧

Learn to find and fix problems in your batch scripts.

---

## Common Errors & Solutions

### "Was unexpected at this time"
**Cause:** Special characters not escaped

**Fix:**
```batch
echo This ^& That    REM Escape & with ^
```

### "File not found"
**Cause:** Path has spaces, not quoted

**Fix:**
```batch
if exist "C:\My Files\file.txt" (   REM Quote paths
    echo Found
)
```

### Variable not expanding
**Cause:** Using % inside block without delayed expansion

**Fix:**
```batch
setlocal enabledelayedexpansion
for %%i in (1,2,3) do (
    echo !var!    REM Use ! instead of %
)
endlocal
```

---

## Debugging Techniques

### Echo Mode
```batch
@echo off
REM Enable command display for debugging
@echo on

REM Your code here
copy file.txt backup\
set /a x=5+3

@echo off
pause
```

### Step-by-Step
```batch
@echo off
echo === Step 1: Initialize ===
pause

echo === Step 2: Process ===
pause

echo === Step 3: Complete ===
pause
```

### Debug Variables
```batch
@echo off
set var=test
echo DEBUG: var = [%var%]
pause
```

---

## Logging Everything
```batch
@echo off
set logfile=debug.log

(
echo ===== Debug Log =====
echo Time: %date% %time%
echo Directory: %cd%
echo.
echo Variables:
set
echo.
echo Running tests...
) > %logfile%

notepad %logfile%
pause
```

---

## Test Checklist ✅

Before deploying your script:
- [ ] Test with valid input
- [ ] Test with invalid input
- [ ] Test with empty input
- [ ] Test with special characters
- [ ] Test on different Windows versions
- [ ] Test with missing files
- [ ] Test with no permissions
- [ ] Check error messages are clear

---

**Good debugging saves hours of frustration!** 🎯
