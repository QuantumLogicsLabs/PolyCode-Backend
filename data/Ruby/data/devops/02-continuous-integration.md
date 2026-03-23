# Continuous Integration in Ruby
# Comprehensive guide to CI/CD pipelines and automation

## 🔄 CI/CD Fundamentals

### 1. Continuous Integration Concepts

Core CI/CD principles:

```ruby
class ContinuousIntegrationFundamentals
  def self.explain_ci_cd_concepts
    puts "Continuous Integration/Continuous Deployment Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Continuous Integration (CI)",
        description: "Automated merging and testing of code changes",
        principles: ["Frequent integration", "Automated testing", "Code quality checks"],
        benefits: ["Early bug detection", "Improved code quality", "Faster feedback"],
        tools: ["Jenkins", "GitHub Actions", "GitLab CI", "CircleCI"]
      },
      {
        concept: "Continuous Delivery (CD)",
        description: "Automated deployment to staging environments",
        principles: ["Automated deployment", "Environment parity", "Release readiness"],
        benefits: ["Faster releases", "Reduced risk", "Consistent deployments"],
        tools: ["Spinnaker", "Argo CD", "Octopus Deploy"]
      },
      {
        concept: "Continuous Deployment",
        description: "Automated deployment to production",
        principles: ["Full automation", "Zero-downtime", "Rollback capability"],
        benefits: ["Instant deployment", "Reduced manual work", "Faster feedback"],
        challenges: ["Risk management", "Rollback complexity", "Monitoring"]
      },
      {
        concept: "Pipeline Automation",
        description: "Automated software delivery pipelines",
        stages: ["Build", "Test", "Deploy", "Monitor"],
        components: ["Triggers", "Stages", "Artifacts", "Notifications"],
        benefits: ["Consistency", "Reproducibility", "Scalability"]
      },
      {
        concept: "Infrastructure as Code",
        description: "Managing infrastructure through code",
        principles: ["Version control", "Automation", "Idempotency"],
        tools: ["Terraform", "Ansible", "Puppet", "Chef"],
        benefits: ["Consistency", "Reproducibility", "Scalability"]
      },
      {
        concept: "DevOps Culture",
        description: "Collaborative development and operations",
        principles: ["Collaboration", "Automation", "Measurement", "Sharing"],
        practices: ["Cross-functional teams", "Shared responsibility", "Continuous improvement"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Principles: #{concept[:principles].join(', ')}" if concept[:principles]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Tools: #{concept[:tools].join(', ')}" if concept[:tools]
      puts "  Challenges: #{concept[:challenges].join(', ')}" if concept[:challenges]
      puts "  Stages: #{concept[:stages].join(', ')}" if concept[:stages]
      puts "  Components: #{concept[:components].join(', ')}" if concept[:components]
      puts "  Practices: #{concept[:practices].join(', ')}" if concept[:practices]
      puts
    end
  end
  
  def self.pipeline_stages
    puts "\nCI/CD Pipeline Stages:"
    puts "=" * 50
    
    stages = [
      {
        stage: "1. Source Control",
        description: "Version control and code management",
        activities: ["Code commit", "Branch management", "Pull requests", "Code review"],
        tools: ["Git", "GitHub", "GitLab", "Bitbucket"],
        triggers: ["Push", "Pull request", "Schedule"]
      },
      {
        stage: "2. Build",
        description: "Compile and package application",
        activities: ["Dependency resolution", "Compilation", "Packaging", "Artifact creation"],
        tools: ["Maven", "Gradle", "npm", "Docker"],
        outputs: ["Binary artifacts", "Docker images", "Deployment packages"]
      },
      {
        stage: "3. Test",
        description: "Automated testing and quality checks",
        activities: ["Unit tests", "Integration tests", "Security scans", "Performance tests"],
        tools: ["RSpec", "Jest", "Selenium", "SonarQube"],
        metrics: ["Code coverage", "Test results", "Quality gates"]
      },
      {
        stage: "4. Deploy",
        description: "Deploy to environments",
        activities: ["Environment setup", "Application deployment", "Configuration", "Health checks"],
        tools: ["Kubernetes", "Docker", "Ansible", "Terraform"],
        environments: ["Development", "Staging", "Production"]
      },
      {
        stage: "5. Monitor",
        description: "Monitor deployment and application health",
        activities: ["Health checks", "Metrics collection", "Alerting", "Logging"],
        tools: ["Prometheus", "Grafana", "ELK Stack", "Datadog"],
        metrics: ["Performance", "Availability", "Error rates", "User experience"]
      }
    ]
    
    stages.each do |stage|
      puts "#{stage[:stage]}: #{stage[:description]}"
      puts "  Activities: #{stage[:activities].join(', ')}"
      puts "  Tools: #{stage[:tools].join(', ')}" if stage[:tools]
      puts "  Triggers: #{stage[:triggers].join(', ')}" if stage[:triggers]
      puts "  Outputs: #{stage[:outputs].join(', ')}" if stage[:outputs]
      puts "  Metrics: #{stage[:metrics].join(', ')}" if stage[:metrics]
      puts "  Environments: #{stage[:environments].join(', ')}" if stage[:environments]
      puts
    end
  end
  
  def self.ci_cd_best_practices
    puts "\nCI/CD Best Practices:"
    puts "=" * 50
    
    practices = [
      {
        practice: "Automate Everything",
        description: "Automate as much as possible in the pipeline",
        guidelines: [
          "Automated testing",
          "Automated deployments",
          "Automated quality checks",
          "Automated monitoring"
        ],
        benefits: ["Consistency", "Speed", "Reliability"]
      },
      {
        practice: "Fail Fast",
        description: "Detect and fix issues early in the pipeline",
        guidelines: [
          "Run tests first",
          "Fail fast on errors",
          "Provide clear feedback",
          "Block problematic changes"
        ],
        benefits: ["Faster feedback", "Reduced costs", "Better quality"]
      },
      {
        practice: "Keep Pipelines Simple",
        description: "Maintain simple and understandable pipelines",
        guidelines: [
          "Clear pipeline stages",
          "Minimal dependencies",
          "Fast execution",
          "Easy debugging"
        ],
        benefits: ["Maintainability", "Reliability", "Performance"]
      },
      {
        practice: "Use Environments Effectively",
        description: "Use appropriate environments for different purposes",
        guidelines: [
          "Development environment",
          "Testing environment",
          "Staging environment",
          "Production environment"
        ],
        benefits: ["Risk reduction", "Quality assurance", "User confidence"]
      },
      {
        practice: "Monitor Everything",
        description: "Monitor pipeline and application performance",
        guidelines: [
          "Pipeline metrics",
          "Application metrics",
          "Error tracking",
          "Performance monitoring"
        ],
        benefits: ["Visibility", "Proactive management", "Continuous improvement"]
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
  
  # Run CI/CD fundamentals
  explain_ci_cd_concepts
  pipeline_stages
  ci_cd_best_practices
end
```

### 2. CI Pipeline Implementation

Build automation pipeline:

```ruby
class CIPipeline
  def initialize(config = {})
    @config = config
    @stages = []
    @artifacts = {}
    @environment = {}
    @notifications = []
    @status = :idle
    @start_time = nil
    @end_time = nil
  end
  
  def add_stage(name, &block)
    stage = Stage.new(name, @config)
    stage.instance_eval(&block) if block_given?
    @stages << stage
    stage
  end
  
  def execute
    @status = :running
    @start_time = Time.now
    
    puts "Starting CI Pipeline"
    puts "Pipeline ID: #{SecureRandom.hex(8)}"
    puts "Started at: #{@start_time}"
    
    @stages.each_with_index do |stage, index|
      puts "\n=== Stage #{index + 1}: #{stage.name} ==="
      
      begin
        stage_result = stage.execute(@environment, @artifacts)
        
        if stage_result[:success]
          puts "✅ Stage #{stage.name} completed successfully"
          puts "   Duration: #{stage_result[:duration].round(2)}s"
          
          # Merge artifacts
          @artifacts.merge!(stage_result[:artifacts] || {})
          
          # Merge environment
          @environment.merge!(stage_result[:environment] || {})
          
        else
          puts "❌ Stage #{stage.name} failed"
          puts "   Error: #{stage_result[:error]}"
          
          @status = :failed
          notify_failure(stage, stage_result[:error])
          break
        end
        
      rescue => e
        puts "❌ Stage #{stage.name} crashed: #{e.message}"
        @status = :failed
        notify_failure(stage, e.message)
        break
      end
    end
    
    @end_time = Time.now
    @status = @status == :failed ? :failed : :success
    
    puts "\n=== Pipeline #{@status} ==="
    puts "Duration: #{(@end_time - @start_time).round(2)}s"
    puts "Completed at: #{@end_time}"
    
    notify_completion if @status == :success
    
    {
      success: @status == :success,
      duration: @end_time - @start_time,
      stages: @stages.map(&:summary),
      artifacts: @artifacts
    }
  end
  
  def add_notification(type, config = {})
    @notifications << {
      type: type,
      config: config
    }
  end
  
  def status
    @status
  end
  
  def summary
    {
      status: @status,
      stages: @stages.map(&:summary),
      artifacts: @artifacts.keys,
      duration: @end_time ? @end_time - @start_time : nil
    }
  end
  
  def self.demonstrate_pipeline
    puts "CI Pipeline Demonstration:"
    puts "=" * 50
    
    # Create CI pipeline
    pipeline = CIPipeline.new(
      project_name: 'Ruby App',
      branch: 'main',
      build_number: 123
    )
    
    # Add notifications
    pipeline.add_notification(:slack, webhook: 'https://hooks.slack.com/...')
    pipeline.add_notification(:email, recipients: ['team@example.com'])
    
    # Build pipeline stages
    pipeline.add_stage('Setup') do
      command 'echo "Setting up environment"'
      
      script do
        puts "  Creating build environment"
        @environment[:build_id] = SecureRandom.hex(8)
        @environment[:timestamp] = Time.now
      end
      
      command 'echo "Environment ready"'
    end
    
    pipeline.add_stage('Dependencies') do
      command 'bundle install'
      command 'npm install'
      
      script do
        puts "  Installing dependencies"
        sleep(2) # Simulate installation time
        @artifacts[:dependencies_installed] = true
      end
    end
    
    pipeline.add_stage('Test') do
      command 'rspec spec/'
      command 'npm test'
      
      script do
        puts "  Running tests"
        sleep(3) # Simulate test execution
        
        # Simulate test results
        @artifacts[:test_results] = {
          total: 150,
          passed: 148,
          failed: 2,
          coverage: 85.5
        }
      end
      
      # Fail if tests failed
      fail_if 'Tests failed' do
        @artifacts[:test_results][:failed] > 0
      end
    end
    
    pipeline.add_stage('Build') do
      command 'docker build -t ruby-app .'
      
      script do
        puts "  Building Docker image"
        sleep(4) # Simulate build time
        
        @artifacts[:docker_image] = {
          name: 'ruby-app',
          tag: "v#{SecureRandom.hex(4)}",
          size: '125MB'
        }
      end
    end
    
    pipeline.add_stage('Deploy to Staging') do
      command 'kubectl apply -f k8s/staging.yaml'
      
      script do
        puts "  Deploying to staging environment"
        sleep(2) # Simulate deployment
        
        @artifacts[:deployment] = {
          environment: 'staging',
          url: 'https://staging.ruby-app.com',
          version: @artifacts[:docker_image][:tag]
        }
      end
    end
    
    # Execute pipeline
    result = pipeline.execute
    
    puts "\nPipeline Summary:"
    pipeline.summary.each do |key, value|
      case key
      when :stages
        puts "#{key}:"
        value.each { |stage| puts "  #{stage[:name]}: #{stage[:status]}" }
      when :artifacts
        puts "#{key}: #{value.join(', ')}"
      else
        puts "#{key}: #{value}"
      end
    end
    
    puts "\nCI Pipeline Features:"
    puts "- Stage-based pipeline execution"
    puts "- Command and script execution"
    puts "- Artifact management"
    puts "- Environment variable handling"
    puts "- Conditional execution"
    puts "- Error handling and notifications"
    puts "- Pipeline status tracking"
  end
  
  private
  
  def notify_failure(stage, error)
    @notifications.each do |notification|
      case notification[:type]
      when :slack
        puts "📢 Slack notification: Pipeline failed at #{stage.name}"
        puts "   Error: #{error}"
      when :email
        puts "📧 Email notification sent to #{notification[:config][:recipients].join(', ')}"
      end
    end
  end
  
  def notify_completion
    @notifications.each do |notification|
      case notification[:type]
      when :slack
        puts "📢 Slack notification: Pipeline completed successfully"
      when :email
        puts "📧 Email notification sent to #{notification[:config][:recipients].join(', ')}"
      end
    end
  end
end

class Stage
  def initialize(name, config = {})
    @name = name
    @config = config
    @commands = []
    @scripts = []
    @conditions = []
    @status = :pending
    @start_time = nil
    @end_time = nil
    @artifacts = {}
    @environment = {}
  end
  
  attr_reader :name, :status
  
  def command(cmd)
    @commands << cmd
  end
  
  def script(&block)
    @scripts << block
  end
  
  def fail_if(message, &condition)
    @conditions << { message: message, condition: condition }
  end
  
  def execute(environment, artifacts)
    @start_time = Time.now
    @status = :running
    
    # Merge environment and artifacts
    @environment.merge!(environment)
    @artifacts.merge!(artifacts)
    
    begin
      # Execute commands
      @commands.each do |cmd|
        puts "  $ #{cmd}"
        result = execute_command(cmd)
        
        unless result[:success]
          return {
            success: false,
            error: "Command failed: #{cmd}",
            duration: Time.now - @start_time
          }
        end
      end
      
      # Execute scripts
      @scripts.each do |script|
        instance_eval(&script)
      end
      
      # Check conditions
      @conditions.each do |condition|
        if instance_eval(&condition[:condition])
          return {
            success: false,
            error: condition[:message],
            duration: Time.now - @start_time
          }
        end
      end
      
      @status = :success
      @end_time = Time.now
      
      {
        success: true,
        duration: @end_time - @start_time,
        artifacts: @artifacts,
        environment: @environment
      }
      
    rescue => e
      @status = :failed
      @end_time = Time.now
      
      {
        success: false,
        error: e.message,
        duration: @end_time - @start_time
      }
    end
  end
  
  def summary
    {
      name: @name,
      status: @status,
      duration: @end_time ? @end_time - @start_time : nil,
      commands: @commands.length,
      scripts: @scripts.length
    }
  end
  
  private
  
  def execute_command(cmd)
    # Simulate command execution
    puts "    Executing: #{cmd}"
    
    # Simulate different command types
    case cmd
    when /bundle install/
      { success: true, output: 'Bundle installed successfully' }
    when /npm install/
      { success: true, output: 'npm packages installed' }
    when /rspec/
      test_results = @artifacts[:test_results] || { failed: 0 }
      { success: test_results[:failed] == 0, output: 'Tests passed' }
    when /docker build/
      { success: true, output: 'Docker image built' }
    when /kubectl/
      { success: true, output: 'Kubernetes deployment successful' }
    else
      { success: true, output: 'Command executed' }
    end
  end
end
```

## 🚀 GitHub Actions

### 3. GitHub Actions Workflows

GitHub Actions automation:

```ruby
class GitHubActionsWorkflow
  def initialize(name, config = {})
    @name = name
    @config = config
    @jobs = []
    @triggers = []
    @secrets = {}
    @environment = {}
  end
  
  def on(events, options = {})
    @triggers << {
      events: Array(events),
      branches: options[:branches],
      tags: options[:tags],
      paths: options[:paths]
    }
  end
  
  def job(name, &block)
    job = Job.new(name, @config)
    job.instance_eval(&block) if block_given?
    @jobs << job
    job
  end
  
  def env(name, value)
    @environment[name] = value
  end
  
  def secret(name, value)
    @secrets[name] = value
  end
  
  def generate_workflow
    workflow = {
      name: @name,
      on: generate_triggers,
      env: @environment,
      secrets: @secrets,
      jobs: {}
    }
    
    @jobs.each do |job|
      workflow[:jobs][job.name] = job.to_h
    end
    
    workflow
  end
  
  def self.demonstrate_github_actions
    puts "GitHub Actions Workflow Demonstration:"
    puts "=" * 50
    
    # Create CI/CD workflow
    workflow = GitHubActionsWorkflow.new('Ruby CI/CD Pipeline')
    
    # Define triggers
    workflow.on(:push, branches: ['main', 'develop'])
    workflow.on(:pull_request, branches: ['main'])
    
    # Set environment variables
    workflow.env('RAILS_ENV', 'test')
    workflow.env('NODE_ENV', 'test')
    
    # Add secrets
    workflow.secret('DATABASE_URL', 'postgresql://localhost/test')
    workflow.secret('AWS_ACCESS_KEY_ID', 'AKIAIOSFODNN7EXAMPLE')
    workflow.secret('AWS_SECRET_ACCESS_KEY', 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY')
    
    # Build job
    workflow.job('build') do
      runs_on('ubuntu-latest')
      
      steps do
        uses('actions/checkout@v3')
        uses('actions/setup-ruby@v1', ruby_version: '3.0')
        uses('actions/setup-node@v3', node_version: '16')
        
        run('echo "Installing dependencies"')
        run('bundle install --jobs=4 --retry=3')
        run('npm ci')
        
        run('echo "Running tests"')
        run('bundle exec rspec')
        run('npm test')
        
        run('echo "Building application"')
        run('npm run build')
        run('docker build -t ruby-app .')
      end
    end
    
    # Test job
    workflow.job('test') do
      runs_on('ubuntu-latest')
      
      needs('build')
      
      strategy do
        matrix do
          ruby_version(['3.0', '3.1', '3.2'])
          rails_version(['6.1', '7.0'])
        end
      end
      
      services do
        postgres('postgres:14', {
          env: {
            POSTGRES_PASSWORD: 'postgres',
            POSTGRES_DB: 'test'
          },
          options: >-
            --health-cmd pg_isready
            --health-interval 10s
            --health-timeout 5s
            --health-retries 5
          ports: [5432]
        })
      end
      
      steps do
        uses('actions/checkout@v3')
        uses('actions/setup-ruby@v1', ruby_version: '${{ matrix.ruby_version }}')
        
        run('echo "Running tests for Ruby ${{ matrix.ruby_version }}"')
        run('bundle exec rspec')
      end
    end
    
    # Deploy job
    workflow.job('deploy') do
      runs_on('ubuntu-latest')
      
      needs('test')
      
      if_github_ref('refs/heads/main')
      
      steps do
        uses('actions/checkout@v3')
        
        run('echo "Deploying to production"')
        run('echo ${{ secrets.DATABASE_URL }}')
        
        uses('aws-actions/configure-aws-credentials@v1', {
          aws_access_key_id: '${{ secrets.AWS_ACCESS_KEY_ID }}',
          aws_secret_access_key: '${{ secrets.AWS_SECRET_ACCESS_KEY }}',
          aws_region: 'us-west-2'
        })
        
        run('aws s3 sync ./public s3://ruby-app-bucket')
        run('aws cloudfront create-invalidation --distribution-id ${{ secrets.CLOUDFRONT_ID }} --paths "/*"')
      end
    end
    
    # Generate workflow
    workflow_yaml = workflow.generate_workflow
    
    puts "Generated GitHub Actions Workflow:"
    puts workflow_yaml.to_yaml
    
    puts "\nGitHub Actions Features:"
    puts "- Event-based triggers"
    puts "- Matrix builds"
    "- Service containers"
    "- Secrets management"
    "- Environment variables"
    "- Conditional deployment"
    "- Multi-job workflows"
    "- Reusable actions"
  end
  
  def self.demonstrate_advanced_workflows
    puts "Advanced GitHub Actions Workflows:"
    puts "=" * 50
    
    # Security scanning workflow
    security_workflow = GitHubActionsWorkflow.new('Security Scanning')
    
    security_workflow.on(:push, branches: ['main'])
    security_workflow.on(:schedule, cron: '0 2 * * 1') # Weekly
    
    security_workflow.job('security-scan') do
      runs_on('ubuntu-latest')
      
      steps do
        uses('actions/checkout@v3')
        
        # CodeQL analysis
        uses('github/codeql-action/init@v2', languages: 'ruby')
        uses('github/codeql-action/analyze@v2')
        
        # Security scanning
        uses('securecodewarrior/github-action-add-sarif@v1')
        run('bundle audit --format json')
        
        # Dependency scanning
        uses('github/dependency-review-action@v3')
        
        # Container scanning
        run('docker build -t ruby-app .')
        run('docker run --rm -v /var/run/docker.sock:/var/run/docker.sock aquasec/trivy:latest image ruby-app')
      end
    end
    
    # Performance testing workflow
    perf_workflow = GitHubActionsWorkflow.new('Performance Testing')
    
    perf_workflow.on(:push, branches: ['main'])
    
    perf_workflow.job('performance-test') do
      runs_on('ubuntu-latest')
      
      steps do
        uses('actions/checkout@v3')
        uses('actions/setup-ruby@v1', ruby_version: '3.0')
        
        run('bundle install')
        run('bundle exec rake db:create db:migrate')
        
        # Start application
        run('bundle exec rails server -p 3000 &')
        run('sleep 10')
        
        # Performance tests
        run('bundle exec rspec spec/performance/')
        
        # Load testing
        uses('k6io/action-k6@v0.3.0', {
          script: 'loadtests/api_test.js'
        })
        
        # Benchmark comparison
        uses('benchmark-action/github-action-benchmark@v1', {
          tool: 'bench',
          output_file_path: 'benchmark_results.txt',
          github_token: '${{ secrets.GITHUB_TOKEN }}'
        })
      end
    end
    
    puts "Advanced Workflows Generated:"
    puts "- Security scanning workflow"
    puts "- Performance testing workflow"
    
    puts "\nAdvanced Features:"
    puts "- Scheduled workflows"
    puts "- Security scanning"
    puts "- Performance testing"
    puts "- Container security"
    puts "- Benchmark comparison"
    puts "- Multi-tool integration"
  end
  
  private
  
  def generate_triggers
    triggers = {}
    
    @triggers.each do |trigger|
      trigger[:events].each do |event|
        triggers[event] ||= {}
        
        if trigger[:branches]
          triggers[event][:branches] ||= []
          triggers[event][:branches].concat(trigger[:branches])
        end
        
        if trigger[:tags]
          triggers[event][:tags] ||= []
          triggers[event][:tags].concat(trigger[:tags])
        end
        
        if trigger[:paths]
          triggers[event][:paths] ||= []
          triggers[event][:paths].concat(trigger[:paths])
        end
      end
    end
    
    triggers
  end
end

class Job
  def initialize(name, config = {})
    @name = name
    @config = config
    @runs_on = 'ubuntu-latest'
    @needs = []
    @if_condition = nil
    @strategy = nil
    @services = {}
    @steps = []
    @outputs = {}
  end
  
  attr_reader :name
  
  def runs_on(os)
    @runs_on = os
  end
  
  def needs(*jobs)
    @needs.concat(jobs)
  end
  
  def if_github_ref(ref)
    @if_condition = "github.ref == '#{ref}'"
  end
  
  def strategy(&block)
    @strategy = Strategy.new
    @strategy.instance_eval(&block) if block_given?
  end
  
  def services(&block)
    services_manager = ServicesManager.new(@services)
    services_manager.instance_eval(&block) if block_given?
  end
  
  def steps(&block)
    steps_manager = StepsManager.new(@steps)
    steps_manager.instance_eval(&block) if block_given?
  end
  
  def outputs(&block)
    outputs_manager = OutputsManager.new(@outputs)
    outputs_manager.instance_eval(&block) if block_given?
  end
  
  def to_h
    job_hash = {
      'runs-on' => @runs_on,
      'needs' => @needs,
      'steps' => @steps.map(&:to_h)
    }
    
    job_hash['if'] = @if_condition if @if_condition
    job_hash['strategy'] = @strategy.to_h if @strategy
    job_hash['services'] = @services if @services.any?
    job_hash['outputs'] = @outputs if @outputs.any?
    
    job_hash
  end
end

class Strategy
  def initialize
    @matrix = {}
  end
  
  def matrix(&block)
    matrix_manager = MatrixManager.new(@matrix)
    matrix_manager.instance_eval(&block) if block_given?
  end
  
  def to_h
    { 'matrix' => @matrix }
  end
end

class MatrixManager
  def initialize(matrix)
    @matrix = matrix
  end
  
  def ruby_version(versions)
    @matrix['ruby-version'] = versions
  end
  
  def rails_version(versions)
    @matrix['rails-version'] = versions
  end
  
  def node_version(versions)
    @matrix['node-version'] = versions
  end
end

class ServicesManager
  def initialize(services)
    @services = services
  end
  
  def postgres(image, options = {})
    @services['postgres'] = {
      'image' => image,
      'env' => options[:env] || {},
      'options' => options[:options] || '',
      'ports' => options[:ports] || []
    }
  end
  
  def redis(image, options = {})
    @services['redis'] = {
      'image' => image,
      'env' => options[:env] || {},
      'options' => options[:options] || '',
      'ports' => options[:ports] || []
    }
  end
end

class StepsManager
  def initialize(steps)
    @steps = steps
  end
  
  def uses(action, options = {})
    @steps << {
      'uses' => action,
      'with' => options
    }
  end
  
  def run(command)
    @steps << {
      'run' => command
    }
  end
  
  def env(name, value)
    @steps << {
      'env' => { name => value }
    }
  end
end

class OutputsManager
  def initialize(outputs)
    @outputs = outputs
  end
  
  def step_output(step_name, output_name)
    @outputs["#{step_name}-#{output_name}"] = {
      'value' => "${{steps.#{step_name}.outputs.#{output_name}}"
    }
  end
end

# Add YAML generation capability
class Hash
  def to_yaml
    require 'yaml'
    YAML.dump(self)
  end
end
```

## 🐳 Docker Integration

### 4. Docker CI/CD

Container-based CI/CD pipelines:

```ruby
class DockerCI
  def initialize(config = {})
    @config = config
    @dockerfile_path = config[:dockerfile_path] || 'Dockerfile'
    @image_name = config[:image_name] || 'ruby-app'
    @registry = config[:registry] || 'docker.io'
    @build_args = config[:build_args] || {}
    @environment = config[:environment] || {}
  end
  
  def build_docker_image
    puts "Building Docker image: #{@image_name}"
    
    build_start = Time.now
    
    # Check Dockerfile exists
    unless File.exist?(@dockerfile_path)
      raise "Dockerfile not found: #{@dockerfile_path}"
    end
    
    # Build Docker image
    build_command = build_docker_command
    puts "  Command: #{build_command}"
    
    # Simulate build process
    puts "  Step 1/8: FROM ruby:3.0-slim"
    sleep(1)
    
    puts "  Step 2/8: WORKDIR /app"
    sleep(0.5)
    
    puts "  Step 3/8: COPY Gemfile Gemfile.lock ./"
    sleep(1)
    
    puts "  Step 4/8: RUN bundle install"
    sleep(3)
    
    puts "  Step 5/8: COPY . ."
    sleep(1)
    
    puts "  Step 6/8: EXPOSE 3000"
    sleep(0.5)
    
    puts "  Step 7/8: CMD [\"rails\", \"server\", \"-b\", \"0.0.0.0\"]"
    sleep(0.5)
    
    puts "  Step 8/8: LABEL maintainer=\"devops@company.com\""
    sleep(0.5)
    
    build_duration = Time.now - build_start
    image_id = SecureRandom.hex(12)
    
    puts "✅ Docker image built successfully"
    puts "  Image ID: #{image_id}"
    puts "  Duration: #{build_duration.round(2)}s"
    
    {
      success: true,
      image_id: image_id,
      duration: build_duration,
      image_name: @image_name
    }
  end
  
  def test_docker_image
    puts "Testing Docker image: #{@image_name}"
    
    test_start = Time.now
    
    # Run tests in container
    test_command = "docker run --rm #{@image_name} bundle exec rspec"
    puts "  Command: #{test_command}"
    
    # Simulate test execution
    puts "  Running tests in container..."
    sleep(5)
    
    # Simulate test results
    test_results = {
      total: 150,
      passed: 148,
      failed: 2,
      pending: 0,
      coverage: 85.5
    }
    
    test_duration = Time.now - test_start
    
    puts "✅ Tests completed"
    puts "  Total: #{test_results[:total]}"
    puts "  Passed: #{test_results[:passed]}"
    puts "  Failed: #{test_results[:failed]}"
    puts "  Coverage: #{test_results[:coverage]}%"
    puts "  Duration: #{test_duration.round(2)}s"
    
    {
      success: test_results[:failed] == 0,
      test_results: test_results,
      duration: test_duration
    }
  end
  
  def push_docker_image(tag = nil)
    image_tag = tag || "latest"
    full_image_name = "#{@registry}/#{@image_name}:#{image_tag}"
    
    puts "Pushing Docker image: #{full_image_name}"
    
    push_start = Time.now
    
    # Push to registry
    push_command = "docker push #{full_image_name}"
    puts "  Command: #{push_command}"
    
    # Simulate push process
    puts "  The push refers to repository [#{@registry}/#{@image_name}]"
    puts "  Preparing..."
    sleep(2)
    
    puts "  Pushing..."
    layers = ['f1e2d3c4', 'a5b6c7d8', 'e9f0a1b2', 'c3d4e5f6']
    layers.each_with_index do |layer, i|
      puts "  layer #{i + 1}/#{layers.length}: #{layer}: Pushed"
      sleep(1)
    end
    
    push_duration = Time.now - push_start
    
    puts "✅ Image pushed successfully"
    puts "  Image: #{full_image_name}"
    puts "  Duration: #{push_duration.round(2)}s"
    
    {
      success: true,
      image_name: full_image_name,
      duration: push_duration
    }
  end
  
  def deploy_docker_image(environment)
    puts "Deploying Docker image to #{environment}"
    
    deploy_start = Time.now
    
    case environment
    when 'staging'
      deploy_to_staging
    when 'production'
      deploy_to_production
    else
      raise "Unknown environment: #{environment}"
    end
    
    deploy_duration = Time.now - deploy_start
    
    puts "✅ Deployment completed"
    puts "  Environment: #{environment}"
    puts "  Duration: #{deploy_duration.round(2)}s"
    
    {
      success: true,
      environment: environment,
      duration: deploy_duration
    }
  end
  
  def cleanup_docker_images
    puts "Cleaning up Docker images"
    
    # Remove dangling images
    cleanup_command = "docker image prune -f"
    puts "  Command: #{cleanup_command}"
    
    # Simulate cleanup
    puts "  Deleted: 3 dangling images"
    puts "  Reclaimed: 125MB"
    
    # Remove old images
    old_images = [
      "#{@image_name}:old",
      "#{@image_name}:v1.0",
      "#{@image_name}:v1.1"
    ]
    
    old_images.each do |image|
      puts "  Deleted: #{image}"
    end
    
    puts "✅ Cleanup completed"
  end
  
  def self.demonstrate_docker_ci
    puts "Docker CI/CD Demonstration:"
    puts "=" * 50
    
    docker_ci = DockerCI.new(
      image_name: 'ruby-app',
      registry: 'docker.io/mycompany',
      dockerfile_path: 'Dockerfile'
    )
    
    # Build Docker image
    puts "Building Docker image:"
    build_result = docker_ci.build_docker_image
    
    # Test Docker image
    puts "\nTesting Docker image:"
    test_result = docker_ci.test_docker_image
    
    if test_result[:success]
      # Push image
      puts "\nPushing Docker image:"
      push_result = docker_ci.push_docker_image('v1.2.0')
      
      # Deploy to staging
      puts "\nDeploying to staging:"
      staging_result = docker_ci.deploy_docker_image('staging')
      
      # Deploy to production (if all tests pass)
      if staging_result[:success]
        puts "\nDeploying to production:"
        production_result = docker_ci.deploy_docker_image('production')
      end
    else
      puts "\n❌ Tests failed, skipping deployment"
    end
    
    # Cleanup
    puts "\nCleaning up:"
    docker_ci.cleanup_docker_images
    
    puts "\nDocker CI/CD Features:"
    puts "- Docker image building"
    puts "- Container-based testing"
    puts "- Registry pushing"
    puts "- Environment deployment"
    puts "- Image cleanup"
    puts "- Multi-stage builds"
    puts "- Layer optimization"
  end
  
  private
  
  def build_docker_command
    command = "docker build"
    
    command += " --build-arg RAILS_ENV=#{@environment['RAILS_ENV']}" if @environment['RAILS_ENV']
    command += " --build-arg NODE_ENV=#{@environment['NODE_ENV']}" if @environment['NODE_ENV']
    
    @build_args.each do |key, value|
      command += " --build-arg #{key}=#{value}"
    end
    
    command += " -t #{@image_name} #{@dockerfile_path}"
    command
  end
  
  def deploy_to_staging
    puts "  Deploying to staging environment..."
    puts "  Creating staging namespace"
    puts "  Applying Kubernetes manifests"
    puts "  Waiting for rollout to complete"
    puts "  Verifying deployment health"
    sleep(3)
    
    puts "  Staging URL: https://staging.ruby-app.com"
  end
  
  def deploy_to_production
    puts "  Deploying to production environment..."
    puts "  Creating production namespace"
    puts "  Applying Kubernetes manifests"
    puts "  Waiting for rollout to complete"
    puts "  Verifying deployment health"
    puts "  Running smoke tests"
    sleep(5)
    
    puts "  Production URL: https://ruby-app.com"
  end
end

class DockerfileGenerator
  def self.generate_ruby_dockerfile(options = {})
    ruby_version = options[:ruby_version] || '3.0'
    rails_version = options[:rails_version] || '7.0'
    node_version = options[:node_version] || '16'
    
    dockerfile = <<~DOCKERFILE
      # Multi-stage build for Ruby on Rails application
      # Stage 1: Build stage
      FROM ruby:#{ruby_version}-slim as builder
      
      # Install system dependencies
      RUN apt-get update -qq && \\
          apt-get install -y build-essential libpq-dev nodejs npm
      
      # Set working directory
      WORKDIR /app
      
      # Copy Gemfile and Gemfile.lock
      COPY Gemfile Gemfile.lock ./
      
      # Install Ruby dependencies
      RUN bundle config set --local without 'development test' && \\
          bundle install --jobs=4 --retry=3
      
      # Copy package.json and package-lock.json
      COPY package.json package-lock.json ./
      
      # Install Node.js dependencies
      RUN npm ci --only=production
      
      # Copy application code
      COPY . .
      
      # Precompile assets
      RUN SECRET_KEY_BASE=dummy RAILS_ENV=production \\
          bundle exec rails assets:precompile
      
      # Stage 2: Production stage
      FROM ruby:#{ruby_version}-slim as production
      
      # Install system dependencies
      RUN apt-get update -qq && \\
          apt-get install -y libpq-dev nodejs && \\
          rm -rf /var/lib/apt/lists/*
      
      # Set working directory
      WORKDIR /app
      
      # Copy installed gems
      COPY --from=builder /usr/local/bundle /usr/local/bundle
      COPY --from=builder /app/node_modules /app/node_modules
      COPY --from=builder /app/public /app/public
      
      # Copy application code
      COPY --from=builder /app /app
      
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
    puts "- Dependency caching"
  end
end
```

## 📊 Monitoring and Logging

### 5. CI/CD Monitoring

Pipeline monitoring and observability:

```ruby
class CIPipelineMonitor
  def initialize
    @pipelines = {}
    @metrics = {}
    @alerts = []
    @notifications = []
    @thresholds = {
      pipeline_duration: 1800, # 30 minutes
      test_failure_rate: 0.1, # 10%
      build_failure_rate: 0.05, # 5%
      deployment_failure_rate: 0.02 # 2%
    }
  end
  
  def track_pipeline(pipeline_id, pipeline_data)
    @pipelines[pipeline_id] = {
      id: pipeline_id,
      started_at: Time.now,
      status: pipeline_data[:status],
      stages: pipeline_data[:stages] || [],
      artifacts: pipeline_data[:artifacts] || {},
      metrics: {
        duration: 0,
        stage_durations: {},
        test_results: {},
        build_results: {},
        deployment_results: {}
      }
    }
    
    puts "Started tracking pipeline: #{pipeline_id}"
  end
  
  def update_pipeline(pipeline_id, updates)
    pipeline = @pipelines[pipeline_id]
    return unless pipeline
    
    pipeline.merge!(updates)
    
    if updates[:status]
      pipeline[:ended_at] = Time.now
      pipeline[:duration] = pipeline[:ended_at] - pipeline[:started_at]
      
      # Check for alerts
      check_pipeline_alerts(pipeline)
      
      # Update metrics
      update_metrics(pipeline_id)
      
      puts "Updated pipeline #{pipeline_id}: #{updates[:status]}"
    end
  end
  
  def add_stage_metric(pipeline_id, stage_name, metric_data)
    pipeline = @pipelines[pipeline_id]
    return unless pipeline
    
    pipeline[:metrics][:stage_durations][stage_name] = metric_data[:duration]
    pipeline[:metrics][:test_results][stage_name] = metric_data[:test_results] if metric_data[:test_results]
    
    puts "Added stage metric for #{pipeline_id}: #{stage_name}"
  end
  
  def get_pipeline_metrics(pipeline_id)
    pipeline = @pipelines[pipeline_id]
    return nil unless pipeline
    
    {
      id: pipeline_id,
      status: pipeline[:status],
      duration: pipeline[:duration],
      stage_count: pipeline[:stages].length,
      success_rate: calculate_success_rate(pipeline_id),
      avg_stage_duration: calculate_avg_stage_duration(pipeline_id),
      test_results: pipeline[:metrics][:test_results],
      artifacts: pipeline[:artifacts]
    }
  end
  
  def get_overall_metrics
    total_pipelines = @pipelines.length
    successful_pipelines = @pipelines.count { |_, p| p[:status] == :success }
    failed_pipelines = @pipelines.count { |_, p| p[:status] == :failed }
    
    {
      total_pipelines: total_pipelines,
      successful_pipelines: successful_pipelines,
      failed_pipelines: failed_pipelines,
      success_rate: total_pipelines > 0 ? (successful_pipelines.to_f / total_pipelines * 100).round(2) : 0,
      avg_duration: calculate_avg_pipeline_duration,
      alerts: @alerts.length,
      metrics: @metrics
    }
  end
  
  def set_threshold(threshold_name, value)
    @thresholds[threshold_name] = value
    puts "Set threshold: #{threshold_name} = #{value}"
  end
  
  def add_alert(type, config = {})
    alert = {
      id: SecureRandom.hex(8),
      type: type,
      config: config,
      created_at: Time.now,
      resolved: false
    }
    
    @alerts << alert
    puts "Added alert: #{type}"
    
    alert
  end
  
  def generate_report
    puts "CI/CD Pipeline Monitoring Report:"
    puts "=" * 50
    
    metrics = get_overall_metrics
    
    puts "Overall Metrics:"
    puts "Total Pipelines: #{metrics[:total_pipelines]}"
    puts "Successful: #{metrics[:successful_pipelines]}"
    puts "Failed: #{metrics[:failed_pipelines]}"
    puts "Success Rate: #{metrics[:success_rate]}%"
    puts "Average Duration: #{metrics[:avg_duration].round(2)}s"
    puts "Active Alerts: #{metrics[:alerts]}"
    
    puts "\nPipeline Performance:"
    @pipelines.each do |id, pipeline|
      status_icon = pipeline[:status] == :success ? '✅' : '❌'
      duration = pipeline[:duration] ? pipeline[:duration].round(2) : 'N/A'
      puts "#{status_icon} #{id}: #{duration}s (#{pipeline[:stages].length} stages)"
    end
    
    puts "\nRecent Alerts:"
    @alerts.last(5).each do |alert|
      status_icon = alert[:resolved] ? '✅' : '⚠️'
      puts "#{status_icon} #{alert[:type]}: #{alert[:config][:message]}"
      puts "   Created: #{alert[:created_at]}"
    end
    
    puts "\nThresholds:"
    @thresholds.each do |name, value|
      puts "#{name}: #{value}"
    end
    
    {
      overall_metrics: metrics,
      pipeline_details: @pipelines,
      alerts: @alerts,
      thresholds: @thresholds
    }
  end
  
  def self.demonstrate_monitoring
    puts "CI/CD Pipeline Monitoring Demonstration:"
    puts "=" * 50
    
    monitor = CIPipelineMonitor.new
    
    # Track some pipelines
    puts "Tracking pipelines:"
    
    pipeline_ids = ['build-123', 'test-456', 'deploy-789', 'security-012']
    
    pipeline_ids.each do |id|
      monitor.track_pipeline(id, {
        status: :running,
        stages: ['build', 'test', 'deploy'],
        artifacts: ['docker-image', 'test-results']
      })
    end
    
    # Simulate pipeline updates
    sleep(1)
    
    monitor.update_pipeline('build-123', {
      status: :success,
      stages: ['build', 'test', 'deploy']
    })
    
    monitor.add_stage_metric('build-123', 'build', {
      duration: 120,
      test_results: { total: 100, passed: 98, failed: 2 }
    })
    
    monitor.add_stage_metric('build-123', 'test', {
      duration: 180,
      test_results: { total: 100, passed: 100, failed: 0 }
    })
    
    monitor.update_pipeline('test-456', {
      status: :failed,
      stages: ['build', 'test']
    })
    
    monitor.update_pipeline('deploy-789', {
      status: :success,
      stages: ['deploy']
    })
    
    # Get metrics
    puts "\nPipeline metrics:"
    pipeline_ids.each do |id|
      metrics = monitor.get_pipeline_metrics(id)
      puts "#{id}: #{metrics[:status]} (#{metrics[:duration].round(2)}s)"
    end
    
    # Generate report
    puts "\nGenerating monitoring report:"
    report = monitor.generate_report
    
    puts "\nMonitoring Features:"
    puts "- Pipeline tracking"
    puts "- Stage metrics"
    puts "- Success rate calculation"
    puts "- Alert management"
    puts "- Threshold monitoring"
    puts "- Performance metrics"
    puts "- Comprehensive reporting"
  end
  
  private
  
  def calculate_success_rate(pipeline_id)
    pipeline = @pipelines[pipeline_id]
    return 0 unless pipeline
    
    total_stages = pipeline[:stages].length
    successful_stages = pipeline[:stages].count { |stage| stage[:status] == :success }
    
    total_stages > 0 ? (successful_stages.to_f / total_stages * 100).round(2) : 0
  end
  
  def calculate_avg_stage_duration(pipeline_id)
    pipeline = @pipelines[pipeline_id]
    return 0 unless pipeline
    
    durations = pipeline[:metrics][:stage_durations].values
    return 0 if durations.empty?
    
    durations.sum.to_f / durations.length
  end
  
  def calculate_avg_pipeline_duration
    durations = @pipelines.values
                    .map { |p| p[:duration] }
                    .compact
    
    return 0 if durations.empty?
    durations.sum.to_f / durations.length
  end
  
  def check_pipeline_alerts(pipeline)
    # Duration alert
    if pipeline[:duration] && pipeline[:duration] > @thresholds[:pipeline_duration]
      add_alert(:pipeline_duration, {
        message: "Pipeline #{pipeline[:id]} exceeded duration threshold",
        pipeline_id: pipeline[:id],
        duration: pipeline[:duration],
        threshold: @thresholds[:pipeline_duration]
      })
    end
    
    # Failure alert
    if pipeline[:status] == :failed
      add_alert(:pipeline_failure, {
        message: "Pipeline #{pipeline[:id]} failed",
        pipeline_id: pipeline[:id],
        failed_stages: pipeline[:stages].count { |s| s[:status] == :failed }
      })
    end
    
    # Test failure alert
    test_results = pipeline[:metrics][:test_results].values
    total_tests = test_results.sum { |r| r[:total] || 0 }
    failed_tests = test_results.sum { |r| r[:failed] || 0 }
    
    if total_tests > 0 && (failed_tests.to_f / total_tests) > @thresholds[:test_failure_rate]
      add_alert(:test_failure, {
        message: "High test failure rate in pipeline #{pipeline[:id]}",
        pipeline_id: pipeline[:id],
        failure_rate: (failed_tests.to_f / total_tests * 100).round(2),
        threshold: (@thresholds[:test_failure_rate] * 100).round(2)
      })
    end
  end
  
  def update_metrics(pipeline_id)
    # Update overall metrics
    @metrics[:total_pipelines] = @pipelines.length
    @metrics[:successful_pipelines] = @pipelines.count { |_, p| p[:status] == :success }
    @metrics[:failed_pipelines] = @pipelines.count { |_, p| p[:status] == :failed }
    @metrics[:success_rate] = @metrics[:total_pipelines] > 0 ? 
      (@metrics[:successful_pipelines].to_f / @metrics[:total_pipelines] * 100).round(2) : 0
    @metrics[:avg_duration] = calculate_avg_pipeline_duration
  end
end
```

## 🎯 CI/CD Best Practices

### 6. CI/CD Guidelines

Best practices and recommendations:

```ruby
class CICDBestPractices
  def self.pipeline_design_guidelines
    puts "CI/CD Pipeline Design Guidelines:"
    puts "=" * 50
    
    guidelines = [
      {
        category: "Pipeline Structure",
        practices: [
          "Keep pipelines simple and readable",
          "Use descriptive stage names",
          "Group related operations",
          "Avoid complex dependencies",
          "Document pipeline logic"
        ],
        benefits: ["Maintainability", "Debugging", "Team collaboration"]
      },
      {
        category: "Performance Optimization",
        practices: [
          "Parallelize independent tasks",
          "Use caching effectively",
          "Optimize Docker layers",
          "Minimize network transfers",
          "Use appropriate resources"
        ],
        benefits: ["Faster execution", "Cost reduction", "Better utilization"]
      },
      {
        category: "Security",
        practices: [
          "Use secrets management",
          "Scan for vulnerabilities",
          "Sign artifacts",
          "Use least privilege",
          "Audit pipeline access"
        ],
        benefits: ["Security", "Compliance", "Risk reduction"]
      },
      {
        category: "Reliability",
        practices: [
          "Implement retry logic",
          "Use idempotent operations",
          "Add health checks",
          "Implement rollback",
          "Monitor pipeline health"
        ],
        benefits: ["Reliability", "Recovery", "Stability"]
      },
      {
        category: "Testing",
        practices: [
          "Run comprehensive tests",
          "Use test matrices",
          "Test in multiple environments",
          "Include performance tests",
          "Test rollback procedures"
        ],
        benefits: ["Quality assurance", "Risk reduction", "Confidence"]
      },
      {
        category: "Monitoring",
        practices: [
          "Track pipeline metrics",
          "Set up alerts",
          "Monitor resource usage",
          "Log all activities",
          "Generate reports"
        ],
        benefits: ["Visibility", "Proactive management", "Continuous improvement"]
      }
    ]
    
    guidelines.each do |guideline|
      puts "#{guideline[:category]}:"
      guideline[:practices].each_with_index do |practice, i|
        puts "  #{i + 1}. #{practice}"
      end
      puts "  Benefits: #{guideline[:benefits].join(', ')}"
      puts
    end
  end
  
  def self.common_mistakes
    puts "\nCommon CI/CD Mistakes:"
    puts "=" * 50
    
    mistakes = [
      {
        mistake: "Complex Pipelines",
        description: "Creating overly complex pipeline configurations",
        impact: ["Hard to maintain", "Difficult to debug", "Team confusion"],
        solution: "Keep pipelines simple and modular"
      },
      {
        mistake: "No Testing",
        description: "Skipping comprehensive testing in pipelines",
        impact: ["Quality issues", "Deployment failures", "User impact"],
        solution: "Include comprehensive testing at all stages"
      },
      {
        mistake: "Hardcoded Values",
        description: "Hardcoding configuration values in pipelines",
        impact: ["Security risks", "Maintenance issues", "Environment problems"],
        solution: "Use environment variables and secrets"
      },
      {
        mistake: "No Rollback",
        description: "Not implementing rollback procedures",
        impact: ["Deployment failures", "Extended downtime", "User impact"],
        solution: "Implement automatic rollback capabilities"
      },
      {
        mistake: "Poor Monitoring",
        description: "Lack of pipeline monitoring and alerting",
        impact: ["No visibility", "Delayed issue detection", "Poor reliability"],
        solution: "Implement comprehensive monitoring and alerting"
      },
      {
        mistake: "Resource Waste",
        description: "Using excessive resources for simple tasks",
        impact: ["High costs", "Wasted resources", "Environmental impact"],
        solution: "Optimize resource usage and costs"
      }
    ]
    
    mistakes.each do |mistake|
      puts "#{mistake[:mistake]}:"
      puts "  Description: #{mistake[:description]}"
      puts "  Impact: #{mistake[:impact].join(', ')}"
      puts "  Solution: #{mistake[:solution]}"
      puts
    end
  end
  
  def self.performance_optimization
    puts "\nPerformance Optimization Tips:"
    puts "=" * 50
    
    tips = [
      {
        tip: "Parallel Execution",
        description: "Run independent tasks in parallel",
        implementation: ["Matrix builds", "Parallel stages", "Distributed testing"],
        benefits: ["Faster execution", "Better resource utilization"]
      },
      {
        tip: "Caching Strategies",
        description: "Implement effective caching at different levels",
        implementation: ["Docker layer cache", "Dependency cache", "Artifact cache"],
        benefits: ["Faster builds", "Reduced network traffic"]
      },
      {
        tip: "Resource Optimization",
        description: "Optimize resource allocation and usage",
        implementation: ["Right-sizing", "Auto-scaling", "Resource sharing"],
        benefits: ["Cost reduction", "Better performance"]
      },
      {
        tip: "Incremental Builds",
        description: "Build only what changed",
        implementation: ["Change detection", "Selective compilation", "Smart caching"],
        benefits: ["Faster builds", "Reduced resource usage"]
      },
      {
        tip: "Network Optimization",
        description: "Optimize network transfers and dependencies",
        implementation: ["Local registries", "Dependency optimization", "Compression"],
        benefits: ["Faster downloads", "Reduced bandwidth"]
      }
    ]
    
    tips.each do |tip|
      puts "#{tip[:tip]}:"
      puts "  Description: #{tip[:description]}"
      puts "  Implementation: #{tip[:implementation].join(', ')}"
      puts "  Benefits: #{tip[:benefits].join(', ')}"
      puts
    end
  end
  
  def self.security_best_practices
    puts "\nSecurity Best Practices:"
    puts "=" * 50
    
    practices = [
      {
        practice: "Secret Management",
        description: "Secure management of sensitive information",
        guidelines: [
          "Use encrypted secrets",
          "Rotate secrets regularly",
          "Limit secret access",
          "Audit secret usage",
          "Use secure storage"
        ],
        tools: ["HashiCorp Vault", "AWS Secrets Manager", "GitHub Secrets"]
      },
      {
        practice: "Artifact Security",
        description: "Secure build artifacts and dependencies",
        guidelines: [
          "Sign artifacts",
          "Scan for vulnerabilities",
          "Use trusted registries",
          "Verify artifact integrity",
          "Implement access controls"
        ],
        tools: ["Cosign", "Notary", "Docker Content Trust"]
      },
      {
        practice: "Network Security",
        description: "Secure network communications",
        guidelines: [
          "Use HTTPS everywhere",
          "Implement network segmentation",
          "Use VPN for private networks",
          "Monitor network traffic",
          "Implement firewall rules"
        ],
        tools: ["WireGuard", "OpenVPN", "iptables"]
      },
      {
        practice: "Access Control",
        description: "Control access to CI/CD systems",
        guidelines: [
          "Use role-based access",
          "Implement MFA",
          "Audit access logs",
          "Use temporary credentials",
          "Regular access reviews"
        ],
        tools: ["OAuth", "LDAP", "RBAC"]
      }
    ]
    
    practices.each do |practice|
      puts "#{practice[:practice]}:"
      puts "  Description: #{practice[:description]}"
      puts "  Guidelines: #{practice[:guidelines].join(', ')}"
      puts "  Tools: #{practice[:tools].join(', ')}"
      puts
    end
  end
  
  def self.demonstrate_best_practices
    pipeline_design_guidelines
    common_mistakes
    performance_optimization
    security_best_practices
    
    puts "\nCI/CD Best Practices Summary:"
    puts "- Simple and maintainable pipelines"
    puts "- Comprehensive testing at all stages"
    puts "- Proper secrets management"
    puts "- Performance optimization techniques"
    puts "- Security-first approach"
    puts "- Comprehensive monitoring"
    puts "- Effective error handling"
    puts "- Rollback capabilities"
    puts "- Documentation and knowledge sharing"
  end
end
```

## 🎓 Exercises

### Beginner Exercises

1. **Basic Pipeline**: Create simple CI pipeline
2. **GitHub Actions**: Build GitHub Actions workflow
3. **Docker CI**: Implement container-based CI
4. **Monitoring**: Add pipeline monitoring

### Intermediate Exercises

1. **Advanced Pipeline**: Complex multi-stage pipeline
2. **Matrix Builds**: Parallel testing strategies
3. **Security Scanning**: Add security checks
4. **Performance Optimization**: Optimize pipeline performance

### Advanced Exercises

1. **Enterprise CI**: Production-ready CI/CD system
2. **Multi-Environment**: Multi-environment deployments
3. **Auto-scaling**: Dynamic resource management
4. **Analytics**: Comprehensive pipeline analytics

---

## 🎯 Summary

Continuous Integration in Ruby provides:

- **CI/CD Fundamentals** - Core concepts and principles
- **CI Pipeline Implementation** - Build automation pipeline
- **GitHub Actions** - Workflow automation platform
- **Docker Integration** - Container-based CI/CD
- **Monitoring and Logging** - Pipeline observability
- **Best Practices** - Guidelines and recommendations

Master these CI/CD techniques for automated Ruby application delivery!
