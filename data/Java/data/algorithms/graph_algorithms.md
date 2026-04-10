# Graph Algorithms - Complete Guide

This guide covers graph algorithms from basic to advanced, with implementations, analysis, and real-world applications.

## 📚 Table of Contents

1. [Introduction to Graphs](#introduction-to-graphs)
2. [Graph Representations](#graph-representations)
3. [Traversal Algorithms](#traversal-algorithms)
4. [Shortest Path Algorithms](#shortest-path-algorithms)
5. [Minimum Spanning Tree](#minimum-spanning-tree)
6. [Network Flow Algorithms](#network-flow-algorithms)
7. [Advanced Graph Algorithms](#advanced-graph-algorithms)
8. [Performance Analysis](#performance-analysis)

---

## Introduction to Graphs

### What is a Graph?
A graph is a data structure consisting of vertices (nodes) connected by edges (links).

### Graph Components
- **Vertices (Nodes)**: Entities or points in the graph
- **Edges (Links)**: Connections between vertices
- **Weight**: Cost or distance associated with edges
- **Direction**: Whether edges are one-way or two-way

### Graph Types
- **Undirected**: Edges have no direction (friendship network)
- **Directed**: Edges have direction (web links, road network)
- **Weighted**: Edges have costs (distances, time)
- **Unweighted**: All edges have equal cost

### Applications
- **Social Networks**: Friend relationships, influence
- **Transportation**: Road networks, flight paths
- **Computer Networks**: Network topology, routing
- **Recommendation Systems**: Product recommendations
- **Bioinformatics**: Protein interactions, evolutionary trees

---

## Graph Representations

### Adjacency Matrix

#### Concept
2D array where matrix[i][j] represents edge from vertex i to vertex j.

#### Implementation
```java
class AdjacencyMatrix:
    """Adjacency matrix representation"""
    
    def __init__(self, num_vertices, directed=False):
        self.num_vertices = num_vertices
        self.directed = directed
        self.matrix = [[0] * num_vertices for _ in range(num_vertices)]
    
    def add_edge(self, u, v, weight=1):
        """Add edge from u to v"""
        self.matrix[u][v] = weight
        if not self.directed:
            self.matrix[v][u] = weight
    
    def remove_edge(self, u, v):
        """Remove edge from u to v"""
        self.matrix[u][v] = 0
        if not self.directed:
            self.matrix[v][u] = 0
    
    def has_edge(self, u, v):
        """Check if edge exists from u to v"""
        return self.matrix[u][v] != 0
    
    def get_neighbors(self, u):
        """Get all neighbors of vertex u"""
        neighbors = []
        for v in range(self.num_vertices):
            if self.matrix[u][v] != 0:
                neighbors.append((v, self.matrix[u][v]))
        return neighbors
    
    def display(self):
        """Display the adjacency matrix"""
        for row in self.matrix:
            print(' '.join(map(str, row)))

# Example usage
graph = AdjacencyMatrix(5)  # 5 vertices
graph.add_edge(0, 1, 2)
graph.add_edge(0, 2, 3)
graph.add_edge(1, 3, 1)
graph.add_edge(2, 4, 4)
graph.add_edge(3, 4, 5)

print("Adjacency Matrix:")
graph.display()

print("\nNeighbors of vertex 0:")
print(graph.get_neighbors(0))
```

#### Analysis
- **Space Complexity**: O(V²) where V is number of vertices
- **Edge Operations**: O(1) time complexity
- **Neighbor Query**: O(V) time complexity
- **Best For**: Dense graphs (many edges)

---

### Adjacency List

#### Concept
Array of lists where each index contains list of connected vertices.

#### Implementation
```java
class AdjacencyList:
    """Adjacency list representation"""
    
    def __init__(self, num_vertices, directed=False):
        self.num_vertices = num_vertices
        self.directed = directed
        self.list = [[] for _ in range(num_vertices)]
    
    def add_edge(self, u, v, weight=1):
        """Add edge from u to v"""
        self.list[u].append((v, weight))
        if not self.directed:
            self.list[v].append((u, weight))
    
    def remove_edge(self, u, v):
        """Remove edge from u to v"""
        self.list[u] = [(x, w) for x, w in self.list[u] if x != v]
        if not self.directed:
            self.list[v] = [(x, w) for x, w in self.list[v] if x != u]
    
    def has_edge(self, u, v):
        """Check if edge exists from u to v"""
        return any(x == v for x, _ in self.list[u])
    
    def get_neighbors(self, u):
        """Get all neighbors of vertex u"""
        return self.list[u]
    
    def display(self):
        """Display the adjacency list"""
        for i, neighbors in enumerate(self.list):
            print(f"{i}: {neighbors}")

# Example usage
graph = AdjacencyList(5)
graph.add_edge(0, 1, 2)
graph.add_edge(0, 2, 3)
graph.add_edge(1, 3, 1)
graph.add_edge(2, 4, 4)
graph.add_edge(3, 4, 5)

print("Adjacency List:")
graph.display()
```

#### Analysis
- **Space Complexity**: O(V + E) where E is number of edges
- **Edge Operations**: O(1) average, O(degree) worst case
- **Neighbor Query**: O(degree) time complexity
- **Best For**: Sparse graphs (few edges)

---

## Traversal Algorithms

### Breadth-First Search (BFS)

#### Concept
Level-by-level traversal that explores all neighbors at current depth before moving to next depth.

#### Implementation
```java
from collections import deque

class GraphTraversal:
    """Graph traversal algorithms"""
    
    @staticmethod
    def bfs(graph, start, adjacency_list=True):
        """Breadth-First Search traversal"""
        visited = set()
        queue = deque([start])
        traversal_order = []
        
        while queue:
            vertex = queue.popleft()
            
            if vertex not in visited:
                visited.add(vertex)
                traversal_order.append(vertex)
                
                # Get neighbors based on graph representation
                if adjacency_list:
                    neighbors = graph.get_neighbors(vertex)
                    neighbors = [v for v, _ in neighbors]
                else:
                    neighbors = [v for v in range(len(graph.matrix)) 
                               if graph.has_edge(vertex, v)]
                
                for neighbor in neighbors:
                    if neighbor not in visited:
                        queue.append(neighbor)
        
        return traversal_order, visited
    
    @staticmethod
    def dfs(graph, start, adjacency_list=True):
        """Depth-First Search traversal"""
        visited = set()
        stack = [start]
        traversal_order = []
        
        while stack:
            vertex = stack.pop()
            
            if vertex not in visited:
                visited.add(vertex)
                traversal_order.append(vertex)
                
                # Get neighbors
                if adjacency_list:
                    neighbors = graph.get_neighbors(vertex)
                    neighbors = [v for v, _ in neighbors][::-1]  # Reverse for consistent order
                else:
                    neighbors = [v for v in range(len(graph.matrix)) 
                               if graph.has_edge(vertex, v)][::-1]
                
                for neighbor in neighbors:
                    if neighbor not in visited:
                        stack.append(neighbor)
        
        return traversal_order, visited

# Example usage
graph = AdjacencyList(6)
edges = [
    (0, 1), (0, 2), (1, 3), (2, 4),
    (3, 4), (3, 5), (4, 5)
]

for u, v in edges:
    graph.add_edge(u, v)

print("BFS traversal starting from vertex 0:")
bfs_order, bfs_visited = GraphTraversal.bfs(graph, 0)
print(f"Order: {bfs_order}")
print(f"Visited: {bfs_visited}")

print("\nDFS traversal starting from vertex 0:")
dfs_order, dfs_visited = GraphTraversal.dfs(graph, 0)
print(f"Order: {dfs_order}")
print(f"Visited: {dfs_visited}")
```

#### Analysis
- **Time Complexity**: O(V + E)
- **Space Complexity**: O(V) for visited set and queue/stack
- **BFS Properties**: Finds shortest path in unweighted graphs
- **DFS Properties**: Good for exploring all paths, memory efficient

---

### Connected Components

#### Concept
Find all connected components in an undirected graph.

#### Implementation
```java
class ConnectedComponents:
    """Find connected components in graph"""
    
    @staticmethod
    def find_components(graph, adjacency_list=True):
        """Find all connected components using DFS"""
        visited = set()
        components = []
        
        for vertex in range(graph.num_vertices):
            if vertex not in visited:
                # Start new component
                component = []
                stack = [vertex]
                
                while stack:
                    current = stack.pop()
                    
                    if current not in visited:
                        visited.add(current)
                        component.append(current)
                        
                        # Add neighbors
                        if adjacency_list:
                            neighbors = graph.get_neighbors(current)
                            neighbors = [v for v, _ in neighbors]
                        else:
                            neighbors = [v for v in range(graph.num_vertices) 
                                       if graph.has_edge(current, v)]
                        
                        for neighbor in neighbors:
                            if neighbor not in visited:
                                stack.append(neighbor)
                
                components.append(component)
        
        return components

# Example usage
graph = AdjacencyList(8)
edges = [
    (0, 1), (1, 2), (2, 0),  # Component 1
    (3, 4), (4, 5), (5, 3),  # Component 2
    (6, 7)  # Component 3
]

for u, v in edges:
    graph.add_edge(u, v)

components = ConnectedComponents.find_components(graph)
print(f"Connected components: {components}")
print(f"Number of components: {len(components)}")
```

---

## Shortest Path Algorithms

### Dijkstra's Algorithm

#### Concept
Finds shortest path from source to all other vertices in weighted graph with non-negative weights.

#### Implementation
```java
import heapq

class ShortestPath:
    """Shortest path algorithms"""
    
    @staticmethod
    def dijkstra(graph, source, adjacency_list=True):
        """Dijkstra's algorithm implementation"""
        distances = {i: float('infinity') for i in range(graph.num_vertices)}
        distances[source] = 0
        previous = {i: None for i in range(graph.num_vertices)}
        visited = set()
        
        # Priority queue of (distance, vertex)
        priority_queue = [(0, source)]
        
        while priority_queue:
            current_distance, current_vertex = heapq.heappop(priority_queue)
            
            if current_vertex in visited:
                continue
            
            visited.add(current_vertex)
            
            # Check all neighbors
            if adjacency_list:
                neighbors = graph.get_neighbors(current_vertex)
            else:
                neighbors = [(v, graph.matrix[current_vertex][v]) 
                          for v in range(graph.num_vertices) 
                          if graph.has_edge(current_vertex, v)]
            
            for neighbor, weight in neighbors:
                if neighbor in visited:
                    continue
                
                distance = current_distance + weight
                
                if distance < distances[neighbor]:
                    distances[neighbor] = distance
                    previous[neighbor] = current_vertex
                    heapq.heappush(priority_queue, (distance, neighbor))
        
        return distances, previous
    
    @staticmethod
    def reconstruct_path(previous, target):
        """Reconstruct path from previous array"""
        path = []
        current = target
        
        while current is not None:
            path.insert(0, current)
            current = previous[current]
        
        return path

# Example usage
graph = AdjacencyList(6)
weighted_edges = [
    (0, 1, 7), (0, 2, 9), (0, 5, 14),
    (1, 2, 10), (1, 3, 15),
    (2, 3, 11), (2, 5, 2),
    (3, 4, 6),
    (4, 5, 9),
    (5, 1, 9)  # Adding a back edge
]

for u, v, w in weighted_edges:
    graph.add_edge(u, v, w)

source = 0
distances, previous = ShortestPath.dijkstra(graph, source)

print(f"Shortest distances from vertex {source}:")
for vertex, distance in distances.items():
    if distance != float('infinity'):
        print(f"  To {vertex}: {distance}")
    else:
        print(f"  To {vertex}: Unreachable")

# Reconstruct path to a specific target
target = 4
path = ShortestPath.reconstruct_path(previous, target)
print(f"\nShortest path from {source} to {target}: {' -> '.join(map(str, path))}")
```

---

### Bellman-Ford Algorithm

#### Concept
Finds shortest paths from source to all vertices, handles negative edge weights.

#### Implementation
```java
class BellmanFord:
    """Bellman-Ford algorithm for graphs with negative weights"""
    
    @staticmethod
    def bellman_ford(graph, source, adjacency_list=True):
        """Bellman-Ford algorithm implementation"""
        distances = {i: float('infinity') for i in range(graph.num_vertices)}
        distances[source] = 0
        previous = {i: None for i in range(graph.num_vertices)}
        
        # Relax edges V-1 times
        for _ in range(graph.num_vertices - 1):
            updated = False
            
            for u in range(graph.num_vertices):
                if adjacency_list:
                    neighbors = graph.get_neighbors(u)
                else:
                    neighbors = [(v, graph.matrix[u][v]) 
                              for v in range(graph.num_vertices) 
                              if graph.has_edge(u, v)]
                
                for v, weight in neighbors:
                    if distances[u] + weight < distances[v]:
                        distances[v] = distances[u] + weight
                        previous[v] = u
                        updated = True
            
            if not updated:
                break
        
        # Check for negative cycles
        for u in range(graph.num_vertices):
            if adjacency_list:
                neighbors = graph.get_neighbors(u)
            else:
                neighbors = [(v, graph.matrix[u][v]) 
                          for v in range(graph.num_vertices) 
                          if graph.has_edge(u, v)]
            
            for v, weight in neighbors:
                if distances[u] + weight < distances[v]:
                    return None, "Negative weight cycle detected"
        
        return distances, previous

# Example usage with negative weights
graph = AdjacencyList(5)
edges_with_negative = [
    (0, 1, 6), (0, 2, 7),
    (1, 2, 8), (1, 3, 5), (1, 4, -4),
    (2, 3, -3), (2, 4, 9),
    (3, 1, -2), (3, 4, 7),
    (4, 0, 2)
]

for u, v, w in edges_with_negative:
    graph.add_edge(u, v, w)

source = 0
result = BellmanFord.bellman_ford(graph, source)

if result[0] is None:
    print(result[1])  # Error message
else:
    distances, previous = result
    print(f"Shortest distances from vertex {source}:")
    for vertex, distance in distances.items():
        if distance != float('infinity'):
            print(f"  To {vertex}: {distance}")
```

---

### Floyd-Warshall Algorithm

#### Concept
Finds shortest paths between all pairs of vertices using dynamic programming.

#### Implementation
```java
class FloydWarshall:
    """Floyd-Warshall algorithm for all-pairs shortest paths"""
    
    @staticmethod
    def floyd_warshall(graph, adjacency_list=True):
        """Floyd-Warshall algorithm implementation"""
        # Initialize distance matrix
        if adjacency_list:
            # Convert adjacency list to matrix
            num_vertices = graph.num_vertices
            distance = [[float('infinity')] * num_vertices for _ in range(num_vertices)]
            next_vertex = [[None] * num_vertices for _ in range(num_vertices)]
            
            for u in range(num_vertices):
                distance[u][u] = 0
                next_vertex[u][u] = u
                
                for v, weight in graph.get_neighbors(u):
                    distance[u][v] = weight
                    next_vertex[u][v] = v
        else:
            distance = [row[:] for row in graph.matrix]
            next_vertex = [[None if graph.matrix[i][j] == 0 else j 
                          for j in range(graph.num_vertices)] 
                         for i in range(graph.num_vertices)]
            for i in range(graph.num_vertices):
                next_vertex[i][i] = i
                distance[i][i] = 0
        
        num_vertices = len(distance)
        
        # Main algorithm
        for k in range(num_vertices):
            for i in range(num_vertices):
                for j in range(num_vertices):
                    if distance[i][k] + distance[k][j] < distance[i][j]:
                        distance[i][j] = distance[i][k] + distance[k][j]
                        next_vertex[i][j] = next_vertex[i][k]
        
        return distance, next_vertex
    
    @staticmethod
    def reconstruct_path(next_vertex, i, j):
        """Reconstruct path using next matrix"""
        if next_vertex[i][j] is None:
            return []
        
        path = [i]
        while i != j:
            i = next_vertex[i][j]
            path.append(i)
        
        return path

# Example usage
graph = AdjacencyList(4)
edges = [
    (0, 1, 5), (0, 3, 10),
    (1, 2, 3),
    (2, 3, 1)
]

for u, v, w in edges:
    graph.add_edge(u, v, w)

distance, next_vertex = FloydWarshall.floyd_warshall(graph)

print("All-pairs shortest distances:")
for i in range(4):
    for j in range(4):
        if distance[i][j] != float('infinity'):
            print(f"  {i} -> {j}: {distance[i][j]}")

print("\nPath from 0 to 3:")
path = FloydWarshall.reconstruct_path(next_vertex, 0, 3)
print(f"  {' -> '.join(map(str, path))}")
```

---

## Minimum Spanning Tree

### Prim's Algorithm

#### Concept
Greedy algorithm that builds MST by adding minimum weight edges to growing tree.

#### Implementation
```java
class MinimumSpanningTree:
    """Minimum Spanning Tree algorithms"""
    
    @staticmethod
    def prim(graph, adjacency_list=True):
        """Prim's algorithm for MST"""
        if adjacency_list:
            num_vertices = graph.num_vertices
            # Convert to adjacency matrix for easier manipulation
            adj_matrix = [[float('infinity')] * num_vertices for _ in range(num_vertices)]
            for u in range(num_vertices):
                adj_matrix[u][u] = 0
                for v, weight in graph.get_neighbors(u):
                    adj_matrix[u][v] = weight
                    adj_matrix[v][u] = weight
        else:
            adj_matrix = graph.matrix
            num_vertices = len(adj_matrix)
        
        mst_edges = []
        selected = [False] * num_vertices
        min_edge = [0, 0, float('infinity')]  # [u, v, weight]
        
        # Start with vertex 0
        selected[0] = True
        
        for _ in range(num_vertices - 1):
            # Find minimum edge connecting selected to unselected
            min_edge = [0, 0, float('infinity')]
            
            for u in range(num_vertices):
                if selected[u]:
                    for v in range(num_vertices):
                        if not selected[v] and adj_matrix[u][v] < min_edge[2]:
                            min_edge = [u, v, adj_matrix[u][v]]
            
            # Add edge to MST
            mst_edges.append(min_edge)
            selected[min_edge[1]] = True
        
        return mst_edges

# Example usage
graph = AdjacencyList(4)
edges = [
    (0, 1, 1), (0, 2, 3), (0, 3, 4),
    (1, 2, 2), (1, 3, 5),
    (2, 3, 6)
]

for u, v, w in edges:
    graph.add_edge(u, v, w)

mst = MinimumSpanningTree.prim(graph)
total_weight = sum(edge[2] for edge in mst)

print("Minimum Spanning Tree edges:")
for edge in mst:
    print(f"  {edge[0]} -- {edge[1]} (weight: {edge[2]})")

print(f"Total weight: {total_weight}")
```

---

### Kruskal's Algorithm

#### Concept
Greedy algorithm that sorts all edges by weight and adds them to MST if they don't create cycles.

#### Implementation
```java
class KruskalMST:
    """Kruskal's algorithm for MST with Union-Find"""
    
    class UnionFind:
        """Union-Find data structure"""
        
        def __init__(self, size):
            self.parent = list(range(size))
            self.rank = [0] * size
        
        def find(self, x):
            """Find with path compression"""
            if self.parent[x] != x:
                self.parent[x] = self.find(self.parent[x])
            return self.parent[x]
        
        def union(self, x, y):
            """Union by rank"""
            x_root = self.find(x)
            y_root = self.find(y)
            
            if x_root == y_root:
                return
            
            if self.rank[x_root] < self.rank[y_root]:
                self.parent[x_root] = y_root
            elif self.rank[x_root] > self.rank[y_root]:
                self.parent[y_root] = x_root
            else:
                self.parent[y_root] = x_root
                self.rank[x_root] += 1
    
    @staticmethod
    def kruskal(graph, adjacency_list=True):
        """Kruskal's algorithm for MST"""
        if adjacency_list:
            # Collect all edges
            edges = []
            for u in range(graph.num_vertices):
                for v, weight in graph.get_neighbors(u):
                    if u < v:  # Avoid duplicate edges
                        edges.append((u, v, weight))
        else:
            edges = []
            for u in range(graph.num_vertices):
                for v in range(graph.num_vertices):
                    if u < v and graph.has_edge(u, v):
                        edges.append((u, v, graph.matrix[u][v]))
        
        # Sort edges by weight
        edges.sort(key=lambda x: x[2])
        
        uf = KruskalMST.UnionFind(graph.num_vertices)
        mst_edges = []
        
        for u, v, weight in edges:
            # Check if adding edge creates cycle
            if uf.find(u) != uf.find(v):
                uf.union(u, v)
                mst_edges.append((u, v, weight))
                
                # Stop when we have V-1 edges
                if len(mst_edges) == graph.num_vertices - 1:
                    break
        
        return mst_edges

# Example usage
graph = AdjacencyList(4)
edges = [
    (0, 1, 1), (0, 2, 3), (0, 3, 4),
    (1, 2, 2), (1, 3, 5),
    (2, 3, 6)
]

for u, v, w in edges:
    graph.add_edge(u, v, w)

mst = KruskalMST.kruskal(graph)
total_weight = sum(edge[2] for edge in mst)

print("Minimum Spanning Tree edges (Kruskal):")
for edge in mst:
    print(f"  {edge[0]} -- {edge[1]} (weight: {edge[2]})")

print(f"Total weight: {total_weight}")
```

---

## Network Flow Algorithms

### Ford-Fulkerson Algorithm

#### Concept
Finds maximum flow in a flow network using augmenting paths.

#### Implementation
```java
class NetworkFlow:
    """Network flow algorithms"""
    
    @staticmethod
    def ford_fulkerson(capacity, source, sink):
        """Ford-Fulkerson algorithm for maximum flow"""
        n = len(capacity)
        flow = [[0] * n for _ in range(n)]
        parent = [-1] * n
        max_flow = 0
        
        def bfs_find_path():
            """Find augmenting path using BFS"""
            visited = [False] * n
            queue = [source]
            visited[source] = True
            
            while queue:
                u = queue.pop(0)
                
                for v in range(n):
                    if not visited[v] and capacity[u][v] - flow[u][v] > 0:
                        queue.append(v)
                        visited[v] = True
                        parent[v] = u
            
            return visited[sink]
        
        # Main algorithm
        while bfs_find_path():
            # Find minimum residual capacity along the path
            path_flow = float('infinity')
            v = sink
            
            while v != source:
                u = parent[v]
                path_flow = min(path_flow, capacity[u][v] - flow[u][v])
                v = u
            
            # Add flow to the path
            v = sink
            while v != source:
                u = parent[v]
                flow[u][v] += path_flow
                flow[v][u] -= path_flow  # Residual flow
                v = u
            
            max_flow += path_flow
        
        return max_flow, flow

# Example usage
capacity = [
    [0, 16, 13, 0, 0, 0],
    [0, 0, 10, 12, 0, 0],
    [0, 4, 0, 14, 0, 0],
    [0, 0, 0, 0, 0, 0],
    [0, 0, 9, 0, 20, 0],
    [0, 0, 0, 7, 0, 4],
    [0, 0, 0, 0, 0, 0]
]

source = 0
sink = 5

max_flow, flow_matrix = NetworkFlow.ford_fulkerson(capacity, source, sink)

print(f"Maximum flow from {source} to {sink}: {max_flow}")

print("\nFlow matrix:")
for row in flow_matrix:
    print(' '.join(map(str, row)))
```

---

## Advanced Graph Algorithms

### Topological Sort

#### Concept
Linear ordering of vertices in directed acyclic graph (DAG).

#### Implementation
```java
class TopologicalSort:
    """Topological sorting algorithms"""
    
    @staticmethod
    def kahn_algorithm(graph, adjacency_list=True):
        """Kahn's algorithm for topological sort"""
        if adjacency_list:
            # Calculate in-degrees
            in_degree = [0] * graph.num_vertices
            for u in range(graph.num_vertices):
                for v, _ in graph.get_neighbors(u):
                    in_degree[v] += 1
        else:
            in_degree = [0] * graph.num_vertices
            for u in range(graph.num_vertices):
                for v in range(graph.num_vertices):
                    if graph.has_edge(u, v):
                        in_degree[v] += 1
        
        # Queue of vertices with no incoming edges
        queue = [i for i in range(graph.num_vertices) if in_degree[i] == 0]
        topological_order = []
        
        while queue:
            u = queue.pop(0)
            topological_order.append(u)
            
            # Remove u's outgoing edges
            if adjacency_list:
                for v, _ in graph.get_neighbors(u):
                    in_degree[v] -= 1
                    if in_degree[v] == 0:
                        queue.append(v)
            else:
                for v in range(graph.num_vertices):
                    if graph.has_edge(u, v):
                        in_degree[v] -= 1
                        if in_degree[v] == 0:
                            queue.append(v)
        
        # Check for cycle
        if len(topological_order) != graph.num_vertices:
            return None, "Graph has a cycle"
        
        return topological_order, None

# Example usage - task scheduling
graph = AdjacencyList(6, directed=True)
dependencies = [
    (5, 2), (5, 0), (4, 0), (4, 1),
    (2, 3), (3, 1)
]

for u, v in dependencies:
    graph.add_edge(u, v)

result = TopologicalSort.kahn_algorithm(graph)

if result[1]:
    print("Topological order:")
    print(" -> ".join(map(str, result[0])))
else:
    print(result[1])
```

---

### Strongly Connected Components

#### Concept
Find maximal sets of vertices where each vertex is reachable from every other vertex in the set.

#### Implementation
```java
class StronglyConnectedComponents:
    """Kosaraju's algorithm for SCCs"""
    
    @staticmethod
    def kosaraju(graph, adjacency_list=True):
        """Kosaraju's algorithm for finding SCCs"""
        def dfs_first_pass(v):
            """First DFS to fill stack"""
            visited[v] = True
            if adjacency_list:
                neighbors = graph.get_neighbors(v)
                neighbors = [u for u, _ in neighbors]
            else:
                neighbors = [u for u in range(graph.num_vertices) 
                           if graph.has_edge(v, u)]
            
            for u in neighbors:
                if not visited[u]:
                    dfs_first_pass(u)
            stack.append(v)
        
        def dfs_second_pass(v, component):
            """Second DFS on transposed graph"""
            visited[v] = True
            component.append(v)
            
            if adjacency_list:
                neighbors = graph.get_neighbors(v)
                neighbors = [u for u, _ in neighbors]
            else:
                neighbors = [u for u in range(graph.num_vertices) 
                           if graph.has_edge(v, u)]
            
            for u in neighbors:
                if not visited[u]:
                    dfs_second_pass(u, component)
        
        # First pass
        visited = [False] * graph.num_vertices
        stack = []
        
        for v in range(graph.num_vertices):
            if not visited[v]:
                dfs_first_pass(v)
        
        # Create transposed graph
        transposed = AdjacencyList(graph.num_vertices, directed=True)
        if adjacency_list:
            for u in range(graph.num_vertices):
                for v, _ in graph.get_neighbors(u):
                    transposed.add_edge(v, u)
        else:
            for u in range(graph.num_vertices):
                for v in range(graph.num_vertices):
                    if graph.has_edge(u, v):
                        transposed.add_edge(v, u)
        
        # Second pass
        visited = [False] * graph.num_vertices
        sccs = []
        
        while stack:
            v = stack.pop()
            if not visited[v]:
                component = []
                dfs_second_pass(v, component)
                sccs.append(component)
        
        return sccs

# Example usage
graph = AdjacencyList(5, directed=True)
edges = [
    (0, 2), (2, 1), (1, 0),  # SCC 1
    (3, 4)  # SCC 2
]

for u, v in edges:
    graph.add_edge(u, v)

sccs = StronglyConnectedComponents.kosaraju(graph)

print("Strongly Connected Components:")
for i, scc in enumerate(sccs, 1):
    print(f"  Component {i}: {scc}")
```

---

## Performance Analysis

### Time Complexity Comparison

| Algorithm | Time Complexity | Space Complexity | Best Use Case |
|------------|------------------|------------------|----------------|
| BFS | O(V + E) | O(V) | Shortest path, unweighted |
| DFS | O(V + E) | O(V) | Connectivity, path existence |
| Dijkstra | O((V + E) log V) | O(V) | Shortest path, non-negative weights |
| Bellman-Ford | O(V × E) | O(V) | Negative weights, cycle detection |
| Floyd-Warshall | O(V³) | O(V²) | All-pairs shortest paths |
| Prim's MST | O(E log V) | O(V) | Minimum spanning tree |
| Kruskal's MST | O(E log E) | O(V) | Minimum spanning tree |
| Topological Sort | O(V + E) | O(V) | DAG ordering |
| Kosaraju's SCC | O(V + E) | O(V) | Strongly connected components |

### Memory Usage Patterns

#### Graph Size Impact
- **Sparse Graphs**: E ≈ V, adjacency lists preferred
- **Dense Graphs**: E ≈ V², adjacency matrices preferred
- **Memory Trade-offs**: Time vs space complexity

#### Algorithm-Specific Memory
- **BFS**: O(V) for queue + visited set
- **DFS**: O(V) for stack + visited set
- **Dijkstra**: O(V) for priority queue + distances
- **Floyd-Warshall**: O(V²) for distance matrix

---

## Practical Applications

### Social Network Analysis
```java
class SocialNetwork:
    """Social network analysis using graph algorithms"""
    
    def __init__(self):
        self.graph = AdjacencyList(0, directed=True)
        self.user_to_id = {}
        self.id_to_user = {}
        self.next_id = 0
    
    def add_user(self, username):
        """Add user to network"""
        if username not in self.user_to_id:
            user_id = self.next_id
            self.user_to_id[username] = user_id
            self.id_to_user[user_id] = username
            self.next_id += 1
            
            # Expand graph if needed
            if user_id >= self.graph.num_vertices:
                # This is simplified - in practice, use dynamic structure
                pass
    
    def add_friendship(self, user1, user2):
        """Add friendship (undirected)"""
        if user1 in self.user_to_id and user2 in self.user_to_id:
            id1 = self.user_to_id[user1]
            id2 = self.user_to_id[user2]
            self.graph.add_edge(id1, id2)
            self.graph.add_edge(id2, id1)
    
    def find_mutual_friends(self, user1, user2):
        """Find mutual friends between two users"""
        if user1 not in self.user_to_id or user2 not in self.user_to_id:
            return []
        
        id1 = self.user_to_id[user1]
        id2 = self.user_to_id[user2]
        
        friends1 = set(v for v, _ in self.graph.get_neighbors(id1))
        friends2 = set(v for v, _ in self.graph.get_neighbors(id2))
        
        mutual_ids = friends1.intersection(friends2)
        return [self.id_to_user[id] for id in mutual_ids]
    
    def shortest_connection(self, user1, user2):
        """Find shortest connection path between users"""
        if user1 not in self.user_to_id or user2 not in self.user_to_id:
            return None
        
        id1 = self.user_to_id[user1]
        id2 = self.user_to_id[user2]
        
        distances, previous = ShortestPath.dijkstra(self.graph, id1)
        
        if distances[id2] == float('infinity'):
            return None
        
        path = ShortestPath.reconstruct_path(previous, id2)
        return [self.id_to_user[id] for id in path]

# Example usage
network = SocialNetwork()
users = ["alice", "bob", "charlie", "diana", "eve"]

for user in users:
    network.add_user(user)

friendships = [
    ("alice", "bob"), ("bob", "charlie"), ("charlie", "diana"),
    ("alice", "eve"), ("eve", "diana")
]

for user1, user2 in friendships:
    network.add_friendship(user1, user2)

# Find mutual friends
mutual = network.find_mutual_friends("alice", "diana")
print(f"Mutual friends between alice and diana: {mutual}")

# Find shortest connection
path = network.shortest_connection("alice", "diana")
print(f"Shortest connection from alice to diana: {' -> '.join(path) if path else 'None'}")
```

---

## Optimization Techniques

### Algorithm Selection Guide
```java
def choose_shortest_path_algorithm(graph, has_negative_weights, all_pairs=False):
    """Choose appropriate shortest path algorithm"""
    if all_pairs:
        if graph.num_vertices <= 100:  # Small graphs
            return "Floyd-Warshall"
        else:
            return "Run Dijkstra from each vertex"
    else:
        if has_negative_weights:
            return "Bellman-Ford"
        else:
            return "Dijkstra"

def choose_graph_representation(num_vertices, num_edges, dense_threshold=0.1):
    """Choose appropriate graph representation"""
    density = num_edges / (num_vertices * (num_vertices - 1))
    
    if density > dense_threshold:
        return "Adjacency Matrix"
    else:
        return "Adjacency List"

# Example usage
print("Algorithm selection recommendations:")
print(f"  For 50 vertices, 100 edges: {choose_graph_representation(50, 100)}")
print(f"  For 50 vertices, 1000 edges: {choose_graph_representation(50, 1000)}")
print(f"  Shortest path (no negative): {choose_shortest_path_algorithm(None, False)}")
print(f"  Shortest path (with negative): {choose_shortest_path_algorithm(None, True)}")
```

---

## Exercises and Practice

### Exercise 1: Implement Missing Algorithms
1. **A* Search**: Implement with different heuristics
2. **Minimum Cut**: Implement minimum cut algorithm
3. **Eulerian Path**: Find Eulerian circuit/path
4. **Hamiltonian Cycle**: Implement backtracking solution

### Exercise 2: Optimize Existing Algorithms
1. Add early termination to Floyd-Warshall
2. Implement binary heap for Dijkstra
3. Add path compression to Union-Find
4. Optimize for sparse vs dense graphs

### Exercise 3: Real-world Applications
1. GPS navigation system
2. Social network friend suggestions
3. Network routing protocol
4. Course scheduling system

---

## Summary

Graph algorithms are essential for solving complex relationship and network problems.

### Key Takeaways
1. **Representation matters**: Choose based on graph density
2. **Algorithm selection**: Consider constraints and requirements
3. **Trade-offs**: Time vs space complexity
4. **Real-world constraints**: Memory, disk access, network latency

### Next Steps
- Practice implementing these algorithms
- Study advanced topics like approximation algorithms
- Learn about graph databases
- Explore parallel graph algorithms

---

*Last Updated: March 2026*  
*Algorithms Covered: 20+ graph algorithms*  
*Difficulty: Intermediate to Advanced*
