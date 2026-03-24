package main

import (
	"fmt"
	"math"
	"strings"
	"time"
)

func main() {
	fmt.Println("=== Methods in Go ===")
	
	// Value receiver methods
	fmt.Println("\n--- Value Receiver Methods ---")
	
	rect := Rectangle{Width: 10, Height: 5}
	fmt.Printf("Rectangle: %.2f x %.2f\n", rect.Width, rect.Height)
	fmt.Printf("Area: %.2f\n", rect.Area())
	fmt.Printf("Perimeter: %.2f\n", rect.Perimeter())
	fmt.Printf("Is square: %t\n", rect.IsSquare())
	
	// Pointer receiver methods
	fmt.Println("\n--- Pointer Receiver Methods ---")
	
	circle := &Circle{Radius: 7}
	fmt.Printf("Circle radius: %.2f\n", circle.Radius)
	fmt.Printf("Area: %.2f\n", circle.Area())
	
	// Modify using pointer receiver
	circle.Scale(2)
	fmt.Printf("After scaling by 2, new radius: %.2f\n", circle.Radius)
	fmt.Printf("New area: %.2f\n", circle.Area())
	
	// Method chaining
	fmt.Println("\n--- Method Chaining ---")
	
	bank := &BankAccount{Balance: 1000, Owner: "Alice"}
	bank.Deposit(500).Withdraw(200).ApplyInterest(0.05)
	fmt.Printf("Final balance: $%.2f\n", bank.Balance)
	
	// Methods with different receivers
	fmt.Println("\n--- Methods with Different Receivers ---")
	
	counter := Counter{value: 0}
	
	// Value receiver - doesn't modify original
	counter.Increment()
	fmt.Printf("After increment (value receiver): %d\n", counter.value)
	
	// Pointer receiver - modifies original
	counter.IncrementPtr()
	fmt.Printf("After increment (pointer receiver): %d\n", counter.value)
	
	// Methods on non-struct types
	fmt.Println("\n--- Methods on Non-Struct Types ---")
	
	temp := Temperature(25.5)
	fmt.Printf("Temperature: %.1f°C\n", temp)
	fmt.Printf("Fahrenheit: %.1f°F\n", temp.ToFahrenheit())
	fmt.Printf("Kelvin: %.1fK\n", temp.ToKelvin())
	
	// String method
	fmt.Printf("String representation: %s\n", temp.String())
	
	// Methods with multiple parameters
	fmt.Println("\n--- Methods with Multiple Parameters ---")
	
	student := Student{Name: "Alice", Grades: []int{85, 90, 78, 92, 88}}
	student.AddGrade(95).RemoveGrade(78).UpdateGrade(0, 88)
	fmt.Printf("Final grades: %v\n", student.Grades)
	fmt.Printf("Average: %.2f\n", student.Average())
	
	// Embedded structs and method promotion
	fmt.Println("\n--- Method Promotion ---")
	
	employee := Employee{
		Person: Person{Name: "Bob", Age: 30},
		ID:     "EMP001",
		Salary: 75000,
	}
	
	// Can call Person methods on Employee
	fmt.Printf("Employee: %s\n", employee.GetName())
	employee.SetName("Robert")
	fmt.Printf("After name change: %s\n", employee.GetName())
	
	// Employee-specific methods
	fmt.Printf("Annual salary: $%.2f\n", employee.AnnualSalary())
	
	// Interface methods
	fmt.Println("\n--- Interface Methods ---")
	
	var shapes []Shape
	shapes = append(shapes, &Rectangle{Width: 4, Height: 6})
	shapes = append(shapes, &Circle{Radius: 3})
	shapes = append(shapes, &Triangle{Base: 5, Height: 8})
	
	for i, shape := range shapes {
		fmt.Printf("Shape %d: Area = %.2f\n", i+1, shape.Area())
	}
	
	// Method expressions
	fmt.Println("\n--- Method Expressions ---")
	
	// Method as value
	areaFunc := Rectangle.Area
	rect2 := Rectangle{Width: 8, Height: 3}
	fmt.Printf("Area using method expression: %.2f\n", areaFunc(rect2))
	
	// Method values
	areaMethod := rect.Area
	fmt.Printf("Area using method value: %.2f\n", areaMethod())
	
	// Advanced method examples
	fmt.Println("\n--- Advanced Method Examples ---")
	
	// Builder pattern
	query := NewQueryBuilder().
		Select("name, age").
		From("users").
		Where("age > 18").
		OrderBy("name").
		Build()
	
	fmt.Printf("SQL Query: %s\n", query)
	
	// Fluent interface
	calculator := NewCalculator().
		Add(10).
		Multiply(2).
		Subtract(5).
		Divide(3)
	
	fmt.Printf("Calculator result: %.2f\n", calculator.GetResult())
}

// Basic structs for method examples

type Rectangle struct {
	Width  float64
	Height float64
}

// Value receiver method
func (r Rectangle) Area() float64 {
	return r.Width * r.Height
}

func (r Rectangle) Perimeter() float64 {
	return 2 * (r.Width + r.Height)
}

func (r Rectangle) IsSquare() bool {
	return r.Width == r.Height
}

type Circle struct {
	Radius float64
}

// Pointer receiver method
func (c *Circle) Area() float64 {
	return math.Pi * c.Radius * c.Radius
}

func (c *Circle) Scale(factor float64) {
	c.Radius *= factor
}

// Method chaining example
type BankAccount struct {
	Balance float64
	Owner   string
}

func (ba *BankAccount) Deposit(amount float64) *BankAccount {
	ba.Balance += amount
	fmt.Printf("Deposited: $%.2f, New balance: $%.2f\n", amount, ba.Balance)
	return ba
}

func (ba *BankAccount) Withdraw(amount float64) *BankAccount {
	if amount <= ba.Balance {
		ba.Balance -= amount
		fmt.Printf("Withdrew: $%.2f, New balance: $%.2f\n", amount, ba.Balance)
	} else {
		fmt.Printf("Insufficient funds for withdrawal: $%.2f\n", amount)
	}
	return ba
}

func (ba *BankAccount) ApplyInterest(rate float64) *BankAccount {
	interest := ba.Balance * rate
	ba.Balance += interest
	fmt.Printf("Applied interest: $%.2f, New balance: $%.2f\n", interest, ba.Balance)
	return ba
}

// Value vs pointer receiver comparison
type Counter struct {
	value int
}

// Value receiver - doesn't modify original
func (c Counter) Increment() {
	c.value++
	fmt.Printf("Incremented (value receiver): %d\n", c.value)
}

// Pointer receiver - modifies original
func (c *Counter) IncrementPtr() {
	c.value++
	fmt.Printf("Incremented (pointer receiver): %d\n", c.value)
}

// Method on non-struct type
type Temperature float64

func (t Temperature) ToFahrenheit() float64 {
	return float64(t)*9/5 + 32
}

func (t Temperature) ToKelvin() float64 {
	return float64(t) + 273.15
}

func (t Temperature) String() string {
	return fmt.Sprintf("%.1f°C", t)
}

// Methods with multiple parameters and return values
type Student struct {
	Name   string
	Grades []int
}

func (s *Student) AddGrade(grade int) *Student {
	s.Grades = append(s.Grades, grade)
	return s
}

func (s *Student) RemoveGrade(index int) *Student {
	if index >= 0 && index < len(s.Grades) {
		s.Grades = append(s.Grades[:index], s.Grades[index+1:]...)
	}
	return s
}

func (s *Student) UpdateGrade(index int, newGrade int) *Student {
	if index >= 0 && index < len(s.Grades) {
		s.Grades[index] = newGrade
	}
	return s
}

func (s Student) Average() float64 {
	if len(s.Grades) == 0 {
		return 0
	}
	
	sum := 0
	for _, grade := range s.Grades {
		sum += grade
	}
	
	return float64(sum) / float64(len(s.Grades))
}

// Embedded structs and method promotion
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

func (p Person) GetAge() int {
	return p.Age
}

type Employee struct {
	Person
	ID     string
	Salary float64
}

func (e Employee) AnnualSalary() float64 {
	return e.Salary * 12
}

// Interface for shapes
type Shape interface {
	Area() float64
}

type Triangle struct {
	Base   float64
	Height float64
}

func (t Triangle) Area() float64 {
	return 0.5 * t.Base * t.Height
}

// Builder pattern with methods
type QueryBuilder struct {
	selectClause string
	fromClause   string
	whereClause  string
	orderBy      string
}

func NewQueryBuilder() *QueryBuilder {
	return &QueryBuilder{}
}

func (qb *QueryBuilder) Select(columns string) *QueryBuilder {
	qb.selectClause = columns
	return qb
}

func (qb *QueryBuilder) From(table string) *QueryBuilder {
	qb.fromClause = table
	return qb
}

func (qb *QueryBuilder) Where(condition string) *QueryBuilder {
	qb.whereClause = condition
	return qb
}

func (qb *QueryBuilder) OrderBy(column string) *QueryBuilder {
	qb.orderBy = column
	return qb
}

func (qb *QueryBuilder) Build() string {
	query := fmt.Sprintf("SELECT %s FROM %s", qb.selectClause, qb.fromClause)
	
	if qb.whereClause != "" {
		query += fmt.Sprintf(" WHERE %s", qb.whereClause)
	}
	
	if qb.orderBy != "" {
		query += fmt.Sprintf(" ORDER BY %s", qb.orderBy)
	}
	
	return query
}

// Fluent interface calculator
type Calculator struct {
	result float64
}

func NewCalculator() *Calculator {
	return &Calculator{result: 0}
}

func (c *Calculator) Add(value float64) *Calculator {
	c.result += value
	return c
}

func (c *Calculator) Subtract(value float64) *Calculator {
	c.result -= value
	return c
}

func (c *Calculator) Multiply(value float64) *Calculator {
	c.result *= value
	return c
}

func (c *Calculator) Divide(value float64) *Calculator {
	if value != 0 {
		c.result /= value
	}
	return c
}

func (c *Calculator) GetResult() float64 {
	return c.result
}

// Advanced method patterns

type Logger interface {
	Log(message string)
}

type ConsoleLogger struct {
	Prefix string
}

func (cl ConsoleLogger) Log(message string) {
	timestamp := time.Now().Format("2006-01-02 15:04:05")
	fmt.Printf("[%s] %s: %s\n", timestamp, cl.Prefix, message)
}

// Method with variadic parameters
type Math struct{}

func (m Math) Sum(numbers ...int) int {
	sum := 0
	for _, num := range numbers {
		sum += num
	}
	return sum
}

func (m Math) Average(numbers ...float64) float64 {
	if len(numbers) == 0 {
		return 0
	}
	
	sum := 0.0
	for _, num := range numbers {
		sum += num
	}
	
	return sum / float64(len(numbers))
}

// Method returning multiple values
type Divider struct{}

func (d Divider) Divide(dividend, divisor int) (int, int, error) {
	if divisor == 0 {
		return 0, 0, fmt.Errorf("cannot divide by zero")
	}
	
	quotient := dividend / divisor
	remainder := dividend % divisor
	
	return quotient, remainder, nil
}

// Method with closure
type Multiplier struct {
	factor int
}

func (m Multiplier) CreateMultiplier() func(int) int {
	return func(x int) int {
		return x * m.factor
	}
}

// Demonstrate advanced method patterns
func demonstrateAdvancedMethods() {
	fmt.Println("\n--- Advanced Method Patterns ---")
	
	// Logger interface
	logger := ConsoleLogger{Prefix: "INFO"}
	logger.Log("Application started")
	
	// Variadic methods
	math := Math{}
	sum := math.Sum(1, 2, 3, 4, 5)
	fmt.Printf("Sum of 1,2,3,4,5: %d\n", sum)
	
	avg := math.Average(1.5, 2.5, 3.5, 4.5)
	fmt.Printf("Average: %.2f\n", avg)
	
	// Multiple return values
	divider := Divider{}
	q, r, err := divider.Divide(17, 5)
	if err != nil {
		fmt.Printf("Error: %v\n", err)
	} else {
		fmt.Printf("17 ÷ 5 = %d remainder %d\n", q, r)
	}
	
	// Method with closure
	multiplier := Multiplier{factor: 3}
	triple := multiplier.CreateMultiplier()
	fmt.Printf("Triple of 7: %d\n", triple(7))
}
