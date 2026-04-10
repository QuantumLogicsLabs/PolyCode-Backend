# Java Control Flow

## Conditional Statements

### If-Else Statements
```java
int score = 85;

if (score >= 90) {
    System.out.println("Grade: A");
} else if (score >= 80) {
    System.out.println("Grade: B");
} else if (score >= 70) {
    System.out.println("Grade: C");
} else {
    System.out.println("Grade: F");
}

// Single line if
if (score > 60) System.out.println("Passed");

// Ternary operator
String result = (score >= 60) ? "Passed" : "Failed";
```

### Switch Statement
```java
int day = 3;
String dayName;

switch (day) {
    case 1:
        dayName = "Monday";
        break;
    case 2:
        dayName = "Tuesday";
        break;
    case 3:
        dayName = "Wednesday";
        break;
    case 4:
        dayName = "Thursday";
        break;
    case 5:
        dayName = "Friday";
        break;
    case 6:
    case 7:
        dayName = "Weekend";
        break;
    default:
        dayName = "Invalid day";
        break;
}

// Enhanced switch (Java 14+)
String dayName2 = switch (day) {
    case 1 -> "Monday";
    case 2 -> "Tuesday";
    case 3 -> "Wednesday";
    case 4 -> "Thursday";
    case 5 -> "Friday";
    case 6, 7 -> "Weekend";
    default -> "Invalid day";
};
```

## Looping Statements

### For Loop
```java
// Traditional for loop
for (int i = 0; i < 10; i++) {
    System.out.println("Count: " + i);
}

// For loop with multiple variables
for (int i = 0, j = 10; i < j; i++, j--) {
    System.out.println("i: " + i + ", j: " + j);
}

// Nested for loop
for (int i = 1; i <= 3; i++) {
    for (int j = 1; j <= 3; j++) {
        System.out.println(i + " x " + j + " = " + (i * j));
    }
}
```

### Enhanced For Loop
```java
String[] names = {"Alice", "Bob", "Charlie"};

for (String name : names) {
    System.out.println("Hello, " + name);
}

// With collections
List<Integer> numbers = Arrays.asList(1, 2, 3, 4, 5);
for (Integer num : numbers) {
    System.out.println("Number: " + num);
}
```

### While Loop
```java
int count = 0;

while (count < 5) {
    System.out.println("Count: " + count);
    count++;
}

// Infinite loop with break
int i = 0;
while (true) {
    if (i >= 5) break;
    System.out.println("Count: " + i);
    i++;
}
```

### Do-While Loop
```java
int count = 0;

do {
    System.out.println("Count: " + count);
    count++;
} while (count < 5);

// Always executes at least once
boolean condition = false;
do {
    System.out.println("This will execute once");
} while (condition);
```

## Jump Statements

### Break Statement
```java
// Break out of loop
for (int i = 0; i < 10; i++) {
    if (i == 5) {
        break; // Exit loop when i equals 5
    }
    System.out.println("i: " + i);
}

// Break in nested loops
outer: for (int i = 0; i < 3; i++) {
    for (int j = 0; j < 3; j++) {
        if (i == 1 && j == 1) {
            break outer; // Exit outer loop
        }
        System.out.println("i: " + i + ", j: " + j);
    }
}
```

### Continue Statement
```java
// Skip current iteration
for (int i = 0; i < 10; i++) {
    if (i % 2 == 0) {
        continue; // Skip even numbers
    }
    System.out.println("Odd number: " + i);
}

// Continue in nested loops
for (int i = 0; i < 3; i++) {
    for (int j = 0; j < 3; j++) {
        if (j == 1) {
            continue; // Skip j = 1
        }
        System.out.println("i: " + i + ", j: " + j);
    }
}
```

### Return Statement
```java
public int findMax(int[] numbers) {
    if (numbers == null || numbers.length == 0) {
        return -1; // Return early
    }
    
    int max = numbers[0];
    for (int num : numbers) {
        if (num > max) {
            max = num;
        }
    }
    return max;
}
```

## Exception Handling

### Try-Catch Blocks
```java
try {
    int result = 10 / 0; // This will throw ArithmeticException
    System.out.println("Result: " + result);
} catch (ArithmeticException e) {
    System.out.println("Cannot divide by zero: " + e.getMessage());
} catch (Exception e) {
    System.out.println("General error: " + e.getMessage());
}

// Try with resources (Java 7+)
try (BufferedReader reader = new BufferedReader(new FileReader("file.txt"))) {
    String line = reader.readLine();
    System.out.println(line);
} catch (IOException e) {
    System.out.println("File error: " + e.getMessage());
}
```

### Finally Block
```java
try {
    // Code that might throw exception
    int result = 10 / 2;
    System.out.println("Result: " + result);
} catch (Exception e) {
    System.out.println("Error: " + e.getMessage());
} finally {
    System.out.println("This always executes");
}
```

## Examples

### Control Flow Examples
```java
public class ControlFlowExamples {
    public static void main(String[] args) {
        // Grade calculation
        int score = 85;
        String grade = calculateGrade(score);
        System.out.println("Score: " + score + ", Grade: " + grade);
        
        // Number patterns
        printPattern(5);
        
        // Factorial calculation
        int n = 5;
        int factorial = calculateFactorial(n);
        System.out.println("Factorial of " + n + " = " + factorial);
        
        // Even numbers
        printEvenNumbers(10);
    }
    
    public static String calculateGrade(int score) {
        if (score >= 90) {
            return "A";
        } else if (score >= 80) {
            return "B";
        } else if (score >= 70) {
            return "C";
        } else if (score >= 60) {
            return "D";
        } else {
            return "F";
        }
    }
    
    public static void printPattern(int rows) {
        for (int i = 1; i <= rows; i++) {
            for (int j = 1; j <= i; j++) {
                System.out.print("* ");
            }
            System.out.println();
        }
    }
    
    public static int calculateFactorial(int n) {
        if (n < 0) {
            return -1; // Error case
        }
        
        int result = 1;
        for (int i = 1; i <= n; i++) {
            result *= i;
        }
        return result;
    }
    
    public static void printEvenNumbers(int limit) {
        int count = 0;
        int num = 0;
        
        while (count < limit) {
            if (num % 2 == 0) {
                System.out.println("Even number: " + num);
                count++;
            }
            num++;
        }
    }
}
```

## Best Practices
- Use if-else for simple conditions, switch for multiple cases
- Use enhanced for loop when you don't need the index
- Use try-with-resources for automatic resource management
- Keep loops simple and readable
- Avoid deep nesting of control structures
- Use meaningful variable names in loops
- Handle exceptions appropriately
- Use break and continue sparingly
- Consider using early returns for clarity
- Use finally blocks for cleanup code
