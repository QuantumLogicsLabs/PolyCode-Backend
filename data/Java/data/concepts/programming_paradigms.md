# Programming Paradigms - Complete Guide

This guide covers major programming paradigms with Java implementations, comparisons, and use cases.

## 📚 Table of Contents

1. [Introduction to Paradigms](#introduction-to-paradigms)
2. [Procedural Programming](#procedural-programming)
3. [Object-Oriented Programming](#object-oriented-programming)
4. [Functional Programming](#functional-programming)
5. [Logic Programming](#logic-programming)
6. [Event-Driven Programming](#event-driven-programming)
7. [Aspect-Oriented Programming](#aspect-oriented-programming)
8. [Paradigm Comparison](#paradigm-comparison)

---

## Introduction to Paradigms

### What are Programming Paradigms?
Programming paradigms are fundamental styles or approaches to programming and problem-solving.

### Why Learn Multiple Paradigms?
- **Problem-Solving Skills**: Different approaches for different problems
- **Flexibility**: Choose right tool for the job
- **Code Quality**: Write cleaner, more maintainable code
- **Career Advancement**: Understand different architectural approaches
- **Language Features**: Utilize full power of programming languages

### Paradigm Classification
- **Imperative**: How to accomplish tasks step by step
- **Declarative**: What the result should look like
- **Structured**: Organized code with clear separation
- **Unstructured**: Flexible but potentially chaotic code

---

## Procedural Programming

### Core Concepts
Procedural programming is a programming paradigm based on the concept of procedure calls.

#### Key Principles
- **Procedures/Functions**: Reusable blocks of code
- **Sequential Execution**: Step-by-step program flow
- **Global State**: Shared data accessible to all procedures
- **Modularity**: Breaking code into logical units

#### Java Implementation
```java
# Procedural approach to student management
students = []
courses = []
grades = {}

def add_student(name, age, student_id):
    """Add student to the system"""
    student = {
        'id': student_id,
        'name': name,
        'age': age,
        'courses': []
    }
    students.append(student)
    print(f"Added student: {name}")

def enroll_student(student_id, course_code):
    """Enroll student in a course"""
    for student in students:
        if student['id'] == student_id:
            student['courses'].append(course_code)
            if course_code not in courses:
                courses[course_code] = []
            courses[course_code].append(student_id)
            print(f"Enrolled {student['name']} in {course_code}")
            break

def assign_grade(student_id, course_code, grade):
    """Assign grade to student"""
    if course_code in grades:
        if student_id not in grades[course_code]:
            grades[course_code][student_id] = {}
        grades[course_code][student_id]['grade'] = grade
        print(f"Assigned grade {grade} to student {student_id} in {course_code}")

def generate_transcript(student_id):
    """Generate student transcript"""
    transcript = []
    for student in students:
        if student['id'] == student_id:
            for course_code in student['courses']:
                if course_code in grades and student_id in grades[course_code]:
                    grade = grades[course_code][student_id]['grade']
                    transcript.append(f"Course {course_code}: Grade {grade}")
            break
    
    return '\n'.join(transcript)

# Example usage
add_student("Alice", 20, "S001")
add_student("Bob", 21, "S002")
add_student("Charlie", 19, "S003")

enroll_student("S001", "CS101")
enroll_student("S002", "CS101")
enroll_student("S003", "CS101")

assign_grade("S001", "CS101", "A")
assign_grade("S002", "CS101", "B")
assign_grade("S003", "CS101", "A-")

print("\nAlice's Transcript:")
print(generate_transcript("S001"))
```

#### Characteristics
- **Top-Down Design**: Procedures call other procedures
- **Global Data**: Shared state accessible to all functions
- **Sequential Flow**: Clear execution path
- **Side Effects**: Functions can modify global state

#### Advantages
- **Simple to Understand**: Straightforward execution flow
- **Efficient**: Direct access to shared data
- **Fast Development**: Quick to implement for simple problems
- **Memory Efficient**: Less overhead than OOP for simple cases

#### Disadvantages
- **Global State Issues**: Hard to track data modifications
- **Tight Coupling**: Procedures depend on global data
- **Difficult to Maintain**: Changes affect multiple procedures
- **Testing Challenges**: Hard to isolate and test individual procedures

---

## Object-Oriented Programming

### Core Concepts
Object-oriented programming organizes software as collection of objects that interact with each other.

#### Key Principles
- **Encapsulation**: Bundle data and methods together
- **Inheritance**: Create new classes from existing ones
- **Polymorphism**: Different objects respond to same interface
- **Abstraction**: Hide implementation details, show only essentials

#### Java Implementation
```java
class Student:
    """Student class implementing OOP principles"""
    
    def __init__(self, name, age, student_id):
        self.id = student_id
        self.name = name
        self.age = age
        self.courses = []
        self.grades = {}
    
    def enroll(self, course):
        """Enroll student in a course"""
        self.courses.append(course)
        course.add_student(self)
        print(f"{self.name} enrolled in {course.code}")
    
    def assign_grade(self, course, grade):
        """Assign grade for a course"""
        self.grades[course.code] = grade
        print(f"Grade {grade} assigned to {self.name} for {course.code}")
    
    def get_transcript(self):
        """Generate student transcript"""
        transcript = []
        for course in self.courses:
            if course.code in self.grades:
                transcript.append(f"{course.code}: {self.grades[course.code]}")
        return '\n'.join(transcript)
    
    def get_gpa(self):
        """Calculate student GPA"""
        if not self.grades:
            return 0.0
        
        grade_points = {'A': 4.0, 'B': 3.0, 'C': 2.0, 'D': 1.0, 'F': 0.0}
        total_points = 0
        total_courses = 0
        
        for grade in self.grades.values():
            if grade in grade_points:
                total_points += grade_points[grade]
                total_courses += 1
        
        return total_points / total_courses if total_courses > 0 else 0.0

class Course:
    """Course class representing academic courses"""
    
    def __init__(self, code, name, credits):
        self.code = code
        self.name = name
        self.credits = credits
        self.students = []
    
    def add_student(self, student):
        """Add student to course"""
        self.students.append(student)
        print(f"{student.name} added to {self.code}")
    
    def get_class_list(self):
        """Get list of enrolled students"""
        return [student.name for student in self.students]

class University:
    """University class managing courses and students"""
    
    def __init__(self, name):
        self.name = name
        self.courses = {}
        self.students = {}
    
    def add_course(self, course):
        """Add course to university"""
        self.courses[course.code] = course
        print(f"Course {course.code} added to {self.name}")
    
    def add_student(self, student):
        """Add student to university"""
        self.students[student.id] = student
        print(f"Student {student.name} added to {self.name}")
    
    def get_course(self, code):
        """Get course by code"""
        return self.courses.get(code)
    
    def get_student(self, student_id):
        """Get student by ID"""
        return self.students.get(student_id)

# Example usage
university = University("Java University")

# Add courses
cs101 = Course("CS101", "Introduction to Computer Science", 3)
cs102 = Course("CS102", "Data Structures", 4)
math101 = Course("MATH101", "Calculus I", 4)

university.add_course(cs101)
university.add_course(cs102)
university.add_course(math101)

# Add students
alice = Student("Alice", 20, "S001")
bob = Student("Bob", 21, "S002")
charlie = Student("Charlie", 19, "S003")

university.add_student(alice)
university.add_student(bob)
university.add_student(charlie)

# Enroll students in courses
alice.enroll(cs101)
bob.enroll(cs101)
charlie.enroll(cs101)

alice.enroll(math101)
bob.enroll(cs102)

# Assign grades
alice.assign_grade(cs101, 'A')
alice.assign_grade(math101, 'B')
bob.assign_grade(cs101, 'B')
bob.assign_grade(cs102, 'A')

# Generate reports
print(f"\nAlice's Transcript:")
print(alice.get_transcript())
print(f"Alice's GPA: {alice.get_gpa():.2f}")

print(f"\nCS101 Class List:")
print(cs101.get_class_list())
```

#### Characteristics
- **Encapsulation**: Data and methods bundled in objects
- **Inheritance**: Classes can extend existing functionality
- **Polymorphism**: Objects of different classes treated uniformly
- **Abstraction**: Hide implementation details

#### Advantages
- **Modularity**: Clear separation of concerns
- **Reusability**: Classes can be reused across projects
- **Maintainability**: Changes localized to specific classes
- **Scalability**: Easy to add new features
- **Testing**: Individual classes can be unit tested

#### Disadvantages
- **Complexity**: More complex than procedural for simple problems
- **Overhead**: Object creation and method call overhead
- **Learning Curve**: Steeper learning curve for beginners
- **Memory Usage**: More memory for object instances

---

## Functional Programming

### Core Concepts
Functional programming treats computation as evaluation of mathematical functions, avoiding state and mutable data.

#### Key Principles
- **Pure Functions**: No side effects, same input always produces same output
- **Immutability**: Data structures cannot be modified after creation
- **First-Class Functions**: Functions treated as values
- **Higher-Order Functions**: Functions that take or return other functions
- **Recursion**: Problem-solving through self-referential functions

#### Java Implementation
```java
from functools import reduce
from operator import add
from typing import List, Callable, Any

# Pure functions
def square(x: int) -> int:
    """Pure function - no side effects"""
    return x * x

def is_even(x: int) -> bool:
    """Pure predicate function"""
    return x % 2 == 0

# Higher-order functions
def apply_operation(func: Callable[[int], int], numbers: List[int]) -> List[int]:
    """Higher-order function - takes function as parameter"""
    return [func(num) for num in numbers]

def filter_numbers(predicate: Callable[[int], bool], numbers: List[int]) -> List[int]:
    """Higher-order function for filtering"""
    return [num for num in numbers if predicate(num)]

# Function composition
def compose(f: Callable, g: Callable) -> Callable:
    """Compose two functions"""
    return lambda x: f(g(x))

# Recursive functions
def factorial(n: int) -> int:
    """Pure recursive function"""
    if n <= 1:
        return 1
    return n * factorial(n - 1)

def fibonacci(n: int) -> int:
    """Tail-recursive Fibonacci"""
    def fib_helper(n, a, b):
        if n == 0:
            return a
        return fib_helper(n - 1, b, a + b)
    
    return fib_helper(n, 0, 1)

# Immutable operations
def update_grade_record(record: tuple, new_grade: str) -> tuple:
    """Return new record instead of modifying existing"""
    student_id, name, old_grades = record
    updated_grades = old_grades + (new_grade,)
    return (student_id, name, updated_grades)

# Functional data processing
def process_students_data(students_data: List[tuple]) -> dict:
    """Process student data using functional approach"""
    
    # Map: Extract names
    names = list(map(lambda student: student[1], students_data))
    
    # Filter: Get students over 18
    adult_students = list(filter(lambda student: student[2] > 18, students_data))
    
    # Reduce: Calculate total age
    total_age = reduce(add, map(lambda student: student[2], students_data), 0)
    
    # Group: Create age-based groups
    age_groups = {}
    for student in students_data:
        age_group = "Adult" if student[2] > 18 else "Minor"
        if age_group not in age_groups:
            age_groups[age_group] = []
        age_groups[age_group].append(student[1])
    
    return {
        'all_names': names,
        'adult_students': len(adult_students),
        'total_age': total_age,
        'age_groups': age_groups
    }

# Example usage
students_data = [
    (1, "Alice", 20),
    (2, "Bob", 21),
    (3, "Charlie", 19),
    (4, "Diana", 22)
]

# Functional operations
numbers = [1, 2, 3, 4, 5]
squared = apply_operation(square, numbers)
evens = filter_numbers(is_even, numbers)
total = reduce(add, numbers, 0)

print(f"Squared numbers: {squared}")
print(f"Even numbers: {evens}")
print(f"Sum: {total}")

# Student data processing
results = process_students_data(students_data)
print(f"All student names: {results['all_names']}")
print(f"Adult students: {results['adult_students']}")
print(f"Total age: {results['total_age']}")
print(f"Age groups: {results['age_groups']}")

# Function composition
add_one = lambda x: x + 1
multiply_by_two = lambda x: x * 2
combined = compose(multiply_by_two, add_one)

print(f"Combined function (5): {combined(5)}")  # (5 + 1) * 2 = 12
```

#### Characteristics
- **Pure Functions**: No side effects, predictable behavior
- **Immutability**: Data structures don't change after creation
- **Higher-Order Functions**: Functions as parameters and return values
- **Function Composition**: Building complex functions from simple ones
- **Lazy Evaluation**: Computation delayed until needed

#### Advantages
- **Predictability**: Same input always produces same output
- **Testability**: Easy to test pure functions
- **Concurrency**: Safe for parallel execution
- **Reasoning**: Mathematical reasoning about code behavior
- **Composability**: Build complex functions from simple ones

#### Disadvantages
- **Learning Curve**: Can be difficult for imperative programmers
- **Performance**: Sometimes slower due to immutability
- **Memory Usage**: Creating new objects instead of modifying existing
- **Integration**: Can be challenging with imperative code

---

## Logic Programming

### Core Concepts
Logic programming expresses programs as a set of logical statements and rules.

#### Key Principles
- **Declarative**: Describe what, not how
- **Relations**: Define relationships between entities
- **Rules**: Logical rules for inference
- **Backtracking**: Systematic search for solutions
- **Unification**: Pattern matching and variable binding

#### Java Implementation
```java
class LogicProgram:
    """Simple logic programming system"""
    
    def __init__(self):
        self.facts = set()
        self.rules = []
    
    def add_fact(self, fact):
        """Add a fact to the knowledge base"""
        self.facts.add(fact)
        print(f"Added fact: {fact}")
    
    def add_rule(self, conditions, conclusion):
        """Add a rule to the knowledge base"""
        rule = {'conditions': conditions, 'conclusion': conclusion}
        self.rules.append(rule)
        print(f"Added rule: {conditions} -> {conclusion}")
    
    def query(self, goal):
        """Query the knowledge base"""
        solutions = []
        
        # Direct fact check
        if goal in self.facts:
            solutions.append(f"Direct fact: {goal}")
            return solutions
        
        # Rule-based inference
        for rule in self.rules:
            if self._match_conditions(rule['conditions'], goal):
                # Try to prove conditions
                if self._prove_conditions(rule['conditions']):
                    solutions.append(f"By rule: {rule['conditions']} -> {rule['conclusion']}")
                    # Recursive query with new goal
                    if goal == rule['conclusion']:
                        return solutions
                    else:
                        sub_solutions = self.query(rule['conclusion'])
                        solutions.extend(sub_solutions)
        
        return solutions
    
    def _match_conditions(self, conditions, goal):
        """Check if conditions can lead to goal"""
        # Simple matching - in real logic programming, this would be unification
        return any(cond == goal for cond in conditions)
    
    def _prove_conditions(self, conditions):
        """Try to prove all conditions are true"""
        return all(cond in self.facts for cond in conditions)

# Example usage - Family relationships
lp = LogicProgram()

# Add facts
lp.add_fact("parent(alice, bob)")
lp.add_fact("parent(bob, charlie)")
lp.add_fact("parent(charlie, diana)")
lp.add_fact("male(alice)")
lp.add_fact("male(bob)")
lp.add_fact("male(charlie)")

# Add rules
lp.add_rule(["parent(X, Y)", "grandparent(X, Y)"])
lp.add_rule(["parent(X, Y)", "ancestor(X, Y)"])
lp.add_rule(["male(X)", "father(X)"])

# Query the system
print("\nQuerying grandparent(alice, diana):")
solutions = lp.query("grandparent(alice, diana)")
for solution in solutions:
    print(f"  {solution}")

print("\nQuerying father(bob):")
solutions = lp.query("father(bob)")
for solution in solutions:
    print(f"  {solution}")
```

#### Characteristics
- **Declarative**: Describe what the solution looks like
- **Relational**: Focus on relationships between entities
- **Rule-Based**: Knowledge expressed as rules
- **Backtracking**: Systematic search for solutions
- **Inference**: Derive new facts from existing ones

#### Advantages
- **Expressiveness**: Natural way to express certain problems
- **Modifiability**: Easy to add new rules and facts
- **Correctness**: Mathematical foundation ensures correctness
- **Optimization**: Built-in search and optimization
- **Parallelism**: Natural for parallel execution

#### Disadvantages
- **Performance**: Can be slow for large problems
- **Complexity**: Hard to understand and debug
- **Limited Scope**: Not suitable for all problems
- **Integration**: Challenging with imperative code
- **Tooling**: Less mature tool support

---

## Event-Driven Programming

### Core Concepts
Event-driven programming responds to events or changes in state rather than following a predetermined sequence of operations.

#### Key Principles
- **Events**: Signals that something happened
- **Event Handlers**: Functions that respond to events
- **Event Loop**: Continuous monitoring for events
- **Asynchronous**: Non-blocking event processing
- **Callbacks**: Functions passed as parameters

#### Java Implementation
```java
import time
from typing import Callable, Any, Dict, List
from enum import Enum

class EventType(Enum):
    """Enumeration of event types"""
    USER_LOGIN = "user_login"
    USER_LOGOUT = "user_logout"
    MESSAGE_RECEIVED = "message_received"
    FILE_UPLOADED = "file_uploaded"
    ERROR_OCCURRED = "error_occurred"

class EventManager:
    """Event management system"""
    
    def __init__(self):
        self.listeners: Dict[str, List[Callable]] = {}
        self.event_history = []
    
    def subscribe(self, event_type: str, callback: Callable):
        """Subscribe to an event type"""
        if event_type not in self.listeners:
            self.listeners[event_type] = []
        self.listeners[event_type].append(callback)
        print(f"Subscribed to {event_type}")
    
    def unsubscribe(self, event_type: str, callback: Callable):
        """Unsubscribe from an event type"""
        if event_type in self.listeners:
            self.listeners[event_type].remove(callback)
            print(f"Unsubscribed from {event_type}")
    
    def emit(self, event_type: str, data: Any = None):
        """Emit an event"""
        self.event_history.append((event_type, data, time.time()))
        
        if event_type in self.listeners:
            for callback in self.listeners[event_type]:
                try:
                    callback(data)
                except Exception as e:
                    print(f"Error in callback: {e}")
        
        print(f"Event emitted: {event_type}")
    
    def get_event_history(self, event_type: str = None):
        """Get history of events"""
        if event_type:
            return [event for event in self.event_history if event[0] == event_type]
        return self.event_history

# Event handlers
def on_user_login(data):
    """Handle user login event"""
    username = data.get('username', 'Unknown')
    print(f"Welcome back, {username}!")

def on_user_logout(data):
    """Handle user logout event"""
    username = data.get('username', 'Unknown')
    print(f"Goodbye, {username}!")

def on_message_received(data):
    """Handle message received event"""
    sender = data.get('sender', 'Unknown')
    message = data.get('message', '')
    print(f"New message from {sender}: {message}")

def on_file_uploaded(data):
    """Handle file upload event"""
    filename = data.get('filename', 'Unknown')
    size = data.get('size', 0)
    print(f"File {filename} uploaded ({size} bytes)")

def on_error_occurred(data):
    """Handle error event"""
    error_message = data.get('message', 'Unknown error')
    severity = data.get('severity', 'medium')
    print(f"Error [{severity}]: {error_message}")

# Example usage
event_manager = EventManager()

# Subscribe to events
event_manager.subscribe(EventType.USER_LOGIN.value, on_user_login)
event_manager.subscribe(EventType.USER_LOGOUT.value, on_user_logout)
event_manager.subscribe(EventType.MESSAGE_RECEIVED.value, on_message_received)
event_manager.subscribe(EventType.FILE_UPLOADED.value, on_file_uploaded)
event_manager.subscribe(EventType.ERROR_OCCURRED.value, on_error_occurred)

# Simulate events
print("Simulating event-driven system...")

time.sleep(1)
event_manager.emit(EventType.USER_LOGIN.value, {'username': 'alice'})

time.sleep(0.5)
event_manager.emit(EventType.MESSAGE_RECEIVED.value, {
    'sender': 'bob',
    'message': 'Hi Alice!'
})

time.sleep(0.5)
event_manager.emit(EventType.FILE_UPLOADED.value, {
    'filename': 'document.pdf',
    'size': 1024
})

time.sleep(0.5)
event_manager.emit(EventType.ERROR_OCCURRED.value, {
    'message': 'Network connection failed',
    'severity': 'high'
})

time.sleep(0.5)
event_manager.emit(EventType.USER_LOGOUT.value, {'username': 'alice'})

print("\nEvent History:")
for event in event_manager.get_event_history():
    print(f"  {event[0]} at {time.ctime(event[2])}")
```

#### Characteristics
- **Event-Driven**: Responds to events rather than sequential flow
- **Asynchronous**: Non-blocking event processing
- **Loose Coupling**: Components communicate through events
- **Extensible**: Easy to add new event types and handlers
- **Reactive**: System reacts to changes and inputs

#### Advantages
- **Responsiveness**: Immediate response to events
- **Modularity**: Components can be developed independently
- **Scalability**: Easy to add new features
- **Maintainability**: Changes localized to event handlers
- **Flexibility**: Dynamic event subscription/unsubscription

#### Disadvantages
- **Complexity**: Can be hard to debug event flow
- **Performance**: Event dispatch overhead
- **Ordering**: Event order may not be guaranteed
- **Memory Leaks**: Forgetting to unsubscribe can cause leaks
- **Testing**: Hard to test all event combinations

---

## Aspect-Oriented Programming

### Core Concepts
Aspect-oriented programming allows separation of cross-cutting concerns through aspects.

#### Key Principles
- **Aspects**: Modular units of cross-cutting functionality
- **Join Points**: Specific points in program execution
- **Advice**: Code executed at join points
- **Weaving**: Combining aspects with base code
- **Cross-cutting Concerns**: Functionality that affects multiple parts

#### Java Implementation
```java
import functools
import time
from typing import Callable, Any

class Aspect:
    """Base class for aspects"""
    
    def __init__(self, name: str):
        self.name = name
    
    def __call__(self, func: Callable) -> Callable:
        """Make aspect callable"""
        @functools.wraps(func)
        def wrapper(*args, **kwargs):
            # Before advice
            self.before(func.__name__, args, kwargs)
            
            try:
                # Execute the original function
                result = func(*args, **kwargs)
                
                # After advice
                self.after(func.__name__, result)
                return result
                
            except Exception as e:
                # Around advice with exception handling
                self.around_exception(func.__name__, e)
                raise
        
        return wrapper

class LoggingAspect(Aspect):
    """Logging aspect for method calls"""
    
    def before(self, method_name: str, args: tuple, kwargs: dict):
        """Log method entry"""
        print(f"[{time.strftime('%H:%M:%S')}] Entering {method_name}")
        print(f"  Args: {args}")
        print(f"  Kwargs: {kwargs}")
    
    def after(self, method_name: str, result: Any):
        """Log method exit"""
        print(f"[{time.strftime('%H:%M:%S')}] Exiting {method_name}")
        print(f"  Result: {result}")
    
    def around_exception(self, method_name: str, exception: Exception):
        """Log exceptions"""
        print(f"[{time.strftime('%H:%M:%S')}] Exception in {method_name}: {exception}")

class TimingAspect(Aspect):
    """Timing aspect for performance monitoring"""
    
    def __init__(self):
        super().__init__("timing")
    
    def before(self, method_name: str, args: tuple, kwargs: dict):
        """Record start time"""
        self.start_time = time.time()
    
    def after(self, method_name: str, result: Any):
        """Calculate and log execution time"""
        execution_time = time.time() - self.start_time
        print(f"[TIMING] {method_name} took {execution_time:.4f} seconds")

class SecurityAspect(Aspect):
    """Security aspect for access control"""
    
    def __init__(self):
        super().__init__("security")
    
    def before(self, method_name: str, args: tuple, kwargs: dict):
        """Check security permissions"""
        user_role = kwargs.get('user_role', 'guest')
        required_role = kwargs.get('required_role', 'admin')
        
        if user_role != required_role:
            raise PermissionError(f"Access denied: {user_role} cannot access {method_name}")
        print(f"[SECURITY] Access granted for {user_role} to {method_name}")

# Apply aspects to methods
logging_aspect = LoggingAspect("logging")
timing_aspect = TimingAspect("timing")
security_aspect = SecurityAspect("security")

class DatabaseService:
    """Service class with aspects applied"""
    
    @logging_aspect
    @timing_aspect
    @security_aspect
    def get_user_data(self, user_id: int, user_role: str = 'guest', required_role: str = 'admin'):
        """Get user data with security logging"""
        # Simulate database query
        time.sleep(0.1)  # Simulate I/O
        return f"User data for ID {user_id}"
    
    @logging_aspect
    @timing_aspect
    def update_user(self, user_id: int, new_data: dict):
        """Update user with logging and timing"""
        # Simulate database update
        time.sleep(0.2)
        return f"Updated user {user_id} with {new_data}"

# Example usage
db_service = DatabaseService()

try:
    # This will fail for non-admin users
    print("Attempting admin operation as guest:")
    result = db_service.get_user_data(1, 'guest', required_role='admin')
except PermissionError as e:
    print(f"Permission error: {e}")

# This will succeed
print("\nAttempting regular operation:")
result = db_service.get_user_data(1, 'user')

print("\nUpdating user data:")
update_result = db_service.update_user(1, {'name': 'Alice', 'email': 'alice@example.com'})
```

#### Characteristics
- **Cross-cutting**: Separates concerns that span multiple modules
- **Modular**: Aspects can be developed and tested independently
- **Declarative**: Declare what should happen, not how
- **Non-intrusive**: Base code doesn't know about aspects
- **Composable**: Multiple aspects can be combined

#### Advantages
- **Code Reuse**: Aspects can be applied to multiple methods
- **Separation of Concerns**: Clean separation of business logic
- **Maintainability**: Changes to concerns in one place
- **Flexibility**: Easy to add/remove aspects
- **Testing**: Aspects can be tested independently

#### Disadvantages
- **Complexity**: Hard to understand program flow
- **Debugging**: Difficult to trace execution with aspects
- **Tool Support**: Limited tooling for AOP
- **Performance**: Aspect weaving overhead
- **Integration**: Challenges with some language features
- **Learning Curve**: Steep learning curve for developers

---

## Paradigm Comparison

### Decision Matrix

| Paradigm | Best For | Complexity | Maintainability | Performance | Learning Curve |
|------------|------------|-------------|----------------|----------------|
| Procedural | Simple problems, scripts | Low | Medium | High | Low |
| OOP | Complex systems, GUIs | High | High | Medium | Medium |
| Functional | Data processing, math | Medium | High | Variable | High |
| Logic | Knowledge systems, AI | High | Medium | Low | Very High |
| Event-Driven | GUIs, servers | Medium | Medium | Variable | Medium |
| Aspect-Oriented | Enterprise apps | Very High | High | Variable | Very High |

### Selection Guidelines

#### Use Procedural When:
- Problem is simple and straightforward
- Performance is critical
- Team has procedural background
- Limited project scope
- Quick prototype needed

#### Use OOP When:
- Problem domain is complex
- System has multiple interacting entities
- Long-term maintenance expected
- Team has OOP experience
- Reusability is important

#### Use Functional When:
- Data processing pipelines
- Mathematical computations
- Concurrency is required
- Predictability is critical
- Testing is important

#### Use Event-Driven When:
- Building GUI applications
- Network servers
- Real-time systems
- IoT applications
- Asynchronous processing needed

#### Use AOP When:
- Enterprise applications
- Cross-cutting concerns exist
- Logging and monitoring needed
- Security and transaction management
- Multiple non-functional requirements

---

## Hybrid Approaches

### Multi-Paradigm Design
Modern applications often combine multiple paradigms:

```java
from typing import List, Callable
from functools import reduce

class HybridApplication:
    """Application combining multiple paradigms"""
    
    def __init__(self):
        # OOP: Internal state
        self.data = []
        self.observers = []
        
        # Event-driven: Event system
        self.event_handlers = {}
    
    # OOP method
    def add_data(self, item):
        """OOP method to add data"""
        self.data.append(item)
        self._emit_event('data_added', {'item': item})
    
    # Functional method
    def process_data(self, processor: Callable):
        """Functional approach to data processing"""
        return [processor(item) for item in self.data]
    
    # Event-driven method
    def subscribe(self, event: str, handler: Callable):
        """Event subscription"""
        if event not in self.event_handlers:
            self.event_handlers[event] = []
        self.event_handlers[event].append(handler)
    
    def _emit_event(self, event: str, data: dict):
        """Internal event emission"""
        if event in self.event_handlers:
            for handler in self.event_handlers[event]:
                handler(data)
    
    # Hybrid method combining paradigms
    def analyze_and_notify(self, analyzer: Callable, threshold: float):
        """Combine functional processing with event notification"""
        # Functional processing
        results = self.process_data(analyzer)
        
        # Event-driven notification
        if any(result > threshold for result in results):
            self._emit_event('threshold_exceeded', {
                'results': results,
                'threshold': threshold
            })
        
        return results

# Example usage
app = HybridApplication()

# Event subscription
app.subscribe('threshold_exceeded', lambda data: 
    print(f"ALERT: Threshold exceeded! Max value: {max(data['results'])}"))

# Add data
app.add_data(10)
app.add_data(25)
app.add_data(15)
app.add_data(30)
app.add_data(5)

# Functional analysis
def square_if_positive(x):
    return x * x if x > 0 else 0

results = app.analyze_and_notify(square_if_positive, 20)
print(f"Analysis results: {results}")
```

---

## Best Practices

### Paradigm Selection
1. **Analyze Requirements**: Understand problem domain and constraints
2. **Consider Team Skills**: Choose paradigm team is comfortable with
3. **Evaluate Trade-offs**: Consider maintainability vs performance
4. **Prototype First**: Try different approaches before committing
5. **Stay Consistent**: Don't mix paradigms arbitrarily

### Implementation Guidelines
1. **Clear Separation**: Keep different paradigms separate
2. **Document Decisions**: Explain why specific paradigm was chosen
3. **Test Thoroughly**: Ensure paradigm-specific features work
4. **Refactor When Needed**: Change paradigm if requirements change
5. **Learn Continuously**: Stay updated on best practices

---

## Conclusion

Programming paradigms provide different ways to think about and solve problems. The key is choosing the right paradigm for the specific problem and context.

### Key Takeaways
1. **No Silver Bullet**: Each paradigm has strengths and weaknesses
2. **Context Matters**: Choose based on problem requirements
3. **Team Considerations**: Consider team skills and experience
4. **Hybrid Approaches**: Modern applications often combine paradigms
5. **Continuous Learning**: Stay updated on paradigm developments

---

*Last Updated: March 2026*  
*Paradigms Covered: 7 major approaches*  
*Difficulty: Intermediate to Advanced*
