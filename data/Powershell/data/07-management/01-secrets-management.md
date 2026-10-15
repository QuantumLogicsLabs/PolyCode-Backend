# Lesson 01 — Secrets Management

**Module 07 · Security Engineering · Lesson 01 of 04**


## Learning objectives

- Understand **secrets management** in Powershell
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

Passwords and API keys in scripts are one of the most common security failures. The Microsoft.PowerShell.SecretManagement module gives scripts one interface for many vaults (local SecretStore, Azure Key Vault, KeePass), so code never contains the secret itself.

## Key concepts

1. **Syntax and structure** — `Register-SecretVault`, `Set-Secret`, `Get-Secret`; secrets come back as `SecureString`, `PSCredential`, hashtable, or text
2. **Common patterns** — a local SecretStore for development and Key Vault for production, with identical `Get-Secret` calls in both
3. **Mistakes to avoid** — `ConvertTo-SecureString -AsPlainText` on literals, writing secrets to logs, and committing `.env` files to Git

## Example

```ps1
# Install once:
# Install-Module Microsoft.PowerShell.SecretManagement, Microsoft.PowerShell.SecretStore -Scope CurrentUser
Register-SecretVault -Name LocalStore -ModuleName Microsoft.PowerShell.SecretStore -DefaultVault

# Store (prompts securely; nothing is written into the script)
Set-Secret -Name 'DemoApiKey' -Secret (Read-Host 'API key' -AsSecureString)

# Use
$key = Get-Secret -Name 'DemoApiKey'                 # SecureString
$plain = $key | ConvertFrom-SecureString -AsPlainText # only at the moment of use

Invoke-RestMethod -Uri 'https://example.com/api/items' `
    -Headers @{ Authorization = "Bearer $plain" }

Remove-Variable plain
```

## Exercise

1. Write a short program that reads a credential from the vault and uses it with `Invoke-RestMethod -Authentication Bearer -Token`.
2. Change one value (the secret name) and predict the error message before running.
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to lesson 02 in this module.
