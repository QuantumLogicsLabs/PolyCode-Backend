# Batch Script Examples

Practical examples for common batch scripting tasks.

## Beginner Examples

### 1. Hello World
```batch
@echo off
echo Hello, World!
pause
```

### 2. Simple Calculator
```batch
@echo off
set /p num1=Enter first number: 
set /p num2=Enter second number: 
set /a sum=num1+num2
echo Sum: %sum%
pause
```

### 3. File Backup Script
```batch
@echo off
set source=C:\ImportantFiles
set backup=C:\Backup\%date:~-4,4%%date:~-7,2%%date:~-10,2%

if not exist "%backup%" mkdir "%backup%"
xcopy "%source%\*.*" "%backup%" /E /I /Y

echo Backup completed to %backup%
pause
```

## Intermediate Examples

### 4. Menu System
```batch
@echo off
:menu
cls
echo ========== Main Menu ==========
echo 1. Option One
echo 2. Option Two
echo 3. Option Three
echo 4. Exit
echo ================================
set /p choice=Enter your choice (1-4): 

if "%choice%"=="1" goto option1
if "%choice%"=="2" goto option2
if "%choice%"=="3" goto option3
if "%choice%"=="4" goto end
goto menu

:option1
echo You selected Option One
pause
goto menu

:option2
echo You selected Option Two
pause
goto menu

:option3
echo You selected Option Three
pause
goto menu

:end
echo Goodbye!
```

### 5. Text File Processor
```batch
@echo off
setlocal enabledelayedexpansion

set "inputfile=input.txt"
set "outputfile=output.txt"
set "counter=0"

if not exist "%inputfile%" (
    echo Error: %inputfile% not found!
    pause
    goto :EOF
)

> "%outputfile%" (
    for /f "delims=" %%a in (%inputfile%) do (
        set /a counter+=1
        set "line=%%a"
        echo !counter!: !line!
    )
)

echo Processed %counter% lines
echo Output saved to %outputfile%
pause
```

### 6. System Information Collector
```batch
@echo off
set "report=system_report.txt"

echo System Information Report > "%report%"
echo Generated: %date% %time% >> "%report%"
echo ================================ >> "%report%"
echo. >> "%report%"

echo Computer Name: %computername% >> "%report%"
echo Username: %username% >> "%report%"
echo Current Directory: %cd% >> "%report%"
echo. >> "%report%"

ver >> "%report%"
echo. >> "%report%"

systeminfo | findstr /C:"OS Name" /C:"OS Version" /C:"Total Physical Memory" >> "%report%"
echo. >> "%report%"

ipconfig | findstr /C:"IPv4" >> "%report%"
echo. >> "%report%"

echo Report saved to %report%
pause
```

## Advanced Examples

### 7. Advanced Error Handling
```batch
@echo off
setlocal enabledelayedexpansion

call :SetErrorTrap

echo Starting process...
call :DoSomething
if errorlevel 1 (
    echo Error occurred in DoSomething
    call :ErrorHandler "DoSomething failed"
    goto :EOF
)

echo Process completed successfully
goto :EOF

:SetErrorTrap
set "errortrap=on"
goto :EOF

:ErrorHandler
echo [%date% %time%] ERROR: %~1
echo [%date% %time%] ERROR: %~1 >> error.log
exit /b 1

:DoSomething
rem Your code here
exit /b 0
```

### 8. Array Implementation
```batch
@echo off
setlocal enabledelayedexpansion

rem Create array
set "array[0]=First"
set "array[1]=Second"
set "array[2]=Third"
set "array[3]=Fourth"
set "array[4]=Fifth"
set "arrayLength=5"

rem Access array elements
echo Array contents:
for /l %%i in (0,1,%arrayLength%-1) do (
    echo Index %%i: !array[%%i]!
)

rem Search in array
set "search=Third"
set "found=0"
for /l %%i in (0,1,%arrayLength%-1) do (
    if "!array[%%i]!"=="%search%" (
        echo Found '%search%' at index %%i
        set "found=1"
    )
)

if !found! equ 0 (
    echo '%search%' not found
)

pause
```

### 9. Network Ping Monitor
```batch
@echo off
setlocal enabledelayedexpansion

set "hosts=google.com github.com stackoverflow.com"
set "logfile=ping_monitor.log"

echo Network Monitor - %date% %time% > "%logfile%"
echo ========================================= >> "%logfile%"

for %%h in (%hosts%) do (
    echo Pinging %%h...
    ping -n 1 -w 1000 %%h | findstr /C:"Reply" > nul
    
    if errorlevel 1 (
        echo [FAIL] %%h - %date% %time% >> "%logfile%"
        echo [FAIL] %%h
    ) else (
        echo [OK] %%h - %date% %time% >> "%logfile%"
        echo [OK] %%h
    )
)

echo.
echo Log saved to %logfile%
pause
```

### 10. Batch-PowerShell Integration
```batch
@echo off
echo Running PowerShell command from batch...

powershell -Command "Get-Process | Sort-Object CPU -Descending | Select-Object -First 5 | Format-Table Name, CPU, WorkingSet"

echo.
echo Getting system info via PowerShell...
powershell -Command "Get-WmiObject Win32_OperatingSystem | Select-Object Caption, Version, OSArchitecture"

pause
```

## Templates

### Basic Script Template
```batch
@echo off
setlocal enabledelayedexpansion

rem Script: script_name.bat
rem Description: What the script does
rem Author: Your name
rem Date: YYYY-MM-DD

rem Initialize variables
set "var1=value1"
set "var2=value2"

rem Main logic
call :Function1
call :Function2

goto :EOF

:Function1
echo Function 1 executed
exit /b 0

:Function2
echo Function 2 executed
exit /b 0
```

### Error Handling Template
```batch
@echo off
setlocal enabledelayedexpansion

set "scriptName=%~nx0"
set "logFile=%~dp0%scriptName%.log"

call :Log "INFO" "Script started"

rem Your code here
call :MainProcess

if errorlevel 1 (
    call :Log "ERROR" "Script failed with errorlevel %errorlevel%"
    exit /b %errorlevel%
)

call :Log "INFO" "Script completed successfully"
exit /b 0

:MainProcess
rem Main processing logic
exit /b 0

:Log
rem Usage: call :Log "LEVEL" "Message"
echo [%date% %time%] [%~1] %~2 >> "%logFile%"
echo [%date% %time%] [%~1] %~2
exit /b 0
```

## Tips for Writing Good Batch Scripts

1. **Always use `@echo off`** - Cleaner output
2. **Comment your code** - Explain what each section does
3. **Validate inputs** - Check if files/paths exist
4. **Handle errors** - Use errorlevel checks
5. **Use meaningful variable names** - Easier to understand
6. **Test thoroughly** - Try different scenarios
7. **Quote paths** - Handle spaces in paths
8. **Clean up** - Use `endlocal` if you used `setlocal`

## Related Resources

- [Basics](../01_Basics/) - Learn the fundamentals
- [Intermediate](../02_Intermediate/) - Build your skills
- [Advanced](../03_Advanced/) - Master complex techniques
- [Command Reference](../04_Reference/) - Complete command documentation

## Runnable Folder Examples

### Beginner (`Examples/Beginner`)
- `hello world.bat`
- `user input and greeting.bat`
- `simple calculator.bat`
- `file exists check.bat`
- `for loop counter.bat`

### Intermediate (`Examples/Intermediate`)
- `menu driven tool.bat`
- `file backup copy.bat`
- `logging helper.bat`
- `csv reader.bat`
- `retry command.bat`

### Advanced (`Examples/Advanced`)
- `recursive cleaner.bat`
- `error handling pattern.bat`
- `argument parser.bat`
- `config driven runner.bat`
- `json like parser.bat`

### Real World (`Examples/Real World`)
- `daily project backup.bat`
- `system health report.bat`
- `bulk rename by extension.bat`
- `disk cleanup helper.bat`
- `network ping monitor.bat`
