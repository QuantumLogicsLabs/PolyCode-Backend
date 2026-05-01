# 06 - Functions

## 🎯 Learning Objectives

After completing this section, you will understand:
- How to create and use basic functions
- Function parameters and validation
- Return values and output handling
- Function scope and variable visibility
- Advanced function features
- Best practices for function design

## 📝 Basic Functions

Functions are reusable blocks of code that perform specific tasks.

### Simple Function Syntax

```powershell
# Basic function definition
function Write-Greeting {
    Write-Host "Hello, World!"
}

# Call the function
Write-Greeting

# Function with parameters
function Write-PersonalGreeting {
    param([string]$Name)
    Write-Host "Hello, $Name!"
}

Write-PersonalGreeting -Name "John"

# Function with multiple parameters
function Write-UserGreeting {
    param(
        [string]$Name,
        [int]$Age,
        [string]$City
    )
    Write-Host "Hello, $Name! You are $Age years old and live in $City."
}

Write-UserGreeting -Name "Alice" -Age 25 -City "New York"
```

### Function with Return Values

```powershell
# Function that returns a value
function Get-Sum {
    param([int]$a, [int]$b)
    return $a + $b
}

$result = Get-Sum -a 5 -b 3
Write-Host "Sum: $result"

# Function that returns multiple values
function Get-UserInfo {
    param([string]$Username)
    
    # Simulate getting user info
    $user = @{
        Name = $Username
        Age = Get-Random -Minimum 20 -Maximum 50
        Email = "$Username@example.com"
        IsActive = $true
    }
    
    return $user
}

$userInfo = Get-UserInfo -Username "john"
Write-Host "User: $($userInfo.Name), Age: $($userInfo.Age)"
```

## 🔧 Function Parameters

Parameters make functions flexible and reusable.

### Parameter Types

```powershell
# Named parameters
function Test-Parameters {
    param(
        [string]$Name,
        [int]$Age,
        [bool]$IsActive
    )
    Write-Host "Name: $Name, Age: $Age, Active: $IsActive"
}

Test-Parameters -Name "John" -Age 30 -IsActive $true

# Positional parameters
function Test-Positional {
    param(
        [Parameter(Position=0)]
        [string]$FirstName,
        
        [Parameter(Position=1)]
        [string]$LastName
    )
    Write-Host "Full name: $FirstName $LastName"
}

Test-Positional "John" "Doe"

# Mixed positional and named
function Test-Mixed {
    param(
        [Parameter(Position=0)]
        [string]$Name,
        
        [int]$Age,
        
        [string]$City = "Unknown"
    )
    Write-Host "$Name is $Age years old, lives in $City"
}

Test-Mixed "Alice" 25 -City "Boston"
```

### Parameter Attributes

```powershell
# Mandatory parameters
function Test-Mandatory {
    param(
        [Parameter(Mandatory=$true)]
        [string]$Name,
        
        [Parameter(Mandatory=$true)]
        [int]$Age
    )
    Write-Host "Processing: $Name, Age: $Age"
}

# Test-Mandatory -Name "John"  # Will prompt for Age

# Parameter validation
function Test-Validation {
    param(
        [Parameter(Mandatory=$true)]
        [ValidateLength(3, 20)]
        [string]$Username,
        
        [ValidateRange(18, 100)]
        [int]$Age,
        
        [ValidateSet("Admin", "User", "Guest")]
        [string]$Role,
        
        [ValidatePattern("^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$")]
        [string]$Email
    )
    Write-Host "Valid input: Username=$Username, Age=$Age, Role=$Role, Email=$Email"
}

# Test-Validation -Username "john" -Age 25 -Role "Admin" -Email "john@example.com"

# Parameter sets
function Test-ParameterSets {
    [CmdletBinding()]
    param(
        [Parameter(ParameterSetName="User", Mandatory=$true)]
        [string]$Username,
        
        [Parameter(ParameterSetName="User")]
        [string]$Password,
        
        [Parameter(ParameterSetName="Admin", Mandatory=$true)]
        [string]$AdminKey,
        
        [Parameter(ParameterSetName="Admin")]
        [string]$AdminCode
    )
    
    switch ($PSCmdlet.ParameterSetName) {
        "User" { Write-Host "User login: Username=$Username" }
        "Admin" { Write-Host "Admin login: Key=$AdminKey" }
    }
}

# Test-ParameterSets -Username "john" -Password "secret"
# Test-ParameterSets -AdminKey "admin123"
```

### Advanced Parameter Features

```powershell
# ValueFromPipeline
function Get-ProcessInfo {
    param(
        [Parameter(ValueFromPipeline=$true)]
        [System.Diagnostics.Process]$Process
    )
    
    process {
        $info = @{
            Name = $Process.ProcessName
            ID = $Process.Id
            Memory = [math]::Round($Process.WorkingSet / 1MB, 2)
            CPU = $Process.CPU
        }
        return $info
    }
}

# Usage: Get-Process | Get-ProcessInfo

# ValueFromPipelineByPropertyName
function Set-FileInfo {
    param(
        [Parameter(ValueFromPipelineByPropertyName=$true)]
        [string]$Name,
        
        [Parameter(ValueFromPipelineByPropertyName=$true)]
        [long]$Length
    )
    
    process {
        $sizeMB = [math]::Round($Length / 1MB, 2)
        Write-Host "File: $Name, Size: $sizeMB MB"
    }
}

# Usage: Get-ChildItem | Set-FileInfo

# Dynamic parameters
function Get-DynamicParameter {
    [CmdletBinding()]
    param()
    
    dynamicparam {
        $parameterAttribute = New-Object System.Management.Automation.ParameterAttribute
        $parameterAttribute.Mandatory = $true
        $parameterAttribute.HelpMessage = "Enter a server name"
        
        $attributeCollection = New-Object System.Collections.ObjectModel.Collection[System.Attribute]
        $attributeCollection.Add($parameterAttribute)
        
        $validateSetAttribute = New-Object System.Management.Automation.ValidateSetAttribute([System.Environment]::GetEnvironmentVariable("COMPUTERNAME"))
        $attributeCollection.Add($validateSetAttribute)
        
        $runtimeParameter = New-Object System.Management.Automation.RuntimeDefinedParameter("ServerName", [string], $attributeCollection)
        $runtimeParameterDictionary = New-Object System.Management.Automation.RuntimeDefinedParameterDictionary
        $runtimeParameterDictionary.Add("ServerName", $runtimeParameter)
        
        return $runtimeParameterDictionary
    }
    
    process {
        Write-Host "Server: $($PSBoundParameters.ServerName)"
    }
}
```

## 🔄 Return Values and Output

Functions can return values in different ways.

### Return Statements

```powershell
# Single return value
function Get-Square {
    param([int]$Number)
    return $Number * $Number
}

$square = Get-Square -Number 5
Write-Host "Square: $square"

# Multiple return values
function Get-Statistics {
    param([array]$Numbers)
    
    $sum = ($Numbers | Measure-Object -Sum).Sum
    $average = $sum / $Numbers.Count
    $max = ($Numbers | Measure-Object -Maximum).Maximum
    $min = ($Numbers | Measure-Object -Minimum).Minimum
    
    return @{
        Sum = $sum
        Average = $average
        Max = $max
        Min = $min
        Count = $Numbers.Count
    }
}

$stats = Get-Statistics -Numbers @(1, 5, 3, 9, 2)
Write-Host "Average: $($stats.Average), Max: $($stats.Max)"

# Early return
function Test-Number {
    param([int]$Number)
    
    if ($Number -lt 0) {
        Write-Host "Number is negative"
        return $false
    }
    
    if ($Number -eq 0) {
        Write-Host "Number is zero"
        return $false
    }
    
    Write-Host "Number is positive"
    return $true
}

$result = Test-Number -Number 5
Write-Host "Result: $result"
```

### Output Streams

```powershell
# Different output streams
function Write-MultipleOutput {
    [CmdletBinding()]
    param()
    
    # Success output (stream 1)
    Write-Output "This is success output"
    
    # Verbose output (stream 4)
    Write-Verbose "This is verbose information"
    
    # Warning output (stream 3)
    Write-Warning "This is a warning message"
    
    # Error output (stream 2)
    Write-Error "This is an error message"
    
    # Debug output (stream 5)
    Write-Debug "This is debug information"
    
    # Information output (stream 6)
    Write-Information "This is information"
}

# Usage: Write-MultipleOutput -Verbose -Debug

# Custom objects as output
function Get-ServiceReport {
    param([string[]]$ServiceNames)
    
    foreach ($serviceName in $ServiceNames) {
        $service = Get-Service -Name $serviceName -ErrorAction SilentlyContinue
        
        if ($service) {
            $report = [PSCustomObject]@{
                Name = $service.Name
                DisplayName = $service.DisplayName
                Status = $service.Status
                StartType = $service.StartType
                CanStop = $service.CanStop
                MemoryUsage = if ($service.ProcessId) { 
                    (Get-Process -Id $service.ProcessId).WorkingSet 
                } else { 
                    0 
                }
            }
            
            # Output the custom object
            $report
        }
        else {
            Write-Warning "Service $serviceName not found"
        }
    }
}

# Usage: Get-ServiceReport -ServiceNames @("Spooler", "Themes")
```

## 🌍 Function Scope

Functions have their own scope for variables.

### Variable Scope in Functions

```powershell
# Global scope
$globalVar = "I am global"

function Test-Scope {
    # Function scope
    $localVar = "I am local"
    $script:scriptVar = "I am script-scoped"
    
    Write-Host "Inside function:"
    Write-Host "  Global: $globalVar"
    Write-Host "  Local: $localVar"
    Write-Host "  Script: $script:scriptVar"
    
    # Modify global variable
    $global:globalVar = "Modified from function"
}

Test-Scope

Write-Host "Outside function:"
Write-Host "  Global: $globalVar"
Write-Host "  Script: $script:scriptVar"
# Write-Host "Local: $localVar"  # This would cause error

# Using script scope
$script:counter = 0

function Increment-Counter {
    $script:counter++
    Write-Host "Counter: $script:counter"
}

Increment-Counter
Increment-Counter
Write-Host "Final counter: $script:counter"
```

### Passing Variables to Functions

```powershell
# Pass by value (default)
function Test-ByValue {
    param([string]$Text)
    $Text = "Modified inside function"
    Write-Host "Inside function: $Text"
}

$originalText = "Original text"
Test-ByValue -Text $originalText
Write-Host "Outside function: $originalText"

# Pass by reference
function Test-ByReference {
    param([ref]$Text)
    $Text.Value = "Modified inside function"
    Write-Host "Inside function: $($Text.Value)"
}

$originalText = "Original text"
Test-ByReference -Text ([ref]$originalText)
Write-Host "Outside function: $originalText"

# Using [ref] with multiple parameters
function Swap-Values {
    param(
        [ref]$Value1,
        [ref]$Value2
    )
    
    $temp = $Value1.Value
    $Value1.Value = $Value2.Value
    $Value2.Value = $temp
}

$a = 10
$b = 20
Write-Host "Before: a=$a, b=$b"
Swap-Values -Value1 ([ref]$a) -Value2 ([ref]$b)
Write-Host "After: a=$a, b=$b"
```

## 🎯 Advanced Function Features

### CmdletBinding and Common Parameters

```powershell
# Advanced function with CmdletBinding
function Get-AdvancedInfo {
    [CmdletBinding(SupportsShouldProcess=$true, ConfirmImpact="Medium")]
    param(
        [Parameter(Mandatory=$true, ValueFromPipeline=$true)]
        [string]$Path,
        
        [Parameter()]
        [switch]$Recurse,
        
        [Parameter()]
        [string]$Filter = "*"
    )
    
    begin {
        Write-Verbose "Starting to process paths"
        $processedCount = 0
    }
    
    process {
        Write-Verbose "Processing: $Path"
        
        if ($PSCmdlet.ShouldProcess($Path, "Get information")) {
            $items = Get-Item -Path $Path -Filter $Filter
            
            if ($Recurse -and $items.PSIsContainer) {
                $items = Get-ChildItem -Path $Path -Filter $Filter -Recurse
            }
            
            foreach ($item in $items) {
                $info = [PSCustomObject]@{
                    Name = $item.Name
                    FullName = $item.FullName
                    Type = if ($item.PSIsContainer) { "Directory" } else { "File" }
                    Size = if (-not $item.PSIsContainer) { $item.Length } else { 0 }
                    LastModified = $item.LastWriteTime
                    Attributes = $item.Attributes
                }
                
                $info
                $processedCount++
            }
        }
    }
    
    end {
        Write-Verbose "Processed $processedCount items"
    }
}

# Usage: Get-AdvancedInfo -Path "C:\temp" -Recurse -Verbose
```

### Function Aliases and Exporting

```powershell
# Create function with alias
function Get-MyProcess {
    [Alias("gmp")]
    param([string]$Name = "*")
    
    Get-Process -Name $Name
}

# The alias "gmp" will be available for Get-MyProcess

# Export functions in modules
function Export-MyFunctions {
    # This would typically be in a module manifest
    Export-ModuleMember -Function Get-MyProcess, Get-AdvancedInfo
}
```

### Recursive Functions

```powershell
# Recursive factorial function
function Get-Factorial {
    param([int]$Number)
    
    if ($Number -le 1) {
        return 1
    }
    
    return $Number * (Get-Factorial -Number ($Number - 1))
}

$factorial = Get-Factorial -Number 5
Write-Host "5! = $factorial"

# Recursive directory traversal
function Get-DirectoryTree {
    param(
        [string]$Path,
        [int]$Level = 0
    )
    
    $indent = "  " * $Level
    $dir = Get-Item -Path $Path
    
    Write-Host "$indent$($dir.Name)/"
    
    # Get files in current directory
    $files = Get-ChildItem -Path $Path -File
    foreach ($file in $files) {
        Write-Host "$indent  $($file.Name)"
    }
    
    # Recursively process subdirectories
    $subdirs = Get-ChildItem -Path $Path -Directory
    foreach ($subdir in $subdirs) {
        Get-DirectoryTree -Path $subdir.FullName -Level ($Level + 1)
    }
}

# Usage: Get-DirectoryTree -Path "C:\temp"
```

## 🚀 Practical Examples

### Example 1: User Management System

```powershell
function New-UserAccount {
    [CmdletBinding(SupportsShouldProcess=$true)]
    param(
        [Parameter(Mandatory=$true)]
        [ValidateLength(3, 20)]
        [string]$Username,
        
        [Parameter(Mandatory=$true)]
        [ValidatePattern("^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$")]
        [string]$Email,
        
        [ValidateRange(18, 100)]
        [int]$Age,
        
        [ValidateSet("User", "Admin", "PowerUser")]
        [string]$Role = "User",
        
        [switch]$Force
    )
    
    begin {
        Write-Verbose "Starting user account creation process"
        $users = @()
    }
    
    process {
        Write-Verbose "Creating user: $Username"
        
        if ($PSCmdlet.ShouldProcess($Username, "Create user account")) {
            # Check if user already exists (simulation)
            if (-not $Force -and $Username -eq "admin") {
                Write-Warning "User '$Username' already exists. Use -Force to override."
                return
            }
            
            # Create user object
            $user = [PSCustomObject]@{
                Username = $Username
                Email = $Email
                Age = $Age
                Role = $Role
                CreatedDate = Get-Date
                IsActive = $true
                LastLogin = $null
                UserId = -join ((48..57) + (65..90) + (97..122) | Get-Random -Count 8 | ForEach-Object { [char]$_ })
            }
            
            $users += $user
            
            Write-Host "User '$Username' created successfully with ID: $($user.UserId)"
            
            # Output the user object
            $user
        }
    }
    
    end {
        Write-Verbose "User creation process completed. Created $($users.Count) users."
    }
}

function Set-UserStatus {
    [CmdletBinding(SupportsShouldProcess=$true)]
    param(
        [Parameter(Mandatory=$true, ValueFromPipeline=$true)]
        [PSCustomObject]$User,
        
        [Parameter(Mandatory=$true)]
        [ValidateSet("Active", "Inactive", "Suspended")]
        [string]$Status
    )
    
    process {
        if ($PSCmdlet.ShouldProcess($User.Username, "Set status to $Status")) {
            $User.IsActive = switch ($Status) {
                "Active" { $true }
                "Inactive" { $false }
                "Suspended" { $false }
            }
            
            Write-Host "User '$($User.Username)' status set to $Status"
            
            # Output updated user
            $User
        }
    }
}

# Usage examples
$user1 = New-UserAccount -Username "john" -Email "john@example.com" -Age 25 -Role "User" -Verbose
$user2 = New-UserAccount -Username "admin" -Email "admin@example.com" -Age 30 -Role "Admin" -Force

$user1 | Set-UserStatus -Status "Inactive"
```

### Example 2: File Processing Utility

```powershell
function Backup-Files {
    [CmdletBinding(SupportsShouldProcess=$true)]
    param(
        [Parameter(Mandatory=$true)]
        [string]$SourcePath,
        
        [Parameter(Mandatory=$true)]
        [string]$DestinationPath,
        
        [ValidateSet("*.txt", "*.log", "*.csv", "*")]
        [string]$FilePattern = "*",
        
        [switch]$Recurse,
        
        [switch]$Compress,
        
        [ValidateRange(1, 365)]
        [int]$RetentionDays = 30
    )
    
    begin {
        Write-Verbose "Starting backup process"
        Write-Verbose "Source: $SourcePath"
        Write-Verbose "Destination: $DestinationPath"
        
        # Create destination if it doesn't exist
        if (-not (Test-Path $DestinationPath)) {
            if ($PSCmdlet.ShouldProcess($DestinationPath, "Create directory")) {
                New-Item -ItemType Directory -Path $DestinationPath -Force | Out-Null
                Write-Verbose "Created destination directory"
            }
        }
        
        $backupStats = @{
            TotalFiles = 0
            BackedUpFiles = 0
            SkippedFiles = 0
            TotalSize = 0
            BackupSize = 0
            Errors = @()
        }
    }
    
    process {
        Write-Verbose "Searching for files matching pattern: $FilePattern"
        
        # Get files to backup
        $files = Get-ChildItem -Path $SourcePath -Filter $FilePattern -Recurse:$Recurse -File
        $backupStats.TotalFiles = $files.Count
        
        Write-Verbose "Found $($files.Count) files"
        
        foreach ($file in $files) {
            try {
                $relativePath = $file.FullName.Replace($SourcePath, "").TrimStart("\")
                $destinationFile = Join-Path $DestinationPath $relativePath
                
                # Create subdirectories if needed
                $destinationDir = Split-Path $destinationFile -Parent
                if (-not (Test-Path $destinationDir)) {
                    New-Item -ItemType Directory -Path $destinationDir -Force | Out-Null
                }
                
                # Check if file needs backup
                $needsBackup = $true
                if (Test-Path $destinationFile) {
                    $destFile = Get-Item $destinationFile
                    $needsBackup = $file.LastWriteTime -gt $destFile.LastWriteTime
                }
                
                if ($needsBackup) {
                    if ($PSCmdlet.ShouldProcess($file.FullName, "Backup to $destinationFile")) {
                        Copy-Item -Path $file.FullName -Destination $destinationFile -Force
                        
                        $backupStats.BackedUpFiles++
                        $backupStats.TotalSize += $file.Length
                        $backupStats.BackupSize += (Get-Item $destinationFile).Length
                        
                        Write-Verbose "Backed up: $($file.Name)"
                    }
                }
                else {
                    $backupStats.SkippedFiles++
                    Write-Verbose "Skipped (up to date): $($file.Name)"
                }
            }
            catch {
                $errorInfo = "Error backing up $($file.Name): $($_.Exception.Message)"
                $backupStats.Errors += $errorInfo
                Write-Warning $errorInfo
            }
        }
        
        # Compress if requested
        if ($Compress) {
            $zipPath = "$DestinationPath.zip"
            if ($PSCmdlet.ShouldProcess($DestinationPath, "Compress to $zipPath")) {
                Compress-Archive -Path $DestinationPath -DestinationPath $zipPath -Force
                Write-Verbose "Compressed backup to $zipPath"
            }
        }
        
        # Clean old backups
        $cutoffDate = (Get-Date).AddDays(-$RetentionDays)
        $oldBackups = Get-ChildItem -Path $DestinationPath -Recurse | Where-Object LastWriteTime -lt $cutoffDate
        
        foreach ($oldBackup in $oldBackups) {
            if ($PSCmdlet.ShouldProcess($oldBackup.FullName, "Remove old backup")) {
                Remove-Item -Path $oldBackup.FullName -Force -Recurse
                Write-Verbose "Removed old backup: $($oldBackup.Name)"
            }
        }
    }
    
    end {
        # Create summary report
        $summary = [PSCustomObject]@{
            SourcePath = $SourcePath
            DestinationPath = $DestinationPath
            TotalFiles = $backupStats.TotalFiles
            BackedUpFiles = $backupStats.BackedUpFiles
            SkippedFiles = $backupStats.SkippedFiles
            TotalSize = [math]::Round($backupStats.TotalSize / 1MB, 2)
            BackupSize = [math]::Round($backupStats.BackupSize / 1MB, 2)
            ErrorCount = $backupStats.Errors.Count
            BackupDate = Get-Date
        }
        
        Write-Host "`n=== Backup Summary ==="
        Write-Host "Total files: $($summary.TotalFiles)"
        Write-Host "Files backed up: $($summary.BackedUpFiles)"
        Write-Host "Files skipped: $($summary.SkippedFiles)"
        Write-Host "Total size: $($summary.TotalSize) MB"
        Write-Host "Backup size: $($summary.BackupSize) MB"
        Write-Host "Errors: $($summary.ErrorCount)"
        
        if ($backupStats.Errors.Count -gt 0) {
            Write-Host "`nErrors:"
            $backupStats.Errors | ForEach-Object { Write-Host "  - $_" }
        }
        
        # Return summary
        $summary
    }
}

# Usage: Backup-Files -SourcePath "C:\data" -DestinationPath "C:\backup" -FilePattern "*.txt" -Recurse -Verbose
```

### Example 3: Configuration Manager

```powershell
function Get-Configuration {
    [CmdletBinding()]
    param(
        [Parameter()]
        [string]$ConfigFile = "config.json",
        
        [Parameter()]
        [string]$Section = "all"
    )
    
    process {
        if (-not (Test-Path $ConfigFile)) {
            Write-Warning "Configuration file not found: $ConfigFile"
            return $null
        }
        
        try {
            $config = Get-Content $ConfigFile | ConvertFrom-Json
            
            switch ($Section.ToLower()) {
                "all" { return $config }
                "database" { return $config.Database }
                "logging" { return $config.Logging }
                "features" { return $config.Features }
                default { 
                    Write-Warning "Unknown section: $Section"
                    return $null
                }
            }
        }
        catch {
            Write-Error "Error reading configuration: $($_.Exception.Message)"
            return $null
        }
    }
}

function Set-Configuration {
    [CmdletBinding(SupportsShouldProcess=$true)]
    param(
        [Parameter(Mandatory=$true)]
        [string]$Section,
        
        [Parameter(Mandatory=$true)]
        [hashtable]$Settings,
        
        [Parameter()]
        [string]$ConfigFile = "config.json"
    )
    
    process {
        # Load existing configuration
        $config = if (Test-Path $ConfigFile) {
            Get-Content $ConfigFile | ConvertFrom-Json
        } else {
            @{}
        }
        
        # Update configuration
        if ($PSCmdlet.ShouldProcess($ConfigFile, "Update $Section section")) {
            if (-not $config.$Section) {
                $config | Add-Member -NotePropertyName $Section -NotePropertyValue @{}
            }
            
            foreach ($key in $Settings.Keys) {
                $config.$Section | Add-Member -NotePropertyName $key -NotePropertyValue $Settings[$key] -Force
            }
            
            # Save configuration
            $config | ConvertTo-Json -Depth 10 | Set-Content $ConfigFile
            Write-Host "Configuration updated: $Section"
        }
    }
}

function Test-Configuration {
    [CmdletBinding()]
    param(
        [Parameter()]
        [string]$ConfigFile = "config.json"
    )
    
    process {
        $config = Get-Configuration -ConfigFile $ConfigFile
        
        if (-not $config) {
            Write-Host "Configuration test: FAILED (no config file)"
            return $false
        }
        
        $tests = @()
        
        # Test database configuration
        if ($config.Database) {
            $tests += [PSCustomObject]@{
                Test = "Database section exists"
                Result = "PASS"
                Details = "Found database configuration"
            }
            
            if ($config.Database.Server -and $config.Database.Port) {
                $tests += [PSCustomObject]@{
                    Test = "Database connection settings"
                    Result = "PASS"
                    Details = "Server: $($config.Database.Server), Port: $($config.Database.Port)"
                }
            }
            else {
                $tests += [PSCustomObject]@{
                    Test = "Database connection settings"
                    Result = "FAIL"
                    Details = "Missing server or port configuration"
                }
            }
        }
        else {
            $tests += [PSCustomObject]@{
                Test = "Database section exists"
                Result = "FAIL"
                Details = "Database configuration not found"
            }
        }
        
        # Test logging configuration
        if ($config.Logging) {
            $tests += [PSCustomObject]@{
                Test = "Logging section exists"
                Result = "PASS"
                Details = "Found logging configuration"
            }
        }
        else {
            $tests += [PSCustomObject]@{
                Test = "Logging section exists"
                Result = "FAIL"
                Details = "Logging configuration not found"
            }
        }
        
        # Display results
        Write-Host "`n=== Configuration Test Results ==="
        foreach ($test in $tests) {
            $color = switch ($test.Result) {
                "PASS" { "Green" }
                "FAIL" { "Red" }
                default { "Yellow" }
            }
            Write-Host "$($test.Result): $($test.Test)" -ForegroundColor $color
            Write-Host "  $($test.Details)"
        }
        
        $passCount = ($tests | Where-Object Result -eq "PASS").Count
        $totalCount = $tests.Count
        
        Write-Host "`nOverall: $passCount/$totalCount tests passed"
        
        return $passCount -eq $totalCount
    }
}

# Usage examples:
# Set-Configuration -Section "Database" -Settings @{ Server="localhost"; Port=5432; Name="myapp" }
# $config = Get-Configuration -Section "Database"
# Test-Configuration
```

## 📝 Exercises

### Exercise 1: Calculator Function
Create a calculator function that:
1. Accepts two numbers and an operation
2. Supports basic operations (+, -, *, /)
3. Includes parameter validation
4. Handles division by zero
5. Returns formatted results

### Exercise 2: File Validator
Create a file validation function that:
1. Accepts file path and validation rules
2. Checks file existence, size, and age
3. Uses parameter sets for different validation types
4. Returns detailed validation results
5. Supports pipeline input

### Exercise 3: User Generator
Create a user generator function that:
1. Generates realistic test user data
2. Uses parameters for customization
3. Includes validation for user data
4. Returns array of user objects
5. Supports different output formats

## 🎯 Key Takeaways

- **Functions** encapsulate reusable code blocks
- **Parameters** make functions flexible and testable
- **Return values** can be single values, arrays, or objects
- **Scope** controls variable visibility
- **CmdletBinding** adds advanced features like ShouldProcess
- **Parameter validation** ensures input quality
- **Pipeline support** enables function chaining
- **Error handling** should be built into functions
- **Documentation** is important for maintainability

## 🔄 Next Steps

Move on to [07-Arrays and Collections](07-arrays-and-collections.md) to learn about working with data collections in PowerShell.
