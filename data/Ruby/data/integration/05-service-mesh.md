# Service Mesh in Ruby
# Comprehensive guide to service mesh implementation and management

## 🔗 Service Mesh Fundamentals

### 1. Service Mesh Concepts

Core service mesh principles:

```ruby
class ServiceMeshFundamentals
  def self.explain_service_mesh_concepts
    puts "Service Mesh Fundamentals:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Service Mesh",
        description: "Infrastructure layer for microservices communication",
        purpose: ["Traffic management", "Security", "Observability", "Reliability"],
        benefits: ["Decoupled concerns", "Centralized control", "Enhanced security", "Better observability"],
        components: ["Data plane", "Control plane", "Service discovery", "Load balancing"]
      },
      {
        concept: "Data Plane",
        description: "Layer that handles service-to-service communication",
        responsibilities: ["Request routing", "Load balancing", "Traffic encryption", "Health checking"],
        implementations: ["Envoy", "Linkerd", "NGINX", "HAProxy"],
        features: ["Protocol translation", "Traffic routing", "Circuit breaking", "Retries"]
      },
      {
        concept: "Control Plane",
        description: "Layer that manages and configures the data plane",
        responsibilities: ["Service discovery", "Configuration management", "Policy enforcement", "Telemetry"],
        implementations: ["Istio", "Consul Connect", "Linkerd", "AWS App Mesh"],
        features: ["Service registry", "Configuration distribution", "Policy engine", "Telemetry collection"]
      },
      {
        concept: "Sidecar Pattern",
        description: "Deploying proxy alongside application container",
        benefits: ["Language-agnostic", "No code changes", "Transparent interception", "Easy adoption"],
        challenges: ["Resource overhead", "Complexity", "Performance impact", "Debugging"],
        implementations: ["Envoy sidecar", "Linkerd proxy", "NGINX sidecar"]
      },
      {
        concept: "Service Discovery",
        description: "Automatic discovery of service instances",
        methods: ["DNS-based", "API-based", "Registry-based", "Peer-to-peer"],
        benefits: ["Dynamic scaling", "Load balancing", "Fault tolerance", "Service health"],
        implementations: ["Consul", "Eureka", "Kubernetes", "Zookeeper"]
      },
      {
        concept: "Traffic Management",
        description: "Controlling how traffic flows between services",
        features: ["Load balancing", "Traffic splitting", "Canary deployments", "Circuit breaking"],
        policies: ["Weighted routing", "Request routing", "Header-based routing", "Geo-routing"],
        benefits: ["Gradual rollouts", "A/B testing", "Performance optimization", "Reliability"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Purpose: #{concept[:purpose].join(', ')}" if concept[:purpose]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Components: #{concept[:components].join(', ')}" if concept[:components]
      puts "  Responsibilities: #{concept[:responsibilities].join(', ')}" if concept[:responsibilities]
      puts "  Implementations: #{concept[:implementations].join(', ')}" if concept[:implementations]
      puts "  Features: #{concept[:features].join(', ')}" if concept[:features]
      puts "  Challenges: #{concept[:challenges].join(', ')}" if concept[:challenges]
      puts "  Methods: #{concept[:methods].join(', ')}" if concept[:methods]
      puts "  Policies: #{concept[:policies].join(', ')}" if concept[:policies]
    puts
    end
  end
  
  def self.service_mesh_architecture
    puts "\nService Mesh Architecture:"
    puts "=" * 50
    
    architecture = [
      {
        layer: "Application Layer",
        description: "Business logic and application code",
        components: ["Microservices", "Applications", "APIs"],
        responsibilities: ["Business logic", "Data processing", "User interfaces"],
        interaction: "Communicates through service mesh"
      },
      {
        layer: "Data Plane",
        description: "Handles service-to-service communication",
        components: ["Sidecar proxies", "Gateways", "Ingress controllers"],
        responsibilities: ["Request routing", "Load balancing", "Traffic encryption", "Health checking"],
        interaction: "Intercepts and forwards traffic"
      },
      {
        layer: "Control Plane",
        description: "Manages and configures the data plane",
        components: ["Service registry", "Policy engine", "Telemetry collector", "Configuration manager"],
        responsibilities: ["Service discovery", "Policy enforcement", "Configuration distribution", "Telemetry collection"],
        interaction: "Controls data plane behavior"
      },
      {
        layer: "Infrastructure Layer",
        description: "Underlying infrastructure",
        components: ["Kubernetes", "VMs", "Containers", "Network"],
        responsibilities: ["Resource management", "Network connectivity", "Storage", "Security"],
        interaction: "Hosts service mesh components"
      }
    ]
    
    architecture.each do |layer|
      puts "#{layer[:layer]}:"
      puts "  Description: #{layer[:description]}"
      puts "  Components: #{layer[:components].join(', ')}"
      puts "  Responsibilities: #{layer[:responsibilities].join(', ')}"
      puts "  Interaction: #{layer[:interaction]}"
      puts
    end
  end
  
  def self.service_mesh_features
    puts "\nService Mesh Features:"
    puts "=" * 50
    
    features = [
      {
        feature: "Traffic Management",
        description: "Control how traffic flows between services",
        capabilities: [
          "Load balancing algorithms",
          "Traffic splitting",
          "Canary deployments",
          "Circuit breaking",
          "Timeouts and retries"
        ],
        benefits: ["Performance optimization", "Gradual rollouts", "Fault tolerance", "Reliability"]
      },
      {
        feature: "Security",
        description: "Secure service-to-service communication",
        capabilities: [
          "Mutual TLS",
          "Service authentication",
          "Authorization policies",
          "Network policies",
          "Secret management"
        ],
        benefits: ["Zero-trust security", "Encryption", "Access control", "Compliance"]
      },
      {
        feature: "Observability",
        description: "Monitor and understand service behavior",
        capabilities: [
          "Distributed tracing",
          "Metrics collection",
          "Access logging",
          "Health monitoring",
          "Performance analytics"
        ],
        benefits: ["Visibility", "Debugging", "Performance optimization", "SLA monitoring"]
      },
      {
        feature: "Reliability",
        description: "Ensure reliable service communication",
        capabilities: [
          "Automatic retries",
          "Circuit breaking",
          "Timeout management",
          "Health checking",
          "Failover handling"
        ],
        benefits: ["High availability", "Fault tolerance", "Graceful degradation", "Resilience"]
      },
      {
        feature: "Policy Enforcement",
        description: "Apply policies to service communication",
        capabilities: [
          "Access control",
          "Rate limiting",
          "Traffic shaping",
          "Compliance checks",
          "Security policies"
        ],
        benefits: ["Security", "Compliance", "Resource management", "Governance"]
      },
      {
        feature: "Service Discovery",
        description: "Automatic service instance discovery",
        capabilities: [
          "Service registration",
          "Health checking",
          "Load balancing",
          "Service routing",
          "Dynamic configuration"
        ],
        benefits: ["Scalability", "Load distribution", "Fault tolerance", "Automation"]
      }
    ]
    
    features.each do |feature|
      puts "#{feature[:feature]}:"
      puts "  Description: #{feature[:description]}"
      puts "  Capabilities: #{feature[:capabilities].join(', ')}"
      puts "  Benefits: #{feature[:benefits].join(', ')}"
      puts
    end
  end
  
  def self.service_mesh_benefits
    puts "\nService Mesh Benefits:"
    puts "=" * 50
    
    benefits = [
      {
        benefit: "Operational Simplicity",
        description: "Simplify operational complexity",
        points: [
          "Centralized management",
          "Unified policies",
          "Automated configuration",
          "Reduced operational overhead",
          "Consistent behavior"
        ],
        impact: ["Reduced complexity", "Easier management", "Better consistency", "Lower costs"]
      },
      {
        benefit: "Enhanced Security",
        description: "Improve security posture",
        points: [
          "Zero-trust architecture",
          "Automatic encryption",
          "Fine-grained access control",
          "Security policy enforcement",
          "Compliance automation"
        ],
        impact: ["Better security", "Compliance", "Risk reduction", "Auditability"]
      },
      {
        benefit: "Improved Observability",
        description: "Gain better visibility into services",
        points: [
          "End-to-end tracing",
          "Comprehensive metrics",
          "Detailed logging",
          "Performance insights",
          "Health monitoring"
        ],
        impact: ["Better debugging", "Performance optimization", "SLA monitoring", "Proactive management"]
      },
      {
        benefit: "Increased Reliability",
        description: "Make services more reliable",
        points: [
          "Automatic retries",
          "Circuit breaking",
          "Failover handling",
          "Health checking",
          "Graceful degradation"
        ],
        impact: ["Higher availability", "Better resilience", "User experience", "Reduced downtime"]
      },
      {
        benefit: "Developer Productivity",
        description: "Improve developer experience",
        points: [
          "Language-agnostic",
          "No code changes required",
          "Transparent integration",
          "Built-in features",
          "Focus on business logic"
        ],
        impact: ["Faster development", "Reduced complexity", "Better focus", "Innovation"]
      }
    ]
    
    benefits.each do |benefit|
      puts "#{benefit[:benefit]}:"
      puts "  Description: #{benefit[:description]}"
      puts "  Points: #{benefit[:points].join(', ')}"
      puts "  Impact: #{benefit[:impact].join(', ')}"
      puts
    end
  end
  
  # Run service mesh fundamentals
  explain_service_mesh_concepts
  service_mesh_architecture
  service_mesh_features
  service_mesh_benefits
end
```

### 2. Service Mesh Implementation

Ruby service mesh implementation:

```ruby
class RubyServiceMesh
  def initialize(name, options = {})
    @name = name
    @services = {}
    @proxies = {}
    @control_plane = ControlPlane.new
    @data_plane = DataPlane.new
    @service_registry = ServiceRegistry.new
    @traffic_manager = TrafficManager.new
    @security_manager = SecurityManager.new
    @observability_manager = ObservabilityManager.new
    @running = false
    @mutex = Mutex.new
  end
  
  attr_reader :name, :services, :proxies
  
  def register_service(service_name, endpoint, options = {})
    @mutex.synchronize do
      service = Service.new(service_name, endpoint, options)
      @services[service_name] = service
      
      # Register with service registry
      @service_registry.register(service)
      
      # Create sidecar proxy
      proxy = SidecarProxy.new(service, @data_plane)
      @proxies[service_name] = proxy
      
      puts "Service registered: #{service_name} at #{endpoint}"
      service
    end
  end
  
  def unregister_service(service_name)
    @mutex.synchronize do
      service = @services.delete(service_name)
      @proxies.delete(service_name)
      
      if service
        @service_registry.unregister(service)
        puts "Service unregistered: #{service_name}"
      end
    end
  end
  
  def start
    return if @running
    
    @running = true
    
    # Start control plane
    @control_plane.start
    
    # Start data plane
    @data_plane.start
    
    # Start all proxies
    @proxies.each do |name, proxy|
      proxy.start
    end
    
    puts "Service mesh #{@name} started"
  end
  
  def stop
    return unless @running
    
    @running = false
    
    # Stop all proxies
    @proxies.each do |name, proxy|
      proxy.stop
    end
    
    # Stop data plane
    @data_plane.stop
    
    # Stop control plane
    @control_plane.stop
    
    puts "Service mesh #{@name} stopped"
  end
  
  def get_service(service_name)
    @services[service_name]
  end
  
  def get_proxy(service_name)
    @proxies[service_name]
  end
  
  def get_mesh_stats
    {
      name: @name,
      running: @running,
      services: @services.length,
      proxies: @proxies.length,
      control_plane: @control_plane.stats,
      data_plane: @data_plane.stats,
      service_registry: @service_registry.stats,
      traffic_manager: @traffic_manager.stats,
      security_manager: @security_manager.stats,
      observability_manager: @observability_manager.stats
    }
  end
  
  def self.demonstrate_service_mesh
    puts "Ruby Service Mesh Demonstration:"
    puts "=" * 50
    
    # Create service mesh
    mesh = RubyServiceMesh.new('ruby-mesh')
    
    # Register services
    puts "Registering services:"
    
    user_service = mesh.register_service('user-service', 'http://localhost:3001', {
      version: 'v1',
      tags: ['users', 'auth']
    })
    
    order_service = mesh.register_service('order-service', 'http://localhost:3002', {
      version: 'v1',
      tags: ['orders', 'payments']
    })
    
    notification_service = mesh.register_service('notification-service', 'http://localhost:3003', {
      version: 'v1',
      tags: ['notifications', 'email']
    })
    
    # Start service mesh
    puts "\nStarting service mesh:"
    mesh.start
    
    # Simulate service communication
    puts "\nSimulating service communication:"
    
    # User service calls order service
    user_proxy = mesh.get_proxy('user-service')
    order_endpoint = user_proxy.resolve_service('order-service')
    puts "User service resolved order service: #{order_endpoint}"
    
    # Order service calls notification service
    order_proxy = mesh.get_proxy('order-service')
    notification_endpoint = order_proxy.resolve_service('notification-service')
    puts "Order service resolved notification service: #{notification_endpoint}"
    
    # Get mesh statistics
    puts "\nMesh Statistics:"
    stats = mesh.get_mesh_stats
    puts "  Name: #{stats[:name]}"
    puts "  Running: #{stats[:running]}"
    puts "  Services: #{stats[:services]}"
    puts "  Proxies: #{stats[:proxies]}"
    
    # Stop service mesh
    puts "\nStopping service mesh:"
    mesh.stop
    
    puts "\nService Mesh Features:"
    puts "- Service registration and discovery"
    puts "- Sidecar proxy management"
    puts "- Traffic routing and load balancing"
    puts "- Security and authentication"
    puts "- Observability and monitoring"
    puts "- Centralized control plane"
    puts "- Distributed data plane"
  end
end

class Service
  def initialize(name, endpoint, options = {})
    @name = name
    @endpoint = endpoint
    @options = options
    @version = options[:version] || 'v1'
    @tags = options[:tags] || []
    @metadata = options[:metadata] || {}
    @created_at = Time.now
    @health_status = 'healthy'
    @last_health_check = Time.now
  end
  
  attr_reader :name, :endpoint, :version, :tags, :metadata, :created_at
  attr_accessor :health_status, :last_health_check
  
  def healthy?
    @health_status == 'healthy'
  end
  
  def to_h
    {
      name: @name,
      endpoint: @endpoint,
      version: @version,
      tags: @tags,
      metadata: @metadata,
      created_at: @created_at,
      health_status: @health_status,
      last_health_check: @last_health_check
    }
  end
end

class ControlPlane
  def initialize
    @running = false
    @configurations = {}
    @policies = {}
    @services = {}
    @mutex = Mutex.new
  end
  
  def start
    @running = true
    puts "Control plane started"
  end
  
  def stop
    @running = false
    puts "Control plane stopped"
  end
  
  def configure_service(service_name, config)
    @mutex.synchronize do
      @configurations[service_name] = config
    end
  end
  
  def apply_policy(policy_name, policy)
    @mutex.synchronize do
      @policies[policy_name] = policy
    end
  end
  
  def get_configuration(service_name)
    @mutex.synchronize do
      @configurations[service_name]
    end
  end
  
  def get_policy(policy_name)
    @mutex.synchronize do
      @policies[policy_name]
    end
  end
  
  def stats
    {
      running: @running,
      configurations: @configurations.length,
      policies: @policies.length,
      services: @services.length
    }
  end
end

class DataPlane
  def initialize
    @running = false
    @proxies = {}
    @connections = {}
    @mutex = Mutex.new
  end
  
  def start
    @running = true
    puts "Data plane started"
  end
  
  def stop
    @running = false
    puts "Data plane stopped"
  end
  
  def add_proxy(service_name, proxy)
    @mutex.synchronize do
      @proxies[service_name] = proxy
    end
  end
  
  def remove_proxy(service_name)
    @mutex.synchronize do
      @proxies.delete(service_name)
    end
  end
  
  def get_proxy(service_name)
    @mutex.synchronize do
      @proxies[service_name]
    end
  end
  
  def stats
    {
      running: @running,
      proxies: @proxies.length,
      connections: @connections.length
    }
  end
end

class ServiceRegistry
  def initialize
    @services = {}
    @mutex = Mutex.new
  end
  
  def register(service)
    @mutex.synchronize do
      @services[service.name] = service
    end
  end
  
  def unregister(service)
    @mutex.synchronize do
      @services.delete(service.name)
    end
  end
  
  def get_service(service_name)
    @mutex.synchronize do
      @services[service_name]
    end
  end
  
  def get_all_services
    @mutex.synchronize do
      @services.values
    end
  end
  
  def get_healthy_services
    get_all_services.select(&:healthy?)
  end
  
  def stats
    @mutex.synchronize do
      {
        total_services: @services.length,
        healthy_services: @services.values.count(&:healthy?)
      }
    end
  end
end

class SidecarProxy
  def initialize(service, data_plane)
    @service = service
    @data_plane = data_plane
    @running = false
    @connections = {}
    @load_balancer = LoadBalancer.new
    @circuit_breaker = CircuitBreaker.new
    @retry_policy = RetryPolicy.new
    @mutex = Mutex.new
  end
  
  attr_reader :service, :running
  
  def start
    @running = true
    @data_plane.add_proxy(@service.name, self)
    puts "Sidecar proxy started for #{@service.name}"
  end
  
  def stop
    @running = false
    @data_plane.remove_proxy(@service.name)
    puts "Sidecar proxy stopped for #{@service.name}"
  end
  
  def resolve_service(target_service)
    # Simulate service resolution
    "http://#{target_service}:8080"
  end
  
  def send_request(target_service, request)
    endpoint = resolve_service(target_service)
    
    # Apply circuit breaker
    @circuit_breaker.call do
      # Apply retry policy
      @retry_policy.execute do
        # Simulate request
        puts "  #{@service.name} -> #{target_service}: #{request[:method]} #{request[:path]}"
        
        # Simulate response
        {
          status: 200,
          body: "Response from #{target_service}",
          headers: { 'Content-Type' => 'application/json' }
        }
      end
    end
  end
  
  def stats
    {
      service: @service.name,
      running: @running,
      connections: @connections.length,
      load_balancer: @load_balancer.stats,
      circuit_breaker: @circuit_breaker.stats,
      retry_policy: @retry_policy.stats
    }
  end
end

class LoadBalancer
  def initialize(strategy = :round_robin)
    @strategy = strategy
    @instances = []
    @current_index = 0
    @mutex = Mutex.new
  end
  
  def add_instance(instance)
    @mutex.synchronize do
      @instances << instance
    end
  end
  
  def remove_instance(instance)
    @mutex.synchronize do
      @instances.delete(instance)
    end
  end
  
  def get_instance
    @mutex.synchronize do
      return nil if @instances.empty?
      
      case @strategy
      when :round_robin
        instance = @instances[@current_index]
        @current_index = (@current_index + 1) % @instances.length
        instance
      when :random
        @instances.sample
      when :least_connections
        @instances.min_by(&:connections)
      else
        @instances.first
      end
    end
  end
  
  def stats
    @mutex.synchronize do
      {
        strategy: @strategy,
        instances: @instances.length,
        current_index: @current_index
      }
    end
  end
end

class CircuitBreaker
  def initialize(options = {})
    @failure_threshold = options[:failure_threshold] || 5
    @recovery_timeout = options[:recovery_timeout] || 60
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
          raise CircuitBreakerOpenError, "Circuit breaker is open"
        end
      end
    end
    
    begin
      result = yield
      reset
      result
    rescue => e
      record_failure
      raise
    end
  end
  
  def stats
    @mutex.synchronize do
      {
        state: @state,
        failure_count: @failure_count,
        failure_threshold: @failure_threshold,
        last_failure_time: @last_failure_time
      }
    end
  end
  
  private
  
  def record_failure
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
  end
  
  def execute(&block)
    attempts = 0
    
    begin
      attempts += 1
      result = yield
      result
    rescue => e
      if attempts < @max_attempts
        delay = calculate_delay(attempts)
        sleep(delay)
        retry
      else
        raise
      end
    end
  end
  
  def stats
    {
      max_attempts: @max_attempts,
      base_delay: @base_delay,
      max_delay: @max_delay,
      backoff_multiplier: @backoff_multiplier
    }
  end
  
  private
  
  def calculate_delay(attempts)
    delay = @base_delay * (@backoff_multiplier ** (attempts - 1))
    [delay, @max_delay].min
  end
end

class TrafficManager
  def initialize
    @routing_rules = {}
    @traffic_splits = {}
    @mutex = Mutex.new
  end
  
  def add_routing_rule(rule_name, rule)
    @mutex.synchronize do
      @routing_rules[rule_name] = rule
    end
  end
  
  def add_traffic_split(split_name, split)
    @mutex.synchronize do
      @traffic_splits[split_name] = split
    end
  end
  
  def route_request(request)
    # Simulate routing logic
    target_service = determine_target(request)
    target_service
  end
  
  def stats
    @mutex.synchronize do
      {
        routing_rules: @routing_rules.length,
        traffic_splits: @traffic_splits.length
      }
    end
  end
  
  private
  
  def determine_target(request)
    # Simple routing logic
    case request[:path]
    when /\/users\//
      'user-service'
    when /\/orders\//
      'order-service'
    when /\/notifications\//
      'notification-service'
    else
      'default-service'
    end
  end
end

class SecurityManager
  def initialize
    @policies = {}
    @certificates = {}
    @mutex = Mutex.new
  end
  
  def add_policy(policy_name, policy)
    @mutex.synchronize do
      @policies[policy_name] = policy
    end
  end
  
  def add_certificate(service_name, certificate)
    @mutex.synchronize do
      @certificates[service_name] = certificate
    end
  end
  
  def authenticate_request(request)
    # Simulate authentication
    true
  end
  
  def authorize_request(request, policy)
    # Simulate authorization
    true
  end
  
  def encrypt_message(message)
    # Simulate encryption
    "encrypted:#{message}"
  end
  
  def decrypt_message(encrypted_message)
    # Simulate decryption
    encrypted_message.sub('encrypted:', '')
  end
  
  def stats
    @mutex.synchronize do
      {
        policies: @policies.length,
        certificates: @certificates.length
      }
    end
  end
end

class ObservabilityManager
  def initialize
    @metrics = {}
    @traces = {}
    @logs = []
    @mutex = Mutex.new
  end
  
  def record_metric(metric_name, value, tags = {})
    @mutex.synchronize do
      @metrics[metric_name] ||= []
      @metrics[metric_name] << {
        value: value,
        tags: tags,
        timestamp: Time.now
      }
    end
  end
  
  def start_trace(trace_id, span_name)
    @mutex.synchronize do
      @traces[trace_id] ||= []
      @traces[trace_id] << {
        span_name: span_name,
        start_time: Time.now,
        end_time: nil
      }
    end
  end
  
  def end_trace(trace_id, span_name)
    @mutex.synchronize do
      trace = @traces[trace_id]
      if trace
        span = trace.find { |s| s[:span_name] == span_name }
        span[:end_time] = Time.now if span
      end
    end
  end
  
  def log_event(level, message, context = {})
    @mutex.synchronize do
      @logs << {
        level: level,
        message: message,
        context: context,
        timestamp: Time.now
      }
    end
  end
  
  def get_metrics(metric_name = nil)
    @mutex.synchronize do
      if metric_name
        @metrics[metric_name]
      else
        @metrics
      end
    end
  end
  
  def get_traces(trace_id = nil)
    @mutex.synchronize do
      if trace_id
        @traces[trace_id]
      else
        @traces
      end
    end
  end
  
  def get_logs(limit = 100)
    @mutex.synchronize do
      @logs.last(limit)
    end
  end
  
  def stats
    @mutex.synchronize do
      {
        metrics: @metrics.keys.length,
        traces: @traces.keys.length,
        logs: @logs.length
      }
    end
  end
end

class CircuitBreakerOpenError < StandardError; end
```

## 🔧 Traffic Management

### 3. Traffic Routing and Management

Advanced traffic management:

```ruby
class TrafficManagement
  def self.demonstrate_traffic_management
    puts "Traffic Management Demonstration:"
    puts "=" * 50
    
    # 1. Load Balancing
    demonstrate_load_balancing
    
    # 2. Traffic Splitting
    demonstrate_traffic_splitting
    
    # 3. Circuit Breaking
    demonstrate_circuit_breaking
    
    # 4. Retry Logic
    demonstrate_retry_logic
    
    # 5. Timeout Management
    demonstrate_timeout_management
    
    # 6. Request Routing
    demonstrate_request_routing
  end
  
  def self.demonstrate_load_balancing
    puts "\n1. Load Balancing:"
    puts "=" * 30
    
    # Create instances
    instances = [
      ServiceInstance.new('instance-1', '192.168.1.10', 8080),
      ServiceInstance.new('instance-2', '192.168.1.11', 8080),
      ServiceInstance.new('instance-3', '192.168.1.12', 8080)
    ]
    
    # Test different load balancing strategies
    strategies = [:round_robin, :weighted_round_robin, :least_connections, :random]
    
    strategies.each do |strategy|
      puts "\nTesting #{strategy} strategy:"
      
      load_balancer = LoadBalancer.new(strategy)
      instances.each { |instance| load_balancer.add_instance(instance) }
      
      # Simulate 10 requests
      10.times do |i|
        instance = load_balancer.get_instance
        puts "  Request #{i + 1}: #{instance&.id}"
      end
      
      puts "  Stats: #{load_balancer.stats}"
    end
    
    puts "\nLoad Balancing Features:"
    puts "- Multiple algorithms"
    puts "- Health checking"
    puts "- Weighted routing"
    puts "- Instance management"
    puts "- Performance metrics"
  end
  
  def self.demonstrate_traffic_splitting
    puts "\n2. Traffic Splitting:"
    puts "=" * 30
    
    # Create traffic splitter
    splitter = TrafficSplitter.new
    
    # Define traffic split
    splitter.add_split('feature-flag', {
      'v1' => 80,  # 80% to v1
      'v2' => 20   # 20% to v2
    })
    
    # Simulate traffic
    puts "Simulating traffic split:"
    
    100.times do |i|
      version = splitter.route('feature-flag')
      puts "  Request #{i + 1}: #{version}" if i < 10
    end
    
    # Get split statistics
    stats = splitter.get_split_stats('feature-flag')
    puts "\nSplit Statistics:"
    stats.each do |version, count|
      puts "  #{version}: #{count}%"
    end
    
    puts "\nTraffic Splitting Features:"
    puts "- Percentage-based routing"
    puts "- Feature flags"
    puts "- Canary deployments"
    puts "- A/B testing"
    puts "- Gradual rollouts"
  end
  
  def self.demonstrate_circuit_breaking
    puts "\n3. Circuit Breaking:"
    puts "=" * 30
    
    # Create circuit breaker
    circuit_breaker = CircuitBreaker.new({
      failure_threshold: 3,
      recovery_timeout: 5,
      expected_exceptions: [TimeoutError, ConnectionError]
    })
    
    # Simulate requests
    puts "Simulating circuit breaker behavior:"
    
    8.times do |i|
      begin
        result = circuit_breaker.call do
          simulate_service_call(i)
        end
        puts "  Request #{i + 1}: Success - #{result}"
      rescue => e
        puts "  Request #{i + 1}: Failed - #{e.message}"
      end
    end
    
    # Wait for recovery
    puts "\nWaiting for circuit breaker recovery..."
    sleep(6)
    
    # Try again after recovery
    begin
      result = circuit_breaker.call do
        simulate_service_call(9)
      end
      puts "  Request after recovery: Success - #{result}"
    rescue => e
      puts "  Request after recovery: Failed - #{e.message}"
    end
    
    puts "\nCircuit Breaker Features:"
    puts "- Failure threshold detection"
    puts "- Automatic circuit opening"
    puts "- Recovery timeout"
    puts "- Exception filtering"
    puts "- State management"
  end
  
  def self.demonstrate_retry_logic
    puts "\n4. Retry Logic:"
    puts "=" * 30
    
    # Create retry policy
    retry_policy = RetryPolicy.new({
      max_attempts: 3,
      base_delay: 1,
      max_delay: 5,
      backoff_multiplier: 2,
      retry_exceptions: [TimeoutError, ConnectionError]
    })
    
    # Simulate requests with retries
    puts "Simulating retry logic:"
    
    3.times do |i|
      puts "\nAttempt #{i + 1}:"
      
      begin
        result = retry_policy.execute do
          simulate_failing_service_call(i)
        end
        puts "  Success: #{result}"
      rescue => e
        puts "  Failed after all attempts: #{e.message}"
      end
    end
    
    puts "\nRetry Logic Features:"
    puts "- Configurable retry attempts"
    puts "- Exponential backoff"
    puts "- Exception filtering"
    puts "- Delay management"
    puts "- Jitter support"
  end
  
  def self.demonstrate_timeout_management
    puts "\n5. Timeout Management:"
    puts "=" * 30
    
    # Create timeout manager
    timeout_manager = TimeoutManager.new({
      default_timeout: 5,
      per_service_timeouts: {
        'fast-service' => 2,
        'slow-service' => 10
      }
    })
    
    # Simulate requests with timeouts
    puts "Simulating timeout management:"
    
    services = ['fast-service', 'slow-service', 'unknown-service']
    
    services.each do |service|
      puts "\nRequest to #{service}:"
      
      begin
        result = timeout_manager.execute_with_timeout(service) do
          simulate_service_with_delay(service)
        end
        puts "  Success: #{result}"
      rescue TimeoutError => e
        puts "  Timeout: #{e.message}"
      end
    end
    
    puts "\nTimeout Management Features:"
    puts "- Per-service timeouts"
    puts "- Default timeout fallback"
    puts "- Timeout enforcement"
    puts "- Graceful degradation"
    puts "- Timeout monitoring"
  end
  
  def self.demonstrate_request_routing
    puts "\n6. Request Routing:"
    puts "=" * 30
    
    # Create request router
    router = RequestRouter.new
    
    # Define routing rules
    router.add_rule('user-routes', {
      path_pattern: '/api/users/*',
      target_service: 'user-service',
      rewrite_path: '/users/$1'
    })
    
    router.add_rule('order-routes', {
      path_pattern: '/api/orders/*',
      target_service: 'order-service',
      rewrite_path: '/orders/$1'
    })
    
    router.add_rule('header-routing', {
      header_condition: { 'X-API-Version' => 'v2' },
      target_service: 'v2-service'
    })
    
    # Simulate requests
    requests = [
      { path: '/api/users/123', headers: {} },
      { path: '/api/orders/456', headers: {} },
      { path: '/api/products/789', headers: { 'X-API-Version' => 'v2' } },
      { path: '/api/unknown', headers: {} }
    ]
    
    puts "Simulating request routing:"
    
    requests.each do |request|
      route = router.route_request(request)
      puts "  #{request[:path]} -> #{route[:target_service] || 'no-route'}"
    end
    
    puts "\nRequest Routing Features:"
    puts "- Path-based routing"
    puts "- Header-based routing"
    puts "- Path rewriting"
    puts "- Multiple conditions"
    puts "- Fallback routing"
  end
  
  private
  
  def self.simulate_service_call(attempt)
    sleep(0.1)
    
    case attempt
    when 0, 1, 2
      raise TimeoutError, "Service timeout"
    when 3, 4, 5
      raise ConnectionError, "Connection failed"
    else
      "Service response"
    end
  end
  
  def self.simulate_failing_service_call(attempt)
    sleep(0.1)
    
    case attempt
    when 0
      raise TimeoutError, "Service timeout"
    when 1
      raise ConnectionError, "Connection failed"
    else
      "Success after #{attempt + 1} attempts"
    end
  end
  
  def self.simulate_service_with_delay(service)
    case service
    when 'fast-service'
      sleep(1)
      "Fast service response"
    when 'slow-service'
      sleep(8)
      "Slow service response"
    else
      sleep(6)
      "Unknown service response"
    end
  end
end

class ServiceInstance
  def initialize(id, host, port, weight = 1)
    @id = id
    @host = host
    @port = port
    @weight = weight
    @connections = 0
    @healthy = true
    @last_health_check = Time.now
  end
  
  attr_reader :id, :host, :port, :weight, :connections, :last_health_check
  attr_accessor :healthy
  
  def endpoint
    "#{@host}:#{@port}"
  end
  
  def increment_connections
    @connections += 1
  end
  
  def decrement_connections
    @connections -= 1
  end
end

class TrafficSplitter
  def initialize
    @splits = {}
    @counts = {}
    @mutex = Mutex.new
  end
  
  def add_split(split_name, split_config)
    @mutex.synchronize do
      @splits[split_name] = split_config
      @counts[split_name] = {}
      split_config.each_key { |version| @counts[split_name][version] = 0 }
    end
  end
  
  def route(split_name)
    @mutex.synchronize do
      split_config = @splits[split_name]
      return nil unless split_config
      
      # Calculate cumulative weights
      total_weight = split_config.values.sum
      random_value = rand(total_weight)
      
      cumulative = 0
      split_config.each do |version, weight|
        cumulative += weight
        if random_value <= cumulative
          @counts[split_name][version] += 1
          return version
        end
      end
    end
  end
  
  def get_split_stats(split_name)
    @mutex.synchronize do
      counts = @counts[split_name] || {}
      total = counts.values.sum
      
      if total > 0
        counts.transform_values { |count| (count.to_f / total * 100).round }
      else
        counts
      end
    end
  end
end

class TimeoutManager
  def initialize(options = {})
    @default_timeout = options[:default_timeout] || 5
    @per_service_timeouts = options[:per_service_timeouts] || {}
  end
  
  def execute_with_timeout(service, &block)
    timeout = get_timeout_for_service(service)
    
    begin
      Timeout.timeout(timeout) do
        yield
      end
    rescue Timeout::Error
      raise TimeoutError, "Service #{service} timed out after #{timeout}s"
    end
  end
  
  private
  
  def get_timeout_for_service(service)
    @per_service_timeouts[service] || @default_timeout
  end
end

class RequestRouter
  def initialize
    @rules = []
  end
  
  def add_rule(rule_name, rule_config)
    @rules << {
      name: rule_name,
      config: rule_config
    }
  end
  
  def route_request(request)
    @rules.each do |rule|
      if matches_rule?(request, rule[:config])
        return {
          target_service: rule[:config][:target_service],
          rewrite_path: rewrite_path(request[:path], rule[:config])
        }
      end
    end
    
    { target_service: nil, rewrite_path: request[:path] }
  end
  
  private
  
  def matches_rule?(request, rule_config)
    # Check path pattern
    if rule_config[:path_pattern]
      return false unless match_path_pattern?(request[:path], rule_config[:path_pattern])
    end
    
    # Check header conditions
    if rule_config[:header_condition]
      return false unless match_header_condition?(request[:headers], rule_config[:header_condition])
    end
    
    true
  end
  
  def match_path_pattern?(path, pattern)
    # Simple pattern matching
    regex_pattern = pattern.gsub('*', '.*')
    path.match?(Regexp.new("^#{regex_pattern}$"))
  end
  
  def match_header_condition?(headers, condition)
    condition.all? do |key, value|
      headers[key] == value
    end
  end
  
  def rewrite_path(path, rule_config)
    return path unless rule_config[:rewrite_path]
    
    # Simple path rewriting
    if rule_config[:path_pattern] && rule_config[:rewrite_path]
      pattern = rule_config[:path_pattern].gsub('*', '(.*)')
      replacement = rule_config[:rewrite_path]
      
      path.sub(Regexp.new("^#{pattern}$"), replacement)
    else
      path
    end
  end
end

class TimeoutError < StandardError; end
class ConnectionError < StandardError; end
```

## 🔐 Security and Policies

### 4. Security Implementation

Service mesh security:

```ruby
class ServiceMeshSecurity
  def self.demonstrate_security
    puts "Service Mesh Security Demonstration:"
    puts "=" * 50
    
    # 1. Mutual TLS
    demonstrate_mutual_tls
    
    # 2. Service Authentication
    demonstrate_service_authentication
    
    # 3. Authorization Policies
    demonstrate_authorization_policies
    
    # 4. Network Policies
    demonstrate_network_policies
    
    # 5. Secret Management
    demonstrate_secret_management
    
    # 6. Security Monitoring
    demonstrate_security_monitoring
  end
  
  def self.demonstrate_mutual_tls
    puts "\n1. Mutual TLS:"
    puts "=" * 30
    
    # Create certificate manager
    cert_manager = CertificateManager.new
    
    # Generate certificates for services
    services = ['user-service', 'order-service', 'notification-service']
    
    puts "Generating certificates:"
    
    services.each do |service|
      cert = cert_manager.generate_certificate(service)
      puts "  #{service}: #{cert[:certificate_id]}"
    end
    
    # Simulate TLS handshake
    puts "\nSimulating TLS handshake:"
    
    client_service = 'user-service'
    server_service = 'order-service'
    
    client_cert = cert_manager.get_certificate(client_service)
    server_cert = cert_manager.get_certificate(server_service)
    
    # Verify certificates
    client_valid = cert_manager.verify_certificate(client_cert[:certificate])
    server_valid = cert_manager.verify_certificate(server_cert[:certificate])
    
    puts "  Client certificate valid: #{client_valid}"
    puts "  Server certificate valid: #{server_valid}"
    
    # Establish secure connection
    if client_valid && server_valid
      puts "  Secure connection established between #{client_service} and #{server_service}"
    end
    
    puts "\nMutual TLS Features:"
    puts "- Certificate generation"
    puts "- Certificate verification"
    puts "- Secure handshake"
    puts "- Certificate rotation"
    puts "- Revocation checking"
  end
  
  def self.demonstrate_service_authentication
    puts "\n2. Service Authentication:"
    puts "=" * 30
    
    # Create authentication manager
    auth_manager = AuthenticationManager.new
    
    # Register services
    services = [
      { name: 'user-service', secret: 'user-secret-123' },
      { name: 'order-service', secret: 'order-secret-456' },
      { name: 'notification-service', secret: 'notification-secret-789' }
    ]
    
    puts "Registering services:"
    
    services.each do |service|
      auth_manager.register_service(service[:name], service[:secret])
      puts "  #{service[:name]}: registered"
    end
    
    # Simulate authentication
    puts "\nSimulating service authentication:"
    
    # Valid authentication
    token = auth_manager.authenticate('user-service', 'user-secret-123')
    puts "  Valid authentication: #{token ? 'Success' : 'Failed'}"
    
    # Invalid authentication
    token = auth_manager.authenticate('user-service', 'wrong-secret')
    puts "  Invalid authentication: #{token ? 'Success' : 'Failed'}"
    
    # Verify token
    if token
      verification = auth_manager.verify_token(token)
      puts "  Token verification: #{verification[:valid]}"
      puts "  Service: #{verification[:service_name]}" if verification[:valid]
    end
    
    puts "\nService Authentication Features:"
    puts "- Service registration"
    puts "- Token generation"
    puts "- Token verification"
    puts "- Secret management"
    puts "- Token rotation"
  end
  
  def self.demonstrate_authorization_policies
    puts "\n3. Authorization Policies:"
    puts "=" * 30
    
    # Create policy manager
    policy_manager = PolicyManager.new
    
    # Define authorization policies
    policy_manager.add_policy('user-service-access', {
      subject: 'order-service',
      action: 'read',
      resource: 'user-service',
      conditions: {
        method: ['GET', 'POST'],
        path_pattern: '/api/users/*'
      }
    })
    
    policy_manager.add_policy('order-service-access', {
      subject: 'notification-service',
      action: 'read',
      resource: 'order-service',
      conditions: {
        method: ['GET'],
        path_pattern: '/api/orders/*/status'
      }
    })
    
    # Test authorization
    puts "Testing authorization policies:"
    
    requests = [
      {
        subject: 'order-service',
        action: 'read',
        resource: 'user-service',
        method: 'GET',
        path: '/api/users/123'
      },
      {
        subject: 'notification-service',
        action: 'read',
        resource: 'order-service',
        method: 'GET',
        path: '/api/orders/456/status'
      },
      {
        subject: 'unknown-service',
        action: 'read',
        resource: 'user-service',
        method: 'DELETE',
        path: '/api/users/789'
      }
    ]
    
    requests.each_with_index do |request, i|
      authorized = policy_manager.authorize(request)
      puts "  Request #{i + 1}: #{authorized ? 'Authorized' : 'Denied'}"
    end
    
    puts "\nAuthorization Policies Features:"
    puts "- Policy definition"
    puts "- Rule evaluation"
    puts "- Condition matching"
    puts "- Access control"
    puts "- Policy enforcement"
  end
  
  def self.demonstrate_network_policies
    puts "\n4. Network Policies:"
    puts "=" * 30
    
    # Create network policy manager
    network_manager = NetworkPolicyManager.new
    
    # Define network policies
    network_manager.add_policy('allow-user-to-order', {
      source: 'user-service',
      destination: 'order-service',
      ports: [8080, 8443],
      protocols: ['tcp', 'tls']
    })
    
    network_manager.add_policy('allow-order-to-notification', {
      source: 'order-service',
      destination: 'notification-service',
      ports: [8080],
      protocols: ['tcp']
    })
    
    network_manager.add_policy('deny-all-external', {
      source: '*',
      destination: 'external',
      action: 'deny'
    })
    
    # Test network policies
    puts "Testing network policies:"
    
    connections = [
      { source: 'user-service', destination: 'order-service', port: 8080 },
      { source: 'order-service', destination: 'notification-service', port: 8080 },
      { source: 'user-service', destination: 'external', port: 443 },
      { source: 'unknown-service', destination: 'order-service', port: 8080 }
    ]
    
    connections.each_with_index do |connection, i|
      allowed = network_manager.allow_connection?(connection)
      puts "  Connection #{i + 1}: #{allowed ? 'Allowed' : 'Denied'}"
    end
    
    puts "\nNetwork Policies Features:"
    puts "- Connection filtering"
    puts "- Port-based rules"
    puts "- Protocol filtering"
    puts "- Source/destination control"
    puts "- Default deny policies"
  end
  
  def self.demonstrate_secret_management
    puts "\n5. Secret Management:"
    puts "=" * 30
    
    # Create secret manager
    secret_manager = SecretManager.new
    
    # Store secrets
    puts "Storing secrets:"
    
    secrets = [
      { name: 'db-credentials', value: 'db_user:db_password', type: 'credentials' },
      { name: 'api-keys', value: 'api-key-12345', type: 'api-key' },
      { name: 'tls-certs', value: 'certificate-data', type: 'certificate' }
    ]
    
    secrets.each do |secret|
      secret_id = secret_manager.store_secret(secret[:name], secret[:value], secret[:type])
      puts "  #{secret[:name]}: stored (ID: #{secret_id})"
    end
    
    # Retrieve secrets
    puts "\nRetrieving secrets:"
    
    secrets.each do |secret|
      retrieved_secret = secret_manager.get_secret(secret[:name])
      puts "  #{secret[:name]}: #{retrieved_secret ? 'Retrieved' : 'Not found'}"
    end
    
    # Rotate secret
    puts "\nRotating secret:"
    old_secret_id = secret_manager.get_secret_id('db-credentials')
    new_secret_id = secret_manager.rotate_secret('db-credentials', 'new_db_user:new_db_password')
    puts "  Old ID: #{old_secret_id}"
    puts "  New ID: #{new_secret_id}"
    
    puts "\nSecret Management Features:"
    puts "- Secure storage"
    puts "- Secret retrieval"
    puts "- Secret rotation"
    puts "- Access control"
    puts "- Audit logging"
  end
  
  def self.demonstrate_security_monitoring
    puts "\n6. Security Monitoring:"
    puts "=" * 30
    
    # Create security monitor
    security_monitor = SecurityMonitor.new
    
    # Simulate security events
    puts "Simulating security events:"
    
    events = [
      { type: 'authentication_success', service: 'user-service', timestamp: Time.now },
      { type: 'authentication_failure', service: 'order-service', reason: 'invalid_secret' },
      { type: 'authorization_denied', service: 'unknown-service', resource: 'user-service' },
      { type: 'tls_handshake_success', source: 'user-service', destination: 'order-service' },
      { type: 'policy_violation', service: 'user-service', policy: 'deny-all-external' }
    ]
    
    events.each_with_index do |event, i|
      security_monitor.log_event(event)
      puts "  Event #{i + 1}: #{event[:type]} - #{event[:service] || event[:source]}"
    end
    
    # Get security metrics
    puts "\nSecurity Metrics:"
    metrics = security_monitor.get_metrics
    puts "  Total events: #{metrics[:total_events]}"
    puts "  Authentication successes: #{metrics[:authentication_successes]}"
    puts "  Authentication failures: #{metrics[:authentication_failures]}"
    puts "  Authorization denials: #{metrics[:authorization_denials]}"
    puts "  Policy violations: #{metrics[:policy_violations]}"
    
    # Generate security report
    puts "\nSecurity Report:"
    report = security_monitor.generate_report
    puts "  Risk level: #{report[:risk_level]}"
    puts "  Recommendations: #{report[:recommendations].join(', ')}"
    
    puts "\nSecurity Monitoring Features:"
    puts "- Event logging"
    puts "- Metrics collection"
    puts "- Threat detection"
    puts "- Risk assessment"
    puts "- Compliance reporting"
  end
  
  private
  
  def self.simulate_delay(seconds)
    sleep(seconds)
  end
end

class CertificateManager
  def initialize
    @certificates = {}
    @mutex = Mutex.new
  end
  
  def generate_certificate(service_name)
    @mutex.synchronize do
      cert_id = SecureRandom.uuid
      
      certificate = {
        certificate_id: cert_id,
        service_name: service_name,
        certificate: "cert-#{service_name}-#{cert_id}",
        private_key: "key-#{service_name}-#{cert_id}",
        ca_certificate: "ca-cert-#{cert_id}",
        issued_at: Time.now,
        expires_at: Time.now + 86400 * 365, # 1 year
        serial_number: rand(1000000..9999999)
      }
      
      @certificates[cert_id] = certificate
      certificate
    end
  end
  
  def get_certificate(service_name)
    @mutex.synchronize do
      @certificates.values.find { |cert| cert[:service_name] == service_name }
    end
  end
  
  def verify_certificate(certificate)
    # Simulate certificate verification
    return false unless certificate
    return false if Time.now > certificate[:expires_at]
    
    true
  end
  
  def revoke_certificate(cert_id)
    @mutex.synchronize do
      cert = @certificates[cert_id]
      cert[:revoked] = true if cert
    end
  end
end

class AuthenticationManager
  def initialize
    @services = {}
    @tokens = {}
    @mutex = Mutex.new
  end
  
  def register_service(service_name, secret)
    @mutex.synchronize do
      @services[service_name] = {
        secret: secret,
        registered_at: Time.now
      }
    end
  end
  
  def authenticate(service_name, secret)
    @mutex.synchronize do
      service = @services[service_name]
      return nil unless service
      return nil unless service[:secret] == secret
      
      token = generate_token(service_name)
      @tokens[token] = {
        service_name: service_name,
        issued_at: Time.now,
        expires_at: Time.now + 3600 # 1 hour
      }
      
      token
    end
  end
  
  def verify_token(token)
    @mutex.synchronize do
      token_data = @tokens[token]
      return { valid: false } unless token_data
      return { valid: false } if Time.now > token_data[:expires_at]
      
      {
        valid: true,
        service_name: token_data[:service_name],
        issued_at: token_data[:issued_at],
        expires_at: token_data[:expires_at]
      }
    end
  end
  
  private
  
  def generate_token(service_name)
    "token-#{service_name}-#{SecureRandom.uuid}"
  end
end

class PolicyManager
  def initialize
    @policies = {}
    @mutex = Mutex.new
  end
  
  def add_policy(policy_name, policy_config)
    @mutex.synchronize do
      @policies[policy_name] = policy_config
    end
  end
  
  def authorize(request)
    @mutex.synchronize do
      @policies.values.any? do |policy|
        matches_policy?(request, policy)
      end
    end
  end
  
  private
  
  def matches_policy?(request, policy)
    return false unless request[:subject] == policy[:subject]
    return false unless request[:action] == policy[:action]
    return false unless request[:resource] == policy[:resource]
    
    conditions = policy[:conditions] || {}
    
    # Check method condition
    if conditions[:method]
      return false unless conditions[:method].include?(request[:method])
    end
    
    # Check path pattern condition
    if conditions[:path_pattern]
      pattern = conditions[:path_pattern].gsub('*', '.*')
      return false unless request[:path].match?(Regexp.new("^#{pattern}$"))
    end
    
    true
  end
end

class NetworkPolicyManager
  def initialize
    @policies = {}
    @mutex = Mutex.new
  end
  
  def add_policy(policy_name, policy_config)
    @mutex.synchronize do
      @policies[policy_name] = policy_config
    end
  end
  
  def allow_connection?(connection)
    @mutex.synchronize do
      # Check for explicit deny policies
      deny_policies = @policies.values.select { |policy| policy[:action] == 'deny' }
      
      deny_policies.each do |policy|
        if matches_connection?(connection, policy)
          return false
        end
      end
      
      # Check for allow policies
      allow_policies = @policies.values.select { |policy| policy[:action] != 'deny' }
      
      allow_policies.any? do |policy|
        matches_connection?(connection, policy)
      end
    end
  end
  
  private
  
  def matches_connection?(connection, policy)
    # Check source
    return false unless matches_source?(connection[:source], policy[:source])
    
    # Check destination
    return false unless matches_destination?(connection[:destination], policy[:destination])
    
    # Check ports
    if policy[:ports]
      return false unless policy[:ports].include?(connection[:port])
    end
    
    # Check protocols
    if policy[:protocols]
      return false unless policy[:protocols].include?(connection[:protocol])
    end
    
    true
  end
  
  def matches_source?(source, policy_source)
    policy_source == '*' || source == policy_source
  end
  
  def matches_destination?(destination, policy_destination)
    destination == policy_destination
  end
end

class SecretManager
  def initialize
    @secrets = {}
    @mutex = Mutex.new
  end
  
  def store_secret(name, value, type)
    @mutex.synchronize do
      secret_id = SecureRandom.uuid
      
      @secrets[secret_id] = {
        id: secret_id,
        name: name,
        value: encrypt_value(value),
        type: type,
        created_at: Time.now,
        version: 1
      }
      
      secret_id
    end
  end
  
  def get_secret(name)
    @mutex.synchronize do
      secret = @secrets.values.find { |s| s[:name] == name }
      return nil unless secret
      
      {
        id: secret[:id],
        name: secret[:name],
        value: decrypt_value(secret[:value]),
        type: secret[:type],
        created_at: secret[:created_at],
        version: secret[:version]
      }
    end
  end
  
  def get_secret_id(name)
    secret = get_secret(name)
    secret ? secret[:id] : nil
  end
  
  def rotate_secret(name, new_value)
    @mutex.synchronize do
      secret = @secrets.values.find { |s| s[:name] == name }
      return nil unless secret
      
      new_secret_id = SecureRandom.uuid
      
      @secrets[new_secret_id] = {
        id: new_secret_id,
        name: name,
        value: encrypt_value(new_value),
        type: secret[:type],
        created_at: Time.now,
        version: secret[:version] + 1
      }
      
      new_secret_id
    end
  end
  
  private
  
  def encrypt_value(value)
    # Simulate encryption
    "encrypted:#{Base64.strict_encode64(value)}"
  end
  
  def decrypt_value(encrypted_value)
    # Simulate decryption
    if encrypted_value.start_with?('encrypted:')
      Base64.strict_decode64(encrypted_value.sub('encrypted:', ''))
    else
      encrypted_value
    end
  end
end

class SecurityMonitor
  def initialize
    @events = []
    @metrics = {}
    @mutex = Mutex.new
  end
  
  def log_event(event)
    @mutex.synchronize do
      event[:id] = SecureRandom.uuid
      event[:logged_at] = Time.now
      @events << event
      
      # Update metrics
      update_metrics(event)
    end
  end
  
  def get_metrics
    @mutex.synchronize do
      @metrics.dup
    end
  end
  
  def generate_report
    @mutex.synchronize do
      total_events = @events.length
      auth_failures = @events.count { |e| e[:type] == 'authentication_failure' }
      auth_denials = @events.count { |e| e[:type] == 'authorization_denied' }
      policy_violations = @events.count { |e| e[:type] == 'policy_violation' }
      
      risk_level = calculate_risk_level(auth_failures, auth_denials, policy_violations, total_events)
      recommendations = generate_recommendations(auth_failures, auth_denials, policy_violations)
      
      {
        total_events: total_events,
        authentication_successes: @events.count { |e| e[:type] == 'authentication_success' },
        authentication_failures: auth_failures,
        authorization_denials: auth_denials,
        policy_violations: policy_violations,
        risk_level: risk_level,
        recommendations: recommendations
      }
    end
  end
  
  private
  
  def update_metrics(event)
    @metrics[event[:type]] ||= 0
    @metrics[event[:type]] += 1
  end
  
  def calculate_risk_level(failures, denials, violations, total)
    failure_rate = total > 0 ? (failures + denials + violations).to_f / total : 0
    
    case failure_rate
    when 0..0.1
      'low'
    when 0.1..0.3
      'medium'
    else
      'high'
    end
  end
  
  def generate_recommendations(failures, denials, violations)
    recommendations = []
    
    recommendations << 'Review authentication mechanisms' if failures > 10
    recommendations << 'Check authorization policies' if denials > 5
    recommendations << 'Audit network policies' if violations > 3
    recommendations << 'Monitor security events regularly' if failures + denials + violations > 20
    
    recommendations
  end
end
```

## 📊 Observability

### 5. Observability and Monitoring

Service mesh observability:

```ruby
class ServiceMeshObservability
  def self.demonstrate_observability
    puts "Service Mesh Observability Demonstration:"
    puts "=" * 50
    
    # 1. Distributed Tracing
    demonstrate_distributed_tracing
    
    # 2. Metrics Collection
    demonstrate_metrics_collection
    
    # 3. Logging and Analysis
    demonstrate_logging
    
    # 4. Health Monitoring
    demonstrate_health_monitoring
    
    # 5. Performance Analytics
    demonstrate_performance_analytics
    
    # 6. Dashboard and Visualization
    demonstrate_dashboard
  end
  
  def self.demonstrate_distributed_tracing
    puts "\n1. Distributed Tracing:"
    puts "=" * 30
    
    # Create trace manager
    trace_manager = TraceManager.new
    
    # Start trace
    trace_id = trace_manager.start_trace('user-registration-flow')
    
    # Simulate service calls
    puts "Simulating service calls with tracing:"
    
    # User service call
    user_span = trace_manager.start_span(trace_id, 'user-service', 'create-user')
    sleep(0.1)
    trace_manager.end_span(trace_id, user_span)
    
    # Order service call
    order_span = trace_manager.start_span(trace_id, 'order-service', 'create-initial-order')
    sleep(0.2)
    trace_manager.end_span(trace_id, order_span)
    
    # Notification service call
    notification_span = trace_manager.start_span(trace_id, 'notification-service', 'send-welcome-email')
    sleep(0.05)
    trace_manager.end_span(trace_id, notification_span)
    
    # End trace
    trace_manager.end_trace(trace_id)
    
    # Get trace details
    puts "\nTrace Details:"
    trace = trace_manager.get_trace(trace_id)
    puts "  Trace ID: #{trace[:id]}"
    puts "  Duration: #{trace[:duration]}ms"
    puts "  Spans: #{trace[:spans].length}"
    
    trace[:spans].each do |span|
      puts "    #{span[:service]}: #{span[:operation]} (#{span[:duration]}ms)"
    end
    
    puts "\nDistributed Tracing Features:"
    puts "- Trace correlation"
    puts "- Span tracking"
    puts "- Service dependencies"
    puts "- Performance analysis"
    puts "- Root cause analysis"
  end
  
  def self.demonstrate_metrics_collection
    puts "\n2. Metrics Collection:"
    puts "=" * 30
    
    # Create metrics collector
    metrics_collector = MetricsCollector.new
    
    # Simulate metrics collection
    puts "Collecting metrics:"
    
    # Request metrics
    10.times do |i|
      metrics_collector.record_metric('request_count', 1, {
        service: 'user-service',
        method: 'POST',
        status: '200'
      })
      
      metrics_collector.record_metric('request_duration', rand(10..200), {
        service: 'user-service',
        method: 'POST'
      })
    end
    
    # Error metrics
    2.times do
      metrics_collector.record_metric('error_count', 1, {
        service: 'order-service',
        error_type: 'timeout'
      })
    end
    
    # Connection metrics
    metrics_collector.record_metric('active_connections', 25, {
      service: 'user-service'
    })
    
    metrics_collector.record_metric('active_connections', 15, {
      service: 'order-service'
    })
    
    # Get aggregated metrics
    puts "\nAggregated Metrics:"
    
    request_count = metrics_collector.get_metric('request_count')
    puts "  Total requests: #{request_count[:value]}"
    
    request_duration = metrics_collector.get_metric('request_duration')
    puts "  Average request duration: #{request_duration[:value].round(2)}ms"
    
    error_count = metrics_collector.get_metric('error_count')
    puts "  Total errors: #{error_count[:value]}"
    
    active_connections = metrics_collector.get_metric('active_connections')
    puts "  Active connections: #{active_connections[:value]}"
    
    puts "\nMetrics Collection Features:"
    puts "- Counter metrics"
    puts "- Gauge metrics"
    puts "- Histogram metrics"
    puts "- Tag-based aggregation"
    puts "- Real-time collection"
  end
  
  def self.demonstrate_logging
    puts "\n3. Logging and Analysis:"
    puts "=" * 30
    
    # Create log manager
    log_manager = LogManager.new
    
    # Simulate log events
    puts "Generating log events:"
    
    log_events = [
      { level: 'INFO', service: 'user-service', message: 'User created successfully', user_id: 123 },
      { level: 'WARN', service: 'order-service', message: 'Order processing slow', order_id: 456 },
      { level: 'ERROR', service: 'notification-service', message: 'Email sending failed', error: 'SMTP timeout' },
      { level: 'INFO', service: 'user-service', message: 'User authenticated', user_id: 789 },
      { level: 'DEBUG', service: 'order-service', message: 'Database query executed', query_time: 15 }
    ]
    
    log_events.each_with_index do |event, i|
      log_manager.log_event(event)
      puts "  #{i + 1}. [#{event[:level]}] #{event[:service]}: #{event[:message]}"
    end
    
    # Analyze logs
    puts "\nLog Analysis:"
    
    log_stats = log_manager.get_statistics
    puts "  Total logs: #{log_stats[:total_logs]}"
    puts "  ERROR logs: #{log_stats[:error_count]}"
    puts "  WARN logs: #{log_stats[:warn_count]}"
    puts "  INFO logs: #{log_stats[:info_count]}"
    puts "  DEBUG logs: #{log_stats[:debug_count]}"
    
    # Search logs
    puts "\nLog Search:"
    
    error_logs = log_manager.search_logs('level', 'ERROR')
    puts "  ERROR logs: #{error_logs.length}"
    
    user_service_logs = log_manager.search_logs('service', 'user-service')
    puts "  User service logs: #{user_service_logs.length}"
    
    puts "\nLogging Features:"
    puts "- Structured logging"
    puts "- Log aggregation"
    puts "- Log searching"
    puts "- Log analysis"
    puts "- Log retention"
  end
  
  def self.demonstrate_health_monitoring
    puts "\n4. Health Monitoring:"
    puts "=" * 30
    
    # Create health monitor
    health_monitor = HealthMonitor.new
    
    # Register services
    services = ['user-service', 'order-service', 'notification-service']
    
    services.each do |service|
      health_monitor.register_service(service)
    end
    
    # Simulate health checks
    puts "Performing health checks:"
    
    services.each do |service|
      health_status = health_monitor.check_health(service)
      puts "  #{service}: #{health_status[:status]}"
      puts "    Response time: #{health_status[:response_time]}ms"
      puts "    Last check: #{health_status[:last_check]}"
    end
    
    # Get overall health
    puts "\nOverall Health:"
    overall_health = health_monitor.get_overall_health
    puts "  Status: #{overall_health[:status]}"
    puts "  Healthy services: #{overall_health[:healthy_services]}/#{overall_health[:total_services]}"
    puts "  Unhealthy services: #{overall_health[:unhealthy_services]}"
    
    # Get health history
    puts "\nHealth History:"
    history = health_monitor.get_health_history('user-service', 5)
    history.each do |status|
      puts "  #{status[:timestamp]}: #{status[:status]}"
    end
    
    puts "\nHealth Monitoring Features:"
    puts "- Service health checks"
    puts "- Response time tracking"
    puts "- Health status history"
    puts "- Automatic recovery"
    puts "- Health alerts"
  end
  
  def self.demonstrate_performance_analytics
    puts "\n5. Performance Analytics:"
    puts "=" * 30
    
    # Create performance analyzer
    analyzer = PerformanceAnalyzer.new
    
    # Simulate performance data
    puts "Analyzing performance data:"
    
    # Request performance data
    20.times do |i|
      analyzer.record_request({
        service: ['user-service', 'order-service', 'notification-service'].sample,
        method: ['GET', 'POST', 'PUT', 'DELETE'].sample,
        duration: rand(10..500),
        status: rand > 0.1 ? '200' : '500',
        timestamp: Time.now - rand(3600)
      })
    end
    
    # Get performance insights
    puts "\nPerformance Insights:"
    
    # Service performance
    service_performance = analyzer.get_service_performance
    service_performance.each do |service, metrics|
      puts "  #{service}:"
      puts "    Average response time: #{metrics[:avg_duration].round(2)}ms"
      puts "    Request count: #{metrics[:request_count]}"
      puts "    Error rate: #{metrics[:error_rate].round(2)}%"
      puts "    P95 duration: #{metrics[:p95_duration].round(2)}ms"
    end
    
    # Method performance
    method_performance = analyzer.get_method_performance
    puts "\nMethod Performance:"
    method_performance.each do |method, metrics|
      puts "  #{method}: #{metrics[:avg_duration].round(2)}ms avg"
    end
    
    # Trend analysis
    puts "\nTrend Analysis:"
    trend = analyzer.get_trend_analysis('user-service', 'last_hour')
    puts "  User service trend: #{trend[:direction]}"
    puts "  Change: #{trend[:change_percentage].round(2)}%"
    
    puts "\nPerformance Analytics Features:"
    puts "- Response time analysis"
    puts "- Error rate tracking"
    puts "- Performance trends"
    puts "- Service comparison"
    puts "- Anomaly detection"
  end
  
  def self.demonstrate_dashboard
    puts "\n6. Dashboard and Visualization:"
    puts "=" * 30
    
    # Create dashboard
    dashboard = ServiceMeshDashboard.new
    
    # Add widgets
    dashboard.add_widget('service_health', 'Service Health')
    dashboard.add_widget('request_metrics', 'Request Metrics')
    dashboard.add_widget('error_rates', 'Error Rates')
    dashboard.add_widget('response_times', 'Response Times')
    
    # Simulate dashboard data
    puts "Dashboard Data:"
    
    dashboard_data = dashboard.get_dashboard_data
    dashboard_data.each do |widget|
      puts "  #{widget[:name]}: #{widget[:value]}"
    end
    
    # Render dashboard
    puts "\nDashboard Visualization:"
    dashboard.render
    
    puts "\nDashboard Features:"
    puts "- Real-time widgets"
    puts "- Interactive charts"
    puts "- Customizable layouts"
    puts "- Alert integration"
    puts "- Export capabilities"
  end
  
  private
  
  def self.simulate_delay(seconds)
    sleep(seconds)
  end
end

class TraceManager
  def initialize
    @traces = {}
    @mutex = Mutex.new
  end
  
  def start_trace(trace_name)
    @mutex.synchronize do
      trace_id = SecureRandom.uuid
      
      @traces[trace_id] = {
        id: trace_id,
        name: trace_name,
        started_at: Time.now,
        spans: []
      }
      
      trace_id
    end
  end
  
  def start_span(trace_id, service, operation)
    @mutex.synchronize do
      trace = @traces[trace_id]
      return nil unless trace
      
      span_id = SecureRandom.uuid
      span = {
        id: span_id,
        service: service,
        operation: operation,
        started_at: Time.now
      }
      
      trace[:spans] << span
      span_id
    end
  end
  
  def end_span(trace_id, span_id)
    @mutex.synchronize do
      trace = @traces[trace_id]
      return unless trace
      
      span = trace[:spans].find { |s| s[:id] == span_id }
      return unless span
      
      span[:ended_at] = Time.now
      span[:duration] = ((span[:ended_at] - span[:started_at]) * 1000).round(2)
    end
  end
  
  def end_trace(trace_id)
    @mutex.synchronize do
      trace = @traces[trace_id]
      return unless trace
      
      trace[:ended_at] = Time.now
      trace[:duration] = ((trace[:ended_at] - trace[:started_at]) * 1000).round(2)
    end
  end
  
  def get_trace(trace_id)
    @mutex.synchronize do
      @traces[trace_id]
    end
  end
end

class MetricsCollector
  def initialize
    @metrics = {}
    @mutex = Mutex.new
  end
  
  def record_metric(name, value, tags = {})
    @mutex.synchronize do
      @metrics[name] ||= []
      
      metric = {
        value: value,
        tags: tags,
        timestamp: Time.now
      }
      
      @metrics[name] << metric
    end
  end
  
  def get_metric(name)
    @mutex.synchronize do
      values = @metrics[name] || []
      
      if values.empty?
        { value: 0, count: 0 }
      else
        total = values.sum { |m| m[:value] }
        count = values.length
        average = count > 0 ? total.to_f / count : 0
        
        {
          value: average.round(2),
          count: count,
          total: total
        }
      end
    end
  end
end

class LogManager
  def initialize
    @logs = []
    @mutex = Mutex.new
  end
  
  def log_event(event)
    @mutex.synchronize do
      event[:timestamp] = Time.now
      event[:id] = SecureRandom.uuid
      @logs << event
    end
  end
  
  def search_logs(field, value)
    @mutex.synchronize do
      @logs.select { |log| log[field] == value }
    end
  end
  
  def get_statistics
    @mutex.synchronize do
      total_logs = @logs.length
      
      {
        total_logs: total_logs,
        error_count: @logs.count { |log| log[:level] == 'ERROR' },
        warn_count: @logs.count { |log| log[:level] == 'WARN' },
        info_count: @logs.count { |log| log[:level] == 'INFO' },
        debug_count: @logs.count { |log| log[:level] == 'DEBUG' }
      }
    end
  end
end

class HealthMonitor
  def initialize
    @services = {}
    @health_history = {}
    @mutex = Mutex.new
  end
  
  def register_service(service_name)
    @mutex.synchronize do
      @services[service_name] = {
        name: service_name,
        status: 'unknown',
        last_check: nil,
        response_time: nil
      }
      
      @health_history[service_name] = []
    end
  end
  
  def check_health(service_name)
    @mutex.synchronize do
      service = @services[service_name]
      return nil unless service
      
      # Simulate health check
      start_time = Time.now
      
      # Simulate response time
      response_time = rand(10..200)
      
      # Determine status based on response time
      status = case response_time
              when 0..50
                'healthy'
              when 51..150
                'degraded'
              else
                'unhealthy'
              end
      
      service[:status] = status
      service[:last_check] = Time.now
      service[:response_time] = response_time
      
      # Add to history
      @health_history[service_name] << {
        status: status,
        timestamp: Time.now,
        response_time: response_time
      }
      
      # Keep only last 100 entries
      if @health_history[service_name].length > 100
        @health_history[service_name] = @health_history[service_name].last(100)
      end
      
      service.dup
    end
  end
  
  def get_overall_health
    @mutex.synchronize do
      total_services = @services.length
      healthy_services = @services.values.count { |s| s[:status] == 'healthy' }
      unhealthy_services = @services.values.count { |s| s[:status] == 'unhealthy' }
      
      status = if healthy_services == total_services
                'healthy'
              elsif unhealthy_services > 0
                'unhealthy'
              else
                'degraded'
              end
      
      {
        status: status,
        total_services: total_services,
        healthy_services: healthy_services,
        unhealthy_services: unhealthy_services
      }
    end
  end
  
  def get_health_history(service_name, limit = 10)
    @mutex.synchronize do
      history = @health_history[service_name] || []
      history.last(limit)
    end
  end
end

class PerformanceAnalyzer
  def initialize
    @requests = []
    @mutex = Mutex.new
  end
  
  def record_request(request_data)
    @mutex.synchronize do
      @requests << request_data
    end
  end
  
  def get_service_performance
    @mutex.synchronize do
      service_data = @requests.group_by { |r| r[:service] }
      
      service_data.transform_values do |requests|
        durations = requests.map { |r| r[:duration] }
        errors = requests.count { |r| r[:status] != '200' }
        
        {
          avg_duration: durations.sum.to_f / durations.length,
          request_count: requests.length,
          error_rate: (errors.to_f / requests.length * 100),
          p95_duration: durations.sort[(durations.length * 0.95).to_i]
        }
      end
    end
  end
  
  def get_method_performance
    @mutex.synchronize do
      method_data = @requests.group_by { |r| r[:method] }
      
      method_data.transform_values do |requests|
        durations = requests.map { |r| r[:duration] }
        durations.sum.to_f / durations.length
      end
    end
  end
  
  def get_trend_analysis(service, time_range)
    @mutex.synchronize do
      service_requests = @requests.select { |r| r[:service] == service }
      
      # Simple trend analysis
      recent_requests = service_requests.last(10)
      older_requests = service_requests[0..9]
      
      if recent_requests.any? && older_requests.any?
        recent_avg = recent_requests.map { |r| r[:duration] }.sum.to_f / recent_requests.length
        older_avg = older_requests.map { |r| r[:duration] }.sum.to_f / older_requests.length
        
        change_percentage = ((recent_avg - older_avg) / older_avg * 100).round(2)
        
        direction = if change_percentage > 5
                     'increasing'
                   elsif change_percentage < -5
                     'decreasing'
                   else
                     'stable'
                   end
        
        {
          direction: direction,
          change_percentage: change_percentage
        }
      else
        { direction: 'unknown', change_percentage: 0 }
      end
    end
  end
end

class ServiceMeshDashboard
  def initialize
    @widgets = []
    @mutex = Mutex.new
  end
  
  def add_widget(widget_id, widget_name)
    @mutex.synchronize do
      @widgets << {
        id: widget_id,
        name: widget_name,
        value: generate_widget_value(widget_id)
      }
    end
  end
  
  def get_dashboard_data
    @mutex.synchronize do
      @widgets.map do |widget|
        {
          id: widget[:id],
          name: widget[:name],
          value: widget[:value]
        }
      end
    end
  end
  
  def render
    puts "\nService Mesh Dashboard:"
    puts "=" * 30
    
    @widgets.each do |widget|
      puts "#{widget[:name]}: #{widget[:value]}"
    end
  end
  
  private
  
  def generate_widget_value(widget_id)
    case widget_id
    when 'service_health'
      "#{rand(80..100)}% healthy"
    when 'request_metrics'
      "#{rand(1000..5000)} req/s"
    when 'error_rates'
      "#{rand(0..5)}%"
    when 'response_times'
      "#{rand(50..200)}ms"
    else
      'N/A'
    end
  end
end
```

## 🎯 Exercises

### Beginner Exercises

1. **Basic Service Mesh**: Create simple service mesh
2. **Service Registration**: Implement service discovery
3. **Load Balancing**: Add load balancing
4. **Health Checks**: Implement health monitoring

### Intermediate Exercises

1. **Traffic Management**: Advanced traffic routing
2. **Security**: Add mTLS and authentication
3. **Observability**: Implement tracing and metrics
4. **Policy Engine**: Add policy management

### Advanced Exercises

1. **Enterprise Service Mesh**: Production-ready mesh
2. **Multi-Cluster**: Multi-cluster service mesh
3. **Performance Optimization**: Optimize performance
4. **Custom Control Plane**: Build custom control plane

---

## 🎯 Summary

Service Mesh in Ruby provides:

- **Service Mesh Fundamentals** - Core concepts and architecture
- **Service Mesh Implementation** - Ruby service mesh system
- **Traffic Management** - Advanced traffic routing and management
- **Security and Policies** - Comprehensive security implementation
- **Observability** - Monitoring and analytics
- **Enterprise Features** - Production-ready capabilities

Master these service mesh techniques for microservices architecture!
