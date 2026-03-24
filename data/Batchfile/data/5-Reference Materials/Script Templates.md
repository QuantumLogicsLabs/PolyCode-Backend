# Script Templates Collection

## Ready-to-Use Batch Script Templates

Copy these templates and customize for your needs!

---

## Template 1: Basic Script Structure

```batch
@echo off
setlocal enabledelayedexpansion

REM ============================================
REM Script Name: [Your Script Name]
REM Purpose: [What it does]
REM Author: [Your name]
REM Date: [Created date]
REM Version: 1.0
REM ============================================

REM --- Configuration ---
set "version=1.0"
set "scriptname=%~n0"

REM --- Main Logic ---
echo Starting %scriptname% v%version%
echo.

REM Your code here

echo.
echo Script completed successfully!
pause
exit /b 0
```

---

## Template 2: File Processing Script

```batch
@echo off
setlocal enabledelayedexpansion

REM ============================================
REM File Processing Script
REM ============================================

set "sourcefolder=C:\Source"
set "destfolder=C:\Destination"
set "filemask=*.txt"
set "count=0"

echo Processing files from %sourcefolder%
echo.

if not exist "%sourcefolder%" (
    echo ERROR: Source folder not found!
    exit /b 1
)

if not exist "%destfolder%" mkdir "%destfolder%"

for %%f in ("%sourcefolder%\%filemask%") do (
    set /a count+=1
    echo !count!: Processing %%~nxf
    copy "%%f" "%destfolder%" > nul
)

echo.
echo Processed %count% file(s)
echo Destination: %destfolder%
pause
```

---

## Template 3: Backup Script

```batch
@echo off
setlocal enabledelayedexpansion

REM ============================================
REM Automated Backup Script
REM ============================================

set "backupdate=%date:~-4,4%%date:~-7,2%%date:~-10,2%"
set "backuptime=%time:~0,2%%time:~3,2%"
set "backuptime=%backuptime: =0%"
set "backuproot=D:\Backups"
set "backupfolder=%backuproot%\Backup_%backupdate%_%backuptime%"

echo Creating backup folder: %backupfolder%
if not exist "%backupfolder%" mkdir "%backupfolder%"

echo.
echo Backing up important data...

REM Add your backup sources here
xcopy "%USERPROFILE%\Documents" "%backupfolder%\Documents" /E /I /S /Y
xcopy "%USERPROFILE%\Desktop" "%backupfolder%\Desktop" /E /I /S /Y

echo.
echo Backup complete!
echo Location: %backupfolder%
pause
```

---

## Template 4: Menu System

```batch
@echo off
setlocal enabledelayedexpansion

:mainmenu
cls
echo ================================
echo    [Your Program Title]
echo ================================
echo.
echo 1. Option One
echo 2. Option Two
echo 3. Option Three
echo 4. Exit
echo.
set /p choice=Enter your choice (1-4): 

if "%choice%"=="1" goto option1
if "%choice%"=="2" goto option2
if "%choice%"=="3" goto option3
if "%choice%"=="4" goto end
goto mainmenu

:option1
cls
echo === Option One ===
REM Your code here
pause
goto mainmenu

:option2
cls
echo === Option Two ===
REM Your code here
pause
goto mainmenu

:option3
cls
echo === Option Three ===
REM Your code here
pause
goto mainmenu

:end
cls
echo Goodbye!
pause
```

---

## Template 5: Interactive Form

```batch
@echo off
setlocal enabledelayedexpansion

echo ================================
echo    Data Entry Form
echo ================================
echo.

set /p name=Enter your name: 
set /p email=Enter your email: 
set /p age=Enter your age: 

echo.
echo ================================
echo    Summary
echo ================================
echo Name: %name%
echo Email: %email%
echo Age: %age%
echo ================================
echo.

set /p confirm=Is this correct? (Y/N): 
if /i "%confirm%" neq "Y" (
    echo Restarting...
    goto start
)

echo Data saved!
pause
```

---

## Template 6: Installer Script

```batch
@echo off
setlocal enabledelayedexpansion

REM ============================================
REM Software Installer Template
REM ============================================

set "appname=MyApplication"
set "installdir=%PROGRAMFILES%\%appname%"

echo Installing %appname%...
echo.

REM Check admin rights
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: Run as Administrator!
    pause
    exit /b 1
)

REM Create installation folder
if not exist "%installdir%" mkdir "%installdir%"

REM Copy files
echo Copying files...
copy "*.exe" "%installdir%" > nul
copy "*.dll" "%installdir%" > nul

REM Create shortcuts
echo Creating shortcuts...

REM Add to PATH (optional)
setx PATH "%PATH%;%installdir%"

echo.
echo Installation complete!
pause
```

---

## Template 7: System Checker

```batch
@echo off
setlocal enabledelayedexpansion

echo ================================
echo    System Information
echo ================================
echo.

echo Computer: %COMPUTERNAME%
echo User: %USERNAME%
echo Date: %DATE%
echo Time: %TIME%
echo.

echo Disk Space:
wmic logicaldisk get caption,freesize,size | findstr /C:"C:"
echo.

echo Memory:
systeminfo | findstr /C:"Total Physical Memory"
echo.

echo Operating System:
ver
echo.

pause
```

---

## Template 8: Cleaner Script

```batch
@echo off
setlocal enabledelayedexpansion

echo ================================
echo    Cleanup Utility
echo ================================
echo.

set "targetfolder=%TEMP%"

echo Cleaning: %targetfolder%
echo.

set "count=0"
for %%f in ("%targetfolder%\*.*") do (
    set /a count+=1
    del "%%f" /q 2>nul
)

echo Deleted %count% files
echo.

echo Cleanup complete!
pause
```

---

## Template 9: Network Script

```batch
@echo off
setlocal enabledelayedexpansion

echo ================================
echo    Network Diagnostics
echo ================================
echo.

echo Testing connectivity...
ping -n 1 www.google.com > nul
if !ERRORLEVEL! equ 0 (
    echo Internet: Connected
) else (
    echo Internet: Disconnected
)

echo.
echo IP Configuration:
ipconfig | findstr /C:"IPv4"
echo.

echo Default Gateway:
ipconfig | findstr /C:"Default Gateway"
echo.

pause
```

---

## Template 10: Logger Script

```batch
@echo off
setlocal enabledelayedexpansion

set "logfile=%~dp0script.log"
set "timestamp=%date:~-4,4%%date:~-7,2%%date:~-10,2%_%time:~0,2%%time:~3,2%"
set "timestamp=%timestamp: =0%"

REM Log function
call :log "Script started"

REM Your code here
call :log "Processing files..."

REM More code
call :log "Script completed"

goto end

:log
echo [%timestamp%] %~1 >> "%logfile%"
goto :eof

:end
pause
```

---

Customize these templates for your specific needs! 📝
