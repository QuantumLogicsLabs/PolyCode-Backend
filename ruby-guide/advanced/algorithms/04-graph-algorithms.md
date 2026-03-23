# Graph Algorithms in Ruby
# Comprehensive guide to graph algorithms and their implementations

## 🎯 Overview

Graphs are versatile data structures used to model relationships between objects. This guide covers fundamental graph algorithms, their implementations in Ruby, and their practical applications.

## 📊 Graph Representation

### 1. Graph Data Structures

Different ways to represent graphs:

```ruby
class GraphNode
  attr_accessor :value, :neighbors, :visited
  
  def initialize(value)
    @value = value
    @neighbors = []
    @visited = false
  end
  
  def add_neighbor(node)
    @neighbors << node
  end
  
  def reset_visited
    @visited = false
  end
end

class AdjacencyMatrixGraph
  def initialize(size)
    @size = size
    @matrix = Array.new(size) { Array.new(size, 0) }
    @vertices = {}
  end
  
  def add_vertex(vertex)
    return if @vertices.key?(vertex)
    
    index = @vertices.size
    @vertices[vertex] = index
    
    # Expand matrix if needed
    if index >= @size
      @size *= 2
      @matrix.each { |row| row.concat(Array.new(@size - row.length, 0)) }
      @matrix.concat(Array.new(@size - @matrix.length) { Array.new(@size, 0) })
    end
  end
  
  def add_edge(vertex1, vertex2, weight = 1)
    add_vertex(vertex1) unless @vertices.key?(vertex1)
    add_vertex(vertex2) unless @vertices.key?(vertex2)
    
    index1 = @vertices[vertex1]
    index2 = @vertices[vertex2]
    
    @matrix[index1][index2] = weight
    @matrix[index2][index1] = weight # For undirected graph
  end
  
  def has_edge?(vertex1, vertex2)
    return false unless @vertices.key?(vertex1) && @vertices.key?(vertex2)
    
    index1 = @vertices[vertex1]
    index2 = @vertices[vertex2]
    
    @matrix[index1][index2] > 0
  end
  
  def get_neighbors(vertex)
    return [] unless @vertices.key?(vertex)
    
    index = @vertices[vertex]
    neighbors = []
    
    @matrix[index].each_with_index do |weight, neighbor_index|
      if weight > 0
        neighbor_vertex = @vertices.key(neighbor_index)
        neighbors << neighbor_vertex if neighbor_vertex
      end
    end
    
    neighbors
  end
  
  def display
    puts "Adjacency Matrix:"
    puts "  " + @vertices.keys.join("  ")
    
    @matrix.each_with_index do |row, i|
      vertex = @vertices.key(i)
      if vertex
        puts "#{vertex} " + row.map { |cell| cell.to_s.ljust(2) }.join(" ")
      end
    end
  end
end

class AdjacencyListGraph
  def initialize
    @vertices = {}
  end
  
  def add_vertex(vertex)
    @vertices[vertex] ||= []
  end
  
  def add_edge(vertex1, vertex2, weight = 1)
    add_vertex(vertex1)
    add_vertex(vertex2)
    
    @vertices[vertex1] << { vertex: vertex2, weight: weight }
    @vertices[vertex2] << { vertex: vertex1, weight: weight } # For undirected graph
  end
  
  def has_edge?(vertex1, vertex2)
    return false unless @vertices[vertex1]
    
    @vertices[vertex1].any? { |edge| edge[:vertex] == vertex2 }
  end
  
  def get_neighbors(vertex)
    return [] unless @vertices[vertex]
    
    @vertices[vertex].map { |edge| edge[:vertex] }
  end
  
  def get_edge_weight(vertex1, vertex2)
    return nil unless @vertices[vertex1]
    
    edge = @vertices[vertex1].find { |e| e[:vertex] == vertex2 }
    edge ? edge[:weight] : nil
  end
  
  def display
    puts "Adjacency List:"
    @vertices.each do |vertex, edges|
      neighbors = edges.map { |edge| "#{edge[:vertex]}(#{edge[:weight]})" }.join(", ")
      puts "#{vertex} -> [#{neighbors}]"
    end
  end
end
```

## 🚀 Traversal Algorithms

### 2. Depth-First Search (DFS)

Graph traversal using DFS:

```ruby
class DepthFirstSearch
  def self.dfs_recursive(graph, start_vertex, visited = Set.new)
    return [] unless graph.get_neighbors(start_vertex).any?
    
    result = []
    visited.add(start_vertex)
    result << start_vertex
    
    graph.get_neighbors(start_vertex).each do |neighbor|
      unless visited.include?(neighbor)
        result.concat(dfs_recursive(graph, neighbor, visited))
      end
    end
    
    result
  end
  
  def self.dfs_iterative(graph, start_vertex)
    visited = Set.new
    stack = [start_vertex]
    result = []
    
    while stack.any?
      current = stack.pop
      
      unless visited.include?(current)
        visited.add(current)
        result << current
        
        # Add neighbors to stack (reverse for consistent order)
        graph.get_neighbors(current).reverse.each do |neighbor|
          stack << neighbor unless visited.include?(neighbor)
        end
      end
    end
    
    result
  end
  
  def self.dfs_with_path(graph, start_vertex, target_vertex)
    visited = Set.new
    path = []
    
    if dfs_path_recursive(graph, start_vertex, target_vertex, visited, path)
      path
    else
      []
    end
  end
  
  def self.dfs_path_recursive(graph, current, target, visited, path)
    return false if visited.include?(current)
    
    visited.add(current)
    path << current
    
    if current == target
      return true
    end
    
    graph.get_neighbors(current).each do |neighbor|
      if dfs_path_recursive(graph, neighbor, target, visited, path)
        return true
      end
    end
    
    path.pop
    false
  end
  
  def self.demonstrate_dfs
    puts "Depth-First Search Demonstration:"
    puts "=" * 50
    
    # Create graph
    graph = AdjacencyListGraph.new
    
    # Add edges
    edges = [
      ['A', 'B'], ['A', 'C'], ['B', 'D'], ['B', 'E'],
      ['C', 'F'], ['C', 'G'], ['D', 'H'], ['E', 'I'],
      ['F', 'J'], ['G', 'K']
    ]
    
    edges.each { |v1, v2| graph.add_edge(v1, v2) }
    
    graph.display
    
    start_vertex = 'A'
    
    # Recursive DFS
    dfs_result = dfs_recursive(graph, start_vertex)
    puts "\nDFS (recursive) from #{start_vertex}: #{dfs_result.join(' -> ')}"
    
    # Iterative DFS
    iterative_result = dfs_iterative(graph, start_vertex)
    puts "DFS (iterative) from #{start_vertex}: #{iterative_result.join(' -> ')}"
    
    # Find path
    target = 'I'
    path = dfs_with_path(graph, start_vertex, target)
    puts "Path from #{start_vertex} to #{target}: #{path.join(' -> ')}"
  end
end
```

### 3. Breadth-First Search (BFS)

Graph traversal using BFS:

```ruby
class BreadthFirstSearch
  def self.bfs(graph, start_vertex)
    visited = Set.new([start_vertex])
    queue = [start_vertex]
    result = []
    
    while queue.any?
      current = queue.shift
      result << current
      
      graph.get_neighbors(current).each do |neighbor|
        unless visited.include?(neighbor)
          visited.add(neighbor)
          queue << neighbor
        end
      end
    end
    
    result
  end
  
  def self.shortest_path(graph, start_vertex, target_vertex)
    return [] unless graph.get_neighbors(start_vertex).any?
    
    queue = [start_vertex]
    visited = Set.new([start_vertex])
    parent = {}
    
    while queue.any?
      current = queue.shift
      
      if current == target_vertex
        return build_path(parent, start_vertex, target_vertex)
      end
      
      graph.get_neighbors(current).each do |neighbor|
        unless visited.include?(neighbor)
          visited.add(neighbor)
          parent[neighbor] = current
          queue << neighbor
        end
      end
    end
    
    [] # No path found
  end
  
  def self.build_path(parent, start_vertex, end_vertex)
    path = []
    current = end_vertex
    
    while current
      path.unshift(current)
      current = parent[current]
    end
    
    path
  end
  
  def self.bfs_levels(graph, start_vertex)
    visited = Set.new([start_vertex])
    queue = [[start_vertex, 0]]
    levels = { start_vertex => 0 }
    
    while queue.any?
      current, level = queue.shift
      
      graph.get_neighbors(current).each do |neighbor|
        unless visited.include?(neighbor)
          visited.add(neighbor)
          levels[neighbor] = level + 1
          queue << [neighbor, level + 1]
        end
      end
    end
    
    levels
  end
  
  def self.demonstrate_bfs
    puts "Breadth-First Search Demonstration:"
    puts "=" * 50
    
    # Create graph
    graph = AdjacencyListGraph.new
    
    # Add edges
    edges = [
      ['A', 'B'], ['A', 'C'], ['B', 'D'], ['B', 'E'],
      ['C', 'F'], ['C', 'G'], ['D', 'H'], ['E', 'I'],
      ['F', 'J'], ['G', 'K']
    ]
    
    edges.each { |v1, v2| graph.add_edge(v1, v2) }
    
    start_vertex = 'A'
    
    # BFS traversal
    bfs_result = bfs(graph, start_vertex)
    puts "BFS from #{start_vertex}: #{bfs_result.join(' -> ')}"
    
    # Shortest path
    target = 'I'
    path = shortest_path(graph, start_vertex, target)
    puts "Shortest path from #{start_vertex} to #{target}: #{path.join(' -> ')}"
    
    # BFS levels
    levels = bfs_levels(graph, start_vertex)
    puts "\nBFS levels from #{start_vertex}:"
    levels.each { |vertex, level| puts "  #{vertex}: level #{level}" }
  end
end
```

## 🎯 Shortest Path Algorithms

### 4. Dijkstra's Algorithm

Finding shortest paths in weighted graphs:

```ruby
class DijkstraAlgorithm
  def self.shortest_path(graph, start_vertex, end_vertex)
    distances = {}
    previous = {}
    unvisited = Set.new
    
    # Initialize distances
    graph.instance_variable_get(:@vertices).keys.each do |vertex|
      distances[vertex] = Float::INFINITY
      previous[vertex] = nil
      unvisited.add(vertex)
    end
    
    distances[start_vertex] = 0
    
    while unvisited.any?
      # Find vertex with minimum distance
      current = unvisited.min_by { |vertex| distances[vertex] }
      break if distances[current] == Float::INFINITY
      
      unvisited.delete(current)
      
      # Update distances to neighbors
      graph.get_neighbors(current).each do |neighbor|
        if unvisited.include?(neighbor)
          alt = distances[current] + graph.get_edge_weight(current, neighbor)
          
          if alt < distances[neighbor]
            distances[neighbor] = alt
            previous[neighbor] = current
          end
        end
      end
    end
    
    # Build path
    if distances[end_vertex] == Float::INFINITY
      { distance: Float::INFINITY, path: [] }
    else
      path = []
      current = end_vertex
      
      while current
        path.unshift(current)
        current = previous[current]
      end
      
      { distance: distances[end_vertex], path: path }
    end
  end
  
  def self.all_shortest_paths(graph, start_vertex)
    distances = {}
    previous = {}
    unvisited = Set.new
    
    # Initialize distances
    graph.instance_variable_get(:@vertices).keys.each do |vertex|
      distances[vertex] = Float::INFINITY
      previous[vertex] = nil
      unvisited.add(vertex)
    end
    
    distances[start_vertex] = 0
    
    while unvisited.any?
      current = unvisited.min_by { |vertex| distances[vertex] }
      break if distances[current] == Float::INFINITY
      
      unvisited.delete(current)
      
      graph.get_neighbors(current).each do |neighbor|
        if unvisited.include?(neighbor)
          alt = distances[current] + graph.get_edge_weight(current, neighbor)
          
          if alt < distances[neighbor]
            distances[neighbor] = alt
            previous[neighbor] = current
          end
        end
      end
    end
    
    # Build all paths
    paths = {}
    distances.each do |vertex, distance|
      if distance < Float::INFINITY
        path = []
        current = vertex
        
        while current
          path.unshift(current)
          current = previous[current]
        end
        
        paths[vertex] = { distance: distance, path: path }
      else
        paths[vertex] = { distance: Float::INFINITY, path: [] }
      end
    end
    
    paths
  end
  
  def self.demonstrate_dijkstra
    puts "Dijkstra's Algorithm Demonstration:"
    puts "=" * 50
    
    # Create weighted graph
    graph = AdjacencyListGraph.new
    
    # Add weighted edges
    weighted_edges = [
      ['A', 'B', 4], ['A', 'C', 2], ['B', 'C', 1],
      ['B', 'D', 5], ['C', 'D', 8], ['C', 'E', 10],
      ['D', 'E', 2], ['D', 'F', 6], ['E', 'F', 3]
    ]
    
    weighted_edges.each { |v1, v2, weight| graph.add_edge(v1, v2, weight) }
    
    graph.display
    
    start_vertex = 'A'
    end_vertex = 'F'
    
    # Shortest path
    result = shortest_path(graph, start_vertex, end_vertex)
    puts "\nShortest path from #{start_vertex} to #{end_vertex}:"
    puts "Distance: #{result[:distance]}"
    puts "Path: #{result[:path].join(' -> ')}"
    
    # All shortest paths
    puts "\nAll shortest paths from #{start_vertex}:"
    all_paths = all_shortest_paths(graph, start_vertex)
    all_paths.each do |vertex, path_info|
      if path_info[:distance] < Float::INFINITY
        puts "#{vertex}: distance #{path_info[:distance]}, path #{path_info[:path].join(' -> ')}"
      else
        puts "#{vertex}: unreachable"
      end
    end
  end
end
```

### 5. Bellman-Ford Algorithm

Shortest paths with negative edge weights:

```ruby
class BellmanFordAlgorithm
  def self.shortest_path(graph, start_vertex)
    distances = {}
    previous = {}
    
    # Initialize distances
    graph.instance_variable_get(:@vertices).keys.each do |vertex|
      distances[vertex] = Float::INFINITY
      previous[vertex] = nil
    end
    
    distances[start_vertex] = 0
    
    # Relax edges repeatedly
    (graph.instance_variable_get(:@vertices).size - 1).times do
      graph.instance_variable_get(:@vertices).each do |vertex, edges|
        edges.each do |edge|
          neighbor = edge[:vertex]
          weight = edge[:weight]
          
          if distances[vertex] + weight < distances[neighbor]
            distances[neighbor] = distances[vertex] + weight
            previous[neighbor] = vertex
          end
        end
      end
    end
    
    # Check for negative cycles
    graph.instance_variable_get(:@vertices).each do |vertex, edges|
      edges.each do |edge|
        neighbor = edge[:vertex]
        weight = edge[:weight]
        
        if distances[vertex] + weight < distances[neighbor]
          raise "Negative cycle detected!"
        end
      end
    end
    
    # Build paths
    paths = {}
    distances.each do |vertex, distance|
      if distance < Float::INFINITY
        path = []
        current = vertex
        
        while current
          path.unshift(current)
          current = previous[current]
        end
        
        paths[vertex] = { distance: distance, path: path }
      else
        paths[vertex] = { distance: Float::INFINITY, path: [] }
      end
    end
    
    paths
  end
  
  def self.demonstrate_bellman_ford
    puts "Bellman-Ford Algorithm Demonstration:"
    puts "=" * 50
    
    # Create weighted graph with potential negative edges
    graph = AdjacencyListGraph.new
    
    # Add weighted edges (including negative)
    weighted_edges = [
      ['A', 'B', 4], ['A', 'C', 2], ['B', 'C', -3],
      ['B', 'D', 5], ['C', 'D', 1], ['C', 'E', 10],
      ['D', 'E', 2], ['D', 'F', 6], ['E', 'F', 3]
    ]
    
    weighted_edges.each { |v1, v2, weight| graph.add_edge(v1, v2, weight) }
    
    graph.display
    
    start_vertex = 'A'
    
    begin
      paths = shortest_path(graph, start_vertex)
      
      puts "\nShortest paths from #{start_vertex}:"
      paths.each do |vertex, path_info|
        if path_info[:distance] < Float::INFINITY
          puts "#{vertex}: distance #{path_info[:distance]}, path #{path_info[:path].join(' -> ')}"
        else
          puts "#{vertex}: unreachable"
        end
      end
    rescue => e
      puts "Error: #{e.message}"
    end
  end
end
```

## 🌐 Minimum Spanning Tree

### 6. Kruskal's Algorithm

Finding minimum spanning tree:

```ruby
class KruskalAlgorithm
  Edge = Struct.new(:from, :to, :weight)
  
  def self.minimum_spanning_tree(graph)
    edges = []
    
    # Collect all edges
    graph.instance_variable_get(:@vertices).each do |vertex, vertex_edges|
      vertex_edges.each do |edge|
        # Add each edge only once (avoid duplicates for undirected graphs)
        edges << Edge.new(vertex, edge[:vertex], edge[:weight]) if vertex < edge[:vertex]
      end
    end
    
    # Sort edges by weight
    edges.sort_by!(&:weight)
    
    # Initialize disjoint set
    parent = {}
    rank = {}
    
    graph.instance_variable_get(:@vertices).keys.each do |vertex|
      parent[vertex] = vertex
      rank[vertex] = 0
    end
    
    mst_edges = []
    total_weight = 0
    
    edges.each do |edge|
      root1 = find(parent, edge.from)
      root2 = find(parent, edge.to)
      
      if root1 != root2
        mst_edges << edge
        total_weight += edge.weight
        union(parent, rank, root1, root2)
        
        break if mst_edges.size == graph.instance_variable_get(:@vertices).size - 1
      end
    end
    
    { edges: mst_edges, total_weight: total_weight }
  end
  
  def self.find(parent, vertex)
    return vertex if parent[vertex] == vertex
    
    parent[vertex] = find(parent, parent[vertex])
  end
  
  def self.union(parent, rank, root1, root2)
    if rank[root1] < rank[root2]
      parent[root1] = root2
    elsif rank[root1] > rank[root2]
      parent[root2] = root1
    else
      parent[root2] = root1
      rank[root1] += 1
    end
  end
  
  def self.demonstrate_kruskal
    puts "Kruskal's Algorithm Demonstration:"
    puts "=" * 50
    
    # Create weighted graph
    graph = AdjacencyListGraph.new
    
    # Add weighted edges
    weighted_edges = [
      ['A', 'B', 4], ['A', 'C', 2], ['B', 'C', 1],
      ['B', 'D', 5], ['C', 'D', 8], ['C', 'E', 10],
      ['D', 'E', 2], ['D', 'F', 6], ['E', 'F', 3]
    ]
    
    weighted_edges.each { |v1, v2, weight| graph.add_edge(v1, v2, weight) }
    
    graph.display
    
    # Find MST
    mst = minimum_spanning_tree(graph)
    
    puts "\nMinimum Spanning Tree:"
    puts "Total weight: #{mst[:total_weight]}"
    puts "Edges:"
    mst[:edges].each do |edge|
      puts "  #{edge.from} -- #{edge.to} (weight: #{edge.weight})"
    end
  end
end
```

### 7. Prim's Algorithm

Alternative MST algorithm:

```ruby
class PrimAlgorithm
  def self.minimum_spanning_tree(graph, start_vertex)
    visited = Set.new([start_vertex])
    mst_edges = []
    total_weight = 0
    
    while visited.size < graph.instance_variable_get(:@vertices).size
      min_edge = nil
      min_weight = Float::INFINITY
      
      # Find minimum edge connecting visited to unvisited
      visited.each do |vertex|
        graph.get_neighbors(vertex).each do |neighbor|
          unless visited.include?(neighbor)
            weight = graph.get_edge_weight(vertex, neighbor)
            
            if weight < min_weight
              min_weight = weight
              min_edge = [vertex, neighbor, weight]
            end
          end
        end
      end
      
      break unless min_edge
      
      from, to, weight = min_edge
      mst_edges << { from: from, to: to, weight: weight }
      total_weight += weight
      visited.add(to)
    end
    
    { edges: mst_edges, total_weight: total_weight }
  end
  
  def self.demonstrate_prim
    puts "Prim's Algorithm Demonstration:"
    puts "=" * 50
    
    # Create weighted graph
    graph = AdjacencyListGraph.new
    
    # Add weighted edges
    weighted_edges = [
      ['A', 'B', 4], ['A', 'C', 2], ['B', 'C', 1],
      ['B', 'D', 5], ['C', 'D', 8], ['C', 'E', 10],
      ['D', 'E', 2], ['D', 'F', 6], ['E', 'F', 3]
    ]
    
    weighted_edges.each { |v1, v2, weight| graph.add_edge(v1, v2, weight) }
    
    graph.display
    
    # Find MST
    start_vertex = 'A'
    mst = minimum_spanning_tree(graph, start_vertex)
    
    puts "\nMinimum Spanning Tree (starting from #{start_vertex}):"
    puts "Total weight: #{mst[:total_weight]}"
    puts "Edges:"
    mst[:edges].each do |edge|
      puts "  #{edge[:from]} -- #{edge[:to]} (weight: #{edge[:weight]})"
    end
  end
end
```

## 🔍 Cycle Detection

### 8. Cycle Detection Algorithms

Detecting cycles in graphs:

```ruby
class CycleDetection
  def self.has_cycle_undirected(graph)
    visited = Set.new
    
    graph.instance_variable_get(:@vertices).keys.each do |vertex|
      unless visited.include?(vertex)
        if dfs_cycle_undirected(graph, vertex, nil, visited)
          return true
        end
      end
    end
    
    false
  end
  
  def self.dfs_cycle_undirected(graph, current, parent, visited)
    visited.add(current)
    
    graph.get_neighbors(current).each do |neighbor|
      unless visited.include?(neighbor)
        if dfs_cycle_undirected(graph, neighbor, current, visited)
          return true
        end
      elsif neighbor != parent
        return true # Found a back edge
      end
    end
    
    false
  end
  
  def self.has_cycle_directed(graph)
    visited = Set.new
    recursion_stack = Set.new
    
    graph.instance_variable_get(:@vertices).keys.each do |vertex|
      unless visited.include?(vertex)
        if dfs_cycle_directed(graph, vertex, visited, recursion_stack)
          return true
        end
      end
    end
    
    false
  end
  
  def self.dfs_cycle_directed(graph, current, visited, recursion_stack)
    visited.add(current)
    recursion_stack.add(current)
    
    graph.get_neighbors(current).each do |neighbor|
      unless visited.include?(neighbor)
        if dfs_cycle_directed(graph, neighbor, visited, recursion_stack)
          return true
        end
      elsif recursion_stack.include?(neighbor)
        return true # Found a back edge
      end
    end
    
    recursion_stack.delete(current)
    false
  end
  
  def self.find_cycle(graph)
    visited = Set.new
    parent = {}
    
    graph.instance_variable_get(:@vertices).keys.each do |vertex|
      unless visited.include?(vertex)
        cycle = dfs_find_cycle(graph, vertex, nil, visited, parent)
        return cycle if cycle
      end
    end
    
    []
  end
  
  def self.dfs_find_cycle(graph, current, parent, visited, parent_map)
    visited.add(current)
    parent_map[current] = parent
    
    graph.get_neighbors(current).each do |neighbor|
      unless visited.include?(neighbor)
        cycle = dfs_find_cycle(graph, neighbor, current, visited, parent_map)
        return cycle if cycle.any?
      elsif neighbor != parent
        # Found a cycle, reconstruct it
        cycle = [neighbor]
        node = current
        
        while node && node != neighbor
          cycle.unshift(node)
          node = parent_map[node]
        end
        
        cycle.unshift(neighbor) if node == neighbor
        return cycle
      end
    end
    
    []
  end
  
  def self.demonstrate_cycle_detection
    puts "Cycle Detection Demonstration:"
    puts "=" * 50
    
    # Graph with cycle
    graph_with_cycle = AdjacencyListGraph.new
    cycle_edges = [['A', 'B'], ['B', 'C'], ['C', 'D'], ['D', 'A']]
    cycle_edges.each { |v1, v2| graph_with_cycle.add_edge(v1, v2) }
    
    puts "Graph with cycle:"
    graph_with_cycle.display
    
    has_cycle = has_cycle_undirected(graph_with_cycle)
    puts "Has cycle: #{has_cycle}"
    
    cycle = find_cycle(graph_with_cycle)
    puts "Cycle found: #{cycle.join(' -> ')}" if cycle.any?
    
    # Graph without cycle
    puts "\nGraph without cycle:"
    graph_without_cycle = AdjacencyListGraph.new
    acyclic_edges = [['A', 'B'], ['B', 'C'], ['C', 'D'], ['D', 'E']]
    acyclic_edges.each { |v1, v2| graph_without_cycle.add_edge(v1, v2) }
    
    graph_without_cycle.display
    
    has_cycle = has_cycle_undirected(graph_without_cycle)
    puts "Has cycle: #{has_cycle}"
  end
end
```

## 📊 Performance Analysis

### Algorithm Performance Comparison

```ruby
class GraphAlgorithmPerformance
  def self.compare_traversal_algorithms
    puts "Graph Traversal Algorithm Performance:"
    puts "=" * 60
    
    # Create test graphs of different sizes
    [10, 100, 1000].each do |size|
      puts "\nGraph size: #{size} vertices"
      puts "-" * 40
      
      graph = create_random_graph(size)
      start_vertex = graph.instance_variable_get(:@vertices).keys.first
      
      # DFS
      dfs_time = benchmark do
        DepthFirstSearch.dfs_iterative(graph, start_vertex)
      end
      
      # BFS
      bfs_time = benchmark do
        BreadthFirstSearch.bfs(graph, start_vertex)
      end
      
      puts "DFS:  #{dfs_time.round(4)}ms"
      puts "BFS:  #{bfs_time.round(4)}ms"
    end
  end
  
  def self.compare_shortest_path_algorithms
    puts "\nShortest Path Algorithm Performance:"
    puts "=" * 60
    
    # Create test graphs of different sizes
    [10, 50, 100].each do |size|
      puts "\nGraph size: #{size} vertices"
      puts "-" * 40
      
      graph = create_random_weighted_graph(size)
      vertices = graph.instance_variable_get(:@vertices).keys
      start_vertex = vertices.first
      end_vertex = vertices.last
      
      # Dijkstra
      dijkstra_time = benchmark do
        DijkstraAlgorithm.shortest_path(graph, start_vertex, end_vertex)
      end
      
      puts "Dijkstra: #{dijkstra_time.round(4)}ms"
    end
  end
  
  def self.create_random_graph(size)
    graph = AdjacencyListGraph.new
    
    # Add vertices
    (1..size).each { |i| graph.add_vertex("V#{i}") }
    
    # Add random edges
    vertices = graph.instance_variable_get(:@vertices).keys
    edges = (size * 2).times do
      v1 = vertices.sample
      v2 = vertices.sample
      graph.add_edge(v1, v2) if v1 != v2
    end
    
    graph
  end
  
  def self.create_random_weighted_graph(size)
    graph = AdjacencyListGraph.new
    
    # Add vertices
    (1..size).each { |i| graph.add_vertex("V#{i}") }
    
    # Add random weighted edges
    vertices = graph.instance_variable_get(:@vertices).keys
    edges = (size * 2).times do
      v1 = vertices.sample
      v2 = vertices.sample
      weight = rand(1..20)
      graph.add_edge(v1, v2, weight) if v1 != v2
    end
    
    graph
  end
  
  def self.benchmark
    start_time = Time.now
    yield
    end_time = Time.now
    
    (end_time - start_time) * 1000 # Convert to milliseconds
  end
end
```

## 🎯 Practical Applications

### Real-World Graph Algorithm Examples

```ruby
class GraphApplications
  def self.social_network_analysis
    puts "Social Network Analysis:"
    puts "=" * 40
    
    # Create social network graph
    network = AdjacencyListGraph.new
    
    # Add connections (friendships)
    connections = [
      ['Alice', 'Bob'], ['Alice', 'Charlie'], ['Bob', 'David'],
      ['Charlie', 'David'], ['Charlie', 'Eve'], ['David', 'Frank'],
      ['Eve', 'Frank'], ['Frank', 'Grace'], ['Grace', 'Henry']
    ]
    
    connections.each { |person1, person2| network.add_edge(person1, person2) }
    
    network.display
    
    start_person = 'Alice'
    
    # Find friends within 2 degrees
    friends_within_2 = []
    visited = Set.new([start_person])
    queue = [[start_person, 0]]
    
    while queue.any?
      current, level = queue.shift
      
      if level <= 2
        friends_within_2 << current if level > 0
        
        network.get_neighbors(current).each do |friend|
          unless visited.include?(friend)
            visited.add(friend)
            queue << [friend, level + 1]
          end
        end
      end
    end
    
    puts "Friends within 2 degrees of #{start_person}: #{friends_within_2.join(', ')}"
    
    # Find shortest connection between two people
    person1 = 'Alice'
    person2 = 'Frank'
    path = BreadthFirstSearch.shortest_path(network, person1, person2)
    
    puts "Connection between #{person1} and #{person2}: #{path.join(' -> ')}"
  end
  
  def self.route_planning
    puts "\nRoute Planning:"
    puts "=" * 40
    
    # Create city map graph
    cities = AdjacencyListGraph.new
    
    # Add cities and distances
    routes = [
      ['New York', 'Boston', 215], ['New York', 'Philadelphia', 95],
      ['Boston', 'Philadelphia', 310], ['Philadelphia', 'Washington', 135],
      ['Washington', 'Richmond', 105], ['Richmond', 'Charlotte', 250],
      ['Charlotte', 'Atlanta', 240], ['Atlanta', 'Miami', 660]
    ]
    
    routes.each { |city1, city2, distance| cities.add_edge(city1, city2, distance) }
    
    cities.display
    
    start_city = 'New York'
    end_city = 'Miami'
    
    # Find shortest route
    route = DijkstraAlgorithm.shortest_path(cities, start_city, end_city)
    
    puts "\nShortest route from #{start_city} to #{end_city}:"
    puts "Distance: #{route[:distance]} miles"
    puts "Path: #{route[:path].join(' -> ')}"
  end
  
  def self.dependency_resolution
    puts "\nDependency Resolution:"
    puts "=" * 40
    
    # Create dependency graph
    dependencies = AdjacencyListGraph.new
    
    # Add dependencies (A depends on B means B -> A)
    deps = [
      ['App', 'Database'], ['App', 'API'], ['API', 'Auth'],
      ['API', 'Cache'], ['Auth', 'Database'], ['Cache', 'Database']
    ]
    
    deps.each { |component, dependency| dependencies.add_edge(dependency, component) }
    
    dependencies.display
    
    # Check for circular dependencies
    has_cycle = CycleDetection.has_cycle_directed(dependencies)
    puts "Circular dependencies: #{has_cycle ? 'Yes' : 'No'}"
    
    # Topological sort (simplified)
    order = []
    visited = Set.new
    
    def self.topological_sort_util(graph, vertex, visited, order)
      visited.add(vertex)
      
      graph.get_neighbors(vertex).each do |neighbor|
        unless visited.include?(neighbor)
          topological_sort_util(graph, neighbor, visited, order)
        end
      end
      
      order.unshift(vertex)
    end
    
    dependencies.instance_variable_get(:@vertices).keys.each do |vertex|
      unless visited.include?(vertex)
        topological_sort_util(dependencies, vertex, visited, order)
      end
    end
    
    puts "Dependency order: #{order.join(' -> ')}"
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Graph Representation**: Implement adjacency list and matrix
2. **DFS Traversal**: Implement depth-first search
3. **BFS Traversal**: Implement breadth-first search

### Intermediate Exercises

1. **Shortest Path**: Implement Dijkstra's algorithm
2. **MST**: Implement Kruskal's algorithm
3. **Cycle Detection**: Implement cycle detection

### Advanced Exercises

1. **Advanced Algorithms**: Implement Bellman-Ford and Prim's
2. **Performance Optimization**: Optimize graph algorithms
3. **Real Applications**: Build practical graph applications

---

## 🎯 Summary

Graph algorithms in Ruby provide:

- **Graph Representation** - Adjacency list and matrix
- **Traversal Algorithms** - DFS and BFS
- **Shortest Path** - Dijkstra and Bellman-Ford
- **Minimum Spanning Tree** - Kruskal and Prim's
- **Cycle Detection** - Detect cycles in graphs
- **Performance Analysis** - Algorithm comparison
- **Practical Applications** - Real-world use cases

Master these algorithms to solve complex relationship problems!
