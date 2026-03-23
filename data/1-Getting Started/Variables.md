# 02 — Variables

> **Part of:** Batch File Language Documentation Series  
> **Back to:** `00_Overview.md`

---

## Overview

Batch files use **environment variables** as their primary storage mechanism. All variables are strings — there is no native integer, float, or boolean type, though arithmetic operations and comparisons can simulate them.

---

## Setting Variables

Use `SET` to create or modify a variable:

```bat
SET name=Alice
SET count=10
SET "greeting=Hello, World!"
```

> ✅ **Best practice:** Always wrap assignments in quotes — `SET "var=value"` — to avoid accidentally including trailing spaces in the value.

### Checking the value

```bat
ECHO %name%
```

Output: `Alice`

---

## Reading Variables

Variables are referenced by wrapping the name in `%`:

```bat
SET "city=Lahore"
ECHO I live in %city%
```

Output: `I live in Lahore`

If a variable is not defined, `%varname%` expands to the literal text `%varname%` (i.e., it does not error).

---

## Deleting Variables

```bat
SET name=
```

Setting a variable to an empty string effectively undefines it.

---

## Environment Variables

Batch scripts have access to all Windows environment variables:

| Variable | Description |
|---|---|
| `%USERNAME%` | Current logged-in user |
| `%COMPUTERNAME%` | Machine name |
| `%OS%` | Operating system |
| `%PATH%` | Executable search path |
| `%TEMP%` | Temporary files directory |
| `%USERPROFILE%` | Current user's home directory |
| `%APPDATA%` | AppData\Roaming directory |
| `%CD%` | Current working directory |
| `%DATE%` | Current date |
| `%TIME%` | Current time |
| `%RANDOM%` | Pseudo-random number (0–32767) |
| `%ERRORLEVEL%` | Exit code of last command |

---

## Arithmetic with SET /A

Use `SET /A` for integer arithmetic:

```bat
SET /A result = 5 + 3
ECHO %result%
```

Output: `8`

Supported operators:

| Operator | Operation |
|---|---|
| `+` | Addition |
| `-` | Subtraction |
| `*` | Multiplication |
| `/` | Integer division |
| `%%` | Modulo (use `%` in cmd, `%%` in scripts) |
| `&` | Bitwise AND |
| `\|` | Bitwise OR |
| `^` | Bitwise XOR |
| `<<` `>>` | Bit shift |

Example — increment a counter:

```bat
SET /A count = count + 1
```

Or shorthand:

```bat
SET /A count += 1
```

> ⚠️ `SET /A` works only with integers. No floating-point support.

---

## Scope and SETLOCAL / ENDLOCAL

By default, variables set in a batch script persist in the calling environment after the script ends. To localize changes:

```bat
SETLOCAL
SET "temp_var=only exists here"
ECHO %temp_var%
ENDLOCAL
REM temp_var is now gone
```

`SETLOCAL` creates a snapshot of the environment. `ENDLOCAL` restores it. This is critical for writing reusable scripts that don't pollute the caller's environment.

---

## Passing Variables Out of SETLOCAL

Since `ENDLOCAL` discards all variables set since `SETLOCAL`, passing a value out requires a trick:

```bat
SETLOCAL
SET "result=hello"
ENDLOCAL & SET "result=%result%"
```

The `& SET` on the same line as `ENDLOCAL` runs in the **outer** scope before the environment is discarded.

---

## Variable Substitution in Arguments (for `%0`–`%9`)

Batch provides modifiers for argument variables:

| Modifier | Description |
|---|---|
| `%~1` | Removes surrounding quotes |
| `%~f1` | Full path |
| `%~d1` | Drive letter only |
| `%~p1` | Path only (no drive, no filename) |
| `%~n1` | Filename without extension |
| `%~x1` | Extension only |
| `%~z1` | File size in bytes |
| `%~t1` | File date/time |

Example:

```bat
ECHO Full path: %~f1
ECHO Filename:  %~n1
ECHO Extension: %~x1
```

---

## Delayed Variable Expansion

Inside `IF` or `FOR` blocks, variables are expanded once at **parse time**, not at execution time. This means changes inside a block aren't visible within the same block using `%var%`.

Solution: Enable **delayed expansion** with `!var!` syntax:

```bat
SETLOCAL ENABLEDELAYEDEXPANSION
SET count=0
FOR %%i IN (a b c) DO (
    SET /A count += 1
    ECHO Item !count!: %%i
)
ENDLOCAL
```

Without `!count!`, all three lines would print `Item 0:`.

> See `10_Advanced_Topics.md` for a full explanation of delayed expansion.

---

## String Operations on Variables

Batch supports basic string manipulation via variable substitution syntax:

### Substring

```bat
SET "str=HelloWorld"
ECHO %str:~0,5%
```
Output: `Hello` (start at index 0, take 5 characters)

```bat
ECHO %str:~5%
```
Output: `World` (from index 5 to end)

```bat
ECHO %str:~-5%
```
Output: `World` (last 5 characters)

### Find and Replace

```bat
SET "str=Hello World"
ECHO %str:World=Batch%
```
Output: `Hello Batch`

### Delete a substring

```bat
SET "str=Hello World"
ECHO %str: =%
```
Output: `HelloWorld` (replaces spaces with nothing)

---

## Practical Example

```bat
@ECHO OFF
SETLOCAL ENABLEDELAYEDEXPANSION

SET "files=0"
FOR %%F IN (*.txt) DO (
    SET /A files += 1
)

ECHO Found !files! text files in current directory.
ENDLOCAL
```

---

*Next: [03 — Control Flow](03_Control_Flow.md)*
