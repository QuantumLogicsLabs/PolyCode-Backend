# File and Directory Operations

## Master File Management! 📁

Advanced techniques for working with files and folders.

---

## Creating Files and Folders

### Create Multiple Folders
```batch
@echo off
mkdir Project\src Project\lib Project\docs
echo Project structure created!
pause
```

### Create File with Content
```batch
@echo off
(
echo @echo off
echo echo Hello World
echo pause
) > newscript.bat
echo Script created!
pause
```

---

## Advanced File Operations

### Copy with Filters
```batch
@echo off
REM Copy only files modified today
xcopy src\ dest\ /D:0 /S /I
pause
```

### Move and Archive
```batch
@echo off
set date=%date:~-4,4%%date:~-7,2%%date:~-10,2%
mkdir Archive_%date%
move *.txt Archive_%date%\
move *.docx Archive_%date%\
echo Files archived!
pause
```

### Delete Safely
```batch
@echo off
if exist "*.tmp" (
    del /q "*.tmp"
    echo Temp files deleted
) else (
    echo No temp files found
)
pause
```

---

## Search and Find

### Find Files by Pattern
```batch
@echo off
dir /s /b *.txt
pause
```

### Find Empty Folders
```batch
@echo off
for /f "delims=" %%d in ('dir /ad /b') do (
    dir "%%d" /b > nul 2>&1
    if errorlevel 1 echo Empty: %%d
)
pause
```

---

## Practical Examples

### Backup Script
```batch
@echo off
set source=C:\Important
set dest=D:\Backup\%date:~-4,4%%date:~-7,2%%date:~-10,2%

if not exist "%dest%" mkdir "%dest%"
robocopy "%source%" "%dest%" /E /COPYALL /R:3 /W:5
echo Backup complete to %dest%
pause
```

### Cleanup Script
```batch
@echo off
echo Cleaning temporary files...
del /q/f/s %TEMP%\*
del /q/f/s C:\Windows\Temp\*
echo Cleanup complete!
pause
```

### Organizer Script
```batch
@echo off
for %%e in (jpg png gif) do (
    if exist "*.%%e" (
        if not exist "Images" mkdir Images
        move "*.%%e" Images\
    )
)
echo Images organized!
pause
```

---

**File operations are fundamental to automation!** 🎯
