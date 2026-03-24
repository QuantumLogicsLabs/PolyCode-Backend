package calculator

import (
	"fmt"
	"math"
)

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

// Sine calculates the sine of an angle in radians
func (s *Scientific) Sine(angle float64) float64 {
	return math.Sin(angle)
}

// Cosine calculates the cosine of an angle in radians
func (s *Scientific) Cosine(angle float64) float64 {
	return math.Cos(angle)
}

// Tangent calculates the tangent of an angle in radians
func (s *Scientific) Tangent(angle float64) float64 {
	return math.Tan(angle)
}

// ArcSine calculates the arcsine (inverse sine)
func (s *Scientific) ArcSine(value float64) (float64, error) {
	if value < -1 || value > 1 {
		return 0, fmt.Errorf("arcsine input must be between -1 and 1: %f", value)
	}
	return math.Asin(value), nil
}

// ArcCosine calculates the arccosine (inverse cosine)
func (s *Scientific) ArcCosine(value float64) (float64, error) {
	if value < -1 || value > 1 {
		return 0, fmt.Errorf("arccosine input must be between -1 and 1: %f", value)
	}
	return math.Acos(value), nil
}

// ArcTangent calculates the arctangent (inverse tangent)
func (s *Scientific) ArcTangent(value float64) float64 {
	return math.Atan(value)
}

// DegreesToRadians converts degrees to radians
func (s *Scientific) DegreesToRadians(degrees float64) float64 {
	return degrees * math.Pi / 180
}

// RadiansToDegrees converts radians to degrees
func (s *Scientific) RadiansToDegrees(radians float64) float64 {
	return radians * 180 / math.Pi
}

// Absolute returns the absolute value
func (s *Scientific) Absolute(x float64) float64 {
	return math.Abs(x)
}

// Factorial calculates the factorial of a non-negative integer
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

// Permutation calculates nPk (n permutations of k)
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

// Combination calculates nCk (n choose k)
func (s *Scientific) Combination(n, k int) (int, error) {
	if n < 0 || k < 0 {
		return 0, fmt.Errorf("combination is not defined for negative numbers: n=%d, k=%d", n, k)
	}
	if k > n {
		return 0, fmt.Errorf("k cannot be greater than n in combination: k=%d, n=%d", k, n)
	}
	
	// Use symmetry: nCk = nC(n-k)
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

// GreatestCommonDivisor finds the GCD of two integers
func (s *Scientific) GreatestCommonDivisor(a, b int) int {
	for b != 0 {
		a, b = b, a%b
	}
	return a
}

// LeastCommonMultiple finds the LCM of two integers
func (s *Scientific) LeastCommonMultiple(a, b int) int {
	if a == 0 || b == 0 {
		return 0
	}
	return s.Absolute(float64(a*b/s.GreatestCommonDivisor(a, b)))
}

// IsPrime checks if a number is prime
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
	
	i := 5
	for i*i <= n {
		if n%i == 0 || n%(i+2) == 0 {
			return false
		}
		i += 6
	}
	return true
}

// Fibonacci calculates the nth Fibonacci number
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

// Round rounds a float64 to the nearest integer
func (s *Scientific) Round(x float64) int {
	return int(math.Round(x))
}

// Ceil rounds up to the nearest integer
func (s *Scientific) Ceil(x float64) int {
	return int(math.Ceil(x))
}

// Floor rounds down to the nearest integer
func (s *Scientific) Floor(x float64) int {
	return int(math.Floor(x))
}

// Max returns the maximum of two numbers
func (s *Scientific) Max(a, b float64) float64 {
	return math.Max(a, b)
}

// Min returns the minimum of two numbers
func (s *Scientific) Min(a, b float64) float64 {
	return math.Min(a, b)
}

// Modulo calculates the remainder of division
func (s *Scientific) Modulo(a, b float64) float64 {
	return math.Mod(a, b)
}

// Exponential calculates e^x
func (s *Scientific) Exponential(x float64) float64 {
	return math.Exp(x)
}

// NaturalLog calculates ln(x)
func (s *Scientific) NaturalLog(x float64) (float64, error) {
	return s.Logarithm(x)
}

// HyperbolicSine calculates sinh(x)
func (s *Scientific) HyperbolicSine(x float64) float64 {
	return math.Sinh(x)
}

// HyperbolicCosine calculates cosh(x)
func (s *Scientific) HyperbolicCosine(x float64) float64 {
	return math.Cosh(x)
}

// HyperbolicTangent calculates tanh(x)
func (s *Scientific) HyperbolicTangent(x float64) float64 {
	return math.Tanh(x)
}

// Advanced operations

// StandardDeviation calculates the standard deviation of a slice
func (s *Scientific) StandardDeviation(numbers []float64) (float64, error) {
	if len(numbers) == 0 {
		return 0, fmt.Errorf("cannot calculate standard deviation of empty slice")
	}
	
	mean := s.mean(numbers)
	sum := 0.0
	for _, num := range numbers {
		sum += math.Pow(num-mean, 2)
	}
	
	variance := sum / float64(len(numbers))
	return math.Sqrt(variance), nil
}

// Variance calculates the variance of a slice
func (s *Scientific) Variance(numbers []float64) (float64, error) {
	if len(numbers) == 0 {
		return 0, fmt.Errorf("cannot calculate variance of empty slice")
	}
	
	mean := s.mean(numbers)
	sum := 0.0
	for _, num := range numbers {
		sum += math.Pow(num-mean, 2)
	}
	
	return sum / float64(len(numbers)), nil
}

// Mean calculates the arithmetic mean of a slice
func (s *Scientific) Mean(numbers []float64) (float64, error) {
	if len(numbers) == 0 {
		return 0, fmt.Errorf("cannot calculate mean of empty slice")
	}
	return s.mean(numbers), nil
}

// Median calculates the median of a slice
func (s *Scientific) Median(numbers []float64) (float64, error) {
	if len(numbers) == 0 {
		return 0, fmt.Errorf("cannot calculate median of empty slice")
	}
	
	sorted := make([]float64, len(numbers))
	copy(sorted, numbers)
	
	// Simple bubble sort for demonstration
	for i := 0; i < len(sorted); i++ {
		for j := 0; j < len(sorted)-1-i; j++ {
			if sorted[j] > sorted[j+1] {
				sorted[j], sorted[j+1] = sorted[j+1], sorted[j]
			}
		}
	}
	
	mid := len(sorted) / 2
	if len(sorted)%2 == 1 {
		return sorted[mid], nil
	}
	
	return (sorted[mid-1] + sorted[mid]) / 2, nil
}

// Helper function for mean calculation
func (s *Scientific) mean(numbers []float64) float64 {
	sum := 0.0
	for _, num := range numbers {
		sum += num
	}
	return sum / float64(len(numbers))
}

// Constants
const (
	// Pi mathematical constant
	Pi = math.Pi
	// E mathematical constant
	E = math.E
	// Golden ratio
	GoldenRatio = 1.618033988749895
	// Square root of 2
	Sqrt2 = math.Sqrt2
	// Natural logarithm of 2
	Ln2 = math.Ln2
	// Natural logarithm of 10
	Ln10 = math.Ln10
)

// GetConstants returns common mathematical constants
func (s *Scientific) GetConstants() map[string]float64 {
	return map[string]float64{
		"pi":          Pi,
		"e":           E,
		"goldenRatio": GoldenRatio,
		"sqrt2":       Sqrt2,
		"ln2":         Ln2,
		"ln10":        Ln10,
	}
}

// Trigonometric identities

// PythagoreanTheorem checks if a^2 + b^2 = c^2
func (s *Scientific) PythagoreanTheorem(a, b, c float64) bool {
	return s.Absolute(s.Power(a, 2)+s.Power(b, 2)-s.Power(c, 2)) < 1e-10
}

// QuadraticFormula solves ax^2 + bx + c = 0
func (s *Scientific) QuadraticFormula(a, b, c float64) ([]float64, error) {
	if a == 0 {
		return nil, fmt.Errorf("coefficient 'a' cannot be zero in quadratic equation")
	}
	
	discriminant := s.Power(b, 2) - 4*a*c
	if discriminant < 0 {
		return nil, fmt.Errorf("no real solutions: discriminant is negative: %f", discriminant)
	}
	
	sqrtDiscriminant, err := s.SquareRoot(discriminant)
	if err != nil {
		return nil, err
	}
	
	x1 := (-b + sqrtDiscriminant) / (2 * a)
	x2 := (-b - sqrtDiscriminant) / (2 * a)
	
	return []float64{x1, x2}, nil
}

// Distance calculates Euclidean distance between two points
func (s *Scientific) Distance(x1, y1, x2, y2 float64) float64 {
	dx := x2 - x1
	dy := y2 - y1
	return s.Sqrt(s.Power(dx, 2) + s.Power(dy, 2))
}

// Sqrt is an alias for SquareRoot
func (s *Scientific) Sqrt(x float64) (float64, error) {
	return s.SquareRoot(x)
}

// Log is an alias for Logarithm
func (s *Scientific) Log(x float64) (float64, error) {
	return s.Logarithm(x)
}

// Sin is an alias for Sine
func (s *Scientific) Sin(angle float64) float64 {
	return s.Sine(angle)
}

// Cos is an alias for Cosine
func (s *Scientific) Cos(angle float64) float64 {
	return s.Cosine(angle)
}

// Tan is an alias for Tangent
func (s *Scientific) Tan(angle float64) float64 {
	return s.Tangent(angle)
}

// Abs is an alias for Absolute
func (s *Scientific) Abs(x float64) float64 {
	return s.Absolute(x)
}

// Exp is an alias for Exponential
func (s *Scientific) Exp(x float64) float64 {
	return s.Exponential(x)
}

// GCD is an alias for GreatestCommonDivisor
func (s *Scientific) GCD(a, b int) int {
	return s.GreatestCommonDivisor(a, b)
}

// LCM is an alias for LeastCommonMultiple
func (s *Scientific) LCM(a, b int) int {
	return s.LeastCommonMultiple(a, b)
}
