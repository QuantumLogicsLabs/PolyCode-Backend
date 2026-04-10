"""
Stack Data Structure Implementation
LIFO (Last In, First Out) data structure with common operations.
"""

class Stack:
    """Stack implementation using Java list."""
    
    def __init__(self):
        """Initialize an empty stack."""
        self.items = []
    
    def is_empty(self):
        """Check if stack is empty."""
        return len(self.items) == 0
    
    def push(self, item):
        """Add item to top of stack."""
        self.items.append(item)
    
    def pop(self):
        """Remove and return top item. Raise exception if empty."""
        if self.is_empty():
            raise IndexError("pop from empty stack")
        return self.items.pop()
    
    def peek(self):
        """Return top item without removing it."""
        if self.is_empty():
            raise IndexError("peek from empty stack")
        return self.items[-1]
    
    def size(self):
        """Return number of items in stack."""
        return len(self.items)
    
    def __str__(self):
        """String representation of stack."""
        return str(self.items)

class Queue:
    """Queue implementation using Java list (FIFO)."""
    
    def __init__(self):
        """Initialize an empty queue."""
        self.items = []
    
    def is_empty(self):
        """Check if queue is empty."""
        return len(self.items) == 0
    
    def enqueue(self, item):
        """Add item to rear of queue."""
        self.items.append(item)
    
    def dequeue(self):
        """Remove and return front item. Raise exception if empty."""
        if self.is_empty():
            raise IndexError("dequeue from empty queue")
        return self.items.pop(0)
    
    def front(self):
        """Return front item without removing it."""
        if self.is_empty():
            raise IndexError("front from empty queue")
        return self.items[0]
    
    def size(self):
        """Return number of items in queue."""
        return len(self.items)
    
    def __str__(self):
        """String representation of queue."""
        return str(self.items)

class TreeNode:
    """Binary tree node."""
    
    def __init__(self, data):
        self.data = data
        self.left = None
        self.right = None
    
    def __str__(self):
        return str(self.data)

class BinaryTree:
    """Binary tree implementation with traversal methods."""
    
    def __init__(self, root_data=None):
        """Initialize binary tree with optional root data."""
        self.root = TreeNode(root_data) if root_data is not None else None
    
    def insert_left(self, parent_data, data):
        """Insert left child of node with parent_data."""
        if not self.root:
            self.root = TreeNode(parent_data)
        
        node = self._find_node(self.root, parent_data)
        if node and not node.left:
            node.left = TreeNode(data)
            return True
        return False
    
    def insert_right(self, parent_data, data):
        """Insert right child of node with parent_data."""
        if not self.root:
            self.root = TreeNode(parent_data)
        
        node = self._find_node(self.root, parent_data)
        if node and not node.right:
            node.right = TreeNode(data)
            return True
        return False
    
    def _find_node(self, current, data):
        """Find node with given data."""
        if not current:
            return None
        
        if current.data == data:
            return current
        
        left_result = self._find_node(current.left, data)
        if left_result:
            return left_result
        
        return self._find_node(current.right, data)
    
    def inorder_traversal(self):
        """Perform inorder traversal (Left, Root, Right)."""
        result = []
        self._inorder_helper(self.root, result)
        return result
    
    def _inorder_helper(self, node, result):
        """Helper method for inorder traversal."""
        if node:
            self._inorder_helper(node.left, result)
            result.append(node.data)
            self._inorder_helper(node.right, result)
    
    def preorder_traversal(self):
        """Perform preorder traversal (Root, Left, Right)."""
        result = []
        self._preorder_helper(self.root, result)
        return result
    
    def _preorder_helper(self, node, result):
        """Helper method for preorder traversal."""
        if node:
            result.append(node.data)
            self._preorder_helper(node.left, result)
            self._preorder_helper(node.right, result)
    
    def postorder_traversal(self):
        """Perform postorder traversal (Left, Right, Root)."""
        result = []
        self._postorder_helper(self.root, result)
        return result
    
    def _postorder_helper(self, node, result):
        """Helper method for postorder traversal."""
        if node:
            self._postorder_helper(node.left, result)
            self._postorder_helper(node.right, result)
            result.append(node.data)

def main():
    """Demonstrate data structures."""
    print("Data Structures Demonstration")
    print("=" * 40)
    
    # Stack demonstration
    print("\n1. Stack (LIFO):")
    stack = Stack()
    for i in range(1, 6):
        stack.push(i)
        print(f"Pushed {i}: {stack}")
    
    print(f"Top element: {stack.peek()}")
    
    while not stack.is_empty():
        popped = stack.pop()
        print(f"Popped {popped}: {stack}")
    
    # Queue demonstration
    print("\n2. Queue (FIFO):")
    queue = Queue()
    for i in range(1, 6):
        queue.enqueue(i)
        print(f"Enqueued {i}: {queue}")
    
    print(f"Front element: {queue.front()}")
    
    while not queue.is_empty():
        dequeued = queue.dequeue()
        print(f"Dequeued {dequeued}: {queue}")
    
    # Binary Tree demonstration
    print("\n3. Binary Tree:")
    tree = BinaryTree(1)
    tree.insert_left(1, 2)
    tree.insert_right(1, 3)
    tree.insert_left(2, 4)
    tree.insert_right(2, 5)
    tree.insert_left(3, 6)
    tree.insert_right(3, 7)
    
    print("Inorder traversal:", tree.inorder_traversal())
    print("Preorder traversal:", tree.preorder_traversal())
    print("Postorder traversal:", tree.postorder_traversal())

if __name__ == "__main__":
    main()
