# Working with Text (Strings)

## What are Strings? 💬

A **string** is just text - words, sentences, or any characters.

Examples:
- `"Hello World"` - a string
- `"John Doe"` - a string  
- `"C:\Files\document.txt"` - a string
- `"123 Main Street"` - a string (even though it has numbers!)

---

## Creating String Variables

### Store Text
```batch
set greeting=Hello World
set name=John Doe
set address=123 Main Street
```

### Important: Use Quotes for Special Characters
```batch
set "path=C:\Program Files\App"     REM Good!
set path=C:\Program Files\App       REM Wrong! (space causes issues)
```

---

## Combining Strings (Concatenation)

### Join Text Together
```batch
@echo off
set firstname=John
set lastname=Doe
set fullname=%firstname% %lastname%
echo Full Name: %fullname%
pause
```

### Build Messages
```batch
@echo off
set user=Alice
set level=5
set message=User %user% is at level %level%
echo %message%
pause
```

---

## String Length

### Count Characters
```batch
@echo off
set text=Hello
REM Manual counting: H-e-l-l-o = 5 characters
set length=5
echo Length: %length%
pause
```

**Note:** Batch doesn't have built-in length function, but you can count manually or use tricks.

---

## Extracting Parts (Substring)

### Get First Part
```batch
@echo off
set filename=document.txt
set namepart=%filename:~0,8%
echo Name: %namepart%    REM Shows: document
pause
```

### Get Last Part (Extension)
```batch
@echo off
set filename=document.txt
set ext=%filename:~-3%
echo Extension: %ext%    REM Shows: txt
pause
```

### Format: `%variable:~start,length%`

**Examples:**
```batch
set text=Hello World
echo %text:~0,5%     REM Hello (first 5 chars)
echo %text:~6%       REM World (from position 6 to end)
echo %text:~0,1%     REM H (first character)
```

---

## Replacing Text

### Simple Replace
```batch
@echo off
set text=I love cats
set newtext=%text:cats=dogs%
echo %newtext%       REM Shows: I love dogs
pause
```

### Replace All Occurrences
```batch
@echo off
set text=cat cat cat
set newtext=%text:cat=dog%
echo %newtext%       REM Shows: dog dog dog
pause
```

### Remove Text
```batch
@echo off
set filename=backup_old.txt
set newname=%filename:_old=%
echo %newname%       REM Shows: backup.txt
pause
```

---

## Changing Case (Upper/Lower)

Batch doesn't have direct case conversion, but here's a trick:

### Convert to Uppercase
```batch
@echo off
set text=hello
set "upper=%text:a=A%"
set "upper=%upper:b=B%"
set "upper=%upper:c=C%"
REM ... continue for all letters
echo %upper%
pause
```

**Better approach:** Just type text in uppercase when needed.

---

## Checking if String Contains Text

### Check for Substring
```batch
@echo off
set text=Hello World

echo %text% | findstr "World" > nul
if not errorlevel 1 (
    echo Contains "World"!
) else (
    echo Does not contain "World"
)
pause
```

### Check if Empty
```batch
@echo off
set text=

if "%text%"=="" (
    echo Text is empty!
) else (
    echo Text has content
)
pause
```

---

## Removing Spaces

### Trim Spaces
```batch
@echo off
set text=Hello World
set trimmed=%text: =%
echo %trimmed%       REM Shows: HelloWorld
pause
```

---

## Practical Examples

### Example 1: Filename Cleaner
```batch
@echo off
set /p filename=Enter filename: 
set cleanname=%filename: =_%
set cleanname=%cleanname:(=%
set cleanname=%cleanname:)=%
echo Clean filename: %cleanname%
pause
```

### Example 2: Email Validator (Basic)
```batch
@echo off
set /p email=Enter email: 

echo %email% | findstr "@" > nul
if errorlevel 1 (
    echo Invalid email!
) else (
    echo Email looks valid!
)
pause
```

### Example 3: Path Extractor
```batch
@echo off
set "fullpath=C:\Users\John\Documents\file.txt"

for %%f in ("%fullpath%") do (
    set "drive=%%~df"
    set "path=%%~pf"
    set "name=%%~nf"
    set "ext=%%~xf"
)

echo Drive: %drive%
echo Path: %path%
echo Name: %name%
echo Extension: %ext%
pause
```

### Example 4: Sentence Reverser
```batch
@echo off
set /p sentence=Enter short sentence: 
REM This is advanced - requires complex logic
echo Original: %sentence%
echo Reversing strings is complex in batch!
pause
```

### Example 5: Template Filler
```batch
@echo off
set /p name=Your name: 
set /p city=Your city: 
set /p hobby=Your hobby: 

echo.
echo ===== Profile =====
echo Hi! My name is %name%.
echo I live in %city%.
echo I like to %hobby%.
echo ===================
pause
```

---

## Common String Operations

### Get File Extension
```batch
set filename=report.pdf
set ext=%filename:~-3%
echo %ext%    REM Shows: pdf
```

### Remove Extension
```batch
set filename=report.pdf
set name=%filename:~0,-4%
echo %name%   REM Shows: report
```

### Change Extension
```batch
set filename=report.txt
set newname=%filename:.txt=.pdf%
echo %newname%   REM Shows: report.pdf
```

### Count Words (Approximate)
```batch
@echo off
set text=This is a test
set words=%text: =,%
set count=0
for %%w in (%words%) do set /a count+=1
echo Word count: %count%
pause
```

---

## String Comparison

### Exact Match
```batch
@echo off
set pass1=secret
set pass2=secret

if "%pass1%"=="%pass2%" (
    echo Passwords match!
) else (
    echo Passwords different!
)
pause
```

### Case Sensitive
```batch
@echo off
set text1=Hello
set text2=hello

if "%text1%"=="%text2%" (
    echo Same
) else (
    echo Different - batch is case sensitive!
)
pause
```

---

## Tips & Tricks 💡

### Tip 1: Always Quote Strings
```batch
set "text=Hello World"    REM Safe
set text=Hello World      REM Risky
```

### Tip 2: Escape Special Characters
```batch
set "path=C:\Folder^&Subfolder"   REM & needs ^
```

### Tip 3: Use Delayed Expansion for Complex Scripts
```batch
setlocal enabledelayedexpansion
set text=Hello
echo !text!    REM Use ! instead of %
endlocal
```

### Tip 4: Test String Operations
```batch
@echo off
set test=Example
echo Testing: [%test%]
pause
```

---

## Common Mistakes

### ❌ Forgetting Quotes
```batch
set path=C:\My Documents    REM Wrong
set "path=C:\My Documents"  REM Correct
```

### ❌ Wrong Substring Syntax
```batch
%text:0,5%      REM Wrong
%text:~0,5%     REM Correct
```

### ❌ Not Escaping Special Chars
```batch
set url=http://site.com?a=1&b=2    REM Wrong
set "url=http://site.com?a=1^&b=2"  REM Correct
```

---

## Practice Exercises

1. **Username Generator**: Combine first + last name
2. **File Extension Counter**: Count files by extension
3. **Message Formatter**: Add borders to messages
4. **URL Validator**: Check if URL starts with http
5. **Text Replacer**: Replace word in sentence

---

## Quick Reference

| Operation | Syntax | Example |
|-----------|--------|---------|
| Extract | `%var:~start,len%` | `%text:~0,5%` |
| Replace | `%var:old=new%` | `%text:cat=dog%` |
| Remove ext | `%var:~0,-4%` | `%file:~0,-4%` |
| Get ext | `%var:~-3%` | `%file:~-3%` |
| Combine | `%var1%%var2%` | `%first% %last%` |

---

**Strings are everywhere in programming!** Master these techniques! 🎯

**Next:** [Making Decisions](Making_Decisions.md)
