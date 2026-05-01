# 11 - Error Handling

## 🎯 Learning Objectives

After completing this section, you will understand:
- How PowerShell errors work and their types
- Try/Catch/Finally blocks for error handling
- Error variables and automatic error handling
- Custom error handling and logging
- Best practices for robust scripts
- Debugging and troubleshooting techniques

## 🚨 Understanding PowerShell Errors

PowerShell has two main types of errors: terminating and non-terminating.

### Error Types

```powershell
# Terminating errors (stop script execution)
# These occur with syntax errors or when explicitly thrown
throw "This is a terminating error"

# Non-terminating errors (continue execution)
# These occur with cmdlet failures by default
Get-Process -Name "NonExistentProcess"  # Error but continues

# Error preference variables
$ErrorActionPreference = "Stop"     # Make all errors terminating
$ErrorActionPreference = "Continue" # Default behavior
$ErrorActionPreference = "SilentlyContinue" # Hide errors
$ErrorActionPreference = "Inquire"  # Prompt for action
$ErrorActionPreference = "Ignore"   # Ignore errors completely

# Per-command error handling
Get-Process -Name "NonExistentProcess" -ErrorAction Stop
Get-Process -Name "NonExistentProcess" -ErrorAction SilentlyContinue
```

### Error Variables

```powershell
# Automatic error variables
$Error          # Array of all errors in session
$Error[0]       # Most recent error
$Error.Count    # Number of errors
$?              # Success status of last command ($true/$false)
$LASTEXITCODE   # Exit code of last external program

# Clear error history
$Error.Clear()

# Error object properties
try {
    Get-Process -Name "NonExistentProcess" -ErrorAction Stop
}
catch {
    $errorRecord = $_
    Write-Host "Error type: $($errorRecord.GetType().FullName)"
    Write-Host "Exception: $($errorRecord.Exception)"
    Write-Host "Message: $($errorRecord.Exception.Message)"
    Write-Host "Category: $($errorRecord.CategoryInfo)"
    Write-Host "Target: $($errorRecord.CategoryInfo.TargetName)"
    Write-Host "Command: $($errorRecord.InvocationInfo.MyCommand.Name)"
    Write-Host "Line: $($errorRecord.InvocationInfo.ScriptLineNumber)"
}
```

## 🛡️ Try/Catch/Finally Blocks

Structured error handling provides robust error management.

### Basic Try/Catch

```powershell
# Basic try/catch structure
try {
    # Code that might cause an error
    $result = 100 / 0
}
catch {
    # Handle the error
    Write-Host "Error occurred: $($_.Exception.Message)"
}

# Multiple catch blocks for different exceptions
try {
    # Code that might cause different types of errors
    $file = Get-Content "nonexistent.txt"
    $number = [int]"abc"
}
catch [System.Management.Automation.ItemNotFoundException] {
    Write-Host "File not found error: $($_.Exception.Message)"
}
catch [System.Management.Automation.PSInvalidCastException] {
    Write-Host "Type conversion error: $($_.Exception.Message)"
}
catch {
    Write-Host "Other error: $($_.Exception.Message)"
}
```

### Finally Block

```powershell
# Finally block always executes
try {
    Write-Host "Opening resource"
    # Simulate opening a resource
    $resource = "Open connection"
    
    # Simulate an error
    throw "Something went wrong"
}
catch {
    Write-Host "Error: $($_.Exception.Message)"
}
finally {
    Write-Host "Closing resource"
    # Always cleanup, even if error occurred
    $resource = $null
}
```

### Nested Try/Catch

```powershell
function Get-Data {
    param([string]$Source)
    
    try {
        Write-Host "Attempting to read from: $Source"
        
        if ($Source -eq "database") {
            # Simulate database error
            throw "Database connection failed"
        }
        
        return "Data from $Source"
    }
    catch {
        Write-Host "Error in Get-Data: $($_.Exception.Message)"
        throw  # Re-throw to caller
    }
}

function Process-Data {
    param([string]$Source)
    
    try {
        $data = Get-Data -Source $Source
        Write-Host "Processing: $data"
    }
    catch {
        Write-Host "Error in Process-Data: $($_.Exception.Message)"
        # Handle or log the error
    }
}

Process-Data -Source "database"
Process-Data -Source "file"
```

## 🔧 Error Handling Techniques

### Parameter Validation

```powershell
function Test-Parameter {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory=$true)]
        [ValidateNotNullOrEmpty()]
        [string]$Name,
        
        [ValidateRange(1, 100)]
        [int]$Age,
        
        [ValidateSet("Active", "Inactive", "Pending")]
        [string]$Status,
        
        [ValidateScript({ Test-Path $_ -PathType Leaf })]
        [string]$ConfigFile
    )
    
    Write-Host "Validated parameters: Name=$Name, Age=$Age, Status=$Status"
}

# This will cause validation errors
# Test-Parameter -Name "" -Age 150 -Status "Invalid" -ConfigFile "nonexistent.txt"
```

### Custom Error Handling

```powershell
# Write-Error for non-terminating errors
function Get-UserInfo {
    param([string]$Username)
    
    if ([string]::IsNullOrEmpty($Username)) {
        Write-Error "Username cannot be empty" -ErrorAction Stop
    }
    
    if ($Username.Length -lt 3) {
        Write-Error "Username must be at least 3 characters long"
        return
    }
    
    # Simulate user lookup
    if ($Username -eq "admin") {
        return @{ Name = "Administrator"; Role = "Admin" }
    }
    else {
        Write-Error "User '$Username' not found"
    }
}

# Throw for terminating errors
function Connect-Database {
    param([string]$ConnectionString)
    
    if ([string]::IsNullOrEmpty($ConnectionString)) {
        throw "Connection string is required"
    }
    
    # Simulate connection attempt
    if ($ConnectionString -eq "invalid") {
        throw "Invalid connection string format"
    }
    
    Write-Host "Connected to database"
}

# ErrorRecord creation
function Create-CustomError {
    param([string]$Message, [string]$ErrorId = "CustomError")
    
    $exception = New-Object System.Exception($Message)
    $errorRecord = New-Object System.Management.Automation.ErrorRecord(
        $exception,
        $ErrorId,
        [System.Management.Automation.ErrorCategory]::InvalidOperation,
        $null
    )
    
    $PSCmdlet.WriteError($errorRecord)
}
```

### Error Logging

```powershell
class ErrorLogger {
    [string]$LogPath
    [string]$LogLevel
    
    ErrorLogger([string]$logPath, [string]$logLevel = "Info") {
        $this.LogPath = $logPath
        $this.LogLevel = $logLevel
    }
    
    [void]LogError([string]$message, [System.Management.Automation.ErrorRecord]$errorRecord = $null) {
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        $logEntry = "[$timestamp] ERROR: $message"
        
        if ($errorRecord) {
            $logEntry += "`n  Exception: $($errorRecord.Exception.Message)"
            $logEntry += "`n  Command: $($errorRecord.InvocationInfo.MyCommand.Name)"
            $logEntry += "`n  Line: $($errorRecord.InvocationInfo.ScriptLineNumber)"
            if ($errorRecord.InvocationInfo.ScriptName) {
                $logEntry += "`n  Script: $($errorRecord.InvocationInfo.ScriptName)"
            }
        }
        
        $logEntry | Add-Content $this.LogPath
        Write-Host $logEntry -ForegroundColor Red
    }
    
    [void]LogWarning([string]$message) {
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        $logEntry = "[$timestamp] WARNING: $message"
        
        $logEntry | Add-Content $this.LogPath
        Write-Host $logEntry -ForegroundColor Yellow
    }
    
    [void]LogInfo([string]$message) {
        if ($this.LogLevel -in @("Info", "Debug")) {
            $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
            $logEntry = "[$timestamp] INFO: $message"
            
            $logEntry | Add-Content $this.LogPath
            Write-Host $logEntry -ForegroundColor Green
        }
    }
}

# Usage example
$logger = [ErrorLogger]::new("error.log")

try {
    Get-Process -Name "NonExistentProcess" -ErrorAction Stop
}
catch {
    $logger.LogError("Failed to get process", $_)
}

$logger.LogWarning("This is a warning message")
$logger.LogInfo("This is an info message")
```

## 🚀 Advanced Error Handling

### Retry Logic

```powershell
function Invoke-RetryCommand {
    param(
        [Parameter(Mandatory=$true)]
        [scriptblock]$ScriptBlock,
        
        [int]$MaxAttempts = 3,
        
        [int]$DelaySeconds = 1,
        
        [string]$RetryMessage = "Retrying..."
    )
    
    $attempt = 1
    
    while ($attempt -le $MaxAttempts) {
        try {
            return & $ScriptBlock
        }
        catch {
            if ($attempt -eq $MaxAttempts) {
                Write-Error "Failed after $MaxAttempts attempts: $($_.Exception.Message)"
                throw
            }
            
            Write-Warning "Attempt $attempt failed: $($_.Exception.Message)"
            Write-Host "$RetryMessage (Attempt $($attempt + 1) of $MaxAttempts)"
            
            Start-Sleep -Seconds $DelaySeconds
            $attempt++
        }
    }
}

# Usage
$result = Invoke-RetryCommand -ScriptBlock { 
    # Simulate flaky operation
    if ((Get-Random -Minimum 1 -Maximum 3) -eq 1) {
        throw "Random failure"
    }
    return "Success"
} -MaxAttempts 5 -DelaySeconds 2
```

### Error Handling in Pipelines

```powershell
# Handle errors in pipeline operations
$files = @("file1.txt", "nonexistent.txt", "file3.txt")

$files | ForEach-Object {
    try {
        $content = Get-Content $_ -ErrorAction Stop
        Write-Host "Read $_: $($content.Count) lines"
    }
    catch {
        Write-Warning "Failed to read $_: $($_.Exception.Message)"
        # Continue with next file
    }
}

# Using -ErrorVariable
Get-Process -Name "NonExistentProcess" -ErrorVariable procError -ErrorAction SilentlyContinue
if ($procError) {
    Write-Warning "Process error captured: $($procError.Exception.Message)"
}

# Trap statement (older error handling)
trap {
    Write-Host "Trap caught error: $($_.Exception.Message)"
    continue  # Continue execution
}

Get-Process -Name "NonExistentProcess"  # Will be caught by trap
```

### Transactional Operations

```powershell
class TransactionManager {
    [System.Collections.Generic.List[scriptblock]]$Operations
    [System.Collections.Generic.List[scriptblock]]$Rollbacks
    
    TransactionManager() {
        $this.Operations = New-Object "System.Collections.Generic.List[scriptblock]"
        $this.Rollbacks = New-Object "System.Collections.Generic.List[scriptblock]"
    }
    
    [void]AddOperation([scriptblock]$operation, [scriptblock]$rollback) {
        $this.Operations.Add($operation)
        $this.Rollbacks.Add($rollback)
    }
    
    [void]Execute() {
        $completedOperations = 0
        
        try {
            for ($i = 0; $i -lt $this.Operations.Count; $i++) {
                & $this.Operations[$i]
                $completedOperations++
            }
            
            Write-Host "All operations completed successfully"
        }
        catch {
            Write-Error "Operation failed: $($_.Exception.Message)"
            Write-Host "Rolling back completed operations..."
            
            # Rollback in reverse order
            for ($i = $completedOperations - 1; $i -ge 0; $i--) {
                try {
                    & $this.Rollbacks[$i]
                    Write-Host "Rolled back operation $i"
                }
                catch {
                    Write-Warning "Failed to rollback operation $i: $($_.Exception.Message)"
                }
            }
            
            throw
        }
    }
}

# Usage example
$transaction = [TransactionManager]::new()

# Add file operations
$transaction.AddOperation(
    { 
        New-Item -Path "test1.txt" -ItemType File -Value "Content 1" -Force | Out-Null
        Write-Host "Created test1.txt"
    },
    { 
        Remove-Item -Path "test1.txt" -Force -ErrorAction SilentlyContinue
        Write-Host "Removed test1.txt"
    }
)

$transaction.AddOperation(
    { 
        New-Item -Path "test2.txt" -ItemType File -Value "Content 2" -Force | Out-Null
        Write-Host "Created test2.txt"
    },
    { 
        Remove-Item -Path "test2.txt" -Force -ErrorAction SilentlyContinue
        Write-Host "Removed test2.txt"
    }
)

# Execute transaction
try {
    $transaction.Execute()
}
catch {
    Write-Warning "Transaction failed: $($_.Exception.Message)"
}
```

## 🚀 Practical Examples

### Example 1: Robust File Processor

```powershell
class FileProcessor {
    [string]$SourcePath
    [string]$DestinationPath
    [ErrorLogger]$Logger
    [hashtable]$Statistics
    
    FileProcessor([string]$source, [string]$destination) {
        $this.SourcePath = $source
        $this.DestinationPath = $destination
        $this.Logger = [ErrorLogger]::new("file_processor.log")
        $this.Statistics = @{
            TotalFiles = 0
            ProcessedFiles = 0
            FailedFiles = 0
            SkippedFiles = 0
            TotalSize = 0
            ProcessedSize = 0
            Errors = @()
        }
    }
    
    [void]ProcessFiles() {
        $this.Logger.LogInfo("Starting file processing")
        $this.Logger.LogInfo("Source: $($this.SourcePath)")
        $this.Logger.LogInfo("Destination: $($this.DestinationPath)")
        
        # Validate paths
        if (-not (Test-Path $this.SourcePath)) {
            $this.Logger.LogError("Source path not found: $($this.SourcePath)")
            throw "Source path not found"
        }
        
        if (-not (Test-Path $this.DestinationPath)) {
            try {
                New-Item -Path $this.DestinationPath -ItemType Directory -Force | Out-Null
                $this.Logger.LogInfo("Created destination directory")
            }
            catch {
                $this.Logger.LogError("Failed to create destination directory", $_)
                throw
            }
        }
        
        # Get files to process
        try {
            $files = Get-ChildItem -Path $this.SourcePath -File -Recurse
            $this.Statistics.TotalFiles = $files.Count
            $this.Statistics.TotalSize = ($files | Measure-Object -Property Length -Sum).Sum
            $this.Logger.LogInfo("Found $($files.Count) files to process")
        }
        catch {
            $this.Logger.LogError("Failed to enumerate files", $_)
            throw
        }
        
        # Process each file
        foreach ($file in $files) {
            $this.ProcessFile($file)
        }
        
        # Generate summary
        $this.GenerateSummary()
    }
    
    [void]ProcessFile([System.IO.FileInfo]$file) {
        try {
            $relativePath = $file.FullName.Replace($this.SourcePath, "").TrimStart("\")
            $destinationFile = Join-Path $this.DestinationPath $relativePath
            
            # Check if file needs processing
            if ($this.ShouldSkipFile($file, $destinationFile)) {
                $this.Statistics.SkippedFiles++
                $this.Logger.LogInfo("Skipped: $($file.Name)")
                return
            }
            
            # Create destination directory if needed
            $destinationDir = Split-Path $destinationFile -Parent
            if (-not (Test-Path $destinationDir)) {
                New-Item -Path $destinationDir -ItemType Directory -Force | Out-Null
            }
            
            # Process file with retry logic
            $this.ProcessFileWithRetry($file, $destinationFile)
            
            $this.Statistics.ProcessedFiles++
            $this.Statistics.ProcessedSize += $file.Length
            
            if ($this.Statistics.ProcessedFiles % 10 -eq 0) {
                $this.Logger.LogInfo("Processed $($this.Statistics.ProcessedFiles) files")
            }
        }
        catch {
            $this.Statistics.FailedFiles++
            $errorInfo = "Failed to process $($file.Name): $($_.Exception.Message)"
            $this.Statistics.Errors += $errorInfo
            $this.Logger.LogError($errorInfo, $_)
        }
    }
    
    [bool]ShouldSkipFile([System.IO.FileInfo]$file, [string]$destinationFile) {
        # Skip if destination file exists and is newer
        if (Test-Path $destinationFile) {
            $destFile = Get-Item $destinationFile
            if ($destFile.LastWriteTime -ge $file.LastWriteTime) {
                return $true
            }
        }
        
        # Skip based on file size or extension
        if ($file.Length -eq 0) {
            return $true
        }
        
        return $false
    }
    
    [void]ProcessFileWithRetry([System.IO.FileInfo]$file, [string]$destinationFile) {
        $maxRetries = 3
        $retryDelay = 1
        
        for ($attempt = 1; $attempt -le $maxRetries; $attempt++) {
            try {
                Copy-Item -Path $file.FullName -Destination $destinationFile -Force
                $this.Logger.LogInfo("Processed: $($file.Name)")
                return
            }
            catch {
                if ($attempt -eq $maxRetries) {
                    throw "Failed to copy file after $maxRetries attempts"
                }
                
                $this.Logger.LogWarning("Copy attempt $attempt failed for $($file.Name), retrying...")
                Start-Sleep -Seconds $retryDelay
                $retryDelay *= 2  # Exponential backoff
            }
        }
    }
    
    [void]GenerateSummary() {
        $successRate = if ($this.Statistics.TotalFiles -gt 0) {
            [math]::Round(($this.Statistics.ProcessedFiles / $this.Statistics.TotalFiles) * 100, 2)
        } else { 0 }
        
        $this.Logger.LogInfo("=== Processing Summary ===")
        $this.Logger.LogInfo("Total files: $($this.Statistics.TotalFiles)")
        $this.Logger.LogInfo("Processed: $($this.Statistics.ProcessedFiles)")
        $this.Logger.LogInfo("Skipped: $($this.Statistics.SkippedFiles)")
        $this.Logger.LogInfo("Failed: $($this.Statistics.FailedFiles)")
        $this.Logger.LogInfo("Success rate: $successRate%")
        $this.Logger.LogInfo("Total size: $([math]::Round($this.Statistics.TotalSize / 1MB, 2)) MB")
        $this.Logger.LogInfo("Processed size: $([math]::Round($this.Statistics.ProcessedSize / 1MB, 2)) MB")
        
        if ($this.Statistics.Errors.Count -gt 0) {
            $this.Logger.LogWarning("Errors encountered:")
            foreach ($error in $this.Statistics.Errors) {
                $this.Logger.LogWarning("  - $error")
            }
        }
        
        Write-Host "`n=== File Processing Summary ==="
        Write-Host "Total files: $($this.Statistics.TotalFiles)"
        Write-Host "Processed: $($this.Statistics.ProcessedFiles)"
        Write-Host "Skipped: $($this.Statistics.SkippedFiles)"
        Write-Host "Failed: $($this.Statistics.FailedFiles)"
        Write-Host "Success rate: $successRate%"
        
        if ($this.Statistics.FailedFiles -eq 0) {
            Write-Host "All files processed successfully!" -ForegroundColor Green
        } else {
            Write-Host "$($this.Statistics.FailedFiles) files failed to process" -ForegroundColor Yellow
        }
    }
}

# Usage example
try {
    $processor = [FileProcessor]::new("C:\source", "C:\destination")
    $processor.ProcessFiles()
}
catch {
    Write-Error "File processing failed: $($_.Exception.Message)"
}
```

### Example 2: API Client with Error Handling

```powershell
class ApiClient {
    [string]$BaseUrl
    [hashtable]$Headers
    [int]$Timeout
    [ErrorLogger]$Logger
    
    ApiClient([string]$baseUrl, [int]$timeout = 30) {
        $this.BaseUrl = $baseUrl.TrimEnd('/')
        $this.Timeout = $timeout
        $this.Logger = [ErrorLogger]::new("api_client.log")
        $this.Headers = @{
            'Content-Type' = 'application/json'
            'Accept' = 'application/json'
        }
    }
    
    [void]SetAuthToken([string]$token) {
        $this.Headers['Authorization'] = "Bearer $token"
    }
    
    [object]InvokeRequest([string]$method, [string]$endpoint, [object]$body = $null) {
        $url = "$($this.BaseUrl)/$endpoint"
        $attempt = 1
        $maxAttempts = 3
        
        while ($attempt -le $maxAttempts) {
            try {
                $this.Logger.LogInfo("$method $url (attempt $attempt)")
                
                $params = @{
                    Method = $method
                    Uri = $url
                    Headers = $this.Headers
                    TimeoutSec = $this.Timeout
                }
                
                if ($body) {
                    $params.Body = $body | ConvertTo-Json -Depth 10
                }
                
                $response = Invoke-RestMethod @params
                
                $this.Logger.LogInfo("Request successful: $method $endpoint")
                return $response
            }
            catch [System.Net.WebException] {
                $webEx = $_.Exception
                $errorDetails = if ($_.ErrorDetails) { $_.ErrorDetails.Content } else { $webEx.Message }
                
                if ($webEx.Response -and $webEx.Response.StatusCode -eq 401) {
                    $this.Logger.LogError("Authentication failed for $endpoint")
                    throw "Authentication failed"
                }
                elseif ($webEx.Response -and $webEx.Response.StatusCode -eq 404) {
                    $this.Logger.LogError("Endpoint not found: $endpoint")
                    throw "Endpoint not found"
                }
                elseif ($webEx.Response -and $webEx.Response.StatusCode -ge 500) {
                    $this.Logger.LogWarning("Server error for $endpoint (attempt $attempt)")
                    if ($attempt -eq $maxAttempts) {
                        throw "Server error after $maxAttempts attempts"
                    }
                }
                else {
                    $this.Logger.LogError("Network error for $endpoint: $errorDetails")
                    if ($attempt -eq $maxAttempts) {
                        throw "Network error after $maxAttempts attempts"
                    }
                }
                
                $attempt++
                if ($attempt -le $maxAttempts) {
                    $retryDelay = [math]::Pow(2, $attempt - 1)  # Exponential backoff
                    $this.Logger.LogWarning("Retrying in $retryDelay seconds...")
                    Start-Sleep -Seconds $retryDelay
                }
            }
            catch {
                $this.Logger.LogError("Unexpected error for $endpoint", $_)
                throw
            }
        }
    }
    
    [object]Get([string]$endpoint) {
        return $this.InvokeRequest('GET', $endpoint)
    }
    
    [object]Post([string]$endpoint, [object]$body) {
        return $this.InvokeRequest('POST', $endpoint, $body)
    }
    
    [object]Put([string]$endpoint, [object]$body) {
        return $this.InvokeRequest('PUT', $endpoint, $body)
    }
    
    [void]Delete([string]$endpoint) {
        $this.InvokeRequest('DELETE', $endpoint) | Out-Null
    }
}

# Usage example
try {
    $client = [ApiClient]::new("https://api.example.com")
    $client.SetAuthToken("your-api-token")
    
    # Get data
    $users = $client.Get("users")
    Write-Host "Retrieved $($users.Count) users"
    
    # Create data
    $newUser = @{
        name = "John Doe"
        email = "john@example.com"
        role = "user"
    }
    
    $createdUser = $client.Post("users", $newUser)
    Write-Host "Created user: $($createdUser.name)"
}
catch {
    Write-Error "API operation failed: $($_.Exception.Message)"
}
```

### Example 3: Database Operations with Error Handling

```powershell
class DatabaseManager {
    [string]$ConnectionString
    [ErrorLogger]$Logger
    [bool]$IsConnected
    
    DatabaseManager([string]$connectionString) {
        $this.ConnectionString = $connectionString
        $this.Logger = [ErrorLogger]::new("database.log")
        $this.IsConnected = $false
    }
    
    [void]Connect() {
        try {
            $this.Logger.LogInfo("Connecting to database...")
            
            # Simulate connection (replace with actual database connection)
            if ($this.ConnectionString -eq "invalid") {
                throw "Invalid connection string"
            }
            
            $this.IsConnected = $true
            $this.Logger.LogInfo("Connected to database successfully")
        }
        catch {
            $this.Logger.LogError("Failed to connect to database", $_)
            throw
        }
    }
    
    [void]Disconnect() {
        try {
            if ($this.IsConnected) {
                # Simulate disconnection
                $this.IsConnected = $false
                $this.Logger.LogInfo("Disconnected from database")
            }
        }
        catch {
            $this.Logger.LogError("Error during disconnection", $_)
        }
    }
    
    [object]ExecuteQuery([string]$query, [hashtable]$parameters = @{}) {
        if (-not $this.IsConnected) {
            throw "Not connected to database"
        }
        
        try {
            $this.Logger.LogInfo("Executing query: $query")
            
            # Simulate query execution
            if ($query -like "*ERROR*") {
                throw "Invalid SQL syntax"
            }
            
            # Simulate returning data
            $result = @(
                @{ Id = 1; Name = "John"; Email = "john@example.com" },
                @{ Id = 2; Name = "Jane"; Email = "jane@example.com" }
            )
            
            $this.Logger.LogInfo("Query executed successfully, returned $($result.Count) rows")
            return $result
        }
        catch {
            $this.Logger.LogError("Query execution failed: $query", $_)
            throw
        }
    }
    
    [int]ExecuteNonQuery([string]$command, [hashtable]$parameters = @{}) {
        if (-not $this.IsConnected) {
            throw "Not connected to database"
        }
        
        try {
            $this.Logger.LogInfo("Executing non-query command: $command")
            
            # Simulate command execution
            if ($command -like "*ERROR*") {
                throw "Invalid SQL syntax"
            }
            
            $affectedRows = Get-Random -Minimum 1 -Maximum 10
            $this.Logger.LogInfo("Command executed successfully, affected $affectedRows rows")
            return $affectedRows
        }
        catch {
            $this.Logger.LogError("Command execution failed: $command", $_)
            throw
        }
    }
    
    [object]ExecuteTransaction([scriptblock]$operations) {
        if (-not $this.IsConnected) {
            throw "Not connected to database"
        }
        
        $this.Logger.LogInfo("Starting database transaction")
        
        try {
            # Simulate transaction start
            $this.Logger.LogInfo("Transaction started")
            
            # Execute operations
            $result = & $operations
            
            # Simulate transaction commit
            $this.Logger.LogInfo("Transaction committed successfully")
            return $result
        }
        catch {
            # Simulate transaction rollback
            $this.Logger.LogError("Transaction failed, rolling back", $_)
            throw
        }
    }
}

# Usage example
try {
    $db = [DatabaseManager]::new("Server=localhost;Database=test;Integrated Security=true")
    
    # Connect
    $db.Connect()
    
    # Simple query
    $users = $db.ExecuteQuery("SELECT * FROM users")
    Write-Host "Found $($users.Count) users"
    
    # Transaction
    $result = $db.ExecuteTransaction({
        $db.ExecuteNonQuery("INSERT INTO users (name, email) VALUES ('Alice', 'alice@example.com')")
        $db.ExecuteNonQuery("INSERT INTO users (name, email) VALUES ('Bob', 'bob@example.com')")
        return "Transaction completed"
    })
    
    Write-Host $result
    
    # Disconnect
    $db.Disconnect()
}
catch {
    Write-Error "Database operation failed: $($_.Exception.Message)"
}
```

## 📝 Exercises

### Exercise 1: Error Handling Framework
Create an error handling framework that:
1. Provides centralized logging
2. Supports different error levels
3. Includes retry logic
4. Generates error reports
5. Supports email notifications

### Exercise 2: Robust File Operations
Create a file operations script that:
1. Handles file access errors gracefully
2. Implements retry logic for network files
3. Validates file integrity
4. Provides detailed error reporting
5. Supports transactional operations

### Exercise 3: API Error Handling
Create an API client that:
1. Handles different HTTP status codes
2. Implements exponential backoff
3. Validates API responses
4. Logs all API interactions
5. Provides meaningful error messages

## 🎯 Key Takeaways

- **Terminating errors** stop script execution, **non-terminating** don't
- **Try/Catch/Finally** provides structured error handling
- **Error variables** ($Error, $?) contain error information
- **ErrorAction** parameter controls error behavior per command
- **Write-Error** creates non-terminating errors, **throw** creates terminating
- **Logging** is essential for debugging and monitoring
- **Retry logic** handles transient failures
- **Transactions** ensure atomic operations
- **Validation** prevents errors before they occur

## 🔄 Next Steps

Move on to [12-Advanced Topics](12-advanced-topics.md) to explore advanced PowerShell features and techniques.
