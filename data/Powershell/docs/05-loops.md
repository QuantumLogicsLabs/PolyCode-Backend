# 05 - Loops

## 🎯 Learning Objectives

After completing this section, you will understand:
- How to use For loops for counted iterations
- While and Do-While loops for conditional iterations
- Do-Until loops for negative condition loops
- ForEach loops for iterating through collections
- Loop control statements (Break, Continue, Return)
- Best practices for loop performance and readability

## 🔢 For Loops

For loops are used when you know exactly how many times you want to iterate.

### Basic For Loop Structure

```powershell
# Basic syntax: for (initialization; condition; increment) { code }
for ($i = 1; $i -le 10; $i++) {
    Write-Host "Iteration $i"
}

# Counting backwards
for ($i = 10; $i -ge 1; $i--) {
    Write-Host "Countdown: $i"
}

# Step by more than 1
for ($i = 0; $i -le 20; $i += 2) {
    Write-Host "Even number: $i"
}
```

### Complex For Loop Examples

```powershell
# Multiple variables in initialization
for ($i = 0, $j = 10; $i -lt $j; $i++, $j--) {
    Write-Host "i=$i, j=$j"
}

# Nested for loops
for ($i = 1; $i -le 3; $i++) {
    Write-Host "Outer loop: $i"
    for ($j = 1; $j -le 3; $j++) {
        Write-Host "  Inner loop: $j"
    }
}

# For loop with arrays
$fruits = "Apple", "Banana", "Cherry", "Date"
for ($i = 0; $i -lt $fruits.Length; $i++) {
    Write-Host "Fruit $($i + 1): $($fruits[$i])"
}
```

## 🔄 While Loops

While loops continue as long as a condition is true. The condition is checked before each iteration.

### Basic While Loop

```powershell
# Simple while loop
$count = 1
while ($count -le 5) {
    Write-Host "Count: $count"
    $count++
}

# While loop with user input
$password = ""
while ($password -ne "secret") {
    $password = Read-Host "Enter password"
    if ($password -ne "secret") {
        Write-Host "Wrong password, try again"
    }
}
Write-Host "Access granted!"
```

### Practical While Loop Examples

```powershell
# Monitor process until it stops
$processName = "notepad"
$process = Get-Process -Name $processName -ErrorAction SilentlyContinue

while ($process) {
    Write-Host "Process $($process.Name) is running (PID: $($process.Id))"
    Start-Sleep -Seconds 2
    $process = Get-Process -Name $processName -ErrorAction SilentlyContinue
}
Write-Host "Process $processName has stopped"

# Wait for file to be created
$filePath = "C:\temp\test.txt"
while (-not (Test-Path $filePath)) {
    Write-Host "Waiting for file: $filePath"
    Start-Sleep -Seconds 1
}
Write-Host "File found!"

# Process queue until empty
$queue = "Task1", "Task2", "Task3", "Task4", "Task5"
while ($queue.Count -gt 0) {
    $currentTask = $queue[0]
    Write-Host "Processing: $currentTask"
    $queue = $queue[1..($queue.Length - 1)]  # Remove first item
    Start-Sleep -Milliseconds 500
}
```

## 🔁 Do-While Loops

Do-While loops execute the code block at least once, then check the condition.

### Basic Do-While Loop

```powershell
# Executes at least once
$count = 6
do {
    Write-Host "Count: $count"
    $count++
} while ($count -le 5)  # This condition is false, but loop runs once

# Menu system
do {
    Write-Host "=== Menu ==="
    Write-Host "1. Add User"
    Write-Host "2. Delete User"
    Write-Host "3. List Users"
    Write-Host "4. Exit"
    $choice = Read-Host "Enter your choice (1-4)"
    
    switch ($choice) {
        "1" { Write-Host "Adding user..." }
        "2" { Write-Host "Deleting user..." }
        "3" { Write-Host "Listing users..." }
        "4" { Write-Host "Exiting..." }
        default { Write-Host "Invalid choice" }
    }
    
    if ($choice -ne "4") {
        Read-Host "Press Enter to continue"
    }
} while ($choice -ne "4")
```

### Do-While with Input Validation

```powershell
# Get valid number input
do {
    $input = Read-Host "Enter a positive number"
    $isValid = $input -match '^\d+$' -and [int]$input -gt 0
    if (-not $isValid) {
        Write-Host "Invalid input. Please enter a positive number."
    }
} while (-not $isValid)

Write-Host "You entered: $input"

# File processing with retry
$filePath = "C:\data\input.txt"
$attempts = 0
$maxAttempts = 3

do {
    $attempts++
    try {
        $content = Get-Content $filePath -ErrorAction Stop
        Write-Host "File read successfully on attempt $attempts"
        $success = $true
    }
    catch {
        Write-Host "Attempt $attempts failed: $($_.Exception.Message)"
        if ($attempts -lt $maxAttempts) {
            Write-Host "Retrying in 2 seconds..."
            Start-Sleep -Seconds 2
        }
        $success = $false
    }
} while (-not $success -and $attempts -lt $maxAttempts)

if (-not $success) {
    Write-Host "Failed to read file after $maxAttempts attempts"
}
```

## ⏹️ Do-Until Loops

Do-Until loops execute until a condition becomes true (opposite of Do-While).

### Basic Do-Until Loop

```powershell
# Loop until condition is true
$count = 1
do {
    Write-Host "Count: $count"
    $count++
} until ($count -gt 5)

# Wait for specific time
$targetTime = (Get-Date).AddMinutes(5)
do {
    Write-Host "Waiting... Current time: $(Get-Date -Format 'HH:mm:ss')"
    Start-Sleep -Seconds 10
} until ((Get-Date) -ge $targetTime)

Write-Host "Target time reached!"
```

### Do-Until with System Monitoring

```powershell
# Wait for service to start
$serviceName = "Spooler"
$timeout = 30  # seconds
$startTime = Get-Date

do {
    $service = Get-Service -Name $serviceName -ErrorAction SilentlyContinue
    $elapsed = (Get-Date) - $startTime
    
    if ($service.Status -eq "Running") {
        Write-Host "Service $serviceName is running!"
        break
    }
    
    if ($elapsed.TotalSeconds -gt $timeout) {
        Write-Host "Timeout waiting for service $serviceName"
        break
    }
    
    Write-Host "Waiting for service... ($($elapsed.TotalSeconds.ToString('F0'))s elapsed)"
    Start-Sleep -Seconds 2
} until ($false)

# Process until condition met
$randomNumbers = @()
do {
    $number = Get-Random -Minimum 1 -Maximum 100
    $randomNumbers += $number
    Write-Host "Generated: $number"
} until ($number -eq 50 -or $randomNumbers.Count -ge 20)

Write-Host "Stopped after $($randomNumbers.Count) numbers"
Write-Host "Numbers: $($randomNumbers -join ', ')"
```

## 📋 ForEach Loops

ForEach loops iterate through each item in a collection.

### Basic ForEach Loop

```powershell
# ForEach-Object (pipeline)
$fruits = "Apple", "Banana", "Cherry"
$fruits | ForEach-Object {
    Write-Host "Fruit: $_"
}

# ForEach statement
$fruits = "Apple", "Banana", "Cherry"
foreach ($fruit in $fruits) {
    Write-Host "Fruit: $fruit"
}

# ForEach with file processing
$files = Get-ChildItem "C:\temp\*.txt"
foreach ($file in $files) {
    Write-Host "Processing: $($file.Name)"
    $lines = (Get-Content $file).Count
    Write-Host "  Lines: $lines"
}
```

### Advanced ForEach Examples

```powershell
# ForEach with nested collections
$users = @(
    @{ Name = "John"; Age = 25; City = "New York" },
    @{ Name = "Jane"; Age = 30; City = "Chicago" },
    @{ Name = "Bob"; Age = 35; City = "Los Angeles" }
)

foreach ($user in $users) {
    Write-Host "$($user.Name) is $($user.Age) years old and lives in $($user.City)"
}

# ForEach with break and continue
$numbers = 1..20
foreach ($number in $numbers) {
    if ($number % 2 -eq 0) {
        continue  # Skip even numbers
    }
    
    if ($number -gt 15) {
        break  # Stop when number > 15
    }
    
    Write-Host "Odd number: $number"
}

# ForEach with file filtering
$directory = "C:\Windows\System32"
$files = Get-ChildItem -Path $directory -Filter "*.exe" | Where-Object Length -gt 1MB

foreach ($file in $files) {
    $sizeMB = [math]::Round($file.Length / 1MB, 2)
    Write-Host "$($file.Name) - $sizeMB MB"
}
```

## 🎮 Loop Control Statements

Control statements modify the normal flow of loops.

### Break Statement

```powershell
# Break exits the loop immediately
for ($i = 1; $i -le 10; $i++) {
    if ($i -eq 5) {
        break  # Exit loop when i equals 5
    }
    Write-Host "Iteration: $i"
}
Write-Host "Loop ended"

# Break in nested loops (breaks only inner loop)
for ($i = 1; $i -le 3; $i++) {
    Write-Host "Outer loop: $i"
    for ($j = 1; $j -le 5; $j++) {
        if ($j -eq 3) {
            break  # Only breaks inner loop
        }
        Write-Host "  Inner loop: $j"
    }
}

# Using labels to break specific loops
:outer for ($i = 1; $i -le 3; $i++) {
    Write-Host "Outer loop: $i"
    for ($j = 1; $j -le 5; $j++) {
        if ($i -eq 2 -and $j -eq 3) {
            break outer  # Break outer loop
        }
        Write-Host "  Inner loop: $j"
    }
}
```

### Continue Statement

```powershell
# Continue skips to next iteration
for ($i = 1; $i -le 10; $i++) {
    if ($i % 2 -eq 0) {
        continue  # Skip even numbers
    }
    Write-Host "Odd number: $i"
}

# Continue in While loop
$numbers = 1..10
$index = 0
while ($index -lt $numbers.Length) {
    $number = $numbers[$index]
    $index++
    
    if ($number -eq 5) {
        continue  # Skip number 5
    }
    
    Write-Host "Number: $number"
}

# Continue in ForEach loop
$processes = Get-Process
foreach ($process in $processes) {
    if ($process.ProcessName -like "*svchost*") {
        continue  # Skip system processes
    }
    
    if ($process.WorkingSet -gt 100MB) {
        Write-Host "$($process.ProcessName): $([math]::Round($process.WorkingSet / 1MB, 2)) MB"
    }
}
```

### Return Statement

```powershell
# Return exits function (not just loop)
function Find-Number {
    param([int[]]$Numbers, [int]$Target)
    
    foreach ($number in $Numbers) {
        Write-Host "Checking: $number"
        if ($number -eq $Target) {
            Write-Host "Found target: $number"
            return $number  # Return from function
        }
    }
    
    Write-Host "Target not found"
    return $null
}

$result = Find-Number -Numbers @(1, 5, 3, 7, 9) -Target 7
Write-Host "Result: $result"
```

## 🚀 Practical Examples

### Example 1: Batch File Processor

```powershell
function Process-BatchFiles {
    param(
        [string]$SourceDirectory,
        [string]$DestinationDirectory,
        [string]$FilePattern = "*.txt"
    )
    
    # Create destination if it doesn't exist
    if (-not (Test-Path $DestinationDirectory)) {
        New-Item -ItemType Directory -Path $DestinationDirectory | Out-Null
    }
    
    # Get all files matching pattern
    $files = Get-ChildItem -Path $SourceDirectory -Filter $FilePattern
    $totalFiles = $files.Count
    $processedFiles = 0
    $failedFiles = @()
    
    Write-Host "Found $totalFiles files to process"
    
    foreach ($file in $files) {
        try {
            Write-Host "Processing $($file.Name)... ($($processedFiles + 1)/$totalFiles)"
            
            # Process file (example: add timestamp)
            $content = Get-Content $file.FullName
            $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
            $processedContent = @("Processed: $timestamp") + $content
            
            # Save to destination
            $destPath = Join-Path $DestinationDirectory $file.Name
            $processedContent | Set-Content $destPath
            
            $processedFiles++
            
            # Progress indicator
            $progress = [math]::Round(($processedFiles / $totalFiles) * 100, 1)
            Write-Host "Progress: $progress% complete"
            
        }
        catch {
            Write-Host "Error processing $($file.Name): $($_.Exception.Message)" -ForegroundColor Red
            $failedFiles += $file.Name
        }
    }
    
    # Summary
    Write-Host "`n=== Processing Summary ==="
    Write-Host "Total files: $totalFiles"
    Write-Host "Successfully processed: $processedFiles"
    Write-Host "Failed: $($failedFiles.Count)"
    
    if ($failedFiles.Count -gt 0) {
        Write-Host "Failed files:"
        $failedFiles | ForEach-Object { Write-Host "  - $_" }
    }
    
    return @{
        Total = $totalFiles
        Processed = $processedFiles
        Failed = $failedFiles.Count
        FailedFiles = $failedFiles
    }
}

# Test the function (requires actual files)
# $result = Process-BatchFiles -SourceDirectory "C:\temp\input" -DestinationDirectory "C:\temp\output"
```

### Example 2: Service Health Monitor

```powershell
function Monitor-Services {
    param(
        [string[]]$ServiceNames,
        [int]$CheckInterval = 30,
        [int]$MaxChecks = 10
    )
    
    $checkCount = 0
    $serviceStatus = @{}
    
    # Initialize service status
    foreach ($serviceName in $ServiceNames) {
        $service = Get-Service -Name $serviceName -ErrorAction SilentlyContinue
        $serviceStatus[$serviceName] = @{
            InitialStatus = if ($service) { $service.Status } else { "NotFound" }
            CurrentStatus = if ($service) { $service.Status } else { "NotFound" }
            Failures = 0
            LastCheck = Get-Date
        }
    }
    
    Write-Host "Starting service monitoring..."
    Write-Host "Checking services every $CheckInterval seconds (max $MaxChecks checks)"
    
    while ($checkCount -lt $MaxChecks) {
        $checkCount++
        $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
        Write-Host "`n=== Check $checkCount at $timestamp ==="
        
        foreach ($serviceName in $ServiceNames) {
            $service = Get-Service -Name $serviceName -ErrorAction SilentlyContinue
            $currentStatus = if ($service) { $service.Status } else { "NotFound" }
            $previousStatus = $serviceStatus[$serviceName].CurrentStatus
            
            # Update status
            $serviceStatus[$serviceName].CurrentStatus = $currentStatus
            $serviceStatus[$serviceName].LastCheck = Get-Date
            
            # Check for status changes
            if ($currentStatus -ne $previousStatus) {
                Write-Host "[$serviceName] Status changed: $previousStatus -> $currentStatus" -ForegroundColor Yellow
                
                if ($currentStatus -eq "Stopped") {
                    $serviceStatus[$serviceName].Failures++
                    Write-Host "[$serviceName] Failure count: $($serviceStatus[$serviceName].Failures)" -ForegroundColor Red
                }
            }
            
            # Display current status
            $statusSymbol = switch ($currentStatus) {
                "Running" { "✓" }
                "Stopped" { "✗" }
                "NotFound" { "?" }
                default { "⚠" }
            }
            
            Write-Host "  $statusSymbol $serviceName : $currentStatus"
        }
        
        # Check if we should stop monitoring
        $allRunning = $true
        foreach ($serviceName in $ServiceNames) {
            if ($serviceStatus[$serviceName].CurrentStatus -ne "Running") {
                $allRunning = $false
                break
            }
        }
        
        if ($allRunning) {
            Write-Host "All services are running. Monitoring complete." -ForegroundColor Green
            break
        }
        
        if ($checkCount -lt $MaxChecks) {
            Write-Host "Waiting $CheckInterval seconds until next check..."
            Start-Sleep -Seconds $CheckInterval
        }
    }
    
    # Final summary
    Write-Host "`n=== Final Summary ==="
    foreach ($serviceName in $ServiceNames) {
        $status = $serviceStatus[$serviceName]
        Write-Host "$serviceName : $($status.CurrentStatus) (Failures: $($status.Failures))"
    }
    
    return $serviceStatus
}

# Test the function
$services = @("Spooler", "Themes", "AudioSrv")
Monitor-Services -ServiceNames $services -CheckInterval 5 -MaxChecks 3
```

### Example 3: Data Validation Loop

```powershell
function Validate-DataBatch {
    param([array]$Data)
    
    $validRecords = @()
    $invalidRecords = @()
    $validationRules = @{
        Name = { param($value) $value -and $value.Length -ge 2 }
        Age = { param($value) $value -is [int] -and $value -ge 0 -and $value -le 150 }
        Email = { param($value) $value -match '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$' }
        Salary = { param($value) $value -is [double] -and $value -ge 0 }
    }
    
    Write-Host "Validating $($Data.Count) records..."
    
    foreach ($record in $Data) {
        $isValid = $true
        $errors = @()
        
        # Validate each field
        foreach ($field in $validationRules.Keys) {
            $value = $record[$field]
            $validator = $validationRules[$field]
            
            if (-not (& $validator $value)) {
                $isValid = $false
                $errors += "$field is invalid"
            }
        }
        
        # Categorize record
        if ($isValid) {
            $validRecords += $record
            Write-Host "✓ Valid record: $($record.Name)" -ForegroundColor Green
        }
        else {
            $invalidRecord = $record.Clone()
            $invalidRecord | Add-Member -NotePropertyName "Errors" -NotePropertyValue $errors
            $invalidRecords += $invalidRecord
            Write-Host "✗ Invalid record: $($record.Name) - $($errors -join ', ')" -ForegroundColor Red
        }
    }
    
    # Summary
    Write-Host "`n=== Validation Summary ==="
    Write-Host "Total records: $($Data.Count)"
    Write-Host "Valid records: $($validRecords.Count)"
    Write-Host "Invalid records: $($invalidRecords.Count)"
    
    return @{
        Valid = $validRecords
        Invalid = $invalidRecords
        Summary = @{
            Total = $Data.Count
            ValidCount = $validRecords.Count
            InvalidCount = $invalidRecords.Count
            ValidPercent = [math]::Round(($validRecords.Count / $Data.Count) * 100, 2)
        }
    }
}

# Test data
$testData = @(
    @{ Name = "John Doe"; Age = 30; Email = "john@example.com"; Salary = 50000.0 },
    @{ Name = "Jane"; Age = 25; Email = "jane@example.com"; Salary = 60000 },
    @{ Name = "A"; Age = -5; Email = "invalid-email"; Salary = -1000 },
    @{ Name = "Bob Smith"; Age = 35; Email = "bob@example.com"; Salary = 70000.0 }
)

$result = Validate-DataBatch -Data $testData
Write-Host "Validation complete: $($result.Summary.ValidPercent)% valid"
```

## 📝 Exercises

### Exercise 1: Number Guessing Game
Create a game that:
1. Generates a random number between 1-100
2. Loops until user guesses correctly
3. Provides hints (higher/lower)
4. Tracks number of attempts
5. Allows replay

### Exercise 2: File Cleanup Tool
Create a script that:
1. Scans a directory for old files
2. Uses loops to process each file
3. Prompts user before deletion
4. Provides progress feedback
5. Generates summary report

### Exercise 3: Performance Monitor
Create a monitoring script that:
1. Checks system performance metrics
2. Loops at regular intervals
3. Logs data to file
4. Alerts on threshold breaches
5. Runs for specified duration

## 🎯 Key Takeaways

- **For loops** are best for counted iterations
- **While loops** check conditions before executing
- **Do-While/Do-Until** execute at least once
- **ForEach loops** are perfect for collections
- **Break** exits the loop immediately
- **Continue** skips to the next iteration
- **Return** exits the entire function
- **Labels** can control nested loops
- **Performance** matters in large loops
- **Infinite loops** can be useful but need exit conditions

## 🔄 Next Steps

Move on to [06-Functions](06-functions.md) to learn how to create reusable code blocks with functions.
