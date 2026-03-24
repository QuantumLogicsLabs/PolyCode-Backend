# Quick Start Guide

Get started with batch scripting in 5 minutes!

## What is a Batch File?

A batch file is a text file with a `.bat` extension that contains commands for Windows to execute automatically.

## Creating Your First Batch File

### Step 1: Open Notepad
- Press `Win + R`
- Type `notepad`
- Press Enter

### Step 2: Write Your First Script

```batch
@echo off
echo Hello! I'm learning batch scripting.
echo.
set /p name=What is your name? 
echo Nice to meet you, %name%!
pause
```

### Step 3: Save the File
1. Click **File** → **Save As**
2. Navigate to Desktop (or any folder)
3. Set "Save as type" to **All Files (*.*)**
4. Name it `hello.bat`
5. Click **Save**

### Step 4: Run It!
- Double-click `hello.bat` on your Desktop
- Watch it run!

## Understanding the Code

| Line | What it does |
|------|--------------|
| `@echo off` | Hides command echoing for cleaner output |
| `echo` | Displays text on screen |
| `echo.` | Prints a blank line |
| `set /p name=` | Prompts user for input and stores in variable `name` |
| `%name%` | Uses the value of the `name` variable |
| `pause` | Waits for user to press a key before closing |

## Try These Next

### 1. Simple Calculator
```batch
@echo off
set /p num1=Enter first number: 
set /p num2=Enter second number: 
set /a sum=num1+num2
echo The sum is: %sum%
pause
```

### 2. File Creator
```batch
@echo off
set /p filename=Enter filename to create: 
type nul > %filename%
echo File %filename% created!
pause
```

### 3. System Info
```batch
@echo off
echo Computer Name: %computername%
echo Username: %username%
echo Current Directory: %cd%
echo Date: %date%
echo Time: %time%
pause
```

## Essential Commands to Know

| Command | Purpose | Example |
|---------|---------|---------|
| `echo` | Display text | `echo Hello` |
| `set` | Create variables | `set x=5` |
| `set /p` | Get user input | `set /p var=Prompt:` |
| `set /a` | Do math | `set /a result=2+2` |
| `if` | Make decisions | `if exist file.txt echo Found` |
| `for` | Loop | `for %%i in (1,2,3) do echo %%i` |
| `rem` | Add comments | `rem This is a comment` |
| `pause` | Wait for keypress | `pause` |
| `cls` | Clear screen | `cls` |
| `exit` | End script | `exit` |

## Common Mistakes to Avoid

❌ **Forgetting @echo off**
```batch
REM Wrong - shows all commands
echo Hello

REM Right - clean output
@echo off
echo Hello
```

❌ **Not quoting paths with spaces**
```batch
REM Wrong
copy C:\My Files\file.txt C:\Backup

REM Right
copy "C:\My Files\file.txt" "C:\Backup"
```

❌ **Using single % in batch files**
```batch
REM Wrong in .bat files
for %i in (*.txt) do echo %i

REM Right in .bat files (use %%)
for %%i in (*.txt) do echo %%i
```

## Learning Path

Now that you've created your first script:

1. ✅ **You did:** Created a simple interactive script
2. 📖 **Next:** Read [01_Basics](01_Basics/) for fundamentals
3. 🎯 **Then:** Try examples from [05_Examples](05_Examples/)
4. 🚀 **Finally:** Master advanced topics in [03_Advanced](03_Advanced/)

## Quick Reference

### Variables
```batch
set myvar=hello
echo %myvar%
```

### Math
```batch
set /a result=10+5*2
echo %result%
```

### Conditions
```batch
if exist file.txt (
    echo File found
) else (
    echo File not found
)
```

### Loops
```batch
for /l %%i in (1,1,5) do (
    echo Number %%i
)
```

## Getting Help

For any command, add `/?`:
```cmd
echo /?
set /?
for /?
```

## What's Next?

Choose your path:

- **🎓 Structured Learning:** Start from [Overview](00%29%20Overview.md)
- **💻 Practical Examples:** Jump to [Examples](05_Examples/)
- **📖 Reference:** Check [Command Reference](04_Reference/)

---

**Congratulations!** You've written your first batch script! 🎉

Keep practicing and soon you'll be automating tasks like a pro!
