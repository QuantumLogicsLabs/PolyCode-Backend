# Sorting Algorithms in Ruby
# This file demonstrates various sorting algorithms implemented in Ruby
# with detailed explanations, complexity analysis, and performance comparisons.

module AlgorithmsExamples
  module SortingAlgorithms
    # Bubble Sort - O(n²) time complexity
    # Simple but inefficient sorting algorithm
    class BubbleSort
      def self.sort(array)
        return array if array.length <= 1
        
        sorted_array = array.dup
        n = sorted_array.length
        
        (n - 1).times do |i|
          swapped = false
          
          (n - i - 1).times do |j|
            if sorted_array[j] > sorted_array[j + 1]
              sorted_array[j], sorted_array[j + 1] = sorted_array[j + 1], sorted_array[j]
              swapped = true
            end
          end
          
          break unless swapped
        end
        
        sorted_array
      end

      def self.optimized_sort(array)
        return array if array.length <= 1
        
        sorted_array = array.dup
        n = sorted_array.length
        new_n = n
        
        loop do
          swapped = false
          
          (1...new_n).each do |i|
            if sorted_array[i - 1] > sorted_array[i]
              sorted_array[i - 1], sorted_array[i] = sorted_array[i], sorted_array[i - 1]
              swapped = true
              new_n = i
            end
          end
          
          break unless swapped
        end
        
        sorted_array
      end
    end

    # Selection Sort - O(n²) time complexity
    # Finds the minimum element and places it at the beginning
    class SelectionSort
      def self.sort(array)
        return array if array.length <= 1
        
        sorted_array = array.dup
        n = sorted_array.length
        
        (0...n).each do |i|
          min_index = i
          
          (i + 1...n).each do |j|
            min_index = j if sorted_array[j] < sorted_array[min_index]
          end
          
          sorted_array[i], sorted_array[min_index] = sorted_array[min_index], sorted_array[i]
        end
        
        sorted_array
      end
    end

    # Insertion Sort - O(n²) time complexity
    # Builds the final sorted array one item at a time
    class InsertionSort
      def self.sort(array)
        return array if array.length <= 1
        
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
    end

    # Merge Sort - O(n log n) time complexity
    # Divide and conquer sorting algorithm
    class MergeSort
      def self.sort(array)
        return array if array.length <= 1
        
        mid = array.length / 2
        left = sort(array[0...mid])
        right = sort(array[mid...array.length])
        
        merge(left, right)
      end

      private

      def self.merge(left, right)
        merged = []
        i = 0
        j = 0
        
        while i < left.length && j < right.length
          if left[i] <= right[j]
            merged << left[i]
            i += 1
          else
            merged << right[j]
            j += 1
          end
        end
        
        merged.concat(left[i..-1]).concat(right[j..-1])
      end
    end

    # Quick Sort - O(n log n) average time complexity
    # Efficient divide and conquer sorting algorithm
    class QuickSort
      def self.sort(array)
        return array if array.length <= 1
        
        pivot = array[array.length / 2]
        left = array.select { |x| x < pivot }
        middle = array.select { |x| x == pivot }
        right = array.select { |x| x > pivot }
        
        sort(left) + middle + sort(right)
      end

      def self.in_place_sort(array, low = 0, high = array.length - 1)
        return array if low >= high
        
        pivot_index = partition(array, low, high)
        
        in_place_sort(array, low, pivot_index - 1)
        in_place_sort(array, pivot_index + 1, high)
        
        array
      end

      private

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
    end

    # Heap Sort - O(n log n) time complexity
    # Uses a binary heap data structure
    class HeapSort
      def self.sort(array)
        return array if array.length <= 1
        
        sorted_array = array.dup
        n = sorted_array.length
        
        # Build max heap
        build_max_heap(sorted_array, n)
        
        # Extract elements from heap one by one
        (n - 1).downto(1) do
          sorted_array[0], sorted_array[i] = sorted_array[i], sorted_array[0]
          heapify(sorted_array, i, 0)
        end
        
        sorted_array
      end

      private

      def self.build_max_heap(array, n)
        (n / 2 - 1).downto(0) do |i|
          heapify(array, n, i)
        end
      end

      def self.heapify(array, n, i)
        largest = i
        left = 2 * i + 1
        right = 2 * i + 2
        
        largest = left if left < n && array[left] > array[largest]
        largest = right if right < n && array[right] > array[largest]
        
        if largest != i
          array[i], array[largest] = array[largest], array[i]
          heapify(array, n, largest)
        end
      end
    end

    # Counting Sort - O(n + k) time complexity
    # Non-comparison based sorting algorithm
    class CountingSort
      def self.sort(array)
        return array if array.length <= 1
        
        min_val = array.min
        max_val = array.max
        range = max_val - min_val + 1
        
        # Create counting array
        count = Array.new(range, 0)
        
        # Store count of each element
        array.each do |num|
          count[num - min_val] += 1
        end
        
        # Change count[i] so that it contains actual position
        (1...count.length).each do |i|
          count[i] += count[i - 1]
        end
        
        # Build output array
        output = Array.new(array.length)
        (array.length - 1).downto(0) do |i|
          num = array[i]
          output[count[num - min_val] - 1] = num
          count[num - min_val] -= 1
        end
        
        output
      end
    end

    # Radix Sort - O(nk) time complexity
    # Non-comparison based sorting algorithm for integers
    class RadixSort
      def self.sort(array)
        return array if array.length <= 1
        
        max_val = array.max
        exp = 1
        
        while max_val / exp > 0
          counting_sort_by_digit(array, exp)
          exp *= 10
        end
        
        array
      end

      private

      def self.counting_sort_by_digit(array, exp)
        n = array.length
        output = Array.new(n)
        count = Array.new(10, 0)
        
        # Store count of occurrences
        array.each do |num|
          index = (num / exp) % 10
          count[index] += 1
        end
        
        # Change count[i] so that it contains actual position
        (1...10).each do |i|
          count[i] += count[i - 1]
        end
        
        # Build output array
        (n - 1).downto(0) do |i|
          num = array[i]
          index = (num / exp) % 10
          output[count[index] - 1] = num
          count[index] -= 1
        end
        
        # Copy to original array
        (0...n).each do |i|
          array[i] = output[i]
        end
      end
    end

    # Shell Sort - O(n^1.5) average time complexity
    # Generalization of insertion sort
    class ShellSort
      def self.sort(array)
        return array if array.length <= 1
        
        sorted_array = array.dup
        n = sorted_array.length
        gap = n / 2
        
        while gap > 0
          (gap...n).each do |i|
            temp = sorted_array[i]
            j = i
            
            while j >= gap && sorted_array[j - gap] > temp
              sorted_array[j] = sorted_array[j - gap]
              j -= gap
            end
            
            sorted_array[j] = temp
          end
          
          gap /= 2
        end
        
        sorted_array
      end
    end

    # Performance comparison utility
    class PerformanceComparison
      def self.compare_algorithms(test_array)
        algorithms = {
          'Bubble Sort' => -> { BubbleSort.sort(test_array) },
          'Optimized Bubble Sort' => -> { BubbleSort.optimized_sort(test_array) },
          'Selection Sort' => -> { SelectionSort.sort(test_array) },
          'Insertion Sort' => -> { InsertionSort.sort(test_array) },
          'Merge Sort' => -> { MergeSort.sort(test_array) },
          'Quick Sort' => -> { QuickSort.sort(test_array) },
          'Heap Sort' => -> { HeapSort.sort(test_array) },
          'Shell Sort' => -> { ShellSort.sort(test_array) }
        }
        
        # Add counting sort and radix sort only for integer arrays
        if test_array.all? { |x| x.is_a?(Integer) && x >= 0 }
          algorithms['Counting Sort'] = -> { CountingSort.sort(test_array) }
          algorithms['Radix Sort'] = -> { RadixSort.sort(test_array) }
        end
        
        results = {}
        
        algorithms.each do |name, algorithm|
          start_time = Time.now
          result = algorithm.call
          end_time = Time.now
          
          results[name] = {
            time: end_time - start_time,
            result: result,
            correct: result == test_array.sort
          }
        end
        
        results
      end

      def self.benchmark_algorithms(sizes = [100, 1000, 5000])
        require 'benchmark'
        
        sizes.each do |size|
          test_array = (1..size).to_a.shuffle
          
          puts "\nBenchmark for #{size} elements:"
          puts "-" * 40
          
          algorithms = {
            'Bubble Sort' => -> { BubbleSort.sort(test_array) },
            'Selection Sort' => -> { SelectionSort.sort(test_array) },
            'Insertion Sort' => -> { InsertionSort.sort(test_array) },
            'Merge Sort' => -> { MergeSort.sort(test_array) },
            'Quick Sort' => -> { QuickSort.sort(test_array) },
            'Heap Sort' => -> { HeapSort.sort(test_array) },
            'Shell Sort' => -> { ShellSort.sort(test_array) }
          }
          
          if test_array.all? { |x| x.is_a?(Integer) && x >= 0 }
            algorithms['Counting Sort'] = -> { CountingSort.sort(test_array) }
            algorithms['Radix Sort'] = -> { RadixSort.sort(test_array) }
          end
          
          algorithms.each do |name, algorithm|
            time = Benchmark.realtime { algorithm.call }
            puts "#{name.ljust(20)}: #{time.round(4)}s"
          end
        end
      end
    end

    # Stable sorting algorithms
    class StableSort
      def self.stable_sort(array, algorithm = :merge_sort)
        case algorithm
        when :merge_sort
          MergeSort.sort(array)
        when :insertion_sort
          InsertionSort.sort(array)
        when :bubble_sort
          BubbleSort.sort(array)
        when :counting_sort
          CountingSort.sort(array) if array.all? { |x| x.is_a?(Integer) && x >= 0 }
        else
          raise ArgumentError, "Unknown or unstable algorithm: #{algorithm}"
        end
      end

      def self.is_stable_sort?(algorithm)
        # Test with duplicate elements
        original = [
          { key: 1, value: 'A' },
          { key: 2, value: 'B' },
          { key: 1, value: 'C' },
          { key: 2, value: 'D' }
        ]
        
        sorted = stable_sort(original, algorithm)
        
        # Check if equal keys maintain relative order
        key1_indices = sorted.each_index.select { |item| item[:key] == 1 }
        key2_indices = sorted.each_index.select { |item| item[:key] == 2 }
        
        key1_indices[0] < key1_indices[1] && key2_indices[0] < key2_indices[1]
      end
    end

    # Custom comparator sorting
    class CustomSort
      def self.sort_by_multiple_keys(array, *keys)
        array.sort do |a, b|
          comparison = 0
          
          keys.each do |key|
            comparison = a[key] <=> b[key]
            break unless comparison == 0
          end
          
          comparison
        end
      end

      def self.sort_by_custom_comparator(array, &comparator)
        array.sort { |a, b| comparator.call(a, b) }
      end

      def self.sort_strings_case_insensitive(array)
        array.sort { |a, b| a.downcase <=> b.downcase }
      end

      def self.sort_by_length(array)
        array.sort { |a, b| a.to_s.length <=> b.to_s.length }
      end
    end

    # Visual sorting demonstration
    class VisualSort
      def self.visualize_sort(array, algorithm = :bubble_sort)
        steps = []
        current_array = array.dup
        
        case algorithm
        when :bubble_sort
          visualize_bubble_sort(current_array, steps)
        when :selection_sort
          visualize_selection_sort(current_array, steps)
        when :insertion_sort
          visualize_insertion_sort(current_array, steps)
        else
          raise ArgumentError, "Unsupported algorithm: #{algorithm}"
        end
        
        steps
      end

      private

      def self.visualize_bubble_sort(array, steps)
        n = array.length
        steps << { array: array.dup, description: "Initial array" }
        
        (n - 1).times do |i|
          swapped = false
          
          (n - i - 1).times do |j|
            if array[j] > array[j + 1]
              array[j], array[j + 1] = array[j + 1], array[j]
              swapped = true
              steps << { 
                array: array.dup, 
                description: "Swapped elements at positions #{j} and #{j + 1}",
                highlighted: [j, j + 1]
              }
            end
          end
          
          break unless swapped
          steps << { 
            array: array.dup, 
            description: "Pass #{i + 1} completed"
          }
        end
        
        steps << { array: array.dup, description: "Sorting completed" }
      end

      def self.visualize_selection_sort(array, steps)
        n = array.length
        steps << { array: array.dup, description: "Initial array" }
        
        (0...n).each do |i|
          min_index = i
          
          (i + 1...n).each do |j|
            if array[j] < array[min_index]
              min_index = j
            end
          end
          
          if min_index != i
            array[i], array[min_index] = array[min_index], array[i]
            steps << { 
              array: array.dup, 
              description: "Swapped minimum element from position #{min_index} to position #{i}",
              highlighted: [i, min_index]
            }
          end
        end
        
        steps << { array: array.dup, description: "Sorting completed" }
      end

      def self.visualize_insertion_sort(array, steps)
        steps << { array: array.dup, description: "Initial array" }
        
        (1...array.length).each do |i|
          key = array[i]
          j = i - 1
          
          steps << { 
            array: array.dup, 
            description: "Inserting element #{key} at position #{i}",
            highlighted: [i]
          }
          
          while j >= 0 && array[j] > key
            array[j + 1] = array[j]
            j -= 1
            
            steps << { 
              array: array.dup, 
              description: "Moved element from position #{j} to #{j + 1}",
              highlighted: [j, j + 1]
            }
          end
          
          array[j + 1] = key
          steps << { 
            array: array.dup, 
            description: "Placed element #{key} at position #{j + 1}",
            highlighted: [j + 1]
          }
        end
        
        steps << { array: array.dup, description: "Sorting completed" }
      end
    end
  end
end

# Usage examples and demonstrations
if __FILE__ == $0
  # Test data
  test_arrays = [
    [64, 34, 25, 12, 22, 11, 90],
    [5, 2, 4, 6, 1, 3],
    [1],
    [],
    [3, 3, 3, 3],
    (1..20).to_a.shuffle
  ]

  # Test each sorting algorithm
  puts "Testing Sorting Algorithms"
  puts "=" * 50

  test_arrays.each_with_index do |test_array, index|
    puts "\nTest Array #{index + 1}: #{test_array}"
    puts "-" * 30
    
    # Test basic algorithms
    bubble_result = AlgorithmsExamples::SortingAlgorithms::BubbleSort.sort(test_array)
    selection_result = AlgorithmsExamples::SortingAlgorithms::SelectionSort.sort(test_array)
    insertion_result = AlgorithmsExamples::SortingAlgorithms::InsertionSort.sort(test_array)
    merge_result = AlgorithmsExamples::SortingAlgorithms::MergeSort.sort(test_array)
    quick_result = AlgorithmsExamples::SortingAlgorithms::QuickSort.sort(test_array)
    
    puts "Bubble Sort: #{bubble_result}"
    puts "Selection Sort: #{selection_result}"
    puts "Insertion Sort: #{insertion_result}"
    puts "Merge Sort: #{merge_result}"
    puts "Quick Sort: #{quick_result}"
    
    # Verify all results are correct
    expected = test_array.sort
    all_correct = [bubble_result, selection_result, insertion_result, merge_result, quick_result].all? { |result| result == expected }
    
    puts "All algorithms correct: #{all_correct ? '✅' : '❌'}"
  end

  # Performance comparison
  puts "\nPerformance Comparison"
  puts "=" * 50

  comparison_results = AlgorithmsExamples::SortingAlgorithms::PerformanceComparison.compare_algorithms((1..100).to_a.shuffle)
  
  puts "Algorithm Performance (100 elements):"
  comparison_results.each do |name, result|
    status = result[:correct] ? '✅' : '❌'
    puts "#{name.ljust(25)}: #{result[:time].round(4)}s #{status}"
  end

  # Visual sorting demonstration
  puts "\nVisual Sorting Demonstration"
  puts "=" * 50

  visual_steps = AlgorithmsExamples::SortingAlgorithms::VisualSort.visualize_sort(
    [5, 3, 8, 4, 2], 
    :bubble_sort
  )
  
  visual_steps.each_with_index do |step, index|
    puts "\nStep #{index + 1}: #{step[:description]}"
    puts "Array: #{step[:array]}"
    if step[:highlighted]
      puts "Highlighted positions: #{step[:highlighted]}"
    end
  end

  # Stability test
  puts "\nStability Test"
  puts "=" * 50

  stable_algorithms = [:merge_sort, :insertion_sort, :bubble_sort]
  
  stable_algorithms.each do |algorithm|
    is_stable = AlgorithmsExamples::SortingAlgorithms::StableSort.is_stable_sort?(algorithm)
    puts "#{algorithm.to_s.gsub('_', ' ').capitalize}: #{is_stable ? '✅ Stable' : '❌ Unstable'}"
  end

  # Custom sorting examples
  puts "\nCustom Sorting Examples"
  puts "=" * 50

  # Sort by multiple keys
  people = [
    { name: 'Alice', age: 25, score: 85 },
    { name: 'Bob', age: 30, score: 90 },
    { name: 'Charlie', age: 25, score: 95 },
    { name: 'Diana', age: 30, score: 80 }
  ]

  sorted_people = AlgorithmsExamples::SortingAlgorithms::CustomSort.sort_by_multiple_keys(people, :age, :score)
  puts "Sorted by age, then score:"
  sorted_people.each { |person| puts "  #{person[:name]} (#{person[:age]}, #{person[:score]})" }

  # Case-insensitive string sorting
  strings = ['apple', 'Banana', 'cherry', 'Date', 'elderberry']
  sorted_strings = AlgorithmsExamples::SortingAlgorithms::CustomSort.sort_strings_case_insensitive(strings)
  puts "\nCase-insensitive string sort:"
  puts "  #{sorted_strings.join(', ')}"

  # Sort by length
  mixed = [1, 22, 333, 44, 555, 6]
  sorted_by_length = AlgorithmsExamples::SortingAlgorithms::CustomSort.sort_by_length(mixed)
  puts "\nSorted by length:"
  puts "  #{sorted_by_length.join(', ')}"
end
