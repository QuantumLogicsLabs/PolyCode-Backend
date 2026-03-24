# 01 - PowerShell Basics

## 🎯 Learning Objectives

After completing this section, you will understand:
- What PowerShell is and why it's powerful
- The PowerShell console and ISE/VS Code
- Basic cmdlets and their syntax
- The PowerShell pipeline
- Getting help in PowerShell

## 📚 What is PowerShell?

PowerShell is a powerful command-line shell and scripting language built on the .NET framework. Unlike traditional shells, PowerShell:

- Works with **objects** rather than text
- Uses **verb-noun** naming convention for commands
- Has a consistent **syntax** across all commands
- Provides **remoting** capabilities
- Integrates with **Windows and cross-platform** systems

## 🖥️ PowerShell Console

### Starting PowerShell

**Windows PowerShell (built-in):**
- Press `Win + X` and select "Windows PowerShell"
- Search for "PowerShell" in Start Menu
- Run `powershell` from Command Prompt

**PowerShell 7+ (recommended):**
- Download from [GitHub](https://github.com/PowerShell/PowerShell/releases)
- Run `pwsh` from any terminal

### Console Basics

```powershell
# Check PowerShell version
$PSVersionTable

# Clear the screen
Clear-Host  # or alias 'cls'

# Exit PowerShell
Exit  # or alias 'exit'
```

## 📝 Cmdlets: The Building Blocks

Cmdlets (pronounced "command-lets") are the basic commands in PowerShell. They follow a **Verb-Noun** naming convention:

### Common Verbs
- `Get` - Retrieve information
- `Set` - Modify or set something
- `New` - Create something new
- `Remove` - Delete something
- `Start` - Begin a process
- `Stop` - End a process
- `Test` - Check something

### Common Nouns
- `Process` - Running programs
- `Service` - System services
- `File` - Files
- `Item` - Items (files, folders, registry keys)
- `Content` - File contents

### Basic Cmdlets Examples

```powershell
# Get information about running processes
Get-Process

# Get services
Get-Service

# Get files in current directory
Get-ChildItem

# Get content of a file
Get-Content -Path "filename.txt"

# Start a process
Start-Process notepad.exe

# Stop a process
Stop-Process -Name "notepad"
```

## 🔄 The PowerShell Pipeline

The pipeline (`|`) allows you to pass objects from one cmdlet to another:

```powershell
# Get all processes and sort by CPU usage
Get-Process | Sort-Object CPU -Descending

# Get running services and filter for running ones
Get-Service | Where-Object Status -eq "Running"

# Get files and select specific properties
Get-ChildItem | Select-Object Name, Length, LastWriteTime

# Count the number of running processes
Get-Process | Measure-Object
```

## 📖 Getting Help

PowerShell has excellent built-in help system:

```powershell
# Basic help for a cmdlet
Get-Help Get-Process

# Detailed help with examples
Get-Help Get-Process -Detailed

# Full help with all examples
Get-Help Get-Process -Full

# Show only examples
Get-Help Get-Process -Examples

# Online help (opens in browser)
Get-Help Get-Process -Online

# Find cmdlets for a specific task
Get-Command *process*

# Get help about parameters
Get-Help Get-Process -Parameter Name
```

## 🔍 Discovery Commands

### Finding Commands

```powershell
# Find all cmdlets with "service" in the name
Get-Command *service*

# Find all cmdlets that get something
Get-Command -Verb Get

# Find all cmdlets that work with processes
Get-Command -Noun Process

# Get all available modules
Get-Module -ListAvailable
```

### Exploring Objects

```powershell
# Get properties and methods of an object
Get-Process | Get-Member

# Get specific properties
Get-Process | Select-Object Name, Id, CPU

# Get the first 5 processes
Get-Process | Select-Object -First 5

# Sort processes by memory usage
Get-Process | Sort-Object WorkingSet -Descending
```

## 🎮 Aliases

PowerShell provides aliases for common commands:

```powershell
# Common aliases
ls      # Get-ChildItem
dir     # Get-ChildItem
cls     # Clear-Host
cat     # Get-Content
type    # Get-Content
kill    # Stop-Process
ps      # Get-Process
echo    # Write-Output

# See all aliases
Get-Alias

# Find what a cmdlet is aliased to
Get-Alias ls

# Find aliases for a cmdlet
Get-Alias -Definition Get-ChildItem
```

## 📁 Working with Paths

```powershell
# Current location
Get-Location  # or 'pwd'

# Change directory
Set-Location C:\Users  # or 'cd'

# Go up one level
Set-Location ..  # or 'cd ..'

# Go to home directory
Set-Location ~  # or 'cd ~'

# Show environment variables
Get-ChildItem Env:
```

## 🎯 Practical Examples

### Example 1: System Information Gathering

```powershell
# Get basic system information
$computerInfo = Get-ComputerInfo
$computerInfo.WindowsProductName
$computerInfo.TotalPhysicalMemory

# Get disk information
Get-Volume | Select-Object DriveLetter, Size, SizeRemaining

# Get network adapters
Get-NetAdapter | Where-Object Status -eq "Up"
```

### Example 2: Process Management

```powershell
# Find high CPU usage processes
Get-Process | Where-Object CPU -gt 10 | Sort-Object CPU -Descending

# Find large memory processes
Get-Process | Where-Object WorkingSet -gt 100MB | Sort-Object WorkingSet -Descending

# Kill processes by name
Get-Process | Where-Object ProcessName -like "*chrome*" | Stop-Process
```

### Example 3: Service Management

```powershell
# Get all running services
Get-Service | Where-Object Status -eq "Running"

# Start a service
Start-Service -Name "Spooler"

# Stop a service
Stop-Service -Name "Spooler"

# Get service status
Get-Service -Name "Spooler" | Select-Object Name, Status, StartType
```

## 🚀 Best Practices

1. **Use full cmdlet names** in scripts (avoid aliases)
2. **Use the pipeline** to chain commands efficiently
3. **Get help** before using new cmdlets
4. **Use `Select-Object`** to get only the properties you need
5. **Use `Where-Object`** to filter results early
6. **Test commands interactively** before putting them in scripts

## 📝 Exercises

### Exercise 1: Basic Commands
1. Get a list of all running processes
2. Sort the processes by memory usage (highest first)
3. Display only the name and memory usage
4. Count how many processes are running

### Exercise 2: Service Exploration
1. Get all services on your system
2. Filter to show only stopped services
3. Sort by service name
4. Display the first 10 stopped services

### Exercise 3: File System
1. Get all files in your Documents folder
2. Filter to show only files larger than 1MB
3. Sort by file size (largest first)
4. Display name and size

## 🎯 Key Takeaways

- PowerShell works with **objects**, not text
- Commands follow **Verb-Noun** convention
- The **pipeline (`|`)** passes objects between commands
- Use `Get-Help` to learn about any command
- **Aliases** exist but use full names in scripts
- PowerShell is **cross-platform** with PowerShell 7+

## 🔄 Next Steps

Move on to [02-Variables and Data Types](02-variables-and-data-types.md) to learn about storing and working with data in PowerShell.
