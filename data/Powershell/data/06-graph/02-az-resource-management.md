# Lesson 02 — Azure Resource Management with Az

**Module 06 · Cloud-Native · Lesson 02 of 04**


## Learning objectives

- Understand **azure resource management with az** in Powershell
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

The Az module is the standard way to manage Azure from PowerShell. Beyond one-off commands, real automation tags resources, queries across subscriptions, and cleans up cost leaks. This lesson builds a tagging-audit script that finds untagged resources.

## Key concepts

1. **Syntax and structure** — `Connect-AzAccount`, `Set-AzContext`, and `Get-Az*` / `New-Az*` / `Remove-Az*` verbs follow the same pipeline rules as any cmdlet
2. **Common patterns** — tag audits, `-WhatIf` on destructive calls, and Azure Resource Graph (`Search-AzGraph`) for fast cross-subscription queries
3. **Mistakes to avoid** — running against the wrong subscription (always check `Get-AzContext`), deleting without `-WhatIf` first, and looping `Get-AzResource` per resource group on large estates

## Example

```ps1
# Install once: Install-Module Az.Accounts, Az.Resources -Scope CurrentUser
Connect-AzAccount
Get-AzContext | Select-Object Name, Subscription   # confirm where you are

$required = 'Owner', 'CostCenter'

$report = Get-AzResource | ForEach-Object {
    $missing = $required | Where-Object { -not $_.Tags -or -not $_.Tags.ContainsKey($_) }
    if ($missing) {
        [pscustomobject]@{
            Name          = $_.Name
            ResourceGroup = $_.ResourceGroupName
            Type          = $_.ResourceType
            MissingTags   = $missing -join ', '
        }
    }
}

$report | Export-Csv -Path ./untagged-resources.csv -NoTypeInformation
"{0} resources are missing required tags." -f @($report).Count
```

## Exercise

1. Write a short program that tags every resource in one resource group with `Environment = Dev` using `Update-AzTag -Operation Merge`.
2. Change one value and predict the output before running (use `-WhatIf` first).
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to lesson 03 in this module.
