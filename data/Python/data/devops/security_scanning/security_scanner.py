"""
Security Vulnerability Scanner
==============================

Comprehensive security vulnerability scanning tool.
Demonstrates security assessment, vulnerability detection, and security best practices.
"""

import os
import re
import json
import hashlib
import subprocess
import time
from typing import Dict, List, Optional, Tuple
from datetime import datetime
from dataclasses import dataclass, asdict
from enum import Enum
import logging

try:
    import requests
    REQUESTS_AVAILABLE = True
except ImportError:
    print("Warning: requests not available. Web scanning will be limited.")
    REQUESTS_AVAILABLE = False

class VulnerabilitySeverity(Enum):
    """Vulnerability severity levels"""
    LOW = "low"
    MEDIUM = "medium"
    HIGH = "high"
    CRITICAL = "critical"

@dataclass
class Vulnerability:
    """Vulnerability data structure"""
    id: str
    title: str
    description: str
    severity: VulnerabilitySeverity
    category: str
    file_path: Optional[str]
    line_number: Optional[int]
    code_snippet: Optional[str]
    recommendation: str
    cwe_id: Optional[str] = None
    cvss_score: Optional[float] = None

@dataclass
class SecurityReport:
    """Security assessment report"""
    scan_id: str
    timestamp: datetime
    scan_type: str
    target: str
    vulnerabilities: List[Vulnerability]
    risk_score: float
    summary: Dict[str, int]

class SecurityScanner:
    """Security vulnerability scanning tool"""
    
    def __init__(self):
        self.vulnerabilities = []
        self.scan_history = []
        
        # Security patterns and rules
        self.security_rules = self._load_security_rules()
        
        # OWASP Top 10 patterns
        self.owasp_patterns = self._load_owasp_patterns()
        
        # CWE mappings
        self.cwe_mappings = self._load_cwe_mappings()
        
        # Setup logging
        logging.basicConfig(level=logging.INFO)
        self.logger = logging.getLogger(__name__)
    
    def _load_security_rules(self) -> Dict[str, List[Dict]]:
        """Load security scanning rules"""
        return {
            'injection': [
                {
                    'pattern': r'(?i)(system|exec|shell_exec|popen|passthru|proc_open)\s*\(',
                    'severity': VulnerabilitySeverity.HIGH,
                    'description': 'Potential command injection vulnerability',
                    'recommendation': 'Use parameterized queries or input validation'
                },
                {
                    'pattern': r'(?i)(eval|assert)\s*\(',
                    'severity': VulnerabilitySeverity.HIGH,
                    'description': 'Code execution vulnerability',
                    'recommendation': 'Avoid using eval() with user input'
                }
            ],
            'xss': [
                {
                    'pattern': r'(?i)(innerHTML|outerHTML|document\.write)\s*=',
                    'severity': VulnerabilitySeverity.MEDIUM,
                    'description': 'Potential XSS vulnerability',
                    'recommendation': 'Use safe DOM manipulation methods'
                },
                {
                    'pattern': r'(?i)<script[^>]*>',
                    'severity': VulnerabilitySeverity.MEDIUM,
                    'description': 'Script tag in code',
                    'recommendation': 'Avoid embedding scripts in output'
                }
            ],
            'crypto': [
                {
                    'pattern': r'(?i)md5\(',
                    'severity': VulnerabilitySeverity.MEDIUM,
                    'description': 'Weak cryptographic hash (MD5)',
                    'recommendation': 'Use SHA-256 or stronger'
                },
                {
                    'pattern': r'(?i)sha1\(',
                    'severity': VulnerabilitySeverity.MEDIUM,
                    'description': 'Weak cryptographic hash (SHA-1)',
                    'recommendation': 'Use SHA-256 or stronger'
                },
                {
                    'pattern': r'(?i)password\s*=\s*["\'][^"\']*["\']',
                    'severity': VulnerabilitySeverity.HIGH,
                    'description': 'Hardcoded password',
                    'recommendation': 'Use environment variables or secure storage'
                }
            ],
            'sql_injection': [
                {
                    'pattern': r'(?i)(select|insert|update|delete)\s+.*\s+from\s+.*\s+where\s*.*\+.*',
                    'severity': VulnerabilitySeverity.CRITICAL,
                    'description': 'Potential SQL injection vulnerability',
                    'recommendation': 'Use parameterized queries'
                },
                {
                    'pattern': r'(?i)(mysql_query|pg_query|sqlite_query)\s*\(',
                    'severity': VulnerabilitySeverity.HIGH,
                    'description': 'Direct database query execution',
                    'recommendation': 'Use prepared statements'
                }
            ],
            'file_inclusion': [
                {
                    'pattern': r'(?i)(include|require)\s*\$.*',
                    'severity': VulnerabilitySeverity.HIGH,
                    'description': 'Potential file inclusion vulnerability',
                    'recommendation': 'Validate and sanitize file paths'
                },
                {
                    'pattern': r'(?i)fopen\s*\([^,)]*\$',
                    'severity': VulnerabilitySeverity.MEDIUM,
                    'description': 'File path from user input',
                    'recommendation': 'Validate and sanitize file paths'
                }
            ],
            'authentication': [
                {
                    'pattern': r'(?i)(password|passwd|pwd)\s*=\s*["\'][^"\']*["\']',
                    'severity': VulnerabilitySeverity.HIGH,
                    'description': 'Hardcoded password',
                    'recommendation': 'Use secure authentication methods'
                },
                {
                    'pattern': r'(?i)session_id\s*=\s*["\'][^"\']*["\']',
                    'severity': VulnerabilitySeverity.MEDIUM,
                    'description': 'Hardcoded session ID',
                    'recommendation': 'Use random session IDs'
                }
            ],
            'information_disclosure': [
                {
                    'pattern': r'(?i)(var_dump|print_r|echo)\s*\$',
                    'severity': VulnerabilitySeverity.MEDIUM,
                    'description': 'Information disclosure in output',
                    'recommendation': 'Remove debugging code from production'
                },
                {
                    'pattern': r'(?i)error_reporting\s*\(',
                    'severity': VulnerabilitySeverity.LOW,
                    'description': 'Error reporting enabled',
                    'recommendation': 'Disable error reporting in production'
                }
            ]
        }
    
    def _load_owasp_patterns(self) -> List[Dict]:
        """Load OWASP Top 10 vulnerability patterns"""
        return [
            {
                'category': 'broken_access_control',
                'pattern': r'(?i)(admin|root)\s*=\s*["\']?(true|1)["\']?',
                'severity': VulnerabilitySeverity.HIGH,
                'description': 'Hardcoded admin access'
            },
            {
                'category': 'cryptographic_failures',
                'pattern': r'(?i)(base64_encode|base64_decode)\s*\(',
                'severity': VulnerabilitySeverity.MEDIUM,
                'description': 'Base64 used for encryption'
            },
            {
                'category': 'injection',
                'pattern': r'(?i)(system|exec|shell)\s*\(',
                'severity': VulnerabilitySeverity.CRITICAL,
                'description': 'Command injection vulnerability'
            },
            {
                'category': 'insecure_design',
                'pattern': r'(?i)(debug|test)\s*=\s*["\']?(true|1)["\']?',
                'severity': VulnerabilitySeverity.MEDIUM,
                'description': 'Debug mode enabled in production'
            },
            {
                'category': 'security_logging',
                'pattern': r'(?i)error_log\s*\(',
                'severity': VulnerabilitySeverity.LOW,
                'description': 'Error logging without sanitization'
            },
            {
                'category': 'ssrf',
                'pattern': r'(?i)curl\s*\([^,)]*\$',
                'severity': VulnerabilitySeverity.HIGH,
                'description': 'Potential Server-Side Request Forgery'
            }
        ]
    
    def _load_cwe_mappings(self) -> Dict[str, str]:
        """Load CWE vulnerability mappings"""
        return {
            'sql_injection': 'CWE-89',
            'xss': 'CWE-79',
            'command_injection': 'CWE-78',
            'crypto_weak': 'CWE-327',
            'file_inclusion': 'CWE-98',
            'hardcoded_password': 'CWE-798',
            'information_disclosure': 'CWE-200'
        }
    
    def scan_file(self, file_path: str) -> List[Vulnerability]:
        """Scan a single file for security vulnerabilities"""
        vulnerabilities = []
        
        try:
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                lines = f.readlines()
            
            for line_num, line in enumerate(lines, 1):
                # Scan for each security rule
                for category, rules in self.security_rules.items():
                    for rule in rules:
                        matches = re.finditer(rule['pattern'], line)
                        for match in matches:
                            vulnerability = Vulnerability(
                                id=self._generate_vulnerability_id(),
                                title=rule['description'],
                                description=rule['description'],
                                severity=rule['severity'],
                                category=category,
                                file_path=file_path,
                                line_number=line_num,
                                code_snippet=line.strip(),
                                recommendation=rule['recommendation'],
                                cwe_id=self.cwe_mappings.get(category)
                            )
                            vulnerabilities.append(vulnerability)
                
                # Scan OWASP patterns
                for owasp_rule in self.owasp_patterns:
                    matches = re.finditer(owasp_rule['pattern'], line)
                    for match in matches:
                        vulnerability = Vulnerability(
                            id=self._generate_vulnerability_id(),
                            title=owasp_rule['description'],
                            description=owasp_rule['description'],
                            severity=owasp_rule['severity'],
                            category=owasp_rule['category'],
                            file_path=file_path,
                            line_number=line_num,
                            code_snippet=line.strip(),
                            recommendation='Review and fix security issue'
                        )
                        vulnerabilities.append(vulnerability)
        
        except Exception as e:
            self.logger.error(f"Error scanning file {file_path}: {e}")
        
        return vulnerabilities
    
    def scan_directory(self, directory_path: str, file_extensions: List[str] = None) -> List[Vulnerability]:
        """Scan directory for security vulnerabilities"""
        vulnerabilities = []
        
        if file_extensions is None:
            file_extensions = ['.php', '.py', '.js', '.java', '.rb', '.go', '.cs', '.asp', '.jsp']
        
        for root, dirs, files in os.walk(directory_path):
            for file in files:
                file_path = os.path.join(root, file)
                
                # Check file extension
                if any(file.lower().endswith(ext) for ext in file_extensions):
                    file_vulnerabilities = self.scan_file(file_path)
                    vulnerabilities.extend(file_vulnerabilities)
        
        return vulnerabilities
    
    def scan_web_application(self, base_url: str) -> List[Vulnerability]:
        """Scan web application for common vulnerabilities"""
        vulnerabilities = []
        
        if not REQUESTS_AVAILABLE:
            self.logger.warning("requests library not available, skipping web scan")
            return vulnerabilities
        
        try:
            # Test for common web vulnerabilities
            tests = [
                {
                    'name': 'SQL Injection Test',
                    'url': f"{base_url}/login",
                    'payload': "' OR '1'='1",
                    'pattern': r'(mysql|sql|database).*(error|syntax)',
                    'severity': VulnerabilitySeverity.HIGH
                },
                {
                    'name': 'XSS Test',
                    'url': f"{base_url}/search",
                    'payload': "<script>alert('XSS')</script>",
                    'pattern': r'<script>alert\([\'"]XSS[\'"]\)</script>',
                    'severity': VulnerabilitySeverity.HIGH
                },
                {
                    'name': 'Path Traversal Test',
                    'url': f"{base_url}/file",
                    'payload": "../../../etc/passwd",
                    'pattern': r'root:.*:0:0',
                    'severity': VulnerabilitySeverity.HIGH
                }
            ]
            
            for test in tests:
                try:
                    # Send request with payload
                    response = requests.get(test['url'], params={'q': test['payload']}, timeout=10)
                    
                    # Check response for vulnerability indicators
                    if re.search(test['pattern'], response.text, re.IGNORECASE):
                        vulnerability = Vulnerability(
                            id=self._generate_vulnerability_id(),
                            title=test['name'],
                            description=f"{test['name']} vulnerability detected",
                            severity=test['severity'],
                            category='web_vulnerability',
                            file_path=None,
                            line_number=None,
                            code_snippet=f"URL: {test['url']}, Payload: {test['payload']}",
                            recommendation='Implement proper input validation and output encoding'
                        )
                        vulnerabilities.append(vulnerability)
                
                except Exception as e:
                    self.logger.error(f"Error testing {test['name']}: {e}")
        
        except Exception as e:
            self.logger.error(f"Error scanning web application: {e}")
        
        return vulnerabilities
    
    def check_password_policy(self, passwords: List[str]) -> List[Vulnerability]:
        """Check password policy compliance"""
        vulnerabilities = []
        
        for i, password in enumerate(passwords):
            issues = []
            
            # Check password strength
            if len(password) < 8:
                issues.append("Password too short (< 8 characters)")
            
            if not re.search(r'[A-Z]', password):
                issues.append("No uppercase letters")
            
            if not re.search(r'[a-z]', password):
                issues.append("No lowercase letters")
            
            if not re.search(r'\d', password):
                issues.append("No numbers")
            
            if not re.search(r'[!@#$%^&*(),.?":{}|<>]', password):
                issues.append("No special characters")
            
            # Check for common passwords
            common_passwords = ['password', '123456', 'admin', 'root', 'qwerty']
            if password.lower() in common_passwords:
                issues.append("Common password used")
            
            if issues:
                vulnerability = Vulnerability(
                    id=self._generate_vulnerability_id(),
                    title="Weak Password Policy",
                    description=f"Password policy violation: {', '.join(issues)}",
                    severity=VulnerabilitySeverity.MEDIUM,
                    category='authentication',
                    file_path=None,
                    line_number=i + 1,
                    code_snippet=password,
                    recommendation="Implement strong password policy with complexity requirements"
                )
                vulnerabilities.append(vulnerability)
        
        return vulnerabilities
    
    def check_dependencies(self, requirements_file: str) -> List[Vulnerability]:
        """Check for vulnerable dependencies"""
        vulnerabilities = []
        
        try:
            with open(requirements_file, 'r') as f:
                content = f.read()
            
            # Known vulnerable packages (simplified)
            vulnerable_packages = {
                'django': ['1.11.0', '1.11.1', '1.11.2'],
                'flask': ['0.11.0', '0.11.1'],
                'requests': ['2.20.0', '2.20.1'],
                'urllib3': ['1.24.0', '1.24.1'],
                'pillow': ['6.2.0', '6.2.1']
            }
            
            lines = content.split('\n')
            for line_num, line in enumerate(lines, 1):
                # Parse package name and version
                match = re.match(r'^([a-zA-Z0-9\-_]+)[=<>!]+([0-9\.]+)', line.strip())
                if match:
                    package, version = match.groups()
                    
                    if package in vulnerable_packages and version in vulnerable_packages[package]:
                        vulnerability = Vulnerability(
                            id=self._generate_vulnerability_id(),
                            title=f"Vulnerable Dependency: {package}",
                            description=f"Package {package} version {version} has known vulnerabilities",
                            severity=VulnerabilitySeverity.HIGH,
                            category='dependency',
                            file_path=requirements_file,
                            line_number=line_num,
                            code_snippet=line.strip(),
                            recommendation=f"Update {package} to a secure version"
                        )
                        vulnerabilities.append(vulnerability)
        
        except Exception as e:
            self.logger.error(f"Error checking dependencies: {e}")
        
        return vulnerabilities
    
    def check_file_permissions(self, file_path: str) -> List[Vulnerability]:
        """Check file permissions for security issues"""
        vulnerabilities = []
        
        try:
            stat_info = os.stat(file_path)
            mode = oct(stat_info.st_mode)[-3:]
            
            # Check for world-writable files
            if mode[2] in ['2', '3', '6', '7']:
                vulnerability = Vulnerability(
                    id=self._generate_vulnerability_id(),
                    title="World-Writable File",
                    description=f"File {file_path} is world-writable",
                    severity=VulnerabilitySeverity.MEDIUM,
                    category='file_permissions',
                    file_path=file_path,
                    line_number=None,
                    code_snippet=f"Permissions: {mode}",
                    recommendation="Remove world-write permissions"
                )
                vulnerabilities.append(vulnerability)
            
            # Check for executable files in web directories
            if os.path.basename(os.path.dirname(file_path)) in ['www', 'public_html', 'htdocs']:
                if mode[0] in ['1', '3', '5', '7']:
                    vulnerability = Vulnerability(
                        id=self._generate_vulnerability_id(),
                        title="Executable File in Web Directory",
                        description=f"File {file_path} is executable in web directory",
                        severity=VulnerabilitySeverity.MEDIUM,
                        category='file_permissions',
                        file_path=file_path,
                        line_number=None,
                        code_snippet=f"Permissions: {mode}",
                        recommendation="Remove execute permissions from web-accessible files"
                    )
                    vulnerabilities.append(vulnerability)
        
        except Exception as e:
            self.logger.error(f"Error checking file permissions: {e}")
        
        return vulnerabilities
    
    def _generate_vulnerability_id(self) -> str:
        """Generate unique vulnerability ID"""
        return f"VULN-{int(time.time())}-{hashlib.md5(str(time.time()).encode()).hexdigest()[:8]}"
    
    def calculate_risk_score(self, vulnerabilities: List[Vulnerability]) -> float:
        """Calculate overall risk score"""
        if not vulnerabilities:
            return 0.0
        
        severity_weights = {
            VulnerabilitySeverity.LOW: 1.0,
            VulnerabilitySeverity.MEDIUM: 3.0,
            VulnerabilitySeverity.HIGH: 7.0,
            VulnerabilitySeverity.CRITICAL: 10.0
        }
        
        total_score = 0.0
        for vuln in vulnerabilities:
            total_score += severity_weights[vuln.severity]
        
        # Normalize to 0-100 scale
        max_possible_score = len(vulnerabilities) * 10.0
        normalized_score = (total_score / max_possible_score) * 100
        
        return min(normalized_score, 100.0)
    
    def generate_security_report(self, vulnerabilities: List[Vulnerability], 
                               scan_type: str, target: str) -> SecurityReport:
        """Generate comprehensive security report"""
        scan_id = hashlib.md5(f"{scan_type}{target}{time.time()}".encode()).hexdigest()[:8]
        
        # Calculate risk score
        risk_score = self.calculate_risk_score(vulnerabilities)
        
        # Generate summary
        summary = {
            'total': len(vulnerabilities),
            'critical': len([v for v in vulnerabilities if v.severity == VulnerabilitySeverity.CRITICAL]),
            'high': len([v for v in vulnerabilities if v.severity == VulnerabilitySeverity.HIGH]),
            'medium': len([v for v in vulnerabilities if v.severity == VulnerabilitySeverity.MEDIUM]),
            'low': len([v for v in vulnerabilities if v.severity == VulnerabilitySeverity.LOW])
        }
        
        report = SecurityReport(
            scan_id=scan_id,
            timestamp=datetime.now(),
            scan_type=scan_type,
            target=target,
            vulnerabilities=vulnerabilities,
            risk_score=risk_score,
            summary=summary
        )
        
        return report
    
    def save_report(self, report: SecurityReport, filename: str):
        """Save security report to file"""
        report_data = {
            'scan_id': report.scan_id,
            'timestamp': report.timestamp.isoformat(),
            'scan_type': report.scan_type,
            'target': report.target,
            'risk_score': report.risk_score,
            'summary': report.summary,
            'vulnerabilities': [
                {
                    'id': vuln.id,
                    'title': vuln.title,
                    'description': vuln.description,
                    'severity': vuln.severity.value,
                    'category': vuln.category,
                    'file_path': vuln.file_path,
                    'line_number': vuln.line_number,
                    'code_snippet': vuln.code_snippet,
                    'recommendation': vuln.recommendation,
                    'cwe_id': vuln.cwe_id
                }
                for vuln in report.vulnerabilities
            ]
        }
        
        with open(filename, 'w') as f:
            json.dump(report_data, f, indent=2)
        
        self.logger.info(f"Security report saved: {filename}")
    
    def generate_text_report(self, report: SecurityReport) -> str:
        """Generate text format security report"""
        text_report = []
        text_report.append("=" * 60)
        text_report.append("SECURITY VULNERABILITY REPORT")
        text_report.append("=" * 60)
        text_report.append(f"Scan ID: {report.scan_id}")
        text_report.append(f"Scan Type: {report.scan_type}")
        text_report.append(f"Target: {report.target}")
        text_report.append(f"Timestamp: {report.timestamp.strftime('%Y-%m-%d %H:%M:%S')}")
        text_report.append(f"Risk Score: {report.risk_score:.1f}/100")
        text_report.append("")
        
        # Summary
        text_report.append("VULNERABILITY SUMMARY:")
        text_report.append("-" * 40)
        text_report.append(f"Total Vulnerabilities: {report.summary['total']}")
        text_report.append(f"Critical: {report.summary['critical']}")
        text_report.append(f"High: {report.summary['high']}")
        text_report.append(f"Medium: {report.summary['medium']}")
        text_report.append(f"Low: {report.summary['low']}")
        text_report.append("")
        
        # Vulnerabilities by severity
        if report.vulnerabilities:
            for severity in [VulnerabilitySeverity.CRITICAL, VulnerabilitySeverity.HIGH, 
                           VulnerabilitySeverity.MEDIUM, VulnerabilitySeverity.LOW]:
                severity_vulns = [v for v in report.vulnerabilities if v.severity == severity]
                
                if severity_vulns:
                    text_report.append(f"{severity.value.upper()} SEVERITY:")
                    text_report.append("-" * 40)
                    
                    for vuln in severity_vulns:
                        text_report.append(f"ID: {vuln.id}")
                        text_report.append(f"Title: {vuln.title}")
                        text_report.append(f"Description: {vuln.description}")
                        text_report.append(f"Category: {vuln.category}")
                        
                        if vuln.file_path:
                            text_report.append(f"File: {vuln.file_path}")
                            if vuln.line_number:
                                text_report.append(f"Line: {vuln.line_number}")
                        
                        if vuln.code_snippet:
                            text_report.append(f"Code: {vuln.code_snippet[:100]}...")
                        
                        text_report.append(f"Recommendation: {vuln.recommendation}")
                        
                        if vuln.cwe_id:
                            text_report.append(f"CWE ID: {vuln.cwe_id}")
                        
                        text_report.append("")
        else:
            text_report.append("No vulnerabilities found!")
        
        # Risk assessment
        text_report.append("RISK ASSESSMENT:")
        text_report.append("-" * 40)
        
        if report.risk_score >= 80:
            text_report.append("Risk Level: CRITICAL")
            text_report.append("Immediate action required!")
        elif report.risk_score >= 60:
            text_report.append("Risk Level: HIGH")
            text_report.append("Address vulnerabilities promptly.")
        elif report.risk_score >= 40:
            text_report.append("Risk Level: MEDIUM")
            text_report.append("Plan remediation activities.")
        elif report.risk_score >= 20:
            text_report.append("Risk Level: LOW")
            text_report.append("Monitor and address as needed.")
        else:
            text_report.append("Risk Level: MINIMAL")
            text_report.append("Maintain security practices.")
        
        return "\n".join(text_report)

def create_vulnerable_code_samples():
    """Create sample vulnerable code for testing"""
    vulnerable_code = {
        'vulnerable_php.php': '''
<?php
// SQL Injection vulnerability
$user_input = $_GET['id'];
$query = "SELECT * FROM users WHERE id = " . $user_input;
$result = mysql_query($query);

// XSS vulnerability
echo "<div>Welcome " . $_GET['name'] . "</div>";

// Command injection
system("ls " . $_GET['directory']);

// Hardcoded password
$password = "admin123";

// File inclusion vulnerability
include $_GET['page'] . ".php";

// Information disclosure
var_dump($_SERVER);
?>
''',
        'vulnerable_python.py': '''
# Command injection vulnerability
import os
user_input = input("Enter command: ")
os.system("ls " + user_input)

# Hardcoded password
password = "admin123"

# SQL injection vulnerability
import sqlite3
user_id = input("Enter user ID: ")
query = f"SELECT * FROM users WHERE id = {user_id}"

# File inclusion vulnerability
user_file = input("Enter file: ")
with open(user_file, 'r') as f:
    content = f.read()

# Weak cryptographic hash
import hashlib
password_hash = hashlib.md5("password123".encode()).hexdigest()

# Information disclosure
import sys
print(sys.modules)
'''
    }
    
    # Create vulnerable code directory
    os.makedirs('vulnerable_code', exist_ok=True)
    
    for filename, code in vulnerable_code.items():
        with open(f'vulnerable_code/{filename}', 'w') as f:
            f.write(code)

def main():
    """Main function to demonstrate security scanner"""
    print("=== Security Vulnerability Scanner ===\n")
    
    scanner = SecurityScanner()
    
    # Create vulnerable code samples
    print("1. Creating vulnerable code samples...")
    create_vulnerable_code_samples()
    
    # Scan vulnerable code directory
    print("\n2. Scanning vulnerable code directory...")
    code_vulnerabilities = scanner.scan_directory('vulnerable_code')
    
    print(f"Found {len(code_vulnerabilities)} vulnerabilities in code")
    
    # Check file permissions
    print("\n3. Checking file permissions...")
    permission_vulnerabilities = []
    
    for root, dirs, files in os.walk('vulnerable_code'):
        for file in files:
            file_path = os.path.join(root, file)
            file_vulnerabilities = scanner.check_file_permissions(file_path)
            permission_vulnerabilities.extend(file_vulnerabilities)
    
    print(f"Found {len(permission_vulnerabilities)} permission issues")
    
    # Check password policy
    print("\n4. Checking password policy...")
    test_passwords = [
        'password',
        '123456',
        'admin',
        'weakpass',
        'StrongP@ssw0rd!'
    ]
    
    password_vulnerabilities = scanner.check_password_policy(test_passwords)
    print(f"Found {len(password_vulnerabilities)} password policy violations")
    
    # Create requirements.txt for dependency checking
    print("\n5. Creating requirements file for dependency checking...")
    requirements_content = '''
django==1.11.0
flask==0.11.0
requests==2.20.0
urllib3==1.24.0
pillow==6.2.0
numpy==1.21.0
pandas==1.3.0
'''
    
    with open('requirements.txt', 'w') as f:
        f.write(requirements_content)
    
    dependency_vulnerabilities = scanner.check_dependencies('requirements.txt')
    print(f"Found {len(dependency_vulnerabilities)} vulnerable dependencies")
    
    # Combine all vulnerabilities
    all_vulnerabilities = (code_vulnerabilities + 
                           permission_vulnerabilities + 
                           password_vulnerabilities + 
                           dependency_vulnerabilities)
    
    # Generate security report
    print("\n6. Generating security report...")
    report = scanner.generate_security_report(
        vulnerabilities=all_vulnerabilities,
        scan_type="comprehensive",
        target="vulnerable_code"
    )
    
    # Save reports
    scanner.save_report(report, 'security_report.json')
    
    text_report = scanner.generate_text_report(report)
    with open('security_report.txt', 'w') as f:
        f.write(text_report)
    
    print("Reports saved: security_report.json, security_report.txt")
    
    # Display summary
    print(f"\n7. Security Scan Summary:")
    print(f"Total Vulnerabilities: {len(all_vulnerabilities)}")
    print(f"Risk Score: {report.risk_score:.1f}/100")
    print(f"Critical: {report.summary['critical']}")
    print(f"High: {report.summary['high']}")
    print(f"Medium: {report.summary['medium']}")
    print(f"Low: {report.summary['low']}")
    
    # Show top vulnerabilities
    print("\n8. Top 5 Vulnerabilities:")
    sorted_vulns = sorted(all_vulnerabilities, 
                         key=lambda x: (x.severity.value, x.title), 
                         reverse=True)
    
    for i, vuln in enumerate(sorted_vulns[:5]):
        print(f"{i+1}. [{vuln.severity.value.upper()}] {vuln.title}")
        if vuln.file_path:
            print(f"   File: {vuln.file_path}")
        print(f"   Description: {vuln.description}")
        print()
    
    # Interactive menu
    print("\n=== Security Scanner Interactive ===")
    
    while True:
        print("\nOptions:")
        print("1. Scan directory")
        print("2. Scan file")
        print("3. Scan web application")
        print("4. Check dependencies")
        print("5. Check file permissions")
        print("6. Check password policy")
        print("7. Generate report")
        print("8. Show vulnerability details")
        print("0. Exit")
        
        choice = input("\nSelect option: ").strip()
        
        if choice == "0":
            break
        
        elif choice == "1":
            directory = input("Enter directory path: ").strip()
            if os.path.isdir(directory):
                vulnerabilities = scanner.scan_directory(directory)
                print(f"Found {len(vulnerabilities)} vulnerabilities")
            else:
                print("Directory not found")
        
        elif choice == "2":
            file_path = input("Enter file path: ").strip()
            if os.path.isfile(file_path):
                vulnerabilities = scanner.scan_file(file_path)
                print(f"Found {len(vulnerabilities)} vulnerabilities")
            else:
                print("File not found")
        
        elif choice == "3":
            url = input("Enter web application URL: ").strip()
            if url:
                vulnerabilities = scanner.scan_web_application(url)
                print(f"Found {len(vulnerabilities)} web vulnerabilities")
            else:
                print("Invalid URL")
        
        elif choice == "4":
            req_file = input("Enter requirements file path: ").strip()
            if os.path.isfile(req_file):
                vulnerabilities = scanner.check_dependencies(req_file)
                print(f"Found {len(vulnerabilities)} vulnerable dependencies")
            else:
                print("File not found")
        
        elif choice == "5":
            file_path = input("Enter file path: ").strip()
            if os.path.isfile(file_path):
                vulnerabilities = scanner.check_file_permissions(file_path)
                print(f"Found {len(vulnerabilities)} permission issues")
            else:
                print("File not found")
        
        elif choice == "6":
            passwords_input = input("Enter passwords (comma-separated): ").strip()
            passwords = [p.strip() for p in passwords_input.split(',')]
            vulnerabilities = scanner.check_password_policy(passwords)
            print(f"Found {len(vulnerabilities)} password policy violations")
        
        elif choice == "7":
            scan_type = input("Enter scan type: ").strip()
            target = input("Enter target: ").strip()
            
            if scan_type == "directory" and os.path.isdir(target):
                vulnerabilities = scanner.scan_directory(target)
            elif scan_type == "file" and os.path.isfile(target):
                vulnerabilities = scanner.scan_file(target)
            elif scan_type == "web" and target.startswith(('http://', 'https://')):
                vulnerabilities = scanner.scan_web_application(target)
            else:
                print("Invalid scan type or target")
                continue
            
            report = scanner.generate_security_report(vulnerabilities, scan_type, target)
            scanner.save_report(report, f"security_report_{scan_type}.json")
            print(f"Report saved: security_report_{scan_type}.json")
        
        elif choice == "8":
            if all_vulnerabilities:
                print("\nVulnerability Details:")
                for i, vuln in enumerate(all_vulnerabilities[:10]):
                    print(f"\n{i+1}. {vuln.title}")
                    print(f"   Severity: {vuln.severity.value}")
                    print(f"   Category: {vuln.category}")
                    print(f"   Description: {vuln.description}")
                    print(f"   Recommendation: {vuln.recommendation}")
                    if vuln.file_path:
                        print(f"   File: {vuln.file_path}")
                        if vuln.line_number:
                            print(f"   Line: {vuln.line_number}")
            else:
                print("No vulnerabilities found")
        
        else:
            print("Invalid option")
    
    print("\n=== Security Scanner Demo Completed ===")
    print("Features demonstrated:")
    print("- Static code analysis")
    print("- Web application scanning")
    print("- Dependency vulnerability checking")
    print("- File permission analysis")
    print("- Password policy validation")
    print("- Comprehensive reporting")
    print("- Risk assessment")
    print("- OWASP Top 10 coverage")
    
    print("\nSecurity Categories Covered:")
    print("- SQL Injection")
    print("- Cross-Site Scripting (XSS)")
    print("- Command Injection")
    print("- Cryptographic Failures")
    print("- Authentication Issues")
    print("- File Inclusion")
    print("- Information Disclosure")
    print("- Dependency Vulnerabilities")
    print("- File Permission Issues")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Install dependencies: pip install requests
2. Run scanner: python security_scanner.py
3. Scan code, web applications, and configurations
4. Generate comprehensive security reports

Key Concepts:
- Static Analysis: Code vulnerability detection
- Dynamic Analysis: Web application testing
- OWASP Top 10: Common web vulnerabilities
- CWE Mapping: Common Weakness Enumeration
- Risk Assessment: Vulnerability severity scoring
- Security Policies: Password and permission checks

Vulnerability Types:
- Injection: SQL, command, code injection
- XSS: Cross-site scripting
- Crypto: Weak cryptographic implementations
- Auth: Authentication and authorization issues
- File: File inclusion and permission issues
- Deps: Vulnerable dependencies
- Info: Information disclosure

Scanning Capabilities:
- Static code analysis (multiple languages)
- Web application security testing
- Dependency vulnerability scanning
- File permission analysis
- Password policy validation
- Configuration security checks

Applications:
- Code security review
- Web application penetration testing
- CI/CD security gates
- Compliance auditing
- Security assessment
- Vulnerability management

Dependencies:
- requests: pip install requests (for web scanning)
- re: Built-in Python
- os: Built-in Python
- json: Built-in Python
- datetime: Built-in Python

Best Practices:
- Regular security scanning
- Address high-severity vulnerabilities first
- Implement secure coding practices
- Keep dependencies updated
- Use security testing tools
- Monitor for new vulnerabilities
- Educate developers on security

Legal Note:
- Only scan applications and systems you own or have permission to test
- Unauthorized security testing may be illegal in many jurisdictions
- Follow responsible disclosure practices for found vulnerabilities
"""
