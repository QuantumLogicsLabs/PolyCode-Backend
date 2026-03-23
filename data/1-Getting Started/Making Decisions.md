# Making Decisions (IF Statements)

## Your Program Can Think! 🤔

So far, your scripts do the same thing every time. But what if you want them to make decisions?

That's where **IF statements** come in!

---

## Basic IF Statement

### Simple Check
```batch
@echo off
set /p age=How old are you? 

if %age% gtr 17 (
    echo You can get a driver's license!
)

pause
```

### How it Works:
1. Checks if age is greater than 17
2. If TRUE → shows the message
3. If FALSE → skips the message

---

## Comparison Operators

| Operator | Meaning | Example |
|----------|---------|---------|
| `EQU` | Equal | `if %a% equ 5` |
| `NEQ` | Not equal | `if %a% neq 5` |
| `LSS` | Less than | `if %a% lss 10` |
| `LEQ` | Less or equal | `if %a% leq 10` |
| `GTR` | Greater than | `if %a% gtr 10` |
| `GEQ` | Greater or equal | `if %a% geq 10` |

### Examples:
```batch
if %score% equ 100 echo Perfect score!
if %age% lss 18 echo You're a minor
if %temp% gtr 30 echo It's hot outside!
if %hours% geq 40 echo Full-time worker
```

---

## IF-ELSE Statement

### Two Choices
```batch
@echo off
set /p password=Enter password: 

if "%password%"=="secret123" (
    echo Access GRANTED!
) else (
    echo Access DENIED!
)

pause
```

### Real Example - Login System:
```batch
@echo off
set correctpass=MyPassword123

set /p userpass=Password: 

if "%userpass%"=="%correctpass%" (
    echo Welcome! Login successful.
) else (
    echo Wrong password! Try again.
)

pause
```

---

## Checking Files

### IF EXIST - File Exists
```batch
@echo off
if exist "important.txt" (
    echo File found!
) else (
    echo File not found!
)

pause
```

### Check Multiple Files:
```batch
@echo off
if exist "file1.txt" (
    echo file1.txt exists
)

if exist "file2.txt" (
    echo file2.txt exists
)

if not exist "file3.txt" (
    echo file3.txt is missing!
)

pause
```

---

## Checking Folders

### Folder Exists
```batch
@echo off
if exist "C:\Backup\" (
    echo Backup folder exists
) else (
    echo Creating backup folder...
    mkdir "C:\Backup"
)

pause
```

---

## Multiple Conditions

### Nested IF
```batch
@echo off
set /p age=Your age: 
set /p license=Do you have license? (yes/no): 

if %age% geq 18 (
    if "%license%"=="yes" (
        echo You can drive!
    ) else (
        echo You need a license first!
    )
) else (
    echo Too young to drive!
)

pause
```

### Using AND Logic
```batch
@echo off
set /p temp=Temperature: 
set /p sunny=Is it sunny? (yes/no): 

if %temp% geq 25 (
    if "%sunny%"=="yes" (
        echo Perfect beach day!
    )
)

pause
```

---

## String Comparisons

### Check Text
```batch
@echo off
set /p color=Favorite color: 

if "%color%"=="blue" (
    echo Blue is a great color!
)

if "%color%"=="red" (
    echo Red is passionate!
)

if "%color%"=="green" (
    echo Green is natural!
)

pause
```

### Check if Empty
```batch
@echo off
set /p name=Your name: 

if "%name%"=="" (
    echo You didn't enter a name!
) else (
    echo Hello, %name%!
)

pause
```

---

## Error Level Checking

### Check if Command Succeeded
```batch
@echo off
copy "source.txt" "dest.txt" > nul

if errorlevel 1 (
    echo Copy failed!
) else (
    echo Copy successful!
)

pause
```

### What is ERRORLEVEL?
- `0` = Success
- `1` or higher = Error occurred

---

## Practical Examples

### Example 1: Grade Checker
```batch
@echo off
set /p score=Enter score (0-100): 

if %score% geq 90 (
    echo Grade: A - Excellent!
)

if %score% geq 80 (
    if %score% lss 90 (
        echo Grade: B - Good job!
    )
)

if %score% geq 70 (
    if %score% lss 80 (
        echo Grade: C - Average
    )
)

if %score% lss 70 (
    echo Grade: D - Needs improvement
)

pause
```

### Example 2: File Backup Checker
```batch
@echo off
if exist "backup.txt" (
    echo Backup exists, skipping...
) else (
    echo No backup found, creating...
    copy "original.txt" "backup.txt"
    echo Backup created!
)

pause
```

### Example 3: Age Verifier
```batch
@echo off
echo ===== Age Verifier =====
echo.
set /p age=Your age: 

if %age% lss 13 (
    echo You're a child
    echo Content rating: G
)

if %age% geq 13 (
    if %age% lss 18 (
        echo You're a teenager
        echo Content rating: PG-13
    )
)

if %age% geq 18 (
    echo You're an adult
    echo Content rating: R
)

pause
```

### Example 4: Disk Space Checker
```batch
@echo off
echo Checking disk space...

dir C: | find "bytes free" > nul
if errorlevel 1 (
    echo Warning: Low disk space!
) else (
    echo Disk space OK
)

pause
```

### Example 5: Simple Menu
```batch
@echo off
echo ===== Main Menu =====
echo 1. Start Game
echo 2. Load Game
echo 3. Exit
echo.

set /p choice=Choose option: 

if "%choice%"=="1" (
    echo Starting game...
)

if "%choice%"=="2" (
    echo Loading game...
)

if "%choice%"=="3" (
    echo Goodbye!
    exit /b
)

pause
```

---

## NOT Operator

### Reverse Logic
```batch
@echo off
if not exist "file.txt" (
    echo File does NOT exist
)

if not "%answer%"=="yes" (
    echo Answer is NOT yes
)

pause
```

---

## Tips & Tricks 💡

### Tip 1: Always Quote Strings
```batch
if "%var%"=="value"    REM Safe
if %var%==value        REM Risky
```

### Tip 2: Use Parentheses for Clarity
```batch
if %age% geq 18 (
    echo Adult
) else (
    echo Minor
)
```

### Tip 3: Combine Related Checks
```batch
if %age% geq 18 (
    if %licensed% equ 1 (
        echo Can drive
    )
)
```

### Tip 4: Test Both Paths
Test your script with both TRUE and FALSE conditions!

---

## Common Mistakes

### ❌ Missing Quotes
```batch
if %name%=John    REM Wrong
if "%name%"=="John"   REM Correct
```

### ❌ Wrong Operator
```batch
if %a% = %b%      REM Wrong (= is for SET)
if %a% equ %b%    REM Correct
```

### ❌ Bad Syntax
```batch
if %x%==5 echo Five else echo Not five   REM Wrong
if %x%==5 (
    echo Five
) else (
    echo Not five
)                      REM Correct
```

---

## Practice Exercises

1. **Password Checker**: Check if password matches
2. **Number Guesser**: Check if guess equals secret number
3. **Weather Advisor**: Suggest clothes based on temp
4. **Ticket Seller**: Check age for movie rating
5. **File Manager**: Check if file exists before copying

---

## Quick Reference

| Check | Syntax | Example |
|-------|--------|---------|
| Equal | `if %a% equ %b%` | `if %age% equ 18` |
| Not equal | `if %a% neq %b%` | `if %x% neq 0` |
| Greater | `if %a% gtr %b%` | `if %score% gtr 50` |
| Less than | `if %a% lss %b%` | `if %temp% lss 0` |
| File exists | `if exist file` | `if exist "doc.txt"` |
| Is empty | `if "%var%"==""` | `if "%name%"==""` |

---

**IF statements make your programs smart!** Practice making decisions! 🎯

**Next:** [Looping with FOR](Looping_with_FOR.md)
