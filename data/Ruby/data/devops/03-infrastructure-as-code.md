# Infrastructure as Code in Ruby
# Comprehensive guide to IaC implementation and management

## 🏗️ Infrastructure as Code Fundamentals

### 1. IaC Concepts

Core Infrastructure as Code principles:

```ruby
class InfrastructureAsCodeFundamentals
  def self.explain_iac_concepts
    puts "Infrastructure as Code Concepts:"
    puts "=" * 50
    
    concepts = [
      {
        concept: "Infrastructure as Code (IaC)",
        description: "Managing infrastructure through code and automation",
        principles: ["Version control", "Automation", "Idempotency", "Repeatability"],
        benefits: ["Consistency", "Scalability", "Documentation", "Collaboration"],
        challenges: ["Complexity", "Learning curve", "Tool selection"]
      },
      {
        concept: "Declarative vs Imperative",
        description: "Different approaches to infrastructure definition",
        declarative: ["Desired state", "Automatic convergence", "Simplicity"],
        imperative: ["Step-by-step", "Control flow", "Flexibility"],
        comparison: ["Declarative: What", "Imperative: How"]
      },
      {
        concept: "Idempotency",
        description: "Operations produce same results when repeated",
        importance: ["Reliability", "Repeatability", "Error recovery"],
        implementation: ["State management", "Idempotent operations", "Safe updates"],
        examples: ["Resource creation", "Configuration updates"]
      },
      {
        concept: "State Management",
        description: "Tracking and managing infrastructure state",
        types: ["Local state", "Remote state", "Shared state"],
        challenges: ["State drift", "Concurrent access", "State consistency"],
        solutions: ["State locking", "State backends", "State encryption"]
      },
      {
        concept: "DRY Principle",
        description: "Don't Repeat Yourself in infrastructure",
        applications: ["Reusable modules", "Templates", "Configuration sharing"],
        benefits: ["Consistency", "Maintainability", "Reduced errors"],
        implementations: ["Modules", "Templates", "Libraries"]
      },
      {
        concept: "GitOps",
        description: "Git-based operations for infrastructure",
        workflow: ["Git as source of truth", "Automated sync", "Pull requests"],
        benefits: ["Version control", "Audit trail", "Collaboration"],
        tools: ["FluxCD", "Argo CD", "Rancher"]
      }
    ]
    
    concepts.each do |concept|
      puts "#{concept[:concept]}:"
      puts "  Description: #{concept[:description]}"
      puts "  Principles: #{concept[:principles].join(', ')}" if concept[:principles]
      puts "  Benefits: #{concept[:benefits].join(', ')}" if concept[:benefits]
      puts "  Challenges: #{concept[:challenges].join(', ')}" if concept[:challenges]
      puts "  Declarative: #{concept[:declarative].join(', ')}" if concept[:declarative]
      puts "  Imperative: #{concept[:imperative].join(', ')}" if concept[:imperative]
      puts "  Comparison: #{concept[:comparison].join(', ')}" if concept[:comparison]
      puts "  Importance: #{concept[:importance].join(', ')}" if concept[:importance]
      puts "  Implementation: #{concept[:implementation].join(', ')}" if concept[:implementation]
      puts "  Examples: #{concept[:examples].join(', ')}" if concept[:examples]
      puts "  Types: #{concept[:types].join(', ')}" if concept[:types]
      puts "  Solutions: #{concept[:solutions].join(', ')}" if concept[:solutions]
      puts "  Applications: #{concept[:applications].join(', ')}" if concept[:applications]
      puts "  Implementations: #{concept[:implementations].join(', ')}" if concept[:implementations]
      puts "  Workflow: #{concept[:workflow].join(', ')}" if concept[:workflow]
      puts "  Tools: #{concept[:tools].join(', ')}" if concept[:tools]
      puts
    end
  end
  
  def self.iac_tools_comparison
    puts "\nIaC Tools Comparison:"
    puts "=" * 50
    
    tools = [
      {
        name: "Terraform",
        type: "Multi-cloud",
        language: "HCL",
        strengths: ["Multi-cloud support", "Large community", "State management"],
        weaknesses: ["Steep learning curve", "State complexity"],
        use_cases: ["Multi-cloud deployments", "Complex infrastructure"]
      },
      {
        name: "Ansible",
        type: "Configuration management",
        language: "YAML",
        strengths: ["Agentless", "Simple syntax", "Large module library"],
        weaknesses: ["Imperative", "Limited state management"],
        use_cases: ["Configuration management", "Application deployment"]
      },
      {
        name: "Puppet",
        type: "Configuration management",
        language: "Ruby DSL",
        strengths: ["Mature", "Declarative", "Large ecosystem"],
        weaknesses: ["Complex", "Agent-based", "Steep learning"],
        use_cases: ["Configuration management", "Server orchestration"]
      },
      {
        name: "Chef",
        type: "Configuration management",
        language: "Ruby DSL",
        strengths: ["Testable", "Flexible", "Large ecosystem"],
        weaknesses: ["Complex", "Agent-based", "Steep learning"],
        use_cases: ["Configuration management", "Application deployment"]
      },
      {
        name: "Pulumi",
        type: "Multi-language",
        language: ["Python", "JavaScript", "Go", "C#", "Ruby"],
        strengths: ["Multi-language", "Infrastructure abstractions", "Real languages"],
        weaknesses: ["Newer", "Smaller community", "Learning curve"],
        use_cases: ["Multi-language teams", "Code-first IaC"]
      },
      {
        name: "CloudFormation",
        type: "AWS-specific",
        language: "YAML/JSON",
        strengths: ["AWS native", "Deep integration", "Free"],
        weaknesses: ["AWS lock-in", "Verbose", "Steep learning"],
        use_cases: ["AWS deployments", "CloudFormation templates"]
      }
    ]
    
    tools.each do |tool|
      puts "#{tool[:name]}:"
      puts "  Type: #{tool[:type]}"
      puts "  Language: #{tool[:language]}"
      puts "  Strengths: #{tool[:strengths].join(', ')}"
      puts "  Weaknesses: #{tool[:weaknesses].join(', ')}"
      puts "  Use Cases: #{tool[:use_cases].join(', ')}"
      puts
    end
  end
  
  def self.iac_best_practices
    puts "\nInfrastructure as Code Best Practices:"
    puts "=" * 50
    
    practices = [
      {
        practice: "Version Control",
        description: "Store all infrastructure code in version control",
        guidelines: [
          "Use Git for all IaC code",
          "Commit frequently and in small chunks",
          "Use descriptive commit messages",
          "Use branches for features/environments",
          "Tag releases for production"
        ],
        benefits: ["Version history", "Collaboration", "Rollback capability"]
      },
      {
        practice: "Modular Design",
        description: "Organize infrastructure code into modules",
        guidelines: [
          "Use reusable modules",
          "Follow single responsibility",
          "Create clear interfaces",
          "Document module dependencies",
          "Test modules independently"
        ],
        benefits: ["Reusability", "Maintainability", "Testing"]
      },
      {
        practice: "Environment Separation",
        description: "Separate infrastructure by environment",
        guidelines: [
          "Use different configs per environment",
          "Parameterize environment-specific values",
          "Use environment variables",
          "Implement promotion workflows",
          "Test in staging before production"
        ],
        benefits: ["Safety", "Consistency", "Flexibility"]
      },
      {
        practice: "Security First",
        description: "Implement security from the beginning",
        guidelines: [
          "Encrypt secrets",
          "Use least privilege access",
          "Implement network security",
          "Regular security audits",
          "Compliance checks"
        ],
        benefits: ["Security", "Compliance", "Risk reduction"]
      },
      {
        practice: "Testing",
        description: "Test infrastructure code thoroughly",
        guidelines: [
          "Unit test modules",
          "Integration test infrastructure",
          "Test in isolated environments",
          "Test rollbacks",
          "Automate testing in CI/CD"
        ],
        benefits: ["Reliability", "Confidence", "Quality assurance"]
      },
      {
        practice: "Documentation",
        description: "Document infrastructure code and decisions",
        guidelines: [
          "Document module usage",
          "Document architecture decisions",
          "Include examples",
          "Keep documentation current",
          "Use diagrams for complex systems"
        ],
        benefits: ["Team understanding", "Onboarding", "Maintenance"]
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
  
  # Run IaC fundamentals
  explain_iac_concepts
  iac_tools_comparison
  iac_best_practices
end
```

### 2. Terraform Implementation

Infrastructure provisioning with Terraform:

```ruby
class TerraformProvider
  def initialize(name, config = {})
    @name = name
    @config = config
    @resources = {}
    @data_sources = {}
    @variables = {}
    @outputs = {}
    @state = {}
  end
  
  attr_reader :name, :resources, :data_sources, :variables, :outputs
  
  def resource(type, name, &block)
    resource = TerraformResource.new(type, name, @config)
    resource.instance_eval(&block) if block_given?
    @resources[name] = resource
    resource
  end
  
  def data_source(type, name, &block)
    data_source = TerraformDataSource.new(type, name, @config)
    data_source.instance_eval(&block) if block_given?
    @data_sources[name] = data_source
    data_source
  end
  
  def variable(name, options = {})
    variable = TerraformVariable.new(name, options)
    @variables[name] = variable
    variable
  end
  
  def output(name, &block)
    output = TerraformOutput.new(name)
    output.instance_eval(&block) if block_given?
    @outputs[name] = output
    output
  end
  
  def generate_terraform
    terraform = {
      terraform: {
        required_providers: {
          "#{@name}" = {
            source: @config[:source] || "hashicorp/#{@name}",
            version: @config[:version]
          }
        }
      },
      provider: {
        "#{@name}" => @config[:provider_config] || {}
      },
      resource: {},
      data: {},
      variable: {},
      output: {}
    }
    
    # Add resources
    @resources.each do |name, resource|
      terraform[:resource][name] = resource.to_h
    end
    
    # Add data sources
    @data_sources.each do |name, data_source|
      terraform[:data][name] = data_source.to_h
    end
    
    # Add variables
    @variables.each do |name, variable|
      terraform[:variable][name] = variable.to_h
    end
    
    # Add outputs
    @outputs.each do |name, output|
      terraform[:output][name] = output.to_h
    end
    
    terraform
  end
  
  def plan
    puts "Planning Terraform changes for provider: #{@name}"
    puts "Resources: #{@resources.length}"
    puts "Data Sources: #{@data_sources.length}"
    puts "Variables: #{@variables.length}"
    puts "Outputs: #{@outputs.length}"
    
    # Simulate plan output
    puts "\nTerraform Plan:"
    puts "+ create resource 'aws_instance.example'"
    puts "+ update resource 'aws_security_group.example'"
    puts "~ destroy resource 'aws_instance.old'"
    
    {
      status: :success,
      changes: {
        create: @resources.length,
        update: 0,
        delete: 0
      }
    }
  end
  
  def apply
    puts "Applying Terraform changes for provider: #{@name}"
    
    # Simulate apply
    @resources.each do |name, resource|
      puts "Creating resource: #{resource.type} #{name}"
      resource.apply
    end
    
    @data_sources.each do |name, data_source|
      puts "Reading data source: #{data_source.type} #{name}"
      data_source.read
    end
    
    {
      status: :success,
      applied_resources: @resources.length,
      read_data_sources: @data_sources.length
    }
  end
  
  def destroy
    puts "Destroying Terraform resources for provider: #{@name}"
    
    @resources.each do |name, resource|
      puts "Destroying resource: #{resource.type} #{name}"
      resource.destroy
    end
    
    {
      status: :success,
      destroyed_resources: @resources.length
    }
  end
  
  def state
    @state
  end
  
  def self.demonstrate_terraform_provider
    puts "Terraform Provider Demonstration:"
    puts "=" * 50
    
    # Create AWS provider
    aws_provider = TerraformProvider.new('aws', {
      source: 'hashicorp/aws',
      version: '4.0.0',
      region: 'us-west-2',
      access_key: 'AKIAIOSFODNN7EXAMPLE',
      secret_key: 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY'
    })
    
    # Define resources
    aws_provider.resource('aws_instance', 'web_server') do
      ami 'ami-0c55b159cbfafe1f0'
      instance_type 't3.micro'
      tags {
        Name 'Web Server'
        Environment 'Production'
      }
      
      network_interface do
        device_index 0
        associate_public_ip_address true
      end
      
      user_data <<-EOT
        #!/bin/bash
        echo "Hello World" > /var/www/html/index.html
        service apache2 start
      EOT
    end
    
    aws_provider.resource('aws_security_group', 'web_sg') do
      name 'web-security-group'
      description 'Security group for web server'
      
      ingress {
        from_port 22
        to_port 22
        protocol 'tcp'
        cidr_blocks ['0.0.0.0/0']
        description 'Allow SSH'
      }
      
      ingress {
        from_port 80
        to_port 80
        protocol 'tcp'
        cidr_blocks ['0.0.0.0/0']
        description 'Allow HTTP'
      }
      
      egress {
        from_port 0
        to_port 0
        protocol '-1'
        cidr_blocks ['0.0.0.0/0']
        description 'Allow all outbound'
      }
    end
    
    # Define data source
    aws_provider.data_source('aws_ami', 'ubuntu') do
      most_recent true
      owners ['099720109477']
      name_regex 'ubuntu/images/hvm-ssd/ubuntu-focal-20.04-amd64-server'
    end
    
    # Define variables
    aws_provider.variable('instance_type') do
      description 'EC2 instance type'
      type 'string'
      default 't3.micro'
    end
    
    aws_provider.variable('allow_ssh') do
      description 'Allow SSH access'
      type 'bool'
      default true
    end
    
    # Define outputs
    aws_provider.output('instance_public_ip') do
      description 'Public IP address of EC2 instance'
      value = aws_instance.web_server.public_ip
    end
    
    aws_provider.output('instance_id') do
      description 'ID of the EC2 instance'
      value = aws_instance.web_server.id
    end
    
    # Generate Terraform configuration
    terraform_config = aws_provider.generate_terraform
    
    puts "Generated Terraform Configuration:"
    puts terraform_config.to_yaml
    
    # Simulate plan and apply
    puts "\nPlanning changes:"
    plan_result = aws_provider.plan
    
    puts "\nApplying changes:"
    apply_result = aws_provider.apply
    
    puts "\nTerraform Provider Features:"
    puts "- Resource definition"
    puts "- Data source configuration"
    "- Variable management"
    "- Output configuration"
    "- State management"
    "- Plan and apply operations"
    "- Multi-provider support"
  end
  
  private
end

class TerraformResource
  def initialize(type, name, config = {})
    @type = type
    @name = name
    @config = config
    @attributes = {}
    @created = false
  end
  
  attr_reader :type, :name, :attributes
  
  def method_missing(method_name, *args, &block)
    @attributes[method_name] = args.first if args.any?
    @attributes[method_name] = block.call if block_given?
  end
  
  def apply
    puts "    Creating #{@type} #{@name}"
    @created = true
    
    @attributes.each do |key, value|
      puts "      #{key}: #{value}"
    end
  end
  
  def destroy
    puts "    Destroying #{@type} #{@name}"
    @created = false
  end
  
  def to_h
    {
      type: @type,
      name: @name,
      attributes: @attributes
    }
  end
end

class TerraformDataSource
  def initialize(type, name, config = {})
    @type = type
    @name = name
    @config = config
    @attributes = {}
    @read = false
  end
  
  attr_reader :type, :name, :attributes
  
  def method_missing(method_name, *args, &block)
    @attributes[method_name] = args.first if args.any?
    @attributes[method_name] = block.call if block_given?
  end
  
  def read
    puts "    Reading #{@type} #{@name}"
    @read = true
    
    # Simulate data source read
    case @type
    when 'aws_ami'
      @attributes[:id] = 'ami-0c55b159cbfafe1f0'
      @attributes[:name] = 'ubuntu/images/hvm-ssd/ubuntu-focal-20.04-amd64-server'
      @attributes[:owner_id] = '099720109477'
    end
  end
  
  def to_h
    {
      type: @type,
      name: @name,
      attributes: @attributes
    }
  end
end

class TerraformVariable
  def initialize(name, options = {})
    @name = name
    @options = options
  end
  
  def type(type)
    @options[:type] = type
  end
  
  def default(value)
    @options[:default] = value
  end
  
  def description(description)
    @options[:description] = description
  end
  
  def to_h
    {
      name: @name,
      type: @options[:type],
      default: @options[:default],
      description: @options[:description]
    }
  end
end

class TerraformOutput
  def initialize(name)
    @name = name
    @value = nil
  end
  
  def value(value)
    @value = value
  end
  
  def description(description)
    @description = description
  end
  
  def to_h
    {
      value: @value,
      description: @description
    }
  end
end
```

## 🤖 Configuration Management

### 3. Ansible Implementation

Configuration management with Ansible:

```ruby
class AnsiblePlaybook
  def initialize(name, hosts = 'all')
    @name = name
    @hosts = hosts
    @vars = {}
    @tasks = []
    @handlers = []
    @roles = []
  end
  
  def vars(&block)
    vars_manager = VarsManager.new(@vars)
    vars_manager.instance_eval(&block) if block_given?
    @vars = vars_manager.vars
  end
  
  def tasks(&block)
    tasks_manager = TasksManager.new(@tasks)
    tasks_manager.instance_eval(&block) if block_given?
    @tasks = tasks_manager.tasks
  end
  
  def handlers(&block)
    handlers_manager = HandlersManager.new(@handlers)
    handlers_manager.instance_eval(&block) if block_given?
    @handlers = handlers_manager.handlers
  end
  
  def roles(&block)
    roles_manager = RolesManager.new(@roles)
    roles_manager.instance_eval(&block) if block_given?
    @roles = roles_manager.roles
  end
  
  def play
    puts "Running Ansible Playbook: #{@name}"
    puts "Hosts: #{@hosts}"
    
    # Set variables
    puts "Variables:"
    @vars.each do |name, value|
      puts "  #{name}: #{value}"
    end
    
    # Execute tasks
    puts "\nExecuting tasks:"
    @tasks.each_with_index do |task, i|
      puts "Task #{i + 1}: #{task[:name]}"
      puts "  Module: #{task[:module]}"
      puts "  Action: #{task[:action]}"
      
      # Simulate task execution
      execute_task(task)
    end
    
    # Execute handlers
    if @handlers.any?
      puts "\nExecuting handlers:"
      @handlers.each do |handler|
        puts "Handler: #{handler[:name]}"
        execute_handler(handler)
      end
    end
    
    puts "Playbook completed successfully"
  end
  
  def self.demonstrate_ansible_playbook
    puts "Ansible Playbook Demonstration:"
    puts "=" * 50
    
    # Create web server playbook
    playbook = AnsiblePlaybook.new('Web Server Setup', 'webservers')
    
    # Define variables
    playbook.vars do
      web_port 80
      server_name 'web-server-01'
      ssl_enabled true
      firewall_enabled true
    end
    
    # Define tasks
    playbook.tasks do
      task 'Install Apache', 'apache2' do
        state 'present'
        update_cache true
      end
      
      task 'Configure Apache', 'apache2' do
        action 'config'
        args do
          port web_port
          server_name server_name
          ssl_enabled ssl_enabled
        end
      end
      
      task 'Start Apache', 'apache2' do
        state 'started'
        enabled true
      end
      
      task 'Configure Firewall', 'ufw' do
        action 'enabled'
        args do
          policy 'allow'
          port web_port
          proto 'tcp'
        end
      end
    end
    
    # Define handlers
    playbook.handlers do
      handler 'Restart Apache', 'apache2' do
        action 'restarted'
      end
      
      handler 'Reload Firewall', 'ufw' do
        action 'reloaded'
      end
    end
    
    # Run playbook
    playbook.play
    
    puts "\nAnsible Playbook Features:"
    puts "- Variable management"
    puts "- Task execution"
    "- Handler configuration"
    "- Role support"
    "- Host targeting"
    "- Idempotent operations"
    "- Error handling"
  end
  
  private
  
  def execute_task(task)
    puts "  Executing #{task[:module]} #{task[:action]}"
    
    # Simulate task execution
    case task[:module]
    when 'apache2'
      case task[:action]
      when 'present'
        puts "    Installing Apache2..."
      when 'config'
        puts "    Configuring Apache2..."
        task[:args].each { |key, value| puts "      #{key}: #{value}" }
      when 'started'
        puts "    Starting Apache2..."
      end
    when 'ufw'
      case task[:action]
      when 'enabled'
        puts "    Enabling firewall..."
        task[:args].each { |key, value| puts "      #{key}: #{value}" }
      when 'reloaded'
        puts "    Reloading firewall..."
      end
    end
  end
  
  def execute_handler(handler)
    puts "  Executing #{handler[:name]}: #{handler[:module]} #{handler[:action]}"
  end
end

class VarsManager
  def initialize(vars)
    @vars = vars
  end
  
  attr_reader :vars
  
  def method_missing(name, value)
    @vars[name] = value
  end
end

class TasksManager
  def initialize(tasks)
    @tasks = tasks
  end
  
  attr_reader :tasks
  
  def task(name, module_name, action = nil, &block)
    task = {
      name: name,
      module: module_name,
      action: action
    }
    
    task[:block] = block if block_given?
    @tasks << task
    task
  end
  
  def method_missing(name, *args, &block)
    task(name, name, args.first, &block)
  end
end

class HandlersManager
  def initialize(handlers)
    @handlers = handlers
  end
  
  attr_reader :handlers
  
  def handler(name, module_name, action = nil, &block)
    handler = {
      name: name,
      module: module_name,
      action: action
    }
    
    handler[:block] = block if block_given?
    @handlers << handler
    handler
  end
  
  def method_missing(name, *args, &block)
    handler(name, name, args.first, &block)
  end
end

class RolesManager
  def initialize(roles)
    @roles = roles
  end
  
  attr_reader :roles
  
  def role(name)
    @roles << { name: name }
  end
end

class AnsibleRole
  def initialize(name)
    @name = name
    @tasks = []
    @handlers = []
    @vars = {}
    @defaults = {}
  end
  
  def tasks(&block)
    tasks_manager = TasksManager.new(@tasks)
    tasks_manager.instance_eval(&block) if block_given?
    @tasks = tasks_manager.tasks
  end
  
  def handlers(&block)
    handlers_manager = @handlers
    handlers_manager.instance_eval(&block) if block_given?
    @handlers = handlers_manager.handlers
  end
  
  def vars(&block)
    vars_manager = VarsManager.new(@vars)
    vars_manager.instance_eval(&block) if block_given?
    @vars = vars_manager.vars
  end
  
  def defaults(&block)
    defaults_manager = VarsManager.new(@defaults)
    defaults_manager.instance_eval(&block) if block_given?
    @defaults = defaults_manager.vars
  end
  
  def to_h
    {
      name: @name,
      tasks: @tasks.map(&:to_h),
      handlers: @handlers.map(&:to_h),
      vars: @vars,
      defaults: @defaults
    }
  end
  
  def self.create_web_server_role
    role = AnsibleRole.new('webserver')
    
    role.vars do
      web_port 80
      server_name 'web-server'
      document_root '/var/www/html'
      ssl_enabled false
    end
    
    role.defaults do
      web_port 8080
      server_name 'localhost'
    end
    
    role.tasks do
      task 'Install Apache', 'apache2' do
        state 'present'
        update_cache true
      end
      
      task 'Create Document Root', 'file' do
        path document_root
        state 'directory'
        mode '0755'
        owner 'www-data'
        group 'www-data'
      end
      
      task 'Configure Apache', 'apache2' do
        action 'config'
        args do
          port web_port
          server_name server_name
          document_root document_root
          ssl_enabled ssl_enabled
        end
      end
      
      task 'Start Apache', 'apache2' do
        state 'started'
        enabled true
      end
    end
    
    role.handlers do
      handler 'Restart Apache', 'apache2' do
        action 'restarted'
      end
    end
    
    role
  end
  
  def self.demonstrate_role_creation
    puts "Ansible Role Creation Demonstration:"
    puts "=" * 50
    
    # Create web server role
    web_role = create_web_server_role
    
    puts "Generated Role Configuration:"
    puts web_role.to_h
    
    puts "\nRole Features:"
    puts "- Variable management"
    puts "- Default values"
    puts "- Task definition"
    "- Handler configuration"
    "- Reusable components"
    "- Idempotent operations"
  end
end
```

## 🔧 Container Orchestration

### 4. Kubernetes Orchestration

Container orchestration with Kubernetes:

```ruby
class KubernetesCluster
  def initialize(name, config = {})
    @name = name
    @config = config
    @namespaces = {}
    @deployments = {}
    @services = {}
    @config_maps = {}
    @secrets = {}
    @ingresses = {}
    @persistent_volumes = {}
  end
  
  attr_reader :name, :namespaces, :deployments, :services
  
  def namespace(name, &block)
    namespace = KubernetesNamespace.new(name)
    namespace.instance_eval(&block) if block_given?
    @namespaces[name] = namespace
    namespace
  end
  
  def deployment(name, &block)
    deployment = KubernetesDeployment.new(name)
    deployment.instance_eval(&block) if block_given?
    @deployments[name] = deployment
    deployment
  end
  
  def service(name, &block)
    service = KubernetesService.new(name)
    service.instance_eval(&block) if block_given?
    @services[name] = service
    service
  end
  
  def config_map(name, &block)
    config_map = KubernetesConfigMap.new(name)
    config_map.instance_eval(&block) if block_given?
    @config_maps[name] = config_map
    config_map
  end
  
  def secret(name, &block)
    secret = KubernetesSecret.new(name)
    secret.instance_eval(&block) if block_given?
    @secrets[name] = secret
    secret
  end
  
  def ingress(name, &block)
    ingress = KubernetesIngress.new(name)
    ingress.instance_eval(&block) if block_given?
    @ingresses[name] = ingress
    ingress
  end
  
  def persistent_volume(name, &block)
    pv = KubernetesPersistentVolume.new(name)
    pv.instance_eval(&block) if block_given?
    @persistent_volumes[name] = pv
    pv
  end
  
  def generate_kubernetes_yaml
    k8s = {
      apiVersion: 'v1',
      kind: 'List',
      items: []
    }
    
    # Add namespaces
    @namespaces.each do |name, ns|
      k8s[:items] << ns.to_h
    end
    
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
    
    k8s
  end
  
  def apply
    puts "Applying Kubernetes configuration to cluster: #{@name}"
    
    k8s_config = generate_kubernetes_yaml
    
    puts "Namespaces: #{@namespaces.length}"
    puts "Deployments: #{@deployments.length}"
    puts "Services: #{@services.length}"
    puts "Config Maps: #{@config_maps.length}"
    puts "Secrets: @secrets.length}"
    puts "Ingresses: #{@ingresses.length}"
    puts "Persistent Volumes: #{@persistent_volumes.length}"
    
    # Simulate applying to cluster
    k8s_config[:items].each do |item|
      kind = item[:kind]
      name = item[:metadata][:name]
      
      puts "  Creating #{kind}: #{name}"
      
      case kind
      when 'Namespace'
        puts "    Setting up namespace"
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
      end
    end
    
    {
      success: true,
      cluster: @name,
      resources: {
        namespaces: @namespaces.length,
        deployments: @deployments.length,
        services: @services.length,
        config_maps: @config_maps.length,
        secrets: @secrets.length,
        ingresses: @ingresses.length,
        persistent_volumes: @persistent_volumes.length
      }
    }
  end
  
  def self.demonstrate_kubernetes_cluster
    puts "Kubernetes Cluster Demonstration:"
    puts "=" * 50
    
    # Create cluster
    cluster = KubernetesCluster.new('production-cluster')
    
    # Create namespace
    cluster.namespace('production') do
      labels 'environment' => 'production'
      annotations 'managed-by' => 'terraform'
    end
    
    # Create web application deployment
    cluster.deployment('web-app') do
      replicas 3
      
      labels do
        app 'web-app'
        version 'v1.0.0'
        environment 'production'
      end
      
      container do
        name 'web-app'
        image 'nginx:1.20'
        ports do
          port 80
          protocol 'TCP'
        end
        
        resources do
          requests do
          cpu '100m'
          memory '128Mi'
        end
        end
        
        env do
          name 'NODE_ENV'
          value 'production'
        end
        
        volume_mount do
          name 'web-root'
          mount_path '/usr/share/nginx/html'
        end
      end
      
      volume do
        name 'web-root'
        persistent_volume_claim 'web-root-pvc'
        read_only true
      end
    end
    
    # Create service
    cluster.service('web-app-service') do
      selector do
        app 'web-app'
      end
      
      ports do
        port 80
        protocol 'TCP'
        target_port 80
      end
      
      type 'LoadBalancer'
    end
    
    # Create config map
    cluster.config_map('web-app-config') do
      data do
        'nginx.conf' => <<~CONFIG
          server {
            listen 80;
            server_name _;
            root /usr/share/nginx/html;
          }
        CONFIG
      end
    end
    
    # Create secret
    cluster.secret('web-app-secrets') do
      data do
        'database-url' => 'postgresql://user:password@postgres:5432/webapp'
        'api-key' => 'super-secret-api-key'
      end
    end
    
    # Create ingress
    cluster.ingress('web-app-ingress') do
      rules do
        host 'web-app.example.com'
        path '/'
        backend do
          service_name 'web-app-service'
          service_port 80
        end
      end
    end
    
    # Create persistent volume
    cluster.persistent_volume('web-root-pv') do
      storage_class 'gp2'
      access_modes ['ReadWriteOnce']
      size '1Gi'
    end
    
    # Generate and apply configuration
    k8s_config = cluster.generate_kubernetes_yaml
    
    puts "Generated Kubernetes Configuration:"
    puts k8s_config.to_yaml
    
    # Apply configuration
    result = cluster.apply
    
    puts "\nKubernetes Cluster Features:"
    puts "- Namespace management"
    puts "- Deployment configuration"
    "- Service exposure"
    "- ConfigMap management"
    "- Secret management"
    "- Ingress configuration"
    "- Persistent volume management"
    "- Multi-resource coordination"
  end
  
  private
end

class KubernetesNamespace
  def initialize(name)
    @name = name
    @labels = {}
    @annotations = {}
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
      kind: 'Namespace',
      metadata: {
        name: @name,
        labels: @labels,
        annotations: @annotations
      }
    }
  end
end

class KubernetesDeployment
  def initialize(name)
    @name = name
    @replicas = 1
    @selector = {}
    @labels = {}
    @annotations = {}
    @containers = []
    @volumes = []
    @strategy = {}
  end
  
  def replicas(count)
    @replicas = count
  end
  
  def selector(&block)
    selector_manager = SelectorManager.new(@selector)
    selector_manager.instance_eval(&block) if block_given?
    @selector = selector_manager.selector
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
  
  def strategy(&block)
    strategy_manager = StrategyManager.new(@strategy)
    strategy_manager.instance_eval(&block) if block_given?
      @strategy = strategy_manager.strategy
  end
  
  def container(&block)
    container = KubernetesContainer.new
    container.instance_eval(&block) if block_given?
    @containers << container
  end
  end
  
  def volume(&block)
    volume = KubernetesVolume.new
    volume.instance_eval(&block) if block_given?
    @volumes << volume
  end
  
  def to_h
    {
      apiVersion: 'apps/v1',
      kind: 'Deployment',
      metadata: {
        name: @name,
        labels: @labels,
        annotations: @annotations
      },
      spec: {
        replicas: @replicas,
        selector: @selector,
        template: {
          metadata: {
            labels: @labels,
            annotations: @annotations
          },
          spec: {
            containers: @containers.map(&:to_h),
            volumes: @volumes.map(&:to_h)
          }
        },
        strategy: @strategy
      }
    }
  end
end

class KubernetesService
  def initialize(name)
    @name = name
    @selector = {}
    @ports = []
    @type = 'ClusterIP'
    @labels = {}
    @annotations = {}
  end
  
  def selector(&block)
    selector_manager = SelectorManager.new(@selector)
    selector_manager.instance_eval(&block) if block_given?
    @selector = selector_manager.selector
  end
  
  def ports(&block)
    ports_manager = PortsManager.new(@ports)
    ports_manager.instance_eval(&block) if block_given?
      @ports = ports_manager.ports
    end
  end
  
  def type(type)
    @type = type
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

class KubernetesContainer
  def initialize(name)
    @name = name
    @image = ''
    @ports = []
    @env = []
    @resources = {}
    @volume_mounts = []
    @command = []
    @args = []
  end
  
  def image(image_name)
    @image = image_name
  end
  
  def ports(&block)
    ports_manager = PortsManager.new(@ports)
    ports_manager.instance_eval(&block) if block_given?
      @ports = ports_manager.ports
    end
  end
  
  def env(&block)
    env_manager = EnvManager.new(@env)
    env_manager.instance_eval(&block) if block_given?
      @env = env_manager.env
    end
  end
  
  def resources(&block)
    resources_manager = ResourcesManager.new(@resources)
    resources_manager.instance_eval(&block) if block_given?
      @resources = resources_manager.resources
    end
  end
  
  def volume_mount(&block)
    volume_mount_manager = VolumeMountManager.new(@volume_mounts)
    volume_mount_manager.instance_eval(&block) if block_given?
      @volume_mounts = volume_mount_manager.volume_mounts
    end
  end
  
  def command(cmd)
    @command = Array(cmd)
  end
  
  def args(args)
    @args = Array(args)
  end
  
  def to_h
    {
      name: @name,
      image: @image,
      ports: @ports.map(&:to_h),
      env: @env.map(&:to_h),
      resources: @resources,
      volumeMounts: @volume_mounts.map(&:to_h),
      command: @command,
      args: @args
    }
  end
end

# Helper classes
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
  
  def match(labels)
    @selector.merge!(labels)
  end
end

class PortsManager
  def initialize(ports)
    @ports = ports
  end
  
  attr_reader :ports
  
  def port(port, options = {})
    port_def = { port: port }
    port_def[:protocol] = options[:protocol] if options[:protocol]
    port_def[:target_port] = options[:target_port] if options[:target_port]
    @ports << port_def
  end
  
  def method_missing(name, *args, &block)
    port(name, *args, &block)
  end
end

class EnvManager
  def initialize(env)
    @env = env
  end
  
  attr_reader :env
  
  def method_missing(name, value)
    @env[name] = value
  end
end

class ResourcesManager
  def initialize(resources)
    @resources = resources
  end
  
  attr_reader :resources
  
  def requests(&block)
    @resources[:requests] = {}
    requests_manager = ResourcesSubManager.new(@resources[:requests])
    requests_manager.instance_eval(&block) if block_given?
  end
  
  def limits(&block)
    @resources[:limits] = {}
    limits_manager = ResourcesSubManager.new(@resources[:limits])
    limits_manager.instance_eval(&block) if block_given?
  end
  
  def method_missing(name, *args, &block)
    if name == :requests || name == :limits
      send(name, *args, &block)
    else
      @resources[name] = {}
      resources_sub_manager = ResourcesSubManager.new(@resources[name])
      resources_sub_manager.instance_eval(&block) if block_given?
    end
  end
end

class ResourcesSubManager
  def initialize(resources)
    @resources = resources
  end
  
  def method_missing(name, value)
    @resources[name] = value
  end
end

class VolumeMountManager
  def initialize(volume_mounts)
    @volume_mounts = volume_mounts
  end
  
  attr_reader :volume_mounts
  
  def mount_path(path, options = {})
    volume_mount = { mountPath: path }
    volume_mount[:name] = options[:name] if options[:name]
    volume_mount[:readOnly] = options[:readOnly] if options[:readOnly]
    @volume_mounts << volume_mount
  end
  
  def method_missing(name, value, options = {})
    mount_path(name, options)
  end
end

class StrategyManager
  def initialize(strategy)
    @strategy = strategy
  end
  
  attr_reader :strategy
  
  def type(type)
    @strategy[:type] = type
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
    @strategy[:maxUnavailable] = value
  end
  
  def max_surge(value)
    @strategy[:maxSurge] = value
  end
end

class KubernetesConfigMap
  def initialize(name)
    @name = name
    @data = {}
  end
  
  def data(&block)
    data_manager = DataManager.new(@data)
    data_manager.instance_eval(&block) if block_given?
    @data = data_manager.data
  end
  
  def to_h
    {
      apiVersion: 'v1',
      kind: 'ConfigMap',
      metadata: {
        name: @name
      },
      data: @data
    }
  end
end

class KubernetesSecret
  def initialize(name)
    @name = name
    @data = {}
  end
  
  def data(&block)
    data_manager = DataManager.new(@data)
    data_manager.instance_eval(&block) if block_given?
    @data = data_manager.data
  end
  
  def to_h
    {
      apiVersion: 'v1',
      kind: 'Secret',
      metadata: {
        name: @name
      },
      data: @data
    }
  end
end

class KubernetesIngress
  def initialize(name)
    @name = name
    @rules = []
    @tls = []
    @annotations = {}
  end
  
  def rules(&block)
    rules_manager = IngressRulesManager.new(@rules)
    rules_manager.instance_eval(&block) if block_given?
      @rules = rules_manager.rules
    end
  end
  
  def tls(&block)
    tls_manager = TLSManager.new(@tls)
    tls_manager.instance_eval(&block) if block_given?
      @tls = tls_manager.tls
    end
  
  def annotations(&block)
    annotations_manager = AnnotationsManager.new(@annotations)
    annotations_manager.instance_eval(&block) if block_given?
      @annotations = annotations_manager.annotations
    end
  
  def to_h
      {
        apiVersion: 'networking.k8s.io/v1',
        kind: 'Ingress',
        metadata: {
          name: @name,
          annotations: @annotations
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
  def initialize(rule)
    @rule = rule
  end
  
  attr_reader :rule
  
  def host(host)
    @rule[:host] = host
  end
  
  def path(path)
    @rule[:path] = path
  end
  
  def backend(&block)
    backend_manager = BackendManager.new
    backend_manager.instance_eval(&block) if block_given?
      @rule[:backend] = backend_manager.backend
    end
  end
end

class BackendManager
  def initialize(backend)
    @backend = backend
  end
  
  attr_reader :backend
  
  def service_name(service_name)
    @backend[:serviceName] = service_name
  end
  
  def service_port(service_port)
    @backend[:servicePort] = service_port
  end
end

class TLSManager
  def initialize(tls)
    @tls = tls
  end
  
  attr_reader :tls
  
  def secret_name(secret_name)
    @tls[:secretName] = secret_name
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

class KubernetesPersistentVolume
  def initialize(name)
    @name = name
    @spec = {}
  end
  
  def storage_class(storage_class)
    @spec[:storageClassName] = storage_class
  end
  
  def access_modes(modes)
    @spec[:accessModes] = modes
  end
  
  def size(size)
    @spec[:capacity] = size
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
```

## 🔧 Chef Configuration Management

### 5. Chef Infrastructure

Infrastructure with Chef:

```ruby
class ChefInfrastructure
  def initialize
    @cookbooks = []
    @recipes = []
    @resources = []
    @attributes = {}
    @run_list = []
  end
  
  def cookbook(name, &block)
    cookbook = ChefCookbook.new(name)
    cookbook.instance_eval(&block) if block_given?
    @cookbooks << cookbook
    cookbook
  end
  
  def recipe(name, &block)
    recipe = ChefRecipe.new(name)
    recipe.instance_eval(&block) if block_given?
    @recipes << recipe
    recipe
  end
  
  def resource(type, name, &block)
    resource = ChefResource.new(type, name)
    resource.instance_eval(&block) if block_given?
    @resources << resource
    resource
  end
  
  def attributes(&block)
    attributes_manager = AttributesManager.new(@attributes)
    attributes_manager.instance_eval(&block) if block_given?
    @attributes = attributes_manager.attributes
  end
  
  def run(recipe_name)
    @run_list << recipe_name
  end
  
  def converge
    puts "Converging Chef infrastructure"
    puts "Cookbooks: #{@cookbooks.length}"
    puts "Recipes: #{@recipes.length}"
    puts "Resources: #{@resources.length}"
    
    # Execute recipes
    @run_list.each do |recipe_name|
      recipe = @recipes.find { |r| r.name == recipe_name }
      
      if recipe
        puts "Running recipe: #{recipe_name}"
        recipe.converge(@attributes, @resources)
      end
    end
    
    puts "Infrastructure converged successfully"
  end
  
  def self.demonstrate_chef_infrastructure
    puts "Chef Infrastructure Demonstration:"
    puts "=" * 50
    
    # Create infrastructure
    infrastructure = ChefInfrastructure.new
    
    # Define attributes
    infrastructure.attributes do
      node_name 'web-server-01'
      environment 'production'
      chef_environment 'production'
    end
    
    # Create web server cookbook
    infrastructure.cookbook('webserver') do
      recipe 'install_apache'
      recipe 'configure_apache'
      recipe 'start_apache'
      recipe 'create_website'
    end
    
    # Create web server recipe
    infrastructure.recipe('install_apache') do
      package 'apache2'
      service 'apache2' do
        action :install
        supports :start => true
      end
    end
    
    infrastructure.recipe('configure_apache') do
      template '/etc/apache2/sites-available/000-default.conf' do
          source 'apache_default.conf.erb'
          variables node_name: node_name
          port: 80
        end
        
        template '/etc/apache2/mods-available/ssl.conf' do
          source 'ssl.conf.erb'
          variables ssl_enabled: true
          cert_path: '/etc/ssl/certs'
        end
        
        execute 'a2ensite mod ssl'
        execute 'a2dissite mod headers'
      end
    end
    
    infrastructure.recipe('start_apache') do
      service 'apache2' do
        action [:start, :enable]
      end
    end
    
    infrastructure.recipe('create_website') do
      directory '/var/www/html' do
        owner 'www-data'
        group 'www-data'
        mode '0755'
      end
      
      file '/var/www/html/index.html' do
        content '<html><body><h1>Hello from Chef!</h1></body></html>'
        mode '0644'
      end
    end
    
    # Add resources
    infrastructure.resource('package', 'apache2') do
      action :install
      version '2.4.41'
    end
    
    infrastructure.resource('service', 'apache2') do
      action [:enable, :start]
      supports [:start, :restart, :stop, :reload]
    end
    
    # Converge infrastructure
    infrastructure.converge
    
    puts "\nChef Infrastructure Features:"
    puts "- Cookbook organization"
    "- Recipe management"
    "- Resource definition"
    "- Attribute management"
    "- Convergence process"
    "- Idempotent operations"
    "- Template management"
  end
  
  private
end

class ChefCookbook
  def initialize(name)
    @name = name
    @recipes = []
  end
  
  attr_reader :name
  
  def recipe(name, &block)
    recipe = ChefRecipe.new(name)
    recipe.instance_eval(&block) if block_given?
    @recipes << recipe
    recipe
  end
  
  def method_missing(name, *args, &block)
    recipe(name, *args, &block)
  end
end

class ChefRecipe
  def initialize(name)
    @name = name
    @resources = []
  end
  
  attr_reader :name
  
  def converge(attributes, resources)
    puts "Converging recipe: #{@name}"
    
    @resources.each do |resource|
      resource.converge(attributes)
    end
    
    puts "Recipe #{@name} converged"
  end
  
  def package(name, options = {})
    resource = ChefResource.new('package', name)
    resource.instance_eval(&block) if block_given?
    resource.options.merge!(options)
    @resources << resource
    resource
  end
  
  def service(name, options = {})
    resource = ChefResource.new('service', name)
    resource.instance_eval(&block) if block_given?
    resource.options.merge!(options)
    @resources << resource
    resource
  end
  
  def template(path, options = {})
    resource = ChefResource.new('template', path)
    resource.instance_eval(&block) if block_given?
      resource.options.merge!(options)
      @resources << resource
      resource
  end
  
  def file(path, options = {})
    resource = ChefResource.new('file', path)
    resource.instance_eval(&block) if block_given?
      resource.options.merge!(options)
      @resources << resource
      resource
  end
  
  def directory(path, options = {})
    resource = ChefResource.new('directory', path)
    resource.instance_eval(&block) if block_given?
      resource.options.merge!(options)
      @resources << resource
      resource
  end
  
  def execute(command, options = {})
    resource = ChefResource.new('execute', command)
    resource.instance_eval(&block) if block_given?
      resource.options.merge!(options)
      @resources << resource
      resource
  end
  
  def method_missing(name, *args, &block)
    resource(name, *args, &block)
  end
end

class ChefResource
  def initialize(type, name)
    @type = type
    @name = name
    @options = {}
    @converged = false
  end
  
  attr_reader :type, :name, :options, :converged
  
  def options(options)
    @options.merge!(options)
  end
  
  def converge(attributes)
    puts "  Converging #{@type} #{@name}"
    
    case @type
    when 'package'
      converge_package(attributes)
    when 'service'
      converge_service(attributes)
    when 'template'
      converge_template(attributes)
    when 'file'
      converge_file(attributes)
    when 'directory'
      converge_directory(attributes)
    when 'execute'
      converge_execute(attributes)
    end
    
    @converged = true
  end
  
  private
  
  def converge_package(attributes)
    puts "    Installing #{@options[:version] || 'latest'} #{@name}"
    puts "    Options: #{@options}"
    
    # Simulate package installation
    case @name
    when 'apache2'
      puts "    apt-get update && apt-get install -y apache2"
    when 'nginx'
      puts "    apt-get update && apt-get install -y nginx"
    end
  end
  
  def converge_service(attributes)
    puts "    Configuring service #{@name}"
    puts "    Action: #{@options[:action]}"
    
    # Simulate service configuration
    case @name
    when 'apache2'
      @options[:action].each do |action|
        puts "    systemctl #{action} apache2"
      end
    when 'nginx'
      @options[:action].each do |action|
        puts "    systemctl #{action} nginx"
      end
    end
  end
  
  def converge_template(attributes)
    puts "    Creating template #{name}"
    puts "    Source: #{@options[:source]}"
    puts "    Variables: #{attributes}"
    
    # Simulate template creation
    template_content = generate_template_content(@options[:source], attributes)
    puts "    Content: #{template_content[0..50]}..."
  end
  
  def converge_file(attributes)
    puts "    Creating file #{name}"
    puts "    Content: #{@options[:content]}"
    puts "    Mode: #{@options[:mode] || '0644'}"
    puts "    Owner: #{@options[:owner] || 'root'}"
    
    # Simulate file creation
    puts "    echo '#{@options[:content]}' > #{name}"
    puts "    chmod #{@options[:mode] || '0644'} #{name}"
  end
  
  def converge_directory(attributes)
    puts "    Creating directory #{name}"
    puts "    Mode: #{@options[:mode] || '0755'}"
    puts "    Owner: #{@options[:owner] || 'root'}"
    
    # Simulate directory creation
    puts "    mkdir -p #{name}"
    puts "    chmod #{@options[:mode] || '0755'} #{name}"
  end
  
  def converge_execute(attributes)
    puts "    Executing: #{@name}"
    puts "    Command: #{@options[:command]}"
    
    # Simulate command execution
    puts "    #{@options[:command]}"
  end
  
  def generate_template_content(source, variables)
    # Simple template substitution
    content = source
    
    variables.each do |key, value|
      content = content.gsub("{{#{key}}}", value.to_s)
    end
    
    content
  end
end

class AttributesManager
  def initialize(attributes)
    @attributes = attributes
  end
  
  attr_reader :attributes
  
  def method_missing(name, value)
    @attributes[name] = value
  end
end
```

## 📊 Monitoring and Logging

### 6. IaC Monitoring

Infrastructure monitoring and observability:

```ruby
class InfrastructureMonitor
  def initialize
    @metrics = {}
    @alerts = []
    @dashboards = {}
    @logs = []
    @thresholds = {
      resource_usage: 80,
      error_rate: 5,
      response_time: 5000,
      uptime: 99.9
    }
  end
  
  def track_resource_metrics(resource_type, resource_id, metrics)
    @metrics[resource_type] ||= {}
    @metrics[resource_type][resource_id] = {
      timestamp: Time.now,
      metrics: metrics
    }
    
    # Check thresholds
    check_thresholds(resource_type, resource_id, metrics)
    
    puts "Tracked metrics for #{resource_type}:#{resource_id}"
  end
  
  def track_event(event_type, details = {})
    event = {
      timestamp: @logs.length + 1,
      type: event_type,
      details: details,
      severity: details[:severity] || 'info'
    }
    
    @logs << event
    puts "Event logged: #{event_type} - #{details[:message]}"
    
    # Check for alerts
    if event[:severity] == 'critical'
      add_alert(event_type, details)
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
    puts "Alert added: #{alert_type} - #{details[:message]}"
  end
  
  def create_dashboard(name, &block)
    dashboard = InfrastructureDashboard.new(name)
    dashboard.instance_eval(&block) if block_given?
    @dashboards[name] = dashboard
    dashboard
  end
  
  def get_metrics(resource_type = nil, resource_id = nil)
    if resource_type && resource_id
      @metrics[resource_type][resource_id]
    elsif resource_type
      @metrics[resource_type]
    else
      @metrics
    end
  end
  
  def get_alerts(severity = nil)
    alerts = @alerts.select { |alert| alert[:resolved] == false }
    
    if severity
      alerts = alerts.select { |alert| alert[:severity] == severity }
    end
    
    alerts
  end
  
  def get_logs(event_type = nil, limit = 50)
    logs = @logs
    
    logs = logs.select { |log| log[:type] == event_type } } if event_type
    logs.first(limit)
  end
  
  def generate_report
    puts "Infrastructure Monitoring Report:"
    puts "=" * 50
    
    puts "Metrics Overview:"
    @metrics.each do |resource_type, resources|
      puts "#{resource_type}:"
      resources.each do |resource_id, data|
        metrics = data[:metrics]
        puts "  #{resource_id}:"
        metrics.each do |metric_name, value|
          puts "    #{metric_name}: #{value}"
        end
      end
    end
    
    puts "\nAlert Summary:"
    alert_counts = @alerts.group_by { |alert| alert[:severity] }.transform_values(&:count)
    
    alert_counts.each do |severity, count|
      puts "#{severity}: #{count} alerts"
    end
    
    puts "\nRecent Events:"
    recent_logs = get_logs(nil, 10)
    recent_logs.each do |log|
      severity_icon = log[:severity] == 'critical' ? '🚨' : '📝'
      puts "#{severity_icon} #{log[:type]}: #{log[:details][:message]}"
      puts "   Timestamp: #{log[:timestamp]}"
    end
    
    puts "\nMonitoring Features:"
    puts "- Resource metrics tracking"
    puts "- Event logging"
    puts "- Alert management"
    puts "- Dashboard creation"
    puts "- Threshold monitoring"
    puts "- Comprehensive reporting"
  end
  
  def self.demonstrate_monitoring
    puts "Infrastructure Monitoring Demonstration:"
    puts "=" * 50
    
    monitor = InfrastructureMonitor.new
    
    # Set thresholds
    monitor.set_threshold(:resource_usage, 85)
    monitor.set_threshold(:error_rate, 3)
    monitor.set_threshold(:response_time, 3000)
    
    # Track some metrics
    puts "Tracking infrastructure metrics:"
    
    monitor.track_resource_metrics('ec2_instance', 'i-123456789', {
      cpu_usage: 75.5,
      memory_usage: 68.2,
      disk_usage: 45.8,
      network_in: 1024,
      network_out: 512
    })
    
    monitor.track_resource_metrics('server', 'web-01', {
      load_average: 82.3,
      response_time: 250,
      error_rate: 0.5,
      uptime: 100
    })
    
    # Track events
    puts "\nTracking infrastructure events:"
    
    monitor.track_event('resource_created', {
      resource_type: 'ec2_instance',
      resource_id: 'i-123456789',
      message: 'EC2 instance created successfully',
      severity: 'info'
    })
    
    monitor.track_event('high_cpu_usage', {
      resource_type: 'ec2_instance',
      resource_id: 'i-123456789',
      message: 'CPU usage exceeded threshold',
      severity: 'warning'
    })
    
    monitor.track_event('service_down', {
      resource_type: 'server',
      resource_id: 'web-01',
      dashboard: 'production',
      message: 'Web server is down',
      severity: 'critical'
    })
    
    # Create dashboard
    puts "\nCreating monitoring dashboard:"
    dashboard = monitor.create_dashboard('production') do
      title 'Production Infrastructure'
      
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
      
      metric 'error_rate' do
        label 'Error Rate'
        unit '%'
        threshold 5
        color '#ff0000'
      end
      
      metric 'uptime' do
        label 'Uptime'
        unit '%'
        threshold 99.9
        color => '#00ff00'
      end
    end
    
    # Generate report
    puts "\nGenerating monitoring report:"
    report = monitor.generate_report
    
    puts "\nMonitoring Features:"
    puts "- Multi-resource tracking"
    puts "- Event logging and alerting"
    puts "- Threshold-based alerting"
    puts "- Dashboard creation"
    puts "- Comprehensive reporting"
    puts "- Historical data analysis"
    "- Real-time monitoring"
  end
  
  private
  
  def check_thresholds(resource_type, resource_id, metrics)
    metrics.each do |metric_name, value|
      threshold = @thresholds[metric_name]
      
      if threshold && value > threshold
        add_alert("#{resource_type}_#{metric_name}_threshold", {
          resource_type: resource_type,
          resource_id: resource_id,
          metric_name: metric_name,
          value: value,
          threshold: threshold,
          message: "#{metric_name} exceeded threshold"
        })
      end
    end
  end
  
  def set_threshold(metric_name, value)
    @thresholds[metric_name] = value
    puts "Set threshold: #{metric_name} = #{value}"
  end
end

class InfrastructureDashboard
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
      metrics: @values,
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
  
  def value(value)
    @value = value
  end
  
  def to_h
    {
      name: @name,
      label: @label,
      unit: @unit,
      threshold: @threshold,
      color: @color,
      value: @value
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

## 🎯 Exercises

### Beginner Exercises

1. **Basic IaC**: Create simple infrastructure definition
2. **Terraform**: Build Terraform provider
3. **Ansible**: Create Ansible playbook
4. **Kubernetes**: Deploy to Kubernetes cluster

### Intermediate Exercises

1. **Multi-Cloud IaC**: Support multiple cloud providers
2. **Advanced Terraform**: Complex infrastructure patterns
3. **Chef Recipes**: Infrastructure configuration
4. **Monitoring**: Comprehensive monitoring system

### Advanced Exercises

1. **Enterprise IaC**: Production-ready IaC system
2. **GitOps**: GitOps workflow implementation
3. **Auto-scaling**: Dynamic infrastructure scaling
4. **Compliance**: Compliance and audit trails

---

## 🎯 Summary

Infrastructure as Code in Ruby provides:

- **IaC Fundamentals** - Core concepts and principles
- **Terraform Implementation** - Multi-cloud provisioning
- **Configuration Management** - Ansible automation
- **Container Orchestration** - Kubernetes management
- **Chef Infrastructure** - Configuration management
- **Monitoring and Logging** - Observability and alerting

Master these IaC techniques for infrastructure automation!
