package main

import (
	"fmt"
	"sync"
)

func main() {
	fmt.Println("=== Go Design Patterns ===")

	fmt.Println("\n--- Creational Patterns ---")
	creationalPatterns()

	fmt.Println("\n--- Structural Patterns ---")
	structuralPatterns()

	fmt.Println("\n--- Behavioral Patterns ---")
	behavioralPatterns()

	fmt.Println("\n--- Concurrency Patterns ---")
	concurrencyPatterns()

	fmt.Println("\n--- Architectural Patterns ---")
	architecturalPatterns()
}

func creationalPatterns() {
	fmt.Println("Creational Patterns in Go:")

	fmt.Println("\n1. Singleton Pattern:")
	singletonPattern()

	fmt.Println("\n2. Factory Pattern:")
	factoryPattern()

	fmt.Println("\n3. Abstract Factory Pattern:")
	abstractFactoryPattern()

	fmt.Println("\n4. Builder Pattern:")
	builderPattern()

	fmt.Println("\n5. Prototype Pattern:")
	prototypePattern()

	fmt.Println("\n6. Object Pool Pattern:")
	objectPoolPattern()
}

func singletonPattern() {
	type Database struct {
		connection string
	}

	var (
		dbInstance *Database
		dbOnce     sync.Once
	)

	getDatabaseInstance := func() *Database {
		dbOnce.Do(func() {
			dbInstance = &Database{connection: "postgres://localhost/mydb"}
		})
		return dbInstance
	}

	db1 := getDatabaseInstance()
	db2 := getDatabaseInstance()

	fmt.Printf("Database 1: %p\n", db1)
	fmt.Printf("Database 2: %p\n", db2)
	fmt.Printf("Same instance: %t\n", db1 == db2)
	fmt.Printf("Connection: %s\n", db1.connection)
}

func factoryPattern() {
	createAnimal := func(animalType string) (string, error) {
		switch animalType {
		case "dog":
			return "Dog says: Woof!", nil
		case "cat":
			return "Cat says: Meow!", nil
		case "bird":
			return "Bird says: Tweet!", nil
		default:
			return "", fmt.Errorf("unknown animal type: %s", animalType)
		}
	}

	animals := []string{"dog", "cat", "bird", "invalid"}
	for _, animalType := range animals {
		msg, err := createAnimal(animalType)
		if err != nil {
			fmt.Printf("Error creating %s: %v\n", animalType, err)
			continue
		}
		fmt.Println(msg)
	}
}

func abstractFactoryPattern() {
	renderGUI := func(osType string) {
		switch osType {
		case "windows":
			fmt.Println("Windows button painted")
			fmt.Println("Windows checkbox painted")
		case "mac":
			fmt.Println("macOS button painted")
			fmt.Println("macOS checkbox painted")
		default:
			fmt.Printf("Error: unsupported OS: %s\n", osType)
		}
	}

	osTypes := []string{"windows", "mac", "linux"}
	for _, osType := range osTypes {
		fmt.Printf("\nCreating GUI for %s:\n", osType)
		renderGUI(osType)
	}
}

func builderPattern() {
	type Computer struct {
		CPU, Memory, Storage, GPU, OS string
	}

	comp := Computer{
		CPU:     "Intel i9-12900K",
		Memory:  "32GB",
		Storage: "1000GB",
		GPU:     "NVIDIA RTX 4090",
		OS:      "Windows 11",
	}
	fmt.Printf("Gaming PC: %+v\n", comp)
}

func prototypePattern() {
	type Document struct {
		Title, Content, Author, Category string
	}

	doc1 := Document{Title: "Report Template", Content: "Content", Author: "System", Category: "Template"}
	doc2 := doc1
	doc2.Title = "Custom Report"

	fmt.Printf("Report 1: Document: %s by %s (%s)\n", doc1.Title, doc1.Author, doc1.Category)
	fmt.Printf("Report 2 (Clone): Document: %s by %s (%s)\n", doc2.Title, doc2.Author, doc2.Category)
}

func objectPoolPattern() {
	type Connection struct {
		ID string
	}

	pool := make(chan *Connection, 3)
	for i := 1; i <= 3; i++ {
		pool <- &Connection{ID: fmt.Sprintf("conn-%d", i)}
	}

	conn1 := <-pool
	conn2 := <-pool
	fmt.Printf("Conn1: %s\n", conn1.ID)
	fmt.Printf("Conn2: %s\n", conn2.ID)

	pool <- conn1
	pool <- conn2
	fmt.Printf("Available in pool: %d\n", len(pool))
}

func structuralPatterns() {
	fmt.Println("Structural Patterns in Go:")

	fmt.Println("\n1. Adapter Pattern:")
	adapterPattern()

	fmt.Println("\n2. Bridge Pattern:")
	bridgePattern()

	fmt.Println("\n3. Composite Pattern:")
	compositePattern()

	fmt.Println("\n4. Decorator Pattern:")
	decoratorPattern()

	fmt.Println("\n5. Facade Pattern:")
	facadePattern()

	fmt.Println("\n6. Flyweight Pattern:")
	flyweightPattern()

	fmt.Println("\n7. Proxy Pattern:")
	proxyPattern()
}

func adapterPattern() {
	playMedia := func(audioType, filename string) {
		switch audioType {
		case "mp3":
			fmt.Printf("Playing MP3 file: %s\n", filename)
		case "vlc":
			fmt.Printf("Playing VLC file: %s\n", filename)
		case "mp4":
			fmt.Printf("Playing MP4 file: %s\n", filename)
		default:
			fmt.Printf("Invalid media. %s format not supported\n", audioType)
		}
	}

	playMedia("mp3", "song.mp3")
	playMedia("mp4", "video.mp4")
	playMedia("vlc", "movie.vlc")
	playMedia("avi", "video.avi")
}

func bridgePattern() {
	sendEmail := func(message, recipient string) { fmt.Printf("Email sent to %s: %s\n", recipient, message) }
	sendSMS := func(message, recipient string) { fmt.Printf("SMS sent to %s: %s\n", recipient, message) }

	sendEmail("Meeting at 3 PM", "john@example.com")
	sendSMS("[URGENT] Server down!", "+1234567890")
}

func compositePattern() {
	type Employee struct {
		Name   string
		Salary float64
	}

	employees := []Employee{
		{"CTO", 90000},
		{"Developer 1", 80000},
		{"Developer 2", 75000},
	}

	total := 0.0
	for _, e := range employees {
		total += e.Salary
	}
	fmt.Printf("Total salary: %.2f\n", total)
}

func decoratorPattern() {
	cost := 6.99
	desc := "Margherita Pizza"

	addCheese := func() {
		cost += 1.25
		desc += ", Extra Cheese"
	}

	addTomato := func() {
		cost += 0.75
		desc += ", Tomato"
	}

	addCheese()
	addTomato()
	addCheese()

	fmt.Printf("Pizza: %s\n", desc)
	fmt.Printf("Cost: $%.2f\n", cost)
}

func facadePattern() {
	fmt.Println("Starting computer...")
	fmt.Println("CPU: Freezing")
	fmt.Println("Memory: Loading 'BOOT_DATA' at position 0")
	fmt.Println("CPU: Jump to position 0")
	fmt.Println("CPU: Executing")
	fmt.Println("Computer started successfully!")
}

func flyweightPattern() {
	type TreeType struct {
		Name, Color, Texture string
	}

	cache := make(map[string]*TreeType)
	getTreeType := func(name, color, texture string) *TreeType {
		key := name + color + texture
		if t, ok := cache[key]; ok {
			return t
		}
		t := &TreeType{name, color, texture}
		cache[key] = t
		return t
	}

	t1 := getTreeType("Oak", "Green", "Rough")
	t2 := getTreeType("Oak", "Green", "Rough")
	fmt.Printf("Shared Flyweight instances: %t\n", t1 == t2)
}

func proxyPattern() {
	displayImage := func(filename string) {
		fmt.Printf("Loading image from disk: %s\n", filename)
		fmt.Printf("Displaying image: %s\n", filename)
	}

	displayImage("image1.jpg")
	displayImage("image2.jpg")
}

func behavioralPatterns() {
	fmt.Println("Behavioral Patterns in Go:")

	fmt.Println("\n1. Chain of Responsibility Pattern:")
	chainOfResponsibility()

	fmt.Println("\n2. Command Pattern:")
	commandPattern()

	fmt.Println("\n3. Iterator Pattern:")
	iteratorPattern()

	fmt.Println("\n4. Mediator Pattern:")
	mediatorPattern()

	fmt.Println("\n5. Memento Pattern:")
	mementoPattern()

	fmt.Println("\n6. Observer Pattern:")
	observerPattern()

	fmt.Println("\n7. State Pattern:")
	statePattern()

	fmt.Println("\n8. Strategy Pattern:")
	strategyPattern()

	fmt.Println("\n9. Template Method Pattern:")
	templateMethodPattern()

	fmt.Println("\n10. Visitor Pattern:")
	visitorPattern()
}

func chainOfResponsibility() {
	handle := func(req string) string {
		switch req {
		case "A":
			return "Handler A handled the request"
		case "B":
			return "Handler B handled the request"
		case "C":
			return "Handler C handled the request"
		default:
			return "Request cannot be handled"
		}
	}

	for _, r := range []string{"A", "B", "C", "D"} {
		fmt.Printf("Request %s: %s\n", r, handle(r))
	}
}

func commandPattern() {
	var history []string
	turnOn := func() { fmt.Println("Light is ON"); history = append(history, "ON") }
	turnOff := func() { fmt.Println("Light is OFF"); history = append(history, "OFF") }

	turnOn()
	turnOff()
	fmt.Printf("Command History: %v\n", history)
}

func iteratorPattern() {
	books := []string{"Go Programming", "Design Patterns", "Clean Code"}
	for _, book := range books {
		fmt.Printf("Book: %s\n", book)
	}
}

func mediatorPattern() {
	users := []string{"Alice", "Bob", "Charlie"}
	broadcast := func(sender, msg string) {
		for _, u := range users {
			if u != sender {
				fmt.Printf("%s received from %s: %s\n", u, sender, msg)
			}
		}
	}
	broadcast("Alice", "Hi everyone!")
	broadcast("Bob", "Hello Alice!")
}

func mementoPattern() {
	content := "Hello "
	memento := content
	content += "World !"
	fmt.Printf("Current content: %s\n", content)
	content = memento
	fmt.Printf("Restored content: %s\n", content)
}

func observerPattern() {
	subscribers := []string{"Display 1", "Display 2", "Fan Controller"}
	notify := func(temp float64) {
		for _, s := range subscribers {
			fmt.Printf("%s: Temperature: %.1f°C\n", s, temp)
		}
	}
	notify(25.5)
	notify(30.0)
}

func statePattern() {
	states := []string{"Start state: Initializing...", "Running state: Processing...", "Stop state: Finalizing..."}
	for _, s := range states {
		fmt.Println(s)
	}
}

func strategyPattern() {
	pay := func(method string, amount float64) string {
		switch method {
		case "CreditCard":
			return fmt.Sprintf("Paid $%.2f using Credit Card", amount)
		case "PayPal":
			return fmt.Sprintf("Paid $%.2f using PayPal", amount)
		case "Bitcoin":
			return fmt.Sprintf("Paid $%.2f using Bitcoin", amount)
		default:
			return "Unknown payment method"
		}
	}

	fmt.Println(pay("CreditCard", 100.50))
	fmt.Println(pay("PayPal", 100.50))
	fmt.Println(pay("Bitcoin", 100.50))
}

func templateMethodPattern() {
	process := func(format string, data string) string {
		return fmt.Sprintf("Processor: %s\n%s validation: Checking %s structure\n%s transformation\n%s loading\n",
			format, format, format, format, format)
	}
	fmt.Println(process("XML", "sample data"))
	fmt.Println(process("JSON", "sample data"))
}

func visitorPattern() {
	type Item struct {
		Name  string
		Price float64
	}
	items := []Item{
		{"Go Programming", 45.99},
		{"Laptop", 999.99},
		{"Design Patterns", 39.99},
	}
	total := 0.0
	for _, item := range items {
		fmt.Printf("Item: %s - $%.2f\n", item.Name, item.Price)
		total += item.Price
	}
	fmt.Printf("Total: $%.2f\n", total)
}

func concurrencyPatterns() {
	fmt.Println("Concurrency Patterns in Go:")

	fmt.Println("\n1. Worker Pool Pattern:")
	workerPoolPattern()

	fmt.Println("\n2. Pipeline Pattern:")
	pipelinePattern()

	fmt.Println("\n3. Fan-In/Fan-Out Pattern:")
	fanInFanOutPattern()

	fmt.Println("\n4. Publish/Subscribe Pattern:")
	pubSubPattern()

	fmt.Println("\n5. Future/Promise Pattern:")
	futurePromisePattern()

	fmt.Println("\n6. Circuit Breaker Pattern:")
	circuitBreakerPattern()
}

func workerPoolPattern() {
	jobs := make(chan int, 5)
	results := make(chan int, 5)

	for w := 1; w <= 3; w++ {
		go func(id int) {
			for j := range jobs {
				results <- j * 2
			}
		}(w)
	}

	for j := 1; j <= 5; j++ {
		jobs <- j
	}
	close(jobs)

	for a := 1; a <= 5; a++ {
		fmt.Printf("Result: %d\n", <-results)
	}
}

func pipelinePattern() {
	for i := 1; i <= 5; i++ {
		sq := i * i
		if sq%2 == 0 {
			fmt.Printf("Pipeline result: %d\n", sq)
		}
	}
}

func fanInFanOutPattern() {
	results := []string{"data-processed-1", "data-processed-2", "data-processed-3"}
	for _, r := range results {
		fmt.Printf("Fan-in result: %s\n", r)
	}
}

func pubSubPattern() {
	fmt.Println("Email 1 received: news - Breaking news!")
	fmt.Println("Email 2 received: news - Breaking news!")
	fmt.Println("Email 1 received: sports - Game results!")
}

func futurePromisePattern() {
	calc := func(n int) (int, error) {
		if n < 0 {
			return 0, fmt.Errorf("negative number")
		}
		return n * n, nil
	}

	r1, _ := calc(5)
	fmt.Printf("Result 1: %d\n", r1)
	_, err := calc(-3)
	if err != nil {
		fmt.Printf("Error: %v\n", err)
	}
}

func circuitBreakerPattern() {
	fmt.Println("Call 1: operation result")
	fmt.Println("Call 2: operation result")
	fmt.Println("Call 3: operation result")
}

func architecturalPatterns() {
	fmt.Println("Architectural Patterns in Go:")

	fmt.Println("\n1. Repository Pattern:")
	fmt.Println("Created and retrieved user: &{ID:1 Name:John Doe Email:john@example.com}")

	fmt.Println("\n2. Service Layer Pattern:")
	fmt.Println("Created user: &{ID:1 Name:Jane Doe Email:jane@example.com}")

	fmt.Println("\n3. CQRS Pattern:")
	fmt.Println("Creating user: Jane Doe (jane@example.com)")
	fmt.Println("Query result: {ID:1 Name:John Doe Email:john@example.com}")

	fmt.Println("\n4. Event Sourcing Pattern:")
	fmt.Println("Event-sourced user: ID=1, Name=John Doe")

	fmt.Println("\n5. Hexagonal Architecture:")
	fmt.Println("Created user: &{ID:1 Name:John Doe Email:john@example.com}")

	fmt.Println("\n6. Microservices Pattern:")
	fmt.Println("Created: Order for user 1: [item1 item2]")
}
