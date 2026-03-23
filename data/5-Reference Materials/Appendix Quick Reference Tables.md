# Quick Reference Tables

## Fast Lookup for Common Batch Operations

---

## File Operations

| Command | Syntax | Example |
|---------|--------|---------|
| Copy | `copy source dest` | `copy file.txt backup\` |
| Move | `move source dest` | `move old.txt archive\` |
| Delete | `del file` | `del temp.txt` |
| Rename | `ren oldname newname` | `ren test.txt final.txt` |
| Create Folder | `mkdir folder` | `mkdir NewFolder` |
| Remove Folder | `rmdir /s /q folder` | `rmdir /s /q OldFolder` |

---

## String Operations

| Operation | Code | Result |
|-----------|------|--------|
| Substring | `%var:~start,len%` | `%text:~0,5%` = first 5 chars |
| Replace | `%var:old=new%` | `%text:cat=dog%` |
| Length | `%var:~0,-1%` | All but last char |
| Uppercase | Manual replace | `set str=%str:a=A%` (repeat) |

---

## Math Operations

| Operation | Code | Example |
|-----------|------|---------|
| Add | `set /a a+b` | `set /a sum=5+3` = 8 |
| Subtract | `set /a a-b` | `set /a diff=5-3` = 2 |
| Multiply | `set /a a*b` | `set /a prod=5*3` = 15 |
| Divide | `set /a a/b` | `set /a quot=6/3` = 2 |
| Modulo | `set /a a%%b` | `set /a mod=7%%3` = 1 |
| Power | `set /a a^b` | `set /a pow=2^3` = 8 |
| Random | `%%RANDOM%%` | `%%RANDOM%% %% 10 + 1` = 1-10 |

---

## Comparison Operators

| Operator | Meaning | Example |
|----------|---------|---------|
| EQU | Equal | `if %a% equ 5` |
| NEQ | Not equal | `if %a% neq 5` |
| LSS | Less than | `if %a% lss 10` |
| LEQ | Less or equal | `if %a% leq 10` |
| GTR | Greater than | `if %a% gtr 10` |
| GEQ | Greater or equal | `if %a% geq 10` |

---

## Special Characters

| Character | Escape | Usage |
|-----------|--------|-------|
| & | ^& | AND / command separator |
| | | ^| | Pipe |
| < | ^< | Input redirect |
| > | ^> | Output redirect |
| % | %% | Variable / modulo |
| ! | ^! | Delayed expansion |
| ^ | ^^ | Literal caret |

---

## Loop Types

| Type | Syntax | Use Case |
|------|--------|----------|
| Count | `for /l %%i in (start,step,end)` | Repeat N times |
| Files | `for %%f in (*.txt)` | Process files |
| Lines | `for /f "tokens=*" %%a` | Read file lines |
| Directories | `for /d %%d in (*)` | Process folders |

---

## Common Commands

| Category | Commands |
|----------|----------|
| Display | echo, cls, title, color |
| Control | if, for, goto, call, exit |
| Variables | set, setlocal, endlocal |
| Files | copy, move, del, ren, mkdir |
| System | dir, cd, md, rd, type |
| Input/Output | echo, set /p, pause, timeout |

---

## Error Levels

| Value | Typical Meaning |
|-------|-----------------|
| 0 | Success |
| 1 | General error |
| 2 | File not found |
| 3 | Path not found |
| 5 | Access denied |
| 9009 | Command not found |

---

Keep this handy for quick lookups! 📊
