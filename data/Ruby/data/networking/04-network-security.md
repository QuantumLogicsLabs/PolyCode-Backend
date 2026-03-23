# Network Security in Ruby

## Overview

Network security encompasses the practices and technologies designed to protect network infrastructure, data, and communications from unauthorized access, misuse, and attacks. Ruby provides various libraries and tools for implementing network security measures.

## Cryptography Fundamentals

### Encryption and Decryption
```ruby
require 'openssl'
require 'base64'

class CryptographyManager
  def initialize
    @cipher = OpenSSL::Cipher.new('AES-256-CBC')
  end

  def encrypt(data, password)
    salt = OpenSSL::Random.random_bytes(8)
    
    # Derive key from password
    key = OpenSSL::PKCS5.pbkdf2_hmac_sha1(
      password, salt, 20000, 32
    )
    
    # Encrypt data
    @cipher.encrypt
    @cipher.key = key
    iv = @cipher.random_iv
    
    encrypted = @cipher.update(data) + @cipher.final
    
    # Combine salt, iv, and encrypted data
    Base64.strict_encode64(salt + iv + encrypted)
  end

  def decrypt(encrypted_data, password)
    data = Base64.strict_decode64(encrypted_data)
    
    # Extract salt, iv, and encrypted data
    salt = data[0...8]
    iv = data[8...24]
    encrypted = data[24..-1]
    
    # Derive key from password
    key = OpenSSL::PKCS5.pbkdf2_hmac_sha1(
      password, salt, 20000, 32
    )
    
    # Decrypt data
    @cipher.decrypt
    @cipher.key = key
    @cipher.iv = iv
    
    @cipher.update(encrypted) + @cipher.final
  rescue OpenSSL::Cipher::CipherError => e
    puts "Decryption failed: #{e.message}"
    nil
  end

  def generate_rsa_key_pair(key_size = 2048)
    rsa_key = OpenSSL::PKey::RSA.new(key_size)
    
    {
      private_key: rsa_key.to_pem,
      public_key: rsa_key.public_key.to_pem
    }
  end

  def rsa_encrypt(data, public_key_pem)
    public_key = OpenSSL::PKey::RSA.new(public_key_pem)
    Base64.strict_encode64(public_key.public_encrypt(data))
  end

  def rsa_decrypt(encrypted_data, private_key_pem)
    private_key = OpenSSL::PKey::RSA.new(private_key_pem)
    private_key.private_decrypt(Base64.strict_decode64(encrypted_data))
  rescue OpenSSL::PKey::RSAError => e
    puts "RSA decryption failed: #{e.message}"
    nil
  end

  def generate_hash(data, algorithm = 'sha256')
    digest = OpenSSL::Digest.new(algorithm)
    OpenSSL::HMAC.hexdigest(digest, 'secret_key', data)
  end

  def verify_hash(data, hash, algorithm = 'sha256')
    digest = OpenSSL::Digest.new(algorithm)
    computed_hash = OpenSSL::HMAC.hexdigest(digest, 'secret_key', data)
    computed_hash == hash
  end
end

# Usage
crypto = CryptographyManager.new

# Symmetric encryption
message = "This is a secret message"
password = "secure_password"
encrypted = crypto.encrypt(message, password)
decrypted = crypto.decrypt(encrypted, password)
puts "Original: #{message}"
puts "Decrypted: #{decrypted}"

# RSA encryption
keys = crypto.generate_rsa_key_pair
rsa_encrypted = crypto.rsa_encrypt("RSA secret", keys[:public_key])
rsa_decrypted = crypto.rsa_decrypt(rsa_encrypted, keys[:private_key])
puts "RSA decrypted: #{rsa_decrypted}"
```

## SSL/TLS Implementation

### Secure Socket Layer
```ruby
require 'socket'
require 'openssl'

class SecureSocketServer
  def initialize(host, port, cert_file, key_file)
    @host = host
    @port = port
    @cert_file = cert_file
    @key_file = key_file
  end

  def start
    # Create SSL context
    context = OpenSSL::SSL::SSLContext.new
    context.cert = OpenSSL::X509::Certificate.new(File.read(@cert_file))
    context.key = OpenSSL::PKey::RSA.new(File.read(@key_file))
    context.verify_mode = OpenSSL::SSL::VERIFY_NONE
    
    # Create TCP server
    tcp_server = TCPServer.new(@host, @port)
    ssl_server = OpenSSL::SSL::SSLServer.new(tcp_server, context)
    
    puts "Secure server listening on #{@host}:#{@port}"
    
    loop do
      Thread.start(ssl_server.accept) do |client|
        handle_secure_client(client)
      end
    end
  end

  private

  def handle_secure_client(client)
    peer_cert = client.peer_cert
    if peer_cert
      puts "Client connected with certificate: #{peer_cert.subject}"
    else
      puts "Client connected without certificate"
    end
    
    begin
      client.puts "Welcome to secure server!"
      loop do
        message = client.gets
        break unless message
        
        message = message.chomp
        puts "Received: #{message}"
        client.puts "Echo: #{message}"
      end
    ensure
      client.close
      puts "Client disconnected"
    end
  end
end

class SecureSocketClient
  def initialize(host, port)
    @host = host
    @port = port
  end

  def connect(verify_ssl = true)
    tcp_socket = TCPSocket.new(@host, @port)
    
    context = OpenSSL::SSL::SSLContext.new
    if verify_ssl
      context.verify_mode = OpenSSL::SSL::VERIFY_PEER
      context.ca_file = '/etc/ssl/certs/ca-certificates.crt'
    else
      context.verify_mode = OpenSSL::SSL::VERIFY_NONE
    end
    
    @ssl_socket = OpenSSL::SSL::SSLSocket.new(tcp_socket, context)
    @ssl_socket.connect
    
    puts "Connected to secure server #{@host}:#{@port}"
    puts "Using cipher: #{@ssl_socket.cipher}"
    puts "Peer certificate: #{@ssl_socket.peer_cert.subject}" if @ssl_socket.peer_cert
    
    true
  rescue OpenSSL::SSL::SSLError => e
    puts "SSL connection failed: #{e.message}"
    false
  end

  def send_message(message)
    return false unless @ssl_socket
    
    @ssl_socket.puts(message)
    response = @ssl_socket.gets
    response&.chomp
  end

  def close
    @ssl_socket&.close
  end
end

# Generate self-signed certificate for testing
def generate_self_signed_cert
  key = OpenSSL::PKey::RSA.new(2048)
  
  cert = OpenSSL::X509::Certificate.new
  cert.version = 2
  cert.serial = 1
  cert.not_before = Time.now
  cert.not_after = Time.now + 86400  # 1 day
  cert.public_key = key.public_key
  
  name = OpenSSL::X509::Name.new([['CN', 'localhost']])
  cert.subject = name
  cert.issuer = name
  
  extension_factory = OpenSSL::X509::ExtensionFactory.new
  extension_factory.subject_certificate = cert
  extension_factory.issuer_certificate = cert
  
  cert.add_extension(extension_factory.create_extension('basicConstraints', 'CA:FALSE'))
  cert.add_extension(extension_factory.create_extension('keyUsage', 'keyEncipherment,digitalSignature'))
  cert.add_extension(extension_factory.create_extension('subjectAltName', 'DNS:localhost'))
  
  cert.sign(key, OpenSSL::Digest::SHA256.new)
  
  File.write('server.crt', cert.to_pem)
  File.write('server.key', key.to_pem)
  
  puts "Generated self-signed certificate: server.crt, server.key"
end
```

## Authentication Systems

### Token-Based Authentication
```ruby
require 'jwt'
require 'bcrypt'

class AuthenticationSystem
  def initialize(secret_key)
    @secret_key = secret_key
    @users = {}
    @tokens = {}
  end

  def register_user(username, password)
    if @users.key?(username)
      return { success: false, message: "User already exists" }
    end
    
    hashed_password = BCrypt::Password.create(password)
    @users[username] = {
      password_hash: hashed_password,
      created_at: Time.now,
      last_login: nil
    }
    
    { success: true, message: "User registered successfully" }
  end

  def authenticate_user(username, password)
    user = @users[username]
    return { success: false, message: "User not found" } unless user
    
    if BCrypt::Password.new(user[:password_hash]) == password
      user[:last_login] = Time.now
      token = generate_token(username)
      @tokens[token] = { username: username, created_at: Time.now }
      
      { success: true, token: token, message: "Authentication successful" }
    else
      { success: false, message: "Invalid password" }
    end
  end

  def verify_token(token)
    payload = @tokens[token]
    return { valid: false, message: "Token not found" } unless payload
    
    # Check token age (24 hours)
    if Time.now - payload[:created_at] > 86400
      @tokens.delete(token)
      return { valid: false, message: "Token expired" }
    end
    
    { valid: true, username: payload[:username] }
  end

  def logout(token)
    @tokens.delete(token)
    { success: true, message: "Logged out successfully" }
  end

  private

  def generate_token(username)
    payload = {
      username: username,
      exp: Time.now.to_i + 86400,  # 24 hours
      iat: Time.now.to_i
    }
    
    JWT.encode(payload, @secret_key, 'HS256')
  end
end

# Usage
auth = AuthenticationSystem.new('your_secret_key_here')

# Register user
result = auth.register_user('alice', 'secure_password')
puts result[:message]

# Authenticate user
result = auth.authenticate_user('alice', 'secure_password')
if result[:success]
  token = result[:token]
  puts "Token: #{token}"
  
  # Verify token
  verification = auth.verify_token(token)
  puts "Token valid: #{verification[:valid]}"
  puts "Username: #{verification[:username]}" if verification[:valid]
end
```

### OAuth2 Implementation
```ruby
require 'securerandom'
require 'base64'

class OAuth2Server
  def initialize
    @clients = {}
    @authorization_codes = {}
    @access_tokens = {}
    @refresh_tokens = {}
  end

  def register_client(client_id, client_secret, redirect_uris)
    @clients[client_id] = {
      client_secret: client_secret,
      redirect_uris: redirect_uris,
      created_at: Time.now
    }
  end

  def authorize(client_id, redirect_uri, scope = nil)
    client = @clients[client_id]
    return nil unless client
    
    unless client[:redirect_uris].include?(redirect_uri)
      return nil
    end
    
    code = SecureRandom.hex(16)
    @authorization_codes[code] = {
      client_id: client_id,
      redirect_uri: redirect_uri,
      scope: scope,
      created_at: Time.now
    }
    
    "#{redirect_uri}?code=#{code}"
  end

  def exchange_code_for_token(code, client_id, client_secret)
    auth_code = @authorization_codes[code]
    return nil unless auth_code
    
    client = @clients[client_id]
    return nil unless client && client[:client_secret] == client_secret
    
    # Remove authorization code
    @authorization_codes.delete(code)
    
    # Generate access token
    access_token = SecureRandom.hex(32)
    refresh_token = SecureRandom.hex(32)
    
    @access_tokens[access_token] = {
      client_id: client_id,
      scope: auth_code[:scope],
      created_at: Time.now,
      expires_at: Time.now + 3600  # 1 hour
    }
    
    @refresh_tokens[refresh_token] = {
      client_id: client_id,
      access_token: access_token,
      created_at: Time.now
    }
    
    {
      access_token: access_token,
      token_type: 'Bearer',
      expires_in: 3600,
      refresh_token: refresh_token,
      scope: auth_code[:scope]
    }
  end

  def validate_access_token(access_token)
    token = @access_tokens[access_token]
    return nil unless token
    
    if Time.now > token[:expires_at]
      @access_tokens.delete(access_token)
      return nil
    end
    
    {
      valid: true,
      client_id: token[:client_id],
      scope: token[:scope]
    }
  end

  def refresh_access_token(refresh_token)
    token = @refresh_tokens[refresh_token]
    return nil unless token
    
    # Generate new access token
    new_access_token = SecureRandom.hex(32)
    
    @access_tokens[new_access_token] = {
      client_id: token[:client_id],
      scope: @access_tokens[token[:access_token]][:scope],
      created_at: Time.now,
      expires_at: Time.now + 3600
    }
    
    # Update refresh token
    token[:access_token] = new_access_token
    
    {
      access_token: new_access_token,
      token_type: 'Bearer',
      expires_in: 3600
    }
  end
end

# Usage
oauth = OAuth2Server.new

# Register client
oauth.register_client(
  'client123',
  'secret456',
  ['https://app.example.com/callback']
)

# Authorize
auth_url = oauth.authorize('client123', 'https://app.example.com/callback', 'read write')
puts "Authorization URL: #{auth_url}"

# Exchange code for token (in real implementation, this would be done by the client)
# token_data = oauth.exchange_code_for_token('auth_code', 'client123', 'secret456')
# puts "Access token: #{token_data[:access_token]}"
```

## Network Security Tools

### Port Scanner with Security Checks
```ruby
require 'socket'
require 'timeout'

class SecurityPortScanner
  def initialize(host)
    @host = host
    @open_ports = []
    @vulnerable_services = {}
  end

  def scan_port(port, timeout = 2)
    begin
      Timeout::timeout(timeout) do
        socket = TCPSocket.new(@host, port)
        socket.close
        true
      end
    rescue Errno::ECONNREFUSED, Errno::ETIMEDOUT, Timeout::Error
      false
    end
  end

  def scan_range(start_port, end_port)
    puts "Scanning #{@host} from port #{start_port} to #{end_port}..."
    
    threads = []
    (start_port..end_port).each do |port|
      threads << Thread.new do
        if scan_port(port)
          @open_ports << port
          puts "Port #{port}: OPEN"
          check_vulnerability(port)
        end
      end
    end
    
    threads.each(&:join)
    
    generate_report
  end

  def check_vulnerability(port)
    service_info = identify_service(port)
    vulnerabilities = check_service_vulnerabilities(port, service_info)
    
    if vulnerabilities.any?
      @vulnerable_services[port] = {
        service: service_info,
        vulnerabilities: vulnerabilities
      }
    end
  end

  def identify_service(port)
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

  def check_service_vulnerabilities(port, service)
    vulnerabilities = []
    
    case service
    when 'Telnet'
      vulnerabilities << 'Unencrypted communication'
      vulnerabilities << 'Plain text authentication'
    when 'FTP'
      vulnerabilities << 'Unencrypted file transfer'
      vulnerabilities << 'Anonymous access possible'
    when 'HTTP'
      vulnerabilities << check_http_vulnerabilities(port)
    when 'SMTP'
      vulnerabilities << 'Possible open relay'
    end
    
    vulnerabilities
  end

  def check_http_vulnerabilities(port)
    begin
      socket = TCPSocket.new(@host, port)
      socket.puts "GET / HTTP/1.1\r\nHost: #{@host}\r\n\r\n"
      response = socket.gets
      socket.close
      
      vulnerabilities = []
      
      if response&.include?('Apache/2.2')
        vulnerabilities << 'Outdated Apache version'
      end
      
      if response&.include?('nginx/1.0')
        vulnerabilities << 'Outdated Nginx version'
      end
      
      vulnerabilities
    rescue
      []
    end
  end

  def generate_report
    puts "\n" + "=" * 60
    puts "SECURITY SCAN REPORT FOR #{@host}"
    puts "=" * 60
    
    puts "\nOpen Ports (#{@open_ports.length}):"
    @open_ports.sort.each { |port| puts "  #{port}" }
    
    if @vulnerable_services.any?
      puts "\nVulnerable Services:"
      @vulnerable_services.each do |port, info|
        puts "  Port #{port} (#{info[:service]}):"
        info[:vulnerabilities].each { |vuln| puts "    - #{vuln}" }
      end
    else
      puts "\nNo obvious vulnerabilities detected."
    end
    
    puts "\nRecommendations:"
    puts "  - Close unnecessary ports"
    puts "  - Update services to latest versions"
    puts "  - Use encrypted alternatives (HTTPS, SSH, etc.)"
    puts "  - Implement firewall rules"
    
    {
      open_ports: @open_ports,
      vulnerable_services: @vulnerable_services
    }
  end
end

# Usage
scanner = SecurityPortScanner.new('localhost')
scanner.scan_range(1, 1000)
```

### Intrusion Detection System
```ruby
require 'socket'
require 'thread'

class IntrusionDetectionSystem
  def initialize
    @suspicious_ips = {}
    @blocked_ips = []
    @log_entries = []
    @thresholds = {
      max_connections: 100,
      time_window: 60,  # seconds
      block_duration: 300  # seconds
    }
    @mutex = Mutex.new
  end

  def log_connection(ip, port, timestamp = Time.now)
    @mutex.synchronize do
      @log_entries << {
        ip: ip,
        port: port,
        timestamp: timestamp
      }
      
      # Keep only recent entries
      cutoff_time = timestamp - @thresholds[:time_window]
      @log_entries.reject! { |entry| entry[:timestamp] < cutoff_time }
      
      # Check for suspicious activity
      analyze_connections(ip, timestamp)
    end
  end

  def analyze_connections(ip, current_time)
    recent_connections = @log_entries.select do |entry|
      entry[:ip] == ip && 
      (current_time - entry[:timestamp]) <= @thresholds[:time_window]
    end
    
    connection_count = recent_connections.length
    
    if connection_count > @thresholds[:max_connections]
      handle_suspicious_activity(ip, connection_count, current_time)
    end
  end

  def handle_suspicious_activity(ip, connection_count, timestamp)
    @suspicious_ips[ip] ||= { count: 0, first_seen: timestamp }
    @suspicious_ips[ip][:count] += connection_count
    
    puts "ALERT: Suspicious activity detected from #{ip}"
    puts "  Connections in last #{@thresholds[:time_window]}s: #{connection_count}"
    puts "  Total suspicious connections: #{@suspicious_ips[ip][:count]}"
    
    # Block if threshold exceeded
    if @suspicious_ips[ip][:count] > @thresholds[:max_connections] * 2
      block_ip(ip, timestamp)
    end
  end

  def block_ip(ip, timestamp)
    return if @blocked_ips.include?(ip)
    
    @blocked_ips << ip
    puts "BLOCKED: IP #{ip} has been blocked"
    
    # Unblock after duration
    Thread.new do
      sleep(@thresholds[:block_duration])
      unblock_ip(ip)
    end
  end

  def unblock_ip(ip)
    @blocked_ips.delete(ip)
    @suspicious_ips.delete(ip)
    puts "UNBLOCKED: IP #{ip} has been unblocked"
  end

  def is_ip_blocked?(ip)
    @blocked_ips.include?(ip)
  end

  def get_statistics
    {
      total_connections: @log_entries.length,
      suspicious_ips: @suspicious_ips.keys.length,
      blocked_ips: @blocked_ips.length,
      time_window: @thresholds[:time_window]
    }
  end

  def generate_report
    puts "\n" + "=" * 50
    puts "INTRUSION DETECTION SYSTEM REPORT"
    puts "=" * 50
    
    stats = get_statistics
    puts "\nStatistics:"
    stats.each { |key, value| puts "  #{key}: #{value}" }
    
    if @suspicious_ips.any?
      puts "\nSuspicious IPs:"
      @suspicious_ips.each do |ip, info|
        puts "  #{ip}: #{info[:count]} connections"
      end
    end
    
    if @blocked_ips.any?
      puts "\nBlocked IPs:"
      @blocked_ips.each { |ip| puts "  #{ip}" }
    end
  end
end

# Usage
ids = IntrusionDetectionSystem.new

# Simulate connections
10.times do |i|
  ids.log_connection("192.168.1.#{i + 1}", 80)
end

# Simulate suspicious activity
50.times do
  ids.log_connection("192.168.1.100", 80)
end

ids.generate_report
```

## Best Practices

1. **Use Strong Encryption**: Always use up-to-date encryption algorithms
2. **Implement Proper Authentication**: Multi-factor authentication when possible
3. **Regular Security Audits**: Scan for vulnerabilities regularly
4. **Keep Software Updated**: Apply security patches promptly
5. **Network Segmentation**: Isolate critical systems
6. **Monitor and Log**: Track all network activities
7. **Incident Response**: Have a plan for security breaches

## Conclusion

Network security is a critical aspect of modern software development. Ruby provides robust tools and libraries for implementing comprehensive security measures, from encryption to intrusion detection. By following security best practices and using appropriate tools, you can build secure network applications that protect against common threats.

## Further Reading

- [OWASP Top Ten](https://owasp.org/www-project-top-ten/)
- [Ruby Security Guidelines](https://ruby-doc.org/stdlib-3.0.0/libdoc/openssl/rdoc/index.html)
- [NIST Cybersecurity Framework](https://www.nist.gov/cyberframework)
