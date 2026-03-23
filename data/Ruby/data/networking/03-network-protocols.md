# Network Protocols in Ruby

## Overview

Network protocols define the rules and conventions for communication between devices on a network. Ruby provides libraries and tools for implementing various network protocols, from low-level TCP/IP to high-level application protocols.

## Protocol Layers

### OSI Model
1. **Physical Layer**: Hardware transmission
2. **Data Link Layer**: MAC addresses, Ethernet
3. **Network Layer**: IP addressing, routing
4. **Transport Layer**: TCP, UDP
5. **Session Layer**: Session management
6. **Presentation Layer**: Data formatting
7. **Application Layer**: HTTP, FTP, SMTP, etc.

### TCP/IP Model
1. **Link Layer**: Ethernet, Wi-Fi
2. **Internet Layer**: IP, ICMP
3. **Transport Layer**: TCP, UDP
4. **Application Layer**: HTTP, FTP, SMTP

## HTTP/HTTPS Implementation

### HTTP Client with SSL
```ruby
require 'socket'
require 'openssl'

class HTTPSClient
  def initialize(host, port = 443)
    @host = host
    @port = port
  end

  def get(path)
    tcp_socket = TCPSocket.new(@host, @port)
    ssl_socket = OpenSSL::SSL::SSLSocket.new(tcp_socket)
    ssl_socket.connect
    
    request = "GET #{path} HTTP/1.1\r\n"
    request += "Host: #{@host}\r\n"
    request += "Connection: close\r\n"
    request += "User-Agent: Ruby HTTPS Client\r\n"
    request += "\r\n"
    
    ssl_socket.puts request
    response = ssl_socket.read
    
    ssl_socket.close
    tcp_socket.close
    
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

client = HTTPSClient.new('www.google.com')
response = client.get('/')
puts response[:status]
```

### HTTP/2 Support
```ruby
require 'net/http'
require 'uri'

class HTTP2Client
  def initialize(base_url)
    @base_url = base_url
    @uri = URI.parse(base_url)
  end

  def get(path)
    http = Net::HTTP.new(@uri.host, @uri.port)
    http.use_ssl = (@uri.scheme == 'https')
    
    request = Net::HTTP::Get.new(path)
    request['Accept'] = 'application/json'
    request['User-Agent'] = 'Ruby HTTP/2 Client'
    
    response = http.request(request)
    
    {
      status: response.code,
      headers: response.to_hash,
      body: response.body
    }
  end

  def post(path, data)
    http = Net::HTTP.new(@uri.host, @uri.port)
    http.use_ssl = (@uri.scheme == 'https')
    
    request = Net::HTTP::Post.new(path)
    request['Content-Type'] = 'application/json'
    request['User-Agent'] = 'Ruby HTTP/2 Client'
    request.body = data
    
    response = http.request(request)
    
    {
      status: response.code,
      headers: response.to_hash,
      body: response.body
    }
  end
end

client = HTTP2Client.new('https://httpbin.org')
response = client.get('/get')
puts response[:status]
```

## FTP Implementation

### FTP Client
```ruby
require 'socket'
require 'net/ftp'

class FTPClient
  def initialize(host, username = 'anonymous', password = 'anonymous@')
    @host = host
    @username = username
    @password = password
    @ftp = nil
  end

  def connect
    @ftp = Net::FTP.new(@host)
    @ftp.login(@username, @password)
    puts "Connected to #{@host}"
  end

  def list_files(path = '/')
    return unless @ftp
    
    files = []
    @ftp.list(path) do |file|
      files << file
      puts file
    end
    files
  end

  def download_file(remote_path, local_path)
    return unless @ftp
    
    @ftp.getbinaryfile(remote_path, local_path)
    puts "Downloaded #{remote_path} to #{local_path}"
  end

  def upload_file(local_path, remote_path)
    return unless @ftp
    
    @ftp.putbinaryfile(local_path, remote_path)
    puts "Uploaded #{local_path} to #{remote_path}"
  end

  def create_directory(path)
    return unless @ftp
    
    @ftp.mkdir(path)
    puts "Created directory: #{path}"
  end

  def delete_file(path)
    return unless @ftp
    
    @ftp.delete(path)
    puts "Deleted file: #{path}"
  end

  def disconnect
    if @ftp
      @ftp.close
      puts "Disconnected from #{@host}"
    end
  end
end

# Usage
ftp = FTPClient.new('ftp.example.org')
ftp.connect
ftp.list_files
ftp.disconnect
```

## SMTP Implementation

### Email Client
```ruby
require 'net/smtp'
require 'base64'

class EmailClient
  def initialize(server, port = 587, domain = 'localhost')
    @server = server
    @port = port
    @domain = domain
  end

  def send_email(from, to, subject, body, options = {})
    message = build_email(from, to, subject, body, options)
    
    Net::SMTP.start(@server, @port, @domain, options[:username], options[:password], :login) do |smtp|
      smtp.send_message(message, from, to)
    end
    
    puts "Email sent to #{to.join(', ')}"
  end

  def send_html_email(from, to, subject, html_body, options = {})
    message = build_html_email(from, to, subject, html_body, options)
    
    Net::SMTP.start(@server, @port, @domain, options[:username], options[:password], :login) do |smtp|
      smtp.send_message(message, from, to)
    end
    
    puts "HTML email sent to #{to.join(', ')}"
  end

  private

  def build_email(from, to, subject, body, options)
    message = <<~EMAIL
      From: #{from}
      To: #{to.join(', ')}
      Subject: #{subject}
      Date: #{Time.now.strftime('%a, %d %b %Y %H:%M:%S %z')}
      
      #{body}
    EMAIL
    
    message
  end

  def build_html_email(from, to, subject, html_body, options)
    boundary = "boundary_#{Time.now.to_i}"
    
    message = <<~EMAIL
      From: #{from}
      To: #{to.join(', ')}
      Subject: #{subject}
      Date: #{Time.now.strftime('%a, %d %b %Y %H:%M:%S %z')}
      MIME-Version: 1.0
      Content-Type: multipart/alternative; boundary=#{boundary}
      
      --#{boundary}
      Content-Type: text/plain; charset=UTF-8
      
      This is an HTML email. Please use an HTML-capable email client.
      
      --#{boundary}
      Content-Type: text/html; charset=UTF-8
      
      #{html_body}
      
      --#{boundary}--
    EMAIL
    
    message
  end
end

# Usage
email = EmailClient.new('smtp.gmail.com', 587, 'gmail.com')
email.send_email(
  'sender@example.com',
  ['recipient@example.com'],
  'Test Email',
  'This is a test email sent from Ruby.',
  username: 'your_email@gmail.com',
  password: 'your_password'
)
```

## DNS Implementation

### DNS Resolver
```ruby
require 'resolv'

class DNSResolver
  def initialize
    @resolver = Resolv::DNS.new
  end

  def resolve_a_record(domain)
    begin
      addresses = @resolver.getaddresses(domain)
      addresses.map(&:to_s)
    rescue Resolv::ResolvError => e
      puts "DNS resolution failed: #{e.message}"
      []
    end
  end

  def resolve_mx_record(domain)
    begin
      mx_records = @resolver.getresources(domain, Resolv::DNS::Resource::IN::MX)
      mx_records.map { |mx| "#{mx.exchange} (priority: #{mx.preference})" }
    rescue Resolv::ResolvError => e
      puts "MX record lookup failed: #{e.message}"
      []
    end
  end

  def reverse_lookup(ip_address)
    begin
      hostname = @resolver.getname(ip_address)
      hostname.to_s
    rescue Resolv::ResolvError => e
      puts "Reverse lookup failed: #{e.message}"
      nil
    end
  end

  def resolve_txt_record(domain)
    begin
      txt_records = @resolver.getresources(domain, Resolv::DNS::Resource::IN::TXT)
      txt_records.map(&:data)
    rescue Resolv::ResolvError => e
      puts "TXT record lookup failed: #{e.message}"
      []
    end
  end

  def check_domain_exists(domain)
    addresses = resolve_a_record(domain)
    !addresses.empty?
  end
end

# Usage
dns = DNSResolver.new
puts "Google IPs: #{dns.resolve_a_record('google.com').join(', ')}"
puts "Google MX: #{dns.resolve_mx_record('google.com').join(', ')}"
puts "Reverse lookup 8.8.8.8: #{dns.reverse_lookup('8.8.8.8')}"
```

## SSH Implementation

### SSH Client
```ruby
require 'net/ssh'

class SSHClient
  def initialize(host, username, options = {})
    @host = host
    @username = username
    @options = options
  end

  def execute_command(command)
    result = {}
    
    Net::SSH.start(@host, @username, @options) do |ssh|
      ssh.open_channel do |channel|
        channel.exec(command) do |ch, success|
          if success
            ch.on_data do |c, data|
              result[:stdout] ||= ''
              result[:stdout] += data
            end
            
            ch.on_extended_data do |c, type, data|
              result[:stderr] ||= ''
              result[:stderr] += data
            end
            
            ch.on_request('exit-status') do |c, data|
              result[:exit_code] = data.read_long
            end
          else
            result[:error] = "Command execution failed"
          end
        end
      end
      ssh.loop
    end
    
    result
  end

  def upload_file(local_path, remote_path)
    Net::SSH.start(@host, @username, @options) do |ssh|
      ssh.scp.upload!(local_path, remote_path)
    end
    puts "Uploaded #{local_path} to #{@host}:#{remote_path}"
  end

  def download_file(remote_path, local_path)
    Net::SSH.start(@host, @username, @options) do |ssh|
      ssh.scp.download!(remote_path, local_path)
    end
    puts "Downloaded #{@host}:#{remote_path} to #{local_path}"
  end

  def start_interactive_shell
    Net::SSH.start(@host, @username, @options) do |ssh|
      ssh.shell.open do |sh|
        sh.on_output do |shell, data|
          print data
        end
        
        sh.on_error do |shell, data|
          print data
        end
        
        loop do
          print "ssh> "
          command = gets.chomp
          break if command == 'exit'
          sh.send_command(command)
        end
      end
    end
  end
end

# Usage
ssh = SSHClient.new('example.com', 'username', password: 'password')
result = ssh.execute_command('ls -la')
puts "Output: #{result[:stdout]}"
puts "Error: #{result[:stderr]}"
puts "Exit code: #{result[:exit_code]}"
```

## MQTT Implementation

### MQTT Client
```ruby
require 'socket'
require 'digest/sha1'
require 'openssl'

class MQTTClient
  def initialize(host, port = 1883, client_id = nil)
    @host = host
    @port = port
    @client_id = client_id || "ruby_client_#{rand(1000)}"
    @socket = nil
    @packet_id = 1
  end

  def connect(username = nil, password = nil)
    @socket = TCPSocket.new(@host, @port)
    
    connect_packet = build_connect_packet(username, password)
    @socket.write(connect_packet)
    
    response = read_packet
    response[:type] == :connack
  end

  def publish(topic, message, qos = 0, retain = false)
    return false unless @socket
    
    packet = build_publish_packet(topic, message, qos, retain)
    @socket.write(packet)
    
    if qos > 0
      response = read_packet
      response[:type] == :puback || response[:type] == :pubrec
    else
      true
    end
  end

  def subscribe(topic, qos = 0)
    return false unless @socket
    
    packet = build_subscribe_packet(topic, qos)
    @socket.write(packet)
    
    response = read_packet
    if response[:type] == :suback
      response[:return_codes].first == qos
    else
      false
    end
  end

  def unsubscribe(topic)
    return false unless @socket
    
    packet = build_unsubscribe_packet(topic)
    @socket.write(packet)
    
    response = read_packet
    response[:type] == :unsuback
  end

  def disconnect
    return unless @socket
    
    packet = build_disconnect_packet
    @socket.write(packet)
    @socket.close
    @socket = nil
  end

  def receive_message
    return nil unless @socket
    
    packet = read_packet
    case packet[:type]
    when :publish
      {
        topic: packet[:topic],
        message: packet[:message],
        qos: packet[:qos],
        retain: packet[:retain]
      }
    else
      nil
    end
  end

  private

  def build_connect_packet(username, password)
    flags = 0x02  # Clean session
    flags |= 0x80 if username
    flags |= 0x40 if password
    
    packet = ''
    packet << [0x10].pack('C')  # Connect packet type
    packet << encode_length(12 + @client_id.length + (username ? username.length + 2 : 0) + (password ? password.length + 2 : 0))
    packet << ['MQTT', 4, flags, 60].pack('a4CCn')
    packet << encode_string(@client_id)
    packet << encode_string(username) if username
    packet << encode_string(password) if password
    
    packet
  end

  def build_publish_packet(topic, message, qos, retain)
    header = 0x30 | (qos << 1) | (retain ? 1 : 0)
    
    packet = ''
    packet << [header].pack('C')
    
    payload = encode_string(topic) + message
    if qos > 0
      payload = [@packet_id].pack('n') + payload
      @packet_id += 1
    end
    
    packet << encode_length(payload.length)
    packet << payload
    
    packet
  end

  def build_subscribe_packet(topic, qos)
    header = 0x82
    
    payload = [@packet_id].pack('n') + encode_string(topic) + [qos].pack('C')
    @packet_id += 1
    
    packet = ''
    packet << [header].pack('C')
    packet << encode_length(payload.length)
    packet << payload
    
    packet
  end

  def build_unsubscribe_packet(topic)
    header = 0xA2
    
    payload = [@packet_id].pack('n') + encode_string(topic)
    @packet_id += 1
    
    packet = ''
    packet << [header].pack('C')
    packet << encode_length(payload.length)
    packet << payload
    
    packet
  end

  def build_disconnect_packet
    packet = ''
    packet << [0xE0].pack('C')
    packet << [0].pack('C')
    
    packet
  end

  def encode_string(str)
    [str.length].pack('n') + str
  end

  def encode_length(length)
    bytes = ''
    while length > 0
      byte = length & 0x7F
      length >>= 7
      byte |= 0x80 if length > 0
      bytes << [byte].pack('C')
    end
    bytes
  end

  def read_packet
    return nil unless @socket
    
    header = @socket.read(1)
    return nil unless header
    
    type = header.unpack('C')[0] >> 4
    flags = header.unpack('C')[0] & 0x0F
    
    length = read_remaining_length
    
    case type
    when 2  # CONNACK
      data = @socket.read(length)
      { type: :connack, return_code: data.unpack('C')[1] }
    when 3  # PUBLISH
      data = @socket.read(length)
      topic_length = data.unpack('n')[0]
      topic = data[2...2+topic_length]
      message = data[2+topic_length..-1]
      { type: :publish, topic: topic, message: message, qos: flags >> 1, retain: (flags & 0x01) != 0 }
    when 4  # PUBACK
      data = @socket.read(length)
      { type: :puback, packet_id: data.unpack('n')[0] }
    when 9  # SUBACK
      data = @socket.read(length)
      packet_id = data.unpack('n')[0]
      return_codes = data[2..-1].unpack('C*')
      { type: :suback, packet_id: packet_id, return_codes: return_codes }
    when 11 # UNSUBACK
      data = @socket.read(length)
      { type: :unsuback, packet_id: data.unpack('n')[0] }
    else
      { type: :unknown, data: @socket.read(length) }
    end
  end

  def read_remaining_length
    multiplier = 1
    length = 0
    
    loop do
      byte = @socket.read(1).unpack('C')[0]
      length += (byte & 0x7F) * multiplier
      break if (byte & 0x80) == 0
      multiplier *= 128
    end
    
    length
  end
end

# Usage
mqtt = MQTTClient.new('test.mosquitto.org', 1883)
if mqtt.connect
  mqtt.subscribe('test/topic', 0)
  mqtt.publish('test/topic', 'Hello from Ruby!')
  
  message = mqtt.receive_message
  puts "Received: #{message[:message]}" if message
  
  mqtt.disconnect
end
```

## Protocol Analysis

### Packet Sniffer
```ruby
require 'socket'
require 'packetfu'

class PacketSniffer
  def initialize(interface = 'eth0')
    @interface = interface
    @captured_packets = []
  end

  def start_capture(count = 10)
    puts "Starting packet capture on #{@interface}..."
    
    # This is a simplified example
    # In practice, you'd use raw sockets or pcap libraries
    capture_thread = Thread.new do
      count.times do |i|
        packet = capture_packet
        @captured_packets << packet if packet
        analyze_packet(packet) if packet
        puts "Captured #{i + 1} packets" if (i + 1) % 5 == 0
      end
    end
    
    capture_thread.join
    puts "Capture complete. Captured #{@captured_packets.length} packets."
  end

  private

  def capture_packet
    # Simplified packet capture
    # In reality, you'd use pcap or raw sockets
    {
      timestamp: Time.now,
      source_ip: "192.168.1.#{rand(254) + 1}",
      dest_ip: "192.168.1.#{rand(254) + 1}",
      protocol: ['TCP', 'UDP', 'ICMP'].sample,
      size: rand(1000) + 64,
      data: "Sample packet data..."
    }
  end

  def analyze_packet(packet)
    puts "[#{packet[:timestamp]}] #{packet[:protocol]} #{packet[:source_ip]} -> #{packet[:dest_ip]} (#{packet[:size]} bytes)"
  end
end

sniffer = PacketSniffer.new
sniffer.start_capture(5)
```

## Best Practices

1. **Protocol Compliance**: Follow RFC specifications
2. **Error Handling**: Handle network errors gracefully
3. **Security**: Use encryption for sensitive data
4. **Performance**: Optimize for high-throughput scenarios
5. **Timeouts**: Set appropriate timeouts to prevent hanging
6. **Resource Management**: Close connections properly
7. **Logging**: Log protocol interactions for debugging

## Conclusion

Ruby provides extensive support for various network protocols, making it suitable for building networked applications. From low-level socket programming to high-level protocol implementations, Ruby offers the flexibility and tools needed for robust network communication.

## Further Reading

- [RFC Index](https://tools.ietf.org/rfc/)
- [Ruby Net Library Documentation](https://ruby-doc.org/stdlib-3.0.0/libdoc/net/rdoc/index.html)
- [Protocol Specifications](https://www.iana.org/protocols/)
