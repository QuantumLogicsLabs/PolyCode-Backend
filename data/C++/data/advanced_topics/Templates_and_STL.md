# Module 11: Templates and Standard Template Library (STL)

## Learning Objectives
- Understand function templates and class templates
- Master template specialization and partial specialization
- Learn about STL containers (vector, list, map, set, etc.)
- Master STL algorithms and iterators
- Understand template metaprogramming concepts
- Learn about type traits and SFINAE

## Function Templates

### Basic Function Templates
```cpp
#include <iostream>
#include <string>

// Basic function template
template <typename T>
T maximum(T a, T b) {
    return (a > b) ? a : b;
}

// Function template with multiple parameters
template <typename T, typename U>
void printTypes(T a, U b) {
    std::cout << "First type: " << typeid(a).name() << ", value: " << a << std::endl;
    std::cout << "Second type: " << typeid(b).name() << ", value: " << b << std::endl;
}

// Function template with default parameter
template <typename T = int>
T add(T a, T b) {
    return a + b;
}

// Function template with constraints (C++20 concepts)
template <typename T>
requires std::is_arithmetic_v<T>
T multiply(T a, T b) {
    return a * b;
}

int main() {
    // Using function templates
    std::cout << "Maximum of 5 and 10: " << maximum(5, 10) << std::endl;
    std::cout << "Maximum of 3.14 and 2.71: " << maximum(3.14, 2.71) << std::endl;
    std::cout << "Maximum of 'A' and 'B': " << maximum('A', 'B') << std::endl;
    
    std::cout << "\nType information:" << std::endl;
    printTypes(42, 3.14);
    printTypes("Hello", 100);
    
    std::cout << "\nAddition with default type: " << add(5, 3) << std::endl;
    std::cout << "Addition with double: " << add<double>(2.5, 3.7) << std::endl;
    
    std::cout << "\nMultiplication: " << multiply(4, 5) << std::endl;
    std::cout << "Multiplication: " << multiply(2.5, 3.0) << std::endl;
    
    return 0;
}
```

### Advanced Function Templates
```cpp
#include <iostream>
#include <type_traits>

// Variadic template function
template <typename... Args>
void printAll(Args... args) {
    ((std::cout << args << " "), ...);
    std::cout << std::endl;
}

// Template function with perfect forwarding
template <typename T>
void wrapper(T&& arg) {
    std::cout << "Received: " << arg << std::endl;
    process(std::forward<T>(arg));
}

template <typename T>
void process(T& arg) {
    std::cout << "Processed lvalue: " << arg << std::endl;
}

template <typename T>
void process(T&& arg) {
    std::cout << "Processed rvalue: " << arg << std::endl;
}

// Template function with SFINAE
template <typename T>
typename std::enable_if<std::is_integral<T>::value, T>::type
doubleIfEven(T value) {
    return (value % 2 == 0) ? value * 2 : value;
}

template <typename T>
typename std::enable_if<!std::is_integral<T>::value, T>::type
doubleIfEven(T value) {
    return value; // Non-integer types remain unchanged
}

int main() {
    // Variadic template
    printAll(1, 2.5, "Hello", 'A');
    
    // Perfect forwarding
    int x = 42;
    wrapper(x);        // lvalue
    wrapper(42);        // rvalue
    
    // SFINAE
    std::cout << "Double if even (10): " << doubleIfEven(10) << std::endl;
    std::cout << "Double if even (7): " << doubleIfEven(7) << std::endl;
    std::cout << "Double if even (3.14): " << doubleIfEven(3.14) << std::endl;
    
    return 0;
}
```

## Class Templates

### Basic Class Template
```cpp
#include <iostream>
#include <stdexcept>

template <typename T>
class Stack {
private:
    T* data;
    int top;
    int capacity;
    
public:
    // Constructor
    Stack(int size = 10) : capacity(size), top(-1) {
        data = new T[capacity];
    }
    
    // Destructor
    ~Stack() {
        delete[] data;
    }
    
    // Copy constructor
    Stack(const Stack& other) : capacity(other.capacity), top(other.top) {
        data = new T[capacity];
        for (int i = 0; i <= top; i++) {
            data[i] = other.data[i];
        }
    }
    
    // Assignment operator
    Stack& operator=(const Stack& other) {
        if (this != &other) {
            delete[] data;
            capacity = other.capacity;
            top = other.top;
            data = new T[capacity];
            for (int i = 0; i <= top; i++) {
                data[i] = other.data[i];
            }
        }
        return *this;
    }
    
    // Push operation
    void push(const T& value) {
        if (top >= capacity - 1) {
            throw std::overflow_error("Stack overflow");
        }
        data[++top] = value;
    }
    
    // Pop operation
    T pop() {
        if (top < 0) {
            throw std::underflow_error("Stack underflow");
        }
        return data[top--];
    }
    
    // Peek operation
    T peek() const {
        if (top < 0) {
            throw std::underflow_error("Stack is empty");
        }
        return data[top];
    }
    
    // Check if empty
    bool isEmpty() const {
        return top < 0;
    }
    
    // Get size
    int getSize() const {
        return top + 1;
    }
};

int main() {
    // Stack with integers
    Stack<int> intStack;
    intStack.push(10);
    intStack.push(20);
    intStack.push(30);
    
    std::cout << "Integer stack:" << std::endl;
    while (!intStack.isEmpty()) {
        std::cout << intStack.pop() << " ";
    }
    std::cout << std::endl;
    
    // Stack with doubles
    Stack<double> doubleStack;
    doubleStack.push(3.14);
    doubleStack.push(2.71);
    doubleStack.push(1.41);
    
    std::cout << "\nDouble stack:" << std::endl;
    while (!doubleStack.isEmpty()) {
        std::cout << doubleStack.pop() << " ";
    }
    std::cout << std::endl;
    
    // Stack with strings
    Stack<std::string> stringStack;
    stringStack.push("Hello");
    stringStack.push("World");
    stringStack.push("C++");
    
    std::cout << "\nString stack:" << std::endl;
    while (!stringStack.isEmpty()) {
        std::cout << stringStack.pop() << " ";
    }
    std::cout << std::endl;
    
    return 0;
}
```

### Template Specialization
```cpp
#include <iostream>
#include <cstring>

// Primary template
template <typename T>
class Calculator {
public:
    T add(T a, T b) {
        return a + b;
    }
    
    T multiply(T a, T b) {
        return a * b;
    }
};

// Explicit specialization for const char*
template <>
class Calculator<const char*> {
public:
    const char* add(const char* a, const char* b) {
        static char result[100];
        strcpy(result, a);
        strcat(result, b);
        return result;
    }
    
    const char* multiply(const char* a, int times) {
        static char result[100];
        result[0] = '\0';
        for (int i = 0; i < times; i++) {
            strcat(result, a);
        }
        return result;
    }
};

// Partial specialization for pointer types
template <typename T>
class Calculator<T*> {
public:
    T add(T* a, T* b) {
        return *a + *b;
    }
    
    T multiply(T* a, T* b) {
        return *a * *b;
    }
};

int main() {
    Calculator<int> intCalc;
    std::cout << "5 + 3 = " << intCalc.add(5, 3) << std::endl;
    std::cout << "5 * 3 = " << intCalc.multiply(5, 3) << std::endl;
    
    Calculator<const char*> stringCalc;
    std::cout << "Hello + World = " << stringCalc.add("Hello", "World") << std::endl;
    std::cout << "Hi * 3 = " << stringCalc.multiply("Hi", 3) << std::endl;
    
    int x = 10, y = 20;
    Calculator<int*> pointerCalc;
    std::cout << "*10 + *20 = " << pointerCalc.add(&x, &y) << std::endl;
    
    return 0;
}
```

## STL Containers

### Sequence Containers
```cpp
#include <iostream>
#include <vector>
#include <list>
#include <deque>
#include <array>

void demonstrateVector() {
    std::cout << "=== std::vector ===" << std::endl;
    
    std::vector<int> vec = {1, 2, 3, 4, 5};
    
    // Add elements
    vec.push_back(6);
    vec.insert(vec.begin() + 2, 99);
    
    // Access elements
    std::cout << "First element: " << vec.front() << std::endl;
    std::cout << "Last element: " << vec.back() << std::endl;
    std::cout << "Element at index 2: " << vec[2] << std::endl;
    
    // Iterate
    std::cout << "All elements: ";
    for (const auto& elem : vec) {
        std::cout << elem << " ";
    }
    std::cout << std::endl;
    
    std::cout << "Size: " << vec.size() << std::endl;
    std::cout << "Capacity: " << vec.capacity() << std::endl;
}

void demonstrateList() {
    std::cout << "\n=== std::list ===" << std::endl;
    
    std::list<std::string> names = {"Alice", "Bob", "Charlie"};
    
    // Add elements
    names.push_back("Diana");
    names.push_front("Eve");
    
    // Insert in middle
    auto it = std::find(names.begin(), names.end(), "Bob");
    if (it != names.end()) {
        names.insert(it, "Frank");
    }
    
    // Remove elements
    names.remove("Charlie");
    
    // Iterate
    std::cout << "All names: ";
    for (const auto& name : names) {
        std::cout << name << " ";
    }
    std::cout << std::endl;
}

void demonstrateDeque() {
    std::cout << "\n=== std::deque ===" << std::endl;
    
    std::deque<int> dq = {10, 20, 30};
    
    // Add to both ends
    dq.push_front(5);
    dq.push_back(35);
    
    std::cout << "Deque elements: ";
    for (int i = 0; i < dq.size(); i++) {
        std::cout << dq[i] << " ";
    }
    std::cout << std::endl;
    
    // Remove from both ends
    dq.pop_front();
    dq.pop_back();
    
    std::cout << "After popping: ";
    for (const auto& elem : dq) {
        std::cout << elem << " ";
    }
    std::cout << std::endl;
}

void demonstrateArray() {
    std::cout << "\n=== std::array ===" << std::endl;
    
    std::array<int, 5> arr = {1, 2, 3, 4, 5};
    
    // Access elements
    std::cout << "First element: " << arr.front() << std::endl;
    std::cout << "Last element: " << arr.back() << std::endl;
    std::cout << "Element at index 2: " << arr.at(2) << std::endl;
    
    // Size is always fixed
    std::cout << "Size: " << arr.size() << std::endl;
    
    // Iterate
    std::cout << "All elements: ";
    for (const auto& elem : arr) {
        std::cout << elem << " ";
    }
    std::cout << std::endl;
}

int main() {
    demonstrateVector();
    demonstrateList();
    demonstrateDeque();
    demonstrateArray();
    
    return 0;
}
```

### Associative Containers
```cpp
#include <iostream>
#include <map>
#include <unordered_map>
#include <set>
#include <unordered_set>

void demonstrateMap() {
    std::cout << "=== std::map ===" << std::endl;
    
    std::map<std::string, int> ages;
    
    // Insert elements
    ages["Alice"] = 25;
    ages["Bob"] = 30;
    ages.insert({"Charlie", 35});
    ages.emplace("Diana", 28);
    
    // Access elements
    std::cout << "Alice's age: " << ages["Alice"] << std::endl;
    
    // Check if key exists
    if (ages.find("Bob") != ages.end()) {
        std::cout << "Bob's age: " << ages.at("Bob") << std::endl;
    }
    
    // Iterate
    std::cout << "All ages:" << std::endl;
    for (const auto& [name, age] : ages) {
        std::cout << name << ": " << age << std::endl;
    }
    
    // Size and empty
    std::cout << "Map size: " << ages.size() << std::endl;
}

void demonstrateUnorderedMap() {
    std::cout << "\n=== std::unordered_map ===" << std::endl;
    
    std::unordered_map<std::string, double> prices;
    
    prices["apple"] = 1.99;
    prices["banana"] = 0.99;
    prices["orange"] = 2.49;
    
    std::cout << "Prices:" << std::endl;
    for (const auto& [fruit, price] : prices) {
        std::cout << fruit << ": $" << price << std::endl;
    }
    
    std::cout << "Bucket count: " << prices.bucket_count() << std::endl;
}

void demonstrateSet() {
    std::cout << "\n=== std::set ===" << std::endl;
    
    std::set<int> numbers = {5, 2, 8, 1, 9, 2, 5}; // Duplicates are removed
    
    // Insert elements
    numbers.insert(3);
    numbers.insert(7);
    
    std::cout << "Unique numbers: ";
    for (const auto& num : numbers) {
        std::cout << num << " ";
    }
    std::cout << std::endl;
    
    // Find element
    if (numbers.find(5) != numbers.end()) {
        std::cout << "5 is in the set" << std::endl;
    }
    
    // Count (always 0 or 1 for set)
    std::cout << "Count of 2: " << numbers.count(2) << std::endl;
    std::cout << "Count of 10: " << numbers.count(10) << std::endl;
}

void demonstrateUnorderedSet() {
    std::cout << "\n=== std::unordered_set ===" << std::endl;
    
    std::unordered_set<std::string> words = {"hello", "world", "c++", "programming"};
    
    words.insert("template");
    words.insert("stl");
    
    std::cout << "Words: ";
    for (const auto& word : words) {
        std::cout << word << " ";
    }
    std::cout << std::endl;
    
    // Erase element
    words.erase("world");
    
    std::cout << "After erasing 'world': ";
    for (const auto& word : words) {
        std::cout << word << " ";
    }
    std::cout << std::endl;
}

int main() {
    demonstrateMap();
    demonstrateUnorderedMap();
    demonstrateSet();
    demonstrateUnorderedSet();
    
    return 0;
}
```

## STL Algorithms

### Common Algorithms
```cpp
#include <iostream>
#include <vector>
#include <algorithm>
#include <numeric>
#include <string>

void demonstrateNonModifyingAlgorithms() {
    std::cout << "=== Non-Modifying Algorithms ===" << std::endl;
    
    std::vector<int> numbers = {5, 2, 8, 1, 9, 3, 7, 4, 6};
    
    // Find
    auto it = std::find(numbers.begin(), numbers.end(), 7);
    if (it != numbers.end()) {
        std::cout << "Found 7 at position: " << std::distance(numbers.begin(), it) << std::endl;
    }
    
    // Find if
    auto evenIt = std::find_if(numbers.begin(), numbers.end(), [](int n) {
        return n % 2 == 0;
    });
    if (evenIt != numbers.end()) {
        std::cout << "First even number: " << *evenIt << std::endl;
    }
    
    // Count
    int count = std::count(numbers.begin(), numbers.end(), 5);
    std::cout << "Count of 5: " << count << std::endl;
    
    int evenCount = std::count_if(numbers.begin(), numbers.end(), [](int n) {
        return n % 2 == 0;
    });
    std::cout << "Count of even numbers: " << evenCount << std::endl;
    
    // All, any, none
    bool allPositive = std::all_of(numbers.begin(), numbers.end(), [](int n) {
        return n > 0;
    });
    std::cout << "All numbers positive: " << std::boolalpha << allPositive << std::endl;
    
    bool anyGreaterThan8 = std::any_of(numbers.begin(), numbers.end(), [](int n) {
        return n > 8;
    });
    std::cout << "Any number > 8: " << anyGreaterThan8 << std::endl;
}

void demonstrateModifyingAlgorithms() {
    std::cout << "\n=== Modifying Algorithms ===" << std::endl;
    
    std::vector<int> numbers = {1, 2, 3, 4, 5};
    
    // Copy
    std::vector<int> copy;
    std::copy(numbers.begin(), numbers.end(), std::back_inserter(copy));
    
    std::cout << "Original: ";
    for (int n : numbers) std::cout << n << " ";
    std::cout << "\nCopy: ";
    for (int n : copy) std::cout << n << " ";
    std::cout << std::endl;
    
    // Transform
    std::vector<int> squared;
    std::transform(numbers.begin(), numbers.end(), std::back_inserter(squared),
                   [](int n) { return n * n; });
    
    std::cout << "Squared: ";
    for (int n : squared) std::cout << n << " ";
    std::cout << std::endl;
    
    // Replace
    std::replace(numbers.begin(), numbers.end(), 3, 99);
    std::cout << "After replacing 3 with 99: ";
    for (int n : numbers) std::cout << n << " ";
    std::cout << std::endl;
    
    // Remove
    numbers.erase(std::remove(numbers.begin(), numbers.end(), 2), numbers.end());
    std::cout << "After removing 2: ";
    for (int n : numbers) std::cout << n << " ";
    std::cout << std::endl;
}

void demonstrateSortingAlgorithms() {
    std::cout << "\n=== Sorting Algorithms ===" << std::endl;
    
    std::vector<int> numbers = {5, 2, 8, 1, 9, 3, 7, 4, 6};
    
    std::cout << "Original: ";
    for (int n : numbers) std::cout << n << " ";
    std::cout << std::endl;
    
    // Sort
    std::sort(numbers.begin(), numbers.end());
    std::cout << "Sorted: ";
    for (int n : numbers) std::cout << n << " ";
    std::cout << std::endl;
    
    // Partial sort
    std::partial_sort(numbers.begin(), numbers.begin() + 3, numbers.end());
    std::cout << "Partial sort (first 3): ";
    for (int n : numbers) std::cout << n << " ";
    std::cout << std::endl;
    
    // Nth element
    std::nth_element(numbers.begin(), numbers.begin() + 4, numbers.end());
    std::cout << "After nth_element (4th element in correct position): ";
    for (int n : numbers) std::cout << n << " ";
    std::cout << std::endl;
    
    // Binary search (requires sorted range)
    std::sort(numbers.begin(), numbers.end());
    bool found = std::binary_search(numbers.begin(), numbers.end(), 7);
    std::cout << "7 found in sorted vector: " << std::boolalpha << found << std::endl;
}

void demonstrateNumericAlgorithms() {
    std::cout << "\n=== Numeric Algorithms ===" << std::endl;
    
    std::vector<int> numbers = {1, 2, 3, 4, 5};
    
    // Accumulate
    int sum = std::accumulate(numbers.begin(), numbers.end(), 0);
    std::cout << "Sum: " << sum << std::endl;
    
    int product = std::accumulate(numbers.begin(), numbers.end(), 1, std::multiplies<int>());
    std::cout << "Product: " << product << std::endl;
    
    // Inner product
    std::vector<int> weights = {2, 3, 1, 4, 2};
    int weightedSum = std::inner_product(numbers.begin(), numbers.end(), 
                                        weights.begin(), 0);
    std::cout << "Weighted sum: " << weightedSum << std::endl;
    
    // Adjacent difference
    std::vector<int> differences;
    std::adjacent_difference(numbers.begin(), numbers.end(), 
                            std::back_inserter(differences));
    std::cout << "Adjacent differences: ";
    for (int n : differences) std::cout << n << " ";
    std::cout << std::endl;
}

int main() {
    demonstrateNonModifyingAlgorithms();
    demonstrateModifyingAlgorithms();
    demonstrateSortingAlgorithms();
    demonstrateNumericAlgorithms();
    
    return 0;
}
```

## Iterators

### Iterator Categories and Usage
```cpp
#include <iostream>
#include <vector>
#include <list>
#include <iterator>

void demonstrateIterators() {
    std::cout << "=== Iterator Categories ===" << std::endl;
    
    std::vector<int> vec = {1, 2, 3, 4, 5};
    std::list<int> lst = {10, 20, 30, 40, 50};
    
    // Input iterator
    std::cout << "Input iterator: ";
    std::istream_iterator<int> input_it(std::cin);
    // std::cin >> *input_it; // Would read from input
    
    // Output iterator
    std::cout << "\nOutput iterator: ";
    std::ostream_iterator<int> output_it(std::cout, " ");
    std::copy(vec.begin(), vec.end(), output_it);
    std::cout << std::endl;
    
    // Forward iterator
    std::cout << "Forward iterator (list): ";
    for (auto it = lst.begin(); it != lst.end(); ++it) {
        std::cout << *it << " ";
    }
    std::cout << std::endl;
    
    // Bidirectional iterator
    std::cout << "Bidirectional iterator (reverse): ";
    for (auto it = lst.rbegin(); it != lst.rend(); ++it) {
        std::cout << *it << " ";
    }
    std::cout << std::endl;
    
    // Random access iterator
    std::cout << "Random access iterator (vector): " << std::endl;
    std::cout << "Element at index 2: " << vec[2] << std::endl;
    std::cout << "Element at index 2 (iterator): " << *(vec.begin() + 2) << std::endl;
    
    // Iterator arithmetic
    auto it = vec.begin() + 2;
    std::cout << "Distance from begin: " << std::distance(vec.begin(), it) << std::endl;
    
    // Reverse iterators
    std::cout << "Reverse order: ";
    for (auto rit = vec.rbegin(); rit != vec.rend(); ++rit) {
        std::cout << *rit << " ";
    }
    std::cout << std::endl;
}

void demonstrateIteratorAdapters() {
    std::cout << "\n=== Iterator Adapters ===" << std::endl;
    
    std::vector<int> numbers = {1, 2, 3, 4, 5};
    
    // Back inserter
    std::vector<int> result;
    std::copy(numbers.begin(), numbers.end(), std::back_inserter(result));
    std::cout << "Back inserted: ";
    for (int n : result) std::cout << n << " ";
    std::cout << std::endl;
    
    // Front inserter (works with containers that have push_front)
    std::list<int> resultList;
    std::copy(numbers.begin(), numbers.end(), std::front_inserter(resultList));
    std::cout << "Front inserted (reversed): ";
    for (int n : resultList) std::cout << n << " ";
    std::cout << std::endl;
    
    // Inserter with position
    std::vector<int> insertResult = {99, 100};
    std::copy(numbers.begin(), numbers.end(), 
              std::inserter(insertResult, insertResult.begin() + 1));
    std::cout << "Inserted at position 1: ";
    for (int n : insertResult) std::cout << n << " ";
    std::cout << std::endl;
}

int main() {
    demonstrateIterators();
    demonstrateIteratorAdapters();
    
    return 0;
}
```

## Complete Example: Generic Data Processing System

```cpp
#include <iostream>
#include <vector>
#include <algorithm>
#include <numeric>
#include <map>
#include <string>
#include <functional>
#include <type_traits>

// Generic data processor template
template <typename T>
class DataProcessor {
private:
    std::vector<T> data;
    
public:
    // Add data
    void addData(const T& value) {
        data.push_back(value);
    }
    
    void addData(const std::vector<T>& values) {
        data.insert(data.end(), values.begin(), values.end());
    }
    
    // Get data
    const std::vector<T>& getData() const {
        return data;
    }
    
    // Clear data
    void clear() {
        data.clear();
    }
    
    // Sort data
    void sort() {
        std::sort(data.begin(), data.end());
    }
    
    // Sort with custom comparator
    template <typename Compare>
    void sort(Compare comp) {
        std::sort(data.begin(), data.end(), comp);
    }
    
    // Filter data
    template <typename Predicate>
    std::vector<T> filter(Predicate pred) const {
        std::vector<T> result;
        std::copy_if(data.begin(), data.end(), std::back_inserter(result), pred);
        return result;
    }
    
    // Transform data
    template <typename Transform>
    auto transform(Transform func) const -> std::vector<decltype(func(data[0]))> {
        std::vector<decltype(func(data[0]))> result;
        std::transform(data.begin(), data.end(), std::back_inserter(result), func);
        return result;
    }
    
    // Calculate statistics (only for arithmetic types)
    template <typename U = T>
    typename std::enable_if<std::is_arithmetic<U>::value, U>::type
    sum() const {
        return std::accumulate(data.begin(), data.end(), U{0});
    }
    
    template <typename U = T>
    typename std::enable_if<std::is_arithmetic<U>::value, double>::type
    average() const {
        if (data.empty()) return 0.0;
        return static_cast<double>(sum()) / data.size();
    }
    
    template <typename U = T>
    typename std::enable_if<std::is_arithmetic<U>::value, U>::type
    min() const {
        if (data.empty()) throw std::runtime_error("Empty data");
        return *std::min_element(data.begin(), data.end());
    }
    
    template <typename U = T>
    typename std::enable_if<std::is_arithmetic<U>::value, U>::type
    max() const {
        if (data.empty()) throw std::runtime_error("Empty data");
        return *std::max_element(data.begin(), data.end());
    }
    
    // Group data (for types that support equality)
    std::map<T, int> groupByCount() const {
        std::map<T, int> groups;
        for (const auto& item : data) {
            groups[item]++;
        }
        return groups;
    }
    
    // Display data
    void display() const {
        std::cout << "Data: ";
        for (const auto& item : data) {
            std::cout << item << " ";
        }
        std::cout << std::endl;
    }
    
    // Get size
    size_t size() const {
        return data.size();
    }
    
    // Check if empty
    bool empty() const {
        return data.empty();
    }
};

// Specialization for string data
template <>
class DataProcessor<std::string> {
private:
    std::vector<std::string> data;
    
public:
    void addData(const std::string& value) {
        data.push_back(value);
    }
    
    void addData(const std::vector<std::string>& values) {
        data.insert(data.end(), values.begin(), values.end());
    }
    
    const std::vector<std::string>& getData() const {
        return data;
    }
    
    void clear() {
        data.clear();
    }
    
    void sort() {
        std::sort(data.begin(), data.end());
    }
    
    std::vector<std::string> filter(std::function<bool(const std::string&)> pred) const {
        std::vector<std::string> result;
        std::copy_if(data.begin(), data.end(), std::back_inserter(result), pred);
        return result;
    }
    
    std::vector<int> transform(std::function<int(const std::string&)> func) const {
        std::vector<int> result;
        std::transform(data.begin(), data.end(), std::back_inserter(result), func);
        return result;
    }
    
    // String-specific operations
    std::vector<std::string> filterByLength(int minLength) const {
        return filter([minLength](const std::string& s) {
            return s.length() >= minLength;
        });
    }
    
    std::vector<std::string> filterByPrefix(const std::string& prefix) const {
        return filter([&prefix](const std::string& s) {
            return s.substr(0, prefix.length()) == prefix;
        });
    }
    
    std::vector<int> getLengths() const {
        return transform([](const std::string& s) {
            return static_cast<int>(s.length());
        });
    }
    
    std::map<std::string, int> groupByCount() const {
        std::map<std::string, int> groups;
        for (const auto& item : data) {
            groups[item]++;
        }
        return groups;
    }
    
    void display() const {
        std::cout << "Strings: ";
        for (const auto& item : data) {
            std::cout << "\"" << item << "\" ";
        }
        std::cout << std::endl;
    }
    
    size_t size() const {
        return data.size();
    }
    
    bool empty() const {
        return data.empty();
    }
};

int main() {
    std::cout << "=== Numeric Data Processing ===" << std::endl;
    
    DataProcessor<int> intProcessor;
    intProcessor.addData({1, 5, 3, 7, 2, 9, 4, 6, 8});
    
    std::cout << "Original data: ";
    intProcessor.display();
    
    std::cout << "Sum: " << intProcessor.sum() << std::endl;
    std::cout << "Average: " << intProcessor.average() << std::endl;
    std::cout << "Min: " << intProcessor.min() << std::endl;
    std::cout << "Max: " << intProcessor.max() << std::endl;
    
    intProcessor.sort();
    std::cout << "Sorted: ";
    intProcessor.display();
    
    // Filter even numbers
    auto evenNumbers = intProcessor.filter([](int n) { return n % 2 == 0; });
    std::cout << "Even numbers: ";
    for (int n : evenNumbers) std::cout << n << " ";
    std::cout << std::endl;
    
    // Transform to squares
    auto squares = intProcessor.transform([](int n) { return n * n; });
    std::cout << "Squares: ";
    for (int n : squares) std::cout << n << " ";
    std::cout << std::endl;
    
    // Group by count
    intProcessor.addData({3, 5, 3, 7, 5});
    auto groups = intProcessor.groupByCount();
    std::cout << "Group by count: ";
    for (const auto& [value, count] : groups) {
        std::cout << value << ":" << count << " ";
    }
    std::cout << std::endl;
    
    std::cout << "\n=== String Data Processing ===" << std::endl;
    
    DataProcessor<std::string> stringProcessor;
    stringProcessor.addData({"apple", "banana", "cherry", "date", "elderberry"});
    
    std::cout << "Original strings: ";
    stringProcessor.display();
    
    stringProcessor.sort();
    std::cout << "Sorted: ";
    stringProcessor.display();
    
    // Filter by length
    auto longStrings = stringProcessor.filterByLength(6);
    std::cout << "Strings with length >= 6: ";
    for (const auto& s : longStrings) std::cout << "\"" << s << "\" ";
    std::cout << std::endl;
    
    // Filter by prefix
    auto bStrings = stringProcessor.filterByPrefix("b");
    std::cout << "Strings starting with 'b': ";
    for (const auto& s : bStrings) std::cout << "\"" << s << "\" ";
    std::cout << std::endl;
    
    // Get lengths
    auto lengths = stringProcessor.getLengths();
    std::cout << "Lengths: ";
    for (int len : lengths) std::cout << len << " ";
    std::cout << std::endl;
    
    return 0;
}
```

## Practice Exercises

### Exercise 1: Generic Stack Template
Create a generic stack template:
- Support different data types
- Template specialization for pointers
- Exception handling
- Iterator support

### Exercise 2: STL Container Analysis
Write a program that:
- Compares performance of different containers
- Tests various algorithms
- Measures memory usage
- Provides recommendations

### Exercise 3: Custom Allocator
Implement a custom allocator:
- Memory pool management
- Debug features
- Performance optimization
- Integration with STL containers

### Exercise 4: Template Metaprogramming
Create template metaprogramming examples:
- Compile-time calculations
- Type traits
- SFINAE techniques
- Concept-based constraints

## Key Takeaways
- Templates enable generic programming in C++
- Function templates work with different data types
- Class templates create generic classes
- STL provides powerful containers and algorithms
- Iterators provide uniform access to containers
- Template specialization allows custom behavior
- SFINAE enables compile-time type checking
- Concepts (C++20) improve template constraints

## Next Module
In the next module, we'll explore exception handling and error management in C++.