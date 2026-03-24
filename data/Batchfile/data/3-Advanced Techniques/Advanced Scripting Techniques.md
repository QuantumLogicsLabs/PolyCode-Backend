# Advanced Scripting Techniques

## Next-Level Skills! 🚀

Master advanced batch scripting concepts.

---

## Delayed Expansion

### The Problem
```batch
@echo off
set var=first
(
    set var=second
    echo %var%      REM Shows: first (WRONG!)
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
    echo !var!      REM Shows: second (CORRECT!)
)
endlocal
pause
```

**Use `!` instead of `%` inside loops and blocks!**

---

## Arrays Simulation

### Create Array
```batch
@echo off
setlocal enabledelayedexpansion

set arr[0]=Apple
set arr[1]=Banana
set arr[2]=Cherry
set arrLength=3

REM Access elements
for /l %%i in (0,1,%arrLength%-1) do (
    echo !arr[%%i]!
)

pause
```

### Search Array
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

## Regular Expressions

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

**Advanced techniques for powerful scripts!** 🎯
