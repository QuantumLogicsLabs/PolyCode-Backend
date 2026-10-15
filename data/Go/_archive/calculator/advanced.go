package main

import (
	"fmt"
	"math"
	"math/big"
)

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
	if exp == 0 {
		return &BigNumber{value: big.NewFloat(1)}
	}
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
		cofactorDet := cofactor.determinantRecursive()
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

// ComplexNumber represents a complex number
type ComplexNumber struct {
	Real float64
	Imag float64
}

func NewComplex(real, imag float64) *ComplexNumber {
	return &ComplexNumber{Real: real, Imag: imag}
}

func (c *ComplexNumber) Add(other *ComplexNumber) *ComplexNumber {
	return &ComplexNumber{
		Real: c.Real + other.Real,
		Imag: c.Imag + other.Imag,
	}
}

func (c *ComplexNumber) Multiply(other *ComplexNumber) *ComplexNumber {
	real := c.Real*other.Real - c.Imag*other.Imag
	imag := c.Real*other.Imag + c.Imag*other.Real
	return &ComplexNumber{Real: real, Imag: imag}
}

func (c *ComplexNumber) String() string {
	if c.Imag >= 0 {
		return fmt.Sprintf("%.2f + %.2fi", c.Real, c.Imag)
	}
	return fmt.Sprintf("%.2f - %.2fi", c.Real, -c.Imag)
}

// Polynomial represents a polynomial
type Polynomial struct {
	coefficients []float64
}

func NewPolynomial(coefficients []float64) *Polynomial {
	for len(coefficients) > 1 && coefficients[len(coefficients)-1] == 0 {
		coefficients = coefficients[:len(coefficients)-1]
	}
	return &Polynomial{coefficients: coefficients}
}

func (p *Polynomial) Evaluate(x float64) float64 {
	result := 0.0
	power := 1.0
	for _, coeff := range p.coefficients {
		result += coeff * power
		power *= x
	}
	return result
}

func (p *Polynomial) Degree() int {
	if len(p.coefficients) == 0 {
		return 0
	}
	return len(p.coefficients) - 1
}

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

// Numerical Methods
type Advanced struct{}

func NewAdvanced() *Advanced {
	return &Advanced{}
}

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

func (a *Advanced) NumericalIntegration(f func(float64) float64, start, end float64, n int) (float64, error) {
	if n <= 0 || n%2 != 0 {
		return 0, fmt.Errorf("n must be positive and even: %d", n)
	}

	h := (end - start) / float64(n)
	sum := f(start) + f(end)

	for i := 1; i < n; i++ {
		x := start + float64(i)*h
		if i%2 == 0 {
			sum += 2 * f(x)
		} else {
			sum += 4 * f(x)
		}
	}

	return sum * h / 3, nil
}

func main() {
	fmt.Println("=== Advanced Mathematics Demo ===")

	// 1. Big Number Arithmetic
	big1, _ := NewBigNumber("12345678901234567890.12345")
	big2, _ := NewBigNumber("98765432109876543210.98765")
	fmt.Printf("Big Sum: %s\n", big1.Add(big2).String())

	// 2. Matrix Operations
	m1, _ := NewMatrixFromData([][]float64{
		{1, 2},
		{3, 4},
	})
	m2, _ := NewMatrixFromData([][]float64{
		{5, 6},
		{7, 8},
	})
	prod, _ := m1.Multiply(m2)
	det, _ := prod.Determinant()
	fmt.Printf("Matrix Product Determinant: %.2f\n", det)

	// 3. Complex Numbers
	c1 := NewComplex(3, 4)
	c2 := NewComplex(1, 2)
	fmt.Printf("Complex Product: %s\n", c1.Multiply(c2).String())

	// 4. Polynomials
	poly := NewPolynomial([]float64{1, 2, 3}) // 1 + 2x + 3x^2
	fmt.Printf("P(2): %.2f\n", poly.Evaluate(2))
	fmt.Printf("P'(x) Degree: %d\n", poly.Derivative().Degree())

	// 5. Numerical Methods
	adv := NewAdvanced()
	// Find root of f(x) = x^2 - 4 using Newton-Raphson
	f := func(x float64) float64 { return x*x - 4 }
	df := func(x float64) float64 { return 2 * x }
	root, _ := adv.NewtonRaphson(f, df, 1.0, 1e-6, 100)
	fmt.Printf("Newton-Raphson Root of (x^2 - 4): %.4f\n", root)

	// Integrate f(x) = x^2 from 0 to 3 (exact: 9.0)
	integral, _ := adv.NumericalIntegration(func(x float64) float64 { return x * x }, 0, 3, 100)
	fmt.Printf("Numerical Integral of x^2 on [0, 3]: %.4f\n", integral)
}
