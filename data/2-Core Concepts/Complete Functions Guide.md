# Complete Functions Guide

## Master Subroutines! 🔄

Comprehensive guide to batch functions.

---

## Function Basics

### Creating Functions
```batch
@echo off
call :MyFunction
goto :EOF

:MyFunction
echo Inside function!
goto :EOF
```

### Parameters
```batch
@echo off
call :Greet John Alice Bob
goto :EOF

:Greet
echo Param 1: %1
echo Param 2: %2
echo Param 3: %3
echo All: %*
goto :EOF
```

---

## Advanced Techniques

### Return Values via Variable
```batch
@echo off
call :GetMax 10 20
echo Max: %max%
goto :EOF

:GetMax
if %1 gtr %2 (
    set max=%1
) else (
    set max=%2
)
goto :EOF
```

### Return Values via ERRORLEVEL
```batch
@echo off
call :IsEven 10
if errorlevel 1 (
    echo Even number
) else (
    echo Odd number
)
goto :EOF

:IsEven
set /a mod=%1%%2
if %mod% equ 0 (
    exit /b 1
) else (
    exit /b 0
)
```

---

## Function Library Examples

### String Functions
```batch
:StringToUpper
REM Convert string to uppercase
set result=%1
goto :EOF

:StringTrim
REM Trim spaces (simplified)
set result=%1
goto :EOF
```

### Math Functions
```batch
:Factorial
set result=1
for /l %%i in (1,1,%1) do set /a result*=%%i
goto :EOF

:Fibonacci
set a=0
set b=1
for /l %%i in (1,1,%1) do (
    set /a c=a+b
    set a=b
    set b=c
)
goto :EOF
```

---

**Build powerful function libraries!** 🎯
