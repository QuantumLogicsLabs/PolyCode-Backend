# 10 — Advanced Topics

> **Part of:** Batch File Language Documentation Series  
> **Back to:** `00_Overview.md`

---

## Overview

This section covers advanced batch techniques: delayed expansion, special character handling, environment manipulation, working with the registry, calling PowerShell, and performance tips.

---

## Delayed Variable Expansion

### The Problem

Inside `IF` and `FOR` blocks, batch **parses the entire block first** and expands `%variable%` references at parse time — before any commands execute. This means changes to variables inside the block are invisible within the same block:

```bat
SET count=0
FOR %%i IN (a b c) DO (
    SET /A count += 1
    ECHO %count%    REM Always prints 0!
)
```

All three lines print `0` because `%count%` was expanded to `0` when the block was parsed.

### The Solution — Delayed Expansion

Enable delayed expansion with `SETLOCAL ENABLEDELAYEDEXPANSION` and use `!variable!` instead of `%variable%`:

```bat
SETLOCAL ENABLEDELAYEDEXPANSION
SET count=0
FOR %%i IN (a b c) DO (
    SET /A count += 1
    ECHO !count!    REM Prints 1, 2, 3 correctly
)
ENDLOCAL
```

### Enabling globally

```bat
SETLOCAL ENABLEDELAYEDEXPANSION
REM All code here can use !var!
ENDLOCAL
```

Or via command line:

```bat
CMD /V:ON /C myscript.bat
```

### When `!` conflicts

If your string contains a `!` character (e.g., `Hello!`), it will be interpreted as a delayed expansion delimiter. Escape it with `^`:

```bat
SET "str=Hello^!"
ECHO !str!     REM Output: Hello!
```

---

## Special Characters Reference

| Character | Meaning | How to Use Literally |
|---|---|---|
| `%` | Variable delimiter | `%%` (in scripts) |
| `^` | Escape character | `^^` |
| `&` | Command separator | `^&` |
| `\|` | Pipe | `^\|` |
| `<` | Input redirect | `^<` |
| `>` | Output redirect | `^>` |
| `(` | Block open | `^(` |
| `)` | Block close | `^)` |
| `!` | Delayed expansion | `^!` (with delayed expansion on) |
| `"` | Quoting | `\"` (context-dependent) |
| `;` | Separates values in some contexts | Treat as regular character |

---

## The `^` Escape Character

`^` escapes the next character, preventing special interpretation:

```bat
ECHO Hello ^& World     REM Prints: Hello & World
ECHO 2 ^> 1             REM Prints: 2 > 1
ECHO foo ^| bar         REM Prints: foo | bar
```

Inside quoted strings, `^` is **not** an escape — use it freely:

```bat
ECHO "Hello & World"    REM Works fine without ^
```

---

## The `%~` Variable Expansion Modifiers

For script arguments (`%1`–`%9`) and FOR loop variables (`%%F`):

| Modifier | Description |
|---|---|
| `%~1` | Remove surrounding quotes |
| `%~f1` | Full absolute path |
| `%~d1` | Drive letter only (e.g., `C:`) |
| `%~p1` | Path only (e.g., `\dir\`) |
| `%~n1` | Filename without extension |
| `%~x1` | Extension only (e.g., `.txt`) |
| `%~nx1` | Filename with extension |
| `%~dp1` | Drive + path (parent directory) |
| `%~z1` | File size in bytes |
| `%~t1` | File date/time stamp |
| `%~a1` | File attributes |
| `%~$PATH:1` | Search PATH for the file |

---

## Getting the Script's Own Directory

Reliably locate the script's directory regardless of where it's called from:

```bat
SET "script_dir=%~dp0"
ECHO Script is in: %script_dir%
```

This is essential for scripts that reference files relative to their own location:

```bat
CALL "%~dp0lib\utils.bat"
SET "config=%~dp0config\settings.ini"
```

---

## Environment Manipulation

### Exporting variables to the parent shell

Variables set inside a script normally persist in the parent CMD if `SETLOCAL` is not used. To explicitly set a variable in the parent environment from a script, use `ENDLOCAL & SET`:

```bat
SETLOCAL
SET "result=computed_value"
ENDLOCAL & SET "result=%result%"
```

### Checking if running with Admin privileges

```bat
NET SESSION > NUL 2>&1
IF %ERRORLEVEL% NEQ 0 (
    ECHO This script requires administrator privileges.
    EXIT /B 1
)
```

### Self-elevating script

```bat
@ECHO OFF
NET SESSION > NUL 2>&1
IF %ERRORLEVEL% NEQ 0 (
    ECHO Requesting administrator privileges...
    PowerShell -Command "Start-Process -FilePath '%~f0' -Verb RunAs"
    EXIT /B
)
REM Elevated code runs here
```

---

## Calling PowerShell from Batch

Batch can delegate complex tasks to PowerShell inline:

```bat
REM Run a PowerShell command
PowerShell -Command "Get-Date -Format 'yyyy-MM-dd'"

REM Capture output
FOR /F "tokens=*" %%i IN ('PowerShell -Command "Get-Date -Format 'yyyyMMdd'"') DO SET "today=%%i"
ECHO Today: %today%

REM Run a multi-line script
PowerShell -Command "& { $a = 1; $b = 2; Write-Host ($a + $b) }"

REM Run a .ps1 file
PowerShell -ExecutionPolicy Bypass -File "script.ps1"
```

---

## Registry Operations

### Read a registry value

```bat
FOR /F "tokens=2,* skip=2" %%a IN (
    'REG QUERY "HKCU\Software\MyApp" /v "Setting" 2^>NUL'
) DO SET "value=%%b"
ECHO Value: %value%
```

### Write a registry value

```bat
REG ADD "HKCU\Software\MyApp" /v "Setting" /t REG_SZ /d "MyValue" /f
```

### Delete a registry value

```bat
REG DELETE "HKCU\Software\MyApp" /v "Setting" /f
```

### Check if a registry key exists

```bat
REG QUERY "HKCU\Software\MyApp" > NUL 2>&1
IF %ERRORLEVEL% EQU 0 ECHO Key exists
```

---

## Working with INI-Style Config Files

Read a value from a simple `key=value` config file:

```bat
FOR /F "tokens=1,* delims==" %%a IN (config.ini) DO (
    IF "%%a"=="log_level" SET "log_level=%%b"
    IF "%%a"=="output_dir" SET "output_dir=%%b"
)
ECHO Log level: %log_level%
```

---

## Multi-Command Lines with `&`

Run multiple commands on one line:

```bat
ECHO First & ECHO Second & ECHO Third
CD "C:\temp" & DIR & CD ..
```

---

## Conditional Chaining with `&&` and `||`

```bat
MKDIR newdir && ECHO Created. || ECHO Failed.
```

---

## Performance Tips

- Avoid echoing inside tight loops — `ECHO` is slow at scale.
- Use `> NUL` to suppress output you don't need.
- Prefer `ROBOCOPY` over `XCOPY` for large copy operations.
- Use `FINDSTR` instead of `FIND` for regex support.
- Minimize `SET /P` inside loops — it's slow.
- Declare `SETLOCAL ENABLEDELAYEDEXPANSION` only when needed.

---

## Common Pitfalls and Solutions

| Pitfall | Solution |
|---|---|
| Variable not updating in loop | Use delayed expansion `!var!` |
| Script fails when path has spaces | Always quote paths `"%path%"` |
| Trailing space in variable | Use `SET "var=value"` form |
| `ECHO` prints `ECHO is on/off` | Variable is empty; check first |
| `IF ERRORLEVEL 1` catches 2+ too | Use `IF %ERRORLEVEL% EQU 1` |
| External script doesn't return | Use `CALL` before the filename |
| `:` in label conflicts | Avoid special chars in label names |
| `%DATE%` format varies by locale | Parse carefully or use PowerShell |
| `>>` appends with extra blank line | This is a batch quirk; use `<NUL` workaround |

### Fix: ECHO with empty variable

```bat
IF "%var%"=="" (
    ECHO Variable is empty
) ELSE (
    ECHO %var%
)
```

### Fix: Append without trailing newline issues

```bat
<NUL SET /P ="Text without newline" >> file.txt
```

---

## Debugging Batch Scripts

Enable echoing to trace execution:

```bat
ECHO ON      REM Show each command before it runs
```

Or run from CMD with:

```bat
CMD /C "ECHO ON & myscript.bat"
```

Add debug output:

```bat
SET "DEBUG=1"

:debug
IF "%DEBUG%"=="1" ECHO [DEBUG] %~1
GOTO :EOF
```

```bat
CALL :debug "Entering backup routine"
```

### Pause at specific points

```bat
ECHO [DEBUG] Variable state:
SET                     REM Dump all variables
PAUSE
```

---

## Template — Production-Ready Script

```bat
@ECHO OFF
SETLOCAL ENABLEDELAYEDEXPANSION

REM ============================================================
REM Script:      template.bat
REM Description: Production-ready batch script template
REM Author:      Your Name
REM Version:     1.0.0
REM ============================================================

SET "SCRIPT_DIR=%~dp0"
SET "LOG_FILE=%SCRIPT_DIR%logs\%~n0.log"
SET "SCRIPT_VERSION=1.0.0"

REM --- Argument Validation ---
IF "%~1"=="" (
    ECHO Usage: %~nx0 ^<argument^>
    EXIT /B 1
)

REM --- Initialize ---
IF NOT EXIST "%SCRIPT_DIR%logs" MKDIR "%SCRIPT_DIR%logs"
CALL :log "INFO" "Script v%SCRIPT_VERSION% started"
CALL :log "INFO" "Argument: %~1"

REM --- Main Logic ---
CALL :main "%~1"
SET "exit_code=%ERRORLEVEL%"

CALL :log "INFO" "Script finished with code %exit_code%"
EXIT /B %exit_code%

REM ============================================================
:main
REM Parameters: %1 = main argument
SETLOCAL
SET "arg=%~1"
ECHO Processing: %arg%
ENDLOCAL
GOTO :EOF

REM ============================================================
:log
REM Parameters: %1 = level, %2 = message
SET "timestamp=%DATE% %TIME%"
ECHO [%timestamp%] [%~1] %~2
ECHO [%timestamp%] [%~1] %~2 >> "%LOG_FILE%"
GOTO :EOF
```

---

*End of Batch File Language Documentation Series.*  
*Back to: [00 — Overview](00_Overview.md)*
