# Socket Programming in Ruby

## Overview

Socket programming enables network communication between applications. Ruby provides powerful socket libraries for building networked applications, from simple client-server systems to complex distributed systems.

## Basic Socket Concepts

### What are Sockets?
Sockets are endpoints for communication between processes over a network. They provide a standardized interface for network programming.

### Socket Types
- **TCP Sockets**: Reliable, connection-oriented communication
- **UDP Sockets**: Fast, connectionless communication
- **UNIX Sockets**: Local inter-process communication

## TCP Socket Programming

### Basic TCP Server
```ruby
require 'socket'

server = TCPServer.new('localhost', 3000)
puts "Server running on localhost:3000"

loop do
  client = server.accept
  client.puts "Hello from TCP server!"
  client.close
end
```

### Basic TCP Client
```ruby
require 'socket'

socket = TCPSocket.new('localhost', 3000)
response = socket.gets
puts response
socket.close
```

### Advanced TCP Server
```ruby
require 'socket'

class TCPServerAdvanced
  def initialize(host, port)
    @server = TCPServer.new(host, port)
    @clients = []
    puts "Advanced server running on #{host}:#{port}"
  end

  def start
    loop do
      Thread.start(@server.accept) do |client|
        handle_client(client)
      end
    end
  end

  private

  def handle_client(client)
    @clients << client
    client_id = client.object_id
    
    puts "Client #{client_id} connected"
    
    while message = client.gets
      message = message.chomp
      puts "Client #{client_id}: #{message}"
      
      # Echo back to all clients
      @clients.each do |c|
        c.puts "Client #{client_id}: #{message}"
      end
    end
    
    @clients.delete(client)
    client.close
    puts "Client #{client_id} disconnected"
  end
end

server = TCPServerAdvanced.new('localhost', 3000)
server.start
```

## UDP Socket Programming

### UDP Server
```ruby
require 'socket'

socket = UDPSocket.new
socket.bind('localhost', 3000)

puts "UDP server listening on localhost:3000"

loop do
  data, addr = socket.recvfrom(1024)
  puts "Received from #{addr[2]}:#{addr[1]}: #{data}"
  
  response = "Echo: #{data}"
  socket.send(response, 0, addr[2], addr[1])
end
```

### UDP Client
```ruby
require 'socket'

socket = UDPSocket.new

3.times do |i|
  message = "Message #{i + 1}"
  socket.send(message, 0, 'localhost', 3000)
  
  response, addr = socket.recvfrom(1024)
  puts "Server response: #{response}"
  
  sleep(1)
end

socket.close
```

## HTTP Socket Programming

### Simple HTTP Server
```ruby
require 'socket'

class HTTPServer
  def initialize(port = 8080)
    @server = TCPServer.new('localhost', port)
    puts "HTTP server running on port #{port}"
  end

  def start
    loop do
      client = @server.accept
      handle_request(client)
    end
  end

  private

  def handle_request(client)
    request = client.gets
    method, path, version = request.split(' ')
    
    puts "#{method} #{path} #{version}"
    
    response = case path
              when '/'
                html_response("Hello World!", "Welcome to Ruby HTTP Server")
              when '/about'
                html_response("About", "This is a simple HTTP server in Ruby")
              when '/time'
                html_response("Current Time", Time.now.to_s)
              else
                not_found_response
              end
    
    client.puts response
    client.close
  end

  def html_response(title, content)
    <<~HTTP
      HTTP/1.1 200 OK
      Content-Type: text/html
      Connection: close
      
      <!DOCTYPE html>
      <html>
        <head><title>#{title}</title></head>
        <body>
          <h1>#{title}</h1>
          <p>#{content}</p>
          <p><a href="/">Home</a></p>
        </body>
      </html>
    HTTP
  end

  def not_found_response
    <<~HTTP
      HTTP/1.1 404 Not Found
      Content-Type: text/html
      Connection: close
      
      <!DOCTYPE html>
      <html>
        <head><title>Not Found</title></head>
        <body>
          <h1>404 - Page Not Found</h1>
          <p><a href="/">Home</a></p>
        </body>
      </html>
    HTTP
  end
end

server = HTTPServer.new
server.start
```

### HTTP Client
```ruby
require 'socket'

class HTTPClient
  def initialize(host, port = 80)
    @host = host
    @port = port
  end

  def get(path)
    socket = TCPSocket.new(@host, @port)
    
    request = "GET #{path} HTTP/1.1\r\n"
    request += "Host: #{@host}\r\n"
    request += "Connection: close\r\n"
    request += "\r\n"
    
    socket.puts request
    response = socket.read
    socket.close
    
    parse_response(response)
  end

  def post(path, data)
    socket = TCPSocket.new(@host, @port)
    
    request = "POST #{path} HTTP/1.1\r\n"
    request += "Host: #{@host}\r\n"
    request += "Content-Type: application/json\r\n"
    request += "Content-Length: #{data.length}\r\n"
    request += "Connection: close\r\n"
    request += "\r\n"
    request += data
    
    socket.puts request
    response = socket.read
    socket.close
    
    parse_response(response)
  end

  private

  def parse_response(response)
    headers, body = response.split("\r\n\r\n", 2)
    status_line = headers.split("\n").first
    
    {
      status: status_line,
      headers: parse_headers(headers),
      body: body
    }
  end

  def parse_headers(headers)
    header_lines = headers.split("\n")[1..-1]
    header_lines.each_with_object({}) do |line, hash|
      key, value = line.split(": ", 2)
      hash[key] = value
    end
  end
end

# Usage example
client = HTTPClient.new('httpbin.org', 80)
response = client.get('/get')
puts response[:status]
puts response[:body]
```

## WebSocket Programming

### WebSocket Server
```ruby
require 'socket'
require 'digest/sha1'
require 'base64'

class WebSocketServer
  def initialize(port = 8080)
    @server = TCPServer.new('localhost', port)
    @clients = []
    puts "WebSocket server running on port #{port}"
  end

  def start
    loop do
      client = @server.accept
      Thread.start { handle_client(client) }
    end
  end

  private

  def handle_client(client)
    request = client.gets
    
    if request&.include?('Upgrade: websocket')
      perform_handshake(client)
      @clients << client
      handle_websocket_messages(client)
    else
      client.close
    end
  end

  def perform_handshake(client)
    headers = []
    while line = client.gets
      break if line == "\r\n"
      headers << line.chomp
    end

    key = nil
    headers.each do |header|
      if header.start_with?('Sec-WebSocket-Key:')
        key = header.split(': ')[1]
        break
      end
    end

    return unless key

    accept_key = generate_accept_key(key)
    
    response = "HTTP/1.1 101 Switching Protocols\r\n"
    response += "Upgrade: websocket\r\n"
    response += "Connection: Upgrade\r\n"
    response += "Sec-WebSocket-Accept: #{accept_key}\r\n"
    response += "\r\n"
    
    client.puts response
  end

  def generate_accept_key(key)
    magic_string = "258EAFA5-E914-47DA-95CA-C5AB0DC85B11"
    sha1 = Digest::SHA1.hexdigest(key + magic_string)
    Base64.strict_encode64([sha1].pack('H*'))
  end

  def handle_websocket_messages(client)
    loop do
      frame = receive_frame(client)
      break unless frame
      
      message = decode_frame(frame)
      puts "Received: #{message}"
      
      # Echo back to all clients
      @clients.each do |c|
        send_message(c, "Echo: #{message}")
      end
    end
    
    @clients.delete(client)
    client.close
  end

  def receive_frame(client)
    first_byte = client.readbyte
    second_byte = client.readbyte
    
    fin = (first_byte & 0x80) != 0
    opcode = first_byte & 0x0f
    masked = (second_byte & 0x80) != 0
    payload_length = second_byte & 0x7f
    
    case payload_length
    when 126
      payload_length = client.read(2).unpack('n')[0]
    when 127
      payload_length = client.read(8).unpack('Q>')[0]
    end
    
    mask = nil
    if masked
      mask = client.read(4).bytes
    end
    
    payload = client.read(payload_length)
    
    if masked
      payload = payload.bytes.each_with_index.map { |byte, i| byte ^ mask[i % 4] }.pack('C*')
    end
    
    payload
  end

  def decode_frame(frame)
    frame
  end

  def send_message(client, message)
    frame = encode_frame(message)
    client.write(frame)
  end

  def encode_frame(message)
    payload = message.force_encoding('UTF-8')
    payload_length = payload.bytes.length
    
    frame = ''
    frame << [0x81].pack('C')  # FIN=1, opcode=text
    
    case payload_length
    when 0..125
      frame << [payload_length].pack('C')
    when 126..65535
      frame << [126].pack('C') + [payload_length].pack('n')
    else
      frame << [127].pack('C') + [payload_length].pack('Q>')
    end
    
    frame << payload
    frame
  end
end

server = WebSocketServer.new
server.start
```

## Network Utilities

### Port Scanner
```ruby
require 'socket'

class PortScanner
  def initialize(host)
    @host = host
  end

  def scan_port(port, timeout = 1)
    begin
      socket = TCPSocket.new(@host, port)
      socket.close
      true
    rescue Errno::ECONNREFUSED, Errno::ETIMEDOUT
      false
    end
  end

  def scan_range(start_port, end_port)
    open_ports = []
    
    (start_port..end_port).each do |port|
      print "Scanning port #{port}...\r"
      if scan_port(port)
        open_ports << port
        puts "Port #{port} is open"
      end
    end
    
    open_ports
  end

  def scan_common_ports
    common_ports = [21, 22, 23, 25, 53, 80, 110, 143, 443, 993, 995]
    scan_range(common_ports.min, common_ports.max)
  end
end

scanner = PortScanner.new('localhost')
open_ports = scanner.scan_common_ports
puts "Open ports: #{open_ports.join(', ')}"
```

### Network Information
```ruby
require 'socket'

def get_local_ip
  Socket.ip_address_list.find { |ai| ai.ipv4? && !ai.ipv4_loopback? }.ip_address
end

def get_hostname
  Socket.gethostname
end

def resolve_hostname(hostname)
  Socket.getaddrinfo(hostname, nil, nil, :STREAM).map { |info| info[3] }
end

puts "Local IP: #{get_local_ip}"
puts "Hostname: #{get_hostname}"
puts "Google IPs: #{resolve_hostname('google.com').join(', ')}"
```

## Error Handling

### Common Network Errors
```ruby
require 'socket'

class RobustClient
  def initialize(host, port)
    @host = host
    @port = port
  end

  def connect_with_retry(max_retries = 3, delay = 1)
    attempts = 0
    
    begin
      attempts += 1
      socket = TCPSocket.new(@host, @port)
      puts "Connected successfully after #{attempts} attempts"
      return socket
    rescue Errno::ECONNREFUSED
      puts "Connection refused. Retrying in #{delay} seconds..."
      sleep(delay)
      retry if attempts < max_retries
    rescue Errno::ETIMEDOUT
      puts "Connection timed out. Retrying in #{delay} seconds..."
      sleep(delay)
      retry if attempts < max_retries
    rescue SocketError => e
      puts "Socket error: #{e.message}"
      return nil
    end
    
    puts "Failed to connect after #{max_retries} attempts"
    nil
  end
end

client = RobustClient.new('localhost', 3000)
socket = client.connect_with_retry
```

## Best Practices

1. **Always close sockets**: Use `ensure` blocks or context managers
2. **Handle timeouts**: Set appropriate timeouts to prevent hanging
3. **Use threads**: Handle multiple clients concurrently
4. **Validate input**: Sanitize network input to prevent attacks
5. **Error handling**: Handle network errors gracefully
6. **Resource management**: Limit concurrent connections
7. **Security**: Use SSL/TLS for sensitive data

## Conclusion

Ruby's socket programming capabilities provide everything needed for building robust network applications. From simple TCP/UDP sockets to complex WebSocket servers, Ruby makes network programming accessible and efficient.

## Further Reading

- [Ruby Socket Documentation](https://ruby-doc.org/stdlib-3.0.0/libdoc/socket/rdoc/Socket.html)
- [WebSocket Protocol RFC](https://tools.ietf.org/html/rfc6455)
- [HTTP/1.1 RFC](https://tools.ietf.org/html/rfc7231)
