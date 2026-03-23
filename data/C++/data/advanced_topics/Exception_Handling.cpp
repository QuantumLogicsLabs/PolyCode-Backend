// Module 12: Exception Handling and Error Management - Real-Life Examples
// This file demonstrates practical applications of exception handling

#include <iostream>
#include <string>
#include <vector>
#include <memory>
#include <stdexcept>
#include <fstream>
#include <ctime>

// Example 1: Banking System with Custom Exceptions
class BankingException : public std::exception {
private:
    std::string message;
    int errorCode;
    
public:
    BankingException(const std::string& msg, int code) : message(msg), errorCode(code) {}
    
    const char* what() const noexcept override {
        return message.c_str();
    }
    
    int getErrorCode() const { return errorCode; }
};

class InsufficientFundsException : public BankingException {
private:
    double requestedAmount;
    double availableBalance;
    
public:
    InsufficientFundsException(double requested, double available)
        : BankingException("Insufficient funds", 1001), 
          requestedAmount(requested), availableBalance(available) {}
    
    double getRequestedAmount() const { return requestedAmount; }
    double getAvailableBalance() const { return availableBalance; }
};

class AccountNotFoundException : public BankingException {
private:
    std::string accountNumber;
    
public:
    AccountNotFoundException(const std::string& accNum)
        : BankingException("Account not found", 1002), accountNumber(accNum) {}
    
    const std::string& getAccountNumber() const { return accountNumber; }
};

class InvalidAmountException : public BankingException {
private:
    double amount;
    
public:
    InvalidAmountException(double amount)
        : BankingException("Invalid amount", 1003), amount(amount) {}
    
    double getAmount() const { return amount; }
};

class BankAccount {
private:
    std::string accountNumber;
    std::string accountHolder;
    double balance;
    
public:
    BankAccount(const std::string& accNum, const std::string& holder, double initialBalance)
        : accountNumber(accNum), accountHolder(holder), balance(initialBalance) {
        
        if (initialBalance < 0) {
            throw InvalidAmountException(initialBalance);
        }
    }
    
    void deposit(double amount) {
        if (amount <= 0) {
            throw InvalidAmountException(amount);
        }
        
        balance += amount;
        std::cout << "Deposited $" << amount << ". New balance: $" << balance << std::endl;
    }
    
    void withdraw(double amount) {
        if (amount <= 0) {
            throw InvalidAmountException(amount);
        }
        
        if (amount > balance) {
            throw InsufficientFundsException(amount, balance);
        }
        
        balance -= amount;
        std::cout << "Withdrew $" << amount << ". New balance: $" << balance << std::endl;
    }
    
    double getBalance() const { return balance; }
    std::string getAccountNumber() const { return accountNumber; }
    std::string getAccountHolder() const { return accountHolder; }
    
    void display() const {
        std::cout << "Account: " << accountNumber << std::endl;
        std::cout << "Holder: " << accountHolder << std::endl;
        std::cout << "Balance: $" << balance << std::endl;
    }
};

class Bank {
private:
    std::vector<std::unique_ptr<BankAccount>> accounts;
    
public:
    void createAccount(const std::string& accNum, const std::string& holder, double initialBalance) {
        // Check if account already exists
        for (const auto& account : accounts) {
            if (account->getAccountNumber() == accNum) {
                throw std::runtime_error("Account already exists");
            }
        }
        
        accounts.push_back(std::make_unique<BankAccount>(accNum, holder, initialBalance));
        std::cout << "Account created successfully" << std::endl;
    }
    
    BankAccount* findAccount(const std::string& accNum) {
        for (auto& account : accounts) {
            if (account->getAccountNumber() == accNum) {
                return account.get();
            }
        }
        throw AccountNotFoundException(accNum);
    }
    
    void transferMoney(const std::string& fromAcc, const std::string& toAcc, double amount) {
        try {
            BankAccount* fromAccount = findAccount(fromAcc);
            BankAccount* toAccount = findAccount(toAcc);
            
            fromAccount->withdraw(amount);
            toAccount->deposit(amount);
            
            std::cout << "Transfer completed: $" << amount 
                      << " from " << fromAcc << " to " << toAcc << std::endl;
        }
        catch (const BankingException& e) {
            std::cout << "Transfer failed: " << e.what() << std::endl;
            throw; // Re-throw to allow caller to handle
        }
    }
    
    void displayAllAccounts() const {
        std::cout << "\n=== All Bank Accounts ===" << std::endl;
        for (const auto& account : accounts) {
            account->display();
            std::cout << "---" << std::endl;
        }
    }
};

// Example 2: File Processing with Exception Handling
class FileProcessor {
private:
    std::string filename;
    
public:
    FileProcessor(const std::string& file) : filename(file) {}
    
    std::vector<std::string> readLines() {
        std::ifstream file(filename);
        
        if (!file.is_open()) {
            throw std::runtime_error("Cannot open file: " + filename);
        }
        
        std::vector<std::string> lines;
        std::string line;
        
        while (std::getline(file, line)) {
            lines.push_back(line);
        }
        
        if (lines.empty()) {
            throw std::runtime_error("File is empty: " + filename);
        }
        
        return lines;
    }
    
    void writeLines(const std::vector<std::string>& lines) {
        std::ofstream file(filename);
        
        if (!file.is_open()) {
            throw std::runtime_error("Cannot create file: " + filename);
        }
        
        for (const auto& line : lines) {
            file << line << std::endl;
        }
        
        std::cout << "Successfully wrote " << lines.size() << " lines to " << filename << std::endl;
    }
    
    void processFile() {
        try {
            auto lines = readLines();
            
            // Process lines (example: convert to uppercase)
            for (auto& line : lines) {
                std::transform(line.begin(), line.end(), line.begin(), ::toupper);
            }
            
            writeLines(lines);
        }
        catch (const std::exception& e) {
            std::cerr << "Error processing file: " << e.what() << std::endl;
            throw; // Re-throw for caller to handle
        }
    }
};

// Example 3: Database Connection with RAII
class DatabaseConnectionException : public std::exception {
private:
    std::string message;
    
public:
    DatabaseConnectionException(const std::string& msg) : message(msg) {}
    
    const char* what() const noexcept override {
        return message.c_str();
    }
};

class DatabaseConnection {
private:
    std::string connectionString;
    bool isConnected;
    
public:
    DatabaseConnection(const std::string& connStr) 
        : connectionString(connStr), isConnected(false) {
        connect();
    }
    
    ~DatabaseConnection() {
        if (isConnected) {
            disconnect();
        }
    }
    
    void connect() {
        // Simulate connection attempt
        if (connectionString.empty()) {
            throw DatabaseConnectionException("Connection string cannot be empty");
        }
        
        if (connectionString == "invalid") {
            throw DatabaseConnectionException("Invalid connection string");
        }
        
        // Simulate connection delay
        std::this_thread::sleep_for(std::chrono::milliseconds(100));
        isConnected = true;
        
        std::cout << "Connected to database" << std::endl;
    }
    
    void disconnect() {
        if (isConnected) {
            isConnected = false;
            std::cout << "Disconnected from database" << std::endl;
        }
    }
    
    void executeQuery(const std::string& query) {
        if (!isConnected) {
            throw DatabaseConnectionException("Not connected to database");
        }
        
        std::cout << "Executing query: " << query << std::endl;
        
        // Simulate query execution
        std::this_thread::sleep_for(std::chrono::milliseconds(50));
    }
    
    bool getConnectionStatus() const {
        return isConnected;
    }
};

// Example 4: E-commerce Order Processing
class OrderProcessingException : public std::exception {
private:
    std::string message;
    int orderId;
    
public:
    OrderProcessingException(const std::string& msg, int id) 
        : message(msg), orderId(id) {}
    
    const char* what() const noexcept override {
        return message.c_str();
    }
    
    int getOrderId() const { return orderId; }
};

class OutOfStockException : public OrderProcessingException {
private:
    std::string productId;
    int requestedQuantity;
    int availableQuantity;
    
public:
    OutOfStockException(int orderId, const std::string& productId, 
                        int requested, int available)
        : OrderProcessingException("Product out of stock", orderId),
          productId(productId), requestedQuantity(requested), availableQuantity(available) {}
    
    const std::string& getProductId() const { return productId; }
    int getRequestedQuantity() const { return requestedQuantity; }
    int getAvailableQuantity() const { return availableQuantity; }
};

class InvalidOrderException : public OrderProcessingException {
private:
    std::string reason;
    
public:
    InvalidOrderException(int orderId, const std::string& reason)
        : OrderProcessingException("Invalid order", orderId), reason(reason) {}
    
    const std::string& getReason() const { return reason; }
};

class Product {
private:
    std::string productId;
    std::string name;
    double price;
    int stock;
    
public:
    Product(const std::string& id, const std::string& name, double price, int stock)
        : productId(id), name(name), price(price), stock(stock) {}
    
    const std::string& getProductId() const { return productId; }
    const std::string& getName() const { return name; }
    double getPrice() const { return price; }
    int getStock() const { return stock; }
    
    void reduceStock(int quantity) {
        if (quantity > stock) {
            throw OutOfStockException(0, productId, quantity, stock);
        }
        stock -= quantity;
    }
    
    void display() const {
        std::cout << "Product: " << name << " (" << productId << ")" << std::endl;
        std::cout << "Price: $" << price << ", Stock: " << stock << std::endl;
    }
};

class OrderItem {
public:
    std::string productId;
    int quantity;
    double unitPrice;
    
    OrderItem(const std::string& id, int qty, double price)
        : productId(id), quantity(qty), unitPrice(price) {}
};

class Order {
private:
    int orderId;
    std::vector<OrderItem> items;
    std::string customerName;
    std::string shippingAddress;
    std::time_t orderDate;
    
public:
    Order(int id, const std::string& customer, const std::string& address)
        : orderId(id), customerName(customer), shippingAddress(address) {
        orderDate = std::time(nullptr);
    }
    
    void addItem(const std::string& productId, int quantity, double unitPrice) {
        if (quantity <= 0) {
            throw InvalidOrderException(orderId, "Quantity must be positive");
        }
        
        if (unitPrice <= 0) {
            throw InvalidOrderException(orderId, "Unit price must be positive");
        }
        
        items.push_back(OrderItem(productId, quantity, unitPrice));
    }
    
    double calculateTotal() const {
        double total = 0;
        for (const auto& item : items) {
            total += item.quantity * item.unitPrice;
        }
        return total;
    }
    
    void display() const {
        std::cout << "\n=== Order #" << orderId << " ===" << std::endl;
        std::cout << "Customer: " << customerName << std::endl;
        std::cout << "Shipping Address: " << shippingAddress << std::endl;
        std::cout << "Order Date: " << std::ctime(&orderDate);
        
        std::cout << "\nItems:" << std::endl;
        for (const auto& item : items) {
            std::cout << "  " << item.productId << " x" << item.quantity 
                      << " @ $" << item.unitPrice << " = $" 
                      << (item.quantity * item.unitPrice) << std::endl;
        }
        
        std::cout << "\nTotal: $" << calculateTotal() << std::endl;
    }
    
    int getOrderId() const { return orderId; }
    const std::vector<OrderItem>& getItems() const { return items; }
};

class OrderProcessor {
private:
    std::vector<std::unique_ptr<Product>> products;
    std::vector<std::unique_ptr<Order>> orders;
    int nextOrderId;
    
public:
    OrderProcessor() : nextOrderId(1001) {
        initializeProducts();
    }
    
    void initializeProducts() {
        products.push_back(std::make_unique<Product>("P001", "Laptop", 999.99, 10));
        products.push_back(std::make_unique<Product>("P002", "Mouse", 29.99, 50));
        products.push_back(std::make_unique<Product>("P003", "Keyboard", 79.99, 25));
        products.push_back(std::make_unique<Product>("P004", "Monitor", 299.99, 15));
    }
    
    Product* findProduct(const std::string& productId) {
        for (auto& product : products) {
            if (product->getProductId() == productId) {
                return product.get();
            }
        }
        throw std::runtime_error("Product not found: " + productId);
    }
    
    int createOrder(const std::string& customer, const std::string& address) {
        auto order = std::make_unique<Order>(nextOrderId++, customer, address);
        int orderId = order->getOrderId();
        orders.push_back(std::move(order));
        return orderId;
    }
    
    void addItemToOrder(int orderId, const std::string& productId, int quantity) {
        Order* order = nullptr;
        for (auto& ord : orders) {
            if (ord->getOrderId() == orderId) {
                order = ord.get();
                break;
            }
        }
        
        if (!order) {
            throw InvalidOrderException(orderId, "Order not found");
        }
        
        Product* product = findProduct(productId);
        order->addItem(productId, quantity, product->getPrice());
    }
    
    void processOrder(int orderId) {
        Order* order = nullptr;
        for (auto& ord : orders) {
            if (ord->getOrderId() == orderId) {
                order = ord.get();
                break;
            }
        }
        
        if (!order) {
            throw InvalidOrderException(orderId, "Order not found");
        }
        
        try {
            // Check stock and reserve items
            for (const auto& item : order->getItems()) {
                Product* product = findProduct(item.productId);
                product->reduceStock(item.quantity);
            }
            
            std::cout << "Order #" << orderId << " processed successfully" << std::endl;
            order->display();
        }
        catch (const OutOfStockException& e) {
            std::cout << "Order processing failed: " << e.what() << std::endl;
            std::cout << "Product: " << e.getProductId() 
                      << ", Requested: " << e.getRequestedQuantity()
                      << ", Available: " << e.getAvailableQuantity() << std::endl;
            throw;
        }
    }
    
    void displayProducts() const {
        std::cout << "\n=== Available Products ===" << std::endl;
        for (const auto& product : products) {
            product->display();
            std::cout << "---" << std::endl;
        }
    }
    
    void displayOrders() const {
        std::cout << "\n=== All Orders ===" << std::endl;
        for (const auto& order : orders) {
            order->display();
            std::cout << "===\n" << std::endl;
        }
    }
};

// Example 5: Exception-Safe Resource Management
class ResourceManager {
private:
    std::vector<std::unique_ptr<int>> resources;
    
public:
    void allocateResource(int value) {
        resources.push_back(std::make_unique<int>(value));
        std::cout << "Resource allocated with value: " << value << std::endl;
    }
    
    void useResource(size_t index) {
        if (index >= resources.size()) {
            throw std::out_of_range("Resource index out of range");
        }
        
        std::cout << "Using resource with value: " << *resources[index] << std::endl;
    }
    
    void displayResources() const {
        std::cout << "\n=== Resources ===" << std::endl;
        for (size_t i = 0; i < resources.size(); i++) {
            std::cout << "Resource " << i << ": " << *resources[i] << std::endl;
        }
    }
};

void demonstrateExceptionSafety() {
    std::cout << "\n=== EXCEPTION SAFETY DEMONSTRATION ===" << std::endl;
    
    ResourceManager manager;
    
    try {
        manager.allocateResource(100);
        manager.allocateResource(200);
        manager.allocateResource(300);
        
        manager.displayResources();
        
        // This will throw an exception
        manager.useResource(5);
    }
    catch (const std::exception& e) {
        std::cout << "Exception caught: " << e.what() << std::endl;
    }
    
    // Resources are automatically cleaned up when manager goes out of scope
    std::cout << "Resources automatically cleaned up" << std::endl;
}

int main() {
    std::cout << "=== Exception Handling and Error Management - Real-Life Examples ===" << std::endl;
    std::cout << "Demonstrating practical applications of exception handling\n" << std::endl;
    
    // Example 1: Banking System
    std::cout << "=== BANKING SYSTEM ===" << std::endl;
    Bank bank;
    
    try {
        bank.createAccount("ACC001", "John Doe", 1000.0);
        bank.createAccount("ACC002", "Jane Smith", 500.0);
        
        BankAccount* account = bank.findAccount("ACC001");
        account->display();
        
        account->withdraw(500.0);
        account->deposit(200.0);
        
        // This will throw an exception
        account->withdraw(1000.0);
    }
    catch (const InsufficientFundsException& e) {
        std::cout << "Insufficient funds error: " << e.what() << std::endl;
        std::cout << "Requested: $" << e.getRequestedAmount() 
                  << ", Available: $" << e.getAvailableBalance() << std::endl;
    }
    catch (const BankingException& e) {
        std::cout << "Banking error: " << e.what() << std::endl;
        std::cout << "Error code: " << e.getErrorCode() << std::endl;
    }
    catch (const std::exception& e) {
        std::cout << "General error: " << e.what() << std::endl;
    }
    
    // Example 2: File Processing
    std::cout << "\n=== FILE PROCESSING ===" << std::endl;
    FileProcessor processor("test.txt");
    
    try {
        // Create test file
        std::vector<std::string> lines = {"Hello World", "This is a test", "File processing example"};
        processor.writeLines(lines);
        
        // Process the file
        processor.processFile();
    }
    catch (const std::exception& e) {
        std::cout << "File processing error: " << e.what() << std::endl;
    }
    
    // Example 3: Database Connection
    std::cout << "\n=== DATABASE CONNECTION ===" << std::endl;
    
    try {
        DatabaseConnection conn("server=localhost;database=test");
        conn.executeQuery("SELECT * FROM users");
        
        DatabaseConnection invalidConn("invalid");
    }
    catch (const DatabaseConnectionException& e) {
        std::cout << "Database error: " << e.what() << std::endl;
    }
    
    // Example 4: E-commerce Order Processing
    std::cout << "\n=== E-COMMERCE ORDER PROCESSING ===" << std::endl;
    OrderProcessor orderProcessor;
    
    try {
        orderProcessor.displayProducts();
        
        int orderId = orderProcessor.createOrder("John Doe", "123 Main St, City, State");
        orderProcessor.addItemToOrder(orderId, "P001", 1);
        orderProcessor.addItemToOrder(orderId, "P002", 2);
        orderProcessor.addItemToOrder(orderId, "P003", 1);
        
        orderProcessor.processOrder(orderId);
        
        // This will fail due to insufficient stock
        int orderId2 = orderProcessor.createOrder("Jane Smith", "456 Oak Ave, City, State");
        orderProcessor.addItemToOrder(orderId2, "P001", 20); // Only 10 in stock
        orderProcessor.processOrder(orderId2);
    }
    catch (const OutOfStockException& e) {
        std::cout << "Stock error: " << e.what() << std::endl;
    }
    catch (const InvalidOrderException& e) {
        std::cout << "Invalid order: " << e.what() << std::endl;
        std::cout << "Reason: " << e.getReason() << std::endl;
    }
    catch (const std::exception& e) {
        std::cout << "Order processing error: " << e.what() << std::endl;
    }
    
    // Example 5: Exception Safety
    demonstrateExceptionSafety();
    
    std::cout << "\n\n=== EXCEPTION HANDLING SUMMARY ===" << std::endl;
    std::cout << "This example demonstrates various exception handling concepts:" << std::endl;
    std::cout << "• Custom exception classes with detailed error information" << std::endl;
    std::cout << "• Exception hierarchies for different error types" << std::endl;
    std::cout << "• RAII for automatic resource cleanup" << std::endl;
    std::cout << "• Try-catch blocks for error handling" << std::endl;
    std::cout << "• Exception safety guarantees" << std::endl;
    std::cout << "• Smart pointers for memory management" << std::endl;
    std::cout << "• noexcept specifications" << std::endl;
    std::cout << "\nException handling is crucial for building robust and reliable applications!" << std::endl;
    
    return 0;
}