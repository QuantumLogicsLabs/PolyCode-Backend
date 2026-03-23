# Environment Variables Quick Reference

## Essential Variables at a Glance

---

## Most Important Variables

| Variable | What It Is | When to Use |
|----------|------------|-------------|
| `%CD%` | Current folder | Get script location |
| `%DATE%` | Today's date | Timestamp files |
| `%TIME%` | Current time | Log entries |
| `%USERNAME%` | Your login | User-specific paths |
| `%TEMP%` | Temp folder | Temporary files |
| `%PATH%` | Program locations | Find executables |

---

## File System Variables

### User Folders
```
%USERPROFILE%      = C:\Users\YourName
%HOMEDRIVE%        = C:
%HOMEPATH%         = \Users\YourName
%PUBLIC%           = C:\Users\Public
```

### Program Folders
```
%PROGRAMFILES%     = C:\Program Files
%PROGRAMFILES(X86)%= C:\Program Files (x86)
%PROGRAMDATA%      = C:\ProgramData
%COMMONPROGRAMFILES%= C:\Program Files\Common Files
```

### App Data Folders
```
%APPDATA%          = C:\Users\You\AppData\Roaming
%LOCALAPPDATA%     = C:\Users\You\AppData\Local
```

---

## System Variables

### Windows Location
```
%SYSTEMROOT%       = C:\Windows
%WINDIR%           = C:\Windows
%SYSTEMDRIVE%      = C:
```

### Computer Info
```
%COMPUTERNAME%     = Your-PC-Name
%PROCESSOR_ARCHITECTURE% = AMD64
%NUMBER_OF_PROCESSORS%   = 4 (example)
```

### Execution
```
%COMSPEC%          = C:\Windows\System32\cmd.exe
%OS%               = Windows_NT
%PATHEXT%          = .COM;.EXE;.BAT;.CMD;...
```

---

## Common Uses

### Create Timestamped Filename
```batch
set "filename=backup_%date:~-4,4%%date:~-7,2%%date:~-10,2%_%time:~0,2%%time:~3,2%.zip"
set "filename=%filename: =0%"
```

### Navigate to User Folder
```batch
cd /d "%USERPROFILE%\Documents"
```

### Add to PATH
```batch
set "PATH=%PATH%;C:\MyFolder"
```

### Check if Variable Exists
```batch
if defined MYVAR echo Variable exists
```

---

## Date/Time Formatting

| Code | Returns | Example |
|------|---------|---------|
| `%date:~-4,4%` | Year | `2024` |
| `%date:~-7,2%` | Month | `01` |
| `%date:~-10,2%` | Day | `15` |
| `%time:~0,2%` | Hour | `14` |
| `%time:~3,2%` | Minute | `30` |
| `%time:~6,2%` | Second | `45` |

---

Keep this handy for quick reference! 📋
