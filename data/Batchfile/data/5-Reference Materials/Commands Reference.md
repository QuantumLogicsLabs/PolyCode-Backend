# 04 — Commands Reference

> **Part of:** Batch File Language Documentation Series  
> **Back to:** `00_Overview.md`

---

## Overview

This section covers the most commonly used built-in batch commands. Commands are **case-insensitive**. For full documentation on any command, run `command /?` in CMD.

---

## Output Commands

### ECHO

Displays text or messages. Controls command echoing.

```bat
ECHO Hello, World!
ECHO.                    REM Prints a blank line
ECHO OFF                 REM Stops command echoing
ECHO ON                  REM Restores command echoing
```

> `ECHO.` (with a dot, no space) is the reliable way to print a blank line.

---

### CLS

Clears the console screen.

```bat
CLS
```

---

## Comment Commands

### REM

Remark — the line is ignored by the interpreter.

```bat
REM This is a comment
REM Author: Alice
REM Version: 1.0
```

---

## Script Control Commands

### PAUSE

Suspends execution and waits for any key press.

```bat
PAUSE
```

Output: `Press any key to continue . . .`

To suppress the message:

```bat
PAUSE > NUL
```

---

### EXIT

Exits the script or command processor.

```bat
EXIT /B 0       REM Exit current script with code 0 (success)
EXIT /B 1       REM Exit with error code 1
EXIT            REM Exits CMD entirely (closes the window)
```

> Always use `EXIT /B` in scripts to avoid closing the user's CMD window.

---

### CALL

Calls another batch script or a subroutine within the same script.

```bat
CALL other_script.bat
CALL other_script.bat arg1 arg2
CALL :mySubroutine
CALL :mySubroutine param1
```

Control returns to the calling script after the called script/subroutine finishes.

> Without `CALL`, invoking another `.bat` file transfers control permanently (the original script does not resume).

---

### GOTO

Jumps execution to a label.

```bat
GOTO :label
GOTO :EOF       REM Jump to end of file / exit subroutine
```

---

## Variable Commands

### SET

Sets or displays environment variables.

```bat
SET                         REM Displays all variables
SET name=Alice              REM Sets variable
SET "name=Alice"            REM Recommended form
SET /A count=count+1        REM Arithmetic
SET /P input=Enter value:   REM Prompts user for input
```

### SETLOCAL / ENDLOCAL

Localizes environment changes to the current script block.

```bat
SETLOCAL
SETLOCAL ENABLEDELAYEDEXPANSION
ENDLOCAL
```

---

## File and Directory Commands

### CD / CHDIR

Changes the current directory.

```bat
CD C:\Users
CD ..               REM Go up one level
CD /D D:\Projects   REM Change drive and directory
```

### DIR

Lists directory contents.

```bat
DIR                         REM List current directory
DIR C:\Projects             REM List specific directory
DIR *.txt                   REM List only .txt files
DIR /B                      REM Bare format (names only)
DIR /S                      REM Recursive listing
DIR /A:D                    REM Directories only
DIR /A:H                    REM Hidden files
DIR /O:N                    REM Sort by name
```

### MKDIR / MD

Creates directories.

```bat
MKDIR C:\NewFolder
MD "My Folder"
MKDIR "C:\path\to\deep\folder"  REM Creates entire path
```

### RMDIR / RD

Removes directories.

```bat
RMDIR "C:\OldFolder"
RD /S /Q "C:\OldFolder"    REM /S = recursive, /Q = quiet (no confirm)
```

### COPY

Copies files.

```bat
COPY source.txt dest.txt
COPY "C:\src\*.*" "D:\dst\"
COPY /Y source.txt dest.txt     REM Overwrite without prompt
```

### XCOPY

Extended copy — supports directories and recursion.

```bat
XCOPY "C:\src" "D:\dst" /E /I /Y
```

| Flag | Meaning |
|---|---|
| `/E` | Copy directories including empty ones |
| `/I` | If destination doesn't exist, assume it's a directory |
| `/Y` | Suppress overwrite confirmation |
| `/D` | Copy only files newer than destination |
| `/H` | Include hidden and system files |
| `/S` | Recursive (excludes empty folders) |

### ROBOCOPY

Robust file copy — preferred for production use.

```bat
ROBOCOPY "C:\src" "D:\dst" /E /Z /LOG:copy.log
```

| Flag | Meaning |
|---|---|
| `/E` | Copy all subdirectories including empty |
| `/Z` | Restartable mode |
| `/MIR` | Mirror source to destination |
| `/LOG:file` | Write log to file |

### MOVE

Moves (or renames) files.

```bat
MOVE source.txt destination.txt
MOVE "C:\file.txt" "D:\folder\"
```

### DEL / ERASE

Deletes files.

```bat
DEL file.txt
DEL /Q *.tmp            REM Quiet mode
DEL /F readonly.txt     REM Force delete read-only files
DEL /S /Q "C:\logs\*.log"  REM Recursive delete
```

### REN / RENAME

Renames files.

```bat
REN oldname.txt newname.txt
```

---

## Text and Output Commands

### TYPE

Displays the contents of a text file.

```bat
TYPE file.txt
TYPE file.txt | MORE
```

### MORE

Paginated output, one screen at a time.

```bat
DIR /S | MORE
MORE file.txt
```

### FIND

Searches for a string in files or input.

```bat
FIND "error" logfile.txt
DIR | FIND "2024"
FIND /C "error" logfile.txt     REM Count occurrences
FIND /I "error" logfile.txt     REM Case-insensitive
FIND /V "error" logfile.txt     REM Lines NOT containing string
```

### FINDSTR

More powerful string search (supports regex).

```bat
FINDSTR "error" logfile.txt
FINDSTR /I "warning\|error" logfile.txt
FINDSTR /R "^[0-9]" data.txt    REM Lines starting with digit
```

### SORT

Sorts input alphabetically.

```bat
SORT file.txt
DIR /B | SORT
```

---

## System Commands

### TASKLIST

Lists running processes.

```bat
TASKLIST
TASKLIST | FIND "notepad"
```

### TASKKILL

Terminates processes.

```bat
TASKKILL /IM notepad.exe
TASKKILL /IM notepad.exe /F     REM Force kill
TASKKILL /PID 1234
```

### NET

Manages network services, users, shares.

```bat
NET START "Service Name"
NET STOP "Service Name"
NET USER alice /ADD
```

### SC

Service Control — manages Windows services.

```bat
SC QUERY "wuauserv"
SC START "wuauserv"
SC STOP "wuauserv"
SC CONFIG "service" START=AUTO
```

### REG

Registry manipulation from command line.

```bat
REG QUERY HKLM\Software\Microsoft
REG ADD HKCU\Software\MyApp /v "Setting" /t REG_SZ /d "Value"
REG DELETE HKCU\Software\MyApp /v "Setting" /F
```

### PING

Network connectivity test.

```bat
PING google.com
PING 192.168.1.1 -n 4      REM 4 pings
PING -n 1 server > NUL && ECHO Online || ECHO Offline
```

---

## Useful Miscellaneous Commands

### TITLE

Sets the CMD window title.

```bat
TITLE My Automation Script
```

### COLOR

Sets foreground/background color.

```bat
COLOR 0A      REM Black background, Green foreground
COLOR 07      REM Reset to default
```

Color codes: `0`=Black, `1`=Blue, `2`=Green, `3`=Cyan, `4`=Red, `5`=Magenta, `6`=Yellow, `7`=White, `A`=Light Green, `F`=Bright White.

### TIMEOUT

Waits for a specified number of seconds.

```bat
TIMEOUT /T 5              REM Wait 5 seconds
TIMEOUT /T 10 /NOBREAK    REM Can't be interrupted by keypress
TIMEOUT /T -1             REM Wait indefinitely
```

### CHOICE

Prompts user to choose from a list of keys.

```bat
CHOICE /C YN /M "Continue?"
IF ERRORLEVEL 2 GOTO :no
IF ERRORLEVEL 1 GOTO :yes
```

### START

Opens a file, URL, or application.

```bat
START notepad.exe
START https://www.example.com
START "" "C:\Program Files\App\app.exe"
START /WAIT program.exe     REM Wait for program to finish
START /MIN program.exe      REM Start minimized
```

### WHERE

Finds the location of an executable.

```bat
WHERE python
WHERE /Q git && ECHO Git is installed
```

### WMIC

Windows Management Instrumentation — powerful system info tool.

```bat
WMIC OS GET Caption,Version
WMIC CPU GET Name,NumberOfCores
WMIC DISKDRIVE GET Model,Size
```

---

*Next: [05 — Input & Output](05_Input_Output.md)*
