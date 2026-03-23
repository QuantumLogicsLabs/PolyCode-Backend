# Web Security in Ruby
# Comprehensive guide to web application security implementations

## 🌐 Web Security Fundamentals

### 1. Web Security Concepts

Core web application security principles:

```ruby
class WebSecurityFundamentals
  def self.explain_web_security_concepts
    puts "Web Security Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "OWASP Top 10",
        description: "Most critical web application security risks",
        categories: [
          "Injection flaws",
          "Broken authentication",
          "Sensitive data exposure",
          "XML external entities",
          "Broken access control",
          "Security misconfiguration",
          "Cross-site scripting",
          "Insecure deserialization",
          "Using components with known vulnerabilities",
          "Insufficient logging and monitoring"
        ],
        importance: "Industry standard for web security"
      },
      {
        concept: "Defense in Depth",
        description: "Multiple layers of security controls",
        layers: ["Input validation", "Output encoding", "Authentication", "Authorization", "Logging"],
        principle: "Multiple security layers reduce risk"
      },
      {
        concept: "Secure by Default",
        description: "Secure configurations by default",
        implementation: ["Secure defaults", "Minimal privileges", "Secure headers"],
        benefits: ["Reduced attack surface", "Better security posture"]
      },
      {
        concept: "Least Privilege",
        description: "Minimum necessary permissions for users",
        implementation: ["Role-based access", "Permission audits", "Regular reviews"],
        benefits: ["Limited damage", "Reduced risk"]
      },
      {
        concept: "Input Validation",
        description: "Validate all user input",
        types: ["Whitelisting", "Blacklisting", "Length validation", "Format validation"],
        importance: "Prevents injection attacks"
      },
      {
        concept: "Output Encoding",
        description: "Encode output for safe display",
        contexts: ["HTML", "JavaScript", "URL", "CSS", "SQL"],
        purpose: "Prevents XSS attacks"
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Categories: #{concept[:categories].join(', ')}" if concept[:categories]
      puts "  Layers: #{concept[:layers].join(', ')}" if concept[:layers]
      puts "  Principle: #{concept[:principle]}" if concept[:principle]
      puts "  Implementation: #{concept[:implementation].join(', ')}" if concept[:implementation]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Contexts: #{concept[:contexts].join(', ')}" if concept[:contexts]
      puts "  Purpose: #{concept[:purpose]}" if concept[:purpose]
      puts "  Importance: #{concept[:importance]}" if concept[:importance]
      puts
    end
  end
  
  def self.web_attack_vectors
    puts "\nWeb Attack Vectors:"
    puts "=" * 50
    
    attacks = [
      {
        attack: "SQL Injection",
        description: "Inject malicious SQL into database queries",
        impact: ["Data breach", "Data manipulation", "System compromise"],
        prevention: ["Parameterized queries", "Input validation", "ORM usage"],
        example: "' OR '1'='1"
      },
      {
        attack: "Cross-Site Scripting (XSS)",
        description: "Inject malicious scripts into web pages",
        impact: ["Session theft", "Data theft", "User manipulation"],
        prevention: ["Output encoding", "Content Security Policy", "Input sanitization"],
        example: "<script>alert('XSS')</script>"
      },
      {
        attack: "Cross-Site Request Forgery (CSRF)",
        description: "Trick user into performing unwanted actions",
        impact: ["Unauthorized actions", "Data manipulation", "Account takeover"],
        prevention: ["CSRF tokens", "SameSite cookies", "Origin validation"],
        example: "Hidden form submission"
      },
      {
        attack: "Broken Authentication",
        description: "Weak or broken authentication mechanisms",
        impact: ["Unauthorized access", "Account takeover", "Privilege escalation"],
        prevention: ["Strong passwords", "MFA", "Session management"],
        example: "Default credentials"
      },
      {
        attack: "Sensitive Data Exposure",
        description: "Exposure of sensitive information",
        impact: ["Data breach", "Privacy violation", "Compliance issues"],
        prevention: ["Encryption", "Data masking", "Secure storage"],
        example: "Plain text passwords"
      },
      {
        attack: "XML External Entities (XXE)",
        description: "Malicious XML processing",
        impact: ["Data disclosure", "SSRF", "Denial of service"],
        prevention: ["Disable XXE", "Input validation", "Secure parsers"],
        example: "Malicious DTD"
      }
    ]
    
    attacks.each do |attack|
      puts "#{attack[:attack]}:"
      puts "  Description: #{attack[:description]}"
      puts "  Impact: #{attack[:impact].join(', ')}"
      puts "  Prevention: #{attack[:prevention].join(', ')}"
      puts "  Example: #{attack[:example]}"
      puts
    end
  end
  
  def self.security_headers
    puts "\nHTTP Security Headers:"
    puts "=" * 50
    
    headers = [
      {
        header: "Content-Security-Policy",
        purpose: "Prevent XSS and data injection",
        example: "default-src 'self'; script-src 'self' 'unsafe-inline'",
        importance: "Critical for XSS prevention"
      },
      {
        header: "X-Frame-Options",
        purpose: "Prevent clickjacking attacks",
        example: "DENY",
        alternatives: ["SAMEORIGIN", "ALLOW-FROM"]
      },
      {
        header: "X-Content-Type-Options",
        purpose: "Prevent MIME-type sniffing",
        example: "nosniff",
        importance: "Prevents content-type attacks"
      },
      {
        header: "Strict-Transport-Security",
        purpose: "Enforce HTTPS connections",
        example: "max-age=31536000; includeSubDomains",
        importance: "Critical for HTTPS enforcement"
      },
      {
        header: "X-XSS-Protection",
        purpose: "Enable XSS filtering in browsers",
        example: "1; mode=block",
        alternatives: ["0", "1"]
      },
      {
        header: "Referrer-Policy",
        purpose: "Control referrer information",
        example: "strict-origin-when-cross-origin",
        importance: "Privacy and security"
      }
    ]
    
    headers.each do |header|
      puts "#{header[:header]}:"
      puts "  Purpose: #{header[:purpose]}"
      puts "  Example: #{header[:example]}"
      puts "  Importance: #{header[:importance]}" if header[:importance]
      puts "  Alternatives: #{header[:alternatives].join(', ')}" if header[:alternatives]
      puts
    end
  end
  
  # Run web security fundamentals
  explain_web_security_concepts
  web_attack_vectors
  security_headers
end
```

### 2. Input Validation and Sanitization

Secure input handling:

```ruby
class InputValidator
  def self.validate_email(email)
    # RFC 5322 compliant email validation
    email_regex = /\A
      [a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+
      (?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*
      |
      "(?:[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*)"
      )
      @
      (?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?
        (?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*
      |
      \[([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})\])
      \z/x
    
    email.match?(email_regex)
  end
  
  def self.validate_phone(phone, country_code = nil)
    # Phone validation with country code support
    phone_regex = case country_code
                   when 'US'
                     /\A\+1[ -.]?\(?:(?:\(?\d{3}\)?)[ -.]?\d{3}[ -.]?\d{4}\z/
                   when 'UK'
                     /\A\+44[ -.]?\d{4}[ -.]?\d{6}\z/
                   when 'IN'
                     /\A\+91[ -.]?\d{5}[ -.]?\d{5}\z/
                   else
                     /\A\+[1-9]\d{1,14}[ -.]?\d{1,14}\z/
                   end
    
    phone.gsub(/[\s-]/, '').match?(phone_regex)
  end
  
  def self.validate_url(url)
    begin
      uri = URI.parse(url)
      uri.is_a?(URI::HTTP) || uri.is_a?(URI::HTTPS)
    rescue URI::InvalidURIError
      false
    end
  
  def self.validate_number(input, options = {})
    min = options[:min] || 0
    max = options[:max] || Float::INFINITY
    integer_only = options[:integer_only] || false
    positive_only = options[:positive_only] || false
    
    return false unless input.is_a?(String)
    return false if integer_only && input.include?('.')
    
    begin
      num = positive_only ? input.to_f : input.to_i
      num >= min && num <= max
    rescue ArgumentError
      false
    end
  end
  
  def self.validate_string(input, options = {})
    min_length = options[:min_length] || 1
    max_length = options[:max_length] || 255
    allowed_chars = options[:allowed_chars]
    forbidden_chars = options[:forbidden_chars]
    pattern = options[:pattern]
    
    return false unless input.is_a?(String)
    return false if input.length < min_length
    return false if input.length > max_length
    
    # Character validation
    if allowed_chars
      return false unless input.chars.all? { |char| allowed_chars.include?(char) }
    end
    
    if forbidden_chars
      return false if input.chars.any? { |char| forbidden_chars.include?(char) }
    end
    
    # Pattern validation
    if pattern
      return false unless input.match?(pattern)
    end
    
    true
  end
  
  def self.validate_date(date_string, format = nil)
    case format
    when 'ISO8601'
      begin
        Date.iso8601(date_string)
        true
      rescue ArgumentError
        false
      end
    when 'US'
      begin
        Date.strptime(date_string, '%m/%d/%Y')
        true
      rescue ArgumentError
        false
      end
    else
      begin
        Date.parse(date_string)
        true
      rescue ArgumentError
        false
      end
    end
  end
  
  def self.sanitize_html(input)
    # Basic HTML sanitization
    return '' if input.nil?
    
    # Remove potentially dangerous tags
    dangerous_tags = %w[script iframe object embed form input textarea button select option
    dangerous_attrs = %w[onload onclick onerror onmouseover onmouseout onchange]
    
    sanitized = input.dup
    
    # Remove dangerous tags
    dangerous_tags.each do |tag|
      sanitized.gsub!(/<#{tag}[^>]*>.*?<\/#{tag}>/im, '')
      sanitized.gsub!(/<#{tag}[^>]*\/>/im, '')
    end
    
    # Remove dangerous attributes
    dangerous_attrs.each do |attr|
      sanitized.gsub!(/\s#{attr}=["'][^"']*["']/im, '')
    end
    
    sanitized
  end
  
  def self.sanitize_sql(input)
    return '' if input.nil?
    
    # Remove SQL injection attempts
    dangerous_patterns = [
      /('|( -- |;|\/\*| \*\/)/,
      /\b(UNION|SELECT|INSERT|UPDATE|DELETE|DROP|ALTER|CREATE|EXEC|EXECUTE)\b/i,
      /\b(OR|AND)\s+\w+\s*=\s*\w+/i,
      /\b(1=1|1 = 1|1=1)\b/i
    ]
    
    sanitized = input.dup
    
    dangerous_patterns.each do |pattern|
      sanitized.gsub!(pattern, '')
    end
    
    sanitized
  end
  
  def self.sanitize_filename(filename)
    return '' if filename.nil?
    
    # Remove dangerous characters
    sanitized = filename.gsub(/[<>:"|?*\/\\]/, '_')
    
    # Remove control characters
    sanitized.gsub!(/[\x00-\x1f\x7f]/, '_')
    
    # Remove leading/trailing dots and spaces
    sanitized.gsub!(/^[\.\s]+|[\.\s]+$/, '')
    
    # Limit length
    sanitized[0...255]
  end
  
  def self.validate_upload(file, options = {})
    return false unless file.respond_to?(:original_filename)
    
    filename = file.original_filename
    content_type = file.content_type
    size = file.size
    
    # Validate filename
    return false unless sanitize_filename(filename).length > 0
    
    # Validate content type
    allowed_types = options[:allowed_types] || %w[image/jpeg image/png image/gif text/plain application/pdf]
    return false unless allowed_types.include?(content_type)
    
    # Validate file size
    max_size = options[:max_size] || 10.megabytes
    return false unless size <= max_size
    
    # Validate file content (optional)
    if options[:validate_content] && content_type.start_with?('image/')
      return false unless is_valid_image?(file.path)
    end
    
    true
  end
  
  def self.validate_json(input)
    begin
      JSON.parse(input)
      true
    rescue JSON::ParserError
      false
    end
  
  def self.validate_xml(input)
    begin
      Nokogiri::XML(input)
      true
    rescue Nokogiri::XML::SyntaxError
      false
    end
  
  def self.demonstrate_validation
    puts "Input Validation Demonstration:"
    puts "=" * 50
    
    # Email validation
    emails = ['test@example.com', 'invalid-email', 'user@domain', 'user@.']
    puts "\nEmail Validation:"
    emails.each do |email|
      result = validate_email(email)
      puts "  #{email}: #{result ? 'Valid' : 'Invalid'}"
    end
    
    # Phone validation
    phones = ['+1234567890', '123-456-7890', '(123) 456-7890', '1234567890']
    puts "\nPhone Validation:"
    phones.each do |phone|
      result = validate_phone(phone)
      puts "  #{phone}: #{result ? 'Valid' : 'Invalid'}"
    end
    
    # URL validation
    urls = ['https://example.com', 'http://example.com', 'ftp://example.com', 'not-a-url']
    puts "\nURL Validation:"
    urls.each do |url|
      result = validate_url(url)
      puts "  #{url}: #{result ? 'Valid' : 'Invalid'}"
    end
    
    # Number validation
    numbers = ['123', '-456', '12.34', 'abc', '0']
    puts "\nNumber Validation:"
    numbers.each do |number|
      result = validate_number(number)
      puts "  #{number}: #{result ? 'Valid' : 'Invalid'}"
    end
    
    # String validation
    strings = ['Hello', '', '12345', 'Hello World!', 'Special@#$%']
    puts "\nString Validation:"
    strings.each do |string|
      result = validate_string(string, min_length: 1, max_length: 10)
      puts "  '#{string}': #{result ? 'Valid' : 'Invalid'}"
    end
    
    puts "\nValidation Features:"
    puts "- Email validation (RFC 5322)"
    puts "- Phone validation (international)"
    puts "- URL validation"
    puts "- Number validation"
    puts "- String validation"
    puts "- Date validation"
    puts "- File upload validation"
  end
  
  def self.demonstrate_sanitization
    puts "\nInput Sanitization Demonstration:"
    puts "=" * 50
    
    # HTML sanitization
    html_inputs = [
      '<p>Safe content</p>',
      '<script>alert("XSS")</script>',
      '<img src="x" onerror="alert(1)">',
      '<iframe src="malicious.com"></iframe>'
    ]
    
    puts "HTML Sanitization:"
    html_inputs.each do |html|
      sanitized = sanitize_html(html)
      puts "  Original: #{html}"
      puts "  Sanitized: #{sanitized}"
    end
    
    # SQL sanitization
    sql_inputs = [
      "SELECT * FROM users WHERE id = 1",
      "SELECT * FROM users WHERE id = 1 OR '1'='1",
      "'; DROP TABLE users; --",
      "UNION SELECT * FROM passwords"
    ]
    
    puts "\nSQL Sanitization:"
    sql_inputs.each do |sql|
      sanitized = sanitize_sql(sql)
      puts "  Original: #{sql}"
      puts "  Sanitized: #{sanitized}"
    end
    
    # Filename sanitization
    filenames = [
      'safe_file.txt',
      'dangerous_file.exe',
      'file<with>special.chars',
      'file with spaces.txt'
    ]
    
    puts "\nFilename Sanitization:"
    filenames.each do |filename|
      sanitized = sanitize_filename(filename)
      puts "  Original: #{filename}"
      puts "  Sanitized: #{sanitized}"
    end
    
    puts "\nSanitization Features:"
    puts "- HTML tag removal"
    puts "- SQL injection prevention"
    puts "- Filename sanitization"
    puts "- Character escaping"
    puts "- Content validation"
  end
  
  private
  
  def self.is_valid_image?(file_path)
    begin
      image = MiniMagick::Image.read(file_path)
      %w[jpeg png gif].include?(image.format.downcase)
    rescue
      false
    end
  end
end

class SecureFormBuilder
  def self.build_secure_form(form_data, options = {})
    puts "Building Secure Form..."
    
    # Validate form data
    validation_errors = validate_form_data(form_data, options)
    
    if validation_errors.any?
      puts "Validation errors:"
      validation_errors.each { |field, error| puts "  #{field}: #{error}" }
      return { success: false, errors: validation_errors }
    end
    
    # Sanitize form data
    sanitized_data = sanitize_form_data(form_data)
    
    # Build secure form
    form = {
      action: options[:action] || '/submit',
      method: options[:method] || 'POST',
      fields: [],
      csrf_token: generate_csrf_token,
      data: sanitized_data
    }
    
    # Add form fields
    sanitized_data.each do |field, value|
      form[:fields] << {
        name: field,
        value: value,
        type: determine_field_type(field, value),
        required: options[:required]&.include?(field),
        validation: get_field_validation(field, options)
      }
    end
    
    puts "Secure form built successfully"
    puts "Fields: #{form[:fields].length}"
    puts "CSRF token: #{form[:csrf_token][0...16]}..."
    
    form
  end
  
  def self.validate_form_data(form_data, options = {})
    errors = {}
    
    form_data.each do |field, value|
      # Required field validation
      if options[:required]&.include?(field) && (value.nil? || value.to_s.strip.empty?)
        errors[field] = "#{field} is required"
      end
      
      # Email validation
      if field.to_s.include?('email') && !value.to_s.empty?
        errors[field] = "Invalid email format" unless InputValidator.validate_email(value.to_s)
      end
      
      # Phone validation
      if field.to_s.include?('phone') && !value.to_s.empty?
        errors[field] = "Invalid phone format" unless InputValidator.validate_phone(value.to_s)
      end
      
      # Number validation
      if field.to_s.include?('age') || field.to_s.include?('quantity')
        errors[field] = "Invalid number" unless InputValidator.validate_number(value.to_s, positive_only: true)
      end
    end
    
    errors
  end
  
  def self.sanitize_form_data(form_data)
    sanitized = {}
    
    form_data.each do |field, value|
      sanitized[field] = case field.to_s
                     when 'email'
                       value.to_s.downcase.strip
                     when 'name', 'title'
                       sanitize_html(value.to_s)
                     when 'description', 'message'
                       sanitize_html(value.to_s)
                     when 'phone'
                       value.to_s.gsub(/[^\d+]/, '')
                     else
                       value.to_s.strip
                     end
    end
    
    sanitized
  end
  
  def self.determine_field_type(field, value)
    case field.to_s
    when 'password'
      'password'
    when 'email'
      'email'
    when 'phone'
      'tel'
    when 'url'
      'url'
    when 'date'
      'date'
    when 'number'
      'number'
    when 'file'
      'file'
    else
      'text'
    end
  end
  
  def self.get_field_validation(field, options)
    validations = {}
    
    case field.to_s
    when 'email'
      validations[:pattern] = 'email'
    when 'phone'
      validations[:pattern] = 'phone'
    when 'url'
      validations[:type] = 'url'
    when 'age', 'quantity'
      validations[:min] = 0
      validations[:max] = options[:max_age] || 120
    when 'name', 'title'
      validations[:min_length] = 2
      validations[:max_length] = 50
    when 'description', 'message'
      validations[:max_length] = 1000
    end
    
    validations
  end
  
  def self.generate_csrf_token
    SecureRandom.hex(32)
  end
  
  def self.demonstrate_form_builder
    puts "\nSecure Form Builder Demonstration:"
    puts "=" * 50
    
    # Sample form data
    form_data = {
      name: 'John Doe',
      email: 'john@example.com',
      phone: '+1234567890',
      age: '25',
      message: 'This is a test message with <script>alert("test")</script>'
    }
    
    options = {
      action: '/submit',
      method: 'POST',
      required: ['name', 'email'],
      max_age: 120
    }
    
    # Build secure form
    form = build_secure_form(form_data, options)
    
    puts "\nForm Details:"
    puts "Action: #{form[:action]}"
    puts "Method: #{form[:method]}"
    puts "CSRF Token: #{form[:csrf_token]}"
    
    puts "\nForm Fields:"
    form[:fields].each do |field|
      puts "  #{field[:name]} (#{field[:type]}): #{field[:value]}"
      puts "    Required: #{field[:required]}"
      puts "    Validation: #{field[:validation]}"
    end
    
    puts "\nSecure Form Features:"
    puts "- Input validation"
    puts "- Data sanitization"
    puts "- CSRF protection"
    puts "- Field type detection"
    puts "- Custom validation rules"
  end
end
```

## 🛡️ Web Application Security

### 3. Authentication and Authorization

Secure authentication implementation:

```ruby
class SecureAuthentication
  require 'bcrypt'
  require 'securerandom'
  
  def self.hash_password(password, cost = 12)
    BCrypt::Password.create(password, cost: cost)
  end
  
  def self.verify_password(password, hashed_password)
    hashed_password == password
  end
  
  def self.generate_token(length = 32)
    SecureRandom.hex(length)
  end
  
  def self.generate_session_id
    generate_token(64)
  end
  
  def self.hash_token(token)
    Digest::SHA256.hexdigest(token)
  end
  
  def self.verify_token(token, hashed_token)
    hash_token(token) == hashed_token
  end
  
  def self.generate_otp_secret
    ROTP::Base32.random_base32
  end
  
  def self.generate_otp_qr_code(user_email, secret)
    issuer = "MyApp"
    otpauth_url = "otpauth://totp/#{user_email}?secret=#{secret}&issuer=#{issuer}"
    
    # In practice, you'd use a QR code generation library
    puts "OTP QR Code URL: #{otpauth_url}"
    otpauth_url
  end
  
  def self.verify_otp(secret, code)
    totp = ROTP::TOTP.new(secret)
    totp.verify(code)
  end
  
  def self.generate_api_key
    {
      key: generate_token(32),
      secret: generate_token(64),
      created_at: Time.now,
      expires_at: Time.now + 1.year
    }
  end
  
  def self.verify_api_key(api_key, request)
    # Verify API key exists and is not expired
    return false unless api_key[:expires_at] > Time.now
    
    # Verify request signature
    signature = request.headers['X-Signature']
    expected_signature = generate_signature(request, api_key[:secret])
    
    signature == expected_signature
  end
  
  def self.generate_signature(request, secret)
    # Generate HMAC signature
    string_to_sign = "#{request.method}#{request.url}#{request.body}"
    OpenSSL::HMAC.hexdigest('sha256', secret, string_to_sign)
  end
  
  def self.create_user_session(user_id, options = {})
    session_id = generate_session_id
    session_data = {
      user_id: user_id,
      created_at: Time.now,
      last_activity: Time.now,
      ip_address: options[:ip_address],
      user_agent: options[:user_agent],
      expires_at: Time.now + (options[:expires_in] || 3600)
    }
    
    {
      session_id: session_id,
      session_data: session_data,
      expires_at: session_data[:expires_at]
    }
  end
  
  def self.validate_session(session_id, session_data, options = {})
    return false unless session_data
    return false unless session_data[:expires_at] > Time.now
    
    # Check session age
    max_age = options[:max_age] || 3600
    return false if Time.now - session_data[:created_at] > max_age
    
    # Check inactivity timeout
    inactivity_timeout = options[:inactivity_timeout] || 1800
    return false if Time.now - session_data[:last_activity] > inactivity_timeout
    
    # Check IP address consistency
    if options[:ip_address] && session_data[:ip_address] != options[:ip_address]
      return false
    end
    
    true
  end
  
  def self.create_permission_system
    permissions = {
      'admin' => %w[read write delete manage_users manage_settings],
      'moderator' => %w[read write delete moderate],
      'user' => %w[read write],
      'guest' => %w[read]
    }
    
    roles = {
      'super_admin' => ['admin'],
      'admin' => ['admin'],
      'moderator' => ['moderator'],
      'user' => ['user'],
      'guest' => ['guest']
    }
    
    {
      permissions: permissions,
      roles: roles
    }
  end
  
  def self.check_permission(user_role, permission, resource = nil)
    permission_system = create_permission_system
    
    user_permissions = permission_system[:roles][user_role]&.map { |role| permission_system[:permissions][role] }.flatten || []
    
    user_permissions.include?(permission)
  end
  
  def self.implement_rate_limiting(identifier, max_requests = 100, time_window = 3600)
    key = "rate_limit:#{identifier}:#{Time.now.to_i / time_window}"
    
    current_count = $redis.get(key).to_i
    
    if current_count >= max_requests
      false
    else
      $redis.incr(key)
      $redis.expire(key, time_window)
      true
    end
  end
  
  def self.implement_account_lockout(user_id, max_attempts = 5, lockout_time = 900)
    key = "login_attempts:#{user_id}"
    
    attempts = $redis.get(key).to_i
    
    if attempts >= max_attempts
      $redis.setex(key, 'locked', lockout_time)
      return { locked: true, lockout_time: lockout_time }
    end
    
    { locked: false, attempts: attempts }
  end
  
  def self.check_account_lockout(user_id)
    key = "login_attempts:#{user_id}"
    locked = $redis.get(key) == 'locked'
    
    if locked
      ttl = $redis.ttl(key)
      { locked: true, time_remaining: ttl }
    else
      { locked: false, time_remaining: 0 }
    end
  end
  
  def self.reset_login_attempts(user_id)
    key = "login_attempts:#{user_id}"
    $redis.del(key)
  end
  
  def self.demonstrate_authentication
    puts "Secure Authentication Demonstration:"
    puts "=" * 50
    
    # Password hashing
    puts "Password Hashing:"
    password = "SecurePassword123!"
    hashed_password = hash_password(password)
    puts "Original: #{password}"
    puts "Hashed: #{hashed_password[0...32]}..."
    
    # Password verification
    is_valid = verify_password(password, hashed_password)
    puts "Verification: #{is_valid ? 'SUCCESS' : 'FAILED'}"
    
    # Token generation
    puts "\nToken Generation:"
    token = generate_token
    hashed_token = hash_token(token)
    puts "Token: #{token[0...16]}..."
    puts "Hashed: #{hashed_token[0...16]}..."
    
    # Token verification
    is_valid_token = verify_token(token, hashed_token)
    puts "Token verification: #{is_valid_token ? 'SUCCESS' : 'FAILED'}"
    
    # OTP generation
    puts "\nOTP Generation:"
    secret = generate_otp_secret
    qr_code = generate_otp_qr_code('user@example.com', secret)
    puts "Secret: #{secret}"
    puts "QR Code URL: #{qr_code}"
    
    # OTP verification
    puts "\nOTP Verification:"
    otp_code = ROTP::TOTP.new(secret).now
    puts "Current OTP: #{otp_code}"
    is_valid_otp = verify_otp(secret, otp_code)
    puts "OTP verification: #{is_valid_otp ? 'SUCCESS' : 'FAILED'}"
    
    # Session management
    puts "\nSession Management:"
    session = create_user_session('user123', ip_address: '192.168.1.1')
    puts "Session ID: #{session[:session_id][0...16]}..."
    puts "User ID: #{session[:session_data][:user_id]}"
    puts "Expires at: #{session[:expires_at]}"
    
    # Session validation
    is_valid_session = validate_session(session[:session_id], session[:session_data])
    puts "Session validation: #{is_valid_session ? 'VALID' : 'INVALID'}"
    
    puts "\nAuthentication Features:"
    puts "- Secure password hashing (BCrypt)"
    puts "- Token-based authentication"
    puts "- Session management"
    puts "- Two-factor authentication (OTP)"
    puts "- API key management"
    puts "- Rate limiting"
    puts "- Account lockout"
  end
  
  def self.demonstrate_authorization
    puts "\nAuthorization Demonstration:"
    puts "=" * 50
    
    permission_system = create_permission_system
    
    # Test permissions
    roles = permission_system[:roles]
    puts "Roles and Permissions:"
    
    roles.each do |role, role_permissions|
      permissions = role_permissions.map { |perm| permission_system[:permissions][perm] }.flatten
      puts "#{role}: #{permissions.join(', ')}"
    end
    
    # Test permission checks
    puts "\nPermission Checks:"
    
    test_cases = [
      { role: 'admin', permission: 'delete', resource: 'user' },
      { role: 'moderator', permission: 'delete', resource: 'user' },
      { role: 'user', permission: 'delete', resource: 'user' },
      { role: 'guest', permission: 'read', resource: 'user' },
      { role: 'admin', permission: 'manage_settings', resource: 'system' }
    ]
    
    test_cases.each do |test_case|
      has_permission = check_permission(test_case[:role], test_case[:permission], test_case[:resource])
      puts "#{test_case[:role]} can #{test_case[:permission]} #{test_case[:resource]}: #{has_permission ? 'ALLOWED' : 'DENIED'}"
    end
    
    puts "\nAuthorization Features:"
    puts "- Role-based access control"
    puts "- Permission system"
    puts "- Resource-specific permissions"
    puts "- Hierarchical roles"
    puts "- Fine-grained access control"
  end
  
  def self.demonstrate_security_features
    puts "\nSecurity Features Demonstration:"
    puts "=" * 50
    
    # Rate limiting
    puts "Rate Limiting:"
    identifier = 'user123'
    attempts = 0
    
    5.times do |i|
      if implement_rate_limiting(identifier, 3, 60)
        attempts += 1
        puts "Attempt #{i + 1}: Allowed (Total: #{attempts})"
      else
        puts "Attempt #{i + 1}: Rate limited"
      end
    end
    
    # Account lockout
    puts "\nAccount Lockout:"
    user_id = 'user123'
    
    3.times do |i|
      result = implement_account_lockout(user_id)
      if result[:locked]
        puts "Attempt #{i + 1}: Account locked"
        break
      else
        puts "Attempt #{i + 1}: #{result[:attempts]} attempts"
      end
    end
    
    # Check lockout status
    lockout_status = check_account_lockout(user_id)
    puts "Lockout status: #{lockout_status[:locked] ? 'LOCKED' : 'UNLOCKED'}"
    
    puts "\nSecurity Features:"
    puts "- Rate limiting for API endpoints"
    puts "- Account lockout after failed attempts"
    puts "- Session timeout and inactivity detection"
    puts "- IP address validation"
    puts "- User agent validation"
    puts "- Secure token generation"
  end
end
```

## 🔍 Web Application Monitoring

### 4. Security Monitoring

Security event monitoring and logging:

```ruby
class SecurityMonitor
  def initialize
    @events = []
    @alerts = []
    @thresholds = {
      failed_logins: 5,
      sql_injection_attempts: 3,
      xss_attempts: 5,
      csrf_attempts: 3,
      rate_limit_violations: 10
    }
    @alert_file = 'security_alerts.log'
    @monitoring_active = false
  end
  
  def start_monitoring
    puts "Starting security monitoring..."
    @monitoring_active = true
    puts "Security monitoring started"
  end
  
  def stop_monitoring
    puts "Stopping security monitoring..."
    @monitoring_active = false
    puts "Security monitoring stopped"
  end
  
  def log_security_event(event_type, details = {})
    return unless @monitoring_active
    
    event = {
      id: SecureRandom.uuid,
      timestamp: Time.now,
      type: event_type,
      severity: determine_severity(event_type),
      details: details,
      ip_address: details[:ip_address],
      user_agent: details[:user_agent],
      user_id: details[:user_id]
    }
    
    @events << event
    
    # Check for alert conditions
    if should_alert?(event)
      alert = create_alert(event)
      @alerts << alert
      log_alert(alert)
    end
    
    puts "Security Event: #{event_type} - #{event[:severity]}"
  end
  
  def detect_attack_pattern(ip_address, event_type, time_window = 300)
    recent_events = @events.select do |event|
      event[:timestamp] > Time.now - time_window &&
      event[:ip_address] == ip_address &&
      event[:type] == event_type
    end
    
    {
      count: recent_events.length,
      time_window: time_window,
      events: recent_events
    }
  end
  
  def detect_brute_force_attack(ip_address)
    failed_logins = detect_attack_pattern(ip_address, 'login_failure')
    
    if failed_logins[:count] >= @thresholds[:failed_logins]
      create_alert({
        type: 'brute_force_attack',
        severity: 'high',
        message: "Brute force attack detected from #{ip_address}",
        details: failed_logins
      })
    end
  end
  
  def detect_sql_injection(ip_address)
    sql_attempts = detect_attack_pattern(ip_address, 'sql_injection')
    
    if sql_attempts[:count] >= @endpoints[:sql_injection]
      create_alert({
        type: 'sql_injection',
        severity: 'critical',
        message: "SQL injection attempts detected from #{ip_address}",
        details: sql_attempts
      })
    end
  end
  
  def detect_xss_attempt(ip_address)
    xss_attempts = detect_attack_pattern(ip_address, 'xss_attempt')
    
    if xss_attempts[:count] >= @thresholds[:xss_attempts]
      create_alert({
        type: 'xss_attempt',
        severity: 'high',
        message: "XSS attempts detected from #{ip_address}",
        details: xss_attempts
      })
    end
  end
  
  def detect_csrf_attempt(ip_address)
    csrf_attempts = detect_attack_pattern(ip_address, 'csrf_attempt')
    
    if csrf_attempts[:count] >= @thresholds[:csrf_attempts]
      create_alert({
        type: 'csrf_attempt',
        severity: 'medium',
        message: "CSRF attempts detected from #{ip_address}",
        details: csrf_attempts
      })
    end
  end
  
  def detect_anomalous_behavior(user_id, behavior_metrics)
    # Detect unusual user behavior
    baseline = get_user_baseline(user_id)
    
    anomalies = []
    
    # Check for unusual login patterns
    if behavior_metrics[:login_attempts] > baseline[:login_attempts] * 3
      anomalies << {
        type: 'unusual_login_pattern',
        severity: 'medium',
        message: "Unusual login pattern for user #{user_id}"
      }
    end
    
    # Check for unusual access patterns
    if behavior_metrics[:failed_access_attempts] > baseline[:failed_access_attempts] * 5
      anomalies << {
        type: 'unusual_access_pattern',
        severity: 'high',
        message: "Unusual access pattern for user #{user_id}"
      }
    end
    
    anomalies.each { |anomaly| log_alert(anomaly) }
    anomalies
  end
  
  def get_security_metrics
    {
      total_events: @events.length,
      total_alerts: @alerts.length,
      events_by_type: get_events_by_type,
      alerts_by_severity: get_alerts_by_severity,
      top_attack_sources: get_top_attack_sources,
      top_attack_types: get_top_attack_types,
      recent_alerts: get_recent_alerts(3600)
    }
  end
  
  def generate_security_report
    puts "Generating Security Report..."
    
    metrics = get_security_metrics
    
    report = {
      generated_at: Time.now.iso8601,
      metrics: metrics,
      recommendations: generate_recommendations(metrics),
      summary: generate_summary(metrics)
    }
    
    # Save report
    filename = "security_report_#{Time.now.strftime('%Y%m%d_%H%M%S')}.json"
    File.write(filename, JSON.pretty_generate(report))
    
    puts "Security report generated: #{filename}"
    report
  end
  
  def self.demonstrate_monitoring
    puts "Security Monitoring Demonstration:"
    puts "=" * 50
    
    monitor = SecurityMonitor.new
    monitor.start_monitoring
    
    # Simulate security events
    puts "Simulating Security Events:"
    
    events = [
      {
        type: 'login_failure',
        ip_address: '192.168.1.100',
        user_id: 'user1',
        details: { reason: 'Invalid password' }
      },
      {
        type: 'login_success',
        ip_address: '192.168.1.100',
        user_id: 'user1',
        details: { method: 'password' }
      },
      {
        type: 'sql_injection',
        ip_address: '10.0.0.50',
        user_id: nil,
        details: { query: "SELECT * FROM users WHERE id = 1 OR '1'='1" }
      },
      {
        type: 'xss_attempt',
        ip_address: '10.0.0.51',
        user_id: 'user2',
        details: { payload: '<script>alert("XSS")</script>' }
      },
      {
        type: 'csrf_attempt',
        ip_address: '10.0.0.52',
        user_id: 'user3',
        details: { token: 'invalid_token' }
      }
    ]
    
    events.each do |event|
      monitor.log_security_event(event[:type], event[:details])
    end
    
    # Detect attacks
    puts "\nAttack Detection:"
    
    monitor.detect_brute_force_attack('192.168.1.100')
    monitor.detect_sql_injection('10.0.0.50')
    monitor.detect_xss_attempt('10.0.0.51')
    monitor.detect_csrf_attempt('10.0.0.52')
    
    # Get metrics
    puts "\nSecurity Metrics:"
    metrics = monitor.get_security_metrics
    
    puts "Total events: #{metrics[:total_events]}"
    puts "Total alerts: #{metrics[:total_alerts]}"
    puts "Events by type: #{metrics[:events_by_type]}"
    puts "Alerts by severity: #{metrics[:alerts_by_severity]}"
    
    # Generate report
    puts "\nGenerating Security Report:"
    report = monitor.generate_security_report
    
    puts "\nSecurity Monitoring Features:"
    puts "- Real-time event logging"
    puts "- Attack pattern detection"
    puts "- Anomaly detection"
    puts "- Alert generation"
    puts "- Security metrics"
    puts "- Comprehensive reporting"
    
    monitor.stop_monitoring
  end
  
  private
  
  def determine_severity(event_type)
    severity_map = {
      'login_failure' => 'low',
      'login_success' => 'info',
      'sql_injection' => 'critical',
      'xss_attempt' => 'high',
      'csrf_attempt' => 'medium',
      'brute_force_attack' => 'high',
      'data_breach' => 'critical',
      'privilege_escalation' => 'high',
      'unauthorized_access' => 'medium'
    }
    
    severity_map[event_type] || 'medium'
  end
  
  def should_alert?(event)
    return true if event[:severity] == 'critical'
    return true if event[:severity] == 'high'
    
    # Check thresholds
    case event[:type]
    when 'login_failure'
      get_event_count(event[:type], event[:ip_address]) >= @thresholds[:failed_logins]
    when 'sql_injection'
      get_event_count(event[:type], event[:ip_address]) >= @thresholds[:sql_injection]
    when 'xss_attempt'
      get_event_count(event[:type], event[:ip_address]) >= @thresholds[:xss_attempts]
    when 'csrf_attempt'
      get_event_count(event[:type], event[:ip_address]) >= @thresholds[:csrf_attempts]
    else
      false
    end
  end
  
  def create_alert(event_or_anomaly)
    alert = {
      id: SecureRandom.uuid,
      timestamp: Time.now,
      type: event_or_anomaly[:type],
      severity: event_or_anomaly[:severity],
      message: event_or_anomaly[:message],
      details: event_or_anomaly[:details] || {}
    }
    
    alert
  end
  
  def log_alert(alert)
    log_entry = {
      timestamp: alert[:timestamp].iso8601,
      alert: alert
    }
    
    File.open(@alert_file, 'a') do |file|
      file.puts(JSON.pretty_generate(log_entry))
    end
    
    puts "SECURITY ALERT: #{alert[:severity]} - #{alert[:message]}"
  end
  
  def get_event_count(event_type, ip_address = nil)
    events = @events.select { |event| event[:type] == event_type }
    events = events.select { |event| event[:ip_address] == ip_address } if ip_address
    events.length
  end
  
  def get_events_by_type
    @events.group_by { |event| event[:type] }.transform_values(&:count)
  end
  
  def get_alerts_by_severity
    @alerts.group_by { |alert| alert[:severity] }.transform_values(&:count)
  end
  
  def get_top_attack_sources
    sources = @events.map { |event| event[:ip_address] }.compact
    source_counts = sources.each_with_object(Hash.new(0)) { |ip, hash| hash[ip] += 1 }
    
    source_counts.sort_by { |_, count| -count }.first(10)
  end
  
  def get_top_attack_types
    types = @events.map { |event| event[:type] }
    type_counts = types.each_with_object(Hash.new(0)) { |type, hash| hash[type] += 1 }
    
    type_counts.sort_by { |_, count| -count }.first(10)
  end
  
  def get_recent_alerts(time_window = 3600)
    cutoff = Time.now - time_window
    @alerts.select { |alert| alert[:timestamp] >= cutoff }
  end
  
  def get_user_baseline(user_id)
    # Simplified baseline calculation
    {
      login_attempts: 5,
      failed_access_attempts: 2,
      avg_session_duration: 1800,
      common_actions: %w[read write delete],
      common_resources: %w[profile settings]
    }
  end
  
  def generate_recommendations(metrics)
    recommendations = []
    
    if metrics[:alerts_by_severity]['critical'] > 0
      recommendations << 'Immediate action required for critical security events'
    end
    
    if metrics[:alerts_by_severity]['high'] > 10
      recommendations << 'Investigate high-severity security alerts'
    end
    
    if metrics[:events_by_type]['sql_injection'] > 0
      recommendations << 'Review and fix SQL injection vulnerabilities'
    end
    
    if metrics[:events_by_type]['xss_attempt'] > 5
      recommendations << 'Implement stronger XSS protection measures'
    end
    
    recommendations << 'Regular security audits and penetration testing'
    recommendations << 'Keep security monitoring and logging enabled'
    recommendations << 'Educate users about security best practices'
    
    recommendations
  end
  
  def generate_summary(metrics)
    {
      total_events: metrics[:total_events],
      total_alerts: metrics[:total_alerts],
      critical_alerts: metrics[:alerts_by_severity]['critical'] || 0,
      high_alerts: metrics[:alerts_by_severity]['high'] || 0,
      medium_alerts: metrics[:alerts_by_severity]['medium'] || 0,
      low_alerts: metrics[:alerts_by_severity]['low'] || 0,
      security_score: calculate_security_score(metrics)
    }
  end
  
  def calculate_security_score(metrics)
    base_score = 100
    
    # Deduct points for alerts
    score_deductions = {
      'critical' => 20,
      'high' => 10,
      'medium' => 5,
      'low' => 2
    }
    
    metrics[:alerts_by_severity].each do |severity, count|
      if score_deductions[severity]
        base_score -= (score_deductions[severity] * count)
      end
    end
    
    [base_score, 0].max
  end
end
```

## 🎯 Web Security Best Practices

### 5. Security Headers Implementation

HTTP security headers:

```ruby
class SecurityHeaders
  def self.generate_csp(headers = {})
    default_csp = {
      'default-src' => "'self'",
      'script-src' => "'self' 'unsafe-inline' 'unsafe-eval'",
      'style-src' => "'self' 'unsafe-inline'",
      'img-src' => "'self' data: https:",
      'font-src' => "'self' https:",
      'connect-src' => "'self' https:",
      'frame-ancestors' => "'none'",
      'base-uri' => "'self'",
      'form-action' => "'self'",
      'frame-src' => "'none'",
      'object-src' => "'none'",
      'media-src' => "'self' https:",
      'manifest-src' => "'self'"
    }
    
    csp = default_csp.merge(headers[:csp] || {})
    
    # Build CSP header
    csp_string = csp.map { |directive, value| "#{directive} #{value}" }.join('; ')
    
    { 'Content-Security-Policy' => csp_string }
  end
  
  def self.generate_hsts(headers = {})
    max_age = headers[:max_age] || 31536000 # 1 year
    include_subdomains = headers[:include_subdomains] || true
    preload = headers[:preload] || false
    
    hsts = "max-age=#{max_age}"
    hsts += "; includeSubDomains" if include_subdomains
    hsts += "; preload" if preload
    
    { 'Strict-Transport-Security' => hsts }
  end
  
  def self.generate_x_frame_options(headers = {})
    options = headers[:options] || 'DENY'
    
    { 'X-Frame-Options' => options }
  end
  
  def self.generate_x_content_type_options(headers = {})
    options = headers[:options] || 'nosniff'
    
    { 'X-Content-Type-Options' => options }
  end
  
  def self.generate_x_xss_protection(headers = {})
    mode = headers[:mode] || 'block'
    
    { 'X-XSS-Protection' => "1; mode=#{mode}" }
  end
  
  def self.generate_referrer_policy(headers = {})
    policy = headers[:policy] || 'strict-origin-when-cross-origin'
    
    { 'Referrer-Policy' => policy }
  end
  
  def self.generate_permissions_policy(headers = {})
    geolocation = headers[:geolocation] || 'none'
    microphone = headers[:microphone] || 'none'
    camera = headers[:camera] || 'none'
    payment = headers[:payment] || 'none'
    
    permissions = []
    permissions << "geolocation=#{geolocation}" if geolocation != 'none'
    permissions << "microphone=#{microphone}" if microphone != 'none'
    permissions << "camera=#{camera}" if camera != 'none'
    permissions << "payment=#{payment}" if payment != 'none'
    
    { 'Permissions-Policy' => permissions.join(', ') }
  end
  
  def self.generate_all_headers(custom_headers = {})
    headers = {}
    
    # Generate all security headers
    headers.merge!(generate_csp(custom_headers))
    headers.merge!(generate_hsts(custom_headers))
    headers.merge!(generate_x_frame_options(custom_headers))
    headers.merge!(generate_x_content_type_options(custom_headers))
    headers.merge!(generate_x_xss_protection(custom_headers))
    headers.merge!(generate_referrer_policy(custom_headers))
    headers.merge!(generate_permissions_policy(custom_headers))
    
    # Add custom headers
    headers.merge!(custom_headers)
    
    headers
  end
  
  def self.apply_security_headers(response, custom_headers = {})
    headers = generate_all_headers(custom_headers)
    
    headers.each do |header, value|
      response.headers[header] = value
    end
    
    puts "Applied #{headers.length} security headers:"
    headers.each { |header, value| puts "  #{header}: #{value}" }
    
    response
  end
  
  def self.demonstrate_headers
    puts "Security Headers Demonstration:"
    puts "=" * 50
    
    # Generate individual headers
    puts "Individual Headers:"
    
    csp = generate_csp
    puts "CSP: #{csp['Content-Security-Policy'][0...100]}..."
    
    hsts = generate_hsts
    puts "HSTS: #{hsts['Strict-Transport-Security']}"
    
    xfo = generate_x_frame_options
    puts "X-Frame-Options: #{xfo['X-Frame-Options']}"
    
    xcto = generate_x_content_type_options
    puts "X-Content-Type-Options: #{xcto['X-Content-Type-Options']}"
    
    xxssp = generate_xss_protection
    puts "X-XSS-Protection: #{xxssp['X-XSS-Protection']}"
    
    rp = generate_referrer_policy
    puts "Referrer-Policy: #{rp['Referrer-Policy']}"
    
    pp = generate_permissions_policy
    puts "Permissions-Policy: #{pp['Permissions-Policy']}"
    
    # Generate all headers
    puts "\nAll Security Headers:"
    all_headers = generate_all_headers
    
    all_headers.each do |header, value|
      puts "#{header}: #{value}"
    end
    
    puts "\nSecurity Headers Features:"
    puts "- Content Security Policy (CSP)"
    puts "- HTTP Strict Transport Security (HSTS)"
    puts "- X-Frame-Options (Clickjacking prevention)"
    puts "- X-Content-Type-Options (MIME sniffing prevention)"
    puts "- X-XSS-Protection (Browser XSS filtering)"
    puts "- Referrer-Policy (Privacy protection)"
    puts "- Permissions-Policy (Feature access)"
  end
  
  def self.validate_csp(csp_header)
    # Basic CSP validation
    required_directives = %w[default-src script-src style-src img-src]
    
    directives = csp_header.split(';').map(&:strip)
    present = directives.map { |d| d.split(' ').first }.to_s
    
    missing = required_directives - present
    missing.empty?
  end
  
  def self.validate_hsts(hsts_header)
    # HSTS validation
    return false unless hsts_header.include?('max-age=')
    return false unless hsts_header.include?('includeSubDomains')
    
    max_age_match = hsts_header.match(/max-age=(\d+)/)
    max_age = max_age_match ? max_age[1].to_i : 0
    
    max_age >= 31536000 # 1 year minimum
  end
  
  def self.demonstrate_validation
    puts "Security Headers Validation:"
    puts "=" * 50
    
    # Test CSP validation
    valid_csp = "default-src 'self'; script-src 'self'"
    invalid_csp = "script-src 'unsafe-inline'"
    
    puts "CSP Validation:"
    puts "Valid CSP: #{validate_csp(valid_csp) ? 'VALID' : 'INVALID'}"
    puts "Invalid CSP: #{validate_csp(invalid_csp) ? 'VALID' : 'INVALID'}"
    
    # Test HSTS validation
    valid_hsts = "max-age=31536000; includeSubDomains"
    invalid_hsts = "max-age=3600"
    
    puts "\nHSTS Validation:"
    puts "Valid HSTS: #{validate_hsts(valid_hsts) ? 'VALID' : 'INVALID'}"
    puts "Invalid HSTS: #{validate_hsts(invalid_hsts) ? 'VALID' : 'INVALID'}"
    
    puts "\nValidation Features:"
    puts "- CSP directive validation"
    puts "- HSTS parameter validation"
    puts "- Header format validation"
    puts "- Security best practices"
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Input Validation**: Create validation functions
2. **Password Security**: Implement secure hashing
3. **Security Headers**: Add security headers
4. **Authentication**: Build login system

### Intermediate Exercises

1. **Form Security**: Secure form processing
2. **Session Management**: Implement secure sessions
3. **Authorization**: Create RBAC system
4. **Security Monitoring**: Build monitoring system

### Advanced Exercises

1. **Web Security Platform**: Complete security solution
2. **Threat Detection**: Advanced pattern detection
3. **Compliance**: Security compliance tools
4. **Integration**: SIEM integration

---

## 🎯 Summary

Web Security in Ruby provides:

- **Security Fundamentals** - OWASP Top 10 and principles
- **Input Validation** - Comprehensive validation and sanitization
- **Authentication** - Secure login and session management
- **Authorization** - Role-based access control
- **Security Monitoring** - Event monitoring and alerting
- **Security Headers** - HTTP security headers implementation

Master these web security techniques for secure Ruby applications!
