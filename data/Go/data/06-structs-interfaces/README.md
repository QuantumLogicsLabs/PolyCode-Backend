# Structs and Interfaces in Go

This directory contains comprehensive examples of Go's struct and interface features, including advanced OOP concepts.

## Files

- **main.go** - Basic structs and interfaces examples
- **interfaces.go** - Interface definitions and implementations
- **methods.go** - Method definitions and receivers
- **embedding.go** - Struct embedding and composition
- **polymorphism.go** - Polymorphism and interface-based design
- **README.md** - This file

## Concepts Covered

### Structs
- Basic struct definition and usage
- Field access and modification
- Struct literals and initialization
- Pointer vs value semantics
- Method definitions on structs

### Interfaces
- Interface definition and implementation
- Interface satisfaction
- Type assertions and type switches
- Empty interface (`interface{}`)
- Interface composition

### Methods
- Value receiver methods
- Pointer receiver methods
- Method chaining
- Method promotion
- Method expressions and values

### Embedding
- Struct embedding for composition
- Method promotion
- Name conflict resolution
- Interface embedding
- Anonymous struct embedding

### Polymorphism
- Interface-based polymorphism
- Dynamic dispatch
- Type assertions for specific behavior
- Strategy pattern
- Factory pattern

## Key Features Demonstrated

### Basic Struct Usage
```go
type Person struct {
    Name string
    Age  int
}

person := Person{Name: "Alice", Age: 30}
fmt.Printf("Name: %s, Age: %d\n", person.Name, person.Age)
```

### Interface Definition
```go
type Shape interface {
    Area() float64
    Perimeter() float64
}

type Rectangle struct {
    Width  float64
    Height float64
}

func (r Rectangle) Area() float64 {
    return r.Width * r.Height
}
```

### Method Receivers
```go
// Value receiver
func (r Rectangle) IsSquare() bool {
    return r.Width == r.Height
}

// Pointer receiver
func (c *Circle) Scale(factor float64) {
    c.Radius *= factor
}
```

### Struct Embedding
```go
type Employee struct {
    Person
    ID     string
    Salary float64
}

// Promoted fields
employee.Name // Accessible through Person
employee.ID   // Direct field
```

### Polymorphism
```go
var shapes []Shape
shapes = append(shapes, &Rectangle{Width: 10, Height: 5})
shapes = append(shapes, &Circle{Radius: 7})

for _, shape := range shapes {
    fmt.Printf("Area: %.2f\n", shape.Area())
}
```

## Advanced Patterns

### Method Chaining
```go
type BankAccount struct {
    Balance float64
}

func (ba *BankAccount) Deposit(amount float64) *BankAccount {
    ba.Balance += amount
    return ba
}

account.Deposit(100).Withdraw(50).ApplyInterest(0.05)
```

### Interface Composition
```go
type Reader interface {
    Read() string
}

type Writer interface {
    Write(content string)
}

type ReadWriter interface {
    Reader
    Writer
}
```

### Strategy Pattern
```go
type SortStrategy interface {
    Sort(data []int) []int
}

type Sorter struct {
    strategy SortStrategy
}

func (s *Sorter) SetStrategy(strategy SortStrategy) {
    s.strategy = strategy
}
```

### Factory Pattern
```go
type ShapeFactory struct{}

func (sf ShapeFactory) CreateShape(shapeType string, dimensions ...float64) Shape {
    switch shapeType {
    case "rectangle":
        return &Rectangle{Width: dimensions[0], Height: dimensions[1]}
    // ... other cases
    }
}
```

## Running the Examples

```bash
go run main.go
go run interfaces.go
go run methods.go
go run embedding.go
go run polymorphism.go
```

## Design Principles

### Composition Over Inheritance
- Use struct embedding instead of inheritance
- Favor composition for code reuse
- Implement interfaces through composition

### Interface Segregation
- Keep interfaces small and focused
- Define interfaces for specific behaviors
- Avoid large, monolithic interfaces

### Liskov Substitution
- Any implementation should satisfy the interface contract
- Subtypes should be substitutable for base types
- Maintain behavioral compatibility

### Dependency Inversion
- Depend on abstractions, not concrete types
- Use interfaces for function parameters
- Enable testing and flexibility

## Best Practices

### Struct Design
1. **Keep structs focused** on single responsibilities
2. **Use descriptive field names**
3. **Consider pointer vs value semantics**
4. **Use embedding for composition**
5. **Implement String() for debugging**

### Interface Design
1. **Design interfaces, not implementations**
2. **Keep interfaces small and cohesive**
3. **Use interface composition**
4. **Accept interfaces, return concrete types**
5. **Define interfaces in the package that uses them**

### Method Design
1. **Choose appropriate receiver type**
2. **Use pointer receivers for mutation**
3. **Value receivers for immutable operations**
4. **Method chaining for fluent APIs**
5. **Consistent naming conventions**

## Common Patterns

### Builder Pattern
```go
type QueryBuilder struct {
    selectClause string
    fromClause   string
    whereClause  string
}

func (qb *QueryBuilder) Select(columns string) *QueryBuilder {
    qb.selectClause = columns
    return qb
}
```

### Decorator Pattern
```go
type Logger interface {
    Log(message string)
}

type TimestampLogger struct {
    Logger
}

func (tl TimestampLogger) Log(message string) {
    timestamp := time.Now().Format("2006-01-02 15:04:05")
    tl.Logger.Log(fmt.Sprintf("[%s] %s", timestamp, message))
}
```

### Observer Pattern
```go
type Observer interface {
    Update(data interface{})
}

type Subject struct {
    observers []Observer
}

func (s *Subject) Attach(observer Observer) {
    s.observers = append(s.observers, observer)
}

func (s *Subject) Notify(data interface{}) {
    for _, observer := range s.observers {
        observer.Update(data)
    }
}
```

## Performance Considerations

### Value vs Pointer Semantics
- **Values**: Copying, immutable data, small structs
- **Pointers**: Mutation, large structs, sharing data

### Interface Performance
- Interface calls have slight overhead
- Type assertions are relatively cheap
- Empty interface requires type assertions

### Memory Allocation
- Struct embedding doesn't allocate extra memory
- Interface values are two words (type, value)
- Method calls through interfaces use dynamic dispatch

## Testing with Interfaces

### Mocking
```go
type Database interface {
    GetUser(id int) (*User, error)
    SaveUser(user *User) error
}

type MockDatabase struct {
    users map[int]*User
}

func (m *MockDatabase) GetUser(id int) (*User, error) {
    return m.users[id], nil
}
```

### Dependency Injection
```go
type Service struct {
    db Database
}

func NewService(db Database) *Service {
    return &Service{db: db}
}
```

## Exercises

1. Create a shape hierarchy with interfaces
2. Implement a logger with different output strategies
3. Build a payment system with multiple payment methods
4. Create a file system with different file types
5. Implement a sorting algorithm using the strategy pattern
6. Build a vehicle simulation with polymorphism
7. Create a builder pattern for complex objects
8. Implement an observer pattern for event handling
