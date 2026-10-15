# Lesson 04 — Infrastructure as Code with Bicep

**Module 06 · Cloud-Native · Lesson 04 of 04**


## Learning objectives

- Understand **infrastructure as code with bicep** in Powershell
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

Infrastructure as Code (IaC) describes cloud resources in files that can be reviewed, versioned, and redeployed. Bicep is Azure's declarative language for this, and PowerShell is the glue: it validates, previews, and deploys templates and feeds them parameters.

## Key concepts

1. **Syntax and structure** — a Bicep file declares resources and parameters; PowerShell runs `New-AzResourceGroupDeployment -TemplateFile -TemplateParameterObject`
2. **Common patterns** — preview with `-WhatIf` (what-if analysis), per-environment parameter hashtables, and reading deployment outputs
3. **Mistakes to avoid** — deploying without a what-if preview, hard-coding names that must be globally unique, and storing secrets in parameter files

## Example

```bicep
// storage.bicep
param name string
param location string = resourceGroup().location

resource sa 'Microsoft.Storage/storageAccounts@2023-05-01' = {
  name: name
  location: location
  sku: { name: 'Standard_LRS' }
  kind: 'StorageV2'
  properties: { minimumTlsVersion: 'TLS1_2' }
}

output endpoint string = sa.properties.primaryEndpoints.blob
```

```ps1
$rg     = 'rg-course-demo'
$suffix = -join ((97..122) | Get-Random -Count 6 | ForEach-Object { [char]$_ })

New-AzResourceGroup -Name $rg -Location 'westeurope' -Force | Out-Null

$params = @{
    ResourceGroupName       = $rg
    TemplateFile            = './storage.bicep'
    TemplateParameterObject = @{ name = "stcourse$suffix" }
}

New-AzResourceGroupDeployment @params -WhatIf          # preview first
$result = New-AzResourceGroupDeployment @params        # then deploy
$result.Outputs.endpoint.Value
```

## Exercise

1. Write a short program that deploys the template to two environments (`dev`, `prod`) using different parameter hashtables.
2. Change one value (such as the SKU) and predict the what-if output before running.
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to Module 07 — Security Engineering.
