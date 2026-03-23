# Concurrency Examples in Ruby
# Demonstrating concurrent programming patterns

require 'thread'
require 'concurrent-ruby'

class ConcurrencyExamples
  def initialize
    @examples = []
  end
  
  def start_examples
    puts "⚡ Concurrency Examples in Ruby"
    puts "=============================="
    puts "Explore concurrent programming patterns!"
    puts ""
    
    interactive_menu
  end
  
  def interactive_menu
    loop do
      puts "\n📋 Concurrency Examples Menu:"
      puts "1. Thread Basics"
      puts "2. Thread Synchronization"
      puts "3. Thread Pool"
      puts "4. Producer-Consumer"
      puts "5. Concurrent Collections"
      puts "6. Async/Await Patterns"
      puts "7. Parallel Processing"
      puts "8. View All Examples"
      puts "0. Exit"
      
      print "Choose an example (0-8): "
      choice = gets.chomp.to_i
      
      case choice
      when 1
        thread_basics
      when 2
        thread_synchronization
      when 3
        thread_pool
      when 4
        producer_consumer
      when 5
        concurrent_collections
      when 6
        async_await_patterns
      when 7
        parallel_processing
      when 8
        show_all_examples
      when 0
        break
      else
        puts "Invalid choice. Please try again."
      end
    end
  end
  
  def thread_basics
    puts "\n🧵 Example 1: Thread Basics"
    puts "=" * 50
    puts "Understanding Ruby threading fundamentals."
    puts ""
    
    # Basic thread creation
    puts "🧵 Basic Thread Creation:"
    
    def simple_thread_example
      threads = []
      
      # Create multiple threads
      5.times do |i|
        thread = Thread.new do
          puts "Thread #{i} started"
          sleep(1)
          puts "Thread #{i} finished"
        end
        threads << thread
      end
      
      # Wait for all threads to complete
      threads.each(&:join)
      puts "All threads completed"
    end
    
    # Thread with return value
    puts "\n🔄 Thread with Return Value:"
    
    def thread_with_return
      thread = Thread.new do
        # Simulate some work
        sleep(0.5)
        42 * 2
      end
      
      # Get the return value
      result = thread.value
      puts "Thread returned: #{result}"
    end
    
    # Thread with parameters
    puts "\n📋 Thread with Parameters:"
    
    def thread_with_params
      threads = []
      
      data = [10, 20, 30, 40, 50]
      
      data.each do |value|
        thread = Thread.new(value) do |num|
          result = num * num
          puts "Processing #{num}: #{num}^2 = #{result}"
          result
        end
        threads << thread
      end
      
      # Collect results
      results = threads.map(&:value)
      puts "All results: #{results}"
    end
    
    # Demonstrate the examples
    puts "Running thread examples..."
    
    simple_thread_example
    thread_with_return
    thread_with_params
    
    @examples << {
      title: "Thread Basics",
      description: "Fundamental Ruby threading concepts",
      code: <<~RUBY
        thread = Thread.new do
          puts "Thread running"
          sleep(1)
          42
        end
        
        result = thread.value
        thread.join
      RUBY
    }
    
    puts "\n✅ Thread Basics example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def thread_synchronization
    puts "\n🔒 Example 2: Thread Synchronization"
    puts "=" * 50
    puts "Synchronizing access to shared resources."
    puts ""
    
    # Mutex example
    puts "🔒 Mutex Synchronization:"
    
    class Counter
      def initialize
        @count = 0
        @mutex = Mutex.new
      end
      
      def increment
        @mutex.synchronize do
          @count += 1
          puts "Count incremented to #{@count} by #{Thread.current.object_id}"
        end
      end
      
      def count
        @mutex.synchronize { @count }
      end
    end
    
    def mutex_example
      counter = Counter.new
      threads = []
      
      # Create multiple threads that increment counter
      10.times do |i|
        thread = Thread.new do
          5.times { counter.increment }
        end
        threads << thread
      end
      
      threads.each(&:join)
      puts "Final count: #{counter.count}"
    end
    
    # Condition variable example
    puts "\n⏳ Condition Variable Example:"
    
    class Buffer
      def initialize(size)
        @buffer = []
        @size = size
        @mutex = Mutex.new
        @not_full = ConditionVariable.new
        @not_empty = ConditionVariable.new
      end
      
      def produce(item)
        @mutex.synchronize do
          @not_full.wait(@mutex) while @buffer.size >= @size
          @buffer << item
          puts "Produced: #{item}, Buffer size: #{@buffer.size}"
          @not_empty.signal
        end
      end
      
      def consume
        @mutex.synchronize do
          @not_empty.wait(@mutex) while @buffer.empty?
          item = @buffer.shift
          puts "Consumed: #{item}, Buffer size: #{@buffer.size}"
          @not_full.signal
          item
        end
      end
    end
    
    def condition_variable_example
      buffer = Buffer.new(5)
      
      producer = Thread.new do
        10.times do |i|
          buffer.produce("Item #{i}")
          sleep(0.1)
        end
      end
      
      consumer = Thread.new do
        10.times do
          buffer.consume
          sleep(0.2)
        end
      end
      
      [producer, consumer].each(&:join)
    end
    
    # Demonstrate synchronization examples
    puts "Running synchronization examples..."
    
    mutex_example
    condition_variable_example
    
    @examples << {
      title: "Thread Synchronization",
      description: "Mutex and condition variables for thread safety",
      code: <<~RUBY
        class Counter
          def initialize
            @count = 0
            @mutex = Mutex.new
          end
          
          def increment
            @mutex.synchronize { @count += 1 }
          end
        end
      RUBY
    }
    
    puts "\n✅ Thread Synchronization example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def thread_pool
    puts "\n🏊 Example 3: Thread Pool"
    puts "=" * 50
    puts "Managing a pool of worker threads."
    puts ""
    
    # Simple thread pool implementation
    puts "🏊 Simple Thread Pool:"
    
    class ThreadPool
      def initialize(size)
        @size = size
        @queue = Queue.new
        @threads = []
        @shutdown = false
        
        @size.times do |i|
          @threads << Thread.new do
            thread_loop(i)
          end
        end
      end
      
      def submit(&block)
        raise "Thread pool is shutdown" if @shutdown
        @queue.push(block)
      end
      
      def shutdown
        @shutdown = true
        @threads.each(&:join)
      end
      
      private
      
      def thread_loop(thread_id)
        loop do
          task = @queue.pop
          break if @shutdown && task.nil?
          
          begin
            puts "Thread #{thread_id} executing task"
            task.call
          rescue => e
            puts "Thread #{thread_id} error: #{e.message}"
          end
        end
      end
    end
    
    # Advanced thread pool with futures
    puts "\n🔮 Thread Pool with Futures:"
    
    class Future
      def initialize(thread_pool, &block)
        @thread_pool = thread_pool
        @block = block
        @mutex = Mutex.new
        @condition = ConditionVariable.new
        @value = nil
        @exception = nil
        @completed = false
        
        @thread_pool.submit { compute_value }
      end
      
      def value
        @mutex.synchronize do
          @condition.wait(@mutex) until @completed
          raise @exception if @exception
          @value
        end
      end
      
      def completed?
        @mutex.synchronize { @completed }
      end
      
      private
      
      def compute_value
        begin
          @value = @block.call
        rescue => e
          @exception = e
        ensure
          @mutex.synchronize do
            @completed = true
            @condition.broadcast
          end
        end
      end
    end
    
    def thread_pool_examples
      # Basic thread pool
      pool = ThreadPool.new(3)
      
      # Submit tasks
      10.times do |i|
        pool.submit do
          puts "Task #{i} running"
          sleep(0.1)
          puts "Task #{i} completed"
        end
      end
      
      sleep(2)
      pool.shutdown
      
      # Thread pool with futures
      future_pool = ThreadPool.new(2)
      
      futures = []
      5.times do |i|
        future = Future.new(future_pool) do
          sleep(0.5)
          i * i
        end
        futures << future
      end
      
      # Get results
      results = futures.map(&:value)
      puts "Future results: #{results}"
      
      future_pool.shutdown
    end
    
    # Demonstrate thread pool examples
    puts "Running thread pool examples..."
    
    thread_pool_examples
    
    @examples << {
      title: "Thread Pool",
      description: "Managing a pool of worker threads efficiently",
      code: <<~RUBY
        class ThreadPool
          def initialize(size)
            @queue = Queue.new
            size.times { @threads << Thread.new { thread_loop } }
          end
          
          def submit(&block)
            @queue.push(block)
          end
        end
      RUBY
    }
    
    puts "\n✅ Thread Pool example completed!"
    puts "Press Enter to continue..."
    gets
  end
  
  def show_all_examples
    puts "\n📚 All Concurrency Examples"
    puts "=" * 50
    
    @examples.each_with_index do |example, index|
      puts "\n#{index + 1}. #{example[:title]}"
      puts "   Description: #{example[:description]}"
    end
    
    puts "\nTotal examples: #{@examples.length}"
    puts "All examples demonstrate different concurrency patterns!"
  end
end

if __FILE__ == $0
  examples = ConcurrencyExamples.new
  examples.start_examples
end
