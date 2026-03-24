# Advanced PowerShell Examples
# Demonstrates concepts from sections 11-12

<#
.SYNOPSIS
    Collection of advanced PowerShell examples
.DESCRIPTION
    This script contains practical examples for:
    - Error handling and logging
    - Advanced topics (remoting, jobs, workflows, DSC)
    - Performance optimization
    - Security best practices
.NOTES
    File:      advanced-examples.ps1
    Author:    PowerShell Learning Guide
    Created:   2023-12-01
#>

# Write-Host examples
Write-Host "=== Advanced PowerShell Examples ===" -ForegroundColor Green

# Error handling examples
Write-Host "`n=== Error Handling Examples ===" -ForegroundColor Green

# Custom error logger class
class ErrorLogger {
    [string]$LogPath
    [hashtable]$Statistics
    
    ErrorLogger([string]$logPath) {
        $this.LogPath = $logPath
        $this.Statistics = @{
            TotalErrors = 0
            CriticalErrors = 0
            Warnings = 0
            InfoMessages = 0
        }
    }
    
    [void]LogError([string]$message, [System.Management.Automation.ErrorRecord]$errorRecord = $null) {
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        $logEntry = "[$timestamp] ERROR: $message"
        
        if ($errorRecord) {
            $logEntry += "`n  Exception: $($errorRecord.Exception.Message)"
            $logEntry += "`n  Command: $($errorRecord.InvocationInfo.MyCommand.Name)"
            $logEntry += "`n  Line: $($errorRecord.InvocationInfo.ScriptLineNumber)"
        }
        
        $logEntry | Add-Content $this.LogPath
        $this.Statistics.TotalErrors++
        $this.Statistics.CriticalErrors++
        
        Write-Host $logEntry -ForegroundColor Red
    }
    
    [void]LogWarning([string]$message) {
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        $logEntry = "[$timestamp] WARNING: $message"
        
        $logEntry | Add-Content $this.LogPath
        $this.Statistics.Warnings++
        
        Write-Host $logEntry -ForegroundColor Yellow
    }
    
    [void]LogInfo([string]$message) {
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        $logEntry = "[$timestamp] INFO: $message"
        
        $logEntry | Add-Content $this.LogPath
        $this.Statistics.InfoMessages++
        
        Write-Host $logEntry -ForegroundColor Green
    }
    
    [hashtable]GetStatistics() {
        return $this.Statistics.Clone()
    }
}

# Create logger
$tempDir = Join-Path $env:TEMP "PowerShellAdvancedExamples"
if (-not (Test-Path $tempDir)) {
    New-Item -Path $tempDir -ItemType Directory -Force | Out-Null
}

$logger = [ErrorLogger]::new((Join-Path $tempDir "error.log"))

# Error handling examples
$logger.LogInfo("Starting error handling examples")

# Try/Catch/Finally
try {
    $logger.LogInfo("Attempting risky operation")
    # Simulate an error
    throw "Simulated error for demonstration"
}
catch [System.Exception] {
    $logger.LogError("Caught exception", $_)
}
finally {
    $logger.LogInfo("Cleanup in finally block")
}

# Multiple catch blocks
try {
    $number = "abc"
    $converted = [int]$number
}
catch [System.Management.Automation.PSInvalidCastException] {
    $logger.LogError("Type conversion failed", $_)
}
catch {
    $logger.LogError("Other error occurred", $_)
}

# Retry logic
function Invoke-RetryCommand {
    param(
        [Parameter(Mandatory=$true)]
        [scriptblock]$ScriptBlock,
        
        [int]$MaxAttempts = 3,
        
        [int]$DelaySeconds = 1
    )
    
    $attempt = 1
    
    while ($attempt -le $MaxAttempts) {
        try {
            return & $ScriptBlock
        }
        catch {
            if ($attempt -eq $MaxAttempts) {
                throw "Failed after $MaxAttempts attempts: $($_.Exception.Message)"
            }
            
            $logger.LogWarning("Attempt $attempt failed, retrying in $DelaySeconds seconds...")
            Start-Sleep -Seconds $DelaySeconds
            $attempt++
        }
    }
}

# Test retry logic
$success = $false
$attemptCount = 0

try {
    $result = Invoke-RetryCommand -ScriptBlock {
        $script:attemptCount++
        if ($script:attemptCount -lt 3) {
            throw "Simulated failure attempt $script:attemptCount"
        }
        return "Success on attempt $script:attemptCount"
    }
    $logger.LogInfo("Retry succeeded: $result")
    $success = $true
}
catch {
    $logger.LogError("Retry failed", $_)
}

# Background jobs examples
Write-Host "`n=== Background Jobs Examples ===" -ForegroundColor Green

$logger.LogInfo("Starting background jobs examples")

# Create multiple jobs
$jobs = @()
$computers = "localhost", "127.0.0.1"

foreach ($computer in $computers) {
    $job = Start-Job -ScriptBlock {
        param($Comp, $LoggerPath)
        
        # Simple logging in job
        $logEntry = "$(Get-Date -Format 'HH:mm:ss'): Processing $Comp"
        $logEntry | Add-Content $LoggerPath
        
        # Simulate work
        Start-Sleep -Seconds 2
        
        # Get system info
        $os = Get-WmiObject -Class Win32_OperatingSystem -ComputerName $Comp -ErrorAction SilentlyContinue
        $cs = Get-WmiObject -Class Win32_ComputerSystem -ComputerName $Comp -ErrorAction SilentlyContinue
        
        if ($os -and $cs) {
            return @{
                ComputerName = $Comp
                OS = $os.Caption
                Model = $cs.Model
                Memory = [math]::Round($cs.TotalPhysicalMemory / 1GB, 2)
                Status = "Success"
            }
        }
        else {
            return @{
                ComputerName = $Comp
                Status = "Failed"
                Error = "Could not connect"
            }
        }
    } -ArgumentList $computer, (Join-Path $tempDir "job.log") -Name "Job-$computer"
    
    $jobs += $job
}

# Monitor jobs
$logger.LogInfo("Started $($jobs.Count) background jobs")

while ($jobs.State -contains "Running") {
    $runningJobs = $jobs | Where-Object { $_.State -eq "Running" }
    $logger.LogInfo("Waiting for $($runningJobs.Count) jobs to complete...")
    Start-Sleep -Seconds 1
}

# Collect results
$jobResults = @()
foreach ($job in $jobs) {
    $result = $job | Receive-Job
    $jobResults += $result
    $job | Remove-Job
}

$logger.LogInfo("All jobs completed. Results:")
foreach ($result in $jobResults) {
    if ($result.Status -eq "Success") {
        $logger.LogInfo("$($result.ComputerName): $($result.OS), $($result.Model), $($result.Memory) GB RAM")
    } else {
        $logger.LogWarning("$($result.ComputerName): $($result.Error)")
    }
}

# Workflow examples (if supported)
Write-Host "`n=== Workflow Examples ===" -ForegroundColor Green

if ($PSVersionTable.PSVersion.Major -ge 3) {
    $logger.LogInfo("Creating workflow example")
    
    workflow Test-Workflow {
        param([string[]]$Computers)
        
        # Parallel execution
        foreach -parallel ($computer in $Computers) {
            $result = InlineScript {
                $comp = $using:computer
                
                try {
                    $services = Get-Service -ComputerName $comp -ErrorAction Stop
                    $runningServices = $services | Where-Object { $_.Status -eq "Running" }
                    
                    return @{
                        ComputerName = $comp
                        RunningServices = $runningServices.Count
                        TotalServices = $services.Count
                        Status = "Success"
                    }
                }
                catch {
                    return @{
                        ComputerName = $comp
                        Status = "Failed"
                        Error = $_.Exception.Message
                    }
                }
            }
            
            # Checkpoint for resumability
            Checkpoint-Workflow
            
            # Output result
            $result
        }
    }
    
    # Run workflow
    try {
        $workflowResults = Test-Workflow -Computers @("localhost")
        $logger.LogInfo("Workflow completed successfully")
        
        foreach ($result in $workflowResults) {
            if ($result.Status -eq "Success") {
                $logger.LogInfo("$($result.ComputerName): $($result.RunningServices)/$($result.TotalServices) services running")
            } else {
                $logger.LogWarning("$($result.ComputerName): $($result.Error)")
            }
        }
    }
    catch {
        $logger.LogError("Workflow failed", $_)
    }
} else {
    $logger.LogWarning("Workflows not supported in this PowerShell version")
}

# DSC examples (if supported)
Write-Host "`n=== DSC Examples ===" -ForegroundColor Green

if ($PSVersionTable.PSVersion.Major -ge 4) {
    $logger.LogInfo("Creating DSC configuration example")
    
    # Simple DSC configuration
    configuration SimpleWebConfig {
        param([string]$ComputerName = "localhost")
        
        Import-DscResource -ModuleName PSDesiredStateConfiguration
        
        Node $ComputerName {
            # Ensure a directory exists
            File WebDirectory {
                Ensure = "Present"
                Type = "Directory"
                DestinationPath = Join-Path $tempDir "Website"
            }
            
            # Ensure a file exists
            File DefaultPage {
                Ensure = "Present"
                Type = "File"
                DestinationPath = Join-Path $tempDir "Website\index.html"
                Contents = "<html><body><h1>Test Website</h1></body></html>"
                DependsOn = "[File]WebDirectory"
            }
        }
    }
    
    # Compile configuration (don't actually apply)
    try {
        $configPath = Join-Path $tempDir "SimpleWebConfig"
        SimpleWebConfig -OutputPath $configPath
        $logger.LogInfo("DSC configuration compiled successfully")
        
        # Test configuration
        $testResult = Test-DscConfiguration -Path $configPath
        $logger.LogInfo("DSC test result: $testResult")
    }
    catch {
        $logger.LogWarning("DSC configuration failed: $($_.Exception.Message)")
    }
} else {
    $logger.LogWarning("DSC not supported in this PowerShell version")
}

# Performance optimization examples
Write-Host "`n=== Performance Optimization Examples ===" -ForegroundColor Green

$logger.LogInfo("Starting performance optimization examples")

# Measure different approaches
$iterations = 10000

# Approach 1: PowerShell pipeline (slower)
$stopwatch = [System.Diagnostics.Stopwatch]::StartNew()
$result1 = 1..$iterations | ForEach-Object { $_ * 2 }
$stopwatch.Stop()
$pipelineTime = $stopwatch.ElapsedMilliseconds

# Approach 2: .NET LINQ (faster)
$stopwatch.Restart()
$result2 = [System.Linq.Enumerable]::Select([int[]](1..$iterations), [Func[int, int]]{ param($x) $x * 2 })
$stopwatch.Stop()
$linqTime = $stopwatch.ElapsedMilliseconds

# Approach 3: Simple for loop (fastest)
$stopwatch.Restart()
$result3 = @()
for ($i = 1; $i -le $iterations; $i++) {
    $result3 += $i * 2
}
$stopwatch.Stop()
$loopTime = $stopwatch.ElapsedMilliseconds

$logger.LogInfo("Performance comparison ($iterations iterations):")
$logger.LogInfo("  Pipeline: $pipelineTime ms")
$logger.LogInfo("  LINQ: $linqTime ms")
$logger.LogInfo("  For loop: $loopTime ms")

# String concatenation performance
$strings = 1..1000

# Slow: String concatenation
$stopwatch.Restart()
$slowResult = ""
foreach ($s in $strings) {
    $slowResult += "Item $s`n"
}
$stopwatch.Stop()
$concatTime = $stopwatch.ElapsedMilliseconds

# Fast: StringBuilder
$stopwatch.Restart()
$sb = New-Object System.Text.StringBuilder
foreach ($s in $strings) {
    $sb.AppendLine("Item $s") | Out-Null
}
$fastResult = $sb.ToString()
$stopwatch.Stop()
$sbTime = $stopwatch.ElapsedMilliseconds

$logger.LogInfo("String concatenation (1000 strings):")
$logger.LogInfo("  Concatenation: $concatTime ms")
$logger.LogInfo("  StringBuilder: $sbTime ms")

# Collection performance
$items = 1..10000

# Array (slow for modifications)
$stopwatch.Restart()
$array = @()
foreach ($item in $items) {
    $array += $item
}
$stopwatch.Stop()
$arrayTime = $stopwatch.ElapsedMilliseconds

# ArrayList (faster for modifications)
$stopwatch.Restart()
$arrayList = New-Object System.Collections.ArrayList
foreach ($item in $items) {
    $arrayList.Add($item) | Out-Null
}
$stopwatch.Stop()
$arrayListTime = $stopwatch.ElapsedMilliseconds

# Generic List (fastest and type-safe)
$stopwatch.Restart()
$genericList = New-Object "System.Collections.Generic.List[int]"
foreach ($item in $items) {
    $genericList.Add($item)
}
$stopwatch.Stop()
$genericListTime = $stopwatch.ElapsedMilliseconds

$logger.LogInfo("Collection performance (10000 items):")
$logger.LogInfo("  Array: $arrayTime ms")
$logger.LogInfo("  ArrayList: $arrayListTime ms")
$logger.LogInfo("  Generic List: $genericListTime ms")

# Security examples
Write-Host "`n=== Security Examples ===" -ForegroundColor Green

$logger.LogInfo("Starting security examples")

# Secure string handling
function ConvertTo-SecureStringExample {
    param([string]$PlainText)
    
    $secureString = ConvertTo-SecureString -String $PlainText -AsPlainText -Force
    $credential = New-Object System.Management.Automation.PSCredential("username", $secureString)
    
    return $credential
}

$secureCred = ConvertTo-SecureStringExample -PlainText "MySecretPassword123"
$logger.LogInfo("Secure credential created successfully")

# Input validation
function Test-ValidatedInput {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory=$true)]
        [ValidatePattern("^[a-zA-Z0-9]{3,20}$")]
        [string]$Username,
        
        [ValidateRange(1, 120)]
        [int]$Age,
        
        [ValidateSet("User", "Admin", "Guest")]
        [string]$Role = "User"
    )
    
    $logger.LogInfo("Validated input: Username=$Username, Age=$Age, Role=$Role")
    
    # Sanitize for output
    $sanitizedUsername = $Username -replace "[^\w]", ""
    
    return @{
        Username = $sanitizedUsername
        Age = $Age
        Role = $Role
    }
}

try {
    $validatedInput = Test-ValidatedInput -Username "john123" -Age 25 -Role "Admin"
    $logger.LogInfo("Input validation successful")
}
catch {
    $logger.LogError("Input validation failed", $_)
}

# Execution policy check
$currentPolicy = Get-ExecutionPolicy
$logger.LogInfo("Current execution policy: $currentPolicy")

# Script signing simulation
$scriptContent = @'
# Example script for signing
Write-Host "This script would be signed in production"
'@

$scriptPath = Join-Path $tempDir "example-script.ps1"
$scriptContent | Set-Content $scriptPath

# Check if script would be signed (simulation)
if (Test-Path $scriptPath) {
    $logger.LogInfo("Example script created: $scriptPath")
    
    # In production, you would:
    # $cert = Get-ChildItem Cert:\CurrentUser\My -CodeSigningCert
    # Set-AuthenticodeSignature -FilePath $scriptPath -Certificate $cert
}

# Safe file operations
function Write-SafeFile {
    param(
        [string]$Path,
        [string]$Content
    )
    
    try {
        # Validate path
        $resolvedPath = Resolve-Path -Path $Path -ErrorAction SilentlyContinue
        if (-not $resolvedPath) {
            throw "Invalid path: $Path"
        }
        
        # Check for directory traversal
        if ($Path -contains ".." -or $Path -contains "~") {
            throw "Path traversal detected"
        }
        
        # Write file safely
        $bytes = [System.Text.Encoding]::UTF8.GetBytes($Content)
        [System.IO.File]::WriteAllBytes($resolvedPath.Path, $bytes)
        
        $logger.LogInfo("File written safely: $Path")
        return $true
    }
    catch {
        $logger.LogError("Safe file operation failed", $_)
        return $false
    }
}

$safeFilePath = Join-Path $tempDir "safe-file.txt"
Write-SafeFile -Path $safeFilePath -Content "This is a safely written file"

# Final statistics
Write-Host "`n=== Final Statistics ===" -ForegroundColor Green

$stats = $logger.GetStatistics()
$logger.LogInfo("Advanced examples completed")
$logger.LogInfo("Final statistics:")
$logger.LogInfo("  Total errors: $($stats.TotalErrors)")
$logger.LogInfo("  Critical errors: $($stats.CriticalErrors)")
$logger.LogInfo("  Warnings: $($stats.Warnings)")
$logger.LogInfo("  Info messages: $($stats.InfoMessages)")

Write-Host "Logger Statistics:"
Write-Host "  Total errors: $($stats.TotalErrors)"
Write-Host "  Critical errors: $($stats.CriticalErrors)"
Write-Host "  Warnings: $($stats.Warnings)"
Write-Host "  Info messages: $($stats.InfoMessages)"

# Cleanup
Write-Host "`n=== Cleanup ===" -ForegroundColor Green
if (Test-Path $tempDir) {
    Remove-Item $tempDir -Recurse -Force
    Write-Host "Cleaned up temporary directory: $tempDir"
}

Write-Host "`n=== Advanced Examples Complete ===" -ForegroundColor Green
