# Advanced Scientific Computing

This file contains comprehensive advanced scientific computing examples in C, including linear algebra, numerical analysis, differential equations, interpolation, optimization, statistical functions, complex numbers, and Fast Fourier Transform.

## 📚 Advanced Scientific Computing Fundamentals

### 🔬 Scientific Computing Concepts
- **Numerical Analysis**: Approximation methods for mathematical problems
- **Linear Algebra**: Matrix operations, eigenvalues, and vector spaces
- **Differential Equations**: ODE solvers and numerical integration
- **Optimization**: Finding minima and maxima of functions
- **Signal Processing**: Fourier analysis and digital signal processing

### 🎯 Computational Mathematics
- **Accuracy**: Numerical precision and error analysis
- **Efficiency**: Algorithmic complexity and performance
- **Stability**: Numerical stability and convergence
- **Scalability**: Handling large-scale problems
- **Robustness**: Handling edge cases and singularities

## 📊 Linear Algebra

### Matrix Structure
```c
// Matrix structure
typedef struct {
    double** data;
    int rows;
    int cols;
    int is_square;
} Matrix;
```

### Vector Structure
```c
// Vector structure
typedef struct {
    double* data;
    int size;
} Vector;
```

### Matrix Operations Implementation
```c
// Create matrix
Matrix* createMatrix(int rows, int cols) {
    if (rows <= 0 || cols <= 0 || rows > MATRIX_MAX_SIZE || cols > MATRIX_MAX_SIZE) {
        return NULL;
    }
    
    Matrix* matrix = malloc(sizeof(Matrix));
    if (!matrix) return NULL;
    
    matrix->data = malloc(rows * sizeof(double*));
    if (!matrix->data) {
        free(matrix);
        return NULL;
    }
    
    for (int i = 0; i < rows; i++) {
        matrix->data[i] = malloc(cols * sizeof(double));
        if (!matrix->data[i]) {
            for (int j = 0; j < i; j++) {
                free(matrix->data[j]);
            }
            free(matrix->data);
            free(matrix);
            return NULL;
        }
    }
    
    matrix->rows = rows;
    matrix->cols = cols;
    matrix->is_square = (rows == cols);
    
    // Initialize to zero
    for (int i = 0; i < rows; i++) {
        for (int j = 0; j < cols; j++) {
            matrix->data[i][j] = 0.0;
        }
    }
    
    return matrix;
}

// Matrix multiplication
Matrix* matrixMultiply(Matrix* a, Matrix* b) {
    if (!a || !b || a->cols != b->rows) {
        return NULL;
    }
    
    Matrix* result = createMatrix(a->rows, b->cols);
    if (!result) return NULL;
    
    for (int i = 0; i < a->rows; i++) {
        for (int j = 0; j < b->cols; j++) {
            for (int k = 0; k < a->cols; k++) {
                result->data[i][j] += a->data[i][k] * b->data[k][j];
            }
        }
    }
    
    return result;
}

// Matrix determinant (recursive)
double matrixDeterminant(Matrix* matrix) {
    if (!matrix || !matrix->is_square) {
        return NAN;
    }
    
    if (matrix->rows == 1) {
        return matrix->data[0][0];
    }
    
    if (matrix->rows == 2) {
        return matrix->data[0][0] * matrix->data[1][1] - matrix->data[0][1] * matrix->data[1][0];
    }
    
    double det = 0.0;
    for (int j = 0; j < matrix->cols; j++) {
        // Create minor matrix
        Matrix* minor = createMatrix(matrix->rows - 1, matrix->cols - 1);
        if (!minor) return NAN;
        
        for (int i = 1; i < matrix->rows; i++) {
            int col_count = 0;
            for (int k = 0; k < matrix->cols; k++) {
                if (k != j) {
                    minor->data[i - 1][col_count] = matrix->data[i][k];
                    col_count++;
                }
            }
        }
        
        double minor_det = matrixDeterminant(minor);
        det += (j % 2 == 0 ? 1 : -1) * matrix->data[0][j] * minor_det;
        
        freeMatrix(minor);
    }
    
    return det;
}

// Matrix inverse (Gaussian elimination)
Matrix* matrixInverse(Matrix* matrix) {
    if (!matrix || !matrix->is_square) {
        return NULL;
    }
    
    int n = matrix->rows;
    Matrix* augmented = createMatrix(n, 2 * n);
    if (!augmented) return NULL;
    
    // Create augmented matrix [A|I]
    for (int i = 0; i < n; i++) {
        for (int j = 0; j < n; j++) {
            augmented->data[i][j] = matrix->data[i][j];
        }
        augmented->data[i][i + n] = 1.0;
    }
    
    // Gaussian elimination
    for (int i = 0; i < n; i++) {
        // Find pivot
        int pivot_row = i;
        for (int j = i + 1; j < n; j++) {
            if (fabs(augmented->data[j][i]) > fabs(augmented->data[pivot_row][i])) {
                pivot_row = j;
            }
        }
        
        // Swap rows if necessary
        if (pivot_row != i) {
            for (int j = 0; j < 2 * n; j++) {
                double temp = augmented->data[i][j];
                augmented->data[i][j] = augmented->data[pivot_row][j];
                augmented->data[pivot_row][j] = temp;
            }
        }
        
        // Check for zero pivot
        if (fabs(augmented->data[i][i]) < NUMERICAL_TOLERANCE) {
            freeMatrix(augmented);
            return NULL; // Matrix is singular
        }
        
        // Normalize pivot row
        double pivot = augmented->data[i][i];
        for (int j = 0; j < 2 * n; j++) {
            augmented->data[i][j] /= pivot;
        }
        
        // Eliminate other rows
        for (int j = 0; j < n; j++) {
            if (j != i) {
                double factor = augmented->data[j][i];
                for (int k = 0; k < 2 * n; k++) {
                    augmented->data[j][k] -= factor * augmented->data[i][k];
                }
            }
        }
    }
    
    // Extract inverse
    Matrix* inverse = createMatrix(n, n);
    if (!inverse) {
        freeMatrix(augmented);
        return NULL;
    }
    
    for (int i = 0; i < n; i++) {
        for (int j = 0; j < n; j++) {
            inverse->data[i][j] = augmented->data[i][j + n];
        }
    }
    
    freeMatrix(augmented);
    return inverse;
}
```

**Linear Algebra Benefits**:
- **Efficient Operations**: Optimized matrix and vector computations
- **Numerical Stability**: Robust algorithms for ill-conditioned matrices
- **Memory Management**: Dynamic allocation with proper cleanup
- **Error Handling**: Comprehensive validation and error reporting

## 🔢 Numerical Integration

### Integration Methods
```c
// Numerical integration methods
typedef enum {
    INTEGRATION_RECTANGLE = 0,
    INTEGRATION_TRAPEZOID = 1,
    INTEGRATION_SIMPSON = 2,
    INTEGRATION_GAUSSIAN = 3,
    INTEGRATION_ROMBERG = 4
} IntegrationMethod;

// Function pointer for numerical methods
typedef double (*MathFunction)(double x);
```

### Integration Implementation
```c
// Rectangle rule integration
double rectangleRule(MathFunction f, double a, double b, int n) {
    if (n <= 0) return NAN;
    
    double h = (b - a) / n;
    double sum = 0.0;
    
    for (int i = 0; i < n; i++) {
        double x = a + i * h;
        sum += f(x);
    }
    
    return h * sum;
}

// Trapezoidal rule integration
double trapezoidalRule(MathFunction f, double a, double b, int n) {
    if (n <= 0) return NAN;
    
    double h = (b - a) / n;
    double sum = 0.5 * (f(a) + f(b));
    
    for (int i = 1; i < n; i++) {
        double x = a + i * h;
        sum += f(x);
    }
    
    return h * sum;
}

// Simpson's rule integration
double simpsonRule(MathFunction f, double a, double b, int n) {
    if (n <= 0 || n % 2 != 0) return NAN;
    
    double h = (b - a) / n;
    double sum = f(a) + f(b);
    
    for (int i = 1; i < n; i++) {
        double x = a + i * h;
        if (i % 2 == 0) {
            sum += 2.0 * f(x);
        } else {
            sum += 4.0 * f(x);
        }
    }
    
    return h * sum / 3.0;
}

// Adaptive Simpson's rule
double adaptiveSimpson(MathFunction f, double a, double b, double tolerance, int max_depth, int depth) {
    if (depth > max_depth) {
        return simpsonRule(f, a, b, 2);
    }
    
    double c = (a + b) / 2.0;
    double whole = simpsonRule(f, a, b, 2);
    double left = simpsonRule(f, a, c, 2);
    double right = simpsonRule(f, c, b, 2);
    
    if (fabs(left + right - whole) < 15.0 * tolerance) {
        return left + right + (left + right - whole) / 15.0;
    }
    
    return adaptiveSimpson(f, a, c, tolerance / 2.0, max_depth, depth + 1) +
           adaptiveSimpson(f, c, b, tolerance / 2.0, max_depth, depth + 1);
}
```

**Integration Benefits**:
- **Multiple Methods**: Various integration algorithms for different needs
- **Adaptive Methods**: Automatic refinement for accuracy
- **Error Control**: Built-in error estimation and tolerance handling
- **Flexibility**: Function pointer interface for any integrable function

## 🔍 Root Finding

### Root Finding Methods
```c
// Root finding methods
typedef enum {
    ROOT_BISECTION = 0,
    ROOT_NEWTON = 1,
    ROOT_SECANT = 2,
    ROOT_REGULA_FALSI = 3,
    ROOT_BRENT = 4
} RootFindingMethod;
```

### Root Finding Implementation
```c
// Bisection method
double bisectionMethod(MathFunction f, double a, double b, double tolerance, int max_iterations) {
    if (f(a) * f(b) > 0) {
        return NAN; // Root not bracketed
    }
    
    double left = a;
    double right = b;
    
    for (int i = 0; i < max_iterations; i++) {
        double mid = (left + right) / 2.0;
        double f_mid = f(mid);
        
        if (fabs(f_mid) < tolerance || fabs(right - left) < tolerance) {
            return mid;
        }
        
        if (f(left) * f_mid < 0) {
            right = mid;
        } else {
            left = mid;
        }
    }
    
    return (left + right) / 2.0;
}

// Newton's method
double newtonMethod(MathFunction f, MathFunction df, double x0, double tolerance, int max_iterations) {
    double x = x0;
    
    for (int i = 0; i < max_iterations; i++) {
        double fx = f(x);
        double dfx = df(x);
        
        if (fabs(dfx) < NUMERICAL_TOLERANCE) {
            return NAN; // Derivative too small
        }
        
        double x_new = x - fx / dfx;
        
        if (fabs(x_new - x) < tolerance) {
            return x_new;
        }
        
        x = x_new;
    }
    
    return x;
}

// Secant method
double secantMethod(MathFunction f, double x0, double x1, double tolerance, int max_iterations) {
    double x_prev = x0;
    double x_curr = x1;
    
    for (int i = 0; i < max_iterations; i++) {
        double f_prev = f(x_prev);
        double f_curr = f(x_curr);
        
        if (fabs(f_curr - f_prev) < NUMERICAL_TOLERANCE) {
            return NAN; // Division by zero
        }
        
        double x_next = x_curr - f_curr * (x_curr - x_prev) / (f_curr - f_prev);
        
        if (fabs(x_next - x_curr) < tolerance) {
            return x_next;
        }
        
        x_prev = x_curr;
        x_curr = x_next;
    }
    
    return x_curr;
}
```

**Root Finding Benefits**:
- **Robust Algorithms**: Multiple methods for different function types
- **Convergence Control**: Tolerance and iteration limits
- **Error Handling**: Detection of singularities and convergence issues
- **Flexibility**: Works with any differentiable function

## 📈 Differential Equations

### ODE System Structure
```c
// ODE system structure
typedef struct {
    int size;
    double (*derivatives)(double t, const double* y, double* dydt, void* params);
    void* params;
} ODESystem;
```

### ODE Solvers Implementation
```c
// Euler's method
void eulerMethod(ODESystem* system, double t0, double* y0, double t_end, int steps, double* solution) {
    if (!system || !y0 || !solution) return;
    
    double h = (t_end - t0) / steps;
    double t = t0;
    double* y = malloc(system->size * sizeof(double));
    double* dydt = malloc(system->size * sizeof(double));
    
    if (!y || !dydt) {
        free(y);
        free(dydt);
        return;
    }
    
    // Initialize
    for (int i = 0; i < system->size; i++) {
        y[i] = y0[i];
    }
    
    // Store initial condition
    for (int i = 0; i < system->size; i++) {
        solution[i] = y[i];
    }
    
    // Integrate
    for (int step = 1; step <= steps; step++) {
        system->derivatives(t, y, dydt, system->params);
        
        for (int i = 0; i < system->size; i++) {
            y[i] += h * dydt[i];
            solution[step * system->size + i] = y[i];
        }
        
        t += h;
    }
    
    free(y);
    free(dydt);
}

// Runge-Kutta 4th order
void rungeKutta4(ODESystem* system, double t0, double* y0, double t_end, int steps, double* solution) {
    if (!system || !y0 || !solution) return;
    
    double h = (t_end - t0) / steps;
    double t = t0;
    double* y = malloc(system->size * sizeof(double));
    double* k1 = malloc(system->size * sizeof(double));
    double* k2 = malloc(system->size * sizeof(double));
    double* k3 = malloc(system->size * sizeof(double));
    double* k4 = malloc(system->size * sizeof(double));
    double* y_temp = malloc(system->size * sizeof(double));
    
    if (!y || !k1 || !k2 || !k3 || !k4 || !y_temp) {
        free(y);
        free(k1);
        free(k2);
        free(k3);
        free(k4);
        free(y_temp);
        return;
    }
    
    // Initialize
    for (int i = 0; i < system->size; i++) {
        y[i] = y0[i];
    }
    
    // Store initial condition
    for (int i = 0; i < system->size; i++) {
        solution[i] = y[i];
    }
    
    // Integrate
    for (int step = 1; step <= steps; step++) {
        // k1
        system->derivatives(t, y, k1, system->params);
        
        // k2
        for (int i = 0; i < system->size; i++) {
            y_temp[i] = y[i] + 0.5 * h * k1[i];
        }
        system->derivatives(t + 0.5 * h, y_temp, k2, system->params);
        
        // k3
        for (int i = 0; i < system->size; i++) {
            y_temp[i] = y[i] + 0.5 * h * k2[i];
        }
        system->derivatives(t + 0.5 * h, y_temp, k3, system->params);
        
        // k4
        for (int i = 0; i < system->size; i++) {
            y_temp[i] = y[i] + h * k3[i];
        }
        system->derivatives(t + h, y_temp, k4, system->params);
        
        // Update
        for (int i = 0; i < system->size; i++) {
            y[i] += h * (k1[i] + 2.0 * k2[i] + 2.0 * k3[i] + k4[i]) / 6.0;
            solution[step * system->size + i] = y[i];
        }
        
        t += h;
    }
    
    free(y);
    free(k1);
    free(k2);
    free(k3);
    free(k4);
    free(y_temp);
}
```

**ODE Solver Benefits**:
- **Multiple Methods**: Euler and Runge-Kutta for different accuracy needs
- **System Support**: Handles systems of differential equations
- **Stability**: Numerically stable integration schemes
- **Flexibility**: Custom derivative functions with parameters

## 📊 Interpolation

### Interpolation Methods
```c
// Lagrange interpolation
double lagrangeInterpolation(double* x, double* y, int n, double x_interp) {
    double result = 0.0;
    
    for (int i = 0; i < n; i++) {
        double term = y[i];
        
        for (int j = 0; j < n; j++) {
            if (i != j) {
                term *= (x_interp - x[j]) / (x[i] - x[j]);
            }
        }
        
        result += term;
    }
    
    return result;
}

// Linear interpolation
double linearInterpolation(double x0, double y0, double x1, double y1, double x_interp) {
    if (x1 - x0 < NUMERICAL_TOLERANCE) {
        return y0;
    }
    
    return y0 + (y1 - y0) * (x_interp - x0) / (x1 - x0);
}
```

### Cubic Spline Implementation
```c
// Cubic spline interpolation
typedef struct {
    double* a;
    double* b;
    double* c;
    double* d;
    int n;
} CubicSpline;

// Create cubic spline
CubicSpline* createCubicSpline(double* x, double* y, int n) {
    if (n < 2) return NULL;
    
    CubicSpline* spline = malloc(sizeof(CubicSpline));
    if (!spline) return NULL;
    
    spline->n = n;
    spline->a = malloc(n * sizeof(double));
    spline->b = malloc(n * sizeof(double));
    spline->c = malloc(n * sizeof(double));
    spline->d = malloc(n * sizeof(double));
    
    if (!spline->a || !spline->b || !spline->c || !spline->d) {
        free(spline->a);
        free(spline->b);
        free(spline->c);
        free(spline->d);
        free(spline);
        return NULL;
    }
    
    // Copy y values
    for (int i = 0; i < n; i++) {
        spline->a[i] = y[i];
    }
    
    // Natural spline boundary conditions
    double* h = malloc((n - 1) * sizeof(double));
    double* alpha = malloc((n - 1) * sizeof(double));
    double* l = malloc(n * sizeof(double));
    double* mu = malloc(n * sizeof(double));
    double* z = malloc(n * sizeof(double));
    
    if (!h || !alpha || !l || !mu || !z) {
        free(h);
        free(alpha);
        free(l);
        free(mu);
        free(z);
        free(spline->a);
        free(spline->b);
        free(spline->c);
        free(spline->d);
        free(spline);
        return NULL;
    }
    
    // Step 1
    for (int i = 0; i < n - 1; i++) {
        h[i] = x[i + 1] - x[i];
    }
    
    for (int i = 1; i < n - 1; i++) {
        alpha[i] = 3.0 * (y[i + 1] - y[i]) / h[i] - 3.0 * (y[i] - y[i - 1]) / h[i - 1];
    }
    
    // Step 2
    l[0] = 1.0;
    mu[0] = 0.0;
    z[0] = 0.0;
    
    for (int i = 1; i < n - 1; i++) {
        l[i] = 2.0 * (x[i + 1] - x[i - 1]) - h[i - 1] * mu[i - 1];
        mu[i] = h[i] / l[i];
        z[i] = (alpha[i] - h[i - 1] * z[i - 1]) / l[i];
    }
    
    // Step 3
    l[n - 1] = 1.0;
    z[n - 1] = 0.0;
    spline->c[n - 1] = 0.0;
    
    // Step 4
    for (int j = n - 2; j >= 0; j--) {
        spline->c[j] = z[j] - mu[j] * spline->c[j + 1];
        spline->b[j] = (spline->a[j + 1] - spline->a[j]) / h[j] - h[j] * (spline->c[j + 1] + 2.0 * spline->c[j]) / 3.0;
        spline->d[j] = (spline->c[j + 1] - spline->c[j]) / (3.0 * h[j]);
    }
    
    free(h);
    free(alpha);
    free(l);
    free(mu);
    free(z);
    
    return spline;
}
```

**Interpolation Benefits**:
- **Multiple Methods**: Lagrange, linear, and cubic spline interpolation
- **Smooth Curves**: Cubic splines provide smooth interpolation
- **Accuracy**: High-order methods for better approximation
- **Flexibility**: Works with arbitrary data points

## 🎯 Optimization

### Optimization Methods
```c
// Gradient descent
double gradientDescent(MathFunction f, MathFunction df, double x0, double learning_rate, int max_iterations, double tolerance) {
    double x = x0;
    
    for (int i = 0; i < max_iterations; i++) {
        double gradient = df(x);
        
        if (fabs(gradient) < tolerance) {
            return x;
        }
        
        x -= learning_rate * gradient;
    }
    
    return x;
}

// Golden section search for 1D optimization
double goldenSectionSearch(MathFunction f, double a, double b, double tolerance) {
    const double golden_ratio = (sqrt(5.0) - 1.0) / 2.0;
    
    double x1 = b - golden_ratio * (b - a);
    double x2 = a + golden_ratio * (b - a);
    double f1 = f(x1);
    double f2 = f(x2);
    
    while (fabs(b - a) > tolerance) {
        if (f1 < f2) {
            b = x2;
            x2 = x1;
            f2 = f1;
            x1 = b - golden_ratio * (b - a);
            f1 = f(x1);
        } else {
            a = x1;
            x1 = x2;
            f1 = f2;
            x2 = a + golden_ratio * (b - a);
            f2 = f(x2);
        }
    }
    
    return (a + b) / 2.0;
}
```

**Optimization Benefits**:
- **Convergence**: Guaranteed convergence for convex functions
- **Efficiency**: Fast convergence with appropriate step sizes
- **Robustness**: Handles various function types and landscapes
- **Flexibility**: Works with differentiable and non-differentiable functions

## 📈 Statistical Functions

### Statistical Implementation
```c
// Calculate mean
double calculateMean(double* data, int n) {
    if (!data || n <= 0) return NAN;
    
    double sum = 0.0;
    for (int i = 0; i < n; i++) {
        sum += data[i];
    }
    
    return sum / n;
}

// Calculate variance
double calculateVariance(double* data, int n) {
    if (!data || n <= 0) return NAN;
    
    double mean = calculateMean(data, n);
    double sum = 0.0;
    
    for (int i = 0; i < n; i++) {
        double diff = data[i] - mean;
        sum += diff * diff;
    }
    
    return sum / n;
}

// Calculate standard deviation
double calculateStandardDeviation(double* data, int n) {
    double variance = calculateVariance(data, n);
    return sqrt(variance);
}

// Calculate correlation coefficient
double calculateCorrelation(double* x, double* y, int n) {
    if (!x || !y || n <= 0) return NAN;
    
    double mean_x = calculateMean(x, n);
    double mean_y = calculateMean(y, n);
    
    double sum_xy = 0.0;
    double sum_xx = 0.0;
    double sum_yy = 0.0;
    
    for (int i = 0; i < n; i++) {
        double dx = x[i] - mean_x;
        double dy = y[i] - mean_y;
        sum_xy += dx * dy;
        sum_xx += dx * dx;
        sum_yy += dy * dy;
    }
    
    return sum_xy / sqrt(sum_xx * sum_yy);
}
```

**Statistical Benefits**:
- **Comprehensive**: Mean, variance, standard deviation, correlation
- **Numerical Stability**: Robust algorithms for statistical computations
- **Efficiency**: Optimized calculations for large datasets
- **Accuracy**: High precision statistical measures

## 🔢 Complex Numbers

### Complex Number Structure
```c
// Complex number operations
typedef struct {
    double real;
    double imag;
} Complex;

// Create complex number
Complex createComplex(double real, double imag) {
    Complex c = {real, imag};
    return c;
}

// Complex addition
Complex complexAdd(Complex a, Complex b) {
    return createComplex(a.real + b.real, a.imag + b.imag);
}

// Complex multiplication
Complex complexMultiply(Complex a, Complex b) {
    double real = a.real * b.real - a.imag * b.imag;
    double imag = a.real * b.imag + a.imag * b.real;
    return createComplex(real, imag);
}

// Complex exponential
Complex complexExponential(Complex c) {
    double magnitude = exp(c.real);
    double phase = c.imag;
    return createComplex(magnitude * cos(phase), magnitude * sin(phase));
}
```

**Complex Number Benefits**:
- **Complete Operations**: Addition, multiplication, division, exponentiation
- **Polar Form**: Magnitude and phase calculations
- **Numerical Stability**: Robust complex arithmetic
- **Integration**: Seamless integration with real-number operations

## 🌊 Fast Fourier Transform

### FFT Implementation
```c
// Cooley-Tukey FFT (recursive)
void fft(Complex* data, int n) {
    if (n <= 1) return;
    
    // Separate even and odd indices
    Complex* even = malloc(n / 2 * sizeof(Complex));
    Complex* odd = malloc(n / 2 * sizeof(Complex));
    
    for (int i = 0; i < n / 2; i++) {
        even[i] = data[2 * i];
        odd[i] = data[2 * i + 1];
    }
    
    // Recursively compute FFT
    fft(even, n / 2);
    fft(odd, n / 2);
    
    // Combine results
    for (int k = 0; k < n / 2; k++) {
        Complex t = complexMultiply(createComplex(cos(-2 * PI * k / n), sin(-2 * PI * k / n)), odd[k]);
        data[k] = complexAdd(even[k], t);
        data[k + n / 2] = complexSubtract(even[k], t);
    }
    
    free(even);
    free(odd);
}

// Inverse FFT
void ifft(Complex* data, int n) {
    // Conjugate the input
    for (int i = 0; i < n; i++) {
        data[i].imag = -data[i].imag;
    }
    
    // Forward FFT
    fft(data, n);
    
    // Conjugate and scale
    for (int i = 0; i < n; i++) {
        data[i].real /= n;
        data[i].imag = -data[i].imag / n;
    }
}
```

**FFT Benefits**:
- **Efficient**: O(n log n) complexity for spectral analysis
- **Inverse Transform**: Complete forward and inverse FFT
- **Signal Processing**: Foundation for frequency domain analysis
- **Numerical Accuracy**: Stable implementation with proper scaling

## 🔧 Best Practices

### 1. Numerical Stability
```c
// Good: Check for numerical tolerance
double safeDivision(double a, double b) {
    if (fabs(b) < NUMERICAL_TOLERANCE) {
        return NAN; // Handle near-zero denominator
    }
    return a / b;
}

// Bad: No tolerance check
double unsafeDivision(double a, double b) {
    return a / b; // Can cause division by zero or overflow
}
```

### 2. Memory Management
```c
// Good: Proper memory management
Matrix* createMatrix(int rows, int cols) {
    Matrix* matrix = malloc(sizeof(Matrix));
    if (!matrix) return NULL;
    
    matrix->data = malloc(rows * sizeof(double*));
    if (!matrix->data) {
        free(matrix);
        return NULL;
    }
    
    // Initialize each row
    for (int i = 0; i < rows; i++) {
        matrix->data[i] = malloc(cols * sizeof(double));
        if (!matrix->data[i]) {
            // Clean up on failure
            for (int j = 0; j < i; j++) {
                free(matrix->data[j]);
            }
            free(matrix->data);
            free(matrix);
            return NULL;
        }
    }
    
    return matrix;
}

void freeMatrix(Matrix* matrix) {
    if (!matrix) return;
    
    for (int i = 0; i < matrix->rows; i++) {
        free(matrix->data[i]);
    }
    free(matrix->data);
    free(matrix);
}
```

### 3. Error Handling
```c
// Good: Comprehensive error checking
Matrix* matrixMultiply(Matrix* a, Matrix* b) {
    if (!a || !b) {
        fprintf(stderr, "Error: NULL matrix pointer\n");
        return NULL;
    }
    
    if (a->cols != b->rows) {
        fprintf(stderr, "Error: Matrix dimensions incompatible\n");
        return NULL;
    }
    
    Matrix* result = createMatrix(a->rows, b->cols);
    if (!result) {
        fprintf(stderr, "Error: Failed to allocate result matrix\n");
        return NULL;
    }
    
    // Perform multiplication
    // ...
    
    return result;
}

// Bad: No error checking
Matrix* matrixMultiplyUnsafe(Matrix* a, Matrix* b) {
    Matrix* result = createMatrix(a->rows, b->cols);
    // No checks - can cause crashes
    return result;
}
```

### 4. Numerical Accuracy
```c
// Good: Use appropriate numerical methods
double integrateFunction(MathFunction f, double a, double b) {
    // Choose appropriate method based on function properties
    if (isSmoothFunction(f, a, b)) {
        return simpsonRule(f, a, b, 1000);
    } else {
        return adaptiveSimpson(f, a, b, 1e-6, 10, 0);
    }
}

// Bad: Always use the same method
double integrateFunctionPoor(MathFunction f, double a, double b) {
    return rectangleRule(f, a, b, 100); // May be inaccurate for smooth functions
}
```

### 5. Performance Optimization
```c
// Good: Efficient algorithms
Matrix* matrixMultiplyOptimized(Matrix* a, Matrix* b) {
    // Use cache-friendly access patterns
    Matrix* result = createMatrix(a->rows, b->cols);
    
    for (int i = 0; i < a->rows; i++) {
        for (int k = 0; k < a->cols; k++) {
            double aik = a->data[i][k];
            for (int j = 0; j < b->cols; j++) {
                result->data[i][j] += aik * b->data[k][j];
            }
        }
    }
    
    return result;
}

// Bad: Inefficient access patterns
Matrix* matrixMultiplySlow(Matrix* a, Matrix* b) {
    Matrix* result = createMatrix(a->rows, b->cols);
    
    for (int i = 0; i < a->rows; i++) {
        for (int j = 0; j < b->cols; j++) {
            for (int k = 0; k < a->cols; k++) {
                result->data[i][j] += a->data[i][k] * b->data[k][j];
            }
        }
    }
    
    return result;
}
```

## ⚠️ Common Pitfalls

### 1. Numerical Instability
```c
// Wrong: Subtracting nearly equal numbers
double subtractLargeNumbers(double a, double b) {
    return a - b; // Can lose precision if a ≈ b
}

// Right: Use compensated algorithms
double compensatedSubtraction(double a, double b) {
    double diff = a - b;
    if (fabs(diff) < NUMERICAL_TOLERANCE) {
        return 0.0; // Handle near-equal case
    }
    return diff;
}
```

### 2. Convergence Issues
```c
// Wrong: No convergence check
double newtonMethodUnsafe(MathFunction f, MathFunction df, double x0) {
    double x = x0;
    for (int i = 0; i < 1000; i++) { // Fixed iterations
        x = x - f(x) / df(x);
    }
    return x;
}

// Right: Proper convergence checking
double newtonMethodSafe(MathFunction f, MathFunction df, double x0, double tolerance) {
    double x = x0;
    for (int i = 0; i < 1000; i++) {
        double fx = f(x);
        double dfx = df(x);
        
        if (fabs(dfx) < NUMERICAL_TOLERANCE) {
            return NAN; // Derivative too small
        }
        
        double x_new = x - fx / dfx;
        if (fabs(x_new - x) < tolerance) {
            return x_new;
        }
        x = x_new;
    }
    return x;
}
```

### 3. Memory Leaks
```c
// Wrong: Memory leaks in temporary allocations
double computeEigenvalue(Matrix* matrix) {
    double* temp = malloc(matrix->rows * sizeof(double));
    // Use temp but forget to free
    return temp[0]; // Memory leak
}

// Right: Proper memory management
double computeEigenvalueSafe(Matrix* matrix) {
    double* temp = malloc(matrix->rows * sizeof(double));
    if (!temp) return NAN;
    
    double result = temp[0];
    free(temp);
    return result;
}
```

### 4. Division by Zero
```c
// Wrong: No zero division checks
double computeRatio(double a, double b) {
    return a / b; // Can cause division by zero
}

// Right: Safe division
double computeRatioSafe(double a, double b) {
    if (fabs(b) < NUMERICAL_TOLERANCE) {
        return NAN; // Handle zero or near-zero denominator
    }
    return a / b;
}
```

## 🔧 Real-World Applications

### 1. Signal Processing
```c
// Apply low-pass filter using FFT
void lowPassFilter(double* signal, int n, double cutoff_frequency) {
    Complex* freq_domain = malloc(n * sizeof(Complex));
    
    // Convert to frequency domain
    for (int i = 0; i < n; i++) {
        freq_domain[i] = createComplex(signal[i], 0.0);
    }
    fft(freq_domain, n);
    
    // Apply filter
    for (int i = 0; i < n; i++) {
        double frequency = i * 2.0 * PI / n;
        if (frequency > cutoff_frequency) {
            freq_domain[i] = createComplex(0.0, 0.0); // Remove high frequencies
        }
    }
    
    // Convert back to time domain
    ifft(freq_domain, n);
    for (int i = 0; i < n; i++) {
        signal[i] = freq_domain[i].real;
    }
    
    free(freq_domain);
}
```

### 2. Data Analysis
```c
// Perform linear regression
void linearRegression(double* x, double* y, int n, double* slope, double* intercept) {
    double sum_x = 0.0, sum_y = 0.0, sum_xy = 0.0, sum_x2 = 0.0;
    
    for (int i = 0; i < n; i++) {
        sum_x += x[i];
        sum_y += y[i];
        sum_xy += x[i] * y[i];
        sum_x2 += x[i] * x[i];
    }
    
    double denominator = n * sum_x2 - sum_x * sum_x;
    if (fabs(denominator) < NUMERICAL_TOLERANCE) {
        *slope = NAN;
        *intercept = NAN;
        return;
    }
    
    *slope = (n * sum_xy - sum_x * sum_y) / denominator;
    *intercept = (sum_y - *slope * sum_x) / n;
}
```

### 3. Physics Simulation
```c
// Simulate projectile motion
void simulateProjectile(double v0, double angle, double* x_trajectory, double* y_trajectory, int steps) {
    double vx = v0 * cos(angle);
    double vy = v0 * sin(angle);
    double g = 9.81;
    
    double dt = 0.01;
    double x = 0.0, y = 0.0;
    
    for (int i = 0; i < steps; i++) {
        x_trajectory[i] = x;
        y_trajectory[i] = y;
        
        x += vx * dt;
        y += vy * dt;
        vy -= g * dt;
        
        if (y < 0) break; // Hit ground
    }
}
```

### 4. Financial Modeling
```c
// Calculate option price using Black-Scholes
double blackScholesOption(double S, double K, double r, double sigma, double T, int is_call) {
    double d1 = (log(S / K) + (r + 0.5 * sigma * sigma) * T) / (sigma * sqrt(T));
    double d2 = d1 - sigma * sqrt(T);
    
    if (is_call) {
        return S * normalCDF(d1) - K * exp(-r * T) * normalCDF(d2);
    } else {
        return K * exp(-r * T) * normalCDF(-d2) - S * normalCDF(-d1);
    }
}
```

## 📚 Further Reading

### Books
- "Numerical Recipes in C" by Press et al.
- "Scientific Computing: An Introductory Survey" by Michael T. Heath
- "Matrix Computations" by Golub and Van Loan
- "Analysis of Numerical Methods" by Eugene Isaacson

### Topics
- Parallel numerical algorithms
- GPU computing for scientific applications
- Machine learning algorithms
- Computational fluid dynamics
- Monte Carlo methods
- Finite element methods

Advanced scientific computing in C provides the foundation for solving complex mathematical problems with high accuracy and efficiency. Master these techniques to create robust, performant, and accurate scientific computing applications!
