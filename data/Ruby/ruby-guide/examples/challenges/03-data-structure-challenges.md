# Data Structure Challenges in Ruby

## Overview

Data structure challenges help you understand how to organize and manipulate data efficiently. This guide covers various data structure implementations and problem-solving techniques in Ruby, from basic structures to advanced algorithms.

## Linked Lists

### Singly Linked List
```ruby
class Node
  attr_accessor :value, :next_node

  def initialize(value)
    @value = value
    @next_node = nil
  end
end

class SinglyLinkedList
  def initialize
    @head = nil
    @size = 0
  end

  def append(value)
    new_node = Node.new(value)
    
    if @head.nil?
      @head = new_node
    else
      current = @head
      current = current.next_node while current.next_node
      current.next_node = new_node
    end
    
    @size += 1
  end

  def prepend(value)
    new_node = Node.new(value)
    new_node.next_node = @head
    @head = new_node
    @size += 1
  end

  def delete(value)
    return false if @head.nil?
    
    if @head.value == value
      @head = @head.next_node
      @size -= 1
      return true
    end
    
    current = @head
    while current.next_node
      if current.next_node.value == value
        current.next_node = current.next_node.next_node
        @size -= 1
        return true
      end
      current = current.next_node
    end
    
    false
  end

  def find(value)
    current = @head
    index = 0
    
    while current
      return index if current.value == value
      current = current.next_node
      index += 1
    end
    
    -1
  end

  def to_array
    result = []
    current = @head
    
    while current
      result << current.value
      current = current.next_node
    end
    
    result
  end

  def reverse
    prev = nil
    current = @head
    
    while current
      next_node = current.next_node
      current.next_node = prev
      prev = current
      current = next_node
    end
    
    @head = prev
  end

  def size
    @size
  end

  def empty?
    @size == 0
  end
end

# Usage example
list = SinglyLinkedList.new
list.append(1)
list.append(2)
list.append(3)
list.prepend(0)

puts "List: #{list.to_array}"
puts "Size: #{list.size}"
puts "Find 2: #{list.find(2)}"
puts "Delete 2: #{list.delete(2)}"
puts "List after delete: #{list.to_array}"

list.reverse
puts "Reversed list: #{list.to_array}"
```

### Doubly Linked List
```ruby
class DoublyNode
  attr_accessor :value, :prev, :next_node

  def initialize(value)
    @value = value
    @prev = nil
    @next_node = nil
  end
end

class DoublyLinkedList
  def initialize
    @head = nil
    @tail = nil
    @size = 0
  end

  def append(value)
    new_node = DoublyNode.new(value)
    
    if @head.nil?
      @head = @tail = new_node
    else
      @tail.next_node = new_node
      new_node.prev = @tail
      @tail = new_node
    end
    
    @size += 1
  end

  def prepend(value)
    new_node = DoublyNode.new(value)
    
    if @head.nil?
      @head = @tail = new_node
    else
      @head.prev = new_node
      new_node.next_node = @head
      @head = new_node
    end
    
    @size += 1
  end

  def delete(value)
    current = @head
    
    while current
      if current.value == value
        if current.prev
          current.prev.next_node = current.next_node
        else
          @head = current.next_node
        end
        
        if current.next_node
          current.next_node.prev = current.prev
        else
          @tail = current.prev
        end
        
        @size -= 1
        return true
      end
      
      current = current.next_node
    end
    
    false
  end

  def find_from_head(value)
    index = 0
    current = @head
    
    while current
      return index if current.value == value
      current = current.next_node
      index += 1
    end
    
    -1
  end

  def find_from_tail(value)
    index = @size - 1
    current = @tail
    
    while current
      return index if current.value == value
      current = current.prev
      index -= 1
    end
    
    -1
  end

  def to_array
    result = []
    current = @head
    
    while current
      result << current.value
      current = current.next_node
    end
    
    result
  end

  def to_array_reverse
    result = []
    current = @tail
    
    while current
      result << current.value
      current = current.prev
    end
    
    result
  end

  def size
    @size
  end
end

# Usage example
dll = DoublyLinkedList.new
dll.append(1)
dll.append(2)
dll.append(3)
dll.prepend(0)

puts "Doubly linked list: #{dll.to_array}"
puts "From tail: #{dll.to_array_reverse}"
puts "Find from tail: #{dll.find_from_tail(2)}"
```

## Trees

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

  def breadth_first_traversal
    return [] unless @root
    
    result = []
    queue = [@root]
    
    while queue.any?
      node = queue.shift
      result << node.value
      
      queue << node.left if node.left
      queue << node.right if node.right
    end
    
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

  def height
    calculate_height(@root)
  end

  def balanced?
    balanced_recursive(@root)
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
    return node if node.value == value
    
    if value < node.value
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
      # Node with only one child or no child
      return node.right unless node.left
      return node.left unless node.right
      
      # Node with two children: get inorder successor
      min_larger_node = find_min(node.right)
      node.value = min_larger_node.value
      node.right = delete_recursive(node.right, min_larger_node.value)
    end
    
    node
  end

  def find_min(node)
    current = node
    current = current.left while current.left
    current
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

  def calculate_height(node)
    return -1 unless node
    
    left_height = calculate_height(node.left)
    right_height = calculate_height(node.right)
    
    [left_height, right_height].max + 1
  end

  def balanced_recursive(node)
    return true unless node
    
    left_balanced = balanced_recursive(node.left)
    right_balanced = balanced_recursive(node.right)
    
    height_diff = (calculate_height(node.left) - calculate_height(node.right)).abs
    
    left_balanced && right_balanced && height_diff <= 1
  end
end

# Usage example
bst = BinarySearchTree.new
[50, 30, 70, 20, 40, 60, 80].each { |val| bst.insert(val) }

puts "In-order: #{bst.inorder_traversal}"
puts "Pre-order: #{bst.preorder_traversal}"
puts "Post-order: #{bst.postorder_traversal}"
puts "Breadth-first: #{bst.breadth_first_traversal}"
puts "Min value: #{bst.min_value}"
puts "Max value: #{bst.max_value}"
puts "Height: #{bst.height}"
puts "Balanced: #{bst.balanced?}"

bst.delete(70)
puts "After deleting 70: #{bst.inorder_traversal}"
```

### Trie (Prefix Tree)
```ruby
class TrieNode
  attr_accessor :children, :is_end_of_word

  def initialize
    @children = {}
    @is_end_of_word = false
  end
end

class Trie
  def initialize
    @root = TrieNode.new
  end

  def insert(word)
    node = @root
    
    word.each_char do |char|
      node = node.children[char] ||= TrieNode.new
    end
    
    node.is_end_of_word = true
  end

  def search(word)
    node = @root
    
    word.each_char do |char|
      return false unless node.children[char]
      node = node.children[char]
    end
    
    node.is_end_of_word
  end

  def starts_with(prefix)
    node = @root
    
    prefix.each_char do |char|
      return false unless node.children[char]
      node = node.children[char]
    end
    
    true
  end

  def get_all_words_with_prefix(prefix)
    node = @root
    
    prefix.each_char do |char|
      return [] unless node.children[char]
      node = node.children[char]
    end
    
    words = []
    dfs(node, prefix, words)
    words
  end

  def delete(word)
    delete_recursive(@root, word, 0)
  end

  def count_words
    count_words_recursive(@root)
  end

  def to_array
    words = []
    dfs(@root, '', words)
    words
  end

  private

  def dfs(node, prefix, words)
    if node.is_end_of_word
      words << prefix
    end
    
    node.children.each do |char, child_node|
      dfs(child_node, prefix + char, words)
    end
  end

  def delete_recursive(node, word, index)
    return false if index == word.length
    
    char = word[index]
    child = node.children[char]
    
    return false unless child
    
    should_delete_child = delete_recursive(child, word, index + 1)
    
    if should_delete_child
      node.children.delete(char)
      return true unless node.is_end_of_word || node.children.any?
    end
    
    false
  end

  def count_words_recursive(node)
    return 0 unless node
    
    count = node.is_end_of_word ? 1 : 0
    
    node.children.values.sum { |child| count_words_recursive(child) }
  end
end

# Usage example
trie = Trie.new
words = ['apple', 'app', 'application', 'apt', 'bat', 'batch']

words.each { |word| trie.insert(word) }

puts "Search 'apple': #{trie.search('apple')}"
puts "Search 'app': #{trie.search('app')}"
puts "Search 'appl': #{trie.search('appl')}"
puts "Starts with 'app': #{trie.starts_with('app')}"
puts "Words with prefix 'app': #{trie.get_all_words_with_prefix('app')}"
puts "All words: #{trie.to_array}"
puts "Word count: #{trie.count_words}"

trie.delete('app')
puts "After deleting 'app': #{trie.to_array}"
```

## Heaps

### Min Heap Implementation
```ruby
class MinHeap
  def initialize
    @heap = []
  end

  def insert(value)
    @heap << value
    bubble_up(@heap.length - 1)
  end

  def extract_min
    return nil if @heap.empty?
    
    min = @heap[0]
    
    if @heap.length == 1
      @heap.pop
    else
      @heap[0] = @heap.pop
      bubble_down(0)
    end
    
    min
  end

  def peek
    @heap[0]
  end

  def size
    @heap.length
  end

  def empty?
    @heap.empty?
  end

  def to_array
    @heap.dup
  end

  # Heap sort
  def self.heap_sort(array)
    heap = MinHeap.new
    array.each { |value| heap.insert(value) }
    
    sorted = []
    until heap.empty?
      sorted << heap.extract_min
    end
    
    sorted
  end

  private

  def bubble_up(index)
    return if index == 0
    
    parent_index = (index - 1) / 2
    
    if @heap[parent_index] > @heap[index]
      @heap[parent_index], @heap[index] = @heap[index], @heap[parent_index]
      bubble_up(parent_index)
    end
  end

  def bubble_down(index)
    left_child = 2 * index + 1
    right_child = 2 * index + 2
    smallest = index
    
    if left_child < @heap.length && @heap[left_child] < @heap[smallest]
      smallest = left_child
    end
    
    if right_child < @heap.length && @heap[right_child] < @heap[smallest]
      smallest = right_child
    end
    
    if smallest != index
      @heap[index], @heap[smallest] = @heap[smallest], @heap[index]
      bubble_down(smallest)
    end
  end
end

# Max Heap (inherits from MinHeap with value inversion)
class MaxHeap < MinHeap
  def initialize
    super
    @invert_values = true
  end

  def insert(value)
    super(-value)
  end

  def extract_max
    -extract_min
  end

  def peek
    -super
  end

  def to_array
    super.map { |val| -val }
  end
end

# Usage example
min_heap = MinHeap.new
[5, 3, 8, 1, 2, 7].each { |val| min_heap.insert(val) }

puts "Min heap: #{min_heap.to_array}"
puts "Extract min: #{min_heap.extract_min}"
puts "After extraction: #{min_heap.to_array}"

max_heap = MaxHeap.new
[5, 3, 8, 1, 2, 7].each { |val| max_heap.insert(val) }

puts "Max heap: #{max_heap.to_array}"
puts "Extract max: #{max_heap.extract_max}"
puts "After extraction: #{max_heap.to_array}"

# Heap sort
unsorted = [3, 1, 4, 1, 5, 9, 2, 6, 5]
puts "Heap sort result: #{MinHeap.heap_sort(unsorted)}"
```

## Graphs

### Adjacency List Graph
```ruby
class Graph
  def initialize(directed = false)
    @vertices = {}
    @directed = directed
  end

  def add_vertex(vertex)
    @vertices[vertex] ||= []
  end

  def add_edge(vertex1, vertex2, weight = 1)
    add_vertex(vertex1)
    add_vertex(vertex2)
    
    @vertices[vertex1] << { neighbor: vertex2, weight: weight }
    @vertices[vertex2] << { neighbor: vertex1, weight: weight } unless @directed
  end

  def remove_edge(vertex1, vertex2)
    return unless @vertices[vertex1] && @vertices[vertex2]
    
    @vertices[vertex1].reject! { |edge| edge[:neighbor] == vertex2 }
    @vertices[vertex2].reject! { |edge| edge[:neighbor] == vertex1 } unless @directed
  end

  def remove_vertex(vertex)
    return unless @vertices[vertex]
    
    # Remove all edges to this vertex
    @vertices.each do |v, edges|
      edges.reject! { |edge| edge[:neighbor] == vertex }
    end
    
    @vertices.delete(vertex)
  end

  def neighbors(vertex)
    return [] unless @vertices[vertex]
    @vertices[vertex].map { |edge| edge[:neighbor] }
  end

  def edge_weight(vertex1, vertex2)
    return nil unless @vertices[vertex1]
    
    edge = @vertices[vertex1].find { |e| e[:neighbor] == vertex2 }
    edge ? edge[:weight] : nil
  end

  def vertices
    @vertices.keys
  end

  def edges
    all_edges = []
    @vertices.each do |vertex, edges|
      edges.each do |edge|
        all_edges << { from: vertex, to: edge[:neighbor], weight: edge[:weight] }
      end
    end
    all_edges
  end

  def bfs(start_vertex)
    visited = Set.new
    queue = [start_vertex]
    result = []
    
    while queue.any?
      vertex = queue.shift
      next if visited.include?(vertex)
      
      visited.add(vertex)
      result << vertex
      
      neighbors(vertex).each { |neighbor| queue << neighbor unless visited.include?(neighbor) }
    end
    
    result
  end

  def dfs(start_vertex)
    visited = Set.new
    result = []
    
    dfs_recursive(start_vertex, visited, result)
    result
  end

  def has_path?(vertex1, vertex2)
    return false unless @vertices[vertex1] && @vertices[vertex2]
    
    bfs(vertex1).include?(vertex2)
  end

  def shortest_path(vertex1, vertex2)
    return [] unless @vertices[vertex1] && @vertices[vertex2]
    
    distances = { vertex1 => 0 }
    previous = {}
    queue = [vertex1]
    
    while queue.any?
      current = queue.shift
      
      break if current == vertex2
      
      neighbors(current).each do |neighbor|
        weight = edge_weight(current, neighbor)
        new_distance = distances[current] + weight
        
        if !distances[neighbor] || new_distance < distances[neighbor]
          distances[neighbor] = new_distance
          previous[neighbor] = current
          queue << neighbor
        end
      end
    end
    
    return [] unless distances[vertex2]
    
    # Reconstruct path
    path = []
    current = vertex2
    
    while current
      path.unshift(current)
      current = previous[current]
    end
    
    path
  end

  def connected_components
    visited = Set.new
    components = []
    
    vertices.each do |vertex|
      next if visited.include?(vertex)
      
      component = bfs(vertex)
      component.each { |v| visited.add(v) }
      components << component
    end
    
    components
  end

  def topological_sort
    return nil unless @directed
    
    in_degree = Hash.new(0)
    vertices.each { |v| in_degree[v] = 0 }
    
    edges.each do |edge|
      in_degree[edge[:to]] += 1
    end
    
    queue = vertices.select { |v| in_degree[v] == 0 }
    result = []
    
    while queue.any?
      vertex = queue.shift
      result << vertex
      
      neighbors(vertex).each do |neighbor|
        in_degree[neighbor] -= 1
        queue << neighbor if in_degree[neighbor] == 0
      end
    end
    
    result.length == vertices.length ? result : nil  # nil if cycle detected
  end

  private

  def dfs_recursive(vertex, visited, result)
    return if visited.include?(vertex)
    
    visited.add(vertex)
    result << vertex
    
    neighbors(vertex).each { |neighbor| dfs_recursive(neighbor, visited, result) }
  end
end

# Usage example
graph = Graph.new(false)
graph.add_edge('A', 'B', 4)
graph.add_edge('A', 'C', 2)
graph.add_edge('B', 'C', 5)
graph.add_edge('B', 'D', 10)
graph.add_edge('C', 'D', 3)
graph.add_edge('D', 'E', 7)

puts "Vertices: #{graph.vertices}"
puts "Edges: #{graph.edges}"
puts "Neighbors of A: #{graph.neighbors('A')}"
puts "BFS from A: #{graph.bfs('A')}"
puts "DFS from A: #{graph.dfs('A')}"
puts "Path from A to E: #{graph.shortest_path('A', 'E')}"
puts "Connected components: #{graph.connected_components.map(&:sort)}"

# Directed graph for topological sort
directed_graph = Graph.new(true)
directed_graph.add_edge('A', 'B')
directed_graph.add_edge('B', 'C')
directed_graph.add_edge('A', 'D')
directed_graph.add_edge('D', 'C')

puts "Topological sort: #{directed_graph.topological_sort}"
```

## Advanced Data Structures

### LRU Cache
```ruby
class LRUCache
  def initialize(capacity)
    @capacity = capacity
    @cache = {}
    @order = []
  end

  def get(key)
    return -1 unless @cache.key?(key)
    
    # Move to end (most recently used)
    @order.delete(key)
    @order << key
    
    @cache[key]
  end

  def put(key, value)
    if @cache.key?(key)
      # Update existing key
      @cache[key] = value
      @order.delete(key)
      @order << key
    else
      # Add new key
      if @order.length >= @capacity
        # Remove least recently used
        lru_key = @order.shift
        @cache.delete(lru_key)
      end
      
      @cache[key] = value
      @order << key
    end
  end

  def size
    @cache.length
  end

  def to_array
    @order.map { |key| [key, @cache[key]] }
  end
end

# Usage example
cache = LRUCache.new(2)

cache.put(1, 1)
cache.put(2, 2)
puts "Get 1: #{cache.get(1)}"      # Returns 1
puts "Cache: #{cache.to_array}"

cache.put(3, 3)  # Evicts key 2
puts "Get 2: #{cache.get(2)}"      # Returns -1 (not found)
puts "Cache: #{cache.to_array}"

cache.put(4, 4)  # Evicts key 1
puts "Get 1: #{cache.get(1)}"      # Returns -1 (not found)
puts "Get 3: #{cache.get(3)}"      # Returns 3
puts "Get 4: #{cache.get(4)}"      # Returns 4
puts "Cache: #{cache.to_array}"
```

### Circular Buffer
```ruby
class CircularBuffer
  def initialize(capacity)
    @capacity = capacity
    @buffer = Array.new(capacity)
    @head = 0
    @tail = 0
    @size = 0
  end

  def enqueue(item)
    return false if full?
    
    @buffer[@tail] = item
    @tail = (@tail + 1) % @capacity
    @size += 1
    true
  end

  def dequeue
    return nil if empty?
    
    item = @buffer[@head]
    @head = (@head + 1) % @capacity
    @size -= 1
    item
  end

  def peek
    return nil if empty?
    @buffer[@head]
  end

  def size
    @size
  end

  def empty?
    @size == 0
  end

  def full?
    @size == @capacity
  end

  def to_array
    result = []
    
    @size.times do |i|
      index = (@head + i) % @capacity
      result << @buffer[index]
    end
    
    result
  end
end

# Usage example
buffer = CircularBuffer.new(5)

puts "Enqueue 1: #{buffer.enqueue(1)}"
puts "Enqueue 2: #{buffer.enqueue(2)}"
puts "Enqueue 3: #{buffer.enqueue(3)}"
puts "Buffer: #{buffer.to_array}"

puts "Dequeue: #{buffer.dequeue}"
puts "Buffer: #{buffer.to_array}"

puts "Peek: #{buffer.peek}"
puts "Buffer: #{buffer.to_array}"

# Fill buffer to capacity
4.upto(5) { |i| buffer.enqueue(i) }
puts "Full buffer: #{buffer.to_array}"
puts "Is full? #{buffer.full?}"

puts "Enqueue 6 (should fail): #{buffer.enqueue(6)}"
puts "Dequeue all:"
buffer.dequeue until buffer.empty?
puts "Is empty? #{buffer.empty?}"
```

## Challenge Problems

### Design a Stack that Supports getMin()
```ruby
class MinStack
  def initialize
    @stack = []
    @min_stack = []
  end

  def push(x)
    @stack.push(x)
    
    if @min_stack.empty? || x <= @min_stack.last
      @min_stack.push(x)
    end
  end

  def pop
    return nil if @stack.empty?
    
    popped = @stack.pop
    
    if popped == @min_stack.last
      @min_stack.pop
    end
    
    popped
  end

  def top
    @stack.last
  end

  def get_min
    @min_stack.last
  end

  def empty?
    @stack.empty?
  end
end

# Usage example
min_stack = MinStack.new
min_stack.push(-2)
min_stack.push(0)
min_stack.push(-3)

puts "Get min: #{min_stack.get_min}"  # Returns -3
min_stack.pop
puts "Get min: #{min_stack.get_min}"  # Returns -2
min_stack.pop
puts "Top: #{min_stack.top}"          # Returns 0
puts "Get min: #{min_stack.get_min}"  # Returns -2
```

### Implement a Queue using Two Stacks
```ruby
class QueueWithStacks
  def initialize
    @stack1 = []
    @stack2 = []
  end

  def enqueue(x)
    @stack1.push(x)
  end

  def dequeue
    return nil if @stack2.empty? && @stack1.empty?
    
    if @stack2.empty?
      while !@stack1.empty?
        @stack2.push(@stack1.pop)
      end
    end
    
    @stack2.pop
  end

  def peek
    return nil if @stack2.empty? && @stack1.empty?
    
    if @stack2.empty?
      while !@stack1.empty?
        @stack2.push(@stack1.pop)
      end
    end
    
    @stack2.last
  end

  def empty?
    @stack1.empty? && @stack2.empty?
  end
end

# Usage example
queue = QueueWithStacks.new
queue.enqueue(1)
queue.enqueue(2)
queue.enqueue(3)

puts "Dequeue: #{queue.dequeue}"  # Returns 1
puts "Peek: #{queue.peek}"        # Returns 2
puts "Dequeue: #{queue.dequeue}"  # Returns 2
puts "Empty? #{queue.empty?}"    # Returns false
```

### Find the Kth Largest Element in an Array
```ruby
class KthLargestFinder
  def self.find_kth_largest(nums, k)
    # Using quickselect algorithm
    quickselect(nums, 0, nums.length - 1, nums.length - k)
  end

  def self.find_kth_largest_heap(nums, k)
    # Using min heap
    heap = []
    
    nums.each do |num|
      if heap.length < k
        heap << num
        heap.sort!
      elsif num > heap.first
        heap.shift
        heap << num
        heap.sort!
      end
    end
    
    heap.first
  end

  private

  def self.quickselect(nums, left, right, k)
    if left == right
      return nums[left]
    end
    
    pivot_index = partition(nums, left, right)
    
    if k == pivot_index
      nums[k]
    elsif k < pivot_index
      quickselect(nums, left, pivot_index - 1, k)
    else
      quickselect(nums, pivot_index + 1, right, k)
    end
  end

  def self.partition(nums, left, right)
    pivot = nums[right]
    i = left
    
    (left...right).each do |j|
      if nums[j] <= pivot
        nums[i], nums[j] = nums[j], nums[i]
        i += 1
      end
    end
    
    nums[i], nums[right] = nums[right], nums[i]
    i
  end
end

# Usage example
nums = [3, 2, 1, 5, 6, 4]
k = 2

puts "#{k}nd largest (quickselect): #{KthLargestFinder.find_kth_largest(nums, k)}"
puts "#{k}nd largest (heap): #{KthLargestFinder.find_kth_largest_heap(nums, k)}"
```

## Performance Testing

### Data Structure Performance Comparison
```ruby
require 'benchmark'

class DataStructurePerformanceTester
  def self.compare_stack_implementations
    operations = 10000
    
    puts "=== Stack Performance Comparison (#{operations} operations) ==="
    
    Benchmark.bm(20) do |x|
      x.report("Array Stack:") do
        stack = []
        operations.times { stack.push(rand(100)) }
        operations.times { stack.pop unless stack.empty? }
      end
      
      x.report("Linked List Stack:") do
        stack = SinglyLinkedList.new
        operations.times { stack.append(rand(100)) }
        operations.times do
          stack.delete(stack.to_array.last) unless stack.empty?
        end
      end
    end
  end

  def self.compare_queue_implementations
    operations = 10000
    
    puts "\n=== Queue Performance Comparison (#{operations} operations) ==="
    
    Benchmark.bm(20) do |x|
      x.report("Array Queue:") do
        queue = []
        operations.times { queue << rand(100) }
        operations.times { queue.shift unless queue.empty? }
      end
      
      x.report("Two Stack Queue:") do
        queue = QueueWithStacks.new
        operations.times { queue.enqueue(rand(100)) }
        operations.times { queue.dequeue unless queue.empty? }
      end
      
      x.report("Circular Buffer:") do
        buffer = CircularBuffer.new(1000)
        operations.times { buffer.enqueue(rand(100)) unless buffer.full? }
        operations.times { buffer.dequeue unless buffer.empty? }
      end
    end
  end

  def self.compare_tree_operations
    tree_sizes = [100, 1000, 5000]
    
    tree_sizes.each do |size|
      puts "\n=== BST Performance (#{size} nodes) ==="
      
      # Build tree
      data = (1..size).to_a.shuffle
      bst = BinarySearchTree.new
      data.each { |val| bst.insert(val) }
      
      Benchmark.bm(15) do |x|
        x.report("Search:") do
          1000.times { bst.search(rand(size)) }
        end
        
        x.report("Insert:") do
          1000.times { bst.insert(rand(size * 2)) }
        end
        
        x.report("Delete:") do
          1000.times { bst.delete(rand(size)) }
        end
      end
    end
  end

  def self.compare_heap_operations
    heap_sizes = [1000, 5000, 10000]
    
    heap_sizes.each do |size|
      puts "\n=== Heap Performance (#{size} elements) ==="
      
      Benchmark.bm(15) do |x|
        x.report("Insert:") do
          heap = MinHeap.new
          size.times { heap.insert(rand(size)) }
        end
        
        x.report("Extract Min:") do
          heap = MinHeap.new
          size.times { heap.insert(rand(size)) }
          size.times { heap.extract_min unless heap.empty? }
        end
        
        x.report("Heap Sort:") do
          data = (1..size).to_a.shuffle
          MinHeap.heap_sort(data)
        end
      end
    end
  end
end

# Run performance tests
if __FILE__ == $0
  DataStructurePerformanceTester.compare_stack_implementations
  DataStructurePerformanceTester.compare_queue_implementations
  DataStructurePerformanceTester.compare_tree_operations
  DataStructurePerformanceTester.compare_heap_operations
end
```

## Best Practices

1. **Choose the Right Structure**: Select data structures based on your specific needs
2. **Time Complexity**: Understand the Big O complexity of operations
3. **Space Efficiency**: Consider memory usage and space-time tradeoffs
4. **Edge Cases**: Handle empty structures and boundary conditions
5. **Testing**: Write comprehensive tests for all operations
6. **Documentation**: Document complexity, use cases, and limitations
7. **Performance**: Profile and optimize critical operations

## Conclusion

Data structure challenges are fundamental to developing efficient algorithms and solving complex problems. By implementing and working with various data structures in Ruby, you'll gain a deeper understanding of computational thinking and problem-solving strategies.

## Further Reading

- [Data Structures and Algorithms in Ruby](https://github.com/TheAlgorithms/Ruby)
- [Introduction to Algorithms](https://mitpress.mit.edu/books/introduction-algorithms-third-edition)
- [Algorithm Design Manual](https://www.algorist.com/)
- [GeeksforGeeks Data Structures](https://www.geeksforgeeks.org/data-structures/)
- [LeetCode Data Structure Problems](https://leetcode.com/tag/data-structure/)
