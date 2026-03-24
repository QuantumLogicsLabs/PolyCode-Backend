# 03 - Operators

## 🎯 Learning Objectives

After completing this section, you will understand:
- Arithmetic operators and mathematical operations
- Comparison operators for evaluating conditions
- Logical operators for combining conditions
- Assignment operators and variable assignment
- Special operators unique to PowerShell
- Operator precedence and evaluation order

## 🔢 Arithmetic Operators

Arithmetic operators perform mathematical operations on numeric values.

### Basic Arithmetic

```powershell
# Addition
$result = 10 + 5        # $result = 15

# Subtraction
$result = 10 - 5        # $result = 5

# Multiplication
$result = 10 * 5        # $result = 50

# Division
$result = 10 / 5        # $result = 2

# Modulus (remainder)
$result = 10 % 3        # $result = 1

# Exponentiation
$result = 2 ^ 3         # $result = 8

# Unary operators
$result = +5            # Positive 5
$result = -5            # Negative 5
$result = -$result      # Negate value
```

### Advanced Arithmetic

```powershell
# Integer division
[int]$result = 10 / 3   # $result = 3 (truncated)

# Floating point division
[double]$result = 10 / 3 # $result = 3.33333333333333

# Mathematical functions
[Math]::Abs(-5)         # Absolute value: 5
[Math]::Pow(2, 3)       # Power: 8
[Math]::Sqrt(16)        # Square root: 4
[Math]::Round(3.14159, 2) # Round: 3.14
[Math]::Floor(3.7)      # Floor: 3
[Math]::Ceiling(3.2)    # Ceiling: 4
```

### String Arithmetic

```powershell
# String concatenation
$firstName = "John"
$lastName = "Doe"
$fullName = $firstName + " " + $lastName  # "John Doe"

# String multiplication
$separator = "-" * 20  # "--------------------"

# String and number concatenation
$text = "Value: " + 42  # "Value: 42"
```

## ⚖️ Comparison Operators

Comparison operators compare values and return boolean results (`$true` or `$false`).

### Equality Operators

```powershell
# Equal
5 -eq 5              # $true
"hello" -eq "hello"  # $true
5 -eq "5"            # $true (PowerShell converts)

# Not equal
5 -ne 3              # $true
"hello" -ne "world"  # $true

# Case-sensitive equality
"Hello" -ceq "hello" # $false
"Hello" -cne "hello" # $true

# Case-insensitive equality (default)
"Hello" -ieq "hello" # $true
"Hello" -ine "hello" # $false
```

### Relational Operators

```powershell
# Greater than
10 -gt 5             # $true
10 -gt 10            # $false

# Greater than or equal
10 -ge 10            # $true
10 -ge 5             # $true

# Less than
5 -lt 10             # $true
5 -lt 5              # $false

# Less than or equal
5 -le 5              # $true
5 -le 10             # $true
```

### Pattern Matching Operators

```powershell
# Like (wildcard matching)
"PowerShell" -like "*Power*"     # $true
"PowerShell" -like "Power*"      # $true
"PowerShell" -like "?ower*"      # $true

# Not like
"PowerShell" -notlike "*Shell*"  # $false

# Case-sensitive like
"PowerShell" -clike "*power*"    # $false
"PowerShell" -ilike "*power*"    # $true

# Regular expression matching
"PowerShell7" -match "PowerShell\d+"  # $true
"abc123" -match "\d+"                 # $true (contains digits)

# Not match
"PowerShell" -notmatch "\d+"          # $true

# Case-sensitive match
"PowerShell" -cmatch "powershell"     # $false
"PowerShell" -imatch "powershell"     # $true
```

### Collection Operators

```powershell
# Contains (operator works on collections)
$array = 1, 2, 3, 4, 5
$array -contains 3        # $true
$array -contains 6        # $false

# Not contains
$array -notcontains 3     # $false

# In (reverse of contains)
3 -in $array              # $true
6 -in $array              # $false

# Not in
3 -notin $array           # $false
6 -notin $array           # $true
```

## 🧠 Logical Operators

Logical operators combine multiple conditions and return boolean results.

### Basic Logical Operators

```powershell
# AND
$true -and $true          # $true
$true -and $false         # $false
(5 -gt 3) -and (10 -lt 20)  # $true

# OR
$true -or $false          # $true
$false -or $false         # $false
(5 -gt 10) -or (10 -lt 20)  # $true

# NOT (logical negation)
-not $true               # $false
-not $false              # $true
!$true                   # $false (alternative syntax)

# XOR (exclusive or)
$true -xor $true         # $false
$true -xor $false        # $true
$false -xor $false       # $false
```

### Short-Circuit Evaluation

```powershell
# AND short-circuits (stops if first is false)
$false -and (Write-Host "This won't execute")  # Only shows $false

# OR short-circuits (stops if first is true)
$true -or (Write-Host "This won't execute")    # Only shows $true

# Practical example
$file = "test.txt"
if ((Test-Path $file) -and (Get-Content $file)) {
    Write-Host "File exists and has content"
}
```

## ➕ Assignment Operators

Assignment operators assign values to variables.

### Basic Assignment

```powershell
# Simple assignment
$x = 10
$y = "Hello"

# Assignment with operation
$x += 5      # $x = $x + 5 (15)
$x -= 3      # $x = $x - 3 (12)
$x *= 2      # $x = $x * 2 (24)
$x /= 3      # $x = $x / 3 (8)
$x %= 3      # $x = $x % 3 (2)

# String concatenation assignment
$text = "Hello"
$text += " World"    # "Hello World"

# Multiple assignment
$a, $b, $c = 1, 2, 3  # $a=1, $b=2, $c=3

# Swap variables
$a, $b = $b, $a
```

### Increment and Decrement

```powershell
# Increment
$x = 5
$x++              # $x = 6 (post-increment)
++$x              # $x = 7 (pre-increment)

# Decrement
$x = 5
$x--              # $x = 4 (post-decrement)
--$x              # $x = 3 (pre-decrement)

# In expressions
$y = $x++         # $y gets original value, then $x increments
$y = ++$x         # $x increments first, then $y gets new value
```

## 🔧 Special Operators

PowerShell includes special operators for specific tasks.

### Range Operator

```powershell
# Generate sequence of numbers
1..5               # 1, 2, 3, 4, 5
5..1               # 5, 4, 3, 2, 1

# Reverse range
10..1 | ForEach-Object { $_ }

# With variables
$start = 1
$end = 10
$start..$end        # 1 through 10
```

### Redirection Operators

```powershell
# Output redirection
Get-Process > output.txt          # Redirect to file (overwrite)
Get-Process >> output.txt         # Append to file

# Error redirection
Get-Process 2> errors.txt         # Redirect errors
Get-Process 2>> errors.txt        # Append errors

# Both output and errors
Get-Process *> all.txt            # Redirect all streams
Get-Process 2>&1                  # Redirect errors to output

# Suppress output
Get-Process > $null               # Discard output
Get-Process 2> $null              # Discard errors
```

### Split and Join Operators

```powershell
# Split operator (regex)
$text = "apple,banana,cherry"
$fruits = $text -split ","        # ["apple", "banana", "cherry"]

# Split with regex
$text = "apple, banana; cherry|date"
$items = $text -split "[,\s;|]+"  # ["apple", "banana", "cherry", "date"]

# Join operator
$fruits = "apple", "banana", "cherry"
$text = $fruits -join ", "        # "apple, banana, cherry"

# Join with different separator
$items = 1, 2, 3, 4, 5
$text = $items -join "-"          # "1-2-3-4-5"
```

### Format Operator

```powershell
# Format operator (similar to string.Format)
$name = "John"
$age = 25
$text = "Name: {0}, Age: {1}" -f $name, $age  # "Name: John, Age: 25"

# Number formatting
$number = 1234.5678
$text = "Value: {0:F2}" -f $number            # "Value: 1234.57"
$text = "Hex: 0x{0:X}" -f 255                 # "Hex: 0xFF"

# Date formatting
$date = Get-Date
$text = "Date: {0:yyyy-MM-dd}" -f $date       # "Date: 2023-12-25"
```

### Type Operators

```powershell
# Is operator (type checking)
$number = 42
$number -is [int]           # $true
$number -is [string]        # $false

# IsNot operator
$number -isnot [string]     # $true

# As operator (type casting)
$text = "123"
$number = $text -as [int]   # 123 (or $null if conversion fails)

# Safe casting
$value = "abc"
$number = $value -as [int]  # $null (conversion failed)
```

### Call Operator

```powershell
# Call operator (&) to execute commands
$command = "Get-Process"
& $command                  # Executes Get-Process

# Execute script blocks
$scriptBlock = { Get-Service }
& $scriptBlock              # Executes the script block

# Execute with arguments
$command = "Write-Host"
& $command "Hello World"    # Write-Host "Hello World"

# Use with variable command names
$cmd = "notepad.exe"
& $cmd                      # Opens Notepad
```

## 📊 Operator Precedence

Operators are evaluated in order of precedence (highest to lowest):

```powershell
# 1. Parentheses ()
# 2. Properties and methods . []
# 3. Unary operators + - ! ++ --
# 4. Multiplicative * / %
# 5. Additive + -
# 6. Comparison -like -match -notlike -notmatch
# 7. Equality -eq -ne -ceq -cne -ieq -ine
# 8. AND -and
# 9. OR -or -xor
# 10. Assignment = += -= *= /= %=

# Example showing precedence
$result = 2 + 3 * 4        # 14 (multiplication first)
$result = (2 + 3) * 4      # 20 (parentheses first)

# Complex expression
$age = 25
$score = 85
$result = ($age -ge 18) -and ($score -ge 80) -or ($score -eq 100)
# Evaluates as: ((age >= 18) AND (score >= 80)) OR (score == 100)
```

## 🚀 Practical Examples

### Example 1: Grade Calculator

```powershell
function Get-Grade {
    param([int]$Score)
    
    $grade = switch ($Score) {
        { $_ -ge 90 } { "A" }
        { $_ -ge 80 } { "B" }
        { $_ -ge 70 } { "C" }
        { $_ -ge 60 } { "D" }
        default { "F" }
    }
    
    return $grade
}

# Test the function
$scores = 95, 82, 74, 61, 45
foreach ($score in $scores) {
    $grade = Get-Grade -Score $score
    Write-Host "Score: $score, Grade: $grade"
}
```

### Example 2: File Size Analyzer

```powershell
function Analyze-FileSize {
    param([string]$Path)
    
    if (-not (Test-Path $Path)) {
        Write-Host "File not found: $Path"
        return
    }
    
    $file = Get-Item $Path
    $sizeKB = [math]::Round($file.Length / 1KB, 2)
    $sizeMB = [math]::Round($file.Length / 1MB, 2)
    
    $category = switch ($file.Length) {
        { $_ -lt 1KB } { "Small" }
        { $_ -lt 1MB } { "Medium" }
        { $_ -lt 10MB } { "Large" }
        default { "Very Large" }
    }
    
    Write-Host "File: $($file.Name)"
    Write-Host "Size: $sizeKB KB ($sizeMB MB)"
    Write-Host "Category: $category"
}

# Test with different files
Analyze-FileSize -Path "C:\Windows\notepad.exe"
```

### Example 3: User Input Validation

```powershell
function Test-UserInput {
    param([string]$Input)
    
    # Check if input is not empty
    if ([string]::IsNullOrEmpty($Input)) {
        return "Input cannot be empty"
    }
    
    # Check if input is a number
    if ($Input -match '^\d+$') {
        $number = [int]$Input
        if ($number -lt 0) {
            return "Number must be positive"
        }
        if ($number -gt 100) {
            return "Number must be 100 or less"
        }
        return "Valid number: $number"
    }
    
    # Check if input is a valid email
    if ($Input -match '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$') {
        return "Valid email: $Input"
    }
    
    # Check if input contains only letters
    if ($Input -match '^[a-zA-Z]+$') {
        if ($Input.Length -lt 2) {
            return "Name must be at least 2 characters"
        }
        return "Valid name: $Input"
    }
    
    return "Invalid input format"
}

# Test various inputs
$tests = "", "123", "abc", "test@example.com", "-5", "150"
foreach ($test in $tests) {
    $result = Test-UserInput -Input $test
    Write-Host "Input: '$test' -> $result"
}
```

### Example 4: System Monitor

```powershell
function Get-SystemStatus {
    $cpu = Get-Counter '\Processor(_Total)\% Processor Time' -ErrorAction SilentlyContinue
    $memory = Get-Counter '\Memory\Available MBytes' -ErrorAction SilentlyContinue
    $disk = Get-Counter '\PhysicalDisk(_Total)\% Disk Time' -ErrorAction SilentlyContinue
    
    $status = "OK"
    $issues = @()
    
    if ($cpu -and $cpu.CounterSamples.CookedValue -gt 80) {
        $issues += "High CPU usage: $($cpu.CounterSamples.CookedValue:F1)%"
        $status = "Warning"
    }
    
    if ($memory -and $memory.CounterSamples.CookedValue -lt 1000) {
        $issues += "Low memory: $($memory.CounterSamples.CookedValue:F1) MB available"
        $status = "Critical"
    }
    
    if ($disk -and $disk.CounterSamples.CookedValue -gt 90) {
        $issues += "High disk usage: $($disk.CounterSamples.CookedValue:F1)%"
        if ($status -eq "OK") { $status = "Warning" }
    }
    
    $result = @{
        Status = $status
        CPU = if ($cpu) { "{0:F1}%" -f $cpu.CounterSamples.CookedValue } else { "N/A" }
        Memory = if ($memory) { "{0:F1} MB" -f $memory.CounterSamples.CookedValue } else { "N/A" }
        Disk = if ($disk) { "{0:F1}%" -f $disk.CounterSamples.CookedValue } else { "N/A" }
        Issues = $issues
    }
    
    return $result
}

# Get and display system status
$systemStatus = Get-SystemStatus
Write-Host "System Status: $($systemStatus.Status)"
Write-Host "CPU Usage: $($systemStatus.CPU)"
Write-Host "Available Memory: $($systemStatus.Memory)"
Write-Host "Disk Usage: $($systemStatus.Disk)"

if ($systemStatus.Issues.Count -gt 0) {
    Write-Host "Issues:"
    $systemStatus.Issues | ForEach-Object { Write-Host "  - $_" }
}
```

## 📝 Exercises

### Exercise 1: Calculator
Create a simple calculator that:
1. Takes two numbers as input
2. Performs all arithmetic operations
3. Displays results with proper formatting

### Exercise 2: Password Validator
Create a password validator that checks:
1. Minimum length of 8 characters
2. Contains at least one uppercase letter
3. Contains at least one lowercase letter
4. Contains at least one number
5. Contains at least one special character

### Exercise 3: File Filter
Create a script that:
1. Gets files from a directory
2. Filters files based on size and extension
3. Uses comparison and logical operators
4. Displays results in a formatted way

## 🎯 Key Takeaways

- **Arithmetic operators** perform mathematical operations
- **Comparison operators** evaluate conditions and return booleans
- **Logical operators** combine multiple conditions
- **Assignment operators** assign and modify variable values
- **Special operators** provide unique PowerShell functionality
- **Operator precedence** determines evaluation order
- Use **parentheses** to control evaluation order when needed

## 🔄 Next Steps

Move on to [04-Conditional Statements](04-conditional-statements.md) to learn how to control program flow based on conditions.
