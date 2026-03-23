// Module 8: Structures, Unions, and Enums - Real-Life Examples
// This file demonstrates practical applications of structures, unions, and enums

#include <iostream>
#include <string>
#include <vector>
#include <iomanip>

// Example 1: Employee Management System with Structures
enum class Department {
    ENGINEERING,
    MARKETING,
    SALES,
    HUMAN_RESOURCES,
    FINANCE,
    IT
};

enum class EmploymentStatus {
    FULL_TIME,
    PART_TIME,
    CONTRACT,
    INTERN
};

enum class SkillLevel {
    BEGINNER,
    INTERMEDIATE,
    ADVANCED,
    EXPERT
};

struct Address {
    std::string street;
    std::string city;
    std::string state;
    std::string zipCode;
    std::string country;
    
    void display() const {
        std::cout << street << ", " << city << ", " << state << " " << zipCode << std::endl;
        std::cout << country << std::endl;
    }
};

struct ContactInfo {
    std::string email;
    std::string phone;
    std::string emergencyContact;
    std::string emergencyPhone;
    
    void display() const {
        std::cout << "Email: " << email << std::endl;
        std::cout << "Phone: " << phone << std::endl;
        std::cout << "Emergency: " << emergencyContact << " (" << emergencyPhone << ")" << std::endl;
    }
};

struct Skill {
    std::string name;
    SkillLevel level;
    int yearsOfExperience;
    
    void display() const {
        std::cout << name << " - ";
        switch (level) {
            case SkillLevel::BEGINNER: std::cout << "Beginner"; break;
            case SkillLevel::INTERMEDIATE: std::cout << "Intermediate"; break;
            case SkillLevel::ADVANCED: std::cout << "Advanced"; break;
            case SkillLevel::EXPERT: std::cout << "Expert"; break;
        }
        std::cout << " (" << yearsOfExperience << " years)" << std::endl;
    }
};

struct Employee {
    int employeeId;
    std::string firstName;
    std::string lastName;
    Department department;
    EmploymentStatus status;
    double salary;
    Address address;
    ContactInfo contact;
    std::vector<Skill> skills;
    std::string hireDate;
    
    void displayBasicInfo() const {
        std::cout << "ID: " << employeeId << std::endl;
        std::cout << "Name: " << firstName << " " << lastName << std::endl;
        std::cout << "Department: ";
        switch (department) {
            case Department::ENGINEERING: std::cout << "Engineering"; break;
            case Department::MARKETING: std::cout << "Marketing"; break;
            case Department::SALES: std::cout << "Sales"; break;
            case Department::HUMAN_RESOURCES: std::cout << "Human Resources"; break;
            case Department::FINANCE: std::cout << "Finance"; break;
            case Department::IT: std::cout << "IT"; break;
        }
        std::cout << std::endl;
        
        std::cout << "Status: ";
        switch (status) {
            case EmploymentStatus::FULL_TIME: std::cout << "Full-time"; break;
            case EmploymentStatus::PART_TIME: std::cout << "Part-time"; break;
            case EmploymentStatus::CONTRACT: std::cout << "Contract"; break;
            case EmploymentStatus::INTERN: std::cout << "Intern"; break;
        }
        std::cout << std::endl;
        
        std::cout << "Salary: $" << std::fixed << std::setprecision(2) << salary << std::endl;
        std::cout << "Hire Date: " << hireDate << std::endl;
    }
    
    void displayFullInfo() const {
        displayBasicInfo();
        std::cout << "\nAddress:" << std::endl;
        address.display();
        std::cout << "\nContact Information:" << std::endl;
        contact.display();
        
        if (!skills.empty()) {
            std::cout << "\nSkills:" << std::endl;
            for (const auto& skill : skills) {
                skill.display();
            }
        }
    }
    
    void addSkill(const std::string& skillName, SkillLevel level, int years) {
        skills.push_back({skillName, level, years});
    }
    
    double calculateAnnualBonus() const {
        double bonusRate = 0.0;
        
        // Bonus based on department
        switch (department) {
            case Department::ENGINEERING: bonusRate = 0.15; break;
            case Department::SALES: bonusRate = 0.20; break;
            case Department::MARKETING: bonusRate = 0.12; break;
            case Department::FINANCE: bonusRate = 0.18; break;
            case Department::IT: bonusRate = 0.14; break;
            case Department::HUMAN_RESOURCES: bonusRate = 0.10; break;
        }
        
        // Additional bonus based on status
        switch (status) {
            case EmploymentStatus::FULL_TIME: bonusRate += 0.05; break;
            case EmploymentStatus::PART_TIME: bonusRate -= 0.03; break;
            case EmploymentStatus::CONTRACT: bonusRate -= 0.05; break;
            case EmploymentStatus::INTERN: bonusRate -= 0.08; break;
        }
        
        return salary * bonusRate;
    }
};

class EmployeeManager {
private:
    std::vector<Employee> employees;
    int nextId;
    
public:
    EmployeeManager() : nextId(1001) {}
    
    void addEmployee(const std::string& firstName, const std::string& lastName,
                    Department dept, EmploymentStatus status, double salary,
                    const Address& addr, const ContactInfo& contact, const std::string& hireDate) {
        
        Employee emp;
        emp.employeeId = nextId++;
        emp.firstName = firstName;
        emp.lastName = lastName;
        emp.department = dept;
        emp.status = status;
        emp.salary = salary;
        emp.address = addr;
        emp.contact = contact;
        emp.hireDate = hireDate;
        
        employees.push_back(emp);
        std::cout << "Employee " << firstName << " " << lastName << " added with ID " << emp.employeeId << std::endl;
    }
    
    void displayAllEmployees() const {
        std::cout << "\n=== ALL EMPLOYEES ===" << std::endl;
        for (const auto& emp : employees) {
            emp.displayBasicInfo();
            std::cout << "---" << std::endl;
        }
    }
    
    void displayEmployeeById(int id) const {
        for (const auto& emp : employees) {
            if (emp.employeeId == id) {
                emp.displayFullInfo();
                return;
            }
        }
        std::cout << "Employee with ID " << id << " not found." << std::endl;
    }
    
    void displayEmployeesByDepartment(Department dept) const {
        std::cout << "\n=== EMPLOYEES IN ";
        switch (dept) {
            case Department::ENGINEERING: std::cout << "ENGINEERING"; break;
            case Department::MARKETING: std::cout << "MARKETING"; break;
            case Department::SALES: std::cout << "SALES"; break;
            case Department::HUMAN_RESOURCES: std::cout << "HUMAN RESOURCES"; break;
            case Department::FINANCE: std::cout << "FINANCE"; break;
            case Department::IT: std::cout << "IT"; break;
        }
        std::cout << " ===" << std::endl;
        
        bool found = false;
        for (const auto& emp : employees) {
            if (emp.department == dept) {
                emp.displayBasicInfo();
                std::cout << "---" << std::endl;
                found = true;
            }
        }
        
        if (!found) {
            std::cout << "No employees found in this department." << std::endl;
        }
    }
    
    void calculateTotalPayroll() const {
        double total = 0;
        for (const auto& emp : employees) {
            total += emp.salary;
        }
        
        std::cout << "\nTotal Payroll: $" << std::fixed << std::setprecision(2) << total << std::endl;
    }
    
    void displayBonusReport() const {
        std::cout << "\n=== BONUS REPORT ===" << std::endl;
        double totalBonus = 0;
        
        for (const auto& emp : employees) {
            double bonus = emp.calculateAnnualBonus();
            totalBonus += bonus;
            std::cout << emp.firstName << " " << emp.lastName << " (ID: " << emp.employeeId 
                      << "): $" << std::fixed << std::setprecision(2) << bonus << std::endl;
        }
        
        std::cout << "\nTotal Bonus Pool: $" << std::fixed << std::setprecision(2) << totalBonus << std::endl;
    }
};

// Example 2: Network Packet Processing with Unions
enum class PacketType {
    DATA,
    CONTROL,
    ERROR,
    HEARTBEAT
};

struct DataPacket {
    int sourceId;
    int destinationId;
    int sequenceNumber;
    char payload[256];
};

struct ControlPacket {
    int sourceId;
    int destinationId;
    int commandCode;
    int parameter;
};

struct ErrorPacket {
    int sourceId;
    int errorCode;
    char errorMessage[128];
};

struct HeartbeatPacket {
    int sourceId;
    int timestamp;
    int status;
};

union PacketData {
    DataPacket data;
    ControlPacket control;
    ErrorPacket error;
    HeartbeatPacket heartbeat;
};

struct NetworkPacket {
    PacketType type;
    int priority;
    int size;
    PacketData data;
    
    void display() const {
        std::cout << "Packet Type: ";
        switch (type) {
            case PacketType::DATA: std::cout << "DATA"; break;
            case PacketType::CONTROL: std::cout << "CONTROL"; break;
            case PacketType::ERROR: std::cout << "ERROR"; break;
            case PacketType::HEARTBEAT: std::cout << "HEARTBEAT"; break;
        }
        std::cout << std::endl;
        
        std::cout << "Priority: " << priority << std::endl;
        std::cout << "Size: " << size << " bytes" << std::endl;
        
        switch (type) {
            case PacketType::DATA:
                std::cout << "Source: " << data.data.sourceId << std::endl;
                std::cout << "Destination: " << data.data.destinationId << std::endl;
                std::cout << "Sequence: " << data.data.sequenceNumber << std::endl;
                std::cout << "Payload: " << data.data.payload << std::endl;
                break;
                
            case PacketType::CONTROL:
                std::cout << "Source: " << data.control.sourceId << std::endl;
                std::cout << "Destination: " << data.control.destinationId << std::endl;
                std::cout << "Command: " << data.control.commandCode << std::endl;
                std::cout << "Parameter: " << data.control.parameter << std::endl;
                break;
                
            case PacketType::ERROR:
                std::cout << "Source: " << data.error.sourceId << std::endl;
                std::cout << "Error Code: " << data.error.errorCode << std::endl;
                std::cout << "Message: " << data.error.errorMessage << std::endl;
                break;
                
            case PacketType::HEARTBEAT:
                std::cout << "Source: " << data.heartbeat.sourceId << std::endl;
                std::cout << "Timestamp: " << data.heartbeat.timestamp << std::endl;
                std::cout << "Status: " << data.heartbeat.status << std::endl;
                break;
        }
    }
};

class NetworkProcessor {
private:
    std::vector<NetworkPacket> packets;
    
public:
    void addDataPacket(int source, int dest, int seq, const std::string& payload) {
        NetworkPacket packet;
        packet.type = PacketType::DATA;
        packet.priority = 1;
        packet.size = sizeof(DataPacket);
        
        packet.data.data.sourceId = source;
        packet.data.data.destinationId = dest;
        packet.data.data.sequenceNumber = seq;
        strncpy_s(packet.data.data.payload, payload.c_str(), sizeof(packet.data.data.payload) - 1);
        packet.data.data.payload[sizeof(packet.data.data.payload) - 1] = '\0';
        
        packets.push_back(packet);
    }
    
    void addControlPacket(int source, int dest, int command, int param) {
        NetworkPacket packet;
        packet.type = PacketType::CONTROL;
        packet.priority = 2;
        packet.size = sizeof(ControlPacket);
        
        packet.data.control.sourceId = source;
        packet.data.control.destinationId = dest;
        packet.data.control.commandCode = command;
        packet.data.control.parameter = param;
        
        packets.push_back(packet);
    }
    
    void addErrorPacket(int source, int errorCode, const std::string& message) {
        NetworkPacket packet;
        packet.type = PacketType::ERROR;
        packet.priority = 3;
        packet.size = sizeof(ErrorPacket);
        
        packet.data.error.sourceId = source;
        packet.data.error.errorCode = errorCode;
        strncpy_s(packet.data.error.errorMessage, message.c_str(), sizeof(packet.data.error.errorMessage) - 1);
        packet.data.error.errorMessage[sizeof(packet.data.error.errorMessage) - 1] = '\0';
        
        packets.push_back(packet);
    }
    
    void addHeartbeatPacket(int source, int timestamp, int status) {
        NetworkPacket packet;
        packet.type = PacketType::HEARTBEAT;
        packet.priority = 0;
        packet.size = sizeof(HeartbeatPacket);
        
        packet.data.heartbeat.sourceId = source;
        packet.data.heartbeat.timestamp = timestamp;
        packet.data.heartbeat.status = status;
        
        packets.push_back(packet);
    }
    
    void processPackets() {
        std::cout << "\n=== PROCESSING NETWORK PACKETS ===" << std::endl;
        
        // Sort by priority
        std::sort(packets.begin(), packets.end(), 
                 [](const NetworkPacket& a, const NetworkPacket& b) {
                     return a.priority < b.priority;
                 });
        
        for (const auto& packet : packets) {
            packet.display();
            std::cout << "---" << std::endl;
        }
    }
    
    void getPacketStatistics() {
        int dataCount = 0, controlCount = 0, errorCount = 0, heartbeatCount = 0;
        int totalSize = 0;
        
        for (const auto& packet : packets) {
            totalSize += packet.size;
            
            switch (packet.type) {
                case PacketType::DATA: dataCount++; break;
                case PacketType::CONTROL: controlCount++; break;
                case PacketType::ERROR: errorCount++; break;
                case PacketType::HEARTBEAT: heartbeatCount++; break;
            }
        }
        
        std::cout << "\n=== PACKET STATISTICS ===" << std::endl;
        std::cout << "Total Packets: " << packets.size() << std::endl;
        std::cout << "Total Size: " << totalSize << " bytes" << std::endl;
        std::cout << "Data Packets: " << dataCount << std::endl;
        std::cout << "Control Packets: " << controlCount << std::endl;
        std::cout << "Error Packets: " << errorCount << std::endl;
        std::cout << "Heartbeat Packets: " << heartbeatCount << std::endl;
    }
};

// Example 3: Graphics System with Bit Fields
enum class ColorFormat {
    RGB_24,
    RGBA_32,
    RGB_16,
    GRAYSCALE_8
};

struct Color16Bit {
    unsigned int blue : 5;
    unsigned int green : 6;
    unsigned int red : 5;
    
    void display() const {
        std::cout << "Color16Bit - R:" << red << " G:" << green << " B:" << blue << std::endl;
    }
};

struct Color32Bit {
    unsigned int blue : 8;
    unsigned int green : 8;
    unsigned int red : 8;
    unsigned int alpha : 8;
    
    void display() const {
        std::cout << "Color32Bit - R:" << red << " G:" << green << " B:" << blue << " A:" << alpha << std::endl;
    }
};

struct GraphicsHeader {
    unsigned int width : 12;  // Max width: 4095
    unsigned int height : 12; // Max height: 4095
    unsigned int format : 2;  // 0=RGB16, 1=RGB24, 2=RGBA32, 3=Grayscale
    unsigned int compressed : 1;
    unsigned int reserved : 5;
    
    void display() const {
        std::cout << "Graphics Header:" << std::endl;
        std::cout << "  Dimensions: " << width << "x" << height << std::endl;
        std::cout << "  Format: ";
        switch (static_cast<ColorFormat>(format)) {
            case ColorFormat::RGB_16: std::cout << "RGB 16-bit"; break;
            case ColorFormat::RGBA_32: std::cout << "RGBA 32-bit"; break;
            case ColorFormat::RGB_24: std::cout << "RGB 24-bit"; break;
            case ColorFormat::GRAYSCALE_8: std::cout << "Grayscale 8-bit"; break;
        }
        std::cout << std::endl;
        std::cout << "  Compressed: " << (compressed ? "Yes" : "No") << std::endl;
    }
};

union ColorData {
    Color16Bit color16;
    Color32Bit color32;
    unsigned char grayscale;
    
    void display(ColorFormat format) const {
        switch (format) {
            case ColorFormat::RGB_16: color16.display(); break;
            case ColorFormat::RGBA_32: color32.display(); break;
            case ColorFormat::GRAYSCALE_8: 
                std::cout << "Grayscale: " << static_cast<int>(grayscale) << std::endl; 
                break;
            default: std::cout << "Unknown format" << std::endl; break;
        }
    }
};

struct Pixel {
    int x, y;
    ColorData color;
    GraphicsHeader header;
    
    void display() const {
        std::cout << "Pixel at (" << x << ", " << y << ")" << std::endl;
        header.display();
        color.display(static_cast<ColorFormat>(header.format));
    }
};

class GraphicsProcessor {
private:
    std::vector<Pixel> pixels;
    
public:
    void addPixel(int x, int y, ColorFormat format) {
        Pixel pixel;
        pixel.x = x;
        pixel.y = y;
        
        // Set header
        pixel.header.width = x;
        pixel.header.height = y;
        pixel.header.format = static_cast<int>(format);
        pixel.header.compressed = 0;
        
        // Set color based on format
        switch (format) {
            case ColorFormat::RGB_16:
                pixel.color.color16.red = 31;  // Max 5-bit
                pixel.color.color16.green = 63; // Max 6-bit
                pixel.color.color16.blue = 31;  // Max 5-bit
                break;
                
            case ColorFormat::RGBA_32:
                pixel.color.color32.red = 255;
                pixel.color.color32.green = 128;
                pixel.color.color32.blue = 64;
                pixel.color.color32.alpha = 255;
                break;
                
            case ColorFormat::GRAYSCALE_8:
                pixel.color.grayscale = 128;
                break;
                
            default:
                break;
        }
        
        pixels.push_back(pixel);
    }
    
    void displayAllPixels() const {
        std::cout << "\n=== PIXEL DATA ===" << std::endl;
        for (const auto& pixel : pixels) {
            pixel.display();
            std::cout << "---" << std::endl;
        }
    }
    
    void calculateMemoryUsage() const {
        int totalSize = 0;
        
        for (const auto& pixel : pixels) {
            switch (static_cast<ColorFormat>(pixel.header.format)) {
                case ColorFormat::RGB_16: totalSize += 2; break;
                case ColorFormat::RGB_24: totalSize += 3; break;
                case ColorFormat::RGBA_32: totalSize += 4; break;
                case ColorFormat::GRAYSCALE_8: totalSize += 1; break;
            }
        }
        
        std::cout << "\nTotal memory usage: " << totalSize << " bytes" << std::endl;
    }
};

int main() {
    std::cout << "=== Structures, Unions, and Enums - Real-Life Examples ===" << std::endl;
    std::cout << "Demonstrating practical applications of data structures\n" << std::endl;
    
    // Example 1: Employee Management System
    std::cout << "=== EMPLOYEE MANAGEMENT SYSTEM ===" << std::endl;
    EmployeeManager manager;
    
    Address addr1 = {"123 Main St", "New York", "NY", "10001", "USA"};
    ContactInfo contact1 = {"john@company.com", "555-1234", "Jane Doe", "555-9999"};
    
    manager.addEmployee("John", "Smith", Department::ENGINEERING, EmploymentStatus::FULL_TIME, 
                        85000.0, addr1, contact1, "2022-01-15");
    
    Address addr2 = {"456 Oak Ave", "Los Angeles", "CA", "90210", "USA"};
    ContactInfo contact2 = {"jane@company.com", "555-5678", "John Smith", "555-4321"};
    
    manager.addEmployee("Jane", "Doe", Department::MARKETING, EmploymentStatus::FULL_TIME, 
                        72000.0, addr2, contact2, "2021-06-20");
    
    // Add skills to employees
    // In a real system, we'd need to find the employee first and add skills
    
    manager.displayAllEmployees();
    manager.displayEmployeesByDepartment(Department::ENGINEERING);
    manager.calculateTotalPayroll();
    manager.displayBonusReport();
    
    // Example 2: Network Packet Processing
    std::cout << "\n=== NETWORK PACKET PROCESSING ===" << std::endl;
    NetworkProcessor processor;
    
    processor.addDataPacket(1001, 1002, 1, "Hello World");
    processor.addControlPacket(1001, 1002, 1, 0);
    processor.addHeartbeatPacket(1001, 1642726400, 1);
    processor.addErrorPacket(1002, 404, "Not Found");
    
    processor.processPackets();
    processor.getPacketStatistics();
    
    // Example 3: Graphics System
    std::cout << "\n=== GRAPHICS PROCESSING SYSTEM ===" << std::endl;
    GraphicsProcessor graphics;
    
    graphics.addPixel(100, 200, ColorFormat::RGB_16);
    graphics.addPixel(150, 250, ColorFormat::RGBA_32);
    graphics.addPixel(75, 125, ColorFormat::GRAYSCALE_8);
    
    graphics.displayAllPixels();
    graphics.calculateMemoryUsage();
    
    std::cout << "\n\n=== DATA STRUCTURES SUMMARY ===" << std::endl;
    std::cout << "This example demonstrates various data structure applications:" << std::endl;
    std::cout << "• Structures for organizing complex employee data" << std::endl;
    std::cout << "• Enums for type-safe categorical data" << std::endl;
    std::cout << "• Unions for memory-efficient data storage" << std::endl;
    std::cout << "• Bit fields for compact data representation" << std::endl;
    std::cout << "• Nested structures for hierarchical data" << std::endl;
    std::cout << "• Type-safe operations with enum classes" << std::endl;
    std::cout << "\nStructures, unions, and enums provide powerful ways to organize and optimize data!" << std::endl;
    
    return 0;
}