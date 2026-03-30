using System;
using System.Collections.Generic;

// Sorting Algorithms Implementation

public class SortingAlgorithms
{
    // Bubble Sort - O(n²) time complexity
    public static void BubbleSort(int[] arr)
    {
        int n = arr.Length;
        for (int i = 0; i < n - 1; i++)
        {
            for (int j = 0; j < n - i - 1; j++)
            {
                if (arr[j] > arr[j + 1])
                {
                    // Swap elements
                    int temp = arr[j];
                    arr[j] = arr[j + 1];
                    arr[j + 1] = temp;
                }
            }
        }
    }
    
    // Selection Sort - O(n²) time complexity
    public static void SelectionSort(int[] arr)
    {
        int n = arr.Length;
        for (int i = 0; i < n - 1; i++)
        {
            int minIndex = i;
            for (int j = i + 1; j < n; j++)
            {
                if (arr[j] < arr[minIndex])
                {
                    minIndex = j;
                }
            }
            
            // Swap elements
            int temp = arr[i];
            arr[i] = arr[minIndex];
            arr[minIndex] = temp;
        }
    }
    
    // Insertion Sort - O(n²) time complexity, efficient for small datasets
    public static void InsertionSort(int[] arr)
    {
        for (int i = 1; i < arr.Length; i++)
        {
            int key = arr[i];
            int j = i - 1;
            
            while (j >= 0 && arr[j] > key)
            {
                arr[j + 1] = arr[j];
                j--;
            }
            arr[j + 1] = key;
        }
    }
    
    // Merge Sort - O(n log n) time complexity, stable sort
    public static void MergeSort(int[] arr)
    {
        MergeSortRecursive(arr, 0, arr.Length - 1);
    }
    
    private static void MergeSortRecursive(int[] arr, int left, int right)
    {
        if (left < right)
        {
            int middle = left + (right - left) / 2;
            MergeSortRecursive(arr, left, middle);
            MergeSortRecursive(arr, middle + 1, right);
            Merge(arr, left, middle, right);
        }
    }
    
    private static void Merge(int[] arr, int left, int middle, int right)
    {
        int n1 = middle - left + 1;
        int n2 = right - middle;
        
        // Create temporary arrays
        int[] leftArray = new int[n1];
        int[] rightArray = new int[n2];
        
        // Copy data to temporary arrays
        Array.Copy(arr, left, leftArray, 0, n1);
        Array.Copy(arr, middle + 1, rightArray, 0, n2);
        
        // Merge the temporary arrays
        int i = 0, j = 0, k = left;
        
        while (i < n1 && j < n2)
        {
            if (leftArray[i] <= rightArray[j])
            {
                arr[k] = leftArray[i];
                i++;
            }
            else
            {
                arr[k] = rightArray[j];
                j++;
            }
            k++;
        }
        
        // Copy remaining elements
        while (i < n1)
        {
            arr[k] = leftArray[i];
            i++;
            k++;
        }
        
        while (j < n2)
        {
            arr[k] = rightArray[j];
            j++;
            k++;
        }
    }
    
    // Quick Sort - O(n log n) average case, O(n²) worst case
    public static void QuickSort(int[] arr)
    {
        QuickSortRecursive(arr, 0, arr.Length - 1);
    }
    
    private static void QuickSortRecursive(int[] arr, int low, int high)
    {
        if (low < high)
        {
            int partitionIndex = Partition(arr, low, high);
            QuickSortRecursive(arr, low, partitionIndex - 1);
            QuickSortRecursive(arr, partitionIndex + 1, high);
        }
    }
    
    private static int Partition(int[] arr, int low, int high)
    {
        int pivot = arr[high];
        int i = low - 1;
        
        for (int j = low; j < high; j++)
        {
            if (arr[j] < pivot)
            {
                i++;
                Swap(arr, i, j);
            }
        }
        
        Swap(arr, i + 1, high);
        return i + 1;
    }
    
    private static void Swap(int[] arr, int i, int j)
    {
        int temp = arr[i];
        arr[i] = arr[j];
        arr[j] = temp;
    }
    
    // Heap Sort - O(n log n) time complexity
    public static void HeapSort(int[] arr)
    {
        int n = arr.Length;
        
        // Build max heap
        for (int i = n / 2 - 1; i >= 0; i--)
        {
            Heapify(arr, n, i);
        }
        
        // Extract elements from heap
        for (int i = n - 1; i > 0; i--)
        {
            // Move current root to end
            Swap(arr, 0, i);
            
            // Heapify reduced heap
            Heapify(arr, i, 0);
        }
    }
    
    private static void Heapify(int[] arr, int n, int i)
    {
        int largest = i;
        int left = 2 * i + 1;
        int right = 2 * i + 2;
        
        if (left < n && arr[left] > arr[largest])
        {
            largest = left;
        }
        
        if (right < n && arr[right] > arr[largest])
        {
            largest = right;
        }
        
        if (largest != i)
        {
            Swap(arr, i, largest);
            Heapify(arr, n, largest);
        }
    }
    
    // Counting Sort - O(n + k) time complexity, for integers in a range
    public static void CountingSort(int[] arr)
    {
        if (arr.Length == 0) return;
        
        int max = arr[0];
        int min = arr[0];
        
        // Find min and max values
        for (int i = 1; i < arr.Length; i++)
        {
            if (arr[i] > max) max = arr[i];
            if (arr[i] < min) min = arr[i];
        }
        
        int range = max - min + 1;
        int[] count = new int[range];
        int[] output = new int[arr.Length];
        
        // Count occurrences
        for (int i = 0; i < arr.Length; i++)
        {
            count[arr[i] - min]++;
        }
        
        // Calculate cumulative count
        for (int i = 1; i < count.Length; i++)
        {
            count[i] += count[i - 1];
        }
        
        // Build output array
        for (int i = arr.Length - 1; i >= 0; i--)
        {
            output[count[arr[i] - min] - 1] = arr[i];
            count[arr[i] - min]--;
        }
        
        // Copy to original array
        Array.Copy(output, arr, arr.Length);
    }
}

// Demonstration class
public class SortingDemo
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Sorting Algorithms Demonstration ===");
        
        int[] originalArray = { 64, 34, 25, 12, 22, 11, 90, 88, 76, 50, 42 };
        
        // Test each sorting algorithm
        TestSortingAlgorithm("Bubble Sort", originalArray, SortingAlgorithms.BubbleSort);
        TestSortingAlgorithm("Selection Sort", originalArray, SortingAlgorithms.SelectionSort);
        TestSortingAlgorithm("Insertion Sort", originalArray, SortingAlgorithms.InsertionSort);
        TestSortingAlgorithm("Merge Sort", originalArray, SortingAlgorithms.MergeSort);
        TestSortingAlgorithm("Quick Sort", originalArray, SortingAlgorithms.QuickSort);
        TestSortingAlgorithm("Heap Sort", originalArray, SortingAlgorithms.HeapSort);
        TestSortingAlgorithm("Counting Sort", originalArray, SortingAlgorithms.CountingSort);
    }
    
    private static void TestSortingAlgorithm(string algorithmName, int[] originalArray, Action<int[]> sortAlgorithm)
    {
        int[] testArray = new int[originalArray.Length];
        Array.Copy(originalArray, testArray, originalArray.Length);
        
        Console.WriteLine($"\n--- {algorithmName} ---");
        Console.WriteLine($"Original: [{string.Join(", ", testArray)}]");
        
        var stopwatch = System.Diagnostics.Stopwatch.StartNew();
        sortAlgorithm(testArray);
        stopwatch.Stop();
        
        Console.WriteLine($"Sorted:   [{string.Join(", ", testArray)}]");
        Console.WriteLine($"Time: {stopwatch.ElapsedTicks} ticks");
        
        // Verify array is sorted
        bool isSorted = IsSorted(testArray);
        Console.WriteLine($"Correctly sorted: {isSorted}");
    }
    
    private static bool IsSorted(int[] arr)
    {
        for (int i = 1; i < arr.Length; i++)
        {
            if (arr[i] < arr[i - 1])
                return false;
        }
        return true;
    }
}

// Sorting Algorithm Comparison
public class SortingComparison
{
    public static void CompareAlgorithms()
    {
        Console.WriteLine("\n=== Algorithm Comparison ===");
        
        int[] sizes = { 100, 1000, 5000 };
        
        foreach (int size in sizes)
        {
            Console.WriteLine($"\nArray size: {size}");
            int[] randomArray = GenerateRandomArray(size);
            
            CompareAlgorithm("Bubble Sort", randomArray, SortingAlgorithms.BubbleSort);
            CompareAlgorithm("Selection Sort", randomArray, SortingAlgorithms.SelectionSort);
            CompareAlgorithm("Insertion Sort", randomArray, SortingAlgorithms.InsertionSort);
            CompareAlgorithm("Merge Sort", randomArray, SortingAlgorithms.MergeSort);
            CompareAlgorithm("Quick Sort", randomArray, SortingAlgorithms.QuickSort);
            CompareAlgorithm("Heap Sort", randomArray, SortingAlgorithms.HeapSort);
        }
    }
    
    private static void CompareAlgorithm(string name, int[] originalArray, Action<int[]> algorithm)
    {
        int[] testArray = new int[originalArray.Length];
        Array.Copy(originalArray, testArray, originalArray.Length);
        
        var stopwatch = System.Diagnostics.Stopwatch.StartNew();
        algorithm(testArray);
        stopwatch.Stop();
        
        Console.WriteLine($"{name,-15}: {stopwatch.ElapsedMilliseconds,6} ms");
    }
    
    private static int[] GenerateRandomArray(int size)
    {
        Random random = new Random();
        int[] array = new int[size];
        
        for (int i = 0; i < size; i++)
        {
            array[i] = random.Next(1, 1000);
        }
        
        return array;
    }
}
