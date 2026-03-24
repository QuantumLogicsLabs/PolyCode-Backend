package main

import (
	"fmt"
	"sync"
	"time"
)

func main() {
	fmt.Println("=== Go Design Patterns ===")
	
	// Creational patterns
	fmt.Println("\n--- Creational Patterns ---")
	creationalPatterns()
	
	// Structural patterns
	fmt.Println("\n--- Structural Patterns ---")
	structuralPatterns()
	
	// Behavioral patterns
	fmt.Println("\n--- Behavioral Patterns ---")
	behavioralPatterns()
	
	// Concurrency patterns
	fmt.Println("\n--- Concurrency Patterns ---")
	concurrencyPatterns()
	
	// Architectural patterns
	fmt.Println("\n--- Architectural Patterns ---")
	architecturalPatterns()
}

// Creational patterns
func creationalPatterns() {
	fmt.Println("Creational Patterns in Go:")
	
	// Singleton pattern
	fmt.Println("\n1. Singleton Pattern:")
	singletonPattern()
	
	// Factory pattern
	fmt.Println("\n2. Factory Pattern:")
	factoryPattern()
	
	// Abstract factory pattern
	fmt.Println("\n3. Abstract Factory Pattern:")
	abstractFactoryPattern()
	
	// Builder pattern
	fmt.Println("\n4. Builder Pattern:")
	builderPattern()
	
	// Prototype pattern
	fmt.Println("\n5. Prototype Pattern:")
	prototypePattern()
	
	// Object pool pattern
	fmt.Println("\n6. Object Pool Pattern:")
	objectPoolPattern()
}

// Singleton pattern
func singletonPattern() {
	// Thread-safe singleton using sync.Once
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
	
	// Usage
	db1 := getDatabaseInstance()
	db2 := getDatabaseInstance()
	
	fmt.Printf("Database 1: %p\n", db1)
	fmt.Printf("Database 2: %p\n", db2)
	fmt.Printf("Same instance: %t\n", db1 == db2)
	fmt.Printf("Connection: %s\n", db1.connection)
}

// Factory pattern
func factoryPattern() {
	// Product interface
	type Animal interface {
		Speak() string
		Type() string
	}
	
	// Concrete products
	type Dog struct{}
	func (d Dog) Speak() string { return "Woof!" }
	func (d Dog) Type() string { return "Dog" }
	
	type Cat struct{}
	func (c Cat) Speak() string { return "Meow!" }
	func (c Cat) Type() string { return "Cat" }
	
	type Bird struct{}
	func (b Bird) Speak() string { return "Tweet!" }
	func (b Bird) Type() string { return "Bird" }
	
	// Factory function
	createAnimal := func(animalType string) (Animal, error) {
		switch animalType {
		case "dog":
			return Dog{}, nil
		case "cat":
			return Cat{}, nil
		case "bird":
			return Bird{}, nil
		default:
			return nil, fmt.Errorf("unknown animal type: %s", animalType)
		}
	}
	
	// Usage
	animals := []string{"dog", "cat", "bird", "invalid"}
	
	for _, animalType := range animals {
		animal, err := createAnimal(animalType)
		if err != nil {
			fmt.Printf("Error creating %s: %v\n", animalType, err)
			continue
		}
		
		fmt.Printf("%s says: %s\n", animal.Type(), animal.Speak())
	}
}

// Abstract factory pattern
func abstractFactoryPattern() {
	// Abstract factory interface
	type GUIFactory interface {
		CreateButton() Button
		CreateCheckbox() Checkbox
	}
	
	// Abstract products
	type Button interface {
		Paint()
	}
	
	type Checkbox interface {
		Paint()
	}
	
	// Windows implementation
	type WindowsButton struct{}
	func (wb WindowsButton) Paint() { fmt.Println("Windows button painted") }
	
	type WindowsCheckbox struct{}
	func (wc WindowsCheckbox) Paint() { fmt.Println("Windows checkbox painted") }
	
	type WindowsFactory struct{}
	func (wf WindowsFactory) CreateButton() Button {
		return WindowsButton{}
	}
	func (wf WindowsFactory) CreateCheckbox() Checkbox {
		return WindowsCheckbox{}
	}
	
	// macOS implementation
	type MacButton struct{}
	func (mb MacButton) Paint() { fmt.Println("macOS button painted") }
	
	type MacCheckbox struct{}
	func (mc MacCheckbox) Paint() { fmt.Println("macOS checkbox painted") }
	
	type MacFactory struct{}
	func (mf MacFactory) CreateButton() Button {
		return MacButton{}
	}
	func (mf MacFactory) CreateCheckbox() Checkbox {
		return MacCheckbox{}
	}
	
	// Factory creator
	createGUIFactory := func(osType string) (GUIFactory, error) {
		switch osType {
		case "windows":
			return WindowsFactory{}, nil
		case "mac":
			return MacFactory{}, nil
		default:
			return nil, fmt.Errorf("unsupported OS: %s", osType)
		}
	}
	
	// Usage
	osTypes := []string{"windows", "mac", "linux"}
	
	for _, osType := range osTypes {
		fmt.Printf("\nCreating GUI for %s:\n", osType)
		
		factory, err := createGUIFactory(osType)
		if err != nil {
			fmt.Printf("Error: %v\n", err)
			continue
		}
		
		button := factory.CreateButton()
		checkbox := factory.CreateCheckbox()
		
		button.Paint()
		checkbox.Paint()
	}
}

// Builder pattern
func builderPattern() {
	// Product
	type Computer struct {
		CPU     string
		Memory  int
		Storage int
		GPU     string
		OS      string
	}
	
	// Builder interface
	type ComputerBuilder interface {
		SetCPU(cpu string)
		SetMemory(memory int)
		SetStorage(storage int)
		SetGPU(gpu string)
		SetOS(os string)
		Build() Computer
	}
	
	// Concrete builder
	type GamingComputerBuilder struct {
		computer Computer
	}
	
	NewGamingComputerBuilder() *GamingComputerBuilder {
		return &GamingComputerBuilder{}
	}
	
	(gcb *GamingComputerBuilder) SetCPU(cpu string) {
		gcb.computer.CPU = cpu
	}
	
	(gcb *GamingComputerBuilder) SetMemory(memory int) {
		gcb.computer.Memory = memory
	}
	
	(gcb *GamingComputerBuilder) SetStorage(storage int) {
		gcb.computer.Storage = storage
	}
	
	(gcb *GamingComputerBuilder) SetGPU(gpu string) {
		gcb.computer.GPU = gpu
	}
	
	(gcb *GamingComputerBuilder) SetOS(os string) {
		gcb.computer.OS = os
	}
	
	(gcb *GamingComputerBuilder) Build() Computer {
		return gcb.computer
	}
	
	// Director
	type ComputerDirector struct {
		builder ComputerBuilder
	}
	
	NewComputerDirector(builder ComputerBuilder) *ComputerDirector {
		return &ComputerDirector{builder: builder}
	}
	
	(cd *ComputerDirector) BuildGamingPC() Computer {
		cd.builder.SetCPU("Intel i9-12900K")
		cd.builder.SetMemory(32)
		cd.builder.SetStorage(1000)
		cd.builder.SetGPU("NVIDIA RTX 4090")
		cd.builder.SetOS("Windows 11")
		return cd.builder.Build()
	}
	
	(cd *ComputerDirector) BuildOfficePC() Computer {
		cd.builder.SetCPU("Intel i5-12400")
		cd.builder.SetMemory(16)
		cd.builder.SetStorage(512)
		cd.builder.SetGPU("Integrated")
		cd.builder.SetOS("Windows 11")
		return cd.builder.Build()
	}
	
	// Usage
	builder := NewGamingComputerBuilder()
	director := NewComputerDirector(builder)
	
	gamingPC := director.BuildGamingPC()
	fmt.Printf("Gaming PC: %+v\n", gamingPC)
	
	officePC := director.BuildOfficePC()
	fmt.Printf("Office PC: %+v\n", officePC)
}

// Prototype pattern
func prototypePattern() {
	// Prototype interface
	type Prototype interface {
		Clone() Prototype
		GetDetails() string
	}
	
	// Concrete prototype
	type Document struct {
		Title    string
		Content  string
		Author   string
		Category string
	}
	
	NewDocument(title, content, author, category string) *Document {
		return &Document{
			Title:    title,
			Content:  content,
			Author:   author,
			Category: category,
		}
	}
	
	(doc *Document) Clone() Prototype {
		return &Document{
			Title:    doc.Title,
			Content:  doc.Content,
			Author:   doc.Author,
			Category: doc.Category,
		}
	}
	
	(doc *Document) GetDetails() string {
		return fmt.Sprintf("Document: %s by %s (%s)", doc.Title, doc.Author, doc.Category)
	}
	
	// Prototype manager
	type DocumentManager struct {
		prototypes map[string]Prototype
	}
	
	NewDocumentManager() *DocumentManager {
		return &DocumentManager{
			prototypes: make(map[string]Prototype),
		}
	}
	
	(dm *DocumentManager) AddPrototype(key string, prototype Prototype) {
		dm.prototypes[key] = prototype
	}
	
	(dm *DocumentManager) GetPrototype(key string) (Prototype, error) {
		prototype, exists := dm.prototypes[key]
		if !exists {
			return nil, fmt.Errorf("prototype not found: %s", key)
		}
		return prototype.Clone(), nil
	}
	
	// Usage
	manager := NewDocumentManager()
	
	// Add prototypes
	reportTemplate := NewDocument("Report Template", "Report content", "System", "Template")
	contractTemplate := NewDocument("Contract Template", "Contract content", "Legal", "Template")
	
	manager.AddPrototype("report", reportTemplate)
	manager.AddPrototype("contract", contractTemplate)
	
	// Create documents from prototypes
	reportClone, _ := manager.GetPrototype("report")
	contractClone, _ := manager.GetPrototype("contract")
	
	fmt.Printf("Report: %s\n", reportClone.GetDetails())
	fmt.Printf("Contract: %s\n", contractClone.GetDetails())
}

// Object pool pattern
func objectPoolPattern() {
	// Pooled object
	type Connection struct {
		ID     string
		InUse  bool
		Closed bool
	}
	
	NewConnection := func(id string) *Connection {
		return &Connection{ID: id}
	}
	
	(conn *Connection) Close() {
		conn.Closed = true
		fmt.Printf("Connection %s closed\n", conn.ID)
	}
	
	// Object pool
	type ConnectionPool struct {
		pool     chan *Connection
		maxSize  int
		created  int
		mu       sync.Mutex
	}
	
	NewConnectionPool := func(maxSize int) *ConnectionPool {
		return &ConnectionPool{
			pool:    make(chan *Connection, maxSize),
			maxSize: maxSize,
		}
	}
	
	(cp *ConnectionPool) Get() *Connection {
		select {
		case conn := <-cp.pool:
			return conn
		default:
			cp.mu.Lock()
			if cp.created < cp.maxSize {
				cp.created++
				cp.mu.Unlock()
				return NewConnection(fmt.Sprintf("conn-%d", cp.created))
			}
			cp.mu.Unlock()
			return nil
		}
	}
	
	(cp *ConnectionPool) Put(conn *Connection) {
		if conn.Closed {
			return
		}
		
		select {
		case cp.pool <- conn:
		default:
			// Pool is full, discard connection
		}
	}
	
	(cp *ConnectionPool) Stats() (int, int) {
		cp.mu.Lock()
		defer cp.mu.Unlock()
		return cp.created, len(cp.pool)
	}
	
	// Usage
	pool := NewConnectionPool(3)
	
	// Get connections
	conn1 := pool.Get()
	conn2 := pool.Get()
	conn3 := pool.Get()
	conn4 := pool.Get() // Should return nil (pool exhausted)
	
	fmt.Printf("Conn1: %v\n", conn1 != nil)
	fmt.Printf("Conn2: %v\n", conn2 != nil)
	fmt.Printf("Conn3: %v\n", conn3 != nil)
	fmt.Printf("Conn4: %v\n", conn4 != nil)
	
	// Return connections
	pool.Put(conn1)
	pool.Put(conn2)
	
	// Get again
	conn5 := pool.Get()
	fmt.Printf("Conn5: %v\n", conn5 != nil)
	
	// Stats
	created, available := pool.Stats()
	fmt.Printf("Created: %d, Available: %d\n", created, available)
}

// Structural patterns
func structuralPatterns() {
	fmt.Println("Structural Patterns in Go:")
	
	// Adapter pattern
	fmt.Println("\n1. Adapter Pattern:")
	adapterPattern()
	
	// Bridge pattern
	fmt.Println("\n2. Bridge Pattern:")
	bridgePattern()
	
	// Composite pattern
	fmt.Println("\n3. Composite Pattern:")
	compositePattern()
	
	// Decorator pattern
	fmt.Println("\n4. Decorator Pattern:")
	decoratorPattern()
	
	// Facade pattern
	fmt.Println("\n5. Facade Pattern:")
	facadePattern()
	
	// Flyweight pattern
	fmt.Println("\n6. Flyweight Pattern:")
	flyweightPattern()
	
	// Proxy pattern
	fmt.Println("\n7. Proxy Pattern:")
	proxyPattern()
}

// Adapter pattern
func adapterPattern() {
	// Target interface
	type MediaPlayer interface {
		Play(audioType, filename string)
	}
	
	// Adaptee
	type AdvancedMediaPlayer interface {
		PlayVlc(filename string)
		PlayMp4(filename string)
	}
	
	type VlcPlayer struct{}
	func (vp VlcPlayer) PlayVlc(filename string) {
		fmt.Printf("Playing VLC file: %s\n", filename)
	}
	func (vp VlcPlayer) PlayMp4(filename string) {
		fmt.Printf("Playing MP4 file: %s\n", filename)
	}
	
	// Adapter
	type MediaAdapter struct {
		advancedMusicPlayer AdvancedMediaPlayer
	}
	
	NewMediaAdapter := func(advancedMusicPlayer AdvancedMediaPlayer) *MediaAdapter {
		return &MediaAdapter{advancedMusicPlayer: advancedMusicPlayer}
	}
	
	(ma *MediaAdapter) Play(audioType, filename string) {
		if audioType == "vlc" {
			ma.advancedMusicPlayer.PlayVlc(filename)
		} else if audioType == "mp4" {
			ma.advancedMusicPlayer.PlayMp4(filename)
		}
	}
	
	// Concrete target
	type AudioPlayer struct {
		mediaAdapter *MediaAdapter
	}
	
	NewAudioPlayer() *AudioPlayer {
		return &AudioPlayer{}
	}
	
	(ap *AudioPlayer) Play(audioType, filename string) {
		if audioType == "mp3" {
			fmt.Printf("Playing MP3 file: %s\n", filename)
		} else if audioType == "vlc" || audioType == "mp4" {
			if ap.mediaAdapter == nil {
				ap.mediaAdapter = NewMediaAdapter(VlcPlayer{})
			}
			ap.mediaAdapter.Play(audioType, filename)
		} else {
			fmt.Printf("Invalid media. %s format not supported\n", audioType)
		}
	}
	
	// Usage
	player := NewAudioPlayer()
	player.Play("mp3", "song.mp3")
	player.Play("mp4", "video.mp4")
	player.Play("vlc", "movie.vlc")
	player.Play("avi", "video.avi")
}

// Bridge pattern
func bridgePattern() {
	// Implementor interface
	type MessageSender interface {
		Send(message string, recipient string)
	}
	
	// Concrete implementors
	type EmailMessageSender struct{}
	func (ems EmailMessageSender) Send(message, recipient string) {
		fmt.Printf("Email sent to %s: %s\n", recipient, message)
	}
	
	type SMSMessageSender struct{}
	func (sms SMSMessageSender) Send(message, recipient string) {
		fmt.Printf("SMS sent to %s: %s\n", recipient, message)
	}
	
	// Abstraction
	type Message struct {
		sender  MessageSender
		content string
	}
	
	NewMessage(sender MessageSender, content string) *Message {
		return &Message{sender: sender, sender: sender, content: content}
	}
	
	(m *Message) Send(recipient string) {
		m.sender.Send(m.content, recipient)
	}
	
	// Refined abstractions
	type UrgentMessage struct {
		Message
	}
	
	NewUrgentMessage(sender MessageSender, content string) *UrgentMessage {
		return &UrgentMessage{Message: *NewMessage(sender, content)}
	}
	
	(um *UrgentMessage) Send(recipient string) {
		um.sender.Send(fmt.Sprintf("[URGENT] %s", um.content), recipient)
	}
	
	type NormalMessage struct {
		Message
	}
	
	NewNormalMessage(sender MessageSender, content string) *NormalMessage {
		return &NormalMessage{Message: *NewMessage(sender, content)}
	}
	
	// Usage
	emailSender := EmailMessageSender{}
	smsSender := SMSMessageSender{}
	
	normalEmail := NewNormalMessage(emailSender, "Meeting at 3 PM")
	urgentSMS := NewUrgentMessage(smsSender, "Server down!")
	
	normalEmail.Send("john@example.com")
	urgentSMS.Send("+1234567890")
}

// Composite pattern
func compositePattern() {
	// Component interface
	type Employee interface {
		Name() string
		Salary() float64
		Add(Employee)
		Remove(Employee)
		GetSubordinates() []Employee
	}
	
	// Leaf component
	type Developer struct {
		name   string
		salary float64
	}
	
	NewDeveloper(name string, salary float64) *Developer {
		return &Developer{name: name, salary: salary}
	}
	
	(d *Developer) Name() string { return d.name }
	(d *Developer) Salary() float64 { return d.salary }
	(d *Developer) Add(Employee)     {}
	(d *Developer) Remove(Employee)  {}
	(d *Developer) GetSubordinates() []Employee { return nil }
	
	// Composite component
	type Manager struct {
		name         string
		salary       float64
		subordinates []Employee
	}
	
	NewManager(name string, salary float64) *Manager {
		return &Manager{
			name:         name,
			salary:       salary,
			subordinates: make([]Employee, 0),
		}
	}
	
	(m *Manager) Name() string { return m.name }
	(m *Manager) Salary() float64 { return m.salary }
	
	(m *Manager) Add(employee Employee) {
		m.subordinates = append(m.subordinates, employee)
	}
	
	(m *Manager) Remove(employee Employee) {
		for i, sub := range m.subordinates {
			if sub.Name() == employee.Name() {
				m.subordinates = append(m.subordinates[:i], m.subordinates[i+1:]...)
				break
			}
		}
	}
	
	(m *Manager) GetSubordinates() []Employee {
		return m.subordinates
	}
	
	// Helper function to calculate total salary
	getTotalSalary := func(employee Employee) float64 {
		total := employee.Salary()
		
		for _, subordinate := range employee.GetSubordinates() {
			total += getTotalSalary(subordinate)
		}
		
		return total
	}
	
	// Usage
	ceo := NewManager("CEO", 100000)
	
	cto := NewManager("CTO", 90000)
	ceo.Add(cto)
	
	dev1 := NewDeveloper("Developer 1", 80000)
	dev2 := NewDeveloper("Developer 2", 75000)
	cto.Add(dev1)
	cto.Add(dev2)
	
	cfo := NewManager("CFO", 95000)
	ceo.Add(cfo)
	
	accountant := NewDeveloper("Accountant", 70000)
	cfo.Add(accountant)
	
	fmt.Printf("Total salary: %.2f\n", getTotalSalary(ceo))
}

// Decorator pattern
func decoratorPattern() {
	// Component interface
	type Pizza interface {
		GetDescription() string
		GetCost() float64
	}
	
	// Concrete component
	type MargheritaPizza struct{}
	
	(mp MargheritaPizza) GetDescription() string {
		return "Margherita Pizza"
	}
	
	(mp MargheritaPizza) GetCost() float64 {
		return 6.99
	}
	
	// Decorator
	type PizzaDecorator struct {
		pizza Pizza
	}
	
	NewPizzaDecorator(pizza Pizza) *PizzaDecorator {
		return &PizzaDecorator{pizza: pizza}
	}
	
	(pd *PizzaDecorator) GetDescription() string {
		return pd.pizza.GetDescription()
	}
	
	(pd *PizzaDecorator) GetCost() float64 {
		return pd.pizza.GetCost()
	}
	
	// Concrete decorators
	type ExtraCheeseTopping struct {
		PizzaDecorator
	}
	
	NewExtraCheeseTopping(pizza Pizza) *ExtraCheeseTopping {
		return &ExtraCheeseTopping{PizzaDecorator: *NewPizzaDecorator(pizza)}
	}
	
	(ect *ExtraCheeseTopping) GetDescription() string {
		return ect.pizza.GetDescription() + ", Extra Cheese"
	}
	
	(ect *ExtraCheeseTopping) GetCost() float64 {
		return ect.pizza.GetCost() + 1.25
	}
	
	type TomatoTopping struct {
		PizzaDecorator
	}
	
	NewTomatoTopping(pizza Pizza) *TomatoTopping {
		return &TomatoTopping{PizzaDecorator: *NewPizzaDecorator(pizza)}
	}
	
	(tt *TomatoTopping) GetDescription() string {
		return tt.pizza.GetDescription() + ", Tomato"
	}
	
	(tt *TomatoTopping) GetCost() float64 {
		return tt.pizza.GetCost() + 0.75
	}
	
	// Usage
	pizza := MargheritaPizza{}
	
	pizza = NewExtraCheeseTopping(pizza)
	pizza = NewTomatoTopping(pizza)
	pizza = NewExtraCheeseTopping(pizza) // Add extra cheese again
	
	fmt.Printf("Pizza: %s\n", pizza.GetDescription())
	fmt.Printf("Cost: $%.2f\n", pizza.GetCost())
}

// Facade pattern
func facadePattern() {
	// Complex subsystems
	type CPU struct{}
	func (c *CPU) Freeze()    { fmt.Println("CPU: Freezing") }
	func (c *CPU) Jump(position int) { fmt.Printf("CPU: Jump to position %d\n", position) }
	func (c *CPU) Execute() { fmt.Println("CPU: Executing") }
	
	type Memory struct{}
	func (m *Memory) Load(position int, data string) {
		fmt.Printf("Memory: Loading '%s' at position %d\n", data, position)
	}
	func (m *Memory) Free(position int) {
		fmt.Printf("Memory: Freeing position %d\n", position)
	}
	
	type HardDrive struct{}
	func (hd *HardDrive) Read(lba, size int) string {
		return fmt.Sprintf("Data from LBA %d, size %d", lba, size)
	}
	
	// Facade
	type ComputerFacade struct {
		cpu       *CPU
		memory    *Memory
		hardDrive *HardDrive
	}
	
	NewComputerFacade() *ComputerFacade {
		return &ComputerFacade{
			cpu:       &CPU{},
			memory:    &Memory{},
			hardDrive: &HardDrive{},
		}
	}
	
	(cf *ComputerFacade) Start() {
		fmt.Println("Starting computer...")
		
		cf.cpu.Freeze()
		cf.memory.Load(0, "BOOT_DATA")
		cf.cpu.Jump(0)
		cf.cpu.Execute()
		cf.memory.Free(0)
		
		fmt.Println("Computer started successfully!")
	}
	
	// Usage
	computer := NewComputerFacade()
	computer.Start()
}

// Flyweight pattern
func flyweightPattern() {
	// Flyweight interface
	type Tree interface {
		Display(x, y int)
	}
	
	// Concrete flyweight
	type TreeType struct {
		name     string
		color    string
		texture  string
	}
	
	NewTreeType(name, color, texture string) *TreeType {
		return &TreeType{name: name, color: color, texture: texture}
	}
	
	(tt *TreeType) Display(x, y int) {
		fmt.Printf("Tree: %s, Color: %s, Texture: %s at (%d, %d)\n", 
			tt.name, tt.color, tt.texture, x, y)
	}
	
	// Flyweight factory
	type TreeFactory struct {
		treeTypes map[string]*TreeType
	}
	
	NewTreeFactory() *TreeFactory {
		return &TreeFactory{treeTypes: make(map[string]*TreeType)}
	}
	
	(tf *TreeFactory) GetTreeType(name, color, texture string) *TreeType {
		key := fmt.Sprintf("%s-%s-%s", name, color, texture)
		
		if treeType, exists := tf.treeTypes[key]; exists {
			return treeType
		}
		
		treeType := NewTreeType(name, color, texture)
		tf.treeTypes[key] = treeType
		return treeType
	}
	
	// Context (extrinsic state)
	type TreeContext struct {
		treeType *TreeType
		x        int
		y        int
	}
	
	NewTreeContext(treeType *TreeType, x, y int) *TreeContext {
		return &TreeContext{treeType: treeType, x: x, y: y}
	}
	
	(tc *TreeContext) Display() {
		tc.treeType.Display(tc.x, tc.y)
	}
	
	// Usage
	factory := NewTreeFactory()
	
	var trees []*TreeContext
	
	// Create many trees with shared tree types
	treeTypes := []struct {
		name    string
		color   string
		texture string
	}{
		{"Oak", "Green", "Rough"},
		{"Pine", "Dark Green", "Smooth"},
		{"Oak", "Green", "Rough"}, // Reuse
		{"Birch", "White", "Smooth"},
		{"Pine", "Dark Green", "Smooth"}, // Reuse
	}
	
	for i, tt := range treeTypes {
		treeType := factory.GetTreeType(tt.name, tt.color, tt.texture)
		tree := NewTreeContext(treeType, i*10, i*15)
		trees = append(trees, tree)
	}
	
	fmt.Printf("Created %d trees with %d unique tree types\n", len(trees), len(factory.treeTypes))
	
	// Display all trees
	for _, tree := range trees {
		tree.Display()
	}
}

// Proxy pattern
func proxyPattern() {
	// Subject interface
	type Image interface {
		Display()
	}
	
	// Real subject
	type RealImage struct {
		filename string
	}
	
	NewRealImage(filename string) *RealImage {
		fmt.Printf("Loading image from disk: %s\n", filename)
		time.Sleep(100 * time.Millisecond) // Simulate loading time
		return &RealImage{filename: filename}
	}
	
	(ri *RealImage) Display() {
		fmt.Printf("Displaying image: %s\n", ri.filename)
	}
	
	// Proxy
	type ProxyImage struct {
		filename string
		realImage *RealImage
	}
	
	NewProxyImage(filename string) *ProxyImage {
		return &ProxyImage{filename: filename}
	}
	
	(pi *ProxyImage) Display() {
		if pi.realImage == nil {
			pi.realImage = NewRealImage(pi.filename)
		}
		pi.realImage.Display()
	}
	
	// Usage
	image1 := NewProxyImage("image1.jpg")
	image2 := NewProxyImage("image2.jpg")
	
	// First display (will load images)
	fmt.Println("First display:")
	image1.Display()
	image2.Display()
	
	// Second display (images already loaded)
	fmt.Println("\nSecond display:")
	image1.Display()
	image2.Display()
}

// Behavioral patterns
func behavioralPatterns() {
	fmt.Println("Behavioral Patterns in Go:")
	
	// Chain of responsibility
	fmt.Println("\n1. Chain of Responsibility Pattern:")
	chainOfResponsibility()
	
	// Command pattern
	fmt.Println("\n2. Command Pattern:")
	commandPattern()
	
	// Iterator pattern
	fmt.Println("\n3. Iterator Pattern:")
	iteratorPattern()
	
	// Mediator pattern
	fmt.Println("\n4. Mediator Pattern:")
	mediatorPattern()
	
	// Memento pattern
	fmt.Println("\n5. Memento Pattern:")
	mementoPattern()
	
	// Observer pattern
	fmt.Println("\n6. Observer Pattern:")
	observerPattern()
	
	// State pattern
	fmt.Println("\n7. State Pattern:")
	statePattern()
	
	// Strategy pattern
	fmt.Println("\n8. Strategy Pattern:")
	strategyPattern()
	
	// Template method pattern
	fmt.Println("\n9. Template Method Pattern:")
	templateMethodPattern()
	
	// Visitor pattern
	fmt.Println("\n10. Visitor Pattern:")
	visitorPattern()
}

// Chain of responsibility pattern
func chainOfResponsibility() {
	// Handler interface
	type Handler interface {
		SetNext(handler Handler)
		Handle(request string) string
	}
	
	// Base handler
	type BaseHandler struct {
		next Handler
	}
	
	(bh *BaseHandler) SetNext(handler Handler) {
		bh.next = handler
	}
	
	(bh *BaseHandler) Handle(request string) string {
		if bh.next != nil {
			return bh.next.Handle(request)
		}
		return "Request cannot be handled"
	}
	
	// Concrete handlers
	type HandlerA struct {
		BaseHandler
	}
	
	NewHandlerA() *HandlerA {
		return &HandlerA{}
	}
	
	(ha *HandlerA) Handle(request string) string {
		if request == "A" {
			return "Handler A handled the request"
		}
		return ha.BaseHandler.Handle(request)
	}
	
	type HandlerB struct {
		BaseHandler
	}
	
	NewHandlerB() *HandlerB {
		return &HandlerB{}
	}
	
	(hb *HandlerB) Handle(request string) string {
		if request == "B" {
			return "Handler B handled the request"
		}
		return hb.BaseHandler.Handle(request)
	}
	
	type HandlerC struct {
		BaseHandler
	}
	
	NewHandlerC() *HandlerC {
		return &HandlerC{}
	}
	
	(hc *HandlerC) Handle(request string) string {
		if request == "C" {
			return "Handler C handled the request"
		}
		return hc.BaseHandler.Handle(request)
	}
	
	// Usage
	handlerA := NewHandlerA()
	handlerB := NewHandlerB()
	handlerC := NewHandlerC()
	
	handlerA.SetNext(handlerB)
	handlerB.SetNext(handlerC)
	
	requests := []string{"A", "B", "C", "D"}
	
	for _, request := range requests {
		result := handlerA.Handle(request)
		fmt.Printf("Request %s: %s\n", request, result)
	}
}

// Command pattern
func commandPattern() {
	// Command interface
	type Command interface {
		Execute()
		Undo()
	}
	
	// Receiver
	type Light struct {
		isOn bool
	}
	
	NewLight() *Light {
		return &Light{isOn: false}
	}
	
	(l *Light) TurnOn()  { l.isOn = true; fmt.Println("Light is ON") }
	(l *Light) TurnOff() { l.isOn = false; fmt.Println("Light is OFF") }
	
	// Concrete commands
	type LightOnCommand struct {
		light *Light
	}
	
	NewLightOnCommand(light *Light) *LightOnCommand {
		return &LightOnCommand{light: light}
	}
	
	(loc *LightOnCommand) Execute() {
		loc.light.TurnOn()
	}
	
	(loc *LightOnCommand) Undo() {
		loc.light.TurnOff()
	}
	
	type LightOffCommand struct {
		light *Light
	}
	
	NewLightOffCommand(light *Light) *LightOffCommand {
		return &LightOffCommand{light: light}
	}
	
	(lof *LightOffCommand) Execute() {
		lof.light.TurnOff()
	}
	
	(lof *LightOffCommand) Undo() {
		lof.light.TurnOn()
	}
	
	// Invoker
	type RemoteControl struct {
		command Command
		history []Command
	}
	
	NewRemoteControl() *RemoteControl {
		return &RemoteControl{history: make([]Command, 0)}
	}
	
	(rc *RemoteControl) SetCommand(command Command) {
		rc.command = command
	}
	
	(rc *RemoteControl) PressButton() {
		if rc.command != nil {
			rc.command.Execute()
			rc.history = append(rc.history, rc.command)
		}
	}
	
	(rc *RemoteControl) PressUndo() {
		if len(rc.history) > 0 {
			lastCommand := rc.history[len(rc.history)-1]
			lastCommand.Undo()
			rc.history = rc.history[:len(rc.history)-1]
		}
	}
	
	// Usage
	light := NewLight()
	remote := NewRemoteControl()
	
	lightOn := NewLightOnCommand(light)
	lightOff := NewLightOffCommand(light)
	
	remote.SetCommand(lightOn)
	remote.PressButton()
	
	remote.SetCommand(lightOff)
	remote.PressButton()
	
	remote.PressUndo()
	remote.PressUndo()
}

// Iterator pattern
func iteratorPattern() {
	// Iterator interface
	type Iterator interface {
		HasNext() bool
		Next() interface{}
	}
	
	// Aggregate interface
	type Aggregate interface {
		CreateIterator() Iterator
	}
	
	// Concrete aggregate
	type BookCollection struct {
		books []string
	}
	
	NewBookCollection() *BookCollection {
		return &BookCollection{books: make([]string, 0)}
	}
	
	(bc *BookCollection) AddBook(book string) {
		bc.books = append(bc.books, book)
	}
	
	(bc *BookCollection) CreateIterator() Iterator {
		return &BookIterator{
			collection: bc,
			index:      0,
		}
	}
	
	// Concrete iterator
	type BookIterator struct {
		collection *BookCollection
		index      int
	}
	
	(bi *BookIterator) HasNext() bool {
		return bi.index < len(bi.collection.books)
	}
	
	(bi *BookIterator) Next() interface{} {
		if !bi.HasNext() {
		return nil
	}
		
		book := bi.collection.books[bi.index]
		bi.index++
		return book
	}
	
	// Usage
	books := NewBookCollection()
	books.AddBook("Go Programming")
	books.AddBook("Design Patterns")
	books.AddBook("Clean Code")
	
	iterator := books.CreateIterator()
	
	for iterator.HasNext() {
		book := iterator.Next()
		fmt.Printf("Book: %s\n", book)
	}
}

// Mediator pattern
func mediatorPattern() {
	// Mediator interface
	type Mediator interface {
		Send(message string, colleague Colleague)
	}
	
	// Colleague interface
	type Colleague interface {
		Send(message string)
		Receive(message string)
	}
	
	// Concrete mediator
	type ChatRoom struct {
		colleagues map[string]Colleague
	}
	
	NewChatRoom() *ChatRoom {
		return &ChatRoom{colleagues: make(map[string]Colleague)}
	}
	
	(cr *ChatRoom) Register(colleague Colleague, name string) {
		cr.colleagues[name] = colleague
	}
	
	(cr *ChatRoom) Send(message string, colleague Colleague) {
		for name, col := range cr.colleagues {
			if col != colleague {
				col.Receive(message)
			}
		}
	}
	
	// Concrete colleague
	type User struct {
		name   string
		mediator Mediator
	}
	
	NewUser(name string, mediator Mediator) *User {
		return &User{name: name, mediator: mediator}
	}
	
	(u *User) Send(message string) {
		fmt.Printf("%s sending: %s\n", u.name, message)
		u.mediator.Send(message, u)
	}
	
	(u *User) Receive(message string) {
		fmt.Printf("%s received: %s\n", u.name, message)
	}
	
	// Usage
	chatRoom := NewChatRoom()
	
	user1 := NewUser("Alice", chatRoom)
	user2 := NewUser("Bob", chatRoom)
	user3 := NewUser("Charlie", chatRoom)
	
	chatRoom.Register(user1, "Alice")
	chatRoom.Register(user2, "Bob")
	chatRoom.Register(user3, "Charlie")
	
	user1.Send("Hi everyone!")
	user2.Send("Hello Alice!")
	user3.Send("Hey Bob!")
}

// Memento pattern
func mementoPattern() {
	// Memento
	type TextEditorMemento struct {
		content string
	}
	
	NewTextEditorMemento(content string) *TextEditorMemento {
		return &TextEditorMemento{content: content}
	}
	
	// Originator
	type TextEditor struct {
		content string
	}
	
	NewTextEditor() *TextEditor {
		return &TextEditor{content: ""}
	}
	
	(te *TextEditor) Write(text string) {
		te.content += text
	}
	
	(te *TextEditor) Save() *TextEditorMemento {
		return NewTextEditorMemento(te.content)
	}
	
	(te *TextEditor) Restore(memento *TextEditorMemento) {
		te.content = memento.content
	}
	
	(te *TextEditor) GetContent() string {
		return te.content
	}
	
	// Caretaker
	type TextEditorCaretaker struct {
		mementos []*TextEditorMemento
	}
	
	NewTextEditorCaretaker() *TextEditorCaretaker {
		return &TextEditorCaretaker{mementos: make([]*TextEditorMemento, 0)}
	}
	
	(tec *TextEditorCaretaker) AddMemento(memento *TextEditorMemento) {
		tec.mementos = append(tec.mementos, memento)
	}
	
	(tec *TextEditorCaretaker) GetMemento(index int) *TextEditorMemento {
		if index >= 0 && index < len(tec.mementos) {
			return tec.mementos[index]
		}
		return nil
	}
	
	// Usage
	editor := NewTextEditor()
	caretaker := NewTextEditorCaretaker()
	
	editor.Write("Hello ")
	caretaker.AddMemento(editor.Save())
	
	editor.Write("World ")
	caretaker.AddMemento(editor.Save())
	
	editor.Write("!")
	
	fmt.Printf("Current content: %s\n", editor.GetContent())
	
	// Restore to previous state
	memento := caretaker.GetMemento(0)
	editor.Restore(memento)
	
	fmt.Printf("Restored content: %s\n", editor.GetContent())
}

// Observer pattern
func observerPattern() {
	// Subject interface
	type Subject interface {
		Register(observer Observer)
		Unregister(observer Observer)
		NotifyObservers()
	}
	
	// Observer interface
	type Observer interface {
		Update(data string)
	}
	
	// Concrete subject
	type WeatherStation struct {
		observers []Observer
		temperature float64
	}
	
	NewWeatherStation() *WeatherStation {
		return &WeatherStation{observers: make([]Observer, 0)}
	}
	
	(ws *WeatherStation) Register(observer Observer) {
		ws.observers = append(ws.observers, observer)
	}
	
	(ws *WeatherStation) Unregister(observer Observer) {
		for i, obs := range ws.observers {
			if obs == observer {
				ws.observers = append(ws.observers[:i], ws.observers[i+1:]...)
				break
			}
		}
	}
	
	(ws *WeatherStation) NotifyObservers() {
		for _, observer := range ws.observers {
			observer.Update(fmt.Sprintf("Temperature: %.1f°C", ws.temperature))
		}
	}
	
	(ws *WeatherStation) SetTemperature(temperature float64) {
		ws.temperature = temperature
		ws.NotifyObservers()
	}
	
	// Concrete observers
	type TemperatureDisplay struct {
		name string
	}
	
	NewTemperatureDisplay(name string) *TemperatureDisplay {
		return &TemperatureDisplay{name: name}
	}
	
	(td *TemperatureDisplay) Update(data string) {
		fmt.Printf("%s display: %s\n", td.name, data)
	}
	
	type FanController struct {
		name string
	}
	
	NewFanController(name string) *FanController {
		return &FanController{name: name}
	}
	
	(fc *FanController) Update(data string) {
		fmt.Printf("%s fan: %s\n", fc.name, data)
	}
	
	// Usage
	weatherStation := NewWeatherStation()
	
	display1 := NewTemperatureDisplay("Display 1")
	display2 := NewTemperatureDisplay("Display 2")
	fan := NewFanController("Fan Controller")
	
	weatherStation.Register(display1)
	weatherStation.Register(display2)
	weatherStation.Register(fan)
	
	weatherStation.SetTemperature(25.5)
	weatherStation.SetTemperature(30.0)
}

// State pattern
func statePattern() {
	// State interface
	type State interface {
		Handle(context *Context)
	}
	
	// Context
	type Context struct {
		state State
	}
	
	NewContext(state State) *Context {
		return &Context{state: state}
	}
	
	(c *Context) SetState(state State) {
		c.state = state
	}
	
	(c *Context) Request() {
		c.state.Handle(c)
	}
	
	// Concrete states
	type StartState struct{}
	
	(s *StartState) Handle(context *Context) {
		fmt.Println("Start state: Initializing...")
		context.SetState(&RunningState{})
	}
	
	type RunningState struct{}
	
	(r *RunningState) Handle(context *Context) {
		fmt.Println("Running state: Processing...")
		context.SetState(&StopState{})
	}
	
	type StopState struct{}
	
	(s *StopState) Handle(context *Context) {
		fmt.Println("Stop state: Finalizing...")
		context.SetState(&StartState{})
	}
	
	// Usage
	context := NewContext(&StartState{})
	
	context.Request()
	context.Request()
	context.Request()
}

// Strategy pattern
func strategyPattern() {
	// Strategy interface
	type PaymentStrategy interface {
		Pay(amount float64) string
	}
	
	// Concrete strategies
	type CreditCardStrategy struct{}
	func (ccs CreditCardStrategy) Pay(amount float64) string {
		return fmt.Sprintf("Paid $%.2f using Credit Card", amount)
	}
	
	type PayPalStrategy struct{}
	func (pps PayPalStrategy) Pay(amount float64) string {
		return fmt.Sprintf("Paid $%.2f using PayPal", amount)
	}
	
	type BitcoinStrategy struct{}
	func (bs BitcoinStrategy) Pay(amount float64) string {
		return fmt.Printf("Paid $%.2f using Bitcoin", amount)
	}
	
	// Context
	type ShoppingCart struct {
		paymentStrategy PaymentStrategy
		amount         float64
	}
	
	NewShoppingCart(amount float64) *ShoppingCart {
		return &ShoppingCart{amount: amount}
	}
	
	(sc *ShoppingCart) SetPaymentStrategy(strategy PaymentStrategy) {
		sc.paymentStrategy = strategy
	}
	
	(sc *ShoppingCart) Checkout() string {
		return sc.paymentStrategy.Pay(sc.amount)
	}
	
	// Usage
	cart := NewShoppingCart(100.50)
	
	cart.SetPaymentStrategy(CreditCardStrategy{})
	fmt.Println(cart.Checkout())
	
	cart.SetPaymentStrategy(PayPalStrategy{})
	fmt.Println(cart.Checkout())
	
	cart.SetPaymentStrategy(BitcoinStrategy{})
	fmt.Println(cart.Checkout())
}

// Template method pattern
func templateMethodPattern() {
	// Abstract class
	type DataProcessor struct {
		name string
	}
	
	NewDataProcessor(name string) *DataProcessor {
		return &DataProcessor{name: name}
	}
	
	(dp *DataProcessor) Process(data string) string {
		result := fmt.Sprintf("Processor: %s\n", dp.name)
		result += dp.Validate(data)
		result += dp.Transform(data)
		result += dp.Load(data)
		return result
	}
	
	// Template methods (to be overridden)
	(dp *DataProcessor) Validate(data string) string {
		return "Default validation\n"
	}
	
	(dp *DataProcessor) Transform(data string) string {
		return "Default transformation\n"
	}
	
	(dp *DataProcessor) Load(data string) string {
		return "Default loading\n"
	}
	
	// Concrete class 1
	type XMLProcessor struct {
		DataProcessor
	}
	
	NewXMLProcessor() *XMLProcessor {
		return &XMLProcessor{DataProcessor: *NewDataProcessor("XML")}
	}
	
	(xp *XMLProcessor) Validate(data string) string {
		return "XML validation: Checking XML structure\n"
	}
	
	(xp *XMLProcessor) Transform(data string) string {
		return "XML transformation: Converting to JSON\n"
	}
	
	(xp *XMLProcessor) Load(data string) string {
		return "XML loading: Parsing XML document\n"
	}
	
	// Concrete class 2
	type JSONProcessor struct {
		DataProcessor
	}
	
	NewJSONProcessor() *JSONProcessor {
		return &JSONProcessor{DataProcessor: *NewDataProcessor("JSON")}
	}
	
	(jp *JSONProcessor) Validate(data string) string {
		return "JSON validation: Checking JSON format\n"
	}
	
	(jp *JSONProcessor) Transform(data string) string {
		return "JSON transformation: Converting to XML\n"
	}
	
	(jp *JSONProcessor) Load(data string) string {
		return "JSON loading: Parsing JSON document\n"
	}
	
	// Usage
	xmlProcessor := NewXMLProcessor()
	jsonProcessor := NewJSONProcessor()
	
	data := "sample data"
	
	fmt.Println(xmlProcessor.Process(data))
	fmt.Println(jsonProcessor.Process(data))
}

// Visitor pattern
func visitorPattern() {
	// Visitor interface
	type Visitor interface {
		VisitBook(book *Book)
		VisitElectronics(electronics *Electronics)
	}
	
	// Element interface
	type Element interface {
		Accept(visitor Visitor)
	}
	
	// Concrete elements
	type Book struct {
		title  string
		price  float64
		author string
	}
	
	NewBook(title, author string, price float64) *Book {
		return &Book{title: title, price: price, author: author}
	}
	
	(b *Book) Accept(visitor Visitor) {
		visitor.VisitBook(b)
	}
	
	type Electronics struct {
		name  string
		price float64
		brand string
	}
	
	NewElectronics(name, brand string, price float64) *Electronics {
		return &Electronics{name: name, price: price, brand: brand}
	}
	
	(e *Electronics) Accept(visitor Visitor) {
		visitor.VisitElectronics(e)
	}
	
	// Concrete visitor
	type ShoppingCartVisitor struct {
		total float64
	}
	
	NewShoppingCartVisitor() *ShoppingCartVisitor {
		return &ShoppingCartVisitor{total: 0}
	}
	
	(scv *ShoppingCartVisitor) VisitBook(book *Book) {
		fmt.Printf("Book: %s by %s - $%.2f\n", book.title, book.author, book.price)
		scv.total += book.price
	}
	
	(scv *ShoppingCartVisitor) VisitElectronics(electronics *Electronics) {
		fmt.Printf("Electronics: %s by %s - $%.2f\n", electronics.name, electronics.brand, electronics.price)
		scv.total += electronics.price
	}
	
	(scv *ShoppingCartVisitor) GetTotal() float64 {
		return scv.total
	}
	
	// Object structure
	type ShoppingCart struct {
		elements []Element
	}
	
	NewShoppingCart() *ShoppingCart {
		return &ShoppingCart{elements: make([]Element, 0)}
	}
	
	(sc *ShoppingCart) AddElement(element Element) {
		sc.elements = append(sc.elements, element)
	}
	
	(sc *ShoppingCart) Accept(visitor Visitor) {
		for _, element := range sc.elements {
			element.Accept(visitor)
		}
	}
	
	// Usage
	cart := NewShoppingCart()
	
	cart.AddElement(NewBook("Go Programming", "Alan Donovan", 45.99))
	cart.AddElement(NewElectronics("Laptop", "Dell", 999.99))
	cart.AddElement(NewBook("Design Patterns", "Erich Gamma", 39.99))
	
	visitor := NewShoppingCartVisitor()
	cart.Accept(visitor)
	
	fmt.Printf("Total: $%.2f\n", visitor.GetTotal())
}

// Concurrency patterns
func concurrencyPatterns() {
	fmt.Println("Concurrency Patterns in Go:")
	
	// Worker pool
	fmt.Println("\n1. Worker Pool Pattern:")
	workerPoolPattern()
	
	// Pipeline
	fmt.Println("\n2. Pipeline Pattern:")
	pipelinePattern()
	
	// Fan-in/Fan-out
	fmt.Println("\n3. Fan-In/Fan-Out Pattern:")
	fanInFanOutPattern()
	
	// Publish/Subscribe
	fmt.Println("\n4. Publish/Subscribe Pattern:")
	pubSubPattern()
	
	// Future/Promise
	fmt.Println("\n5. Future/Promise Pattern:")
	futurePromisePattern()
	
	// Circuit breaker
	fmt.Println("\n6. Circuit Breaker Pattern:")
	circuitBreakerPattern()
}

// Worker pool pattern
func workerPoolPattern() {
	type Job struct {
		ID   int
		Data string
	}
	
	type Result struct {
		JobID int
		Value int
		Error error
	}
	
	worker := func(id int, jobs <-chan Job, results chan<- Result) {
		for job := range jobs {
			// Process job
			value := len(job.Data)
			result := Result{
				JobID: job.ID,
				Value: value,
				Error: nil,
			}
			results <- result
			fmt.Printf("Worker %d processed job %d\n", id, job.ID)
		}
	}
	
	const numWorkers = 3
	jobs := make(chan Job, 10)
	results := make(chan Result, 10)
	
	// Start workers
	for i := 0; i < numWorkers; i++ {
		go worker(i, jobs, results)
	}
	
	// Send jobs
	for i := 0; i < 5; i++ {
		jobs <- Job{ID: i, Data: fmt.Sprintf("task-%d", i)}
	}
	close(jobs)
	
	// Collect results
	for i := 0; i < 5; i++ {
		result := <-results
		fmt.Printf("Result: JobID=%d, Value=%d\n", result.JobID, result.Value)
	}
}

// Pipeline pattern
func pipelinePattern() {
	// Stage 1: Generate numbers
	generate := func() <-chan int {
		out := make(chan int)
		go func() {
			defer close(out)
			for i := 1; i <= 5; i++ {
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

// Fan-in/Fan-out pattern
func fanInFanOutPattern() {
	// Fan-out: distribute work to multiple workers
	process := func(id int, data string) <-chan string {
		out := make(chan string)
		go func() {
			defer close(out)
			for i := 0; i < 3; i++ {
				out <- fmt.Sprintf("%s-processed-by-worker-%d", data, id)
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
	ch1 := process(1, "data")
	ch2 := process(2, "data")
	ch3 := process(3, "data")
	
	results := fanIn(ch1, ch2, ch3)
	
	for result := range results {
		fmt.Printf("Fan-in result: %s\n", result)
	}
}

// Publish/Subscribe pattern
func pubSubPattern() {
	type Message struct {
		Topic string
		Data  string
	}
	
	type Subscriber interface {
		Receive(message Message)
	}
	
	type EventBus struct {
		subscribers map[string][]Subscriber
		mutex       sync.RWMutex
	}
	
	NewEventBus() *EventBus {
		return &EventBus{subscribers: make(map[string][]Subscriber)}
	}
	
	(eb *EventBus) Subscribe(topic string, subscriber Subscriber) {
		eb.mutex.Lock()
		defer eb.mutex.Unlock()
		
		eb.subscribers[topic] = append(eb.subscribers[topic], subscriber)
	}
	
	(eb *EventBus) Publish(message Message) {
		eb.mutex.RLock()
		defer eb.mutex.RUnlock()
		
		for _, subscriber := range eb.subscribers[message.Topic] {
			go subscriber.Receive(message)
		}
	}
	
	// Concrete subscriber
	type EmailSubscriber struct {
		name string
	}
	
	NewEmailSubscriber(name string) *EmailSubscriber {
		return &EmailSubscriber{name: name}
	}
	
	(es *EmailSubscriber) Receive(message Message) {
		fmt.Printf("%s received: %s - %s\n", es.name, message.Topic, message.Data)
	}
	
	// Usage
	eventBus := NewEventBus()
	
	subscriber1 := NewEmailSubscriber("Email 1")
	subscriber2 := NewEmailSubscriber("Email 2")
	
	eventBus.Subscribe("news", subscriber1)
	eventBus.Subscribe("news", subscriber2)
	eventBus.Subscribe("sports", subscriber1)
	
	eventBus.Publish(Message{Topic: "news", Data: "Breaking news!"})
	eventBus.Publish(Message{Topic: "sports", Data: "Game results!"})
}

// Future/Promise pattern
func futurePromisePattern() {
	type Future struct {
		result chan int
		err    chan error
	}
	
	NewFuture() *Future {
		return &Future{
			result: make(chan int),
		err:    make(chan error),
	}
	}
	
	(f *Future) Get() (int, error) {
		select {
		case result := <-f.result:
			return result, nil
		case err := <-f.err:
			return 0, err
		}
	}
	
	(f *Future) Set(result int) {
		f.result <- result
	}
	
	(f *Future) SetError(err error) {
		f.err <- err
	}
	
	// Function that returns a future
	calculate := func(n int) *Future {
		future := NewFuture()
		
		go func() {
			if n < 0 {
				future.SetError(fmt.Errorf("negative number"))
				return
			}
			
			// Simulate work
			time.Sleep(100 * time.Millisecond)
			result := n * n
			future.Set(result)
		}()
		
		return future
	}
	
	// Usage
	future1 := calculate(5)
	future2 := calculate(-3)
	
	result1, err1 := future1.Get()
	if err1 != nil {
		fmt.Printf("Error: %v\n", err1)
	} else {
		fmt.Printf("Result 1: %d\n", result1)
	}
	
	result2, err2 := future2.Get()
	if err2 != nil {
		fmt.Printf("Error: %v\n", err2)
	} else {
		fmt.Printf("Result 2: %d\n", result2)
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
	
	for i := 0; i < 5; i++ {
		result, err := cb.Call(operation)
		if err != nil {
			fmt.Printf("Call %d: %v\n", i+1, err)
		} else {
			fmt.Printf("Call %d: %s\n", i+1, result)
		}
	}
}

// Architectural patterns
func architecturalPatterns() {
	fmt.Println("Architectural Patterns in Go:")
	
	// Repository pattern
	fmt.Println("\n1. Repository Pattern:")
	repositoryPattern()
	
	// Service layer pattern
	fmt.Println("\n2. Service Layer Pattern:")
	serviceLayerPattern()
	
	// CQRS pattern
	fmt.Println("\n3. CQRS Pattern:")
	cqrsPattern()
	
	// Event sourcing pattern
	fmt.Println("\n4. Event Sourcing Pattern:")
	eventSourcingPattern()
	
	// Hexagonal architecture
	fmt.Println("\n5. Hexagonal Architecture:")
	hexagonalArchitecture()
	
	// Microservices pattern
	fmt.Println("\n6. Microservices Pattern:")
	microservicesPattern()
}

// Repository pattern
func repositoryPattern() {
	// Entity
	type User struct {
		ID    int
		Name  string
		Email string
	}
	
	// Repository interface
	type UserRepository interface {
		Create(user *User) error
		GetByID(id int) (*User, error)
		Update(user *User) error
		Delete(id int) error
	}
	
	// In-memory implementation
	type InMemoryUserRepository struct {
		users map[int]*User
		nextID int
		mutex  sync.RWMutex
	}
	
	NewInMemoryUserRepository() *InMemoryUserRepository {
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
	
	retrievedUser, err := repo.GetByID(user.ID)
	if err != nil {
		fmt.Printf("Error: %v\n", err)
	} else {
		fmt.Printf("Retrieved user: %+v\n", retrievedUser)
	}
}

// Service layer pattern
func serviceLayerPattern() {
	// Entity
	type User struct {
		ID    int
		Name  string
		Email string
	}
	
	// Repository interface
	type UserRepository interface {
		Create(user *User) error
		GetByID(id int) (*User, error)
		GetByEmail(email string) (*User, error)
	}
	
	// Service
	type UserService struct {
		repo UserRepository
	}
	
	NewUserService(repo UserRepository) *UserService {
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
	
	// Mock repository for demonstration
	type MockUserRepository struct {
		users map[int]*User
		nextID int
	}
	
	NewMockUserRepository() *MockUserRepository {
		return &MockUserRepository{
			users: make(map[int]*User),
			nextID: 1,
		}
	}
	
	(mur *MockUserRepository) Create(user *User) error {
		user.ID = mur.nextID
		mur.users[user.ID] = user
		mur.nextID++
		return nil
	}
	
	(mur *MockUserRepository) GetByID(id int) (*User, error) {
		user, exists := mur.users[id]
	if !exists {
		return nil, fmt.Errorf("not found")
	}
	return user, nil
	}
	
	(mur *MockUserRepository) GetByEmail(email string) (*User, error) {
		for _, user := range mur.users {
			if user.Email == email {
				return user, nil
			}
		}
		return nil, fmt.Errorf("not found")
	}
	
	// Usage
	repo := NewMockUserRepository()
	service := NewUserService(repo)
	
	user, err := service.Create("Jane Doe", "jane@example.com")
	if err != nil {
		fmt.Printf("Error: %v\n", err)
	} else {
		fmt.Printf("Created user: %+v\n", user)
	}
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
	
	// Concrete events
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
	
	NewEventStore() *EventStore {
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
	
	NewUserAggregate() *UserAggregate {
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

// Hexagonal architecture
func hexagonalArchitecture() {
	// Domain entity
	type User struct {
		ID    int
		Name  string
		Email string
	}
	
	// Port (interface)
	type UserRepositoryPort interface {
		Create(user *User) error
		GetByID(id int) (*User, error)
	}
	
	// Application service
	type UserService struct {
		repo UserRepositoryPort
	}
	
	NewUserService(repo UserRepositoryPort) *UserService {
		return &UserService{repo: repo}
	}
	
	(s *UserService) CreateUser(name, email string) (*User, error) {
		user := &User{Name: name, Email: email}
		if err := s.repo.Create(user); err != nil {
			return nil, err
		}
		return user, nil
	}
	
	// Adapter (implementation)
	type InMemoryUserRepositoryAdapter struct {
		users map[int]*User
		nextID int
	}
	
	NewInMemoryUserRepositoryAdapter() *InMemoryUserRepositoryAdapter {
		return &InMemoryUserRepositoryAdapter{
			users: make(map[int]*User),
			nextID: 1,
		}
	}
	
	(adapter *InMemoryUserRepositoryAdapter) Create(user *User) error {
		user.ID = adapter.nextID
		adapter.users[user.ID] = user
		adapter.nextID++
		return nil
	}
	
	(adapter *InMemoryUserRepositoryAdapter) GetByID(id int) (*User, error) {
		user, exists := adapter.users[id]
	if !exists {
		return nil, fmt.Errorf("not found")
	}
	return user, nil
	}
	
	// Usage
	adapter := NewInMemoryUserRepositoryAdapter()
	service := NewUserService(adapter)
	
	user, err := service.Create("John Doe", "john@example.com")
	if err != nil {
		fmt.Printf("Error: %v\n", err)
	} else {
		fmt.Printf("Created user: %+v\n", user)
	}
}

// Microservices pattern
func microservicesPattern() {
	// Service interfaces
	type UserService interface {
		GetUser(id int) (*User, error)
	}
	
	type OrderService interface {
		CreateOrder(userID int, items []string) error
	}
	
	// Service implementations
	type UserMicroservice struct {
		users map[int]*User
	}
	
	NewUserMicroservice() *UserMicroservice {
		users := make(map[int]*User)
		users[1] = &User{ID: 1, Name: "John Doe", Email: "john@example.com"}
		return &UserMicroservice{users: users}
	}
	
	(us *UserMicroservice) GetUser(id int) (*User, error) {
		user, exists := us.users[id]
		if !exists {
			return nil, fmt.Errorf("user not found")
		}
		return user, nil
	}
	
	type OrderMicroservice struct {
		orders []string
	}
	
	NewOrderMicroservice() *OrderMicroservice {
		return &OrderMicroservice{orders: make([]string, 0)}
	}
	
	(os *OrderMicroservice) CreateOrder(userID int, items []string) error {
		order := fmt.Sprintf("Order for user %d: %v", userID, items)
		os.orders = append(os.orders, order)
		fmt.Printf("Created: %s\n", order)
		return nil
	}
	
	// API Gateway
	type APIGateway struct {
		userService  UserService
		orderService OrderService
	}
	
	NewAPIGateway(userService UserService, orderService OrderService) *APIGateway {
		return &APIGateway{
			userService:  userService,
		orderService: orderService,
	}
	}
	
	(gateway *APIGateway) CreateOrder(userID int, items []string) error {
		// Validate user exists
		_, err := gateway.userService.GetUser(userID)
		if err != nil {
			return fmt.Errorf("user validation failed: %w", err)
		}
		
		// Create order
		return gateway.orderService.CreateOrder(userID, items)
	}
	
	// Usage
	userService := NewUserMicroservice()
	orderService := NewOrderMicroservice()
	
	gateway := NewAPIGateway(userService, orderService)
	
	err := gateway.CreateOrder(1, []string{"item1", "item2"})
	if err != nil {
		fmt.Printf("Error: %v\n", err)
	}
	
	err = gateway.CreateOrder(2, []string{"item3"}) // User 2 doesn't exist
	if err != nil {
		fmt.Printf("Error: %v\n", err)
	}
}

// Additional patterns demonstration
func demonstrateAllPatterns() {
	fmt.Println("\n--- Additional Patterns ---")
	fmt.Println("1. Null Object Pattern")
	fmt.Println("2. Specification Pattern")
	fmt.Println("3. Dependency Injection Pattern")
	fmt.Println("4. Data Mapper Pattern")
	fmt.Println("5. Unit of Work Pattern")
	fmt.Println("6. Active Record Pattern")
	fmt.Println("7. Data Transfer Object Pattern")
	fmt.Println("8. Repository Pattern (already shown)")
	fmt.Println("9. Service Layer Pattern (already shown)")
	fmt.Println("10. CQRS Pattern (already shown)")
}
