# Intermediate PowerShell Examples
# Demonstrates concepts from sections 07-10

<#
.SYNOPSIS
    Collection of intermediate PowerShell examples
.DESCRIPTION
    This script contains practical examples for:
    - Arrays and collections
    - Hashtables and custom objects
    - File system operations
    - Modules and scripts
.NOTES
    File:      intermediate-examples.ps1
    Author:    PowerShell Learning Guide
    Created:   2023-12-01
#>

# Write-Host examples
Write-Host "=== Intermediate PowerShell Examples ===" -ForegroundColor Green

# Array and collection examples
Write-Host "`n=== Array and Collection Examples ===" -ForegroundColor Green

# Basic array operations
$numbers = 1..15
Write-Host "Numbers array: $($numbers.Count) items"

# Array slicing
$firstFive = $numbers[0..4]
$lastFive = $numbers[-5..-1]
Write-Host "First 5: $($firstFive -join ', ')"
Write-Host "Last 5: $($lastFive -join ', ')"

# Array filtering
$largeNumbers = $numbers | Where-Object { $_ -gt 10 }
Write-Host "Numbers > 10: $($largeNumbers -join ', ')"

# Array transformation
$doubledNumbers = $numbers | ForEach-Object { $_ * 2 }
Write-Host "Doubled numbers (first 5): $($doubledNumbers[0..4] -join ', ')"

# ArrayList for better performance with modifications
$arrayList = New-Object System.Collections.ArrayList
foreach ($number in 1..1000) {
    $arrayList.Add($number) | Out-Null
}
Write-Host "ArrayList contains $($arrayList.Count) items"

# Generic List with type safety
$stringList = New-Object "System.Collections.Generic.List[string]"
$stringList.Add("PowerShell")
$stringList.Add("Scripting")
$stringList.Add("Automation")
Write-Host "Generic list: $($stringList -join ', ')"

# Multi-dimensional arrays
$matrix = @(
    @(1, 2, 3),
    @(4, 5, 6),
    @(7, 8, 9)
)
Write-Host "Matrix element [1,1]: $($matrix[1][1])"

# Hashtable and custom object examples
Write-Host "`n=== Hashtable and Custom Object Examples ===" -ForegroundColor Green

# Basic hashtable
$userConfig = @{
    Name = "John Doe"
    Email = "john@example.com"
    Preferences = @{
        Theme = "Dark"
        Language = "en-US"
        Notifications = $true
    }
    Roles = @("User", "PowerUser")
}

Write-Host "User configuration:"
Write-Host "  Name: $($userConfig.Name)"
Write-Host "  Email: $($userConfig.Email)"
Write-Host "  Theme: $($userConfig.Preferences.Theme)"
Write-Host "  Roles: $($userConfig.Roles -join ', ')"

# Ordered hashtable
$orderedSettings = [ordered]@{
    First = "Value1"
    Second = "Value2"
    Third = "Value3"
}
Write-Host "Ordered settings: $($orderedSettings.Keys -join ', ')"

# PSCustomObject
$user = [PSCustomObject]@{
    Id = 1
    Name = "Jane Smith"
    Email = "jane@example.com"
    Department = "IT"
    IsActive = $true
    CreatedDate = Get-Date
}

# Add calculated property
$user | Add-Member -MemberType ScriptProperty -Name "DisplayName" -Value {
    return "$($this.Name) ($($this.Department))"
}

# Add method
$user | Add-Member -MemberType ScriptMethod -Name "Deactivate" -Value {
    $this.IsActive = $false
    $this | Add-Member -NotePropertyName "DeactivatedDate" -NotePropertyValue (Get-Date) -Force
}

Write-Host "Custom object:"
Write-Host "  $($user.DisplayName)"
Write-Host "  Active: $($user.IsActive)"

# Deactivate user
$user.Deactivate()
Write-Host "  After deactivation: Active = $($user.IsActive)"

# Class example (PowerShell 5+)
class Employee {
    [string]$FirstName
    [string]$LastName
    [int]$EmployeeId
    [string]$Department
    [decimal]$Salary
    
    Employee([string]$firstName, [string]$lastName, [int]$employeeId) {
        $this.FirstName = $firstName
        $this.LastName = $lastName
        $this.EmployeeId = $employeeId
        $this.Department = "General"
        $this.Salary = 30000
    }
    
    [string]GetFullName() {
        return "$($this.FirstName) $($this.LastName)"
    }
    
    [void]Promote([decimal]$raiseAmount) {
        $this.Salary += $raiseAmount
    }
    
    [string]GetDisplayName() {
        return "$($this.GetFullName()) (ID: $($this.EmployeeId))"
    }
}

$emp = [Employee]::new("Alice", "Johnson", 1001)
$emp.Department = "Engineering"
$emp.Promote(5000)
Write-Host "Employee: $($emp.GetDisplayName()), Salary: $($emp.Salary):C"

# File system examples
Write-Host "`n=== File System Examples ===" -ForegroundColor Green

# Create temporary directory for examples
$tempDir = Join-Path $env:TEMP "PowerShellExamples"
if (Test-Path $tempDir) {
    Remove-Item $tempDir -Recurse -Force
}
New-Item -Path $tempDir -ItemType Directory -Force | Out-Null
Write-Host "Created temporary directory: $tempDir"

# Create files
$files = @(
    "document1.txt",
    "document2.txt", 
    "data.csv",
    "config.json"
)

foreach ($file in $files) {
    $filePath = Join-Path $tempDir $file
    $content = "Content of $file created at $(Get-Date)"
    Set-Content -Path $filePath -Value $content
}
Write-Host "Created $($files.Count) files"

# Read files
Write-Host "File contents:"
Get-ChildItem -Path $tempDir -Filter "*.txt" | ForEach-Object {
    $content = Get-Content -Path $_.FullName
    Write-Host "  $($_.Name): $content"
}

# File operations
Write-Host "File operations:"

# Copy file
$sourceFile = Join-Path $tempDir "document1.txt"
$destFile = Join-Path $tempDir "document1_copy.txt"
Copy-Item -Path $sourceFile -Destination $destFile
Write-Host "  Copied: document1.txt -> document1_copy.txt"

# Get file info
$fileInfo = Get-Item -Path $sourceFile
Write-Host "  File info: $($fileInfo.Name), Size: $($fileInfo.Length) bytes, Created: $($fileInfo.CreationTime)"

# Search in files
$searchResults = Select-String -Path (Join-Path $tempDir "*.txt") -Pattern "Content" -SimpleMatch
Write-Host "  Found $($searchResults.Count) matches for 'Content'"

# Directory operations
$subDir = Join-Path $tempDir "subfolder"
New-Item -Path $subDir -ItemType Directory -Force | Out-Null

# Move file to subfolder
$movedFile = Join-Path $subDir "document2.txt"
Move-Item -Path (Join-Path $tempDir "document2.txt") -Destination $movedFile
Write-Host "  Moved document2.txt to subfolder"

# Get directory size
$allFiles = Get-ChildItem -Path $tempDir -Recurse -File
$totalSize = ($allFiles | Measure-Object -Property Length -Sum).Sum
Write-Host "  Total directory size: $([math]::Round($totalSize / 1KB, 2)) KB"

# Path manipulation
Write-Host "Path manipulation examples:"
$testPath = "C:\Users\John\Documents\file.txt"

$directory = Split-Path -Path $testPath -Parent
$filename = Split-Path -Path $testPath -Leaf
$extension = [System.IO.Path]::GetExtension($testPath)
$baseName = [System.IO.Path]::GetFileNameWithoutExtension($testPath)

Write-Host "  Original: $testPath"
Write-Host "  Directory: $directory"
Write-Host "  Filename: $filename"
Write-Host "  Extension: $extension"
Write-Host "  Base name: $baseName"

# Join paths safely
$joinedPath = Join-Path -Path $directory -ChildPath "newfile.txt"
Write-Host "  Joined path: $joinedPath"

# Module and script examples
Write-Host "`n=== Module and Script Examples ===" -ForegroundColor Green

# Create a simple module structure
$moduleDir = Join-Path $tempDir "MyModule"
New-Item -Path $moduleDir -ItemType Directory -Force | Out-Null

# Create module manifest
$manifestContent = @"
@{
    RootModule = 'MyModule.psm1'
    ModuleVersion = '1.0.0'
    GUID = '12345678-1234-1234-1234-123456789abc'
    Author = 'PowerShell Learning Guide'
    Description = 'Example module for learning purposes'
    PowerShellVersion = '5.1'
    FunctionsToExport = @('Get-ExampleData', 'Set-ExampleConfig')
    VariablesToExport = @()
    AliasesToExport = @('ged', 'sec')
}
"@

$manifestPath = Join-Path $moduleDir "MyModule.psd1"
$manifestContent | Set-Content -Path $manifestPath

# Create module script
$moduleScriptContent = @"
# MyModule.psm1

# Module variable
$Script:ModuleData = @{
    Initialized = $true
    Version = "1.0.0"
}

# Function 1
function Get-ExampleData {
    [CmdletBinding()]
    param(
        [Parameter()]
        [string]$Filter = "*"
    )
    
    Write-Verbose "Getting example data with filter: $Filter"
    
    $data = @(
        @{ Name = "Item1"; Type = "Data"; Value = 100 },
        @{ Name = "Item2"; Type = "Info"; Value = 200 },
        @{ Name = "Item3"; Type = "Config"; Value = 300 }
    )
    
    return $data | Where-Object { $_.Name -like $Filter }
}

# Function 2
function Set-ExampleConfig {
    [CmdletBinding(SupportsShouldProcess=$true)]
    param(
        [Parameter(Mandatory=$true)]
        [string]$Key,
        
        [Parameter(Mandatory=$true)]
        [string]$Value
    )
    
    if ($PSCmdlet.ShouldProcess($Key, "Set configuration")) {
        $Script:ModuleData[$Key] = $Value
        Write-Host "Set $Key = $Value"
        return $true
    }
    
    return $false
}

# Set aliases
Set-Alias -Name ged -Value Get-ExampleData
Set-Alias -Name sec -Value Set-ExampleConfig

# Module cleanup
$MyInvocation.MyCommand.ScriptBlock.Module.OnRemove = {
    Write-Verbose "MyModule is being removed"
}
"@

$moduleScriptPath = Join-Path $moduleDir "MyModule.psm1"
$moduleScriptContent | Set-Content -Path $moduleScriptPath

Write-Host "Created example module at: $moduleDir"

# Import and use the module
Import-Module -Name $moduleDir -Force

Write-Host "Module imported successfully"
Write-Host "Available commands:"
Get-Command -Module MyModule | ForEach-Object { Write-Host "  - $($_.Name)" }

# Use module functions
Write-Host "Using module functions:"
$data = Get-ExampleData -Filter "Item*"
$data | ForEach-Object { Write-Host "  $($_.Name): $($_.Type) = $($_.Value)" }

Set-ExampleConfig -Key "TestSetting" -Value "TestValue"

# Export module data
$exportPath = Join-Path $tempDir "module-export.json"
$Script:ModuleData | ConvertTo-Json -Depth 3 | Set-Content $exportPath
Write-Host "Module data exported to: $exportPath"

# Remove module
Remove-Module -Name MyModule -Force
Write-Host "Module removed"

# Script parameter examples
Write-Host "`n=== Script Parameter Examples ===" -ForegroundColor Green

# Simulate script with parameters
function Test-ScriptParameters {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory=$true, Position=0)]
        [ValidateNotNullOrEmpty()]
        [string]$Name,
        
        [Parameter(Position=1)]
        [ValidateRange(1, 100)]
        [int]$Count = 1,
        
        [Parameter()]
        [ValidateSet("Low", "Medium", "High")]
        [string]$Priority = "Medium",
        
        [Parameter()]
        [switch]$Force,
        
        [Parameter(ValueFromPipeline=$true)]
        [string[]]$Items
    )
    
    Write-Host "Script parameters:"
    Write-Host "  Name: $Name"
    Write-Host "  Count: $Count"
    Write-Host "  Priority: $Priority"
    Write-Host "  Force: $Force"
    Write-Host "  Items: $($Items -join ', ')"
    
    return @{
        Name = $Name
        Count = $Count
        Priority = $Priority
        Force = $Force
        Items = $Items
    }
}

# Test parameter validation
try {
    Test-ScriptParameters -Name "Test" -Count 50 -Priority "High" -Force -Items "A", "B", "C" | Out-Null
    Write-Host "Parameter test successful"
}
catch {
    Write-Warning "Parameter validation failed: $($_.Exception.Message)"
}

# Cleanup
Write-Host "`n=== Cleanup ===" -ForegroundColor Green
if (Test-Path $tempDir) {
    Remove-Item $tempDir -Recurse -Force
    Write-Host "Cleaned up temporary directory: $tempDir"
}

Write-Host "`n=== Intermediate Examples Complete ===" -ForegroundColor Green
