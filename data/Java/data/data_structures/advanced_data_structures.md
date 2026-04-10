# Advanced Data Structures - Complete Guide

This guide covers advanced data structures beyond Java's built-in types, with implementations and analysis.

## 📚 Table of Contents

1. [Introduction to Advanced Data Structures](#introduction-to-advanced-data-structures)
2. [Tree Structures](#tree-structures)
3. [Graph Structures](#graph-structures)
4. [Heap and Priority Queue](#heap-and-priority-queue)
5. [Trie and Prefix Trees](#trie-and-prefix-trees)
6. [Skip Lists and Jump Lists](#skip-lists-and-jump-lists)
7. [Bloom Filters](#bloom-filters)
8. [Performance Analysis](#performance-analysis)

---

## Introduction to Advanced Data Structures

### Why Advanced Data Structures?
While Java provides excellent built-in data structures, advanced problems often require specialized structures with specific performance characteristics.

### When to Use Advanced Structures
- **Performance Requirements**: Built-ins don't meet performance needs
- **Specific Operations**: Need operations not available in built-ins
- **Memory Constraints**: Need more memory-efficient structures
- **Algorithm Requirements**: Specific algorithms need specific structures
- **Domain-Specific**: Problems have unique data access patterns

### Design Considerations
- **Time Complexity**: Operations must meet performance requirements
- **Space Complexity**: Memory usage must be acceptable
- **Interface Design**: Clear, consistent API for the structure
- **Edge Cases**: Handle boundary conditions properly
- **Thread Safety**: Consider concurrent access if needed

---

## Tree Structures

### Balanced Binary Search Tree (AVL Tree)

#### Concept
Self-balancing binary search tree that maintains O(log n) height through rotations.

#### Implementation
```java
from typing import Optional, Any

class AVLNode:
    """AVL Tree Node"""
    
    def __init__(self, key: Any):
        self.key = key
        self.left: Optional['AVLNode'] = None
        self.right: Optional['AVLNode'] = None
        self.height: int = 1

class AVLTree:
    """AVL Tree Implementation"""
    
    def __init__(self):
        self.root: Optional[AVLNode] = None
    
    def _get_height(self, node: Optional[AVLNode]) -> int:
        """Get node height"""
        return node.height if node else 0
    
    def _get_balance(self, node: Optional[AVLNode]) -> int:
        """Get balance factor"""
        if not node:
            return 0
        return self._get_height(node.left) - self._get_height(node.right)
    
    def _right_rotate(self, y: AVLNode) -> AVLNode:
        """Right rotation"""
        x = y.left
        T2 = x.right
        
        # Perform rotation
        x.right = y
        y.left = T2
        
        # Update heights
        y.height = 1 + max(self._get_height(y.left), self._get_height(y.right))
        x.height = 1 + max(self._get_height(x.left), self._get_height(x.right))
        
        return y
    
    def _left_rotate(self, x: AVLNode) -> AVLNode:
        """Left rotation"""
        y = x.right
        T2 = y.left
        
        # Perform rotation
        y.left = x
        x.right = T2
        
        # Update heights
        y.height = 1 + max(self._get_height(y.left), self._get_height(y.right))
        x.height = 1 + max(self._get_height(x.left), self._get_height(x.right))
        
        return y
    
    def _rebalance(self, node: AVLNode) -> AVLNode:
        """Rebalance node if necessary"""
        balance = self._get_balance(node)
        
        # Left Left Case
        if balance > 1 and self._get_balance(node.left) >= 0:
            return self._right_rotate(node)
        
        # Right Right Case
        if balance < -1 and self._get_balance(node.right) <= 0:
            return self._left_rotate(node)
        
        # Left Right Case
        if balance > 1 and self._get_balance(node.left) < 0:
            node.left = self._left_rotate(node.left)
            return self._right_rotate(node)
        
        # Right Left Case
        if balance < -1 and self._get_balance(node.right) > 0:
            node.right = self._right_rotate(node.right)
            return self._left_rotate(node)
        
        return node
    
    def insert(self, key: Any) -> None:
        """Insert key into AVL tree"""
        def _insert(node: Optional[AVLNode], key: Any) -> AVLNode:
            if not node:
                return AVLNode(key)
            
            if key < node.key:
                node.left = _insert(node.left, key)
            else:
                node.right = _insert(node.right, key)
            
            # Update height
            node.height = 1 + max(self._get_height(node.left), self._get_height(node.right))
            
            # Rebalance if necessary
            return self._rebalance(node)
        
        self.root = _insert(self.root, key)
    
    def search(self, key: Any) -> Optional[AVLNode]:
        """Search for key in AVL tree"""
        current = self.root
        
        while current:
            if key == current.key:
                return current
            elif key < current.key:
                current = current.left
            else:
                current = current.right
        
        return None
    
    def _inorder_traversal(self, node: Optional[AVLNode], result: list) -> None:
        """Inorder traversal helper"""
        if not node:
            return
        
        self._inorder_traversal(node.left, result)
        result.append(node.key)
        self._inorder_traversal(node.right, result)
    
    def inorder_traversal(self) -> list:
        """Get inorder traversal"""
        result = []
        self._inorder_traversal(self.root, result)
        return result
    
    def delete(self, key: Any) -> bool:
        """Delete key from AVL tree"""
        def _find_min(node: AVLNode) -> AVLNode:
            while node.left:
                node = node.left
            return node
        
        def _delete(node: Optional[AVLNode], key: Any) -> Optional[AVLNode]:
            if not node:
                return None
            
            if key < node.key:
                node.left = _delete(node.left, key)
            elif key > node.key:
                node.right = _delete(node.right, key)
            else:
                # Node with key found
                # Case 1: No children
                if not node.left and not node.right:
                    return None
                
                # Case 2: One child
                if not node.left:
                    return node.right
                if not node.right:
                    return node.left
                
                # Case 3: Two children
                successor = _find_min(node.right)
                node.right = _delete(node.right, successor.key)
                node.key = successor.key
            
            # Update height and rebalance
            if node:
                node.height = 1 + max(self._get_height(node.left), self._get_height(node.right))
                return self._rebalance(node)
        
        self.root = _delete(self.root, key)
        return self.root is not None

# Example usage
avl = AVLTree()
keys = [10, 20, 5, 15, 30, 25, 2, 8]

for key in keys:
    avl.insert(key)

print("Inorder traversal:", avl.inorder_traversal())
print("Search for 15:", avl.search(15).key if avl.search(15) else "Not found")
print("Delete 20:", avl.delete(20))
print("Inorder traversal after deletion:", avl.inorder_traversal())
```

#### Analysis
- **Time Complexity**: O(log n) for all operations
- **Space Complexity**: O(n) for storage
- **Guaranteed Balance**: Always maintains O(log n) height
- **Use Cases**: Database indexing, in-memory search trees

---

### Red-Black Tree

#### Concept
Self-balancing binary search tree with color-based balancing rules.

#### Implementation
```java
from enum import Enum

class Color(Enum):
    RED = 1
    BLACK = 2

class RBNode:
    """Red-Black Tree Node"""
    
    def __init__(self, key: Any):
        self.key = key
        self.color = Color.RED
        self.left: Optional['RBNode'] = None
        self.right: Optional['RBNode'] = None
        self.parent: Optional['RBNode'] = None

class RedBlackTree:
    """Red-Black Tree Implementation"""
    
    def __init__(self):
        self.NIL = RBNode(None)  # Sentinel node
        self.NIL.color = Color.BLACK
        self.root = self.NIL
    
    def _left_rotate(self, x: RBNode) -> RBNode:
        """Left rotation"""
        y = x.right
        T2 = y.left
        
        x.right = y
        y.left = T2
        y.parent = x.parent
        if T2:
            T2.parent = y
        
        return y
    
    def _right_rotate(self, x: RBNode) -> RBNode:
        """Right rotation"""
        y = x.left
        T2 = y.right
        
        x.left = y
        y.right = T2
        y.parent = x.parent
        if T2:
            T2.parent = y
        
        return y
    
    def insert(self, key: Any) -> None:
        """Insert key into Red-Black tree"""
        new_node = RBNode(key)
        new_node.left = self.NIL
        new_node.right = self.NIL
        
        # Standard BST insertion
        parent = None
        current = self.root
        
        while current != self.NIL:
            parent = current
            if key < current.key:
                current = current.left
            else:
                current = current.right
        
        new_node.parent = parent
        
        if parent is None:
            self.root = new_node
        elif key < parent.key:
            parent.left = new_node
        else:
            parent.right = new_node
        
        self._insert_fixup(new_node)
    
    def _insert_fixup(self, z: RBNode) -> None:
        """Fix Red-Black properties after insertion"""
        while z.parent and z.parent.color == Color.RED:
            if z == z.parent.right:
                y = z.parent.parent
                if y and y.right == z.parent:
                    # Case 1: Uncle is red
                    z.parent.color = Color.BLACK
                    y.color = Color.RED
                    z = z.parent
                    continue
            
            # Case 2, 3, 4: Uncle is black or NIL
            grandparent = z.parent.parent
            if z.parent == grandparent.left:
                z.parent.color = Color.BLACK
                grandparent.color = Color.RED
                self._right_rotate(grandparent)
            else:
                z.parent.color = Color.BLACK
                grandparent.color = Color.RED
                self._left_rotate(grandparent)
    
    def search(self, key: Any) -> Optional[RBNode]:
        """Search for key in Red-Black tree"""
        current = self.root
        
        while current != self.NIL:
            if key == current.key:
                return current
            elif key < current.key:
                current = current.left
            else:
                current = current.right
        
        return None
    
    def inorder_traversal(self) -> list:
        """Get inorder traversal"""
        result = []
        
        def _inorder(node: RBNode):
            if node != self.NIL:
                _inorder(node.left)
                result.append(node.key)
                _inorder(node.right)
        
        _inorder(self.root)
        return result

# Example usage
rb_tree = RedBlackTree()
keys = [10, 20, 5, 15, 30, 25, 2, 8]

for key in keys:
    rb_tree.insert(key)

print("Inorder traversal:", rb_tree.inorder_traversal())
print("Search for 15:", rb_tree.search(15).key if rb_tree.search(15) else "Not found")
```

#### Analysis
- **Time Complexity**: O(log n) for all operations
- **Space Complexity**: O(n) for storage
- **Worst-Case Bound**: 2*log n height
- **Use Cases**: Linux kernel, associative arrays, database indexes

---

## Graph Structures

### Adjacency List with Weighted Edges

#### Concept
Enhanced adjacency list that handles weighted edges efficiently.

#### Implementation
```java
from typing import Dict, List, Tuple, Optional
import heapq

class WeightedGraph:
    """Weighted graph using adjacency list"""
    
    def __init__(self, num_vertices: int):
        self.num_vertices = num_vertices
        self.adj_list: Dict[int, List[Tuple[int, float]] = {i: [] for i in range(num_vertices)}
    
    def add_edge(self, u: int, v: int, weight: float) -> None:
        """Add weighted edge"""
        self.adj_list[u].append((v, weight))
        self.adj_list[v].append((u, weight))  # For undirected graph
    
    def get_neighbors(self, vertex: int) -> List[Tuple[int, float]]:
        """Get neighbors with weights"""
        return self.adj_list[vertex]
    
    def dijkstra(self, start: int) -> Dict[int, float]:
        """Dijkstra's algorithm for shortest paths"""
        distances = {i: float('inf') for i in range(self.num_vertices)}
        distances[start] = 0
        visited = set()
        heap = [(0, start)]
        
        while heap:
            current_dist, current = heapq.heappop(heap)
            
            if current in visited:
                continue
            
            visited.add(current)
            
            for neighbor, weight in self.get_neighbors(current):
                if neighbor not in visited:
                    new_dist = current_dist + weight
                    if new_dist < distances[neighbor]:
                        distances[neighbor] = new_dist
                        heapq.heappush(heap, (new_dist, neighbor))
        
        return distances
    
    def shortest_path(self, start: int, end: int) -> List[int]:
        """Reconstruct shortest path"""
        distances = self.dijkstra(start)
        
        if distances[end] == float('inf'):
            return []
        
        path = [end]
        current = end
        
        while current != start:
            # Find predecessor
            for neighbor, weight in self.get_neighbors(current):
                if distances[neighbor] == distances[current] - weight:
                    current = neighbor
                    break
            
            path.insert(0, current)
        
        return path

# Example usage
graph = WeightedGraph(6)
edges = [
    (0, 1, 7), (0, 2, 9), (0, 5, 14),
    (1, 2, 10), (1, 3, 15), (2, 3, 11),
    (2, 4, 6), (3, 4, 2), (4, 5, 9)
]

for u, v, w in edges:
    graph.add_edge(u, v, w)

start = 0
distances = graph.dijkstra(start)
path = graph.shortest_path(start, 5)

print("Shortest distances from vertex 0:")
for vertex, distance in distances.items():
    if distance != float('inf'):
        print(f"  To {vertex}: {distance}")

print(f"\nShortest path from 0 to 5: {' -> '.join(map(str, path))}")
```

---

## Heap and Priority Queue

### Fibonacci Heap

#### Concept
Heap structure that supports efficient insertion and extraction of minimum/maximum elements.

#### Implementation
```java
from typing import List, Any, Optional

class FibonacciHeap:
    """Fibonacci Heap implementation"""
    
    class Node:
        def __init__(self, key: Any):
            self.key = key
            self.degree = 0
            self.child: List['FibonacciHeap.Node'] = []
            self.mark = False
            self.parent: Optional['FibonacciHeap.Node'] = None
    
    def __init__(self):
        self.min: Optional['FibonacciHeap.Node'] = None
        self.total_nodes = 0
    
    def _add_child(self, parent: 'FibonacciHeap.Node', child: 'FibonacciHeap.Node') -> None:
        """Add child to parent node"""
        child.parent = parent
        parent.child.append(child)
        parent.degree += 1
    
    def _remove_child(self, parent: 'FibonacciHeap.Node', child: 'FibonacciHeap.Node') -> None:
        """Remove child from parent node"""
        parent.child.remove(child)
        parent.degree -= 1
    
    def _meld(self, a: 'FibonacciHeap.Node', b: 'FibonacciHeap.Node') -> 'FibonacciHeap.Node':
        """Meld two Fibonacci heaps"""
        if not a:
            return b
        if not b:
            return a
        
        # Ensure a.key <= b.key
        if a.key > b.key:
            a, b = b, a
        
        # Link a as child of b
        self._add_child(b, a)
        a.mark = True
        
        return b
    
    def _extract_min(self) -> Optional['FibonacciHeap.Node']:
        """Extract minimum node from heap"""
        if not self.min:
            return None
        
        min_node = self.min
        if min_node.degree == 0:
            # Only child, remove it
            if self.min.parent:
                self._remove_child(self.min.parent, self.min)
            else:
                self.min = None
        
        else:
            # Find child with minimum key
            min_child = min(self.min.child, key=lambda x: x.key)
            
            # Remove min_child from min_node
            self._remove_child(self.min, min_child)
            
            # Meld remaining children
            remaining_children = self.min.child
            self.min.child = []
            
            for child in remaining_children:
                if child != min_child:
                    self.min = self._meld(self.min, child)
                else:
                    self.min = child
        
        if self.min:
            self.min.parent = None
            self.min.mark = False
        
        self.total_nodes -= 1
        return min_node
    
    def insert(self, key: Any) -> None:
        """Insert key into Fibonacci heap"""
        new_node = self.Node(key)
        self.total_nodes += 1
        
        if not self.min:
            self.min = new_node
        else:
            self.min = self._meld(self.min, new_node)
    
    def get_min(self) -> Optional[Any]:
        """Get minimum key without removing"""
        return self.min.key if self.min else None

# Example usage
fib_heap = FibonacciHeap()
keys = [15, 3, 7, 20, 1, 25, 5, 30, 17]

for key in keys:
    fib_heap.insert(key)

print("Minimum elements extracted:")
while fib_heap.get_min() is not None:
    min_node = fib_heap._extract_min()
    print(f"  {min_node.key}")
```

#### Analysis
- **Time Complexity**: O(log n) amortized for insert, O(1) amortized for find-min
- **Space Complexity**: O(n) for storage
- **Advantages**: Better performance than binary heap for some operations
- **Use Cases**: Priority queues, Dijkstra optimization, event simulation

---

## Trie and Prefix Trees

### Compressed Trie (Radix Tree)

#### Concept
Space-efficient trie implementation that compresses common prefixes.

#### Implementation
```java
from typing import Dict, List, Optional

class CompressedTrieNode:
    """Compressed Trie Node"""
    
    def __init__(self):
        self.children: Dict[str, 'CompressedTrieNode'] = {}
        self.is_end = False
        self.value: Optional[str] = None

class CompressedTrie:
    """Compressed Trie (Radix Tree) Implementation"""
    
    def __init__(self):
        self.root = CompressedTrieNode()
    
    def insert(self, word: str) -> None:
        """Insert word into compressed trie"""
        current = self.root
        
        for char in word:
            if char not in current.children:
                current.children[char] = CompressedTrieNode()
            current = current.children[char]
        
        current.is_end = True
    
    def compress(self):
        """Compress the trie by merging nodes with single child"""
        self._compress_node(self.root)
    
    def _compress_node(self, node: CompressedTrieNode) -> None:
        """Recursively compress nodes"""
        if len(node.children) == 1 and not node.is_end:
            # Merge with single child
            single_char, single_child = next(iter(node.children.items()))
            node.value = single_char + single_child.value
            node.children = single_child.children
            node.is_end = single_child.is_end
            
            # Recursively compress
            self._compress_node(node)
    
    def search(self, prefix: str) -> bool:
        """Search if prefix exists in trie"""
        current = self.root
        
        for char in prefix:
            if char not in current.children:
                return False
            current = current.children[char]
        
        return True
    
    def get_all_words(self) -> List[str]:
        """Get all words in the trie"""
        words = []
        
        def _collect_words(node: CompressedTrieNode, prefix: str):
            if node.is_end:
                words.append(prefix + (node.value or ''))
            
            for char, child in node.children.items():
                _collect_words(child, prefix + char)
        
        _collect_words(self.root, '')
        return words

# Example usage
trie = CompressedTrie()
words = ["apple", "app", "application", "apply", "banana", "band"]

for word in words:
    trie.insert(word)

trie.compress()

print("Does 'app' exist?", trie.search("app"))
print("Does 'appl' exist?", trie.search("appl"))
print("Does 'orange' exist?", trie.search("orange"))

print("\nAll words in trie:")
for word in trie.get_all_words():
    print(f"  {word}")
```

#### Analysis
- **Time Complexity**: O(m) for search, O(m) for insertion
- **Space Complexity**: O(Σlength of words) for storage
- **Compression**: Reduces space for common prefixes
- **Use Cases**: Autocomplete, spell checking, prefix matching

---

## Skip Lists and Jump Lists

### Skip List

#### Concept
Probabilistic data structure that allows fast search by skipping elements.

#### Implementation
```java
import random
from typing import List, Optional, Any

class SkipListNode:
    """Skip List Node"""
    
    def __init__(self, key: Any, level: int = 0):
        self.key = key
        self.forward: List[Optional['SkipListNode']] = [None] * (level + 1)
        self.level = level

class SkipList:
    """Skip List Implementation"""
    
    def __init__(self, max_level: int = 16):
        self.max_level = max_level
        self.head = SkipListNode(float('-inf'), max_level)
        self.level = 0
    
    def _random_level(self) -> int:
        """Get random level for insertion"""
        return random.randint(0, self.level)
    
    def insert(self, key: Any) -> None:
        """Insert key into skip list"""
        update = [self.head]
        
        for i in range(self.level + 1):
            if i < len(update[i - 1].forward):
                update[i] = update[i - 1].forward[i]
        
        # Create new node
        new_node = SkipListNode(key, self.level)
        
        # Update forward pointers
        for i in range(self.level + 1):
            new_node.forward[i] = update[i]
            while update[i].forward[i] and update[i].forward[i].key < key:
                update[i] = update[i].forward[i]
        
        # Insert new node
        for i in range(self.level + 1):
            update[i - 1].forward[i] = new_node
        
        # Update level
        if random.random() < 0.5:  # 50% chance to increase level
            self.level = min(self.level + 1, self.max_level)
    
    def search(self, key: Any) -> Optional[Any]:
        """Search for key in skip list"""
        current = self.head
        
        # Start from highest level
        for i in range(self.level, -1, -1):
            while current.forward[i] and current.forward[i].key < key:
                current = current.forward[i]
        
        # Linear search at bottom level
        while current.forward[0] and current.forward[0].key < key:
            current = current.forward[0]
        
        return current.forward[0] if current.forward[0] and current.forward[0].key == key else None
    
    def delete(self, key: Any) -> bool:
        """Delete key from skip list"""
        update = [self.head]
        
        # Find node to delete
        for i in range(self.level, -1, -1):
            while update[i].forward[i] and update[i].forward[i].key < key:
                update[i] = update[i].forward[i]
        
        node_to_delete = update[0]
        
        if not node_to_delete or node_to_delete.key != key:
            return False
        
        # Update forward pointers
        for i in range(self.level + 1):
            if update[i].forward[i] and update[i].forward[i].key == key:
                update[i].forward[i] = update[i].forward[i].forward[i]
        
        return True

# Example usage
skip_list = SkipList()
keys = [10, 20, 5, 15, 30, 25, 2, 8, 35, 12, 18, 40]

for key in keys:
    skip_list.insert(key)

print("Search results:")
for key in [5, 15, 25, 35, 50]:
    result = skip_list.search(key)
    print(f"  {key}: {result.key if result else 'Not found'}")

print(f"\nDelete 15: {skip_list.delete(15)}")
print(f"Search 15 after deletion: {skip_list.search(15).key if skip_list.search(15) else 'Not found'}")
```

#### Analysis
- **Time Complexity**: O(log n) average, O(n) worst case
- **Space Complexity**: O(n) for storage
- **Probabilistic**: Performance based on randomization
- **Use Cases**: Database indexes, cache implementations, search optimization

---

## Bloom Filters

#### Concept
Probabilistic data structure for testing membership with false positives.

#### Implementation
```java
import mmh3
from typing import List, Optional

class BloomFilter:
    """Bloom Filter Implementation"""
    
    def __init__(self, size: int, num_hashes: int = 3):
        self.size = size
        self.num_hashes = num_hashes
        self.bit_array = [0] * size
        self.hash_functions = [
            lambda x: mmh3.hash(x, i) % size for i in range(num_hashes)
        ]
    
    def add(self, item: str) -> None:
        """Add item to bloom filter"""
        for i in range(self.num_hashes):
            hash_val = self.hash_functions[i](item)
            self.bit_array[hash_val] = 1
    
    def __contains__(self, item: str) -> bool:
        """Check if item might be in set"""
        for i in range(self.num_hashes):
            hash_val = self.hash_functions[i](item)
            if not self.bit_array[hash_val]:
                return False
        return True
    
    def false_positive_rate(self) -> float:
        """Estimate false positive rate"""
        # This would require tracking actual false positives
        # Simplified estimation based on load factor
        return 0.01  # Typical false positive rate

# Example usage
bloom = BloomFilter(size=10000, num_hashes=3)

# Add some items
items = ["apple", "banana", "orange", "grape", "watermelon"]
for item in items:
    bloom.add(item)

# Test membership
test_items = ["apple", "cherry", "date", "elderberry", "fig", "grape"]

print("Bloom filter membership test:")
for item in test_items:
    in_filter = item in bloom
    actual_in_set = item in items
    status = "✓" if actual_in_set else "✗"
    fp = " (FP)" if in_filter and not actual_in_set else ""
    print(f"  {item}: {in_filter}{fp}")

print(f"\nEstimated false positive rate: {bloom.false_positive_rate():.2%}")
```

#### Analysis
- **Time Complexity**: O(k) for add and check, where k is number of hash functions
- **Space Complexity**: O(n) for bit array
- **False Positives**: Possible but can be controlled
- **Use Cases**: Cache filtering, URL blacklists, recommendation systems

---

## Performance Analysis

### Complexity Comparison

| Data Structure | Search | Insert | Delete | Space | Best Use Case |
|---------------|--------|--------|--------|----------------|
| AVL Tree | O(log n) | O(log n) | O(log n) | Dynamic search |
| Red-Black Tree | O(log n) | O(log n) | O(log n) | Database indexing |
| Skip List | O(log n) avg | O(log n) avg | O(n) | Cache optimization |
| Fibonacci Heap | O(1) find-min | O(log n) insert | O(n) | Priority queue |
| Compressed Trie | O(m) | O(m) | O(Σ|word|) | Autocomplete |
| Bloom Filter | O(k) | O(k) | N/A | Membership testing |

### Memory Usage Patterns

#### Space-Efficient Structures
- **Compressed Trie**: Reduces memory for common prefixes
- **Bloom Filter**: Constant space for large sets
- **Skip List**: O(n) space with O(log n) search
- **Fibonacci Heap**: Efficient priority queue operations

#### Cache-Friendly Structures
- **Arrays**: Sequential access patterns
- **B-Trees**: Disk-based data structures
- **Skip Lists**: Skip levels for cache efficiency
- **Spatial Structures**: R-trees, k-d trees

---

## Practical Applications

### Autocomplete System
```java
class AutocompleteSystem:
    """Autocomplete system using compressed trie"""
    
    def __init__(self):
        self.trie = CompressedTrie()
        self.cache = {}
    
    def add_word(self, word: str, frequency: int = 1) -> None:
        """Add word with frequency"""
        self.trie.insert(word)
        self.cache[word] = frequency
    
    def get_suggestions(self, prefix: str, max_suggestions: int = 5) -> List[str]:
        """Get autocomplete suggestions"""
        all_words = self.trie.get_all_words()
        
        # Filter words starting with prefix
        candidates = [word for word in all_words if word.startswith(prefix)]
        
        # Sort by frequency (if available) then alphabetically
        candidates.sort(key=lambda x: (-self.cache.get(x, 0), x))
        
        return candidates[:max_suggestions]

# Example usage
autocomplete = AutocompleteSystem()
words_with_freq = [
    ("java", 100), ("programming", 50), ("java", 80),
    ("javascript", 60), ("c++", 40), ("ruby", 30)
]

for word, freq in words_with_freq:
    autocomplete.add_word(word, freq)

suggestions = autocomplete.get_suggestions("pro")
print("Autocomplete suggestions for 'pro':")
for i, suggestion in enumerate(suggestions, 1):
    print(f"  {i}. {suggestion}")
```

---

## Best Practices

### Implementation Guidelines
1. **Interface Design**: Create clear, consistent APIs
2. **Error Handling**: Handle edge cases and invalid inputs
3. **Memory Management**: Consider memory usage patterns
4. **Testing**: Thoroughly test all operations
5. **Documentation**: Explain complexity and use cases

### Selection Criteria
1. **Performance Requirements**: Choose based on operation needs
2. **Memory Constraints**: Consider available memory
3. **Data Characteristics**: Match structure to data patterns
4. **Maintenance Needs**: Consider long-term maintainability
5. **Team Expertise**: Choose structures team is familiar with

---

## Conclusion

Advanced data structures provide specialized performance characteristics for specific use cases. The key is understanding the trade-offs and choosing the right structure for your specific requirements.

### Key Takeaways
1. **No Perfect Structure**: Each has strengths and weaknesses
2. **Context Matters**: Consider access patterns and constraints
3. **Measure Performance**: Profile with real data
4. **Start Simple**: Use built-ins until they're insufficient
5. **Learn Continuously**: Stay updated on new techniques

---

*Last Updated: March 2026*  
*Structures Covered: 8 advanced data structures*  
*Difficulty: Advanced*
