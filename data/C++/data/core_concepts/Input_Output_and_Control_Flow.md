# Module 4: Input/Output and Control Flow

## Learning Objectives
- Master input/output operations in C++
- Understand conditional statements (if, else if, else, switch)
- Learn about loops (for, while, do-while)
- Understand break, continue, and goto statements
- Learn to create interactive programs

## Input/Output Operations

### Basic Input/Output with iostream
```cpp
#include <iostream>
#include <string>

int main() {
    // Output operations
    std::cout << "Hello, World!" << std::endl;    // endl adds newline
    std::cout << "C++ Programming" << '\n';       // '\n' also adds newline
    
    // Input operations
    std::string name;
    int age;
    
    std::cout << "Enter your name: ";
    std::getline(std::cin, name);  // Reads entire line including spaces
    
    std::cout << "Enter your age: ";
    std::cin >> age;               // Reads single word/number
    
    std::cout << "Hello, " << name << "! You are " << age << " years old." << std::endl;
    
    return 0;
}
```

### Formatted Output with iomanip
```cpp
#include <iostream>
#include <iomanip>
#include <string>

int main() {
    double pi = 3.14159265359;
    double value = 123.456;
    
    // Setting precision
    std::cout << std::fixed << std::setprecision(2);
    std::cout << "Pi with 2 decimals: " << pi << std::endl;
    
    // Setting width and alignment
    std::cout << std::setw(10) << std::left << "Name" 
              << std::setw(5) << "Age" << std::endl;
    std::cout << std::setw(10) << std::left << "John" 
              << std::setw(5) << "25" << std::endl;
    
    // Scientific notation
    std::cout << std::scientific << value << std::endl;
    
    return 0;
}
```

### Input Validation
```cpp
#include <iostream>
#include <limits>

int main() {
    int number;
    
    std::cout << "Enter an integer: ";
    
    // Input validation loop
    while (!(std::cin >> number)) {
        std::cout << "Invalid input! Please enter an integer: ";
        std::cin.clear();  // Clear error flags
        std::cin.ignore(std::numeric_limits<std::streamsize>::max(), '\n');  // Ignore bad input
    }
    
    std::cout << "You entered: " << number << std::endl;
    
    return 0;
}
```

## Conditional Statements

### if, else if, else Statements
```cpp
#include <iostream>

int main() {
    int score;
    
    std::cout << "Enter your score (0-100): ";
    std::cin >> score;
    
    if (score >= 90) {
        std::cout << "Grade: A - Excellent!" << std::endl;
    } else if (score >= 80) {
        std::cout << "Grade: B - Good!" << std::endl;
    } else if (score >= 70) {
        std::cout << "Grade: C - Average" << std::endl;
    } else if (score >= 60) {
        std::cout << "Grade: D - Below Average" << std::endl;
    } else {
        std::cout << "Grade: F - Fail" << std::endl;
    }
    
    // Nested if statements
    if (score >= 60) {
        std::cout << "You passed!" << std::endl;
        if (score >= 90) {
            std::cout << "Congratulations on your excellent performance!" << std::endl;
        }
    } else {
        std::cout << "You need to improve." << std::endl;
    }
    
    return 0;
}
```

### switch Statement
```cpp
#include <iostream>

int main() {
    char grade;
    
    std::cout << "Enter your grade (A, B, C, D, F): ";
    std::cin >> grade;
    
    switch (grade) {
        case 'A':
        case 'a':
            std::cout << "Excellent work!" << std::endl;
            break;
        case 'B':
        case 'b':
            std::cout << "Good job!" << std::endl;
            break;
        case 'C':
        case 'c':
            std::cout << "Average performance." << std::endl;
            break;
        case 'D':
        case 'd':
            std::cout << "Needs improvement." << std::endl;
            break;
        case 'F':
        case 'f':
            std::cout << "Fail. Please study more." << std::endl;
            break;
        default:
            std::cout << "Invalid grade entered!" << std::endl;
    }
    
    return 0;
}
```

### Ternary Operator for Simple Conditions
```cpp
#include <iostream>

int main() {
    int age;
    
    std::cout << "Enter your age: ";
    std::cin >> age;
    
    std::string message = (age >= 18) ? "You are eligible to vote." : "You are not eligible to vote.";
    std::cout << message << std::endl;
    
    return 0;
}
```

## Loops

### for Loop
```cpp
#include <iostream>

int main() {
    // Basic for loop
    std::cout << "Counting from 1 to 10:" << std::endl;
    for (int i = 1; i <= 10; i++) {
        std::cout << i << " ";
    }
    std::cout << std::endl;
    
    // Nested for loops
    std::cout << "\nMultiplication table (1-5):" << std::endl;
    for (int i = 1; i <= 5; i++) {
        for (int j = 1; j <= 5; j++) {
            std::cout << i * j << "\t";
        }
        std::cout << std::endl;
    }
    
    // For loop with multiple variables
    std::cout << "\nCounting up and down:" << std::endl;
    for (int i = 0, j = 10; i <= 10; i++, j--) {
        std::cout << i << " " << j << std::endl;
    }
    
    return 0;
}
```

### while Loop
```cpp
#include <iostream>

int main() {
    // Basic while loop
    int count = 1;
    std::cout << "Counting with while loop:" << std::endl;
    while (count <= 5) {
        std::cout << count << " ";
        count++;
    }
    std::cout << std::endl;
    
    // Input validation with while loop
    int number;
    std::cout << "\nEnter a positive number: ";
    std::cin >> number;
    
    while (number <= 0) {
        std::cout << "Please enter a positive number: ";
        std::cin >> number;
    }
    
    std::cout << "You entered: " << number << std::endl;
    
    return 0;
}
```

### do-while Loop
```cpp
#include <iostream>

int main() {
    // Menu-driven program
    int choice;
    
    do {
        std::cout << "\n=== Menu ===" << std::endl;
        std::cout << "1. Add" << std::endl;
        std::cout << "2. Subtract" << std::endl;
        std::cout << "3. Multiply" << std::endl;
        std::cout << "4. Exit" << std::endl;
        std::cout << "Enter your choice (1-4): ";
        std::cin >> choice;
        
        switch (choice) {
            case 1:
                std::cout << "Addition selected" << std::endl;
                break;
            case 2:
                std::cout << "Subtraction selected" << std::endl;
                break;
            case 3:
                std::cout << "Multiplication selected" << std::endl;
                break;
            case 4:
                std::cout << "Exiting..." << std::endl;
                break;
            default:
                std::cout << "Invalid choice!" << std::endl;
        }
    } while (choice != 4);
    
    return 0;
}
```

## Loop Control Statements

### break Statement
```cpp
#include <iostream>

int main() {
    // Breaking out of a loop
    std::cout << "Finding first multiple of 7 between 1 and 20:" << std::endl;
    for (int i = 1; i <= 20; i++) {
        if (i % 7 == 0) {
            std::cout << "First multiple of 7 is: " << i << std::endl;
            break;  // Exit the loop
        }
    }
    
    // Breaking out of nested loops
    std::cout << "\nNested loop example:" << std::endl;
    for (int i = 1; i <= 3; i++) {
        for (int j = 1; j <= 3; j++) {
            std::cout << i << " " << j << std::endl;
            if (i == 2 && j == 2) {
                std::cout << "Breaking out of inner loop" << std::endl;
                break;  // Only breaks inner loop
            }
        }
    }
    
    return 0;
}
```

### continue Statement
```cpp
#include <iostream>

int main() {
    // Skipping even numbers
    std::cout << "Odd numbers from 1 to 10:" << std::endl;
    for (int i = 1; i <= 10; i++) {
        if (i % 2 == 0) {
            continue;  // Skip even numbers
        }
        std::cout << i << " ";
    }
    std::cout << std::endl;
    
    // Skipping specific values
    std::cout << "\nNumbers 1-10 except 5 and 8:" << std::endl;
    for (int i = 1; i <= 10; i++) {
        if (i == 5 || i == 8) {
            continue;
        }
        std::cout << i << " ";
    }
    std::cout << std::endl;
    
    return 0;
}
```

## Complete Example: Number Guessing Game

```cpp
#include <iostream>
#include <cstdlib>  // for rand() and srand()
#include <ctime>    // for time()
#include <limits>

int main() {
    // Seed the random number generator
    std::srand(std::time(0));
    
    // Generate random number between 1 and 100
    int secretNumber = std::rand() % 100 + 1;
    int guess;
    int attempts = 0;
    const int maxAttempts = 7;
    
    std::cout << "=== Number Guessing Game ===" << std::endl;
    std::cout << "I'm thinking of a number between 1 and 100." << std::endl;
    std::cout << "You have " << maxAttempts << " attempts to guess it." << std::endl;
    
    do {
        std::cout << "\nAttempt " << (attempts + 1) << "/" << maxAttempts << std::endl;
        std::cout << "Enter your guess: ";
        
        // Input validation
        while (!(std::cin >> guess) || guess < 1 || guess > 100) {
            std::cout << "Invalid input! Please enter a number between 1 and 100: ";
            std::cin.clear();
            std::cin.ignore(std::numeric_limits<std::streamsize>::max(), '\n');
        }
        
        attempts++;
        
        if (guess == secretNumber) {
            std::cout << "\nCongratulations! You guessed the number in " << attempts << " attempts!" << std::endl;
            
            // Performance rating
            if (attempts == 1) {
                std::cout << "Amazing! You got it on the first try!" << std::endl;
            } else if (attempts <= 3) {
                std::cout << "Excellent performance!" << std::endl;
            } else if (attempts <= 5) {
                std::cout << "Good job!" << std::endl;
            } else {
                std::cout << "Not bad! Keep practicing!" << std::endl;
            }
            break;
        } else if (guess < secretNumber) {
            std::cout << "Too low! ";
        } else {
            std::cout << "Too high! ";
        }
        
        // Give hints based on remaining attempts
        int remaining = maxAttempts - attempts;
        if (remaining > 0) {
            std::cout << "You have " << remaining << " attempts left." << std::endl;
            
            // Provide range hint
            int difference = std::abs(guess - secretNumber);
            if (difference <= 5) {
                std::cout << "You're very close!" << std::endl;
            } else if (difference <= 10) {
                std::cout << "You're getting warmer!" << std::endl;
            } else {
                std::cout << "You're far from the target." << std::endl;
            }
        }
        
    } while (attempts < maxAttempts);
    
    if (attempts >= maxAttempts && guess != secretNumber) {
        std::cout << "\nGame Over! You've used all " << maxAttempts << " attempts." << std::endl;
        std::cout << "The secret number was: " << secretNumber << std::endl;
    }
    
    // Ask to play again
    char playAgain;
    std::cout << "\nWould you like to play again? (y/n): ";
    std::cin >> playAgain;
    
    if (playAgain == 'y' || playAgain == 'Y') {
        std::cout << "\nStarting a new game..." << std::endl;
        // In a real program, you might wrap the game logic in a function
        // and call it recursively or use a loop
    }
    
    std::cout << "Thanks for playing!" << std::endl;
    
    return 0;
}
```

## Practice Exercises

### Exercise 1: Calculator Menu
Create a calculator program that:
- Displays a menu with operations (+, -, *, /, %)
- Takes two numbers as input
- Performs the selected operation
- Handles division by zero
- Allows the user to perform multiple calculations

### Exercise 2: Prime Number Checker
Write a program that:
- Takes a number as input
- Checks if it's a prime number
- Displays the result
- Allows the user to check multiple numbers

### Exercise 3: Pattern Printing
Write programs to print the following patterns using loops:
- Right triangle of stars
- Inverted triangle
- Pyramid
- Diamond shape

### Exercise 4: Student Grade System
Create a student grade management system that:
- Takes student information (name, marks for 5 subjects)
- Calculates total and average
- Determines grade based on average
- Displays a report card

## Key Takeaways
- Use `std::cin` for input and `std::cout` for output
- `std::getline()` reads entire lines including spaces
- Input validation is crucial for robust programs
- `if-else` statements handle conditional logic
- `switch` statements are useful for multiple conditions
- Loops (`for`, `while`, `do-while`) enable repetitive tasks
- `break` exits loops, `continue` skips iterations
- Combine control structures to create complex programs

## Next Module
In the next module, we'll explore functions and program organization in C++.