# More Practical Examples

## Additional Real-World Scripts! 🚀

More useful batch script examples.

---

## Automation Scripts

### Auto File Sorter
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
for %%e in (mp3 wav ogg) do (
    if not exist Music mkdir Music
    move *.%%e Music\ > nul
)
echo Files sorted!
pause
```

### Daily Reminder
```batch
@echo off
echo ===== Daily Tasks =====
echo Date: %date%
echo.
echo To Do:
echo - Check emails
echo - Review code
echo - Update documentation
echo.
pause
```

---

## Fun Scripts

### ASCII Art Banner
```batch
@echo off
echo.
echo  ____  _                  _       
echo | __ )(_)_ __ ___  _   _| | ___  
echo |  _ \| | '_ ` _ \| | | | |/ _ \ 
echo | |_) | | | | | | | |_| | | (_) |
echo |____/|_|_| |_| |_|\__,_|_|\___/ 
echo.
pause
```

### Magic 8-Ball
```batch
@echo off
set /p question=Ask a yes/no question: 
set /a rand=%random% %% 5

if %rand% equ 0 echo Yes, definitely!
if %rand% equ 1 echo Maybe later
if %rand% equ 2 echo No way
if %rand% equ 3 echo Ask again
if %rand% equ 4 echo Definitely not!

pause
```

---

## Utility Scripts

### Quick Notes
```batch
@echo off
set /p note=Enter note: 
echo [%date% %time%] %note% >> notes.txt
echo Note saved!
pause
```

### Password Generator
```batch
@echo off
set chars=ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%%^&*
set pass=

for /l %%i in (1,1,12) do (
    set /a rand=!random! %% 70
    for %%c in (!chars:~!rand!,1!) do set pass=!pass!%%c
)

echo Generated password: %pass%
pause
```

---

**Expand your script collection!** 🎯
