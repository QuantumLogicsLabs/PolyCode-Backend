# Algorithms and Data Structures in Ruby

## Overview

This guide covers fundamental algorithms and data structures implemented in Ruby, including sorting, searching, graph algorithms, trees, and optimization techniques.

## Data Structures

### Arrays and Dynamic Arrays

```ruby
# Ruby's Array is a dynamic array
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

# Usage
arr = DynamicArray.new
arr.push(1)
arr.push(2)
arr.push(3)
puts "Array: #{arr.to_a}"
puts "Size: #{arr.size}"
puts "Element at index 1: #{arr[1]}"
```

### Linked Lists

```ruby
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
  
  def empty?
    @size == 0
  end
end

# Usage
list = LinkedList.new
list.append(1)
list.append(2)
list.append(3)
list.prepend(0)
puts "List: #{list.to_a}"
puts "Size: #{list.size}"
puts "Find 2: #{list.find(2).value}"
```

### Stack

```ruby
class Stack
  def initialize
    @elements = []
  end
  
  def push(element)
    @elements.push(element)
  end
  
  def pop
    @elements.pop
  end
  
  def peek
    @elements.last
  end
  
  def empty?
    @elements.empty?
  end
  
  def size
    @elements.size
  end
end

# Usage
stack = Stack.new
stack.push(1)
stack.push(2)
stack.push(3)
puts "Stack size: #{stack.size}"
puts "Top element: #{stack.peek}"
puts "Popped: #{stack.pop}"
puts "New top: #{stack.peek}"
```

### Queue

```ruby
class Queue
  def initialize
    @elements = []
  end
  
  def enqueue(element)
    @elements.push(element)
  end
  
  def dequeue
    @elements.shift
  end
  
  def peek
    @elements.first
  end
  
  def empty?
    @elements.empty?
  end
  
  def size
    @elements.size
  end
end

# Usage
queue = Queue.new
queue.enqueue(1)
queue.enqueue(2)
queue.enqueue(3)
puts "Queue size: #{queue.size}"
puts "Front element: #{queue.peek}"
puts "Dequeued: #{queue.dequeue}"
puts "New front: #{queue.peek}"
```

### Hash Table

```ruby
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
  
  def to_h
    result = {}
    keys.each { |key| result[key] = get(key) }
    result
  end
  
  private
  
  def hash(key)
    key.to_s.chars.reduce(0) { |hash, char| hash * 31 + char.ord }
  end
end

# Usage
hash_table = HashTable.new
hash_table.set("name", "John")
hash_table.set("age", 30)
hash_table.set("city", "New York")

puts "Name: #{hash_table.get("name")}"
puts "Age: #{hash_table.get("age")}"
puts "Keys: #{hash_table.keys}"
puts "Values: #{hash_table.values}"
```

### Binary Search Tree

```ruby
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
  
  def preorder_traversal
    result = []
    preorder_recursive(@root, result)
    result
  end
  
  def postorder_traversal
    result = []
    postorder_recursive(@root, result)
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
  
  def preorder_recursive(node, result)
    return unless node
    
    result << node.value
    preorder_recursive(node.left, result)
    preorder_recursive(node.right, result)
  end
  
  def postorder_recursive(node, result)
    return unless node
    
    postorder_recursive(node.left, result)
    postorder_recursive(node.right, result)
    result << node.value
  end
end

# Usage
bst = BinarySearchTree.new
[50, 30, 70, 20, 40, 60, 80].each { |value| bst.insert(value) }

puts "In-order traversal: #{bst.inorder_traversal}"
puts "Pre-order traversal: #{bst.preorder_traversal}"
puts "Post-order traversal: #{bst.postorder_traversal}"
puts "Search 40: #{bst.search(40)&.value}"
puts "Min value: #{bst.min_value}"
puts "Max value: #{bst.max_value}"

bst.delete(30)
puts "After deleting 30: #{bst.inorder_traversal}"
```

## Sorting Algorithms

### Bubble Sort

```ruby
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

# Usage
array = [64, 34, 25, 12, 22, 11, 90]
puts "Bubble sort: #{bubble_sort(array)}"
```

### Selection Sort

```ruby
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

# Usage
array = [64, 34, 25, 12, 22, 11, 90]
puts "Selection sort: #{selection_sort(array)}"
```

### Insertion Sort

```ruby
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

# Usage
array = [64, 34, 25, 12, 22, 11, 90]
puts "Insertion sort: #{insertion_sort(array)}"
```

### Merge Sort

```ruby
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

# Usage
array = [64, 34, 25, 12, 22, 11, 90]
puts "Merge sort: #{merge_sort(array)}"
```

### Quick Sort

```ruby
def quick_sort(array)
  return array if array.length <= 1
  
  pivot = array[array.length / 2]
  left = array.select { |x| x < pivot }
  middle = array.select { |x| x == pivot }
  right = array.select { |x| x > pivot }
  
  quick_sort(left) + middle + quick_sort(right)
end

# Usage
array = [64, 34, 25, 12, 22, 11, 90]
puts "Quick sort: #{quick_sort(array)}"
```

## Searching Algorithms

### Linear Search

```ruby
def linear_search(array, target)
  array.each_with_index do |element, index|
    return index if element == target
  end
  -1
end

# Usage
array = [2, 3, 4, 10, 40]
target = 10
index = linear_search(array, target)
puts "Linear search: Found #{target} at index #{index}"
```

### Binary Search

```ruby
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

# Usage
sorted_array = [2, 3, 4, 10, 40]
target = 10
index = binary_search(sorted_array, target)
puts "Binary search: Found #{target} at index #{index}"
```

## Graph Algorithms

### Graph Representation

```ruby
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
  
  def remove_vertex(vertex)
    @vertices.delete(vertex)
    
    @vertices.each do |v, edges|
      edges.delete(vertex)
    end
  end
  
  def remove_edge(from, to)
    @vertices[from]&.delete(to)
    @vertices[to]&.delete(from) unless @directed
  end
  
  def neighbors(vertex)
    @vertices[vertex] || []
  end
  
  def vertices
    @vertices.keys
  end
  
  def edges
    edges = []
    
    @vertices.each do |from, to_vertices|
      to_vertices.each do |to|
        edges << [from, to]
      end
    end
    
    edges
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
  
  def has_cycle?
    visited = Set.new
    recursion_stack = Set.new
    
    vertices.each do |vertex|
      if has_cycle_util(vertex, visited, recursion_stack)
        return true
      end
    end
    
    false
  end
  
  def topological_sort
    in_degree = Hash.new(0)
    queue = []
    result = []
    
    # Calculate in-degrees
    vertices.each do |vertex|
      in_degree[vertex] = 0
    end
    
    edges.each do |from, to|
      in_degree[to] += 1
    end
    
    # Find vertices with no incoming edges
    vertices.each do |vertex|
      queue << vertex if in_degree[vertex] == 0
    end
    
    while queue.any?
      vertex = queue.shift
      result << vertex
      
      neighbors(vertex).each do |neighbor|
        in_degree[neighbor] -= 1
        queue << neighbor if in_degree[neighbor] == 0
      end
    end
    
    result.length == vertices.length ? result : nil
  end
  
  private
  
  def has_cycle_util(vertex, visited, recursion_stack)
    return true if recursion_stack.include?(vertex)
    return false if visited.include?(vertex)
    
    visited.add(vertex)
    recursion_stack.add(vertex)
    
    neighbors(vertex).each do |neighbor|
      if has_cycle_util(neighbor, visited, recursion_stack)
        return true
      end
    end
    
    recursion_stack.delete(vertex)
    false
  end
end

# Usage
graph = Graph.new(false)
graph.add_edge('A', 'B')
graph.add_edge('A', 'C')
graph.add_edge('B', 'D')
graph.add_edge('C', 'D')
graph.add_edge('D', 'E')

puts "BFS from A: #{graph.bfs('A')}"
puts "DFS from A: #{graph.dfs('A')}"
puts "Shortest path A to E: #{graph.shortest_path('A', 'E')}"
puts "Has cycle: #{graph.has_cycle?}"
```

### Dijkstra's Algorithm

```ruby
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

# Usage
graph = Graph.new(false)
graph.add_edge('A', 'B')
graph.add_edge('A', 'C')
graph.add_edge('B', 'D')
graph.add_edge('C', 'D')
graph.add_edge('D', 'E')

distances, previous = Dijkstra.shortest_path(graph, 'A')
puts "Shortest distances from A: #{distances}"
puts "Path to E: #{Dijkstra.path_to(previous, 'E')}"
```

## Dynamic Programming

### Fibonacci Sequence

```ruby
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

# Usage
puts "Fibonacci recursive (10): #{fibonacci_recursive(10)}"
puts "Fibonacci memoization (10): #{fibonacci_memoization(10)}"
puts "Fibonacci iterative (10): #{fibonacci_iterative(10)}"
```

### Longest Common Subsequence

```ruby
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

# Usage
str1 = "AGGTAB"
str2 = "GXTXAYB"
puts "LCS length: #{longest_common_subsequence(str1, str2)}"
```

### Knapsack Problem

```ruby
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

# Usage
weights = [1, 3, 4, 5]
values = [1, 4, 5, 7]
capacity = 7
puts "Knapsack max value: #{knapsack(weights, values, capacity)}"
```

## Practice Exercises

### Exercise 1: Priority Queue
Implement a priority queue with:
- Insert operation
- Extract max/min operation
- Peek operation
- Priority change operation

### Exercise 2: Trie Data Structure
Build a trie with:
- Insert word
- Search word
- Prefix search
- Delete word

### Exercise 3: Red-Black Tree
Implement a self-balancing binary search tree with:
- Insert operation
- Delete operation
- Search operation
- Balance maintenance

### Exercise 4: Graph Algorithms
Create a comprehensive graph library with:
- Minimum spanning tree (Kruskal's algorithm)
- Shortest path (Floyd-Warshall algorithm)
- Connected components
- Strongly connected components

---

**Ready to explore more advanced Ruby topics? Let's continue! 🚀**
