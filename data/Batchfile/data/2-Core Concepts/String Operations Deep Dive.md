# String Operations Deep Dive

## Advanced Text Manipulation! 📝

Master string operations in batch scripts.

---

## Substring Extraction

### Basic Syntax
```batch
%variable:~start,length%
```

### Examples
```batch
@echo off
set text=Hello World
echo %text:~0,5%     REM Hello
echo %text:~6%       REM World
echo %text:~-5%      REM World
echo %text:~0,1%     REM H
pause
```

---

## String Replacement

### Simple Replace
```batch
@echo off
set text=I love cats
echo %text:cats=dogs%
echo %text:love=hate%
pause
```

### Remove Text
```batch
@echo off
set filename=backup_old.txt
echo %filename:_old=%
pause
```

### Replace All
```batch
@echo off
set text=cat cat cat
echo %text:cat=dog%
pause
```

---

## Practical Examples

### Extract File Extension
```batch
@echo off
set filename=document.pdf
set ext=%filename:~-3%
echo Extension: %ext%
pause
```

### Remove Extension
```batch
@echo off
set filename=document.pdf
set name=%filename:~0,-4%
echo Name: %name%
pause
```

### Change Extension
```batch
@echo off
set filename=file.txt
set newname=%filename:.txt=.pdf%
echo %newname%
pause
```

---

**Become a string manipulation expert!** 🎯
