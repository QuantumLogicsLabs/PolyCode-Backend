# Java Lambda Expressions

## Lambda Basics

### Lambda Syntax
```java
public class LambdaBasics {
    public static void main(String[] args) {
        // Lambda expression syntax: (parameters) -> { body }
        
        // No parameters
        Runnable noParams = () -> System.out.println("No parameters");
        
        // Single parameter
        Consumer<String> singleParam = name -> System.out.println("Hello, " + name);
        
        // Multiple parameters
        BiFunction<String, Integer, String> multipleParams = 
            (name, age) -> name + " is " + age + " years old";
        
        // With body block
        Predicate<Integer> withBody = age -> {
            if (age >= 18) {
                return true;
            } else {
                return false;
            }
        };
        
        // Execute lambdas
        noParams.run();
        singleParam.accept("Alice");
        System.out.println(multipleParams.apply("Bob", 30));
        System.out.println("Is 25 adult? " + withBody.test(25));
    }
}
```

### Functional Interfaces
```java
import java.util.function.*;

public class FunctionalInterfaces {
    public static void main(String[] args) {
        // Predicate - returns boolean
        Predicate<Integer> isEven = n -> n % 2 == 0;
        System.out.println("Is 4 even? " + isEven.test(4));
        System.out.println("Is 3 even? " + isEven.test(3));
        
        // Consumer - accepts value, returns void
        Consumer<String> printer = str -> System.out.println("Printing: " + str);
        printer.accept("Hello World");
        
        // Supplier - supplies value
        Supplier<Double> randomSupplier = () -> Math.random();
        System.out.println("Random number: " + randomSupplier.get());
        
        // Function - transforms input to output
        Function<String, Integer> stringLength = str -> str.length();
        System.out.println("Length of 'Hello': " + stringLength.apply("Hello"));
        
        // UnaryOperator - single input and output of same type
        UnaryOperator<String> toUpper = String::toUpperCase;
        System.out.println("Uppercase: " + toUpper.apply("hello"));
        
        // BinaryOperator - two inputs of same type, output of same type
        BinaryOperator<Integer> adder = (a, b) -> a + b;
        System.out.println("5 + 3 = " + adder.apply(5, 3));
        
        // BiPredicate - two parameters, returns boolean
        BiPredicate<String, Integer> lengthCheck = (str, len) -> str.length() == len;
        System.out.println("Is 'hello' length 5? " + lengthCheck.test("hello", 5));
        
        // BiConsumer - two parameters, returns void
        BiConsumer<String, Integer> printerWithCount = (str, count) -> {
            for (int i = 0; i < count; i++) {
                System.out.println(str);
            }
        };
        printerWithCount.accept("Repeat", 3);
    }
}
```

## Method References

### Types of Method References
```java
public class MethodReferences {
    public static void main(String[] args) {
        // Reference to static method
        Function<String, Integer> parseInt = Integer::parseInt;
        System.out.println("Parsed 123: " + parseInt.apply("123"));
        
        // Reference to instance method
        String str = "Hello World";
        Supplier<Integer> lengthGetter = str::length;
        System.out.println("Length: " + lengthGetter.get());
        
        // Reference to instance method of arbitrary object
        Function<String, String> toUpper = String::toUpperCase;
        System.out.println("Uppercase: " + toUpper.apply("hello"));
        
        // Reference to constructor
        Supplier<StringBuilder> sbSupplier = StringBuilder::new;
        StringBuilder sb = sbSupplier.get();
        sb.append("Hello");
        System.out.println("StringBuilder: " + sb.toString());
        
        // Array constructor reference
        Function<Integer, int[]> arrayCreator = int[]::new;
        int[] array = arrayCreator.apply(5);
        System.out.println("Array length: " + array.length);
        
        // Using method references with streams
        List<String> names = Arrays.asList("Alice", "Bob", "Charlie");
        names.forEach(System.out::println);
        
        List<Integer> numbers = Arrays.asList(1, 2, 3, 4, 5);
        int sum = numbers.stream()
            .reduce(0, Integer::sum);
        System.out.println("Sum: " + sum);
    }
}
```

## Lambda with Collections

### List Operations
```java
import java.util.*;
import java.util.stream.*;

public class LambdaCollections {
    public static void main(String[] args) {
        List<String> names = Arrays.asList("Alice", "Bob", "Charlie", "David", "Eve");
        
        // Iterate with lambda
        names.forEach(name -> System.out.println("Hello, " + name));
        
        // Filter with lambda
        List<String> longNames = names.stream()
            .filter(name -> name.length() > 4)
            .collect(Collectors.toList());
        System.out.println("Long names: " + longNames);
        
        // Map with lambda
        List<String> upperNames = names.stream()
            .map(name -> name.toUpperCase())
            .collect(Collectors.toList());
        System.out.println("Uppercase names: " + upperNames);
        
        // Sort with lambda
        names.sort((a, b) -> b.length() - a.length());
        System.out.println("Sorted by length (desc): " + names);
        
        // Remove if with lambda
        names.removeIf(name -> name.startsWith("A"));
        System.out.println("After removing names starting with A: " + names);
        
        // Replace all with lambda
        names.replaceAll(name -> "Mr./Ms. " + name);
        System.out.println("After replaceAll: " + names);
        
        // Map operations
        Map<String, Integer> nameLengths = new HashMap<>();
        nameLengths.put("Alice", 5);
        nameLengths.put("Bob", 3);
        nameLengths.put("Charlie", 7);
        
        // Iterate map entries
        nameLengths.forEach((name, length) -> 
            System.out.println(name + " has length " + length));
        
        // Filter map values
        Map<String, Integer> filteredMap = nameLengths.entrySet().stream()
            .filter(entry -> entry.getValue() > 4)
            .collect(Collectors.toMap(
                Map.Entry::getKey,
                Map.Entry::getValue
            ));
        System.out.println("Filtered map: " + filteredMap);
    }
}
```

## Custom Functional Interfaces

### Creating Custom Interfaces
```java
@FunctionalInterface
interface TriFunction<T, U, V, R> {
    R apply(T t, U u, V v);
}

@FunctionalInterface
interface Validator<T> {
    boolean validate(T t);
    
    default Validator<T> and(Validator<T> other) {
        return t -> this.validate(t) && other.validate(t);
    }
    
    default Validator<T> or(Validator<T> other) {
        return t -> this.validate(t) || other.validate(t);
    }
}

@FunctionalInterface
interface Calculator {
    double calculate(double a, double b);
}

public class CustomFunctionalInterfaces {
    public static void main(String[] args) {
        // Using TriFunction
        TriFunction<String, Integer, Double, String> formatter = 
            (name, age, salary) -> String.format("%s is %d years old and earns $%.2f", name, age, salary);
        
        String result = formatter.apply("Alice", 30, 75000.50);
        System.out.println(result);
        
        // Using Validator
        Validator<String> notEmpty = str -> str != null && !str.isEmpty();
        Validator<String> minLength = str -> str.length() >= 3;
        
        Validator<String> combinedValidator = notEmpty.and(minLength);
        
        System.out.println("Is 'Hello' valid? " + combinedValidator.validate("Hello"));
        System.out.println("Is 'Hi' valid? " + combinedValidator.validate("Hi"));
        System.out.println("Is '' valid? " + combinedValidator.validate(""));
        
        // Using Calculator
        Calculator add = (a, b) -> a + b;
        Calculator multiply = (a, b) -> a * b;
        Calculator divide = (a, b) -> b != 0 ? a / b : 0;
        
        System.out.println("5 + 3 = " + add.calculate(5, 3));
        System.out.println("5 * 3 = " + multiply.calculate(5, 3));
        System.out.println("10 / 2 = " + divide.calculate(10, 2));
    }
}
```

## Lambda in GUI and Event Handling

### Event Handling with Lambdas
```java
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;

public class LambdaEventHandling {
    public static void main(String[] args) {
        JFrame frame = new JFrame("Lambda Event Handling");
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        frame.setSize(300, 200);
        frame.setLayout(new FlowLayout());
        
        JButton button = new JButton("Click Me!");
        JTextField textField = new JTextField(15);
        
        // Traditional anonymous class
        button.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                textField.setText("Button clicked!");
            }
        });
        
        // Lambda equivalent
        button.addActionListener(e -> textField.setText("Button clicked!"));
        
        // More complex lambda
        button.addActionListener(e -> {
            String currentText = textField.getText();
            if (currentText.isEmpty()) {
                textField.setText("First click!");
            } else {
                textField.setText(currentText + " - Clicked again!");
            }
        });
        
        frame.add(button);
        frame.add(textField);
        frame.setVisible(true);
    }
}
```

## Lambda Best Practices

### Effective Lambda Usage
```java
import java.util.*;
import java.util.stream.*;

public class LambdaBestPractices {
    
    // Good: Use method references when possible
    public static void methodReferenceExample() {
        List<String> names = Arrays.asList("Alice", "Bob", "Charlie");
        
        // Good: Method reference
        names.forEach(System.out::println);
        
        // Not as good: Lambda equivalent
        names.forEach(name -> System.out.println(name));
    }
    
    // Good: Keep lambdas short and focused
    public static void focusedLambdaExample() {
        List<Integer> numbers = Arrays.asList(1, 2, 3, 4, 5);
        
        // Good: Short, focused lambda
        List<Integer> squares = numbers.stream()
            .map(n -> n * n)
            .collect(Collectors.toList());
        
        // Not as good: Complex lambda in stream
        List<Integer> complexResult = numbers.stream()
            .map(n -> {
                int result = n * n;
                if (result > 10) {
                    result = result / 2;
                }
                return result;
            })
            .collect(Collectors.toList());
    }
    
    // Good: Use descriptive parameter names
    public static void descriptiveParametersExample() {
        Map<String, Integer> nameToAge = new HashMap<>();
        nameToAge.put("Alice", 30);
        nameToAge.put("Bob", 25);
        
        // Good: Descriptive parameter names
        nameToAge.forEach((name, age) -> 
            System.out.println(name + " is " + age + " years old"));
        
        // Not as good: Generic parameter names
        nameToAge.forEach((k, v) -> 
            System.out.println(k + " is " + v + " years old"));
    }
    
    // Good: Avoid side effects in pure functions
    public static void pureFunctionExample() {
        List<Integer> numbers = Arrays.asList(1, 2, 3, 4, 5);
        
        // Good: Pure function
        List<Integer> doubled = numbers.stream()
            .map(n -> n * 2)
            .collect(Collectors.toList());
        
        // Not as good: Lambda with side effects
        List<Integer> mutableList = new ArrayList<>();
        numbers.stream()
            .forEach(n -> mutableList.add(n * 2)); // Side effect
    }
    
    public static void main(String[] args) {
        methodReferenceExample();
        focusedLambdaExample();
        descriptiveParametersExample();
        pureFunctionExample();
    }
}
```

## Examples

### Complete Lambda Example
```java
import java.util.*;
import java.util.function.*;
import java.util.stream.*;

public class CompleteLambdaExample {
    
    static class Product {
        private String name;
        private String category;
        private double price;
        private int stock;
        
        public Product(String name, String category, double price, int stock) {
            this.name = name;
            this.category = category;
            this.price = price;
            this.stock = stock;
        }
        
        // Getters
        public String getName() { return name; }
        public String getCategory() { return category; }
        public double getPrice() { return price; }
        public int getStock() { return stock; }
        
        public void setPrice(double price) { this.price = price; }
        
        @Override
        public String toString() {
            return String.format("%s (%s, $%.2f, stock: %d)", name, category, price, stock);
        }
    }
    
    public static void main(String[] args) {
        List<Product> products = Arrays.asList(
            new Product("Laptop", "Electronics", 999.99, 10),
            new Product("Mouse", "Electronics", 29.99, 50),
            new Product("Keyboard", "Electronics", 79.99, 25),
            new Product("Book", "Education", 19.99, 100),
            new Product("Pen", "Education", 2.99, 200),
            new Product("Coffee Mug", "Kitchen", 9.99, 75)
        );
        
        // 1. Filter products by category
        Predicate<Product> isElectronic = p -> "Electronics".equals(p.getCategory());
        List<Product> electronics = products.stream()
            .filter(isElectronic)
            .collect(Collectors.toList());
        
        System.out.println("Electronic products:");
        electronics.forEach(System.out::println);
        
        // 2. Apply discount to expensive products
        UnaryOperator<Product> applyDiscount = p -> {
            if (p.getPrice() > 50) {
                p.setPrice(p.getPrice() * 0.9); // 10% discount
            }
            return p;
        };
        
        List<Product> discountedProducts = products.stream()
            .map(applyDiscount)
            .collect(Collectors.toList());
        
        System.out.println("\nProducts after discount:");
        discountedProducts.forEach(System.out::println);
        
        // 3. Group products by category
        Map<String, List<Product>> byCategory = products.stream()
            .collect(Collectors.groupingBy(Product::getCategory));
        
        System.out.println("\nProducts by category:");
        byCategory.forEach((category, productList) -> {
            System.out.println(category + ": " + productList.size() + " products");
        });
        
        // 4. Calculate total stock by category
        Map<String, Integer> stockByCategory = products.stream()
            .collect(Collectors.groupingBy(
                Product::getCategory,
                Collectors.summingInt(Product::getStock)
            ));
        
        System.out.println("\nTotal stock by category:");
        stockByCategory.forEach((category, stock) -> {
            System.out.println(category + ": " + stock + " items");
        });
        
        // 5. Find most expensive product
        Optional<Product> mostExpensive = products.stream()
            .max(Comparator.comparingDouble(Product::getPrice));
        
        mostExpensive.ifPresent(p -> 
            System.out.println("\nMost expensive: " + p));
        
        // 6. Custom functional interface for product validation
        TriFunction<Product, String, Double, Boolean> productValidator = 
            (product, category, minPrice) -> {
                return product.getCategory().equals(category) && 
                       product.getPrice() >= minPrice;
            };
        
        boolean isValid = productValidator.apply(
            products.get(0), "Electronics", 100.0);
        System.out.println("\nIs first product valid electronic over $100? " + isValid);
        
        // 7. Chain operations with complex lambda
        List<String> productSummaries = products.stream()
            .filter(p -> p.getStock() > 20)
            .map(p -> String.format("%s ($%.2f)", p.getName(), p.getPrice()))
            .sorted(Comparator.comparing(String::length))
            .collect(Collectors.toList());
        
        System.out.println("\nProduct summaries (stock > 20):");
        productSummaries.forEach(System.out::println);
        
        // 8. Use lambda for custom sorting
        products.sort((p1, p2) -> {
            int categoryCompare = p1.getCategory().compareTo(p2.getCategory());
            if (categoryCompare != 0) {
                return categoryCompare;
            }
            return Double.compare(p2.getPrice(), p1.getPrice()); // Price descending
        });
        
        System.out.println("\nProducts sorted by category and price:");
        products.forEach(System.out::println);
        
        // 9. Lambda with exception handling
        Function<String, Integer> safeParseInt = str -> {
            try {
                return Integer.parseInt(str);
            } catch (NumberFormatException e) {
                return 0;
            }
        };
        
        List<String> numberStrings = Arrays.asList("123", "abc", "456", "def", "789");
        int sum = numberStrings.stream()
            .mapToInt(safeParseInt::apply)
            .sum();
        
        System.out.println("\nSum of valid numbers: " + sum);
        
        // 10. BiFunction for combining products
        BiFunction<Product, Product, String> productCombiner = (p1, p2) -> {
            return String.format("Compare: %s ($%.2f) vs %s ($%.2f)",
                p1.getName(), p1.getPrice(), p2.getName(), p2.getPrice());
        };
        
        System.out.println("\nProduct comparisons:");
        for (int i = 0; i < products.size() - 1; i++) {
            String comparison = productCombiner.apply(products.get(i), products.get(i + 1));
            System.out.println(comparison);
        }
    }
}
```

## Best Practices
- Use lambda expressions for functional interfaces
- Prefer method references over equivalent lambdas
- Keep lambda expressions short and focused
- Use descriptive parameter names
- Avoid side effects in pure functional operations
- Use appropriate functional interfaces from java.util.function
- Consider readability when choosing between lambda and anonymous classes
- Use lambda expressions for event handling and callbacks
- Test lambda expressions thoroughly
- Be aware of lambda performance characteristics
