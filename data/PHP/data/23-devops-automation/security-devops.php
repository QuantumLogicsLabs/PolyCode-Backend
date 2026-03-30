<?php
/**
 * Security DevOps Implementation
 * 
 * Security practices, vulnerability scanning, and compliance in DevOps pipelines.
 */

// Security Scanner
class SecurityScanner
{
    private array $vulnerabilities = [];
    private array $dependencies = [];
    private array $scanResults = [];
    private array $rules = [];
    
    public function __construct()
    {
        $this->initializeRules();
        $this->initializeDependencies();
    }
    
    /**
     * Initialize security rules
     */
    private function initializeRules(): void
    {
        $this->rules = [
            'sql_injection' => [
                'pattern' => '/\b(SELECT|INSERT|UPDATE|DELETE)\b.*\$\{.*\}/i',
                'severity' => 'critical',
                'description' => 'Potential SQL injection vulnerability'
            ],
            'xss' => [
                'pattern' => '/echo.*\$_(GET|POST|REQUEST)\[/i',
                'severity' => 'high',
                'description' => 'Cross-site scripting vulnerability'
            ],
            'hardcoded_password' => [
                'pattern' => '/password\s*=\s*[\'"][^\'"]{8,}[\'"]/i',
                'severity' => 'critical',
                'description' => 'Hardcoded password detected'
            ],
            'eval_usage' => [
                'pattern' => '/eval\s*\(/i',
                'severity' => 'high',
                'description' => 'Use of eval() function'
            ],
            'shell_exec' => [
                'pattern' => '/(shell_exec|exec|system|passthru)\s*\(/i',
                'severity' => 'medium',
                'description' => 'Use of shell execution functions'
            ],
            'file_inclusion' => [
                'pattern' => '/(include|require)(_once)?\s*\$.*\(/i',
                'severity' => 'high',
                'description' => 'Potential file inclusion vulnerability'
            ],
            'weak_crypto' => [
                'pattern' => '/(md5|sha1)\s*\(/i',
                'severity' => 'medium',
                'description' => 'Weak cryptographic function detected'
            ],
            'debug_mode' => [
                'pattern' => '/error_reporting\s*\(\s*E_ALL\s*\)/i',
                'severity' => 'low',
                'description' => 'Debug mode enabled in production'
            ]
        ];
    }
    
    /**
     * Initialize dependencies
     */
    private function initializeDependencies(): void
    {
        $this->dependencies = [
            'composer' => [
                'phpmailer/phpmailer' => [
                    'version' => '6.8.0',
                    'vulnerabilities' => []
                ],
                'doctrine/orm' => [
                    'version' => '2.14.0',
                    'vulnerabilities' => [
                        [
                            'id' => 'CVE-2023-1234',
                            'severity' => 'medium',
                            'description' => 'SQL injection in query builder'
                        ]
                    ]
                ],
                'symfony/http-foundation' => [
                    'version' => '6.2.0',
                    'vulnerabilities' => []
                ]
            ],
            'npm' => [
                'axios' => [
                    'version' => '1.4.0',
                    'vulnerabilities' => []
                ],
                'lodash' => [
                    'version' => '4.17.20',
                    'vulnerabilities' => [
                        [
                            'id' => 'CVE-2021-23337',
                            'severity' => 'high',
                            'description' => 'Prototype pollution vulnerability'
                        ]
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Scan source code for vulnerabilities
     */
    public function scanSourceCode(string $path): array
    {
        $results = [
            'scan_id' => uniqid('scan_'),
            'path' => $path,
            'started_at' => time(),
            'files_scanned' => 0,
            'vulnerabilities' => [],
            'status' => 'running'
        ];
        
        // Simulate scanning files
        $files = $this->getFilesToScan($path);
        
        foreach ($files as $file) {
            $fileVulns = $this->scanFile($file);
            $results['vulnerabilities'] = array_merge($results['vulnerabilities'], $fileVulns);
            $results['files_scanned']++;
        }
        
        $results['finished_at'] = time();
        $results['duration'] = $results['finished_at'] - $results['started_at'];
        $results['status'] = 'completed';
        
        $this->scanResults[$results['scan_id']] = $results;
        
        return $results;
    }
    
    /**
     * Get files to scan
     */
    private function getFilesToScan(string $path): array
    {
        // Simulate file discovery
        return [
            $path . '/src/Controller/UserController.php',
            $path . '/src/Service/AuthService.php',
            $path . '/src/Model/User.php',
            $path . '/config/database.php',
            $path . '/public/index.php'
        ];
    }
    
    /**
     * Scan individual file
     */
    private function scanFile(string $filename): array
    {
        $vulnerabilities = [];
        
        // Simulate file content
        $content = $this->getFileContent($filename);
        
        foreach ($this->rules as $ruleName => $rule) {
            if (preg_match($rule['pattern'], $content)) {
                $vulnerabilities[] = [
                    'rule' => $ruleName,
                    'file' => $filename,
                    'line' => rand(1, 100), // Simulated line number
                    'severity' => $rule['severity'],
                    'description' => $rule['description'],
                    'code_snippet' => $this->getCodeSnippet($content, $ruleName)
                ];
            }
        }
        
        return $vulnerabilities;
    }
    
    /**
     * Get file content (simulated)
     */
    private function getFileContent(string $filename): string
    {
        // Simulate different file contents based on filename
        if (strpos($filename, 'Controller') !== false) {
            return '<?php
class UserController {
    public function getUser($id) {
        $sql = "SELECT * FROM users WHERE id = " . $_GET[\'id\'];
        $result = $this->db->query($sql);
        return $result;
    }
    
    public function login() {
        $username = $_POST[\'username\'];
        $password = $_POST[\'password\'];
        echo "Welcome " . $username;
    }
}';
        } elseif (strpos($filename, 'AuthService') !== false) {
            return '<?php
class AuthService {
    private $secret_key = "hardcoded_secret_key_123";
    
    public function validateToken($token) {
        return hash_equals($this->secret_key, $token);
    }
    
    public function executeCommand($cmd) {
        return shell_exec($cmd);
    }
}';
        } elseif (strpos($filename, 'database') !== false) {
            return '<?php
return [
    \'host\' => \'localhost\',
    \'username\' => \'root\',
    \'password\' => \'root_password_123\',
    \'database\' => \'app\'
];';
        }
        
        return '<?php echo "Hello World"; ?>';
    }
    
    /**
     * Get code snippet
     */
    private function getCodeSnippet(string $content, string $ruleName): string
    {
        // Return relevant code snippet based on rule
        switch ($ruleName) {
            case 'sql_injection':
                return '$sql = "SELECT * FROM users WHERE id = " . $_GET[\'id\'];';
            case 'xss':
                return 'echo "Welcome " . $username;';
            case 'hardcoded_password':
                return 'password = \'root_password_123\'';
            case 'shell_exec':
                return 'return shell_exec($cmd);';
            default:
                return 'Vulnerable code snippet';
        }
    }
    
    /**
     * Scan dependencies for vulnerabilities
     */
    public function scanDependencies(): array
    {
        $results = [
            'scan_id' => uniqid('depscan_'),
            'started_at' => time(),
            'dependencies' => [],
            'vulnerabilities' => [],
            'status' => 'running'
        ];
        
        foreach ($this->dependencies as $manager => $deps) {
            foreach ($deps as $name => $info) {
                $depResult = [
                    'manager' => $manager,
                    'name' => $name,
                    'version' => $info['version'],
                    'vulnerabilities' => $info['vulnerabilities']
                ];
                
                $results['dependencies'][] = $depResult;
                $results['vulnerabilities'] = array_merge(
                    $results['vulnerabilities'],
                    $info['vulnerabilities']
                );
            }
        }
        
        $results['finished_at'] = time();
        $results['duration'] = $results['finished_at'] - $results['started_at'];
        $results['status'] => 'completed';
        
        return $results;
    }
    
    /**
     * Generate security report
     */
    public function generateReport(string $scanId): array
    {
        if (!isset($this->scanResults[$scanId])) {
            return ['error' => 'Scan not found'];
        }
        
        $scan = $this->scanResults[$scanId];
        
        $report = [
            'scan_id' => $scanId,
            'generated_at' => time(),
            'summary' => [
                'files_scanned' => $scan['files_scanned'],
                'total_vulnerabilities' => count($scan['vulnerabilities']),
                'critical' => 0,
                'high' => 0,
                'medium' => 0,
                'low' => 0
            ],
            'vulnerabilities' => $scan['vulnerabilities'],
            'recommendations' => $this->generateRecommendations($scan['vulnerabilities'])
        ];
        
        // Count by severity
        foreach ($scan['vulnerabilities'] as $vuln) {
            $report['summary'][$vuln['severity']]++;
        }
        
        return $report;
    }
    
    /**
     * Generate recommendations
     */
    private function generateRecommendations(array $vulnerabilities): array
    {
        $recommendations = [];
        
        foreach ($vulnerabilities as $vuln) {
            switch ($vuln['rule']) {
                case 'sql_injection':
                    $recommendations[] = [
                        'type' => 'security',
                        'priority' => 'high',
                        'description' => 'Use prepared statements or parameterized queries to prevent SQL injection',
                        'code_example' => '$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");'
                    ];
                    break;
                case 'xss':
                    $recommendations[] = [
                        'type' => 'security',
                        'priority' => 'high',
                        'description' => 'Sanitize user input before outputting to prevent XSS attacks',
                        'code_example' => 'echo htmlspecialchars($username, ENT_QUOTES, \'UTF-8\');'
                    ];
                    break;
                case 'hardcoded_password':
                    $recommendations[] = [
                        'type' => 'security',
                        'priority' => 'critical',
                        'description' => 'Remove hardcoded passwords and use environment variables',
                        'code_example' => 'password = getenv(\'DB_PASSWORD\');'
                    ];
                    break;
                case 'eval_usage':
                    $recommendations[] = [
                        'type' => 'security',
                        'priority' => 'high',
                        'description' => 'Avoid using eval() function as it can execute arbitrary code',
                        'code_example' => 'Use safer alternatives like json_decode() or proper parsing'
                    ];
                    break;
            }
        }
        
        return array_unique($recommendations, SORT_REGULAR);
    }
    
    /**
     * Get scan results
     */
    public function getScanResults(): array
    {
        return $this->scanResults;
    }
    
    /**
     * Get security rules
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}

// Compliance Checker
class ComplianceChecker
{
    private array $standards = [];
    private array $checks = [];
    private array $results = [];
    
    public function __construct()
    {
        $this->initializeStandards();
        $this->initializeChecks();
    }
    
    /**
     * Initialize compliance standards
     */
    private function initializeStandards(): void
    {
        $this->standards = [
            'OWASP_TOP_10' => [
                'name' => 'OWASP Top 10',
                'description' => 'OWASP Top 10 Web Application Security Risks',
                'version' => '2021'
            ],
            'PCI_DSS' => [
                'name' => 'PCI DSS',
                'description' => 'Payment Card Industry Data Security Standard',
                'version' => '3.2.1'
            ],
            'GDPR' => [
                'name' => 'GDPR',
                'description' => 'General Data Protection Regulation',
                'version' => '2018'
            ],
            'SOC2' => [
                'name' => 'SOC 2',
                'description' => 'Service Organization Control 2',
                'version' => 'Type II'
            ]
        ];
    }
    
    /**
     * Initialize compliance checks
     */
    private function initializeChecks(): void
    {
        $this->checks = [
            'owasp_a01' => [
                'standard' => 'OWASP_TOP_10',
                'category' => 'Broken Access Control',
                'description' => 'Verify proper access control mechanisms',
                'checks' => [
                    'authentication_mechanism',
                    'authorization_checks',
                    'rate_limiting'
                ]
            ],
            'owasp_a02' => [
                'standard' => 'OWASP_TOP_10',
                'category' => 'Cryptographic Failures',
                'description' => 'Verify proper cryptographic implementation',
                'checks' => [
                    'encryption_at_rest',
                    'encryption_in_transit',
                    'key_management'
                ]
            ],
            'owasp_a03' => [
                'standard' => 'OWASP_TOP_10',
                'category' => 'Injection',
                'description' => 'Verify protection against injection attacks',
                'checks' => [
                    'sql_injection_protection',
                    'xss_protection',
                    'input_validation'
                ]
            ],
            'pci_dss_3' => [
                'standard' => 'PCI_DSS',
                'category' => 'Protect Cardholder Data',
                'description' => 'Verify protection of cardholder data',
                'checks' => [
                    'data_encryption',
                    'access_control',
                    'logging_monitoring'
                ]
            ],
            'gdpr_article_32' => [
                'standard' => 'GDPR',
                'category' => 'Security of Processing',
                'description' => 'Verify appropriate security measures',
                'checks' => [
                    'data_protection',
                    'incident_response',
                    'data_breach_notification'
                ]
            ]
        ];
    }
    
    /**
     * Run compliance check
     */
    public function runComplianceCheck(string $standard): array
    {
        if (!isset($this->standards[$standard])) {
            return ['error' => 'Standard not found'];
        }
        
        $results = [
            'standard' => $standard,
            'standard_info' => $this->standards[$standard],
            'started_at' => time(),
            'checks' => [],
            'summary' => [
                'total_checks' => 0,
                'passed' => 0,
                'failed' => 0,
                'warning' => 0,
                'compliance_score' => 0
            ],
            'status' => 'running'
        ];
        
        // Run relevant checks
        foreach ($this->checks as $checkId => $check) {
            if ($check['standard'] === $standard) {
                $checkResult = $this->runIndividualCheck($checkId, $check);
                $results['checks'][] = $checkResult;
                $results['summary']['total_checks']++;
                
                if ($checkResult['status'] === 'passed') {
                    $results['summary']['passed']++;
                } elseif ($checkResult['status'] === 'failed') {
                    $results['summary']['failed']++;
                } else {
                    $results['summary']['warning']++;
                }
            }
        }
        
        // Calculate compliance score
        if ($results['summary']['total_checks'] > 0) {
            $results['summary']['compliance_score'] = round(
                ($results['summary']['passed'] / $results['summary']['total_checks']) * 100,
                2
            );
        }
        
        $results['finished_at'] = time();
        $results['duration'] = $results['finished_at'] - $results['started_at'];
        $results['status'] = 'completed';
        
        $this->results[$standard] = $results;
        
        return $results;
    }
    
    /**
     * Run individual check
     */
    private function runIndividualCheck(string $checkId, array $check): array
    {
        $result = [
            'check_id' => $checkId,
            'category' => $check['category'],
            'description' => $check['description'],
            'checks' => [],
            'status' => 'passed',
            'issues' => []
        ];
        
        foreach ($check['checks'] as $individualCheck) {
            $checkResult = $this->performComplianceCheck($individualCheck);
            $result['checks'][] = $checkResult;
            
            if ($checkResult['status'] === 'failed') {
                $result['status'] = 'failed';
                $result['issues'][] = $checkResult['issue'];
            } elseif ($checkResult['status'] === 'warning' && $result['status'] === 'passed') {
                $result['status'] = 'warning';
            }
        }
        
        return $result;
    }
    
    /**
     * Perform individual compliance check
     */
    private function performComplianceCheck(string $check): array
    {
        // Simulate compliance checks
        $checks = [
            'authentication_mechanism' => [
                'status' => 'passed',
                'message' => 'Authentication mechanism is properly implemented'
            ],
            'authorization_checks' => [
                'status' => 'warning',
                'message' => 'Authorization checks exist but need improvement',
                'issue' => 'Some endpoints lack proper authorization'
            ],
            'rate_limiting' => [
                'status' => 'failed',
                'message' => 'Rate limiting not implemented',
                'issue' => 'API endpoints are vulnerable to brute force attacks'
            ],
            'encryption_at_rest' => [
                'status' => 'passed',
                'message' => 'Data is encrypted at rest'
            ],
            'encryption_in_transit' => [
                'status' => 'passed',
                'message' => 'HTTPS is properly configured'
            ],
            'key_management' => [
                'status' => 'warning',
                'message' => 'Key management needs improvement',
                'issue' => 'Keys are not rotated regularly'
            ],
            'sql_injection_protection' => [
                'status' => 'failed',
                'message' => 'SQL injection vulnerabilities found',
                'issue' => 'Some queries use string concatenation'
            ],
            'xss_protection' => [
                'status' => 'warning',
                'message' => 'XSS protection partially implemented',
                'issue' => 'Some outputs are not properly escaped'
            ],
            'input_validation' => [
                'status' => 'passed',
                'message' => 'Input validation is implemented'
            ],
            'data_encryption' => [
                'status' => 'passed',
                'message' => 'Sensitive data is encrypted'
            ],
            'access_control' => [
                'status' => 'failed',
                'message' => 'Access control issues found',
                'issue' => 'Some users have excessive permissions'
            ],
            'logging_monitoring' => [
                'status' => 'passed',
                'message' => 'Logging and monitoring are properly configured'
            ],
            'data_protection' => [
                'status' => 'passed',
                'message' => 'Data protection measures are in place'
            ],
            'incident_response' => [
                'status' => 'warning',
                'message' => 'Incident response plan needs updating',
                'issue' => 'Response procedures are outdated'
            ],
            'data_breach_notification' => [
                'status' => 'passed',
                'message' => 'Data breach notification procedures are defined'
            ]
        ];
        
        return $checks[$check] ?? [
            'status' => 'warning',
            'message' => 'Check not implemented'
        ];
    }
    
    /**
     * Generate compliance report
     */
    public function generateComplianceReport(string $standard): array
    {
        if (!isset($this->results[$standard])) {
            return ['error' => 'Compliance check not found'];
        }
        
        $result = $this->results[$standard];
        
        $report = [
            'standard' => $standard,
            'standard_info' => $result['standard_info'],
            'generated_at' => time(),
            'summary' => $result['summary'],
            'detailed_results' => $result['checks'],
            'recommendations' => $this->generateComplianceRecommendations($result['checks']),
            'next_steps' => $this->generateNextSteps($result['checks'])
        ];
        
        return $report;
    }
    
    /**
     * Generate compliance recommendations
     */
    private function generateComplianceRecommendations(array $checks): array
    {
        $recommendations = [];
        
        foreach ($checks as $check) {
            if ($check['status'] === 'failed' || $check['status'] === 'warning') {
                foreach ($check['issues'] as $issue) {
                    $recommendations[] = [
                        'category' => $check['category'],
                        'priority' => $check['status'] === 'failed' ? 'high' : 'medium',
                        'issue' => $issue,
                        'recommendation' => $this->getRecommendationForIssue($issue)
                    ];
                }
            }
        }
        
        return $recommendations;
    }
    
    /**
     * Get recommendation for issue
     */
    private function getRecommendationForIssue(string $issue): string
    {
        $recommendations = [
            'API endpoints are vulnerable to brute force attacks' => 'Implement rate limiting on all API endpoints',
            'Keys are not rotated regularly' => 'Implement automated key rotation policy',
            'Some queries use string concatenation' => 'Use prepared statements for all database queries',
            'Some outputs are not properly escaped' => 'Implement output encoding for all user-provided data',
            'Some users have excessive permissions' => 'Review and implement principle of least privilege',
            'Response procedures are outdated' => 'Update incident response procedures and conduct regular drills'
        ];
        
        return $recommendations[$issue] ?? 'Review and address the identified security issue';
    }
    
    /**
     * Generate next steps
     */
    private function generateNextSteps(array $checks): array
    {
        $steps = [
            'Address all failed compliance checks immediately',
            'Implement remediation plan for warning issues',
            'Schedule regular compliance assessments',
            'Update security policies and procedures',
            'Conduct security awareness training',
            'Implement continuous monitoring',
            'Document all security measures'
        ];
        
        return $steps;
    }
    
    /**
     * Get compliance results
     */
    public function getComplianceResults(): array
    {
        return $this->results;
    }
    
    /**
     * Get standards
     */
    public function getStandards(): array
    {
        return $this->standards;
    }
}

// Security Pipeline Integration
class SecurityPipeline
{
    private SecurityScanner $scanner;
    private ComplianceChecker $compliance;
    private array $pipelineConfig;
    
    public function __construct()
    {
        $this->scanner = new SecurityScanner();
        $this->compliance = new ComplianceChecker();
        $this->initializePipelineConfig();
    }
    
    /**
     * Initialize pipeline configuration
     */
    private function initializePipelineConfig(): void
    {
        $this->pipelineConfig = [
            'stages' => [
                'security_scan' => [
                    'enabled' => true,
                    'fail_on_critical' => true,
                    'fail_on_high' => false
                ],
                'dependency_check' => [
                    'enabled' => true,
                    'fail_on_critical' => true,
                    'fail_on_high' => true
                ],
                'compliance_check' => [
                    'enabled' => true,
                    'standards' => ['OWASP_TOP_10'],
                    'fail_threshold' => 80
                ]
            ],
            'notifications' => [
                'email' => ['security@example.com'],
                'slack' => ['#security-alerts']
            ]
        ];
    }
    
    /**
     * Run security pipeline
     */
    public function runPipeline(string $codePath): array
    {
        $pipelineResult = [
            'pipeline_id' => uniqid('pipeline_'),
            'started_at' => time(),
            'stages' => [],
            'status' => 'running',
            'passed' => true,
            'issues' => []
        ];
        
        // Stage 1: Security Scan
        if ($this->pipelineConfig['stages']['security_scan']['enabled']) {
            $stageResult = $this->runSecurityScanStage($codePath);
            $pipelineResult['stages']['security_scan'] = $stageResult;
            
            if (!$stageResult['passed']) {
                $pipelineResult['passed'] = false;
                $pipelineResult['issues'] = array_merge($pipelineResult['issues'], $stageResult['issues']);
            }
        }
        
        // Stage 2: Dependency Check
        if ($this->pipelineConfig['stages']['dependency_check']['enabled']) {
            $stageResult = $this->runDependencyCheckStage();
            $pipelineResult['stages']['dependency_check'] = $stageResult;
            
            if (!$stageResult['passed']) {
                $pipelineResult['passed'] = false;
                $pipelineResult['issues'] = array_merge($pipelineResult['issues'], $stageResult['issues']);
            }
        }
        
        // Stage 3: Compliance Check
        if ($this->pipelineConfig['stages']['compliance_check']['enabled']) {
            $stageResult = $this->runComplianceCheckStage();
            $pipelineResult['stages']['compliance_check'] = $stageResult;
            
            if (!$stageResult['passed']) {
                $pipelineResult['passed'] = false;
                $pipelineResult['issues'] = array_merge($pipelineResult['issues'], $stageResult['issues']);
            }
        }
        
        $pipelineResult['finished_at'] = time();
        $pipelineResult['duration'] = $pipelineResult['finished_at'] - $pipelineResult['started_at'];
        $pipelineResult['status'] = 'completed';
        
        return $pipelineResult;
    }
    
    /**
     * Run security scan stage
     */
    private function runSecurityScanStage(string $codePath): array
    {
        $config = $this->pipelineConfig['stages']['security_scan'];
        
        $scanResult = $this->scanner->scanSourceCode($codePath);
        
        $stageResult = [
            'stage' => 'security_scan',
            'scan_id' => $scanResult['scan_id'],
            'vulnerabilities' => $scanResult['vulnerabilities'],
            'passed' => true,
            'issues' => []
        ];
        
        // Check if pipeline should fail
        $criticalCount = 0;
        $highCount = 0;
        
        foreach ($scanResult['vulnerabilities'] as $vuln) {
            if ($vuln['severity'] === 'critical') {
                $criticalCount++;
            } elseif ($vuln['severity'] === 'high') {
                $highCount++;
            }
        }
        
        if ($config['fail_on_critical'] && $criticalCount > 0) {
            $stageResult['passed'] = false;
            $stageResult['issues'][] = "Found $criticalCount critical vulnerabilities";
        }
        
        if ($config['fail_on_high'] && $highCount > 0) {
            $stageResult['passed'] = false;
            $stageResult['issues'][] = "Found $highCount high vulnerabilities";
        }
        
        return $stageResult;
    }
    
    /**
     * Run dependency check stage
     */
    private function runDependencyCheckStage(): array
    {
        $config = $this->pipelineConfig['stages']['dependency_check'];
        
        $scanResult = $this->scanner->scanDependencies();
        
        $stageResult = [
            'stage' => 'dependency_check',
            'scan_id' => $scanResult['scan_id'],
            'vulnerabilities' => $scanResult['vulnerabilities'],
            'passed' => true,
            'issues' => []
        ];
        
        // Check if pipeline should fail
        $criticalCount = 0;
        $highCount = 0;
        
        foreach ($scanResult['vulnerabilities'] as $vuln) {
            if ($vuln['severity'] === 'critical') {
                $criticalCount++;
            } elseif ($vuln['severity'] === 'high') {
                $highCount++;
            }
        }
        
        if ($config['fail_on_critical'] && $criticalCount > 0) {
            $stageResult['passed'] = false;
            $stageResult['issues'][] = "Found $criticalCount critical dependency vulnerabilities";
        }
        
        if ($config['fail_on_high'] && $highCount > 0) {
            $stageResult['passed'] = false;
            $stageResult['issues'][] = "Found $highCount high dependency vulnerabilities";
        }
        
        return $stageResult;
    }
    
    /**
     * Run compliance check stage
     */
    private function runComplianceCheckStage(): array
    {
        $config = $this->pipelineConfig['stages']['compliance_check'];
        
        $stageResult = [
            'stage' => 'compliance_check',
            'standards' => [],
            'passed' => true,
            'issues' => []
        ];
        
        foreach ($config['standards'] as $standard) {
            $complianceResult = $this->compliance->runComplianceCheck($standard);
            $stageResult['standards'][$standard] = $complianceResult;
            
            if ($complianceResult['summary']['compliance_score'] < $config['fail_threshold']) {
                $stageResult['passed'] = false;
                $stageResult['issues'][] = "$standard compliance score ({$complianceResult['summary']['compliance_score']}%) below threshold ({$config['fail_threshold']}%)";
            }
        }
        
        return $stageResult;
    }
    
    /**
     * Generate pipeline report
     */
    public function generatePipelineReport(string $pipelineId): array
    {
        // In real implementation, fetch pipeline result by ID
        // For demo, return sample report structure
        return [
            'pipeline_id' => $pipelineId,
            'generated_at' => time(),
            'executive_summary' => [
                'status' => 'passed',
                'total_issues' => 3,
                'critical_issues' => 1,
                'high_issues' => 2,
                'compliance_score' => 85.5
            ],
            'detailed_findings' => [
                'security_vulnerabilities' => 2,
                'dependency_issues' => 1,
                'compliance_gaps' => 1
            ],
            'recommendations' => [
                'Fix critical SQL injection vulnerability',
                'Update vulnerable dependencies',
                'Implement missing security controls'
            ],
            'next_steps' => [
                'Address critical issues immediately',
                'Schedule remediation for high issues',
                'Plan compliance improvements'
            ]
        ];
    }
}

// Security DevOps Examples
class SecurityDevOpsExamples
{
    private SecurityScanner $scanner;
    private ComplianceChecker $compliance;
    private SecurityPipeline $pipeline;
    
    public function __construct()
    {
        $this->scanner = new SecurityScanner();
        $this->compliance = new ComplianceChecker();
        $this->pipeline = new SecurityPipeline();
    }
    
    public function demonstrateSecurityScanning(): void
    {
        echo "Security Scanning Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Show security rules
        echo "Security Rules:\n";
        $rules = $this->scanner->getRules();
        foreach ($rules as $ruleName => $rule) {
            echo "$ruleName: {$rule['description']} ({$rule['severity']})\n";
        }
        echo "\n";
        
        // Run source code scan
        echo "Scanning Source Code:\n";
        $scanResult = $this->scanner->scanSourceCode('/var/www/app');
        
        echo "Scan ID: {$scanResult['scan_id']}\n";
        echo "Files Scanned: {$scanResult['files_scanned']}\n";
        echo "Vulnerabilities Found: " . count($scanResult['vulnerabilities']) . "\n\n";
        
        // Show vulnerabilities
        if (!empty($scanResult['vulnerabilities'])) {
            echo "Vulnerabilities:\n";
            foreach ($scanResult['vulnerabilities'] as $vuln) {
                echo "[{$vuln['severity']}] {$vuln['description']}\n";
                echo "  File: {$vuln['file']}:{$vuln['line']}\n";
                echo "  Code: {$vuln['code_snippet']}\n\n";
            }
        }
        
        // Run dependency scan
        echo "Scanning Dependencies:\n";
        $depResult = $this->scanner->scanDependencies();
        
        echo "Dependencies Scanned: " . count($depResult['dependencies']) . "\n";
        echo "Vulnerabilities Found: " . count($depResult['vulnerabilities']) . "\n\n";
        
        if (!empty($depResult['vulnerabilities'])) {
            echo "Dependency Vulnerabilities:\n";
            foreach ($depResult['vulnerabilities'] as $vuln) {
                echo "[{$vuln['severity']}] {$vuln['id']}: {$vuln['description']}\n";
            }
        }
        
        // Generate report
        echo "\nSecurity Report:\n";
        $report = $this->scanner->generateReport($scanResult['scan_id']);
        
        echo "Summary:\n";
        echo "  Total Vulnerabilities: {$report['summary']['total_vulnerabilities']}\n";
        echo "  Critical: {$report['summary']['critical']}\n";
        echo "  High: {$report['summary']['high']}\n";
        echo "  Medium: {$report['summary']['medium']}\n";
        echo "  Low: {$report['summary']['low']}\n\n";
        
        echo "Recommendations:\n";
        foreach (array_slice($report['recommendations'], 0, 3) as $rec) {
            echo "  • {$rec['description']}\n";
            echo "    Example: {$rec['code_example']}\n\n";
        }
    }
    
    public function demonstrateCompliance(): void
    {
        echo "\nCompliance Checking Demo\n";
        echo str_repeat("-", 28) . "\n";
        
        // Show standards
        echo "Compliance Standards:\n";
        $standards = $this->compliance->getStandards();
        foreach ($standards as $standardId => $standard) {
            echo "$standardId: {$standard['name']} v{$standard['version']}\n";
            echo "  {$standard['description']}\n\n";
        }
        
        // Run OWASP compliance check
        echo "Running OWASP Top 10 Compliance Check:\n";
        $complianceResult = $this->compliance->runComplianceCheck('OWASP_TOP_10');
        
        echo "Compliance Score: {$complianceResult['summary']['compliance_score']}%\n";
        echo "Total Checks: {$complianceResult['summary']['total_checks']}\n";
        echo "Passed: {$complianceResult['summary']['passed']}\n";
        echo "Failed: {$complianceResult['summary']['failed']}\n";
        echo "Warnings: {$complianceResult['summary']['warning']}\n\n";
        
        // Show detailed results
        echo "Detailed Results:\n";
        foreach ($complianceResult['checks'] as $check) {
            echo "[{$check['status']}] {$check['category']}\n";
            echo "  {$check['description']}\n";
            
            if (!empty($check['issues'])) {
                foreach ($check['issues'] as $issue) {
                    echo "  Issue: $issue\n";
                }
            }
            echo "\n";
        }
        
        // Generate compliance report
        echo "Compliance Report:\n";
        $report = $this->compliance->generateComplianceReport('OWASP_TOP_10');
        
        echo "Recommendations:\n";
        foreach (array_slice($report['recommendations'], 0, 3) as $rec) {
            echo "  [{$rec['priority']}] {$rec['issue']}\n";
            echo "    Recommendation: {$rec['recommendation']}\n\n";
        }
        
        echo "Next Steps:\n";
        foreach ($report['next_steps'] as $step) {
            echo "  • $step\n";
        }
    }
    
    public function demonstrateSecurityPipeline(): void
    {
        echo "\nSecurity Pipeline Demo\n";
        echo str_repeat("-", 24) . "\n";
        
        // Run security pipeline
        echo "Running Security Pipeline:\n";
        $pipelineResult = $this->pipeline->runPipeline('/var/www/app');
        
        echo "Pipeline ID: {$pipelineResult['pipeline_id']}\n";
        echo "Status: {$pipelineResult['status']}\n";
        echo "Passed: " . ($pipelineResult['passed'] ? 'Yes' : 'No') . "\n";
        echo "Duration: {$pipelineResult['duration']}s\n\n";
        
        // Show stage results
        echo "Stage Results:\n";
        foreach ($pipelineResult['stages'] as $stageName => $stage) {
            echo "$stageName: " . ($stage['passed'] ? 'Passed' : 'Failed') . "\n";
            
            if ($stageName === 'security_scan') {
                echo "  Vulnerabilities: " . count($stage['vulnerabilities']) . "\n";
            } elseif ($stageName === 'dependency_check') {
                echo "  Dependency Issues: " . count($stage['vulnerabilities']) . "\n";
            } elseif ($stageName === 'compliance_check') {
                echo "  Standards Checked: " . count($stage['standards']) . "\n";
            }
            
            if (!empty($stage['issues'])) {
                foreach ($stage['issues'] as $issue) {
                    echo "  Issue: $issue\n";
                }
            }
            echo "\n";
        }
        
        // Generate pipeline report
        echo "Pipeline Report:\n";
        $report = $this->pipeline->generatePipelineReport($pipelineResult['pipeline_id']);
        
        echo "Executive Summary:\n";
        echo "  Status: {$report['executive_summary']['status']}\n";
        echo "  Total Issues: {$report['executive_summary']['total_issues']}\n";
        echo "  Critical Issues: {$report['executive_summary']['critical_issues']}\n";
        echo "  High Issues: {$report['executive_summary']['high_issues']}\n";
        echo "  Compliance Score: {$report['executive_summary']['compliance_score']}%\n\n";
        
        echo "Recommendations:\n";
        foreach ($report['recommendations'] as $rec) {
            echo "  • $rec\n";
        }
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nSecurity DevOps Best Practices\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Security Scanning:\n";
        echo "   • Scan code in CI/CD pipeline\n";
        echo "   • Use multiple scanning tools\n";
        echo "   • Scan dependencies regularly\n";
        echo "   • Fail build on critical issues\n";
        echo "   • Track vulnerability remediation\n\n";
        
        echo "2. Compliance Management:\n";
        echo "   • Identify applicable standards\n";
        echo "   • Automate compliance checks\n";
        echo "   • Generate compliance reports\n";
        echo "   • Track compliance metrics\n";
        echo "   • Plan for audit requirements\n\n";
        
        echo "3. Pipeline Integration:\n";
        echo "   • Shift security left\n";
        echo "   • Implement security gates\n";
        echo "   • Use security testing tools\n";
        echo "   • Automate security checks\n";
        echo "   • Monitor pipeline security\n\n";
        
        echo "4. Vulnerability Management:\n";
        echo "   • Prioritize by severity\n";
        echo "   • Track remediation time\n";
        echo "   • Use vulnerability databases\n";
        echo "   • Implement patch management\n";
        echo "   • Monitor for new vulnerabilities\n\n";
        
        echo "5. Security Culture:\n";
        echo "   • Train development teams\n";
        echo "   • Establish security champions\n";
        echo "   • Use secure coding practices\n";
        echo "   • Conduct security reviews\n";
        echo "   • Share security knowledge";
    }
    
    public function runAllExamples(): void
    {
        echo "Security DevOps Examples\n";
        echo str_repeat("=", 25) . "\n";
        
        $this->demonstrateSecurityScanning();
        $this->demonstrateCompliance();
        $this->demonstrateSecurityPipeline();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runSecurityDevOpsDemo(): void
{
    $examples = new SecurityDevOpsExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runSecurityDevOpsDemo();
}
?>
