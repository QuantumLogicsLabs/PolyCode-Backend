# Lesson 5: Arrays, Slices, And Maps

## Arrays

Arrays have a fixed size.

```go
var numbers [3]int = [3]int{10, 20, 30}
```

## Slices

Slices are more flexible and are used much more often than arrays.

```go
scores := []int{90, 85, 100}
scores = append(scores, 95)
```

## Maps

Maps store key-value pairs.

```go
student := map[string]int{
    "math":    95,
    "science": 88,
}
```

## Practice

- Run `go run ./examples/05-collections`
- Add values to a slice
- Create your own map for book titles and ratings
