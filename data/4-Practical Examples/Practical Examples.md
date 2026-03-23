# Batch Scripting Examples

## Real-World Automation Scripts! 🚀

Practical examples you can use immediately.

---

## File Management

### Example 1: Quick Backup
```batch
@echo off
set source=C:\Important
set dest=D:\Backup\%date:~-4,4%%date:~-7,2%%date:~-10,2%

if not exist "%dest%" mkdir "%dest%"
xcopy "%source%\*.*" "%dest%" /E /I /Y
echo Backup complete to %dest%!
pause
```

### Example 2: File Organizer
```batch
@echo off
for %%e in (jpg png gif bmp) do (
    if not exist Images mkdir Images
    move *.%%e Images\ > nul
)
for %%e in (doc docx pdf txt) do (
    if not exist Documents mkdir Documents
    move *.%%e Documents\ > nul
)
echo Files organized!
pause
```

### Example 3: Bulk Renamer
```batch
@echo off
set /p prefix=Enter prefix: 
set count=0

for %%f in (*.txt) do (
    set /a count+=1
    ren "%%f" "%prefix%_!count!.txt"
    echo Renamed: %%f
)
echo Total renamed: %count%
pause
```

---

## System Utilities

### Example 4: System Cleaner
```batch
@echo off
echo Cleaning temporary files...
del /q/f/s %TEMP%\*
del /q/f/s C:\Windows\Temp\*
echo Cleanup complete!
pause
```

### Example 5: Info Reporter
```batch
@echo off
(
echo ===== System Report =====
echo Date: %date%
echo Time: %time%
echo Computer: %computername%
echo User: %username%
echo Folder: %cd%
) > report.txt
notepad report.txt
pause
```

---

## Fun & Games

### Example 6: Quiz Game
```batch
@echo off
set score=0

echo Question 1: What is 5+3?
set /p a=Answer: 
if "%a%"=="8" (
    echo Correct!
    set /a score+=1
) else (
    echo Wrong!
)

echo Question 2: What is 10-4?
set /p a=Answer: 
if "%a%"=="6" (
    echo Correct!
    set /a score+=1
)

echo.
echo Score: %score%/2
pause
```

### Example 7: Mad Libs
```batch
@echo off
set /p adj=Adjective: 
set /p noun=Noun: 
set /p verb=Verb: 

echo.
echo The %adj% %noun% decided to %verb% all day!
pause
```

---

**Practice with these real examples!** 🎯
