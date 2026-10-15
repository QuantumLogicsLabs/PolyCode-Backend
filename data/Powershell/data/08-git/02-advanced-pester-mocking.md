# Lesson 02 — Advanced Pester and Mocking

**Module 08 · Engineering and Delivery · Lesson 02 of 04**


## Learning objectives

- Understand **advanced pester and mocking** in Powershell
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

Good tests run without touching real servers, disks, or clouds. Pester's `Mock` replaces commands with controlled fakes, so you can test failure paths (network down, user missing) that are hard to trigger for real.

## Key concepts

1. **Syntax and structure** — `Describe` / `Context` / `It`, `BeforeAll`, `Mock`, `Should -Invoke`, and `-ParameterFilter`
2. **Common patterns** — mock the boundary (API, AD, file system), assert it was called with the right parameters, and use `TestDrive:` for temporary files
3. **Mistakes to avoid** — mocking the function under test, tests that depend on each other's order, and asserting only the happy path

## Example

```ps1
# Get-ServiceReport.ps1
function Get-ServiceReport {
    param([string[]]$Name)
    foreach ($n in $Name) {
        try {
            $s = Get-Service -Name $n -ErrorAction Stop
            [pscustomobject]@{ Name = $n; Status = $s.Status; Error = $null }
        }
        catch {
            [pscustomobject]@{ Name = $n; Status = 'Unknown'; Error = $_.Exception.Message }
        }
    }
}
```

```ps1
# Get-ServiceReport.Tests.ps1
BeforeAll { . $PSScriptRoot/Get-ServiceReport.ps1 }

Describe 'Get-ServiceReport' {
    It 'reports a running service' {
        Mock Get-Service { [pscustomobject]@{ Status = 'Running' } }
        (Get-ServiceReport -Name 'Spooler').Status | Should -Be 'Running'
        Should -Invoke Get-Service -Times 1 -ParameterFilter { $Name -eq 'Spooler' }
    }

    It 'captures the error when a service is missing' {
        Mock Get-Service { throw 'Service not found' }
        $r = Get-ServiceReport -Name 'Nope'
        $r.Status | Should -Be 'Unknown'
        $r.Error  | Should -Match 'not found'
    }

    It 'writes output to a temporary drive' {
        Set-Content -Path 'TestDrive:\out.txt' -Value 'ok'
        Get-Content 'TestDrive:\out.txt' | Should -Be 'ok'
    }
}
```

Run with `Invoke-Pester -Output Detailed`.

## Exercise

1. Write a short program that adds a `-Computer` parameter and a test that mocks `Invoke-Command`.
2. Change one value and predict which `It` block fails before running.
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to lesson 03 in this module.
