# Command Usage Examples

## `DIR`
```batch
dir /b
dir /s "C:\\Temp"
```

## `COPY` and `XCOPY`
```batch
copy /y "config.ini" "backup\\config.ini"
xcopy "src\\*" "backup\\src\\" /e /i /y
```

## `FINDSTR`
```batch
findstr /i "error" app.log
findstr /r "^[0-9][0-9]*$" values.txt
```

## `TASKLIST` and `TASKKILL`
```batch
tasklist | findstr /i "notepad"
taskkill /im notepad.exe /f
```

## `ROBOCOPY`
```batch
robocopy "D:\\Projects" "E:\\Backups\\Projects" /e /r:1 /w:1
```
