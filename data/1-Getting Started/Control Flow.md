# 03 — Control Flow

> **Part of:** Batch File Language Documentation Series  
> **Back to:** `00_Overview.md`

---

## Overview

Batch files support three primary control flow mechanisms:

1. **Conditional execution** — `IF` statements
2. **Loops** — `FOR` loops
3. **Jumps** — `GOTO` and labels

---

## IF Statements

### Basic Syntax

```bat
IF condition command
```

Or with a block:

```bat
IF condition (
    command1
    command2
)
```

### IF / ELSE

```bat
IF condition (
    echo True branch
) ELSE (
    echo False branch
)
```

> ⚠️ The `ELSE` must be on the **same line** as the closing `)` of the IF block, or on the same line as the single IF command.

---

### Comparing Strings

```bat
IF "%name%"=="Alice" ECHO Hello Alice
```

Case-insensitive comparison with `/I`:

```bat
IF /I "%name%"=="alice" ECHO Hello Alice
```

Always quote variables to handle empty values and spaces:

```bat
IF "%var%"=="" ECHO Variable is empty
```

### Negation with NOT

```bat
IF NOT "%name%"=="Alice" ECHO Not Alice
```

---

### Comparing Numbers

```bat
IF %count% EQU 5 ECHO Count is five
```

Numeric comparison operators:

| Operator | Meaning |
|---|---|
| `EQU` | Equal |
| `NEQ` | Not equal |
| `LSS` | Less than |
| `LEQ` | Less than or equal |
| `GTR` | Greater than |
| `GEQ` | Greater than or equal |

Example:

```bat
IF %score% GEQ 50 (
    ECHO Passed
) ELSE (
    ECHO Failed
)
```

---

### Checking File/Directory Existence

```bat
IF EXIST "C:\file.txt" ECHO File exists
IF NOT EXIST "C:\folder" MKDIR "C:\folder"
```

### Checking ERRORLEVEL

```bat
some_command
IF %ERRORLEVEL% NEQ 0 ECHO Command failed

REM Older style (true if errorlevel is >= value):
IF ERRORLEVEL 1 ECHO An error occurred
```

---

## FOR Loops

`FOR` is the only loop construct in batch. It has several forms.

### FOR — Iterate over a set of values

```bat
FOR %%variable IN (set) DO command
```

> In batch scripts use `%%` for loop variables; in command line use `%`.

#### Iterate over a list:

```bat
FOR %%i IN (apple banana cherry) DO ECHO %%i
```

#### Iterate over files:

```bat
FOR %%f IN (*.txt) DO ECHO %%f
```

---

### FOR /L — Loop with a counter (numeric range)

```bat
FOR /L %%i IN (start, step, end) DO command
```

Example — count from 1 to 10:

```bat
FOR /L %%i IN (1, 1, 10) DO ECHO %%i
```

Count down:

```bat
FOR /L %%i IN (10, -1, 1) DO ECHO %%i
```

---

### FOR /D — Iterate over directories

```bat
FOR /D %%d IN (*) DO ECHO Directory: %%d
```

---

### FOR /R — Recursive file iteration

```bat
FOR /R "C:\folder" %%f IN (*.log) DO ECHO %%f
```

Recursively finds all `.log` files under `C:\folder`.

---

### FOR /F — Parse file content, command output, or strings

This is the most powerful `FOR` variant.

#### Parse a file line by line:

```bat
FOR /F "tokens=*" %%line IN (data.txt) DO ECHO %%line
```

#### Parse command output:

```bat
FOR /F "tokens=*" %%out IN ('dir /b *.txt') DO ECHO %%out
```

#### Parse a string:

```bat
FOR /F "tokens=1,2 delims=," %%a IN ("Alice,30") DO (
    ECHO Name: %%a
    ECHO Age: %%b
)
```

**FOR /F Options:**

| Option | Description |
|---|---|
| `tokens=n` | Which token(s) to capture (1-based) |
| `delims=chars` | Characters that separate tokens (default: space/tab) |
| `skip=n` | Skip first n lines |
| `eol=c` | Lines beginning with this char are ignored |
| `usebackq` | Allows quoted filenames; use backticks for commands |

#### usebackq example (handles paths with spaces):

```bat
FOR /F "usebackq tokens=*" %%line IN ("my file.txt") DO ECHO %%line
FOR /F "usebackq tokens=*" %%out IN (`some command`) DO ECHO %%out
```

---

## GOTO and Labels

### GOTO

Unconditionally jumps to a label:

```bat
GOTO :myLabel

ECHO This line is skipped.

:myLabel
ECHO Jumped here.
```

### Common Pattern — Skip to End

```bat
IF %error%==1 GOTO :error

ECHO Normal execution.
GOTO :EOF

:error
ECHO Something went wrong.
EXIT /B 1
```

`:EOF` is a special built-in label meaning "end of file" — used with `GOTO :EOF` to exit the current script or subroutine cleanly.

---

## Nested Conditions

```bat
IF EXIST "config.txt" (
    IF %mode%==debug (
        ECHO Debug mode with config found.
    ) ELSE (
        ECHO Normal mode with config found.
    )
) ELSE (
    ECHO No config file found.
)
```

---

## Practical Example — Menu

```bat
@ECHO OFF
:menu
CLS
ECHO ================================
ECHO   Main Menu
ECHO ================================
ECHO  1. Run Backup
ECHO  2. View Logs
ECHO  3. Exit
ECHO ================================
SET /P choice=Enter choice: 

IF "%choice%"=="1" GOTO :backup
IF "%choice%"=="2" GOTO :logs
IF "%choice%"=="3" EXIT /B 0
ECHO Invalid choice.
GOTO :menu

:backup
ECHO Running backup...
GOTO :menu

:logs
ECHO Showing logs...
GOTO :menu
```

---

*Next: [04 — Commands Reference](04_Commands_Reference.md)*
