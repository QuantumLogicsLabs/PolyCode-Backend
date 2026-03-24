# 10 - Modules and Scripts

## 🎯 Learning Objectives

After completing this section, you will understand:
- How to create and organize PowerShell scripts
- Module structure and development
- Script signing and security
- Module publishing and distribution
- Scope and variable management
- Best practices for code organization

## 📄 PowerShell Scripts

Scripts are files containing PowerShell commands that can be executed as a unit.

### Creating Scripts

```powershell
# Basic script structure
# Save as MyScript.ps1

<#
.SYNOPSIS
    Brief description of the script
.DESCRIPTION
    Detailed description of the script functionality
.PARAMETER ParameterName
    Description of the parameter
.EXAMPLE
    .\MyScript.ps1 -ParameterName Value
#>

[CmdletBinding()]
param(
    [Parameter(Mandatory=$true)]
    [string]$Name,
    
    [Parameter()]
    [int]$Count = 1,
    
    [Parameter()]
    [switch]$Verbose
)

# Script body
Write-Host "Hello, $Name!"
Write-Host "Count: $Count"

if ($Verbose) {
    Write-Verbose "Verbose output enabled"
}

# Functions within script
function Get-ScriptInfo {
    param([string]$ScriptName)
    
    return @{
        Name = $ScriptName
        Version = "1.0.0"
        Author = "Your Name"
        Created = Get-Date
    }
}

# Call function
$info = Get-ScriptInfo -ScriptName "MyScript"
Write-Host "Script: $($info.Name) v$($info.Version)"
```

### Script Parameters

```powershell
# Advanced parameter handling
[CmdletBinding()]
param(
    [Parameter(Mandatory=$true, Position=0)]
    [ValidateNotNullOrEmpty()]
    [string]$Server,
    
    [Parameter(Position=1)]
    [ValidateRange(1, 65535)]
    [int]$Port = 8080,
    
    [Parameter()]
    [ValidateSet("HTTP", "HTTPS", "FTP")]
    [string]$Protocol = "HTTP",
    
    [Parameter()]
    [ValidatePattern("^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$")]
    [string]$Email,
    
    [Parameter()]
    [ValidateScript({ Test-Path $_ -PathType Leaf })]
    [string]$ConfigFile,
    
    [Parameter(ValueFromPipeline=$true)]
    [string[]]$Files,
    
    [Parameter()]
    [hashtable]$Options = @{},
    
    [Parameter()]
    [switch]$Force,
    
    [Parameter()]
    [switch]$Recurse
)

begin {
    Write-Verbose "Script starting..."
    $scriptStartTime = Get-Date
}

process {
    # Process each item from pipeline
    foreach ($file in $Files) {
        Write-Host "Processing file: $file"
    }
}

end {
    $duration = (Get-Date) - $scriptStartTime
    Write-Verbose "Script completed in $($duration.TotalSeconds) seconds"
}
```

### Script Execution

```powershell
# Execution policies
Get-ExecutionPolicy  # Check current policy
Set-ExecutionPolicy RemoteSigned -Scope CurrentUser  # Set policy

# Running scripts
.\MyScript.ps1 -Name "John" -Count 5
.\MyScript.ps1 -Name "John" -Count 5 -Verbose

# Pipeline input
"file1.txt", "file2.txt" | .\MyScript.ps1 -Server "localhost"

# Using splatting
$params = @{
    Name = "John"
    Count = 5
    Verbose = $true
}
.\MyScript.ps1 @params

# Dot-sourcing (load functions into current scope)
. .\MyScript.ps1
Get-ScriptInfo -ScriptName "Test"
```

## 📦 PowerShell Modules

Modules are packages of PowerShell functions, cmdlets, and other resources.

### Module Structure

```
MyModule/
├── MyModule.psd1          # Module manifest
├── MyModule.psm1          # Module script
├── Functions/
│   ├── Get-Something.ps1
│   ├── Set-Something.ps1
│   └── Remove-Something.ps1
├── Scripts/
│   ├── Install-MyModule.ps1
│   └── Uninstall-MyModule.ps1
├── Resources/
│   ├── config.json
│   └── template.txt
└── Tests/
    └── MyModule.Tests.ps1
```

### Module Manifest (.psd1)

```powershell
# MyModule.psd1
@{
    # Script module or binary module file associated with this manifest.
    RootModule = 'MyModule.psm1'

    # Version number of this module.
    ModuleVersion = '1.0.0'

    # Supported PSEditions
    # CompatiblePSEditions = @()

    # ID used to uniquely identify this module
    GUID = '12345678-1234-1234-1234-123456789012'

    # Author of this module
    Author = 'Your Name'

    # Company or vendor of this module
    CompanyName = 'Your Company'

    # Copyright statement for this module
    Copyright = '(c) 2023 Your Name. All rights reserved.'

    # Description of the functionality provided by this module
    Description = 'A sample PowerShell module for demonstration purposes.'

    # Minimum version of the PowerShell engine required by this module
    PowerShellVersion = '5.1'

    # Name of the PowerShell host required by this module
    # PowerShellHostName = ''

    # Minimum version of the PowerShell host required by this module
    # PowerShellHostVersion = ''

    # Minimum version of Microsoft .NET Framework required by this module. This prerequisite is valid for the PowerShell Desktop edition only.
    # DotNetFrameworkVersion = ''

    # Minimum version of the common language runtime (CLR) required by this module. This prerequisite is valid for the PowerShell Desktop edition only.
    # CLRVersion = ''

    # Processor architecture (None, X86, Amd64) required by this module
    # ProcessorArchitecture = ''

    # Modules that must be imported into the global environment prior to importing this module
    RequiredModules = @()

    # Assemblies that must be loaded prior to importing this module
    # RequiredAssemblies = @()

    # Script files (.ps1) that are run in the caller's environment prior to importing this module.
    # ScriptsToProcess = @()

    # Type files (.ps1xml) to be loaded when importing this module
    # TypesToProcess = @()

    # Format files (.ps1xml) to be loaded when importing this module
    # FormatsToProcess = @()

    # Modules to import as nested modules of the module specified in RootModule/ModuleToProcess
    # NestedModules = @()

    # Functions to export from this module, for best performance, do not use wildcards and do not delete the entry, use an empty array if there are no functions to export.
    FunctionsToExport = @('Get-Something', 'Set-Something', 'Remove-Something')

    # Cmdlets to export from this module, for best performance, do not use wildcards and do not delete the entry, use an empty array if there are no cmdlets to export.
    CmdletsToExport = @()

    # Variables to export from this module
    VariablesToExport = '*'

    # Aliases to export from this module, for best performance, do not use wildcards and do not delete the entry, use an empty array if there are no aliases to export.
    AliasesToExport = @('gs', 'ss', 'rs')

    # DSC resources to export from this module
    # DscResourcesToExport = @()

    # List of all modules packaged with this module
    # ModuleList = @()

    # List of all files packaged with this module
    # FileList = @()

    # Private data to pass to the module specified in RootModule/ModuleToProcess. This may also contain a PSData hashtable with additional module metadata used by PowerGet and others.
    PrivateData = @{

        PSData = @{

            # Tags applied to this module. These help with module discovery in online galleries.
            Tags = @('Sample', 'Demo', 'Utilities')

            # A URL to the license for this module.
            LicenseUri = 'https://opensource.org/licenses/MIT'

            # A URL to the main website for this project.
            ProjectUri = 'https://github.com/yourusername/MyModule'

            # A URL to an icon representing this module.
            # IconUri = ''

            # ReleaseNotes of this module
            ReleaseNotes = @'
Initial release of MyModule with basic functionality.
'@

        } # End of PSData hashtable

    } # End of PrivateData hashtable

    # HelpInfo URI of this module
    # HelpInfoURI = ''

    # Default prefix for commands exported from this module. Override the default prefix using Import-Module -Prefix.
    # DefaultCommandPrefix = ''
}
```

### Module Script (.psm1)

```powershell
# MyModule.psm1

# Module variables
$Script:ModuleData = @{
    Version = "1.0.0"
    Initialized = $false
}

# Initialize module
$Script:ModuleData.Initialized = $true
Write-Verbose "MyModule initialized"

# Import functions
. $PSScriptRoot\Functions\Get-Something.ps1
. $PSScriptRoot\Functions\Set-Something.ps1
. $PSScriptRoot\Functions\Remove-Something.ps1

# Export aliases
Set-Alias -Name Get-MyData -Value Get-Something
Set-Alias -Name Set-MyData -Value Set-Something
Set-Alias -Name Remove-MyData -Value Remove-Something

# Module cleanup
$MyInvocation.MyCommand.ScriptBlock.Module.OnRemove = {
    Write-Verbose "MyModule is being removed"
    # Cleanup code here
}
```

### Individual Function Files

```powershell
# Functions\Get-Something.ps1
function Get-Something {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory=$true)]
        [string]$Name,
        
        [Parameter()]
        [string]$Filter = "*"
    )
    
    begin {
        Write-Verbose "Starting Get-Something"
    }
    
    process {
        # Function implementation
        $result = [PSCustomObject]@{
            Name = $Name
            Filter = $Filter
            Timestamp = Get-Date
            Value = "Sample data for $Name"
        }
        
        return $result
    }
    
    end {
        Write-Verbose "Completed Get-Something"
    }
}

# Functions\Set-Something.ps1
function Set-Something {
    [CmdletBinding(SupportsShouldProcess=$true)]
    param(
        [Parameter(Mandatory=$true)]
        [string]$Name,
        
        [Parameter(Mandatory=$true)]
        [string]$Value
    )
    
    if ($PSCmdlet.ShouldProcess($Name, "Set value")) {
        # Implementation
        Write-Host "Set $Name to $Value"
        return $true
    }
    
    return $false
}

# Functions\Remove-Something.ps1
function Remove-Something {
    [CmdletBinding(SupportsShouldProcess=$true)]
    param(
        [Parameter(Mandatory=$true, ValueFromPipeline=$true)]
        [string]$Name,
        
        [Parameter()]
        [switch]$Force
    )
    
    process {
        if ($PSCmdlet.ShouldProcess($Name, "Remove")) {
            Write-Host "Removed: $Name"
        }
    }
}
```

## 🔧 Module Management

### Installing and Importing Modules

```powershell
# Find modules
Find-Module -Name "*utility*"
Find-Module -Tag "Database"

# Install modules
Install-Module -Name "PowerShellGet" -Force
Install-Module -Name "PSReadLine" -Scope CurrentUser

# Update modules
Update-Module -Name "PSReadLine"
Update-Module

# List installed modules
Get-Module -ListAvailable
Get-InstalledModule

# Import modules
Import-Module -Name "MyModule"
Import-Module -Path ".\MyModule.psd1"
Import-Module -Name "MyModule" -Prefix "My"

# Remove modules
Remove-Module -Name "MyModule"

# Uninstall modules
Uninstall-Module -Name "MyModule"
```

### Module Development Tools

```powershell
# Test module manifest
Test-ModuleManifest -Path ".\MyModule.psd1"

# Create new module
New-Module -Name "NewModule" -Path ".\NewModule.psm1"

# Build module
function Build-Module {
    param(
        [Parameter(Mandatory=$true)]
        [string]$ModulePath,
        
        [Parameter()]
        [string]$OutputPath = ".\dist"
    )
    
    # Create output directory
    if (-not (Test-Path $OutputPath)) {
        New-Item -Path $OutputPath -ItemType Directory -Force | Out-Null
    }
    
    # Copy module files
    $moduleName = Split-Path $ModulePath -Leaf
    $moduleOutputPath = Join-Path $OutputPath $moduleName
    
    Copy-Item -Path $ModulePath -Destination $moduleOutputPath -Recurse -Force
    
    Write-Host "Module built to: $moduleOutputPath"
}

# Publish module
Publish-Module -Path ".\MyModule" -NuGetApiKey "your-api-key"
```

## 🌍 Scope and Variable Management

Understanding scope is crucial for module and script development.

### Scope Types

```powershell
# Global scope - accessible everywhere
$Global:GlobalVar = "I am global"

# Script scope - accessible within the current script
$Script:ScriptVar = "I am script-scoped"

# Local scope - default scope
$Local:LocalVar = "I am local"

# Private scope - only accessible in current scope
$Private:PrivateVar = "I am private"

# Function scope
function Test-FunctionScope {
    $functionVar = "I am function-scoped"
    $Script:FromFunction = "Set from function"
    Write-Host "Inside function: $functionVar"
}

Test-FunctionScope
Write-Host "From function: $Script:FromFunction"
# Write-Host "Function var: $functionVar"  # This would fail
```

### Module Scope

```powershell
# Module scope demonstration
$moduleVar = "Module scope variable"

function Get-ModuleVar {
    return $moduleVar
}

function Set-ModuleVar {
    param([string]$Value)
    $moduleVar = $Value  # This creates a new local variable
    return $moduleVar
}

function Set-ModuleVarGlobal {
    param([string]$Value)
    $script:moduleVar = $Value  # This modifies the module variable
    return $script:moduleVar
}

# Export module variable
$ExportedVar = "This is exported"
```

### Using Exported Members

```powershell
# In module manifest (.psd1)
VariablesToExport = @('ExportedVar')
FunctionsToExport = @('Get-ModuleVar', 'Set-ModuleVarGlobal')
AliasesToExport = @('gmv')

# In module script (.psm1)
Set-Alias -Name gmv -Value Get-ModuleVar

# When using the module
Import-Module MyModule
$ExportedVar  # Accessible
$moduleVar    # Not accessible (private)
Get-ModuleVar  # Accessible
```

## 🚀 Practical Examples

### Example 1: Configuration Management Module

```powershell
# ConfigManager.psd1
@{
    RootModule = 'ConfigManager.psm1'
    ModuleVersion = '1.0.0'
    GUID = 'abcdef01-2345-6789-abcd-ef0123456789'
    Author = 'Your Name'
    Description = 'Configuration management module'
    PowerShellVersion = '5.1'
    FunctionsToExport = @(
        'Get-Configuration',
        'Set-Configuration',
        'Test-Configuration',
        'Export-Configuration',
        'Import-Configuration'
    )
    VariablesToExport = @()
    AliasesToExport = @('gcfg', 'scfg', 'tcfg')
    PrivateData = @{
        PSData = @{
            Tags = @('Configuration', 'Management', 'Settings')
            LicenseUri = 'https://opensource.org/licenses/MIT'
            ProjectUri = 'https://github.com/yourusername/ConfigManager'
        }
    }
}
```

```powershell
# ConfigManager.psm1
# Configuration Management Module

# Module variables
$Script:DefaultConfigFile = "config.json"
$Script:Configuration = @{}
$Script:ConfigSchema = @{
    Database = @{
        Server = "string"
        Port = "int"
        Name = "string"
        Username = "string"
        Password = "string"
    }
    Logging = @{
        Level = "string"
        File = "string"
        MaxSize = "string"
    }
    Features = @{
        EnableCache = "bool"
        EnableDebug = "bool"
        MaxConnections = "int"
    }
}

# Import functions
. $PSScriptRoot\Functions\Get-Configuration.ps1
. $PSScriptRoot\Functions\Set-Configuration.ps1
. $PSScriptRoot\Functions\Test-Configuration.ps1
. $PSScriptRoot\Functions\Export-Configuration.ps1
. $PSScriptRoot\Functions\Import-Configuration.ps1

# Set aliases
Set-Alias -Name gcfg -Value Get-Configuration
Set-Alias -Name scfg -Value Set-Configuration
Set-Alias -Name tcfg -Value Test-Configuration

# Initialize module
Write-Verbose "ConfigManager module loaded"
```

```powershell
# Functions\Get-Configuration.ps1
function Get-Configuration {
    [CmdletBinding()]
    param(
        [Parameter()]
        [string]$ConfigFile = $Script:DefaultConfigFile,
        
        [Parameter()]
        [string]$Section,
        
        [Parameter()]
        [switch]$Refresh
    )
    
    if ($Refresh -or $Script:Configuration.Count -eq 0) {
        if (Test-Path $ConfigFile) {
            try {
                $content = Get-Content $ConfigFile -Raw | ConvertFrom-Json
                $Script:Configuration = $content
                Write-Verbose "Configuration loaded from $ConfigFile"
            }
            catch {
                Write-Error "Failed to load configuration: $($_.Exception.Message)"
                return $null
            }
        }
        else {
            Write-Warning "Configuration file not found: $ConfigFile"
            return $null
        }
    }
    
    if ($Section) {
        if ($Script:Configuration.ContainsKey($Section)) {
            return $Script:Configuration[$Section]
        }
        else {
            Write-Warning "Configuration section '$Section' not found"
            return $null
        }
    }
    
    return $Script:Configuration
}

# Functions\Set-Configuration.ps1
function Set-Configuration {
    [CmdletBinding(SupportsShouldProcess=$true)]
    param(
        [Parameter(Mandatory=$true)]
        [string]$Section,
        
        [Parameter(Mandatory=$true)]
        [hashtable]$Settings,
        
        [Parameter()]
        [string]$ConfigFile = $Script:DefaultConfigFile,
        
        [Parameter()]
        [switch]$Save
    )
    
    if ($PSCmdlet.ShouldProcess($Section, "Update configuration")) {
        # Load current configuration
        $currentConfig = Get-Configuration -ConfigFile $ConfigFile
        if (-not $currentConfig) {
            $currentConfig = @{}
        }
        
        # Update section
        if (-not $currentConfig.ContainsKey($Section)) {
            $currentConfig[$Section] = @{}
        }
        
        foreach ($key in $Settings.Keys) {
            $currentConfig[$Section][$key] = $Settings[$key]
        }
        
        $Script:Configuration = $currentConfig
        
        # Save if requested
        if ($Save) {
            try {
                $json = $currentConfig | ConvertTo-Json -Depth 4
                $json | Set-Content $ConfigFile
                Write-Verbose "Configuration saved to $ConfigFile"
            }
            catch {
                Write-Error "Failed to save configuration: $($_.Exception.Message)"
            }
        }
        
        return $currentConfig[$Section]
    }
}

# Functions\Test-Configuration.ps1
function Test-Configuration {
    [CmdletBinding()]
    param(
        [Parameter()]
        [string]$ConfigFile = $Script:DefaultConfigFile
    )
    
    $config = Get-Configuration -ConfigFile $ConfigFile
    if (-not $config) {
        return $false
    }
    
    $errors = @()
    
    # Validate against schema
    foreach ($section in $Script:ConfigSchema.Keys) {
        if ($config.ContainsKey($section)) {
            $sectionConfig = $config[$section]
            $schema = $Script:ConfigSchema[$section]
            
            foreach ($key in $schema.Keys) {
                $expectedType = $schema[$key]
                if ($sectionConfig.ContainsKey($key)) {
                    $value = $sectionConfig[$key]
                    $actualType = switch -Wildcard ($value.GetType().Name) {
                        "String*" { "string" }
                        "Int*" { "int" }
                        "Boolean*" { "bool" }
                        default { "unknown" }
                    }
                    
                    if ($actualType -ne $expectedType) {
                        $errors += "Section '$section', key '$key': Expected type '$expectedType', got '$actualType'"
                    }
                }
                else {
                    $errors += "Section '$section', key '$key': Missing required key"
                }
            }
        }
        else {
            $errors += "Missing required section: $section"
        }
    }
    
    if ($errors.Count -gt 0) {
        Write-Warning "Configuration validation failed:"
        $errors | ForEach-Object { Write-Warning "  - $_" }
        return $false
    }
    else {
        Write-Verbose "Configuration validation passed"
        return $true
    }
}

# Functions\Export-Configuration.ps1
function Export-Configuration {
    [CmdletBinding()]
    param(
        [Parameter()]
        [string]$ConfigFile = $Script:DefaultConfigFile,
        
        [Parameter()]
        [ValidateSet("JSON", "XML", "CSV")]
        [string]$Format = "JSON",
        
        [Parameter()]
        [string]$OutputPath
    )
    
    $config = Get-Configuration -ConfigFile $ConfigFile
    if (-not $config) {
        Write-Error "No configuration to export"
        return
    }
    
    $outputFile = if ($OutputPath) { $OutputPath } else { "$ConfigFile.exported.$($Format.ToLower())" }
    
    try {
        switch ($Format) {
            "JSON" {
                $config | ConvertTo-Json -Depth 4 | Set-Content $outputFile
            }
            "XML" {
                $config | ConvertTo-Xml | Save-Xml $outputFile
            }
            "CSV" {
                # Flatten configuration for CSV export
                $flatConfig = @()
                foreach ($section in $config.Keys) {
                    foreach ($key in $config[$section].Keys) {
                        $flatConfig += [PSCustomObject]@{
                            Section = $section
                            Key = $key
                            Value = $config[$section][$key]
                        }
                    }
                }
                $flatConfig | Export-Csv -Path $outputFile -NoTypeInformation
            }
        }
        
        Write-Host "Configuration exported to: $outputFile"
    }
    catch {
        Write-Error "Failed to export configuration: $($_.Exception.Message)"
    }
}

# Functions\Import-Configuration.ps1
function Import-Configuration {
    [CmdletBinding(SupportsShouldProcess=$true)]
    param(
        [Parameter(Mandatory=$true)]
        [string]$ImportFile,
        
        [Parameter()]
        [string]$ConfigFile = $Script:DefaultConfigFile,
        
        [Parameter()]
        [switch]$Merge
    )
    
    if (-not (Test-Path $ImportFile)) {
        Write-Error "Import file not found: $ImportFile"
        return
    }
    
    if ($PSCmdlet.ShouldProcess($ConfigFile, "Import configuration")) {
        try {
            $extension = (Get-Item $ImportFile).Extension.ToLower()
            
            switch ($extension) {
                ".json" {
                    $importedConfig = Get-Content $ImportFile -Raw | ConvertFrom-Json
                }
                ".xml" {
                    $xml = [xml](Get-Content $ImportFile)
                    $importedConfig = $xml.Objects.Object
                }
                ".csv" {
                    $csvData = Import-Csv $ImportFile
                    $importedConfig = @{}
                    
                    foreach ($row in $csvData) {
                        if (-not $importedConfig.ContainsKey($row.Section)) {
                            $importedConfig[$row.Section] = @{}
                        }
                        $importedConfig[$row.Section][$row.Key] = $row.Value
                    }
                }
                default {
                    throw "Unsupported file format: $extension"
                }
            }
            
            if ($Merge) {
                $currentConfig = Get-Configuration -ConfigFile $ConfigFile
                if ($currentConfig) {
                    # Merge configurations
                    foreach ($section in $importedConfig.Keys) {
                        if (-not $currentConfig.ContainsKey($section)) {
                            $currentConfig[$section] = @{}
                        }
                        
                        foreach ($key in $importedConfig[$section].Keys) {
                            $currentConfig[$section][$key] = $importedConfig[$section][$key]
                        }
                    }
                    $importedConfig = $currentConfig
                }
            }
            
            # Save merged/imported configuration
            $json = $importedConfig | ConvertTo-Json -Depth 4
            $json | Set-Content $ConfigFile
            
            # Refresh module configuration
            $Script:Configuration = $importedConfig
            
            Write-Host "Configuration imported from: $ImportFile"
        }
        catch {
            Write-Error "Failed to import configuration: $($_.Exception.Message)"
        }
    }
}
```

### Example 2: Module Build Script

```powershell
# Build-Module.ps1
<#
.SYNOPSIS
    Build script for PowerShell modules
.DESCRIPTION
    Automated build script for creating, testing, and packaging PowerShell modules
#>

[CmdletBinding()]
param(
    [Parameter(Mandatory=$true)]
    [string]$ModulePath,
    
    [Parameter()]
    [string]$OutputPath = ".\dist",
    
    [Parameter()]
    [switch]$RunTests,
    
    [Parameter()]
    [switch]$GenerateDocs,
    
    [Parameter()]
    [switch]$Publish,
    
    [Parameter()]
    [string]$NuGetApiKey
)

# Initialize build
$ErrorActionPreference = "Stop"
$buildStartTime = Get-Date

Write-Host "Starting module build..." -ForegroundColor Green
Write-Host "Module path: $ModulePath"
Write-Host "Output path: $OutputPath"

# Validate module
if (-not (Test-Path $ModulePath)) {
    throw "Module path not found: $ModulePath"
}

$manifestPath = Join-Path $ModulePath "*.psd1"
$manifestFile = Get-ChildItem $manifestPath -ErrorAction Stop

Write-Host "Found manifest: $($manifestFile.Name)"

# Test module manifest
Write-Host "Testing module manifest..."
$manifestTest = Test-ModuleManifest -Path $manifestFile.FullName
Write-Host "Manifest test passed: $($manifestTest.Name) v$($manifestTest.Version)"

# Create output directory
if (Test-Path $OutputPath) {
    Remove-Item -Path $OutputPath -Recurse -Force
}
New-Item -Path $OutputPath -ItemType Directory -Force | Out-Null

# Copy module files
Write-Host "Copying module files..."
$moduleName = $manifestTest.Name
$moduleOutputPath = Join-Path $OutputPath $moduleName

Copy-Item -Path $ModulePath -Destination $moduleOutputPath -Recurse -Force

# Run tests if requested
if ($RunTests) {
    Write-Host "Running tests..."
    $testPath = Join-Path $ModulePath "Tests"
    
    if (Test-Path $testPath) {
        $testFiles = Get-ChildItem -Path $testPath -Filter "*.Tests.ps1"
        
        foreach ($testFile in $testFiles) {
            Write-Host "Running test: $($testFile.Name)"
            & $testFile.FullName
        }
        
        Write-Host "All tests passed!" -ForegroundColor Green
    }
    else {
        Write-Warning "No tests found in: $testPath"
    }
}

# Generate documentation if requested
if ($GenerateDocs) {
    Write-Host "Generating documentation..."
    
    # Import the built module
    Import-Module $moduleOutputPath -Force
    
    # Generate command documentation
    $commands = Get-Command -Module $moduleName
    $docsPath = Join-Path $moduleOutputPath "Docs"
    New-Item -Path $docsPath -ItemType Directory -Force | Out-Null
    
    foreach ($command in $commands) {
        $docFile = Join-Path $docsPath "$($command.Name).md"
        $help = Get-Help $command.Name -Full
        
        $docContent = @"
# $($command.Name)

## Synopsis
$($help.Synopsis)

## Description
$($help.Description)

## Syntax
$($help.Syntax)

## Parameters
$($help.Parameters | ForEach-Object { "- **$($_.Name)**: $($_.Description.text)" })

## Examples
$($help.Examples | ForEach-Object { $_.Example.text })

## Notes
$($help.alertSet.alert.text)
"@
        
        $docContent | Set-Content $docFile
        Write-Host "Generated docs for: $($command.Name)"
    }
    
    Write-Host "Documentation generated in: $docsPath"
}

# Publish if requested
if ($Publish) {
    if (-not $NuGetApiKey) {
        throw "NuGet API key required for publishing"
    }
    
    Write-Host "Publishing module to PowerShell Gallery..."
    Publish-Module -Path $moduleOutputPath -NuGetApiKey $NuGetApiKey -Force
    Write-Host "Module published successfully!" -ForegroundColor Green
}

# Build summary
$buildDuration = (Get-Date) - $buildStartTime

Write-Host "`n=== Build Summary ===" -ForegroundColor Green
Write-Host "Module: $($manifestTest.Name) v$($manifestTest.Version)"
Write-Host "Build time: $($buildDuration.TotalSeconds) seconds"
Write-Host "Output location: $moduleOutputPath"

if ($RunTests) {
    Write-Host "Tests: Passed"
}

if ($GenerateDocs) {
    Write-Host "Documentation: Generated"
}

if ($Publish) {
    Write-Host "Published: Yes"
}

Write-Host "Build completed successfully!" -ForegroundColor Green
```

## 📝 Exercises

### Exercise 1: Create a Utility Module
Create a utility module that includes:
1. File system helper functions
2. String manipulation functions
3. Date/time utilities
4. System information functions
5. Proper module manifest

### Exercise 2: Script to Module Conversion
Convert a complex script into a module:
1. Break script into separate function files
2. Create proper module structure
3. Write module manifest
4. Add parameter validation
5. Include tests

### Exercise 3: Module Distribution Pipeline
Create a build and distribution pipeline:
1. Automated testing script
2. Module build script
3. Documentation generation
4. Version management
5. Publishing automation

## 🎯 Key Takeaways

- **Scripts** are single files with PowerShell commands
- **Modules** are packages of reusable code with structure
- **Manifests** define module metadata and dependencies
- **Scope** controls variable and function visibility
- **Export controls** determine what's available to users
- **Testing** ensures module reliability
- **Documentation** makes modules usable by others
- **Versioning** manages module updates
- **Publishing** shares modules with the community

## 🔄 Next Steps

Move on to [11-Error Handling](11-error-handling.md) to learn about robust error management in PowerShell.
