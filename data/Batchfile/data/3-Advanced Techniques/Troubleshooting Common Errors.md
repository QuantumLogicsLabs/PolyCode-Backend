# Troubleshooting Common Errors

## Fix Batch Script Problems! 🔧

Solutions to most common batch scripting errors.

---

## Syntax Errors

### "Was unexpected at this time"
**Problem:** Special characters not escaped

**Solution:**
```batch
echo This ^& That    REM Escape & with ^
echo Pipe: ^|
echo Redirect: ^< ^>
```

### Missing Parentheses
**Problem:** Unmatched ( or )

**Solution:**
```batch
if exist file.txt (
    echo Found
)    REM Don't forget closing )
```

---

## Variable Errors

### Variable Not Expanding
**Problem:** Using % inside block

**Solution:**
```batch
setlocal enabledelayedexpansion
for %%i in (1,2,3) do (
    echo !var!    REM Use ! not %
)
endlocal
```

### Spaces Around =
**Problem:** `set var = value`

**Solution:**
```batch
set var=value    REM No spaces!
```

---

## File Operation Errors

### File Not Found
**Problem:** Path has spaces, not quoted

**Solution:**
```batch
if exist "C:\My Files\file.txt" (
    echo Found
)
```

### Copy Fails
**Problem:** Destination doesn't exist

**Solution:**
```batch
if not exist "backup" mkdir "backup"
copy file.txt backup\
```

---

## Debugging Tips

### Enable Echo for Debug
```batch
@echo off
REM Enable to see commands
@echo on
copy file.txt backup\
set /a x=5+3
@echo off
pause
```

### Step-by-Step
```batch
@echo off
echo Step 1
pause
echo Step 2
pause
echo Complete
pause
```

---

**Fix errors quickly and efficiently!** 🎯
