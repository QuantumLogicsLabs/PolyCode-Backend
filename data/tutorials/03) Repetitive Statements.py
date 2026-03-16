# ============================================================
#  Python Repetitive Statements (Loops) — Complete Examples
# ============================================================

# ─────────────────────────────────────────────
# SECTION 1: for Loop with range()
# ─────────────────────────────────────────────
print("=" * 50)
print("SECTION 1: for loop with range()")
print("=" * 50)

# range(stop)
print("range(5):", end=" ")
for i in range(5):
    print(i, end=" ")
print()

# range(start, stop)
print("range(1, 6):", end=" ")
for i in range(1, 6):
    print(i, end=" ")
print()

# range(start, stop, step)
print("Even numbers:", end=" ")
for i in range(0, 11, 2):
    print(i, end=" ")
print()

# Countdown
print("Countdown:", end=" ")
for i in range(10, 0, -1):
    print(i, end=" ")
print("Blast off! 🚀")

# ─────────────────────────────────────────────
# SECTION 2: for Loop over Collections
# ─────────────────────────────────────────────
print("\n--- for over List, String, Dict ---")

# List
fruits = ["apple", "banana", "mango", "grape"]
print("Fruits:")
for fruit in fruits:
    print(f"  - {fruit}")

# String
word = "Python"
print(f"\nLetters in '{word}':", end=" ")
for char in word:
    print(char, end=" ")
print()

# Tuple
coordinates = (10, 20, 30)
for coord in coordinates:
    print(f"Coordinate: {coord}")

# Dictionary
student = {"name": "Alice", "age": 20, "grade": "A"}
print("\nStudent info:")
for key, value in student.items():
    print(f"  {key}: {value}")

# ─────────────────────────────────────────────
# SECTION 3: enumerate() and zip()
# ─────────────────────────────────────────────
print("\n--- enumerate() ---")

languages = ["Python", "Java", "C++", "JavaScript"]
for index, lang in enumerate(languages, start=1):
    print(f"  {index}. {lang}")

print("\n--- zip() ---")
names = ["Alice", "Bob", "Charlie"]
scores = [95, 82, 78]
for name, score in zip(names, scores):
    print(f"  {name}: {score}")

# ─────────────────────────────────────────────
# SECTION 4: while Loop
# ─────────────────────────────────────────────
print("\n--- while Loop ---")

# Basic while
count = 1
while count <= 5:
    print(f"Count: {count}")
    count += 1

# While with condition
print("\nFactorial of 5:")
n = 5
factorial = 1
i = 1
while i <= n:
    factorial *= i
    i += 1
print(f"5! = {factorial}")

# ─────────────────────────────────────────────
# SECTION 5: break Statement
# ─────────────────────────────────────────────
print("\n--- break ---")

# Stop at 5
print("Stop at 5:", end=" ")
for i in range(10):
    if i == 5:
        break
    print(i, end=" ")
print()

# Find first even number
numbers = [1, 3, 7, 4, 9, 12]
print("Finding first even number...")
for num in numbers:
    if num % 2 == 0:
        print(f"Found: {num}")
        break

# while with break
print("while + break:")
x = 0
while True:
    x += 1
    if x > 5:
        break
    print(f"  x = {x}")

# ─────────────────────────────────────────────
# SECTION 6: continue Statement
# ─────────────────────────────────────────────
print("\n--- continue ---")

# Skip even numbers
print("Odd numbers up to 10:", end=" ")
for i in range(1, 11):
    if i % 2 == 0:
        continue
    print(i, end=" ")
print()

# Skip negative numbers
data = [3, -1, 7, -5, 2, 8, -3, 6]
print("Positive numbers only:", end=" ")
for num in data:
    if num < 0:
        continue
    print(num, end=" ")
print()

# ─────────────────────────────────────────────
# SECTION 7: pass Statement
# ─────────────────────────────────────────────
print("\n--- pass ---")

for i in range(5):
    if i == 2:
        pass   # placeholder — do nothing, just continue
    print(i, end=" ")
print()

# ─────────────────────────────────────────────
# SECTION 8: else with Loops
# ─────────────────────────────────────────────
print("\n--- else with Loops ---")

# else runs when loop completes normally
for i in range(1, 4):
    print(i, end=" ")
else:
    print("← Loop finished normally")

# else does NOT run when break is used
print("Searching for 99...")
for num in [1, 5, 3, 7, 2]:
    if num == 99:
        print("Found 99!")
        break
else:
    print("99 not found in list")

# ─────────────────────────────────────────────
# SECTION 9: Nested Loops
# ─────────────────────────────────────────────
print("\n--- Nested Loops ---")

# Multiplication table (3x3)
print("Multiplication Table (3x3):")
print("    ", end="")
for j in range(1, 4):
    print(f"{j:4}", end="")
print()
print("   " + "-" * 13)
for i in range(1, 4):
    print(f"{i:2} |", end="")
    for j in range(1, 4):
        print(f"{i*j:4}", end="")
    print()

# Triangle pattern
print("\nStar Triangle:")
for i in range(1, 6):
    for j in range(i):
        print("*", end=" ")
    print()

# ─────────────────────────────────────────────
# SECTION 10: List Comprehension
# ─────────────────────────────────────────────
print("\n--- List Comprehension ---")

# Basic
squares = [n ** 2 for n in range(1, 8)]
print(f"Squares: {squares}")

# With condition
evens = [n for n in range(1, 21) if n % 2 == 0]
print(f"Evens 1-20: {evens}")

# String transformation
names = ["alice", "bob", "charlie"]
upper_names = [name.upper() for name in names]
print(f"Uppercase: {upper_names}")

# Nested comprehension (flatten 2D)
matrix = [[1, 2, 3], [4, 5, 6], [7, 8, 9]]
flat = [num for row in matrix for num in row]
print(f"Flattened: {flat}")

# ─────────────────────────────────────────────
# SECTION 11: Real-World Example — Prime Numbers
# ─────────────────────────────────────────────
print("\n--- Mini Program: Prime Numbers up to 50 ---")

primes = []
for num in range(2, 51):
    is_prime = True
    for i in range(2, int(num**0.5) + 1):
        if num % i == 0:
            is_prime = False
            break
    if is_prime:
        primes.append(num)

print(f"Primes: {primes}")
print(f"Count: {len(primes)}")

print("\n✅ All Loop examples completed!")
