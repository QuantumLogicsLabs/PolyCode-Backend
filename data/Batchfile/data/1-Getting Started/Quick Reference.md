# Quick Reference Guide

## Essential Commands
- `@echo off` - Hide command output
- `echo message` - Display text
- `set VAR=value` - Set variable
- `set /p VAR=prompt` - Get user input
- `pause` - Wait for keypress
- `exit /b` - Exit script

## Control Flow
- `IF condition command` - Conditional execution
- `FOR %%i in (set) DO command` - Loop
- `GOTO label` - Jump to label
- `CALL :label` - Call subroutine

## File Operations
- `copy source dest` - Copy files
- `move source dest` - Move files
- `del filename` - Delete files
- `dir` - List directory contents
- `type filename` - Display file contents

## Common Variables
- `%CD%` - Current directory
- `%DATE%` - Current date
- `%TIME%` - Current time
- `%RANDOM%` - Random number
- `%ERRORLEVEL%` - Last command exit code

## Example Script
```batch
@echo off
echo Hello, %USERNAME%!
echo Today is %DATE%
set /p name=What's your name? 
echo Nice to meet you, %name%!
pause
```
