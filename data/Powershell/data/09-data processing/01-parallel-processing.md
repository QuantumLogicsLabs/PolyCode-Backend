# Lesson 01 — Parallel Processing

**Module 09 · Data, Performance, and AI · Lesson 01 of 04**


## Learning objectives

- Understand **parallel processing** in Powershell
- Read and write small examples you can run locally
- Connect this topic to the next lesson in the course

## Overview

When a task spends its time waiting (pinging hosts, calling APIs, reading many files), running items at the same time can turn minutes into seconds. PowerShell 7 adds `ForEach-Object -Parallel` and `ThreadJob`, both far lighter than classic background jobs.

## Key concepts

1. **Syntax and structure** — `-Parallel { }` runs the block per item in its own runspace; `-ThrottleLimit` caps concurrency; outer variables need the `$using:` prefix
2. **Common patterns** — parallel pings, bulk API reads, and collecting results with `[System.Collections.Concurrent.ConcurrentBag[object]]`
3. **Mistakes to avoid** — parallelizing tiny CPU-light work (overhead wins), writing to a shared plain array or hashtable, and unlimited throttle against a rate-limited API

## Example

```ps1
$hosts = 'localhost', '127.0.0.1', 'example.com', 'example.org', 'example.net'

# Sequential baseline
$seq = Measure-Command {
    foreach ($h in $hosts) { Test-Connection -TargetName $h -Count 1 -TimeoutSeconds 2 -ErrorAction SilentlyContinue | Out-Null }
}

# Parallel
$par = Measure-Command {
    $results = $hosts | ForEach-Object -ThrottleLimit 5 -Parallel {
        $r = Test-Connection -TargetName $_ -Count 1 -TimeoutSeconds 2 -ErrorAction SilentlyContinue
        [pscustomobject]@{ Host = $_; Reachable = [bool]$r; Ms = $r.Latency }
    }
}

"Sequential: {0:N1}s   Parallel: {1:N1}s" -f $seq.TotalSeconds, $par.TotalSeconds
$results | Format-Table -AutoSize
```

## Exercise

1. Write a short program that downloads headers from 10 URLs in parallel with a throttle of 3.
2. Change one value (the throttle limit) and predict how total time changes before running.
3. Explain the result in your own words (2–3 sentences).

## Checkpoint

You are ready for the next lesson when you can solve the exercise without copying the example.

---

**Next:** Continue to lesson 02 in this module.
