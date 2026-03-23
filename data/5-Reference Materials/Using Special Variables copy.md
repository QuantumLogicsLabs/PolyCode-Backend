# Using Special Variables Effectively

## Practical Guide to Batch Variables

Learn how to use special variables in real-world scenarios.

---

## Building Useful Scripts

### 1. Automated Backup Script
```batch
@echo off
setlocal enabledelayedexpansion

REM Create timestamped backup folder
set "backupdir=%HOMEDRIVE%%HOMEPATH%\Backup\%date:~-4,4%%date:~-7,2%%date:~-10,2%"

echo ============================================
echo    Automated Backup Script
echo ============================================
echo.
echo Date: %DATE%
echo Time: %TIME%
echo User: %USERNAME%
echo Computer: %COMPUTERNAME%
echo.
echo Creating backup folder: %backupdir%

if not exist "%backupdir%" (
    mkdir "%backupdir%"
    echo Backup folder created!
) else (
    echo Backup folder already exists.
)

echo.
echo Backing up Documents...
xcopy "%HOMEDRIVE%%HOMEPATH%\Documents" "%backupdir%\Documents" /E /I /S /Y > nul

echo.
echo Backing up Desktop...
xcopy "%HOMEDRIVE%%HOMEPATH%\Desktop" "%backupdir%\Desktop" /E /I /S /Y > nul

echo.
echo ============================================
echo    Backup Complete!
echo ============================================
echo Location: %backupdir%
echo Finished at: %TIME%
pause
```

### 2. System Information Reporter
```batch
@echo off
set "reportfile=SystemReport_%computername%_%date:~-4,4%%date:~-7,2%%date:~-10,2%.txt"

(
    echo ============================================
    echo SYSTEM INFORMATION REPORT
    echo ============================================
    echo Generated: %DATE% %TIME%
    echo Computer Name: %COMPUTERNAME%
    echo Username: %USERNAME%
    echo Current Directory: %CD%
    echo Home Drive: %HOMEDRIVE%
    echo Home Path: %HOMEPATH%
    echo.
    echo ============================================
    echo RUNNING PROCESSES
    echo ============================================
    tasklist
    echo.
    echo ============================================
    echo DISK USAGE
    echo ============================================
    wmic logicaldisk get size,freespace,caption
    echo.
    echo ============================================
    echo NETWORK CONFIGURATION
    echo ============================================
    ipconfig /all
) > "%reportfile%"

echo Report saved to: %reportfile%
pause
```

### 3. Random Quiz Question Generator
```batch
@echo off
setlocal enabledelayedexpansion

set "questions=5"
set "score=0"

echo ============================================
echo    Random Quiz Generator
echo ============================================
echo.

for /l %%q in (1,1,%questions%) do (
    set /a num1=!RANDOM! %% 20 + 1
    set /a num2=!RANDOM! %% 20 + 1
    set /a correct_answer=!num1! + !num2!
    
    echo Question %%q: What is !num1! + !num2!?
    set /p user_answer=Your answer: 
    
    if "!user_answer!"=="!correct_answer!" (
        echo ✓ Correct!
        set /a score+=1
    ) else (
        echo ✗ Wrong! The answer was !correct_answer!
    )
    echo.
)

echo ============================================
echo    Quiz Complete!
echo ============================================
echo Your Score: %score% out of %questions%
if %score% equ %questions% (
    echo Perfect score! Excellent work!
) else if %score% geq 3 (
    echo Good job! Keep practicing!
) else (
    echo Keep trying! You'll get better!
)
pause
```

---

## Working with Parameters

### Flexible File Processor
```batch
@echo off
setlocal enabledelayedexpansion

set "action="
set "source="
set "dest="

REM Parse parameters
:parse
if "%~1"=="" goto validate
if /i "%~1"=="-copy" set "action=copy" & shift & goto parse
if /i "%~1"=="-move" set "action=move" & shift & goto parse
if /i "%~1"=="-delete" set "action=delete" & shift & goto parse
if "%source%"=="" set "source=%~1" & shift & goto parse
set "dest=%~1"

:validate
if "%action%"=="" (
    echo ERROR: No action specified!
    echo Usage: %0 [-copy^-move^-delete] source [destination]
    exit /b 1
)

if "%source%"=="" (
    echo ERROR: No source specified!
    exit /b 1
)

echo Action: %action%
echo Source: %source%
if "%dest%" neq "" echo Destination: %dest%

REM Execute action
if "%action%"=="copy" (
    copy "%source%" "%dest%" /Y
) else if "%action%"=="move" (
    move "%source%" "%dest%"
) else if "%action%"=="delete" (
    del "%source%"
)

echo Done!
pause
```

**Usage Examples:**
```
script.bat -copy file.txt backup\
script.bat -move old.txt archive\
script.bat -delete temp.txt
```

---

## Error Handling Patterns

### Robust Script Template
```batch
@echo off
setlocal enabledelayedexpansion

set "errorcount=0"

echo Starting script at %TIME%
echo.

REM Check if required files exist
if not exist "required.txt" (
    echo ERROR: required.txt not found!
    set /a errorcount+=1
    goto errorhandler
)

REM Try to perform operation
copy "source.txt" "dest.txt" > nul 2>&1
if !ERRORLEVEL! neq 0 (
    echo ERROR: Copy failed!
    set /a errorcount+=1
)

:errorhandler
if %errorcount% gtr 0 (
    echo.
    echo Script completed with %errorcount% error(s)
    echo Error level: %ERRORLEVEL%
    exit /b 1
)

echo.
echo Script completed successfully!
exit /b 0
```

---

## Tips and Best Practices

### 1. Always Quote Paths
```batch
REM ✅ Good
if exist "%HOMEDRIVE%%HOMEPATH%\file.txt" (
    copy "%source%" "%dest%"
)

REM ❌ Bad
if exist %HOMEDRIVE%%HOMEPATH%\file.txt (
    copy %source% %dest%
)
```

### 2. Use Delayed Expansion in Loops
```batch
setlocal enabledelayedexpansion
set count=0
for %%i in (*.txt) do (
    set /a count+=1
    echo File !count!: %%i
)
endlocal
```

### 3. Validate Input
```batch
if not defined PARAM (
    echo ERROR: Parameter not set!
    exit /b 1
)

if "%PARAM%"=="" (
    echo ERROR: Parameter is empty!
    exit /b 1
)
```

### 4. Clean Up After Yourself
```batch
setlocal
REM Your code here
endlocal
REM Variables automatically cleaned up
```

---

## Common Use Cases

| Task | Variables to Use |
|------|------------------|
| Backup files | `%DATE%`, `%TIME%`, `%HOMEDRIVE%` |
| Process arguments | `%1`-`%9`, `%*` |
| Error handling | `%ERRORLEVEL%` |
| Random selection | `%RANDOM%` |
| System info | `%USERNAME%`, `%COMPUTERNAME%` |
| File paths | `%CD%`, `%HOMEPATH%` |

---

## Practice Exercises

1. Create a script that renames all files with today's date
2. Build a menu system using parameters
3. Make a script that logs actions with timestamps
4. Create a game using random numbers
