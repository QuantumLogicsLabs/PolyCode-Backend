# 05 — Input & Output

> **Part of:** Batch File Language Documentation Series  
> **Back to:** `00_Overview.md`

---

## Overview

Batch files interact with three standard I/O streams inherited from Unix-style systems:

| Stream | Number | Default |
|---|---|---|
| STDIN | 0 | Keyboard |
| STDOUT | 1 | Console |
| STDERR | 2 | Console |

---

## Output Redirection

### Write to a file (overwrite)

```bat
ECHO Hello > output.txt
DIR > listing.txt
```

### Append to a file

```bat
ECHO Line 1 > log.txt
ECHO Line 2 >> log.txt
ECHO Line 3 >> log.txt
```

### Redirect STDERR

```bat
command 2> errors.txt
```

### Redirect both STDOUT and STDERR

```bat
command > output.txt 2>&1
```

`2>&1` means "redirect stream 2 (STDERR) to wherever stream 1 (STDOUT) is currently going" — so both go to `output.txt`.

### Suppress output (send to NUL)

```bat
command > NUL               REM Suppress STDOUT
command 2> NUL              REM Suppress STDERR
command > NUL 2>&1          REM Suppress everything
```

---

## Input Redirection

Feed file contents as STDIN to a command:

```bat
SORT < data.txt
MORE < README.txt
```

---

## Pipes

The `|` operator connects STDOUT of one command to STDIN of the next:

```bat
DIR /B | SORT
DIR /B | FIND ".txt"
TYPE log.txt | FIND "ERROR" | MORE
```

### Pipeline chain example

```bat
DIR /B /S *.log | FIND /C ".log"
```

This lists all `.log` files recursively and counts them.

---

## User Input with SET /P

`SET /P` prompts the user and reads their input into a variable:

```bat
SET /P name=Enter your name: 
ECHO Hello, %name%!
```

### Input with default value pattern

```bat
SET "default=yes"
SET /P choice=Continue? [%default%]: 
IF "%choice%"=="" SET "choice=%default%"
ECHO You chose: %choice%
```

---

## User Input with CHOICE

`CHOICE` is more controlled — limits input to specific keys:

```bat
CHOICE /C YN /M "Are you sure?"
IF ERRORLEVEL 2 ECHO You chose No.
IF ERRORLEVEL 1 ECHO You chose Yes.
```

> CHOICE sets ERRORLEVEL to the **position** of the selected key (1 = first key, 2 = second key, etc.). Check from highest to lowest since `IF ERRORLEVEL n` is true for n **or greater**.

With timeout:

```bat
CHOICE /C YN /T 10 /D Y /M "Continue in 10 seconds?"
```

---

## Reading a File Line by Line

```bat
FOR /F "tokens=* eol=" %%line IN (data.txt) DO (
    ECHO %%line
)
```

- `tokens=*` — capture the entire line.
- `eol=` — do not skip lines starting with `;` (default behavior).

---

## Reading Command Output

Capture the output of a command into a variable:

```bat
FOR /F "tokens=*" %%out IN ('DATE /T') DO SET "today=%%out"
ECHO Today is: %today%
```

---

## Writing Multi-line Files

```bat
(
    ECHO Line 1
    ECHO Line 2
    ECHO Line 3
) > output.txt
```

This writes all three lines at once by grouping the output of multiple ECHO commands.

### Creating a file with specific content

```bat
(
    ECHO [Config]
    ECHO debug=false
    ECHO log_level=info
) > config.ini
```

---

## Appending with Blocks

```bat
(
    ECHO New section start
    ECHO Content here
) >> existing_log.txt
```

---

## Logging Pattern

A common pattern for production scripts:

```bat
@ECHO OFF
SET "log=C:\logs\script.log"

CALL :log "Script started"
CALL :doWork
CALL :log "Script finished"
EXIT /B 0

:log
ECHO [%DATE% %TIME%] %~1 >> "%log%"
ECHO [%DATE% %TIME%] %~1
GOTO :EOF

:doWork
CALL :log "Doing work..."
REM ... actual work here ...
GOTO :EOF
```

---

## Handling Output in Conditions

Check if a command produces any output:

```bat
TASKLIST | FIND /I "notepad.exe" > NUL
IF %ERRORLEVEL% EQU 0 (
    ECHO Notepad is running.
) ELSE (
    ECHO Notepad is not running.
)
```

---

## HERE-DOC Style Input

Batch doesn't have true heredocs, but you can simulate them:

```bat
(
    ECHO Subject: Test Email
    ECHO Body: This is a test.
) | some_mail_tool
```

---

## STDIN with CHOICE or SET /P in Automation

When running scripts non-interactively (e.g., via Task Scheduler), avoid `SET /P` and `CHOICE` since there's no user. Instead, use arguments or config files to pass parameters.

---

## Practical Example — User-Driven Backup

```bat
@ECHO OFF
SETLOCAL

SET /P src=Enter source folder: 
SET /P dst=Enter destination folder: 

IF NOT EXIST "%src%" (
    ECHO Source folder does not exist.
    EXIT /B 1
)

ECHO Copying from %src% to %dst%...
XCOPY "%src%" "%dst%" /E /I /Y > NUL 2>&1

IF %ERRORLEVEL% EQU 0 (
    ECHO Backup completed successfully.
) ELSE (
    ECHO Backup failed.
)

ENDLOCAL
```

---

*Next: [06 — Functions & Modularity](06_Functions_and_Modularity.md)*
