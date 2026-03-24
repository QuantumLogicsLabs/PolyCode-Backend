# Command Line Arguments Reference

## Accessing Arguments
- `%0` - Script name
- `%1` - First argument
- `%2` - Second argument
- `%9` - Ninth argument
- `%*` - All arguments as single string

## Example Usage
```batch
@echo off
echo Script: %0
echo Arg 1: %1
echo Arg 2: %2
echo All args: %*
```

## Argument Shifts
```batch
@echo off
echo First arg: %1
shift
echo New first arg: %1
```

## Handling Missing Arguments
```batch
if "%1"=="" (
    echo Error: Missing argument
    echo Usage: %~nx0 [filename]
    exit /b 1
)
```

## Quoted Arguments
```batch
@echo off
echo Arg 1: %~1  :: Removes quotes
echo Arg 1: %1   :: Keeps quotes
```

## Argument Count
```batch
@echo off
set count=0
:loop
if "%~1"=="" goto :done
set /a count+=1
shift
goto :loop
:done
echo Argument count: %count%
```

## Best Practices
- Always validate required arguments
- Provide usage help for missing arguments
- Handle quoted arguments properly
- Use meaningful argument names
