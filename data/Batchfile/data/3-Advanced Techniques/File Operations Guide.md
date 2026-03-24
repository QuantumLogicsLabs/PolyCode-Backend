# Advanced File Operations

## Master File Manipulation! 📁

Complete guide to advanced file and directory operations.

---

## Copy Operations

### Robocopy - Robust Copy
```batch
@echo off
robocopy "C:\Source" "D:\Dest" /E /COPYALL /R:3 /W:5
echo Backup complete!
pause
```

**Options:**
- `/E` - Copy subdirectories (including empty)
- `/COPYALL` - Copy all file attributes
- `/R:3` - Retry 3 times on failure
- `/W:5` - Wait 5 seconds between retries

### XCopy - Extended Copy
```batch
@echo off
xcopy "C:\Docs\*.docx" "D:\Backup\" /S /I /Y
echo Documents backed up!
pause
```

---

## Move Operations

### Move with Filters
```batch
@echo off
for %%e in (jpg png gif) do (
    if not exist Images mkdir Images
    move *.%%e Images\
)
echo Images organized!
pause
```

### Archive Old Files
```batch
@echo off
set date=%date:~-4,4%%date:~-7,2%%date:~-10,2%
mkdir Archive_%date%
move *.txt Archive_%date%\
move *.docx Archive_%date%\
echo Files archived!
pause
```

---

## Delete Operations

### Safe Delete
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

### Delete Tree
```batch
@echo off
rd /s /q "OldFolder"
echo Folder removed!
pause
```

---

## Search and Find

### Find Files
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

**Master file operations for powerful automation!** 🎯
