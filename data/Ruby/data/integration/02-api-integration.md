# API Integration in Ruby
# Comprehensive guide to building and consuming APIs

## 🌐 API Fundamentals

### 1. API Concepts

Core API integration principles:

```ruby
class APIFundamentals
  def self.explain_api_concepts
    puts "API Fundamentals:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Application Programming Interface (API)",
        description: "Set of protocols and tools for building software applications",
        types: ["REST", "GraphQL", "SOAP", "gRPC"],
        benefits: ["Interoperability", "Scalability", "Reusability", "Standardization"],
        components: ["Endpoints", "Methods", "Data formats", "Authentication"]
      },
      {
        concept: "REST (Representational State Transfer)",
        description: "Architectural style for distributed systems",
        principles: ["Stateless", "Client-server", "Cacheable", "Uniform interface"],
        methods: ["GET", "POST", "PUT", "DELETE", "PATCH"],
        characteristics: ["HTTP-based", "Resource-oriented", "Stateless", "Cacheable"]
      },
      {
        concept: "GraphQL",
        description: "Query language for APIs",
        features: ["Single endpoint", "Typed schema", "Introspection", "Real-time"],
        benefits: ["Efficient data fetching", "Strong typing", "Self-documenting"],
        operations: ["Query", "Mutation", "Subscription"]
      },
      {
        concept: "SOAP (Simple Object Access Protocol)",
        description: "Protocol for exchanging structured information",
        features: ["XML-based", "WSDL", "WS-Security", "WS-ReliableMessaging"],
        benefits: ["Standardized", "Platform independent", "Extensible"],
        use_cases: ["Enterprise integration", "Financial services", "Government"]
      },
      {
        concept: "gRPC",
        description: "High-performance RPC framework",
        features: ["Protocol Buffers", "HTTP/2", "Streaming", "Code generation"],
        benefits: ["Performance", "Type safety", "Cross-language", "Streaming"],
        use_cases: ["Microservices", "High-performance systems", "Real-time communication"]
      },
      {
        concept: "API Authentication",
        description: "Methods for securing API access",
        types: ["API Keys", "OAuth 2.0", "JWT", "Basic Auth"],
        considerations: ["Security", "Scalability", "Usability", "Compliance"],
        best_practices: ["HTTPS", "Token expiration", "Rate limiting", "Audit logging"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Components: #{concept[:components].join(', ')}" if concept[:components]
      puts "  Principles: #{concept[:principles].join(', ')}" if concept[:principles]
      puts "  Methods: #{concept[:methods].join(', ')}" if concept[:methods]
      puts "  Characteristics: #{concept[:characteristics].join(', ')}" if concept[:characteristics]
      puts "  Features: #{concept[:features].join(', ')}" if concept[:features]
      puts "  Operations: #{concept[:operations].join(', ')}" if concept[:operations]
      puts "  Use Cases: #{concept[:use_cases].join(', ')}" if concept[:use_cases]
      puts "  Considerations: #{concept[:considerations].join(', ')}" if concept[:considerations]
      puts "  Best Practices: #{concept[:best_practices].join(', ')}" if concept[:best_practices]
      puts
    end
  end
  
  def self.http_methods
    puts "\nHTTP Methods:"
    puts "=" * 50
    
    methods = [
      {
        method: "GET",
        description: "Retrieve resource",
        safe: true,
        idempotent: true,
        cacheable: true,
        examples: ["GET /users", "GET /users/123", "GET /users?active=true"]
      },
      {
        method: "POST",
        description: "Create resource",
        safe: false,
        idempotent: false,
        cacheable: false,
        examples: ["POST /users", "POST /orders", "POST /comments"]
      },
      {
        method: "PUT",
        description: "Replace resource",
        safe: false,
        idempotent: true,
        cacheable: false,
        examples: ["PUT /users/123", "PUT /orders/456", "PUT /settings"]
      },
      {
        method: "PATCH",
        description: "Partial update",
        safe: false,
        idempotent: false,
        cacheable: false,
        examples: ["PATCH /users/123", "PATCH /orders/456", "PATCH /settings"]
      },
      {
        method: "DELETE",
        description: "Delete resource",
        safe: false,
        idempotent: true,
        cacheable: false,
        examples: ["DELETE /users/123", "DELETE /orders/456", "DELETE /cache"]
      },
      {
        method: "HEAD",
        description: "Get headers only",
        safe: true,
        idempotent: true,
        cacheable: true,
        examples: ["HEAD /users/123", "HEAD /status", "HEAD /health"]
      },
      {
        method: "OPTIONS",
        description: "Get allowed methods",
        safe: true,
        idempotent: true,
        cacheable: false,
        examples: ["OPTIONS /users", "OPTIONS /api", "OPTIONS /"]
      }
    ]
    
    methods.each do |method|
      puts "#{method[:method]}:"
      puts "  Description: #{method[:description]}"
      puts "  Safe: #{method[:safe]}"
      puts "  Idempotent: #{method[:idempotent]}"
      puts "  Cacheable: #{method[:cacheable]}"
      puts "  Examples: #{method[:examples].join(', ')}"
      puts
    end
  end
  
  def self.status_codes
    puts "\nHTTP Status Codes:"
    puts "=" * 50
    
    codes = [
      {
        category: "2xx Success",
        codes: [
          { code: "200", meaning: "OK", description: "Request successful" },
          { code: "201", meaning: "Created", description: "Resource created" },
          { code: "202", meaning: "Accepted", description: "Request accepted for processing" },
          { code: "204", meaning: "No Content", description: "Request successful, no content" }
        ]
      },
      {
        category: "3xx Redirection",
        codes: [
          { code: "301", meaning: "Moved Permanently", description: "Resource moved permanently" },
          { code: "302", meaning: "Found", description: "Resource moved temporarily" },
          { code: "304", meaning: "Not Modified", description: "Resource not modified" },
          { code: "307", meaning: "Temporary Redirect", description: "Temporary redirect" }
        ]
      },
      {
        category: "4xx Client Error",
        codes: [
          { code: "400", meaning: "Bad Request", description: "Invalid request" },
          { code: "401", meaning: "Unauthorized", description: "Authentication required" },
          { code: "403", meaning: "Forbidden", description: "Access denied" },
          { code: "404", meaning: "Not Found", description: "Resource not found" },
          { code: "422", meaning: "Unprocessable Entity", description: "Invalid data" }
        ]
      },
      {
        category: "5xx Server Error",
        codes: [
          { code: "500", meaning: "Internal Server Error", description: "Server error" },
          { code: "502", meaning: "Bad Gateway", description: "Gateway error" },
          { code: "503", meaning: "Service Unavailable", description: "Service unavailable" },
          { code: "504", meaning: "Gateway Timeout", description: "Gateway timeout" }
        ]
      }
    ]
    
    codes.each do |category|
      puts "#{category[:category]}:"
      category[:codes].each do |code|
        puts "  #{code[:code]} #{code[:meaning]}: #{code[:description]}"
      end
      puts
    end
  end
  
  def self.api_design_principles
    puts "\nAPI Design Principles:"
    puts "=" * 50
    
    principles = [
      {
        principle: "Consistency",
        description: "Maintain consistent patterns across the API",
        guidelines: [
          "Consistent naming conventions",
          "Uniform response formats",
          "Consistent error handling",
          "Standard status codes",
          "Consistent authentication"
        ],
        benefits: ["Predictability", "Ease of use", "Reduced learning curve"]
      },
      {
        principle: "Simplicity",
        description: "Keep the API simple and intuitive",
        guidelines: [
          "Clear resource names",
          "Intuitive endpoints",
          "Minimal required fields",
          "Clear documentation",
          "Logical grouping"
        ],
        benefits: ["Usability", "Adoption", "Maintenance"]
      },
      {
        principle: "Versioning",
        description: "Plan for API evolution",
        guidelines: [
          "Semantic versioning",
          "Backward compatibility",
          "Deprecation strategy",
          "Migration path",
          "Clear changelog"
        ],
        benefits: ["Stability", "Evolution", "Client confidence"]
      },
      {
        principle: "Security",
        description: "Implement robust security measures",
        guidelines: [
          "HTTPS enforcement",
          "Authentication",
          "Authorization",
          "Input validation",
          "Rate limiting"
        ],
        benefits: ["Protection", "Compliance", "Trust"]
      },
      {
        principle: "Performance",
        description: "Optimize for performance",
        guidelines: [
          "Efficient data structures",
          "Pagination",
          "Caching",
          "Compression",
          "Async operations"
        ],
        benefits: ["Speed", "Scalability", "User experience"]
      },
      {
        principle: "Documentation",
        description: "Provide comprehensive documentation",
        guidelines: [
          "API reference",
          "Examples",
          "Use cases",
          "Error codes",
          "SDKs"
        ],
        benefits: ["Adoption", "Support", "Developer experience"]
      }
    ]
    
    principles.each do |principle|
      puts "#{principle[:principle]}:"
      puts "  Description: #{principle[:description]}"
      puts "  Guidelines: #{principle[:guidelines].join(', ')}"
      puts "  Benefits: #{principle[:benefits].join(', ')}"
      puts
    end
  end
  
  # Run API fundamentals
  explain_api_concepts
  http_methods
  status_codes
  api_design_principles
end
```

### 2. REST API Client

Ruby HTTP client implementation:

```ruby
class RESTAPIClient
  def initialize(base_url, options = {})
    @base_url = base_url
    @headers = options[:headers] || {}
    @timeout = options[:timeout] || 30
    @retry_count = options[:retry_count] || 3
    @retry_delay = options[:retry_delay] || 1
    @authenticator = options[:authenticator]
    @logger = options[:logger]
  end
  
  def get(path, params = {}, headers = {})
    request(:get, path, nil, params, headers)
  end
  
  def post(path, body = nil, headers = {})
    request(:post, path, body, nil, headers)
  end
  
  def put(path, body = nil, headers = {})
    request(:put, path, body, nil, headers)
  end
  
  def patch(path, body = nil, headers = {})
    request(:patch, path, body, nil, headers)
  end
  
  def delete(path, headers = {})
    request(:delete, path, nil, nil, headers)
  end
  
  def self.demonstrate_rest_client
    puts "REST API Client Demonstration:"
    puts "=" * 50
    
    # Create API client
    client = RESTAPIClient.new('https://api.example.com/v1', {
      headers: {
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
      },
      timeout: 10,
      retry_count: 2
    })
    
    # GET request
    puts "GET Request:"
    response = client.get('/users', { page: 1, limit: 10 })
    puts "Status: #{response[:status]}"
    puts "Data: #{response[:data]}"
    
    # POST request
    puts "\nPOST Request:"
    user_data = { name: 'John Doe', email: 'john@example.com' }
    response = client.post('/users', user_data)
    puts "Status: #{response[:status]}"
    puts "Data: #{response[:data]}"
    
    # PUT request
    puts "\nPUT Request:"
    update_data = { name: 'John Smith' }
    response = client.put('/users/123', update_data)
    puts "Status: #{response[:status]}"
    puts "Data: #{response[:data]}"
    
    # DELETE request
    puts "\nDELETE Request:"
    response = client.delete('/users/123')
    puts "Status: #{response[:status]}"
    puts "Data: #{response[:data]}"
    
    puts "\nREST API Client Features:"
    puts "- Multiple HTTP methods"
    puts "- Request/response handling"
    puts "- Error handling and retries"
    puts "- Authentication support"
    puts "- Logging and monitoring"
    puts "- Timeout management"
  end
  
  private
  
  def request(method, path, body = nil, params = {}, headers = {})
    url = build_url(path, params)
    request_headers = build_headers(headers)
    request_body = build_body(body)
    
    log_request(method, url, request_headers, request_body)
    
    response = execute_request(method, url, request_headers, request_body)
    
    log_response(response)
    
    response
  end
  
  def build_url(path, params)
    url = "#{@base_url}#{path}"
    
    if params.any?
      query_string = params.map { |k, v| "#{k}=#{CGI.escape(v.to_s)}" }.join('&')
      url += "?#{query_string}"
    end
    
    url
  end
  
  def build_headers(headers)
    merged_headers = @headers.merge(headers)
    
    if @authenticator
      auth_headers = @authenticator.authenticate
      merged_headers.merge!(auth_headers)
    end
    
    merged_headers
  end
  
  def build_body(body)
    return nil unless body
    
    case body
    when Hash
      body.to_json
    when String
      body
    else
      body.to_s
    end
  end
  
  def execute_request(method, url, headers, body)
    attempts = 0
    
    begin
      attempts += 1
      
      # Simulate HTTP request
      response = simulate_http_request(method, url, headers, body)
      
      # Check for retryable errors
      if should_retry?(response) && attempts < @retry_count
        log_retry(attempts, response)
        sleep(@retry_delay * attempts)
        return execute_request(method, url, headers, body)
      end
      
      response
      
    rescue => e
      if attempts < @retry_count
        log_retry(attempts, e)
        sleep(@retry_delay * attempts)
        retry
      else
        log_error(e)
        raise
      end
    end
  end
  
  def simulate_http_request(method, url, headers, body)
    # Simulate different responses based on URL and method
    case url
    when /\/users$/
      case method
      when :get
        {
          status: 200,
          headers: { 'Content-Type' => 'application/json' },
          data: {
            users: [
              { id: 1, name: 'John Doe', email: 'john@example.com' },
              { id: 2, name: 'Jane Smith', email: 'jane@example.com' }
            ],
            total: 2,
            page: 1
          }
        }
      when :post
        {
          status: 201,
          headers: { 'Content-Type' => 'application/json' },
          data: { id: 3, name: 'John Doe', email: 'john@example.com', created_at: Time.now }
        }
      end
    when /\/users\/\d+$/
      case method
      when :get
        {
          status: 200,
          headers: { 'Content-Type' => 'application/json' },
          data: { id: 123, name: 'John Doe', email: 'john@example.com' }
        }
      when :put
        {
          status: 200,
          headers: { 'Content-Type' => 'application/json' },
          data: { id: 123, name: 'John Smith', email: 'john@example.com', updated_at: Time.now }
        }
      when :delete
        {
          status: 204,
          headers: {},
          data: nil
        }
      end
    else
      {
        status: 404,
        headers: { 'Content-Type' => 'application/json' },
        data: { error: 'Not Found' }
      }
    end
  end
  
  def should_retry?(response)
    response[:status] >= 500 || response[:status] == 429
  end
  
  def log_request(method, url, headers, body)
    return unless @logger
    
    @logger.info("API Request", {
      method: method.upcase,
      url: url,
      headers: headers,
      body: body
    })
  end
  
  def log_response(response)
    return unless @logger
    
    @logger.info("API Response", {
      status: response[:status],
      headers: response[:headers],
      data: response[:data]
    })
  end
  
  def log_retry(attempt, error_or_response)
    return unless @logger
    
    if error_or_response.is_a?(Hash)
      @logger.warn("API Retry", {
        attempt: attempt,
        status: error_or_response[:status],
        error: error_or_response[:data]
      })
    else
      @logger.warn("API Retry", {
        attempt: attempt,
        error: error_or_response.message
      })
    end
  end
  
  def log_error(error)
    return unless @logger
    
    @logger.error("API Error", {
      error: error.message,
      backtrace: error.backtrace
    })
  end
end

class APIAuthenticator
  def initialize(type, credentials)
    @type = type
    @credentials = credentials
  end
  
  def authenticate
    case @type
    when :api_key
      { 'X-API-Key' => @credentials[:api_key] }
    when :bearer
      { 'Authorization' => "Bearer #{@credentials[:token]}" }
    when :basic
      encoded = Base64.strict_encode64("#{@credentials[:username]}:#{@credentials[:password]}")
      { 'Authorization' => "Basic #{encoded}" }
    when :oauth2
      { 'Authorization' => "Bearer #{@credentials[:access_token]}" }
    else
      {}
    end
  end
  
  def self.demonstrate_authentication
    puts "API Authentication Demonstration:"
    puts "=" * 50
    
    # API Key authentication
    api_key_auth = APIAuthenticator.new(:api_key, { api_key: 'secret123' })
    puts "API Key: #{api_key_auth.authenticate}"
    
    # Bearer token authentication
    bearer_auth = APIAuthenticator.new(:bearer, { token: 'jwt123' })
    puts "Bearer: #{bearer_auth.authenticate}"
    
    # Basic authentication
    basic_auth = APIAuthenticator.new(:basic, { username: 'user', password: 'pass' })
    puts "Basic: #{basic_auth.authenticate}"
    
    # OAuth2 authentication
    oauth2_auth = APIAuthenticator.new(:oauth2, { access_token: 'oauth123' })
    puts "OAuth2: #{oauth2_auth.authenticate}"
    
    puts "\nAuthentication Features:"
    puts "- Multiple authentication types"
    puts "- API key authentication"
    puts "- Bearer token support"
    puts "- Basic authentication"
    puts "- OAuth2 support"
    puts "- Flexible credential management"
  end
end
```

## 🔗 API Integration Patterns

### 3. Integration Patterns

Common API integration patterns:

```ruby
class APIIntegrationPatterns
  def self.demonstrate_patterns
    puts "API Integration Patterns:"
    puts "=" * 50
    
    # 1. Circuit Breaker Pattern
    demonstrate_circuit_breaker
    
    # 2. Retry Pattern
    demonstrate_retry_pattern
    
    # 3. Rate Limiting Pattern
    demonstrate_rate_limiting
    
    # 4. Caching Pattern
    demonstrate_caching
    
    # 5. Bulk Operations Pattern
    demonstrate_bulk_operations
    
    # 6. Pagination Pattern
    demonstrate_pagination
  end
  
  def self.demonstrate_circuit_breaker
    puts "\n1. Circuit Breaker Pattern:"
    puts "=" * 30
    
    circuit_breaker = CircuitBreaker.new('external-api', {
      failure_threshold: 3,
      recovery_timeout: 60,
      expected_exceptions: [TimeoutError, ConnectionError]
    })
    
    # Simulate API calls
    5.times do |i|
      begin
        result = circuit_breaker.call do
          simulate_api_call(i)
        end
        puts "Call #{i + 1}: Success - #{result}"
      rescue => e
        puts "Call #{i + 1}: Failed - #{e.message}"
      end
    end
    
    puts "\nCircuit Breaker Features:"
    puts "- Failure threshold detection"
    puts "- Automatic circuit opening"
    puts "- Recovery timeout"
    puts "- Exception filtering"
    puts "- State management"
  end
  
  def self.demonstrate_retry_pattern
    puts "\n2. Retry Pattern:"
    puts "=" * 30
    
    retry_policy = RetryPolicy.new(
      max_attempts: 3,
      base_delay: 1,
      max_delay: 10,
      backoff_multiplier: 2,
      retry_exceptions: [TimeoutError, ConnectionError]
    )
    
    # Simulate API call with retries
    begin
      result = retry_policy.execute do
        simulate_failing_api_call
      end
      puts "Retry: Success - #{result}"
    rescue => e
      puts "Retry: Failed after all attempts - #{e.message}"
    end
    
    puts "\nRetry Pattern Features:"
    puts "- Configurable retry attempts"
    puts "- Exponential backoff"
    puts "- Exception filtering"
    puts "- Delay management"
    puts "- Error handling"
  end
  
  def self.demonstrate_rate_limiting
    puts "\n3. Rate Limiting Pattern:"
    puts "=" * 30
    
    rate_limiter = RateLimiter.new(10, 60) # 10 requests per minute
    
    # Simulate API calls with rate limiting
    15.times do |i|
      if rate_limiter.allow_request?
        puts "Request #{i + 1}: Allowed"
        simulate_api_call(i)
      else
        puts "Request #{i + 1}: Rate limited"
      end
    end
    
    puts "\nRate Limiting Features:"
    puts "- Request rate limiting"
    puts "- Time window management"
    puts "- Sliding window algorithm"
    puts "- Request rejection"
    puts "- Rate tracking"
  end
  
  def self.demonstrate_caching
    puts "\n4. Caching Pattern:"
    puts "=" * 30
    
    cache = APICache.new(ttl: 300) # 5 minutes TTL
    
    # First call - cache miss
    puts "First call:"
    result1 = cache.get_or_set('user:123') do
      simulate_api_call('user:123')
    end
    puts "Result: #{result1}"
    
    # Second call - cache hit
    puts "\nSecond call:"
    result2 = cache.get_or_set('user:123') do
      simulate_api_call('user:123')
    end
    puts "Result: #{result2}"
    
    puts "\nCaching Features:"
    puts "- TTL-based expiration"
    puts "- Cache hit/miss tracking"
    puts "- Lazy loading"
    puts "- Thread safety"
    puts "- Memory management"
  end
  
  def self.demonstrate_bulk_operations
    puts "\n5. Bulk Operations Pattern:"
    puts "=" * 30
    
    bulk_processor = BulkProcessor.new(batch_size: 5, max_concurrent: 3)
    
    # Simulate bulk operation
    items = (1..15).map { |i| "item_#{i}" }
    
    results = bulk_processor.process(items) do |batch|
      simulate_bulk_api_call(batch)
    end
    
    puts "Bulk operation completed with #{results.length} results"
    
    puts "\nBulk Operations Features:"
    puts "- Batch processing"
    puts "- Concurrent execution"
    puts "- Error handling"
    puts "- Progress tracking"
    puts "- Resource management"
  end
  
  def self.demonstrate_pagination
    puts "\n6. Pagination Pattern:"
    puts "=" * 30
    
    paginator = Paginator.new(page_size: 5)
    
    # Simulate paginated API call
    all_items = []
    
    paginator.each_page do |page|
      items = simulate_paginated_api_call(page)
      all_items.concat(items)
      puts "Page #{page}: Retrieved #{items.length} items"
      
      break if items.length < page.page_size # Last page
    end
    
    puts "Total items retrieved: #{all_items.length}"
    
    puts "\nPagination Features:"
    puts "- Page-based pagination"
    puts "- Cursor-based pagination"
    puts "- Automatic page handling"
    puts "- Data aggregation"
    puts "- Memory efficiency"
  end
  
  private
  
  def self.simulate_api_call(identifier)
    sleep(0.1) # Simulate network latency
    "Data for #{identifier}"
  end
  
  def self.simulate_failing_api_call
    sleep(0.1)
    raise TimeoutError, "Request timeout" if rand < 0.7
    "Success"
  end
  
  def self.simulate_bulk_api_call(batch)
    sleep(0.2)
    batch.map { |item| "Processed #{item}" }
  end
  
  def self.simulate_paginated_api_call(page)
    sleep(0.1)
    start_index = (page - 1) * 5 + 1
    end_index = [start_index + 4, 20].min
    (start_index..end_index).to_a.map { |i| "item_#{i}" }
  end
end

class CircuitBreaker
  def initialize(name, options = {})
    @name = name
    @failure_threshold = options[:failure_threshold] || 5
    @recovery_timeout = options[:recovery_timeout] || 60
    @expected_exceptions = options[:expected_exceptions] || [StandardError]
    @state = :closed
    @failure_count = 0
    @last_failure_time = nil
    @mutex = Mutex.new
  end
  
  def call(&block)
    @mutex.synchronize do
      case @state
      when :open
        if Time.now - @last_failure_time > @recovery_timeout
          @state = :half_open
        else
          raise CircuitBreakerOpenError, "Circuit breaker #{@name} is open"
        end
      end
    end
    
    begin
      result = yield
      reset
      result
    rescue => e
      record_failure(e)
      raise
    end
  end
  
  private
  
  def record_failure(error)
    @mutex.synchronize do
      @failure_count += 1
      @last_failure_time = Time.now
      
      if @failure_count >= @failure_threshold
        @state = :open
      end
    end
  end
  
  def reset
    @mutex.synchronize do
      @failure_count = 0
      @state = :closed
    end
  end
end

class RetryPolicy
  def initialize(options = {})
    @max_attempts = options[:max_attempts] || 3
    @base_delay = options[:base_delay] || 1
    @max_delay = options[:max_delay] || 30
    @backoff_multiplier = options[:backoff_multiplier] || 2
    @retry_exceptions = options[:retry_exceptions] || [StandardError]
  end
  
  def execute(&block)
    attempts = 0
    
    begin
      attempts += 1
      result = yield
      result
    rescue => e
      if should_retry?(e, attempts)
        delay = calculate_delay(attempts)
        sleep(delay)
        retry
      else
        raise
      end
    end
  end
  
  private
  
  def should_retry?(error, attempts)
    return false if attempts >= @max_attempts
    @retry_exceptions.any? { |exception| error.is_a?(exception) }
  end
  
  def calculate_delay(attempts)
    delay = @base_delay * (@backoff_multiplier ** (attempts - 1))
    [delay, @max_delay].min
  end
end

class RateLimiter
  def initialize(max_requests, time_window)
    @max_requests = max_requests
    @time_window = time_window
    @requests = []
    @mutex = Mutex.new
  end
  
  def allow_request?
    @mutex.synchronize do
      now = Time.now
      @requests.reject! { |time| now - time > @time_window }
      
      if @requests.length < @max_requests
        @requests << now
        true
      else
        false
      end
    end
  end
end

class APICache
  def initialize(ttl: 300)
    @cache = {}
    @ttl = ttl
    @mutex = Mutex.new
  end
  
  def get(key)
    @mutex.synchronize do
      entry = @cache[key]
      return nil unless entry
      return nil if Time.now - entry[:time] > @ttl
      entry[:value]
    end
  end
  
  def set(key, value)
    @mutex.synchronize do
      @cache[key] = {
        value: value,
        time: Time.now
      }
    end
  end
  
  def get_or_set(key, &block)
    value = get(key)
    return value if value
    
    value = yield
    set(key, value)
    value
  end
end

class BulkProcessor
  def initialize(batch_size: 10, max_concurrent: 5)
    @batch_size = batch_size
    @max_concurrent = max_concurrent
  end
  
  def process(items, &block)
    batches = items.each_slice(@batch_size).to_a
    results = []
    semaphore = Mutex.new
    
    threads = batches.map.with_index do |batch, index|
      Thread.new do
        if index < @max_concurrent
          batch_results = yield(batch)
          semaphore.synchronize { results.concat(batch_results) }
        else
          # Wait for a thread to finish
          sleep(0.1)
          retry
        end
      end
    end
    
    threads.each(&:join)
    results
  end
end

class Paginator
  def initialize(page_size: 10, strategy: :offset)
    @page_size = page_size
    @strategy = strategy
    @current_page = 1
  end
  
  def each_page(&block)
    loop do
      page_info = PageInfo.new(@current_page, @page_size, @strategy)
      yield(page_info)
      @current_page += 1
    end
  end
end

class PageInfo
  def initialize(number, size, strategy)
    @number = number
    @size = size
    @strategy = strategy
  end
  
  attr_reader :number, :size, :strategy
  
  def offset
    @strategy == :offset ? (@number - 1) * @size : nil
  end
  
  def cursor
    @strategy == :cursor ? "cursor_#{@number}" : nil
  end
end

class CircuitBreakerOpenError < StandardError; end
```

## 📡 GraphQL Integration

### 4. GraphQL Client

GraphQL query implementation:

```ruby
class GraphQLClient
  def initialize(endpoint_url, options = {})
    @endpoint_url = endpoint_url
    @headers = options[:headers] || {}
    @timeout = options[:timeout] || 30
    @logger = options[:logger]
  end
  
  def query(query_string, variables = {}, operation_name = nil)
    execute_request('query', query_string, variables, operation_name)
  end
  
  def mutation(mutation_string, variables = {}, operation_name = nil)
    execute_request('mutation', mutation_string, variables, operation_name)
  end
  
  def subscription(subscription_string, variables = {}, operation_name = nil, &block)
    execute_subscription(subscription_string, variables, operation_name, &block)
  end
  
  def introspect
    introspection_query = <<~GRAPHQL
      query IntrospectionQuery {
        __schema {
          types {
            name
            kind
            description
            fields {
              name
              type {
                name
                kind
              }
            }
          }
        }
      }
    GRAPHQL
    
    query(introspection_query)
  end
  
  def self.demonstrate_graphql_client
    puts "GraphQL Client Demonstration:"
    puts "=" * 50
    
    client = GraphQLClient.new('https://api.example.com/graphql', {
      headers: {
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer token123'
      }
    })
    
    # Query example
    puts "Query Example:"
    query_string = <<~GRAPHQL
      query GetUser($id: ID!) {
        user(id: $id) {
          id
          name
          email
          posts {
            id
            title
            content
          }
        }
      }
    GRAPHQL
    
    variables = { id: "123" }
    result = client.query(query_string, variables)
    puts "Result: #{result}"
    
    # Mutation example
    puts "\nMutation Example:"
    mutation_string = <<~GRAPHQL
      mutation CreateUser($input: CreateUserInput!) {
        createUser(input: $input) {
          id
          name
          email
        }
      }
    GRAPHQL
    
    variables = {
      input: {
        name: "John Doe",
        email: "john@example.com"
      }
    }
    result = client.mutation(mutation_string, variables)
    puts "Result: #{result}"
    
    # Introspection example
    puts "\nIntrospection Example:"
    schema = client.introspect
    puts "Schema types: #{schema[:data][:__schema][:types].length}"
    
    puts "\nGraphQL Client Features:"
    puts "- Query execution"
    puts "- Mutation execution"
    puts "- Subscription support"
    puts "- Schema introspection"
    puts "- Variable support"
    puts "- Error handling"
  end
  
  private
  
  def execute_request(operation_type, query_string, variables, operation_name)
    request_body = {
      query: query_string,
      variables: variables,
      operationName: operation_name
    }.compact
    
    log_request(operation_type, request_body)
    
    response = simulate_graphql_request(request_body)
    
    log_response(response)
    
    response
  end
  
  def execute_subscription(subscription_string, variables, operation_name, &block)
    # Simulate subscription
    puts "Starting subscription: #{operation_name}"
    
    Thread.new do
      loop do
        result = simulate_graphql_request({
          query: subscription_string,
          variables: variables,
          operationName: operation_name
        })
        
        yield(result) if block_given?
        sleep(1) # Simulate real-time updates
      end
    end
  end
  
  def simulate_graphql_request(request_body)
    # Simulate GraphQL response
    case request_body[:query]
    when /GetUser/
      {
        data: {
          user: {
            id: request_body[:variables][:id],
            name: "John Doe",
            email: "john@example.com",
            posts: [
              { id: "1", title: "First Post", content: "Hello World" },
              { id: "2", title: "Second Post", content: "GraphQL Rocks" }
            ]
          }
        }
      }
    when /CreateUser/
      {
        data: {
          createUser: {
            id: "456",
            name: request_body[:variables][:input][:name],
            email: request_body[:variables][:input][:email]
          }
        }
      }
    when /__schema/
      {
        data: {
          __schema: {
            types: [
              { name: "User", kind: "OBJECT", description: "User type" },
              { name: "Post", kind: "OBJECT", description: "Post type" },
              { name: "String", kind: "SCALAR", description: "String type" }
            ]
          }
        }
      }
    else
      {
        errors: [{ message: "Unknown query" }]
      }
    end
  end
  
  def log_request(operation_type, request_body)
    return unless @logger
    
    @logger.info("GraphQL Request", {
      operation: operation_type,
      query: request_body[:query],
      variables: request_body[:variables],
      operation_name: request_body[:operation_name]
    })
  end
  
  def log_response(response)
    return unless @logger
    
    @logger.info("GraphQL Response", {
      data: response[:data],
      errors: response[:errors]
    })
  end
end

class GraphQLQueryBuilder
  def initialize
    @fields = []
    @arguments = {}
    @directives = []
  end
  
  def field(name, alias_name = nil, arguments = {}, &block)
    field_builder = FieldBuilder.new(name, alias_name, arguments)
    field_builder.instance_eval(&block) if block_given?
    @fields << field_builder
    self
  end
  
  def argument(name, value)
    @arguments[name] = value
    self
  end
  
  def directive(name, arguments = {})
    @directives << { name: name, arguments: arguments }
    self
  end
  
  def build
    query_parts = []
    
    @fields.each do |field|
      query_parts << field.build
    end
    
    query_parts.join("\n  ")
  end
  
  def self.demonstrate_query_builder
    puts "GraphQL Query Builder Demonstration:"
    puts "=" * 50
    
    # Build a complex query
    query = GraphQLQueryBuilder.new
      .field('user', nil, { id: '$id' }) do
        field('id')
        field('name', 'fullName')
        field('email')
        field('posts') do
          field('id')
          field('title')
          field('content')
          field('createdAt', nil, {}, &:field('timestamp'))
        end
      end
      .argument('id', '123')
    
    built_query = <<~GRAPHQL
      query GetUser($id: ID!) {
        user(id: $id) {
          id
          fullName: name
          email
          posts {
            id
            title
            content
            createdAt {
              timestamp
            }
          }
        }
      }
    GRAPHQL
    
    puts "Built Query:"
    puts built_query
    
    puts "\nQuery Builder Features:"
    puts "- Field selection"
    puts "- Alias support"
    puts "- Arguments"
    puts "- Nested fields"
    puts "- Directives"
    puts "- Variable binding"
  end
end

class FieldBuilder
  def initialize(name, alias_name = nil, arguments = {})
    @name = name
    @alias_name = alias_name
    @arguments = arguments
    @fields = []
  end
  
  def field(name, alias_name = nil, arguments = {}, &block)
    field_builder = FieldBuilder.new(name, alias_name, arguments)
    field_builder.instance_eval(&block) if block_given?
    @fields << field_builder
    self
  end
  
  def build
    parts = []
    
    if @alias_name
      parts << "#{@alias_name}: #{@name}"
    else
      parts << @name
    end
    
    if @arguments.any?
      args = @arguments.map { |k, v| "#{k}: #{format_value(v)}" }.join(', ')
      parts << "(#{args})"
    end
    
    if @fields.any?
      fields = @fields.map(&:build).join("\n    ")
      parts << " {\n    #{fields}\n  }"
    end
    
    parts.join
  end
  
  private
  
  def format_value(value)
    case value
    when String
      "\"#{value}\""
    when Symbol
      "$#{value}"
    else
      value.to_s
    end
  end
end
```

## 🔐 API Security

### 5. API Security Implementation

Security best practices for APIs:

```ruby
class APISecurity
  def self.demonstrate_security
    puts "API Security Demonstration:"
    puts "=" * 50
    
    # 1. API Key Authentication
    demonstrate_api_key_auth
    
    # 2. JWT Authentication
    demonstrate_jwt_auth
    
    # 3. OAuth2 Flow
    demonstrate_oauth2_flow
    
    # 4. Rate Limiting
    demonstrate_security_rate_limiting
    
    # 5. Input Validation
    demonstrate_input_validation
    
    # 6. HTTPS Enforcement
    demonstrate_https_enforcement
  end
  
  def self.demonstrate_api_key_auth
    puts "\n1. API Key Authentication:"
    puts "=" * 30
    
    authenticator = APIKeyAuthenticator.new
    
    # Generate API key
    api_key = authenticator.generate_key('user123', ['read', 'write'])
    puts "Generated API Key: #{api_key[:key]}"
    puts "Expires: #{api_key[:expires_at]}"
    
    # Validate API key
    validation = authenticator.validate_key(api_key[:key])
    puts "Validation: #{validation[:valid]}"
    puts "User ID: #{validation[:user_id]}" if validation[:valid]
    puts "Permissions: #{validation[:permissions]}" if validation[:valid]
    
    # Revoke API key
    authenticator.revoke_key(api_key[:key])
    puts "API key revoked"
    
    validation_after_revoke = authenticator.validate_key(api_key[:key])
    puts "Validation after revoke: #{validation_after_revoke[:valid]}"
  end
  
  def self.demonstrate_jwt_auth
    puts "\n2. JWT Authentication:"
    puts "=" * 30
    
    jwt_auth = JWTAuthenticator.new('secret123')
    
    # Create JWT token
    payload = { user_id: '123', email: 'user@example.com', role: 'admin' }
    token = jwt_auth.create_token(payload, 3600) # 1 hour
    puts "Created JWT: #{token[0..50]}..."
    
    # Validate JWT token
    validation = jwt_auth.validate_token(token)
    puts "Validation: #{validation[:valid]}"
    puts "User ID: #{validation[:payload][:user_id]}" if validation[:valid]
    puts "Role: #{validation[:payload][:role]}" if validation[:valid]
    
    # Refresh token
    new_token = jwt_auth.refresh_token(token)
    puts "Refreshed JWT: #{new_token[0..50]}..."
  end
  
  def self.demonstrate_oauth2_flow
    puts "\n3. OAuth2 Flow:"
    puts "=" * 30
    
    oauth2 = OAuth2Provider.new('myapp', 'secret123')
    
    # Authorization code flow
    puts "Authorization Code Flow:"
    
    # Step 1: Get authorization code
    auth_url = oauth2.authorization_url('read write', 'http://localhost:3000/callback')
    puts "Authorization URL: #{auth_url}"
    
    # Step 2: Exchange code for token
    auth_code = 'auth123'
    token_response = oauth2.exchange_code_for_token(auth_code, 'http://localhost:3000/callback')
    puts "Access Token: #{token_response[:access_token][0..20]}..."
    puts "Refresh Token: #{token_response[:refresh_token][0..20]}..."
    puts "Expires In: #{token_response[:expires_in]}"
    
    # Step 3: Use access token
    resource = oauth2.get_resource(token_response[:access_token], '/api/user')
    puts "Resource: #{resource}"
    
    # Step 4: Refresh token
    new_token = oauth2.refresh_access_token(token_response[:refresh_token])
    puts "New Access Token: #{new_token[:access_token][0..20]}..."
  end
  
  def self.demonstrate_security_rate_limiting
    puts "\n4. Security Rate Limiting:"
    puts "=" * 30
    
    rate_limiter = SecurityRateLimiter.new(100, 3600, 10) # 100 requests/hour, 10 burst
    
    # Simulate requests
    15.times do |i|
      client_ip = "192.168.1.#{i % 5 + 1}"
      
      if rate_limiter.allow_request?(client_ip)
        puts "Request #{i + 1} from #{client_ip}: Allowed"
      else
        puts "Request #{i + 1} from #{client_ip}: Rate limited"
      end
    end
    
    puts "\nRate Limiting Features:"
    puts "- Per-client rate limiting"
    puts "- Sliding window algorithm"
    puts "- Burst capacity"
    puts "- Automatic reset"
    puts "- IP-based tracking"
  end
  
  def self.demonstrate_input_validation
    puts "\n5. Input Validation:"
    puts "=" * 30
    
    validator = APIInputValidator.new
    
    # Valid input
    valid_input = { name: 'John Doe', email: 'john@example.com', age: 25 }
    validation = validator.validate_user_input(valid_input)
    puts "Valid input validation: #{validation[:valid]}"
    
    # Invalid input
    invalid_input = { name: '', email: 'invalid-email', age: -5 }
    validation = validator.validate_user_input(invalid_input)
    puts "Invalid input validation: #{validation[:valid]}"
    puts "Errors: #{validation[:errors].join(', ')}"
    
    # SQL injection prevention
    malicious_input = "'; DROP TABLE users; --"
    sanitized = validator.sanitize_input(malicious_input)
    puts "Sanitized input: #{sanitized}"
    
    # XSS prevention
    xss_input = "<script>alert('xss')</script>"
    sanitized = validator.sanitize_html(xss_input)
    puts "Sanitized HTML: #{sanitized}"
  end
  
  def self.demonstrate_https_enforcement
    puts "\n6. HTTPS Enforcement:"
    puts "=" * 30
    
    https_enforcer = HTTPSEnforcer.new
    
    # Test HTTP request
    http_request = OpenStruct.new(scheme: 'http', host: 'example.com', path: '/api/users')
    response = https_enforcer.enforce_https(http_request)
    puts "HTTP request redirected to: #{response[:location]}"
    
    # Test HTTPS request
    https_request = OpenStruct.new(scheme: 'https', host: 'example.com', path: '/api/users')
    response = https_enforcer.enforce_https(https_request)
    puts "HTTPS request: #{response[:allowed] ? 'Allowed' : 'Blocked'}"
    
    # Test HSTS headers
    hsts_headers = https_enforcer.get_hsts_headers
    puts "HSTS Headers: #{hsts_headers}"
    
    puts "\nHTTPS Enforcement Features:"
    puts "- Automatic HTTPS redirect"
    puts "- HSTS header support"
    puts "- Secure cookie handling"
    puts "- Certificate validation"
    puts "- Mixed content prevention"
  end
end

class APIKeyAuthenticator
  def initialize
    @keys = {}
    @mutex = Mutex.new
  end
  
  def generate_key(user_id, permissions = [], expires_in = nil)
    key = SecureRandom.hex(32)
    expires_at = expires_in ? Time.now + expires_in : nil
    
    @mutex.synchronize do
      @keys[key] = {
        user_id: user_id,
        permissions: permissions,
        created_at: Time.now,
        expires_at: expires_at,
        revoked: false
      }
    end
    
    { key: key, expires_at: expires_at }
  end
  
  def validate_key(api_key)
    @mutex.synchronize do
      key_data = @keys[api_key]
      return { valid: false } unless key_data
      
      return { valid: false } if key_data[:revoked]
      return { valid: false } if key_data[:expires_at] && Time.now > key_data[:expires_at]
      
      {
        valid: true,
        user_id: key_data[:user_id],
        permissions: key_data[:permissions]
      }
    end
  end
  
  def revoke_key(api_key)
    @mutex.synchronize do
      @keys[api_key][:revoked] = true if @keys[api_key]
    end
  end
end

class JWTAuthenticator
  def initialize(secret)
    @secret = secret
  end
  
  def create_token(payload, expires_in = 3600)
    exp = Time.now.to_i + expires_in
    jwt_payload = payload.merge(exp: exp)
    
    # Simplified JWT creation (in real implementation, use proper JWT library)
    header = { alg: 'HS256', typ: 'JWT' }
    encoded_header = Base64.urlsafe_encode64(header.to_json)
    encoded_payload = Base64.urlsafe_encode64(jwt_payload.to_json)
    signature = OpenSSL::HMAC.hexdigest('sha256', @secret, "#{encoded_header}.#{encoded_payload}")
    
    "#{encoded_header}.#{encoded_payload}.#{signature}"
  end
  
  def validate_token(token)
    parts = token.split('.')
    return { valid: false } if parts.length != 3
    
    begin
      header = JSON.parse(Base64.urlsafe_decode64(parts[0]))
      payload = JSON.parse(Base64.urlsafe_decode64(parts[1]))
      signature = parts[2]
      
      # Verify signature
      expected_signature = OpenSSL::HMAC.hexdigest('sha256', @secret, "#{parts[0]}.#{parts[1]}")
      return { valid: false } unless signature == expected_signature
      
      # Check expiration
      return { valid: false } if payload['exp'] && Time.now.to_i > payload['exp']
      
      { valid: true, payload: payload }
    rescue JSON::ParserError
      { valid: false }
    end
  end
  
  def refresh_token(token)
    validation = validate_token(token)
    return nil unless validation[:valid]
    
    payload = validation[:payload].dup
    payload.delete('exp')
    
    create_token(payload)
  end
end

class OAuth2Provider
  def initialize(client_id, client_secret)
    @client_id = client_id
    @client_secret = client_secret
    @authorization_codes = {}
    @access_tokens = {}
    @refresh_tokens = {}
  end
  
  def authorization_url(scopes, redirect_uri)
    auth_code = SecureRandom.hex(16)
    @authorization_codes[auth_code] = {
      scopes: scopes,
      redirect_uri: redirect_uri,
      created_at: Time.now
    }
    
    "https://auth.example.com/authorize?client_id=#{@client_id}&response_type=code&scope=#{scopes.join('+')}&redirect_uri=#{redirect_uri}&code=#{auth_code}"
  end
  
  def exchange_code_for_token(code, redirect_uri)
    auth_data = @authorization_codes[code]
    return nil unless auth_data
    return nil unless auth_data[:redirect_uri] == redirect_uri
    
    access_token = SecureRandom.hex(32)
    refresh_token = SecureRandom.hex(32)
    expires_in = 3600
    
    @access_tokens[access_token] = {
      scopes: auth_data[:scopes],
      created_at: Time.now,
      expires_at: Time.now + expires_in
    }
    
    @refresh_tokens[refresh_token] = {
      access_token: access_token,
      created_at: Time.now
    }
    
    {
      access_token: access_token,
      refresh_token: refresh_token,
      expires_in: expires_in,
      token_type: 'Bearer'
    }
  end
  
  def get_resource(access_token, resource_path)
    token_data = @access_tokens[access_token]
    return nil unless token_data
    return nil if Time.now > token_data[:expires_at]
    
    # Simulate resource access
    case resource_path
    when '/api/user'
      { id: 123, name: 'John Doe', email: 'john@example.com' }
    else
      { error: 'Resource not found' }
    end
  end
  
  def refresh_access_token(refresh_token)
    token_data = @refresh_tokens[refresh_token]
    return nil unless token_data
    
    new_access_token = SecureRandom.hex(32)
    expires_in = 3600
    
    @access_tokens[new_access_token] = {
      scopes: @access_tokens[token_data[:access_token]][:scopes],
      created_at: Time.now,
      expires_at: Time.now + expires_in
    }
    
    {
      access_token: new_access_token,
      expires_in: expires_in,
      token_type: 'Bearer'
    }
  end
end

class SecurityRateLimiter
  def initialize(max_requests, time_window, burst_capacity)
    @max_requests = max_requests
    @time_window = time_window
    @burst_capacity = burst_capacity
    @clients = {}
    @mutex = Mutex.new
  end
  
  def allow_request?(client_id)
    @mutex.synchronize do
      now = Time.now
      client_data = @clients[client_id] ||= { requests: [], tokens: @burst_capacity }
      
      # Clean old requests
      client_data[:requests].reject! { |time| now - time > @time_window }
      
      # Check if allowed
      if client_data[:requests].length < @max_requests && client_data[:tokens] > 0
        client_data[:requests] << now
        client_data[:tokens] -= 1
        true
      else
        false
      end
    end
  end
end

class APIInputValidator
  def validate_user_input(input)
    errors = []
    
    # Validate name
    if input[:name].nil? || input[:name].strip.empty?
      errors << 'Name is required'
    elsif input[:name].length > 100
      errors << 'Name is too long'
    end
    
    # Validate email
    unless input[:email] =~ /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i
      errors << 'Invalid email format'
    end
    
    # Validate age
    if input[:age] && (input[:age] < 0 || input[:age] > 150)
      errors << 'Age must be between 0 and 150'
    end
    
    {
      valid: errors.empty?,
      errors: errors
    }
  end
  
  def sanitize_input(input)
    # Basic input sanitization
    input.gsub(/['";\\]/, '').strip
  end
  
  def sanitize_html(html)
    # Basic HTML sanitization
    html.gsub(/<script.*?<\/script>/mi, '').gsub(/<[^>]*>/, '')
  end
end

class HTTPSEnforcer
  def initialize
    @hsts_max_age = 31536000 # 1 year
    @hsts_include_subdomains = true
    @hsts_preload = false
  end
  
  def enforce_https(request)
    if request.scheme == 'http'
      {
        status: 301,
        location: "https://#{request.host}#{request.path}"
      }
    else
      { allowed: true }
    end
  end
  
  def get_hsts_headers
    hsts_value = "max-age=#{@hsts_max_age}"
    hsts_value += '; includeSubDomains' if @hsts_include_subdomains
    hsts_value += '; preload' if @hsts_preload
    
    {
      'Strict-Transport-Security' => hsts_value
    }
  end
end
```

## 📊 API Testing

### 6. API Testing Framework

Comprehensive API testing:

```ruby
class APITestFramework
  def initialize(base_url, options = {})
    @base_url = base_url
    @client = RESTAPIClient.new(base_url, options)
    @test_results = []
    @current_test = nil
  end
  
  def test(name, &block)
    @current_test = {
      name: name,
      started_at: Time.now,
      assertions: [],
      status: :running
    }
    
    begin
      instance_eval(&block)
      @current_test[:status] = :passed
      @current_test[:ended_at] = Time.now
    rescue => e
      @current_test[:status] = :failed
      @current_test[:error] = e.message
      @current_test[:ended_at] = Time.now
    ensure
      @test_results << @current_test
      @current_test = nil
    end
  end
  
  def get(path, expected_status = 200)
    response = @client.get(path)
    assert_status(expected_status, response)
    response
  end
  
  def post(path, body, expected_status = 201)
    response = @client.post(path, body)
    assert_status(expected_status, response)
    response
  end
  
  def put(path, body, expected_status = 200)
    response = @client.put(path, body)
    assert_status(expected_status, response)
    response
  end
  
  def delete(path, expected_status = 204)
    response = @client.delete(path)
    assert_status(expected_status, response)
    response
  end
  
  def assert_equal(expected, actual, message = nil)
    assertion = {
      type: :equal,
      expected: expected,
      actual: actual,
      message: message || "Expected #{expected}, got #{actual}",
      passed: expected == actual
    }
    
    @current_test[:assertions] << assertion
    raise AssertionError, assertion[:message] unless assertion[:passed]
  end
  
  def assert_not_nil(value, message = nil)
    assertion = {
      type: :not_nil,
      value: value,
      message: message || "Expected not nil, got #{value}",
      passed: !value.nil?
    }
    
    @current_test[:assertions] << assertion
    raise AssertionError, assertion[:message] unless assertion[:passed]
  end
  
  def assert_includes(collection, value, message = nil)
    assertion = {
      type: :includes,
      collection: collection,
      value: value,
      message: message || "Expected #{collection} to include #{value}",
      passed: collection.include?(value)
    }
    
    @current_test[:assertions] << assertion
    raise AssertionError, assertion[:message] unless assertion[:passed]
  end
  
  def assert_status(expected_status, response)
    assert_equal(expected_status, response[:status], "Expected status #{expected_status}, got #{response[:status]}")
  end
  
  def run_all_tests
    puts "Running API Tests..."
    puts "Base URL: #{@base_url}"
    
    start_time = Time.now
    
    # Define tests
    test_user_crud
    test_error_handling
    test_authentication
    test_rate_limiting
    
    end_time = Time.now
    duration = end_time - start_time
    
    generate_report(duration)
  end
  
  def generate_report(duration)
    puts "\nAPI Test Report:"
    puts "=" * 50
    
    total_tests = @test_results.length
    passed_tests = @test_results.count { |test| test[:status] == :passed }
    failed_tests = @test_results.count { |test| test[:status] == :failed }
    
    puts "Total Tests: #{total_tests}"
    puts "Passed: #{passed_tests}"
    puts "Failed: #{failed_tests}"
    puts "Success Rate: #{(passed_tests.to_f / total_tests * 100).round(2)}%"
    puts "Duration: #{duration.round(2)}s"
    
    puts "\nTest Results:"
    @test_results.each do |test|
      status_icon = test[:status] == :passed ? '✅' : '❌'
      puts "#{status_icon} #{test[:name]} (#{(test[:ended_at] - test[:started_at]).round(2)}s)"
      
      if test[:status] == :failed
        puts "   Error: #{test[:error]}"
      end
      
      test[:assertions].each do |assertion|
        assertion_icon = assertion[:passed] ? '✓' : '✗'
        puts "   #{assertion_icon} #{assertion[:message]}"
      end
    end
    
    {
      total_tests: total_tests,
      passed_tests: passed_tests,
      failed_tests: failed_tests,
      success_rate: (passed_tests.to_f / total_tests * 100).round(2),
      duration: duration,
      results: @test_results
    }
  end
  
  def self.demonstrate_api_testing
    puts "API Testing Framework Demonstration:"
    puts "=" * 50
    
    # Create test framework
    test_framework = APITestFramework.new('https://api.example.com/v1', {
      headers: { 'Content-Type' => 'application/json' }
    })
    
    # Run tests
    report = test_framework.run_all_tests
    
    puts "\nAPI Testing Features:"
    puts "- Test organization"
    puts "- Assertion methods"
    puts "- HTTP method testing"
    puts "- Error handling"
    puts "- Report generation"
    puts "- Performance measurement"
  end
  
  private
  
  def test_user_crud
    test("Create User") do
      user_data = { name: 'Test User', email: 'test@example.com' }
      response = post('/users', user_data, 201)
      assert_not_nil(response[:data][:id])
      assert_equal(user_data[:name], response[:data][:name])
    end
    
    test("Get User") do
      response = get('/users/1', 200)
      assert_not_nil(response[:data])
      assert_equal('Test User', response[:data][:name])
    end
    
    test("Update User") do
      update_data = { name: 'Updated User' }
      response = put('/users/1', update_data, 200)
      assert_equal(update_data[:name], response[:data][:name])
    end
    
    test("Delete User") do
      response = delete('/users/1', 204)
      assert_equal(nil, response[:data])
    end
  end
  
  def test_error_handling
    test("Not Found Error") do
      response = get('/users/999', 404)
      assert_not_nil(response[:data][:error])
    end
    
    test("Validation Error") do
      invalid_data = { name: '', email: 'invalid' }
      response = post('/users', invalid_data, 422)
      assert_includes(response[:data][:errors], 'Name is required')
    end
  end
  
  def test_authentication
    test("Unauthorized Access") do
      response = get('/admin/users', 401)
      assert_equal('Unauthorized', response[:data][:error])
    end
    
    test("Authorized Access") do
      # Simulate authenticated request
      client = RESTAPIClient.new(@base_url, {
        headers: { 'Authorization' => 'Bearer valid_token' }
      })
      response = client.get('/admin/users')
      assert_equal(200, response[:status])
    end
  end
  
  def test_rate_limiting
    test("Rate Limiting") do
      # Make multiple requests quickly
      11.times do |i|
        response = get('/users')
        if i < 10
          assert_equal(200, response[:status])
        else
          assert_equal(429, response[:status])
        end
      end
    end
  end
end

class AssertionError < StandardError; end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic API Client**: Create simple HTTP client
2. **REST API**: Build RESTful endpoints
3. **Authentication**: Implement API auth
4. **Error Handling**: Add error management

### Intermediate Exercises

1. **GraphQL Client**: Build GraphQL integration
2. **API Security**: Implement security measures
3. **Rate Limiting**: Add rate limiting
4. **API Testing**: Create test framework

### Advanced Exercises

1. **API Gateway**: Build API gateway
2. **Microservices APIs**: Design microservice APIs
3. **Real-time APIs**: Implement WebSocket APIs
4. **API Analytics**: Add analytics and monitoring

---

## 🎯 Summary

API Integration in Ruby provides:

- **API Fundamentals** - Core concepts and principles
- **REST API Client** - HTTP client implementation
- **Integration Patterns** - Common integration patterns
- **GraphQL Integration** - GraphQL query implementation
- **API Security** - Security best practices
- **API Testing** - Comprehensive testing framework

Master these API integration techniques for robust Ruby applications!
