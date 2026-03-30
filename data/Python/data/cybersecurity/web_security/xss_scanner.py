"""
XSS (Cross-Site Scripting) Scanner
====================================

Web security scanner for detecting Cross-Site Scripting vulnerabilities.
Demonstrates web security testing, HTTP requests, and vulnerability assessment.
"""

import requests
import re
import json
import time
from typing import List, Dict, Tuple, Optional
from urllib.parse import urljoin, urlparse, parse_qs
from bs4 import BeautifulSoup
from dataclasses import dataclass
import warnings
warnings.filterwarnings('ignore')

@dataclass
class Vulnerability:
    """Vulnerability data structure"""
    url: str
    parameter: str
    payload: str
    vulnerability_type: str
    severity: str
    description: str
    evidence: str
    recommendation: str

class XSSScanner:
    """Cross-Site Scripting vulnerability scanner"""
    
    def __init__(self, timeout: int = 10, user_agent: str = None):
        self.session = requests.Session()
        self.session.timeout = timeout
        
        if user_agent:
            self.session.headers.update({'User-Agent': user_agent})
        else:
            self.session.headers.update({
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            })
        
        self.vulnerabilities = []
        self.scanned_urls = set()
        
        # XSS payloads for testing
        self.xss_payloads = [
            # Basic XSS payloads
            '<script>alert("XSS")</script>',
            '<script>alert(1)</script>',
            '<img src=x onerror=alert(1)>',
            '<svg onload=alert(1)>',
            
            # Filter bypassing
            '<ScRiPt>alert(1)</ScRiPt>',
            '<script>alert(String.fromCharCode(88,83,83))</script>',
            '<img src=x onerror=alert&#40;1&#41;>',
            '<svg><script>alert(1)</script></svg>',
            
            # Context-specific payloads
            'javascript:alert(1)',
            '" onmouseover="alert(1)"',
            "' onmouseover='alert(1)'",
            '<iframe src="javascript:alert(1)"></iframe>',
            
            # Advanced payloads
            '<script>eval(String.fromCharCode(97,108,101,114,116,40,49,41))</script>',
            '<body onload=alert(1)>',
            '<input autofocus onfocus=alert(1)>',
            '<select onfocus=alert(1) autofocus>',
            '<textarea onfocus=alert(1) autofocus>',
            
            # Encoding variations
            '%3Cscript%3Ealert(1)%3C/script%3E',
            '&#60;script&#62;alert(1)&#60;/script&#62;',
            '&lt;script&gt;alert(1)&lt;/script&gt;',
        ]
        
        # Common XSS contexts
        self.contexts = {
            'html_content': ['<script>alert(1)</script>', '<img src=x onerror=alert(1)>'],
            'html_attribute': ['" onmouseover="alert(1)"', "' onmouseover='alert(1)'" ],
            'javascript': ['javascript:alert(1)', 'javascript:alert(String.fromCharCode(88,83,83))'],
            'css': ['expression(alert(1))', 'url(javascript:alert(1))']
        }
    
    def get_forms(self, url: str) -> List[Dict]:
        """Extract all forms from a webpage"""
        try:
            response = self.session.get(url)
            response.raise_for_status()
            
            soup = BeautifulSoup(response.text, 'html.parser')
            forms = []
            
            for form in soup.find_all('form'):
                form_data = {
                    'action': form.get('action', ''),
                    'method': form.get('method', 'get').lower(),
                    'inputs': []
                }
                
                # Extract form inputs
                for input_tag in form.find_all(['input', 'textarea', 'select']):
                    input_info = {
                        'name': input_tag.get('name', ''),
                        'type': input_tag.get('type', 'text'),
                        'value': input_tag.get('value', ''),
                        'tag': input_tag.name
                    }
                    form_data['inputs'].append(input_info)
                
                forms.append(form_data)
            
            return forms
            
        except Exception as e:
            print(f"Error extracting forms from {url}: {e}")
            return []
    
    def get_links(self, url: str, max_depth: int = 2) -> List[str]:
        """Extract all links from a webpage"""
        try:
            response = self.session.get(url)
            response.raise_for_status()
            
            soup = BeautifulSoup(response.text, 'html.parser')
            links = []
            
            for link in soup.find_all('a', href=True):
                href = link['href']
                absolute_url = urljoin(url, href)
                
                # Only include same-origin links
                if urlparse(absolute_url).netloc == urlparse(url).netloc:
                    links.append(absolute_url)
            
            return list(set(links))  # Remove duplicates
            
        except Exception as e:
            print(f"Error extracting links from {url}: {e}")
            return []
    
    def test_parameter_xss(self, url: str, param_name: str, param_value: str, method: str = 'GET') -> Optional[Vulnerability]:
        """Test a specific parameter for XSS"""
        for payload in self.xss_payloads:
            try:
                if method == 'GET':
                    test_url = f"{url}?{param_name}={payload}"
                    response = self.session.get(test_url)
                else:  # POST
                    data = {param_name: payload}
                    response = self.session.post(url, data=data)
                
                # Check if payload is reflected in response
                if payload in response.text or self._check_xss_execution(response.text):
                    vulnerability = Vulnerability(
                        url=url,
                        parameter=param_name,
                        payload=payload,
                        vulnerability_type='Cross-Site Scripting',
                        severity='HIGH',
                        description=f'XSS vulnerability found in parameter "{param_name}"',
                        evidence=f'Payload "{payload}" was reflected in the response',
                        recommendation='Sanitize and validate all user input. Use proper output encoding.'
                    )
                    return vulnerability
                    
            except Exception as e:
                print(f"Error testing parameter {param_name}: {e}")
                continue
        
        return None
    
    def test_form_xss(self, url: str, form: Dict) -> List[Vulnerability]:
        """Test a form for XSS vulnerabilities"""
        vulnerabilities = []
        
        try:
            form_action = urljoin(url, form['action'])
            form_method = form.get('method', 'get')
            
            # Prepare form data
            form_data = {}
            for input_tag in form['inputs']:
                if input_tag['name']:
                    # Test each input field separately
                    for payload in self.xss_payloads[:5]:  # Test first 5 payloads
                        test_data = {}
                        for other_input in form['inputs']:
                            if other_input['name']:
                                if other_input['name'] == input_tag['name']:
                                    test_data[other_input['name']] = payload
                                else:
                                    test_data[other_input['name']] = other_input['value'] or 'test'
                        
                        try:
                            if form_method == 'get':
                                response = self.session.get(form_action, params=test_data)
                            else:
                                response = self.session.post(form_action, data=test_data)
                            
                            # Check for XSS
                            if payload in response.text or self._check_xss_execution(response.text):
                                vulnerability = Vulnerability(
                                    url=form_action,
                                    parameter=input_tag['name'],
                                    payload=payload,
                                    vulnerability_type='Cross-Site Scripting',
                                    severity='HIGH',
                                    description=f'XSS vulnerability in form field "{input_tag['name']}"',
                                    evidence=f'Payload "{payload}" was reflected in form response',
                                    recommendation='Implement proper input validation and output encoding in form processing.'
                                )
                                vulnerabilities.append(vulnerability)
                                break  # Found vulnerability, move to next input
                        
                        except Exception as e:
                            print(f"Error testing form field {input_tag['name']}: {e}")
                            continue
                
        except Exception as e:
            print(f"Error testing form: {e}")
        
        return vulnerabilities
    
    def test_url_parameters(self, url: str) -> List[Vulnerability]:
        """Test URL parameters for XSS"""
        vulnerabilities = []
        
        try:
            parsed_url = urlparse(url)
            query_params = parse_qs(parsed_url.query)
            
            for param_name, param_values in query_params.items():
                if param_values:
                    vulnerability = self.test_parameter_xss(url, param_name, param_values[0])
                    if vulnerability:
                        vulnerabilities.append(vulnerability)
        
        except Exception as e:
            print(f"Error testing URL parameters: {e}")
        
        return vulnerabilities
    
    def test_reflected_xss(self, url: str) -> List[Vulnerability]:
        """Test for reflected XSS vulnerabilities"""
        vulnerabilities = []
        
        # Test URL parameters
        vulnerabilities.extend(self.test_url_parameters(url))
        
        # Test forms
        forms = self.get_forms(url)
        for form in forms:
            form_vulns = self.test_form_xss(url, form)
            vulnerabilities.extend(form_vulns)
        
        return vulnerabilities
    
    def _check_xss_execution(self, response_text: str) -> bool:
        """Check if XSS payload was executed (simplified check)"""
        # Look for common XSS execution indicators
        xss_indicators = [
            'alert("XSS")',
            'alert(1)',
            'javascript:',
            '<script>',
            'onerror=',
            'onload=',
            'onmouseover=',
            'onfocus='
        ]
        
        for indicator in xss_indicators:
            if indicator.lower() in response_text.lower():
                return True
        
        return False
    
    def scan_url(self, url: str, crawl: bool = False, max_depth: int = 2) -> List[Vulnerability]:
        """Scan a single URL for XSS vulnerabilities"""
        print(f"Scanning URL: {url}")
        
        if url in self.scanned_urls:
            return []
        
        self.scanned_urls.add(url)
        vulnerabilities = []
        
        # Test the main URL
        url_vulnerabilities = self.test_reflected_xss(url)
        vulnerabilities.extend(url_vulnerabilities)
        
        # Crawl and test additional URLs if requested
        if crawl:
            links = self.get_links(url)
            for link in links[:20]:  # Limit to 20 links to prevent infinite crawling
                if link not in self.scanned_urls:
                    link_vulnerabilities = self.test_reflected_xss(link)
                    vulnerabilities.extend(link_vulnerabilities)
                    self.scanned_urls.add(link)
        
        return vulnerabilities
    
    def scan_multiple_urls(self, urls: List[str]) -> List[Vulnerability]:
        """Scan multiple URLs for XSS vulnerabilities"""
        all_vulnerabilities = []
        
        for url in urls:
            try:
                vulnerabilities = self.scan_url(url)
                all_vulnerabilities.extend(vulnerabilities)
                time.sleep(1)  # Rate limiting
            except Exception as e:
                print(f"Error scanning {url}: {e}")
        
        return all_vulnerabilities
    
    def generate_report(self, vulnerabilities: List[Vulnerability]) -> str:
        """Generate vulnerability report"""
        report = []
        report.append("=" * 60)
        report.append("XSS VULNERABILITY SCAN REPORT")
        report.append("=" * 60)
        report.append(f"Scan Date: {time.strftime('%Y-%m-%d %H:%M:%S')}")
        report.append(f"Total Vulnerabilities Found: {len(vulnerabilities)}")
        report.append("")
        
        if not vulnerabilities:
            report.append("✅ No XSS vulnerabilities found!")
            report.append("")
            report.append("Security Recommendations:")
            report.append("- Implement proper input validation")
            report.append("- Use output encoding for all user input")
            report.append("- Use Content Security Policy (CSP)")
            report.append("- Keep frameworks and libraries updated")
            report.append("- Regular security testing")
        else:
            # Group vulnerabilities by severity
            by_severity = {'HIGH': [], 'MEDIUM': [], 'LOW': []}
            
            for vuln in vulnerabilities:
                by_severity[vuln.severity].append(vuln)
            
            for severity in ['HIGH', 'MEDIUM', 'LOW']:
                if by_severity[severity]:
                    report.append(f"{severity} SEVERITY ({len(by_severity[severity])}):")
                    report.append("-" * 40)
                    
                    for i, vuln in enumerate(by_severity[severity], 1):
                        report.append(f"{i}. {vuln.url}")
                        report.append(f"   Parameter: {vuln.parameter}")
                        report.append(f"   Payload: {vuln.payload[:50]}...")
                        report.append(f"   Description: {vuln.description}")
                        report.append(f"   Recommendation: {vuln.recommendation}")
                        report.append("")
        
        return "\n".join(report)
    
    def save_report(self, vulnerabilities: List[Vulnerability], filename: str):
        """Save vulnerability report to file"""
        report = self.generate_report(vulnerabilities)
        
        with open(filename, 'w') as f:
            f.write(report)
        
        print(f"Report saved to: {filename}")
    
    def save_json_report(self, vulnerabilities: List[Vulnerability], filename: str):
        """Save vulnerability report as JSON"""
        json_data = {
            'scan_date': time.strftime('%Y-%m-%d %H:%M:%S'),
            'total_vulnerabilities': len(vulnerabilities),
            'vulnerabilities': [
                {
                    'url': vuln.url,
                    'parameter': vuln.parameter,
                    'payload': vuln.payload,
                    'type': vuln.vulnerability_type,
                    'severity': vuln.severity,
                    'description': vuln.description,
                    'evidence': vuln.evidence,
                    'recommendation': vuln.recommendation
                }
                for vuln in vulnerabilities
            ]
        }
        
        with open(filename, 'w') as f:
            json.dump(json_data, f, indent=2)
        
        print(f"JSON report saved to: {filename}")

def create_test_server():
    """Create a simple test server with XSS vulnerabilities"""
    from http.server import HTTPServer, BaseHTTPRequestHandler
    
    class XSSHandler(BaseHTTPRequestHandler):
        def do_GET(self):
            if self.path == '/':
                self.send_response(200)
                self.send_header('Content-type', 'text/html')
                self.end_headers()
                
                html = '''
                <html>
                <head><title>XSS Test Server</title></head>
                <body>
                    <h1>XSS Vulnerability Test Server</h1>
                    
                    <h2>Reflected XSS Test</h2>
                    <p>Current search: <span id="search">{}</span></p>
                    
                    <h2>Search Form</h2>
                    <form method="GET" action="/search">
                        <input type="text" name="q" value="">
                        <input type="submit" value="Search">
                    </form>
                    
                    <h2>Comment Form</h2>
                    <form method="POST" action="/comment">
                        <textarea name="comment"></textarea>
                        <input type="submit" value="Submit">
                    </form>
                </body>
                </html>
                '''.format(self.path.split('?q=')[-1] if '?q=' in self.path else '')
                
                self.wfile.write(html.encode())
            
            elif self.path.startswith('/search'):
                query = self.path.split('?q=')[-1] if '?q=' in self.path else ''
                
                self.send_response(200)
                self.send_header('Content-type', 'text/html')
                self.end_headers()
                
                html = f'''
                <html>
                <head><title>Search Results</title></head>
                <body>
                    <h1>Search Results</h1>
                    <p>You searched for: {query}</p>
                    <p>No results found for "{query}"</p>
                    <a href="/">Back</a>
                </body>
                </html>
                '''
                
                self.wfile.write(html.encode())
            
            else:
                self.send_response(404)
                self.end_headers()
                self.wfile.write(b'Not Found')
        
        def do_POST(self):
            if self.path == '/comment':
                content_length = int(self.headers['Content-Length'])
                post_data = self.rfile.read(content_length).decode('utf-8')
                
                # Parse form data
                comment = ''
                for line in post_data.split('&'):
                    if line.startswith('comment='):
                        comment = line.split('=')[1].replace('+', ' ')
                
                self.send_response(200)
                self.send_header('Content-type', 'text/html')
                self.end_headers()
                
                html = f'''
                <html>
                <head><title>Comment Submitted</title></head>
                <body>
                    <h1>Comment Submitted</h1>
                    <p>Your comment: {comment}</p>
                    <p>Thank you for your submission!</p>
                    <a href="/">Back</a>
                </body>
                </html>
                '''
                
                self.wfile.write(html.encode())
            else:
                self.send_response(404)
                self.end_headers()
                self.wfile.write(b'Not Found')
    
    return HTTPServer(('localhost', 8080), XSSHandler)

def main():
    """Main function to demonstrate XSS scanner"""
    print("=== XSS (Cross-Site Scripting) Scanner ===\n")
    
    scanner = XSSScanner()
    
    print("Choose scanning mode:")
    print("1. Scan single URL")
    print("2. Scan multiple URLs")
    print("3. Scan test server (with vulnerabilities)")
    print("4. Load URLs from file")
    
    choice = input("Select mode (1-4): ").strip()
    
    if choice == "1":
        # Single URL scan
        url = input("Enter URL to scan: ").strip()
        if not url.startswith(('http://', 'https://')):
            url = 'https://' + url
        
        print(f"\nScanning {url}...")
        vulnerabilities = scanner.scan_url(url, crawl=True)
        
        report = scanner.generate_report(vulnerabilities)
        print("\n" + report)
        
        # Save reports
        scanner.save_report(vulnerabilities, 'xss_report.txt')
        scanner.save_json_report(vulnerabilities, 'xss_report.json')
    
    elif choice == "2":
        # Multiple URLs scan
        urls_input = input("Enter URLs (comma-separated): ").strip()
        urls = [url.strip() for url in urls_input.split(',')]
        
        # Add protocol if missing
        for i, url in enumerate(urls):
            if not url.startswith(('http://', 'https://')):
                urls[i] = 'https://' + url
        
        print(f"\nScanning {len(urls)} URLs...")
        vulnerabilities = scanner.scan_multiple_urls(urls)
        
        report = scanner.generate_report(vulnerabilities)
        print("\n" + report)
        
        scanner.save_report(vulnerabilities, 'xss_report.txt')
        scanner.save_json_report(vulnerabilities, 'xss_report.json')
    
    elif choice == "3":
        # Test server scan
        print("Starting test server with XSS vulnerabilities...")
        print("Server will be available at: http://localhost:8080")
        print("Press Ctrl+C to stop the server and start scanning")
        
        try:
            server = create_test_server()
            server_thread = threading.Thread(target=server.serve_forever)
            server_thread.daemon = True
            server_thread.start()
            
            # Wait a moment for server to start
            time.sleep(2)
            
            # Scan the test server
            test_url = "http://localhost:8080"
            print(f"\nScanning test server: {test_url}")
            vulnerabilities = scanner.scan_url(test_url, crawl=True)
            
            report = scanner.generate_report(vulnerabilities)
            print("\n" + report)
            
            scanner.save_report(vulnerabilities, 'xss_test_report.txt')
            scanner.save_json_report(vulnerabilities, 'xss_test_report.json')
            
            # Stop server
            server.shutdown()
            
        except KeyboardInterrupt:
            print("\nScan interrupted by user")
        except Exception as e:
            print(f"Error with test server: {e}")
    
    elif choice == "4":
        # Load URLs from file
        filename = input("Enter filename with URLs: ").strip()
        
        try:
            with open(filename, 'r') as f:
                urls = [line.strip() for line in f if line.strip()]
            
            print(f"Loaded {len(urls)} URLs from file")
            vulnerabilities = scanner.scan_multiple_urls(urls)
            
            report = scanner.generate_report(vulnerabilities)
            print("\n" + report)
            
            scanner.save_report(vulnerabilities, 'xss_report.txt')
            scanner.save_json_report(vulnerabilities, 'xss_report.json')
            
        except FileNotFoundError:
            print(f"File not found: {filename}")
        except Exception as e:
            print(f"Error reading file: {e}")
    
    else:
        print("Invalid choice")
    
    print("\n=== XSS Scanner Demo Completed ===")
    print("Features demonstrated:")
    print("- URL parameter scanning")
    print("- Form field testing")
    print("- Multiple payload testing")
    print("- Vulnerability reporting")
    print("- Test server with XSS vulnerabilities")
    
    print("\nSecurity Recommendations:")
    print("- Always validate and sanitize user input")
    print("- Use proper output encoding (HTML, JavaScript, CSS)")
    print("- Implement Content Security Policy (CSP)")
    print("- Use security headers (X-XSS-Protection, etc.)")
    print("- Keep web frameworks and libraries updated")
    print("- Regular security testing and code reviews")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Install dependencies: pip install requests beautifulsoup4
2. Run scanner: python xss_scanner.py
3. Choose scanning mode (single URL, multiple URLs, test server, file)
4. View vulnerability report and recommendations
5. Reports saved to text and JSON files

Key Concepts:
- Cross-Site Scripting (XSS): Injection of malicious scripts
- Reflected XSS: Malicious script reflected in response
- Stored XSS: Malicious script stored and served later
- DOM-based XSS: Client-side XSS in DOM manipulation
- Payload Testing: Various XSS payload variations

Detection Techniques:
- Parameter injection testing
- Form field testing
- URL parameter scanning
- Response analysis
- Payload reflection checking

Payload Categories:
- Basic script tags
- Event handlers
- JavaScript protocols
- Encoding variations
- Filter bypassing

Applications:
- Web application security testing
- Vulnerability assessment
- Penetration testing
- Security auditing
- Compliance testing
- Development security

Legal Notice:
Only scan websites and applications you own or have explicit permission to test.
Unauthorized security testing may be illegal in many jurisdictions.
"""
