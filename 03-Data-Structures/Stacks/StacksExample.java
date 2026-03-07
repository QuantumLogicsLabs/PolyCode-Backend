import java.util.Stack;

public class StacksExample {
    public static void main(String[] args) {
        // Using Java's built-in Stack
        System.out.println("=== Java Built-in Stack ===");
        Stack<Integer> stack = new Stack<>();
        
        stack.push(10);
        stack.push(20);
        stack.push(30);
        stack.push(40);
        
        System.out.println("Stack: " + stack);
        System.out.println("Top element: " + stack.peek());
        System.out.println("Stack size: " + stack.size());
        System.out.println("Is empty: " + stack.isEmpty());
        
        System.out.println("Popped: " + stack.pop());
        System.out.println("After pop: " + stack);
        
        // Custom Stack Implementation
        System.out.println("\n=== Custom Stack Implementation ===");
        CustomStack<String> customStack = new CustomStack<>();
        
        customStack.push("Apple");
        customStack.push("Banana");
        customStack.push("Orange");
        customStack.push("Grape");
        
        System.out.println("Custom Stack: " + customStack);
        System.out.println("Top element: " + customStack.peek());
        System.out.println("Stack size: " + customStack.getSize());
        
        System.out.println("Popped: " + customStack.pop());
        System.out.println("After pop: " + customStack);
        
        // Stack Applications
        System.out.println("\n=== Stack Applications ===");
        
        // Palindrome check
        System.out.println("Palindrome Check:");
        System.out.println("Is 'racecar' palindrome: " + isPalindrome("racecar"));
        System.out.println("Is 'hello' palindrome: " + isPalindrome("hello"));
        
        // Bracket balancing
        System.out.println("\nBracket Balancing:");
        System.out.println("Balanced '{[()]}': " + isBalanced("{[()]}"));
        System.out.println("Balanced '{[(])}': " + isBalanced("{[(])}"));
        
        // Reverse string
        System.out.println("\nString Reversal:");
        String original = "Hello World";
        String reversed = reverseString(original);
        System.out.println("Original: " + original);
        System.out.println("Reversed: " + reversed);
        
        // Expression evaluation
        System.out.println("\nExpression Evaluation:");
        int result = evaluatePostfix("3 4 + 2 * 7 /");
        System.out.println("Postfix '3 4 + 2 * 7 /' = " + result);
        
        // Stack with Array Implementation
        System.out.println("\n=== Array-based Stack ===");
        ArrayStack<Integer> arrayStack = new ArrayStack<>(5);
        
        arrayStack.push(1);
        arrayStack.push(2);
        arrayStack.push(3);
        
        System.out.println("Array Stack: " + arrayStack);
        System.out.println("Is full: " + arrayStack.isFull());
        
        arrayStack.push(4);
        arrayStack.push(5);
        System.out.println("After pushing 5: " + arrayStack);
        System.out.println("Is full: " + arrayStack.isFull());
        
        // Try to push to full stack
        try {
            arrayStack.push(6);
        } catch (IllegalStateException e) {
            System.out.println("Cannot push to full stack: " + e.getMessage());
        }
        
        // Performance comparison
        System.out.println("\n=== Performance Comparison ===");
        performanceComparison();
    }
    
    // Check if string is palindrome using stack
    public static boolean isPalindrome(String str) {
        Stack<Character> stack = new Stack<>();
        
        // Push all characters to stack
        for (char c : str.toCharArray()) {
            stack.push(c);
        }
        
        // Compare characters
        for (char c : str.toCharArray()) {
            if (stack.pop() != c) {
                return false;
            }
        }
        
        return true;
    }
    
    // Check if brackets are balanced
    public static boolean isBalanced(String expression) {
        Stack<Character> stack = new Stack<>();
        
        for (char c : expression.toCharArray()) {
            if (c == '{' || c == '[' || c == '(') {
                stack.push(c);
            } else if (c == '}' || c == ']' || c == ')') {
                if (stack.isEmpty()) {
                    return false;
                }
                
                char top = stack.pop();
                if ((c == '}' && top != '{') ||
                    (c == ']' && top != '[') ||
                    (c == ')' && top != '(')) {
                    return false;
                }
            }
        }
        
        return stack.isEmpty();
    }
    
    // Reverse string using stack
    public static String reverseString(String str) {
        Stack<Character> stack = new Stack<>();
        
        // Push all characters
        for (char c : str.toCharArray()) {
            stack.push(c);
        }
        
        // Pop to build reversed string
        StringBuilder reversed = new StringBuilder();
        while (!stack.isEmpty()) {
            reversed.append(stack.pop());
        }
        
        return reversed.toString();
    }
    
    // Evaluate postfix expression
    public static int evaluatePostfix(String expression) {
        Stack<Integer> stack = new Stack<>();
        String[] tokens = expression.split(" ");
        
        for (String token : tokens) {
            if (token.matches("\\d+")) {
                stack.push(Integer.parseInt(token));
            } else {
                int b = stack.pop();
                int a = stack.pop();
                int result;
                
                switch (token) {
                    case "+":
                        result = a + b;
                        break;
                    case "-":
                        result = a - b;
                        break;
                    case "*":
                        result = a * b;
                        break;
                    case "/":
                        result = a / b;
                        break;
                    default:
                        throw new IllegalArgumentException("Invalid operator: " + token);
                }
                stack.push(result);
            }
        }
        
        return stack.pop();
    }
    
    public static void performanceComparison() {
        final int OPERATIONS = 100000;
        
        // Test Java Stack
        Stack<Integer> javaStack = new Stack<>();
        long startTime = System.nanoTime();
        
        for (int i = 0; i < OPERATIONS; i++) {
            javaStack.push(i);
        }
        
        for (int i = 0; i < OPERATIONS; i++) {
            javaStack.pop();
        }
        
        long endTime = System.nanoTime();
        System.out.println("Java Stack (" + OPERATIONS + " operations): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
        
        // Test Custom Stack
        CustomStack<Integer> customStack = new CustomStack<>();
        startTime = System.nanoTime();
        
        for (int i = 0; i < OPERATIONS; i++) {
            customStack.push(i);
        }
        
        for (int i = 0; i < OPERATIONS; i++) {
            customStack.pop();
        }
        
        endTime = System.nanoTime();
        System.out.println("Custom Stack (" + OPERATIONS + " operations): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
    }
}

// Custom Stack Implementation using Linked List
class CustomStack<T> {
    private Node<T> top;
    private int size;
    
    private static class Node<T> {
        T data;
        Node<T> next;
        
        Node(T data) {
            this.data = data;
            this.next = null;
        }
    }
    
    public CustomStack() {
        top = null;
        size = 0;
    }
    
    // Push element to stack
    public void push(T data) {
        Node<T> newNode = new Node<>(data);
        newNode.next = top;
        top = newNode;
        size++;
    }
    
    // Pop element from stack
    public T pop() {
        if (isEmpty()) {
            throw new IllegalStateException("Stack is empty");
        }
        
        T data = top.data;
        top = top.next;
        size--;
        return data;
    }
    
    // Peek at top element
    public T peek() {
        if (isEmpty()) {
            throw new IllegalStateException("Stack is empty");
        }
        
        return top.data;
    }
    
    // Check if stack is empty
    public boolean isEmpty() {
        return top == null;
    }
    
    // Get stack size
    public int getSize() {
        return size;
    }
    
    // Convert to string
    @Override
    public String toString() {
        StringBuilder sb = new StringBuilder();
        sb.append("[");
        
        Node<T> current = top;
        while (current != null) {
            sb.append(current.data);
            if (current.next != null) {
                sb.append(", ");
            }
            current = current.next;
        }
        
        sb.append("]");
        return sb.toString();
    }
}

// Array-based Stack Implementation
class ArrayStack<T> {
    private T[] array;
    private int top;
    private int capacity;
    
    @SuppressWarnings("unchecked")
    public ArrayStack(int capacity) {
        this.capacity = capacity;
        this.array = (T[]) new Object[capacity];
        this.top = -1;
    }
    
    // Push element
    public void push(T data) {
        if (isFull()) {
            throw new IllegalStateException("Stack is full");
        }
        
        array[++top] = data;
    }
    
    // Pop element
    public T pop() {
        if (isEmpty()) {
            throw new IllegalStateException("Stack is empty");
        }
        
        T data = array[top];
        array[top--] = null; // Avoid memory leak
        return data;
    }
    
    // Peek at top element
    public T peek() {
        if (isEmpty()) {
            throw new IllegalStateException("Stack is empty");
        }
        
        return array[top];
    }
    
    // Check if empty
    public boolean isEmpty() {
        return top == -1;
    }
    
    // Check if full
    public boolean isFull() {
        return top == capacity - 1;
    }
    
    // Get size
    public int getSize() {
        return top + 1;
    }
    
    // Convert to string
    @Override
    public String toString() {
        StringBuilder sb = new StringBuilder();
        sb.append("[");
        
        for (int i = top; i >= 0; i--) {
            sb.append(array[i]);
            if (i > 0) {
                sb.append(", ");
            }
        }
        
        sb.append("]");
        return sb.toString();
    }
}
