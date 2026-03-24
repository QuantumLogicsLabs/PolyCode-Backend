# Common Code Patterns

## Reusable Batch Scripting Patterns

Copy and adapt these proven patterns for your scripts.

---

## Pattern 1: Menu System

```batch
@echo off
:menu
cls
echo ================================
echo    Main Menu
echo ================================
echo 1. Option One
echo 2. Option Two
echo 3. Option Three
echo 4. Exit
echo ================================
set /p choice=Enter your choice (1-4): 

if "%choice%"=="1" goto option1
if "%choice%"=="2" goto option2
if "%choice%"=="3" goto option3
if "%choice%"=="4" goto end
goto menu

:option1
echo You chose option 1
pause
goto menu

:option2
echo You chose option 2
pause
goto menu

:option3
echo You chose option 3
pause
goto menu

:end
echo Goodbye!
pause
```

---

## Pattern 2: Confirmation Prompt

```batch
@echo off
set /p confirm="Are you sure? (Y/N): "
if /i not "%confirm%"=="Y" (
    echo Operation cancelled
    exit /b 1
)
echo Proceeding...
```

---

## Pattern 3: Input Validation

```batch
@echo off
:ask_age
set /p age=Enter your age (1-120): 
if %age% lss 1 goto ask_age
if %age% gtr 120 goto ask_age
echo Age validated: %age%
```

---

## Pattern 4: File Existence Check

```batch
@echo off
set "filename=test.txt"
if not exist "%filename%" (
    echo ERROR: %filename% not found!
    exit /b 1
)
echo File found, processing...
```

---

## Pattern 5: Loop with Counter

```batch
@echo off
setlocal enabledelayedexpansion
set count=0
for %%f in (*.txt) do (
    set /a count+=1
    echo !count!: %%f
)
echo Total: %count% files
```

---

## Pattern 6: Retry Logic

```batch
@echo off
setlocal enabledelayedexpansion
set maxtries=3
set try=1

:retry
echo Attempt !try! of %maxtries%
copy source.txt dest.txt > nul 2>&1
if !ERRORLEVEL! equ 0 goto success
if !try! geq %maxtries% goto failed
set /a try+=1
timeout /t 2 > nul
goto retry

:success
echo Copy successful!
goto end

:failed
echo Copy failed after %maxtries% attempts
exit /b 1

:end
pause
```

---

## Pattern 7: Progress Indicator

```batch
@echo off
setlocal enabledelayedexpansion
echo Starting process...
for /l %%i in (1,1,10) do (
    set /a percent=%%i*10
    echo Progress: !percent!%% [!repeat!]
    timeout /t 1 > nul
)
echo Complete!
pause
```

---

## Pattern 8: Logging

```batch
@echo off
set "logfile=script.log"
set "timestamp=%date:~-4,4%%date:~-7,2%%date:~-10,2%_%time:~0,2%%time:~3,2%"
set "timestamp=%timestamp: =0%"

echo [%timestamp%] Script started >> "%logfile%"
echo Processing... >> "%logfile%"
echo [%timestamp%] Script completed >> "%logfile%"
```

---

## Pattern 9: Cleanup on Exit

```batch
@echo off
setlocal enabledelayedexpansion

REM Create temp files
set "tempfile1=temp1.txt"
set "tempfile2=temp2.txt"

REM Ensure cleanup on exit
goto main

:cleanup
echo Cleaning up...
if exist "%tempfile1%" del "%tempfile1%"
if exist "%tempfile2%" del "%tempfile2%"
exit /b

:main
echo Doing work...
goto cleanup
```

---

## Pattern 10: Multiple Choice

```batch
@echo off
:choose
echo Select an option:
echo A. First choice
echo B. Second choice
echo C. Third choice
set /p opt=Your choice: 

if /i "%opt%"=="A" goto first
if /i "%opt%"=="B" goto second
if /i "%opt%"=="C" goto third
echo Invalid choice
goto choose

:first
echo You chose A
goto end

:second
echo You chose B
goto end

:third
echo You chose C
goto end

:end
pause
```

---

## Pattern 11: Wait for Key

```batch
@echo off
echo Press any key to continue...
pause > nul
```

---

## Pattern 12: Timed Wait

```batch
@echo off
echo Waiting 5 seconds...
timeout /t 5 /nobreak
echo Done waiting!
```

---

## Pattern 13: Administrator Check

```batch
@echo off
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: Run as Administrator!
    pause
    exit /b 1
)
echo Running with admin privileges
```

---

## Pattern 14: Backup Before Modify

```batch
@echo off
set "file=config.ini"
if exist "%file%" (
    copy "%file%" "%file%.backup" > nul
    echo Backup created: %file%.backup
)
```

---

## Pattern 15: Safe Delete

```batch
@echo off
set "folder=C:\Temp"
if exist "%folder%" (
    dir /b "%folder%" > nul 2>&1
    if !ERRORLEVEL! equ 0 (
        echo Folder has files, deleting...
        rmdir /s /q "%folder%"
    ) else (
        echo Folder is empty or doesn't exist
    )
)
```

---

Use these patterns as building blocks for your scripts! 🛠️
