package main

import (
	"fmt"
	"math"
)

func main() {
	fmt.Println("=== Polymorphism in Go ===")
	
	// Interface-based polymorphism
	fmt.Println("\n--- Interface-based Polymorphism ---")
	
	// Create different shapes
	shapes := []Shape{
		&Rectangle{Width: 10, Height: 5},
		&Circle{Radius: 7},
		&Triangle{Base: 8, Height: 6},
		&Square{Side: 4},
	}
	
	// Polymorphic behavior - same interface, different implementations
	fmt.Println("Shape Areas:")
	for i, shape := range shapes {
		fmt.Printf("Shape %d: Area = %.2f\n", i+1, shape.Area())
	}
	
	fmt.Println("\nShape Perimeters:")
	for i, shape := range shapes {
		fmt.Printf("Shape %d: Perimeter = %.2f\n", i+1, shape.Perimeter())
	}
	
	// Polymorphic function
	fmt.Println("\n--- Polymorphic Functions ---")
	
	describeShape(&Rectangle{Width: 3, Height: 4})
	describeShape(&Circle{Radius: 5})
	describeShape(&Triangle{Base: 6, Height: 8})
	
	// Dynamic dispatch
	fmt.Println("\n--- Dynamic Dispatch ---")
	
	var shape Shape
	
	shape = &Rectangle{Width: 15, Height: 10}
	fmt.Printf("Rectangle area: %.2f\n", shape.Area())
	
	shape = &Circle{Radius: 8}
	fmt.Printf("Circle area: %.2f\n", shape.Area())
	
	// Type assertions for specific behavior
	fmt.Println("\n--- Type Assertions ---")
	
	for _, shape := range shapes {
		fmt.Printf("Shape: %T\n", shape)
		
		// Type assertion to access specific methods
		if rect, ok := shape.(*Rectangle); ok {
			fmt.Printf("  Rectangle dimensions: %.2f x %.2f\n", 
				rect.Width, rect.Height)
			fmt.Printf("  Is square: %t\n", rect.IsSquare())
		}
		
		if circle, ok := shape.(*Circle); ok {
			fmt.Printf("  Circle radius: %.2f\n", circle.Radius)
			fmt.Printf("  Diameter: %.2f\n", circle.Diameter())
		}
		
		if square, ok := shape.(*Square); ok {
			fmt.Printf("  Square side: %.2f\n", square.Side)
			fmt.Printf("  Diagonal: %.2f\n", square.Diagonal())
		}
	}
	
	// Interface composition
	fmt.Println("\n--- Interface Composition ---")
	
	var drawable Drawable
	
	drawable = &Rectangle{Width: 12, Height: 8}
	fmt.Printf("Drawing rectangle: %s\n", drawable.Draw())
	
	drawable = &Circle{Radius: 6}
	fmt.Printf("Drawing circle: %s\n", drawable.Draw())
	
	drawable = &Triangle{Base: 10, Height: 12}
	fmt.Printf("Drawing triangle: %s\n", drawable.Draw())
	
	// Polymorphism with multiple interfaces
	fmt.Println("\n--- Multiple Interface Polymorphism ---")
	
	var drawableShape DrawableShape
	
	drawableShape = &Rectangle{Width: 7, Height: 9}
	fmt.Printf("Drawable shape: %s, Area: %.2f\n", 
		drawableShape.Draw(), drawableShape.Area())
	
	drawableShape = &Circle{Radius: 5}
	fmt.Printf("Drawable shape: %s, Area: %.2f\n", 
		drawableShape.Draw(), drawableShape.Area())
	
	// Animal polymorphism
	fmt.Println("\n--- Animal Polymorphism ---")
	
	animals := []Animal{
		&Dog{Breed: "Golden Retriever"},
		&Cat{Breed: "Siamese"},
		&Bird{Species: "Parrot"},
	}
	
	fmt.Println("Animal Sounds:")
	for _, animal := range animals {
		fmt.Printf("%s: %s\n", animal.GetName(), animal.MakeSound())
	}
	
	// Polymorphic behavior with different implementations
	fmt.Println("\n--- Polymorphic Behavior ---")
	
	for _, animal := range animals {
		fmt.Printf("%s:\n", animal.GetName())
		fmt.Printf("  Sound: %s\n", animal.MakeSound())
		fmt.Printf("  Movement: %s\n", animal.Move())
		fmt.Printf("  Eats: %s\n", animal.Eat())
		fmt.Println()
	}
	
	// Payment system polymorphism
	fmt.Println("\n--- Payment System Polymorphism ---")
	
	payments := []PaymentMethod{
		&CreditCard{Number: "1234-5678-9012-3456", Limit: 5000},
		&PayPal{Email: "user@example.com", Balance: 1000},
		&BankTransfer{AccountNumber: "ACC123456", BankName: "First Bank"},
	}
	
	amount := 250.0
	fmt.Printf("Processing payment of $%.2f:\n", amount)
	
	for _, payment := range payments {
		fmt.Printf("%s: ", payment.GetName())
		if payment.ProcessPayment(amount) {
			fmt.Printf("Payment successful\n")
		} else {
			fmt.Printf("Payment failed\n")
		}
	}
	
	// File system polymorphism
	fmt.Println("\n--- File System Polymorphism ---")
	
	files := []File{
		&TextFile{Name: "document.txt", Size: 1024},
		&ImageFile{Name: "photo.jpg", Size: 2048, Format: "JPEG"},
		&VideoFile{Name: "movie.mp4", Size: 10240, Duration: 120},
	}
	
	for _, file := range files {
		fmt.Printf("File: %s\n", file.GetName())
		fmt.Printf("  Size: %d bytes\n", file.GetSize())
		fmt.Printf("  Type: %s\n", file.GetType())
		fmt.Printf("  Open: %s\n", file.Open())
		fmt.Printf("  Close: %s\n", file.Close())
		fmt.Println()
	}
}

// Basic shape interface
type Shape interface {
	Area() float64
	Perimeter() float64
}

// Shape implementations
type Rectangle struct {
	Width  float64
	Height float64
}

func (r *Rectangle) Area() float64 {
	return r.Width * r.Height
}

func (r *Rectangle) Perimeter() float64 {
	return 2 * (r.Width + r.Height)
}

func (r *Rectangle) IsSquare() bool {
	return r.Width == r.Height
}

type Circle struct {
	Radius float64
}

func (c *Circle) Area() float64 {
	return math.Pi * c.Radius * c.Radius
}

func (c *Circle) Perimeter() float64 {
	return 2 * math.Pi * c.Radius
}

func (c *Circle) Diameter() float64 {
	return 2 * c.Radius
}

type Triangle struct {
	Base   float64
	Height float64
}

func (t *Triangle) Area() float64 {
	return 0.5 * t.Base * t.Height
}

func (t *Triangle) Perimeter() float64 {
	// Assuming it's an isosceles triangle
	side := math.Sqrt(t.Base*t.Base/4 + t.Height*t.Height)
	return t.Base + 2*side
}

type Square struct {
	Side float64
}

func (s *Square) Area() float64 {
	return s.Side * s.Side
}

func (s *Square) Perimeter() float64 {
	return 4 * s.Side
}

func (s *Square) Diagonal() float64 {
	return s.Side * math.Sqrt(2)
}

// Polymorphic function
func describeShape(shape Shape) {
	fmt.Printf("Shape with area: %.2f and perimeter: %.2f\n", 
		shape.Area(), shape.Perimeter())
}

// Drawing interface
type Drawable interface {
	Draw() string
}

// Drawable implementations
func (r *Rectangle) Draw() string {
	return fmt.Sprintf("Drawing rectangle (%.2f x %.2f)", r.Width, r.Height)
}

func (c *Circle) Draw() string {
	return fmt.Sprintf("Drawing circle (radius: %.2f)", c.Radius)
}

func (t *Triangle) Draw() string {
	return fmt.Sprintf("Drawing triangle (base: %.2f, height: %.2f)", t.Base, t.Height)
}

// Interface composition
type DrawableShape interface {
	Shape
	Drawable
}

// Animal polymorphism
type Animal interface {
	MakeSound() string
	Move() string
	Eat() string
	GetName() string
}

type Dog struct {
	Breed string
}

func (d *Dog) MakeSound() string {
	return "Woof! Woof!"
}

func (d *Dog) Move() string {
	return "Running and fetching"
}

func (d *Dog) Eat() string {
	return "Eating dog food and treats"
}

func (d *Dog) GetName() string {
	return fmt.Sprintf("Dog (%s)", d.Breed)
}

type Cat struct {
	Breed string
}

func (c *Cat) MakeSound() string {
	return "Meow!"
}

func (c *Cat) Move() string {
	return "Prowling and jumping"
}

func (c *Cat) Eat() string {
	return "Eating fish and cat food"
}

func (c *Cat) GetName() string {
	return fmt.Sprintf("Cat (%s)", c.Breed)
}

type Bird struct {
	Species string
}

func (b *Bird) MakeSound() string {
	return "Tweet! Tweet!"
}

func (b *Bird) Move() string {
	return "Flying and hopping"
}

func (b *Bird) Eat() string {
	return "Eating seeds and insects"
}

func (b *Bird) GetName() string {
	return fmt.Sprintf("Bird (%s)", b.Species)
}

// Payment system polymorphism
type PaymentMethod interface {
	ProcessPayment(amount float64) bool
	GetName() string
}

type CreditCard struct {
	Number string
	Limit  float64
}

func (cc *CreditCard) ProcessPayment(amount float64) bool {
	return amount <= cc.Limit
}

func (cc *CreditCard) GetName() string {
	return fmt.Sprintf("Credit Card (%s)", cc.Number[len(cc.Number)-4:])
}

type PayPal struct {
	Email   string
	Balance float64
}

func (pp *PayPal) ProcessPayment(amount float64) bool {
	return amount <= pp.Balance
}

func (pp *PayPal) GetName() string {
	return fmt.Sprintf("PayPal (%s)", pp.Email)
}

type BankTransfer struct {
	AccountNumber string
	BankName      string
}

func (bt *BankTransfer) ProcessPayment(amount float64) bool {
	// Bank transfers typically always succeed if account is valid
	return true
}

func (bt *BankTransfer) GetName() string {
	return fmt.Sprintf("Bank Transfer (%s)", bt.BankName)
}

// File system polymorphism
type File interface {
	GetName() string
	GetSize() int
	GetType() string
	Open() string
	Close() string
}

type TextFile struct {
	Name string
	Size int
}

func (tf *TextFile) GetName() string {
	return tf.Name
}

func (tf *TextFile) GetSize() int {
	return tf.Size
}

func (tf *TextFile) GetType() string {
	return "Text"
}

func (tf *TextFile) Open() string {
	return fmt.Sprintf("Opening text file %s for editing", tf.Name)
}

func (tf *TextFile) Close() string {
	return fmt.Sprintf("Closing text file %s", tf.Name)
}

type ImageFile struct {
	Name   string
	Size   int
	Format string
}

func (ifile *ImageFile) GetName() string {
	return ifile.Name
}

func (ifile *ImageFile) GetSize() int {
	return ifile.Size
}

func (ifile *ImageFile) GetType() string {
	return fmt.Sprintf("Image (%s)", ifile.Format)
}

func (ifile *ImageFile) Open() string {
	return fmt.Sprintf("Opening image %s in image viewer", ifile.Name)
}

func (ifile *ImageFile) Close() string {
	return fmt.Sprintf("Closing image %s", ifile.Name)
}

type VideoFile struct {
	Name     string
	Size     int
	Duration int // in seconds
}

func (vf *VideoFile) GetName() string {
	return vf.Name
}

func (vf *VideoFile) GetSize() int {
	return vf.Size
}

func (vf *VideoFile) GetType() string {
	return fmt.Sprintf("Video (%d seconds)", vf.Duration)
}

func (vf *VideoFile) Open() string {
	return fmt.Sprintf("Playing video %s", vf.Name)
}

func (vf *VideoFile) Close() string {
	return fmt.Sprintf("Stopping video %s", vf.Name)
}

// Advanced polymorphism examples

type Vehicle interface {
	Start() string
	Stop() string
	Drive() string
}

type Car struct {
	Brand string
	Model string
}

func (c *Car) Start() string {
	return fmt.Sprintf("%s %s engine starts", c.Brand, c.Model)
}

func (c *Car) Stop() string {
	return fmt.Sprintf("%s %s brakes applied", c.Brand, c.Model)
}

func (c *Car) Drive() string {
	return fmt.Sprintf("%s %s is driving", c.Brand, c.Model)
}

type Motorcycle struct {
	Brand string
	Type  string
}

func (m *Motorcycle) Start() string {
	return fmt.Sprintf("%s %s roars to life", m.Brand, m.Type)
}

func (m *Motorcycle) Stop() string {
	return fmt.Sprintf("%s %s slows down", m.Brand, m.Type)
}

func (m *Motorcycle) Drive() string {
	return fmt.Sprintf("%s %s speeds down the road", m.Brand, m.Type)
}

// Factory pattern with polymorphism
type ShapeFactory struct{}

func (sf ShapeFactory) CreateShape(shapeType string, dimensions ...float64) Shape {
	switch shapeType {
	case "rectangle":
		if len(dimensions) >= 2 {
			return &Rectangle{Width: dimensions[0], Height: dimensions[1]}
		}
	case "circle":
		if len(dimensions) >= 1 {
			return &Circle{Radius: dimensions[0]}
		}
	case "triangle":
		if len(dimensions) >= 2 {
			return &Triangle{Base: dimensions[0], Height: dimensions[1]}
		}
	case "square":
		if len(dimensions) >= 1 {
			return &Square{Side: dimensions[0]}
		}
	}
	return nil
}

// Demonstrate advanced polymorphism
func demonstrateAdvancedPolymorphism() {
	fmt.Println("\n--- Advanced Polymorphism Examples ---")
	
	// Vehicle polymorphism
	vehicles := []Vehicle{
		&Car{Brand: "Toyota", Model: "Camry"},
		&Motorcycle{Brand: "Harley", Type: "Cruiser"},
	}
	
	fmt.Println("Vehicle Actions:")
	for _, vehicle := range vehicles {
		fmt.Printf("%s\n", vehicle.Start())
		fmt.Printf("%s\n", vehicle.Drive())
		fmt.Printf("%s\n\n", vehicle.Stop())
	}
	
	// Factory pattern
	factory := ShapeFactory{}
	
	shapes := []Shape{
		factory.CreateShape("rectangle", 5, 3),
		factory.CreateShape("circle", 4),
		factory.CreateShape("triangle", 6, 4),
		factory.CreateShape("square", 5),
	}
	
	fmt.Println("Factory-created shapes:")
	for i, shape := range shapes {
		fmt.Printf("Shape %d: Area = %.2f\n", i+1, shape.Area())
	}
}

// Strategy pattern with polymorphism
type SortStrategy interface {
	Sort(data []int) []int
}

type BubbleSort struct{}

func (bs BubbleSort) Sort(data []int) []int {
	// Simple bubble sort implementation
	result := make([]int, len(data))
	copy(result, data)
	
	n := len(result)
	for i := 0; i < n-1; i++ {
		for j := 0; j < n-i-1; j++ {
			if result[j] > result[j+1] {
				result[j], result[j+1] = result[j+1], result[j]
			}
		}
	}
	return result
}

type QuickSort struct{}

func (qs QuickSort) Sort(data []int) []int {
	// Simplified quicksort
	if len(data) <= 1 {
		return data
	}
	
	pivot := data[0]
	var less, greater []int
	
	for _, value := range data[1:] {
		if value <= pivot {
			less = append(less, value)
		} else {
			greater = append(greater, value)
		}
	}
	
	result := append(append(qs.Sort(less), pivot), qs.Sort(greater)...)
	return result
}

type Sorter struct {
	strategy SortStrategy
}

func (s *Sorter) SetStrategy(strategy SortStrategy) {
	s.strategy = strategy
}

func (s *Sorter) SortData(data []int) []int {
	return s.strategy.Sort(data)
}

func demonstrateStrategyPattern() {
	fmt.Println("\n--- Strategy Pattern ---")
	
	data := []int{64, 34, 25, 12, 22, 11, 90}
	
	sorter := Sorter{}
	
	sorter.SetStrategy(BubbleSort{})
	bubbleSorted := sorter.SortData(data)
	fmt.Printf("Bubble sorted: %v\n", bubbleSorted)
	
	sorter.SetStrategy(QuickSort{})
	quickSorted := sorter.SortData(data)
	fmt.Printf("Quick sorted: %v\n", quickSorted)
}
