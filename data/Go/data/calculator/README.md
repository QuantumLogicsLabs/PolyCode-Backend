# Calculator Package

A comprehensive mathematical calculator package for Go that provides basic, scientific, statistical, and advanced mathematical operations.

## Overview

The calculator package is organized into several components:

- **Basic** - Simple arithmetic operations
- **Scientific** - Advanced mathematical functions
- **Statistics** - Statistical analysis functions
- **Advanced** - Complex mathematical operations

## Files

- **calculator.go** - Basic arithmetic operations
- **scientific.go** - Scientific mathematical functions
- **statistics.go** - Statistical analysis tools
- **advanced.go** - Advanced mathematical operations
- **README.md** - This file

## Features

### Basic Operations
- Addition, subtraction, multiplication, division
- Power and square root operations
- Sum and average of slices

### Scientific Functions
- Trigonometric functions (sin, cos, tan, arcsin, arccos, arctan)
- Logarithmic functions (log, log10, natural log)
- Exponential functions
- Factorial, permutations, combinations
- GCD, LCM, prime checking
- Fibonacci sequence

### Statistical Analysis
- Descriptive statistics (mean, median, mode, variance, std dev)
- Percentiles and quartiles
- Correlation and covariance
- Linear regression
- Moving averages
- Histograms and confidence intervals

### Advanced Mathematics
- Arbitrary precision arithmetic
- Matrix operations
- Complex number arithmetic
- Polynomial operations
- Numerical methods (Newton-Raphson, bisection)
- Special functions (Gamma, Beta, Error function)

## Usage Examples

### Basic Calculator
```go
package main

import (
    "fmt"
    "go-learning-guide/calculator"
)

func main() {
    calc := calculator.NewBasic()
    
    // Basic operations
    result := calc.Add(10, 5)
    fmt.Printf("10 + 5 = %d\n", result)
    
    // Slice operations
    numbers := []int{1, 2, 3, 4, 5}
    sum := calc.Sum(numbers)
    fmt.Printf("Sum of %v = %d\n", numbers, sum)
}
```

### Scientific Calculator
```go
package main

import (
    "fmt"
    "go-learning-guide/calculator"
)

func main() {
    sci := calculator.NewScientific()
    
    // Trigonometric functions
    angle := sci.DegreesToRadians(45)
    sin := sci.Sine(angle)
    fmt.Printf("sin(45°) = %.4f\n", sin)
    
    // Logarithmic functions
    result, err := sci.Logarithm(10)
    if err != nil {
        fmt.Printf("Error: %v\n", err)
    } else {
        fmt.Printf("ln(10) = %.4f\n", result)
    }
    
    // Factorial
    fact, err := sci.Factorial(5)
    if err != nil {
        fmt.Printf("Error: %v\n", err)
    } else {
        fmt.Printf("5! = %d\n", fact)
    }
}
```

### Statistical Analysis
```go
package main

import (
    "fmt"
    "go-learning-guide/calculator"
)

func main() {
    stats := calculator.NewStatistics()
    
    data := []float64{1, 2, 3, 4, 5, 6, 7, 8, 9, 10}
    
    // Descriptive statistics
    descStats, err := stats.CalculateDescriptiveStats(data)
    if err != nil {
        fmt.Printf("Error: %v\n", err)
        return
    }
    
    fmt.Printf("Mean: %.2f\n", descStats.Mean)
    fmt.Printf("Median: %.2f\n", descStats.Median)
    fmt.Printf("Std Dev: %.2f\n", descStats.StdDev)
    
    // Correlation
    x := []float64{1, 2, 3, 4, 5}
    y := []float64{2, 4, 6, 8, 10}
    
    corr, err := stats.Correlation(x, y)
    if err != nil {
        fmt.Printf("Error: %v\n", err)
    } else {
        fmt.Printf("Correlation: %.4f\n", corr)
    }
}
```

### Advanced Operations
```go
package main

import (
    "fmt"
    "go-learning-guide/calculator"
)

func main() {
    adv := calculator.NewAdvanced()
    
    // Big number arithmetic
    big1, _ := calculator.NewBigNumber("12345678901234567890")
    big2, _ := calculator.NewBigNumber("98765432109876543210")
    
    sum := big1.Add(big2)
    fmt.Printf("Big sum: %s\n", sum.String())
    
    // Matrix operations
    m1 := calculator.NewMatrixFromData([][]float64{
        {1, 2},
        {3, 4},
    })
    
    m2 := calculator.NewMatrixFromData([][]float64{
        {5, 6},
        {7, 8},
    })
    
    product, _ := m1.Multiply(m2)
    fmt.Printf("Matrix product:\n%s", product.String())
    
    // Complex numbers
    c1 := calculator.NewComplex(3, 4)
    c2 := calculator.NewComplex(1, 2)
    
    sumComplex := c1.Add(c2)
    fmt.Printf("Complex sum: %s\n", sumComplex.String())
    
    // Polynomial operations
    poly := calculator.NewPolynomial([]float64{1, 2, 3}) // 1 + 2x + 3x²
    value := poly.Evaluate(2)
    fmt.Printf("P(2) = %.2f\n", value)
    
    derivative := poly.Derivative()
    fmt.Printf("P'(x) = %s\n", derivative.String())
}
```

## API Reference

### Basic Calculator

#### Methods
- `Add(a, b int) int` - Add two integers
- `Subtract(a, b int) int` - Subtract two integers
- `Multiply(a, b int) int` - Multiply two integers
- `Divide(a, b int) (int, error)` - Divide two integers
- `Power(base, exponent int) int` - Calculate base^exponent
- `SquareRoot(x int) (int, error)` - Calculate square root
- `Sum(numbers []int) int` - Sum of integers in slice
- `Average(numbers []int) float64` - Average of integers in slice

### Scientific Calculator

#### Methods
- `Power(base, exponent float64) float64` - Calculate power
- `SquareRoot(x float64) (float64, error)` - Calculate square root
- `Logarithm(x float64) (float64, error)` - Natural logarithm
- `Log10(x float64) (float64, error)` - Base-10 logarithm
- `Sine(angle float64) float64` - Sine function
- `Cosine(angle float64) float64` - Cosine function
- `Tangent(angle float64) float64` - Tangent function
- `DegreesToRadians(degrees float64) float64` - Convert degrees to radians
- `RadiansToDegrees(radians float64) float64` - Convert radians to degrees
- `Factorial(n int) (int, error)` - Calculate factorial
- `Permutation(n, k int) (int, error)` - Calculate nPk
- `Combination(n, k int) (int, error)` - Calculate nCk
- `GCD(a, b int) int` - Greatest common divisor
- `LCM(a, b int) int` - Least common multiple
- `IsPrime(n int) bool` - Check if number is prime
- `Fibonacci(n int) (int, error)` - Calculate nth Fibonacci number

### Statistics Calculator

#### Methods
- `CalculateDescriptiveStats(data []float64) (*DescriptiveStatistics, error)` - Comprehensive statistics
- `Mean(data []float64) (float64, error)` - Calculate mean
- `Median(data []float64) (float64, error)` - Calculate median
- `Mode(data []float64) []int` - Calculate mode(s)
- `Variance(data []float64) (float64, error)` - Calculate variance
- `StandardDeviation(data []float64) (float64, error)` - Calculate standard deviation
- `Percentile(data []float64, p float64) (float64, error)` - Calculate percentile
- `Correlation(x, y []float64) (float64, error)` - Calculate correlation
- `LinearRegression(x, y []float64) (*LinearRegression, error)` - Linear regression
- `MovingAverage(data []float64, windowSize int) ([]float64, error)` - Moving average
- `Histogram(data []float64, numBins int) ([]HistogramBin, error)` - Create histogram

### Advanced Calculator

#### Types
- `BigNumber` - Arbitrary precision arithmetic
- `Matrix` - Matrix operations
- `ComplexNumber` - Complex number arithmetic
- `Polynomial` - Polynomial operations

#### Methods
- `NewtonRaphson(f, df func(float64) float64, x0, tolerance float64, maxIterations int) (float64, error)` - Root finding
- `Bisection(f func(float64) float64, a, b, tolerance float64, maxIterations int) (float64, error)` - Root finding
- `NumericalIntegration(f func(float64) float64, a, b float64, n int) (float64, error)` - Numerical integration
- `Gamma(x float64) float64` - Gamma function
- `Beta(x, y float64) float64` - Beta function
- `Erf(x float64) float64` - Error function

## Error Handling

The calculator package follows Go's error handling conventions:

```go
result, err := sci.Factorial(-1)
if err != nil {
    // Handle error
    fmt.Printf("Error: %v\n", err)
} else {
    // Use result
    fmt.Printf("Result: %d\n", result)
}
```

Common errors include:
- Division by zero
- Invalid input for mathematical functions (e.g., negative numbers for square roots)
- Empty datasets for statistical functions
- Matrix dimension mismatches

## Performance Considerations

### Big Numbers
- Use `BigNumber` for calculations requiring high precision
- `BigNumber` operations are slower than native float64 operations
- Consider precision requirements before using `BigNumber`

### Statistical Functions
- Large datasets may require significant memory
- Some operations (like sorting) have O(n log n) complexity
- Use streaming algorithms for very large datasets

### Matrix Operations
- Matrix multiplication is O(n³) complexity
- Consider sparse matrix libraries for large matrices
- Use parallel algorithms for large matrix operations

## Testing

Run tests with:

```bash
go test ./calculator
go test -v ./calculator
go test -bench ./calculator
```

## Examples

The calculator package includes comprehensive examples in the main Go learning guide. See the `data/` directory for complete usage examples.

## Dependencies

The calculator package uses only Go standard library:
- `math` - Mathematical functions
- `math/big` - Arbitrary precision arithmetic
- `sort` - Sorting algorithms
- `fmt` - Formatting
- `strings` - String operations

## Contributing

When contributing to the calculator package:

1. Follow Go coding conventions
2. Add comprehensive tests for new functions
3. Update documentation
4. Consider performance implications
5. Handle edge cases appropriately

## License

This package is part of the Go learning guide and is provided for educational purposes.

## Version History

- **v1.0.0** - Initial release with basic, scientific, and statistical functions
- **v1.1.0** - Added advanced mathematical operations
- **v1.2.0** - Improved error handling and performance optimizations

## Related Packages

- `go-learning-guide/formatter` - String formatting utilities
- `go-learning-guide/validator` - Input validation functions

## Troubleshooting

### Common Issues

1. **Precision Loss**: Use `BigNumber` for high-precision requirements
2. **Performance Issues**: Consider algorithm complexity for large datasets
3. **Memory Usage**: Monitor memory usage with large matrices or datasets
4. **Error Handling**: Always check returned errors

### Debugging Tips

1. Use `fmt.Printf` for debugging intermediate values
2. Check input ranges before calling functions
3. Use test cases to verify correctness
4. Profile performance-critical sections

## Best Practices

1. **Error Handling**: Always check and handle errors
2. **Input Validation**: Validate inputs before calculations
3. **Precision**: Choose appropriate precision for your use case
4. **Performance**: Consider algorithm complexity
5. **Testing**: Write comprehensive tests for mathematical functions

## Mathematical Constants

The scientific calculator provides common mathematical constants:

```go
fmt.Printf("π = %.10f\n", calculator.Pi)
fmt.Printf("e = %.10f\n", calculator.E)
fmt.Printf("Golden Ratio = %.10f\n", calculator.GoldenRatio)
fmt.Printf("√2 = %.10f\n", calculator.Sqrt2)
```

## Integration Examples

### Web Application
```go
func calculateHandler(w http.ResponseWriter, r *http.Request) {
    a := parseFloat(r.FormValue("a"))
    b := parseFloat(r.FormValue("b"))
    operation := r.FormValue("op")
    
    calc := calculator.NewBasic()
    var result float64
    
    switch operation {
    case "add":
        result = float64(calc.Add(int(a), int(b)))
    case "subtract":
        result = float64(calc.Subtract(int(a), int(b)))
    // ... other operations
    }
    
    fmt.Fprintf(w, "Result: %.2f", result)
}
```

### CLI Application
```go
func main() {
    calc := calculator.NewScientific()
    
    for {
        fmt.Print("Enter expression (or 'quit'): ")
        input := scanLine()
        
        if input == "quit" {
            break
        }
        
        result, err := evaluateExpression(calc, input)
        if err != nil {
            fmt.Printf("Error: %v\n", err)
        } else {
            fmt.Printf("Result: %f\n", result)
        }
    }
}
```

## Future Enhancements

Planned features for future versions:

1. **GPU Acceleration** - CUDA/OpenCL support for large matrix operations
2. **Streaming Algorithms** - Memory-efficient algorithms for big data
3. **Machine Learning** - Basic ML algorithms (linear regression, clustering)
4. **Symbolic Mathematics** - Computer algebra system features
5. **Visualization** - Basic plotting and charting capabilities
