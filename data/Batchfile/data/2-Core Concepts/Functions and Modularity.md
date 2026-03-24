# 06 — Functions & Modularity

> **Part of:** Batch File Language Documentation Series  
> **Back to:** `00_Overview.md`

---

## Overview

Batch files don't have true functions in the programming language sense, but they support **subroutines** — labeled blocks of code called with `CALL :label` and exited with `GOTO :EOF`. Combined with `SETLOCAL`/`ENDLOCAL`, these behave much like functions.

---

## Subroutines

### Defining a Subroutine

A subroutine is simply a labeled section at the bottom of the script:

```bat
:mySubroutine
ECHO I am a subroutine.
GOTO :EOF
```

`GOTO :EOF` exits the subroutine and returns control to the caller.

---

### Calling a Subroutine

```bat
CALL :mySubroutine
```

---

### Full Example

```bat
@ECHO OFF

CALL :greet
ECHO Back in main script.
EXIT /B 0

:greet
ECHO Hello from the subroutine!
GOTO :EOF
```

Output:
```
Hello from the subroutine!
Back in main script.
```

---

## Passing Arguments to Subroutines

Arguments are passed after the label name and accessed via `%1`, `%2`, etc.:

```bat
@ECHO OFF

CALL :greet Alice 30
EXIT /B 0

:greet
ECHO Name: %1
ECHO Age:  %2
GOTO :EOF
```

Output:
```
Name: Alice
Age:  30
```

> ⚠️ If argument values contain spaces, quote them: `CALL :sub "Hello World"`  
> And use `%~1` in the subroutine to strip the quotes.

---

## Return Values

Batch subroutines can't return values directly, but they can set a variable that the caller reads:

```bat
@ECHO OFF

CALL :add 5 3
ECHO Result: %result%
EXIT /B 0

:add
SET /A result = %1 + %2
GOTO :EOF
```

Output: `Result: 8`

For isolated variable scope, use the SETLOCAL trick:

```bat
:add
SETLOCAL
SET /A local_result = %1 + %2
ENDLOCAL & SET "result=%local_result%"
GOTO :EOF
```

---

## Variable Scoping in Subroutines

By default, all variables share the same scope in batch. Use `SETLOCAL`/`ENDLOCAL` inside subroutines to avoid polluting the global environment:

```bat
:cleanSubroutine
SETLOCAL
SET "temp=only exists here"
ECHO %temp%
ENDLOCAL
GOTO :EOF
```

After `ENDLOCAL`, `temp` is gone.

---

## Calling External Scripts

`CALL` can also invoke other batch files:

```bat
CALL helper.bat
CALL helper.bat arg1 arg2
```

The external script runs, then control returns here. Without `CALL`, control does **not** return.

### Passing and receiving data between scripts

Use environment variables (if `SETLOCAL` is not used in the child) or temp files for communication:

```bat
REM parent.bat
SET "shared_data=hello"
CALL child.bat
ECHO Child set: %result%
```

```bat
REM child.bat
ECHO Received: %shared_data%
SET "result=world"
```

---

## Structuring Large Scripts

For large projects, split into multiple files:

```
project/
  main.bat          ← entry point
  lib\utils.bat     ← utility subroutines
  lib\logger.bat    ← logging helpers
  config\settings.bat ← configuration
```

```bat
REM main.bat
@ECHO OFF
CALL config\settings.bat
CALL lib\logger.bat :init
CALL :run
EXIT /B 0

:run
CALL lib\utils.bat :doSomething
GOTO :EOF
```

---

## Practical Logger Module

```bat
REM lib\logger.bat — callable as: CALL lib\logger.bat :info "Message"

:info
ECHO [INFO]  [%DATE% %TIME%] %~2
ECHO [INFO]  [%DATE% %TIME%] %~2 >> "%LOG_FILE%"
GOTO :EOF

:warn
ECHO [WARN]  [%DATE% %TIME%] %~2
ECHO [WARN]  [%DATE% %TIME%] %~2 >> "%LOG_FILE%"
GOTO :EOF

:error
ECHO [ERROR] [%DATE% %TIME%] %~2
ECHO [ERROR] [%DATE% %TIME%] %~2 >> "%LOG_FILE%"
GOTO :EOF
```

Usage in main script:

```bat
SET "LOG_FILE=C:\logs\run.log"
CALL lib\logger.bat :info "Starting backup"
CALL lib\logger.bat :error "Backup failed"
```

---

## Recursive Subroutines

Batch supports recursion (with caution — there's no enforced stack limit, but deep recursion can cause issues):

```bat
@ECHO OFF
CALL :countdown 5
EXIT /B 0

:countdown
IF %1 LEQ 0 GOTO :EOF
ECHO %1
SET /A next = %1 - 1
CALL :countdown %next%
GOTO :EOF
```

Output:
```
5
4
3
2
1
```

---

## Subroutine Best Practices

- Always end subroutines with `GOTO :EOF`.
- Use `SETLOCAL`/`ENDLOCAL` to prevent variable leakage.
- Place all subroutines **after** the main code, preceded by `EXIT /B 0` to prevent fall-through.
- Document subroutine parameters with `REM` comments.
- Keep subroutines focused on a single task.

```bat
@ECHO OFF

REM --- Main ---
CALL :backupFiles "C:\src" "D:\dst"
EXIT /B 0

REM ============================================================
:backupFiles
REM Parameters: %1 = source path, %2 = destination path
REM Returns: sets %backup_status% to 0 (success) or 1 (failure)
REM ============================================================
SETLOCAL
SET "src=%~1"
SET "dst=%~2"

IF NOT EXIST "%src%" (
    ENDLOCAL & SET "backup_status=1"
    GOTO :EOF
)

XCOPY "%src%" "%dst%" /E /I /Y > NUL 2>&1
ENDLOCAL & SET "backup_status=%ERRORLEVEL%"
GOTO :EOF
```

---

*Next: [07 — Error Handling](07_Error_Handling.md)*
