# Java Multithreading

## Thread Basics

### Creating Threads
```java
// Method 1: Extending Thread class
public class MyThread extends Thread {
    private String name;
    
    public MyThread(String name) {
        this.name = name;
    }
    
    @Override
    public void run() {
        for (int i = 0; i < 5; i++) {
            System.out.println(name + " - Count: " + i);
            try {
                Thread.sleep(1000); // Sleep for 1 second
            } catch (InterruptedException e) {
                System.out.println(name + " interrupted");
            }
        }
    }
}

// Method 2: Implementing Runnable interface
public class MyRunnable implements Runnable {
    private String name;
    
    public MyRunnable(String name) {
        this.name = name;
    }
    
    @Override
    public void run() {
        for (int i = 0; i < 5; i++) {
            System.out.println(name + " - Count: " + i);
            try {
                Thread.sleep(1000);
            } catch (InterruptedException e) {
                System.out.println(name + " interrupted");
            }
        }
    }
}

// Using threads
public class ThreadDemo {
    public static void main(String[] args) {
        // Method 1: Extending Thread
        MyThread thread1 = new MyThread("Thread-1");
        thread1.start();
        
        // Method 2: Implementing Runnable
        Thread thread2 = new Thread(new MyRunnable("Thread-2"));
        thread2.start();
        
        // Method 3: Lambda expression (Java 8+)
        Thread thread3 = new Thread(() -> {
            for (int i = 0; i < 5; i++) {
                System.out.println("Lambda Thread - Count: " + i);
                try {
                    Thread.sleep(1000);
                } catch (InterruptedException e) {
                    System.out.println("Lambda thread interrupted");
                }
            }
        });
        thread3.start();
        
        System.out.println("Main thread continues...");
    }
}
```

### Thread States
```java
public class ThreadStates {
    public static void main(String[] args) throws InterruptedException {
        Thread thread = new Thread(() -> {
            try {
                System.out.println("Thread running");
                Thread.sleep(2000);
                System.out.println("Thread finished");
            } catch (InterruptedException e) {
                System.out.println("Thread interrupted");
            }
        });
        
        System.out.println("Thread state: " + thread.getState()); // NEW
        thread.start();
        System.out.println("Thread state: " + thread.getState()); // RUNNABLE
        
        Thread.sleep(500);
        System.out.println("Thread state: " + thread.getState()); // TIMED_WAITING
        
        thread.join();
        System.out.println("Thread state: " + thread.getState()); // TERMINATED
    }
}
```

## Thread Synchronization

### Synchronized Methods
```java
public class BankAccount {
    private double balance;
    private final Object lock = new Object();
    
    public BankAccount(double initialBalance) {
        this.balance = initialBalance;
    }
    
    // Synchronized method
    public synchronized void deposit(double amount) {
        if (amount > 0) {
            balance += amount;
            System.out.println(Thread.currentThread().getName() + 
                " deposited: $" + amount + ", Balance: $" + balance);
        }
    }
    
    // Synchronized method
    public synchronized void withdraw(double amount) {
        if (amount > 0 && balance >= amount) {
            balance -= amount;
            System.out.println(Thread.currentThread().getName() + 
                " withdrew: $" + amount + ", Balance: $" + balance);
        } else {
            System.out.println(Thread.currentThread().getName() + 
                " insufficient funds for withdrawal: $" + amount);
        }
    }
    
    // Synchronized block
    public void transfer(BankAccount toAccount, double amount) {
        synchronized (lock) {
            if (balance >= amount) {
                this.balance -= amount;
                toAccount.deposit(amount);
                System.out.println(Thread.currentThread().getName() + 
                    " transferred: $" + amount);
            }
        }
    }
    
    public synchronized double getBalance() {
        return balance;
    }
}

// Testing synchronized methods
public class BankDemo {
    public static void main(String[] args) {
        BankAccount account = new BankAccount(1000.0);
        
        // Multiple threads accessing the same account
        Thread[] threads = new Thread[5];
        
        for (int i = 0; i < threads.length; i++) {
            threads[i] = new Thread(() -> {
                for (int j = 0; j < 10; j++) {
                    account.deposit(10.0);
                    account.withdraw(5.0);
                }
            }, "Thread-" + i);
            threads[i].start();
        }
        
        // Wait for all threads to complete
        for (Thread thread : threads) {
            try {
                thread.join();
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
        }
        
        System.out.println("Final balance: $" + account.getBalance());
    }
}
```

### Wait and Notify
```java
public class ProducerConsumer {
    private static final int BUFFER_SIZE = 5;
    private int[] buffer = new int[BUFFER_SIZE];
    private int count = 0;
    
    // Producer method
    public synchronized void produce(int item) throws InterruptedException {
        while (count == BUFFER_SIZE) {
            System.out.println("Buffer full, producer waiting...");
            wait(); // Wait for consumer to consume
        }
        
        buffer[count] = item;
        count++;
        System.out.println("Produced: " + item + ", Buffer count: " + count);
        
        notify(); // Notify consumer that item is available
    }
    
    // Consumer method
    public synchronized int consume() throws InterruptedException {
        while (count == 0) {
            System.out.println("Buffer empty, consumer waiting...");
            wait(); // Wait for producer to produce
        }
        
        int item = buffer[count - 1];
        count--;
        System.out.println("Consumed: " + item + ", Buffer count: " + count);
        
        notify(); // Notify producer that space is available
        return item;
    }
}

// Producer thread
class Producer extends Thread {
    private ProducerConsumer pc;
    
    public Producer(ProducerConsumer pc) {
        this.pc = pc;
    }
    
    @Override
    public void run() {
        try {
            for (int i = 1; i <= 10; i++) {
                pc.produce(i);
                Thread.sleep(1000);
            }
        } catch (InterruptedException e) {
            e.printStackTrace();
        }
    }
}

// Consumer thread
class Consumer extends Thread {
    private ProducerConsumer pc;
    
    public Consumer(ProducerConsumer pc) {
        this.pc = pc;
    }
    
    @Override
    public void run() {
        try {
            for (int i = 1; i <= 10; i++) {
                pc.consume();
                Thread.sleep(1500);
            }
        } catch (InterruptedException e) {
            e.printStackTrace();
        }
    }
}

public class ProducerConsumerDemo {
    public static void main(String[] args) {
        ProducerConsumer pc = new ProducerConsumer();
        
        Producer producer = new Producer(pc);
        Consumer consumer = new Consumer(pc);
        
        producer.start();
        consumer.start();
    }
}
```

## Thread Pools

### ExecutorService
```java
import java.util.concurrent.*;

public class ThreadPoolDemo {
    public static void main(String[] args) {
        // Create a fixed thread pool
        ExecutorService executor = Executors.newFixedThreadPool(3);
        
        // Submit tasks for execution
        for (int i = 1; i <= 10; i++) {
            final int taskId = i;
            executor.submit(() -> {
                System.out.println("Task " + taskId + 
                    " executed by: " + Thread.currentThread().getName());
                try {
                    Thread.sleep(2000);
                } catch (InterruptedException e) {
                    e.printStackTrace();
                }
            });
        }
        
        // Shutdown the executor
        executor.shutdown();
        
        try {
            // Wait for all tasks to complete
            if (!executor.awaitTermination(60, TimeUnit.SECONDS)) {
                executor.shutdownNow();
            }
        } catch (InterruptedException e) {
            executor.shutdownNow();
        }
        
        System.out.println("All tasks completed");
    }
}
```

### Callable and Future
```java
import java.util.concurrent.*;

public class CallableFutureDemo {
    public static void main(String[] args) {
        ExecutorService executor = Executors.newFixedThreadPool(3);
        
        // Submit Callable tasks
        Future<Integer> future1 = executor.submit(() -> {
            Thread.sleep(2000);
            return 42;
        });
        
        Future<String> future2 = executor.submit(() -> {
            Thread.sleep(1000);
            return "Hello World";
        });
        
        Future<Double> future3 = executor.submit(() -> {
            Thread.sleep(3000);
            return 3.14159;
        });
        
        // Get results
        try {
            System.out.println("Result 1: " + future1.get());
            System.out.println("Result 2: " + future2.get());
            System.out.println("Result 3: " + future3.get());
        } catch (InterruptedException | ExecutionException e) {
            e.printStackTrace();
        }
        
        executor.shutdown();
    }
}
```

## Concurrent Collections

### ConcurrentHashMap
```java
import java.util.concurrent.*;
import java.util.Map;

public class ConcurrentMapDemo {
    public static void main(String[] args) throws InterruptedException {
        Map<String, Integer> concurrentMap = new ConcurrentHashMap<>();
        
        // Multiple threads updating the map
        Thread[] threads = new Thread[5];
        
        for (int i = 0; i < threads.length; i++) {
            final int threadId = i;
            threads[i] = new Thread(() -> {
                for (int j = 0; j < 1000; j++) {
                    String key = "Key-" + (j % 10);
                    concurrentMap.put(key, concurrentMap.getOrDefault(key, 0) + 1);
                }
                System.out.println("Thread " + threadId + " completed");
            });
            threads[i].start();
        }
        
        // Wait for all threads to complete
        for (Thread thread : threads) {
            thread.join();
        }
        
        System.out.println("Final map size: " + concurrentMap.size());
        concurrentMap.forEach((key, value) -> 
            System.out.println(key + ": " + value));
    }
}
```

### BlockingQueue
```java
import java.util.concurrent.*;

public class BlockingQueueDemo {
    public static void main(String[] args) {
        BlockingQueue<String> queue = new LinkedBlockingQueue<>(5);
        
        // Producer thread
        Thread producer = new Thread(() -> {
            try {
                for (int i = 1; i <= 10; i++) {
                    String item = "Item-" + i;
                    queue.put(item); // Blocks if queue is full
                    System.out.println("Produced: " + item);
                    Thread.sleep(500);
                }
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
        });
        
        // Consumer thread
        Thread consumer = new Thread(() -> {
            try {
                for (int i = 1; i <= 10; i++) {
                    String item = queue.take(); // Blocks if queue is empty
                    System.out.println("Consumed: " + item);
                    Thread.sleep(1000);
                }
            } catch (InterruptedException e) {
                e.printStackTrace();
            }
        });
        
        producer.start();
        consumer.start();
    }
}
```

## Atomic Operations

### AtomicInteger
```java
import java.util.concurrent.atomic.AtomicInteger;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

public class AtomicDemo {
    private static AtomicInteger counter = new AtomicInteger(0);
    
    public static void main(String[] args) throws InterruptedException {
        ExecutorService executor = Executors.newFixedThreadPool(10);
        
        // Multiple threads incrementing the counter
        for (int i = 0; i < 1000; i++) {
            executor.submit(() -> {
                counter.incrementAndGet();
            });
        }
        
        executor.shutdown();
        executor.awaitTermination(1, TimeUnit.MINUTES);
        
        System.out.println("Final counter value: " + counter.get());
    }
}
```

### AtomicReference
```java
import java.util.concurrent.atomic.AtomicReference;

public class AtomicReferenceDemo {
    private static AtomicReference<String> ref = new AtomicReference<>("Initial");
    
    public static void main(String[] args) {
        // Compare and set
        boolean success = ref.compareAndSet("Initial", "Updated");
        System.out.println("Update successful: " + success);
        System.out.println("Current value: " + ref.get());
        
        // This will fail because current value is "Updated"
        success = ref.compareAndSet("Initial", "Failed");
        System.out.println("Update successful: " + success);
        System.out.println("Current value: " + ref.get());
    }
}
```

## Examples

### Complete Multithreading Example
```java
import java.util.concurrent.*;
import java.util.concurrent.atomic.AtomicInteger;

public class CompleteMultithreadingExample {
    
    // Shared resource with synchronization
    static class SharedCounter {
        private int count = 0;
        private final Object lock = new Object();
        
        public void increment() {
            synchronized (lock) {
                count++;
                System.out.println(Thread.currentThread().getName() + 
                    " incremented count to: " + count);
            }
        }
        
        public void decrement() {
            synchronized (lock) {
                count--;
                System.out.println(Thread.currentThread().getName() + 
                    " decremented count to: " + count);
            }
        }
        
        public int getCount() {
            synchronized (lock) {
                return count;
            }
        }
    }
    
    // Worker task
    static class WorkerTask implements Callable<String> {
        private final String taskId;
        private final SharedCounter counter;
        private final AtomicInteger atomicCounter;
        
        public WorkerTask(String taskId, SharedCounter counter, AtomicInteger atomicCounter) {
            this.taskId = taskId;
            this.counter = counter;
            this.atomicCounter = atomicCounter;
        }
        
        @Override
        public String call() throws Exception {
            try {
                for (int i = 0; i < 5; i++) {
                    counter.increment();
                    atomicCounter.incrementAndGet();
                    
                    Thread.sleep(100);
                    
                    if (i % 2 == 0) {
                        counter.decrement();
                        atomicCounter.decrementAndGet();
                    }
                    
                    Thread.sleep(50);
                }
                
                return taskId + " completed successfully";
            } catch (InterruptedException e) {
                Thread.currentThread().interrupt();
                return taskId + " was interrupted";
            }
        }
    }
    
    public static void main(String[] args) throws InterruptedException, ExecutionException {
        // Create shared resources
        SharedCounter sharedCounter = new SharedCounter();
        AtomicInteger atomicCounter = new AtomicInteger(0);
        
        // Create thread pool
        ExecutorService executor = Executors.newFixedThreadPool(3);
        
        // Submit tasks
        List<Future<String>> futures = new ArrayList<>();
        
        for (int i = 1; i <= 5; i++) {
            Future<String> future = executor.submit(
                new WorkerTask("Task-" + i, sharedCounter, atomicCounter));
            futures.add(future);
        }
        
        // Wait for all tasks to complete
        for (Future<String> future : futures) {
            String result = future.get();
            System.out.println(result);
        }
        
        // Shutdown executor
        executor.shutdown();
        executor.awaitTermination(1, TimeUnit.MINUTES);
        
        // Print final results
        System.out.println("\nFinal Results:");
        System.out.println("Shared counter: " + sharedCounter.getCount());
        System.out.println("Atomic counter: " + atomicCounter.get());
        
        // Demonstrate concurrent collection
        BlockingQueue<String> workQueue = new LinkedBlockingQueue<>();
        
        // Producer
        Thread producer = new Thread(() -> {
            try {
                for (int i = 1; i <= 5; i++) {
                    String work = "Work-Item-" + i;
                    workQueue.put(work);
                    System.out.println("Produced: " + work);
                    Thread.sleep(200);
                }
            } catch (InterruptedException e) {
                Thread.currentThread().interrupt();
            }
        });
        
        // Consumer
        Thread consumer = new Thread(() -> {
            try {
                while (true) {
                    String work = workQueue.take();
                    System.out.println("Consumed: " + work);
                    Thread.sleep(300);
                    
                    if (work.equals("Work-Item-5")) {
                        break;
                    }
                }
            } catch (InterruptedException e) {
                Thread.currentThread().interrupt();
            }
        });
        
        producer.start();
        consumer.start();
        
        producer.join();
        consumer.join();
        
        System.out.println("\nAll operations completed!");
    }
}
```

## Best Practices
- Use thread pools instead of creating threads directly
- Use synchronized blocks instead of synchronized methods when possible
- Use concurrent collections for thread-safe data structures
- Use atomic variables for simple atomic operations
- Avoid shared mutable state when possible
- Use proper exception handling in multithreaded code
- Be aware of deadlock and race conditions
- Use appropriate synchronization granularity
- Consider using higher-level concurrency utilities
- Test multithreaded code thoroughly
