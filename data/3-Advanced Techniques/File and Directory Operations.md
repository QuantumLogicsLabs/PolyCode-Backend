# 09 — File & Directory Operations

> **Part of:** Batch File Language Documentation Series  
> **Back to:** `00_Overview.md`

---

## Overview

File and directory manipulation is one of the primary uses of batch scripting. This section covers all standard operations: creating, copying, moving, deleting, listing, and querying files and folders.

---

## Directory Navigation

### CD / CHDIR — Change directory

```bat
CD C:\Users\Alice
CD ..                   REM Go up one level
CD ..\..                REM Go up two levels
CD /D D:\Projects       REM Change drive AND directory
```

### PUSHD / POPD — Save and restore directory

```bat
PUSHD "C:\some\path"
REM Do work here
POPD
REM Back to original directory
```

Very useful in subroutines to temporarily work in another directory without losing your place.

---

## Listing Directory Contents

### DIR

```bat
DIR                         REM Current directory
DIR "C:\Projects"
DIR /B                      REM Bare names only
DIR /B /S                   REM Recursive bare names
DIR /A:D                    REM Directories only
DIR /A:H                    REM Hidden files
DIR /A:-D                   REM Files only (not directories)
DIR /O:N                    REM Sort by name
DIR /O:D                    REM Sort by date
DIR /O:S                    REM Sort by size
DIR /O:-S                   REM Sort by size, descending
DIR *.txt                   REM Only .txt files
DIR /W                      REM Wide listing format
```

### Count files in a directory

```bat
DIR /B *.txt | FIND /C /V ""
```

### List subdirectories only

```bat
DIR /B /A:D
```

---

## Creating Directories

### MKDIR / MD

```bat
MKDIR "C:\NewFolder"
MKDIR "C:\path\to\deep\nested\folder"   REM Creates entire path
```

### Create only if it doesn't exist

```bat
IF NOT EXIST "C:\logs" MKDIR "C:\logs"
```

---

## Removing Directories

### RMDIR / RD

```bat
RMDIR "EmptyFolder"                     REM Only works if empty
RMDIR /S "FolderWithContent"            REM Prompts for confirmation
RMDIR /S /Q "FolderWithContent"         REM Silent recursive delete
```

---

## Copying Files

### COPY

```bat
COPY source.txt dest.txt
COPY "C:\source.txt" "D:\backup\"
COPY /Y source.txt dest.txt     REM Overwrite without prompt
COPY *.txt "D:\backup\"         REM Copy all .txt files
```

Combine files:

```bat
COPY file1.txt + file2.txt combined.txt
```

### XCOPY — Extended Copy

```bat
XCOPY "C:\src" "D:\dst" /E /I /Y
```

| Flag | Description |
|---|---|
| `/E` | Copy directories including empty ones |
| `/S` | Subdirectories, exclude empty ones |
| `/I` | If destination doesn't exist, assume it's a directory |
| `/Y` | Suppress overwrite prompt |
| `/D` | Copy only newer files |
| `/H` | Include hidden/system files |
| `/R` | Overwrite read-only files |
| `/C` | Continue even if errors occur |
| `/Q` | Quiet mode (suppress filenames) |
| `/EXCLUDE:list.txt` | Exclude files matching patterns in list |

### ROBOCOPY — Robust Copy (Preferred for Production)

```bat
ROBOCOPY "C:\src" "D:\dst" /E /Z /LOG:"copy.log"
```

| Flag | Description |
|---|---|
| `/E` | Copy subdirectories including empty |
| `/MIR` | Mirror (delete destination files not in source) |
| `/Z` | Restartable mode (resume interrupted copies) |
| `/B` | Backup mode |
| `/R:3` | Retry 3 times on failure (default 1M) |
| `/W:5` | Wait 5 seconds between retries |
| `/LOG:file` | Write output to log file |
| `/NP` | No progress indicator |
| `/NFL` | No file list in output |
| `/NDL` | No directory list in output |
| `/XF *.tmp` | Exclude files matching pattern |
| `/XD temp` | Exclude directory named "temp" |

---

## Moving and Renaming

### MOVE

```bat
MOVE source.txt destination.txt         REM Rename
MOVE "C:\file.txt" "D:\folder\"        REM Move to another folder
MOVE *.log "C:\archive\"               REM Move all logs
```

### REN / RENAME

```bat
REN oldname.txt newname.txt
REN "*.txt" "*.bak"                     REM Rename all .txt to .bak
```

---

## Deleting Files

### DEL / ERASE

```bat
DEL file.txt
DEL /Q *.tmp                REM Quiet (no per-file confirmation)
DEL /F locked.txt           REM Force delete read-only files
DEL /S /Q "C:\logs\*.log"   REM Recursive delete
DEL /A:H hidden.txt         REM Delete hidden files
```

> ⚠️ `DEL` is permanent — there's no Recycle Bin via command line.

---

## File Attributes

### ATTRIB

```bat
ATTRIB file.txt              REM View attributes
ATTRIB +R file.txt           REM Set read-only
ATTRIB -R file.txt           REM Remove read-only
ATTRIB +H file.txt           REM Set hidden
ATTRIB -H file.txt           REM Remove hidden
ATTRIB +S file.txt           REM Set system
ATTRIB +R +H file.txt        REM Multiple attributes
ATTRIB +R /S /D "C:\folder"  REM Recursive
```

---

## Checking File/Directory Existence

```bat
IF EXIST "file.txt" ECHO File exists
IF NOT EXIST "C:\folder" MKDIR "C:\folder"
IF EXIST "C:\folder\" ECHO Folder exists   REM Trailing backslash = directory
```

---

## Getting File Information

### File size

```bat
FOR %%F IN ("file.txt") DO SET "size=%%~zF"
ECHO File size: %size% bytes
```

### File date/time

```bat
FOR %%F IN ("file.txt") DO SET "fdate=%%~tF"
ECHO Modified: %fdate%
```

### File name components

```bat
FOR %%F IN ("C:\path\to\file.txt") DO (
    ECHO Full path:  %%~fF
    ECHO Drive:      %%~dF
    ECHO Path only:  %%~pF
    ECHO Name only:  %%~nF
    ECHO Extension:  %%~xF
    ECHO Name+Ext:   %%~nxF
)
```

---

## Iterating Over Files

### All .txt files in current directory

```bat
FOR %%F IN (*.txt) DO (
    ECHO Processing: %%F
)
```

### Recursive — all files in subdirectories

```bat
FOR /R "C:\data" %%F IN (*.csv) DO (
    ECHO Found: %%F
)
```

### All subdirectories

```bat
FOR /D %%D IN (*) DO (
    ECHO Directory: %%D
)
```

---

## Working with Temp Files

```bat
SET "tempfile=%TEMP%\mytemp_%RANDOM%.txt"
ECHO Some data > "%tempfile%"
REM ... use the file ...
DEL "%tempfile%"
```

---

## Practical Example — Archive Old Logs

```bat
@ECHO OFF
SETLOCAL

SET "log_dir=C:\app\logs"
SET "archive_dir=C:\app\logs\archive"
SET "days_old=30"

IF NOT EXIST "%archive_dir%" MKDIR "%archive_dir%"

FOR %%F IN ("%log_dir%\*.log") DO (
    REM Check if file is older than 30 days using ROBOCOPY /MAXAGE trick
    ROBOCOPY "%log_dir%" "%archive_dir%" "%%~nxF" /MAXAGE:%days_old% /MOV > NUL
)

ECHO Old logs archived.
ENDLOCAL
```

---

## Practical Example — Batch Rename Files

Add a date prefix to all `.txt` files:

```bat
@ECHO OFF
SETLOCAL ENABLEDELAYEDEXPANSION

SET "prefix=%DATE:~10,4%%DATE:~4,2%%DATE:~7,2%_"

FOR %%F IN (*.txt) DO (
    IF NOT "%%~nF"=="%prefix%%%~nF" (
        REN "%%F" "%prefix%%%F"
    )
)

ECHO Rename complete.
ENDLOCAL
```

---

*Next: [10 — Advanced Topics](10_Advanced_Topics.md)*
