# 07 - Arrays and Collections

## 🎯 Learning Objectives

After completing this section, you will understand:
- How to create and manipulate arrays
- Different types of arrays and their uses
- Array operations and methods
- Working with lists and other collections
- Performance considerations for large collections
- Best practices for array usage

## 📋 Basic Arrays

Arrays are ordered collections of items that can be accessed by index.

### Creating Arrays

```powershell
# Simple array creation
$numbers = 1, 2, 3, 4, 5
$fruits = "Apple", "Banana", "Cherry"
$mixed = 1, "Hello", $true, 3.14

# Using array subexpression operator
$numbers = @(1, 2, 3, 4, 5)
$empty = @()

# Range operator for numeric arrays
$range = 1..10
$reverseRange = 10..1

# Creating arrays with specific types
[int[]]$intArray = 1, 2, 3, 4, 5
[string[]]$stringArray = "one", "two", "three"
[bool[]]$boolArray = $true, $false, $true

# Single-element arrays (comma is required)
$singleElement = ,42
$singleString = ,"Hello"
```

### Accessing Array Elements

```powershell
$fruits = "Apple", "Banana", "Cherry", "Date", "Elderberry"

# Access by index (0-based)
$first = $fruits[0]      # "Apple"
$third = $fruits[2]      # "Cherry"

# Negative indexing (from end)
$last = $fruits[-1]      # "Elderberry"
$secondLast = $fruits[-2] # "Date"

# Multiple elements
$firstTwo = $fruits[0,1] # "Apple", "Banana"
$selected = $fruits[0,2,4] # "Apple", "Cherry", "Elderberry"

# Range of elements
$slice = $fruits[1..3]   # "Banana", "Cherry", "Date"
$lastThree = $fruits[-3..-1] # "Cherry", "Date", "Elderberry"
```

### Array Properties

```powershell
$numbers = 1, 2, 3, 4, 5, 6, 7, 8, 9, 10

# Basic properties
$numbers.Count      # 10
$numbers.Length     # 10
$numbers.Rank       # 1 (dimensions)
$numbers.LongLength  # 10 (for large arrays)

# Check if array is empty
$emptyArray = @()
$emptyArray.Count -eq 0  # $true

# Check if array contains a value
$numbers -contains 5     # $true
$numbers -contains 99    # $false

# Array dimensions
$multiDim = @(@(1,2), @(3,4), @(5,6))
$multiDim.Rank       # 2
$multiDim.Length     # 3 (number of rows)
$multiDim[0].Length  # 2 (number of columns)
```

## 🔄 Array Operations

### Modifying Arrays

```powershell
# Arrays are immutable in PowerShell - operations create new arrays
$numbers = 1, 2, 3, 4, 5

# Add elements (creates new array)
$numbers = $numbers + 6      # 1, 2, 3, 4, 5, 6
$numbers += 7                # Same as above

# Add multiple elements
$numbers += 8, 9, 10

# Remove elements (filtering)
$numbers = $numbers | Where-Object { $_ -ne 5 }  # Remove 5

# Replace elements
$numbers[0] = 100            # Replace first element

# Insert elements (requires creating new array)
$insertIndex = 2
$numbers = $numbers[0..($insertIndex-1)] + 99 + $numbers[$insertIndex..($numbers.Length-1)]
```

### Array Methods

```powershell
$fruits = "Apple", "Banana", "Cherry", "Date"

# ForEach method
$fruits.ForEach({ Write-Host "Fruit: $_" })

# Where method (filtering)
$longFruits = $fruits.Where({ $_.Length -gt 5 })

# Sort array
$sorted = $fruits.Sort()  # Note: This sorts in place
$numbers = 5, 2, 8, 1, 9
$sortedNumbers = $numbers | Sort-Object

# Reverse array
$reversed = $fruits[-1..-($fruits.Length)]

# Join array elements
$joined = $fruits -join ", "  # "Apple, Banana, Cherry, Date"
$joinedWithSpace = $fruits -join " "

# Split string to array
$text = "Apple,Banana,Cherry"
$array = $text -split ","  # "Apple", "Banana", "Cherry"
```

### Array Filtering and Selection

```powershell
$numbers = 1..20

# Where-Object filtering
$even = $numbers | Where-Object { $_ % 2 -eq 0 }
$greaterThan10 = $numbers | Where-Object { $_ -gt 10 }

# Complex filtering
$divisibleBy3Or5 = $numbers | Where-Object { ($_ % 3 -eq 0) -or ($_ % 5 -eq 0) }

# Select-Object
$first5 = $numbers | Select-Object -First 5
$last5 = $numbers | Select-Object -Last 5
$skip5 = $numbers | Select-Object -Skip 5
$unique = 1, 2, 2, 3, 3, 3, 4 | Select-Object -Unique

# Select-Object with calculated properties
$processes = Get-Process | Select-Object -First 5 | Select-Object ProcessName, @{
    Name = "MemoryMB"
    Expression = { [math]::Round($_.WorkingSet / 1MB, 2) }
}
```

## 📚 Multi-dimensional Arrays

### 2D Arrays

```powershell
# Creating 2D arrays
$matrix = @(
    @(1, 2, 3),
    @(4, 5, 6),
    @(7, 8, 9)
)

# Accessing elements
$element = $matrix[1][2]  # 6 (second row, third column)
$firstRow = $matrix[0]   # @(1, 2, 3)

# Iterating through 2D array
for ($i = 0; $i -lt $matrix.Length; $i++) {
    for ($j = 0; $j -lt $matrix[$i].Length; $j++) {
        Write-Host "[$i][$j] = $($matrix[$i][$j])"
    }
}

# Jagged arrays (arrays with different lengths)
$jagged = @(
    @(1, 2),
    @(3, 4, 5),
    @(6, 7, 8, 9)
)
```

### Array Operations with Matrices

```powershell
# Matrix addition
function Add-Matrices {
    param($MatrixA, $MatrixB)
    
    $result = @()
    for ($i = 0; $i -lt $MatrixA.Length; $i++) {
        $row = @()
        for ($j = 0; $j -lt $MatrixA[$i].Length; $j++) {
            $row += $MatrixA[$i][$j] + $MatrixB[$i][$j]
        }
        $result += ,$row
    }
    return $result
}

$matrixA = @(@(1, 2), @(3, 4))
$matrixB = @(@(5, 6), @(7, 8))
$sum = Add-Matrices -MatrixA $matrixA -MatrixB $matrixB

# Transpose matrix
function Transpose-Matrix {
    param($Matrix)
    
    $rows = $Matrix.Length
    $cols = $Matrix[0].Length
    
    $result = @()
    for ($j = 0; $j -lt $cols; $j++) {
        $newRow = @()
        for ($i = 0; $i -lt $rows; $i++) {
            $newRow += $Matrix[$i][$j]
        }
        $result += ,$newRow
    }
    return $result
}
```

## 🗂️ Lists and Generic Collections

PowerShell supports .NET collections for better performance with large datasets.

### ArrayList

```powershell
# Creating ArrayList (better for frequent modifications)
$list = New-Object System.Collections.ArrayList

# Add items
$list.Add("Apple") | Out-Null
$list.Add("Banana") | Out-Null
$list.AddRange(("Cherry", "Date")) | Out-Null

# Insert at specific position
$list.Insert(1, "Orange") | Out-Null

# Remove items
$list.Remove("Banana") | Out-Null
$list.RemoveAt(0) | Out-Null

# Convert back to array if needed
$array = $list.ToArray()
```

### Generic List

```powershell
# Generic List with type safety
[stringList] = New-Object "System.Collections.Generic.List[string]"
[intList] = New-Object "System.Collections.Generic.List[int]"

# Add items
$stringList.Add("Hello")
$stringList.Add("World")
$stringList.AddRange(("PowerShell", "Scripting"))

# Check if contains
if ($stringList.Contains("PowerShell")) {
    Write-Host "Found PowerShell!"
}

# Find items
$found = $stringList.Find({ param($item) $item -like "Power*" })

# Sort list
$stringList.Sort()

# Convert to array
$stringArray = $stringList.ToArray()
```

### Other Collections

```powershell
# Stack (LIFO - Last In, First Out)
$stack = New-Object System.Collections.Stack
$stack.Push("First")
$stack.Push("Second")
$stack.Push("Third")
$top = $stack.Pop()  # "Third"

# Queue (FIFO - First In, First Out)
$queue = New-Object System.Collections.Queue
$queue.Enqueue("First")
$queue.Enqueue("Second")
$queue.Enqueue("Third")
$first = $queue.Dequeue()  # "First"

# HashSet (unique items only)
$hashSet = New-Object "System.Collections.Generic.HashSet[string]"
$hashSet.Add("Apple") | Out-Null
$hashSet.Add("Apple") | Out-Null  # Won't add duplicate
$hashSet.Add("Banana") | Out-Null

# Dictionary (key-value pairs)
$dictionary = New-Object "System.Collections.Generic.Dictionary[string, int]"
$dictionary.Add("Apple", 5) | Out-Null
$dictionary.Add("Banana", 3) | Out-Null
$appleCount = $dictionary["Apple"]
```

## 🚀 Practical Examples

### Example 1: Data Processing Pipeline

```powershell
function Process-SalesData {
    param([array]$RawData)
    
    Write-Host "Processing $($RawData.Count) records..."
    
    # Step 1: Filter valid records
    $validRecords = $RawData | Where-Object { 
        $_.Sales -gt 0 -and 
        $_.Price -gt 0 -and 
        $_.Product -ne $null 
    }
    
    Write-Host "Valid records: $($validRecords.Count)"
    
    # Step 2: Calculate totals
    $processedData = $validRecords | ForEach-Object {
        $total = $_.Sales * $_.Price
        [PSCustomObject]@{
            Product = $_.Product
            Sales = $_.Sales
            Price = $_.Price
            Total = $total
            Category = if ($total -gt 1000) { "High" } 
                      elseif ($total -gt 500) { "Medium" } 
                      else { "Low" }
        }
    }
    
    # Step 3: Sort by total
    $sortedData = $processedData | Sort-Object Total -Descending
    
    # Step 4: Group by category
    $categoryGroups = $sortedData | Group-Object Category
    
    # Step 5: Create summary
    $summary = $categoryGroups | ForEach-Object {
        $group = $_
        $totalSales = ($group.Group | Measure-Object -Property Total -Sum).Sum
        $avgSales = ($group.Group | Measure-Object -Property Total -Average).Average
        
        [PSCustomObject]@{
            Category = $group.Name
            Count = $group.Count
            TotalSales = [math]::Round($totalSales, 2)
            AverageSales = [math]::Round($avgSales, 2)
            Percentage = [math]::Round(($group.Count / $sortedData.Count) * 100, 1)
        }
    }
    
    return @{
        ProcessedData = $sortedData
        Summary = $summary | Sort-Object TotalSales -Descending
        Statistics = [PSCustomObject]@{
            TotalRecords = $sortedData.Count
            TotalRevenue = ($sortedData | Measure-Object -Property Total -Sum).Sum
            AverageRevenue = ($sortedData | Measure-Object -Property Total -Average).Average
            MaxRevenue = ($sortedData | Measure-Object -Property Total -Maximum).Maximum
            MinRevenue = ($sortedData | Measure-Object -Property Total -Minimum).Minimum
        }
    }
}

# Test data
$salesData = @(
    @{ Product = "Laptop"; Sales = 10; Price = 999.99 },
    @{ Product = "Mouse"; Sales = 50; Price = 29.99 },
    @{ Product = "Keyboard"; Sales = 30; Price = 79.99 },
    @{ Product = "Monitor"; Sales = 15; Price = 299.99 },
    @{ Product = "Headphones"; Sales = 25; Price = 199.99 },
    @{ Product = "Webcam"; Sales = 20; Price = 89.99 },
    @{ Product = "Laptop"; Sales = 8; Price = 1099.99 },
    @{ Product = "Tablet"; Sales = 12; Price = 499.99 }
)

$result = Process-SalesData -RawData $salesData

Write-Host "`n=== Sales Summary ==="
$result.Summary | ForEach-Object {
    Write-Host "$($_.Category): $($_.Count) items, $($_.TotalSales):C total ($($_.Percentage)%)"
}

Write-Host "`n=== Statistics ==="
Write-Host "Total Revenue: $($result.Statistics.TotalRevenue):C"
Write-Host "Average Revenue: $($result.Statistics.AverageRevenue):C"
Write-Host "Max Revenue: $($result.Statistics.MaxRevenue):C"
Write-Host "Min Revenue: $($result.Statistics.MinRevenue):C"
```

### Example 2: Inventory Management System

```powershell
class InventoryItem {
    [string]$SKU
    [string]$Name
    [int]$Quantity
    [double]$Price
    [string]$Category
    [DateTime]$LastUpdated
    
    InventoryItem([string]$sku, [string]$name, [int]$quantity, [double]$price, [string]$category) {
        $this.SKU = $sku
        $this.Name = $name
        $this.Quantity = $quantity
        $this.Price = $price
        $this.Category = $category
        $this.LastUpdated = Get-Date
    }
    
    [void]UpdateQuantity([int]$newQuantity) {
        $this.Quantity = $newQuantity
        $this.LastUpdated = Get-Date
    }
    
    [double]GetTotalValue() {
        return $this.Quantity * $this.Price
    }
}

class InventoryManager {
    [System.Collections.Generic.List[InventoryItem]]$Items
    
    InventoryManager() {
        $this.Items = New-Object "System.Collections.Generic.List[InventoryItem]"
    }
    
    [void]AddItem([InventoryItem]$item) {
        $existingItem = $this.Items | Where-Object { $_.SKU -eq $item.SKU }
        if ($existingItem) {
            $existingItem.UpdateQuantity($existingItem.Quantity + $item.Quantity)
        } else {
            $this.Items.Add($item)
        }
    }
    
    [InventoryItem]FindItem([string]$sku) {
        return $this.Items | Where-Object { $_.SKU -eq $sku }
    }
    
    [InventoryItem[]]FindItemsByCategory([string]$category) {
        return $this.Items | Where-Object { $_.Category -eq $category }
    }
    
    [InventoryItem[]]GetLowStockItems([int]$threshold = 10) {
        return $this.Items | Where-Object { $_.Quantity -lt $threshold }
    }
    
    [hashtable]GetCategorySummary() {
        $categories = $this.Items | Group-Object Category
        $summary = @{}
        
        foreach ($category in $categories) {
            $items = $category.Group
            $totalValue = ($items | ForEach-Object { $_.GetTotalValue() } | Measure-Object -Sum).Sum
            $totalQuantity = ($items | Measure-Object -Property Quantity -Sum).Sum
            
            $summary[$category.Name] = @{
                Count = $items.Count
                TotalQuantity = $totalQuantity
                TotalValue = $totalValue
                AveragePrice = ($items | Measure-Object -Property Price -Average).Average
            }
        }
        
        return $summary
    }
    
    [void]GenerateReport() {
        Write-Host "=== Inventory Report ==="
        Write-Host "Total Items: $($this.Items.Count)"
        Write-Host "Total Value: $(($this.Items | ForEach-Object { $_.GetTotalValue() } | Measure-Object -Sum).Sum):C"
        Write-Host "Last Updated: $(Get-Date)"
        
        Write-Host "`n=== Low Stock Items ==="
        $lowStock = $this.GetLowStockItems()
        if ($lowStock.Count -gt 0) {
            $lowStock | ForEach-Object {
                Write-Host "$($_.SKU) - $($_.Name): $($_.Quantity) units"
            }
        } else {
            Write-Host "No low stock items"
        }
        
        Write-Host "`n=== Category Summary ==="
        $summary = $this.GetCategorySummary()
        foreach ($category in $summary.Keys) {
            $data = $summary[$category]
            Write-Host "$category: $($data.Count) items, $($data.TotalQuantity) units, $($data.TotalValue):C"
        }
    }
}

# Usage example
$inventory = [InventoryManager]::new()

# Add items
$inventory.AddItem([InventoryItem]::new("LAP001", "Laptop", 15, 999.99, "Electronics"))
$inventory.AddItem([InventoryItem]::new("MOU001", "Mouse", 50, 29.99, "Electronics"))
$inventory.AddItem([InventoryItem]::new("KEY001", "Keyboard", 8, 79.99, "Electronics"))
$inventory.AddItem([InventoryItem]::new("DES001", "Desk", 5, 299.99, "Furniture"))
$inventory.AddItem([InventoryItem]::new("CHA001", "Chair", 12, 199.99, "Furniture"))

# Generate report
$inventory.GenerateReport()

# Find specific items
$laptop = $inventory.FindItem("LAP001")
if ($laptop) {
    Write-Host "`nLaptop Details: $($laptop.Name), $($laptop.Quantity) units, $($laptop.GetTotalValue()):C total value"
}

# Get low stock items
$lowStock = $inventory.GetLowStockItems(10)
Write-Host "`nLow Stock Items (less than 10): $($lowStock.Count)"
```

### Example 3: Array Performance Comparison

```powershell
function Compare-ArrayPerformance {
    param([int]$ItemCount = 10000)
    
    Write-Host "Performance comparison with $ItemCount items"
    
    # Test 1: Array vs ArrayList for adding items
    Write-Host "`n=== Test 1: Adding Items ==="
    
    $stopwatch = [System.Diagnostics.Stopwatch]::StartNew()
    $array = @()
    for ($i = 0; $i -lt $ItemCount; $i++) {
        $array += "Item $i"
    }
    $stopwatch.Stop()
    Write-Host "Array (+=): $($stopwatch.ElapsedMilliseconds) ms"
    
    $stopwatch.Restart()
    $arrayList = New-Object System.Collections.ArrayList
    for ($i = 0; $i -lt $ItemCount; $i++) {
        $arrayList.Add("Item $i") | Out-Null
    }
    $stopwatch.Stop()
    Write-Host "ArrayList: $($stopwatch.ElapsedMilliseconds) ms"
    
    $stopwatch.Restart()
    $genericList = New-Object "System.Collections.Generic.List[string]"
    for ($i = 0; $i -lt $ItemCount; $i++) {
        $genericList.Add("Item $i")
    }
    $stopwatch.Stop()
    Write-Host "Generic List: $($stopwatch.ElapsedMilliseconds) ms"
    
    # Test 2: Searching items
    Write-Host "`n=== Test 2: Searching ==="
    
    $searchItem = "Item 5000"
    
    $stopwatch.Restart()
    $found = $array -contains $searchItem
    $stopwatch.Stop()
    Write-Host "Array -contains: $($stopwatch.ElapsedTicks) ticks"
    
    $stopwatch.Restart()
    $found = $arrayList.Contains($searchItem)
    $stopwatch.Stop()
    Write-Host "ArrayList Contains: $($stopwatch.ElapsedTicks) ticks"
    
    $stopwatch.Restart()
    $found = $genericList.Contains($searchItem)
    $stopwatch.Stop()
    Write-Host "Generic List Contains: $($stopwatch.ElapsedTicks) ticks"
    
    # Test 3: Filtering
    Write-Host "`n=== Test 3: Filtering ==="
    
    $stopwatch.Restart()
    $filtered = $array | Where-Object { $_ -like "*5*" }
    $stopwatch.Stop()
    Write-Host "Array Where-Object: $($stopwatch.ElapsedMilliseconds) ms"
    
    $stopwatch.Restart()
    $filtered = $arrayList.Where({ param($item) $item -like "*5*" })
    $stopwatch.Stop()
    Write-Host "ArrayList Where: $($stopwatch.ElapsedMilliseconds) ms"
    
    $stopwatch.Restart()
    $filtered = $genericList.FindAll({ param($item) $item -like "*5*" })
    $stopwatch.Stop()
    Write-Host "Generic List FindAll: $($stopwatch.ElapsedMilliseconds) ms"
    
    # Test 4: Memory usage
    Write-Host "`n=== Test 4: Memory Usage ==="
    
    $arraySize = [System.GC]::GetTotalMemory($true)
    $testArray = @()
    for ($i = 0; $i -lt 1000; $i++) {
        $testArray += "This is a test string with some content $i"
    }
    $arrayAfterSize = [System.GC]::GetTotalMemory($true)
    $arrayMemory = $arrayAfterSize - $arraySize
    
    $listSize = [System.GC]::GetTotalMemory($true)
    $testList = New-Object "System.Collections.Generic.List[string]"
    for ($i = 0; $i -lt 1000; $i++) {
        $testList.Add("This is a test string with some content $i")
    }
    $listAfterSize = [System.GC]::GetTotalMemory($true)
    $listMemory = $listAfterSize - $listSize
    
    Write-Host "Array memory: $([math]::Round($arrayMemory / 1KB, 2)) KB"
    Write-Host "Generic List memory: $([math]::Round($listMemory / 1KB, 2)) KB"
    
    # Cleanup
    $testArray = $null
    $testList = $null
    [System.GC]::Collect()
}

# Run performance comparison
Compare-ArrayPerformance -ItemCount 5000
```

## 📝 Exercises

### Exercise 1: Array Manipulation
Create functions to:
1. Reverse an array without using built-in reverse
2. Remove duplicates from an array
3. Merge two sorted arrays into one sorted array
4. Find the intersection of two arrays

### Exercise 2: Data Analysis
Create a script that:
1. Loads sample data into arrays
2. Calculates statistics (mean, median, mode)
3. Filters data based on multiple criteria
4. Groups data by categories
5. Generates summary reports

### Exercise 3: Performance Test
Create a performance comparison script that:
1. Tests different collection types
2. Measures add, remove, search operations
3. Compares memory usage
4. Provides recommendations for different use cases

## 🎯 Key Takeaways

- **Arrays** are fixed-size collections (PowerShell creates new arrays when modifying)
- **ArrayList** and **Generic Lists** are better for frequent modifications
- **Multi-dimensional arrays** support matrix operations
- **Performance** varies significantly between collection types
- **Memory usage** should be considered for large datasets
- **Filtering** and **sorting** are common operations
- **Generic collections** provide type safety and better performance
- **Choose the right collection** based on your use case

## 🔄 Next Steps

Move on to [08-Hashtables and Objects](08-hashtables-and-objects.md) to learn about key-value pairs and custom objects.
