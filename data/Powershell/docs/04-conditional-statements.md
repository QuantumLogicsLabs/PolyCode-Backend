# 04 - Conditional Statements

## 🎯 Learning Objectives

After completing this section, you will understand:
- How to use If/Else statements for decision making
- ElseIf conditions for multiple branches
- Switch statements for complex conditions
- Ternary operators in PowerShell
- Best practices for conditional logic
- Real-world examples and use cases

## 🔀 If/Else Statements

If/Else statements execute code blocks based on whether a condition evaluates to true or false.

### Basic If Statement

```powershell
# Simple if statement
$age = 25
if ($age -ge 18) {
    Write-Host "You are an adult"
}

# If with multiple statements
if ($age -ge 18) {
    Write-Host "You are an adult"
    Write-Host "You can vote"
    Write-Host "You can drive"
}
```

### If/Else Statement

```powershell
# If/Else structure
$age = 16
if ($age -ge 18) {
    Write-Host "You are an adult"
}
else {
    Write-Host "You are a minor"
}

# Example with file checking
$filePath = "C:\temp\test.txt"
if (Test-Path $filePath) {
    Write-Host "File exists: $filePath"
    $content = Get-Content $filePath
    Write-Host "File has $($content.Count) lines"
}
else {
    Write-Host "File does not exist: $filePath"
}
```

### If/ElseIf/Else Structure

```powershell
# Multiple conditions
$score = 85
if ($score -ge 90) {
    Write-Host "Grade: A"
}
elseif ($score -ge 80) {
    Write-Host "Grade: B"
}
elseif ($score -ge 70) {
    Write-Host "Grade: C"
}
elseif ($score -ge 60) {
    Write-Host "Grade: D"
}
else {
    Write-Host "Grade: F"
}

# Complex example with system checks
function Test-SystemHealth {
    $cpuUsage = Get-Random -Minimum 20 -Maximum 95
    $memoryUsage = Get-Random -Minimum 30 -Maximum 90
    $diskUsage = Get-Random -Minimum 40 -Maximum 85
    
    Write-Host "CPU: $cpuUsage%, Memory: $memoryUsage%, Disk: $diskUsage%"
    
    if ($cpuUsage -gt 90 -or $memoryUsage -gt 90 -or $diskUsage -gt 90) {
        Write-Host "Status: CRITICAL - System resources critically low"
        return "Critical"
    }
    elseif ($cpuUsage -gt 80 -or $memoryUsage -gt 80 -or $diskUsage -gt 80) {
        Write-Host "Status: WARNING - System resources high"
        return "Warning"
    }
    elseif ($cpuUsage -gt 70 -or $memoryUsage -gt 70 -or $diskUsage -gt 70) {
        Write-Host "Status: CAUTION - Monitor system resources"
        return "Caution"
    }
    else {
        Write-Host "Status: GOOD - System resources normal"
        return "Good"
    }
}

Test-SystemHealth
```

## 🔄 Switch Statements

Switch statements are useful when you have many conditions to check against the same value.

### Basic Switch Statement

```powershell
# Simple switch
$day = "Monday"
switch ($day) {
    "Monday" { Write-Host "Start of the work week" }
    "Tuesday" { Write-Host "Second day of work" }
    "Wednesday" { Write-Host "Mid-week" }
    "Thursday" { Write-Host "Almost Friday" }
    "Friday" { Write-Host "TGIF!" }
    "Saturday" { Write-Host "Weekend!" }
    "Sunday" { Write-Host "Weekend!" }
    default { Write-Host "Unknown day" }
}
```

### Switch with Multiple Values

```powershell
# Multiple values in same block
$month = "July"
switch ($month) {
    { $_ -in @("December", "January", "February") } {
        Write-Host "Winter"
    }
    { $_ -in @("March", "April", "May") } {
        Write-Host "Spring"
    }
    { $_ -in @("June", "July", "August") } {
        Write-Host "Summer"
    }
    { $_ -in @("September", "October", "November") } {
        Write-Host "Fall"
    }
    default {
        Write-Host "Unknown month"
    }
}
```

### Switch with Wildcards

```powershell
# Switch with wildcard patterns
$filename = "report_final_v2.docx"
switch -Wildcard ($filename) {
    "*.txt" { Write-Host "Text file" }
    "*.docx" { Write-Host "Word document" }
    "*.xlsx" { Write-Host "Excel spreadsheet" }
    "*_final*" { Write-Host "Final version" }
    "*_draft*" { Write-Host "Draft version" }
    default { Write-Host "Other file type" }
}
```

### Switch with Regex

```powershell
# Switch with regular expressions
$input = "user123@example.com"
switch -Regex ($input) {
    '^\d+$' { Write-Host "Numeric input" }
    '^[a-zA-Z]+$' { Write-Host "Alphabetic input" }
    '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$' { 
        Write-Host "Email address" 
    }
    '^https?://' { Write-Host "URL" }
    default { Write-Host "Other input type" }
}
```

### Switch with Case Sensitivity

```powershell
# Case-sensitive switch
$text = "PowerShell"
switch -CaseSensitive ($text) {
    "powershell" { Write-Host "Lowercase" }
    "POWERSHELL" { Write-Host "Uppercase" }
    "PowerShell" { Write-Host "Proper case" }
    default { Write-Host "Unknown case" }
}

# Case-insensitive (default)
switch -CaseInsensitive ($text) {
    "powershell" { Write-Host "Matches case-insensitive" }
    "POWERSHELL" { Write-Host "Matches case-insensitive" }
    "PowerShell" { Write-Host "Matches case-insensitive" }
}
```

## 🎯 Advanced Conditional Techniques

### Nested Conditions

```powershell
# Nested if statements
$age = 25
$hasLicense = $true
$hasCar = $false

if ($age -ge 18) {
    Write-Host "You are old enough to drive"
    if ($hasLicense) {
        Write-Host "You have a license"
        if ($hasCar) {
            Write-Host "You can drive your car"
        }
        else {
            Write-Host "You need a car to drive"
        }
    }
    else {
        Write-Host "You need a license to drive"
    }
}
else {
    Write-Host "You are too young to drive"
}

# Better approach with logical operators
if ($age -ge 18 -and $hasLicense -and $hasCar) {
    Write-Host "You can drive your car"
}
elseif ($age -ge 18 -and $hasLicense -and -not $hasCar) {
    Write-Host "You need a car to drive"
}
elseif ($age -ge 18 -and -not $hasLicense) {
    Write-Host "You need a license to drive"
}
else {
    Write-Host "You are too young to drive"
}
```

### Complex Conditions

```powershell
# Multiple conditions with different operators
function Test-UserAccess {
    param(
        [string]$Username,
        [int]$Age,
        [bool]$IsActive,
        [string[]]$Roles,
        [datetime]$LastLogin
    )
    
    # Check basic requirements
    if (-not $Username -or $Username.Length -lt 3) {
        return "Invalid username"
    }
    
    if ($Age -lt 18) {
        return "Too young for access"
    }
    
    if (-not $IsActive) {
        return "Account inactive"
    }
    
    # Check roles
    if ($Roles -notcontains "User") {
        return "Missing required role"
    }
    
    # Check recent login
    $daysSinceLogin = (Get-Date) - $LastLogin
    if ($daysSinceLogin.Days -gt 90) {
        return "Account inactive for too long"
    }
    
    # Grant access based on roles
    if ($Roles -contains "Admin") {
        return "Full admin access granted"
    }
    elseif ($Roles -contains "PowerUser") {
        return "Power user access granted"
    }
    elseif ($Roles -contains "User") {
        return "Basic user access granted"
    }
    else {
        return "Limited access granted"
    }
}

# Test the function
$result = Test-UserAccess -Username "john" -Age 25 -IsActive $true -Roles @("User", "PowerUser") -LastLogin (Get-Date).AddDays(-10)
Write-Host $result
```

### Ternary-like Operations

```powershell
# PowerShell doesn't have a traditional ternary operator
# But you can use if/else in one line or use conditional expressions

# One-line if/else
$message = if ($age -ge 18) { "Adult" } else { "Minor" }

# Using the conditional operator for assignment
$status = switch ($score) {
    { $_ -ge 90 } { "Excellent" }
    { $_ -ge 80 } { "Good" }
    { $_ -ge 70 } { "Average" }
    default { "Poor" }
}

# Inline conditional execution
$age -ge 18 ? (Write-Host "Adult") : (Write-Host "Minor")  # PowerShell 7+

# Using array indexing for simple conditions
$messages = @("Minor", "Adult")
$message = $messages[($age -ge 18)]
```

## 🚀 Practical Examples

### Example 1: File Processor

```powershell
function Process-File {
    param([string]$FilePath)
    
    if (-not (Test-Path $FilePath)) {
        Write-Error "File not found: $FilePath"
        return
    }
    
    $file = Get-Item $FilePath
    $extension = $file.Extension.ToLower()
    
    switch ($extension) {
        ".txt" {
            Write-Host "Processing text file..."
            $content = Get-Content $FilePath
            Write-Host "File has $($content.Count) lines"
        }
        ".csv" {
            Write-Host "Processing CSV file..."
            $data = Import-Csv $FilePath
            Write-Host "CSV has $($data.Count) rows"
        }
        ".json" {
            Write-Host "Processing JSON file..."
            $data = Get-Content $FilePath | ConvertFrom-Json
            Write-Host "JSON processed successfully"
        }
        ".xml" {
            Write-Host "Processing XML file..."
            $xml = [xml](Get-Content $FilePath)
            Write-Host "XML has $($xml.DocumentElement.ChildNodes.Count) elements"
        }
        default {
            Write-Host "Unknown file type: $extension"
        }
    }
    
    # Check file size
    $sizeKB = [math]::Round($file.Length / 1KB, 2)
    if ($sizeKB -gt 1024) {  # Greater than 1MB
        Write-Host "Large file: $sizeKB KB"
    }
    elseif ($sizeKB -gt 100) {  # Greater than 100KB
        Write-Host "Medium file: $sizeKB KB"
    }
    else {
        Write-Host "Small file: $sizeKB KB"
    }
}

# Test the function
Process-File -FilePath "C:\Windows\notepad.exe"
```

### Example 2: Service Monitor

```powershell
function Monitor-Services {
    param([string[]]$ServiceNames)
    
    foreach ($serviceName in $ServiceNames) {
        try {
            $service = Get-Service -Name $serviceName -ErrorAction Stop
            
            switch ($service.Status) {
                "Running" {
                    Write-Host "✓ $serviceName is running" -ForegroundColor Green
                    
                    # Check if service is critical
                    if ($serviceName -match "SQL|MSSQL|Exchange") {
                        Write-Host "  Critical service is healthy" -ForegroundColor Green
                    }
                }
                "Stopped" {
                    Write-Host "✗ $serviceName is stopped" -ForegroundColor Red
                    
                    # Try to start if it's an essential service
                    if ($serviceName -match "Spooler|Themes|Audio") {
                        Write-Host "  Attempting to start essential service..." -ForegroundColor Yellow
                        try {
                            Start-Service -Name $serviceName -ErrorAction Stop
                            Write-Host "  ✓ Service started successfully" -ForegroundColor Green
                        }
                        catch {
                            Write-Host "  ✗ Failed to start service: $($_.Exception.Message)" -ForegroundColor Red
                        }
                    }
                }
                "Paused" {
                    Write-Host "⏸ $serviceName is paused" -ForegroundColor Yellow
                }
                default {
                    Write-Host "? $serviceName status: $($service.Status)" -ForegroundColor Yellow
                }
            }
        }
        catch {
            Write-Host "✗ Error checking $serviceName`: $($_.Exception.Message)" -ForegroundColor Red
        }
    }
}

# Monitor common services
$services = @("Spooler", "Themes", "AudioSrv", "BITS", "WinRM")
Monitor-Services -ServiceNames $services
```

### Example 3: User Input Validator

```powershell
function Validate-UserInput {
    param([string]$Input, [string]$Type)
    
    switch ($Type.ToLower()) {
        "email" {
            if ([string]::IsNullOrEmpty($Input)) {
                return "Email is required"
            }
            if ($Input -notmatch '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$') {
                return "Invalid email format"
            }
            if ($Input.Length -gt 254) {
                return "Email too long"
            }
            return "Valid email"
        }
        
        "phone" {
            if ([string]::IsNullOrEmpty($Input)) {
                return "Phone number is required"
            }
            # Remove common formatting
            $cleanPhone = $Input -replace '[^\d]', ''
            if ($cleanPhone -notmatch '^\d{10}$') {
                return "Invalid phone number (10 digits required)"
            }
            return "Valid phone number"
        }
        
        "password" {
            if ([string]::IsNullOrEmpty($Input)) {
                return "Password is required"
            }
            if ($Input.Length -lt 8) {
                return "Password must be at least 8 characters"
            }
            if ($Input -notmatch '[A-Z]') {
                return "Password must contain uppercase letter"
            }
            if ($Input -notmatch '[a-z]') {
                return "Password must contain lowercase letter"
            }
            if ($Input -notmatch '\d') {
                return "Password must contain number"
            }
            if ($Input -notmatch '[!@#$%^&*(),.?":{}|<>]') {
                return "Password must contain special character"
            }
            return "Valid password"
        }
        
        "username" {
            if ([string]::IsNullOrEmpty($Input)) {
                return "Username is required"
            }
            if ($Input.Length -lt 3) {
                return "Username must be at least 3 characters"
            }
            if ($Input.Length -gt 20) {
                return "Username must be 20 characters or less"
            }
            if ($Input -notmatch '^[a-zA-Z0-9_]+$') {
                return "Username can only contain letters, numbers, and underscores"
            }
            if ($Input -match '^\d') {
                return "Username cannot start with a number"
            }
            return "Valid username"
        }
        
        default {
            return "Unknown validation type: $Type"
        }
    }
}

# Test validations
$tests = @(
    @{ Input = "user@example.com"; Type = "email" },
    @{ Input = "invalid-email"; Type = "email" },
    @{ Input = "123-456-7890"; Type = "phone" },
    @{ Input = "1234567890"; Type = "phone" },
    @{ Input = "Password123!"; Type = "password" },
    @{ Input = "weak"; Type = "password" },
    @{ Input = "john_doe"; Type = "username" },
    @{ Input = "123user"; Type = "username" }
)

foreach ($test in $tests) {
    $result = Validate-UserInput -Input $test.Input -Type $test.Type
    Write-Host "$($test.Type) '$($test.Input)': $result"
}
```

### Example 4: Automated Decision System

```powershell
function Get-SystemRecommendation {
    param(
        [int]$CPUUsage,
        [int]$MemoryUsage,
        [int]$DiskUsage,
        [int]$ActiveUsers,
        [bool]$IsWeekend,
        [bool]$IsAfterHours
    )
    
    $recommendations = @()
    $priority = "Low"
    
    # CPU Analysis
    if ($CPUUsage -gt 90) {
        $recommendations += "Critical: CPU usage at $CPUUsage% - immediate action required"
        $priority = "Critical"
    }
    elseif ($CPUUsage -gt 80) {
        $recommendations += "Warning: CPU usage at $CPUUsage% - monitor closely"
        if ($priority -eq "Low") { $priority = "High" }
    }
    elseif ($CPUUsage -gt 70) {
        $recommendations += "Info: CPU usage at $CPUUsage% - normal operation"
    }
    
    # Memory Analysis
    if ($MemoryUsage -gt 90) {
        $recommendations += "Critical: Memory usage at $MemoryUsage% - add more RAM"
        $priority = "Critical"
    }
    elseif ($MemoryUsage -gt 80) {
        $recommendations += "Warning: Memory usage at $MemoryUsage% - consider optimization"
        if ($priority -eq "Low") { $priority = "High" }
    }
    
    # Disk Analysis
    if ($DiskUsage -gt 95) {
        $recommendations += "Critical: Disk usage at $DiskUsage% - immediate cleanup required"
        $priority = "Critical"
    }
    elseif ($DiskUsage -gt 85) {
        $recommendations += "Warning: Disk usage at $DiskUsage% - plan cleanup soon"
        if ($priority -eq "Low") { $priority = "Medium" }
    }
    
    # User Load Analysis
    if ($ActiveUsers -gt 100) {
        $recommendations += "High user load: $ActiveUsers active users"
        if ($priority -eq "Low") { $priority = "Medium" }
    }
    
    # Time-based Recommendations
    if ($IsWeekend -and $CPUUsage -gt 50) {
        $recommendations += "Unusual weekend activity detected"
    }
    
    if ($IsAfterHours -and $ActiveUsers -gt 10) {
        $recommendations += "After-hours user activity detected"
    }
    
    # Generate final recommendation
    if ($recommendations.Count -eq 0) {
        $recommendations += "System operating normally"
    }
    
    return @{
        Priority = $priority
        Recommendations = $recommendations
        Summary = "Priority: $priority - $($recommendations.Count) recommendations"
    }
}

# Test the system
$analysis = Get-SystemRecommendation -CPUUsage 85 -MemoryUsage 75 -DiskUsage 60 -ActiveUsers 25 -IsWeekend $false -IsAfterHours $true

Write-Host $analysis.Summary
Write-Host "Recommendations:"
$analysis.Recommendations | ForEach-Object { Write-Host "  - $_" }
```

## 📝 Exercises

### Exercise 1: Grade Calculator
Create a function that:
1. Takes a numerical score (0-100)
2. Returns a letter grade (A, B, C, D, F)
3. Handles edge cases (negative numbers, >100)
4. Provides additional feedback (Excellent, Good, etc.)

### Exercise 2: File Classifier
Create a script that:
1. Analyzes files in a directory
2. Classifies them by type, size, and age
3. Uses nested conditions for complex logic
4. Provides recommendations for each file

### Exercise 3: Access Control System
Create a function that:
1. Takes user information (age, role, permissions)
2. Uses complex conditions to determine access
3. Returns access level and restrictions
4. Logs access decisions

## 🎯 Key Takeaways

- **If/Else** is perfect for simple binary decisions
- **ElseIf** handles multiple sequential conditions
- **Switch** is ideal for checking one value against multiple possibilities
- **Nested conditions** should be used carefully - consider flattening with logical operators
- **Switch supports** wildcards, regex, and case sensitivity options
- **PowerShell 7+** introduces ternary-like operators
- **Complex conditions** can be simplified by breaking them into smaller parts

## 🔄 Next Steps

Move on to [05-Loops](05-loops.md) to learn how to repeat actions and iterate through collections.
