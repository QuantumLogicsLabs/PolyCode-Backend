// ============================================================
//  C++ Mathematics — Complete Examples
// ============================================================

#include <iostream>
#include <vector>
#include <cmath>
#include <numeric>
#include <algorithm>
#include <functional>

// ─────────────────────────────────────────────
// SECTION 1: Number Theory
// ─────────────────────────────────────────────

std::vector<bool> sieve(int n) {
    std::vector<bool> is_prime(n + 1, true);
    is_prime[0] = is_prime[1] = false;
    for (int i = 2; i * i <= n; i++)
        if (is_prime[i])
            for (int j = i * i; j <= n; j += i) is_prime[j] = false;
    return is_prime;
}

int gcd(int a, int b) { return b == 0 ? a : gcd(b, a % b); }
int lcm(int a, int b) { return a / gcd(a, b) * b; }

long long power(long long base, long long exp, long long mod) {
    long long result = 1;
    base %= mod;
    while (exp > 0) {
        if (exp & 1) result = result * base % mod;
        base = base * base % mod;
        exp >>= 1;
    }
    return result;
}

// ─────────────────────────────────────────────
// SECTION 2: Fibonacci & Sequences
// ─────────────────────────────────────────────

long long fibonacci(int n) {
    if (n <= 1) return n;
    long long a = 0, b = 1;
    for (int i = 2; i <= n; i++) { long long c = a + b; a = b; b = c; }
    return b;
}

std::vector<long long> pascalRow(int n) {
    std::vector<long long> row(n + 1, 1);
    for (int i = 1; i < n; i++) row[i] = row[i - 1] * (n - i + 1) / i;
    return row;
}

// ─────────────────────────────────────────────
// SECTION 3: Statistics
// ─────────────────────────────────────────────

double mean(const std::vector<double>& v) {
    return std::accumulate(v.begin(), v.end(), 0.0) / v.size();
}

double variance(const std::vector<double>& v) {
    double m = mean(v), var = 0;
    for (double x : v) var += (x - m) * (x - m);
    return var / v.size();
}

double stddev(const std::vector<double>& v) { return std::sqrt(variance(v)); }

double median(std::vector<double> v) {
    std::sort(v.begin(), v.end());
    int n = v.size();
    return (n % 2 == 0) ? (v[n/2-1] + v[n/2]) / 2.0 : v[n/2];
}

// ─────────────────────────────────────────────
// SECTION 4: Numerical Methods
// ─────────────────────────────────────────────

double sqrtNewton(double n, double eps = 1e-9) {
    double x = n;
    while (std::abs(x * x - n) > eps) x = (x + n / x) / 2.0;
    return x;
}

double bisection(std::function<double(double)> f, double a, double b, double eps = 1e-9) {
    while (b - a > eps) {
        double mid = (a + b) / 2;
        if (f(a) * f(mid) < 0) b = mid; else a = mid;
    }
    return (a + b) / 2;
}

// ─────────────────────────────────────────────
// SECTION 5: Matrix Operations
// ─────────────────────────────────────────────

using Matrix = std::vector<std::vector<double>>;

Matrix multiply(const Matrix& A, const Matrix& B) {
    int n = A.size(), m = B[0].size(), k = B.size();
    Matrix C(n, std::vector<double>(m, 0));
    for (int i = 0; i < n; i++)
        for (int j = 0; j < m; j++)
            for (int p = 0; p < k; p++) C[i][j] += A[i][p] * B[p][j];
    return C;
}

void printMatrix(const Matrix& M) {
    for (auto& row : M) {
        for (double v : row) std::cout << v << "\t";
        std::cout << "\n";
    }
}

// ─────────────────────────────────────────────
// MAIN
// ─────────────────────────────────────────────

int main() {
    std::cout << "===== C++ Mathematics Demo =====\n\n";

    // Primes up to 50
    std::cout << "--- Primes up to 50 ---\n";
    auto primes = sieve(50);
    for (int i = 2; i <= 50; i++) if (primes[i]) std::cout << i << " ";
    std::cout << "\n";

    // GCD / LCM
    std::cout << "\n--- GCD & LCM ---\n";
    std::cout << "gcd(48, 18) = " << gcd(48, 18) << "\n";
    std::cout << "lcm(4, 6)   = " << lcm(4, 6) << "\n";

    // Modular exponentiation
    std::cout << "\n--- Fast Power ---\n";
    std::cout << "2^10 mod 1000 = " << power(2, 10, 1000) << "\n";
    std::cout << "3^20 mod 1e9+7 = " << power(3, 20, 1000000007) << "\n";

    // Fibonacci
    std::cout << "\n--- Fibonacci ---\n";
    for (int i = 0; i <= 12; i++) std::cout << "F(" << i << ")=" << fibonacci(i) << "  ";
    std::cout << "\n";

    // Pascal's triangle row 6
    std::cout << "\n--- Pascal Row 6 ---\n";
    auto row = pascalRow(6);
    for (long long x : row) std::cout << x << " ";
    std::cout << "\n";

    // Statistics
    std::cout << "\n--- Statistics ---\n";
    std::vector<double> data = {4, 8, 15, 16, 23, 42};
    std::cout << "Data: 4 8 15 16 23 42\n";
    std::cout << "Mean:   " << mean(data) << "\n";
    std::cout << "Median: " << median(data) << "\n";
    std::cout << "StdDev: " << stddev(data) << "\n";

    // Newton sqrt
    std::cout << "\n--- Newton-Raphson sqrt(2) ---\n";
    std::cout << "sqrt(2) = " << sqrtNewton(2.0) << " (std::sqrt = " << std::sqrt(2.0) << ")\n";

    // Bisection: find root of x^3 - x - 2 in [1, 2]
    std::cout << "\n--- Bisection: root of x^3 - x - 2 ---\n";
    auto f = [](double x){ return x*x*x - x - 2; };
    std::cout << "Root ≈ " << bisection(f, 1.0, 2.0) << " (expected ≈ 1.5214)\n";

    // Matrix multiply
    std::cout << "\n--- Matrix Multiplication ---\n";
    Matrix A = {{1,2},{3,4}};
    Matrix B = {{5,6},{7,8}};
    std::cout << "A × B =\n";
    printMatrix(multiply(A, B));

    std::cout << "\n✅ Mathematics demos complete!\n";
    return 0;
}
