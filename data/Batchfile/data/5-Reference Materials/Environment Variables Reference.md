# Environment Variables Reference

## System Variables
- `%COMPUTERNAME%` - Computer name
- `%USERNAME%` - Current user name
- `%USERPROFILE%` - User profile directory
- `%SYSTEMROOT%` - Windows directory
- `%TEMP%` - Temporary files directory
- `%PATH%` - System executable search path

## Dynamic Variables
- `%DATE%` - Current date
- `%TIME%` - Current time
- `%CD%` - Current directory
- `%ERRORLEVEL%` - Exit code of last command
- `%RANDOM%` - Random number 0-32767

## Usage Examples
```batch
echo Current user: %USERNAME%
echo System directory: %SYSTEMROOT%
echo Current date: %DATE%
echo Random number: %RANDOM%
```

## Custom Variables
```batch
set "MY_VAR=value"
echo %MY_VAR%
```

## Best Practices
- Use quotes for variables with spaces
- Check if variables exist before use
- Clear variables when done: set "VAR="
