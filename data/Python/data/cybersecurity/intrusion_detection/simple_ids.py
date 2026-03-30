"""
Simple Intrusion Detection System
=================================

Basic intrusion detection system for network and host-based monitoring.
Demonstrates pattern matching, anomaly detection, and security monitoring.
"""

import re
import time
import json
import hashlib
import threading
from typing import Dict, List, Tuple, Optional, Callable
from datetime import datetime, timedelta
from dataclasses import dataclass, asdict
from collections import defaultdict, deque
import socket
import os

@dataclass
class SecurityEvent:
    """Security event data structure"""
    timestamp: datetime
    event_type: str
    severity: str  # LOW, MEDIUM, HIGH, CRITICAL
    source: str
    details: Dict
    raw_data: str
    
    def to_dict(self) -> Dict:
        """Convert to dictionary for JSON serialization"""
        data = asdict(self)
        data['timestamp'] = self.timestamp.isoformat()
        return data

@dataclass
class Alert:
    """Security alert data structure"""
    id: str
    timestamp: datetime
    alert_type: str
    severity: str
    message: str
    source: str
    confidence: float
    events: List[SecurityEvent]
    
    def to_dict(self) -> Dict:
        """Convert to dictionary for JSON serialization"""
        data = asdict(self)
        data['timestamp'] = self.timestamp.isoformat()
        data['events'] = [event.to_dict() for event in self.events]
        return data

class RuleEngine:
    """Rule-based detection engine"""
    
    def __init__(self):
        self.rules = []
        self.load_default_rules()
    
    def load_default_rules(self):
        """Load default detection rules"""
        self.rules = [
            # Failed login attempts
            {
                'name': 'Failed Login Brute Force',
                'pattern': r'Failed password for .* from (\d+\.\d+\.\d+\.\d+)',
                'event_type': 'authentication_failure',
                'severity': 'HIGH',
                'threshold': 5,
                'time_window': 300,  # 5 minutes
                'description': 'Multiple failed login attempts detected'
            },
            
            # SSH connection attempts
            {
                'name': 'SSH Connection',
                'pattern': r'sshd.*Accepted password for .* from (\d+\.\d+\.\d+\.\d+)',
                'event_type': 'ssh_connection',
                'severity': 'LOW',
                'threshold': 1,
                'time_window': 60,
                'description': 'SSH connection established'
            },
            
            # Port scanning detection
            {
                'name': 'Port Scan Detected',
                'pattern': r'Connection from (\d+\.\d+\.\d+\.\d+):(\d+) to port (\d+)',
                'event_type': 'port_scan',
                'severity': 'MEDIUM',
                'threshold': 10,
                'time_window': 60,
                'description': 'Multiple connection attempts detected'
            },
            
            # Suspicious process execution
            {
                'name': 'Suspicious Process',
                'pattern': r'Process (\w+) executed by user (\w+) with PID (\d+)',
                'event_type': 'suspicious_process',
                'severity': 'MEDIUM',
                'threshold': 1,
                'time_window': 60,
                'description': 'Suspicious process execution detected'
            },
            
            # File system access
            {
                'name': 'Sensitive File Access',
                'pattern': r'Access to (\/etc\/passwd|\/etc\/shadow|\/root\/.*) by user (\w+)',
                'event_type': 'sensitive_file_access',
                'severity': 'HIGH',
                'threshold': 1,
                'time_window': 60,
                'description': 'Access to sensitive file detected'
            },
            
            # Network anomalies
            {
                'name': 'Unusual Network Traffic',
                'pattern': r'Transfer of (\d+) bytes from (\d+\.\d+\.\d+\.\d+) to (\d+\.\d+\.\d+\.\d+)',
                'event_type': 'network_anomaly',
                'severity': 'MEDIUM',
                'threshold': 1000000,  # 1MB
                'time_window': 300,
                'description': 'Unusual network traffic detected'
            }
        ]
    
    def add_rule(self, rule: Dict):
        """Add custom detection rule"""
        self.rules.append(rule)
    
    def match_pattern(self, log_line: str) -> Optional[Dict]:
        """Match log line against rules"""
        for rule in self.rules:
            match = re.search(rule['pattern'], log_line, re.IGNORECASE)
            if match:
                return {
                    'rule': rule,
                    'match': match.groups(),
                    'event_type': rule['event_type'],
                    'severity': rule['severity']
                }
        return None

class AnomalyDetector:
    """Anomaly detection engine"""
    
    def __init__(self):
        self.baseline_stats = defaultdict(list)
        self.anomaly_threshold = 2.0  # Standard deviations
    
    def update_baseline(self, event_type: str, value: float):
        """Update baseline statistics"""
        self.baseline_stats[event_type].append(value)
        
        # Keep only last 1000 data points
        if len(self.baseline_stats[event_type]) > 1000:
            self.baseline_stats[event_type] = self.baseline_stats[event_type][-1000:]
    
    def detect_anomaly(self, event_type: str, value: float) -> bool:
        """Detect if value is anomalous"""
        if event_type not in self.baseline_stats:
            return False
        
        values = self.baseline_stats[event_type]
        if len(values) < 10:  # Need baseline data
            return False
        
        mean = sum(values) / len(values)
        variance = sum((x - mean) ** 2 for x in values) / len(values)
        std_dev = variance ** 0.5
        
        if std_dev == 0:
            return False
        
        z_score = abs(value - mean) / std_dev
        return z_score > self.anomaly_threshold

class IntrusionDetectionSystem:
    """Main intrusion detection system"""
    
    def __init__(self):
        self.rule_engine = RuleEngine()
        self.anomaly_detector = AnomalyDetector()
        self.events = deque(maxlen=10000)  # Keep last 10,000 events
        self.alerts = []
        self.event_counts = defaultdict(int)
        self.ip_reputation = defaultdict(float)
        self.blocked_ips = set()
        
        # Statistics
        self.stats = {
            'total_events': 0,
            'alerts_generated': 0,
            'blocked_attempts': 0,
            'start_time': datetime.now()
        }
        
        # Alert callbacks
        self.alert_callbacks = []
    
    def add_alert_callback(self, callback: Callable):
        """Add callback for alert notifications"""
        self.alert_callbacks.append(callback)
    
    def process_log_line(self, log_line: str) -> Optional[SecurityEvent]:
        """Process a single log line"""
        self.stats['total_events'] += 1
        
        # Try to match against rules
        match_result = self.rule_engine.match_pattern(log_line)
        
        if match_result:
            rule = match_result['rule']
            match_groups = match_result['match']
            
            # Create security event
            event = SecurityEvent(
                timestamp=datetime.now(),
                event_type=match_result['event_type'],
                severity=match_result['severity'],
                source='log_processor',
                details={
                    'rule_name': rule['name'],
                    'pattern_match': match_groups,
                    'description': rule['description']
                },
                raw_data=log_line
            )
            
            self.events.append(event)
            self.event_counts[event.event_type] += 1
            
            # Check for threshold-based alerts
            self.check_threshold_alert(event)
            
            return event
        
        return None
    
    def check_threshold_alert(self, event: SecurityEvent):
        """Check if event triggers threshold-based alert"""
        rule = event.details['rule_name']
        
        # Find the rule
        for rule_def in self.rule_engine.rules:
            if rule_def['name'] == rule:
                # Count similar events in time window
                time_window = timedelta(seconds=rule_def['time_window'])
                threshold = rule_def['threshold']
                
                recent_events = [
                    e for e in self.events 
                    if e.event_type == event.event_type 
                    and (event.timestamp - e.timestamp) <= time_window
                ]
                
                if len(recent_events) >= threshold:
                    self.generate_alert(
                        alert_type='threshold_breach',
                        severity=event.severity,
                        message=f"Threshold breach: {len(recent_events)} {event.event_type} events in {rule_def['time_window']}s",
                        source=event.source,
                        confidence=0.8,
                        events=recent_events
                )
                
                break
    
    def generate_alert(self, alert_type: str, severity: str, message: str, 
                       source: str, confidence: float, events: List[SecurityEvent]):
        """Generate security alert"""
        alert_id = hashlib.md5(f"{message}{time.time()}".encode()).hexdigest()[:8]
        
        alert = Alert(
            id=alert_id,
            timestamp=datetime.now(),
            alert_type=alert_type,
            severity=severity,
            message=message,
            source=source,
            confidence=confidence,
            events=events[-5:]  # Keep last 5 events
        )
        
        self.alerts.append(alert)
        self.stats['alerts_generated'] += 1
        
        # Call alert callbacks
        for callback in self.alert_callbacks:
            try:
                callback(alert)
            except Exception as e:
                print(f"Alert callback error: {e}")
        
        print(f"ALERT [{alert.severity}]: {alert.message}")
    
    def monitor_network_connection(self, src_ip: str, dst_ip: str, dst_port: int):
        """Monitor network connections"""
        # Check IP reputation
        if self.ip_reputation[src_ip] < 0.5:
            self.generate_alert(
                alert_type='suspicious_ip',
                severity='HIGH',
                message=f"Connection from suspicious IP: {src_ip}",
                source='network_monitor',
                confidence=0.7,
                events=[]
            )
            self.stats['blocked_attempts'] += 1
            return False
        
        # Track connection patterns
        connection_key = f"{src_ip}:{dst_port}"
        self.anomaly_detector.update_baseline('network_connections', 1.0)
        
        return True
    
    def update_ip_reputation(self, ip: str, score: float):
        """Update IP reputation score"""
        current_score = self.ip_reputation[ip]
        self.ip_reputation[ip] = max(0.0, min(1.0, current_score + score * 0.1))
        
        # Block low-reputation IPs
        if self.ip_reputation[ip] < 0.2:
            self.blocked_ips.add(ip)
    
    def get_statistics(self) -> Dict:
        """Get IDS statistics"""
        current_time = datetime.now()
        uptime = current_time - self.stats['start_time']
        
        # Calculate events per minute
        events_per_minute = self.stats['total_events'] / max(uptime.total_seconds() / 60, 1)
        
        # Get top event types
        top_events = sorted(self.event_counts.items(), key=lambda x: x[1], reverse=True)[:5]
        
        return {
            'uptime_seconds': uptime.total_seconds(),
            'total_events': self.stats['total_events'],
            'alerts_generated': self.stats['alerts_generated'],
            'blocked_attempts': self.stats['blocked_attempts'],
            'events_per_minute': events_per_minute,
            'top_event_types': top_events,
            'blocked_ips_count': len(self.blocked_ips),
            'ip_reputation_count': len(self.ip_reputation)
        }
    
    def get_recent_alerts(self, hours: int = 24) -> List[Alert]:
        """Get recent alerts"""
        cutoff_time = datetime.now() - timedelta(hours=hours)
        return [alert for alert in self.alerts if alert.timestamp >= cutoff_time]
    
    def save_state(self, filename: str):
        """Save IDS state to file"""
        state = {
            'statistics': self.get_statistics(),
            'alerts': [alert.to_dict() for alert in self.get_recent_alerts(168)],  # Last week
            'ip_reputation': dict(self.ip_reputation),
            'blocked_ips': list(self.blocked_ips),
            'save_time': datetime.now().isoformat()
        }
        
        with open(filename, 'w') as f:
            json.dump(state, f, indent=2)
        
        print(f"IDS state saved to: {filename}")
    
    def load_state(self, filename: str):
        """Load IDS state from file"""
        try:
            with open(filename, 'r') as f:
                state = json.load(f)
            
            self.ip_reputation = defaultdict(float, state.get('ip_reputation', {}))
            self.blocked_ips = set(state.get('blocked_ips', []))
            
            print(f"IDS state loaded from: {filename}")
            
        except FileNotFoundError:
            print(f"State file not found: {filename}")
        except Exception as e:
            print(f"Error loading state: {e}")

def sample_log_generator():
    """Generate sample log entries for testing"""
    import random
    
    sample_logs = [
        "Failed password for admin from 192.168.1.100 port 22 ssh2",
        "sshd[1234]: Accepted password for user from 10.0.0.1 port 22 ssh2",
        "Connection from 172.16.0.50:12345 to port 80",
        "Process httpd executed by user root with PID 5678",
        "Access to /etc/passwd by user guest",
        "Transfer of 1048576 bytes from 192.168.1.1 to 10.0.0.2",
        "Failed password for root from 192.168.1.101 port 22 ssh2",
        "Failed password for admin from 192.168.1.100 port 22 ssh2",
        "sshd[1235]: Accepted password for user from 10.0.0.2 port 22 ssh2",
        "Connection from 172.16.0.51:12346 to port 443"
    ]
    
    while True:
        yield random.choice(sample_logs)
        time.sleep(random.uniform(0.1, 1.0))

def console_alert_callback(alert: Alert):
    """Console alert callback"""
    timestamp = alert.timestamp.strftime("%Y-%m-%d %H:%M:%S")
    print(f"[{timestamp}] {alert.severity} ALERT: {alert.message}")

def main():
    """Main function to demonstrate IDS"""
    print("=== Simple Intrusion Detection System ===\n")
    
    # Initialize IDS
    ids = IntrusionDetectionSystem()
    
    # Add alert callback
    ids.add_alert_callback(console_alert_callback)
    
    print("IDS initialized. Starting monitoring...")
    print("Processing sample log entries...\n")
    
    # Process sample logs
    log_generator = sample_log_generator()
    
    try:
        for i, log_line in enumerate(log_generator):
            event = ids.process_log_line(log_line)
            
            if event:
                # Update IP reputation based on events
                if event.event_type == 'authentication_failure':
                    if 'pattern_match' in event.details:
                        ip = event.details['pattern_match'][0]
                        ids.update_ip_reputation(ip, -0.2)
                elif event.event_type == 'ssh_connection':
                    if 'pattern_match' in event.details:
                        ip = event.details['pattern_match'][0]
                        ids.update_ip_reputation(ip, 0.1)
            
            # Show statistics every 10 events
            if (i + 1) % 10 == 0:
                stats = ids.get_statistics()
                print(f"\n--- Statistics (Event {i+1}) ---")
                print(f"Total Events: {stats['total_events']}")
                print(f"Alerts Generated: {stats['alerts_generated']}")
                print(f"Blocked Attempts: {stats['blocked_attempts']}")
                print(f"Events/Minute: {stats['events_per_minute']:.2f}")
                
                if stats['top_event_types']:
                    print("Top Event Types:")
                    for event_type, count in stats['top_event_types']:
                        print(f"  {event_type}: {count}")
                
                print(f"Blocked IPs: {stats['blocked_ips_count']}")
                print()
            
            # Stop after 50 events for demo
            if i >= 49:
                break
    
    except KeyboardInterrupt:
        print("\nMonitoring stopped by user")
    
    # Show final statistics
    print("\n=== Final Statistics ===")
    final_stats = ids.get_statistics()
    
    for key, value in final_stats.items():
        if key != 'top_event_types':
            print(f"{key}: {value}")
    
    print("\nTop Event Types:")
    for event_type, count in final_stats['top_event_types']:
        print(f"  {event_type}: {count}")
    
    # Show recent alerts
    print("\n=== Recent Alerts ===")
    recent_alerts = ids.get_recent_alerts(1)  # Last hour
    
    if recent_alerts:
        for alert in recent_alerts[-5:]:  # Show last 5 alerts
            timestamp = alert.timestamp.strftime("%Y-%m-%d %H:%M:%S")
            print(f"[{timestamp}] {alert.severity}: {alert.message}")
    else:
        print("No recent alerts")
    
    # Save state
    ids.save_state('ids_state.json')
    
    # Demonstrate IP reputation
    print("\n=== IP Reputation ===")
    print("Top 10 IP Reputation Scores:")
    sorted_ips = sorted(ids.ip_reputation.items(), key=lambda x: x[1], reverse=True)[:10]
    for ip, score in sorted_ips:
        status = "BLOCKED" if ip in ids.blocked_ips else "ALLOWED"
        print(f"  {ip}: {score:.2f} ({status})")
    
    print("\n=== IDS Demo Completed ===")
    print("Features demonstrated:")
    print("- Rule-based event detection")
    print("- Threshold-based alerting")
    print("- IP reputation tracking")
    print("- Real-time monitoring")
    print("- Statistical analysis")
    print("- State persistence")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Run IDS: python simple_ids.py
2. Processes sample log entries in real-time
3. Generates alerts for suspicious activities
4. Tracks IP reputation and blocks malicious IPs
5. Provides statistics and reporting

Key Concepts:
- Rule-Based Detection: Pattern matching against security rules
- Threshold Alerting: Event frequency analysis
- IP Reputation: Trust scoring for network sources
- Real-time Monitoring: Continuous log processing
- Anomaly Detection: Statistical deviation analysis

Detection Capabilities:
- Brute force attacks
- Port scanning
- Suspicious process execution
- Sensitive file access
- Network anomalies
- Authentication failures

Alert Types:
- LOW: Informational events
- MEDIUM: Suspicious activities
- HIGH: Potential attacks
- CRITICAL: Active threats

Applications:
- Network security monitoring
- Host-based intrusion detection
- Log analysis
- Security operations center
- Incident response
- Compliance monitoring

Extensions:
- Add custom detection rules
- Integrate with SIEM systems
- Machine learning-based detection
- Automated response actions
- External threat intelligence
- Network traffic analysis
"""
