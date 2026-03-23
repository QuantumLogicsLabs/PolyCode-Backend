# Infrastructure as Code in Ruby
# This file demonstrates comprehensive Infrastructure as Code (IaC) implementations
# using Ruby, including Terraform, CloudFormation, and configuration management.

module DevOpsExamples
  module InfrastructureAsCode
    # 1. Terraform Ruby DSL
    # Infrastructure definition using Ruby DSL for Terraform
    
    class TerraformRuby
      def self.generate_infrastructure
        infrastructure = <<~RUBY
          # Terraform Infrastructure Definition in Ruby
          require 'json'
          
          module Terraform
            class Resource
              attr_reader :type, :name, :config
              
              def initialize(type, name, config = {})
                @type = type
                @name = name
                @config = config
              end
              
              def to_h
                {
                  "resource" => {
                    @type => {
                      @name.to_s => @config
                    }
                  }
                }
              end
              
              def to_json
                to_h.to_json
              end
            end
            
            class Variable
              attr_reader :name, :config
              
              def initialize(name, config = {})
                @name = name
                @config = config
              end
              
              def to_h
                {
                  "variable" => {
                    @name.to_s => @config
                  }
                }
              end
            end
            
            class Output
              attr_reader :name, :config
              
              def initialize(name, config = {})
                @name = name
                @config = config
              end
              
              def to_h
                {
                  "output" => {
                    @name.to_s => @config
                  }
                }
              end
            end
            
            class Provider
              attr_reader :name, :config
              
              def initialize(name, config = {})
                @name = name
                @config = config
              end
              
              def to_h
                {
                  "provider" => {
                    @name.to_s => @config
                  }
                }
              end
            end
          end
          
          # Infrastructure Definition
          class RubyAppInfrastructure
            def initialize
              @resources = []
              @variables = []
              @outputs = []
              @providers = []
            end
            
            def provider(name, config = {})
              @providers << Terraform::Provider.new(name, config)
              self
            end
            
            def variable(name, config = {})
              @variables << Terraform::Variable.new(name, config)
              self
            end
            
            def resource(type, name, config = {})
              @resources << Terraform::Resource.new(type, name, config)
              self
            end
            
            def output(name, config = {})
              @outputs << Terraform::Output.new(name, config)
              self
            end
            
            def to_terraform_json
              result = {}
              
              @providers.each { |provider| result.merge!(provider.to_h) }
              @variables.each { |variable| result.merge!(variable.to_h) }
              @resources.each { |resource| result.merge!(resource.to_h) }
              @outputs.each { |output| result.merge!(output.to_h) }
              
              result.to_json
            end
          end
          
          # Define Ruby application infrastructure
          infra = RubyAppInfrastructure.new
          
          # Provider configuration
          infra.provider('aws', {
            region: 'us-west-2',
            profile: 'default'
          })
          
          infra.provider('random', {})
          
          # Variables
          infra.variable('app_name', {
            type: 'string',
            description: 'Name of the Ruby application',
            default: 'ruby-app'
          })
          
          infra.variable('environment', {
            type: 'string',
            description: 'Environment (dev, staging, prod)',
            default: 'dev'
          })
          
          infra.variable('instance_type', {
            type: 'string',
            description: 'EC2 instance type',
            default: 't3.micro'
          })
          
          infra.variable('db_instance_class', {
            type: 'string',
            description: 'RDS instance class',
            default: 'db.t3.micro'
          })
          
          infra.variable('ssh_public_key', {
            type: 'string',
            description: 'SSH public key for EC2 instances'
          })
          
          # Random resources
          infra.resource('random_pet', 'db_password', {
            length: 16,
            special: true
          })
          
          infra.resource('random_id', 'unique_id', {
            byte_length: 4
          })
          
          # VPC and networking
          infra.resource('aws_vpc', 'main', {
            cidr_block: '10.0.0.0/16',
            enable_dns_hostnames: true,
            enable_dns_support: true,
            tags: {
              Name: 'var.app_name',
              Environment: 'var.environment'
            }
          })
          
          infra.resource('aws_subnet', 'public', {
            vpc_id: 'aws_vpc.main.id',
            cidr_block: '10.0.1.0/24',
            availability_zone: 'us-west-2a',
            map_public_ip_on_launch: true,
            tags: {
              Name: 'var.app_name-public',
              Environment: 'var.environment'
            }
          })
          
          infra.resource('aws_subnet', 'private', {
            vpc_id: 'aws_vpc.main.id',
            cidr_block: '10.0.2.0/24',
            availability_zone: 'us-west-2b',
            tags: {
              Name: 'var.app_name-private',
              Environment: 'var.environment'
            }
          })
          
          infra.resource('aws_internet_gateway', 'main', {
            vpc_id: 'aws_vpc.main.id',
            tags: {
              Name: 'var.app_name',
              Environment: 'var.environment'
            }
          })
          
          infra.resource('aws_route_table', 'public', {
            vpc_id: 'aws_vpc.main.id',
            route: [{
              cidr_block: '0.0.0.0/0',
              gateway_id: 'aws_internet_gateway.main.id'
            }],
            tags: {
              Name: 'var.app_name-public',
              Environment: 'var.environment'
            }
          })
          
          infra.resource('aws_route_table_association', 'public', {
            subnet_id: 'aws_subnet.public.id',
            route_table_id: 'aws_route_table.public.id'
          })
          
          # Security groups
          infra.resource('aws_security_group', 'web', {
            vpc_id: 'aws_vpc.main.id',
            ingress: [
              {
                description: 'HTTP',
                from_port: 80,
                to_port: 80,
                protocol: 'tcp',
                cidr_blocks: ['0.0.0.0/0']
              },
              {
                description: 'HTTPS',
                from_port: 443,
                to_port: 443,
                protocol: 'tcp',
                cidr_blocks: ['0.0.0.0/0']
              },
              {
                description: 'SSH',
                from_port: 22,
                to_port: 22,
                protocol: 'tcp',
                cidr_blocks: ['0.0.0.0/0']
              }
            ],
            egress: [
              {
                description: 'All outbound',
                from_port: 0,
                to_port: 0,
                protocol: '-1',
                cidr_blocks: ['0.0.0.0/0']
              }
            ],
            tags: {
              Name: 'var.app_name-web',
              Environment: 'var.environment'
            }
          })
          
          infra.resource('aws_security_group', 'db', {
            vpc_id: 'aws_vpc.main.id',
            ingress: [
              {
                description: 'PostgreSQL from web',
                from_port: 5432,
                to_port: 5432,
                protocol: 'tcp',
                security_groups: ['aws_security_group.web.id']
              }
            ],
            egress: [
              {
                description: 'All outbound',
                from_port: 0,
                to_port: 0,
                protocol: '-1',
                cidr_blocks: ['0.0.0.0/0']
              }
            ],
            tags: {
              Name: 'var.app_name-db',
              Environment: 'var.environment'
            }
          })
          
          # IAM role and instance profile
          infra.resource('aws_iam_role', 'ec2_role', {
            name: 'var.app_name-ec2-role',
            assume_role_policy: JSON.generate({
              Version: '2012-10-17',
              Statement: [{
                Effect: 'Allow',
                Principal: {
                  Service: 'ec2.amazonaws.com'
                },
                Action: 'sts:AssumeRole'
              }]
            })
          })
          
          infra.resource('aws_iam_role_policy', 'ec2_policy', {
            name: 'var.app_name-ec2-policy',
            role: 'aws_iam_role.ec2_role.id',
            policy: JSON.generate({
              Version: '2012-10-17',
              Statement: [
                {
                  Effect: 'Allow',
                  Action: [
                    'logs:CreateLogGroup',
                    'logs:CreateLogStream',
                    'logs:PutLogEvents',
                    'logs:DescribeLogStreams'
                  ],
                  Resource: 'arn:aws:logs:*:*:*'
                },
                {
                  Effect: 'Allow',
                  Action: [
                    'ssm:GetParameter',
                    'ssm:GetParameters'
                  ],
                  Resource: 'arn:aws:ssm:*:*:parameter/var.app_name/*'
                }
              ]
            })
          })
          
          infra.resource('aws_iam_instance_profile', 'ec2_profile', {
            name: 'var.app_name-ec2-profile',
            role: 'aws_iam_role.ec2_role.name'
          })
          
          # EC2 instance
          infra.resource('aws_instance', 'web', {
            ami: 'ami-0c55b159cbfafe1f0', # Ubuntu 20.04 LTS
            instance_type: 'var.instance_type',
            subnet_id: 'aws_subnet.public.id',
            vpc_security_group_ids: ['aws_security_group.web.id'],
            iam_instance_profile: 'aws_iam_instance_profile.ec2_profile.name',
            associate_public_ip_address: true,
            user_data: Base64.strict_encode64(<<~USERDATA),
              #!/bin/bash
              apt-get update
              apt-get install -y docker.io docker-compose
              usermod -aG docker ubuntu
              systemctl enable docker
              systemctl start docker
              
              # Install Ruby and dependencies
              apt-get install -y ruby ruby-dev build-essential
              gem install bundler
              
              # Pull and run application
              docker pull my-ruby-app:latest
              docker run -d -p 80:3000 --name ruby-app my-ruby-app:latest
            USERDATA
            tags: {
              Name: 'var.app_name-web',
              Environment: 'var.environment'
            }
          })
          
          # RDS database
          infra.resource('aws_db_instance', 'postgres', {
            identifier: 'var.app_name-db',
            engine: 'postgres',
            engine_version: '15.4',
            instance_class: 'var.db_instance_class',
            allocated_storage: 20,
            storage_type: 'gp2',
            db_name: 'var.app_name',
            username: 'postgres',
            password: 'random_pet.db_password.result',
            vpc_security_group_ids: ['aws_security_group.db.id'],
            db_subnet_group_name: 'aws_db_subnet_group.main.name',
            skip_final_snapshot: true,
            tags: {
              Name: 'var.app_name-db',
              Environment: 'var.environment'
            }
          })
          
          infra.resource('aws_db_subnet_group', 'main', {
            name: 'var.app_name-db-subnet-group',
            description: 'Database subnet group for var.app_name',
            subnet_ids: ['aws_subnet.private.id']
          })
          
          # S3 bucket for assets
          infra.resource('aws_s3_bucket', 'assets', {
            bucket: 'var.app_name-assets-random_id.unique_id.hex',
            acl: 'private',
            versioning: {
              enabled: true
            },
            lifecycle_rule: [{
              id: 'log_cleanup',
              enabled: true,
              transition: [{
                days: 30,
                storage_class: 'STANDARD_IA'
              }, {
                days: 60,
                storage_class: 'GLACIER'
              }],
              expiration: {
                days: 365
              }
            }],
            tags: {
              Name: 'var.app_name-assets',
              Environment: 'var.environment'
            }
          })
          
          # CloudWatch log group
          infra.resource('aws_cloudwatch_log_group', 'app_logs', {
            name: '/aws/ec2/var.app_name',
            retention_in_days: 30
          })
          
          # Outputs
          infra.output('instance_public_ip', {
            description: 'Public IP of the EC2 instance',
            value: 'aws_instance.web.public_ip'
          })
          
          infra.output('database_endpoint', {
            description: 'RDS database endpoint',
            value: 'aws_db_instance.postgres.endpoint'
          })
          
          infra.output('assets_bucket_name', {
            description: 'S3 bucket name for assets',
            value: 'aws_s3_bucket.assets.bucket'
          })
          
          # Generate Terraform JSON
          File.write('infrastructure.tf.json', infra.to_terraform_json)
          puts "Terraform infrastructure definition generated!"
          
          infra
        RUBY
        
        infrastructure
      end
    end
    
    # 2. AWS CloudFormation Ruby DSL
    # Infrastructure definition using Ruby DSL for CloudFormation
    
    class CloudFormationRuby
      def self.generate_template
        template = <<~RUBY
          # AWS CloudFormation Template in Ruby
          require 'json'
          
          module CloudFormation
            class Template
              attr_reader :resources, :parameters, :outputs, :mappings, :conditions
              
              def initialize
                @resources = {}
                @parameters = {}
                @outputs = {}
                @mappings = {}
                @conditions = {}
              end
              
              def parameter(name, properties = {})
                @parameters[name] = {
                  Type: properties[:type] || 'String',
                  Default: properties[:default],
                  Description: properties[:description],
                  AllowedValues: properties[:allowed_values],
                  MinLength: properties[:min_length],
                  MaxLength: properties[:max_length],
                  MinValue: properties[:min_value],
                  MaxValue: properties[:max_value]
                }.compact
              end
              
              def resource(name, type, properties = {})
                @resources[name] = {
                  Type: type,
                  Properties: properties,
                  DependsOn: properties.delete(:DependsOn),
                  Condition: properties.delete(:Condition),
                  Metadata: properties.delete(:Metadata)
                }.compact
              end
              
              def output(name, properties = {})
                @outputs[name] = {
                  Description: properties[:description],
                  Value: properties[:value],
                  Export: properties[:export]
                }.compact
              end
              
              def mapping(name, mapping)
                @mappings[name] = mapping
              end
              
              def condition(name, expression)
                @conditions[name] = expression
              end
              
              def ref(name)
                { 'Ref' => name }
              end
              
              def get_att(resource, attribute)
                { 'Fn::GetAtt' => [resource, attribute] }
              end
              
              def join(delimiter, values)
                { 'Fn::Join' => [delimiter, values] }
              end
              
              def base64(value)
                { 'Fn::Base64' => value }
              end
              
              def import_value(value)
                { 'Fn::ImportValue' => value }
              end
              
              def find_in_map(map_name, top_level_key, second_level_key)
                { 'Fn::FindInMap' => [map_name, top_level_key, second_level_key] }
              end
              
              def to_json
                {
                  AWSTemplateFormatVersion: '2010-09-09',
                  Description: 'Ruby application infrastructure',
                  Parameters: @parameters,
                  Mappings: @mappings,
                  Conditions: @conditions,
                  Resources: @resources,
                  Outputs: @outputs
                }.to_json
              end
            end
          end
          
          # Define CloudFormation template
          template = CloudFormation::Template.new
          
          # Parameters
          template.parameter('AppName', {
            type: 'String',
            description: 'Name of the Ruby application',
            default: 'ruby-app',
            min_length: 1,
            max_length: 64
          })
          
          template.parameter('Environment', {
            type: 'String',
            description: 'Environment (dev, staging, prod)',
            default: 'dev',
            allowed_values: ['dev', 'staging', 'prod']
          })
          
          template.parameter('InstanceType', {
            type: 'String',
            description: 'EC2 instance type',
            default: 't3.micro',
            allowed_values: ['t3.micro', 't3.small', 't3.medium', 't3.large']
          })
          
          template.parameter('DBInstanceClass', {
            type: 'String',
            description: 'RDS instance class',
            default: 'db.t3.micro',
            allowed_values: ['db.t3.micro', 'db.t3.small', 'db.t3.medium']
          })
          
          template.parameter('DBPassword', {
            type: 'String',
            description: 'Database password',
            no_echo: true,
            min_length: 8,
            max_length: 41
          })
          
          template.parameter('SSHKeyPairName', {
            type: 'AWS::EC2::KeyPair::KeyName',
            description: 'SSH key pair name for EC2 instances'
          })
          
          template.parameter('VpcCidr', {
            type: 'String',
            description: 'CIDR block for VPC',
            default: '10.0.0.0/16'
          })
          
          # Mappings
          template.mapping('AWSInstanceType2Arch', {
            't3.micro' => { 'Arch' => 'HVM64' },
            't3.small' => { 'Arch' => 'HVM64' },
            't3.medium' => { 'Arch' => 'HVM64' },
            't3.large' => { 'Arch' => 'HVM64' }
          })
          
          template.mapping('AWSRegionArch2AMI', {
            'us-west-2' => {
              'HVM64' => 'ami-0c55b159cbfafe1f0'
            },
            'us-east-1' => {
              'HVM64' => 'ami-0c02fb55956c7d316'
            }
          })
          
          # Conditions
          template.condition('IsProduction', {
            'Fn::Equals' => [
              template.ref('Environment'),
              'prod'
            ]
          })
          
          template.condition('CreateProductionResources', {
            'Fn::Equals' => [
              template.ref('Environment'),
              'prod'
            ]
          })
          
          # Resources
          template.resource('VPC', 'AWS::EC2::VPC', {
            CidrBlock: template.ref('VpcCidr'),
            EnableDnsHostnames: true,
            EnableDnsSupport: true,
            Tags: [
              {
                Key: 'Name',
                Value: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'vpc'])
              },
              {
                Key: 'Environment',
                Value: template.ref('Environment')
              }
            ]
          })
          
          template.resource('PublicSubnet', 'AWS::EC2::Subnet', {
            VpcId: template.ref('VPC'),
            CidrBlock: template.join('', [template.find_in_map('VpcCidr', 'Public', 'CIDR'), '/24']),
            AvailabilityZone: template.select(0, template.get_azs('', 'AWS::Region')),
            MapPublicIpOnLaunch: true,
            Tags: [
              {
                Key: 'Name',
                Value: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'public'])
              },
              {
                Key: 'Environment',
                Value: template.ref('Environment')
              }
            ]
          })
          
          template.resource('PrivateSubnet', 'AWS::EC2::Subnet', {
            VpcId: template.ref('VPC'),
            CidrBlock: template.join('', [template.find_in_map('VpcCidr', 'Private', 'CIDR'), '/24']),
            AvailabilityZone: template.select(1, template.get_azs('', 'AWS::Region')),
            Tags: [
              {
                Key: 'Name',
                Value: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'private'])
              },
              {
                Key: 'Environment',
                Value: template.ref('Environment')
              }
            ]
          })
          
          template.resource('InternetGateway', 'AWS::EC2::InternetGateway', {
            Tags: [
              {
                Key: 'Name',
                Value: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'igw'])
              },
              {
                Key: 'Environment',
                Value: template.ref('Environment')
              }
            ]
          })
          
          template.resource('VPCGatewayAttachment', 'AWS::EC2::VPCGatewayAttachment', {
            VpcId: template.ref('VPC'),
            InternetGatewayId: template.ref('InternetGateway')
          })
          
          template.resource('PublicRouteTable', 'AWS::EC2::RouteTable', {
            VpcId: template.ref('VPC'),
            Tags: [
              {
                Key: 'Name',
                Value: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'public-rt'])
              },
              {
                Key: 'Environment',
                Value: template.ref('Environment')
              }
            ]
          })
          
          template.resource('PublicRoute', 'AWS::EC2::Route', {
            RouteTableId: template.ref('PublicRouteTable'),
            DestinationCidrBlock: '0.0.0.0/0',
            GatewayId: template.ref('InternetGateway')
          })
          
          template.resource('PublicSubnetRouteTableAssociation', 'AWS::EC2::SubnetRouteTableAssociation', {
            SubnetId: template.ref('PublicSubnet'),
            RouteTableId: template.ref('PublicRouteTable')
          })
          
          template.resource('WebSecurityGroup', 'AWS::EC2::SecurityGroup', {
            GroupDescription: 'Security group for web servers',
            VpcId: template.ref('VPC'),
            SecurityGroupIngress: [
              {
                IpProtocol: 'tcp',
                FromPort: 80,
                ToPort: 80,
                CidrIp: '0.0.0.0/0'
              },
              {
                IpProtocol: 'tcp',
                FromPort: 443,
                ToPort: 443,
                CidrIp: '0.0.0.0/0'
              },
              {
                IpProtocol: 'tcp',
                FromPort: 22,
                ToPort: 22,
                CidrIp: '0.0.0.0/0'
              }
            ],
            SecurityGroupEgress: [
              {
                IpProtocol: '-1',
                CidrIp: '0.0.0.0/0'
              }
            ],
            Tags: [
              {
                Key: 'Name',
                Value: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'web'])
              },
              {
                Key: 'Environment',
                Value: template.ref('Environment')
              }
            ]
          })
          
          template.resource('DatabaseSecurityGroup', 'AWS::EC2::SecurityGroup', {
            GroupDescription: 'Security group for database',
            VpcId: template.ref('VPC'),
            SecurityGroupIngress: [
              {
                IpProtocol: 'tcp',
                FromPort: 5432,
                ToPort: 5432,
                SourceSecurityGroupId: template.ref('WebSecurityGroup')
              }
            ],
            Tags: [
              {
                Key: 'Name',
                Value: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'db'])
              },
              {
                Key: 'Environment',
                Value: template.ref('Environment')
              }
            ]
          })
          
          template.resource('WebInstance', 'AWS::EC2::Instance', {
            InstanceType: template.ref('InstanceType'),
            ImageId: template.find_in_map(
              'AWSRegionArch2AMI',
              template.find_in_map('AWSInstanceType2Arch', template.ref('InstanceType'), 'Arch'),
              template.ref('AWS::Region')
            ),
            SubnetId: template.ref('PublicSubnet'),
            SecurityGroupIds: [template.ref('WebSecurityGroup')],
            KeyName: template.ref('SSHKeyPairName'),
            UserData: template.base64(template.join('', [
              '#!/bin/bash -xe',
              'yum update -y',
              'yum install -y docker',
              'service docker start',
              'usermod -a -G docker ec2-user',
              'docker run -d -p 80:3000 --name ruby-app my-ruby-app:latest'
            ])),
            Tags: [
              {
                Key: 'Name',
                Value: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'web'])
              },
              {
                Key: 'Environment',
                Value: template.ref('Environment')
              }
            ],
            DependsOn: ['VPCGatewayAttachment']
          })
          
          template.resource('DatabaseSubnetGroup', 'AWS::RDS::DBSubnetGroup', {
            DBSubnetGroupDescription: 'Subnet group for RDS database',
            SubnetIds: [template.ref('PrivateSubnet')],
            Tags: [
              {
                Key: 'Name',
                Value: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'db-subnet-group'])
              },
              {
                Key: 'Environment',
                Value: template.ref('Environment')
              }
            ]
          })
          
          template.resource('Database', 'AWS::RDS::DBInstance', {
            DBInstanceIdentifier: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'db']),
            DBInstanceClass: template.ref('DBInstanceClass'),
            Engine: 'postgres',
            EngineVersion: '15.4',
            AllocatedStorage: 20,
            StorageType: 'gp2',
            DBName: template.ref('AppName'),
            MasterUsername: 'postgres',
            MasterUserPassword: template.ref('DBPassword'),
            DBSubnetGroupName: template.ref('DatabaseSubnetGroup'),
            VPCSecurityGroups: [template.ref('DatabaseSecurityGroup')],
            BackupRetentionPeriod: template.condition('IsProduction') ? 7 : 0,
            MultiAZ: template.condition('IsProduction'),
            StorageEncrypted: template.condition('IsProduction'),
            Tags: [
              {
                Key: 'Name',
                Value: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'db'])
              },
              {
                Key: 'Environment',
                Value: template.ref('Environment')
              }
            ]
          })
          
          template.resource('AssetsBucket', 'AWS::S3::Bucket', {
            BucketName: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'assets', template.select(0, template.split('', template.ref('AWS::AccountId'), '-'))]),
            VersioningConfiguration: {
              Status: 'Enabled'
            },
            LifecycleConfiguration: {
              Rules: [
                {
                  Status: 'Enabled',
                  ExpirationInDays: 365,
                  Id: 'DeleteOldObjects'
                }
              ]
            },
            Tags: [
              {
                Key: 'Name',
                Value: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'assets'])
              },
              {
                Key: 'Environment',
                Value: template.ref('Environment')
              }
            ]
          })
          
          template.resource('ApplicationLoadBalancer', 'AWS::ElasticLoadBalancingV2::LoadBalancer', {
            Name: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'alb']),
            Scheme: 'internet-facing',
            Type: 'application',
            Subnets: [template.ref('PublicSubnet')],
            SecurityGroups: [template.ref('WebSecurityGroup')],
            Tags: [
              {
                Key: 'Name',
                Value: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'alb'])
              },
              {
                Key: 'Environment',
                Value: template.ref('Environment')
              }
            ]
          })
          
          template.resource('TargetGroup', 'AWS::ElasticLoadBalancingV2::TargetGroup', {
            Name: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'tg']),
            Port: 3000,
            Protocol: 'HTTP',
            VpcId: template.ref('VPC'),
            HealthCheckIntervalSeconds: 30,
            HealthCheckPath: '/health',
            HealthCheckProtocol: 'HTTP',
            HealthCheckTimeoutSeconds: 5,
            HealthyThresholdCount: 2,
            UnhealthyThresholdCount: 2,
            Matcher: {
              HttpCode: '200'
            },
            Tags: [
              {
                Key: 'Name',
                Value: template.join('-', [template.ref('AppName'), template.ref('Environment'), 'tg'])
              },
              {
                Key: 'Environment',
                Value: template.ref('Environment')
              }
            ]
          })
          
          # Outputs
          template.output('WebInstancePublicIP', {
            description: 'Public IP address of the web server',
            value: template.get_att('WebInstance', 'PublicIp')
          })
          
          template.output('DatabaseEndpoint', {
            description: 'RDS database endpoint',
            value: template.get_att('Database', 'Endpoint.Address')
          })
          
          template.output('AssetsBucketName', {
            description: 'Name of the S3 bucket for assets',
            value: template.ref('AssetsBucket')
          })
          
          template.output('LoadBalancerDNS', {
            description: 'DNS name of the load balancer',
            value: template.get_att('ApplicationLoadBalancer', 'DNSName')
          })
          
          # Generate CloudFormation template
          File.write('infrastructure.json', template.to_json)
          puts "CloudFormation template generated!"
          
          template
        RUBY
        
        template
      end
    end
    
    # 3. Docker Compose Ruby DSL
    # Docker Compose configuration using Ruby DSL
    
    class DockerComposeRuby
      def self.generate_compose_file
        compose = <<~RUBY
          # Docker Compose Configuration in Ruby
          require 'yaml'
          
          module DockerCompose
            class ComposeFile
              attr_reader :version, :services, :networks, :volumes, :secrets, :configs
              
              def initialize(version: '3.8')
                @version = version
                @services = {}
                @networks = {}
                @volumes = {}
                @secrets = {}
                @configs = {}
              end
              
              def service(name, config = {})
                @services[name] = {
                  image: config[:image],
                  build: config[:build],
                  command: config[:command],
                  environment: config[:environment],
                  ports: config[:ports],
                  volumes: config[:volumes],
                  depends_on: config[:depends_on],
                  networks: config[:networks],
                  secrets: config[:secrets],
                  configs: config[:configs],
                  restart: config[:restart],
                  healthcheck: config[:healthcheck],
                  deploy: config[:deploy],
                  labels: config[:labels]
                }.compact
              end
              
              def network(name, config = {})
                @networks[name] = {
                  driver: config[:driver],
                  driver_opts: config[:driver_opts],
                  ipam: config[:ipam],
                  external: config[:external]
                }.compact
              end
              
              def volume(name, config = {})
                @volumes[name] = {
                  driver: config[:driver],
                  driver_opts: config[:driver_opts],
                  external: config[:external]
                }.compact
              end
              
              def secret(name, config = {})
                @secrets[name] = {
                  file: config[:file],
                  external: config[:external]
                }.compact
              end
              
              def config(name, config = {})
                @configs[name] = {
                  file: config[:file],
                  external: config[:external]
                }.compact
              end
              
              def to_yaml
                {
                  version: @version,
                  services: @services,
                  networks: @networks,
                  volumes: @volumes,
                  secrets: @secrets,
                  configs: @configs
                }.compact.to_yaml
              end
            end
          end
          
          # Define Docker Compose configuration
          compose = DockerCompose::ComposeFile.new
          
          # Networks
          compose.network('app-network', {
            driver: 'bridge'
          })
          
          compose.network('db-network', {
            driver: 'bridge',
            internal: true
          })
          
          # Volumes
          compose.volume('postgres-data', {
            driver: 'local'
          })
          
          compose.volume('redis-data', {
            driver: 'local'
          })
          
          compose.volume('app-storage', {
            driver: 'local'
          })
          
          # Secrets
          compose.secret('db-password', {
            file: './secrets/db-password.txt'
          })
          
          compose.secret('app-secrets', {
            file: './secrets/app-secrets.txt'
          })
          
          # Services
          compose.service('app', {
            build: {
              context: '.',
              dockerfile: 'Dockerfile'
            },
            command: 'bundle exec rails server -b 0.0.0.0',
            environment: {
              'RAILS_ENV' => 'production',
              'DATABASE_URL' => 'postgresql://postgres:$(cat /run/secrets/db-password)@db:5432/rubyapp_production',
              'REDIS_URL' => 'redis://redis:6379/0',
              'SECRET_KEY_BASE' => '$(cat /run/secrets/app-secrets)',
              'RAILS_LOG_TO_STDOUT' => 'true',
              'RAILS_SERVE_STATIC_FILES' => 'true'
            },
            ports: ['3000:3000'],
            volumes: [
              'app-storage:/app/storage',
              './log:/app/log'
            ],
            depends_on: ['db', 'redis'],
            networks: ['app-network'],
            secrets: ['db-password', 'app-secrets'],
            restart: 'unless-stopped',
            healthcheck: {
              test: ['CMD', 'curl', '-f', 'http://localhost:3000/health'],
              interval: '30s',
              timeout: '10s',
              retries: 3,
              start_period: '40s'
            },
            deploy: {
              replicas: 2,
              resources: {
                limits: {
                  cpus: '0.5',
                  memory: '512M'
                },
                reservations: {
                  cpus: '0.25',
                  memory: '256M'
                }
              },
              restart_policy: {
                condition: 'on-failure',
                delay: '5s',
                max_attempts: 3
              }
            },
            labels: {
              'traefik.enable' => 'true',
              'traefik.http.routers.app.rule' => 'Host(`ruby-app.local`)'
            }
          })
          
          compose.service('db', {
            image: 'postgres:15-alpine',
            environment: {
              'POSTGRES_DB' => 'rubyapp_production',
              'POSTGRES_USER' => 'postgres',
              'POSTGRES_PASSWORD_FILE' => '/run/secrets/db-password'
            },
            volumes: ['postgres-data:/var/lib/postgresql/data'],
            networks: ['db-network'],
            secrets: ['db-password'],
            restart: 'unless-stopped',
            healthcheck: {
              test: ['CMD-SHELL', 'pg_isready -U postgres'],
              interval: '10s',
              timeout: '5s',
              retries: 5
            },
            deploy: {
              resources: {
                limits: {
                  cpus: '0.5',
                  memory: '1G'
                },
                reservations: {
                  cpus: '0.25',
                  memory: '512M'
                }
              }
            }
          })
          
          compose.service('redis', {
            image: 'redis:7-alpine',
            command: 'redis-server --appendonly yes',
            volumes: ['redis-data:/data'],
            networks: ['app-network'],
            restart: 'unless-stopped',
            healthcheck: {
              test: ['CMD', 'redis-cli', 'ping'],
              interval: '10s',
              timeout: '3s',
              retries: 3
            },
            deploy: {
              resources: {
                limits: {
                  cpus: '0.25',
                  memory: '256M'
                },
                reservations: {
                  cpus: '0.1',
                  memory: '128M'
                }
              }
            }
          })
          
          compose.service('nginx', {
            image: 'nginx:alpine',
            ports: ['80:80', '443:443'],
            volumes: [
              './nginx/nginx.conf:/etc/nginx/nginx.conf',
              './nginx/ssl:/etc/nginx/ssl',
              './public:/app/public'
            ],
            depends_on: ['app'],
            networks: ['app-network'],
            restart: 'unless-stopped',
            deploy: {
              resources: {
                limits: {
                  cpus: '0.25',
                  memory: '128M'
                }
              }
            }
          })
          
          compose.service('sidekiq', {
            build: {
              context: '.',
              dockerfile: 'Dockerfile'
            },
            command: 'bundle exec sidekiq',
            environment: {
              'RAILS_ENV' => 'production',
              'DATABASE_URL' => 'postgresql://postgres:$(cat /run/secrets/db-password)@db:5432/rubyapp_production',
              'REDIS_URL' => 'redis://redis:6379/0',
              'SECRET_KEY_BASE' => '$(cat /run/secrets/app-secrets)'
            },
            volumes: ['app-storage:/app/storage'],
            depends_on: ['db', 'redis'],
            networks: ['app-network'],
            secrets: ['db-password', 'app-secrets'],
            restart: 'unless-stopped',
            deploy: {
              replicas: 1,
              resources: {
                limits: {
                  cpus: '0.25',
                  memory: '256M'
                }
              }
            }
          })
          
          compose.service('elasticsearch', {
            image: 'elasticsearch:8.8.0',
            environment: {
              'discovery.type' => 'single-node',
              'ES_JAVA_OPTS' => '-Xms512m -Xmx512m',
              'xpack.security.enabled' => 'false'
            },
            volumes: ['elasticsearch-data:/usr/share/elasticsearch/data'],
            networks: ['app-network'],
            restart: 'unless-stopped',
            deploy: {
              resources: {
                limits: {
                  cpus: '1.0',
                  memory: '1G'
                }
              }
            }
          })
          
          compose.service('kibana', {
            image: 'kibana:8.8.0',
            environment: {
              'ELASTICSEARCH_HOSTS' => 'http://elasticsearch:9200'
            },
            ports: ['5601:5601'],
            depends_on: ['elasticsearch'],
            networks: ['app-network'],
            restart: 'unless-stopped',
            deploy: {
              resources: {
                limits: {
                  cpus: '0.25',
                  memory: '512M'
                }
              }
            }
          })
          
          # Development override
          if ENV['RAILS_ENV'] == 'development'
            compose.service('app', {
              environment: {
                'RAILS_ENV' => 'development',
                'DATABASE_URL' => 'postgresql://postgres:postgres@db:5432/rubyapp_development'
              },
              volumes: [
                '.:/app',
                'bundle_cache:/usr/local/bundle'
              ],
              command: 'bundle exec rails server -b 0.0.0.0 -p 3000'
            })
            
            compose.volume('bundle_cache', {
              driver: 'local'
            })
          end
          
          # Generate Docker Compose file
          File.write('docker-compose.yml', compose.to_yaml)
          puts "Docker Compose configuration generated!"
          
          compose
        RUBY
        
        compose
      end
    end
    
    # 4. Ansible Playbooks in Ruby
    # Configuration management using Ruby DSL for Ansible
    
    class AnsibleRuby
      def self.generate_playbook
        playbook = <<~RUBY
          # Ansible Playbook Configuration in Ruby
          require 'yaml'
          
          module Ansible
            class Playbook
              attr_reader :name, :hosts, :vars, :tasks, :handlers, :roles
              
              def initialize(name)
                @name = name
                @hosts = 'all'
                @vars = {}
                @tasks = []
                @handlers = []
                @roles = []
              end
              
              def hosts(hosts)
                @hosts = hosts
                self
              end
              
              def vars(variables)
                @vars.merge!(variables)
                self
              end
              
              def task(name, module_name, args = {})
                @tasks << {
                  name: name,
                  module: module_name,
                  args: args
                }
                self
              end
              
              def handler(name, module_name, args = {})
                @handlers << {
                  name: name,
                  module: module_name,
                  args: args
                }
                self
              end
              
              def role(role_name)
                @roles << role_name
                self
              end
              
              def to_yaml
                {
                  name: @name,
                  hosts: @hosts,
                  vars: @vars,
                  tasks: @tasks.map { |task| task_to_hash(task) },
                  handlers: @handlers.map { |handler| handler_to_hash(handler) },
                  roles: @roles
                }.to_yaml
              end
              
              private
              
              def task_to_hash(task)
                {
                  name: task[:name],
                  task[:module].merge(task[:args] || {})
                }
              end
              
              def handler_to_hash(handler)
                {
                  name: handler[:name],
                  'listen' => handler[:listen] || 'always',
                  handler[:module].merge(handler[:args] || {})
                }
              end
            end
          end
          
          # Define Ansible playbook for Ruby application deployment
          playbook = Ansible::Playbook.new('Deploy Ruby Application')
          
          playbook.hosts('webservers')
          playbook.vars({
            'app_name' => 'ruby-app',
            'app_user' => 'deploy',
            'app_path' => '/var/www/ruby-app',
            'ruby_version' => '3.2',
            'rails_env' => 'production',
            'git_repo' => 'https://github.com/user/ruby-app.git',
            'git_branch' => 'main'
          })
          
          # System setup tasks
          playbook.task('Update system packages', 'apt', {
            update_cache: 'yes',
            upgrade: 'dist'
          })
          
          playbook.task('Install required packages', 'apt', {
            name: [
              'curl',
              'wget',
              'git',
              'build-essential',
              'libssl-dev',
              'libreadline-dev',
              'zlib1g-dev',
              'libncurses5-dev',
              'libffi-dev',
              'postgresql-client',
              'nodejs',
              'npm'
            ],
            state: 'present'
          })
          
          # User setup
          playbook.task('Create application user', 'user', {
            name: '{{ app_user }}',
            shell: '/bin/bash',
            home: '/home/{{ app_user }}',
            create_home: 'yes',
            state: 'present'
          })
          
          playbook.task('Add user to docker group', 'user', {
            name: '{{ app_user }}',
            groups: 'docker',
            append: 'yes',
            state: 'present'
          })
          
          # Application directory setup
          playbook.task('Create application directory', 'file', {
            path: '{{ app_path }}',
            state: 'directory',
            owner: '{{ app_user }}',
            group: '{{ app_user }}',
            mode: '0755'
          })
          
          playbook.task('Create shared directories', 'file', {
            path: '{{ item }}',
            state: 'directory',
            owner: '{{ app_user }}',
            group: '{{ app_user }}',
            mode: '0755'
          })
          playbook.vars({
            'shared_dirs' => [
              '{{ app_path }}/shared/log',
              '{{ app_path }}/shared/tmp/pids',
              '{{ app_path }}/shared/cache',
              '{{ app_path }}/shared/sockets',
              '{{ app_path }}/shared/bundle'
            ]
          })
          
          # Ruby installation
          playbook.task('Download Ruby installer', 'get_url', {
            url: 'https://github.com/rbenv/rbenv-installer/raw/master/bin/rbenv-installer',
            dest: '/tmp/rbenv-installer',
            mode: '0755'
          })
          
          playbook.task('Install rbenv', 'shell', {
            cmd: '/tmp/rbenv-installer',
            creates: '/usr/local/bin/rbenv'
          })
          
          playbook.task('Add rbenv to system profile', 'lineinfile', {
            path: '/etc/profile.d/rbenv.sh',
            line: 'export PATH="$HOME/.rbenv/bin:$PATH"',
            create: 'yes',
            state: 'present'
          })
          
          playbook.task('Install Ruby', 'shell', {
            cmd: 'rbenv install {{ ruby_version }}',
            creates: '/usr/local/rbenv/versions/{{ ruby_version }}/bin/ruby'
          })
          
          playbook.task('Set global Ruby version', 'shell', {
            cmd: 'rbenv global {{ ruby_version }}'
          })
          
          playbook.task('Install bundler', 'shell', {
            cmd: 'gem install bundler --no-document',
            creates: '/usr/local/rbenv/shims/bundle'
          })
          
          # Application deployment
          playbook.task('Clone application repository', 'git', {
            repo: '{{ git_repo }}',
            dest: '{{ app_path }}',
            version: '{{ git_branch }}',
            force: 'yes',
            owner: '{{ app_user }}',
            group: '{{ app_user }}'
          })
          
          playbook.task('Install application dependencies', 'shell', {
            cmd: 'bundle install --deployment --without development test',
            chdir: '{{ app_path }}',
            creates: '{{ app_path }}/vendor/bundle'
          })
          
          playbook.task('Install Node.js dependencies', 'shell', {
            cmd: 'npm install',
            chdir: '{{ app_path }}',
            creates: '{{ app_path }}/node_modules'
          })
          
          playbook.task('Precompile assets', 'shell', {
            cmd: 'bundle exec rails assets:precompile',
            chdir: '{{ app_path }}',
            environment: {
              'RAILS_ENV' => '{{ rails_env }}',
              'SECRET_KEY_BASE' => 'dummy'
            }
          })
          
          playbook.task('Create database configuration', 'template', {
            src: 'config/database.yml.j2',
            dest: '{{ app_path }}/config/database.yml',
            owner: '{{ app_user }}',
            group: '{{ app_user }}',
            mode: '0600'
          })
          
          playbook.task('Create environment variables', 'template', {
            src: 'env.j2',
            dest: '{{ app_path }}/.env',
            owner: '{{ app_user }}',
            group: '{{ app_user }}',
            mode: '0600'
          })
          
          # Systemd service setup
          playbook.task('Create systemd service file', 'template', {
            src: 'ruby-app.service.j2',
            dest: '/etc/systemd/system/ruby-app.service',
            mode: '0644'
          })
          
          playbook.task('Enable and start Ruby application', 'systemd', {
            name: 'ruby-app',
            enabled: 'yes',
            state: 'restarted'
          })
          
          # Nginx setup
          playbook.task('Install Nginx', 'apt', {
            name: 'nginx',
            state: 'present'
          })
          
          playbook.task('Configure Nginx', 'template', {
            src: 'nginx/ruby-app.conf.j2',
            dest: '/etc/nginx/sites-available/ruby-app',
            mode: '0644'
          })
          
          playbook.task('Enable Nginx site', 'file', {
            src: '/etc/nginx/sites-available/ruby-app',
            dest: '/etc/nginx/sites-enabled/ruby-app',
            state: 'link'
          })
          
          playbook.task('Remove default Nginx site', 'file', {
            path: '/etc/nginx/sites-enabled/default',
            state: 'absent'
          })
          
          playbook.task('Restart Nginx', 'systemd', {
            name: 'nginx',
            state: 'restarted'
          })
          
          # SSL certificate setup
          playbook.task('Create SSL directory', 'file', {
            path: '/etc/nginx/ssl',
            state: 'directory',
            mode: '0755'
          })
          
          playbook.task('Generate self-signed SSL certificate', 'shell', {
            cmd: 'openssl req -x509 -nodes -days 365 -newkey rsa:2048 \\
                  -keyout /etc/nginx/ssl/ruby-app.key \\
                  -out /etc/nginx/ssl/ruby-app.crt \\
                  -subj "/C=US/ST=State/L=City/O=Organization/CN=ruby-app.local"',
            creates: '/etc/nginx/ssl/ruby-app.crt'
          })
          
          # Monitoring setup
          playbook.task('Install monitoring tools', 'apt', {
            name: [
              'htop',
              'iotop',
              'nethogs',
              'monit'
            ],
            state: 'present'
          })
          
          playbook.task('Configure Monit', 'template', {
            src: 'monit/monitrc.j2',
            dest: '/etc/monit/monitrc',
            mode: '0600'
          })
          
          playbook.task('Enable Monit', 'systemd', {
            name: 'monit',
            enabled: 'yes',
            state: 'restarted'
          })
          
          # Backup setup
          playbook.task('Create backup script', 'template', {
            src: 'scripts/backup.sh.j2',
            dest: '/usr/local/bin/ruby-app-backup',
            mode: '0755'
          })
          
          playbook.task('Create backup cron job', 'cron', {
            name: 'ruby-app-backup',
            job: '/usr/local/bin/ruby-app-backup',
            minute: '0',
            hour: '2',
            user: '{{ app_user }}'
          })
          
          # Handlers
          playbook.handler('Restart Ruby application', 'systemd', {
            name: 'ruby-app',
            state: 'restarted'
          })
          
          playbook.handler('Restart Nginx', 'systemd', {
            name: 'nginx',
            state: 'restarted'
          })
          
          # Generate Ansible playbook
          File.write('deploy-ruby-app.yml', playbook.to_yaml)
          puts "Ansible playbook generated!"
          
          playbook
        RUBY
        
        playbook
      end
    end
    
    # 5. Kubernetes Ruby DSL
    # Kubernetes manifests using Ruby DSL
    
    class KubernetesRuby
      def self.generate_manifests
        manifests = <<~RUBY
          # Kubernetes Manifests in Ruby
          require 'json'
          
          module Kubernetes
            class Manifest
              attr_reader :api_version, :kind, :metadata, :spec
              
              def initialize(api_version, kind, name, namespace = nil)
                @api_version = api_version
                @kind = kind
                @metadata = {
                  name: name,
                  namespace: namespace
                }.compact
                @spec = {}
              end
              
              def metadata(metadata)
                @metadata.merge!(metadata)
                self
              end
              
              def spec(spec)
                @spec = spec
                self
              end
              
              def labels(labels)
                @metadata[:labels] = labels
                self
              end
              
              def annotations(annotations)
                @metadata[:annotations] = annotations
                self
              end
              
              def to_yaml
                {
                  apiVersion: @api_version,
                  kind: @kind,
                  metadata: @metadata,
                  spec: @spec
                }.to_yaml
              end
            end
          end
          
          # Namespace
          namespace = Kubernetes::Manifest.new('v1', 'Namespace', 'ruby-app')
          namespace.labels({
            name: 'ruby-app',
            environment: 'production'
          })
          
          # ConfigMap
          config_map = Kubernetes::Manifest.new('v1', 'ConfigMap', 'ruby-app-config', 'ruby-app')
          config_map.labels({
            app: 'ruby-app'
          })
          config_map.spec({
            data: {
              'RAILS_ENV' => 'production',
              'RAILS_LOG_TO_STDOUT' => 'true',
              'RAILS_SERVE_STATIC_FILES' => 'true'
            }
          })
          
          # Secret
          secret = Kubernetes::Manifest.new('v1', 'Secret', 'ruby-app-secrets', 'ruby-app')
          secret.labels({
            app: 'ruby-app'
          })
          secret.spec({
            type: 'Opaque',
            data: {
              'database-password' => Base64.strict_encode64('secure_password'),
              'secret-key-base' => Base64.strict_encode64('very_secure_key_base')
            }
          })
          
          # PersistentVolume
          pv = Kubernetes::Manifest.new('v1', 'PersistentVolume', 'ruby-app-pv')
          pv.spec({
            capacity: {
              storage: '10Gi'
            },
            accessModes: ['ReadWriteOnce'],
            persistentVolumeReclaimPolicy: 'Retain',
            storageClassName: 'manual',
            hostPath: {
              path: '/data/ruby-app'
            }
          })
          
          # PersistentVolumeClaim
          pvc = Kubernetes::Manifest.new('v1', 'PersistentVolumeClaim', 'ruby-app-pvc', 'ruby-app')
          pvc.labels({
            app: 'ruby-app'
          })
          pvc.spec({
            accessModes: ['ReadWriteOnce'],
            resources: {
              requests: {
                storage: '10Gi'
              }
            },
            storageClassName: 'manual'
          })
          
          # Deployment
          deployment = Kubernetes::Manifest.new('apps/v1', 'Deployment', 'ruby-app', 'ruby-app')
          deployment.labels({
            app: 'ruby-app',
            version: 'v1.0.0'
          })
          deployment.spec({
            replicas: 3,
            selector: {
              matchLabels: {
                app: 'ruby-app'
              }
            },
            template: {
              metadata: {
                labels: {
                  app: 'ruby-app',
                  version: 'v1.0.0'
                }
              },
              spec: {
                containers: [
                  {
                    name: 'ruby-app',
                    image: 'ruby-app:latest',
                    ports: [
                      {
                        containerPort: 3000,
                        protocol: 'TCP'
                      }
                    ],
                    env: [
                      {
                        name: 'RAILS_ENV',
                        valueFrom: {
                          configMapKeyRef: {
                            name: 'ruby-app-config',
                            key: 'RAILS_ENV'
                          }
                        }
                      },
                      {
                        name: 'DATABASE_URL',
                        value: 'postgresql://postgres:$(DATABASE_PASSWORD)@db:5432/rubyapp_production'
                      },
                      {
                        name: 'DATABASE_PASSWORD',
                        valueFrom: {
                          secretKeyRef: {
                            name: 'ruby-app-secrets',
                            key: 'database-password'
                          }
                        }
                      },
                      {
                        name: 'SECRET_KEY_BASE',
                        valueFrom: {
                          secretKeyRef: {
                            name: 'ruby-app-secrets',
                            key: 'secret-key-base'
                          }
                        }
                      }
                    ],
                    volumeMounts: [
                      {
                        name: 'app-storage',
                        mountPath: '/app/storage'
                      }
                    ],
                    resources: {
                      requests: {
                        memory: '256Mi',
                        cpu: '250m'
                      },
                      limits: {
                        memory: '512Mi',
                        cpu: '500m'
                      }
                    },
                    livenessProbe: {
                      httpGet: {
                        path: '/health',
                        port: 3000
                      },
                      initialDelaySeconds: 30,
                      periodSeconds: 10
                    },
                    readinessProbe: {
                      httpGet: {
                        path: '/ready',
                        port: 3000
                      },
                      initialDelaySeconds: 5,
                      periodSeconds: 5
                    }
                  }
                ],
                volumes: [
                  {
                    name: 'app-storage',
                    persistentVolumeClaim: {
                      claimName: 'ruby-app-pvc'
                    }
                  }
                ]
              }
            }
          })
          
          # Service
          service = Kubernetes::Manifest.new('v1', 'Service', 'ruby-app-service', 'ruby-app')
          service.labels({
            app: 'ruby-app'
          })
          service.spec({
            selector: {
              app: 'ruby-app'
            },
            ports: [
              {
                port: 80,
                targetPort: 3000,
                protocol: 'TCP'
              }
            ],
            type: 'ClusterIP'
          })
          
          # Ingress
          ingress = Kubernetes::Manifest.new('networking.k8s.io/v1', 'Ingress', 'ruby-app-ingress', 'ruby-app')
          ingress.labels({
            app: 'ruby-app'
          })
          ingress.annotations({
            'kubernetes.io/ingress.class' => 'nginx',
            'cert-manager.io/cluster-issuer' => 'letsencrypt-prod'
          })
          ingress.spec({
            rules: [
              {
                host: 'ruby-app.example.com',
                http: {
                  paths: [
                    {
                      path: '/',
                      pathType: 'Prefix',
                      backend: {
                        service: {
                          name: 'ruby-app-service',
                          port: {
                            number: 80
                          }
                        }
                      }
                    }
                  ]
                }
              }
            ],
            tls: [
              {
                hosts: ['ruby-app.example.com'],
                secretName: 'ruby-app-tls'
              }
            ]
          })
          
          # Database StatefulSet
          statefulset = Kubernetes::Manifest.new('apps/v1', 'StatefulSet', 'postgres', 'ruby-app')
          statefulset.labels({
            app: 'ruby-app',
            component: 'database'
          })
          statefulset.spec({
            serviceName: 'postgres',
            replicas: 1,
            selector: {
              matchLabels: {
                app: 'ruby-app',
                component: 'database'
              }
            },
            template: {
              metadata: {
                labels: {
                  app: 'ruby-app',
                  component: 'database'
                }
              },
              spec: {
                containers: [
                  {
                    name: 'postgres',
                    image: 'postgres:15-alpine',
                    ports: [
                      {
                        containerPort: 5432,
                        protocol: 'TCP'
                      }
                    ],
                    env: [
                      {
                        name: 'POSTGRES_DB',
                        value: 'rubyapp_production'
                      },
                      {
                        name: 'POSTGRES_USER',
                        value: 'postgres'
                      },
                      {
                        name: 'POSTGRES_PASSWORD',
                        valueFrom: {
                          secretKeyRef: {
                            name: 'ruby-app-secrets',
                            key: 'database-password'
                          }
                        }
                      }
                    ],
                    volumeMounts: [
                      {
                        name: 'postgres-storage',
                        mountPath: '/var/lib/postgresql/data'
                      }
                    ],
                    resources: {
                      requests: {
                        memory: '256Mi',
                        cpu: '250m'
                      },
                      limits: {
                        memory: '512Mi',
                        cpu: '500m'
                      }
                    }
                  }
                ],
                volumes: [
                  {
                    name: 'postgres-storage',
                    persistentVolumeClaim: {
                      claimName: 'postgres-pvc'
                    }
                  }
                ]
              }
            }
          })
          
          # Database Service
          db_service = Kubernetes::Manifest.new('v1', 'Service', 'postgres-service', 'ruby-app')
          db_service.labels({
            app: 'ruby-app',
            component: 'database'
          })
          db_service.spec({
            selector: {
              app: 'ruby-app',
              component: 'database'
            },
            ports: [
              {
                port: 5432,
                targetPort: 5432,
                protocol: 'TCP'
              }
            ],
            clusterIP: 'None'
          })
          
          # Redis Deployment
          redis_deployment = Kubernetes::Manifest.new('apps/v1', 'Deployment', 'redis', 'ruby-app')
          redis_deployment.labels({
            app: 'ruby-app',
            component: 'redis'
          })
          redis_deployment.spec({
            replicas: 1,
            selector: {
              matchLabels: {
                app: 'ruby-app',
                component: 'redis'
              }
            },
            template: {
              metadata: {
                labels: {
                  app: 'ruby-app',
                  component: 'redis'
                }
              },
              spec: {
                containers: [
                  {
                    name: 'redis',
                    image: 'redis:7-alpine',
                    ports: [
                      {
                        containerPort: 6379,
                        protocol: 'TCP'
                      }
                    ],
                    resources: {
                      requests: {
                        memory: '128Mi',
                        cpu: '100m'
                      },
                      limits: {
                        memory: '256Mi',
                        cpu: '200m'
                      }
                    }
                  }
                ]
              }
            }
          })
          
          # Redis Service
          redis_service = Kubernetes::Manifest.new('v1', 'Service', 'redis-service', 'ruby-app')
          redis_service.labels({
            app: 'ruby-app',
            component: 'redis'
          })
          redis_service.spec({
            selector: {
              app: 'ruby-app',
              component: 'redis'
            },
            ports: [
              {
                port: 6379,
                targetPort: 6379,
                protocol: 'TCP'
              }
            ],
            clusterIP: 'None'
          })
          
          # HorizontalPodAutoscaler
          hpa = Kubernetes::Manifest.new('autoscaling/v2', 'HorizontalPodAutoscaler', 'ruby-app-hpa', 'ruby-app')
          hpa.spec({
            scaleTargetRef: {
              apiVersion: 'apps/v1',
              kind: 'Deployment',
              name: 'ruby-app'
            },
            minReplicas: 2,
            maxReplicas: 10,
            metrics: [
              {
                type: 'Resource',
                resource: {
                  name: 'cpu',
                  target: {
                    type: 'Utilization',
                    averageUtilization: 70
                  }
                }
              },
              {
                type: 'Resource',
                resource: {
                  name: 'memory',
                  target: {
                    type: 'Utilization',
                    averageUtilization: 80
                  }
                }
              }
            ]
          })
          
          # NetworkPolicy
          network_policy = Kubernetes::Manifest.new('networking.k8s.io/v1', 'NetworkPolicy', 'ruby-app-netpol', 'ruby-app')
          network_policy.spec({
            podSelector: {
              matchLabels: {
                app: 'ruby-app'
              }
            },
            policyTypes: ['Ingress', 'Egress'],
            ingress: [
              {
                from: [
                  {
                    namespaceSelector: {}
                  }
                ],
                ports: [
                  {
                  protocol: 'TCP',
                  port: 3000
                }
              ]
            }
          ],
            egress: [
              {
                to: [],
                ports: [
                  {
                  protocol: 'TCP',
                  port: 5432
                },
                  {
                  protocol: 'TCP',
                  port: 6379
                }
              ]
              }
            ]
          })
          
          # Generate all manifests
          manifests = {
            namespace: namespace.to_yaml,
            config_map: config_map.to_yaml,
            secret: secret.to_yaml,
            persistent_volume: pv.to_yaml,
            persistent_volume_claim: pvc.to_yaml,
            deployment: deployment.to_yaml,
            service: service.to_yaml,
            ingress: ingress.to_yaml,
            database_statefulset: statefulset.to_yaml,
            database_service: db_service.to_yaml,
            redis_deployment: redis_deployment.to_yaml,
            redis_service: redis_service.to_yaml,
            hpa: hpa.to_yaml,
            network_policy: network_policy.to_yaml
          }
          
          # Write manifests to files
          manifests.each do |name, content|
            filename = "#{name}.yaml"
            File.write(filename, content)
            puts "Generated #{filename}"
          end
          
          manifests
        RUBY
        
        manifests
      end
    end
    
    # 6. Configuration Management
    # Centralized configuration management
    
    class ConfigurationManager
      def self.generate_config_files
        config_files = <<~RUBY
          # Configuration Management in Ruby
          
          # Environment-specific configurations
          class EnvironmentConfig
            def initialize(environment)
              @environment = environment
              @config = load_config
            end
            
            def get(key, default = nil)
              value = @config.dig(@environment, key)
              value || default
            end
            
            def set(key, value)
              @config[@environment] ||= {}
              @config[@environment][key] = value
            end
            
            def to_h
              @config[@environment] || {}
            end
            
            def to_json
              to_h.to_json
            end
            
            def save
              File.write("config/environments/#{@environment}.json", to_json)
            end
            
            private
            
            def load_config
              config_file = "config/environments/#{@environment}.json"
              if File.exist?(config_file)
                JSON.parse(File.read(config_file))
              else
                {}
              end
            end
          end
          
          # Development environment
          dev_config = EnvironmentConfig.new('development')
          dev_config.set('database', {
            'adapter' => 'postgresql',
            'host' => 'localhost',
            'port' => 5432,
            'database' => 'rubyapp_development',
            'username' => 'postgres',
            'password' => 'postgres'
          })
          
          dev_config.set('redis', {
            'host' => 'localhost',
            'port' => 6379,
            'db' => 0
          })
          
          dev_config.set('app', {
            'name' => 'Ruby App',
            'domain' => 'localhost',
            'port' => 3000,
            'secret_key_base' => 'development_secret_key_base',
            'debug' => true,
            'log_level' => 'debug'
          })
          
          dev_config.set('features', {
            'enable_signup' => true,
            'enable_social_login' => false,
            'enable_analytics' => false,
            'enable_monitoring' => false
          })
          
          dev_config.set('services', {
            'email_provider' => 'letter_opener',
            'payment_gateway' => 'stripe_test',
            'file_storage' => 'local'
          })
          
          # Staging environment
          staging_config = EnvironmentConfig.new('staging')
          staging_config.set('database', {
            'adapter' => 'postgresql',
            'host' => 'staging-db.ruby-app.com',
            'port' => 5432,
            'database' => 'rubyapp_staging',
            'username' => 'rubyapp',
            'password' => 'staging_password',
            'pool' => 25,
            'timeout' => 5000
          })
          
          staging_config.set('redis', {
            'host' => 'staging-redis.ruby-app.com',
            'port' => 6379,
            'db' => 0,
            'password' => 'staging_redis_password'
          })
          
          staging_config.set('app', {
            'name' => 'Ruby App',
            'domain' => 'staging.ruby-app.com',
            'port' => 443,
            'ssl' => true,
            'log_level' => 'info'
          })
          
          staging_config.set('features', {
            'enable_signup' => true,
            'enable_social_login' => true,
            'enable_analytics' => true,
            'enable_monitoring' => true
          })
          
          staging_config.set('services', {
            'email_provider' => 'sendgrid',
            'payment_gateway' => 'stripe',
            'file_storage' => 's3'
          })
          
          staging_config.set('monitoring', {
            'sentry_dsn' => 'https://sentry.io/dsn',
            'new_relic_license_key' => 'staging_license_key',
            'prometheus_enabled' => true
          })
          
          # Production environment
          prod_config = EnvironmentConfig.new('production')
          prod_config.set('database', {
            'adapter' => 'postgresql',
            'host' => 'prod-db.ruby-app.com',
            'port' => 5432,
            'database' => 'rubyapp_production',
            'username' => 'rubyapp',
            'password' => 'production_password',
            'pool' => 50,
            'timeout' => 5000,
            'replica_host' => 'prod-db-replica.ruby-app.com'
          })
          
          prod_config.set('redis', {
            'host' => 'prod-redis.ruby-app.com',
            'port' => 6379,
            'db' => 0,
            'password' => 'production_redis_password',
            'cluster' => true
          })
          
          prod_config.set('app', {
            'name' => 'Ruby App',
            'domain' => 'ruby-app.com',
            'port' => 443,
            'ssl' => true,
            'force_ssl' => true,
            'log_level' => 'warn'
          })
          
          prod_config.set('features', {
            'enable_signup' => false,
            'enable_social_login' => true,
            'enable_analytics' => true,
            'enable_monitoring' => true,
            'enable_caching' => true,
            'enable_rate_limiting' => true
          })
          
          prod_config.set('services', {
            'email_provider' => 'sendgrid',
            'payment_gateway' => 'stripe',
            'file_storage' => 's3',
            'cdn' => 'cloudfront'
          })
          
          prod_config.set('monitoring', {
            'sentry_dsn' => 'https://sentry.io/dsn',
            'new_relic_license_key' => 'production_license_key',
            'prometheus_enabled' => true,
            'datadog_api_key' => 'datadog_api_key'
          })
          
          prod_config.set('security', {
            'session_timeout' => 3600,
            'max_login_attempts' => 5,
            'password_min_length' => 12,
            'require_2fa' => true,
            'allowed_origins' => ['https://ruby-app.com']
          })
          
          prod_config.set('performance', {
            'cache_ttl' => 3600,
            'max_concurrent_requests' => 1000,
            'enable_compression' => true,
            'static_file_ttl' => 86400
          })
          
          # Save configurations
          dev_config.save
          staging_config.save
          prod_config.save
          
          puts "Configuration files generated!"
          
          {
            development: dev_config.to_h,
            staging: staging_config.to_h,
            production: prod_config.to_h
          }
        RUBY
        
        config_files
      end
    end
  end
end

# Usage examples and demonstrations
if __FILE__ == $0
  puts "Infrastructure as Code Demonstration"
  puts "=" * 60
  
  # Demonstrate different IaC tools
  puts "\n1. Infrastructure as Code Tools:"
  puts "✅ Terraform Ruby DSL"
  puts "✅ AWS CloudFormation Ruby DSL"
  puts "✅ Docker Compose Ruby DSL"
  puts "✅ Ansible Playbooks in Ruby"
  puts "✅ Kubernetes Ruby DSL"
  
  # Demonstrate configuration management
  puts "\n2. Configuration Management:"
  puts "✅ Environment-specific configurations"
  puts "✅ Centralized configuration"
  puts "✅ Secret management"
  puts "✅ Feature flags"
  
  # Demonstrate infrastructure components
  puts "\n3. Infrastructure Components:"
  puts "✅ VPC and networking"
  puts "✅ Compute resources"
  puts "✅ Database configuration"
  puts "✅ Load balancers"
  puts "✅ Storage and backups"
  
  # Demonstrate automation
  puts "\n4. Automation Features:"
  puts "✅ Declarative configuration"
  puts "✅ Version control integration"
  puts "✅ Automated testing"
  puts "✅ Continuous deployment"
  
  puts "\nInfrastructure as Code enables reproducible and maintainable infrastructure!"
end
