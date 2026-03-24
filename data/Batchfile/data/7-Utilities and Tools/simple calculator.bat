@echo off
setlocal

set /p a=Enter first number: 
set /p b=Enter second number: 

set /a sum=a+b
set /a diff=a-b
set /a mul=a*b

echo Sum: %sum%
echo Difference: %diff%
echo Product: %mul%

pause
