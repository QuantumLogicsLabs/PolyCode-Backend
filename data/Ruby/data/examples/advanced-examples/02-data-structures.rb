# Data Structures in Ruby
# This file demonstrates various data structures implemented in Ruby
# with detailed explanations, operations, and complexity analysis.

module AlgorithmsExamples
  module DataStructures
    # Linked List Implementation
    class LinkedList
      class Node
        attr_accessor :value, :next_node, :prev_node
        
        def initialize(value)
          @value = value
          @next_node = nil
          @prev_node = nil
        end
      end

      def initialize
        @head = nil
        @tail = nil
        @size = 0
      end

      def empty?
        @head.nil?
      end

      def size
        @size
      end

      def append(value)
        new_node = Node.new(value)
        
        if empty?
          @head = @tail = new_node
        else
          @tail.next_node = new_node
          new_node.prev_node = @tail
          @tail = new_node
        end
        
        @size += 1
        new_node
      end

      def prepend(value)
        new_node = Node.new(value)
        
        if empty?
          @head = @tail = new_node
        else
          new_node.next_node = @head
          @head.prev_node = new_node
          @head = new_node
        end
        
        @size += 1
        new_node
      end

      def insert_after(node, value)
        new_node = Node.new(value)
        
        new_node.next_node = node.next_node
        new_node.prev_node = node
        
        if node.next_node
          node.next_node.prev_node = new_node
        else
          @tail = new_node
        end
        
        node.next_node = new_node
        @size += 1
        new_node
      end

      def remove(node)
        return nil unless node
        
        if node.prev_node
          node.prev_node.next_node = node.next_node
        else
          @head = node.next_node
        end
        
        if node.next_node
          node.next_node.prev_node = node.prev_node
        else
          @tail = node.prev_node
        end
        
        @size -= 1
        node.value
      end

      def find(value)
        current = @head
        
        while current
          return current if current.value == value
          current = current.next_node
        end
        
        nil
      end

      def to_a
        result = []
        current = @head
        
        while current
          result << current.value
          current = current.next_node
        end
        
        result
      end

      def reverse
        current = @head
        
        while current
          current.prev_node, current.next_node = current.next_node, current.prev_node
          current = current.prev_node
        end
        
        @head, @tail = @tail, @head
      end
    end

    # Stack Implementation
    class Stack
      def initialize
        @items = []
      end

      def push(item)
        @items.push(item)
        self
      end

      def pop
        @items.pop
      end

      def peek
        @items.last
      end

      def empty?
        @items.empty?
      end

      def size
        @items.size
      end

      def to_a
        @items.dup
      end
    end

    # Queue Implementation
    class Queue
      def initialize
        @items = []
      end

      def enqueue(item)
        @items.push(item)
        self
      end

      def dequeue
        @items.shift
      end

      def peek
        @items.first
      end

      def empty?
        @items.empty?
      end

      def size
        @items.size
      end

      def to_a
        @items.dup
      end
    end

    # Binary Search Tree Implementation
    class BinarySearchTree
      class Node
        attr_accessor :value, :left, :right, :parent
        
        def initialize(value)
          @value = value
          @left = nil
          @right = nil
          @parent = nil
        end

        def leaf?
          @left.nil? && @right.nil?
        end

        def children_count
          count = 0
          count += 1 if @left
          count += 1 if @right
          count
        end
      end

      def initialize
        @root = nil
        @size = 0
      end

      def empty?
        @root.nil?
      end

      def size
        @size
      end

      def insert(value)
        if empty?
          @root = Node.new(value)
          @size = 1
        else
          insert_node(@root, value)
        end
      end

      def search(value)
        search_node(@root, value)
      end

      def contains?(value)
        !search(value).nil?
      end

      def delete(value)
        node = search(value)
        return false unless node
        
        delete_node(node)
        @size -= 1
        true
      end

      def inorder_traversal
        result = []
        inorder_traversal_node(@root, result)
        result
      end

      def preorder_traversal
        result = []
        preorder_traversal_node(@root, result)
        result
      end

      def postorder_traversal
        result = []
        postorder_traversal_node(@root, result)
        result
      end

      def min
        return nil if empty?
        
        current = @root
        while current.left
          current = current.left
        end
        current.value
      end

      def max
        return nil if empty?
        
        current = @root
        while current.right
          current = current.right
        end
        current.value
      end

      def balanced?
        balanced_node(@root)
      end

      private

      def insert_node(node, value)
        if value < node.value
          if node.left
            insert_node(node.left, value)
          else
            node.left = Node.new(value)
            node.left.parent = node
            @size += 1
          end
        elsif value > node.value
          if node.right
            insert_node(node.right, value)
          else
            node.right = Node.new(value)
            node.right.parent = node
            @size += 1
          end
        end
      end

      def search_node(node, value)
        return nil if node.nil?
        
        if value == node.value
          node
        elsif value < node.value
          search_node(node.left, value)
        else
          search_node(node.right, value)
        end
      end

      def delete_node(node)
        # Case 1: No children
        if node.leaf?
          replace_node(node, nil)
        # Case 2: One child
        elsif node.children_count == 1
          child = node.left || node.right
          replace_node(node, child)
        # Case 3: Two children
        else
          successor = find_min_node(node.right)
          node.value = successor.value
          delete_node(successor)
        end
      end

      def replace_node(old_node, new_node)
        if old_node.parent
          if old_node.parent.left == old_node
            old_node.parent.left = new_node
          else
            old_node.parent.right = new_node
          end
        else
          @root = new_node
        end
        
        if new_node
          new_node.parent = old_node.parent
        end
      end

      def find_min_node(node)
        return node unless node.left
        find_min_node(node.left)
      end

      def inorder_traversal_node(node, result)
        return unless node
        
        inorder_traversal_node(node.left, result)
        result << node.value
        inorder_traversal_node(node.right, result)
      end

      def preorder_traversal_node(node, result)
        return unless node
        
        result << node.value
        preorder_traversal_node(node.left, result)
        preorder_traversal_node(node.right, result)
      end

      def postorder_traversal_node(node, result)
        return unless node
        
        postorder_traversal_node(node.left, result)
        postorder_traversal_node(node.right, result)
        result << node.value
      end

      def balanced_node(node)
        return true if node.nil?
        
        left_height = height(node.left)
        right_height = height(node.right)
        
        return false if (left_height - right_height).abs > 1
        
        balanced_node(node.left) && balanced_node(node.right)
      end

      def height(node)
        return 0 if node.nil?
        
        1 + [height(node.left), height(node.right)].max
      end
    end

    # Hash Table Implementation
    class HashTable
      def initialize(capacity = 16)
        @capacity = capacity
        @buckets = Array.new(capacity) { [] }
        @size = 0
      end

      def empty?
        @size == 0
      end

      def size
        @size
      end

      def set(key, value)
        index = hash(key) % @capacity
        bucket = @buckets[index]
        
        existing_entry = bucket.find { |entry| entry[:key] == key }
        
        if existing_entry
          existing_entry[:value] = value
        else
          bucket << { key: key, value: value }
          @size += 1
          
          # Resize if load factor is too high
          resize if @size > @capacity * 0.75
        end
      end

      def get(key)
        index = hash(key) % @capacity
        bucket = @buckets[index]
        
        entry = bucket.find { |entry| entry[:key] == key }
        entry ? entry[:value] : nil
      end

      def delete(key)
        index = hash(key) % @capacity
        bucket = @buckets[index]
        
        entry_index = bucket.find_index { |entry| entry[:key] == key }
        
        if entry_index
          bucket.delete_at(entry_index)
          @size -= 1
          true
        else
          false
        end
      end

      def contains_key?(key)
        !get(key).nil?
      end

      def keys
        @buckets.flat_map { |bucket| bucket.map { |entry| entry[:key] } }
      end

      def values
        @buckets.flat_map { |bucket| bucket.map { |entry| entry[:value] } }
      end

      def to_h
        result = {}
        @buckets.each do |bucket|
          bucket.each do |entry|
            result[entry[:key]] = entry[:value]
          end
        end
        result
      end

      private

      def hash(key)
        key.hash
      end

      def resize
        old_buckets = @buckets
        @capacity *= 2
        @buckets = Array.new(@capacity) { [] }
        @size = 0
        
        old_buckets.each do |bucket|
          bucket.each do |entry|
            set(entry[:key], entry[:value])
          end
        end
      end
    end

    # Graph Implementation
    class Graph
      def initialize(directed = false)
        @directed = directed
        @vertices = {}
        @edges = []
      end

      def directed?
        @directed
      end

      def add_vertex(vertex)
        @vertices[vertex] = [] unless @vertices.key?(vertex)
      end

      def add_edge(vertex1, vertex2, weight = 1)
        add_vertex(vertex1)
        add_vertex(vertex2)
        
        @vertices[vertex1] << vertex2
        @edges << { from: vertex1, to: vertex2, weight: weight }
        
        unless @directed
          @vertices[vertex2] << vertex1
          @edges << { from: vertex2, to: vertex1, weight: weight }
        end
      end

      def remove_vertex(vertex)
        return unless @vertices.key?(vertex)
        
        # Remove all edges connected to this vertex
        @edges.reject! do |edge|
          edge[:from] == vertex || edge[:to] == vertex
        end
        
        # Remove vertex from adjacency lists
        @vertices.each do |v, neighbors|
          neighbors.reject! { |neighbor| neighbor == vertex }
        end
        
        @vertices.delete(vertex)
      end

      def remove_edge(vertex1, vertex2)
        @edges.reject! do |edge|
          (edge[:from] == vertex1 && edge[:to] == vertex2) ||
          (!@directed && edge[:from] == vertex2 && edge[:to] == vertex1)
        end
        
        @vertices[vertex1].reject! { |neighbor| neighbor == vertex2 }
        unless @directed
          @vertices[vertex2].reject! { |neighbor| neighbor == vertex1 }
        end
      end

      def has_vertex?(vertex)
        @vertices.key?(vertex)
      end

      def has_edge?(vertex1, vertex2)
        @vertices[vertex1]&.include?(vertex2) || 
        (!@directed && @vertices[vertex2]&.include?(vertex1))
      end

      def neighbors(vertex)
        @vertices[vertex] || []
      end

      def vertices
        @vertices.keys
      end

      def edges
        @edges.dup
      end

      def degree(vertex)
        @vertices[vertex]&.length || 0
      end

      def bfs(start_vertex)
        return [] unless has_vertex?(start_vertex)
        
        visited = Set.new([start_vertex])
        queue = [start_vertex]
        result = []
        
        while queue.any?
          vertex = queue.shift
          result << vertex
          
          neighbors(vertex).each do |neighbor|
            unless visited.include?(neighbor)
              visited.add(neighbor)
              queue << neighbor
            end
          end
        end
        
        result
      end

      def dfs(start_vertex)
        return [] unless has_vertex?(start_vertex)
        
        visited = Set.new
        result = []
        
        dfs_recursive(start_vertex, visited, result)
        
        result
      end

      def topological_sort
        return [] unless @directed
        
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
        
        result.length == vertices.length ? result : nil
      end

      def shortest_path(start_vertex, end_vertex)
        return [] unless has_vertex?(start_vertex) && has_vertex?(end_vertex)
        
        distances = Hash.new(Float::INFINITY)
        predecessors = Hash.new
        distances[start_vertex] = 0
        
        queue = [start_vertex]
        
        while queue.any?
          current = queue.shift
          
          break if current == end_vertex
          
          neighbors(current).each do |neighbor|
            distance = distances[current] + 1
            
            if distance < distances[neighbor]
              distances[neighbor] = distance
              predecessors[neighbor] = current
              queue << neighbor unless queue.include?(neighbor)
            end
          end
        end
        
        return [] if distances[end_vertex] == Float::INFINITY
        
        # Reconstruct path
        path = []
        current = end_vertex
        
        while current
          path.unshift(current)
          current = predecessors[current]
        end
        
        path
      end

      private

      def dfs_recursive(vertex, visited, result)
        return if visited.include?(vertex)
        
        visited.add(vertex)
        result << vertex
        
        neighbors(vertex).each do |neighbor|
          dfs_recursive(neighbor, visited, result)
        end
      end
    end

    # Min-Heap Implementation
    class MinHeap
      def initialize
        @elements = []
      end

      def empty?
        @elements.empty?
      end

      def size
        @elements.size
      end

      def insert(element)
        @elements << element
        bubble_up(@elements.size - 1)
      end

      def extract_min
        return nil if empty?
        
        min = @elements[0]
        last = @elements.pop
        
        unless empty?
          @elements[0] = last
          bubble_down(0)
        end
        
        min
      end

      def peek
        @elements[0]
      end

      def to_a
        @elements.dup
      end

      private

      def bubble_up(index)
        return if index == 0
        
        parent_index = (index - 1) / 2
        
        while index > 0 && @elements[index] < @elements[parent_index]
          @elements[index], @elements[parent_index] = @elements[parent_index], @elements[index]
          index = parent_index
          parent_index = (index - 1) / 2
        end
      end

      def bubble_down(index)
        smallest = index
        left_child = 2 * index + 1
        right_child = 2 * index + 2
        
        if left_child < @elements.size && @elements[left_child] < @elements[smallest]
          smallest = left_child
        end
        
        if right_child < @elements.size && @elements[right_child] < @elements[smallest]
          smallest = right_child
        end
        
        if smallest != index
          @elements[index], @elements[smallest] = @elements[smallest], @elements[index]
          bubble_down(smallest)
        end
      end
    end

    # Priority Queue Implementation
    class PriorityQueue
      def initialize
        @heap = MinHeap.new
      end

      def empty?
        @heap.empty?
      end

      def size
        @heap.size
      end

      def enqueue(priority, item)
        @heap.insert([priority, item])
      end

      def dequeue
        return nil if empty?
        
        priority, item = @heap.extract_min
        item
      end

      def peek
        return nil if empty?
        
        priority, item = @heap.peek
        item
      end

      def change_priority(item, new_priority)
        # This is a simplified implementation
        # In a real implementation, you'd need to track item positions
        @heap.elements.each_with_index do |element, index|
          if element[1] == item
            element[0] = new_priority
            # Rebuild heap to maintain heap property
            @heap.elements.sort_by! { |e| e[0] }
            break
          end
        end
      end
    end

    # Union-Find (Disjoint Set) Implementation
    class UnionFind
      def initialize
        @parent = {}
        @rank = {}
      end

      def make_set(element)
        @parent[element] = element
        @rank[element] = 0
      end

      def find(element)
        return element unless @parent[element]
        
        @parent[element] = find(@parent[element])
      end

      def union(element1, element2)
        root1 = find(element1)
        root2 = find(element2)
        
        return if root1 == root2
        
        if @rank[root1] < @rank[root2]
          @parent[root1] = root2
        elsif @rank[root1] > @rank[root2]
          @parent[root2] = root1
        else
          @parent[root2] = root1
          @rank[root1] += 1
        end
      end

      def connected?(element1, element2)
        find(element1) == find(element2)
      end

      def sets
        @parent.keys.group_by { |element| find(element) }.values
      end
    end

    # Trie (Prefix Tree) Implementation
    class Trie
      class Node
        attr_accessor :children, :is_end_of_word
        
        def initialize
          @children = {}
          @is_end_of_word = false
        end
      end

      def initialize
        @root = Node.new
        @size = 0
      end

      def insert(word)
        node = @root
        
        word.each_char do |char|
          node.children[char] = Node.new unless node.children[char]
          node = node.children[char]
        end
        
        unless node.is_end_of_word
          node.is_end_of_word = true
          @size += 1
        end
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

      def words_with_prefix(prefix)
        node = @root
        
        prefix.each_char do |char|
          return [] unless node.children[char]
          node = node.children[char]
        end
        
        words = []
        collect_words(node, prefix, words)
        words
      end

      def size
        @size
      end

      def empty?
        @size == 0
      end

      def to_a
        words = []
        collect_words(@root, "", words)
        words
      end

      private

      def collect_words(node, prefix, words)
        if node.is_end_of_word
          words << prefix
        end
        
        node.children.each do |char, child|
          collect_words(child, prefix + char, words)
        end
      end
    end

    # LRU Cache Implementation
    class LRUCache
      def initialize(capacity)
        @capacity = capacity
        @cache = {}
        @order = []
      end

      def get(key)
        if @cache.key?(key)
          # Move to end (most recently used)
          @order.delete(key)
          @order << key
          @cache[key]
        else
          nil
        end
      end

      def put(key, value)
        if @cache.key?(key)
          # Update value and move to end
          @cache[key] = value
          @order.delete(key)
          @order << key
        else
          # Add new entry
          if @order.size >= @capacity
            # Remove least recently used
            lru = @order.shift
            @cache.delete(lru)
          end
          
          @cache[key] = value
          @order << key
        end
      end

      def delete(key)
        if @cache.key?(key)
          @cache.delete(key)
          @order.delete(key)
        end
      end

      def size
        @cache.size
      end

      def empty?
        @cache.empty?
      end

      def keys
        @order.dup
      end

      def to_h
        @cache.dup
      end
    end
  end
end

# Usage examples and demonstrations
if __FILE__ == $0
  # Linked List demonstration
  puts "Linked List Demonstration"
  puts "=" * 50

  list = AlgorithmsExamples::DataStructures::LinkedList.new
  list.append(1)
  list.append(2)
  list.append(3)
  list.prepend(0)
  
  puts "Linked List: #{list.to_a}"
  puts "Size: #{list.size}"
  puts "Find 2: #{list.find(2).value}"
  
  list.remove(list.find(2))
  puts "After removing 2: #{list.to_a}"

  # Stack demonstration
  puts "\nStack Demonstration"
  puts "=" * 50

  stack = AlgorithmsExamples::DataStructures::Stack.new
  stack.push(1)
  stack.push(2)
  stack.push(3)
  
  puts "Stack: #{stack.to_a}"
  puts "Peek: #{stack.peek}"
  puts "Pop: #{stack.pop}"
  puts "After pop: #{stack.to_a}"

  # Queue demonstration
  puts "\nQueue Demonstration"
  puts "=" * 50

  queue = AlgorithmsExamples::DataStructures::Queue.new
  queue.enqueue(1)
  queue.enqueue(2)
  queue.enqueue(3)
  
  puts "Queue: #{queue.to_a}"
  puts "Peek: #{queue.peek}"
  puts "Dequeue: #{queue.dequeue}"
  puts "After dequeue: #{queue.to_a}"

  # Binary Search Tree demonstration
  puts "\nBinary Search Tree Demonstration"
  puts "=" * 50

  bst = AlgorithmsExamples::DataStructures::BinarySearchTree.new
  [5, 3, 7, 2, 4, 6, 8].each { |val| bst.insert(val) }
  
  puts "Inorder: #{bst.inorder_traversal}"
  puts "Preorder: #{bst.preorder_traversal}"
  puts "Postorder: #{bst.postorder_traversal}"
  puts "Min: #{bst.min}"
  puts "Max: #{bst.max}"
  puts "Contains 4: #{bst.contains?(4)}"
  puts "Balanced: #{bst.balanced?}"

  # Hash Table demonstration
  puts "\nHash Table Demonstration"
  puts "=" * 50

  hash_table = AlgorithmsExamples::DataStructures::HashTable.new
  hash_table.set('name', 'Alice')
  hash_table.set('age', 25)
  hash_table.set('city', 'New York')
  
  puts "Get name: #{hash_table.get('name')}"
  puts "Keys: #{hash_table.keys}"
  puts "Values: #{hash_table.values}"
  puts "Contains 'age': #{hash_table.contains_key?('age')}"
  puts "Size: #{hash_table.size}"

  # Graph demonstration
  puts "\nGraph Demonstration"
  puts "=" * 50

  graph = AlgorithmsExamples::DataStructures::Graph.new(false)
  graph.add_edge('A', 'B')
  graph.add_edge('B', 'C')
  graph.add_edge('C', 'D')
  graph.add_edge('D', 'E')
  graph.add_edge('A', 'E')
  
  puts "Vertices: #{graph.vertices}"
  puts "Edges: #{graph.edges.length}"
  puts "BFS from A: #{graph.bfs('A')}"
  puts "DFS from A: #{graph.dfs('A')}"
  puts "Shortest path A to E: #{graph.shortest_path('A', 'E')}"
  puts "Degree of B: #{graph.degree('B')}"

  # Min-Heap demonstration
  puts "\nMin-Heap Demonstration"
  puts "=" * 50

  heap = AlgorithmsExamples::DataStructures::MinHeap.new
  [5, 3, 8, 1, 4, 2].each { |val| heap.insert(val) }
  
  puts "Heap: #{heap.to_a}"
  puts "Extract min: #{heap.extract_min}"
  puts "After extraction: #{heap.to_a}"
  puts "Peek: #{heap.peek}"

  # Priority Queue demonstration
  puts "\nPriority Queue Demonstration"
  puts "=" * 50

  pq = AlgorithmsExamples::DataStructures::PriorityQueue.new
  pq.enqueue(3, 'Task 3')
  pq.enqueue(1, 'Task 1')
  pq.enqueue(2, 'Task 2')
  
  puts "Dequeue: #{pq.dequeue}"
  puts "Peek: #{pq.peek}"
  puts "Dequeue: #{pq.dequeue}"

  # Trie demonstration
  puts "\nTrie Demonstration"
  puts "=" * 50

  trie = AlgorithmsExamples::DataStructures::Trie.new
  ['apple', 'app', 'application', 'apply'].each { |word| trie.insert(word) }
  
  puts "Search 'apple': #{trie.search('apple')}"
  puts "Search 'app': #{trie.search('app')}"
  puts "Starts with 'app': #{trie.starts_with('app')}"
  puts "Words with prefix 'app': #{trie.words_with_prefix('app')}"
  puts "All words: #{trie.to_a}"

  # LRU Cache demonstration
  puts "\nLRU Cache Demonstration"
  puts "=" * 50

  lru = AlgorithmsExamples::DataStructures::LRUCache.new(3)
  lru.put('a', 1)
  lru.put('b', 2)
  lru.put('c', 3)
  
  puts "Cache: #{lru.to_h}"
  puts "Get 'a': #{lru.get('a')}"
  puts "Cache after get 'a': #{lru.to_h}"
  
  lru.put('d', 4)  # Should evict 'b'
  puts "After adding 'd': #{lru.to_h}"
  puts "Keys (LRU order): #{lru.keys}"
end
