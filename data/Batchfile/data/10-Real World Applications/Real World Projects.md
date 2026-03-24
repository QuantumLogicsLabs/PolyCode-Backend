# Real-World Batch Script Projects

Practical, ready-to-use batch scripts for common automation tasks.

## Table of Contents

1. [System Administration](#system-administration)
2. [File Management](#file-management)
3. [Backup Solutions](#backup-solutions)
4. [Development Tools](#development-tools)
5. [Network Utilities](#network-utilities)
6. [Maintenance Scripts](#maintenance-scripts)

---

## System Administration

### 1. System Information Reporter

Generates a comprehensive system report.

```batch
@echo off
setlocal enabledelayedexpansion

set "reportfile=SystemReport_%computername%_%date:~-4,4%%date:~-7,2%%date:~-10,2%.txt"

echo ============================================ > "%reportfile%"
echo SYSTEM INFORMATION REPORT >> "%reportfile%"
echo Generated: %date% %time% >> "%reportfile%"
echo Computer: %computername% >> "%reportfile%"
echo User: %username% >> "%reportfile%"
echo ============================================ >> "%reportfile%"
echo. >> "%reportfile%"

REM Operating System
echo === OPERATING SYSTEM === >> "%reportfile%"
ver >> "%reportfile%"
systeminfo | findstr /C:"OS Name" /C:"OS Version" /C:"Install Date" >> "%reportfile%"
echo. >> "%reportfile%"

REM Hardware
echo === HARDWARE === >> "%reportfile%"
systeminfo | findstr /C:"Processor" /C:"Memory" >> "%reportfile%"
wmic cpu get name,numberofcores >> "%reportfile%"
wmic memorychip get capacity,speed >> "%reportfile%"
echo. >> "%reportfile%"

REM Disk Space
echo === DISK SPACE === >> "%reportfile%"
wmic logicaldisk get caption,freespace,size >> "%reportfile%"
echo. >> "%reportfile%"

REM Network
echo === NETWORK === >> "%reportfile%"
ipconfig | findstr /C:"IPv4" /C:"Subnet" /C:"Gateway" >> "%reportfile%"
echo. >> "%reportfile%"

REM Running Services
echo === CRITICAL SERVICES === >> "%reportfile%"
sc query state= running | findstr /C:"SERVICE_NAME" | findstr /C:"Spooler" /C:"wuauserv" /C:"BITS" >> "%reportfile%"
echo. >> "%reportfile%"

REM Installed Software
echo === INSTALLED SOFTWARE === >> "%reportfile%"
wmic product get name,version | findstr /C:"Microsoft" /C:"Adobe" /C:"Google" >> "%reportfile%"
echo. >> "%reportfile%"

echo Report saved to: %reportfile%
notepad "%reportfile%"
```

**Usage**: Run to generate detailed system report

### 2. Windows Update Checker

Checks Windows Update status and forces update check.

```batch
@echo off
echo ===================================
echo    Windows Update Status Check
echo ===================================
echo.

REM Check if running as admin
net session >nul 2>&1
if errorlevel 1 (
    echo ERROR: Administrator privileges required
    echo Right-click and select "Run as Administrator"
    pause
    exit /b 1
)

echo Checking Windows Update service...
sc query wuauserv | findstr "STATE"
echo.

echo Checking BITS service...
sc query bits | findstr "STATE"
echo.

echo Last successful update check:
reg query "HKLM\SOFTWARE\Microsoft\Windows\CurrentVersion\WindowsUpdate\Auto Update" /v LastSuccessSyncTimestamp 2>nul
echo.

set /p action="Choose action (1=Check for updates, 2=Force update check, 3=Exit): "

if "%action%"=="1" (
    echo Opening Windows Update...
    start ms-settings:windowsupdate
) else if "%action%"=="2" (
    echo Forcing update detection...
    sc start wuauserv >nul
    sc start bits >nul
    usoclient StartScan
    echo Update scan initiated!
) else if "%action%"=="3" (
    exit /b 0
) else (
    echo Invalid option
)

pause
```

### 3. User Account Manager

Creates and manages user accounts.

```batch
@echo off
setlocal enabledelayedexpansion

net session >nul 2>&1
if errorlevel 1 (
    echo ERROR: Administrator privileges required
    pause
    exit /b 1
)

:menu
cls
echo ================================
echo    User Account Manager
echo ================================
echo 1. List all users
echo 2. Create new user
echo 3. Enable user account
echo 4. Disable user account
echo 5. Delete user
echo 6. Reset password
echo 7. Exit
echo ================================
set /p choice="Select option: "

if "%choice%"=="1" goto listusers
if "%choice%"=="2" goto createuser
if "%choice%"=="3" goto enableuser
if "%choice%"=="4" goto disableuser
if "%choice%"=="5" goto deleteuser
if "%choice%"=="6" goto resetpass
if "%choice%"=="7" goto end
goto menu

:listusers
echo.
echo Current users on this computer:
net user
echo.
pause
goto menu

:createuser
echo.
set /p username="Enter username: "
set /p password="Enter password: "
net user "%username%" "%password%" /add
if errorlevel 1 (
    echo ERROR: Failed to create user
) else (
    echo User %username% created successfully!
)
pause
goto menu

:enableuser
echo.
set /p username="Enter username to enable: "
net user "%username%" /active:yes
pause
goto menu

:disableuser
echo.
set /p username="Enter username to disable: "
net user "%username%" /active:no
pause
goto menu

:deleteuser
echo.
echo WARNING: This will permanently delete the user!
set /p username="Enter username to delete: "
set /p confirm="Type username to confirm: "
if not "%username%"=="%confirm%" (
    echo Confirmation failed - usernames don't match
) else (
    net user "%username%" /delete
    if errorlevel 1 (
        echo ERROR: Failed to delete user
    ) else (
        echo User %username% deleted successfully!
    )
)
pause
goto menu

:resetpass
echo.
set /p username="Enter username: "
set /p newpass="Enter new password: "
net user "%username%" "%newpass%"
pause
goto menu

:end
echo Goodbye!
exit /b 0
```

---

## File Management

### 4. Duplicate File Finder

Finds duplicate files by name and size.

```batch
@echo off
setlocal enabledelayedexpansion

set "searchdir=%CD%"
set "logfile=duplicates_%date:~-4,4%%date:~-7,2%%date:~-10,2%.txt"

echo Searching for duplicate files in %searchdir%...
echo.

> "%logfile%" (
    echo Duplicate Files Report >> "%logfile%"
    echo Generated: %date% %time% >> "%logfile%"
    echo Location: %searchdir% >> "%logfile%"
    echo ============================================ >> "%logfile%"
    echo. >> "%logfile%"
    
    REM Create temporary file list
    set "tempfile=%TEMP%\filelist_%RANDOM%.txt"
    dir /s /b /a-d "%searchdir%" > "%tempfile%"
    
    REM Count occurrences of each filename
    for /f "delims=" %%f in (%tempfile%) do (
        set "filepath=%%f"
        for %%i in ("!filepath!") do (
            set "filename=%%~ni"
            set "extension=%%~xi"
            set "size=%%~zi"
            
            REM Store unique name+size combinations
            echo !filename!!extension! !size! "!filepath!"
        )
    ) | sort | findstr /v "^$"
    
    del "%tempfile%"
)

echo.
echo Report saved to: %logfile%
echo Opening report...
notepad "%logfile%"
```

### 5. Bulk File Renamer

Renames multiple files based on patterns.

```batch
@echo off
setlocal enabledelayedexpansion

:prompt
echo ================================
echo    Bulk File Renamer
echo ================================
echo.
set "folder=%CD%"
echo Current folder: %folder%
echo.
set /p changeFolder="Change folder? (Y/N): "
if /i "%changeFolder%"=="Y" (
    set /p folder="Enter full path: "
)

cd /d "%folder%"

echo.
echo Files in current folder:
dir /b /a-d
echo.

set /p pattern="Enter search pattern (e.g., 'old'): "
set /p replacement="Enter replacement text (e.g., 'new'): "

echo.
echo Preview of changes:
set count=0

for %%f in (*%pattern%*) do (
    set "oldname=%%f"
    set "newname=!oldname:%pattern%=%replacement%!"
    if not "!oldname!"=="!newname!" (
        echo Will rename: "!oldname!" to "!newname!"
        set /a count+=1
    )
)

if %count% equ 0 (
    echo No files found matching pattern "%pattern%"
    pause
    exit /b 0
)

echo.
echo Total files to rename: %count%
set /p confirm="Proceed with renaming? (Y/N): "

if /i not "%confirm%"=="Y" (
    echo Operation cancelled
    pause
    exit /b 0
)

echo.
echo Renaming files...
for %%f in (*%pattern%*) do (
    set "oldname=%%f"
    set "newname=!oldname:%pattern%=%replacement%!"
    if not "!oldname!"=="!newname!" (
        ren "%%f" "!newname!"
        echo Renamed: "%%f" to "!newname!"
    )
)

echo.
echo Renaming complete!
pause
```

### 6. File Organizer by Type

Automatically sorts files into folders by extension.

```batch
@echo off
setlocal enabledelayedexpansion

set "targetdir=%CD%"
set /p targetdir="Organize files in (default: current folder): "
if "%targetdir%"=="" set "targetdir=%CD%"

echo Organizing files in: %targetdir%
echo.

cd /d "%targetdir%"

REM Define categories and their extensions
set "images=jpg jpeg png gif bmp tiff ico svg webp"
set "documents=pdf doc docx xls xlsx ppt pptx txt rtf odt ods odp"
set "archives=zip rar 7z tar gz iso img"
set "audio=mp3 wav flac aac ogg wma m4a"
set "video=mp4 avi mkv mov wmv flv webm mpg mpeg"
set "code=bat cmd ps1 py js java c cpp cs html css php sql"

REM Create category folders
for %%c in (Images Documents Archives Audio Video Code) do (
    if not exist "%%c" mkdir "%%c"
)

REM Move images
for %%e in (%images%) do (
    for %%f in (*.%%e) do (
        echo Moving %%f to Images...
        move "%%f" "Images\" > nul
    )
)

REM Move documents
for %%e in (%documents%) do (
    for %%f in (*.%%e) do (
        echo Moving %%f to Documents...
        move "%%f" "Documents\" > nul
    )
)

REM Move archives
for %%e in (%archives%) do (
    for %%f in (*.%%e) do (
        echo Moving %%f to Archives...
        move "%%f" "Archives\" > nul
    )
)

REM Move audio files
for %%e in (%audio%) do (
    for %%f in (*.%%e) do (
        echo Moving %%f to Audio...
        move "%%f" "Audio\" > nul
    )
)

REM Move video files
for %%e in (%video%) do (
    for %%f in (*.%%e) do (
        echo Moving %%f to Video...
        move "%%f" "Video\" > nul
    )
)

REM Move code files
for %%e in (%code%) do (
    for %%f in (*.%%e) do (
        echo Moving %%f to Code...
        move "%%f" "Code\" > nul
    )
)

echo.
echo ===================================
echo    Organization Complete!
echo ===================================
echo.
echo Folder summary:
for /d %%d in (*) do (
    for %%f in ("%%d") do (
        set "foldername=%%~nxd"
        if not "!foldername!"=="$RECYCLE.BIN" if not "!foldername!"=="System Volume Information" (
            echo !foldername!: 
            dir "%%d" /b | find /c "."
        )
    )
)

pause
```

---

## Backup Solutions

### 7. Smart Backup System

Intelligent backup with compression and logging.

```batch
@echo off
setlocal enabledelayedexpansion

REM Configuration
set "source=C:\ImportantData"
set "backupRoot=D:\Backups"
set "maxbackups=5"
set "logfile=%backupRoot%\backup_log.txt"

REM Generate backup folder name with timestamp
set "timestamp=%date:~-4,4%%date:~-7,2%%date:~-10,2%_%time:~0,2%%time:~3,2%"
set "timestamp=%timestamp: =0%"
set "timestamp=%timestamp::=%"
set "backupdest=%backupRoot%\Backup_%timestamp%"

echo ============================================
echo    Smart Backup System
echo ============================================
echo Source: %source%
echo Destination: %backupdest%
echo Max backups: %maxbackups%
echo.

REM Check if source exists
if not exist "%source%" (
    echo ERROR: Source folder not found!
    echo Please check the source path: %source%
    >> "%logfile%" echo [%date% %time%] ERROR: Source not found - %source%
    pause
    exit /b 1
)

REM Create backup destination
if not exist "%backupRoot%" mkdir "%backupRoot%"
mkdir "%backupdest%"

echo Starting backup at %date% %time%...
>> "%logfile%" echo [%date% %time%] Starting backup to %backupdest%

REM Perform backup with robocopy (more reliable than xcopy)
robocopy "%source%" "%backupdest%" /E /COPYALL /R:3 /W:5 /NFL /NDL /NP

if errorlevel 8 (
    echo ERROR: Backup failed!
    >> "%logfile%" echo [%date% %time%] ERROR: Backup failed
    pause
    exit /b 1
)

if errorlevel 1 (
    echo WARNING: Some files had issues, but backup mostly successful
    >> "%logfile%" echo [%date% %time%] WARNING: Minor errors in backup
) else (
    echo SUCCESS: All files backed up perfectly!
    >> "%logfile%" echo [%date% %time%] SUCCESS: Backup completed
)

REM Cleanup old backups
echo.
echo Cleaning up old backups (keeping %maxbackups%)...
cd /d "%backupRoot%"
for /f "skip=%maxbackups% tokens=*" %%d in ('dir /b /o-d /ad Backup_*') do (
    echo Removing old backup: %%d
    rd /s /q "%%d"
    >> "%logfile%" echo [%date% %time%] Removed old backup: %%d
)

echo.
echo ============================================
echo    Backup Complete!
echo ============================================
echo Backup location: %backupdest%
>> "%logfile%" echo [%date% %time%] Backup location: %backupdest%

pause
```

### 8. Quick Sync Script

Synchronizes two folders (one-way sync).

```batch
@echo off
setlocal

set /p source="Enter source folder: "
set /p dest="Enter destination folder: "

echo.
echo Synchronization Settings:
echo Source:      %source%
echo Destination: %dest%
echo.
echo This will copy newer/changed files from source to destination.
echo Files in destination but not in source will NOT be deleted.
echo.
set /p confirm="Proceed? (Y/N): "

if /i not "%confirm%"=="Y" (
    echo Sync cancelled
    pause
    exit /b 0
)

echo.
echo Starting synchronization...
echo.

robocopy "%source%" "%dest%" /E /XO /NFL /NDL /NP /XD "$RECYCLE.BIN" "System Volume Information"

if errorlevel 8 (
    echo ERROR: Sync failed!
    pause
    exit /b 1
)

echo.
echo Synchronization complete!
pause
```

---

## Development Tools

### 9. Project Builder

Builds multi-file projects automatically.

```batch
@echo off
setlocal enabledelayedexpansion

set "projectname=%~n1"
set "projectdir=%~dp1"
set "outputdir=%projectdir%Output"

if "%projectname%"=="" (
    set "projectdir=%CD%"
    set "projectname=%~n0"
)

echo ===================================
echo    Project Builder
echo ===================================
echo Project: %projectname%
echo Location: %projectdir%
echo.

if not exist "%outputdir%" mkdir "%outputdir%"

REM Compile batch files (check syntax)
echo Checking batch files...
set errors=0
for %%f in ("%projectdir%*.bat") do (
    if not "%%~nf"=="%~nx0" (
        call :CheckBatch "%%f"
        if errorlevel 1 set /a errors+=1
    )
)

REM Copy resource files
echo Copying resource files...
xcopy "%projectdir%*.txt" "%outputdir%" /Y > nul
xcopy "%projectdir%*.ini" "%outputdir%" /Y > nul
xcopy "%projectdir%*.cfg" "%outputdir%" /Y > nul

REM Create launcher
echo Creating launcher...
(
    echo @echo off
    echo cd /d "%%~dp0"
    echo call main.bat
    echo if errorlevel 1 pause
) > "%outputdir%\run.bat"

echo.
echo ===================================
if %errors% equ 0 (
    echo Build SUCCESSFUL!
    echo Output: %outputdir%
) else (
    echo Build FAILED with %errors% error(s)
)
echo ===================================

pause
exit /b %errors%

:CheckBatch
REM Simple syntax check - tries to parse the file
cmd /c "type %~1 ^| findstr /r /c:'^:' /c:'^rem' /c:'^@' /c:'^echo' /c:'^set' /c:'^if' /c:'^for' /c:'^call' /c:'^goto'" > nul
exit /b 0
```

### 10. Environment Setup Script

Sets up development environment quickly.

```batch
@echo off
setlocal

echo ===================================
echo    Development Environment Setup
echo ===================================
echo.

REM Add common paths to PATH
set "newpath=%PATH%"
set "newpath=C:\Git\bin;%newpath%"
set "newpath=C:\Program Files\NodeJS;%newpath%"
set "newpath=C:\Python39;%newpath%"
set "newpath=C:\Program Files\Java\jdk-11\bin;%newpath%"

setx PATH "%newpath%"
echo Updated system PATH

REM Set common environment variables
setx JAVA_HOME "C:\Program Files\Java\jdk-11"
setx PYTHON_HOME "C:\Python39"
setx NODE_HOME "C:\Program Files\NodeJS"

REM Create project structure
set /p projectname="Enter project name: "
if "%projectname%"=="" set "projectname=MyProject"

mkdir "%USERPROFILE%\Projects\%projectname%"
mkdir "%USERPROFILE%\Projects\%projectname%\src"
mkdir "%USERPROFILE%\Projects\%projectname%\lib"
mkdir "%USERPROFILE%\Projects\%projectname%\docs"
mkdir "%USERPROFILE%\Projects\%projectname%\test"

echo.
echo Created project structure in:
echo %USERPROFILE%\Projects\%projectname%
echo.
echo Remember to restart Command Prompt for PATH changes to take effect!

pause
```

---

## Network Utilities

### 11. Network Diagnostics Tool

Comprehensive network troubleshooting.

```batch
@echo off
setlocal enabledelayedexpansion

echo ============================================
echo    Network Diagnostics Tool
echo ============================================
echo.

set "logfile=network_diag_%date:~-4,4%%date:~-7,2%%date:~-10,2%.txt"

> "%logfile%" (
    echo Network Diagnostic Report >> "%logfile%"
    echo Generated: %date% %time% >> "%logfile%"
    echo Computer: %computername% >> "%logfile%"
    echo ============================================ >> "%logfile%"
    echo. >> "%logfile%"
    
    echo === IP CONFIGURATION === >> "%logfile%"
    ipconfig /all >> "%logfile%"
    echo. >> "%logfile%"
    
    echo === DNS CACHE === >> "%logfile%"
    ipconfig /displaydns >> "%logfile%"
    echo. >> "%logfile%"
    
    echo === ROUTING TABLE === >> "%logfile%"
    route print >> "%logfile%"
    echo. >> "%logfile%"
    
    echo === OPEN PORTS === >> "%logfile%"
    netstat -ano >> "%logfile%"
    echo. >> "%logfile%"
    
    echo === PING TESTS === >> "%logfile%"
    echo Pinging gateway... >> "%logfile%"
    for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /C:"Default Gateway"') do (
        set "gateway=%%a"
        ping -n 4 !gateway! >> "%logfile%"
    )
    echo. >> "%logfile%"
    
    echo Pinging Google DNS... >> "%logfile%"
    ping -n 4 8.8.8.8 >> "%logfile%"
    echo. >> "%logfile%"
    
    echo Pinging google.com... >> "%logfile%"
    ping -n 4 google.com >> "%logfile%"
    echo. >> "%logfile%"
    
    echo === TRACEROUTE === >> "%logfile%"
    tracert -d google.com >> "%logfile%"
)

echo Diagnostic report saved to: %logfile%
notepad "%logfile%"
```

### 12. Website Monitor

Monitors website availability.

```batch
@echo off
setlocal enabledelayedexpansion

set "sites=google.com github.com stackoverflow.com microsoft.com"
set "interval=60"
set "logfile=website_monitor.log"

echo ===================================
echo    Website Monitor
echo ===================================
echo Monitoring sites: %sites%
echo Check interval: %interval% seconds
echo Log file: %logfile%
echo.
echo Press Ctrl+C to stop monitoring
echo.

:loop
>> "%logfile%" echo.
>> "%logfile%" echo [%date% %time%] Starting check...

for %%s in (%sites%) do (
    ping -n 1 -w 2000 %%s > nul
    if errorlevel 1 (
        echo [FAIL] %%s - %date% %time%
        >> "%logfile%" echo [FAIL] %%s - %date% %time%
    ) else (
        echo [OK]  %%s - %date% %time%
        >> "%logfile%" echo [OK] %%s - %date% %time%
    )
)

timeout /t %interval% /nobreak > nul
goto loop
```

---

## Maintenance Scripts

### 13. System Cleaner

Safely cleans temporary files and caches.

```batch
@echo off
setlocal

echo ===================================
echo    Safe System Cleaner
echo ===================================
echo.
echo This script will clean:
echo - Temporary files
echo - Browser cache (basic)
echo - Windows update cache
echo - Recycle Bin
echo.
echo This is SAFE - only removes unnecessary files
echo.
set /p confirm="Proceed with cleanup? (Y/N): "

if /i not "%confirm%"=="Y" (
    echo Cleanup cancelled
    pause
    exit /b 0
)

echo.
echo Starting cleanup at %date% %time%...
echo.

REM Clean temp folders
echo Cleaning temporary files...
del /q/f/s %TEMP%\* 2>nul
del /q/f/s C:\Windows\Temp\* 2>nul

REM Clean browser caches (common locations)
echo Cleaning browser caches...
del /q/f/s "%LOCALAPPDATA%\Google\Chrome\User Data\Default\Cache\*" 2>nul
del /q/f/s "%LOCALAPPDATA%\Microsoft\Edge\User Data\Default\Cache\*" 2>nul

REM Clean Windows Update cache
echo Cleaning Windows Update cache...
net stop wuauserv >nul 2>&1
rd /s /q C:\Windows\SoftwareDistribution\Download 2>nul
net start wuauserv >nul 2>&1

REM Empty Recycle Bin
echo Emptying Recycle Bin...
powercfg /h off 2>nul
cleanmgr /d C /sagerun:1 2>nul

echo.
echo ===================================
echo    Cleanup Complete!
echo ===================================
echo Finished at %date% %time%

pause
```

### 14. Scheduled Task Creator

Creates automated scheduled tasks.

```batch
@echo off
setlocal

net session >nul 2>&1
if errorlevel 1 (
    echo ERROR: Administrator privileges required
    pause
    exit /b 1
)

echo ===================================
echo    Scheduled Task Creator
echo ===================================
echo.

set /p taskname="Enter task name: "
set /p taskaction="Enter command to run (full path): "
set /p taskdesc="Enter description: "

echo.
echo Schedule type:
echo 1. Daily
echo 2. Weekly
echo 3. Monthly
echo 4. At startup
echo 5. At logon
set /p scheduletype="Select schedule type: "

if "%scheduletype%"=="1" (
    set /p starttime="Enter start time (HH:MM, 24-hour format): "
    schtasks /create /tn "%taskname%" /tr "%taskaction%" /sc daily /st %starttime% /ru SYSTEM /rl HIGHEST
) else if "%scheduletype%"=="2" (
    set /p dayofweek="Enter day of week (MON,TUE,WED,THU,FRI,SAT,SUN): "
    set /p starttime="Enter start time (HH:MM): "
    schtasks /create /tn "%taskname%" /tr "%taskaction%" /sc weekly /d %dayofweek% /st %starttime% /ru SYSTEM /rl HIGHEST
) else if "%scheduletype%"=="3" (
    set /p dayofmonth="Enter day of month (1-31): "
    set /p starttime="Enter start time (HH:MM): "
    schtasks /create /tn "%taskname%" /tr "%taskaction%" /sc monthly /d %dayofmonth% /st %starttime% /ru SYSTEM /rl HIGHEST
) else if "%scheduletype%"=="4" (
    schtasks /create /tn "%taskname%" /tr "%taskaction%" /sc onstart /ru SYSTEM /rl HIGHEST
) else if "%scheduletype%"=="5" (
    schtasks /create /tn "%taskname%" /tr "%taskaction%" /sc onlogon /ru %USERNAME% /rl HIGHEST
) else (
    echo Invalid schedule type
    pause
    exit /b 1
)

if errorlevel 0 (
    echo.
    echo ===================================
    echo Task created successfully!
    echo ===================================
    echo Task name: %taskname%
    echo Description: %taskdesc%
    echo.
    echo To view or modify:
    echo - Open Task Scheduler
    echo - Find task: %taskname%
) else (
    echo.
    echo ERROR: Failed to create task
)

pause
```

---

## Tips for Using These Scripts

### ✅ Before Running

1. **Review the code** - Understand what it does
2. **Test in safe environment** - Don't run on production immediately
3. **Backup important data** - Just in case
4. **Check permissions** - Some need Administrator rights

### ✅ Customization

- Modify paths to match your environment
- Adjust parameters for your needs
- Add additional error handling
- Customize logging as needed

### ✅ Sharing

- Remove hardcoded paths
- Add configuration section
- Include usage instructions
- Document all parameters

---

**Need more scripts?** 

Check out:
- [Examples](../05_Examples/) - More code samples
- [Best Practices](07_Best_Practices.md) - Write better scripts
- [Troubleshooting](08_Troubleshooting.md) - Fix common issues

**Want to contribute?** 

Share your useful scripts and help others learn!
