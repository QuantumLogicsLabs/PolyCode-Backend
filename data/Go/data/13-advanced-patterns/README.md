# Advanced Go Patterns

This directory contains comprehensive examples of advanced design patterns, architectural patterns, and concurrency patterns in Go programming.

## Files

- **main.go** - Overview of all advanced patterns
- **design-patterns.go** - Detailed implementation of creational, structural, and behavioral patterns
- **README.md** - This file

## Overview

This section covers advanced Go programming patterns including:

- **Design Patterns** - Creational, structural, and behavioral patterns
- **Concurrency Patterns** - Advanced concurrency and parallelism patterns
- **Architectural Patterns** - High-level architectural patterns
- **Performance Patterns** - Memory management and optimization patterns
- **Error Handling Patterns** - Advanced error handling strategies
- **Testing Patterns** - Professional testing approaches

## Design Patterns

### Creational Patterns

#### 1. Singleton Pattern
Ensures a class has only one instance and provides a global point of access to it.

```go
type Database struct {
    connection string
}

var (
    dbInstance *Database
    dbOnce     sync.Once
)

func GetDatabaseInstance() *Database {
    dbOnce.Do(func() {
        dbInstance = &Database{connection: "postgres://localhost/mydb"}
    })
    return dbInstance
}
```

#### 2. Factory Pattern
Creates objects without specifying the exact class.

```go
type Animal interface {
    Speak() string
    Type() string
}

func CreateAnimal(animalType string) (Animal, error) {
    switch animalType {
    case "dog":
        return Dog{}, nil
    case "cat":
        return Cat{}, nil
    default:
        return nil, fmt.Errorf("unknown animal type: %s", animalType)
    }
}
```

#### 3. Builder Pattern
Separates the construction of a complex object from its representation.

```go
type ComputerBuilder interface {
    SetCPU(cpu string)
    SetMemory(memory int)
    SetStorage(storage int)
    Build() Computer
}
```

#### 4. Prototype Pattern
Creates new objects by copying an existing object.

```go
type Prototype interface {
    Clone() Prototype
    GetDetails() string
}
```

#### 5. Object Pool Pattern
Manages a pool of reusable objects to improve performance.

```go
type ConnectionPool struct {
    pool    chan *Connection
    maxSize int
    created int
    mu      sync.Mutex
}
```

### Structural Patterns

#### 1. Adapter Pattern
Allows incompatible interfaces to work together.

```go
type MediaPlayer interface {
    Play(audioType, filename string)
}

type MediaAdapter struct {
    advancedMusicPlayer AdvancedMediaPlayer
}
```

#### 2. Bridge Pattern
Decouples an abstraction from its implementation.

```go
type MessageSender interface {
    Send(message string, recipient string)
}

type Message struct {
    sender  MessageSender
    content string
}
```

#### 3. Composite Pattern
Compose objects into tree structures to represent part-whole hierarchies.

```go
type Employee interface {
    Name() string
    Salary() float64
    Add(Employee)
    Remove(Employee)
    GetSubordinates() []Employee
}
```

#### 4. Decorator Pattern
Adds new functionality to an object dynamically.

```go
type Pizza interface {
    GetDescription() string
    GetCost() float64
}

type PizzaDecorator struct {
    pizza Pizza
}
```

#### 5. Facade Pattern
Provides a simplified interface to a complex subsystem.

```go
type ComputerFacade struct {
    cpu       *CPU
    memory    *Memory
    hardDrive *HardDrive
}
```

#### 6. Flyweight Pattern
Reduces memory usage by sharing common data.

```go
type TreeFactory struct {
    treeTypes map[string]*TreeType
}
```

#### 7. Proxy Pattern
Provides a surrogate or placeholder for another object.

```go
type ProxyImage struct {
    filename string
    realImage *RealImage
}
```

### Behavioral Patterns

#### 1. Chain of Responsibility
Passes a request along a chain of handlers.

```go
type Handler interface {
    SetNext(handler Handler)
    Handle(request string) string
}
```

#### 2. Command Pattern
Encapsulates a request as an object.

```go
type Command interface {
    Execute()
    Undo()
}
```

#### 3. Iterator Pattern
Provides a way to access elements of an aggregate object sequentially.

```go
type Iterator interface {
    HasNext() bool
    Next() interface{}
}
```

#### 4. Mediator Pattern
Defines an object that centralizes communications between objects.

```go
type Mediator interface {
    Send(message string, colleague Colleague)
}
```

#### 5. Memento Pattern
Captures and restores an object's internal state.

```go
type Memento interface {
    GetState() string
    SetState(state string)
}
```

#### 6. Observer Pattern
Defines a one-to-many dependency between objects.

```go
type Observer interface {
    Update(data string)
}

type Subject interface {
    Register(observer Observer)
    Unregister(observer Observer)
    NotifyObservers()
}
```

#### 7. State Pattern
Allows an object to alter its behavior when its state changes.

```go
type State interface {
    Handle(context *Context)
}
```

#### 8. Strategy Pattern
Defines a family of algorithms and makes them interchangeable.

```go
type Strategy interface {
    Execute(data interface{}) interface{}
}
```

#### 9. Template Method Pattern
Defines the skeleton of an algorithm and lets subclasses fill in the steps.

```go
type TemplateMethod interface {
    StepOne()
    StepTwo()
    StepThree()
}
```

#### 10. Visitor Pattern
Represents an operation to be performed on elements of an object structure.

```go
type Visitor interface {
    VisitElementA(element *ElementA)
    VisitElementB(element *ElementB)
}
```

## Concurrency Patterns

### 1. Worker Pool Pattern
Manages a pool of worker goroutines to process tasks.

```go
type WorkerPool struct {
    workers   []*Worker
    workQueue chan Work
    wg        sync.WaitGroup
}
```

### 2. Pipeline Pattern
Processes data through a series of stages.

```go
type Pipeline struct {
    stages []Stage
}
```

### 3. Fan-In/Fan-Out Pattern
Distributes work to multiple goroutines and collects results.

```go
func fanIn(channels ...<-chan result) <-chan result
func fanOut(input <-chan work, numWorkers int) []<-chan result
```

### 4. Publish/Subscribe Pattern
Decouples message senders from receivers.

```go
type EventBus struct {
    subscribers map[string][]Subscriber
    mutex       sync.RWMutex
}
```

### 5. Future/Promise Pattern
Represents a value that may be available in the future.

```go
type Future struct {
    result chan interface{}
    error  chan error
}
```

### 6. Circuit Breaker Pattern
Prevents cascading failures by detecting failures and stopping propagation.

```go
type CircuitBreaker struct {
    maxFailures int
    failures    int
    state       string
    lastFailure time.Time
    timeout     time.Duration
}
```

## Architectural Patterns

### 1. Repository Pattern
Encapsulates data access logic.

```go
type Repository interface {
    Create(entity Entity) error
    GetByID(id int) (Entity, error)
    Update(entity Entity) error
    Delete(id int) error
}
```

### 2. Service Layer Pattern
Defines application's boundary and its set of available operations.

```go
type Service interface {
    Execute(command Command) (Result, error)
}
```

### 3. CQRS (Command Query Responsibility Segregation)
Separates read and write operations.

```go
type CommandHandler interface {
    Handle(command Command) error
}

type QueryHandler interface {
    Handle(query Query) (Result, error)
}
```

### 4. Event Sourcing
Stores all changes to an application state as a sequence of events.

```go
type Event interface {
    ID() string
    Type() string
    Data() interface{}
    Timestamp() time.Time
}
```

### 5. Hexagonal Architecture
Separates core logic from external concerns.

```go
type Port interface {
    // Define interface for external concerns
}

type Adapter struct {
    // Implement interface for external concerns
}
```

### 6. Microservices Pattern
Structures applications as a collection of loosely coupled services.

```go
type Microservice struct {
    name    string
    port    int
    handler Handler
}
```

## Performance Patterns

### 1. Object Pooling
Reuses objects to avoid allocation overhead.

```go
type Pool struct {
    pool chan interface{}
    New  func() interface{}
}
```

### 2. Lazy Initialization
Delays object creation until it's needed.

```go
type Lazy struct {
    once  sync.Once
    value interface{}
}
```

### 3. Memoization
Caches results of expensive function calls.

```go
type Memoizer struct {
    cache map[string]interface{}
    mu    sync.RWMutex
}
```

### 4. Batching
Groups multiple operations to reduce overhead.

```go
type BatchProcessor struct {
    batchSize int
    timeout  time.Duration
    processor func([]interface{})
}
```

### 5. Rate Limiting
Controls the rate of operations.

```go
type RateLimiter struct {
    tokens    chan struct{}
    refillRate time.Duration
}
```

## Error Handling Patterns

### 1. Error Wrapping
Wraps errors with additional context.

```go
func wrapError(err error, message string) error {
    return fmt.Errorf("%s: %w", message, err)
}
```

### 2. Error Aggregation
Collects multiple errors into one.

```go
type MultiError struct {
    errors []error
}
```

### 3. Error Recovery
Implements retry and fallback mechanisms.

```go
type RetryConfig struct {
    MaxAttempts int
    Delay       time.Duration
}
```

### 4. Contextual Errors
Includes context information in errors.

```go
type ContextError struct {
    Err     error
	Context map[string]interface{}
}
```

## Testing Patterns

### 1. Table-Driven Tests
Uses tables to define test cases.

```go
func TestAdd(t *testing.T) {
    tests := []struct {
        name     string
        a, b     int
        expected int
    }{
        {"positive", 2, 3, 5},
        {"negative", -2, -3, -5},
        {"zero", 0, 5, 5},
    }
    
    for _, tt := range tests {
        t.Run(tt.name, func(t *testing.T) {
            result := Add(tt.a, tt.b)
            if result != tt.expected {
                t.Errorf("Add(%d, %d) = %d; want %d", tt.a, tt.b, result, tt.expected)
            }
        })
    }
}
```

### 2. Test Fixtures
Provides reusable test data.

```go
type TestFixture struct {
    Users []User
    Products []Product
}
```

### 3. Mock Objects
Simulates dependencies for testing.

```go
type MockRepository struct {
    data map[int]*Entity
}
```

### 4. Test Helpers
Provides utility functions for testing.

```go
func AssertEqual(t *testing.T, expected, actual interface{}) {
    if expected != actual {
        t.Errorf("Expected %v, got %v", expected, actual)
    }
}
```

## Memory Management

### 1. Buffer Pooling
Reuses buffers to reduce allocations.

```go
var bufferPool = sync.Pool{
    New: func() interface{} {
        return make([]byte, 1024)
    },
}
```

### 2. Memory Profiling
Monitors memory usage and leaks.

```go
func ProfileMemory() {
    runtime.GC()
    var m runtime.MemStats
    runtime.ReadMemStats(&m)
    fmt.Printf("Alloc = %d, TotalAlloc = %d\n", m.Alloc, m.TotalAlloc)
}
```

### 3. Garbage Collection Tuning
Optimizes GC behavior.

```go
func TuneGC() {
    debug.SetGCPercent(100)
    runtime.GC()
}
```

## Resource Management

### 1. Connection Pooling
Manages database connections efficiently.

```go
type ConnectionPool struct {
    connections chan *sql.DB
    maxSize     int
}
```

### 2. File Handling
Manages file operations safely.

```go
func ReadFile(filename string) ([]byte, error) {
    file, err := os.Open(filename)
    if err != nil {
        return nil, err
    }
    defer file.Close()
    
    return io.ReadAll(file)
}
```

### 3. Resource Cleanup
Ensures proper resource cleanup.

```go
func WithResource(resource Resource, fn func(Resource) error) error {
    if err := resource.Open(); err != nil {
        return err
    }
    defer resource.Close()
    
    return fn(resource)
}
```

## Logging Patterns

### 1. Structured Logging
Uses structured log formats.

```go
type Logger struct {
    level  LogLevel
    fields map[string]interface{}
}
```

### 2. Contextual Logging
Includes context information in logs.

```go
func LogWithContext(ctx context.Context, message string, fields map[string]interface{}) {
    logger := FromContext(ctx)
    logger.Info(message, fields)
}
```

### 3. Performance Logging
Monitors performance metrics.

```go
func MeasureTime(name string, fn func()) time.Duration {
    start := time.Now()
    fn()
    return time.Since(start)
}
```

## Best Practices

### When to Use Patterns

1. **Singleton**: Use for global configuration, logging, or database connections
2. **Factory**: Use when object creation logic is complex
3. **Builder**: Use for complex object construction
4. **Repository**: Use for data access abstraction
5. **Service**: Use for business logic encapsulation
6. **Observer**: Use for event-driven systems
7. **Strategy**: Use for algorithm selection
8. **Command**: Use for undo/redo functionality

### Pattern Selection Guide

| Pattern | Use Case | Complexity |
|---------|---------|------------|
| Singleton | Global state management | Low |
| Factory | Object creation | Medium |
| Builder | Complex construction | High |
| Repository | Data access | Medium |
| Service | Business logic | Medium |
| Observer | Event systems | Medium |
| Strategy | Algorithm selection | Low |
| Command | Undo/redo | Medium |

### Anti-Patterns

1. **Over-engineering**: Don't use complex patterns for simple problems
2. **Pattern abuse**: Use patterns only when they solve real problems
3. **Premature optimization**: Optimize only when necessary
4. **Pattern proliferation**: Too many patterns make code hard to understand

## Performance Considerations

### Memory Allocation
- Use object pools for frequently allocated objects
- Avoid unnecessary allocations in hot paths
- Use value types for small objects

### Concurrency
- Use channels over mutexes when possible
- Avoid shared state when possible
- Use worker pools for CPU-bound tasks

### Error Handling
- Use error wrapping for context
- Avoid panic in library code
- Provide meaningful error messages

## Testing Strategies

### Unit Testing
- Test individual components in isolation
- Use mocks for external dependencies
- Cover edge cases and error conditions

### Integration Testing
- Test component interactions
- Use real implementations where possible
- Test error scenarios

### Benchmark Testing
- Measure performance of critical paths
- Compare alternative implementations
- Profile memory usage

## Examples

Each pattern in this directory includes:

- **Complete Implementation**: Full working code examples
- **Usage Examples**: How to use the pattern in practice
- **Best Practices**: Guidelines for proper usage
- **Common Pitfalls**: What to avoid
- **Performance Notes**: Performance considerations

## Learning Path

1. **Start Simple**: Begin with basic patterns like Singleton and Factory
2. **Progress Gradually**: Move to more complex patterns
3. **Practice**: Implement patterns in real projects
4. **Refactor**: Apply patterns to improve existing code
5. **Review**: Learn from existing Go codebases

## Resources

### Books
- "Design Patterns: Elements of Reusable Object-Oriented Software"
- "Head First Design Patterns"
- "Refactoring: Improving the Design of Existing Code"
- "Clean Architecture: A Craftsman's Guide"

### Online Resources
- [Go Design Patterns](https://github.com/tmrts/go-patterns)
- [Go by Example](https://gobyexample.com/)
- [Effective Go](https://go.dev/doc/effective_go)

### Libraries
- [GORM](https://gorm.io/) - ORM with repository pattern
- [Chi](https://github.com/go-chi/chi) - HTTP router with middleware
- [Testify](https://github.com/stretchr/testify) - Testing utilities

## Contributing

When contributing to this directory:

1. Follow Go coding conventions
2. Provide complete, working examples
3. Include comprehensive documentation
4. Add usage examples
5. Consider performance implications
6. Write tests for your patterns

## License

This code is provided for educational purposes. Feel free to use and modify according to your needs.
