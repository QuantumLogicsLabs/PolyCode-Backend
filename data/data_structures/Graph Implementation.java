"""
Graph Data Structure Implementation
Adjacency list and matrix representations with common graph algorithms.
"""

class Graph:
    """Graph implementation using adjacency list."""
    
    def __init__(self, num_vertices):
        """Initialize graph with given number of vertices."""
        self.num_vertices = num_vertices
        self.adj_list = {i: [] for i in range(num_vertices)}
        self.adj_matrix = [[0] * num_vertices for _ in range(num_vertices)]
    
    def add_edge(self, u, v, weight=1, directed=False):
        """Add edge between vertices u and v."""
        if u >= self.num_vertices or v >= self.num_vertices:
            raise ValueError("Vertex index out of range")
        
        self.adj_list[u].append((v, weight))
        self.adj_matrix[u][v] = weight
        
        if not directed:
            self.adj_list[v].append((u, weight))
            self.adj_matrix[v][u] = weight
    
    def remove_edge(self, u, v, directed=False):
        """Remove edge between vertices u and v."""
        if u >= self.num_vertices or v >= self.num_vertices:
            raise ValueError("Vertex index out of range")
        
        self.adj_list[u] = [(neighbor, weight) for neighbor, weight in self.adj_list[u] if neighbor != v]
        self.adj_matrix[u][v] = 0
        
        if not directed:
            self.adj_list[v] = [(neighbor, weight) for neighbor, weight in self.adj_list[v] if neighbor != u]
            self.adj_matrix[v][u] = 0
    
    def get_neighbors(self, vertex):
        """Get neighbors of a vertex."""
        if vertex >= self.num_vertices:
            raise ValueError("Vertex index out of range")
        return [neighbor for neighbor, _ in self.adj_list[vertex]]
    
    def get_degree(self, vertex):
        """Get degree of a vertex."""
        if vertex >= self.num_vertices:
            raise ValueError("Vertex index out of range")
        return len(self.adj_list[vertex])
    
    def bfs(self, start_vertex):
        """Breadth-First Search traversal."""
        if start_vertex >= self.num_vertices:
            raise ValueError("Vertex index out of range")
        
        visited = [False] * self.num_vertices
        queue = [start_vertex]
        visited[start_vertex] = True
        result = []
        
        while queue:
            vertex = queue.pop(0)
            result.append(vertex)
            
            for neighbor, _ in self.adj_list[vertex]:
                if not visited[neighbor]:
                    visited[neighbor] = True
                    queue.append(neighbor)
        
        return result
    
    def dfs(self, start_vertex):
        """Depth-First Search traversal."""
        if start_vertex >= self.num_vertices:
            raise ValueError("Vertex index out of range")
        
        visited = [False] * self.num_vertices
        result = []
        
        def dfs_helper(vertex):
            visited[vertex] = True
            result.append(vertex)
            
            for neighbor, _ in self.adj_list[vertex]:
                if not visited[neighbor]:
                    dfs_helper(neighbor)
        
        dfs_helper(start_vertex)
        return result
    
    def has_path(self, u, v):
        """Check if there's a path between vertices u and v."""
        if u >= self.num_vertices or v >= self.num_vertices:
            raise ValueError("Vertex index out of range")
        
        visited = [False] * self.num_vertices
        queue = [u]
        visited[u] = True
        
        while queue:
            vertex = queue.pop(0)
            
            if vertex == v:
                return True
            
            for neighbor, _ in self.adj_list[vertex]:
                if not visited[neighbor]:
                    visited[neighbor] = True
                    queue.append(neighbor)
        
        return False
    
    def is_connected(self):
        """Check if graph is connected (for undirected graphs)."""
        if self.num_vertices == 0:
            return True
        
        visited = [False] * self.num_vertices
        queue = [0]
        visited[0] = True
        count = 1
        
        while queue:
            vertex = queue.pop(0)
            
            for neighbor, _ in self.adj_list[vertex]:
                if not visited[neighbor]:
                    visited[neighbor] = True
                    queue.append(neighbor)
                    count += 1
        
        return count == self.num_vertices
    
    def print_adj_list(self):
        """Print adjacency list representation."""
        print("Adjacency List:")
        for vertex in range(self.num_vertices):
            neighbors = [f"{neighbor}({weight})" for neighbor, weight in self.adj_list[vertex]]
            print(f"{vertex}: {', '.join(neighbors) if neighbors else 'None'}")
    
    def print_adj_matrix(self):
        """Print adjacency matrix representation."""
        print("Adjacency Matrix:")
        for row in self.adj_matrix:
            print(" ".join(str(cell).rjust(3) for cell in row))

class WeightedGraph(Graph):
    """Weighted graph with Dijkstra's algorithm."""
    
    def dijkstra(self, start_vertex):
        """Find shortest paths from start_vertex to all other vertices."""
        if start_vertex >= self.num_vertices:
            raise ValueError("Vertex index out of range")
        
        import heapq
        
        distances = [float('inf')] * self.num_vertices
        distances[start_vertex] = 0
        previous = [None] * self.num_vertices
        heap = [(0, start_vertex)]
        
        while heap:
            current_dist, current_vertex = heapq.heappop(heap)
            
            if current_dist > distances[current_vertex]:
                continue
            
            for neighbor, weight in self.adj_list[current_vertex]:
                distance = current_dist + weight
                
                if distance < distances[neighbor]:
                    distances[neighbor] = distance
                    previous[neighbor] = current_vertex
                    heapq.heappush(heap, (distance, neighbor))
        
        return distances, previous
    
    def shortest_path(self, start_vertex, end_vertex):
        """Find shortest path between two vertices."""
        distances, previous = self.dijkstra(start_vertex)
        
        if distances[end_vertex] == float('inf'):
            return None, float('inf')
        
        path = []
        current = end_vertex
        
        while current is not None:
            path.append(current)
            current = previous[current]
        
        path.reverse()
        return path, distances[end_vertex]

def main():
    """Demonstrate graph data structures."""
    print("Graph Data Structures Demonstration")
    print("=" * 45)
    
    # Create a graph
    graph = Graph(6)
    graph.add_edge(0, 1)
    graph.add_edge(0, 2)
    graph.add_edge(1, 3)
    graph.add_edge(1, 4)
    graph.add_edge(2, 4)
    graph.add_edge(3, 4)
    graph.add_edge(3, 5)
    graph.add_edge(4, 5)
    
    print("\n1. Graph Structure:")
    graph.print_adj_list()
    print()
    graph.print_adj_matrix()
    
    # Traversals
    print("\n2. Graph Traversals:")
    print(f"BFS from vertex 0: {graph.bfs(0)}")
    print(f"DFS from vertex 0: {graph.dfs(0)}")
    
    # Graph properties
    print("\n3. Graph Properties:")
    print(f"Degree of vertex 1: {graph.get_degree(1)}")
    print(f"Neighbors of vertex 0: {graph.get_neighbors(0)}")
    print(f"Path exists between 0 and 5: {graph.has_path(0, 5)}")
    print(f"Graph is connected: {graph.is_connected()}")
    
    # Weighted graph with Dijkstra
    print("\n4. Weighted Graph - Dijkstra's Algorithm:")
    weighted_graph = WeightedGraph(5)
    weighted_graph.add_edge(0, 1, 2)
    weighted_graph.add_edge(0, 2, 4)
    weighted_graph.add_edge(1, 2, 1)
    weighted_graph.add_edge(1, 3, 7)
    weighted_graph.add_edge(2, 4, 3)
    weighted_graph.add_edge(3, 4, 1)
    
    weighted_graph.print_adj_list()
    
    start = 0
    distances, previous = weighted_graph.dijkstra(start)
    print(f"\nShortest distances from vertex {start}:")
    for vertex, distance in enumerate(distances):
        print(f"  To {vertex}: {distance}")
    
    # Find specific shortest path
    path, distance = weighted_graph.shortest_path(0, 4)
    print(f"\nShortest path from 0 to 4: {path} (distance: {distance})")

if __name__ == "__main__":
    main()
