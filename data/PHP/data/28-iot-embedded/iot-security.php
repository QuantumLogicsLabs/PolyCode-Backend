<?php
/**
 * IoT Security in PHP
 * 
 * Security considerations, vulnerabilities, and best practices for IoT systems.
 */

// IoT Security Manager
class IoTSecurityManager
{
    private array $devices = [];
    private array $securityPolicies = [];
    private array $threats = [];
    private array $auditLog = [];
    
    public function __construct()
    {
        $this->initializeSecurityPolicies();
        $this->initializeThreats();
    }
    
    private function initializeSecurityPolicies(): void
    {
        $this->securityPolicies = [
            'authentication' => [
                'require_device_auth' => true,
                'password_min_length' => 12,
                'password_complexity' => true,
                'session_timeout' => 3600,
                'max_login_attempts' => 3
            ],
            'encryption' => [
                'require_tls' => true,
                'encryption_algorithm' => 'AES-256',
                'key_rotation_interval' => 86400,
                'certificate_validation' => true
            ],
            'access_control' => [
                'principle_of_least_privilege' => true,
                'role_based_access' => true,
                'time_based_access' => true,
                'geo_fencing' => false
            ],
            'data_protection' => [
                'data_at_rest_encryption' => true,
                'data_in_transit_encryption' => true,
                'data_retention_policy' => 90,
                'audit_trail' => true
            ],
            'network_security' => [
                'firewall_enabled' => true,
                'intrusion_detection' => true,
                'dos_protection' => true,
                'port_scanning_protection' => true
            ]
        ];
    }
    
    private function initializeThreats(): void
    {
        $this->threats = [
            'unauthorized_access' => [
                'description' => 'Unauthorized access to IoT devices',
                'severity' => 'high',
                'mitigation' => ['strong_authentication', 'access_control', 'monitoring']
            ],
            'data_interception' => [
                'description' => 'Interception of IoT data in transit',
                'severity' => 'high',
                'mitigation' => ['encryption', 'secure_protocols', 'network_isolation']
            ],
            'device_compromise' => [
                'description' => 'Compromise of IoT device firmware/software',
                'severity' => 'critical',
                'mitigation' => ['firmware_updates', 'code_signing', 'secure_boot']
            ],
            'denial_of_service' => [
                'description' => 'Denial of service attacks on IoT devices',
                'severity' => 'medium',
                'mitigation' => ['rate_limiting', 'dos_protection', 'redundancy']
            ],
            'physical_tampering' => [
                'description' => 'Physical tampering with IoT devices',
                'severity' => 'medium',
                'mitigation' => ['tamper_detection', 'secure_enclosure', 'monitoring']
            ]
        ];
    }
    
    public function registerDevice(IoTDevice $device, array $securityConfig = []): bool
    {
        $deviceId = $device->getId();
        
        // Validate device security requirements
        if (!$this->validateDeviceSecurity($device, $securityConfig)) {
            echo "Device security validation failed: $deviceId\n";
            return false;
        }
        
        $this->devices[$deviceId] = [
            'device' => $device,
            'security_config' => array_merge($this->getDefaultSecurityConfig(), $securityConfig),
            'last_security_check' => time(),
            'security_score' => $this->calculateSecurityScore($device, $securityConfig),
            'vulnerabilities' => $this->scanForVulnerabilities($device)
        ];
        
        echo "Device registered with security: $deviceId\n";
        echo "Security score: {$this->devices[$deviceId]['security_score']}/100\n";
        
        $this->logAuditEvent('device_registered', $deviceId, [
            'security_score' => $this->devices[$deviceId]['security_score']
        ]);
        
        return true;
    }
    
    private function validateDeviceSecurity(IoTDevice $device, array $config): bool
    {
        // Check authentication requirements
        if ($this->securityPolicies['authentication']['require_device_auth']) {
            if (!isset($config['authentication_method']) || 
                !in_array($config['authentication_method'], ['certificate', 'token', 'password'])) {
                echo "Authentication method not specified or invalid\n";
                return false;
            }
        }
        
        // Check encryption requirements
        if ($this->securityPolicies['encryption']['require_tls']) {
            if (!isset($config['encryption_enabled']) || !$config['encryption_enabled']) {
                echo "Encryption not enabled\n";
                return false;
            }
        }
        
        // Check access control
        if ($this->securityPolicies['access_control']['role_based_access']) {
            if (!isset($config['role']) || empty($config['role'])) {
                echo "Role not specified for access control\n";
                return false;
            }
        }
        
        return true;
    }
    
    private function getDefaultSecurityConfig(): array
    {
        return [
            'authentication_method' => 'certificate',
            'encryption_enabled' => true,
            'role' => 'device',
            'access_level' => 'basic',
            'monitoring_enabled' => true,
            'auto_update' => false,
            'tamper_detection' => true
        ];
    }
    
    private function calculateSecurityScore(IoTDevice $device, array $config): int
    {
        $score = 0;
        $maxScore = 100;
        
        // Authentication (25 points)
        if (isset($config['authentication_method'])) {
            switch ($config['authentication_method']) {
                case 'certificate':
                    $score += 25;
                    break;
                case 'token':
                    $score += 20;
                    break;
                case 'password':
                    $score += 15;
                    break;
            }
        }
        
        // Encryption (25 points)
        if ($config['encryption_enabled'] ?? false) {
            $score += 25;
        }
        
        // Access Control (20 points)
        if (isset($config['role']) && !empty($config['role'])) {
            $score += 20;
        }
        
        // Monitoring (15 points)
        if ($config['monitoring_enabled'] ?? false) {
            $score += 15;
        }
        
        // Additional Security (15 points)
        if (($config['tamper_detection'] ?? false) && ($config['auto_update'] ?? false)) {
            $score += 15;
        } elseif ($config['tamper_detection'] ?? false) {
            $score += 10;
        }
        
        return min($score, $maxScore);
    }
    
    private function scanForVulnerabilities(IoTDevice $device): array
    {
        $vulnerabilities = [];
        
        // Simulate vulnerability scanning
        $vulnerabilityChecks = [
            'weak_authentication' => rand(0, 1),
            'missing_encryption' => rand(0, 1),
            'outdated_firmware' => rand(0, 1),
            'open_ports' => rand(0, 1),
            'default_credentials' => rand(0, 1),
            'missing_patches' => rand(0, 1)
        ];
        
        foreach ($vulnerabilityChecks as $vulnerability => $detected) {
            if ($detected) {
                $vulnerabilities[] = [
                    'type' => $vulnerability,
                    'severity' => $this->getVulnerabilitySeverity($vulnerability),
                    'description' => $this->getVulnerabilityDescription($vulnerability),
                    'recommendation' => $this->getVulnerabilityRecommendation($vulnerability)
                ];
            }
        }
        
        return $vulnerabilities;
    }
    
    private function getVulnerabilitySeverity(string $vulnerability): string
    {
        $severityMap = [
            'weak_authentication' => 'high',
            'missing_encryption' => 'critical',
            'outdated_firmware' => 'medium',
            'open_ports' => 'medium',
            'default_credentials' => 'critical',
            'missing_patches' => 'high'
        ];
        
        return $severityMap[$vulnerability] ?? 'low';
    }
    
    private function getVulnerabilityDescription(string $vulnerability): string
    {
        $descriptions = [
            'weak_authentication' => 'Device uses weak authentication mechanism',
            'missing_encryption' => 'Device communication is not encrypted',
            'outdated_firmware' => 'Device firmware is outdated and may contain vulnerabilities',
            'open_ports' => 'Device has unnecessary open ports',
            'default_credentials' => 'Device still uses default credentials',
            'missing_patches' => 'Device is missing security patches'
        ];
        
        return $descriptions[$vulnerability] ?? 'Unknown vulnerability';
    }
    
    private function getVulnerabilityRecommendation(string $vulnerability): string
    {
        $recommendations = [
            'weak_authentication' => 'Implement certificate-based authentication',
            'missing_encryption' => 'Enable TLS/SSL encryption for all communications',
            'outdated_firmware' => 'Update device firmware to latest version',
            'open_ports' => 'Close unnecessary ports and services',
            'default_credentials' => 'Change default credentials to strong passwords',
            'missing_patches' => 'Apply all available security patches'
        ];
        
        return $recommendations[$vulnerability] ?? 'Contact security team';
    }
    
    public function authenticateDevice(string $deviceId, array $credentials): bool
    {
        if (!isset($this->devices[$deviceId])) {
            echo "Device not found: $deviceId\n";
            return false;
        }
        
        $deviceInfo = $this->devices[$deviceId];
        $authMethod = $deviceInfo['security_config']['authentication_method'];
        
        switch ($authMethod) {
            case 'certificate':
                return $this->authenticateWithCertificate($deviceId, $credentials);
            case 'token':
                return $this->authenticateWithToken($deviceId, $credentials);
            case 'password':
                return $this->authenticateWithPassword($deviceId, $credentials);
            default:
                echo "Unknown authentication method: $authMethod\n";
                return false;
        }
    }
    
    private function authenticateWithCertificate(string $deviceId, array $credentials): bool
    {
        echo "Authenticating device $deviceId with certificate\n";
        
        // Simulate certificate validation
        $certificateValid = isset($credentials['certificate']) && 
                           isset($credentials['signature']) &&
                           $this->validateCertificate($credentials['certificate']);
        
        if ($certificateValid) {
            $this->logAuditEvent('device_authenticated', $deviceId, [
                'method' => 'certificate',
                'timestamp' => time()
            ]);
        }
        
        return $certificateValid;
    }
    
    private function authenticateWithToken(string $deviceId, array $credentials): bool
    {
        echo "Authenticating device $deviceId with token\n";
        
        // Simulate token validation
        $tokenValid = isset($credentials['token']) && 
                     $this->validateToken($credentials['token']);
        
        if ($tokenValid) {
            $this->logAuditEvent('device_authenticated', $deviceId, [
                'method' => 'token',
                'timestamp' => time()
            ]);
        }
        
        return $tokenValid;
    }
    
    private function authenticateWithPassword(string $deviceId, array $credentials): bool
    {
        echo "Authenticating device $deviceId with password\n";
        
        // Simulate password validation
        $passwordValid = isset($credentials['password']) && 
                         strlen($credentials['password']) >= $this->securityPolicies['authentication']['password_min_length'];
        
        if ($passwordValid) {
            $this->logAuditEvent('device_authenticated', $deviceId, [
                'method' => 'password',
                'timestamp' => time()
            ]);
        }
        
        return $passwordValid;
    }
    
    private function validateCertificate(string $certificate): bool
    {
        // Simulate certificate validation
        return !empty($certificate) && strlen($certificate) > 100;
    }
    
    private function validateToken(string $token): bool
    {
        // Simulate token validation
        return !empty($token) && strlen($token) > 20;
    }
    
    public function encryptData(string $deviceId, string $data): string
    {
        if (!isset($this->devices[$deviceId])) {
            echo "Device not found: $deviceId\n";
            return '';
        }
        
        $encryptionMethod = $this->securityPolicies['encryption']['encryption_algorithm'];
        echo "Encrypting data for device $deviceId using $encryptionMethod\n";
        
        // Simulate encryption
        $encryptedData = base64_encode($data . '_encrypted_' . time());
        
        $this->logAuditEvent('data_encrypted', $deviceId, [
            'algorithm' => $encryptionMethod,
            'timestamp' => time()
        ]);
        
        return $encryptedData;
    }
    
    public function decryptData(string $deviceId, string $encryptedData): string
    {
        if (!isset($this->devices[$deviceId])) {
            echo "Device not found: $deviceId\n";
            return '';
        }
        
        echo "Decrypting data for device $deviceId\n";
        
        // Simulate decryption
        $parts = explode('_encrypted_', base64_decode($encryptedData));
        $decryptedData = $parts[0] ?? '';
        
        $this->logAuditEvent('data_decrypted', $deviceId, [
            'timestamp' => time()
        ]);
        
        return $decryptedData;
    }
    
    public function checkAccessRights(string $deviceId, string $resource, string $action): bool
    {
        if (!isset($this->devices[$deviceId])) {
            echo "Device not found: $deviceId\n";
            return false;
        }
        
        $deviceInfo = $this->devices[$deviceId];
        $role = $deviceInfo['security_config']['role'];
        $accessLevel = $deviceInfo['security_config']['access_level'];
        
        // Simulate access control check
        $hasAccess = $this->checkRoleAccess($role, $resource, $action) &&
                     $this->checkAccessLevel($accessLevel, $resource, $action);
        
        if ($hasAccess) {
            $this->logAuditEvent('access_granted', $deviceId, [
                'resource' => $resource,
                'action' => $action,
                'timestamp' => time()
            ]);
        } else {
            $this->logAuditEvent('access_denied', $deviceId, [
                'resource' => $resource,
                'action' => $action,
                'timestamp' => time()
            ]);
        }
        
        return $hasAccess;
    }
    
    private function checkRoleAccess(string $role, string $resource, string $action): bool
    {
        $rolePermissions = [
            'admin' => ['*'],
            'controller' => ['read', 'write', 'execute'],
            'sensor' => ['read'],
            'actuator' => ['read', 'write'],
            'device' => ['read']
        ];
        
        $permissions = $rolePermissions[$role] ?? ['read'];
        
        return in_array('*', $permissions) || in_array($action, $permissions);
    }
    
    private function checkAccessLevel(string $accessLevel, string $resource, string $action): bool
    {
        $levelPermissions = [
            'full' => ['*'],
            'advanced' => ['read', 'write', 'execute', 'configure'],
            'basic' => ['read', 'write'],
            'minimal' => ['read']
        ];
        
        $permissions = $levelPermissions[$accessLevel] ?? ['read'];
        
        return in_array('*', $permissions) || in_array($action, $permissions);
    }
    
    public function detectAnomalies(string $deviceId, array $deviceData): array
    {
        if (!isset($this->devices[$deviceId])) {
            echo "Device not found: $deviceId\n";
            return [];
        }
        
        $anomalies = [];
        
        // Check for unusual patterns
        if (isset($deviceData['value']) && is_numeric($deviceData['value'])) {
            $value = $deviceData['value'];
            
            // Check for outlier values
            if ($value > 100 || $value < 0) {
                $anomalies[] = [
                    'type' => 'value_out_of_range',
                    'severity' => 'medium',
                    'description' => "Device value $value is out of normal range",
                    'timestamp' => time()
                ];
            }
            
            // Check for rapid changes
            if (isset($deviceData['previous_value'])) {
                $change = abs($value - $deviceData['previous_value']);
                if ($change > 50) {
                    $anomalies[] = [
                        'type' => 'rapid_value_change',
                        'severity' => 'high',
                        'description' => "Rapid value change detected: $change",
                        'timestamp' => time()
                    ];
                }
            }
        }
        
        // Check for unusual timing
        if (isset($deviceData['timestamp'])) {
            $timeDiff = time() - $deviceData['timestamp'];
            if ($timeDiff > 300) { // 5 minutes
                $anomalies[] = [
                    'type' => 'old_data',
                    'severity' => 'low',
                    'description' => "Data is $timeDiff seconds old",
                    'timestamp' => time()
                ];
            }
        }
        
        // Check for security anomalies
        if (isset($deviceData['source_ip'])) {
            $sourceIp = $deviceData['source_ip'];
            if ($this->isSuspiciousIp($sourceIp)) {
                $anomalies[] = [
                    'type' => 'suspicious_ip',
                    'severity' => 'high',
                    'description' => "Connection from suspicious IP: $sourceIp",
                    'timestamp' => time()
                ];
            }
        }
        
        if (!empty($anomalies)) {
            $this->logAuditEvent('anomalies_detected', $deviceId, [
                'anomalies' => $anomalies,
                'timestamp' => time()
            ]);
        }
        
        return $anomalies;
    }
    
    private function isSuspiciousIp(string $ip): bool
    {
        // Simulate IP reputation check
        $suspiciousIps = ['192.168.1.100', '10.0.0.100', '172.16.0.100'];
        return in_array($ip, $suspiciousIps);
    }
    
    public function runSecurityAudit(string $deviceId = null): array
    {
        $auditResults = [];
        
        $devicesToAudit = $deviceId ? [$deviceId] : array_keys($this->devices);
        
        foreach ($devicesToAudit as $id) {
            if (!isset($this->devices[$id])) {
                continue;
            }
            
            $deviceInfo = $this->devices[$id];
            
            $auditResults[$id] = [
                'device_id' => $id,
                'security_score' => $deviceInfo['security_score'],
                'last_security_check' => $deviceInfo['last_security_check'],
                'vulnerabilities' => $deviceInfo['vulnerabilities'],
                'security_config' => $deviceInfo['security_config'],
                'compliance_score' => $this->calculateComplianceScore($deviceInfo),
                'recommendations' => $this->generateSecurityRecommendations($deviceInfo)
            ];
            
            // Update last security check time
            $this->devices[$id]['last_security_check'] = time();
        }
        
        $this->logAuditEvent('security_audit_completed', $deviceId ?? 'all_devices', [
            'devices_audited' => count($devicesToAudit),
            'timestamp' => time()
        ]);
        
        return $auditResults;
    }
    
    private function calculateComplianceScore(array $deviceInfo): int
    {
        $score = 0;
        $maxScore = 100;
        
        $config = $deviceInfo['security_config'];
        
        // Check authentication compliance
        if (isset($config['authentication_method'])) {
            $score += 20;
        }
        
        // Check encryption compliance
        if ($config['encryption_enabled'] ?? false) {
            $score += 20;
        }
        
        // Check monitoring compliance
        if ($config['monitoring_enabled'] ?? false) {
            $score += 15;
        }
        
        // Check tamper detection
        if ($config['tamper_detection'] ?? false) {
            $score += 15;
        }
        
        // Check auto updates
        if ($config['auto_update'] ?? false) {
            $score += 10;
        }
        
        // Check vulnerability count
        $vulnerabilityCount = count($deviceInfo['vulnerabilities']);
        if ($vulnerabilityCount === 0) {
            $score += 20;
        } elseif ($vulnerabilityCount <= 2) {
            $score += 10;
        }
        
        return min($score, $maxScore);
    }
    
    private function generateSecurityRecommendations(array $deviceInfo): array
    {
        $recommendations = [];
        $config = $deviceInfo['security_config'];
        $vulnerabilities = $deviceInfo['vulnerabilities'];
        
        // Check authentication
        if (!isset($config['authentication_method'])) {
            $recommendations[] = 'Implement device authentication';
        }
        
        // Check encryption
        if (!($config['encryption_enabled'] ?? false)) {
            $recommendations[] = 'Enable device encryption';
        }
        
        // Check monitoring
        if (!($config['monitoring_enabled'] ?? false)) {
            $recommendations[] = 'Enable device monitoring';
        }
        
        // Check vulnerabilities
        foreach ($vulnerabilities as $vulnerability) {
            $recommendations[] = $vulnerability['recommendation'];
        }
        
        return array_unique($recommendations);
    }
    
    public function generateSecurityReport(string $deviceId = null): string
    {
        $auditResults = $this->runSecurityAudit($deviceId);
        
        $report = "IoT Security Report\n";
        $report .= str_repeat("=", 50) . "\n";
        $report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($auditResults as $id => $result) {
            $report .= "Device: $id\n";
            $report .= str_repeat("-", 30) . "\n";
            $report .= "Security Score: {$result['security_score']}/100\n";
            $report .= "Compliance Score: {$result['compliance_score']}/100\n";
            $report .= "Last Security Check: " . date('Y-m-d H:i:s', $result['last_security_check']) . "\n";
            $report .= "Vulnerabilities: " . count($result['vulnerabilities']) . "\n\n";
            
            if (!empty($result['vulnerabilities'])) {
                $report .= "Vulnerabilities:\n";
                foreach ($result['vulnerabilities'] as $vuln) {
                    $report .= "  [{$vuln['severity']}] {$vuln['type']}: {$vuln['description']}\n";
                    $report .= "    Recommendation: {$vuln['recommendation']}\n";
                }
                $report .= "\n";
            }
            
            if (!empty($result['recommendations'])) {
                $report .= "Recommendations:\n";
                foreach ($result['recommendations'] as $rec) {
                    $report .= "  • $rec\n";
                }
                $report .= "\n";
            }
            
            $report .= str_repeat("-", 50) . "\n\n";
        }
        
        // Summary
        $report .= "Summary:\n";
        $totalDevices = count($auditResults);
        $avgSecurityScore = array_sum(array_column($auditResults, 'security_score')) / $totalDevices;
        $avgComplianceScore = array_sum(array_column($auditResults, 'compliance_score')) / $totalDevices;
        $totalVulnerabilities = array_sum(array_map(fn($r) => count($r['vulnerabilities']), $auditResults));
        
        $report .= "Total Devices: $totalDevices\n";
        $report .= "Average Security Score: " . round($avgSecurityScore, 1) . "/100\n";
        $report .= "Average Compliance Score: " . round($avgComplianceScore, 1) . "/100\n";
        $report .= "Total Vulnerabilities: $totalVulnerabilities\n";
        
        return $report;
    }
    
    public function getSecurityPolicies(): array
    {
        return $this->securityPolicies;
    }
    
    public function getThreats(): array
    {
        return $this->threats;
    }
    
    public function getAuditLog(): array
    {
        return $this->auditLog;
    }
    
    private function logAuditEvent(string $event, string $deviceId, array $details): void
    {
        $this->auditLog[] = [
            'event' => $event,
            'device_id' => $deviceId,
            'details' => $details,
            'timestamp' => time()
        ];
        
        // Keep audit log size manageable
        if (count($this->auditLog) > 1000) {
            array_shift($this->auditLog);
        }
    }
}

// Security Vulnerability Scanner
class SecurityVulnerabilityScanner
{
    private array $vulnerabilityDatabase;
    private array $scanResults;
    
    public function __construct()
    {
        $this->initializeVulnerabilityDatabase();
    }
    
    private function initializeVulnerabilityDatabase(): void
    {
        $this->vulnerabilityDatabase = [
            'weak_password' => [
                'pattern' => '/password|123456|admin|root/i',
                'severity' => 'high',
                'description' => 'Weak or default password detected',
                'cve' => 'CVE-2019-12345',
                'cvss' => 7.5
            ],
            'hardcoded_credentials' => [
                'pattern' => '/\$password\s*=\s*[\'"][^\'"]+[\'"]/',
                'severity' => 'critical',
                'description' => 'Hardcoded credentials found in code',
                'cve' => 'CVE-2020-54321',
                'cvss' => 9.8
            ],
            'insecure_protocol' => [
                'pattern' => '/http:\/\/|telnet|ftp/i',
                'severity' => 'medium',
                'description' => 'Insecure protocol usage detected',
                'cve' => 'CVE-2018-12345',
                'cvss' => 5.5
            ],
            'sql_injection' => [
                'pattern' => '/\$_GET|\$_POST|\$_REQUEST.*mysql/i',
                'severity' => 'critical',
                'description' => 'Potential SQL injection vulnerability',
                'cve' => 'CVE-2019-98765',
                'cvss' => 9.0
            ],
            'command_injection' => [
                'pattern' => '/exec\s*\(|shell_exec\s*\(|system\s*\(/i',
                'severity' => 'critical',
                'description' => 'Command injection vulnerability',
                'cve' => 'CVE-2021-54321',
                'cvss' => 9.5
            ],
            'xss_vulnerability' => [
                'pattern' => '/echo\s*\$.*<script|document\.write/i',
                'severity' => 'high',
                'description' => 'Cross-site scripting vulnerability',
                'cve' => 'CVE-2020-12345',
                'cvss' => 6.1
            ],
            'path_traversal' => [
                'pattern' => '/\.\.\/|\.\.\\\|\.\.%2f/i',
                'severity' => 'high',
                'description' => 'Path traversal vulnerability',
                'cve' => 'CVE-2019-54321',
                'cvss' => 7.0
            ],
            'buffer_overflow' => [
                'pattern' => '/strcpy|strcat|gets\s*\(/i',
                'severity' => 'high',
                'description' => 'Buffer overflow vulnerability',
                'cve' => 'CVE-2018-12345',
                'cvss' => 7.8
            ],
            'information_disclosure' => [
                'pattern' => '/var_dump|print_r|phpinfo/i',
                'severity' => 'medium',
                'description' => 'Information disclosure vulnerability',
                'cve' => 'CVE-2017-12345',
                'cvss' => 4.3
            ],
            'session_fixation' => [
                'pattern' => '/session_id.*=.*\$_GET|\$_POST/i',
                'severity' => 'high',
                'description' => 'Session fixation vulnerability',
                'cve' => 'CVE-2016-12345',
                'cvss' => 6.8
            ]
        ];
    }
    
    public function scanCode(string $code, string $filename = 'unknown'): array
    {
        $vulnerabilities = [];
        
        foreach ($this->vulnerabilityDatabase as $type => $vuln) {
            if (preg_match($vuln['pattern'], $code)) {
                $vulnerabilities[] = [
                    'type' => $type,
                    'severity' => $vuln['severity'],
                    'description' => $vuln['description'],
                    'cve' => $vuln['cve'],
                    'cvss' => $vuln['cvss'],
                    'filename' => $filename,
                    'line' => $this->findLineNumber($code, $vuln['pattern'])
                ];
            }
        }
        
        $this->scanResults[$filename] = $vulnerabilities;
        
        return $vulnerabilities;
    }
    
    private function findLineNumber(string $code, string $pattern): int
    {
        $lines = explode("\n", $code);
        
        foreach ($lines as $lineNumber => $line) {
            if (preg_match($pattern, $line)) {
                return $lineNumber + 1;
            }
        }
        
        return 0;
    }
    
    public function scanDirectory(string $directory): array
    {
        $results = [];
        
        if (!is_dir($directory)) {
            echo "Directory not found: $directory\n";
            return $results;
        }
        
        $files = glob($directory . '/*.php');
        
        foreach ($files as $file) {
            $code = file_get_contents($file);
            if ($code !== false) {
                $filename = basename($file);
                $vulnerabilities = $this->scanCode($code, $filename);
                
                if (!empty($vulnerabilities)) {
                    $results[$filename] = $vulnerabilities;
                }
            }
        }
        
        return $results;
    }
    
    public function getScanResults(): array
    {
        return $this->scanResults;
    }
    
    public function generateVulnerabilityReport(): string
    {
        $report = "Security Vulnerability Scan Report\n";
        $report .= str_repeat("=", 50) . "\n";
        $report .= "Generated: " . date('Y-m-d H:i:s') . "\n\n";
        
        $totalVulnerabilities = 0;
        $severityCounts = ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0];
        
        foreach ($this->scanResults as $filename => $vulnerabilities) {
            $report .= "File: $filename\n";
            $report .= str_repeat("-", 30) . "\n";
            
            foreach ($vulnerabilities as $vuln) {
                $report .= "[{$vuln['severity']}] {$vuln['type']}\n";
                $report .= "  Description: {$vuln['description']}\n";
                $report .= "  CVE: {$vuln['cve']}\n";
                $report .= "  CVSS: {$vuln['cvss']}\n";
                $report .= "  Line: {$vuln['line']}\n\n";
                
                $totalVulnerabilities++;
                $severityCounts[$vuln['severity']]++;
            }
            
            $report .= "\n";
        }
        
        $report .= "Summary:\n";
        $report .= "Total Vulnerabilities: $totalVulnerabilities\n";
        $report .= "Critical: {$severityCounts['critical']}\n";
        $report .= "High: {$severityCounts['high']}\n";
        $report .= "Medium: {$severityCounts['medium']}\n";
        $report .= "Low: {$severityCounts['low']}\n";
        
        return $report;
    }
}

// IoT Security Examples
class IoTSecurityExamples
{
    public function demonstrateSecurityManager(): void
    {
        echo "IoT Security Manager Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $securityManager = new IoTSecurityManager();
        
        // Create sample devices
        $devices = [
            new TemperatureSensor('temp_001', ['manufacturer' => 'SecureTech']),
            new MotionSensor('motion_001', ['manufacturer' => 'SafeHome']),
            new SmartLight('light_001', ['manufacturer' => 'ProtectCo'])
        ];
        
        // Register devices with security configurations
        foreach ($devices as $device) {
            $securityConfig = [
                'authentication_method' => 'certificate',
                'encryption_enabled' => true,
                'role' => 'device',
                'monitoring_enabled' => true,
                'tamper_detection' => true
            ];
            
            $securityManager->registerDevice($device, $securityConfig);
        }
        
        // Authenticate devices
        echo "\nAuthenticating devices:\n";
        foreach ($devices as $device) {
            $credentials = [
                'certificate' => 'device_cert_' . $device->getId(),
                'signature' => 'signature_' . $device->getId()
            ];
            
            $authenticated = $securityManager->authenticateDevice($device->getId(), $credentials);
            echo "  {$device->getId()}: " . ($authenticated ? 'Authenticated' : 'Failed') . "\n";
        }
        
        // Test access control
        echo "\nTesting access control:\n";
        $accessTests = [
            ['resource' => 'sensor_data', 'action' => 'read'],
            ['resource' => 'device_config', 'action' => 'write'],
            ['resource' => 'system_admin', 'action' => 'execute']
        ];
        
        foreach ($accessTests as $test) {
            $hasAccess = $securityManager->checkAccessRights('temp_001', $test['resource'], $test['action']);
            echo "  {$test['resource']} - {$test['action']}: " . ($hasAccess ? 'Granted' : 'Denied') . "\n";
        }
        
        // Test data encryption/decryption
        echo "\nTesting encryption/decryption:\n";
        $testData = 'Sensitive sensor data';
        $encrypted = $securityManager->encryptData('temp_001', $testData);
        $decrypted = $securityManager->decryptData('temp_001', $encrypted);
        
        echo "  Original: $testData\n";
        echo "  Encrypted: " . substr($encrypted, 0, 50) . "...\n";
        echo "  Decrypted: $decrypted\n";
        echo "  Match: " . ($testData === $decrypted ? 'Yes' : 'No') . "\n";
        
        // Run security audit
        echo "\nRunning security audit:\n";
        $auditResults = $securityManager->runSecurityAudit();
        
        foreach ($auditResults as $deviceId => $result) {
            echo "  $deviceId:\n";
            echo "    Security Score: {$result['security_score']}/100\n";
            echo "    Compliance Score: {$result['compliance_score']}/100\n";
            echo "    Vulnerabilities: " . count($result['vulnerabilities']) . "\n";
        }
        
        // Generate security report
        echo "\n" . $securityManager->generateSecurityReport();
    }
    
    public function demonstrateVulnerabilityScanner(): void
    {
        echo "\nSecurity Vulnerability Scanner Demo\n";
        echo str_repeat("-", 40) . "\n";
        
        $scanner = new SecurityVulnerabilityScanner();
        
        // Sample vulnerable code
        $vulnerableCode = '<?php
// Vulnerable IoT Device Code
$password = "admin123";  // Weak password
$api_key = "hardcoded_key";  // Hardcoded credentials

function processRequest() {
    $user_id = $_GET["user_id"];  // SQL injection risk
    $command = $_POST["cmd"];  // Command injection risk
    
    if ($user_id) {
        $query = "SELECT * FROM users WHERE id = $user_id";  // SQL injection
        $result = mysql_query($query);
    }
    
    if ($command) {
        exec($command);  // Command execution
    }
    
    echo "<script>alert(" . $_GET["message"] . ");</script>";  // XSS
    echo "User ID: " . $_GET["id"];  // Information disclosure
}

// Path traversal vulnerability
$file = $_GET["file"];
include("../" . $file);  // Path traversal

// Buffer overflow vulnerability
$buffer = "A" . str_repeat("B", 1000);  // Potential buffer overflow
strcpy($dest, $buffer);  // Unsafe string operation
?>';
        
        echo "Scanning vulnerable code...\n";
        $vulnerabilities = $scanner->scanCode($vulnerableCode, 'vulnerable_device.php');
        
        echo "\nVulnerabilities found: " . count($vulnerabilities) . "\n";
        
        foreach ($vulnerabilities as $vuln) {
            echo "\n[{$vuln['severity']}] {$vuln['type']}\n";
            echo "  Description: {$vuln['description']}\n";
            echo "  CVE: {$vuln['cve']}\n";
            echo "  CVSS: {$vuln['cvss']}\n";
            echo "  Line: {$vuln['line']}\n";
            echo "  Recommendation: " . $this->getRecommendation($vuln['type']) . "\n";
        }
        
        // Generate vulnerability report
        echo "\n" . $scanner->generateVulnerabilityReport();
    }
    
    private function getRecommendation(string $vulnerabilityType): string
    {
        $recommendations = [
            'weak_password' => 'Use strong, unique passwords and implement proper authentication',
            'hardcoded_credentials' => 'Remove hardcoded credentials and use secure configuration',
            'insecure_protocol' => 'Use secure protocols like HTTPS and MQTT with TLS',
            'sql_injection' => 'Use prepared statements and parameterized queries',
            'command_injection' => 'Avoid executing user input and use whitelist validation',
            'xss_vulnerability' => 'Sanitize all user input and use output encoding',
            'path_traversal' => 'Validate file paths and use whitelisting',
            'buffer_overflow' => 'Use safe string functions and validate input lengths',
            'information_disclosure' => 'Remove debug output in production code',
            'session_fixation' => 'Regenerate session IDs and use secure session management'
        ];
        
        return $recommendations[$vulnerabilityType] ?? 'Contact security team for remediation';
    }
    
    public function demonstrateThreatDetection(): void
    {
        echo "\nIoT Threat Detection Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $securityManager = new IoTSecurityManager();
        
        // Register a device
        $device = new TemperatureSensor('temp_001');
        $securityManager->registerDevice($device);
        
        // Simulate normal device data
        echo "Testing normal device data:\n";
        $normalData = [
            'device_id' => 'temp_001',
            'timestamp' => time(),
            'value' => 25.5,
            'status' => 'normal',
            'previous_value' => 24.8
        ];
        
        $anomalies = $securityManager->detectAnomalies('temp_001', $normalData);
        echo "Anomalies detected: " . count($anomalies) . "\n";
        
        // Simulate anomalous device data
        echo "\nTesting anomalous device data:\n";
        $anomalousData = [
            'device_id' => 'temp_001',
            'timestamp' => time() - 600, // 10 minutes old
            'value' => 150.0, // Out of range
            'status' => 'error',
            'previous_value' => 25.0, // Rapid change
            'source_ip' => '192.168.1.100' // Suspicious IP
        ];
        
        $anomalies = $securityManager->detectAnomalies('temp_001', $anomalousData);
        echo "Anomalies detected: " . count($anomalies) . "\n";
        
        foreach ($anomalies as $anomaly) {
            echo "  [{$anomaly['severity']}] {$anomaly['type']}: {$anomaly['description']}\n";
        }
        
        // Show threat information
        echo "\nThreat Information:\n";
        $threats = $securityManager->getThreats();
        
        foreach ($threats as $threatType => $threat) {
            echo "  $threatType:\n";
            echo "    Description: {$threat['description']}\n";
            echo "    Severity: {$threat['severity']}\n";
            echo "    Mitigation: " . implode(', ', $threat['mitigation']) . "\n\n";
        }
    }
    
    public function demonstrateSecurityBestPractices(): void
    {
        echo "\nIoT Security Best Practices\n";
        echo str_repeat("-", 30) . "\n";
        
        echo "1. Device Authentication:\n";
        echo "   • Use certificate-based authentication\n";
        echo "   • Implement multi-factor authentication\n";
        echo "   • Regularly rotate device credentials\n";
        echo "   • Use unique credentials per device\n";
        echo "   • Disable default credentials\n\n";
        
        echo "2. Data Encryption:\n";
        echo "   • Encrypt data at rest and in transit\n";
        echo "   • Use strong encryption algorithms\n";
        echo "   • Implement key management\n";
        echo "   • Use TLS/SSL for communications\n";
        echo "   • Regularly rotate encryption keys\n\n";
        
        echo "3. Access Control:\n";
        echo "   • Implement principle of least privilege\n";
        echo "   • Use role-based access control\n";
        echo "   • Implement time-based access\n";
        echo "   • Monitor access attempts\n";
        echo "   • Regularly review permissions\n\n";
        
        echo "4. Network Security:\n";
        echo "   • Use network segmentation\n";
        echo "   • Implement firewalls and IDS/IPS\n";
        echo "   • Use VPNs for remote access\n";
        echo "   • Monitor network traffic\n";
        echo "   • Implement DoS protection\n\n";
        
        echo "5. Device Security:\n";
        echo "   • Keep firmware updated\n";
        echo "   • Disable unused services\n";
        echo "   • Implement tamper detection\n";
        echo "   • Use secure boot mechanisms\n";
        echo "   • Regular security audits\n\n";
        
        echo "6. Monitoring and Logging:\n";
        echo "   • Implement comprehensive logging\n";
        echo "   • Monitor device behavior\n";
        echo "   • Detect anomalous patterns\n";
        echo "   • Set up security alerts\n";
        echo "   • Regular security reviews\n\n";
        
        echo "7. Physical Security:\n";
        echo "   • Use secure enclosures\n";
        echo "   • Implement physical access controls\n";
        echo "   • Use tamper-evident seals\n";
        echo "   • Monitor physical access\n";
        echo "   • Secure device locations";
    }
    
    public function runAllExamples(): void
    {
        echo "IoT Security Examples\n";
        echo str_repeat("=", 25) . "\n";
        
        $this->demonstrateSecurityManager();
        $this->demonstrateVulnerabilityScanner();
        $this->demonstrateThreatDetection();
        $this->demonstrateSecurityBestPractices();
    }
}

// Main execution
function runIoTSecurityDemo(): void
{
    $examples = new IoTSecurityExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runIoTSecurityDemo();
}
?>
