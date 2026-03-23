# Environment Variables Examples

## Practical Examples with Environment Variables

Real-world scripts using environment variables effectively.

---

## Example 1: System Setup Script

```batch
@echo off
setlocal enabledelayedexpansion

echo ============================================
echo    System Setup and Configuration
echo ============================================
echo.

REM Display system information
echo Computer: %COMPUTERNAME%
echo User: %USERNAME%
echo Windows Directory: %SYSTEMROOT%
echo System Drive: %SYSTEMDRIVE%
echo.

REM Check available disk space
echo === Disk Space ===
wmic logicaldisk get caption,freesize,size | findstr /C:"C:"
echo.

REM Check memory
echo === Memory Information ===
wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value
echo.

REM Display PATH length
set "pathlength=%PATH%"
echo PATH variable has !pathlength:~0,5!... characters
echo.

pause
```

---

## Example 2: Application Launcher

```batch
@echo off
setlocal

REM Set up application environment
set "APP_NAME=MyApplication"
set "APP_VERSION=1.0"
set "APP_HOME=%PROGRAMFILES%\%APP_NAME%"

echo Starting %APP_NAME% v%APP_VERSION%
echo.

REM Add application to PATH temporarily
if exist "%APP_HOME%\bin" (
    set "PATH=%PATH%;%APP_HOME%\bin"
    echo Added %APP_HOME%\bin to PATH
)

REM Set application-specific variables
set "CONFIG_DIR=%APPDATA%\%APP_NAME%"
set "DATA_DIR=%LOCALAPPDATA%\%APP_NAME%"

REM Create config folder if needed
if not exist "%CONFIG_DIR%" (
    echo Creating configuration folder...
    mkdir "%CONFIG_DIR%"
)

REM Launch application
if exist "%APP_HOME%\app.exe" (
    echo Launching application...
    start "" "%APP_HOME%\app.exe"
) else (
    echo ERROR: Application not found at %APP_HOME%
)

pause
```

---

## Example 3: Development Environment Checker

```batch
@echo off
setlocal enabledelayedexpansion

echo ============================================
echo    Development Environment Check
echo ============================================
echo.

set "errors=0"

REM Check Java
if defined JAVA_HOME (
    echo [OK] Java is configured
    echo     JAVA_HOME: %JAVA_HOME%
) else (
    echo [MISSING] Java not configured
    set /a errors+=1
)

REM Check Python
if defined PYTHON (
    echo [OK] Python is configured: %PYTHON%
) else (
    echo [MISSING] Python not configured
    set /a errors+=1
)

REM Check Node.js
where node > nul 2>&1
if !ERRORLEVEL! equ 0 (
    echo [OK] Node.js is installed
) else (
    echo [MISSING] Node.js not found
    set /a errors+=1
)

REM Check Git
where git > nul 2>&1
if !ERRORLEVEL! equ 0 (
    echo [OK] Git is installed
) else (
    echo [MISSING] Git not found
    set /a errors+=1
)

REM Check Visual Studio
if defined VSINSTALLDIR (
    echo [OK] Visual Studio is installed
) else (
    echo [INFO] Visual Studio not detected
)

echo.
echo ============================================
if %errors% equ 0 (
    echo All development tools are configured!
) else (
    echo Found %errors% missing tool(s)
)
echo ============================================
pause
```

---

## Example 4: Portable App Configurator

```batch
@echo off
setlocal

REM Get script location
set "SCRIPT_DIR=%~dp0"

echo ============================================
echo    Portable Application Setup
echo ============================================
echo.

REM Create portable paths
set "PORTABLE_APP=%SCRIPT_DIR%App"
set "PORTABLE_DATA=%SCRIPT_DIR%Data"
set "PORTABLE_CONFIG=%SCRIPT_DIR%Config"

echo Setting up portable folders...
echo.

REM Create folders
if not exist "%PORTABLE_APP%" (
    mkdir "%PORTABLE_APP%"
    echo Created: App\
)

if not exist "%PORTABLE_DATA%" (
    mkdir "%PORTABLE_DATA%"
    echo Created: Data\
)

if not exist "%PORTABLE_CONFIG%" (
    mkdir "%PORTABLE_CONFIG%"
    echo Created: Config\
)

echo.
echo Creating launcher script...

REM Create launcher
(
echo @echo off
echo set "MYAPP_DATA=%PORTABLE_DATA%"
echo set "MYAPP_CONFIG=%PORTABLE_CONFIG%"
echo start "" "%PORTABLE_APP%\myapp.exe"
) > "launch.bat"

echo.
echo ============================================
echo Setup complete!
echo Run launch.bat to start the application
echo ============================================
pause
```

---

## Example 5: Backup with Environment Variables

```batch
@echo off
setlocal enabledelayedexpansion

echo ============================================
echo    Smart Backup Script
echo ============================================
echo.

REM Create backup location
set "BACKUP_ROOT=%HOMEDRIVE%\Backup"
set "BACKUP_DATE=%date:~-4,4%%date:~-7,2%%date:~-10,2%"
set "BACKUP_TIME=%time:~0,2%%time:~3,2%"
set "BACKUP_TIME=%BACKUP_TIME: =0%"
set "BACKUP_FOLDER=%BACKUP_ROOT%\%BACKUP_DATE%_%BACKUP_TIME%"

echo Backup destination: %BACKUP_FOLDER%
echo.

REM Create backup folder
if not exist "%BACKUP_FOLDER%" (
    mkdir "%BACKUP_FOLDER%"
    echo Backup folder created
)

echo.
echo Backing up important folders...
echo.

REM Backup Documents
if exist "%USERPROFILE%\Documents" (
    echo - Documents
    xcopy "%USERPROFILE%\Documents" "%BACKUP_FOLDER%\Documents" /E /I /S /Y > nul
)

REM Backup Desktop
if exist "%USERPROFILE%\Desktop" (
    echo - Desktop
    xcopy "%USERPROFILE%\Desktop" "%BACKUP_FOLDER%\Desktop" /E /I /S /Y > nul
)

REM Backup Pictures
if exist "%USERPROFILE%\Pictures" (
    echo - Pictures
    xcopy "%USERPROFILE%\Pictures" "%BACKUP_FOLDER%\Pictures" /E /I /S /Y > nul
)

REM Backup browser bookmarks (Chrome example)
if exist "%LOCALAPPDATA%\Google\Chrome\User Data\Default" (
    echo - Chrome Bookmarks
    xcopy "%LOCALAPPDATA%\Google\Chrome\User Data\Default\Bookmarks" "%BACKUP_FOLDER%\Browser" /Y > nul
)

echo.
echo ============================================
echo Backup Complete!
echo Location: %BACKUP_FOLDER%
echo Size: 
dir /s /b "%BACKUP_FOLDER%" | find /c /v ""
echo ============================================
pause
```

---

## Example 6: Clean Temporary Files

```batch
@echo off
setlocal

echo ============================================
echo    Temporary Files Cleaner
echo ============================================
echo.

echo Cleaning user temp folder: %TEMP%
echo.

REM Count files before cleanup
set "count=0"
for %%f in ("%TEMP%\*.*") do set /a count+=1
echo Found %count% files to clean...
echo.

REM Delete temp files
del /q /f "%TEMP%\*.*" 2>nul
for /d %%d in ("%TEMP%\*") do rd /q /s "%%d" 2>nul

echo.
echo ✓ Temp folder cleaned!
echo.

REM Clean recycle bin info
echo Recycle Bin on %SYSTEMDRIVE%:
dir /a "%SYSTEMDRIVE%\$Recycle.Bin" 2>nul || echo Access denied
echo.

echo ============================================
echo Cleanup Complete!
echo ============================================
pause
```

---

## Example 7: Network Drive Mapper

```batch
@echo off
setlocal

echo ============================================
echo    Network Drive Mapper
echo ============================================
echo.

REM Define network paths
set "SERVER_NAME=%COMPUTERNAME%"
set "SHARE_DOCS=\\%SERVER_NAME%\Docs"
set "SHARE_BACKUP=\\%SERVER_NAME%\Backup"

echo Server: %SERVER_NAME%
echo.

REM Map drives
echo Mapping network drives...

net use Z: "%SHARE_DOCS%" /persistent:yes 2>nul
if !ERRORLEVEL! equ 0 (
    echo ✓ Mapped Z: to Documents share
) else (
    echo ✗ Failed to map Z:
)

net use Y: "%SHARE_BACKUP%" /persistent:yes 2>nul
if !ERRORLEVEL! equ 0 (
    echo ✓ Mapped Y: to Backup share
) else (
    echo ✗ Failed to map Y:
)

echo.
echo Current network connections:
net use
echo.

pause
```

---

## Example 8: Software Installer Checker

```batch
@echo off
setlocal enabledelayedexpansion

echo ============================================
echo    Installed Software Reporter
echo ============================================
echo.

REM Check common installation folders
echo Checking installation folders...
echo.

if exist "%PROGRAMFILES%" (
    echo Programs in %PROGRAMFILES%:
    dir /b "%PROGRAMFILES%" | find /c /v ""
    echo.
)

if exist "%PROGRAMFILES(X86)%" (
    echo Programs in %PROGRAMFILES(X86)%:
    dir /b "%PROGRAMFILES(X86)%" | find /c /v ""
    echo.
)

REM Check desktop shortcuts
echo Desktop shortcuts:
dir /b "%USERPROFILE%\Desktop\*.lnk" 2>nul
echo.

REM Check start menu
echo Start Menu programs:
dir /b "%PROGRAMDATA%\Microsoft\Windows\Start Menu\Programs" 2>nul | find /c /v ""
echo.

pause
```

---

These examples show how environment variables make scripts flexible and portable! 🚀
