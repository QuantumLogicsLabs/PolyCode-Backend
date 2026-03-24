@echo off
setlocal enabledelayedexpansion

set "name=Batch Scripting"
set "text=Learning %name% is practical"

echo Original: !text!

echo First 8 chars: !text:~0,8!
echo Replace practical with useful: !text:practical=useful!

echo Upper-like comparison with IF /I
if /i "!name!"=="batch scripting" echo Names match (case-insensitive)
