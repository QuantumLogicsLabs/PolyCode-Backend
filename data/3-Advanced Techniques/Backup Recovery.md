# Backup and Recovery Scripts

## Protect Your Data! 💾

Complete guide to backup operations in batch scripts.

---

## Simple Backup

### Basic File Backup
```batch
@echo off
set source=C:\Important
set dest=D:\Backup

if not exist "%dest%" mkdir "%dest%"
xcopy "%source%\*.*" "%dest%" /E /I /Y
echo Backup complete!
pause
```

### Date-Stamped Backup
```batch
@echo off
set date=%date:~-4,4%%date:~-7,2%%date:~-10,2%
set time=%time:~0,2%%time:~3,2%
set time=%time: =0%

set dest=D:\Backups\Backup_%date%_%time%

mkdir "%dest%"
xcopy "C:\Documents\*.*" "%dest%" /E /I /Y
echo Backup saved to %dest%
pause
```

---

## Advanced Backup

### Robocopy Backup (Recommended)
```batch
@echo off
robocopy "C:\Source" "D:\Dest" /E /COPYALL /R:3 /W:5 /NFL /NDL /NP
if errorlevel 8 (
    echo ERROR: Backup failed!
) else (
    echo SUCCESS: Backup completed!
)
pause
```

### Incremental Backup
```batch
@echo off
robocopy "C:\Data" "D:\Backup" /MIR /R:1 /W:1
echo Mirror backup complete!
pause
```

---

## Recovery Scripts

### Restore from Backup
```batch
@echo off
set backup=D:\Backup\Latest
set restore=C:\Restore

if not exist "%backup%" (
    echo ERROR: Backup not found!
    pause
    exit /b 1
)

xcopy "%backup%\*.*" "%restore%" /E /I /Y
echo Restore complete!
pause
```

### Selective Restore
```batch
@echo off
set backup=D:\Backup
set /p filetype=What to restore? (doc/xls/all): 

if "%filetype%"=="doc" (
    xcopy "%backup%\*.docx" "C:\Restore\" /S /I /Y
)
if "%filetype%"=="xls" (
    xcopy "%backup%\*.xlsx" "C:\Restore\" /S /I /Y
)
if "%filetype%"=="all" (
    xcopy "%backup%\*.*" "C:\Restore\" /E /I /Y
)

echo Restore complete!
pause
```

---

**Keep your data safe with automated backups!** 🎯
