# Networking in Ruby

## Overview

Ruby provides powerful networking capabilities through its standard library and various gems. This guide covers socket programming, HTTP clients, web services, and network programming concepts.

## Socket Programming

### Basic TCP Server

```ruby
require 'socket'

class TCPServer
  def initialize(host = 'localhost', port = 3000)
    @server = TCPServer.new(host, port)
    @running = false
    puts "Server started on #{host}:#{port}"
  end
  
  def start
    @running = true
    puts "Waiting for connections..."
    
    while @running
      begin
        client = @server.accept
        handle_client(client)
      rescue => e
        puts "Error accepting connection: #{e.message}"
      end
    end
  end
  
  def stop
    @running = false
    @server.close
    puts "Server stopped"
  end
  
  private
  
  def handle_client(client)
    Thread.new do
      begin
        client.puts "Welcome to Ruby TCP Server!"
        client.puts "Type 'quit' to disconnect"
        
        while line = client.gets
          line = line.chomp
          break if line.downcase == 'quit'
          
          response = process_request(line)
          client.puts response
        end
        
      rescue => e
        puts "Error handling client: #{e.message}"
      ensure
        client.close
        puts "Client disconnected"
      end
    end
  end
  
  def process_request(request)
    case request.downcase
    when 'time'
      Time.now.strftime("%Y-%m-%d %H:%M:%S")
    when 'date'
      Date.today.strftime("%Y-%m-%d")
    when 'hello'
      "Hello from Ruby server!"
    when 'help'
      "Available commands: time, date, hello, quit"
    else
      "Unknown command: #{request}. Type 'help' for available commands."
    end
  end
end

# Usage
server = TCPServer.new('localhost', 3000)
server.start
```

### TCP Client

```ruby
require 'socket'

class TCPClient
  def initialize(host = 'localhost', port = 3000)
    @host = host
    @port = port
    @socket = nil
  end
  
  def connect
    @socket = TCPSocket.new(@host, @port)
    puts "Connected to #{@host}:#{@port}"
  end
  
  def send_message(message)
    @socket.puts(message)
  end
  
  def receive_response
    @socket.gets.chomp
  end
  
  def disconnect
    @socket.close if @socket
    puts "Disconnected from server"
  end
  
  def chat
    connect
    
    # Welcome message
    puts receive_response
    
    loop do
      print "> "
      message = gets.chomp
      
      send_message(message)
      break if message.downcase == 'quit'
      
      response = receive_response
      puts response
    end
    
    disconnect
  end
end

# Usage
client = TCPClient.new
client.chat
```

### UDP Server

```ruby
require 'socket'

class UDPServer
  def initialize(host = 'localhost', port = 3001)
    @socket = UDPSocket.new
    @socket.bind(host, port)
    puts "UDP Server started on #{host}:#{port}"
  end
  
  def start
    puts "Waiting for UDP packets..."
    
    loop do
      data, addr = @socket.recvfrom(1024)
      response = process_request(data, addr)
      @socket.send(response, 0, addr[3], addr[1])
    end
  end
  
  private
  
  def process_request(data, addr)
    client_info = "#{addr[3]}:#{addr[1]}"
    puts "Received from #{client_info}: #{data}"
    
    case data.strip.downcase
    when 'ping'
      "pong"
    when 'time'
      Time.now.strftime("%Y-%m-%d %H:%M:%S")
    else
      "Echo: #{data}"
    end
  end
end

# Usage
server = UDPServer.new
server.start
```

### UDP Client

```ruby
require 'socket'

class UDPClient
  def initialize(host = 'localhost', port = 3001)
    @socket = UDPSocket.new
    @host = host
    @port = port
  end
  
  def send_message(message)
    @socket.send(message, 0, @host, @port)
    
    response, = @socket.recvfrom(1024)
    response
  end
  
  def close
    @socket.close
  end
end

# Usage
client = UDPClient.new
response = client.send_message("ping")
puts "Server response: #{response}"

response = client.send_message("Hello UDP!")
puts "Server response: #{response}"

client.close
```

## HTTP Clients

### Net::HTTP

```ruby
require 'net/http'
require 'uri'
require 'json'

class HTTPClient
  def initialize(base_url)
    @base_url = URI(base_url)
    @http = Net::HTTP.new(@base_url.host, @base_url.port)
    @http.use_ssl = @base_url.scheme == 'https'
  end
  
  def get(path, params = {})
    uri = build_uri(path, params)
    request = Net::HTTP::Get.new(uri)
    execute_request(request)
  end
  
  def post(path, data = {})
    uri = build_uri(path)
    request = Net::HTTP::Post.new(uri)
    request['Content-Type'] = 'application/json'
    request.body = data.to_json
    execute_request(request)
  end
  
  def put(path, data = {})
    uri = build_uri(path)
    request = Net::HTTP::Put.new(uri)
    request['Content-Type'] = 'application/json'
    request.body = data.to_json
    execute_request(request)
  end
  
  def delete(path)
    uri = build_uri(path)
    request = Net::HTTP::Delete.new(uri)
    execute_request(request)
  end
  
  private
  
  def build_uri(path, params = {})
    uri = URI("#{@base_url}#{path}")
    uri.query = URI.encode_www_form(params) unless params.empty?
    uri
  end
  
  def execute_request(request)
    response = @http.request(request)
    
    {
      status: response.code.to_i,
      headers: response.to_hash,
      body: response.body
    }
  end
end

# Usage
client = HTTPClient.new('https://jsonplaceholder.typicode.com')

# GET request
response = client.get('/posts/1')
puts "Status: #{response[:status]}"
puts "Body: #{response[:body]}"

# POST request
data = { title: 'New Post', body: 'This is a new post', userId: 1 }
response = client.post('/posts', data)
puts "Status: #{response[:status]}"
puts "Body: #{response[:body]}"
```

### HTTParty Gem

```ruby
require 'httparty'

class APIClient
  include HTTParty
  
  base_uri 'https://jsonplaceholder.typicode.com'
  
  def initialize
    @options = {
      headers: { 'Content-Type' => 'application/json' }
    }
  end
  
  def get_posts
    self.class.get('/posts', @options)
  end
  
  def get_post(id)
    self.class.get("/posts/#{id}", @options)
  end
  
  def create_post(data)
    self.class.post('/posts', @options.merge(body: data.to_json))
  end
  
  def update_post(id, data)
    self.class.put("/posts/#{id}", @options.merge(body: data.to_json))
  end
  
  def delete_post(id)
    self.class.delete("/posts/#{id}", @options)
  end
end

# Usage
client = APIClient.new

# Get all posts
response = client.get_posts
puts "Posts count: #{response.parsed_response.length}"

# Create new post
data = { title: 'New Post', body: 'This is a new post', userId: 1 }
response = client.create_post(data)
puts "Created post ID: #{response.parsed_response['id']}"
```

## Web Services

### REST API Server

```ruby
require 'sinatra'
require 'json'
require 'sqlite3'

class SimpleAPI
  def initialize
    @db = SQLite3::Database.new('api.db')
    setup_database
  end
  
  def setup_database
    @db.execute <<-SQL
      CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
      )
    SQL
  end
  
  def get_users
    @db.execute('SELECT * FROM users ORDER BY created_at DESC').map do |row|
      {
        id: row[0],
        name: row[1],
        email: row[2],
        created_at: row[3]
      }
    end
  end
  
  def get_user(id)
    result = @db.execute('SELECT * FROM users WHERE id = ?', [id])
    return nil if result.empty?
    
    row = result.first
    {
      id: row[0],
      name: row[1],
      email: row[2],
      created_at: row[3]
    }
  end
  
  def create_user(data)
    @db.execute('INSERT INTO users (name, email) VALUES (?, ?)', 
                 [data[:name], data[:email]])
    
    id = @db.last_insert_rowid
    get_user(id)
  end
  
  def update_user(id, data)
    @db.execute('UPDATE users SET name = ?, email = ? WHERE id = ?', 
                 [data[:name], data[:email], id])
    get_user(id)
  end
  
  def delete_user(id)
    @db.execute('DELETE FROM users WHERE id = ?', [id])
    { message: "User #{id} deleted" }
  end
end

# Sinatra application
api = SimpleAPI.new

get '/users' do
  content_type :json
  api.get_users.to_json
end

get '/users/:id' do
  content_type :json
  user = api.get_user(params[:id].to_i)
  
  if user
    user.to_json
  else
    status 404
    { error: 'User not found' }.to_json
  end
end

post '/users' do
  content_type :json
  
  begin
    data = JSON.parse(request.body.read)
    user = api.create_user(data)
    status 201
    user.to_json
  rescue JSON::ParserError
    status 400
    { error: 'Invalid JSON' }.to_json
  end
end

put '/users/:id' do
  content_type :json
  
  begin
    data = JSON.parse(request.body.read)
    user = api.update_user(params[:id].to_i, data)
    
    if user
      user.to_json
    else
      status 404
      { error: 'User not found' }.to_json
    end
  rescue JSON::ParserError
    status 400
    { error: 'Invalid JSON' }.to_json
  end
end

delete '/users/:id' do
  content_type :json
  result = api.delete_user(params[:id].to_i)
  result.to_json
end

# Run with: ruby api_server.rb
```

### SOAP Client

```ruby
require 'savon'

class SOAPClient
  def initialize(wsdl, options = {})
    @client = Savon.client(
      wsdl: wsdl,
      log: options[:log] || false,
      log_level: options[:log_level] || :debug
    )
  end
  
  def call(operation, message = {})
    response = @client.call(operation, message: message)
    
    {
      success: response.success?,
      body: response.body,
      headers: response.headers,
      soap_fault: response.soap_fault?
    }
  end
end

# Example with a sample SOAP service
client = SOAPClient.new('http://www.example.com/wsdl?wsdl')

response = client.call(:get_user, message: {
  user_id: 123
})

if response[:success]
  puts "Response: #{response[:body]}"
else
  puts "SOAP Fault: #{response[:body]}"
end
```

## WebSocket Programming

### WebSocket Server

```ruby
require 'websocket-driver'
require 'thread'

class WebSocketServer
  def initialize(host = 'localhost', port = 8080)
    @host = host
    @port = port
    @clients = []
    @mutex = Mutex.new
  end
  
  def start
    @server = TCPServer.new(@host, @port)
    puts "WebSocket server started on #{@host}:#{@port}"
    
    Thread.new { accept_connections }
  end
  
  def stop
    @server&.close
    puts "WebSocket server stopped"
  end
  
  def broadcast(message)
    @mutex.synchronize do
      @clients.each do |client|
        client.send(message)
      end
    end
  end
  
  private
  
  def accept_connections
    loop do
      client = @server.accept
      Thread.new { handle_client(client) }
    end
  rescue => e
    puts "Error accepting connections: #{e.message}"
  end
  
  def handle_client(socket)
    driver = WebSocket::Driver.rack(socket)
    
    driver.on :open do |ws|
      @mutex.synchronize { @clients << ws }
      puts "Client connected (Total: #{@clients.length})"
      
      ws.send({ type: 'welcome', message: 'Connected to WebSocket server' }.to_json)
    end
    
    driver.on :message do |ws, msg|
      begin
        data = JSON.parse(msg)
        handle_message(ws, data)
      rescue JSON::ParserError
        ws.send({ type: 'error', message: 'Invalid JSON' }.to_json)
      end
    end
    
    driver.on :close do |ws|
      @mutex.synchronize { @clients.delete(ws) }
      puts "Client disconnected (Total: #{@clients.length})"
    end
    
    # Start the WebSocket driver
    driver.start
  end
  
  def handle_message(ws, data)
    case data['type']
    when 'chat'
      broadcast({
        type: 'chat',
        message: data['message'],
        timestamp: Time.now.strftime('%H:%M:%S')
      }.to_json)
      
    when 'ping'
      ws.send({ type: 'pong', timestamp: Time.now.strftime('%H:%M:%S') }.to_json)
      
    when 'echo'
      ws.send({ type: 'echo', message: data['message'] }.to_json)
      
    else
      ws.send({ type: 'error', message: 'Unknown message type' }.to_json)
    end
  end
end

# Usage
server = WebSocketServer.new
server.start

# Keep server running
loop do
  sleep 1
end
```

### WebSocket Client

```ruby
require 'websocket-client'
require 'json'

class WebSocketClient
  def initialize(url)
    @url = url
    @connected = false
    @callbacks = {}
  end
  
  def connect
    @ws = WebSocket::Client::Simple.connect(@url) do |ws|
      @connected = true
      puts "Connected to WebSocket server"
      
      ws.on :message do |msg|
        begin
          data = JSON.parse(msg)
          handle_message(data)
        rescue JSON::ParserError
          puts "Received invalid JSON: #{msg}"
        end
      end
      
      ws.on :close do |event|
        @connected = false
        puts "Disconnected from WebSocket server"
        trigger_callback(:close, event)
      end
      
      ws.on :error do |error|
        puts "WebSocket error: #{error}"
        trigger_callback(:error, error)
      end
    end
  end
  
  def send_message(data)
    if @connected
      @ws.send(data.to_json)
    else
      puts "Not connected to WebSocket server"
    end
  end
  
  def on(event, &block)
    @callbacks[event] = block
  end
  
  def close
    @ws.close if @connected
  end
  
  private
  
  def handle_message(data)
    case data['type']
    when 'welcome'
      puts "Server: #{data['message']}"
    when 'chat'
      puts "[#{data['timestamp']}] #{data['message']}"
    when 'pong'
      puts "Pong received at #{data['timestamp']}"
    when 'echo'
      puts "Echo: #{data['message']}"
    when 'error'
      puts "Error: #{data['message']}"
    end
    
    trigger_callback(:message, data)
  end
  
  def trigger_callback(event, data)
    callback = @callbacks[event]
    callback.call(data) if callback
  end
end

# Usage
client = WebSocketClient.new('ws://localhost:8080')
client.connect

# Set up callbacks
client.on(:message) do |data|
  puts "Received: #{data}"
end

client.on(:close) do |event|
  puts "Connection closed: #{event}"
end

# Send messages
client.send_message({ type: 'chat', message: 'Hello, WebSocket!' })
client.send_message({ type: 'ping' })
client.send_message({ type: 'echo', message: 'Echo this!' })

# Keep client running
sleep 10
client.close
```

## Network Utilities

### DNS Resolution

```ruby
require 'resolv'

class DNSResolver
  def initialize
    @resolver = Resolv::DNS.new
  end
  
  def resolve(hostname)
    begin
      addresses = @resolver.getaddresses(hostname)
      {
        hostname: hostname,
        addresses: addresses,
        success: true
      }
    rescue Resolv::ResolvError => e
      {
        hostname: hostname,
        error: e.message,
        success: false
      }
    end
  end
  
  def reverse_lookup(ip_address)
    begin
      hostname = @resolver.getname(ip_address)
      {
        ip_address: ip_address,
        hostname: hostname,
        success: true
      }
    rescue Resolv::ResolvError => e
      {
        ip_address: ip_address,
        error: e.message,
        success: false
      }
    end
  end
  
  def mx_records(domain)
    begin
      records = @resolver.getresources(domain, Resolv::DNS::Resource::IN::MX)
      {
        domain: domain,
        mx_records: records.map { |r| { preference: r.preference, exchange: r.exchange } },
        success: true
      }
    rescue Resolv::ResolvError => e
      {
        domain: domain,
        error: e.message,
        success: false
      }
    end
  end
end

# Usage
resolver = DNSResolver.new

# Forward lookup
result = resolver.resolve('google.com')
if result[:success]
  puts "Google.com addresses: #{result[:addresses].join(', ')}"
else
  puts "Error: #{result[:error]}"
end

# Reverse lookup
result = resolver.reverse_lookup('8.8.8.8')
if result[:success]
  puts "8.8.8.8 resolves to: #{result[:hostname]}"
else
  puts "Error: #{result[:error]}"
end
```

### Network Scanning

```ruby
require 'socket'
require 'timeout'

class PortScanner
  def initialize(target, start_port = 1, end_port = 1024)
    @target = target
    @start_port = start_port
    @end_port = end_port
    @open_ports = []
  end
  
  def scan
    puts "Scanning #{@target} ports #{@start_port}-#{@end_port}..."
    
    (@start_port..@end_port).each do |port|
      if port_open?(@target, port)
        @open_ports << port
        puts "Port #{port} is open"
      end
    end
    
    puts "Scan completed. Open ports: #{@open_ports.join(', ')}"
    @open_ports
  end
  
  def scan_concurrent(threads = 50)
    puts "Scanning #{@target} ports #{@start_port}-#{@end_port} concurrently..."
    
    threads = []
    mutex = Mutex.new
    
    (@start_port..@end_port).each_slice(threads) do |ports|
      threads << Thread.new do
        ports.each do |port|
          if port_open?(@target, port)
            mutex.synchronize do
              @open_ports << port
              puts "Port #{port} is open"
            end
          end
        end
      end
    end
    
    threads.each(&:join)
    
    puts "Scan completed. Open ports: #{@open_ports.join(', ')}"
    @open_ports
  end
  
  private
  
  def port_open?(host, port, timeout = 1)
    begin
      Timeout::timeout(timeout) do
        TCPSocket.new(host, port).close
      end
      true
    rescue Errno::ECONNREFUSED, Errno::EHOSTUNREACH, Timeout::Error
      false
    end
  end
end

# Usage
scanner = PortScanner.new('localhost', 1, 1000)
scanner.scan

# Concurrent scanning (faster)
scanner = PortScanner.new('localhost', 1, 1000)
scanner.scan_concurrent(100)
```

## Network Monitoring

### Network Statistics

```ruby
class NetworkMonitor
  def initialize
    @stats = {
      packets_sent: 0,
      packets_received: 0,
      bytes_sent: 0,
      bytes_received: 0,
      connections: 0,
      start_time: Time.now
    }
  end
  
  def record_packet_sent(bytes)
    @stats[:packets_sent] += 1
    @stats[:bytes_sent] += bytes
  end
  
  def record_packet_received(bytes)
    @stats[:packets_received] += 1
    @stats[:bytes_received] += bytes
  end
  
  def record_connection
    @stats[:connections] += 1
  end
  
  def get_stats
    uptime = Time.now - @stats[:start_time]
    
    @stats.merge(
      uptime: uptime,
      packets_per_second: (@stats[:packets_sent] + @stats[:packets_received]) / uptime,
      bytes_per_second: (@stats[:bytes_sent] + @stats[:bytes_received]) / uptime
    )
  end
  
  def reset
    @stats[:packets_sent] = 0
    @stats[:packets_received] = 0
    @stats[:bytes_sent] = 0
    @stats[:bytes_received] = 0
    @stats[:connections] = 0
    @stats[:start_time] = Time.now
  end
end

# Usage
monitor = NetworkMonitor.new

# Simulate network activity
10.times do |i|
  monitor.record_packet_sent(1024)
  monitor.record_packet_received(512)
  monitor.record_connection
end

stats = monitor.get_stats
puts "Network Statistics:"
puts "Uptime: #{stats[:uptime].round(2)} seconds"
puts "Packets sent: #{stats[:packets_sent]}"
puts "Packets received: #{stats[:packets_received]}"
puts "Bytes sent: #{stats[:bytes_sent]}"
puts "Bytes received: #{stats[:bytes_received]}"
puts "Connections: #{stats[:connections]}"
puts "Packets per second: #{stats[:packets_per_second].round(2)}"
puts "Bytes per second: #{stats[:bytes_per_second].round(2)}"
```

## Best Practices

### 1. Use Timeouts

```ruby
# Always use timeouts for network operations
require 'timeout'

begin
  Timeout::timeout(5) do
    response = Net::HTTP.get_response(URI('http://example.com'))
  end
rescue Timeout::Error
  puts "Request timed out"
end
```

### 2. Handle Network Errors

```ruby
begin
  response = Net::HTTP.get_response(URI('http://example.com'))
rescue SocketError => e
  puts "Socket error: #{e.message}"
rescue Net::HTTPBadResponse => e
  puts "HTTP error: #{e.message}"
rescue => e
  puts "Network error: #{e.message}"
end
```

### 3. Use Connection Pooling

```ruby
# Reuse connections when possible
class ConnectionPool
  def initialize(size = 5)
    @pool = Queue.new
    size.times { @pool << create_connection }
  end
  
  def with_connection
    conn = @pool.pop
    begin
      yield conn
    ensure
      @pool.push(conn)
    end
  end
  
  private
  
  def create_connection
    Net::HTTP.new('example.com')
  end
end
```

## Practice Exercises

### Exercise 1: Chat Server
Build a chat server with:
- Multiple rooms
- User authentication
- Message history
- File sharing

### Exercise 2: REST API Client
Create a REST API client for:
- GitHub API integration
- Rate limiting
- Error handling
- Response caching

### Exercise 3: Network Scanner
Develop a network scanner with:
- Port scanning
- Service detection
- Network mapping
- Vulnerability checking

### Exercise 4: WebSocket Application
Build a real-time application with:
- Live notifications
- Real-time updates
- Multi-user collaboration
- File synchronization

---

**Ready to explore more advanced Ruby topics? Let's continue! 🌐**
