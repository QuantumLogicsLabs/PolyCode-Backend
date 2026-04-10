# Java Generics

## Generic Classes

### Basic Generic Class
```java
public class Box<T> {
    private T content;
    
    public Box() {
        this.content = null;
    }
    
    public Box(T content) {
        this.content = content;
    }
    
    public void setContent(T content) {
        this.content = content;
    }
    
    public T getContent() {
        return content;
    }
    
    public boolean isEmpty() {
        return content == null;
    }
    
    @Override
    public String toString() {
        return "Box contains: " + content;
    }
}

// Using generic class
public class GenericClassDemo {
    public static void main(String[] args) {
        // Box for String
        Box<String> stringBox = new Box<>("Hello World");
        System.out.println(stringBox);
        
        // Box for Integer
        Box<Integer> integerBox = new Box<>(42);
        System.out.println(integerBox);
        
        // Box for custom object
        Box<Person> personBox = new Box<>(new Person("Alice", 30));
        System.out.println(personBox);
    }
}
```

### Multiple Type Parameters
```java
public class Pair<K, V> {
    private K key;
    private V value;
    
    public Pair(K key, V value) {
        this.key = key;
        this.value = value;
    }
    
    public K getKey() { return key; }
    public void setKey(K key) { this.key = key; }
    
    public V getValue() { return value; }
    public void setValue(V value) { this.value = value; }
    
    @Override
    public String toString() {
        return "Pair[key=" + key + ", value=" + value + "]";
    }
}

// Using multiple type parameters
public class PairDemo {
    public static void main(String[] args) {
        Pair<String, Integer> pair1 = new Pair<>("Age", 30);
        Pair<Integer, String> pair2 = new Pair<>(100, "Success");
        Pair<String, Double> pair3 = new Pair<>("Price", 99.99);
        
        System.out.println(pair1);
        System.out.println(pair2);
        System.out.println(pair3);
    }
}
```

## Generic Methods

### Basic Generic Methods
```java
public class GenericMethods {
    
    // Generic method to swap two elements
    public static <T> void swap(T[] array, int i, int j) {
        T temp = array[i];
        array[i] = array[j];
        array[j] = temp;
    }
    
    // Generic method to find maximum
    public static <T extends Comparable<T>> T findMax(T[] array) {
        if (array == null || array.length == 0) {
            return null;
        }
        
        T max = array[0];
        for (int i = 1; i < array.length; i++) {
            if (array[i].compareTo(max) > 0) {
                max = array[i];
            }
        }
        return max;
    }
    
    // Generic method to print array
    public static <T> void printArray(T[] array) {
        for (T element : array) {
            System.out.print(element + " ");
        }
        System.out.println();
    }
    
    // Generic method with multiple parameters
    public static <T, U> boolean compare(T obj1, U obj2) {
        return obj1.equals(obj2);
    }
    
    public static void main(String[] args) {
        // Test with Integer array
        Integer[] numbers = {3, 1, 4, 1, 5, 9};
        System.out.print("Original: ");
        printArray(numbers);
        
        swap(numbers, 0, 1);
        System.out.print("After swap: ");
        printArray(numbers);
        
        Integer max = findMax(numbers);
        System.out.println("Maximum: " + max);
        
        // Test with String array
        String[] names = {"Alice", "Bob", "Charlie"};
        System.out.print("Names: ");
        printArray(names);
        
        String maxName = findMax(names);
        System.out.println("Maximum name: " + maxName);
    }
}
```

## Bounded Type Parameters

### Upper Bounded Generics
```java
public class BoundedGenerics {
    
    // Generic method with upper bound
    public static <T extends Number> double sum(T[] numbers) {
        double total = 0.0;
        for (T num : numbers) {
            total += num.doubleValue();
        }
        return total;
    }
    
    // Generic class with upper bound
    public static class NumberBox<T extends Number> {
        private T number;
        
        public NumberBox(T number) {
            this.number = number;
        }
        
        public T getNumber() { return number; }
        
        public void setNumber(T number) { this.number = number; }
        
        public double getDoubleValue() {
            return number.doubleValue();
        }
        
        @Override
        public String toString() {
            return "NumberBox[" + number + "]";
        }
    }
    
    // Generic method with multiple bounds
    public static <T extends Number & Comparable<T>> T findMaxNumber(T[] numbers) {
        if (numbers == null || numbers.length == 0) {
            return null;
        }
        
        T max = numbers[0];
        for (int i = 1; i < numbers.length; i++) {
            if (numbers[i].compareTo(max) > 0) {
                max = numbers[i];
            }
        }
        return max;
    }
    
    public static void main(String[] args) {
        // Test with different number types
        Integer[] integers = {1, 2, 3, 4, 5};
        Double[] doubles = {1.1, 2.2, 3.3, 4.4, 5.5};
        
        System.out.println("Sum of integers: " + sum(integers));
        System.out.println("Sum of doubles: " + sum(doubles));
        
        // Test NumberBox
        NumberBox<Integer> intBox = new NumberBox<>(42);
        NumberBox<Double> doubleBox = new NumberBox<>(3.14);
        
        System.out.println(intBox + " -> " + intBox.getDoubleValue());
        System.out.println(doubleBox + " -> " + doubleBox.getDoubleValue());
        
        // Test bounded method
        Integer maxInt = findMaxNumber(integers);
        System.out.println("Max integer: " + maxInt);
    }
}
```

## Wildcards

### Unbounded Wildcards
```java
public class WildcardExamples {
    
    // Method with unbounded wildcard
    public static void printList(List<?> list) {
        for (Object element : list) {
            System.out.print(element + " ");
        }
        System.out.println();
    }
    
    // Method to get size of any list
    public static int getSize(List<?> list) {
        return list.size();
    }
    
    public static void main(String[] args) {
        List<String> strings = Arrays.asList("A", "B", "C");
        List<Integer> integers = Arrays.asList(1, 2, 3);
        List<Double> doubles = Arrays.asList(1.1, 2.2, 3.3);
        
        printList(strings);
        printList(integers);
        printList(doubles);
        
        System.out.println("String list size: " + getSize(strings));
        System.out.println("Integer list size: " + getSize(integers));
    }
}
```

### Upper Bounded Wildcards
```java
public class UpperBoundedWildcards {
    
    // Method with upper bounded wildcard
    public static double sumOfList(List<? extends Number> list) {
        double sum = 0.0;
        for (Number n : list) {
            sum += n.doubleValue();
        }
        return sum;
    }
    
    // Method to find max in list of comparable items
    public static <T extends Comparable<? super T>> T max(List<? extends T> list) {
        if (list.isEmpty()) {
            return null;
        }
        
        T max = list.get(0);
        for (T item : list) {
            if (item.compareTo(max) > 0) {
                max = item;
            }
        }
        return max;
    }
    
    public static void main(String[] args) {
        List<Integer> integers = Arrays.asList(1, 2, 3, 4, 5);
        List<Double> doubles = Arrays.asList(1.1, 2.2, 3.3, 4.4, 5.5);
        List<String> strings = Arrays.asList("A", "B", "C");
        
        System.out.println("Sum of integers: " + sumOfList(integers));
        System.out.println("Sum of doubles: " + sumOfList(doubles));
        
        System.out.println("Max integer: " + max(integers));
        System.out.println("Max string: " + max(strings));
    }
}
```

### Lower Bounded Wildcards
```java
public class LowerBoundedWildcards {
    
    // Method with lower bounded wildcard
    public static void addNumbers(List<? super Integer> list) {
        for (int i = 1; i <= 5; i++) {
            list.add(i);
        }
    }
    
    // Method to copy from source to destination
    public static <T> void copy(List<? extends T> source, List<? super T> destination) {
        for (T item : source) {
            destination.add(item);
        }
    }
    
    public static void main(String[] args) {
        List<Number> numbers = new ArrayList<>();
        addNumbers(numbers);
        System.out.println("Numbers: " + numbers);
        
        List<Object> objects = new ArrayList<>();
        addNumbers(objects);
        System.out.println("Objects: " + objects);
        
        // Copy example
        List<String> source = Arrays.asList("A", "B", "C");
        List<Object> destination = new ArrayList<>();
        copy(source, destination);
        System.out.println("Copied: " + destination);
    }
}
```

## Generic Interfaces

### Generic Interface Definition
```java
public interface Container<T> {
    void add(T item);
    T get(int index);
    int size();
    boolean isEmpty();
    void clear();
}

// Implementation of generic interface
public class ListContainer<T> implements Container<T> {
    private List<T> items = new ArrayList<>();
    
    @Override
    public void add(T item) {
        items.add(item);
    }
    
    @Override
    public T get(int index) {
        return items.get(index);
    }
    
    @Override
    public int size() {
        return items.size();
    }
    
    @Override
    public boolean isEmpty() {
        return items.isEmpty();
    }
    
    @Override
    public void clear() {
        items.clear();
    }
    
    @Override
    public String toString() {
        return items.toString();
    }
}

// Using generic interface
public class GenericInterfaceDemo {
    public static void main(String[] args) {
        Container<String> stringContainer = new ListContainer<>();
        stringContainer.add("Hello");
        stringContainer.add("World");
        System.out.println("String container: " + stringContainer);
        
        Container<Integer> integerContainer = new ListContainer<>();
        integerContainer.add(1);
        integerContainer.add(2);
        integerContainer.add(3);
        System.out.println("Integer container: " + integerContainer);
    }
}
```

## Type Erasure

### Understanding Type Erasure
```java
public class TypeErasureExample {
    
    // Generic method (type erasure at runtime)
    public static <T> void printType(T item) {
        System.out.println("Type: " + item.getClass().getSimpleName());
        System.out.println("Value: " + item);
    }
    
    // Generic class with type erasure
    public static class GenericBox<T> {
        private T content;
        
        public void setContent(T content) {
            this.content = content;
        }
        
        public T getContent() {
            return content;
        }
        
        // This method uses type erasure
        public void printContentType() {
            if (content != null) {
                System.out.println("Content type: " + content.getClass().getSimpleName());
            }
        }
    }
    
    public static void main(String[] args) {
        // Type erasure in action
        GenericBox<String> stringBox = new GenericBox<>();
        stringBox.setContent("Hello");
        stringBox.printContentType();
        
        GenericBox<Integer> integerBox = new GenericBox<>();
        integerBox.setContent(42);
        integerBox.printContentType();
        
        // At runtime, both are just GenericBox objects
        System.out.println("stringBox class: " + stringBox.getClass().getSimpleName());
        System.out.println("integerBox class: " + integerBox.getClass().getSimpleName());
    }
}
```

## Examples

### Complete Generic Example
```java
import java.util.*;

public class CompleteGenericExample {
    
    // Generic stack implementation
    public static class GenericStack<T> {
        private List<T> items = new ArrayList<>();
        
        public void push(T item) {
            items.add(item);
        }
        
        public T pop() {
            if (isEmpty()) {
                throw new EmptyStackException();
            }
            return items.remove(items.size() - 1);
        }
        
        public T peek() {
            if (isEmpty()) {
                throw new EmptyStackException();
            }
            return items.get(items.size() - 1);
        }
        
        public boolean isEmpty() {
            return items.isEmpty();
        }
        
        public int size() {
            return items.size();
        }
        
        @Override
        public String toString() {
            return items.toString();
        }
    }
    
    // Generic utility class
    public static class CollectionUtils {
        
        // Generic method to reverse collection
        public static <T> void reverse(List<T> list) {
            for (int i = 0, j = list.size() - 1; i < j; i++, j--) {
                Collections.swap(list, i, j);
            }
        }
        
        // Generic method to filter collection
        public static <T> List<T> filter(List<T> list, Predicate<T> predicate) {
            List<T> result = new ArrayList<>();
            for (T item : list) {
                if (predicate.test(item)) {
                    result.add(item);
                }
            }
            return result;
        }
        
        // Generic method to map collection
        public static <T, R> List<R> map(List<T> list, Function<T, R> mapper) {
            List<R> result = new ArrayList<>();
            for (T item : list) {
                result.add(mapper.apply(item));
            }
            return result;
        }
    }
    
    // Functional interfaces for demonstration
    @FunctionalInterface
    public interface Predicate<T> {
        boolean test(T t);
    }
    
    @FunctionalInterface
    public interface Function<T, R> {
        R apply(T t);
    }
    
    public static void main(String[] args) {
        // Test generic stack
        GenericStack<String> stringStack = new GenericStack<>();
        stringStack.push("First");
        stringStack.push("Second");
        stringStack.push("Third");
        
        System.out.println("Stack: " + stringStack);
        System.out.println("Pop: " + stringStack.pop());
        System.out.println("Peek: " + stringStack.peek());
        System.out.println("Stack after operations: " + stringStack);
        
        // Test generic stack with integers
        GenericStack<Integer> intStack = new GenericStack<>();
        for (int i = 1; i <= 5; i++) {
            intStack.push(i);
        }
        
        System.out.println("Integer stack: " + intStack);
        
        // Test collection utilities
        List<Integer> numbers = Arrays.asList(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
        
        // Filter even numbers
        List<Integer> evenNumbers = CollectionUtils.filter(numbers, n -> n % 2 == 0);
        System.out.println("Even numbers: " + evenNumbers);
        
        // Map to squares
        List<Integer> squares = CollectionUtils.map(numbers, n -> n * n);
        System.out.println("Squares: " + squares);
        
        // Reverse list
        List<String> names = new ArrayList<>(Arrays.asList("Alice", "Bob", "Charlie"));
        CollectionUtils.reverse(names);
        System.out.println("Reversed names: " + names);
    }
}
```

## Best Practices
- Use generics for type safety and code reusability
- Prefer generic types over raw types
- Use bounded type parameters when constraints are needed
- Use wildcards appropriately for flexible APIs
- Consider type erasure when designing generic APIs
- Use meaningful type parameter names (T, E, K, V)
- Document generic type parameters
- Avoid mixing generics and arrays when possible
- Use generic methods for utility operations
- Test with different type parameters to ensure robustness
