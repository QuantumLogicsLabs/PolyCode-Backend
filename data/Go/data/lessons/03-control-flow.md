# Lesson 3: Control Flow

## If Statements

Use `if` to make decisions.

```go
if age >= 18 {
    fmt.Println("Adult")
} else {
    fmt.Println("Minor")
}
```

## For Loops

Go uses `for` for looping.

```go
for i := 1; i <= 5; i++ {
    fmt.Println(i)
}
```

## Switch

`switch` is useful when one value can lead to many branches.

```go
switch day {
case "Monday":
    fmt.Println("Start")
case "Friday":
    fmt.Println("Almost weekend")
default:
    fmt.Println("Normal day")
}
```

## Practice

- Run `go run ./examples/03-control-flow`
- Change the loop range
- Add a new `switch` case
