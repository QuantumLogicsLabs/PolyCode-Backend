# Network Security in Ruby
# Comprehensive guide to network security implementations and protections

## 🌐 Network Security Fundamentals

### 1. Network Security Concepts

Core network security principles:

```ruby
class NetworkSecurityFundamentals
  def self.explain_network_security_concepts
    puts "Network Security Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Network Segmentation",
        description: "Dividing network into smaller segments",
        benefits: ["Containment of breaches", "Improved performance", "Better access control"],
        implementation: ["VLANs", "Subnets", "Firewalls", "ACLs"]
      },
      {
        concept: "Defense in Depth",
        description: "Multiple layers of security controls",
        layers: ["Perimeter security", "Network security", "Host security", "Application security"],
        principle: "If one layer fails, others still provide protection"
      },
      {
        concept: "Zero Trust Architecture",
        description: "Never trust, always verify",
        principles: ["Verify explicitly", "Least privilege access", "Assume breach"],
        implementation: ["Micro-segmentation", "Identity verification", "Continuous monitoring"]
      },
      {
        concept: "Intrusion Detection/Prevention",
        description: "Monitor and block malicious activities",
        types: ["Network-based (NIDS)", "Host-based (HIDS)", "Hybrid systems"],
        techniques: ["Signature-based", "Anomaly-based", "Behavioral analysis"]
      },
      {
        concept: "Network Access Control",
        description: "Control who can access network resources",
        methods: ["802.1X", "MAC filtering", "Port security", "Role-based access"],
        benefits: ["Unauthorized access prevention", "Device compliance"]
      },
      {
        concept: "Secure Communication",
        description: "Protect data in transit",
        protocols: ["TLS/SSL", "IPsec", "SSH", "VPN"],
        features: ["Encryption", "Authentication", "Integrity", "Non-repudiation"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Implementation: #{concept[:implementation].join(', ')}" if concept[:implementation]
      puts "  Layers: #{concept[:layers].join(', ')}" if concept[:layers]
      puts "  Principle: #{concept[:principle]}" if concept[:principle]
      puts "  Principles: #{concept[:principles].join(', ')}" if concept[:principles]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Techniques: #{concept[:techniques].join(', ')}" if concept[:techniques]
      puts "  Methods: #{concept[:methods].join(', ')}" if concept[:methods]
      puts "  Protocols: #{concept[:protocols].join(', ')}" if concept[:protocols]
      puts "  Features: #{concept[:features].join(', ')}" if concept[:features]
      puts
    end
  end
  
  def self.network_threats
    puts "\nNetwork Threats and Attack Vectors:"
    puts "=" * 50
    
    threats = [
      {
        threat: "DDoS Attacks",
        description: "Overwhelm network with traffic",
        types: ["Volumetric", "Protocol", "Application layer"],
        impact: ["Service disruption", "Financial loss", "Reputation damage"],
        mitigation: ["Rate limiting", "CDN", "Scalable infrastructure"]
      },
      {
        threat: "Man-in-the-Middle",
        description: "Intercept communication between parties",
        methods: ["ARP spoofing", "DNS spoofing", "Session hijacking"],
        impact: ["Data theft", "Session takeover", "Message manipulation"],
        mitigation: ["Encryption", "Certificate validation", "Secure protocols"]
      },
      {
        threat: "Port Scanning",
        description: "Systematically scan for open ports",
        purpose: ["Service discovery", "Vulnerability identification", "Attack planning"],
        impact: ["Information disclosure", "Attack preparation"],
        mitigation: ["Firewalls", "Port knocking", "Stealth techniques"]
      },
      {
        threat: "Network Sniffing",
        description: "Capture and analyze network traffic",
        methods: ["Packet capture", "Protocol analysis", "Traffic analysis"],
        impact: ["Credential theft", "Data exposure", "Session hijacking"],
        mitigation: ["Encryption", "Switch security", "Network segmentation"]
      },
      {
        threat: "SQL Injection",
        description: "Inject malicious SQL into database queries",
        vectors: ["Web forms", "API endpoints", "URL parameters"],
        impact: ["Data breach", "Data manipulation", "System compromise"],
        mitigation: ["Input validation", "Parameterized queries", "WAFs"]
      },
      {
        threat: "Cross-Site Scripting",
        description: "Inject malicious scripts into web pages",
        types: ["Reflected", "Stored", "DOM-based"],
        impact: ["Session theft", "Data theft", "User manipulation"],
        mitigation: ["Input sanitization", "Content Security Policy", "XSS protection"]
      }
    ]
    
    threats.each do |threat|
      puts "#{threat[:threat]}:"
      puts "  Description: #{threat[:description]}"
      puts "  Types: #{threat[:types].join(', ')}" if threat[:types]
      puts "  Methods: #{threat[:methods].join(', ')}" if threat[:methods]
      puts "  Vectors: #{threat[:vectors].join(', ')}" if threat[:vectors]
      puts "  Purpose: #{threat[:purpose]}" if threat[:purpose]
      puts "  Impact: #{threat[:impact].join(', ')}"
      puts "  Mitigation: #{threat[:mitigation].join(', ')}"
      puts
    end
  end
  
  def self.security_protocols
    puts "\nNetwork Security Protocols:"
    puts "=" * 50
    
    protocols = [
      {
        protocol: "TLS/SSL",
        purpose: "Secure communication over networks",
        version: "TLS 1.3",
        features: ["Encryption", "Authentication", "Integrity", "Forward secrecy"],
        applications: ["HTTPS", "SMTPS", "FTPS", "VPNs"]
      },
      {
        protocol: "IPsec",
        purpose: "Network layer security",
        modes: ["Transport mode", "Tunnel mode"],
        components: ["AH", "ESP", "IKE"],
        applications: ["Site-to-site VPNs", "Remote access", "Network encryption"]
      },
      {
        protocol: "SSH",
        purpose: "Secure remote access",
        version: "SSH-2",
        features: ["Encryption", "Authentication", "Port forwarding", "Tunneling"],
        applications: ["Remote administration", "File transfer", "VPN tunneling"]
      },
      {
        protocol: "802.1X",
        purpose: "Port-based network access control",
        components: ["Supplicant", "Authenticator", "Authentication Server"],
        methods: ["EAP-TLS", "EAP-PEAP", "EAP-TTLS"],
        applications: ["WLAN security", "Wired network access", "Switch security"]
      },
      {
        protocol: "DNSSEC",
        purpose: "Secure DNS resolution",
        features: ["Data origin authentication", "Data integrity", "Authenticated denial of existence"],
        benefits: ["Prevents DNS spoofing", "Ensures DNS responses are authentic"],
        applications: ["Domain name resolution", "Email security", "Web security"]
      }
    ]
    
    protocols.each do |protocol|
      puts "#{protocol[:protocol]}:"
      puts "  Purpose: #{protocol[:purpose]}"
      puts "  Version: #{protocol[:version]}" if protocol[:version]
      puts "  Modes: #{protocol[:modes].join(', ')}" if protocol[:modes]
      puts "  Components: #{protocol[:components].join(', ')}" if protocol[:components]
      puts "  Methods: #{protocol[:methods].join(', ')}" if protocol[:methods]
      puts "  Features: #{protocol[:features].join(', ')}"
      puts "  Applications: #{protocol[:applications].join(', ')}"
      puts "  Benefits: #{protocol[:benefits].join(', ')}" if protocol[:benefits]
      puts
    end
  end
  
  # Run network security fundamentals
  explain_network_security_concepts
  network_threats
  security_protocols
end
```

### 2. Network Scanning and Enumeration

Network reconnaissance tools:

```ruby
class NetworkScanner
  require 'socket'
  require 'timeout'
  
  def initialize(target)
    @target = target
    @open_ports = []
    @services = {}
  end
  
  def port_scan(port_range = 1..1000, timeout = 2)
    puts "Scanning #{@target} ports #{port_range}..."
    
    port_range.each do |port|
      begin
        Timeout.timeout(timeout) do
          socket = Socket.new(Socket::AF_INET, Socket::SOCK_STREAM)
          socket.connect(@target, port)
          socket.close
          
          @open_ports << port
          puts "Port #{port} is OPEN"
          
          # Try to identify service
          service = identify_service(port)
          @services[port] = service if service
        end
      rescue Timeout::Error, Errno::ECONNREFUSED, Errno::EHOSTUNREACH
        # Port is closed or filtered
      rescue => e
        puts "Error scanning port #{port}: #{e.message}"
      end
    end
    
    puts "Scan completed. Found #{@open_ports.length} open ports"
    @open_ports
  end
  
  def service_detection(port)
    puts "Detecting service on port #{port}..."
    
    begin
      Timeout.timeout(5) do
        socket = Socket.new(Socket::AF_INET, Socket::SOCK_STREAM)
        socket.connect(@target, port)
        
        # Send probe data
        probe_data = "GET / HTTP/1.1\r\nHost: #{@target}\r\n\r\n"
        socket.write(probe_data)
        
        # Read response
        response = socket.readpartial(1024)
        socket.close
        
        # Analyze response
        service = analyze_service_response(port, response)
        puts "Service on port #{port}: #{service[:name]} (#{service[:version]})"
        
        service
      end
    rescue => e
      puts "Error detecting service on port #{port}: #{e.message}"
      nil
    end
  end
  
  def os_detection
    puts "Attempting OS detection..."
    
    begin
      # TTL analysis
      ttl_values = []
      
      5.times do
        ping_result = ping_host(@target)
        ttl_values << ping_result[:ttl] if ping_result[:ttl]
      end
      
      if ttl_values.any?
        avg_ttl = ttl_values.sum.to_f / ttl_values.length
        
        os_guess = case avg_ttl
                 when 0..64
                   "Linux/Unix"
                 when 65..128
                   "Windows"
                 when 129..255
                   "Cisco/Network Device"
                 else
                   "Unknown"
                 end
        
        puts "Average TTL: #{avg_ttl.round(2)}"
        puts "OS Guess: #{os_guess}"
        
        os_guess
      else
        puts "Could not determine TTL values"
        "Unknown"
      end
    rescue => e
      puts "OS detection failed: #{e.message}"
      "Unknown"
    end
  end
  
  def vulnerability_scan
    puts "Performing vulnerability scan..."
    
    vulnerabilities = []
    
    @open_ports.each do |port|
      service = @services[port]
      
      # Check for common vulnerabilities
      case port
      when 21
        vulnerabilities << check_ftp_vulnerabilities(port)
      when 22
        vulnerabilities << check_ssh_vulnerabilities(port)
      when 23
        vulnerabilities << check_telnet_vulnerabilities(port)
      when 25
        vulnerabilities << check_smtp_vulnerabilities(port)
      when 53
        vulnerabilities << check_dns_vulnerabilities(port)
      when 80, 443, 8080
        vulnerabilities << check_web_vulnerabilities(port)
      when 135, 139, 445
        vulnerabilities << check_smb_vulnerabilities(port)
      when 3306
        vulnerabilities << check_mysql_vulnerabilities(port)
      when 5432
        vulnerabilities << check_postgresql_vulnerabilities(port)
      end
    end
    
    vulnerabilities.flatten!
    
    puts "Found #{vulnerabilities.length} potential vulnerabilities"
    vulnerabilities.each { |vuln| puts "- #{vuln[:severity]}: #{vuln[:description]}" }
    
    vulnerabilities
  end
  
  def generate_report
    puts "\nNetwork Scan Report:"
    puts "=" * 50
    puts "Target: #{@target}"
    puts "Scan Date: #{Time.now}"
    puts "Open Ports: #{@open_ports.length}"
    puts "Services: #{@services.length}"
    
    puts "\nOpen Ports:"
    @open_ports.sort.each do |port|
      service = @services[port]
      puts "  #{port}: #{service ? "#{service[:name]} #{service[:version]}" : 'Unknown'}"
    end
    
    puts "\nOS Detection:"
    os = os_detection
    puts "  Detected OS: #{os}"
    
    puts "\nVulnerabilities:"
    vulnerabilities = vulnerability_scan
    if vulnerabilities.any?
      vulnerabilities.each { |vuln| puts "  #{vuln[:severity]}: #{vuln[:description]}" }
    else
      puts "  No vulnerabilities found"
    end
    
    {
      target: @target,
      scan_date: Time.now,
      open_ports: @open_ports,
      services: @services,
      os: os,
      vulnerabilities: vulnerabilities
    }
  end
  
  def self.demonstrate_scanning
    puts "Network Scanning Demonstration:"
    puts "=" * 50
    
    # Note: This is a demonstration with a safe target
    target = "scanme.nmap.org" # Safe scanning target
    
    scanner = NetworkScanner.new(target)
    
    # Port scanning
    puts "Port Scanning:"
    scanner.port_scan(20, 25) # Scan common ports with short timeout
    
    # Service detection
    puts "\nService Detection:"
    scanner.open_ports.first(3).each do |port|
      scanner.service_detection(port)
    end
    
    # OS detection
    puts "\nOS Detection:"
    scanner.os_detection
    
    # Generate report
    puts "\nGenerating Report:"
    scanner.generate_report
    
    puts "\nNetwork Scanning Features:"
    puts "- Port scanning (TCP)"
    puts "- Service detection"
    puts "- OS fingerprinting"
    puts "- Vulnerability assessment"
    puts "- Comprehensive reporting"
  end
  
  private
  
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
      995 => 'POP3S',
      3306 => 'MySQL',
      5432 => 'PostgreSQL',
      6379 => 'Redis',
      8080 => 'HTTP-Alt'
    }
    
    common_services[port]
  end
  
  def analyze_service_response(port, response)
    case port
    when 80, 8080
      if response.include?('HTTP')
        server_line = response.split("\n").first
        server = server_line.include?('Server:') ? server_line.split(':')[1].strip : 'Unknown'
        { name: 'HTTP', version: server }
      else
        { name: 'HTTP', version: 'Unknown' }
      end
    when 443
      { name: 'HTTPS', version: 'TLS' }
    when 22
      { name: 'SSH', version: response.include?('SSH') ? 'SSH' : 'Unknown' }
    when 21
      { name: 'FTP', version: response.include?('FTP') ? 'FTP' : 'Unknown' }
    else
      { name: 'Unknown', version: 'Unknown' }
    end
  end
  
  def ping_host(host)
    # Simplified ping implementation
    begin
      require 'ping'
      result = Ping.pingecho(host, 5)
      
      {
        success: result[0],
        ttl: result[2]
      }
    rescue
      { success: false, ttl: nil }
    end
  end
  
  def check_ftp_vulnerabilities(port)
    [
      {
        severity: 'medium',
        description: 'FTP allows anonymous access',
        recommendation: 'Disable anonymous FTP access'
      },
      {
        severity: 'high',
        description: 'FTP uses unencrypted transmission',
        recommendation: 'Use SFTP or FTPS instead'
      }
    ]
  end
  
  def check_ssh_vulnerabilities(port)
    [
      {
        severity: 'low',
        description: 'SSH version may be outdated',
        recommendation: 'Update SSH to latest version'
      },
      {
        severity: 'medium',
        description: 'Weak SSH configuration detected',
        recommendation: 'Implement strong SSH security settings'
      }
    ]
  end
  
  def check_telnet_vulnerabilities(port)
    [
      {
        severity: 'high',
        description: 'Telnet uses unencrypted transmission',
        recommendation: 'Replace with SSH'
      },
      {
        severity: 'critical',
        description: 'Telnet is obsolete and insecure',
        recommendation: 'Disable telnet service immediately'
      }
    ]
  end
  
  def check_smtp_vulnerabilities(port)
    [
      {
        severity: 'medium',
        description: 'SMTP may allow open relay',
        recommendation: 'Configure SMTP relay restrictions'
      },
      {
        severity: 'low',
        description: 'SMTP may not require authentication',
        recommendation: 'Implement SMTP authentication'
      }
    ]
  end
  
  def check_dns_vulnerabilities(port)
    [
      {
        severity: 'medium',
        description: 'DNS may be vulnerable to cache poisoning',
        recommendation: 'Implement DNSSEC'
      },
      {
        severity: 'low',
        description: 'DNS recursion may be enabled',
        recommendation: 'Disable DNS recursion for public servers'
      }
    ]
  end
  
  def check_web_vulnerabilities(port)
    [
      {
        severity: 'medium',
        description: 'Web server may have default configuration',
        recommendation: 'Secure web server configuration'
      },
      {
        severity: 'high',
        description: 'Web server may have known vulnerabilities',
        recommendation: 'Update web server software'
      }
    ]
  end
  
  def check_smb_vulnerabilities(port)
    [
      {
        severity: 'high',
        description: 'SMB may have known vulnerabilities',
        recommendation: 'Update SMB protocol version'
      },
      {
        severity: 'medium',
        description: 'SMB may allow anonymous access',
        recommendation: 'Restrict SMB access'
      }
    ]
  end
  
  def check_mysql_vulnerabilities(port)
    [
      {
        severity: 'medium',
        description: 'MySQL may have default configuration',
        recommendation: 'Secure MySQL configuration'
      },
      {
        severity: 'low',
        description: 'MySQL version may be outdated',
        recommendation: 'Update MySQL to latest version'
      }
    ]
  end
  
  def check_postgresql_vulnerabilities(port)
    [
      {
        severity: 'medium',
        description: 'PostgreSQL may have weak authentication',
        recommendation: 'Implement strong authentication'
      },
      {
        severity: 'low',
        description: 'PostgreSQL version may be outdated',
        recommendation: 'Update PostgreSQL to latest version'
      }
    ]
  end
end
```

## 🔒 Firewall and Packet Filtering

### 3. Firewall Implementation

Network traffic filtering:

```ruby
class Firewall
  def initialize
    @rules = []
    @default_policy = :deny
    @log_file = 'firewall.log'
    @blocked_ips = Set.new
    @allowed_ips = Set.new
    @blocked_ports = Set.new
    @allowed_ports = Set.new
  end
  
  attr_reader :default_policy
  
  def add_rule(rule)
    @rules << rule
    puts "Added rule: #{rule[:action]} #{rule[:protocol]} #{rule[:source]}:#{rule[:source_port]} -> #{rule[:destination]}:#{rule[:destination_port]}"
  end
  
  def remove_rule(index)
    removed = @rules.delete_at(index)
    puts "Removed rule: #{removed[:action]} #{removed[:protocol]}"
    removed
  end
  
  def block_ip(ip)
    @blocked_ips.add(ip)
    puts "Blocked IP: #{ip}"
  end
  
  def allow_ip(ip)
    @allowed_ips.add(ip)
    puts "Allowed IP: #{ip}"
  end
  
  def block_port(port)
    @blocked_ports.add(port)
    puts "Blocked port: #{port}"
  end
  
  def allow_port(port)
    @allowed_ports.add(port)
    puts "Allowed port: #{port}"
  end
  
  def evaluate_packet(packet)
    # Check IP blocklist/allowlist
    if @blocked_ips.include?(packet[:source_ip])
      log_action(:deny, packet, "IP blocked")
      return { action: :deny, reason: "IP blocked" }
    end
    
    if @allowed_ips.include?(packet[:source_ip])
      log_action(:allow, packet, "IP allowed")
      return { action: :allow, reason: "IP allowed" }
    end
    
    # Check port blocklist/allowlist
    if @blocked_ports.include?(packet[:destination_port])
      log_action(:deny, packet, "Port blocked")
      return { action: :deny, reason: "Port blocked" }
    end
    
    if @allowed_ports.include?(packet[:destination_port])
      log_action(:allow, packet, "Port allowed")
      return { action: :allow, reason: "Port allowed" }
    end
    
    # Evaluate rules
    @rules.each do |rule|
      if rule_matches?(rule, packet)
        log_action(rule[:action], packet, "Rule matched")
        return { action: rule[:action], reason: "Rule matched" }
      end
    end
    
    # Apply default policy
    log_action(@default_policy, packet, "Default policy")
    { action: @default_policy, reason: "Default policy" }
  end
  
  def log_action(action, packet, reason)
    log_entry = {
      timestamp: Time.now.iso8601,
      action: action,
      packet: packet,
      reason: reason
    }
    
    File.open(@log_file, 'a') do |file|
      file.puts(JSON.pretty_generate(log_entry))
    end
  end
  
  def get_statistics
    stats = {
      total_packets: 0,
      allowed_packets: 0,
      denied_packets: 0,
      blocked_ips: @blocked_ips.size,
      allowed_ips: @allowed_ips.size,
      top_blocked_ips: {},
      top_allowed_ports: {},
      top_denied_ports: {}
    }
    
    # Analyze log file
    if File.exist?(@log_file)
      File.readlines(@log_file).each do |line|
        begin
          log_entry = JSON.parse(line)
          stats[:total_packets] += 1
          
          if log_entry['action'] == 'allow'
            stats[:allowed_packets] += 1
            port = log_entry['packet']['destination_port']
            stats[:top_allowed_ports][port] ||= 0
            stats[:top_allowed_ports][port] += 1
          else
            stats[:denied_packets] += 1
            ip = log_entry['packet']['source_ip']
            port = log_entry['packet']['destination_port']
            stats[:top_blocked_ips][ip] ||= 0
            stats[:top_blocked_ips][ip] += 1
            stats[:top_denied_ports][port] ||= 0
            stats[:top_denied_ports][port] += 1
          end
        rescue JSON::ParserError
          # Skip invalid log entries
        end
      end
    end
    
    stats
  end
  
  def export_rules(filename = 'firewall_rules.json')
    rules_data = {
      created_at: Time.now.iso8601,
      default_policy: @default_policy,
      rules: @rules,
      blocked_ips: @blocked_ips.to_a,
      allowed_ips: @allowed_ips.to_a,
      blocked_ports: @blocked_ports.to_a,
      allowed_ports: @allowed_ports.to_a
    }
    
    File.write(filename, JSON.pretty_generate(rules_data))
    puts "Firewall rules exported to #{filename}"
  end
  
  def import_rules(filename = 'firewall_rules.json')
    return unless File.exist?(filename)
    
    rules_data = JSON.parse(File.read(filename))
    
    @default_policy = rules_data['default_policy'].to_sym
    @rules = rules_data['rules']
    @blocked_ips = Set.new(rules_data['blocked_ips'])
    @allowed_ips = Set.new(rules_data['allowed_ips'])
    @blocked_ports = Set.new(rules_data['blocked_ports'])
    @allowed_ports = Set.new(rules_data['allowed_ports'])
    
    puts "Firewall rules imported from #{filename}"
  end
  
  def self.demonstrate_firewall
    puts "Firewall Demonstration:"
    puts "=" * 50
    
    # Create firewall
    firewall = Firewall.new
    
    # Add rules
    puts "Adding Firewall Rules:"
    
    firewall.add_rule({
      action: :allow,
      protocol: :tcp,
      source: 'any',
      source_port: 'any',
      destination: 'any',
      destination_port: 80,
      description: 'Allow HTTP traffic'
    })
    
    firewall.add_rule({
      action: :allow,
      protocol: :tcp,
      source: 'any',
      source_port: 'any',
      destination: 'any',
      destination_port: 443,
      description: 'Allow HTTPS traffic'
    })
    
    firewall.add_rule({
      action: :deny,
      protocol: :tcp,
      source: 'any',
      source_port: 'any',
      destination: 'any',
      destination_port: 22,
      description: 'Deny SSH traffic'
    })
    
    # Block/allow specific IPs and ports
    puts "\nIP and Port Controls:"
    firewall.block_ip('192.168.1.100')
    firewall.allow_ip('192.168.1.10')
    firewall.block_port(23)
    firewall.allow_port(22)
    
    # Test packet evaluation
    puts "\nTesting Packet Evaluation:"
    
    test_packets = [
      { source_ip: '192.168.1.1', destination_ip: '10.0.0.1', destination_port: 80 },
      { source_ip: '192.168.1.100', destination_ip: '10.0.0.1', destination_port: 80 },
      { source_ip: '192.168.1.1', destination_ip: '10.0.0.1', destination_port: 22 },
      { source_ip: '192.168.1.1', destination_ip: '10.0.0.1', destination_port: 23 },
      { source_ip: '192.168.1.10', destination_ip: '10.0.0.1', destination_port: 443 }
    ]
    
    test_packets.each do |packet|
      result = firewall.evaluate_packet(packet)
      puts "Packet from #{packet[:source_ip]} to port #{packet[:destination_port]}: #{result[:action]} (#{result[:reason]})"
    end
    
    # Get statistics
    puts "\nFirewall Statistics:"
    stats = firewall.get_statistics
    
    puts "Total packets: #{stats[:total_packets]}"
    puts "Allowed packets: #{stats[:allowed_packets]}"
    puts "Denied packets: #{stats[:denied_packets]}"
    puts "Blocked IPs: #{stats[:blocked_ips]}"
    puts "Allowed IPs: #{stats[:allowed_ips]}"
    
    # Export rules
    puts "\nExporting Rules:"
    firewall.export_rules
    
    puts "\nFirewall Features:"
    puts "- Rule-based packet filtering"
    puts "- IP blocklist/allowlist"
    puts "- Port blocklist/allowlist"
    puts "- Comprehensive logging"
    puts "- Statistics and reporting"
    puts "- Rule import/export"
  end
  
  private
  
  def rule_matches?(rule, packet)
    # Check protocol
    return false if rule[:protocol] && rule[:protocol] != packet[:protocol]
    
    # Check source
    return false if rule[:source] && rule[:source] != 'any' && rule[:source] != packet[:source_ip]
    
    # Check source port
    return false if rule[:source_port] && rule[:source_port] != 'any' && rule[:source_port] != packet[:source_port]
    
    # Check destination
    return false if rule[:destination] && rule[:destination] != 'any' && rule[:destination] != packet[:destination_ip]
    
    # Check destination port
    return false if rule[:destination_port] && rule[:destination_port] != 'any' && rule[:destination_port] != packet[:destination_port]
    
    true
  end
end

class PacketFilter
  def initialize
    @filters = []
    @actions = []
  end
  
  def add_filter(filter_proc, action = :allow)
    @filters << filter_proc
    @actions << action
  end
  
  def filter_packet(packet)
    @filters.each_with_index do |filter, index|
      if filter.call(packet)
        return { action: @actions[index], filter_index: index }
      end
    end
    
    { action: :allow, filter_index: -1 }
  end
  
  def self.demonstrate_packet_filtering
    puts "Packet Filtering Demonstration:"
    puts "=" * 50
    
    filter = PacketFilter.new
    
    # Add filters
    puts "Adding Packet Filters:"
    
    filter.add_filter(
      ->(packet) { packet[:source_port] == 22 },
      :allow
    )
    
    filter.add_filter(
      ->(packet) { packet[:destination_port] == 80 },
      :allow
    )
    
    filter.add_filter(
      ->(packet) { packet[:source_ip].start_with?('192.168.1.') },
      :allow
    )
    
    filter.add_filter(
      ->(packet) { packet[:source_ip] == '10.0.0.100' },
      :deny
    )
    
    # Test packet filtering
    puts "\nTesting Packet Filtering:"
    
    test_packets = [
      { source_ip: '192.168.1.1', source_port: 22, destination_port: 80 },
      { source_ip: '10.0.0.1', source_port: 80, destination_port: 443 },
      { source_ip: '10.0.0.100', source_port: 80, destination_port: 443 },
      { source_ip: '10.0.0.1', source_port: 25, destination_port: 110 }
    ]
    
    test_packets.each do |packet|
      result = filter.filter_packet(packet)
      puts "Packet from #{packet[:source_ip]}:#{packet[:source_port]} -> #{packet[:destination_port]}: #{result[:action]}"
    end
    
    puts "\nPacket Filtering Features:"
    puts "- Custom filter functions"
    puts "- Chain of filters"
    puts "- Action-based filtering"
    puts "- Flexible packet inspection"
  end
end
```

## 🛡️ Intrusion Detection

### 4. Intrusion Detection System

Monitor and detect suspicious activities:

```ruby
class IntrusionDetectionSystem
  def initialize
    @rules = []
    @alerts = []
    @baseline = {}
    @anomaly_threshold = 2.0 # 2 standard deviations
    @alert_file = 'ids_alerts.log'
  end
  
  def add_rule(rule)
    @rules << rule
    puts "Added IDS rule: #{rule[:name]}"
  end
  
  def analyze_event(event)
    alerts = []
    
    # Check against rules
    @rules.each do |rule|
      if rule_matches?(rule, event)
        alert = create_alert(rule, event)
        alerts << alert
        @alerts << alert
        log_alert(alert)
      end
    end
    
    # Check for anomalies
    anomaly_alerts = detect_anomalies(event)
    alerts.concat(anomaly_alerts)
    
    alerts
  end
  
  def establish_baseline(events)
    puts "Establishing behavioral baseline..."
    
    # Calculate baseline metrics
    @baseline = {
      avg_events_per_minute: calculate_avg_events_per_minute(events),
      avg_packet_size: calculate_avg_packet_size(events),
      common_ports: calculate_common_ports(events),
      common_protocols: calculate_common_protocols(events),
      time_distribution: calculate_time_distribution(events)
    }
    
    puts "Baseline established:"
    puts "- Avg events/min: #{@baseline[:avg_events_per_minute]}"
    puts "- Avg packet size: #{@baseline[:avg_packet_size]}"
    puts "- Common ports: #{@baseline[:common_ports].keys.join(', ')}"
    puts "- Common protocols: #{@baseline[:common_protocols].keys.join(', ')}"
  end
  
  def detect_anomalies(event)
    anomalies = []
    
    # Check event rate anomaly
    current_rate = get_current_event_rate
    if current_rate > @baseline[:avg_events_per_minute] * @anomaly_threshold
      anomalies << create_anomaly_alert('high_event_rate', event, current_rate)
    end
    
    # Check packet size anomaly
    if event[:packet_size] && @baseline[:avg_packet_size]
      size_ratio = event[:packet_size].to_f / @baseline[:avg_packet_size]
      if size_ratio > @anomaly_threshold
        anomalies << create_anomaly_alert('unusual_packet_size', event, size_ratio)
      end
    end
    
    # Check unusual port
    if event[:destination_port] && @baseline[:common_ports]
      unless @baseline[:common_ports].include?(event[:destination_port])
        anomalies << create_anomaly_alert('unusual_port', event, event[:destination_port])
      end
    end
    
    # Check unusual protocol
    if event[:protocol] && @baseline[:common_protocols]
      unless @baseline[:common_protocols].include?(event[:protocol])
        anomalies << create_anomaly_alert('unusual_protocol', event, event[:protocol])
      end
    end
    
    anomalies.each { |alert| log_alert(alert) }
    anomalies
  end
  
  def get_alerts(severity = nil, time_range = nil)
    alerts = @alerts
    
    # Filter by severity
    alerts = alerts.select { |alert| alert[:severity] == severity } if severity
    
    # Filter by time range
    if time_range
      start_time = Time.now - time_range
      alerts = alerts.select { |alert| alert[:timestamp] >= start_time }
    end
    
    alerts
  end
  
  def generate_report
    puts "Generating IDS Report..."
    
    report = {
      generated_at: Time.now.iso8601,
      total_alerts: @alerts.length,
      alerts_by_severity: {},
      alerts_by_type: {},
      recent_alerts: get_alerts(nil, 3600), # Last hour
      top_attack_sources: {},
      top_target_ports: {},
      recommendations: []
    }
    
    # Analyze alerts by severity
    %w[low medium high critical].each do |severity|
      count = @alerts.count { |alert| alert[:severity] == severity }
      report[:alerts_by_severity][severity] = count
    end
    
    # Analyze alerts by type
    alert_types = @alerts.map { |alert| alert[:type] }.uniq
    alert_types.each do |type|
      count = @alerts.count { |alert| alert[:type] == type }
      report[:alerts_by_type][type] = count
    end
    
    # Analyze top attack sources
    source_ips = @alerts.map { |alert| alert[:event][:source_ip] }.compact
    source_ips.each do |ip|
      count = source_ips.count(ip)
      report[:top_attack_sources][ip] = count
    end
    
    report[:top_attack_sources] = report[:top_attack_sources].sort_by { |_, count| -count }.first(10).to_h
    
    # Analyze top target ports
    target_ports = @alerts.map { |alert| alert[:event][:destination_port] }.compact
    target_ports.each do |port|
      count = target_ports.count(port)
      report[:top_target_ports][port] = count
    end
    
    report[:top_target_ports] = report[:top_target_ports].sort_by { |_, count| -count }.first(10).to_h
    
    # Generate recommendations
    report[:recommendations] = generate_recommendations(report)
    
    # Save report
    filename = "ids_report_#{Time.now.strftime('%Y%m%d_%H%M%S')}.json"
    File.write(filename, JSON.pretty_generate(report))
    
    puts "IDS Report generated: #{filename}"
    report
  end
  
  def self.demonstrate_ids
    puts "Intrusion Detection System Demonstration:"
    puts "=" * 50
    
    ids = IntrusionDetectionSystem.new
    
    # Add detection rules
    puts "Adding Detection Rules:"
    
    ids.add_rule({
      name: 'Brute Force Attack',
      type: 'brute_force',
      severity: 'high',
      condition: ->(event) { event[:type] == 'login_failure' && event[:count] > 5 },
      description: 'Multiple failed login attempts'
    })
    
    ids.add_rule({
      name: 'Port Scan',
      type: 'port_scan',
      severity: 'medium',
      condition: ->(event) { event[:type] == 'connection_attempt' && event[:unique_ports] > 10 },
      description: 'Scanning multiple ports'
    })
    
    ids.add_rule({
      name: 'Suspicious Traffic',
      type: 'suspicious_traffic',
      severity: 'medium',
      condition: ->(event) { event[:protocol] == 'unknown' || event[:packet_size] > 1500 },
      description: 'Unusual traffic patterns'
    })
    
    ids.add_rule({
      name: 'DDoS Attack',
      type: 'ddos',
      severity: 'critical',
      condition: ->(event) { event[:type] == 'connection_attempt' && event[:source_ip_connections] > 100 },
      description: 'High number of connections from single IP'
    })
    
    # Generate sample events
    puts "\nAnalyzing Sample Events:"
    
    sample_events = [
      {
        timestamp: Time.now,
        type: 'login_failure',
        source_ip: '192.168.1.100',
        count: 7,
        user: 'admin'
      },
      {
        timestamp: Time.now,
        type: 'connection_attempt',
        source_ip: '10.0.0.50',
        unique_ports: 15,
        destination_ports: [21, 22, 23, 25, 53, 80, 110, 143, 443, 993, 995, 1433, 3306, 5432, 8080]
      },
      {
        timestamp: Time.now,
        type: 'connection_attempt',
        source_ip: '10.0.0.60',
        source_ip_connections: 150,
        protocol: 'tcp'
      },
      {
        timestamp: Time.now,
        type: 'normal_traffic',
        source_ip: '192.168.1.10',
        protocol: 'http',
        packet_size: 1024
      }
    ]
    
    sample_events.each do |event|
      alerts = ids.analyze_event(event)
      if alerts.any?
        puts "Event: #{event[:type]} from #{event[:source_ip]}"
        alerts.each do |alert|
          puts "  ALERT: #{alert[:severity]} - #{alert[:description]}"
        end
      end
    end
    
    # Establish baseline
    puts "\nEstablishing Baseline:"
    baseline_events = generate_sample_events(100)
    ids.establish_baseline(baseline_events)
    
    # Generate report
    puts "\nGenerating IDS Report:"
    report = ids.generate_report
    
    puts "\nIDS Features:"
    puts "- Rule-based detection"
    puts "- Anomaly detection"
    puts "- Behavioral baseline"
    puts "- Alert generation"
    puts "- Comprehensive reporting"
    puts "- Statistical analysis"
  end
  
  private
  
  def rule_matches?(rule, event)
    rule[:condition].call(event)
  end
  
  def create_alert(rule, event)
    {
      id: SecureRandom.uuid,
      timestamp: Time.now,
      rule: rule[:name],
      type: rule[:type],
      severity: rule[:severity],
      description: rule[:description],
      event: event
    }
  end
  
  def create_anomaly_alert(anomaly_type, event, value)
    {
      id: SecureRandom.uuid,
      timestamp: Time.now,
      rule: 'Anomaly Detection',
      type: anomaly_type,
      severity: 'medium',
      description: "Anomaly detected: #{anomaly_type}",
      event: event,
      value: value
    }
  end
  
  def log_alert(alert)
    log_entry = {
      timestamp: alert[:timestamp].iso8601,
      alert: alert
    }
    
    File.open(@alert_file, 'a') do |file|
      file.puts(JSON.pretty_generate(log_entry))
    end
    
    puts "ALERT: #{alert[:severity]} - #{alert[:description]}"
  end
  
  def calculate_avg_events_per_minute(events)
    return 0 if events.empty?
    
    time_span = events.last[:timestamp] - events.first[:timestamp]
    time_span = 60 if time_span < 60 # Minimum 1 minute
    
    events.length.to_f / (time_span / 60)
  end
  
  def calculate_avg_packet_size(events)
    sizes = events.map { |e| e[:packet_size] }.compact
    return 0 if sizes.empty?
    
    sizes.sum.to_f / sizes.length
  end
  
  def calculate_common_ports(events)
    ports = events.map { |e| e[:destination_port] }.compact
    port_counts = Hash.new(0)
    
    ports.each { |port| port_counts[port] += 1 }
    
    # Return top 10 ports
    port_counts.sort_by { |_, count| -count }.first(10).to_h
  end
  
  def calculate_common_protocols(events)
    protocols = events.map { |e| e[:protocol] }.compact
    protocol_counts = Hash.new(0)
    
    protocols.each { |protocol| protocol_counts[protocol] += 1 }
    
    protocol_counts
  end
  
  def calculate_time_distribution(events)
    # Simplified time distribution
    hours = Hash.new(0)
    
    events.each do |event|
      hour = event[:timestamp].hour
      hours[hour] += 1
    end
    
    hours
  end
  
  def get_current_event_rate
    # Simplified current rate calculation
    Time.now.to_i % 100
  end
  
  def generate_sample_events(count)
    events = []
    
    count.times do |i|
      events << {
        timestamp: Time.now - i.minutes,
        type: ['normal_traffic', 'connection_attempt', 'data_transfer'].sample,
        source_ip: "192.168.1.#{rand(1..254)}",
        protocol: ['tcp', 'udp'].sample,
        packet_size: rand(100..1500),
        destination_port: rand(1..65535)
      }
    end
    
    events
  end
  
  def generate_recommendations(report)
    recommendations = []
    
    if report[:alerts_by_severity]['high'] > 10 || report[:alerts_by_severity]['critical'] > 5
      recommendations << 'Increase monitoring frequency and response capability'
    end
    
    if report[:alerts_by_type]['brute_force'] > 5
      recommendations << 'Implement stronger authentication mechanisms'
    end
    
    if report[:alerts_by_type]['port_scan'] > 10
      recommendations << 'Consider implementing port knocking or stealth techniques'
    end
    
    if report[:alerts_by_type]['ddos'] > 3
      recommendations << 'Implement DDoS protection and rate limiting'
    end
    
    recommendations << 'Regular security audits and penetration testing'
    recommendations << 'Keep IDS signatures and rules up to date'
    
    recommendations
  end
end
```

## 🔍 Network Monitoring

### 5. Network Traffic Analysis

Monitor and analyze network traffic:

```ruby
class NetworkMonitor
  def initialize
    @traffic_data = []
    @protocols = Hash.new(0)
    @ports = Hash.new(0)
    @sources = Hash.new(0)
    @destinations = Hash.new(0)
    @monitoring_active = false
    @monitor_thread = nil
  end
  
  def start_monitoring(interface = 'eth0')
    puts "Starting network monitoring on #{interface}..."
    
    @monitoring_active = true
    @monitor_thread = Thread.new do
      while @monitoring_active
        capture_traffic(interface)
        sleep(1) # Capture every second
      end
    end
    
    puts "Network monitoring started"
  end
  
  def stop_monitoring
    puts "Stopping network monitoring..."
    
    @monitoring_active = false
    @monitor_thread&.join
    
    puts "Network monitoring stopped"
  end
  
  def capture_traffic(interface)
    # Simulate traffic capture
    # In real implementation, this would use packet capture libraries
    
    traffic = generate_sample_traffic
    
    traffic.each do |packet|
      @traffic_data << packet
      @protocols[packet[:protocol]] += 1
      @ports[packet[:destination_port]] += 1
      @sources[packet[:source_ip]] += 1
      @destinations[packet[:destination_ip]] += 1
    end
  end
  
  def get_traffic_statistics
    {
      total_packets: @traffic_data.length,
      protocols: @protocols,
      top_ports: @ports.sort_by { |_, count| -count }.first(10).to_h,
      top_sources: @sources.sort_by { |_, count| -count }.first(10).to_h,
      top_destinations: @destinations.sort_by { |_, count| -count }.first(10).to_h,
      avg_packet_size: calculate_avg_packet_size,
      bandwidth_usage: calculate_bandwidth_usage
    }
  end
  
  def detect_traffic_anomalies
    anomalies = []
    
    # Check for unusual protocols
    total_packets = @traffic_data.length
    @protocols.each do |protocol, count|
      percentage = (count.to_f / total_packets) * 100
      
      if percentage > 20 # More than 20% of traffic
        anomalies << {
          type: 'unusual_protocol',
          protocol: protocol,
          percentage: percentage,
          description: "Unusual amount of #{protocol} traffic"
        }
      end
    end
    
    # Check for unusual ports
    @ports.each do |port, count|
      if port > 49152 && count > 100 # Dynamic ports with high traffic
        anomalies << {
          type: 'unusual_port',
          port: port,
          count: count,
          description: "High traffic on dynamic port #{port}"
        }
      end
    end
    
    # Check for high traffic sources
    @sources.each do |source, count|
      if count > total_packets * 0.1 # More than 10% from single source
        anomalies << {
          type: 'high_traffic_source',
          source: source,
          count: count,
          percentage: (count.to_f / total_packets) * 100,
          description: "High traffic from #{source}"
        }
      end
    end
    
    anomalies
  end
  
  def generate_traffic_report
    puts "Generating Traffic Report..."
    
    stats = get_traffic_statistics
    anomalies = detect_traffic_anomalies
    
    report = {
      generated_at: Time.now.iso8601,
      statistics: stats,
      anomalies: anomalies,
      recommendations: generate_traffic_recommendations(stats, anomalies)
    }
    
    # Save report
    filename = "traffic_report_#{Time.now.strftime('%Y%m%d_%H%M%S')}.json"
    File.write(filename, JSON.pretty_generate(report))
    
    puts "Traffic report generated: #{filename}"
    report
  end
  
  def visualize_traffic
    puts "Traffic Visualization:"
    puts "=" * 50
    
    stats = get_traffic_statistics
    
    # Protocol distribution
    puts "\nProtocol Distribution:"
    total = stats[:protocols].values.sum
    stats[:protocols].each do |protocol, count|
      percentage = (count.to_f / total * 100).round(2)
      bar_length = (percentage / 100 * 50).round
      bar = '█' * bar_length + '░' * (50 - bar_length)
      puts "#{protocol.ljust(10)}: #{bar} #{percentage}%"
    end
    
    # Top ports
    puts "\nTop Destination Ports:"
    stats[:top_ports].each_with_index do |(port, count), i|
      puts "#{i + 1}. Port #{port}: #{count} packets"
    end
    
    # Top sources
    puts "\nTop Source IPs:"
    stats[:top_sources].each_with_index do |(ip, count), i|
      puts "#{i + 1}. #{ip}: #{count} packets"
    end
    
    # Bandwidth usage
    puts "\nBandwidth Usage:"
    puts "Average packet size: #{stats[:avg_packet_size]} bytes"
    puts "Total bandwidth: #{stats[:bandwidth_usage]} MB/s"
    
    # Anomalies
    anomalies = detect_traffic_anomalies
    if anomalies.any?
      puts "\nTraffic Anomalies:"
      anomalies.each do |anomaly|
        puts "- #{anomaly[:type]}: #{anomaly[:description]}"
      end
    else
      puts "\nNo traffic anomalies detected"
    end
  end
  
  def self.demonstrate_monitoring
    puts "Network Monitoring Demonstration:"
    puts "=" * 50
    
    monitor = NetworkMonitor.new
    
    # Generate sample traffic
    puts "Generating Sample Traffic:"
    5.times do
      monitor.capture_traffic('eth0')
      sleep(0.1)
    end
    
    # Display statistics
    puts "\nTraffic Statistics:"
    stats = monitor.get_traffic_statistics
    
    puts "Total packets: #{stats[:total_packets]}"
    puts "Protocols: #{stats[:protocols]}"
    puts "Top ports: #{stats[:top_ports]}"
    puts "Top sources: #{stats[:top_sources]}"
    puts "Average packet size: #{stats[:avg_packet_size]}"
    
    # Detect anomalies
    puts "\nAnomaly Detection:"
    anomalies = monitor.detect_traffic_anomalies
    
    if anomalies.any?
      puts "Found #{anomalies.length} anomalies:"
      anomalies.each { |anomaly| puts "- #{anomaly[:type]}: #{anomaly[:description]}" }
    else
      puts "No anomalies detected"
    end
    
    # Visualize traffic
    puts "\nTraffic Visualization:"
    monitor.visualize_traffic
    
    # Generate report
    puts "\nGenerating Report:"
    report = monitor.generate_traffic_report
    
    puts "\nNetwork Monitoring Features:"
    puts "- Real-time traffic capture"
    puts "- Protocol analysis"
    puts "- Port statistics"
    puts "- Source/destination tracking"
    puts "- Anomaly detection"
    puts "- Traffic visualization"
    puts "- Comprehensive reporting"
  end
  
  private
  
  def generate_sample_traffic
    protocols = ['tcp', 'udp', 'icmp']
    source_ips = (1..10).map { |i| "192.168.1.#{i}" }
    destination_ips = (1..10).map { |i| "10.0.0.#{i}" }
    
    traffic = []
    
    rand(5..15).times do
      traffic << {
        timestamp: Time.now,
        source_ip: source_ips.sample,
        destination_ip: destination_ips.sample,
        source_port: rand(1024..65535),
        destination_port: [80, 443, 22, 25, 53, 3306, 5432].sample,
        protocol: protocols.sample,
        packet_size: rand(64..1500),
        flags: ['SYN', 'ACK', 'FIN', 'RST'].sample
      }
    end
    
    traffic
  end
  
  def calculate_avg_packet_size
    return 0 if @traffic_data.empty?
    
    sizes = @traffic_data.map { |packet| packet[:packet_size] }
    sizes.sum.to_f / sizes.length
  end
  
  def calculate_bandwidth_usage
    # Simplified bandwidth calculation
    total_size = @traffic_data.sum { |packet| packet[:packet_size] }
    time_span = @traffic_data.empty? ? 60 : (@traffic_data.last[:timestamp] - @traffic_data.first[:timestamp])
    
    (total_size / 1024.0 / 1024.0) / (time_span || 60) # MB/s
  end
  
  def generate_traffic_recommendations(stats, anomalies)
    recommendations = []
    
    if stats[:protocols]['unknown'] > 0
      recommendations << 'Investigate unknown protocols in network traffic'
    end
    
    if anomalies.any? { |a| a[:type] == 'high_traffic_source' }
      recommendations << 'Monitor high traffic sources for potential attacks'
    end
    
    if anomalies.any? { |a| a[:type] == 'unusual_port' }
      recommendations << 'Investigate traffic on unusual ports'
    end
    
    if stats[:avg_packet_size] > 1200
      recommendations << 'Consider optimizing packet sizes for better performance'
    end
    
    recommendations << 'Implement network segmentation for better security'
    recommendations << 'Regular network traffic analysis and monitoring'
    
    recommendations
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Network Scanning**: Implement port scanner
2. **Firewall Rules**: Create packet filtering
3. **Traffic Analysis**: Monitor network traffic
4. **Security Monitoring**: Basic IDS setup

### Intermediate Exercises

1. **Advanced Firewall**: Implement stateful firewall
2. **Intrusion Detection**: Build comprehensive IDS
3. **Packet Analysis**: Deep packet inspection
4. **Anomaly Detection**: Statistical analysis

### Advanced Exercises

1. **Network Security Platform**: Complete security solution
2. **Real-time Monitoring**: Live threat detection
3. **Machine Learning**: AI-based threat detection
4. **Integration**: SIEM system integration

---

## 🎯 Summary

Network Security in Ruby provides:

- **Security Fundamentals** - Core network security concepts
- **Network Scanning** - Port scanning and enumeration
- **Firewall Implementation** - Packet filtering and rules
- **Intrusion Detection** - Threat detection and alerting
- **Network Monitoring** - Traffic analysis and visualization
- **Security Best Practices** - Comprehensive protection strategies

Master these network security techniques for robust Ruby applications!
