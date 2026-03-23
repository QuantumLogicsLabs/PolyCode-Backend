@echo off
setlocal

set "report=%~dp0system_report.txt"

echo System Health Report > "%report%"
echo Generated: %date% %time%>> "%report%"
echo.>> "%report%"

echo [Hostname]>> "%report%"
hostname>> "%report%"
echo.>> "%report%"

echo [OS Info]>> "%report%"
systeminfo | findstr /i /c:"OS Name" /c:"OS Version">> "%report%"
echo.>> "%report%"

echo [Disk Usage]>> "%report%"
wmic logicaldisk get caption,freespace,size>> "%report%"
echo.>> "%report%"

echo [Top running processes]>> "%report%"
tasklist | more +3>> "%report%"

echo Report created: %report%
endlocal
