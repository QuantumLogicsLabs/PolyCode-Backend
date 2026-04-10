# Strings Example

## File Overview
This file demonstrates comprehensive string operations in Java including creation, manipulation, comparison, searching, and performance considerations.

## Key Concepts Explained

### 1. String Creation Methods

#### String Literals
```java
String str1 = "Hello World";
```
- Stored in string pool
- Reused for identical literals
- Most efficient method

#### Using new Keyword
```java
String str2 = new String("Hello World");
```
- Creates new object in heap
- Not stored in string pool
- Less efficient for identical strings

#### From Character Array
```java
char[] charArray = {'J', 'a', 'v', 'a'};
String str3 = new String(charArray);
```
- Converts character arrays to strings
- Useful for character manipulation

### 2. String Immutability

#### Immutable Nature
- Strings cannot be modified after creation
- Operations create new string objects
- Thread-safe by design

#### Memory Implications
- Multiple string objects for modifications
- Use StringBuilder for mutable strings
- Consider string pooling for optimization

### 3. String Comparison

#### Reference Equality (==)
- Compares object references
- True only for same object
- Works with string pool literals

#### Content Equality (equals)
- Compares string content
- Case-sensitive by default
- Use equalsIgnoreCase for case-insensitive

#### Comparison Methods
- `equals()`: Exact content match
- `equalsIgnoreCase()`: Case-insensitive match
- `compareTo()`: Lexicographical comparison
- `compareToIgnoreCase()`: Case-insensitive lexicographical

### 4. String Properties

#### Length and Characters
- `length()`: Number of characters
- `charAt(index)`: Character at position
- `isEmpty()`: Check if length is 0
- `isBlank()`: Check if empty or whitespace (Java 11+)

#### Unicode Support
- Strings use UTF-16 encoding
- Support for international characters
- Supplementary characters require special handling

### 5. String Searching and Manipulation

#### Search Methods
- `contains()`: Check for substring
- `startsWith()`: Check prefix
- `endsWith()`: Check suffix
- `indexOf()`: First occurrence position
- `lastIndexOf()`: Last occurrence position

#### Manipulation Methods
- `toUpperCase()`: Convert to uppercase
- `toLowerCase()`: Convert to lowercase
- `trim()`: Remove leading/trailing whitespace
- `replace()`: Replace characters or substrings
- `replaceAll()`: Replace using regex

#### Substring Operations
- `substring(beginIndex)`: From position to end
- `substring(beginIndex, endIndex)`: Range of characters
- Creates new string object

### 6. String Splitting and Joining

#### Splitting
```java
String[] parts = string.split(delimiter);
```
- Splits by delimiter (regex supported)
- Returns array of substrings
- Limit parameter controls maximum splits

#### Joining (Java 8+)
```java
String joined = String.join(delimiter, elements);
```
- Joins multiple strings/arrays
- Uses specified delimiter
- Efficient for concatenation

### 7. String Formatting

#### printf-style Formatting
```java
String formatted = String.format("Name: %s, Age: %d", name, age);
```
- Similar to C printf
- Supports various format specifiers
- Locale-aware formatting

### 8. Mutable String Alternatives

#### StringBuilder
- Not thread-safe
- Better performance for single-threaded use
- Supports append, insert, delete, reverse

#### StringBuffer
- Thread-safe (synchronized)
- Slightly slower than StringBuilder
- Use in multi-threaded environments

### 9. String Pool

#### Interning
- String literals stored in pool
- `intern()` method adds strings to pool
- Reuses identical string objects
- Memory optimization technique

#### Performance Considerations
- String concatenation in loops is expensive
- StringBuilder is much more efficient
- Consider string pool for frequently used strings

## Best Practices
1. Use string literals for fixed strings
2. Use StringBuilder for string building in loops
3. Use equals() for content comparison
4. Use isEmpty() instead of length() == 0
5. Consider StringBuilder for performance-critical code
6. Use String.join() for joining multiple strings
7. Be careful with null strings in operations

## Common Pitfalls
1. Using == instead of equals()
2. String concatenation in loops
3. Not handling null strings
4. Assuming string modification changes original
5. Inefficient string operations in performance-critical code
6. Ignoring string pool behavior

## Performance Tips
- StringBuilder > StringBuffer > String concatenation
- String pool reduces memory usage
- Avoid creating unnecessary string objects
- Use char[] for character-level manipulation
- Consider intern() for frequently used strings
