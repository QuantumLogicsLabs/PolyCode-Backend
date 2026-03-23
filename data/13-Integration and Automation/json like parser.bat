@echo off
setlocal enabledelayedexpansion

set "line={\"name\":\"saad\",\"role\":\"admin\"}"
set "line=!line:{=!"
set "line=!line:}=!"
set "line=!line:\"=!"

for %%P in (!line:,= !) do (
  for /f "tokens=1,2 delims=:" %%K in ("%%P") do (
    echo %%K = %%L
  )
)
