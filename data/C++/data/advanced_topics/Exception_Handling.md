# Module 12: Exception Handling and Error Management

## Learning Objectives
- Understand the fundamentals of exception handling in C++
- Master try, catch, and throw statements
- Learn about standard exception classes
- Understand custom exception creation
- Master exception safety and RAII
- Learn about error handling best practices

## Introduction to Exception Handling

Exception handling provides a structured way to handle errors and exceptional situations in C++ programs. It separates error detection from error handling and makes code more readable and maintainable.

### Basic Exception Handling Syntax
```cpp
#include <iostream>
#include <stdexcept>

double divide(double a, double b) {
    if (b == 0.0) {
        throw std::runtime_error("Division by zero!");
    }
    return a / b;
}

int main() {
    try {
        double result = divide(10.0, 2.0);
        std::cout << "10 / 2 = " << result << std::endl;
        
        result = divide(10.0, 0.0);  // This will throw an exception
        std::cout << "10 / 0 = " << result << std::endl;
    }
    catch (const std::runtime_error& e) {
        std::cerr << "Error: " << e.what() << std::endl;
    }
    
    std::cout << "Program continues after exception handling." << std::endl;
    
    return 0;
}
```

## Standard Exception Hierarchy

### Built-in Exception Classes
```cpp
#include <iostream>
#include <stdexcept>
#include <vector>
#include <string>

void demonstrateStandardExceptions() {
    std::cout << "=== Standard Exception Examples ===" << std::endl;
    
    // std::logic_error - logic errors detectable before runtime
    try {
        throw std::logic_error("Logic error occurred");
    }
    catch (const std::logic_error& e) {
        std::cout << "Logic error: " << e.what() << std::endl;
    }
    
    // std::runtime_error - runtime errors
    try {
        throw std::runtime_error("Runtime error occurred");
    }
    catch (const std::runtime_error& e) {
        std::cout << "Runtime error: " << e.what() << std::endl;
    }
    
    // std::invalid_argument - invalid argument
    try {
        throw std::invalid_argument("Invalid argument provided");
    }
    catch (const std::invalid_argument& e) {
        std::cout << "Invalid argument: " << e.what() << std::endl;
    }
    
    // std::out_of_range - out of range access
    try {
        std::vector<int> vec = {1, 2, 3};
        int value = vec.at(10);  // Throws std::out_of_range
    }
    catch (const std::out_of_range& e) {
        std::cout << "Out of range: " << e.what() << std::endl;
    }
    
    // std::bad_alloc - memory allocation failure
    try {
        // Try to allocate a very large amount of memory
        int* hugeArray = new int[1000000000000];
        delete[] hugeArray;
    }
    catch (const std::bad_alloc& e) {
        std::cout << "Bad allocation: " << e.what() << std::endl;
    }
}

int main() {
    demonstrateStandardExceptions();
    return 0;
}
```

## Multiple Catch Blocks

### Handling Different Exception Types
```cpp
#include <iostream>
#include <stdexcept>
#include <string>
#include <vector>

class CustomException : public std::exception {
private:
    std::string message;
    
public:
    CustomException(const std::string& msg) : message(msg) {}
    
    const char* what() const noexcept override {
        return message.c_str();
    }
};

void riskyFunction(int choice) {
    switch (choice) {
        case 1:
            throw std::invalid_argument("Invalid argument provided");
        case 2:
            throw std::out_of_range("Index out of range");
        case 3:
            throw CustomException("Custom exception occurred");
        case 4:
            throw "C-style string exception";
        case 5:
            throw 42;  // Integer exception
        default:
            std::cout << "No exception thrown" << std::endl;
    }
}

int main() {
    for (int i = 1; i <= 6; i++) {
        try {
            std::cout << "\nTest case " << i << ": ";
            riskyFunction(i);
        }
        catch (const std::invalid_argument& e) {
            std::cout << "Caught invalid_argument: " << e.what() << std::endl;
        }
        catch (const std::out_of_range& e) {
            std::cout << "Caught out_of_range: " << e.what() << std::endl;
        }
        catch (const CustomException& e) {
            std::cout << "Caught CustomException: " << e.what() << std::endl;
        }
        catch (const std::exception& e) {
            std::cout << "Caught standard exception: " << e.what() << std::endl;
        }
        catch (const char* e) {
            std::cout << "Caught C-style string: " << e << std::endl;
        }
        catch (...) {
            std::cout << "Caught unknown exception" << std::endl;
        }
    }
    
    return 0;
}
```

## Custom Exception Classes

### Creating Your Own Exceptions
```cpp
#include <iostream>
#include <string>
#include <exception>

// Base custom exception
class BankingException : public std::exception {
protected:
    std::string message;
    
public:
    BankingException(const std::string& msg) : message(msg) {}
    virtual ~BankingException() noexcept = default;
    
    const char* what() const noexcept override {
        return message.c_str();
    }
};

// Specific banking exceptions
class InsufficientFundsException : public BankingException {
private:
    double requestedAmount;
    double availableBalance;
    
public:
    InsufficientFundsException(double requested, double available)
        : BankingException("Insufficient funds"), 
          requestedAmount(requested), 
          availableBalance(available) {
        message += ": Requested $" + std::to_string(requestedAmount) + 
                  ", Available $" + std::to_string(availableBalance);
    }
    
    double getRequestedAmount() const { return requestedAmount; }
    double getAvailableBalance() const { return availableBalance; }
};

class AccountNotFoundException : public BankingException {
private:
    std::string accountNumber;
    
public:
    AccountNotFoundException(const std::string& accNum)
        : BankingException("Account not found"), accountNumber(accNum) {
        message += ": " + accountNumber;
    }
    
    const std::string& getAccountNumber() const { return accountNumber; }
};

class InvalidAmountException : public BankingException {
private:
    double amount;
    
public:
    InvalidAmountException(double amt)
        : BankingException("Invalid amount"), amount(amt) {
        message += ": " + std::to_string(amount);
    }
    
    double getAmount() const { return amount; }
};

// Bank account class that uses custom exceptions
class BankAccount {
private:
    std::string accountNumber;
    std::string ownerName;
    double balance;
    
public:
    BankAccount(const std::string& accNum, const std::string& owner, double initialBalance = 0.0)
        : accountNumber(accNum), ownerName(owner), balance(initialBalance) {
        if (initialBalance < 0) {
            throw InvalidAmountException(initialBalance);
        }
    }
    
    void deposit(double amount) {
        if (amount <= 0) {
            throw InvalidAmountException(amount);
        }
        balance += amount;
    }
    
    void withdraw(double amount) {
        if (amount <= 0) {
            throw InvalidAmountException(amount);
        }
        if (amount > balance) {
            throw InsufficientFundsException(amount, balance);
        }
        balance -= amount;
    }
    
    double getBalance() const { return balance; }
    const std::string& getAccountNumber() const { return accountNumber; }
    const std::string& getOwnerName() const { return ownerName; }
};

// Bank class that manages accounts
class Bank {
private:
    std::vector<BankAccount> accounts;
    
public:
    void addAccount(const BankAccount& account) {
        accounts.push_back(account);
    }
    
    BankAccount& findAccount(const std::string& accountNumber) {
        for (auto& account : accounts) {
            if (account.getAccountNumber() == accountNumber) {
                return account;
            }
        }
        throw AccountNotFoundException(accountNumber);
    }
    
    void transferMoney(const std::string& fromAccount, const std::string& toAccount, double amount) {
        try {
            BankAccount& from = findAccount(fromAccount);
            BankAccount& to = findAccount(toAccount);
            
            from.withdraw(amount);
            to.deposit(amount);
            
            std::cout << "Transfer successful: $" << amount 
                      << " from " << fromAccount << " to " << toAccount << std::endl;
        }
        catch (const BankingException& e) {
            std::cout << "Transfer failed: " << e.what() << std::endl;
            throw;  // Re-throw the exception
        }
    }
};

int main() {
    Bank bank;
    
    // Create accounts
    try {
        bank.addAccount(BankAccount("12345", "Alice Johnson", 1000.0));
        bank.addAccount(BankAccount("67890", "Bob Smith", 500.0));
        
        // Test successful operations
        std::cout << "=== Successful Operations ===" << std::endl;
        bank.findAccount("12345").deposit(200.0);
        std::cout << "Alice's balance: $" << bank.findAccount("12345").getBalance() << std::endl;
        
        bank.findAccount("12345").withdraw(100.0);
        std::cout << "Alice's balance after withdrawal: $" << bank.findAccount("12345").getBalance() << std::endl;
        
        // Test transfer
        bank.transferMoney("12345", "67890", 300.0);
        std::cout << "Alice's balance: $" << bank.findAccount("12345").getBalance() << std::endl;
        std::cout << "Bob's balance: $" << bank.findAccount("67890").getBalance() << std::endl;
        
    }
    catch (const BankingException& e) {
        std::cout << "Banking error: " << e.what() << std::endl;
    }
    
    // Test exception scenarios
    std::cout << "\n=== Exception Scenarios ===" << std::endl;
    
    // Test insufficient funds
    try {
        bank.findAccount("67890").withdraw(1000.0);
    }
    catch (const InsufficientFundsException& e) {
        std::cout << "Insufficient funds error: " << e.what() << std::endl;
        std::cout << "Requested: $" << e.getRequestedAmount() 
                  << ", Available: $" << e.getAvailableBalance() << std::endl;
    }
    
    // Test account not found
    try {
        bank.findAccount("99999");
    }
    catch (const AccountNotFoundException& e) {
        std::cout << "Account not found: " << e.what() << std::endl;
        std::cout << "Missing account: " << e.getAccountNumber() << std::endl;
    }
    
    // Test invalid amount
    try {
        bank.findAccount("12345").deposit(-100.0);
    }
    catch (const InvalidAmountException& e) {
        std::cout << "Invalid amount: " << e.what() << std::endl;
        std::cout << "Amount: $" << e.getAmount() << std::endl;
    }
    
    return 0;
}
```

## Exception Safety and RAII

### Resource Acquisition Is Initialization
```cpp
#include <iostream>
#include <memory>
#include <fstream>
#include <vector>

class FileHandler {
private:
    std::unique_ptr<std::ofstream> file;
    
public:
    FileHandler(const std::string& filename) {
        file = std::make_unique<std::ofstream>(filename);
        if (!file->is_open()) {
            throw std::runtime_error("Failed to open file: " + filename);
        }
        std::cout << "File opened: " << filename << std::endl;
    }
    
    ~FileHandler() {
        if (file && file->is_open()) {
            file->close();
            std::cout << "File closed automatically" << std::endl;
        }
    }
    
    void write(const std::string& content) {
        if (file && file->is_open()) {
            *file << content << std::endl;
        }
    }
    
    // Prevent copying
    FileHandler(const FileHandler&) = delete;
    FileHandler& operator=(const FileHandler&) = delete;
    
    // Allow moving
    FileHandler(FileHandler&&) = default;
    FileHandler& operator=(FileHandler&&) = default;
};

class DatabaseConnection {
private:
    bool connected;
    std::string connectionString;
    
public:
    DatabaseConnection(const std::string& connStr) : connectionString(connStr) {
        // Simulate connection
        connected = true;
        std::cout << "Database connected: " << connectionString << std::endl;
    }
    
    ~DatabaseConnection() {
        if (connected) {
            // Simulate disconnection
            connected = false;
            std::cout << "Database disconnected automatically" << std::endl;
        }
    }
    
    void executeQuery(const std::string& query) {
        if (!connected) {
            throw std::runtime_error("Database not connected");
        }
        std::cout << "Executing query: " << query << std::endl;
    }
    
    // Prevent copying
    DatabaseConnection(const DatabaseConnection&) = delete;
    DatabaseConnection& operator=(const DatabaseConnection&) = delete;
};

void demonstrateRAII() {
    std::cout << "=== RAII Demonstration ===" << std::endl;
    
    try {
        FileHandler file("example.txt");
        DatabaseConnection db("localhost:5432");
        
        file.write("Hello, RAII!");
        db.executeQuery("SELECT * FROM users");
        
        // Simulate an error
        throw std::runtime_error("Something went wrong!");
    }
    catch (const std::exception& e) {
        std::cout << "Exception caught: " << e.what() << std::endl;
        std::cout << "Resources are automatically cleaned up" << std::endl;
    }
}

// Smart pointer examples
void demonstrateSmartPointers() {
    std::cout << "\n=== Smart Pointers ===" << std::endl;
    
    // unique_ptr - exclusive ownership
    {
        std::unique_ptr<int> ptr1 = std::make_unique<int>(42);
        std::cout << "unique_ptr value: " << *ptr1 << std::endl;
        
        // Transfer ownership
        std::unique_ptr<int> ptr2 = std::move(ptr1);
        // ptr1 is now nullptr
        if (!ptr1) {
            std::cout << "ptr1 is null after move" << std::endl;
        }
        std::cout << "ptr2 value: " << *ptr2 << std::endl;
    } // ptr2 is automatically deleted here
    
    // shared_ptr - shared ownership
    {
        std::shared_ptr<int> shared1 = std::make_shared<int>(100);
        std::cout << "shared1 use count: " << shared1.use_count() << std::endl;
        
        {
            std::shared_ptr<int> shared2 = shared1;
            std::cout << "shared1 use count: " << shared1.use_count() << std::endl;
            std::cout << "shared2 use count: " << shared2.use_count() << std::endl;
        } // shared2 goes out of scope
        
        std::cout << "shared1 use count after shared2 destruction: " 
                  << shared1.use_count() << std::endl;
    } // shared1 is automatically deleted here
    
    // weak_ptr - non-owning reference
    {
        std::shared_ptr<int> shared = std::make_shared<int>(200);
        std::weak_ptr<int> weak = shared;
        
        std::cout << "shared use count: " << shared.use_count() << std::endl;
        
        if (auto locked = weak.lock()) {
            std::cout << "Weak pointer locked, value: " << *locked << std::endl;
        }
        
        shared.reset();
        
        if (weak.expired()) {
            std::cout << "Weak pointer has expired" << std::endl;
        }
    }
}

int main() {
    demonstrateRAII();
    demonstrateSmartPointers();
    return 0;
}
```

## Exception Safety Levels

### Different Levels of Exception Safety
```cpp
#include <iostream>
#include <vector>
#include <algorithm>
#include <memory>

class UnsafeArray {
private:
    int* data;
    size_t size;
    
public:
    UnsafeArray(size_t s) : size(s) {
        data = new int[size];
        std::fill(data, data + size, 0);
    }
    
    ~UnsafeArray() {
        delete[] data;
    }
    
    // Not exception safe - can leak memory
    void unsafeResize(size_t newSize) {
        int* newData = new int[newSize];  // Might throw std::bad_alloc
        
        // If copy throws, newData is leaked
        std::copy(data, data + std::min(size, newSize), newData);
        
        delete[] data;
        data = newData;
        size = newSize;
    }
    
    // Basic exception safety - no leaks, but state may be invalid
    void basicExceptionSafeResize(size_t newSize) {
        int* newData = nullptr;
        try {
            newData = new int[newSize];
            std::fill(newData, newData + newSize, 0);
            std::copy(data, data + std::min(size, newSize), newData);
        }
        catch (...) {
            delete[] newData;  // Clean up on exception
            throw;  // Re-throw
        }
        
        delete[] data;
        data = newData;
        size = newSize;
    }
    
    // Strong exception safety - either succeeds or leaves object unchanged
    void strongExceptionSafeResize(size_t newSize) {
        std::unique_ptr<int[]> newData(new int[newSize]);
        std::fill(newData.get(), newData.get() + newSize, 0);
        std::copy(data, data + std::min(size, newSize), newData.get());
        
        // No exception can happen after this point
        delete[] data;
        data = newData.release();
        size = newSize;
    }
    
    int& operator[](size_t index) {
        return data[index];
    }
    
    size_t getSize() const { return size; }
};

// RAII wrapper for array
class SafeArray {
private:
    std::unique_ptr<int[]> data;
    size_t size;
    
public:
    SafeArray(size_t s) : size(s), data(std::make_unique<int[]>(s)) {
        std::fill(data.get(), data.get() + size, 0);
    }
    
    // Automatically exception safe due to RAII
    void resize(size_t newSize) {
        auto newData = std::make_unique<int[]>(newSize);
        std::fill(newData.get(), newData.get() + newSize, 0);
        std::copy(data.get(), data.get() + std::min(size, newSize), newData.get());
        
        data = std::move(newData);
        size = newSize;
    }
    
    int& operator[](size_t index) {
        return data[index];
    }
    
    size_t getSize() const { return size; }
};

void demonstrateExceptionSafety() {
    std::cout << "=== Exception Safety Levels ===" << std::endl;
    
    // Demonstrate strong exception safety
    SafeArray arr(5);
    for (size_t i = 0; i < arr.getSize(); i++) {
        arr[i] = static_cast<int>(i * 10);
    }
    
    std::cout << "Original array: ";
    for (size_t i = 0; i < arr.getSize(); i++) {
        std::cout << arr[i] << " ";
    }
    std::cout << std::endl;
    
    try {
        arr.resize(8);  // Safe resize
        std::cout << "After resize to 8: ";
        for (size_t i = 0; i < arr.getSize(); i++) {
            std::cout << arr[i] << " ";
        }
        std::cout << std::endl;
    }
    catch (const std::exception& e) {
        std::cout << "Resize failed: " << e.what() << std::endl;
        std::cout << "Array remains in valid state" << std::endl;
    }
}

int main() {
    demonstrateExceptionSafety();
    return 0;
}
```

## noexcept Specification

### noexcept Keyword and Exception Guarantees
```cpp
#include <iostream>
#include <vector>
#include <string>

// Functions that don't throw exceptions
void safeFunction() noexcept {
    std::cout << "This function promises not to throw" << std::endl;
}

// Conditional noexcept
template <typename T>
void conditionalNoexcept(T value) noexcept(std::is_arithmetic_v<T>) {
    std::cout << "This function is noexcept for arithmetic types" << std::endl;
}

// noexcept operator
void testNoexcept() {
    std::cout << "safeFunction() is noexcept: " << noexcept(safeFunction()) << std::endl;
    std::cout << "conditionalNoexcept(42) is noexcept: " << noexcept(conditionalNoexcept(42)) << std::endl;
    std::cout << "conditionalNoexcept(std::string(\"hello\")) is noexcept: " 
              << noexcept(conditionalNoexcept(std::string("hello"))) << std::endl;
}

// Move operations should be noexcept when possible
class NoexceptMove {
private:
    std::vector<int> data;
    
public:
    NoexceptMove() = default;
    
    // Copy constructor
    NoexceptMove(const NoexceptMove& other) : data(other.data) {
        std::cout << "Copy constructor" << std::endl;
    }
    
    // Move constructor - noexcept for better performance
    NoexceptMove(NoexceptMove&& other) noexcept : data(std::move(other.data)) {
        std::cout << "Move constructor (noexcept)" << std::endl;
    }
    
    // Copy assignment
    NoexceptMove& operator=(const NoexceptMove& other) {
        data = other.data;
        std::cout << "Copy assignment" << std::endl;
        return *this;
    }
    
    // Move assignment - noexcept for better performance
    NoexceptMove& operator=(NoexceptMove&& other) noexcept {
        data = std::move(other.data);
        std::cout << "Move assignment (noexcept)" << std::endl;
        return *this;
    }
    
    void addValue(int value) {
        data.push_back(value);
    }
    
    void display() const {
        std::cout << "Data: ";
        for (int val : data) {
            std::cout << val << " ";
        }
        std::cout << std::endl;
    }
};

void demonstrateNoexceptMove() {
    std::cout << "\n=== noexcept Move Operations ===" << std::endl;
    
    std::vector<NoexceptMove> vec;
    
    std::cout << "Creating objects..." << std::endl;
    NoexceptMove obj1;
    obj1.addValue(1);
    obj1.addValue(2);
    
    NoexceptMove obj2;
    obj2.addValue(3);
    obj2.addValue(4);
    
    std::cout << "\nAdding to vector (triggers moves)..." << std::endl;
    vec.push_back(std::move(obj1));
    vec.push_back(std::move(obj2));
    
    std::cout << "\nVector contents:" << std::endl;
    for (const auto& obj : vec) {
        obj.display();
    }
}

int main() {
    testNoexcept();
    demonstrateNoexceptMove();
    return 0;
}
```

## Complete Example: Robust File Processing System

```cpp
#include <iostream>
#include <fstream>
#include <string>
#include <vector>
#include <memory>
#include <stdexcept>
#include <algorithm>

// Custom exceptions for file processing
class FileProcessingException : public std::exception {
protected:
    std::string message;
    
public:
    FileProcessingException(const std::string& msg) : message(msg) {}
    const char* what() const noexcept override {
        return message.c_str();
    }
};

class FileNotFoundException : public FileProcessingException {
private:
    std::string filename;
    
public:
    FileNotFoundException(const std::string& file)
        : FileProcessingException("File not found"), filename(file) {
        message += ": " + filename;
    }
    
    const std::string& getFilename() const { return filename; }
};

class FileCorruptedException : public FileProcessingException {
private:
    std::string filename;
    size_t lineNumber;
    
public:
    FileCorruptedException(const std::string& file, size_t line)
        : FileProcessingException("File corrupted"), filename(file), lineNumber(line) {
        message += ": " + filename + " at line " + std::to_string(lineNumber);
    }
    
    const std::string& getFilename() const { return filename; }
    size_t getLineNumber() const { return lineNumber; }
};

class InvalidDataException : public FileProcessingException {
private:
    std::string data;
    
public:
    InvalidDataException(const std::string& badData)
        : FileProcessingException("Invalid data format"), data(badData) {
        message += ": " + data;
    }
    
    const std::string& getData() const { return data; }
};

// RAII File handler
class FileHandler {
private:
    std::unique_ptr<std::ifstream> file;
    std::string filename;
    
public:
    explicit FileHandler(const std::string& filename) : filename(filename) {
        file = std::make_unique<std::ifstream>(filename);
        if (!file->is_open()) {
            throw FileNotFoundException(filename);
        }
    }
    
    ~FileHandler() {
        if (file && file->is_open()) {
            file->close();
        }
    }
    
    std::ifstream& getFile() {
        if (!file || !file->is_open()) {
            throw FileProcessingException("File is not open");
        }
        return *file;
    }
    
    const std::string& getFilename() const { return filename; }
    
    // Prevent copying
    FileHandler(const FileHandler&) = delete;
    FileHandler& operator=(const FileHandler&) = delete;
    
    // Allow moving
    FileHandler(FileHandler&&) = default;
    FileHandler& operator=(FileHandler&&) = default;
};

// Data structure to hold processed records
struct DataRecord {
    int id;
    std::string name;
    double value;
    
    DataRecord(int i, const std::string& n, double v) : id(i), name(n), value(v) {}
    
    void display() const {
        std::cout << "ID: " << id << ", Name: " << name << ", Value: " << value << std::endl;
    }
};

// File processor class
class FileProcessor {
private:
    std::vector<DataRecord> records;
    
    DataRecord parseLine(const std::string& line, size_t lineNumber) {
        std::stringstream ss(line);
        std::string idStr, name, valueStr;
        
        if (!std::getline(ss, idStr, ',')) {
            throw InvalidDataException("Missing ID in line: " + line);
        }
        
        if (!std::getline(ss, name, ',')) {
            throw InvalidDataException("Missing name in line: " + line);
        }
        
        if (!std::getline(ss, valueStr)) {
            throw InvalidDataException("Missing value in line: " + line);
        }
        
        try {
            int id = std::stoi(idStr);
            double value = std::stod(valueStr);
            return DataRecord(id, name, value);
        }
        catch (const std::exception& e) {
            throw InvalidDataException("Invalid numeric data in line: " + line);
        }
    }
    
public:
    void processFile(const std::string& filename) {
        FileHandler fileHandler(filename);
        auto& file = fileHandler.getFile();
        
        std::string line;
        size_t lineNumber = 0;
        
        while (std::getline(file, line)) {
            lineNumber++;
            
            // Skip empty lines
            if (line.empty()) continue;
            
            try {
                DataRecord record = parseLine(line, lineNumber);
                records.push_back(record);
            }
            catch (const InvalidDataException& e) {
                throw FileCorruptedException(filename, lineNumber);
            }
        }
        
        std::cout << "Successfully processed " << records.size() 
                  << " records from " << filename << std::endl;
    }
    
    void displayRecords() const {
        std::cout << "\n=== Processed Records ===" << std::endl;
        for (const auto& record : records) {
            record.display();
        }
    }
    
    void sortById() {
        std::sort(records.begin(), records.end(), 
                 [](const DataRecord& a, const DataRecord& b) {
                     return a.id < b.id;
                 });
    }
    
    void sortByName() {
        std::sort(records.begin(), records.end(), 
                 [](const DataRecord& a, const DataRecord& b) {
                     return a.name < b.name;
                 });
    }
    
    void sortByValue() {
        std::sort(records.begin(), records.end(), 
                 [](const DataRecord& a, const DataRecord& b) {
                     return a.value < b.value;
                 });
    }
    
    std::vector<DataRecord> filterByValue(double minValue) const {
        std::vector<DataRecord> filtered;
        std::copy_if(records.begin(), records.end(), std::back_inserter(filtered),
                    [minValue](const DataRecord& record) {
                        return record.value >= minValue;
                    });
        return filtered;
    }
    
    double getTotalValue() const {
        return std::accumulate(records.begin(), records.end(), 0.0,
                             [](double sum, const DataRecord& record) {
                                 return sum + record.value;
                             });
    }
    
    double getAverageValue() const {
        if (records.empty()) return 0.0;
        return getTotalValue() / records.size();
    }
    
    size_t getRecordCount() const { return records.size(); }
    
    void clear() { records.clear(); }
};

// Utility function to create a test file
void createTestFile(const std::string& filename) {
    std::ofstream file(filename);
    if (file.is_open()) {
        file << "1,John Doe,100.50\n";
        file << "2,Jane Smith,200.75\n";
        file << "3,Bob Johnson,150.25\n";
        file << "4,Alice Brown,300.00\n";
        file << "5,Charlie Wilson,175.50\n";
        file.close();
        std::cout << "Test file created: " << filename << std::endl;
    }
}

int main() {
    try {
        std::cout << "=== Robust File Processing System ===" << std::endl;
        
        // Create test file
        const std::string testFile = "test_data.csv";
        createTestFile(testFile);
        
        // Process the file
        FileProcessor processor;
        processor.processFile(testFile);
        
        // Display records
        processor.displayRecords();
        
        // Calculate statistics
        std::cout << "\n=== Statistics ===" << std::endl;
        std::cout << "Total records: " << processor.getRecordCount() << std::endl;
        std::cout << "Total value: $" << processor.getTotalValue() << std::endl;
        std::cout << "Average value: $" << processor.getAverageValue() << std::endl;
        
        // Sort and display
        std::cout << "\n=== Sorted by Value ===" << std::endl;
        processor.sortByValue();
        processor.displayRecords();
        
        // Filter records
        std::cout << "\n=== Records with value >= 150 ===" << std::endl;
        auto filtered = processor.filterByValue(150.0);
        for (const auto& record : filtered) {
            record.display();
        }
        
        // Test exception handling with non-existent file
        std::cout << "\n=== Testing Exception Handling ===" << std::endl;
        try {
            FileProcessor errorProcessor;
            errorProcessor.processFile("nonexistent.csv");
        }
        catch (const FileNotFoundException& e) {
            std::cout << "Expected error: " << e.what() << std::endl;
        }
        
        // Test with corrupted file
        std::ofstream corruptedFile("corrupted.csv");
        corruptedFile << "1,Valid,100.0\n";
        corruptedFile << "invalid,line,here\n";
        corruptedFile << "3,Another,200.0\n";
        corruptedFile.close();
        
        try {
            FileProcessor errorProcessor;
            errorProcessor.processFile("corrupted.csv");
        }
        catch (const FileCorruptedException& e) {
            std::cout << "Expected error: " << e.what() << std::endl;
            std::cout << "File: " << e.getFilename() << ", Line: " << e.getLineNumber() << std::endl;
        }
        
    }
    catch (const FileProcessingException& e) {
        std::cerr << "File processing error: " << e.what() << std::endl;
        return 1;
    }
    catch (const std::exception& e) {
        std::cerr << "Unexpected error: " << e.what() << std::endl;
        return 1;
    }
    
    std::cout << "\nProgram completed successfully!" << std::endl;
    return 0;
}
```

## Practice Exercises

### Exercise 1: Exception-Safe Container
Create an exception-safe container class:
- Implement all exception safety levels
- Use RAII for resource management
- Provide strong exception guarantees
- Handle memory allocation failures

### Exercise 2: Custom Exception Hierarchy
Design a comprehensive exception system:
- Base exception class with context
- Multiple specialized exception types
- Exception chaining support
- Logging and debugging features

### Exercise 3: Smart Pointer Implementation
Implement your own smart pointers:
- Unique pointer with move semantics
- Shared pointer with reference counting
- Weak pointer for cycle breaking
- Exception safety guarantees

### Exercise 4: Error Recovery System
Build an error recovery framework:
- Automatic retry mechanisms
- Fallback strategies
- Error reporting and logging
- Configuration-based error handling

## Key Takeaways
- Exception handling separates error detection from error handling
- Use standard exception classes when possible
- Create custom exceptions for domain-specific errors
- RAII ensures automatic resource cleanup
- Exception safety has multiple levels (basic, strong, no-throw)
- Smart pointers prevent memory leaks
- noexcept improves performance and provides guarantees
- Always consider exception safety in class design

## Next Module
In the next module, we'll explore concurrency and multithreading in C++.