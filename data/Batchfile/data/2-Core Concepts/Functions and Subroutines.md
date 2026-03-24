# Functions and Subroutines

## Reuse Your Code! 🔄

Functions let you write code once and use it many times.

---

## Creating Functions

### Basic Subroutine
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
call :Greet Bob
goto :EOF

:Greet
echo Hello, %1!
goto :EOF
```

**How it works:**
- `%1` = First parameter
- `%2` = Second parameter
- `%9` = Ninth parameter

---

## Multiple Parameters

### Example - Add Numbers
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

### Example - Create User
```batch
@echo off
call :CreateUser Alice password123
call :CreateUser Bob secret456
goto :EOF

:CreateUser
echo Creating user: %1
echo Password: %2
mkdir "C:\Users\%1"
goto :EOF
```

---

## Return Values

### Using ERRORLEVEL
```batch
@echo off
call :CheckAge 20
if errorlevel 1 (
    echo Adult
) else (
    echo Minor
)
goto :EOF

:CheckAge
if %1 geq 18 (
    exit /b 1
) else (
    exit /b 0
)
```

### Setting Variable
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

## Practical Examples

### Math Library
```batch
@echo off
call :Square 5
echo 5 squared = %result%

call :Cube 3
echo 3 cubed = %result%
goto :EOF

:Square
set /a result=%1*%1
goto :EOF

:Cube
set /a result=%1*%1*%1
goto :EOF
```

### String Functions
```batch
@echo off
call :ToUpper hello
echo Uppercase: %result%

call :ToLower HELLO
echo Lowercase: %result%
goto :EOF

:ToUpper
REM Simplified example
set result=%1
goto :EOF

:ToLower
set result=%1
goto :EOF
```

---

## Best Practices 💡

1. **Always use CALL** for subroutines
2. **End with GOTO :EOF** or EXIT /B
3. **Use meaningful names** for functions
4. **Document parameters** in comments

---

**Functions make code reusable and organized!** 🎯
