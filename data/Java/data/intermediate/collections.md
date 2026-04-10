# Java Collections Framework

## List Interface

### ArrayList
```java
import java.util.ArrayList;
import java.util.List;

// Create ArrayList
List<String> names = new ArrayList<>();
List<Integer> numbers = new ArrayList<>();

// Add elements
names.add("Alice");
names.add("Bob");
names.add("Charlie");

// Add at specific index
names.add(1, "David");

// Access elements
String first = names.get(0);
String last = names.get(names.size() - 1);

// Modify elements
names.set(0, "Alice Smith");

// Remove elements
names.remove("Bob");
names.remove(0); // Remove by index

// Check if contains
boolean hasAlice = names.contains("Alice");

// Get size
int size = names.size();

// Iterate
for (String name : names) {
    System.out.println(name);
}

// Clear all elements
names.clear();
```

### LinkedList
```java
import java.util.LinkedList;

// Create LinkedList
LinkedList<String> list = new LinkedList<>();

// Add elements
list.add("First");
list.add("Second");
list.add("Third");

// Add to beginning and end
list.addFirst("Beginning");
list.addLast("End");

// Remove from beginning and end
list.removeFirst();
list.removeLast();

// Get first and last elements
String first = list.getFirst();
String last = list.getLast();
```

## Set Interface

### HashSet
```java
import java.util.HashSet;
import java.util.Set;

// Create HashSet
Set<String> uniqueNames = new HashSet<>();

// Add elements (duplicates are ignored)
uniqueNames.add("Alice");
uniqueNames.add("Bob");
uniqueNames.add("Alice"); // Duplicate - ignored

// Check if contains
boolean hasBob = uniqueNames.contains("Bob");

// Get size
int size = uniqueNames.size();

// Remove element
uniqueNames.remove("Bob");

// Iterate
for (String name : uniqueNames) {
    System.out.println(name);
}

// Clear all
uniqueNames.clear();
```

### TreeSet
```java
import java.util.TreeSet;
import java.util.Set;

// Create TreeSet (sorted)
Set<Integer> sortedNumbers = new TreeSet<>();

// Add elements (automatically sorted)
sortedNumbers.add(5);
sortedNumbers.add(2);
sortedNumbers.add(8);
sortedNumbers.add(1);

// Get first and last (smallest and largest)
int first = sortedNumbers.first();
int last = sortedNumbers.last();

// Get subset
Set<Integer> subset = sortedNumbers.headSet(5); // Elements < 5
Set<Integer> tailset = sortedNumbers.tailSet(5); // Elements >= 5
```

## Map Interface

### HashMap
```java
import java.util.HashMap;
import java.util.Map;

// Create HashMap
Map<String, Integer> scores = new HashMap<>();

// Put key-value pairs
scores.put("Alice", 85);
scores.put("Bob", 92);
scores.put("Charlie", 78);

// Get value by key
Integer aliceScore = scores.get("Alice");

// Check if key exists
boolean hasAlice = scores.containsKey("Alice");

// Check if value exists
boolean hasScore92 = scores.containsValue(92);

// Get all keys
Set<String> keys = scores.keySet();

// Get all values
Collection<Integer> values = scores.values();

// Get all entries
Set<Map.Entry<String, Integer>> entries = scores.entrySet();

// Iterate over entries
for (Map.Entry<String, Integer> entry : scores.entrySet()) {
    System.out.println(entry.getKey() + ": " + entry.getValue());
}

// Remove entry
scores.remove("Bob");

// Replace value
scores.replace("Alice", 90);

// Get or default
Integer score = scores.getOrDefault("David", 0);

// Put if absent
scores.putIfAbsent("David", 88);
```

### TreeMap
```java
import java.util.TreeMap;

// Create TreeMap (sorted by key)
TreeMap<String, Integer> sortedScores = new TreeMap<>();

// Add elements (automatically sorted by key)
sortedScores.put("Charlie", 78);
sortedScores.put("Alice", 85);
sortedScores.put("Bob", 92);

// Get first and last keys
String firstKey = sortedScores.firstKey();
String lastKey = sortedScores.lastKey();

// Get submap
Map<String, Integer> submap = sortedScores.subMap("Alice", "Charlie");
```

## Queue Interface

### LinkedList as Queue
```java
import java.util.Queue;
import java.util.LinkedList;

// Create Queue
Queue<String> queue = new LinkedList<>();

// Add elements
queue.offer("First");
queue.offer("Second");
queue.offer("Third");

// Peek at front element (without removing)
String front = queue.peek();

// Remove and return front element
String removed = queue.poll();

// Check if empty
boolean empty = queue.isEmpty();

// Get size
int size = queue.size();
```

### PriorityQueue
```java
import java.util.PriorityQueue;

// Create PriorityQueue (sorted)
PriorityQueue<Integer> pq = new PriorityQueue<>();

// Add elements
pq.offer(5);
pq.offer(2);
pq.offer(8);
pq.offer(1);

// Elements are automatically sorted
while (!pq.isEmpty()) {
    System.out.println(pq.poll()); // Prints in sorted order
}
```

## Stack

### Stack Class
```java
import java.util.Stack;

// Create Stack
Stack<String> stack = new Stack<>();

// Push elements
stack.push("First");
stack.push("Second");
stack.push("Third");

// Peek at top element
String top = stack.peek();

// Pop top element
String popped = stack.pop();

// Check if empty
boolean empty = stack.isEmpty();

// Search for element
int position = stack.search("First");
```

## Collections Utility Class

### Sorting and Searching
```java
import java.util.Collections;
import java.util.List;
import java.util.ArrayList;

// Create list
List<Integer> numbers = new ArrayList<>();
numbers.add(5);
numbers.add(2);
numbers.add(8);
numbers.add(1);

// Sort list
Collections.sort(numbers);

// Reverse list
Collections.reverse(numbers);

// Shuffle list
Collections.shuffle(numbers);

// Binary search (list must be sorted)
int index = Collections.binarySearch(numbers, 5);

// Find max and min
int max = Collections.max(numbers);
int min = Collections.min(numbers);

// Fill list with value
Collections.fill(numbers, 0);

// Create unmodifiable list
List<Integer> unmodifiable = Collections.unmodifiableList(numbers);
```

### Synchronized Collections
```java
// Create synchronized collections
List<String> syncList = Collections.synchronizedList(new ArrayList<>());
Set<String> syncSet = Collections.synchronizedSet(new HashSet<>());
Map<String, Integer> syncMap = Collections.synchronizedMap(new HashMap<>());

// Must use synchronized block when iterating
synchronized (syncList) {
    for (String item : syncList) {
        System.out.println(item);
    }
}
```

## Custom Objects in Collections

### Comparable Interface
```java
public class Person implements Comparable<Person> {
    private String name;
    private int age;
    
    public Person(String name, int age) {
        this.name = name;
        this.age = age;
    }
    
    @Override
    public int compareTo(Person other) {
        return this.name.compareTo(other.name); // Sort by name
    }
    
    @Override
    public String toString() {
        return name + " (" + age + ")";
    }
    
    // Getters
    public String getName() { return name; }
    public int getAge() { return age; }
}

// Using in collections
List<Person> people = new ArrayList<>();
people.add(new Person("Alice", 30));
people.add(new Person("Bob", 25));
people.add(new Person("Charlie", 35));

// Sort using natural ordering
Collections.sort(people);
```

### Comparator Interface
```java
import java.util.Comparator;

// Custom comparator
class AgeComparator implements Comparator<Person> {
    @Override
    public int compare(Person p1, Person p2) {
        return Integer.compare(p1.getAge(), p2.getAge());
    }
}

// Using comparator
List<Person> people = new ArrayList<>();
// ... add people

// Sort by age using comparator
Collections.sort(people, new AgeComparator());

// Lambda comparator (Java 8+)
Collections.sort(people, (p1, p2) -> Integer.compare(p1.getAge(), p2.getAge()));
```

## Examples

### Collection Examples
```java
import java.util.*;

public class CollectionExamples {
    public static void main(String[] args) {
        // List example
        List<String> fruits = new ArrayList<>();
        fruits.add("Apple");
        fruits.add("Banana");
        fruits.add("Orange");
        fruits.add("Apple"); // Duplicate allowed
        
        System.out.println("List:");
        for (String fruit : fruits) {
            System.out.println(fruit);
        }
        
        // Set example
        Set<String> uniqueFruits = new HashSet<>();
        uniqueFruits.add("Apple");
        uniqueFruits.add("Banana");
        uniqueFruits.add("Orange");
        uniqueFruits.add("Apple"); // Duplicate ignored
        
        System.out.println("\nSet:");
        for (String fruit : uniqueFruits) {
            System.out.println(fruit);
        }
        
        // Map example
        Map<String, Integer> fruitPrices = new HashMap<>();
        fruitPrices.put("Apple", 50);
        fruitPrices.put("Banana", 30);
        fruitPrices.put("Orange", 40);
        
        System.out.println("\nMap:");
        for (Map.Entry<String, Integer> entry : fruitPrices.entrySet()) {
            System.out.println(entry.getKey() + ": $" + entry.getValue());
        }
        
        // Queue example
        Queue<String> queue = new LinkedList<>();
        queue.offer("First");
        queue.offer("Second");
        queue.offer("Third");
        
        System.out.println("\nQueue:");
        while (!queue.isEmpty()) {
            System.out.println("Processing: " + queue.poll());
        }
        
        // Stack example
        Stack<String> stack = new Stack<>();
        stack.push("First");
        stack.push("Second");
        stack.push("Third");
        
        System.out.println("\nStack:");
        while (!stack.isEmpty()) {
            System.out.println("Popping: " + stack.pop());
        }
    }
}
```

## Best Practices
- Choose the right collection type for your needs
- Use ArrayList for random access, LinkedList for frequent insertions/deletions
- Use HashSet for uniqueness, TreeSet for sorted unique elements
- Use HashMap for key-value pairs, TreeMap for sorted keys
- Use generics for type safety
- Consider memory usage for large collections
- Use enhanced for loop for iteration
- Use Collections utility class for common operations
- Implement equals() and hashCode() for custom objects
- Use appropriate initial capacity for better performance
