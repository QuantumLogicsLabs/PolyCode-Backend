# Special Variables Cheat Sheet

## Quick Reference for All Special Variables

Print this page or keep it open while scripting!

---

## Parameter Variables

| Variable | Description | Example |
|----------|-------------|---------|
| `%0` | Batch file name | `myscript.bat` |
| `%1` to `%9` | Command line arguments | `John`, `Doe` |
| `%*` | All command line parameters | `John Doe 25` |
| `%~1` | Parameter without quotes | `%~1` expands `%1` |
| `%~f1` | Fully qualified path | `C:\folder\file.txt` |
| `%~d1` | Drive letter only | `C:` |
| `%~p1` | Path only | `\folder\` |
| `%~n1` | Filename only | `file` |
| `%~x1` | Extension only | `.txt` |

---

## System Information

| Variable | Returns | Example Value |
|----------|---------|---------------|
| `%CD%` | Current directory | `C:\Users\John` |
| `%DATE%` | Current date | `Mon 01/15/2024` |
| `%TIME%` | Current time | `14:30:45.67` |
| `%RANDOM%` | Random number (0-32767) | `12345` |
| `%ERRORLEVEL%` | Last error code | `0` = success |
| `%CMDEXTVERSION%` | Command extensions version | `1` |
| `%CMDCMDLINE%` | Original command line | Full command used |

---

## User and Computer

| Variable | Returns | Example |
|----------|---------|---------|
| `%USERNAME%` | Current user login | `John` |
| `%USERPROFILE%` | User's home directory | `C:\Users\John` |
| `%HOMEDRIVE%` | Home drive letter | `C:` |
| `%HOMEPATH%` | Home path | `\Users\John` |
| `%COMPUTERNAME%` | Computer/network name | `DESKTOP-ABC` |

---

## Application Data

| Variable | Returns | Typical Path |
|----------|---------|--------------|
| `%APPDATA%` | Roaming app data | `C:\Users\John\AppData\Roaming` |
| `%LOCALAPPDATA%` | Local app data | `C:\Users\John\AppData\Local` |
| `%PROGRAMDATA%` | Common app data | `C:\ProgramData` |
| `%TEMP%` / `%TMP%` | Temporary files | `C:\Users\John\AppData\Local\Temp` |
| `%PROGRAMFILES%` | Program files (64-bit) | `C:\Program Files` |
| `%PROGRAMFILES(X86)%` | Program files (32-bit) | `C:\Program Files (x86)` |

---

## System Variables

| Variable | Returns | Example |
|----------|---------|---------|
| `%SYSTEMROOT%` | Windows directory | `C:\Windows` |
| `%SYSTEMDRIVE%` | System drive | `C:` |
| `%WINDIR%` | Windows directory | `C:\Windows` |
| `%PATH%` | Executable search paths | `C:\Windows;C:\Windows\System32;...` |
| `%PATHEXT%` | Executable extensions | `.COM;.EXE;.BAT;.CMD;...` |
| `%OS%` | Operating system | `Windows_NT` |
| `%PROCESSOR_ARCHITECTURE%` | CPU architecture | `AMD64` |

---

## Date Formatting Tricks

### Extract Components
```batch
%date:~-4,4%    = Year (2024)
%date:~-7,2%    = Month (01)
%date:~-10,2%   = Day (15)
```

### Create ISO Format
```batch
set "iso=%date:~-4,4%-%date:~-7,2%-%date:~-10,2%"
REM Result: 2024-01-15
```

### Create Filename-Safe Date
```batch
set "safedate=%date:~-4,4%%date:~-7,2%%date:~-10,2%"
REM Result: 20240115
```

---

## Time Formatting Tricks

### Extract Components
```batch
%time:~0,2%     = Hour (14)
%time:~3,2%     = Minute (30)
%time:~6,2%     = Second (45)
```

### Handle Single Digit Hours
```batch
set "hour=%time:~0,2%"
set "hour=%hour: =0%"
REM Converts " 9" to "09"
```

### Create Timestamp
```batch
set "timestamp=%date:~-4,4%%date:~-7,2%%date:~-10,2%_%time:~0,2%%time:~3,2%"
set "timestamp=%timestamp: =0%"
REM Result: 20240115_1430
```

---

## Random Number Formulas

### Basic Ranges
```batch
%RANDOM% %% 10 + 1      = 1 to 10
%RANDOM% %% 100         = 0 to 99
%RANDOM% %% 50 + 50     = 50 to 99
%RANDOM% %% 6 + 1       = Dice roll (1-6)
%RANDOM% %% 2           = Coin flip (0-1)
```

### Advanced Examples
```batch
REM Percentage (0-100)
set /a percent=%RANDOM% %% 101

REM Temperature (-10 to 40)
set /a temp=%RANDOM% %% 51 - 10

REM Card suit (1-4)
set /a suit=%RANDOM% %% 4 + 1
```

---

## Error Level Values

| Value | Meaning |
|-------|---------|
| `0` | Success |
| `1` | General error |
| `2` | File not found |
| `3` | Path not found |
| `5` | Access denied |
| `9009` | Command not recognized |

### Check Error Levels
```batch
if %ERRORLEVEL% equ 0 echo Success
if %ERRORLEVEL% neq 0 echo Failed
if %ERRORLEVEL% gtr 5 echo Serious error
```

---

## Quick Code Snippets

### Display All Variables
```batch
@echo off
echo CD: %CD%
echo DATE: %DATE%
echo TIME: %TIME%
echo USER: %USERNAME%
echo PC: %COMPUTERNAME%
pause
```

### Create Backup Folder
```batch
set "backup=%HOMEDRIVE%%HOMEPATH%\Backup\%date:~-4,4%%date:~-7,2%%date:~-10,2%"
if not exist "%backup%" mkdir "%backup%"
```

### Log with Timestamp
```batch
echo [%TIME%] Operation completed >> logfile.txt
```

### Generate Unique ID
```batch
set "uniqueid=%RANDOM%%RANDOM%%TIME:~6,2%"
```

---

## Pro Tips

✅ **Always quote paths** with spaces: `"%HOMEDRIVE%%HOMEPATH%"`  
✅ **Use delayed expansion** (`!var!`) inside loops  
✅ **Clean temp variables** with `setlocal`/`endlocal`  
✅ **Validate ERRORLEVEL** after critical operations  
✅ **Use meaningful variable names** for clarity  

❌ **Don't use** `%` inside loops (use `!` instead)  
❌ **Don't forget** to handle spaces in paths  
❌ **Don't ignore** error levels  
❌ **Avoid** special characters in variable names  

---

## Memory Aids

**DATE Parts**: Think backwards!
- Last 4 = Year (`~-4,4`)
- 7 from end = Month (`~-7,2`)  
- 10 from end = Day (`~-10,2`)

**TIME Parts**: Count forward!
- Start 0, length 2 = Hour (`~0,2`)
- Start 3, length 2 = Minute (`~3,2`)
- Start 6, length 2 = Second (`~6,2`)

**RANDOM Range**: Formula is always:
```
%RANDOM% %% (max-min+1) + min
```

---

Keep this cheat sheet handy for quick reference while scripting! 📚
