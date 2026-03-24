# Complete Batch Scripting Reference

## Every Command You Need! 📚

Comprehensive reference for all batch commands.

---

## System Commands

| Command | Purpose | Example |
|---------|---------|---------|
| `ECHO` | Display message | `echo Hello` |
| `PAUSE` | Wait for key | `pause` |
| `TIMEOUT` | Wait seconds | `timeout /t 5` |
| `CLS` | Clear screen | `cls` |
| `EXIT` | Exit script | `exit /b 0` |
| `REM` | Add comment | `rem Note` |

---

## File Commands

| Command | Purpose | Example |
|---------|---------|---------|
| `COPY` | Copy files | `copy a.txt b\` |
| `MOVE` | Move files | `move file.txt folder\` |
| `DEL` | Delete files | `del *.tmp` |
| `REN` | Rename files | `ren old.txt new.txt` |
| `TYPE` | Show file | `type readme.txt` |
| `ATTRIB` | Change attributes | `attrib +r file.txt` |

---

## Directory Commands

| Command | Purpose | Example |
|---------|---------|---------|
| `CD` | Change directory | `cd Documents` |
| `MD` | Make directory | `mkdir NewFolder` |
| `RD` | Remove directory | `rd OldFolder /s` |
| `DIR` | List contents | `dir /a /w` |

---

## Control Commands

| Command | Purpose | Example |
|---------|---------|---------|
| `IF` | Conditional | `if exist file.txt echo Found` |
| `FOR` | Loop | `for %%f in (*.txt) do echo %%f` |
| `GOTO` | Jump to label | `goto MyLabel` |
| `CALL` | Call subroutine | `call :MyFunction` |
| `CHOICE` | Get choice | `choice /C YN` |

---

**Your complete batch command reference!** 🎯
