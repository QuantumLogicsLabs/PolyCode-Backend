# 12 - Advanced Topics

## 🎯 Learning Objectives

After completing this section, you will understand:
- PowerShell remoting and remote execution
- Background jobs and asynchronous operations
- Workflows for long-running tasks
- Desired State Configuration (DSC)
- PowerShell classes and object-oriented programming
- Advanced .NET integration
- Performance optimization techniques
- Security best practices

## 🌐 PowerShell Remoting

PowerShell remoting allows you to execute commands on remote systems.

### Basic Remoting Setup

```powershell
# Enable remoting on target machine (requires admin rights)
Enable-PSRemoting -Force

# Test remoting connectivity
Test-WSMan -ComputerName "remote-computer"

# Check remoting status
Get-PSSessionConfiguration | Select-Object Name, Permission

# Configure trusted hosts (for non-domain environments)
Set-Item wsman:\localhost\Client\TrustedHosts -Value "remote-computer" -Force
# Or add multiple hosts
Set-Item wsman:\localhost\Client\TrustedHosts -Value "computer1,computer2,*.domain.com" -Force
```

### Remote Command Execution

```powershell
# One-to-one remoting (interactive session)
Enter-PSSession -ComputerName "remote-computer"
# Exit with: Exit-PSSession

# One-to-many remoting (execute on multiple computers)
$computers = "server1", "server2", "server3"
Invoke-Command -ComputerName $computers -ScriptBlock {
    Get-Service | Where-Object Status -eq "Running" | Select-Object Name, Status
}

# Using credentials
$cred = Get-Credential
Invoke-Command -ComputerName "remote-computer" -Credential $cred -ScriptBlock {
    Get-Process | Sort-Object CPU -Descending | Select-Object -First 5
}

# Execute local script on remote computer
Invoke-Command -ComputerName "remote-computer" -FilePath ".\local-script.ps1"

# Persistent sessions
$session = New-PSSession -ComputerName "remote-computer"
Invoke-Command -Session $session -ScriptBlock { $env:COMPUTERNAME }
Invoke-Command -Session $session -ScriptBlock { Get-Date }
Remove-PSSession -Session $session
```

### Advanced Remoting Techniques

```powershell
# Custom session configuration
Register-PSSessionConfiguration -Name "AdminConfig" -RunAsCredential "domain\admin" -Force

# Using session options
$sessionOption = New-PSSessionOption -MaximumRedirection 1 -NoMachineProfile
$session = New-PSSession -ComputerName "remote-computer" -SessionOption $sessionOption

# Remoting over HTTPS (more secure)
# Requires SSL certificate setup on target
Invoke-Command -ComputerName "remote-computer" -UseSSL -Port 5986 -ScriptBlock {
    Get-ChildItem Cert:\LocalMachine\My
}

# Fan-out remoting (parallel execution)
$jobs = Invoke-Command -ComputerName $computers -ScriptBlock {
    Get-WmiObject -Class Win32_OperatingSystem
} -AsJob

# Wait for all jobs and collect results
$jobs | Wait-Job | Receive-Job
$jobs | Remove-Job
```

## ⚡ Background Jobs

Jobs allow you to run commands asynchronously without blocking the console.

### Basic Job Operations

```powershell
# Start a background job
$job = Start-Job -ScriptBlock {
    Get-Process -Name "powershell" | Select-Object Name, CPU, WorkingSet
}

# Check job status
$job.State

# Get job results when complete
$job | Wait-Job
$result = $job | Receive-Job

# Remove job
$job | Remove-Job

# Start job with parameters
$job = Start-Job -ScriptBlock {
    param($ProcessName, $Count)
    Get-Process -Name $ProcessName | Select-Object -First $Count
} -ArgumentList "powershell", 3
```

### Advanced Job Management

```powershell
# Multiple jobs
$jobs = @()
$processes = "powershell", "notepad", "chrome"

foreach ($process in $processes) {
    $job = Start-Job -ScriptBlock {
        param($ProcName)
        Get-Process -Name $ProcName -ErrorAction SilentlyContinue
    } -ArgumentList $process -Name "$process-job"
    $jobs += $job
}

# Monitor all jobs
while ($jobs.State -contains "Running") {
    Clear-Host
    Write-Host "Job Status:"
    $jobs | Format-Table Name, State, PSBeginTime -AutoSize
    Start-Sleep -Seconds 2
}

# Collect all results
$results = $jobs | Receive-Job
$jobs | Remove-Job

# Scheduled jobs (run at specific times)
$trigger = New-JobTrigger -Daily -At "3:00 AM"
Register-ScheduledJob -Name "DailyReport" -ScriptBlock {
    # Generate daily report
    Get-EventLog -LogName System -EntryType Error -Newest 10 | 
        Export-Csv "C:\reports\errors_$(Get-Date -Format yyyyMMdd).csv"
} -Trigger $trigger

# View scheduled jobs
Get-ScheduledJob
Get-ScheduledJobOption -Name "DailyReport"
```

### Parallel Processing with Jobs

```powershell
function Invoke-ParallelCommand {
    param(
        [Parameter(Mandatory=$true)]
        [string[]]$Computers,
        
        [Parameter(Mandatory=$true)]
        [scriptblock]$ScriptBlock,
        
        [int]$MaxConcurrentJobs = 5,
        
        [int]$TimeoutSeconds = 300
    )
    
    $jobs = @()
    $results = @()
    $completed = 0
    
    # Process computers in batches
    for ($i = 0; $i -lt $Computers.Count; $i += $MaxConcurrentJobs) {
        $batch = $Computers[$i..[math]::Min($i + $MaxConcurrentJobs - 1, $Computers.Count - 1)]
        
        # Start jobs for current batch
        foreach ($computer in $batch) {
            $job = Start-Job -ScriptBlock {
                param($Comp, $Script, $Timeout)
                
                try {
                    $result = Invoke-Command -ComputerName $Comp -ScriptBlock $Script -ErrorAction Stop
                    return @{ Computer = $Comp; Result = $result; Success = $true }
                }
                catch {
                    return @{ Computer = $Comp; Result = $_.Exception.Message; Success = $false }
                }
            } -ArgumentList $computer, $ScriptBlock, $TimeoutSeconds -Name "Job-$computer"
            
            $jobs += $job
        }
        
        # Wait for batch to complete
        $batchJobs = $jobs | Where-Object { $_.State -eq "Running" }
        $batchJobs | Wait-Job -Timeout $TimeoutSeconds | Out-Null
        
        # Collect results
        foreach ($job in $jobs) {
            if ($job.State -eq "Completed") {
                $result = $job | Receive-Job
                $results += $result
                $completed++
                Write-Progress -Activity "Processing Computers" -Status "Completed: $completed/$($Computers.Count)" -PercentComplete (($completed / $Computers.Count) * 100)
            }
            else {
                $results += @{ Computer = ($job.Name -replace "Job-", ""); Result = "Timeout or failed"; Success = $false }
            }
            
            $job | Remove-Job
        }
        
        $jobs = @()
    }
    
    return $results
}

# Usage example
$computers = "server1", "server2", "server3", "server4", "server5"
$scriptBlock = {
    Get-WmiObject -Class Win32_OperatingSystem | Select-Object CSName, Caption, ServicePackMajorVersion
}

$results = Invoke-ParallelCommand -Computers $computers -ScriptBlock $scriptBlock -MaxConcurrentJobs 2
$results | Format-Table Computer, Success, Result -AutoSize
```

## 🔄 PowerShell Workflows

Workflows are designed for long-running, resilient tasks that can survive interruptions.

### Basic Workflow Syntax

```powershell
# Basic workflow structure
workflow Test-Workflow {
    param([string]$ComputerName)
    
    # Workflow activities (special cmdlets)
    InlineScript {
        # Regular PowerShell code
        Get-Date
    }
    
    # Checkpoint for resumability
    Checkpoint-Workflow
    
    # Parallel execution
    Parallel {
        Get-Service
        Get-Process
    }
    
    # Sequence (forces sequential execution)
    Sequence {
        InlineScript { Write-Host "Step 1" }
        InlineScript { Write-Host "Step 2" }
    }
}

# Run workflow
Test-Workflow -ComputerName "localhost"
```

### Advanced Workflow Example

```powershell
workflow Get-SystemInventory {
    param(
        [string[]]$ComputerNames,
        [string]$OutputPath = "C:\inventory"
    )
    
    # Create output directory
    InlineScript {
        if (-not (Test-Path $using:OutputPath)) {
            New-Item -Path $using:OutputPath -ItemType Directory -Force | Out-Null
        }
    }
    
    # Process computers in parallel
    foreach -parallel ($computer in $ComputerNames) {
        $inventory = InlineScript {
            $comp = $using:computer
            
            try {
                # Get system information
                $os = Get-WmiObject -Class Win32_OperatingSystem -ComputerName $comp
                $cs = Get-WmiObject -Class Win32_ComputerSystem -ComputerName $comp
                $services = Get-WmiObject -Class Win32_Service -ComputerName $comp
                
                # Create inventory object
                $inventory = @{
                    ComputerName = $comp
                    OS = $os.Caption
                    Version = $os.Version
                    Manufacturer = $cs.Manufacturer
                    Model = $cs.Model
                    TotalMemory = $cs.TotalPhysicalMemory
                    Processors = $cs.NumberOfProcessors
                    ServiceCount = $services.Count
                    Timestamp = Get-Date
                    Status = "Success"
                }
                
                return $inventory
            }
            catch {
                return @{
                    ComputerName = $comp
                    Status = "Failed"
                    Error = $_.Exception.Message
                    Timestamp = Get-Date
                }
            }
        }
        
        # Save individual computer inventory
        $outputFile = Join-Path $OutputPath "$computer-inventory.json"
        $inventory | ConvertTo-Json | Out-File $outputFile
        
        # Checkpoint for resumability
        Checkpoint-Workflow
    }
    
    # Generate summary report
    $summary = InlineScript {
        $summaryPath = Join-Path $using:OutputPath "inventory-summary.json"
        $inventoryFiles = Get-ChildItem -Path $using:OutputPath -Filter "*-inventory.json"
        
        $summary = @{
            GeneratedAt = Get-Date
            TotalComputers = $using:ComputerNames.Count
            ProcessedComputers = $inventoryFiles.Count
            Inventories = @()
        }
        
        foreach ($file in $inventoryFiles) {
            $inventory = Get-Content $file.FullName | ConvertFrom-Json
            $summary.Inventories += $inventory
        }
        
        $summary | ConvertTo-Json -Depth 3 | Out-File $summaryPath
        return $summary
    }
    
    return $summary
}

# Run workflow
$computers = "server1", "server2", "server3"
$inventory = Get-SystemInventory -ComputerNames $computers
Write-Host "Inventory completed for $($inventory.ProcessedComputers)/$($inventory.TotalComputers) computers"
```

## 📦 Desired State Configuration (DSC)

DSC is a management platform for configuring and monitoring system configuration.

### DSC Configuration Basics

```powershell
# Basic DSC configuration
configuration MyWebServer {
    param([string[]]$ComputerName = "localhost")
    
    Import-DscResource -ModuleName PSDesiredStateConfiguration
    
    Node $ComputerName {
        # Install IIS
        WindowsFeature IIS {
            Ensure = "Present"
            Name = "Web-Server"
        }
        
        # Create website directory
        File WebsiteDirectory {
            Ensure = "Present"
            Type = "Directory"
            DestinationPath = "C:\inetpub\MyWebsite"
        }
        
        # Create default page
        File DefaultPage {
            Ensure = "Present"
            Type = "File"
            DestinationPath = "C:\inetpub\MyWebsite\index.html"
            Contents = "<html><body><h1>Welcome to My Website</h1></body></html>"
            DependsOn = "[File]WebsiteDirectory"
        }
        
        # Create website
        WebSite MyWebsite {
            Ensure = "Present"
            Name = "MyWebsite"
            State = "Started"
            PhysicalPath = "C:\inetpub\MyWebsite"
            DependsOn = @("[WindowsFeature]IIS", "[File]DefaultPage")
        }
    }
}

# Compile configuration
MyWebServer -ComputerName "localhost"

# Apply configuration
Start-DscConfiguration -Path ".\MyWebServer" -Wait -Verbose -Force

# Test configuration
Test-DscConfiguration -Path ".\MyWebServer"
```

### Advanced DSC with Custom Resources

```powershell
# Custom DSC resource for application configuration
configuration ApplicationConfig {
    param([string[]]$ComputerName = "localhost")
    
    Import-DscResource -ModuleName PSDesiredStateConfiguration
    
    Node $ComputerName {
        # Registry configuration
        Registry AppSettings {
            Ensure = "Present"
            Key = "HKEY_LOCAL_MACHINE\SOFTWARE\MyApp"
            ValueName = "LogLevel"
            ValueData = "Info"
            ValueType = "String"
        }
        
        # Service configuration
        Service AppService {
            Ensure = "Present"
            Name = "MyAppService"
            State = "Running"
            StartupType = "Automatic"
        }
        
        # File configuration
        File ConfigFile {
            Ensure = "Present"
            Type = "File"
            DestinationPath = "C:\MyApp\config.json"
            Contents = '{
                "LogLevel": "Info",
                "ConnectionString": "Server=localhost;Database=MyApp;",
                "Timeout": 30
            }'
        }
        
        # Environment variable
        Environment AppEnvironment {
            Ensure = "Present"
            Name = "MYAPP_ENVIRONMENT"
            Value = "Production"
        }
    }
}

# Pull mode DSC
configuration PullClientConfig {
    param([string]$PullServer)
    
    LocalConfigurationManager {
        ConfigurationMode = "ApplyAndAutoCorrect"
        ConfigurationModeFrequencyMins = 15
        RefreshMode = "Pull"
        DownloadManagerName = "WebDownloadManager"
        DownloadManagerCustomData = @{
            ServerUrl = $PullServer
            AllowUnsecureConnection = $true
        }
        ConfigurationID = (New-Guid).ToString()
        CertificateID = $null
        PartialConfiguration = @{}
        ReportServerUrl = $PullServer
        RefreshFrequencyMins = 30
    }
}

# Generate and apply pull client configuration
PullClientConfig -PullServer "http://dsc-pull-server:8080/PSDSCPullServer.svc"
Set-DscLocalConfigurationManager -Path ".\PullClientConfig" -Force
```

## 🏗️ PowerShell Classes

PowerShell 5+ supports object-oriented programming with classes.

### Basic Class Definition

```powershell
# Basic class
class Person {
    [string]$FirstName
    [string]$LastName
    [int]$Age
    
    # Constructor
    Person([string]$firstName, [string]$lastName, [int]$age) {
        $this.FirstName = $firstName
        $this.LastName = $lastName
        $this.Age = $age
    }
    
    # Method
    [string]GetFullName() {
        return "$($this.FirstName) $($this.LastName)"
    }
    
    # Method
    [void]CelebrateBirthday() {
        $this.Age++
        Write-Host "Happy Birthday $($this.FirstName)! You are now $($this.Age) years old."
    }
    
    # Static method
    static [Person]CreateChild([string]$firstName, [string]$lastName) {
        return [Person]::new($firstName, $lastName, 0)
    }
    
    # Property with getter/setter
    [bool]$IsAdult {
        get { return $this.Age -ge 18 }
    }
    
    # Calculated property
    [string]$DisplayName {
        get { return "$($this.FirstName) $($this.LastName) ($($this.Age))" }
    }
}

# Use the class
$person = [Person]::new("John", "Doe", 30)
Write-Host $person.GetFullName()
Write-Host $person.DisplayName
Write-Host "Is adult: $($person.IsAdult)"

$child = [Person]::CreateChild("Jane", "Doe")
Write-Host $child.DisplayName
```

### Advanced Class Features

```powershell
# Inheritance
class Employee : Person {
    [string]$EmployeeId
    [string]$Department
    [decimal]$Salary
    
    Employee([string]$firstName, [string]$lastName, [int]$age, [string]$employeeId, [string]$department, [decimal]$salary) : base($firstName, $lastName, $age) {
        $this.EmployeeId = $employeeId
        $this.Department = $department
        $this.Salary = $salary
    }
    
    [void]GiveRaise([decimal]$percentage) {
        $this.Salary *= (1 + $percentage / 100)
        Write-Host "New salary: $($this.Salary):C"
    }
    
    [string]GetEmployeeInfo() {
        return "$($this.GetFullName()) - $($this.Department) - $($this.EmployeeId)"
    }
    
    # Override method
    [string]GetFullName() {
        return "$($this.FirstName) $($this.LastName) ($($this.EmployeeId))"
    }
}

# Abstract class (simulated)
class Shape {
    [double]$Area
    [double]$Perimeter
    
    [double]GetArea() {
        return $this.Area
    }
    
    [double]GetPerimeter() {
        return $this.Perimeter
    }
}

class Circle : Shape {
    [double]$Radius
    
    Circle([double]$radius) {
        $this.Radius = $radius
        $this.Area = [Math]::PI * $radius * $radius
        $this.Perimeter = 2 * [Math]::PI * $radius
    }
    
    [double]GetCircumference() {
        return $this.Perimeter
    }
}

# Enumeration
enum Department {
    IT
    HR
    Finance
    Marketing
    Operations
}

enum Status {
    Active
    Inactive
    Suspended
    Terminated
}

# Class with enum
class Manager : Employee {
    [Department]$Department
    [Status]$Status
    [string[]]$DirectReports
    
    Manager([string]$firstName, [string]$lastName, [int]$age, [string]$employeeId, [Department]$department, [decimal]$salary) : base($firstName, $lastName, $age, $employeeId, $department.ToString(), $salary) {
        $this.Department = $department
        $this.Status = [Status]::Active
        $this.DirectReports = @()
    }
    
    [void]AddDirectReport([string]$employeeId) {
        $this.DirectReports += $employeeId
    }
    
    [int]GetTeamSize() {
        return $this.DirectReports.Count + 1
    }
}
```

## 🔧 .NET Integration

PowerShell provides deep integration with the .NET Framework.

### Using .NET Classes

```powershell
# Create .NET objects
$stringBuilder = New-Object System.Text.StringBuilder
$stringBuilder.Append("Hello")
$stringBuilder.Append(" ")
$stringBuilder.Append("World")
$result = $stringBuilder.ToString()

# Using static methods
[Math]::Abs(-42)
[Math]::Pow(2, 8)
[Math]::Round(3.14159, 2)
[DateTime]::Now
[DateTime]::Parse("2023-12-25")
[Guid]::NewGuid()

# Working with collections
$list = New-Object "System.Collections.Generic.List[string]"
$list.Add("Item1")
$list.Add("Item2")
$list.Add("Item3")
$list.Contains("Item2")

$dictionary = New-Object "System.Collections.Generic.Dictionary[string, int]"
$dictionary.Add("One", 1)
$dictionary.Add("Two", 2)
$dictionary["One"]

# File operations with .NET
$fileStream = [System.IO.File]::OpenRead("C:\temp\test.txt")
$reader = New-Object System.IO.StreamReader($fileStream)
$content = $reader.ReadToEnd()
$reader.Close()
$fileStream.Close()
```

### Advanced .NET Integration

```powershell
# Event handling
$timer = New-Object System.Timers.Timer
$timer.Interval = 1000  # 1 second
$timer.Enabled = $true

# Register event handler
Register-ObjectEvent -InputObject $timer -EventName Elapsed -Action {
    Write-Host "Timer ticked at $(Get-Date)"
}

# Start timer
$timer.Start()

# Wait for a few seconds
Start-Sleep -Seconds 5

# Stop and cleanup
$timer.Stop()
Unregister-Event -SourceIdentifier $timer.Elapsed
$timer.Dispose()

# Reflection
$stringType = [string]
$methods = $stringType.GetMethods()
$methods | Where-Object { $_.Name -like "Contains*" } | Select-Object Name, ReturnType

# Create custom .NET object
$assembly = [System.Reflection.Assembly]::LoadFrom("CustomLibrary.dll")
$type = $assembly.GetType("CustomLibrary.MyClass")
$instance = [Activator]::CreateInstance($type)
$method = $type.GetMethod("MyMethod")
$result = $method.Invoke($instance, @("parameter"))

# Async operations
$webClient = New-Object System.Net.WebClient
$task = $webClient.DownloadStringTaskAsync("http://example.com")
while (-not $task.IsCompleted) {
    Write-Host "Downloading..."
    Start-Sleep -Milliseconds 500
}
$content = $task.Result
$webClient.Dispose()
```

## ⚡ Performance Optimization

### Measuring Performance

```powershell
# Measure-Command for timing
$measure = Measure-Command {
    Get-Process | Where-Object { $_.WorkingSet -gt 100MB }
}
Write-Host "Execution time: $($measure.TotalMilliseconds) ms"

# Measure-Object for statistics
$processes = Get-Process
$stats = $processes | Measure-Object -Property WorkingSet -Average -Maximum -Minimum -Sum
Write-Host "Average memory: $($stats.Average / 1MB) MB"

# Performance counters
$counter = New-Object System.Diagnostics.PerformanceCounter("Processor", "% Processor Time", "_Total")
$cpuUsage = $counter.NextValue()
Start-Sleep -Seconds 1
$cpuUsage = $counter.NextValue()
Write-Host "CPU usage: $cpuUsage%"
```

### Optimization Techniques

```powershell
# Use .NET methods for better performance
# Slow: PowerShell operators
$slowResult = 1..10000 | ForEach-Object { $_ * 2 }

# Fast: .NET methods
$fastResult = [System.Linq.Enumerable]::Select([int[]](1..10000), [Func[int, int]]{ param($x) $x * 2 })

# Avoid pipeline for simple operations
# Slow: Pipeline
$slowResult = Get-ChildItem | Where-Object { $_.Extension -eq ".txt" }

# Fast: Direct method
$fastResult = [System.IO.Directory]::GetFiles("C:\temp", "*.txt")

# Use StringBuilder for string concatenation
# Slow: String concatenation in loop
$slow = ""
for ($i = 0; $i -lt 10000; $i++) {
    $slow += "Line $i`n"
}

# Fast: StringBuilder
$fast = New-Object System.Text.StringBuilder
for ($i = 0; $i -lt 10000; $i++) {
    $fast.AppendLine("Line $i") | Out-Null
}
$result = $fast.ToString()

# Use appropriate collection types
# For frequent modifications, use ArrayList or Generic List
$arrayList = New-Object System.Collections.ArrayList
$genericList = New-Object "System.Collections.Generic.List[string]"

# Parallel processing with .NET
$cancellationToken = New-Object System.Threading.CancellationTokenSource
$parallelOptions = New-Object System.Threading.Tasks.ParallelOptions
$parallelOptions.CancellationToken = $cancellationToken.Token
$parallelOptions.MaxDegreeOfParallelism = 4

[System.Threading.Tasks.Parallel]::ForEach(
    [string[]]("server1", "server2", "server3", "server4"),
    $parallelOptions,
    [Action[string]]{
        param($server)
        # Process each server
        Write-Host "Processing $server"
    }
)
```

## 🔒 Security Best Practices

### Script Security

```powershell
# Set appropriate execution policy
Set-ExecutionPolicy -ExecutionPolicy AllSigned -Scope LocalMachine

# Script signing
$cert = Get-ChildItem Cert:\CurrentUser\My -CodeSigningCert | Select-Object -First 1
Set-AuthenticodeSignature -FilePath ".\MyScript.ps1" -Certificate $cert

# Verify signature
$signature = Get-AuthenticodeSignature ".\MyScript.ps1"
if ($signature.Status -eq "Valid") {
    Write-Host "Script signature is valid"
}

# Secure credential handling
function Get-SecureCredential {
    param([string]$UserName)
    
    $credential = Get-Credential -UserName $UserName -Message "Enter password"
    return $credential
}

# Use secure strings
$secureString = ConvertTo-SecureString "MySecretPassword" -AsPlainText -Force
$credential = New-Object System.Management.Automation.PSCredential("username", $secureString)

# JEA (Just Enough Administration) configuration
# Create JEA endpoint
Register-PSSessionConfiguration -Name "MaintenanceJEA" -Path ".\JEAConfig.pssc" -Force
```

### Secure Coding Practices

```powershell
# Input validation
function Get-SecureData {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory=$true)]
        [ValidatePattern("^[a-zA-Z0-9]{3,20}$")]
        [string]$Username,
        
        [ValidateRange(1, 100)]
        [int]$Count,
        
        [ValidateSet("Read", "Write", "Admin")]
        [string]$Permission
    )
    
    # Sanitize input
    $sanitizedUsername = $Username -replace "[^a-zA-Z0-9]", ""
    
    # Use parameter binding safely
    $query = "SELECT * FROM Users WHERE Username = @Username"
    
    # Avoid injection attacks
    # Never do this: "SELECT * FROM Users WHERE Username = '$Username'"
    
    return $sanitizedUsername
}

# Secure file operations
function Write-SecureFile {
    param(
        [Parameter(Mandatory=$true)]
        [string]$Path,
        
        [Parameter(Mandatory=$true)]
        [string]$Content
    )
    
    # Validate path
    $resolvedPath = Resolve-Path $Path -ErrorAction SilentlyContinue
    if (-not $resolvedPath) {
        throw "Invalid path: $Path"
    }
    
    # Check for directory traversal
    if ($Path -contains ".." -or $Path -contains "~") {
        throw "Path traversal detected"
    }
    
    # Use secure encoding
    $bytes = [System.Text.Encoding]::UTF8.GetBytes($Content)
    [System.IO.File]::WriteAllBytes($resolvedPath.Path, $bytes)
}

# Logging security
function Write-SecureLog {
    param(
        [string]$Message,
        [string]$LogPath = "C:\logs\secure.log"
    )
    
    # Sanitize log message
    $sanitizedMessage = $Message -replace "[^\w\s\-.,]", ""
    
    # Add timestamp and context
    $logEntry = "$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss') [$env:USERNAME] $sanitizedMessage"
    
    # Ensure log directory exists and has proper permissions
    $logDir = Split-Path $LogPath -Parent
    if (-not (Test-Path $logDir)) {
        New-Item -Path $logDir -ItemType Directory -Force | Out-Null
    }
    
    # Append to log file
    Add-Content -Path $LogPath -Value $logEntry -Encoding UTF8
}
```

## 🚀 Practical Examples

### Example 1: Distributed System Monitor

```powershell
class SystemMonitor {
    [string[]]$Computers
    [hashtable]$Results
    [System.Collections.Generic.List[PSObject]]$Alerts
    [int]$ConcurrentJobs
    [int]$TimeoutSeconds
    
    SystemMonitor([string[]]$computers, [int]$concurrentJobs = 10, [int]$timeoutSeconds = 30) {
        $this.Computers = $computers
        $this.Results = @{}
        $this.Alerts = New-Object "System.Collections.Generic.List[PSObject]"
        $this.ConcurrentJobs = $concurrentJobs
        $this.TimeoutSeconds = $timeoutSeconds
    }
    
    [void]MonitorSystems() {
        Write-Host "Starting system monitoring for $($this.Computers.Count) computers"
        
        $jobs = @()
        $batchSize = $this.ConcurrentJobs
        
        # Process in batches
        for ($i = 0; $i -lt $this.Computers.Count; $i += $batchSize) {
            $batch = $this.Computers[$i..[math]::Min($i + $batchSize - 1, $this.Computers.Count - 1)]
            
            foreach ($computer in $batch) {
                $job = Start-Job -ScriptBlock {
                    param($Comp, $Timeout)
                    
                    try {
                        $session = New-PSSession -ComputerName $Comp -ErrorAction Stop
                        $data = Invoke-Command -Session $session -ScriptBlock {
                            # Get system metrics
                            $cpu = Get-WmiObject -Class Win32_Processor | Select-Object -First 1
                            $memory = Get-WmiObject -Class Win32_OperatingSystem
                            $disk = Get-WmiObject -Class Win32_LogicalDisk -Filter "DeviceID='C:'"
                            $services = Get-Service | Where-Object { $_.Status -eq "Stopped" -and $_.StartType -eq "Automatic" }
                            
                            return @{
                                ComputerName = $env:COMPUTERNAME
                                CPUUsage = $cpu.LoadPercentage
                                TotalMemory = $memory.TotalVisibleMemorySize
                                FreeMemory = $memory.FreePhysicalMemory
                                DiskUsage = [math]::Round((($disk.Size - $disk.FreeSpace) / $disk.Size) * 100, 2)
                                StoppedServices = $services.Count
                                Timestamp = Get-Date
                                Status = "Online"
                            }
                        } -ErrorAction Stop
                        
                        Remove-PSSession -Session $session
                        return $data
                    }
                    catch {
                        return @{
                            ComputerName = $Comp
                            Status = "Offline"
                            Error = $_.Exception.Message
                            Timestamp = Get-Date
                        }
                    }
                } -ArgumentList $computer, $this.TimeoutSeconds -Name "Monitor-$computer"
                
                $jobs += $job
            }
            
            # Wait for batch completion
            $batchJobs = $jobs | Where-Object { $_.State -eq "Running" }
            if ($batchJobs) {
                $batchJobs | Wait-Job -Timeout $this.TimeoutSeconds | Out-Null
            }
            
            # Collect results
            foreach ($job in $jobs) {
                if ($job.State -eq "Completed") {
                    $result = $job | Receive-Job
                    $this.Results[$($job.Name -replace "Monitor-", "")] = $result
                    
                    # Check for alerts
                    $this.CheckAlerts($result)
                }
                else {
                    $this.Results[$($job.Name -replace "Monitor-", "")] = @{
                        ComputerName = ($job.Name -replace "Monitor-", "")
                        Status = "Timeout"
                        Timestamp = Get-Date
                    }
                }
                
                $job | Remove-Job
            }
            
            $jobs = @()
        }
        
        Write-Host "Monitoring completed for all computers"
    }
    
    [void]CheckAlerts([PSObject]$data) {
        if ($data.Status -eq "Online") {
            # CPU alert
            if ($data.CPUUsage -gt 90) {
                $alert = [PSCustomObject]@{
                    ComputerName = $data.ComputerName
                    Type = "CPU"
                    Message = "High CPU usage: $($data.CPUUsage)%"
                    Severity = "High"
                    Timestamp = $data.Timestamp
                }
                $this.Alerts.Add($alert)
            }
            
            # Memory alert
            $memoryUsage = [math]::Round((($data.TotalMemory - $data.FreeMemory) / $data.TotalMemory) * 100, 2)
            if ($memoryUsage -gt 90) {
                $alert = [PSCustomObject]@{
                    ComputerName = $data.ComputerName
                    Type = "Memory"
                    Message = "High memory usage: $memoryUsage%"
                    Severity = "High"
                    Timestamp = $data.Timestamp
                }
                $this.Alerts.Add($alert)
            }
            
            # Disk alert
            if ($data.DiskUsage -gt 85) {
                $alert = [PSCustomObject]@{
                    ComputerName = $data.ComputerName
                    Type = "Disk"
                    Message = "High disk usage: $($data.DiskUsage)%"
                    Severity = "Medium"
                    Timestamp = $data.Timestamp
                }
                $this.Alerts.Add($alert)
            }
            
            # Service alert
            if ($data.StoppedServices -gt 0) {
                $alert = [PSCustomObject]@{
                    ComputerName = $data.ComputerName
                    Type = "Service"
                    Message = "$($data.StoppedServices) critical services stopped"
                    Severity = "Medium"
                    Timestamp = $data.Timestamp
                }
                $this.Alerts.Add($alert)
            }
        }
        else {
            # Offline alert
            $alert = [PSCustomObject]@{
                ComputerName = $data.ComputerName
                Type = "Connectivity"
                Message = "System offline: $($data.Error)"
                Severity = "Critical"
                Timestamp = $data.Timestamp
            }
            $this.Alerts.Add($alert)
        }
    }
    
    [void]GenerateReport() {
        Write-Host "`n=== System Monitor Report ==="
        Write-Host "Monitored Computers: $($this.Computers.Count)"
        
        $onlineCount = ($this.Results.Values | Where-Object { $_.Status -eq "Online" }).Count
        $offlineCount = ($this.Results.Values | Where-Object { $_.Status -ne "Online" }).Count
        
        Write-Host "Online: $onlineCount"
        Write-Host "Offline: $offlineCount"
        Write-Host "Alerts: $($this.Alerts.Count)"
        
        if ($this.Alerts.Count -gt 0) {
            Write-Host "`n=== Alerts ==="
            $this.Alerts | Sort-Object Severity, Timestamp | ForEach-Object {
                $color = switch ($_.Severity) {
                    "Critical" { "Red" }
                    "High" { "Yellow" }
                    "Medium" { "Yellow" }
                    default { "White" }
                }
                Write-Host "[$($_.Timestamp)] $($_.ComputerName) - $($_.Type): $($_.Message)" -ForegroundColor $color
            }
        }
        
        # Export results
        $reportPath = "system-monitor-report-$(Get-Date -Format yyyyMMdd-HHmmss).json"
        $report = @{
            GeneratedAt = Get-Date
            Computers = $this.Computers
            Results = $this.Results
            Alerts = $this.Alerts
            Summary = @{
                Total = $this.Computers.Count
                Online = $onlineCount
                Offline = $offlineCount
                AlertCount = $this.Alerts.Count
            }
        }
        
        $report | ConvertTo-Json -Depth 4 | Set-Content $reportPath
        Write-Host "`nReport saved to: $reportPath"
    }
}

# Usage example
$computers = "server1", "server2", "server3", "localhost"
$monitor = [SystemMonitor]::new($computers, 2, 15)
$monitor.MonitorSystems()
$monitor.GenerateReport()
```

## 📝 Exercises

### Exercise 1: Distributed Task Runner
Create a distributed task runner that:
1. Uses remoting to execute tasks on multiple computers
2. Implements parallel processing with jobs
3. Provides real-time progress updates
4. Handles failures gracefully
5. Generates comprehensive reports

### Exercise 2: Configuration Management System
Build a configuration management system that:
1. Uses DSC for system configuration
2. Implements custom DSC resources
3. Supports pull and push modes
4. Provides configuration drift detection
5. Includes compliance reporting

### Exercise 3: Performance Analysis Tool
Create a performance analysis tool that:
1. Monitors multiple performance counters
2. Implements real-time alerting
3. Uses workflows for long-running monitoring
4. Provides historical data analysis
5. Generates performance reports

## 🎯 Key Takeaways

- **Remoting** enables remote command execution and management
- **Jobs** provide asynchronous processing capabilities
- **Workflows** are designed for long-running, resilient tasks
- **DSC** provides declarative configuration management
- **Classes** enable object-oriented programming in PowerShell
- **.NET integration** provides access to powerful framework capabilities
- **Performance optimization** requires understanding of PowerShell internals
- **Security** should be considered in all script development

## 🎓 Conclusion

Congratulations! You've completed the comprehensive PowerShell learning guide. You now have the knowledge and skills to:

- Write efficient and effective PowerShell scripts
- Automate complex system administration tasks
- Develop robust modules and functions
- Implement proper error handling and logging
- Use advanced features like remoting and DSC
- Apply security best practices
- Optimize performance for large-scale operations

Continue practicing and exploring the PowerShell ecosystem to become a true PowerShell expert!

## 📚 Additional Resources

- [Official PowerShell Documentation](https://docs.microsoft.com/en-us/powershell/)
- [PowerShell Gallery](https://www.powershellgallery.com/)
- [PowerShell Community](https://powershell.org/)
- [PowerShell Blog](https://devblogs.microsoft.com/powershell/)
- [PowerShell GitHub](https://github.com/PowerShell/PowerShell)
