# Lesson 03 — Containers and Cross-Platform PowerShell 7

**Module 06 · Cloud-Native · Lesson 03 of 04**


## Learning objectives

- Understand **containers and cross-platform powershell 7** in Powershell
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

PowerShell 7 runs on Windows, Linux, and macOS, which means the same script can run on a laptop, a CI runner, and inside a container. This lesson covers writing portable scripts and packaging them in a Docker image.

## Key concepts

1. **Syntax and structure** — the automatic variables `$IsWindows`, `$IsLinux`, `$IsMacOS`; `Join-Path` and `[IO.Path]::DirectorySeparatorChar` instead of hard-coded `\`
2. **Common patterns** — a small image based on `mcr.microsoft.com/powershell`, running a script with `pwsh -NoProfile -File`, and passing config through environment variables
3. **Mistakes to avoid** — Windows-only cmdlets (`Get-WmiObject`, registry paths), assuming case-insensitive paths on Linux, and baking secrets into an image

## Example

```ps1
# portable-info.ps1
param([string]$OutDir = (Join-Path ([IO.Path]::GetTempPath()) 'reports'))

New-Item -ItemType Directory -Path $OutDir -Force | Out-Null

$os = if ($IsWindows) { 'Windows' } elseif ($IsLinux) { 'Linux' } else { 'macOS' }

$info = [pscustomobject]@{
    OS         = $os
    PSVersion  = $PSVersionTable.PSVersion.ToString()
    Host       = [Environment]::MachineName
    Collected  = (Get-Date).ToUniversalTime().ToString('o')
}

$info | ConvertTo-Json | Set-Content -Path (Join-Path $OutDir 'info.json')
$info
```

```dockerfile
# Dockerfile
FROM mcr.microsoft.com/powershell:latest
WORKDIR /app
COPY portable-info.ps1 .
ENTRYPOINT ["pwsh", "-NoProfile", "-File", "./portable-info.ps1"]
```

Build and run: `docker build -t ps-info .` then `docker run --rm ps-info`.

## Exercise

1. Write a short program that reports free disk space in a way that works on both Windows and Linux.
2. Change one value and predict the output before running (for example the output directory).
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to lesson 04 in this module.
