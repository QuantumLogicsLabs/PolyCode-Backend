# Commands Deep Dive

## Master Every Command! 📚

Detailed reference for essential batch commands.

---

## ECHO Command

### Display Messages
```batch
echo Hello World          REM Basic message
echo.                     REM Blank line
echo ==================  REM Separator
echo Special chars: ^& ^| ^< ^>
```

### Control Echo Mode
```batch
@echo off                 REM Hide commands
echo on                   REM Show commands
echo off                  REM Hide after this line
```

---

## SET Command

### Create Variables
```batch
set name=John
set "path=C:\Program Files"
set /a number=5+3
set /p input=Enter value: 
```

### View Variables
```batch
set                       REM All variables
set name                  REM Specific variable
```

---

## IF Command

### Conditions
```batch
if exist file.txt echo Found
if "%var%"=="value" echo Match
if %num% equ 5 echo Five
if %num% gtr 10 echo Big
if defined var echo Set
if not exist file.txt echo Missing
```

### Operators
- `EQU` - Equal
- `NEQ` - Not equal
- `LSS` - Less than
- `LEQ` - Less or equal
- `GTR` - Greater than
- `GEQ` - Greater or equal

---

## FOR Command

### Basic Loop
```batch
for %%f in (*.txt) do echo %%f
for /l %%i in (1,1,10) do echo %%i
for /f "delims=" %%l in (file.txt) do echo %%l
```

### Modifiers
- `%%~fI` - Full path
- `%%~dI` - Drive
- `%%~nI` - Name
- `%%~xI` - Extension

---

**Command mastery achieved!** 🎯
