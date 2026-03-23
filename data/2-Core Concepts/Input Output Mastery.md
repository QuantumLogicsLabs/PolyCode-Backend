# Input Output Mastery

## Complete I/O Guide! 💬

Master input and output operations.

---

## Output Methods

### ECHO Variations
```batch
@echo off
echo Normal message
echo.                    REM Blank line
echo ==================  REM Separator
echo Special: ^& ^\| ^< ^>
pause
```

### Write to File
```batch
@echo off
echo Line 1 > output.txt
echo Line 2 >> output.txt
type output.txt
pause
```

### Redirect Output
```batch
@echo off
dir > filelist.txt       REM Save to file
dir >> logfile.txt       REM Append
dir > nul                REM Hide output
pause
```

---

## Input Methods

### SET /P - Get Input
```batch
@echo off
set /p name=Your name: 
echo Hello, %name%!
pause
```

### Multiple Inputs
```batch
@echo off
set /p fname=First: 
set /p lname=Last: 
set /p email=Email: 
echo.
echo Welcome, %fname% %lname%!
pause
```

---

## Formatting Output

### Create Boxes
```batch
@echo off
echo ╔══════════════════╗
echo ║   Welcome Menu   ║
echo ╚══════════════════╝
pause
```

### Progress Messages
```batch
@echo off
echo [1/3] Starting...
timeout /t 1 > nul
echo [2/3] Processing...
timeout /t 1 > nul
echo [3/3] Complete!
pause
```

---

**Master I/O for interactive scripts!** 🎯
