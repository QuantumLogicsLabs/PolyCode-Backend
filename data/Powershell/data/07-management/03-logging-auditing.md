# Lesson 03 — Logging and Auditing

**Module 07 · Security Engineering · Lesson 03 of 04**


## Learning objectives

- Understand **logging and auditing** in Powershell
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

You cannot defend or debug what you cannot see. PowerShell can log every script block, module call, and session transcript. This lesson covers enabling that logging and writing your own structured audit trail from automation scripts.

## Key concepts

1. **Syntax and structure** — Script Block Logging (Event ID 4104), Module Logging (4103), and transcription via `Start-Transcript`; configured by Group Policy or registry
2. **Common patterns** — JSON-lines logs with timestamp, user, action, and result; reading events with `Get-WinEvent -FilterHashtable`
3. **Mistakes to avoid** — logging secrets or tokens, logs with no timestamps or correlation ID, and unbounded log files

## Example

```ps1
# Reusable structured logger (one JSON object per line)
function Write-AuditLog {
    param(
        [Parameter(Mandatory)][string]$Action,
        [ValidateSet('Success','Failure')][string]$Result = 'Success',
        [string]$Detail,
        [string]$Path = (Join-Path ([IO.Path]::GetTempPath()) 'audit.jsonl')
    )
    [pscustomobject]@{
        Time   = (Get-Date).ToUniversalTime().ToString('o')
        User   = [Environment]::UserName
        Host   = [Environment]::MachineName
        Action = $Action
        Result = $Result
        Detail = $Detail
    } | ConvertTo-Json -Compress | Add-Content -Path $Path
}

Write-AuditLog -Action 'DisableUser' -Detail 'jdoe'
Write-AuditLog -Action 'DisableUser' -Result Failure -Detail 'user not found: asmith'

# Query suspicious script blocks on Windows (needs Script Block Logging enabled)
# Get-WinEvent -FilterHashtable @{ LogName='Microsoft-Windows-PowerShell/Operational'; Id=4104 } -MaxEvents 20 |
#     Where-Object Message -match 'DownloadString|FromBase64String'
```

## Exercise

1. Write a short program that reads the audit file and counts failures per action.
2. Change one value and predict the output before running.
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to lesson 04 in this module.
