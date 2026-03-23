# Batch Command Reference

Complete reference for Windows batch commands.

## Command Categories

### File Operations
- `COPY` - Copy files
- `DEL` / `ERASE` - Delete files
- `MOVE` - Move files
- `REN` / `RENAME` - Rename files
- `ATTRIB` - Display/change file attributes
- `FC` - Compare files

### Directory Operations
- `CD` / `CHDIR` - Change directory
- `MD` / `MKDIR` - Create directory
- `RD` / `RMDIR` - Remove directory
- `DIR` - List directory contents
- `TREE` - Display directory structure

### System Commands
- `ECHO` - Display messages or toggle echo
- `PAUSE` - Wait for user input
- `TIMEOUT` - Wait for specified time
- `EXIT` - Exit command processor
- `CLS` - Clear screen
- `TITLE` - Set console window title

### Variable and Environment
- `SET` - Set/display variables
- `SETLOCAL` / `ENDLOCAL` - Control variable scope
- `CALL` - Call other batch files or subroutines

### Control Flow
- `IF` - Conditional processing
- `FOR` - Loop through items
- `GOTO` - Jump to label
- `CALL` - Call subroutine
- `EXIT /B` - Return from subroutine

### Input/Output
- `ECHO` - Output messages
- `SET /P` - Get user input
- `>` - Redirect output (overwrite)
- `>>` - Redirect output (append)
- `<` - Redirect input
- `2>&1` - Redirect stderr to stdout

### String Operations
- `%variable:~start,length%` - Substring
- `%variable:old=new%` - Replace
- `%variable:*find=remove%` - Remove prefix/suffix

### Information Commands
- `VER` - Display Windows version
- `DATE` / `TIME` - Display/set date/time
- `VOL` - Display volume label
- `HOSTNAME` - Display computer name
- `WHOAMI` - Display user information

### Advanced Commands
- `FINDSTR` - Search for patterns
- `WHERE` - Locate files
- `SCHTASKS` - Schedule tasks
- `REG` - Registry operations
- `WMIC` - WMI operations
- `POWERSHELL` - Run PowerShell commands

## Command Syntax Quick Reference

### IF Statement
```batch
IF [NOT] condition command
IF [NOT] EXIST "path" command
IF [NOT] ERRORLEVEL number command
IF [NOT] "string1"=="string2" command
IF [NOT] DEFINED variable command
```

### FOR Loop Variants
```batch
FOR %%variable IN (set) DO command
FOR /D %%variable IN (set) DO command
FOR /R [[drive:]path] %%variable IN (set) DO command
FOR /F ["options"] %%variable IN (file) DO command
FOR /L %%variable IN (start,step,end) DO command
```

### Common Options
```batch
/E:ON  - Enable extensions (default)
/E:OFF - Disable extensions
/V:ON  - Enable delayed expansion
/V:OFF - Disable delayed expansion
```

## Error Levels

Common error codes:
- `0` - Success
- `1` - General error
- `2` - File not found
- `3` - Path not found
- `5` - Access denied
- `9009` - Command not found

## Special Characters

| Character | Description | Escape Method |
|-----------|-------------|---------------|
| `&` | Command separator | `^&` |
| `|` | Pipe | `^|` |
| `<` | Input redirect | `^<` |
| `>` | Output redirect | `^>` |
| `^` | Escape character | `^^` |
| `%` | Variable marker | `%%` |
| `!` | Delayed expansion | `^!` |
| `"` | String delimiter | `\"` |

## Tips

1. Use `command /?` to get built-in help for any command
2. Test commands interactively before adding to scripts
3. Quote paths that contain spaces
4. Use full command names for clarity (e.g., `MKDIR` instead of `MD`)

## Related Resources

- [Basics](../01_Basics/) - Fundamental concepts
- [Intermediate](../02_Intermediate/) - Usage examples
- [Advanced](../03_Advanced/) - Complex scenarios
- [Examples](../05_Examples/) - Practical applications

## New Example Files

- [Command Usage Examples](Commands/Command Usage Examples.md)
- [Quick Command Examples](Quick Reference/Quick Command Examples.md)
- [Batch Syntax Examples](Syntax Guide/Batch Syntax Examples.md)
- [File and Directory Examples](Examples/File and Directory Examples.bat)
- [Control Flow Examples](Examples/Control Flow Examples.bat)
- [Variables and Strings Examples](Examples/Variables and Strings Examples.bat)
