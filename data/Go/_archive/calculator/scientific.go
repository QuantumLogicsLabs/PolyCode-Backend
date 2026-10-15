package main

import (
	"fmt"
	"math"
)

// Basic arithmetic struct
type Basic struct{}

func NewBasic() *Basic {
	return &Basic{}
}

func (b *Basic) Add(x, y float64) float64      { return x + y }
func (b *Basic) Subtract(x, y float64) float64 { return x - y }
func (b *Basic) Multiply(x, y float64) float64 { return x * y }
func (b *Basic) Divide(x, y float64) (float64, error) {
	if y == 0 {
		return 0, fmt.Errorf("division by zero")
	}
	return x / y, nil
}

// Scientific provides advanced mathematical operations
type Scientific struct {
	basic *Basic
}

// NewScientific creates a new scientific calculator
func NewScientific() *Scientific {
	return &Scientific{
		basic: NewBasic(),
	}
}

// Power calculates base raised to the power of exponent
func (s *Scientific) Power(base, exponent float64) float64 {
	return math.Pow(base, exponent)
}

// SquareRoot calculates the square root of a number
func (s *Scientific) SquareRoot(x float64) (float64, error) {
	if x < 0 {
		return 0, fmt.Errorf("cannot calculate square root of negative number: %f", x)
	}
	return math.Sqrt(x), nil
}

// Logarithm calculates the natural logarithm
func (s *Scientific) Logarithm(x float64) (float64, error) {
	if x <= 0 {
		return 0, fmt.Errorf("cannot calculate logarithm of non-positive number: %f", x)
	}
	return math.Log(x), nil
}

// Log10 calculates the base-10 logarithm
func (s *Scientific) Log10(x float64) (float64, error) {
	if x <= 0 {
		return 0, fmt.Errorf("cannot calculate base-10 logarithm of non-positive number: %f", x)
	}
	return math.Log10(x), nil
}

// Trigonometric functions
func (s *Scientific) Sine(angle float64) float64 {
	return math.Sin(angle)
}

func (s *Scientific) Cosine(angle float64) float64 {
	return math.Cos(angle)
}

func (s *Scientific) Tangent(angle float64) float64 {
	return math.Tan(angle)
}

func (s *Scientific) DegreesToRadians(degrees float64) float64 {
	return degrees * math.Pi / 180
}

func (s *Scientific) RadiansToDegrees(radians float64) float64 {
	return radians * 180 / math.Pi
}

func (s *Scientific) Absolute(x float64) float64 {
	return math.Abs(x)
}

// Combinatorics
func (s *Scientific) Factorial(n int) (int, error) {
	if n < 0 {
		return 0, fmt.Errorf("factorial is not defined for negative numbers: %d", n)
	}
	if n > 20 {
		return 0, fmt.Errorf("factorial result too large for n > 20: %d", n)
	}

	result := 1
	for i := 2; i <= n; i++ {
		result *= i
	}
	return result, nil
}

func (s *Scientific) Permutation(n, k int) (int, error) {
	if n < 0 || k < 0 {
		return 0, fmt.Errorf("permutation is not defined for negative numbers: n=%d, k=%d", n, k)
	}
	if k > n {
		return 0, fmt.Errorf("k cannot be greater than n in permutation: k=%d, n=%d", k, n)
	}

	result := 1
	for i := 0; i < k; i++ {
		result *= (n - i)
	}
	return result, nil
}

func (s *Scientific) Combination(n, k int) (int, error) {
	if n < 0 || k < 0 {
		return 0, fmt.Errorf("combination is not defined for negative numbers: n=%d, k=%d", n, k)
	}
	if k > n {
		return 0, fmt.Errorf("k cannot be greater than n in combination: k=%d, n=%d", k, n)
	}

	if k > n-k {
		k = n - k
	}

	result := 1
	for i := 0; i < k; i++ {
		result *= (n - i)
		result /= (i + 1)
	}
	return result, nil
}

func (s *Scientific) GreatestCommonDivisor(a, b int) int {
	for b != 0 {
		a, b = b, a%b
	}
	return a
}

func (s *Scientific) LeastCommonMultiple(a, b int) int {
	if a == 0 || b == 0 {
		return 0
	}
	gcd := s.GreatestCommonDivisor(a, b)
	val := (a * b) / gcd
	if val < 0 {
		return -val
	}
	return val
}

func (s *Scientific) IsPrime(n int) bool {
	if n <= 1 {
		return false
	}
	if n <= 3 {
		return true
	}
	if n%2 == 0 || n%3 == 0 {
		return false
	}

	for i := 5; i*i <= n; i += 6 {
		if n%i == 0 || n%(i+2) == 0 {
			return false
		}
	}
	return true
}

func (s *Scientific) Fibonacci(n int) (int, error) {
	if n < 0 {
		return 0, fmt.Errorf("Fibonacci is not defined for negative numbers: %d", n)
	}
	if n > 92 {
		return 0, fmt.Errorf("Fibonacci result too large for n > 92: %d", n)
	}

	if n == 0 {
		return 0, nil
	}
	if n == 1 {
		return 1, nil
	}

	a, b := 0, 1
	for i := 2; i <= n; i++ {
		a, b = b, a+b
	}
	return b, nil
}

// Quadratic solver
func (s *Scientific) QuadraticFormula(a, b, c float64) ([]float64, error) {
	if a == 0 {
		return nil, fmt.Errorf("coefficient 'a' cannot be zero in quadratic equation")
	}

	discriminant := s.Power(b, 2) - 4*a*c
	if discriminant < 0 {
		return nil, fmt.Errorf("no real solutions: discriminant is negative: %f", discriminant)
	}

	sqrtDisc, err := s.SquareRoot(discriminant)
	if err != nil {
		return nil, err
	}

	x1 := (-b + sqrtDisc) / (2 * a)
	x2 := (-b - sqrtDisc) / (2 * a)

	return []float64{x1, x2}, nil
}

func main() {
	fmt.Println("=== Scientific Calculator Demo ===")
	sci := NewScientific()

	// Trigonometry
	deg := 45.0
	rad := sci.DegreesToRadians(deg)
	fmt.Printf("sin(%.0f°) = %.4f\n", deg, sci.Sine(rad))
	fmt.Printf("cos(%.0f°) = %.4f\n", deg, sci.Cosine(rad))

	// Combinatorics
	fact, _ := sci.Factorial(5)
	perm, _ := sci.Permutation(5, 2)
	comb, _ := sci.Combination(5, 2)
	fmt.Printf("5! = %d | 5P2 = %d | 5C2 = %d\n", fact, perm, comb)

	// Number Theory
	fmt.Printf("GCD(48, 18) = %d\n", sci.GreatestCommonDivisor(48, 18))
	fmt.Printf("LCM(48, 18) = %d\n", sci.LeastCommonMultiple(48, 18))
	fmt.Printf("IsPrime(29) = %t\n", sci.IsPrime(29))

	// Fibonacci
	fib, _ := sci.Fibonacci(10)
	fmt.Printf("10th Fibonacci Number = %d\n", fib)

	// Quadratic Equation: x^2 - 5x + 6 = 0 -> Roots (3, 2)
	roots, _ := sci.QuadraticFormula(1, -5, 6)
	fmt.Printf("Roots for x^2 - 5x + 6 = 0: x1=%.2f, x2=%.2f\n", roots[0], roots[1])
}
