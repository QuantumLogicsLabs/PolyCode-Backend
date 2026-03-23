# C++ Mathematics

Essential mathematical algorithms and techniques in C++ — from number theory to numerical methods.

---

## 1. Number Theory

### Prime Numbers — Sieve of Eratosthenes
Generates all primes up to `n` in O(n log log n).

```cpp
std::vector<bool> sieve(int n) {
    std::vector<bool> is_prime(n + 1, true);
    is_prime[0] = is_prime[1] = false;
    for (int i = 2; i * i <= n; i++)
        if (is_prime[i])
            for (int j = i * i; j <= n; j += i)
                is_prime[j] = false;
    return is_prime;
}
```

### GCD & LCM
```cpp
int gcd(int a, int b) { return b == 0 ? a : gcd(b, a % b); }
int lcm(int a, int b) { return a / gcd(a, b) * b; }
```

### Fast Power (Modular Exponentiation)
```cpp
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
```

---

## 2. Fibonacci & Sequences

```cpp
// Iterative — O(n), O(1) space
long long fibonacci(int n) {
    if (n <= 1) return n;
    long long a = 0, b = 1;
    for (int i = 2; i <= n; i++) { long long c = a + b; a = b; b = c; }
    return b;
}

// Pascal's Triangle row
std::vector<long long> pascalRow(int n) {
    std::vector<long long> row(n + 1, 1);
    for (int i = 1; i < n; i++)
        row[i] = row[i - 1] * (n - i + 1) / i;
    return row;
}
```

---

## 3. Statistics

```cpp
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
```

---

## 4. Numerical Methods

### Newton-Raphson Square Root
```cpp
double sqrtNewton(double n, double eps = 1e-9) {
    double x = n;
    while (std::abs(x * x - n) > eps)
        x = (x + n / x) / 2.0;
    return x;
}
```

### Bisection Method (Root Finding)
```cpp
double bisection(std::function<double(double)> f, double a, double b, double eps = 1e-9) {
    while (b - a > eps) {
        double mid = (a + b) / 2;
        if (f(a) * f(mid) < 0) b = mid;
        else a = mid;
    }
    return (a + b) / 2;
}
```

---

## 5. Matrix Operations

```cpp
using Matrix = std::vector<std::vector<double>>;

Matrix multiply(const Matrix& A, const Matrix& B) {
    int n = A.size(), m = B[0].size(), k = B.size();
    Matrix C(n, std::vector<double>(m, 0));
    for (int i = 0; i < n; i++)
        for (int j = 0; j < m; j++)
            for (int p = 0; p < k; p++)
                C[i][j] += A[i][p] * B[p][j];
    return C;
}

Matrix transpose(const Matrix& A) {
    int n = A.size(), m = A[0].size();
    Matrix T(m, std::vector<double>(n));
    for (int i = 0; i < n; i++)
        for (int j = 0; j < m; j++) T[j][i] = A[i][j];
    return T;
}
```

---

## Summary

| Topic | Key Function | Complexity |
|---|---|---|
| Prime Sieve | `sieve(n)` | O(n log log n) |
| GCD | `gcd(a, b)` | O(log min(a,b)) |
| Fast Power | `power(b, e, mod)` | O(log e) |
| Statistics | `mean/stddev/median` | O(n) |
| Matrix Multiply | `multiply(A, B)` | O(n³) |

> 💡 Always use `long long` for large number arithmetic and apply modular arithmetic to prevent overflow in competitive programming.
