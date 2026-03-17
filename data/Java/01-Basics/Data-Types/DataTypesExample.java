public class DataTypesExample {
    public static void main(String[] args) {
        // Primitive Data Types
        
        // Numeric Types
        byte byteVar = 127;           // 8-bit signed integer (-128 to 127)
        short shortVar = 32767;       // 16-bit signed integer (-32768 to 32767)
        int intVar = 2147483647;      // 32-bit signed integer
        long longVar = 9223372036854775807L; // 64-bit signed integer (L suffix)
        
        // Floating Point Types
        float floatVar = 3.14f;       // 32-bit floating point (f suffix)
        double doubleVar = 3.14159265359; // 64-bit floating point (default)
        
        // Character Type
        char charVar = 'A';           // 16-bit Unicode character
        
        // Boolean Type
        boolean boolVar = true;       // true or false
        
        // Reference Data Types
        
        // String
        String stringVar = "Hello, Java!";
        
        // Arrays
        int[] intArray = {1, 2, 3, 4, 5};
        String[] stringArray = {"Apple", "Banana", "Orange"};
        
        // Custom Object
        Person personVar = new Person("Alice", 30);
        
        // Display all data types
        System.out.println("=== Primitive Data Types ===");
        System.out.println("byte: " + byteVar);
        System.out.println("short: " + shortVar);
        System.out.println("int: " + intVar);
        System.out.println("long: " + longVar);
        System.out.println("float: " + floatVar);
        System.out.println("double: " + doubleVar);
        System.out.println("char: " + charVar);
        System.out.println("boolean: " + boolVar);
        
        System.out.println("\n=== Reference Data Types ===");
        System.out.println("String: " + stringVar);
        System.out.println("int array: " + java.util.Arrays.toString(intArray));
        System.out.println("String array: " + java.util.Arrays.toString(stringArray));
        System.out.println("Person: " + personVar);
        
        // Type casting examples
        System.out.println("\n=== Type Casting ===");
        double doubleValue = 10.5;
        int intValue = (int) doubleValue; // Explicit casting (loss of precision)
        System.out.println("double to int: " + doubleValue + " -> " + intValue);
        
        int autoCast = 100;
        double autoDouble = autoCast; // Automatic casting (no loss)
        System.out.println("int to double: " + autoCast + " -> " + autoDouble);
    }
}

// Custom class for reference type example
class Person {
    private String name;
    private int age;
    
    public Person(String name, int age) {
        this.name = name;
        this.age = age;
    }
    
    @Override
    public String toString() {
        return "Person[name=" + name + ", age=" + age + "]";
    }
}
