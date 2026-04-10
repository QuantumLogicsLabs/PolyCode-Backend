public class LinkedListsExample {
    public static void main(String[] args) {
        // Singly Linked List
        System.out.println("=== Singly Linked List ===");
        SinglyLinkedList<Integer> singlyList = new SinglyLinkedList<>();
        
        singlyList.insertAtBeginning(10);
        singlyList.insertAtBeginning(20);
        singlyList.insertAtEnd(30);
        singlyList.insertAtEnd(40);
        singlyList.insertAtPosition(2, 25);
        
        System.out.println("Singly List: " + singlyList);
        System.out.println("Size: " + singlyList.getSize());
        System.out.println("Contains 25: " + singlyList.contains(25));
        System.out.println("Contains 99: " + singlyList.contains(99));
        
        singlyList.deleteAtBeginning();
        singlyList.deleteAtEnd();
        singlyList.deleteAtPosition(1);
        
        System.out.println("After deletions: " + singlyList);
        
        // Doubly Linked List
        System.out.println("\n=== Doubly Linked List ===");
        DoublyLinkedList<Integer> doublyList = new DoublyLinkedList<>();
        
        doublyList.insertAtBeginning(5);
        doublyList.insertAtEnd(15);
        doublyList.insertAtEnd(25);
        doublyList.insertAtPosition(1, 10);
        doublyList.insertAtPosition(3, 20);
        
        System.out.println("Doubly List: " + doublyList);
        System.out.println("Forward traversal: " + doublyList.traverseForward());
        System.out.println("Backward traversal: " + doublyList.traverseBackward());
        
        doublyList.deleteAtPosition(2);
        System.out.println("After deleting position 2: " + doublyList);
        
        // Circular Linked List
        System.out.println("\n=== Circular Linked List ===");
        CircularLinkedList<Integer> circularList = new CircularLinkedList<>();
        
        circularList.insert(1);
        circularList.insert(2);
        circularList.insert(3);
        circularList.insert(4);
        circularList.insert(5);
        
        System.out.println("Circular List: " + circularList);
        System.out.println("Size: " + circularList.getSize());
        
        circularList.delete(3);
        System.out.println("After deleting 3: " + circularList);
        
        // Performance comparison
        System.out.println("\n=== Performance Comparison ===");
        demonstratePerformance();
        
        // Real-world applications
        System.out.println("\n=== Real-world Applications ===");
        demonstrateApplications();
    }
    
    public static void demonstratePerformance() {
        final int SIZE = 10000;
        
        // Singly LinkedList performance
        SinglyLinkedList<Integer> list = new SinglyLinkedList<>();
        long startTime = System.nanoTime();
        
        for (int i = 0; i < SIZE; i++) {
            list.insertAtEnd(i);
        }
        
        long endTime = System.nanoTime();
        System.out.println("Singly LinkedList insertion (" + SIZE + " elements): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
        
        // Search performance
        startTime = System.nanoTime();
        boolean found = list.contains(SIZE - 1);
        endTime = System.nanoTime();
        System.out.println("Singly LinkedList search (last element): " + 
                          (endTime - startTime) / 1000000.0 + " ms, Found: " + found);
    }
    
    public static void demonstrateApplications() {
        // Browser history (using doubly linked list)
        DoublyLinkedList<String> browserHistory = new DoublyLinkedList<>();
        browserHistory.insertAtEnd("google.com");
        browserHistory.insertAtEnd("stackoverflow.com");
        browserHistory.insertAtEnd("github.com");
        browserHistory.insertAtEnd("youtube.com");
        
        System.out.println("Browser history: " + browserHistory.traverseForward());
        
        // Music playlist (using circular linked list)
        CircularLinkedList<String> playlist = new CircularLinkedList<>();
        playlist.insert("Song 1");
        playlist.insert("Song 2");
        playlist.insert("Song 3");
        
        System.out.println("Music playlist: " + playlist);
        System.out.println("Circular playback enabled");
    }
}

// Singly Linked List Implementation
class SinglyLinkedList<T> {
    private Node<T> head;
    private int size;
    
    private static class Node<T> {
        T data;
        Node<T> next;
        
        Node(T data) {
            this.data = data;
            this.next = null;
        }
    }
    
    public SinglyLinkedList() {
        head = null;
        size = 0;
    }
    
    // Insert at beginning
    public void insertAtBeginning(T data) {
        Node<T> newNode = new Node<>(data);
        newNode.next = head;
        head = newNode;
        size++;
    }
    
    // Insert at end
    public void insertAtEnd(T data) {
        Node<T> newNode = new Node<>(data);
        
        if (head == null) {
            head = newNode;
        } else {
            Node<T> current = head;
            while (current.next != null) {
                current = current.next;
            }
            current.next = newNode;
        }
        size++;
    }
    
    // Insert at specific position
    public void insertAtPosition(int position, T data) {
        if (position < 0 || position > size) {
            throw new IndexOutOfBoundsException("Invalid position");
        }
        
        if (position == 0) {
            insertAtBeginning(data);
            return;
        }
        
        Node<T> newNode = new Node<>(data);
        Node<T> current = head;
        
        for (int i = 0; i < position - 1; i++) {
            current = current.next;
        }
        
        newNode.next = current.next;
        current.next = newNode;
        size++;
    }
    
    // Delete at beginning
    public void deleteAtBeginning() {
        if (head == null) {
            throw new IllegalStateException("List is empty");
        }
        
        head = head.next;
        size--;
    }
    
    // Delete at end
    public void deleteAtEnd() {
        if (head == null) {
            throw new IllegalStateException("List is empty");
        }
        
        if (head.next == null) {
            head = null;
        } else {
            Node<T> current = head;
            while (current.next.next != null) {
                current = current.next;
            }
            current.next = null;
        }
        size--;
    }
    
    // Delete at specific position
    public void deleteAtPosition(int position) {
        if (position < 0 || position >= size) {
            throw new IndexOutOfBoundsException("Invalid position");
        }
        
        if (position == 0) {
            deleteAtBeginning();
            return;
        }
        
        Node<T> current = head;
        for (int i = 0; i < position - 1; i++) {
            current = current.next;
        }
        
        current.next = current.next.next;
        size--;
    }
    
    // Search for element
    public boolean contains(T data) {
        Node<T> current = head;
        while (current != null) {
            if (current.data.equals(data)) {
                return true;
            }
            current = current.next;
        }
        return false;
    }
    
    // Get size
    public int getSize() {
        return size;
    }
    
    // Check if empty
    public boolean isEmpty() {
        return head == null;
    }
    
    // Convert to string
    @Override
    public String toString() {
        StringBuilder sb = new StringBuilder();
        sb.append("[");
        Node<T> current = head;
        
        while (current != null) {
            sb.append(current.data);
            if (current.next != null) {
                sb.append(" -> ");
            }
            current = current.next;
        }
        
        sb.append("]");
        return sb.toString();
    }
}

// Doubly Linked List Implementation
class DoublyLinkedList<T> {
    private DoublyNode<T> head;
    private DoublyNode<T> tail;
    private int size;
    
    private static class DoublyNode<T> {
        T data;
        DoublyNode<T> prev;
        DoublyNode<T> next;
        
        DoublyNode(T data) {
            this.data = data;
            this.prev = null;
            this.next = null;
        }
    }
    
    public DoublyLinkedList() {
        head = null;
        tail = null;
        size = 0;
    }
    
    // Insert at beginning
    public void insertAtBeginning(T data) {
        DoublyNode<T> newNode = new DoublyNode<>(data);
        
        if (head == null) {
            head = tail = newNode;
        } else {
            newNode.next = head;
            head.prev = newNode;
            head = newNode;
        }
        size++;
    }
    
    // Insert at end
    public void insertAtEnd(T data) {
        DoublyNode<T> newNode = new DoublyNode<>(data);
        
        if (tail == null) {
            head = tail = newNode;
        } else {
            tail.next = newNode;
            newNode.prev = tail;
            tail = newNode;
        }
        size++;
    }
    
    // Insert at specific position
    public void insertAtPosition(int position, T data) {
        if (position < 0 || position > size) {
            throw new IndexOutOfBoundsException("Invalid position");
        }
        
        if (position == 0) {
            insertAtBeginning(data);
            return;
        }
        
        if (position == size) {
            insertAtEnd(data);
            return;
        }
        
        DoublyNode<T> newNode = new DoublyNode<>(data);
        DoublyNode<T> current = head;
        
        for (int i = 0; i < position; i++) {
            current = current.next;
        }
        
        newNode.prev = current.prev;
        newNode.next = current;
        current.prev.next = newNode;
        current.prev = newNode;
        size++;
    }
    
    // Delete at specific position
    public void deleteAtPosition(int position) {
        if (position < 0 || position >= size) {
            throw new IndexOutOfBoundsException("Invalid position");
        }
        
        if (position == 0) {
            deleteAtBeginning();
            return;
        }
        
        if (position == size - 1) {
            deleteAtEnd();
            return;
        }
        
        DoublyNode<T> current = head;
        for (int i = 0; i < position; i++) {
            current = current.next;
        }
        
        current.prev.next = current.next;
        current.next.prev = current.prev;
        size--;
    }
    
    // Delete at beginning
    public void deleteAtBeginning() {
        if (head == null) {
            throw new IllegalStateException("List is empty");
        }
        
        if (head == tail) {
            head = tail = null;
        } else {
            head = head.next;
            head.prev = null;
        }
        size--;
    }
    
    // Delete at end
    public void deleteAtEnd() {
        if (tail == null) {
            throw new IllegalStateException("List is empty");
        }
        
        if (head == tail) {
            head = tail = null;
        } else {
            tail = tail.prev;
            tail.next = null;
        }
        size--;
    }
    
    // Traverse forward
    public String traverseForward() {
        StringBuilder sb = new StringBuilder();
        sb.append("[");
        DoublyNode<T> current = head;
        
        while (current != null) {
            sb.append(current.data);
            if (current.next != null) {
                sb.append(" <-> ");
            }
            current = current.next;
        }
        
        sb.append("]");
        return sb.toString();
    }
    
    // Traverse backward
    public String traverseBackward() {
        StringBuilder sb = new StringBuilder();
        sb.append("[");
        DoublyNode<T> current = tail;
        
        while (current != null) {
            sb.append(current.data);
            if (current.prev != null) {
                sb.append(" <-> ");
            }
            current = current.prev;
        }
        
        sb.append("]");
        return sb.toString();
    }
    
    // Get size
    public int getSize() {
        return size;
    }
    
    // Convert to string
    @Override
    public String toString() {
        return traverseForward();
    }
}

// Circular Linked List Implementation
class CircularLinkedList<T> {
    private CircularNode<T> head;
    private int size;
    
    private static class CircularNode<T> {
        T data;
        CircularNode<T> next;
        
        CircularNode(T data) {
            this.data = data;
            this.next = null;
        }
    }
    
    public CircularLinkedList() {
        head = null;
        size = 0;
    }
    
    // Insert element
    public void insert(T data) {
        CircularNode<T> newNode = new CircularNode<>(data);
        
        if (head == null) {
            head = newNode;
            newNode.next = head; // Point to itself
        } else {
            CircularNode<T> current = head;
            while (current.next != head) {
                current = current.next;
            }
            current.next = newNode;
            newNode.next = head;
        }
        size++;
    }
    
    // Delete element
    public void delete(T data) {
        if (head == null) {
            throw new IllegalStateException("List is empty");
        }
        
        CircularNode<T> current = head;
        CircularNode<T> prev = null;
        
        // Find the node to delete
        do {
            if (current.data.equals(data)) {
                break;
            }
            prev = current;
            current = current.next;
        } while (current != head);
        
        // If element not found
        if (!current.data.equals(data)) {
            return;
        }
        
        // If only one element
        if (size == 1) {
            head = null;
        } else if (prev == null) {
            // Delete head
            CircularNode<T> last = head;
            while (last.next != head) {
                last = last.next;
            }
            head = head.next;
            last.next = head;
        } else {
            // Delete middle or last element
            prev.next = current.next;
        }
        
        size--;
    }
    
    // Search for element
    public boolean contains(T data) {
        if (head == null) {
            return false;
        }
        
        CircularNode<T> current = head;
        do {
            if (current.data.equals(data)) {
                return true;
            }
            current = current.next;
        } while (current != head);
        
        return false;
    }
    
    // Get size
    public int getSize() {
        return size;
    }
    
    // Convert to string
    @Override
    public String toString() {
        if (head == null) {
            return "[]";
        }
        
        StringBuilder sb = new StringBuilder();
        sb.append("[");
        CircularNode<T> current = head;
        
        do {
            sb.append(current.data);
            current = current.next;
            if (current != head) {
                sb.append(" -> ");
            }
        } while (current != head);
        
        sb.append("]");
        return sb.toString();
    }
}
