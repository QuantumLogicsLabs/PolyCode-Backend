# Arrays Example

## File Overview
This file demonstrates comprehensive array operations in Java including declaration, initialization, manipulation, searching, sorting, and multi-dimensional arrays.

## Key Concepts Explained

### 1. Array Declaration and Initialization

#### Method 1: Declaration with Initialization
```java
int[] numbers = {1, 2, 3, 4, 5};
String[] fruits = {"Apple", "Banana", "Orange"};
```
- Array initializer syntax
- Size determined by number of elements
- Must be declared and initialized together

#### Method 2: Declaration then Initialization
```java
double[] prices = new double[3];
prices[0] = 10.99;
prices[1] = 5.99;
prices[2] = 2.99;
```
- Specify size with `new`
- Elements initialized to default values
- Individual element assignment

#### Method 3: Dynamic Initialization
```java
int[] squares = new int[5];
for (int i = 0; i < squares.length; i++) {
    squares[i] = i * i;
}
```
- Calculate values programmatically
- Useful for computed sequences

### 2. Array Properties

#### Fixed Size
- Arrays have fixed size once created
- Cannot be resized dynamically
- Size accessed via `.length` property

#### Zero-based Indexing
- First element at index 0
- Last element at index `length - 1`
- Index out of bounds causes runtime exception

#### Type Safety
- All elements must be same type
- Primitive arrays store values directly
- Object arrays store references

### 3. Array Operations

#### Accessing Elements
- `array[index]` syntax
- Valid range: 0 to length-1
- Returns element at specified position

#### Iteration Methods
- **Traditional for loop**: Access by index
- **Enhanced for loop**: Read-only iteration
- **Arrays.toString()**: Convert to string representation

#### Common Operations
- Finding min/max values
- Calculating sum and average
- Linear search for elements
- Sorting algorithms

### 4. Array Sorting

#### Built-in Sorting
```java
Arrays.sort(array); // Quick sort for primitives, dual-pivot quicksort
```
- Efficient O(n log n) average case
- Stable for objects, not for primitives
- Modifies original array

#### Custom Sorting
- Bubble sort implementation shown
- Selection sort, insertion sort alternatives
- Custom comparators for objects

### 5. Multi-dimensional Arrays

#### 2D Arrays
```java
int[][] matrix = {
    {1, 2, 3},
    {4, 5, 6},
    {7, 8, 9}
};
```
- Array of arrays
- Rectangular (regular) structure
- Access with `matrix[row][col]`

#### Jagged Arrays
```java
int[][] jagged = {
    {1, 2},
    {3, 4, 5},
    {6, 7, 8, 9}
};
```
- Each row can have different length
- More flexible memory usage
- Still accessed with row, column indices

### 6. Array Copying

#### Arrays.copyOf()
```java
int[] copy = Arrays.copyOf(original, length);
```
- Creates new array with specified length
- Copies all or truncated elements

#### System.arraycopy()
```java
System.arraycopy(src, srcPos, dest, destPos, length);
```
- Most efficient method
- Native implementation
- Precise control over copy range

#### clone() Method
```java
int[] copy = original.clone();
```
- Creates shallow copy
- Same as Arrays.copyOf(original, original.length)

## Best Practices
1. Use enhanced for-loop when index not needed
2. Check array bounds before accessing
3. Use Arrays.toString() for debugging
4. Prefer built-in sorting over custom implementations
5. Consider ArrayList for dynamic sizing needs
6. Be careful with array copying (reference vs value)

## Common Pitfalls
1. ArrayIndexOutOfBoundsException
2. Confusing length with size()
3. Modifying array when iterating
4. Assuming arrays are dynamic
5. Forgetting to initialize array elements
6. Using wrong array type for data

## Performance Considerations
- Arrays provide O(1) random access
- Linear search is O(n)
- Built-in sort is O(n log n)
- Memory overhead is minimal
- Cache-friendly for sequential access
