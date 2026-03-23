# Batch File Basics - Quick Reference

## Essential Commands You Need to Know 📚

This is your go-to reference for basic batch commands. Keep this handy!

---

## Display & Output

### ECHO - Show Messages
```batch
echo Hello World          REM Shows: Hello World
echo.                     REM Shows blank line
echo ==================   REM Shows separator line
```

### Turn Off Command Display
```batch
@echo off                 REM Cleaner output
```

**Example:**
```batch
@echo off
echo Welcome to my program!
echo.
pause
```

---

## Variables (Storing Information)

### SET - Create Variables
```batch
set name=John             REM Store text
set age=25                REM Store number
set "message=Hello"       REM Safe way with spaces
```

### Use Variables
```batch
echo %name%               REM Shows: John
echo Hello, %name%!       REM Shows: Hello, John!
```

### Get User Input
```batch
set /p username=What is your name? 
echo Hello, %username%!
```

**Example:**
```batch
@echo off
set /p name=Enter your name: 
set /p age=Enter your age: 
echo.
echo Name: %name%
echo Age: %age%
pause
```

---

## Math Operations

### SET /A - Do Calculations
```batch
set /a sum=5+3            REM Addition: 8
set /a diff=10-4          REM Subtraction: 6
set /a prod=6*7           REM Multiplication: 42
set /a quot=20/4          REM Division: 5
set /a mod=17%%5          REM Modulo (remainder): 2
```

**Example - Simple Calculator:**
```batch
@echo off
set /p num1=First number: 
set /p num2=Second number: 
set /a sum=num1+num2
set /a prod=num1*num2
echo Sum: %sum%
echo Product: %prod%
pause
```

---

## File Operations

### COPY - Copy Files
```batch
copy file.txt backup\     REM Copy to folder
copy *.txt docs\          REM Copy all .txt files
copy /Y file.txt dest\    REM Overwrite without asking
```

### MOVE - Move Files
```batch
move file.txt folder\     REM Move file
move *.jpg images\        REM Move all .jpg files
```

### DEL - Delete Files
```batch
del temp.txt              REM Delete specific file
del *.tmp                 REM Delete all .tmp files
del /q old.txt            REM Delete quietly (no confirm)
```

### REN - Rename Files
```batch
ren oldname.txt newname.txt    REM Rename file
ren *.txt *.bak                REM Change all extensions
```

**Example - Backup Script:**
```batch
@echo off
echo Backing up files...
mkdir Backup
copy *.txt Backup\
copy *.docx Backup\
echo Backup complete!
pause
```

---

## Folder Operations

### MKDIR (MD) - Create Folder
```batch
mkdir NewFolder         REM Create folder
mkdir "My Files"        REM Folder with space
```

### CD (CHDIR) - Change Folder
```batch
cd Documents            REM Go to Documents
cd ..                   REM Go up one level
cd \                    REM Go to root
cd /d D:\Projects       REM Change drive and folder
```

### RD (RMDIR) - Remove Folder
```batch
rd EmptyFolder          REM Remove empty folder
rd /s FullFolder        REM Remove folder with contents
rd /s /q Folder         REM Remove quietly
```

**Example - Folder Organizer:**
```batch
@echo off
mkdir Images Documents Music
move *.jpg Images\
move *.png Images\
move *.pdf Documents\
move *.mp3 Music\
echo Organized!
pause
```

---

## Conditional Logic (Making Decisions)

### IF - Check Conditions
```batch
if exist file.txt (
    echo File found
)

if "%name%"=="John" (
    echo Hello John
)

if %age% gtr 18 (
    echo Adult
)
```

### Comparison Operators
| Operator | Meaning | Example |
|----------|---------|---------|
| `EQU` | Equal | `if %a% equ %b%` |
| `NEQ` | Not equal | `if %a% neq %b%` |
| `LSS` | Less than | `if %a% lss %b%` |
| `LEQ` | Less or equal | `if %a% leq %b%` |
| `GTR` | Greater | `if %a% gtr %b%` |
| `GEQ` | Greater or equal | `if %a% geq %b%` |

**Example - File Checker:**
```batch
@echo off
if exist "important.txt" (
    echo File exists!
) else (
    echo File not found!
)
pause
```

---

## Loops (Repeating Actions)

### FOR - Loop Through Items
```batch
for %%f in (*.txt) do (
    echo Processing %%f
)

for /l %%i in (1,1,5) do (
    echo Number %%i
)
```

### FOR /L - Counting Loop
```batch
for /l %%i in (start,step,end) do command

for /l %%i in (1,1,10) do echo %%i    REM 1 to 10
for /l %%i in (0,2,10) do echo %%i    REM 0,2,4,6,8,10
for /l %%i in (10,-1,1) do echo %%i   REM 10 down to 1
```

**Example - File Counter:**
```batch
@echo off
set count=0
for %%f in (*.txt) do (
    set /a count+=1
    echo Processing file %%i of %count%
)
echo Total files: %count%
pause
```

---

## Special Variables

### Built-in Variables
| Variable | What it shows |
|----------|---------------|
| `%date%` | Current date |
| `%time%` | Current time |
| `%cd%` | Current directory |
| `%computername%` | Computer name |
| `%username%` | Your username |
| `%temp%` | Temp folder |
| `%errorlevel%` | Last error code |

**Example - System Info:**
```batch
@echo off
echo Date: %date%
echo Time: %time%
echo Computer: %computername%
echo User: %username%
echo Folder: %cd%
pause
```

---

## Pausing & Waiting

### PAUSE - Wait for Key
```batch
pause                 REM Waits for keypress
```

### TIMEOUT - Wait Seconds
```batch
timeout /t 5          REM Wait 5 seconds
timeout /t 10 /nobreak  REM Wait 10 seconds (can't skip)
```

**Example - Countdown:**
```batch
@echo off
echo Starting in...
timeout /t 3
echo 3
timeout /t 1
echo 2
timeout /t 1
echo 1
echo GO!
```

---

## Comments (Notes to Yourself)

### REM - Add Comments
```batch
REM This is a comment
echo Hello
REM Another note here
```

**Example - Well Commented Script:**
```batch
@echo off
REM ======================
REM Backup Script
REM Created: Today
REM ======================

REM Create backup folder
mkdir Backup

REM Copy important files
copy *.txt Backup\
copy *.docx Backup\

echo Done!
pause
```

---

## Complete Examples

### Example 1: File Manager
```batch
@echo off
echo ===== File Manager =====
echo.
echo 1. Copy files
echo 2. Move files
echo 3. Delete files
echo 4. Exit
echo.

set /p choice=Choose option: 

if "%choice%"=="1" (
    copy *.txt Backup\
    echo Files copied!
)
if "%choice%"=="2" (
    move *.jpg Images\
    echo Files moved!
)
if "%choice%"=="3" (
    del *.tmp
    echo Files deleted!
)
if "%choice%"=="4" (
    echo Goodbye!
)

pause
```

### Example 2: Quiz Program
```batch
@echo off
set score=0

echo Question 1: What is 5+3?
set /p answer=Your answer: 
if "%answer%"=="8" (
    echo Correct!
    set /a score+=1
) else (
    echo Wrong!
)

echo Question 2: What is 10-4?
set /p answer=Your answer: 
if "%answer%"=="6" (
    echo Correct!
    set /a score+=1
) else (
    echo Wrong!
)

echo.
echo Your score: %score%/2
pause
```

### Example 3: Automated Setup
```batch
@echo off
echo Setting up project...

REM Create folders
mkdir src
mkdir lib
mkdir docs
mkdir test

REM Create initial files
type nul > src\main.bat
type nul > README.md
type nul > .gitignore

echo Project structure created!
dir /b
pause
```

---

## Quick Troubleshooting

| Problem | Solution |
|---------|----------|
| Variable not showing | Use `%var%` not `var` |
| Math not working | Use `set /a` for math |
| Spaces in path | Use quotes: `"path with spaces"` |
| FOR loop error | Use `%%` in files, `%` in command line |
| Can't find file | Check current directory with `cd` |

---

## Practice Exercises

1. **Greeter**: Ask name, say hello
2. **Calculator**: Add two numbers
3. **File Counter**: Count .txt files
4. **Backup**: Copy files to backup folder
5. **Quiz**: Ask 3 questions, keep score

---

## Remember These Rules! ⭐

1. ✅ No spaces around `=` in SET
2. ✅ Use `%variable%` to access variables
3. ✅ Quote paths with spaces
4. ✅ Use `%%` in FOR loops (in files)
5. ✅ End with `pause` to keep window open
6. ✅ Start with `@echo off` for clean output

---

**Keep this reference handy!** Print it or bookmark it.

**Next:** Start practicing with these commands! 💪
