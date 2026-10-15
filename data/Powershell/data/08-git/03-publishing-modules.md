# Lesson 03 — Packaging and Publishing Modules

**Module 08 · Engineering and Delivery · Lesson 03 of 04**


## Learning objectives

- Understand **packaging and publishing modules** in Powershell
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

A module turns loose scripts into a versioned, installable product. This lesson covers the manifest, a clean folder layout, semantic versioning, and publishing to the PowerShell Gallery or a private feed with PSResourceGet.

## Key concepts

1. **Syntax and structure** — a `.psm1` file plus a `.psd1` manifest created with `New-ModuleManifest`; export only public functions
2. **Common patterns** — `Public/` and `Private/` folders dot-sourced by the root module, semantic versioning (MAJOR.MINOR.PATCH), and `Publish-PSResource`
3. **Mistakes to avoid** — `FunctionsToExport = '*'` (slows import and leaks internals), forgetting to bump the version, and publishing an API key in the repository

## Example

```ps1
# Layout
# MyTools/
#   MyTools.psd1
#   MyTools.psm1
#   Public/Get-DiskReport.ps1
#   Private/Format-Size.ps1

# MyTools.psm1
$public  = Get-ChildItem -Path "$PSScriptRoot/Public/*.ps1"  -ErrorAction SilentlyContinue
$private = Get-ChildItem -Path "$PSScriptRoot/Private/*.ps1" -ErrorAction SilentlyContinue
foreach ($f in @($private) + @($public)) { . $f.FullName }
Export-ModuleMember -Function $public.BaseName
```

```ps1
New-ModuleManifest -Path ./MyTools/MyTools.psd1 `
    -RootModule 'MyTools.psm1' `
    -ModuleVersion '1.0.0' `
    -Author 'Your Name' `
    -Description 'Helpful admin tools' `
    -PowerShellVersion '7.2' `
    -FunctionsToExport @('Get-DiskReport')

Test-ModuleManifest ./MyTools/MyTools.psd1

# Install-PSResource Microsoft.PowerShell.PSResourceGet   (once)
# Publish-PSResource -Path ./MyTools -Repository PSGallery -ApiKey (Get-Secret GalleryKey -AsPlainText)
```

## Exercise

1. Write a short program that builds a two-function module and imports it with `Import-Module -Force`.
2. Change one value (bump the version to 1.1.0) and predict what `Get-Module MyTools` shows before running.
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to lesson 04 in this module.
