# Monitoring and Logging in Microservices
# Comprehensive guide to observability and monitoring patterns

## 🎯 Overview

Monitoring and logging are critical for maintaining microservices health and performance. This guide covers distributed logging, metrics collection, tracing, and alerting patterns in Ruby microservices.

## 📝 Distributed Logging

### 1. Structured Logging

Structured logging with consistent format across services:

```ruby
# Structured logger implementation
class StructuredLogger
  def initialize(service_name, level = :info)
    @service_name = service_name
    @level = level
    @outputs = []
    @context = {}
    @mutex = Mutex.new
  end
  
  def add_output(output)
    @outputs << output
  end
  
  def with_context(context)
    @mutex.synchronize do
      old_context = @context.dup
      @context.merge!(context)
      yield
    ensure
      @mutex.synchronize { @context = old_context }
    end
  end
  
  def debug(message, data = {})
    log(:debug, message, data)
  end
  
  def info(message, data = {})
    log(:info, message, data)
  end
  
  def warn(message, data = {})
    log(:warn, message, data)
  end
  
  def error(message, data = {})
    log(:error, message, data)
  end
  
  def fatal(message, data = {})
    log(:fatal, message, data)
  end
  
  private
  
  def log(level, message, data)
    return unless should_log?(level)
    
    @mutex.synchronize do
      log_entry = build_log_entry(level, message, data)
      
      @outputs.each do |output|
        output.write(log_entry)
      end
    end
  end
  
  def build_log_entry(level, message, data)
    {
      timestamp: Time.now.iso8601,
      service: @service_name,
      level: level.to_s.upcase,
      message: message,
      context: @context.dup,
      data: data,
      thread_id: Thread.current.object_id,
      process_id: Process.pid
    }
  end
  
  def should_log?(level)
    level_priorities = {
      debug: 0,
      info: 1,
      warn: 2,
      error: 3,
      fatal: 4
    }
    
    level_priorities[level] >= level_priorities[@level]
  end
end

# Log output implementations
class ConsoleOutput
  def write(log_entry)
    puts format_log_entry(log_entry)
  end
  
  private
  
  def format_log_entry(entry)
    "#{entry[:timestamp]} [#{entry[:level]}] #{entry[:service]} - #{entry[:message]} #{format_data(entry)}"
  end
  
  def format_data(entry)
    data_parts = []
    data_parts << "thread=#{entry[:thread_id]}" if entry[:thread_id]
    data_parts << "context=#{entry[:context]}" unless entry[:context].empty?
    data_parts << "data=#{entry[:data]}" unless entry[:data].empty?
    data_parts.join(' ')
  end
end

class JsonOutput
  def write(log_entry)
    puts log_entry.to_json
  end
end

class FileOutput
  def initialize(filename)
    @filename = filename
    @mutex = Mutex.new
  end
  
  def write(log_entry)
    @mutex.synchronize do
      File.open(@filename, 'a') do |file|
        file.puts(log_entry.to_json)
      end
    end
  end
end

# Usage
logger = StructuredLogger.new('user-service')
logger.add_output(ConsoleOutput.new)
logger.add_output(JsonOutput.new)
logger.add_output(FileOutput.new('user-service.log'))

# Basic logging
logger.info("User created", user_id: 123, email: "user@example.com")

# Context-based logging
logger.with_context(request_id: "req_123", user_id: 123) do
  logger.info("Processing user request")
  logger.debug("Validating user data")
  logger.info("User request processed successfully")
end

# Error logging
begin
  # Some operation that might fail
  raise "Database connection failed"
rescue => e
  logger.error("Failed to process user", error: e.message, backtrace: e.backtrace.first(5))
end
```

### 2. Correlation ID Logging

Track requests across multiple services:

```ruby
# Correlation ID middleware
class CorrelationIdMiddleware
  def initialize(app, logger)
    @app = app
    @logger = logger
  end
  
  def call(env)
    # Extract or generate correlation ID
    correlation_id = extract_correlation_id(env) || generate_correlation_id
    
    # Store in thread-local storage
    Thread.current[:correlation_id] = correlation_id
    
    # Add to logger context
    @logger.with_context(correlation_id: correlation_id) do
      response = @app.call(env)
      
      # Add correlation ID to response headers
      response[1]['X-Correlation-ID'] = correlation_id
      response
    end
  ensure
    Thread.current[:correlation_id] = nil
  end
  
  private
  
  def extract_correlation_id(env)
    env['HTTP_X_CORRELATION_ID'] || env['HTTP_X_REQUEST_ID']
  end
  
  def generate_correlation_id
    "req_#{Time.now.to_f}_#{SecureRandom.hex(4)}"
  end
end

# Correlation ID helper
module CorrelationIdHelper
  def current_correlation_id
    Thread.current[:correlation_id]
  end
  
  def with_correlation_id(correlation_id)
    old_id = Thread.current[:correlation_id]
    Thread.current[:correlation_id] = correlation_id
    
    yield
  ensure
    Thread.current[:correlation_id] = old_id
  end
end

# Enhanced logger with correlation ID
class CorrelationAwareLogger < StructuredLogger
  include CorrelationIdHelper
  
  def build_log_entry(level, message, data)
    entry = super(level, message, data)
    entry[:correlation_id] = current_correlation_id
    entry
  end
end

# Usage in service
class UserService
  def initialize(logger)
    @logger = logger
  end
  
  def create_user(user_data)
    correlation_id = current_correlation_id || generate_correlation_id
    
    @logger.with_context(correlation_id: correlation_id) do
      @logger.info("Starting user creation")
      
      user = nil
      begin
        user = User.create!(user_data)
        @logger.info("User created successfully", user_id: user.id)
      rescue => e
        @logger.error("Failed to create user", error: e.message)
        raise
      end
      
      user
    end
  end
  
  private
  
  def generate_correlation_id
    "usr_#{Time.now.to_f}_#{SecureRandom.hex(4)}"
  end
end
```

### 3. Log Aggregation

Centralized log collection and processing:

```ruby
# Log aggregator
class LogAggregator
  def initialize
    @logs = []
    @subscribers = []
    @mutex = Mutex.new
  end
  
  def ingest(log_entry)
    @mutex.synchronize do
      @logs << log_entry
      
      # Keep only last 10000 logs to prevent memory issues
      @logs.shift if @logs.length > 10000
      
      # Notify subscribers
      @subscribers.each { |subscriber| subscriber.call(log_entry) }
    end
  end
  
  def subscribe(&block)
    @subscribers << block
  end
  
  def query(filters = {})
    @mutex.synchronize do
      filtered_logs = @logs.dup
      
      filters.each do |key, value|
        case key
        when :service
          filtered_logs = filtered_logs.select { |log| log[:service] == value }
        when :level
          filtered_logs = filtered_logs.select { |log| log[:level] == value.to_s.upcase }
        when :time_range
          start_time, end_time = value
          filtered_logs = filtered_logs.select do |log|
            log_time = Time.parse(log[:timestamp])
            log_time >= start_time && log_time <= end_time
          end
        when :correlation_id
          filtered_logs = filtered_logs.select { |log| log[:correlation_id] == value }
        end
      end
      
      filtered_logs
    end
  end
  
  def get_log_stats
    @mutex.synchronize do
      {
        total_logs: @logs.length,
        services: @logs.map { |log| log[:service] }.uniq,
        levels: @logs.map { |log| log[:level] }.uniq,
        time_range: {
          earliest: @logs.first&.dig(:timestamp),
          latest: @logs.last&.dig(:timestamp)
        }
      }
    end
  end
end

# Log shipper
class LogShipper
  def initialize(log_aggregator, config)
    @aggregator = log_aggregator
    @config = config
    @shipping_thread = nil
  end
  
  def start
    @shipping_thread = Thread.new do
      loop do
        ship_logs
        sleep(@config[:shipping_interval] || 60)
      end
    end
  end
  
  def stop
    @shipping_thread&.kill
  end
  
  private
  
  def ship_logs
    # Get logs to ship
    logs = @aggregator.query(time_range: [
      Time.now - @config[:shipping_interval],
      Time.now
    ])
    
    return if logs.empty?
    
    # Ship to external system
    ship_to_external_system(logs)
  end
  
  def ship_to_external_system(logs)
    # Implementation depends on external system
    puts "Shipping #{logs.length} logs to external system"
  end
end

# Usage
aggregator = LogAggregator.new

# Subscribe to logs for real-time processing
aggregator.subscribe do |log_entry|
  if log_entry[:level] == 'ERROR'
    alert_on_error(log_entry)
  end
end

# Start log shipper
shipper = LogShipper.new(aggregator, shipping_interval: 30)
shipper.start

# Ingest logs from different services
user_service_logs = [
  { timestamp: Time.now.iso8601, service: 'user-service', level: 'INFO', message: 'User created' },
  { timestamp: Time.now.iso8601, service: 'user-service', level: 'ERROR', message: 'Database failed' }
]

order_service_logs = [
  { timestamp: Time.now.iso8601, service: 'order-service', level: 'INFO', message: 'Order processed' }
]

user_service_logs.each { |log| aggregator.ingest(log) }
order_service_logs.each { |log| aggregator.ingest(log) }

# Query logs
error_logs = aggregator.query(level: 'ERROR')
puts "Found #{error_logs.length} error logs"

# Get stats
stats = aggregator.get_log_stats
puts "Log stats: #{stats}"
```

## 📊 Metrics Collection

### 1. Metrics Registry

Centralized metrics collection:

```ruby
# Metrics registry
class MetricsRegistry
  def initialize
    @counters = {}
    @gauges = {}
    @histograms = {}
    @timers = {}
    @mutex = Mutex.new
  end
  
  def counter(name)
    @mutex.synchronize do
      @counters[name] ||= Counter.new(name)
    end
  end
  
  def gauge(name, &block)
    @mutex.synchronize do
      @gauges[name] ||= Gauge.new(name, &block)
    end
  end
  
  def histogram(name, buckets = nil)
    @mutex.synchronize do
      @histograms[name] ||= Histogram.new(name, buckets)
    end
  end
  
  def timer(name)
    @mutex.synchronize do
      @timers[name] ||= Timer.new(name)
    end
  end
  
  def get_all_metrics
    @mutex.synchronize do
      {
        counters: @counters.transform_values(&:to_hash),
        gauges: @gauges.transform_values(&:to_hash),
        histograms: @histograms.transform_values(&:to_hash),
        timers: @timers.transform_values(&:to_hash)
      }
    end
  end
  
  def reset
    @mutex.synchronize do
      @counters.each_value(&:reset)
      @timers.each_value(&:reset)
      @histograms.each_value(&:reset)
    end
  end
end

# Counter metric
class Counter
  def initialize(name)
    @name = name
    @value = 0
    @mutex = Mutex.new
  end
  
  def increment(amount = 1)
    @mutex.synchronize { @value += amount }
  end
  
  def value
    @mutex.synchronize { @value }
  end
  
  def reset
    @mutex.synchronize { @value = 0 }
  end
  
  def to_hash
    { name: @name, type: 'counter', value: value }
  end
end

# Gauge metric
class Gauge
  def initialize(name, &block)
    @name = name
    @block = block
  end
  
  def value
    @block ? @block.call : 0
  end
  
  def to_hash
    { name: @name, type: 'gauge', value: value }
  end
end

# Histogram metric
class Histogram
  def initialize(name, buckets = nil)
    @name = name
    @buckets = buckets || default_buckets
    @counts = Hash.new(0)
    @sum = 0
    @count = 0
    @mutex = Mutex.new
  end
  
  def observe(value)
    @mutex.synchronize do
      @sum += value
      @count += 1
      
      # Find appropriate bucket
      bucket = @buckets.find { |b| value <= b } || Float::INFINITY
      @counts[bucket] += 1
    end
  end
  
  def value
    @mutex.synchronize do
      {
        count: @count,
        sum: @sum,
        average: @count > 0 ? @sum.to_f / @count : 0,
        buckets: @counts
      }
    end
  end
  
  def reset
    @mutex.synchronize do
      @counts.clear
      @sum = 0
      @count = 0
    end
  end
  
  def to_hash
    { name: @name, type: 'histogram' }.merge(value)
  end
  
  private
  
  def default_buckets
    [0.1, 0.5, 1.0, 2.5, 5.0, 10.0, 25.0, 50.0, 100.0, 250.0, 500.0, 1000.0, Float::INFINITY]
  end
end

# Timer metric
class Timer
  def initialize(name)
    @name = name
    @histogram = Histogram.new(name)
  end
  
  def time(&block)
    start_time = Time.now
    result = yield
    duration = (Time.now - start_time) * 1000  # Convert to milliseconds
    @histogram.observe(duration)
    result
  end
  
  def observe(duration_ms)
    @histogram.observe(duration_ms)
  end
  
  def value
    @histogram.value
  end
  
  def reset
    @histogram.reset
  end
  
  def to_hash
    { name: @name, type: 'timer' }.merge(@histogram.to_hash)
  end
end

# Usage
metrics = MetricsRegistry.new

# Counters
request_counter = metrics.counter('http_requests_total')
error_counter = metrics.counter('http_errors_total')

# Gauges
active_connections = metrics.gauge('active_connections') { ConnectionPool.active_connections }
memory_usage = metrics.gauge('memory_usage') { get_memory_usage }

# Histograms
request_duration = metrics.histogram('http_request_duration_ms')

# Timers
database_timer = metrics.timer('database_query_time')

# Instrument code
def handle_request
  request_counter.increment
  
  begin
    request_duration.time do
      # Process request
      process_request_logic
    end
  rescue => e
    error_counter.increment
    raise
  end
end

def process_request_logic
  database_timer.time do
    # Database operation
    sleep(0.1)
    "Response data"
  end
end

# Get metrics
all_metrics = metrics.get_all_metrics
puts "Metrics: #{all_metrics}"
```

### 2. Metrics Exporter

Export metrics to monitoring systems:

```ruby
# Prometheus metrics exporter
class PrometheusExporter
  def initialize(metrics_registry)
    @registry = metrics_registry
  end
  
  def export
    metrics = @registry.get_all_metrics
    prometheus_format = []
    
    # Export counters
    metrics[:counters].each do |name, data|
      prometheus_format << "#{name} #{data[:value]}"
    end
    
    # Export gauges
    metrics[:gauges].each do |name, data|
      prometheus_format << "#{name} #{data[:value]}"
    end
    
    # Export histograms
    metrics[:histograms].each do |name, data|
      prometheus_format << format_histogram(name, data)
    end
    
    # Export timers
    metrics[:timers].each do |name, data|
      prometheus_format << format_histogram(name, data)
    end
    
    prometheus_format.join("\n")
  end
  
  def start_http_server(port = 9090)
    server = TCPServer.new('0.0.0.0', port)
    puts "Prometheus metrics server started on port #{port}"
    
    loop do
      client = server.accept
      Thread.new { handle_metrics_request(client) }
    end
  end
  
  private
  
  def format_histogram(name, data)
    lines = []
    
    # Count and sum
    lines << "#{name}_count #{data[:count]}"
    lines << "#{name}_sum #{data[:sum]}"
    
    # Buckets
    data[:buckets].each do |bucket, count|
      bucket_label = bucket == Float::INFINITY ? 'le_+Inf' : "le_#{bucket}"
      lines << "#{name}_bucket{#{bucket_label}} #{count}"
    end
    
    lines.join("\n")
  end
  
  def handle_metrics_request(client)
    request_line = client.gets
    return unless request_line
    
    method, path, version = request_line.split(' ')
    
    if method == 'GET' && path == '/metrics'
      response = export_metrics
      
      client.print "HTTP/1.1 200 OK\r\n"
      client.print "Content-Type: text/plain\r\n"
      client.print "Content-Length: #{response.length}\r\n"
      client.print "\r\n"
      client.print response
    else
      client.print "HTTP/1.1 404 Not Found\r\n\r\n"
    end
    
    client.close
  end
end

# Usage
metrics = MetricsRegistry.new

# Add some metrics
request_counter = metrics.counter('http_requests_total')
request_counter.increment(100)

error_counter = metrics.counter('http_errors_total')
error_counter.increment(5)

request_duration = metrics.histogram('http_request_duration_ms')
10.times { request_duration.observe(rand(10..100)) }

# Export metrics
exporter = PrometheusExporter.new(metrics)
prometheus_metrics = exporter.export
puts "Prometheus metrics:\n#{prometheus_metrics}"

# Start HTTP server for Prometheus scraping
exporter_thread = Thread.new { exporter.start_http_server }
```

## 🔍 Distributed Tracing

### 1. Basic Tracing

Track requests across service boundaries:

```ruby
# Span representation
class Span
  attr_reader :trace_id, :span_id, :parent_span_id, :operation_name, :start_time, :end_time, :tags, :logs
  
  def initialize(trace_id, span_id, operation_name, parent_span_id = nil)
    @trace_id = trace_id
    @span_id = span_id
    @parent_span_id = parent_span_id
    @operation_name = operation_name
    @start_time = Time.now
    @end_time = nil
    @tags = {}
    @logs = []
  end
  
  def finish
    @end_time = Time.now
  end
  
  def duration
    return nil unless @end_time
    (@end_time - @start_time) * 1000  # Convert to milliseconds
  end
  
  def set_tag(key, value)
    @tags[key] = value
  end
  
  def log(message, timestamp = Time.now)
    @logs << { timestamp: timestamp.iso8601, message: message }
  end
  
  def to_hash
    {
      trace_id: @trace_id,
      span_id: @span_id,
      parent_span_id: @parent_span_id,
      operation_name: @operation_name,
      start_time: @start_time.iso8601,
      end_time: @end_time&.iso8601,
      duration_ms: duration,
      tags: @tags,
      logs: @logs
    }
  end
end

# Tracer
class Tracer
  def initialize(service_name, span_collector = nil)
    @service_name = service_name
    @span_collector = span_collector
    @current_span = nil
  end
  
  def start_span(operation_name, parent_span = nil)
    trace_id = parent_span ? parent_span.trace_id : generate_trace_id
    span_id = generate_span_id
    parent_span_id = parent_span ? parent_span.span_id : nil
    
    span = Span.new(trace_id, span_id, operation_name, parent_span_id)
    span.set_tag('service.name', @service_name)
    
    @current_span = span
    
    if block_given?
      begin
        result = yield span
        span.finish
        @span_collector&.collect_span(span)
        result
      ensure
        @current_span = nil
      end
    else
      span
    end
  end
  
  def current_span
    @current_span
  end
  
  def inject_headers(headers)
    span = current_span
    return headers unless span
    
    headers['X-Trace-Id'] = span.trace_id
    headers['X-Span-Id'] = span.span_id
    headers['X-Parent-Span-Id'] = span.parent_span_id if span.parent_span_id
    
    headers
  end
  
  def extract_span(headers)
    trace_id = headers['X-Trace-Id']
    span_id = headers['X-Span-Id']
    parent_span_id = headers['X-Parent-Span-Id']
    
    return nil unless trace_id && span_id
    
    Span.new(trace_id, span_id, 'inherited', parent_span_id)
  end
  
  private
  
  def generate_trace_id
    "trace_#{Time.now.to_f}_#{SecureRandom.hex(8)}"
  end
  
  def generate_span_id
    "span_#{Time.now.to_f}_#{SecureRandom.hex(4)}"
  end
end

# Span collector
class SpanCollector
  def initialize
    @spans = []
    @mutex = Mutex.new
  end
  
  def collect_span(span)
    @mutex.synchronize do
      @spans << span.to_hash
    end
  end
  
  def get_spans(trace_id = nil)
    @mutex.synchronize do
      if trace_id
        @spans.select { |span| span[:trace_id] == trace_id }
      else
        @spans.dup
      end
    end
  end
  
  def get_trace(trace_id)
    spans = get_spans(trace_id)
    return nil if spans.empty?
    
    {
      trace_id: trace_id,
      spans: spans.sort_by { |span| span[:start_time] }
    }
  end
end

# Usage
tracer = Tracer.new('user-service')
collector = SpanCollector.new
tracer_with_collector = Tracer.new('user-service', collector)

# Create spans
tracer.start_span('create_user') do |span|
  span.set_tag('user.id', 123)
  span.log('Starting user creation')
  
  # Nested span
  tracer.start_span('validate_user_data', span) do |validation_span|
    validation_span.set_tag('validation.type', 'email')
    sleep(0.1)  # Simulate work
    validation_span.log('User data validated')
  end
  
  # Another nested span
  tracer.start_span('save_to_database', span) do |db_span|
    db_span.set_tag('database.table', 'users')
    sleep(0.2)  # Simulate database work
    db_span.log('User saved to database')
  end
  
  span.log('User creation completed')
end

# Get collected spans
spans = collector.get_spans
puts "Collected #{spans.length} spans"

# Get trace
trace = collector.get_trace(spans.first[:trace_id])
puts "Trace: #{trace}"
```

### 2. Cross-Service Tracing

Trace requests across multiple services:

```ruby
# HTTP client with tracing
class TracingHttpClient
  def initialize(base_url, tracer = nil)
    @base_url = base_url
    @tracer = tracer
  end
  
  def get(endpoint, headers = {})
    headers = inject_tracing_headers(headers)
    
    span_name = "HTTP GET #{endpoint}"
    @tracer&.start_span(span_name) do |span|
      span.set_tag('http.method', 'GET')
      span.set_tag('http.url', "#{@base_url}#{endpoint}")
      
      begin
        response = HTTP.get("#{@base_url}#{endpoint}", headers: headers)
        
        span.set_tag('http.status_code', response.code)
        span.set_tag('http.status_code', response.code)
        
        response
      rescue => e
        span.set_tag('error', true)
        span.set_tag('error.message', e.message)
        raise
      end
    end
  end
  
  def post(endpoint, data = {}, headers = {})
    headers = inject_tracing_headers(headers)
    
    span_name = "HTTP POST #{endpoint}"
    @tracer&.start_span(span_name) do |span|
      span.set_tag('http.method', 'POST')
      span.set_tag('http.url', "#{@base_url}#{endpoint}")
      
      begin
        response = HTTP.post("#{@base_url}#{endpoint}", 
                           json: data, 
                           headers: headers)
        
        span.set_tag('http.status_code', response.code)
        
        response
      rescue => e
        span.set_tag('error', true)
        span.set_tag('error.message', e.message)
        raise
      end
    end
  end
  
  private
  
  def inject_tracing_headers(headers)
    return headers unless @tracer
    
    @tracer.inject_headers(headers)
  end
end

# Middleware for tracing
class TracingMiddleware
  def initialize(app, tracer)
    @app = app
    @tracer = tracer
  end
  
  def call(env)
    # Extract incoming span context
    incoming_span = @tracer.extract_span(env)
    
    # Start new span
    operation_name = "#{env['REQUEST_METHOD']} #{env['REQUEST_PATH']}"
    
    @tracer.start_span(operation_name, incoming_span) do |span|
      # Add request tags
      span.set_tag('http.method', env['REQUEST_METHOD'])
      span.set_tag('http.url', env['REQUEST_URI'])
      span.set_tag('http.host', env['HTTP_HOST'])
      span.set_tag('http.user_agent', env['HTTP_USER_AGENT'])
      
      # Log request start
      span.log('Request started')
      
      begin
        response = @app.call(env)
        
        # Add response tags
        span.set_tag('http.status_code', response[0].to_i)
        span.log('Request completed')
        
        response
      rescue => e
        span.set_tag('error', true)
        span.set_tag('error.message', e.message)
        span.log('Request failed')
        raise
      end
    end
  end
end

# Usage in services
class UserService
  def initialize(tracer)
    @tracer = tracer
    @order_client = TracingHttpClient.new('http://order-service:3002', tracer)
  end
  
  def get_user_with_orders(user_id)
    @tracer.start_span('get_user_with_orders') do |span|
      span.set_tag('user.id', user_id)
      
      # Get user
      user = get_user_from_db(user_id)
      span.set_tag('user.found', !user.nil?)
      
      if user
        # Get user orders
        orders = @order_client.get("/users/#{user_id}/orders")
        span.set_tag('orders.count', orders.length)
        
        {
          user: user,
          orders: orders
        }
      else
        span.set_tag('error', true)
        span.set_tag('error.message', 'User not found')
        nil
      end
    end
  end
  
  private
  
  def get_user_from_db(user_id)
    # Simulate database lookup
    sleep(0.1)
    { id: user_id, name: "User #{user_id}", email: "user#{user_id}@example.com" }
  end
end

# Usage
tracer = Tracer.new('user-service')
collector = SpanCollector.new
tracer_with_collector = Tracer.new('user-service', collector)

user_service = UserService.new(tracer_with_collector)

# Make traced request
result = user_service.get_user_with_orders(123)

# Get trace
spans = collector.get_spans
trace = collector.get_trace(spans.first[:trace_id])

puts "Trace with #{trace[:spans].length} spans:"
trace[:spans].each do |span|
  puts "  #{span[:operation_name]}: #{span[:duration_ms]}ms"
end
```

## 🚨 Alerting

### 1. Alert Manager

Manage alerts based on metrics and logs:

```ruby
# Alert manager
class AlertManager
  def initialize
    @rules = []
    @alert_handlers = []
    @alert_history = []
    @mutex = Mutex.new
  end
  
  def add_rule(rule)
    @rules << rule
  end
  
  def add_alert_handler(&handler)
    @alert_handlers << handler
  end
  
  def check_metrics(metrics)
    @rules.each do |rule|
      alert = rule.check(metrics)
      if alert
        handle_alert(alert)
      end
    end
  end
  
  def check_logs(logs)
    error_logs = logs.select { |log| log[:level] == 'ERROR' }
    
    if error_logs.length > 10
      alert = Alert.new(
        'high_error_rate',
        'High error rate detected',
        'critical',
        { error_count: error_logs.length, time_window: '1 minute' }
      )
      handle_alert(alert)
    end
  end
  
  private
  
  def handle_alert(alert)
    @mutex.synchronize do
      @alert_history << {
        alert: alert.to_hash,
        timestamp: Time.now.iso8601
      }
      
      # Keep only last 1000 alerts
      @alert_history.shift if @alert_history.length > 1000
    end
    
    # Notify handlers
    @alert_handlers.each { |handler| handler.call(alert) }
  end
end

# Alert rule
class AlertRule
  def initialize(name, condition, severity = 'warning', message = nil)
    @name = name
    @condition = condition
    @severity = severity
    @message = message || "Alert: #{name}"
  end
  
  def check(metrics)
    if @condition.call(metrics)
      Alert.new(@name, @message, @severity, metrics)
    end
  end
end

# Alert
class Alert
  attr_reader :name, :message, :severity, :data
  
  def initialize(name, message, severity, data)
    @name = name
    @message = message
    @severity = severity
    @data = data
  end
  
  def to_hash
    {
      name: @name,
      message: @message,
      severity: @severity,
      data: @data,
      timestamp: Time.now.iso8601
    }
  end
end

# Alert handlers
class ConsoleAlertHandler
  def call(alert)
    puts "ALERT [#{alert.severity.upcase}] #{alert.name}: #{alert.message}"
    puts "Data: #{alert.data}"
  end
end

class EmailAlertHandler
  def initialize(smtp_config)
    @smtp_config = smtp_config
  end
  
  def call(alert)
    # Send email alert
    puts "Sending email alert for #{alert.name}"
    # Implementation would use SMTP library
  end
end

class SlackAlertHandler
  def initialize(webhook_url)
    @webhook_url = webhook_url
  end
  
  def call(alert)
    # Send Slack notification
    puts "Sending Slack alert for #{alert.name}"
    # Implementation would send HTTP POST to Slack webhook
  end
end

# Usage
alert_manager = AlertManager.new

# Add alert handlers
alert_manager.add_alert_handler(&ConsoleAlertHandler.new.method(:call))
alert_manager.add_alert_handler(&EmailAlertHandler.new({}).method(:call))
alert_manager.add_alert_handler(&SlackAlertHandler.new('https://hooks.slack.com/...').method(:call))

# Add alert rules
alert_manager.add_rule(
  AlertRule.new(
    'high_error_rate',
    ->(metrics) { metrics[:counters]['http_errors_total'][:value] > 10 },
    'critical',
    'High error rate detected'
  )
)

alert_manager.add_rule(
  AlertRule.new(
    'high_response_time',
    ->(metrics) { metrics[:histograms]['http_request_duration_ms'][:average] > 1000 },
    'warning',
    'High response time detected'
  )
)

# Check metrics
metrics = {
  counters: {
    'http_requests_total' => { value: 1000 },
    'http_errors_total' => { value: 15 }
  },
  histograms: {
    'http_request_duration_ms' => { average: 1200 }
  }
}

alert_manager.check_metrics(metrics)
```

## 🎯 Best Practices

### 1. Logging Best Practices

```ruby
# Logging best practices implementation
class LoggingBestPractices
  def self.log_with_context(logger, level, message, context = {})
    # Always include correlation ID
    correlation_id = Thread.current[:correlation_id] || 'no-correlation'
    
    # Always include service name
    service_name = ENV['SERVICE_NAME'] || 'unknown-service'
    
    # Always include request ID if available
    request_id = Thread.current[:request_id]
    
    # Structure the log entry
    logger.send(level, message, {
      correlation_id: correlation_id,
      service: service_name,
      request_id: request_id,
      **context
    })
  end
  
  def self.log_performance(operation, duration, context = {})
    logger = StructuredLogger.new(ENV['SERVICE_NAME'])
    
    log_with_context(logger, :info, "Performance: #{operation}", {
      operation: operation,
      duration_ms: duration,
      **context
    })
  end
  
  def self.log_error(error, context = {})
    logger = StructuredLogger.new(ENV['SERVICE_NAME'])
    
    log_with_context(logger, :error, "Error occurred", {
      error_class: error.class.name,
      error_message: error.message,
      backtrace: error.backtrace.first(5),
      **context
    })
  end
end
```

### 2. Metrics Best Practices

```ruby
# Metrics best practices
class MetricsBestPractices
  def self.instrument_method(klass, method_name, metrics_registry)
    original_method = klass.instance_method(method_name)
    
    klass.define_method(method_name) do |*args, &block|
      timer = metrics_registry.timer("#{klass.name}.#{method_name}.duration")
      
      timer.time do
        original_method.bind(self).call(*args, &block)
      end
    end
  end
  
  def self.track_business_metric(name, value, tags = {})
    metrics = MetricsRegistry.instance
    histogram = metrics.histogram(name)
    histogram.observe(value)
    
    # Add tags if provided
    tags.each { |key, val| histogram.set_tag(key, val) }
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Structured Logger**: Implement a structured logger
2. **Metrics Collection**: Create basic metrics collection
3. **Simple Tracing**: Implement basic request tracing

### Intermediate Exercises

1. **Correlation ID**: Add correlation ID tracking
2. **Metrics Exporter**: Export metrics to Prometheus
3. **Alert System**: Create an alerting system

### Advanced Exercises

1. **Distributed Tracing**: Implement cross-service tracing
2. **Log Aggregation**: Build a log aggregation system
3. **Monitoring Dashboard**: Create a monitoring dashboard

---

## 🎯 Summary

Monitoring and logging in microservices provide:

- **Structured Logging** - Consistent log formats across services
- **Distributed Tracing** - Request tracking across service boundaries
- **Metrics Collection** - Performance and business metrics
- **Alerting** - Proactive issue detection
- **Observability** - Complete system visibility

Master these patterns to build observable, maintainable microservices!
