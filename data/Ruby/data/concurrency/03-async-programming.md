# Async Programming in Ruby
# Comprehensive guide to asynchronous programming patterns

## 🎯 Overview

Asynchronous programming enables non-blocking operations, improving responsiveness and resource utilization. This guide covers async patterns, event-driven programming, and modern async frameworks in Ruby.

## 🔄 Async Programming Concepts

### 1. Event Loop Pattern

Basic event loop implementation:

```ruby
class EventLoop
  def initialize
    @queue = []
    @running = false
  end
  
  def start
    @running = true
    while @running
      process_events
      sleep(0.001)  # Prevent CPU spinning
    end
  end
  
  def stop
    @running = false
  end
  
  def schedule(&block)
    @queue << { type: :task, block: block }
  end
  
  def schedule_delayed(delay, &block)
    Thread.new do
      sleep(delay)
      @queue << { type: :task, block: block }
    end
  end
  
  def schedule_io(io, events = :read, &block)
    @queue << { type: :io, io: io, events: events, block: block }
  end
  
  private
  
  def process_events
    return if @queue.empty?
    
    event = @queue.shift
    case event[:type]
    when :task
      event[:block].call
    when :io
      handle_io_event(event)
    end
  end
  
  def handle_io_event(event)
    io = event[:io]
    events = event[:events]
    
    if events == :read && io.ready_to_read?
      event[:block].call
    elsif events == :write && io.ready_to_write?
      event[:block].call
    end
  end
end

# Usage
loop = EventLoop.new

# Schedule tasks
loop.schedule { puts "Task 1 executed" }
loop.schedule { puts "Task 2 executed" }
loop.schedule_delayed(1) { puts "Delayed task executed" }

# Start event loop (in separate thread)
event_thread = Thread.new { loop.start }

sleep(2)
loop.stop
event_thread.join
```

### 2. Promise/Future Pattern

```ruby
class Promise
  def initialize
    @state = :pending
    @value = nil
    @reason = nil
    @callbacks = []
    @mutex = Mutex.new
  end
  
  def resolve(value)
    @mutex.synchronize do
      return unless @state == :pending
      
      @state = :fulfilled
      @value = value
      execute_callbacks
    end
  end
  
  def reject(reason)
    @mutex.synchronize do
      return unless @state == :pending
      
      @state = :rejected
      @reason = reason
      execute_callbacks
    end
  end
  
  def then(&block)
    promise = Promise.new
    
    @mutex.synchronize do
      case @state
      when :fulfilled
        execute_callback(block, @value, promise)
      when :rejected
        promise.reject(@reason)
      else
        @callbacks << { callback: block, promise: promise }
      end
    end
    
    promise
  end
  
  def rescue(&block)
    promise = Promise.new
    
    @mutex.synchronize do
      case @state
      when :fulfilled
        promise.resolve(@value)
      when :rejected
        execute_callback(block, @reason, promise)
      else
        @callbacks << { callback: block, promise: promise, type: :rescue }
      end
    end
    
    promise
  end
  
  def fulfilled?
    @state == :fulfilled
  end
  
  def rejected?
    @state == :rejected
  end
  
  def pending?
    @state == :pending
  end
  
  private
  
  def execute_callbacks
    @callbacks.each do |callback|
      if callback[:type] == :rescue
        execute_callback(callback[:callback], @reason, callback[:promise])
      else
        execute_callback(callback[:callback], @value, callback[:promise])
      end
    end
    @callbacks.clear
  end
  
  def execute_callback(callback, value, promise)
    begin
      result = callback.call(value)
      promise.resolve(result)
    rescue => e
      promise.reject(e)
    end
  end
end

# Usage
def fetch_data_async(url)
  promise = Promise.new
  
  Thread.new do
    begin
      # Simulate network request
      sleep(1)
      data = "Data from #{url}"
      promise.resolve(data)
    rescue => e
      promise.reject(e)
    end
  end
  
  promise
end

# Chain promises
fetch_data_async("https://api.example.com/data")
  .then { |data| puts "Received: #{data}" }
  .then { |data| data.upcase }
  .then { |upper_data| puts "Processed: #{upper_data}" }
  .rescue { |error| puts "Error: #{error.message}" }
```

### 3. Async/Await Simulation

```ruby
module Async
  def self.async(&block)
    promise = Promise.new
    
    Thread.new do
      begin
        result = block.call
        promise.resolve(result)
      rescue => e
        promise.reject(e)
      end
    end
    
    promise
  end
  
  def self.await(promise)
    # Wait for promise to complete
    while promise.pending?
      sleep(0.01)
    end
    
    if promise.fulfilled?
      promise.instance_variable_get(:@value)
    else
      raise promise.instance_variable_get(:@reason)
    end
  end
end

# Usage examples
def fetch_user_async(user_id)
  Async.async do
    # Simulate database query
    sleep(0.5)
    { id: user_id, name: "User #{user_id}", email: "user#{user_id}@example.com" }
  end
end

def fetch_posts_async(user_id)
  Async.async do
    # Simulate API call
    sleep(0.3)
    [
      { id: 1, title: "Post 1", user_id: user_id },
      { id: 2, title: "Post 2", user_id: user_id }
    ]
  end
end

# Async function
def get_user_with_posts(user_id)
  user = Async.await(fetch_user_async(user_id))
  posts = Async.await(fetch_posts_async(user_id))
  
  {
    user: user,
    posts: posts
  }
end

# Execute
result = get_user_with_posts(123)
puts "Result: #{result}"
```

## 🌐 Async HTTP Clients

### 1. HTTP Client with EventMachine

```ruby
require 'eventmachine'
require 'em-http-request'

class AsyncHTTPClient
  def initialize
    @responses = {}
    @mutex = Mutex.new
  end
  
  def get(url, &callback)
    EM.run do
      http = EM::HttpRequest.new(url).get
      
      http.callback do
        response = {
          status: http.response_header.status,
          body: http.response,
          headers: http.response_header
        }
        
        callback.call(response) if callback
        EM.stop
      end
      
      http.errback do
        error = { error: "Request failed" }
        callback.call(error) if callback
        EM.stop
      end
    end
  end
  
  def parallel_get(urls, &callback)
    responses = {}
    completed = 0
    
    EM.run do
      urls.each_with_index do |url, index|
        http = EM::HttpRequest.new(url).get
        
        http.callback do
          responses[index] = {
            status: http.response_header.status,
            body: http.response,
            headers: http.response_header
          }
          
          completed += 1
          if completed == urls.size
            callback.call(responses) if callback
            EM.stop
          end
        end
        
        http.errback do
          responses[index] = { error: "Request failed" }
          completed += 1
          
          if completed == urls.size
            callback.call(responses) if callback
            EM.stop
          end
        end
      end
    end
  end
end

# Usage
client = AsyncHTTPClient.new

# Single request
client.get("https://jsonplaceholder.typicode.com/posts/1") do |response|
  if response[:error]
    puts "Error: #{response[:error]}"
  else
    puts "Status: #{response[:status]}"
    puts "Body: #{response[:body][0..100]}..."
  end
end

# Parallel requests
urls = [
  "https://jsonplaceholder.typicode.com/posts/1",
  "https://jsonplaceholder.typicode.com/posts/2",
  "https://jsonplaceholder.typicode.com/posts/3"
]

client.parallel_get(urls) do |responses|
  responses.each_with_index do |response, index|
    puts "URL #{index + 1}: #{response[:status] || 'Error'}"
  end
end
```

### 2. Async Database Operations

```ruby
require 'async'
require 'async/io'

class AsyncDatabase
  def initialize(connection_string)
    @connection_string = connection_string
    @pool = []
    @mutex = Mutex.new
  end
  
  def query(sql, params = [])
    Async::Task.current.async do
      connection = acquire_connection
      
      begin
        # Simulate async database query
        sleep(0.1)
        result = execute_query(connection, sql, params)
        result
      ensure
        release_connection(connection)
      end
    end
  end
  
  def transaction(&block)
    Async::Task.current.async do
      connection = acquire_connection
      
      begin
        # Simulate async transaction
        sleep(0.05)
        result = yield(connection)
        result
      ensure
        release_connection(connection)
      end
    end
  end
  
  private
  
  def acquire_connection
    @mutex.synchronize do
      if @pool.empty?
        create_connection
      else
        @pool.pop
      end
    end
  end
  
  def release_connection(connection)
    @mutex.synchronize do
      @pool.push(connection)
    end
  end
  
  def create_connection
    # Simulate connection creation
    Object.new
  end
  
  def execute_query(connection, sql, params)
    # Simulate query execution
    { rows: [{ id: 1, name: "Test" }] }
  end
end

# Usage
Async do
  db = AsyncDatabase.new("postgresql://localhost/test")
  
  # Async query
  result = db.query("SELECT * FROM users WHERE id = $1", [1])
  puts "Query result: #{result}"
  
  # Async transaction
  transaction_result = db.transaction do |connection|
    # Multiple async operations
    query1 = db.query("INSERT INTO users (name) VALUES ($1)", ["Alice"])
    query2 = db.query("INSERT INTO users (name) VALUES ($1)", ["Bob"])
    
    { query1: query1, query2: query2 }
  end
  
  puts "Transaction result: #{transaction_result}"
end
```

## 🔄 Async Frameworks

### 1. Async gem

```ruby
require 'async'
require 'async/http'

class AsyncWebCrawler
  def initialize
    @visited = Set.new
    @results = []
  end
  
  def crawl(urls, max_concurrent: 5)
    Async do
      semaphore = Async::Semaphore.new(max_concurrent)
      
      tasks = urls.map do |url|
        Async do
          semaphore.async do
            crawl_single(url)
          end
        end
      end
      
      tasks.each(&:wait)
      @results
    end
  end
  
  private
  
  def crawl_single(url)
    return nil if @visited.include?(url)
    @visited.add(url)
    
    begin
      # Async HTTP request
      response = Async::HTTP::Internet.get(URI(url))
      
      if response.success?
        content = response.read
        links = extract_links(content, url)
        
        result = {
          url: url,
          status: response.status,
          links: links
        }
        
        @results << result
        
        # Recursively crawl links
        crawl(links) if links.any?
        
        result
      else
        { url: url, error: "HTTP #{response.status}" }
      end
    rescue => e
      { url: url, error: e.message }
    end
  end
  
  def extract_links(content, base_url)
    # Simple link extraction
    content.scan(/href="([^"]+)"/).flatten.map do |link|
      URI.join(base_url, link).to_s
    end.uniq
  end
end

# Usage
crawler = AsyncWebCrawler.new
urls = [
  "https://example.com",
  "https://example.com/about",
  "https://example.com/contact"
]

results = crawler.crawl(urls, max_concurrent: 3)
puts "Crawled #{results.size} pages"
```

### 2. Concurrent Ruby with Async Patterns

```ruby
require 'concurrent-ruby'

class ConcurrentAsyncProcessor
  def initialize(size: 4)
    @executor = Concurrent::ThreadPoolExecutor.new(
      min_threads: 1,
      max_threads: size,
      max_queue: size * 10
    )
    @promises = Concurrent::Hash.new
  end
  
  def submit(&block)
    future = Concurrent::Future.execute(executor: @executor) do
      block.call
    end
    
    promise_id = SecureRandom.uuid
    @promises[promise_id] = future
    promise_id
  end
  
  def result(promise_id, timeout = nil)
    future = @promises[promise_id]
    return nil unless future
    
    if timeout
      future.value(timeout)
    else
      future.value
    end
  end
  
  def submit_with_callback(&block)
    promise_id = submit(&block)
    
    # Check for completion asynchronously
    Thread.new do
      loop do
        future = @promises[promise_id]
        break if future && future.fulfilled?
        sleep(0.01)
      end
      
      yield result(promise_id) if block_given?
    end
    
    promise_id
  end
  
  def shutdown
    @executor.shutdown
    @executor.wait_for_termination(30)
  end
end

# Usage
processor = ConcurrentAsyncProcessor.new(size: 4)

# Submit tasks
promise_ids = []
5.times do |i|
  promise_id = processor.submit do
    sleep(rand(0.1..0.5))
    "Task #{i} completed"
  end
  promise_ids << promise_id
end

# Get results
promise_ids.each do |id|
  result = processor.result(id)
  puts "Result: #{result}"
end

# Submit with callback
processor.submit_with_callback do
  sleep(0.2)
  "Async callback task"
end

processor.shutdown
```

## 🎯 Real-World Async Patterns

### 1. Async File Processing

```ruby
class AsyncFileProcessor
  def initialize
    @queue = Queue.new
    @workers = []
    @results = {}
  end
  
  def start_workers(count = 4)
    count.times do |i|
      worker = Thread.new { worker_loop(i) }
      @workers << worker
    end
  end
  
  def process_file(filename, &callback)
    task_id = SecureRandom.uuid
    @queue.push({ id: task_id, filename: filename, callback: callback })
    task_id
  end
  
  def get_result(task_id)
    @results[task_id]
  end
  
  def stop
    @workers.size.times { @queue.push({ shutdown: true }) }
    @workers.each(&:join)
  end
  
  private
  
  def worker_loop(worker_id)
    loop do
      task = @queue.pop
      break if task[:shutdown]
      
      begin
        result = process_file_async(task[:filename])
        @results[task[:id]] = result
        
        if task[:callback]
          task[:callback].call(result)
        end
      rescue => e
        @results[task[:id]] = { error: e.message }
      end
    end
  end
  
  def process_file_async(filename)
    # Simulate async file processing
    sleep(0.1)
    
    {
      filename: filename,
      size: File.size(filename),
      lines: File.readlines(filename).size,
      processed_at: Time.now
    }
  end
end

# Usage
processor = AsyncFileProcessor.new
processor.start_workers

# Process files
Dir.glob('*.rb').each do |filename|
  processor.process_file(filename) do |result|
    if result[:error]
      puts "Error processing #{filename}: #{result[:error]}"
    else
      puts "Processed #{filename}: #{result[:lines]} lines"
    end
  end
end

sleep(2)
processor.stop
```

### 2. Async Web Server

```ruby
require 'socket'
require 'json'

class AsyncWebServer
  def initialize(port = 8080)
    @port = port
    @server = TCPServer.new('localhost', @port)
    @routes = {}
    @middleware = []
  end
  
  def get(path, &handler)
    @routes["GET #{path}"] = handler
  end
  
  def post(path, &handler)
    @routes["POST #{path}"] = handler
  end
  
  def use(&middleware)
    @middleware << middleware
  end
  
  def start
    puts "Server starting on port #{@port}"
    
    loop do
      client = @server.accept_nonblock
      handle_client_async(client)
    rescue IO::WaitReadable
      IO.select([@server])
      retry
    end
  end
  
  private
  
  def handle_client_async(client)
    Thread.new do
      begin
        request = client.gets
        method, path, version = request.split(' ')
        
        # Apply middleware
        context = { method: method, path: path, client: client }
        
        @middleware.each do |middleware|
          middleware.call(context)
        end
        
        # Route handling
        route_key = "#{method} #{path}"
        handler = @routes[route_key]
        
        if handler
          response = handler.call(context)
          send_response(client, response)
        else
          send_404(client)
        end
        
      rescue => e
        send_500(client, e)
      ensure
        client.close
      end
    end
  end
  
  def send_response(client, response)
    status = response[:status] || 200
    headers = response[:headers] || {}
    body = response[:body] || ""
    
    response_lines = [
      "HTTP/1.1 #{status}",
      "Content-Type: text/plain",
      "Content-Length: #{body.length}",
      "Connection: close"
    ]
    
    headers.each { |key, value| response_lines.insert(-2, "#{key}: #{value}") }
    
    client.print(response_lines.join("\r\n") + "\r\n\r\n" + body)
  end
  
  def send_404(client)
    send_response(client, status: 404, body: "Not Found")
  end
  
  def send_500(client, error)
    send_response(client, status: 500, body: "Internal Server Error")
  end
end

# Usage
server = AsyncWebServer.new(8080)

# Add middleware for logging
server.use do |context|
  puts "#{Time.now} - #{context[:method]} #{context[:path]}"
end

# Add routes
server.get("/") do |context|
  { status: 200, body: "Hello, World!" }
end

server.get("/async") do |context|
  # Simulate async operation
  sleep(0.1)
  { status: 200, body: "Async response" }
end

server.post("/data") do |context|
  # Simulate async data processing
  sleep(0.2)
  { status: 201, body: "Data processed" }
end

# Start server (in separate thread)
server_thread = Thread.new { server.start }

sleep(5)
server_thread.kill
```

## 🎯 Best Practices

### 1. Error Handling

```ruby
class RobustAsyncProcessor
  def initialize
    @retry_count = 3
    @timeout = 30
  end
  
  def process_async(&block)
    Async::Task.current.async do
      attempts = 0
      
      begin
        Timeout.timeout(@timeout) do
          block.call
        end
      rescue Timeout::Error
        attempts += 1
        if attempts <= @retry_count
          sleep(0.1 * attempts)
          retry
        else
          { error: "Operation timed out after #{@timeout}s" }
        end
      rescue => e
        attempts += 1
        if attempts <= @retry_count
          sleep(0.1 * attempts)
          retry
        else
          { error: e.message }
        end
      end
    end
  end
end
```

### 2. Resource Management

```ruby
class ResourceManager
  def initialize
    @resources = {}
    @mutex = Mutex.new
  end
  
  def acquire(key, &block)
    @mutex.synchronize do
      @resources[key] ||= []
      @resources[key] << block
    end
  end
  
  def release(key, value)
    @mutex.synchronize do
      if @resources[key] && @resources[key].any?
        callback = @resources[key].shift
        callback.call(value)
      end
    end
  end
end
```

### 3. Backpressure Handling

```ruby
class BackpressureQueue
  def initialize(max_size: 1000)
    @queue = Queue.new
    @max_size = max_size
    @mutex = Mutex.new
    @size = 0
  end
  
  def push(item)
    @mutex.synchronize do
      while @size >= @max_size
        @mutex.sleep(0.01)
      end
      
      @queue.push(item)
      @size += 1
    end
  end
  
  def pop
    item = @queue.pop
    @mutex.synchronize { @size -= 1 }
    item
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Event Loop**: Implement a basic event loop
2. **Promise Pattern**: Create a promise/future implementation
3. **Async HTTP**: Build an async HTTP client

### Intermediate Exercises

1. **Async Database**: Create an async database wrapper
2. **Web Server**: Build an async web server
3. **File Processor**: Implement async file processing

### Advanced Exercises

1. **Message Queue**: Build an async message queue
2. **Stream Processing**: Create async stream processor
3. **Load Balancer**: Implement async load balancer

---

## 🎯 Summary

Async programming in Ruby provides:

- **Event-driven architecture** - Non-blocking I/O operations
- **Promise/Future patterns** - Composable async operations
- **Async frameworks** - Modern async programming tools
- **Resource management** - Efficient resource utilization
- **Error handling** - Robust async error management

Master async programming to build responsive, scalable Ruby applications!
