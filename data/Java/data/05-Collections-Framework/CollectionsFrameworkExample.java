import java.util.*;

public class CollectionsFrameworkExample {
    public static void main(String[] args) {
        // List Interface
        System.out.println("=== List Interface ===");
        demonstrateLists();
        
        // Set Interface
        System.out.println("\n=== Set Interface ===");
        demonstrateSets();
        
        // Map Interface
        System.out.println("\n=== Map Interface ===");
        demonstrateMaps();
        
        // Queue Interface
        System.out.println("\n=== Queue Interface ===");
        demonstrateQueues();
        
        // Collections Utility Class
        System.out.println("\n=== Collections Utility ===");
        demonstrateCollectionsUtility();
        
        // Custom Collections
        System.out.println("\n=== Custom Collections ===");
        demonstrateCustomCollections();
        
        // Performance Comparison
        System.out.println("\n=== Performance Comparison ===");
        performanceComparison();
    }
    
    public static void demonstrateLists() {
        // ArrayList
        List<String> arrayList = new ArrayList<>();
        arrayList.add("Apple");
        arrayList.add("Banana");
        arrayList.add("Orange");
        arrayList.add("Grape");
        arrayList.add(2, "Mango"); // Insert at index
        
        System.out.println("ArrayList: " + arrayList);
        System.out.println("Get element at index 1: " + arrayList.get(1));
        System.out.println("Remove Orange: " + arrayList.remove("Orange"));
        System.out.println("After removal: " + arrayList);
        System.out.println("Contains Banana: " + arrayList.contains("Banana"));
        
        // LinkedList
        List<Integer> linkedList = new LinkedList<>();
        linkedList.add(10);
        linkedList.add(20);
        linkedList.add(30);
        linkedList.addFirst(5);
        linkedList.addLast(40);
        
        System.out.println("\nLinkedList: " + linkedList);
        System.out.println("First element: " + ((LinkedList<Integer>) linkedList).getFirst());
        System.out.println("Last element: " + ((LinkedList<Integer>) linkedList).getLast());
        
        // Vector (Legacy, synchronized)
        Vector<String> vector = new Vector<>();
        vector.add("Vector1");
        vector.add("Vector2");
        vector.add("Vector3");
        
        System.out.println("\nVector: " + vector);
        System.out.println("Vector size: " + vector.size());
        
        // List Iteration
        System.out.println("\nList Iteration:");
        for (String fruit : arrayList) {
            System.out.println("Fruit: " + fruit);
        }
        
        // List Sorting
        List<Integer> numbers = Arrays.asList(5, 2, 8, 1, 9, 3);
        System.out.println("\nOriginal numbers: " + numbers);
        Collections.sort(numbers);
        System.out.println("Sorted numbers: " + numbers);
        
        // List with custom objects
        List<CollectionsStudent> students = new ArrayList<>();
        students.add(new CollectionsStudent("Alice", 85));
        students.add(new CollectionsStudent("Bob", 92));
        students.add(new CollectionsStudent("Charlie", 78));
        
        System.out.println("\nStudents (unsorted): " + students);
        Collections.sort(students);
        System.out.println("Students (sorted by grade): " + students);
    }
    
    public static void demonstrateSets() {
        // HashSet
        Set<String> hashSet = new HashSet<>();
        hashSet.add("Java");
        hashSet.add("Python");
        hashSet.add("JavaScript");
        hashSet.add("Java"); // Duplicate - won't be added
        
        System.out.println("HashSet: " + hashSet);
        System.out.println("Size: " + hashSet.size());
        System.out.println("Contains Python: " + hashSet.contains("Python"));
        
        // LinkedHashSet (maintains insertion order)
        Set<String> linkedHashSet = new LinkedHashSet<>();
        linkedHashSet.add("First");
        linkedHashSet.add("Second");
        linkedHashSet.add("Third");
        linkedHashSet.add("First"); // Duplicate
        
        System.out.println("\nLinkedHashSet: " + linkedHashSet);
        
        // TreeSet (sorted)
        Set<Integer> treeSet = new TreeSet<>();
        treeSet.add(30);
        treeSet.add(10);
        treeSet.add(50);
        treeSet.add(20);
        treeSet.add(40);
        
        System.out.println("\nTreeSet: " + treeSet);
        
        // Set Operations
        Set<Integer> set1 = new HashSet<>(Arrays.asList(1, 2, 3, 4, 5));
        Set<Integer> set2 = new HashSet<>(Arrays.asList(4, 5, 6, 7, 8));
        
        System.out.println("\nSet1: " + set1);
        System.out.println("Set2: " + set2);
        
        // Union
        Set<Integer> union = new HashSet<>(set1);
        union.addAll(set2);
        System.out.println("Union: " + union);
        
        // Intersection
        Set<Integer> intersection = new HashSet<>(set1);
        intersection.retainAll(set2);
        System.out.println("Intersection: " + intersection);
        
        // Difference
        Set<Integer> difference = new HashSet<>(set1);
        difference.removeAll(set2);
        System.out.println("Difference (set1 - set2): " + difference);
    }
    
    public static void demonstrateMaps() {
        // HashMap
        Map<String, Integer> hashMap = new HashMap<>();
        hashMap.put("Alice", 25);
        hashMap.put("Bob", 30);
        hashMap.put("Charlie", 35);
        hashMap.put("Alice", 26); // Update existing key
        
        System.out.println("HashMap: " + hashMap);
        System.out.println("Alice's age: " + hashMap.get("Alice"));
        System.out.println("Contains key Bob: " + hashMap.containsKey("Bob"));
        System.out.println("Contains value 30: " + hashMap.containsValue(30));
        
        // LinkedHashMap (maintains insertion order)
        Map<String, String> linkedHashMap = new LinkedHashMap<>();
        linkedHashMap.put("Country", "USA");
        linkedHashMap.put("State", "California");
        linkedHashMap.put("City", "San Francisco");
        
        System.out.println("\nLinkedHashMap: " + linkedHashMap);
        
        // TreeMap (sorted by keys)
        Map<String, Double> treeMap = new TreeMap<>();
        treeMap.put("Apple", 1.99);
        treeMap.put("Banana", 0.99);
        treeMap.put("Orange", 1.49);
        treeMap.put("Grape", 2.99);
        
        System.out.println("\nTreeMap (sorted by key): " + treeMap);
        
        // Map Iteration
        System.out.println("\nMap Iteration:");
        for (Map.Entry<String, Integer> entry : hashMap.entrySet()) {
            System.out.println(entry.getKey() + " -> " + entry.getValue());
        }
        
        // Word frequency counter
        System.out.println("\nWord Frequency Counter:");
        String text = "Java is great Java is powerful Java is popular";
        Map<String, Integer> wordCount = new HashMap<>();
        
        String[] words = text.toLowerCase().split("\\s+");
        for (String word : words) {
            wordCount.put(word, wordCount.getOrDefault(word, 0) + 1);
        }
        
        System.out.println("Word frequencies: " + wordCount);
    }
    
    public static void demonstrateQueues() {
        // PriorityQueue
        PriorityQueue<Integer> priorityQueue = new PriorityQueue<>();
        priorityQueue.offer(30);
        priorityQueue.offer(10);
        priorityQueue.offer(50);
        priorityQueue.offer(20);
        priorityQueue.offer(40);
        
        System.out.println("PriorityQueue: " + priorityQueue);
        System.out.println("Poll (smallest first): " + priorityQueue.poll());
        System.out.println("After poll: " + priorityQueue);
        
        // ArrayDeque (double-ended queue)
        Deque<String> deque = new ArrayDeque<>();
        deque.addFirst("First");
        deque.addLast("Last");
        deque.addFirst("New First");
        deque.addLast("New Last");
        
        System.out.println("\nArrayDeque: " + deque);
        System.out.println("Remove first: " + deque.removeFirst());
        System.out.println("Remove last: " + deque.removeLast());
        System.out.println("After operations: " + deque);
    }
    
    public static void demonstrateCollectionsUtility() {
        List<Integer> numbers = new ArrayList<>(Arrays.asList(3, 1, 4, 1, 5, 9, 2, 6, 5));
        
        System.out.println("Original list: " + numbers);
        
        // Sorting
        Collections.sort(numbers);
        System.out.println("Sorted: " + numbers);
        
        // Reverse
        Collections.reverse(numbers);
        System.out.println("Reversed: " + numbers);
        
        // Shuffle
        Collections.shuffle(numbers);
        System.out.println("Shuffled: " + numbers);
        
        // Binary search
        Collections.sort(numbers); // Must be sorted first
        int index = Collections.binarySearch(numbers, 5);
        System.out.println("Binary search for 5: " + index);
        
        // Min and Max
        System.out.println("Min: " + Collections.min(numbers));
        System.out.println("Max: " + Collections.max(numbers));
        
        // Frequency
        System.out.println("Frequency of 5: " + Collections.frequency(numbers, 5));
        
        // Unmodifiable collection
        List<Integer> unmodifiable = Collections.unmodifiableList(numbers);
        System.out.println("Unmodifiable list: " + unmodifiable);
        
        try {
            unmodifiable.add(100);
        } catch (UnsupportedOperationException e) {
            System.out.println("Cannot modify unmodifiable collection: " + e.getMessage());
        }
        
        // Synchronized collection
        List<Integer> synchronizedList = Collections.synchronizedList(new ArrayList<>());
        synchronizedList.add(1);
        synchronizedList.add(2);
        System.out.println("Synchronized list: " + synchronizedList);
    }
    
    public static void demonstrateCustomCollections() {
        // Custom comparator
        List<CollectionsPerson> people = new ArrayList<>();
        people.add(new CollectionsPerson("Alice", 25, "Engineer"));
        people.add(new CollectionsPerson("Bob", 30, "Doctor"));
        people.add(new CollectionsPerson("Charlie", 22, "Student"));
        people.add(new CollectionsPerson("David", 28, "Teacher"));
        
        System.out.println("People (unsorted): " + people);
        
        // Sort by age
        Collections.sort(people, java.util.Comparator.comparingInt(CollectionsPerson::getAge));
        System.out.println("People (sorted by age): " + people);
        
        // Sort by name
        Collections.sort(people, java.util.Comparator.comparing(CollectionsPerson::getName));
        System.out.println("People (sorted by name): " + people);
        
        // Custom comparator
        Collections.sort(people, new PersonComparator());
        System.out.println("People (custom sorted): " + people);
    }
    
    public static void performanceComparison() {
        final int OPERATIONS = 100000;
        
        // ArrayList performance
        List<Integer> arrayList = new ArrayList<>();
        long startTime = System.nanoTime();
        
        for (int i = 0; i < OPERATIONS; i++) {
            arrayList.add(i);
        }
        
        for (int i = 0; i < OPERATIONS; i++) {
            arrayList.get(i);
        }
        
        long endTime = System.nanoTime();
        System.out.println("ArrayList (" + OPERATIONS + " operations): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
        
        // LinkedList performance
        List<Integer> linkedList = new LinkedList<>();
        startTime = System.nanoTime();
        
        for (int i = 0; i < OPERATIONS; i++) {
            linkedList.add(i);
        }
        
        for (int i = 0; i < OPERATIONS; i++) {
            linkedList.get(i); // This is slow for LinkedList
        }
        
        endTime = System.nanoTime();
        System.out.println("LinkedList (" + OPERATIONS + " operations): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
        
        // HashMap performance
        Map<Integer, String> hashMap = new HashMap<>();
        startTime = System.nanoTime();
        
        for (int i = 0; i < OPERATIONS; i++) {
            hashMap.put(i, "Value" + i);
        }
        
        for (int i = 0; i < OPERATIONS; i++) {
            hashMap.get(i);
        }
        
        endTime = System.nanoTime();
        System.out.println("HashMap (" + OPERATIONS + " operations): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
    }
}

// Supporting Classes

class CollectionsStudent implements java.lang.Comparable<CollectionsStudent> {
    private String name;
    private int grade;
    
    public CollectionsStudent(String name, int grade) {
        this.name = name;
        this.grade = grade;
    }
    
    @Override
    public int compareTo(CollectionsStudent other) {
        return Integer.compare(this.grade, other.grade);
    }
    
    @Override
    public String toString() {
        return name + "(" + grade + ")";
    }
    
    public String getName() { return name; }
    public int getGrade() { return grade; }
}

class CollectionsPerson {
    private String name;
    private int age;
    private String profession;
    
    public CollectionsPerson(String name, int age, String profession) {
        this.name = name;
        this.age = age;
        this.profession = profession;
    }
    
    @Override
    public String toString() {
        return name + "(" + age + ", " + profession + ")";
    }
    
    public String getName() { return name; }
    public int getAge() { return age; }
    public String getProfession() { return profession; }
}

// Custom Comparator
class PersonComparator implements java.util.Comparator<CollectionsPerson> {
    @Override
    public int compare(CollectionsPerson p1, CollectionsPerson p2) {
        // Sort by profession first, then by age
        int professionCompare = p1.getProfession().compareTo(p2.getProfession());
        if (professionCompare != 0) {
            return professionCompare;
        }
        return Integer.compare(p1.getAge(), p2.getAge());
    }
}
