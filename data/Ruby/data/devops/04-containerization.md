# Containerization in Ruby
# Comprehensive guide to Docker, Kubernetes, and container orchestration

## 🐳 Container Fundamentals

### 1. Container Concepts

Core containerization principles:

```ruby
class ContainerFundamentals
  def self.explain_container_concepts
    puts "Container Fundamentals:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Container",
        description: "Lightweight, portable software packaging",
        characteristics: ["Isolated", "Portable", "Lightweight", "Consistent"],
        benefits: ["Portability", "Efficiency", "Consistency", "Scalability"],
        components: ["Runtime", "Image", "Registry", "Orchestration"]
      },
      {
        concept: "Docker",
        description: "Platform for building and running containers",
        features: ["Image building", "Container runtime", "Registry", "Orchestration"],
        components: ["Docker Engine", "Dockerfile", "Docker Compose", "Docker Swarm"],
        benefits: ["Standardization", "Ecosystem", "Tooling", "Community"]
      },
      {
        concept: "Container Image",
        description: "Immutable template for creating containers",
        layers: ["Base layer", "Application layer", "Configuration layer"],
        benefits: ["Reusability", "Efficiency", "Versioning", "Distribution"],
        formats: ["Docker", "OCI", "AppC"]
      },
      {
        concept: "Container Runtime",
        description: "System that runs containers",
        responsibilities: ["Image management", "Container lifecycle", "Resource isolation", "Networking"],
        types: ["Docker Engine", "containerd", "CRI-O", "runc"],
        interfaces: ["Docker API", "OCI Runtime Interface"]
      },
      {
        concept: "Container Orchestration",
        description: "Automated management of containerized applications",
        features: ["Scheduling", "Scaling", "Self-healing", "Service discovery"],
        tools: ["Kubernetes", "Docker Swarm", "Apache Mesos", "Nomad"],
        benefits: ["Automation", "Scalability", "Reliability", "Efficiency"]
      },
      {
        concept: "Microservices",
        description: "Architectural style using small, independent services",
        characteristics: ["Single responsibility", "Independent deployment", "Technology diversity"],
        benefits: ["Scalability", "Flexibility", "Resilience", "Team autonomy"],
        container_role: "Packaging", "Deployment", "Isolation", "Portability"
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Characteristics: #{concept[:characteristics].join(', ')}" if concept[:characteristics]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Components: #{concept[:components].join(', ')}" if concept[:components]
      puts "  Features: #{concept[:features].join(', ')}" if concept[:features]
      puts "  Layers: #{concept[:layers].join(', ')}" if concept[:layers]
      puts "  Formats: #{concept[:formats].join(', ')}" if concept[:formats]
      puts "  Responsibilities: #{concept[:responsibilities].join(', ')}" if concept[:responsibilities]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Interfaces: #{concept[:interfaces].join(', ')}" if concept[:interfaces]
      puts "  Tools: #{concept[:tools].join(', ')}" if concept[:tools]
      puts "  Container Role: #{concept[:container_role].join(', ')}" if concept[:container_role]
      puts
    end
  end
  
  def self.container_lifecycle
    puts "\nContainer Lifecycle:"
    puts "=" * 50
    
    lifecycle = [
      {
        phase: "1. Build",
        description: "Create container image from Dockerfile",
        steps: ["Write Dockerfile", "Build image", "Tag image", "Push to registry"],
        commands: ["docker build", "docker tag", "docker push"],
        duration: "1-10 minutes"
      },
      {
        phase: "2. Run",
        description: "Start container from image",
        steps: ["Pull image", "Create container", "Start container", "Allocate resources"],
        commands: ["docker run", "docker create", "docker start"],
        duration: "Seconds"
      },
      {
        phase: "3. Execute",
        description: "Container runs and processes requests",
        steps: ["Initialize", "Run main process", "Handle requests", "Log output"],
        commands: ["docker logs", "docker exec"],
        duration: "Until stopped"
      },
      {
        phase: "4. Stop",
        description: "Gracefully stop container",
        steps: ["Send SIGTERM", "Wait for graceful shutdown", "Send SIGKILL", "Remove container"],
        commands: ["docker stop", "docker kill", "docker rm"],
        duration: "10-30 seconds"
      },
      {
        phase: "5. Cleanup",
        description: "Clean up resources",
        steps: ["Remove container", "Remove images", "Clean volumes", "Free resources"],
        commands: ["docker prune", "docker system prune"],
        duration: "Seconds"
      }
    ]
    
    lifecycle.each do |phase|
      puts "#{phase[:phase]}: #{phase[:description]}"
      puts "  Steps: #{phase[:steps].join(', ')}"
      puts "  Commands: #{phase[:commands].join(', ')}"
      puts "  Duration: #{phase[:duration]}"
      puts
    end
  end
  
  def self.container_vs_vm
    puts "\nContainer vs Virtual Machine:"
    puts "=" * 50
    
    comparison = [
      {
        aspect: "Architecture",
        containers: ["Shared OS kernel", "Process isolation", "Lightweight", "Fast startup"],
        vms: ["Separate OS", "Hardware virtualization", "Heavy", "Slow startup"]
      },
      {
        aspect: "Resource Usage",
        containers: ["Low overhead", "Shared libraries", "Efficient", "High density"],
        vms: ["High overhead", "Full OS", "Resource intensive", "Low density"]
      },
      {
        aspect: "Portability",
        containers: ["Portable", "Consistent", "Cross-platform", "Versioned"],
        vms: ["Portable", "Consistent", "Platform-specific", "Large images"]
      },
      {
        aspect: "Isolation",
        containers: ["Process isolation", "Network isolation", "File system isolation"],
        vms: ["Complete isolation", "Hardware isolation", "Full separation"]
      },
      {
        aspect: "Management",
        containers: ["Orchestration", "Microservices", "CI/CD integration"],
        vms: ["Traditional management", "Monolithic", "Manual deployment"]
      },
      {
        aspect: "Use Cases",
        containers: ["Microservices", "CI/CD", "DevOps", "Cloud-native"],
        vms: ["Legacy apps", "Strong isolation", "Multi-OS", "Testing"]
      }
    ]
    
    comparison.each do |aspect|
      puts "#{aspect[:aspect]}:"
      puts "  Containers: #{aspect[:containers].join(', ')}"
      puts "  VMs: #{aspect[:vms].join(', ')}"
      puts
    end
  end
  
  def self.containerization_benefits
    puts "\nContainerization Benefits:"
    puts "=" * 50
    
    benefits = [
      {
        benefit: "Portability",
        description: "Run anywhere with Docker runtime",
        advantages: ["Consistent environments", "No dependency issues", "Easy deployment"],
        impact: "Reduced 'it works on my machine' problems"
      },
      {
        benefit: "Efficiency",
        description: "Lightweight and resource-efficient",
        advantages: ["Fast startup", "Low overhead", "High density"],
        impact: "Better resource utilization and cost savings"
      },
      {
        benefit: "Scalability",
        description: "Easy to scale horizontally",
        advantages: ["Auto-scaling", "Load balancing", "Service discovery"],
        impact: "Handle traffic spikes efficiently"
      },
      {
        benefit: "Isolation",
        description: "Isolated runtime environments",
        advantages: ["Process isolation", "Network isolation", "File system isolation"],
        impact: "Security and stability improvements"
      },
      {
        benefit: "Versioning",
        description: "Immutable and versioned images",
        advantages: ["Rollback capability", "Version control", "Reproducibility"],
        impact: "Reliable deployments and rollbacks"
      },
      {
        benefit: "DevOps Integration",
        description: "Native DevOps tooling integration",
        advantages: ["CI/CD pipelines", "Infrastructure as code", "Automation"],
        impact: "Faster and more reliable deployments"
      }
    ]
    
    benefits.each do |benefit|
      puts "#{benefit[:benefit]}:"
      puts "  Description: #{benefit[:description]}"
      puts "  Advantages: #{benefit[:advantages].join(', ')}"
      puts "  Impact: #{benefit[:impact]}"
      puts
    end
  end
  
  # Run container fundamentals
  explain_container_concepts
  container_lifecycle
  container_vs_vm
  containerization_benefits
end
```

### 2. Docker Implementation

Docker container management:

```ruby
class DockerContainer
  def initialize(name, image)
    @name = name
    @image = image
    @status = :created
    @ports = {}
    @volumes = {}
    @environment = {}
    @command = nil
    @args = []
    @created_at = Time.now
    @started_at = nil
    @stopped_at = nil
    @logs = []
    @networks = []
  end
  
  attr_reader :name, :image, :status, :created_at, :started_at, :stopped_at
  
  def port_mapping(container_port, host_port)
    @ports[container_port] = host_port
    self
  end
  
  def volume_mount(host_path, container_path, options = {})
    @volumes[container_path] = {
      host_path: host_path,
      options: options
    }
    self
  end
  
  def env_var(name, value)
    @environment[name] = value
    self
  end
  
  def command(cmd, args = [])
    @command = cmd
    @args = args
    self
  end
  
  def network(network_name)
    @networks << network_name
    self
  end
  
  def start
    return false if @status == :running
    
    puts "Starting container: #{@name}"
    puts "Image: #{@image}"
    puts "Ports: #{@ports}"
    puts "Volumes: #{@volumes}"
    puts "Environment: #{@environment}"
    puts "Command: #{@command} #{@args.join(' ')}"
    
    # Simulate container startup
    @status = :running
    @started_at = Time.now
    
    # Add startup log
    add_log("Container started")
    
    puts "Container #{@name} started successfully"
    true
  end
  
  def stop
    return false if @status != :running
    
    puts "Stopping container: #{@name}"
    
    # Simulate container stop
    @status = :stopped
    @stopped_at = Time.now
    
    # Add stop log
    add_log("Container stopped")
    
    puts "Container #{@name} stopped"
    true
  end
  
  def restart
    stop if @status == :running
    start
  end
  
  def remove
    return false if @status == :running
    
    puts "Removing container: #{@name}"
    
    @status = :removed
    
    puts "Container #{@name} removed"
    true
  end
  
  def logs(tail = 10)
    puts "Container logs for #{@name}:"
    @logs.last(tail).each do |log|
      puts "  [#{log[:timestamp]}] #{log[:message]}"
    end
  end
  
  def exec(command)
    puts "Executing command in container #{@name}: #{command}"
    
    # Simulate command execution
    result = {
      exit_code: 0,
      output: "Command executed successfully",
      error: nil
    }
    
    add_log("Executed: #{command}")
    result
  end
  
  def stats
    {
      name: @name,
      image: @image,
      status: @status,
      created_at: @created_at,
      started_at: @started_at,
      stopped_at: @stopped_at,
      uptime: @started_at ? Time.now - @started_at : 0,
      ports: @ports,
      volumes: @volumes,
      networks: @networks,
      environment: @environment
    }
  end
  
  def self.demonstrate_container
    puts "Docker Container Demonstration:"
    puts "=" * 50
    
    # Create and configure container
    container = DockerContainer.new('web-app', 'nginx:latest')
    
    container
      .port_mapping(80, 8080)
      .port_mapping(443, 8443)
      .volume_mount('/host/logs', '/var/log/nginx')
      .env_var('NGINX_HOST', 'localhost')
      .env_var('NGINX_PORT', '80')
      .network('web-network')
      .command('nginx', ['-g', 'daemon off;'])
    
    puts "Container Configuration:"
    puts container.stats
    
    # Start container
    puts "\nStarting container:"
    container.start
    
    # Execute command
    puts "\nExecuting command:"
    result = container.exec('nginx -v')
    puts "Result: #{result[:output]}"
    
    # View logs
    puts "\nContainer logs:"
    container.logs
    
    # Stop container
    puts "\nStopping container:"
    container.stop
    
    # Remove container
    puts "\nRemoving container:"
    container.remove
    
    puts "\nDocker Container Features:"
    puts "- Port mapping"
    puts "- Volume mounting"
    puts "- Environment variables"
    puts "- Network configuration"
    puts "- Command execution"
    puts "- Log management"
    puts "- Container lifecycle"
  end
  
  private
  
  def add_log(message)
    @logs << {
      timestamp: Time.now,
      message: message
    }
  end
end

class DockerImage
  def initialize(name, tag = 'latest')
    @name = name
    @tag = tag
    @layers = []
    @size = 0
    @created_at = Time.now
  end
  
  attr_reader :name, :tag, :layers, :size, :created_at
  
  def add_layer(command, size = nil)
    layer = {
      command: command,
      size: size || rand(10..100),
      created_at: Time.now
    }
    
    @layers << layer
    @size += layer[:size]
    
    layer
  end
  
  def full_name
    "#{@name}:#{@tag}"
  end
  
  def history
    @layers.reverse
  end
  
  def inspect
    {
      name: @name,
      tag: @tag,
      size: @size,
      layers: @layers.length,
      created_at: @created_at,
      history: history
    }
  end
  
  def self.demonstrate_image
    puts "Docker Image Demonstration:"
    puts "=" * 50
    
    # Create image
    image = DockerImage.new('ruby-app', 'v1.0.0')
    
    # Add layers (simulating Dockerfile)
    image.add_layer('FROM ruby:3.0-slim', 150)
    image.add_layer('WORKDIR /app', 5)
    image.add_layer('COPY Gemfile Gemfile.lock ./', 10)
    image.add_layer('RUN bundle install', 200)
    image.add_layer('COPY . .', 50)
    image.add_layer('EXPOSE 3000', 5)
    image.add_layer('CMD ["rails", "server", "-b", "0.0.0.0"]', 5)
    
    puts "Image Information:"
    puts image.inspect
    
    puts "\nImage History:"
    image.history.each_with_index do |layer, i|
      puts "#{i + 1}. #{layer[:command]} (#{layer[:size]}MB)"
    end
    
    puts "\nDocker Image Features:"
    puts "- Layer-based architecture"
    puts "- Image versioning"
    puts "- Size optimization"
    puts "- History tracking"
    puts "- Tagging support"
  end
end

class DockerfileGenerator
  def self.generate_ruby_dockerfile(options = {})
    ruby_version = options[:ruby_version] || '3.0'
    rails_version = options[:rails_version] || '7.0'
    node_version = options[:node_version] || '16'
    
    dockerfile = <<~DOCKERFILE
      # Ruby on Rails application Dockerfile
      # Multi-stage build for optimization
      
      # Stage 1: Build stage
      FROM ruby:#{ruby_version}-slim as builder
      
      # Install system dependencies
      RUN apt-get update -qq && \\
          apt-get install -y build-essential libpq-dev nodejs npm \\
          && rm -rf /var/lib/apt/lists/*
      
      # Set working directory
      WORKDIR /app
      
      # Copy Gemfile and Gemfile.lock
      COPY Gemfile Gemfile.lock ./
      
      # Install Ruby dependencies
      RUN bundle config set --local without 'development test' && \\
          bundle install --jobs=4 --retry=3 && \\
          bundle clean --force
      
      # Copy package.json and package-lock.json
      COPY package.json package-lock.json ./
      
      # Install Node.js dependencies
      RUN npm ci --only=production && \\
          npm cache clean --force
      
      # Stage 2: Production stage
      FROM ruby:#{ruby_version}-slim as production
      
      # Install runtime dependencies
      RUN apt-get update -qq && \\
          apt-get install -y libpq-dev nodejs && \\
          rm -rf /var/lib/apt/lists/*
      
      # Set working directory
      WORKDIR /app
      
      # Copy installed gems
      COPY --from=builder /usr/local/bundle /usr/local/bundle
      COPY --from=builder /app/node_modules /app/node_modules
      
      # Copy application code
      COPY --from=builder /app /app
      
      # Precompile assets
      RUN SECRET_KEY_BASE=dummy RAILS_ENV=production \\
          bundle exec rails assets:precompile
      
      # Create non-root user
      RUN groupadd -r appuser && useradd -r -g appuser appuser
      
      # Change ownership
      RUN chown -R appuser:appuser /app
      
      # Switch to non-root user
      USER appuser
      
      # Expose port
      EXPOSE 3000
      
      # Health check
      HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \\
        CMD curl -f http://localhost:3000/health || exit 1
      
      # Start application
      CMD ["rails", "server", "-b", "0.0.0.0", "-p", "3000"]
      
      # Labels
      LABEL maintainer="devops@company.com" \\
            version="#{rails_version}" \\
            ruby-version="#{ruby_version}" \\
            node-version="#{node_version}"
    DOCKERFILE
    
    dockerfile
  end
  
  def self.generate_optimized_dockerfile
    dockerfile = <<~DOCKERFILE
      # Optimized multi-stage Dockerfile for Ruby on Rails
      # Stage 1: Dependencies
      FROM ruby:3.0-alpine as dependencies
      
      # Install build tools
      RUN apk add --no-cache \\
          build-base \\
          postgresql-dev \\
          git \\
          imagemagick-dev \\
          nodejs \\
          npm
      
      WORKDIR /app
      
      # Install Ruby dependencies
      COPY Gemfile Gemfile.lock ./
      RUN bundle config set --local without 'development test' && \\
          bundle install --jobs=4 --retry=3 && \\
          bundle clean --force
      
      # Install Node.js dependencies
      COPY package.json package-lock.json ./
      RUN npm ci --only=production && \\
          npm cache clean --force
      
      # Stage 2: Assets
      FROM node:16-alpine as assets
      
      WORKDIR /app
      
      COPY package.json package-lock.json ./
      RUN npm ci --only=production
      
      COPY . .
      RUN npm run build
      
      # Stage 3: Production
      FROM ruby:3.0-alpine as production
      
      # Install runtime dependencies
      RUN apk add --no-cache \\
          postgresql-client \\
          imagemagick \\
          nodejs \\
          npm
      
      # Add app user
      RUN addgroup -g appuser -S appuser && \\
          adduser -S appuser -G appuser
      
      WORKDIR /app
      
      # Copy dependencies
      COPY --from=dependencies /usr/local/bundle /usr/local/bundle
      COPY --from=assets /app/node_modules /app/node_modules
      COPY --from=assets /app/public /app/public
      
      # Copy application
      COPY . .
      
      # Precompile assets
      RUN SECRET_KEY_BASE=dummy RAILS_ENV=production \\
          bundle exec rails assets:precompile
      
      # Change ownership
      RUN chown -R appuser:appuser /app
      
      USER appuser
      
      EXPOSE 3000
      
      HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \\
        CMD wget --no-verbose --tries=1 --spider http://localhost:3000/health || exit 1
      
      CMD ["rails", "server", "-b", "0.0.0.0", "-p", "3000"]
    DOCKERFILE
    
    dockerfile
  end
  
  def self.demonstrate_dockerfile_generation
    puts "Dockerfile Generation Demonstration:"
    puts "=" * 50
    
    puts "Standard Ruby on Rails Dockerfile:"
    puts generate_ruby_dockerfile
    
    puts "\nOptimized Multi-stage Dockerfile:"
    puts generate_optimized_dockerfile
    
    puts "\nDockerfile Features:"
    puts "- Multi-stage builds"
    puts "- Layer optimization"
    puts "- Security best practices"
    puts "- Health checks"
    puts "- Non-root user"
    puts "- Environment variables"
    puts "- Asset precompilation"
  end
end
```

## 🚀 Docker Compose

### 3. Multi-Container Applications

Docker Compose orchestration:

```ruby
class DockerCompose
  def initialize(name, version = '3.8')
    @name = name
    @version = version
    @services = {}
    @networks = {}
    @volumes = {}
    @configs = {}
    @secrets = {}
    @environment = {}
  end
  
  attr_reader :name, :version, :services, :networks, :volumes
  
  def service(name, &block)
    service = DockerComposeService.new(name)
    service.instance_eval(&block) if block_given?
    @services[name] = service
    service
  end
  
  def network(name, &block)
    network = DockerComposeNetwork.new(name)
    network.instance_eval(&block) if block_given?
    @networks[name] = network
    network
  end
  
  def volume(name, &block)
    volume = DockerComposeVolume.new(name)
    volume.instance_eval(&block) if block_given?
    @volumes[name] = volume
    volume
  end
  
  def config(name, &block)
    config = DockerComposeConfig.new(name)
    config.instance_eval(&block) if block_given?
    @configs[name] = config
    config
  end
  
  def secret(name, &block)
    secret = DockerComposeSecret.new(name)
    secret.instance_eval(&block) if block_given?
    @secrets[name] = secret
    secret
  end
  
  def env_file(file_path)
    @environment[:env_file] = file_path
  end
  
  def generate_compose
    compose = {
      version: @version,
      name: @name,
      services: {},
      networks: {},
      volumes: {},
      configs: {},
      secrets: {}
    }
    
    # Add services
    @services.each do |name, service|
      compose[:services][name] = service.to_h
    end
    
    # Add networks
    @networks.each do |name, network|
      compose[:networks][name] = network.to_h
    end
    
    # Add volumes
    @volumes.each do |name, volume|
      compose[:volumes][name] = volume.to_h
    end
    
    # Add configs
    @configs.each do |name, config|
      compose[:configs][name] = config.to_h
    end
    
    # Add secrets
    @secrets.each do |name, secret|
      compose[:secrets][name] = secret.to_h
    end
    
    compose
  end
  
  def up(services = nil)
    puts "Starting Docker Compose: #{@name}"
    
    services_to_start = services || @services.keys
    
    services_to_start.each do |service_name|
      service = @services[service_name]
      next unless service
      
      puts "Starting service: #{service_name}"
      puts "  Image: #{service.image}"
      puts "  Ports: #{service.ports}" if service.ports.any?
      puts "  Volumes: #{service.volumes}" if service.volumes.any?
      puts "  Environment: #{service.environment}" if service.environment.any?
      
      # Simulate service start
      service.start
    end
    
    puts "Docker Compose started successfully"
  end
  
  def down(services = nil)
    puts "Stopping Docker Compose: #{@name}"
    
    services_to_stop = services || @services.keys
    
    services_to_stop.each do |service_name|
      service = @services[service_name]
      next unless service
      
      puts "Stopping service: #{service_name}"
      service.stop
    end
    
    puts "Docker Compose stopped"
  end
  
  def logs(services = nil, follow = false)
    services_to_log = services || @services.keys
    
    services_to_log.each do |service_name|
      service = @services[service_name]
      next unless service
      
      puts "Logs for service: #{service_name}"
      service.logs
    end
  end
  
  def self.demonstrate_compose
    puts "Docker Compose Demonstration:"
    puts "=" * 50
    
    # Create compose file
    compose = DockerCompose.new('ruby-app')
    
    # Define services
    compose.service('web') do
      image 'ruby-app:latest'
      build 'context: .'
      
      ports do
        port '3000:3000'
      end
      
      volumes do
        volume '.:/app'
        volume 'gems:/usr/local/bundle'
      end
      
      environment do
        env 'RAILS_ENV', 'development'
        env 'DATABASE_URL', 'postgresql://postgres:password@db:5432/rubyapp_development'
      end
      
      depends_on 'db'
      depends_on 'redis'
      
      networks do
        network 'app-network'
      end
    end
    
    compose.service('db') do
      image 'postgres:14'
      
      volumes do
        volume 'postgres_data:/var/lib/postgresql/data'
      end
      
      environment do
        env 'POSTGRES_DB', 'rubyapp_development'
        env 'POSTGRES_USER', 'postgres'
        env 'POSTGRES_PASSWORD', 'password'
      end
      
      networks do
        network 'app-network'
      end
    end
    
    compose.service('redis') do
      image 'redis:6-alpine'
      
      volumes do
        volume 'redis_data:/data'
      end
      
      networks do
        network 'app-network'
      end
    end
    
    compose.service('nginx') do
      image 'nginx:alpine'
      
      ports do
        port '80:80'
        port '443:443'
      end
      
      volumes do
        volume './nginx.conf:/etc/nginx/nginx.conf'
        volume './ssl:/etc/ssl/certs'
      end
      
      depends_on 'web'
      
      networks do
        network 'app-network'
      end
    end
    
    # Define networks
    compose.network('app-network') do
      driver 'bridge'
    end
    
    # Define volumes
    compose.volume('postgres_data') do
      driver 'local'
    end
    
    compose.volume('redis_data') do
      driver 'local'
    end
    
    compose.volume('gems') do
      driver 'local'
    end
    
    # Generate compose file
    compose_yaml = compose.generate_compose
    
    puts "Generated Docker Compose:"
    puts compose_yaml.to_yaml
    
    # Simulate up and down
    puts "\nStarting services:"
    compose.up
    
    puts "\nViewing logs:"
    compose.logs(['web', 'db'])
    
    puts "\nStopping services:"
    compose.down
    
    puts "\nDocker Compose Features:"
    puts "- Multi-service orchestration"
    puts "- Service dependencies"
    puts "- Network management"
    puts "- Volume management"
    puts "- Environment variables"
    puts "- Build configuration"
    puts "- Service scaling"
  end
  
  private
end

class DockerComposeService
  def initialize(name)
    @name = name
    @image = nil
    @build = nil
    @ports = {}
    @volumes = {}
    @environment = {}
    @depends_on = []
    @networks = []
    @command = nil
    @restart = 'no'
    @healthcheck = nil
    @started = false
  end
  
  attr_reader :name, :image, :ports, :volumes, :environment, :depends_on, :networks
  
  def image(image_name)
    @image = image_name
  end
  
  def build(build_config)
    @build = build_config
  end
  
  def ports(&block)
    ports_manager = PortsManager.new(@ports)
    ports_manager.instance_eval(&block) if block_given?
    @ports = ports_manager.ports
  end
  
  def volumes(&block)
    volumes_manager = VolumesManager.new(@volumes)
    volumes_manager.instance_eval(&block) if block_given?
    @volumes = volumes_manager.volumes
  end
  
  def environment(&block)
    env_manager = EnvironmentManager.new(@environment)
    env_manager.instance_eval(&block) if block_given?
    @environment = env_manager.environment
  end
  
  def depends_on(*services)
    @depends_on.concat(services)
  end
  
  def networks(&block)
    networks_manager = NetworksManager.new(@networks)
    networks_manager.instance_eval(&block) if block_given?
    @networks = networks_manager.networks
  end
  
  def command(cmd)
    @command = cmd
  end
  
  def restart(policy)
    @restart = policy
  end
  
  def healthcheck(&block)
    healthcheck_manager = HealthcheckManager.new
    healthcheck_manager.instance_eval(&block) if block_given?
    @healthcheck = healthcheck_manager.config
  end
  
  def start
    @started = true
    puts "  Service #{@name} started"
  end
  
  def stop
    @started = false
    puts "  Service #{@name} stopped"
  end
  
  def logs
    puts "  Service #{@name} logs:"
    puts "    [timestamp] Starting service..."
    puts "    [timestamp] Service ready"
    puts "    [timestamp] Handling requests..."
  end
  
  def to_h
    service_hash = {
      image: @image,
      restart: @restart
    }
    
    service_hash[:build] = @build if @build
    service_hash[:ports] = @ports if @ports.any?
    service_hash[:volumes] = @volumes if @volumes.any?
    service_hash[:environment] = @environment if @environment.any?
    service_hash[:depends_on] = @depends_on if @depends_on.any?
    service_hash[:networks] = @networks if @networks.any?
    service_hash[:command] = @command if @command
    service_hash[:healthcheck] = @healthcheck if @healthcheck
    
    service_hash
  end
end

# Helper classes for Docker Compose
class PortsManager
  def initialize(ports)
    @ports = ports
  end
  
  attr_reader :ports
  
  def port(mapping)
    @ports[mapping] = mapping
  end
  
  def method_missing(name, *args, &block)
    port(name, *args, &block)
  end
end

class VolumesManager
  def initialize(volumes)
    @volumes = volumes
  end
  
  attr_reader :volumes
  
  def volume(mount)
    @volumes[mount] = mount
  end
  
  def method_missing(name, *args, &block)
    volume(name, *args, &block)
  end
end

class EnvironmentManager
  def initialize(environment)
    @environment = environment
  end
  
  attr_reader :environment
  
  def method_missing(name, value)
    @environment[name] = value
  end
end

class NetworksManager
  def initialize(networks)
    @networks = networks
  end
  
  attr_reader :networks
  
  def network(network_name)
    @networks << network_name
  end
  
  def method_missing(name, *args, &block)
    network(name, *args, &block)
  end
end

class HealthcheckManager
  def initialize
    @config = {}
  end
  
  attr_reader :config
  
  def test(command)
    @config[:test] = command
  end
  
  def interval(interval)
    @config[:interval] = interval
  end
  
  def timeout(timeout)
    @config[:timeout] = timeout
  end
  
  def retries(retries)
    @config[:retries] = retries
  end
end

class DockerComposeNetwork
  def initialize(name)
    @name = name
    @driver = 'bridge'
    @options = {}
  end
  
  attr_reader :name
  
  def driver(driver_name)
    @driver = driver_name
  end
  
  def options(&block)
    options_manager = NetworkOptionsManager.new(@options)
    options_manager.instance_eval(&block) if block_given?
    @options = options_manager.options
  end
  
  def to_h
    network_hash = {
      driver: @driver
    }
    
    network_hash[:options] = @options if @options.any?
    
    network_hash
  end
end

class NetworkOptionsManager
  def initialize(options)
    @options = options
  end
  
  attr_reader :options
  
  def method_missing(name, value)
    @options[name] = value
  end
end

class DockerComposeVolume
  def initialize(name)
    @name = name
    @driver = 'local'
    @options = {}
  end
  
  attr_reader :name
  
  def driver(driver_name)
    @driver = driver_name
  end
  
  def options(&block)
    options_manager = VolumeOptionsManager.new(@options)
    options_manager.instance_eval(&block) if block_given?
    @options = options_manager.options
  end
  
  def to_h
    volume_hash = {
      driver: @driver
    }
    
    volume_hash[:options] = @options if @options.any?
    
    volume_hash
  end
end

class VolumeOptionsManager
  def initialize(options)
    @options = options
  end
  
  attr_reader :options
  
  def method_missing(name, value)
    @options[name] = value
  end
end

class DockerComposeConfig
  def initialize(name)
    @name = name
    @file = nil
    @external = false
  end
  
  attr_reader :name
  
  def file(file_path)
    @file = file_path
  end
  
  def external
    @external = true
  end
  
  def to_h
    config_hash = {}
    
    config_hash[:file] = @file if @file
    config_hash[:external] = @external if @external
    
    config_hash
  end
end

class DockerComposeSecret
  def initialize(name)
    @name = name
    @file = nil
    @external = false
  end
  
  attr_reader :name
  
  def file(file_path)
    @file = file_path
  end
  
  def external
    @external = true
  end
  
  def to_h
    secret_hash = {}
    
    secret_hash[:file] = @file if @file
    secret_hash[:external] = @external if @external
    
    secret_hash
  end
end
```

## ☸️ Kubernetes Orchestration

### 4. Kubernetes Deployment

Kubernetes application deployment:

```ruby
class KubernetesDeployment
  def initialize(name, namespace = 'default')
    @name = name
    @namespace = namespace
    @deployments = {}
    @services = {}
    @config_maps = {}
    @secrets = {}
    @ingresses = {}
    @persistent_volumes = {}
    @persistent_volume_claims = {}
  end
  
  attr_reader :name, :namespace
  
  def deployment(name, &block)
    deployment = K8sDeployment.new(name, @namespace)
    deployment.instance_eval(&block) if block_given?
    @deployments[name] = deployment
    deployment
  end
  
  def service(name, &block)
    service = K8sService.new(name, @namespace)
    service.instance_eval(&block) if block_given?
    @services[name] = service
    service
  end
  
  def config_map(name, &block)
    config_map = K8sConfigMap.new(name, @namespace)
    config_map.instance_eval(&block) if block_given?
    @config_maps[name] = config_map
    config_map
  end
  
  def secret(name, &block)
    secret = K8sSecret.new(name, @namespace)
    secret.instance_eval(&block) if block_given?
    @secrets[name] = secret
    secret
  end
  
  def ingress(name, &block)
    ingress = K8sIngress.new(name, @namespace)
    ingress.instance_eval(&block) if block_given?
    @ingresses[name] = ingress
    ingress
  end
  
  def persistent_volume(name, &block)
    pv = K8sPersistentVolume.new(name)
    pv.instance_eval(&block) if block_given?
    @persistent_volumes[name] = pv
    pv
  end
  
  def persistent_volume_claim(name, &block)
    pvc = K8sPersistentVolumeClaim.new(name, @namespace)
    pvc.instance_eval(&block) if block_given?
    @persistent_volume_claims[name] = pvc
    pvc
  end
  
  def generate_kubernetes_yaml
    k8s = {
      apiVersion: 'v1',
      kind: 'List',
      items: []
    }
    
    # Add deployments
    @deployments.each do |name, deployment|
      k8s[:items] << deployment.to_h
    end
    
    # Add services
    @services.each do |name, service|
      k8s[:items] << service.to_h
    end
    
    # Add config maps
    @config_maps.each do |name, config_map|
      k8s[:items] << config_map.to_h
    end
    
    # Add secrets
    @secrets.each do |name, secret|
      k8s[:items] << secret.to_h
    end
    
    # Add ingresses
    @ingresses.each do |name, ingress|
      k8s[:items] << ingress.to_h
    end
    
    # Add persistent volumes
    @persistent_volumes.each do |name, pv|
      k8s[:items] << pv.to_h
    end
    
    # Add persistent volume claims
    @persistent_volume_claims.each do |name, pvc|
      k8s[:items] << pvc.to_h
    end
    
    k8s
  end
  
  def apply
    puts "Applying Kubernetes deployment: #{@name}"
    
    k8s_config = generate_kubernetes_yaml
    
    puts "Namespace: #{@namespace}"
    puts "Deployments: #{@deployments.length}"
    puts "Services: #{@services.length}"
    puts "Config Maps: #{@config_maps.length}"
    puts "Secrets: #{@secrets.length}"
    puts "Ingresses: #{@ingresses.length}"
    puts "Persistent Volumes: #{@persistent_volumes.length}"
    puts "Persistent Volume Claims: #{@persistent_volume_claims.length}"
    
    # Simulate applying to cluster
    k8s_config[:items].each do |item|
      kind = item[:kind]
      name = item[:metadata][:name]
      
      puts "  Creating #{kind}: #{name}"
      
      case kind
      when 'Deployment'
        puts "    Deploying application"
      when 'Service'
        puts "    Exposing service"
      when 'ConfigMap'
        puts "    Creating config map"
      when 'Secret'
        puts "    Creating secret"
      when 'Ingress'
        puts "    Creating ingress"
      when 'PersistentVolume'
        puts "    Creating persistent volume"
      when 'PersistentVolumeClaim'
        puts "    Creating persistent volume claim"
      end
    end
    
    {
      success: true,
      namespace: @namespace,
      resources: {
        deployments: @deployments.length,
        services: @services.length,
        config_maps: @config_maps.length,
        secrets: @secrets.length,
        ingresses: @ingresses.length,
        persistent_volumes: @persistent_volumes.length,
        persistent_volume_claims: @persistent_volume_claims.length
      }
    }
  end
  
  def self.demonstrate_kubernetes_deployment
    puts "Kubernetes Deployment Demonstration:"
    puts "=" * 50
    
    # Create deployment
    deployment = KubernetesDeployment.new('ruby-app', 'production')
    
    # Create web application deployment
    deployment.deployment('web-app') do
      replicas 3
      
      labels do
        app 'ruby-app'
        version 'v1.0.0'
        environment 'production'
      end
      
      selector do
        match_labels do
          app 'ruby-app'
        end
      end
      
      template do
        metadata do
          labels do
            app 'ruby-app'
            version 'v1.0.0'
            environment 'production'
          end
        end
        
        spec do
          containers do
            container do
              name 'ruby-app'
              image 'ruby-app:latest'
              image_pull_policy 'Always'
              
              ports do
                container_port 3000
                protocol 'TCP'
              end
              
              env do
                name 'RAILS_ENV'
                value 'production'
              end
              
              env do
                name_from_secret 'DATABASE_URL'
                secret_key_ref 'database-secrets'
              end
              
              resources do
                requests do
                  memory '256Mi'
                  cpu '250m'
                end
                
                limits do
                  memory '512Mi'
                  cpu '500m'
                end
              end
              
              volume_mount do
                name 'app-storage'
                mount_path '/app/storage'
              end
            end
          end
          
          volumes do
            persistent_volume_claim do
              claim_name 'app-storage-pvc'
              read_only false
            end
          end
        end
      end
    end
    
    # Create service
    deployment.service('web-app-service') do
      selector do
        app 'ruby-app'
      end
      
      ports do
        port 80
        target_port 3000
        protocol 'TCP'
      end
      
      type 'ClusterIP'
    end
    
    # Create ingress
    deployment.ingress('web-app-ingress') do
      annotations do
        annotation 'kubernetes.io/ingress.class', 'nginx'
        annotation 'cert-manager.io/cluster-issuer', 'letsencrypt-prod'
      end
      
      rules do
        host 'ruby-app.example.com'
        paths do
          path '/'
          path_type 'Prefix'
          backend do
            service_name 'web-app-service'
            service_port 80
          end
        end
      end
      
      tls do
        hosts 'ruby-app.example.com'
        secret_name 'ruby-app-tls'
      end
    end
  
    # Create config map
    deployment.config_map('app-config') do
      data do
        'database.yml' => <<~CONFIG
          production:
            adapter: postgresql
            encoding: unicode
            pool: 5
            database: rubyapp_production
        CONFIG
      end
    end
    
    # Create secret
    deployment.secret('database-secrets') do
      data do
        'DATABASE_URL' => Base64.strict_encode64('postgresql://user:password@postgres:5432/rubyapp_production')
        'SECRET_KEY_BASE' => Base64.strict_encode64('super-secret-key')
      end
    end
    
    # Create persistent volume claim
    deployment.persistent_volume_claim('app-storage-pvc') do
      access_modes 'ReadWriteOnce'
      storage_class 'gp2'
      resources do
        requests do
          storage '1Gi'
        end
      end
    end
    
    # Generate and apply configuration
    k8s_config = deployment.generate_kubernetes_yaml
    
    puts "Generated Kubernetes Configuration:"
    puts k8s_config.to_yaml
    
    # Apply configuration
    result = deployment.apply
    
    puts "\nKubernetes Deployment Features:"
    puts "- Deployment management"
    puts "- Service exposure"
    puts "- ConfigMap management"
    puts "- Secret management"
    puts "- Ingress configuration"
    puts "- Persistent volume management"
    puts "- Multi-resource coordination"
    puts "- Namespace isolation"
  end
  
  private
end

class K8sDeployment
  def initialize(name, namespace)
    @name = name
    @namespace = namespace
    @replicas = 1
    @labels = {}
    @selector = {}
    @template = {}
    @strategy = {}
  end
  
  attr_reader :name, :namespace
  
  def replicas(count)
    @replicas = count
  end
  
  def labels(&block)
    labels_manager = LabelsManager.new(@labels)
    labels_manager.instance_eval(&block) if block_given?
    @labels = labels_manager.labels
  end
  
  def selector(&block)
    selector_manager = SelectorManager.new(@selector)
    selector_manager.instance_eval(&block) if block_given?
    @selector = selector_manager.selector
  end
  
  def strategy(&block)
    strategy_manager = StrategyManager.new(@strategy)
    strategy_manager.instance_eval(&block) if block_given?
    @strategy = strategy_manager.strategy
  end
  
  def template(&block)
    template_manager = TemplateManager.new(@template)
    template_manager.instance_eval(&block) if block_given?
    @template = template_manager.template
  end
  
  def to_h
    {
      apiVersion: 'apps/v1',
      kind: 'Deployment',
      metadata: {
        name: @name,
        namespace: @namespace,
        labels: @labels
      },
      spec: {
        replicas: @replicas,
        selector: @selector,
        template: @template,
        strategy: @strategy
      }
    }
  end
end

class K8sService
  def initialize(name, namespace)
    @name = name
    @namespace = namespace
    @selector = {}
    @ports = []
    @type = 'ClusterIP'
    @labels = {}
    @annotations = {}
  end
  
  attr_reader :name, :namespace
  
  def selector(&block)
    selector_manager = SelectorManager.new(@selector)
    selector_manager.instance_eval(&block) if block_given?
    @selector = selector_manager.selector
  end
  
  def ports(&block)
    ports_manager = ServicePortsManager.new(@ports)
    ports_manager.instance_eval(&block) if block_given?
    @ports = ports_manager.ports
  end
  
  def type(service_type)
    @type = service_type
  end
  
  def labels(&block)
    labels_manager = LabelsManager.new(@labels)
    labels_manager.instance_eval(&block) if block_given?
    @labels = labels_manager.labels
  end
  
  def annotations(&block)
    annotations_manager = AnnotationsManager.new(@annotations)
    annotations_manager.instance_eval(&block) if block_given?
    @annotations = annotations_manager.annotations
  end
  
  def to_h
    {
      apiVersion: 'v1',
      kind: 'Service',
      metadata: {
        name: @name,
        namespace: @namespace,
        labels: @labels,
        annotations: @annotations
      },
      spec: {
        selector: @selector,
        ports: @ports,
        type: @type
      }
    }
  end
end

class K8sConfigMap
  def initialize(name, namespace)
    @name = name
    @namespace = namespace
    @data = {}
    @labels = {}
    @annotations = {}
  end
  
  attr_reader :name, :namespace
  
  def data(&block)
    data_manager = DataManager.new(@data)
    data_manager.instance_eval(&block) if block_given?
    @data = data_manager.data
  end
  
  def labels(&block)
    labels_manager = LabelsManager.new(@labels)
    labels_manager.instance_eval(&block) if block_given?
    @labels = labels_manager.labels
  end
  
  def annotations(&block)
    annotations_manager = AnnotationsManager.new(@annotations)
    annotations_manager.instance_eval(&block) if block_given?
    @annotations = annotations_manager.annotations
  end
  
  def to_h
    {
      apiVersion: 'v1',
      kind: 'ConfigMap',
      metadata: {
        name: @name,
        namespace: @namespace,
        labels: @labels,
        annotations: @annotations
      },
      data: @data
    }
  end
end

class K8sSecret
  def initialize(name, namespace)
    @name = name
    @namespace = namespace
    @data = {}
    @type = 'Opaque'
    @labels = {}
    @annotations = {}
  end
  
  attr_reader :name, :namespace
  
  def data(&block)
    data_manager = SecretDataManager.new(@data)
    data_manager.instance_eval(&block) if block_given?
    @data = data_manager.data
  end
  
  def type(secret_type)
    @type = secret_type
  end
  
  def labels(&block)
    labels_manager = LabelsManager.new(@labels)
    labels_manager.instance_eval(&block) if block_given?
    @labels = labels_manager.labels
  end
  
  def annotations(&block)
    annotations_manager = AnnotationsManager.new(@annotations)
    annotations_manager.instance_eval(&block) if block_given?
    @annotations = annotations_manager.annotations
  end
  
  def to_h
    {
      apiVersion: 'v1',
      kind: 'Secret',
      metadata: {
        name: @name,
        namespace: @namespace,
        labels: @labels,
        annotations: @annotations
      },
      type: @type,
      data: @data
    }
  end
end

# Additional helper classes for Kubernetes
class LabelsManager
  def initialize(labels)
    @labels = labels
  end
  
  attr_reader :labels
  
  def method_missing(name, value)
    @labels[name] = value
  end
end

class AnnotationsManager
  def initialize(annotations)
    @annotations = annotations
  end
  
  attr_reader :annotations
  
  def method_missing(name, value)
    @annotations[name] = value
  end
end

class SelectorManager
  def initialize(selector)
    @selector = selector
  end
  
  attr_reader :selector
  
  def match_labels(&block)
    labels_manager = LabelsManager.new({})
    labels_manager.instance_eval(&block) if block_given?
    @selector[:matchLabels] = labels_manager.labels
  end
end

class ServicePortsManager
  def initialize(ports)
    @ports = ports
  end
  
  attr_reader :ports
  
  def port(port, options = {})
    port_def = { port: port }
    port_def[:targetPort] = options[:target_port] if options[:target_port]
    port_def[:protocol] = options[:protocol] if options[:protocol]
    @ports << port_def
  end
  
  def method_missing(name, *args, &block)
    port(name, *args, &block)
  end
end

class StrategyManager
  def initialize(strategy)
    @strategy = strategy
  end
  
  attr_reader :strategy
  
  def type(strategy_type)
    @strategy[:type] = strategy_type
  end
  
  def rolling_update(&block)
    @strategy[:type] = 'RollingUpdate'
    rolling_update_manager = RollingUpdateManager.new(@strategy)
    rolling_update_manager.instance_eval(&block) if block_given?
  end
end

class RollingUpdateManager
  def initialize(strategy)
    @strategy = strategy
  end
  
  def max_unavailable(value)
    @strategy[:rollingUpdate] ||= {}
    @strategy[:rollingUpdate][:maxUnavailable] = value
  end
  
  def max_surge(value)
    @strategy[:rollingUpdate] ||= {}
    @strategy[:rollingUpdate][:maxSurge] = value
  end
end

class TemplateManager
  def initialize(template)
    @template = template
  end
  
  attr_reader :template
  
  def metadata(&block)
    metadata_manager = MetadataManager.new(@template)
    metadata_manager.instance_eval(&block) if block_given?
  end
  
  def spec(&block)
    spec_manager = SpecManager.new(@template)
    spec_manager.instance_eval(&block) if block_given?
  end
end

class MetadataManager
  def initialize(template)
    @template = template
  end
  
  def labels(&block)
    labels_manager = LabelsManager.new({})
    labels_manager.instance_eval(&block) if block_given?
    @template[:metadata] ||= {}
    @template[:metadata][:labels] = labels_manager.labels
  end
end

class SpecManager
  def initialize(template)
    @template = template
  end
  
  def containers(&block)
    containers_manager = ContainersManager.new(@template)
    containers_manager.instance_eval(&block) if block_given?
  end
  
  def volumes(&block)
    volumes_manager = VolumesManager.new(@template)
    volumes_manager.instance_eval(&block) if block_given?
  end
end

class ContainersManager
  def initialize(template)
    @template = template
    @containers = []
  end
  
  def container(&block)
    container_manager = ContainerManager.new
    container_manager.instance_eval(&block) if block_given?
    @containers << container_manager.container
  end
  
  def containers
    @containers.map(&:to_h)
  end
end

class ContainerManager
  def initialize
    @container = {}
  end
  
  attr_reader :container
  
  def name(name)
    @container[:name] = name
  end
  
  def image(image_name)
    @container[:image] = image_name
  end
  
  def image_pull_policy(policy)
    @container[:imagePullPolicy] = policy
  end
  
  def ports(&block)
    ports_manager = ContainerPortsManager.new(@container)
    ports_manager.instance_eval(&block) if block_given?
  end
  
  def env(&block)
    env_manager = ContainerEnvManager.new(@container)
    env_manager.instance_eval(&block) if block_given?
  end
  
  def resources(&block)
    resources_manager = ContainerResourcesManager.new(@container)
    resources_manager.instance_eval(&block) if block_given?
  end
  
  def volume_mount(&block)
    volume_mount_manager = VolumeMountManager.new(@container)
    volume_mount_manager.instance_eval(&block) if block_given?
  end
  
  def to_h
    @container
  end
end

class ContainerPortsManager
  def initialize(container)
    @container = container
  end
  
  def container_port(port)
    @container[:ports] ||= []
    @container[:ports] << { containerPort: port }
  end
  
  def protocol(protocol)
    @container[:ports] ||= []
    @container[:ports].last[:protocol] = protocol
  end
end

class ContainerEnvManager
  def initialize(container)
    @container = container
  end
  
  def name(name)
    @container[:env] ||= []
    @container[:env] << { name: name }
  end
  
  def value(value)
    @container[:env] ||= []
    @container[:env].last[:value] = value
  end
  
  def value_from_secret(secret_key_ref)
    @container[:env] ||= []
    @container[:env].last[:valueFrom] = {
      secretKeyRef: { name: secret_key_ref }
    }
  end
end

class ContainerResourcesManager
  def initialize(container)
    @container = container
  end
  
  def requests(&block)
    requests_manager = ResourceRequestsManager.new(@container)
    requests_manager.instance_eval(&block) if block_given?
  end
  
  def limits(&block)
    limits_manager = ResourceLimitsManager.new(@container)
    limits_manager.instance_eval(&block) if block_given?
  end
end

class ResourceRequestsManager
  def initialize(container)
    @container = container
  end
  
  def memory(memory)
    @container[:resources] ||= {}
    @container[:resources][:requests] ||= {}
    @container[:resources][:requests][:memory] = memory
  end
  
  def cpu(cpu)
    @container[:resources] ||= {}
    @container[:resources][:requests] ||= {}
    @container[:resources][:requests][:cpu] = cpu
  end
end

class ResourceLimitsManager
  def initialize(container)
    @container = container
  end
  
  def memory(memory)
    @container[:resources] ||= {}
    @container[:resources][:limits] ||= {}
    @container[:resources][:limits][:memory] = memory
  end
  
  def cpu(cpu)
    @container[:resources] ||= {}
    @container[:resources][:limits] ||= {}
    @container[:resources][:limits][:cpu] = cpu
  end
end

class VolumeMountManager
  def initialize(container)
    @container = container
  end
  
  def name(name)
    @container[:volumeMounts] ||= []
    @container[:volumeMounts] << { name: name }
  end
  
  def mount_path(path)
    @container[:volumeMounts] ||= []
    @container[:volumeMounts].last[:mountPath] = path
  end
  
  def read_only(read_only)
    @container[:volumeMounts] ||= []
    @container[:volumeMounts].last[:readOnly] = read_only
  end
end

class VolumesManager
  def initialize(template)
    @template = template
    @volumes = []
  end
  
  def persistent_volume_claim(&block)
    pvc_manager = PVCManager.new(@template)
    pvc_manager.instance_eval(&block) if block_given?
    @volumes << pvc_manager.volume
  end
  
  def volumes
    @volumes.map(&:to_h)
  end
end

class PVCManager
  def initialize(template)
    @template = template
    @volume = {}
  end
  
  attr_reader :volume
  
  def claim_name(claim_name)
    @volume[:persistentVolumeClaim] = { claimName: claim_name }
  end
  
  def read_only(read_only)
    @volume[:readOnly] = read_only
  end
  
  def to_h
    @volume
  end
end

class DataManager
  def initialize(data)
    @data = data
  end
  
  attr_reader :data
  
  def method_missing(key, value)
    @data[key] = value
  end
end

class SecretDataManager
  def initialize(data)
    @data = data
  end
  
  attr_reader :data
  
  def method_missing(key, value)
    @data[key] = Base64.strict_encode64(value)
  end
end

class K8sIngress
  def initialize(name, namespace)
    @name = name
    @namespace = namespace
    @rules = []
    @tls = []
    @annotations = {}
    @labels = {}
  end
  
  attr_reader :name, :namespace
  
  def annotations(&block)
    annotations_manager = AnnotationsManager.new(@annotations)
    annotations_manager.instance_eval(&block) if block_given?
    @annotations = annotations_manager.annotations
  end
  
  def labels(&block)
    labels_manager = LabelsManager.new(@labels)
    labels_manager.instance_eval(&block) if block_given?
    @labels = labels_manager.labels
  end
  
  def rules(&block)
    rules_manager = IngressRulesManager.new(@rules)
    rules_manager.instance_eval(&block) if block_given?
    @rules = rules_manager.rules
  end
  
  def tls(&block)
    tls_manager = TLSManager.new(@tls)
    tls_manager.instance_eval(&block) if block_given?
    @tls = tls_manager.tls
  end
  
  def to_h
    {
      apiVersion: 'networking.k8s.io/v1',
      kind: 'Ingress',
      metadata: {
        name: @name,
        namespace: @namespace,
        annotations: @annotations,
        labels: @labels
      },
      spec: {
        rules: @rules.map(&:to_h),
        tls: @tls
      }
    }
  end
end

class IngressRulesManager
  def initialize(rules)
    @rules = rules
  end
  
  attr_reader :rules
  
  def rule(&block)
    rule_manager = IngressRuleManager.new
    rule_manager.instance_eval(&block) if block_given?
    @rules << rule_manager.rule
  end
  
  def method_missing(name, *args, &block)
    rule(name, *args, &block)
  end
end

class IngressRuleManager
  def initialize
    @rule = {}
  end
  
  attr_reader :rule
  
  def host(host)
    @rule[:host] = host
  end
  
  def paths(&block)
    paths_manager = IngressPathsManager.new(@rule)
    paths_manager.instance_eval(&block) if block_given?
  end
  
  def backend(&block)
    backend_manager = IngressBackendManager.new(@rule)
    backend_manager.instance_eval(&block) if block_given?
  end
end

class IngressPathsManager
  def initialize(rule)
    @rule = rule
  end
  
  def path(path)
    @rule[:path] = path
  end
  
  def path_type(type)
    @rule[:pathType] = type
  end
end

class IngressBackendManager
  def initialize(rule)
    @rule = rule
  end
  
  def service_name(service_name)
    @rule[:service] = { name: service_name }
  end
  
  def service_port(service_port)
    @rule[:service][:port] = { number: service_port }
  end
end

class TLSManager
  def initialize(tls)
    @tls = tls
  end
  
  attr_reader :tls
  
  def hosts(hosts)
    @tls[:hosts] = Array(hosts)
  end
  
  def secret_name(secret_name)
    @tls[:secretName] = secret_name
  end
end

class K8sPersistentVolume
  def initialize(name)
    @name = name
    @spec = {}
  end
  
  attr_reader :name
  
  def storage_class(storage_class)
    @spec[:storageClassName] = storage_class
  end
  
  def access_modes(modes)
    @spec[:accessModes] = Array(modes)
  end
  
  def capacity(storage)
    @spec[:capacity] = { storage: storage }
  end
  
  def host_path(path)
    @spec[:hostPath] = { path: path }
  end
  
  def to_h
    {
      apiVersion: 'v1',
      kind: 'PersistentVolume',
      metadata: {
        name: @name
      },
      spec: @spec
    }
  end
end

class K8sPersistentVolumeClaim
  def initialize(name, namespace)
    @name = name
    @namespace = namespace
    @spec = {}
  end
  
  attr_reader :name, :namespace
  
  def access_modes(modes)
    @spec[:accessModes] = Array(modes)
  end
  
  def storage_class(storage_class)
    @spec[:storageClassName] = storage_class
  end
  
  def resources(&block)
    resources_manager = ResourcesManager.new(@spec)
    resources_manager.instance_eval(&block) if block_given?
  end
  
  def to_h
    {
      apiVersion: 'v1',
      kind: 'PersistentVolumeClaim',
      metadata: {
        name: @name,
        namespace: @namespace
      },
      spec: @spec
    }
  end
end

class ResourcesManager
  def initialize(spec)
    @spec = spec
  end
  
  def requests(&block)
    requests_manager = ResourceRequestsManager.new(@spec)
    requests_manager.instance_eval(&block) if block_given?
  end
end
```

## 📊 Container Monitoring

### 5. Container Observability

Monitoring and logging for containers:

```ruby
class ContainerMonitor
  def initialize
    @containers = {}
    @metrics = {}
    @logs = []
    @alerts = []
    @dashboards = {}
    @thresholds = {
      cpu_usage: 80,
      memory_usage: 80,
      disk_usage: 85,
      network_io: 1000,
      restart_count: 5,
      error_rate: 5
    }
  end
  
  def track_container(container_id, container_info)
    @containers[container_id] = {
      id: container_id,
      name: container_info[:name],
      image: container_info[:image],
      status: container_info[:status],
      created_at: Time.now,
      started_at: container_info[:started_at],
      stopped_at: nil,
      restart_count: 0,
      last_error: nil
    }
    
    puts "Tracking container: #{container_info[:name]} (#{container_id})"
  end
  
  def update_container_status(container_id, status)
    container = @containers[container_id]
    return unless container
    
    old_status = container[:status]
    container[:status] = status
    
    case status
    when 'running'
      container[:started_at] ||= Time.now
      puts "Container #{container[:name]} is running"
    when 'stopped'
      container[:stopped_at] = Time.now
      puts "Container #{container[:name]} stopped"
    when 'error'
      container[:last_error] = Time.now
      puts "Container #{container[:name]} encountered an error"
    end
    
    # Log status change
    if old_status != status
      log_container_event(container_id, 'status_change', {
        from: old_status,
        to: status
      })
    end
  end
  
  def collect_metrics(container_id, metrics)
    @metrics[container_id] ||= []
    
    metric_data = {
      timestamp: Time.now,
      metrics: metrics
    }
    
    @metrics[container_id] << metric_data
    
    # Check thresholds
    check_thresholds(container_id, metrics)
    
    # Keep only last 1000 metrics
    if @metrics[container_id].length > 1000
      @metrics[container_id] = @metrics[container_id].last(1000)
    end
  end
  
  def restart_container(container_id)
    container = @containers[container_id]
    return unless container
    
    container[:restart_count] += 1
    puts "Container #{container[:name]} restarted (#{container[:restart_count]} times)"
    
    # Check restart threshold
    if container[:restart_count] >= @thresholds[:restart_count]
      add_alert('container_restart_threshold', {
        container_id: container_id,
        container_name: container[:name],
        restart_count: container[:restart_count],
        threshold: @thresholds[:restart_count]
      })
    end
    
    log_container_event(container_id, 'restart', {
      restart_count: container[:restart_count]
    })
  end
  
  def log_container_event(container_id, event_type, details = {})
    container = @containers[container_id]
    return unless container
    
    event = {
      timestamp: Time.now,
      container_id: container_id,
      container_name: container[:name],
      event_type: event_type,
      details: details
    }
    
    @logs << event
    
    # Keep only last 10000 logs
    if @logs.length > 10000
      @logs = @logs.last(10000)
    end
  end
  
  def add_alert(alert_type, details)
    alert = {
      id: SecureRandom.hex(8),
      type: alert_type,
      details: details,
      timestamp: Time.now,
      resolved: false
    }
    
    @alerts << alert
    puts "Alert: #{alert_type} - #{details[:container_name]}"
  end
  
  def create_dashboard(name, &block)
    dashboard = ContainerDashboard.new(name)
    dashboard.instance_eval(&block) if block_given?
    @dashboards[name] = dashboard
    dashboard
  end
  
  def get_container_metrics(container_id, time_range = nil)
    metrics = @metrics[container_id] || []
    
    if time_range
      start_time = Time.now - time_range
      metrics = metrics.select { |m| m[:timestamp] >= start_time }
    end
    
    metrics
  end
  
  def get_container_logs(container_id, limit = 100)
    container = @containers[container_id]
    return [] unless container
    
    @logs
      .select { |log| log[:container_id] == container_id }
      .last(limit)
  end
  
  def get_alerts(severity = nil)
    alerts = @alerts.select { |alert| alert[:resolved] == false }
    
    if severity
      alerts = alerts.select { |alert| alert[:details][:severity] == severity }
    end
    
    alerts
  end
  
  def get_container_status(container_id)
    @containers[container_id]
  end
  
  def generate_report
    puts "Container Monitoring Report:"
    puts "=" * 50
    
    puts "Container Overview:"
    @containers.each do |id, container|
      uptime = container[:started_at] ? Time.now - container[:started_at] : 0
      puts "  #{container[:name]} (#{id[0..8]}...): #{container[:status]}"
      puts "    Uptime: #{uptime.round(2)}s"
      puts "    Restarts: #{container[:restart_count]}"
      puts "    Image: #{container[:image]}"
    end
    
    puts "\nAlert Summary:"
    alert_counts = @alerts.group_by { |alert| alert[:type] }.transform_values(&:count)
    
    alert_counts.each do |type, count|
      puts "#{type}: #{count} alerts"
    end
    
    puts "\nRecent Events:"
    recent_logs = @logs.last(10)
    recent_logs.each do |log|
      puts "  [#{log[:timestamp]}] #{log[:container_name]}: #{log[:event_type]}"
    end
    
    puts "\nMonitoring Features:"
    puts "- Container tracking"
    puts "- Metrics collection"
    puts "- Event logging"
    puts "- Alert management"
    puts "- Dashboard creation"
    puts "- Threshold monitoring"
    puts "- Comprehensive reporting"
  end
  
  def self.demonstrate_monitoring
    puts "Container Monitoring Demonstration:"
    puts "=" * 50
    
    monitor = ContainerMonitor.new
    
    # Track containers
    puts "Tracking containers:"
    
    monitor.track_container('abc123', {
      name: 'web-app',
      image: 'ruby-app:latest',
      status: 'running',
      started_at: Time.now - 3600
    })
    
    monitor.track_container('def456', {
      name: 'database',
      image: 'postgres:14',
      status: 'running',
      started_at: Time.now - 7200
    })
    
    # Collect metrics
    puts "\nCollecting metrics:"
    
    monitor.collect_metrics('abc123', {
      cpu_usage: 75.5,
      memory_usage: 68.2,
      disk_usage: 45.8,
      network_in: 1024,
      network_out: 512
    })
    
    monitor.collect_metrics('def456', {
      cpu_usage: 85.3,
      memory_usage: 72.1,
      disk_usage: 82.5,
      network_in: 256,
      network_out: 128
    })
    
    # Simulate container events
    puts "\nContainer events:"
    
    monitor.update_container_status('abc123', 'running')
    monitor.restart_container('abc123')
    monitor.update_container_status('def456', 'error')
    
    # Create dashboard
    puts "\nCreating monitoring dashboard:"
    dashboard = monitor.create_dashboard('production') do
      title 'Production Container Monitoring'
      
      metric 'cpu_usage' do
        label 'CPU Usage'
        unit '%'
        threshold 80
        color '#ff0000'
      end
      
      metric 'memory_usage' do
        label 'Memory Usage'
        unit '%'
        threshold 80
        color '#ffff00'
      end
      
      metric 'disk_usage' do
        label 'Disk Usage'
        unit '%'
        threshold 85
        color '#ff0000'
      end
    end
    
    # Generate report
    puts "\nGenerating monitoring report:"
    report = monitor.generate_report
    
    puts "\nContainer Monitoring Features:"
    puts "- Real-time container tracking"
    puts "- Metrics collection and analysis"
    puts "- Event logging and alerting"
    puts "- Threshold-based monitoring"
    puts "- Dashboard creation"
    puts "- Comprehensive reporting"
    puts "- Container lifecycle management"
  end
  
  private
  
  def check_thresholds(container_id, metrics)
    container = @containers[container_id]
    return unless container
    
    metrics.each do |metric_name, value|
      threshold = @thresholds[metric_name]
      
      if threshold && value > threshold
        add_alert("#{metric_name}_threshold", {
          container_id: container_id,
          container_name: container[:name],
          metric_name: metric_name,
          value: value,
          threshold: threshold
        })
      end
    end
  end
end

class ContainerDashboard
  def initialize(name)
    @name = name
    @title = name.humanize
    @metrics = {}
    @widgets = []
  end
  
  def title(title)
    @title = title
  end
  
  def metric(name, &block)
    metric = DashboardMetric.new(name)
    metric.instance_eval(&block) if block_given?
    @metrics[name] = metric
    metric
  end
  
  def widget(name, &block)
    widget = DashboardWidget.new(name)
    widget.instance_eval(&block) if block_given?
    @widgets << widget
    widget
  end
  
  def to_h
    {
      name: @name,
      title: @title,
      metrics: @metrics.transform_values(&:to_h),
      widgets: @widgets.map(&:to_h)
    }
  end
end

class DashboardMetric
  def initialize(name)
    @name = name
    @label = name.humanize
    @unit = ''
    @threshold = 0
    @color = '#0000ff'
  end
  
  attr_reader :name, :label, :unit, :threshold, :color
  
  def label(label)
    @label = label
  end
  
  def unit(unit)
    @unit = unit
  end
  
  def threshold(threshold)
    @threshold = threshold
  end
  
  def color(color)
    @color = color
  end
  
  def to_h
    {
      name: @name,
      label: @label,
      unit: @unit,
      threshold: @threshold,
      color: @color
    }
  end
end

class DashboardWidget
  def initialize(name)
    @name = name
    @type = 'metric'
    @title = name.humanize
    @config = {}
  end
  
  def type(type)
    @type = type
  end
  
  def title(title)
    @title = title
  end
  
  def config(&block)
    config_manager = ConfigManager.new(@config)
    config_manager.instance_eval(&block) if block_given?
    @config = config_manager.config
  end
  
  def to_h
    {
      name: @name,
      type: @type,
      title: @title,
      config: @config
    }
  end
end

class ConfigManager
  def initialize(config)
    @config = config
  end
  
  attr_reader :config
  
  def method_missing(name, value)
    @config[name] = value
  end
end
```

## 🎯 Container Security

### 6. Container Security Best Practices

Security considerations for containers:

```ruby
class ContainerSecurity
  def self.security_guidelines
    puts "Container Security Guidelines:"
    puts "=" * 50
    
    guidelines = [
      {
        category: "Image Security",
        practices: [
          "Use official base images",
          "Scan images for vulnerabilities",
          "Use minimal base images",
          "Keep images up to date",
          "Sign and verify images"
        ],
        tools: ["Docker Scout", "Clair", "Trivy", "Anchore"],
        benefits: ["Reduced attack surface", "Vulnerability prevention", "Compliance"]
      },
      {
        category: "Container Runtime Security",
        practices: [
          "Run as non-root user",
          "Use read-only filesystem",
          "Limit container capabilities",
          "Implement resource limits",
          "Use seccomp profiles"
        ],
        tools: ["Docker", "Kubernetes", "Podman", "CRI-O"],
        benefits: ["Privilege escalation prevention", "Resource protection", "Isolation"]
      },
      {
        category: "Network Security",
        practices: [
          "Use network segmentation",
          "Implement firewall rules",
          "Encrypt network traffic",
          "Use secure protocols",
          "Limit network exposure"
        ],
        tools: ["Kubernetes Network Policies", "Calico", "Istio", "Linkerd"],
        benefits: ["Network isolation", "Traffic control", "Data protection"]
      },
      {
        category: "Secrets Management",
        practices: [
          "Use secrets management tools",
          "Encrypt secrets at rest",
          "Rotate secrets regularly",
          "Use least privilege",
          "Audit secret access"
        ],
        tools: ["Kubernetes Secrets", "Vault", "AWS Secrets Manager", "Azure Key Vault"],
        benefits: ["Secret protection", "Access control", "Compliance"]
      },
      {
        category: "Orchestration Security",
        practices: [
          "Implement RBAC",
          "Use network policies",
          "Admission controllers",
          "Audit logging",
          "Regular security updates"
        ],
        tools: ["Kubernetes RBAC", "OPA/Gatekeeper", "Falco", "Open Policy Agent"],
        benefits: ["Access control", "Policy enforcement", "Audit trail"]
      },
      {
        category: "Compliance and Auditing",
        practices: [
          "Regular security audits",
          "Compliance scanning",
          "Vulnerability assessment",
          "Penetration testing",
          "Security monitoring"
        ],
        tools: ["OpenSCAP", "CIS Benchmarks", "Popeye", "kube-bench"],
        benefits: ["Compliance", "Risk assessment", "Security improvement"]
      }
    ]
    
    guidelines.each do |guideline|
      puts "#{guideline[:category]}:"
      puts "  Practices: #{guideline[:practices].join(', ')}"
      puts "  Tools: #{guideline[:tools].join(', ')}"
      puts "  Benefits: #{guideline[:benefits].join(', ')}"
      puts
    end
  end
  
  def self.security_checklist
    puts "\nContainer Security Checklist:"
    puts "=" * 50
    
    checklist = [
      {
        area: "Image Security",
        items: [
          "Use official or trusted base images",
          "Scan images for vulnerabilities",
          "Remove unnecessary packages",
          "Use multi-stage builds",
          "Sign and verify images",
          "Use specific image tags",
          "Regularly update base images"
        ]
      },
      {
        area: "Runtime Security",
        items: [
          "Run containers as non-root",
          "Use read-only filesystems",
          "Limit container capabilities",
          "Set resource limits",
          "Use seccomp/AppArmor profiles",
          "Enable container security policies",
          "Monitor container activity"
        ]
      },
      {
        area: "Network Security",
        items: [
          "Implement network segmentation",
          "Use network policies",
          "Encrypt network traffic",
          "Limit port exposure",
          "Use secure protocols",
          "Implement firewall rules",
          "Monitor network traffic"
        ]
      },
      {
        area: "Secrets Management",
        items: [
          "Use secrets management tools",
          "Encrypt secrets at rest",
          "Rotate secrets regularly",
          "Use least privilege",
          "Audit secret access",
          "Avoid secrets in images",
          "Use environment variables carefully"
        ]
      },
      {
        area: "Orchestration Security",
        items: [
          "Implement RBAC",
          "Use network policies",
          "Admission controllers",
          "Pod security policies",
          "Audit logging",
          "Regular security updates",
          "Security monitoring"
        ]
      }
    ]
    
    checklist.each do |area|
      puts "#{area[:area]}:"
      area[:items].each_with_index do |item, i|
        puts "  #{i + 1}. #{item}"
      end
      puts
    end
  end
  
  def self.common_vulnerabilities
    puts "\nCommon Container Vulnerabilities:"
    puts "=" * 50
    
    vulnerabilities = [
      {
        vulnerability: "Privilege Escalation",
        description: "Gaining higher privileges than intended",
        examples: ["Running as root", "SUID binaries", "Capabilities abuse"],
        mitigation: ["Run as non-root", "Drop capabilities", "Use security contexts"],
        severity: "High"
      },
      {
        vulnerability: "Image Vulnerabilities",
        description: "Known vulnerabilities in container images",
        examples: ["Outdated packages", "Known CVEs", "Malicious images"],
        mitigation: ["Regular scanning", "Base image updates", "Image signing"],
        severity: "High"
      },
      {
        vulnerability: "Network Exposure",
        description: "Unnecessary network exposure",
        examples: ["Open ports", "Insecure protocols", "Network misconfiguration"],
        mitigation: ["Network policies", "Port restrictions", "Secure protocols"],
        severity: "Medium"
      },
      {
        vulnerability: "Secrets Exposure",
        description: "Secrets exposed in images or logs",
        examples: ["Secrets in environment variables", "Secrets in image layers", "Log exposure"],
        mitigation: ["Secrets management", "Image scanning", "Log filtering"],
        severity: "High"
      },
      {
        vulnerability: "Resource Exhaustion",
        description: "Resource exhaustion attacks",
        examples: ["CPU exhaustion", "Memory exhaustion", "Disk exhaustion"],
        mitigation: ["Resource limits", "Monitoring", "Rate limiting"],
        severity: "Medium"
      },
      {
        vulnerability: "Container Escape",
        description: "Breaking out of container isolation",
        examples: ["Kernel vulnerabilities", "Misconfigurations", "Exploits"],
        mitigation: ["Regular updates", "Security policies", "Monitoring"],
        severity: "Critical"
      }
    ]
    
    vulnerabilities.each do |vulnerability|
      puts "#{vulnerability[:vulnerability]}:"
      puts "  Description: #{vulnerability[:description]}"
      puts "  Examples: #{vulnerability[:examples].join(', ')}"
      puts "  Mitigation: #{vulnerability[:mitigation].join(', ')}"
      puts "  Severity: #{vulnerability[:severity]}"
      puts
    end
  end
  
  def self.security_tools
    puts "\nContainer Security Tools:"
    puts "=" * 50
    
    tools = [
      {
        category: "Image Scanning",
        tools: [
          {
            name: "Docker Scout",
            description: "Official Docker security scanning",
            features: ["Vulnerability scanning", "Best practices", "Compliance"],
            integration: ["Docker Hub", "Docker Desktop", "CI/CD"]
          },
          {
            name: "Trivy",
            description: "Open-source vulnerability scanner",
            features: ["Multiple image formats", "OS packages", "Language packages"],
            integration: ["CI/CD", "Kubernetes", "CLI"]
          },
          {
            name: "Clair",
            description: "Open-source vulnerability analysis",
            features: ["CVE database", "Vulnerability assessment", "Reporting"],
            integration: ["Kubernetes", "CI/CD", "API"]
          }
        ]
      },
      {
        category: "Runtime Security",
        tools: [
          {
            name: "Falco",
            description: "Behavioral monitoring and alerting",
            features: ["Runtime monitoring", "Rule engine", "Alerting"],
            integration: ["Kubernetes", "Docker", "Sysdig"]
          },
          {
            name: "Sysdig",
            description: "Container security and monitoring",
            features: ["Runtime security", "Network monitoring", "Forensics"],
            integration: ["Kubernetes", "Docker", "Cloud platforms"]
          },
          {
            name: "Aqua Security",
            description: "Container security platform",
            features: ["Image scanning", "Runtime protection", "Compliance"],
            integration: ["Kubernetes", "CI/CD", "Cloud platforms"]
          }
        ]
      },
      {
        category: "Policy Enforcement",
        tools: [
          {
            name: "OPA/Gatekeeper",
            description: "Policy as code for Kubernetes",
            features: ["Policy enforcement", "Admission control", "Validation"],
            integration: ["Kubernetes", "CI/CD", "GitOps"]
          },
          {
            name: "Popeye",
            description: "Kubernetes cluster sanitizer",
            features: ["Best practices", "Resource validation", "Security checks"],
            integration: ["Kubernetes", "CLI", "CI/CD"]
          },
          {
            name: "kube-bench",
            description: "CIS Kubernetes benchmark tool",
            features: ["Compliance checking", "Security assessment", "Reporting"],
            integration: ["Kubernetes", "CLI", "Security tools"]
          }
        ]
      }
    ]
    
    tools.each do |category|
      puts "#{category[:category]}:"
      category[:tools].each do |tool|
        puts "  #{tool[:name]}:"
        puts "    Description: #{tool[:description]}"
        puts "    Features: #{tool[:features].join(', ')}"
        puts "    Integration: #{tool[:integration].join(', ')}"
      end
      puts
    end
  end
  
  def self.demonstrate_security
    security_guidelines
    security_checklist
    common_vulnerabilities
    security_tools
    
    puts "\nContainer Security Best Practices Summary:"
    puts "- Use minimal and secure base images"
    puts "- Run containers as non-root users"
    puts "- Implement network segmentation"
    puts "- Use secrets management tools"
    puts "- Regular security scanning and updates"
    puts "- Implement RBAC and policies"
    puts "- Monitor and audit container activity"
    puts "- Follow security best practices"
    puts "- Regular security assessments"
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic Container**: Create simple container
2. **Dockerfile**: Build custom Docker image
3. **Docker Compose**: Multi-container application
4. **Container Monitoring**: Basic monitoring system

### Intermediate Exercises

1. **Multi-Stage Builds**: Optimized Docker images
2. **Kubernetes Deployment**: Deploy to Kubernetes
3. **Container Security**: Security best practices
4. **Container Orchestration**: Advanced orchestration

### Advanced Exercises

1. **Enterprise Containers**: Production-ready containers
2. **Microservices**: Containerized microservices
3. **Container Security**: Comprehensive security
4. **Container Platform**: Complete container platform

---

## 🎯 Summary

Containerization in Ruby provides:

- **Container Fundamentals** - Core concepts and principles
- **Docker Implementation** - Container management and operations
- **Docker Compose** - Multi-container orchestration
- **Kubernetes Deployment** - Cloud-native deployment
- **Container Monitoring** - Observability and logging
- **Container Security** - Security best practices and tools

Master these containerization techniques for modern Ruby applications!
