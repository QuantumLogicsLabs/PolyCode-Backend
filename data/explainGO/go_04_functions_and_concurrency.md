# Go Programming Language — Functions & Concurrency

## Functions

### Basic Function
```go
func add(a int, b int) int {
    return a + b
}

result := add(3, 4) // 7
```

### Multiple Return Values
```go
func divide(a, b float64) (float64, error) {
    if b == 0 {
        return 0, fmt.Errorf("cannot divide by zero")
    }
    return a / b, nil
}

result, err := divide(10, 2)
if err != nil {
    log.Fatal(err)
}
fmt.Println(result) // 5
```

### Named Return Values
```go
func minMax(nums []int) (min, max int) {
    min, max = nums[0], nums[0]
    for _, n := range nums {
        if n < min { min = n }
        if n > max { max = n }
    }
    return // naked return
}
```

### Variadic Functions
```go
func sum(nums ...int) int {
    total := 0
    for _, n := range nums {
        total += n
    }
    return total
}

fmt.Println(sum(1, 2, 3, 4, 5)) // 15
```

### First-Class Functions
```go
// Function as variable
greet := func(name string) string {
    return "Hello, " + name
}
fmt.Println(greet("World"))

// Function as argument
apply := func(fn func(int) int, val int) int {
    return fn(val)
}
double := func(x int) int { return x * 2 }
fmt.Println(apply(double, 5)) // 10
```

### Closures
```go
func counter() func() int {
    count := 0
    return func() int {
        count++
        return count
    }
}

c := counter()
fmt.Println(c()) // 1
fmt.Println(c()) // 2
fmt.Println(c()) // 3
```

---

## Concurrency

### Goroutines

A **goroutine** is a lightweight thread managed by the Go runtime. Launch one with the `go` keyword.

```go
func sayHello(name string) {
    fmt.Println("Hello,", name)
}

func main() {
    go sayHello("Alice") // runs concurrently
    go sayHello("Bob")
    time.Sleep(time.Second) // wait for goroutines
}
```

### Channels

Channels allow goroutines to communicate safely.

```go
ch := make(chan int)

go func() {
    ch <- 42 // send value
}()

value := <-ch // receive value
fmt.Println(value) // 42
```

### Buffered Channels
```go
ch := make(chan string, 3)
ch <- "first"
ch <- "second"
ch <- "third"

fmt.Println(<-ch) // first
fmt.Println(<-ch) // second
```

### Select Statement
```go
ch1 := make(chan string)
ch2 := make(chan string)

go func() { ch1 <- "from ch1" }()
go func() { ch2 <- "from ch2" }()

select {
case msg := <-ch1:
    fmt.Println(msg)
case msg := <-ch2:
    fmt.Println(msg)
}
```

### WaitGroups
```go
var wg sync.WaitGroup

for i := 0; i < 5; i++ {
    wg.Add(1)
    go func(id int) {
        defer wg.Done()
        fmt.Printf("Worker %d done\n", id)
    }(i)
}

wg.Wait() // blocks until all goroutines finish
```

### Mutex (Mutual Exclusion)
```go
var mu sync.Mutex
count := 0

for i := 0; i < 1000; i++ {
    go func() {
        mu.Lock()
        count++
        mu.Unlock()
    }()
}
```

---

## Summary

Go's concurrency model — goroutines and channels — is one of its standout features. It is simple to write concurrent programs compared to threads in other languages, making Go ideal for high-performance servers and distributed systems.
