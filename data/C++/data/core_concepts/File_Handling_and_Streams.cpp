// Module 9: File Handling and Streams - Real-Life Examples
// This file demonstrates practical applications of file handling and streams

#include <iostream>
#include <fstream>
#include <string>
#include <vector>
#include <sstream>
#include <iomanip>
#include <algorithm>

// Example 1: Contact Management System
class ContactManager {
private:
    struct Contact {
        std::string name;
        std::string phone;
        std::string email;
        std::string address;
    };
    
    std::vector<Contact> contacts;
    std::string filename;
    
public:
    ContactManager(const std::string& file) : filename(file) {
        loadContacts();
    }
    
    ~ContactManager() {
        saveContacts();
    }
    
    void addContact(const std::string& name, const std::string& phone, 
                   const std::string& email, const std::string& address) {
        contacts.push_back({name, phone, email, address});
        std::cout << "Contact '" << name << "' added." << std::endl;
    }
    
    void loadContacts() {
        std::ifstream file(filename);
        if (!file.is_open()) {
            std::cout << "No existing contact file found. Starting fresh." << std::endl;
            return;
        }
        
        contacts.clear();
        std::string line;
        while (std::getline(file, line)) {
            std::istringstream iss(line);
            std::string name, phone, email, address;
            
            // Parse CSV format: name,phone,email,address
            if (std::getline(iss, name, ',') &&
                std::getline(iss, phone, ',') &&
                std::getline(iss, email, ',') &&
                std::getline(iss, address)) {
                contacts.push_back({name, phone, email, address});
            }
        }
        
        std::cout << "Loaded " << contacts.size() << " contacts from file." << std::endl;
    }
    
    void saveContacts() {
        std::ofstream file(filename);
        if (!file.is_open()) {
            std::cerr << "Error: Could not save contacts to file." << std::endl;
            return;
        }
        
        for (const auto& contact : contacts) {
            file << contact.name << "," << contact.phone << "," 
                 << contact.email << "," << contact.address << "\n";
        }
        
        std::cout << "Saved " << contacts.size() << " contacts to file." << std::endl;
    }
    
    void displayContacts() {
        if (contacts.empty()) {
            std::cout << "No contacts found." << std::endl;
            return;
        }
        
        std::cout << "\n=== CONTACT LIST ===" << std::endl;
        std::cout << std::left << std::setw(20) << "Name" 
                  << std::setw(15) << "Phone" 
                  << std::setw(25) << "Email" 
                  << "Address" << std::endl;
        std::cout << std::string(80, '-') << std::endl;
        
        for (const auto& contact : contacts) {
            std::cout << std::left << std::setw(20) << contact.name
                      << std::setw(15) << contact.phone
                      << std::setw(25) << contact.email
                      << contact.address << std::endl;
        }
    }
    
    void searchContacts(const std::string& searchTerm) {
        std::cout << "\n=== SEARCH RESULTS FOR '" << searchTerm << "' ===" << std::endl;
        
        bool found = false;
        for (const auto& contact : contacts) {
            if (contact.name.find(searchTerm) != std::string::npos ||
                contact.phone.find(searchTerm) != std::string::npos ||
                contact.email.find(searchTerm) != std::string::npos) {
                std::cout << "Name: " << contact.name << std::endl;
                std::cout << "Phone: " << contact.phone << std::endl;
                std::cout << "Email: " << contact.email << std::endl;
                std::cout << "Address: " << contact.address << std::endl;
                std::cout << "---" << std::endl;
                found = true;
            }
        }
        
        if (!found) {
            std::cout << "No contacts found matching '" << searchTerm << "'." << std::endl;
        }
    }
};

// Example 2: Log File Manager
class LogManager {
private:
    std::string logFilename;
    std::ofstream logFile;
    
    enum LogLevel {
        INFO,
        WARNING,
        ERROR,
        DEBUG
    };
    
    std::string getLogLevelString(LogLevel level) {
        switch (level) {
            case INFO: return "INFO";
            case WARNING: return "WARNING";
            case ERROR: return "ERROR";
            case DEBUG: return "DEBUG";
            default: return "UNKNOWN";
        }
    }
    
    std::string getCurrentTimestamp() {
        auto now = std::time(nullptr);
        auto tm = *std::localtime(&now);
        
        std::ostringstream oss;
        oss << std::put_time(&tm, "%Y-%m-%d %H:%M:%S");
        return oss.str();
    }
    
public:
    LogManager(const std::string& filename) : logFilename(filename) {
        logFile.open(logFilename, std::ios::app); // Open in append mode
        if (!logFile.is_open()) {
            std::cerr << "Error: Could not open log file." << std::endl;
        } else {
            logMessage(INFO, "Log manager initialized");
        }
    }
    
    ~LogManager() {
        if (logFile.is_open()) {
            logMessage(INFO, "Log manager shutting down");
            logFile.close();
        }
    }
    
    void logMessage(LogLevel level, const std::string& message) {
        if (!logFile.is_open()) return;
        
        logFile << "[" << getCurrentTimestamp() << "] "
                << "[" << getLogLevelString(level) << "] "
                << message << std::endl;
        
        logFile.flush(); // Ensure immediate write
    }
    
    void logInfo(const std::string& message) {
        logMessage(INFO, message);
    }
    
    void logWarning(const std::string& message) {
        logMessage(WARNING, message);
    }
    
    void logError(const std::string& message) {
        logMessage(ERROR, message);
    }
    
    void logDebug(const std::string& message) {
        logMessage(DEBUG, message);
    }
    
    void displayRecentLogs(int lines = 10) {
        std::ifstream file(logFilename);
        if (!file.is_open()) {
            std::cout << "Could not open log file for reading." << std::endl;
            return;
        }
        
        std::vector<std::string> logLines;
        std::string line;
        
        // Read all lines
        while (std::getline(file, line)) {
            logLines.push_back(line);
        }
        
        // Display last N lines
        std::cout << "\n=== RECENT LOG ENTRIES (Last " << lines << ") ===" << std::endl;
        
        int startIdx = std::max(0, static_cast<int>(logLines.size()) - lines);
        for (int i = startIdx; i < logLines.size(); ++i) {
            std::cout << logLines[i] << std::endl;
        }
    }
};

// Example 3: Configuration File Manager
class ConfigManager {
private:
    std::map<std::string, std::string> settings;
    std::string configFilename;
    
public:
    ConfigManager(const std::string& filename) : configFilename(filename) {
        loadConfig();
    }
    
    void loadConfig() {
        std::ifstream file(configFilename);
        if (!file.is_open()) {
            std::cout << "Config file not found. Creating default configuration." << std::endl;
            createDefaultConfig();
            return;
        }
        
        settings.clear();
        std::string line;
        
        while (std::getline(file, line)) {
            // Skip comments and empty lines
            if (line.empty() || line[0] == '#') {
                continue;
            }
            
            size_t equalPos = line.find('=');
            if (equalPos != std::string::npos) {
                std::string key = line.substr(0, equalPos);
                std::string value = line.substr(equalPos + 1);
                
                // Trim whitespace
                key.erase(0, key.find_first_not_of(" \t"));
                key.erase(key.find_last_not_of(" \t") + 1);
                value.erase(0, value.find_first_not_of(" \t"));
                value.erase(value.find_last_not_of(" \t") + 1);
                
                settings[key] = value;
            }
        }
        
        std::cout << "Configuration loaded with " << settings.size() << " settings." << std::endl;
    }
    
    void saveConfig() {
        std::ofstream file(configFilename);
        if (!file.is_open()) {
            std::cerr << "Error: Could not save configuration." << std::endl;
            return;
        }
        
        file << "# Application Configuration File\n";
        file << "# Generated automatically\n\n";
        
        for (const auto& setting : settings) {
            file << setting.first << " = " << setting.second << "\n";
        }
        
        std::cout << "Configuration saved." << std::endl;
    }
    
    void createDefaultConfig() {
        settings = {
            {"app_name", "MyApplication"},
            {"version", "1.0.0"},
            {"debug_mode", "false"},
            {"max_connections", "100"},
            {"timeout", "30"},
            {"log_level", "INFO"},
            {"data_directory", "./data"},
            {"backup_enabled", "true"}
        };
        saveConfig();
    }
    
    std::string getSetting(const std::string& key, const std::string& defaultValue = "") {
        auto it = settings.find(key);
        return (it != settings.end()) ? it->second : defaultValue;
    }
    
    void setSetting(const std::string& key, const std::string& value) {
        settings[key] = value;
    }
    
    int getIntSetting(const std::string& key, int defaultValue = 0) {
        std::string value = getSetting(key);
        try {
            return std::stoi(value);
        } catch (...) {
            return defaultValue;
        }
    }
    
    bool getBoolSetting(const std::string& key, bool defaultValue = false) {
        std::string value = getSetting(key);
        std::transform(value.begin(), value.end(), value.begin(), ::tolower);
        return (value == "true" || value == "1" || value == "yes");
    }
    
    void displaySettings() {
        std::cout << "\n=== CURRENT SETTINGS ===" << std::endl;
        for (const auto& setting : settings) {
            std::cout << setting.first << " = " << setting.second << std::endl;
        }
    }
};

// Example 4: Data Export/Import System
class DataExporter {
private:
    struct SalesData {
        std::string productId;
        std::string productName;
        int quantity;
        double price;
        std::string date;
    };
    
    std::vector<SalesData> salesData;
    
public:
    void addSalesData(const std::string& id, const std::string& name, 
                     int qty, double price, const std::string& date) {
        salesData.push_back({id, name, qty, price, date});
    }
    
    void exportToCSV(const std::string& filename) {
        std::ofstream file(filename);
        if (!file.is_open()) {
            std::cerr << "Error: Could not create CSV file." << std::endl;
            return;
        }
        
        // Write header
        file << "Product ID,Product Name,Quantity,Price,Total,Date\n";
        
        // Write data
        for (const auto& data : salesData) {
            double total = data.quantity * data.price;
            file << data.productId << ","
                  << data.productName << ","
                  << data.quantity << ","
                  << std::fixed << std::setprecision(2) << data.price << ","
                  << total << ","
                  << data.date << "\n";
        }
        
        std::cout << "Data exported to " << filename << std::endl;
    }
    
    void exportToJSON(const std::string& filename) {
        std::ofstream file(filename);
        if (!file.is_open()) {
            std::cerr << "Error: Could not create JSON file." << std::endl;
            return;
        }
        
        file << "{\n";
        file << "  \"sales_data\": [\n";
        
        for (size_t i = 0; i < salesData.size(); ++i) {
            const auto& data = salesData[i];
            file << "    {\n";
            file << "      \"product_id\": \"" << data.productId << "\",\n";
            file << "      \"product_name\": \"" << data.productName << "\",\n";
            file << "      \"quantity\": " << data.quantity << ",\n";
            file << "      \"price\": " << std::fixed << std::setprecision(2) << data.price << ",\n";
            file << "      \"total\": " << std::fixed << std::setprecision(2) << (data.quantity * data.price) << ",\n";
            file << "      \"date\": \"" << data.date << "\"\n";
            file << "    }";
            
            if (i < salesData.size() - 1) {
                file << ",";
            }
            file << "\n";
        }
        
        file << "  ]\n";
        file << "}\n";
        
        std::cout << "Data exported to " << filename << std::endl;
    }
    
    void importFromCSV(const std::string& filename) {
        std::ifstream file(filename);
        if (!file.is_open()) {
            std::cerr << "Error: Could not open CSV file." << std::endl;
            return;
        }
        
        salesData.clear();
        std::string line;
        
        // Skip header
        std::getline(file, line);
        
        while (std::getline(file, line)) {
            std::istringstream iss(line);
            std::string id, name, qtyStr, priceStr, totalStr, date;
            
            if (std::getline(iss, id, ',') &&
                std::getline(iss, name, ',') &&
                std::getline(iss, qtyStr, ',') &&
                std::getline(iss, priceStr, ',') &&
                std::getline(iss, totalStr, ',') &&
                std::getline(iss, date)) {
                
                try {
                    int quantity = std::stoi(qtyStr);
                    double price = std::stod(priceStr);
                    
                    salesData.push_back({id, name, quantity, price, date});
                } catch (...) {
                    std::cerr << "Error parsing line: " << line << std::endl;
                }
            }
        }
        
        std::cout << "Imported " << salesData.size() << " records from " << filename << std::endl;
    }
    
    void generateReport(const std::string& filename) {
        std::ofstream file(filename);
        if (!file.is_open()) {
            std::cerr << "Error: Could not create report file." << std::endl;
            return;
        }
        
        // Calculate statistics
        double totalRevenue = 0;
        int totalQuantity = 0;
        std::map<std::string, double> productRevenue;
        
        for (const auto& data : salesData) {
            double revenue = data.quantity * data.price;
            totalRevenue += revenue;
            totalQuantity += data.quantity;
            productRevenue[data.productName] += revenue;
        }
        
        file << "SALES REPORT\n";
        file << "=============\n\n";
        file << "Total Revenue: $" << std::fixed << std::setprecision(2) << totalRevenue << "\n";
        file << "Total Units Sold: " << totalQuantity << "\n";
        file << "Average Price: $" << (totalQuantity > 0 ? totalRevenue / totalQuantity : 0) << "\n\n";
        
        file << "Revenue by Product:\n";
        file << "------------------\n";
        
        // Sort products by revenue
        std::vector<std::pair<std::string, double>> sortedProducts(
            productRevenue.begin(), productRevenue.end());
        std::sort(sortedProducts.begin(), sortedProducts.end(),
                 [](const auto& a, const auto& b) { return a.second > b.second; });
        
        for (const auto& product : sortedProducts) {
            file << product.first << ": $" << std::fixed << std::setprecision(2) 
                  << product.second << "\n";
        }
        
        std::cout << "Report generated: " << filename << std::endl;
    }
};

// Example 5: Binary File Operations
class BinaryFileManager {
private:
    struct Employee {
        int id;
        char name[50];
        double salary;
        int department;
    };
    
public:
    void writeBinaryFile(const std::string& filename) {
        std::ofstream file(filename, std::ios::binary);
        if (!file.is_open()) {
            std::cerr << "Error: Could not create binary file." << std::endl;
            return;
        }
        
        std::vector<Employee> employees = {
            {1001, "John Smith", 75000.0, 1},
            {1002, "Jane Doe", 82000.0, 2},
            {1003, "Bob Johnson", 68000.0, 1},
            {1004, "Alice Brown", 95000.0, 3}
        };
        
        // Write the number of employees first
        size_t count = employees.size();
        file.write(reinterpret_cast<const char*>(&count), sizeof(count));
        
        // Write each employee
        for (const auto& emp : employees) {
            file.write(reinterpret_cast<const char*>(&emp), sizeof(emp));
        }
        
        std::cout << "Wrote " << employees.size() << " employees to binary file." << std::endl;
    }
    
    void readBinaryFile(const std::string& filename) {
        std::ifstream file(filename, std::ios::binary);
        if (!file.is_open()) {
            std::cerr << "Error: Could not open binary file." << std::endl;
            return;
        }
        
        // Read the number of employees
        size_t count;
        file.read(reinterpret_cast<char*>(&count), sizeof(count));
        
        std::cout << "\n=== EMPLOYEE DATA FROM BINARY FILE ===" << std::endl;
        std::cout << "Total Employees: " << count << std::endl;
        
        // Read each employee
        for (size_t i = 0; i < count; ++i) {
            Employee emp;
            file.read(reinterpret_cast<char*>(&emp), sizeof(emp));
            
            std::cout << "ID: " << emp.id << ", Name: " << emp.name 
                      << ", Salary: $" << emp.salary << ", Dept: " << emp.department << std::endl;
        }
    }
    
    void updateEmployeeInBinary(const std::string& filename, int employeeId, double newSalary) {
        // Open for reading and writing
        std::fstream file(filename, std::ios::binary | std::ios::in | std::ios::out);
        if (!file.is_open()) {
            std::cerr << "Error: Could not open binary file for updating." << std::endl;
            return;
        }
        
        // Read the number of employees
        size_t count;
        file.read(reinterpret_cast<char*>(&count), sizeof(count));
        
        // Find and update the employee
        for (size_t i = 0; i < count; ++i) {
            Employee emp;
            file.read(reinterpret_cast<char*>(&emp), sizeof(emp));
            
            if (emp.id == employeeId) {
                emp.salary = newSalary;
                
                // Move back to overwrite this employee
                file.seekp(sizeof(size_t) + i * sizeof(emp), std::ios::beg);
                file.write(reinterpret_cast<const char*>(&emp), sizeof(emp));
                
                std::cout << "Updated employee " << employeeId << " salary to $" << newSalary << std::endl;
                return;
            }
        }
        
        std::cout << "Employee " << employeeId << " not found." << std::endl;
    }
};

int main() {
    std::cout << "=== File Handling and Streams - Real-Life Examples ===" << std::endl;
    std::cout << "Demonstrating practical applications of file handling\n" << std::endl;
    
    // Example 1: Contact Management
    std::cout << "=== CONTACT MANAGEMENT SYSTEM ===" << std::endl;
    ContactManager contacts("contacts.txt");
    contacts.addContact("Alice Johnson", "555-1234", "alice@email.com", "123 Main St");
    contacts.addContact("Bob Smith", "555-5678", "bob@email.com", "456 Oak Ave");
    contacts.addContact("Charlie Brown", "555-9012", "charlie@email.com", "789 Pine Rd");
    
    contacts.displayContacts();
    contacts.searchContacts("Alice");
    
    // Example 2: Log Management
    std::cout << "\n=== LOG MANAGEMENT SYSTEM ===" << std::endl;
    LogManager logger("application.log");
    logger.logInfo("Application started");
    logger.logWarning("Low disk space");
    logger.logError("Database connection failed");
    logger.logDebug("User login attempt");
    logger.logInfo("Processing request");
    
    logger.displayRecentLogs(5);
    
    // Example 3: Configuration Management
    std::cout << "\n=== CONFIGURATION MANAGER ===" << std::endl;
    ConfigManager config("app_config.txt");
    config.displaySettings();
    
    std::cout << "\nApp Name: " << config.getSetting("app_name") << std::endl;
    std::cout << "Max Connections: " << config.getIntSetting("max_connections") << std::endl;
    std::cout << "Debug Mode: " << (config.getBoolSetting("debug_mode") ? "Enabled" : "Disabled") << std::endl;
    
    config.setSetting("new_setting", "test_value");
    config.saveConfig();
    
    // Example 4: Data Export/Import
    std::cout << "\n=== DATA EXPORT/IMPORT SYSTEM ===" << std::endl;
    DataExporter exporter;
    exporter.addSalesData("P001", "Laptop", 5, 999.99, "2024-03-18");
    exporter.addSalesData("P002", "Mouse", 10, 29.99, "2024-03-18");
    exporter.addSalesData("P003", "Keyboard", 7, 79.99, "2024-03-19");
    exporter.addSalesData("P004", "Monitor", 3, 299.99, "2024-03-19");
    
    exporter.exportToCSV("sales_data.csv");
    exporter.exportToJSON("sales_data.json");
    exporter.generateReport("sales_report.txt");
    
    // Example 5: Binary File Operations
    std::cout << "\n=== BINARY FILE OPERATIONS ===" << std::endl;
    BinaryFileManager binaryManager;
    binaryManager.writeBinaryFile("employees.dat");
    binaryManager.readBinaryFile("employees.dat");
    binaryManager.updateEmployeeInBinary("employees.dat", 1002, 85000.0);
    binaryManager.readBinaryFile("employees.dat");
    
    std::cout << "\n\n=== FILE HANDLING SUMMARY ===" << std::endl;
    std::cout << "This example demonstrates various file handling concepts:" << std::endl;
    std::cout << "• Text file operations for CSV data storage" << std::endl;
    std::cout << "• Log file management with timestamped entries" << std::endl;
    std::cout << "• Configuration file parsing and management" << std::endl;
    std::cout << "• Data export in multiple formats (CSV, JSON)" << std::endl;
    std::cout << "• Binary file operations for structured data" << std::endl;
    std::cout << "• File streams for input/output operations" << std::endl;
    std::cout << "• String streams for data parsing" << std::endl;
    std::cout << "\nFile handling is essential for data persistence and application configuration!" << std::endl;
    
    return 0;
}