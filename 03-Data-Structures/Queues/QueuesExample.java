import java.util.LinkedList;
import java.util.Queue;
import java.util.ArrayDeque;
import java.util.PriorityQueue;

public class QueuesExample {
    public static void main(String[] args) {
        // Using Java's built-in Queue
        System.out.println("=== Java Built-in Queue ===");
        Queue<String> queue = new LinkedList<>();
        
        queue.offer("Alice");
        queue.offer("Bob");
        queue.offer("Charlie");
        queue.offer("David");
        
        System.out.println("Queue: " + queue);
        System.out.println("Front element: " + queue.peek());
        System.out.println("Queue size: " + queue.size());
        System.out.println("Is empty: " + queue.isEmpty());
        
        System.out.println("Removed: " + queue.poll());
        System.out.println("After poll: " + queue);
        
        // Custom Queue Implementation
        System.out.println("\n=== Custom Queue Implementation ===");
        CustomQueue<Integer> customQueue = new CustomQueue<>();
        
        customQueue.enqueue(10);
        customQueue.enqueue(20);
        customQueue.enqueue(30);
        customQueue.enqueue(40);
        
        System.out.println("Custom Queue: " + customQueue);
        System.out.println("Front element: " + customQueue.peek());
        System.out.println("Queue size: " + customQueue.getSize());
        
        System.out.println("Dequeued: " + customQueue.dequeue());
        System.out.println("After dequeue: " + customQueue);
        
        // Priority Queue
        System.out.println("\n=== Priority Queue ===");
        PriorityQueue<Integer> priorityQueue = new PriorityQueue<>();
        
        priorityQueue.offer(30);
        priorityQueue.offer(10);
        priorityQueue.offer(40);
        priorityQueue.offer(20);
        
        System.out.println("Priority Queue: " + priorityQueue);
        System.out.println("Poll (smallest first): " + priorityQueue.poll());
        System.out.println("After poll: " + priorityQueue);
        
        // Circular Queue
        System.out.println("\n=== Circular Queue ===");
        CircularQueue<String> circularQueue = new CircularQueue<>(4);
        
        circularQueue.enqueue("Task 1");
        circularQueue.enqueue("Task 2");
        circularQueue.enqueue("Task 3");
        circularQueue.enqueue("Task 4");
        
        System.out.println("Circular Queue: " + circularQueue);
        System.out.println("Is full: " + circularQueue.isFull());
        
        System.out.println("Dequeued: " + circularQueue.dequeue());
        System.out.println("After dequeue: " + circularQueue);
        
        // Enqueue more to show circular behavior
        circularQueue.enqueue("Task 5");
        System.out.println("After enqueuing Task 5: " + circularQueue);
        
        // Deque (Double-ended Queue)
        System.out.println("\n=== Deque (Double-ended Queue) ===");
        ArrayDeque<String> deque = new ArrayDeque<>();
        
        deque.addFirst("First");
        deque.addLast("Last");
        deque.addFirst("New First");
        deque.addLast("New Last");
        
        System.out.println("Deque: " + deque);
        System.out.println("First element: " + deque.getFirst());
        System.out.println("Last element: " + deque.getLast());
        
        System.out.println("Remove first: " + deque.removeFirst());
        System.out.println("Remove last: " + deque.removeLast());
        System.out.println("After operations: " + deque);
        
        // Queue Applications
        System.out.println("\n=== Queue Applications ===");
        
        // Task scheduling
        System.out.println("Task Scheduling:");
        taskScheduling();
        
        // Print queue simulation
        System.out.println("\nPrint Queue Simulation:");
        printQueueSimulation();
        
        // Performance comparison
        System.out.println("\n=== Performance Comparison ===");
        performanceComparison();
    }
    
    // Task scheduling using queue
    public static void taskScheduling() {
        Queue<Task> taskQueue = new LinkedList<>();
        
        // Add tasks
        taskQueue.offer(new Task("Process Data", 3));
        taskQueue.offer(new Task("Send Email", 1));
        taskQueue.offer(new Task("Generate Report", 2));
        taskQueue.offer(new Task("Backup Database", 4));
        
        System.out.println("Tasks in queue:");
        while (!taskQueue.isEmpty()) {
            Task task = taskQueue.poll();
            System.out.println("Executing: " + task);
        }
    }
    
    // Print queue simulation
    public static void printQueueSimulation() {
        Queue<String> printQueue = new LinkedList<>();
        
        // Add print jobs
        printQueue.offer("Document1.pdf");
        printQueue.offer("Image.jpg");
        printQueue.offer("Presentation.pptx");
        printQueue.offer("Spreadsheet.xlsx");
        
        System.out.println("Print jobs in queue:");
        int jobNumber = 1;
        while (!printQueue.isEmpty()) {
            String document = printQueue.poll();
            System.out.println("Job " + jobNumber + ": Printing " + document);
            jobNumber++;
        }
    }
    
    public static void performanceComparison() {
        final int OPERATIONS = 100000;
        
        // Test Java Queue
        Queue<Integer> javaQueue = new LinkedList<>();
        long startTime = System.nanoTime();
        
        for (int i = 0; i < OPERATIONS; i++) {
            javaQueue.offer(i);
        }
        
        for (int i = 0; i < OPERATIONS; i++) {
            javaQueue.poll();
        }
        
        long endTime = System.nanoTime();
        System.out.println("Java Queue (" + OPERATIONS + " operations): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
        
        // Test Custom Queue
        CustomQueue<Integer> customQueue = new CustomQueue<>();
        startTime = System.nanoTime();
        
        for (int i = 0; i < OPERATIONS; i++) {
            customQueue.enqueue(i);
        }
        
        for (int i = 0; i < OPERATIONS; i++) {
            customQueue.dequeue();
        }
        
        endTime = System.nanoTime();
        System.out.println("Custom Queue (" + OPERATIONS + " operations): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
    }
}

// Task class for scheduling
class Task {
    private String name;
    private int priority;
    
    public Task(String name, int priority) {
        this.name = name;
        this.priority = priority;
    }
    
    @Override
    public String toString() {
        return name + " (Priority: " + priority + ")";
    }
    
    public String getName() { return name; }
    public int getPriority() { return priority; }
}

// Custom Queue Implementation using Linked List
class CustomQueue<T> {
    private Node<T> front;
    private Node<T> rear;
    private int size;
    
    private static class Node<T> {
        T data;
        Node<T> next;
        
        Node(T data) {
            this.data = data;
            this.next = null;
        }
    }
    
    public CustomQueue() {
        front = null;
        rear = null;
        size = 0;
    }
    
    // Add element to rear
    public void enqueue(T data) {
        Node<T> newNode = new Node<>(data);
        
        if (isEmpty()) {
            front = rear = newNode;
        } else {
            rear.next = newNode;
            rear = newNode;
        }
        size++;
    }
    
    // Remove element from front
    public T dequeue() {
        if (isEmpty()) {
            throw new IllegalStateException("Queue is empty");
        }
        
        T data = front.data;
        front = front.next;
        
        if (front == null) {
            rear = null;
        }
        
        size--;
        return data;
    }
    
    // Peek at front element
    public T peek() {
        if (isEmpty()) {
            throw new IllegalStateException("Queue is empty");
        }
        
        return front.data;
    }
    
    // Check if empty
    public boolean isEmpty() {
        return front == null;
    }
    
    // Get size
    public int getSize() {
        return size;
    }
    
    // Convert to string
    @Override
    public String toString() {
        StringBuilder sb = new StringBuilder();
        sb.append("[");
        
        Node<T> current = front;
        while (current != null) {
            sb.append(current.data);
            if (current.next != null) {
                sb.append(", ");
            }
            current = current.next;
        }
        
        sb.append("]");
        return sb.toString();
    }
}

// Circular Queue Implementation
class CircularQueue<T> {
    private T[] array;
    private int front;
    private int rear;
    private int capacity;
    private int size;
    
    @SuppressWarnings("unchecked")
    public CircularQueue(int capacity) {
        this.capacity = capacity;
        this.array = (T[]) new Object[capacity];
        this.front = 0;
        this.rear = -1;
        this.size = 0;
    }
    
    // Add element to queue
    public void enqueue(T data) {
        if (isFull()) {
            throw new IllegalStateException("Queue is full");
        }
        
        rear = (rear + 1) % capacity;
        array[rear] = data;
        size++;
    }
    
    // Remove element from queue
    public T dequeue() {
        if (isEmpty()) {
            throw new IllegalStateException("Queue is empty");
        }
        
        T data = array[front];
        array[front] = null; // Avoid memory leak
        front = (front + 1) % capacity;
        size--;
        return data;
    }
    
    // Peek at front element
    public T peek() {
        if (isEmpty()) {
            throw new IllegalStateException("Queue is empty");
        }
        
        return array[front];
    }
    
    // Check if empty
    public boolean isEmpty() {
        return size == 0;
    }
    
    // Check if full
    public boolean isFull() {
        return size == capacity;
    }
    
    // Get size
    public int getSize() {
        return size;
    }
    
    // Convert to string
    @Override
    public String toString() {
        StringBuilder sb = new StringBuilder();
        sb.append("[");
        
        for (int i = 0; i < size; i++) {
            int index = (front + i) % capacity;
            sb.append(array[index]);
            if (i < size - 1) {
                sb.append(", ");
            }
        }
        
        sb.append("]");
        return sb.toString();
    }
}
