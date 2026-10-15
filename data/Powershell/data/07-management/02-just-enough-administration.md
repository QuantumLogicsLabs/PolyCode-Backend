# Lesson 02 — Just Enough Administration (JEA)

**Module 07 · Security Engineering · Lesson 02 of 04**


## Learning objectives

- Understand **just enough administration (jea)** in Powershell
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

JEA lets people run only specific commands through a remoting endpoint, using a temporary virtual account instead of a full administrator login. A help-desk user can restart one service without ever holding admin rights. JEA is Windows PowerShell remoting based.

## Key concepts

1. **Syntax and structure** — a **role capability file** (`.psrc`) lists what is allowed; a **session configuration file** (`.pssc`) maps groups to roles
2. **Common patterns** — allow named cmdlets with constrained parameters, run as a virtual account, and review the transcripts JEA writes
3. **Mistakes to avoid** — allowing `Invoke-Expression` or wildcard cmdlets, exposing cmdlets that can launch arbitrary processes, and skipping testing with `Get-PSSessionCapability`

## Example

```ps1
# 1. Role capability: help desk may restart only the Spooler service
$roleDir = 'C:\Program Files\WindowsPowerShell\Modules\HelpDeskJEA\RoleCapabilities'
New-Item -ItemType Directory -Path $roleDir -Force | Out-Null

New-PSRoleCapabilityFile -Path "$roleDir\HelpDesk.psrc" -VisibleCmdlets @(
    @{ Name = 'Restart-Service'; Parameters = @{ Name = 'Name'; ValidateSet = 'Spooler' } },
    'Get-Service'
)

# 2. Session configuration: map a group to the role
New-PSSessionConfigurationFile -Path .\HelpDesk.pssc `
    -SessionType RestrictedRemoteServer `
    -RunAsVirtualAccount `
    -TranscriptDirectory 'C:\JEA\Transcripts' `
    -RoleDefinitions @{ 'CONTOSO\HelpDeskUsers' = @{ RoleCapabilities = 'HelpDesk' } }

Register-PSSessionConfiguration -Name HelpDesk -Path .\HelpDesk.pssc -Force

# 3. Verify what a user can do
Get-PSSessionCapability -ConfigurationName HelpDesk -Username 'CONTOSO\jdoe'
```

## Exercise

1. Write a short program that creates a role allowing `Get-Process` only (no `Stop-Process`).
2. Change one value and predict whether a connected user can run `Stop-Process` before testing.
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to lesson 03 in this module.
