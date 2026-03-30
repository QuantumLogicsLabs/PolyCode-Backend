using System;
using System.Collections;
using System.Collections.Generic;

// Data Structures Implementation

// 1. Linked List Implementation
public class LinkedListNode<T>
{
    public T Data { get; set; }
    public LinkedListNode<T> Next { get; set; }
    
    public LinkedListNode(T data)
    {
        Data = data;
        Next = null;
    }
}

public class CustomLinkedList<T> : IEnumerable<T>
{
    private LinkedListNode<T> head;
    private LinkedListNode<T> tail;
    private int count;
    
    public int Count => count;
    public bool IsEmpty => head == null;
    
    public void Add(T data)
    {
        var newNode = new LinkedListNode<T>(data);
        
        if (IsEmpty)
        {
            head = tail = newNode;
        }
        else
        {
            tail.Next = newNode;
            tail = newNode;
        }
        
        count++;
    }
    
    public void AddFirst(T data)
    {
        var newNode = new LinkedListNode<T>(data);
        
        if (IsEmpty)
        {
            head = tail = newNode;
        }
        else
        {
            newNode.Next = head;
            head = newNode;
        }
        
        count++;
    }
    
    public bool Remove(T data)
    {
        if (IsEmpty) return false;
        
        if (head.Data.Equals(data))
        {
            head = head.Next;
            if (head == null) tail = null;
            count--;
            return true;
        }
        
        var current = head;
        while (current.Next != null && !current.Next.Data.Equals(data))
        {
            current = current.Next;
        }
        
        if (current.Next != null)
        {
            current.Next = current.Next.Next;
            if (current.Next == null) tail = current;
            count--;
            return true;
        }
        
        return false;
    }
    
    public bool Contains(T data)
    {
        var current = head;
        while (current != null)
        {
            if (current.Data.Equals(data))
                return true;
            current = current.Next;
        }
        return false;
    }
    
    public void Clear()
    {
        head = tail = null;
        count = 0;
    }
    
    public IEnumerator<T> GetEnumerator()
    {
        var current = head;
        while (current != null)
        {
            yield return current.Data;
            current = current.Next;
        }
    }
    
    IEnumerator IEnumerable.GetEnumerator()
    {
        return GetEnumerator();
    }
    
    public void PrintList()
    {
        Console.Write("LinkedList: ");
        foreach (var item in this)
        {
            Console.Write(item + " -> ");
        }
        Console.WriteLine("null");
    }
}

// 2. Stack Implementation
public class CustomStack<T>
{
    private T[] items;
    private int top;
    private const int DEFAULT_CAPACITY = 10;
    
    public int Count { get; private set; }
    public bool IsEmpty => Count == 0;
    
    public CustomStack()
    {
        items = new T[DEFAULT_CAPACITY];
        top = -1;
        Count = 0;
    }
    
    public CustomStack(int capacity)
    {
        items = new T[capacity];
        top = -1;
        Count = 0;
    }
    
    public void Push(T item)
    {
        if (Count == items.Length)
        {
            ResizeArray();
        }
        
        items[++top] = item;
        Count++;
    }
    
    public T Pop()
    {
        if (IsEmpty)
            throw new InvalidOperationException("Stack is empty");
        
        T item = items[top];
        items[top] = default(T);
        top--;
        Count--;
        
        return item;
    }
    
    public T Peek()
    {
        if (IsEmpty)
            throw new InvalidOperationException("Stack is empty");
        
        return items[top];
    }
    
    private void ResizeArray()
    {
        int newCapacity = items.Length * 2;
        T[] newArray = new T[newCapacity];
        Array.Copy(items, newArray, items.Length);
        items = newArray;
    }
    
    public void Clear()
    {
        Array.Clear(items, 0, items.Length);
        top = -1;
        Count = 0;
    }
}

// 3. Queue Implementation
public class CustomQueue<T>
{
    private T[] items;
    private int head;
    private int tail;
    private const int DEFAULT_CAPACITY = 10;
    
    public int Count { get; private set; }
    public bool IsEmpty => Count == 0;
    
    public CustomQueue()
    {
        items = new T[DEFAULT_CAPACITY];
        head = tail = -1;
        Count = 0;
    }
    
    public void Enqueue(T item)
    {
        if (Count == items.Length)
        {
            ResizeArray();
        }
        
        if (head == -1)
        {
            head = tail = 0;
        }
        else
        {
            tail = (tail + 1) % items.Length;
        }
        
        items[tail] = item;
        Count++;
    }
    
    public T Dequeue()
    {
        if (IsEmpty)
            throw new InvalidOperationException("Queue is empty");
        
        T item = items[head];
        
        if (head == tail)
        {
            head = tail = -1;
        }
        else
        {
            head = (head + 1) % items.Length;
        }
        
        Count--;
        return item;
    }
    
    public T Peek()
    {
        if (IsEmpty)
            throw new InvalidOperationException("Queue is empty");
        
        return items[head];
    }
    
    private void ResizeArray()
    {
        int newCapacity = items.Length * 2;
        T[] newArray = new T[newCapacity];
        
        if (head <= tail)
        {
            Array.Copy(items, head, newArray, 0, Count);
        }
        else
        {
            Array.Copy(items, head, newArray, 0, items.Length - head);
            Array.Copy(items, 0, newArray, items.Length - head, tail + 1);
        }
        
        items = newArray;
        head = 0;
        tail = Count - 1;
    }
    
    public void Clear()
    {
        Array.Clear(items, 0, items.Length);
        head = tail = -1;
        Count = 0;
    }
}

// 4. Binary Tree Implementation
public class BinaryTreeNode<T>
{
    public T Data { get; set; }
    public BinaryTreeNode<T> Left { get; set; }
    public BinaryTreeNode<T> Right { get; set; }
    
    public BinaryTreeNode(T data)
    {
        Data = data;
        Left = Right = null;
    }
}

public class BinaryTree<T> where T : IComparable<T>
{
    public BinaryTreeNode<T> Root { get; private set; }
    
    public void Insert(T data)
    {
        Root = InsertRecursive(Root, data);
    }
    
    private BinaryTreeNode<T> InsertRecursive(BinaryTreeNode<T> node, T data)
    {
        if (node == null)
        {
            return new BinaryTreeNode<T>(data);
        }
        
        if (data.CompareTo(node.Data) < 0)
        {
            node.Left = InsertRecursive(node.Left, data);
        }
        else if (data.CompareTo(node.Data) > 0)
        {
            node.Right = InsertRecursive(node.Right, data);
        }
        
        return node;
    }
    
    public bool Search(T data)
    {
        return SearchRecursive(Root, data);
    }
    
    private bool SearchRecursive(BinaryTreeNode<T> node, T data)
    {
        if (node == null)
            return false;
        
        if (data.CompareTo(node.Data) == 0)
            return true;
        
        return data.CompareTo(node.Data) < 0 
            ? SearchRecursive(node.Left, data) 
            : SearchRecursive(node.Right, data);
    }
    
    public void InOrderTraversal()
    {
        Console.Write("In-order: ");
        InOrderTraversalRecursive(Root);
        Console.WriteLine();
    }
    
    private void InOrderTraversalRecursive(BinaryTreeNode<T> node)
    {
        if (node != null)
        {
            InOrderTraversalRecursive(node.Left);
            Console.Write(node.Data + " ");
            InOrderTraversalRecursive(node.Right);
        }
    }
    
    public void PreOrderTraversal()
    {
        Console.Write("Pre-order: ");
        PreOrderTraversalRecursive(Root);
        Console.WriteLine();
    }
    
    private void PreOrderTraversalRecursive(BinaryTreeNode<T> node)
    {
        if (node != null)
        {
            Console.Write(node.Data + " ");
            PreOrderTraversalRecursive(node.Left);
            PreOrderTraversalRecursive(node.Right);
        }
    }
    
    public void PostOrderTraversal()
    {
        Console.Write("Post-order: ");
        PostOrderTraversalRecursive(Root);
        Console.WriteLine();
    }
    
    private void PostOrderTraversalRecursive(BinaryTreeNode<T> node)
    {
        if (node != null)
        {
            PostOrderTraversalRecursive(node.Left);
            PostOrderTraversalRecursive(node.Right);
            Console.Write(node.Data + " ");
        }
    }
    
    public int GetHeight()
    {
        return GetHeightRecursive(Root);
    }
    
    private int GetHeightRecursive(BinaryTreeNode<T> node)
    {
        if (node == null)
            return 0;
        
        int leftHeight = GetHeightRecursive(node.Left);
        int rightHeight = GetHeightRecursive(node.Right);
        
        return Math.Max(leftHeight, rightHeight) + 1;
    }
    
    public int GetNodeCount()
    {
        return GetNodeCountRecursive(Root);
    }
    
    private int GetNodeCountRecursive(BinaryTreeNode<T> node)
    {
        if (node == null)
            return 0;
        
        return 1 + GetNodeCountRecursive(node.Left) + GetNodeCountRecursive(node.Right);
    }
}

// 5. Hash Table Implementation (Simple)
public class CustomHashTable<TKey, TValue>
{
    private class Entry
    {
        public TKey Key { get; set; }
        public TValue Value { get; set; }
        public Entry Next { get; set; }
        
        public Entry(TKey key, TValue value)
        {
            Key = key;
            Value = value;
            Next = null;
        }
    }
    
    private Entry[] buckets;
    private int count;
    private const int DEFAULT_CAPACITY = 16;
    
    public int Count => count;
    
    public CustomHashTable()
    {
        buckets = new Entry[DEFAULT_CAPACITY];
        count = 0;
    }
    
    public CustomHashTable(int capacity)
    {
        buckets = new Entry[capacity];
        count = 0;
    }
    
    public void Add(TKey key, TValue value)
    {
        int index = GetBucketIndex(key);
        var entry = buckets[index];
        
        // Check if key already exists
        while (entry != null)
        {
            if (entry.Key.Equals(key))
                throw new ArgumentException("Key already exists");
            entry = entry.Next;
        }
        
        // Add new entry
        var newEntry = new Entry(key, value);
        newEntry.Next = buckets[index];
        buckets[index] = newEntry;
        count++;
    }
    
    public TValue Get(TKey key)
    {
        int index = GetBucketIndex(key);
        var entry = buckets[index];
        
        while (entry != null)
        {
            if (entry.Key.Equals(key))
                return entry.Value;
            entry = entry.Next;
        }
        
        throw new KeyNotFoundException($"Key '{key}' not found");
    }
    
    public bool Remove(TKey key)
    {
        int index = GetBucketIndex(key);
        var current = buckets[index];
        Entry previous = null;
        
        while (current != null)
        {
            if (current.Key.Equals(key))
            {
                if (previous == null)
                {
                    buckets[index] = current.Next;
                }
                else
                {
                    previous.Next = current.Next;
                }
                count--;
                return true;
            }
            previous = current;
            current = current.Next;
        }
        
        return false;
    }
    
    public bool ContainsKey(TKey key)
    {
        int index = GetBucketIndex(key);
        var entry = buckets[index];
        
        while (entry != null)
        {
            if (entry.Key.Equals(key))
                return true;
            entry = entry.Next;
        }
        
        return false;
    }
    
    private int GetBucketIndex(TKey key)
    {
        int hashCode = key.GetHashCode();
        return Math.Abs(hashCode) % buckets.Length;
    }
    
    public void Clear()
    {
        Array.Clear(buckets, 0, buckets.Length);
        count = 0;
    }
}

// 6. Graph Implementation (Adjacency List)
public class Graph<T>
{
    private class Edge
    {
        public int Destination { get; set; }
        public int Weight { get; set; }
        
        public Edge(int destination, int weight = 1)
        {
            Destination = destination;
            Weight = weight;
        }
    }
    
    private Dictionary<T, List<Edge>> adjacencyList;
    private Dictionary<T, int> vertexIndices;
    private List<T> vertices;
    
    public int VertexCount => vertices.Count;
    public bool IsDirected { get; }
    
    public Graph(bool isDirected = false)
    {
        adjacencyList = new Dictionary<T, List<Edge>>();
        vertexIndices = new Dictionary<T, int>();
        vertices = new List<T>();
        IsDirected = isDirected;
    }
    
    public void AddVertex(T vertex)
    {
        if (!vertexIndices.ContainsKey(vertex))
        {
            vertexIndices[vertex] = vertices.Count;
            vertices.Add(vertex);
            adjacencyList[vertex] = new List<Edge>();
        }
    }
    
    public void AddEdge(T source, T destination, int weight = 1)
    {
        AddVertex(source);
        AddVertex(destination);
        
        adjacencyList[source].Add(new Edge(vertexIndices[destination], weight));
        
        if (!IsDirected)
        {
            adjacencyList[destination].Add(new Edge(vertexIndices[source], weight));
        }
    }
    
    public bool HasEdge(T source, T destination)
    {
        if (!adjacencyList.ContainsKey(source))
            return false;
        
        return adjacencyList[source].Any(edge => 
            edge.Destination == vertexIndices[destination]);
    }
    
    public List<T> GetNeighbors(T vertex)
    {
        if (!adjacencyList.ContainsKey(vertex))
            return new List<T>();
        
        return adjacencyList[vertex]
            .Select(edge => vertices[edge.Destination])
            .ToList();
    }
    
    public void BFS(T startVertex)
    {
        if (!vertexIndices.ContainsKey(startVertex))
            return;
        
        var visited = new HashSet<T>();
        var queue = new Queue<T>();
        
        visited.Add(startVertex);
        queue.Enqueue(startVertex);
        
        Console.Write($"BFS starting from {startVertex}: ");
        
        while (queue.Count > 0)
        {
            T current = queue.Dequeue();
            Console.Write(current + " ");
            
            foreach (T neighbor in GetNeighbors(current))
            {
                if (!visited.Contains(neighbor))
                {
                    visited.Add(neighbor);
                    queue.Enqueue(neighbor);
                }
            }
        }
        
        Console.WriteLine();
    }
    
    public void DFS(T startVertex)
    {
        if (!vertexIndices.ContainsKey(startVertex))
            return;
        
        var visited = new HashSet<T>();
        DFSRecursive(startVertex, visited);
        Console.WriteLine();
    }
    
    private void DFSRecursive(T vertex, HashSet<T> visited)
    {
        visited.Add(vertex);
        Console.Write(vertex + " ");
        
        foreach (T neighbor in GetNeighbors(vertex))
        {
            if (!visited.Contains(neighbor))
            {
                DFSRecursive(neighbor, visited);
            }
        }
    }
    
    public void PrintGraph()
    {
        Console.WriteLine("Graph Adjacency List:");
        foreach (var vertex in adjacencyList.Keys)
        {
            Console.Write($"{vertex}: ");
            foreach (var edge in adjacencyList[vertex])
            {
                Console.Write($"{vertices[edge.Destination]}(w:{edge.Weight}) ");
            }
            Console.WriteLine();
        }
    }
}

// Data Structures Demonstration
public class DataStructuresDemo
{
    public static void Main(string[] args)
    {
        Console.WriteLine("=== Data Structures Demonstration ===");
        
        // Linked List Demo
        Console.WriteLine("\n--- Linked List ---");
        var linkedList = new CustomLinkedList<int>();
        linkedList.Add(10);
        linkedList.Add(20);
        linkedList.Add(30);
        linkedList.AddFirst(5);
        linkedList.PrintList();
        
        Console.WriteLine($"Contains 20: {linkedList.Contains(20)}");
        Console.WriteLine($"Contains 40: {linkedList.Contains(40)}");
        
        linkedList.Remove(20);
        linkedList.PrintList();
        
        // Stack Demo
        Console.WriteLine("\n--- Stack ---");
        var stack = new CustomStack<string>();
        stack.Push("First");
        stack.Push("Second");
        stack.Push("Third");
        
        Console.WriteLine($"Stack count: {stack.Count}");
        Console.WriteLine($"Top element: {stack.Peek()}");
        
        while (!stack.IsEmpty)
        {
            Console.WriteLine($"Popped: {stack.Pop()}");
        }
        
        // Queue Demo
        Console.WriteLine("\n--- Queue ---");
        var queue = new CustomQueue<int>();
        queue.Enqueue(100);
        queue.Enqueue(200);
        queue.Enqueue(300);
        
        Console.WriteLine($"Queue count: {queue.Count}");
        Console.WriteLine($"Front element: {queue.Peek()}");
        
        while (!queue.IsEmpty)
        {
            Console.WriteLine($"Dequeued: {queue.Dequeue()}");
        }
        
        // Binary Tree Demo
        Console.WriteLine("\n--- Binary Tree ---");
        var binaryTree = new BinaryTree<int>();
        binaryTree.Insert(50);
        binaryTree.Insert(30);
        binaryTree.Insert(70);
        binaryTree.Insert(20);
        binaryTree.Insert(40);
        binaryTree.Insert(60);
        binaryTree.Insert(80);
        
        Console.WriteLine($"Tree height: {binaryTree.GetHeight()}");
        Console.WriteLine($"Node count: {binaryTree.GetNodeCount()}");
        
        binaryTree.InOrderTraversal();
        binaryTree.PreOrderTraversal();
        binaryTree.PostOrderTraversal();
        
        Console.WriteLine($"Search 40: {binaryTree.Search(40)}");
        Console.WriteLine($"Search 90: {binaryTree.Search(90)}");
        
        // Hash Table Demo
        Console.WriteLine("\n--- Hash Table ---");
        var hashTable = new CustomHashTable<string, int>();
        hashTable.Add("apple", 5);
        hashTable.Add("banana", 3);
        hashTable.Add("orange", 7);
        
        Console.WriteLine($"Hash table count: {hashTable.Count}");
        Console.WriteLine($"Apple count: {hashTable.Get("apple")}");
        Console.WriteLine($"Contains banana: {hashTable.ContainsKey("banana")}");
        
        hashTable.Remove("orange");
        Console.WriteLine($"After removing orange - count: {hashTable.Count}");
        Console.WriteLine($"Contains orange: {hashTable.ContainsKey("orange")}");
        
        // Graph Demo
        Console.WriteLine("\n--- Graph ---");
        var graph = new Graph<string>();
        graph.AddEdge("A", "B");
        graph.AddEdge("A", "C");
        graph.AddEdge("B", "D");
        graph.AddEdge("C", "D");
        graph.AddEdge("D", "E");
        
        graph.PrintGraph();
        
        Console.WriteLine($"Neighbors of A: {string.Join(", ", graph.GetNeighbors("A"))}");
        Console.WriteLine($"Has edge A->B: {graph.HasEdge("A", "B")}");
        Console.WriteLine($"Has edge B->A: {graph.HasEdge("B", "A")}");
        
        graph.BFS("A");
        graph.DFS("A");
        
        // Directed Graph Demo
        Console.WriteLine("\n--- Directed Graph ---");
        var directedGraph = new Graph<string>(isDirected: true);
        directedGraph.AddEdge("A", "B");
        directedGraph.AddEdge("B", "C");
        directedGraph.AddEdge("C", "D");
        directedGraph.AddEdge("A", "D");
        
        directedGraph.PrintGraph();
        directedGraph.BFS("A");
        directedGraph.DFS("A");
    }
}
