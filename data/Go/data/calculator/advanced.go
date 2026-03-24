package calculator

import (
	"fmt"
	"math"
	"math/big"
	"strconv"
	"strings"
)

// Advanced provides advanced mathematical operations
type Advanced struct {
	scientific *Scientific
	statistics *Statistics
}

// NewAdvanced creates a new advanced calculator
func NewAdvanced() *Advanced {
	return &Advanced{
		scientific: NewScientific(),
		statistics: NewStatistics(),
	}
}

// BigNumber represents arbitrary precision numbers
type BigNumber struct {
	value *big.Float
}

// NewBigNumber creates a new big number from string
func NewBigNumber(s string) (*BigNumber, error) {
	f, ok := new(big.Float).SetString(s)
	if !ok {
		return nil, fmt.Errorf("invalid number format: %s", s)
	}
	return &BigNumber{value: f}, nil
}

// NewBigNumberFromFloat creates a new big number from float64
func NewBigNumberFromFloat(f float64) *BigNumber {
	return &BigNumber{value: big.NewFloat(f)}
}

// Add adds two big numbers
func (bn *BigNumber) Add(other *BigNumber) *BigNumber {
	result := new(big.Float).Add(bn.value, other.value)
	return &BigNumber{value: result}
}

// Subtract subtracts two big numbers
func (bn *BigNumber) Subtract(other *BigNumber) *BigNumber {
	result := new(big.Float).Sub(bn.value, other.value)
	return &BigNumber{value: result}
}

// Multiply multiplies two big numbers
func (bn *BigNumber) Multiply(other *BigNumber) *BigNumber {
	result := new(big.Float).Mul(bn.value, other.value)
	return &BigNumber{value: result}
}

// Divide divides two big numbers
func (bn *BigNumber) Divide(other *BigNumber) (*BigNumber, error) {
	if other.value.Sign() == 0 {
		return nil, fmt.Errorf("division by zero")
	}
	result := new(big.Float).Quo(bn.value, other.value)
	return &BigNumber{value: result}, nil
}

// Power raises a big number to an integer power
func (bn *BigNumber) Power(exp int) *BigNumber {
	result := new(big.Float).Copy(bn.value)
	for i := 1; i < exp; i++ {
		result.Mul(result, bn.value)
	}
	return &BigNumber{value: result}
}

// String returns the string representation
func (bn *BigNumber) String() string {
	return bn.value.String()
}

// ToFloat converts to float64 (may lose precision)
func (bn *BigNumber) ToFloat() float64 {
	f, _ := bn.value.Float64()
	return f
}

// Matrix represents a mathematical matrix
type Matrix struct {
	data [][]float64
	rows int
	cols int
}

// NewMatrix creates a new matrix
func NewMatrix(rows, cols int) *Matrix {
	data := make([][]float64, rows)
	for i := range data {
		data[i] = make([]float64, cols)
	}
	return &Matrix{
		data: data,
		rows: rows,
		cols: cols,
	}
}

// NewMatrixFromData creates a matrix from 2D slice
func NewMatrixFromData(data [][]float64) (*Matrix, error) {
	if len(data) == 0 {
		return nil, fmt.Errorf("matrix data cannot be empty")
	}
	
	rows := len(data)
	cols := len(data[0])
	
	// Validate all rows have same length
	for i, row := range data {
		if len(row) != cols {
			return nil, fmt.Errorf("row %d has %d columns, expected %d", i, len(row), cols)
		}
	}
	
	matrix := NewMatrix(rows, cols)
	for i := range data {
		copy(matrix.data[i], data[i])
	}
	
	return matrix, nil
}

// Get returns the value at (row, col)
func (m *Matrix) Get(row, col int) (float64, error) {
	if row < 0 || row >= m.rows || col < 0 || col >= m.cols {
		return 0, fmt.Errorf("index out of bounds: (%d, %d)", row, col)
	}
	return m.data[row][col], nil
}

// Set sets the value at (row, col)
func (m *Matrix) Set(row, col int, value float64) error {
	if row < 0 || row >= m.rows || col < 0 || col >= m.cols {
		return fmt.Errorf("index out of bounds: (%d, %d)", row, col)
	}
	m.data[row][col] = value
	return nil
}

// Add adds two matrices
func (m *Matrix) Add(other *Matrix) (*Matrix, error) {
	if m.rows != other.rows || m.cols != other.cols {
		return nil, fmt.Errorf("matrix dimensions must match for addition")
	}
	
	result := NewMatrix(m.rows, m.cols)
	for i := 0; i < m.rows; i++ {
		for j := 0; j < m.cols; j++ {
			result.data[i][j] = m.data[i][j] + other.data[i][j]
		}
	}
	
	return result, nil
}

// Subtract subtracts two matrices
func (m *Matrix) Subtract(other *Matrix) (*Matrix, error) {
	if m.rows != other.rows || m.cols != other.cols {
		return nil, fmt.Errorf("matrix dimensions must match for subtraction")
	}
	
	result := NewMatrix(m.rows, m.cols)
	for i := 0; i < m.rows; i++ {
		for j := 0; j < m.cols; j++ {
			result.data[i][j] = m.data[i][j] - other.data[i][j]
		}
	}
	
	return result, nil
}

// Multiply multiplies two matrices
func (m *Matrix) Multiply(other *Matrix) (*Matrix, error) {
	if m.cols != other.rows {
		return nil, fmt.Errorf("matrix dimensions incompatible for multiplication: %dx%d * %dx%d",
			m.rows, m.cols, other.rows, other.cols)
	}
	
	result := NewMatrix(m.rows, other.cols)
	for i := 0; i < m.rows; i++ {
		for j := 0; j < other.cols; j++ {
			sum := 0.0
			for k := 0; k < m.cols; k++ {
				sum += m.data[i][k] * other.data[k][j]
			}
			result.data[i][j] = sum
		}
	}
	
	return result, nil
}

// Transpose returns the transpose of the matrix
func (m *Matrix) Transpose() *Matrix {
	result := NewMatrix(m.cols, m.rows)
	for i := 0; i < m.rows; i++ {
		for j := 0; j < m.cols; j++ {
			result.data[j][i] = m.data[i][j]
		}
	}
	return result
}

// Determinant calculates the determinant (for square matrices only)
func (m *Matrix) Determinant() (float64, error) {
	if m.rows != m.cols {
		return 0, fmt.Errorf("determinant only defined for square matrices")
	}
	
	return m.determinantRecursive(), nil
}

func (m *Matrix) determinantRecursive() float64 {
	if m.rows == 1 {
		return m.data[0][0]
	}
	
	if m.rows == 2 {
		return m.data[0][0]*m.data[1][1] - m.data[0][1]*m.data[1][0]
	}
	
	det := 0.0
	for col := 0; col < m.cols; col++ {
		cofactor := m.getCofactor(0, col)
		cofactorDet, _ := cofactor.determinantRecursive()
		det += math.Pow(-1, float64(col)) * m.data[0][col] * cofactorDet
	}
	
	return det
}

func (m *Matrix) getCofactor(row, col int) *Matrix {
	result := NewMatrix(m.rows-1, m.cols-1)
	resultRow := 0
	
	for i := 0; i < m.rows; i++ {
		if i == row {
			continue
		}
		resultCol := 0
		for j := 0; j < m.cols; j++ {
			if j == col {
				continue
			}
			result.data[resultRow][resultCol] = m.data[i][j]
			resultCol++
		}
		resultRow++
	}
	
	return result
}

// Identity creates an identity matrix
func Identity(size int) *Matrix {
	result := NewMatrix(size, size)
	for i := 0; i < size; i++ {
		result.data[i][i] = 1
	}
	return result
}

// String returns string representation
func (m *Matrix) String() string {
	var sb strings.Builder
	
	for i := 0; i < m.rows; i++ {
		sb.WriteString("[")
		for j := 0; j < m.cols; j++ {
			sb.WriteString(fmt.Sprintf("%8.2f", m.data[i][j]))
			if j < m.cols-1 {
				sb.WriteString(" ")
			}
		}
		sb.WriteString("]\n")
	}
	
	return sb.String()
}

// ComplexNumber represents a complex number
type ComplexNumber struct {
	Real float64
	Imag float64
}

// NewComplex creates a new complex number
func NewComplex(real, imag float64) *ComplexNumber {
	return &ComplexNumber{Real: real, Imag: imag}
}

// Add adds two complex numbers
func (c *ComplexNumber) Add(other *ComplexNumber) *ComplexNumber {
	return &ComplexNumber{
		Real: c.Real + other.Real,
		Imag: c.Imag + other.Imag,
	}
}

// Subtract subtracts two complex numbers
func (c *ComplexNumber) Subtract(other *ComplexNumber) *ComplexNumber {
	return &ComplexNumber{
		Real: c.Real - other.Real,
		Imag: c.Imag - other.Imag,
	}
}

// Multiply multiplies two complex numbers
func (c *ComplexNumber) Multiply(other *ComplexNumber) *ComplexNumber {
	real := c.Real*other.Real - c.Imag*other.Imag
	imag := c.Real*other.Imag + c.Imag*other.Real
	return &ComplexNumber{Real: real, Imag: imag}
}

// Conjugate returns the complex conjugate
func (c *ComplexNumber) Conjugate() *ComplexNumber {
	return &ComplexNumber{Real: c.Real, Imag: -c.Imag}
}

// Magnitude returns the magnitude (absolute value)
func (c *ComplexNumber) Magnitude() float64 {
	return math.Sqrt(c.Real*c.Real + c.Imag*c.Imag)
}

// Phase returns the phase angle in radians
func (c *ComplexNumber) Phase() float64 {
	return math.Atan2(c.Imag, c.Real)
}

// String returns string representation
func (c *ComplexNumber) String() string {
	if c.Imag >= 0 {
		return fmt.Sprintf("%.2f + %.2fi", c.Real, c.Imag)
	}
	return fmt.Sprintf("%.2f - %.2fi", c.Real, -c.Imag)
}

// ToGoComplex converts to Go's complex128
func (c *ComplexNumber) ToGoComplex() complex128 {
	return complex(c.Real, c.Imag)
}

// Polynomial represents a polynomial
type Polynomial struct {
	coefficients []float64 // coefficients[0] is constant term
}

// NewPolynomial creates a new polynomial
func NewPolynomial(coefficients []float64) *Polynomial {
	// Remove trailing zeros
	for len(coefficients) > 1 && coefficients[len(coefficients)-1] == 0 {
		coefficients = coefficients[:len(coefficients)-1]
	}
	
	return &Polynomial{coefficients: coefficients}
}

// Evaluate evaluates the polynomial at x
func (p *Polynomial) Evaluate(x float64) float64 {
	result := 0.0
	power := 1.0
	
	for _, coeff := range p.coefficients {
		result += coeff * power
		power *= x
	}
	
	return result
}

// Degree returns the degree of the polynomial
func (p *Polynomial) Degree() int {
	return len(p.coefficients) - 1
}

// Add adds two polynomials
func (p *Polynomial) Add(other *Polynomial) *Polynomial {
	maxDegree := max(p.Degree(), other.Degree())
	result := make([]float64, maxDegree+1)
	
	for i := 0; i <= maxDegree; i++ {
		if i < len(p.coefficients) {
			result[i] += p.coefficients[i]
		}
		if i < len(other.coefficients) {
			result[i] += other.coefficients[i]
		}
	}
	
	return NewPolynomial(result)
}

// Multiply multiplies two polynomials
func (p *Polynomial) Multiply(other *Polynomial) *Polynomial {
	result := make([]float64, p.Degree()+other.Degree()+1)
	
	for i, coeff1 := range p.coefficients {
		for j, coeff2 := range other.coefficients {
			result[i+j] += coeff1 * coeff2
		}
	}
	
	return NewPolynomial(result)
}

// Derivative returns the derivative of the polynomial
func (p *Polynomial) Derivative() *Polynomial {
	if p.Degree() == 0 {
		return NewPolynomial([]float64{0})
	}
	
	result := make([]float64, p.Degree())
	for i := 1; i < len(p.coefficients); i++ {
		result[i-1] = float64(i) * p.coefficients[i]
	}
	
	return NewPolynomial(result)
}

// Integral returns the indefinite integral of the polynomial
func (p *Polynomial) Integral() *Polynomial {
	result := make([]float64, p.Degree()+2)
	result[0] = 0 // Constant of integration
	
	for i, coeff := range p.coefficients {
		result[i+1] = coeff / float64(i+1)
	}
	
	return NewPolynomial(result)
}

// String returns string representation
func (p *Polynomial) String() string {
	if len(p.coefficients) == 0 {
		return "0"
	}
	
	var terms []string
	
	for i := len(p.coefficients) - 1; i >= 0; i-- {
		coeff := p.coefficients[i]
		if coeff == 0 {
			continue
		}
		
		var term string
		
		switch i {
		case 0:
			term = fmt.Sprintf("%.2f", coeff)
		case 1:
			if coeff == 1 {
				term = "x"
			} else if coeff == -1 {
				term = "-x"
			} else {
				term = fmt.Sprintf("%.2fx", coeff)
			}
		default:
			if coeff == 1 {
				term = fmt.Sprintf("x^%v", i)
			} else if coeff == -1 {
				term = fmt.Sprintf("-x^%v", i)
			} else {
				term = fmt.Sprintf("%.2fx^%v", coeff, i)
			}
		}
		
		if len(terms) > 0 && coeff > 0 {
			term = " + " + term
		} else if len(terms) > 0 && coeff < 0 {
			term = " - " + term[1:] // Remove minus sign
		}
		
		terms = append(terms, term)
	}
	
	if len(terms) == 0 {
		return "0"
	}
	
	result := strings.Join(terms, "")
	if strings.HasPrefix(result, " + ") {
		result = result[3:]
	}
	
	return result
}

// Numerical methods

// NewtonRaphson finds root using Newton-Raphson method
func (a *Advanced) NewtonRaphson(f func(float64) float64, df func(float64) float64, x0, tolerance float64, maxIterations int) (float64, error) {
	x := x0
	
	for i := 0; i < maxIterations; i++ {
		fx := f(x)
		dfx := df(x)
		
		if dfx == 0 {
			return 0, fmt.Errorf("derivative zero at iteration %d", i)
		}
		
		xNew := x - fx/dfx
		
		if math.Abs(xNew-x) < tolerance {
			return xNew, nil
		}
		
		x = xNew
	}
	
	return 0, fmt.Errorf("maximum iterations reached")
}

// Bisection finds root using bisection method
func (a *Advanced) Bisection(f func(float64) float64, a, b, tolerance float64, maxIterations int) (float64, error) {
	fa := f(a)
	fb := f(b)
	
	if fa*fb > 0 {
		return 0, fmt.Errorf("function must have different signs at endpoints")
	}
	
	for i := 0; i < maxIterations; i++ {
		c := (a + b) / 2
		fc := f(c)
		
		if math.Abs(fc) < tolerance || (b-a)/2 < tolerance {
			return c, nil
		}
		
		if fa*fc < 0 {
			b = c
			fb = fc
		} else {
			a = c
			fa = fc
		}
	}
	
	return (a + b) / 2, nil
}

// NumericalIntegration performs numerical integration using Simpson's rule
func (a *Advanced) NumericalIntegration(f func(float64) float64, a, b float64, n int) (float64, error) {
	if n <= 0 || n%2 != 0 {
		return 0, fmt.Errorf("n must be positive and even: %d", n)
	}
	
	h := (b - a) / float64(n)
	sum := f(a) + f(b)
	
	for i := 1; i < n; i++ {
		x := a + float64(i)*h
		if i%2 == 0 {
			sum += 2 * f(x)
		} else {
			sum += 4 * f(x)
		}
	}
	
	return sum * h / 3, nil
}

// NumericalDifferentiation performs numerical differentiation
func (a *Advanced) NumericalDifferentiation(f func(float64) float64, x, h float64) float64 {
	return (f(x+h) - f(x-h)) / (2 * h)
}

// Helper functions

func max(a, b int) int {
	if a > b {
		return a
	}
	return b
}

// Advanced mathematical functions

// Gamma function approximation (Stirling's formula)
func (a *Advanced) Gamma(x float64) float64 {
	if x < 0.5 {
		// Use reflection formula: Γ(x) = π / (sin(πx) * Γ(1-x))
		return math.Pi / (math.Sin(math.Pi*x) * a.Gamma(1-x))
	}
	
	// Stirling's approximation
	if x > 50 {
		return math.Sqrt(2*math.Pi/x) * math.Pow(x/math.E, x)
	}
	
	// Use Lanczos approximation for better accuracy
	// Simplified version here
	return a.lanczosGamma(x)
}

func (a *Advanced) lanczosGamma(x float64) float64 {
	// Simplified Lanczos approximation
	// In practice, this would use precomputed coefficients
	p := []float64{
		676.5203681218851,
		-1259.1392167224028,
		771.32342877765313,
		-176.61502916214059,
		12.507343278686905,
		-0.13857109526572012,
		9.9843695780195716e-6,
		1.5056327351493116e-7,
	}
	
	g := 7
	if x < 0.5 {
		return math.Pi / (math.Sin(math.Pi*x) * a.lanczosGamma(1-x))
	}
	
	x -= 1
	t := p[0]
	for i := 1; i < len(p); i++ {
		t += p[i] / (x + float64(i))
	}
	
	w := x + float64(g) + 0.5
	sqrt2pi := math.Sqrt(2 * math.Pi)
	
	return sqrt2pi * math.Pow(w, x+0.5) * math.Exp(-w) * t
}

// Beta function
func (a *Advanced) Beta(x, y float64) float64 {
	return a.Gamma(x) * a.Gamma(y) / a.Gamma(x+y)
}

// Error function
func (a *Advanced) Erf(x float64) float64 {
	// Abramowitz and Stegun approximation
	const (
		a1 = 0.254829592
		a2 = -0.284496736
		a3 = 1.421413741
		a4 = -1.453152027
		a5 = 1.061405429
		p  = 0.3275911
	)
	
	sign := 1.0
	if x < 0 {
		sign = -1.0
		x = -x
	}
	
	t := 1.0 / (1.0 + p*x)
	y := 1.0 - (((((a5*t + a4)*t) + a3)*t + a2)*t + a1)*t*math.Exp(-x*x)
	
	return sign * y
}

// Complementary error function
func (a *Advanced) Erfc(x float64) float64 {
	return 1.0 - a.Erf(x)
}
