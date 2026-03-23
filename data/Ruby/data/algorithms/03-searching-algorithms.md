# Searching Algorithms in Ruby
# Comprehensive guide to searching algorithms and their implementations

## 🎯 Overview

Searching is a fundamental operation for finding specific elements in data structures. This guide covers various searching algorithms, their implementations in Ruby, and their performance characteristics.

## 🔍 Linear Search

### 1. Basic Linear Search

Simple sequential search through data:

```ruby
class LinearSearch
  def self.search(array, target)
    array.each_with_index do |element, index|
      return index if element == target
    end
    -1 # Not found
  end
  
  def self.search_all(array, target)
    indices = []
    array.each_with_index do |element, index|
      indices << index if element == target
    end
    indices
  end
  
  def self.demonstrate_linear_search
    puts "Linear Search Demonstration:"
    puts "=" * 50
    
    test_arrays = [
      [64, 34, 25, 12, 22, 11, 90],
      [5, 1, 4, 2, 8],
      [1, 2, 3, 4, 5],
      [5, 4, 3, 2, 1],
      [3, 1, 4, 1, 5, 9, 2, 6, 5]
    ]
    
    test_arrays.each do |array|
      target = array[array.length / 2]
      index = search(array, target)
      
      puts "Array: #{array}"
      puts "Target: #{target}"
      puts "Index: #{index}"
      puts "Found: #{index >= 0 ? 'Yes' : 'No'}"
      puts
    end
  end
end
```

### 2. Enhanced Linear Search

With additional features:

```ruby
class EnhancedLinearSearch
  def self.search_with_condition(array, condition)
    array.each_with_index do |element, index|
      return index, element if condition.call(element)
    end
    nil
  end
  
  def self.find_min_max(array)
    return nil, nil if array.empty?
    
    min = max = array[0]
    min_index = max_index = 0
    
    array.each_with_index do |element, index|
      if element < min
        min = element
        min_index = index
      elsif element > max
        max = element
        max_index = index
      end
    end
    
    { min: { value: min, index: min_index }, max: { value: max, index: max_index } }
  end
  
  def self.find_second_largest(array)
    return nil if array.length < 2
    
    largest = second_largest = -Float::INFINITY
    
    array.each do |element|
      if element > largest
        second_largest = largest
        largest = element
      elsif element > second_largest && element != largest
        second_largest = element
      end
    end
    
    second_largest == -Float::INFINITY ? nil : second_largest
  end
  
  def self.demonstrate_enhanced_search
    puts "Enhanced Linear Search Demonstration:"
    puts "=" * 50
    
    array = [64, 34, 25, 12, 22, 11, 90, 45, 78, 56]
    
    # Search with condition
    puts "Array: #{array}"
    index, value = search_with_condition(array, ->(x) { x > 50 })
    puts "First element > 50: #{value} at index #{index}"
    
    # Find min and max
    min_max = find_min_max(array)
    puts "Min: #{min_max[:min][:value]} at index #{min_max[:min][:index]}"
    puts "Max: #{min_max[:max][:value]} at index #{min_max[:max][:index]}"
    
    # Find second largest
    second_largest = find_second_largest(array)
    puts "Second largest: #{second_largest}"
  end
end
```

## ⚡ Binary Search

### 3. Binary Search on Sorted Arrays

Efficient search on sorted data:

```ruby
class BinarySearch
  def self.search(array, target)
    low = 0
    high = array.length - 1
    
    while low <= high
      mid = (low + high) / 2
      
      if array[mid] == target
        return mid
      elsif array[mid] < target
        low = mid + 1
      else
        high = mid - 1
      end
    end
    
    -1 # Not found
  end
  
  def self.recursive_search(array, target, low = 0, high = array.length - 1)
    return -1 if low > high
    
    mid = (low + high) / 2
    
    if array[mid] == target
      mid
    elsif array[mid] < target
      recursive_search(array, target, mid + 1, high)
    else
      recursive_search(array, target, low, mid - 1)
    end
  end
  
  def self.find_first_occurrence(array, target)
    low = 0
    high = array.length - 1
    result = -1
    
    while low <= high
      mid = (low + high) / 2
      
      if array[mid] == target
        result = mid
        high = mid - 1 # Search left half
      elsif array[mid] < target
        low = mid + 1
      else
        high = mid - 1
      end
    end
    
    result
  end
  
  def self.find_last_occurrence(array, target)
    low = 0
    high = array.length - 1
    result = -1
    
    while low <= high
      mid = (low + high) / 2
      
      if array[mid] == target
        result = mid
        low = mid + 1 # Search right half
      elsif array[mid] < target
        low = mid + 1
      else
        high = mid - 1
      end
    end
    
    result
  end
  
  def self.demonstrate_binary_search
    puts "Binary Search Demonstration:"
    puts "=" * 50
    
    test_arrays = [
      [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
      [2, 4, 6, 8, 10, 12, 14, 16, 18, 20],
      [1, 3, 5, 7, 9, 11, 13, 15, 17, 19],
      [10, 20, 30, 40, 50, 60, 70, 80, 90, 100]
    ]
    
    test_arrays.each do |array|
      target = array[array.length / 2]
      index = search(array, target)
      
      puts "Array: #{array}"
      puts "Target: #{target}"
      puts "Index: #{index}"
      puts "Found: #{index >= 0 ? 'Yes' : 'No'}"
      puts
    end
  end
end
```

### 4. Binary Search Variants

Different binary search applications:

```ruby
class BinarySearchVariants
  def self.find_closest(array, target)
    return nil if array.empty?
    
    low = 0
    high = array.length - 1
    closest = nil
    min_diff = Float::INFINITY
    
    while low <= high
      mid = (low + high) / 2
      diff = (array[mid] - target).abs
      
      if diff < min_diff
        min_diff = diff
        closest = array[mid]
      end
      
      if array[mid] == target
        return array[mid]
      elsif array[mid] < target
        low = mid + 1
      else
        high = mid - 1
      end
    end
    
    closest
  end
  
  def self.find_floor_ceiling(array, target)
    return { floor: nil, ceiling: nil } if array.empty?
    
    low = 0
    high = array.length - 1
    floor = ceiling = nil
    
    while low <= high
      mid = (low + high) / 2
      
      if array[mid] == target
        return { floor: array[mid], ceiling: array[mid] }
      elsif array[mid] < target
        floor = array[mid]
        low = mid + 1
      else
        ceiling = array[mid]
        high = mid - 1
      end
    end
    
    { floor: floor, ceiling: ceiling }
  end
  
  def self.count_occurrences(array, target)
    first = BinarySearch.find_first_occurrence(array, target)
    return 0 if first == -1
    
    last = BinarySearch.find_last_occurrence(array, target)
    last - first + 1
  end
  
  def self.demonstrate_variants
    puts "Binary Search Variants Demonstration:"
    puts "=" * 50
    
    array = [1, 3, 5, 7, 9, 11, 13, 15, 17, 19]
    
    # Find closest
    closest = find_closest(array, 8)
    puts "Array: #{array}"
    puts "Closest to 8: #{closest}"
    
    # Find floor and ceiling
    floor_ceiling = find_floor_ceiling(array, 8)
    puts "Floor of 8: #{floor_ceiling[:floor]}"
    puts "Ceiling of 8: #{floor_ceiling[:ceiling]}"
    
    # Count occurrences in array with duplicates
    array_with_duplicates = [1, 2, 2, 2, 3, 4, 5, 5, 6]
    count = count_occurrences(array_with_duplicates, 2)
    puts "\nArray with duplicates: #{array_with_duplicates}"
    puts "Occurrences of 2: #{count}"
  end
end
```

## 🌳 Tree Searching

### 5. Binary Search Tree Operations

Search in binary search trees:

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
  def initialize(root = nil)
    @root = root
  end
  
  def insert(value)
    @root = insert_recursive(@root, value)
  end
  
  def search(value)
    search_recursive(@root, value)
  end
  
  def find_min
    return nil unless @root
    
    current = @root
    current = current.left while current.left
    current.value
  end
  
  def find_max
    return nil unless @root
    
    current = @root
    current = current.right while current.right
    current.value
  end
  
  def inorder_traversal
    result = []
    inorder_recursive(@root, result)
    result
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
  
  def inorder_recursive(node, result)
    return unless node
    
    inorder_recursive(node.left, result)
    result << node.value
    inorder_recursive(node.right, result)
  end
  
  def self.demonstrate_bst_search
    puts "Binary Search Tree Demonstration:"
    puts "=" * 50
    
    bst = BinarySearchTree.new
    
    # Insert values
    values = [50, 30, 70, 20, 40, 60, 80]
    values.each { |value| bst.insert(value) }
    
    puts "BST inorder traversal: #{bst.inorder_traversal}"
    puts "Min value: #{bst.find_min}"
    puts "Max value: #{bst.find_max}"
    
    # Search for values
    search_values = [40, 25, 80, 10]
    search_values.each do |value|
      result = bst.search(value)
      puts "Search #{value}: #{result ? 'Found' : 'Not found'}"
    end
  end
end
```

### 6. Tree Traversal Searching

Different tree traversal methods:

```ruby
class TreeTraversalSearch
  def self.depth_first_search(root, target)
    return nil unless root
    
    # Check current node
    return root if root.value == target
    
    # Search left subtree
    left_result = depth_first_search(root.left, target)
    return left_result if left_result
    
    # Search right subtree
    depth_first_search(root.right, target)
  end
  
  def self.breadth_first_search(root, target)
    return nil unless root
    
    queue = [root]
    
    while queue.any?
      current = queue.shift
      
      return current if current.value == target
      
      queue << current.left if current.left
      queue << current.right if current.right
    end
    
    nil
  end
  
  def self.find_path(root, target)
    return [] unless root
    
    path = []
    
    if find_path_recursive(root, target, path)
      path
    else
      []
    end
  end
  
  def self.find_path_recursive(node, target, path)
    return false unless node
    
    path << node.value
    
    if node.value == target
      return true
    end
    
    if find_path_recursive(node.left, target, path) || 
       find_path_recursive(node.right, target, path)
      return true
    end
    
    path.pop
    false
  end
  
  def self.demonstrate_tree_search
    puts "Tree Traversal Search Demonstration:"
    puts "=" * 50
    
    # Build a sample tree
    root = TreeNode.new(1)
    root.left = TreeNode.new(2)
    root.right = TreeNode.new(3)
    root.left.left = TreeNode.new(4)
    root.left.right = TreeNode.new(5)
    root.right.left = TreeNode.new(6)
    root.right.right = TreeNode.new(7)
    
    target = 5
    
    # DFS
    dfs_result = depth_first_search(root, target)
    puts "DFS search for #{target}: #{dfs_result ? 'Found' : 'Not found'}"
    
    # BFS
    bfs_result = breadth_first_search(root, target)
    puts "BFS search for #{target}: #{bfs_result ? 'Found' : 'Not found'}"
    
    # Find path
    path = find_path(root, target)
    puts "Path to #{target}: #{path.join(' -> ')}"
  end
end
```

## 🗺️ Graph Searching

### 7. Depth-First Search (DFS)

Graph traversal using DFS:

```ruby
class GraphNode
  attr_accessor :value, :neighbors
  
  def initialize(value)
    @value = value
    @neighbors = []
  end
  
  def add_neighbor(node)
    @neighbors << node
  end
end

class DepthFirstSearch
  def self.search(start_node, target)
    visited = Set.new
    dfs_recursive(start_node, target, visited)
  end
  
  def self.dfs_recursive(node, target, visited)
    return nil if visited.include?(node)
    return node if node.value == target
    
    visited.add(node)
    
    node.neighbors.each do |neighbor|
      result = dfs_recursive(neighbor, target, visited)
      return result if result
    end
    
    nil
  end
  
  def self.iterative_search(start_node, target)
    stack = [start_node]
    visited = Set.new
    
    while stack.any?
      current = stack.pop
      
      next if visited.include?(current)
      return current if current.value == target
      
      visited.add(current)
      
      # Add neighbors to stack
      current.neighbors.each { |neighbor| stack.push(neighbor) }
    end
    
    nil
  end
  
  def self.find_all_paths(start_node, target)
    paths = []
    current_path = []
    visited = Set.new
    
    find_all_paths_recursive(start_node, target, current_path, visited, paths)
    paths
  end
  
  def self.find_all_paths_recursive(node, target, current_path, visited, paths)
    return if visited.include?(node)
    
    current_path << node
    visited.add(node)
    
    if node.value == target
      paths << current_path.map(&:value)
    else
      node.neighbors.each do |neighbor|
        find_all_paths_recursive(neighbor, target, current_path, visited, paths)
      end
    end
    
    current_path.pop
    visited.delete(node)
  end
  
  def self.demonstrate_dfs
    puts "Depth-First Search Demonstration:"
    puts "=" * 50
    
    # Build a sample graph
    nodes = {}
    ('A'..'G').each { |letter| nodes[letter] = GraphNode.new(letter) }
    
    # Add edges
    nodes['A'].add_neighbor(nodes['B'])
    nodes['A'].add_neighbor(nodes['C'])
    nodes['B'].add_neighbor(nodes['D'])
    nodes['B'].add_neighbor(nodes['E'])
    nodes['C'].add_neighbor(nodes['F'])
    nodes['C'].add_neighbor(nodes['G'])
    
    start_node = nodes['A']
    target = 'E'
    
    # Recursive DFS
    result = search(start_node, nodes[target])
    puts "DFS search for #{target}: #{result ? 'Found' : 'Not found'}"
    
    # Iterative DFS
    result = iterative_search(start_node, nodes[target])
    puts "Iterative DFS for #{target}: #{result ? 'Found' : 'Not found'}"
    
    # Find all paths
    paths = find_all_paths(start_node, nodes['G'])
    puts "All paths to G: #{paths}"
  end
end
```

### 8. Breadth-First Search (BFS)

Graph traversal using BFS:

```ruby
class BreadthFirstSearch
  def self.search(start_node, target)
    queue = [start_node]
    visited = Set.new([start_node])
    
    while queue.any?
      current = queue.shift
      
      return current if current.value == target
      
      current.neighbors.each do |neighbor|
        unless visited.include?(neighbor)
          visited.add(neighbor)
          queue << neighbor
        end
      end
    end
    
    nil
  end
  
  def self.shortest_path(start_node, target)
    queue = [start_node]
    visited = Set.new([start_node])
    parent = {}
    
    while queue.any?
      current = queue.shift
      
      return build_path(parent, start_node, current) if current.value == target
      
      current.neighbors.each do |neighbor|
        unless visited.include?(neighbor)
          visited.add(neighbor)
          parent[neighbor] = current
          queue << neighbor
        end
      end
    end
    
    nil
  end
  
  def self.build_path(parent, start_node, end_node)
    path = []
    current = end_node
    
    while current
      path.unshift(current)
      current = parent[current]
    end
    
    path
  end
  
  def self.bfs_levels(start_node)
    queue = [start_node]
    visited = Set.new([start_node])
    levels = { start_node => 0 }
    
    while queue.any?
      current = queue.shift
      
      current.neighbors.each do |neighbor|
        unless visited.include?(neighbor)
          visited.add(neighbor)
          levels[neighbor] = levels[current] + 1
          queue << neighbor
        end
      end
    end
    
    levels
  end
  
  def self.demonstrate_bfs
    puts "Breadth-First Search Demonstration:"
    puts "=" * 50
    
    # Build a sample graph
    nodes = {}
    ('A'..'G').each { |letter| nodes[letter] = GraphNode.new(letter) }
    
    # Add edges
    nodes['A'].add_neighbor(nodes['B'])
    nodes['A'].add_neighbor(nodes['C'])
    nodes['B'].add_neighbor(nodes['D'])
    nodes['B'].add_neighbor(nodes['E'])
    nodes['C'].add_neighbor(nodes['F'])
    nodes['C'].add_neighbor(nodes['G'])
    
    start_node = nodes['A']
    target = 'E'
    
    # BFS search
    result = search(start_node, nodes[target])
    puts "BFS search for #{target}: #{result ? 'Found' : 'Not found'}"
    
    # Shortest path
    path = shortest_path(start_node, nodes['G'])
    puts "Shortest path to G: #{path.map(&:value).join(' -> ')}"
    
    # BFS levels
    levels = bfs_levels(start_node)
    puts "Levels from A:"
    levels.each { |node, level| puts "  #{node.value}: level #{level}" }
  end
end
```

## 📊 Performance Comparison

### Algorithm Performance Analysis

```ruby
class SearchingPerformance
  def self.compare_search_algorithms
    puts "Search Algorithm Performance Comparison:"
    puts "=" * 60
    
    algorithms = {
      'Linear Search' => ->(arr, target) { LinearSearch.search(arr, target) },
      'Binary Search' => ->(arr, target) { BinarySearch.search(arr, target) },
      'Ruby Array#include?' => ->(arr, target) { arr.include?(target) ? arr.index(target) : -1 },
      'Ruby Array#index' => ->(arr, target) { arr.index(target) || -1 }
    }
    
    # Test with different array sizes
    [1000, 10000, 100000].each do |size|
      puts "\nArray size: #{size}"
      puts "-" * 40
      
      sorted_array = (1..size).to_a
      unsorted_array = sorted_array.shuffle
      target = size / 2
      
      algorithms.each do |name, algorithm|
        # Use appropriate array for each algorithm
        test_array = name.include?('Binary') || name.include?('Ruby') ? sorted_array : unsorted_array
        
        start_time = Time.now
        result = algorithm.call(test_array, target)
        end_time = Time.now
        
        duration = (end_time - start_time) * 1000 # Convert to microseconds
        puts "#{name.ljust(20)}: #{duration.round(4)}μs (found at index #{result})"
      end
    end
  end
  
  def self.time_complexity_analysis
    puts "\nTime Complexity Analysis:"
    puts "=" * 50
    
    complexities = {
      'Linear Search' => { best: 'O(1)', average: 'O(n)', worst: 'O(n)', space: 'O(1)' },
      'Binary Search' => { best: 'O(1)', average: 'O(log n)', worst: 'O(log n)', space: 'O(1)' },
      'DFS (Tree)' => { best: 'O(1)', average: 'O(n)', worst: 'O(n)', space: 'O(h)' },
      'BFS (Tree)' => { best: 'O(1)', average: 'O(n)', worst: 'O(n)', space: 'O(w)' },
      'DFS (Graph)' => { best: 'O(1)', average: 'O(V+E)', worst: 'O(V+E)', space: 'O(V)' },
      'BFS (Graph)' => { best: 'O(1)', average: 'O(V+E)', worst: 'O(V+E)', space: 'O(V)' }
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

### Real-World Searching Examples

```ruby
class PracticalSearching
  def self.search_contacts
    puts "Searching Contacts:"
    puts "=" * 40
    
    contacts = [
      { name: 'John Doe', email: 'john@example.com', phone: '123-456-7890' },
      { name: 'Jane Smith', email: 'jane@example.com', phone: '234-567-8901' },
      { name: 'Bob Johnson', email: 'bob@example.com', phone: '345-678-9012' },
      { name: 'Alice Brown', email: 'alice@example.com', phone: '456-789-0123' }
    ]
    
    # Search by name
    target_name = 'Jane'
    found_contact = contacts.find { |contact| contact[:name].include?(target_name) }
    
    puts "Searching for '#{target_name}':"
    if found_contact
      puts "Found: #{found_contact[:name]} - #{found_contact[:email]}"
    else
      puts "Not found"
    end
    
    # Search by email domain
    domain = 'example.com'
    domain_contacts = contacts.select { |contact| contact[:email].end_with?(domain) }
    
    puts "\nContacts with domain '#{domain}':"
    domain_contacts.each { |contact| puts "  #{contact[:name]}" }
  end
  
  def self.search_products
    puts "\nSearching Products:"
    puts "=" * 40
    
    products = [
      { id: 1, name: 'Laptop', category: 'Electronics', price: 999.99, rating: 4.5 },
      { id: 2, name: 'Mouse', category: 'Electronics', price: 29.99, rating: 4.2 },
      { id: 3, name: 'Book', category: 'Books', price: 19.99, rating: 4.8 },
      { id: 4, name: 'Keyboard', category: 'Electronics', price: 79.99, rating: 4.6 },
      { id: 5, name: 'Pen', category: 'Office', price: 2.99, rating: 3.9 }
    ]
    
    # Search by category
    category = 'Electronics'
    electronics = products.select { |product| product[:category] == category }
    
    puts "Products in '#{category}' category:"
    electronics.each { |product| puts "  #{product[:name]} - $#{product[:price]}" }
    
    # Search by price range
    min_price = 50
    max_price = 200
    price_range_products = products.select do |product|
      product[:price] >= min_price && product[:price] <= max_price
    end
    
    puts "\nProducts between $#{min_price} and $#{max_price}:"
    price_range_products.each { |product| puts "  #{product[:name]} - $#{product[:price]}" }
    
    # Search by rating
    min_rating = 4.5
    high_rated = products.select { |product| product[:rating] >= min_rating }
    
    puts "\nProducts with rating >= #{min_rating}:"
    high_rated.each { |product| puts "  #{product[:name]} - #{product[:rating]} stars" }
  end
  
  def self.text_search
    puts "\nText Search:"
    puts "=" * 40
    
    documents = [
      'Ruby is a dynamic, object-oriented programming language',
      'Python is another popular programming language',
      'JavaScript is used for web development',
      'Ruby on Rails is a web framework written in Ruby',
      'Programming languages have different paradigms and features'
    ]
    
    # Simple text search
    query = 'Ruby'
    matching_docs = documents.select { |doc| doc.include?(query) }
    
    puts "Documents containing '#{query}':"
    matching_docs.each_with_index do |doc, index|
      puts "  #{index + 1}. #{doc}"
    end
    
    # Case-insensitive search
    query_downcase = query.downcase
    case_insensitive_matches = documents.select { |doc| doc.downcase.include?(query_downcase) }
    
    puts "\nCase-insensitive matches for '#{query}':"
    case_insensitive_matches.each_with_index do |doc, index|
      puts "  #{index + 1}. #{doc}"
    end
    
    # Word boundary search
    word_boundary_matches = documents.select do |doc|
      doc =~ /\b#{query}\b/i
    end
    
    puts "\nWord boundary matches for '#{query}':"
    word_boundary_matches.each_with_index do |doc, index|
      puts "  #{index + 1}. #{doc}"
    end
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Linear Search**: Implement linear search from scratch
2. **Binary Search**: Implement binary search on sorted arrays
3. **Tree Search**: Implement basic tree traversal

### Intermediate Exercises

1. **Graph Search**: Implement DFS and BFS for graphs
2. **Search Optimization**: Optimize search algorithms
3. **Multi-criteria Search**: Implement complex search logic

### Advanced Exercises

1. **Fuzzy Search**: Implement approximate string matching
2. **Index-based Search**: Build and use search indexes
3. **Parallel Search**: Implement concurrent searching

---

## 🎯 Summary

Searching algorithms in Ruby provide:

- **Linear Search** - Simple sequential search
- **Binary Search** - Efficient search on sorted data
- **Tree Searching** - Search in tree data structures
- **Graph Searching** - DFS and BFS traversal
- **Performance Analysis** - Time and space complexity
- **Practical Applications** - Real-world search scenarios

Master these algorithms to efficiently locate and retrieve data!
