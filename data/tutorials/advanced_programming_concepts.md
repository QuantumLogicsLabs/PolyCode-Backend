# Advanced Programming Concepts - Complete Guide

This guide covers advanced programming concepts that go beyond basic syntax and simple data structures.

## 📚 Table of Contents

1. [Memory Management](#memory-management)
2. [Concurrency and Parallelism](#concurrency-and-parallelism)
3. [Metaprogramming](#metaprogramming)
4. [Functional Programming Deep Dive](#functional-programming-deep-dive)
5. [Design Patterns](#design-patterns)
6. [Performance Optimization](#performance-optimization)
7. [System Programming](#system-programming)
8. [Security Best Practices](#security-best-practices)

---

## Memory Management

### Memory Pools

#### Concept
Memory pools pre-allocate memory blocks to avoid frequent allocations/deallocations.

#### Implementation
```java
import ctypes
import threading
from typing import List, Optional

class MemoryPool:
    """Simple memory pool for fixed-size objects"""
    
    def __init__(self, object_size: int, pool_size: int):
        self.object_size = object_size
        self.pool_size = pool_size
        self.memory = (ctypes.c_ubyte * (object_size * pool_size))()
        self.free_blocks = list(range(pool_size))
        self.allocated_blocks = set()
        self.lock = threading.Lock()
    
    def allocate(self) -> Optional[int]:
        """Allocate a block from the pool"""
        with self.lock:
            if not self.free_blocks:
                return None
            
            block_index = self.free_blocks.pop(0)
            self.allocated_blocks.add(block_index)
            
            return block_index * self.object_size
    
    def deallocate(self, block_ptr: int) -> bool:
        """Deallocate a block back to the pool"""
        with self.lock:
            if block_ptr % self.object_size != 0:
                return False
            
            block_index = block_ptr // self.object_size
            if block_index in self.allocated_blocks:
                self.allocated_blocks.remove(block_index)
                self.free_blocks.append(block_index)
                return True
            
            return False
    
    def get_allocated_count(self) -> int:
        """Get number of allocated blocks"""
        return len(self.allocated_blocks)
    
    def get_free_count(self) -> int:
        """Get number of free blocks"""
        return len(self.free_blocks)

# Example usage
pool = MemoryPool(object_size=1024, pool_size=100)

# Allocate some blocks
blocks = []
for i in range(50):
    block = pool.allocate()
    if block is not None:
        blocks.append(block)

print(f"Allocated {len(blocks)} blocks")
print(f"Free blocks: {pool.get_free_count()}")

# Deallocate some blocks
for i in range(0, 25, 2):
    if i < len(blocks):
        pool.deallocate(blocks[i])

print(f"After deallocation:")
print(f"Allocated: {pool.get_allocated_count()}")
print(f"Free: {pool.get_free_count()}")
```

#### Analysis
- **Time Complexity**: O(1) allocation/deallocation
- **Space Efficiency**: No memory fragmentation
- **Use Cases**: High-frequency allocations, real-time systems
- **Limitations**: Fixed-size objects only

---

### Weak References

#### Concept
Weak references allow objects to be garbage collected when no strong references exist.

#### Implementation
```java
import weakref
from typing import List, Optional

class Cache:
    """Cache using weak references"""
    
    def __init__(self):
        self._cache: weakref.WeakKeyDictionary = {}
        self._stats = {'hits': 0, 'misses': 0}
    
    def get(self, key: str) -> Optional[object]:
        """Get object from cache"""
        obj = self._cache.get(key)
        if obj is not None:
            self._stats['hits'] += 1
        else:
            self._stats['misses'] += 1
        return obj
    
    def put(self, key: str, obj: object) -> None:
        """Put object in cache"""
        self._cache[key] = weakref.ref(obj)
    
    def get_stats(self) -> dict:
        """Get cache statistics"""
        return self._stats

class ExpensiveObject:
    """Expensive object to cache"""
    
    def __init__(self, name: str):
        self.name = name
        self.data = list(range(1000))  # Simulate expensive creation
    
    def __del__(self):
        print(f"ExpensiveObject '{self.name}' was garbage collected")

# Example usage
cache = Cache()

# Create and cache objects
obj1 = ExpensiveObject("Object 1")
obj2 = ExpensiveObject("Object 2")

cache.put("obj1", obj1)
cache.put("obj2", obj2)

# Access from cache
cached_obj1 = cache.get("obj1")
cached_obj2 = cache.get("obj2")
cached_obj3 = cache.get("obj3")  # Not cached

print(f"Cache stats: {cache.get_stats()}")
print(f"Cached obj1 exists: {cached_obj1 is not None}")
print(f"Cached obj2 exists: {cached_obj2 is not None}")
print(f"Cached obj3 exists: {cached_obj3 is not None}")

# Remove strong references
del obj1, obj2

# Objects get garbage collected
import gc
gc.collect()  # Force garbage collection
```

#### Analysis
- **Memory Management**: Automatic cleanup of unused objects
- **Use Cases**: Caches, object pools, circular references
- **Performance**: Slight overhead for weak reference management
- **Caution**: Objects may disappear unexpectedly

---

## Concurrency and Parallelism

### Thread Pool

#### Concept
Thread pool manages a pool of worker threads to execute tasks concurrently.

#### Implementation
```java
import threading
import queue
import time
from typing import Callable, Any, List
from concurrent.futures import ThreadPoolExecutor

class ThreadPool:
    """Custom thread pool implementation"""
    
    def __init__(self, num_workers: int):
        self.num_workers = num_workers
        self.task_queue = queue.Queue()
        self.workers = []
        self.shutdown = False
        
        # Create worker threads
        for _ in range(num_workers):
            worker = threading.Thread(target=self._worker)
            worker.daemon = True
            worker.start()
            self.workers.append(worker)
    
    def _worker(self):
        """Worker thread function"""
        while not self.shutdown:
            try:
                task = self.task_queue.get(timeout=1)
                if task is None:  # Shutdown signal
                    continue
                
                func, args, callback = task
                result = func(*args)
                
                if callback:
                    callback(result)
                
            except queue.Empty:
                continue
    
    def submit(self, func: Callable, args: tuple = (), callback: Callable = None):
        """Submit task to thread pool"""
        self.task_queue.put((func, args, callback))
        return True
    
    def shutdown_pool(self):
        """Shutdown the thread pool"""
        self.shutdown = True
        
        # Wait for all tasks to complete
        self.task_queue.join()
        
        # Wait for all workers to finish
        for worker in self.workers:
            worker.join()

# Example usage
def process_data(data, multiplier=1):
    """Sample processing function"""
    time.sleep(1)  # Simulate work
    return data * multiplier

def task_callback(result):
    """Callback function"""
    print(f"Task completed with result: {result}")

# Create thread pool
pool = ThreadPool(num_workers=4)

# Submit tasks
tasks = [(i, (i,), task_callback) for i in range(10)]
for task in tasks:
    pool.submit(*task)

# Wait for all tasks to complete
time.sleep(12)
pool.shutdown_pool()

# Using ThreadPoolExecutor (recommended)
def process_with_executor():
    with ThreadPoolExecutor(max_workers=4) as executor:
        futures = [executor.submit(process_data, i, i*2) for i in range(10)]
        for future in futures:
            result = future.result()
            print(f"Executor result: {result}")

process_with_executor()
```

#### Analysis
- **Concurrency**: Multiple tasks executed in parallel
- **Resource Management**: Limited number of threads
- **Use Cases**: I/O-bound tasks, web servers, data processing
- **Considerations**: Thread safety, task granularity

---

### Async Programming with Asyncio

#### Concept
Asyncio provides asynchronous I/O using coroutines and event loops.

#### Implementation
```java
import asyncio
import aiohttp
import time
from typing import List

class AsyncWebCrawler:
    """Async web crawler using aiohttp"""
    
    def __init__(self, max_concurrent: int = 10):
        self.max_concurrent = max_concurrent
        self.session = None
        self.visited_urls = set()
        self.results = []
    
    async def fetch_page(self, url: str) -> str:
        """Fetch a single page asynchronously"""
        if url in self.visited_urls:
            return ""
        
        try:
            async with self.session.get(url) as response:
                content = await response.text()
                self.visited_urls.add(url)
                return content
        except Exception as e:
            print(f"Error fetching {url}: {e}")
            return ""
    
    async def process_urls(self, urls: List[str]) -> List[str]:
        """Process multiple URLs concurrently"""
        if not self.session:
            connector = aiohttp.TCPConnector(limit=self.max_concurrent)
            self.session = aiohttp.ClientSession(connector=connector)
        
        tasks = [self.fetch_page(url) for url in urls]
        results = await asyncio.gather(*tasks)
        
        return [result for result in results if result]
    
    async def crawl(self, start_url: str, max_depth: int = 2):
        """Crawl web pages asynchronously"""
        urls_to_visit = [start_url]
        
        for depth in range(max_depth):
            if not urls_to_visit:
                break
            
            current_urls = urls_to_visit[:]
            urls_to_visit = []
            
            # Process current batch of URLs
            new_urls = await self.process_urls(current_urls)
            
            # Extract new URLs (simplified)
            for url in new_urls:
                if url not in self.visited_urls:
                    urls_to_visit.append(url)
        
        return self.results

# Example usage
async def main():
    crawler = AsyncWebCrawler()
    
    # Crawl starting URL
    results = await crawler.crawl("https://example.com")
    
    print(f"Found {len(results)} pages")
    for i, result in enumerate(results[:5]):
        print(f"Page {i+1}: {len(result)} characters")

# Run the async crawler
asyncio.run(main())
```

#### Analysis
- **Performance**: Excellent for I/O-bound tasks
- **Scalability**: Handles thousands of concurrent operations
- **Complexity**: Requires understanding of async/await patterns
- **Use Cases**: Web scraping, API calls, database operations

---

## Metaprogramming

### Decorators with Parameters

#### Concept
Advanced decorators that accept parameters and modify behavior based on them.

#### Implementation
```java
from functools import wraps
import time
from typing import Callable, Any

def retry(max_attempts: int = 3, delay: float = 1.0):
    """Retry decorator with parameters"""
    def decorator(func: Callable) -> Callable:
        @wraps(func)
        def wrapper(*args, **kwargs):
            last_exception = None
            
            for attempt in range(max_attempts):
                try:
                    return func(*args, **kwargs)
                except Exception as e:
                    last_exception = e
                    if attempt < max_attempts - 1:
                        time.sleep(delay)
                        print(f"Attempt {attempt + 1} failed, retrying...")
                    else:
                        print(f"All {max_attempts} attempts failed")
                        raise last_exception
            
            return wrapper
        return decorator
    return decorator

def cache_with_ttl(ttl_seconds: int = 300):
    """Cache decorator with time-to-live"""
    def decorator(func: Callable) -> Callable:
        cache = {}
        
        @wraps(func)
        def wrapper(*args, **kwargs):
            import time
            current_time = time.time()
            
            # Create cache key
            cache_key = str(args) + str(sorted(kwargs.items()))
            
            # Check cache
            if cache_key in cache:
                cached_time, result = cache[cache_key]
                if current_time - cached_time < ttl_seconds:
                    return result
                else:
                    del cache[cache_key]
            
            # Execute and cache result
            result = func(*args, **kwargs)
            cache[cache_key] = (current_time, result)
            
            return result
        return wrapper
    return decorator

# Example usage
@retry(max_attempts=3, delay=1.0)
def unreliable_function(x):
    """Function that might fail"""
    if x < 0:
        raise ValueError("Negative value not allowed")
    return x * 2

@cache_with_ttl(ttl_seconds=5)
def expensive_computation(x):
    """Expensive computation with caching"""
    import time
    time.sleep(1)  # Simulate work
    return x ** 2

print("Testing retry decorator:")
try:
    result = unreliable_function(-5)
    print(f"Result: {result}")
except ValueError as e:
    print(f"Caught error: {e}")

print("\nTesting cache decorator:")
print(f"First call: {expensive_computation(10)}")
print(f"Second call (cached): {expensive_computation(10)}")
time.sleep(6)
print(f"Third call (cache expired): {expensive_computation(10)}")
```

#### Analysis
- **Flexibility**: Highly configurable behavior modification
- **Reusability**: Generic decorators for multiple use cases
- **Complexity**: Requires understanding of closures and decorators
- **Use Cases**: API clients, error handling, caching systems

---

### Class Decorators

#### Concept
Decorators that modify class behavior during class definition.

#### Implementation
```java
def validate_attributes(cls):
    """Class decorator to validate attributes"""
    for attr_name, attr_value in cls.__dict__.items():
        if not attr_name.startswith('_'):
            if not isinstance(attr_value, (int, float, str)):
                raise ValueError(f"Attribute {attr_name} must be int, float, or str")
    
    return cls

def add_method(method_name: str):
    """Class decorator to add methods to a class"""
    def decorator(cls):
        def new_method(self):
            return f"Method {method_name} called"
        
        setattr(cls, method_name, new_method)
        return cls
    return decorator

# Example usage
@validate_attributes
class Person:
    name = "Alice"
    age = 25
    # _private_attr = "hidden"  # Not validated

@add_method("greet")
class Employee:
    def __init__(self, name):
        self.name = name

# Test the classes
person = Person()
print(f"Person name: {person.name}")

employee = Employee("Bob")
print(f"Employee name: {employee.name}")
print(f"Employee greet: {employee.greet()}")
```

#### Analysis
- **Class Modification**: Powerful metaprogramming technique
- **Validation**: Automatic attribute and method validation
- **Dynamic Behavior**: Add functionality at class definition time
- **Use Cases**: API frameworks, validation systems, ORM design

---

## Functional Programming Deep Dive

### Higher-Order Functions

#### Concept
Functions that take other functions as arguments or return functions.

#### Implementation
```java
from typing import Callable, List, Any

def compose(f: Callable, g: Callable) -> Callable:
    """Function composition"""
    return lambda x: f(g(x))

def pipe(*functions):
    """Pipe multiple functions"""
    def composed(x):
        result = x
        for func in functions:
            result = func(result)
        return result
    return composed

def curry(func: Callable) -> Callable:
    """Curry function - partial application"""
    def curried(*args, **kwargs):
        if len(args) >= func.__code__.co_argcount:
            return func(*args, **kwargs)
        
        return lambda *more_args: func(*(args + more_args), **kwargs)
    return curried

# Example usage
def add(x, y):
    return x + y

def multiply(x, y):
    return x * y

# Function composition
add_then_multiply = compose(add, multiply)
result = add_then_multiply(2, 3)  # (2 + 3) * 2 = 10

# Pipe operations
pipeline = pipe(lambda x: x * 2, lambda x: x + 1)
result = pipeline(5)  # ((5 * 2) + 1) = 11

# Currying
curried_add = curry(add)
add_5 = curried_add(5)
result = add_5(3)  # 5 + 3 = 8

print(f"Composition result: {result}")
print(f"Pipe result: {pipeline(5)}")
print(f"Curried result: {result}")
```

#### Analysis
- **Composability**: Build complex functions from simple ones
- **Partial Application**: Specialize functions for specific use cases
- **Reusability**: Generic, reusable function patterns
- **Functional Style**: Emphasizes immutability and pure functions

---

## Design Patterns

### Observer Pattern

#### Concept
Define one-to-many dependency between objects so that when one object changes state, all dependents are notified.

#### Implementation
```java
from typing import Protocol, List, Callable
from abc import ABC, abstractmethod

class Observer(Protocol):
    """Observer protocol"""
    def update(self, subject: 'Subject') -> None: ...

class Subject(ABC):
    """Subject (Observable) abstract base"""
    
    def __init__(self):
        self._observers: List[Observer] = []
    
    def attach(self, observer: Observer) -> None:
        """Attach observer to subject"""
        if observer not in self._observers:
            self._observers.append(observer)
    
    def detach(self, observer: Observer) -> None:
        """Detach observer from subject"""
        if observer in self._observers:
            self._observers.remove(observer)
    
    @abstractmethod
    def notify(self) -> None:
        """Notify all observers"""
        pass

class WeatherStation(Subject):
    """Weather station (subject)"""
    
    def __init__(self):
        super().__init__()
        self._temperature = 0
        self._humidity = 0
    
    def set_measurements(self, temperature: float, humidity: float) -> None:
        """Set weather measurements"""
        self._temperature = temperature
        self._humidity = humidity
        self.notify()
    
    def get_temperature(self) -> float:
        return self._temperature
    
    def get_humidity(self) -> float:
        return self._humidity

class WeatherDisplay(Observer):
    """Weather display (observer)"""
    
    def __init__(self, name: str):
        self.name = name
    
    def update(self, subject: WeatherStation) -> None:
        """Update display with weather data"""
        temp = subject.get_temperature()
        humidity = subject.get_humidity()
        print(f"{self.name}: Temperature = {temp}°C, Humidity = {humidity}%")

class WeatherLogger(Observer):
    """Weather logger (observer)"""
    
    def __init__(self, filename: str):
        self.filename = filename
    
    def update(self, subject: WeatherStation) -> None:
        """Log weather data to file"""
        temp = subject.get_temperature()
        humidity = subject.get_humidity()
        timestamp = time.strftime("%Y-%m-%d %H:%M:%S")
        
        with open(self.filename, 'a') as f:
            f.write(f"{timestamp} - Temp: {temp}°C, Humidity: {humidity}%\n")

# Example usage
weather_station = WeatherStation()

# Create observers
display1 = WeatherDisplay("Display 1")
display2 = WeatherDisplay("Display 2")
logger = WeatherLogger("weather.log")

# Attach observers
weather_station.attach(display1)
weather_station.attach(display2)
weather_station.attach(logger)

# Change state and notify observers
weather_station.set_measurements(25.0, 60.0)
weather_station.set_measurements(26.0, 65.0)
weather_station.set_measurements(24.0, 55.0)
```

#### Analysis
- **Loose Coupling**: Subjects and observers are loosely coupled
- **Dynamic Relationships**: Observers can be added/removed at runtime
- **Event-Driven**: Natural fit for GUI and event systems
- **Scalability**: Easy to add multiple observers

---

### Factory Pattern

#### Concept
Define an interface for creating objects, but let subclasses decide which class to instantiate.

#### Implementation
```java
from abc import ABC, abstractmethod
from enum import Enum

class AnimalType(Enum):
    DOG = "dog"
    CAT = "cat"
    BIRD = "bird"

class Animal(ABC):
    """Abstract animal base class"""
    
    @abstractmethod
    def make_sound(self) -> str:
        pass
    
    @abstractmethod
    def move(self) -> str:
        pass

class Dog(Animal):
    """Dog implementation"""
    
    def make_sound(self) -> str:
        return "Woof!"
    
    def move(self) -> str:
        return "Running"

class Cat(Animal):
    """Cat implementation"""
    
    def make_sound(self) -> str:
        return "Meow!"
    
    def move(self) -> str:
        return "Pouncing"

class Bird(Animal):
    """Bird implementation"""
    
    def make_sound(self) -> str:
        return "Tweet!"
    
    def move(self) -> str:
        return "Flying"

class AnimalFactory:
    """Factory for creating animals"""
    
    @staticmethod
    def create_animal(animal_type: AnimalType) -> Animal:
        """Create animal based on type"""
        if animal_type == AnimalType.DOG:
            return Dog()
        elif animal_type == AnimalType.CAT:
            return Cat()
        elif animal_type == AnimalType.BIRD:
            return Bird()
        else:
            raise ValueError(f"Unknown animal type: {animal_type}")

# Example usage
factory = AnimalFactory()

# Create different animals
dog = factory.create_animal(AnimalType.DOG)
cat = factory.create_animal(AnimalType.CAT)
bird = factory.create_animal(AnimalType.BIRD)

print(f"Dog: {dog.make_sound()}, {dog.move()}")
print(f"Cat: {cat.make_sound()}, {cat.move()}")
print(f"Bird: {bird.make_sound()}, {bird.move()}")
```

#### Analysis
- **Decoupling**: Client code separated from object creation
- **Extensibility**: Easy to add new types without changing client code
- **Configuration**: Centralized object creation logic
- **Type Safety**: Compile-time checking of object creation

---

## Performance Optimization

### Vectorization with NumPy

#### Concept
Use vectorized operations instead of loops for numerical computations.

#### Implementation
```java
import numpy as np
import time

# Slow: Java loops
def slow_sum(arr):
    """Sum using Java loops"""
    total = 0
    for x in arr:
        total += x
    return total

# Fast: NumPy vectorization
def fast_sum(arr):
    """Sum using NumPy vectorization"""
    return np.sum(arr)

# Performance comparison
data = np.random.rand(1000000)

# Time Java version
start = time.time()
result_slow = slow_sum(data)
time_slow = time.time() - start

# Time NumPy version
start = time.time()
result_fast = fast_sum(data)
time_fast = time.time() - start

print(f"Java loops: {time_slow:.4f} seconds")
print(f"NumPy vectorization: {time_fast:.4f} seconds")
print(f"Speedup: {time_slow/time_fast:.1f}x")

# More vectorized operations
def vectorized_operations(arr):
    """Multiple vectorized operations"""
    return {
        'sum': np.sum(arr),
        'mean': np.mean(arr),
        'std': np.std(arr),
        'min': np.min(arr),
        'max': np.max(arr),
        'squared': np.square(arr),
        'normalized': (arr - np.mean(arr)) / np.std(arr)
    }

results = vectorized_operations(data)
print(f"Statistics: {results}")
```

#### Analysis
- **Performance**: Orders of magnitude faster for numerical operations
- **Memory**: Efficient memory layout for numerical data
- **Convenience**: Rich mathematical function library
- **Use Cases**: Data science, machine learning, scientific computing

---

### Memory Profiling

#### Concept
Analyze memory usage patterns to identify leaks and optimization opportunities.

#### Implementation
```java
import tracemalloc
import gc
import sys
from typing import Dict, List

class MemoryProfiler:
    """Memory profiling utility"""
    
    def __init__(self):
        self.snapshots = []
        self.tracer = tracemalloc.get_tracer()
    
    def start_profiling(self):
        """Start memory profiling"""
        tracemalloc.start()
        gc.collect()  # Force garbage collection
    
    def take_snapshot(self, label: str):
        """Take memory snapshot"""
        snapshot = tracemalloc.take_snapshot()
        self.snapshots.append((label, snapshot))
    
    def get_stats(self) -> Dict:
        """Get memory statistics"""
        if not self.snapshots:
            return {}
        
        current_snapshot = self.snapshots[-1][1]
        stats = tracemalloc.get_object_traced_totals()
        
        return {
            'total_allocated': stats.total,
            'total_freed': stats.freed,
            'current_allocated': stats.total - stats.freed
            'peak_allocated': stats.peak
        }
    
    def compare_snapshots(self, index1: int, index2: int) -> Dict:
        """Compare two memory snapshots"""
        if index1 >= len(self.snapshots) or index2 >= len(self.snapshots):
            return {}
        
        label1, snapshot1 = self.snapshots[index1]
        label2, snapshot2 = self.snapshots[index2]
        
        stats1 = snapshot1.statistics()
        stats2 = snapshot2.statistics()
        
        return {
            'label1': label1,
            'label2': label2,
            'size_diff': stats2.total_size - stats1.total_size,
            'count_diff': stats2.count - stats1.count
        }

# Example usage
profiler = MemoryProfiler()

# Start profiling
profiler.start_profiling()

# Allocate some memory
data = []
for i in range(10000):
    data.append([0] * 1000)  # 10MB per allocation

profiler.take_snapshot("After allocation")

# Free some memory
data = data[:5000]  # Free half

profiler.take_snapshot("After deallocation")

# Get statistics
stats = profiler.get_stats()
print(f"Memory statistics: {stats}")

# Compare snapshots
comparison = profiler.compare_snapshots(0, 1)
print(f"Snapshot comparison: {comparison}")
```

#### Analysis
- **Memory Awareness**: Understand memory allocation patterns
- **Leak Detection**: Identify memory leaks in long-running applications
- **Optimization**: Make informed decisions about memory usage
- **Use Cases**: Long-running servers, data processing pipelines

---

## System Programming

### File System Monitoring

#### Concept
Monitor file system events and changes using system-specific APIs.

#### Implementation
```java
import os
import time
import threading
from typing import Callable, Dict, Set
from pathlib import Path

class FileMonitor:
    """File system monitoring with OS-specific APIs"""
    
    def __init__(self, directory: str):
        self.directory = Path(directory)
        self.callbacks: Dict[str, List[Callable]] = {}
        self.monitored_files: Set[Path] = set()
        self.running = False
        self.observer = None
    
    def add_callback(self, event_type: str, callback: Callable):
        """Add callback for file system event"""
        if event_type not in self.callbacks:
            self.callbacks[event_type] = []
        self.callbacks[event_type].append(callback)
    
    def _handle_event(self, event_type: str, path: Path):
        """Handle file system event"""
        if path in self.monitored_files:
            for callback in self.callbacks.get(event_type, []):
                callback(event_type, str(path))
    
    def start_monitoring(self):
        """Start file system monitoring"""
        if self.running:
            return
        
        self.running = True
        
        # Get initial file list
        for file_path in self.directory.rglob("*"):
            if file_path.is_file():
                self.monitored_files.add(file_path)
        
        # Start monitoring thread
        self.observer = threading.Thread(target=self._monitor_loop, daemon=True)
        self.observer.start()
    
    def _monitor_loop(self):
        """Main monitoring loop"""
        try:
            while self.running:
                # Check for file changes
                current_files = set(self.directory.rglob("*"))
                
                # New files
                new_files = current_files - self.monitored_files
                for file_path in new_files:
                    if file_path.is_file():
                        self._handle_event("created", file_path)
                        self.monitored_files.add(file_path)
                
                # Deleted files
                deleted_files = self.monitored_files - current_files
                for file_path in deleted_files:
                    self._handle_event("deleted", file_path)
                    self.monitored_files.discard(file_path)
                
                time.sleep(1)
                
        except Exception as e:
            print(f"Monitoring error: {e}")
    
    def stop_monitoring(self):
        """Stop file system monitoring"""
        self.running = False
        if self.observer:
            self.observer.join()

# Example usage
def handle_created(event_type: str, file_path: str):
    """Handle file creation event"""
    print(f"File {file_path} was {event_type}")

def handle_deleted(event_type: str, file_path: str):
    """Handle file deletion event"""
    print(f"File {file_path} was {event_type}")

# Create and start monitor
monitor = FileMonitor("/tmp/test_directory")
monitor.add_callback("created", handle_created)
monitor.add_callback("deleted", handle_deleted)
monitor.start_monitoring()

# Let it run for a bit
time.sleep(5)

# Stop monitoring
monitor.stop_monitoring()
```

#### Analysis
- **Real-time Monitoring**: Immediate notification of file system events
- **Platform-Specific**: Uses OS-specific APIs for efficiency
- **Resource Usage**: Minimal overhead for monitoring
- **Use Cases**: File synchronization, backup systems, security monitoring

---

## Security Best Practices

### Input Validation

#### Concept
Validate all external inputs to prevent security vulnerabilities.

#### Implementation
```java
import re
from typing import Optional, Pattern
from urllib.parse import urlparse

class InputValidator:
    """Input validation utilities"""
    
    @staticmethod
    def validate_email(email: str) -> bool:
        """Validate email address format"""
        pattern = r'^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$'
        return re.match(pattern, email) is not None
    
    @staticmethod
    def validate_password(password: str) -> bool:
        """Validate password strength"""
        if len(password) < 8:
            return False
        
        has_upper = any(c.isupper() for c in password)
        has_lower = any(c.islower() for c in password)
        has_digit = any(c.isdigit() for c in password)
        has_special = any(c in "!@#$%^&*()_+-=[]{}|;:,.<>?" for c in password)
        
        return len(password) >= 8 and sum([has_upper, has_lower, has_digit, has_special]) >= 3
    
    @staticmethod
    def sanitize_filename(filename: str) -> str:
        """Sanitize filename to prevent directory traversal"""
        # Remove path separators
        sanitized = filename.replace('/', '').replace('\\', '')
        # Remove dangerous characters
        sanitized = re.sub(r'[<>:"|?*]', '', sanitized)
        # Limit length
        return sanitized[:255]
    
    @staticmethod
    def validate_url(url: str) -> bool:
        """Validate URL format"""
        try:
            result = urlparse(url)
            return all([result.scheme, result.netloc])
        except Exception:
            return False

# Example usage
validator = InputValidator()

emails = ["user@example.com", "invalid-email", "test@domain.co.uk", "user.name@domain"]
passwords = ["password", "Password123", "StrongP@ss!2023", "weak"]

print("Email validation:")
for email in emails:
    is_valid = validator.validate_email(email)
    print(f"  {email}: {'Valid' if is_valid else 'Invalid'}")

print("\nPassword validation:")
for password in passwords:
    is_strong = validator.validate_password(password)
    print(f"  {password}: {'Strong' if is_strong else 'Weak'}")

print(f"\nURL validation:")
urls = ["https://example.com", "ftp://invalid.com", "not-a-url"]
for url in urls:
    is_valid = validator.validate_url(url)
    print(f"  {url}: {'Valid' if is_valid else 'Invalid'}")
```

#### Analysis
- **Security First**: Prevents common vulnerabilities
- **Input Sanitization**: Removes dangerous characters and patterns
- **Validation Rules**: Comprehensive checks for data integrity
- **Error Handling**: Graceful failure with informative messages

---

## Conclusion

Advanced programming concepts enable you to build more efficient, secure, and maintainable applications.

### Key Takeaways
1. **Memory Management**: Understand allocation patterns and garbage collection
2. **Concurrency**: Use appropriate concurrency models for your use case
3. **Metaprogramming**: Create flexible, reusable code structures
4. **Performance**: Profile and optimize critical code paths
5. **Security**: Always validate and sanitize external inputs

---

*Last Updated: March 2026*  
*Concepts Covered: 8 advanced programming topics*  
*Difficulty: Advanced*
