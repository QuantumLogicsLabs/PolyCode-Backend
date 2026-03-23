# Module 9: File Handling and Streams

## Learning Objectives
- Understand C++ stream hierarchy and file streams
- Master text file operations (reading and writing)
- Learn binary file operations
- Understand file positioning and random access
- Master error handling in file operations
- Learn about string streams and stringstream

## C++ Stream Hierarchy

### Stream Classes Overview
```cpp
#include <iostream>
#include <fstream>
#include <sstream>

int main() {
    // Standard streams
    std::istream& input = std::cin;   // Input stream
    std::ostream& output = std::cout; // Output stream
    std::ostream& error = std::cerr;  // Error stream (unbuffered)
    std::ostream& log = std::clog;    // Log stream (buffered)
    
    std::cout << "This goes to standard output" << std::endl;
    std::cerr << "This goes to standard error" << std::endl;
    std::clog << "This goes to standard log" << std::endl;
    
    return 0;
}
```

## Text File Operations

### Writing to Text Files
```cpp
#include <iostream>
#include <fstream>
#include <string>

int main() {
    // Create and open a file for writing
    std::ofstream outFile("example.txt");
    
    if (!outFile.is_open()) {
        std::cerr << "Error: Could not open file for writing!" << std::endl;
        return 1;
    }
    
    // Write different types of data
    outFile << "Hello, World!" << std::endl;
    outFile << "This is a text file example." << std::endl;
    outFile << "Number: " << 42 << std::endl;
    outFile << "Pi: " << 3.14159 << std::endl;
    
    outFile.close();
    std::cout << "Data written to file successfully!" << std::endl;
    
    return 0;
}
```

### Reading from Text Files
```cpp
#include <iostream>
#include <fstream>
#include <string>

int main() {
    std::ifstream inFile("example.txt");
    
    if (!inFile.is_open()) {
        std::cerr << "Error: Could not open file for reading!" << std::endl;
        return 1;
    }
    
    std::string line;
    int lineNumber = 1;
    
    std::cout << "File contents:" << std::endl;
    while (std::getline(inFile, line)) {
        std::cout << "Line " << lineNumber << ": " << line << std::endl;
        lineNumber++;
    }
    
    inFile.close();
    
    return 0;
}
```

### Reading and Writing Structured Data
```cpp
#include <iostream>
#include <fstream>
#include <string>
#include <vector>

struct Person {
    std::string name;
    int age;
    double salary;
};

void writePeopleToFile(const std::vector<Person>& people, const std::string& filename) {
    std::ofstream outFile(filename);
    
    if (!outFile.is_open()) {
        std::cerr << "Error: Could not open file for writing!" << std::endl;
        return;
    }
    
    for (const auto& person : people) {
        outFile << person.name << "," << person.age << "," << person.salary << std::endl;
    }
    
    outFile.close();
    std::cout << "People data written to " << filename << std::endl;
}

std::vector<Person> readPeopleFromFile(const std::string& filename) {
    std::vector<Person> people;
    std::ifstream inFile(filename);
    
    if (!inFile.is_open()) {
        std::cerr << "Error: Could not open file for reading!" << std::endl;
        return people;
    }
    
    std::string line;
    while (std::getline(inFile, line)) {
        std::stringstream ss(line);
        std::string name, ageStr, salaryStr;
        
        if (std::getline(ss, name, ',') && 
            std::getline(ss, ageStr, ',') && 
            std::getline(ss, salaryStr, ',')) {
            
            Person person;
            person.name = name;
            person.age = std::stoi(ageStr);
            person.salary = std::stod(salaryStr);
            people.push_back(person);
        }
    }
    
    inFile.close();
    return people;
}

int main() {
    // Create sample data
    std::vector<Person> people = {
        {"John Doe", 30, 50000.0},
        {"Jane Smith", 25, 45000.0},
        {"Bob Johnson", 35, 60000.0}
    };
    
    // Write to file
    writePeopleToFile(people, "people.csv");
    
    // Read from file
    std::vector<Person> readPeople = readPeopleFromFile("people.csv");
    
    std::cout << "\nPeople read from file:" << std::endl;
    for (const auto& person : readPeople) {
        std::cout << "Name: " << person.name 
                  << ", Age: " << person.age 
                  << ", Salary: $" << person.salary << std::endl;
    }
    
    return 0;
}
```

## File Modes and Options

### Different File Opening Modes
```cpp
#include <iostream>
#include <fstream>

int main() {
    // Different file modes
    std::ofstream outFile1("append.txt", std::ios::app);    // Append mode
    std::ofstream outFile2("truncate.txt", std::ios::trunc); // Truncate mode (default)
    std::fstream file("readwrite.txt", std::ios::in | std::ios::out); // Read and write
    
    // Write in append mode
    if (outFile1.is_open()) {
        outFile1 << "This line will be appended." << std::endl;
        outFile1.close();
    }
    
    // Write in truncate mode
    if (outFile2.is_open()) {
        outFile2 << "This file will be truncated before writing." << std::endl;
        outFile2.close();
    }
    
    // Read and write operations
    if (file.is_open()) {
        file << "Initial content" << std::endl;
        file.seekp(0); // Move write pointer to beginning
        file << "Modified content" << std::endl;
        file.close();
    }
    
    return 0;
}
```

### Binary File Operations
```cpp
#include <iostream>
#include <fstream>
#include <string>

struct Student {
    int id;
    char name[50];
    double gpa;
};

void writeBinaryFile(const std::vector<Student>& students, const std::string& filename) {
    std::ofstream outFile(filename, std::ios::binary);
    
    if (!outFile.is_open()) {
        std::cerr << "Error: Could not open binary file for writing!" << std::endl;
        return;
    }
    
    for (const auto& student : students) {
        outFile.write(reinterpret_cast<const char*>(&student), sizeof(Student));
    }
    
    outFile.close();
    std::cout << "Binary data written successfully!" << std::endl;
}

std::vector<Student> readBinaryFile(const std::string& filename) {
    std::vector<Student> students;
    std::ifstream inFile(filename, std::ios::binary);
    
    if (!inFile.is_open()) {
        std::cerr << "Error: Could not open binary file for reading!" << std::endl;
        return students;
    }
    
    Student student;
    while (inFile.read(reinterpret_cast<char*>(&student), sizeof(Student))) {
        students.push_back(student);
    }
    
    inFile.close();
    return students;
}

int main() {
    // Create sample students
    std::vector<Student> students = {
        {1001, "John Doe", 3.8},
        {1002, "Jane Smith", 3.9},
        {1003, "Bob Johnson", 3.5}
    };
    
    // Write to binary file
    writeBinaryFile(students, "students.dat");
    
    // Read from binary file
    std::vector<Student> readStudents = readBinaryFile("students.dat");
    
    std::cout << "\nStudents read from binary file:" << std::endl;
    for (const auto& student : readStudents) {
        std::cout << "ID: " << student.id 
                  << ", Name: " << student.name 
                  << ", GPA: " << student.gpa << std::endl;
    }
    
    return 0;
}
```

## File Positioning and Random Access

### File Pointer Operations
```cpp
#include <iostream>
#include <fstream>
#include <string>

int main() {
    std::fstream file("random_access.txt", std::ios::in | std::ios::out | std::ios::trunc);
    
    if (!file.is_open()) {
        std::cerr << "Error: Could not open file!" << std::endl;
        return 1;
    }
    
    // Write some data
    file << "Line 1: Hello World" << std::endl;
    file << "Line 2: C++ Programming" << std::endl;
    file << "Line 3: File Handling" << std::endl;
    file << "Line 4: Random Access" << std::endl;
    
    // Get current positions
    std::streampos writePos = file.tellp();
    std::cout << "Current write position: " << writePos << std::endl;
    
    // Move to beginning and read
    file.seekg(0, std::ios::beg); // Move read pointer to beginning
    std::string line;
    std::getline(file, line);
    std::cout << "First line: " << line << std::endl;
    
    // Move to specific position
    file.seekg(20, std::ios::beg); // Move 20 bytes from beginning
    std::getline(file, line);
    std::cout << "Line from position 20: " << line << std::endl;
    
    // Move to end and get file size
    file.seekg(0, std::ios::end);
    std::streampos fileSize = file.tellg();
    std::cout << "File size: " << fileSize << " bytes" << std::endl;
    
    // Move back from end
    file.seekg(-10, std::ios::end);
    std::getline(file, line);
    std::cout << "Last 10 characters: " << line << std::endl;
    
    file.close();
    return 0;
}
```

### Random Access Example
```cpp
#include <iostream>
#include <fstream>
#include <string>

struct Record {
    int id;
    char name[50];
    double balance;
};

class BankDatabase {
private:
    std::fstream file;
    const std::string filename;
    
public:
    BankDatabase(const std::string& fname) : filename(fname) {
        file.open(filename, std::ios::in | std::ios::out | std::ios::binary);
        if (!file.is_open()) {
            // Create file if it doesn't exist
            file.open(filename, std::ios::out | std::ios::binary);
            file.close();
            file.open(filename, std::ios::in | std::ios::out | std::ios::binary);
        }
    }
    
    ~BankDatabase() {
        if (file.is_open()) {
            file.close();
        }
    }
    
    void addRecord(const Record& record) {
        file.seekp(0, std::ios::end);
        file.write(reinterpret_cast<const char*>(&record), sizeof(Record));
        file.flush();
    }
    
    Record getRecord(int recordNumber) {
        Record record;
        file.seekg(recordNumber * sizeof(Record), std::ios::beg);
        file.read(reinterpret_cast<char*>(&record), sizeof(Record));
        return record;
    }
    
    void updateRecord(int recordNumber, const Record& record) {
        file.seekp(recordNumber * sizeof(Record), std::ios::beg);
        file.write(reinterpret_cast<const char*>(&record), sizeof(Record));
        file.flush();
    }
    
    int getRecordCount() {
        file.seekg(0, std::ios::end);
        return static_cast<int>(file.tellg()) / sizeof(Record);
    }
    
    void displayAllRecords() {
        int count = getRecordCount();
        std::cout << "Total records: " << count << std::endl;
        
        for (int i = 0; i < count; i++) {
            Record record = getRecord(i);
            std::cout << "Record " << i << ": ID=" << record.id 
                      << ", Name=" << record.name 
                      << ", Balance=$" << record.balance << std::endl;
        }
    }
};

int main() {
    BankDatabase bank("bank.dat");
    
    // Add some records
    Record r1 = {1001, "John Doe", 1500.50};
    Record r2 = {1002, "Jane Smith", 2500.75};
    Record r3 = {1003, "Bob Johnson", 750.25};
    
    bank.addRecord(r1);
    bank.addRecord(r2);
    bank.addRecord(r3);
    
    // Display all records
    std::cout << "Initial database:" << std::endl;
    bank.displayAllRecords();
    
    // Update a record
    std::cout << "\nUpdating record 1..." << std::endl;
    Record updated = {1002, "Jane Smith", 3000.00};
    bank.updateRecord(1, updated);
    
    // Display updated database
    std::cout << "\nUpdated database:" << std::endl;
    bank.displayAllRecords();
    
    // Access specific record
    std::cout << "\nAccessing record 0:" << std::endl;
    Record specific = bank.getRecord(0);
    std::cout << "ID: " << specific.id << ", Name: " << specific.name 
              << ", Balance: $" << specific.balance << std::endl;
    
    return 0;
}
```

## Error Handling in File Operations

### Comprehensive Error Handling
```cpp
#include <iostream>
#include <fstream>
#include <string>

bool safeFileRead(const std::string& filename, std::string& content) {
    std::ifstream inFile(filename);
    
    // Check if file opened successfully
    if (!inFile.is_open()) {
        std::cerr << "Error: Cannot open file '" << filename << "'" << std::endl;
        
        // Check specific error conditions
        if (inFile.fail()) {
            std::cerr << "Reason: File operation failed" << std::endl;
        }
        if (inFile.bad()) {
            std::cerr << "Reason: Critical I/O error occurred" << std::endl;
        }
        
        return false;
    }
    
    try {
        std::string line;
        content.clear();
        
        while (std::getline(inFile, line)) {
            content += line + "\n";
        }
        
        // Check for read errors
        if (inFile.bad()) {
            std::cerr << "Error: Critical error occurred while reading file" << std::endl;
            return false;
        }
        
        if (!inFile.eof()) {
            std::cerr << "Warning: File reading stopped before EOF" << std::endl;
        }
        
    } catch (const std::exception& e) {
        std::cerr << "Exception caught: " << e.what() << std::endl;
        return false;
    }
    
    inFile.close();
    return true;
}

bool safeFileWrite(const std::string& filename, const std::string& content) {
    std::ofstream outFile(filename);
    
    if (!outFile.is_open()) {
        std::cerr << "Error: Cannot create/open file '" << filename << "' for writing" << std::endl;
        return false;
    }
    
    try {
        outFile << content;
        
        if (outFile.fail()) {
            std::cerr << "Error: Failed to write to file" << std::endl;
            return false;
        }
        
    } catch (const std::exception& e) {
        std::cerr << "Exception caught during write: " << e.what() << std::endl;
        return false;
    }
    
    outFile.close();
    return true;
}

int main() {
    std::string content = "This is a test file.\nIt contains multiple lines.\nFor error handling demonstration.";
    
    // Write to file with error handling
    if (safeFileWrite("test_error.txt", content)) {
        std::cout << "File written successfully!" << std::endl;
    } else {
        std::cout << "Failed to write file!" << std::endl;
        return 1;
    }
    
    // Read from file with error handling
    std::string readContent;
    if (safeFileRead("test_error.txt", readContent)) {
        std::cout << "\nFile content:" << std::endl;
        std::cout << readContent << std::endl;
    } else {
        std::cout << "Failed to read file!" << std::endl;
        return 1;
    }
    
    // Try to read non-existent file
    std::string nonExistent;
    if (!safeFileRead("nonexistent.txt", nonExistent)) {
        std::cout << "\nAs expected, could not read non-existent file!" << std::endl;
    }
    
    return 0;
}
```

## String Streams

### stringstream Usage
```cpp
#include <iostream>
#include <sstream>
#include <string>
#include <vector>

void parseCSVLine(const std::string& line) {
    std::stringstream ss(line);
    std::string field;
    std::vector<std::string> fields;
    
    while (std::getline(ss, field, ',')) {
        fields.push_back(field);
    }
    
    std::cout << "Parsed " << fields.size() << " fields:" << std::endl;
    for (size_t i = 0; i < fields.size(); i++) {
        std::cout << "Field " << i << ": " << fields[i] << std::endl;
    }
}

void convertDataTypes() {
    std::string numStr = "123.456";
    std::string intStr = "42";
    std::string boolStr = "1";
    
    // Convert string to numbers
    std::stringstream ss;
    
    ss << numStr;
    double doubleValue;
    ss >> doubleValue;
    std::cout << "String '" << numStr << "' to double: " << doubleValue << std::endl;
    
    ss.clear(); // Clear error flags
    ss.str(""); // Clear content
    
    ss << intStr;
    int intValue;
    ss >> intValue;
    std::cout << "String '" << intStr << "' to int: " << intValue << std::endl;
    
    // Convert numbers to string
    ss.clear();
    ss.str("");
    ss << intValue;
    std::string convertedBack = ss.str();
    std::cout << "Int " << intValue << " back to string: '" << convertedBack << "'" << std::endl;
}

void formatString() {
    int id = 1001;
    std::string name = "John Doe";
    double score = 95.5;
    
    std::stringstream ss;
    ss << "ID: " << id << ", Name: " << name << ", Score: " << score;
    
    std::string formatted = ss.str();
    std::cout << "Formatted string: " << formatted << std::endl;
    
    // Using manipulators
    ss.clear();
    ss.str("");
    ss << std::fixed << std::setprecision(2) << score;
    std::cout << "Score with 2 decimal places: " << ss.str() << std::endl;
}

int main() {
    std::cout << "=== CSV Parsing ===" << std::endl;
    parseCSVLine("John,Doe,30,New York");
    
    std::cout << "\n=== Type Conversion ===" << std::endl;
    convertDataTypes();
    
    std::cout << "\n=== String Formatting ===" << std::endl;
    formatString();
    
    return 0;
}
```

## Complete Example: Log File Manager

```cpp
#include <iostream>
#include <fstream>
#include <string>
#include <vector>
#include <ctime>
#include <sstream>

enum class LogLevel { DEBUG, INFO, WARNING, ERROR, CRITICAL };

struct LogEntry {
    std::timestamp;
    LogLevel level;
    std::string message;
    std::string source;
};

class LogManager {
private:
    std::string logFilename;
    std::ofstream logFile;
    LogLevel currentLogLevel;
    
    std::string levelToString(LogLevel level) {
        switch (level) {
            case LogLevel::DEBUG: return "DEBUG";
            case LogLevel::INFO: return "INFO";
            case LogLevel::WARNING: return "WARNING";
            case LogLevel::ERROR: return "ERROR";
            case LogLevel::CRITICAL: return "CRITICAL";
            default: return "UNKNOWN";
        }
    }
    
    std::string getCurrentTimestamp() {
        std::time_t now = std::time(nullptr);
        std::tm* localTime = std::localtime(&now);
        
        std::stringstream ss;
        ss << std::put_time(localTime, "%Y-%m-%d %H:%M:%S");
        return ss.str();
    }
    
public:
    LogManager(const std::string& filename, LogLevel minLevel = LogLevel::INFO) 
        : logFilename(filename), currentLogLevel(minLevel) {
        logFile.open(filename, std::ios::app);
        if (!logFile.is_open()) {
            std::cerr << "Error: Could not open log file!" << std::endl;
        }
    }
    
    ~LogManager() {
        if (logFile.is_open()) {
            logFile.close();
        }
    }
    
    void log(LogLevel level, const std::string& message, const std::string& source = "") {
        if (level < currentLogLevel || !logFile.is_open()) {
            return;
        }
        
        LogEntry entry;
        entry.timestamp = getCurrentTimestamp();
        entry.level = level;
        entry.message = message;
        entry.source = source;
        
        writeLogEntry(entry);
    }
    
    void debug(const std::string& message, const std::string& source = "") {
        log(LogLevel::DEBUG, message, source);
    }
    
    void info(const std::string& message, const std::string& source = "") {
        log(LogLevel::INFO, message, source);
    }
    
    void warning(const std::string& message, const std::string& source = "") {
        log(LogLevel::WARNING, message, source);
    }
    
    void error(const std::string& message, const std::string& source = "") {
        log(LogLevel::ERROR, message, source);
    }
    
    void critical(const std::string& message, const std::string& source = "") {
        log(LogLevel::CRITICAL, message, source);
    }
    
private:
    void writeLogEntry(const LogEntry& entry) {
        logFile << "[" << entry.timestamp << "] "
                << "[" << levelToString(entry.level) << "] ";
        
        if (!entry.source.empty()) {
            logFile << "[" << entry.source << "] ";
        }
        
        logFile << entry.message << std::endl;
        logFile.flush(); // Ensure immediate write
    }
    
public:
    std::vector<LogEntry> readLogEntries() {
        std::vector<LogEntry> entries;
        std::ifstream inFile(logFilename);
        
        if (!inFile.is_open()) {
            std::cerr << "Error: Could not open log file for reading!" << std::endl;
            return entries;
        }
        
        std::string line;
        while (std::getline(inFile, line)) {
            LogEntry entry = parseLogLine(line);
            if (!entry.timestamp.empty()) {
                entries.push_back(entry);
            }
        }
        
        inFile.close();
        return entries;
    }
    
    std::vector<LogEntry> filterByLevel(LogLevel level) {
        std::vector<LogEntry> allEntries = readLogEntries();
        std::vector<LogEntry> filtered;
        
        for (const auto& entry : allEntries) {
            if (entry.level == level) {
                filtered.push_back(entry);
            }
        }
        
        return filtered;
    }
    
    void displayRecentLogs(int count = 10) {
        std::vector<LogEntry> entries = readLogEntries();
        int start = std::max(0, static_cast<int>(entries.size()) - count);
        
        std::cout << "Recent " << (entries.size() - start) << " log entries:" << std::endl;
        std::cout << "----------------------------------------" << std::endl;
        
        for (int i = start; i < entries.size(); i++) {
            const auto& entry = entries[i];
            std::cout << "[" << entry.timestamp << "] "
                      << "[" << levelToString(entry.level) << "] ";
            
            if (!entry.source.empty()) {
                std::cout << "[" << entry.source << "] ";
            }
            
            std::cout << entry.message << std::endl;
        }
    }
    
private:
    LogEntry parseLogLine(const std::string& line) {
        LogEntry entry;
        
        // Parse format: [timestamp] [level] [source] message
        std::stringstream ss(line);
        std::string token;
        
        // Extract timestamp
        if (ss.peek() == '[') {
            ss.ignore(); // ignore '['
            std::getline(ss, entry.timestamp, ']');
            ss.ignore(2); // ignore "] "
        }
        
        // Extract level
        if (ss.peek() == '[') {
            ss.ignore(); // ignore '['
            std::string levelStr;
            std::getline(ss, levelStr, ']');
            
            if (levelStr == "DEBUG") entry.level = LogLevel::DEBUG;
            else if (levelStr == "INFO") entry.level = LogLevel::INFO;
            else if (levelStr == "WARNING") entry.level = LogLevel::WARNING;
            else if (levelStr == "ERROR") entry.level = LogLevel::ERROR;
            else if (levelStr == "CRITICAL") entry.level = LogLevel::CRITICAL;
            
            ss.ignore(1); // ignore " "
        }
        
        // Extract source (optional)
        if (ss.peek() == '[') {
            ss.ignore(); // ignore '['
            std::getline(ss, entry.source, ']');
            ss.ignore(1); // ignore " "
        }
        
        // Extract message
        std::getline(ss, entry.message);
        
        return entry;
    }
};

int main() {
    LogManager logger("application.log", LogLevel::DEBUG);
    
    // Log some messages
    logger.info("Application started", "main");
    logger.debug("Initializing components", "init");
    logger.warning("Low memory detected", "memory");
    logger.error("Failed to connect to database", "database");
    logger.critical("System crash imminent", "system");
    
    std::cout << "Log entries written to file." << std::endl;
    
    // Display recent logs
    std::cout << "\n";
    logger.displayRecentLogs();
    
    // Filter by level
    std::cout << "\nError logs only:" << std::endl;
    std::vector<LogEntry> errorLogs = logger.filterByLevel(LogLevel::ERROR);
    for (const auto& entry : errorLogs) {
        std::cout << "[" << entry.timestamp << "] " << entry.message << std::endl;
    }
    
    return 0;
}
```

## Practice Exercises

### Exercise 1: Text File Processor
Create a program that:
- Reads a text file
- Counts words, lines, and characters
- Finds the most frequent word
- Creates a summary report

### Exercise 2: Configuration Manager
Implement a configuration system:
- Read/write configuration files
- Support different data types
- Validate configuration values
- Handle configuration inheritance

### Exercise 3: Binary Database
Create a simple database:
- Store records in binary format
- Implement CRUD operations
- Add indexing for fast search
- Handle data migration

### Exercise 4: Log Analyzer
Build a log analysis tool:
- Parse different log formats
- Filter by time range and level
- Generate statistics and reports
- Export analysis results

## Key Takeaways
- C++ provides powerful stream classes for file I/O
- Text files are human-readable but less efficient
- Binary files are efficient but not human-readable
- Random access enables direct record manipulation
- Proper error handling is crucial for robust file operations
- String streams provide memory-based I/O operations
- Always close files to ensure data is written properly

## Next Module
In the next module, we'll explore object-oriented programming with classes in C++.