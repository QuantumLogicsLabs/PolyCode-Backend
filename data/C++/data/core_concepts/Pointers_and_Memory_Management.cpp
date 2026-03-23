// Module 7: Pointers and Memory Management - Real-Life Examples
// This file demonstrates practical applications of pointers and memory management

#include <iostream>
#include <string>
#include <vector>
#include <memory>
#include <stdexcept>

// Example 1: Dynamic Array Implementation
class DynamicArray {
private:
    int* data;
    size_t size;
    size_t capacity;
    
    void resize(size_t newCapacity) {
        int* newData = new int[newCapacity];
        for (size_t i = 0; i < size; ++i) {
            newData[i] = data[i];
        }
        delete[] data;
        data = newData;
        capacity = newCapacity;
    }
    
public:
    DynamicArray() : data(nullptr), size(0), capacity(0) {}
    
    explicit DynamicArray(size_t initialCapacity) : size(0), capacity(initialCapacity) {
        data = new int[capacity];
    }
    
    ~DynamicArray() {
        delete[] data;
    }
    
    // Copy constructor (deep copy)
    DynamicArray(const DynamicArray& other) : size(other.size), capacity(other.capacity) {
        data = new int[capacity];
        for (size_t i = 0; i < size; ++i) {
            data[i] = other.data[i];
        }
    }
    
    // Assignment operator (deep copy)
    DynamicArray& operator=(const DynamicArray& other) {
        if (this != &other) {
            delete[] data;
            size = other.size;
            capacity = other.capacity;
            data = new int[capacity];
            for (size_t i = 0; i < size; ++i) {
                data[i] = other.data[i];
            }
        }
        return *this;
    }
    
    // Move constructor
    DynamicArray(DynamicArray&& other) noexcept : data(other.data), size(other.size), capacity(other.capacity) {
        other.data = nullptr;
        other.size = 0;
        other.capacity = 0;
    }
    
    // Move assignment operator
    DynamicArray& operator=(DynamicArray&& other) noexcept {
        if (this != &other) {
            delete[] data;
            data = other.data;
            size = other.size;
            capacity = other.capacity;
            
            other.data = nullptr;
            other.size = 0;
            other.capacity = 0;
        }
        return *this;
    }
    
    void push_back(int value) {
        if (size >= capacity) {
            resize(capacity == 0 ? 1 : capacity * 2);
        }
        data[size++] = value;
    }
    
    int& operator[](size_t index) {
        if (index >= size) {
            throw std::out_of_range("Index out of bounds");
        }
        return data[index];
    }
    
    const int& operator[](size_t index) const {
        if (index >= size) {
            throw std::out_of_range("Index out of bounds");
        }
        return data[index];
    }
    
    size_t getSize() const { return size; }
    
    // Pointer-based access
    int* begin() { return data; }
    int* end() { return data + size; }
    const int* begin() const { return data; }
    const int* end() const { return data + size; }
    
    void display() const {
        std::cout << "Array [";
        for (size_t i = 0; i < size; ++i) {
            std::cout << data[i];
            if (i < size - 1) std::cout << ", ";
        }
        std::cout << "]" << std::endl;
    }
};

// Example 2: Smart Pointer-based Resource Management
class ResourceManager {
private:
    struct Resource {
        std::string name;
        int id;
        bool isInUse;
        
        Resource(const std::string& n, int i) : name(n), id(i), isInUse(false) {
            std::cout << "Resource '" << name << "' created." << std::endl;
        }
        
        ~Resource() {
            std::cout << "Resource '" << name << "' destroyed." << std::endl;
        }
        
        friend class ResourceManager;
    };
    
    std::vector<std::unique_ptr<Resource>> resources;
    
public:
    int createResource(const std::string& name) {
        static int nextId = 1;
        auto resource = std::make_unique<Resource>(name, nextId);
        int id = resource->id;
        resources.push_back(std::move(resource));
        return id;
    }
    
    Resource* getResource(int id) {
        for (auto& resource : resources) {
            if (resource->id == id && !resource->isInUse) {
                resource->isInUse = true;
                return resource.get();
            }
        }
        return nullptr;
    }
    
    void releaseResource(Resource* resource) {
        if (resource) {
            resource->isInUse = false;
        }
    }
    
    void displayResources() const {
        std::cout << "Available Resources:" << std::endl;
        for (const auto& resource : resources) {
            std::cout << "  ID: " << resource->id << ", Name: " << resource->name
                      << ", Status: " << (resource->isInUse ? "In Use" : "Available") << std::endl;
        }
    }
};

// Example 3: Linked List Implementation
template<typename T>
class LinkedList {
private:
    struct Node {
        T data;
        Node* next;
        
        Node(const T& value) : data(value), next(nullptr) {}
    };
    
    Node* head;
    Node* tail;
    size_t count;
    
public:
    LinkedList() : head(nullptr), tail(nullptr), count(0) {}
    
    ~LinkedList() {
        clear();
    }
    
    // Copy constructor
    LinkedList(const LinkedList& other) : head(nullptr), tail(nullptr), count(0) {
        Node* current = other.head;
        while (current) {
            push_back(current->data);
            current = current->next;
        }
    }
    
    // Assignment operator
    LinkedList& operator=(const LinkedList& other) {
        if (this != &other) {
            clear();
            Node* current = other.head;
            while (current) {
                push_back(current->data);
                current = current->next;
            }
        }
        return *this;
    }
    
    void push_back(const T& value) {
        Node* newNode = new Node(value);
        
        if (!head) {
            head = tail = newNode;
        } else {
            tail->next = newNode;
            tail = newNode;
        }
        count++;
    }
    
    void push_front(const T& value) {
        Node* newNode = new Node(value);
        
        if (!head) {
            head = tail = newNode;
        } else {
            newNode->next = head;
            head = newNode;
        }
        count++;
    }
    
    bool pop_front() {
        if (!head) return false;
        
        Node* temp = head;
        head = head->next;
        
        if (!head) {
            tail = nullptr;
        }
        
        delete temp;
        count--;
        return true;
    }
    
    bool pop_back() {
        if (!head) return false;
        
        if (head == tail) {
            delete head;
            head = tail = nullptr;
        } else {
            Node* current = head;
            while (current->next != tail) {
                current = current->next;
            }
            delete tail;
            tail = current;
            tail->next = nullptr;
        }
        
        count--;
        return true;
    }
    
    T* find(const T& value) {
        Node* current = head;
        while (current) {
            if (current->data == value) {
                return &(current->data);
            }
            current = current->next;
        }
        return nullptr;
    }
    
    void display() const {
        std::cout << "LinkedList: ";
        Node* current = head;
        while (current) {
            std::cout << current->data;
            if (current->next) std::cout << " -> ";
            current = current->next;
        }
        std::cout << std::endl;
    }
    
    size_t size() const { return count; }
    
    void clear() {
        while (pop_front()) {
            // Continue until empty
        }
    }
    
    // Iterator support
    class Iterator {
    private:
        Node* current;
        
    public:
        Iterator(Node* node) : current(node) {}
        
        T& operator*() { return current->data; }
        T* operator->() { return &current->data; }
        
        Iterator& operator++() {
            current = current->next;
            return *this;
        }
        
        bool operator!=(const Iterator& other) const {
            return current != other.current;
        }
        
        bool operator==(const Iterator& other) const {
            return current == other.current;
        }
    };
    
    Iterator begin() { return Iterator(head); }
    Iterator end() { return Iterator(nullptr); }
};

// Example 4: Memory Pool Allocator
class MemoryPool {
private:
    struct Block {
        void* data;
        bool inUse;
        Block* next;
    };
    
    Block* freeList;
    size_t blockSize;
    size_t totalBlocks;
    std::vector<void*> allocatedChunks;
    
public:
    MemoryPool(size_t blockSz, size_t numBlocks) 
        : blockSize(blockSz), totalBlocks(numBlocks), freeList(nullptr) {
        
        // Allocate a large chunk of memory
        char* chunk = new char[blockSize * numBlocks];
        allocatedChunks.push_back(chunk);
        
        // Split chunk into blocks and add to free list
        for (size_t i = 0; i < numBlocks; ++i) {
            Block* block = reinterpret_cast<Block*>(chunk + i * blockSize);
            block->data = block + 1; // Data area starts after Block header
            block->inUse = false;
            block->next = freeList;
            freeList = block;
        }
    }
    
    ~MemoryPool() {
        // Free all allocated chunks
        for (void* chunk : allocatedChunks) {
            delete[] static_cast<char*>(chunk);
        }
    }
    
    void* allocate() {
        if (!freeList) {
            return nullptr; // Pool exhausted
        }
        
        Block* block = freeList;
        freeList = freeList->next;
        block->inUse = true;
        block->next = nullptr;
        
        return block->data;
    }
    
    void deallocate(void* ptr) {
        if (!ptr) return;
        
        // Find the block header (data is stored right after the header)
        Block* block = reinterpret_cast<Block*>(ptr) - 1;
        
        if (!block->inUse) {
            return; // Already freed
        }
        
        block->inUse = false;
        block->next = freeList;
        freeList = block;
    }
    
    size_t getAvailableBlocks() const {
        size_t count = 0;
        Block* current = freeList;
        while (current) {
            count++;
            current = current->next;
        }
        return count;
    }
    
    void displayStats() const {
        std::cout << "Memory Pool Stats:" << std::endl;
        std::cout << "  Total Blocks: " << totalBlocks << std::endl;
        std::cout << "  Available: " << getAvailableBlocks() << std::endl;
        std::cout << "  In Use: " << (totalBlocks - getAvailableBlocks()) << std::endl;
        std::cout << "  Block Size: " << blockSize << " bytes" << std::endl;
    }
};

// Example 5: Smart Pointer Demonstration
class SmartPointerDemo {
public:
    static void demonstrateUniquePtr() {
        std::cout << "\n=== UNIQUE POINTER DEMO ===" << std::endl;
        
        // Creating unique pointers
        auto ptr1 = std::make_unique<int>(42);
        std::cout << "Value: " << *ptr1 << std::endl;
        
        // Transfer ownership
        auto ptr2 = std::move(ptr1);
        std::cout << "After move, value: " << *ptr2 << std::endl;
        
        // ptr1 is now null
        std::cout << "ptr1 is null: " << (ptr1 == nullptr) << std::endl;
        
        // Automatic cleanup when ptr2 goes out of scope
    }
    
    static void demonstrateSharedPtr() {
        std::cout << "\n=== SHARED POINTER DEMO ===" << std::endl;
        
        auto shared1 = std::make_shared<std::string>("Hello, World!");
        std::cout << "Value: " << *shared1 << std::endl;
        std::cout << "Use count: " << shared1.use_count() << std::endl;
        
        {
            auto shared2 = shared1;
            std::cout << "After copy, use count: " << shared1.use_count() << std::endl;
            std::cout << "Value from shared2: " << *shared2 << std::endl;
        }
        
        std::cout << "After shared2 goes out of scope, use count: " << shared1.use_count() << std::endl;
    }
    
    static void demonstrateWeakPtr() {
        std::cout << "\n=== WEAK POINTER DEMO ===" << std::endl;
        
        auto shared = std::make_shared<int>(100);
        std::weak_ptr<int> weak = shared;
        
        std::cout << "Shared use count: " << shared.use_count() << std::endl;
        std::cout << "Weak expired: " << weak.expired() << std::endl;
        
        if (auto locked = weak.lock()) {
            std::cout << "Locked value: " << *locked << std::endl;
        }
        
        shared.reset();
        std::cout << "After shared reset, weak expired: " << weak.expired() << std::endl;
    }
};

int main() {
    std::cout << "=== Pointers and Memory Management - Real-Life Examples ===" << std::endl;
    std::cout << "Demonstrating practical applications of pointers and memory management\n" << std::endl;
    
    // Example 1: Dynamic Array
    std::cout << "=== DYNAMIC ARRAY ===" << std::endl;
    DynamicArray arr;
    arr.push_back(10);
    arr.push_back(20);
    arr.push_back(30);
    arr.push_back(40);
    arr.push_back(50);
    
    arr.display();
    
    // Test copy constructor
    DynamicArray arr2 = arr;
    arr2[0] = 99;
    std::cout << "Original: ";
    arr.display();
    std::cout << "Copy: ";
    arr2.display();
    
    // Test move constructor
    DynamicArray arr3 = std::move(arr2);
    std::cout << "After move: ";
    arr3.display();
    
    // Pointer arithmetic
    std::cout << "Using pointers to iterate: ";
    for (int* ptr = arr3.begin(); ptr != arr3.end(); ++ptr) {
        std::cout << *ptr << " ";
    }
    std::cout << std::endl;
    
    // Example 2: Resource Manager
    std::cout << "\n=== RESOURCE MANAGER ===" << std::endl;
    ResourceManager manager;
    int res1 = manager.createResource("Database Connection");
    int res2 = manager.createResource("File Handle");
    int res3 = manager.createResource("Network Socket");
    
    manager.displayResources();
    
    auto* resource = manager.getResource(res1);
    if (resource) {
        std::cout << "Acquired resource: " << resource->name << std::endl;
        manager.releaseResource(resource);
    }
    
    manager.displayResources();
    
    // Example 3: Linked List
    std::cout << "\n=== LINKED LIST ===" << std::endl;
    LinkedList<std::string> list;
    list.push_back("First");
    list.push_back("Second");
    list.push_back("Third");
    list.push_front("Zero");
    
    list.display();
    
    // Find operation
    if (auto* found = list.find("Second")) {
        std::cout << "Found: " << *found << std::endl;
    }
    
    // Iterator usage
    std::cout << "Using iterators: ";
    for (auto it = list.begin(); it != list.end(); ++it) {
        std::cout << "[" << *it << "] ";
    }
    std::cout << std::endl;
    
    list.pop_front();
    list.pop_back();
    list.display();
    
    // Example 4: Memory Pool
    std::cout << "\n=== MEMORY POOL ===" << std::endl;
    MemoryPool pool(64, 10); // 10 blocks of 64 bytes each
    pool.displayStats();
    
    // Allocate some memory
    void* ptr1 = pool.allocate();
    void* ptr2 = pool.allocate();
    void* ptr3 = pool.allocate();
    
    pool.displayStats();
    
    // Deallocate memory
    pool.deallocate(ptr2);
    pool.displayStats();
    
    // Allocate again
    void* ptr4 = pool.allocate();
    pool.displayStats();
    
    // Example 5: Smart Pointers
    SmartPointerDemo::demonstrateUniquePtr();
    SmartPointerDemo::demonstrateSharedPtr();
    SmartPointerDemo::demonstrateWeakPtr();
    
    std::cout << "\n\n=== MEMORY MANAGEMENT SUMMARY ===" << std::endl;
    std::cout << "This example demonstrates various memory management concepts:" << std::endl;
    std::cout << "• Dynamic arrays with manual memory management" << std::endl;
    std::cout << "• RAII with smart pointers for automatic cleanup" << std::endl;
    std::cout << "• Linked list implementation with pointers" << std::endl;
    std::cout << "• Custom memory pool for efficient allocation" << std::endl;
    std::cout << "• Different types of smart pointers (unique, shared, weak)" << std::endl;
    std::cout << "• Copy and move semantics for proper resource management" << std::endl;
    std::cout << "\nProper memory management is crucial for robust and efficient C++ applications!" << std::endl;
    
    return 0;
}