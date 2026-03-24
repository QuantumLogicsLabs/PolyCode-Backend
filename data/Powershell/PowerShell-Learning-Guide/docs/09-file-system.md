# 09 - File System Operations

## 🎯 Learning Objectives

After completing this section, you will understand:
- How to navigate and manage directories
- File creation, reading, and writing operations
- File and directory permissions
- File system monitoring and events
- Path manipulation and validation
- Best practices for file system operations

## 📁 Directory Operations

PowerShell provides comprehensive cmdlets for managing directories.

### Navigating Directories

```powershell
# Get current location
Get-Location
$PWD  # Automatic variable

# Change location
Set-Location "C:\Users"
cd "C:\Users"        # Alias
sl "C:\Users"        # Alias

# Push and pop locations (stack-based navigation)
Push-Location "C:\Windows"
Push-Location "C:\Windows\System32"
Pop-Location  # Returns to C:\Windows
Pop-Location  # Returns to original location

# Location stack
Get-Location -Stack
```

### Creating and Managing Directories

```powershell
# Create directory
New-Item -Path "C:\temp\test" -ItemType Directory
mkdir "C:\temp\test"  # Alias

# Create nested directories (creates parent if needed)
New-Item -Path "C:\temp\level1\level2\level3" -ItemType Directory -Force

# Remove directory (empty)
Remove-Item "C:\temp\test"

# Remove directory with contents
Remove-Item "C:\temp\test" -Recurse -Force

# Rename directory
Rename-Item "C:\temp\oldname" "newname"

# Move directory
Move-Item "C:\temp\source" "C:\temp\destination"

# Copy directory
Copy-Item "C:\temp\source" "C:\temp\destination" -Recurse
```

### Directory Information

```powershell
# Get directory contents
Get-ChildItem "C:\temp"
ls "C:\temp"         # Alias
dir "C:\temp"        # Alias

# Get directory contents with options
Get-ChildItem -Path "C:\temp" -Recurse -File
Get-ChildItem -Path "C:\temp" -Recurse -Directory
Get-ChildItem -Path "C:\temp" -Hidden
Get-ChildItem -Path "C:\temp" -System
Get-ChildItem -Path "C:\temp" -Force

# Get directory information
$dirInfo = Get-Item "C:\temp"
$dirInfo.Name
$dirInfo.FullName
$dirInfo.Parent
$dirInfo.Root
$dirInfo.CreationTime
$dirInfo.LastAccessTime
$dirInfo.LastWriteTime

# Test directory existence
if (Test-Path "C:\temp") {
    Write-Host "Directory exists"
}

# Get directory size
function Get-DirectorySize {
    param([string]$Path)
    
    $files = Get-ChildItem -Path $Path -Recurse -File -ErrorAction SilentlyContinue
    $totalSize = ($files | Measure-Object -Property Length -Sum).Sum
    
    return @{
        Path = $Path
        SizeBytes = $totalSize
        SizeKB = [math]::Round($totalSize / 1KB, 2)
        SizeMB = [math]::Round($totalSize / 1MB, 2)
        SizeGB = [math]::Round($totalSize / 1GB, 2)
        FileCount = $files.Count
    }
}

$size = Get-DirectorySize -Path "C:\temp"
Write-Host "Directory size: $($size.SizeMB) MB ($($size.FileCount) files)"
```

## 📄 File Operations

PowerShell provides extensive capabilities for file manipulation.

### Creating and Writing Files

```powershell
# Create empty file
New-Item -Path "C:\temp\test.txt" -ItemType File

# Write content to file
Set-Content -Path "C:\temp\test.txt" -Value "Hello, World!"

# Append content to file
Add-Content -Path "C:\temp\test.txt" -Value "This is a new line."

# Write multiple lines
$lines = @(
    "Line 1",
    "Line 2", 
    "Line 3"
)
Set-Content -Path "C:\temp\multiline.txt" -Value $lines

# Write with encoding
Set-Content -Path "C:\temp\unicode.txt" -Value "Unicode text" -Encoding UTF8

# Create file with here-string
$hereString = @"
This is a here-string
It can span multiple lines
And preserve formatting
"@
Set-Content -Path "C:\temp\here.txt" -Value $hereString

# Using Out-File
Get-Process | Out-File -Path "C:\temp\processes.txt"
Get-Process | Out-File -Path "C:\temp\processes.csv" -Encoding UTF8
```

### Reading Files

```powershell
# Read entire file
$content = Get-Content -Path "C:\temp\test.txt"

# Read file as single string
$content = Get-Content -Path "C:\temp\test.txt" -Raw

# Read specific number of lines
$firstLines = Get-Content -Path "C:\temp\test.txt" -TotalCount 5

# Read file with encoding
$content = Get-Content -Path "C:\temp\unicode.txt" -Encoding UTF8

# Read file line by line
Get-Content -Path "C:\temp\test.txt" | ForEach-Object {
    Write-Host "Line: $_"
}

# Read file into array (each line as element)
$lines = Get-Content -Path "C:\temp\test.txt"
$firstLine = $lines[0]
$lastLine = $lines[-1]

# Stream large files (memory efficient)
$reader = [System.IO.File]::OpenText("C:\temp\largefile.txt")
while ($null -ne ($line = $reader.ReadLine())) {
    # Process each line
    Write-Host $line
}
$reader.Close()

# Read binary files
$bytes = Get-Content -Path "C:\temp\binary.bin" -Encoding Byte
```

### File Manipulation

```powershell
# Copy file
Copy-Item -Path "C:\temp\source.txt" -Destination "C:\temp\copy.txt"
Copy-Item -Path "*.txt" -Destination "C:\backup\" -Force

# Move file
Move-Item -Path "C:\temp\old.txt" -Destination "C:\temp\new.txt"

# Rename file
Rename-Item -Path "C:\temp\oldname.txt" -NewName "newname.txt"

# Delete file
Remove-Item -Path "C:\temp\test.txt"
Remove-Item -Path "*.tmp" -Force

# Get file information
$fileInfo = Get-Item -Path "C:\temp\test.txt"
$fileInfo.Name
$fileInfo.FullName
$fileInfo.Length
$fileInfo.Extension
$fileInfo.BaseName
$fileInfo.DirectoryName
$fileInfo.CreationTime
$fileInfo.LastAccessTime
$fileInfo.LastWriteTime

# Update file timestamps
$fileInfo.CreationTime = "2023-01-01 10:00:00"
$fileInfo.LastAccessTime = Get-Date
$fileInfo.LastWriteTime = Get-Date

# Compare files
Compare-Object -ReferenceObject (Get-Content "file1.txt") -DifferenceObject (Get-Content "file2.txt")

# Get file hash
$hash = Get-FileHash -Path "C:\temp\test.txt" -Algorithm SHA256
Write-Host "File hash: $($hash.Hash)"
```

## 🔐 File and Directory Permissions

PowerShell can manage file system permissions on Windows.

### Viewing Permissions

```powershell
# Get ACL (Access Control List)
$acl = Get-Acl -Path "C:\temp\test.txt"

# Show access rules
foreach ($access in $acl.Access) {
    Write-Host "Identity: $($access.IdentityReference)"
    Write-Host "Rights: $($access.FileSystemRights)"
    Write-Host "Type: $($access.AccessControlType)"
    Write-Host "---"
}

# Get owner
$owner = $acl.Owner
Write-Host "Owner: $owner"
```

### Modifying Permissions

```powershell
# Create new access rule
$account = "Everyone"
$rights = "ReadAndExecute"
$type = "Allow"
$accessRule = New-Object System.Security.AccessControl.FileSystemAccessRule($account, $rights, $type)

# Get current ACL and add rule
$acl = Get-Acl -Path "C:\temp\test.txt"
$acl.SetAccessRule($accessRule)
Set-Acl -Path "C:\temp\test.txt" -AclObject $acl

# Remove access rule
$acl = Get-Acl -Path "C:\temp\test.txt"
$accessRuleToRemove = $acl.Access | Where-Object { $_.IdentityReference -eq "Everyone" }
if ($accessRuleToRemove) {
    $acl.RemoveAccessRule($accessRuleToRemove) | Out-Null
    Set-Acl -Path "C:\temp\test.txt" -AclObject $acl
}

# Set owner
$acl = Get-Acl -Path "C:\temp\test.txt"
$account = New-Object System.Security.Principal.NTAccount("Administrators")
$acl.SetOwner($account)
Set-Acl -Path "C:\temp\test.txt" -AclObject $acl
```

## 📊 File System Monitoring

PowerShell can monitor file system changes using .NET classes.

### FileSystemWatcher

```powershell
# Create file system watcher
$watcher = New-Object System.IO.FileSystemWatcher
$watcher.Path = "C:\temp"
$watcher.Filter = "*.txt"
$watcher.IncludeSubdirectories = $true
$watcher.EnableRaisingEvents = $true

# Define event handlers
$onCreated = Register-ObjectEvent -InputObject $watcher -EventName Created -Action {
    Write-Host "File created: $($Event.SourceEventArgs.FullPath)" -ForegroundColor Green
}

$onChanged = Register-ObjectEvent -InputObject $watcher -EventName Changed -Action {
    Write-Host "File changed: $($Event.SourceEventArgs.FullPath)" -ForegroundColor Yellow
}

$onDeleted = Register-ObjectEvent -InputObject $watcher -EventName Deleted -Action {
    Write-Host "File deleted: $($Event.SourceEventArgs.FullPath)" -ForegroundColor Red
}

$onRenamed = Register-ObjectEvent -InputObject $watcher -EventName Renamed -Action {
    Write-Host "File renamed from $($Event.SourceEventArgs.OldFullPath) to $($Event.SourceEventArgs.FullPath)" -ForegroundColor Cyan
}

Write-Host "Monitoring C:\temp for *.txt changes. Press Ctrl+C to stop."

# Keep script running
try {
    while ($true) { Start-Sleep -Seconds 1 }
}
finally {
    # Clean up event handlers
    Unregister-Event -SourceIdentifier $onCreated.Name
    Unregister-Event -SourceIdentifier $onChanged.Name
    Unregister-Event -SourceIdentifier $onDeleted.Name
    Unregister-Event -SourceIdentifier $onRenamed.Name
    $watcher.Dispose()
}
```

## 🛣️ Path Manipulation

Working with file paths is common in PowerShell scripts.

### Path Operations

```powershell
# Join paths
$path = Join-Path -Path "C:\temp" -ChildPath "test.txt"
$path = Join-Path -Path "C:\temp" -ChildPath "logs" -AdditionalChildPath "app.log"

# Split path
$split = Split-Path -Path "C:\temp\test.txt" -Leaf      # test.txt
$split = Split-Path -Path "C:\temp\test.txt" -Parent    # C:\temp
$split = Split-Path -Path "C:\temp\test.txt" -Qualifier # C:
$split = Split-Path -Path "C:\temp\test.txt" -NoQualifier # temp\test.txt

# Get directory name
$dir = [System.IO.Path]::GetDirectoryName("C:\temp\test.txt")  # C:\temp

# Get file name
$file = [System.IO.Path]::GetFileName("C:\temp\test.txt")        # test.txt

# Get file name without extension
$baseName = [System.IO.Path]::GetFileNameWithoutExtension("C:\temp\test.txt")  # test

# Get extension
$ext = [System.IO.Path]::GetExtension("C:\temp\test.txt")       # .txt

# Combine paths
$combined = [System.IO.Path]::Combine("C:", "temp", "test.txt")   # C:\temp\test.txt

# Get absolute path
$absolute = [System.IO.Path]::GetFullPath("..\test.txt")

# Check if path is rooted
$isRooted = [System.IO.Path]::IsPathRooted("C:\temp\test.txt")   # $true
$isRooted = [System.IO.Path]::IsPathRooted("temp\test.txt")      # $false

# Get temp path
$tempPath = [System.IO.Path]::GetTempPath()

# Get random file name
$randomFile = [System.IO.Path]::GetRandomFileName()
```

### Path Validation

```powershell
# Test if path exists
if (Test-Path "C:\temp\test.txt") {
    Write-Host "File exists"
}

# Test if path is file or directory
if (Test-Path "C:\temp\test.txt" -PathType Leaf) {
    Write-Host "It's a file"
}

if (Test-Path "C:\temp" -PathType Container) {
    Write-Host "It's a directory"
}

# Validate path format
function Test-ValidPath {
    param([string]$Path)
    
    try {
        $null = [System.IO.Path]::GetFullPath($Path)
        return $true
    }
    catch {
        return $false
    }
}

# Check for invalid characters
function Test-InvalidPathChars {
    param([string]$Path)
    
    $invalidChars = [System.IO.Path]::GetInvalidPathChars()
    foreach ($char in $invalidChars) {
        if ($Path.Contains($char)) {
            Write-Host "Invalid character found: $char"
            return $false
        }
    }
    return $true
}
```

## 🚀 Practical Examples

### Example 1: File Backup Utility

```powershell
class FileBackupUtility {
    [string]$SourcePath
    [string]$DestinationPath
    [hashtable]$Settings
    
    FileBackupUtility([string]$source, [string]$destination) {
        $this.SourcePath = $source
        $this.DestinationPath = $destination
        $this.Settings = @{
            IncludeSubdirectories = $true
            ExcludePatterns = @("*.tmp", "*.log", "*.bak")
            CompressBackups = $false
            RetentionDays = 30
            VerifyBackups = $true
            CreateTimestampFolders = $true
        }
    }
    
    [void]SetSetting([string]$key, [object]$value) {
        $this.Settings[$key] = $value
    }
    
    [void]CreateBackup() {
        Write-Host "Starting backup from $($this.SourcePath) to $($this.DestinationPath)"
        
        # Create destination if it doesn't exist
        if (-not (Test-Path $this.DestinationPath)) {
            New-Item -Path $this.DestinationPath -ItemType Directory -Force | Out-Null
            Write-Host "Created destination directory"
        }
        
        # Create timestamp folder if enabled
        $backupPath = $this.DestinationPath
        if ($this.Settings.CreateTimestampFolders) {
            $timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
            $backupPath = Join-Path $this.DestinationPath $timestamp
            New-Item -Path $backupPath -ItemType Directory -Force | Out-Null
            Write-Host "Created timestamped backup folder: $timestamp"
        }
        
        # Get files to backup
        $files = $this.GetFilesToBackup()
        Write-Host "Found $($files.Count) files to backup"
        
        $backupStats = @{
            TotalFiles = $files.Count
            BackedUpFiles = 0
            SkippedFiles = 0
            FailedFiles = 0
            TotalSize = 0
            BackupSize = 0
            Errors = @()
        }
        
        foreach ($file in $files) {
            try {
                $relativePath = $file.FullName.Replace($this.SourcePath, "").TrimStart("\")
                $destinationFile = Join-Path $backupPath $relativePath
                
                # Create subdirectories if needed
                $destinationDir = Split-Path $destinationFile -Parent
                if (-not (Test-Path $destinationDir)) {
                    New-Item -Path $destinationDir -ItemType Directory -Force | Out-Null
                }
                
                # Check if file needs backup
                $needsBackup = $true
                if (Test-Path $destinationFile) {
                    $sourceFile = Get-Item $file.FullName
                    $destFile = Get-Item $destinationFile
                    $needsBackup = $sourceFile.LastWriteTime -gt $destFile.LastWriteTime
                }
                
                if ($needsBackup) {
                    Copy-Item -Path $file.FullName -Destination $destinationFile -Force
                    
                    $backupStats.BackedUpFiles++
                    $backupStats.TotalSize += $file.Length
                    $backupStats.BackupSize += (Get-Item $destinationFile).Length
                    
                    if ($backupStats.BackedUpFiles % 10 -eq 0) {
                        Write-Host "Backed up $($backupStats.BackedUpFiles) files..."
                    }
                }
                else {
                    $backupStats.SkippedFiles++
                }
            }
            catch {
                $errorInfo = "Error backing up $($file.FullName): $($_.Exception.Message)"
                $backupStats.Errors += $errorInfo
                $backupStats.FailedFiles++
                Write-Warning $errorInfo
            }
        }
        
        # Compress backup if enabled
        if ($this.Settings.CompressBackups) {
            $this.CompressBackup($backupPath)
        }
        
        # Verify backup if enabled
        if ($this.Settings.VerifyBackups) {
            $this.VerifyBackup($backupPath, $files.Count)
        }
        
        # Clean old backups
        $this.CleanupOldBackups()
        
        # Display summary
        $this.DisplayBackupSummary($backupStats)
    }
    
    [System.IO.FileInfo[]]GetFilesToBackup() {
        $getChildItemParams = @{
            Path = $this.SourcePath
            File = $true
            Recurse = $this.Settings.IncludeSubdirectories
            ErrorAction = "SilentlyContinue"
        }
        
        $allFiles = Get-ChildItem @getChildChildItemParams
        
        # Filter out excluded patterns
        $filteredFiles = $allFiles | Where-Object {
            $file = $_
            $exclude = $false
            
            foreach ($pattern in $this.Settings.ExcludePatterns) {
                if ($file.Name -like $pattern) {
                    $exclude = $true
                    break
                }
            }
            
            -not $exclude
        }
        
        return $filteredFiles
    }
    
    [void]CompressBackup([string]$backupPath) {
        Write-Host "Compressing backup..."
        $zipPath = "$backupPath.zip"
        
        try {
            Compress-Archive -Path $backupPath -DestinationPath $zipPath -Force
            Remove-Item -Path $backupPath -Recurse -Force
            Write-Host "Backup compressed to: $zipPath"
        }
        catch {
            Write-Warning "Failed to compress backup: $($_.Exception.Message)"
        }
    }
    
    [void]VerifyBackup([string]$backupPath, [int]$expectedFileCount) {
        Write-Host "Verifying backup..."
        
        try {
            $backupFiles = Get-ChildItem -Path $backupPath -Recurse -File -ErrorAction SilentlyContinue
            $actualFileCount = $backupFiles.Count
            
            if ($actualFileCount -eq $expectedFileCount) {
                Write-Host "Backup verification successful: $actualFileCount files" -ForegroundColor Green
            }
            else {
                Write-Warning "Backup verification failed: expected $expectedFileCount files, found $actualFileCount"
            }
        }
        catch {
            Write-Warning "Backup verification error: $($_.Exception.Message)"
        }
    }
    
    [void]CleanupOldBackups() {
        if ($this.Settings.RetentionDays -le 0) {
            return
        }
        
        Write-Host "Cleaning up old backups (retention: $($this.Settings.RetentionDays) days)..."
        
        $cutoffDate = (Get-Date).AddDays(-$this.Settings.RetentionDays)
        $oldBackups = Get-ChildItem -Path $this.DestinationPath -Directory | Where-Object { 
            $_.CreationTime -lt $cutoffDate 
        }
        
        foreach ($oldBackup in $oldBackups) {
            try {
                Remove-Item -Path $oldBackup.FullName -Recurse -Force
                Write-Host "Removed old backup: $($oldBackup.Name)"
            }
            catch {
                Write-Warning "Failed to remove old backup $($oldBackup.Name): $($_.Exception.Message)"
            }
        }
    }
    
    [void]DisplayBackupSummary([hashtable]$stats) {
        Write-Host "`n=== Backup Summary ==="
        Write-Host "Total files: $($stats.TotalFiles)"
        Write-Host "Files backed up: $($stats.BackedUpFiles)"
        Write-Host "Files skipped: $($stats.SkippedFiles)"
        Write-Host "Files failed: $($stats.FailedFiles)"
        Write-Host "Total size: $([math]::Round($stats.TotalSize / 1MB, 2)) MB"
        Write-Host "Backup size: $([math]::Round($stats.BackupSize / 1MB, 2)) MB"
        
        if ($stats.Errors.Count -gt 0) {
            Write-Host "`nErrors encountered:"
            $stats.Errors | ForEach-Object { Write-Host "  - $_" -ForegroundColor Red }
        }
        
        if ($stats.FailedFiles -eq 0) {
            Write-Host "Backup completed successfully!" -ForegroundColor Green
        }
        else {
            Write-Host "Backup completed with $($stats.FailedFiles) errors." -ForegroundColor Yellow
        }
    }
}

# Usage example
$backup = [FileBackupUtility]::new("C:\data", "C:\backup")
$backup.SetSetting("ExcludePatterns", @("*.tmp", "*.log", "*.bak", "*.cache"))
$backup.SetSetting("CompressBackups", $true)
$backup.SetSetting("RetentionDays", 7)
$backup.CreateBackup()
```

### Example 2: Log File Analyzer

```powershell
class LogAnalyzer {
    [string]$LogPath
    [hashtable]$AnalysisResults
    
    LogAnalyzer([string]$logPath) {
        $this.LogPath = $logPath
        $this.AnalysisResults = @{}
    }
    
    [void]AnalyzeLog() {
        Write-Host "Analyzing log file: $($this.LogPath)"
        
        if (-not (Test-Path $this.LogPath)) {
            throw "Log file not found: $($this.LogPath)"
        }
        
        # Initialize counters
        $this.AnalysisResults = @{
            TotalLines = 0
            ErrorCount = 0
            WarningCount = 0
            InfoCount = 0
            DebugCount = 0
            DateRange = @{}
            TopErrors = @{}
            TopWarnings = @{}
            HourlyDistribution = @{}
            ErrorPatterns = @{}
        }
        
        # Read and analyze log file
        $reader = [System.IO.File]::OpenText($this.LogPath)
        $lineNumber = 0
        
        while ($null -ne ($line = $reader.ReadLine())) {
            $lineNumber++
            $this.AnalysisResults.TotalLines++
            
            # Parse log line (assuming format: [TIMESTAMP] [LEVEL] MESSAGE)
            if ($line -match '^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \[(\w+)\] (.+)$') {
                $timestamp = [DateTime]$matches[1]
                $level = $matches[2]
                $message = $matches[3]
                
                # Update date range
                if (-not $this.AnalysisResults.DateRange.ContainsKey("Start") -or 
                    $timestamp -lt $this.AnalysisResults.DateRange.Start) {
                    $this.AnalysisResults.DateRange.Start = $timestamp
                }
                
                if (-not $this.AnalysisResults.DateRange.ContainsKey("End") -or 
                    $timestamp -gt $this.AnalysisResults.DateRange.End) {
                    $this.AnalysisResults.DateRange.End = $timestamp
                }
                
                # Count by level
                switch ($level) {
                    "ERROR" { 
                        $this.AnalysisResults.ErrorCount++
                        $this.TrackError($message, $timestamp)
                    }
                    "WARN" { 
                        $this.AnalysisResults.WarningCount++
                        $this.TrackWarning($message, $timestamp)
                    }
                    "INFO" { $this.AnalysisResults.InfoCount++ }
                    "DEBUG" { $this.AnalysisResults.DebugCount++ }
                }
                
                # Track hourly distribution
                $hour = $timestamp.Hour
                if (-not $this.AnalysisResults.HourlyDistribution.ContainsKey($hour)) {
                    $this.AnalysisResults.HourlyDistribution[$hour] = 0
                }
                $this.AnalysisResults.HourlyDistribution[$hour]++
            }
        }
        
        $reader.Close()
        
        # Sort top errors and warnings
        $this.AnalysisResults.TopErrors = $this.SortHashtable($this.AnalysisResults.TopErrors)
        $this.AnalysisResults.TopWarnings = $this.SortHashtable($this.AnalysisResults.TopWarnings)
        
        Write-Host "Log analysis completed: $($this.AnalysisResults.TotalLines) lines processed"
    }
    
    [void]TrackError([string]$message, [DateTime]$timestamp) {
        # Track error messages
        if (-not $this.AnalysisResults.TopErrors.ContainsKey($message)) {
            $this.AnalysisResults.TopErrors[$message] = @{
                Count = 0
                FirstOccurrence = $timestamp
                LastOccurrence = $timestamp
            }
        }
        
        $this.AnalysisResults.TopErrors[$message].Count++
        $this.AnalysisResults.TopErrors[$message].LastOccurrence = $timestamp
        
        # Track error patterns
        $pattern = $this.ExtractPattern($message)
        if ($pattern) {
            if (-not $this.AnalysisResults.ErrorPatterns.ContainsKey($pattern)) {
                $this.AnalysisResults.ErrorPatterns[$pattern] = 0
            }
            $this.AnalysisResults.ErrorPatterns[$pattern]++
        }
    }
    
    [void]TrackWarning([string]$message, [DateTime]$timestamp) {
        # Track warning messages
        if (-not $this.AnalysisResults.TopWarnings.ContainsKey($message)) {
            $this.AnalysisResults.TopWarnings[$message] = @{
                Count = 0
                FirstOccurrence = $timestamp
                LastOccurrence = $timestamp
            }
        }
        
        $this.AnalysisResults.TopWarnings[$message].Count++
        $this.AnalysisResults.TopWarnings[$message].LastOccurrence = $timestamp
    }
    
    [string]ExtractPattern([string]$message) {
        # Extract common patterns from error messages
        if ($message -match 'File not found: (.+)') {
            return "File not found"
        }
        elseif ($message -match 'Connection failed to (.+)') {
            return "Connection failed"
        }
        elseif ($message -match 'Permission denied: (.+)') {
            return "Permission denied"
        }
        elseif ($message -match 'Timeout after (\d+) seconds') {
            return "Timeout"
        }
        elseif ($message -match 'Invalid (.+): (.+)') {
            return "Invalid $($matches[1])"
        }
        
        return $null
    }
    
    [hashtable]SortHashtable([hashtable]$hashtable) {
        $sorted = @{}
        $hashtable.GetEnumerator() | Sort-Object { $_.Value.Count } -Descending | ForEach-Object {
            $sorted[$_.Key] = $_.Value
        }
        return $sorted
    }
    
    [void]GenerateReport() {
        Write-Host "`n=== Log Analysis Report ==="
        Write-Host "Log file: $($this.LogPath)"
        Write-Host "Total lines: $($this.AnalysisResults.TotalLines)"
        
        if ($this.AnalysisResults.DateRange.ContainsKey("Start")) {
            $duration = $this.AnalysisResults.DateRange.End - $this.AnalysisResults.DateRange.Start
            Write-Host "Date range: $($this.AnalysisResults.DateRange.Start) to $($this.AnalysisResults.DateRange.End)"
            Write-Host "Duration: $($duration.Days) days, $($duration.Hours) hours"
        }
        
        Write-Host "`nLog Levels:"
        Write-Host "  Errors: $($this.AnalysisResults.ErrorCount)"
        Write-Host "  Warnings: $($this.AnalysisResults.WarningCount)"
        Write-Host "  Info: $($this.AnalysisResults.InfoCount)"
        Write-Host "  Debug: $($this.AnalysisResults.DebugCount)"
        
        # Top errors
        if ($this.AnalysisResults.TopErrors.Count -gt 0) {
            Write-Host "`nTop Errors:"
            $topErrors = $this.AnalysisResults.TopErrors.GetEnumerator() | Select-Object -First 5
            foreach ($error in $topErrors) {
                Write-Host "  ($($error.Value.Count) occurrences) $($error.Key)"
            }
        }
        
        # Top warnings
        if ($this.AnalysisResults.TopWarnings.Count -gt 0) {
            Write-Host "`nTop Warnings:"
            $topWarnings = $this.AnalysisResults.TopWarnings.GetEnumerator() | Select-Object -First 5
            foreach ($warning in $topWarnings) {
                Write-Host "  ($($warning.Value.Count) occurrences) $($warning.Key)"
            }
        }
        
        # Error patterns
        if ($this.AnalysisResults.ErrorPatterns.Count -gt 0) {
            Write-Host "`nError Patterns:"
            $this.AnalysisResults.ErrorPatterns.GetEnumerator() | Sort-Object { $_.Value } -Descending | ForEach-Object {
                Write-Host "  $($_.Key): $($_.Value) occurrences"
            }
        }
        
        # Hourly distribution
        if ($this.AnalysisResults.HourlyDistribution.Count -gt 0) {
            Write-Host "`nHourly Distribution:"
            for ($hour = 0; $hour -lt 24; $hour++) {
                $count = if ($this.AnalysisResults.HourlyDistribution.ContainsKey($hour)) { 
                    $this.AnalysisResults.HourlyDistribution[$hour] 
                } else { 
                    0 
                }
                $bar = "█" * [math]::Min($count / 10, 20)
                Write-Host "  $hour`:00 $bar ($count)"
            }
        }
    }
    
    [void]ExportReport([string]$reportPath) {
        $report = [PSCustomObject]@{
            LogPath = $this.LogPath
            AnalysisDate = Get-Date
            TotalLines = $this.AnalysisResults.TotalLines
            ErrorCount = $this.AnalysisResults.ErrorCount
            WarningCount = $this.AnalysisResults.WarningCount
            InfoCount = $this.AnalysisResults.InfoCount
            DebugCount = $this.AnalysisResults.DebugCount
            DateRange = $this.AnalysisResults.DateRange
            TopErrors = $this.AnalysisResults.TopErrors
            TopWarnings = $this.AnalysisResults.TopWarnings
            ErrorPatterns = $this.AnalysisResults.ErrorPatterns
            HourlyDistribution = $this.AnalysisResults.HourlyDistribution
        }
        
        $report | ConvertTo-Json -Depth 4 | Set-Content $reportPath
        Write-Host "Report exported to: $reportPath"
    }
}

# Create sample log file for testing
$sampleLog = @"
[2023-12-01 10:00:01] [INFO] Application started
[2023-12-01 10:00:02] [INFO] Database connection established
[2023-12-01 10:00:05] [ERROR] File not found: config.json
[2023-12-01 10:00:10] [WARN] High memory usage detected
[2023-12-01 10:00:15] [INFO] User login: john.doe
[2023-12-01 10:00:20] [ERROR] Connection failed to api.example.com
[2023-12-01 10:00:25] [INFO] Processing request
[2023-12-01 10:00:30] [ERROR] File not found: config.json
[2023-12-01 10:00:35] [WARN] Timeout after 30 seconds
[2023-12-01 10:00:40] [INFO] Request completed
[2023-12-01 10:00:45] [ERROR] Permission denied: /var/log/app.log
[2023-12-01 10:00:50] [INFO] User logout: john.doe
"@

$sampleLog | Set-Content "sample.log"

# Analyze the log
$analyzer = [LogAnalyzer]::new("sample.log")
$analyzer.AnalyzeLog()
$analyzer.GenerateReport()
$analyzer.ExportReport("log_analysis.json")
```

### Example 3: File System Cleaner

```powershell
class FileSystemCleaner {
    [string]$TargetPath
    [hashtable]$CleanupRules
    [hashtable]$Statistics
    
    FileSystemCleaner([string]$targetPath) {
        $this.TargetPath = $targetPath
        $this.CleanupRules = @{
            TempFiles = @{
                Enabled = $true
                Patterns = @("*.tmp", "*.temp", "~*")
                MaxAge = 7
                MinSize = 1MB
            }
            LogFiles = @{
                Enabled = $true
                Patterns = @("*.log", "*.log.*")
                MaxAge = 30
                MinSize = 10MB
            }
            BackupFiles = @{
                Enabled = $true
                Patterns = @("*.bak", "*.backup", "*.old")
                MaxAge = 90
                MinSize = 0
            }
            CacheFiles = @{
                Enabled = $true
                Patterns = @("*.cache", "*.cache.*")
                MaxAge = 14
                MinSize = 5MB
            }
        }
        $this.Statistics = @{
            FilesScanned = 0
            FilesDeleted = 0
            SpaceFreed = 0
            Errors = @()
        }
    }
    
    [void]AddCleanupRule([string]$ruleName, [hashtable]$rule) {
        $this.CleanupRules[$ruleName] = $rule
    }
    
    [void]ExecuteCleanup() {
        Write-Host "Starting cleanup of: $($this.TargetPath)"
        
        if (-not (Test-Path $this.TargetPath)) {
            throw "Target path not found: $($this.TargetPath)"
        }
        
        $this.Statistics = @{
            FilesScanned = 0
            FilesDeleted = 0
            SpaceFreed = 0
            Errors = @()
        }
        
        foreach ($ruleName in $this.CleanupRules.Keys) {
            $rule = $this.CleanupRules[$ruleName]
            if ($rule.Enabled) {
                Write-Host "Processing rule: $ruleName"
                $this.ProcessCleanupRule($ruleName, $rule)
            }
        }
        
        $this.DisplayCleanupSummary()
    }
    
    [void]ProcessCleanupRule([string]$ruleName, [hashtable]$rule) {
        $cutoffDate = (Get-Date).AddDays(-$rule.MaxAge)
        
        foreach ($pattern in $rule.Patterns) {
            Write-Host "  Scanning for pattern: $pattern"
            
            try {
                $files = Get-ChildItem -Path $this.TargetPath -Filter $pattern -Recurse -File -ErrorAction SilentlyContinue
                
                foreach ($file in $files) {
                    $this.Statistics.FilesScanned++
                    
                    # Check age
                    if ($file.LastWriteTime -lt $cutoffDate) {
                        # Check size
                        if ($file.Length -ge $rule.MinSize) {
                            try {
                                $size = $file.Length
                                Remove-Item -Path $file.FullName -Force
                                
                                $this.Statistics.FilesDeleted++
                                $this.Statistics.SpaceFreed += $size
                                
                                Write-Host "    Deleted: $($file.Name) ($([math]::Round($size / 1MB, 2)) MB)"
                            }
                            catch {
                                $errorInfo = "Failed to delete $($file.FullName): $($_.Exception.Message)"
                                $this.Statistics.Errors += $errorInfo
                                Write-Warning "    $errorInfo"
                            }
                        }
                    }
                }
            }
            catch {
                $errorInfo = "Error scanning pattern $pattern`: $($_.Exception.Message)"
                $this.Statistics.Errors += $errorInfo
                Write-Warning $errorInfo
            }
        }
    }
    
    [void]DisplayCleanupSummary() {
        Write-Host "`n=== Cleanup Summary ==="
        Write-Host "Files scanned: $($this.Statistics.FilesScanned)"
        Write-Host "Files deleted: $($this.Statistics.FilesDeleted)"
        Write-Host "Space freed: $([math]::Round($this.Statistics.SpaceFreed / 1MB, 2)) MB"
        
        if ($this.Statistics.Errors.Count -gt 0) {
            Write-Host "`nErrors encountered:"
            $this.Statistics.Errors | ForEach-Object { Write-Host "  - $_" -ForegroundColor Red }
        }
        
        if ($this.Statistics.FilesDeleted -gt 0) {
            Write-Host "Cleanup completed successfully!" -ForegroundColor Green
        }
        else {
            Write-Host "No files matched cleanup criteria." -ForegroundColor Yellow
        }
    }
    
    [void]PreviewCleanup() {
        Write-Host "Cleanup Preview for: $($this.TargetPath)"
        Write-Host "(No files will be deleted)`n"
        
        foreach ($ruleName in $this.CleanupRules.Keys) {
            $rule = $this.CleanupRules[$ruleName]
            if ($rule.Enabled) {
                Write-Host "Rule: $ruleName"
                $cutoffDate = (Get-Date).AddDays(-$rule.MaxAge)
                
                foreach ($pattern in $rule.Patterns) {
                    $files = Get-ChildItem -Path $this.TargetPath -Filter $pattern -Recurse -File -ErrorAction SilentlyContinue
                    $matchingFiles = $files | Where-Object { 
                        $_.LastWriteTime -lt $cutoffDate -and $_.Length -ge $rule.MinSize 
                    }
                    
                    if ($matchingFiles.Count -gt 0) {
                        Write-Host "  Pattern: $pattern"
                        $totalSize = ($matchingFiles | Measure-Object -Property Length -Sum).Sum
                        Write-Host "    Files to delete: $($matchingFiles.Count)"
                        Write-Host "    Space to free: $([math]::Round($totalSize / 1MB, 2)) MB"
                        
                        # Show first few files as examples
                        $matchingFiles | Select-Object -First 3 | ForEach-Object {
                            $age = (Get-Date) - $_.LastWriteTime
                            Write-Host "      - $($_.Name) ($([math]::Round($_.Length / 1MB, 2)) MB, $($age.Days) days old)"
                        }
                        
                        if ($matchingFiles.Count -gt 3) {
                            Write-Host "      ... and $($matchingFiles.Count - 3) more files"
                        }
                    }
                }
            }
        }
    }
    
    [void]GenerateReport([string]$reportPath) {
        $report = [PSCustomObject]@{
            TargetPath = $this.TargetPath
            CleanupDate = Get-Date
            CleanupRules = $this.CleanupRules
            Statistics = $this.Statistics
        }
        
        $report | ConvertTo-Json -Depth 4 | Set-Content $reportPath
        Write-Host "Cleanup report saved to: $reportPath"
    }
}

# Usage example
$cleaner = [FileSystemCleaner]::new("C:\temp")
$cleaner.PreviewCleanup()
$cleaner.ExecuteCleanup()
$cleaner.GenerateReport("cleanup_report.json")
```

## 📝 Exercises

### Exercise 1: File Organizer
Create a file organization script that:
1. Scans a directory for different file types
2. Organizes files into subdirectories by type
3. Renames files based on date patterns
4. Generates organization report
5. Handles duplicate files

### Exercise 2: Log Monitor
Create a log monitoring system that:
1. Monitors multiple log files simultaneously
2. Detects specific patterns and thresholds
4. Sends alerts for critical events
5. Rotates log files when they get too large

### Exercise 3: File Synchronizer
Create a file synchronization tool that:
1. Synchronizes files between two directories
2. Detects conflicts and changes
3. Preserves file permissions
4. Provides dry-run mode
5. Generates sync reports

## 🎯 Key Takeaways

- **Get-ChildItem** is the primary cmdlet for listing files and directories
- **Test-Path** validates file and directory existence
- **New-Item**, **Copy-Item**, **Move-Item**, **Remove-Item** handle file operations
- **Get-Content** and **Set-Content** read and write file contents
- **Join-Path** and **Split-Path** manipulate file paths safely
- **FileSystemWatcher** enables real-time file system monitoring
- **ACL cmdlets** manage file permissions on Windows
- **Error handling** is crucial for file system operations
- **Performance** matters when working with large numbers of files

## 🔄 Next Steps

Move on to [10-Modules and Scripts](10-modules-and-scripts.md) to learn about organizing and reusing PowerShell code.
