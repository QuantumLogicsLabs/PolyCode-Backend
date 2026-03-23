# Module 7: Pointers and Memory Management

## Learning Objectives
- Understand what pointers are and how they work
- Learn pointer declaration, initialization, and dereferencing
- Master pointer arithmetic and operations
- Understand dynamic memory allocation (new/delete)
- Learn about smart pointers (C++11)
- Understand memory leaks and how to avoid them

## What are Pointers?

A pointer is a variable that stores the memory address of another variable. Pointers provide direct access to memory locations and are essential for dynamic memory management.

### Basic Pointer Concepts
```cpp
#include <iostream>

int main() {
    int number = 42;
    int* ptr;          // Pointer declaration
    
    ptr = &number;     // Store address of 'number' in pointer
    
    std::cout << "Value of number: " << number << std::endl;
    std::cout << "Address of number: " << &number << std::endl;
    std::cout << "Value of pointer: " << ptr << std::endl;
    std::cout << "Address of pointer: " << &ptr << std::endl;
    std::cout << "Value pointed to by pointer: " << *ptr << std::endl;
    
    // Modifying value through pointer
    *ptr = 100;
    std::cout << "Modified number through pointer: " << number << std::endl;
    
    return 0;
}
```

## Pointer Declaration and Initialization

### Different Ways to Initialize Pointers
```cpp
#include <iostream>

int main() {
    int var = 10;
    
    // Method 1: Declare and assign separately
    int* ptr1;
    ptr1 = &var;
    
    // Method 2: Declare and initialize together
    int* ptr2 = &var;
    
    // Method 3: Multiple pointer declaration
    int* ptr3 = &var, *ptr4 = &var;
    
    // Method 4: Null pointer (C++11)
    int* ptr5 = nullptr;
    
    // Method 5: Null pointer (traditional)
    int* ptr6 = NULL;
    
    std::cout << "ptr1 points to: " << *ptr1 << std::endl;
    std::cout << "ptr2 points to: " << *ptr2 << std::endl;
    std::cout << "ptr3 points to: " << *ptr3 << std::endl;
    std::cout << "ptr4 points to: " << *ptr4 << std::endl;
    std::cout << "ptr5 is null: " << (ptr5 == nullptr) << std::endl;
    
    return 0;
}
```

### Pointer Types and Size
```cpp
#include <iostream>

int main() {
    int i = 10;
    double d = 3.14;
    char c = 'A';
    
    int* intPtr = &i;
    double* doublePtr = &d;
    char* charPtr = &c;
    
    std::cout << "Size of int pointer: " << sizeof(intPtr) << " bytes" << std::endl;
    std::cout << "Size of double pointer: " << sizeof(doublePtr) << " bytes" << std::endl;
    std::cout << "Size of char pointer: " << sizeof(charPtr) << " bytes" << std::endl;
    
    std::cout << "\nAddress values:" << std::endl;
    std::cout << "int pointer: " << intPtr << std::endl;
    std::cout << "double pointer: " << doublePtr << std::endl;
    std::cout << "char pointer: " << static_cast<void*>(charPtr) << std::endl;
    
    return 0;
}
```

## Pointer Arithmetic

### Basic Pointer Arithmetic
```cpp
#include <iostream>

int main() {
    int arr[5] = {10, 20, 30, 40, 50};
    int* ptr = arr;  // Point to first element
    
    std::cout << "Array elements using pointer arithmetic:" << std::endl;
    
    for (int i = 0; i < 5; i++) {
        std::cout << "arr[" << i << "] = " << *(ptr + i) << std::endl;
        std::cout << "Address: " << (ptr + i) << std::endl;
    }
    
    // Pointer increment/decrement
    std::cout << "\nPointer traversal:" << std::endl;
    int* current = arr;
    
    while (current < arr + 5) {
        std::cout << *current << " ";
        current++;  // Move to next element
    }
    std::cout << std::endl;
    
    // Pointer difference
    int* first = arr;
    int* last = arr + 4;
    std::cout << "Difference between pointers: " << (last - first) << std::endl;
    
    return 0;
}
```

### Advanced Pointer Operations
```cpp
#include <iostream>

int main() {
    double arr[] = {1.1, 2.2, 3.3, 4.4, 5.5};
    double* ptr = arr;
    
    std::cout << "Original array:" << std::endl;
    for (int i = 0; i < 5; i++) {
        std::cout << arr[i] << " ";
    }
    std::cout << std::endl;
    
    // Accessing elements using different pointer notations
    std::cout << "\nDifferent access methods:" << std::endl;
    std::cout << "arr[2]: " << arr[2] << std::endl;
    std::cout << "*(arr + 2): " << *(arr + 2) << std::endl;
    std::cout << "ptr[2]: " << ptr[2] << std::endl;
    std::cout << "*(ptr + 2): " << *(ptr + 2) << std::endl;
    
    // Pointer comparison
    double* start = arr;
    double* end = arr + 4;
    
    std::cout << "\nPointer comparison:" << std::endl;
    std::cout << "start < end: " << (start < end) << std::endl;
    std::cout << "start == arr: " << (start == arr) << std::endl;
    
    return 0;
}
```

## Pointers and Arrays

### Array-Pointer Duality
```cpp
#include <iostream>

void printArray(int* arr, int size) {
    for (int i = 0; i < size; i++) {
        std::cout << arr[i] << " ";
    }
    std::cout << std::endl;
}

void modifyArray(int* arr, int size) {
    for (int i = 0; i < size; i++) {
        arr[i] *= 2;  // Double each element
    }
}

int main() {
    int numbers[5] = {1, 2, 3, 4, 5};
    
    std::cout << "Original array: ";
    printArray(numbers, 5);
    
    modifyArray(numbers, 5);
    
    std::cout << "Modified array: ";
    printArray(numbers, 5);
    
    // Array name is a pointer to first element
    int* ptr = numbers;
    std::cout << "First element via pointer: " << *ptr << std::endl;
    std::cout << "Second element via pointer: " << *(ptr + 1) << std::endl;
    
    return 0;
}
```

### 2D Arrays and Pointers
```cpp
#include <iostream>

int main() {
    int matrix[3][4] = {
        {1, 2, 3, 4},
        {5, 6, 7, 8},
        {9, 10, 11, 12}
    };
    
    // Pointer to first row
    int (*rowPtr)[4] = matrix;
    
    std::cout << "2D Array using pointers:" << std::endl;
    for (int i = 0; i < 3; i++) {
        for (int j = 0; j < 4; j++) {
            std::cout << *(*(matrix + i) + j) << " ";
        }
        std::cout << std::endl;
    }
    
    // Alternative access
    std::cout << "\nAlternative access:" << std::endl;
    for (int i = 0; i < 3; i++) {
        for (int j = 0; j < 4; j++) {
            std::cout << matrix[i][j] << " ";
        }
        std::cout << std::endl;
    }
    
    return 0;
}
```

## Dynamic Memory Allocation

### new and delete Operators
```cpp
#include <iostream>

int main() {
    // Dynamic integer allocation
    int* dynamicInt = new int;
    *dynamicInt = 100;
    std::cout << "Dynamic integer: " << *dynamicInt << std::endl;
    delete dynamicInt;
    
    // Dynamic array allocation
    int size;
    std::cout << "Enter array size: ";
    std::cin >> size;
    
    int* dynamicArray = new int[size];
    
    // Initialize array
    for (int i = 0; i < size; i++) {
        dynamicArray[i] = i * 10;
    }
    
    // Display array
    std::cout << "Dynamic array: ";
    for (int i = 0; i < size; i++) {
        std::cout << dynamicArray[i] << " ";
    }
    std::cout << std::endl;
    
    // Deallocate array
    delete[] dynamicArray;
    
    return 0;
}
```

### Memory Allocation Errors
```cpp
#include <iostream>
#include <new>  // For std::bad_alloc

int main() {
    try {
        // Attempt to allocate very large memory
        int* largeArray = new int[1000000000];
        std::cout << "Memory allocation successful!" << std::endl;
        delete[] largeArray;
    } catch (const std::bad_alloc& e) {
        std::cerr << "Memory allocation failed: " << e.what() << std::endl;
    }
    
    // Using nothrow version
    int* safeArray = new(std::nothrow) int[1000000000];
    if (safeArray == nullptr) {
        std::cout << "Memory allocation failed (nothrow version)" << std::endl;
    } else {
        std::cout << "Memory allocation successful (nothrow version)" << std::endl;
        delete[] safeArray;
    }
    
    return 0;
}
```

## Pointers and Functions

### Pass by Pointer
```cpp
#include <iostream>

void swap(int* a, int* b) {
    int temp = *a;
    *a = *b;
    *b = temp;
}

void modifyValue(int* ptr) {
    *ptr = *ptr * 2;
}

int main() {
    int x = 10, y = 20;
    
    std::cout << "Before swap: x = " << x << ", y = " << y << std::endl;
    swap(&x, &y);
    std::cout << "After swap: x = " << x << ", y = " << y << std::endl;
    
    int value = 5;
    std::cout << "\nBefore modification: " << value << std::endl;
    modifyValue(&value);
    std::cout << "After modification: " << value << std::endl;
    
    return 0;
}
```

### Returning Pointers from Functions
```cpp
#include <iostream>

int* createArray(int size) {
    int* arr = new int[size];
    for (int i = 0; i < size; i++) {
        arr[i] = i * i;
    }
    return arr;
}

int* findMax(int* arr, int size) {
    if (size <= 0) return nullptr;
    
    int* maxPtr = arr;
    for (int i = 1; i < size; i++) {
        if (arr[i] > *maxPtr) {
            maxPtr = &arr[i];
        }
    }
    return maxPtr;
}

int main() {
    int size = 5;
    int* array = createArray(size);
    
    std::cout << "Array: ";
    for (int i = 0; i < size; i++) {
        std::cout << array[i] << " ";
    }
    std::cout << std::endl;
    
    int* maxPtr = findMax(array, size);
    if (maxPtr != nullptr) {
        std::cout << "Maximum value: " << *maxPtr << std::endl;
        std::cout << "Address of max value: " << maxPtr << std::endl;
    }
    
    delete[] array;
    return 0;
}
```

## Smart Pointers (C++11)

### unique_ptr
```cpp
#include <iostream>
#include <memory>

class Resource {
public:
    Resource() { std::cout << "Resource acquired" << std::endl; }
    ~Resource() { std::cout << "Resource released" << std::endl; }
    void doSomething() { std::cout << "Doing something with resource" << std::endl; }
};

int main() {
    // unique_ptr automatically manages memory
    std::unique_ptr<Resource> resource1(new Resource());
    resource1->doSomething();
    
    // unique_ptr with make_unique (C++14)
    auto resource2 = std::make_unique<Resource>();
    resource2->doSomething();
    
    // unique_ptr cannot be copied, only moved
    std::unique_ptr<Resource> resource3 = std::move(resource2);
    if (resource2 == nullptr) {
        std::cout << "resource2 is now null" << std::endl;
    }
    
    return 0; // Resource automatically released
}
```

### shared_ptr
```cpp
#include <iostream>
#include <memory>

class SharedResource {
public:
    SharedResource() { std::cout << "SharedResource created" << std::endl; }
    ~SharedResource() { std::cout << "SharedResource destroyed" << std::endl; }
    void use() { std::cout << "Using shared resource" << std::endl; }
};

int main() {
    // Multiple pointers can share ownership
    std::shared_ptr<SharedResource> ptr1 = std::make_shared<SharedResource>();
    std::cout << "Reference count: " << ptr1.use_count() << std::endl;
    
    {
        std::shared_ptr<SharedResource> ptr2 = ptr1;
        std::cout << "Reference count: " << ptr1.use_count() << std::endl;
        ptr2->use();
    }
    
    std::cout << "Reference count after ptr2 goes out of scope: " << ptr1.use_count() << std::endl;
    
    return 0;
}
```

### weak_ptr
```cpp
#include <iostream>
#include <memory>

class Node {
public:
    std::string name;
    std::shared_ptr<Node> next;
    std::weak_ptr<Node> parent;  // Use weak_ptr to avoid circular reference
    
    Node(const std::string& n) : name(n) {
        std::cout << "Node " << name << " created" << std::endl;
    }
    
    ~Node() {
        std::cout << "Node " << name << " destroyed" << std::endl;
    }
};

int main() {
    auto node1 = std::make_shared<Node>("A");
    auto node2 = std::make_shared<Node>("B");
    
    node1->next = node2;
    node2->parent = node1;  // weak_ptr doesn't increase reference count
    
    std::cout << "node1 use_count: " << node1.use_count() << std::endl;
    std::cout << "node2 use_count: " << node2.use_count() << std::endl;
    
    // Lock weak_ptr to get shared_ptr
    if (auto parentPtr = node2->parent.lock()) {
        std::cout << "Parent of " << node2->name << " is " << parentPtr->name << std::endl;
    }
    
    return 0;
}
```

## Common Pointer Pitfalls

### Dangling Pointers
```cpp
#include <iostream>

int* createDanglingPointer() {
    int local = 10;
    return &local;  // DANGEROUS: returning address of local variable
}

int main() {
    int* dangling = createDanglingPointer();
    std::cout << "Dangling pointer value: " << *dangling << std::endl; // Undefined behavior
    return 0;
}
```

### Memory Leaks
```cpp
#include <iostream>

void memoryLeakExample() {
    int* leak = new int(100);
    // Forgot to delete leak - memory leak!
}

void noMemoryLeak() {
    int* noLeak = new int(100);
    delete noLeak;  // Proper deallocation
}

int main() {
    memoryLeakExample();  // This causes a memory leak
    noMemoryLeak();       // This is correct
    
    // Using smart pointers to avoid leaks
    auto smartPtr = std::make_unique<int>(100);
    // Automatically deleted when smartPtr goes out of scope
    
    return 0;
}
```

## Complete Example: Dynamic Array Class

```cpp
#include <iostream>
#include <memory>

template <typename T>
class DynamicArray {
private:
    T* data;
    size_t size;
    size_t capacity;
    
    void resize(size_t newCapacity) {
        T* newData = new T[newCapacity];
        for (size_t i = 0; i < size; i++) {
            newData[i] = data[i];
        }
        delete[] data;
        data = newData;
        capacity = newCapacity;
    }
    
public:
    DynamicArray() : data(nullptr), size(0), capacity(0) {}
    
    DynamicArray(size_t initialCapacity) : size(0), capacity(initialCapacity) {
        data = new T[capacity];
    }
    
    ~DynamicArray() {
        delete[] data;
    }
    
    void push_back(const T& value) {
        if (size >= capacity) {
            resize(capacity == 0 ? 1 : capacity * 2);
        }
        data[size++] = value;
    }
    
    T& operator[](size_t index) {
        if (index >= size) {
            throw std::out_of_range("Index out of bounds");
        }
        return data[index];
    }
    
    const T& operator[](size_t index) const {
        if (index >= size) {
            throw std::out_of_range("Index out of bounds");
        }
        return data[index];
    }
    
    size_t getSize() const { return size; }
    size_t getCapacity() const { return capacity; }
    
    void print() const {
        std::cout << "[";
        for (size_t i = 0; i < size; i++) {
            std::cout << data[i];
            if (i < size - 1) std::cout << ", ";
        }
        std::cout << "]" << std::endl;
    }
};

int main() {
    DynamicArray<int> arr;
    
    // Add elements
    for (int i = 0; i < 10; i++) {
        arr.push_back(i * 10);
        std::cout << "Added " << i * 10 << ", Size: " << arr.getSize() 
                  << ", Capacity: " << arr.getCapacity() << std::endl;
    }
    
    std::cout << "Final array: ";
    arr.print();
    
    // Access elements
    std::cout << "Element at index 3: " << arr[3] << std::endl;
    arr[3] = 999;
    std::cout << "Modified element at index 3: " << arr[3] << std::endl;
    
    // Using with strings
    DynamicArray<std::string> stringArr;
    stringArr.push_back("Hello");
    stringArr.push_back("World");
    stringArr.push_back("C++");
    
    std::cout << "String array: ";
    stringArr.print();
    
    return 0;
}
```

## Practice Exercises

### Exercise 1: Pointer Basics
Write a program that:
- Declares variables of different types
- Creates pointers to these variables
- Demonstrates pointer arithmetic
- Shows the relationship between arrays and pointers

### Exercise 2: Dynamic Memory Management
Create a program that:
- Dynamically allocates a 2D array
- Performs matrix operations
- Properly deallocates memory
- Handles allocation failures

### Exercise 3: Smart Pointer Usage
Rewrite a program using raw pointers to use smart pointers:
- Convert unique_ptr usage
- Implement shared_ptr for shared resources
- Demonstrate weak_ptr to avoid circular references

### Exercise 4: Custom Container
Implement a simple container class:
- Use dynamic memory allocation
- Implement copy constructor and assignment operator
- Follow Rule of Three/Five
- Use smart pointers where appropriate

## Key Takeaways
- Pointers store memory addresses and enable direct memory access
- Pointer arithmetic allows traversal of arrays and memory
- Dynamic memory allocation requires manual deallocation
- Smart pointers (unique_ptr, shared_ptr, weak_ptr) prevent memory leaks
- Always initialize pointers and check for nullptr
- Be aware of dangling pointers and memory leaks
- Follow RAII principles for automatic resource management

## Next Module
In the next module, we'll explore structures, unions, and enums in C++.