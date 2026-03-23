# Module 15: Modern C++ and Best Practices

## Learning Objectives
- Understand C++11/14/17/20/23 features
- Master modern C++ programming practices
- Learn about performance optimization techniques
- Understand code quality and maintainability
- Master build systems and project organization
- Learn about debugging and profiling tools

## Modern C++ Features

### C++11 Features
```cpp
#include <iostream>
#include <vector>
#include <memory>
#include <thread>
#include <chrono>
#include <algorithm>
#include <unordered_map>
#include <tuple>

void demonstrateCpp11Features() {
    std::cout << "=== C++11 Features ===" << std::endl;
    
    // Auto type deduction
    auto number = 42;
    auto pi = 3.14159;
    auto text = "Hello, Modern C++!";
    
    std::cout << "Auto deduction: " << number << ", " << pi << ", " << text << std::endl;
    
    // Range-based for loops
    std::vector<int> numbers = {1, 2, 3, 4, 5};
    std::cout << "Range-based for loop: ";
    for (const auto& num : numbers) {
        std::cout << num << " ";
    }
    std::cout << std::endl;
    
    // Lambda expressions
    auto square = [](int x) { return x * x; };
    std::cout << "Lambda square(5): " << square(5) << std::endl;
    
    // Smart pointers
    auto uniquePtr = std::make_unique<int>(100);
    std::cout << "Unique pointer: " << *uniquePtr << std::endl;
    
    auto sharedPtr = std::make_shared<std::string>("Shared");
    std::cout << "Shared pointer: " << *sharedPtr << std::endl;
    
    // Move semantics
    std::string str1 = "Hello";
    std::string str2 = std::move(str1);
    std::cout << "After move: str1='" << str1 << "', str2='" << str2 << "'" << std::endl;
    
    // nullptr
    int* ptr = nullptr;
    std::cout << "nullptr check: " << (ptr == nullptr ? "null" : "not null") << std::endl;
    
    // constexpr
    constexpr int factorial(int n) {
        return (n <= 1) ? 1 : n * factorial(n - 1);
    }
    constexpr int fact5 = factorial(5);
    std::cout << "Compile-time factorial(5): " << fact5 << std::endl;
    
    // Tuple
    auto person = std::make_tuple("John", 30, "Engineer");
    std::string name = std::get<0>(person);
    int age = std::get<1>(person);
    std::cout << "Tuple: " << name << ", " << age << std::endl;
    
    // Unordered containers
    std::unordered_map<std::string, int> phoneBook;
    phoneBook["Alice"] = 12345;
    phoneBook["Bob"] = 67890;
    std::cout << "Phone book size: " << phoneBook.size() << std::endl;
}
```

### C++14 Features
```cpp
#include <iostream>
#include <memory>
#include <utility>

void demonstrateCpp14Features() {
    std::cout << "\n=== C++14 Features ===" << std::endl;
    
    // Generic lambdas
    auto genericLambda = [](auto x, auto y) { return x + y; };
    std::cout << "Generic lambda (5 + 3.14): " << genericLambda(5, 3.14) << std::endl;
    
    // make_unique with arrays
    auto arrUnique = std::make_unique<int[]>(5);
    for (int i = 0; i < 5; i++) {
        arrUnique[i] = i * 10;
    }
    std::cout << "make_unique array: ";
    for (int i = 0; i < 5; i++) {
        std::cout << arrUnique[i] << " ";
    }
    std::cout << std::endl;
    
    // Return type deduction
    auto getLarger = [](auto a, auto b) {
        return (a > b) ? a : b;
    };
    std::cout << "Return type deduction: " << getLarger(10, 20) << std::endl;
    
    // std::exchange
    int value = 10;
    int oldValue = std::exchange(value, 20);
    std::cout << "std::exchange: old=" << oldValue << ", new=" << value << std::endl;
}
```

### C++17 Features
```cpp
#include <iostream>
#include <string>
#include <optional>
#include <variant>
#include <any>
#include <filesystem>
#include <algorithm>

void demonstrateCpp17Features() {
    std::cout << "\n=== C++17 Features ===" << std::endl;
    
    // std::optional
    std::optional<int> maybeValue = 42;
    if (maybeValue) {
        std::cout << "Optional value: " << *maybeValue << std::endl;
    }
    
    std::optional<int> emptyValue;
    std::cout << "Optional has value: " << emptyValue.has_value() << std::endl;
    
    // std::variant
    std::variant<int, double, std::string> data = 3.14;
    std::visit([](const auto& value) {
        std::cout << "Variant value: " << value << std::endl;
    }, data);
    
    data = "Hello";
    std::visit([](const auto& value) {
        std::cout << "Variant value: " << value << std::endl;
    }, data);
    
    // std::any
    std::any anyValue = 42;
    std::cout << "std::any as int: " << std::any_cast<int>(anyValue) << std::endl;
    
    anyValue = std::string("Hello");
    if (anyValue.type() == typeid(std::string)) {
        std::cout << "std::any as string: " << std::any_cast<std::string>(anyValue) << std::endl;
    }
    
    // std::filesystem
    std::filesystem::path currentPath = std::filesystem::current_path();
    std::cout << "Current path: " << currentPath.string() << std::endl;
    
    // Structured bindings
    std::pair<int, std::string> person = {25, "Alice"};
    auto [age, name] = person;
    std::cout << "Structured binding: " << name << " is " << age << " years old" << std::endl;
    
    // std::string_view
    std::string_view sv = "Hello, string view!";
    std::cout << "String view: " << sv << std::endl;
    
    // Parallel algorithms (if supported)
    std::vector<int> vec = {1, 2, 3, 4, 5};
    std::for_each(std::execution::par, vec.begin(), vec.end(), 
                  [](int& n) { n *= 2; });
    
    std::cout << "Parallel algorithm result: ";
    for (int n : vec) {
        std::cout << n << " ";
    }
    std::cout << std::endl;
}
```

### C++20 Features
```cpp
#include <iostream>
#include <concepts>
#include <ranges>
#include <format>
#include <numbers>

// Concept definition
template<typename T>
concept Numeric = std::is_arithmetic_v<T>;

// Function with concept
template<Numeric T>
T add(T a, T b) {
    return a + b;
}

// Concept with requirements
template<typename T>
concept Container = requires(T t) {
    typename T::value_type;
    typename T::iterator;
    { t.size() } -> std::convertible_to<size_t>;
    { t.begin() } -> std::input_iterator;
    { t.end() } -> std::input_iterator;
};

void demonstrateCpp20Features() {
    std::cout << "\n=== C++20 Features ===" << std::endl;
    
    // Concepts
    std::cout << "Concepts - add(5, 3): " << add(5, 3) << std::endl;
    std::cout << "Concepts - add(2.5, 3.5): " << add(2.5, 3.5) << std::endl;
    
    // Ranges
    std::vector<int> numbers = {1, 2, 3, 4, 5, 6, 7, 8, 9, 10};
    auto evenNumbers = numbers | std::views::filter([](int n) { return n % 2 == 0; });
    auto squared = evenNumbers | std::views::transform([](int n) { return n * n; });
    
    std::cout << "Ranges - even squares: ";
    for (int n : squared) {
        std::cout << n << " ";
    }
    std::cout << std::endl;
    
    // std::format
    std::string formatted = std::format("Hello, {}! The answer is {}", "World", 42);
    std::cout << "std::format: " << formatted << std::endl;
    
    // Mathematical constants
    std::cout << "Math constants - pi: " << std::numbers::pi << std::endl;
    std::cout << "Math constants - e: " << std::numbers::e << std::endl;
    
    // Three-way comparison (spaceship operator)
    auto compare = [](auto a, auto b) {
        if (a <=> b == 0) return "equal";
        else if (a <=> b > 0) return "greater";
        else return "less";
    };
    
    std::cout << "Three-way comparison: 5 " << compare(5, 3) << " 3" << std::endl;
}
```

## Performance Optimization

### Memory Management and Optimization
```cpp
#include <iostream>
#include <vector>
#include <memory>
#include <chrono>

class PerformanceOptimization {
public:
    // Reserve vector capacity to avoid reallocations
    static void demonstrateVectorOptimization() {
        std::cout << "=== Vector Optimization ===" << std::endl;
        
        auto start = std::chrono::high_resolution_clock::now();
        
        // Bad: Multiple reallocations
        std::vector<int> badVector;
        for (int i = 0; i < 100000; i++) {
            badVector.push_back(i);
        }
        
        auto mid = std::chrono::high_resolution_clock::now();
        
        // Good: Reserve capacity upfront
        std::vector<int> goodVector;
        goodVector.reserve(100000);
        for (int i = 0; i < 100000; i++) {
            goodVector.push_back(i);
        }
        
        auto end = std::chrono::high_resolution_clock::now();
        
        auto badDuration = std::chrono::duration_cast<std::chrono::microseconds>(mid - start);
        auto goodDuration = std::chrono::duration_cast<std::chrono::microseconds>(end - mid);
        
        std::cout << "Without reserve: " << badDuration.count() << " μs" << std::endl;
        std::cout << "With reserve: " << goodDuration.count() << " μs" << std::endl;
    }
    
    // Move semantics for efficiency
    static void demonstrateMoveSemantics() {
        std::cout << "\n=== Move Semantics ===" << std::endl;
        
        class LargeObject {
        private:
            std::vector<int> data;
            
        public:
            LargeObject() : data(1000000, 42) {
                std::cout << "LargeObject constructed" << std::endl;
            }
            
            LargeObject(const LargeObject& other) : data(other.data) {
                std::cout << "LargeObject copy constructed" << std::endl;
            }
            
            LargeObject(LargeObject&& other) noexcept : data(std::move(other.data)) {
                std::cout << "LargeObject move constructed" << std::endl;
            }
            
            ~LargeObject() {
                std::cout << "LargeObject destroyed" << std::endl;
            }
        };
        
        std::vector<LargeObject> objects;
        
        std::cout << "Creating objects with emplace_back:" << std::endl;
        objects.emplace_back();  // Constructs in place
        
        std::cout << "\nMoving objects:" << std::endl;
        LargeObject obj;
        objects.push_back(std::move(obj));  // Uses move constructor
    }
    
    // Cache-friendly data access
    static void demonstrateCacheOptimization() {
        std::cout << "\n=== Cache Optimization ===" << std::endl;
        
        const int size = 10000;
        std::vector<std::vector<int>> matrix(size, std::vector<int>(size, 1));
        
        // Cache-friendly: Row-major access
        auto start = std::chrono::high_resolution_clock::now();
        long long sum1 = 0;
        for (int i = 0; i < size; i++) {
            for (int j = 0; j < size; j++) {
                sum1 += matrix[i][j];
            }
        }
        auto mid = std::chrono::high_resolution_clock::now();
        
        // Cache-unfriendly: Column-major access
        long long sum2 = 0;
        for (int i = 0; i < size; i++) {
            for (int j = 0; j < size; j++) {
                sum2 += matrix[j][i];
            }
        }
        auto end = std::chrono::high_resolution_clock::now();
        
        auto cacheFriendly = std::chrono::duration_cast<std::chrono::microseconds>(mid - start);
        auto cacheUnfriendly = std::chrono::duration_cast<std::chrono::microseconds>(end - mid);
        
        std::cout << "Cache-friendly access: " << cacheFriendly.count() << " μs" << std::endl;
        std::cout << "Cache-unfriendly access: " << cacheUnfriendly.count() << " μs" << std::endl;
        std::cout << "Speedup: " << static_cast<double>(cacheUnfriendly.count()) / cacheFriendly.count() << "x" << std::endl;
    }
};
```

## Code Quality and Maintainability

### SOLID Principles
```cpp
#include <iostream>
#include <vector>
#include <memory>

// Single Responsibility Principle
class Logger {
public:
    void log(const std::string& message) {
        std::cout << "[LOG]: " << message << std::endl;
    }
};

class DataStorage {
public:
    void save(const std::string& data) {
        std::cout << "Saving: " << data << std::endl;
    }
};

// Open/Closed Principle
class Shape {
public:
    virtual double area() const = 0;
    virtual ~Shape() = default;
};

class Circle : public Shape {
private:
    double radius;
    
public:
    Circle(double r) : radius(r) {}
    double area() const override {
        return 3.14159 * radius * radius;
    }
};

class Rectangle : public Shape {
private:
    double width, height;
    
public:
    Rectangle(double w, double h) : width(w), height(h) {}
    double area() const override {
        return width * height;
    }
};

// Liskov Substitution Principle
class Bird {
public:
    virtual void fly() {
        std::cout << "Bird is flying" << std::endl;
    }
    virtual ~Bird() = default;
};

class Sparrow : public Bird {
public:
    void fly() override {
        std::cout << "Sparrow is flying" << std::endl;
    }
};

class Ostrich : public Bird {
public:
    void fly() override {
        std::cout << "Ostrich cannot fly" << std::endl;
    }
};

// Interface Segregation Principle
class Printer {
public:
    virtual void print() = 0;
    virtual ~Printer() = default;
};

class Scanner {
public:
    virtual void scan() = 0;
    virtual ~Scanner() = default;
};

class MultiFunctionDevice : public Printer, public Scanner {
public:
    void print() override {
        std::cout << "Printing document" << std::endl;
    }
    
    void scan() override {
        std::cout << "Scanning document" << std::endl;
    }
};

// Dependency Inversion Principle
class INotificationService {
public:
    virtual void send(const std::string& message) = 0;
    virtual ~INotificationService() = default;
};

class EmailService : public INotificationService {
public:
    void send(const std::string& message) override {
        std::cout << "Email sent: " << message << std::endl;
    }
};

class SMSService : public INotificationService {
public:
    void send(const std::string& message) override {
        std::cout << "SMS sent: " << message << std::endl;
    }
};

class NotificationManager {
private:
    std::unique_ptr<INotificationService> service;
    
public:
    NotificationManager(std::unique_ptr<INotificationService> svc) 
        : service(std::move(svc)) {}
    
    void notify(const std::string& message) {
        service->send(message);
    }
};

void demonstrateSOLIDPrinciples() {
    std::cout << "=== SOLID Principles ===" << std::endl;
    
    // Single Responsibility
    Logger logger;
    DataStorage storage;
    logger.log("Application started");
    storage.save("Important data");
    
    // Open/Closed
    std::vector<std::unique_ptr<Shape>> shapes;
    shapes.push_back(std::make_unique<Circle>(5.0));
    shapes.push_back(std::make_unique<Rectangle>(4.0, 6.0));
    
    for (const auto& shape : shapes) {
        std::cout << "Shape area: " << shape->area() << std::endl;
    }
    
    // Liskov Substitution
    std::vector<std::unique_ptr<Bird>> birds;
    birds.push_back(std::make_unique<Sparrow>());
    birds.push_back(std::make_unique<Ostrich>());
    
    for (const auto& bird : birds) {
        bird->fly();
    }
    
    // Dependency Inversion
    auto emailManager = std::make_unique<NotificationManager>(
        std::make_unique<EmailService>());
    emailManager->notify("Hello via Email!");
    
    auto smsManager = std::make_unique<NotificationManager>(
        std::make_unique<SMSService>());
    smsManager->notify("Hello via SMS!");
}
```

## Build Systems and Project Organization

### CMake Example
```cmake
# CMakeLists.txt
cmake_minimum_required(VERSION 3.16)
project(ModernCppExamples VERSION 1.0.0 LANGUAGES CXX)

# Set C++ standard
set(CMAKE_CXX_STANDARD 20)
set(CMAKE_CXX_STANDARD_REQUIRED ON)

# Set build type
if(NOT CMAKE_BUILD_TYPE)
    set(CMAKE_BUILD_TYPE Release)
endif()

# Compiler-specific options
if(MSVC)
    add_compile_options(/W4 /permissive-)
else()
    add_compile_options(-Wall -Wextra -Wpedantic)
endif()

# Find required packages
find_package(Threads REQUIRED)

# Include directories
include_directories(include)

# Source files
set(SOURCES
    src/main.cpp
    src/performance.cpp
    src/solid.cpp
)

# Create executable
add_executable(${PROJECT_NAME} ${SOURCES})

# Link libraries
target_link_libraries(${PROJECT_NAME} PRIVATE Threads::Threads)

# Install rules
install(TARGETS ${PROJECT_NAME}
    RUNTIME DESTINATION bin
    LIBRARY DESTINATION lib
    ARCHIVE DESTINATION lib
)

# Testing
enable_testing()
add_subdirectory(tests)
```

### Modern CMake with FetchContent
```cmake
# CMakeLists.txt with external dependencies
cmake_minimum_required(VERSION 3.16)
project(ModernCppProject VERSION 1.0.0 LANGUAGES CXX)

# Include FetchContent module
include(FetchContent)

# Fetch external dependencies
FetchContent_Declare(
    fmt
    GIT_REPOSITORY https://github.com/fmtlib/fmt.git
    GIT_TAG 10.1.1
)

FetchContent_MakeAvailable(fmt)

# Set C++ standard
set(CMAKE_CXX_STANDARD 20)
set(CMAKE_CXX_STANDARD_REQUIRED ON)

# Create library
add_library(mylib
    src/library.cpp
    src/utils.cpp
)

target_include_directories(mylib PUBLIC
    $<BUILD_INTERFACE:${CMAKE_CURRENT_SOURCE_DIR}/include>
    $<INSTALL_INTERFACE:include>
)

# Link external dependencies
target_link_libraries(mylib PUBLIC fmt::fmt)

# Create executable
add_executable(myapp
    src/main.cpp
)

target_link_libraries(myapp PRIVATE mylib)

# Installation
install(TARGETS mylib myapp
    EXPORT MyProjectTargets
    LIBRARY DESTINATION lib
    ARCHIVE DESTINATION lib
    RUNTIME DESTINATION bin
)

install(DIRECTORY include/ DESTINATION include)
```

## Debugging and Profiling

### Debugging Techniques
```cpp
#include <iostream>
#include <cassert>
#include <stdexcept>
#include <source_location>

class DebuggingTechniques {
public:
    // Assertions
    static void demonstrateAssertions() {
        std::cout << "=== Assertions ===" << std::endl;
        
        int x = 5;
        assert(x > 0 && "x must be positive");
        
        // Custom assertion
        #define ASSERT(condition, message) \
            do { \
                if (!(condition)) { \
                    std::cerr << "Assertion failed: " << message \
                              << " at " << __FILE__ << ":" << __LINE__ << std::endl; \
                    std::abort(); \
                } \
            } while(0)
        
        ASSERT(x == 5, "x should be 5");
    }
    
    // Exception handling with source location (C++20)
    static void demonstrateSourceLocation() {
        std::cout << "\n=== Source Location ===" << std::endl;
        
        auto trace = [](const std::source_location& location = std::source_location::current()) {
            std::cout << "Function: " << location.function_name() << std::endl;
            std::cout << "File: " << location.file_name() << std::endl;
            std::cout << "Line: " << location.line() << std::endl;
            std::cout << "Column: " << location.column() << std::endl;
        };
        
        trace();
    }
    
    // Logging system
    enum class LogLevel { DEBUG, INFO, WARNING, ERROR };
    
    class Logger {
    private:
        static void log(LogLevel level, const std::string& message, 
                     const std::source_location& location = std::source_location::current()) {
            const char* levelStr[] = {"DEBUG", "INFO", "WARNING", "ERROR"};
            std::cout << "[" << levelStr[static_cast<int>(level)] << "] "
                      << location.file_name() << ":" << location.line() << " "
                      << location.function_name() << "() - " << message << std::endl;
        }
        
    public:
        static void debug(const std::string& message) {
            log(LogLevel::DEBUG, message);
        }
        
        static void info(const std::string& message) {
            log(LogLevel::INFO, message);
        }
        
        static void warning(const std::string& message) {
            log(LogLevel::WARNING, message);
        }
        
        static void error(const std::string& message) {
            log(LogLevel::ERROR, message);
        }
    };
    
    static void demonstrateLogging() {
        std::cout << "\n=== Logging System ===" << std::endl;
        
        Logger::debug("Debug message");
        Logger::info("Info message");
        Logger::warning("Warning message");
        Logger::error("Error message");
    }
};
```

## Complete Example: Modern C++ Application

### Task Management System
```cpp
#include <iostream>
#include <vector>
#include <memory>
#include <string>
#include <optional>
#include <format>
#include <chrono>
#include <algorithm>
#include <ranges>
#include <fstream>
#include <nlohmann/json.hpp> // JSON library (would need to be included)

// Modern C++ Task class
class Task {
private:
    std::string title;
    std::string description;
    std::chrono::system_clock::time_point dueDate;
    bool completed;
    int priority; // 1-5, 1 is highest
    
public:
    Task(std::string t, std::string desc, 
         std::chrono::system_clock::time_point due, int prio)
        : title(std::move(t)), description(std::move(desc)), 
          dueDate(due), completed(false), priority(prio) {}
    
    // Getters
    const std::string& getTitle() const { return title; }
    const std::string& getDescription() const { return description; }
    auto getDueDate() const { return dueDate; }
    bool isCompleted() const { return completed; }
    int getPriority() const { return priority; }
    
    // Setters
    void setCompleted(bool comp) { completed = comp; }
    void setPriority(int prio) { priority = prio; }
    
    // Utility methods
    std::string toString() const {
        auto timeT = std::chrono::system_clock::to_time_t(dueDate);
        return std::format("Task: {} (Priority: {}, Due: {}, Status: {})",
                          title, priority, std::ctime(&timeT),
                          completed ? "Completed" : "Pending");
    }
    
    // Serialization
    nlohmann::json toJSON() const {
        return nlohmann::json{
            {"title", title},
            {"description", description},
            {"dueDate", std::chrono::system_clock::to_time_t(dueDate)},
            {"completed", completed},
            {"priority", priority}
        };
    }
    
    static Task fromJSON(const nlohmann::json& j) {
        auto timePoint = std::chrono::system_clock::from_time_t(j["dueDate"].get<std::time_t>());
        return Task(j["title"], j["description"], timePoint, j["priority"]);
    }
};

// Modern Task Manager
class TaskManager {
private:
    std::vector<std::unique_ptr<Task>> tasks;
    std::string dataFile;
    
public:
    explicit TaskManager(const std::string& filename = "tasks.json") 
        : dataFile(filename) {
        loadTasks();
    }
    
    ~TaskManager() {
        saveTasks();
    }
    
    // CRUD operations
    void addTask(std::unique_ptr<Task> task) {
        tasks.push_back(std::move(task));
        std::cout << "Task added successfully!" << std::endl;
    }
    
    std::optional<Task*> findTask(const std::string& title) {
        auto it = std::find_if(tasks.begin(), tasks.end(),
            [&title](const auto& task) {
                return task->getTitle() == title;
            });
        
        if (it != tasks.end()) {
            return it->get();
        }
        return std::nullopt;
    }
    
    bool completeTask(const std::string& title) {
        if (auto task = findTask(title)) {
            (*task)->setCompleted(true);
            std::cout << "Task marked as completed!" << std::endl;
            return true;
        }
        std::cout << "Task not found!" << std::endl;
        return false;
    }
    
    bool removeTask(const std::string& title) {
        auto it = std::remove_if(tasks.begin(), tasks.end(),
            [&title](const auto& task) {
                return task->getTitle() == title;
            });
        
        if (it != tasks.end()) {
            tasks.erase(it, tasks.end());
            std::cout << "Task removed successfully!" << std::endl;
            return true;
        }
        std::cout << "Task not found!" << std::endl;
        return false;
    }
    
    // Query operations using ranges
    void displayTasks() const {
        std::cout << "\n=== All Tasks ===" << std::endl;
        
        auto taskViews = tasks | std::views::transform([](const auto& task) {
            return task->toString();
        });
        
        for (const auto& taskStr : taskViews) {
            std::cout << taskStr << std::endl;
        }
    }
    
    void displayPendingTasks() const {
        std::cout << "\n=== Pending Tasks ===" << std::endl;
        
        auto pendingTasks = tasks | 
            std::views::filter([](const auto& task) {
                return !task->isCompleted();
            }) |
            std::views::transform([](const auto& task) {
                return task->toString();
            });
        
        for (const auto& taskStr : pendingTasks) {
            std::cout << taskStr << std::endl;
        }
    }
    
    void displayHighPriorityTasks() const {
        std::cout << "\n=== High Priority Tasks ===" << std::endl;
        
        auto highPriorityTasks = tasks |
            std::views::filter([](const auto& task) {
                return !task->isCompleted() && task->getPriority() <= 2;
            }) |
            std::views::transform([](const auto& task) {
                return task->toString();
            });
        
        for (const auto& taskStr : highPriorityTasks) {
            std::cout << taskStr << std::endl;
        }
    }
    
    // Statistics
    void displayStatistics() const {
        std::cout << "\n=== Task Statistics ===" << std::endl;
        
        auto totalTasks = tasks.size();
        auto completedTasks = std::count_if(tasks.begin(), tasks.end(),
            [](const auto& task) { return task->isCompleted(); });
        
        std::cout << "Total tasks: " << totalTasks << std::endl;
        std::cout << "Completed tasks: " << completedTasks << std::endl;
        std::cout << "Pending tasks: " << (totalTasks - completedTasks) << std::endl;
        std::cout << "Completion rate: " 
                  << (totalTasks > 0 ? (100.0 * completedTasks / totalTasks) : 0.0) 
                  << "%" << std::endl;
    }
    
private:
    void saveTasks() const {
        nlohmann::json j = nlohmann::json::array();
        
        for (const auto& task : tasks) {
            j.push_back(task->toJSON());
        }
        
        std::ofstream file(dataFile);
        file << j.dump(4);
    }
    
    void loadTasks() {
        std::ifstream file(dataFile);
        if (!file.is_open()) {
            std::cout << "No existing task file found. Starting fresh." << std::endl;
            return;
        }
        
        try {
            nlohmann::json j;
            file >> j;
            
            for (const auto& taskJson : j) {
                tasks.push_back(std::make_unique<Task>(Task::fromJSON(taskJson)));
            }
            
            std::cout << "Loaded " << tasks.size() << " tasks from file." << std::endl;
        } catch (const std::exception& e) {
            std::cout << "Error loading tasks: " << e.what() << std::endl;
        }
    }
};

// Interactive menu
void runTaskManager() {
    TaskManager manager;
    
    while (true) {
        std::cout << "\n=== Task Manager Menu ===" << std::endl;
        std::cout << "1. Add Task" << std::endl;
        std::cout << "2. Complete Task" << std::endl;
        std::cout << "3. Remove Task" << std::endl;
        std::cout << "4. Display All Tasks" << std::endl;
        std::cout << "5. Display Pending Tasks" << std::endl;
        std::cout << "6. Display High Priority Tasks" << std::endl;
        std::cout << "7. Display Statistics" << std::endl;
        std::cout << "8. Exit" << std::endl;
        std::cout << "Enter choice: ";
        
        int choice;
        std::cin >> choice;
        std::cin.ignore(); // Clear newline
        
        switch (choice) {
            case 1: {
                std::string title, description;
                int priority, daysUntilDue;
                
                std::cout << "Enter task title: ";
                std::getline(std::cin, title);
                
                std::cout << "Enter description: ";
                std::getline(std::cin, description);
                
                std::cout << "Enter priority (1-5, 1 is highest): ";
                std::cin >> priority;
                
                std::cout << "Enter days until due: ";
                std::cin >> daysUntilDue;
                
                auto dueDate = std::chrono::system_clock::now() + 
                              std::chrono::hours(24 * daysUntilDue);
                
                manager.addTask(std::make_unique<Task>(title, description, dueDate, priority));
                break;
            }
            case 2: {
                std::string title;
                std::cout << "Enter task title to complete: ";
                std::getline(std::cin, title);
                manager.completeTask(title);
                break;
            }
            case 3: {
                std::string title;
                std::cout << "Enter task title to remove: ";
                std::getline(std::cin, title);
                manager.removeTask(title);
                break;
            }
            case 4:
                manager.displayTasks();
                break;
            case 5:
                manager.displayPendingTasks();
                break;
            case 6:
                manager.displayHighPriorityTasks();
                break;
            case 7:
                manager.displayStatistics();
                break;
            case 8:
                std::cout << "Goodbye!" << std::endl;
                return;
            default:
                std::cout << "Invalid choice. Please try again." << std::endl;
        }
    }
}

int main() {
    std::cout << "=== Modern C++ Best Practices ===" << std::endl;
    
    // Demonstrate modern C++ features
    demonstrateCpp11Features();
    demonstrateCpp14Features();
    demonstrateCpp17Features();
    demonstrateCpp20Features();
    
    // Performance optimization
    PerformanceOptimization::demonstrateVectorOptimization();
    PerformanceOptimization::demonstrateMoveSemantics();
    PerformanceOptimization::demonstrateCacheOptimization();
    
    // SOLID principles
    demonstrateSOLIDPrinciples();
    
    // Debugging techniques
    DebuggingTechniques::demonstrateAssertions();
    DebuggingTechniques::demonstrateSourceLocation();
    DebuggingTechniques::demonstrateLogging();
    
    // Run the task manager application
    std::cout << "\n=== Starting Task Manager Application ===" << std::endl;
    runTaskManager();
    
    return 0;
}
```

## Practice Exercises

### Exercise 1: Modernize Legacy Code
Take existing C++98 code and modernize it:
- Replace raw pointers with smart pointers
- Use range-based for loops
- Implement move semantics
- Add constexpr where appropriate

### Exercise 2: Performance Optimization
Optimize a computationally intensive algorithm:
- Profile the bottlenecks
- Apply cache-friendly optimizations
- Use parallel algorithms
- Measure performance improvements

### Exercise 3: Build System Setup
Create a complete build system:
- CMake configuration
- External dependency management
- Cross-platform compilation
- Testing and integration

### Exercise 4: Code Quality Tools
Implement code quality improvements:
- Static analysis integration
- Code coverage measurement
- Documentation generation
- Continuous integration setup

## Best Practices Summary

### Code Style Guidelines
- Use consistent naming conventions
- Keep functions small and focused
- Prefer const correctness
- Use RAII for resource management
- Write self-documenting code

### Performance Guidelines
- Profile before optimizing
- Use appropriate data structures
- Minimize memory allocations
- Leverage cache locality
- Use move semantics when possible

### Safety Guidelines
- Prefer smart pointers over raw pointers
- Use strong typing
- Validate inputs and outputs
- Handle exceptions appropriately
- Avoid undefined behavior

### Maintainability Guidelines
- Follow SOLID principles
- Write comprehensive tests
- Use modern C++ features
- Keep dependencies minimal
- Document complex logic

## Key Takeaways
- Modern C++ provides powerful abstractions
- Performance requires careful design choices
- Code quality is essential for maintainability
- Build systems automate complex processes
- Debugging tools help identify issues quickly
- Best practices evolve with language features
- Continuous learning is crucial for C++ developers

## Course Completion
Congratulations! You've completed the comprehensive C++ learning course from beginner to advanced. You now have the knowledge and skills to:

- Write efficient and modern C++ code
- Design scalable and maintainable software
- Use advanced C++ features effectively
- Optimize performance-critical applications
- Build robust and error-safe programs
- Work with complex systems and architectures

Continue practicing and exploring new C++ features as they are released. Happy coding!