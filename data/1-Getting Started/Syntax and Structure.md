# 01 — Syntax & Structure

> **Part of:** Batch File Language Documentation Series  
> **Back to:** `00_Overview.md`

---

## File Anatomy

A batch file is a plain UTF-8 or ANSI text file with `.bat` or `.cmd` extension. When executed, `cmd.exe` reads it line by line from top to bottom.

```
[Optional: @ECHO OFF]
[Optional: Comments]
[Body: Commands, variables, logic]
[Optional: EXIT /B]
```

---

## The First Line Convention

Nearly every professional batch script begins with:

```bat
@ECHO OFF
```

- `ECHO OFF` — tells `cmd.exe` to stop printing each command before executing it (echoing).
- `@` — suppresses the echo of the `ECHO OFF` command itself, keeping the output clean.

Without it, every command prints to the console before running, which is noisy and confusing for end users.

---

## Line Structure

Each line in a batch file is one of:

| Type | Example |
|---|---|
| Command | `ECHO Hello` |
| Comment | `REM This is a comment` |
| Label | `:start` |
| Variable assignment | `SET "name=value"` |
| Empty line | *(ignored)* |

### Line Continuation

Batch does **not** support the traditional backslash `\` line continuation used in other languages cleanly. However, you can use `^` (caret) to continue a long command on the next line:

```bat
ECHO This is a very long line ^
that continues here
```

The `^` escapes the newline, causing the next line to be treated as part of the same command.

---

## Comments

Use `REM` (Remark) for comments:

```bat
REM This is a comment and will not be executed
```

An alternative (faster but less readable) is using `::`:

```bat
:: This also acts as a comment in most contexts
```

> ⚠️ **Warning:** `::` inside code blocks (parentheses) can cause errors. Prefer `REM` inside `IF` and `FOR` blocks.

---

## Case Sensitivity

Batch is **case-insensitive** for commands and variable names:

```bat
echo hello
ECHO hello
Echo hello
```

All three are identical. However, **string comparisons in IF** are case-sensitive by default (use `/I` flag to ignore case):

```bat
IF /I "%name%"=="alice" ECHO Hello Alice
```

---

## Labels

Labels are used as jump targets for `GOTO` and subroutine markers for `CALL`:

```bat
:myLabel
ECHO I am at myLabel
```

- Must start with `:`.
- The rest of the line after `:labelname` (with a space or end of line) is ignored.
- Labels are **case-insensitive**.

---

## Special Characters

Several characters have special meaning in batch and must be escaped with `^` if used literally:

| Character | Meaning | Escaped Form |
|---|---|---|
| `%` | Variable delimiter | `%%` (in scripts) |
| `^` | Escape character | `^^` |
| `&` | Command separator | `^&` |
| `\|` | Pipe | `^\|` |
| `<` | Input redirect | `^<` |
| `>` | Output redirect | `^>` |
| `(` `)` | Grouping | `^(` `^)` |

---

## Encoding

- Default encoding: **ANSI** (Windows code page, e.g., CP1252).
- For Unicode support: use `CHCP 65001` at the start to switch to UTF-8.

```bat
@ECHO OFF
CHCP 65001 > NUL
ECHO Héllo Wörld
```

> Note: Full Unicode support in CMD is limited regardless of code page.

---

## File Extensions: .bat vs .cmd

Both `.bat` and `.cmd` work with `cmd.exe`. The difference:

- `.bat` — legacy extension from MS-DOS; compatible with older systems.
- `.cmd` — Windows NT extension; slightly different behavior in some edge cases (e.g., `ERRORLEVEL` propagation with `CALL`).

For modern Windows scripting, `.cmd` is preferred.

---

## Execution Flow

Commands execute **sequentially** from top to bottom unless altered by:

- `GOTO :label` — jumps to a label.
- `CALL :label` — calls a subroutine.
- `IF` / `FOR` — conditional and iterative execution.
- `EXIT /B` — exits the script or subroutine.

---

## Blank Lines and Whitespace

- Blank lines are ignored.
- Leading spaces on a line are generally ignored for commands.
- Trailing spaces in variable assignments **are significant** — `SET name=value ` sets a variable with a trailing space.

---

## Script Arguments

Arguments passed to a batch script are accessed via `%1` through `%9`:

```bat
@ECHO OFF
ECHO First argument: %1
ECHO Second argument: %2
```

Run as: `myscript.bat Hello World`  
Output:
```
First argument: Hello
Second argument: World
```

- `%0` — the script's own filename/path.
- `%*` — all arguments combined.
- Use `SHIFT` to shift arguments left (makes `%2` become `%1`, etc.).

---

## Nested Parentheses (Code Blocks)

Parentheses group multiple commands, commonly used with `IF` and `FOR`:

```bat
IF EXIST "file.txt" (
    ECHO File found.
    DEL "file.txt"
    ECHO File deleted.
)
```

The entire block executes as a unit when the condition is true.

> ⚠️ Variables inside parentheses expand at **parse time** unless delayed expansion is enabled. See `10_Advanced_Topics.md`.

---

*Next: [02 — Variables](02_Variables.md)*
