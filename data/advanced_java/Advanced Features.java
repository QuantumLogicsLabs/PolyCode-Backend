"""
Advanced Java Features
Comprehensive examples of advanced Java programming concepts.
"""

from __future__ import annotations
import functools
import itertools
import collections
import contextlib
import dataclasses
import typing
import asyncio
import time
from abc import ABC, abstractmethod
from typing import (
    Any, Callable, Dict, List, Optional, TypeVar, Generic, Union, 
    Protocol, runtime_checkable, Type, ClassVar
)

# Decorators
def timer_decorator(func: Callable) -> Callable:
    """Decorator to measure function execution time."""
    @functools.wraps(func)
    def wrapper(*args, **kwargs):
        start_time = time.time()
        result = func(*args, **kwargs)
        end_time = time.time()
        print(f"{func.__name__} took {end_time - start_time:.4f} seconds")
        return result
    return wrapper

def cache_decorator(func: Callable) -> Callable:
    """Simple cache decorator."""
    cache = {}
    
    @functools.wraps(func)
    def wrapper(*args, **kwargs):
        key = str(args) + str(sorted(kwargs.items()))
        if key not in cache:
            cache[key] = func(*args, **kwargs)
        return cache[key]
    
    wrapper.cache_clear = lambda: cache.clear()
    wrapper.cache_info = lambda: {'size': len(cache)}
    return wrapper

def validate_types(**type_hints) -> Callable:
    """Decorator to validate function argument types."""
    def decorator(func: Callable) -> Callable:
        @functools.wraps(func)
        def wrapper(*args, **kwargs):
            # Get function signature
            sig = typing.signature(func)
            bound_args = sig.bind(*args, **kwargs)
            bound_args.apply_defaults()
            
            # Validate types
            for param_name, param_value in bound_args.arguments.items():
                if param_name in type_hints:
                    expected_type = type_hints[param_name]
                    if not isinstance(param_value, expected_type):
                        raise TypeError(
                            f"Argument '{param_name}' must be {expected_type.__name__}, "
                            f"got {type(param_value).__name__}"
                        )
            
            return func(*args, **kwargs)
        return wrapper
    return decorator

# Metaclasses
class SingletonMeta(type):
    """Metaclass that implements the Singleton pattern."""
    
    _instances = {}
    
    def __call__(cls, *args, **kwargs):
        if cls not in cls._instances:
            cls._instances[cls] = super().__call__(*args, **kwargs)
        return cls._instances[cls]

class ValidationMeta(type):
    """Metaclass that adds validation to class attributes."""
    
    def __new__(cls, name, bases, namespace):
        # Add validation methods
        def validate(self):
            for attr_name, attr_value in self.__dict__.items():
                if not attr_name.startswith('_'):
                    if hasattr(self, f'validate_{attr_name}'):
                        validation_method = getattr(self, f'validate_{attr_name}')
                        validation_method(attr_value)
            return True
        
        namespace['validate'] = validate
        return super().__new__(cls, name, bases, namespace)

# Abstract Base Classes
class Shape(ABC):
    """Abstract base class for shapes."""
    
    @abstractmethod
    def area(self) -> float:
        """Calculate area of the shape."""
        pass
    
    @abstractmethod
    def perimeter(self) -> float:
        """Calculate perimeter of the shape."""
        pass
    
    def __str__(self) -> str:
        return f"{self.__class__.__name__} with area {self.area():.2f}"

class Drawable(Protocol):
    """Protocol for drawable objects."""
    
    def draw(self) -> None:
        """Draw the object."""
        ...
    
    def get_color(self) -> str:
        """Get the object's color."""
        ...

@runtime_checkable
class Printable(Protocol):
    """Protocol for printable objects."""
    
    def print(self) -> str:
        """Return printable representation."""
        ...

# Data Classes
@dataclasses.dataclass
class Person:
    """Data class representing a person."""
    name: str
    age: int
    email: str = ""
    
    def __post_init__(self):
        """Post-initialization validation."""
        if self.age < 0:
            raise ValueError("Age cannot be negative")
        if '@' not in self.email and self.email:
            raise ValueError("Invalid email format")
    
    def is_adult(self) -> bool:
        """Check if person is an adult."""
        return self.age >= 18

@dataclasses.dataclass(frozen=True)
class Point:
    """Immutable point data class."""
    x: float
    y: float
    
    def distance_to(self, other: Point) -> float:
        """Calculate distance to another point."""
        return ((self.x - other.x) ** 2 + (self.y - other.y) ** 2) ** 0.5

# Generic Classes
T = TypeVar('T')

class Stack(Generic[T]):
    """Generic stack implementation."""
    
    def __init__(self) -> None:
        self._items: List[T] = []
    
    def push(self, item: T) -> None:
        """Push item onto stack."""
        self._items.append(item)
    
    def pop(self) -> T:
        """Pop item from stack."""
        if not self._items:
            raise IndexError("Stack is empty")
        return self._items.pop()
    
    def is_empty(self) -> bool:
        """Check if stack is empty."""
        return not self._items

# Context Managers
class DatabaseConnection:
    """Context manager for database connections."""
    
    def __init__(self, connection_string: str):
        self.connection_string = connection_string
        self.connection = None
    
    def __enter__(self):
        print(f"Connecting to database: {self.connection_string}")
        self.connection = f"Connection to {self.connection_string}"
        return self.connection
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        print("Closing database connection")
        self.connection = None
        return False  # Don't suppress exceptions

@contextlib.contextmanager
def timer_context():
    """Context manager for timing operations."""
    start_time = time.time()
    try:
        yield
    finally:
        end_time = time.time()
        print(f"Operation took {end_time - start_time:.4f} seconds")

# Iterators and Generators
class Fibonacci:
    """Fibonacci sequence iterator."""
    
    def __init__(self, limit: int):
        self.limit = limit
        self.a, self.b = 0, 1
    
    def __iter__(self):
        return self
    
    def __next__(self) -> int:
        if self.a > self.limit:
            raise StopIteration
        result = self.a
        self.a, self.b = self.b, self.a + self.b
        return result

def fibonacci_generator(limit: int):
    """Fibonacci sequence generator."""
    a, b = 0, 1
    while a <= limit:
        yield a
        a, b = b, a + b

def infinite_counter():
    """Infinite counter generator."""
    count = 0
    while True:
        yield count
        count += 1

# Descriptors
class PositiveNumber:
    """Descriptor for positive numbers."""
    
    def __init__(self, name: str):
        self.name = name
    
    def __get__(self, obj, objtype=None) -> float:
        if obj is None:
            return self
        return obj.__dict__[self.name]
    
    def __set__(self, obj, value: float) -> None:
        if value < 0:
            raise ValueError(f"{self.name} must be positive")
        obj.__dict__[self.name] = value

class Product:
    """Product class using descriptors."""
    
    price = PositiveNumber('price')
    quantity = PositiveNumber('quantity')
    
    def __init__(self, name: str, price: float, quantity: int):
        self.name = name
        self.price = price
        self.quantity = quantity
    
    @property
    def total_value(self) -> float:
        """Calculate total value."""
        return self.price * self.quantity

# Property and Setters
class Temperature:
    """Temperature class with property and setter."""
    
    def __init__(self, celsius: float = 0.0):
        self._celsius = celsius
    
    @property
    def celsius(self) -> float:
        """Get temperature in Celsius."""
        return self._celsius
    
    @celsius.setter
    def celsius(self, value: float) -> None:
        """Set temperature in Celsius."""
        if value < -273.15:
            raise ValueError("Temperature below absolute zero is not possible")
        self._celsius = value
    
    @property
    def fahrenheit(self) -> float:
        """Get temperature in Fahrenheit."""
        return (self._celsius * 9/5) + 32
    
    @fahrenheit.setter
    def fahrenheit(self, value: float) -> None:
        """Set temperature in Fahrenheit."""
        self.celsius = (value - 32) * 5/9

# Multiple Inheritance and Mixins
class Loggable:
    """Mixin for adding logging functionality."""
    
    def log(self, message: str) -> None:
        """Log a message."""
        print(f"[{self.__class__.__name__}] {message}")

class Timestamped:
    """Mixin for adding timestamp functionality."""
    
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self.created_at = time.time()
        self.updated_at = self.created_at
    
    def update_timestamp(self) -> None:
        """Update the timestamp."""
        self.updated_at = time.time()

class Document(Loggable, Timestamped):
    """Document class with logging and timestamping."""
    
    def __init__(self, title: str, content: str):
        super().__init__()
        self.title = title
        self.content = content
        self.log(f"Document '{title}' created")
    
    def update_content(self, new_content: str) -> None:
        """Update document content."""
        self.content = new_content
        self.update_timestamp()
        self.log(f"Document '{self.title}' updated")

# Functional Programming
def compose(*functions):
    """Compose multiple functions."""
    return functools.reduce(lambda f, g: lambda x: f(g(x)), functions, lambda x: x)

def pipe(value, *functions):
    """Pipe value through multiple functions."""
    for func in functions:
        value = func(value)
    return value

def curry(func: Callable) -> Callable:
    """Curry a function."""
    @functools.wraps(func)
    def curried(*args, **kwargs):
        if len(args) + len(kwargs) >= func.__code__.co_argcount:
            return func(*args, **kwargs)
        return lambda *more_args, **more_kwargs: curried(*(args + more_args), **{**kwargs, **more_kwargs})
    return curried

# Advanced Collections
class MultiDict(dict):
    """Dictionary that can hold multiple values per key."""
    
    def __setitem__(self, key, value):
        if key in self:
            if not isinstance(self[key], list):
                super().__setitem__(key, [self[key]])
            self[key].append(value)
        else:
            super().__setitem__(key, value)
    
    def get_all(self, key):
        """Get all values for a key."""
        value = self.get(key)
        return value if isinstance(value, list) else [value] if value else []

class CaseInsensitiveDict(dict):
    """Case-insensitive dictionary."""
    
    def __init__(self, *args, **kwargs):
        super().__init__()
        for key, value in dict(*args, **kwargs).items():
            self[key] = value
    
    def __setitem__(self, key, value):
        super().__setitem__(key.lower(), value)
    
    def __getitem__(self, key):
        return super().__getitem__(key.lower())

# Type Hints and Protocols
class DataProcessor(Protocol):
    """Protocol for data processors."""
    
    def process(self, data: List[Any]) -> List[Any]:
        """Process data."""
        ...

def process_data(data: List[Any], processor: DataProcessor) -> List[Any]:
    """Process data using a processor."""
    return processor.process(data)

# Advanced Examples
class AdvancedExamples:
    """Collection of advanced Java examples."""
    
    @staticmethod
    def demonstrate_decorators():
        """Demonstrate various decorators."""
        print("=== Decorators Demo ===")
        
        @timer_decorator
        @cache_decorator
        def fibonacci(n: int) -> int:
            if n <= 1:
                return n
            return fibonacci(n-1) + fibonacci(n-2)
        
        print("Calculating Fibonacci(10):")
        result = fibonacci(10)
        print(f"Result: {result}")
        
        print("Calculating Fibonacci(10) again (cached):")
        result = fibonacci(10)
        print(f"Result: {result}")
        
        @validate_types(x=int, y=int)
        def add_numbers(x: int, y: int) -> int:
            return x + y
        
        print(f"add_numbers(5, 3) = {add_numbers(5, 3)}")
        
        try:
            add_numbers(5, "3")
        except TypeError as e:
            print(f"Type validation error: {e}")
    
    @staticmethod
    def demonstrate_metaclasses():
        """Demonstrate metaclasses."""
        print("\n=== Metaclasses Demo ===")
        
        class Database(metaclass=SingletonMeta):
            def __init__(self):
                self.connection = "database_connection"
        
        db1 = Database()
        db2 = Database()
        print(f"Same instance? {db1 is db2}")
        
        class User(metaclass=ValidationMeta):
            def __init__(self, name: str, age: int):
                self.name = name
                self.age = age
            
            def validate_age(self, age: int):
                if age < 0:
                    raise ValueError("Age must be positive")
        
        user = User("Alice", 25)
        user.validate()
        print("User validation passed")
    
    @staticmethod
    def demonstrate_dataclasses():
        """Demonstrate dataclasses."""
        print("\n=== Dataclasses Demo ===")
        
        person = Person("Alice", 25, "alice@example.com")
        print(f"Person: {person}")
        print(f"Is adult? {person.is_adult()}")
        
        point1 = Point(0, 0)
        point2 = Point(3, 4)
        print(f"Distance: {point1.distance_to(point2)}")
    
    @staticmethod
    def demonstrate_generics():
        """Demonstrate generic classes."""
        print("\n=== Generics Demo ===")
        
        int_stack = Stack[int]()
        int_stack.push(1)
        int_stack.push(2)
        print(f"Popped from int stack: {int_stack.pop()}")
        
        str_stack = Stack[str]()
        str_stack.push("hello")
        str_stack.push("world")
        print(f"Popped from str stack: {str_stack.pop()}")
    
    @staticmethod
    def demonstrate_context_managers():
        """Demonstrate context managers."""
        print("\n=== Context Managers Demo ===")
        
        with DatabaseConnection("mysql://localhost/db") as conn:
            print(f"Using connection: {conn}")
        
        with timer_context():
            time.sleep(0.1)
            print("Doing some work...")
    
    @staticmethod
    def demonstrate_iterators():
        """Demonstrate iterators and generators."""
        print("\n=== Iterators Demo ===")
        
        print("Fibonacci iterator:")
        for num in Fibonacci(10):
            print(num, end=" ")
        print()
        
        print("Fibonacci generator:")
        for num in fibonacci_generator(10):
            print(num, end=" ")
        print()
        
        print("First 5 numbers from infinite counter:")
        counter = infinite_counter()
        for i, num in zip(range(5), counter):
            print(num, end=" ")
        print()
    
    @staticmethod
    def demonstrate_descriptors():
        """Demonstrate descriptors."""
        print("\n=== Descriptors Demo ===")
        
        product = Product("Laptop", 999.99, 10)
        print(f"Product: {product.name}")
        print(f"Total value: ${product.total_value:.2f}")
        
        try:
            product.price = -100
        except ValueError as e:
            print(f"Descriptor validation: {e}")
    
    @staticmethod
    def demonstrate_properties():
        """Demonstrate properties."""
        print("\n=== Properties Demo ===")
        
        temp = Temperature()
        temp.celsius = 25
        print(f"25°C = {temp.fahrenheit:.1f}°F")
        
        temp.fahrenheit = 77
        print(f"77°F = {temp.celsius:.1f}°C")
        
        try:
            temp.celsius = -300
        except ValueError as e:
            print(f"Property validation: {e}")
    
    @staticmethod
    def demonstrate_multiple_inheritance():
        """Demonstrate multiple inheritance."""
        print("\n=== Multiple Inheritance Demo ===")
        
        doc = Document("My Document", "Initial content")
        time.sleep(0.1)  # Small delay to show timestamp difference
        doc.update_content("Updated content")
    
    @staticmethod
    def demonstrate_functional_programming():
        """Demonstrate functional programming."""
        print("\n=== Functional Programming Demo ===")
        
        # Function composition
        add_one = lambda x: x + 1
        multiply_by_two = lambda x: x * 2
        square = lambda x: x ** 2
        
        composed = compose(square, multiply_by_two, add_one)
        result = composed(3)  # ((3 + 1) * 2) ** 2 = 64
        print(f"Composed function result: {result}")
        
        # Function piping
        result = pipe(3, add_one, multiply_by_two, square)
        print(f"Piped function result: {result}")
        
        # Currying
        @curry
        def add(a, b, c):
            return a + b + c
        
        add_five = add(2, 3)  # Partially applied function
        result = add_five(5)  # 2 + 3 + 5 = 10
        print(f"Curried function result: {result}")
    
    @staticmethod
    def demonstrate_advanced_collections():
        """Demonstrate advanced collections."""
        print("\n=== Advanced Collections Demo ===")
        
        # MultiDict
        md = MultiDict()
        md['key1'] = 'value1'
        md['key1'] = 'value2'
        md['key2'] = 'value3'
        
        print("MultiDict values:")
        print(f"key1: {md.get_all('key1')}")
        print(f"key2: {md.get_all('key2')}")
        
        # CaseInsensitiveDict
        cid = CaseInsensitiveDict({'Key1': 'Value1', 'KEY2': 'Value2'})
        print(f"key1: {cid['key1']}")
        print(f"key2: {cid['KEY2']}")

def main():
    """Run all advanced Java examples."""
    print("ADVANCED JAVA FEATURES DEMONSTRATION")
    print("=" * 50)
    
    examples = AdvancedExamples()
    
    examples.demonstrate_decorators()
    examples.demonstrate_metaclasses()
    examples.demonstrate_dataclasses()
    examples.demonstrate_generics()
    examples.demonstrate_context_managers()
    examples.demonstrate_iterators()
    examples.demonstrate_descriptors()
    examples.demonstrate_properties()
    examples.demonstrate_multiple_inheritance()
    examples.demonstrate_functional_programming()
    examples.demonstrate_advanced_collections()
    
    print("\n" + "=" * 50)
    print("Advanced Java features demonstration complete!")

if __name__ == "__main__":
    main()
