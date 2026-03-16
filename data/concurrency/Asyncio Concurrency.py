"""
Asyncio and Concurrency
Comprehensive examples of asynchronous programming and concurrency.
"""

import asyncio
import aiohttp
import threading
import multiprocessing
import concurrent.futures
import time
import queue
import random
from typing import List, Dict, Any, Optional, Callable
from dataclasses import dataclass
from contextlib import asynccontextmanager
import functools

# Asyncio Examples
class AsyncWebScraper:
    """Asynchronous web scraper using aiohttp."""
    
    def __init__(self):
        self.session: Optional[aiohttp.ClientSession] = None
    
    async def __aenter__(self):
        self.session = aiohttp.ClientSession()
        return self
    
    async def __aexit__(self, exc_type, exc_val, exc_tb):
        if self.session:
            await self.session.close()
    
    async def fetch_url(self, url: str, timeout: int = 10) -> Dict[str, Any]:
        """Fetch a single URL asynchronously."""
        if not self.session:
            raise RuntimeError("Session not initialized")
        
        try:
            async with self.session.get(url, timeout=timeout) as response:
                content = await response.text()
                return {
                    'url': url,
                    'status': response.status,
                    'content_length': len(content),
                    'success': response.status == 200
                }
        except Exception as e:
            return {
                'url': url,
                'status': 0,
                'content_length': 0,
                'success': False,
                'error': str(e)
            }
    
    async def fetch_multiple_urls(self, urls: List[str]) -> List[Dict[str, Any]]:
        """Fetch multiple URLs concurrently."""
        tasks = [self.fetch_url(url) for url in urls]
        return await asyncio.gather(*tasks, return_exceptions=True)

class AsyncTaskProcessor:
    """Asynchronous task processor."""
    
    def __init__(self, max_concurrent_tasks: int = 5):
        self.semaphore = asyncio.Semaphore(max_concurrent_tasks)
        self.results = []
    
    async def process_item(self, item: Any, processor: Callable) -> Any:
        """Process a single item with concurrency control."""
        async with self.semaphore:
            # Simulate async processing
            await asyncio.sleep(random.uniform(0.1, 0.5))
            result = await asyncio.to_thread(processor, item)
            return result
    
    async def process_items(self, items: List[Any], processor: Callable) -> List[Any]:
        """Process multiple items concurrently."""
        tasks = [self.process_item(item, processor) for item in items]
        results = await asyncio.gather(*tasks, return_exceptions=True)
        return [r for r in results if not isinstance(r, Exception)]

# Async Context Managers
@asynccontextmanager
async def async_timer():
    """Async context manager for timing operations."""
    start_time = time.time()
    try:
        yield
    finally:
        end_time = time.time()
        print(f"Async operation took {end_time - start_time:.4f} seconds")

class AsyncDatabaseConnection:
    """Async database connection manager."""
    
    def __init__(self, connection_string: str):
        self.connection_string = connection_string
        self.connection = None
    
    async def __aenter__(self):
        print(f"Connecting to database: {self.connection_string}")
        await asyncio.sleep(0.1)  # Simulate connection time
        self.connection = f"AsyncConnection to {self.connection_string}"
        return self.connection
    
    async def __aexit__(self, exc_type, exc_val, exc_tb):
        print("Closing async database connection")
        await asyncio.sleep(0.05)  # Simulate disconnection time
        self.connection = None
    
    async def execute_query(self, query: str) -> List[Dict[str, Any]]:
        """Execute a query asynchronously."""
        if not self.connection:
            raise RuntimeError("Not connected to database")
        
        await asyncio.sleep(0.2)  # Simulate query execution time
        return [{'id': 1, 'name': 'Sample', 'query': query}]

# Thread-based Concurrency
class ThreadWorker:
    """Thread-based worker for CPU-bound tasks."""
    
    def __init__(self, num_workers: int = 4):
        self.num_workers = num_workers
        self.executor = concurrent.futures.ThreadPoolExecutor(max_workers=num_workers)
    
    def process_items(self, items: List[Any], processor: Callable) -> List[Any]:
        """Process items using thread pool."""
        futures = [self.executor.submit(processor, item) for item in items]
        results = []
        
        for future in concurrent.futures.as_completed(futures):
            try:
                result = future.result()
                results.append(result)
            except Exception as e:
                print(f"Error processing item: {e}")
        
        return results
    
    def shutdown(self):
        """Shutdown the executor."""
        self.executor.shutdown(wait=True)

# Process-based Concurrency
class ProcessWorker:
    """Process-based worker for CPU-intensive tasks."""
    
    def __init__(self, num_processes: int = None):
        self.num_processes = num_processes or multiprocessing.cpu_count()
    
    @staticmethod
    def cpu_intensive_task(n: int) -> int:
        """CPU-intensive task for demonstration."""
        result = 0
        for i in range(n):
            result += i * i
        return result
    
    def process_items(self, items: List[int]) -> List[int]:
        """Process items using process pool."""
        with multiprocessing.Pool(self.num_processes) as pool:
            results = pool.map(self.cpu_intensive_task, items)
        return results

# Producer-Consumer Pattern
class ProducerConsumer:
    """Producer-Consumer pattern using queues."""
    
    def __init__(self, max_queue_size: int = 10):
        self.queue = queue.Queue(maxsize=max_queue_size)
        self.producer_thread = None
        self.consumer_thread = None
        self.stop_event = threading.Event()
    
    def producer(self, items: List[Any]):
        """Producer function."""
        for item in items:
            if self.stop_event.is_set():
                break
            
            try:
                self.queue.put(item, timeout=1)
                print(f"Produced: {item}")
                time.sleep(0.1)  # Simulate production time
            except queue.Full:
                print("Queue is full, waiting...")
                continue
    
    def consumer(self):
        """Consumer function."""
        while not self.stop_event.is_set():
            try:
                item = self.queue.get(timeout=1)
                print(f"Consumed: {item}")
                time.sleep(0.2)  # Simulate consumption time
                self.queue.task_done()
            except queue.Empty:
                continue
    
    def start(self, items: List[Any]):
        """Start producer and consumer threads."""
        self.producer_thread = threading.Thread(target=self.producer, args=(items,))
        self.consumer_thread = threading.Thread(target=self.consumer)
        
        self.producer_thread.start()
        self.consumer_thread.start()
    
    def stop(self):
        """Stop producer and consumer threads."""
        self.stop_event.set()
        
        if self.producer_thread:
            self.producer_thread.join()
        if self.consumer_thread:
            self.consumer_thread.join()

# Async Producer-Consumer
class AsyncProducerConsumer:
    """Async producer-consumer using asyncio queues."""
    
    def __init__(self, max_queue_size: int = 10):
        self.queue = asyncio.Queue(maxsize=max_queue_size)
        self.producer_task = None
        self.consumer_task = None
    
    async def producer(self, items: List[Any]):
        """Async producer function."""
        for item in items:
            try:
                await self.queue.put(item)
                print(f"Produced: {item}")
                await asyncio.sleep(0.1)
            except asyncio.QueueFull:
                print("Queue is full, waiting...")
                await asyncio.sleep(0.1)
    
    async def consumer(self):
        """Async consumer function."""
        while True:
            try:
                item = await asyncio.wait_for(self.queue.get(), timeout=1.0)
                print(f"Consumed: {item}")
                await asyncio.sleep(0.2)
                self.queue.task_done()
            except asyncio.TimeoutError:
                break
    
    async def run(self, items: List[Any]):
        """Run async producer and consumer."""
        producer_task = asyncio.create_task(self.producer(items))
        consumer_task = asyncio.create_task(self.consumer())
        
        await producer_task
        await self.queue.join()
        
        # Signal consumer to stop
        await self.queue.put(None)  # Sentinel value
        await consumer_task

# Coroutines and Generators
async def async_generator():
    """Async generator example."""
    for i in range(5):
        await asyncio.sleep(0.1)
        yield i

async def consume_async_generator():
    """Consume async generator."""
    async for value in async_generator():
        print(f"Generated value: {value}")

# Async Decorators
def async_timer(func):
    """Decorator to time async functions."""
    @functools.wraps(func)
    async def wrapper(*args, **kwargs):
        start_time = time.time()
        result = await func(*args, **kwargs)
        end_time = time.time()
        print(f"{func.__name__} took {end_time - start_time:.4f} seconds")
        return result
    return wrapper

def retry_async(max_retries: int = 3, delay: float = 1.0):
    """Decorator to retry async functions."""
    def decorator(func):
        @functools.wraps(func)
        async def wrapper(*args, **kwargs):
            last_exception = None
            
            for attempt in range(max_retries):
                try:
                    return await func(*args, **kwargs)
                except Exception as e:
                    last_exception = e
                    if attempt < max_retries - 1:
                        await asyncio.sleep(delay)
                    else:
                        raise last_exception
        return wrapper
    return decorator

# Async Classes
class AsyncCounter:
    """Async counter with locking."""
    
    def __init__(self):
        self.count = 0
        self._lock = asyncio.Lock()
    
    async def increment(self):
        """Increment counter asynchronously."""
        async with self._lock:
            await asyncio.sleep(0.01)  # Simulate work
            self.count += 1
            return self.count
    
    async def get_count(self):
        """Get current count."""
        async with self._lock:
            return self.count

# Performance Comparison
class PerformanceComparison:
    """Compare performance of different concurrency approaches."""
    
    @staticmethod
    def cpu_intensive_task(n: int) -> int:
        """CPU-intensive task."""
        result = 0
        for i in range(n):
            result += i * i
        return result
    
    @staticmethod
    def io_intensive_task(duration: float) -> float:
        """IO-intensive task simulation."""
        time.sleep(duration)
        return duration
    
    async def async_io_task(self, duration: float) -> float:
        """Async IO task."""
        await asyncio.sleep(duration)
        return duration
    
    async def compare_io_performance(self):
        """Compare async vs sync IO performance."""
        print("=== IO Performance Comparison ===")
        
        # Synchronous approach
        start_time = time.time()
        for _ in range(5):
            self.io_intensive_task(0.1)
        sync_time = time.time() - start_time
        
        # Asynchronous approach
        start_time = time.time()
        tasks = [self.async_io_task(0.1) for _ in range(5)]
        await asyncio.gather(*tasks)
        async_time = time.time() - start_time
        
        print(f"Synchronous IO time: {sync_time:.4f} seconds")
        print(f"Asynchronous IO time: {async_time:.4f} seconds")
        print(f"Speedup: {sync_time/async_time:.2f}x")
    
    def compare_cpu_performance(self):
        """Compare thread vs process CPU performance."""
        print("\n=== CPU Performance Comparison ===")
        
        items = [10000, 20000, 30000, 40000, 50000]
        
        # Sequential processing
        start_time = time.time()
        sequential_results = [self.cpu_intensive_task(n) for n in items]
        sequential_time = time.time() - start_time
        
        # Thread-based processing
        thread_worker = ThreadWorker(num_workers=4)
        start_time = time.time()
        thread_results = thread_worker.process_items(items, self.cpu_intensive_task)
        thread_time = time.time() - start_time
        thread_worker.shutdown()
        
        # Process-based processing
        process_worker = ProcessWorker()
        start_time = time.time()
        process_results = process_worker.process_items(items)
        process_time = time.time() - start_time
        
        print(f"Sequential CPU time: {sequential_time:.4f} seconds")
        print(f"Thread-based CPU time: {thread_time:.4f} seconds")
        print(f"Process-based CPU time: {process_time:.4f} seconds")
        print(f"Thread speedup: {sequential_time/thread_time:.2f}x")
        print(f"Process speedup: {sequential_time/process_time:.2f}x")

# Main Demonstration
async def main_async():
    """Main async demonstration."""
    print("ASYNCIO AND CONCURRENCY DEMONSTRATION")
    print("=" * 50)
    
    # Async web scraping (simulated)
    print("\n1. Async Web Scraping:")
    urls = [f"https://example.com/page{i}" for i in range(5)]
    
    async with AsyncWebScraper() as scraper:
        results = await scraper.fetch_multiple_urls(urls)
        for result in results:
            if isinstance(result, dict):
                print(f"  {result['url']}: {'Success' if result['success'] else 'Failed'}")
    
    # Async task processing
    print("\n2. Async Task Processing:")
    processor = AsyncTaskProcessor(max_concurrent_tasks=3)
    
    def square_number(x):
        return x * x
    
    items = list(range(1, 6))
    results = await processor.process_items(items, square_number)
    print(f"  Results: {results}")
    
    # Async context manager
    print("\n3. Async Context Manager:")
    async with async_timer():
        await asyncio.sleep(0.2)
        print("  Doing async work...")
    
    # Async database operations
    print("\n4. Async Database Operations:")
    async with AsyncDatabaseConnection("postgresql://localhost/db") as conn:
        results = await conn.execute_query("SELECT * FROM users")
        print(f"  Query results: {results}")
    
    # Async producer-consumer
    print("\n5. Async Producer-Consumer:")
    producer_consumer = AsyncProducerConsumer()
    items = [f"item_{i}" for i in range(5)]
    await producer_consumer.run(items)
    
    # Async generator
    print("\n6. Async Generator:")
    await consume_async_generator()
    
    # Async counter
    print("\n7. Async Counter with Lock:")
    counter = AsyncCounter()
    tasks = [counter.increment() for _ in range(5)]
    results = await asyncio.gather(*tasks)
    print(f"  Counter results: {results}")
    print(f"  Final count: {await counter.get_count()}")
    
    # Performance comparison
    print("\n8. Performance Comparison:")
    comparison = PerformanceComparison()
    await comparison.compare_io_performance()
    comparison.compare_cpu_performance()

def main_threading():
    """Main threading demonstration."""
    print("\nTHREADING DEMONSTRATION")
    print("=" * 30)
    
    # Thread worker
    print("\n1. Thread Worker:")
    thread_worker = ThreadWorker(num_workers=3)
    
    def process_item(item):
        time.sleep(0.1)
        return f"processed_{item}"
    
    items = [f"item_{i}" for i in range(5)]
    results = thread_worker.process_items(items, process_item)
    print(f"  Results: {results}")
    thread_worker.shutdown()
    
    # Producer-Consumer
    print("\n2. Thread Producer-Consumer:")
    producer_consumer = ProducerConsumer()
    items = [f"item_{i}" for i in range(5)]
    producer_consumer.start(items)
    time.sleep(2)  # Let it run for a bit
    producer_consumer.stop()

def main_processes():
    """Main process demonstration."""
    print("\nPROCESS DEMONSTRATION")
    print("=" * 25)
    
    process_worker = ProcessWorker()
    items = [1000, 2000, 3000, 4000, 5000]
    results = process_worker.process_items(items)
    print(f"Process results: {results}")

# Entry point
async def main():
    """Main entry point combining all demonstrations."""
    await main_async()
    main_threading()
    main_processes()
    
    print("\n" + "=" * 50)
    print("Asyncio and Concurrency demonstration complete!")

if __name__ == "__main__":
    # Run the async main function
    asyncio.run(main())
