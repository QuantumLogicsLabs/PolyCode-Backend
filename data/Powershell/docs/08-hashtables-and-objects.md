# 08 - Hashtables and Objects

## 🎯 Learning Objectives

After completing this section, you will understand:
- How to create and manipulate hashtables
- Working with key-value pairs
- Creating custom objects with PSCustomObject
- Object properties and methods
- Object serialization and deserialization
- Best practices for object-oriented PowerShell

## 🔑 Hashtables

Hashtables are collections of key-value pairs that provide fast lookup by key.

### Creating Hashtables

```powershell
# Basic hashtable creation
$user = @{
    Name = "John Doe"
    Age = 30
    Email = "john@example.com"
    City = "New York"
}

# Empty hashtable
$empty = @{}

# Hashtable with different value types
$mixed = @{
    String = "Hello"
    Number = 42
    Boolean = $true
    Array = @(1, 2, 3)
    Nested = @{
        Inner = "Value"
    }
}

# Ordered hashtable (preserves insertion order)
$ordered = [ordered]@{
    First = "Value1"
    Second = "Value2"
    Third = "Value3"
}
```

### Accessing Hashtable Values

```powershell
$user = @{
    Name = "John Doe"
    Age = 30
    Email = "john@example.com"
    City = "New York"
}

# Access by key (dot notation)
$name = $user.Name
$age = $user.Age

# Access by key (bracket notation)
$email = $user["Email"]
$city = $user["City"]

# Access with variable key
$key = "Name"
$value = $user[$key]

# Check if key exists
if ($user.ContainsKey("Name")) {
    Write-Host "Name exists: $($user.Name)"
}

# Check if value exists
if ($user.ContainsValue("John Doe")) {
    Write-Host "Value 'John Doe' exists"
}

# Get all keys and values
$keys = $user.Keys
$values = $user.Values
```

### Modifying Hashtables

```powershell
$user = @{
    Name = "John Doe"
    Age = 30
    Email = "john@example.com"
}

# Add or update values
$user.City = "New York"
$user["Country"] = "USA"
$user.Age = 31

# Remove values
$user.Remove("Email")
$user.Remove("Country")

# Clear all values
$user.Clear()

# Add multiple values
$user += @{
    Phone = "555-1234"
    Department = "IT"
    IsActive = $true
}
```

### Hashtable Operations

```powershell
$settings = @{
    Server = "localhost"
    Port = 8080
    Timeout = 30
    UseSSL = $true
}

# Iterate through hashtable
foreach ($key in $settings.Keys) {
    $value = $settings[$key]
    Write-Host "$key = $value"
}

# Iterate with GetEnumerator()
foreach ($item in $settings.GetEnumerator()) {
    Write-Host "$($item.Key) = $($item.Value)"
}

# Filter hashtable
$numericSettings = $settings.GetEnumerator() | Where-Object { $_.Value -is [int] }

# Convert to array of objects
$settingsArray = $settings.GetEnumerator() | ForEach-Object {
    [PSCustomObject]@{
        Setting = $_.Key
        Value = $_.Value
    }
}

# Merge hashtables
$default = @{ Server = "localhost"; Port = 8080 }
$override = @{ Port = 9090; Timeout = 60 }
$merged = $default + $override
```

## 🏗️ Custom Objects

PowerShell allows you to create custom objects with properties and methods.

### PSCustomObject

```powershell
# Basic custom object
$user = [PSCustomObject]@{
    Name = "John Doe"
    Age = 30
    Email = "john@example.com"
    City = "New York"
}

# Add properties dynamically
$user | Add-Member -NotePropertyName "Country" -NotePropertyValue "USA"
$user | Add-Member -MemberType ScriptProperty -Name "DisplayName" -Value { "$($this.Name) ($($this.Age))" }

# Add methods
$user | Add-Member -MemberType ScriptMethod -Name "GetInfo" -Value {
    return "User: $($this.Name), Email: $($this.Email), Location: $($this.City), $($this.Country)"
}

# Use the object
Write-Host $user.DisplayName
Write-Host $user.GetInfo()
```

### Creating Objects with Constructors

```powershell
# Function to create user objects
function New-User {
    param(
        [Parameter(Mandatory=$true)]
        [string]$Name,
        
        [int]$Age,
        [string]$Email,
        [string]$City = "Unknown"
    )
    
    $user = [PSCustomObject]@{
        Name = $Name
        Age = $Age
        Email = $Email
        City = $City
        CreatedDate = Get-Date
        IsActive = $true
    }
    
    # Add calculated property
    $user | Add-Member -MemberType ScriptProperty -Name "IsAdult" -Value {
        return $this.Age -ge 18
    }
    
    # Add method
    $user | Add-Member -MemberType ScriptMethod -Name "Deactivate" -Value {
        $this.IsActive = $false
        $this.DeactivatedDate = Get-Date
    }
    
    # Add method
    $user | Add-Member -MemberType ScriptMethod -Name "UpdateAge" -Value {
        param([int]$NewAge)
        $this.Age = $NewAge
    }
    
    return $user
}

# Create and use user objects
$john = New-User -Name "John Doe" -Age 30 -Email "john@example.com" -City "New York"
$jane = New-User -Name "Jane Smith" -Age 25 -Email "jane@example.com"

Write-Host "$($john.Name) is adult: $($john.IsAdult)"
$john.UpdateAge(31)
Write-Host "$($john.Name) is now $($john.Age) years old"
```

### Object Classes (PowerShell 5+)

```powershell
# Define a class
class Employee {
    [string]$FirstName
    [string]$LastName
    [int]$EmployeeId
    [string]$Department
    [decimal]$Salary
    [DateTime]$HireDate
    
    # Constructor
    Employee([string]$firstName, [string]$lastName, [int]$employeeId) {
        $this.FirstName = $firstName
        $this.LastName = $lastName
        $this.EmployeeId = $employeeId
        $this.HireDate = Get-Date
        $this.Department = "General"
        $this.Salary = 30000
    }
    
    # Method
    [string]GetFullName() {
        return "$($this.FirstName) $($this.LastName)"
    }
    
    # Method
    [void]Promote([decimal]$raiseAmount) {
        $this.Salary += $raiseAmount
    }
    
    # Static method
    static [Employee]CreateManager([string]$firstName, [string]$lastName, [int]$employeeId) {
        $manager = [Employee]::new($firstName, $lastName, $employeeId)
        $manager.Department = "Management"
        $manager.Salary = 75000
        return $manager
    }
    
    # Property with getter/setter logic
    [string]$DisplayName {
        get { return "$($this.FirstName) $($this.LastName) ($($this.EmployeeId))" }
    }
    
    # Calculated property
    [int]$YearsOfService {
        get { return (Get-Date) - $this.HireDate | Select-Object -ExpandProperty Days / 365 }
    }
}

# Use the class
$emp1 = [Employee]::new("John", "Doe", 1001)
$emp1.Department = "IT"
$emp1.Salary = 65000

$manager = [Employee]::CreateManager("Jane", "Smith", 2001)

Write-Host "Employee: $($emp1.DisplayName)"
Write-Host "Years of service: $($emp1.YearsOfService)"
Write-Host "Full name: $($emp1.GetFullName())"

$emp1.Promote(5000)
Write-Host "New salary: $($emp1.Salary):C"
```

## 🔄 Object Serialization

Convert objects to and from different formats for storage or transmission.

### JSON Serialization

```powershell
# Create object
$user = [PSCustomObject]@{
    Name = "John Doe"
    Age = 30
    Email = "john@example.com"
    City = "New York"
    Preferences = @{
        Theme = "Dark"
        Language = "en-US"
        Notifications = $true
    }
}

# Convert to JSON
$json = $user | ConvertTo-Json -Depth 3
Write-Host $json

# Save to file
$json | Set-Content -Path "user.json"

# Convert from JSON
$loadedUser = Get-Content -Path "user.json" | ConvertFrom-Json
Write-Host "Loaded user: $($loadedUser.Name)"

# Convert array of objects
$users = @(
    [PSCustomObject]@{ Name = "John"; Age = 30; City = "New York" },
    [PSCustomObject]@{ Name = "Jane"; Age = 25; City = "Chicago" },
    [PSCustomObject]@{ Name = "Bob"; Age = 35; City = "Los Angeles" }
)

$usersJson = $users | ConvertTo-Json
$loadedUsers = $usersJson | ConvertFrom-Json
```

### XML Serialization

```powershell
# Create object
$config = [PSCustomObject]@{
    Database = @{
        Server = "localhost"
        Port = 5432
        Name = "myapp"
    }
    Logging = @{
        Level = "Info"
        File = "app.log"
    }
    Features = @{
        EnableCache = $true
        EnableDebug = $false
    }
}

# Convert to XML
$xml = $config | ConvertTo-Xml -Depth 3
Write-Host $xml.OuterXml

# Save to file
$xml.Save("config.xml")

# Load from XML
[xml]$loadedXml = Get-Content "config.xml"
$configNode = $loadedXml.Objects.Object

# Reconstruct object
$reconstructedConfig = [PSCustomObject]@{
    Database = @{
        Server = $configNode.Property[0].Property[0].InnerText
        Port = [int]$configNode.Property[0].Property[1].InnerText
        Name = $configNode.Property[0].Property[2].InnerText
    }
}
```

### CSV Import/Export

```powershell
# Create array of objects
$employees = @(
    [PSCustomObject]@{ Name = "John Doe"; Department = "IT"; Salary = 65000; HireDate = "2020-01-15" },
    [PSCustomObject]@{ Name = "Jane Smith"; Department = "HR"; Salary = 55000; HireDate = "2019-03-20" },
    [PSCustomObject]@{ Name = "Bob Johnson"; Department = "IT"; Salary = 70000; HireDate = "2018-07-10" }
)

# Export to CSV
$employees | Export-Csv -Path "employees.csv" -NoTypeInformation

# Import from CSV
$importedEmployees = Import-Csv -Path "employees.csv"

# Convert string dates back to DateTime
$importedEmployees | ForEach-Object {
    $_.HireDate = [DateTime]$_.HireDate
    $_.Salary = [double]$_.Salary
}
```

## 🚀 Practical Examples

### Example 1: Configuration Management System

```powershell
class ConfigurationManager {
    [hashtable]$Settings
    [string]$ConfigPath
    
    ConfigurationManager([string]$configPath) {
        $this.ConfigPath = $configPath
        $this.Settings = @{}
        $this.LoadConfiguration()
    }
    
    [void]LoadConfiguration() {
        if (Test-Path $this.ConfigPath) {
            try {
                $content = Get-Content $this.ConfigPath -Raw
                $this.Settings = $content | ConvertFrom-Json -AsHashtable
                Write-Host "Configuration loaded from $($this.ConfigPath)"
            }
            catch {
                Write-Warning "Failed to load configuration: $($_.Exception.Message)"
                $this.Settings = $this.GetDefaultSettings()
            }
        }
        else {
            Write-Host "Configuration file not found, using defaults"
            $this.Settings = $this.GetDefaultSettings()
            $this.SaveConfiguration()
        }
    }
    
    [hashtable]GetDefaultSettings() {
        return @{
            Database = @{
                Server = "localhost"
                Port = 5432
                Name = "myapp"
                Username = "admin"
                Password = "secret"
                Timeout = 30
            }
            Logging = @{
                Level = "Info"
                File = "logs/app.log"
                MaxSize = "10MB"
                RetentionDays = 30
            }
            Features = @{
                EnableCache = $true
                EnableDebug = $false
                EnableMetrics = $true
                MaxConnections = 100
            }
            Security = @{
                EnableSSL = $true
                RequireAuth = $true
                TokenExpiry = 3600
            }
        }
    }
    
    [void]SaveConfiguration() {
        try {
            $json = $this.Settings | ConvertTo-Json -Depth 4
            $json | Set-Content $this.ConfigPath
            Write-Host "Configuration saved to $($this.ConfigPath)"
        }
        catch {
            Write-Error "Failed to save configuration: $($_.Exception.Message)"
        }
    }
    
    [object]GetSetting([string]$key) {
        $keys = $key.Split('.')
        $current = $this.Settings
        
        foreach ($k in $keys) {
            if ($current.ContainsKey($k)) {
                $current = $current[$k]
            }
            else {
                return $null
            }
        }
        
        return $current
    }
    
    [void]SetSetting([string]$key, [object]$value) {
        $keys = $key.Split('.')
        $current = $this.Settings
        
        for ($i = 0; $i -lt $keys.Length - 1; $i++) {
            $k = $keys[$i]
            if (-not $current.ContainsKey($k)) {
                $current[$k] = @{}
            }
            $current = $current[$k]
        }
        
        $current[$keys[-1]] = $value
        $this.SaveConfiguration()
    }
    
    [void]ShowConfiguration() {
        Write-Host "=== Current Configuration ==="
        $this.DisplayHashtable($this.Settings, "")
    }
    
    [void]DisplayHashtable([hashtable]$hashtable, [string]$indent) {
        foreach ($key in $hashtable.Keys) {
            $value = $hashtable[$key]
            if ($value -is [hashtable]) {
                Write-Host "$indent$key:"
                $this.DisplayHashtable($value, "$indent  ")
            }
            else {
                Write-Host "$indent$key = $value"
            }
        }
    }
    
    [void]ValidateConfiguration() {
        $errors = @()
        
        # Validate database settings
        $dbServer = $this.GetSetting("Database.Server")
        $dbPort = $this.GetSetting("Database.Port")
        
        if ([string]::IsNullOrEmpty($dbServer)) {
            $errors += "Database.Server is required"
        }
        
        if ($dbPort -and ($dbPort -lt 1 -or $dbPort -gt 65535)) {
            $errors += "Database.Port must be between 1 and 65535"
        }
        
        # Validate logging level
        $logLevel = $this.GetSetting("Logging.Level")
        $validLevels = @("Debug", "Info", "Warning", "Error", "Fatal")
        if ($logLevel -and $logLevel -notin $validLevels) {
            $errors += "Logging.Level must be one of: $($validLevels -join ', ')"
        }
        
        # Display results
        if ($errors.Count -gt 0) {
            Write-Host "Configuration validation failed:" -ForegroundColor Red
            $errors | ForEach-Object { Write-Host "  - $_" -ForegroundColor Red }
        }
        else {
            Write-Host "Configuration validation passed" -ForegroundColor Green
        }
    }
}

# Usage example
$configManager = [ConfigurationManager]::new("app-config.json")
$configManager.ShowConfiguration()

# Get and set settings
$dbServer = $configManager.GetSetting("Database.Server")
Write-Host "Database server: $dbServer"

$configManager.SetSetting("Database.Port", 5433)
$configManager.SetSetting("Features.EnableDebug", $true)

# Validate configuration
$configManager.ValidateConfiguration()
```

### Example 2: User Management with Custom Objects

```powershell
class User {
    [string]$UserId
    [string]$Username
    [string]$Email
    [string]$FirstName
    [string]$LastName
    [DateTime]$CreatedDate
    [DateTime]$LastLogin
    [bool]$IsActive
    [string[]]$Roles
    [hashtable]$Preferences
    
    User([string]$username, [string]$email, [string]$firstName, [string]$lastName) {
        $this.UserId = -join ((48..57) + (65..90) + (97..122) | Get-Random -Count 8 | ForEach-Object { [char]$_ })
        $this.Username = $username
        $this.Email = $email
        $this.FirstName = $firstName
        $this.LastName = $lastName
        $this.CreatedDate = Get-Date
        $this.LastLogin = $null
        $this.IsActive = $true
        $this.Roles = @("User")
        $this.Preferences = @{
            Theme = "Light"
            Language = "en-US"
            Notifications = $true
            TimeZone = "UTC"
        }
    }
    
    [string]GetFullName() {
        return "$($this.FirstName) $($this.LastName)"
    }
    
    [void]AddRole([string]$role) {
        if ($role -notin $this.Roles) {
            $this.Roles += $role
        }
    }
    
    [void]RemoveRole([string]$role) {
        if ($role -in $this.Roles -and $role -ne "User") {
            $this.Roles = $this.Roles | Where-Object { $_ -ne $role }
        }
    }
    
    [void]UpdateLogin() {
        $this.LastLogin = Get-Date
    }
    
    [void]Deactivate() {
        $this.IsActive = $false
    }
    
    [void]Activate() {
        $this.IsActive = $true
    }
    
    [void]SetPreference([string]$key, [object]$value) {
        $this.Preferences[$key] = $value
    }
    
    [object]GetPreference([string]$key) {
        return $this.Preferences[$key]
    }
    
    [hashtable]ToHashtable() {
        return @{
            UserId = $this.UserId
            Username = $this.Username
            Email = $this.Email
            FirstName = $this.FirstName
            LastName = $this.LastName
            CreatedDate = $this.CreatedDate
            LastLogin = $this.LastLogin
            IsActive = $this.IsActive
            Roles = $this.Roles
            Preferences = $this.Preferences
        }
    }
}

class UserManager {
    [System.Collections.Generic.List[User]]$Users
    [string]$DataFile
    
    UserManager([string]$dataFile) {
        $this.DataFile = $dataFile
        $this.Users = New-Object "System.Collections.Generic.List[User]"
        $this.LoadUsers()
    }
    
    [void]LoadUsers() {
        if (Test-Path $this.DataFile) {
            try {
                $userData = Get-Content $this.DataFile | ConvertFrom-Json
                foreach ($userHash in $userData) {
                    $user = [User]::new($userHash.Username, $userHash.Email, $userHash.FirstName, $userHash.LastName)
                    $user.UserId = $userHash.UserId
                    $user.CreatedDate = [DateTime]$userHash.CreatedDate
                    $user.LastLogin = if ($userHash.LastLogin) { [DateTime]$userHash.LastLogin } else { $null }
                    $user.IsActive = [bool]$userHash.IsActive
                    $user.Roles = [string[]]$userHash.Roles
                    $user.Preferences = [hashtable]$userHash.Preferences
                    
                    $this.Users.Add($user)
                }
                Write-Host "Loaded $($this.Users.Count) users from $($this.DataFile)"
            }
            catch {
                Write-Warning "Failed to load users: $($_.Exception.Message)"
            }
        }
    }
    
    [void]SaveUsers() {
        try {
            $userData = $this.Users | ForEach-Object { $_.ToHashtable() }
            $json = $userData | ConvertTo-Json -Depth 4
            $json | Set-Content $this.DataFile
            Write-Host "Saved $($this.Users.Count) users to $($this.DataFile)"
        }
        catch {
            Write-Error "Failed to save users: $($_.Exception.Message)"
        }
    }
    
    [User]CreateUser([string]$username, [string]$email, [string]$firstName, [string]$lastName) {
        # Check if user already exists
        if ($this.Users | Where-Object { $_.Username -eq $username -or $_.Email -eq $email }) {
            throw "User with username '$username' or email '$email' already exists"
        }
        
        $user = [User]::new($username, $email, $firstName, $lastName)
        $this.Users.Add($user)
        $this.SaveUsers()
        
        Write-Host "Created user: $($user.GetFullName()) ($($user.Username))"
        return $user
    }
    
    [User]FindUser([string]$identifier) {
        return $this.Users | Where-Object { 
            $_.UserId -eq $identifier -or 
            $_.Username -eq $identifier -or 
            $_.Email -eq $identifier 
        } | Select-Object -First 1
    }
    
    [User[]]FindUsersByRole([string]$role) {
        return $this.Users | Where-Object { $role -in $_.Roles }
    }
    
    [User[]]GetActiveUsers() {
        return $this.Users | Where-Object { $_.IsActive }
    }
    
    [void]DeleteUser([string]$identifier) {
        $user = $this.FindUser($identifier)
        if ($user) {
            $this.Users.Remove($user)
            $this.SaveUsers()
            Write-Host "Deleted user: $($user.GetFullName())"
        }
        else {
            Write-Warning "User '$identifier' not found"
        }
    }
    
    [void]GenerateReport() {
        Write-Host "=== User Management Report ==="
        Write-Host "Total users: $($this.Users.Count)"
        Write-Host "Active users: $($this.GetActiveUsers().Count)"
        Write-Host "Inactive users: $($($this.Users.Count - $this.GetActiveUsers().Count))"
        
        # Users by role
        $roleStats = @{}
        foreach ($user in $this.Users) {
            foreach ($role in $user.Roles) {
                if (-not $roleStats.ContainsKey($role)) {
                    $roleStats[$role] = 0
                }
                $roleStats[$role]++
            }
        }
        
        Write-Host "`nUsers by Role:"
        foreach ($role in $roleStats.Keys | Sort-Object) {
            Write-Host "  $role`: $($roleStats[$role])"
        }
        
        # Recent logins
        $recentLogins = $this.Users | Where-Object { $_.LastLogin -and $_.LastLogin -gt (Get-Date).AddDays(-7) }
        Write-Host "`nRecent logins (last 7 days): $($recentLogins.Count)"
        
        # New users
        $newUsers = $this.Users | Where-Object { $_.CreatedDate -gt (Get-Date).AddDays(-30) }
        Write-Host "New users (last 30 days): $($newUsers.Count)"
    }
}

# Usage example
$userManager = [UserManager]::new("users.json")

# Create users
$user1 = $userManager.CreateUser("john.doe", "john@example.com", "John", "Doe")
$user2 = $userManager.CreateUser("jane.smith", "jane@example.com", "Jane", "Smith")
$user3 = $userManager.CreateUser("bob.wilson", "bob@example.com", "Bob", "Wilson")

# Add roles
$user1.AddRole("Admin")
$user2.AddRole("PowerUser")
$user3.AddRole("Support")

# Update login
$user1.UpdateLogin()
$user2.UpdateLogin()

# Set preferences
$user1.SetPreference("Theme", "Dark")
$user2.SetPreference("Language", "fr-FR")

# Generate report
$userManager.GenerateReport()

# Find users
$adminUsers = $userManager.FindUsersByRole("Admin")
Write-Host "`nAdmin users: $($adminUsers.Count)"
$adminUsers | ForEach-Object { Write-Host "  - $($_.GetFullName()) ($($_.Username))" }
```

### Example 3: Data Processing Pipeline

```powershell
class DataProcessor {
    [PSCustomObject]$Config
    [System.Collections.Generic.List[PSCustomObject]]$Data
    
    DataProcessor([hashtable]$config) {
        $this.Config = [PSCustomObject]$config
        $this.Data = New-Object "System.Collections.Generic.List[PSCustomObject]"
    }
    
    [void]LoadData([string]$filePath) {
        try {
            $rawData = Import-Csv $filePath
            foreach ($row in $rawData) {
                $processedRow = $this.ProcessRow($row)
                $this.Data.Add($processedRow)
            }
            Write-Host "Loaded $($this.Data.Count) records from $filePath"
        }
        catch {
            Write-Error "Failed to load data: $($_.Exception.Message)"
        }
    }
    
    [PSCustomObject]ProcessRow([PSCustomObject]$row) {
        return [PSCustomObject]@{
            Id = $row.Id
            Name = $row.Name
            Category = $row.Category
            Value = [double]$row.Value
            Date = [DateTime]$row.Date
            ProcessedDate = Get-Date
            IsValid = $this.ValidateRow($row)
            Score = $this.CalculateScore($row)
        }
    }
    
    [bool]ValidateRow([PSCustomObject]$row) {
        $isValid = $true
        
        if ([string]::IsNullOrEmpty($row.Name)) {
            $isValid = $false
        }
        
        if ($row.Value -and ($row.Value -lt 0 -or $row.Value -gt 1000)) {
            $isValid = $false
        }
        
        try {
            $date = [DateTime]$row.Date
            if ($date -lt (Get-Date).AddYears(-10) -or $date -gt (Get-Date).AddDays(30)) {
                $isValid = $false
            }
        }
        catch {
            $isValid = $false
        }
        
        return $isValid
    }
    
    [double]CalculateScore([PSCustomObject]$row) {
        $score = 0.0
        
        # Base score from value
        if ($row.Value) {
            $score += [math]::Min($row.Value / 100, 10)
        }
        
        # Category bonus
        $categoryScores = @{
            "Premium" = 5
            "Standard" = 3
            "Basic" = 1
        }
        
        if ($categoryScores.ContainsKey($row.Category)) {
            $score += $categoryScores[$row.Category]
        }
        
        # Recency bonus
        try {
            $date = [DateTime]$row.Date
            $daysOld = (Get-Date) - $date
            if ($daysOld.Days -lt 30) {
                $score += 2
            }
            elseif ($daysOld.Days -lt 90) {
                $score += 1
            }
        }
        catch {
            # Invalid date, no bonus
        }
        
        return [math]::Round($score, 2)
    }
    
    [PSCustomObject[]]FilterData([hashtable]$criteria) {
        $filtered = $this.Data
        
        if ($criteria.ContainsKey("Category")) {
            $filtered = $filtered | Where-Object { $_.Category -eq $criteria.Category }
        }
        
        if ($criteria.ContainsKey("MinValue")) {
            $filtered = $filtered | Where-Object { $_.Value -ge $criteria.MinValue }
        }
        
        if ($criteria.ContainsKey("MaxValue")) {
            $filtered = $filtered | Where-Object { $_.Value -le $criteria.MaxValue }
        }
        
        if ($criteria.ContainsKey("IsValid")) {
            $filtered = $filtered | Where-Object { $_.IsValid -eq $criteria.IsValid }
        }
        
        if ($criteria.ContainsKey("MinScore")) {
            $filtered = $filtered | Where-Object { $_.Score -ge $criteria.MinScore }
        }
        
        return $filtered
    }
    
    [hashtable]GetStatistics() {
        $validData = $this.Data | Where-Object { $_.IsValid }
        $invalidData = $this.Data | Where-Object { -not $_.IsValid }
        
        $stats = @{
            TotalRecords = $this.Data.Count
            ValidRecords = $validData.Count
            InvalidRecords = $invalidData.Count
            ValidityRate = if ($this.Data.Count -gt 0) { [math]::Round(($validData.Count / $this.Data.Count) * 100, 2) } else { 0 }
        }
        
        if ($validData.Count -gt 0) {
            $stats.AverageValue = ($validData | Measure-Object -Property Value -Average).Average
            $stats.MaxValue = ($validData | Measure-Object -Property Value -Maximum).Maximum
            $stats.MinValue = ($validData | Measure-Object -Property Value -Minimum).Minimum
            $stats.AverageScore = ($validData | Measure-Object -Property Score -Average).Average
            $stats.MaxScore = ($validData | Measure-Object -Property Score -Maximum).Maximum
        }
        
        # Category breakdown
        $categoryGroups = $validData | Group-Object Category
        $stats.CategoryBreakdown = @{}
        foreach ($group in $categoryGroups) {
            $stats.CategoryBreakdown[$group.Name] = @{
                Count = $group.Count
                AverageValue = ($group.Group | Measure-Object -Property Value -Average).Average
                AverageScore = ($group.Group | Measure-Object -Property Score -Average).Average
            }
        }
        
        return $stats
    }
    
    [void]ExportResults([string]$filePath, [string]$format = "CSV") {
        $validData = $this.Data | Where-Object { $_.IsValid }
        
        try {
            switch ($format.ToUpper()) {
                "CSV" {
                    $validData | Export-Csv -Path $filePath -NoTypeInformation
                }
                "JSON" {
                    $validData | ConvertTo-Json -Depth 3 | Set-Content $filePath
                }
                "XML" {
                    $validData | ConvertTo-Xml | Save-Xml $filePath
                }
                default {
                    throw "Unsupported format: $format"
                }
            }
            Write-Host "Exported $($validData.Count) records to $filePath ($format)"
        }
        catch {
            Write-Error "Failed to export data: $($_.Exception.Message)"
        }
    }
    
    [void]GenerateReport() {
        $stats = $this.GetStatistics()
        
        Write-Host "=== Data Processing Report ==="
        Write-Host "Total Records: $($stats.TotalRecords)"
        Write-Host "Valid Records: $($stats.ValidRecords)"
        Write-Host "Invalid Records: $($stats.InvalidRecords)"
        Write-Host "Validity Rate: $($stats.ValidityRate)%"
        
        if ($stats.ContainsKey("AverageValue")) {
            Write-Host "`nValue Statistics:"
            Write-Host "  Average: $($stats.AverageValue)"
            Write-Host "  Min: $($stats.MinValue)"
            Write-Host "  Max: $($stats.MaxValue)"
            Write-Host "  Average Score: $($stats.AverageScore)"
            Write-Host "  Max Score: $($stats.MaxScore)"
        }
        
        Write-Host "`nCategory Breakdown:"
        foreach ($category in $stats.CategoryBreakdown.Keys) {
            $data = $stats.CategoryBreakdown[$category]
            Write-Host "  $category`: $($data.Count) records, avg value: $($data.AverageValue), avg score: $($data.AverageScore)"
        }
    }
}

# Usage example
$config = @{
    ProcessingDate = Get-Date
    Version = "1.0"
    Thresholds = @{
        MinValue = 0
        MaxValue = 1000
        MinScore = 5
    }
}

$processor = [DataProcessor]::new($config)

# Create sample data
$sampleData = @(
    [PSCustomObject]@{ Id = 1; Name = "Product A"; Category = "Premium"; Value = 150.50; Date = "2023-12-01" },
    [PSCustomObject]@{ Id = 2; Name = "Product B"; Category = "Standard"; Value = 75.25; Date = "2023-11-15" },
    [PSCustomObject]@{ Id = 3; Name = "Product C"; Category = "Basic"; Value = 25.00; Date = "2023-10-20" },
    [PSCustomObject]@{ Id = 4; Name = ""; Category = "Premium"; Value = 200.00; Date = "2023-12-10" },
    [PSCustomObject]@{ Id = 5; Name = "Product D"; Category = "Standard"; Value = -10.00; Date = "2023-12-05" }
)

# Save sample data to CSV
$sampleData | Export-Csv -Path "sample_data.csv" -NoTypeInformation

# Process the data
$processor.LoadData("sample_data.csv")
$processor.GenerateReport()

# Filter data
$filtered = $processor.FilterData(@{ MinScore = 5; IsValid = $true })
Write-Host "`nHigh-quality records: $($filtered.Count)"

# Export results
$processor.ExportResults("processed_data.json", "JSON")
```

## 📝 Exercises

### Exercise 1: Contact Manager
Create a contact management system that:
1. Uses custom objects for contacts
2. Implements CRUD operations
3. Supports serialization to JSON
4. Includes search and filter functionality
5. Has data validation

### Exercise 2: Configuration Builder
Create a configuration system that:
1. Uses nested hashtables for settings
2. Supports different configuration formats
3. Includes validation rules
4. Provides configuration inheritance
5. Supports environment-specific settings

### Exercise 3: Data Transformation Tool
Create a data transformation tool that:
1. Loads data from CSV/JSON
2. Transforms data using custom objects
3. Applies business rules
4. Generates summary statistics
5. Exports in multiple formats

## 🎯 Key Takeaways

- **Hashtables** provide fast key-value lookups
- **PSCustomObject** creates flexible custom objects
- **Classes** provide structured object-oriented programming
- **Serialization** converts objects to/from JSON, XML, CSV
- **Object properties** can be calculated and dynamic
- **Methods** add behavior to objects
- **Inheritance** and **polymorphism** supported in classes
- **Type safety** improves code reliability
- **Object composition** enables complex data structures

## 🔄 Next Steps

Move on to [09-File System Operations](09-file-system.md) to learn about working with files and directories in PowerShell.
