# Looping with FOR

## Repeat Without Rewriting! 🔄

Loops let you do the same thing multiple times without rewriting code.

Imagine you need to process 100 files. Would you write:
```batch
copy file1.txt backup\
copy file2.txt backup\
copy file3.txt backup\
... (100 times)
```

NO! Use a loop instead! 😊

---

## Basic FOR Loop

### Simple Syntax
```batch
FOR %%variable IN (set) DO command
```

### Example - Count 1 to 5:
```batch
@echo off
for /l %%i in (1,1,5) do (
    echo Number %%i
)
pause
```

**Output:**
```
Number 1
Number 2
Number 3
Number 4
Number 5
```

---

## FOR /L - Counting Loop

### Syntax
```batch
FOR /L %%variable IN (start,step,end) DO command
```

### Examples:

**Count 1 to 10:**
```batch
for /l %%i in (1,1,10) do echo %%i
```

**Count by 2s (2,4,6,8,10):**
```batch
for /l %%i in (2,2,10) do echo %%i
```

**Countdown (10 to 1):**
```batch
for /l %%i in (10,-1,1) do echo %%i
```

### Complete Example:
```batch
@echo off
echo Counting down...
for /l %%i in (10,-1,1) do (
    echo %%i
)
echo BLAST OFF!
pause
```

---

## FOR Each File in Folder

### Process All Files
```batch
@echo off
for %%f in (*.txt) do (
    echo Processing %%f
    copy "%%f" "backup\"
)
pause
```

### What Happens:
1. Finds all .txt files
2. For each file:
   - Shows filename
   - Copies to backup folder

### More Examples:

**List All Text Files:**
```batch
@echo off
echo Text files in this folder:
for %%f in (*.txt) do echo - %%f
pause
```

**Rename All JPG Files:**
```batch
@echo off
for %%f in (*.jpg) do (
    ren "%%f" "backup_%%f"
    echo Renamed %%f
)
pause
```

**Delete All TMP Files:**
```batch
@echo off
for %%f in (*.tmp) do (
    del "%%f"
    echo Deleted %%f
)
pause
```

---

## FOR Meta-Variables (File Information)

### Get File Parts
```batch
@echo off
for %%f in ("C:\Users\John\document.txt") do (
    echo Full path: %%f
    echo Drive: %%~df
    echo Path: %%~pf
    echo Name: %%~nf
    echo Extension: %%~xf
)
pause
```

### Modifiers:
| Modifier | Returns | Example |
|----------|---------|---------|
| `%%~fI` | Full path | `C:\folder\file.txt` |
| `%%~dI` | Drive letter | `C:` |
| `%%~pI` | Path only | `\folder\` |
| `%%~nI` | Filename | `file` |
| `%%~xI` | Extension | `.txt` |
| `%%~zI` | File size | `1024` |

### Practical Example:
```batch
@echo off
echo File Report:
echo ============
for %%f in (*.txt) do (
    echo File: %%~nf
    echo Extension: %%~xf
    echo Size: %%~zf bytes
    echo Location: %%~pf
    echo.
)
pause
```

---

## FOR /F - Read File Line by Line

### Basic Syntax
```batch
FOR /F ["options"] %%variable IN (file) DO command
```

### Read Text File:
```batch
@echo off
for /f "delims=" %%line in (data.txt) do (
    echo Line: %%line
)
pause
```

### Options:
- `"delims="` - No delimiter (read entire line)
- `"delims=,"` - Comma is delimiter
- `"tokens=1,2"` - Get first two tokens
- `"skip=2"` - Skip first 2 lines

### Example - CSV Reader:
```batch
@echo off
for /f "tokens=1,2 delims=," %%a in (data.csv) do (
    echo Name: %%a, Age: %%b
)
pause
```

### Example - Skip Header:
```batch
@echo off
for /f "skip=1 delims=" %%line in (report.txt) do (
    echo %%line
)
pause
```

---

## Nested Loops

### Loop Inside Another Loop
```batch
@echo off
for /l %%i in (1,1,3) do (
    echo Outer: %%i
    for /l %%j in (1,1,3) do (
        echo   Inner: %%j
    )
)
pause
```

**Output:**
```
Outer: 1
  Inner: 1
  Inner: 2
  Inner: 3
Outer: 2
  Inner: 1
  Inner: 2
  Inner: 3
Outer: 3
  Inner: 1
  Inner: 2
  Inner: 3
```

---

## Practical Examples

### Example 1: File Counter
```batch
@echo off
set count=0

for %%f in (*.txt) do (
    set /a count+=1
    echo Processing file #!count!: %%f
)

echo Total files: %count%
pause
```

### Example 2: Bulk Renamer
```batch
@echo off
set /p prefix=Enter prefix: 
set num=1

for %%f in (*.jpg) do (
    ren "%%f" "%prefix%_!num!.jpg"
    set /a num+=1
)

echo All files renamed!
pause
```

### Example 3: Create Numbered Files
```batch
@echo off
for /l %%i in (1,1,10) do (
    type nul > "file_%%i.txt"
    echo Created file_%%i.txt
)
echo 10 files created!
pause
```

### Example 4: Folder Creator
```batch
@echo off
for /l %%i in (1,1,5) do (
    mkdir "Folder_%%i"
    echo Created Folder_%%i
)
pause
```

### Example 5: Progress Bar
```batch
@echo off
setlocal enabledelayedexpansion
for /l %%i in (1,1,100) do (
    <nul set /p ".=Progress: %%i%% &#13;"
)
echo Complete!
pause
```

---

## Loop Through List

### Custom List
```batch
@echo off
for %%day in (Monday Tuesday Wednesday Thursday Friday) do (
    echo Day: %%day
)
pause
```

### Multiple Extensions
```batch
@echo off
for %%ext in (jpg png gif bmp) do (
    for %%f in (*.%%ext) do (
        echo Image: %%f
    )
)
pause
```

---

## Tips & Tricks 💡

### Tip 1: Use Delayed Expansion in Loops
```batch
setlocal enabledelayedexpansion
set count=0
for %%f in (*.txt) do (
    set /a count+=1
    echo !count!   REM Use ! not %
)
endlocal
```

### Tip 2: Test with Small Sets First
```batch
for /l %%i in (1,1,3) do echo Test %%i
REM Works? Then change to (1,1,100)
```

### Tip 3: Use Parentheses for Multiple Commands
```batch
for %%f in (*.txt) do (
    echo %%f
    copy "%%f" backup\
)
```

### Tip 4: Suppress Output with > nul
```batch
for %%f in (*.txt) do (
    copy "%%f" backup\ > nul
    echo Copied %%f
)
```

---

## Common Mistakes

### ❌ Wrong Variable Syntax
```batch
for %i in (*.txt) do echo %i    REM Wrong in batch file
for %%i in (*.txt) do echo %%i  REM Correct
```

### ❌ Missing IN
```batch
for %%i (*.txt) do echo %%i     REM Wrong
for %%i in (*.txt) do echo %%i  REM Correct
```

### ❌ Bad Range
```batch
for /l %%i in (1,10) do echo %%i    REM Wrong (missing end)
for /l %%i in (1,1,10) do echo %%i  REM Correct
```

---

## Practice Exercises

1. **Number Generator**: Print numbers 1-50
2. **Even Numbers**: Print 2,4,6,...,20
3. **File Backup**: Copy all .docx to backup
4. **Image Organizer**: Move all images to Images folder
5. **Report Generator**: Create 10 numbered reports

---

## Quick Reference

| Loop Type | Syntax | Example |
|-----------|--------|---------|
| Counting | `for /l %%i in (start,step,end)` | `for /l %%i in (1,1,10)` |
| Files | `for %%f in (*.ext)` | `for %%f in (*.txt)` |
| Read file | `for /f "delims=" %%l in (file)` | `for /f %%l in (data.txt)` |
| List | `for %%item in (a b c)` | `for %%d in (Mon Tue Wed)` |

---

**Loops are POWERFUL!** Master them and automate anything! 💪

**Next:** Start practicing with real projects! 🚀
