# Advanced Techniques

## Next-Level Batch Scripting! 🚀

Take your skills to the next level with these advanced concepts.

---

## Delayed Variable Expansion

### The Problem
```batch
@echo off
set var=first
(
    set var=second
    echo %var%      REM Shows: first (wrong!)
)
pause
```

### The Solution
```batch
@echo off
setlocal enabledelayedexpansion
set var=first
(
    set var=second
    echo !var!      REM Shows: second (correct!)
)
endlocal
pause
```

**When to use `!` instead of `%`:**
- Inside loops
- Inside IF blocks
- When variable changes in same block

---

## Arrays in Batch

### Simulate Array
```batch
@echo off
setlocal enabledelayedexpansion

REM Create array
set arr[0]=Apple
set arr[1]=Banana
set arr[2]=Cherry
set arrLength=3

REM Access array
for /l %%i in (0,1,%arrLength%-1) do (
    echo !arr[%%i]!
)

pause
```

### Search in Array
```batch
@echo off
setlocal enabledelayedexpansion

set arr[0]=Red
set arr[1]=Green
set arr[2]=Blue
set found=0

set /p search=Find color: 

for /l %%i in (0,1,2) do (
    if "!arr[%%i]!"=="%search%" (
        echo Found at index %%i
        set found=1
    )
)

if !found! equ 0 echo Not found
pause
```

---

## Regular Expressions (FINDSTR)

### Basic Pattern Matching
```batch
@echo off
echo Hello World | findstr "Hello"
if not errorlevel 1 (
    echo Pattern found!
)
pause
```

### Email Validation
```batch
@echo off
set /p email=Email: 

echo %email% | findstr /R "[a-zA-Z0-9._%+-]*@[a-zA-Z0-9.-]*\.[a-zA-Z]*" > nul
if errorlevel 1 (
    echo Invalid email!
) else (
    echo Valid email!
)
pause
```

---

## Working with Registry

### Read Registry
```batch
@echo off
reg query "HKLM\SOFTWARE\Microsoft\Windows\CurrentVersion" /v ProgramFilesDir
pause
```

### Write to Registry (Admin Required)
```batch
@echo off
reg add "HKCU\Software\MyApp" /v Version /t REG_SZ /d "1.0" /f
pause
```

---

## Calling External Programs

### Run PowerShell
```batch
@echo off
powershell -Command "Get-Process | Select-Object -First 5 Name"
pause
```

### Run VBScript
```batch
@echo off
cscript //nologo script.vbs
pause
```

---

## Best Practices 💡

1. **Use SETLOCAL/ENDLOCAL** for complex scripts
2. **Enable delayed expansion** when needed
3. **Quote all paths** with spaces
4. **Check error levels** after critical ops
5. **Document your code** thoroughly

---

**Advanced techniques = Professional scripts!** 🎯
