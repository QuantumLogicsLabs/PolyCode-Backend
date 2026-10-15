# Lesson 03 — Interactive Tools and Resilient API Clients

**Module 09 · Data, Performance, and AI · Lesson 03 of 04**


## Learning objectives

- Understand **interactive tools and resilient api clients** in Powershell
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

Production scripts call web APIs that fail, throttle, and page their results. This lesson builds a client that retries with backoff, follows pagination, and respects rate limits, then wraps it in a friendly interactive picker for human operators.

## Key concepts

1. **Syntax and structure** — `Invoke-RestMethod -StatusCodeVariable`, `-MaximumRetryCount` / `-RetryIntervalSec`, and `Out-GridView` or `Out-ConsoleGridView` for selection
2. **Common patterns** — exponential backoff on HTTP 429 and 5xx, `Retry-After` headers, following `next` links until done, and a `-WhatIf` switch for write actions
3. **Mistakes to avoid** — retrying non-idempotent POSTs blindly, infinite retry loops, and swallowing the final error

## Example

```ps1
function Invoke-ApiWithRetry {
    param(
        [Parameter(Mandatory)][string]$Uri,
        [int]$MaxAttempts = 4
    )
    for ($i = 1; $i -le $MaxAttempts; $i++) {
        try {
            return Invoke-RestMethod -Uri $Uri -TimeoutSec 15 -ErrorAction Stop
        }
        catch {
            $code = $_.Exception.Response.StatusCode.value__
            $retryable = $code -in 429, 500, 502, 503, 504
            if (-not $retryable -or $i -eq $MaxAttempts) { throw }
            $wait = [math]::Pow(2, $i)          # 2, 4, 8 seconds
            Write-Warning "HTTP $code. Retry $i/$MaxAttempts in ${wait}s"
            Start-Sleep -Seconds $wait
        }
    }
}

# Paginate a public API (GitHub repos)
$all = @()
$uri = 'https://api.github.com/orgs/PowerShell/repos?per_page=30&page=1'
for ($page = 1; $page -le 2; $page++) {
    $all += Invoke-ApiWithRetry -Uri ($uri -replace 'page=\d+', "page=$page")
}

$all | Select-Object name, stargazers_count |
    Sort-Object stargazers_count -Descending |
    Select-Object -First 10 |
    Format-Table -AutoSize

# Let a human choose (needs Microsoft.PowerShell.ConsoleGuiTools)
# $pick = $all | Out-ConsoleGridView -OutputMode Single -Title 'Pick a repo'
```

## Exercise

1. Write a short program that wraps a call to any public API with retry and logs each attempt.
2. Change one value (point the URL at a path that returns 404) and predict whether it retries before running.
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to lesson 04 in this module.
