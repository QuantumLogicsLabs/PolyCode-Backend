public class OperatorsExample {
    public static void main(String[] args) {
        // Arithmetic Operators
        System.out.println("=== Arithmetic Operators ===");
        int a = 10, b = 3;
        System.out.println("Addition: " + a + " + " + b + " = " + (a + b));
        System.out.println("Subtraction: " + a + " - " + b + " = " + (a - b));
        System.out.println("Multiplication: " + a + " * " + b + " = " + (a * b));
        System.out.println("Division: " + a + " / " + b + " = " + (a / b));
        System.out.println("Modulus: " + a + " % " + b + " = " + (a % b));
        
        // Unary Operators
        System.out.println("\n=== Unary Operators ===");
        int x = 5;
        System.out.println("Original x: " + x);
        System.out.println("Post-increment x++: " + (x++)); // Use then increment
        System.out.println("After post-increment: " + x);
        System.out.println("Pre-increment ++x: " + (++x)); // Increment then use
        System.out.println("Negation -x: " + (-x));
        
        // Assignment Operators
        System.out.println("\n=== Assignment Operators ===");
        int y = 10;
        y += 5; // y = y + 5
        System.out.println("y += 5: " + y);
        y -= 3; // y = y - 3
        System.out.println("y -= 3: " + y);
        y *= 2; // y = y * 2
        System.out.println("y *= 2: " + y);
        y /= 4; // y = y / 4
        System.out.println("y /= 4: " + y);
        y %= 3; // y = y % 3
        System.out.println("y %= 3: " + y);
        
        // Comparison Operators
        System.out.println("\n=== Comparison Operators ===");
        int p = 10, q = 20;
        System.out.println(p + " == " + q + ": " + (p == q));
        System.out.println(p + " != " + q + ": " + (p != q));
        System.out.println(p + " > " + q + ": " + (p > q));
        System.out.println(p + " < " + q + ": " + (p < q));
        System.out.println(p + " >= " + q + ": " + (p >= q));
        System.out.println(p + " <= " + q + ": " + (p <= q));
        
        // Logical Operators
        System.out.println("\n=== Logical Operators ===");
        boolean bool1 = true, bool2 = false;
        System.out.println("true && false: " + (bool1 && bool2)); // Logical AND
        System.out.println("true || false: " + (bool1 || bool2)); // Logical OR
        System.out.println("!true: " + (!bool1)); // Logical NOT
        
        // Bitwise Operators
        System.out.println("\n=== Bitwise Operators ===");
        int m = 5; // Binary: 0101
        int n = 3; // Binary: 0011
        System.out.println("5 & 3 (AND): " + (m & n)); // 0001 = 1
        System.out.println("5 | 3 (OR): " + (m | n)); // 0111 = 7
        System.out.println("5 ^ 3 (XOR): " + (m ^ n)); // 0110 = 6
        System.out.println("~5 (NOT): " + (~m)); // Complement
        System.out.println("5 << 2 (Left Shift): " + (m << 2)); // 0101 << 2 = 10100 = 20
        System.out.println("5 >> 2 (Right Shift): " + (m >> 2)); // 0101 >> 2 = 0001 = 1
        
        // Ternary Operator
        System.out.println("\n=== Ternary Operator ===");
        int age = 18;
        String result = (age >= 18) ? "Adult" : "Minor";
        System.out.println("Age " + age + " is: " + result);
        
        // Operator Precedence Example
        System.out.println("\n=== Operator Precedence ===");
        int result1 = 10 + 5 * 2; // Multiplication first
        int result2 = (10 + 5) * 2; // Parentheses first
        System.out.println("10 + 5 * 2 = " + result1);
        System.out.println("(10 + 5) * 2 = " + result2);
    }
}
