# 02 - Variables and Data Types

## 🎯 Learning Objectives

After completing this section, you will understand:
- How to create and use variables in PowerShell
- PowerShell data types and type casting
- Variable scope and naming conventions
- Special variables and automatic variables
- Best practices for variable usage

## 📝 Variables in PowerShell

Variables in PowerShell are used to store data values. They are created by assigning a value to a variable name that starts with a dollar sign (`$`).

### Creating Variables

```powershell
# Basic variable assignment
$name = "John Doe"
$age = 25
$price = 19.99
$isStudent = $true

# Variable names are case-insensitive
$myVar = "Hello"
$MYVAR = "Hello"  # Same variable
$myvar = "Hello"  # Same variable

# Variable naming rules
# - Start with a letter or underscore
# - Can contain letters, numbers, and underscores
# - Cannot contain spaces
# - PowerShell is case-insensitive

# Valid names
$userName = "Alice"
$User_Name = "Bob"
$_private = "secret"
$number123 = 42

# Invalid names
# $123number = "invalid"  # Cannot start with number
# $user-name = "invalid"  # Cannot contain hyphen
# $user name = "invalid"  # Cannot contain space
```

### Displaying Variables

```powershell
# Display variable value
$name

# Using Write-Output
Write-Output $name

# Using Write-Host (for display only)
Write-Host "Hello, $name"

# Using string interpolation
Write-Host "User: $name, Age: $age"

# Using format strings
Write-Host ("User: {0}, Age: {1}" -f $name, $age)
```

## 🔢 Data Types

PowerShell is a dynamically typed language, but it supports strong typing when needed.

### Basic Data Types

```powershell
# String
$string = "Hello World"
$stringWithQuotes = "He said 'Hello'"
$multilineString = @"
This is a
multiline string
"@

# Integer
$integer = 42
$negativeInteger = -10

# Double (floating-point)
$double = 3.14159
$scientific = 1.5e-3

# Boolean
$booleanTrue = $true
$booleanFalse = $false

# Null
$nullValue = $null

# DateTime
$dateTime = Get-Date
$specificDate = [DateTime]"2023-12-25"
```

### Strong Typing

```powershell
# Explicit type declaration
[string]$name = "John"  # Must be a string
[int]$age = 25          # Must be an integer
[bool]$isActive = $true # Must be boolean

# Type casting
$numberAsString = "123"
$numberAsInt = [int]$numberAsString  # Cast to integer

$stringFromNumber = [string]$42      # Cast to string

# Type checking
$age.GetType().Name
$age -is [int]
$age -is [string]
```

## 📊 Common Data Types

### Numeric Types

```powershell
# Integer types
[Byte]$byte = 255
[Int16]$short = 32767
[Int32]$int = 2147483647
[Int64]$long = 9223372036854775807

# Floating-point types
[Single]$float = 3.14
[Double]$double = 3.141592653589793
[Decimal]$decimal = 123.45

# Check numeric ranges
[Byte]::MaxValue
[Int32]::MaxValue
[Int64]::MaxValue
```

### String Types

```powershell
# Regular string
$singleQuote = 'Hello $name'      # Literal string
$doubleQuote = "Hello $name"      # Interpolated string

# Here-strings (multiline)
$hereStringSingle = @'
This is a here-string
Variables are not expanded: $name
'@

$hereStringDouble = @"
This is a here-string
Variables are expanded: $name
"@

# String methods
$text = "PowerShell is awesome"
$text.Length
$text.ToUpper()
$text.Replace("awesome", "powerful")
$text.Contains("Power")
$text.Split(" ")
```

### Array Types

```powershell
# Simple array
$numbers = 1, 2, 3, 4, 5
$names = "Alice", "Bob", "Charlie"

# Array with explicit type
[string[]]$stringArray = "one", "two", "three"
[int[]]$intArray = 1, 2, 3

# Empty array
$emptyArray = @()

# Array operations
$numbers[0]           # First element
$numbers[-1]          # Last element
$numbers.Count        # Number of elements
$numbers.Length       # Same as Count

# Adding elements
$numbers += 6         # Add to end (creates new array)
```

### Hashtable Types

```powershell
# Creating hashtables
$user = @{
    Name = "John Doe"
    Age = 30
    Email = "john@example.com"
}

# Accessing values
$user.Name
$user["Name"]

# Adding/Modifying values
$user.City = "New York"
$user["Age"] = 31

# Hashtable properties
$user.Count
$user.Keys
$user.Values
```

## 🌟 Special Variables

### Automatic Variables

```powershell
# Current object in pipeline
$_  # Used in script blocks and loops

# True/False constants
$true
$false

# Null
$null

# Current directory
$PWD  # Same as Get-Location

# Home directory
$HOME

# PowerShell version
$PSVersionTable

# Error information
$Error          # Array of recent errors
$Error[0]       # Most recent error
$?              # Success status of last command

# Execution information
$PID            # Current process ID
$MyInvocation   # Information about current command

# Preferences
$VerbosePreference
$ErrorActionPreference
$WarningPreference
```

### Preference Variables

```powershell
# Control output behavior
$VerbosePreference = "Continue"      # Show verbose messages
$DebugPreference = "Continue"        # Show debug messages
$WarningPreference = "Continue"      # Show warnings
$ErrorActionPreference = "Stop"      # Stop on errors

# Possible values: Continue, Stop, SilentlyContinue, Inquire, Ignore
```

## 🔍 Variable Scope

PowerShell variables have different scopes that determine where they can be accessed.

### Scope Types

```powershell
# Global scope - accessible everywhere
$Global:globalVar = "I am global"

# Script scope - accessible within the current script
$Script:scriptVar = "I am script-scoped"

# Local scope - default scope
$localVar = "I am local"

# Private scope - only accessible in current scope
$Private:privateVar = "I am private"
```

### Scope Demonstration

```powershell
function Test-Scope {
    $localVar = "Inside function"
    Write-Host "Function: $localVar"
    
    # Access global variable
    Write-Host "Global: $Global:globalVar"
    
    # Create script-scoped variable
    $Script:scriptVar = "Set from function"
}

# Before function call
Write-Host "Before: $localVar"  # Will be empty or error

Test-Scope

# After function call
Write-Host "After: $Script:scriptVar"  # Accessible
# Write-Host "After: $localVar"  # Not accessible
```

## 🎯 Type Accelerators

Type accelerators are shortcuts for .NET types:

```powershell
# Common type accelerators
[string]    # System.String
[int]       # System.Int32
[long]      # System.Int64
[bool]      # System.Boolean
[datetime]  # System.DateTime
[regex]     # System.Text.RegularExpressions.Regex
[xml]       # System.Xml.XmlDocument
[scriptblock] # System.Management.Automation.ScriptBlock
[array]     # System.Array
[hashtable] # System.Collections.Hashtable

# Using type accelerators
$pattern = [regex]"\d+"
$xmlDoc = [xml]"<root><item>test</item></root>"
$script = [scriptblock]{ Get-Process }
```

## 📝 Variable Best Practices

### Naming Conventions

```powershell
# Good naming practices
$userName           # CamelCase for variables
$User-Name          # PascalCase with hyphen (PowerShell style)
$connectionString   # Descriptive names
$isEnabled          # Boolean prefix with "is"
$hasPermission      # Boolean prefix with "has"

# Avoid
$n                  # Too short
$temp               # Not descriptive
$data1, $data2      # Use descriptive names instead
```

### Initialization

```powershell
# Always initialize variables
$count = 0           # Not $count = $null
$isActive = $false   # Not $isActive = $null
$items = @()         # Empty array, not $null

# Use default values
$timeout = 30        # Default timeout in seconds
$retryCount = 3      # Default retry count
```

### Type Safety

```powershell
# Use explicit types when appropriate
[string]$filePath = "C:\temp\file.txt"
[int]$port = 8080
[bool]$verbose = $false

# Validate input
if ($age -is [int] -and $age -gt 0) {
    Write-Host "Valid age: $age"
}
```

## 🚀 Practical Examples

### Example 1: User Information System

```powershell
# Create user profile with typed variables
[string]$firstName = "John"
[string]$lastName = "Doe"
[int]$age = 30
[string]$email = "john.doe@example.com"
[bool]$isActive = $true
[datetime]$joinDate = Get-Date

# Create user object
$user = @{
    FirstName = $firstName
    LastName = $lastName
    FullName = "$firstName $lastName"
    Age = $age
    Email = $email
    IsActive = $isActive
    JoinDate = $joinDate
}

# Display user information
Write-Host "User Profile:"
Write-Host "Name: $($user.FullName)"
Write-Host "Age: $($user.Age)"
Write-Host "Email: $($user.Email)"
Write-Host "Status: $(if ($user.IsActive) { 'Active' } else { 'Inactive' })"
Write-Host "Member Since: $($user.JoinDate.ToShortDateString())"
```

### Example 2: Configuration Management

```powershell
# Configuration variables
$Global:Config = @{
    Database = @{
        Server = "localhost"
        Port = 5432
        Name = "myapp"
        Username = "admin"
        Timeout = 30
    }
    Logging = @{
        Level = "Info"
        File = "C:\logs\app.log"
        MaxSize = 10MB
    }
    Features = @{
        EnableCache = $true
        EnableDebug = $false
        MaxConnections = 100
    }
}

# Access configuration
$dbServer = $Global:Config.Database.Server
$logLevel = $Global:Config.Logging.Level
$cacheEnabled = $Global:Config.Features.EnableCache

Write-Host "Database: $dbServer"
Write-Host "Log Level: $logLevel"
Write-Host "Cache Enabled: $cacheEnabled"
```

### Example 3: Data Processing

```powershell
# Process data with typed variables
[string[]]$fileNames = @("data1.txt", "data2.txt", "data3.txt")
[int]$totalLines = 0
[hashtable]$fileStats = @()

foreach ($fileName in $fileNames) {
    # Simulate file processing
    $lines = Get-Random -Minimum 100 -Maximum 1000
    $totalLines += $lines
    
    $fileStats[$fileName] = @{
        Lines = $lines
        SizeKB = [math]::Round($lines * 50 / 1KB, 2)
        Processed = (Get-Date)
    }
}

# Display results
Write-Host "Total files processed: $($fileNames.Count)"
Write-Host "Total lines: $totalLines"
Write-Host "Average lines per file: $([math]::Round($totalLines / $fileNames.Count, 0))"

foreach ($file in $fileStats.GetEnumerator()) {
    Write-Host "$($file.Key): $($file.Value.Lines) lines, $($file.Value.SizeKB) KB"
}
```

## 📝 Exercises

### Exercise 1: Variable Types
1. Create variables for each basic data type (string, int, bool, double)
2. Display the type of each variable using `.GetType().Name`
3. Try to cast a string to an integer and handle any errors

### Exercise 2: User Profile
1. Create a user profile with at least 5 properties
2. Use appropriate data types for each property
3. Display the profile in a formatted way

### Exercise 3: Configuration
1. Create a configuration hashtable for an application
2. Include nested hashtables for different sections
3. Access and display specific configuration values

## 🎯 Key Takeaways

- Variables start with `$` and are **case-insensitive**
- PowerShell is **dynamically typed** but supports **strong typing**
- Use **descriptive names** and follow naming conventions
- Understand **variable scope** (Global, Script, Local, Private)
- Use **type accelerators** for .NET types
- **Initialize variables** before use
- Use **explicit types** when type safety is important

## 🔄 Next Steps

Move on to [03-Operators](03-operators.md) to learn how to work with variables using PowerShell operators.
