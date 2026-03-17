public class TreesExample {
    public static void main(String[] args) {
        // Binary Search Tree
        System.out.println("=== Binary Search Tree ===");
        BinarySearchTree bst = new BinarySearchTree();
        
        int[] values = {50, 30, 70, 20, 40, 60, 80, 10, 25, 35, 45};
        for (int value : values) {
            bst.insert(value);
        }
        
        System.out.println("BST (In-order): " + bst.inOrderTraversal());
        System.out.println("BST (Pre-order): " + bst.preOrderTraversal());
        System.out.println("BST (Post-order): " + bst.postOrderTraversal());
        System.out.println("BST (Level-order): " + bst.levelOrderTraversal());
        
        System.out.println("Search 40: " + bst.search(40));
        System.out.println("Search 99: " + bst.search(99));
        System.out.println("Tree height: " + bst.getHeight());
        System.out.println("Tree size: " + bst.getSize());
        
        bst.delete(30);
        System.out.println("After deleting 30 (In-order): " + bst.inOrderTraversal());
        
        // AVL Tree (Self-balancing)
        System.out.println("\n=== AVL Tree ===");
        AVLTree avl = new AVLTree();
        
        int[] avlValues = {10, 20, 30, 40, 50, 25};
        for (int value : avlValues) {
            avl.insert(value);
            System.out.println("Inserted " + value + ", Tree (In-order): " + avl.inOrderTraversal());
        }
        
        // Binary Tree (not necessarily BST)
        System.out.println("\n=== General Binary Tree ===");
        BinaryTree tree = new BinaryTree();
        
        // Build tree manually
        tree.root = new TreeNode(1);
        tree.root.left = new TreeNode(2);
        tree.root.right = new TreeNode(3);
        tree.root.left.left = new TreeNode(4);
        tree.root.left.right = new TreeNode(5);
        tree.root.right.left = new TreeNode(6);
        tree.root.right.right = new TreeNode(7);
        
        System.out.println("Binary Tree (Level-order): " + tree.levelOrderTraversal());
        System.out.println("Max depth: " + tree.maxDepth());
        System.out.println("Leaf count: " + tree.countLeaves());
        System.out.println("Is balanced: " + tree.isBalanced());
        
        // Tree Applications
        System.out.println("\n=== Tree Applications ===");
        
        // Expression tree
        System.out.println("Expression Tree:");
        ExpressionTree exprTree = new ExpressionTree();
        exprTree.buildExpressionTree("3 + 4 * 2 - 1");
        System.out.println("Expression tree (In-order): " + exprTree.inOrderTraversal());
        System.out.println("Expression evaluation: " + exprTree.evaluate());
        
        // File system simulation
        System.out.println("\nFile System Simulation:");
        FileSystemTree fileSystem = new FileSystemTree();
        fileSystem.buildFileSystem();
        fileSystem.displayFileSystem();
        
        // Tree traversals comparison
        System.out.println("\n=== Traversal Comparison ===");
        demonstrateTraversals();
        
        // Performance comparison
        System.out.println("\n=== Performance Comparison ===");
        performanceComparison();
    }
    
    public static void demonstrateTraversals() {
        BinarySearchTree demo = new BinarySearchTree();
        int[] values = {8, 3, 10, 1, 6, 14, 4, 7, 13};
        
        for (int value : values) {
            demo.insert(value);
        }
        
        System.out.println("Tree values: " + java.util.Arrays.toString(values));
        System.out.println("In-order (sorted):   " + demo.inOrderTraversal());
        System.out.println("Pre-order (root first): " + demo.preOrderTraversal());
        System.out.println("Post-order (leaves first): " + demo.postOrderTraversal());
        System.out.println("Level-order (by depth): " + demo.levelOrderTraversal());
    }
    
    public static void performanceComparison() {
        final int OPERATIONS = 10000;
        
        // Test BST
        BinarySearchTree bst = new BinarySearchTree();
        long startTime = System.nanoTime();
        
        for (int i = 0; i < OPERATIONS; i++) {
            bst.insert(i);
        }
        
        for (int i = 0; i < OPERATIONS; i++) {
            bst.search(i);
        }
        
        long endTime = System.nanoTime();
        System.out.println("BST (" + OPERATIONS + " insert+search): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
    }
}

// Binary Search Tree Implementation
class BinarySearchTree {
    TreeNode root;
    private int size;
    
    public BinarySearchTree() {
        root = null;
        size = 0;
    }
    
    // Insert value
    public void insert(int value) {
        root = insertRecursive(root, value);
        size++;
    }
    
    private TreeNode insertRecursive(TreeNode node, int value) {
        if (node == null) {
            return new TreeNode(value);
        }
        
        if (value < node.data) {
            node.left = insertRecursive(node.left, value);
        } else if (value > node.data) {
            node.right = insertRecursive(node.right, value);
        }
        
        return node;
    }
    
    // Search value
    public boolean search(int value) {
        return searchRecursive(root, value);
    }
    
    private boolean searchRecursive(TreeNode node, int value) {
        if (node == null) {
            return false;
        }
        
        if (value == node.data) {
            return true;
        }
        
        return value < node.data ? 
               searchRecursive(node.left, value) : 
               searchRecursive(node.right, value);
    }
    
    // Delete value
    public void delete(int value) {
        root = deleteRecursive(root, value);
        size--;
    }
    
    private TreeNode deleteRecursive(TreeNode node, int value) {
        if (node == null) {
            return null;
        }
        
        if (value < node.data) {
            node.left = deleteRecursive(node.left, value);
        } else if (value > node.data) {
            node.right = deleteRecursive(node.right, value);
        } else {
            // Node with one child or no child
            if (node.left == null) {
                return node.right;
            } else if (node.right == null) {
                return node.left;
            }
            
            // Node with two children
            node.data = findMinValue(node.right);
            node.right = deleteRecursive(node.right, node.data);
        }
        
        return node;
    }
    
    private int findMinValue(TreeNode node) {
        int minValue = node.data;
        while (node.left != null) {
            minValue = node.left.data;
            node = node.left;
        }
        return minValue;
    }
    
    // Traversals
    public java.util.List<Integer> inOrderTraversal() {
        java.util.List<Integer> result = new java.util.ArrayList<>();
        inOrderRecursive(root, result);
        return result;
    }
    
    private void inOrderRecursive(TreeNode node, java.util.List<Integer> result) {
        if (node != null) {
            inOrderRecursive(node.left, result);
            result.add(node.data);
            inOrderRecursive(node.right, result);
        }
    }
    
    public java.util.List<Integer> preOrderTraversal() {
        java.util.List<Integer> result = new java.util.ArrayList<>();
        preOrderRecursive(root, result);
        return result;
    }
    
    private void preOrderRecursive(TreeNode node, java.util.List<Integer> result) {
        if (node != null) {
            result.add(node.data);
            preOrderRecursive(node.left, result);
            preOrderRecursive(node.right, result);
        }
    }
    
    public java.util.List<Integer> postOrderTraversal() {
        java.util.List<Integer> result = new java.util.ArrayList<>();
        postOrderRecursive(root, result);
        return result;
    }
    
    private void postOrderRecursive(TreeNode node, java.util.List<Integer> result) {
        if (node != null) {
            postOrderRecursive(node.left, result);
            postOrderRecursive(node.right, result);
            result.add(node.data);
        }
    }
    
    public java.util.List<Integer> levelOrderTraversal() {
        java.util.List<Integer> result = new java.util.ArrayList<>();
        if (root == null) return result;
        
        java.util.Queue<TreeNode> queue = new java.util.LinkedList<>();
        queue.offer(root);
        
        while (!queue.isEmpty()) {
            TreeNode node = queue.poll();
            result.add(node.data);
            
            if (node.left != null) queue.offer(node.left);
            if (node.right != null) queue.offer(node.right);
        }
        
        return result;
    }
    
    // Get tree height
    public int getHeight() {
        return getHeightRecursive(root);
    }
    
    private int getHeightRecursive(TreeNode node) {
        if (node == null) {
            return 0;
        }
        
        return 1 + Math.max(getHeightRecursive(node.left), 
                          getHeightRecursive(node.right));
    }
    
    // Get size
    public int getSize() {
        return size;
    }
}

// AVL Tree Node
class AVLNode extends TreeNode {
    int height;
    
    AVLNode(int data) {
        super(data);
        this.height = 1;
    }
}

// Simplified AVL Tree
class AVLTree {
    AVLNode root;
    
    public void insert(int value) {
        root = insertRecursive(root, value);
    }
    
    private AVLNode insertRecursive(AVLNode node, int value) {
        if (node == null) {
            return new AVLNode(value);
        }
        
        if (value < node.data) {
            node.left = insertRecursive((AVLNode) node.left, value);
        } else if (value > node.data) {
            node.right = insertRecursive((AVLNode) node.right, value);
        } else {
            return node; // Duplicate values not allowed
        }
        
        // Update height
        node.height = 1 + Math.max(getHeight((AVLNode) node.left), 
                                getHeight((AVLNode) node.right));
        
        // Balance the tree
        return balance(node);
    }
    
    private int getHeight(AVLNode node) {
        return node == null ? 0 : node.height;
    }
    
    private int getBalance(AVLNode node) {
        return node == null ? 0 : getHeight((AVLNode) node.left) - getHeight((AVLNode) node.right);
    }
    
    private AVLNode balance(AVLNode node) {
        int balance = getBalance(node);
        
        // Left heavy
        if (balance > 1) {
            if (getBalance((AVLNode) node.left) >= 0) {
                return rotateRight(node);
            } else {
                node.left = rotateLeft((AVLNode) node.left);
                return rotateRight(node);
            }
        }
        
        // Right heavy
        if (balance < -1) {
            if (getBalance((AVLNode) node.right) <= 0) {
                return rotateLeft(node);
            } else {
                node.right = rotateRight((AVLNode) node.right);
                return rotateLeft(node);
            }
        }
        
        return node;
    }
    
    private AVLNode rotateRight(AVLNode y) {
        AVLNode x = (AVLNode) y.left;
        AVLNode T2 = (AVLNode) x.right;
        
        x.right = y;
        y.left = T2;
        
        y.height = 1 + Math.max(getHeight((AVLNode) y.left), getHeight((AVLNode) y.right));
        x.height = 1 + Math.max(getHeight((AVLNode) x.left), getHeight((AVLNode) x.right));
        
        return x;
    }
    
    private AVLNode rotateLeft(AVLNode x) {
        AVLNode y = (AVLNode) x.right;
        AVLNode T2 = (AVLNode) y.left;
        
        y.left = x;
        x.right = T2;
        
        x.height = 1 + Math.max(getHeight((AVLNode) x.left), getHeight((AVLNode) x.right));
        y.height = 1 + Math.max(getHeight((AVLNode) y.left), getHeight((AVLNode) y.right));
        
        return y;
    }
    
    public java.util.List<Integer> inOrderTraversal() {
        java.util.List<Integer> result = new java.util.ArrayList<>();
        inOrderRecursive(root, result);
        return result;
    }
    
    private void inOrderRecursive(AVLNode node, java.util.List<Integer> result) {
        if (node != null) {
            inOrderRecursive((AVLNode) node.left, result);
            result.add(node.data);
            inOrderRecursive((AVLNode) node.right, result);
        }
    }
}

// Tree Node
class TreeNode {
    int data;
    TreeNode left;
    TreeNode right;
    
    TreeNode(int data) {
        this.data = data;
        this.left = null;
        this.right = null;
    }
    
    @Override
    public String toString() {
        return String.valueOf(data);
    }
}

// General Binary Tree
class BinaryTree {
    TreeNode root;
    
    public java.util.List<Integer> levelOrderTraversal() {
        java.util.List<Integer> result = new java.util.ArrayList<>();
        if (root == null) return result;
        
        java.util.Queue<TreeNode> queue = new java.util.LinkedList<>();
        queue.offer(root);
        
        while (!queue.isEmpty()) {
            TreeNode node = queue.poll();
            result.add(node.data);
            
            if (node.left != null) queue.offer(node.left);
            if (node.right != null) queue.offer(node.right);
        }
        
        return result;
    }
    
    public int maxDepth() {
        return maxDepthRecursive(root);
    }
    
    private int maxDepthRecursive(TreeNode node) {
        if (node == null) {
            return 0;
        }
        
        return 1 + Math.max(maxDepthRecursive(node.left), 
                          maxDepthRecursive(node.right));
    }
    
    public int countLeaves() {
        return countLeavesRecursive(root);
    }
    
    private int countLeavesRecursive(TreeNode node) {
        if (node == null) {
            return 0;
        }
        
        if (node.left == null && node.right == null) {
            return 1;
        }
        
        return countLeavesRecursive(node.left) + countLeavesRecursive(node.right);
    }
    
    public boolean isBalanced() {
        return isBalancedRecursive(root);
    }
    
    private boolean isBalancedRecursive(TreeNode node) {
        if (node == null) {
            return true;
        }
        
        int leftHeight = maxDepthRecursive(node.left);
        int rightHeight = maxDepthRecursive(node.right);
        
        return Math.abs(leftHeight - rightHeight) <= 1 &&
               isBalancedRecursive(node.left) &&
               isBalancedRecursive(node.right);
    }
}

// Expression Tree
class ExpressionTree {
    TreeNode root;
    
    public void buildExpressionTree(String expression) {
        // Simplified expression tree building
        // For demo purposes, building a fixed expression: 3 + 4 * 2 - 1
        root = new TreeNode('+');
        root.left = new TreeNode(3);
        root.right = new TreeNode('-');
        root.right.left = new TreeNode('*');
        root.right.left.left = new TreeNode(4);
        root.right.left.right = new TreeNode(2);
        root.right.right = new TreeNode(1);
    }
    
    public java.util.List<Integer> inOrderTraversal() {
        java.util.List<Integer> result = new java.util.ArrayList<>();
        inOrderRecursive(root, result);
        return result;
    }
    
    private void inOrderRecursive(TreeNode node, java.util.List<Integer> result) {
        if (node != null) {
            inOrderRecursive(node.left, result);
            result.add(node.data);
            inOrderRecursive(node.right, result);
        }
    }
    
    public int evaluate() {
        return evaluateRecursive(root);
    }
    
    private int evaluateRecursive(TreeNode node) {
        if (node == null) {
            return 0;
        }
        
        // Leaf node (operand)
        if (node.left == null && node.right == null) {
            return node.data;
        }
        
        int leftValue = evaluateRecursive(node.left);
        int rightValue = evaluateRecursive(node.right);
        
        switch (node.data) {
            case '+': return leftValue + rightValue;
            case '-': return leftValue - rightValue;
            case '*': return leftValue * rightValue;
            case '/': return leftValue / rightValue;
            default: return node.data;
        }
    }
}

// File System Tree
class FileSystemTree {
    FileNode root;
    
    public void buildFileSystem() {
        root = new FileNode("root", true);
        
        FileNode documents = new FileNode("Documents", true);
        FileNode pictures = new FileNode("Pictures", true);
        FileNode programFiles = new FileNode("Program Files", true);
        
        root.addChild(documents);
        root.addChild(pictures);
        root.addChild(programFiles);
        
        documents.addChild(new FileNode("resume.doc", false));
        documents.addChild(new FileNode("report.pdf", false));
        
        pictures.addChild(new FileNode("vacation.jpg", false));
        pictures.addChild(new FileNode("family.png", false));
        
        programFiles.addChild(new FileNode("java.exe", false));
        programFiles.addChild(new FileNode("eclipse.exe", false));
    }
    
    public void displayFileSystem() {
        displayFileSystemRecursive(root, 0);
    }
    
    private void displayFileSystemRecursive(FileNode node, int level) {
        StringBuilder indent = new StringBuilder();
        for (int i = 0; i < level; i++) {
            indent.append("  ");
        }
        
        System.out.println(indent + (node.isDirectory ? "📁 " : "📄 ") + node.name);
        
        for (FileNode child : node.children) {
            displayFileSystemRecursive(child, level + 1);
        }
    }
}

// File System Node
class FileNode {
    String name;
    boolean isDirectory;
    java.util.List<FileNode> children;
    
    FileNode(String name, boolean isDirectory) {
        this.name = name;
        this.isDirectory = isDirectory;
        this.children = new java.util.ArrayList<>();
    }
    
    public void addChild(FileNode child) {
        children.add(child);
    }
}
