# Lesson 01 — Microsoft Graph Automation

**Module 06 · Cloud-Native · Lesson 01 of 04**


## Learning objectives

- Understand **microsoft graph automation** in Powershell
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

Microsoft Graph is the single REST API behind Microsoft 365 and Entra ID (users, groups, mail, Teams, devices). The Microsoft.Graph PowerShell SDK wraps it in cmdlets, replacing the retired AzureAD and MSOnline modules. This lesson shows how to connect with least-privilege scopes and query tenant data.

## Key concepts

1. **Syntax and structure** — `Connect-MgGraph -Scopes` requests only the permissions you need; every cmdlet maps to a Graph endpoint (`Get-MgUser` → `/users`)
2. **Common patterns** — filtering server-side with `-Filter`, selecting fields with `-Property`, paging with `-All`, and calling raw endpoints with `Invoke-MgGraphRequest`
3. **Mistakes to avoid** — requesting `*.ReadWrite.All` when read is enough, filtering client-side with `Where-Object` on large tenants, and forgetting `-All` so only 100 results return

## Example

```ps1
# Install once: Install-Module Microsoft.Graph.Users -Scope CurrentUser
Connect-MgGraph -Scopes 'User.Read.All'

# Server-side filter + only the properties we need
$users = Get-MgUser -All `
    -Filter "accountEnabled eq true and startsWith(displayName,'A')" `
    -Property Id, DisplayName, UserPrincipalName, CreatedDateTime

$users |
    Select-Object DisplayName, UserPrincipalName, CreatedDateTime |
    Sort-Object DisplayName |
    Format-Table -AutoSize

# Raw endpoint call for anything without a dedicated cmdlet
$org = Invoke-MgGraphRequest -Method GET -Uri 'v1.0/organization'
$org.value[0].displayName

Disconnect-MgGraph
```

## Exercise

1. Write a short program that lists all enabled users created in the last 30 days.
2. Change one value (the number of days or the scope) and predict the output before running.
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to lesson 02 in this module.
