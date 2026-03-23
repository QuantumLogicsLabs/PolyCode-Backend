// Module 6: Arrays and Strings - Real-Life Examples
// This file demonstrates practical applications of arrays and strings

#include <iostream>
#include <string>
#include <vector>
#include <algorithm>
#include <cctype>
#include <iomanip>

// Example 1: Student Grade Management System
class GradeManager {
private:
    struct Student {
        std::string name;
        std::string id;
        double grades[5]; // 5 subjects
        double average;
    };
    
    Student* students;
    int capacity;
    int count;
    
public:
    GradeManager(int maxStudents) : capacity(maxStudents), count(0) {
        students = new Student[capacity];
    }
    
    ~GradeManager() {
        delete[] students;
    }
    
    void addStudent(const std::string& name, const std::string& id) {
        if (count >= capacity) {
            std::cout << "Maximum capacity reached!" << std::endl;
            return;
        }
        
        students[count].name = name;
        students[count].id = id;
        
        // Initialize grades to 0
        for (int i = 0; i < 5; i++) {
            students[count].grades[i] = 0.0;
        }
        students[count].average = 0.0;
        
        count++;
        std::cout << "Student " << name << " added successfully." << std::endl;
    }
    
    void setGrades(const std::string& studentId, double grades[5]) {
        for (int i = 0; i < count; i++) {
            if (students[i].id == studentId) {
                double sum = 0;
                for (int j = 0; j < 5; j++) {
                    students[i].grades[j] = grades[j];
                    sum += grades[j];
                }
                students[i].average = sum / 5;
                std::cout << "Grades set for " << students[i].name << std::endl;
                return;
            }
        }
        std::cout << "Student ID " << studentId << " not found." << std::endl;
    }
    
    void displayStudentReport(const std::string& studentId) {
        for (int i = 0; i < count; i++) {
            if (students[i].id == studentId) {
                std::cout << "\n=== STUDENT REPORT ===" << std::endl;
                std::cout << "Name: " << students[i].name << std::endl;
                std::cout << "ID: " << students[i].id << std::endl;
                std::cout << "Grades: ";
                for (int j = 0; j < 5; j++) {
                    std::cout << std::fixed << std::setprecision(1) << students[i].grades[j];
                    if (j < 4) std::cout << ", ";
                }
                std::cout << std::endl;
                std::cout << "Average: " << std::fixed << std::setprecision(2) << students[i].average << std::endl;
                
                // Grade classification
                if (students[i].average >= 90) std::cout << "Grade: A (Excellent)" << std::endl;
                else if (students[i].average >= 80) std::cout << "Grade: B (Good)" << std::endl;
                else if (students[i].average >= 70) std::cout << "Grade: C (Average)" << std::endl;
                else if (students[i].average >= 60) std::cout << "Grade: D (Below Average)" << std::endl;
                else std::cout << "Grade: F (Failing)" << std::endl;
                
                return;
            }
        }
        std::cout << "Student ID " << studentId << " not found." << std::endl;
    }
    
    void displayClassStatistics() {
        if (count == 0) {
            std::cout << "No students in the system." << std::endl;
            return;
        }
        
        double totalAverage = 0;
        double highestAverage = students[0].average;
        double lowestAverage = students[0].average;
        int highestIndex = 0;
        int lowestIndex = 0;
        
        for (int i = 0; i < count; i++) {
            totalAverage += students[i].average;
            
            if (students[i].average > highestAverage) {
                highestAverage = students[i].average;
                highestIndex = i;
            }
            
            if (students[i].average < lowestAverage) {
                lowestAverage = students[i].average;
                lowestIndex = i;
            }
        }
        
        std::cout << "\n=== CLASS STATISTICS ===" << std::endl;
        std::cout << "Total Students: " << count << std::endl;
        std::cout << "Class Average: " << std::fixed << std::setprecision(2) << (totalAverage / count) << std::endl;
        std::cout << "Highest Performer: " << students[highestIndex].name 
                  << " (" << highestAverage << ")" << std::endl;
        std::cout << "Lowest Performer: " << students[lowestIndex].name 
                  << " (" << lowestAverage << ")" << std::endl;
    }
    
    void sortStudentsByAverage() {
        // Simple bubble sort for demonstration
        for (int i = 0; i < count - 1; i++) {
            for (int j = 0; j < count - i - 1; j++) {
                if (students[j].average < students[j + 1].average) {
                    Student temp = students[j];
                    students[j] = students[j + 1];
                    students[j + 1] = temp;
                }
            }
        }
        
        std::cout << "Students sorted by average (highest to lowest)." << std::endl;
    }
    
    void displayAllStudents() {
        if (count == 0) {
            std::cout << "No students in the system." << std::endl;
            return;
        }
        
        std::cout << "\n=== ALL STUDENTS ===" << std::endl;
        std::cout << std::left << std::setw(20) << "Name" 
                  << std::setw(15) << "ID" 
                  << std::setw(10) << "Average" << "Grade" << std::endl;
        std::cout << std::string(50, '-') << std::endl;
        
        for (int i = 0; i < count; i++) {
            std::string grade;
            if (students[i].average >= 90) grade = "A";
            else if (students[i].average >= 80) grade = "B";
            else if (students[i].average >= 70) grade = "C";
            else if (students[i].average >= 60) grade = "D";
            else grade = "F";
            
            std::cout << std::left << std::setw(20) << students[i].name
                      << std::setw(15) << students[i].id
                      << std::setw(10) << std::fixed << std::setprecision(2) << students[i].average
                      << grade << std::endl;
        }
    }
};

// Example 2: Inventory Management with 2D Arrays
class InventoryManager {
private:
    static const int ROWS = 10;
    static const int COLS = 4;
    int inventory[ROWS][COLS]; // [product_id][quantity, price, min_stock, max_stock]
    std::string productNames[ROWS];
    int productCount;
    
public:
    InventoryManager() : productCount(0) {
        // Initialize inventory
        for (int i = 0; i < ROWS; i++) {
            for (int j = 0; j < COLS; j++) {
                inventory[i][j] = 0;
            }
        }
    }
    
    bool addProduct(const std::string& name, int quantity, int price, int minStock, int maxStock) {
        if (productCount >= ROWS) {
            std::cout << "Inventory is full!" << std::endl;
            return false;
        }
        
        productNames[productCount] = name;
        inventory[productCount][0] = quantity;  // quantity
        inventory[productCount][1] = price;     // price
        inventory[productCount][2] = minStock;   // min_stock
        inventory[productCount][3] = maxStock;   // max_stock
        
        productCount++;
        std::cout << "Product '" << name << "' added to inventory." << std::endl;
        return true;
    }
    
    void updateStock(int productId, int newQuantity) {
        if (productId < 0 || productId >= productCount) {
            std::cout << "Invalid product ID." << std::endl;
            return;
        }
        
        inventory[productId][0] = newQuantity;
        std::cout << "Stock updated for " << productNames[productId] 
                  << ": " << newQuantity << " units" << std::endl;
        
        // Check stock levels
        checkStockLevels(productId);
    }
    
    void checkStockLevels(int productId) {
        int quantity = inventory[productId][0];
        int minStock = inventory[productId][2];
        int maxStock = inventory[productId][3];
        
        if (quantity <= minStock) {
            std::cout << "WARNING: " << productNames[productId] 
                      << " is below minimum stock level!" << std::endl;
        } else if (quantity >= maxStock) {
            std::cout << "INFO: " << productNames[productId] 
                      << " has reached maximum stock level." << std::endl;
        }
    }
    
    void displayInventory() {
        if (productCount == 0) {
            std::cout << "Inventory is empty." << std::endl;
            return;
        }
        
        std::cout << "\n=== INVENTORY REPORT ===" << std::endl;
        std::cout << std::left << std::setw(20) << "Product" 
                  << std::setw(10) << "Quantity" 
                  << std::setw(8) << "Price" 
                  << std::setw(10) << "Min Stock" 
                  << std::setw(10) << "Max Stock" 
                  << "Status" << std::endl;
        std::cout << std::string(75, '-') << std::endl;
        
        for (int i = 0; i < productCount; i++) {
            std::string status;
            if (inventory[i][0] <= inventory[i][2]) {
                status = "LOW";
            } else if (inventory[i][0] >= inventory[i][3]) {
                status = "HIGH";
            } else {
                status = "OK";
            }
            
            std::cout << std::left << std::setw(20) << productNames[i]
                      << std::setw(10) << inventory[i][0]
                      << std::setw(8) << "$" << inventory[i][1]
                      << std::setw(10) << inventory[i][2]
                      << std::setw(10) << inventory[i][3]
                      << status << std::endl;
        }
    }
    
    int getTotalValue() {
        int total = 0;
        for (int i = 0; i < productCount; i++) {
            total += inventory[i][0] * inventory[i][1];
        }
        return total;
    }
    
    void findLowStockProducts() {
        std::cout << "\n=== LOW STOCK PRODUCTS ===" << std::endl;
        bool found = false;
        
        for (int i = 0; i < productCount; i++) {
            if (inventory[i][0] <= inventory[i][2]) {
                std::cout << productNames[i] << ": " << inventory[i][0] 
                          << " (Min: " << inventory[i][2] << ")" << std::endl;
                found = true;
            }
        }
        
        if (!found) {
            std::cout << "No products are below minimum stock level." << std::endl;
        }
    }
};

// Example 3: Text Processing and String Manipulation
class TextProcessor {
private:
    std::string text;
    
public:
    TextProcessor(const std::string& initialText) : text(initialText) {}
    
    void setText(const std::string& newText) {
        text = newText;
    }
    
    std::string getText() const {
        return text;
    }
    
    int getWordCount() {
        int count = 0;
        bool inWord = false;
        
        for (char c : text) {
            if (std::isspace(c)) {
                inWord = false;
            } else if (!inWord) {
                count++;
                inWord = true;
            }
        }
        
        return count;
    }
    
    int getCharacterCount() {
        return text.length();
    }
    
    int getCharacterCountNoSpaces() {
        int count = 0;
        for (char c : text) {
            if (!std::isspace(c)) {
                count++;
            }
        }
        return count;
    }
    
    std::string toUpperCase() {
        std::string result = text;
        std::transform(result.begin(), result.end(), result.begin(), ::toupper);
        return result;
    }
    
    std::string toLowerCase() {
        std::string result = text;
        std::transform(result.begin(), result.end(), result.begin(), ::tolower);
        return result;
    }
    
    std::string capitalizeWords() {
        std::string result = text;
        bool newWord = true;
        
        for (size_t i = 0; i < result.length(); ++i) {
            if (std::isspace(result[i])) {
                newWord = true;
            } else if (newWord) {
                result[i] = std::toupper(result[i]);
                newWord = false;
            }
        }
        
        return result;
    }
    
    std::vector<std::string> splitWords() {
        std::vector<std::string> words;
        std::string currentWord;
        
        for (char c : text) {
            if (std::isspace(c)) {
                if (!currentWord.empty()) {
                    words.push_back(currentWord);
                    currentWord.clear();
                }
            } else {
                currentWord += c;
            }
        }
        
        if (!currentWord.empty()) {
            words.push_back(currentWord);
        }
        
        return words;
    }
    
    bool containsWord(const std::string& word) {
        std::string lowerText = toLowerCase();
        std::string lowerWord = toLowerCase();
        
        size_t pos = lowerText.find(lowerWord);
        while (pos != std::string::npos) {
            // Check if it's a whole word
            bool startOk = (pos == 0) || std::isspace(lowerText[pos - 1]);
            bool endOk = (pos + lowerWord.length() >= lowerText.length()) || 
                        std::isspace(lowerText[pos + lowerWord.length()]);
            
            if (startOk && endOk) {
                return true;
            }
            
            pos = lowerText.find(lowerWord, pos + 1);
        }
        
        return false;
    }
    
    std::string replaceWord(const std::string& oldWord, const std::string& newWord) {
        std::string result = text;
        std::string lowerResult = toLowerCase();
        std::string lowerOldWord = oldWord;
        std::transform(lowerOldWord.begin(), lowerOldWord.end(), lowerOldWord.begin(), ::tolower);
        
        size_t pos = 0;
        while ((pos = lowerResult.find(lowerOldWord, pos)) != std::string::npos) {
            // Check if it's a whole word
            bool startOk = (pos == 0) || std::isspace(result[pos - 1]);
            bool endOk = (pos + oldWord.length() >= result.length()) || 
                        std::isspace(result[pos + oldWord.length()]);
            
            if (startOk && endOk) {
                result.replace(pos, oldWord.length(), newWord);
                lowerResult.replace(pos, oldWord.length(), newWord);
                pos += newWord.length();
            } else {
                pos++;
            }
        }
        
        return result;
    }
    
    void reverseText() {
        std::reverse(text.begin(), text.end());
    }
    
    bool isPalindrome() {
        std::string cleanText;
        
        // Remove non-alphanumeric characters and convert to lowercase
        for (char c : text) {
            if (std::isalnum(c)) {
                cleanText += std::tolower(c);
            }
        }
        
        std::string reversed = cleanText;
        std::reverse(reversed.begin(), reversed.end());
        
        return cleanText == reversed;
    }
    
    void displayStatistics() {
        std::cout << "\n=== TEXT STATISTICS ===" << std::endl;
        std::cout << "Original Text: \"" << text << "\"" << std::endl;
        std::cout << "Character Count: " << getCharacterCount() << std::endl;
        std::cout << "Character Count (no spaces): " << getCharacterCountNoSpaces() << std::endl;
        std::cout << "Word Count: " << getWordCount() << std::endl;
        std::cout << "Is Palindrome: " << (isPalindrome() ? "Yes" : "No") << std::endl;
        
        auto words = splitWords();
        std::cout << "Words: ";
        for (const auto& word : words) {
            std::cout << "[" << word << "] ";
        }
        std::cout << std::endl;
    }
};

// Example 4: Matrix Operations for Game Board
class GameBoard {
private:
    static const int SIZE = 5;
    int board[SIZE][SIZE];
    
public:
    GameBoard() {
        // Initialize board with zeros
        for (int i = 0; i < SIZE; i++) {
            for (int j = 0; j < SIZE; j++) {
                board[i][j] = 0;
            }
        }
    }
    
    void setValue(int row, int col, int value) {
        if (row >= 0 && row < SIZE && col >= 0 && col < SIZE) {
            board[row][col] = value;
        }
    }
    
    int getValue(int row, int col) {
        if (row >= 0 && row < SIZE && col >= 0 && col < SIZE) {
            return board[row][col];
        }
        return -1; // Invalid position
    }
    
    void displayBoard() {
        std::cout << "\n=== GAME BOARD ===" << std::endl;
        for (int i = 0; i < SIZE; i++) {
            for (int j = 0; j < SIZE; j++) {
                std::cout << std::setw(3) << board[i][j];
            }
            std::cout << std::endl;
        }
    }
    
    int getRowSum(int row) {
        if (row < 0 || row >= SIZE) return 0;
        
        int sum = 0;
        for (int j = 0; j < SIZE; j++) {
            sum += board[row][j];
        }
        return sum;
    }
    
    int getColumnSum(int col) {
        if (col < 0 || col >= SIZE) return 0;
        
        int sum = 0;
        for (int i = 0; i < SIZE; i++) {
            sum += board[i][col];
        }
        return sum;
    }
    
    int getDiagonalSum(bool mainDiagonal) {
        int sum = 0;
        for (int i = 0; i < SIZE; i++) {
            if (mainDiagonal) {
                sum += board[i][i];
            } else {
                sum += board[i][SIZE - 1 - i];
            }
        }
        return sum;
    }
    
    bool checkWinCondition() {
        // Check rows
        for (int i = 0; i < SIZE; i++) {
            if (board[i][0] != 0 && getRowSum(i) == board[i][0] * SIZE) {
                return true;
            }
        }
        
        // Check columns
        for (int j = 0; j < SIZE; j++) {
            if (board[0][j] != 0 && getColumnSum(j) == board[0][j] * SIZE) {
                return true;
            }
        }
        
        // Check diagonals
        if (board[0][0] != 0 && getDiagonalSum(true) == board[0][0] * SIZE) {
            return true;
        }
        
        if (board[0][SIZE-1] != 0 && getDiagonalSum(false) == board[0][SIZE-1] * SIZE) {
            return true;
        }
        
        return false;
    }
    
    void fillRandom() {
        for (int i = 0; i < SIZE; i++) {
            for (int j = 0; j < SIZE; j++) {
                board[i][j] = rand() % 3; // 0, 1, or 2
            }
        }
    }
};

int main() {
    std::cout << "=== Arrays and Strings - Real-Life Examples ===" << std::endl;
    std::cout << "Demonstrating practical applications of arrays and strings\n" << std::endl;
    
    // Example 1: Student Grade Management
    std::cout << "=== STUDENT GRADE MANAGEMENT ===" << std::endl;
    GradeManager gradeSystem(5);
    
    gradeSystem.addStudent("Alice Johnson", "S001");
    gradeSystem.addStudent("Bob Smith", "S002");
    gradeSystem.addStudent("Charlie Brown", "S003");
    gradeSystem.addStudent("Diana Prince", "S004");
    
    double aliceGrades[] = {92.5, 88.0, 95.0, 90.5, 87.5};
    double bobGrades[] = {78.5, 82.0, 75.5, 80.0, 85.5};
    double charlieGrades[] = {65.0, 70.5, 68.0, 72.5, 66.5};
    double dianaGrades[] = {95.0, 98.5, 92.0, 96.5, 94.0};
    
    gradeSystem.setGrades("S001", aliceGrades);
    gradeSystem.setGrades("S002", bobGrades);
    gradeSystem.setGrades("S003", charlieGrades);
    gradeSystem.setGrades("S004", dianaGrades);
    
    gradeSystem.displayStudentReport("S001");
    gradeSystem.displayClassStatistics();
    gradeSystem.sortStudentsByAverage();
    gradeSystem.displayAllStudents();
    
    // Example 2: Inventory Management
    std::cout << "\n=== INVENTORY MANAGEMENT ===" << std::endl;
    InventoryManager inventory;
    
    inventory.addProduct("Laptop", 15, 999, 5, 50);
    inventory.addProduct("Mouse", 100, 25, 20, 200);
    inventory.addProduct("Keyboard", 45, 75, 10, 100);
    inventory.addProduct("Monitor", 8, 300, 3, 25);
    
    inventory.displayInventory();
    inventory.updateStock(0, 3); // Update laptop stock to 3 (below min)
    inventory.updateStock(1, 250); // Update mouse stock to 250 (above max)
    
    inventory.findLowStockProducts();
    std::cout << "Total Inventory Value: $" << inventory.getTotalValue() << std::endl;
    
    // Example 3: Text Processing
    std::cout << "\n=== TEXT PROCESSING ===" << std::endl;
    TextProcessor processor("Hello World! This is a test of the text processing system.");
    processor.displayStatistics();
    
    std::cout << "Uppercase: " << processor.toUpperCase() << std::endl;
    std::cout << "Capitalized: " << processor.capitalizeWords() << std::endl;
    std::cout << "Contains 'test': " << (processor.containsWord("test") ? "Yes" : "No") << std::endl;
    std::cout << "Contains 'python': " << (processor.containsWord("python") ? "Yes" : "No") << std::endl;
    
    TextProcessor palindromeProcessor("A man a plan a canal Panama");
    std::cout << "Palindrome test: \"" << palindromeProcessor.getText() << "\" is " 
              << (palindromeProcessor.isPalindrome() ? "a palindrome" : "not a palindrome") << std::endl;
    
    // Example 4: Game Board
    std::cout << "\n=== GAME BOARD ===" << std::endl;
    GameBoard board;
    
    // Create a winning condition
    board.setValue(0, 0, 1);
    board.setValue(0, 1, 1);
    board.setValue(0, 2, 1);
    board.setValue(0, 3, 1);
    board.setValue(0, 4, 1);
    
    board.displayBoard();
    std::cout << "Row 0 sum: " << board.getRowSum(0) << std::endl;
    std::cout << "Column 0 sum: " << board.getColumnSum(0) << std::endl;
    std::cout << "Main diagonal sum: " << board.getDiagonalSum(true) << std::endl;
    std::cout << "Win condition: " << (board.checkWinCondition() ? "Yes" : "No") << std::endl;
    
    // Random board
    GameBoard randomBoard;
    randomBoard.fillRandom();
    randomBoard.displayBoard();
    std::cout << "Win condition: " << (randomBoard.checkWinCondition() ? "Yes" : "No") << std::endl;
    
    std::cout << "\n\n=== ARRAYS AND STRINGS SUMMARY ===" << std::endl;
    std::cout << "This example demonstrates various array and string applications:" << std::endl;
    std::cout << "• 1D arrays for student grade management" << std::endl;
    std::cout << "• 2D arrays for inventory tracking and game boards" << std::endl;
    std::cout << "• String manipulation for text processing" << std::endl;
    std::cout << "• Array operations like sorting and searching" << std::endl;
    std::cout << "• Matrix operations for mathematical computations" << std::endl;
    std::cout << "• Text analysis and statistics" << std::endl;
    std::cout << "\nArrays and strings are fundamental data structures for organizing and processing data!" << std::endl;
    
    return 0;
}