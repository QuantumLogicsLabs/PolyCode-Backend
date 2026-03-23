# Monitoring and Logging in Ruby
# Comprehensive guide to application monitoring, logging, and observability

## 📊 Monitoring Fundamentals

### 1. Monitoring Concepts

Core monitoring principles:

```ruby
class MonitoringFundamentals
  def self.explain_monitoring_concepts
    puts "Monitoring Fundamentals:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Application Monitoring",
        description: "Tracking application performance and health",
        types: ["Performance monitoring", "Error monitoring", "Business metrics"],
        benefits: ["Proactive issue detection", "Performance optimization", "User experience"],
        components: ["Metrics collection", "Alerting", "Visualization", "Analysis"]
      },
      {
        concept: "Observability",
        description: "Understanding system behavior from external outputs",
        pillars: ["Logs", "Metrics", "Traces"],
        benefits: ["System understanding", "Debugging", "Performance analysis"],
        tools: ["ELK Stack", "Prometheus", "Jaeger", "Grafana"]
      },
      {
        concept: "Metrics",
        description: "Quantitative measurements of system behavior",
        types: ["Counter", "Gauge", "Histogram", "Summary"],
        characteristics: ["Numerical", "Time-series", "Aggregatable", "Queryable"],
        examples: ["Request count", "Response time", "Error rate", "Memory usage"]
      },
      {
        concept: "Logging",
        description: "Recording system events and messages",
        levels: ["DEBUG", "INFO", "WARN", "ERROR", "FATAL"],
        formats: ["Structured", "Unstructured", "JSON", "Plain text"],
        purposes: ["Debugging", "Auditing", "Monitoring", "Compliance"]
      },
      {
        concept: "Tracing",
        description: "Tracking requests through distributed systems",
        concepts: ["Trace ID", "Span ID", "Parent-child", "Service graph"],
        benefits: ["Distributed debugging", "Performance analysis", "Dependency mapping"],
        tools: ["OpenTelemetry", "Jaeger", "Zipkin", "AWS X-Ray"]
      },
      {
        concept: "Alerting",
        description: "Automated notification of system issues",
        types: ["Threshold-based", "Anomaly detection", "Rate-based", "Pattern-based"],
        channels: ["Email", "Slack", "SMS", "PagerDuty"],
        strategies: ["Severity levels", "Escalation", "Suppression", "Grouping"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Components: #{concept[:components].join(', ')}" if concept[:components]
      puts "  Pillars: #{concept[:pillars].join(', ')}" if concept[:pillars]
      puts "  Characteristics: #{concept[:characteristics].join(', ')}" if concept[:characteristics]
      puts "  Examples: #{concept[:examples].join(', ')}" if concept[:examples]
      puts "  Tools: #{concept[:tools].join(', ')}" if concept[:tools]
      puts "  Levels: #{concept[:levels].join(', ')}" if concept[:levels]
      puts "  Formats: #{concept[:formats].join(', ')}" if concept[:formats]
      puts "  Purposes: #{concept[:purposes].join(', ')}" if concept[:purposes]
      puts "  Concepts: #{concept[:concepts].join(', ')}" if concept[:concepts]
      puts "  Channels: #{concept[:channels].join(', ')}" if concept[:channels]
      puts "  Strategies: #{concept[:strategies].join(', ')}" if concept[:strategies]
      puts
    end
  end
  
  def self.monitoring_lifecycle
    puts "\nMonitoring Lifecycle:"
    puts "=" * 50
    
    lifecycle = [
      {
        phase: "1. Data Collection",
        description: "Collect monitoring data from various sources",
        sources: ["Application metrics", "System metrics", "Logs", "Traces"],
        methods: ["Instrumentation", "Agents", "Collectors", "Scraping"],
        frequency: "Continuous"
      },
      {
        phase: "2. Data Processing",
        description: "Process and aggregate collected data",
        operations: ["Aggregation", "Filtering", "Transformation", "Enrichment"],
        tools: ["Prometheus", "Logstash", "Fluentd", "Vector"],
        latency: "Real-time to batch"
      },
      {
        phase: "3. Storage",
        description: "Store processed data for analysis",
        systems: ["Time-series databases", "Log stores", "Trace stores", "Object storage"],
        retention: ["Hot storage", "Cold storage", "Archive", "Deletion"],
        performance: ["Indexing", "Compression", "Sharding", "Replication"]
      },
      {
        phase: "4. Analysis",
        description: "Analyze data to extract insights",
        techniques: ["Statistical analysis", "Pattern recognition", "Anomaly detection", "Trend analysis"],
        tools: ["Grafana", "Kibana", "Custom dashboards", "ML models"],
        output: ["Alerts", "Reports", "Visualizations", "Insights"]
      },
      {
        phase: "5. Alerting",
        description: "Generate alerts based on analysis",
        triggers: ["Thresholds", "Patterns", "Anomalies", "Rate changes"],
        delivery: ["Email", "Slack", "PagerDuty", "Webhooks"],
        management: ["Escalation", "Suppression", "Grouping", "Scheduling"]
      },
      {
        phase: "6. Response",
        description: "Respond to alerts and incidents",
        actions: ["Investigation", "Mitigation", "Resolution", "Documentation"],
        tools: ["Runbooks", "Automation", "ChatOps", "Incident management"],
        feedback: ["Alert tuning", "Process improvement", "System optimization"]
      }
    ]
    
    lifecycle.each do |phase|
      puts "#{phase[:phase]}: #{phase[:description]}"
      puts "  Sources: #{phase[:sources].join(', ')}" if phase[:sources]
      puts "  Methods: #{phase[:methods].join(', ')}" if phase[:methods]
      puts "  Tools: #{phase[:tools].join(', ')}" if phase[:tools]
      puts "  Operations: #{phase[:operations].join(', ')}" if phase[:operations]
      puts "  Systems: #{phase[:systems].join(', ')}" if phase[:systems]
      puts "  Techniques: #{phase[:techniques].join(', ')}" if phase[:techniques]
      puts "  Triggers: #{phase[:triggers].join(', ')}" if phase[:triggers]
      puts "  Delivery: #{phase[:delivery].join(', ')}" if phase[:delivery]
      puts "  Actions: #{phase[:actions].join(', ')}" if phase[:actions]
      puts "  Frequency: #{phase[:frequency]}" if phase[:frequency]
      puts "  Latency: #{phase[:latency]}" if phase[:latency]
      puts "  Retention: #{phase[:retention].join(', ')}" if phase[:retention]
      puts "  Performance: #{phase[:performance].join(', ')}" if phase[:performance]
      puts "  Output: #{phase[:output].join(', ')}" if phase[:output]
      puts "  Management: #{phase[:management].join(', ')}" if phase[:management]
      puts "  Feedback: #{phase[:feedback].join(', ')}" if phase[:feedback]
      puts
    end
  end
  
  def self.monitoring_best_practices
    puts "\nMonitoring Best Practices:"
    puts "=" * 50
    
    practices = [
      {
        practice: "Define SLOs and SLIs",
        description: "Establish clear service level objectives and indicators",
        guidelines: [
          "Define measurable objectives",
          "Set realistic targets",
          "Monitor SLIs continuously",
          "Report on SLO compliance",
          "Review and adjust regularly"
        ],
        benefits: ["Clear expectations", "Performance focus", "Customer satisfaction"]
      },
      {
        practice: "Use Structured Logging",
        description: "Implement structured and consistent logging",
        guidelines: [
          "Use JSON format",
          "Include context information",
          "Log at appropriate levels",
          "Avoid sensitive data",
          "Use correlation IDs"
        ],
        benefits: ["Searchability", "Analysis", "Debugging", "Automation"]
      },
      {
        practice: "Implement Distributed Tracing",
        description: "Track requests across distributed systems",
        guidelines: [
          "Use OpenTelemetry",
          "Trace all services",
          "Include business context",
          "Sample traces appropriately",
          "Analyze trace data"
        ],
        benefits: ["Distributed debugging", "Performance analysis", "Dependency mapping"]
      },
      {
        practice: "Monitor the Right Metrics",
        description: "Focus on meaningful and actionable metrics",
        guidelines: [
          "Define key metrics",
          "Avoid vanity metrics",
          "Use leading indicators",
          "Monitor business metrics",
          "Track user experience"
        ],
        benefits: ["Actionable insights", "Business alignment", "Early detection"]
      },
      {
        practice: "Implement Alerting Strategy",
        description: "Design effective alerting and escalation",
        guidelines: [
          "Define alert thresholds",
          "Use severity levels",
          "Implement escalation",
          "Avoid alert fatigue",
          "Test alerting regularly"
        ],
        benefits: ["Timely response", "Reduced noise", "Effective escalation"]
      },
      {
        practice: "Create Dashboards",
        description: "Build informative and actionable dashboards",
        guidelines: [
          "Focus on key metrics",
          "Use visualizations",
          "Include context",
          "Make it actionable",
          "Regular review and update"
        ],
        benefits: ["Visibility", "Quick insights", "Data-driven decisions"]
      }
    ]
    
    practices.each do |practice|
      puts "#{practice[:practice]}:"
      puts "  Description: #{practice[:description]}"
      puts "  Guidelines: #{practice[:guidelines].join(', ')}"
      puts "  Benefits: #{practice[:benefits].join(', ')}"
      puts
    end
  end
  
  # Run monitoring fundamentals
  explain_monitoring_concepts
  monitoring_lifecycle
  monitoring_best_practices
end
```

### 2. Metrics Collection

Application metrics implementation:

```ruby
class MetricsCollector
  def initialize
    @counters = {}
    @gauges = {}
    @histograms = {}
    @summaries = {}
    @mutex = Mutex.new
  end
  
  def counter(name, labels = {})
    @mutex.synchronize do
      @counters[name] ||= {}
      key = labels_key(labels)
      @counters[name][key] ||= 0
    end
  end
  
  def increment_counter(name, value = 1, labels = {})
    @mutex.synchronize do
      @counters[name] ||= {}
      key = labels_key(labels)
      @counters[name][key] ||= 0
      @counters[name][key] += value
    end
  end
  
  def gauge(name, value, labels = {})
    @mutex.synchronize do
      @gauges[name] ||= {}
      key = labels_key(labels)
      @gauges[name][key] = value
    end
  end
  
  def set_gauge(name, value, labels = {})
    gauge(name, value, labels)
  end
  
  def histogram(name, value, labels = {})
    @mutex.synchronize do
      @histograms[name] ||= {}
      key = labels_key(labels)
      @histograms[name][key] ||= {
        count: 0,
        sum: 0,
        buckets: {}
      }
      
      hist = @histograms[name][key]
      hist[:count] += 1
      hist[:sum] += value
      
      # Update buckets (simplified)
      buckets = [0.1, 0.5, 1.0, 5.0, 10.0]
      buckets.each do |bucket|
        if value <= bucket
          hist[:buckets][bucket] ||= 0
          hist[:buckets][bucket] += 1
        end
      end
    end
  end
  
  def summary(name, value, labels = {})
    @mutex.synchronize do
      @summaries[name] ||= {}
      key = labels_key(labels)
      @summaries[name][key] ||= {
        count: 0,
        sum: 0,
        min: Float::INFINITY,
        max: -Float::INFINITY
      }
      
      summary = @summaries[name][key]
      summary[:count] += 1
      summary[:sum] += value
      summary[:min] = [summary[:min], value].min
      summary[:max] = [summary[:max], value].max
    end
  end
  
  def get_counter(name, labels = {})
    @mutex.synchronize do
      @counters[name]&.dig(labels_key(labels)) || 0
    end
  end
  
  def get_gauge(name, labels = {})
    @mutex.synchronize do
      @gauges[name]&.dig(labels_key(labels)) || 0
    end
  end
  
  def get_histogram(name, labels = {})
    @mutex.synchronize do
      @histograms[name]&.dig(labels_key(labels)) || {}
    end
  end
  
  def get_summary(name, labels = {})
    @mutex.synchronize do
      summary = @summaries[name]&.dig(labels_key(labels))
      return {} unless summary
      
      {
        count: summary[:count],
        sum: summary[:sum],
        min: summary[:min],
        max: summary[:max],
        avg: summary[:count] > 0 ? summary[:sum] / summary[:count] : 0
      }
    end
  end
  
  def reset_all
    @mutex.synchronize do
      @counters.clear
      @gauges.clear
      @histograms.clear
      @summaries.clear
    end
  end
  
  def get_all_metrics
    @mutex.synchronize do
      {
        counters: @counters,
        gauges: @gauges,
        histograms: @histograms,
        summaries: @summaries
      }
    end
  end
  
  def self.demonstrate_metrics
    puts "Metrics Collection Demonstration:"
    puts "=" * 50
    
    collector = MetricsCollector.new
    
    # Counter metrics
    puts "Counter Metrics:"
    collector.increment_counter('http_requests_total', 1, { method: 'GET', status: '200' })
    collector.increment_counter('http_requests_total', 1, { method: 'GET', status: '200' })
    collector.increment_counter('http_requests_total', 1, { method: 'POST', status: '201' })
    collector.increment_counter('http_requests_total', 1, { method: 'POST', status: '400' })
    
    puts "GET requests: #{collector.get_counter('http_requests_total', { method: 'GET' })}"
    puts "POST requests: #{collector.get_counter('http_requests_total', { method: 'POST' })}"
    puts "Total requests: #{collector.get_counter('http_requests_total')}"
    
    # Gauge metrics
    puts "\nGauge Metrics:"
    collector.set_gauge('active_connections', 25)
    collector.set_gauge('memory_usage', 512.5)
    collector.set_gauge('cpu_usage', 75.2)
    
    puts "Active connections: #{collector.get_gauge('active_connections')}"
    puts "Memory usage: #{collector.get_gauge('memory_usage')}MB"
    puts "CPU usage: #{collector.get_gauge('cpu_usage')}%"
    
    # Histogram metrics
    puts "\nHistogram Metrics:"
    [0.1, 0.2, 0.5, 1.0, 2.5, 5.0, 10.0].each do |response_time|
      collector.histogram('http_request_duration_seconds', response_time)
    end
    
    histogram = collector.get_histogram('http_request_duration_seconds')
    puts "Request count: #{histogram[:count]}"
    puts "Average duration: #{histogram[:sum] / histogram[:count]}s"
    puts "Buckets: #{histogram[:buckets]}"
    
    # Summary metrics
    puts "\nSummary Metrics:"
    [100, 150, 200, 250, 300, 350, 400].each do |response_size|
      collector.summary('http_response_size_bytes', response_size)
    end
    
    summary = collector.get_summary('http_response_size_bytes')
    puts "Response count: #{summary[:count]}"
    puts "Average size: #{summary[:avg]} bytes"
    puts "Min size: #{summary[:min]} bytes"
    puts "Max size: #{summary[:max]} bytes"
    
    # All metrics
    puts "\nAll Metrics:"
    all_metrics = collector.get_all_metrics
    puts "Counters: #{all_metrics[:counters].keys}"
    puts "Gauges: #{all_metrics[:gauges].keys}"
    puts "Histograms: #{all_metrics[:histograms].keys}"
    puts "Summaries: #{all_metrics[:summaries].keys}"
    
    puts "\nMetrics Collection Features:"
    puts "- Counter metrics (incrementing)"
    puts "- Gauge metrics (setting values)"
    puts "- Histogram metrics (distribution)"
    puts "- Summary metrics (statistics)"
    puts "- Label support for dimensions"
    puts "- Thread-safe operations"
    puts "- Metric aggregation"
  end
  
  private
  
  def labels_key(labels)
    labels.sort.map { |k, v| "#{k}=#{v}" }.join(',')
  end
end

class ApplicationMetrics
  def initialize(collector)
    @collector = collector
    @request_count = 0
    @error_count = 0
    @start_time = Time.now
  end
  
  def track_request(method, path, status, duration, response_size = nil)
    @request_count += 1
    
    # Request metrics
    @collector.increment_counter('http_requests_total', 1, {
      method: method,
      status: status.to_s,
      path: path
    })
    
    # Duration metrics
    @collector.histogram('http_request_duration_seconds', duration, {
      method: method,
      path: path
    })
    
    # Response size metrics
    if response_size
      @collector.summary('http_response_size_bytes', response_size, {
        method: method,
        path: path
      })
    end
    
    # Error tracking
    if status >= 400
      @error_count += 1
      @collector.increment_counter('http_errors_total', 1, {
        method: method,
        status: status.to_s,
        path: path
      })
    end
  end
  
  def track_database_query(query_type, duration, success = true)
    @collector.histogram('database_query_duration_seconds', duration, {
      query_type: query_type
    })
    
    unless success
      @collector.increment_counter('database_errors_total', 1, {
        query_type: query_type
      })
    end
  end
  
  def track_cache_operation(operation, cache_type, hit = true)
    @collector.increment_counter('cache_operations_total', 1, {
      operation: operation,
      cache_type: cache_type,
      hit: hit.to_s
    })
  end
  
  def track_background_job(job_name, duration, success = true)
    @collector.histogram('background_job_duration_seconds', duration, {
      job_name: job_name
    })
    
    unless success
      @collector.increment_counter('background_job_errors_total', 1, {
        job_name: job_name
      })
    end
  end
  
  def update_system_metrics
    # System metrics (simplified)
    memory_usage = get_memory_usage
    cpu_usage = get_cpu_usage
    disk_usage = get_disk_usage
    
    @collector.set_gauge('system_memory_usage_bytes', memory_usage)
    @collector.set_gauge('system_cpu_usage_percent', cpu_usage)
    @collector.set_gauge('system_disk_usage_percent', disk_usage)
    
    # Application metrics
    @collector.set_gauge('application_uptime_seconds', Time.now - @start_time)
    @collector.set_gauge('application_request_count', @request_count)
    @collector.set_gauge('application_error_count', @error_count)
  end
  
  def get_metrics_summary
    {
      requests: @request_count,
      errors: @error_count,
      error_rate: @request_count > 0 ? (@error_count.to_f / @request_count * 100).round(2) : 0,
      uptime: Time.now - @start_time,
      metrics: @collector.get_all_metrics
    }
  end
  
  def self.demonstrate_application_metrics
    puts "Application Metrics Demonstration:"
    puts "=" * 50
    
    collector = MetricsCollector.new
    app_metrics = ApplicationMetrics.new(collector)
    
    # Simulate requests
    puts "Simulating application requests:"
    
    requests = [
      { method: 'GET', path: '/api/users', status: 200, duration: 0.1, size: 1024 },
      { method: 'POST', path: '/api/users', status: 201, duration: 0.2, size: 512 },
      { method: 'GET', path: '/api/posts', status: 200, duration: 0.15, size: 2048 },
      { method: 'PUT', path: '/api/users/1', status: 200, duration: 0.25, size: 256 },
      { method: 'DELETE', path: '/api/users/2', status: 404, duration: 0.05, size: 0 },
      { method: 'GET', path: '/api/posts', status: 500, duration: 2.5, size: 0 }
    ]
    
    requests.each do |request|
      app_metrics.track_request(
        request[:method],
        request[:path],
        request[:status],
        request[:duration],
        request[:size]
      )
    end
    
    # Simulate database queries
    puts "\nSimulating database queries:"
    
    queries = [
      { type: 'SELECT', duration: 0.05, success: true },
      { type: 'INSERT', duration: 0.1, success: true },
      { type: 'UPDATE', duration: 0.08, success: true },
      { type: 'DELETE', duration: 0.03, success: true },
      { type: 'SELECT', duration: 1.5, success: false }
    ]
    
    queries.each do |query|
      app_metrics.track_database_query(
        query[:type],
        query[:duration],
        query[:success]
      )
    end
    
    # Simulate cache operations
    puts "\nSimulating cache operations:"
    
    cache_ops = [
      { operation: 'get', cache_type: 'redis', hit: true },
      { operation: 'set', cache_type: 'redis', hit: false },
      { operation: 'get', cache_type: 'memcached', hit: false },
      { operation: 'set', cache_type: 'memcached', hit: false },
      { operation: 'get', cache_type: 'redis', hit: true }
    ]
    
    cache_ops.each do |op|
      app_metrics.track_cache_operation(
        op[:operation],
        op[:cache_type],
        op[:hit]
      )
    end
    
    # Simulate background jobs
    puts "\nSimulating background jobs:"
    
    jobs = [
      { name: 'email_sender', duration: 2.5, success: true },
      { name: 'image_processor', duration: 5.0, success: true },
      { name: 'report_generator', duration: 10.0, success: false },
      { name: 'data_cleanup', duration: 15.0, success: true }
    ]
    
    jobs.each do |job|
      app_metrics.track_background_job(
        job[:name],
        job[:duration],
        job[:success]
      )
    end
    
    # Update system metrics
    puts "\nUpdating system metrics:"
    app_metrics.update_system_metrics
    
    # Get summary
    puts "\nMetrics Summary:"
    summary = app_metrics.get_metrics_summary
    summary.each do |key, value|
      case value
      when Hash
        puts "#{key}:"
        value.each { |k, v| puts "  #{k}: #{v}" }
      else
        puts "#{key}: #{value}"
      end
    end
    
    puts "\nApplication Metrics Features:"
    puts "- HTTP request tracking"
    puts "- Database query monitoring"
    puts "- Cache operation tracking"
    puts "- Background job monitoring"
    puts "- System metrics collection"
    puts "- Error rate calculation"
    puts "- Performance metrics"
  end
  
  private
  
  def get_memory_usage
    # Simulate memory usage
    rand(100..1000) * 1024 * 1024 # 100MB to 1GB
  end
  
  def get_cpu_usage
    # Simulate CPU usage
    rand(10..90)
  end
  
  def get_disk_usage
    # Simulate disk usage
    rand(20..80)
  end
end
```

## 📝 Logging System

### 3. Structured Logging

Advanced logging implementation:

```ruby
class StructuredLogger
  def initialize(name, level = :info)
    @name = name
    @level = level
    @outputs = []
    @context = {}
    @mutex = Mutex.new
  end
  
  attr_reader :name, :level
  
  def add_output(output)
    @outputs << output
    self
  end
  
  def with_context(context)
    @mutex.synchronize do
      old_context = @context.dup
      @context.merge!(context)
      yield
    ensure
      @context = old_context
    end
  end
  
  def debug(message, context = {})
    log(:debug, message, context)
  end
  
  def info(message, context = {})
    log(:info, message, context)
  end
  
  def warn(message, context = {})
    log(:warn, message, context)
  end
  
  def error(message, context = {})
    log(:error, message, context)
  end
  
  def fatal(message, context = {})
    log(:fatal, message, context)
  end
  
  def log(level, message, context = {})
    return unless should_log?(level)
    
    log_entry = create_log_entry(level, message, context)
    
    @outputs.each do |output|
      output.write(log_entry)
    end
  end
  
  def self.demonstrate_structured_logging
    puts "Structured Logging Demonstration:"
    puts "=" * 50
    
    # Create logger with multiple outputs
    logger = StructuredLogger.new('myapp', :debug)
    
    # Add console output
    logger.add_output(ConsoleOutput.new)
    
    # Add file output
    logger.add_output(FileOutput.new('app.log'))
    
    # Add JSON output
    logger.add_output(JsonOutput.new('app.json'))
    
    # Log different levels
    logger.debug("Debug message", { user_id: 123, action: 'login' })
    logger.info("User logged in", { user_id: 123, ip: '192.168.1.1' })
    logger.warn("Deprecated API used", { api: 'old_api', version: '1.0' })
    logger.error("Database connection failed", { error: 'Connection timeout', retries: 3 })
    
    # Log with context
    logger.with_context({ request_id: 'abc123', user_id: 123 }) do
      logger.info("Processing request", { endpoint: '/api/users' })
      logger.debug("Query executed", { query: 'SELECT * FROM users', duration: 0.05 })
      logger.info("Request completed", { status: 200, duration: 0.1 })
    end
    
    puts "\nStructured Logging Features:"
    puts "- Multiple output formats"
    puts "- Structured context"
    puts "- Log levels"
    puts "- Thread safety"
    puts "- Context inheritance"
    puts "- Performance optimization"
  end
  
  private
  
  def should_log?(level)
    level_severity = {
      debug: 0,
      info: 1,
      warn: 2,
      error: 3,
      fatal: 4
    }
    
    level_severity[level] >= level_severity[@level]
  end
  
  def create_log_entry(level, message, context)
    {
      timestamp: Time.now.utc.iso8601,
      level: level.to_s.upcase,
      logger: @name,
      message: message,
      context: @context.merge(context),
      thread_id: Thread.current.object_id,
      process_id: Process.pid
    }
  end
end

class ConsoleOutput
  def write(log_entry)
    level = log_entry[:level]
    timestamp = log_entry[:timestamp]
    message = log_entry[:message]
    context = log_entry[:context]
    
    # Colorize output
    color = case level
            when 'DEBUG'
              :light_blue
            when 'INFO'
              :green
            when 'WARN'
              :yellow
            when 'ERROR'
              :red
            when 'FATAL'
              :magenta
            else
              :white
            end
    
    puts "#{timestamp} [#{level}] #{message}"
    
    if context.any?
      context.each do |key, value|
        puts "    #{key}: #{value}"
      end
    end
  end
end

class FileOutput
  def initialize(filename)
    @filename = filename
    @file = File.open(filename, 'a')
  end
  
  def write(log_entry)
    @file.puts(log_entry.to_json)
    @file.flush
  rescue => e
    puts "Failed to write to file: #{e.message}"
  end
  
  def close
    @file.close
  end
end

class JsonOutput
  def initialize(filename)
    @filename = filename
    @file = File.open(filename, 'a')
  end
  
  def write(log_entry)
    @file.puts(log_entry.to_json)
    @file.flush
  rescue => e
    puts "Failed to write JSON to file: #{e.message}"
  end
  
  def close
    @file.close
  end
end

class RequestLogger
  def initialize(logger)
    @logger = logger
    @request_id = SecureRandom.uuid
  end
  
  def log_request(request)
    @logger.with_context({
      request_id: @request_id,
      method: request.method,
      path: request.path,
      ip: request.ip,
      user_agent: request.user_agent
    }) do
      @logger.info("Request started", {
        headers: request.headers,
        params: request.params
      })
    end
  end
  
  def log_response(response, duration)
    @logger.with_context({
      request_id: @request_id,
      status: response.status,
      content_length: response.content_length
    }) do
      @logger.info("Request completed", {
        duration: duration,
        response_headers: response.headers
      })
    end
  end
  
  def log_error(error)
    @logger.with_context({
      request_id: @request_id,
      error_class: error.class.name,
      error_message: error.message
    }) do
      @logger.error("Request failed", {
        backtrace: error.backtrace,
        context: error.respond_to?(:context) ? error.context : {}
      })
    end
  end
  
  def self.demonstrate_request_logging
    puts "Request Logging Demonstration:"
    puts "=" * 50
    
    # Create logger
    logger = StructuredLogger.new('webapp', :debug)
    logger.add_output(ConsoleOutput.new)
    
    # Create request logger
    request_logger = RequestLogger.new(logger)
    
    # Simulate request
    request = OpenStruct.new(
      method: 'GET',
      path: '/api/users/123',
      ip: '192.168.1.1',
      user_agent: 'Mozilla/5.0',
      headers: { 'Authorization' => 'Bearer token123' },
      params: { 'include' => 'profile' }
    )
    
    # Simulate response
    response = OpenStruct.new(
      status: 200,
      content_length: 1024,
      headers: { 'Content-Type' => 'application/json' }
    )
    
    # Log request
    request_logger.log_request(request)
    
    # Simulate processing
    sleep(0.1)
    
    # Log response
    request_logger.log_response(response, 0.1)
    
    # Simulate error
    error = StandardError.new("Database connection failed")
    request_logger.log_error(error)
    
    puts "\nRequest Logging Features:"
    puts "- Request tracking"
    puts "- Response logging"
    puts "- Error handling"
    puts "- Request ID correlation"
    puts "- Context inheritance"
    puts "- Performance measurement"
  end
end

class AuditLogger
  def initialize(logger)
    @logger = logger
  end
  
  def log_user_action(user_id, action, resource, details = {})
    @logger.info("User action", {
      user_id: user_id,
      action: action,
      resource: resource,
      details: details,
      audit: true
    })
  end
  
  def log_security_event(event_type, details = {})
    @logger.warn("Security event", {
      event_type: event_type,
      details: details,
      security: true
    })
  end
  
  def log_data_access(user_id, data_type, record_id, action = 'read')
    @logger.info("Data access", {
      user_id: user_id,
      data_type: data_type,
      record_id: record_id,
      action: action,
      audit: true
    })
  end
  
  def log_api_call(user_id, endpoint, method, params = {}, status = nil)
    @logger.info("API call", {
      user_id: user_id,
      endpoint: endpoint,
      method: method,
      params: params,
      status: status,
      audit: true
    })
  end
  
  def self.demonstrate_audit_logging
    puts "Audit Logging Demonstration:"
    puts "=" * 50
    
    # Create logger
    logger = StructuredLogger.new('audit', :info)
    logger.add_output(ConsoleOutput.new)
    logger.add_output(FileOutput.new('audit.log'))
    
    # Create audit logger
    audit_logger = AuditLogger.new(logger)
    
    # Log user actions
    audit_logger.log_user_action(123, 'create', 'user', {
      username: 'john_doe',
      email: 'john@example.com'
    })
    
    audit_logger.log_user_action(123, 'update', 'user', {
      user_id: 123,
      changes: { email: 'john.doe@example.com' }
    })
    
    # Log security events
    audit_logger.log_security_event('login_failed', {
      user_id: 123,
      ip: '192.168.1.1',
      reason: 'invalid_password'
    })
    
    audit_logger.log_security_event('privilege_escalation', {
      user_id: 123,
      from_role: 'user',
      to_role: 'admin'
    })
    
    # Log data access
    audit_logger.log_data_access(123, 'user_profile', 456)
    audit_logger.log_data_access(123, 'user_profile', 456, 'update')
    audit_logger.log_data_access(123, 'user_profile', 456, 'delete')
    
    # Log API calls
    audit_logger.log_api_call(123, '/api/users', 'GET', { page: 1 }, 200)
    audit_logger.log_api_call(123, '/api/users', 'POST', { name: 'test' }, 201)
    
    puts "\nAudit Logging Features:"
    puts "- User action tracking"
    puts "- Security event logging"
    puts "- Data access monitoring"
    puts "- API call logging"
    puts "- Compliance support"
    puts "- Audit trail maintenance"
  end
end
```

## 📊 Monitoring Dashboard

### 4. Dashboard Creation

Visualization and alerting:

```ruby
class MonitoringDashboard
  def initialize(name, refresh_interval = 30)
    @name = name
    @refresh_interval = refresh_interval
    @panels = []
    @alerts = []
    @data_sources = {}
    @refresh_thread = nil
    @running = false
  end
  
  attr_reader :name, :panels, :alerts
  
  def add_panel(panel)
    @panels << panel
    panel
  end
  
  def add_alert(alert)
    @alerts << alert
    alert
  end
  
  def add_data_source(name, data_source)
    @data_sources[name] = data_source
  end
  
  def start
    return if @running
    
    @running = true
    puts "Starting dashboard: #{@name}"
    
    @refresh_thread = Thread.new do
      while @running
        refresh_data
        check_alerts
        sleep(@refresh_interval)
      end
    end
  end
  
  def stop
    @running = false
    @refresh_thread&.join
    puts "Dashboard stopped: #{@name}"
  end
  
  def refresh_data
    @panels.each do |panel|
      data = fetch_panel_data(panel)
      panel.update_data(data)
    end
  end
  
  def check_alerts
    @alerts.each do |alert|
      data = fetch_alert_data(alert)
      alert.check(data)
    end
  end
  
  def get_dashboard_data
    {
      name: @name,
      refresh_interval: @refresh_interval,
      panels: @panels.map(&:to_h),
      alerts: @alerts.map(&:to_h),
      last_updated: Time.now
    }
  end
  
  def self.demonstrate_dashboard
    puts "Monitoring Dashboard Demonstration:"
    puts "=" * 50
    
    # Create dashboard
    dashboard = MonitoringDashboard.new('Production Dashboard', 30)
    
    # Add data sources
    dashboard.add_data_source('prometheus', PrometheusDataSource.new)
    dashboard.add_data_source('elasticsearch', ElasticsearchDataSource.new)
    
    # Add panels
    dashboard.add_panel(MetricPanel.new('Request Rate', 'requests_per_second', 'line'))
    dashboard.add_panel(MetricPanel.new('Response Time', 'response_time', 'gauge'))
    dashboard.add_panel(MetricPanel.new('Error Rate', 'error_rate', 'percentage'))
    dashboard.add_panel(MetricPanel.new('Active Users', 'active_users', 'number'))
    
    # Add alerts
    dashboard.add_alert(Alert.new('High Error Rate', 'error_rate', '>', 5))
    dashboard.add_alert(Alert.new('Slow Response', 'response_time', '>', 1000))
    dashboard.add_alert(Alert.new('Low Active Users', 'active_users', '<', 10))
    
    # Start dashboard
    dashboard.start
    
    # Simulate some data
    puts "Simulating dashboard data:"
    sleep(2)
    
    # Get dashboard data
    data = dashboard.get_dashboard_data
    
    puts "Dashboard Data:"
    puts "Name: #{data[:name]}"
    puts "Panels: #{data[:panels].length}"
    puts "Alerts: #{data[:alerts].length}"
    puts "Last Updated: #{data[:last_updated]}"
    
    # Stop dashboard
    dashboard.stop
    
    puts "\nDashboard Features:"
    puts "- Real-time data refresh"
    puts "- Multiple panel types"
    puts "- Alert management"
    puts "- Data source integration"
    puts "- Configurable refresh intervals"
    puts "- Dashboard visualization"
  end
  
  private
  
  def fetch_panel_data(panel)
    # Simulate data fetching
    case panel.metric_name
    when 'requests_per_second'
      rand(50..200).to_f
    when 'response_time'
      rand(100..500).to_f
    when 'error_rate'
      rand(0..10).to_f
    when 'active_users'
      rand(100..1000)
    else
      0
    end
  end
  
  def fetch_alert_data(alert)
    # Simulate alert data fetching
    case alert.metric
    when 'error_rate'
      rand(0..10).to_f
    when 'response_time'
      rand(100..2000).to_f
    when 'active_users'
      rand(50..200)
    else
      0
    end
  end
end

class MetricPanel
  def initialize(title, metric_name, type)
    @title = title
    @metric_name = metric_name
    @type = type
    @data = []
    @last_updated = Time.now
  end
  
  attr_reader :title, :metric_name, :type, :data, :last_updated
  
  def update_data(new_data)
    @data << { value: new_data, timestamp: Time.now }
    
    # Keep only last 100 data points
    if @data.length > 100
      @data = @data.last(100)
    end
    
    @last_updated = Time.now
  end
  
  def to_h
    {
      title: @title,
      metric_name: @metric_name,
      type: @type,
      data_points: @data.length,
      last_value: @data.last&.dig(:value),
      last_updated: @last_updated
    }
  end
end

class Alert
  def initialize(name, metric, operator, threshold)
    @name = name
    @metric = metric
    @operator = operator
    @threshold = threshold
    @triggered = false
    @last_triggered = nil
  end
  
  attr_reader :name, :metric, :operator, :threshold, :triggered, :last_triggered
  
  def check(data)
    triggered = evaluate_condition(data)
    
    if triggered && !@triggered
      @triggered = true
      @last_triggered = Time.now
      puts "ALERT: #{@name} triggered (#{@metric} #{@operator} #{@threshold})"
    elsif !triggered && @triggered
      @triggered = false
      puts "ALERT: #{@name} resolved"
    end
  end
  
  def to_h
    {
      name: @name,
      metric: @metric,
      operator: @operator,
      threshold: @threshold,
      triggered: @triggered,
      last_triggered: @last_triggered
    }
  end
  
  private
  
  def evaluate_condition(data)
    case @operator
    when '>'
      data > @threshold
    when '<'
      data < @threshold
    when '>='
      data >= @threshold
    when '<='
      data <= @threshold
    when '=='
      data == @threshold
    when '!='
      data != @threshold
    else
      false
    end
  end
end

class PrometheusDataSource
  def initialize
    @client = PrometheusClient.new
  end
  
  def query(metric_name, time_range = nil)
    # Simulate Prometheus query
    case metric_name
    when 'requests_per_second'
      rand(50..200).to_f
    when 'response_time'
      rand(100..500).to_f
    when 'error_rate'
      rand(0..10).to_f
    else
      0
    end
  end
end

class ElasticsearchDataSource
  def initialize
    @client = ElasticsearchClient.new
  end
  
  def query(index, query, time_range = nil)
    # Simulate Elasticsearch query
    case index
    when 'logs'
      rand(100..1000)
    when 'errors'
      rand(0..50)
    else
      0
    end
  end
end

class PrometheusClient
  def query(query)
    # Simulate Prometheus query
    { value: rand(100..1000) }
  end
end

class ElasticsearchClient
  def search(index, query)
    # Simulate Elasticsearch search
    { hits: { total: { value: rand(100..1000) } } }
  end
end
```

## 🔍 Alert Management

### 5. Alert System

Sophisticated alerting:

```ruby
class AlertManager
  def initialize
    @alerts = {}
    @alert_rules = []
    @notification_channels = {}
    @alert_history = []
    @suppressed_alerts = {}
    @escalation_policies = {}
  end
  
  def add_rule(name, condition, severity = :medium, options = {})
    rule = AlertRule.new(name, condition, severity, options)
    @alert_rules << rule
    rule
  end
  
  def add_channel(name, channel)
    @notification_channels[name] = channel
  end
  
  def add_escalation_policy(name, policy)
    @escalation_policies[name] = policy
  end
  
  def evaluate_alerts
    @alert_rules.each do |rule|
      if rule.evaluate
        alert = create_alert(rule)
        process_alert(alert)
      end
    end
  end
  
  def suppress_alert(alert_id, duration = 3600)
    @suppressed_alerts[alert_id] = Time.now + duration
    puts "Alert #{alert_id} suppressed for #{duration} seconds"
  end
  
  def get_alert_history(limit = 100)
    @alert_history.last(limit)
  end
  
  def get_active_alerts
    @alerts.values.select { |alert| alert.status == :active }
  end
  
  def get_suppressed_alerts
    @suppressed_alerts.select { |_, expiry| Time.now < expiry }
  end
  
  def self.demonstrate_alert_manager
    puts "Alert Manager Demonstration:"
    puts "=" * 50
    
    # Create alert manager
    alert_manager = AlertManager.new
    
    # Add notification channels
    alert_manager.add_channel('email', EmailChannel.new('team@example.com'))
    alert_manager.add_channel('slack', SlackChannel.new('#alerts'))
    alert_manager.add_channel('pagerduty', PagerDutyChannel.new('service123'))
    
    # Add alert rules
    alert_manager.add_rule('High Error Rate', 'error_rate > 5', :high, {
      description: 'Error rate is above 5%',
      channels: ['email', 'slack']
    })
    
    alert_manager.add_rule('Slow Response Time', 'response_time > 1000', :medium, {
      description: 'Response time is above 1000ms',
      channels: ['slack']
    })
    
    alert_manager.add_rule('Low Memory', 'memory_usage < 20', :low, {
      description: 'Memory usage is below 20%',
      channels: ['email']
    })
    
    # Add escalation policy
    alert_manager.add_escalation_policy('critical', {
      levels: [
        { severity: :high, delay: 300, channels: ['slack'] },
        { severity: :critical, delay: 600, channels: ['pagerduty'] }
      ]
    })
    
    # Simulate alert evaluation
    puts "Evaluating alerts:"
    alert_manager.evaluate_alerts
    
    # Get alert status
    puts "\nAlert Status:"
    active_alerts = alert_manager.get_active_alerts
    puts "Active alerts: #{active_alerts.length}"
    
    active_alerts.each do |alert|
      puts "  #{alert.rule_name}: #{alert.severity} - #{alert.message}"
    end
    
    # Get alert history
    puts "\nAlert History:"
    history = alert_manager.get_alert_history(5)
    history.each do |alert|
      puts "  [#{alert.timestamp}] #{alert.rule_name}: #{alert.severity}"
    end
    
    puts "\nAlert Manager Features:"
    puts "- Rule-based alerting"
    puts "- Multiple notification channels"
    puts "- Alert suppression"
    puts "- Escalation policies"
    puts "- Alert history tracking"
    puts "- Severity management"
  end
  
  private
  
  def create_alert(rule)
    alert = Alert.new(rule)
    @alerts[alert.id] = alert
    @alert_history << alert
    alert
  end
  
  def process_alert(alert)
    return if suppressed?(alert.id)
    
    # Send notifications
    rule = alert.rule
    rule.channels&.each do |channel_name|
      channel = @notification_channels[channel_name]
      channel&.notify(alert)
    end
    
    # Check escalation
    escalate_alert(alert) if should_escalate?(alert)
  end
  
  def suppressed?(alert_id)
    expiry = @suppressed_alerts[alert_id]
    expiry && Time.now < expiry
  end
  
  def should_escalate?(alert)
    # Check escalation policies
    @escalation_policies.each do |_, policy|
      policy[:levels].each do |level|
        if alert.severity == level[:severity] && 
           alert.created_at < Time.now - level[:delay]
          return true
        end
      end
    end
    
    false
  end
  
  def escalate_alert(alert)
    policy = @escalation_policies['critical']
    return unless policy
    
    policy[:levels].each do |level|
      if alert.severity == level[:severity]
        level[:channels].each do |channel_name|
          channel = @notification_channels[channel_name]
          channel&.notify(alert)
        end
      end
    end
  end
end

class AlertRule
  def initialize(name, condition, severity, options = {})
    @name = name
    @condition = condition
    @severity = severity
    @description = options[:description]
    @channels = options[:channels]
    @last_triggered = nil
  end
  
  attr_reader :name, :condition, :severity, :description, :channels, :last_triggered
  
  def evaluate
    # Simulate condition evaluation
    triggered = rand(10) > 7 # 30% chance of triggering
    
    if triggered
      @last_triggered = Time.now
    else
      @last_triggered = nil
    end
    
    triggered
  end
end

class Alert
  def initialize(rule)
    @id = SecureRandom.uuid
    @rule = rule
    @status = :active
    @created_at = Time.now
    @acknowledged_at = nil
    @resolved_at = nil
  end
  
  attr_reader :id, :rule, :status, :created_at, :acknowledged_at, :resolved_at
  
  def message
    @rule.description || @rule.name
  end
  
  def severity
    @rule.severity
  end
  
  def rule_name
    @rule.name
  end
  
  def acknowledge
    @acknowledged_at = Time.now
    @status = :acknowledged
  end
  
  def resolve
    @resolved_at = Time.now
    @status = :resolved
  end
  
  def to_h
    {
      id: @id,
      rule_name: rule_name,
      message: message,
      severity: severity,
      status: @status,
      created_at: @created_at,
      acknowledged_at: @acknowledged_at,
      resolved_at: @resolved_at
    }
  end
end

class EmailChannel
  def initialize(recipient)
    @recipient = recipient
  end
  
  def notify(alert)
    puts "EMAIL: Alert sent to #{@recipient}"
    puts "  Subject: [#{alert.severity.upcase}] #{alert.rule_name}"
    puts "  Message: #{alert.message}"
    puts "  Timestamp: #{alert.created_at}"
  end
end

class SlackChannel
  def initialize(webhook_url)
    @webhook_url = webhook_url
  end
  
  def notify(alert)
    puts "SLACK: Alert sent to #{@webhook_url}"
    puts "  Channel: #alerts"
    puts "  Message: [#{alert.severity.upcase}] #{alert.rule_name} - #{alert.message}"
  end
end

class PagerDutyChannel
  def initialize(service_key)
    @service_key = service_key
  end
  
  def notify(alert)
    puts "PAGERDUTY: Alert sent to service #{@service_key}"
    puts "  Severity: #{alert.severity}"
    puts "  Message: #{alert.message}"
    puts "  Timestamp: #{alert.created_at}"
  end
end
```

## 🎯 Observability Platform

### 6. Complete Observability

Comprehensive observability system:

```ruby
class ObservabilityPlatform
  def initialize(name)
    @name = name
    @metrics_collector = MetricsCollector.new
    @logger = StructuredLogger.new(name, :info)
    @tracer = DistributedTracer.new
    @alert_manager = AlertManager.new
    @dashboard = MonitoringDashboard.new("#{name}-dashboard")
    @correlation_id = nil
  end
  
  attr_reader :name
  
  def start_trace(operation_name)
    @correlation_id = SecureRandom.uuid
    @tracer.start_trace(@correlation_id, operation_name)
    
    @logger.with_context({ correlation_id: @correlation_id }) do
      @logger.info("Trace started", { operation: operation_name })
    end
    
    @correlation_id
  end
  
  def end_trace(span_name = nil)
    return unless @correlation_id
    
    @tracer.end_trace(@correlation_id, span_name)
    
    @logger.with_context({ correlation_id: @correlation_id }) do
      @logger.info("Trace ended", { span: span_name })
    end
    
    @correlation_id = nil
  end
  
  def track_span(span_name, tags = {})
    return unless @correlation_id
    
    @tracer.create_span(@correlation_id, span_name, tags)
    @logger.with_context({ correlation_id: @correlation_id }) do
      @logger.debug("Span created", { span: span_name, tags: tags })
    end
  end
  
  def record_metric(name, value, tags = {})
    @metrics_collector.gauge(name, value, tags)
    
    @logger.with_context({ correlation_id: @correlation_id }) do
      @logger.debug("Metric recorded", { metric: name, value: value, tags: tags })
    end
  end
  
  def log_event(level, message, context = {})
    context_with_trace = @correlation_id ? context.merge(correlation_id: @correlation_id) : context
    
    @logger.send(level, message, context_with_trace)
  end
  
  def create_alert(rule_name, condition, severity = :medium)
    @alert_manager.add_rule(rule_name, condition, severity, {
      correlation_id: @correlation_id
    })
  end
  
  def get_observability_data
    {
      name: @name,
      correlation_id: @correlation_id,
      metrics: @metrics_collector.get_all_metrics,
      recent_logs: get_recent_logs,
      traces: @tracer.get_recent_traces,
      alerts: @alert_manager.get_active_alerts,
      dashboard: @dashboard.get_dashboard_data
    }
  end
  
  def self.demonstrate_platform
    puts "Observability Platform Demonstration:"
    puts "=" * 50
    
    # Create platform
    platform = ObservabilityPlatform.new('myapp')
    
    # Add outputs to logger
    platform.logger.add_output(ConsoleOutput.new)
    platform.logger.add_output(FileOutput.new('app.log'))
    
    # Add alert channels
    platform.alert_manager.add_channel('email', EmailChannel.new('team@example.com'))
    platform.alert_manager.add_channel('slack', SlackChannel.new('#alerts'))
    
    # Add alert rules
    platform.create_alert('high_error_rate', 'error_rate > 10', :high)
    platform.create_alert('slow_response', 'response_time > 2000', :medium)
    
    # Start trace
    correlation_id = platform.start_trace('user_registration')
    
    # Track spans
    platform.track_span('validate_input', { component: 'validation' })
    platform.record_metric('validation_time', 0.05, { component: 'validation' })
    
    platform.track_span('database_query', { component: 'database' })
    platform.record_metric('query_time', 0.1, { component: 'database' })
    
    platform.track_span('send_email', { component: 'notification' })
    platform.record_metric('email_time', 0.2, { component: 'notification' })
    
    # Log events
    platform.log_event(:info, "User registration started")
    platform.log_event(:info, "User validation completed")
    platform.log_event(:info, "User created successfully")
    
    # End trace
    platform.end_trace('user_registration')
    
    # Get observability data
    data = platform.get_observability_data
    
    puts "Observability Data:"
    puts "Platform: #{data[:name]}"
    puts "Correlation ID: #{data[:correlation_id]}"
    puts "Metrics: #{data[:metrics][:counters].keys.length} counters"
    puts "Recent Logs: #{data[:recent_logs].length}"
    puts "Traces: #{data[:traces].length}"
    puts "Active Alerts: #{data[:alerts].length}"
    
    puts "\nObservability Features:"
    puts "- Distributed tracing"
    puts "- Metrics collection"
    puts "- Structured logging"
    puts "- Alert management"
    puts "- Correlation tracking"
    puts "- Comprehensive observability"
  end
  
  private
  
  def get_recent_logs(limit = 10)
    # Simulate recent logs
    (1..limit).map do |i|
      {
        timestamp: Time.now - i,
        level: 'INFO',
        message: "Recent log entry #{i}",
        correlation_id: @correlation_id
      }
    end
  end
end

class DistributedTracer
  def initialize
    @traces = {}
    @mutex = Mutex.new
  end
  
  def start_trace(trace_id, operation_name)
    @mutex.synchronize do
      @traces[trace_id] = {
        id: trace_id,
        operation_name: operation_name,
        started_at: Time.now,
        spans: [],
        status: :active
      }
    end
    
    trace_id
  end
  
  def end_trace(trace_id, span_name = nil)
    @mutex.synchronize do
      trace = @traces[trace_id]
      return unless trace
      
      trace[:status] = :completed
      trace[:ended_at] = Time.now
      trace[:duration] = trace[:ended_at] - trace[:started_at]
    end
  end
  
  def create_span(trace_id, span_name, tags = {})
    @mutex.synchronize do
      trace = @traces[trace_id]
      return unless trace
      
      span = {
        id: SecureRandom.uuid,
        name: span_name,
        tags: tags,
        started_at: Time.now,
        trace_id: trace_id
      }
      
      trace[:spans] << span
      span
    end
  end
  
  def get_recent_traces(limit = 10)
    @mutex.synchronize do
      @traces.values
        .sort_by { |t| t[:started_at] }
        .reverse
        .first(limit)
    end
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic Metrics**: Create simple metrics system
2. **Structured Logging**: Implement structured logger
3. **Dashboard**: Create monitoring dashboard
4. **Alert System**: Build alerting system

### Intermediate Exercises

1. **Advanced Metrics**: Complex metrics collection
2. **Distributed Tracing**: Implement tracing system
3. **Alert Management**: Sophisticated alerting
4. **Observability**: Complete observability platform

### Advanced Exercises

1. **Enterprise Monitoring**: Production-ready monitoring
2. **Real-time Analytics**: Real-time data processing
3. **ML-based Anomaly Detection**: Intelligent alerting
4. **Multi-tenant Observability**: Multi-service monitoring

---

## 🎯 Summary

Monitoring and Logging in Ruby provides:

- **Monitoring Fundamentals** - Core concepts and principles
- **Metrics Collection** - Application metrics implementation
- **Structured Logging** - Advanced logging system
- **Monitoring Dashboard** - Visualization and alerting
- **Alert Management** - Sophisticated alerting system
- **Observability Platform** - Complete observability solution

Master these monitoring and logging techniques for production-ready Ruby applications!
