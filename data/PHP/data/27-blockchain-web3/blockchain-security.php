<?php
/**
 * Blockchain Security in PHP
 * 
 * Security considerations, vulnerabilities, and best practices.
 */

// Security Vulnerability Scanner
class VulnerabilityScanner
{
    private array $vulnerabilities = [];
    private array $patterns = [];
    
    public function __construct()
    {
        $this->initializePatterns();
    }
    
    private function initializePatterns(): void
    {
        $this->patterns = [
            'reentrancy' => [
                'pattern' => '/call\s*\(\s*\w+\s*\.\s*\w+\s*\)\s*\.call\s*\(/',
                'description' => 'Potential reentrancy vulnerability',
                'severity' => 'high',
                'recommendation' => 'Use Checks-Effects-Interactions pattern'
            ],
            'integer_overflow' => [
                'pattern' => '/\+\s*\w+\s*;\s*\w+\s*=\s*\w+\s*\+\s*\w+/',
                'description' => 'Potential integer overflow',
                'severity' => 'high',
                'recommendation' => 'Use SafeMath library or overflow checks'
            ],
            'uninitialized_storage' => [
                'pattern' => '/\w+\s*\[\s*\w+\s*\]\s*=\s*\w+\s*\[\s*\w+\s*\]/',
                'description' => 'Uninitialized storage access',
                'severity' => 'medium',
                'recommendation' => 'Initialize all storage variables'
            ],
            'delegatecall_to_untrusted' => [
                'pattern' => '/delegatecall\s*\(\s*\w+\s*\)/',
                'description' => 'Delegatecall to untrusted address',
                'severity' => 'high',
                'recommendation' => 'Validate delegatecall targets'
            ],
            'selfdestruct_vulnerability' => [
                'pattern' => '/selfdestruct\s*\(\s*\w+\s*\)/',
                'description' => 'Selfdestruct accessible to non-owners',
                'severity' => 'critical',
                'recommendation' => 'Add proper access control'
            ],
            'timestamp_dependency' => [
                'pattern' => '/block\.timestamp|now\(\s*\)/',
                'description' => 'Timestamp dependency vulnerability',
                'severity' => 'medium',
                'recommendation' => 'Use block number instead of timestamp'
            ]
        ];
    }
    
    public function scanContract(string $contractCode): array
    {
        $this->vulnerabilities = [];
        
        foreach ($this->patterns as $type => $pattern) {
            if (preg_match($pattern['pattern'], $contractCode)) {
                $this->vulnerabilities[] = [
                    'type' => $type,
                    'description' => $pattern['description'],
                    'severity' => $pattern['severity'],
                    'recommendation' => $pattern['recommendation'],
                    'line' => $this->findLineNumber($contractCode, $pattern['pattern'])
                ];
            }
        }
        
        return $this->vulnerabilities;
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
    
    public function getVulnerabilities(): array
    {
        return $this->vulnerabilities;
    }
    
    public function getSeverityDistribution(): array
    {
        $distribution = [
            'critical' => 0,
            'high' => 0,
            'medium' => 0,
            'low' => 0
        ];
        
        foreach ($this->vulnerabilities as $vuln) {
            $distribution[$vuln['severity']]++;
        }
        
        return $distribution;
    }
    
    public function generateReport(): string
    {
        $report = "Security Vulnerability Report\n";
        $report .= str_repeat("=", 40) . "\n\n";
        
        if (empty($this->vulnerabilities)) {
            $report .= "No vulnerabilities found!\n";
        } else {
            $report .= "Found " . count($this->vulnerabilities) . " vulnerabilities:\n\n";
            
            foreach ($this->vulnerabilities as $vuln) {
                $report .= "[{$vuln['severity']}] {$vuln['type']}\n";
                $report .= "  Description: {$vuln['description']}\n";
                $report .= "  Line: {$vuln['line']}\n";
                $report .= "  Recommendation: {$vuln['recommendation']}\n\n";
            }
            
            $distribution = $this->getSeverityDistribution();
            $report .= "Severity Distribution:\n";
            foreach ($distribution as $severity => $count) {
                if ($count > 0) {
                    $report .= "  $severity: $count\n";
                }
            }
        }
        
        return $report;
    }
}

// Secure Contract Template
class SecureContractTemplate
{
    private array $securityChecks = [];
    private array $accessControl = [];
    private array $eventLogging = [];
    
    public function __construct()
    {
        $this->initializeSecurityChecks();
        $this->initializeAccessControl();
        $this->initializeEventLogging();
    }
    
    private function initializeSecurityChecks(): void
    {
        $this->securityChecks = [
            'require_owner' => [
                'code' => 'require(msg.sender == owner, "Only owner can call this function");',
                'description' => 'Restrict function to contract owner'
            ],
            'require_non_zero' => [
                'code' => 'require(amount > 0, "Amount must be greater than zero");',
                'description' => 'Validate amount is not zero'
            ],
            'require_sufficient_balance' => [
                'code' => 'require(balances[msg.sender] >= amount, "Insufficient balance");',
                'description' => 'Check sufficient balance before transfer'
            ],
            'require_valid_address' => [
                'code' => 'require(to != address(0), "Invalid recipient address");',
                'description' => 'Validate recipient address'
            ],
            'require_not_paused' => [
                'code' => 'require(!paused, "Contract is paused");',
                'description' => 'Check if contract is paused'
            ],
            'require_within_limits' => [
                'code' => 'require(amount <= MAX_AMOUNT, "Amount exceeds limit");',
                'description' => 'Check amount within allowed limits'
            ]
        ];
    }
    
    private function initializeAccessControl(): void
    {
        $this->accessControl = [
            'only_owner' => [
                'modifier' => 'modifier onlyOwner() { require(msg.sender == owner); _; }',
                'description' => 'Restrict access to contract owner'
            ],
            'only_pauser' => [
                'modifier' => 'modifier onlyPauser() { require(msg.sender == pauser || msg.sender == owner); _; }',
                'description' => 'Restrict access to pauser or owner'
            ],
            'when_not_paused' => [
                'modifier' => 'modifier whenNotPaused() { require(!paused); _; }',
                'description' => 'Execute only when not paused'
            ],
            'when_not_paused_owner' => [
                'modifier' => 'modifier whenNotPausedOwner() { require(!paused && msg.sender == owner); _; }',
                'description' => 'Execute only when not paused and caller is owner'
            ],
            'valid_address' => [
                'modifier' => 'modifier validAddress(address _addr) { require(_addr != address(0)); _; }',
                'description' => 'Validate address is not zero'
            ]
        ];
    }
    
    private function initializeEventLogging(): void
    {
        $this->eventLogging = [
            'transfer_event' => [
                'event' => 'event Transfer(address indexed from, address indexed to, uint256 value);',
                'description' => 'Log transfer events'
            ],
            'approval_event' => [
                'event' => 'event Approval(address indexed owner, address indexed spender, uint256 value);',
                'description' => 'Log approval events'
            ],
            'ownership_transferred' => [
                'event' => 'event OwnershipTransferred(address indexed previousOwner, address indexed newOwner);',
                'description' => 'Log ownership transfer events'
            ],
            'pause_event' => [
                'event' => 'event Pause(address indexed account);',
                'description' => 'Log pause events'
            ],
            'unpause_event' => [
                'event' => 'event Unpause(address indexed account);',
                'description' => 'Log unpause events'
            ],
            'emergency_action' => [
                'event' => 'event EmergencyAction(string action, address indexed executor, uint256 timestamp);',
                'description' => 'Log emergency actions'
            ]
        ];
    }
    
    public function generateSecureContract(string $contractName, string $contractType): string
    {
        $template = "// SPDX-License-Identifier: MIT\n";
        $template .= "pragma solidity ^0.8.0;\n\n";
        $template .= "contract $contractName {\n";
        
        // Add state variables
        $template .= $this->generateStateVariables($contractType);
        
        // Add events
        $template .= "\n    // Events\n";
        foreach ($this->eventLogging as $event) {
            $template .= "    {$event['event']}\n";
        }
        
        // Add modifiers
        $template .= "\n    // Modifiers\n";
        foreach ($this->accessControl as $modifier) {
            $template .= "    {$modifier['modifier']}\n";
        }
        
        // Add constructor
        $template .= "\n    constructor() {\n";
        $template .= "        owner = msg.sender;\n";
        $template .= "        emit OwnershipTransferred(address(0), msg.sender);\n";
        $template .= "    }\n";
        
        // Add functions based on contract type
        $template .= $this->generateFunctions($contractType);
        
        $template .= "}\n";
        
        return $template;
    }
    
    private function generateStateVariables(string $contractType): string
    {
        $variables = "    address public owner;\n";
        $variables .= "    bool public paused = false;\n";
        $variables .= "    uint256 public MAX_AMOUNT = 1000000 * 10**18;\n";
        
        switch ($contractType) {
            case 'ERC20':
                $variables .= "    mapping(address => uint256) public balances;\n";
                $variables .= "    mapping(address => mapping(address => uint256)) public allowances;\n";
                $variables .= "    uint256 public totalSupply;\n";
                $variables .= "    string public name;\n";
                $variables .= "    string public symbol;\n";
                $variables .= "    uint8 public decimals;\n";
                break;
                
            case 'NFT':
                $variables .= "    uint256 public tokenCounter;\n";
                $variables .= "    mapping(uint256 => address) public tokenOwners;\n";
                $variables .= "    mapping(address => uint256) public ownedTokens;\n";
                $variables .= "    mapping(uint256 => address) public tokenApprovals;\n";
                $variables .= "    string public baseURI;\n";
                break;
                
            case 'MultiSig':
                $variables .= "    uint256 public requiredSignatures;\n";
                $variables .= "    mapping(address => bool) public isOwner;\n";
                $variables .= "    mapping(bytes32 => mapping(address => bool)) public signatures;\n";
                $variables .= "    mapping(bytes32 => bool) public executedTransactions;\n";
                break;
        }
        
        return $variables;
    }
    
    private function generateFunctions(string $contractType): string
    {
        $functions = "";
        
        // Common functions
        $functions .= "\n    function transferOwnership(address newOwner) public onlyOwner {\n";
        $functions .= "        require(newOwner != address(0));\n";
        $functions .= "        emit OwnershipTransferred(owner, newOwner);\n";
        $functions .= "        owner = newOwner;\n";
        $functions .= "    }\n\n";
        
        $functions .= "    function pause() public onlyOwner {\n";
        $functions .= "        paused = true;\n";
        $functions .= "        emit Pause(msg.sender);\n";
        $functions .= "    }\n\n";
        
        $functions .= "    function unpause() public onlyOwner {\n";
        $functions .= "        paused = false;\n";
        $functions .= "        emit Unpause(msg.sender);\n";
        $functions .= "    }\n\n";
        
        // Type-specific functions
        switch ($contractType) {
            case 'ERC20':
                $functions .= $this->generateERC20Functions();
                break;
                
            case 'NFT':
                $functions .= $this->generateNFTFunctions();
                break;
                
            case 'MultiSig':
                $functions .= $this->generateMultiSigFunctions();
                break;
        }
        
        return $functions;
    }
    
    private function generateERC20Functions(): string
    {
        $functions = "    function transfer(address to, uint256 amount) public whenNotPaused validAddress(to) returns (bool) {\n";
        $functions .= "        require(balances[msg.sender] >= amount, \"Insufficient balance\");\n";
        $functions .= "        require(amount <= MAX_AMOUNT, \"Amount exceeds limit\");\n";
        $functions .= "        balances[msg.sender] -= amount;\n";
        $functions .= "        balances[to] += amount;\n";
        $functions .= "        emit Transfer(msg.sender, to, amount);\n";
        $functions .= "        return true;\n";
        $functions .= "    }\n\n";
        
        $functions .= "    function approve(address spender, uint256 amount) public whenNotPaused returns (bool) {\n";
        $functions .= "        allowances[msg.sender][spender] = amount;\n";
        $functions .= "        emit Approval(msg.sender, spender, amount);\n";
        $functions .= "        return true;\n";
        $functions .= "    }\n\n";
        
        $functions .= "    function transferFrom(address from, address to, uint256 amount) public whenNotPaused validAddress(to) returns (bool) {\n";
        $functions .= "        require(allowances[from][msg.sender] >= amount, \"Insufficient allowance\");\n";
        $functions .= "        require(balances[from] >= amount, \"Insufficient balance\");\n";
        $functions .= "        allowances[from][msg.sender] -= amount;\n";
        $functions .= "        balances[from] -= amount;\n";
        $functions .= "        balances[to] += amount;\n";
        $functions .= "        emit Transfer(from, to, amount);\n";
        $functions .= "        return true;\n";
        $functions .= "    }\n";
        
        return $functions;
    }
    
    private function generateNFTFunctions(): string
    {
        $functions = "    function mint(address to) public onlyOwner returns (uint256) {\n";
        $functions .= "        tokenCounter++;\n";
        $functions .= "        uint256 tokenId = tokenCounter;\n";
        $functions .= "        _mint(to, tokenId);\n";
        $functions .= "        return tokenId;\n";
        $functions .= "    }\n\n";
        
        $functions .= "    function _mint(address to, uint256 tokenId) internal {\n";
        $functions .= "        require(to != address(0), \"Invalid recipient\");\n";
        $functions .= "        require(tokenOwners[tokenId] == address(0), \"Token already minted\");\n";
        $functions .= "        tokenOwners[tokenId] = to;\n";
        $functions .= "        ownedTokens[to]++;\n";
        $functions .= "    }\n\n";
        
        $functions .= "    function transfer(address to, uint256 tokenId) public validAddress(to) returns (bool) {\n";
        $functions .= "        require(tokenOwners[tokenId] == msg.sender, \"Not token owner\");\n";
        $functions .= "        require(to != address(0), \"Invalid recipient\");\n";
        $functions .= "        _transfer(msg.sender, to, tokenId);\n";
        $functions .= "        return true;\n";
        $functions .= "    }\n\n";
        
        $functions .= "    function _transfer(address from, address to, uint256 tokenId) internal {\n";
        $functions .= "        require(tokenOwners[tokenId] == from, \"Not token owner\");\n";
        $functions .= "        ownedTokens[from]--;\n";
        $functions .= "        ownedTokens[to]++;\n";
        $functions .= "        tokenOwners[tokenId] = to;\n";
        $functions .= "    }\n";
        
        return $functions;
    }
    
    private function generateMultiSigFunctions(): string
    {
        $functions = "    function submitTransaction(address destination, uint256 value, bytes data) public onlyOwner returns (bytes32) {\n";
        $functions .= "        bytes32 transactionId = keccak256(abi.encodePacked(destination, value, data));\n";
        $functions .= "        require(!executedTransactions[transactionId], \"Transaction already executed\");\n";
        $functions .= "        signatures[transactionId][msg.sender] = true;\n";
        $functions .= "        return transactionId;\n";
        $functions .= "    }\n\n";
        
        $functions .= "    function confirmTransaction(bytes32 transactionId) public {\n";
        $functions .= "        require(!executedTransactions[transactionId], \"Transaction already executed\");\n";
        $functions .= "        require(signatures[transactionId][msg.sender], \"Not authorized\");\n";
        $functions .= "        signatures[transactionId][msg.sender] = false;\n";
        $functions .= "        if (isTransactionConfirmed(transactionId)) {\n";
        $functions .= "            executedTransactions[transactionId] = true;\n";
        $functions .= "        }\n";
        $functions .= "    }\n\n";
        
        $functions .= "    function isTransactionConfirmed(bytes32 transactionId) public view returns (bool) {\n";
        $functions .= "        uint256 count = 0;\n";
        $functions .= "        for (uint i = 0; i < owners.length; i++) {\n";
        $functions .= "            if (signatures[transactionId][owners[i]]) {\n";
        $functions .= "                count++;\n";
        $functions .= "            }\n";
        $functions .= "        }\n";
        $functions .= "        return count >= requiredSignatures;\n";
        $functions .= "    }\n";
        
        return $functions;
    }
    
    public function getSecurityChecks(): array
    {
        return $this->securityChecks;
    }
    
    public function getAccessControl(): array
    {
        return $this->accessControl;
    }
    
    public function getEventLogging(): array
    {
        return $this->eventLogging;
    }
}

// Security Audit Tool
class SecurityAuditTool
{
    private VulnerabilityScanner $scanner;
    private SecureContractTemplate $template;
    private array $auditResults = [];
    
    public function __construct()
    {
        $this->scanner = new VulnerabilityScanner();
        $this->template = new SecureContractTemplate();
    }
    
    public function auditContract(string $contractCode, string $contractName = ''): array
    {
        echo "Auditing contract: $contractName\n";
        echo str_repeat("-", 40) . "\n";
        
        $this->auditResults = [];
        
        // Scan for vulnerabilities
        $vulnerabilities = $this->scanner->scanContract($contractCode);
        $this->auditResults['vulnerabilities'] = $vulnerabilities;
        
        // Check for security patterns
        $securityPatterns = $this->checkSecurityPatterns($contractCode);
        $this->auditResults['security_patterns'] = $securityPatterns;
        
        // Check for access control
        $accessControl = $this->checkAccessControl($contractCode);
        $this->auditResults['access_control'] = $accessControl;
        
        // Check for event logging
        $eventLogging = $this->checkEventLogging($contractCode);
        $this->auditResults['event_logging'] = $eventLogging;
        
        // Calculate security score
        $securityScore = $this->calculateSecurityScore();
        $this->auditResults['security_score'] = $securityScore;
        
        echo "Audit completed\n";
        echo "Security score: $securityScore/100\n";
        
        return $this->auditResults;
    }
    
    private function checkSecurityPatterns(string $code): array
    {
        $patterns = [
            'has_owner' => preg_match('/address\s+public\s+owner/', $code),
            'has_pause' => preg_match('/bool\s+public\s+paused/', $code),
            'has_events' => preg_match('/event\s+\w+\(/', $code),
            'has_modifiers' => preg_match('/modifier\s+\w+\s*\(/', $code),
            'has_require_statements' => preg_match('/require\s*\(/', $code),
            'has_revert_statements' => preg_match('/revert\s*\(\s*\)/', $code)
        ];
        
        return $patterns;
    }
    
    private function checkAccessControl(string $code): array
    {
        $checks = [
            'owner_restriction' => preg_match('/onlyOwner\s*\(/', $code),
            'pause_protection' => preg_match('/whenNotPaused\s*\(/', $code),
            'address_validation' => preg_match('/require\s*\(\s*\w+\s*!=\s*address\(0\)/', $code)
        ];
        
        return $checks;
    }
    
    private function checkEventLogging(string $code): array
    {
        $events = [
            'transfer_events' => preg_match('/event\s+Transfer\s*\(/', $code),
            'approval_events' => preg_match('/event\s+Approval\s*\(/', $code),
            'ownership_events' => preg_match('/event\s+OwnershipTransferred\s*\(/', $code)
        ];
        
        return $events;
    }
    
    private function calculateSecurityScore(): int
    {
        $score = 100;
        
        // Deduct points for vulnerabilities
        foreach ($this->auditResults['vulnerabilities'] as $vuln) {
            switch ($vuln['severity']) {
                case 'critical':
                    $score -= 25;
                    break;
                case 'high':
                    $score -= 15;
                    break;
                case 'medium':
                    $score -= 10;
                    break;
                case 'low':
                    $score -= 5;
                    break;
            }
        }
        
        // Add points for security patterns
        foreach ($this->auditResults['security_patterns'] as $pattern => $found) {
            if ($found) {
                $score += 5;
            }
        }
        
        // Add points for access control
        foreach ($this->auditResults['access_control'] as $control => $found) {
            if ($found) {
                $score += 3;
            }
        }
        
        // Add points for event logging
        foreach ($this->auditResults['event_logging'] as $event => $found) {
            if ($found) {
                $score += 2;
            }
        }
        
        return max(0, min(100, $score));
    }
    
    public function generateSecureVersion(string $contractCode, string $contractName, string $contractType): string
    {
        echo "Generating secure version of: $contractName\n";
        
        $secureTemplate = $this->template->generateSecureContract($contractName, $contractType);
        
        // Extract functions from original code
        $functions = $this->extractFunctions($contractCode);
        
        // Apply security patterns to functions
        $secureFunctions = $this->applySecurityPatterns($functions);
        
        // Combine template with secure functions
        $secureCode = $this->mergeCode($secureTemplate, $secureFunctions);
        
        echo "Secure version generated\n";
        return $secureCode;
    }
    
    private function extractFunctions(string $code): array
    {
        $functions = [];
        
        // Simplified function extraction
        preg_match_all('/function\s+(\w+)\s*\([^)]*)\s*(public|private|internal|external)?\s*(view|pure|payable|nonpayable)?\s*(returns\s*\([^)]*))?\s*\{([^}]*)\}/', $code, $matches, PREG_SET_ORDER);
        
        foreach ($matches[1] as $i => $functionName) {
            $functions[$functionName] = [
                'params' => $matches[2][$i],
                'visibility' => $matches[3][$i] ?? 'public',
                'mutability' => $matches[4][$i] ?? '',
                'returns' => $matches[5][$i] ?? '',
                'body' => $matches[6][$i]
            ];
        }
        
        return $functions;
    }
    
    private function applySecurityPatterns(array $functions): array
    {
        $secureFunctions = [];
        
        foreach ($functions as $name => $function) {
            $body = $function['body'];
            
            // Add security checks
            if (strpos($name, 'transfer') !== false) {
                $body = $this->addSecurityChecks($body, ['amount_validation', 'balance_check']);
            }
            
            if (strpos($name, 'approve') !== false) {
                $body = $this->addSecurityChecks($body, ['amount_validation']);
            }
            
            if (strpos($name, 'withdraw') !== false) {
                $body = $this->addSecurityChecks($body, ['owner_check', 'amount_validation']);
            }
            
            $secureFunctions[$name] = array_merge($function, ['body' => $body]);
        }
        
        return $secureFunctions;
    }
    
    private function addSecurityChecks(string $code, array $checks): string
    {
        $securityCode = '';
        
        foreach ($checks as $check) {
            switch ($check) {
                case 'amount_validation':
                    $securityCode .= "        require(amount > 0, \"Amount must be greater than zero\");\n";
                    $securityCode .= "        require(amount <= MAX_AMOUNT, \"Amount exceeds limit\");\n";
                    break;
                case 'balance_check':
                    $securityCode .= "        require(balances[msg.sender] >= amount, \"Insufficient balance\");\n";
                    break;
                case 'owner_check':
                    $securityCode .= "        require(msg.sender == owner, \"Only owner can withdraw\");\n";
                    break;
            }
        }
        
        return $securityCode . $code;
    }
    
    private function mergeCode(string $template, array $functions): string
    {
        // Simplified code merging
        return $template; // In practice, this would be more sophisticated
    }
    
    public function getAuditResults(): array
    {
        return $this->auditResults;
    }
    
    public function generateAuditReport(): string
    {
        $report = "Security Audit Report\n";
        $report .= str_repeat("=", 40) . "\n\n";
        
        $report .= "Security Score: {$this->auditResults['security_score']}/100\n\n";
        
        $report .= "Vulnerabilities Found: " . count($this->auditResults['vulnerabilities']) . "\n";
        foreach ($this->auditResults['vulnerabilities'] as $vuln) {
            $report .= "  [{$vuln['severity']}] {$vuln['type']}: {$vuln['description']}\n";
        }
        
        $report .= "\nSecurity Patterns:\n";
        foreach ($this->auditResults['security_patterns'] as $pattern => $found) {
            $report .= "  " . ($found ? '✓' : '✗') . " $pattern\n";
        }
        
        $report .= "\nAccess Control:\n";
        foreach ($this->auditResults['access_control'] as $control => $found) {
            $report .= "  " . ($found ? '✓' : '✗') . " $control\n";
        }
        
        $report .= "\nEvent Logging:\n";
        foreach ($this->auditResults['event_logging'] as $event => $found) {
            $report .= "  " . ($found ? '✓' : '✗') . " $event\n";
        }
        
        return $report;
    }
}

// Blockchain Security Examples
class BlockchainSecurityExamples
{
    public function demonstrateVulnerabilityScanner(): void
    {
        echo "Vulnerability Scanner Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $scanner = new VulnerabilityScanner();
        
        // Vulnerable contract code
        $vulnerableCode = '
        contract VulnerableContract {
            address public owner;
            mapping(address => uint256) public balances;
            bool public paused;
            
            function withdraw(uint256 amount) public {
                balances[msg.sender] -= amount;
                msg.sender.transfer(amount);
            }
            
            function delegateCall(address target) public {
                target.delegatecall(abi.encodeWithSignature(msg.sender));
            }
            
            function selfDestruct() public {
                selfdestruct(msg.sender);
            }
            
            function overflow() public {
                uint256 a = balances[msg.sender];
                balances[msg.sender] = a + amount;
            }
        }';
        
        echo "Scanning vulnerable contract code...\n";
        $vulnerabilities = $scanner->scanContract($vulnerableCode);
        
        echo "\nScan Results:\n";
        foreach ($vulnerabilities as $vuln) {
            echo "[{$vuln['severity']}] {$vuln['type']}\n";
            echo "  Description: {$vuln['description']}\n";
            echo "  Line: {$vuln['line']}\n";
            echo "  Recommendation: {$vuln['recommendation']}\n\n";
        }
        
        echo "Severity Distribution:\n";
        $distribution = $scanner->getSeverityDistribution();
        foreach ($distribution as $severity => $count) {
            if ($count > 0) {
                echo "  $severity: $count\n";
            }
        }
        
        echo "\nFull Report:\n";
        echo $scanner->generateReport();
    }
    
    public function demonstrateSecureContractTemplate(): void
    {
        echo "\nSecure Contract Template Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        $template = new SecureContractTemplate();
        
        // Generate secure ERC20 contract
        echo "Generating secure ERC20 contract...\n";
        $erc20Contract = $template->generateSecureContract('SecureToken', 'ERC20');
        
        echo "Generated Secure ERC20 Contract:\n";
        echo substr($erc20Contract, 0, 1000) . "...\n";
        echo str_repeat("...", 30) . "\n";
        echo substr($erc20Contract, -1000) . "\n\n";
        
        // Generate secure NFT contract
        echo "Generating secure NFT contract...\n";
        $nftContract = $template->generateSecureContract('SecureNFT', 'NFT');
        
        echo "Generated Secure NFT Contract:\n";
        echo substr($nftContract, 0, 1000) . "...\n";
        echo str_repeat("...", 30) . "\n";
        echo substr($nftContract, -1000) . "\n\n";
        
        // Generate secure multi-sig contract
        echo "Generating secure MultiSig contract...\n";
        $multisigContract = $template->generateSecureContract('SecureMultiSig', 'MultiSig');
        
        echo "Generated Secure MultiSig Contract:\n";
        echo substr($multisigContract, 0, 1000) . "...\n";
        echo str_repeat("...", 30) . "\n";
        echo substr($multisigContract, -1000) . "\n\n";
        
        // Show security features
        echo "Security Features:\n";
        $securityChecks = $template->getSecurityChecks();
        echo "Security Checks:\n";
        foreach ($securityChecks as $check => $info) {
            echo "  $check: {$info['description']}\n";
        }
        
        echo "\nAccess Control:\n";
        $accessControl = $template->getAccessControl();
        foreach ($accessControl as $modifier => $info) {
            echo "  $modifier: {$info['description']}\n";
        }
        
        echo "\nEvent Logging:\n";
        $eventLogging = $template->getEventLogging();
        foreach ($eventLogging as $event => $info) {
            echo "  $event: {$info['description']}\n";
        }
    }
    
    public function demonstrateSecurityAudit(): void
    {
        echo "\nSecurity Audit Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        $auditTool = new SecurityAuditTool();
        
        // Audit vulnerable contract
        $vulnerableCode = '
        contract VulnerableToken {
            address public owner;
            mapping(address => uint256) public balances;
            uint256 public totalSupply;
            
            constructor() {
                totalSupply = 1000000;
                balances[msg.sender] = totalSupply;
            }
            
            function transfer(address to, uint256 amount) public returns (bool) {
                balances[msg.sender] -= amount;
                balances[to] += amount;
                return true;
            }
            
            function withdraw(uint256 amount) public {
                msg.sender.transfer(amount);
            }
        }';
        
        echo "Auditing vulnerable contract...\n";
        $results = $auditTool->auditContract($vulnerableCode, 'VulnerableToken');
        
        echo "\nAudit Results:\n";
        echo $auditTool->generateAuditReport();
        
        // Generate secure version
        echo "\nGenerating secure version...\n";
        $secureCode = $auditTool->generateSecureVersion($vulnerableCode, 'SecureToken', 'ERC20');
        
        echo "Secure contract generated\n";
        echo "Length: " . strlen($secureCode) . " characters\n";
        
        // Compare results
        echo "\nComparison:\n";
        echo "Original vulnerabilities: " . count($results['vulnerabilities']) . "\n";
        echo "Security score: {$results['security_score']}/100\n";
        
        $secureAudit = $auditTool->auditContract($secureCode, 'SecureToken');
        echo "Secure version vulnerabilities: " . count($secureAudit['vulnerabilities']) . "\n";
        echo "Secure version score: {$secureAudit['security_score']}/100\n";
    }
    
    public function demonstrateSecurityBestPractices(): void
    {
        echo "\nBlockchain Security Best Practices\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "1. Smart Contract Security:\n";
        echo "   • Use the Checks-Effects-Interactions pattern\n";
        echo "   • Implement proper access control\n";
        echo "   • Use SafeMath for arithmetic operations\n";
        echo "   • Add input validation\n";
        echo "   • Implement emergency stop functionality\n";
        echo "   • Use upgradable proxy patterns\n\n";
        
        echo "2. Reentrancy Protection:\n";
        echo "   • Use reentrancy guards\n";
        echo "   • Update state before external calls\n";
        echo "   • Limit gas for external calls\n";
        echo "   • Use pull-over-push pattern\n";
        echo "   • Avoid recursive calls\n\n";
        
        echo "3. Access Control:\n";
        echo "   • Implement proper ownership model\n";
        echo "   • Use modifiers for common checks\n";
        echo "   • Implement role-based access\n";
        echo "   • Add pause/unpause functionality\n";
        echo "   • Validate all inputs\n\n";
        
        echo "4. Event Logging:\n";
        echo "   • Log all state changes\n";
        echo "   • Use indexed parameters efficiently\n";
        echo "   • Add context to events\n";
        echo "   • Log emergency actions\n";
        echo "   • Use structured event data\n\n";
        
        echo "5. Gas Optimization:\n";
        echo "   • Minimize storage operations\n";
        echo "   • Use appropriate data types\n";
        echo "   • Optimize loops and iterations\n";
        echo "   • Use libraries for complex operations\n";
        echo "   • Batch operations when possible\n\n";
        
        echo "6. Testing and Auditing:\n";
        echo "   • Write comprehensive tests\n";
        echo "   • Use static analysis tools\n";
        echo "   • Conduct security audits\n";
        echo "   • Test on testnets first\n";
        echo "   • Use formal verification when possible\n\n";
        
        echo "7. Deployment Security:\n";
        echo "   • Verify contract bytecode\n";
        echo "   • Use multisig for deployment\n";
        echo "   • Implement upgrade patterns\n";
        echo "   • Monitor contract activity\n";
        echo "   • Have emergency response plan";
    }
    
    public function runAllExamples(): void
    {
        echo "Blockchain Security Examples\n";
        echo str_repeat("=", 30) . "\n";
        
        $this->demonstrateVulnerabilityScanner();
        $this->demonstrateSecureContractTemplate();
        $this->demonstrateSecurityAudit();
        $this->demonstrateSecurityBestPractices();
    }
}

// Main execution
function runBlockchainSecurityDemo(): void
{
    $examples = new BlockchainSecurityExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runBlockchainSecurityDemo();
}
?>
