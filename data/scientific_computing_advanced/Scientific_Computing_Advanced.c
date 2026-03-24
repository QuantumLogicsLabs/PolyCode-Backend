#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <math.h>
#include <time.h>
#include <complex.h>

// =============================================================================
// ADVANCED SCIENTIFIC COMPUTING
// =============================================================================

#define MATRIX_MAX_SIZE 1000
#define VECTOR_MAX_SIZE 1000
#define NUMERICAL_TOLERANCE 1e-10
#define MAX_ITERATIONS 10000
#define PI 3.14159265358979323846

// =============================================================================
// LINEAR ALGEBRA
// =============================================================================

// Matrix structure
typedef struct {
    double** data;
    int rows;
    int cols;
    int is_square;
} Matrix;

// Vector structure
typedef struct {
    double* data;
    int size;
} Vector;

// Eigenvalue structure
typedef struct {
    double* eigenvalues;
    double** eigenvectors;
    int size;
} EigenDecomposition;

// =============================================================================
// NUMERICAL ANALYSIS
// =============================================================================

// Function pointer for numerical methods
typedef double (*MathFunction)(double x);

// Numerical integration methods
typedef enum {
    INTEGRATION_RECTANGLE = 0,
    INTEGRATION_TRAPEZOID = 1,
    INTEGRATION_SIMPSON = 2,
    INTEGRATION_GAUSSIAN = 3,
    INTEGRATION_ROMBERG = 4
} IntegrationMethod;

// Root finding methods
typedef enum {
    ROOT_BISECTION = 0,
    ROOT_NEWTON = 1,
    ROOT_SECANT = 2,
    ROOT_REGULA_FALSI = 3,
    ROOT_BRENT = 4
} RootFindingMethod;

// =============================================================================
// MATRIX OPERATIONS
// =============================================================================

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

// Create identity matrix
Matrix* createIdentityMatrix(int size) {
    Matrix* matrix = createMatrix(size, size);
    if (!matrix) return NULL;
    
    for (int i = 0; i < size; i++) {
        matrix->data[i][i] = 1.0;
    }
    
    return matrix;
}

// Free matrix
void freeMatrix(Matrix* matrix) {
    if (!matrix) return;
    
    for (int i = 0; i < matrix->rows; i++) {
        free(matrix->data[i]);
    }
    free(matrix->data);
    free(matrix);
}

// Create vector
Vector* createVector(int size) {
    if (size <= 0 || size > VECTOR_MAX_SIZE) {
        return NULL;
    }
    
    Vector* vector = malloc(sizeof(Vector));
    if (!vector) return NULL;
    
    vector->data = malloc(size * sizeof(double));
    if (!vector->data) {
        free(vector);
        return NULL;
    }
    
    vector->size = size;
    
    // Initialize to zero
    for (int i = 0; i < size; i++) {
        vector->data[i] = 0.0;
    }
    
    return vector;
}

// Free vector
void freeVector(Vector* vector) {
    if (!vector) return;
    
    free(vector->data);
    free(vector);
}

// Matrix addition
Matrix* matrixAdd(Matrix* a, Matrix* b) {
    if (!a || !b || a->rows != b->rows || a->cols != b->cols) {
        return NULL;
    }
    
    Matrix* result = createMatrix(a->rows, a->cols);
    if (!result) return NULL;
    
    for (int i = 0; i < a->rows; i++) {
        for (int j = 0; j < a->cols; j++) {
            result->data[i][j] = a->data[i][j] + b->data[i][j];
        }
    }
    
    return result;
}

// Matrix subtraction
Matrix* matrixSubtract(Matrix* a, Matrix* b) {
    if (!a || !b || a->rows != b->rows || a->cols != b->cols) {
        return NULL;
    }
    
    Matrix* result = createMatrix(a->rows, a->cols);
    if (!result) return NULL;
    
    for (int i = 0; i < a->rows; i++) {
        for (int j = 0; j < a->cols; j++) {
            result->data[i][j] = a->data[i][j] - b->data[i][j];
        }
    }
    
    return result;
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

// Matrix transpose
Matrix* matrixTranspose(Matrix* matrix) {
    if (!matrix) return NULL;
    
    Matrix* result = createMatrix(matrix->cols, matrix->rows);
    if (!result) return NULL;
    
    for (int i = 0; i < matrix->rows; i++) {
        for (int j = 0; j < matrix->cols; j++) {
            result->data[j][i] = matrix->data[i][j];
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

// =============================================================================
// EIGENVALUE PROBLEMS
// =============================================================================

// Power method for dominant eigenvalue
double powerMethodEigenvalue(Matrix* matrix, Vector* initial_vector, int max_iterations, double tolerance) {
    if (!matrix || !initial_vector || !matrix->is_square || matrix->rows != initial_vector->size) {
        return NAN;
    }
    
    Vector* vector = createVector(initial_vector->size);
    if (!vector) return NAN;
    
    // Copy initial vector
    for (int i = 0; i < initial_vector->size; i++) {
        vector->data[i] = initial_vector->data[i];
    }
    
    double eigenvalue = 0.0;
    
    for (int iter = 0; iter < max_iterations; iter++) {
        // Multiply matrix by vector
        Vector* new_vector = createVector(matrix->rows);
        if (!new_vector) {
            freeVector(vector);
            return NAN;
        }
        
        for (int i = 0; i < matrix->rows; i++) {
            new_vector->data[i] = 0.0;
            for (int j = 0; j < matrix->cols; j++) {
                new_vector->data[i] += matrix->data[i][j] * vector->data[j];
            }
        }
        
        // Find maximum element (approximate eigenvalue)
        double max_element = 0.0;
        int max_index = 0;
        for (int i = 0; i < new_vector->size; i++) {
            if (fabs(new_vector->data[i]) > fabs(max_element)) {
                max_element = new_vector->data[i];
                max_index = i;
            }
        }
        
        // Normalize vector
        if (fabs(max_element) < NUMERICAL_TOLERANCE) {
            freeVector(new_vector);
            freeVector(vector);
            return NAN;
        }
        
        for (int i = 0; i < new_vector->size; i++) {
            new_vector->data[i] /= max_element;
        }
        
        // Check convergence
        if (iter > 0 && fabs(max_element - eigenvalue) < tolerance) {
            eigenvalue = max_element;
            freeVector(vector);
            freeVector(new_vector);
            return eigenvalue;
        }
        
        eigenvalue = max_element;
        freeVector(vector);
        vector = new_vector;
    }
    
    freeVector(vector);
    return eigenvalue;
}

// =============================================================================
// NUMERICAL INTEGRATION
// =============================================================================

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

// =============================================================================
// ROOT FINDING
// =============================================================================

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

// =============================================================================
// DIFFERENTIAL EQUATIONS
// =============================================================================

// ODE system structure
typedef struct {
    int size;
    double (*derivatives)(double t, const double* y, double* dydt, void* params);
    void* params;
} ODESystem;

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

// =============================================================================
// INTERPOLATION
// =============================================================================

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

// Evaluate cubic spline
double evaluateCubicSpline(CubicSpline* spline, double* x, double x_interp) {
    if (!spline || !x) return NAN;
    
    // Find interval
    int i = 0;
    while (i < spline->n - 1 && x_interp > x[i + 1]) {
        i++;
    }
    
    if (i >= spline->n - 1) {
        i = spline->n - 2;
    }
    
    double dx = x_interp - x[i];
    return spline->a[i] + spline->b[i] * dx + spline->c[i] * dx * dx + spline->d[i] * dx * dx * dx;
}

// Free cubic spline
void freeCubicSpline(CubicSpline* spline) {
    if (!spline) return;
    
    free(spline->a);
    free(spline->b);
    free(spline->c);
    free(spline->d);
    free(spline);
}

// =============================================================================
// OPTIMIZATION
// =============================================================================

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

// =============================================================================
// STATISTICAL FUNCTIONS
// =============================================================================

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

// =============================================================================
// COMPLEX NUMBERS
// =============================================================================

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

// Complex subtraction
Complex complexSubtract(Complex a, Complex b) {
    return createComplex(a.real - b.real, a.imag - b.imag);
}

// Complex multiplication
Complex complexMultiply(Complex a, Complex b) {
    double real = a.real * b.real - a.imag * b.imag;
    double imag = a.real * b.imag + a.imag * b.real;
    return createComplex(real, imag);
}

// Complex division
Complex complexDivide(Complex a, Complex b) {
    double denominator = b.real * b.real + b.imag * b.imag;
    if (fabs(denominator) < NUMERICAL_TOLERANCE) {
        return createComplex(NAN, NAN);
    }
    
    double real = (a.real * b.real + a.imag * b.imag) / denominator;
    double imag = (a.imag * b.real - a.real * b.imag) / denominator;
    return createComplex(real, imag);
}

// Complex magnitude
double complexMagnitude(Complex c) {
    return sqrt(c.real * c.real + c.imag * c.imag);
}

// Complex phase
double complexPhase(Complex c) {
    return atan2(c.imag, c.real);
}

// Complex exponential
Complex complexExponential(Complex c) {
    double magnitude = exp(c.real);
    double phase = c.imag;
    return createComplex(magnitude * cos(phase), magnitude * sin(phase));
}

// =============================================================================
// FAST FOURIER TRANSFORM
// =============================================================================

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

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateLinearAlgebra() {
    printf("=== LINEAR ALGEBRA DEMO ===\n");
    
    // Create matrices
    Matrix* A = createMatrix(3, 3);
    Matrix* B = createMatrix(3, 3);
    
    if (!A || !B) {
        printf("Failed to create matrices\n");
        return;
    }
    
    // Initialize matrix A
    A->data[0][0] = 2.0; A->data[0][1] = -1.0; A->data[0][2] = 0.0;
    A->data[1][0] = -1.0; A->data[1][1] = 2.0; A->data[1][2] = -1.0;
    A->data[2][0] = 0.0; A->data[2][1] = -1.0; A->data[2][2] = 2.0;
    
    // Initialize matrix B
    B->data[0][0] = 1.0; B->data[0][1] = 2.0; B->data[0][2] = 3.0;
    B->data[1][0] = 4.0; B->data[1][1] = 5.0; B->data[1][2] = 6.0;
    B->data[2][0] = 7.0; B->data[2][1] = 8.0; B->data[2][2] = 9.0;
    
    printf("Matrix A:\n");
    for (int i = 0; i < A->rows; i++) {
        for (int j = 0; j < A->cols; j++) {
            printf("%8.3f ", A->data[i][j]);
        }
        printf("\n");
    }
    
    printf("\nMatrix B:\n");
    for (int i = 0; i < B->rows; i++) {
        for (int j = 0; j < B->cols; j++) {
            printf("%8.3f ", B->data[i][j]);
        }
        printf("\n");
    }
    
    // Matrix operations
    Matrix* C = matrixAdd(A, B);
    if (C) {
        printf("\nA + B:\n");
        for (int i = 0; i < C->rows; i++) {
            for (int j = 0; j < C->cols; j++) {
                printf("%8.3f ", C->data[i][j]);
            }
            printf("\n");
        }
        freeMatrix(C);
    }
    
    Matrix* D = matrixMultiply(A, B);
    if (D) {
        printf("\nA * B:\n");
        for (int i = 0; i < D->rows; i++) {
            for (int j = 0; j < D->cols; j++) {
                printf("%8.3f ", D->data[i][j]);
            }
            printf("\n");
        }
        freeMatrix(D);
    }
    
    double det = matrixDeterminant(A);
    printf("\nDeterminant of A: %.6f\n", det);
    
    Matrix* A_inv = matrixInverse(A);
    if (A_inv) {
        printf("\nInverse of A:\n");
        for (int i = 0; i < A_inv->rows; i++) {
            for (int j = 0; j < A_inv->cols; j++) {
                printf("%8.6f ", A_inv->data[i][j]);
            }
            printf("\n");
        }
        freeMatrix(A_inv);
    }
    
    // Eigenvalue computation
    Vector* initial = createVector(3);
    if (initial) {
        initial->data[0] = 1.0;
        initial->data[1] = 1.0;
        initial->data[2] = 1.0;
        
        double eigenvalue = powerMethodEigenvalue(A, initial, 100, 1e-10);
        if (!isnan(eigenvalue)) {
            printf("\nDominant eigenvalue of A: %.6f\n", eigenvalue);
        } else {
            printf("\nFailed to compute eigenvalue\n");
        }
        
        freeVector(initial);
    }
    
    freeMatrix(A);
    freeMatrix(B);
}

void demonstrateNumericalIntegration() {
    printf("\n=== NUMERICAL INTEGRATION DEMO ===\n");
    
    // Test function: f(x) = x^2
    MathFunction f = [](double x) { return x * x; };
    
    double a = 0.0;
    double b = 2.0;
    double exact = (b * b * b - a * a * a) / 3.0; // ∫x^2 dx = x^3/3
    
    printf("Integrating f(x) = x^2 from %.1f to %.1f\n", a, b);
    printf("Exact value: %.6f\n", exact);
    
    // Rectangle rule
    double rect = rectangleRule(f, a, b, 100);
    printf("Rectangle rule (n=100): %.6f (error: %.6f)\n", rect, fabs(rect - exact));
    
    // Trapezoidal rule
    double trap = trapezoidalRule(f, a, b, 100);
    printf("Trapezoidal rule (n=100): %.6f (error: %.6f)\n", trap, fabs(trap - exact));
    
    // Simpson's rule
    double simpson = simpsonRule(f, a, b, 100);
    printf("Simpson's rule (n=100): %.6f (error: %.6f)\n", simpson, fabs(simpson - exact));
    
    // Adaptive Simpson's rule
    double adaptive = adaptiveSimpson(f, a, b, 1e-6, 10, 0);
    printf("Adaptive Simpson: %.6f (error: %.6f)\n", adaptive, fabs(adaptive - exact));
    
    // Test with trigonometric function
    MathFunction sin_func = [](double x) { return sin(x); };
    a = 0.0;
    b = PI;
    exact = 2.0; // ∫sin(x) dx from 0 to π = 2
    
    printf("\nIntegrating f(x) = sin(x) from %.1f to %.1f\n", a, b);
    printf("Exact value: %.6f\n", exact);
    
    rect = rectangleRule(sin_func, a, b, 1000);
    printf("Rectangle rule (n=1000): %.6f (error: %.6f)\n", rect, fabs(rect - exact));
    
    trap = trapezoidalRule(sin_func, a, b, 1000);
    printf("Trapezoidal rule (n=1000): %.6f (error: %.6f)\n", trap, fabs(trap - exact));
    
    simpson = simpsonRule(sin_func, a, b, 1000);
    printf("Simpson's rule (n=1000): %.6f (error: %.6f)\n", simpson, fabs(simpson - exact));
}

void demonstrateRootFinding() {
    printf("\n=== ROOT FINDING DEMO ===\n");
    
    // Test function: f(x) = x^3 - 2x - 5
    MathFunction f = [](double x) { return x * x * x - 2 * x - 5; };
    MathFunction df = [](double x) { return 3 * x * x - 2; };
    
    printf("Finding root of f(x) = x^3 - 2x - 5\n");
    
    // Bisection method
    double bisection_root = bisectionMethod(f, 2.0, 3.0, 1e-10, 100);
    printf("Bisection method: %.10f\n", bisection_root);
    
    // Newton's method
    double newton_root = newtonMethod(f, df, 2.5, 1e-10, 100);
    printf("Newton's method: %.10f\n", newton_root);
    
    // Secant method
    double secant_root = secantMethod(f, 2.0, 3.0, 1e-10, 100);
    printf("Secant method: %.10f\n", secant_root);
    
    // Verify root
    double f_root = f(bisection_root);
    printf("f(root) = %.2e\n", f_root);
}

void demonstrateDifferentialEquations() {
    printf("\n=== DIFFERENTIAL EQUATIONS DEMO ===\n");
    
    // Test ODE: dy/dt = -2y, y(0) = 1
    // Exact solution: y(t) = e^(-2t)
    
    ODESystem system;
    system.size = 1;
    system.derivatives = [](double t, const double* y, double* dydt, void* params) {
        dydt[0] = -2.0 * y[0];
        return 0.0;
    };
    system.params = NULL;
    
    double t0 = 0.0;
    double t_end = 1.0;
    int steps = 10;
    double y0 = 1.0;
    
    double* euler_solution = malloc((steps + 1) * sizeof(double));
    double* rk4_solution = malloc((steps + 1) * sizeof(double));
    
    if (!euler_solution || !rk4_solution) {
        printf("Failed to allocate memory\n");
        free(euler_solution);
        free(rk4_solution);
        return;
    }
    
    printf("Solving dy/dt = -2y, y(0) = 1\n");
    printf("Exact solution: y(t) = e^(-2t)\n");
    
    // Euler's method
    eulerMethod(&system, t0, &y0, t_end, steps, euler_solution);
    
    // Runge-Kutta 4th order
    rungeKutta4(&system, t0, &y0, t_end, steps, rk4_solution);
    
    printf("\nResults:\n");
    printf("t\t\tEuler\t\tRK4\t\tExact\t\tEuler Error\tRK4 Error\n");
    printf("------------------------------------------------------------------------\n");
    
    for (int i = 0; i <= steps; i++) {
        double t = t0 + i * (t_end - t0) / steps;
        double exact = exp(-2.0 * t);
        double euler_error = fabs(euler_solution[i] - exact);
        double rk4_error = fabs(rk4_solution[i] - exact);
        
        printf("%.2f\t\t%.6f\t%.6f\t%.6f\t%.2e\t\t%.2e\n", 
               t, euler_solution[i], rk4_solution[i], exact, euler_error, rk4_error);
    }
    
    free(euler_solution);
    free(rk4_solution);
}

void demonstrateInterpolation() {
    printf("\n=== INTERPOLATION DEMO ===\n");
    
    // Sample points for sin(x)
    int n = 5;
    double x[] = {0.0, PI/4, PI/2, 3*PI/4, PI};
    double y[] = {0.0, sqrt(2)/2, 1.0, sqrt(2)/2, 0.0};
    
    printf("Interpolating sin(x) at given points:\n");
    for (int i = 0; i < n; i++) {
        printf("x[%.0f] = %.3f, y[%.0f] = %.3f\n", i, x[i], i, y[i]);
    }
    
    // Test interpolation points
    double test_points[] = {PI/6, PI/3, 2*PI/3, 5*PI/6};
    
    printf("\nInterpolation results:\n");
    printf("x\t\tLagrange\tLinear\t\tExact\t\tLagrange Error\tLinear Error\n");
    printf("------------------------------------------------------------------------\n");
    
    for (int i = 0; i < 4; i++) {
        double x_test = test_points[i];
        double exact = sin(x_test);
        
        double lagrange = lagrangeInterpolation(x, y, n, x_test);
        double linear = linearInterpolation(x[1], y[1], x[2], y[2], x_test);
        
        double lagrange_error = fabs(lagrange - exact);
        double linear_error = fabs(linear - exact);
        
        printf("%.3f\t\t%.6f\t%.6f\t%.6f\t%.2e\t\t%.2e\n", 
               x_test, lagrange, linear, exact, lagrange_error, linear_error);
    }
    
    // Cubic spline
    CubicSpline* spline = createCubicSpline(x, y, n);
    if (spline) {
        printf("\nCubic spline interpolation:\n");
        printf("x\t\tSpline\t\tExact\t\tError\n");
        printf("--------------------------------------------\n");
        
        for (int i = 0; i < 4; i++) {
            double x_test = test_points[i];
            double exact = sin(x_test);
            double spline_val = evaluateCubicSpline(spline, x, x_test);
            double error = fabs(spline_val - exact);
            
            printf("%.3f\t\t%.6f\t%.6f\t%.2e\n", x_test, spline_val, exact, error);
        }
        
        freeCubicSpline(spline);
    }
}

void demonstrateOptimization() {
    printf("\n=== OPTIMIZATION DEMO ===\n");
    
    // Test function: f(x) = (x-2)^2 + 1
    // Minimum at x = 2, f(x) = 1
    
    MathFunction f = [](double x) { return (x - 2) * (x - 2) + 1; };
    MathFunction df = [](double x) { return 2 * (x - 2); };
    
    printf("Finding minimum of f(x) = (x-2)^2 + 1\n");
    printf("Exact minimum: x = 2, f(x) = 1\n");
    
    // Gradient descent
    double gd_min = gradientDescent(f, df, 0.0, 0.1, 1000, 1e-10);
    printf("Gradient descent: x = %.6f, f(x) = %.6f\n", gd_min, f(gd_min));
    
    // Golden section search
    double gs_min = goldenSectionSearch(f, -5.0, 5.0, 1e-10);
    printf("Golden section: x = %.6f, f(x) = %.6f\n", gs_min, f(gs_min));
    
    // Test with more complex function
    MathFunction f2 = [](double x) { return x * x * x * x - 3 * x * x + 2; };
    MathFunction df2 = [](double x) { return 4 * x * x * x - 6 * x; };
    
    printf("\nFinding minimum of f(x) = x^4 - 3x^2 + 2\n");
    
    gd_min = gradientDescent(f2, df2, 1.0, 0.01, 1000, 1e-10);
    printf("Gradient descent: x = %.6f, f(x) = %.6f\n", gd_min, f2(gd_min));
    
    gs_min = goldenSectionSearch(f2, -2.0, 2.0, 1e-10);
    printf("Golden section: x = %.6f, f(x) = %.6f\n", gs_min, f2(gs_min));
}

void demonstrateStatistics() {
    printf("\n=== STATISTICS DEMO ===\n");
    
    // Sample data
    double data[] = {2.3, 3.1, 4.2, 3.8, 2.9, 3.5, 4.1, 3.7, 2.8, 3.3};
    int n = sizeof(data) / sizeof(data[0]);
    
    printf("Sample data: ");
    for (int i = 0; i < n; i++) {
        printf("%.1f ", data[i]);
    }
    printf("\n");
    
    double mean = calculateMean(data, n);
    double variance = calculateVariance(data, n);
    double std_dev = calculateStandardDeviation(data, n);
    
    printf("Mean: %.3f\n", mean);
    printf("Variance: %.3f\n", variance);
    printf("Standard deviation: %.3f\n", std_dev);
    
    // Correlation test
    double x[] = {1.0, 2.0, 3.0, 4.0, 5.0};
    double y[] = {2.1, 3.9, 6.2, 7.8, 10.1};
    
    printf("\nCorrelation test:\n");
    printf("x: ");
    for (int i = 0; i < 5; i++) {
        printf("%.1f ", x[i]);
    }
    printf("\n");
    
    printf("y: ");
    for (int i = 0; i < 5; i++) {
        printf("%.1f ", y[i]);
    }
    printf("\n");
    
    double correlation = calculateCorrelation(x, y, 5);
    printf("Correlation coefficient: %.6f\n", correlation);
}

void demonstrateComplexNumbers() {
    printf("\n=== COMPLEX NUMBERS DEMO ===\n");
    
    Complex a = createComplex(3.0, 4.0);
    Complex b = createComplex(1.0, -2.0);
    
    printf("Complex a = %.1f + %.1fi\n", a.real, a.imag);
    printf("Complex b = %.1f + %.1fi\n", b.real, b.imag);
    
    Complex sum = complexAdd(a, b);
    printf("a + b = %.1f + %.1fi\n", sum.real, sum.imag);
    
    Complex diff = complexSubtract(a, b);
    printf("a - b = %.1f + %.1fi\n", diff.real, diff.imag);
    
    Complex product = complexMultiply(a, b);
    printf("a * b = %.1f + %.1fi\n", product.real, product.imag);
    
    Complex quotient = complexDivide(a, b);
    printf("a / b = %.1f + %.1fi\n", quotient.real, quotient.imag);
    
    double magnitude = complexMagnitude(a);
    printf("|a| = %.3f\n", magnitude);
    
    double phase = complexPhase(a);
    printf("arg(a) = %.3f radians\n", phase);
    
    Complex exp_a = complexExponential(a);
    printf("e^a = %.3f + %.3fi\n", exp_a.real, exp_a.imag);
}

void demonstrateFFT() {
    printf("\n=== FFT DEMO ===\n");
    
    // Create test signal: sum of sine waves
    int n = 8;
    Complex* signal = malloc(n * sizeof(Complex));
    
    for (int i = 0; i < n; i++) {
        double t = 2.0 * PI * i / n;
        signal[i] = createComplex(sin(t) + 0.5 * sin(3 * t), 0.0);
    }
    
    printf("Original signal:\n");
    for (int i = 0; i < n; i++) {
        printf("x[%d] = %.3f + %.3fi\n", i, signal[i].real, signal[i].imag);
    }
    
    // FFT
    fft(signal, n);
    
    printf("\nFFT result:\n");
    for (int i = 0; i < n; i++) {
        printf("X[%d] = %.3f + %.3fi (magnitude: %.3f)\n", 
               i, signal[i].real, signal[i].imag, complexMagnitude(signal[i]));
    }
    
    // Inverse FFT
    ifft(signal, n);
    
    printf("\nInverse FFT result:\n");
    for (int i = 0; i < n; i++) {
        printf("x[%d] = %.3f + %.3fi\n", i, signal[i].real, signal[i].imag);
    }
    
    free(signal);
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Advanced Scientific Computing Examples\n");
    printf("===================================\n\n");
    
    // Run all demonstrations
    demonstrateLinearAlgebra();
    demonstrateNumericalIntegration();
    demonstrateRootFinding();
    demonstrateDifferentialEquations();
    demonstrateInterpolation();
    demonstrateOptimization();
    demonstrateStatistics();
    demonstrateComplexNumbers();
    demonstrateFFT();
    
    printf("\nAll advanced scientific computing examples demonstrated!\n");
    printf("Key features implemented:\n");
    printf("- Linear algebra (matrices, vectors, eigenvalues)\n");
    printf("- Numerical integration (rectangle, trapezoidal, Simpson, adaptive)\n");
    printf("- Root finding (bisection, Newton, secant)\n");
    printf("- Differential equations (Euler, Runge-Kutta)\n");
    printf("- Interpolation (Lagrange, linear, cubic spline)\n");
    printf("- Optimization (gradient descent, golden section)\n");
    printf("- Statistical functions (mean, variance, correlation)\n");
    printf("- Complex number operations\n");
    printf("- Fast Fourier Transform (FFT)\n");
    printf("- Numerical stability and error handling\n");
    
    return 0;
}
