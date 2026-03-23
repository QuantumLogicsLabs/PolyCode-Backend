# Search Algorithms - Complete Guide

This guide covers search algorithms from basic to advanced, with implementations, analysis, and real-world applications.

## 📚 Table of Contents

1. [Introduction to Searching](#introduction-to-searching)
2. [Linear Search Algorithms](#linear-search-algorithms)
3. [Binary Search Algorithms](#binary-search-algorithms)
4. [Tree-Based Search](#tree-based-search)
5. [Graph Search Algorithms](#graph-search-algorithms)
6. [Hash-Based Search](#hash-based-search)
7. [Advanced Search Techniques](#advanced-search-techniques)
8. [Performance Analysis](#performance-analysis)

---

## Introduction to Searching

### What is Searching?
Searching is the process of finding a specific item or set of items from a collection of data.

### Why Learn Searching?
- **Fundamental Operation**: Essential for data retrieval
- **Problem-Solving**: Develops analytical thinking
- **Real Applications**: Databases, web search, AI
- **Interview Preparation**: Common technical interview topic

### Key Concepts
- **Search Space**: The collection being searched
- **Target**: The item being searched for
- **Complexity**: Time and space requirements
- **Deterministic vs Probabilistic**: Exact vs approximate matches

---

## Linear Search Algorithms

### Simple Linear Search

#### Concept
Sequentially check each element in the collection until the target is found or the end is reached.

#### Algorithm Steps
1. Start at the beginning of the collection
2. Compare each element with the target
3. If match found, return position/index
4. If end reached without match, return not found

#### Implementation
```java
def linear_search(arr, target):
    """Simple linear search implementation"""
    for i, element in enumerate(arr):
        if element == target:
            return i  # Return index of found element
    return -1  # Return -1 if not found

# Example usage
arr = [64, 34, 25, 12, 22, 11, 90]
target = 22
result = linear_search(arr, target)

if result != -1:
    print(f"Found {target} at index {result}")
else:
    print(f"{target} not found in the array")
```

#### Analysis
- **Time Complexity**: O(n) worst and average
- **Space Complexity**: O(1)
- **Best Case**: O(1) - target at first position
- **Worst Case**: O(n) - target at last position or not found

#### Pros and Cons
**Pros:**
- Simple to implement
- Works on unsorted data
- No preprocessing required

**Cons:**
- Inefficient for large datasets
- Linear time complexity

---

### Optimized Linear Search

#### Sentinel Linear Search
Add a sentinel value to eliminate boundary checks.

```java
def sentinel_linear_search(arr, target):
    """Linear search with sentinel optimization"""
    if not arr:
        return -1
    
    # Add target as sentinel
    arr_copy = arr + [target]
    i = 0
    
    # Search without boundary check
    while arr_copy[i] != target:
        i += 1
    
    # Check if found in original array
    return i if i < len(arr) else -1

# Example usage
arr = [64, 34, 25, 12, 22, 11, 90]
target = 22
result = sentinel_linear_search(arr, target)
print(f"Sentinel search result: {result}")
```

#### Bidirectional Linear Search
Search from both ends simultaneously.

```java
def bidirectional_linear_search(arr, target):
    """Bidirectional linear search"""
    left, right = 0, len(arr) - 1
    
    while left <= right:
        if arr[left] == target:
            return left
        if arr[right] == target:
            return right
        left += 1
        right -= 1
    
    return -1

# Example usage
arr = [64, 34, 25, 12, 22, 11, 90]
target = 22
result = bidirectional_linear_search(arr, target)
print(f"Bidirectional search result: {result}")
```

---

## Binary Search Algorithms

### Standard Binary Search

#### Concept
Divide and conquer algorithm that works on sorted arrays by repeatedly dividing the search interval in half.

#### Algorithm Steps
1. Ensure array is sorted
2. Find middle element
3. Compare with target
4. If equal, return position
5. If target < middle, search left half
6. If target > middle, search right half
7. Repeat until found or interval empty

#### Implementation
```java
def binary_search(arr, target):
    """Iterative binary search implementation"""
    left, right = 0, len(arr) - 1
    
    while left <= right:
        mid = left + (right - left) // 2
        
        if arr[mid] == target:
            return mid
        elif arr[mid] < target:
            left = mid + 1
        else:
            right = mid - 1
    
    return -1

def binary_search_recursive(arr, target, left=0, right=None):
    """Recursive binary search implementation"""
    if right is None:
        right = len(arr) - 1
    
    if left > right:
        return -1
    
    mid = left + (right - left) // 2
    
    if arr[mid] == target:
        return mid
    elif arr[mid] < target:
        return binary_search_recursive(arr, target, mid + 1, right)
    else:
        return binary_search_recursive(arr, target, left, mid - 1)

# Example usage
sorted_arr = [11, 12, 22, 25, 34, 64, 90]
target = 25
result = binary_search(sorted_arr, target)
result_recursive = binary_search_recursive(sorted_arr, target)

print(f"Binary search result: {result}")
print(f"Recursive binary search result: {result_recursive}")
```

#### Analysis
- **Time Complexity**: O(log n) worst and average
- **Space Complexity**: O(1) iterative, O(log n) recursive
- **Best Case**: O(1) - target at middle
- **Prerequisite**: Array must be sorted

#### Pros and Cons
**Pros:**
- Very efficient for large datasets
- Logarithmic time complexity
- Predictable performance

**Cons:**
- Requires sorted data
- Not suitable for unsorted collections
- Additional preprocessing cost if data needs sorting

---

### Exponential Search

#### Concept
Find range where target might exist, then perform binary search within that range.

#### Algorithm Steps
1. Start with range size = 1
2. Compare target with element at range boundary
3. If target > boundary, double range size
4. Repeat until target ≤ boundary or end of array
5. Perform binary search in found range

#### Implementation
```java
def exponential_search(arr, target):
    """Exponential search implementation"""
    n = len(arr)
    
    if n == 0:
        return -1
    
    # Find range where target might be
    bound = 1
    while bound < n and arr[bound] < target:
        bound *= 2
    
    # Binary search in the found range
    left = bound // 2
    right = min(bound, n - 1)
    
    return binary_search_range(arr, target, left, right)

def binary_search_range(arr, target, left, right):
    """Binary search within specific range"""
    while left <= right:
        mid = left + (right - left) // 2
        
        if arr[mid] == target:
            return mid
        elif arr[mid] < target:
            left = mid + 1
        else:
            right = mid - 1
    
    return -1

# Example usage
sorted_arr = [1, 3, 5, 7, 9, 11, 13, 15, 17, 19, 21]
target = 13
result = exponential_search(sorted_arr, target)
print(f"Exponential search result: {result}")
```

#### Analysis
- **Time Complexity**: O(log i) where i is position of target
- **Space Complexity**: O(1)
- **Best Use Case**: When target is near beginning
- **Worst Use Case**: O(log n) same as binary search

---

### Interpolation Search

#### Concept
Improved binary search that estimates position based on target value distribution.

#### Algorithm Steps
1. Assume uniform distribution of values
2. Estimate position using interpolation formula
3. Compare with target
4. Adjust search range based on comparison
5. Repeat until found or range empty

#### Implementation
```java
def interpolation_search(arr, target):
    """Interpolation search implementation"""
    left, right = 0, len(arr) - 1
    
    while left <= right and target >= arr[left] and target <= arr[right]:
        # Estimate position
        if left == right:
            pos = left
        else:
            pos = left + ((target - arr[left]) * (right - left) // 
                        (arr[right] - arr[left]))
        
        # Check if estimated position contains target
        if arr[pos] == target:
            return pos
        elif arr[pos] < target:
            left = pos + 1
        else:
            right = pos - 1
    
    return -1

# Example usage
sorted_arr = [10, 12, 13, 15, 20, 25, 30, 35, 40, 45, 50]
target = 25
result = interpolation_search(sorted_arr, target)
print(f"Interpolation search result: {result}")
```

#### Analysis
- **Time Complexity**: O(log log n) average, O(n) worst
- **Space Complexity**: O(1)
- **Best Use Case**: Uniformly distributed data
- **Worst Case**: Poorly distributed data

---

## Tree-Based Search

### Binary Search Tree Search

#### Concept
Search in a binary search tree where left child < parent < right child.

#### Implementation
```java
class TreeNode:
    """Binary search tree node"""
    def __init__(self, key):
        self.key = key
        self.left = None
        self.right = None

def bst_search(root, target):
    """Search in binary search tree"""
    current = root
    
    while current is not None:
        if target == current.key:
            return current  # Found the node
        elif target < current.key:
            current = current.left
        else:
            current = current.right
    
    return None  # Not found

def bst_insert(root, key):
    """Insert key into binary search tree"""
    if root is None:
        return TreeNode(key)
    
    if key < root.key:
        root.left = bst_insert(root.left, key)
    else:
        root.right = bst_insert(root.right, key)
    
    return root

# Example usage
root = None
keys = [50, 30, 70, 20, 40, 60, 80]

# Build BST
for key in keys:
    root = bst_insert(root, key)

# Search for values
for target in [40, 55, 80]:
    result = bst_search(root, target)
    if result:
        print(f"Found {target} in BST")
    else:
        print(f"{target} not found in BST")
```

#### Analysis
- **Time Complexity**: O(h) where h is tree height
- **Average Case**: O(log n) for balanced tree
- **Worst Case**: O(n) for degenerate tree
- **Space Complexity**: O(1) iterative, O(h) recursive

---

### AVL Tree Search

#### Concept
Self-balancing binary search tree that maintains O(log n) height through rotations.

#### Implementation
```java
class AVLNode:
    """AVL tree node with height"""
    def __init__(self, key):
        self.key = key
        self.left = None
        self.right = None
        self.height = 1

class AVLTree:
    """AVL tree implementation"""
    
    def get_height(self, node):
        """Get height of node"""
        return node.height if node else 0
    
    def get_balance(self, node):
        """Get balance factor of node"""
        return self.get_height(node.left) - self.get_height(node.right)
    
    def right_rotate(self, y):
        """Right rotation"""
        x = y.left
        T2 = x.right
        
        # Perform rotation
        x.right = y
        y.left = T2
        
        # Update heights
        y.height = 1 + max(self.get_height(y.left), self.get_height(y.right))
        x.height = 1 + max(self.get_height(x.left), self.get_height(x.right))
        
        return x
    
    def left_rotate(self, x):
        """Left rotation"""
        y = x.right
        T2 = y.left
        
        # Perform rotation
        y.left = x
        x.right = T2
        
        # Update heights
        x.height = 1 + max(self.get_height(x.left), self.get_height(x.right))
        y.height = 1 + max(self.get_height(y.left), self.get_height(y.right))
        
        return y
    
    def insert(self, root, key):
        """Insert key into AVL tree"""
        if not root:
            return AVLNode(key)
        
        if key < root.key:
            root.left = self.insert(root.left, key)
        else:
            root.right = self.insert(root.right, key)
        
        # Update height
        root.height = 1 + max(self.get_height(root.left), self.get_height(root.right))
        
        # Get balance factor
        balance = self.get_balance(root)
        
        # Balance the tree
        # Left Left Case
        if balance > 1 and key < root.left.key:
            return self.right_rotate(root)
        
        # Right Right Case
        if balance < -1 and key > root.right.key:
            return self.left_rotate(root)
        
        # Left Right Case
        if balance > 1 and key > root.left.key:
            root.left = self.left_rotate(root.left)
            return self.right_rotate(root)
        
        # Right Left Case
        if balance < -1 and key < root.right.key:
            root.right = self.right_rotate(root.right)
            return self.left_rotate(root)
        
        return root
    
    def search(self, root, target):
        """Search in AVL tree"""
        current = root
        
        while current:
            if target == current.key:
                return current
            elif target < current.key:
                current = current.left
            else:
                current = current.right
        
        return None

# Example usage
avl = AVLTree()
root = None
keys = [10, 20, 30, 40, 50, 25]

# Build AVL tree
for key in keys:
    root = avl.insert(root, key)

# Search for values
for target in [25, 35, 45]:
    result = avl.search(root, target)
    if result:
        print(f"Found {target} in AVL tree")
    else:
        print(f"{target} not found in AVL tree")
```

#### Analysis
- **Time Complexity**: O(log n) always
- **Space Complexity**: O(1) iterative, O(log n) recursive
- **Guaranteed Balance**: Automatic rebalancing
- **Overhead**: More complex than BST

---

## Graph Search Algorithms

### Breadth-First Search (BFS)

#### Concept
Level-by-level traversal that explores all neighbors at current depth before moving to next depth.

#### Implementation
```java
from collections import deque

def bfs(graph, start, target):
    """Breadth-First Search implementation"""
    visited = set()
    queue = deque([(start, [start])])  # (node, path)
    
    while queue:
        node, path = queue.popleft()
        
        if node == target:
            return path  # Found target
        
        if node not in visited:
            visited.add(node)
            
            # Add neighbors to queue
            for neighbor in graph.get(node, []):
                if neighbor not in visited:
                    queue.append((neighbor, path + [neighbor]))
    
    return None  # Target not found

# Example usage
graph = {
    'A': ['B', 'C'],
    'B': ['A', 'D', 'E'],
    'C': ['A', 'F'],
    'D': ['B'],
    'E': ['B', 'F'],
    'F': ['C', 'E']
}

start = 'A'
target = 'F'
path = bfs(graph, start, target)

if path:
    print(f"BFS path from {start} to {target}: {' -> '.join(path)}")
else:
    print(f"No path found from {start} to {target}")
```

#### Analysis
- **Time Complexity**: O(V + E) where V is vertices, E is edges
- **Space Complexity**: O(V) for visited set and queue
- **Guarantees**: Shortest path in unweighted graphs
- **Memory Usage**: Can be high for large graphs

---

### Depth-First Search (DFS)

#### Concept
Explore as far as possible along each branch before backtracking.

#### Implementation
```java
def dfs(graph, start, target, visited=None, path=None):
    """Depth-First Search implementation"""
    if visited is None:
        visited = set()
    if path is None:
        path = []
    
    visited.add(start)
    path = path + [start]
    
    if start == target:
        return path  # Found target
    
    # Explore neighbors
    for neighbor in graph.get(start, []):
        if neighbor not in visited:
            result = dfs(graph, neighbor, target, visited, path)
            if result:
                return result
    
    return None  # Target not found

def dfs_iterative(graph, start, target):
    """Iterative DFS implementation"""
    visited = set()
    stack = [(start, [start])]  # (node, path)
    
    while stack:
        node, path = stack.pop()
        
        if node == target:
            return path
        
        if node not in visited:
            visited.add(node)
            
            # Add neighbors to stack
            for neighbor in graph.get(node, []):
                if neighbor not in visited:
                    stack.append((neighbor, path + [neighbor]))
    
    return None

# Example usage
graph = {
    'A': ['B', 'C'],
    'B': ['A', 'D', 'E'],
    'C': ['A', 'F'],
    'D': ['B'],
    'E': ['B', 'F'],
    'F': ['C', 'E']
}

start = 'A'
target = 'F'
path_recursive = dfs(graph, start, target)
path_iterative = dfs_iterative(graph, start, target)

print(f"DFS recursive path: {' -> '.join(path_recursive)}")
print(f"DFS iterative path: {' -> '.join(path_iterative)}")
```

#### Analysis
- **Time Complexity**: O(V + E)
- **Space Complexity**: O(V) worst case
- **Path Found**: Not necessarily shortest
- **Memory Usage**: Generally less than BFS

---

### Dijkstra's Algorithm

#### Concept
Finds shortest path from start node to all other nodes in weighted graphs.

#### Implementation
```java
import heapq

def dijkstra(graph, start):
    """Dijkstra's algorithm implementation"""
    distances = {node: float('infinity') for node in graph}
    distances[start] = 0
    previous = {}
    unvisited = [(0, start)]
    
    while unvisited:
        current_distance, current_node = heapq.heappop(unvisited)
        
        if current_distance > distances[current_node]:
            continue
        
        # Check all neighbors
        for neighbor, weight in graph[current_node].items():
            distance = current_distance + weight
            
            if distance < distances[neighbor]:
                distances[neighbor] = distance
                previous[neighbor] = current_node
                heapq.heappush(unvisited, (distance, neighbor))
    
    return distances, previous

def shortest_path(graph, start, end):
    """Find shortest path between two nodes"""
    distances, previous = dijkstra(graph, start)
    
    path = []
    current = end
    
    while current in previous:
        path.insert(0, current)
        current = previous[current]
    
    path.insert(0, start)
    return path

# Example usage
weighted_graph = {
    'A': {'B': 5, 'C': 2},
    'B': {'A': 5, 'D': 1, 'E': 2},
    'C': {'A': 2, 'F': 4},
    'D': {'B': 1, 'E': 3},
    'E': {'B': 2, 'D': 3, 'F': 1},
    'F': {'C': 4, 'E': 1}
}

start = 'A'
end = 'F'
path = shortest_path(weighted_graph, start, end)
distance = dijkstra(weighted_graph, start)[0][end]

print(f"Shortest path from {start} to {end}: {' -> '.join(path)}")
print(f"Total distance: {distance}")
```

#### Analysis
- **Time Complexity**: O((V + E) log V)
- **Space Complexity**: O(V)
- **Guarantees**: Shortest path in weighted graphs
- **Limitations**: No negative edge weights

---

### A* Search Algorithm

#### Concept
Informed search algorithm that uses heuristics to guide search toward goal.

#### Implementation
```java
def a_star(graph, start, goal, heuristic):
    """A* search algorithm implementation"""
    open_set = [(0, start)]  # (f_score, node)
    came_from = {}
    g_score = {start: 0}
    f_score = {start: heuristic(start, goal)}
    
    while open_set:
        current_f, current = heapq.heappop(open_set)
        
        if current == goal:
            # Reconstruct path
            path = []
            while current in came_from:
                path.insert(0, current)
                current = came_from[current]
            path.insert(0, start)
            return path
        
        # Check all neighbors
        for neighbor in graph.get(current, []):
            tentative_g = g_score[current] + graph[current][neighbor]
            
            if neighbor not in g_score or tentative_g < g_score[neighbor]:
                came_from[neighbor] = current
                g_score[neighbor] = tentative_g
                f_score[neighbor] = tentative_g + heuristic(neighbor, goal)
                heapq.heappush(open_set, (f_score[neighbor], neighbor))
    
    return None

def manhattan_distance(node1, node2):
    """Manhattan distance heuristic"""
    return abs(node1[0] - node2[0]) + abs(node1[1] - node2[1])

# Example usage - grid-based pathfinding
grid_graph = {}
for x in range(5):
    for y in range(5):
        node = (x, y)
        neighbors = {}
        
        # Add valid neighbors
        for dx, dy in [(0, 1), (1, 0), (0, -1), (-1, 0)]:
            nx, ny = x + dx, y + dy
            if 0 <= nx < 5 and 0 <= ny < 5:
                neighbors[(nx, ny)] = 1  # Uniform cost
        
        grid_graph[node] = neighbors

start = (0, 0)
goal = (4, 4)
path = a_star(grid_graph, start, goal, manhattan_distance)

if path:
    print(f"A* path: {' -> '.join(map(str, path))}")
else:
    print("No path found")
```

#### Analysis
- **Time Complexity**: O(b^d) where b is branching factor, d is depth
- **Space Complexity**: O(b^d)
- **Optimality**: Guaranteed optimal if heuristic is admissible
- **Efficiency**: Much faster than uninformed search with good heuristic

---

## Hash-Based Search

### Hash Table Search

#### Concept
Use hash function to compute index and achieve O(1) average search time.

#### Implementation
```java
class HashTable:
    """Simple hash table implementation"""
    
    def __init__(self, size=100):
        self.size = size
        self.table = [[] for _ in range(size)]
    
    def _hash_function(self, key):
        """Simple hash function"""
        return hash(key) % self.size
    
    def insert(self, key, value):
        """Insert key-value pair"""
        index = self._hash_function(key)
        self.table[index].append((key, value))
    
    def search(self, key):
        """Search for key"""
        index = self._hash_function(key)
        
        for k, v in self.table[index]:
            if k == key:
                return v
        
        return None  # Not found
    
    def delete(self, key):
        """Delete key-value pair"""
        index = self._hash_function(key)
        
        for i, (k, v) in enumerate(self.table[index]):
            if k == key:
                del self.table[index][i]
                return True
        
        return False  # Not found

# Example usage
hash_table = HashTable()

# Insert data
data = [
    ("apple", 5),
    ("banana", 3),
    ("orange", 7),
    ("grape", 2)
]

for key, value in data:
    hash_table.insert(key, value)

# Search for values
for target in ["banana", "pear", "apple"]:
    result = hash_table.search(target)
    if result is not None:
        print(f"Found {target}: {result}")
    else:
        print(f"{target} not found")
```

#### Analysis
- **Time Complexity**: O(1) average, O(n) worst case
- **Space Complexity**: O(n)
- **Collision Handling**: Critical for performance
- **Load Factor**: Affects performance significantly

---

## Advanced Search Techniques

### Fuzzy Search

#### Concept
Find approximate matches using string similarity metrics.

#### Implementation
```java
def levenshtein_distance(s1, s2):
    """Calculate Levenshtein distance between two strings"""
    m, n = len(s1), len(s2)
    dp = [[0] * (n + 1) for _ in range(m + 1)]
    
    # Initialize base cases
    for i in range(m + 1):
        dp[i][0] = i
    for j in range(n + 1):
        dp[0][j] = j
    
    # Fill DP table
    for i in range(1, m + 1):
        for j in range(1, n + 1):
            if s1[i-1] == s2[j-1]:
                cost = 0
            else:
                cost = 1
            
            dp[i][j] = min(
                dp[i-1][j] + 1,      # deletion
                dp[i][j-1] + 1,      # insertion
                dp[i-1][j-1] + cost   # substitution
            )
    
    return dp[m][n]

def fuzzy_search(strings, target, threshold=2):
    """Fuzzy search using Levenshtein distance"""
    matches = []
    
    for string in strings:
        distance = levenshtein_distance(string.lower(), target.lower())
        if distance <= threshold:
            matches.append((string, distance))
    
    # Sort by distance (best matches first)
    matches.sort(key=lambda x: x[1])
    return matches

# Example usage
dictionary = ["java", "java", "javascript", "typescript", "ruby"]
target = "pythn"
matches = fuzzy_search(dictionary, target, threshold=2)

print(f"Fuzzy search for '{target}':")
for match, distance in matches:
    print(f"  {match} (distance: {distance})")
```

---

### Regular Expression Search

#### Concept
Search for patterns using regular expressions.

#### Implementation
```java
import re

def regex_search(text, pattern, flags=0):
    """Search using regular expressions"""
    try:
        compiled_pattern = re.compile(pattern, flags)
        matches = compiled_pattern.finditer(text)
        
        results = []
        for match in matches:
            results.append({
                'match': match.group(),
                'start': match.start(),
                'end': match.end(),
                'groups': match.groups()
            })
        
        return results
    
    except re.error as e:
        print(f"Invalid regex pattern: {e}")
        return []

# Example usage
text = "Contact us at support@example.com or sales@company.org"
patterns = [
    (r'\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}\b', r'Email addresses'),
    (r'\b\d{3}-\d{3}-\d{4}\b', r'Phone numbers'),
    (r'\b(?:https?://)?(?:www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}(?:/[^\s]*)?\b', r'URLs')
]

for pattern, description in patterns:
    matches = regex_search(text, pattern)
    print(f"\n{description}:")
    for match in matches:
        print(f"  Found: {match['match']} at position {match['start']}")
```

---

## Performance Analysis

### Time Complexity Comparison

| Algorithm | Time Complexity | Space Complexity | Best Use Case |
|------------|------------------|------------------|----------------|
| Linear Search | O(n) | O(1) | Unsorted data, small datasets |
| Binary Search | O(log n) | O(1) | Sorted data, large datasets |
| Exponential Search | O(log i) | O(1) | Sorted data, target near start |
| Interpolation Search | O(log log n) | O(1) | Uniform distributed data |
| BST Search | O(h) | O(1) | Dynamic data, tree structure |
| AVL Search | O(log n) | O(1) | Balanced tree required |
| BFS | O(V + E) | O(V) | Shortest path, unweighted |
| DFS | O(V + E) | O(V) | Path existence, memory efficient |
| Dijkstra | O((V + E) log V) | O(V) | Shortest path, weighted |
| A* | O(b^d) | O(b^d) | Pathfinding with heuristic |
| Hash Search | O(1) average | O(n) | Key-value lookup, fast access |

### Memory Usage Patterns

#### In-Place Algorithms
- **Linear Search**: O(1) additional space
- **Binary Search**: O(1) additional space
- **Tree Search**: O(h) recursion stack

#### Additional Memory Required
- **BFS**: O(V) for queue and visited set
- **DFS**: O(V) for recursion stack
- **Dijkstra**: O(V) for priority queue
- **Hash Table**: O(n) for table storage

---

## Practical Applications

### Database Search
```java
class SimpleDatabase:
    """Simple database with multiple search methods"""
    
    def __init__(self):
        self.records = []
        self.indexes = {}
    
    def insert(self, record):
        """Insert record into database"""
        self.records.append(record)
        
        # Update indexes
        for field, value in record.items():
            if field not in self.indexes:
                self.indexes[field] = {}
            if value not in self.indexes[field]:
                self.indexes[field][value] = []
            self.indexes[field][value].append(len(self.records) - 1)
    
    def linear_search(self, field, value):
        """Linear search on records"""
        results = []
        for i, record in enumerate(self.records):
            if record.get(field) == value:
                results.append((i, record))
        return results
    
    def indexed_search(self, field, value):
        """Search using index"""
        if field in self.indexes and value in self.indexes[field]:
            indices = self.indexes[field][value]
            return [(i, self.records[i]) for i in indices]
        return []

# Example usage
db = SimpleDatabase()
records = [
    {"id": 1, "name": "Alice", "age": 25, "city": "NYC"},
    {"id": 2, "name": "Bob", "age": 30, "city": "LA"},
    {"id": 3, "name": "Charlie", "age": 25, "city": "NYC"},
    {"id": 4, "name": "Diana", "age": 35, "city": "SF"}
]

for record in records:
    db.insert(record)

# Search operations
print("Linear search for age 25:")
results = db.linear_search("age", 25)
for i, record in results:
    print(f"  Found at index {i}: {record}")

print("\nIndexed search for city NYC:")
results = db.indexed_search("city", "NYC")
for i, record in results:
    print(f"  Found at index {i}: {record}")
```

### Autocomplete System
```java
class Autocomplete:
    """Autocomplete system using trie"""
    
    class TrieNode:
        def __init__(self):
            self.children = {}
            self.is_end = False
    
    def __init__(self):
        self.root = self.TrieNode()
    
    def insert(self, word):
        """Insert word into trie"""
        node = self.root
        for char in word.lower():
            if char not in node.children:
                node.children[char] = self.TrieNode()
            node = node.children[char]
        node.is_end = True
    
    def search_prefix(self, prefix):
        """Find all words with given prefix"""
        node = self.root
        for char in prefix.lower():
            if char not in node.children:
                return []
            node = node.children[char]
        
        return self._collect_words(node, prefix)
    
    def _collect_words(self, node, prefix):
        """Collect all words from node"""
        words = []
        if node.is_end:
            words.append(prefix)
        
        for char, child in node.children.items():
            words.extend(self._collect_words(child, prefix + char))
        
        return words

# Example usage
autocomplete = Autocomplete()
words = ["java", "programming", "programmer", "progress", "apple", "application"]

for word in words:
    autocomplete.insert(word)

prefix = "prog"
suggestions = autocomplete.search_prefix(prefix)
print(f"Autocomplete suggestions for '{prefix}': {suggestions}")
```

---

## Optimization Techniques

### Hybrid Search
```java
def hybrid_search(arr, target):
    """Hybrid search combining different algorithms"""
    n = len(arr)
    
    # Use linear search for small arrays
    if n < 10:
        return linear_search(arr, target)
    
    # Check if array is sorted
    is_sorted = all(arr[i] <= arr[i + 1] for i in range(n - 1))
    
    if is_sorted:
        # Use binary search for sorted arrays
        return binary_search(arr, target)
    else:
        # Use linear search for unsorted arrays
        return linear_search(arr, target)

# Example usage
sorted_arr = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
unsorted_arr = [5, 2, 8, 1, 9, 3, 7, 4, 6, 10]

result1 = hybrid_search(sorted_arr, 7)
result2 = hybrid_search(unsorted_arr, 7)

print(f"Hybrid search on sorted array: {result1}")
print(f"Hybrid search on unsorted array: {result2}")
```

---

## Exercises and Practice

### Exercise 1: Implement Missing Algorithms
1. **Jump Search**: Block search algorithm
2. **Fibonacci Search**: Divide array using Fibonacci numbers
3. **Ternary Search**: Three-way search
4. **Binary Search Tree**: Complete implementation with deletion

### Exercise 2: Optimize Existing Algorithms
1. Add caching to recursive algorithms
2. Implement iterative versions of recursive algorithms
3. Add collision resolution to hash table
4. Optimize Dijkstra with Fibonacci heap

### Exercise 3: Real-world Applications
1. Implement spell checker using fuzzy search
2. Create file search utility with regex
3. Build route finder for navigation
4. Design autocomplete for search engine

---

## Summary

Search algorithms are fundamental to computer science and have numerous practical applications.

### Key Takeaways
1. **Choose the right algorithm**: Consider data structure and requirements
2. **Understand trade-offs**: Time vs space complexity
3. **Preprocess when possible**: Sorting and indexing can dramatically improve performance
4. **Consider real-world constraints**: Memory, disk access, network latency

### Next Steps
- Practice implementing these algorithms
- Analyze performance on different datasets
- Learn about advanced topics like probabilistic search
- Study database indexing techniques

---

*Last Updated: March 2026*  
*Algorithms Covered: 15+ search algorithms*  
*Difficulty: Beginner to Advanced*
