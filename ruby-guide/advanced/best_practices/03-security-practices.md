# Security Practices in Ruby
# Comprehensive guide to secure Ruby application development

## 🎯 Overview

Security is critical for Ruby applications. This guide covers security best practices, common vulnerabilities, and techniques for building secure Ruby applications.

## 🔒 Security Fundamentals

### 1. OWASP Top 10 for Ruby

Understanding common security vulnerabilities:

```ruby
class SecurityFundamentals
  def self.owasp_top_10_ruby
    puts "OWASP Top 10 for Ruby Applications:"
    puts "=" * 50
    
    vulnerabilities = [
      {
        name: "A01: Broken Access Control",
        description: "Improperly implemented access control",
        ruby_risks: [
          "Insecure default permissions",
          "Missing authorization checks",
          "Privilege escalation vulnerabilities"
        ],
        examples: [
          "Admin endpoints without authentication",
          "Mass assignment vulnerabilities",
          "Insecure direct object references"
        ],
        prevention: [
          "Implement proper authentication",
          "Use strong authorization",
          "Validate user permissions",
          "Use role-based access control"
        ]
      },
      {
        name: "A02: Cryptographic Failures",
        description: "Weak or improper use of cryptography",
        ruby_risks: [
          "Weak encryption algorithms",
          "Insecure key management",
          "Hardcoded secrets"
        ],
        examples: [
          "Using MD5 for password hashing",
          "Storing secrets in code",
          "Weak random number generation"
        ],
        prevention: [
          "Use strong encryption algorithms",
          "Proper key management",
          "Use environment variables for secrets",
          "Implement proper password hashing"
        ]
      },
      {
        name: "A03: Injection",
        description: "SQL, NoSQL, OS, and LDAP injection",
        ruby_risks: [
          "SQL injection vulnerabilities",
          "Command injection risks",
          "Template injection issues"
        ],
        examples: [
          "Unparameterized SQL queries",
          "System command execution",
          "Unsafe template rendering"
        ],
        prevention: [
          "Use parameterized queries",
          "Avoid string concatenation",
          "Use safe template engines",
          "Validate and sanitize inputs"
        ]
      },
      {
        name: "A04: Insecure Design",
        description: "Flawed security architecture",
        ruby_risks: [
          "Insecure authentication flows",
          "Inadequate threat modeling",
          "Poor security architecture"
        ],
        examples: [
          "Weak password policies",
          "Insecure session management",
          "Inadequate logging and monitoring"
        ],
        prevention: [
          "Implement secure design patterns",
          "Conduct threat modeling",
          "Use secure authentication",
          "Implement proper logging"
        ]
      },
      {
        name: "A05: Security Misconfiguration",
        description: "Improperly configured security settings",
        ruby_risks: [
          "Default credentials",
          "Unnecessary services",
          "Verbose error messages"
        ],
        examples: [
          "Default database passwords",
          "Debug mode in production",
          "Unnecessary gems and dependencies"
        ],
        prevention: [
          "Remove default credentials",
          "Disable debug mode in production",
          "Review and harden configuration",
          "Regular security audits"
        ]
      },
      {
        name: "A06: Vulnerable Components",
        description: "Outdated or vulnerable dependencies",
        ruby_risks: [
          "Outdated Ruby versions",
          "Vulnerable gems",
          "Unpatched dependencies"
        ],
        examples: [
          "Ruby < 2.7 vulnerabilities",
          "Outdated Rails versions",
          "Vulnerable third-party gems"
        ],
        prevention: [
          "Keep Ruby and gems updated",
          "Use dependency scanning tools",
          "Regular security updates",
          "Monitor CVE databases"
        ]
      },
      {
        name: "A07: Identification & Authentication Failures",
        description: "Weak authentication and session management",
        ruby_risks: [
          "Weak password policies",
          "Insecure session management",
          "Poor authentication flows"
        ],
        examples: [
          "Weak password requirements",
          "Session fixation attacks",
          "Insecure password reset"
        ],
        prevention: [
          "Strong password policies",
          "Secure session management",
          "Multi-factor authentication",
          "Proper authentication flows"
        ]
      },
      {
        name: "A08: Software & Data Integrity Failures",
        description: "Code and data integrity issues",
        ruby_risks: [
          "Insecure deserialization",
          "Code injection",
          "Data tampering"
        ],
        examples: [
          "Unsafe YAML deserialization",
          "Code injection vulnerabilities",
          "Insecure file uploads"
        ],
        prevention: [
          "Safe deserialization practices",
          "Input validation and sanitization",
          "Code signing",
          "File upload security"
        ]
      },
      {
        name: "A09: Security Logging & Monitoring Failures",
        description: "Inadequate logging and monitoring",
        ruby_risks: [
          "Insufficient logging",
          "No intrusion detection",
          "Poor security monitoring"
        ],
        examples: [
          "No security event logging",
          "Missing intrusion detection",
          "Poor log management"
        ],
        prevention: [
          "Comprehensive security logging",
          "Intrusion detection systems",
          "Security monitoring",
          "Log analysis and alerting"
        ]
      },
      {
        name: "A10: Server-Side Request Forgery (SSRF)",
        description: "Server-side request forgery vulnerabilities",
        ruby_risks: [
          "Unrestricted URL fetches",
          "Blind SSRF vulnerabilities",
          "Cloud metadata SSRF"
        ],
        examples: [
          "Unrestricted HTTP requests",
          "File inclusion vulnerabilities",
          "Cloud service SSRF"
        ],
        prevention: [
          "URL allowlists",
          "Request validation",
          "Network segmentation",
          "Metadata service protection"
        ]
      }
    ]
    
    vulnerabilities.each do |vuln|
      puts "#{vuln[:name]}:"
      puts "  Description: #{vuln[:description]}"
      puts "  Ruby Risks: #{vuln[:ruby_risks].join(', ')}"
      puts "  Examples: #{vuln[:examples].join(', ')}"
      puts "  Prevention: #{vuln[:prevention].join(', ')}"
      puts
    end
  end
  
  def self.security_principles
    puts "\nSecurity Principles:"
    puts "=" * 50
    
    principles = [
      {
        principle: "Defense in Depth",
        description: "Multiple layers of security controls",
        examples: [
          "Authentication + Authorization",
          "Input validation + Output encoding",
          "Network security + Application security"
        ]
      },
      {
        principle: "Least Privilege",
        description: "Grant minimum necessary permissions",
        examples: [
          "Database user with limited permissions",
          "Application user with minimal rights",
          "File system access restrictions"
        ]
      },
      {
        principle: "Fail Securely",
        description: "Default to secure behavior on failure",
        examples: [
          "Deny access by default",
          "Return empty results on errors",
          "Log security events"
        ]
      },
      {
        principle: "Zero Trust",
        description: "Never trust, always verify",
        examples: [
          "Validate all inputs",
          "Verify all requests",
          "Authenticate all access"
        ]
      },
      {
        principle: "Secure by Default",
        description: "Secure configurations out of the box",
        examples: [
          "Secure default settings",
          "Disabled debug mode",
          "Strong default passwords"
        ]
      }
    ]
    
    principles.each do |principle|
      puts "#{principle[:principle]}:"
      puts "  Description: #{principle[:description]}"
      puts "  Examples: #{principle[:examples].join(', ')}"
      puts
    end
  end
  
  def self.ruby_specific_security
    puts "\nRuby-Specific Security Considerations:"
    puts "=" * 50
    
    ruby_security = [
      {
        area: "YAML Deserialization",
        risk: "Code execution vulnerabilities",
        examples: [
          "Unsafe YAML.load usage",
          "Psych.load with user input",
          "Rails YAML parsing vulnerabilities"
        ],
        mitigation: [
          "Use SafeYAML.load",
          "Avoid YAML.load with user input",
          "Use JSON instead of YAML"
        ]
      },
      {
        area: "Symbol Injection",
        risk: "Symbol conversion vulnerabilities",
        examples: [
          "to_sym with user input",
          "Hash key conversion",
          "Method name generation"
        ],
        mitigation: [
          "Validate symbol conversion",
          "Use allowlists for symbols",
          "Avoid to_sym with user input"
        ]
      },
      {
        area: "Eval and Binding",
        risk: "Code execution vulnerabilities",
        examples: [
          "eval with user input",
          "instance_eval with user data",
          "binding.eval vulnerabilities"
        ],
        mitigation: [
          "Avoid eval with user input",
          "Use safe alternatives",
          "Implement proper validation"
        ]
      },
      {
        area: "Marshal Deserialization",
        risk: "Object injection vulnerabilities",
        examples: [
          "Marshal.load with user data",
          "Cookie serialization",
          "Session data handling"
        ],
        mitigation: [
          "Avoid Marshal.load with user input",
          "Use JSON serialization",
          "Implement safe deserialization"
        ]
      },
      {
        area: "Regexp Injection",
        risk: "Regular expression injection",
        examples: [
          "Dynamic regex with user input",
          "Pattern injection vulnerabilities",
          "Regex denial of service"
        ],
        mitigation: [
          "Validate regex patterns",
          "Use regex escaping",
          "Limit regex complexity"
        ]
      }
    ]
    
    ruby_security.each do |security|
      puts "#{security[:area]}:"
      puts "  Risk: #{security[:risk]}"
      puts "  Examples: #{security[:examples].join(', ')}"
      puts "  Mitigation: #{security[:mitigation].join(', ')}"
      puts
    end
  end
  
  # Run security fundamentals examples
  owasp_top_10_ruby
  security_principles
  ruby_specific_security
end
```

### 2. Input Validation and Sanitization

Securing input handling:

```ruby
class InputValidation
  def self.validation_strategies
    puts "Input Validation Strategies:"
    puts "=" * 50
    
    strategies = [
      {
        strategy: "Allowlist Validation",
        description: "Only allow known good values",
        examples: [
          "Email format validation",
          "Phone number format",
          "Allowed file types"
        ],
        implementation: <<~RUBY
          class EmailValidator
            ALLOWED_DOMAINS = %w[example.com test.com]
            
            def self.valid?(email)
              return false unless email.is_a?(String)
              return false unless email.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
              
              domain = email.split('@').last
              ALLOWED_DOMAINS.include?(domain)
            end
          end
        RUBY
      },
      {
        strategy: "Blocklist Validation",
        description: "Block known bad values",
        examples: [
          "SQL injection patterns",
          "XSS attack patterns",
          "Command injection strings"
        ],
        implementation: <<~RUBY
          class InputSanitizer
            DANGEROUS_PATTERNS = [
              /<script.*?<\/script>/mi,
              /javascript:/i,
              /on\w+\s*=/i
            ].freeze
            
            def self.sanitize_html(input)
              return "" unless input.is_a?(String)
              
              DANGEROUS_PATTERNS.each do |pattern|
                input = input.gsub(pattern, "")
              end
              
              input
            end
          end
        RUBY
      },
      {
        strategy: "Format Validation",
        description: "Validate specific formats",
        examples: [
          "Date format validation",
          "Number range validation",
          "URL format validation"
        ],
        implementation: <<~RUBY
          class FormatValidator
            DATE_FORMAT = /\A\d{4}-\d{2}-\d{2}\z/
            NUMBER_RANGE = (1..100)
            
            def self.valid_date?(date_string)
              date_string.match?(DATE_FORMAT)
            end
            
            def self.valid_number?(number)
              return false unless number.is_a?(Numeric)
              NUMBER_RANGE.include?(number)
            end
          end
        RUBY
      },
      {
        strategy: "Length Validation",
        description: "Validate input length",
        examples: [
          "Password minimum length",
          "Username length limits",
          "Text field max length"
        ],
        implementation: <<~RUBY
          class LengthValidator
            PASSWORD_MIN_LENGTH = 8
            USERNAME_MAX_LENGTH = 50
            
            def self.valid_password?(password)
              return false unless password.is_a?(String)
              password.length >= PASSWORD_MIN_LENGTH
            end
            
            def self.valid_username?(username)
              return false unless username.is_a?(String)
              username.length <= USERNAME_MAX_LENGTH
            end
          end
        RUBY
      }
    ]
    
    strategies.each do |strategy|
      puts "#{strategy[:strategy]}:"
      puts "  Description: #{strategy[:description]}"
      puts "  Examples: #{strategy[:examples].join(', ')}"
      puts "  Implementation: #{strategy[:implementation]}"
      puts
    end
  end
  
  def self.sanitization_techniques
    puts "\nSanitization Techniques:"
    puts "=" * 50
    
    techniques = [
      {
        technique: "HTML Sanitization",
        description: "Remove dangerous HTML content",
        implementation: <<~RUBY
          class HTMLSanitizer
            DANGEROUS_TAGS = %w[script iframe object embed]
            DANGEROUS_ATTRIBUTES = %w[onload onclick onerror]
            
            def self.sanitize(html)
              return "" unless html.is_a?(String)
              
              # Remove dangerous tags
              DANGEROUS_TAGS.each do |tag|
                html = html.gsub(/<\s*\/?\s*#{tag}[^>]*>/i, "")
              end
              
              # Remove dangerous attributes
              DANGEROUS_ATTRIBUTES.each do |attr|
                html = html.gsub(/\s*#{attr}\s*=\s*["'][^"']*["']/i, "")
              end
              
              html
            end
          end
        RUBY
      },
      {
        technique: "SQL Injection Prevention",
        description: "Prevent SQL injection attacks",
        implementation: <<~RUBY
          class SQLSafeQuery
            def self.safe_query(table, conditions = {})
              # Use parameterized queries
              where_clauses = []
              params = []
              
              conditions.each do |column, value|
                where_clauses << "#{column} = ?"
                params << value
              end
              
              query = "SELECT * FROM #{table}"
              query += " WHERE #{where_clauses.join(' AND ')}" unless where_clauses.empty?
              
              [query, *params]
            end
          end
        RUBY
      },
      {
        technique: "Command Injection Prevention",
        description: "Prevent command injection attacks",
        implementation: <<~RUBY
          class SafeCommand
            ALLOWED_COMMANDS = %w[ls cat grep].freeze
            
            def self.safe_execute(command, args = [])
              return false unless ALLOWED_COMMANDS.include?(command)
              return false unless args.all? { |arg| arg.match?(/\A[a-zA-Z0-9_\-\.\/]+\z/) }
              
              system(command, *args)
            end
          end
        RUBY
      },
      {
        technique: "File Upload Security",
        description: "Secure file upload handling",
        implementation: <<~RUBY
          class SecureFileUpload
            ALLOWED_EXTENSIONS = %w[.jpg .png .pdf .docx].freeze
            MAX_FILE_SIZE = 10 * 1024 * 1024 # 10MB
            
            def self.secure_upload(file, upload_dir)
              return false unless file.respond_to?(:original_filename)
              return false unless file.respond_to?(:size)
              
              # Check file extension
              extension = File.extname(file.original_filename).downcase
              return false unless ALLOWED_EXTENSIONS.include?(extension)
              
              # Check file size
              return false if file.size > MAX_FILE_SIZE
              
              # Generate secure filename
              filename = SecureRandom.hex(16) + extension
              filepath = File.join(upload_dir, filename)
              
              # Save file
              File.open(filepath, 'wb') { |f| f.write(file.read) }
              
              filepath
            end
          end
        RUBY
      }
    ]
    
    techniques.each do |technique|
      puts "#{technique[:technique]}:"
      puts "  Description: #{technique[:description]}"
      puts "  Implementation: #{technique[:implementation]}"
      puts
    end
  end
  
  def self.validation_framework
    puts "\nValidation Framework:"
    puts "=" * 50
    
    framework = <<~RUBY
      class SecureValidator
        class ValidationError < StandardError; end
        
        def self.validate(params, rules)
          errors = []
          
          rules.each do |field, field_rules|
            value = params[field]
            
            # Required validation
            if field_rules[:required] && (value.nil? || value.to_s.strip.empty?)
              errors << "#{field} is required"
              next
            end
            
            next if value.nil? || value.to_s.empty?
            
            # Type validation
            if field_rules[:type]
              case field_rules[:type]
              when :string
                errors << "#{field} must be a string" unless value.is_a?(String)
              when :integer
                errors << "#{field} must be an integer" unless value.is_a?(Integer)
              when :email
                errors << "#{field} must be a valid email" unless value.match?(/\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i)
              when :url
                errors << "#{field} must be a valid URL" unless value.match?(/\Ahttps?:\/\/.+\z/i)
              end
            end
            
            # Length validation
            if field_rules[:min_length] && value.to_s.length < field_rules[:min_length]
              errors << "#{field} must be at least #{field_rules[:min_length]} characters"
            end
            
            if field_rules[:max_length] && value.to_s.length > field_rules[:max_length]
              errors << "#{field} must be no more than #{field_rules[:max_length]} characters"
            end
            
            # Range validation
            if field_rules[:range] && value.is_a?(Numeric)
              min_val, max_val = field_rules[:range]
              if value < min_val || value > max_val
                errors << "#{field} must be between #{min_val} and #{max_val}"
              end
            end
            
            # Pattern validation
            if field_rules[:pattern]
              pattern = field_rules[:pattern]
              errors << "#{field} format is invalid" unless value.to_s.match?(pattern)
            end
          end
          
          raise ValidationError, errors.join(', ') if errors.any?
          
          true
        end
      end
      
      # Usage example
      class UserRegistration
        def self.validate_registration(params)
          rules = {
            name: {
              required: true,
              type: :string,
              min_length: 2,
              max_length: 50
            },
            email: {
              required: true,
              type: :email
            },
            age: {
              required: true,
              type: :integer,
              range: (18..120)
            },
            password: {
              required: true,
              type: :string,
              min_length: 8,
              max_length: 100,
              pattern: /\A(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+\z/i
            }
          }
          
          SecureValidator.validate(params, rules)
        end
      end
      
      # Example usage
      begin
        UserRegistration.validate_registration({
          name: "John Doe",
          email: "john@example.com",
          age: 25,
          password: "SecurePass123"
        })
        puts "Validation passed!"
      rescue SecureValidator::ValidationError => e
        puts "Validation failed: #{e.message}"
      end
    RUBY
    
    puts "Validation Framework Example:"
    puts framework
  end
  
  # Run input validation examples
  validation_strategies
  sanitization_techniques
  validation_framework
end
```

## 🔐 Authentication and Authorization

### 1. Secure Authentication

Implementing secure authentication systems:

```ruby
class SecureAuthentication
  def self.password_security
    puts "Password Security:"
    puts "=" * 50
    
    puts "Password Best Practices:"
    practices = [
      "Use strong hashing algorithms (bcrypt, Argon2)",
      "Implement proper password policies",
      "Use password hashing with salt",
      "Implement password strength validation",
      "Use multi-factor authentication",
      "Implement secure password reset",
      "Store passwords securely"
    ]
    
    practices.each { |practice| puts "• #{practice}" }
    
    password_hashing = <<~RUBY
      require 'bcrypt'
      
      class PasswordManager
        def self.hash_password(password)
          # Use bcrypt with salt
          BCrypt::Password.create(password)
        end
        
        def self.verify_password(password, hashed_password)
          BCrypt::Password.new(hashed_password) == password
        end
        
        def self.validate_password_strength(password)
          errors = []
          
          # Minimum length
          errors << "Password must be at least 8 characters" if password.length < 8
          
          # Uppercase letter
          errors << "Password must contain uppercase letter" unless password.match?(/[A-Z]/)
          
          # Lowercase letter
          errors << "Password must contain lowercase letter" unless password.match?(/[a-z]/)
          
          # Number
          errors << "Password must contain number" unless password.match?(/\d/)
          
          # Special character
          errors << "Password must contain special character" unless password.match?(/[!@#$%^&*]/)
          
          errors
        end
      end
      
      # Usage
      password = "SecurePass123!"
      hashed_password = PasswordManager.hash_password(password)
      
      # Verify password
      is_valid = PasswordManager.verify_password(password, hashed_password)
      puts "Password verification: #{is_valid}"
      
      # Validate password strength
      errors = PasswordManager.validate_password_strength("weak")
      puts "Password validation errors: #{errors.join(', ')}" if errors.any?
    RUBY
    
    puts "\nPassword Hashing Example:"
    puts password_hashing
  end
  
  def self.session_security
    puts "\nSession Security:"
    puts "=" * 50
    
    puts "Session Security Best Practices:"
    practices = [
      "Use secure session storage",
      "Implement proper session timeout",
      "Use secure session cookies",
      "Implement session fixation protection",
      "Regenerate session on login",
      "Destroy session on logout",
      "Use HTTPS for session cookies"
    ]
    
    practices.each { |practice| puts "• #{practice}" }
    
    session_management = <<~RUBY
      class SecureSessionManager
        SESSION_TIMEOUT = 30.minutes
        COOKIE_OPTIONS = {
          httponly: true,
          secure: true,
          same_site: :strict,
          max_age: SESSION_TIMEOUT
        }.freeze
        
        def self.create_session(user_id, request)
          # Generate secure session ID
          session_id = SecureRandom.hex(32)
          
          # Store session data
          session_data = {
            user_id: user_id,
            created_at: Time.now,
            ip_address: request.remote_ip,
            user_agent: request.user_agent
          }
          
          # Store in secure storage (Redis, database, etc.)
          store_session(session_id, session_data)
          
          # Set secure cookie
          cookies[:session_id] = {
            value: session_id,
            **COOKIE_OPTIONS
          }
          
          session_id
        end
        
        def self.validate_session(session_id, request)
          return nil unless session_id
          
          # Retrieve session data
          session_data = retrieve_session(session_id)
          return nil unless session_data
          
          # Check session timeout
          return nil if session_data[:created_at] < Time.now - SESSION_TIMEOUT
          
          # Check IP address (optional)
          return nil if session_data[:ip_address] != request.remote_ip
          
          # Check user agent (optional)
          return nil if session_data[:user_agent] != request.user_agent
          
          session_data
        end
        
        def self.destroy_session(session_id)
          # Remove session from storage
          remove_session(session_id)
          
          # Clear cookie
          cookies.delete(:session_id)
        end
        
        def self.regenerate_session(old_session_id, request)
          # Get current session data
          old_session_data = retrieve_session(old_session_id)
          return nil unless old_session_data
          
          # Create new session
          new_session_id = create_session(old_session_data[:user_id], request)
          
          # Destroy old session
          destroy_session(old_session_id)
          
          new_session_id
        end
        
        private
        
        def self.store_session(session_id, data)
          # Store in Redis, database, or other secure storage
          # This is a simplified example
          $redis.setex("session:#{session_id}", SESSION_TIMEOUT, data.to_json)
        end
        
        def self.retrieve_session(session_id)
          # Retrieve from storage
          data = $redis.get("session:#{session_id}")
          data ? JSON.parse(data) : nil
        end
        
        def self.remove_session(session_id)
          # Remove from storage
          $redis.del("session:#{session_id}")
        end
      end
    RUBY
    
    puts "\nSession Management Example:"
    puts session_management
  end
  
  def self.multi_factor_authentication
    puts "\nMulti-Factor Authentication:"
    puts "=" * 50
    
    puts "MFA Best Practices:"
    practices = [
      "Use time-based OTP (TOTP)",
      "Implement backup codes",
      "Use authenticator apps",
      "Support SMS verification",
      "Implement device trust",
      "Use secure QR codes",
      "Implement rate limiting"
    ]
    
    practices.each { |practice| puts "• #{practice}" }
    
    mfa_implementation = <<~RUBY
      require 'rotp'
      require 'rqrcode'
      
      class TwoFactorAuth
        def self.generate_secret
          ROTP::Base32.random_base32
        end
        
        def self.generate_qr_code(secret, user_email)
          provisioning_uri = "otpauth://totp/RubyApp:#{user_email}?secret=#{secret}"
          qrcode = RQRCode.new(provisioning_uri)
          
          # Convert to image or return data
          qrcode.as_png
        end
        
        def self.verify_code(secret, code)
          totp = ROTP::TOTP.new(secret)
          totp.verify(code)
        end
        
        def self.generate_backup_codes(count = 10)
          Array.new(count) { SecureRandom.hex(4) }
        end
        
        def self.verify_backup_code(user_id, code)
          # Check against stored backup codes
          backup_codes = get_backup_codes(user_id)
          return false unless backup_codes.include?(code)
          
          # Remove used backup code
          backup_codes.delete(code)
          save_backup_codes(user_id, backup_codes)
          
          true
        end
        
        def self.send_sms_code(phone_number)
          code = SecureRandom.hex(3).upcase
          # Send SMS via SMS service
          SMSService.send_code(phone_number, code)
          code
        end
        
        def self.verify_sms_code(phone_number, code)
          # Verify against sent code
          SMSService.verify_code(phone_number, code)
        end
        
        private
        
        def self.get_backup_codes(user_id)
          # Retrieve from database
          BackupCode.where(user_id: user_id, used: false).pluck(:code)
        end
        
        def self.save_backup_codes(user_id, codes)
          # Save to database
          codes.each do |code|
            BackupCode.create(user_id: user_id, code: code, used: false)
          end
        end
      end
      
      class SMSService
        def self.send_code(phone_number, code)
          # Integration with SMS service
          puts "Sending code #{code} to #{phone_number}"
          # Actual implementation would use Twilio, AWS SNS, etc.
        end
        
        def self.verify_code(phone_number, code)
          # Verify against sent code
          puts "Verifying code #{code} for #{phone_number}"
          # Actual implementation would check against sent code
          true
        end
      end
      
      class BackupCode < ActiveRecord::Base
        # Backup code model
      end
    RUBY
    
    puts "\nMFA Implementation Example:"
    puts mfa_implementation
  end
  
  # Run authentication examples
  password_security
  session_security
  multi_factor_authentication
end
```

### 2. Authorization Systems

Implementing proper authorization:

```ruby
class AuthorizationSystems
  def self.rbac_implementation
    puts "Role-Based Access Control (RBAC):"
    puts "=" * 50
    
    rbac_implementation = <<~RUBY
      class User < ActiveRecord::Base
        has_many :user_roles
        has_many :roles, through: :user_roles
        has_many :permissions, through: :roles
        
        def has_permission?(permission_name)
          permissions.exists?(name: permission_name)
        end
        
        def has_role?(role_name)
          roles.exists?(name: role_name)
        end
        
        def can?(action, resource)
          permission_name = "#{action}_#{resource}"
          has_permission?(permission_name)
        end
      end
      
      class Role < ActiveRecord::Base
        has_many :user_roles
        has_many :users, through: :user_roles
        has_and_belongs_to_many :permissions
        
        def self.admin
          find_by(name: 'admin')
        end
        
        def self.user
          find_by(name: 'user')
        end
        
        def self.moderator
          find_by(name: 'moderator')
        end
      end
      
      class UserRole < ActiveRecord::Base
        belongs_to :user
        belongs_to :role
      end
      
      class Permission < ActiveRecord::Base
        has_and_belongs_to_many :roles
        
        def self.create_permissions
          permissions = [
            # User permissions
            { name: 'read_user', description: 'Read user information' },
            { name: 'write_user', description: 'Update user information' },
            { name: 'delete_user', description: 'Delete user' },
            
            # Admin permissions
            { name: 'read_all_users', description: 'Read all users' },
            { name: 'manage_roles', description: 'Manage user roles' },
            { name: 'system_config', description: 'Configure system' },
            
            # Content permissions
            { name: 'create_content', description: 'Create content' },
            { name: 'edit_content', description: 'Edit content' },
            { name: 'delete_content', description: 'Delete content' },
            { name: 'publish_content', description: 'Publish content' },
            
            # Moderation permissions
            { name: 'moderate_content', description: 'Moderate content' },
            { name: 'ban_users', description: 'Ban users' }
          ]
          
          permissions.each do |perm|
            find_or_create_by(name: perm[:name]) do |permission|
              permission.description = perm[:description]
            end
          end
        end
      end
      
      # Setup roles and permissions
      class AuthorizationSetup
        def self.setup_roles_and_permissions
          Permission.create_permissions
          
          # Create roles
          admin_role = Role.find_or_create_by(name: 'admin', description: 'System administrator')
          user_role = Role.find_or_create_by(name: 'user', description: 'Regular user')
          moderator_role = Role.find_or_create_by(name: 'moderator', description: 'Content moderator')
          
          # Assign permissions to roles
          admin_role.permissions << Permission.where(name: [
            'read_user', 'write_user', 'delete_user',
            'read_all_users', 'manage_roles', 'system_config'
          ])
          
          user_role.permissions << Permission.where(name: [
            'read_user', 'write_user'
          ])
          
          moderator_role.permissions << Permission.where(name: [
            'read_user', 'write_user',
            'moderate_content', 'ban_users'
          ])
        end
      end
      
      # Authorization middleware
      class AuthorizationMiddleware
        def initialize(app)
          @app = app
        end
        
        def call(env)
          request = ActionDispatch::Request.new(env)
          
          # Skip authorization for public routes
          return @app.call(env) if public_path?(request.path)
          
          # Get current user
          current_user = get_current_user(env)
          
          # Check if user is authenticated
          unless current_user
            return unauthorized_response
          end
          
          # Check authorization
          unless authorized?(current_user, request)
            return forbidden_response
          end
          
          @app.call(env)
        end
        
        private
        
        def public_path?(path)
          public_paths = ['/login', '/register', '/forgot_password', '/']
          public_paths.any? { |public_path| path.start_with?(public_path) }
        end
        
        def get_current_user(env)
          # Get user from session or token
          session_id = env['rack.session'][:session_id]
          return nil unless session_id
          
          session_data = SecureSessionManager.validate_session(session_id, request)
          return nil unless session_data
          
          User.find(session_data[:user_id])
        end
        
        def authorized?(user, request)
          # Check if user has permission for the requested action
          controller_name = request.path_parameters[:controller]
          action_name = request.path_parameters[:action]
          
          # Define authorization rules
          authorization_rules = {
            'users' => {
              'index' => 'read_all_users',
              'show' => 'read_user',
              'update' => 'write_user',
              'destroy' => 'delete_user'
            },
            'admin' => {
              'index' => 'system_config',
              'settings' => 'system_config'
            },
            'content' => {
              'index' => 'read_content',
              'new' => 'create_content',
              'create' => 'create_content',
              'edit' => 'edit_content',
              'update' => 'edit_content',
              'destroy' => 'delete_content',
              'publish' => 'publish_content'
            }
          }
          
          permission_name = authorization_rules.dig(controller_name)&.dig(action_name)
          return true unless permission_name
          
          user.has_permission?(permission_name)
        end
        
        def unauthorized_response
          [401, { 'Content-Type' => 'application/json' }, [{ error: 'Unauthorized' }.to_json]]
        end
        
        def forbidden_response
          [403, { 'Content-Type' => 'application/json' }, [{ error: 'Forbidden' }.to_json]]
        end
      end
    RUBY
    
    puts "RBAC Implementation Example:"
    puts rbac_implementation
  end
  
  def self.attribute_based_access_control
    puts "\nAttribute-Based Access Control (ABAC):"
    puts "=" * 50
    
    abac_implementation = <<~RUBY
      class ABACPolicy
        def self.can_access?(user, resource, action, context = {})
          # Define policy rules
          rules = [
            # Rule 1: Users can only access their own data
            {
              name: 'own_data_access',
              condition: ->(user, resource, action, context) {
                resource.user_id == user.id
              },
              actions: %w[read update delete]
            },
            
            # Rule 2: Admins can access any data
            {
              name: 'admin_access',
              condition: ->(user, resource, action, context) {
                user.has_role?('admin')
              },
              actions: %w[read update delete]
            },
            
            # Rule 3: Users can access public content
            {
              name: 'public_content_access',
              condition: ->(user, resource, action, context) {
                resource.visibility == 'public'
              },
              actions: %w[read]
            },
            
            # Rule 4: Users can access content during business hours
            {
              name: 'business_hours_access',
              condition: ->(user, resource, action, context) {
                Time.now.hour >= 9 && Time.now.hour <= 17
              },
              actions: %w[read update]
            },
            
            # Rule 5: Users can access resources in their location
            {
              name: 'location_based_access',
              condition: ->(user, resource, action, context) {
                resource.location == user.location
              },
              actions: %w[read update]
            }
          ]
          
          # Evaluate rules
          rules.each do |rule|
            next unless rule[:actions].include?(action)
            
            if rule[:condition].call(user, resource, action, context)
              return true
            end
          end
          
          false
        end
        
        def self.evaluate_policies(user, resource, action, context = {})
          results = {}
          
          # Evaluate all policies
          results[:own_data_access] = can_access?(user, resource, action, context) if resource.user_id
          results[:admin_access] = can_access?(user, resource, action, context)
          results[:public_content_access] = can_access?(user, resource, action, context) if resource.respond_to?(:visibility)
          results[:business_hours_access] = can_access?(user, resource, action, context)
          results[:location_based_access] = can_access?(user, resource, action, context) if user.respond_to?(:location) && resource.respond_to?(:location)
          
          results
        end
      end
      
      class Resource
        attr_accessor :id, :user_id, :visibility, :location, :type
        
        def initialize(id:, user_id: nil, visibility: 'private', location: 'US', type: 'document')
          @id = id
          @user_id = user_id
          @visibility = visibility
          @location = location
          @type = type
        end
      end
      
      class User
        attr_accessor :id, :name, :email, :location, :roles
        
        def initialize(id:, name:, email:, location: 'US', roles: [])
          @id = id
          @name = name
          @email = email
          @location = location
          @roles = roles
        end
        
        def has_role?(role_name)
          @roles.include?(role_name)
        end
      end
      
      # Usage example
      def self.demonstrate_abac
        # Create users
        regular_user = User.new(
          id: 1,
          name: 'John Doe',
          email: 'john@example.com',
          location: 'US',
          roles: ['user']
        )
        
        admin_user = User.new(
          id: 2,
          name: 'Jane Smith',
          email: 'jane@example.com',
          location: 'US',
          roles: ['admin']
        )
        
        # Create resources
        user_resource = Resource.new(
          id: 1,
          user_id: 1,
          visibility: 'private',
          location: 'US',
          type: 'document'
        )
        
        public_resource = Resource.new(
          id: 2,
          visibility: 'public',
          location: 'US',
          type: 'document'
        )
        
        # Test access
        puts "Regular user accessing own resource: #{ABACPolicy.can_access?(regular_user, user_resource, 'read')}"
        puts "Regular user accessing public resource: #{ABACPolicy.can_access?(regular_user, public_resource, 'read')}"
        puts "Admin user accessing any resource: #{ABACPolicy.can_access?(admin_user, user_resource, 'delete')}"
        
        # Show policy evaluation
        puts "\nPolicy evaluation for regular user:"
        policies = ABACPolicy.evaluate_policies(regular_user, user_resource, 'read')
        policies.each { |policy, result| puts "  #{policy}: #{result}" }
      end
    RUBY
    
    puts "ABAC Implementation Example:"
    puts abac_implementation
  end
  
  def self.authorization_middleware
    puts "\nAuthorization Middleware:"
    puts "=" * 50
    
    middleware_implementation = <<~RUBY
      class AuthorizationMiddleware
        def initialize(app)
          @app = app
          @policies = load_policies
        end
        
        def call(env)
          request = ActionDispatch::Request.new(env)
          
          # Skip authorization for public routes
          return @app.call(env) if public_path?(request.path)
          
          # Get current user
          current_user = get_current_user(env)
          
          # Check if user is authenticated
          unless current_user
            return unauthorized_response
          end
          
          # Check authorization
          unless authorized?(current_user, request)
            return forbidden_response
          end
          
          @app.call(env)
        end
        
        private
        
        def load_policies
          # Load authorization policies from configuration
          {
            'users' => {
              'index' => { roles: ['admin'], permissions: ['read_all_users'] },
              'show' => { roles: ['admin', 'user'], permissions: ['read_user'] },
              'update' => { roles: ['admin'], permissions: ['write_user'] },
              'destroy' => { roles: ['admin'], permissions: ['delete_user'] }
            },
            'content' => {
              'index' => { roles: ['admin', 'user', 'moderator'], permissions: ['read_content'] },
              'create' => { roles: ['admin', 'user'], permissions: ['create_content'] },
              'update' => { roles: ['admin', 'moderator'], permissions: ['edit_content'] },
              'destroy' => { roles: ['admin'], permissions: ['delete_content'] }
            },
            'admin' => {
              'index' => { roles: ['admin'], permissions: ['system_config'] },
              'settings' => { roles: ['admin'], permissions: ['system_config'] }
            }
          }
        end
        
        def get_current_user(env)
          # Get user from session or token
          session_id = env['rack.session'][:session_id]
          return nil unless session_id
          
          session_data = SecureSessionManager.validate_session(session_id, request)
          return nil unless session_data
          
          User.find(session_data[:user_id])
        end
        
        def authorized?(user, request)
          controller_name = request.path_parameters[:controller]
          action_name = request.path_parameters[:action]
          
          # Get policy for controller/action
          policy = @policies.dig(controller_name)&.dig(action_name)
          return false unless policy
          
          # Check roles
          if policy[:roles]
            return true unless policy[:roles].any?
            return true if policy[:roles].any? { |role| user.has_role?(role) }
          end
          
          # Check permissions
          if policy[:permissions]
            return true unless policy[:permissions].any?
            return true if policy[:permissions].any? { |permission| user.has_permission?(permission) }
          end
          
          false
        end
        
        def public_path?(path)
          public_paths = ['/login', '/register', '/forgot_password', '/']
          public_paths.any? { |public_path| path.start_with?(public_path) }
        end
        
        def unauthorized_response
          [401, { 'Content-Type' => 'application/json' }, [{ error: 'Unauthorized' }.to_json]]
        end
        
        def forbidden_response
          [403, { 'Content-Type' => 'application/json' }, [{ error: 'Forbidden' }.to_json]]
        end
      end
    RUBY
    
    puts "Authorization Middleware Example:"
    puts middleware_implementation
  end
  
  # Run authorization examples
  rbac_implementation
  attribute_based_access_control
  authorization_middleware
end
```

## 🛡️ Data Protection

### 1. Encryption and Hashing

Secure data protection techniques:

```ruby
class DataProtection
  def self.encryption_best_practices
    puts "Encryption Best Practices:"
    puts "=" * 50
    
    practices = [
      "Use strong encryption algorithms (AES-256)",
      "Use proper key management",
      "Encrypt sensitive data at rest",
      "Encrypt data in transit",
      "Use secure key derivation",
      "Implement proper key rotation",
      "Use authenticated encryption"
    ]
    
    practices.each { |practice| puts "• #{practice}" }
    
    encryption_implementation = <<~RUBY
      require 'openssl'
      require 'base64'
      
      class DataEncryption
        # Use AES-256-GCM for authenticated encryption
        CIPHER = 'AES-256-GCM'
        KEY_SIZE = 32
        IV_SIZE = 12
        TAG_SIZE = 16
        
        def self.encrypt(data, encryption_key)
          return nil unless data && encryption_key
          
          # Generate random IV
          iv = OpenSSL::Random.random_bytes(IV_SIZE)
          
          # Create cipher
          cipher = OpenSSL::Cipher.new(CIPHER)
          cipher.encrypt
          cipher.key = encryption_key
          cipher.iv = iv
          
          # Encrypt data
          encrypted_data = cipher.update(data)
          encrypted_data << cipher.final
          
          # Get authentication tag
          tag = cipher.auth_tag
          
          # Combine IV, tag, and encrypted data
          combined = iv + tag + encrypted_data
          
          Base64.strict_encode64(combined)
        end
        
        def self.decrypt(encrypted_data, encryption_key)
          return nil unless encrypted_data && encryption_key
          
          # Decode Base64
          combined = Base64.strict_decode64(encrypted_data)
          
          # Extract IV, tag, and encrypted data
          iv = combined[0...IV_SIZE]
          tag = combined[IV_SIZE...IV_SIZE + TAG_SIZE]
          encrypted_data = combined[IV_SIZE + TAG_SIZE..-1]
          
          # Create cipher
          cipher = OpenSSL::Cipher.new(CIPHER)
          cipher.decrypt
          cipher.key = encryption_key
          cipher.iv = iv
          cipher.auth_tag = tag
          
          # Decrypt data
          decrypted_data = cipher.update(encrypted_data)
          decrypted_data << cipher.final
          
          decrypted_data
        rescue OpenSSL::Cipher::CipherError
          nil
        end
        
        def self.generate_key
          OpenSSL::Random.random_bytes(KEY_SIZE)
        end
        
        def self.derive_key(password, salt, iterations: 10000)
          # Use PBKDF2 for key derivation
          OpenSSL::PKCS5.pbkdf2_hmac(
            password,
            salt,
            iterations,
            KEY_SIZE,
            OpenSSL::Digest::SHA256.new
          )
        end
      end
      
      # Usage example
      class SecureDataStorage
        def self.store_sensitive_data(data, password)
          # Generate salt for key derivation
          salt = OpenSSL::Random.random_bytes(16)
          
          # Derive encryption key from password
          encryption_key = DataEncryption.derive_key(password, salt)
          
          # Encrypt data
          encrypted_data = DataEncryption.encrypt(data, encryption_key)
          
          # Store salt and encrypted data
          {
            salt: Base64.strict_encode64(salt),
            encrypted_data: encrypted_data
          }
        end
        
        def self.retrieve_sensitive_data(stored_data, password)
          # Decode salt
          salt = Base64.strict_decode64(stored_data[:salt])
          
          # Derive encryption key from password
          encryption_key = DataEncryption.derive_key(password, salt)
          
          # Decrypt data
          DataEncryption.decrypt(stored_data[:encrypted_data], encryption_key)
        end
      end
      
      # Example usage
      sensitive_data = "This is sensitive information"
      password = "SecurePassword123!"
      
      # Store data
      stored_data = SecureDataStorage.store_sensitive_data(sensitive_data, password)
      puts "Stored data: #{stored_data.keys.join(', ')}"
      
      # Retrieve data
      retrieved_data = SecureDataStorage.retrieve_sensitive_data(stored_data, password)
      puts "Retrieved data: #{retrieved_data}"
    RUBY
    
    puts "\nEncryption Implementation Example:"
    puts encryption_implementation
  end
  
  def self.hashing_techniques
    puts "\nHashing Techniques:"
    puts "=" * 50
    
    puts "Hashing Best Practices:"
    practices = [
      "Use strong hashing algorithms (bcrypt, Argon2)",
      "Use salt with hashing",
      "Use appropriate work factors",
      "Hash passwords only",
      "Never hash with MD5 or SHA-1",
      "Use different salts for each hash",
      "Implement proper password policies"
    ]
    
    practices.each { |practice| puts "• #{practice}" }
    
    hashing_implementation = <<~RUBY
      require 'bcrypt'
      require 'digest'
      
      class SecureHashing
        # Password hashing with bcrypt
        def self.hash_password(password)
          BCrypt::Password.create(password)
        end
        
        def self.verify_password(password, hashed_password)
          BCrypt::Password.new(hashed_password) == password
        end
        
        # Data hashing with SHA-256
        def self.hash_data(data)
          Digest::SHA256.hexdigest(data)
        end
        
        # HMAC for message authentication
        def self.hmac_sha256(data, secret)
          OpenSSL::HMAC.digest('sha256', secret, data)
        end
        
        # Password strength checking
        def self.check_password_strength(password)
          errors = []
          
          # Length check
          errors << "Password too short (minimum 8 characters)" if password.length < 8
          
          # Complexity check
          errors << "Password must contain uppercase letter" unless password.match?(/[A-Z]/)
          errors << "Password must contain lowercase letter" unless password.match?(/[a-z]/)
          errors << "Password must contain number" unless password.match?(/\d/)
          errors << "Password must contain special character" unless password.match?(/[!@#$%^&*]/)
          
          # Common password check
          common_passwords = %w[password123 qwerty admin 123456]
          errors << "Password is too common" if common_passwords.include?(password.downcase)
          
          errors
        end
        
        # Password generation
        def self.generate_secure_password(length = 12)
          characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*'
          password = ''
          
          length.times do
            password += characters[SecureRandom.random_number(characters.length)]
          end
          
          # Ensure password meets requirements
          until check_password_strength(password).empty?
            password = generate_secure_password(length)
          end
          
          password
        end
      end
      
      # Usage example
      password = "SecurePassword123!"
      
      # Hash password
      hashed_password = SecureHashing.hash_password(password)
      puts "Hashed password: #{hashed_password}"
      
      # Verify password
      is_valid = SecureHashing.verify_password(password, hashed_password)
      puts "Password verification: #{is_valid}"
      
      # Hash data
      data = "Important data"
      hashed_data = SecureHashing.hash_data(data)
      puts "Hashed data: #{hashed_data}"
      
      # HMAC
      secret = "secret_key"
      hmac = SecureHashing.hmac_sha256(data, secret)
      puts "HMAC: #{hmac}"
      
      # Check password strength
      weak_password = "password"
      errors = SecureHashing.check_password_strength(weak_password)
      puts "Password strength errors: #{errors.join(', ')}" if errors.any?
      
      # Generate secure password
      secure_password = SecureHashing.generate_secure_password
      puts "Generated secure password: #{secure_password}"
    RUBY
    
    puts "\nHashing Implementation Example:"
    puts hashing_implementation
  end
  
  def self.key_management
    puts "\nKey Management:"
    puts "=" * 50
    
    puts "Key Management Best Practices:"
    practices = [
      "Use environment variables for secrets",
      "Never commit secrets to version control",
      "Use key management services",
      "Implement key rotation",
      "Use different keys for different purposes",
      "Encrypt keys at rest",
      "Use secure key generation",
      "Implement proper access controls"
    ]
    
    practices.each { |practice| puts "• #{practice}" }
    
    key_management_implementation = <<~RUBY
      require 'openssl'
      require 'base64'
      
      class KeyManager
        KEY_SIZE = 32
        IV_SIZE = 16
        
        def self.generate_key
          OpenSSL::Random.random_bytes(KEY_SIZE)
        end
        
        def self.generate_iv
          OpenSSL::Random.random_bytes(IV_SIZE)
        end
        
        def self.encrypt_key(key, master_key)
          # Use master key to encrypt application key
          iv = generate_iv
          
          cipher = OpenSSL::Cipher.new('AES-256-CBC')
          cipher.encrypt
          cipher.key = master_key
          cipher.iv = iv
          
          encrypted_key = cipher.update(key)
          encrypted_key << cipher.final
          
          # Combine IV and encrypted key
          combined = iv + encrypted_key
          Base64.strict_encode64(combined)
        end
        
        def self.decrypt_key(encrypted_key, master_key)
          combined = Base64.strict_decode64(encrypted_key)
          
          iv = combined[0...IV_SIZE]
          encrypted_key = combined[IV_SIZE..-1]
          
          cipher = OpenSSL::Cipher.new('AES-256-CBC')
          cipher.decrypt
          cipher.key = master_key
          cipher.iv = iv
          
          key = cipher.update(encrypted_key)
          key << cipher.final
          
          key
        rescue OpenSSL::Cipher::CipherError
          nil
        end
        
        def self.rotate_key(old_key, master_key)
          # Generate new key
          new_key = generate_key
          
          # Encrypt new key with master key
          encrypted_new_key = encrypt_key(new_key, master_key)
          
          # Return both old and new encrypted keys for migration
          {
            old_key: encrypt_key(old_key, master_key),
            new_key: encrypted_new_key
          }
        end
      end
      
      # Environment variable management
      class EnvironmentManager
        def self.get_secret(key, default = nil)
          ENV[key] || default
        end
        
        def self.set_secret(key, value)
          ENV[key] = value
        end
        
        def self.require_secret(key)
          ENV[key] || raise("Missing required environment variable: #{key}")
        end
        
        def self.load_secrets_from_file(file_path)
          return unless File.exist?(file_path)
          
          secrets = YAML.load_file(file_path)
          secrets.each do |key, value|
            ENV[key] = value.to_s
          end
        end
      end
      
      # Usage example
      class ApplicationSecurity
        def self.initialize
          # Load master key from environment
          @master_key = EnvironmentManager.require_secret('MASTER_KEY')
          
          # Generate or load encryption key
          encryption_key = EnvironmentManager.get_secret('ENCRYPTION_KEY')
          
          if encryption_key
            # Decrypt existing key
            @encryption_key = KeyManager.decrypt_key(encryption_key, @master_key)
          else
            # Generate new key
            @encryption_key = KeyManager.generate_key
            encrypted_key = KeyManager.encrypt_key(@encryption_key, @master_key)
            EnvironmentManager.set_secret('ENCRYPTION_KEY', encrypted_key)
          end
        end
        
        def self.encrypt_data(data)
          DataEncryption.encrypt(data, @encryption_key)
        end
        
        def self.decrypt_data(encrypted_data)
          DataEncryption.decrypt(encrypted_data, @encryption_key)
        end
        
        def self.rotate_encryption_key
          # Rotate encryption key
          keys = KeyManager.rotate_key(@encryption_key, @master_key)
          
          # Update environment
          EnvironmentManager.set_secret('ENCRYPTION_KEY', keys[:new_key])
          @encryption_key = KeyManager.decrypt_key(keys[:new_key], @master_key)
          
          # Return old key for data migration
          KeyManager.decrypt_key(keys[:old_key], @master_key)
        end
      end
      
      # Usage example
      begin
        ApplicationSecurity.initialize
        
        # Encrypt data
        data = "Sensitive information"
        encrypted_data = ApplicationSecurity.encrypt_data(data)
        puts "Encrypted data: #{encrypted_data[0..20]}..."
        
        # Decrypt data
        decrypted_data = ApplicationSecurity.decrypt_data(encrypted_data)
        puts "Decrypted data: #{decrypted_data}"
        
        # Rotate key
        old_key = ApplicationSecurity.rotate_encryption_key
        puts "Key rotated successfully"
      rescue => e
        puts "Error: #{e.message}"
      end
    RUBY
    
    puts "\nKey Management Implementation:"
    puts key_management_implementation
  end
  
  # Run data protection examples
  encryption_best_practices
  hashing_techniques
  key_management
end
```

## 🎯 Security Best Practices

### 1. Security Checklist

Comprehensive security checklist:

```ruby
class SecurityChecklist
  def self.security_checklist
    puts "Security Checklist:"
    puts "=" * 50
    
    checklist = [
      {
        category: "Authentication",
        items: [
          "✓ Use strong password hashing (bcrypt, Argon2)",
          "✓ Implement password strength requirements",
          "✓ Use multi-factor authentication",
          "✓ Implement secure session management",
          "✓ Use secure session cookies",
          "✓ Implement session timeout",
          "✓ Regenerate session on login"
        ]
      },
      {
        category: "Authorization",
        items: [
          "✓ Implement role-based access control",
          "✓ Use principle of least privilege",
          "✓ Implement proper authorization checks",
          "✓ Use secure middleware",
          "✓ Validate permissions on every request",
          "✓ Implement proper error handling",
          "✓ Log authorization failures"
        ]
      },
      {
        category: "Input Validation",
        items: [
          "✓ Validate all user input",
          "✓ Use allowlist validation",
          "✓ Sanitize user input",
          "✓ Implement length validation",
          "✓ Use parameterized queries",
          "✓ Validate file uploads",
          "✓ Implement rate limiting"
        ]
      },
      {
        category: "Data Protection",
        items: [
          "✓ Encrypt sensitive data at rest",
          "✓ Use HTTPS for all communications",
          "✓ Implement proper key management",
          "✓ Use strong encryption algorithms",
          "✓ Hash passwords properly",
          "✓ Implement data backup security",
          "✓ Use secure data disposal"
        ]
      },
      {
        category: "Application Security",
        items: [
          "✓ Keep dependencies updated",
          "✓ Use secure coding practices",
          "✓ Implement error handling",
          "✓ Use secure logging",
          "✓ Implement security headers",
          "✓ Use secure cookies",
          "✓ Implement CSRF protection"
        ]
      },
      {
        category: "Infrastructure Security",
        items: [
          "✓ Use secure server configuration",
          "✓ Implement firewall rules",
          "✓ Use secure database configuration",
          "✓ Implement network security",
          "✓ Use secure backup systems",
          "✓ Implement monitoring and alerting",
          "✓ Use secure deployment practices"
        ]
      }
    ]
    
    checklist.each do |category|
      puts "#{category[:category]}:"
      category[:items].each { |item| puts "  #{item}" }
      puts
    end
  end
  
  def self.security_audit
    puts "\nSecurity Audit:"
    puts "=" * 50
    
    audit_procedures = [
      {
        procedure: "Code Review",
        description: "Review code for security vulnerabilities",
        checklist: [
          "Check for SQL injection vulnerabilities",
          "Check for XSS vulnerabilities",
          "Check for authentication bypasses",
          "Check for authorization issues",
          "Check for input validation issues"
        ]
      },
      {
        procedure: "Penetration Testing",
        description: "Test application for security vulnerabilities",
        checklist: [
          "Test authentication bypasses",
          "Test authorization bypasses",
          "Test for injection vulnerabilities",
          "Test for CSRF vulnerabilities",
          "Test for session management issues"
        ]
      },
      {
        procedure: "Dependency Scanning",
        description: "Scan dependencies for known vulnerabilities",
        checklist: [
          "Scan Ruby gems for vulnerabilities",
          "Check for outdated dependencies",
          "Scan for security advisories",
          "Check for vulnerable versions",
          "Update vulnerable dependencies"
        ]
      },
      {
        procedure: "Configuration Review",
        description: "Review system configuration for security issues",
        checklist: [
          "Check database configuration",
          "Check server configuration",
          "Check network configuration",
          "Check application configuration",
          "Check security headers"
        ]
      },
      {
        procedure: "Log Analysis",
        description: "Analyze logs for security events",
        checklist: [
          "Check for authentication failures",
          "Check for authorization failures",
          "Check for suspicious activities",
          "Check for error patterns",
          "Check for unusual patterns"
        ]
      }
    ]
    
    audit_procedures.each do |procedure|
      puts "#{procedure[:procedure]}:"
      puts "  Description: #{procedure[:description]}"
      puts "  Checklist: #{procedure[:checklist].join(', ')}"
      puts
    end
  end
  
  def self.security_monitoring
    puts "\nSecurity Monitoring:"
    puts "=" * 50
    
    monitoring_implementation = <<~RUBY
      class SecurityMonitor
        def self.log_security_event(event_type, details = {})
          log_entry = {
            timestamp: Time.now,
            event_type: event_type,
            details: details,
            ip_address: details[:ip_address],
            user_id: details[:user_id],
            user_agent: details[:user_agent]
          }
          
          # Log to security log
          Rails.logger.info "SECURITY_EVENT: #{log_entry.to_json}"
          
          # Send to security monitoring service
          SecurityMonitoringService.log_event(log_entry)
        end
        
        def self.detect_suspicious_activity(user, request)
          suspicious_indicators = []
          
          # Check for unusual login patterns
          if user.login_attempts > 5
            suspicious_indicators << "Multiple login attempts"
          end
          
          # Check for unusual IP addresses
          if user.last_login_ip != request.remote_ip
            suspicious_indicators << "Unusual IP address"
          end
          
          # Check for unusual user agents
          if user.last_login_user_agent != request.user_agent
            suspicious_indicators << "Unusual user agent"
          end
          
          # Check for rapid requests
          if user.request_rate > 100
            suspicious_indicators << "High request rate"
          end
          
          if suspicious_indicators.any?
            log_security_event('suspicious_activity', {
              user_id: user.id,
              ip_address: request.remote_ip,
              user_agent: request.user_agent,
              indicators: suspicious_indicators
            })
            
            # Trigger security alert
            SecurityAlertService.alert("Suspicious activity detected", suspicious_indicators)
          end
        end
        
        def self.detect_brute_force_attack(ip_address, user_id = nil)
          # Check for multiple failed login attempts
          failed_attempts = FailedLoginAttempt.where(
            ip_address: ip_address,
            created_at: 1.hour.ago..Time.now
          )
          
          if failed_attempts.count > 10
            log_security_event('brute_force_attack', {
              ip_address: ip_address,
              user_id: user_id,
              attempts: failed_attempts.count
            })
            
            # Block IP address
            BlockListService.block_ip(ip_address, duration: 1.hour)
            
            # Send security alert
            SecurityAlertService.alert("Brute force attack detected", {
              ip_address: ip_address,
              attempts: failed_attempts.count
            })
          end
        end
        
        def self.detect_sql_injection(request)
          # Check for SQL injection patterns
          sql_injection_patterns = [
            /('|(union|select|insert|update|delete|drop|create|alter)/i,
            /('|(or|and)\s+\d+\s*=\s*\d+/i,
            /('|(sleep|benchmark|waitfor|delay)/i,
            /('|(exec|system|shell_exec)/i
          ]
          
          suspicious_params = []
          
          request.parameters.each do |key, value|
            if value.is_a?(String)
              sql_injection_patterns.each do |pattern|
                if value.match?(pattern)
                  suspicious_params << "#{key}: #{value}"
                end
              end
            end
          end
          
          if suspicious_params.any?
            log_security_event('sql_injection_attempt', {
              ip_address: request.remote_ip,
              user_agent: request.user_agent,
              suspicious_params: suspicious_params
            })
            
            # Block request
            return false
          end
          
          true
        end
        
        def self.detect_xss_attempt(request)
          # Check for XSS patterns
          xss_patterns = [
            /<script[^>]*>.*?<\/script>/mi,
            /javascript:/i,
            /on\w+\s*=/i,
            /<iframe[^>]*>.*?<\/iframe>/mi
          ]
          
          suspicious_params = []
          
          request.parameters.each do |key, value|
            if value.is_a?(String)
              xss_patterns.each do |pattern|
                if value.match?(pattern)
                  suspicious_params << "#{key}: #{value}"
                end
              end
            end
          end
          
          if suspicious_params.any?
            log_security_event('xss_attempt', {
              ip_address: request.remote_ip,
              user_agent: request.user_agent,
              suspicious_params: suspicious_params
            })
            
            # Sanitize input
            suspicious_params.each do |param|
              key, value = param.split(':')
              request.params[key] = sanitize_html(value)
            end
          end
          
          true
        end
        
        private
        
        def self.sanitize_html(html)
          # Remove dangerous HTML elements
          dangerous_tags = %w[script iframe object embed]
          dangerous_attributes = %w[onload onclick onerror]
          
          dangerous_tags.each do |tag|
            html = html.gsub(/<\s*\/?\s*#{tag}[^>]*>/i, '')
          end
          
          dangerous_attributes.each do |attr|
            html = html.gsub(/\s*#{attr}\s*=\s*["'][^"']*["']/i, '')
          end
          
          html
        end
      end
      
      class SecurityAlertService
        def self.alert(message, details = {})
          # Send alert to security team
          puts "SECURITY ALERT: #{message}"
          puts "Details: #{details}"
          
          # Send to monitoring service
          MonitoringService.alert("security", message, details)
        end
      end
      
      class BlockListService
        def self.block_ip(ip_address, duration: 1.hour)
          # Block IP address in firewall
          puts "Blocking IP: #{ip_address} for #{duration}"
          
          # Add to blocklist
          BlockedIp.create(
            ip_address: ip_address,
            blocked_until: Time.now + duration,
            reason: "Security violation"
          )
        end
        
        def self.is_blocked?(ip_address)
          BlockedIp.where(ip_address: ip_address)
                     .where('blocked_until > ?', Time.now)
                     .exists?
        end
      end
      
      class BlockedIp < ActiveRecord::Base
        # Blocked IP model
      end
    RUBY
    
    puts "Security Monitoring Implementation:"
    puts monitoring_implementation
  end
  
  # Run security checklist examples
  security_checklist
  security_audit
  security_monitoring
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Input Validation**: Implement input validation and sanitization
2. **Password Security**: Implement secure password hashing
3. **Session Security**: Implement secure session management

### Intermediate Exercises

1. **Encryption**: Implement data encryption and decryption
2. **Authorization**: Build role-based access control
3. **Security Monitoring**: Implement security event logging

### Advanced Exercises

1. **Security Audit**: Conduct comprehensive security audit
2. **Penetration Testing**: Implement security testing
3. **Security Framework**: Build comprehensive security framework

---

## 🎯 Summary

Security practices in Ruby provide:

- **Security Fundamentals** - OWASP Top 10 and security principles
- **Input Validation** - Validate and sanitize user input
- **Authentication** - Secure authentication and session management
- **Authorization** - RBAC and ABAC implementation
- **Data Protection** - Encryption, hashing, and key management
- **Security Best Practices** - Comprehensive security guidelines

Master these practices to build secure Ruby applications!
