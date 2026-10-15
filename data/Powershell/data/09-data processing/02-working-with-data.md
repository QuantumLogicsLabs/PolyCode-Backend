# Lesson 02 — Working with Data: JSON, CSV, and SQL

**Module 09 · Data, Performance, and AI · Lesson 02 of 04**


## Learning objectives

- Understand **working with data: json, csv, and sql** in Powershell
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

Automation is mostly moving data between systems. This lesson turns PowerShell's object pipeline into a small ETL (extract, transform, load) tool: read CSV, reshape with calculated properties, group and summarize, then output JSON or load a SQLite/SQL database with parameterized queries.

## Key concepts

1. **Syntax and structure** — `Import-Csv`, `ConvertFrom-Json -Depth`, `Group-Object`, `Measure-Object`, calculated properties `@{ N=''; E={} }`
2. **Common patterns** — typed conversion (`[datetime]`, `[decimal]`), summarizing per group, and the SimplySql or SqlServer modules for database work
3. **Mistakes to avoid** — string-building SQL (injection risk; always use parameters), leaving numbers as strings so sorting is wrong, and loading huge files fully into memory when streaming works

## Example

```ps1
# Create sample data
@'
Date,Region,Product,Amount
2026-01-05,East,Widget,120.50
2026-01-09,West,Widget,80.00
2026-02-02,East,Gadget,200.25
2026-02-11,West,Gadget,150.75
2026-02-20,East,Widget,99.99
'@ | Set-Content sales.csv

$sales = Import-Csv sales.csv | ForEach-Object {
    [pscustomobject]@{
        Date    = [datetime]$_.Date
        Region  = $_.Region
        Product = $_.Product
        Amount  = [decimal]$_.Amount
    }
}

$summary = $sales | Group-Object Region | ForEach-Object {
    [pscustomobject]@{
        Region = $_.Name
        Orders = $_.Count
        Total  = ($_.Group | Measure-Object Amount -Sum).Sum
        Avg    = [math]::Round(($_.Group | Measure-Object Amount -Average).Average, 2)
    }
}

$summary | Sort-Object Total -Descending | Format-Table -AutoSize
$summary | ConvertTo-Json | Set-Content summary.json

# Parameterized SQL (pattern, using SimplySql: Install-Module SimplySql)
# Open-SQLiteConnection -DataSource ./sales.db
# Invoke-SqlUpdate -Query 'INSERT INTO sales(region,amount) VALUES (@r,@a)' -Parameters @{ r='East'; a=120.50 }
```

## Exercise

1. Write a short program that reports total sales per month and per product.
2. Change one value (a row's amount) and predict which totals change before running.
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to lesson 03 in this module.
