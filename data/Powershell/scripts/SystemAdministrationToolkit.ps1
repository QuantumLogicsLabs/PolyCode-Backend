# PowerShell Learning Project - System Administration Toolkit
# Comprehensive project demonstrating all learned concepts

<#
.SYNOPSIS
    System Administration Toolkit - Comprehensive PowerShell project
.DESCRIPTION
    This project demonstrates practical application of PowerShell concepts learned
    throughout the learning guide. It includes system monitoring, user management,
    service management, and reporting capabilities.
    
    Features demonstrated:
    - Functions and parameter validation
    - Error handling and logging
    - File operations and configuration management
    - Custom objects and classes
    - Background jobs for parallel processing
    - Module structure and organization
    - Security best practices
.NOTES
    File:      SystemAdministrationToolkit.ps1
    Author:    PowerShell Learning Guide
    Version:   1.0.0
    Created:   2023-12-01
#>

# Module requirements
#Requires -Version 5.1

# Write-Host banner
Write-Host "=== System Administration Toolkit ===" -ForegroundColor Green
Write-Host "Comprehensive PowerShell Learning Project" -ForegroundColor Cyan
Write-Host "Version 1.0.0" -ForegroundColor Gray

# Configuration management class
class ConfigurationManager {
    [string]$ConfigPath
    [hashtable]$Settings
    [bool]$IsLoaded
    
    ConfigurationManager([string]$configPath) {
        $this.ConfigPath = $configPath
        $this.Settings = @{}
        $this.IsLoaded = $false
    }
    
    [void]LoadConfiguration() {
        try {
            if (Test-Path $this.ConfigPath) {
                $content = Get-Content $this.ConfigPath -Raw | ConvertFrom-Json
                $this.Settings = $content
                $this.IsLoaded = $true
                Write-Host "Configuration loaded from $($this.ConfigPath)" -ForegroundColor Green
            }
            else {
                $this.Settings = $this.GetDefaultSettings()
                $this.SaveConfiguration()
                Write-Host "Created default configuration" -ForegroundColor Yellow
            }
        }
        catch {
            Write-Error "Failed to load configuration: $($_.Exception.Message)"
            $this.Settings = $this.GetDefaultSettings()
            $this.IsLoaded = $false
        }
    }
    
    [hashtable]GetDefaultSettings() {
        return @{
            General = @{
                LogPath = "./toolkit.log"
                MaxConcurrentJobs = 5
                TimeoutSeconds = 30
            }
            Monitoring = @{
                CPUThreshold = 80
                MemoryThreshold = 85
                DiskThreshold = 90
                ServiceCheckInterval = 300
            }
            Reporting = @{
                OutputPath = "./reports"
                EmailEnabled = $false
                EmailRecipients = @()
                RetentionDays = 30
            }
            Security = @{
                RequireElevation = $true
                LogSensitiveData = $false
                AuditCommands = $true
            }
        }
    }
    
    [void]SaveConfiguration() {
        try {
            $json = $this.Settings | ConvertTo-Json -Depth 4
            $json | Set-Content $this.ConfigPath
            $this.IsLoaded = $true
        }
        catch {
            Write-Error "Failed to save configuration: $($_.Exception.Message)"
        }
    }
    
    [object]GetSetting([string]$section, [string]$key) {
        if ($this.Settings.ContainsKey($section) -and $this.Settings[$section].ContainsKey($key)) {
            return $this.Settings[$section][$key]
        }
        return $null
    }
    
    [void]SetSetting([string]$section, [string]$key, [object]$value) {
        if (-not $this.Settings.ContainsKey($section)) {
            $this.Settings[$section] = @{}
        }
        $this.Settings[$section][$key] = $value
        $this.SaveConfiguration()
    }
}

# Logging system
class Logger {
    [string]$LogPath
    [int]$LogLevel  # 0=Info, 1=Warning, 2=Error, 3=Critical
    
    Logger([string]$logPath, [int]$logLevel = 0) {
        $this.LogPath = $logPath
        $this.LogLevel = $logLevel
        $this.InitializeLog()
    }
    
    [void]InitializeLog() {
        $logDir = Split-Path $this.LogPath -Parent
        if (-not (Test-Path $logDir)) {
            New-Item -Path $logDir -ItemType Directory -Force | Out-Null
        }
        
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        $initMessage = "[$timestamp] [INFO] System Administration Toolkit initialized"
        $initMessage | Set-Content $this.LogPath
    }
    
    [void]WriteLog([string]$level, [string]$message, [string]$color = "White") {
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        $logEntry = "[$timestamp] [$level] $message"
        
        $logEntry | Add-Content $this.LogPath
        Write-Host $logEntry -ForegroundColor $color
    }
    
    [void]Info([string]$message) {
        if ($this.LogLevel -le 0) {
            $this.WriteLog("INFO", $message, "Green")
        }
    }
    
    [void]Warning([string]$message) {
        if ($this.LogLevel -le 1) {
            $this.WriteLog("WARNING", $message, "Yellow")
        }
    }
    
    [void]Error([string]$message) {
        if ($this.LogLevel -le 2) {
            $this.WriteLog("ERROR", $message, "Red")
        }
    }
    
    [void]Critical([string]$message) {
        if ($this.LogLevel -le 3) {
            $this.WriteLog("CRITICAL", $message, "Magenta")
        }
    }
}

# System monitor class
class SystemMonitor {
    [Logger]$Logger
    [ConfigurationManager]$Config
    
    SystemMonitor([Logger]$logger, [ConfigurationManager]$config) {
        $this.Logger = $logger
        $this.Config = $config
    }
    
    [PSCustomObject]GetSystemInfo([string]$computerName = "localhost") {
        try {
            $this.Logger.Info("Getting system information for: $computerName")
            
            $os = Get-WmiObject -Class Win32_OperatingSystem -ComputerName $computerName -ErrorAction Stop
            $cs = Get-WmiObject -Class Win32_ComputerSystem -ComputerName $computerName -ErrorAction Stop
            $processors = Get-WmiObject -Class Win32_Processor -ComputerName $computerName -ErrorAction Stop
            
            $systemInfo = [PSCustomObject]@{
                ComputerName = $computerName
                OS = $os.Caption
                Version = $os.Version
                Manufacturer = $cs.Manufacturer
                Model = $cs.Model
                TotalMemory = [math]::Round($cs.TotalPhysicalMemory / 1GB, 2)
                Processors = $processors.Count
                Architecture = $cs.SystemType
                LastBootTime = $cs.ConvertToDateTime($cs.LastBootUpTime)
                Status = "Online"
                Timestamp = Get-Date
            }
            
            $this.Logger.Info("System information retrieved successfully")
            return $systemInfo
        }
        catch {
            $this.Logger.Error("Failed to get system information for $computerName`: $($_.Exception.Message)")
            return [PSCustomObject]@{
                ComputerName = $computerName
                Status = "Offline"
                Error = $_.Exception.Message
                Timestamp = Get-Date
            }
        }
    }
    
    [PSCustomObject]GetPerformanceMetrics([string]$computerName = "localhost") {
        try {
            $this.Logger.Info("Getting performance metrics for: $computerName")
            
            $cpu = Get-WmiObject -Class Win32_Processor -ComputerName $computerName -ErrorAction Stop | Select-Object -First 1
            $memory = Get-WmiObject -Class Win32_OperatingSystem -ComputerName $computerName -ErrorAction Stop
            $disk = Get-WmiObject -Class Win32_LogicalDisk -ComputerName $computerName -Filter "DeviceID='C:'" -ErrorAction Stop
            
            $cpuUsage = $cpu.LoadPercentage
            $totalMemory = $memory.TotalVisibleMemorySize
            $freeMemory = $memory.FreePhysicalMemory
            $memoryUsage = [math]::Round((($totalMemory - $freeMemory) / $totalMemory) * 100, 2)
            $diskUsage = [math]::Round((($disk.Size - $disk.FreeSpace) / $disk.Size) * 100, 2)
            
            $metrics = [PSCustomObject]@{
                ComputerName = $computerName
                CPUUsage = $cpuUsage
                MemoryUsage = $memoryUsage
                DiskUsage = $diskUsage
                TotalMemoryGB = [math]::Round($totalMemory / 1MB, 2)
                FreeMemoryGB = [math]::Round($freeMemory / 1MB, 2)
                DiskSizeGB = [math]::Round($disk.Size / 1GB, 2)
                FreeDiskGB = [math]::Round($disk.FreeSpace / 1GB, 2)
                Timestamp = Get-Date
                Status = "Healthy"
            }
            
            # Check thresholds
            $cpuThreshold = $this.Config.GetSetting("Monitoring", "CPUThreshold")
            $memoryThreshold = $this.Config.GetSetting("Monitoring", "MemoryThreshold")
            $diskThreshold = $this.Config.GetSetting("Monitoring", "DiskThreshold")
            
            if ($cpuUsage -gt $cpuThreshold -or $memoryUsage -gt $memoryThreshold -or $diskUsage -gt $diskThreshold) {
                $metrics.Status = "Warning"
                $this.Logger.Warning("Performance thresholds exceeded on $computerName")
            }
            
            return $metrics
        }
        catch {
            $this.Logger.Error("Failed to get performance metrics for $computerName`: $($_.Exception.Message)")
            return [PSCustomObject]@{
                ComputerName = $computerName
                Status = "Error"
                Error = $_.Exception.Message
                Timestamp = Get-Date
            }
        }
    }
    
    [System.Collections.Generic.List[PSCustomObject]]MonitorMultipleSystems([string[]]$computerNames) {
        $this.Logger.Info("Starting monitoring for $($computerNames.Count) systems")
        
        $maxJobs = $this.Config.GetSetting("General", "MaxConcurrentJobs")
        $timeout = $this.Config.GetSetting("General", "TimeoutSeconds")
        
        $results = New-Object "System.Collections.Generic.List[PSCustomObject]"
        $jobs = @()
        
        # Process in batches
        for ($i = 0; $i -lt $computerNames.Count; $i += $maxJobs) {
            $batch = $computerNames[$i..[math]::Min($i + $maxJobs - 1, $computerNames.Count - 1)]
            
            foreach ($computer in $batch) {
                $job = Start-Job -ScriptBlock {
                    param($Comp, $Timeout)
                    
                    try {
                        # Get system info
                        $os = Get-WmiObject -Class Win32_OperatingSystem -ComputerName $Comp -ErrorAction Stop
                        $cs = Get-WmiObject -Class Win32_ComputerSystem -ComputerName $Comp -ErrorAction Stop
                        $cpu = Get-WmiObject -Class Win32_Processor -ComputerName $Comp -ErrorAction Stop | Select-Object -First 1
                        $disk = Get-WmiObject -Class Win32_LogicalDisk -ComputerName $Comp -Filter "DeviceID='C:'" -ErrorAction Stop
                        
                        $memoryUsage = [math]::Round((($os.TotalVisibleMemorySize - $os.FreePhysicalMemory) / $os.TotalVisibleMemorySize) * 100, 2)
                        $diskUsage = [math]::Round((($disk.Size - $disk.FreeSpace) / $disk.Size) * 100, 2)
                        
                        return [PSCustomObject]@{
                            ComputerName = $Comp
                            OS = $os.Caption
                            Model = $cs.Model
                            CPUUsage = $cpu.LoadPercentage
                            MemoryUsage = $memoryUsage
                            DiskUsage = $diskUsage
                            Status = "Online"
                            Timestamp = Get-Date
                        }
                    }
                    catch {
                        return [PSCustomObject]@{
                            ComputerName = $Comp
                            Status = "Offline"
                            Error = $_.Exception.Message
                            Timestamp = Get-Date
                        }
                    }
                } -ArgumentList $computer, $timeout -Name "Monitor-$computer"
                
                $jobs += $job
            }
            
            # Wait for batch completion
            $batchJobs = $jobs | Where-Object { $_.State -eq "Running" }
            if ($batchJobs) {
                $batchJobs | Wait-Job -Timeout $timeout | Out-Null
            }
            
            # Collect results
            foreach ($job in $jobs) {
                if ($job.State -eq "Completed") {
                    $result = $job | Receive-Job
                    $results.Add($result)
                }
                else {
                    $results.Add([PSCustomObject]@{
                        ComputerName = ($job.Name -replace "Monitor-", "")
                        Status = "Timeout"
                        Timestamp = Get-Date
                    })
                }
                
                $job | Remove-Job
            }
            
            $jobs = @()
        }
        
        $this.Logger.Info("Monitoring completed for $($results.Count) systems")
        return $results
    }
}

# Service manager class
class ServiceManager {
    [Logger]$Logger
    [ConfigurationManager]$Config
    
    ServiceManager([Logger]$logger, [ConfigurationManager]$config) {
        $this.Logger = $logger
        $this.Config = $config
    }
    
    [PSCustomObject[]]GetServices([string]$computerName = "localhost", [string]$serviceName = $null) {
        try {
            $this.Logger.Info("Getting services for: $computerName")
            
            $params = @{
                ComputerName = $computerName
                ErrorAction = "Stop"
            }
            
            if ($serviceName) {
                $params.Name = $serviceName
            }
            
            $services = Get-Service @params
            
            $serviceInfo = $services | ForEach-Object {
                [PSCustomObject]@{
                    Name = $_.Name
                    DisplayName = $_.DisplayName
                    Status = $_.Status
                    StartType = $_.StartType
                    CanStop = $_.CanStop
                    CanPauseAndContinue = $_.CanPauseAndContinue
                    ComputerName = $computerName
                    Timestamp = Get-Date
                }
            }
            
            $this.Logger.Info("Retrieved $($serviceInfo.Count) services")
            return $serviceInfo
        }
        catch {
            $this.Logger.Error("Failed to get services for $computerName`: $($_.Exception.Message)")
            return @()
        }
    }
    
    [bool]StartService([string]$serviceName, [string]$computerName = "localhost") {
        try {
            $this.Logger.Info("Starting service: $serviceName on $computerName")
            
            $service = Get-Service -Name $serviceName -ComputerName $computerName -ErrorAction Stop
            
            if ($service.Status -eq "Running") {
                $this.Logger.Warning("Service $serviceName is already running")
                return $true
            }
            
            Start-Service -Name $serviceName -ComputerName $computerName -ErrorAction Stop
            
            # Verify service started
            $timeout = 30
            $elapsed = 0
            while ($elapsed -lt $timeout) {
                $service = Get-Service -Name $serviceName -ComputerName $computerName -ErrorAction SilentlyContinue
                if ($service.Status -eq "Running") {
                    $this.Logger.Info("Service $serviceName started successfully")
                    return $true
                }
                Start-Sleep -Seconds 2
                $elapsed += 2
            }
            
            $this.Logger.Error("Service $serviceName failed to start within timeout")
            return $false
        }
        catch {
            $this.Logger.Error("Failed to start service $serviceName`: $($_.Exception.Message)")
            return $false
        }
    }
    
    [bool]StopService([string]$serviceName, [string]$computerName = "localhost") {
        try {
            $this.Logger.Info("Stopping service: $serviceName on $computerName")
            
            $service = Get-Service -Name $serviceName -ComputerName $computerName -ErrorAction Stop
            
            if ($service.Status -eq "Stopped") {
                $this.Logger.Warning("Service $serviceName is already stopped")
                return $true
            }
            
            Stop-Service -Name $serviceName -ComputerName $computerName -Force -ErrorAction Stop
            
            # Verify service stopped
            $timeout = 30
            $elapsed = 0
            while ($elapsed -lt $timeout) {
                $service = Get-Service -Name $serviceName -ComputerName $computerName -ErrorAction SilentlyContinue
                if ($service.Status -eq "Stopped") {
                    $this.Logger.Info("Service $serviceName stopped successfully")
                    return $true
                }
                Start-Sleep -Seconds 2
                $elapsed += 2
            }
            
            $this.Logger.Error("Service $serviceName failed to stop within timeout")
            return $false
        }
        catch {
            $this.Logger.Error("Failed to stop service $serviceName`: $($_.Exception.Message)")
            return $false
        }
    }
    
    [PSCustomObject[]]GetCriticalServices([string]$computerName = "localhost") {
        $this.Logger.Info("Getting critical services for: $computerName")
        
        $criticalServices = @(
            "EventLog", "RpcSs", "lanmanserver", "lanmanworkstation",
            "Netlogon", "DNS", "DHCP", "W32Time", "BITS", "WinRM"
        )
        
        $services = $this.GetServices($computerName)
        $criticalServicesInfo = $services | Where-Object { $_.Name -in $criticalServices }
        
        $this.Logger.Info("Found $($criticalServicesInfo.Count) critical services")
        return $criticalServicesInfo
    }
}

# Report generator class
class ReportGenerator {
    [Logger]$Logger
    [ConfigurationManager]$Config
    
    ReportGenerator([Logger]$logger, [ConfigurationManager]$config) {
        $this.Logger = $logger
        $this.Config = $config
    }
    
    [void]GenerateSystemReport([System.Collections.Generic.List[PSCustomObject]]$systemData) {
        $this.Logger.Info("Generating system report")
        
        $outputPath = $this.Config.GetSetting("Reporting", "OutputPath")
        if (-not (Test-Path $outputPath)) {
            New-Item -Path $outputPath -ItemType Directory -Force | Out-Null
        }
        
        $timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
        $reportPath = Join-Path $outputPath "SystemReport-$timestamp.html"
        
        $html = @"
<!DOCTYPE html>
<html>
<head>
    <title>System Administration Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; }
        .summary { background-color: #f2f2f2; padding: 15px; margin: 20px 0; }
        .system { border: 1px solid #ddd; margin: 10px 0; padding: 15px; }
        .online { border-left: 5px solid #4CAF50; }
        .offline { border-left: 5px solid #f44336; }
        .warning { border-left: 5px solid #ff9800; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .metric { display: inline-block; margin: 5px; padding: 5px 10px; border-radius: 3px; }
        .good { background-color: #dff0d8; color: #3c763d; }
        .warning { background-color: #fcf8e3; color: #8a6d3b; }
        .danger { background-color: #f2dede; color: #a94442; }
    </style>
</head>
<body>
    <div class="header">
        <h1>System Administration Report</h1>
        <p>Generated on $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')</p>
    </div>
    
    <div class="summary">
        <h2>Summary</h2>
        <p>Total Systems: $($systemData.Count)</p>
        <p>Online: $($systemData.Count | Where-Object { $_.Status -eq "Online" }).Count)</p>
        <p>Offline: $($systemData.Count | Where-Object { $_.Status -eq "Offline" }).Count)</p>
        <p>Warning: $($systemData.Count | Where-Object { $_.Status -eq "Warning" }).Count)</p>
    </div>
"@
        
        foreach ($system in $systemData) {
            $statusClass = switch ($system.Status) {
                "Online" { "online" }
                "Offline" { "offline" }
                "Warning" { "warning" }
                default { "offline" }
            }
            
            $cpuClass = if ($system.CPUUsage -lt 80) { "good" } elseif ($system.CPUUsage -lt 90) { "warning" } else { "danger" }
            $memoryClass = if ($system.MemoryUsage -lt 80) { "good" } elseif ($system.MemoryUsage -lt 90) { "warning" } else { "danger" }
            $diskClass = if ($system.DiskUsage -lt 80) { "good" } elseif ($system.DiskUsage -lt 90) { "warning" } else { "danger" }
            
            $html += @"
    <div class="system $statusClass">
        <h3>$($system.ComputerName)</h3>
        <p><strong>Status:</strong> $($system.Status)</p>
        <p><strong>OS:</strong> $($system.OS)</p>
        <p><strong>Model:</strong> $($system.Model)</p>
        
        <div class="metric $cpuClass">CPU: $($system.CPUUsage)%</div>
        <div class="metric $memoryClass">Memory: $($system.MemoryUsage)%</div>
        <div class="metric $diskClass">Disk: $($system.DiskUsage)%</div>
        
        <table>
            <tr>
                <th>Metric</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Total Memory</td>
                <td>$($system.TotalMemoryGB) GB</td>
            </tr>
            <tr>
                <td>Free Memory</td>
                <td>$($system.FreeMemoryGB) GB</td>
            </tr>
            <tr>
                <td>Disk Size</td>
                <td>$($system.DiskSizeGB) GB</td>
            </tr>
            <tr>
                <td>Free Disk</td>
                <td>$($system.FreeDiskGB) GB</td>
            </tr>
        </table>
    </div>
"@
        }
        
        $html += @"
</body>
</html>
"@
        
        $html | Set-Content $reportPath
        $this.Logger.Info("System report generated: $reportPath")
        
        # Open report in browser
        Start-Process $reportPath
    }
    
    [void]GenerateServiceReport([PSCustomObject[]]$serviceData) {
        $this.Logger.Info("Generating service report")
        
        $outputPath = $this.Config.GetSetting("Reporting", "OutputPath")
        $timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
        $reportPath = Join-Path $outputPath "ServiceReport-$timestamp.csv"
        
        $serviceData | Export-Csv -Path $reportPath -NoTypeInformation
        $this.Logger.Info("Service report generated: $reportPath")
    }
}

# Main toolkit class
class SystemAdministrationToolkit {
    [ConfigurationManager]$Config
    [Logger]$Logger
    [SystemMonitor]$Monitor
    [ServiceManager]$ServiceManager
    [ReportGenerator]$ReportGenerator
    
    SystemAdministrationToolkit() {
        # Initialize components
        $this.Config = [ConfigurationManager]::new("./toolkit-config.json")
        $this.Config.LoadConfiguration()
        
        $logPath = $this.Config.GetSetting("General", "LogPath")
        $this.Logger = [Logger]::new($logPath)
        
        $this.Monitor = [SystemMonitor]::new($this.Logger, $this.Config)
        $this.ServiceManager = [ServiceManager]::new($this.Logger, $this.Config)
        $this.ReportGenerator = [ReportGenerator]::new($this.Logger, $this.Config)
        
        $this.Logger.Info("System Administration Toolkit initialized")
    }
    
    [void]ShowMenu() {
        while ($true) {
            Clear-Host
            Write-Host "=== System Administration Toolkit ===" -ForegroundColor Green
            Write-Host "1. Get System Information" -ForegroundColor Cyan
            Write-Host "2. Monitor Performance" -ForegroundColor Cyan
            Write-Host "3. Monitor Multiple Systems" -ForegroundColor Cyan
            Write-Host "4. Manage Services" -ForegroundColor Cyan
            Write-Host "5. Generate Reports" -ForegroundColor Cyan
            Write-Host "6. Configuration Settings" -ForegroundColor Cyan
            Write-Host "7. View Logs" -ForegroundColor Cyan
            Write-Host "8. Exit" -ForegroundColor Red
            Write-Host "========================================"
            
            $choice = Read-Host "Enter your choice (1-8)"
            
            switch ($choice) {
                "1" { $this.GetSystemInfo() }
                "2" { $this.MonitorPerformance() }
                "3" { $this.MonitorMultipleSystems() }
                "4" { $this.ManageServices() }
                "5" { $this.GenerateReports() }
                "6" { $this.ManageConfiguration() }
                "7" { $this.ViewLogs() }
                "8" { 
                    $this.Logger.Info("Exiting System Administration Toolkit")
                    break 
                }
                default { 
                    Write-Host "Invalid choice. Please try again." -ForegroundColor Red
                    Start-Sleep -Seconds 2
                }
            }
            
            if ($choice -ne "8") {
                Write-Host "`nPress Enter to continue..."
                Read-Host
            }
        }
    }
    
    [void]GetSystemInfo() {
        $computerName = Read-Host "Enter computer name (default: localhost)"
        if ([string]::IsNullOrEmpty($computerName)) {
            $computerName = "localhost"
        }
        
        $this.Logger.Info("Getting system information for: $computerName")
        $systemInfo = $this.Monitor.GetSystemInfo($computerName)
        
        if ($systemInfo.Status -eq "Online") {
            Write-Host "`n=== System Information ===" -ForegroundColor Green
            Write-Host "Computer: $($systemInfo.ComputerName)" -ForegroundColor Cyan
            Write-Host "OS: $($systemInfo.OS)" -ForegroundColor Cyan
            Write-Host "Version: $($systemInfo.Version)" -ForegroundColor Cyan
            Write-Host "Manufacturer: $($systemInfo.Manufacturer)" -ForegroundColor Cyan
            Write-Host "Model: $($systemInfo.Model)" -ForegroundColor Cyan
            Write-Host "Total Memory: $($systemInfo.TotalMemory) GB" -ForegroundColor Cyan
            Write-Host "Processors: $($systemInfo.Processors)" -ForegroundColor Cyan
            Write-Host "Architecture: $($systemInfo.Architecture)" -ForegroundColor Cyan
            Write-Host "Last Boot: $($systemInfo.LastBootTime)" -ForegroundColor Cyan
        }
        else {
            Write-Host "`nError: $($systemInfo.Error)" -ForegroundColor Red
        }
    }
    
    [void]MonitorPerformance() {
        $computerName = Read-Host "Enter computer name (default: localhost)"
        if ([string]::IsNullOrEmpty($computerName)) {
            $computerName = "localhost"
        }
        
        $this.Logger.Info("Monitoring performance for: $computerName")
        $metrics = $this.Monitor.GetPerformanceMetrics($computerName)
        
        if ($metrics.Status -eq "Healthy" -or $metrics.Status -eq "Warning") {
            Write-Host "`n=== Performance Metrics ===" -ForegroundColor Green
            Write-Host "Computer: $($metrics.ComputerName)" -ForegroundColor Cyan
            Write-Host "CPU Usage: $($metrics.CPUUsage)%" -ForegroundColor $(if ($metrics.CPUUsage -lt 80) { "Green" } elseif ($metrics.CPUUsage -lt 90) { "Yellow" } else { "Red" })
            Write-Host "Memory Usage: $($metrics.MemoryUsage)%" -ForegroundColor $(if ($metrics.MemoryUsage -lt 80) { "Green" } elseif ($metrics.MemoryUsage -lt 90) { "Yellow" } else { "Red" })
            Write-Host "Disk Usage: $($metrics.DiskUsage)%" -ForegroundColor $(if ($metrics.DiskUsage -lt 80) { "Green" } elseif ($metrics.DiskUsage -lt 90) { "Yellow" } else { "Red" })
            Write-Host "Total Memory: $($metrics.TotalMemoryGB) GB" -ForegroundColor Cyan
            Write-Host "Free Memory: $($metrics.FreeMemoryGB) GB" -ForegroundColor Cyan
            Write-Host "Disk Size: $($metrics.DiskSizeGB) GB" -ForegroundColor Cyan
            Write-Host "Free Disk: $($metrics.FreeDiskGB) GB" -ForegroundColor Cyan
        }
        else {
            Write-Host "`nError: $($metrics.Error)" -ForegroundColor Red
        }
    }
    
    [void]MonitorMultipleSystems() {
        $computersInput = Read-Host "Enter computer names (comma-separated)"
        if ([string]::IsNullOrEmpty($computersInput)) {
            Write-Host "No computers specified. Using localhost." -ForegroundColor Yellow
            $computers = @("localhost")
        }
        else {
            $computers = $computersInput -split "," | ForEach-Object { $_.Trim() }
        }
        
        $this.Logger.Info("Monitoring $($computers.Count) systems")
        $results = $this.Monitor.MonitorMultipleSystems($computers)
        
        Write-Host "`n=== Multiple Systems Monitor ===" -ForegroundColor Green
        foreach ($result in $results) {
            $color = switch ($result.Status) {
                "Online" { "Green" }
                "Offline" { "Red" }
                "Warning" { "Yellow" }
                default { "Red" }
            }
            
            Write-Host "$($result.ComputerName): $($result.Status)" -ForegroundColor $color
            
            if ($result.Status -eq "Online" -or $result.Status -eq "Warning") {
                Write-Host "  CPU: $($result.CPUUsage)% | Memory: $($result.MemoryUsage)% | Disk: $($result.DiskUsage)%" -ForegroundColor Gray
            }
        }
        
        # Generate report
        $this.ReportGenerator.GenerateSystemReport($results)
    }
    
    [void]ManageServices() {
        Write-Host "`n=== Service Management ===" -ForegroundColor Green
        Write-Host "1. List Services" -ForegroundColor Cyan
        Write-Host "2. Start Service" -ForegroundColor Cyan
        Write-Host "3. Stop Service" -ForegroundColor Cyan
        Write-Host "4. Get Critical Services" -ForegroundColor Cyan
        Write-Host "5. Back to Main Menu" -ForegroundColor Red
        
        $choice = Read-Host "Enter your choice (1-5)"
        
        switch ($choice) {
            "1" { $this.ListServices() }
            "2" { $this.StartService() }
            "3" { $this.StopService() }
            "4" { $this.GetCriticalServices() }
            "5" { return }
            default { Write-Host "Invalid choice." -ForegroundColor Red }
        }
    }
    
    [void]ListServices() {
        $computerName = Read-Host "Enter computer name (default: localhost)"
        if ([string]::IsNullOrEmpty($computerName)) {
            $computerName = "localhost"
        }
        
        $serviceName = Read-Host "Enter service name (leave empty for all services)"
        
        $this.Logger.Info("Listing services for: $computerName"
        if ($serviceName) {
            $this.Logger.Info("Filtering by service: $serviceName")
        }
        
        $services = $this.ServiceManager.GetServices($computerName, $serviceName)
        
        if ($services.Count -gt 0) {
            Write-Host "`n=== Services ===" -ForegroundColor Green
            $services | Format-Table Name, DisplayName, Status, StartType -AutoSize
        }
        else {
            Write-Host "No services found." -ForegroundColor Yellow
        }
    }
    
    [void]StartService() {
        $serviceName = Read-Host "Enter service name"
        $computerName = Read-Host "Enter computer name (default: localhost)"
        if ([string]::IsNullOrEmpty($computerName)) {
            $computerName = "localhost"
        }
        
        $this.Logger.Info("Starting service: $serviceName on $computerName")
        $success = $this.ServiceManager.StartService($serviceName, $computerName)
        
        if ($success) {
            Write-Host "Service started successfully." -ForegroundColor Green
        } else {
            Write-Host "Failed to start service." -ForegroundColor Red
        }
    }
    
    [void]StopService() {
        $serviceName = Read-Host "Enter service name"
        $computerName = Read-Host "Enter computer name (default: localhost)"
        if ([string]::IsNullOrEmpty($computerName)) {
            $computerName = "localhost"
        }
        
        $this.Logger.Info("Stopping service: $serviceName on $computerName")
        $success = $this.ServiceManager.StopService($serviceName, $computerName)
        
        if ($success) {
            Write-Host "Service stopped successfully." -ForegroundColor Green
        } else {
            Write-Host "Failed to stop service." -ForegroundColor Red
        }
    }
    
    [void]GetCriticalServices() {
        $computerName = Read-Host "Enter computer name (default: localhost)"
        if ([string]::IsNullOrEmpty($computerName)) {
            $computerName = "localhost"
        }
        
        $this.Logger.Info("Getting critical services for: $computerName")
        $services = $this.ServiceManager.GetCriticalServices($computerName)
        
        if ($services.Count -gt 0) {
            Write-Host "`n=== Critical Services ===" -ForegroundColor Green
            $services | Format-Table Name, DisplayName, Status, StartType -AutoSize
        }
        else {
            Write-Host "No critical services found." -ForegroundColor Yellow
        }
    }
    
    [void]GenerateReports() {
        Write-Host "`n=== Report Generation ===" -ForegroundColor Green
        Write-Host "1. Generate System Report" -ForegroundColor Cyan
        Write-Host "2. Generate Service Report" -ForegroundColor Cyan
        Write-Host "3. Back to Main Menu" -ForegroundColor Red
        
        $choice = Read-Host "Enter your choice (1-3)"
        
        switch ($choice) {
            "1" { $this.GenerateSystemReport() }
            "2" { $this.GenerateServiceReport() }
            "3" { return }
            default { Write-Host "Invalid choice." -ForegroundColor Red }
        }
    }
    
    [void]GenerateSystemReport() {
        $computersInput = Read-Host "Enter computer names (comma-separated, default: localhost)"
        if ([string]::IsNullOrEmpty($computersInput)) {
            $computers = @("localhost")
        }
        else {
            $computers = $computersInput -split "," | ForEach-Object { $_.Trim() }
        }
        
        $this.Logger.Info("Generating system report for $($computers.Count) systems")
        $results = $this.Monitor.MonitorMultipleSystems($computers)
        $this.ReportGenerator.GenerateSystemReport($results)
    }
    
    [void]GenerateServiceReport() {
        $computerName = Read-Host "Enter computer name (default: localhost)"
        if ([string]::IsNullOrEmpty($computerName)) {
            $computerName = "localhost"
        }
        
        $this.Logger.Info("Generating service report for: $computerName")
        $services = $this.ServiceManager.GetServices($computerName)
        $this.ReportGenerator.GenerateServiceReport($services)
    }
    
    [void]ManageConfiguration() {
        Write-Host "`n=== Configuration Management ===" -ForegroundColor Green
        Write-Host "1. View Current Settings" -ForegroundColor Cyan
        Write-Host "2. Update Setting" -ForegroundColor Cyan
        Write-Host "3. Reset to Defaults" -ForegroundColor Cyan
        Write-Host "4. Back to Main Menu" -ForegroundColor Red
        
        $choice = Read-Host "Enter your choice (1-4)"
        
        switch ($choice) {
            "1" { $this.ViewConfiguration() }
            "2" { $this.UpdateConfiguration() }
            "3" { $this.ResetConfiguration() }
            "4" { return }
            default { Write-Host "Invalid choice." -ForegroundColor Red }
        }
    }
    
    [void]ViewConfiguration() {
        Write-Host "`n=== Current Configuration ===" -ForegroundColor Green
        
        $sections = $this.Config.Settings.Keys
        foreach ($section in $sections) {
            Write-Host "`n[$section]" -ForegroundColor Yellow
            foreach ($key in $this.Config.Settings[$section].Keys) {
                $value = $this.Config.Settings[$section][$key]
                Write-Host "  $key = $value" -ForegroundColor Gray
            }
        }
    }
    
    [void]UpdateConfiguration() {
        Write-Host "`nAvailable sections:" -ForegroundColor Green
        $sections = $this.Config.Settings.Keys
        for ($i = 0; $i -lt $sections.Count; $i++) {
            Write-Host "$($i + 1). $($sections[$i])" -ForegroundColor Cyan
        }
        
        $sectionChoice = Read-Host "Select section (1-$($sections.Count))"
        if ($sectionChoice -match '^\d+$' -and [int]$sectionChoice -ge 1 -and [int]$sectionChoice -le $sections.Count) {
            $section = $sections[[int]$sectionChoice - 1]
            
            Write-Host "`nAvailable keys in [$section]:" -ForegroundColor Green
            $keys = $this.Config.Settings[$section].Keys
            for ($i = 0; $i -lt $keys.Count; $i++) {
                $value = $this.Config.Settings[$section][$keys[$i]]
                Write-Host "$($i + 1). $($keys[$i]) = $value" -ForegroundColor Cyan
            }
            
            $keyChoice = Read-Host "Select key to update (1-$($keys.Count))"
            if ($keyChoice -match '^\d+$' -and [int]$keyChoice -ge 1 -and [int]$keyChoice -le $keys.Count) {
                $key = $keys[[int]$keyChoice - 1]
                $newValue = Read-Host "Enter new value for $key"
                
                $this.Config.SetSetting($section, $key, $newValue)
                Write-Host "Setting updated successfully." -ForegroundColor Green
            }
            else {
                Write-Host "Invalid key selection." -ForegroundColor Red
            }
        }
        else {
            Write-Host "Invalid section selection." -ForegroundColor Red
        }
    }
    
    [void]ResetConfiguration() {
        $confirm = Read-Host "Are you sure you want to reset configuration to defaults? (y/N)"
        if ($confirm -eq "y" -or $confirm -eq "Y") {
            $this.Config.Settings = $this.Config.GetDefaultSettings()
            $this.Config.SaveConfiguration()
            Write-Host "Configuration reset to defaults." -ForegroundColor Green
        }
        else {
            Write-Host "Operation cancelled." -ForegroundColor Yellow
        }
    }
    
    [void]ViewLogs() {
        $logPath = $this.Config.GetSetting("General", "LogPath")
        
        if (Test-Path $logPath) {
            Write-Host "`n=== Recent Log Entries ===" -ForegroundColor Green
            $logs = Get-Content $logPath -Tail 20
            foreach ($log in $logs) {
                if ($log -match "\[INFO\]") {
                    Write-Host $log -ForegroundColor Green
                }
                elseif ($log -match "\[WARNING\]") {
                    Write-Host $log -ForegroundColor Yellow
                }
                elseif ($log -match "\[ERROR\]") {
                    Write-Host $log -ForegroundColor Red
                }
                elseif ($log -match "\[CRITICAL\]") {
                    Write-Host $log -ForegroundColor Magenta
                }
                else {
                    Write-Host $log -ForegroundColor Gray
                }
            }
        }
        else {
            Write-Host "No log file found at: $logPath" -ForegroundColor Yellow
        }
    }
}
}

# Main execution
try {
    # Check if running with elevation
    $isAdmin = ([System.Security.Principal.WindowsPrincipal] [System.Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([System.Security.Principal.WindowsBuiltInRole]::Administrator)
    
    if (-not $isAdmin) {
        Write-Host "Warning: Some features may require administrator privileges." -ForegroundColor Yellow
        $continue = Read-Host "Continue anyway? (y/N)"
        if ($continue -ne "y" -and $continue -ne "Y") {
            Write-Host "Exiting. Please run as administrator for full functionality." -ForegroundColor Red
            exit
        }
    }
    
    # Create and run toolkit
    $toolkit = [SystemAdministrationToolkit]::new()
    $toolkit.ShowMenu()
}
catch {
    Write-Host "Fatal error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Stack trace: $($_.ScriptStackTrace)" -ForegroundColor Gray
    exit 1
}

Write-Host "Thank you for using the System Administration Toolkit!" -ForegroundColor Green
