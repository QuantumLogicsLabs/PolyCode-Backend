# ============================================================
#  Python I/O Statements — Complete Examples
# ============================================================

# ─────────────────────────────────────────────
# SECTION 1: Basic print() Output
# ─────────────────────────────────────────────

print("=" * 50)
print("SECTION 1: Basic Output with print()")
print("=" * 50)

print("Hello, World!")           # string
print(42)                         # integer
print(3.14)                       # float
print(True)                       # boolean

# ─────────────────────────────────────────────
# SECTION 2: print() with sep and end
# ─────────────────────────────────────────────

print("\n--- sep and end ---")

print("Python", "Java", "C++", sep=" | ")
# Output: Python | Java | C++

print("Loading", end="")
print("...", end="")
print(" Done!")
# Output: Loading... Done!

# ─────────────────────────────────────────────
# SECTION 3: Formatted Output
# ─────────────────────────────────────────────

print("\n--- Formatted Output ---")

name = "Alice"
age = 25
gpa = 3.876

# f-strings (modern, recommended)
print(f"Name: {name}, Age: {age}, GPA: {gpa:.2f}")

# .format() method
print("Name: {}, Age: {}".format(name, age))

# % operator (classic)
print("Name: %s, Age: %d" % (name, age))

# Padding and alignment
print(f"{'Item':<15} {'Price':>10}")
print(f"{'Apple':<15} {'$1.20':>10}")
print(f"{'Watermelon':<15} {'$5.00':>10}")

# ─────────────────────────────────────────────
# SECTION 4: Special Characters
# ─────────────────────────────────────────────

print("\n--- Special Characters ---")
print("Line 1\nLine 2\nLine 3")
print("Col1\tCol2\tCol3")
print("He said \"Python is great!\"")
print("Path: C:\\Users\\Alice")

# ─────────────────────────────────────────────
# SECTION 5: input() — User Input
# ─────────────────────────────────────────────

print("\n--- Input Examples (Demo - values hardcoded) ---")

# In real use, you'd do: name = input("Enter your name: ")
# Here we simulate it:
name = "Bob"
print(f"[Simulated input: '{name}']")
print(f"Hello, {name}!")

# Integer input
age = int("20")   # simulates: int(input("Enter age: "))
print(f"[Simulated int input: {age}]")
print(f"In 5 years you'll be {age + 5}")

# Float input
price = float("29.99")   # simulates: float(input("Enter price: "))
print(f"[Simulated float input: {price}]")
tax = price * 0.1
print(f"Tax: ${tax:.2f}, Total: ${price + tax:.2f}")

# ─────────────────────────────────────────────
# SECTION 6: Multiple Values from Input
# ─────────────────────────────────────────────

print("\n--- Multiple Values ---")

# Simulating: x, y = map(int, input("Enter two nums: ").split())
data = "10 20"
x, y = map(int, data.split())
print(f"x={x}, y={y}, sum={x+y}, product={x*y}")

# ─────────────────────────────────────────────
# SECTION 7: Type Conversion
# ─────────────────────────────────────────────

print("\n--- Type Conversion ---")

num_str = "42"
num_int = int(num_str)
num_float = float(num_str)

print(f"String: '{num_str}' → type: {type(num_str)}")
print(f"Integer: {num_int} → type: {type(num_int)}")
print(f"Float: {num_float} → type: {type(num_float)}")

# ─────────────────────────────────────────────
# SECTION 8: File I/O
# ─────────────────────────────────────────────

print("\n--- File I/O ---")

# Writing to a file
with open("sample.txt", "w") as f:
    f.write("Line 1: Python is fun\n")
    f.write("Line 2: I/O is important\n")
    f.write("Line 3: Keep practicing!\n")
print("File written successfully.")

# Reading entire file
with open("sample.txt", "r") as f:
    content = f.read()
print("File contents:\n" + content)

# Reading line by line
print("Line by line:")
with open("sample.txt", "r") as f:
    for i, line in enumerate(f, 1):
        print(f"  Line {i}: {line.strip()}")

# Appending to a file
with open("sample.txt", "a") as f:
    f.write("Line 4: Appended line\n")
print("Line appended.")

print("\n✅ All I/O examples completed!")
