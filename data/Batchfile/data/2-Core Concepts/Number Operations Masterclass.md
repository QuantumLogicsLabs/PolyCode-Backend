# Number Operations Masterclass

## Math in Batch! 🔢

Complete guide to numeric operations.

---

## Basic Arithmetic

### SET /A Command
```batch
@echo off
set /a sum=10+5
set /a diff=10-5
set /a prod=10*5
set /a quot=10/5
set /a mod=10%%3

echo Sum: %sum%
echo Difference: %diff%
echo Product: %prod%
echo Quotient: %quot%
echo Remainder: %mod%
pause
```

### Increment/Decrement
```batch
@echo off
set count=0
set /a count+=1    REM Add 1
set /a count-=1    REM Subtract 1
set /a count*=2    REM Multiply by 2
set /a count/=2    REM Divide by 2
echo Count: %count%
pause
```

---

## Advanced Math

### Exponents
```batch
@echo off
set /a power=2^3
set /a square=5^2
echo 2^3 = %power%
echo 5^2 = %square%
pause
```

### Bitwise Operations
```batch
@echo off
set /a and=5 & 3
set /a or=5 | 3
set /a xor=5 ^ 3
echo AND: %and%
echo OR: %or%
echo XOR: %xor%
pause
```

---

## Practical Examples

### Percentage Calculator
```batch
@echo off
set /p bill=Bill amount: 
set /p percent=Tip percentage: 
set /a tip=bill*percent/100
set /a total=bill+tip
echo Tip: %tip%
echo Total: %total%
pause
```

### Temperature Converter
```batch
@echo off
set /p c=Celsius: 
set /a f=c*9/5+32
echo Fahrenheit: %f%
pause
```

### Age Calculator
```batch
@echo off
set /p birth=Birth year: 
set /a age=2026-birth
echo Age: %age%
pause
```

---

**Master batch math operations!** 🎯
