@echo off
setlocal

set /a total=0

for /l %%N in (1,1,5) do (
  set /a total+=%%N
)

echo Total from 1 to 5 = %total%

if %total% geq 10 (
  echo Total is greater than or equal to 10
) else (
  echo Total is less than 10
)
