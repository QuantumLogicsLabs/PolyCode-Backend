# Service Discovery in Microservices
# Comprehensive guide to service registration and discovery patterns

## 🎯 Overview

Service discovery is crucial for microservices to find and communicate with each other. This guide covers service registration, discovery patterns, load balancing, and Ruby implementations.

## 🗄️ Service Registry

### 1. Centralized Service Registry

Central registry for managing service instances:

```ruby
# Service registry implementation
class ServiceRegistry
  def initialize
    @services = {}
    @mutex = Mutex.new
  end
  
  def register(service_name, instance_url, metadata = {})
    @mutex.synchronize do
      @services[service_name] ||= []
      
      instance = {
        url: instance_url,
        metadata: metadata,
        registered_at: Time.now,
        last_heartbeat: Time.now,
        status: :healthy
      }
      
      # Remove existing instance with same URL
      @services[service_name].reject! { |inst| inst[:url] == instance_url }
      
      @services[service_name] << instance
      puts "Registered #{service_name} instance: #{instance_url}"
    end
  end
  
  def unregister(service_name, instance_url)
    @mutex.synchronize do
      instances = @services[service_name]
      if instances
        instances.reject! { |inst| inst[:url] == instance_url }
        puts "Unregistered #{service_name} instance: #{instance_url}"
      end
    end
  end
  
  def get_instances(service_name, status = :healthy)
    @mutex.synchronize do
      instances = @services[service_name] || []
      instances.select { |inst| inst[:status] == status }
    end
  end
  
  def get_instance(service_name, strategy = :round_robin)
    instances = get_instances(service_name)
    return nil if instances.empty?
    
    case strategy
    when :round_robin
      round_robin_selection(service_name, instances)
    when :random
      instances.sample
    when :least_connections
      least_connections_selection(instances)
    else
      instances.first
    end
  end
  
  def heartbeat(service_name, instance_url)
    @mutex.synchronize do
      instances = @services[service_name]
      return unless instances
      
      instance = instances.find { |inst| inst[:url] == instance_url }
      if instance
        instance[:last_heartbeat] = Time.now
        instance[:status] = :healthy if instance[:status] == :unhealthy
      end
    end
  end
  
  def check_health
    @mutex.synchronize do
      @services.each do |service_name, instances|
        instances.each do |instance|
          if Time.now - instance[:last_heartbeat] > 30
            instance[:status] = :unhealthy
            puts "Marked #{service_name} instance as unhealthy: #{instance[:url]}"
          end
        end
      end
    end
  end
  
  def service_list
    @mutex.synchronize do
      @services.keys
    end
  end
  
  def service_stats(service_name)
    @mutex.synchronize do
      instances = @services[service_name] || []
      
      {
        name: service_name,
        total_instances: instances.length,
        healthy_instances: instances.count { |i| i[:status] == :healthy },
        unhealthy_instances: instances.count { |i| i[:status] == :unhealthy },
        instances: instances.map { |i| { url: i[:url], status: i[:status] } }
      }
    end
  end
  
  def all_stats
    @mutex.synchronize do
      @services.map do |name, instances|
        service_stats(name)
      end
    end
  end
  
  private
  
  def round_robin_selection(service_name, instances)
    @round_robin_counters ||= {}
    @round_robin_counters[service_name] ||= 0
    
    index = @round_robin_counters[service_name] % instances.length
    @round_robin_counters[service_name] += 1
    
    instances[index]
  end
  
  def least_connections_selection(instances)
    instances.min_by { |inst| inst[:metadata][:connections] || 0 }
  end
end

# Service registration client
class ServiceRegistrationClient
  def initialize(registry_url, service_name, instance_url)
    @registry_url = registry_url
    @service_name = service_name
    @instance_url = instance_url
    @heartbeat_thread = nil
  end
  
  def register(metadata = {})
    response = HTTP.post(
      "#{@registry_url}/services/#{@service_name}/register",
      json: { url: @instance_url, metadata: metadata }
    )
    
    if response.code == 200
      start_heartbeat
      true
    else
      false
    end
  end
  
  def unregister
    stop_heartbeat
    
    response = HTTP.delete(
      "#{@registry_url}/services/#{@service_name}/instances/#{@instance_url}"
    )
    
    response.code == 200
  end
  
  private
  
  def start_heartbeat
    stop_heartbeat
    
    @heartbeat_thread = Thread.new do
      loop do
        begin
          response = HTTP.post(
            "#{@registry_url}/services/#{@service_name}/heartbeat",
            json: { url: @instance_url }
          )
          
          unless response.code == 200
            puts "Heartbeat failed for #{@service_name}"
          end
          
          sleep(10)  # Send heartbeat every 10 seconds
        rescue => e
          puts "Heartbeat error: #{e.message}"
          sleep(5)
        end
      end
    end
  end
  
  def stop_heartbeat
    if @heartbeat_thread
      @heartbeat_thread.kill
      @heartbeat_thread = nil
    end
  end
end

# Usage
registry = ServiceRegistry.new

# Register services
registry.register('user-service', 'http://user-service-1:3001', region: 'us-east-1')
registry.register('user-service', 'http://user-service-2:3001', region: 'us-east-1')
registry.register('order-service', 'http://order-service-1:3002', region: 'us-west-1')
registry.register('payment-service', 'http://payment-service-1:3003', region: 'us-east-1')

# Get instances
user_instances = registry.get_instances('user-service')
puts "User service instances: #{user_instances.length}"

# Get single instance with round-robin
user_instance = registry.get_instance('user-service', :round_robin)
puts "Selected user instance: #{user_instance[:url]}"

# Service stats
user_stats = registry.service_stats('user-service')
puts "User service stats: #{user_stats}"

# Health check
registry.check_health
```

### 2. Distributed Service Registry

Multi-node service registry with replication:

```ruby
# Distributed service registry node
class DistributedServiceRegistry
  def initialize(node_id, peers = [])
    @node_id = node_id
    @peers = peers
    @services = {}
    @mutex = Mutex.new
    @replication_thread = nil
  end
  
  def start
    start_replication
    start_health_check
    puts "Started distributed registry node #{@node_id}"
  end
  
  def stop
    stop_replication
    puts "Stopped distributed registry node #{@node_id}"
  end
  
  def register(service_name, instance_url, metadata = {})
    @mutex.synchronize do
      @services[service_name] ||= []
      
      instance = {
        url: instance_url,
        metadata: metadata,
        registered_at: Time.now,
        last_heartbeat: Time.now,
        status: :healthy,
        node_id: @node_id
      }
      
      # Remove existing instance with same URL
      @services[service_name].reject! { |inst| inst[:url] == instance_url }
      
      @services[service_name] << instance
      
      # Replicate to peers
      replicate_registration(service_name, instance)
      
      puts "Registered #{service_name} instance: #{instance_url}"
    end
  end
  
  def get_instances(service_name, status = :healthy)
    @mutex.synchronize do
      instances = @services[service_name] || []
      instances.select { |inst| inst[:status] == status }
    end
  end
  
  def get_instance(service_name, strategy = :round_robin)
    instances = get_instances(service_name)
    return nil if instances.empty?
    
    case strategy
    when :round_robin
      round_robin_selection(service_name, instances)
    when :random
      instances.sample
    when :local_first
      local_first_selection(instances)
    else
      instances.first
    end
  end
  
  def heartbeat(service_name, instance_url)
    @mutex.synchronize do
      instances = @services[service_name]
      return unless instances
      
      instance = instances.find { |inst| inst[:url] == instance_url }
      if instance
        instance[:last_heartbeat] = Time.now
        instance[:status] = :healthy if instance[:status] == :unhealthy
      end
    end
  end
  
  def sync_with_peer(peer_url)
    begin
      response = HTTP.get("#{peer_url}/services")
      
      if response.code == 200
        peer_services = response.parse
        
        @mutex.synchronize do
          peer_services.each do |service_name, instances|
            @services[service_name] ||= []
            
            instances.each do |instance_data|
              # Don't overwrite our own instances
              next if instance_data[:node_id] == @node_id
              
              existing = @services[service_name].find { |inst| inst[:url] == instance_data[:url] }
              
              if existing
                # Update existing instance
                existing.merge!(instance_data)
              else
                # Add new instance
                @services[service_name] << instance_data
                puts "Synced #{service_name} instance: #{instance_data[:url]}"
              end
            end
          end
        end
      end
    rescue => e
      puts "Failed to sync with peer #{peer_url}: #{e.message}"
    end
  end
  
  def get_all_services
    @mutex.synchronize { @services.dup }
  end
  
  private
  
  def start_replication
    @replication_thread = Thread.new do
      loop do
        sync_with_peers
        sleep(30)  # Sync every 30 seconds
      end
    end
  end
  
  def stop_replication
    if @replication_thread
      @replication_thread.kill
      @replication_thread = nil
    end
  end
  
  def start_health_check
    Thread.new do
      loop do
        check_health
        sleep(60)  # Check health every minute
      end
    end
  end
  
  def check_health
    @mutex.synchronize do
      @services.each do |service_name, instances|
        instances.each do |instance|
          if Time.now - instance[:last_heartbeat] > 60
            instance[:status] = :unhealthy
            puts "Marked #{service_name} instance as unhealthy: #{instance[:url]}"
          end
        end
      end
    end
  end
  
  def replicate_registration(service_name, instance)
    @peers.each do |peer_url|
      Thread.new do
        begin
          HTTP.post(
            "#{peer_url}/services/#{service_name}/register",
            json: instance
          )
        rescue => e
          puts "Failed to replicate to peer #{peer_url}: #{e.message}"
        end
      end
    end
  end
  
  def sync_with_peers
    @peers.each do |peer_url|
      sync_with_peer(peer_url)
    end
  end
  
  def round_robin_selection(service_name, instances)
    @round_robin_counters ||= {}
    @round_robin_counters[service_name] ||= 0
    
    index = @round_robin_counters[service_name] % instances.length
    @round_robin_counters[service_name] += 1
    
    instances[index]
  end
  
  def local_first_selection(instances)
    # Prefer local instances
    local_instances = instances.select { |inst| inst[:node_id] == @node_id }
    return local_instances.sample if local_instances.any?
    
    # Fall back to any instance
    instances.sample
  end
end

# Usage
# Create distributed registry nodes
node1 = DistributedServiceRegistry.new('node1', ['http://node2:8761', 'http://node3:8761'])
node2 = DistributedServiceRegistry.new('node2', ['http://node1:8761', 'http://node3:8761'])
node3 = DistributedServiceRegistry.new('node3', ['http://node1:8761', 'http://node2:8761'])

# Start nodes
node1.start
node2.start
node3.start

# Register services on different nodes
node1.register('user-service', 'http://user-service-1:3001')
node2.register('user-service', 'http://user-service-2:3001')
node3.register('order-service', 'http://order-service-1:3002')

# Wait for sync
sleep(5)

# All nodes should have all services
puts "Node1 services: #{node1.get_all_services.keys}"
puts "Node2 services: #{node2.get_all_services.keys}"
puts "Node3 services: #{node3.get_all_services.keys}"
```

## 🔄 Load Balancing

### 1. Client-Side Load Balancing

Load balancing at the client level:

```ruby
# Load balancer strategies
class LoadBalancer
  def initialize(strategy = :round_robin)
    @strategy = strategy
    @counters = {}
    @mutex = Mutex.new
  end
  
  def select_instance(instances)
    return nil if instances.empty?
    
    case @strategy
    when :round_robin
      round_robin_selection(instances)
    when :random
      random_selection(instances)
    when :least_connections
      least_connections_selection(instances)
    when :weighted_round_robin
      weighted_round_robin_selection(instances)
    when :ip_hash
      ip_hash_selection(instances)
    else
      instances.first
    end
  end
  
  private
  
  def round_robin_selection(instances)
    @mutex.synchronize do
      @counters[:round_robin] ||= 0
      index = @counters[:round_robin] % instances.length
      @counters[:round_robin] += 1
      instances[index]
    end
  end
  
  def random_selection(instances)
    instances.sample
  end
  
  def least_connections_selection(instances)
    instances.min_by { |inst| inst[:metadata][:connections] || 0 }
  end
  
  def weighted_round_robin_selection(instances)
    @mutex.synchronize do
      @counters[:weighted_round_robin] ||= 0
      
      # Calculate weights (default to 1 if not specified)
      weights = instances.map { |inst| inst[:metadata][:weight] || 1 }
      total_weight = weights.sum
      
      if total_weight > 0
        @counters[:weighted_round_robin] = (@counters[:weighted_round_robin] + 1) % total_weight
        
        current_weight = 0
        instances.each_with_index do |instance, index|
          current_weight += weights[index]
          return instance if @counters[:weighted_round_robin] < current_weight
        end
      end
      
      instances.first
    end
  end
  
  def ip_hash_selection(instances)
    # Use client IP hash (simplified)
    client_ip = Thread.current[:client_ip] || '127.0.0.1'
    hash = client_ip.split('.').map(&:to_i).sum
    instances[hash % instances.length]
  end
end

# Service client with load balancing
class LoadBalancedServiceClient
  def initialize(service_registry, service_name, load_balancer = nil)
    @registry = service_registry
    @service_name = service_name
    @load_balancer = load_balancer || LoadBalancer.new
    @client = ServiceClient.new
  end
  
  def get(endpoint, params = {})
    instance = get_instance
    raise NoAvailableInstancesError unless instance
    
    url = "#{instance[:url]}#{endpoint}"
    @client.get(url, params)
  rescue => e
    # Try next instance on failure
    fallback_instance = get_instance(exclude: instance[:url])
    if fallback_instance
      url = "#{fallback_instance[:url]}#{endpoint}"
      @client.get(url, params)
    else
      raise e
    end
  end
  
  def post(endpoint, data = {}, params = {})
    instance = get_instance
    raise NoAvailableInstancesError unless instance
    
    url = "#{instance[:url]}#{endpoint}"
    @client.post(url, data, params)
  rescue => e
    fallback_instance = get_instance(exclude: instance[:url])
    if fallback_instance
      url = "#{fallback_instance[:url]}#{endpoint}"
      @client.post(url, data, params)
    else
      raise e
    end
  end
  
  private
  
  def get_instance(exclude: nil)
    instances = @registry.get_instances(@service_name)
    instances = instances.reject { |inst| inst[:url] == exclude } if exclude
    
    @load_balancer.select_instance(instances)
  end
end

# Usage
registry = ServiceRegistry.new

# Register multiple instances of the same service
registry.register('user-service', 'http://user-service-1:3001', weight: 2)
registry.register('user-service', 'http://user-service-2:3001', weight: 1)
registry.register('user-service', 'http://user-service-3:3001', weight: 1)

# Create load balanced client
load_balancer = LoadBalancer.new(:weighted_round_robin)
client = LoadBalancedServiceClient.new(registry, 'user-service', load_balancer)

# Make requests - they'll be load balanced
5.times do |i|
  begin
    user = client.get("/users/#{i + 1}")
    puts "Request #{i + 1}: Success"
  rescue => e
    puts "Request #{i + 1}: Failed - #{e.message}"
  end
end
```

### 2. Server-Side Load Balancing

Load balancing at the server level with reverse proxy:

```ruby
# Reverse proxy load balancer
class ReverseProxyLoadBalancer
  def initialize(port = 8080)
    @port = port
    @services = {}
    @load_balancer = LoadBalancer.new
    @server = nil
  end
  
  def start
    @server = TCPServer.new('0.0.0.0', @port)
    puts "Reverse proxy started on port #{@port}"
    
    loop do
      client = @server.accept
      Thread.new { handle_request(client) }
    end
  end
  
  def stop
    @server&.close
    puts "Reverse proxy stopped"
  end
  
  def register_service(path, instances)
    @services[path] = instances
    puts "Registered service #{path} with #{instances.length} instances"
  end
  
  private
  
  def handle_request(client)
    request_line = client.gets
    return unless request_line
    
    method, path, version = request_line.split(' ')
    
    # Read headers
    headers = {}
    while (line = client.gets) && line != "\r\n"
      key, value = line.strip.split(': ', 2)
      headers[key] = value if key && value
    end
    
    # Read body if present
    body = nil
    if headers['Content-Length']
      body_length = headers['Content-Length'].to_i
      body = client.read(body_length)
    end
    
    # Route request
    response = route_request(method, path, headers, body)
    
    # Send response
    client.print response
    client.close
  end
  
  def route_request(method, path, headers, body)
    # Find matching service
    service_path = find_service_path(path)
    return not_found_response unless service_path
    
    instances = @services[service_path]
    return service_unavailable_response unless instances
    
    # Select instance using load balancer
    instance = @load_balancer.select_instance(instances)
    return service_unavailable_response unless instance
    
    # Forward request to selected instance
    forward_request(method, path, headers, body, instance)
  rescue => e
    error_response(e.message)
  end
  
  def find_service_path(request_path)
    # Simple path matching - could be more sophisticated
    @services.keys.find { |service_path| request_path.start_with?(service_path) }
  end
  
  def forward_request(method, path, headers, body, instance)
    uri = URI("#{instance[:url]}#{path}")
    
    request_class = case method
                   when 'GET'
                     Net::HTTP::Get
                   when 'POST'
                     Net::HTTP::Post
                   when 'PUT'
                     Net::HTTP::Put
                   when 'DELETE'
                     Net::HTTP::Delete
                   else
                     Net::HTTP::Get
                   end
    
    http = Net::HTTP.new(uri.host, uri.port)
    request = request_class.new(uri)
    
    # Copy headers
    headers.each { |key, value| request[key] = value }
    
    # Set body if present
    request.body = body if body
    
    response = http.request(request)
    
    # Build response string
    response_string = "HTTP/1.1 #{response.code} #{response.message}\r\n"
    response.each_header { |key, value| response_string += "#{key}: #{value}\r\n" }
    response_string += "\r\n"
    response_string += response.body
    
    response_string
  end
  
  def not_found_response
    "HTTP/1.1 404 Not Found\r\nContent-Type: text/plain\r\n\r\nService not found"
  end
  
  def service_unavailable_response
    "HTTP/1.1 503 Service Unavailable\r\nContent-Type: text/plain\r\n\r\nService unavailable"
  end
  
  def error_response(message)
    "HTTP/1.1 500 Internal Server Error\r\nContent-Type: text/plain\r\n\r\n#{message}"
  end
end

# Usage
proxy = ReverseProxyLoadBalancer.new.new(8080)

# Register services
proxy.register_service('/api/users', [
  { url: 'http://user-service-1:3001', weight: 2 },
  { url: 'http://user-service-2:3001', weight: 1 }
])

proxy.register_service('/api/orders', [
  { url: 'http://order-service-1:3002', weight: 1 },
  { url: 'http://order-service-2:3002', weight: 1 }
])

# Start proxy in separate thread
proxy_thread = Thread.new { proxy.start }

puts "Reverse proxy is running on http://localhost:8080"
puts "Try accessing:"
puts "  http://localhost:8080/api/users/1"
puts "  http://localhost:8080/api/orders/123"
```

## 🔍 Service Discovery Patterns

### 1. Client-Side Discovery

Clients discover services directly:

```ruby
# Client-side discovery client
class ClientSideDiscoveryClient
  def initialize(service_registry)
    @registry = service_registry
    @load_balancer = LoadBalancer.new
    @cache = {}
    @cache_ttl = 30  # Cache for 30 seconds
    @mutex = Mutex.new
  end
  
  def get_service_url(service_name)
    cached_url = get_cached_url(service_name)
    return cached_url if cached_url
    
    # Discover from registry
    instance = @registry.get_instance(service_name)
    return nil unless instance
    
    url = instance[:url]
    cache_url(service_name, url)
    url
  end
  
  def get_service_client(service_name)
    url = get_service_url(service_name)
    return nil unless url
    
    ServiceClient.new(url)
  end
  
  def refresh_cache(service_name = nil)
    @mutex.synchronize do
      if service_name
        @cache.delete(service_name)
      else
        @cache.clear
      end
    end
  end
  
  private
  
  def get_cached_url(service_name)
    @mutex.synchronize do
      cached = @cache[service_name]
      return nil unless cached
      
      if Time.now - cached[:timestamp] < @cache_ttl
        cached[:url]
      else
        @cache.delete(service_name)
        nil
      end
    end
  end
  
  def cache_url(service_name, url)
    @mutex.synchronize do
      @cache[service_name] = {
        url: url,
        timestamp: Time.now
      }
    end
  end
end

# Usage
registry = ServiceRegistry.new
discovery_client = ClientSideDiscoveryClient.new(registry)

# Register services
registry.register('user-service', 'http://user-service-1:3001')
registry.register('order-service', 'http://order-service-1:3002')

# Get service clients
user_client = discovery_client.get_service_client('user-service')
order_client = discovery_client.get_service_client('order-service')

# Use clients
if user_client
  user = user_client.get('/users/1')
  puts "User: #{user[:name]}"
end
```

### 2. Server-Side Discovery

Server handles service discovery for clients:

```ruby
# Server-side discovery API
class ServerSideDiscoveryAPI
  def initialize(service_registry)
    @registry = service_registry
  end
  
  def start(port = 8761)
    @server = TCPServer.new('0.0.0.0', port)
    puts "Discovery API started on port #{port}"
    
    loop do
      client = @server.accept
      Thread.new { handle_request(client) }
    end
  end
  
  private
  
  def handle_request(client)
    request_line = client.gets
    return unless request_line
    
    method, path, version = request_line.split(' ')
    
    # Read headers
    headers = {}
    while (line = client.gets) && line != "\r\n"
      key, value = line.strip.split(': ', 2)
      headers[key] = value if key && value
    end
    
    # Read body if present
    body = nil
    if headers['Content-Length']
      body_length = headers['Content-Length'].to_i
      body = client.read(body_length)
    end
    
    # Route request
    response = route_request(method, path, headers, body)
    
    # Send response
    client.print response
    client.close
  end
  
  def route_request(method, path, headers, body)
    case path
    when '/services'
      list_services
    when %r{^/services/([^/]+)$}
      service_name = $1
      get_service_instances(service_name)
    when %r{^/services/([^/]+)/instances/([^/]+)$}
      service_name = $1
      instance_url = $2
      get_service_instance(service_name, instance_url)
    else
      not_found_response
    end
  rescue => e
    error_response(e.message)
  end
  
  def list_services
    services = @registry.service_list
    response_data = { services: services }
    
    json_response(response_data)
  end
  
  def get_service_instances(service_name)
    instances = @registry.get_instances(service_name)
    response_data = {
      service: service_name,
      instances: instances.map { |inst| { url: inst[:url], metadata: inst[:metadata] } }
    }
    
    json_response(response_data)
  end
  
  def get_service_instance(service_name, instance_url)
    instances = @registry.get_instances(service_name)
    instance = instances.find { |inst| inst[:url] == instance_url }
    
    return not_found_response unless instance
    
    response_data = {
      service: service_name,
      instance: { url: instance[:url], metadata: instance[:metadata] }
    }
    
    json_response(response_data)
  end
  
  def json_response(data)
    response_body = data.to_json
    
    "HTTP/1.1 200 OK\r\n" \
    "Content-Type: application/json\r\n" \
    "Content-Length: #{response_body.length}\r\n" \
    "\r\n" \
    "#{response_body}"
  end
  
  def not_found_response
    "HTTP/1.1 404 Not Found\r\nContent-Type: text/plain\r\n\r\nNot found"
  end
  
  def error_response(message)
    "HTTP/1.1 500 Internal Server Error\r\nContent-Type: text/plain\r\n\r\n#{message}"
  end
end

# Client for server-side discovery
class ServerSideDiscoveryClient
  def initialize(discovery_url)
    @discovery_url = discovery_url
    @cache = {}
    @cache_ttl = 30
    @mutex = Mutex.new
  end
  
  def get_service_instances(service_name)
    cached_instances = get_cached_instances(service_name)
    return cached_instances if cached_instances
    
    # Query discovery service
    response = HTTP.get("#{@discovery_url}/services/#{service_name}")
    
    if response.code == 200
      data = response.parse
      instances = data[:instances].map { |inst| inst[:url] }
      cache_instances(service_name, instances)
      instances
    else
      []
    end
  end
  
  def get_service_url(service_name, strategy = :round_robin)
    instances = get_service_instances(service_name)
    return nil if instances.empty?
    
    case strategy
    when :round_robin
      round_robin_selection(service_name, instances)
    when :random
      instances.sample
    else
      instances.first
    end
  end
  
  private
  
  def get_cached_instances(service_name)
    @mutex.synchronize do
      cached = @cache[service_name]
      return nil unless cached
      
      if Time.now - cached[:timestamp] < @cache_ttl
        cached[:instances]
      else
        @cache.delete(service_name)
        nil
      end
    end
  end
  
  def cache_instances(service_name, instances)
    @mutex.synchronize do
      @cache[service_name] = {
        instances: instances,
        timestamp: Time.now
      }
    end
  end
  
  def round_robin_selection(service_name, instances)
    @round_robin_counters ||= {}
    @round_robin_counters[service_name] ||= 0
    
    index = @round_robin_counters[service_name] % instances.length
    @round_robin_counters[service_name] += 1
    
    instances[index]
  end
end

# Usage
registry = ServiceRegistry.new
discovery_api = ServerSideDiscoveryAPI.new(registry)

# Start discovery API in separate thread
discovery_thread = Thread.new { discovery_api.start }

# Register services
registry.register('user-service', 'http://user-service-1:3001')
registry.register('order-service', 'http://order-service-1:3002')

# Client using server-side discovery
client = ServerSideDiscoveryClient.new('http://localhost:8761')

# Get service URLs
user_url = client.get_service_url('user-service')
order_url = client.get_service_url('order-service')

puts "User service URL: #{user_url}"
puts "Order service URL: #{order_url}"
```

## 🎯 Best Practices

### 1. Health Checking

```ruby
# Comprehensive health checking
class HealthChecker
  def initialize(service_registry)
    @registry = service_registry
    @checks = {}
  end
  
  def register_health_check(service_name, &block)
    @checks[service_name] = block
  end
  
  def check_all_services
    @registry.service_list.each do |service_name|
      check_service(service_name)
    end
  end
  
  def check_service(service_name)
    instances = @registry.get_instances(service_name)
    
    instances.each do |instance|
      healthy = check_instance(instance, service_name)
      
      unless healthy
        @registry.unregister(service_name, instance[:url])
        puts "Unregistered unhealthy instance: #{instance[:url]}"
      end
    end
  end
  
  private
  
  def check_instance(instance, service_name)
    check_proc = @checks[service_name]
    
    if check_proc
      check_proc.call(instance)
    else
      # Default health check
      default_health_check(instance)
    end
  rescue => e
    puts "Health check failed for #{instance[:url]}: #{e.message}"
    false
  end
  
  def default_health_check(instance)
    response = HTTP.get("#{instance[:url]}/health", timeout: 5)
    response.code == 200
  rescue
    false
  end
end
```

### 2. Configuration Management

```ruby
# Service discovery configuration
class DiscoveryConfig
  attr_reader :registry_url, :service_name, :instance_url, :metadata
  
  def initialize(config_file = nil)
    if config_file
      load_from_file(config_file)
    else
      load_from_env
    end
  end
  
  private
  
  def load_from_file(config_file)
    config = YAML.load_file(config_file)
    @registry_url = config['registry_url']
    @service_name = config['service_name']
    @instance_url = config['instance_url']
    @metadata = config['metadata'] || {}
  end
  
  def load_from_env
    @registry_url = ENV['REGISTRY_URL']
    @service_name = ENV['SERVICE_NAME']
    @instance_url = ENV['INSTANCE_URL']
    @metadata = {
      region: ENV['REGION'],
      version: ENV['VERSION'],
      weight: ENV['WEIGHT']&.to_i
    }.compact
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Service Registry**: Implement a basic service registry
2. **Load Balancer**: Create different load balancing strategies
3. **Health Check**: Add health checking to services

### Intermediate Exercises

1. **Distributed Registry**: Build a multi-node registry
2. **Client Discovery**: Implement client-side discovery
3. **Reverse Proxy**: Create a reverse proxy load balancer

### Advanced Exercises

1. **Service Mesh**: Implement a simple service mesh
2. **Dynamic Configuration**: Add dynamic configuration updates
3. **Monitoring**: Add comprehensive monitoring and metrics

---

## 🎯 Summary

Service discovery patterns provide:

- **Service Registry** - Centralized service registration
- **Load Balancing** - Efficient request distribution
- **Health Checking** - Service availability monitoring
- **Discovery Patterns** - Client and server-side discovery
- **Distributed Architecture** - Scalable service management

Master service discovery to build robust, scalable microservices architectures!
