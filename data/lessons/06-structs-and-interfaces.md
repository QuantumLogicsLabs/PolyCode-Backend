# Lesson 6: Structs And Interfaces

## Structs

Structs let you group related data together.

```go
type User struct {
    Name string
    Age  int
}
```

## Methods

You can attach functions to a type.

```go
func (u User) Greet() string {
    return "Hello, " + u.Name
}
```

## Interfaces

Interfaces describe behavior.

```go
type Speaker interface {
    Speak() string
}
```

If a type has the required methods, it satisfies the interface automatically.

## Practice

- Run `go run ./examples/06-structs-interfaces`
- Add a new struct
- Make it satisfy an interface
