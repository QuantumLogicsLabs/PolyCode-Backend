# Data Operations Guide

## Master Data Manipulation! 🔢

Work effectively with strings and numbers.

---

## String Operations

### Extract Substrings
```batch
@echo off
set text=Hello World
echo First 5: %text:~0,5%
echo Last 5: %text:~-5%
echo From pos 6: %text:~6%
pause
```

### Replace Text
```batch
@echo off
set message=I love cats
echo %message:cats=dogs%
echo %message:love=hate%
pause
```

### Remove Characters
```batch
@echo off
set filename=backup_old.txt
set clean=%filename:_old=%
echo %clean%
pause
```

---

## Number Operations

### Math Functions
```batch
@echo off
set /a "sum=10+5"
set /a "diff=10-5"
set /a "prod=10*5"
set /a "quot=10/5"
set /a "mod=10%%3"
set /a "power=2^3"

echo Sum: %sum%
echo Product: %prod%
echo Power: %power%
pause
```

### Format Numbers
```batch
@echo off
set num=5
set formatted=00%num%
set formatted=%formatted:~-3%
echo %formatted%    REM Shows: 005
pause
```

---

## Practical Examples

### Age Calculator
```batch
@echo off
set /p birthyear=Birth year: 
set /a age=2026-birthyear
echo You are %age% years old!
pause
```

### Tip Calculator
```batch
@echo off
set /p bill=Bill amount: 
set /a tip=bill*15/100
set /a total=bill+tip
echo Tip (15%%): %tip%
echo Total: %total%
pause
```

### Discount Calculator
```batch
@echo off
set /p price=Original price: 
set /p discount=Discount %%: 
set /a save=price*discount/100
set /a final=price-save
echo You save: %save%
echo Pay: %final%
pause
```

---

**Master data manipulation!** 🎯
