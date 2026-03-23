# Level 2: Intermediate Programming - Building Real Applications

**Duration:** 8 Weeks | **Difficulty:** Intermediate | **Prerequisites:** Level 1 Complete

## 🎯 Weeks 7-8: Object-Oriented Programming

### Day 29: Classes and Objects

#### Understanding Object-Oriented Programming (OOP)
OOP is a programming paradigm that uses "objects" to represent data and behavior.

#### What are Classes and Objects?
- **Class**: Blueprint/template for creating objects
- **Object**: Instance of a class with actual data
- **Attribute**: Data stored in an object
- **Method**: Function that belongs to an object

#### Your First Class
```java
class Dog:
    """Simple Dog class"""
    
    # Class attribute (shared by all dogs)
    species = "Canis familiaris"
    
    # Constructor method (called when creating object)
    def __init__(self, name, age, breed):
        """Initialize a new dog"""
        self.name = name          # Instance attribute
        self.age = age            # Instance attribute
        self.breed = breed        # Instance attribute
    
    # Instance method
    def bark(self):
        """Make the dog bark"""
        return f"{self.name} says: Woof!"
    
    # Another instance method
    def describe(self):
        """Describe the dog"""
        return f"{self.name} is a {self.age}-year-old {self.breed}"
    
    # Method that modifies object state
    def celebrate_birthday(self):
        """Celebrate dog's birthday"""
        self.age += 1
        return f"Happy birthday {self.name}! Now {self.age} years old!"

# Creating objects (instances of the class)
dog1 = Dog("Buddy", 3, "Golden Retriever")
dog2 = Dog("Lucy", 5, "Labrador")

# Using objects
print(dog1.bark())           # Buddy says: Woof!
print(dog1.describe())       # Buddy is a 3-year-old Golden Retriever
print(dog2.describe())       # Lucy is a 5-year-old Labrador

# Accessing attributes
print(f"{dog1.name}'s species: {dog1.species}")
print(f"{dog2.name}'s age: {dog2.age}")

# Modifying object state
print(dog1.celebrate_birthday())
print(f"New age: {dog1.age}")
```

#### Class with More Complex Behavior
```java
class BankAccount:
    """Bank account class demonstrating encapsulation"""
    
    def __init__(self, account_number, owner_name, initial_balance=0):
        """Initialize bank account"""
        self.account_number = account_number
        self.owner_name = owner_name
        self.balance = initial_balance
        self.transaction_history = []
        
    def deposit(self, amount):
        """Deposit money to account"""
        if amount <= 0:
            return "Deposit amount must be positive!"
        
        self.balance += amount
        self.transaction_history.append(f"Deposit: +${amount:.2f}")
        return f"Deposited ${amount:.2f}. New balance: ${self.balance:.2f}"
    
    def withdraw(self, amount):
        """Withdraw money from account"""
        if amount <= 0:
            return "Withdrawal amount must be positive!"
        elif amount > self.balance:
            return "Insufficient funds!"
        
        self.balance -= amount
        self.transaction_history.append(f"Withdrawal: -${amount:.2f}")
        return f"Withdrew ${amount:.2f}. New balance: ${self.balance:.2f}"
    
    def get_balance(self):
        """Get current balance"""
        return f"Current balance: ${self.balance:.2f}"
    
    def get_transaction_history(self):
        """Get transaction history"""
        if not self.transaction_history:
            return "No transactions yet."
        
        history = "Transaction History:\n"
        for transaction in self.transaction_history:
            history += f"  {transaction}\n"
        return history
    
    def __str__(self):
        """String representation of the account"""
        return f"Account {self.account_number}: {self.owner_name} - Balance: ${self.balance:.2f}"

# Test the BankAccount class
account1 = BankAccount("12345", "Alice Smith", 1000)
print(account1)
print(account1.deposit(500))
print(account1.withdraw(200))
print(account1.get_balance())
print(account1.get_transaction_history())
```

### Day 30: Methods and Attributes

#### Types of Methods
```java
class Student:
    """Student class with different types of methods"""
    
    # Class attribute
    school = "Java High School"
    total_students = 0
    
    def __init__(self, name, grade, student_id):
        """Constructor - instance method"""
        self.name = name
        self.grade = grade
        self.student_id = student_id
        self.courses = []
        Student.total_students += 1
    
    # Instance methods (operate on instance data)
    def add_course(self, course_name):
        """Add a course to student's schedule"""
        if course_name not in self.courses:
            self.courses.append(course_name)
            return f"Added {course_name} to {self.name}'s courses"
        else:
            return f"{course_name} already in {self.name}'s courses"
    
    def get_info(self):
        """Get student information"""
        course_list = ", ".join(self.courses) if self.courses else "No courses"
        return f"{self.name} (ID: {self.student_id}) - Grade {self.grade} - Courses: {course_list}"
    
    # Class method (operates on class data)
    @classmethod
    def get_total_students(cls):
        """Get total number of students"""
        return f"Total students at {cls.school}: {cls.total_students}"
    
    @classmethod
    def change_school(cls, new_school_name):
        """Change school name for all students"""
        old_school = cls.school
        cls.school = new_school_name
        return f"School changed from {old_school} to {new_school_name}"
    
    # Static method (doesn't use instance or class data)
    @staticmethod
    def validate_grade(grade):
        """Validate if grade is acceptable"""
        if grade < 9 or grade > 12:
            return False, "Grade must be between 9 and 12"
        return True, "Valid grade"
    
    # Special methods (dunder methods)
    def __str__(self):
        """String representation"""
        return f"Student: {self.name} ({self.grade})"
    
    def __repr__(self):
        """Official representation"""
        return f"Student('{self.name}', {self.grade}, '{self.student_id}')"
    
    def __len__(self):
        """Return number of courses"""
        return len(self.courses)

# Test the Student class
student1 = Student("Alice Johnson", 10, "S1001")
student2 = Student("Bob Smith", 11, "S1002")

# Instance methods
print(student1.add_course("Math"))
print(student1.add_course("Science"))
print(student1.add_course("Math"))  # Duplicate
print(student1.get_info())

# Class methods
print(Student.get_total_students())
print(Student.change_school("Java Academy"))
print(student1.school)  # Updated for all instances

# Static method
is_valid, message = Student.validate_grade(10)
print(f"Grade validation: {message}")

# Special methods
print(str(student1))
print(repr(student1))
print(f"Number of courses: {len(student1)}")
```

#### Property Methods
```java
class Temperature:
    """Temperature class with properties"""
    
    def __init__(self, celsius=0):
        """Initialize temperature in Celsius"""
        self._celsius = celsius  # Protected attribute
    
    # Getter property
    @property
    def celsius(self):
        """Get temperature in Celsius"""
        return self._celsius
    
    # Setter property
    @celsius.setter
    def celsius(self, value):
        """Set temperature in Celsius with validation"""
        if value < -273.15:
            raise ValueError("Temperature cannot be below absolute zero!")
        self._celsius = value
    
    # Computed property (read-only)
    @property
    def fahrenheit(self):
        """Get temperature in Fahrenheit"""
        return (self._celsius * 9/5) + 32
    
    @property
    def kelvin(self):
        """Get temperature in Kelvin"""
        return self._celsius + 273.15
    
    # Setter for Fahrenheit (converts to Celsius)
    @fahrenheit.setter
    def fahrenheit(self, value):
        """Set temperature using Fahrenheit"""
        self.celsius = (value - 32) * 5/9
    
    def __str__(self):
        """String representation"""
        return f"{self._celsius:.1f}°C / {self.fahrenheit:.1f}°F / {self.kelvin:.1f}K"

# Test the Temperature class
temp = Temperature(25)
print(temp)
print(f"Celsius: {temp.celsius}")
print(f"Fahrenheit: {temp.fahrenheit}")
print(f"Kelvin: {temp.kelvin}")

# Using setters
temp.celsius = 30
print(f"After setting to 30°C: {temp}")

temp.fahrenheit = 68
print(f"After setting to 68°F: {temp}")

# Try invalid temperature
try:
    temp.celsius = -300
except ValueError as e:
    print(f"Error: {e}")
```

### Day 31: Inheritance and Polymorphism

#### Understanding Inheritance
Inheritance allows a class to inherit attributes and methods from another class.

#### Basic Inheritance
```java
class Animal:
    """Base Animal class"""
    
    def __init__(self, name, age):
        """Initialize animal"""
        self.name = name
        self.age = age
    
    def speak(self):
        """Make animal speak"""
        return f"{self.name} makes a sound"
    
    def eat(self):
        """Animal eating"""
        return f"{self.name} is eating"
    
    def sleep(self):
        """Animal sleeping"""
        return f"{self.name} is sleeping"
    
    def __str__(self):
        """String representation"""
        return f"{self.name} (Age: {self.age})"

# Child class inheriting from Animal
class Dog(Animal):
    """Dog class inheriting from Animal"""
    
    def __init__(self, name, age, breed):
        """Initialize dog with breed"""
        # Call parent constructor
        super().__init__(name, age)
        self.breed = breed
    
    # Override parent method
    def speak(self):
        """Dog barking"""
        return f"{self.name} says: Woof! Woof!"
    
    # New method specific to Dog
    def wag_tail(self):
        """Dog wagging tail"""
        return f"{self.name} is wagging tail happily!"
    
    def fetch(self):
        """Dog fetching"""
        return f"{self.name} is fetching the ball!"
    
    def __str__(self):
        """Enhanced string representation"""
        return f"{self.name} (Age: {self.age}, Breed: {self.breed})"

class Cat(Animal):
    """Cat class inheriting from Animal"""
    
    def __init__(self, name, age, color):
        """Initialize cat with color"""
        super().__init__(name, age)
        self.color = color
    
    # Override parent method
    def speak(self):
        """Cat meowing"""
        return f"{self.name} says: Meow!"
    
    # New methods specific to Cat
    def purr(self):
        """Cat purring"""
        return f"{self.name} is purring contentedly"
    
    def climb(self):
        """Cat climbing"""
        return f"{self.name} is climbing the curtains!"
    
    def __str__(self):
        """Enhanced string representation"""
        return f"{self.name} (Age: {self.age}, Color: {self.color})"

# Test inheritance
dog = Dog("Buddy", 3, "Golden Retriever")
cat = Cat("Whiskers", 2, "Orange")

print(dog)
print(cat)

# Inherited methods
print(dog.eat())
print(cat.sleep())

# Overridden methods
print(dog.speak())
print(cat.speak())

# Class-specific methods
print(dog.wag_tail())
print(cat.purr())
```

#### Multiple Inheritance
```java
class Flyable:
    """Mixin class for flying ability"""
    
    def fly(self):
        """Fly method"""
        return f"{self.name} is flying!"
    
    def take_off(self):
        """Take off method"""
        return f"{self.name} is taking off!"
    
    def land(self):
        """Land method"""
        return f"{self.name} is landing!"

class Swimmable:
    """Mixin class for swimming ability"""
    
    def swim(self):
        """Swim method"""
        return f"{self.name} is swimming!"
    
    def dive(self):
        """Dive method"""
        return f"{self.name} is diving underwater!"

class Duck(Animal, Flyable, Swimmable):
    """Duck class with multiple inheritance"""
    
    def __init__(self, name, age):
        """Initialize duck"""
        super().__init__(name, age)
    
    def speak(self):
        """Duck quacking"""
        return f"{self.name} says: Quack! Quack!"
    
    def __str__(self):
        """String representation"""
        return f"Duck {self.name} (Age: {self.age})"

# Test multiple inheritance
duck = Duck("Donald", 1)
print(duck)
print(duck.speak())
print(duck.fly())
print(duck.swim())
print(duck.eat())
```

#### Polymorphism
```java
class Shape:
    """Base shape class"""
    
    def area(self):
        """Calculate area (to be overridden)"""
        raise NotImplementedError("Subclass must implement area method")
    
    def perimeter(self):
        """Calculate perimeter (to be overridden)"""
        raise NotImplementedError("Subclass must implement perimeter method")
    
    def describe(self):
        """Describe shape"""
        return "This is a shape"

class Rectangle(Shape):
    """Rectangle class"""
    
    def __init__(self, width, height):
        """Initialize rectangle"""
        self.width = width
        self.height = height
    
    def area(self):
        """Calculate rectangle area"""
        return self.width * self.height
    
    def perimeter(self):
        """Calculate rectangle perimeter"""
        return 2 * (self.width + self.height)
    
    def describe(self):
        """Describe rectangle"""
        return f"Rectangle: {self.width} × {self.height}"

class Circle(Shape):
    """Circle class"""
    
    def __init__(self, radius):
        """Initialize circle"""
        self.radius = radius
    
    def area(self):
        """Calculate circle area"""
        import math
        return math.pi * self.radius ** 2
    
    def perimeter(self):
        """Calculate circle circumference"""
        import math
        return 2 * math.pi * self.radius
    
    def describe(self):
        """Describe circle"""
        return f"Circle: radius {self.radius}"

class Triangle(Shape):
    """Triangle class"""
    
    def __init__(self, base, height, side1, side2, side3):
        """Initialize triangle"""
        self.base = base
        self.height = height
        self.side1 = side1
        self.side2 = side2
        self.side3 = side3
    
    def area(self):
        """Calculate triangle area"""
        return 0.5 * self.base * self.height
    
    def perimeter(self):
        """Calculate triangle perimeter"""
        return self.side1 + self.side2 + self.side3
    
    def describe(self):
        """Describe triangle"""
        return f"Triangle: base {self.base}, height {self.height}"

# Polymorphism in action
def analyze_shape(shape):
    """Analyze any shape (polymorphic function)"""
    print(shape.describe())
    print(f"Area: {shape.area():.2f}")
    print(f"Perimeter: {shape.perimeter():.2f}")
    print("-" * 30)

# Test polymorphism
shapes = [
    Rectangle(5, 3),
    Circle(4),
    Triangle(6, 4, 5, 5, 6)
]

for shape in shapes:
    analyze_shape(shape)
```

### Day 32: Encapsulation and Abstraction

#### Understanding Encapsulation
Encapsulation is the bundling of data (attributes) and methods that operate on the data into a single unit (class).

#### Access Modifiers in Java
```java
class Employee:
    """Employee class demonstrating encapsulation"""
    
    def __init__(self, employee_id, name, salary):
        """Initialize employee"""
        # Public attribute
        self.employee_id = employee_id
        self.name = name
        
        # Protected attribute (convention: single underscore)
        self._department = "General"
        
        # Private attribute (convention: double underscore)
        self.__salary = salary
        self.__bonus = 0
    
    # Public methods
    def get_name(self):
        """Get employee name"""
        return self.name
    
    def set_name(self, name):
        """Set employee name"""
        if name.strip():
            self.name = name.strip()
            return "Name updated successfully"
        return "Invalid name"
    
    # Protected method
    def _calculate_bonus(self):
        """Calculate bonus (protected method)"""
        return self.__salary * 0.10
    
    # Private methods
    def __validate_salary(self, salary):
        """Validate salary (private method)"""
        return salary > 0
    
    # Public interface for private data
    def get_salary(self):
        """Get salary (read-only)"""
        return self.__salary
    
    def set_salary(self, new_salary):
        """Set salary with validation"""
        if self.__validate_salary(new_salary):
            self.__salary = new_salary
            return "Salary updated successfully"
        return "Invalid salary amount"
    
    def get_total_compensation(self):
        """Get total compensation including bonus"""
        bonus = self._calculate_bonus()
        return self.__salary + bonus
    
    def promote(self, new_department, salary_increase):
        """Promote employee"""
        self._department = new_department
        new_salary = self.__salary + salary_increase
        if self.set_salary(new_salary) == "Salary updated successfully":
            return f"Promoted to {new_department} with new salary ${new_salary}"
        return "Promotion failed due to invalid salary"
    
    def __str__(self):
        """String representation"""
        return f"Employee {self.name} (ID: {self.employee_id})"

# Test encapsulation
emp = Employee("E1001", "Alice Johnson", 75000)

# Public access
print(emp.get_name())
print(emp.employee_id)

# Protected access (possible but not recommended)
print(emp._department)

# Private access (will cause AttributeError)
try:
    print(emp.__salary)  # This will fail
except AttributeError as e:
    print(f"Error accessing private attribute: {e}")

# Name mangling for private attributes
print(emp._Employee__salary)  # This works but is not recommended

# Use public methods instead
print(f"Salary: ${emp.get_salary()}")
print(f"Total compensation: ${emp.get_total_compensation()}")

# Modify through public methods
print(emp.set_name("Alice Smith"))
print(emp.set_salary(80000))
print(f"New salary: ${emp.get_salary()}")
```

#### Abstraction with Abstract Classes
```java
from abc import ABC, abstractmethod

class Vehicle(ABC):
    """Abstract base class for vehicles"""
    
    def __init__(self, make, model, year):
        """Initialize vehicle"""
        self.make = make
        self.model = model
        self.year = year
        self._is_running = False
    
    @abstractmethod
    def start_engine(self):
        """Start the engine (must be implemented by subclasses)"""
        pass
    
    @abstractmethod
    def stop_engine(self):
        """Stop the engine (must be implemented by subclasses)"""
        pass
    
    @abstractmethod
    def get_fuel_type(self):
        """Get fuel type (must be implemented by subclasses)"""
        pass
    
    # Concrete method (can be used as-is)
    def get_info(self):
        """Get vehicle information"""
        return f"{self.year} {self.make} {self.model}"
    
    def toggle_engine(self):
        """Toggle engine on/off"""
        if self._is_running:
            return self.stop_engine()
        else:
            return self.start_engine()
    
    def __str__(self):
        """String representation"""
        status = "Running" if self._is_running else "Off"
        return f"{self.get_info()} - Engine: {status}"

class Car(Vehicle):
    """Car class implementing abstract methods"""
    
    def __init__(self, make, model, year, fuel_type="gasoline"):
        """Initialize car"""
        super().__init__(make, model, year)
        self.fuel_type = fuel_type
    
    def start_engine(self):
        """Start car engine"""
        if not self._is_running:
            self._is_running = True
            return f"{self.get_info()} engine started with {self.fuel_type}"
        return "Engine is already running"
    
    def stop_engine(self):
        """Stop car engine"""
        if self._is_running:
            self._is_running = False
            return f"{self.get_info()} engine stopped"
        return "Engine is already off"
    
    def get_fuel_type(self):
        """Get car fuel type"""
        return self.fuel_type
    
    def drive(self):
        """Drive the car"""
        if self._is_running:
            return f"{self.get_info()} is driving"
        return "Start the engine first!"

class ElectricCar(Vehicle):
    """Electric car class"""
    
    def __init__(self, make, model, year, battery_capacity):
        """Initialize electric car"""
        super().__init__(make, model, year)
        self.battery_capacity = battery_capacity
        self.battery_level = 100
    
    def start_engine(self):
        """Start electric car"""
        if not self._is_running and self.battery_level > 0:
            self._is_running = True
            return f"{self.get_info()} powered on (Battery: {self.battery_level}%)"
        elif self.battery_level == 0:
            return "Battery is dead! Charge the car!"
        return "Car is already running"
    
    def stop_engine(self):
        """Stop electric car"""
        if self._is_running:
            self._is_running = False
            return f"{self.get_info()} powered off"
        return "Car is already off"
    
    def get_fuel_type(self):
        """Get fuel type"""
        return "electric"
    
    def charge(self, amount):
        """Charge the battery"""
        self.battery_level = min(100, self.battery_level + amount)
        return f"Battery charged to {self.battery_level}%"
    
    def drive(self):
        """Drive the electric car"""
        if self._is_running and self.battery_level > 0:
            self.battery_level -= 5
            return f"{self.get_info()} is driving (Battery: {self.battery_level}%)"
        elif self.battery_level == 0:
            return "Battery is dead! Charge the car!"
        return "Start the car first!"

# Test abstraction
car = Car("Toyota", "Camry", 2022)
tesla = ElectricCar("Tesla", "Model 3", 2023, 75)

print(car)
print(car.start_engine())
print(car.drive())
print(car.stop_engine())

print(tesla)
print(tesla.start_engine())
print(tesla.drive())
print(tesla.charge(20))
print(tesla.drive())

# Cannot instantiate abstract class directly
try:
    vehicle = Vehicle("Generic", "Vehicle", 2023)
except TypeError as e:
    print(f"Error: {e}")
```

### Day 33: Special Methods (Dunder Methods)

#### Common Special Methods
```java
class Book:
    """Book class with special methods"""
    
    def __init__(self, title, author, pages, isbn):
        """Initialize book"""
        self.title = title
        self.author = author
        self.pages = pages
        self.isbn = isbn
        self.current_page = 1
        self.is_open = False
    
    def __str__(self):
        """String representation for users"""
        return f'"{self.title}" by {self.author} ({self.pages} pages)'
    
    def __repr__(self):
        """Official representation for developers"""
        return f"Book('{self.title}', '{self.author}', {self.pages}, '{self.isbn}')"
    
    def __len__(self):
        """Return number of pages"""
        return self.pages
    
    def __bool__(self):
        """Truthiness - book is 'truthy' if it has pages"""
        return self.pages > 0
    
    def __call__(self, page=None):
        """Make book callable - open to specific page"""
        self.is_open = True
        if page:
            if 1 <= page <= self.pages:
                self.current_page = page
                return f"Opened to page {page}"
            else:
                return f"Page {page} doesn't exist. Book has {self.pages} pages"
        return f"Opened to page {self.current_page}"
    
    def __getitem__(self, page):
        """Access pages like indexing"""
        if 1 <= page <= self.pages:
            self.current_page = page
            return f"Page {page} of {self.title}"
        raise IndexError(f"Page {page} not found. Book has {self.pages} pages")
    
    def __contains__(self, item):
        """Check if content is in book (simplified)"""
        return item.lower() in [self.title.lower(), self.author.lower()]
    
    def __eq__(self, other):
        """Equality comparison"""
        if isinstance(other, Book):
            return self.isbn == other.isbn
        return False
    
    def __lt__(self, other):
        """Less than comparison (by pages)"""
        if isinstance(other, Book):
            return self.pages < other.pages
        return NotImplemented
    
    def __le__(self, other):
        """Less than or equal comparison"""
        if isinstance(other, Book):
            return self.pages <= other.pages
        return NotImplemented
    
    def __gt__(self, other):
        """Greater than comparison"""
        if isinstance(other, Book):
            return self.pages > other.pages
        return NotImplemented
    
    def __ge__(self, other):
        """Greater than or equal comparison"""
        if isinstance(other, Book):
            return self.pages >= other.pages
        return NotImplemented
    
    def __add__(self, other):
        """Add two books (combine pages)"""
        if isinstance(other, Book):
            combined_pages = self.pages + other.pages
            combined_title = f"{self.title} + {other.title}"
            combined_author = f"{self.author} & {other.author}"
            return Book(combined_title, combined_author, combined_pages, "COMBINED")
        return NotImplemented
    
    def __enter__(self):
        """Context manager entry"""
        self.is_open = True
        print(f"Started reading {self.title}")
        return self
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        """Context manager exit"""
        self.is_open = False
        print(f"Finished reading {self.title}")
        return False  # Don't suppress exceptions
    
    # Custom methods
    def turn_page(self, pages=1):
        """Turn pages forward"""
        if self.current_page + pages <= self.pages:
            self.current_page += pages
            return f"Turned to page {self.current_page}"
        else:
            return "Already at the last page"
    
    def go_back(self, pages=1):
        """Go back pages"""
        if self.current_page - pages >= 1:
            self.current_page -= pages
            return f"Went back to page {self.current_page}"
        else:
            return "Already at the first page"

# Test special methods
book1 = Book("Java Programming", "John Doe", 450, "1234567890")
book2 = Book("Data Science", "Jane Smith", 380, "0987654321")

# String representations
print(str(book1))          # User-friendly
print(repr(book1))         # Developer-friendly

# Built-in functions
print(len(book1))          # Number of pages
print(bool(book1))         # Truthiness

# Callable
print(book1(50))           # Open to page 50
print(book1())             # Open to current page

# Indexing
print(book1[100])          # Access page 100

# Membership testing
print("Java" in book1)   # True
print("Java" in book1)     # False

# Comparisons
print(book1 > book2)       # More pages
print(book1 < book2)       # False

# Arithmetic
combined = book1 + book2
print(combined)

# Context manager
with book1 as book:
    print(f"Current page: {book.current_page}")
    print(book.turn_page(5))
    print(f"Now on page: {book.current_page}")
```

#### Custom Container Class
```java
class Playlist:
    """Custom playlist class implementing container protocol"""
    
    def __init__(self, name):
        """Initialize playlist"""
        self.name = name
        self.songs = []
    
    def __str__(self):
        """String representation"""
        return f"Playlist '{self.name}' ({len(self.songs)} songs)"
    
    def __len__(self):
        """Number of songs"""
        return len(self.songs)
    
    def __getitem__(self, index):
        """Get song by index"""
        return self.songs[index]
    
    def __setitem__(self, index, song):
        """Set song by index"""
        self.songs[index] = song
    
    def __delitem__(self, index):
        """Remove song by index"""
        del self.songs[index]
    
    def __contains__(self, song):
        """Check if song is in playlist"""
        return song in self.songs
    
    def __iter__(self):
        """Iterate over songs"""
        return iter(self.songs)
    
    def __reversed__(self):
        """Reverse iteration"""
        return reversed(self.songs)
    
    def __add__(self, other):
        """Combine two playlists"""
        if isinstance(other, Playlist):
            combined = Playlist(f"{self.name} + {other.name}")
            combined.songs = self.songs + other.songs
            return combined
        return NotImplemented
    
    def append(self, song):
        """Add song to playlist"""
        self.songs.append(song)
    
    def remove(self, song):
        """Remove song from playlist"""
        if song in self.songs:
            self.songs.remove(song)
            return True
        return False
    
    def shuffle(self):
        """Shuffle playlist"""
        import random
        random.shuffle(self.songs)
    
    def clear(self):
        """Clear playlist"""
        self.songs.clear()

# Test custom container
playlist = Playlist("My Favorites")
playlist.append("Song 1")
playlist.append("Song 2")
playlist.append("Song 3")

print(playlist)
print(f"Length: {len(playlist)}")
print(f"First song: {playlist[0]}")
print(f"Contains Song 2: {'Song 2' in playlist}")

# Iteration
print("All songs:")
for song in playlist:
    print(f"  {song}")

# Modify
playlist[1] = "New Song 2"
print(f"Modified: {playlist[1]}")

# Delete
del playlist[2]
print(f"After deletion: {len(playlist)} songs")
```

### Day 34: OOP Design Principles

#### SOLID Principles Overview
```java
# S - Single Responsibility Principle
class User:
    """User class - only handles user data"""
    
    def __init__(self, username, email):
        """Initialize user"""
        self.username = username
        self.email = email
    
    def update_email(self, new_email):
        """Update user email"""
        self.email = new_email
    
    def __str__(self):
        """String representation"""
        return f"User: {self.username} ({self.email})"

class UserRepository:
    """Repository for user data persistence"""
    
    def __init__(self):
        """Initialize repository"""
        self.users = {}
    
    def save(self, user):
        """Save user to repository"""
        self.users[user.username] = user
        return f"User {user.username} saved"
    
    def find_by_username(self, username):
        """Find user by username"""
        return self.users.get(username)
    
    def delete(self, username):
        """Delete user from repository"""
        if username in self.users:
            del self.users[username]
            return f"User {username} deleted"
        return f"User {username} not found"

class EmailService:
    """Service for sending emails"""
    
    @staticmethod
    def send_welcome_email(user):
        """Send welcome email to user"""
        return f"Welcome email sent to {user.email}"
    
    @staticmethod
    def send_password_reset(user):
        """Send password reset email"""
        return f"Password reset email sent to {user.email}"

# O - Open/Closed Principle
class ShapeCalculator:
    """Calculator for shapes - open for extension, closed for modification"""
    
    @staticmethod
    def calculate_area(shape):
        """Calculate area for any shape"""
        return shape.area()
    
    @staticmethod
    def calculate_perimeter(shape):
        """Calculate perimeter for any shape"""
        return shape.perimeter()

# Can add new shapes without modifying the calculator
class Rectangle:
    def __init__(self, width, height):
        self.width = width
        self.height = height
    
    def area(self):
        return self.width * self.height
    
    def perimeter(self):
        return 2 * (self.width + self.height)

class Circle:
    def __init__(self, radius):
        self.radius = radius
    
    def area(self):
        import math
        return math.pi * self.radius ** 2
    
    def perimeter(self):
        import math
        return 2 * math.pi * self.radius

# L - Liskov Substitution Principle
class Bird:
    """Base bird class"""
    
    def fly(self):
        """Bird flying"""
        raise NotImplementedError("Subclass must implement fly method")
    
    def make_sound(self):
        """Bird making sound"""
        return "Tweet tweet"

class Sparrow(Bird):
    """Sparrow can fly"""
    
    def fly(self):
        return "Sparrow is flying high!"
    
    def make_sound(self):
        return "Chirp chirp"

class Penguin(Bird):
    """Penguin cannot fly - violates LSP if used as Bird"""
    
    def fly(self):
        # Penguins can't fly, but we shouldn't change the interface
        return "Penguin waddles instead of flying"
    
    def make_sound(self):
        return "Squawk squawk"

# Better approach - use separate interfaces
class FlyingBird(Bird):
    """Bird that can fly"""
    
    def fly(self):
        pass

class FlightlessBird(Bird):
    """Bird that cannot fly"""
    
    def walk(self):
        pass

# I - Interface Segregation Principle
class Printer:
    """Basic printer interface"""
    
    def print(self, document):
        """Print document"""
        pass

class Scanner:
    """Scanner interface"""
    
    def scan(self):
        """Scan document"""
        pass

class FaxMachine:
    """Fax machine interface"""
    
    def fax(self, document):
        """Fax document"""
        pass

class BasicPrinter(Printer):
    """Simple printer - only prints"""
    
    def print(self, document):
        return f"Printing: {document}"

class MultiFunctionDevice(Printer, Scanner, FaxMachine):
    """Multi-function device - implements all interfaces"""
    
    def print(self, document):
        return f"Printing: {document}"
    
    def scan(self):
        return "Scanning document"
    
    def fax(self, document):
        return f"Faxing: {document}"

# D - Dependency Inversion Principle
class DatabaseConnection:
    """Database connection interface"""
    
    def connect(self):
        """Connect to database"""
        pass
    
    def execute_query(self, query):
        """Execute query"""
        pass

class MySQLConnection(DatabaseConnection):
    """MySQL connection implementation"""
    
    def connect(self):
        return "Connected to MySQL database"
    
    def execute_query(self, query):
        return f"Executing query in MySQL: {query}"

class PostgreSQLConnection(DatabaseConnection):
    """PostgreSQL connection implementation"""
    
    def connect(self):
        return "Connected to PostgreSQL database"
    
    def execute_query(self, query):
        return f"Executing query in PostgreSQL: {query}"

class UserService:
    """Service that depends on abstraction, not concrete implementation"""
    
    def __init__(self, db_connection: DatabaseConnection):
        """Initialize with database connection"""
        self.db = db_connection
    
    def get_user(self, user_id):
        """Get user from database"""
        self.db.connect()
        query = f"SELECT * FROM users WHERE id = {user_id}"
        return self.db.execute_query(query)

# Test dependency inversion
mysql_service = UserService(MySQLConnection())
postgresql_service = UserService(PostgreSQLConnection())

print(mysql_service.get_user(1))
print(postgresql_service.get_user(1))
```

### Day 35: Practice Exercises

#### Exercise 1: Banking System with OOP
```java
class Account:
    """Base account class"""
    
    def __init__(self, account_number, owner, balance=0):
        """Initialize account"""
        self.account_number = account_number
        self.owner = owner
        self.balance = balance
        self.transactions = []
    
    def deposit(self, amount):
        """Deposit money"""
        if amount > 0:
            self.balance += amount
            self.transactions.append(f"Deposit: +${amount:.2f}")
            return f"Deposited ${amount:.2f}"
        return "Invalid deposit amount"
    
    def withdraw(self, amount):
        """Withdraw money"""
        if amount > 0 and amount <= self.balance:
            self.balance -= amount
            self.transactions.append(f"Withdrawal: -${amount:.2f}")
            return f"Withdrew ${amount:.2f}"
        return "Invalid withdrawal amount"
    
    def get_balance(self):
        """Get current balance"""
        return self.balance
    
    def get_statement(self):
        """Get account statement"""
        statement = f"Account {self.account_number} - {self.owner}\n"
        statement += f"Current Balance: ${self.balance:.2f}\n"
        statement += "Transactions:\n"
        for transaction in self.transactions:
            statement += f"  {transaction}\n"
        return statement

class CheckingAccount(Account):
    """Checking account with overdraft protection"""
    
    def __init__(self, account_number, owner, balance=0, overdraft_limit=500):
        """Initialize checking account"""
        super().__init__(account_number, owner, balance)
        self.overdraft_limit = overdraft_limit
    
    def withdraw(self, amount):
        """Withdraw with overdraft protection"""
        if amount > 0 and (self.balance + self.overdraft_limit) >= amount:
            self.balance -= amount
            self.transactions.append(f"Withdrawal: -${amount:.2f}")
            return f"Withdrew ${amount:.2f}"
        return "Insufficient funds (including overdraft)"

class SavingsAccount(Account):
    """Savings account with interest"""
    
    def __init__(self, account_number, owner, balance=0, interest_rate=0.02):
        """Initialize savings account"""
        super().__init__(account_number, owner, balance)
        self.interest_rate = interest_rate
    
    def apply_interest(self):
        """Apply monthly interest"""
        interest = self.balance * self.interest_rate
        self.balance += interest
        self.transactions.append(f"Interest: +${interest:.2f}")
        return f"Interest applied: ${interest:.2f}"

# Test the banking system
checking = CheckingAccount("C001", "Alice Smith", 1000)
savings = SavingsAccount("S001", "Bob Jones", 5000, 0.03)

print(checking.deposit(500))
print(checking.withdraw(1200))  # Uses overdraft
print(checking.get_statement())

print(savings.apply_interest())
print(savings.withdraw(1000))
print(savings.get_statement())
```

### Day 36: OOP Project - Bank Account System

#### Complete Bank Account System
```java
import datetime
from enum import Enum

class AccountType(Enum):
    """Account types enumeration"""
    CHECKING = "Checking"
    SAVINGS = "Savings"
    BUSINESS = "Business"

class TransactionType(Enum):
    """Transaction types enumeration"""
    DEPOSIT = "Deposit"
    WITHDRAWAL = "Withdrawal"
    TRANSFER = "Transfer"
    INTEREST = "Interest"
    FEE = "Fee"

class Transaction:
    """Transaction class"""
    
    def __init__(self, transaction_type, amount, description=""):
        """Initialize transaction"""
        self.transaction_type = transaction_type
        self.amount = amount
        self.description = description
        self.timestamp = datetime.datetime.now()
    
    def __str__(self):
        """String representation"""
        date_str = self.timestamp.strftime("%Y-%m-%d %H:%M:%S")
        return f"{date_str} - {self.transaction_type.value}: ${self.amount:.2f} - {self.description}"

class Account:
    """Base account class"""
    
    def __init__(self, account_number, owner, account_type, initial_balance=0):
        """Initialize account"""
        self.account_number = account_number
        self.owner = owner
        self.account_type = account_type
        self.balance = initial_balance
        self.transactions = []
        self.is_active = True
        self.created_date = datetime.datetime.now()
    
    def deposit(self, amount, description=""):
        """Deposit money to account"""
        if not self.is_active:
            return "Account is not active"
        
        if amount <= 0:
            return "Deposit amount must be positive"
        
        self.balance += amount
        transaction = Transaction(TransactionType.DEPOSIT, amount, description or "Deposit")
        self.transactions.append(transaction)
        return f"Deposited ${amount:.2f}"
    
    def withdraw(self, amount, description=""):
        """Withdraw money from account"""
        if not self.is_active:
            return "Account is not active"
        
        if amount <= 0:
            return "Withdrawal amount must be positive"
        
        if amount > self.balance:
            return "Insufficient funds"
        
        self.balance -= amount
        transaction = Transaction(TransactionType.WITHDRAWAL, amount, description or "Withdrawal")
        self.transactions.append(transaction)
        return f"Withdrew ${amount:.2f}"
    
    def transfer(self, target_account, amount, description=""):
        """Transfer money to another account"""
        if not self.is_active or not target_account.is_active:
            return "One or both accounts are not active"
        
        if amount <= 0:
            return "Transfer amount must be positive"
        
        if amount > self.balance:
            return "Insufficient funds for transfer"
        
        # Withdraw from this account
        self.balance -= amount
        transaction = Transaction(TransactionType.TRANSFER, amount, 
                                 f"Transfer to {target_account.account_number}")
        self.transactions.append(transaction)
        
        # Deposit to target account
        target_account.balance += amount
        target_transaction = Transaction(TransactionType.TRANSFER, amount,
                                       f"Transfer from {self.account_number}")
        target_account.transactions.append(target_transaction)
        
        return f"Transferred ${amount:.2f} to account {target_account.account_number}"
    
    def get_balance(self):
        """Get current balance"""
        return self.balance
    
    def get_statement(self):
        """Get account statement"""
        statement = f"{'='*50}\n"
        statement += f"ACCOUNT STATEMENT\n"
        statement += f"{'='*50}\n"
        statement += f"Account Number: {self.account_number}\n"
        statement += f"Account Type: {self.account_type.value}\n"
        statement += f"Owner: {self.owner}\n"
        statement += f"Status: {'Active' if self.is_active else 'Inactive'}\n"
        statement += f"Created: {self.created_date.strftime('%Y-%m-%d')}\n"
        statement += f"Current Balance: ${self.balance:.2f}\n"
        statement += f"\nTRANSACTIONS ({len(self.transactions)} total):\n"
        statement += f"{'-'*50}\n"
        
        for transaction in self.transactions[-10:]:  # Show last 10 transactions
            statement += f"{transaction}\n"
        
        if len(self.transactions) > 10:
            statement += f"... and {len(self.transactions) - 10} more transactions\n"
        
        statement += f"{'='*50}\n"
        return statement
    
    def close_account(self):
        """Close account"""
        if self.balance > 0:
            return "Account must have zero balance to close"
        
        self.is_active = False
        return f"Account {self.account_number} closed"
    
    def __str__(self):
        """String representation"""
        status = "Active" if self.is_active else "Inactive"
        return f"{self.account_type.value} Account {self.account_number}: {self.owner} - ${self.balance:.2f} ({status})"

class CheckingAccount(Account):
    """Checking account with overdraft protection"""
    
    def __init__(self, account_number, owner, initial_balance=0, overdraft_limit=500):
        """Initialize checking account"""
        super().__init__(account_number, owner, AccountType.CHECKING, initial_balance)
        self.overdraft_limit = overdraft_limit
        self.monthly_fee = 10.00
    
    def withdraw(self, amount, description=""):
        """Withdraw with overdraft protection"""
        if not self.is_active:
            return "Account is not active"
        
        if amount <= 0:
            return "Withdrawal amount must be positive"
        
        available_funds = self.balance + self.overdraft_limit
        if amount > available_funds:
            return f"Insufficient funds. Available: ${available_funds:.2f}"
        
        self.balance -= amount
        transaction = Transaction(TransactionType.WITHDRAWAL, amount, description or "Withdrawal")
        self.transactions.append(transaction)
        
        # Check if we're in overdraft
        if self.balance < 0:
            overdraft_fee = 5.00
            self.balance -= overdraft_fee
            fee_transaction = Transaction(TransactionType.FEE, overdraft_fee, "Overdraft fee")
            self.transactions.append(fee_transaction)
        
        return f"Withdrew ${amount:.2f}"
    
    def apply_monthly_fee(self):
        """Apply monthly maintenance fee"""
        if self.is_active:
            self.balance -= self.monthly_fee
            transaction = Transaction(TransactionType.FEE, self.monthly_fee, "Monthly maintenance fee")
            self.transactions.append(transaction)
            return f"Monthly fee of ${self.monthly_fee:.2f} applied"
        return "Account is not active"

class SavingsAccount(Account):
    """Savings account with interest"""
    
    def __init__(self, account_number, owner, initial_balance=0, interest_rate=0.02):
        """Initialize savings account"""
        super().__init__(account_number, owner, AccountType.SAVINGS, initial_balance)
        self.interest_rate = interest_rate
        self.minimum_balance = 100
    
    def withdraw(self, amount, description=""):
        """Withdraw with minimum balance check"""
        if not self.is_active:
            return "Account is not active"
        
        if amount <= 0:
            return "Withdrawal amount must be positive"
        
        if (self.balance - amount) < self.minimum_balance:
            return f"Withdrawal would leave balance below minimum of ${self.minimum_balance:.2f}"
        
        self.balance -= amount
        transaction = Transaction(TransactionType.WITHDRAWAL, amount, description or "Withdrawal")
        self.transactions.append(transaction)
        return f"Withdrew ${amount:.2f}"
    
    def apply_monthly_interest(self):
        """Apply monthly interest"""
        if self.is_active and self.balance > 0:
            interest = self.balance * (self.interest_rate / 12)  # Monthly interest
            self.balance += interest
            transaction = Transaction(TransactionType.INTEREST, interest, "Monthly interest")
            self.transactions.append(transaction)
            return f"Interest of ${interest:.2f} applied"
        return "No interest applied"

class Bank:
    """Bank class to manage accounts"""
    
    def __init__(self, name):
        """Initialize bank"""
        self.name = name
        self.accounts = {}
        self.next_account_number = 1001
    
    def create_account(self, owner, account_type, initial_balance=0, **kwargs):
        """Create new account"""
        account_number = f"{self.next_account_number:04d}"
        self.next_account_number += 1
        
        if account_type == AccountType.CHECKING:
            overdraft_limit = kwargs.get('overdraft_limit', 500)
            account = CheckingAccount(account_number, owner, initial_balance, overdraft_limit)
        elif account_type == AccountType.SAVINGS:
            interest_rate = kwargs.get('interest_rate', 0.02)
            account = SavingsAccount(account_number, owner, initial_balance, interest_rate)
        else:
            account = Account(account_number, owner, account_type, initial_balance)
        
        self.accounts[account_number] = account
        return account
    
    def get_account(self, account_number):
        """Get account by number"""
        return self.accounts.get(account_number)
    
    def list_accounts(self):
        """List all accounts"""
        return list(self.accounts.values())
    
    def get_total_deposits(self):
        """Get total deposits across all accounts"""
        return sum(account.get_balance() for account in self.accounts.values())
    
    def apply_monthly_processes(self):
        """Apply monthly processes to all accounts"""
        results = []
        for account in self.accounts.values():
            if isinstance(account, CheckingAccount):
                results.append(account.apply_monthly_fee())
            elif isinstance(account, SavingsAccount):
                results.append(account.apply_monthly_interest())
        return results

# Test the complete banking system
def main():
    """Main function to test the banking system"""
    
    print("=" * 60)
    print("    BANKING SYSTEM DEMO")
    print("=" * 60)
    
    # Create bank
    bank = Bank("Java Bank")
    
    # Create accounts
    print("\nCreating accounts...")
    alice_checking = bank.create_account("Alice Smith", AccountType.CHECKING, 1000, overdraft_limit=1000)
    bob_savings = bank.create_account("Bob Jones", AccountType.SAVINGS, 5000, interest_rate=0.03)
    
    print(f"Created: {alice_checking}")
    print(f"Created: {bob_savings}")
    
    # Perform transactions
    print("\nPerforming transactions...")
    print(alice_checking.deposit(500, "Paycheck deposit"))
    print(alice_checking.withdraw(200, "Grocery shopping"))
    print(alice_checking.withdraw(1500, "Large purchase (uses overdraft)"))
    
    print(bob_savings.deposit(1000, "Bonus"))
    print(bob_savings.withdraw(500, "Vacation"))
    
    # Transfer between accounts
    print("\nTransferring money...")
    print(alice_checking.transfer(bob_savings, 300, "Transfer to savings"))
    
    # Apply monthly processes
    print("\nApplying monthly processes...")
    monthly_results = bank.apply_monthly_processes()
    for result in monthly_results:
        if result:  # Skip empty results
            print(result)
    
    # Display statements
    print("\n" + "=" * 60)
    print("ACCOUNT STATEMENTS")
    print("=" * 60)
    
    for account in bank.list_accounts():
        print(account.get_statement())
        print()
    
    # Bank summary
    print("=" * 60)
    print("BANK SUMMARY")
    print("=" * 60)
    print(f"Bank: {bank.name}")
    print(f"Total Accounts: {len(bank.list_accounts())}")
    print(f"Total Deposits: ${bank.get_total_deposits():.2f}")

if __name__ == "__main__":
    main()
```

## 🎉 Level 2 Weeks 7-8 Completion

### ✅ What You've Mastered
- [ ] **Classes and Objects**: Creating and using classes
- [ ] **Methods and Attributes**: Instance, class, and static methods
- [ ] **Inheritance**: Single and multiple inheritance
- [ ] **Polymorphism**: Method overriding and duck typing
- [ ] **Encapsulation**: Public, protected, and private members
- [ ] **Abstraction**: Abstract classes and interfaces
- [ ] **Special Methods**: Dunder methods for custom behavior
- [ ] **OOP Design**: SOLID principles and best practices
- [ ] **Complex Project**: Complete banking system

### 🚀 Ready for Weeks 9-10?
Continue with Level 2: Advanced Data Structures and Modules!

---

**Next:** Continue with Weeks 9-10: Advanced Data Structures and Modules
