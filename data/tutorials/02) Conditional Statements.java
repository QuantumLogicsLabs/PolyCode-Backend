# ============================================================
#  Java Conditional Statements — Complete Examples
# ============================================================

# ─────────────────────────────────────────────
# SECTION 1: Simple if Statement
# ─────────────────────────────────────────────
print("=" * 50)
print("SECTION 1: if Statement")
print("=" * 50)

temperature = 38
if temperature > 37.5:
    print("You have a fever!")

score = 85
if score >= 50:
    print(f"Score {score}: PASSED")

# ─────────────────────────────────────────────
# SECTION 2: if-else Statement
# ─────────────────────────────────────────────
print("\n--- if-else ---")

age = 16
if age >= 18:
    print("You can vote.")
else:
    print("You cannot vote yet.")

number = -7
if number >= 0:
    print(f"{number} is positive or zero")
else:
    print(f"{number} is negative")

# ─────────────────────────────────────────────
# SECTION 3: if-elif-else Statement
# ─────────────────────────────────────────────
print("\n--- if-elif-else ---")

marks = 72
if marks >= 90:
    grade = "A (Excellent)"
elif marks >= 80:
    grade = "B (Good)"
elif marks >= 70:
    grade = "C (Average)"
elif marks >= 60:
    grade = "D (Below Average)"
else:
    grade = "F (Fail)"
print(f"Marks: {marks} → Grade: {grade}")


# Day category
day = "Saturday"
if day == "Monday":
    print("Start of work week")
elif day in ["Tuesday", "Wednesday", "Thursday"]:
    print("Midweek")
elif day == "Friday":
    print("End of work week — TGIF!")
elif day in ["Saturday", "Sunday"]:
    print("Weekend! 🎉")
else:
    print("Invalid day")

# ─────────────────────────────────────────────
# SECTION 4: Nested if Statements
# ─────────────────────────────────────────────
print("\n--- Nested if ---")

username = "admin"
password = "secure123"

if username == "admin":
    if password == "secure123":
        print("Login successful! Welcome, Admin.")
    else:
        print("Wrong password.")
else:
    print("Username not found.")


# ATM simulation
balance = 5000
withdrawal = 2000

if withdrawal > 0:
    if withdrawal <= balance:
        if withdrawal % 100 == 0:
            balance -= withdrawal
            print(f"Dispensed: ${withdrawal}. Remaining: ${balance}")
        else:
            print("Amount must be in multiples of 100")
    else:
        print("Insufficient funds!")
else:
    print("Invalid amount")

# ─────────────────────────────────────────────
# SECTION 5: Ternary (One-Line) Operator
# ─────────────────────────────────────────────
print("\n--- Ternary Operator ---")

age = 20
status = "Adult" if age >= 18 else "Minor"
print(f"Age {age}: {status}")

a, b = 15, 28
maximum = a if a > b else b
minimum = a if a < b else b
print(f"a={a}, b={b} → Max: {maximum}, Min: {minimum}")

# Ternary in print
for n in range(1, 6):
    print(f"{n} is {'even' if n % 2 == 0 else 'odd'}")

# ─────────────────────────────────────────────
# SECTION 6: Logical Operators in Conditions
# ─────────────────────────────────────────────
print("\n--- Logical Operators (and, or, not) ---")

age = 25
income = 50000

# and — both must be True
if age >= 18 and income >= 30000:
    print("Eligible for loan")
else:
    print("Not eligible for loan")

# or — at least one must be True
has_cash = False
has_card = True
if has_cash or has_card:
    print("Can pay for purchase")

# not — reverses condition
is_raining = False
if not is_raining:
    print("No umbrella needed today")

# combined
x = 15
if x > 10 and x < 20 and x % 3 == 0:
    print(f"{x} is between 10-20 AND divisible by 3")

# ─────────────────────────────────────────────
# SECTION 7: Comparison Operators
# ─────────────────────────────────────────────
print("\n--- Comparison Operators ---")

a, b = 10, 20
print(f"a={a}, b={b}")
print(f"a == b → {a == b}")
print(f"a != b → {a != b}")
print(f"a > b  → {a > b}")
print(f"a < b  → {a < b}")
print(f"a >= b → {a >= b}")
print(f"a <= b → {a <= b}")

# ─────────────────────────────────────────────
# SECTION 8: Special Conditions
# ─────────────────────────────────────────────
print("\n--- Special Conditions ---")

# None check
value = None
if value is None:
    print("Value is None (not assigned)")

# in / not in
fruits = ["apple", "banana", "mango"]
if "banana" in fruits:
    print("Banana is in the list")
if "grape" not in fruits:
    print("Grape is not in the list")

# Truthy / Falsy
empty_list = []
if not empty_list:
    print("List is empty (falsy)")

name = "Alice"
if name:
    print(f"Name is set: {name}")

# ─────────────────────────────────────────────
# SECTION 9: match-case (Java 3.10+)
# ─────────────────────────────────────────────
print("\n--- match-case ---")

http_status = 404

match http_status:
    case 200:
        print("200 OK — Success")
    case 301 | 302:
        print("Redirect")
    case 404:
        print("404 Not Found")
    case 500:
        print("500 Internal Server Error")
    case _:
        print("Unknown status code")

# ─────────────────────────────────────────────
# SECTION 10: Real-World Mini Program
# ─────────────────────────────────────────────
print("\n--- Mini Program: BMI Calculator ---")

weight = 70   # kg
height = 1.75  # meters

bmi = weight / (height ** 2)
print(f"Weight: {weight}kg, Height: {height}m")
print(f"BMI: {bmi:.2f}")

if bmi < 18.5:
    category = "Underweight"
elif bmi < 25:
    category = "Normal weight"
elif bmi < 30:
    category = "Overweight"
else:
    category = "Obese"

print(f"Category: {category}")

print("\n✅ All Conditional Statement examples completed!")
