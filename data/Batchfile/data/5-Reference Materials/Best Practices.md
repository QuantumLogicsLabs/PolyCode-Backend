# Best Practices Guide

## Writing Clean and Maintainable Batch Code

Follow these practices to write professional batch scripts.

---

## Code Organization

### Use Comments Liberally
```batch
REM ============================================
REM Section: File Backup Operations
REM Purpose: Safely backup user documents
REM Author: John Doe
REM Date: 2024-01-15
REM ============================================

REM Create backup directory
if not exist "%backupdir%" mkdir "%backupdir%"

REM Copy files with error handling
xcopy "%source%" "%dest%" /E /I /Y > nul
if errorlevel 1 (
    echo ERROR: Backup failed!
    goto errorhandler
)
```

### Group Related Code
```batch
REM --- Initialization Section ---
setlocal enabledelayedexpansion
set "version=1.0"
set "errors=0"

REM --- Configuration Section ---
set "source=C:\Data"
set "dest=D:\Backup"

REM --- Main Processing Section ---
echo Processing...

REM --- Cleanup Section ---
endlocal
```

---

## Naming Conventions

### Variables
```batch
REM ✅ Good names
set "username=John"
set "filecount=0"
set "backupfolder=C:\Backup"

REM ❌ Bad names
set "x=John"
set "var1=0"
set "f=C:\Backup"
```

### Labels
```batch
REM ✅ Clear labels
:startmenu
:processfiles
:errorhandler
:cleanup

REM ❌ Unclear labels
:l1
:a
:xyz
```

---

## Error Handling

### Always Check Critical Operations
```batch
copy "important.txt" "backup\" > nul 2>&1
if errorlevel 1 (
    echo ERROR: Copy failed!
    exit /b 1
)
```

### Validate Input
```batch
if "%1"=="" (
    echo ERROR: No parameter provided!
    exit /b 1
)

if not exist "%filepath%" (
    echo ERROR: File not found: %filepath%
    exit /b 1
)
```

### Use Error Counter
```batch
set "errorcount=0"

REM Operation 1
copy file1.txt dest\ > nul 2>&1
if errorlevel 1 set /a errorcount+=1

REM Operation 2
copy file2.txt dest\ > nul 2>&1
if errorlevel 1 set /a errorcount+=1

if %errorcount% gtr 0 (
    echo Completed with %errorcount% errors
)
```

---

## Performance Tips

### Use @echo off
```batch
@echo off
REM Prevents command echoing
```

### Redirect Unnecessary Output
```batch
xcopy source dest /Y > nul 2>&1
mkdir folder > nul 2>&1
```

### Minimize Disk Access
```batch
REM ✅ Efficient
set "count=0"
for %%f in (*.txt) do set /a count+=1
echo Total: %count%

REM ❌ Inefficient - accesses disk each time
for %%f in (*.txt) do dir "%%f"
```

---

## Security Practices

### Quote All Paths
```batch
REM ✅ Safe
copy "%USERPROFILE%\My Documents\file.txt" "%dest%"

REM ❌ Dangerous
copy %USERPROFILE%\My Documents\file.txt %dest%
```

### Validate User Input
```batch
set /p filename=Enter filename: 
REM Remove dangerous characters
set "filename=%filename:<=%"
set "filename=%filename:>=%"
set "filename=%filename:|=%"
```

### Never Store Passwords
```batch
REM ❌ NEVER do this
set password=mysecret123

REM ✅ Use secure alternatives
set /p password=Enter password: 
```

---

## Documentation

### Header Comment
```batch
REM ============================================
REM Script Name: backup.bat
REM Purpose: Automated daily backup
REM Usage: backup.bat [source] [destination]
REM Requirements: Windows 7 or higher
REM Author: Your Name
REM Version: 1.0
REM Last Modified: 2024-01-15
REM ============================================
```

### Inline Documentation
```batch
REM Calculate days since last backup
set /a days=(%currentdate%-%lastdate%)/86400

REM Exit if no files to process
if !filecount! equ 0 goto nofiles
```

---

## Testing

### Test Edge Cases
```batch
REM Test empty folder
REM Test missing file
REM Test invalid input
REM Test admin rights needed
```

### Add Debug Mode
```batch
set "debug=1"

if %debug% equ 1 (
    echo DEBUG: Processing %filename%
    echo DEBUG: Source=%source%
    echo DEBUG: Dest=%dest%
)
```

---

## Maintenance

### Version Control
```batch
REM Version 1.0 - Initial release
REM Version 1.1 - Added error handling
REM Version 1.2 - Fixed path issue
set "version=1.2"
```

### Change Log
```batch
REM CHANGELOG:
REM 2024-01-15 - Added logging feature
REM 2024-01-10 - Improved error messages
REM 2024-01-05 - Initial version
```

---

## Common Mistakes to Avoid

### ❌ Don't Hardcode Paths
```batch
REM Bad
copy file.txt C:\Users\John\Documents\

REM Good
copy file.txt "%USERPROFILE%\Documents\"
```

### ❌ Don't Ignore Errors
```batch
REM Bad
copy file.txt dest\
echo Done

REM Good
copy file.txt dest\ > nul 2>&1
if errorlevel 1 (
    echo ERROR: Copy failed!
    exit /b 1
)
```

### ❌ Don't Use Goto Excessively
```batch
REM Bad - Spaghetti code
:start
goto step1
:step1
goto step2
:step2
goto end

REM Good - Structured code
call :step1
call :step2
goto end
```

---

## Checklist Before Sharing

- [ ] All paths are quoted
- [ ] Error handling added
- [ ] Comments explain complex logic
- [ ] Variable names are clear
- [ ] No hardcoded passwords
- [ ] Tested on clean system
- [ ] Version number updated
- [ ] Help text included

---

Following these practices makes your scripts professional and maintainable! 👍
