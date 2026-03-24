# Special Variables Deep Dive

## Master Batch Scripting Variables

Let's explore special variables in more detail with practical examples!

---

## Date and Time Formatting

### Extract Date Components
```batch
@echo off
setlocal enabledelayedexpansion

REM Get date parts
set "yyyy=%date:~-4,4%"
set "mm=%date:~-7,2%"
set "dd=%date:~-10,2%"

echo Year: %yyyy%
echo Month: %mm%
echo Day: %dd%

REM Create formatted date
set "formatted=%yyyy%-%mm%-%dd%"
echo Formatted: %formatted%
pause
```

### Create Timestamp for Filenames
```batch
@echo off
set "timestamp=%date:~-4,4%%date:~-7,2%%date:~-10,2%_%time:~0,2%%time:~3,2%"
set "timestamp=%timestamp: =0%"
echo Backup_%timestamp%.zip
pause
```

---

## Random Number Tricks

### Generate Range of Numbers
```batch
@echo off
REM Random number between 1 and 10
set /a random10=%RANDOM% %% 10 + 1
echo 1-10: %random10%

REM Random number between 50 and 100
set /a random50to100=%RANDOM% %% 51 + 50
echo 50-100: %random50to100%

REM Random number between -10 and 10
set /a randomNeg=%RANDOM% %% 21 - 10
echo -10 to 10: %randomNeg%
pause
```

### Simple Dice Roller
```batch
@echo off
:roll
set /a dice1=%RANDOM% %% 6 + 1
set /a dice2=%RANDOM% %% 6 + 1
set /a total=%dice1% + %dice2%

echo You rolled: %dice1% and %dice2% = %total%
set /p again=Roll again? (Y/N): 
if /i "%again%"=="Y" goto roll
pause
```

---

## Error Handling with ERRORLEVEL

### Check Command Success
```batch
@echo off
copy source.txt dest.txt > nul 2>&1
if %ERRORLEVEL% equ 0 (
    echo Copy successful!
) else (
    echo Copy failed with error %ERRORLEVEL%
)
pause
```

### Multiple Error Checks
```batch
@echo off
setlocal enabledelayedexpansion

dir C:\ > nul
set "lasterror=!ERRORLEVEL!"
echo Dir command error level: !lasterror!

cd C:\NonExistent > nul 2>&1
set "lasterror=!ERRORLEVEL!"
echo CD command error level: !lasterror!
pause
```

---

## Parameter Processing

### Shift Command
```batch
@echo off
:loop
if "%1"=="" goto end
echo Parameter %~1: %1
shift
goto loop
:end
echo Done processing parameters
pause
```

### Named Parameters
```batch
@echo off
set "name="
set "age="

:parse
if "%1"=="" goto process
if "%1"=="-name" set "name=%2" & shift & shift & goto parse
if "%1"=="-age" set "age=%2" & shift & shift & goto parse
shift
goto parse

:process
echo Name: %name%
echo Age: %age%
pause
```

**Usage:**
```
script.bat -name John -age 25
```

---

## Advanced Techniques

### Delayed Expansion with Special Vars
```batch
@echo off
setlocal enabledelayedexpansion

set counter=0
for %%i in (1,2,3,4,5) do (
    set /a counter+=1
    echo Counter is: !counter! at !TIME!
)
pause
```

### Build Dynamic Paths
```batch
@echo off
set "backup=%HOMEDRIVE%%HOMEPATH%\Backup\%date:~-4,4%%date:~-7,2%%date:~-10,2%"
echo Creating backup folder: %backup%
if not exist "%backup%" mkdir "%backup%"
pause
```

---

## Common Mistakes

### ❌ Wrong: Spaces Around Equals
```batch
set var = value    REM Wrong!
echo [%var%]       REM Shows [ ]
```

### ✅ Correct: No Spaces
```batch
set "var=value"    REM Correct!
echo [%var%]       REM Shows [value]
```

### ❌ Wrong: Using % Inside Loop
```batch
set i=0
for /l %%n in (1,1,3) do (
    set /a i+=1
    echo %i%        REM Always shows 0
)
```

### ✅ Correct: Use ! with Delayed Expansion
```batch
setlocal enabledelayedexpansion
set i=0
for /l %%n in (1,1,3) do (
    set /a i+=1
    echo !i!        REM Shows 1, 2, 3
)
endlocal
```

---

## Practice Projects

1. **Date Calculator**: Calculate days between dates
2. **Random Password Generator**: Create secure passwords
3. **System Info Reporter**: Display computer details
4. **Parameter Parser**: Build a flexible command parser
