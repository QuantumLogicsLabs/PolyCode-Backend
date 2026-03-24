# Functions Library

## Reusable Code Blocks! 🔄

Create and use functions in batch scripts.

---

## Basic Functions

### Simple Subroutine
```batch
@echo off
call :Greet
call :Greet
goto :EOF

:Greet
echo Hello there!
goto :EOF
```

### Function with Parameters
```batch
@echo off
call :Greet John
call :Greet Jane
goto :EOF

:Greet
echo Hello, %1!
goto :EOF
```

---

## Advanced Functions

### Multiple Parameters
```batch
@echo off
call :Add 5 3
call :Add 10 20
goto :EOF

:Add
set /a sum=%1+%2
echo Sum: %sum%
goto :EOF
```

### Return Value
```batch
@echo off
call :GetMax 10 20
echo Max is: %max%
goto :EOF

:GetMax
if %1 gtr %2 (
    set max=%1
) else (
    set max=%2
)
goto :EOF
```

---

## Function Library

### Math Library
```batch
:Square
set /a result=%1*%1
goto :EOF

:Cube
set /a result=%1*%1*%1
goto :EOF

:Power
set result=1
for /l %%i in (1,1,%2) do set /a result*=%1
goto :EOF
```

### String Library
```batch
:ToUpper
REM Convert to uppercase (simplified)
set result=%1
goto :EOF

:StringLength
set str=%1
set len=0
for %%c in (!str!) do set /a len+=1
goto :EOF
```

---

**Build reusable function libraries!** 🎯
