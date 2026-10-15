@echo off
setlocal enabledelayedexpansion

set "line=name=saad,role=admin"
echo Line value is: !line!

for %%P in (!line:,= !) do (
  for /f "tokens=1,2 delims==" %%K in ("%%P") do (
    echo %%K = %%L
  )
)

pause