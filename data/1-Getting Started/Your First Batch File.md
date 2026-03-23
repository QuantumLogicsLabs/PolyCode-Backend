# Your First Batch File

## Let's Create Magic! ✨

Ready to write your very first batch file? Don't worry - it's easier than you think!

By the end of this guide, you'll have created **5 working batch files** that do real things!

---

## Example 1: The Greeter 👋

Your first program will say hello and ask for your name.

### Step-by-Step:

**1. Open Notepad**
- Press `Windows + R`
- Type: `notepad`
- Press Enter

**2. Type This Code:**
```batch
@echo off
echo ================================
echo    Welcome to Batch Scripting!
echo ================================
echo.
set /p name=What is your name? 
echo.
echo Hello, %name%! Nice to meet you!
echo.
echo You're learning batch scripting!
echo.
pause
```

**3. Save the File:**
- Click **File** → **Save As**
- Go to Desktop
- Change "Save as type" to **All Files (*.*)**
- Name it: `hello.bat`
- Click **Save**

**4. Run It:**
- Go to Desktop
- Double-click `hello.bat`

### What Happens?

The script will:
1. Display a welcome message
2. Ask for your name
3. Greet you personally
4. Show an encouraging message
5. Wait for you to press a key

### Understanding Each Line:

```batch
@echo off
```
Turns off command echoing. Makes output cleaner.

```batch
echo ================================
```
Displays text. We use `=` to make a line.

```batch
echo.
```
Creates a blank line (for spacing).

```batch
set /p name=What is your name? 
```
Asks user for input and stores it in variable `name`.

```batch
echo Hello, %name%!
```
Uses the variable. `%name%` gets replaced with what user typed.

```batch
pause
```
Waits for user to press a key. Keeps window open.

---

## Example 2: The Calculator 🧮

Let's make a simple calculator!

```batch
@echo off
echo ========== Calculator ==========
echo.
set /p num1=Enter first number: 
set /p num2=Enter second number: 
echo.
set /a sum=num1+num2
set /a diff=num1-num2
set /a prod=num1*num2
set /a quot=num1/num2
echo Results:
echo %num1% + %num2% = %sum%
echo %num1% - %num2% = %diff%
echo %num1% * %num2% = %prod%
echo %num1% / %num2% = %quot%
echo.
pause
```

### Save as: `calculator.bat`

### Try It:
1. Run the file
2. Enter two numbers
3. See all calculations instantly!

### New Commands:

```batch
set /a sum=num1+num2
```
Does math! `set /a` means "do arithmetic".

---

## Example 3: File Creator 📄

Create multiple files at once!

```batch
@echo off
echo ===== File Creator =====
echo.
set /p filename=Enter base filename: 
set /p count=How many files to create? 
echo.
echo Creating %count% files...
echo.

for /l %%i in (1,1,%count%) do (
    type nul > "%filename%_%%i.txt"
    echo Created: %filename%_%%i.txt
)

echo.
echo All files created!
pause
```

### Save as: `create_files.bat`

### Try It:
1. Run it
2. Enter base name (like "report")
3. Enter count (like "5")
4. Check your folder - 5 files created!

### What's New?

```batch
for /l %%i in (1,1,%count%) do (
```
A loop! Runs from 1 to count, incrementing by 1.

```batch
type nul > "%filename%_%%i.txt"
```
Creates an empty file.

---

## Example 4: System Info Reporter 💻

Get your computer's information!

```batch
@echo off
echo ****** System Information ******
echo.
echo Computer Name: %computername%
echo Username: %username%
echo Current Directory: %cd%
echo Today's Date: %date%
echo Current Time: %time%
echo.
echo Windows Version:
ver
echo.
echo Processor:
wmic cpu get name
echo.
echo Memory:
wmic memorychip get capacity
echo.
pause
```

### Save as: `system_info.bat`

### Run It:
Watch it display all your system information!

### Special Variables Used:

| Variable | Shows |
|----------|-------|
| `%computername%` | Computer name |
| `%username%` | Your username |
| `%cd%` | Current folder |
| `%date%` | Today's date |
| `%time%` | Current time |

---

## Example 5: Quick Backup 📦

Backup important files automatically!

```batch
@echo off
echo Starting backup...
echo.

REM Create backup folder if it doesn't exist
if not exist "Backup" mkdir "Backup"

REM Copy files
echo Copying text files...
copy *.txt Backup\ > nul

echo Copying documents...
copy *.docx Backup\ > nul
copy *.pdf Backup\ > nul

echo.
echo ===== Backup Complete! =====
echo Files saved to: %cd%\Backup
echo.
pause
```

### Save as: `backup.bat`

### How to Use:
1. Put it in a folder with your files
2. Run it
3. All .txt, .docx, and .pdf files copied to Backup folder!

### New Tricks:

```batch
if not exist "Backup" mkdir "Backup"
```
Checks if folder exists. If not, creates it!

```batch
copy *.txt Backup\ > nul
```
Copies files. `> nul` hides the success messages.

---

## Common Mistakes & Fixes

### ❌ Mistake 1: Forgetting `.bat` Extension

**Problem:** Saved as `hello.bat.txt`

**Fix:** 
- In Notepad Save As, choose "All Files (*.*)"
- Make sure filename ends with `.bat`

### ❌ Mistake 2: Spaces Around `=`

**Problem:**
```batch
set name = John
```

**Fix:**
```batch
set name=John
```
No spaces around the `=` sign!

### ❌ Mistake 3: Wrong Variable Syntax

**Problem:**
```batch
echo Hello, name%
```

**Fix:**
```batch
echo Hello, %name%
```
Percent signs go on BOTH sides!

### ❌ Mistake 4: Not Quoting Paths

**Problem:**
```batch
copy C:\My Files\file.txt C:\Backup
```

**Fix:**
```batch
copy "C:\My Files\file.txt" "C:\Backup"
```
Always quote paths with spaces!

---

## Try These Challenges! 🎯

### Challenge 1: Mad Libs Game
Create a story using variables:
```batch
@echo off
set /p adjective=Enter an adjective: 
set /p noun=Enter a noun: 
set /p verb=Enter a verb: 
echo.
echo The %adjective% %noun% decided to %verb% all day!
pause
```

### Challenge 2: Age Calculator
Ask for birth year, calculate age:
```batch
@echo off
set /p birthyear=Enter birth year: 
set /a age=2026-birthyear
echo You are %age% years old!
pause
```

### Challenge 3: File Renamer
Rename all files in folder:
```batch
@echo off
set /p prefix=Enter prefix: 
for %%f in (*.txt) do ren "%%f" "%prefix%_%%f"
echo Files renamed!
pause
```

---

## Tips for Success 💡

### ✅ Test After Every Change
1. Write one line
2. Save
3. Run it
4. If it works, add another line

### ✅ Start Simple
Don't try to write complex scripts immediately. Master the basics first!

### ✅ Copy and Modify
Take working examples and change them:
- Change messages
- Change colors
- Change operations

### ✅ Learn from Errors
When something breaks:
- Read the error message
- Google it
- Fix it
- Learn from it

### ✅ Keep Your Scripts
Save every script you write, even small ones. Build your own library!

---

## Your Turn! 🎮

Now it's time to practice:

1. **Create all 5 examples** above
2. **Run each one** multiple times
3. **Modify them** - change messages, add features
4. **Create your own** - automate something you do

### Practice Ideas:

- 🎵 Music file organizer
- 📸 Photo renamer  
- 📚 Homework helper
- 🎮 Game launcher
- 📊 Grade calculator

---

## What You Learned ✅

You now know how to:
- ✅ Create batch files
- ✅ Display messages with `echo`
- ✅ Get user input with `set /p`
- ✅ Do math with `set /a`
- ✅ Use variables with `%var%`
- ✅ Create loops with `for`
- ✅ Make decisions with `if`
- ✅ Copy and move files
- ✅ Create folders

That's a LOT for your first lesson! 🎉

---

## Ready for More?

Great job! You've written your first batch files. 

But we're just getting started...

In the next sections, you'll learn:
- More commands
- Better techniques
- Advanced tricks
- Professional patterns

---

**Congratulations, Batch Programmer!** 🎊

Keep practicing, keep experimenting, and most importantly - have fun!

**Next:** [Understanding Commands](../02_Fundamentals/Understanding_Commands.md)
