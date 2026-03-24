# Intermediate Batch Scripting

This section covers intermediate topics for building more powerful batch scripts.

## Topics Covered

- [Input and Output](05_Input_Output.md) - Advanced input/output operations
- [Functions and Modularity](06_Functions_and_Modularity.md) - Creating reusable code blocks
- [Error Handling](07_Error_Handling.md) - Robust error handling techniques
- [String and Number Operations](08_String_and_Number_Operations.md) - Text and numeric manipulations

## Prerequisites

Before diving into these topics, make sure you're comfortable with:
- Basic batch syntax
- Variables
- Control flow (IF, FOR, GOTO)

## What You'll Learn

### Input/Output Operations
- Reading user input with SET /P
- Redirecting output to files
- Working with different output streams

### Functions and Code Organization
- Creating callable subroutines
- Passing parameters to functions
- Return values and error codes

### Error Handling
- Using ERRORLEVEL
- Implementing try-catch patterns
- Graceful failure handling

### String Manipulation
- Substring extraction
- String replacement
- Length calculations
- Number conversions

## Example: Function with Parameters

```batch
@echo off
call :Greet John
call :Greet Jane
pause
goto :EOF

:Greet
echo Hello, %1!
goto :EOF
```

## Next Steps

After completing intermediate topics:
- Explore [Advanced Topics](../03_Advanced/) for file operations and complex scenarios
- Check [Command Reference](../04_Reference/) for detailed command documentation
- Practice with [Examples](../05_Examples/)
