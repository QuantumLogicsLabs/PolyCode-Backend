# Lesson 04 — Static Analysis and Code Quality

**Module 08 · Engineering and Delivery · Lesson 04 of 04**


## Learning objectives

- Understand **static analysis and code quality** in Powershell
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

PSScriptAnalyzer inspects scripts without running them and flags bad habits: aliases in shared code, plain-text passwords, missing `ShouldProcess`, unused variables. A team-wide settings file keeps style consistent and lets CI enforce it.

## Key concepts

1. **Syntax and structure** — `Invoke-ScriptAnalyzer -Path -Recurse -Settings`, rule names such as `PSAvoidUsingPlainTextForPassword`
2. **Common patterns** — a `PSScriptAnalyzerSettings.psd1` committed to the repo, selective suppression with a justification, and `-Fix` for safe auto-corrections
3. **Mistakes to avoid** — suppressing rules without a reason, treating warnings as noise, and linting only on one developer's machine

## Example

```ps1
# PSScriptAnalyzerSettings.psd1
@{
    Severity     = @('Error', 'Warning')
    IncludeRules = @('PSAvoid*', 'PSUse*', 'PSPlaceOpenBrace')
    ExcludeRules = @('PSAvoidUsingWriteHost')
    Rules        = @{
        PSPlaceOpenBrace = @{ Enable = $true; OnSameLine = $true }
    }
}
```

```ps1
# Sample with problems
function Do-Thing {
    param($pw)
    gci C:\temp | % { $_.Name }
    Remove-Item C:\temp\old.txt
}
```

```ps1
Invoke-ScriptAnalyzer -Path ./Do-Thing.ps1 -Settings ./PSScriptAnalyzerSettings.psd1 |
    Select-Object RuleName, Severity, Line, Message |
    Format-Table -AutoSize

# Suppress with a reason, never silently
[Diagnostics.CodeAnalysis.SuppressMessageAttribute(
    'PSAvoidUsingWriteHost', '', Justification = 'Interactive progress banner')]
param()
```

## Exercise

1. Write a short program that analyzes a folder and exits with code 1 when any Error-level finding exists.
2. Change one value (add a rule to `ExcludeRules`) and predict how the finding count changes before running.
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to Module 09 — Data, Performance, and AI.
