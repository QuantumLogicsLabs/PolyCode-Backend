public class StringsExample {
    public static void main(String[] args) {
        // String Creation
        System.out.println("=== String Creation ===");
        
        // Method 1: String literal
        String str1 = "Hello World";
        
        // Method 2: Using new keyword
        String str2 = new String("Hello World");
        
        // Method 3: From character array
        char[] charArray = {'J', 'a', 'v', 'a'};
        String str3 = new String(charArray);
        
        System.out.println("str1: " + str1);
        System.out.println("str2: " + str2);
        System.out.println("str3: " + str3);
        
        // String Immutability
        System.out.println("\n=== String Immutability ===");
        String original = "Hello";
        String modified = original + " World";
        System.out.println("Original: " + original);
        System.out.println("Modified: " + modified);
        System.out.println("Same reference? " + (original == modified));
        
        // String Comparison
        System.out.println("\n=== String Comparison ===");
        String a = "Java";
        String b = "Java";
        String c = new String("Java");
        String d = "java";
        
        System.out.println("a == b: " + (a == b)); // true (same literal pool)
        System.out.println("a == c: " + (a == c)); // false (different objects)
        System.out.println("a.equals(c): " + a.equals(c)); // true (same content)
        System.out.println("a.equals(d): " + a.equals(d)); // false (case sensitive)
        System.out.println("a.equalsIgnoreCase(d): " + a.equalsIgnoreCase(d)); // true
        
        // String Length and Character Access
        System.out.println("\n=== String Length and Characters ===");
        String text = "Programming";
        System.out.println("String: " + text);
        System.out.println("Length: " + text.length());
        System.out.println("First character: " + text.charAt(0));
        System.out.println("Last character: " + text.charAt(text.length() - 1));
        
        // String Searching
        System.out.println("\n=== String Searching ===");
        String sentence = "Java Programming Language";
        System.out.println("Sentence: " + sentence);
        System.out.println("Contains 'Java': " + sentence.contains("Java"));
        System.out.println("Starts with 'Java': " + sentence.startsWith("Java"));
        System.out.println("Ends with 'Language': " + sentence.endsWith("Language"));
        System.out.println("Index of 'Program': " + sentence.indexOf("Program"));
        System.out.println("Last index of 'a': " + sentence.lastIndexOf("a"));
        
        // String Manipulation
        System.out.println("\n=== String Manipulation ===");
        String name = " john doe ";
        System.out.println("Original: '" + name + "'");
        System.out.println("Trimmed: '" + name.trim() + "'");
        System.out.println("Uppercase: " + name.toUpperCase());
        System.out.println("Lowercase: " + name.toLowerCase());
        System.out.println("Replace 'o' with '0': " + name.replace('o', '0'));
        System.out.println("Replace 'john' with 'jane': " + name.replace("john", "jane"));
        
        // String Substring
        System.out.println("\n=== String Substring ===");
        String fullName = "John Michael Doe";
        System.out.println("Full name: " + fullName);
        System.out.println("First name: " + fullName.substring(0, 4));
        System.out.println("Last name: " + fullName.substring(12));
        System.out.println("Middle name: " + fullName.substring(5, 12));
        
        // String Splitting
        System.out.println("\n=== String Splitting ===");
        String data = "apple,banana,orange,grape";
        String[] fruits = data.split(",");
        System.out.println("Original: " + data);
        System.out.println("Split result:");
        for (String fruit : fruits) {
            System.out.println("- " + fruit);
        }
        
        // String Joining (Java 8+)
        System.out.println("\n=== String Joining ===");
        String[] words = {"Java", "is", "awesome"};
        String joined1 = String.join(" ", words);
        String joined2 = String.join("-", words);
        System.out.println("Joined with spaces: " + joined1);
        System.out.println("Joined with hyphens: " + joined2);
        
        // String Formatting
        System.out.println("\n=== String Formatting ===");
        String formatted = String.format("Name: %s, Age: %d, Score: %.2f", "Alice", 25, 95.5);
        System.out.println("Formatted string: " + formatted);
        
        // StringBuilder for Mutable Strings
        System.out.println("\n=== StringBuilder ===");
        StringBuilder sb = new StringBuilder();
        sb.append("Hello");
        sb.append(" ");
        sb.append("World");
        sb.append("!");
        System.out.println("StringBuilder result: " + sb.toString());
        
        // StringBuilder operations
        sb.insert(5, " Beautiful");
        System.out.println("After insert: " + sb.toString());
        sb.delete(5, 15);
        System.out.println("After delete: " + sb.toString());
        sb.reverse();
        System.out.println("Reversed: " + sb.toString());
        
        // StringBuffer (Thread-safe version)
        System.out.println("\n=== StringBuffer ===");
        StringBuffer buffer = new StringBuffer("Thread Safe");
        buffer.append(" String");
        System.out.println("StringBuffer result: " + buffer.toString());
        
        // Common String Operations
        System.out.println("\n=== Common Operations ===");
        demonstrateCommonOperations();
        
        // String Pool Demonstration
        System.out.println("\n=== String Pool ===");
        demonstrateStringPool();
    }
    
    public static void demonstrateCommonOperations() {
        String text = "  Java Programming 123  ";
        
        // Remove extra spaces and numbers
        String cleaned = text.trim().replaceAll("\\d+", "").replaceAll("\\s+", " ");
        System.out.println("Original: '" + text + "'");
        System.out.println("Cleaned: '" + cleaned + "'");
        
        // Check if string is empty or blank
        String empty = "";
        String blank = "   ";
        System.out.println("Empty string isEmpty(): " + empty.isEmpty());
        System.out.println("Blank string isEmpty(): " + blank.isEmpty());
        System.out.println("Blank string isBlank() (Java 11+): " + blank.isBlank());
        
        // Count occurrences
        String repeated = "banana";
        int count = repeated.length() - repeated.replace("a", "").length();
        System.out.println("Occurrences of 'a' in 'banana': " + count);
        
        // Palindrome check
        String palindrome = "racecar";
        boolean isPalindrome = palindrome.equals(new StringBuilder(palindrome).reverse().toString());
        System.out.println("Is 'racecar' a palindrome? " + isPalindrome);
    }
    
    public static void demonstrateStringPool() {
        String s1 = "Hello";
        String s2 = "Hello";
        String s3 = new String("Hello");
        String s4 = s3.intern();
        
        System.out.println("s1 == s2: " + (s1 == s2)); // true (same pool object)
        System.out.println("s1 == s3: " + (s1 == s3)); // false (different objects)
        System.out.println("s1 == s4: " + (s1 == s4)); // true (interned to pool)
        
        // Creating many strings (performance consideration)
        long start = System.currentTimeMillis();
        String result = "";
        for (int i = 0; i < 10000; i++) {
            result += i;
        }
        long end = System.currentTimeMillis();
        System.out.println("String concatenation time: " + (end - start) + "ms");
        
        // Using StringBuilder (more efficient)
        start = System.currentTimeMillis();
        StringBuilder sb = new StringBuilder();
        for (int i = 0; i < 10000; i++) {
            sb.append(i);
        }
        sb.toString(); // Result not used, just for timing
        end = System.currentTimeMillis();
        System.out.println("StringBuilder time: " + (end - start) + "ms");
    }
}
