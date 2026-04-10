# Java Streams API

## Stream Basics

### Creating Streams
```java
import java.util.*;
import java.util.stream.*;

public class StreamBasics {
    public static void main(String[] args) {
        // Create stream from collection
        List<String> names = Arrays.asList("Alice", "Bob", "Charlie", "David");
        Stream<String> nameStream = names.stream();
        
        // Create stream from array
        int[] numbers = {1, 2, 3, 4, 5};
        IntStream numberStream = Arrays.stream(numbers);
        
        // Create stream using Stream.of()
        Stream<String> streamOf = Stream.of("A", "B", "C", "D");
        
        // Create stream using Stream.builder()
        Stream<String> builderStream = Stream.<String>builder()
            .add("One")
            .add("Two")
            .add("Three")
            .build();
        
        // Create infinite stream
        Stream<Integer> infiniteStream = Stream.iterate(0, n -> n + 1);
        infiniteStream.limit(10).forEach(System.out::println);
        
        // Create stream of random numbers
        Stream<Double> randomStream = Stream.generate(Math::random);
        randomStream.limit(5).forEach(System.out::println);
    }
}
```

### Stream Operations
```java
public class StreamOperations {
    public static void main(String[] args) {
        List<Integer> numbers = Arrays.asList(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
        
        // Filter operation
        List<Integer> evenNumbers = numbers.stream()
            .filter(n -> n % 2 == 0)
            .collect(Collectors.toList());
        System.out.println("Even numbers: " + evenNumbers);
        
        // Map operation
        List<Integer> squares = numbers.stream()
            .map(n -> n * n)
            .collect(Collectors.toList());
        System.out.println("Squares: " + squares);
        
        // Sorted operation
        List<Integer> sortedDesc = numbers.stream()
            .sorted(Comparator.reverseOrder())
            .collect(Collectors.toList());
        System.out.println("Sorted descending: " + sortedDesc);
        
        // Distinct operation
        List<Integer> withDuplicates = Arrays.asList(1, 2, 2, 3, 3, 4, 4, 5);
        List<Integer> distinct = withDuplicates.stream()
            .distinct()
            .collect(Collectors.toList());
        System.out.println("Distinct: " + distinct);
        
        // Limit operation
        List<Integer> firstFive = numbers.stream()
            .limit(5)
            .collect(Collectors.toList());
        System.out.println("First five: " + firstFive);
        
        // Skip operation
        List<Integer> lastFive = numbers.stream()
            .skip(5)
            .collect(Collectors.toList());
        System.out.println("Last five: " + lastFive);
    }
}
```

## Terminal Operations

### Collect Operations
```java
public class CollectOperations {
    public static void main(String[] args) {
        List<String> names = Arrays.asList("Alice", "Bob", "Charlie", "David", "Eve");
        
        // Collect to list
        List<String> nameList = names.stream()
            .filter(name -> name.length() > 4)
            .collect(Collectors.toList());
        System.out.println("Names longer than 4 chars: " + nameList);
        
        // Collect to set
        Set<String> nameSet = names.stream()
            .map(String::toLowerCase)
            .collect(Collectors.toSet());
        System.out.println("Lowercase names: " + nameSet);
        
        // Collect to map
        Map<String, Integer> nameLengthMap = names.stream()
            .collect(Collectors.toMap(
                name -> name,
                String::length
            ));
        System.out.println("Name lengths: " + nameLengthMap);
        
        // Joining strings
        String joined = names.stream()
            .collect(Collectors.joining(", "));
        System.out.println("Joined names: " + joined);
        
        // Grouping by
        Map<Integer, List<String>> groupedByLength = names.stream()
            .collect(Collectors.groupingBy(String::length));
        System.out.println("Grouped by length: " + groupedByLength);
        
        // Partitioning by
        Map<Boolean, List<String>> partitioned = names.stream()
            .collect(Collectors.partitioningBy(name -> name.length() > 4));
        System.out.println("Partitioned by length > 4: " + partitioned);
        
        // Counting
        long count = names.stream()
            .filter(name -> name.startsWith("A"))
            .count();
        System.out.println("Names starting with A: " + count);
        
        // Summarizing
        IntSummaryStatistics stats = names.stream()
            .collect(Collectors.summarizingInt(String::length));
        System.out.println("Name length statistics: " + stats);
    }
}
```

### Reduction Operations
```java
public class ReductionOperations {
    public static void main(String[] args) {
        List<Integer> numbers = Arrays.asList(1, 2, 3, 4, 5);
        
        // Sum using reduce
        int sum = numbers.stream()
            .reduce(0, Integer::sum);
        System.out.println("Sum: " + sum);
        
        // Product using reduce
        int product = numbers.stream()
            .reduce(1, (a, b) -> a * b);
        System.out.println("Product: " + product);
        
        // Maximum using reduce
        Optional<Integer> max = numbers.stream()
            .reduce(Integer::max);
        System.out.println("Maximum: " + max.orElse(0));
        
        // Minimum using reduce
        Optional<Integer> min = numbers.stream()
            .reduce(Integer::min);
        System.out.println("Minimum: " + min.orElse(0));
        
        // Concatenation using reduce
        List<String> words = Arrays.asList("Hello", " ", "World", "!");
        String sentence = words.stream()
            .reduce("", String::concat);
        System.out.println("Sentence: " + sentence);
        
        // Find first
        Optional<Integer> first = numbers.stream()
            .filter(n -> n > 3)
            .findFirst();
        System.out.println("First > 3: " + first.orElse(-1));
        
        // Find any
        Optional<Integer> any = numbers.stream()
            .filter(n -> n > 3)
            .findAny();
        System.out.println("Any > 3: " + any.orElse(-1));
        
        // All match
        boolean allEven = numbers.stream()
            .allMatch(n -> n % 2 == 0);
        System.out.println("All even: " + allEven);
        
        // Any match
        boolean anyEven = numbers.stream()
            .anyMatch(n -> n % 2 == 0);
        System.out.println("Any even: " + anyEven);
        
        // None match
        boolean noneNegative = numbers.stream()
            .noneMatch(n -> n < 0);
        System.out.println("None negative: " + noneNegative);
    }
}
```

## Intermediate Operations

### Mapping and Filtering
```java
public class MappingFiltering {
    public static void main(String[] args) {
        List<Person> people = Arrays.asList(
            new Person("Alice", 25, "New York"),
            new Person("Bob", 30, "Chicago"),
            new Person("Charlie", 35, "New York"),
            new Person("David", 28, "Los Angeles"),
            new Person("Eve", 32, "Chicago")
        );
        
        // Filter and map to names
        List<String> namesInNY = people.stream()
            .filter(person -> "New York".equals(person.getCity()))
            .map(Person::getName)
            .collect(Collectors.toList());
        System.out.println("People in New York: " + namesInNY);
        
        // Flat map example
        List<List<Integer>> nestedLists = Arrays.asList(
            Arrays.asList(1, 2, 3),
            Arrays.asList(4, 5, 6),
            Arrays.asList(7, 8, 9)
        );
        
        List<Integer> flattened = nestedLists.stream()
            .flatMap(List::stream)
            .collect(Collectors.toList());
        System.out.println("Flattened list: " + flattened);
        
        // Multiple operations chain
        List<String> result = people.stream()
            .filter(person -> person.getAge() > 25)
            .filter(person -> person.getCity().equals("Chicago"))
            .map(person -> person.getName() + " (" + person.getAge() + ")")
            .sorted()
            .collect(Collectors.toList());
        System.out.println("People >25 in Chicago: " + result);
        
        // Peek operation (for debugging)
        List<Person> processedPeople = people.stream()
            .peek(person -> System.out.println("Processing: " + person.getName()))
            .filter(person -> person.getAge() >= 30)
            .peek(person -> person.setAge(person.getAge() + 1))
            .collect(Collectors.toList());
        System.out.println("Processed people: " + processedPeople);
    }
}

class Person {
    private String name;
    private int age;
    private String city;
    
    public Person(String name, int age, String city) {
        this.name = name;
        this.age = age;
        this.city = city;
    }
    
    // Getters and setters
    public String getName() { return name; }
    public int getAge() { return age; }
    public String getCity() { return city; }
    public void setAge(int age) { this.age = age; }
    
    @Override
    public String toString() {
        return name + " (" + age + ", " + city + ")";
    }
}
```

## Primitive Streams

### IntStream, LongStream, DoubleStream
```java
public class PrimitiveStreams {
    public static void main(String[] args) {
        // IntStream examples
        IntStream intStream = IntStream.range(1, 10); // 1-9
        int[] ints = intStream.toArray();
        System.out.println("Int array: " + Arrays.toString(ints));
        
        // IntStream with rangeClosed
        int sum = IntStream.rangeClosed(1, 100).sum();
        System.out.println("Sum 1-100: " + sum);
        
        // IntStream from array
        int[] numbers = {1, 2, 3, 4, 5};
        int max = Arrays.stream(numbers).max().orElse(0);
        System.out.println("Max: " + max);
        
        // DoubleStream examples
        DoubleStream doubleStream = DoubleStream.of(1.1, 2.2, 3.3, 4.4, 5.5);
        double average = doubleStream.average().orElse(0.0);
        System.out.println("Average: " + average);
        
        // LongStream examples
        LongStream longStream = LongStream.rangeClosed(1L, 10L);
        long product = longStream.reduce(1L, (a, b) -> a * b);
        System.out.println("Product 1-10: " + product);
        
        // Box primitive stream to object stream
        List<Integer> boxed = IntStream.range(1, 6)
            .boxed()
            .collect(Collectors.toList());
        System.out.println("Boxed integers: " + boxed);
        
        // Map to object stream
        List<String> strings = IntStream.range(1, 6)
            .mapToObj(i -> "Number " + i)
            .collect(Collectors.toList());
        System.out.println("Mapped strings: " + strings);
    }
}
```

## Parallel Streams

### Parallel Processing
```java
public class ParallelStreams {
    public static void main(String[] args) {
        List<Integer> numbers = new ArrayList<>();
        for (int i = 1; i <= 1000000; i++) {
            numbers.add(i);
        }
        
        // Sequential stream
        long startTime = System.currentTimeMillis();
        long sequentialSum = numbers.stream()
            .mapToLong(Integer::longValue)
            .sum();
        long sequentialTime = System.currentTimeMillis() - startTime;
        
        System.out.println("Sequential sum: " + sequentialSum);
        System.out.println("Sequential time: " + sequentialTime + "ms");
        
        // Parallel stream
        startTime = System.currentTimeMillis();
        long parallelSum = numbers.parallelStream()
            .mapToLong(Integer::longValue)
            .sum();
        long parallelTime = System.currentTimeMillis() - startTime;
        
        System.out.println("Parallel sum: " + parallelSum);
        System.out.println("Parallel time: " + parallelTime + "ms");
        
        // Check if stream is parallel
        boolean isParallel = numbers.parallelStream().isParallel();
        System.out.println("Is parallel: " + isParallel);
        
        // Sequential vs parallel comparison
        List<String> words = Arrays.asList(
            "apple", "banana", "cherry", "date", "elderberry",
            "fig", "grape", "honeydew", "kiwi", "lemon"
        );
        
        // Sequential processing
        startTime = System.currentTimeMillis();
        List<String> sequentialResult = words.stream()
            .filter(word -> word.length() > 5)
            .map(String::toUpperCase)
            .sorted()
            .collect(Collectors.toList());
        sequentialTime = System.currentTimeMillis() - startTime;
        
        // Parallel processing
        startTime = System.currentTimeMillis();
        List<String> parallelResult = words.parallelStream()
            .filter(word -> word.length() > 5)
            .map(String::toUpperCase)
            .sorted()
            .collect(Collectors.toList());
        parallelTime = System.currentTimeMillis() - startTime;
        
        System.out.println("Sequential result: " + sequentialResult);
        System.out.println("Sequential time: " + sequentialTime + "ms");
        System.out.println("Parallel result: " + parallelResult);
        System.out.println("Parallel time: " + parallelTime + "ms");
    }
}
```

## Custom Collectors

### Creating Custom Collectors
```java
import java.util.*;
import java.util.function.*;
import java.util.stream.*;

public class CustomCollectors {
    
    // Custom collector to calculate statistics
    public static class Statistics {
        private long count;
        private double sum;
        private double min;
        private double max;
        
        public Statistics() {
            this.count = 0;
            this.sum = 0;
            this.min = Double.MAX_VALUE;
            this.max = Double.MIN_VALUE;
        }
        
        public void accept(double value) {
            count++;
            sum += value;
            if (value < min) min = value;
            if (value > max) max = value;
        }
        
        public Statistics combine(Statistics other) {
            Statistics combined = new Statistics();
            combined.count = this.count + other.count;
            combined.sum = this.sum + other.sum;
            combined.min = Math.min(this.min, other.min);
            combined.max = Math.max(this.max, other.max);
            return combined;
        }
        
        public double getAverage() {
            return count > 0 ? sum / count : 0;
        }
        
        public long getCount() { return count; }
        public double getSum() { return sum; }
        public double getMin() { return min; }
        public double getMax() { return max; }
        
        @Override
        public String toString() {
            return String.format("Count: %d, Sum: %.2f, Min: %.2f, Max: %.2f, Avg: %.2f",
                count, sum, min, max, getAverage());
        }
    }
    
    public static void main(String[] args) {
        List<Double> numbers = Arrays.asList(1.1, 2.2, 3.3, 4.4, 5.5, 6.6, 7.7, 8.8, 9.9);
        
        // Custom collector
        Statistics stats = numbers.stream()
            .collect(Statistics::new, Statistics::accept, Statistics::combine);
        System.out.println("Custom statistics: " + stats);
        
        // Using built-in statistics
        DoubleSummaryStatistics builtInStats = numbers.stream()
            .collect(Collectors.summarizingDouble(Double::doubleValue));
        System.out.println("Built-in statistics: " + builtInStats);
        
        // Custom collector using Collector.of()
        Collector<Double, ?, List<Double>> customCollector = Collector.of(
            ArrayList::new,                    // Supplier
            ArrayList::add,                     // Accumulator
            (list1, list2) -> {                // Combiner
                list1.addAll(list2);
                return list1;
            },
            Collector.Characteristics.IDENTITY_FINISH // Characteristics
        );
        
        List<Double> collected = numbers.stream()
            .collect(customCollector);
        System.out.println("Custom collected: " + collected);
    }
}
```

## Examples

### Complete Stream Processing Example
```java
import java.util.*;
import java.util.stream.*;
import java.util.concurrent.*;

public class CompleteStreamExample {
    
    static class Employee {
        private String name;
        private String department;
        private int age;
        private double salary;
        private List<String> skills;
        
        public Employee(String name, String department, int age, double salary, List<String> skills) {
            this.name = name;
            this.department = department;
            this.age = age;
            this.salary = salary;
            this.skills = skills;
        }
        
        // Getters
        public String getName() { return name; }
        public String getDepartment() { return department; }
        public int getAge() { return age; }
        public double getSalary() { return salary; }
        public List<String> getSkills() { return skills; }
        
        @Override
        public String toString() {
            return name + " (" + department + ", " + age + ", $" + salary + ")";
        }
    }
    
    public static void main(String[] args) {
        List<Employee> employees = Arrays.asList(
            new Employee("Alice", "IT", 28, 75000, Arrays.asList("Java", "Python", "SQL")),
            new Employee("Bob", "HR", 32, 65000, Arrays.asList("Communication", "Excel")),
            new Employee("Charlie", "IT", 35, 85000, Arrays.asList("Java", "Spring", "React")),
            new Employee("David", "Finance", 30, 70000, Arrays.asList("Excel", "Accounting", "SQL")),
            new Employee("Eve", "IT", 26, 60000, Arrays.asList("JavaScript", "Node.js")),
            new Employee("Frank", "HR", 40, 80000, Arrays.asList("Management", "Communication")),
            new Employee("Grace", "Finance", 28, 68000, Arrays.asList("Excel", "Financial Analysis"))
        );
        
        // 1. Filter IT employees and sort by salary
        List<Employee> itEmployees = employees.stream()
            .filter(emp -> "IT".equals(emp.getDepartment()))
            .sorted(Comparator.comparingDouble(Employee::getSalary).reversed())
            .collect(Collectors.toList());
        
        System.out.println("IT employees sorted by salary:");
        itEmployees.forEach(System.out::println);
        
        // 2. Group employees by department
        Map<String, List<Employee>> byDepartment = employees.stream()
            .collect(Collectors.groupingBy(Employee::getDepartment));
        
        System.out.println("\nEmployees by department:");
        byDepartment.forEach((dept, empList) -> {
            System.out.println(dept + ": " + empList.size() + " employees");
        });
        
        // 3. Calculate average salary by department
        Map<String, Double> avgSalaryByDept = employees.stream()
            .collect(Collectors.groupingBy(
                Employee::getDepartment,
                Collectors.averagingDouble(Employee::getSalary)
            ));
        
        System.out.println("\nAverage salary by department:");
        avgSalaryByDept.forEach((dept, avgSalary) -> {
            System.out.println(dept + ": $" + String.format("%.2f", avgSalary));
        });
        
        // 4. Find all unique skills
        Set<String> allSkills = employees.stream()
            .flatMap(emp -> emp.getSkills().stream())
            .collect(Collectors.toSet());
        
        System.out.println("\nAll unique skills: " + allSkills);
        
        // 5. Find employees with specific skills
        List<Employee> javaDevelopers = employees.stream()
            .filter(emp -> emp.getSkills().contains("Java"))
            .collect(Collectors.toList());
        
        System.out.println("\nJava developers:");
        javaDevelopers.forEach(System.out::println);
        
        // 6. Create summary statistics
        IntSummaryStatistics ageStats = employees.stream()
            .collect(Collectors.summarizingInt(Employee::getAge));
        
        DoubleSummaryStatistics salaryStats = employees.stream()
            .collect(Collectors.summarizingDouble(Employee::getSalary));
        
        System.out.println("\nAge statistics:");
        System.out.println("Count: " + ageStats.getCount());
        System.out.println("Average: " + ageStats.getAverage());
        System.out.println("Min: " + ageStats.getMin());
        System.out.println("Max: " + ageStats.getMax());
        
        System.out.println("\nSalary statistics:");
        System.out.println("Count: " + salaryStats.getCount());
        System.out.println("Average: $" + String.format("%.2f", salaryStats.getAverage()));
        System.out.println("Min: $" + String.format("%.2f", salaryStats.getMin()));
        System.out.println("Max: $" + String.format("%.2f", salaryStats.getMax()));
        
        // 7. Parallel processing for large dataset
        List<Employee> largeEmployeeList = new ArrayList<>();
        Random random = new Random();
        String[] departments = {"IT", "HR", "Finance", "Marketing"};
        String[] skills = {"Java", "Python", "SQL", "Excel", "Communication", "Management"};
        
        for (int i = 0; i < 10000; i++) {
            List<String> empSkills = new ArrayList<>();
            for (int j = 0; j < 3; j++) {
                empSkills.add(skills[random.nextInt(skills.length)]);
            }
            
            largeEmployeeList.add(new Employee(
                "Employee-" + i,
                departments[random.nextInt(departments.length)],
                25 + random.nextInt(20),
                50000 + random.nextInt(50000),
                empSkills
            ));
        }
        
        long startTime = System.currentTimeMillis();
        
        Map<String, Long> skillFrequency = largeEmployeeList.parallelStream()
            .flatMap(emp -> emp.getSkills().stream())
            .collect(Collectors.groupingBy(
                skill -> skill,
                Collectors.counting()
            ));
        
        long endTime = System.currentTimeMillis();
        
        System.out.println("\nSkill frequency (parallel processing):");
        skillFrequency.entrySet().stream()
            .sorted(Map.Entry.<String, Long>comparingByValue().reversed())
            .forEach(entry -> {
                System.out.println(entry.getKey() + ": " + entry.getValue());
            });
        
        System.out.println("Processing time: " + (endTime - startTime) + "ms");
    }
}
```

## Best Practices
- Use streams for data processing, not for side effects
- Prefer method references over lambdas when possible
- Use parallel streams for large datasets and CPU-intensive operations
- Be careful with infinite streams - always use limit()
- Use appropriate collectors for terminal operations
- Consider performance when chaining many operations
- Use primitive streams for numeric data
- Avoid modifying the source collection while streaming
- Use meaningful variable names for stream operations
- Test stream operations with different data sizes
