# Algorithms and Data Structures Examples
# Demonstrating fundamental algorithms and data structures in Ruby

puts "=== DATA STRUCTURES ==="

# Dynamic Array Implementation
class DynamicArray
  def initialize
    @capacity = 4
    @size = 0
    @data = Array.new(@capacity)
  end
  
  def push(element)
    resize if @size >= @capacity
    @data[@size] = element
    @size += 1
  end
  
  def pop
    return nil if @size == 0
    
    element = @data[@size - 1]
    @size -= 1
    element
  end
  
  def [](index)
    return nil if index >= @size || index < -@size
    
    if index < 0
      @data[@size + index]
    else
      @data[index]
    end
  end
  
  def []=(index, value)
    return nil if index >= @size || index < -@size
    
    if index < 0
      @data[@size + index] = value
    else
      @data[index] = value
    end
  end
  
  def size
    @size
  end
  
  def empty?
    @size == 0
  end
  
  def to_a
    @data[0...@size]
  end
  
  private
  
  def resize
    @capacity *= 2
    new_data = Array.new(@capacity)
    (0...@size).each { |i| new_data[i] = @data[i] }
    @data = new_data
  end
end

puts "Dynamic Array Example:"
arr = DynamicArray.new
arr.push(1)
arr.push(2)
arr.push(3)
puts "Array: #{arr.to_a}"
puts "Size: #{arr.size}"
puts "Element at index 1: #{arr[1]}"

# Linked List Implementation
class Node
  attr_accessor :value, :next, :prev
  
  def initialize(value)
    @value = value
    @next = nil
    @prev = nil
  end
end

class LinkedList
  def initialize
    @head = nil
    @tail = nil
    @size = 0
  end
  
  def append(value)
    node = Node.new(value)
    
    if @tail
      @tail.next = node
      node.prev = @tail
      @tail = node
    else
      @head = @tail = node
    end
    
    @size += 1
  end
  
  def prepend(value)
    node = Node.new(value)
    
    if @head
      @head.prev = node
      node.next = @head
      @head = node
    else
      @head = @tail = node
    end
    
    @size += 1
  end
  
  def remove(value)
    current = @head
    
    while current
      if current.value == value
        if current.prev
          current.prev.next = current.next
        else
          @head = current.next
        end
        
        if current.next
          current.next.prev = current.prev
        else
          @tail = current.prev
        end
        
        @size -= 1
        return true
      end
      
      current = current.next
    end
    
    false
  end
  
  def find(value)
    current = @head
    
    while current
      return current if current.value == value
      current = current.next
    end
    
    nil
  end
  
  def to_a
    result = []
    current = @head
    
    while current
      result << current.value
      current = current.next
    end
    
    result
  end
  
  def size
    @size
  end
end

puts "\nLinked List Example:"
list = LinkedList.new
list.append(1)
list.append(2)
list.append(3)
list.prepend(0)
puts "List: #{list.to_a}"
puts "Size: #{list.size}"
puts "Find 2: #{list.find(2).value}"

# Hash Table Implementation
class HashTable
  def initialize(size = 16)
    @size = size
    @buckets = Array.new(size) { [] }
  end
  
  def set(key, value)
    index = hash(key) % @size
    bucket = @buckets[index]
    
    # Check if key already exists
    bucket.each_with_index do |pair, i|
      if pair[0] == key
        bucket[i] = [key, value]
        return
      end
    end
    
    # Add new key-value pair
    bucket << [key, value]
  end
  
  def get(key)
    index = hash(key) % @size
    bucket = @buckets[index]
    
    bucket.each do |pair|
      return pair[1] if pair[0] == key
    end
    
    nil
  end
  
  def delete(key)
    index = hash(key) % @size
    bucket = @buckets[index]
    
    bucket.each_with_index do |pair, i|
      if pair[0] == key
        bucket.delete_at(i)
        return pair[1]
      end
    end
    
    nil
  end
  
  def keys
    @buckets.map { |bucket| bucket.map(&:first) }.flatten
  end
  
  def values
    @buckets.map { |bucket| bucket.map(&:last) }.flatten
  end
  
  private
  
  def hash(key)
    key.to_s.chars.reduce(0) { |hash, char| hash * 31 + char.ord }
  end
end

puts "\nHash Table Example:"
hash_table = HashTable.new
hash_table.set("name", "John")
hash_table.set("age", 30)
hash_table.set("city", "New York")

puts "Name: #{hash_table.get("name")}"
puts "Age: #{hash_table.get("age")}"
puts "Keys: #{hash_table.keys}"
puts "Values: #{hash_table.values}"

# Binary Search Tree Implementation
class TreeNode
  attr_accessor :value, :left, :right
  
  def initialize(value)
    @value = value
    @left = nil
    @right = nil
  end
end

class BinarySearchTree
  def initialize
    @root = nil
  end
  
  def insert(value)
    @root = insert_recursive(@root, value)
  end
  
  def search(value)
    search_recursive(@root, value)
  end
  
  def delete(value)
    @root = delete_recursive(@root, value)
  end
  
  def inorder_traversal
    result = []
    inorder_recursive(@root, result)
    result
  end
  
  def min_value
    return nil unless @root
    
    current = @root
    current = current.left while current.left
    current.value
  end
  
  def max_value
    return nil unless @root
    
    current = @root
    current = current.right while current.right
    current.value
  end
  
  private
  
  def insert_recursive(node, value)
    return TreeNode.new(value) unless node
    
    if value < node.value
      node.left = insert_recursive(node.left, value)
    elsif value > node.value
      node.right = insert_recursive(node.right, value)
    end
    
    node
  end
  
  def search_recursive(node, value)
    return nil unless node
    
    if value == node.value
      node
    elsif value < node.value
      search_recursive(node.left, value)
    else
      search_recursive(node.right, value)
    end
  end
  
  def delete_recursive(node, value)
    return nil unless node
    
    if value < node.value
      node.left = delete_recursive(node.left, value)
    elsif value > node.value
      node.right = delete_recursive(node.right, value)
    else
      # Node to delete found
      if node.left.nil?
        node.right
      elsif node.right.nil?
        node.left
      else
        # Node has two children
        min_right = find_min(node.right)
        node.value = min_right.value
        node.right = delete_recursive(node.right, min_right.value)
      end
    end
  end
  
  def find_min(node)
    return node unless node.left
    find_min(node.left)
  end
  
  def inorder_recursive(node, result)
    return unless node
    
    inorder_recursive(node.left, result)
    result << node.value
    inorder_recursive(node.right, result)
  end
end

puts "\nBinary Search Tree Example:"
bst = BinarySearchTree.new
[50, 30, 70, 20, 40, 60, 80].each { |value| bst.insert(value) }

puts "In-order traversal: #{bst.inorder_traversal}"
puts "Search 40: #{bst.search(40)&.value}"
puts "Min value: #{bst.min_value}"
puts "Max value: #{bst.max_value}"

bst.delete(30)
puts "After deleting 30: #{bst.inorder_traversal}"

puts "\n=== SORTING ALGORITHMS ==="

# Bubble Sort
def bubble_sort(array)
  n = array.length
  sorted = array.dup
  
  (n - 1).times do |i|
    swapped = false
    
    (n - i - 1).times do |j|
      if sorted[j] > sorted[j + 1]
        sorted[j], sorted[j + 1] = sorted[j + 1], sorted[j]
        swapped = true
      end
    end
    
    break unless swapped
  end
  
  sorted
end

# Selection Sort
def selection_sort(array)
  n = array.length
  sorted = array.dup
  
  (n - 1).times do |i|
    min_index = i
    
    (i + 1...n).each do |j|
      min_index = j if sorted[j] < sorted[min_index]
    end
    
    sorted[i], sorted[min_index] = sorted[min_index], sorted[i]
  end
  
  sorted
end

# Insertion Sort
def insertion_sort(array)
  sorted = array.dup
  
  (1...sorted.length).each do |i|
    key = sorted[i]
    j = i - 1
    
    while j >= 0 && sorted[j] > key
      sorted[j + 1] = sorted[j]
      j -= 1
    end
    
    sorted[j + 1] = key
  end
  
  sorted
end

# Merge Sort
def merge_sort(array)
  return array if array.length <= 1
  
  mid = array.length / 2
  left = merge_sort(array[0...mid])
  right = merge_sort(array[mid..-1])
  
  merge(left, right)
end

def merge(left, right)
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
  
  result.concat(left[i..-1]) if i < left.length
  result.concat(right[j..-1]) if j < right.length
  
  result
end

# Quick Sort
def quick_sort(array)
  return array if array.length <= 1
  
  pivot = array[array.length / 2]
  left = array.select { |x| x < pivot }
  middle = array.select { |x| x == pivot }
  right = array.select { |x| x > pivot }
  
  quick_sort(left) + middle + quick_sort(right)
end

puts "Sorting Algorithms Example:"
array = [64, 34, 25, 12, 22, 11, 90]

puts "Original: #{array}"
puts "Bubble sort: #{bubble_sort(array)}"
puts "Selection sort: #{selection_sort(array)}"
puts "Insertion sort: #{insertion_sort(array)}"
puts "Merge sort: #{merge_sort(array)}"
puts "Quick sort: #{quick_sort(array)}"

puts "\n=== SEARCHING ALGORITHMS ==="

# Linear Search
def linear_search(array, target)
  array.each_with_index do |element, index|
    return index if element == target
  end
  -1
end

# Binary Search
def binary_search(sorted_array, target)
  low = 0
  high = sorted_array.length - 1
  
  while low <= high
    mid = (low + high) / 2
    
    if sorted_array[mid] == target
      return mid
    elsif sorted_array[mid] < target
      low = mid + 1
    else
      high = mid - 1
    end
  end
  
  -1
end

puts "Searching Algorithms Example:"
array = [2, 3, 4, 10, 40]
target = 10

index = linear_search(array, target)
puts "Linear search: Found #{target} at index #{index}"

sorted_array = [2, 3, 4, 10, 40]
index = binary_search(sorted_array, target)
puts "Binary search: Found #{target} at index #{index}"

puts "\n=== GRAPH ALGORITHMS ==="

# Graph Implementation
class Graph
  def initialize(directed = false)
    @vertices = {}
    @directed = directed
  end
  
  def add_vertex(vertex)
    @vertices[vertex] = [] unless @vertices[vertex]
  end
  
  def add_edge(from, to)
    add_vertex(from) unless @vertices[from]
    add_vertex(to) unless @vertices[to]
    
    @vertices[from] << to
    @vertices[to] << from unless @directed
  end
  
  def neighbors(vertex)
    @vertices[vertex] || []
  end
  
  def vertices
    @vertices.keys
  end
  
  def bfs(start)
    visited = Set.new
    queue = [start]
    result = []
    
    while queue.any?
      vertex = queue.shift
      
      next if visited.include?(vertex)
      
      visited.add(vertex)
      result << vertex
      
      neighbors(vertex).each do |neighbor|
        queue << neighbor unless visited.include?(neighbor)
      end
    end
    
    result
  end
  
  def dfs(start, visited = Set.new)
    return [] if visited.include?(start)
    
    visited.add(start)
    result = [start]
    
    neighbors(start).each do |neighbor|
      result.concat(dfs(neighbor, visited))
    end
    
    result
  end
  
  def shortest_path(start, target)
    return nil unless @vertices[start] && @vertices[target]
    
    queue = [[start]]
    visited = Set.new([start])
    
    while queue.any?
      path = queue.shift
      current = path.last
      
      return path if current == target
      
      neighbors(current).each do |neighbor|
        unless visited.include?(neighbor)
          visited.add(neighbor)
          queue << path + [neighbor]
        end
      end
    end
    
    nil
  end
end

puts "Graph Algorithms Example:"
graph = Graph.new(false)
graph.add_edge('A', 'B')
graph.add_edge('A', 'C')
graph.add_edge('B', 'D')
graph.add_edge('C', 'D')
graph.add_edge('D', 'E')

puts "BFS from A: #{graph.bfs('A')}"
puts "DFS from A: #{graph.dfs('A')}"
puts "Shortest path A to E: #{graph.shortest_path('A', 'E')}"

# Dijkstra's Algorithm
class Dijkstra
  def self.shortest_path(graph, start)
    distances = Hash.new(Float::INFINITY)
    previous = {}
    unvisited = Set.new(graph.vertices)
    
    graph.vertices.each do |vertex|
      distances[vertex] = Float::INFINITY
    end
    
    distances[start] = 0
    
    while unvisited.any?
      current = unvisited.min_by { |v| distances[v] }
      
      unvisited.delete(current)
      
      graph.neighbors(current).each do |neighbor|
        distance = distances[current] + 1  # Assuming edge weight of 1
        
        if distance < distances[neighbor]
          distances[neighbor] = distance
          previous[neighbor] = current
        end
      end
    end
    
    [distances, previous]
  end
  
  def self.path_to(previous, target)
    path = []
    current = target
    
    while current
      path.unshift(current)
      current = previous[current]
    end
    
    path
  end
end

puts "\nDijkstra's Algorithm Example:"
distances, previous = Dijkstra.shortest_path(graph, 'A')
puts "Shortest distances from A: #{distances}"
puts "Path to E: #{Dijkstra.path_to(previous, 'E')}"

puts "\n=== DYNAMIC PROGRAMMING ==="

# Fibonacci Sequence
def fibonacci_recursive(n)
  return n if n <= 1
  fibonacci_recursive(n - 1) + fibonacci_recursive(n - 2)
end

def fibonacci_memoization(n, memo = {})
  return n if n <= 1
  return memo[n] if memo[n]
  
  memo[n] = fibonacci_memoization(n - 1, memo) + fibonacci_memoization(n - 2, memo)
end

def fibonacci_iterative(n)
  return n if n <= 1
  
  a, b = 0, 1
  (n - 1).times { a, b = b, a + b }
  b
end

puts "Dynamic Programming Example:"
puts "Fibonacci recursive (10): #{fibonacci_recursive(10)}"
puts "Fibonacci memoization (10): #{fibonacci_memoization(10)}"
puts "Fibonacci iterative (10): #{fibonacci_iterative(10)}"

# Longest Common Subsequence
def longest_common_subsequence(str1, str2)
  m, n = str1.length, str2.length
  dp = Array.new(m + 1) { Array.new(n + 1, 0) }
  
  (1..m).each do |i|
    (1..n).each do |j|
      if str1[i - 1] == str2[j - 1]
        dp[i][j] = dp[i - 1][j - 1] + 1
      else
        dp[i][j] = [dp[i - 1][j], dp[i][j - 1]].max
      end
    end
  end
  
  dp[m][n]
end

puts "\nLCS Example:"
str1 = "AGGTAB"
str2 = "GXTXAYB"
puts "LCS length: #{longest_common_subsequence(str1, str2)}"

# Knapsack Problem
def knapsack(weights, values, capacity)
  n = weights.length
  dp = Array.new(n + 1) { Array.new(capacity + 1, 0) }
  
  (1..n).each do |i|
    (0..capacity).each do |w|
      if weights[i - 1] <= w
        dp[i][w] = [dp[i - 1][w], dp[i - 1][w - weights[i - 1]] + values[i - 1]].max
      else
        dp[i][w] = dp[i - 1][w]
      end
    end
  end
  
  dp[n][capacity]
end

puts "\nKnapsack Problem Example:"
weights = [1, 3, 4, 5]
values = [1, 4, 5, 7]
capacity = 7
puts "Knapsack max value: #{knapsack(weights, values, capacity)}"

puts "\n=== ALGORITHM PERFORMANCE ==="

# Algorithm Performance Comparison
def benchmark_sorts
  arrays = {
    small: (1..100).to_a.shuffle,
    medium: (1..1000).to_a.shuffle,
    large: (1..5000).to_a.shuffle
  }
  
  sorts = {
    'Bubble Sort' => method(:bubble_sort),
    'Selection Sort' => method(:selection_sort),
    'Insertion Sort' => method(:insertion_sort),
    'Merge Sort' => method(:merge_sort),
    'Quick Sort' => method(:quick_sort)
  }
  
  puts "Algorithm Performance Benchmark:"
  puts "=" * 50
  
  arrays.each do |size, array|
    puts "\n#{size.to_s.capitalize} array (#{array.length} elements):"
    
    sorts.each do |name, sort_method|
      start_time = Time.now
      sorted = sort_method.call(array)
      end_time = Time.now
      
      duration = end_time - start_time
      puts "  #{name}: #{(duration * 1000).round(3)}ms"
    end
  end
end

# Search Algorithm Performance
def benchmark_searches
  array = (1..10000).to_a
  target = 9999
  
  puts "\nSearch Algorithm Performance:"
  puts "=" * 40
  
  # Linear Search
  start_time = Time.now
  index = linear_search(array, target)
  end_time = Time.now
  linear_time = end_time - start_time
  
  # Binary Search
  start_time = Time.now
  index = binary_search(array, target)
  end_time = Time.now
  binary_time = end_time - start_time
  
  puts "Linear search: #{(linear_time * 1000).round(3)}ms"
  puts "Binary search: #{(binary_time * 1000).round(3)}ms"
  puts "Speedup: #{(linear_time / binary_time).round(2)}x"
end

# Run benchmarks
benchmark_sorts
benchmark_searches

puts "\n=== ALGORITHMS SUMMARY ==="
puts "- Data Structures: Dynamic arrays, linked lists, hash tables, binary search trees"
puts "- Sorting Algorithms: Bubble, selection, insertion, merge, quick sort"
puts "- Searching Algorithms: Linear search, binary search"
puts "- Graph Algorithms: BFS, DFS, shortest path, Dijkstra's algorithm"
puts "- Dynamic Programming: Fibonacci, LCS, knapsack problem"
puts "- Performance: Benchmarking, complexity analysis, optimization"
puts "\nAll examples demonstrate fundamental algorithms and data structures in Ruby!"
