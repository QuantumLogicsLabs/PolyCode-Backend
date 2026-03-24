# 07 — Error Handling

> **Part of:** Batch File Language Documentation Series  
> **Back to:** `00_Overview.md`

---

## Overview

Error handling in batch is primitive compared to modern languages — there are no exceptions, try/catch blocks, or stack traces. Instead, batch relies on **exit codes** (ERRORLEVEL) and manual checks after each critical command.

---

## ERRORLEVEL

Every command that runs sets `ERRORLEVEL` to an integer:

- `0` — success
- Non-zero — some kind of failure (the specific meaning varies by command)

### Checking ERRORLEVEL

```bat
some_command
IF %ERRORLEVEL% NEQ 0 ECHO Command failed with error %ERRORLEVEL%
```

### Older syntax (still widely used)

```bat
some_command
IF ERRORLEVEL 1 ECHO An error occurred
```

> ⚠️ `IF ERRORLEVEL n` is true if ERRORLEVEL is **greater than or equal to** n. This is not the same as equality — check from highest to lowest when checking multiple levels.

```bat
IF ERRORLEVEL 3 ECHO Critical error
IF ERRORLEVEL 2 ECHO Moderate error
IF ERRORLEVEL 1 ECHO Minor error
```

---

## EXIT /B with Error Codes

Propagate errors to the caller using `EXIT /B`:

```bat
EXIT /B 0       REM Success
EXIT /B 1       REM General failure
EXIT /B 2       REM Custom meaning (e.g., "file not found")
```

Callers can check these:

```bat
CALL myscript.bat
IF %ERRORLEVEL% NEQ 0 (
    ECHO Script failed with code %ERRORLEVEL%
)
```

---

## Conditional Execution Operators

Batch provides two inline operators for conditional chaining:

### `&&` — Run next command only if previous succeeded (ERRORLEVEL = 0)

```bat
MKDIR "C:\logs" && ECHO Directory created.
```

### `||` — Run next command only if previous failed (ERRORLEVEL ≠ 0)

```bat
MKDIR "C:\logs" || ECHO Failed to create directory.
```

### Combining both

```bat
XCOPY source dest /E /Y && ECHO Copy OK || ECHO Copy FAILED
```

---

## Common Error Handling Patterns

### Pattern 1 — Check and abort

```bat
XCOPY "%src%" "%dst%" /E /Y
IF %ERRORLEVEL% NEQ 0 (
    ECHO ERROR: Copy failed. Aborting.
    EXIT /B 1
)
ECHO Copy succeeded.
```

### Pattern 2 — Log and continue

```bat
DEL "temp.tmp" 2> NUL
IF %ERRORLEVEL% NEQ 0 (
    ECHO WARNING: Could not delete temp.tmp
)
```

### Pattern 3 — Wrapper function

```bat
:runCommand
%*
IF %ERRORLEVEL% NEQ 0 (
    ECHO FAILED: %*
    EXIT /B %ERRORLEVEL%
)
GOTO :EOF
```

Usage:

```bat
CALL :runCommand XCOPY "%src%" "%dst%" /E /Y
CALL :runCommand MKDIR "C:\output"
```

---

## Handling Missing Files

```bat
IF NOT EXIST "config.ini" (
    ECHO ERROR: config.ini not found.
    EXIT /B 1
)
```

---

## Handling Missing Arguments

```bat
IF "%~1"=="" (
    ECHO Usage: %~nx0 source_folder destination_folder
    EXIT /B 1
)
```

`%~nx0` expands to the script's filename (name + extension), useful in usage messages.

---

## Suppressing Expected Errors

Sometimes a command may fail "expectedly" (e.g., deleting a file that might not exist). Suppress the error output and reset ERRORLEVEL:

```bat
DEL "optional_file.tmp" 2> NUL
SET ERRORLEVEL=0
```

Or using `||` to handle it inline:

```bat
DEL "optional_file.tmp" 2> NUL || REM Ignore if file doesn't exist
```

---

## Trapping ERRORLEVEL After Pipes

> ⚠️ Pipes and redirection can reset ERRORLEVEL. Always capture ERRORLEVEL **immediately** after the relevant command, before any pipes or redirects.

```bat
some_command
SET "err=%ERRORLEVEL%"
REM Now use %err% instead of %ERRORLEVEL%
IF %err% NEQ 0 ECHO Failed: %err%
```

---

## ERRORLEVEL with ROBOCOPY

ROBOCOPY uses a bitmask for ERRORLEVEL (non-zero doesn't always mean failure):

| ERRORLEVEL | Meaning |
|---|---|
| 0 | No files copied, no failure |
| 1 | Files copied successfully |
| 2 | Extra files in destination |
| 4 | Mismatched files |
| 8 | Copy failed (some files) |
| 16 | Fatal error |

So for ROBOCOPY, success means ERRORLEVEL < 8:

```bat
ROBOCOPY "%src%" "%dst%" /E /Z
IF %ERRORLEVEL% GEQ 8 (
    ECHO ROBOCOPY failed with code %ERRORLEVEL%
    EXIT /B 1
)
```

---

## Global Error Handler Pattern

```bat
@ECHO OFF
SETLOCAL

CALL :main
IF %ERRORLEVEL% NEQ 0 (
    ECHO Script encountered errors. Check log for details.
    EXIT /B 1
)
EXIT /B 0

:main
CALL :step1 || GOTO :fail
CALL :step2 || GOTO :fail
CALL :step3 || GOTO :fail
GOTO :EOF

:fail
ECHO Step failed. Aborting.
EXIT /B 1

:step1
ECHO Running step 1...
GOTO :EOF

:step2
ECHO Running step 2...
GOTO :EOF

:step3
ECHO Running step 3...
GOTO :EOF
```

---

## Logging Errors

```bat
:logError
ECHO [%DATE% %TIME%] ERROR: %~1 >> "%LOG_FILE%"
ECHO ERROR: %~1
GOTO :EOF
```

```bat
XCOPY "%src%" "%dst%" /E /Y
IF %ERRORLEVEL% NEQ 0 CALL :logError "XCOPY failed with code %ERRORLEVEL%"
```

---

*Next: [08 — String & Number Operations](08_String_and_Number_Operations.md)*
