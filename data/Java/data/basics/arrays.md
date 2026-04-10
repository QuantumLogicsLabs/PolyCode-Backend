# Java Arrays

## Array Declaration

### Basic Array Declaration
```java
// Declaration and initialization
int[] numbers = new int[5];
numbers[0] = 10;
numbers[1] = 20;
numbers[2] = 30;
numbers[3] = 40;
numbers[4] = 50;

// Array literal
int[] primes = {2, 3, 5, 7, 11, 13, 17, 19};

// Different data types
String[] names = {"Alice", "Bob", "Charlie"};
double[] prices = {99.99, 49.99, 29.99};
boolean[] flags = {true, false, true};
```

### Array Creation
```java
// Create with size
int[] array1 = new int[10];
String[] array2 = new String[5];

// Create with values
int[] array3 = {1, 2, 3, 4, 5};

// Create with new keyword
int[] array4 = new int[]{1, 2, 3, 4, 5};
```

## Array Operations

### Accessing Elements
```java
int[] numbers = {10, 20, 30, 40, 50};

// Access by index
int first = numbers[0];    // 10
int last = numbers[4];     // 50
int middle = numbers[2];   // 30

// Array length
int length = numbers.length; // 5

// Access last element dynamically
int lastElement = numbers[numbers.length - 1];
```

### Modifying Elements
```java
int[] numbers = {10, 20, 30, 40, 50};

// Modify elements
numbers[0] = 100;
numbers[2] = 300;

// Increment all elements
for (int i = 0; i < numbers.length; i++) {
    numbers[i] += 10;
}
```

## Array Traversal

### For Loop
```java
int[] numbers = {10, 20, 30, 40, 50};

// Traditional for loop
for (int i = 0; i < numbers.length; i++) {
    System.out.println("Element " + i + ": " + numbers[i]);
}

// Enhanced for loop
for (int num : numbers) {
    System.out.println("Value: " + num);
}
```

### While Loop
```java
int[] numbers = {10, 20, 30, 40, 50};
int i = 0;

while (i < numbers.length) {
    System.out.println("Element " + i + ": " + numbers[i]);
    i++;
}
```

## Array Algorithms

### Find Maximum/Minimum
```java
public static int findMax(int[] array) {
    if (array == null || array.length == 0) {
        return Integer.MIN_VALUE;
    }
    
    int max = array[0];
    for (int num : array) {
        if (num > max) {
            max = num;
        }
    }
    return max;
}

public static int findMin(int[] array) {
    if (array == null || array.length == 0) {
        return Integer.MAX_VALUE;
    }
    
    int min = array[0];
    for (int num : array) {
        if (num < min) {
            min = num;
        }
    }
    return min;
}
```

### Search Array
```java
public static int linearSearch(int[] array, int target) {
    for (int i = 0; i < array.length; i++) {
        if (array[i] == target) {
            return i; // Found at index i
        }
    }
    return -1; // Not found
}

public static boolean contains(int[] array, int target) {
    return linearSearch(array, target) != -1;
}
```

### Sort Array
```java
// Bubble sort
public static void bubbleSort(int[] array) {
    for (int i = 0; i < array.length - 1; i++) {
        for (int j = 0; j < array.length - i - 1; j++) {
            if (array[j] > array[j + 1]) {
                // Swap elements
                int temp = array[j];
                array[j] = array[j + 1];
                array[j + 1] = temp;
            }
        }
    }
}

// Using built-in sort
import java.util.Arrays;
int[] numbers = {5, 2, 8, 1, 9};
Arrays.sort(numbers);
```

## Multidimensional Arrays

### 2D Arrays
```java
// Declaration and initialization
int[][] matrix = new int[3][3];
matrix[0][0] = 1;
matrix[0][1] = 2;
matrix[0][2] = 3;
matrix[1][0] = 4;
matrix[1][1] = 5;
matrix[1][2] = 6;
matrix[2][0] = 7;
matrix[2][1] = 8;
matrix[2][2] = 9;

// 2D array literal
int[][] matrix2 = {
    {1, 2, 3},
    {4, 5, 6},
    {7, 8, 9}
};

// Accessing elements
int value = matrix[1][2]; // 6

// Traversing 2D array
for (int i = 0; i < matrix.length; i++) {
    for (int j = 0; j < matrix[i].length; j++) {
        System.out.print(matrix[i][j] + " ");
    }
    System.out.println();
}
```

### Jagged Arrays
```java
// Jagged array (different lengths)
int[][] jagged = new int[3][];
jagged[0] = new int[2];
jagged[1] = new int[4];
jagged[2] = new int[3];

// Initialize jagged array
jagged[0] = {1, 2};
jagged[1] = {3, 4, 5, 6};
jagged[2] = {7, 8, 9};

// Traversing jagged array
for (int i = 0; i < jagged.length; i++) {
    for (int j = 0; j < jagged[i].length; j++) {
        System.out.print(jagged[i][j] + " ");
    }
    System.out.println();
}
```

## Array Utilities

### Array Copy
```java
int[] source = {1, 2, 3, 4, 5};
int[] destination = new int[source.length];

// Manual copy
for (int i = 0; i < source.length; i++) {
    destination[i] = source[i];
}

// Using System.arraycopy
System.arraycopy(source, 0, destination, 0, source.length);

// Using Arrays.copyOf
int[] copy = Arrays.copyOf(source, source.length);
```

### Array Comparison
```java
int[] array1 = {1, 2, 3};
int[] array2 = {1, 2, 3};
int[] array3 = {1, 2, 4};

// Manual comparison
boolean equal = true;
if (array1.length != array2.length) {
    equal = false;
} else {
    for (int i = 0; i < array1.length; i++) {
        if (array1[i] != array2[i]) {
            equal = false;
            break;
        }
    }
}

// Using Arrays.equals
boolean equal2 = Arrays.equals(array1, array2);
boolean equal3 = Arrays.equals(array1, array3);
```

### Array Conversion
```java
// Array to String
int[] numbers = {1, 2, 3, 4, 5};
String arrayString = Arrays.toString(numbers);
// Output: [1, 2, 3, 4, 5]

// Array to List
String[] names = {"Alice", "Bob", "Charlie"};
List<String> nameList = Arrays.asList(names);

// List to Array
List<Integer> numberList = Arrays.asList(1, 2, 3, 4, 5);
Integer[] numberArray = numberList.toArray(new Integer[0]);
```

## Examples

### Array Examples
```java
public class ArrayExamples {
    public static void main(String[] args) {
        // Basic array operations
        int[] numbers = {10, 20, 30, 40, 50};
        
        System.out.println("Array elements:");
        for (int num : numbers) {
            System.out.print(num + " ");
        }
        System.out.println();
        
        // Find max and min
        int max = findMax(numbers);
        int min = findMin(numbers);
        System.out.println("Max: " + max + ", Min: " + min);
        
        // Search example
        int target = 30;
        int index = linearSearch(numbers, target);
        System.out.println("Index of " + target + ": " + index);
        
        // 2D array example
        int[][] matrix = {
            {1, 2, 3},
            {4, 5, 6},
            {7, 8, 9}
        };
        
        System.out.println("2D array:");
        for (int[] row : matrix) {
            for (int val : row) {
                System.out.print(val + " ");
            }
            System.out.println();
        }
        
        // Sort array
        int[] unsorted = {5, 2, 8, 1, 9};
        Arrays.sort(unsorted);
        System.out.println("Sorted array: " + Arrays.toString(unsorted));
    }
    
    public static int findMax(int[] array) {
        int max = array[0];
        for (int num : array) {
            if (num > max) max = num;
        }
        return max;
    }
    
    public static int findMin(int[] array) {
        int min = array[0];
        for (int num : array) {
            if (num < min) min = num;
        }
        return min;
    }
    
    public static int linearSearch(int[] array, int target) {
        for (int i = 0; i < array.length; i++) {
            if (array[i] == target) return i;
        }
        return -1;
    }
}
```

## Best Practices
- Use enhanced for loop when index is not needed
- Check array bounds before accessing elements
- Use Arrays utility class for common operations
- Consider using ArrayList for dynamic size requirements
- Initialize arrays with meaningful default values
- Use meaningful variable names for array indices
- Avoid magic numbers for array sizes
- Use System.arraycopy for efficient array copying
- Consider memory usage for large arrays
- Use appropriate data types for array elements
