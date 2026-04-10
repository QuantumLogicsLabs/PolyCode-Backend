public class MethodsExample {
    
    // Instance variable
    private String message = "Hello from instance";
    
    // Static variable
    private static String staticMessage = "Hello from static";
    
    public static void main(String[] args) {
        MethodsExample obj = new MethodsExample();
        
        // Calling different types of methods
        obj.demonstrateMethodTypes();
        obj.demonstrateParameters();
        obj.demonstrateReturnTypes();
        obj.demonstrateOverloading();
        obj.demonstrateRecursion();
        obj.demonstrateVarargs();
        
        // Calling static methods
        staticMethod();
        System.out.println("Static method result: " + addStatic(5, 3));
    }
    
    // Method with no parameters and no return value (void)
    public void simpleMethod() {
        System.out.println("This is a simple method");
    }
    
    // Static method with no parameters and no return value
    public static void staticMethod() {
        System.out.println("This is a static method");
    }
    
    // Method with parameters and return value
    public int add(int a, int b) {
        return a + b;
    }
    
    // Static method with parameters and return value
    public static int addStatic(int a, int b) {
        return a + b;
    }
    
    // Method with different parameter types
    public void displayInfo(String name, int age, double height) {
        System.out.println("Name: " + name + ", Age: " + age + ", Height: " + height);
    }
    
    // Method returning different data types
    public String getGreeting(String name) {
        return "Hello, " + name + "!";
    }
    
    public boolean isEven(int number) {
        return number % 2 == 0;
    }
    
    public double calculateArea(double radius) {
        return Math.PI * radius * radius;
    }
    
    // Method overloading - same name, different parameters
    public int multiply(int a, int b) {
        return a * b;
    }
    
    public double multiply(double a, double b) {
        return a * b;
    }
    
    public int multiply(int a, int b, int c) {
        return a * b * c;
    }
    
    // Recursive method
    public int factorial(int n) {
        if (n <= 1) {
            return 1;
        }
        return n * factorial(n - 1);
    }
    
    // Fibonacci using recursion
    public int fibonacci(int n) {
        if (n <= 1) {
            return n;
        }
        return fibonacci(n - 1) + fibonacci(n - 2);
    }
    
    // Method with variable arguments (varargs)
    public int sum(int... numbers) {
        int total = 0;
        for (int num : numbers) {
            total += num;
        }
        return total;
    }
    
    public void printNames(String... names) {
        System.out.println("Names provided:");
        for (String name : names) {
            System.out.println("- " + name);
        }
    }
    
    // Method with default-like behavior using overloading
    public void greet() {
        greet("Guest");
    }
    
    public void greet(String name) {
        System.out.println("Hello, " + name + "!");
    }
    
    // Method demonstrating access to instance and static variables
    public void showMessages() {
        System.out.println("Instance message: " + message);
        System.out.println("Static message: " + staticMessage);
    }
    
    // Demonstration method
    public void demonstrateMethodTypes() {
        System.out.println("=== Method Types ===");
        simpleMethod();
        staticMethod();
        showMessages();
    }
    
    public void demonstrateParameters() {
        System.out.println("\n=== Parameters ===");
        displayInfo("Alice", 25, 1.65);
        System.out.println("Addition: " + add(10, 5));
        System.out.println("Greeting: " + getGreeting("Bob"));
    }
    
    public void demonstrateReturnTypes() {
        System.out.println("\n=== Return Types ===");
        System.out.println("Is 4 even? " + isEven(4));
        System.out.println("Area of circle (r=5): " + calculateArea(5));
    }
    
    public void demonstrateOverloading() {
        System.out.println("\n=== Method Overloading ===");
        System.out.println("multiply(3, 4): " + multiply(3, 4));
        System.out.println("multiply(3.5, 2.5): " + multiply(3.5, 2.5));
        System.out.println("multiply(2, 3, 4): " + multiply(2, 3, 4));
        greet();
        greet("Charlie");
    }
    
    public void demonstrateRecursion() {
        System.out.println("\n=== Recursion ===");
        System.out.println("Factorial of 5: " + factorial(5));
        System.out.println("Fibonacci of 6: " + fibonacci(6));
    }
    
    public void demonstrateVarargs() {
        System.out.println("\n=== Variable Arguments ===");
        System.out.println("Sum of 1, 2, 3, 4, 5: " + sum(1, 2, 3, 4, 5));
        System.out.println("Sum of 10, 20: " + sum(10, 20));
        printNames("Alice", "Bob", "Charlie");
    }
}
