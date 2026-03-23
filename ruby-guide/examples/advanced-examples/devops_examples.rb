# DevOps Examples
# Demonstrating CI/CD, containerization, infrastructure automation, and monitoring

puts "=== CONTAINERIZATION ==="

class DockerManager
  def initialize
    @containers = {}
    @images = {}
    @networks = {}
    @volumes = {}
  end
  
  def build_image(name, dockerfile_content)
    puts "Building Docker image: #{name}"
    puts "Dockerfile content:"
    puts dockerfile_content
    
    # Simulate build process
    steps = dockerfile_content.split("\n").select { |line| line.strip.start_with?('RUN') || line.strip.start_with?('FROM') }
    
    steps.each_with_index do |step, i|
      puts "Step #{i + 1}/#{steps.length}: #{step.strip}"
      sleep(0.1)  # Simulate build time
    end
    
    image_id = "img_#{name.gsub(/[^a-zA-Z0-9]/, '_')}_#{Time.now.to_i}"
    @images[name] = {
      id: image_id,
      size: rand(100..1000),
      created_at: Time.now,
      layers: steps.length
    }
    
    puts "Successfully built #{name} (ID: #{image_id})"
    image_id
  end
  
  def run_container(image_name, container_name = nil, options = {})
    image = @images[image_name]
    return "Image #{image_name} not found" unless image
    
    container_name ||= "container_#{Time.now.to_i}"
    container_id = "ctr_#{container_name.gsub(/[^a-zA-Z0-9]/, '_')}"
    
    @containers[container_name] = {
      id: container_id,
      image: image_name,
      status: :running,
      created_at: Time.now,
      ports: options[:ports] || [],
      volumes: options[:volumes] || [],
      environment: options[:environment] || {}
    }
    
    puts "Started container: #{container_name} (ID: #{container_id})"
    puts "Image: #{image_name}"
    puts "Ports: #{options[:ports].join(', ')}" if options[:ports]
    puts "Volumes: #{options[:volumes].join(', ')}" if options[:volumes]
    
    container_id
  end
  
  def stop_container(container_name)
    container = @containers[container_name]
    return "Container #{container_name} not found" unless container
    
    container[:status] = :stopped
    container[:stopped_at] = Time.now
    
    puts "Stopped container: #{container_name}"
    true
  end
  
  def list_containers
    puts "Containers:"
    @containers.each do |name, container|
      status_icon = container[:status] == :running ? "🟢" : "🔴"
      puts "  #{status_icon} #{name} (#{container[:image]}) - #{container[:status]}"
    end
    @containers
  end
  
  def list_images
    puts "Images:"
    @images.each do |name, image|
      puts "  📦 #{name} (#{image[:size]}MB) - #{image[:layers]} layers"
    end
    @images
  end
  
  def remove_container(container_name)
    container = @containers[container_name]
    return "Container #{container_name} not found" unless container
    
    if container[:status] == :running
      return "Cannot remove running container. Stop it first."
    end
    
    @containers.delete(container_name)
    puts "Removed container: #{container_name}"
    true
  end
end

puts "Docker Containerization Example:"

docker = DockerManager.new

# Build images
dockerfile_content = <<~DOCKERFILE
  FROM ruby:3.1-alpine
  RUN apk add --no-cache build-base
  WORKDIR /app
  COPY Gemfile Gemfile.lock ./
  RUN bundle install
  COPY . .
  RUN bundle exec rails assets:precompile
  EXPOSE 3000
  CMD ["rails", "server", "-b", "0.0.0.0"]
DOCKERFILE

docker.build_image('myapp', dockerfile_content)

# Run containers
docker.run_container('myapp', 'web', {
  ports: ['3000:3000'],
  environment: { 'RAILS_ENV' => 'production' },
  volumes: ['/app/data:/data']
})

docker.run_container('myapp', 'worker', {
  environment: { 'RAILS_ENV' => 'production' }
})

# List containers and images
docker.list_containers
docker.list_images

# Stop and remove container
docker.stop_container('web')
docker.remove_container('web')

puts "\n=== CI/CD PIPELINE ==="

class CICDPipeline
  def initialize
    @stages = []
    @current_stage = 0
    @build_number = Time.now.to_i
    @status = :pending
    @artifacts = {}
  end
  
  def add_stage(name, &block)
    @stages << {
      name: name,
      block: block,
      status: :pending,
      duration: 0,
      started_at: nil,
      completed_at: nil
    }
  end
  
  def run
    puts "🚀 Starting CI/CD Pipeline ##{@build_number}"
    puts "=" * 50
    
    @status = :running
    success = true
    
    @stages.each_with_index do |stage, index|
      @current_stage = index
      success = run_stage(stage)
      break unless success
    end
    
    @status = success ? :success : :failed
    puts "\n🎯 Pipeline #{@status} ##{@build_number}"
    
    success
  end
  
  def add_artifact(name, content)
    @artifacts[name] = {
      content: content,
      size: content.length,
      created_at: Time.now
    }
  end
  
  def get_artifact(name)
    @artifacts[name]
  end
  
  def pipeline_summary
    {
      build_number: @build_number,
      status: @status,
      stages: @stages.length,
      artifacts: @artifacts.length,
      total_duration: @stages.sum { |s| s[:duration] }
    }
  end
  
  private
  
  def run_stage(stage)
    puts "\n📋 Stage #{stage[:name]}"
    puts "-" * 30
    
    stage[:status] = :running
    stage[:started_at] = Time.now
    
    begin
      result = stage[:block].call(self)
      stage[:status] = :success
      stage[:result] = result
      puts "✅ #{stage[:name]} completed successfully"
      true
    rescue => e
      stage[:status] = :failed
      stage[:error] = e.message
      puts "❌ #{stage[:name]} failed: #{e.message}"
      false
    ensure
      stage[:completed_at] = Time.now
      stage[:duration] = stage[:completed_at] - stage[:started_at]
      puts "⏱️  Duration: #{stage[:duration].round(2)}s"
    end
  end
end

puts "CI/CD Pipeline Example:"

pipeline = CICDPipeline.new

# Add pipeline stages
pipeline.add_stage('Checkout') do |pipeline|
  puts "Checking out code..."
  sleep(0.5)
  "Code checked out successfully"
end

pipeline.add_stage('Install Dependencies') do |pipeline|
  puts "Installing dependencies..."
  sleep(1)
  
  dependencies = %w[rails pg redis sidekiq]
  puts "Installed: #{dependencies.join(', ')}"
  
  pipeline.add_artifact('Gemfile.lock', "gem 'rails'\ngem 'pg'\ngem 'redis'\ngem 'sidekiq'")
  
  "Dependencies installed"
end

pipeline.add_stage('Run Tests') do |pipeline|
  puts "Running test suite..."
  sleep(1.5)
  
  # Simulate test results
  test_results = {
    unit_tests: { passed: 145, failed: 2, skipped: 3 },
    integration_tests: { passed: 67, failed: 1, skipped: 0 },
    system_tests: { passed: 23, failed: 0, skipped: 1 }
  }
  
  test_results.each do |test_type, results|
    puts "  #{test_type.to_s.gsub('_', ' ').capitalize}: #{results[:passed]} passed, #{results[:failed]} failed, #{results[:skipped]} skipped"
  end
  
  total_passed = test_results.values.sum { |r| r[:passed] }
  total_failed = test_results.values.sum { |r| r[:failed] }
  
  if total_failed > 0
    raise "Tests failed: #{total_failed} tests failed"
  end
  
  pipeline.add_artifact('test_results.xml', "<tests><passed>#{total_passed}</passed><failed>#{total_failed}</failed></tests>")
  
  "All tests passed"
end

pipeline.add_stage('Security Scan') do |pipeline|
  puts "Running security scan..."
  sleep(0.8)
  
  vulnerabilities = [
    { severity: 'high', file: 'app/controllers/user_controller.rb', line: 45 },
    { severity: 'medium', file: 'Gemfile', line: 12 },
    { severity: 'low', file: 'config/routes.rb', line: 8 }
  ]
  
  puts "Found #{vulnerabilities.length} vulnerabilities:"
  vulnerabilities.each do |vuln|
    puts "  #{vuln[:severity].upcase}: #{vuln[:file]}:#{vuln[:line]}"
  end
  
  pipeline.add_artifact('security_report.json', vulnerabilities.to_json)
  
  "Security scan completed"
end

pipeline.add_stage('Build Application') do |pipeline|
  puts "Building application..."
  sleep(1.2)
  
  build_artifacts = [
    'app.jar',
    'public/assets/application.css',
    'public/assets/application.js'
  ]
  
  puts "Built artifacts: #{build_artifacts.join(', ')}"
  
  build_artifacts.each do |artifact|
    pipeline.add_artifact(artifact, "Content of #{artifact}")
  end
  
  "Application built successfully"
end

pipeline.add_stage('Deploy to Staging') do |pipeline|
  puts "Deploying to staging environment..."
  sleep(2)
  
  deployment_steps = [
    'Backup current version',
    'Stop application',
    'Deploy new version',
    'Run database migrations',
    'Start application',
    'Health check'
  ]
  
  deployment_steps.each do |step|
    puts "  ✓ #{step}"
    sleep(0.3)
  end
  
  "Deployed to staging"
end

# Run pipeline
success = pipeline.run

# Show pipeline summary
summary = pipeline.pipeline_summary
puts "\n📊 Pipeline Summary:"
puts "Build Number: #{summary[:build_number]}"
puts "Status: #{summary[:status]}"
puts "Stages: #{summary[:stages]}"
puts "Artifacts: #{summary[:artifacts]}"
puts "Total Duration: #{summary[:total_duration].round(2)}s"

puts "\n=== INFRASTRUCTURE AS CODE ==="

class InfrastructureManager
  def initialize
    @resources = {}
    @state = {}
  end
  
  def define_resource(type, name, &block)
    resource = {
      type: type,
      name: name,
      properties: {},
      dependencies: []
    }
    
    if block
      resource_definition = ResourceDefinition.new(resource)
      resource_definition.instance_eval(&block)
    end
    
    @resources[name] = resource
    puts "Defined #{type}: #{name}"
  end
  
  def deploy
    puts "🏗️  Deploying infrastructure..."
    
    # Check dependencies
    dependency_order = resolve_dependencies
    
    # Deploy resources in order
    dependency_order.each do |resource_name|
      resource = @resources[resource_name]
      deploy_resource(resource)
    end
    
    puts "✅ Infrastructure deployed successfully"
  end
  
  def destroy
    puts "🗑️  Destroying infrastructure..."
    
    # Destroy in reverse order
    dependency_order = resolve_dependencies.reverse
    
    dependency_order.each do |resource_name|
      resource = @resources[resource_name]
      destroy_resource(resource)
    end
    
    puts "✅ Infrastructure destroyed"
  end
  
  def list_resources
    puts "Infrastructure Resources:"
    @resources.each do |name, resource|
      status = @state[name] ? "✅ Created" : "❌ Not created"
      puts "  #{status} #{resource[:type]}: #{name}"
    end
  end
  
  def get_resource_state(name)
    @state[name]
  end
  
  private
  
  def resolve_dependencies
    resolved = []
    visited = Set.new
    
    @resources.keys.each do |resource_name|
      resolve_dependencies_recursive(resource_name, resolved, visited)
    end
    
    resolved
  end
  
  def resolve_dependencies_recursive(resource_name, resolved, visited)
    return if visited.include?(resource_name)
    
    visited.add(resource_name)
    resource = @resources[resource_name]
    
    resource[:dependencies].each do |dependency|
      resolve_dependencies_recursive(dependency, resolved, visited)
    end
    
    resolved << resource_name unless resolved.include?(resource_name)
  end
  
  def deploy_resource(resource)
    puts "Deploying #{resource[:type]}: #{resource[:name]}"
    
    case resource[:type]
    when :server
      deploy_server(resource)
    when :database
      deploy_database(resource)
    when :network
      deploy_network(resource)
    when :storage
      deploy_storage(resource)
    end
    
    @state[resource[:name]] = {
      status: :created,
      created_at: Time.now,
      properties: resource[:properties]
    }
  end
  
  def destroy_resource(resource)
    puts "Destroying #{resource[:type]}: #{resource[:name]}"
    
    case resource[:type]
    when :server
      destroy_server(resource)
    when :database
      destroy_database(resource)
    when :network
      destroy_network(resource)
    when :storage
      destroy_storage(resource)
    end
    
    @state[resource[:name]] = {
      status: :destroyed,
      destroyed_at: Time.now
    }
  end
  
  def deploy_server(resource)
    props = resource[:properties]
    puts "  Creating server with #{props[:instance_type]} instance"
    puts "  Installing #{props[:operating_system]}"
    puts "  Configuring security groups"
  end
  
  def deploy_database(resource)
    props = resource[:properties]
    puts "  Creating #{props[:engine]} database"
    puts "  Setting storage size to #{props[:storage_size]}GB"
    puts "  Configuring backups"
  end
  
  def deploy_network(resource)
    props = resource[:properties]
    puts "  Creating network with CIDR #{props[:cidr]}"
    puts "  Setting up subnets"
    puts "  Configuring routing"
  end
  
  def deploy_storage(resource)
    props = resource[:properties]
    puts "  Creating #{props[:type]} storage"
    puts "  Setting size to #{props[:size]}GB"
    puts "  Configuring permissions"
  end
  
  def destroy_server(resource)
    puts "  Stopping server"
    puts "  Terminating instance"
  end
  
  def destroy_database(resource)
    puts "  Creating final backup"
    puts "  Deleting database"
  end
  
  def destroy_network(resource)
    puts "  Removing network interfaces"
    puts "  Deleting network"
  end
  
  def destroy_storage(resource)
    puts "  Unmounting storage"
    puts "  Deleting storage volume"
  end
end

class ResourceDefinition
  def initialize(resource)
    @resource = resource
  end
  
  def property(key, value)
    @resource[:properties][key] = value
  end
  
  def depends_on(resource_name)
    @resource[:dependencies] << resource_name
  end
end

puts "Infrastructure as Code Example:"

infra = InfrastructureManager.new

# Define infrastructure
infra.define_resource(:network, 'main_network') do
  property :cidr, '10.0.0.0/16'
  property :region, 'us-west-2'
end

infra.define_resource(:database, 'main_db') do
  property :engine, 'PostgreSQL'
  property :storage_size, 100
  property :backup_retention, 7
  depends_on 'main_network'
end

infra.define_resource(:server, 'web_server') do
  property :instance_type, 't3.medium'
  property :operating_system, 'Ubuntu 20.04'
  property :min_instances, 2
  property :max_instances, 5
  depends_on 'main_network'
  depends_on 'main_db'
end

infra.define_resource(:storage, 'file_storage') do
  property :type, 'EFS'
  property :size, 1000
  property :performance, 'general_purpose'
  depends_on 'main_network'
end

# Deploy infrastructure
infra.deploy
infra.list_resources

# Show resource states
puts "\nResource States:"
infra.list_resources

# Destroy infrastructure
infra.destroy

puts "\n=== MONITORING AND LOGGING ==="

class MonitoringSystem
  def initialize
    @metrics = {}
    @alerts = []
    @logs = []
    @dashboards = {}
  end
  
  def collect_metric(name, value, tags = {})
    timestamp = Time.now
    @metrics[name] ||= []
    
    @metrics[name] << {
      value: value,
      timestamp: timestamp,
      tags: tags
    }
    
    # Keep only last 1000 data points
    @metrics[name] = @metrics[name].last(1000)
    
    # Check alerts
    check_alerts(name, value, tags)
  end
  
  def add_alert(name, condition, threshold, action = nil)
    @alerts << {
      name: name,
      condition: condition,
      threshold: threshold,
      action: action,
      triggered: false
    }
  end
  
  def log(message, level = :info, context = {})
    @logs << {
      message: message,
      level: level,
      timestamp: Time.now,
      context: context
    }
    
    # Keep only last 1000 logs
    @logs = @logs.last(1000)
  end
  
  def create_dashboard(name, &block)
    dashboard = Dashboard.new(name)
    dashboard.instance_eval(&block) if block
    @dashboards[name] = dashboard
  end
  
  def get_metrics(name)
    @metrics[name] || []
  end
  
  def get_logs(level = nil, since = nil)
    logs = @logs.dup
    
    logs = logs.select { |log| log[:level] == level } if level
    logs = logs.select { |log| log[:timestamp] >= since } if since
    
    logs
  end
  
  def get_dashboard(name)
    @dashboards[name]
  end
  
  def system_health
    {
      metrics_count: @metrics.values.sum(&:length),
      alerts_count: @alerts.length,
      logs_count: @logs.length,
      dashboards_count: @dashboards.length,
      uptime: Time.now - (@logs.first&.dig(:timestamp) || Time.now)
    }
  end
  
  private
  
  def check_alerts(metric_name, value, tags)
    @alerts.each do |alert|
      next unless alert[:name] == metric_name
      
      triggered = case alert[:condition]
                  when :greater_than
                    value > alert[:threshold]
                  when :less_than
                    value < alert[:threshold]
                  when :equals
                    value == alert[:threshold]
                  else
                    false
                  end
      
      if triggered && !alert[:triggered]
        alert[:triggered] = true
        alert[:triggered_at] = Time.now
        
        puts "🚨 ALERT: #{alert[:name]} - #{alert[:condition]} #{alert[:threshold]} (current: #{value})"
        
        alert[:action]&.call(value, tags)
      elsif !triggered && alert[:triggered]
        alert[:triggered] = false
        puts "✅ RESOLVED: #{alert[:name]}"
      end
    end
  end
end

class Dashboard
  def initialize(name)
    @name = name
    @widgets = []
  end
  
  def widget(type, title, &block)
    widget = {
      type: type,
      title: title,
      config: {}
    }
    
    if block
      widget_config = WidgetConfig.new(widget)
      widget_config.instance_eval(&block)
    end
    
    @widgets << widget
  end
  
  def render
    puts "📊 Dashboard: #{@name}"
    puts "=" * 40
    
    @widgets.each do |widget|
      puts "📈 #{widget[:type]}: #{widget[:title]}"
      widget[:config].each do |key, value|
        puts "   #{key}: #{value}"
      end
    end
  end
end

class WidgetConfig
  def initialize(widget)
    @widget = widget
  end
  
  def metric(name)
    @widget[:config][:metric] = name
  end
  
  def aggregation(type)
    @widget[:config][:aggregation] = type
  end
  
  def time_range(range)
    @widget[:config][:time_range] = range
  end
end

puts "Monitoring and Logging Example:"

monitor = MonitoringSystem.new

# Set up alerts
monitor.add_alert('cpu_usage', :greater_than, 80) do |value, tags|
  monitor.log("High CPU usage detected: #{value}%", :alert, tags)
end

monitor.add_alert('memory_usage', :greater_than, 90) do |value, tags|
  monitor.log("High memory usage detected: #{value}%", :alert, tags)
end

monitor.add_alert('error_rate', :greater_than, 5) do |value, tags|
  monitor.log("High error rate detected: #{value}%", :alert, tags)
end

# Create dashboard
monitor.create_dashboard('Application Overview') do
  widget('gauge', 'CPU Usage') do
    metric 'cpu_usage'
    aggregation 'avg'
    time_range '1h'
  end
  
  widget('gauge', 'Memory Usage') do
    metric 'memory_usage'
    aggregation 'avg'
    time_range '1h'
  end
  
  widget('line', 'Request Rate') do
    metric 'requests_per_second'
    aggregation 'sum'
    time_range '24h'
  end
end

# Simulate metrics collection
10.times do |i|
  cpu = 50 + rand(40)
  memory = 60 + rand(30)
  requests = 100 + rand(50)
  errors = rand(10)
  
  monitor.collect_metric('cpu_usage', cpu, { host: 'web-1' })
  monitor.collect_metric('memory_usage', memory, { host: 'web-1' })
  monitor.collect_metric('requests_per_second', requests, { service: 'api' })
  monitor.collect_metric('error_rate', errors, { service: 'api' })
  
  # Log some events
  monitor.log("Request processed successfully", :info, { request_id: i, duration: rand(100..500) })
  monitor.log("Database connection established", :info, { connection_id: i })
  
  if i == 5
    monitor.log("User login failed", :warn, { user_id: 123, reason: 'invalid_password' })
  end
  
  sleep(0.1)
end

# Show dashboard
dashboard = monitor.get_dashboard('Application Overview')
dashboard.render if dashboard

# Show recent logs
puts "\nRecent Logs:"
recent_logs = monitor.get_logs(nil, Time.now - 1)
recent_logs.last(5).each do |log|
  level_icon = case log[:level]
               when :info then "ℹ️"
               when :warn then "⚠️"
               when :error then "❌"
               when :alert then "🚨"
               else "📝"
               end
  
  puts "#{level_icon} [#{log[:timestamp].strftime('%H:%M:%S')}] #{log[:message]}"
end

# Show system health
health = monitor.system_health
puts "\n🏥 System Health:"
puts "Metrics: #{health[:metrics_count]}"
puts "Alerts: #{health[:alerts_count]}"
puts "Logs: #{health[:logs_count]}"
puts "Dashboards: #{health[:dashboards_count]}"
puts "Uptime: #{health[:uptime].round(2)}s"

puts "\n=== DEVOPS SUMMARY ==="
puts "- Containerization: Docker image building, container management"
puts "- CI/CD Pipeline: Multi-stage builds, artifact management, deployment"
puts "- Infrastructure as Code: Resource definitions, dependency resolution"
puts "- Monitoring: Metrics collection, alerting, dashboards, logging"
puts "\nAll examples demonstrate comprehensive DevOps concepts in Ruby!"
