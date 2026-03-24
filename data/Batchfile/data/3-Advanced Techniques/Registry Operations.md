# Registry Operations in Batch Files

## Reading Registry Values
```batch
:: Read a value
reg query "HKLM\SOFTWARE\Microsoft\Windows\CurrentVersion" /v ProductName

:: Store in variable
for /f "tokens=3*" %%a in ('reg query "HKLM\SOFTWARE\Microsoft\Windows\CurrentVersion" /v ProductName') do set "productName=%%a %%b"
echo %productName%
```

## Writing Registry Values
```batch
:: Add a new key
reg add "HKCU\Software\MyApp" /f

:: Set a value
reg add "HKCU\Software\MyApp" /v "Setting1" /t REG_SZ /d "Value1" /f

:: Set DWORD value
reg add "HKCU\Software\MyApp" /v "Number" /t REG_DWORD /d 42 /f
```

## Safety Considerations
- Always backup registry before making changes
- Test on non-production systems first
- Use /f flag with caution
- Handle permission issues gracefully
