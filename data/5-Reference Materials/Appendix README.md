# Appendix

Additional resources and reference materials for batch scripting.

## Quick Reference Guides

### Special Variables

| Variable | Description |
|----------|-------------|
| `%0` | Batch file name |
| `%1` to `%9` | Command line parameters |
| `%*` | All command line parameters |
| `%CD%` | Current directory |
| `%DATE%` | Current date |
| `%TIME%` | Current time |
| `%RANDOM%` | Random number (0-32767) |
| `%ERRORLEVEL%` | Last error code |
| `%CMDEXTVERSION%` | Command extensions version |
| `%CMDCMDLINE%` | Original command line |

### Environment Variables

| Variable | Description |
|----------|-------------|
| `%PATH%` | Search path for executables |
| `%TEMP%` / `%TMP%` | Temporary files folder |
| `%USERPROFILE%` | User's home directory |
| `%HOMEDRIVE%` / `%HOMEPATH%` | Home drive and path |
| `%APPDATA%` | Application data folder |
| `%COMPUTERNAME%` | Computer name |
| `%USERNAME%` | Current user name |
| `%OS%` | Operating system |
| `%PROCESSOR_ARCHITECTURE%` | CPU architecture |
| `%NUMBER_OF_PROCESSORS%` | Number of CPUs |

### FOR Loop Meta-Variables

| Modifier | Description | Example | Result |
|----------|-------------|---------|--------|
| `%%~fI` | Fully qualified path | `%%~fI` | C:\folder\file.txt |
| `%%~dI` | Drive letter only | `%%~dI` | C: |
| `%%~pI` | Path only | `%%~pI` | \folder\ |
| `%%~nI` | Filename only | `%%~nI` | file |
| `%%~xI` | Extension only | `%%~xI` | .txt |
| `%%~sI` | Short 8.3 name | `%%~sI` | FILE~1.TXT |
| `%%~aI` | File attributes | `%%~aI` | -A------ |
| `%%~tI` | Date/time | `%%~tI` | 2024-01-01 12:00 |
| `%%~zI` | File size | `%%~zI` | 1024 |
| `%%~$PATH:I` | Search PATH | `%%~$PATH:I` | Full path if found |

### Comparison Operators

| Operator | Meaning | Example |
|----------|---------|---------|
| `EQU` | Equal | `IF %a% EQU %b%` |
| `NEQ` | Not equal | `IF %a% NEQ %b%` |
| `LSS` | Less than | `IF %a% LSS %b%` |
| `LEQ` | Less than or equal | `IF %a% LEQ %b%` |
| `GTR` | Greater than | `IF %a% GTR %b%` |
| `GEQ` | Greater than or equal | `IF %a% GEQ %b%` |

### Arithmetic Operations

```batch
SET /A result=num1 + num2    rem Addition
SET /A result=num1 - num2    rem Subtraction
SET /A result=num1 * num2    rem Multiplication
SET /A result=num1 / num2    rem Division
SET /A result=num1 %% num2   rem Modulo
SET /A result=num1 ^ num2    rem Exponentiation
SET /A result+=num           rem Add and assign
SET /A result-=num           rem Subtract and assign
SET /A result*=num           rem Multiply and assign
SET /A result/=num           rem Divide and assign
SET /A result%%=num          rem Modulo and assign
```

### Bitwise Operations

```batch
SET /A result=num1 & num2    rem AND
SET /A result=num1 | num2    rem OR
SET /A result=num1 ^ num2    rem XOR
SET /A result=~num           rem NOT
SET /A result=num << n       rem Left shift
SET /A result=num >> n       rem Right shift
```

## Common Patterns

### Check if File Exists
```batch
if exist "filename.txt" (
    echo File exists
) else (
    echo File not found
)
```

### Check if Directory Exists
```batch
if exist "C:\Folder\" (
    echo Directory exists
) else (
    echo Directory not found
)
```

### Wait for User Input with Timeout
```batch
set /p "choice=Press any key to continue..."
timeout /t 5 /nobreak >nul
```

### Create Timestamp
```batch
set "timestamp=%date:~-4,4%%date:~-7,2%%date:~-10,2%_%time:~0,2%%time:~3,2%"
echo %timestamp%
rem Output: 20240101_1230
```

### Remove Spaces from String
```batch
set "string=Hello World"
set "string=%string: =%"
echo %string%
rem Output: HelloWorld
```

### Convert to Uppercase
```batch
set "string=hello"
set "string=%string:a=A%"
set "string=%string:b=B%"
rem ... repeat for all letters
```

### Get Parent Directory
```batch
for %%a in ("%cd%\..") do set "parent=%%~fa"
echo %parent%
```

## Debugging Techniques

### Echo Debug Mode
```batch
@echo off
set "debug=1"

if "%debug%"=="1" echo [DEBUG] Starting process...

rem Your code here

if "%debug%"=="1" echo [DEBUG] Process completed
```

### Step-by-Step Execution
```batch
@echo off
pause
rem Each pause lets you step through
```

### Log All Output
```batch
@echo off
call :MainLogic > log.txt 2>&1
goto :EOF

:MainLogic
echo This goes to log.txt
exit /b 0
```

## Best Practices Checklist

- [ ] Use `@echo off` at the start
- [ ] Add comments explaining complex logic
- [ ] Validate all inputs
- [ ] Handle errors gracefully
- [ ] Quote all paths
- [ ] Use meaningful variable names
- [ ] Test with edge cases
- [ ] Clean up temporary files
- [ ] Document return codes
- [ ] Add usage instructions

## Useful External Commands

| Command | Purpose |
|---------|---------|
| `robocopy` | Advanced file copy |
| `xcopy` | Extended file copy |
| `findstr` | Search with regex |
| `where` | Find files in PATH |
| `tasklist` | List running processes |
| `taskkill` | Terminate processes |
| `sc` | Service control |
| `net` | Network operations |
| `reg` | Registry operations |
| `cipher` | Encryption/decryption |

## Resources

### Official Documentation
- [Microsoft Docs - Windows Commands](https://docs.microsoft.com/en-us/windows-server/administration/windows-commands/)
- [CMD.exe Reference](https://ss64.com/nt/)

### Community Resources
- [Stack Overflow - Batch](https://stackoverflow.com/questions/tagged/batch-file)
- [Reddit - r/batchfiles](https://www.reddit.com/r/batchfiles/)

### Tools
- **Notepad++** - Better text editor for batch files
- **VS Code** - With batch language support
- **WinBatch** - Enhanced batch scripting

## Keyboard Shortcuts (Command Prompt)

| Shortcut | Action |
|----------|--------|
| `F7` | Show command history |
| `Alt+F7` | Clear command history |
| `F8` | Search command history |
| `Ctrl+C` | Copy selected text |
| `Ctrl+V` | Paste text |
| `Ctrl+A` | Select all |
| `Tab` | Auto-complete filenames |

## Migration Notes

### From Batch to PowerShell
While batch files are still supported, consider PowerShell for:
- Complex data manipulation
- Object-oriented operations
- Better error handling
- Cross-platform compatibility
- Modern Windows administration

### Compatibility
- Batch files work on all Windows versions
- Some commands vary between Windows versions
- Command extensions are enabled by default on modern Windows
- Test on target Windows versions if deploying widely
