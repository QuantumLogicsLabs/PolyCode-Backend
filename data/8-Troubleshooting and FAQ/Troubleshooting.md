# Troubleshooting Guide

Common problems and solutions for batch scripting issues.

## Table of Contents

1. [Syntax Errors](#syntax-errors)
2. [Variable Problems](#variable-problems)
3. [File Operation Issues](#file-operation-issues)
4. [Loop Problems](#loop-problems)
5. [Conditional Statement Issues](#conditional-statement-issues)
6. [Performance Issues](#performance-issues)
7. [Environment Problems](#environment-problems)
8. [Debugging Techniques](#debugging-techniques)

---

## Syntax Errors

### Problem: "was unexpected at this time"

**Cause**: Special characters not escaped or quotes mismatched

**❌ Error:**
```batch
echo Hello & Welcome
set "path=C:\Program Files\App"
```

**✅ Solution:**
```batch
echo Hello ^& Welcome
set "path=C:\Program Files\App"
```

**Special characters to escape with `^`**:
- `&` → `^&` (ampersand)
- `|` → `^|` (pipe)
- `<` → `^<` (less than)
- `>` → `^>` (greater than)
- `%` → `%%` (percent in batch files)
- `^` → `^^` (caret itself)

### Problem: Missing Parentheses

**❌ Error:**
```batch
if exist file.txt (
    echo Found
rem Missing closing parenthesis
```

**✅ Solution:**
```batch
if exist file.txt (
    echo Found
)
```

**Tip**: Always match your opening `(` with closing `)`

### Problem: Incorrect FOR Loop Syntax

**❌ Error:**
```batch
for %i in (*.txt) do echo %i
```

**✅ Solution:**
```batch
for %%i in (*.txt) do echo %%i
```

**Rule**: Use `%%` in batch files, `%` only in command line

---

## Variable Problems

### Problem: Variable Not Expanding

**Symptom**: `%myvar%` shows as literal text

**Cause 1**: Variable not set

**✅ Solution:**
```batch
set "myvar=hello"
echo %myvar%
```

**Cause 2**: Inside a code block without delayed expansion

**❌ Error:**
```batch
setlocal
set "counter=0"
for /l %%i in (1,1,5) do (
    set /a counter+=1
    echo Counter is %counter%
)
```

**✅ Solution:**
```batch
setlocal enabledelayedexpansion
set "counter=0"
for /l %%i in (1,1,5) do (
    set /a counter+=1
    echo Counter is !counter!
)
```

**Key Point**: Use `!var!` instead of `%var%` when using delayed expansion inside loops

### Problem: Spaces in Variable Assignment

**❌ Error:**
```batch
set myvar = hello
echo [%myvar%]
REM Output: [ hello ]
```

**✅ Solution:**
```batch
set "myvar=hello"
echo [%myvar%]
REM Output: [hello]
```

**Rule**: No spaces around `=` sign

### Problem: Special Characters in Variables

**❌ Error:**
```batch
set path=C:\Folder&Another
```

**✅ Solution:**
```batch
set "path=C:\Folder&Another"
```

**Always quote variable assignments with special characters!**

### Problem: Arithmetic Not Working

**❌ Error:**
```batch
set x=5
set y=10
set z=%x%+%y%
echo %z%
REM Output: 5+10
```

**✅ Solution:**
```batch
set x=5
set y=10
set /a z=x+y
echo %z%
REM Output: 15
```

**Use `SET /A` for math operations**

---

## File Operation Issues

### Problem: File Not Found When It Exists

**Cause 1**: Path has spaces, not quoted

**❌ Error:**
```batch
if exist C:\My Documents\file.txt (
    echo Found
)
```

**✅ Solution:**
```batch
if exist "C:\My Documents\file.txt" (
    echo Found
)
```

**Cause 2**: Using forward slashes instead of backslashes

**❌ Error:**
```batch
copy C:/Users/file.txt D:/Backup/
```

**✅ Solution:**
```batch
copy "C:\Users\file.txt" "D:\Backup\"
```

### Problem: Copy Fails Silently

**❌ Issue:**
```batch
copy source.txt dest.txt
if errorlevel 1 echo Failed
```

**✅ Better:**
```batch
copy "source.txt" "dest.txt" > nul
if errorlevel 1 (
    echo ERROR: Copy failed with error level %errorlevel%
    exit /b 1
)
```

**Tips**:
- Always check errorlevel after file operations
- Redirect output to see errors: `2>&1`
- Use `/Y` flag to suppress overwrite prompts

### Problem: DEL Doesn't Delete Read-Only Files

**❌ Issue:**
```batch
del file.txt
REM Fails if file is read-only
```

**✅ Solution:**
```batch
attrib -r file.txt
del file.txt
```

Or use:
```batch
del /f file.txt
```

### Problem: FOR Loop Can't Find Files

**Issue**: Wildcard not working

**✅ Check current directory first:**
```batch
cd /d "C:\Target\Folder"
for %%f in (*.txt) do (
    echo Processing %%f
)
```

**Or use full path:**
```batch
for %%f in ("C:\Target\Folder\*.txt") do (
    echo Processing %%~nxf
)
```

---

## Loop Problems

### Problem: FOR Loop Only Processes First Item

**❌ Issue:**
```batch
for %%f in (*.txt) do (
    echo %%f
    goto end
)
:end
```

**✅ Solution:**
```batch
for %%f in (*.txt) do (
    echo %%f
)
```

**Don't use GOTO inside loops unless necessary**

### Problem: Nested Loop Variables

**❌ Error:**
```batch
for %%i in (1,2,3) do (
    for %%i in (a,b,c) do (
        echo %%i
    )
)
```

**✅ Solution:**
```batch
for %%i in (1,2,3) do (
    for %%j in (a,b,c) do (
        echo Outer: %%i, Inner: %%j
    )
)
```

**Use different variable names for nested loops**

### Problem: FOR /F Not Reading File Correctly

**Issue**: delims option causing problems

**✅ Explicit delimiter specification:**
```batch
for /f "delims=" %%line in (file.txt) do (
    echo Full line: %%line
)
```

**Common delims settings:**
- `"delims="` - No delimiters (entire line)
- `"delims= "` - Space delimiter
- `"delims=\t"` - Tab delimiter
- `"tokens=1,2"` - Get first two tokens

### Problem: Loop Counter Not Incrementing

**❌ Error:**
```batch
set i=0
:loop
set /a i+=1
echo %i%
if %i% lss 10 goto loop
```

**Problem**: `%i%` doesn't update in the IF statement

**✅ Solution 1 (Delayed Expansion):**
```batch
setlocal enabledelayedexpansion
set i=0
:loop
set /a i+=1
echo !i!
if !i! lss 10 goto loop
```

**✅ Solution 2 (FOR /L):**
```batch
for /l %%i in (1,1,9) do (
    echo %%i
)
```

---

## Conditional Statement Issues

### Problem: IF Comparison Not Working

**❌ Error:**
```batch
set var=hello
if %var%==hello echo Match
```

**Problem**: Fails if var is empty or has spaces

**✅ Solution:**
```batch
set "var=hello"
if "%var%"=="hello" echo Match
```

**ALWAYS quote both sides of comparison**

### Problem: Comparing Numbers

**❌ Error:**
```batch
set a=5
set b=10
if %a% > %b% echo Greater
```

**Problem**: `>` is redirection, not comparison

**✅ Solution:**
```batch
set a=5
set b=10
if %a% gtr %b% echo Greater
```

**Numeric comparison operators:**
- `EQU` - Equal
- `NEQ` - Not equal
- `LSS` - Less than
- `LEQ` - Less than or equal
- `GTR` - Greater than
- `GEQ` - Greater than or equal

### Problem: Multiple Conditions

**❌ Error:**
```batch
if %a%==1 if %b%==2 echo Both true
```

**✅ Better:**
```batch
if "%a%"=="1" (
    if "%b%"=="2" (
        echo Both true
    )
)
```

**Or use:**
```batch
if "%a%"=="1" if "%b%"=="2" (
    echo Both true
)
```

### Problem: ELSE on Same Line

**❌ Error:**
```batch
if exist file.txt echo Found else echo Not found
```

**✅ Solution:**
```batch
if exist file.txt (
    echo Found
) else (
    echo Not found
)
```

**CRITICAL**: No space before `else`, and must be on same line as closing parenthesis

---

## Performance Issues

### Problem: Script Runs Very Slowly

**Cause 1**: Excessive disk I/O

**❌ Slow:**
```batch
for %%f in (*.txt) do (
    type "%%f"
    echo.
)
```

**✅ Faster:**
```batch
(
    for %%f in (*.txt) do (
        type "%%f"
        echo.
    )
) > output.txt
```

**Cause 2**: Unnecessary command echoing

**✅ Solution:**
```batch
@echo off
```

**Cause 3**: Too many external program calls

**Minimize calls to external programs within loops**

### Problem: Memory Leak

**Cause**: Not cleaning up SETLOCAL

**❌ Bad:**
```batch
:loop
setlocal
set var=value
REM Missing ENDLOCAL
goto loop
```

**✅ Good:**
```batch
:loop
setlocal
set var=value
REM Your code here
endlocal
goto loop
```

**Or better yet:**
```batch
setlocal
:loop
set var=value
REM Your code here
if condition goto loop
endlocal
```

---

## Environment Problems

### Problem: Script Works Interactively But Not Scheduled

**Cause**: Different environment variables in Task Scheduler

**✅ Solution:**
```batch
@echo off
REM Set required environment variables
set "PATH=%PATH%;C:\Required\Path"
set "TEMP=%TEMP%"

REM Use full paths
"C:\Full\Path\To\Program.exe"
```

**Also in Task Scheduler:**
- ✓ "Run with highest privileges"
- ✓ Set "Start in" folder
- ✓ Configure for correct Windows version

### Problem: Different Behavior on Different Computers

**Causes**:
1. Different Windows versions
2. Different PATH variables
3. Command extensions disabled

**✅ Solutions:**

1. **Check Windows version:**
```batch
ver | findstr /i "Windows 10" > nul
if errorlevel 1 (
    echo Warning: Not Windows 10
)
```

2. **Enable command extensions explicitly:**
```batch
cmd /e:on /c yourscript.bat
```

3. **Use full paths for all external commands**

### Problem: Unicode/Special Character Issues

**Issue**: Non-ASCII characters display incorrectly

**✅ Solution:**
```batch
chcp 65001 > nul
echo UTF-8 text: café, naïve, résumé
```

**Add to script start for UTF-8 support**

---

## Debugging Techniques

### Technique 1: Enable Echo Temporarily

```batch
@echo off
set "DEBUG=1"

if "%DEBUG%"=="1" @echo on

REM Your code here - will show all commands

if "%DEBUG%"=="1" @echo off
```

### Technique 2: Step-by-Step Execution

```batch
@echo off
pause
REM Each pause lets you see what happened
```

### Technique 3: Log Everything

```batch
@echo off
set "logfile=%~dp0debug.log"

REM Redirect all output to log
call :MainLogic > "%logfile%" 2>&1
goto :EOF

:MainLogic
echo Starting at %date% %time%
echo Current directory: %cd%
echo Variables:
set
echo.
echo Running tests...
REM Your code here
exit /b 0
```

### Technique 4: Check Variable Values

```batch
@echo off
set "testvar=somevalue"

REM Method 1: Echo it
echo testvar = [%testvar%]

REM Method 2: List all vars with name
set testvar

REM Method 3: For delayed expansion
setlocal enabledelayedexpansion
echo testvar = [!testvar!]
endlocal
```

### Technique 5: Isolate the Problem

Create a minimal test case:

```batch
@echo off
REM Test just the problematic section
set "problematic_var=test"
echo Testing: %problematic_var%

REM Add complexity gradually
```

### Technique 6: Use Built-in Help

```cmd
command /?
```

Examples:
```cmd
for /?
if /?
set /?
```

### Technique 7: Test in Parts

Break script into sections and test each:

```batch
@echo off
echo === Testing Section 1 ===
call :Section1
echo.

echo === Testing Section 2 ===
call :Section2
echo.

goto :EOF

:Section1
REM Code for section 1
exit /b 0

:Section2
REM Code for section 2
exit /b 0
```

---

## Quick Reference: Common Error Messages

| Error Message | Cause | Solution |
|---------------|-------|----------|
| `was unexpected at this time` | Syntax error | Check special chars, quotes |
| `The system cannot find the file specified` | File not found | Check path, use quotes |
| `Access is denied` | Permission issue | Run as Administrator |
| `Invalid drive specification` | Bad drive letter | Check drive exists |
| `Duplicate label` | Same label twice | Rename one label |
| `Missing operand` | Incomplete command | Add missing parameter |

---

## Getting More Help

### Built-in Resources
- `command /?` - Help for any command
- Event Viewer - System logs
- Task Scheduler History - For scheduled tasks

### Online Resources
- [Microsoft Docs](https://docs.microsoft.com/en-us/windows-server/administration/windows-commands/)
- [Stack Overflow - batch-file tag](https://stackoverflow.com/questions/tagged/batch-file)
- [SS64 Batch Reference](https://ss64.com/nt/)

### Community Help

When asking for help, always include:
1. Your code (use code blocks)
2. The exact error message
3. What you expected to happen
4. What actually happened
5. Windows version
6. What you've already tried

---

## Prevention Tips

✅ **Always**:
- Quote paths and variables
- Check errorlevel after critical operations
- Test with edge cases
- Use `@echo off`
- Comment your code
- Validate inputs

✅ **Before deploying**:
- Test on clean system
- Run as different user
- Check with UAC on/off
- Verify on target Windows version

✅ **When stuck**:
- Take a break
- Explain problem to someone (rubber duck debugging)
- Search online - others had same issue
- Try simpler approach

---

**Remember**: Every error is a learning opportunity! Keep this guide handy and refer to it when problems arise.

For more help, check:
- [Best Practices](07_Best_Practices.md)
- [Examples](../05_Examples/)
- [Command Reference](../04_Reference/)
