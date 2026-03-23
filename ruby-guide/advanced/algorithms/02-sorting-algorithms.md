# Sorting Algorithms in Ruby
# Comprehensive guide to sorting algorithms and their implementations

## 🎯 Overview

Sorting is a fundamental operation in computer science. This guide covers various sorting algorithms, their implementations in Ruby, and their performance characteristics.

## 🔄 Basic Sorting Algorithms

### 1. Bubble Sort

Simple but inefficient sorting algorithm:

```ruby
class BubbleSort
  def self.sort(array)
    n = array.length
    sorted_array = array.dup
    
    (n - 1).times do |i|
      (n - i - 1).times do |j|
        if sorted_array[j] > sorted_array[j + 1]
          sorted_array[j], sorted_array[j + 1] = sorted_array[j + 1], sorted_array[j]
        end
      end
    end
    
    sorted_array
  end
  
  def self.optimized_sort(array)
    n = array.length
    sorted_array = array.dup
    
    loop do
      swapped = false
      
      (n - 1).times do |i|
        if sorted_array[i] > sorted_array[i + 1]
          sorted_array[i], sorted_array[i + 1] = sorted_array[i + 1], sorted_array[i]
          swapped = true
        end
      end
      
      break unless swapped
    end
    
    sorted_array
  end
  
  def self.demonstrate_bubble_sort
    puts "Bubble Sort Demonstration:"
    puts "=" * 50
    
    test_arrays = [
      [64, 34, 25, 12, 22, 11, 90],
      [5, 1, 4, 2, 8],
      [1, 2, 3, 4, 5],
      [5, 4, 3, 2, 1],
      [3, 1, 4, 1, 5, 9, 2, 6, 5]
    ]
    
    test_arrays.each do |array|
      puts "Original: #{array}"
      puts "Sorted:   #{sort(array)}"
      puts
    end
  end
end
```

### 2. Selection Sort

Find minimum and place at beginning:

```ruby
class SelectionSort
  def self.sort(array)
    n = array.length
    sorted_array = array.dup
    
    (n - 1).times do |i|
      min_index = i
      
      (i + 1...n).each do |j|
        min_index = j if sorted_array[j] < sorted_array[min_index]
      end
      
      sorted_array[i], sorted_array[min_index] = sorted_array[min_index], sorted_array[i]
    end
    
    sorted_array
  end
  
  def self.demonstrate_selection_sort
    puts "Selection Sort Demonstration:"
    puts "=" * 50
    
    test_arrays = [
      [64, 34, 25, 12, 22, 11, 90],
      [5, 1, 4, 2, 8],
      [1, 2, 3, 4, 5],
      [5, 4, 3, 2, 1],
      [3, 1, 4, 1, 5, 9, 2, 6, 5]
    ]
    
    test_arrays.each do |array|
      puts "Original: #{array}"
      puts "Sorted:   #{sort(array)}"
      puts
    end
  end
end
```

### 3. Insertion Sort

Build sorted array one element at a time:

```ruby
class InsertionSort
  def self.sort(array)
    sorted_array = array.dup
    
    (1...sorted_array.length).each do |i|
      key = sorted_array[i]
      j = i - 1
      
      while j >= 0 && sorted_array[j] > key
        sorted_array[j + 1] = sorted_array[j]
        j -= 1
      end
      
      sorted_array[j + 1] = key
    end
    
    sorted_array
  end
  
  def self.demonstrate_insertion_sort
    puts "Insertion Sort Demonstration:"
    puts "=" * 50
    
    test_arrays = [
      [64, 34, 25, 12, 22, 11, 90],
      [5, 1, 4, 2, 8],
      [1, 2, 3, 4, 5],
      [5, 4, 3, 2, 1],
      [3, 1, 4, 1, 5, 9, 2, 6, 5]
    ]
    
    test_arrays.each do |array|
      puts "Original: #{array}"
      puts "Sorted:   #{sort(array)}"
      puts
    end
  end
end
```

## 🚀 Advanced Sorting Algorithms

### 4. Merge Sort

Divide and conquer sorting algorithm:

```ruby
class MergeSort
  def self.sort(array)
    return array if array.length <= 1
    
    mid = array.length / 2
    left = sort(array[0...mid])
    right = sort(array[mid..-1])
    
    merge(left, right)
  end
  
  def self.merge(left, right)
    result = []
    i = j = 0
    
    while i < left.length && j < right.length
      if left[i] <= right[j]
        result << left[i]
        i += 1
      else
        result << right[j]
        j += 1
      end
    end
    
    result.concat(left[i..-1]).concat(right[j..-1])
  end
  
  def self.demonstrate_merge_sort
    puts "Merge Sort Demonstration:"
    puts "=" * 50
    
    test_arrays = [
      [64, 34, 25, 12, 22, 11, 90],
      [5, 1, 4, 2, 8],
      [1, 2, 3, 4, 5],
      [5, 4, 3, 2, 1],
      [3, 1, 4, 1, 5, 9, 2, 6, 5]
    ]
    
    test_arrays.each do |array|
      puts "Original: #{array}"
      puts "Sorted:   #{sort(array)}"
      puts
    end
  end
end
```

### 5. Quick Sort

Efficient divide and conquer algorithm:

```ruby
class QuickSort
  def self.sort(array)
    return array if array.length <= 1
    
    pivot = array[array.length / 2]
    left = array.select { |x| x < pivot }
    middle = array.select { |x| x == pivot }
    right = array.select { |x| x > pivot }
    
    sort(left) + middle + sort(right)
  end
  
  def self.in_place_sort!(array, low = 0, high = array.length - 1)
    return array if low >= high
    
    pivot_index = partition(array, low, high)
    in_place_sort!(array, low, pivot_index - 1)
    in_place_sort!(array, pivot_index + 1, high)
    
    array
  end
  
  def self.partition(array, low, high)
    pivot = array[high]
    i = low - 1
    
    (low...high).each do |j|
      if array[j] <= pivot
        i += 1
        array[i], array[j] = array[j], array[i]
      end
    end
    
    array[i + 1], array[high] = array[high], array[i + 1]
    i + 1
  end
  
  def self.demonstrate_quick_sort
    puts "Quick Sort Demonstration:"
    puts "=" * 50
    
    test_arrays = [
      [64, 34, 25, 12, 22, 11, 90],
      [5, 1, 4, 2, 8],
      [1, 2, 3, 4, 5],
      [5, 4, 3, 2, 1],
      [3, 1, 4, 1, 5, 9, 2, 6, 5]
    ]
    
    test_arrays.each do |array|
      puts "Original: #{array}"
      puts "Sorted:   #{sort(array)}"
      puts
    end
  end
end
```

### 6. Heap Sort

Use binary heap data structure:

```ruby
class HeapSort
  def self.sort(array)
    n = array.length
    sorted_array = array.dup
    
    # Build max heap
    (n / 2 - 1).downto(0) do |i|
      heapify(sorted_array, n, i)
    end
    
    # Extract elements one by one
    (n - 1).downto(1) do |i|
      sorted_array[0], sorted_array[i] = sorted_array[i], sorted_array[0]
      heapify(sorted_array, i, 0)
    end
    
    sorted_array
  end
  
  def self.heapify(array, n, i)
    largest = i
    left = 2 * i + 1
    right = 2 * i + 2
    
    if left < n && array[left] > array[largest]
      largest = left
    end
    
    if right < n && array[right] > array[largest]
      largest = right
    end
    
    if largest != i
      array[i], array[largest] = array[largest], array[i]
      heapify(array, n, largest)
    end
  end
  
  def self.demonstrate_heap_sort
    puts "Heap Sort Demonstration:"
    puts "=" * 50
    
    test_arrays = [
      [64, 34, 25, 12, 22, 11, 90],
      [5, 1, 4, 2, 8],
      [1, 2, 3, 4, 5],
      [5, 4, 3, 2, 1],
      [3, 1, 4, 1, 5, 9, 2, 6, 5]
    ]
    
    test_arrays.each do |array|
      puts "Original: #{array}"
      puts "Sorted:   #{sort(array)}"
      puts
    end
  end
end
```

## 🎨 Specialized Sorting Algorithms

### 7. Counting Sort

Non-comparison based sorting:

```ruby
class CountingSort
  def self.sort(array)
    return array if array.empty?
    
    min_val = array.min
    max_val = array.max
    range = max_val - min_val + 1
    
    count = Array.new(range, 0)
    
    # Count occurrences
    array.each { |num| count[num - min_val] += 1 }
    
    # Build sorted array
    sorted = []
    count.each_with_index do |freq, i|
      sorted.concat([i + min_val] * freq) if freq > 0
    end
    
    sorted
  end
  
  def self.demonstrate_counting_sort
    puts "Counting Sort Demonstration:"
    puts "=" * 50
    
    test_arrays = [
      [4, 2, 2, 8, 3, 3, 1],
      [0, 1, 4, 0, 2, 3, 5, 1],
      [6, 1, 3, 9, 2, 5, 8, 4],
      [1, 1, 1, 1, 1],
      [9, 8, 7, 6, 5, 4, 3, 2, 1]
    ]
    
    test_arrays.each do |array|
      puts "Original: #{array}"
      puts "Sorted:   #{sort(array)}"
      puts
    end
  end
end
```

### 8. Radix Sort

Sort by processing individual digits:

```ruby
class RadixSort
  def self.sort(array)
    return array if array.empty?
    
    max_val = array.max
    exp = 1
    
    while max_val / exp > 0
      counting_sort_by_digit(array, exp)
      exp *= 10
    end
    
    array
  end
  
  def self.counting_sort_by_digit(array, exp)
    n = array.length
    output = Array.new(n)
    count = Array.new(10, 0)
    
    # Count occurrences of digits
    array.each do |num|
      digit = (num / exp) % 10
      count[digit] += 1
    end
    
    # Calculate cumulative count
    (1...10).each { |i| count[i] += count[i - 1] }
    
    # Build output array
    (n - 1).downto(0) do |i|
      num = array[i]
      digit = (num / exp) % 10
      output[count[digit] - 1] = num
      count[digit] -= 1
    end
    
    # Copy back to original array
    n.times { |i| array[i] = output[i] }
  end
  
  def self.demonstrate_radix_sort
    puts "Radix Sort Demonstration:"
    puts "=" * 50
    
    test_arrays = [
      [170, 45, 75, 90, 802, 24, 2, 66],
      [121, 432, 564, 23, 1, 45, 788],
      [3, 1, 4, 1, 5, 9, 2, 6, 5],
      [100, 200, 300, 400, 500],
      [999, 888, 777, 666, 555]
    ]
    
    test_arrays.each do |array|
      puts "Original: #{array}"
      puts "Sorted:   #{sort(array.dup)}"
      puts
    end
  end
end
```

## 📊 Performance Comparison

### Algorithm Performance Analysis

```ruby
class SortingPerformance
  def self.compare_algorithms
    puts "Sorting Algorithm Performance Comparison:"
    puts "=" * 60
    
    algorithms = {
      'Bubble Sort' => ->(arr) { BubbleSort.sort(arr) },
      'Selection Sort' => ->(arr) { SelectionSort.sort(arr) },
      'Insertion Sort' => ->(arr) { InsertionSort.sort(arr) },
      'Merge Sort' => ->(arr) { MergeSort.sort(arr) },
      'Quick Sort' => ->(arr) { QuickSort.sort(arr) },
      'Heap Sort' => ->(arr) { HeapSort.sort(arr) },
      'Counting Sort' => ->(arr) { CountingSort.sort(arr) },
      'Ruby Sort' => ->(arr) { arr.sort }
    }
    
    # Test with different array sizes
    [100, 1000, 2000].each do |size|
      puts "\nArray size: #{size}"
      puts "-" * 40
      
      test_array = (1..size).to_a.shuffle
      
      algorithms.each do |name, algorithm|
        next if name == 'Counting Sort' && size > 1000 # Skip for large arrays
        
        start_time = Time.now
        algorithm.call(test_array.dup)
        end_time = Time.now
        
        duration = (end_time - start_time) * 1000 # Convert to milliseconds
        puts "#{name.ljust(15)}: #{duration.round(2)}ms"
      end
    end
  end
  
  def self.time_complexity_analysis
    puts "\nTime Complexity Analysis:"
    puts "=" * 50
    
    complexities = {
      'Bubble Sort' => { best: 'O(n)', average: 'O(n²)', worst: 'O(n²)', space: 'O(1)' },
      'Selection Sort' => { best: 'O(n²)', average: 'O(n²)', worst: 'O(n²)', space: 'O(1)' },
      'Insertion Sort' => { best: 'O(n)', average: 'O(n²)', worst: 'O(n²)', space: 'O(1)' },
      'Merge Sort' => { best: 'O(n log n)', average: 'O(n log n)', worst: 'O(n log n)', space: 'O(n)' },
      'Quick Sort' => { best: 'O(n log n)', average: 'O(n log n)', worst: 'O(n²)', space: 'O(log n)' },
      'Heap Sort' => { best: 'O(n log n)', average: 'O(n log n)', worst: 'O(n log n)', space: 'O(1)' },
      'Counting Sort' => { best: 'O(n+k)', average: 'O(n+k)', worst: 'O(n+k)', space: 'O(k)' },
      'Radix Sort' => { best: 'O(nk)', average: 'O(nk)', worst: 'O(nk)', space: 'O(n+k)' }
    }
    
    complexities.each do |algorithm, complexity|
      puts "#{algorithm}:"
      puts "  Best:     #{complexity[:best]}"
      puts "  Average:  #{complexity[:average]}"
      puts "  Worst:    #{complexity[:worst]}"
      puts "  Space:    #{complexity[:space]}"
      puts
    end
  end
end
```

## 🎯 Practical Applications

### Real-World Sorting Examples

```ruby
class PracticalSorting
  def self.sort_students_by_grade
    puts "Sorting Students by Grade:"
    puts "=" * 40
    
    students = [
      { name: 'Alice', grade: 85 },
      { name: 'Bob', grade: 92 },
      { name: 'Charlie', grade: 78 },
      { name: 'Diana', grade: 95 },
      { name: 'Eve', grade: 88 }
    ]
    
    # Sort by grade using Ruby's sort
    sorted_students = students.sort_by { |student| -student[:grade] }
    
    puts "Students sorted by grade (highest first):"
    sorted_students.each_with_index do |student, index|
      puts "#{index + 1}. #{student[:name]}: #{student[:grade]}"
    end
  end
  
  def self.sort_products_by_price
    puts "\nSorting Products by Price:"
    puts "=" * 40
    
    products = [
      { name: 'Laptop', price: 999.99 },
      { name: 'Mouse', price: 29.99 },
      { name: 'Keyboard', price: 79.99 },
      { name: 'Monitor', price: 299.99 },
      { name: 'Headphones', price: 149.99 }
    ]
    
    # Sort by price
    sorted_products = products.sort_by { |product| product[:price] }
    
    puts "Products sorted by price (lowest first):"
    sorted_products.each do |product|
      puts "#{product[:name]}: $#{product[:price]}"
    end
  end
  
  def self.sort_words_by_length
    puts "\nSorting Words by Length:"
    puts "=" * 40
    
    words = ['Ruby', 'programming', 'is', 'awesome', 'and', 'fun']
    
    # Sort by length
    sorted_words = words.sort_by(&:length)
    
    puts "Words sorted by length:"
    sorted_words.each { |word| puts "#{word} (#{word.length} chars)" }
  end
  
  def self.multi_criteria_sorting
    puts "\nMulti-Criteria Sorting:"
    puts "=" * 40
    
    employees = [
      { name: 'John', department: 'Engineering', salary: 80000, experience: 3 },
      { name: 'Jane', department: 'Engineering', salary: 90000, experience: 5 },
      { name: 'Bob', department: 'Marketing', salary: 70000, experience: 2 },
      { name: 'Alice', department: 'Marketing', salary: 75000, experience: 4 },
      { name: 'Charlie', department: 'Engineering', salary: 85000, experience: 3 }
    ]
    
    # Sort by department, then by salary (descending), then by experience
    sorted_employees = employees.sort_by do |emp|
      [emp[:department], -emp[:salary], -emp[:experience]]
    end
    
    puts "Employees sorted by department, salary (desc), experience (desc):"
    sorted_employees.each do |emp|
      puts "#{emp[:name]} - #{emp[:department]} - $#{emp[:salary]} - #{emp[:experience]} years"
    end
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic Sorting**: Implement bubble sort from scratch
2. **Selection Sort**: Implement selection sort algorithm
3. **Insertion Sort**: Implement insertion sort algorithm

### Intermediate Exercises

1. **Merge Sort**: Implement merge sort algorithm
2. **Quick Sort**: Implement quick sort algorithm
3. **Performance Testing**: Compare sorting algorithm performance

### Advanced Exercises

1. **Custom Comparator**: Implement custom sorting logic
2. **Stable Sort**: Implement stable sorting algorithm
3. **External Sort**: Implement sorting for large datasets

---

## 🎯 Summary

Sorting algorithms in Ruby provide:

- **Basic Algorithms** - Bubble, Selection, Insertion sort
- **Advanced Algorithms** - Merge, Quick, Heap sort
- **Specialized Algorithms** - Counting, Radix sort
- **Performance Analysis** - Time and space complexity
- **Practical Applications** - Real-world sorting scenarios

Master these algorithms to understand fundamental computer science concepts!
