# Code Style Guide

## Naming Conventions
### Variables
- Use descriptive names: %user_name% not %un%
- Use uppercase for constants: %MAX_RETRIES%
- Use underscores for multi-word names: %file_path%

### Files
- Use lowercase with underscores: script_name.bat
- Avoid spaces in filenames
- Use descriptive names: backup_database.bat not backup.bat

## Code Organization
### Structure
```batch
@echo off
setlocal enabledelayedexpansion

:: Configuration Section
set "config_var=value"

:: Main Logic
call :function_name

:: Cleanup
endlocal
goto :eof

:: Functions Section
:function_name
    :: Function code
    goto :eof
```

### Comments
- Add header comments with purpose and usage
- Comment complex logic
- Use :: for comments, not REM
- Keep comments concise and relevant

## Error Handling
- Always check error levels
- Provide meaningful error messages
- Include cleanup routines
- Log important operations

## Performance
- Minimize external command calls
- Use built-in commands when possible
- Avoid unnecessary variable operations
- Optimize loops and iterations
