<?php
/**
 * Infrastructure as Code
 * 
 * Implementation of IaC patterns and infrastructure automation.
 */

// Terraform Configuration Generator
class TerraformGenerator
{
    private array $resources = [];
    private array $variables = [];
    private array 'outputs' => [];
    private array 'providers = [];
    
    public function __construct()
    {
        $this->initializeProviders();
    }
    
    /**
     * Initialize providers
     */
    private function initializeProviders(): void
    {
        $this->providers = [
            'aws' => [
                'source' => 'hashicorp/aws',
                'version' => '~> 5.0',
                'region' => 'us-west-2'
            ],
            'digitalocean' => [
                'source' => 'digitalocean/digitalocean',
                'version' => '~> 2.0'
            ]
        ];
    }
    
    /**
     * Add provider
     */
    public function addProvider(string $name, array $config): void
    {
        $this->providers[$name] = $config;
    }
    
    /**
     * Add variable
     */
    public function addVariable(string $name, array $config): void
    {
        $this->variables[$name] = $config;
    }
    
    /**
     * Add resource
     */
    public function addResource(string $type, string $name, array $config): void
    {
        $resourceKey = "$type.$name";
        $this->resources[$resourceKey] = [
            'type' => $type,
            'name' => $name,
            'config' => $config
        ];
    }
    
    /**
     * Add output
     */
    public function addOutput(string $name, string $value): void
    {
        $this->outputs[$name] = $value;
    }
    
    /**
     * Generate Terraform configuration
     */
    public function generate(): string
    {
        $tf = "# Generated Terraform Configuration\n\n";
        
        // Add providers
        $tf .= "# Providers\n";
        $tf .= "terraform {\n";
        $tf .= "  required_providers {\n";
        foreach ($this->providers as $name => $config) {
            $tf .= "    $name = {\n";
            $tf .= "      source  = \"{$config['source']}\"\n";
            $tf .= "      version = \"{$config['version']}\"\n";
            $tf .= "    }\n";
        }
        $tf .= "  }\n";
        $tf .= "}\n\n";
        
        // Add provider configurations
        foreach ($this->providers as $name => $config) {
            $tf .= "provider \"$name\" {\n";
            foreach ($config as $key => $value) {
                if ($key !== 'source' && $key !== 'version') {
                    $tf .= "  $key = \"$value\"\n";
                }
            }
            $tf .= "}\n\n";
        }
        
        // Add variables
        if (!empty($this->variables)) {
            $tf .= "# Variables\n";
            foreach ($this->variables as $name => $config) {
                $tf .= "variable \"$name\" {\n";
                if (isset($config['description'])) {
                    $tf .= "  description = \"{$config['description']}\"\n";
                }
                if (isset($config['type'])) {
                    $tf .= "  type        = \"{$config['type']}\"\n";
                }
                if (isset($config['default'])) {
                    $tf .= "  default     = \"{$config['default']}\"\n";
                }
                $tf .= "}\n\n";
            }
        }
        
        // Add resources
        $tf .= "# Resources\n";
        foreach ($this->resources as $resourceKey => $resource) {
            $tf .= "resource \"{$resource['type']}\" \"{$resource['name']}\" {\n";
            foreach ($resource['config'] as $key => $value) {
                $tf .= "  $key = " . $this->formatTerraformValue($value) . "\n";
            }
            $tf .= "}\n\n";
        }
        
        // Add outputs
        if (!empty($this->outputs)) {
            $tf .= "# Outputs\n";
            foreach ($this->outputs as $name => $value) {
                $tf .= "output \"$name\" {\n";
                $tf .= "  value = $value\n";
                $tf .= "}\n\n";
            }
        }
        
        return $tf;
    }
    
    /**
     * Format Terraform value
     */
    private function formatTerraformValue($value): string
    {
        if (is_string($value)) {
            return "\"$value\"";
        } elseif (is_array($value)) {
            $items = [];
            foreach ($value as $item) {
                $items[] = $this->formatTerraformValue($item);
            }
            return "[" . implode(", ", $items) . "]";
        } elseif (is_bool($value)) {
            return $value ? "true" : "false";
        } else {
            return (string) $value;
        }
    }
    
    /**
     * Generate complete AWS infrastructure
     */
    public function generateAWSInfrastructure(): void
    {
        // Add variables
        $this->addVariable('app_name', [
            'description' => 'Name of the application',
            'type' => 'string',
            'default' => 'php-app'
        ]);
        
        $this->addVariable('environment', [
            'description' => 'Environment name',
            'type' => 'string',
            'default' => 'development'
        ]);
        
        $this->addVariable('instance_type', [
            'description' => 'EC2 instance type',
            'type' => 'string',
            'default' => 't3.micro'
        ]);
        
        // VPC
        $this->addResource('aws_vpc', 'main', [
            'cidr_block' => '10.0.0.0/16',
            'enable_dns_hostnames' => true,
            'enable_dns_support' => true,
            'tags' => [
                'Name' => '${var.app_name}-vpc',
                'Environment' => '${var.environment}'
            ]
        ]);
        
        // Internet Gateway
        $this->addResource('aws_internet_gateway', 'main', [
            'vpc_id' => '${aws_vpc.main.id}',
            'tags' => [
                'Name' => '${var.app_name}-igw',
                'Environment' => '${var.environment}'
            ]
        ]);
        
        // Subnet
        $this->addResource('aws_subnet', 'public', [
            'vpc_id' => '${aws_vpc.main.id}',
            'cidr_block' => '10.0.1.0/24',
            'availability_zone' => 'us-west-2a',
            'map_public_ip_on_launch' => true,
            'tags' => [
                'Name' => '${var.app_name}-public-subnet',
                'Environment' => '${var.environment}'
            ]
        ]);
        
        // Route Table
        $this->addResource('aws_route_table', 'public', [
            'vpc_id' => '${aws_vpc.main.id}',
            'route' => [
                'cidr_block' => '0.0.0.0/0',
                'gateway_id' => '${aws_internet_gateway.main.id}'
            ],
            'tags' => [
                'Name' => '${var.app_name}-public-rt',
                'Environment' => '${var.environment}'
            ]
        ]);
        
        // Route Table Association
        $this->addResource('aws_route_table_association', 'public', [
            'subnet_id' => '${aws_subnet.public.id}',
            'route_table_id' => '${aws_route_table.public.id}'
        ]);
        
        // Security Group
        $this->addResource('aws_security_group', 'web', [
            'name' => '${var.app_name}-web-sg',
            'description' => 'Security group for web servers',
            'vpc_id' => '${aws_vpc.main.id}',
            'ingress' => [
                [
                    'from_port' => 22,
                    'to_port' => 22,
                    'protocol' => 'tcp',
                    'cidr_blocks' => ['0.0.0.0/0']
                ],
                [
                    'from_port' => 80,
                    'to_port' => 80,
                    'protocol' => 'tcp',
                    'cidr_blocks' => ['0.0.0.0/0']
                ],
                [
                    'from_port' => 443,
                    'to_port' => 443,
                    'protocol' => 'tcp',
                    'cidr_blocks' => ['0.0.0.0/0']
                ]
            ],
            'egress' => [
                [
                    'from_port' => 0,
                    'to_port' => 0,
                    'protocol' => '-1',
                    'cidr_blocks' => ['0.0.0.0/0']
                ]
            ],
            'tags' => [
                'Name' => '${var.app_name}-web-sg',
                'Environment' => '${var.environment}'
            ]
        ]);
        
        // EC2 Instance
        $this->addResource('aws_instance', 'web', [
            'ami' => 'ami-0c55b159cbfafe1f0', // Amazon Linux 2
            'instance_type' => '${var.instance_type}',
            'subnet_id' => '${aws_subnet.public.id}',
            'vpc_security_group_ids' => ['${aws_security_group.web.id}'],
            'associate_public_ip_address' => true,
            'user_data' => base64_encode($this->getUserData()),
            'tags' => [
                'Name' => '${var.app_name}-web-server',
                'Environment' => '${var.environment}'
            ]
        ]);
        
        // Add outputs
        $this->addOutput('instance_public_ip', '${aws_instance.web.public_ip}');
        $this->addOutput('instance_id', '${aws_instance.web.id}');
        $this->addOutput('vpc_id', '${aws_vpc.main.id}');
    }
    
    /**
     * Get user data for EC2 instance
     */
    private function getUserData(): string
    {
        return '#!/bin/bash
yum update -y
yum install -y httpd php php-mysqlnd
systemctl start httpd
systemctl enable httpd
echo "<h1>Hello from Terraform!</h1>" > /var/www/html/index.html';
    }
}

// Ansible Playbook Generator
class AnsibleGenerator
{
    private array $playbooks = [];
    private array $roles = [];
    private array 'inventory = [];
    
    public function __construct()
    {
        $this->initializeInventory();
    }
    
    /**
     * Initialize inventory
     */
    private function initializeInventory(): void
    {
        $this->inventory = [
            'webservers' => [
                'hosts' => [
                    'web1.example.com',
                    'web2.example.com'
                ],
                'vars' => [
                    'http_port' => 80,
                    'max_clients' => 200
                ]
            ],
            'database' => [
                'hosts' => [
                    'db1.example.com'
                ],
                'vars' => [
                    'mysql_port' => 3306,
                    'mysql_db' => 'app'
                ]
            ]
        ];
    }
    
    /**
     * Add playbook
     */
    public function addPlaybook(string $name, array $config): void
    {
        $this->playbooks[$name] = $config;
    }
    
    /**
     * Add role
     */
    public function addRole(string $name, array $tasks): void
    {
        $this->roles[$name] = $tasks;
    }
    
    /**
     * Generate playbook
     */
    public function generatePlaybook(string $name): string
    {
        if (!isset($this->playbooks[$name])) {
            throw new Exception("Playbook '$name' not found");
        }
        
        $playbook = $this->playbooks[$name];
        $yaml = "---\n";
        $yaml .= "- name: {$playbook['name']}\n";
        
        if (isset($playbook['hosts'])) {
            $yaml .= "  hosts: {$playbook['hosts']}\n";
        }
        
        if (isset($playbook['become'])) {
            $yaml .= "  become: {$playbook['become']}\n";
        }
        
        if (isset($playbook['vars'])) {
            $yaml .= "  vars:\n";
            foreach ($playbook['vars'] as $key => $value) {
                $yaml .= "    $key: $value\n";
            }
        }
        
        if (isset($playbook['tasks'])) {
            $yaml .= "  tasks:\n";
            foreach ($playbook['tasks'] as $task) {
                $yaml .= $this->formatAnsibleTask($task, 2);
            }
        }
        
        if (isset($playbook['roles'])) {
            $yaml .= "  roles:\n";
            foreach ($playbook['roles'] as $role) {
                $yaml .= "    - $role\n";
            }
        }
        
        return $yaml;
    }
    
    /**
     * Format Ansible task
     */
    private function formatAnsibleTask(array $task, int $indent = 2): string
    {
        $indentStr = str_repeat('  ', $indent);
        $yaml = $indentStr . "- name: {$task['name']}\n";
        
        foreach ($task as $key => $value) {
            if ($key === 'name') continue;
            
            if (is_array($value)) {
                $yaml .= $indentStr . "  $key:\n";
                foreach ($value as $k => $v) {
                    $yaml .= $indentStr . "    $k: $v\n";
                }
            } else {
                $yaml .= $indentStr . "  $key: $value\n";
            }
        }
        
        return $yaml;
    }
    
    /**
     * Generate inventory file
     */
    public function generateInventory(): string
    {
        $yaml = "# Generated Ansible Inventory\n\n";
        
        foreach ($this->inventory as $group => $config) {
            $yaml .= "[$group]\n";
            
            if (isset($config['hosts'])) {
                foreach ($config['hosts'] as $host) {
                    $yaml .= "$host\n";
                }
                $yaml .= "\n";
            }
            
            if (isset($config['vars'])) {
                $yaml .= "[$group:vars]\n";
                foreach ($config['vars'] as $key => $value) {
                    $yaml .= "$key=$value\n";
                }
                $yaml .= "\n";
            }
        }
        
        return $yaml;
    }
    
    /**
     * Generate complete PHP application setup
     */
    public function generatePHPAppSetup(): void
    {
        // Web server setup playbook
        $this->addPlaybook('webserver-setup', [
            'name' => 'Setup PHP Web Server',
            'hosts' => 'webservers',
            'become' => true,
            'vars' => [
                'php_version' => '8.2',
                'app_path' => '/var/www/html',
                'app_user' => 'www-data'
            ],
            'tasks' => [
                [
                    'name' => 'Update system packages',
                    'yum' => [
                        'name' => '*',
                        'state' => 'latest',
                        'update_cache' => 'yes'
                    ]
                ],
                [
                    'name' => 'Install required packages',
                    'yum' => [
                        'name' => ['httpd', 'php', 'php-mysqlnd', 'php-json', 'php-mbstring'],
                        'state' => 'present'
                    ]
                ],
                [
                    'name' => 'Start and enable Apache',
                    'systemd' => [
                        'name' => 'httpd',
                        'state' => 'started',
                        'enabled' => 'yes'
                    ]
                ],
                [
                    'name' => 'Create application directory',
                    'file' => [
                        'path' => '{{ app_path }}',
                        'state' => 'directory',
                        'owner' => '{{ app_user }}',
                        'group' => '{{ app_user }}',
                        'mode' => '0755'
                    ]
                ],
                [
                    'name' => 'Deploy application files',
                    'copy' => [
                        'src' => 'src/',
                        'dest' => '{{ app_path }}/',
                        'owner' => '{{ app_user }}',
                        'group' => '{{ app_user }}',
                        'mode' => '0755'
                    ]
                ],
                [
                    'name' => 'Configure Apache virtual host',
                    'template' => [
                        'src' => 'templates/vhost.conf.j2',
                        'dest' => '/etc/httpd/conf.d/app.conf',
                        'owner' => 'root',
                        'group' => 'root',
                        'mode' => '0644'
                    ]
                ],
                [
                    'name' => 'Restart Apache',
                    'systemd' => [
                        'name' => 'httpd',
                        'state' => 'restarted'
                    ]
                ]
            ]
        ]);
        
        // Database setup playbook
        $this->addPlaybook('database-setup', [
            'name' => 'Setup MySQL Database',
            'hosts' => 'database',
            'become' => true,
            'vars' => [
                'mysql_root_password' => 'secure_password',
                'mysql_database' => 'app',
                'mysql_user' => 'app_user',
                'mysql_password' => 'app_password'
            ],
            'tasks' => [
                [
                    'name' => 'Update system packages',
                    'yum' => [
                        'name' => '*',
                        'state' => 'latest',
                        'update_cache' => 'yes'
                    ]
                ],
                [
                    'name' => 'Install MySQL server',
                    'yum' => [
                        'name' => ['mysql-server', 'mysql'],
                        'state' => 'present'
                    ]
                ],
                [
                    'name' => 'Start and enable MySQL',
                    'systemd' => [
                        'name' => 'mysqld',
                        'state' => 'started',
                        'enabled' => 'yes'
                    ]
                ],
                [
                    'name' => 'Set MySQL root password',
                    'mysql_user' => [
                        'name' => 'root',
                        'password' => '{{ mysql_root_password }}',
                        'login_unix_socket' => '/var/lib/mysql/mysql.sock',
                        'state' => 'present'
                    ]
                ],
                [
                    'name' => 'Create application database',
                    'mysql_db' => [
                        'name' => '{{ mysql_database }}',
                        'state' => 'present',
                        'login_user' => 'root',
                        'login_password' => '{{ mysql_root_password }}'
                    ]
                ],
                [
                    'name' => 'Create application user',
                    'mysql_user' => [
                        'name' => '{{ mysql_user }}',
                        'password' => '{{ mysql_password }}',
                        'priv' => '{{ mysql_database }}.*:ALL',
                        'host' => '%',
                        'state' => 'present',
                        'login_user' => 'root',
                        'login_password' => '{{ mysql_root_password }}'
                    ]
                ]
            ]
        ]);
    }
}

// Configuration Management
class ConfigurationManager
{
    private array $configurations = [];
    private array $environments = [];
    private string $configPath;
    
    public function __construct(string $configPath = '/etc/app')
    {
        $this->configPath = $configPath;
        $this->initializeEnvironments();
    }
    
    /**
     * Initialize environments
     */
    private function initializeEnvironments(): void
    {
        $this->environments = [
            'development' => [
                'database' => [
                    'host' => 'localhost',
                    'port' => 3306,
                    'name' => 'app_dev',
                    'user' => 'dev_user',
                    'password' => 'dev_password'
                ],
                'app' => [
                    'debug' => true,
                    'log_level' => 'DEBUG',
                    'cache' => false
                ],
                'services' => [
                    'redis' => [
                        'host' => 'localhost',
                        'port' => 6379
                    ],
                    'mail' => [
                        'driver' => 'log'
                    ]
                ]
            ],
            'staging' => [
                'database' => [
                    'host' => 'staging-db.example.com',
                    'port' => 3306,
                    'name' => 'app_staging',
                    'user' => 'staging_user',
                    'password' => 'staging_password'
                ],
                'app' => [
                    'debug' => false,
                    'log_level' => 'INFO',
                    'cache' => true
                ],
                'services' => [
                    'redis' => [
                        'host' => 'staging-redis.example.com',
                        'port' => 6379
                    ],
                    'mail' => [
                        'driver' => 'smtp',
                        'host' => 'smtp.example.com'
                    ]
                ]
            ],
            'production' => [
                'database' => [
                    'host' => 'prod-db.example.com',
                    'port' => 3306,
                    'name' => 'app_production',
                    'user' => 'prod_user',
                    'password' => 'prod_password'
                ],
                'app' => [
                    'debug' => false,
                    'log_level' => 'ERROR',
                    'cache' => true
                ],
                'services' => [
                    'redis' => [
                        'host' => 'prod-redis.example.com',
                        'port' => 6379
                    ],
                    'mail' => [
                        'driver' => 'smtp',
                        'host' => 'smtp.example.com'
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Add configuration
     */
    public function addConfiguration(string $environment, array $config): void
    {
        $this->configurations[$environment] = array_merge(
            $this->environments[$environment] ?? [],
            $config
        );
    }
    
    /**
     * Get configuration
     */
    public function getConfiguration(string $environment): array
    {
        return $this->configurations[$environment] ?? $this->environments[$environment] ?? [];
    }
    
    /**
     * Generate configuration files
     */
    public function generateConfigFiles(string $environment): array
    {
        $config = $this->getConfiguration($environment);
        $files = [];
        
        // Generate PHP config file
        $files['config.php'] = $this->generatePHPConfig($config);
        
        // Generate JSON config file
        $files['config.json'] = json_encode($config, JSON_PRETTY_PRINT);
        
        // Generate YAML config file
        $files['config.yaml'] = $this->arrayToYaml($config);
        
        // Generate environment file
        $files['.env'] = $this->generateEnvFile($config);
        
        return $files;
    }
    
    /**
     * Generate PHP configuration
     */
    private function generatePHPConfig(array $config): string
    {
        $php = "<?php\n";
        $php .= "// Generated configuration for environment\n";
        $php .= "return [\n";
        
        foreach ($config as $section => $values) {
            $php .= "    '$section' => [\n";
            foreach ($values as $key => $value) {
                if (is_bool($value)) {
                    $php .= "        '$key' => " . ($value ? 'true' : 'false') . ",\n";
                } elseif (is_string($value)) {
                    $php .= "        '$key' => '$value',\n";
                } elseif (is_array($value)) {
                    $php .= "        '$key' => " . $this->arrayToPHP($value) . ",\n";
                } else {
                    $php .= "        '$key' => $value,\n";
                }
            }
            $php .= "    ],\n";
        }
        
        $php .= "];\n";
        
        return $php;
    }
    
    /**
     * Convert array to PHP array syntax
     */
    private function arrayToPHP(array $array): string
    {
        $php = "[\n";
        foreach ($array as $key => $value) {
            if (is_string($key)) {
                $php .= "            '$key' => ";
            } else {
                $php .= "            ";
            }
            
            if (is_bool($value)) {
                $php .= ($value ? 'true' : 'false');
            } elseif (is_string($value)) {
                $php .= "'$value'";
            } elseif (is_array($value)) {
                $php .= $this->arrayToPHP($value);
            } else {
                $php .= $value;
            }
            
            $php .= ",\n";
        }
        $php .= "        ]";
        
        return $php;
    }
    
    /**
     * Generate environment file
     */
    private function generateEnvFile(array $config): string
    {
        $env = "# Generated environment configuration\n";
        
        foreach ($config as $section => $values) {
            foreach ($values as $key => $value) {
                $envKey = strtoupper($section . '_' . $key);
                if (is_bool($value)) {
                    $env .= "$envKey=" . ($value ? 'true' : 'false') . "\n";
                } elseif (is_string($value)) {
                    $env .= "$envKey=\"$value\"\n";
                } elseif (is_array($value)) {
                    $env .= "$envKey=" . json_encode($value) . "\n";
                } else {
                    $env .= "$envKey=$value\n";
                }
            }
        }
        
        return $env;
    }
    
    /**
     * Convert array to YAML
     */
    private function arrayToYaml(array $array, int $level = 0): string
    {
        $yaml = '';
        $indent = str_repeat('  ', $level);
        
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $yaml .= "$indent$key:\n";
                $yaml .= $this->arrayToYaml($value, $level + 1);
            } else {
                $yaml .= "$indent$key: $value\n";
            }
        }
        
        return $yaml;
    }
    
    /**
     * Validate configuration
     */
    public function validateConfiguration(string $environment): array
    {
        $config = $this->getConfiguration($environment);
        $errors = [];
        
        // Validate required sections
        $requiredSections = ['database', 'app'];
        foreach ($requiredSections as $section) {
            if (!isset($config[$section])) {
                $errors[] = "Missing required section: $section";
            }
        }
        
        // Validate database configuration
        if (isset($config['database'])) {
            $dbRequired = ['host', 'port', 'name', 'user', 'password'];
            foreach ($dbRequired as $field) {
                if (!isset($config['database'][$field])) {
                    $errors[] = "Missing required database field: $field";
                }
            }
        }
        
        return $errors;
    }
}

// Infrastructure as Code Examples
class InfrastructureAsCodeExamples
{
    private TerraformGenerator $terraform;
    private AnsibleGenerator $ansible;
    private ConfigurationManager $configManager;
    
    public function __construct()
    {
        $this->terraform = new TerraformGenerator();
        $this->ansible = new AnsibleGenerator();
        $this->configManager = new ConfigurationManager();
        
        $this->terraform->generateAWSInfrastructure();
        $this->ansible->generatePHPAppSetup();
    }
    
    public function demonstrateTerraform(): void
    {
        echo "Terraform Infrastructure Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        // Generate Terraform configuration
        echo "Generated Terraform Configuration:\n";
        $tfConfig = $this->terraform->generate();
        echo substr($tfConfig, 0, 1500) . "...\n\n";
        
        // Show resources
        echo "Infrastructure Resources:\n";
        $resources = $this->terraform->getResources();
        foreach ($resources as $resourceKey => $resource) {
            echo "{$resource['type']}.{$resource['name']}\n";
            foreach ($resource['config'] as $key => $value) {
                echo "  $key: " . (is_array($value) ? json_encode($value) : $value) . "\n";
            }
            echo "\n";
        }
        
        // Show providers
        echo "Providers:\n";
        $providers = $this->terraform->getProviders();
        foreach ($providers as $name => $config) {
            echo "$name: {$config['source']} ({$config['version']})\n";
            foreach ($config as $key => $value) {
                if ($key !== 'source' && $key !== 'version') {
                    echo "  $key: $value\n";
                }
            }
            echo "\n";
        }
    }
    
    public function demonstrateAnsible(): void
    {
        echo "\nAnsible Configuration Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Generate inventory
        echo "Generated Inventory:\n";
        $inventory = $this->ansible->generateInventory();
        echo substr($inventory, 0, 500) . "...\n\n";
        
        // Generate playbooks
        echo "Web Server Setup Playbook:\n";
        $webPlaybook = $this->ansible->generatePlaybook('webserver-setup');
        echo substr($webPlaybook, 0, 800) . "...\n\n";
        
        echo "Database Setup Playbook:\n";
        $dbPlaybook = $this->ansible->generatePlaybook('database-setup');
        echo substr($dbPlaybook, 0, 800) . "...\n\n";
        
        // Show playbook summary
        echo "Playbook Summary:\n";
        $playbooks = $this->ansible->getPlaybooks();
        foreach ($playbooks as $name => $playbook) {
            echo "$name: {$playbook['name']}\n";
            echo "  Hosts: {$playbook['hosts']}\n";
            echo "  Tasks: " . count($playbook['tasks']) . "\n";
            echo "  Become: " . ($playbook['become'] ? 'Yes' : 'No') . "\n\n";
        }
    }
    
    public function demonstrateConfigurationManagement(): void
    {
        echo "\nConfiguration Management Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        // Show environment configurations
        echo "Environment Configurations:\n";
        $environments = ['development', 'staging', 'production'];
        
        foreach ($environments as $env) {
            echo "\n$env Environment:\n";
            $config = $this->configManager->getConfiguration($env);
            
            foreach ($config as $section => $values) {
                echo "  $section:\n";
                foreach ($values as $key => $value) {
                    if (is_array($value)) {
                        echo "    $key: " . json_encode($value) . "\n";
                    } elseif (is_bool($value)) {
                        echo "    $key: " . ($value ? 'true' : 'false') . "\n";
                    } else {
                        echo "    $key: $value\n";
                    }
                }
            }
        }
        
        // Generate configuration files
        echo "\nGenerated Configuration Files (Production):\n";
        $files = $this->configManager->generateConfigFiles('production');
        
        foreach ($files as $filename => $content) {
            echo "\n$filename:\n";
            echo substr($content, 0, 300) . "...\n";
        }
        
        // Validate configuration
        echo "\nConfiguration Validation:\n";
        $errors = $this->configManager->validateConfiguration('production');
        
        if (empty($errors)) {
            echo "✓ Configuration is valid\n";
        } else {
            echo "✗ Configuration errors:\n";
            foreach ($errors as $error) {
                echo "  • $error\n";
            }
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nInfrastructure as Code Best Practices\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "1. Terraform Best Practices:\n";
        echo "   • Use remote state storage\n";
        echo "   • Implement proper module structure\n";
        echo "   • Use variables for configuration\n";
        echo "   • Implement proper tagging\n";
        echo "   • Use workspaces for environments\n\n";
        
        echo "2. Ansible Best Practices:\n";
        echo "   • Use roles for reusability\n";
        echo "   • Implement idempotent tasks\n";
        echo "   • Use variables and templates\n";
        echo "   • Implement proper error handling\n";
        echo "   • Use vault for secrets\n\n";
        
        echo "3. Configuration Management:\n";
        echo "   • Separate environment configs\n";
        echo "   • Use version control for configs\n";
        echo "   • Implement validation\n";
        echo "   • Use encrypted secrets\n";
        echo "   • Document configuration options\n\n";
        
        echo "4. General IaC Principles:\n";
        echo "   • Treat infrastructure as code\n";
        echo "   • Use version control\n";
        echo "   • Implement testing\n";
        echo "   • Use proper naming conventions\n";
        echo "   • Document your infrastructure\n\n";
        
        echo "5. Security Considerations:\n";
        echo "   • Use least privilege principle\n";
        echo "   • Encrypt sensitive data\n";
        echo "   • Implement network segmentation\n";
        echo "   • Regular security audits\n";
        echo "   • Use security scanning tools";
    }
    
    public function runAllExamples(): void
    {
        echo "Infrastructure as Code Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateTerraform();
        $this->demonstrateAnsible();
        $this->demonstrateConfigurationManagement();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runInfrastructureAsCodeDemo(): void
{
    $examples = new InfrastructureAsCodeExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runInfrastructureAsCodeDemo();
}
?>
