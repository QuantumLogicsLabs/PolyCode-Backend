public class ClassesObjectsExample {
    public static void main(String[] args) {
        // Creating objects
        System.out.println("=== Creating Objects ===");
        
        // Default constructor
        Student student1 = new Student();
        student1.displayInfo();
        
        // Parameterized constructor
        Student student2 = new Student("Alice", 20, "Computer Science");
        student2.displayInfo();
        
        // Using setters
        Student student3 = new Student();
        student3.setName("Bob");
        student3.setAge(22);
        student3.setMajor("Mathematics");
        student3.displayInfo();
        
        // Class vs Instance Members
        System.out.println("\n=== Class vs Instance Members ===");
        System.out.println("Student count (static): " + Student.getStudentCount());
        System.out.println("University name (static): " + Student.getUniversityName());
        
        Student student4 = new Student("Charlie", 19, "Physics");
        System.out.println("After creating Charlie:");
        System.out.println("Student count: " + Student.getStudentCount());
        
        // Method types
        System.out.println("\n=== Method Types ===");
        student2.study(); // Instance method
        Student.showUniversityInfo(); // Static method
        
        // Object comparison
        System.out.println("\n=== Object Comparison ===");
        Student student5 = new Student("Alice", 20, "Computer Science");
        System.out.println("student2.equals(student5): " + student2.equals(student5));
        System.out.println("student2 == student5: " + (student2 == student5));
        
        // toString() method
        System.out.println("\n=== toString() Method ===");
        System.out.println("student2: " + student2.toString());
        System.out.println("student2: " + student2); // toString() called automatically
        
        // hashCode() method
        System.out.println("\n=== hashCode() Method ===");
        System.out.println("student2 hashCode: " + student2.hashCode());
        System.out.println("student5 hashCode: " + student5.hashCode());
        
        // Object cloning
        System.out.println("\n=== Object Cloning ===");
        try {
            Student cloned = (Student) student2.clone();
            System.out.println("Original: " + student2);
            System.out.println("Cloned: " + cloned);
            System.out.println("Are they equal? " + student2.equals(cloned));
            System.out.println("Same reference? " + (student2 == cloned));
        } catch (CloneNotSupportedException e) {
            System.out.println("Cloning not supported: " + e.getMessage());
        }
        
        // Garbage collection demonstration
        System.out.println("\n=== Garbage Collection ===");
        Student tempStudent = new Student("Temp", 25, "Temporary");
        tempStudent = null; // Eligible for garbage collection
        System.gc(); // Suggest garbage collection
        System.runFinalization();
        
        // Object lifecycle
        System.out.println("\n=== Object Lifecycle ===");
        demonstrateObjectLifecycle();
    }
    
    public static void demonstrateObjectLifecycle() {
        Student lifecycle = new Student("Lifecycle", 30, "Demo");
        lifecycle.displayInfo();
        // lifecycle goes out of scope and becomes eligible for GC
    }
}

// Student class demonstrating various OOP concepts
class Student implements Cloneable {
    // Instance variables
    private String name;
    private int age;
    private String major;
    
    // Static variables (class variables)
    private static int studentCount = 0;
    private static final String UNIVERSITY_NAME = "State University";
    
    // Default constructor
    public Student() {
        this.name = "Unknown";
        this.age = 0;
        this.major = "Undeclared";
        studentCount++;
        System.out.println("Default constructor called");
    }
    
    // Parameterized constructor
    public Student(String name, int age, String major) {
        this.name = name;
        this.age = age;
        this.major = major;
        studentCount++;
        System.out.println("Parameterized constructor called for " + name);
    }
    
    // Copy constructor
    public Student(Student other) {
        this.name = other.name;
        this.age = other.age;
        this.major = other.major;
        studentCount++;
        System.out.println("Copy constructor called");
    }
    
    // Instance methods
    public void study() {
        System.out.println(name + " is studying " + major);
    }
    
    public void displayInfo() {
        System.out.println("Student: " + name + ", Age: " + age + ", Major: " + major);
    }
    
    // Getters and Setters
    public String getName() {
        return name;
    }
    
    public void setName(String name) {
        this.name = name;
    }
    
    public int getAge() {
        return age;
    }
    
    public void setAge(int age) {
        if (age > 0) {
            this.age = age;
        }
    }
    
    public String getMajor() {
        return major;
    }
    
    public void setMajor(String major) {
        this.major = major;
    }
    
    // Static methods
    public static int getStudentCount() {
        return studentCount;
    }
    
    public static String getUniversityName() {
        return UNIVERSITY_NAME;
    }
    
    public static void showUniversityInfo() {
        System.out.println("University: " + UNIVERSITY_NAME);
        System.out.println("Total students: " + studentCount);
    }
    
    // Overridden methods from Object class
    
    @Override
    public String toString() {
        return "Student{name='" + name + "', age=" + age + ", major='" + major + "'}";
    }
    
    @Override
    public boolean equals(Object obj) {
        if (this == obj) return true;
        if (obj == null || getClass() != obj.getClass()) return false;
        
        Student student = (Student) obj;
        return age == student.age &&
               name.equals(student.name) &&
               major.equals(student.major);
    }
    
    @Override
    public int hashCode() {
        return name.hashCode() + age + major.hashCode();
    }
    
    @Override
    protected void finalize() throws Throwable {
        try {
            System.out.println("Finalizing student: " + name);
            studentCount--;
        } finally {
            super.finalize();
        }
    }
    
    @Override
    public Object clone() throws CloneNotSupportedException {
        return super.clone();
    }
}
