import java.lang.annotation.Retention;
import java.lang.annotation.Target;
import java.lang.reflect.*;
import java.util.*;
import java.util.concurrent.*;
import java.util.function.*;
import java.util.stream.*;

public class AdvancedTopicsExample {
    public static void main(String[] args) {
        // Reflection
        System.out.println("=== Reflection ===");
        demonstrateReflection();
        
        // Generics
        System.out.println("\n=== Generics ===");
        demonstrateGenerics();
        
        // Lambda Expressions
        System.out.println("\n=== Lambda Expressions ===");
        demonstrateLambdas();
        
        // Streams API
        System.out.println("\n=== Streams API ===");
        demonstrateStreams();
        
        // Annotations
        System.out.println("\n=== Annotations ===");
        demonstrateAnnotations();
        
        // Concurrent Programming
        System.out.println("\n=== Concurrent Programming ===");
        demonstrateConcurrentProgramming();
        
        // Design Patterns
        System.out.println("\n=== Design Patterns ===");
        demonstrateDesignPatterns();
        
        // Performance Optimization
        System.out.println("\n=== Performance Optimization ===");
        performanceOptimization();
    }
    
    public static void demonstrateReflection() {
        try {
            // Get class information
            Class<?> stringClass = String.class;
            System.out.println("Class: " + stringClass.getName());
            System.out.println("Package: " + stringClass.getPackage().getName());
            System.out.println("Modifiers: " + Modifier.toString(stringClass.getModifiers()));
            
            // Get constructors
            Constructor<?>[] constructors = stringClass.getConstructors();
            System.out.println("Constructors:");
            for (Constructor<?> constructor : constructors) {
                System.out.println("  " + constructor);
            }
            
            // Get methods
            Method[] methods = stringClass.getMethods();
            System.out.println("Methods (first 5):");
            for (int i = 0; i < Math.min(5, methods.length); i++) {
                System.out.println("  " + methods[i].getName() + methods[i].getParameterTypes());
            }
            
            // Get fields
            Field[] fields = stringClass.getDeclaredFields();
            System.out.println("Fields (first 3):");
            for (int i = 0; i < Math.min(3, fields.length); i++) {
                System.out.println("  " + fields[i].getName() + " (" + fields[i].getType().getSimpleName() + ")");
            }
            
            // Create instance dynamically
            Constructor<?> constructor = stringClass.getConstructor(String.class);
            String instance = (String) constructor.newInstance("Hello Reflection!");
            System.out.println("Created instance: " + instance);
            
            // Invoke method dynamically
            Method lengthMethod = stringClass.getMethod("length");
            int length = (Integer) lengthMethod.invoke(instance);
            System.out.println("Length of instance: " + length);
            
            // Access private field
            Field valueField = stringClass.getDeclaredField("value");
            valueField.setAccessible(true);
            valueField.set(instance, "Modified Value");
            System.out.println("Modified instance: " + instance);
            
        } catch (Exception e) {
            System.err.println("Reflection error: " + e.getMessage());
        }
    }
    
    public static void demonstrateGenerics() {
        // Generic class
        GenericBox<String> stringBox = new GenericBox<>("Hello Generics");
        GenericBox<Integer> intBox = new GenericBox<>(42);
        
        System.out.println("String box: " + stringBox.getContent());
        System.out.println("Integer box: " + intBox.getContent());
        
        // Generic method
        String[] stringArray = {"Apple", "Banana", "Orange"};
        String firstString = GenericUtils.getFirst(stringArray);
        System.out.println("First string: " + firstString);
        
        Integer[] intArray = {10, 20, 30, 40};
        Integer firstInt = GenericUtils.getFirst(intArray);
        System.out.println("First integer: " + firstInt);
        
        // Bounded generic
        BoundedGeneric<Number> numberBox = new BoundedGeneric<>(3.14);
        System.out.println("Number box: " + numberBox.getContent());
        System.out.println("Double value: " + numberBox.getDoubleValue());
        
        // Wildcard generic
        List<String> stringList = Arrays.asList("A", "B", "C");
        printList(stringList);
        
        List<Integer> intList = Arrays.asList(1, 2, 3);
        printList(intList);
    }
    
    public static <T> void printList(List<T> list) {
        System.out.print("List contents: ");
        for (T item : list) {
            System.out.print(item + " ");
        }
        System.out.println();
    }
    
    public static void demonstrateLambdas() {
        // Traditional way
        Thread traditionalThread = new Thread(new Runnable() {
            @Override
            public void run() {
                System.out.println("Traditional thread");
            }
        });
        
        // Lambda way
        Thread lambdaThread = new Thread(() -> System.out.println("Lambda thread"));
        
        // Functional interfaces
        Predicate<String> stringPredicate = s -> s.length() > 5;
        Function<String, Integer> stringLength = String::length;
        java.util.function.Consumer<String> stringPrinter = s -> System.out.println(s);
        Supplier<String> stringSupplier = () -> "Hello from Supplier";
        
        // Use functional interfaces
        List<String> strings = Arrays.asList("Apple", "Banana", "Orange", "Grape");
        List<String> longStrings = strings.stream()
                .filter(stringPredicate)
                .collect(Collectors.toList());
        
        System.out.println("Long strings: " + longStrings);
        
        int totalLength = strings.stream()
                .mapToInt(s -> s.length())
                .sum();
        
        System.out.println("Total length: " + totalLength);
        
        stringPrinter.accept(stringSupplier.get());
        
        // Comparator with lambda
        List<String> sortedStrings = new ArrayList<>(strings);
        sortedStrings.sort(java.util.Comparator.comparingInt(String::length));
        System.out.println("Sorted by length: " + sortedStrings);
    }
    
    public static void demonstrateStreams() {
        List<AdvancedPerson> people = Arrays.asList(
            new AdvancedPerson("Alice", 25, "Engineering"),
            new AdvancedPerson("Bob", 30, "Marketing"),
            new AdvancedPerson("Charlie", 22, "Sales"),
            new AdvancedPerson("Diana", 28, "Engineering"),
            new AdvancedPerson("Eve", 35, "Marketing")
        );
        
        // Filter
        List<AdvancedPerson> engineers = people.stream()
                .filter(p -> "Engineering".equals(p.getDepartment()))
                .collect(Collectors.toList());
        
        System.out.println("Engineers: " + engineers);
        
        // Map
        List<String> names = people.stream()
                .map(AdvancedPerson::getName)
                .collect(Collectors.toList());
        
        System.out.println("Names: " + names);
        
        // Reduce
        OptionalInt totalAge = people.stream()
                .mapToInt(AdvancedPerson::getAge)
                .reduce(Integer::sum);
        
        System.out.println("Total age: " + totalAge.orElse(0));
        
        // Grouping
        Map<String, List<AdvancedPerson>> byDepartment = people.stream()
                .collect(Collectors.groupingBy(AdvancedPerson::getDepartment));
        
        System.out.println("People by department:");
        byDepartment.forEach((dept, deptPeople) -> 
                System.out.println("  " + dept + ": " + deptPeople.size()));
        
        // Parallel stream
        long startTime = System.nanoTime();
        
        long count = people.parallelStream()
                .filter(p -> p.getAge() > 25)
                .count();
        
        long endTime = System.nanoTime();
        
        System.out.println("People over 25 (parallel): " + count);
        System.out.println("Parallel processing time: " + (endTime - startTime) / 1000000.0 + " ms");
    }
    
    public static void demonstrateAnnotations() {
        // Get annotations from class
        Class<AnnotatedClass> annotatedClass = AnnotatedClass.class;
        
        // Class-level annotation
        if (annotatedClass.isAnnotationPresent(MyClassAnnotation.class)) {
            MyClassAnnotation classAnnotation = annotatedClass.getAnnotation(MyClassAnnotation.class);
            System.out.println("Class annotation: " + classAnnotation.value());
            System.out.println("Class version: " + classAnnotation.version());
        }
        
        // Method-level annotations
        for (Method method : annotatedClass.getDeclaredMethods()) {
            if (method.isAnnotationPresent(MyMethodAnnotation.class)) {
                MyMethodAnnotation methodAnnotation = method.getAnnotation(MyMethodAnnotation.class);
                System.out.println("Method " + method.getName() + ":");
                System.out.println("  Description: " + methodAnnotation.description());
                System.out.println("  Priority: " + methodAnnotation.priority());
            }
        }
        
        // Create annotated object and process
        AnnotatedClass obj = new AnnotatedClass();
        processAnnotations(obj);
    }
    
    public static void processAnnotations(Object obj) {
        Class<?> clazz = obj.getClass();
        
        for (Method method : clazz.getDeclaredMethods()) {
            if (method.isAnnotationPresent(MyMethodAnnotation.class)) {
                MyMethodAnnotation annotation = method.getAnnotation(MyMethodAnnotation.class);
                
                try {
                    System.out.println("Executing method: " + method.getName());
                    method.invoke(obj);
                } catch (Exception e) {
                    System.err.println("Error executing method: " + e.getMessage());
                }
            }
        }
    }
    
    public static void demonstrateConcurrentProgramming() {
        // CompletableFuture
        CompletableFuture<String> future1 = CompletableFuture.supplyAsync(() -> {
            try {
                Thread.sleep(1000);
                return "Result from Future 1";
            } catch (InterruptedException e) {
                return "Interrupted";
            }
        });
        
        CompletableFuture<String> future2 = CompletableFuture.supplyAsync(() -> {
            try {
                Thread.sleep(800);
                return "Result from Future 2";
            } catch (InterruptedException e) {
                return "Interrupted";
            }
        });
        
        // Combine futures
        CompletableFuture<String> combinedFuture = future1.thenCombine(future2, 
                (result1, result2) -> result1 + " + " + result2);
        
        try {
            String combinedResult = combinedFuture.get(3, TimeUnit.SECONDS);
            System.out.println("Combined result: " + combinedResult);
        } catch (Exception e) {
            System.err.println("Future error: " + e.getMessage());
        }
        
        // Fork/Join framework
        ForkJoinPool pool = new ForkJoinPool();
        RecursiveTask task = new RecursiveTask(1, 100);
        pool.invoke(task);
        
        System.out.println("Fork/Join result: " + task.join());
        pool.shutdown();
    }
    
    public static void demonstrateDesignPatterns() {
        // Singleton Pattern
        Singleton singleton1 = Singleton.getInstance();
        Singleton singleton2 = Singleton.getInstance();
        
        System.out.println("Singleton instances equal: " + (singleton1 == singleton2));
        System.out.println("Singleton value: " + singleton1.getValue());
        
        // Factory Pattern
        Vehicle car = VehicleFactory.createVehicle("car");
        Vehicle motorcycle = VehicleFactory.createVehicle("motorcycle");
        
        System.out.println("Car: " + car.getType());
        System.out.println("Motorcycle: " + motorcycle.getType());
        
        // Observer Pattern
        NewsAgency agency = new NewsAgency();
        NewsChannel channel1 = new NewsChannel("CNN");
        NewsChannel channel2 = new NewsChannel("BBC");
        
        agency.addObserver(channel1);
        agency.addObserver(channel2);
        
        agency.setNews("Breaking: Java 21 Released!");
        agency.setNews("Update: New Features Added!");
        
        // Strategy Pattern
        PaymentContext creditCardContext = new PaymentContext(new CreditCardStrategy());
        PaymentContext paypalContext = new PaymentContext(new PayPalStrategy());
        
        System.out.println("Credit card payment: " + creditCardContext.pay(100.00));
        System.out.println("PayPal payment: " + paypalContext.pay(150.00));
    }
    
    public static void performanceOptimization() {
        final int OPERATIONS = 1000000;
        
        // Traditional loop
        long startTime = System.nanoTime();
        long sum = 0;
        for (int i = 0; i < OPERATIONS; i++) {
            sum += i;
        }
        long endTime = System.nanoTime();
        System.out.println("Traditional loop: " + (endTime - startTime) / 1000000.0 + " ms");
        
        // Stream API
        startTime = System.nanoTime();
        long streamSum = LongStream.range(0, OPERATIONS).sum();
        endTime = System.nanoTime();
        System.out.println("Stream API: " + (endTime - startTime) / 1000000.0 + " ms");
        
        // Parallel stream
        startTime = System.nanoTime();
        long parallelSum = LongStream.range(0, OPERATIONS).parallel().sum();
        endTime = System.nanoTime();
        System.out.println("Parallel stream: " + (endTime - startTime) / 1000000.0 + " ms");
        
        // Memory usage
        Runtime runtime = Runtime.getRuntime();
        long usedMemory = runtime.totalMemory() - runtime.freeMemory();
        System.out.println("Memory used: " + (usedMemory / 1024 / 1024) + " MB");
        
        // Garbage collection
        System.gc();
        long afterGC = runtime.totalMemory() - runtime.freeMemory();
        System.out.println("Memory after GC: " + (afterGC / 1024 / 1024) + " MB");
        System.out.println("Memory freed: " + ((usedMemory - afterGC) / 1024 / 1024) + " MB");
    }
}

// Supporting Classes for Advanced Topics

// Generic classes
class GenericBox<T> {
    private T content;
    
    public GenericBox(T content) {
        this.content = content;
    }
    
    public T getContent() {
        return content;
    }
    
    public void setContent(T content) {
        this.content = content;
    }
}

class GenericUtils {
    public static <T> T getFirst(T[] array) {
        if (array != null && array.length > 0) {
            return array[0];
        }
        return null;
    }
}

class BoundedGeneric<T extends Number> {
    private T content;
    
    public BoundedGeneric(T content) {
        this.content = content;
    }
    
    public T getContent() {
        return content;
    }
    
    public double getDoubleValue() {
        return content.doubleValue();
    }
}

// Annotation definitions
@Retention(java.lang.annotation.RetentionPolicy.RUNTIME)
@Target(java.lang.annotation.ElementType.TYPE)
@interface MyClassAnnotation {
    String value() default "Default Class";
    String version() default "1.0";
}

@Retention(java.lang.annotation.RetentionPolicy.RUNTIME)
@Target(java.lang.annotation.ElementType.METHOD)
@interface MyMethodAnnotation {
    String description() default "Default Method";
    int priority() default 0;
}

// Annotated class
@MyClassAnnotation(value = "Advanced Topics Example", version = "2.0")
class AnnotatedClass {
    
    @MyMethodAnnotation(description = "Important method", priority = 1)
    public void importantMethod() {
        System.out.println("This is an important method");
    }
    
    @MyMethodAnnotation(description = "Utility method", priority = 2)
    public void utilityMethod() {
        System.out.println("This is a utility method");
    }
}

// Person class for streams
class AdvancedPerson {
    private String name;
    private int age;
    private String department;
    
    public AdvancedPerson(String name, int age, String department) {
        this.name = name;
        this.age = age;
        this.department = department;
    }
    
    public String getName() { return name; }
    public int getAge() { return age; }
    public String getDepartment() { return department; }
    
    @Override
    public String toString() {
        return name + " (" + age + ", " + department + ")";
    }
}

// Recursive task for Fork/Join
class RecursiveTask extends java.util.concurrent.RecursiveTask<Long> {
    private final long start;
    private final long end;
    
    public RecursiveTask(long start, long end) {
        this.start = start;
        this.end = end;
    }
    
    @Override
    protected Long compute() {
        if (end - start <= 1000) {
            long sum = 0;
            for (long i = start; i <= end; i++) {
                sum += i;
            }
            return sum;
        } else {
            long mid = start + (end - start) / 2;
            RecursiveTask left = new RecursiveTask(start, mid);
            RecursiveTask right = new RecursiveTask(mid + 1, end);
            left.fork();
            right.fork();
            return left.join() + right.join();
        }
    }
}

// Design Pattern Implementations

// Singleton
class Singleton {
    private static volatile Singleton instance;
    private String value;
    
    private Singleton() {
        this.value = "Singleton Value";
    }
    
    public static Singleton getInstance() {
        if (instance == null) {
            synchronized (Singleton.class) {
                if (instance == null) {
                    instance = new Singleton();
                }
            }
        }
        return instance;
    }
    
    public String getValue() {
        return value;
    }
}

// Factory Pattern
interface Vehicle {
    String getType();
}

class Car implements Vehicle {
    @Override
    public String getType() {
        return "Car";
    }
}

class Motorcycle implements Vehicle {
    @Override
    public String getType() {
        return "Motorcycle";
    }
}

class VehicleFactory {
    public static Vehicle createVehicle(String type) {
        switch (type.toLowerCase()) {
            case "car":
                return new Car();
            case "motorcycle":
                return new Motorcycle();
            default:
                throw new IllegalArgumentException("Unknown vehicle type: " + type);
        }
    }
}

// Observer Pattern
interface Observer {
    void update(String news);
}

class NewsChannel implements Observer {
    private String name;
    
    public NewsChannel(String name) {
        this.name = name;
    }
    
    @Override
    public void update(String news) {
        System.out.println(name + " received: " + news);
    }
}

class NewsAgency {
    private final List<Observer> observers = new ArrayList<>();
    private String news;
    
    public void addObserver(Observer observer) {
        observers.add(observer);
    }
    
    public void removeObserver(Observer observer) {
        observers.remove(observer);
    }
    
    public void setNews(String news) {
        this.news = news;
        notifyObservers();
    }
    
    private void notifyObservers() {
        for (Observer observer : observers) {
            observer.update(news);
        }
    }
}

// Strategy Pattern
interface PaymentStrategy {
    String pay(double amount);
}

class CreditCardStrategy implements PaymentStrategy {
    @Override
    public String pay(double amount) {
        return "Paid $" + amount + " using Credit Card";
    }
}

class PayPalStrategy implements PaymentStrategy {
    @Override
    public String pay(double amount) {
        return "Paid $" + amount + " using PayPal";
    }
}

class PaymentContext {
    private PaymentStrategy strategy;
    
    public PaymentContext(PaymentStrategy strategy) {
        this.strategy = strategy;
    }
    
    public String pay(double amount) {
        return strategy.pay(amount);
    }
}
