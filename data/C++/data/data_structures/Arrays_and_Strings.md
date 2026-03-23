# Module 6: Arrays and Strings

## Learning Objectives
- Understand arrays and their declaration/initialization
- Master array operations and manipulation
- Learn about multidimensional arrays
- Understand C-style strings and string library functions
- Master C++ string class and its operations
- Learn about dynamic memory allocation for arrays

## Arrays

### One-Dimensional Arrays

An array is a collection of elements of the same data type stored in contiguous memory locations.

```cpp
#include <iostream>

int main() {
    // Array declaration and initialization
    int numbers[5];                    // Declaration only
    int scores[5] = {85, 92, 78, 95, 88}; // Declaration with initialization
    int values[] = {1, 2, 3, 4, 5};    // Size inferred from initializer
    
    // Accessing array elements
    std::cout << "First element: " << scores[0] << std::endl;
    std::cout << "Last element: " << scores[4] << std::endl;
    
    // Modifying array elements
    numbers[0] = 10;
    numbers[1] = 20;
    numbers[2] = 30;
    numbers[3] = 40;
    numbers[4] = 50;
    
    // Iterating through an array
    std::cout << "Array elements: ";
    for (int i = 0; i < 5; i++) {
        std::cout << numbers[i] << " ";
    }
    std::cout << std::endl;
    
    // Range-based for loop (C++11)
    std::cout << "Scores: ";
    for (int score : scores) {
        std::cout << score << " ";
    }
    std::cout << std::endl;
    
    return 0;
}
```

### Array Size and Bounds
```cpp
#include <iostream>

int main() {
    int arr[10];
    
    // Getting array size
    int size = sizeof(arr) / sizeof(arr[0]);
    std::cout << "Array size: " << size << std::endl;
    
    // Safe array access
    for (int i = 0; i < size; i++) {
        arr[i] = i * 2;
        std::cout << "arr[" << i << "] = " << arr[i] << std::endl;
    }
    
    // Warning: Array bounds are not checked by C++
    // arr[10] = 100; // This would be undefined behavior!
    
    return 0;
}
```

### Array Operations
```cpp
#include <iostream>
#include <algorithm> // For std::sort, std::min, std::max

int main() {
    int numbers[] = {64, 34, 25, 12, 22, 11, 90};
    int size = sizeof(numbers) / sizeof(numbers[0]);
    
    // Find minimum and maximum
    int min_val = numbers[0];
    int max_val = numbers[0];
    
    for (int i = 1; i < size; i++) {
        if (numbers[i] < min_val) min_val = numbers[i];
        if (numbers[i] > max_val) max_val = numbers[i];
    }
    
    std::cout << "Minimum: " << min_val << std::endl;
    std::cout << "Maximum: " << max_val << std::endl;
    
    // Calculate sum and average
    int sum = 0;
    for (int num : numbers) {
        sum += num;
    }
    double average = static_cast<double>(sum) / size;
    std::cout << "Sum: " << sum << std::endl;
    std::cout << "Average: " << average << std::endl;
    
    // Search for an element
    int search_value = 25;
    bool found = false;
    int index = -1;
    
    for (int i = 0; i < size; i++) {
        if (numbers[i] == search_value) {
            found = true;
            index = i;
            break;
        }
    }
    
    if (found) {
        std::cout << search_value << " found at index " << index << std::endl;
    } else {
        std::cout << search_value << " not found" << std::endl;
    }
    
    // Sort array using STL
    std::sort(numbers, numbers + size);
    
    std::cout << "Sorted array: ";
    for (int num : numbers) {
        std::cout << num << " ";
    }
    std::cout << std::endl;
    
    return 0;
}
```

## Multidimensional Arrays

### Two-Dimensional Arrays
```cpp
#include <iostream>

int main() {
    // 2D array declaration and initialization
    int matrix[3][4] = {
        {1, 2, 3, 4},
        {5, 6, 7, 8},
        {9, 10, 11, 12}
    };
    
    // Accessing elements
    std::cout << "Element at [0][0]: " << matrix[0][0] << std::endl;
    std::cout << "Element at [2][3]: " << matrix[2][3] << std::endl;
    
    // Iterating through 2D array
    std::cout << "\nMatrix elements:" << std::endl;
    for (int i = 0; i < 3; i++) {
        for (int j = 0; j < 4; j++) {
            std::cout << matrix[i][j] << "\t";
        }
        std::cout << std::endl;
    }
    
    // Calculate sum of each row
    std::cout << "\nRow sums:" << std::endl;
    for (int i = 0; i < 3; i++) {
        int row_sum = 0;
        for (int j = 0; j < 4; j++) {
            row_sum += matrix[i][j];
        }
        std::cout << "Row " << i << ": " << row_sum << std::endl;
    }
    
    return 0;
}
```

### Matrix Operations
```cpp
#include <iostream>

void addMatrices(int a[3][3], int b[3][3], int result[3][3]) {
    for (int i = 0; i < 3; i++) {
        for (int j = 0; j < 3; j++) {
            result[i][j] = a[i][j] + b[i][j];
        }
    }
}

void multiplyMatrices(int a[3][3], int b[3][3], int result[3][3]) {
    for (int i = 0; i < 3; i++) {
        for (int j = 0; j < 3; j++) {
            result[i][j] = 0;
            for (int k = 0; k < 3; k++) {
                result[i][j] += a[i][k] * b[k][j];
            }
        }
    }
}

void printMatrix(int matrix[3][3]) {
    for (int i = 0; i < 3; i++) {
        for (int j = 0; j < 3; j++) {
            std::cout << matrix[i][j] << "\t";
        }
        std::cout << std::endl;
    }
}

int main() {
    int matrix1[3][3] = {{1, 2, 3}, {4, 5, 6}, {7, 8, 9}};
    int matrix2[3][3] = {{9, 8, 7}, {6, 5, 4}, {3, 2, 1}};
    int result[3][3];
    
    std::cout << "Matrix 1:" << std::endl;
    printMatrix(matrix1);
    
    std::cout << "\nMatrix 2:" << std::endl;
    printMatrix(matrix2);
    
    // Matrix addition
    addMatrices(matrix1, matrix2, result);
    std::cout << "\nMatrix Addition:" << std::endl;
    printMatrix(result);
    
    // Matrix multiplication
    multiplyMatrices(matrix1, matrix2, result);
    std::cout << "\nMatrix Multiplication:" << std::endl;
    printMatrix(result);
    
    return 0;
}
```

## C-Style Strings

### Character Arrays as Strings
```cpp
#include <iostream>
#include <cstring> // For string functions

int main() {
    // C-style string declaration
    char str1[] = "Hello";           // Automatically null-terminated
    char str2[20] = "World";         // With specified size
    char str3[] = {'H', 'e', 'l', 'l', 'o', '\0'}; // Manual null termination
    
    std::cout << "str1: " << str1 << std::endl;
    std::cout << "str2: " << str2 << std::endl;
    std::cout << "str3: " << str3 << std::endl;
    
    // String length
    std::cout << "Length of str1: " << strlen(str1) << std::endl;
    
    // String copying
    char destination[20];
    strcpy(destination, str1);
    std::cout << "Copied string: " << destination << std::endl;
    
    // String concatenation
    strcat(destination, " ");
    strcat(destination, str2);
    std::cout << "Concatenated: " << destination << std::endl;
    
    // String comparison
    if (strcmp(str1, str2) == 0) {
        std::cout << "Strings are equal" << std::endl;
    } else {
        std::cout << "Strings are not equal" << std::endl;
    }
    
    return 0;
}
```

### String Input/Output
```cpp
#include <iostream>
#include <cstring>

int main() {
    char name[50];
    char sentence[100];
    
    // Input with cin (stops at whitespace)
    std::cout << "Enter your first name: ";
    std::cin >> name;
    std::cout << "Hello, " << name << "!" << std::endl;
    
    // Clear input buffer
    std::cin.ignore();
    
    // Input with getline (reads entire line)
    std::cout << "Enter a sentence: ";
    std::cin.getline(sentence, 100);
    std::cout << "You entered: " << sentence << std::endl;
    
    return 0;
}
```

## C++ String Class

### Basic String Operations
```cpp
#include <iostream>
#include <string>

int main() {
    // String declaration and initialization
    std::string str1 = "Hello";
    std::string str2("World");
    std::string str3(10, 'A'); // "AAAAAAAAAA"
    
    std::cout << "str1: " << str1 << std::endl;
    std::cout << "str2: " << str2 << std::endl;
    std::cout << "str3: " << str3 << std::endl;
    
    // String concatenation
    std::string result = str1 + " " + str2;
    std::cout << "Concatenated: " << result << std::endl;
    
    // String length
    std::cout << "Length of result: " << result.length() << std::endl;
    std::cout << "Size of result: " << result.size() << std::endl;
    
    // Accessing characters
    std::cout << "First character: " << result[0] << std::endl;
    std::cout << "Last character: " << result[result.length() - 1] << std::endl;
    
    // Modifying characters
    result[0] = 'h';
    std::cout << "Modified: " << result << std::endl;
    
    return 0;
}
```

### Advanced String Operations
```cpp
#include <iostream>
#include <string>
#include <algorithm>
#include <cctype>

int main() {
    std::string text = "Hello, World! Programming is Fun!";
    
    // Substring
    std::string sub = text.substr(7, 5); // Start at index 7, length 5
    std::cout << "Substring: " << sub << std::endl;
    
    // Find
    size_t pos = text.find("World");
    if (pos != std::string::npos) {
        std::cout << "\"World\" found at position: " << pos << std::endl;
    }
    
    // Replace
    text.replace(pos, 5, "C++");
    std::cout << "After replacement: " << text << std::endl;
    
    // Insert
    text.insert(0, "Welcome to ");
    std::cout << "After insertion: " << text << std::endl;
    
    // Erase
    text.erase(0, 11); // Remove "Welcome to "
    std::cout << "After erasure: " << text << std::endl;
    
    // Case conversion
    std::string upper = text;
    std::transform(upper.begin(), upper.end(), upper.begin(), ::toupper);
    std::cout << "Uppercase: " << upper << std::endl;
    
    std::string lower = text;
    std::transform(lower.begin(), lower.end(), lower.begin(), ::tolower);
    std::cout << "Lowercase: " << lower << std::endl;
    
    return 0;
}
```

### String Input and Validation
```cpp
#include <iostream>
#include <string>
#include <sstream>

int main() {
    std::string name;
    int age;
    double salary;
    
    // Input with validation
    std::cout << "Enter your name: ";
    std::getline(std::cin, name);
    
    std::cout << "Enter your age: ";
    while (!(std::cin >> age) || age <= 0) {
        std::cout << "Invalid age! Please enter a positive number: ";
        std::cin.clear();
        std::cin.ignore(std::numeric_limits<std::streamsize>::max(), '\n');
    }
    
    std::cout << "Enter your salary: ";
    while (!(std::cin >> salary) || salary < 0) {
        std::cout << "Invalid salary! Please enter a non-negative number: ";
        std::cin.clear();
        std::cin.ignore(std::numeric_limits<std::streamsize>::max(), '\n');
    }
    
    // String formatting
    std::stringstream ss;
    ss << "Name: " << name << ", Age: " << age << ", Salary: $" << salary;
    std::string info = ss.str();
    
    std::cout << "Formatted info: " << info << std::endl;
    
    return 0;
}
```

## Dynamic Memory Allocation for Arrays

### Dynamic Arrays
```cpp
#include <iostream>

int main() {
    // Dynamic array allocation
    int size;
    std::cout << "Enter array size: ";
    std::cin >> size;
    
    // Allocate memory
    int* dynamicArray = new int[size];
    
    // Initialize array
    for (int i = 0; i < size; i++) {
        dynamicArray[i] = i * 10;
    }
    
    // Display array
    std::cout << "Dynamic array elements: ";
    for (int i = 0; i < size; i++) {
        std::cout << dynamicArray[i] << " ";
    }
    std::cout << std::endl;
    
    // Deallocate memory
    delete[] dynamicArray;
    
    return 0;
}
```

## Complete Example: Text Analysis Program

```cpp
#include <iostream>
#include <string>
#include <vector>
#include <map>
#include <algorithm>
#include <cctype>

// Function to count words in a string
int countWords(const std::string& text) {
    std::stringstream ss(text);
    std::string word;
    int count = 0;
    
    while (ss >> word) {
        count++;
    }
    
    return count;
}

// Function to count characters
void countCharacters(const std::string& text, int& letters, int& digits, int& spaces, int& others) {
    for (char c : text) {
        if (isalpha(c)) {
            letters++;
        } else if (isdigit(c)) {
            digits++;
        } else if (isspace(c)) {
            spaces++;
        } else {
            others++;
        }
    }
}

// Function to find word frequency
std::map<std::string, int> getWordFrequency(const std::string& text) {
    std::map<std::string, int> frequency;
    std::stringstream ss(text);
    std::string word;
    
    while (ss >> word) {
        // Convert to lowercase and remove punctuation
        std::string cleanWord;
        for (char c : word) {
            if (isalpha(c)) {
                cleanWord += tolower(c);
            }
        }
        
        if (!cleanWord.empty()) {
            frequency[cleanWord]++;
        }
    }
    
    return frequency;
}

// Function to find longest word
std::string findLongestWord(const std::string& text) {
    std::stringstream ss(text);
    std::string word;
    std::string longest;
    
    while (ss >> word) {
        // Remove punctuation
        std::string cleanWord;
        for (char c : word) {
            if (isalpha(c)) {
                cleanWord += c;
            }
        }
        
        if (cleanWord.length() > longest.length()) {
            longest = cleanWord;
        }
    }
    
    return longest;
}

int main() {
    std::string text;
    
    std::cout << "=== Text Analysis Program ===" << std::endl;
    std::cout << "Enter a text to analyze (multiple lines supported, end with empty line):" << std::endl;
    
    std::string line;
    std::getline(std::cin, line);
    text = line;
    
    // Read multiple lines until empty line
    while (std::getline(std::cin, line) && !line.empty()) {
        text += " " + line;
    }
    
    std::cout << "\n=== Analysis Results ===" << std::endl;
    
    // Basic statistics
    std::cout << "Total characters: " << text.length() << std::endl;
    std::cout << "Total words: " << countWords(text) << std::endl;
    
    // Character type analysis
    int letters = 0, digits = 0, spaces = 0, others = 0;
    countCharacters(text, letters, digits, spaces, others);
    
    std::cout << "\nCharacter breakdown:" << std::endl;
    std::cout << "Letters: " << letters << std::endl;
    std::cout << "Digits: " << digits << std::endl;
    std::cout << "Spaces: " << spaces << std::endl;
    std::cout << "Others: " << others << std::endl;
    
    // Word frequency
    std::map<std::string, int> frequency = getWordFrequency(text);
    std::cout << "\nWord frequency (top 10):" << std::endl;
    
    // Convert map to vector for sorting
    std::vector<std::pair<std::string, int>> freqVector(frequency.begin(), frequency.end());
    std::sort(freqVector.begin(), freqVector.end(),
              [](const auto& a, const auto& b) { return a.second > b.second; });
    
    int count = 0;
    for (const auto& pair : freqVector) {
        if (count >= 10) break;
        std::cout << pair.first << ": " << pair.second << std::endl;
        count++;
    }
    
    // Longest word
    std::string longest = findLongestWord(text);
    std::cout << "\nLongest word: " << longest << " (" << longest.length() << " characters)" << std::endl;
    
    // Palindrome check
    std::string cleanText;
    for (char c : text) {
        if (isalpha(c)) {
            cleanText += tolower(c);
        }
    }
    
    std::string reversedText = cleanText;
    std::reverse(reversedText.begin(), reversedText.end());
    
    if (cleanText == reversedText && !cleanText.empty()) {
        std::cout << "\nThe text is a palindrome!" << std::endl;
    } else {
        std::cout << "\nThe text is not a palindrome." << std::endl;
    }
    
    return 0;
}
```

## Practice Exercises

### Exercise 1: Array Manipulation
Write a program that:
- Takes an array of integers as input
- Finds the sum, average, min, and max
- Sorts the array in ascending and descending order
- Removes duplicate elements

### Exercise 2: Matrix Calculator
Create a program that performs matrix operations:
- Addition and subtraction of matrices
- Matrix multiplication
- Transpose of a matrix
- Determinant calculation (for 2x2 and 3x3 matrices)

### Exercise 3: String Processing
Write functions for:
- Checking if a string is a palindrome
- Counting vowels and consonants
- Reversing words in a sentence
- Removing extra spaces

### Exercise 4: Student Records
Create a program that manages student records using arrays:
- Store student information (name, ID, grades)
- Calculate average grades
- Find top and bottom performers
- Search for students by name or ID

## Key Takeaways
- Arrays store multiple elements of the same type in contiguous memory
- Multidimensional arrays represent tables or matrices
- C-style strings are character arrays terminated with null character
- C++ string class provides safer and more convenient string operations
- Dynamic memory allocation allows arrays of variable size
- Always deallocate dynamically allocated memory to prevent leaks

## Next Module
In the next module, we'll explore pointers and memory management in C++.