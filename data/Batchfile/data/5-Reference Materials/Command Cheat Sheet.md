# Batch Command Cheat Sheet

Quick reference for the most commonly used batch commands. Print this page or keep it open while scripting!

## Quick Navigation

- [Basic Commands](#basic-commands)
- [File Operations](#file-operations)
- [Directory Operations](#directory-operations)
- [Control Flow](#control-flow)
- [Variables & Data](#variables--data)
- [String Manipulation](#string-manipulation)
- [Input/Output](#inputoutput)
- [System Commands](#system-commands)
- [Network Commands](#network-commands)
- [Special Characters](#special-characters)

---

## Basic Commands

| Command | Syntax | Example | Description |
|---------|--------|---------|-------------|
| **ECHO** | `ECHO [message]` | `echo Hello World` | Display message |
| | `ECHO off/on` | `@echo off` | Toggle command display |
| | `ECHO.` | `echo.` | Print blank line |
| **REM** | `REM [comment]` | `rem This is a comment` | Add comment |
| **PAUSE** | `PAUSE` | `pause` | Wait for keypress |
| **TIMEOUT** | `TIMEOUT /t seconds [/nobreak]` | `timeout /t 5 /nobreak` | Wait specified time |
| **CLS** | `CLS` | `cls` | Clear screen |
| **EXIT** | `EXIT [/b [exitcode]]` | `exit /b 1` | Exit script or return from subroutine |
| **CALL** | `CALL [file.bat] [label]` | `call otherscript.bat` | Call another script or subroutine |

---

## File Operations

| Command | Syntax | Example | Description |
|---------|--------|---------|-------------|
| **COPY** | `COPY source destination` | `copy file.txt backup\` | Copy files |
| | `COPY /Y` | `copy /Y file.txt dest\` | Overwrite without prompt |
| **XCOPY** | `XCOPY source dest [options]` | `xcopy src\ dest\ /E /I /Y` | Extended copy |
| | `/E` | | Copy subdirectories (including empty) |
| | `/I` | | Assume destination is directory |
| | `/Y` | | Suppress overwrite prompt |
| | `/R` | | Overwrite read-only files |
| | `/H` | | Copy hidden and system files |
| **ROBOCOPY** | `ROBOCOPY source dest [files] [opts]` | `robocopy src dest *.txt /E` | Robust file copy |
| | `/E` | | Copy subdirectories |
| | `/Z` | | Restartable mode |
| | `/R:n` | `/R:3` | Retry count (default 1M) |
| | `/W:n` | `/W:5` | Wait time between retries |
| **MOVE** | `MOVE source destination` | `move file.txt folder\` | Move files |
| **DEL** | `DEL [path] [options]` | `del /q/f/s *.tmp` | Delete files |
| | `/F` | | Force delete read-only |
| | `/Q` | | Quiet mode (no confirm) |
| | `/S` | | Delete from all subdirs |
| | `/A` | | Select by attribute |
| **REN** | `REN oldname newname` | `ren file.txt newfile.txt` | Rename file |
| **TYPE** | `TYPE [file]` | `type readme.txt` | Display file contents |
| **ATTRIB** | `ATTRIB [+/-attributes] [file]` | `attrib +r file.txt` | Change file attributes |
| | `+r/-r` | | Set/clear read-only |
| | `+h/-h` | | Set/clear hidden |
| | `+s/-s` | | Set/clear system |
| | `+a/-a` | | Set/clear archive |

---

## Directory Operations

| Command | Syntax | Example | Description |
|---------|--------|---------|-------------|
| **CD** | `CD [path]` | `cd C:\Projects` | Change directory |
| | `CD ..` | `cd ..` | Go to parent directory |
| | `CD \` | `cd \` | Go to root |
| | `CD /d drive:path` | `cd /d D:\Docs` | Change drive and directory |
| **MD** | `MD dirname` | `mkdir NewFolder` | Create directory |
| **RD** | `RD dirname` | `rmdir OldFolder` | Remove directory |
| | `/S` | `rd /s /q folder` | Remove tree |
| | `/Q` | | Quiet mode |
| **DIR** | `DIR [path] [options]` | `dir /b /a-d` | List directory |
| | `/B` | `dir /b` | Bare format |
| | `/A` | `dir /a` | Show all files |
| | `/AD` | `dir /ad` | Directories only |
| | `/OD` | `dir /od` | Sort by date |
| | `/S` | `dir /s` | Include subdirs |

---

## Control Flow

### IF Statements

```batch
IF condition command
IF NOT condition command
IF condition (
    command1
    command2
)
```

| Condition | Example | Description |
|-----------|---------|-------------|
| **ERRORLEVEL** | `if errorlevel 1 echo Error` | Check error level |
| **EXIST** | `if exist file.txt echo Found` | File/directory exists |
| **String Compare** | `if "%var%"=="hello" echo Match` | Compare strings |
| **Numeric Compare** | `if %num% equ 5 echo Five` | Compare numbers |
| **DEFINED** | `if defined var echo Set` | Variable is defined |

### Numeric Operators

| Operator | Meaning | Example |
|----------|---------|---------|
| `EQU` | Equal | `if %a% equ %b%` |
| `NEQ` | Not equal | `if %a% neq %b%` |
| `LSS` | Less than | `if %a% lss %b%` |
| `LEQ` | Less than or equal | `if %a% leq %b%` |
| `GTR` | Greater than | `if %a% gtr %b%` |
| `GEQ` | Greater than or equal | `if %a% geq %b%` |

### FOR Loops

#### Basic FOR Loop
```batch
FOR %%variable IN (set) DO command
```

**Examples:**
```batch
for %%i in (1,2,3) do echo %%i
for %%f in (*.txt) do echo %%f
for /l %%i in (1,1,10) do echo %%i
```

#### FOR /L - Numeric Loop
```batch
FOR /L %%variable IN (start,step,end) DO command
```

**Examples:**
```batch
for /l %%i in (1,1,5) do echo %%i        REM 1 to 5
for /l %%i in (0,2,10) do echo %%i       REM 0,2,4,6,8,10
for /l %%i in (10,-1,1) do echo %%i      REM 10 down to 1
```

#### FOR /F - File/Text Parsing
```batch
FOR /F ["options"] %%variable IN (file) DO command
FOR /F ["options"] %%variable IN ("string") DO command
FOR /F ["options"] %%variable IN ('command') DO command
```

**Options:**
- `delims=xyz` - Delimiters to use (default: space/tab)
- `tokens=1,2,3` - Which tokens to extract
- `skip=n` - Skip n lines at start
- `eol=x` - End-of-line character

**Examples:**
```batch
for /f "delims=" %%line in (file.txt) do echo %%line
for /f "tokens=1,2 delims=," %%a in (data.csv) do echo %%a %%b
for /f "skip=2 tokens=*" %%l in ('dir /b') do echo %%l
```

#### FOR /D - Directory Loop
```batch
FOR /D %%variable IN (path) DO command
```

**Example:**
```batch
for /d %%d in (C:\Folders\*) do echo %%d
```

#### FOR Meta-Variable Modifiers

| Modifier | Result | Example with `%%~fi` |
|----------|--------|---------------------|
| `%%~fI` | Fully qualified path | `C:\folder\file.txt` |
| `%%~dI` | Drive letter | `C:` |
| `%%~pI` | Path only | `\folder\` |
| `%%~nI` | Filename only | `file` |
| `%%~xI` | Extension only | `.txt` |
| `%%~sI` | Short 8.3 name | `FILE~1.TXT` |
| `%%~aI` | File attributes | `-A------` |
| `%%~tI` | Date/time | `2024-01-15 12:30` |
| `%%~zI` | File size | `1024` |
| `%%~$PATH:I` | Search PATH | Full path if found |

**Combined:**
```batch
%%~dpnfI  = Drive + Path + Name (full path without extension)
%%~nxfI   = Name + Extension (filename.ext)
```

---

## Variables & Data

### SET Command

| Operation | Syntax | Example |
|-----------|--------|---------|
| **Set variable** | `SET var=value` | `set name=John` |
| **Set with spaces** | `SET "var=value"` | `set "path=C:\Program Files"` |
| **Display variables** | `SET` | `set` |
| **Display specific** | `SET var` | `set myvar` |
| **Arithmetic** | `SET /A expression` | `set /a result=5+3` |
| **User input** | `SET /P var=prompt` | `set /p name=Enter name: ` |
| **Environment var** | `SETX var value` | `setx MYVAR hello` |

### Arithmetic Operations

| Operator | Example | Result |
|----------|---------|--------|
| `+` | `set /a r=5+3` | 8 |
| `-` | `set /a r=5-3` | 2 |
| `*` | `set /a r=5*3` | 15 |
| `/` | `set /a r=5/3` | 1 |
| `%%` | `set /a r=5%%3` | 2 |
| `^` | `set /a r=2^3` | 8 (exponentiation) |
| `+=` | `set /a x+=5` | Add 5 to x |
| `-=` | `set /a x-=5` | Subtract 5 from x |

### Special Variables

| Variable | Value | Example |
|----------|-------|---------|
| `%0` | Batch file name | `%0` |
| `%1-%9` | Command parameters | `%1` |
| `%*` | All parameters | `%*` |
| `%CD%` | Current directory | `%CD%` |
| `%DATE%` | Current date | `%DATE%` |
| `%TIME%` | Current time | `%TIME%` |
| `%RANDOM%` | Random number | `%RANDOM%` |
| `%ERRORLEVEL%` | Last error code | `%ERRORLEVEL%` |
| `%CMDCMDLINE%` | Original command line | `%CMDCMDLINE%` |

### Environment Variables

| Variable | Description |
|----------|-------------|
| `%PATH%` | Executable search path |
| `%TEMP%` / `%TMP%` | Temporary files folder |
| `%USERPROFILE%` | User's home directory |
| `%HOMEDRIVE%` / `%HOMEPATH%` | Home drive and path |
| `%APPDATA%` | Application data |
| `%LOCALAPPDATA%` | Local application data |
| `%COMPUTERNAME%` | Computer name |
| `%USERNAME%` | Current user |
| `%OS%` | Operating system |
| `%PROCESSOR_ARCHITECTURE%` | CPU architecture |

---

## String Manipulation

### Substring Extraction
```batch
%variable:~start,length%
%variable:~start%        REM From start to end
%variable:~-length%      REM Last n characters
```

**Examples:**
```batch
set text=Hello World
echo %text:~0,5%     REM Output: Hello
echo %text:~6%       REM Output: World
echo %text:~-5%      REM Output: World
```

### String Replacement
```batch
%variable:old=new%           REM Replace first occurrence
%variable:old=new%           REM Replace all occurrences
%variable:*old=new%          REM Remove prefix up to and including old
```

**Examples:**
```batch
set text=Hello World Hello
echo %text:Hello=Hi%              REM Output: Hi World Hello
echo %text:Hello=Hi%              REM Output: Hi World Hi (all with extra %)
echo %text:*World=%               REM Output:  (everything after World)
```

### Case Conversion (CMD Extensions)
```batch
%variable:A=a%    REM Replace A with a (repeat for all letters)
```

---

## Input/Output

### Redirection Operators

| Operator | Description | Example |
|----------|-------------|---------|
| `>` | Redirect output (overwrite) | `echo test > file.txt` |
| `>>` | Redirect output (append) | `echo test >> file.txt` |
| `<` | Redirect input | `sort < input.txt` |
| `2>&1` | Redirect stderr to stdout | `command > log.txt 2>&1` |
| `>nul` | Suppress output | `echo test >nul` |

### Standard Streams

| Stream | Handle | Description |
|--------|--------|-------------|
| stdin | 0 | Input |
| stdout | 1 | Normal output |
| stderr | 2 | Error output |

**Examples:**
```batch
command > output.txt 2>&1    REM All output to file
command > nul 2>&1           REM Suppress all output
command < input.txt          Read from file
```

---

## System Commands

| Command | Syntax | Example | Description |
|---------|--------|---------|-------------|
| **VER** | `VER` | `ver` | Show Windows version |
| **SYSTEMINFO** | `SYSTEMINFO` | `systeminfo` | Detailed system info |
| **HOSTNAME** | `HOSTNAME` | `hostname` | Show computer name |
| **WHOAMI** | `WHOAMI` | `whoami` | Show current user |
| **TASKLIST** | `TASKLIST` | `tasklist` | List running processes |
| **TASKKILL** | `TASKKILL /IM process` | `taskkill /IM notepad.exe` | Kill process |
| **SC** | `SC query servicename` | `sc query spooler` | Service control |
| **NET** | `NET USER` | `net user` | Network commands |
| **REG** | `REG QUERY key` | `reg query HKLM\Software` | Registry operations |
| **WMIC** | `WMIC class get property` | `wmic cpu get name` | WMI queries |

---

## Network Commands

| Command | Syntax | Example | Description |
|---------|--------|---------|-------------|
| **PING** | `PING [-n count] host` | `ping -n 4 google.com` | Test connectivity |
| **IPCONFIG** | `IPCONFIG [/all]` | `ipconfig /all` | Show IP config |
| **TRACERT** | `TRACERT host` | `tracert google.com` | Trace route |
| **NETSTAT** | `NETSTAT [-ano]` | `netstat -ano` | Network statistics |
| **NSLOOKUP** | `NSLOOKUP domain` | `nslookup google.com` | DNS lookup |

---

## Special Characters

| Character | Purpose | Escape Method |
|-----------|---------|---------------|
| `&` | Command separator | `^&` |
| `\|` | Pipe | `^\|` |
| `<` | Input redirect | `^<` |
| `>` | Output redirect | `^>` |
| `^` | Escape character | `^^` |
| `%` | Variable marker | `%%` |
| `!` | Delayed expansion | `^!` |
| `"` | String delimiter | `\"` |
| `(` | Begin block | `^(` |
| `)` | End block | `^)` |

**Escaping Examples:**
```batch
echo This ^& That
echo Percent: %%
echo Caret: ^^
echo Exclaim: ^!
```

---

## Common Patterns Quick Reference

### Check if File Exists
```batch
if exist "file.txt" (
    echo File found
) else (
    echo File not found
)
```

### Get User Confirmation
```batch
set /p confirm="Continue? (Y/N): "
if /i "%confirm%"=="Y" (
    echo Proceeding...
)
```

### Create Timestamp
```batch
set timestamp=%date:~-4,4%%date:~-7,2%%date:~-10,2%_%time:~0,2%%time:~3,2%
set timestamp=%timestamp: =0%
set timestamp=%timestamp::=%
```

### Extract File Information
```batch
for %%f in ("C:\Path\file.txt") do (
    echo Drive: %%~df
    echo Path: %%~pf
    echo Name: %%~nf
    echo Extension: %%~xf
    echo Full Path: %%~ff
    echo Size: %%~zf
)
```

### Loop Through Array
```batch
setlocal enabledelayedexpansion
set items=apple banana cherry
for %%i in (%items%) do (
    echo Item: %%i
)
```

### Read File Line by Line
```batch
for /f "delims=" %%line in (file.txt) do (
    echo Line: %%line
)
```

### Wait for File to Exist
```batch
:waitforfile
if exist "file.txt" (
    goto filefound
) else (
    timeout /t 2 >nul
    goto waitforfile
)
:filefound
echo File found!
```

---

## Error Handling Pattern

```batch
@echo off
setlocal

call :MainLogic
if errorlevel 1 (
    echo ERROR: Script failed
    exit /b %errorlevel%
)
goto :EOF

:MainLogic
REM Your code here
if not exist "required.txt" (
    echo ERROR: Required file missing
    exit /b 1
)
exit /b 0
```

---

## Debugging Tips

### Enable Debug Mode
```batch
set DEBUG=1
if "%DEBUG%"=="1" @echo on
```

### Step Through Code
```batch
@echo off
pause
REM Each pause shows progress
```

### Log Everything
```batch
call :Log "INFO" "Message"
goto :EOF

:Log
echo [%date% %time%] [%~1] %~2 >> log.txt
echo [%date% %time%] [%~1] %~2
exit /b 0
```

---

**Print this cheat sheet or bookmark it for quick reference!**

For detailed explanations, see:
- [Command Reference](../04_Reference/)
- [Examples](../05_Examples/)
- [Best Practices](07_Best_Practices.md)
