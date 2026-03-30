"""
Network Port Scanner
===================

Comprehensive network port scanning tool for security assessment.
Demonstrates network programming, socket operations, and security scanning techniques.
"""

import socket
import threading
import time
import concurrent.futures
from typing import List, Dict, Tuple, Optional
import json
import argparse
import sys
import ipaddress
from dataclasses import dataclass
from enum import Enum

class PortStatus(Enum):
    """Port scan status enumeration"""
    OPEN = "open"
    CLOSED = "closed"
    FILTERED = "filtered"
    TIMEOUT = "timeout"

@dataclass
class ScanResult:
    """Port scan result data class"""
    host: str
    port: int
    status: PortStatus
    service: Optional[str] = None
    banner: Optional[str] = None
    response_time: Optional[float] = None

class PortScanner:
    """Advanced network port scanner"""
    
    def __init__(self, timeout: float = 2.0, max_threads: int = 100):
        self.timeout = timeout
        self.max_threads = max_threads
        self.common_ports = self._get_common_ports()
        self.service_map = self._get_service_map()
        self.scan_results = []
        self.start_time = None
        self.end_time = None
    
    def _get_common_ports(self) -> List[int]:
        """Get list of common ports to scan"""
        return [
            21, 22, 23, 25, 53, 80, 110, 111, 135, 139, 143, 443, 993, 995,
            1723, 3306, 3389, 5432, 5900, 8080, 8443, 9200, 11211, 27017
        ]
    
    def _get_service_map(self) -> Dict[int, str]:
        """Get port to service mapping"""
        return {
            21: "FTP", 22: "SSH", 23: "Telnet", 25: "SMTP", 53: "DNS",
            80: "HTTP", 110: "POP3", 111: "RPC", 135: "RPC", 139: "NetBIOS",
            143: "IMAP", 443: "HTTPS", 993: "IMAPS", 995: "POP3S",
            1723: "PPTP", 3306: "MySQL", 3389: "RDP", 5432: "PostgreSQL",
            5900: "VNC", 8080: "HTTP-Alt", 8443: "HTTPS-Alt", 9200: "Elasticsearch",
            11211: "Memcached", 27017: "MongoDB"
        }
    
    def scan_port(self, host: str, port: int) -> ScanResult:
        """Scan a single port"""
        result = ScanResult(host=host, port=port, status=PortStatus.CLOSED)
        
        try:
            start_time = time.time()
            
            # Create socket
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            sock.settimeout(self.timeout)
            
            # Try to connect
            connection_result = sock.connect_ex((host, port))
            response_time = time.time() - start_time
            
            if connection_result == 0:
                result.status = PortStatus.OPEN
                result.service = self.service_map.get(port, "Unknown")
                result.response_time = response_time
                
                # Try to get banner
                try:
                    banner = sock.recv(1024).decode('utf-8', errors='ignore').strip()
                    if banner:
                        result.banner = banner[:100]  # Limit banner length
                except:
                    pass
            else:
                result.status = PortStatus.CLOSED
            
            sock.close()
            
        except socket.timeout:
            result.status = PortStatus.TIMEOUT
        except socket.gaierror:
            result.status = PortStatus.FILTERED
        except Exception as e:
            result.status = PortStatus.FILTERED
        
        return result
    
    def scan_ports_range(self, host: str, start_port: int, end_port: int) -> List[ScanResult]:
        """Scan a range of ports"""
        print(f"Scanning {host} ports {start_port}-{end_port}...")
        self.start_time = time.time()
        
        results = []
        ports_to_scan = list(range(start_port, end_port + 1))
        
        with concurrent.futures.ThreadPoolExecutor(max_workers=self.max_threads) as executor:
            # Submit all scan tasks
            future_to_port = {
                executor.submit(self.scan_port, host, port): port 
                for port in ports_to_scan
            }
            
            # Collect results
            for future in concurrent.futures.as_completed(future_to_port):
                port = future_to_port[future]
                try:
                    result = future.result()
                    results.append(result)
                    
                    # Print progress
                    if result.status == PortStatus.OPEN:
                        print(f"Port {port} OPEN - {result.service}")
                    
                except Exception as e:
                    print(f"Error scanning port {port}: {e}")
        
        self.end_time = time.time()
        self.scan_results = results
        
        print(f"Scan completed in {self.end_time - self.start_time:.2f} seconds")
        return results
    
    def scan_common_ports(self, host: str) -> List[ScanResult]:
        """Scan common ports only"""
        print(f"Scanning common ports on {host}...")
        return self.scan_ports_range(host, min(self.common_ports), max(self.common_ports))
    
    def quick_scan(self, host: str) -> List[ScanResult]:
        """Quick scan of top 20 ports"""
        top_ports = self.common_ports[:20]
        print(f"Quick scan of top 20 ports on {host}...")
        
        results = []
        self.start_time = time.time()
        
        with concurrent.futures.ThreadPoolExecutor(max_workers=self.max_threads) as executor:
            future_to_port = {
                executor.submit(self.scan_port, host, port): port 
                for port in top_ports
            }
            
            for future in concurrent.futures.as_completed(future_to_port):
                port = future_to_port[future]
                try:
                    result = future.result()
                    results.append(result)
                    
                    if result.status == PortStatus.OPEN:
                        print(f"Port {port} OPEN - {result.service}")
                        
                except Exception as e:
                    print(f"Error scanning port {port}: {e}")
        
        self.end_time = time.time()
        self.scan_results = results
        
        print(f"Quick scan completed in {self.end_time - self.start_time:.2f} seconds")
        return results
    
    def scan_multiple_hosts(self, hosts: List[str], ports: List[int]) -> Dict[str, List[ScanResult]]:
        """Scan multiple hosts"""
        print(f"Scanning {len(hosts)} hosts...")
        all_results = {}
        
        for host in hosts:
            print(f"\nScanning host: {host}")
            host_results = []
            
            with concurrent.futures.ThreadPoolExecutor(max_workers=self.max_threads) as executor:
                future_to_port = {
                    executor.submit(self.scan_port, host, port): port 
                    for port in ports
                }
                
                for future in concurrent.futures.as_completed(future_to_port):
                    port = future_to_port[future]
                    try:
                        result = future.result()
                        host_results.append(result)
                        
                        if result.status == PortStatus.OPEN:
                            print(f"  Port {port} OPEN - {result.service}")
                            
                    except Exception as e:
                        print(f"  Error scanning port {port}: {e}")
            
            all_results[host] = host_results
        
        return all_results
    
    def get_open_ports(self, results: List[ScanResult] = None) -> List[ScanResult]:
        """Get list of open ports from results"""
        if results is None:
            results = self.scan_results
        
        return [r for r in results if r.status == PortStatus.OPEN]
    
    def get_service_summary(self, results: List[ScanResult] = None) -> Dict[str, List[int]]:
        """Get summary of services found"""
        if results is None:
            results = self.scan_results
        
        services = {}
        for result in results:
            if result.status == PortStatus.OPEN:
                service = result.service or "Unknown"
                if service not in services:
                    services[service] = []
                services[service].append(result.port)
        
        return services
    
    def generate_report(self, results: List[ScanResult] = None) -> str:
        """Generate detailed scan report"""
        if results is None:
            results = self.scan_results
        
        open_ports = self.get_open_ports(results)
        services = self.get_service_summary(results)
        
        report = []
        report.append("=" * 60)
        report.append("PORT SCAN REPORT")
        report.append("=" * 60)
        report.append(f"Scan Duration: {self.end_time - self.start_time:.2f} seconds")
        report.append(f"Total Ports Scanned: {len(results)}")
        report.append(f"Open Ports Found: {len(open_ports)}")
        report.append("")
        
        # Open ports details
        if open_ports:
            report.append("OPEN PORTS:")
            report.append("-" * 40)
            for result in open_ports:
                report.append(f"Port {result.port:<6} {result.service:<15} "
                            f"Time: {result.response_time:.3f}s")
                if result.banner:
                    report.append(f"  Banner: {result.banner}")
            report.append("")
        
        # Services summary
        if services:
            report.append("SERVICES FOUND:")
            report.append("-" * 40)
            for service, ports in services.items():
                report.append(f"{service:<15}: {', '.join(map(str, ports))}")
            report.append("")
        
        # Security assessment
        report.append("SECURITY ASSESSMENT:")
        report.append("-" * 40)
        
        risky_services = ['Telnet', 'FTP', 'HTTP', 'RPC', 'NetBIOS']
        found_risky = []
        
        for service, ports in services.items():
            if service in risky_services:
                found_risky.append(f"{service} ({', '.join(map(str, ports))})")
        
        if found_risky:
            report.append("⚠️  RISKY SERVICES DETECTED:")
            for service in found_risky:
                report.append(f"   - {service}")
            report.append("")
            report.append("Recommendations:")
            report.append("- Close unnecessary services")
            report.append("- Use secure alternatives (HTTPS, SSH, SFTP)")
            report.append("- Implement firewall rules")
            report.append("- Regular security updates")
        else:
            report.append("✅ No obvious risky services detected")
        
        return "\n".join(report)
    
    def save_results(self, filename: str, results: List[ScanResult] = None) -> None:
        """Save scan results to JSON file"""
        if results is None:
            results = self.scan_results
        
        # Convert results to serializable format
        json_results = []
        for result in results:
            json_results.append({
                'host': result.host,
                'port': result.port,
                'status': result.status.value,
                'service': result.service,
                'banner': result.banner,
                'response_time': result.response_time
            })
        
        scan_data = {
            'scan_time': self.start_time,
            'duration': self.end_time - self.start_time if self.end_time else None,
            'results': json_results
        }
        
        with open(filename, 'w') as f:
            json.dump(scan_data, f, indent=2)
        
        print(f"Results saved to: {filename}")
    
    def load_results(self, filename: str) -> List[ScanResult]:
        """Load scan results from JSON file"""
        with open(filename, 'r') as f:
            scan_data = json.load(f)
        
        results = []
        for result_data in scan_data['results']:
            result = ScanResult(
                host=result_data['host'],
                port=result_data['port'],
                status=PortStatus(result_data['status']),
                service=result_data.get('service'),
                banner=result_data.get('banner'),
                response_time=result_data.get('response_time')
            )
            results.append(result)
        
        self.scan_results = results
        self.start_time = scan_data['scan_time']
        self.end_time = self.start_time + scan_data['duration'] if scan_data['duration'] else None
        
        print(f"Results loaded from: {filename}")
        return results

def validate_host(host: str) -> bool:
    """Validate host address"""
    try:
        ipaddress.ip_address(host)
        return True
    except ValueError:
        try:
            socket.gethostbyname(host)
            return True
        except socket.gaierror:
            return False

def main():
    """Main function to demonstrate port scanner"""
    print("=== Network Port Scanner ===\n")
    
    scanner = PortScanner(timeout=2.0, max_threads=50)
    
    # Get target host
    target_host = input("Enter target host (IP or hostname) [default: localhost]: ").strip()
    if not target_host:
        target_host = "localhost"
    
    if not validate_host(target_host):
        print(f"Invalid host: {target_host}")
        return
    
    print(f"Target host: {target_host}")
    
    # Scan options
    print("\nScan Options:")
    print("1. Quick scan (top 20 ports)")
    print("2. Common ports scan")
    print("3. Custom port range")
    print("4. Scan multiple hosts")
    
    choice = input("Select scan type (1-4): ").strip()
    
    if choice == "1":
        # Quick scan
        results = scanner.quick_scan(target_host)
        
    elif choice == "2":
        # Common ports scan
        results = scanner.scan_common_ports(target_host)
        
    elif choice == "3":
        # Custom port range
        try:
            start_port = int(input("Enter start port: "))
            end_port = int(input("Enter end port: "))
            
            if start_port < 1 or end_port > 65535 or start_port > end_port:
                print("Invalid port range")
                return
            
            results = scanner.scan_ports_range(target_host, start_port, end_port)
            
        except ValueError:
            print("Invalid port numbers")
            return
    
    elif choice == "4":
        # Multiple hosts
        hosts_input = input("Enter hosts (comma-separated): ").strip()
        hosts = [h.strip() for h in hosts_input.split(',')]
        
        # Validate hosts
        valid_hosts = []
        for host in hosts:
            if validate_host(host):
                valid_hosts.append(host)
            else:
                print(f"Invalid host: {host}")
        
        if not valid_hosts:
            print("No valid hosts provided")
            return
        
        # Choose port range
        port_choice = input("Port range (1: quick, 2: common, 3: custom): ").strip()
        
        if port_choice == "1":
            ports = scanner.common_ports[:20]
        elif port_choice == "2":
            ports = scanner.common_ports
        elif port_choice == "3":
            try:
                start_port = int(input("Enter start port: "))
                end_port = int(input("Enter end port: "))
                ports = list(range(start_port, end_port + 1))
            except ValueError:
                print("Invalid port range")
                return
        else:
            ports = scanner.common_ports[:20]
        
        results_dict = scanner.scan_multiple_hosts(valid_hosts, ports)
        
        # Flatten results for reporting
        results = []
        for host_results in results_dict.values():
            results.extend(host_results)
        
        scanner.scan_results = results
    else:
        print("Invalid choice")
        return
    
    # Generate and display report
    print("\n" + "=" * 60)
    print(scanner.generate_report())
    
    # Save results
    save_choice = input("\nSave results to file? (y/n): ").strip().lower()
    if save_choice == 'y':
        filename = input("Enter filename [default: scan_results.json]: ").strip()
        if not filename:
            filename = "scan_results.json"
        scanner.save_results(filename)
    
    # Show statistics
    open_ports = scanner.get_open_ports()
    services = scanner.get_service_summary()
    
    print(f"\nScan Statistics:")
    print(f"  Total ports scanned: {len(results)}")
    print(f"  Open ports: {len(open_ports)}")
    print(f"  Services found: {len(services)}")
    print(f"  Scan duration: {scanner.end_time - scanner.start_time:.2f} seconds")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Run scanner: python port_scanner.py
2. Enter target host (IP or hostname)
3. Choose scan type (quick, common, custom, multiple)
4. View results and security assessment
5. Save results to JSON file

Key Concepts:
- Socket Programming: Network communication basics
- Multi-threading: Concurrent port scanning
- Port States: Open, closed, filtered, timeout
- Service Identification: Port to service mapping
- Banner Grabbing: Service information gathering

Applications:
- Network security assessment
- Vulnerability scanning
- Service discovery
- Network inventory
- Penetration testing
- Security auditing

Scan Types:
- Quick Scan: Top 20 common ports
- Common Ports: Well-known services
- Custom Range: User-defined port range
- Multiple Hosts: Batch scanning

Security Features:
- Concurrent scanning for performance
- Timeout handling
- Error recovery
- Service identification
- Banner grabbing
- Security assessment

Legal Notice:
Only scan networks and systems you own or have explicit permission to test.
Unauthorized port scanning may be illegal in many jurisdictions.
"""
