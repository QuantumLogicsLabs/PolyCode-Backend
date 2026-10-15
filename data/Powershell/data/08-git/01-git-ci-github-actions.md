# Lesson 01 — Git and CI with GitHub Actions

**Module 08 · Engineering and Delivery · Lesson 01 of 04**


## Learning objectives

- Understand **git and ci with github actions** in Powershell
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

Scripts that matter belong in version control and get tested automatically on every change. GitHub Actions runners include PowerShell 7 on Windows, Linux, and macOS, so a short workflow can lint and test your code on all three.

## Key concepts

1. **Syntax and structure** — a workflow YAML file in `.github/workflows/`, with `shell: pwsh` steps
2. **Common patterns** — a build matrix across operating systems, caching modules, and failing the build when tests or analysis fail
3. **Mistakes to avoid** — hard-coding secrets in YAML (use repository secrets), running only on one OS, and ignoring exit codes

## Example

```yaml
# .github/workflows/ci.yml
name: ci
on: [push, pull_request]

jobs:
  test:
    strategy:
      matrix:
        os: [ubuntu-latest, windows-latest]
    runs-on: ${{ matrix.os }}
    steps:
      - uses: actions/checkout@v4
      - name: Install tools
        shell: pwsh
        run: |
          Install-Module Pester -MinimumVersion 5.5 -Force -Scope CurrentUser
          Install-Module PSScriptAnalyzer -Force -Scope CurrentUser
      - name: Analyze
        shell: pwsh
        run: |
          $r = Invoke-ScriptAnalyzer -Path . -Recurse -Severity Error,Warning
          $r | Format-Table
          if ($r) { exit 1 }
      - name: Test
        shell: pwsh
        run: Invoke-Pester -Path ./tests -CI
```

```ps1
# Local workflow before you push
git switch -c feature/add-report
git add .
git commit -m "Add disk report function"
git push -u origin feature/add-report
```

## Exercise

1. Write a short program (a function plus one Pester test) and a workflow that runs it on push.
2. Change one value so a test fails and predict whether the build goes red before pushing.
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to lesson 02 in this module.
