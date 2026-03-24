package main

import (
	"fmt"
	"math"
)

func main() {
	fmt.Println("=== Interfaces in Go ===")
	
	// Basic interface
	fmt.Println("\n--- Basic Interface ---")
	
	var shaper Shape
	rect := Rectangle{Width: 10, Height: 5}
	circle := Circle{Radius: 7}
	
	shaper = rect
	fmt.Printf("Rectangle area: %.2f\n", shaper.Area())
	fmt.Printf("Rectangle perimeter: %.2f\n", shaper.Perimeter())
	
	shaper = circle
	fmt.Printf("Circle area: %.2f\n", shaper.Area())
	fmt.Printf("Circle perimeter: %.2f\n", shaper.Perimeter())
	
	// Multiple interfaces
	fmt.Println("\n--- Multiple Interfaces ---")
	
	var mover Mover
	var moverShaper MoverShaper
	
	car := Car{Brand: "Toyota", Speed: 60}
	animal := Animal{Species: "Cheetah", Speed: 100}
	
	mover = car
	fmt.Printf("Car: %s\n", mover.Move())
	
	mover = animal
	fmt.Printf("Animal: %s\n", mover.Move())
	
	// Implementing multiple interfaces
	moverShaper = car
	fmt.Printf("Car moves: %s\n", moverShaper.Move())
	fmt.Printf("Car area: %.2f\n", moverShaper.Area())
	
	// Empty interface
	fmt.Println("\n--- Empty Interface ---")
	
	var data interface{}
	
	data = 42
	fmt.Printf("Integer: %d (type: %T)\n", data, data)
	
	data = "Hello"
	fmt.Printf("String: %s (type: %T)\n", data, data)
	
	data = []int{1, 2, 3}
	fmt.Printf("Slice: %v (type: %T)\n", data, data)
	
	// Type assertions
	fmt.Println("\n--- Type Assertions ---")
	
	var x interface{} = "hello"
	
	// Safe type assertion
	if str, ok := x.(string); ok {
		fmt.Printf("String value: %s\n", str)
	}
	
	// Type switch
	fmt.Println("\n--- Type Switch ---")
	
	values := []interface{}{42, "hello", true, 3.14, []int{1, 2, 3}}
	
	for i, val := range values {
		switch v := val.(type) {
		case int:
			fmt.Printf("Index %d: Integer %d\n", i, v)
		case string:
			fmt.Printf("Index %d: String %s\n", i, v)
		case bool:
			fmt.Printf("Index %d: Boolean %t\n", i, v)
		case float64:
			fmt.Printf("Index %d: Float %.2f\n", i, v)
		case []int:
			fmt.Printf("Index %d: Integer slice %v\n", i, v)
		default:
			fmt.Printf("Index %d: Unknown type %T\n", i, v)
		}
	}
	
	// Interface composition
	fmt.Println("\n--- Interface Composition ---")
	
	var writerLogger WriterLogger
	logger := Logger{Level: "INFO"}
	
	writerLogger = logger
	writerLogger.Write("This is a log message")
	writerLogger.Log("Another log message")
	
	// Interface with methods
	fmt.Println("\n--- Interface with Methods ---")
	
	figures := []Shape{
		Rectangle{Width: 4, Height: 6},
		Circle{Radius: 3},
		Triangle{Base: 4, Height: 5},
	}
	
	fmt.Println("Areas of different shapes:")
	for i, shape := range figures {
		fmt.Printf("Shape %d: Area = %.2f\n", i+1, shape.Area())
	}
	
	// Interface satisfaction
	fmt.Println("\n--- Interface Satisfaction ---")
	
	// Check if a type satisfies an interface
	var _ Shape = (*Rectangle)(nil) // Rectangle satisfies Shape
	var _ Mover = (*Car)(nil)       // Car satisfies Mover
	var _ Writer = (*Logger)(nil)  // Logger satisfies Writer
	
	fmt.Println("All interface satisfactions verified!")
	
	// Practical example: sorting
	fmt.Println("\n--- Practical Example: Sorting ---")
	
	people := []Person{
		{Name: "Alice", Age: 30},
		{Name: "Bob", Age: 25},
		{Name: "Charlie", Age: 35},
	}
	
	fmt.Printf("Before sorting: %v\n", people)
	sortPeople(people)
	fmt.Printf("After sorting: %v\n", people)
}

// Basic interface
type Shape interface {
	Area() float64
	Perimeter() float64
}

// Implementations
type Rectangle struct {
	Width  float64
	Height float64
}

func (r Rectangle) Area() float64 {
	return r.Width * r.Height
}

func (r Rectangle) Perimeter() float64 {
	return 2 * (r.Width + r.Height)
}

type Circle struct {
	Radius float64
}

func (c Circle) Area() float64 {
	return math.Pi * c.Radius * c.Radius
}

func (c Circle) Perimeter() float64 {
	return 2 * math.Pi * c.Radius
}

type Triangle struct {
	Base   float64
	Height float64
}

func (t Triangle) Area() float64 {
	return 0.5 * t.Base * t.Height
}

func (t Triangle) Perimeter() float64 {
	// Simplified: assuming it's an isosceles triangle
	side := math.Sqrt(t.Base*t.Base/4 + t.Height*t.Height)
	return t.Base + 2*side
}

// Multiple interfaces
type Mover interface {
	Move() string
}

type MoverShaper interface {
	Mover
	Shape
}

type Car struct {
	Brand string
	Speed int
}

func (c Car) Move() string {
	return fmt.Sprintf("%s is moving at %d mph", c.Brand, c.Speed)
}

func (c Car) Area() float64 {
	// Car footprint (simplified)
	return 15.5 // Average car area in square meters
}

func (c Car) Perimeter() float64 {
	return 15.0 // Simplified perimeter
}

type Animal struct {
	Species string
	Speed   int
}

func (a Animal) Move() string {
	return fmt.Sprintf("%s is running at %d mph", a.Species, a.Speed)
}

// Empty interface usage
type Container struct {
	data []interface{}
}

func (c *Container) Add(item interface{}) {
	c.data = append(c.data, item)
}

func (c *Container) Get(index int) interface{} {
	if index >= 0 && index < len(c.data) {
		return c.data[index]
	}
	return nil
}

// Interface composition
type Writer interface {
	Write(message string)
}

type Logger interface {
	Log(message string)
}

type WriterLogger interface {
	Writer
	Logger
}

type Logger struct {
	Level string
}

func (l Logger) Write(message string) {
	fmt.Printf("[%s] WRITE: %s\n", l.Level, message)
}

func (l Logger) Log(message string) {
	fmt.Printf("[%s] LOG: %s\n", l.Level, message)
}

// Interface for sorting
type Sortable interface {
	Len() int
	Less(i, j int) bool
	Swap(i, j int)
}

type Person struct {
	Name string
	Age  int
}

type People []Person

func (p People) Len() int {
	return len(p)
}

func (p People) Less(i, j int) bool {
	return p[i].Age < p[j].Age
}

func (p People) Swap(i, j int) {
	p[i], p[j] = p[j], p[i]
}

func sortPeople(people People) {
	// Simple bubble sort implementation
	for i := 0; i < people.Len(); i++ {
		for j := 0; j < people.Len()-1-i; j++ {
			if people.Less(j, j+1) {
				people.Swap(j, j+1)
			}
		}
	}
}

// Advanced interface examples

type Comparable interface {
	Compare(other Comparable) int
}

type Number struct {
	Value int
}

func (n Number) Compare(other Comparable) int {
	otherNum, ok := other.(Number)
	if !ok {
		return 0 // Can't compare
	}
	
	if n.Value < otherNum.Value {
		return -1
	} else if n.Value > otherNum.Value {
		return 1
	}
	return 0
}

// Interface with generic behavior
type Processor interface {
	Process(data interface{}) interface{}
}

type UppercaseProcessor struct{}

func (u UppercaseProcessor) Process(data interface{}) interface{} {
	if str, ok := data.(string); ok {
		return strings.ToUpper(str)
	}
	return data
}

type NumberDoubler struct{}

func (n NumberDoubler) Process(data interface{}) interface{} {
	if num, ok := data.(int); ok {
		return num * 2
	}
	return data
}

// Interface for data validation
type Validator interface {
	Validate(data interface{}) error
}

type StringValidator struct {
	MinLength int
	MaxLength int
}

func (sv StringValidator) Validate(data interface{}) error {
	str, ok := data.(string)
	if !ok {
		return fmt.Errorf("expected string, got %T", data)
	}
	
	if len(str) < sv.MinLength {
		return fmt.Errorf("string too short: minimum %d characters", sv.MinLength)
	}
	
	if len(str) > sv.MaxLength {
		return fmt.Errorf("string too long: maximum %d characters", sv.MaxLength)
	}
	
	return nil
}

// Demonstrate advanced interfaces
func demonstrateAdvancedInterfaces() {
	fmt.Println("\n--- Advanced Interface Examples ---")
	
	// Comparable interface
	num1 := Number{Value: 10}
	num2 := Number{Value: 20}
	
	result := num1.Compare(num2)
	fmt.Printf("Comparison result: %d\n", result)
	
	// Processor interface
	processors := []Processor{
		UppercaseProcessor{},
		NumberDoubler{},
	}
	
	data := []interface{}{"hello", 42, "world", 100}
	
	for _, processor := range processors {
		fmt.Printf("\nProcessing with %T:\n", processor)
		for _, item := range data {
			processed := processor.Process(item)
			fmt.Printf("  %v -> %v\n", item, processed)
		}
	}
	
	// Validator interface
	validator := StringValidator{MinLength: 3, MaxLength: 10}
	
	testStrings := []string{"hi", "hello", "this is too long"}
	
	for _, str := range testStrings {
		err := validator.Validate(str)
		if err != nil {
			fmt.Printf("'%s' validation failed: %v\n", str, err)
		} else {
			fmt.Printf("'%s' is valid\n", str)
		}
	}
}
