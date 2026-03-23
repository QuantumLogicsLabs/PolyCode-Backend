# Advanced Networking in Ruby

## Overview

Advanced networking in Ruby encompasses sophisticated techniques for building high-performance, scalable, and resilient network applications. This guide covers advanced concepts including distributed systems, load balancing, network optimization, and modern networking paradigms.

## Distributed Systems Architecture

### Message Queue Implementation
```ruby
require 'socket'
require 'json'
require 'thread'
require 'timeout'

class MessageQueue
  def initialize(host = 'localhost', port = 5672)
    @host = host
    @port = port
    @queues = {}
    @subscribers = {}
    @server = nil
    @running = false
    @mutex = Mutex.new
  end

  def start
    @server = TCPServer.new(@host, @port)
    @running = true
    
    puts "Message Queue Server started on #{@host}:#{@port}"
    
    Thread.new { accept_connections }
  end

  def stop
    @running = false
    @server&.close
    puts "Message Queue Server stopped"
  end

  def publish(queue_name, message)
    return false unless @running
    
    message_data = {
      id: SecureRandom.uuid,
      timestamp: Time.now,
      queue: queue_name,
      body: message
    }
    
    @mutex.synchronize do
      @queues[queue_name] ||= []
      @queues[queue_name] << message_data
      
      # Notify subscribers
      notify_subscribers(queue_name, message_data)
    end
    
    true
  end

  def subscribe(queue_name, callback)
    @mutex.synchronize do
      @subscribers[queue_name] ||= []
      @subscribers[queue_name] << callback
      
      # Deliver existing messages
      if @queues[queue_name]
        @queues[queue_name].each do |message|
          callback.call(message)
        end
      end
    end
  end

  def create_queue(queue_name, options = {})
    @mutex.synchronize do
      @queues[queue_name] ||= []
      @queues[queue_name].options = options if options
    end
  end

  def delete_queue(queue_name)
    @mutex.synchronize do
      @queues.delete(queue_name)
      @subscribers.delete(queue_name)
    end
  end

  def get_queue_stats(queue_name)
    @mutex.synchronize do
      queue = @queues[queue_name]
      return nil unless queue
      
      {
        name: queue_name,
        message_count: queue.length,
        subscriber_count: (@subscribers[queue_name] || []).length
      }
    end
  end

  private

  def accept_connections
    while @running
      begin
        client = @server.accept_nonblock
        Thread.new { handle_client(client) }
      rescue IO::WaitReadable
        sleep(0.1)
      rescue => e
        puts "Error accepting connection: #{e.message}"
      end
    end
  end

  def handle_client(client)
    while @running
      begin
        data = client.gets
        break unless data
        
        command = JSON.parse(data)
        response = process_command(command)
        client.puts(JSON.generate(response))
        
      rescue JSON::ParserError
        client.puts(JSON.generate({ error: 'Invalid JSON' }))
      rescue => e
        client.puts(JSON.generate({ error: e.message }))
      end
    end
    
    client.close
  end

  def process_command(command)
    case command['action']
    when 'publish'
      success = publish(command['queue'], command['message'])
      { success: success }
    when 'create_queue'
      create_queue(command['queue'], command['options'] || {})
      { success: true }
    when 'delete_queue'
      delete_queue(command['queue'])
      { success: true }
    when 'stats'
      stats = get_queue_stats(command['queue'])
      stats ? { success: true, stats: stats } : { success: false, error: 'Queue not found' }
    else
      { success: false, error: 'Unknown command' }
    end
  end

  def notify_subscribers(queue_name, message)
    subscribers = @subscribers[queue_name] || []
    subscribers.each do |callback|
      Thread.new { callback.call(message) }
    end
  end
end

# Message Queue Client
class MQClient
  def initialize(host = 'localhost', port = 5672)
    @host = host
    @port = port
    @socket = nil
  end

  def connect
    @socket = TCPSocket.new(@host, @port)
    puts "Connected to Message Queue at #{@host}:#{@port}"
  end

  def disconnect
    @socket&.close
    @socket = nil
  end

  def publish(queue_name, message)
    command = {
      action: 'publish',
      queue: queue_name,
      message: message
    }
    
    send_command(command)
  end

  def create_queue(queue_name, options = {})
    command = {
      action: 'create_queue',
      queue: queue_name,
      options: options
    }
    
    send_command(command)
  end

  def get_stats(queue_name)
    command = {
      action: 'stats',
      queue: queue_name
    }
    
    send_command(command)
  end

  private

  def send_command(command)
    return nil unless @socket
    
    @socket.puts(JSON.generate(command))
    response_data = @socket.gets
    JSON.parse(response_data) if response_data
  end
end
```

### Distributed Cache System
```ruby
require 'socket'
require 'json'
require 'digest/sha1'
require 'thread'

class DistributedCache
  def initialize(nodes)
    @nodes = nodes
    @ring = build_consistent_hash_ring
    @replication_factor = 2
    @local_cache = {}
    @mutex = Mutex.new
  end

  def set(key, value, ttl = 3600)
    nodes = get_nodes_for_key(key)
    
    nodes.each do |node|
      if node[:local]
        @mutex.synchronize do
          @local_cache[key] = {
            value: value,
            expires_at: Time.now + ttl,
            timestamp: Time.now
          }
        end
      else
        send_to_node(node, 'SET', key, value, ttl)
      end
    end
    
    true
  end

  def get(key)
    nodes = get_nodes_for_key(key)
    
    nodes.each do |node|
      if node[:local]
        @mutex.synchronize do
          entry = @local_cache[key]
          if entry && entry[:expires_at] > Time.now
            return entry[:value]
          elsif entry && entry[:expires_at] <= Time.now
            @local_cache.delete(key)
          end
        end
      else
        result = send_to_node(node, 'GET', key)
        return result if result
      end
    end
    
    nil
  end

  def delete(key)
    nodes = get_nodes_for_key(key)
    
    nodes.each do |node|
      if node[:local]
        @mutex.synchronize { @local_cache.delete(key) }
      else
        send_to_node(node, 'DELETE', key)
      end
    end
    
    true
  end

  def clear
    @mutex.synchronize { @local_cache.clear }
    
    @nodes.each do |node|
      next if node[:local]
      send_to_node(node, 'CLEAR')
    end
    
    true
  end

  def get_stats
    stats = {}
    
    @nodes.each_with_index do |node, index|
      if node[:local]
        @mutex.synchronize do
          stats["node_#{index}"] = {
            entries: @local_cache.length,
            memory_usage: calculate_memory_usage
          }
        end
      else
        result = send_to_node(node, 'STATS')
        stats["node_#{index}"] = result if result
      end
    end
    
    stats
  end

  private

  def build_consistent_hash_ring
    ring = {}
    virtual_nodes = 150
    
    @nodes.each_with_index do |node, index|
      virtual_nodes.times do |i|
        virtual_key = "#{node[:host]}:#{node[:port]}:#{i}"
        hash = hash_key(virtual_key)
        ring[hash] = index
      end
    end
    
    ring.sort.to_h
  end

  def hash_key(key)
    Digest::SHA1.hexdigest(key.to_s).to_i(16)
  end

  def get_nodes_for_key(key)
    key_hash = hash_key(key)
    ring_keys = @ring.keys
    
    # Find the first node with key >= key_hash
    node_index = ring_keys.find_index { |k| k >= key_hash }
    node_index = 0 if node_index.nil?
    
    selected_nodes = []
    
    @replication_factor.times do |i|
      actual_index = (node_index + i) % ring_keys.length
      node_id = @ring[ring_keys[actual_index]]
      selected_nodes << @nodes[node_id]
    end
    
    selected_nodes
  end

  def send_to_node(node, command, key = nil, value = nil, ttl = nil)
    return nil if node[:local]
    
    begin
      socket = TCPSocket.new(node[:host], node[:port])
      
      request = {
        command: command,
        key: key,
        value: value,
        ttl: ttl
      }
      
      socket.puts(JSON.generate(request))
      response_data = socket.gets
      socket.close
      
      response = JSON.parse(response_data) if response_data
      
      case command
      when 'GET'
        response ? response['value'] : nil
      when 'STATS'
        response
      else
        response ? response['success'] : false
      end
    rescue => e
      puts "Error communicating with node #{node[:host]}:#{node[:port]}: #{e.message}"
      nil
    end
  end

  def calculate_memory_usage
    @local_cache.to_s.bytesize
  end
end

# Cache Node Server
class CacheNode
  def initialize(host, port)
    @host = host
    @port = port
    @cache = {}
    @server = nil
    @running = false
    @mutex = Mutex.new
  end

  def start
    @server = TCPServer.new(@host, @port)
    @running = true
    
    puts "Cache node started on #{@host}:#{@port}"
    
    Thread.new { accept_connections }
    
    # Cleanup expired entries
    Thread.new { cleanup_expired_entries }
  end

  def stop
    @running = false
    @server&.close
    puts "Cache node stopped"
  end

  private

  def accept_connections
    while @running
      begin
        client = @server.accept_nonblock
        Thread.new { handle_client(client) }
      rescue IO::WaitReadable
        sleep(0.1)
      rescue => e
        puts "Error accepting connection: #{e.message}"
      end
    end
  end

  def handle_client(client)
    while @running
      begin
        data = client.gets
        break unless data
        
        request = JSON.parse(data)
        response = process_request(request)
        client.puts(JSON.generate(response))
        
      rescue JSON::ParserError
        client.puts(JSON.generate({ error: 'Invalid JSON' }))
      rescue => e
        client.puts(JSON.generate({ error: e.message }))
      end
    end
    
    client.close
  end

  def process_request(request)
    case request['command']
    when 'SET'
      set_value(request['key'], request['value'], request['ttl'])
      { success: true }
    when 'GET'
      value = get_value(request['key'])
      { success: true, value: value }
    when 'DELETE'
      delete_value(request['key'])
      { success: true }
    when 'CLEAR'
      clear_cache
      { success: true }
    when 'STATS'
      stats = get_node_stats
      { success: true, stats: stats }
    else
      { success: false, error: 'Unknown command' }
    end
  end

  def set_value(key, value, ttl)
    @mutex.synchronize do
      @cache[key] = {
        value: value,
        expires_at: Time.now + (ttl || 3600),
        timestamp: Time.now
      }
    end
  end

  def get_value(key)
    @mutex.synchronize do
      entry = @cache[key]
      if entry && entry[:expires_at] > Time.now
        entry[:value]
      elsif entry && entry[:expires_at] <= Time.now
        @cache.delete(key)
        nil
      else
        nil
      end
    end
  end

  def delete_value(key)
    @mutex.synchronize { @cache.delete(key) }
  end

  def clear_cache
    @mutex.synchronize { @cache.clear }
  end

  def get_node_stats
    @mutex.synchronize do
      {
        entries: @cache.length,
        memory_usage: @cache.to_s.bytesize,
        uptime: Time.now - (@start_time || Time.now)
      }
    end
  end

  def cleanup_expired_entries
    while @running
      sleep(60)  # Check every minute
      
      @mutex.synchronize do
        @cache.delete_if do |key, entry|
          entry[:expires_at] <= Time.now
        end
      end
    end
  end
end
```

## Load Balancing

### Round Robin Load Balancer
```ruby
require 'socket'
require 'thread'
require 'timeout'

class LoadBalancer
  def initialize(listen_port, backend_servers)
    @listen_port = listen_port
    @backend_servers = backend_servers
    @current_index = 0
    @server = nil
    @running = false
    @mutex = Mutex.new
    @health_status = {}
    @stats = {
      total_requests: 0,
      active_connections: 0,
      backend_stats: {}
    }
  end

  def start
    @server = TCPServer.new('0.0.0.0', @listen_port)
    @running = true
    
    puts "Load Balancer started on port #{@listen_port}"
    puts "Backend servers: #{@backend_servers.join(', ')}"
    
    # Start health checking
    Thread.new { health_check_loop }
    
    # Start accepting connections
    accept_connections
  end

  def stop
    @running = false
    @server&.close
    puts "Load Balancer stopped"
  end

  private

  def accept_connections
    while @running
      begin
        client = @server.accept
        Thread.new { handle_client(client) }
      rescue => e
        puts "Error accepting connection: #{e.message}"
      end
    end
  end

  def handle_client(client)
    backend = get_next_healthy_backend
    unless backend
      client.puts("HTTP/1.1 503 Service Unavailable\r\n\r\n")
      client.close
      return
    end
    
    @mutex.synchronize do
      @stats[:total_requests] += 1
      @stats[:active_connections] += 1
      @stats[:backend_stats][backend] ||= 0
      @stats[:backend_stats][backend] += 1
    end
    
    begin
      # Connect to backend
      backend_socket = connect_to_backend(backend)
      unless backend_socket
        client.puts("HTTP/1.1 502 Bad Gateway\r\n\r\n")
        client.close
        return
      end
      
      # Proxy data between client and backend
      proxy_connection(client, backend_socket)
      
    rescue => e
      puts "Error handling client: #{e.message}"
    ensure
      client.close
      @mutex.synchronize { @stats[:active_connections] -= 1 }
    end
  end

  def get_next_healthy_backend
    @mutex.synchronize do
      healthy_servers = @backend_servers.select { |server| @health_status[server] }
      return nil if healthy_servers.empty?
      
      @current_index = (@current_index + 1) % healthy_servers.length
      healthy_servers[@current_index]
    end
  end

  def connect_to_backend(backend)
    host, port = backend.split(':')
    socket = TCPSocket.new(host, port.to_i)
    socket
  rescue => e
    puts "Failed to connect to backend #{backend}: #{e.message}"
    mark_backend_unhealthy(backend)
    nil
  end

  def proxy_connection(client, backend)
    client_thread = Thread.new do
      begin
        while data = client.readpartial(4096)
          backend.write(data)
        end
      rescue EOFError
        # Client disconnected
      rescue => e
        puts "Error reading from client: #{e.message}"
      end
    end
    
    backend_thread = Thread.new do
      begin
        while data = backend.readpartial(4096)
          client.write(data)
        end
      rescue EOFError
        # Backend disconnected
      rescue => e
        puts "Error reading from backend: #{e.message}"
      end
    end
    
    client_thread.join
    backend_thread.join
  ensure
    backend&.close
  end

  def health_check_loop
    while @running
      @backend_servers.each do |server|
        check_backend_health(server)
      end
      
      sleep(10)  # Check every 10 seconds
    end
  end

  def check_backend_health(server)
    begin
      host, port = server.split(':')
      
      Timeout::timeout(5) do
        socket = TCPSocket.new(host, port.to_i)
        socket.close
        mark_backend_healthy(server)
      end
    rescue Timeout::Error, Errno::ECONNREFUSED => e
      mark_backend_unhealthy(server)
    end
  end

  def mark_backend_healthy(server)
    @mutex.synchronize do
      unless @health_status[server]
        @health_status[server] = true
        puts "Backend #{server} is now healthy"
      end
    end
  end

  def mark_backend_unhealthy(server)
    @mutex.synchronize do
      if @health_status[server]
        @health_status[server] = false
        puts "Backend #{server} is now unhealthy"
      end
    end
  end
end

# Usage example
if __FILE__ == $0
  backend_servers = [
    'localhost:3001',
    'localhost:3002',
    'localhost:3003'
  ]
  
  balancer = LoadBalancer.new(8080, backend_servers)
  
  # Handle Ctrl+C gracefully
  trap('INT') do
    balancer.stop
    exit
  end
  
  balancer.start
end
```

## Network Optimization

### Connection Pool Manager
```ruby
require 'socket'
require 'thread'
require 'timeout'

class ConnectionPool
  def initialize(host, port, size = 10)
    @host = host
    @port = port
    @size = size
    @pool = []
    @available = []
    @in_use = {}
    @mutex = Mutex.new
    @created = 0
    
    # Pre-create connections
    size.times { create_connection }
  end

  def with_connection
    connection = checkout
    begin
      yield connection
    ensure
      checkin(connection)
    end
  end

  def checkout
    @mutex.synchronize do
      if @available.empty?
        create_connection if @created < @size
        wait_for_available
      end
      
      connection = @available.pop
      @in_use[connection] = Time.now
      connection
    end
  end

  def checkin(connection)
    @mutex.synchronize do
      @in_use.delete(connection)
      @available.push(connection)
      @mutex.signal
    end
  end

  def close_all
    @mutex.synchronize do
      (@pool + @available).each(&:close)
      @pool.clear
      @available.clear
      @in_use.clear
    end
  end

  def stats
    @mutex.synchronize do
      {
        total_connections: @created,
        available: @available.length,
        in_use: @in_use.length,
        host: @host,
        port: @port
      }
    end
  end

  private

  def create_connection
    connection = TCPSocket.new(@host, @port)
    @pool << connection
    @available << connection
    @created += 1
    connection
  rescue => e
    puts "Failed to create connection: #{e.message}"
    nil
  end

  def wait_for_available
    Timeout::timeout(30) do
      while @available.empty?
        @mutex.wait(@mutex)
      end
    end
  rescue Timeout::Error
    raise "No available connections in pool"
  end
end

# HTTP Client with Connection Pooling
class PooledHTTPClient
  def initialize(host, port = 80, pool_size = 10)
    @pool = ConnectionPool.new(host, port, pool_size)
    @host = host
    @port = port
  end

  def get(path, headers = {})
    @pool.with_connection do |socket|
      request = build_request('GET', path, headers)
      socket.write(request)
      response = read_response(socket)
      response
    end
  end

  def post(path, data, headers = {})
    headers['Content-Length'] = data.length.to_s unless headers['Content-Length']
    
    @pool.with_connection do |socket|
      request = build_request('POST', path, headers, data)
      socket.write(request)
      response = read_response(socket)
      response
    end
  end

  def close
    @pool.close_all
  end

  private

  def build_request(method, path, headers, body = nil)
    request = "#{method} #{path} HTTP/1.1\r\n"
    request += "Host: #{@host}\r\n"
    request += "Connection: keep-alive\r\n"
    
    headers.each do |key, value|
      request += "#{key}: #{value}\r\n"
    end
    
    request += "\r\n"
    request += body if body
    
    request
  end

  def read_response(socket)
    response = ""
    
    # Read headers
    while line = socket.gets
      response += line
      break if line == "\r\n"
    end
    
    # Read body if present
    content_length = extract_content_length(response)
    if content_length
      body = socket.read(content_length)
      response += body
    else
      # Read until connection closed
      while chunk = socket.readpartial(4096)
        response += chunk
      end
    end
    
    response
  end

  def extract_content_length(headers)
    match = headers.match(/Content-Length: (\d+)/i)
    match ? match[1].to_i : nil
  end
end
```

## Advanced Network Protocols

### gRPC Implementation (Simplified)
```ruby
require 'socket'
require 'json'
require 'protobuf'  # Hypothetical protobuf gem

class GRPCServer
  def initialize(host, port)
    @host = host
    @port = port
    @services = {}
    @server = nil
    @running = false
  end

  def start
    @server = TCPServer.new(@host, @port)
    @running = true
    
    puts "gRPC Server started on #{@host}:#{@port}"
    
    accept_connections
  end

  def add_service(service_name, service_instance)
    @services[service_name] = service_instance
  end

  private

  def accept_connections
    while @running
      client = @server.accept
      Thread.new { handle_client(client) }
    end
  end

  def handle_client(client)
    while @running
      begin
        # Read gRPC frame (simplified)
        frame_length = client.read(4).unpack('N')[0]
        frame_data = client.read(frame_length)
        
        # Parse gRPC request
        request = parse_grpc_request(frame_data)
        
        # Process request
        response = process_grpc_request(request)
        
        # Send response
        send_grpc_response(client, response)
        
      rescue => e
        puts "Error handling gRPC client: #{e.message}"
        break
      end
    end
    
    client.close
  end

  def parse_grpc_request(data)
    # Simplified gRPC parsing
    {
      service: extract_service(data),
      method: extract_method(data),
      payload: extract_payload(data)
    }
  end

  def process_grpc_request(request)
    service = @services[request[:service]]
    return { error: "Service not found: #{request[:service]}" } unless service
    
    method = service.method(request[:method])
    return { error: "Method not found: #{request[:method]}" } unless method
    
    begin
      result = method.call(request[:payload])
      { success: true, result: result }
    rescue => e
      { error: e.message }
    end
  end

  def send_grpc_response(client, response)
    response_data = JSON.generate(response)
    response_frame = [response_data.length].pack('N') + response_data
    client.write(response_frame)
  end

  def extract_service(data)
    # Simplified extraction
    JSON.parse(data)['service'] rescue 'unknown'
  end

  def extract_method(data)
    # Simplified extraction
    JSON.parse(data)['method'] rescue 'unknown'
  end

  def extract_payload(data)
    # Simplified extraction
    JSON.parse(data)['payload'] rescue {}
  end
end

# Example gRPC Service
class CalculatorService
  def add(request)
    { result: request['a'] + request['b'] }
  end

  def multiply(request)
    { result: request['a'] * request['b'] }
  end

  def divide(request)
    if request['b'] == 0
      raise "Division by zero"
    end
    { result: request['a'] / request['b'] }
  end
end

# Usage
server = GRPCServer.new('localhost', 50051)
server.add_service('Calculator', CalculatorService.new)
server.start
```

## Performance Monitoring

### Network Performance Monitor
```ruby
require 'socket'
require 'thread'
require 'benchmark'

class NetworkMonitor
  def initialize
    @metrics = {
      connections: 0,
      bytes_sent: 0,
      bytes_received: 0,
      response_times: [],
      errors: 0
    }
    @mutex = Mutex.new
    @monitoring = false
  end

  def start_monitoring
    @monitoring = true
    @monitor_thread = Thread.new { monitor_loop }
  end

  def stop_monitoring
    @monitoring = false
    @monitor_thread&.join
  end

  def record_connection
    @mutex.synchronize { @metrics[:connections] += 1 }
  end

  def record_bytes_sent(bytes)
    @mutex.synchronize { @metrics[:bytes_sent] += bytes }
  end

  def record_bytes_received(bytes)
    @mutex.synchronize { @metrics[:bytes_received] += bytes }
  end

  def record_response_time(time)
    @mutex.synchronize do
      @metrics[:response_times] << time
      # Keep only last 1000 measurements
      @metrics[:response_times] = @metrics[:response_times].last(1000)
    end
  end

  def record_error
    @mutex.synchronize { @metrics[:errors] += 1 }
  end

  def get_metrics
    @mutex.synchronize do
      response_times = @metrics[:response_times]
      
      {
        connections: @metrics[:connections],
        bytes_sent: @metrics[:bytes_sent],
        bytes_received: @metrics[:bytes_received],
        errors: @metrics[:errors],
        avg_response_time: response_times.empty? ? 0 : response_times.sum / response_times.length,
        max_response_time: response_times.max || 0,
        min_response_time: response_times.min || 0,
        p95_response_time: calculate_percentile(response_times, 95),
        p99_response_time: calculate_percentile(response_times, 99)
      }
    end
  end

  def benchmark_operation(operation, &block)
    record_connection
    
    result = nil
    time = Benchmark.realtime do
      result = block.call
    end
    
    record_response_time(time)
    result
  rescue => e
    record_error
    raise
  end

  private

  def monitor_loop
    while @monitoring
      sleep(60)  # Report every minute
      print_metrics
    end
  end

  def print_metrics
    metrics = get_metrics
    
    puts "\n" + "=" * 50
    puts "NETWORK PERFORMANCE METRICS"
    puts "=" * 50
    puts "Total Connections: #{metrics[:connections]}"
    puts "Bytes Sent: #{format_bytes(metrics[:bytes_sent])}"
    puts "Bytes Received: #{format_bytes(metrics[:bytes_received])}"
    puts "Errors: #{metrics[:errors]}"
    puts "Avg Response Time: #{metrics[:avg_response_time].round(3)}ms"
    puts "Max Response Time: #{metrics[:max_response_time].round(3)}ms"
    puts "95th Percentile: #{metrics[:p95_response_time].round(3)}ms"
    puts "99th Percentile: #{metrics[:p99_response_time].round(3)}ms"
    puts "=" * 50
  end

  def calculate_percentile(values, percentile)
    return 0 if values.empty?
    
    sorted = values.sort
    index = (percentile / 100.0 * (sorted.length - 1)).round
    sorted[index]
  end

  def format_bytes(bytes)
    units = ['B', 'KB', 'MB', 'GB', 'TB']
    size = bytes.to_f
    unit_index = 0
    
    while size >= 1024 && unit_index < units.length - 1
      size /= 1024
      unit_index += 1
    end
    
    "#{size.round(2)} #{units[unit_index]}"
  end
end

# Monitored HTTP Client
class MonitoredHTTPClient
  def initialize(host, port = 80)
    @host = host
    @port = port
    @monitor = NetworkMonitor.new
    @monitor.start_monitoring
  end

  def get(path)
    @monitor.benchmark_operation('GET') do
      socket = TCPSocket.new(@host, @port)
      
      request = "GET #{path} HTTP/1.1\r\nHost: #{@host}\r\n\r\n"
      socket.write(request)
      @monitor.record_bytes_sent(request.length)
      
      response = socket.read
      @monitor.record_bytes_received(response.length)
      
      socket.close
      response
    end
  rescue => e
    @monitor.record_error
    raise
  end

  def stop
    @monitor.stop_monitoring
  end
end
```

## Best Practices

1. **Connection Pooling**: Reuse connections to reduce overhead
2. **Load Balancing**: Distribute load across multiple servers
3. **Caching**: Implement distributed caching for performance
4. **Monitoring**: Track network performance and metrics
5. **Error Handling**: Implement robust error recovery
6. **Security**: Use encryption and authentication
7. **Scalability**: Design for horizontal scaling

## Conclusion

Advanced networking in Ruby enables the creation of sophisticated, high-performance distributed systems. By implementing proper load balancing, connection pooling, distributed caching, and monitoring, you can build scalable and resilient network applications that can handle enterprise-level workloads.

## Further Reading

- [Distributed Systems Concepts](https://en.wikipedia.org/wiki/Distributed_computing)
- [Load Balancing Algorithms](https://en.wikipedia.org/wiki/Load_balancing_(computing))
- [Consistent Hashing](https://en.wikipedia.org/wiki/Consistent_hashing)
- [Network Performance Optimization](https://www.ietf.org/)
