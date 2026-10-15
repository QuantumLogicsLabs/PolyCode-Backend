# Lesson 04 — Code Signing and Execution Trust

**Module 07 · Security Engineering · Lesson 04 of 04**


## Learning objectives

- Understand **code signing and execution trust** in Powershell
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

A digital signature proves who wrote a script and that it has not changed since. Combined with the `AllSigned` execution policy or App Control for Business, signing lets an organization run only trusted automation. Execution policy is a safety net against mistakes, not a security boundary on its own.

## Key concepts

1. **Syntax and structure** — `Get-AuthenticodeSignature`, `Set-AuthenticodeSignature`, and a code-signing certificate with a timestamp server
2. **Common patterns** — sign in the release pipeline, verify before running, and use a self-signed certificate only for lab practice
3. **Mistakes to avoid** — treating `RemoteSigned` as protection against a malicious local user, signing without a timestamp (signature dies with the cert), and storing the private key on a shared server

## Example

```ps1
# LAB ONLY: create a self-signed code-signing certificate (Windows)
$cert = New-SelfSignedCertificate -Type CodeSigningCert `
    -Subject 'CN=PolyCode Lab Signing' `
    -CertStoreLocation Cert:\CurrentUser\My

# Sign a script
Set-Content -Path .\hello-signed.ps1 -Value 'Write-Host "Signed hello"'
Set-AuthenticodeSignature -FilePath .\hello-signed.ps1 -Certificate $cert |
    Select-Object Status, StatusMessage

# Verify before running anything you did not just write
$sig = Get-AuthenticodeSignature -FilePath .\hello-signed.ps1
if ($sig.Status -eq 'Valid') { & .\hello-signed.ps1 }
else { Write-Warning "Refusing to run: $($sig.Status)" }
```

Note: a self-signed certificate shows `UnknownError` / not trusted until it is added to the Trusted Publishers store; that is exactly the behavior you want to observe.

## Exercise

1. Write a short program that verifies every `.ps1` in a folder and lists those that are not `Valid`.
2. Change one value (edit one signed file by a single character) and predict the new status before running.
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to Module 08 — Engineering and Delivery.
