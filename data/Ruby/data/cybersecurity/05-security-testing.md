# Security Testing in Ruby
# Comprehensive guide to security testing methodologies and implementations

## 🔍 Security Testing Fundamentals

### 1. Security Testing Concepts

Core security testing principles:

```ruby
class SecurityTestingFundamentals
  def self.explain_security_testing_concepts
    puts "Security Testing Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Security Testing",
        description: "Testing applications for security vulnerabilities",
        purpose: "Identify and fix security issues before deployment",
        scope: ["Authentication", "Authorization", "Input validation", "Data protection"],
        importance: "Prevents security breaches and data loss"
      },
      {
        concept: "Penetration Testing",
        description: "Simulated attacks to test security defenses",
        types: ["Black box", "White box", "Gray box"],
        phases: ["Reconnaissance", "Scanning", "Exploitation", "Post-exploitation", "Reporting"],
        benefits: ["Real-world attack simulation", "Vulnerability discovery", "Security validation"]
      },
      {
        concept: "Vulnerability Assessment",
        description: "Systematic search for security vulnerabilities",
        methods: ["Automated scanning", "Manual testing", "Code review"],
        tools: ["Static analysis", "Dynamic analysis", "Interactive analysis"],
        output: "Comprehensive vulnerability report"
      },
      {
        concept: "Security Testing Pyramid",
        description: "Hierarchy of security testing methods",
        levels: ["Unit tests", "Integration tests", "System tests", "Penetration tests"],
        balance: "Mix of automated and manual testing",
        coverage: "Comprehensive security validation"
      },
      {
        concept: "Threat Modeling",
        description: "Identify and analyze potential threats",
        process: ["Asset identification", "Threat analysis", "Vulnerability assessment", "Risk evaluation"],
        methodologies: ["STRIDE", "DREAD", "PASTA"],
        benefits: ["Proactive security", "Risk prioritization", "Security design"]
      },
      {
        concept: "Security Code Review",
        description: "Manual review of source code for security issues",
        focus: ["Authentication", "Authorization", "Input validation", "Cryptography"],
        techniques: ["Pattern matching", "Data flow analysis", "Control flow analysis"],
        tools: ["Static analysis", "Manual review", "Peer review"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Purpose: #{concept[:purpose]}" if concept[:purpose]
      puts "  Scope: #{concept[:scope].join(', ')}" if concept[:scope]
      puts "  Importance: #{concept[:importance]}" if concept[:importance]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Phases: #{concept[:phases].join(', ')}" if concept[:phases]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Methods: #{concept[:methods].join(', ')}" if concept[:methods]
      puts "  Tools: #{concept[:tools].join(', ')}" if concept[:tools]
      puts "  Output: #{concept[:output]}" if concept[:output]
      puts "  Levels: #{concept[:levels].join(', ')}" if concept[:levels]
      puts "  Balance: #{concept[:balance]}" if concept[:balance]
      puts "  Coverage: #{concept[:coverage]}" if concept[:coverage]
      puts "  Process: #{concept[:process].join(', ')}" if concept[:process]
      puts "  Methodologies: #{concept[:methodologies].join(', ')}" if concept[:methodologies]
      puts "  Focus: #{concept[:focus].join(', ')}" if concept[:focus]
      puts "  Techniques: #{concept[:techniques].join(', ')}" if concept[:techniques]
      puts
    end
  end
  
  def self.testing_methodologies
    puts "\nSecurity Testing Methodologies:"
    puts "=" * 50
    
    methodologies = [
      {
        methodology: "Black Box Testing",
        description: "Testing without knowledge of internal structure",
        advantages: ["Real-world simulation", "Unbiased testing", "External perspective"],
        disadvantages: ["Limited coverage", "Time-consuming", "Requires expertise"],
        use_cases: ["Penetration testing", "Vulnerability scanning"]
      },
      {
        methodology: "White Box Testing",
        description: "Testing with full knowledge of internal structure",
        advantages: ["Comprehensive coverage", "Efficient", "Code-level analysis"],
        disadvantages: ["May miss real-world issues", "Requires code access", "Time-intensive"],
        use_cases: ["Code review", "Unit testing", "Static analysis"]
      },
      {
        methodology: "Gray Box Testing",
        description: "Testing with partial knowledge of internal structure",
        advantages: ["Balanced approach", "Realistic testing", "Good coverage"],
        disadvantages: ["Requires some knowledge", "Complex setup", "Limited scope"],
        use_cases: ["Integration testing", "API testing", "System testing"]
      }
    ]
    
    methodologies.each do |methodology|
      puts "#{methodology[:methodology]}:"
      puts "  Description: #{methodology[:description]}"
      puts "  Advantages: #{methodology[:advantages].join(', ')}"
      puts "  Disadvantages: #{methodology[:disadvantages].join(', ')}"
      puts "  Use Cases: #{methodology[:use_cases].join(', ')}"
      puts
    end
  end
  
  def self.security_testing_types
    puts "\nSecurity Testing Types:"
    puts "=" * 50
    
    types = [
      {
        type: "Static Application Security Testing (SAST)",
        description: "Analyze source code without executing",
        tools: ["SonarQube", "Checkmarx", "Fortify", "CodeQL"],
        advantages: ["Early detection", "Code-level analysis", "Comprehensive"],
        limitations: ["False positives", "No runtime context", "Code dependency"]
      },
      {
        type: "Dynamic Application Security Testing (DAST)",
        description: "Test running application for vulnerabilities",
        tools: ["OWASP ZAP", "Burp Suite", "Nessus", "Acunetix"],
        advantages: ["Real-world testing", "Runtime context", "False negatives"],
        limitations: ["Limited coverage", "No code analysis", "Black box only"]
      },
      {
        type: "Interactive Application Security Testing (IAST)",
        description: "Combine SAST and DAST approaches",
        tools: ["Contrast Security", "Seeker", "Hdiv"],
        advantages: ["Best of both worlds", "Runtime analysis", "Code context"],
        limitations: ["Complex setup", "Performance impact", "Tool dependency"]
      },
      {
        type: "Software Composition Analysis (SCA)",
        description: "Analyze third-party dependencies",
        tools: ["Snyk", "OWASP Dependency-Check", "WhiteSource"],
        advantages: ["Dependency security", "License compliance", "Vulnerability database"],
        limitations: ["No custom code", "False positives", "Database dependency"]
      }
    ]
    
    types.each do |type|
      puts "#{type[:type]}:"
      puts "  Description: #{type[:description]}"
      puts "  Tools: #{type[:tools].join(', ')}"
      puts "  Advantages: #{type[:advantages].join(', ')}"
      puts "  Limitations: #{type[:limitations].join(', ')}"
      puts
    end
  end
  
  # Run security testing fundamentals
  explain_security_testing_concepts
  testing_methodologies
  security_testing_types
end
```

### 2. Vulnerability Scanning

Automated vulnerability detection:

```ruby
class VulnerabilityScanner
  def initialize(target)
    @target = target
    @vulnerabilities = []
    @scan_results = {}
  end
  
  def scan_sql_injection
    puts "Scanning for SQL Injection vulnerabilities..."
    
    sql_payloads = [
      "' OR '1'='1",
      "' OR '1'='1' --",
      "' OR '1'='1' /*",
      "'; DROP TABLE users; --",
      "' UNION SELECT * FROM users --",
      "1' OR '1'='1",
      "admin'--",
      "admin'/*",
      "' OR 1=1--"
    ]
    
    test_endpoints = [
      '/login',
      '/search',
      '/api/users',
      '/products',
      '/admin/users'
    ]
    
    sql_vulnerabilities = []
    
    test_endpoints.each do |endpoint|
      sql_payloads.each do |payload|
        # Simulate SQL injection test
        response = simulate_request(endpoint, { query: payload })
        
        if response[:body].include?('SQL') || response[:body].include?('error') || response[:status] == 500
          sql_vulnerabilities << {
            endpoint: endpoint,
            payload: payload,
            severity: 'high',
            description: 'SQL Injection vulnerability detected',
            recommendation: 'Use parameterized queries'
          }
        end
      end
    end
    
    @scan_results[:sql_injection] = sql_vulnerabilities
    puts "Found #{sql_vulnerabilities.length} SQL injection vulnerabilities"
    
    sql_vulnerabilities
  end
  
  def scan_xss
    puts "Scanning for XSS vulnerabilities..."
    
    xss_payloads = [
      '<script>alert("XSS")</script>',
      '<img src="x" onerror="alert(1)">',
      '<svg onload="alert(1)">',
      '"><script>alert("XSS")</script>',
      "'><script>alert('XSS')</script>",
      '<iframe src="javascript:alert(1)">',
      '<body onload="alert(1)">',
      '<input autofocus onfocus=alert(1)>'
    ]
    
    test_endpoints = [
      '/search',
      '/profile',
      '/comments',
      '/feedback',
      '/api/data'
    ]
    
    xss_vulnerabilities = []
    
    test_endpoints.each do |endpoint|
      xss_payloads.each do |payload|
        # Simulate XSS test
        response = simulate_request(endpoint, { input: payload })
        
        if response[:body].include?(payload) || response[:body].include?('alert')
          xss_vulnerabilities << {
            endpoint: endpoint,
            payload: payload,
            severity: 'high',
            description: 'Cross-Site Scripting vulnerability detected',
            recommendation: 'Implement output encoding and CSP'
          }
        end
      end
    end
    
    @scan_results[:xss] = xss_vulnerabilities
    puts "Found #{xss_vulnerabilities.length} XSS vulnerabilities"
    
    xss_vulnerabilities
  end
  
  def scan_csrf
    puts "Scanning for CSRF vulnerabilities..."
    
    test_endpoints = [
      '/login',
      '/register',
      '/profile/update',
      '/password/change',
      '/api/transfer'
    ]
    
    csrf_vulnerabilities = []
    
    test_endpoints.each do |endpoint|
      # Check for CSRF token
      response = simulate_request(endpoint, {}, method: 'GET')
      
      unless response[:body].include?('csrf_token') || response[:body].include?('authenticity_token')
        csrf_vulnerabilities << {
          endpoint: endpoint,
          severity: 'medium',
          description: 'CSRF token missing',
          recommendation: 'Implement CSRF protection'
        }
      end
      
      # Check for SameSite cookie
      unless response[:headers]['Set-Cookie']&.include?('SameSite')
        csrf_vulnerabilities << {
          endpoint: endpoint,
          severity: 'medium',
          description: 'SameSite cookie attribute missing',
          recommendation: 'Set SameSite cookie attribute'
        }
      end
    end
    
    @scan_results[:csrf] = csrf_vulnerabilities
    puts "Found #{csrf_vulnerabilities.length} CSRF vulnerabilities"
    
    csrf_vulnerabilities
  end
  
  def scan_authentication
    puts "Scanning for authentication vulnerabilities..."
    
    auth_vulnerabilities = []
    
    # Test for weak passwords
    weak_passwords = ['password', '123456', 'admin', 'qwerty', 'letmein']
    
    weak_passwords.each do |password|
      response = simulate_request('/login', { username: 'admin', password: password })
      
      if response[:status] == 302 || response[:body].include?('dashboard')
        auth_vulnerabilities << {
          type: 'weak_password',
          severity: 'high',
          description: "Weak password accepted: #{password}",
          recommendation: 'Implement strong password policy'
        }
      end
    end
    
    # Test for default credentials
    default_creds = [
      { username: 'admin', password: 'admin' },
      { username: 'admin', password: 'password' },
      { username: 'root', password: 'root' },
      { username: 'test', password: 'test' }
    ]
    
    default_creds.each do |creds|
      response = simulate_request('/login', creds)
      
      if response[:status] == 302 || response[:body].include?('dashboard')
        auth_vulnerabilities << {
          type: 'default_credentials',
          severity: 'critical',
          description: "Default credentials work: #{creds[:username]}/#{creds[:password]}",
          recommendation: 'Change default credentials'
        }
      end
    end
    
    # Test for session fixation
    session_id = 'fixed_session_id'
    response = simulate_request('/login', { username: 'user', password: 'password' }, 
                                headers: { 'Cookie' => "session_id=#{session_id}" })
    
    if response[:headers]['Set-Cookie']&.include?(session_id)
      auth_vulnerabilities << {
        type: 'session_fixation',
        severity: 'medium',
        description: 'Session fixation vulnerability',
        recommendation: 'Regenerate session ID after login'
      }
    end
    
    @scan_results[:authentication] = auth_vulnerabilities
    puts "Found #{auth_vulnerabilities.length} authentication vulnerabilities"
    
    auth_vulnerabilities
  end
  
  def scan_authorization
    puts "Scanning for authorization vulnerabilities..."
    
    auth_vulnerabilities = []
    
    # Test for privilege escalation
    protected_endpoints = [
      '/admin/users',
      '/admin/settings',
      '/api/admin/data',
      '/user/profile',
      '/api/user/data'
    ]
    
    protected_endpoints.each do |endpoint|
      # Test with regular user
      response = simulate_request(endpoint, {}, headers: { 'Authorization' => 'Bearer user_token' })
      
      if response[:status] == 200
        auth_vulnerabilities << {
          endpoint: endpoint,
          severity: 'high',
          description: 'Authorization bypass detected',
          recommendation: 'Implement proper authorization checks'
        }
      end
      
      # Test without authentication
      response = simulate_request(endpoint, {})
      
      if response[:status] == 200
        auth_vulnerabilities << {
          endpoint: endpoint,
          severity: 'critical',
          description: 'Unprotected endpoint',
          recommendation: 'Add authentication requirement'
        }
      end
    end
    
    # Test for IDOR (Insecure Direct Object Reference)
    user_ids = [1, 2, 3, 999, 1000]
    
    user_ids.each do |user_id|
      response = simulate_request("/api/users/#{user_id}", {}, 
                                  headers: { 'Authorization' => 'Bearer user_token_1' })
      
      if response[:status] == 200 && user_id != 1
        auth_vulnerabilities << {
          endpoint: "/api/users/#{user_id}",
          severity: 'high',
          description: 'IDOR vulnerability detected',
          recommendation: 'Implement proper object ownership checks'
        }
      end
    end
    
    @scan_results[:authorization] = auth_vulnerabilities
    puts "Found #{auth_vulnerabilities.length} authorization vulnerabilities"
    
    auth_vulnerabilities
  end
  
  def scan_configuration
    puts "Scanning for configuration vulnerabilities..."
    
    config_vulnerabilities = []
    
    # Test for security headers
    security_headers = [
      'Content-Security-Policy',
      'X-Frame-Options',
      'X-Content-Type-Options',
      'Strict-Transport-Security',
      'X-XSS-Protection'
    ]
    
    response = simulate_request('/', {})
    
    security_headers.each do |header|
      unless response[:headers][header]
        config_vulnerabilities << {
          type: 'missing_security_header',
          severity: 'medium',
          description: "Missing security header: #{header}",
          recommendation: "Add #{header} header"
        }
      end
    end
    
    # Test for information disclosure
    if response[:headers]['Server']
      config_vulnerabilities << {
        type: 'information_disclosure',
        severity: 'low',
        description: "Server information disclosed: #{response[:headers]['Server']}",
        recommendation: 'Hide server information'
      }
    end
    
    # Test for directory listing
    response = simulate_request('/admin/', {})
    
    if response[:status] == 200 && response[:body].include?('Index of')
      config_vulnerabilities << {
        type: 'directory_listing',
        severity: 'medium',
        description: 'Directory listing enabled',
        recommendation: 'Disable directory listing'
      }
    end
    
    # Test for debug mode
    debug_endpoints = ['/debug', '/test', '/dev', '/phpinfo']
    
    debug_endpoints.each do |endpoint|
      response = simulate_request(endpoint, {})
      
      if response[:status] == 200 && response[:body].include?('debug')
        config_vulnerabilities << {
          endpoint: endpoint,
          severity: 'high',
          description: 'Debug endpoint exposed',
          recommendation: 'Remove debug endpoints in production'
        }
      end
    end
    
    @scan_results[:configuration] = config_vulnerabilities
    puts "Found #{config_vulnerabilities.length} configuration vulnerabilities"
    
    config_vulnerabilities
  end
  
  def generate_scan_report
    puts "Generating vulnerability scan report..."
    
    report = {
      target: @target,
      scan_date: Time.now.iso8601,
      total_vulnerabilities: @vulnerabilities.length,
      vulnerabilities_by_severity: {},
      vulnerabilities_by_type: {},
      detailed_results: @scan_results,
      recommendations: generate_recommendations
    }
    
    # Count vulnerabilities by severity
    severities = %w[critical high medium low]
    severities.each do |severity|
      count = @vulnerabilities.count { |v| v[:severity] == severity }
      report[:vulnerabilities_by_severity][severity] = count
    end
    
    # Count vulnerabilities by type
    types = @scan_results.keys
    types.each do |type|
      count = @scan_results[type].length
      report[:vulnerabilities_by_type][type] = count
    end
    
    # Save report
    filename = "vulnerability_report_#{Time.now.strftime('%Y%m%d_%H%M%S')}.json"
    File.write(filename, JSON.pretty_generate(report))
    
    puts "Vulnerability scan report generated: #{filename}"
    report
  end
  
  def self.demonstrate_scanning
    puts "Vulnerability Scanning Demonstration:"
    puts "=" * 50
    
    scanner = VulnerabilityScanner.new('http://example.com')
    
    # Run all scans
    scanner.scan_sql_injection
    scanner.scan_xss
    scanner.scan_csrf
    scanner.scan_authentication
    scanner.scan_authorization
    scanner.scan_configuration
    
    # Generate report
    report = scanner.generate_scan_report
    
    puts "\nScan Summary:"
    puts "Total vulnerabilities: #{report[:total_vulnerabilities]}"
    puts "By severity: #{report[:vulnerabilities_by_severity]}"
    puts "By type: #{report[:vulnerabilities_by_type]}"
    
    puts "\nVulnerability Scanning Features:"
    puts "- SQL injection detection"
    puts "- XSS vulnerability scanning"
    puts "- CSRF protection testing"
    puts "- Authentication testing"
    puts "- Authorization testing"
    puts "- Configuration scanning"
    puts "- Comprehensive reporting"
  end
  
  private
  
  def simulate_request(endpoint, params = {}, options = {})
    # Simulate HTTP request (simplified)
    method = options[:method] || 'GET'
    headers = options[:headers] || {}
    
    # Simulate response
    {
      status: 200,
      headers: {
        'Content-Type' => 'text/html',
        'Server' => 'Apache/2.4.41'
      },
      body: "<html><body>Response for #{endpoint}</body></html>"
    }
  end
  
  def generate_recommendations
    recommendations = []
    
    if @scan_results[:sql_injection].any?
      recommendations << 'Implement parameterized queries to prevent SQL injection'
    end
    
    if @scan_results[:xss].any?
      recommendations << 'Implement output encoding and CSP to prevent XSS'
    end
    
    if @scan_results[:csrf].any?
      recommendations << 'Implement CSRF protection tokens'
    end
    
    if @scan_results[:authentication].any?
      recommendations << 'Strengthen authentication mechanisms'
    end
    
    if @scan_results[:authorization].any?
      recommendations << 'Implement proper authorization checks'
    end
    
    if @scan_results[:configuration].any?
      recommendations << 'Fix security configuration issues'
    end
    
    recommendations << 'Regular security assessments and updates'
    recommendations << 'Security training for development team'
    
    recommendations
  end
end
```

## 🧪 Penetration Testing

### 3. Penetration Testing Framework

Comprehensive penetration testing:

```ruby
class PenetrationTester
  def initialize(target)
    @target = target
    @reconnaissance_data = {}
    @exploitation_results = {}
    @post_exploitation_data = {}
    @report_data = {}
  end
  
  def reconnaissance
    puts "Phase 1: Reconnaissance"
    puts "=" * 50
    
    # Port scanning
    puts "Port scanning..."
    @reconnaissance_data[:open_ports] = scan_ports
    
    # Service enumeration
    puts "Service enumeration..."
    @reconnaissance_data[:services] = enumerate_services
    
    # Technology identification
    puts "Technology identification..."
    @reconnaissance_data[:technologies] = identify_technologies
    
    # Directory enumeration
    puts "Directory enumeration..."
    @reconnaissance_data[:directories] = enumerate_directories
    
    # Subdomain enumeration
    puts "Subdomain enumeration..."
    @reconnaissance_data[:subdomains] = enumerate_subdomains
    
    # SSL/TLS analysis
    puts "SSL/TLS analysis..."
    @reconnaissance_data[:ssl_info] = analyze_ssl
    
    puts "Reconnaissance completed"
    @reconnaissance_data
  end
  
  def scanning
    puts "\nPhase 2: Scanning"
    puts "=" * 50
    
    # Vulnerability scanning
    puts "Vulnerability scanning..."
    @exploitation_results[:vulnerabilities] = scan_vulnerabilities
    
    # Web application scanning
    puts "Web application scanning..."
    @exploitation_results[:web_vulns] = scan_web_application
    
    # Network scanning
    puts "Network scanning..."
    @exploitation_results[:network_vulns] = scan_network
    
    # Configuration scanning
    puts "Configuration scanning..."
    @exploitation_results[:config_vulns] = scan_configuration
    
    puts "Scanning completed"
    @exploitation_results
  end
  
  def exploitation
    puts "\nPhase 3: Exploitation"
    puts "=" * 50
    
    # Attempt exploits based on vulnerabilities
    puts "Attempting exploits..."
    
    successful_exploits = []
    
    @exploitation_results[:vulnerabilities].each do |vuln|
      case vuln[:type]
      when 'sql_injection'
        result = exploit_sql_injection(vuln)
        successful_exploits << result if result[:success]
      when 'xss'
        result = exploit_xss(vuln)
        successful_exploits << result if result[:success]
      when 'rce'
        result = exploit_rce(vuln)
        successful_exploits << result if result[:success]
      when 'file_upload'
        result = exploit_file_upload(vuln)
        successful_exploits << result if result[:success]
      end
    end
    
    @exploitation_results[:successful_exploits] = successful_exploits
    puts "Successfully exploited #{successful_exploits.length} vulnerabilities"
    
    successful_exploits
  end
  
  def post_exploitation
    puts "\nPhase 4: Post-Exploitation"
    puts "=" * 50
    
    # Privilege escalation
    puts "Privilege escalation..."
    @post_exploitation_data[:privilege_escalation] = attempt_privilege_escalation
    
    # Lateral movement
    puts "Lateral movement..."
    @post_exploitation_data[:lateral_movement] = attempt_lateral_movement
    
    # Data exfiltration
    puts "Data exfiltration..."
    @post_exploitation_data[:data_exfiltration] = attempt_data_exfiltration
    
    # Persistence
    puts "Persistence..."
    @post_exploitation_data[:persistence] = attempt_persistence
    
    # Cleanup
    puts "Cleanup..."
    @post_exploitation_data[:cleanup] = cleanup_traces
    
    puts "Post-exploitation completed"
    @post_exploitation_data
  end
  
  def reporting
    puts "\nPhase 5: Reporting"
    puts "=" * 50
    
    @report_data = {
      executive_summary: generate_executive_summary,
      technical_findings: generate_technical_findings,
      risk_assessment: generate_risk_assessment,
      remediation_plan: generate_remediation_plan,
      appendix: generate_appendix
    }
    
    # Save report
    filename = "pentest_report_#{Time.now.strftime('%Y%m%d_%H%M%S')}.json"
    File.write(filename, JSON.pretty_generate(@report_data))
    
    puts "Penetration test report generated: #{filename}"
    @report_data
  end
  
  def run_full_pentest
    puts "Starting Full Penetration Test"
    puts "=" * 50
    puts "Target: #{@target}"
    puts "Start Time: #{Time.now}"
    
    # Run all phases
    reconnaissance
    scanning
    exploitation
    post_exploitation
    reporting
    
    puts "\nPenetration Test Completed"
    puts "End Time: #{Time.now}"
    puts "Total Vulnerabilities: #{@exploitation_results[:vulnerabilities].length}"
    puts "Successful Exploits: #{@exploitation_results[:successful_exploits].length}"
    puts "Risk Level: #{@report_data[:risk_assessment][:overall_risk]}"
    
    @report_data
  end
  
  def self.demonstrate_pentesting
    puts "Penetration Testing Demonstration:"
    puts "=" * 50
    
    tester = PenetrationTester.new('http://example.com')
    
    # Run full pentest
    report = tester.run_full_pentest
    
    puts "\nPenetration Test Summary:"
    puts "Target: #{report[:executive_summary][:target]}"
    puts "Overall Risk: #{report[:risk_assessment][:overall_risk]}"
    puts "Critical Findings: #{report[:risk_assessment][:critical_count]}"
    puts "High Risk Findings: #{report[:risk_assessment][:high_count]}"
    puts "Recommendations: #{report[:remediation_plan][:recommendations].length}"
    
    puts "\nPenetration Testing Features:"
    puts "- Comprehensive reconnaissance"
    puts "- Vulnerability scanning"
    puts "- Exploitation attempts"
    puts "- Post-exploitation activities"
    puts "- Detailed reporting"
    puts "- Risk assessment"
    puts "- Remediation planning"
  end
  
  private
  
  def scan_ports
    # Simulate port scanning
    [22, 80, 443, 3306, 5432]
  end
  
  def enumerate_services
    # Simulate service enumeration
    {
      22 => 'SSH',
      80 => 'HTTP',
      443 => 'HTTPS',
      3306 => 'MySQL',
      5432 => 'PostgreSQL'
    }
  end
  
  def identify_technologies
    # Simulate technology identification
    {
      web_server: 'Apache/2.4.41',
      framework: 'Ruby on Rails',
      database: 'PostgreSQL',
      os: 'Linux'
    }
  end
  
  def enumerate_directories
    # Simulate directory enumeration
    ['/admin', '/api', '/backup', '/config', '/logs']
  end
  
  def enumerate_subdomains
    # Simulate subdomain enumeration
    ['api.example.com', 'admin.example.com', 'test.example.com']
  end
  
  def analyze_ssl
    # Simulate SSL analysis
    {
      certificate_valid: true,
      protocol_version: 'TLSv1.2',
      cipher_suites: ['ECDHE-RSA-AES256-GCM-SHA384'],
      vulnerabilities: ['weak_cipher']
    }
  end
  
  def scan_vulnerabilities
    # Simulate vulnerability scanning
    [
      { type: 'sql_injection', severity: 'high', endpoint: '/login' },
      { type: 'xss', severity: 'medium', endpoint: '/search' },
      { type: 'rce', severity: 'critical', endpoint: '/upload' }
    ]
  end
  
  def scan_web_application
    # Simulate web application scanning
    [
      { type: 'missing_csrf', severity: 'medium', endpoint: '/profile' },
      { type: 'weak_password', severity: 'low', endpoint: '/register' }
    ]
  end
  
  def scan_network
    # Simulate network scanning
    [
      { type: 'open_port', severity: 'low', port: 3306 },
      { type: 'weak_cipher', severity: 'medium', service: 'SSH' }
    ]
  end
  
  def scan_configuration
    # Simulate configuration scanning
    [
      { type: 'default_config', severity: 'medium', service: 'Apache' },
      { type: 'debug_mode', severity: 'high', service: 'Application' }
    ]
  end
  
  def exploit_sql_injection(vulnerability)
    # Simulate SQL injection exploit
    {
      success: true,
      payload: "' OR '1'='1",
      result: 'Database access obtained',
      data_extracted: ['users', 'passwords']
    }
  end
  
  def exploit_xss(vulnerability)
    # Simulate XSS exploit
    {
      success: true,
      payload: '<script>alert(1)</script>',
      result: 'JavaScript execution',
      impact: 'Session theft possible'
    }
  end
  
  def exploit_rce(vulnerability)
    # Simulate RCE exploit
    {
      success: true,
      payload: 'whoami',
      result: 'Command execution',
      shell_access: true
    }
  end
  
  def exploit_file_upload(vulnerability)
    # Simulate file upload exploit
    {
      success: true,
      payload: 'webshell.php',
      result: 'Web shell uploaded',
      backdoor: true
    }
  end
  
  def attempt_privilege_escalation
    # Simulate privilege escalation
    {
      success: true,
      method: 'sudo misconfiguration',
      result: 'Root access obtained'
    }
  end
  
  def attempt_lateral_movement
    # Simulate lateral movement
    {
      success: true,
      targets: ['db.example.com', 'backup.example.com'],
      result: 'Access to additional systems'
    }
  end
  
  def attempt_data_exfiltration
    # Simulate data exfiltration
    {
      success: true,
      data_types: ['user_data', 'financial_data'],
      size: '500MB',
      method: 'encrypted tunnel'
    }
  end
  
  def attempt_persistence
    # Simulate persistence
    {
      success: true,
      methods: ['cron_job', 'systemd_service', 'backdoor_user'],
      result: 'Persistent access established'
    }
  end
  
  def cleanup_traces
    # Simulate cleanup
    {
      success: true,
      actions: ['log_cleanup', 'temp_file_removal', 'process_hide'],
      result: 'Traces cleaned'
    }
  end
  
  def generate_executive_summary
    {
      target: @target,
      test_date: Time.now.iso8601,
      overall_risk: 'HIGH',
      critical_findings: 2,
      high_findings: 3,
      medium_findings: 5,
      low_findings: 4,
      summary: 'Multiple critical vulnerabilities discovered requiring immediate attention'
    }
  end
  
  def generate_technical_findings
    {
      vulnerabilities: @exploitation_results[:vulnerabilities],
      exploits: @exploitation_results[:successful_exploits],
      post_exploitation: @post_exploitation_data
    }
  end
  
  def generate_risk_assessment
    {
      overall_risk: 'HIGH',
      critical_count: 2,
      high_count: 3,
      medium_count: 5,
      low_count: 4,
      risk_matrix: {
        'critical' => { 'impact' => 'HIGH', 'likelihood' => 'HIGH' },
        'high' => { 'impact' => 'HIGH', 'likelihood' => 'MEDIUM' },
        'medium' => { 'impact' => 'MEDIUM', 'likelihood' => 'MEDIUM' },
        'low' => { 'impact' => 'LOW', 'likelihood' => 'LOW' }
      }
    }
  end
  
  def generate_remediation_plan
    {
      immediate_actions: [
        'Patch SQL injection vulnerabilities',
        'Remove debug endpoints',
        'Implement proper access controls'
      ],
      short_term_actions: [
        'Update software versions',
        'Implement security headers',
        'Configure SSL properly'
      ],
      long_term_actions: [
        'Implement security training',
        'Regular security assessments',
        'Security monitoring implementation'
      ],
      recommendations: [
        'Implement secure coding practices',
        'Regular vulnerability scanning',
        'Penetration testing program'
      ]
    }
  end
  
  def generate_appendix
    {
      tools_used: ['Nmap', 'Burp Suite', 'SQLMap', 'Metasploit'],
      methodology: 'OWASP Testing Guide',
      limitations: 'Time constraints, scope limitations',
      assumptions: 'All systems were in production state'
    }
  end
end
```

## 🔬 Security Testing Tools

### 4. Security Testing Framework

Comprehensive testing framework:

```ruby
class SecurityTestingFramework
  def initialize
    @test_suites = {}
    @test_results = {}
    @coverage_data = {}
    @metrics = {}
  end
  
  def add_test_suite(name, test_suite)
    @test_suites[name] = test_suite
    puts "Added test suite: #{name}"
  end
  
  def run_test_suite(suite_name, options = {})
    puts "Running test suite: #{suite_name}"
    
    suite = @test_suites[suite_name]
    return { success: false, error: "Test suite not found: #{suite_name}" } unless suite
    
    results = {
      suite_name: suite_name,
      start_time: Time.now,
      tests_run: 0,
      tests_passed: 0,
      tests_failed: 0,
      tests_skipped: 0,
      vulnerabilities_found: 0,
      details: []
    }
    
    suite[:tests].each do |test|
      test_result = run_single_test(test, options)
      
      results[:tests_run] += 1
      results[:details] << test_result
      
      case test_result[:status]
      when 'passed'
        results[:tests_passed] += 1
      when 'failed'
        results[:tests_failed] += 1
        results[:vulnerabilities_found] += 1 if test_result[:vulnerability]
      when 'skipped'
        results[:tests_skipped] += 1
      end
    end
    
    results[:end_time] = Time.now
    results[:duration] = results[:end_time] - results[:start_time]
    
    @test_results[suite_name] = results
    
    puts "Test suite completed:"
    puts "  Tests run: #{results[:tests_run]}"
    puts "  Passed: #{results[:tests_passed]}"
    puts "  Failed: #{results[:tests_failed]}"
    puts "  Skipped: #{results[:tests_skipped]}"
    puts "  Vulnerabilities found: #{results[:vulnerabilities_found]}"
    puts "  Duration: #{results[:duration]}s"
    
    results
  end
  
  def run_all_tests(options = {})
    puts "Running all test suites..."
    
    all_results = {
      start_time: Time.now,
      suites: {},
      total_tests: 0,
      total_passed: 0,
      total_failed: 0,
      total_skipped: 0,
      total_vulnerabilities: 0
    }
    
    @test_suites.each do |suite_name, suite|
      results = run_test_suite(suite_name, options)
      all_results[:suites][suite_name] = results
      
      all_results[:total_tests] += results[:tests_run]
      all_results[:total_passed] += results[:tests_passed]
      all_results[:total_failed] += results[:tests_failed]
      all_results[:total_skipped] += results[:tests_skipped]
      all_results[:total_vulnerabilities] += results[:vulnerabilities_found]
    end
    
    all_results[:end_time] = Time.now
    all_results[:duration] = all_results[:end_time] - all_results[:start_time]
    
    puts "\nAll test suites completed:"
    puts "  Total tests: #{all_results[:total_tests]}"
    puts "  Passed: #{all_results[:total_passed]}"
    puts "  Failed: #{all_results[:total_failed]}"
    puts "  Skipped: #{all_results[:total_skipped]}"
    puts "  Total vulnerabilities: #{all_results[:total_vulnerabilities]}"
    puts "  Total duration: #{all_results[:duration]}s"
    
    all_results
  end
  
  def generate_coverage_report
    puts "Generating coverage report..."
    
    coverage = {
      test_coverage: calculate_test_coverage,
      vulnerability_coverage: calculate_vulnerability_coverage,
      code_coverage: calculate_code_coverage,
      recommendations: generate_coverage_recommendations
    }
    
    @coverage_data = coverage
    
    puts "Coverage Report:"
    puts "  Test coverage: #{coverage[:test_coverage]}%"
    puts "  Vulnerability coverage: #{coverage[:vulnerability_coverage]}%"
    puts "  Code coverage: #{coverage[:code_coverage]}%"
    
    coverage
  end
  
  def generate_metrics
    puts "Generating security metrics..."
    
    metrics = {
      security_score: calculate_security_score,
      risk_score: calculate_risk_score,
      compliance_score: calculate_compliance_score,
      trend_analysis: calculate_trend_analysis,
      recommendations: generate_metrics_recommendations
    }
    
    @metrics = metrics
    
    puts "Security Metrics:"
    puts "  Security score: #{metrics[:security_score]}/100"
    puts "  Risk score: #{metrics[:risk_score]}/100"
    puts "  Compliance score: #{metrics[:compliance_score]}/100"
    
    metrics
  end
  
  def generate_comprehensive_report
    puts "Generating comprehensive security report..."
    
    all_results = run_all_tests
    coverage = generate_coverage_report
    metrics = generate_metrics
    
    report = {
      generated_at: Time.now.iso8601,
      test_results: all_results,
      coverage: coverage,
      metrics: metrics,
      executive_summary: generate_executive_summary(all_results, coverage, metrics),
      detailed_findings: generate_detailed_findings(all_results),
      remediation_plan: generate_remediation_plan(all_results, metrics),
      appendix: generate_appendix
    }
    
    # Save report
    filename = "security_test_report_#{Time.now.strftime('%Y%m%d_%H%M%S')}.json"
    File.write(filename, JSON.pretty_generate(report))
    
    puts "Comprehensive security report generated: #{filename}"
    report
  end
  
  def self.create_default_suites
    framework = SecurityTestingFramework.new
    
    # Authentication test suite
    auth_suite = {
      name: 'Authentication Security',
      description: 'Tests for authentication vulnerabilities',
      tests: [
        {
          name: 'Weak Password Test',
          type: 'authentication',
          method: 'test_weak_passwords',
          severity: 'high'
        },
        {
          name: 'Default Credentials Test',
          type: 'authentication',
          method: 'test_default_credentials',
          severity: 'critical'
        },
        {
          name: 'Session Management Test',
          type: 'authentication',
          method: 'test_session_management',
          severity: 'medium'
        },
        {
          name: 'Multi-Factor Authentication Test',
          type: 'authentication',
          method: 'test_mfa',
          severity: 'medium'
        }
      ]
    }
    
    # Input validation test suite
    input_suite = {
      name: 'Input Validation',
      description: 'Tests for input validation vulnerabilities',
      tests: [
        {
          name: 'SQL Injection Test',
          type: 'injection',
          method: 'test_sql_injection',
          severity: 'critical'
        },
        {
          name: 'XSS Test',
          type: 'xss',
          method: 'test_xss',
          severity: 'high'
        },
        {
          name: 'CSRF Test',
          type: 'csrf',
          method: 'test_csrf',
          severity: 'medium'
        },
        {
          name: 'File Upload Test',
          type: 'file_upload',
          method: 'test_file_upload',
          severity: 'high'
        }
      ]
    }
    
    # Authorization test suite
    authz_suite = {
      name: 'Authorization',
      description: 'Tests for authorization vulnerabilities',
      tests: [
        {
          name: 'Privilege Escalation Test',
          type: 'authorization',
          method: 'test_privilege_escalation',
          severity: 'high'
        },
        {
          name: 'Access Control Test',
          type: 'authorization',
          method: 'test_access_control',
          severity: 'medium'
        },
        {
          name: 'IDOR Test',
          type: 'authorization',
          method: 'test_idor',
          severity: 'high'
        }
      ]
    }
    
    # Configuration test suite
    config_suite = {
      name: 'Configuration Security',
      description: 'Tests for configuration vulnerabilities',
      tests: [
        {
          name: 'Security Headers Test',
          type: 'configuration',
          method: 'test_security_headers',
          severity: 'medium'
        },
        {
          name: 'SSL/TLS Test',
          type: 'configuration',
          method: 'test_ssl_tls',
          severity: 'high'
        },
        {
          name: 'Error Handling Test',
          type: 'configuration',
          method: 'test_error_handling',
          severity: 'low'
        }
      ]
    }
    
    # Add all suites
    framework.add_test_suite('authentication', auth_suite)
    framework.add_test_suite('input_validation', input_suite)
    framework.add_test_suite('authorization', authz_suite)
    framework.add_test_suite('configuration', config_suite)
    
    framework
  end
  
  def self.demonstrate_framework
    puts "Security Testing Framework Demonstration:"
    puts "=" * 50
    
    # Create framework with default suites
    framework = create_default_suites
    
    # Run all tests
    report = framework.generate_comprehensive_report
    
    puts "\nFramework Features:"
    puts "- Multiple test suites"
    puts "- Comprehensive test coverage"
    puts "- Vulnerability detection"
    puts "- Security metrics"
    puts "- Detailed reporting"
    puts "- Remediation planning"
    puts "- Executive summary"
    
    puts "\nTest Suites Available:"
    framework.test_suites.each do |name, suite|
      puts "- #{name}: #{suite[:tests].length} tests"
    end
  end
  
  private
  
  def run_single_test(test, options = {})
    puts "  Running test: #{test[:name]}"
    
    start_time = Time.now
    
    begin
      # Simulate test execution
      result = case test[:method]
              when 'test_weak_passwords'
                test_weak_passwords
              when 'test_default_credentials'
                test_default_credentials
              when 'test_sql_injection'
                test_sql_injection
              when 'test_xss'
                test_xss
              else
                { status: 'passed', vulnerability: false, message: 'Test passed' }
              end
      
      result[:test_name] = test[:name]
      result[:test_type] = test[:type]
      result[:severity] = test[:severity]
      result[:duration] = Time.now - start_time
      
      puts "    Status: #{result[:status]}"
      puts "    Vulnerability: #{result[:vulnerability]}"
      
      result
    rescue => e
      {
        test_name: test[:name],
        status: 'error',
        error: e.message,
        duration: Time.now - start_time
      }
    end
  end
  
  def test_weak_passwords
    # Simulate weak password test
    { status: 'failed', vulnerability: true, message: 'Weak passwords accepted' }
  end
  
  def test_default_credentials
    # Simulate default credentials test
    { status: 'passed', vulnerability: false, message: 'Default credentials rejected' }
  end
  
  def test_sql_injection
    # Simulate SQL injection test
    { status: 'failed', vulnerability: true, message: 'SQL injection vulnerability found' }
  end
  
  def test_xss
    # Simulate XSS test
    { status: 'failed', vulnerability: true, message: 'XSS vulnerability found' }
  end
  
  def calculate_test_coverage
    # Simulate test coverage calculation
    85
  end
  
  def calculate_vulnerability_coverage
    # Simulate vulnerability coverage calculation
    75
  end
  
  def calculate_code_coverage
    # Simulate code coverage calculation
    70
  end
  
  def calculate_security_score
    # Simulate security score calculation
    72
  end
  
  def calculate_risk_score
    # Simulate risk score calculation
    65
  end
  
  def calculate_compliance_score
    # Simulate compliance score calculation
    80
  end
  
  def calculate_trend_analysis
    # Simulate trend analysis
    {
      security_score_trend: 'improving',
      vulnerability_trend: 'decreasing',
      compliance_trend: 'stable'
    }
  end
  
  def generate_coverage_recommendations
    ['Increase test coverage for edge cases', 'Add more integration tests', 'Improve error handling tests']
  end
  
  def generate_metrics_recommendations
    ['Improve security posture', 'Address high-risk vulnerabilities', 'Implement security best practices']
  end
  
  def generate_executive_summary(results, coverage, metrics)
    {
      overall_status: 'NEEDS_ATTENTION',
      security_score: metrics[:security_score],
      critical_vulnerabilities: results[:total_vulnerabilities],
      recommendations: 'Immediate action required for critical vulnerabilities'
    }
  end
  
  def generate_detailed_findings(results)
    results[:suites].values.flat_map { |suite| suite[:details] }
  end
  
  def generate_remediation_plan(results, metrics)
    {
      immediate: ['Patch critical vulnerabilities', 'Implement security headers'],
      short_term: ['Update dependencies', 'Improve authentication'],
      long_term: ['Security training', 'Regular testing program']
    }
  end
  
  def generate_appendix
    {
      tools_used: ['Custom test framework', 'OWASP ZAP', 'Burp Suite'],
      methodology: 'OWASP Testing Guide',
      limitations: 'Time constraints, scope limitations'
    }
  end
end
```

## 🎯 Security Testing Best Practices

### 5. Security Testing Guidelines

Best practices and methodologies:

```ruby
class SecurityTestingGuidelines
  def self.testing_checklist
    puts "Security Testing Checklist:"
    puts "=" * 50
    
    checklist = [
      {
        category: 'Planning',
        items: [
          'Define test scope and objectives',
          'Identify threat models',
          'Select appropriate testing tools',
          'Create test plan and schedule',
          'Establish success criteria'
        ]
      },
      {
        category: 'Preparation',
        items: [
          'Set up testing environment',
          'Create test accounts and data',
          'Configure monitoring and logging',
          'Prepare test tools and scripts',
          'Document test procedures'
        ]
      },
      {
        category: 'Execution',
        items: [
          'Perform reconnaissance',
          'Conduct vulnerability scanning',
          'Execute penetration tests',
          'Document all findings',
          'Validate vulnerabilities'
        ]
      },
      {
        category: 'Reporting',
        items: [
          'Generate detailed reports',
          'Create executive summary',
          'Provide remediation recommendations',
          'Assign risk levels',
          'Schedule follow-up testing'
        ]
      },
      {
        category: 'Follow-up',
        items: [
          'Verify remediation',
          'Retest fixed vulnerabilities',
          'Update documentation',
          'Improve testing processes',
          'Conduct lessons learned'
        ]
      }
    ]
    
    checklist.each do |category|
      puts "#{category[:category]}:"
      category[:items].each_with_index do |item, i|
        puts "  #{i + 1}. #{item}"
      end
      puts
    end
  end
  
  def self.security_testing_phases
    puts "\nSecurity Testing Phases:"
    puts "=" * 50
    
    phases = [
      {
        phase: '1. Planning and Scoping',
        description: 'Define testing objectives and scope',
        activities: [
          'Define test boundaries',
          'Identify assets to protect',
          'Determine testing methods',
          'Establish success criteria',
          'Create test schedule'
        ],
        deliverables: ['Test plan', 'Scope document', 'Risk assessment']
      },
      {
        phase: '2. Reconnaissance',
        description: 'Gather information about target',
        activities: [
          'Passive information gathering',
          'Network scanning',
          'Service enumeration',
          'Technology identification',
          'Vulnerability mapping'
        ],
        deliverables: ['Network map', 'Service inventory', 'Technology stack']
      },
      {
        phase: '3. Vulnerability Assessment',
        description: 'Identify security vulnerabilities',
        activities: [
          'Automated scanning',
          'Manual testing',
          'Configuration review',
          'Code analysis',
          'Documentation review'
        ],
        deliverables: ['Vulnerability list', 'Risk assessment', 'Priority matrix']
      },
      {
        phase: '4. Exploitation',
        description: 'Test vulnerability exploitation',
        activities: [
          'Attempt exploits',
          'Privilege escalation',
          'Lateral movement',
          'Data access',
          'Persistence testing'
        ],
        deliverables: ['Exploitation results', 'Access gained', 'Impact assessment']
      },
      {
        phase: '5. Post-Exploitation',
        description: 'Assess impact and cleanup',
        activities: [
          'Impact assessment',
          'Data exfiltration test',
          'Persistence testing',
          'Cleanup procedures',
          'Documentation'
        ],
        deliverables: ['Impact report', 'Cleanup procedures', 'Recommendations']
      },
      {
        phase: '6. Reporting',
        description: 'Document findings and recommendations',
        activities: [
          'Generate reports',
          'Create executive summary',
          'Provide remediation plan',
          'Assign priorities',
          'Schedule follow-up'
        ],
        deliverables: ['Technical report', 'Executive summary', 'Remediation plan']
      }
    ]
    
    phases.each do |phase|
      puts "#{phase[:phase]}: #{phase[:description]}"
      puts "  Activities: #{phase[:activities].join(', ')}"
      puts "  Deliverables: #{phase[:deliverables].join(', ')}"
      puts
    end
  end
  
  def self.security_testing_tools
    puts "\nSecurity Testing Tools:"
    puts "=" * 50
    
    tools = [
      {
        category: 'Web Application Testing',
        tools: [
          { name: 'OWASP ZAP', type: 'DAST', description: 'Free web application security scanner' },
          { name: 'Burp Suite', type: 'DAST', description: 'Professional web application security testing' },
          { name: 'Nikto', type: 'DAST', description: 'Web server scanner' },
          { name: 'SQLMap', type: 'DAST', description: 'SQL injection testing tool' }
        ]
      },
      {
        category: 'Network Security Testing',
        tools: [
          { name: 'Nmap', type: 'Port Scanner', description: 'Network discovery and security auditing' },
          { name: 'Metasploit', type: 'Exploitation', description: 'Penetration testing framework' },
          { name: 'Nessus', type: 'Vulnerability Scanner', description: 'Network vulnerability scanner' },
          { name: 'OpenVAS', type: 'Vulnerability Scanner', description: 'Open source vulnerability scanner' }
        ]
      },
      {
        category: 'Static Analysis',
        tools: [
          { name: 'SonarQube', type: 'SAST', description: 'Code quality and security analysis' },
          { name: 'Checkmarx', type: 'SAST', description: 'Static application security testing' },
          { name: 'Fortify', type: 'SAST', description: 'Static analysis security testing' },
          { name: 'CodeQL', type: 'SAST', description: 'Code analysis platform' }
        ]
      },
      {
        category: 'Mobile Security',
        tools: [
          { name: 'MobSF', type: 'Mobile Analysis', description: 'Mobile security framework' },
          { name: 'Frida', type: 'Dynamic Analysis', description: 'Dynamic instrumentation toolkit' },
          { name: 'Drozer', type: 'Security Assessment', description: 'Android security assessment' }
        ]
      },
      {
        category: 'API Security',
        tools: [
          { name: 'Postman', type: 'API Testing', description: 'API development and testing' },
          { name: 'OWASP API Security Top 10', type: 'Guidelines', description: 'API security guidelines' },
          { name: 'Insomnia', type: 'API Testing', description: 'REST client for API testing' }
        ]
      }
    ]
    
    tools.each do |category|
      puts "#{category[:category]}:"
      category[:tools].each do |tool|
        puts "  #{tool[:name]} (#{tool[:type]}): #{tool[:description]}"
      end
      puts
    end
  end
  
  def self.security_testing_metrics
    puts "\nSecurity Testing Metrics:"
    puts "=" * 50
    
    metrics = [
      {
        metric: 'Vulnerability Density',
        description: 'Number of vulnerabilities per line of code',
        calculation: 'Total vulnerabilities / Lines of code',
        target: '< 1 vulnerability per 1000 lines',
        importance: 'Measures code quality'
      },
      {
        metric: 'Mean Time to Remediate',
        description: 'Average time to fix vulnerabilities',
        calculation: 'Total remediation time / Number of vulnerabilities',
        target: '< 30 days for critical vulnerabilities',
        importance: 'Measures response efficiency'
      },
      {
        metric: 'Security Test Coverage',
        description: 'Percentage of security tests covering code',
        calculation: 'Security tests / Total tests * 100',
        target: '> 80% coverage',
        importance: 'Measures testing completeness'
      },
      {
        metric: 'Vulnerability Recurrence Rate',
        description: 'Percentage of vulnerabilities that reoccur',
        calculation: 'Recurring vulnerabilities / Total vulnerabilities * 100',
        target: '< 5% recurrence',
        importance: 'Measures remediation effectiveness'
      },
      {
        metric: 'Security Score',
        description: 'Overall security posture score',
        calculation: 'Weighted sum of security metrics',
        target: '> 80/100',
        importance: 'Measures overall security'
      }
    ]
    
    metrics.each do |metric|
      puts "#{metric[:metric]}:"
      puts "  Description: #{metric[:description]}"
      puts "  Calculation: #{metric[:calculation]}"
      puts "  Target: #{metric[:target]}"
      puts "  Importance: #{metric[:importance]}"
      puts
    end
  end
  
  def self.demonstrate_guidelines
    testing_checklist
    security_testing_phases
    security_testing_tools
    security_testing_metrics
    
    puts "\nSecurity Testing Best Practices:"
    puts "- Regular and systematic testing"
    puts "- Combine automated and manual testing"
    puts "- Use multiple testing tools"
    puts "- Document all findings"
    puts "- Prioritize vulnerabilities by risk"
    puts "- Test in production-like environment"
    puts "- Include security in CI/CD pipeline"
    puts "- Regular security training"
    puts "- Keep tools updated"
    puts "- Follow industry standards (OWASP, NIST)"
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Vulnerability Scanning**: Implement basic scanner
2. **Security Testing**: Create test framework
3. **Penetration Testing**: Build pentest tools
4. **Security Reporting**: Generate security reports

### Intermediate Exercises

1. **Advanced Scanning**: Multi-vector vulnerability detection
2. **Automated Testing**: CI/CD security integration
3. **Security Metrics**: Comprehensive metrics system
4. **Testing Framework**: Complete testing solution

### Advanced Exercises

1. **Security Platform**: Enterprise security testing
2. **AI-Powered Testing**: Machine learning for testing
3. **Real-time Testing**: Continuous security monitoring
4. **Compliance Framework**: Regulatory compliance testing

---

## 🎯 Summary

Security Testing in Ruby provides:

- **Testing Fundamentals** - Core concepts and methodologies
- **Vulnerability Scanning** - Automated vulnerability detection
- **Penetration Testing** - Comprehensive security assessment
- **Security Testing Framework** - Structured testing approach
- **Testing Tools** - Security testing utilities
- **Best Practices** - Industry standards and guidelines

Master these security testing techniques for robust Ruby applications!
