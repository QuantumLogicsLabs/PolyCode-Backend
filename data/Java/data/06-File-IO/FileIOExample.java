import java.io.*;
import java.nio.file.*;
import java.nio.file.attribute.BasicFileAttributes;
import java.util.*;

public class FileIOExample {
    public static void main(String[] args) {
        // Basic File Operations
        System.out.println("=== Basic File Operations ===");
        demonstrateBasicFileOperations();
        
        // File Reading and Writing
        System.out.println("\n=== File Reading and Writing ===");
        demonstrateFileReadingWriting();
        
        // NIO.2 Features
        System.out.println("\n=== NIO.2 Features ===");
        demonstrateNIO2Features();
        
        // Directory Operations
        System.out.println("\n=== Directory Operations ===");
        demonstrateDirectoryOperations();
        
        // Serialization
        System.out.println("\n=== Serialization ===");
        demonstrateSerialization();
        
        // Properties Files
        System.out.println("\n=== Properties Files ===");
        demonstrateProperties();
        
        // Performance Considerations
        System.out.println("\n=== Performance Considerations ===");
        performanceComparison();
    }
    
    public static void demonstrateBasicFileOperations() {
        try {
            // Create file
            File file = new File("test.txt");
            if (file.createNewFile()) {
                System.out.println("File created: " + file.getName());
            } else {
                System.out.println("File already exists: " + file.getName());
            }
            
            // Write to file
            try (FileWriter writer = new FileWriter(file)) {
                writer.write("Hello, World!\n");
                writer.write("This is a test file.\n");
                writer.write("Java File I/O demonstration.\n");
            }
            
            // File information
            System.out.println("File path: " + file.getAbsolutePath());
            System.out.println("File size: " + file.length() + " bytes");
            System.out.println("Can read: " + file.canRead());
            System.out.println("Can write: " + file.canWrite());
            System.out.println("Is file: " + file.isFile());
            
            // Read from file
            try (FileReader reader = new FileReader(file);
                 BufferedReader br = new BufferedReader(reader)) {
                
                System.out.println("File contents:");
                String line;
                while ((line = br.readLine()) != null) {
                    System.out.println("  " + line);
                }
            }
            
            // Delete file
            if (file.delete()) {
                System.out.println("File deleted: " + file.getName());
            }
            
        } catch (IOException e) {
            System.err.println("File operation error: " + e.getMessage());
        }
    }
    
    public static void demonstrateFileReadingWriting() {
        String filename = "data.txt";
        
        // Write using BufferedWriter
        try (BufferedWriter writer = new BufferedWriter(new FileWriter(filename))) {
            writer.write("Product,Price,Quantity\n");
            writer.write("Apple,1.99,10\n");
            writer.write("Banana,0.99,15\n");
            writer.write("Orange,1.49,8\n");
            writer.write("Grape,2.99,5\n");
            System.out.println("Data written to " + filename);
        } catch (IOException e) {
            System.err.println("Write error: " + e.getMessage());
        }
        
        // Read using BufferedReader
        try (BufferedReader reader = new BufferedReader(new FileReader(filename))) {
            String line;
            int lineNumber = 0;
            
            System.out.println("\nReading from " + filename + ":");
            while ((line = reader.readLine()) != null) {
                lineNumber++;
                if (lineNumber == 1) {
                    System.out.println("Header: " + line);
                } else {
                    String[] parts = line.split(",");
                    System.out.printf("Product: %s, Price: $%s, Quantity: %d%n",
                            parts[0], parts[1], Integer.parseInt(parts[2]));
                }
            }
        } catch (IOException e) {
            System.err.println("Read error: " + e.getMessage());
        }
        
        // Clean up
        new File(filename).delete();
    }
    
    public static void demonstrateNIO2Features() {
        try {
            // Create directory and file using Path
            Path dirPath = Paths.get("nio_demo");
            Path filePath = dirPath.resolve("nio_file.txt");
            
            Files.createDirectories(dirPath);
            System.out.println("Directory created: " + dirPath);
            
            // Write using Files.write()
            List<String> lines = Arrays.asList(
                "Line 1: NIO.2 is powerful",
                "Line 2: Modern file handling",
                "Line 3: Better error handling",
                "Line 4: More efficient operations"
            );
            
            Files.write(filePath, lines);
            System.out.println("File written using NIO.2: " + filePath);
            
            // Read using Files.readAllLines()
            List<String> readLines = Files.readAllLines(filePath);
            System.out.println("File contents using NIO.2:");
            readLines.forEach(line -> System.out.println("  " + line));
            
            // Copy file
            Path copyPath = dirPath.resolve("nio_copy.txt");
            Files.copy(filePath, copyPath, java.nio.file.StandardCopyOption.REPLACE_EXISTING);
            System.out.println("File copied to: " + copyPath);
            
            // File attributes
            java.nio.file.attribute.BasicFileAttributes attrs = Files.readAttributes(filePath, java.nio.file.attribute.BasicFileAttributes.class);
            System.out.println("File size: " + attrs.size() + " bytes");
            System.out.println("Creation time: " + attrs.creationTime());
            System.out.println("Last modified: " + attrs.lastModifiedTime());
            
            // Clean up
            Files.walk(dirPath)
                 .sorted(Comparator.reverseOrder())
                 .forEach(path -> {
                     try {
                         Files.delete(path);
                         System.out.println("Deleted: " + path);
                     } catch (IOException e) {
                         System.err.println("Delete error: " + e.getMessage());
                     }
                 });
            
        } catch (IOException e) {
            System.err.println("NIO.2 error: " + e.getMessage());
        }
    }
    
    public static void demonstrateDirectoryOperations() {
        try {
            // Create directory structure
            Path projectDir = Paths.get("project");
            Path srcDir = projectDir.resolve("src");
            Path testDir = projectDir.resolve("test");
            
            Files.createDirectories(srcDir);
            Files.createDirectories(testDir);
            
            // Create files in directories
            Files.write(srcDir.resolve("Main.java"), "public class Main { }".getBytes());
            Files.write(srcDir.resolve("Utils.java"), "public class Utils { }".getBytes());
            Files.write(testDir.resolve("MainTest.java"), "public class MainTest { }".getBytes());
            
            System.out.println("Created project structure:");
            
            // List directory contents
            try (java.nio.file.DirectoryStream<Path> stream = Files.newDirectoryStream(projectDir)) {
                System.out.println("Project directory contents:");
                for (Path path : stream) {
                    System.out.println("  " + path + " (" + 
                            (Files.isDirectory(path) ? "Directory" : "File") + ")");
                }
            }
            
            // Walk directory tree
            System.out.println("\nDirectory tree:");
            Files.walk(projectDir)
                 .forEach(path -> {
                     int depth = path.getNameCount() - projectDir.getNameCount();
                     String indent = "  ".repeat(depth);
                     System.out.println(indent + path.getFileName() + 
                            (Files.isDirectory(path) ? "/" : ""));
                 });
            
            // Find files
            System.out.println("\nJava files found:");
            Files.walk(projectDir)
                 .filter(path -> path.toString().endsWith(".java"))
                 .forEach(path -> System.out.println("  " + path));
            
            // Clean up
            Files.walk(projectDir)
                 .sorted(Comparator.reverseOrder())
                 .forEach(path -> {
                     try {
                         Files.delete(path);
                     } catch (IOException e) {
                         System.err.println("Delete error: " + e.getMessage());
                     }
                 });
            
        } catch (IOException e) {
            System.err.println("Directory operation error: " + e.getMessage());
        }
    }
    
    public static void demonstrateSerialization() {
        // Create objects to serialize
        List<FileIOUser> users = Arrays.asList(
            new FileIOUser("Alice", "alice@example.com", 25),
            new FileIOUser("Bob", "bob@example.com", 30),
            new FileIOUser("Charlie", "charlie@example.com", 28)
        );
        
        String filename = "users.ser";
        
        // Serialize objects
        try (ObjectOutputStream oos = new ObjectOutputStream(
                new FileOutputStream(filename))) {
            
            oos.writeObject(users);
            System.out.println("Users serialized to " + filename);
            
        } catch (IOException e) {
            System.err.println("Serialization error: " + e.getMessage());
        }
        
        // Deserialize objects
        try (ObjectInputStream ois = new ObjectInputStream(
                new FileInputStream(filename))) {
            
            @SuppressWarnings("unchecked")
            List<FileIOUser> deserializedUsers = (List<FileIOUser>) ois.readObject();
            
            System.out.println("\nDeserialized users:");
            deserializedUsers.forEach(user -> System.out.println("  " + user));
            
        } catch (IOException | ClassNotFoundException e) {
            System.err.println("Deserialization error: " + e.getMessage());
        }
        
        // Clean up
        new File(filename).delete();
    }
    
    public static void demonstrateProperties() {
        Properties props = new Properties();
        
        // Set properties
        props.setProperty("database.url", "jdbc:mysql://localhost:3306/mydb");
        props.setProperty("database.user", "admin");
        props.setProperty("database.password", "secret123");
        props.setProperty("app.name", "MyApplication");
        props.setProperty("app.version", "1.0.0");
        
        String filename = "config.properties";
        
        // Store properties to file
        try (FileOutputStream fos = new FileOutputStream(filename)) {
            props.store(fos, "Application Configuration");
            System.out.println("Properties saved to " + filename);
            
        } catch (IOException e) {
            System.err.println("Properties save error: " + e.getMessage());
        }
        
        // Load properties from file
        try (FileInputStream fis = new FileInputStream(filename)) {
            Properties loadedProps = new Properties();
            loadedProps.load(fis);
            
            System.out.println("\nLoaded properties:");
            loadedProps.forEach((key, value) -> 
                System.out.println("  " + key + " = " + value));
            
        } catch (IOException e) {
            System.err.println("Properties load error: " + e.getMessage());
        }
        
        // Clean up
        new File(filename).delete();
    }
    
    public static void performanceComparison() {
        final int LINES = 10000;
        String filename = "performance_test.txt";
        
        // Traditional I/O performance
        long startTime = System.nanoTime();
        try (BufferedWriter writer = new BufferedWriter(new FileWriter(filename))) {
            for (int i = 0; i < LINES; i++) {
                writer.write("Line " + i + ": This is a performance test line.\n");
            }
        } catch (IOException e) {
            System.err.println("Write error: " + e.getMessage());
        }
        long endTime = System.nanoTime();
        
        System.out.println("Traditional I/O write (" + LINES + " lines): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
        
        // NIO.2 performance
        startTime = System.nanoTime();
        List<String> lines = new ArrayList<>();
        for (int i = 0; i < LINES; i++) {
            lines.add("Line " + i + ": This is a performance test line.");
        }
        
        try {
            Files.write(Paths.get(filename + "_nio"), lines);
        } catch (IOException e) {
            System.err.println("NIO write error: " + e.getMessage());
        }
        endTime = System.nanoTime();
        
        System.out.println("NIO.2 write (" + LINES + " lines): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
        
        // Clean up
        new File(filename).delete();
        new File(filename + "_nio").delete();
        
        System.out.println("\nPerformance tip: NIO.2 is generally faster for bulk operations");
    }
}

// Supporting Classes

class FileIOUser implements Serializable {
    private static final long serialVersionUID = 1L;
    
    private String name;
    private String email;
    private int age;
    
    public FileIOUser(String name, String email, int age) {
        this.name = name;
        this.email = email;
        this.age = age;
    }
    
    @Override
    public String toString() {
        return String.format("User{name='%s', email='%s', age=%d}", name, email, age);
    }
    
    // Getters
    public String getName() { return name; }
    public String getEmail() { return email; }
    public int getAge() { return age; }
}
