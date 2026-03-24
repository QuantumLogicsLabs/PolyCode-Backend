# Collections in Go

This directory contains comprehensive examples of Go's collection types and operations.

## Files

- **main.go** - Basic collections examples
- **maps.go** - Map operations and utilities
- **slices-advanced.go** - Advanced slice operations
- **arrays.go** - Array operations and comparisons
- **structs-collections.go** - Structs as collections
- **README.md** - This file

## Collection Types Covered

### Arrays
- Fixed-size collections
- Value semantics
- Multi-dimensional arrays
- Performance considerations

### Slices
- Dynamic collections
- Slice operations (append, copy, slice)
- Advanced operations (filter, map, reduce)
- Sorting and searching
- Chunking, flattening, rotating

### Maps
- Key-value collections
- Map literals and operations
- Iteration and filtering
- Nested maps
- Maps as sets

### Structs as Collections
- Slices of structs
- Maps of structs
- Nested structs
- Struct methods
- Pointers to structs

## Key Features Demonstrated

### Array Features
```go
// Fixed size array
var numbers [5]int

// Multi-dimensional array
matrix := [3][4]int{{1, 2, 3, 4}, {5, 6, 7, 8}, {9, 10, 11, 12}}

// Array operations
sum := 0
for _, value := range numbers {
    sum += value
}
```

### Slice Features
```go
// Dynamic slice
numbers := []int{1, 2, 3, 4, 5}

// Advanced operations
even := filter(numbers, func(x int) bool { return x%2 == 0 })
doubled := mapSlice(numbers, func(x int) int { return x * 2 })
sum := reduce(numbers, 0, func(acc, x int) int { return acc + x })
```

### Map Features
```go
// Map operations
studentAges := make(map[string]int)
studentAges["Alice"] = 21

// Check existence
age, exists := studentAges["Alice"]

// Iteration
for name, age := range studentAges {
    fmt.Printf("%s: %d\n", name, age)
}
```

### Struct Collection Features
```go
// Slice of structs
people := []Person{
    {Name: "Alice", Age: 30},
    {Name: "Bob", Age: 25},
}

// Filtering
over30 := filterPeople(people, func(p Person) bool {
    return p.Age > 30
})
```

## Advanced Operations

### Slice Algorithms
- Chunking and flattening
- Reversing and rotating
- Shuffling
- Unique elements
- Set operations (intersection, union, difference)

### Map Utilities
- Counting occurrences
- Filtering and transforming
- Merging and inverting
- Using maps as sets

### Struct Operations
- Sorting by different fields
- Grouping and filtering
- Finding and updating
- Pointer operations

## Running the Examples

```bash
go run main.go
go run maps.go
go run slices-advanced.go
go run arrays.go
go run structs-collections.go
```

## Performance Considerations

### Arrays vs Slices
- **Arrays**: Fixed size, value type, better performance for known sizes
- **Slices**: Dynamic size, reference type, more flexible

### Maps
- O(1) average case for lookups
- Good for key-value associations
- Can be used as sets

### Structs in Collections
- Organized data storage
- Methods for behavior
- Pointers for efficiency

## Best Practices

1. **Use slices for dynamic collections**
2. **Use arrays when size is known and fixed**
3. **Use maps for key-value lookups**
4. **Organize related data in structs**
5. **Consider performance implications**
6. **Use appropriate data structures for the problem**

## Common Patterns

### Filter-Map-Reduce Pipeline
```go
result := reduce(
    mapSlice(
        filter(data, predicate),
        transform,
    ),
    initial,
    accumulator,
)
```

### Map as Set
```go
set := make(map[string]bool)
for _, item := range items {
    set[item] = true
}
```

### Struct Collection Operations
```go
// Sort by field
sort.Slice(people, func(i, j int) bool {
    return people[i].Age < people[j].Age
})

// Filter by condition
filtered := filterPeople(people, func(p Person) bool {
    return p.Age > 25
})
```

## Exercises

1. Create a function that removes duplicates from a slice
2. Implement a map that counts word frequencies
3. Build a struct collection with sorting capabilities
4. Create a slice utility library
5. Implement set operations using maps
