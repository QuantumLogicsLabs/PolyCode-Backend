public class VariablesExample {
    public static void main(String[] args) {
        // Primitive variables
        int age = 25;
        double price = 99.99;
        char grade = 'A';
        boolean isActive = true;
        
        // Reference variables
        String name = "John Doe";
        int[] numbers = {1, 2, 3, 4, 5};
        
        // Constants (final variables)
        final double PI = 3.14159;
        
        // Display all variables
        System.out.println("Age: " + age);
        System.out.println("Price: $" + price);
        System.out.println("Grade: " + grade);
        System.out.println("Active: " + isActive);
        System.out.println("Name: " + name);
        System.out.println("Numbers: " + java.util.Arrays.toString(numbers));
        System.out.println("PI: " + PI);
        
        // Demonstrate instance and static variables
        VariablesExample obj = new VariablesExample();
        obj.displayInfo();
        System.out.println("Student count: " + studentCount);
    }
    
    // Static variable
    static int studentCount = 0;
    
    // Instance variable
    private String course = "Java Programming";
    
    // Local variable in method
    void displayInfo() {
        String message = "Welcome to Java!";
        System.out.println(message);
        System.out.println("Course: " + course);
    }
}
