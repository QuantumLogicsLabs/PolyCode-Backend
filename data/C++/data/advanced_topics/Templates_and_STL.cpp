// Module 11: Templates and STL - Real-Life Examples
// This file demonstrates practical applications of templates and STL

#include <iostream>
#include <string>
#include <vector>
#include <list>
#include <deque>
#include <queue>
#include <stack>
#include <map>
#include <unordered_map>
#include <set>
#include <unordered_set>
#include <algorithm>
#include <numeric>
#include <memory>
#include <functional>

// Example 1: Generic Stack Template
template <typename T>
class GenericStack {
private:
    std::vector<T> elements;
    
public:
    void push(const T& element) {
        elements.push_back(element);
    }
    
    T pop() {
        if (elements.empty()) {
            throw std::runtime_error("Stack is empty");
        }
        
        T element = elements.back();
        elements.pop_back();
        return element;
    }
    
    T& top() {
        if (elements.empty()) {
            throw std::runtime_error("Stack is empty");
        }
        return elements.back();
    }
    
    bool empty() const {
        return elements.empty();
    }
    
    size_t size() const {
        return elements.size();
    }
    
    void display() const {
        std::cout << "Stack (top to bottom): ";
        for (auto it = elements.rbegin(); it != elements.rend(); ++it) {
            std::cout << *it << " ";
        }
        std::cout << std::endl;
    }
};

// Example 2: Generic Calculator Template
template <typename T>
class Calculator {
public:
    T add(T a, T b) {
        return a + b;
    }
    
    T subtract(T a, T b) {
        return a - b;
    }
    
    T multiply(T a, T b) {
        return a * b;
    }
    
    T divide(T a, T b) {
        if (b == 0) {
            throw std::runtime_error("Division by zero");
        }
        return a / b;
    }
    
    // Template specialization for strings
    template <>
    std::string add<std::string>(std::string a, std::string b) {
        return a + b;
    }
};

// Example 3: Smart Pointer Wrapper Template
template <typename T>
class SmartPointer {
private:
    T* ptr;
    bool isArray;
    size_t arraySize;
    
public:
    // Constructor for single object
    explicit SmartPointer(T* p = nullptr) : ptr(p), isArray(false), arraySize(0) {}
    
    // Constructor for array
    explicit SmartPointer(T* p, size_t size) : ptr(p), isArray(true), arraySize(size) {}
    
    // Destructor
    ~SmartPointer() {
        if (ptr) {
            if (isArray) {
                delete[] ptr;
            } else {
                delete ptr;
            }
        }
    }
    
    // Copy constructor (deep copy)
    SmartPointer(const SmartPointer& other) : isArray(other.isArray), arraySize(other.arraySize) {
        if (other.isArray) {
            ptr = new T[other.arraySize];
            for (size_t i = 0; i < arraySize; ++i) {
                ptr[i] = other.ptr[i];
            }
        } else {
            ptr = new T(*other.ptr);
        }
    }
    
    // Assignment operator
    SmartPointer& operator=(const SmartPointer& other) {
        if (this != &other) {
            // Clean up existing data
            if (ptr) {
                if (isArray) {
                    delete[] ptr;
                } else {
                    delete ptr;
                }
            }
            
            // Copy new data
            isArray = other.isArray;
            arraySize = other.arraySize;
            
            if (other.isArray) {
                ptr = new T[other.arraySize];
                for (size_t i = 0; i < arraySize; ++i) {
                    ptr[i] = other.ptr[i];
                }
            } else {
                ptr = new T(*other.ptr);
            }
        }
        return *this;
    }
    
    // Overload operators
    T& operator*() {
        return *ptr;
    }
    
    T* operator->() {
        return ptr;
    }
    
    T& operator[](size_t index) {
        if (!isArray || index >= arraySize) {
            throw std::out_of_range("Index out of bounds");
        }
        return ptr[index];
    }
    
    bool isNull() const {
        return ptr == nullptr;
    }
};

// Example 4: Generic Container Manager
template <typename T, typename Container = std::vector<T>>
class ContainerManager {
private:
    Container container;
    
public:
    void add(const T& element) {
        container.push_back(element);
    }
    
    void remove(const T& element) {
        auto it = std::find(container.begin(), container.end(), element);
        if (it != container.end()) {
            container.erase(it);
        }
    }
    
    bool contains(const T& element) const {
        return std::find(container.begin(), container.end(), element) != container.end();
    }
    
    size_t size() const {
        return container.size();
    }
    
    void display() const {
        std::cout << "Container contents: ";
        for (const auto& element : container) {
            std::cout << element << " ";
        }
        std::cout << std::endl;
    }
    
    void sort() {
        std::sort(container.begin(), container.end());
    }
    
    T sum() const {
        return std::accumulate(container.begin(), container.end(), T{});
    }
    
    T average() const {
        if (container.empty()) {
            return T{};
        }
        return sum() / static_cast<T>(container.size());
    }
    
    T max() const {
        if (container.empty()) {
            return T{};
        }
        return *std::max_element(container.begin(), container.end());
    }
    
    T min() const {
        if (container.empty()) {
            return T{};
        }
        return *std::min_element(container.begin(), container.end());
    }
};

// Example 5: Employee Management System using STL
class Employee {
private:
    int id;
    std::string name;
    std::string department;
    double salary;
    
public:
    Employee(int id, const std::string& name, const std::string& dept, double salary)
        : id(id), name(name), department(dept), salary(salary) {}
    
    int getId() const { return id; }
    std::string getName() const { return name; }
    std::string getDepartment() const { return department; }
    double getSalary() const { return salary; }
    
    void setSalary(double newSalary) { salary = newSalary; }
    
    void display() const {
        std::cout << "ID: " << id << ", Name: " << name 
                  << ", Dept: " << department << ", Salary: $" << salary << std::endl;
    }
    
    // Overload comparison operators for sorting
    bool operator<(const Employee& other) const {
        return salary < other.salary;
    }
    
    bool operator==(const Employee& other) const {
        return id == other.id;
    }
};

// Hash function for Employee (for unordered_map)
namespace std {
    template<>
    struct hash<Employee> {
        size_t operator()(const Employee& emp) const {
            return hash<int>()(emp.getId());
        }
    };
}

class EmployeeManager {
private:
    std::vector<Employee> employees;
    std::map<int, Employee> employeeMap;
    std::unordered_map<std::string, std::vector<Employee*>> departmentMap;
    
public:
    void addEmployee(const Employee& employee) {
        employees.push_back(employee);
        employeeMap[employee.getId()] = employee;
        departmentMap[employee.getDepartment()].push_back(&employees.back());
    }
    
    Employee* findEmployee(int id) {
        auto it = employeeMap.find(id);
        return (it != employeeMap.end()) ? &it->second : nullptr;
    }
    
    std::vector<Employee*> getEmployeesByDepartment(const std::string& department) {
        auto it = departmentMap.find(department);
        return (it != departmentMap.end()) ? it->second : std::vector<Employee*>();
    }
    
    void displayAllEmployees() {
        std::cout << "\n=== All Employees ===" << std::endl;
        for (const auto& employee : employees) {
            employee.display();
        }
    }
    
    void sortEmployeesBySalary() {
        std::sort(employees.begin(), employees.end());
        std::cout << "Employees sorted by salary" << std::endl;
    }
    
    void displayTopEarners(int count) {
        sortEmployeesBySalary();
        
        std::cout << "\n=== Top " << count << " Earners ===" << std::endl;
        int displayCount = std::min(count, static_cast<int>(employees.size()));
        
        for (int i = employees.size() - displayCount; i < employees.size(); ++i) {
            employees[i].display();
        }
    }
    
    void giveRaiseToDepartment(const std::string& department, double percentage) {
        auto empList = getEmployeesByDepartment(department);
        for (auto* emp : empList) {
            double newSalary = emp->getSalary() * (1.0 + percentage / 100.0);
            emp->setSalary(newSalary);
            
            // Update in map
            employeeMap[emp->getId()].setSalary(newSalary);
        }
        
        std::cout << "Gave " << percentage << "% raise to " << department << " department" << std::endl;
    }
    
    void displayDepartmentStatistics() {
        std::cout << "\n=== Department Statistics ===" << std::endl;
        
        std::map<std::string, std::pair<int, double>> deptStats;
        
        for (const auto& employee : employees) {
            std::string dept = employee.getDepartment();
            deptStats[dept].first++;  // Count
            deptStats[dept].second += employee.getSalary(); // Total salary
        }
        
        for (const auto& stat : deptStats) {
            double avgSalary = stat.second.second / stat.second.first;
            std::cout << stat.first << ": " << stat.second.first << " employees, "
                      << "Avg Salary: $" << std::fixed << std::setprecision(2) << avgSalary << std::endl;
        }
    }
};

// Example 6: Task Scheduler using STL
class Task {
private:
    std::string name;
    int priority;
    std::chrono::system_clock::time_point scheduledTime;
    
public:
    Task(const std::string& name, int priority, std::chrono::system_clock::time_point time)
        : name(name), priority(priority), scheduledTime(time) {}
    
    std::string getName() const { return name; }
    int getPriority() const { return priority; }
    std::chrono::system_clock::time_point getScheduledTime() const { return scheduledTime; }
    
    void display() const {
        auto timeT = std::chrono::system_clock::to_time_t(scheduledTime);
        std::cout << "Task: " << name << ", Priority: " << priority 
                  << ", Scheduled: " << std::ctime(&timeT);
    }
};

class TaskScheduler {
private:
    std::priority_queue<Task, std::vector<Task>, 
        std::function<bool(const Task&, const Task&)>> taskQueue;
    std::queue<Task> completedTasks;
    
public:
    TaskScheduler() : taskQueue([](const Task& a, const Task& b) {
        return a.getPriority() < b.getPriority(); // Min-heap (lower priority number = higher priority)
    }) {}
    
    void addTask(const std::string& name, int priority, 
                 std::chrono::system_clock::time_point scheduledTime) {
        Task task(name, priority, scheduledTime);
        taskQueue.push(task);
        std::cout << "Task added: " << name << std::endl;
    }
    
    void executeNextTask() {
        if (taskQueue.empty()) {
            std::cout << "No tasks to execute" << std::endl;
            return;
        }
        
        Task task = taskQueue.top();
        taskQueue.pop();
        
        std::cout << "Executing task: " << task.getName() << std::endl;
        task.display();
        
        completedTasks.push(task);
    }
    
    void displayPendingTasks() {
        std::cout << "\n=== Pending Tasks ===" << std::endl;
        
        // Create a copy to display without modifying the queue
        auto tempQueue = taskQueue;
        
        while (!tempQueue.empty()) {
            Task task = tempQueue.top();
            tempQueue.pop();
            task.display();
        }
    }
    
    void displayCompletedTasks() {
        std::cout << "\n=== Completed Tasks ===" << std::endl;
        
        std::queue<Task> tempQueue = completedTasks;
        while (!tempQueue.empty()) {
            Task task = tempQueue.front();
            tempQueue.pop();
            task.display();
        }
    }
    
    size_t getPendingTaskCount() const {
        return taskQueue.size();
    }
    
    size_t getCompletedTaskCount() const {
        return completedTasks.size();
    }
};

// Example 7: Data Processing Pipeline
class DataProcessor {
private:
    std::deque<int> dataQueue;
    std::stack<int> processedData;
    
public:
    void addData(int value) {
        dataQueue.push_back(value);
    }
    
    void processData() {
        if (dataQueue.empty()) {
            std::cout << "No data to process" << std::endl;
            return;
        }
        
        int value = dataQueue.front();
        dataQueue.pop_front();
        
        // Process the data (example: square the value)
        int processed = value * value;
        processedData.push(processed);
        
        std::cout << "Processed " << value << " -> " << processed << std::endl;
    }
    
    void displayDataQueue() const {
        std::cout << "Data Queue: ";
        for (const auto& value : dataQueue) {
            std::cout << value << " ";
        }
        std::cout << std::endl;
    }
    
    void displayProcessedData() const {
        std::cout << "Processed Data Stack: ";
        
        // Create a copy to display without destroying the stack
        std::stack<int> tempStack = processedData;
        std::vector<int> tempVector;
        
        while (!tempStack.empty()) {
            tempVector.push_back(tempStack.top());
            tempStack.pop();
        }
        
        // Display in reverse order (top to bottom)
        for (auto it = tempVector.rbegin(); it != tempVector.rend(); ++it) {
            std::cout << *it << " ";
        }
        std::cout << std::endl;
    }
    
    void reverseDataQueue() {
        std::reverse(dataQueue.begin(), dataQueue.end());
        std::cout << "Data queue reversed" << std::endl;
    }
    
    void sortDataQueue() {
        std::sort(dataQueue.begin(), dataQueue.end());
        std::cout << "Data queue sorted" << std::endl;
    }
};

int main() {
    std::cout << "=== Templates and STL - Real-Life Examples ===" << std::endl;
    std::cout << "Demonstrating practical applications of templates and STL\n" << std::endl;
    
    // Example 1: Generic Stack
    std::cout << "=== GENERIC STACK ===" << std::endl;
    GenericStack<int> intStack;
    intStack.push(10);
    intStack.push(20);
    intStack.push(30);
    
    intStack.display();
    std::cout << "Top element: " << intStack.top() << std::endl;
    std::cout << "Popped: " << intStack.pop() << std::endl;
    intStack.display();
    
    GenericStack<std::string> stringStack;
    stringStack.push("Hello");
    stringStack.push("World");
    stringStack.push("C++");
    
    stringStack.display();
    
    // Example 2: Generic Calculator
    std::cout << "\n=== GENERIC CALCULATOR ===" << std::endl;
    Calculator<int> intCalc;
    Calculator<double> doubleCalc;
    Calculator<std::string> stringCalc;
    
    std::cout << "Int: 5 + 3 = " << intCalc.add(5, 3) << std::endl;
    std::cout << "Double: 5.5 + 3.3 = " << doubleCalc.add(5.5, 3.3) << std::endl;
    std::cout << "String: Hello + World = " << stringCalc.add("Hello", "World") << std::endl;
    
    // Example 3: Smart Pointer
    std::cout << "\n=== SMART POINTER ===" << std::endl;
    SmartPointer<int> smartInt(new int(42));
    std::cout << "Smart pointer value: " << *smartInt << std::endl;
    
    SmartPointer<int> smartArray(new int[5], 5);
    for (size_t i = 0; i < 5; i++) {
        smartArray[i] = i * 10;
    }
    
    std::cout << "Smart array values: ";
    for (size_t i = 0; i < 5; i++) {
        std::cout << smartArray[i] << " ";
    }
    std::cout << std::endl;
    
    // Example 4: Container Manager
    std::cout << "\n=== CONTAINER MANAGER ===" << std::endl;
    ContainerManager<int> intManager;
    intManager.add(10);
    intManager.add(20);
    intManager.add(30);
    intManager.add(40);
    
    intManager.display();
    std::cout << "Sum: " << intManager.sum() << std::endl;
    std::cout << "Average: " << intManager.average() << std::endl;
    std::cout << "Max: " << intManager.max() << std::endl;
    std::cout << "Min: " << intManager.min() << std::endl;
    
    intManager.sort();
    intManager.display();
    
    // Example 5: Employee Management
    std::cout << "\n=== EMPLOYEE MANAGEMENT ===" << std::endl;
    EmployeeManager manager;
    
    manager.addEmployee(Employee(1001, "John Doe", "Engineering", 75000.0));
    manager.addEmployee(Employee(1002, "Jane Smith", "Marketing", 65000.0));
    manager.addEmployee(Employee(1003, "Bob Johnson", "Engineering", 80000.0));
    manager.addEmployee(Employee(1004, "Alice Brown", "Sales", 55000.0));
    manager.addEmployee(Employee(1005, "Charlie Wilson", "Engineering", 85000.0));
    
    manager.displayAllEmployees();
    manager.displayTopEarners(3);
    manager.giveRaiseToDepartment("Engineering", 5.0);
    manager.displayDepartmentStatistics();
    
    // Example 6: Task Scheduler
    std::cout << "\n=== TASK SCHEDULER ===" << std::endl;
    TaskScheduler scheduler;
    
    auto now = std::chrono::system_clock::now();
    scheduler.addTask("High Priority Task", 1, now);
    scheduler.addTask("Medium Priority Task", 5, now + std::chrono::hours(1));
    scheduler.addTask("Low Priority Task", 10, now + std::chrono::hours(2));
    
    scheduler.displayPendingTasks();
    scheduler.executeNextTask();
    scheduler.executeNextTask();
    
    scheduler.displayPendingTasks();
    scheduler.displayCompletedTasks();
    
    // Example 7: Data Processing Pipeline
    std::cout << "\n=== DATA PROCESSING PIPELINE ===" << std::endl;
    DataProcessor processor;
    
    processor.addData(5);
    processor.addData(3);
    processor.addData(8);
    processor.addData(1);
    processor.addData(9);
    
    processor.displayDataQueue();
    processor.sortDataQueue();
    processor.displayDataQueue();
    
    processor.processData();
    processor.processData();
    processor.processData();
    
    processor.displayDataQueue();
    processor.displayProcessedData();
    
    std::cout << "\n\n=== TEMPLATES AND STL SUMMARY ===" << std::endl;
    std::cout << "This example demonstrates various template and STL applications:" << std::endl;
    std::cout << "• Function templates for generic operations" << std::endl;
    std::cout << "• Class templates for reusable data structures" << std::endl;
    std::cout << "• Template specialization for specific types" << std::endl;
    std::cout << "• STL containers (vector, map, queue, stack, deque)" << std::endl;
    std::cout << "• STL algorithms (sort, find, accumulate, min/max)" << std::endl;
    std::cout << "• Smart pointers for memory management" << std::endl;
    std::cout << "• Custom allocators and resource management" << std::endl;
    std::cout << "• Function objects and lambda expressions" << std::endl;
    std::cout << "\nTemplates and STL provide powerful tools for generic programming!" << std::endl;
    
    return 0;
}