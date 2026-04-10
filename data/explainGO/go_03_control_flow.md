# Go Programming Language — Control Flow

## If / Else

```go
age := 20

if age >= 18 {
    fmt.Println("Adult")
} else if age >= 13 {
    fmt.Println("Teenager")
} else {
    fmt.Println("Child")
}
```

### If with Initialization Statement
```go
if score := getScore(); score > 90 {
    fmt.Println("Excellent!")
} else {
    fmt.Println("Keep trying.")
}
// score is not accessible here
```

---

## For Loop

Go has **only one loop keyword**: `for`. It replaces `while` and `do-while` too.

### Traditional For Loop
```go
for i := 0; i < 5; i++ {
    fmt.Println(i)
}
```

### While-style Loop
```go
n := 1
for n < 100 {
    n *= 2
}
```

### Infinite Loop
```go
for {
    // runs forever
    break // use break to exit
}
```

### Range Loop
```go
fruits := []string{"apple", "banana", "cherry"}

for index, value := range fruits {
    fmt.Printf("%d: %s\n", index, value)
}

// Ignore index
for _, value := range fruits {
    fmt.Println(value)
}

// Range over a map
scores := map[string]int{"Alice": 90, "Bob": 85}
for name, score := range scores {
    fmt.Printf("%s: %d\n", name, score)
}
```

---

## Switch

```go
day := "Monday"

switch day {
case "Saturday", "Sunday":
    fmt.Println("Weekend!")
case "Monday":
    fmt.Println("Back to work.")
default:
    fmt.Println("Weekday")
}
```

### Switch without condition (acts like if-else chain)
```go
x := 42
switch {
case x < 0:
    fmt.Println("Negative")
case x == 0:
    fmt.Println("Zero")
case x > 0:
    fmt.Println("Positive")
}
```

### Fallthrough
```go
switch 2 {
case 1:
    fmt.Println("One")
    fallthrough
case 2:
    fmt.Println("Two")
    fallthrough
case 3:
    fmt.Println("Three") // This also prints
}
```

---

## Break and Continue

```go
for i := 0; i < 10; i++ {
    if i == 5 {
        break // exits the loop
    }
    if i%2 == 0 {
        continue // skips even numbers
    }
    fmt.Println(i)
}
```

---

## Defer

`defer` schedules a function call to run **after the surrounding function returns**. Deferred calls are stacked (LIFO).

```go
func main() {
    defer fmt.Println("Third")
    defer fmt.Println("Second")
    fmt.Println("First")
}
// Output:
// First
// Second
// Third
```

### Common Use: Closing Resources
```go
func readFile(path string) {
    f, _ := os.Open(path)
    defer f.Close() // guaranteed to run

    // read file...
}
```

---

## Goto (rarely used)

```go
i := 0
loop:
    if i < 5 {
        fmt.Println(i)
        i++
        goto loop
    }
```

---

## Summary

Go's control flow is deliberately minimal. With just `if`, `for`, `switch`, and `defer`, Go covers all practical needs without complexity, making code easier to follow and maintain.
