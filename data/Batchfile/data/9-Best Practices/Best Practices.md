# Batch File Best Practices

Writing good batch scripts is about more than just making them work. This guide covers professional practices that will make your scripts reliable, maintainable, and efficient.

## Table of Contents

1. [Code Organization](#code-organization)
2. [Error Handling](#error-handling)
3. [Security Practices](#security-practices)
4. [Performance Tips](#performance-tips)
5. [Documentation Standards](#documentation-standards)
6. [Testing Strategies](#testing-strategies)
7. [Common Patterns](#common-patterns)

---

## Code Organization

### 1. Use Clear Structure

**❌ Bad:**
```batch
@echo off
set x=5
set y=10
if %x%==5 echo Five
for %%i in (1,2,3) do echo %%i
```

**✅ Good:**
```batch
@echo off
REM ============================================
REM Main Script Logic
REM ============================================

REM Initialize variables
set "counter=5"
set "limit=10"

REM Check counter value
if %counter% equ 5 (
    echo Counter is set to five
)

REM Loop through numbers
for /l %%i in (1,1,3) do (
    echo Processing number %%i
)
```

### 2. Group Related Commands

Organize your script into logical sections:

```batch
@echo off
REM ============================================
REM SECTION 1: Initialization
REM ============================================
setlocal enabledelayedexpansion
set "startTime=%time%"

REM ============================================
REM SECTION 2: Configuration
REM ============================================
set "sourceFolder=C:\Data"
set "backupFolder=D:\Backup"

REM ============================================
REM SECTION 3: Main Processing
REM ============================================
call :ProcessFiles
call :CreateReport

REM ============================================
REM SECTION 4: Cleanup
REM ============================================
endlocal
echo Completed at %time%
pause
```

### 3. Use Functions/Subroutines

Break complex logic into reusable functions:

```batch
@echo off
call :ValidateInput
call :ProcessData
call :GenerateOutput
goto :EOF

:ValidateInput
if not exist "%inputFile%" (
    echo Error: Input file not found
    exit /b 1
)
exit /b 0

:ProcessData
REM Processing logic here
exit /b 0

:GenerateOutput
echo Report generated successfully
exit /b 0
```

---

## Error Handling

### 1. Always Check for Errors

**❌ Bad:**
```batch
copy file.txt backup\
del file.txt
```

**✅ Good:**
```batch
if not exist "backup\" mkdir "backup"
copy "file.txt" "backup\" > nul
if errorlevel 1 (
    echo ERROR: Copy failed!
    exit /b 1
)
del "file.txt" > nul
if errorlevel 1 (
    echo WARNING: Delete failed, but copy succeeded
)
```

### 2. Use Proper Exit Codes

```batch
@echo off
setlocal

REM Success = 0
REM General error = 1
REM File not found = 2
REM Access denied = 3

if not exist "input.txt" (
    echo ERROR: input.txt not found
    exit /b 2
)

REM Your code here

if errorlevel 1 (
    echo ERROR: Operation failed
    exit /b 1
)

echo SUCCESS
exit /b 0
```

### 3. Implement Error Logging

```batch
@echo off
set "logfile=%~dp0script.log"

:LogMessage
rem Usage: call :LogMessage "LEVEL" "Message"
echo [%date% %time%] [%~1] %~2 >> "%logfile%"
echo [%date% %time%] [%~1] %~2
goto :EOF

call :LogMessage "INFO" "Script started"

REM Your code here
if errorlevel 1 (
    call :LogMessage "ERROR" "Operation failed with errorlevel %errorlevel%"
    exit /b 1
)

call :LogMessage "INFO" "Script completed successfully"
```

### 4. Graceful Failure Recovery

```batch
@echo off
set "retryCount=0"
set "maxRetries=3"

:TryOperation
ping -n 1 example.com > nul
if errorlevel 1 (
    set /a retryCount+=1
    if !retryCount! leq !maxRetries! (
        echo Retry !retryCount! of !maxRetries!...
        timeout /t 2 > nul
        goto TryOperation
    ) else (
        echo Operation failed after !maxRetries! attempts
        exit /b 1
    )
)
echo Operation successful
exit /b 0
```

---

## Security Practices

### 1. Validate ALL Inputs

**❌ Dangerous:**
```batch
set /p filename="Enter filename: "
type "%filename%"
```

**✅ Safe:**
```batch
set /p filename="Enter filename: "

REM Remove dangerous characters
set "filename=%filename:<=%"
set "filename=%filename:>=%"
set "filename=%filename:|=%"
set "filename=%filename:&=%"

REM Check if file exists in allowed directory
if not exist "allowed\%filename%" (
    echo ERROR: Invalid filename
    exit /b 1
)

type "allowed\%filename%"
```

### 2. Quote All Paths

**❌ Risky:**
```batch
copy C:\My Documents\file.txt C:\Backup
```

**✅ Safe:**
```batch
copy "C:\My Documents\file.txt" "C:\Backup"
```

### 3. Avoid Hardcoded Passwords

**❌ NEVER do this:**
```batch
net use \\server\share /user:admin password123
```

**✅ Do this instead:**
```batch
REM Prompt for credentials
set /p username="Enter username: "
set /p password="Enter password: "
net use \\server\share /user:%username% %password%

REM Or use saved credentials in Windows Credential Manager
```

### 4. Check Permissions Early

```batch
@echo off
net session >nul 2>&1
if errorlevel 1 (
    echo ERROR: This script requires Administrator privileges
    echo Please right-click and select "Run as Administrator"
    pause
    exit /b 1
)
echo Administrator access confirmed
```

---

## Performance Tips

### 1. Minimize Disk I/O

**❌ Slow:**
```batch
for %%f in (*.txt) do (
    type "%%f"
    echo.
    echo ---
    echo.
)
```

**✅ Faster:**
```batch
(
    for %%f in (*.txt) do (
        type "%%f"
        echo.
        echo ---
        echo.
    )
) > output.txt
```

### 2. Use Appropriate FOR Loops

**❌ Inefficient:**
```batch
set i=1
:loop
if %i% gtr 100 goto end
echo %i%
set /a i+=1
goto loop
:end
```

**✅ Efficient:**
```batch
for /l %%i in (1,1,100) do (
    echo %%i
)
```

### 3. Disable Unnecessary Features

```batch
@echo off
REM Start with clean environment
setlocal
REM Disable command extensions if not needed
set "CMDEXTVERSION="
```

### 4. Cache Frequently Used Values

**❌ Repeated calls:**
```batch
for %%f in (*.txt) do (
    for %%d in ("%%~dpf.") do (
        echo %%f is in %%~nd
    )
)
```

**✅ Cached value:**
```batch
for %%f in (*.txt) do (
    set "filepath=%%~dpf"
    for %%d in ("!filepath!.") do (
        echo %%f is in %%~nd
    )
)
```

---

## Documentation Standards

### 1. Add Header Comments

```batch
REM ============================================
REM Script Name: BackupSystem.bat
REM Description: Automated backup solution
REM Author: John Doe
REM Version: 1.2.0
REM Date: 2024-01-15
REM Requirements: Windows 10+, Admin rights
REM Usage: Run from Task Scheduler or manually
REM ============================================
```

### 2. Document Variables

```batch
REM Configuration Variables:
REM   sourceDir     - Source directory for files to backup
REM   backupDir     - Destination backup location
REM   maxRetries    - Number of retry attempts on failure
REM   logFile       - Path to log file
REM   compressFiles - Enable compression (1=yes, 0=no)
```

### 3. Comment Complex Logic

```batch
REM Extract date components in YYYYMMDD format
REM %date% format: "Mon 01/15/2024"
REM We need: year (chars 10-13), month (5-6), day (7-8)
set "year=%date:~-4,4%"
set "month=%date:~-7,2%"
set "day=%date:~-10,2%"
set "fileDate=%year%%month%%day%"
```

### 4. Create Usage Instructions

```batch
REM ============================================
REM USAGE:
REM   backup.bat [source] [destination] [/quiet]
REM
REM PARAMETERS:
REM   source      - Folder to backup (required)
REM   destination - Backup location (optional, default: D:\Backup)
REM   /quiet      - Suppress progress messages
REM
REM EXAMPLES:
REM   backup.bat C:\Documents
REM   backup.bat C:\Data D:\Backups /quiet
REM ============================================
```

---

## Testing Strategies

### 1. Test with Different Scenarios

Create test cases:

```batch
REM TEST CASES:
REM 1. Normal operation with valid files
REM 2. Empty source folder
REM 3. Missing source folder
REM 4. Files with special characters in names
REM 5. Very long file paths
REM 6. No write permissions
REM 7. Disk full scenario
```

### 2. Use Debug Mode

```batch
@echo off
set "DEBUG=1"

:DebugEcho
if "%DEBUG%"=="1" echo [DEBUG] %*
goto :EOF

call :DebugEcho "Starting process..."
REM Your code here
call :DebugEcho "Processing complete"
```

### 3. Create Test Environment

```batch
REM Set up test environment
set "testDir=%TEMP%\BatchTest_%RANDOM%"
mkdir "%testDir%"
mkdir "%testDir%\Input"
mkdir "%testDir%\Output"

REM Create test files
echo Test content > "%testDir%\Input\test1.txt"
echo More content > "%testDir%\Input\test2.txt"

REM Run tests
call :RunTests

REM Cleanup
rd /s /q "%testDir%"
```

---

## Common Patterns

### 1. Singleton Pattern (Prevent Multiple Instances)

```batch
@echo off
set "mutexName=MyUniqueScriptName"

REM Try to create mutex
openfiles >nul 2>&1
if errorlevel 1 (
    REM First instance - continue
) else (
    REM Check if already running
    for /f "tokens=2" %%i in ('tasklist /fi "imagename eq cmd.exe" /fo csv ^| find /c "cmd.exe"') do (
        if %%i gtr 1 (
            echo Another instance is already running!
            exit /b 1
        )
    )
)
```

### 2. Configuration File Pattern

```batch
@echo off
set "configFile=%~dp0config.ini"

if exist "%configFile%" (
    for /f "tokens=1,2 delims==" %%a in (%configFile%) do (
        set "%%a=%%b"
    )
) else (
    REM Create default config
    (
        echo sourcePath=C:\Data
        echo destPath=D:\Backup
        echo maxRetries=3
    ) > "%configFile%"
)
```

### 3. Progress Indicator Pattern

```batch
@echo off
setlocal enabledelayedexpansion

set "total=100"
set "current=0"

for /l %%i in (1,1,%total%) do (
    REM Do work here
    
    set /a current+=1
    set /a percent=current*100/total
    
    REM Show progress
    <nul set /p ".=Processing: !percent!%% &#13;"
)

echo Complete!                                      
```

### 4. Menu System Pattern

```batch
@echo off
:MainMenu
cls
echo ================================
echo         MAIN MENU
echo ================================
echo 1. Option One
echo 2. Option Two
echo 3. Option Three
echo 4. Exit
echo ================================
set /p choice="Select option: "

if "%choice%"=="1" (
    call :OptionOne
    goto MainMenu
)
if "%choice%"=="2" (
    call :OptionTwo
    goto MainMenu
)
if "%choice%"=="3" (
    call :OptionThree
    goto MainMenu
)
if "%choice%"=="4" (
    echo Goodbye!
    exit /b 0
)
echo Invalid option!
timeout /t 2 >nul
goto MainMenu

:OptionOne
echo Executing Option One...
timeout /t 2 >nul
exit /b 0
```

---

## Checklist Before Deployment

Before sharing or deploying your batch script:

- [ ] Added comprehensive header comments
- [ ] Validated all user inputs
- [ ] Implemented error handling
- [ ] Tested with edge cases
- [ ] Quoted all paths
- [ ] Removed debug code
- [ ] Added logging (if needed)
- [ ] Documented return codes
- [ ] Created usage instructions
- [ ] Tested on target systems
- [ ] Checked security implications
- [ ] Optimized performance
- [ ] Backed up important data
- [ ] Added cleanup code
- [ ] Verified exit codes

---

## Summary

Following these best practices will help you create:

✅ **Reliable** scripts that handle errors gracefully  
✅ **Maintainable** code that others can understand  
✅ **Secure** solutions that protect against misuse  
✅ **Efficient** automation that runs quickly  
✅ **Professional** quality worthy of production use  

Remember: **Good habits make good scripts!**

For more examples, see [Examples](../05_Examples/)  
For command reference, see [Reference](../04_Reference/)
