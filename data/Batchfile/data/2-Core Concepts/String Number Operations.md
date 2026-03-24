# String and Number Operations

## Master Data Manipulation! 🔢

Learn to work with text and numbers like a pro!

---

## Advanced String Operations

### Extract Substrings
```batch
@echo off
set text=Hello World
echo First 5 chars: %text:~0,5%
echo Last 5 chars: %text:~-5%
echo From position 6: %text:~6%
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

### Increment/Decrement
```batch
@echo off
set count=0
set /a count+=1    REM Add 1
set /a count-=1    REM Subtract 1
set /a count*=2    REM Multiply by 2
echo Count: %count%
pause
```

---

## Format Numbers

### Add Leading Zeros
```batch
@echo off
set num=5
set formatted=00%num%
set formatted=%formatted:~-3%
echo %formatted%    REM Shows: 005
pause
```

### Percentage Calculation
```batch
@echo off
set /a percent=75*100/300
echo %percent%%%    REM Shows: 25%
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

## Conversion Tricks

### String to Number
```batch
@echo off
set str=123
set /a num=str+0
echo %num%    REM Now treated as number
pause
```

### Boolean Logic
```batch
@echo off
set /a result=5 & 3    REM AND
set /a result=5 | 3    REM OR
set /a result=5 ^ 3    REM XOR
echo Result: %result%
pause
```

---

**Data manipulation is essential for powerful scripts!** 🎯
