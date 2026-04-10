import java.util.Arrays;

public class ArraysExample {
    public static void main(String[] args) {
        // Array Declaration and Initialization
        System.out.println("=== Array Declaration and Initialization ===");
        
        // Method 1: Declare and initialize
        int[] numbers = {1, 2, 3, 4, 5};
        String[] fruits = {"Apple", "Banana", "Orange"};
        
        // Method 2: Declare then initialize
        double[] prices = new double[3];
        prices[0] = 10.99;
        prices[1] = 5.99;
        prices[2] = 2.99;
        
        // Method 3: Declare with size and initialize
        int[] squares = new int[5];
        for (int i = 0; i < squares.length; i++) {
            squares[i] = i * i;
        }
        
        // Accessing Array Elements
        System.out.println("\n=== Accessing Array Elements ===");
        System.out.println("First number: " + numbers[0]);
        System.out.println("Last fruit: " + fruits[fruits.length - 1]);
        System.out.println("Array length: " + numbers.length);
        
        // Iterating through Arrays
        System.out.println("\n=== Array Iteration ===");
        
        // Using traditional for loop
        System.out.println("Numbers (for loop):");
        for (int i = 0; i < numbers.length; i++) {
            System.out.print(numbers[i] + " ");
        }
        System.out.println();
        
        // Using enhanced for loop
        System.out.println("Fruits (enhanced for loop):");
        for (String fruit : fruits) {
            System.out.print(fruit + " ");
        }
        System.out.println();
        
        // Using Arrays.toString()
        System.out.println("Squares (Arrays.toString): " + Arrays.toString(squares));
        
        // Array Operations
        System.out.println("\n=== Array Operations ===");
        
        // Finding min and max
        int[] values = {45, 12, 89, 23, 67, 34};
        int min = values[0];
        int max = values[0];
        int sum = 0;
        
        for (int value : values) {
            if (value < min) min = value;
            if (value > max) max = value;
            sum += value;
        }
        
        System.out.println("Array: " + Arrays.toString(values));
        System.out.println("Minimum: " + min);
        System.out.println("Maximum: " + max);
        System.out.println("Sum: " + sum);
        System.out.println("Average: " + (double) sum / values.length);
        
        // Searching in Array
        System.out.println("\n=== Array Searching ===");
        int target = 23;
        int index = -1;
        
        for (int i = 0; i < values.length; i++) {
            if (values[i] == target) {
                index = i;
                break;
            }
        }
        
        if (index != -1) {
            System.out.println(target + " found at index " + index);
        } else {
            System.out.println(target + " not found in array");
        }
        
        // Array Sorting
        System.out.println("\n=== Array Sorting ===");
        int[] unsorted = {64, 34, 25, 12, 22, 11, 90};
        System.out.println("Before sorting: " + Arrays.toString(unsorted));
        
        // Using Arrays.sort()
        Arrays.sort(unsorted);
        System.out.println("After sorting: " + Arrays.toString(unsorted));
        
        // Bubble sort implementation
        int[] bubbleArray = {64, 34, 25, 12, 22, 11, 90};
        bubbleSort(bubbleArray);
        System.out.println("Bubble sort result: " + Arrays.toString(bubbleArray));
        
        // Multi-dimensional Arrays
        System.out.println("\n=== Multi-dimensional Arrays ===");
        
        // 2D Array
        int[][] matrix = {
            {1, 2, 3},
            {4, 5, 6},
            {7, 8, 9}
        };
        
        System.out.println("2D Array:");
        for (int i = 0; i < matrix.length; i++) {
            for (int j = 0; j < matrix[i].length; j++) {
                System.out.print(matrix[i][j] + "\t");
            }
            System.out.println();
        }
        
        // Jagged Array (irregular 2D array)
        int[][] jagged = {
            {1, 2},
            {3, 4, 5},
            {6, 7, 8, 9}
        };
        
        System.out.println("\nJagged Array:");
        for (int i = 0; i < jagged.length; i++) {
            System.out.println("Row " + i + ": " + Arrays.toString(jagged[i]));
        }
        
        // Array Copy
        System.out.println("\n=== Array Copy ===");
        int[] original = {1, 2, 3, 4, 5};
        
        // Method 1: Using Arrays.copyOf()
        int[] copy1 = Arrays.copyOf(original, original.length);
        
        // Method 2: Using System.arraycopy()
        int[] copy2 = new int[original.length];
        System.arraycopy(original, 0, copy2, 0, original.length);
        
        // Method 3: Using clone()
        int[] copy3 = original.clone();
        
        System.out.println("Original: " + Arrays.toString(original));
        System.out.println("Copy 1 (Arrays.copyOf): " + Arrays.toString(copy1));
        System.out.println("Copy 2 (System.arraycopy): " + Arrays.toString(copy2));
        System.out.println("Copy 3 (clone): " + Arrays.toString(copy3));
        
        // Modifying copy to show independence
        copy1[0] = 99;
        System.out.println("After modifying copy1[0] = 99:");
        System.out.println("Original: " + Arrays.toString(original));
        System.out.println("Copy 1: " + Arrays.toString(copy1));
    }
    
    // Bubble sort implementation
    public static void bubbleSort(int[] arr) {
        int n = arr.length;
        for (int i = 0; i < n - 1; i++) {
            for (int j = 0; j < n - i - 1; j++) {
                if (arr[j] > arr[j + 1]) {
                    // Swap arr[j] and arr[j+1]
                    int temp = arr[j];
                    arr[j] = arr[j + 1];
                    arr[j + 1] = temp;
                }
            }
        }
    }
}
