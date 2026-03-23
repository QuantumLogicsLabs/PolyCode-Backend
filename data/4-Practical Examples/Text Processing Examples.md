# Text Processing Examples

## File Content Manipulation
```batch
:: Count lines in a file
set /a count=0
for /f %%a in (filename.txt) do set /a count+=1
echo Total lines: %count%

:: Search and replace in text files
powershell -Command "(Get-Content input.txt) -replace 'old', 'new' | Set-Content output.txt"

:: Extract specific columns from CSV
for /f "tokens=1,3 delims=," %%a in (data.csv) do echo %%a %%b
```

## Text Analysis
- Word counting
- Pattern matching
- Data extraction
- Format conversion

## Common Use Cases
- Log file processing
- Data cleaning
- Report generation
- Configuration file updates
