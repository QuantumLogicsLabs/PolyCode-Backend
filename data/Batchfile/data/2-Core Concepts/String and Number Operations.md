# 08 — String & Number Operations

> **Part of:** Batch File Language Documentation Series  
> **Back to:** `00_Overview.md`

---

## Overview

All batch variables are stored as strings. Numbers are treated as strings unless `SET /A` is used. This section covers all built-in techniques for manipulating strings and performing arithmetic.

---

## String Basics

### Length

Batch has no native `strlen` function. Common workarounds:

**Method 1 — FOR /L counting trick (slow):**

```bat
SET "str=Hello"
CALL :strlen "%str%" len
ECHO Length: %len%
EXIT /B

:strlen
SETLOCAL ENABLEDELAYEDEXPANSION
SET "s=%~1"
SET len=0
:loop
IF "!s!"=="" (ENDLOCAL & SET "%2=%len%" & GOTO :EOF)
SET "s=!s:~1!"
SET /A len += 1
GOTO :loop
```

**Method 2 — PowerShell one-liner (simpler):**

```bat
FOR /F %%i IN ('powershell -command "'Hello'.Length"') DO SET len=%%i
ECHO Length: %len%
```

---

## Substrings

Syntax: `%var:~start,length%`

```bat
SET "str=Hello, World!"
```

| Expression | Result | Description |
|---|---|---|
| `%str:~0,5%` | `Hello` | First 5 characters |
| `%str:~7%` | `World!` | From index 7 to end |
| `%str:~7,5%` | `World` | 5 chars starting at index 7 |
| `%str:~-6%` | `orld!` | Last 6 characters |
| `%str:~0,-1%` | `Hello, World` | All but last character |
| `%str:~-6,5%` | `World` | 5 chars from 6th from end |

---

## Find and Replace

Syntax: `%var:find=replace%`

```bat
SET "str=Hello World"
ECHO %str:World=Batch%
```

Output: `Hello Batch`

### Delete a substring (replace with nothing)

```bat
SET "str=Hello World"
ECHO %str: =%
```

Output: `HelloWorld`

### Replace all occurrences

```bat
SET "str=aabbcc"
ECHO %str:b=X%
```

Output: `aaXXcc` — replaces **all** occurrences.

### Case conversion

Native batch has **no** uppercase/lowercase conversion. Use PowerShell or `FOR /F` workarounds:

```bat
FOR /F %%i IN ('powershell -command "'hello world'.ToUpper()"') DO SET "upper=%%i"
ECHO %upper%
```

---

## String Comparisons in IF

```bat
IF "%str%"=="hello" ECHO Match
IF /I "%str%"=="HELLO" ECHO Case-insensitive match
IF NOT "%str%"=="" ECHO Not empty
```

### Checking if a string contains a substring

```bat
ECHO %str% | FIND /I "word" > NUL
IF %ERRORLEVEL% EQU 0 ECHO Contains "word"
```

Or with FINDSTR:

```bat
ECHO %str% | FINDSTR /I "word" > NUL && ECHO Contains "word"
```

---

## String Concatenation

Simply place variable references adjacent to each other or to literal text:

```bat
SET "first=Hello"
SET "second=World"
SET "combined=%first%, %second%!"
ECHO %combined%
```

Output: `Hello, World!`

---

## Trimming Whitespace

Batch has no built-in trim. A workaround using FOR /F:

```bat
SET "str=   hello   "
FOR /F "tokens=* delims= " %%i IN ("%str%") DO SET "str=%%i"
ECHO [%str%]
```

Output: `[hello   ]` — trims **leading** spaces only.

Trimming trailing spaces is harder and often requires a loop or PowerShell.

---

## Checking If a Variable Is Empty

```bat
IF "%var%"=="" ECHO Variable is empty
IF NOT DEFINED var ECHO Variable is not defined
```

> `IF NOT DEFINED` is more reliable — it returns true if the variable doesn't exist at all.

---

## Number Arithmetic

Use `SET /A` for integer math:

```bat
SET /A result = 10 + 3    & REM 13
SET /A result = 10 - 3    & REM 7
SET /A result = 10 * 3    & REM 30
SET /A result = 10 / 3    & REM 3  (integer division)
SET /A result = 10 %% 3   & REM 1  (modulo — use %% in scripts)
```

### Compound assignment

```bat
SET /A count += 1
SET /A count -= 1
SET /A count *= 2
SET /A count /= 2
```

### Random numbers

`%RANDOM%` returns a random integer between 0 and 32767:

```bat
ECHO %RANDOM%
```

Range between 1 and 100:

```bat
SET /A num = %RANDOM% %% 100 + 1
ECHO Random 1-100: %num%
```

### Using variables in SET /A

```bat
SET x=5
SET y=3
SET /A z = x + y
ECHO %z%
```

Note: Inside `SET /A`, variable names don't need `%` delimiters — they're expanded automatically.

---

## Floating Point

Batch has **no native floating-point support**. Workarounds:

**Using PowerShell:**

```bat
FOR /F %%r IN ('powershell -command "10 / 3.0"') DO SET "result=%%r"
ECHO %result%
```

Output: `3.33333333333333`

---

## Number Comparisons

Use numeric comparison operators (not string operators):

```bat
SET /A val = 42
IF %val% EQU 42 ECHO Equal
IF %val% GTR 10 ECHO Greater than 10
IF %val% LSS 100 ECHO Less than 100
```

---

## Date and Time Math

Getting current date parts:

```bat
FOR /F "tokens=1-3 delims=/" %%a IN ("%DATE:~4%") DO (
    SET "month=%%a"
    SET "day=%%b"
    SET "year=%%c"
)
ECHO Month: %month%, Day: %day%, Year: %year%
```

> Date format varies by locale — this is a common source of bugs. PowerShell is more reliable for date manipulation.

Getting current time parts:

```bat
SET "hh=%TIME:~0,2%"
SET "mm=%TIME:~3,2%"
SET "ss=%TIME:~6,2%"
ECHO Time: %hh%:%mm%:%ss%
```

---

## Practical Example — Padded Numbers

Zero-pad a number to 3 digits:

```bat
SET /A n = 7
SET "padded=00%n%"
SET "padded=%padded:~-3%"
ECHO %padded%
```

Output: `007`

---

*Next: [09 — File & Directory Operations](09_File_and_Directory_Operations.md)*
