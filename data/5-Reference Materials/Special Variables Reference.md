# Special Variables Reference

## Complete Guide to Batch Special Variables

Special variables provide quick access to system information and script parameters.

---

## Parameter Variables

### `%0` - Script Name
```batch
@echo off
echo This script is named: %0
pause
```

### `%1` to `%9` - Command Line Arguments
```batch
@echo off
echo First parameter: %1
echo Second parameter: %2
echo Third parameter: %3
pause
```

**Usage:**
```
myscript.bat John Doe 25
```
**Output:**
```
First parameter: John
Second parameter: Doe
Third parameter: 25
```

### `%*` - All Parameters
```batch
@echo off
echo All parameters: %*
pause
```

---

## System Information Variables

### `%CD%` - Current Directory
```batch
@echo off
echo You are in: %CD%
pause
```

### `%DATE%` - Current Date
```batch
@echo off
echo Today is: %DATE%
echo Short date: %date:~-4,4%%date:~-7,2%%date:~-10,2%
pause
```

### `%TIME%` - Current Time
```batch
@echo off
echo Current time: %TIME%
pause
```

### `%RANDOM%` - Random Number
```batch
@echo off
echo Random number: %RANDOM%
set /a dice=%RANDOM% %% 6 + 1
echo Dice roll (1-6): %dice%
pause
```

---

## Environment Variables

### `%ERRORLEVEL%` - Last Error Code
```batch
@echo off
dir nonexistent.txt > nul 2>&1
echo Error level: %ERRORLEVEL%
pause
```

### `%USERNAME%` - Current User
```batch
@echo off
echo Hello, %USERNAME%!
pause
```

### `%COMPUTERNAME%` - Computer Name
```batch
@echo off
echo Running on: %COMPUTERNAME%
pause
```

### `%HOMEDRIVE%` and `%HOMEPATH%`
```batch
@echo off
echo Home directory: %HOMEDRIVE%%HOMEPATH%
pause
```

---

## Quick Reference Table

| Variable | Returns | Example |
|----------|---------|---------|
| `%0` | Script name | `myscript.bat` |
| `%1-%9` | Parameters | `John`, `Doe` |
| `%*` | All parameters | `John Doe 25` |
| `%CD%` | Current directory | `C:\Users\John` |
| `%DATE%` | Current date | `Mon 01/15/2024` |
| `%TIME%` | Current time | `14:30:45.67` |
| `%RANDOM%` | Random number | `12345` |
| `%ERRORLEVEL%` | Error code | `0` = success |
| `%USERNAME%` | User name | `John` |
| `%COMPUTERNAME%` | PC name | `DESKTOP-ABC123` |

---

## Practice Exercises

1. Create a script that displays all special variables
2. Make a greeting script that uses `%USERNAME%`
3. Create a random number generator for games
4. Build a script that shows current directory and date
