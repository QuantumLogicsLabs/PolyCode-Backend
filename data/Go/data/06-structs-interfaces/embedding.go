package main

import (
	"fmt"
	"strings"
)

func main() {
	fmt.Println("=== Struct Embedding in Go ===")
	
	// Basic embedding
	fmt.Println("\n--- Basic Embedding ---")
	
	person := Person{
		Name: "Alice",
		Age:  30,
	}
	
	employee := Employee{
		Person: person,
		ID:     "EMP001",
		Salary: 75000,
	}
	
	// Access embedded fields directly
	fmt.Printf("Employee name: %s\n", employee.Name) // Promoted field
	fmt.Printf("Employee age: %d\n", employee.Age)   // Promoted field
	fmt.Printf("Employee ID: %s\n", employee.ID)
	fmt.Printf("Employee salary: $%.2f\n", employee.Salary)
	
	// Access through embedded struct
	fmt.Printf("Person name: %s\n", employee.Person.Name)
	
	// Method promotion
	fmt.Println("\n--- Method Promotion ---")
	
	// Person methods are promoted to Employee
	fmt.Printf("Employee details: %s\n", employee.GetDetails())
	employee.SetName("Alice Johnson")
	fmt.Printf("Updated name: %s\n", employee.GetName())
	
	// Multiple embedding
	fmt.Println("\n--- Multiple Embedding ---")
	
	contact := Contact{
		Email: "alice@example.com",
		Phone: "555-1234",
	}
	
	fullEmployee := FullEmployee{
		Person:  Person{Name: "Bob", Age: 28},
		Contact: contact,
		ID:      "EMP002",
		Salary:  65000,
	}
	
	// Fields from both embedded structs are promoted
	fmt.Printf("Full employee: %s, %d, %s, %s, %s, $%.2f\n",
		fullEmployee.Name, fullEmployee.Age,
		fullEmployee.Email, fullEmployee.Phone,
		fullEmployee.ID, fullEmployee.Salary)
	
	// Method resolution
	fmt.Println("\n--- Method Resolution ---")
	
	// Call GetDetails - which method is called?
	fmt.Printf("Employee details: %s\n", employee.GetDetails())
	fmt.Printf("Full employee details: %s\n", fullEmployee.GetDetails())
	
	// Embedding with name conflicts
	fmt.Println("\n--- Name Conflicts ---")
	
	manager := Manager{
		Person: Person{Name: "Charlie", Age: 35},
		ID:     "MGR001",
		Salary: 90000,
		Level:  "Senior",
	}
	
	// Ambiguous access - need to specify
	fmt.Printf("Manager name: %s\n", manager.Name)
	fmt.Printf("Manager ID: %s\n", manager.ID)
	fmt.Printf("Manager level: %s\n", manager.Level)
	
	// Method conflicts
	fmt.Printf("Manager details: %s\n", manager.GetDetails())
	
	// Embedding interfaces
	fmt.Println("\n--- Interface Embedding ---")
	
	var reader Reader
	var writer Writer
	
	doc := Document{
		Title:   "Go Programming",
		Content: "Go is awesome!",
		Author:  "Alice",
	}
	
	reader = doc
	writer = doc
	
	fmt.Printf("Read: %s\n", reader.Read())
	writer.Write("Go is powerful!")
	fmt.Printf("After writing: %s\n", doc.Content)
	
	// Composition over inheritance
	fmt.Println("\n--- Composition Pattern ---")
	
	car := Car{
		Vehicle: Vehicle{Brand: "Toyota", Model: "Camry"},
		Engine:  Engine{Type: "V6", Horsepower: 301},
		Wheels:  4,
	}
	
	fmt.Printf("Car: %s %s with %s engine\n", 
		car.Brand, car.Model, car.Engine.Type)
	car.Start()
	car.Drive()
	
	// Advanced embedding patterns
	fmt.Println("\n--- Advanced Embedding Patterns ---")
	
	// Embedding for behavior extension
	extendedShape := ExtendedShape{
		Shape: Rectangle{Width: 10, Height: 5},
		Color: "Red",
	}
	
	fmt.Printf("Extended shape area: %.2f\n", extendedShape.Area())
	fmt.Printf("Extended shape color: %s\n", extendedShape.Color)
	fmt.Printf("Extended shape info: %s\n", extendedShape.GetInfo())
	
	// Embedding with pointers
	fmt.Println("\n--- Embedding with Pointers ---")
	
	original := Person{Name: "Diana", Age: 32}
	pointerEmbed := PointerEmbed{
		Person: &original,
		Role:   "Developer",
	}
	
	fmt.Printf("Pointer embed name: %s\n", pointerEmbed.Name)
	pointerEmbed.SetName("Diana Smith")
	fmt.Printf("After modification: %s\n", original.Name)
	
	// Anonymous struct embedding
	fmt.Println("\n--- Anonymous Struct Embedding ---")
	
	anonymous := struct {
		Person
		Department string
	}{
		Person:     Person{Name: "Eve", Age: 27},
		Department: "Engineering",
	}
	
	fmt.Printf("Anonymous embed: %s, %d, %s\n", 
		anonymous.Name, anonymous.Age, anonymous.Department)
}

// Basic structs for embedding examples

type Person struct {
	Name string
	Age  int
}

func (p *Person) SetName(name string) {
	p.Name = name
}

func (p Person) GetName() string {
	return p.Name
}

func (p Person) GetDetails() string {
	return fmt.Sprintf("%s (age %d)", p.Name, p.Age)
}

type Employee struct {
	Person
	ID     string
	Salary float64
}

// Employee has its own GetDetails method (overrides Person's)
func (e Employee) GetDetails() string {
	return fmt.Sprintf("%s (ID: %s, Salary: $%.2f)", 
		e.Name, e.ID, e.Salary)
}

type Contact struct {
	Email string
	Phone string
}

func (c Contact) GetContactInfo() string {
	return fmt.Sprintf("%s, %s", c.Email, c.Phone)
}

type FullEmployee struct {
	Person
	Contact
	ID     string
	Salary float64
}

// FullEmployee has its own GetDetails method
func (fe FullEmployee) GetDetails() string {
	return fmt.Sprintf("%s (age %d) - %s, %s, ID: %s, Salary: $%.2f", 
		fe.Name, fe.Age, fe.Email, fe.Phone, fe.ID, fe.Salary)
}

// Name conflict example
type Manager struct {
	Person
	ID     string
	Salary float64
	Level  string
}

func (m Manager) GetDetails() string {
	return fmt.Sprintf("Manager %s (age %d) - Level: %s, ID: %s, Salary: $%.2f", 
		m.Name, m.Age, m.Level, m.ID, m.Salary)
}

// Interface embedding
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

type Document struct {
	Title   string
	Content string
	Author  string
}

func (d Document) Read() string {
	return fmt.Sprintf("Reading '%s' by %s: %s", d.Title, d.Author, d.Content)
}

func (d *Document) Write(content string) {
	d.Content = content
}

// Composition pattern
type Vehicle struct {
	Brand string
	Model string
}

func (v Vehicle) Start() {
	fmt.Printf("%s %s is starting\n", v.Brand, v.Model)
}

func (v Vehicle) Stop() {
	fmt.Printf("%s %s is stopping\n", v.Brand, v.Model)
}

type Engine struct {
	Type       string
	Horsepower int
}

func (e Engine) Start() {
	fmt.Printf("%s engine with %d horsepower is running\n", 
		e.Type, e.Horsepower)
}

type Car struct {
	Vehicle
	Engine
	Wheels int
}

func (c Car) Drive() {
	fmt.Printf("Driving %s %s\n", c.Brand, c.Model)
}

// Method resolution in Car
func (c Car) Start() {
	// Call embedded Vehicle's Start method
	c.Vehicle.Start()
	// Call embedded Engine's Start method
	c.Engine.Start()
	fmt.Println("Car is ready to drive")
}

// Advanced embedding patterns

type Shape interface {
	Area() float64
}

type Rectangle struct {
	Width  float64
	Height float64
}

func (r Rectangle) Area() float64 {
	return r.Width * r.Height
}

type ExtendedShape struct {
	Shape
	Color string
}

func (es ExtendedShape) GetInfo() string {
	return fmt.Sprintf("Shape with area %.2f and color %s", es.Area(), es.Color)
}

// Pointer embedding
type PointerEmbed struct {
	*Person
	Role string
}

// Anonymous struct embedding example
func demonstrateAnonymousEmbedding() {
	fmt.Println("\n--- Anonymous Struct Embedding Examples ---")
	
	// Anonymous struct with embedded named struct
	user := struct {
		Person
		Username string
		Active   bool
	}{
		Person:   Person{Name: "Frank", Age: 40},
		Username: "frank123",
		Active:   true,
	}
	
	fmt.Printf("User: %s (%s), Active: %t\n", 
		user.Name, user.Username, user.Active)
	
	// Anonymous struct with embedded anonymous struct
	product := struct {
		struct {
			Name  string
			Price float64
		}
		Category string
		InStock  bool
	}{
		struct {
			Name  string
			Price float64
		}{"Laptop", 999.99},
		"Electronics",
		true,
	}
	
	fmt.Printf("Product: %s, $%.2f, %s, In Stock: %t\n",
		product.Name, product.Price, product.Category, product.InStock)
}

// Embedding for behavior extension
type Logger struct {
	Prefix string
}

func (l Logger) Log(message string) {
	fmt.Printf("[%s] %s\n", l.Prefix, message)
}

type Service struct {
	Name string
	Logger
}

func (s Service) Process(data string) {
	s.Log(fmt.Sprintf("Processing %s in service %s", data, s.Name))
	// Processing logic here
}

// Demonstrate behavior extension
func demonstrateBehaviorExtension() {
	fmt.Println("\n--- Behavior Extension ---")
	
	service := Service{
		Name: "UserService",
		Logger: Logger{Prefix: "SERVICE"},
	}
	
	service.Process("user data")
}

// Embedding with method conflicts resolution
type Base struct {
	Value int
}

func (b Base) Display() string {
	return fmt.Sprintf("Base value: %d", b.Value)
}

type Derived struct {
	Base
	Value string // Name conflict
}

func (d Derived) Display() string {
	return fmt.Sprintf("Derived - Base: %s, Derived: %s", 
		d.Base.Display(), d.Value)
}

func demonstrateMethodConflicts() {
	fmt.Println("\n--- Method Conflicts Resolution ---")
	
	derived := Derived{
		Base:  Base{Value: 42},
		Value: "answer",
	}
	
	fmt.Printf("Base.Value: %d\n", derived.Base.Value)
	fmt.Printf("Derived.Value: %s\n", derived.Value)
	fmt.Printf("Display: %s\n", derived.Display())
}

// Embedding in interfaces
type Animal interface {
	Speak() string
	Move() string
}

type Dog struct {
	Breed string
}

func (d Dog) Speak() string {
	return "Woof!"
}

func (d Dog) Move() string {
	return "Running"
}

type Robot struct {
	Model string
}

func (r Robot) Speak() string {
	return "Beep boop"
}

func (r Robot) Move() string {
	return "Rolling"
}

type Pet interface {
	Animal
	Play() string
}

type RoboticDog struct {
	Dog
	Robot
	BatteryLevel int
}

func (rd RoboticDog) Speak() string {
	if rd.BatteryLevel > 50 {
		return rd.Dog.Speak()
	}
	return rd.Robot.Speak()
}

func (rd RoboticDog) Move() string {
	if rd.BatteryLevel > 30 {
		return rd.Dog.Move()
	}
	return rd.Robot.Move()
}

func (rd RoboticDog) Play() string {
	return "Playing fetch!"
}

func demonstrateInterfaceEmbedding() {
	fmt.Println("\n--- Interface Embedding ---")
	
	robotDog := RoboticDog{
		Dog:          Dog{Breed: "Corgi"},
		Robot:        Robot{Model: "RX-2000"},
		BatteryLevel: 75,
	}
	
	var pet Pet = robotDog
	fmt.Printf("Pet speaks: %s\n", pet.Speak())
	fmt.Printf("Pet moves: %s\n", pet.Move())
	fmt.Printf("Pet plays: %s\n", pet.Play())
	
	// Low battery
	robotDog.BatteryLevel = 20
	fmt.Printf("Low battery - speaks: %s\n", robotDog.Speak())
	fmt.Printf("Low battery - moves: %s\n", robotDog.Move())
}
