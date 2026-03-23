# Networking Examples
# Demonstrating socket programming, HTTP clients, and network protocols

puts "=== TCP SERVER AND CLIENT ==="

require 'socket'
require 'thread'

# Simple TCP Server
class TCPServer
  def initialize(host = 'localhost', port = 3000)
    @host = host
    @port = port
    @server = nil
    @clients = []
    @running = false
  end
  
  def start
    @server = TCPServer.new(@host, @port)
    @running = true
    
    puts "TCP Server started on #{@host}:#{@port}"
    puts "Waiting for connections..."
    
    while @running
      begin
        client = @server.accept_nonblock
        handle_client(client)
      rescue IO::WaitReadable
        sleep(0.1)
      rescue => e
        puts "Error accepting connection: #{e.message}"
      end
    end
  end
  
  def stop
    @running = false
    @server&.close
    puts "TCP Server stopped"
  end
  
  def broadcast(message)
    @clients.each do |client|
      begin
        client.puts(message)
      rescue => e
        puts "Error broadcasting to client: #{e.message}"
        @clients.delete(client)
      end
    end
  end
  
  private
  
  def handle_client(client)
    @clients << client
    
    Thread.new do
      begin
        client.puts "Welcome to Ruby TCP Server!"
        client.puts "Type 'quit' to disconnect"
        client.puts "Type 'broadcast:message' to broadcast to all clients"
        
        while line = client.gets
          line = line.chomp
          break if line.downcase == 'quit'
          
          if line.start_with?('broadcast:')
            message = line.sub('broadcast:', '')
            broadcast("#{client.peeraddr[2]}: #{message}")
          else
            response = process_request(line, client)
            client.puts(response)
          end
        end
        
      rescue => e
        puts "Error handling client: #{e.message}"
      ensure
        @clients.delete(client)
        client.close
        puts "Client disconnected: #{client.peeraddr[2]}"
      end
    end
  end
  
  def process_request(request, client)
    client_ip = client.peeraddr[2]
    
    case request.downcase
    when 'time'
      "Server time: #{Time.now.strftime('%Y-%m-%d %H:%M:%S')}"
    when 'date'
      "Today's date: #{Date.today.strftime('%Y-%m-%d')}"
    when 'hello'
      "Hello from #{client_ip}!"
    when 'clients'
      "Connected clients: #{@clients.length}"
    when 'help'
      "Available commands: time, date, hello, clients, quit, broadcast:message"
    else
      "Unknown command: #{request}. Type 'help' for available commands."
    end
  end
end

# TCP Client
class TCPClient
  def initialize(host = 'localhost', port = 3000)
    @host = host
    @port = port
    @socket = nil
  end
  
  def connect
    @socket = TCPSocket.new(@host, @port)
    puts "Connected to #{@host}:#{@port}"
    
    # Read welcome message
    welcome = @socket.gets.chomp
    puts "Server: #{welcome}"
  end
  
  def send_message(message)
    return false unless @socket
    
    @socket.puts(message)
    true
  end
  
  def receive_response
    return nil unless @socket
    
    @socket.gets.chomp
  end
  
  def disconnect
    @socket&.close
    @socket = nil
    puts "Disconnected from server"
  end
  
  def chat
    connect
    
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

puts "TCP Server/Client Example:"

# Start server in background
server_thread = Thread.new do
  server = TCPServer.new
  server.start
end

sleep(1)  # Let server start

# Test client
client = TCPClient.new
client.send_message("hello")
response = client.receive_response
puts "Client received: #{response}"

client.send_message("time")
response = client.receive_response
puts "Client received: #{response}"

client.disconnect

server_thread.exit

puts "\n=== HTTP CLIENT ==="

require 'net/http'
require 'uri'
require 'json'

class HTTPClient
  def initialize(base_url)
    @base_url = URI(base_url)
    @http = Net::HTTP.new(@base_url.host, @base_url.port)
    @http.use_ssl = @base_url.scheme == 'https'
    @http.read_timeout = 30
    @http.open_timeout = 10
  end
  
  def get(path, params = {})
    uri = build_uri(path, params)
    request = Net::HTTP::Get.new(uri)
    add_headers(request)
    
    execute_request(request)
  end
  
  def post(path, data = {})
    uri = build_uri(path)
    request = Net::HTTP::Post.new(uri)
    add_headers(request)
    request.body = data.to_json
    
    execute_request(request)
  end
  
  def put(path, data = {})
    uri = build_uri(path)
    request = Net::HTTP::Put.new(uri)
    add_headers(request)
    request.body = data.to_json
    
    execute_request(request)
  end
  
  def delete(path)
    uri = build_uri(path)
    request = Net::HTTP::Delete.new(uri)
    add_headers(request)
    
    execute_request(request)
  end
  
  def download_file(url, save_path)
    uri = URI(url)
    
    Net::HTTP.start(uri.host, uri.port, use_ssl: uri.scheme == 'https') do |http|
      request = Net::HTTP::Get.new(uri)
      
      http.request(request) do |response|
        case response
        when Net::HTTPSuccess
          File.open(save_path, 'wb') do |file|
            response.read_body { |chunk| file.write(chunk) }
          end
          return true
        else
          puts "Download failed: #{response.code} #{response.message}"
          return false
        end
      end
    end
  end
  
  private
  
  def build_uri(path, params = {})
    uri = URI("#{@base_url}#{path}")
    uri.query = URI.encode_www_form(params) unless params.empty?
    uri
  end
  
  def add_headers(request)
    request['User-Agent'] = 'Ruby HTTP Client 1.0'
    request['Accept'] = 'application/json'
    request['Content-Type'] = 'application/json'
  end
  
  def execute_request(request)
    begin
      response = @http.request(request)
      
      {
        status: response.code.to_i,
        status_text: response.message,
        headers: response.to_hash,
        body: response.body,
        success: response.is_a?(Net::HTTPSuccess)
      }
    rescue => e
      {
        status: 0,
        status_text: e.message,
        headers: {},
        body: '',
        success: false
      }
    end
  end
end

puts "HTTP Client Example:"

# Test with a public API
client = HTTPClient.new('https://jsonplaceholder.typicode.com')

# GET request
response = client.get('/posts/1')
puts "GET /posts/1:"
puts "Status: #{response[:status]}"
puts "Success: #{response[:success]}"
puts "Body: #{response[:body][0..100]}..." if response[:body].length > 100

# POST request
data = { title: 'Ruby Test Post', body: 'This is a test post from Ruby', userId: 1 }
response = client.post('/posts', data)
puts "\nPOST /posts:"
puts "Status: #{response[:status]}"
puts "Success: #{response[:success]}"
puts "Body: #{response[:body][0..100]}..." if response[:body].length > 100

puts "\n=== WEBHOOK HANDLER ==="

class WebhookHandler
  def initialize(port = 8080)
    @port = port
    @handlers = {}
    @server = nil
  end
  
  def start
    @server = TCPServer.new('localhost', @port)
    puts "Webhook server started on port #{@port}"
    
    loop do
      client = @server.accept
      Thread.new { handle_request(client) }
    end
  end
  
  def add_handler(path, &block)
    @handlers[path] = block
    puts "Added webhook handler for: #{path}"
  end
  
  private
  
  def handle_request(client)
    request_line = client.gets
    method, path, version = request_line.split(' ')
    
    # Read headers
    headers = {}
    while line = client.gets
      break if line.strip.empty?
      key, value = line.split(':', 2)
      headers[key.strip] = value.strip if key && value
    end
    
    # Read body
    body = ''
    content_length = headers['Content-Length']
    if content_length
      body = client.read(content_length.to_i)
    end
    
    # Generate response
    response = handle_webhook(method, path, headers, body)
    
    # Send response
    client.print "HTTP/1.1 #{response[:status]} #{response[:status_text]}\r\n"
    response[:headers].each { |key, value| client.print "#{key}: #{value}\r\n" }
    client.print "\r\n"
    client.print response[:body]
    
    client.close
  end
  
  def handle_webhook(method, path, headers, body)
    handler = @handlers[path]
    
    if handler
      begin
        result = handler.call(method, headers, body)
        {
          status: 200,
          status_text: 'OK',
          headers: { 'Content-Type' => 'application/json' },
          body: { success: true, result: result }.to_json
        }
      rescue => e
        {
          status: 500,
          status_text: 'Internal Server Error',
          headers: { 'Content-Type' => 'application/json' },
          body: { success: false, error: e.message }.to_json
        }
      end
    else
      {
        status: 404,
        status_text: 'Not Found',
        headers: { 'Content-Type' => 'application/json' },
        body: { success: false, error: 'Webhook not found' }.to_json
      }
    end
  end
end

puts "Webhook Handler Example:"

# Create webhook server
webhook_server = Thread.new do
  server = WebhookHandler.new(8080)
  
  # Add webhook handlers
  server.add_handler('/webhook/github') do |method, headers, body|
    puts "GitHub webhook received: #{method}"
    puts "Headers: #{headers.keys.join(', ')}"
    puts "Body: #{body[0..100]}..."
    
    { processed: true, timestamp: Time.now }
  end
  
  server.add_handler('/webhook/slack') do |method, headers, body|
    puts "Slack webhook received: #{method}"
    
    { acknowledged: true }
  end
  
  server.start
end

sleep(1)  # Let server start

# Simulate webhook calls
require 'net/http'

def send_webhook(port, path, data)
  uri = URI("http://localhost:#{port}#{path}")
  
  http = Net::HTTP.new(uri.host, uri.port)
  request = Net::HTTP::Post.new(uri)
  request['Content-Type'] = 'application/json'
  request.body = data.to_json
  
  response = http.request(request)
  puts "Webhook sent to #{path}: #{response.code}"
end

# Send webhook calls
send_webhook(8080, '/webhook/github', { event: 'push', repository: 'test-repo' })
send_webhook(8080, '/webhook/slack', { text: 'Hello from Ruby!' })

webhook_server.exit

puts "\n=== PORT SCANNER ==="

class PortScanner
  def initialize(target, start_port = 1, end_port = 1024)
    @target = target
    @start_port = start_port
    @end_port = end_port
    @open_ports = []
    @timeout = 1
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
    
    thread_pool = []
    mutex = Mutex.new
    
    (@start_port..@end_port).each_slice(threads) do |port_range|
      thread = Thread.new do
        port_range.each do |port|
          if port_open?(@target, port)
            mutex.synchronize do
              @open_ports << port
              puts "Port #{port} is open"
            end
          end
        end
      end
      
      thread_pool << thread
    end
    
    thread_pool.each(&:join)
    
    puts "Scan completed. Open ports: #{@open_ports.join(', ')}"
    @open_ports
  end
  
  def service_detection(port)
    common_services = {
      21 => 'FTP',
      22 => 'SSH',
      23 => 'Telnet',
      25 => 'SMTP',
      53 => 'DNS',
      80 => 'HTTP',
      110 => 'POP3',
      143 => 'IMAP',
      443 => 'HTTPS',
      993 => 'IMAPS',
      995 => 'POP3S'
    }
    
    common_services[port] || 'Unknown'
  end
  
  def detailed_scan
    open_ports = scan_concurrent
    
    puts "\nService Detection:"
    open_ports.each do |port|
      service = service_detection(port)
      puts "Port #{port}: #{service}"
    end
    
    open_ports
  end
  
  private
  
  def port_open?(host, port)
    begin
      Timeout::timeout(@timeout) do
        socket = TCPSocket.new(host, port)
        socket.close
        true
      end
    rescue Errno::ECONNREFUSED, Errno::EHOSTUNREACH, Timeout::Error
      false
    end
  end
end

puts "Port Scanner Example:"

# Scan localhost (limited range for demo)
scanner = PortScanner.new('localhost', 3000, 3010)
open_ports = scanner.scan

puts "\n=== DNS RESOLVER ==="

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
  
  def ns_records(domain)
    begin
      records = @resolver.getresources(domain, Resolv::DNS::Resource::IN::NS)
      {
        domain: domain,
        ns_records: records.map(&:name),
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
  
  def txt_records(domain)
    begin
      records = @resolver.getresources(domain, Resolv::DNS::Resource::IN::TXT)
      {
        domain: domain,
        txt_records: records.map { |r| r.strings.join('') },
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

puts "DNS Resolver Example:"

resolver = DNSResolver.new

# Forward lookup
result = resolver.resolve('localhost')
if result[:success]
  puts "localhost addresses: #{result[:addresses].join(', ')}"
else
  puts "Error resolving localhost: #{result[:error]}"
end

# Reverse lookup
result = resolver.reverse_lookup('127.0.0.1')
if result[:success]
  puts "127.0.0.1 resolves to: #{result[:hostname]}"
else
  puts "Error reverse lookup: #{result[:error]}"
end

# MX records (may not work for localhost)
result = resolver.mx_records('gmail.com')
if result[:success]
  puts "Gmail MX records:"
  result[:mx_records].each { |mx| puts "  #{mx[:exchange]} (preference: #{mx[:preference]})" }
else
  puts "MX lookup error: #{result[:error]}"
end

puts "\n=== NETWORK MONITOR ==="

class NetworkMonitor
  def initialize
    @stats = {
      connections: 0,
      bytes_sent: 0,
      bytes_received: 0,
      start_time: Time.now
    }
    @mutex = Mutex.new
  end
  
  def record_connection
    @mutex.synchronize do
      @stats[:connections] += 1
    end
  end
  
  def record_bytes_sent(bytes)
    @mutex.synchronize do
      @stats[:bytes_sent] += bytes
    end
  end
  
  def record_bytes_received(bytes)
    @mutex.synchronize do
      @stats[:bytes_received] += bytes
    end
  end
  
  def get_stats
    @mutex.synchronize do
      uptime = Time.now - @stats[:start_time]
      total_bytes = @stats[:bytes_sent] + @stats[:bytes_received]
      
      @stats.merge(
        uptime: uptime,
        total_bytes: total_bytes,
        bytes_per_second: uptime > 0 ? total_bytes / uptime : 0,
        connections_per_second: uptime > 0 ? @stats[:connections] / uptime : 0
      )
    end
  end
  
  def reset
    @mutex.synchronize do
      @stats = {
        connections: 0,
        bytes_sent: 0,
        bytes_received: 0,
        start_time: Time.now
      }
    end
  end
end

class MonitoredServer
  def initialize(port = 3001)
    @port = port
    @monitor = NetworkMonitor.new
    @server = nil
  end
  
  def start
    @server = TCPServer.new('localhost', @port)
    puts "Monitored server started on port #{@port}"
    
    loop do
      client = @server.accept
      @monitor.record_connection
      
      Thread.new do
        handle_client(client)
      end
    end
  end
  
  private
  
  def handle_client(client)
    begin
      # Simulate receiving data
      data = client.gets
      @monitor.record_bytes_received(data.length) if data
      
      # Simulate sending response
      response = "Echo: #{data}"
      client.puts(response)
      @monitor.record_bytes_sent(response.length)
      
    rescue => e
      puts "Error handling client: #{e.message}"
    ensure
      client.close
    end
  end
end

puts "Network Monitor Example:"

# Start monitored server
monitor_thread = Thread.new do
  server = MonitoredServer.new(3001)
  server.start
end

sleep(1)  # Let server start

# Connect to monitored server
3.times do |i|
  client = TCPSocket.new('localhost', 3001)
  client.puts("Test message #{i}")
  response = client.gets
  puts "Received: #{response.strip}"
  client.close
end

# Show stats
monitor = NetworkMonitor.new
stats = monitor.get_stats
puts "Network Stats:"
puts "  Connections: #{stats[:connections]}"
puts "  Bytes sent: #{stats[:bytes_sent]}"
puts "  Bytes received: #{stats[:bytes_received]}"
puts "  Total bytes: #{stats[:total_bytes]}"
puts "  Uptime: #{stats[:uptime].round(2)}s"

monitor_thread.exit

puts "\n=== NETWORKING SUMMARY ==="
puts "- TCP Server/Client: Socket programming, concurrent connections"
puts "- HTTP Client: REST API calls, file downloads, error handling"
puts "- Webhook Handler: HTTP server, request parsing, response generation"
puts "- Port Scanner: Network discovery, concurrent scanning, service detection"
puts "- DNS Resolver: Forward/reverse lookups, record types, error handling"
puts "- Network Monitor: Connection tracking, bandwidth monitoring, statistics"
puts "\nAll examples demonstrate comprehensive networking concepts in Ruby!"
