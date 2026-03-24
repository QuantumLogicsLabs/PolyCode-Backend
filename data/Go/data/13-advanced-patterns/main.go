package main

import (
	"context"
	"fmt"
	"log"
	"sync"
	"time"
)

func main() {
	fmt.Println("=== Advanced Go Patterns ===")
	
	// Design patterns
	fmt.Println("\n--- Design Patterns ---")
	designPatterns()
	
	// Concurrency patterns
	fmt.Println("\n--- Advanced Concurrency Patterns ---")
	advancedConcurrency()
	
	// Architectural patterns
	fmt.Println("\n--- Architectural Patterns ---")
	architecturalPatterns()
	
	// Performance patterns
	fmt.Println("\n--- Performance Patterns ---")
	performancePatterns()
	
	// Error handling patterns
	fmt.Println("\n--- Error Handling Patterns ---")
	errorHandlingPatterns()
	
	// Testing patterns
	fmt.Println("\n--- Testing Patterns ---")
	testingPatterns()
	
	// Memory management patterns
	fmt.Println("\n--- Memory Management Patterns ---")
	memoryManagement()
	
	// Resource management patterns
	fmt.Println("\n--- Resource Management Patterns ---")
	resourceManagement()
	
	// Logging patterns
	fmt.Println("\n--- Logging Patterns ---")
	loggingPatterns()
}

// Design patterns
func designPatterns() {
	fmt.Println("Common Design Patterns in Go:")
	
	// Singleton pattern
	fmt.Println("\n1. Singleton Pattern:")
	singletonExample()
	
	// Factory pattern
	fmt.Println("\n2. Factory Pattern:")
	factoryExample()
	
	// Builder pattern
	fmt.Println("\n3. Builder Pattern:")
	builderExample()
	
	// Observer pattern
	fmt.Println("\n4. Observer Pattern:")
	observerExample()
	
	// Strategy pattern
	fmt.Println("\n5. Strategy Pattern:")
	strategyExample()
	
	// Command pattern
	fmt.Println("\n6. Command Pattern:")
	commandExample()
}

// Singleton pattern
func singletonExample() {
	type Database struct {
		connection string
	}
	
	var (
		instance *Database
		once     sync.Once
	)
	
	getInstance := func() *Database {
		once.Do(func() {
			instance = &Database{connection: "database-connection-string"}
		})
		return instance
	}
	
	// Usage
	db1 := getInstance()
	db2 := getInstance()
	
	fmt.Printf("Database instances: %p, %p (should be same)\n", db1, db2)
	fmt.Printf("Connection: %s\n", db1.connection)
}

// Factory pattern
func factoryExample() {
	type Animal interface {
		Speak() string
	}
	
	type Dog struct{}
	func (d Dog) Speak() string { return "Woof!" }
	
	type Cat struct{}
	func (c Cat) Speak() string { return "Meow!" }
	
	createAnimal := func(animalType string) Animal {
		switch animalType {
		case "dog":
			return Dog{}
		case "cat":
			return Cat{}
		default:
			return nil
		}
	}
	
	// Usage
	dog := createAnimal("dog")
	cat := createAnimal("cat")
	
	fmt.Printf("Dog says: %s\n", dog.Speak())
	fmt.Printf("Cat says: %s\n", cat.Speak())
}

// Builder pattern
func builderExample() {
	type House struct {
		windows int
		doors   int
		rooms   int
		garage  bool
		pool    bool
	}
	
	type HouseBuilder struct {
		house House
	}
	
	func NewHouseBuilder() *HouseBuilder {
		return &HouseBuilder{house: House{}}
	}
	
	func (hb *HouseBuilder) Windows(count int) *HouseBuilder {
		hb.house.windows = count
		return hb
	}
	
	func (hb *HouseBuilder) Doors(count int) *HouseBuilder {
		hb.house.doors = count
		return hb
	}
	
	func (hb *HouseBuilder) Rooms(count int) *HouseBuilder {
		hb.house.rooms = count
		return hb
	}
	
	func (hb *HouseBuilder) Garage(hasGarage bool) *HouseBuilder {
		hb.house.garage = hasGarage
		return hb
	}
	
	func (hb *HouseBuilder) Pool(hasPool bool) *HouseBuilder {
		hb.house.pool = hasPool
		return hb
	}
	
	func (hb *HouseBuilder) Build() House {
		return hb.house
	}
	
	// Usage
	house := NewHouseBuilder().
		Windows(8).
		Doors(4).
		Rooms(6).
		Garage(true).
		Pool(false).
		Build()
	
	fmt.Printf("House: %+v\n", house)
}

// Observer pattern
func observerExample() {
	type Observer interface {
		Update(data string)
	}
	
	type Subject struct {
		observers []Observer
		data      string
	}
	
	func (s *Subject) Attach(observer Observer) {
		s.observers = append(s.observers, observer)
	}
	
	func (s *Subject) Notify() {
		for _, observer := range s.observers {
			observer.Update(s.data)
		}
	}
	
	func (s *Subject) SetData(data string) {
		s.data = data
		s.Notify()
	}
	
	type EmailObserver struct{}
	func (e EmailObserver) Update(data string) {
		fmt.Printf("Email notification: %s\n", data)
	}
	
	type SMSObserver struct{}
	func (s SMSObserver) Update(data string) {
		fmt.Printf("SMS notification: %s\n", data)
	}
	
	// Usage
	subject := &Subject{}
	subject.Attach(EmailObserver{})
	subject.Attach(SMSObserver{})
	
	subject.SetData("New product available!")
}

// Strategy pattern
func strategyExample() {
	type PaymentStrategy interface {
		Pay(amount float64) string
	}
	
	type CreditCardStrategy struct{}
	func (c CreditCardStrategy) Pay(amount float64) string {
		return fmt.Sprintf("Paid %.2f using Credit Card", amount)
	}
	
	type PayPalStrategy struct{}
	func (p PayPalStrategy) Pay(amount float64) string {
		return fmt.Sprintf("Paid %.2f using PayPal", amount)
	}
	
	type ShoppingCart struct {
		paymentStrategy PaymentStrategy
		amount         float64
	}
	
	func (sc *ShoppingCart) SetPaymentStrategy(strategy PaymentStrategy) {
		sc.paymentStrategy = strategy
	}
	
	func (sc *ShoppingCart) Checkout() string {
		return sc.paymentStrategy.Pay(sc.amount)
	}
	
	// Usage
	cart := &ShoppingCart{amount: 100.50}
	
	cart.SetPaymentStrategy(CreditCardStrategy{})
	fmt.Println(cart.Checkout())
	
	cart.SetPaymentStrategy(PayPalStrategy{})
	fmt.Println(cart.Checkout())
}

// Command pattern
func commandExample() {
	type Command interface {
		Execute()
	}
	
	type Light struct {
		isOn bool
	}
	
	func (l *Light) TurnOn()  { l.isOn = true; fmt.Println("Light is ON") }
	func (l *Light) TurnOff() { l.isOn = false; fmt.Println("Light is OFF") }
	
	type LightOnCommand struct {
		light *Light
	}
	
	func (loc LightOnCommand) Execute() {
		loc.light.TurnOn()
	}
	
	type LightOffCommand struct {
		light *Light
	}
	
	func (lof LightOffCommand) Execute() {
		lof.light.TurnOff()
	}
	
	type RemoteControl struct {
		command Command
	}
	
	func (rc *RemoteControl) SetCommand(command Command) {
		rc.command = command
	}
	
	func (rc *RemoteControl) PressButton() {
		rc.command.Execute()
	}
	
	// Usage
	light := &Light{}
	remote := &RemoteControl{}
	
	remote.SetCommand(LightOnCommand{light: light})
	remote.PressButton()
	
	remote.SetCommand(LightOffCommand{light: light})
	remote.PressButton()
}

// Advanced concurrency patterns
func advancedConcurrency() {
	fmt.Println("Advanced Concurrency Patterns:")
	
	// Worker pool with results
	fmt.Println("\n1. Worker Pool with Results:")
	workerPoolWithResults()
	
	// Fan-in, fan-out pattern
	fmt.Println("\n2. Fan-In/Fan-Out Pattern:")
	fanInFanOut()
	
	// Pipeline pattern
	fmt.Println("\n3. Pipeline Pattern:")
	pipelinePattern()
	
	// Timeout pattern
	fmt.Println("\n4. Timeout Pattern:")
	timeoutPattern()
	
	// Retry pattern
	fmt.Println("\n5. Retry Pattern:")
	retryPattern()
	
	// Circuit breaker pattern
	fmt.Println("\n6. Circuit Breaker Pattern:")
	circuitBreakerPattern()
}

// Worker pool with results
func workerPoolWithResults() {
	type Work struct {
		ID   int
		Data string
	}
	
	type Result struct {
		ID    int
		Value int
		Error error
	}
	
	worker := func(id int, jobs <-chan Work, results chan<- Result) {
		for work := range jobs {
			// Process work
			value := len(work.Data)
			result := Result{
				ID:    work.ID,
				Value: value,
				Error: nil,
			}
			results <- result
		}
	}
	
	const numWorkers = 3
	jobs := make(chan Work, 10)
	results := make(chan Result, 10)
	
	// Start workers
	for i := 0; i < numWorkers; i++ {
		go worker(i, jobs, results)
	}
	
	// Send jobs
	for i := 0; i < 5; i++ {
		jobs <- Work{ID: i, Data: fmt.Sprintf("task-%d", i)}
	}
	close(jobs)
	
	// Collect results
	for i := 0; i < 5; i++ {
		result := <-results
		fmt.Printf("Result: ID=%d, Value=%d\n", result.ID, result.Value)
	}
}

// Fan-in, fan-out pattern
func fanInFanOut() {
	// Fan-out: distribute work to multiple workers
	process := func(data string) <-chan string {
		out := make(chan string)
		go func() {
			defer close(out)
			for i := 0; i < 3; i++ {
				out <- fmt.Sprintf("%s-processed-%d", data, i)
			}
		}()
		return out
	}
	
	// Fan-in: collect results from multiple channels
	fanIn := func(channels ...<-chan string) <-chan string {
		out := make(chan string)
		var wg sync.WaitGroup
		
		for _, ch := range channels {
			wg.Add(1)
			go func(c <-chan string) {
				defer wg.Done()
				for item := range c {
					out <- item
				}
			}(ch)
		}
		
		go func() {
			wg.Wait()
			close(out)
		}()
		
		return out
	}
	
	// Usage
	ch1 := process("data1")
	ch2 := process("data2")
	ch3 := process("data3")
	
	results := fanIn(ch1, ch2, ch3)
	
	for result := range results {
		fmt.Printf("Fan-in result: %s\n", result)
	}
}

// Pipeline pattern
func pipelinePattern() {
	// Stage 1: Generate numbers
	generate := func() <-chan int {
		out := make(chan int)
		go func() {
			defer close(out)
			for i := 1; i <= 10; i++ {
				out <- i
			}
		}()
		return out
	}
	
	// Stage 2: Square numbers
	square := func(in <-chan int) <-chan int {
		out := make(chan int)
		go func() {
			defer close(out)
			for num := range in {
				out <- num * num
			}
		}()
		return out
	}
	
	// Stage 3: Filter even numbers
	filter := func(in <-chan int) <-chan int {
		out := make(chan int)
		go func() {
			defer close(out)
			for num := range in {
				if num%2 == 0 {
					out <- num
				}
			}
		}()
		return out
	}
	
	// Build pipeline
	numbers := generate()
	squared := square(numbers)
	filtered := filter(squared)
	
	// Consume results
	for result := range filtered {
		fmt.Printf("Pipeline result: %d\n", result)
	}
}

// Timeout pattern
func timeoutPattern() {
	work := func() <-chan string {
		out := make(chan string)
		go func() {
			defer close(out)
			time.Sleep(2 * time.Second) // Simulate work
			out <- "work completed"
		}()
		return out
	}
	
	ctx, cancel := context.WithTimeout(context.Background(), 1*time.Second)
	defer cancel()
	
	select {
	case result := <-work():
		fmt.Printf("Work completed: %s\n", result)
	case <-ctx.Done():
		fmt.Printf("Work timed out: %v\n", ctx.Err())
	}
}

// Retry pattern
func retryPattern() {
	operation := func() (string, error) {
		attempts := 0
		return "", fmt.Errorf("attempt %d failed", attempts)
	}
	
	retry := func(fn func() (string, error), maxAttempts int) (string, error) {
		var lastErr error
		
		for i := 0; i < maxAttempts; i++ {
			result, err := fn()
			if err == nil {
				return result, nil
			}
			lastErr = err
			fmt.Printf("Attempt %d failed: %v\n", i+1, err)
			time.Sleep(time.Duration(i+1) * time.Second)
		}
		
		return "", lastErr
	}
	
	// Usage
	result, err := retry(func() (string, error) {
		return "success", nil // Simulate success
	}, 3)
	
	if err != nil {
		fmt.Printf("All attempts failed: %v\n", err)
	} else {
		fmt.Printf("Operation succeeded: %s\n", result)
	}
}

// Circuit breaker pattern
func circuitBreakerPattern() {
	type CircuitBreaker struct {
		maxFailures int
		failures    int
		state       string // "closed", "open", "half-open"
		lastFailure time.Time
		timeout     time.Duration
		mutex       sync.Mutex
	}
	
	NewCircuitBreaker := func(maxFailures int, timeout time.Duration) *CircuitBreaker {
		return &CircuitBreaker{
			maxFailures: maxFailures,
			state:       "closed",
			timeout:     timeout,
		}
	}
	
	(cb *CircuitBreaker) Call(operation func() (string, error)) (string, error) {
		cb.mutex.Lock()
		defer cb.mutex.Unlock()
		
		// Check if circuit is open and timeout has passed
		if cb.state == "open" && time.Since(cb.lastFailure) > cb.timeout {
			cb.state = "half-open"
		}
		
		// Reject calls if circuit is open
		if cb.state == "open" {
			return "", fmt.Errorf("circuit breaker is open")
		}
		
		// Execute operation
		result, err := operation()
		
		if err != nil {
			cb.failures++
			cb.lastFailure = time.Now()
			
			if cb.failures >= cb.maxFailures {
				cb.state = "open"
			}
			
			return "", err
		}
		
		// Reset on success
		cb.failures = 0
		cb.state = "closed"
		
		return result, nil
	}
	
	// Usage
	cb := NewCircuitBreaker(3, 5*time.Second)
	
	operation := func() (string, error) {
		return "operation result", nil
	}
	
	result, err := cb.Call(operation)
	if err != nil {
		fmt.Printf("Circuit breaker error: %v\n", err)
	} else {
		fmt.Printf("Operation succeeded: %s\n", result)
	}
}

// Architectural patterns
func architecturalPatterns() {
	fmt.Println("Architectural Patterns:")
	
	// Repository pattern
	fmt.Println("\n1. Repository Pattern:")
	repositoryPattern()
	
	// Service layer pattern
	fmt.Println("\n2. Service Layer Pattern:")
	serviceLayerPattern()
	
	// Dependency injection pattern
	fmt.Println("\n3. Dependency Injection Pattern:")
	dependencyInjectionPattern()
	
	// MVC pattern
	fmt.Println("\n4. MVC Pattern:")
	mvcPattern()
	
	// CQRS pattern
	fmt.Println("\n5. CQRS Pattern:")
	cqrsPattern()
	
	// Event sourcing pattern
	fmt.Println("\n6. Event Sourcing Pattern:")
	eventSourcingPattern()
}

// Repository pattern
func repositoryPattern() {
	type User struct {
		ID    int
		Name  string
		Email string
	}
	
	type UserRepository interface {
		Create(user *User) error
		GetByID(id int) (*User, error)
		Update(user *User) error
		Delete(id int) error
	}
	
	type InMemoryUserRepository struct {
		users map[int]*User
		nextID int
		mutex  sync.RWMutex
	}
	
	NewInMemoryUserRepository := func() *InMemoryUserRepository {
		return &InMemoryUserRepository{
			users: make(map[int]*User),
			nextID: 1,
		}
	}
	
	(repo *InMemoryUserRepository) Create(user *User) error {
		repo.mutex.Lock()
		defer repo.mutex.Unlock()
		
		user.ID = repo.nextID
		repo.users[user.ID] = user
		repo.nextID++
		
		return nil
	}
	
	(repo *InMemoryUserRepository) GetByID(id int) (*User, error) {
		repo.mutex.RLock()
		defer repo.mutex.RUnlock()
		
		user, exists := repo.users[id]
		if !exists {
			return nil, fmt.Errorf("user not found")
		}
		
		return user, nil
	}
	
	(repo *InMemoryUserRepository) Update(user *User) error {
		repo.mutex.Lock()
		defer repo.mutex.Unlock()
		
		if _, exists := repo.users[user.ID]; !exists {
			return fmt.Errorf("user not found")
		}
		
		repo.users[user.ID] = user
		return nil
	}
	
	(repo *InMemoryUserRepository) Delete(id int) error {
		repo.mutex.Lock()
		defer repo.mutex.Unlock()
		
		if _, exists := repo.users[id]; !exists {
			return fmt.Errorf("user not found")
		}
		
		delete(repo.users, id)
		return nil
	}
	
	// Usage
	repo := NewInMemoryUserRepository()
	user := &User{Name: "John Doe", Email: "john@example.com"}
	
	repo.Create(user)
	retrievedUser, _ := repo.GetByID(user.ID)
	
	fmt.Printf("Created and retrieved user: %+v\n", retrievedUser)
}

// Service layer pattern
func serviceLayerPattern() {
	type User struct {
		ID    int
		Name  string
	Email string
	}
	
	type UserRepository interface {
		Create(user *User) error
		GetByID(id int) (*User, error)
		GetByEmail(email string) (*User, error)
	}
	
	type UserService struct {
		repo UserRepository
	}
	
	NewUserService := func(repo UserRepository) *UserService {
		return &UserService{repo: repo}
	}
	
	(s *UserService) CreateUser(name, email string) (*User, error) {
		// Validate input
		if name == "" {
			return nil, fmt.Errorf("name is required")
		}
		if email == "" {
			return nil, fmt.Errorf("email is required")
		}
		
		// Check if user already exists
		if _, err := s.repo.GetByEmail(email); err == nil {
			return nil, fmt.Errorf("user with email %s already exists", email)
		}
		
		// Create user
		user := &User{Name: name, Email: email}
		if err := s.repo.Create(user); err != nil {
			return nil, err
		}
		
		return user, nil
	}
	
	// Usage (with mock repository)
	type MockRepo struct{}
	func (m *MockRepo) Create(user *User) error { return nil }
	func (m *MockRepo) GetByID(id int) (*User, error) { return nil, fmt.Errorf("not found") }
	func (m *MockRepo) GetByEmail(email string) (*User, error) { return nil, fmt.Errorf("not found") }
	
	service := NewUserService(&MockRepo{})
	user, err := service.Create("Jane Doe", "jane@example.com")
	
	if err != nil {
		fmt.Printf("Error creating user: %v\n", err)
	} else {
		fmt.Printf("Created user: %+v\n", user)
	}
}

// Dependency injection pattern
func dependencyInjectionPattern() {
	type Database interface {
		Query(query string) (string, error)
	}
	
	type Logger interface {
		Log(message string)
	}
	
	type Service struct {
		db     Database
		logger Logger
	}
	
	NewService := func(db Database, logger Logger) *Service {
		return &Service{db: db, logger: logger}
	}
	
	(s *Service) DoWork() {
		s.logger.Log("Starting work")
		result, err := s.db.Query("SELECT * FROM users")
		if err != nil {
			s.logger.Log("Error: " + err.Error())
			return
		}
		s.logger.Log("Work completed: " + result)
	}
	
	// Mock implementations
	type MockDB struct{}
	func (m *MockDB) Query(query string) (string, error) { return "query result", nil }
	
	type MockLogger struct{}
	func (m *MockLogger) Log(message string) { fmt.Printf("Log: %s\n", message) }
	
	// Dependency injection
	service := NewService(&MockDB{}, &MockLogger{})
	service.DoWork()
}

// MVC pattern
func mvcPattern() {
	// Model
	type User struct {
		ID    int
		Name  string
		Email string
	}
	
	// View
	type UserView struct{}
	
	(userView *UserView) Render(user *User) string {
		return fmt.Sprintf("User: %s (%s)", user.Name, user.Email)
	}
	
	// Controller
	type UserController struct {
		view *UserView
	}
	
	NewUserController := func() *UserController {
		return &UserController{view: &UserView{}}
	}
	
	(controller *UserController) GetUser(id int) string {
		// In real app, get user from service/repository
		user := &User{ID: id, Name: "John Doe", Email: "john@example.com"}
		return controller.view.Render(user)
	}
	
	// Usage
	controller := NewUserController()
	output := controller.GetUser(1)
	fmt.Printf("MVC output: %s\n", output)
}

// CQRS pattern
func cqrsPattern() {
	// Command
	type CreateUserCommand struct {
		Name  string
		Email string
	}
	
	// Command Handler
	type CreateUserCommandHandler struct{}
	
	(handler *CreateUserCommandHandler) Handle(command CreateUserCommand) error {
		fmt.Printf("Creating user: %s (%s)\n", command.Name, command.Email)
		return nil
	}
	
	// Query
	type GetUserQuery struct {
		ID int
	}
	
	// Query Result
	type UserQueryResult struct {
		ID    int
		Name  string
		Email string
	}
	
	// Query Handler
	type GetUserQueryHandler struct{}
	
	(handler *GetUserQueryHandler) Handle(query GetUserQuery) (UserQueryResult, error) {
		// In real app, query read model
		return UserQueryResult{ID: query.ID, Name: "John Doe", Email: "john@example.com"}, nil
	}
	
	// Usage
	commandHandler := &CreateUserCommandHandler{}
	queryHandler := &GetUserQueryHandler{}
	
	// Command side
	err := commandHandler.Handle(CreateUserCommand{Name: "Jane Doe", Email: "jane@example.com"})
	if err != nil {
		fmt.Printf("Command error: %v\n", err)
	}
	
	// Query side
	result, err := queryHandler.Handle(GetUserQuery{ID: 1})
	if err != nil {
		fmt.Printf("Query error: %v\n", err)
	} else {
		fmt.Printf("Query result: %+v\n", result)
	}
}

// Event sourcing pattern
func eventSourcingPattern() {
	// Event
	type Event interface {
		Type() string
		Data() interface{}
	}
	
	type UserCreatedEvent struct {
		ID    int
		Name  string
		Email string
	}
	
	(e UserCreatedEvent) Type() string { return "UserCreated" }
	(e UserCreatedEvent) Data() interface{} {
		return map[string]interface{}{
			"id":    e.ID,
			"name":  e.Name,
			"email": e.Email,
		}
	}
	
	// Event Store
	type EventStore struct {
		events []Event
		mutex  sync.Mutex
	}
	
	NewEventStore := func() *EventStore {
		return &EventStore{events: make([]Event, 0)}
	}
	
	(store *EventStore) Save(event Event) error {
		store.mutex.Lock()
		defer store.mutex.Unlock()
		
		store.events = append(store.events, event)
		return nil
	}
	
	(store *EventStore) GetEvents() []Event {
		store.mutex.Lock()
		defer store.mutex.Unlock()
		
		return store.events
	}
	
	// Aggregate
	type UserAggregate struct {
		id   int
		name string
	}
	
	NewUserAggregate := func() *UserAggregate {
		return &UserAggregate{}
	}
	
	(ua *UserAggregate) Apply(event Event) error {
		switch e := event.(type) {
		case UserCreatedEvent:
			ua.id = e.ID
			ua.name = e.Name
		default:
			return fmt.Errorf("unknown event type: %s", event.Type())
		}
		return nil
	}
	
	// Usage
	eventStore := NewEventStore()
	user := NewUserAggregate()
	
	// Create and save event
	event := UserCreatedEvent{ID: 1, Name: "John Doe", Email: "john@example.com"}
	eventStore.Save(event)
	
	// Replay events
	for _, event := range eventStore.GetEvents() {
		user.Apply(event)
	}
	
	fmt.Printf("Event-sourced user: ID=%d, Name=%s\n", user.id, user.name)
}

// Performance patterns
func performancePatterns() {
	fmt.Println("Performance Patterns:")
	
	// Object pooling
	fmt.Println("\n1. Object Pool Pattern:")
	objectPoolPattern()
	
	// Lazy initialization
	fmt.Println("\n2. Lazy Initialization Pattern:")
	lazyInitializationPattern()
	
	// Memoization
	fmt.Println("\n3. Memoization Pattern:")
	memoizationPattern()
	
	// Batching
	fmt.Println("\n4. Batching Pattern:")
	batchingPattern()
	
	// Caching
	fmt.Println("\n5. Caching Pattern:")
	cachingPattern()
	
	// Rate limiting
	fmt.Println("\n6. Rate Limiting Pattern:")
	rateLimitingPattern()
}

// Object pool pattern
func objectPoolPattern() {
	type Worker struct {
		id int
	}
	
	NewWorker := func(id int) *Worker {
		return &Worker{id: id}
	}
	
	(worker *Worker) DoWork(task string) {
		fmt.Printf("Worker %d doing: %s\n", worker.id, task)
	}
	
	type WorkerPool struct {
		workers chan *Worker
	}
	
	NewWorkerPool := func(size int) *WorkerPool {
		pool := &WorkerPool{
			workers: make(chan *Worker, size),
		}
		
		for i := 0; i < size; i++ {
			pool.workers <- NewWorker(i)
		}
		
		return pool
	}
	
	(pool *WorkerPool) Get() *Worker {
		return <-pool.workers
	}
	
	(pool *WorkerPool) Put(worker *Worker) {
		pool.workers <- worker
	}
	
	// Usage
	pool := NewWorkerPool(3)
	
	worker1 := pool.Get()
	worker1.DoWork("task 1")
	pool.Put(worker1)
	
	worker2 := pool.Get()
	worker2.DoWork("task 2")
	pool.Put(worker2)
}

// Lazy initialization pattern
func lazyInitializationPattern() {
	type ExpensiveResource struct {
		data string
	}
	
	NewExpensiveResource := func() *ExpensiveResource {
		fmt.Println("Creating expensive resource...")
		time.Sleep(100 * time.Millisecond)
		return &ExpensiveResource{data: "expensive data"}
	}
	
	type LazyResource struct {
		resource *ExpensiveResource
		once     sync.Once
	}
	
	NewLazyResource := func() *LazyResource {
		return &LazyResource{}
	}
	
	(lr *LazyResource) Get() *ExpensiveResource {
		lr.once.Do(func() {
			lr.resource = NewExpensiveResource()
		})
		return lr.resource
	}
	
	// Usage
	lazy := NewLazyResource()
	
	fmt.Println("First access:")
	resource1 := lazy.Get()
	fmt.Printf("Resource: %s\n", resource1.data)
	
	fmt.Println("Second access:")
	resource2 := lazy.Get()
	fmt.Printf("Resource: %s\n", resource2.data)
}

// Memoization pattern
func memoizationPattern() {
	memoize := func(fn func(int) int) func(int) int {
		cache := make(map[int]int)
		var mutex sync.Mutex
		
		return func(n int) int {
			mutex.Lock()
			defer mutex.Unlock()
			
			if result, exists := cache[n]; exists {
				return result
			}
			
			result := fn(n)
			cache[n] = result
			return result
		}
	}
	
	// Fibonacci function
	fib := func(n int) int {
		if n <= 1 {
			return n
		}
		return fib(n-1) + fib(n-2)
	}
	
	// Memoized Fibonacci
	memoFib := memoize(fib)
	
	fmt.Printf("Fibonacci(10): %d\n", memoFib(10))
	fmt.Printf("Fibonacci(20): %d\n", memoFib(20))
}

// Batching pattern
func batchingPattern() {
	type BatchProcessor struct {
		batchSize int
		timeout   time.Duration
		processor func([]interface{})
		buffer    []interface{}
		mutex     sync.Mutex
		timer     *time.Timer
	}
	
	NewBatchProcessor := func(batchSize int, timeout time.Duration, processor func([]interface{})) *BatchProcessor {
		return &BatchProcessor{
			batchSize: batchSize,
			timeout:   timeout,
			processor: processor,
		}
	}
	
	(bp *BatchProcessor) Add(item interface{}) {
		bp.mutex.Lock()
		defer bp.mutex.Unlock()
		
		bp.buffer = append(bp.buffer, item)
		
		if len(bp.buffer) >= bp.batchSize {
			bp.flush()
		} else if bp.timer == nil {
			bp.timer = time.AfterFunc(bp.timeout, func() {
				bp.mutex.Lock()
				defer bp.mutex.Unlock()
				bp.flush()
			})
		}
	}
	
	(bp *BatchProcessor) flush() {
		if bp.timer != nil {
			bp.timer.Stop()
			bp.timer = nil
		}
		
		if len(bp.buffer) > 0 {
			bp.processor(bp.buffer)
			bp.buffer = bp.buffer[:0]
		}
	}
	
	// Usage
	processor := func(batch []interface{}) {
		fmt.Printf("Processing batch of %d items\n", len(batch))
	}
	
	bp := NewBatchProcessor(3, 2*time.Second, processor)
	
	for i := 0; i < 7; i++ {
		bp.Add(i)
		time.Sleep(500 * time.Millisecond)
	}
	
	time.Sleep(3 * time.Second) // Wait for any remaining batch
}

// Caching pattern
func cachingPattern() {
	type Cache struct {
		data  map[string]interface{}
		mutex sync.RWMutex
	}
	
	NewCache := func() *Cache {
		return &Cache{data: make(map[string]interface{})}
	}
	
	(cache *Cache) Set(key string, value interface{}) {
		cache.mutex.Lock()
		defer cache.mutex.Unlock()
		cache.data[key] = value
	}
	
	(cache *Cache) Get(key string) (interface{}, bool) {
		cache.mutex.RLock()
		defer cache.mutex.RUnlock()
		value, exists := cache.data[key]
		return value, exists
	}
	
	// Usage
	cache := NewCache()
	
	cache.Set("user:1", "John Doe")
	
	if value, exists := cache.Get("user:1"); exists {
		fmt.Printf("Cached value: %s\n", value)
	}
	
	if _, exists := cache.Get("user:2"); !exists {
		fmt.Println("Value not found in cache")
	}
}

// Rate limiting pattern
func rateLimitingPattern() {
	type RateLimiter struct {
		tokens    chan struct{}
		refillRate time.Duration
	}
	
	NewRateLimiter := func(rate int, refillRate time.Duration) *RateLimiter {
		rl := &RateLimiter{
			tokens:    make(chan struct{}, rate),
			refillRate: refillRate,
		}
		
		// Initial tokens
		for i := 0; i < rate; i++ {
			rl.tokens <- struct{}{}
		}
		
		// Refill tokens
		go func() {
			ticker := time.NewTicker(refillRate)
			for range ticker.C {
				select {
				case rl.tokens <- struct{}{}:
				default:
					// Channel full, skip
				}
			}
		}()
		
		return rl
	}
	
	(rl *RateLimiter) Allow() bool {
		select {
		case <-rl.tokens:
			return true
		default:
			return false
		}
	}
	
	// Usage
	limiter := NewRateLimiter(3, time.Second)
	
	for i := 0; i < 10; i++ {
		if limiter.Allow() {
			fmt.Printf("Request %d: Allowed\n", i+1)
		} else {
			fmt.Printf("Request %d: Rate limited\n", i+1)
		}
		time.Sleep(200 * time.Millisecond)
	}
}

// Error handling patterns
func errorHandlingPatterns() {
	fmt.Println("Error Handling Patterns:")
	
	// Error wrapping
	fmt.Println("\n1. Error Wrapping Pattern:")
	errorWrappingPattern()
	
	// Error aggregation
	fmt.Println("\n2. Error Aggregation Pattern:")
	errorAggregationPattern()
	
	// Error recovery
	fmt.Println("\n3. Error Recovery Pattern:")
	errorRecoveryPattern()
	
	// Error context
	fmt.Println("\n4. Error Context Pattern:")
	errorContextPattern()
}

// Error wrapping pattern
func errorWrappingPattern() {
	operation1 := func() error {
		return fmt.Errorf("operation 1 failed")
	}
	
	operation2 := func() error {
		if err := operation1(); err != nil {
			return fmt.Errorf("operation 2 failed: %w", err)
		}
		return nil
	}
	
	operation3 := func() error {
		if err := operation2(); err != nil {
			return fmt.Errorf("operation 3 failed: %w", err)
		}
		return nil
	}
	
	err := operation3()
	if err != nil {
		fmt.Printf("Wrapped error: %v\n", err)
		fmt.Printf("Unwrapped: %v\n", fmt.Errorf("cause: %w", err))
	}
}

// Error aggregation pattern
func errorAggregationPattern() {
	validate := func(data map[string]string) error {
		var errors []string
		
		if data["name"] == "" {
			errors = append(errors, "name is required")
		}
		if data["email"] == "" {
			errors = append(errors, "email is required")
		}
		if data["age"] == "" {
			errors = append(errors, "age is required")
		}
		
		if len(errors) > 0 {
			return fmt.Errorf("validation errors: %s", strings.Join(errors, ", "))
		}
		
		return nil
	}
	
	data := map[string]string{"name": "", "email": "", "age": ""}
	err := validate(data)
	
	if err != nil {
		fmt.Printf("Aggregated error: %v\n", err)
	}
}

// Error recovery pattern
func errorRecoveryPattern() {
	operation := func() (string, error) {
		return "", fmt.Errorf("operation failed")
	}
	
	retry := func(fn func() (string, error), maxAttempts int) (string, error) {
		var lastErr error
		
		for i := 0; i < maxAttempts; i++ {
			result, err := fn()
			if err == nil {
				return result, nil
			}
			
			lastErr = err
			fmt.Printf("Attempt %d failed: %v\n", i+1, err)
			time.Sleep(time.Duration(i+1) * time.Second)
		}
		
		return "", fmt.Errorf("all %d attempts failed, last error: %w", maxAttempts, lastErr)
	}
	
	result, err := retry(func() (string, error) {
		return "success", nil
	}, 3)
	
	if err != nil {
		fmt.Printf("Recovery failed: %v\n", err)
	} else {
		fmt.Printf("Recovery succeeded: %s\n", result)
	}
}

// Error context pattern
func errorContextPattern() {
	operation := func(ctx context.Context) error {
		select {
		case <-time.After(2 * time.Second):
			return fmt.Errorf("operation completed")
		case <-ctx.Done():
			return fmt.Errorf("operation cancelled: %w", ctx.Err())
		}
	}
	
	ctx, cancel := context.WithTimeout(context.Background(), 1*time.Second)
	defer cancel()
	
	err := operation(ctx)
	if err != nil {
		fmt.Printf("Context error: %v\n", err)
	}
}

// Testing patterns
func testingPatterns() {
	fmt.Println("Testing Patterns:")
	
	// Table-driven tests
	fmt.Println("\n1. Table-Driven Tests:")
	tableDrivenTests()
	
	// Test fixtures
	fmt.Println("\n2. Test Fixtures:")
	testFixtures()
	
	// Mock objects
	fmt.Println("\n3. Mock Objects:")
	mockObjects()
	
	// Test helpers
	fmt.Println("\n4. Test Helpers:")
	testHelpers()
}

// Table-driven tests
func tableDrivenTests() {
	type TestCase struct {
		name     string
		input    int
		expected int
	}
	
	testCases := []TestCase{
		{"positive", 5, 120},
		{"zero", 0, 1},
		{"negative", -1, 1},
	}
	
	factorial := func(n int) int {
		if n <= 0 {
			return 1
		}
		return n * factorial(n-1)
	}
	
	for _, tc := range testCases {
		result := factorial(tc.input)
		fmt.Printf("Test %s: input=%d, expected=%d, got=%d, pass=%v\n",
			tc.name, tc.input, tc.expected, result, result == tc.expected)
	}
}

// Test fixtures
func testFixtures() {
	type TestUser struct {
		ID    int
		Name  string
		Email string
	}
	
	createTestUser := func(id int, name, email string) TestUser {
		return TestUser{ID: id, Name: name, Email: email}
	}
	
	fixtures := map[string]TestUser{
		"john": createTestUser(1, "John Doe", "john@example.com"),
		"jane": createTestUser(2, "Jane Smith", "jane@example.com"),
	}
	
	for name, user := range fixtures {
		fmt.Printf("Fixture %s: %+v\n", name, user)
	}
}

// Mock objects
func mockObjects() {
	type Database interface {
		Get(id int) (string, error)
	}
	
	type MockDatabase struct {
		data map[int]string
	}
	
	NewMockDatabase := func() *MockDatabase {
		return &MockDatabase{
			data: map[int]string{
				1: "user1",
				2: "user2",
			},
		}
	}
	
	(db *MockDatabase) Get(id int) (string, error) {
		if data, exists := db.data[id]; exists {
			return data, nil
		}
		return "", fmt.Errorf("not found")
	}
	
	// Usage
	mockDB := NewMockDatabase()
	result, err := mockDB.Get(1)
	
	if err != nil {
		fmt.Printf("Mock error: %v\n", err)
	} else {
		fmt.Printf("Mock result: %s\n", result)
	}
}

// Test helpers
func testHelpers() {
	assertEqual := func(expected, actual interface{}) bool {
		return expected == actual
	}
	
	assertNotEqual := func(expected, actual interface{}) bool {
		return expected != actual
	}
	
	assertNil := fn func(actual interface{}) bool {
		return actual == nil
	}
	
	assertNotNil := func(actual interface{}) bool {
		return actual != nil
	}
	
	// Usage
	fmt.Printf("Assert equal: %v\n", assertEqual(5, 5))
	fmt.Printf("Assert not equal: %v\n", assertNotEqual(5, 3))
	fmt.Printf("Assert nil: %v\n", assertNil(nil))
	fmt.Printf("Assert not nil: %v\n", assertNotNil("test"))
}

// Memory management patterns
func memoryManagement() {
	fmt.Println("Memory Management Patterns:")
	
	// Buffer pooling
	fmt.Println("\n1. Buffer Pooling:")
	bufferPooling()
	
	// Memory profiling
	fmt.Println("\n2. Memory Profiling:")
	memoryProfiling()
	
	// Garbage collection tuning
	fmt.Println("\n3. Garbage Collection Tuning:")
	garbageCollectionTuning()
}

// Buffer pooling
func bufferPooling() {
	bufferPool := sync.Pool{
		New: func() interface{} {
			return make([]byte, 1024)
		},
	}
	
	process := func(data string) {
		buffer := bufferPool.Get().([]byte)
		defer bufferPool.Put(buffer[:0])
		
		copy(buffer, data)
		fmt.Printf("Processing: %s\n", string(buffer))
	}
	
	process("test data 1")
	process("test data 2")
}

// Memory profiling
func memoryProfiling() {
	fmt.Println("Memory profiling commands:")
	fmt.Println("  go tool pprof -http=:8080 http://localhost:6060/debug/pprof/heap")
	fmt.Println("  go tool pprof -http=:8080 http://localhost:6060/debug/pprof/allocs")
	fmt.Println("  go tool pprof -http=:8080 http://localhost:6060/debug/pprof/goroutine")
}

// Garbage collection tuning
func garbageCollectionTuning() {
	fmt.Println("GC tuning options:")
	fmt.Println("  GOGC=100 - GC target percentage (default 100)")
	fmt.Println("  GOMEMLIMIT=256MiB - Memory limit")
	fmt.Println("  GODEBUG=gctrace=1 - GC trace output")
	fmt.Println("  runtime.GC() - Force garbage collection")
	
	// Force GC
	runtime.GC()
}

// Resource management patterns
func resourceManagement() {
	fmt.Println("Resource Management Patterns:")
	
	// Resource cleanup
	fmt.Println("\n1. Resource Cleanup:")
	resourceCleanup()
	
	// Connection management
	fmt.Println("\n2. Connection Management:")
	connectionManagement()
	
	// File handling
	fmt.Println("\n3. File Handling:")
	fileHandling()
}

// Resource cleanup
func resourceCleanup() {
	// Using defer for cleanup
	createResource := func() func() {
		fmt.Println("Resource created")
		return func() {
			fmt.Println("Resource cleaned up")
		}
	}
	
	cleanup := createResource()
	defer cleanup()
	
	fmt.Println("Using resource")
}

// Connection management
func connectionManagement() {
	type Connection struct {
		id string
	}
	
	NewConnection := func(id string) *Connection {
		fmt.Printf("Connection %s created\n", id)
		return &Connection{id: id}
	}
	
	(conn *Connection) Close() {
		fmt.Printf("Connection %s closed\n", conn.id)
	}
	
	useConnection := func() {
		conn := NewConnection("conn1")
		defer conn.Close()
		
		fmt.Printf("Using connection %s\n", conn.id)
	}
	
	useConnection()
}

// File handling
func fileHandling() {
	processFile := func(filename string) error {
		// In real app, open and process file
		fmt.Printf("Processing file: %s\n", filename)
		return nil
	}
	
	filename := "test.txt"
	err := processFile(filename)
	
	if err != nil {
		fmt.Printf("File processing error: %v\n", err)
	}
}

// Logging patterns
func loggingPatterns() {
	fmt.Println("Logging Patterns:")
	
	// Structured logging
	fmt.Println("\n1. Structured Logging:")
	structuredLogging()
	
	// Contextual logging
	fmt.Println("\n2. Contextual Logging:")
	contextualLogging()
	
	// Performance logging
	fmt.Println("\n3. Performance Logging:")
	performanceLogging()
}

// Structured logging
func structuredLogging() {
	type LogEntry struct {
		Level   string
		Message string
		Context map[string]interface{}
		Time    time.Time
	}
	
	log := func(level, message string, context map[string]interface{}) {
		entry := LogEntry{
			Level:   level,
			Message: message,
			Context: context,
			Time:    time.Now(),
		}
		
		fmt.Printf("[%s] %s: %s %+v\n", entry.Time.Format("15:04:05"), entry.Level, entry.Message, entry.Context)
	}
	
	log("INFO", "User logged in", map[string]interface{}{"user_id": 123, "ip": "192.168.1.1"})
	log("ERROR", "Database connection failed", map[string]interface{}{"error": "timeout"})
}

// Contextual logging
func contextualLogging() {
	type Logger struct {
		context map[string]interface{}
	}
	
	NewLogger := func(context map[string]interface{}) *Logger {
		return &Logger{context: context}
	}
	
	(logger *Logger) WithContext(key string, value interface{}) *Logger {
		newContext := make(map[string]interface{})
		for k, v := range logger.context {
			newContext[k] = v
		}
		newContext[key] = value
		
		return &Logger{context: newContext}
	}
	
	(logger *Logger) Info(message string) {
		fmt.Printf("[INFO] %s %+v\n", message, logger.context)
	}
	
	// Usage
	logger := NewLogger(map[string]interface{}{"service": "user-api"})
	logger.WithContext("request_id", "req-123").Info("Processing request")
}

// Performance logging
func performanceLogging() {
	measure := func(name string, fn func()) time.Duration {
		start := time.Now()
		fn()
		duration := time.Since(start)
		fmt.Printf("[PERF] %s took %v\n", name, duration)
		return duration
	}
	
	measure("database query", func() {
		time.Sleep(100 * time.Millisecond)
	})
	
	measure("API call", func() {
		time.Sleep(200 * time.Millisecond)
	})
}

// Demonstrate all patterns
func demonstrateAllPatterns() {
	// Additional patterns could be demonstrated here
	fmt.Println("\n--- Additional Patterns ---")
	fmt.Println("1. Circuit Breaker - Already demonstrated")
	fmt.Println("2. Retry Pattern - Already demonstrated")
	fmt.Println("3. Timeout Pattern - Already demonstrated")
	fmt.Println("4. Worker Pool - Already demonstrated")
	fmt.Println("5. Pipeline - Already demonstrated")
	fmt.Println("6. Fan-In/Fan-Out - Already demonstrated")
}
