# Basic PowerShell Examples
# Demonstrates fundamental concepts from sections 01-06

<#
.SYNOPSIS
    Collection of basic PowerShell examples demonstrating fundamental concepts
.DESCRIPTION
    This script contains practical examples for:
    - Basic cmdlets and pipeline
    - Variables and data types
    - Operators and comparisons
    - Conditional statements
    - Loops and iteration
    - Functions and parameters
.NOTES
    File:      basic-examples.ps1
    Author:    PowerShell Learning Guide
    Created:   2023-12-01
#>

# Write-Host examples
Write-Host "=== Basic Output Examples ===" -ForegroundColor Green
Write-Host "Hello, PowerShell!" -ForegroundColor Cyan
Write-Host "Multiple colors: " -NoNewline; Write-Host "Red" -ForegroundColor Red

# Get-Command examples
Write-Host "`n=== Command Discovery ===" -ForegroundColor Green
$processCommands = Get-Command -Noun Process
Write-Host "Found $($processCommands.Count) process-related commands:"
$processCommands | Select-Object -First 3 | ForEach-Object { Write-Host "  - $($_.Name)" }

# Pipeline examples
Write-Host "`n=== Pipeline Examples ===" -ForegroundColor Green
Write-Host "Top 5 processes by CPU usage:"
Get-Process | Sort-Object CPU -Descending | Select-Object -First 5 | Format-Table Name, CPU, WorkingSet -AutoSize

# Variable examples
Write-Host "`n=== Variable Examples ===" -ForegroundColor Green
$stringVar = "PowerShell"
$intVar = 42
$boolVar = $true
$arrayVar = "Apple", "Banana", "Cherry"
$hashtableVar = @{ Name = "John"; Age = 30; City = "New York" }

Write-Host "String: $stringVar"
Write-Host "Integer: $intVar"
Write-Host "Boolean: $boolVar"
Write-Host "Array first item: $($arrayVar[0])"
Write-Host "Hashtable name: $($hashtableVar.Name)"

# Data type examples
Write-Host "`n=== Data Type Examples ===" -ForegroundColor Green
$number = "123"
$convertedNumber = [int]$number
Write-Host "Original: $number (type: $($number.GetType().Name))"
Write-Host "Converted: $convertedNumber (type: $($convertedNumber.GetType().Name))"

# Strong typing
[string]$typedString = "Typed string"
[int]$typedInt = 100
Write-Host "Typed string: $typedString"
Write-Host "Typed int: $typedInt"

# Operator examples
Write-Host "`n=== Operator Examples ===" -ForegroundColor Green
$x = 10
$y = 3

Write-Host "Arithmetic: $x + $y = $($x + $y)"
Write-Host "Arithmetic: $x * $y = $($x * $y)"
Write-Host "Modulus: $x % $y = $($x % $y)"

Write-Host "Comparison: $x -gt $y = $($x -gt $y)"
Write-Host "Comparison: $x -eq $y = $($x -eq $y)"

Write-Host "Logical: ($x -gt 5) -and ($y -lt 5) = $(($x -gt 5) -and ($y -lt 5))"
Write-Host "Logical: ($x -gt 5) -or ($y -gt 5) = $(($x -gt 5) -or ($y -gt 5))"

# Conditional statement examples
Write-Host "`n=== Conditional Examples ===" -ForegroundColor Green
$age = 25

if ($age -lt 18) {
    Write-Host "Minor"
}
elseif ($age -lt 65) {
    Write-Host "Adult"
}
else {
    Write-Host "Senior"
}

# Switch statement
$day = "Monday"
switch ($day) {
    "Monday" { Write-Host "Start of the work week" }
    "Friday" { Write-Host "TGIF!" }
    "Saturday" { Write-Host "Weekend!" }
    "Sunday" { Write-Host "Weekend!" }
    default { Write-Host "Regular day" }
}

# Loop examples
Write-Host "`n=== Loop Examples ===" -ForegroundColor Green

# For loop
Write-Host "For loop (1-5):"
for ($i = 1; $i -le 5; $i++) {
    Write-Host "  Iteration $i"
}

# While loop
Write-Host "While loop (countdown):"
$count = 5
while ($count -gt 0) {
    Write-Host "  Countdown: $count"
    $count--
}

# ForEach loop
Write-Host "ForEach loop (fruits):"
$fruits = "Apple", "Banana", "Cherry"
foreach ($fruit in $fruits) {
    Write-Host "  Fruit: $fruit"
}

# Do-While loop
Write-Host "Do-While loop:"
$attempt = 1
do {
    Write-Host "  Attempt $attempt"
    $attempt++
} while ($attempt -le 3)

# Function examples
Write-Host "`n=== Function Examples ===" -ForegroundColor Green

# Simple function
function Write-Greeting {
    param([string]$Name)
    Write-Host "Hello, $Name!"
}

Write-Greeting -Name "PowerShell"

# Function with multiple parameters
function Calculate-Area {
    param(
        [double]$Length,
        [double]$Width
    )
    $area = $Length * $Width
    return $area
}

$rectangleArea = Calculate-Area -Length 10 -Width 5
Write-Host "Rectangle area: $rectangleArea"

# Function with parameter validation
function Get-UserInfo {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory=$true)]
        [ValidateLength(3, 20)]
        [string]$Username,
        
        [ValidateRange(18, 100)]
        [int]$Age,
        
        [ValidateSet("User", "Admin", "Guest")]
        [string]$Role = "User"
    )
    
    $user = [PSCustomObject]@{
        Username = $Username
        Age = $Age
        Role = $Role
        Timestamp = Get-Date
    }
    
    return $user
}

$user = Get-UserInfo -Username "john" -Age 25 -Role "Admin"
Write-Host "User: $($user.Username), Age: $($user.Age), Role: $($user.Role)"

# Function with return values
function Test-Number {
    param([int]$Number)
    
    if ($Number -gt 0) {
        return "Positive"
    }
    elseif ($Number -lt 0) {
        return "Negative"
    }
    else {
        return "Zero"
    }
}

Write-Host "Number 5 is: $(Test-Number -Number 5)"
Write-Host "Number -3 is: $(Test-Number -Number -3)"
Write-Host "Number 0 is: $(Test-Number -Number 0)"

# Array manipulation examples
Write-Host "`n=== Array Examples ===" -ForegroundColor Green
$numbers = 1..10
Write-Host "Numbers: $($numbers -join ', ')"

$evenNumbers = $numbers | Where-Object { $_ % 2 -eq 0 }
Write-Host "Even numbers: $($evenNumbers -join ', ')"

$squaredNumbers = $numbers | ForEach-Object { $_ * $_ }
Write-Host "Squared numbers: $($squaredNumbers -join ', ')"

$sum = ($numbers | Measure-Object -Sum).Sum
Write-Host "Sum of numbers: $sum"

# Hashtable examples
Write-Host "`n=== Hashtable Examples ===" -ForegroundColor Green
$settings = @{
    Server = "localhost"
    Port = 8080
    UseSSL = $true
    Timeout = 30
}

Write-Host "Settings:"
foreach ($key in $settings.Keys) {
    Write-Host "  $key = $($settings[$key])"
}

# Add to hashtable
$settings["RetryCount"] = 3
Write-Host "Added RetryCount: $($settings.RetryCount)"

# Check if key exists
if ($settings.ContainsKey("UseSSL")) {
    Write-Host "SSL is enabled: $($settings.UseSSL)"
}

Write-Host "`n=== Basic Examples Complete ===" -ForegroundColor Green
