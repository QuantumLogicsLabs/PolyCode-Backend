<?php
/**
 * CI/CD Pipeline Implementation
 * 
 * Comprehensive CI/CD pipeline setup and automation for PHP applications.
 */

// CI/CD Pipeline Manager
class CICDPipelineManager
{
    private array $pipelines = [];
    private array $jobs = [];
    private array $stages = [];
    private array $artifacts = [];
    
    public function __construct()
    {
        $this->initializePipelines();
        $this->setupStages();
    }
    
    /**
     * Initialize CI/CD pipelines
     */
    private function initializePipelines(): void
    {
        $this->pipelines = [
            'php-app-pipeline' => [
                'name' => 'PHP Application Pipeline',
                'description' => 'Complete CI/CD pipeline for PHP applications',
                'trigger' => 'push',
                'branches' => ['main', 'develop'],
                'variables' => [
                    'PHP_VERSION' => '8.2',
                    'NODE_VERSION' => '18',
                    'COMPOSER_CACHE_DIR' => '/tmp/composer-cache'
                ],
                'cache' => [
                    'paths' => [
                        'vendor/',
                        'node_modules/',
                        '.npm/'
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Setup pipeline stages
     */
    private function setupStages(): void
    {
        $this->stages = [
            'build' => [
                'name' => 'Build',
                'description' => 'Build and prepare application',
                'jobs' => ['install-dependencies', 'compile-assets']
            ],
            'test' => [
                'name' => 'Test',
                'description' => 'Run tests and quality checks',
                'jobs' => ['unit-tests', 'integration-tests', 'code-quality', 'security-scan']
            ],
            'package' => [
                'name' => 'Package',
                'description' => 'Package application for deployment',
                'jobs' => ['create-artifact', 'generate-docs']
            ],
            'deploy' => [
                'name' => 'Deploy',
                'description' => 'Deploy to environments',
                'jobs' => ['deploy-staging', 'deploy-production']
            ]
        ];
    }
    
    /**
     * Add pipeline
     */
    public function addPipeline(string $name, array $config): void
    {
        $this->pipelines[$name] = $config;
    }
    
    /**
     * Add job to pipeline
     */
    public function addJob(string $pipeline, string $stage, string $jobName, array $jobConfig): void
    {
        if (!isset($this->jobs[$pipeline])) {
            $this->jobs[$pipeline] = [];
        }
        
        if (!isset($this->jobs[$pipeline][$stage])) {
            $this->jobs[$pipeline][$stage] = [];
        }
        
        $this->jobs[$pipeline][$stage][$jobName] = $jobConfig;
    }
    
    /**
     * Generate GitLab CI configuration
     */
    public function generateGitLabCI(string $pipeline): string
    {
        $config = $this->pipelines[$pipeline] ?? null;
        
        if (!$config) {
            throw new Exception("Pipeline $pipeline not found");
        }
        
        $yaml = "# Generated GitLab CI Configuration for {$config['name']}\n\n";
        
        // Add global configuration
        $yaml .= "stages:\n";
        foreach (array_keys($this->stages) as $stage) {
            $yaml .= "  - $stage\n";
        }
        $yaml .= "\n";
        
        // Add variables
        if (!empty($config['variables'])) {
            $yaml .= "variables:\n";
            foreach ($config['variables'] as $key => $value) {
                $yaml .= "  $key: \"$value\"\n";
            }
            $yaml .= "\n";
        }
        
        // Add cache configuration
        if (!empty($config['cache'])) {
            $yaml .= "cache:\n";
            if (isset($config['cache']['paths'])) {
                $yaml .= "  paths:\n";
                foreach ($config['cache']['paths'] as $path) {
                    $yaml .= "    - $path\n";
                }
            }
            $yaml .= "\n";
        }
        
        // Add jobs
        $pipelineJobs = $this->jobs[$pipeline] ?? [];
        
        foreach ($pipelineJobs as $stage => $jobs) {
            foreach ($jobs as $jobName => $jobConfig) {
                $yaml .= $this->generateGitLabJob($jobName, $stage, $jobConfig);
                $yaml .= "\n";
            }
        }
        
        return $yaml;
    }
    
    /**
     * Generate GitLab job configuration
     */
    private function generateGitLabJob(string $jobName, string $stage, array $config): string
    {
        $yaml = "$jobName:\n";
        $yaml .= "  stage: $stage\n";
        
        if (isset($config['image'])) {
            $yaml .= "  image: {$config['image']}\n";
        }
        
        if (isset($config['services'])) {
            $yaml .= "  services:\n";
            foreach ($config['services'] as $service) {
                $yaml .= "    - $service\n";
            }
        }
        
        if (isset($config['before_script'])) {
            $yaml .= "  before_script:\n";
            foreach ($config['before_script'] as $script) {
                $yaml .= "    - $script\n";
            }
        }
        
        if (isset($config['script'])) {
            $yaml .= "  script:\n";
            foreach ($config['script'] as $script) {
                $yaml .= "    - $script\n";
            }
        }
        
        if (isset($config['artifacts'])) {
            $yaml .= "  artifacts:\n";
            if (isset($config['artifacts']['paths'])) {
                $yaml .= "    paths:\n";
                foreach ($config['artifacts']['paths'] as $path) {
                    $yaml .= "      - $path\n";
                }
            }
            if (isset($config['artifacts']['expire_in'])) {
                $yaml .= "    expire_in: {$config['artifacts']['expire_in']}\n";
            }
        }
        
        if (isset($config['only'])) {
            $yaml .= "  only:\n";
            foreach ($config['only'] as $only) {
                $yaml .= "    - $only\n";
            }
        }
        
        if (isset($config['except'])) {
            $yaml .= "  except:\n";
            foreach ($config['except'] as $except) {
                $yaml .= "    - $except\n";
            }
        }
        
        if (isset($config['when'])) {
            $yaml .= "  when: {$config['when']}\n";
        }
        
        return $yaml;
    }
    
    /**
     * Generate GitHub Actions workflow
     */
    public function generateGitHubActions(string $pipeline): string
    {
        $config = $this->pipelines[$pipeline] ?? null;
        
        if (!$config) {
            throw new Exception("Pipeline $pipeline not found");
        }
        
        $yaml = "name: {$config['name']}\n\n";
        
        // Add triggers
        $yaml .= "on:\n";
        if ($config['trigger'] === 'push') {
            $yaml .= "  push:\n";
            $yaml .= "    branches: [ " . implode(', ', array_map(fn($b) => "'$b'", $config['branches'])) . " ]\n";
        }
        $yaml .= "\n";
        
        // Add jobs
        $pipelineJobs = $this->jobs[$pipeline] ?? [];
        
        foreach ($pipelineJobs as $stage => $jobs) {
            foreach ($jobs as $jobName => $jobConfig) {
                $yaml .= $this->generateGitHubJob($jobName, $jobConfig);
                $yaml .= "\n";
            }
        }
        
        return $yaml;
    }
    
    /**
     * Generate GitHub job configuration
     */
    private function generateGitHubJob(string $jobName, array $config): string
    {
        $yaml = "$jobName:\n";
        $yaml .= "  runs-on: " . ($config['runs-on'] ?? 'ubuntu-latest') . "\n";
        
        if (isset($config['strategy'])) {
            $yaml .= "  strategy:\n";
            foreach ($config['strategy'] as $key => $value) {
                $yaml .= "    $key:\n";
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        if (is_array($v)) {
                            $yaml .= "      $k: [ " . implode(', ', array_map(fn($i) => "'$i'", $v)) . " ]\n";
                        } else {
                            $yaml .= "      $k: $v\n";
                        }
                    }
                }
            }
        }
        
        if (isset($config['steps'])) {
            $yaml .= "  steps:\n";
            foreach ($config['steps'] as $step) {
                if (isset($step['uses'])) {
                    $yaml .= "    - uses: {$step['uses']}\n";
                    if (isset($step['with'])) {
                        $yaml .= "      with:\n";
                        foreach ($step['with'] as $key => $value) {
                            $yaml .= "        $key: $value\n";
                        }
                    }
                } elseif (isset($step['run'])) {
                    $yaml .= "    - run: {$step['run']}\n";
                } elseif (isset($step['name'])) {
                    $yaml .= "    - name: {$step['name']}\n";
                    if (isset($step['run'])) {
                        $yaml .= "      run: |\n";
                        foreach (explode("\n", $step['run']) as $line) {
                            $yaml .= "        $line\n";
                        }
                    }
                }
            }
        }
        
        return $yaml;
    }
    
    /**
     * Setup complete PHP pipeline
     */
    public function setupCompletePHPPipeline(): void
    {
        $pipeline = 'php-app-pipeline';
        
        // Build stage jobs
        $this->addJob($pipeline, 'build', 'install-dependencies', [
            'image' => 'php:8.2-cli',
            'script' => [
                'composer install --no-progress --no-interaction --prefer-dist',
                'composer dump-autoload'
            ],
            'artifacts' => [
                'paths' => ['vendor/'],
                'expire_in' => '1 hour'
            ]
        ]);
        
        $this->addJob($pipeline, 'build', 'compile-assets', [
            'image' => 'node:18-alpine',
            'script' => [
                'npm ci',
                'npm run build'
            ],
            'artifacts' => [
                'paths' => ['public/build/', 'node_modules/'],
                'expire_in' => '1 hour'
            ]
        ]);
        
        // Test stage jobs
        $this->addJob($pipeline, 'test', 'unit-tests', [
            'image' => 'php:8.2-cli',
            'script' => [
                'php vendor/bin/phpunit --coverage-text --coverage-clover=coverage.xml',
                'php vendor/bin/phpunit --log-junit=junit.xml'
            ],
            'artifacts' => [
                'paths' => ['coverage.xml', 'junit.xml'],
                'expire_in' => '1 week'
            ]
        ]);
        
        $this->addJob($pipeline, 'test', 'integration-tests', [
            'image' => 'php:8.2-cli',
            'services' => ['mysql:8.0', 'redis:6-alpine'],
            'script' => [
                'php tests/bin/integration-tests'
            ]
        ]);
        
        $this->addJob($pipeline, 'test', 'code-quality', [
            'image' => 'php:8.2-cli',
            'script' => [
                'composer require --dev phpstan/phpstan',
                'vendor/bin/phpstan analyse',
                'vendor/bin/php-cs-fixer fix --dry-run --diff'
            ]
        ]);
        
        $this->addJob($pipeline, 'test', 'security-scan', [
            'image' => 'php:8.2-cli',
            'script' => [
                'composer audit',
                'composer require --dev enlightn/security-checker',
                'vendor/bin/security-checker security:check'
            ]
        ]);
        
        // Package stage jobs
        $this->addJob($pipeline, 'package', 'create-artifact', [
            'image' => 'alpine:latest',
            'script' => [
                'tar -czf application.tar.gz --exclude=.git --exclude=node_modules --exclude=tests .',
                'echo "Artifact created: application.tar.gz"'
            ],
            'artifacts' => [
                'paths' => ['application.tar.gz'],
                'expire_in' => '1 month'
            ]
        ]);
        
        // Deploy stage jobs
        $this->addJob($pipeline, 'deploy', 'deploy-staging', [
            'image' => 'alpine:latest',
            'script' => [
                'echo "Deploying to staging environment"',
                'echo "Deployment completed successfully"'
            ],
            'when' => 'manual',
            'only' => ['main']
        ]);
        
        $this->addJob($pipeline, 'deploy', 'deploy-production', [
            'image' => 'alpine:latest',
            'script' => [
                'echo "Deploying to production environment"',
                'echo "Deployment completed successfully"'
            ],
            'when' => 'manual',
            'only' => ['main']
        ]);
    }
}

// Build Automation
class BuildAutomation
{
    private array $buildSteps = [];
    private array $dependencies = [];
    private array $artifacts = [];
    
    public function __construct()
    {
        $this->initializeBuildSteps();
        $this->setupDependencies();
    }
    
    /**
     * Initialize build steps
     */
    private function initializeBuildSteps(): void
    {
        $this->buildSteps = [
            'setup' => [
                'name' => 'Setup Environment',
                'commands' => [
                    'php -v',
                    'composer --version',
                    'node --version',
                    'npm --version'
                ]
            ],
            'install' => [
                'name' => 'Install Dependencies',
                'commands' => [
                    'composer install --no-dev --optimize-autoloader',
                    'npm ci --production'
                ]
            ],
            'compile' => [
                'name' => 'Compile Assets',
                'commands' => [
                    'npm run build',
                    'npm run minify'
                ]
            ],
            'test' => [
                'name' => 'Run Tests',
                'commands' => [
                    'vendor/bin/phpunit',
                    'npm run test'
                ]
            ],
            'package' => [
                'name' => 'Package Application',
                'commands' => [
                    'composer install --no-dev',
                    'npm run build:prod',
                    'tar -czf build.tar.gz .'
                ]
            ]
        ];
    }
    
    /**
     * Setup dependencies
     */
    private function setupDependencies(): void
    {
        $this->dependencies = [
            'php' => '>=8.1',
            'composer' => '>=2.0',
            'node' => '>=16.0',
            'npm' => '>=8.0'
        ];
    }
    
    /**
     * Execute build step
     */
    public function executeStep(string $stepName): array
    {
        if (!isset($this->buildSteps[$stepName])) {
            throw new Exception("Build step '$stepName' not found");
        }
        
        $step = $this->buildSteps[$stepName];
        $results = [
            'step' => $stepName,
            'name' => $step['name'],
            'commands' => [],
            'success' => true,
            'output' => [],
            'errors' => []
        ];
        
        foreach ($step['commands'] as $command) {
            $output = $this->executeCommand($command);
            $results['commands'][] = $command;
            $results['output'][] = $output;
            
            if (!$output['success']) {
                $results['success'] = false;
                $results['errors'][] = $output['error'];
                break;
            }
        }
        
        return $results;
    }
    
    /**
     * Execute command (simulated)
     */
    private function executeCommand(string $command): array
    {
        // Simulate command execution
        $success = true;
        $output = '';
        $error = '';
        
        if (strpos($command, 'php -v') !== false) {
            $output = 'PHP 8.2.10 (cli) (built: Sep 29 2023 15:23:45)';
        } elseif (strpos($command, 'composer --version') !== false) {
            $output = 'Composer version 2.5.5 2023-09-01 10:23:15';
        } elseif (strpos($command, 'composer install') !== false) {
            $output = 'Installing dependencies from lock file';
            $output .= 'Package operations: 15 installs, 0 updates, 0 removals';
        } elseif (strpos($command, 'npm ci') !== false) {
            $output = 'npm ci successfully installed packages';
        } elseif (strpos($command, 'npm run build') !== false) {
            $output = 'Build completed successfully';
        } elseif (strpos($command, 'vendor/bin/phpunit') !== false) {
            $output = 'PHPUnit 9.5.20 by Sebastian Bergmann';
            $output .= 'Tests: 15, Assertions: 45, Skipped: 0';
        } elseif (strpos($command, 'tar -czf') !== false) {
            $output = 'Archive created successfully';
        } else {
            $output = "Command executed: $command";
        }
        
        // Simulate occasional failures
        if (rand(1, 20) === 1) {
            $success = false;
            $error = "Command failed: $command";
        }
        
        return [
            'command' => $command,
            'success' => $success,
            'output' => $output,
            'error' => $error,
            'timestamp' => time()
        ];
    }
    
    /**
     * Run complete build
     */
    public function runBuild(): array
    {
        $buildResults = [
            'build_id' => uniqid('build_'),
            'started_at' => time(),
            'steps' => [],
            'success' => true,
            'duration' => 0
        ];
        
        foreach (array_keys($this->buildSteps) as $stepName) {
            $stepResult = $this->executeStep($stepName);
            $buildResults['steps'][] = $stepResult;
            
            if (!$stepResult['success']) {
                $buildResults['success'] = false;
                break;
            }
        }
        
        $buildResults['duration'] = time() - $buildResults['started_at'];
        $buildResults['finished_at'] = time();
        
        return $buildResults;
    }
    
    /**
     * Get build steps
     */
    public function getBuildSteps(): array
    {
        return $this->buildSteps;
    }
    
    /**
     * Get dependencies
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }
}

// Test Automation
class TestAutomation
{
    private array $testSuites = [];
    private array $testResults = [];
    private array $coverage = [];
    
    public function __construct()
    {
        $this->initializeTestSuites();
    }
    
    /**
     * Initialize test suites
     */
    private function initializeTestSuites(): void
    {
        $this->testSuites = [
            'unit' => [
                'name' => 'Unit Tests',
                'path' => 'tests/Unit',
                'tests' => [
                    'UserServiceTest',
                    'PostServiceTest',
                    'EmailServiceTest',
                    'ValidationServiceTest'
                ]
            ],
            'integration' => [
                'name' => 'Integration Tests',
                'path' => 'tests/Integration',
                'tests' => [
                    'UserRepositoryTest',
                    'PostControllerTest',
                    'ApiEndpointTest'
                ]
            ],
            'feature' => [
                'name' => 'Feature Tests',
                'path' => 'tests/Feature',
                'tests' => [
                    'UserRegistrationTest',
                    'PostCreationTest',
                    'CommentSystemTest'
                ]
            ],
            'browser' => [
                'name' => 'Browser Tests',
                'path' => 'tests/Browser',
                'tests' => [
                    'LoginPageTest',
                    'DashboardTest',
                    'ProfilePageTest'
                ]
            ]
        ];
    }
    
    /**
     * Run test suite
     */
    public function runTestSuite(string $suite): array
    {
        if (!isset($this->testSuites[$suite])) {
            throw new Exception("Test suite '$suite' not found");
        }
        
        $testSuite = $this->testSuites[$suite];
        $results = [
            'suite' => $suite,
            'name' => $testSuite['name'],
            'started_at' => time(),
            'tests' => [],
            'total' => 0,
            'passed' => 0,
            'failed' => 0,
            'skipped' => 0,
            'success' => true,
            'duration' => 0
        ];
        
        foreach ($testSuite['tests'] as $testName) {
            $testResult = $this->runTest($testName, $suite);
            $results['tests'][] = $testResult;
            $results['total']++;
            
            if ($testResult['status'] === 'passed') {
                $results['passed']++;
            } elseif ($testResult['status'] === 'failed') {
                $results['failed']++;
                $results['success'] = false;
            } elseif ($testResult['status'] === 'skipped') {
                $results['skipped']++;
            }
        }
        
        $results['duration'] = time() - $results['started_at'];
        $results['finished_at'] = time();
        
        $this->testResults[$suite] = $results;
        
        return $results;
    }
    
    /**
     * Run individual test
     */
    private function runTest(string $testName, string $suite): array
    {
        // Simulate test execution
        $status = 'passed';
        $message = '';
        $duration = rand(100, 2000) / 1000; // 0.1-2 seconds
        
        // Simulate occasional failures
        if (rand(1, 20) === 1) {
            $status = 'failed';
            $message = 'Test assertion failed';
        } elseif (rand(1, 50) === 1) {
            $status = 'skipped';
            $message = 'Test skipped due to dependency';
        }
        
        return [
            'test' => $testName,
            'suite' => $suite,
            'status' => $status,
            'message' => $message,
            'duration' => $duration,
            'assertions' => rand(1, 10)
        ];
    }
    
    /**
     * Run all test suites
     */
    public function runAllTests(): array
    {
        $allResults = [
            'run_id' => uniqid('test_'),
            'started_at' => time(),
            'suites' => [],
            'total_tests' => 0,
            'total_passed' => 0,
            'total_failed' => 0,
            'total_skipped' => 0,
            'success' => true,
            'duration' => 0
        ];
        
        foreach (array_keys($this->testSuites) as $suite) {
            $suiteResults = $this->runTestSuite($suite);
            $allResults['suites'][] = $suiteResults;
            
            $allResults['total_tests'] += $suiteResults['total'];
            $allResults['total_passed'] += $suiteResults['passed'];
            $allResults['total_failed'] += $suiteResults['failed'];
            $allResults['total_skipped'] += $suiteResults['skipped'];
            
            if (!$suiteResults['success']) {
                $allResults['success'] = false;
            }
        }
        
        $allResults['duration'] = time() - $allResults['started_at'];
        $allResults['finished_at'] = time();
        
        return $allResults;
    }
    
    /**
     * Generate code coverage report
     */
    public function generateCoverage(): array
    {
        // Simulate coverage data
        $this->coverage = [
            'total_lines' => 1000,
            'covered_lines' => 850,
            'coverage_percentage' => 85.0,
            'files' => [
                'src/UserService.php' => [
                    'lines' => 200,
                    'covered' => 180,
                    'percentage' => 90.0
                ],
                'src/PostService.php' => [
                    'lines' => 150,
                    'covered' => 120,
                    'percentage' => 80.0
                ],
                'src/EmailService.php' => [
                    'lines' => 100,
                    'covered' => 95,
                    'percentage' => 95.0
                ],
                'src/ValidationService.php' => [
                    'lines' => 80,
                    'covered' => 60,
                    'percentage' => 75.0
                ]
            ]
        ];
        
        return $this->coverage;
    }
    
    /**
     * Get test results
     */
    public function getTestResults(): array
    {
        return $this->testResults;
    }
    
    /**
     * Get coverage data
     */
    public function getCoverage(): array
    {
        return $this->coverage;
    }
}

// Deployment Automation
class DeploymentAutomation
{
    private array $environments = [];
    private array $deployments = [];
    private array $strategies = [];
    
    public function __construct()
    {
        $this->initializeEnvironments();
        $this->setupStrategies();
    }
    
    /**
     * Initialize environments
     */
    private function initializeEnvironments(): void
    {
        $this->environments = [
            'development' => [
                'name' => 'Development',
                'url' => 'dev.example.com',
                'branch' => 'develop',
                'auto_deploy' => true,
                'servers' => ['dev-server-1'],
                'health_check' => '/health'
            ],
            'staging' => [
                'name' => 'Staging',
                'url' => 'staging.example.com',
                'branch' => 'main',
                'auto_deploy' => false,
                'servers' => ['staging-server-1', 'staging-server-2'],
                'health_check' => '/health'
            ],
            'production' => [
                'name' => 'Production',
                'url' => 'example.com',
                'branch' => 'main',
                'auto_deploy' => false,
                'servers' => ['prod-server-1', 'prod-server-2', 'prod-server-3'],
                'health_check' => '/health'
            ]
        ];
    }
    
    /**
     * Setup deployment strategies
     */
    private function setupStrategies(): void
    {
        $this->strategies = [
            'rolling' => [
                'name' => 'Rolling Update',
                'description' => 'Update servers one by one',
                'downtime' => 'none',
                'rollback_time' => 'fast'
            ],
            'blue_green' => [
                'name' => 'Blue-Green Deployment',
                'description' => 'Deploy to parallel environment',
                'downtime' => 'minimal',
                'rollback_time' => 'instant'
            ],
            'canary' => [
                'name' => 'Canary Deployment',
                'description' => 'Gradual rollout to subset of servers',
                'downtime' => 'none',
                'rollback_time' => 'fast'
            ]
        ];
    }
    
    /**
     * Deploy to environment
     */
    public function deploy(string $environment, string $strategy = 'rolling'): array
    {
        if (!isset($this->environments[$environment])) {
            throw new Exception("Environment '$environment' not found");
        }
        
        if (!isset($this->strategies[$strategy])) {
            throw new Exception("Strategy '$strategy' not found");
        }
        
        $env = $this->environments[$environment];
        $strat = $this->strategies[$strategy];
        
        $deployment = [
            'id' => uniqid('deploy_'),
            'environment' => $environment,
            'strategy' => $strategy,
            'started_at' => time(),
            'status' => 'deploying',
            'servers' => [],
            'success' => true,
            'duration' => 0
        ];
        
        // Simulate deployment process
        foreach ($env['servers'] as $server) {
            $serverResult = $this->deployToServer($server, $strategy);
            $deployment['servers'][] = $serverResult;
            
            if (!$serverResult['success']) {
                $deployment['success'] = false;
                $deployment['status'] = 'failed';
                break;
            }
        }
        
        if ($deployment['success']) {
            $deployment['status'] = 'completed';
            
            // Run health checks
            $healthCheck = $this->runHealthCheck($environment);
            $deployment['health_check'] = $healthCheck;
            
            if (!$healthCheck['success']) {
                $deployment['success'] = false;
                $deployment['status'] = 'unhealthy';
            }
        }
        
        $deployment['duration'] = time() - $deployment['started_at'];
        $deployment['finished_at'] = time();
        
        $this->deployments[] = $deployment;
        
        return $deployment;
    }
    
    /**
     * Deploy to individual server
     */
    private function deployToServer(string $server, string $strategy): array
    {
        // Simulate deployment time
        $deployTime = rand(30, 120); // 30-120 seconds
        
        // Simulate occasional failures
        $success = rand(1, 20) !== 1; // 95% success rate
        
        return [
            'server' => $server,
            'strategy' => $strategy,
            'deploy_time' => $deployTime,
            'success' => $success,
            'error' => $success ? null : 'Deployment failed on server',
            'timestamp' => time()
        ];
    }
    
    /**
     * Run health check
     */
    private function runHealthCheck(string $environment): array
    {
        $env = $this->environments[$environment];
        
        // Simulate health check
        $success = rand(1, 50) !== 1; // 98% success rate
        
        return [
            'environment' => $environment,
            'url' => $env['url'] . $env['health_check'],
            'success' => $success,
            'response_time' => rand(50, 500), // 50-500ms
            'status_code' => $success ? 200 : 500,
            'timestamp' => time()
        ];
    }
    
    /**
     * Rollback deployment
     */
    public function rollback(string $deploymentId): array
    {
        // Find deployment
        $deployment = null;
        foreach ($this->deployments as $d) {
            if ($d['id'] === $deploymentId) {
                $deployment = $d;
                break;
            }
        }
        
        if (!$deployment) {
            throw new Exception("Deployment '$deploymentId' not found");
        }
        
        $rollback = [
            'deployment_id' => $deploymentId,
            'environment' => $deployment['environment'],
            'started_at' => time(),
            'status' => 'rolling_back',
            'servers' => [],
            'success' => true,
            'duration' => 0
        ];
        
        // Simulate rollback
        foreach ($deployment['servers'] as $server) {
            $rollbackResult = [
                'server' => $server['server'],
                'success' => true,
                'rollback_time' => 30,
                'timestamp' => time()
            ];
            
            $rollback['servers'][] = $rollbackResult;
        }
        
        $rollback['status'] = 'completed';
        $rollback['duration'] = time() - $rollback['started_at'];
        $rollback['finished_at'] = time();
        
        return $rollback;
    }
    
    /**
     * Get deployment history
     */
    public function getDeploymentHistory(): array
    {
        return $this->deployments;
    }
    
    /**
     * Get environments
     */
    public function getEnvironments(): array
    {
        return $this->environments;
    }
    
    /**
     * Get strategies
     */
    public function getStrategies(): array
    {
        return $this->strategies;
    }
}

// CI/CD Examples
class CICDExamples
{
    private CICDPipelineManager $pipelineManager;
    private BuildAutomation $buildAutomation;
    private TestAutomation $testAutomation;
    private DeploymentAutomation $deploymentAutomation;
    
    public function __construct()
    {
        $this->pipelineManager = new CICDPipelineManager();
        $this->buildAutomation = new BuildAutomation();
        $this->testAutomation = new TestAutomation();
        $this->deploymentAutomation = new DeploymentAutomation();
        
        $this->pipelineManager->setupCompletePHPPipeline();
    }
    
    public function demonstratePipelines(): void
    {
        echo "CI/CD Pipeline Demo\n";
        echo str_repeat("-", 22) . "\n";
        
        // Generate GitLab CI configuration
        echo "GitLab CI Configuration:\n";
        $gitlabCI = $this->pipelineManager->generateGitLabCI('php-app-pipeline');
        echo substr($gitlabCI, 0, 1000) . "...\n\n";
        
        // Generate GitHub Actions workflow
        echo "GitHub Actions Workflow:\n";
        $githubActions = $this->pipelineManager->generateGitHubActions('php-app-pipeline');
        echo substr($githubActions, 0, 1000) . "...\n\n";
        
        // Show pipeline stages
        echo "Pipeline Stages:\n";
        $stages = $this->pipelineManager->getStages();
        foreach ($stages as $stageName => $stage) {
            echo "$stageName: {$stage['name']}\n";
            echo "  Description: {$stage['description']}\n";
            echo "  Jobs: " . implode(', ', $stage['jobs']) . "\n\n";
        }
    }
    
    public function demonstrateBuildAutomation(): void
    {
        echo "Build Automation Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Show build steps
        echo "Build Steps:\n";
        $steps = $this->buildAutomation->getBuildSteps();
        foreach ($steps as $stepName => $step) {
            echo "$stepName: {$step['name']}\n";
            echo "  Commands: " . implode(', ', $step['commands']) . "\n\n";
        }
        
        // Run complete build
        echo "Running Complete Build:\n";
        $buildResult = $this->buildAutomation->runBuild();
        
        echo "Build ID: {$buildResult['build_id']}\n";
        echo "Success: " . ($buildResult['success'] ? 'Yes' : 'No') . "\n";
        echo "Duration: {$buildResult['duration']}s\n";
        echo "Steps:\n";
        
        foreach ($buildResult['steps'] as $step) {
            echo "  {$step['name']}: " . ($step['success'] ? 'Success' : 'Failed') . "\n";
            if (!$step['success']) {
                echo "    Errors: " . implode(', ', $step['errors']) . "\n";
            }
        }
        
        // Show dependencies
        echo "\nBuild Dependencies:\n";
        $deps = $this->buildAutomation->getDependencies();
        foreach ($deps as $dep => $version) {
            echo "  $dep: $version\n";
        }
    }
    
    public function demonstrateTestAutomation(): void
    {
        echo "\nTest Automation Demo\n";
        echo str_repeat("-", 23) . "\n";
        
        // Show test suites
        echo "Test Suites:\n";
        $suites = $this->testAutomation->getTestSuites();
        foreach ($suites as $suiteName => $suite) {
            echo "$suiteName: {$suite['name']}\n";
            echo "  Path: {$suite['path']}\n";
            echo "  Tests: " . implode(', ', $suite['tests']) . "\n\n";
        }
        
        // Run all tests
        echo "Running All Tests:\n";
        $testResults = $this->testAutomation->runAllTests();
        
        echo "Run ID: {$testResults['run_id']}\n";
        echo "Success: " . ($testResults['success'] ? 'Yes' : 'No') . "\n";
        echo "Duration: {$testResults['duration']}s\n";
        echo "Total Tests: {$testResults['total_tests']}\n";
        echo "Passed: {$testResults['total_passed']}\n";
        echo "Failed: {$testResults['total_failed']}\n";
        echo "Skipped: {$testResults['total_skipped']}\n\n";
        
        // Show suite results
        echo "Suite Results:\n";
        foreach ($testResults['suites'] as $suite) {
            echo "  {$suite['name']}: " . ($suite['success'] ? 'Passed' : 'Failed') . "\n";
            echo "    Tests: {$suite['total']} ({$suite['passed']} passed, {$suite['failed']} failed, {$suite['skipped']} skipped)\n";
            echo "    Duration: {$suite['duration']}s\n\n";
        }
        
        // Generate coverage report
        echo "Code Coverage:\n";
        $coverage = $this->testAutomation->generateCoverage();
        echo "Total Coverage: {$coverage['coverage_percentage']}%\n";
        echo "Files:\n";
        foreach ($coverage['files'] as $file => $fileCoverage) {
            echo "  $file: {$fileCoverage['percentage']}% ({$fileCoverage['covered']}/{$fileCoverage['lines']} lines)\n";
        }
    }
    
    public function demonstrateDeployment(): void
    {
        echo "\nDeployment Automation Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        // Show environments
        echo "Deployment Environments:\n";
        $environments = $this->deploymentAutomation->getEnvironments();
        foreach ($environments as $envName => $env) {
            echo "$envName: {$env['name']}\n";
            echo "  URL: {$env['url']}\n";
            echo "  Branch: {$env['branch']}\n";
            echo "  Auto Deploy: " . ($env['auto_deploy'] ? 'Yes' : 'No') . "\n";
            echo "  Servers: " . implode(', ', $env['servers']) . "\n\n";
        }
        
        // Show deployment strategies
        echo "Deployment Strategies:\n";
        $strategies = $this->deploymentAutomation->getStrategies();
        foreach ($strategies as $stratName => $strat) {
            echo "$stratName: {$strat['name']}\n";
            echo "  Description: {$strat['description']}\n";
            echo "  Downtime: {$strat['downtime']}\n";
            echo "  Rollback Time: {$strat['rollback_time']}\n\n";
        }
        
        // Deploy to staging
        echo "Deploying to Staging (Rolling Update):\n";
        $deployment = $this->deploymentAutomation->deploy('staging', 'rolling');
        
        echo "Deployment ID: {$deployment['id']}\n";
        echo "Status: {$deployment['status']}\n";
        echo "Success: " . ($deployment['success'] ? 'Yes' : 'No') . "\n";
        echo "Duration: {$deployment['duration']}s\n";
        echo "Servers:\n";
        
        foreach ($deployment['servers'] as $server) {
            echo "  {$server['server']}: " . ($server['success'] ? 'Success' : 'Failed') . "\n";
            echo "    Deploy Time: {$server['deploy_time']}s\n";
        }
        
        if (isset($deployment['health_check'])) {
            echo "\nHealth Check:\n";
            echo "  Success: " . ($deployment['health_check']['success'] ? 'Yes' : 'No') . "\n";
            echo "  Response Time: {$deployment['health_check']['response_time']}ms\n";
            echo "  Status Code: {$deployment['health_check']['status_code']}\n";
        }
        
        // Deploy to production (canary)
        echo "\nDeploying to Production (Canary):\n";
        $prodDeployment = $this->deploymentAutomation->deploy('production', 'canary');
        
        echo "Deployment ID: {$prodDeployment['id']}\n";
        echo "Status: {$prodDeployment['status']}\n";
        echo "Success: " . ($prodDeployment['success'] ? 'Yes' : 'No') . "\n";
        echo "Duration: {$prodDeployment['duration']}s\n";
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nCI/CD Best Practices\n";
        echo str_repeat("-", 25) . "\n";
        
        echo "1. Pipeline Design:\n";
        echo "   • Keep pipelines fast and reliable\n";
        echo "   • Use appropriate caching strategies\n";
        echo "   • Implement proper error handling\n";
        echo "   • Use parallel execution when possible\n";
        echo "   • Monitor pipeline performance\n\n";
        
        echo "2. Build Automation:\n";
        echo "   • Use reproducible builds\n";
        echo "   • Implement dependency caching\n";
        echo "   • Use containerized builds\n";
        echo "   • Generate build artifacts\n";
        echo "   • Version build outputs\n\n";
        
        echo "3. Test Automation:\n";
        echo "   • Test early and often\n";
        echo "   • Use multiple test types\n";
        echo "   • Implement test coverage requirements\n";
        echo "   • Use test data management\n";
        echo "   • Run tests in parallel\n\n";
        
        echo "4. Deployment Automation:\n";
        echo "   • Use zero-downtime deployments\n";
        echo "   • Implement proper rollback strategies\n";
        echo "   • Use blue-green or canary deployments\n";
        echo "   • Implement health checks\n";
        echo "   • Monitor deployment success\n\n";
        
        echo "5. Security:\n";
        echo "   • Secure pipeline secrets\n";
        echo "   • Scan for vulnerabilities\n";
        echo "   • Use signed commits\n";
        echo "   • Implement access controls\n";
        echo "   • Audit pipeline activities";
    }
    
    public function runAllExamples(): void
    {
        echo "CI/CD Pipeline Automation Examples\n";
        echo str_repeat("=", 35) . "\n";
        
        $this->demonstratePipelines();
        $this->demonstrateBuildAutomation();
        $this->demonstrateTestAutomation();
        $this->demonstrateDeployment();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runCICDDemo(): void
{
    $examples = new CICDExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runCICDDemo();
}
?>
