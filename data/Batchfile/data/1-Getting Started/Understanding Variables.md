# Understanding Variables

## What are Variables? 📦

Think of variables as **labeled boxes** where you can store information.

Imagine you have:
- A box labeled "Name" with "John" inside
- A box labeled "Age" with "25" inside  
- A box labeled "Score" with "100" inside

In batch scripting, that's exactly what variables are!

---

## Creating Variables

### The SET Command

Use `SET` to create or change variables:

```batch
set name=John
set age=25
set city=New York
```

**Important Rules:**
- ✅ No spaces around `=`
- ✅ Start with a letter
- ✅ Can contain numbers (but not start with them)
- ✅ No special characters except underscore

### Good Variable Names:
```batch
set username=Alice
set user_age=30
set score1=100
```

### Bad Variable Names:
```batch
set user name=Bob      REM ❌ Space in name
set 1score=50          REM ❌ Starts with number
set user-name=Test     REM ❌ Hyphen not allowed
```

---

## Using Variables

### Access Variable Value

Put `%` signs around the variable name:

```batch
set name=Sarah
echo Hello, %name%!    REM Shows: Hello, Sarah!
```

**Example:**
```batch
@echo off
set firstname=John
set lastname=Doe
echo User: %firstname% %lastname%
pause
```

### Complete Example:
```batch
@echo off
set game=Minecraft
set player=Steve
set level=10

echo Welcome to %game%!
echo Player: %player%
echo Level: %level%
pause
```

---

## Getting User Input

### SET /P - Prompt User

Ask the user to type something:

```batch
set /p name=What is your name? 
echo Hello, %name%!
```

**How it works:**
1. Shows the message after `=`
2. Waits for user to type
3. Stores what they typed in the variable

### Examples:

**Ask for Name:**
```batch
@echo off
set /p username=Enter your name: 
echo Nice to meet you, %username%!
pause
```

**Ask Multiple Questions:**
```batch
@echo off
set /p name=Your name: 
set /p age=Your age: 
set /p city=Your city: 
echo.
echo Profile:
echo Name: %name%
echo Age: %age%
echo City: %city%
pause
```

**Create Account:**
```batch
@echo off
echo ===== Create Account =====
echo.
set /p username=Username: 
set /p password=Password: 
set /p email=Email: 
echo.
echo Account created!
echo Username: %username%
echo Email: %email%
pause
```

---

## Doing Math with Variables

### SET /A - Arithmetic

Use `SET /A` for calculations:

```batch
set /a result=5+3
echo %result%          REM Shows: 8

set /a product=6*7
echo %product%         REM Shows: 42
```

### Math Operations:

| Operation | Symbol | Example | Result |
|-----------|--------|---------|--------|
| Addition | `+` | `set /a r=5+3` | 8 |
| Subtraction | `-` | `set /a r=10-4` | 6 |
| Multiplication | `*` | `set /a r=6*7` | 42 |
| Division | `/` | `set /a r=20/4` | 5 |
| Modulo | `%%` | `set /a r=17%%5` | 2 (remainder) |

### Calculator Example:
```batch
@echo off
set /p num1=First number: 
set /p num2=Second number: 

set /a sum=num1+num2
set /a diff=num1-num2
set /a prod=num1*num2
set /a quot=num1/num2

echo.
echo Results:
echo %num1% + %num2% = %sum%
echo %num1% - %num2% = %diff%
echo %num1% * %num2% = %prod%
echo %num1% / %num2% = %quot%
pause
```

### Increment Counter:
```batch
@echo off
set count=0
set /a count+=1
set /a count+=1
set /a count+=1
echo Count: %count%    REM Shows: 3
pause
```

---

## Special Variables

Windows provides some variables automatically:

### Environment Variables

| Variable | What it contains |
|----------|------------------|
| `%date%` | Current date |
| `%time%` | Current time |
| `%cd%` | Current folder |
| `%computername%` | Computer name |
| `%username%` | Your username |
| `%temp%` | Temporary files folder |
| `%userprofile%` | Your home folder |

### See All Variables:
```batch
@echo off
echo Today: %date%
echo Time: %time%
echo Computer: %computername%
echo User: %username%
echo Folder: %cd%
pause
```

### List All Variables:
```batch
@echo off
set
pause
```

---

## String Manipulation

### Get Part of Text (Substring)

Extract portion of text:

```batch
set text=Hello World
echo %text:~0,5%       REM Shows: Hello
echo %text:~6%         REM Shows: World
```

**Format:** `%variable:~start,length%`

**Examples:**
```batch
set filename=document.txt
echo %filename:~0,8%   REM First 8 chars: document
echo %filename:~-3%    REM Last 3 chars: txt
```

### Replace Text

Replace part of text:

```batch
set text=Hello World
echo %text:World=Earth%   REM Shows: Hello Earth
```

**Format:** `%variable:old=new%`

**Example:**
```batch
set message=I like cats
echo %message:cats=dogs%  REM Shows: I like dogs
```

---

## Variable Scope

### Local vs Global

By default, variables are **global** (available everywhere).

Use `SETLOCAL` to make them **local**:

```batch
@echo off
set name=Global

setlocal
set name=Local
echo Inside: %name%    REM Shows: Local
endlocal

echo Outside: %name%   REM Shows: Global
pause
```

### When to Use SETLOCAL:
```batch
@echo off
setlocal

REM Variables here are temporary
set tempvar=test
REM ... do work ...

endlocal
REM tempvar is now gone
pause
```

---

## Practical Examples

### Example 1: Registration Form
```batch
@echo off
echo ===== Registration Form =====
echo.
set /p fname=First Name: 
set /p lname=Last Name: 
set /p email=Email: 
set /p phone=Phone: 
echo.
echo ===== Confirmation =====
echo Name: %fname% %lname%
echo Email: %email%
echo Phone: %phone%
echo.
echo Registration complete!
pause
```

### Example 2: Grade Calculator
```batch
@echo off
echo ===== Grade Calculator =====
echo.
set /p math=Math score: 
set /p science=Science score: 
set /p english=English score: 

set /a total=math+science+english
set /a average=total/3

echo.
echo Total: %total%/300
echo Average: %average%%%
if %average% geq 60 (
    echo Status: PASSED
) else (
    echo Status: FAILED
)
pause
```

### Example 3: File Renamer
```batch
@echo off
set /p prefix=Enter prefix for files: 
set count=0

for %%f in (*.txt) do (
    set /a count+=1
    ren "%%f" "%prefix%_!count!.txt"
    echo Renamed: %%f
)

echo Total renamed: %count%
pause
```

### Example 4: Password Checker
```batch
@echo off
set correctpass=secret123

set /p userpass=Enter password: 

if "%userpass%"=="%correctpass%" (
    echo Access GRANTED!
) else (
    echo Access DENIED!
)
pause
```

---

## Common Mistakes

### ❌ Mistake 1: Spaces Around =
```batch
set name = John    REM WRONG!
set name=John      REM CORRECT!
```

### ❌ Mistake 2: Forgetting % Signs
```batch
echo name          REM Shows: name
echo %name%        REM Shows: John
```

### ❌ Mistake 3: Wrong Math Syntax
```batch
set result=5+3     REM Stores text "5+3"
set /a result=5+3  REM Calculates: 8
```

### ❌ Mistake 4: Special Characters
```batch
set path=C:\Folder & Other    REM WRONG!
set "path=C:\Folder & Other"  REM CORRECT!
```

---

## Tips & Tricks 💡

### Tip 1: Clear Variable Names
```batch
set username=John           REM Good
set x=John                  REM Unclear
```

### Tip 2: Use Quotes for Paths
```batch
set "mypath=C:\Program Files\App"
```

### Tip 3: Initialize Variables
```batch
set count=0
set total=0
set done=false
```

### Tip 4: Display Variable Info
```batch
set myvar          REM Shows: myvar=value
echo %myvar%       REM Shows just the value
```

---

## Practice Exercises

1. **Bio Generator**: Ask name, age, hobby - display bio
2. **Tip Calculator**: Bill amount × 15% = tip
3. **Temperature Converter**: Celsius to Fahrenheit
4. **Discount Calculator**: Price - discount = final
5. **Story Creator**: Mad Libs style story

---

## Quick Reference

| Task | Command | Example |
|------|---------|---------|
| Create variable | `SET var=value` | `set name=John` |
| Use variable | `%var%` | `echo %name%` |
| Get input | `SET /P var=prompt` | `set /p n=Name?` |
| Do math | `SET /A expr` | `set /a r=5+3` |
| Show all | `SET` | `set` |

---

**Variables are fundamental to programming!** Master them and you're halfway to being a pro! 🎯

**Next:** [Working with Text](Working_with_Text.md)
