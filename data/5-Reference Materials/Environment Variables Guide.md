# Environment Variables Complete Guide

## Understanding Windows Environment Variables

Environment variables are system-wide or user-specific settings that store configuration information.

---

## What are Environment Variables?

Environment variables tell Windows and programs important information like:
- Where to find executable files (`%PATH%`)
- Where to store temporary files (`%TEMP%`)
- User-specific folders (`%APPDATA%`)
- System configuration

---

## Types of Environment Variables

### 1. System Variables
Apply to all users on the computer.

**Examples:**
- `%SYSTEMROOT%` = `C:\Windows`
- `%COMPUTERNAME%` = Your PC's name
- `%NUMBER_OF_PROCESSORS%` = CPU count

### 2. User Variables
Specific to your user account.

**Examples:**
- `%USERNAME%` = Your login name
- `%USERPROFILE%` = Your home folder
- `%TEMP%` = Your temp folder

### 3. Volatile Variables
Exist only during script execution.

```batch
setlocal
set "tempvar=temporary"
endlocal
REM Variable automatically deleted
```

---

## Viewing Environment Variables

### Method 1: SET Command
```batch
@echo off
echo === All Environment Variables ===
set
pause
```

### Method 2: Specific Variable
```batch
@echo off
echo PATH is: %PATH%
pause
```

### Method 3: SET with Filter
```batch
@echo off
echo === Variables containing "APP" ===
set APP
pause
```

### Method 4: GUI Interface
```batch
@echo off
SystemPropertiesAdvanced.exe
pause
```

---

## Common Environment Variables

### File System Paths
```batch
%HOMEDRIVE%           = C:
%HOMEPATH%            = \Users\John
%USERPROFILE%         = C:\Users\John
%PUBLIC%              = C:\Users\Public
%PROGRAMFILES%        = C:\Program Files
%PROGRAMFILES(X86)%   = C:\Program Files (x86)
```

### Application Folders
```batch
%APPDATA%             = C:\Users\John\AppData\Roaming
%LOCALAPPDATA%        = C:\Users\John\AppData\Local
%PROGRAMDATA%         = C:\ProgramData
%COMMONPROGRAMFILES%  = C:\Program Files\Common Files
```

### System Configuration
```batch
%SYSTEMROOT%          = C:\Windows
%WINDIR%              = C:\Windows
%SYSTEMDRIVE%         = C:
%TEMP% / %TMP%        = Temporary files folder
```

### Execution Settings
```batch
%PATH%                = Search paths for executables
%PATHEXT%             = Executable file extensions
%COMSPEC%             = Path to cmd.exe
```

---

## Modifying Environment Variables

### Temporarily (Script Only)
```batch
@echo off
REM Set variable for this script only
set "MYVAR=Hello"
echo %MYVAR%
pause
```

### Using SETX (Permanent)
```batch
@echo off
REM Set user variable
setx MYVAR "Hello World"

REM Set path variable (append)
setx PATH "%PATH%;C:\MyFolder"

echo Changes will apply to new command windows
pause
```

**⚠️ Warning:** SETX changes are permanent!

### Administrative SETX
```batch
@echo off
REM Set system variable (requires admin)
setx MYVAR "System Value" /M
pause
```

---

## Working with PATH Variable

### View Current PATH
```batch
@echo off
echo Current PATH:
echo %PATH%
pause
```

### Add to PATH Temporarily
```batch
@echo off
set "PATH=%PATH%;C:\MyTools"
echo New PATH: %PATH%
pause
```

### Add to PATH Permanently
```batch
@echo off
setx PATH "%PATH%;C:\MyTools"
echo PATH updated! Open new window to use.
pause
```

### Clean PATH
```batch
@echo off
REM Remove duplicate semicolons
set "cleanpath=%PATH:;;=;%"
echo Cleaned PATH: %cleanpath%
pause
```

---

## Practical Examples

### 1. Check Required Software
```batch
@echo off
if defined JAVA_HOME (
    echo Java is installed: %JAVA_HOME%
) else (
    echo ERROR: Java not found!
)

if defined PYTHON (
    echo Python is installed
) else (
    echo Python not found
)
pause
```

### 2. Setup Development Environment
```batch
@echo off
setlocal

REM Add tools to PATH for this session
set "PATH=%PATH%;C:\Dev\Java\bin"
set "PATH=%PATH%;C:\Dev\Python"
set "PATH=%PATH%;C:\Dev\NodeJS"

echo Development environment ready!
echo Updated PATH: %PATH%
pause
```

### 3. Backup Environment Variables
```batch
@echo off
set "backupfile=env_backup_%date:~-4,4%%date:~-7,2%%date:~-10,2%.txt"
set > "%backupfile%"
echo Environment saved to: %backupfile%
pause
```

### 4. Restore from Backup
```batch
@echo off
set /p backupfile=Enter backup file: 
for /f "tokens=1,* delims==" %%a in (%backupfile%) do (
    set "%%a=%%b"
)
echo Environment restored!
pause
```

---

## Best Practices

### ✅ DO:
- Use `setlocal`/`endlocal` to contain changes
- Quote paths with spaces: `"%PROGRAMFILES%"`
- Test with temporary variables first
- Document custom variables you create

### ❌ DON'T:
- Modify system PATH without backup
- Use SETX in scripts run multiple times
- Create variables with special characters
- Overwrite important system variables

---

## Troubleshooting

### Variable Not Expanding
**Problem:** `%MYVAR%` shows as literal text

**Solution:** Check spelling and scope
```batch
set "MYVAR=test"
echo %MYVAR%    REM Should show: test
```

### PATH Changes Not Taking Effect
**Problem:** New PATH doesn't work

**Solution:** Open new command window
```batch
@echo off
setx TEST "value"
echo Close and reopen command prompt
pause
```

### Variable Lost After Script
**Problem:** Variable disappears after script ends

**Solution:** This is normal! Use SETX for permanence
```batch
setlocal
set "temp=temporary"    REM Gone after script
endlocal
```

---

## Quick Reference Table

| Variable | Purpose | Example |
|----------|---------|---------|
| `%PATH%` | Executable locations | `C:\Windows;C:\Tools` |
| `%TEMP%` | Temporary files | For installers, cache |
| `%APPDATA%` | App settings | Store configs |
| `%USERPROFILE%` | User folder | Navigate to user files |
| `%PROGRAMFILES%` | Installed programs | Find applications |

---

## Practice Exercises

1. List all variables containing "DIR"
2. Add a custom folder to your PATH
3. Create a script that checks if Python is installed
4. Backup all your environment variables
