public class MultithreadingExample {
    public static void main(String[] args) {
        // Basic Thread Creation
        System.out.println("=== Basic Thread Creation ===");
        demonstrateBasicThreads();
        
        // Thread Synchronization
        System.out.println("\n=== Thread Synchronization ===");
        demonstrateSynchronization();
        
        // Thread Communication
        System.out.println("\n=== Thread Communication ===");
        demonstrateThreadCommunication();
        
        // Thread Pool
        System.out.println("\n=== Thread Pool ===");
        demonstrateThreadPool();
        
        // Producer-Consumer Pattern
        System.out.println("\n=== Producer-Consumer Pattern ===");
        demonstrateProducerConsumer();
        
        // Thread Safety
        System.out.println("\n=== Thread Safety ===");
        demonstrateThreadSafety();
        
        // Concurrent Collections
        System.out.println("\n=== Concurrent Collections ===");
        demonstrateConcurrentCollections();
        
        // Performance Considerations
        System.out.println("\n=== Performance Considerations ===");
        performanceComparison();
    }
    
    public static void demonstrateBasicThreads() {
        // Create threads using Runnable
        Thread thread1 = new Thread(new NumberPrinter("Thread-1", 1, 5));
        Thread thread2 = new Thread(new NumberPrinter("Thread-2", 6, 10));
        
        // Start threads
        thread1.start();
        thread2.start();
        
        // Wait for threads to complete
        try {
            thread1.join();
            thread2.join();
        } catch (InterruptedException e) {
            System.err.println("Thread interrupted: " + e.getMessage());
        }
        
        System.out.println("Basic threads completed");
        
        // Create thread using Thread subclass
        MyThread thread3 = new MyThread("Thread-3", 11, 15);
        thread3.start();
        
        try {
            thread3.join();
        } catch (InterruptedException e) {
            System.err.println("Thread interrupted: " + e.getMessage());
        }
    }
    
    public static void demonstrateSynchronization() {
        Counter counter = new Counter();
        
        // Create multiple threads without synchronization
        Thread[] unsyncThreads = new Thread[5];
        for (int i = 0; i < unsyncThreads.length; i++) {
            unsyncThreads[i] = new Thread(new CounterIncrementer(counter, false, 1000));
            unsyncThreads[i].start();
        }
        
        // Wait for unsynchronized threads
        for (Thread thread : unsyncThreads) {
            try {
                thread.join();
            } catch (InterruptedException e) {
                System.err.println("Thread interrupted: " + e.getMessage());
            }
        }
        
        System.out.println("Unsynchronized counter: " + counter.getCount());
        
        // Reset counter
        counter.reset();
        
        // Create synchronized threads
        Thread[] syncThreads = new Thread[5];
        for (int i = 0; i < syncThreads.length; i++) {
            syncThreads[i] = new Thread(new CounterIncrementer(counter, true, 1000));
            syncThreads[i].start();
        }
        
        // Wait for synchronized threads
        for (Thread thread : syncThreads) {
            try {
                thread.join();
            } catch (InterruptedException e) {
                System.err.println("Thread interrupted: " + e.getMessage());
            }
        }
        
        System.out.println("Synchronized counter: " + counter.getCount());
    }
    
    public static void demonstrateThreadCommunication() {
        ThreadMessageQueue messageQueue = new ThreadMessageQueue();
        
        // Producer thread
        Thread producer = new Thread(new MessageProducer(messageQueue), "Producer");
        
        // Consumer threads
        Thread consumer1 = new Thread(new MessageConsumer(messageQueue), "Consumer-1");
        Thread consumer2 = new Thread(new MessageConsumer(messageQueue), "Consumer-2");
        
        // Start threads
        producer.start();
        consumer1.start();
        consumer2.start();
        
        // Let them run for a while
        try {
            Thread.sleep(3000);
        } catch (InterruptedException e) {
            System.err.println("Sleep interrupted: " + e.getMessage());
        }
        
        // Stop threads
        producer.interrupt();
        consumer1.interrupt();
        consumer2.interrupt();
        
        // Wait for threads to finish
        try {
            producer.join();
            consumer1.join();
            consumer2.join();
        } catch (InterruptedException e) {
            System.err.println("Thread interrupted: " + e.getMessage());
        }
        
        System.out.println("Thread communication demo completed");
    }
    
    public static void demonstrateThreadPool() {
        // Create thread pool
        ThreadPool threadPool = new ThreadPool(3);
        
        // Submit tasks
        for (int i = 1; i <= 10; i++) {
            final int taskId = i;
            threadPool.submit(new ThreadPoolTask(taskId, "Task " + taskId));
        }
        
        // Wait for all tasks to complete
        try {
            Thread.sleep(2000);
        } catch (InterruptedException e) {
            System.err.println("Sleep interrupted: " + e.getMessage());
        }
        
        // Shutdown thread pool
        threadPool.shutdown();
        System.out.println("Thread pool demo completed");
    }
    
    public static void demonstrateProducerConsumer() {
        Buffer buffer = new Buffer(5);
        
        // Producer thread
        Thread producer = new Thread(new Producer(buffer), "Producer");
        
        // Consumer thread
        Thread consumer = new Thread(new Consumer(buffer), "Consumer");
        
        // Start threads
        producer.start();
        consumer.start();
        
        // Let them run for a while
        try {
            Thread.sleep(2000);
        } catch (InterruptedException e) {
            System.err.println("Sleep interrupted: " + e.getMessage());
        }
        
        // Stop threads
        producer.interrupt();
        consumer.interrupt();
        
        // Wait for threads to finish
        try {
            producer.join();
            consumer.join();
        } catch (InterruptedException e) {
            System.err.println("Thread interrupted: " + e.getMessage());
        }
        
        System.out.println("Producer-consumer demo completed");
    }
    
    public static void demonstrateThreadSafety() {
        // Unsafe operations
        UnsafeList unsafeList = new UnsafeList();
        Thread[] unsafeThreads = new Thread[5];
        
        for (int i = 0; i < unsafeThreads.length; i++) {
            unsafeThreads[i] = new Thread(new ListAdder(unsafeList, i));
            unsafeThreads[i].start();
        }
        
        // Safe operations
        SafeList safeList = new SafeList();
        Thread[] safeThreads = new Thread[5];
        
        for (int i = 0; i < safeThreads.length; i++) {
            safeThreads[i] = new Thread(new ListAdder(safeList, i));
            safeThreads[i].start();
        }
        
        // Wait for all threads
        for (int i = 0; i < unsafeThreads.length; i++) {
            try {
                unsafeThreads[i].join();
                safeThreads[i].join();
            } catch (InterruptedException e) {
                System.err.println("Thread interrupted: " + e.getMessage());
            }
        }
        
        System.out.println("Unsafe list size: " + unsafeList.size());
        System.out.println("Safe list size: " + safeList.size());
    }
    
    public static void demonstrateConcurrentCollections() {
        // ConcurrentHashMap
        java.util.concurrent.ConcurrentHashMap<String, Integer> concurrentMap = 
            new java.util.concurrent.ConcurrentHashMap<>();
        
        // Multiple threads adding to map
        Thread[] mapThreads = new Thread[10];
        for (int i = 0; i < mapThreads.length; i++) {
            final int threadId = i;
            mapThreads[i] = new Thread(() -> {
                for (int j = 0; j < 100; j++) {
                    concurrentMap.put("Key" + threadId + "-" + j, j);
                }
            });
            mapThreads[i].start();
        }
        
        // CopyOnWriteArrayList
        java.util.concurrent.CopyOnWriteArrayList<String> concurrentList = 
            new java.util.concurrent.CopyOnWriteArrayList<>();
        
        // Multiple threads adding to list
        Thread[] listThreads = new Thread[5];
        for (int i = 0; i < listThreads.length; i++) {
            final int threadId = i;
            listThreads[i] = new Thread(() -> {
                for (int j = 0; j < 50; j++) {
                    concurrentList.add("Item" + threadId + "-" + j);
                }
            });
            listThreads[i].start();
        }
        
        // Wait for all threads
        for (int i = 0; i < mapThreads.length; i++) {
            try {
                mapThreads[i].join();
                listThreads[i].join();
            } catch (InterruptedException e) {
                System.err.println("Thread interrupted: " + e.getMessage());
            }
        }
        
        System.out.println("Concurrent map size: " + concurrentMap.size());
        System.out.println("Concurrent list size: " + concurrentList.size());
    }
    
    public static void performanceComparison() {
        final int OPERATIONS = 100000;
        
        // Single-threaded performance
        long startTime = System.nanoTime();
        long sum = 0;
        for (int i = 0; i < OPERATIONS; i++) {
            sum += i;
        }
        long endTime = System.nanoTime();
        
        System.out.println("Single-threaded (" + OPERATIONS + " operations): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
        
        // Multi-threaded performance
        startTime = System.nanoTime();
        Thread[] threads = new Thread[4];
        long[] partialSums = new long[4];
        
        for (int i = 0; i < threads.length; i++) {
            final int threadId = i;
            final int start = threadId * (OPERATIONS / 4);
            final int end = (threadId + 1) * (OPERATIONS / 4);
            
            threads[i] = new Thread(() -> {
                for (int j = start; j < end; j++) {
                    partialSums[threadId] += j;
                }
            });
            threads[i].start();
        }
        
        for (Thread thread : threads) {
            try {
                thread.join();
            } catch (InterruptedException e) {
                System.err.println("Thread interrupted: " + e.getMessage());
            }
        }
        
        long multiSum = 0;
        for (long partial : partialSums) {
            multiSum += partial;
        }
        
        endTime = System.nanoTime();
        System.out.println("Multi-threaded (" + OPERATIONS + " operations): " + 
                          (endTime - startTime) / 1000000.0 + " ms");
        
        System.out.println("Results match: " + (sum == multiSum));
    }
}

// Supporting Classes

class NumberPrinter implements Runnable {
    private String name;
    private int start;
    private int end;
    
    public NumberPrinter(String name, int start, int end) {
        this.name = name;
        this.start = start;
        this.end = end;
    }
    
    @Override
    public void run() {
        for (int i = start; i <= end; i++) {
            System.out.println(name + ": " + i);
            try {
                Thread.sleep(100); // Simulate work
            } catch (InterruptedException e) {
                System.err.println(name + " interrupted: " + e.getMessage());
                break;
            }
        }
    }
}

class MyThread extends Thread {
    private int start;
    private int end;
    
    public MyThread(String name, int start, int end) {
        super(name);
        this.start = start;
        this.end = end;
    }
    
    @Override
    public void run() {
        for (int i = start; i <= end; i++) {
            System.out.println(getName() + ": " + i);
            try {
                Thread.sleep(100);
            } catch (InterruptedException e) {
                System.err.println(getName() + " interrupted: " + e.getMessage());
                break;
            }
        }
    }
}

class Counter {
    private int count = 0;
    
    public void increment() {
        count++;
    }
    
    public synchronized void synchronizedIncrement() {
        count++;
    }
    
    public int getCount() {
        return count;
    }
    
    public void reset() {
        count = 0;
    }
}

class CounterIncrementer implements Runnable {
    private Counter counter;
    private boolean synchronizedMethod;
    private int increments;
    
    public CounterIncrementer(Counter counter, boolean synchronizedMethod, int increments) {
        this.counter = counter;
        this.synchronizedMethod = synchronizedMethod;
        this.increments = increments;
    }
    
    @Override
    public void run() {
        for (int i = 0; i < increments; i++) {
            if (synchronizedMethod) {
                counter.synchronizedIncrement();
            } else {
                counter.increment();
            }
        }
    }
}

class ThreadMessageQueue {
    private java.util.Queue<String> messages = new java.util.LinkedList<>();
    
    public synchronized void put(String message) {
        messages.offer(message);
        notifyAll();
    }
    
    public synchronized String take() throws InterruptedException {
        while (messages.isEmpty()) {
            wait();
        }
        return messages.poll();
    }
}

class MessageProducer implements Runnable {
    private ThreadMessageQueue queue;
    
    public MessageProducer(ThreadMessageQueue queue) {
        this.queue = queue;
    }
    
    @Override
    public void run() {
        try {
            for (int i = 1; i <= 10; i++) {
                String message = "Message " + i;
                queue.put(message);
                System.out.println(Thread.currentThread().getName() + " produced: " + message);
                Thread.sleep(200);
            }
        } catch (InterruptedException e) {
            System.err.println(Thread.currentThread().getName() + " interrupted: " + e.getMessage());
        }
    }
}

class MessageConsumer implements Runnable {
    private ThreadMessageQueue queue;
    
    public MessageConsumer(ThreadMessageQueue queue) {
        this.queue = queue;
    }
    
    @Override
    public void run() {
        try {
            while (!Thread.currentThread().isInterrupted()) {
                String message = queue.take();
                System.out.println(Thread.currentThread().getName() + " consumed: " + message);
                Thread.sleep(300);
            }
        } catch (InterruptedException e) {
            System.err.println(Thread.currentThread().getName() + " interrupted: " + e.getMessage());
        }
    }
}

class ThreadPoolTask implements Runnable {
    private int id;
    private String name;
    
    public ThreadPoolTask(int id, String name) {
        this.id = id;
        this.name = name;
    }
    
    @Override
    public void run() {
        System.out.println("Executing " + name + " on " + Thread.currentThread().getName());
        try {
            Thread.sleep(500); // Simulate work
        } catch (InterruptedException e) {
            System.err.println(name + " interrupted: " + e.getMessage());
        }
        System.out.println(name + " completed");
    }
}

// Simple Connection Pool Implementation
class ThreadPool {
    private java.util.concurrent.BlockingQueue<Runnable> taskQueue;
    private Thread[] workers;
    private volatile boolean isRunning;
    
    public ThreadPool(int size) {
        this.taskQueue = new java.util.concurrent.LinkedBlockingQueue<>();
        this.workers = new Thread[size];
        this.isRunning = true;
        
        // Create worker threads
        for (int i = 0; i < size; i++) {
            workers[i] = new Thread(new Worker(), "Worker-" + (i + 1));
            workers[i].start();
        }
    }
    
    public void submit(Runnable task) {
        if (isRunning) {
            taskQueue.offer(task);
        }
    }
    
    public void shutdown() {
        isRunning = false;
        for (Thread worker : workers) {
            worker.interrupt();
        }
    }
    
    private class Worker implements Runnable {
        @Override
        public void run() {
            while (isRunning) {
                try {
                    Runnable task = taskQueue.take();
                    task.run();
                } catch (InterruptedException e) {
                    // Thread interrupted during shutdown
                }
            }
        }
    }
}

class Buffer {
    private java.util.Queue<Integer> buffer;
    private int capacity;
    
    public Buffer(int capacity) {
        this.buffer = new java.util.LinkedList<>();
        this.capacity = capacity;
    }
    
    public synchronized void put(int item) throws InterruptedException {
        while (buffer.size() >= capacity) {
            wait();
        }
        buffer.offer(item);
        System.out.println("Produced: " + item + ", Buffer size: " + buffer.size());
        notifyAll();
    }
    
    public synchronized int take() throws InterruptedException {
        while (buffer.isEmpty()) {
            wait();
        }
        int item = buffer.poll();
        System.out.println("Consumed: " + item + ", Buffer size: " + buffer.size());
        notifyAll();
        return item;
    }
}

class Producer implements Runnable {
    private Buffer buffer;
    
    public Producer(Buffer buffer) {
        this.buffer = buffer;
    }
    
    @Override
    public void run() {
        try {
            for (int i = 1; i <= 20; i++) {
                buffer.put(i);
                Thread.sleep(100);
            }
        } catch (InterruptedException e) {
            System.err.println("Producer interrupted: " + e.getMessage());
        }
    }
}

class Consumer implements Runnable {
    private Buffer buffer;
    
    public Consumer(Buffer buffer) {
        this.buffer = buffer;
    }
    
    @Override
    public void run() {
        try {
            while (!Thread.currentThread().isInterrupted()) {
                buffer.take();
                Thread.sleep(150);
            }
        } catch (InterruptedException e) {
            System.err.println("Consumer interrupted: " + e.getMessage());
        }
    }
}

class UnsafeList {
    private java.util.List<Integer> list = new java.util.ArrayList<>();
    
    public void add(int value) {
        list.add(value);
    }
    
    public int size() {
        return list.size();
    }
}

class SafeList {
    private java.util.List<Integer> list = new java.util.ArrayList<>();
    
    public synchronized void add(int value) {
        list.add(value);
    }
    
    public synchronized int size() {
        return list.size();
    }
}

class ListAdder implements Runnable {
    private Object list;
    private int value;
    
    public ListAdder(Object list, int value) {
        this.list = list;
        this.value = value;
    }
    
    @Override
    public void run() {
        if (list instanceof UnsafeList) {
            ((UnsafeList) list).add(value);
        } else if (list instanceof SafeList) {
            ((SafeList) list).add(value);
        }
    }
}
