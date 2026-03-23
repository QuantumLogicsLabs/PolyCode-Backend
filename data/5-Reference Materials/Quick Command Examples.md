# Quick Command Examples

## File checks
```batch
if exist "report.txt" echo Found
if not exist "output" mkdir "output"
```

## User input
```batch
set /p username=Enter username: 
echo Hello, %username%
```

## Math
```batch
set /a a=10
set /a b=3
set /a result=a*b+b
```

## Timestamp log line
```batch
>> app.log echo [%date% %time%] Job completed
```

## Loop files
```batch
for %%F in (*.txt) do echo %%F
```
