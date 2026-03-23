# Sorting Algorithms - Complete Guide

This guide covers sorting algorithms from basic to advanced, with implementations, analysis, and optimizations.

## 📚 Table of Contents

1. [Introduction to Sorting](#introduction-to-sorting)
2. [Basic Sorting Algorithms](#basic-sorting-algorithms)
3. [Efficient Sorting Algorithms](#efficient-sorting-algorithms)
4. [Specialized Sorting Algorithms](#specialized-sorting-algorithms)
5. [Algorithm Analysis](#algorithm-analysis)
6. [Implementation Comparisons](#implementation-comparisons)

---

## Introduction to Sorting

### What is Sorting?
Sorting is the process of arranging items in a specific order (ascending, descending, or custom order).

### Why Learn Sorting?
- **Fundamental Concept**: Essential for computer science understanding
- **Problem-Solving**: Develops algorithmic thinking
- **Practical Applications**: Data organization, searching, optimization
- **Interview Preparation**: Common interview topic

### Key Concepts
- **In-place vs Out-of-place**: Memory usage patterns
- **Stable vs Unstable**: Equal element handling
- **Adaptive**: Performance on partially sorted data
- **Time Complexity**: Big O notation analysis

---

## Basic Sorting Algorithms

### Bubble Sort

#### Concept
Repeatedly step through the list, compare adjacent elements, and swap them if they're in wrong order.

#### Algorithm Steps
1. Start from the beginning of the list
2. Compare adjacent elements
3. Swap if they're in wrong order
4. Continue until end of list
5. Repeat until no swaps are needed

#### Implementation
```java
def bubble_sort(arr):
    """Bubble Sort implementation with optimization"""
    n = len(arr)
    
    for i in range(n):
        # Flag to detect if any swap happened
        swapped = False
        
        # Last i elements are already in place
        for j in range(0, n - i - 1):
            if arr[j] > arr[j + 1]:
                # Swap elements
                arr[j], arr[j + 1] = arr[j + 1], arr[j]
                swapped = True
        
        # If no swapping occurred, array is sorted
        if not swapped:
            break
    
    return arr

# Example usage
arr = [64, 34, 25, 12, 22, 11, 90]
sorted_arr = bubble_sort(arr.copy())
print(f"Original: {arr}")
print(f"Sorted: {sorted_arr}")
```

#### Analysis
- **Time Complexity**: O(n²) worst and average, O(n) best
- **Space Complexity**: O(1) - in-place
- **Stability**: Stable
- **Adaptive**: Yes (with optimization)

#### Pros and Cons
**Pros:**
- Simple to understand and implement
- Works well on small datasets
- Adaptive with optimization

**Cons:**
- Inefficient for large datasets
- Too many comparisons and swaps

---

### Selection Sort

#### Concept
Divide the list into sorted and unsorted portions, repeatedly select the smallest element from unsorted portion.

#### Algorithm Steps
1. Find minimum element in unsorted portion
2. Swap it with first unsorted element
3. Move boundary between sorted/unsorted portions
4. Repeat until sorted

#### Implementation
```java
def selection_sort(arr):
    """Selection Sort implementation"""
    n = len(arr)
    
    for i in range(n):
        # Find minimum element in unsorted portion
        min_idx = i
        for j in range(i + 1, n):
            if arr[j] < arr[min_idx]:
                min_idx = j
        
        # Swap minimum element with first unsorted element
        arr[i], arr[min_idx] = arr[min_idx], arr[i]
    
    return arr

# Example usage
arr = [64, 25, 12, 22, 11]
sorted_arr = selection_sort(arr.copy())
print(f"Original: {arr}")
print(f"Sorted: {sorted_arr}")
```

#### Analysis
- **Time Complexity**: O(n²) always
- **Space Complexity**: O(1) - in-place
- **Stability**: Not stable
- **Adaptive**: No

#### Pros and Cons
**Pros:**
- Simple implementation
- Minimal memory usage
- Predictable performance

**Cons:**
- Always O(n²) regardless of input
- Not stable
- Not adaptive

---

### Insertion Sort

#### Concept
Build final sorted array one item at a time by inserting each element into its correct position.

#### Algorithm Steps
1. Start with second element (first is trivially sorted)
2. Compare with elements in sorted portion
3. Insert into correct position
4. Shift elements as needed
5. Continue until all elements are processed

#### Implementation
```java
def insertion_sort(arr):
    """Insertion Sort implementation"""
    for i in range(1, len(arr)):
        key = arr[i]
        j = i - 1
        
        # Move elements greater than key one position ahead
        while j >= 0 and arr[j] > key:
            arr[j + 1] = arr[j]
            j -= 1
        
        # Place key in its correct position
        arr[j + 1] = key
    
    return arr

# Example usage
arr = [12, 11, 13, 5, 6]
sorted_arr = insertion_sort(arr.copy())
print(f"Original: {arr}")
print(f"Sorted: {sorted_arr}")
```

#### Analysis
- **Time Complexity**: O(n²) worst/average, O(n) best
- **Space Complexity**: O(1) - in-place
- **Stability**: Stable
- **Adaptive**: Yes

#### Pros and Cons
**Pros:**
- Efficient for small datasets
- Stable
- Adaptive
- Online algorithm (can sort as it receives data)

**Cons:**
- Inefficient for large datasets
- Many element shifts in worst case

---

## Efficient Sorting Algorithms

### Merge Sort

#### Concept
Divide-and-conquer algorithm that splits array into halves, sorts them, then merges them back together.

#### Algorithm Steps
1. Divide array into two halves
2. Recursively sort each half
3. Merge the two sorted halves
4. Continue until base case (single element)

#### Implementation
```java
def merge_sort(arr):
    """Merge Sort implementation"""
    if len(arr) <= 1:
        return arr
    
    # Divide array into two halves
    mid = len(arr) // 2
    left = merge_sort(arr[:mid])
    right = merge_sort(arr[mid:])
    
    # Merge the sorted halves
    return merge(left, right)

def merge(left, right):
    """Merge two sorted arrays"""
    result = []
    i = j = 0
    
    # Compare elements from both arrays
    while i < len(left) and j < len(right):
        if left[i] <= right[j]:
            result.append(left[i])
            i += 1
        else:
            result.append(right[j])
            j += 1
    
    # Add remaining elements
    result.extend(left[i:])
    result.extend(right[j:])
    
    return result

# Example usage
arr = [12, 11, 13, 5, 6, 7]
sorted_arr = merge_sort(arr)
print(f"Original: {arr}")
print(f"Sorted: {sorted_arr}")
```

#### Analysis
- **Time Complexity**: O(n log n) always
- **Space Complexity**: O(n) - requires extra space
- **Stability**: Stable
- **Adaptive**: No

#### Pros and Cons
**Pros:**
- Guaranteed O(n log n) performance
- Stable
- Excellent for linked lists
- Parallelizable

**Cons:**
- Requires extra space
- More complex than simple sorts
- Not in-place for arrays

---

### Quick Sort

#### Concept
Divide-and-conquer algorithm that selects a pivot element and partitions the array around it.

#### Algorithm Steps
1. Choose pivot element
2. Partition array around pivot
3. Recursively sort sub-arrays
4. Combine results

#### Implementation
```java
def quick_sort(arr):
    """Quick Sort implementation"""
    if len(arr) <= 1:
        return arr
    
    # Choose pivot (middle element for better average case)
    pivot = arr[len(arr) // 2]
    
    # Partition array
    left = [x for x in arr if x < pivot]
    middle = [x for x in arr if x == pivot]
    right = [x for x in arr if x > pivot]
    
    # Recursively sort and combine
    return quick_sort(left) + middle + quick_sort(right)

# In-place version (more efficient)
def quick_sort_inplace(arr, low=0, high=None):
    """In-place Quick Sort implementation"""
    if high is None:
        high = len(arr) - 1
    
    if low < high:
        # Partition array
        pi = partition(arr, low, high)
        
        # Recursively sort elements before and after partition
        quick_sort_inplace(arr, low, pi - 1)
        quick_sort_inplace(arr, pi + 1, high)
    
    return arr

def partition(arr, low, high):
    """Partition function for Quick Sort"""
    # Choose pivot as last element
    pivot = arr[high]
    i = low - 1
    
    for j in range(low, high):
        if arr[j] <= pivot:
            i += 1
            arr[i], arr[j] = arr[j], arr[i]
    
    # Place pivot in correct position
    arr[i + 1], arr[high] = arr[high], arr[i + 1]
    return i + 1

# Example usage
arr = [10, 7, 8, 9, 1, 5]
sorted_arr = quick_sort(arr)
print(f"Original: {arr}")
print(f"Sorted: {sorted_arr}")

# In-place version
arr_copy = arr.copy()
quick_sort_inplace(arr_copy)
print(f"In-place sorted: {arr_copy}")
```

#### Analysis
- **Time Complexity**: O(n log n) average, O(n²) worst
- **Space Complexity**: O(log n) average, O(n) worst
- **Stability**: Not stable (can be made stable)
- **Adaptive**: No

#### Pros and Cons
**Pros:**
- Fast average performance
- In-place (low space usage)
- Good cache performance
- Widely used in practice

**Cons:**
- O(n²) worst case
- Not stable
- Performance depends on pivot selection

---

### Heap Sort

#### Concept
Uses a binary heap data structure to sort elements by repeatedly extracting the maximum element.

#### Algorithm Steps
1. Build a max heap from the array
2. Repeatedly extract maximum element
3. Place it at the end of array
4. Restore heap property
5. Continue until heap is empty

#### Implementation
```java
def heap_sort(arr):
    """Heap Sort implementation"""
    n = len(arr)
    
    # Build max heap
    for i in range(n // 2 - 1, -1, -1):
        heapify(arr, n, i)
    
    # Extract elements from heap
    for i in range(n - 1, 0, -1):
        # Move current root to end
        arr[0], arr[i] = arr[i], arr[0]
        
        # Heapify reduced heap
        heapify(arr, i, 0)
    
    return arr

def heapify(arr, n, i):
    """Heapify subtree rooted at index i"""
    largest = i
    left = 2 * i + 1
    right = 2 * i + 2
    
    # Find largest among root, left child, right child
    if left < n and arr[left] > arr[largest]:
        largest = left
    
    if right < n and arr[right] > arr[largest]:
        largest = right
    
    # If root is not largest, heapify
    if largest != i:
        arr[i], arr[largest] = arr[largest], arr[i]
        heapify(arr, n, largest)

# Example usage
arr = [12, 11, 13, 5, 6, 7]
sorted_arr = heap_sort(arr.copy())
print(f"Original: {arr}")
print(f"Sorted: {sorted_arr}")
```

#### Analysis
- **Time Complexity**: O(n log n) always
- **Space Complexity**: O(1) - in-place
- **Stability**: Not stable
- **Adaptive**: No

#### Pros and Cons
**Pros:**
- Guaranteed O(n log n) performance
- In-place sorting
- No additional memory requirements
- Consistent performance

**Cons:**
- Not stable
- More complex than simple sorts
- Poor cache locality

---

## Specialized Sorting Algorithms

### Counting Sort

#### Concept
Non-comparison based sorting algorithm that counts occurrences of each distinct element.

#### Algorithm Steps
1. Find range of input values
2. Create count array for each possible value
3. Count occurrences of each value
4. Reconstruct sorted array from counts

#### Implementation
```java
def counting_sort(arr):
    """Counting Sort implementation"""
    if not arr:
        return arr
    
    # Find minimum and maximum values
    min_val = min(arr)
    max_val = max(arr)
    range_val = max_val - min_val + 1
    
    # Create count array
    count = [0] * range_val
    
    # Count occurrences
    for num in arr:
        count[num - min_val] += 1
    
    # Reconstruct sorted array
    sorted_arr = []
    for i in range(range_val):
        sorted_arr.extend([i + min_val] * count[i])
    
    return sorted_arr

# Example usage
arr = [4, 2, 2, 8, 3, 3, 1]
sorted_arr = counting_sort(arr)
print(f"Original: {arr}")
print(f"Sorted: {sorted_arr}")
```

#### Analysis
- **Time Complexity**: O(n + k) where k is range of values
- **Space Complexity**: O(k)
- **Stability**: Can be made stable
- **Adaptive**: No

#### Pros and Cons
**Pros:**
- Linear time for small range
- Stable (with modification)
- No comparisons needed

**Cons:**
- Only works for integers
- Space intensive for large ranges
- Not suitable for general sorting

---

### Radix Sort

#### Concept
Non-comparison based sorting that processes digits of numbers from least significant to most significant.

#### Algorithm Steps
1. Find maximum number of digits
2. For each digit position:
   - Use counting sort on that digit
   - Maintain stability
3. Repeat for all digit positions

#### Implementation
```java
def radix_sort(arr):
    """Radix Sort implementation"""
    if not arr:
        return arr
    
    # Find maximum number to know number of digits
    max_num = max(arr)
    exp = 1
    
    # Do counting sort for every digit
    while max_num // exp > 0:
        counting_sort_by_digit(arr, exp)
        exp *= 10
    
    return arr

def counting_sort_by_digit(arr, exp):
    """Counting sort based on digit at exp position"""
    n = len(arr)
    output = [0] * n
    count = [0] * 10
    
    # Count occurrences of digits
    for i in range(n):
        index = (arr[i] // exp) % 10
        count[index] += 1
    
    # Change count to contain actual position
    for i in range(1, 10):
        count[i] += count[i - 1]
    
    # Build output array
    for i in range(n - 1, -1, -1):
        index = (arr[i] // exp) % 10
        output[count[index] - 1] = arr[i]
        count[index] -= 1
    
    # Copy output to original array
    for i in range(n):
        arr[i] = output[i]

# Example usage
arr = [170, 45, 75, 90, 802, 24, 2, 66]
sorted_arr = radix_sort(arr.copy())
print(f"Original: {arr}")
print(f"Sorted: {sorted_arr}")
```

#### Analysis
- **Time Complexity**: O(d × (n + k)) where d is digits, k is base
- **Space Complexity**: O(n + k)
- **Stability**: Stable
- **Adaptive**: No

#### Pros and Cons
**Pros:**
- Linear time for fixed-length integers
- Stable
- No comparisons needed

**Cons:**
- Only works for integers
- Complex implementation
- Space requirements

---

## Algorithm Analysis

### Time Complexity Comparison

| Algorithm | Best | Average | Worst | Space | Stable |
|------------|--------|----------|---------|---------|
| Bubble Sort | O(n) | O(n²) | O(n²) | O(1) | Yes |
| Selection Sort | O(n²) | O(n²) | O(n²) | O(1) | No |
| Insertion Sort | O(n) | O(n²) | O(n²) | O(1) | Yes |
| Merge Sort | O(n log n) | O(n log n) | O(n log n) | O(n) | Yes |
| Quick Sort | O(n log n) | O(n log n) | O(n²) | O(log n) | No |
| Heap Sort | O(n log n) | O(n log n) | O(n log n) | O(1) | No |
| Counting Sort | O(n + k) | O(n + k) | O(n + k) | O(k) | Yes |
| Radix Sort | O(d × (n + k)) | O(d × (n + k)) | O(d × (n + k)) | O(n + k) | Yes |

### When to Use Each Algorithm

#### Bubble Sort
- **Educational purposes**: Teaching sorting concepts
- **Small datasets**: < 100 elements
- **Nearly sorted**: When data is mostly sorted

#### Selection Sort
- **Memory constraints**: When space is limited
- **Small datasets**: Simple implementation needed
- **Predictable performance**: When worst-case must be known

#### Insertion Sort
- **Small datasets**: < 100 elements
- **Nearly sorted**: When data is mostly sorted
- **Online sorting**: When data arrives incrementally
- **Hybrid algorithms**: As sub-routine for quicksort

#### Merge Sort
- **Large datasets**: When O(n log n) is needed
- **Stable sorting**: When equal elements must maintain order
- **External sorting**: When data doesn't fit in memory
- **Linked lists**: When random access is expensive

#### Quick Sort
- **General purpose**: Most common choice
- **Large datasets**: When average performance matters
- **In-place requirement**: When space is limited
- **Cache performance**: When memory locality is important

#### Heap Sort
- **Guaranteed performance**: When worst-case must be O(n log n)
- **In-place requirement**: When space is limited
- **Priority queues**: When heap operations are needed
- **Embedded systems**: When memory is constrained

#### Counting Sort
- **Small range**: When k is much smaller than n
- **Integer sorting**: When only integers are involved
- **Stable requirement**: When stability is needed
- **Linear time**: When O(n) is required

#### Radix Sort
- **Fixed-length integers**: When numbers have limited digits
- **Large datasets**: When O(n) is needed
- **Memory available**: When O(n + k) space is acceptable
- **Integer sorting**: When only integers are involved

---

## Implementation Comparisons

### Java Built-in Sort
```java
# Java's built-in sort (Timsort)
arr = [64, 34, 25, 12, 22, 11, 90]
sorted_arr = sorted(arr)  # Returns new sorted list
arr.sort()  # Sorts in-place

print(f"Built-in sorted: {sorted_arr}")
print(f"In-place sort: {arr}")
```

### Custom Comparison
```java
# Sort with custom key
people = [
    {"name": "Alice", "age": 25},
    {"name": "Bob", "age": 20},
    {"name": "Charlie", "age": 30}
]

# Sort by age
sorted_by_age = sorted(people, key=lambda x: x["age"])
# Sort by name
sorted_by_name = sorted(people, key=lambda x: x["name"])

print(f"Sorted by age: {sorted_by_age}")
print(f"Sorted by name: {sorted_by_name}")
```

### Reverse Sorting
```java
arr = [64, 34, 25, 12, 22, 11, 90]

# Method 1: reverse parameter
sorted_desc = sorted(arr, reverse=True)

# Method 2: negative key
sorted_desc2 = sorted(arr, key=lambda x: -x)

print(f"Descending sort: {sorted_desc}")
```

---

## Practical Applications

### Database Indexing
```java
def create_index(data, key_column):
    """Create sorted index for database table"""
    indexed_data = list(enumerate(data))
    return sorted(indexed_data, key=lambda x: x[1][key_column])

# Example database table
users = [
    {"id": 3, "name": "Charlie", "age": 30},
    {"id": 1, "name": "Alice", "age": 25},
    {"id": 2, "name": "Bob", "age": 20}
]

# Create index by name
name_index = create_index(users, "name")
print(f"Name index: {name_index}")
```

### Leaderboard System
```java
def update_leaderboard(leaderboard, player_name, score):
    """Update and maintain sorted leaderboard"""
    # Add or update player score
    for i, (name, player_score) in enumerate(leaderboard):
        if name == player_name:
            if score > player_score:
                leaderboard[i] = (name, score)
            return leaderboard
    
    # Add new player
    leaderboard.append((name, score))
    
    # Sort by score (descending)
    leaderboard.sort(key=lambda x: x[1], reverse=True)
    
    return leaderboard

# Example usage
leaderboard = [("Alice", 1000), ("Bob", 1500)]
leaderboard = update_leaderboard(leaderboard, "Charlie", 1200)
print(f"Updated leaderboard: {leaderboard}")
```

---

## Optimization Techniques

### Hybrid Algorithms
```java
def hybrid_sort(arr):
    """Hybrid sort using Insertion Sort for small arrays"""
    if len(arr) < 10:
        return insertion_sort(arr)
    else:
        return quick_sort(arr)

# Example usage
arr = [64, 34, 25, 12, 22, 11, 90, 88, 76, 50, 43]
sorted_arr = hybrid_sort(arr)
print(f"Hybrid sort result: {sorted_arr}")
```

### Adaptive Sorting
```java
def adaptive_sort(arr):
    """Adaptive sort that chooses algorithm based on input"""
    n = len(arr)
    
    # Check if array is nearly sorted
    inversions = 0
    for i in range(n - 1):
        if arr[i] > arr[i + 1]:
            inversions += 1
    
    # Choose algorithm based on characteristics
    if inversions < n * 0.1:  # Less than 10% inversions
        return insertion_sort(arr)
    elif n < 100:
        return insertion_sort(arr)
    else:
        return quick_sort(arr)

# Example usage
nearly_sorted = [1, 2, 3, 5, 4, 6, 7, 8]
sorted_arr = adaptive_sort(nearly_sorted)
print(f"Adaptive sort result: {sorted_arr}")
```

---

## Exercises and Practice

### Exercise 1: Implement Missing Algorithms
Implement the following algorithms:
1. **Cocktail Shaker Sort**: Bidirectional bubble sort
2. **Odd-Even Sort**: Parallel bubble sort variant
3. **Gnome Sort**: Simple garden gnome algorithm
4. **Bogo Sort**: Inefficient permutation sort

### Exercise 2: Optimize Existing Algorithms
1. Add cutoff to quicksort for small arrays
2. Implement three-way quicksort partition
3. Add stability to heap sort
4. Optimize merge sort for already sorted arrays

### Exercise 3: Real-world Applications
1. Sort student records by multiple criteria
2. Implement external sorting for large files
3. Create a priority queue using heap sort
4. Sort geometric objects by distance

---

## Summary

Sorting algorithms are fundamental to computer science and programming. Understanding their trade-offs helps in choosing the right algorithm for specific situations.

### Key Takeaways
1. **No perfect algorithm**: Each has strengths and weaknesses
2. **Context matters**: Consider data size, type, and constraints
3. **Built-in is often best**: Java's Timsort is highly optimized
4. **Understanding is crucial**: Helps in problem-solving and interviews

### Next Steps
- Practice implementing these algorithms
- Analyze performance on different datasets
- Learn about advanced topics like external sorting
- Study Java's built-in sorting implementation

---

*Last Updated: March 2026*  
*Algorithms Covered: 8 major sorting algorithms*  
*Difficulty: Beginner to Advanced*
