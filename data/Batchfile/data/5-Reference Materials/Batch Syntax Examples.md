# Batch Syntax Examples

## Variables
```batch
set "project=Batchfile"
echo %project%
```

## IF / ELSE
```batch
if exist "notes.txt" (
  echo Found notes.txt
) else (
  echo notes.txt not found
)
```

## FOR /F
```batch
for /f "tokens=1,2 delims=," %%A in ("users.csv") do (
  echo Name=%%A Role=%%B
)
```

## CALL labels (functions)
```batch
call :greet "Saad"
exit /b 0

:greet
echo Hello, %~1
exit /b 0
```

## Redirection and pipes
```batch
dir /b > files.txt
type files.txt | findstr /i "report"
```
