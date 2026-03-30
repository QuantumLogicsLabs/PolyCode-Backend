"""
System Metrics Collector
========================

Comprehensive system monitoring and metrics collection tool.
Demonstrates performance monitoring, resource tracking, and alerting.
"""

import psutil
import time
import json
import sqlite3
import threading
from typing import Dict, List, Optional, Callable
from datetime import datetime, timedelta
from dataclasses import dataclass, asdict
import matplotlib.pyplot as plt
import numpy as np
from collections import deque
import logging

@dataclass
class SystemMetric:
    """System metric data structure"""
    timestamp: datetime
    metric_type: str
    value: float
    unit: str
    source: str
    tags: Dict[str, str]

@dataclass
class Alert:
    """Alert data structure"""
    id: str
    timestamp: datetime
    alert_type: str
    severity: str
    message: str
    threshold: float
    current_value: float
    resolved: bool = False
    resolved_at: Optional[datetime] = None

class MetricsCollector:
    """System metrics collection and monitoring"""
    
    def __init__(self, collection_interval: int = 5, retention_days: int = 7):
        self.collection_interval = collection_interval
        self.retention_days = retention_days
        self.metrics = deque(maxlen=10000)  # Keep last 10k metrics
        self.alerts = []
        self.alert_callbacks = []
        self.is_collecting = False
        self.collection_thread = None
        
        # Database setup
        self.db_path = "metrics.db"
        self._setup_database()
        
        # Thresholds for alerts
        self.thresholds = {
            'cpu_percent': {'warning': 70.0, 'critical': 90.0},
            'memory_percent': {'warning': 75.0, 'critical': 90.0},
            'disk_percent': {'warning': 80.0, 'critical': 95.0},
            'network_error_rate': {'warning': 5.0, 'critical': 15.0},
            'response_time': {'warning': 1000.0, 'critical': 5000.0}
        }
        
        # Setup logging
        logging.basicConfig(level=logging.INFO)
        self.logger = logging.getLogger(__name__)
    
    def _setup_database(self):
        """Setup SQLite database for metrics storage"""
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS metrics (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                timestamp TEXT NOT NULL,
                metric_type TEXT NOT NULL,
                value REAL NOT NULL,
                unit TEXT NOT NULL,
                source TEXT NOT NULL,
                tags TEXT
            )
        ''')
        
        cursor.execute('''
            CREATE TABLE IF NOT EXISTS alerts (
                id TEXT PRIMARY KEY,
                timestamp TEXT NOT NULL,
                alert_type TEXT NOT NULL,
                severity TEXT NOT NULL,
                message TEXT NOT NULL,
                threshold REAL NOT NULL,
                current_value REAL NOT NULL,
                resolved INTEGER DEFAULT 0,
                resolved_at TEXT
            )
        ''')
        
        conn.commit()
        conn.close()
    
    def add_alert_callback(self, callback: Callable):
        """Add callback for alert notifications"""
        self.alert_callbacks.append(callback)
    
    def collect_system_metrics(self) -> Dict[str, float]:
        """Collect basic system metrics"""
        metrics = {}
        
        try:
            # CPU metrics
            cpu_percent = psutil.cpu_percent(interval=1)
            metrics['cpu_percent'] = cpu_percent
            
            # CPU count and load
            metrics['cpu_count'] = psutil.cpu_count()
            load_avg = psutil.getloadavg()
            metrics['load_1min'] = load_avg[0]
            metrics['load_5min'] = load_avg[1]
            metrics['load_15min'] = load_avg[2]
            
            # Memory metrics
            memory = psutil.virtual_memory()
            metrics['memory_percent'] = memory.percent
            metrics['memory_used'] = memory.used
            metrics['memory_available'] = memory.available
            metrics['memory_total'] = memory.total
            
            # Disk metrics
            disk = psutil.disk_usage('/')
            metrics['disk_percent'] = (disk.used / disk.total) * 100
            metrics['disk_used'] = disk.used
            metrics['disk_free'] = disk.free
            metrics['disk_total'] = disk.total
            
            # Network metrics
            network = psutil.net_io_counters()
            metrics['network_bytes_sent'] = network.bytes_sent
            metrics['network_bytes_recv'] = network.bytes_recv
            metrics['network_packets_sent'] = network.packets_sent
            metrics['network_packets_recv'] = network.packets_recv
            
            # Process metrics
            metrics['process_count'] = len(psutil.pids())
            
            # System uptime
            metrics['uptime'] = time.time() - psutil.boot_time()
            
        except Exception as e:
            self.logger.error(f"Error collecting system metrics: {e}")
        
        return metrics
    
    def collect_process_metrics(self, pid: int = None) -> Dict[str, Dict]:
        """Collect metrics for specific processes"""
        process_metrics = {}
        
        try:
            if pid:
                pids = [pid]
            else:
                # Get top 10 processes by CPU usage
                pids = sorted(psutil.pids(), 
                            key=lambda x: psutil.Process(x).cpu_percent() if psutil.pid_exists(x) else 0,
                            reverse=True)[:10]
            
            for proc_pid in pids:
                if not psutil.pid_exists(proc_pid):
                    continue
                
                try:
                    process = psutil.Process(proc_pid)
                    
                    metrics = {
                        'pid': proc_pid,
                        'name': process.name(),
                        'cpu_percent': process.cpu_percent(),
                        'memory_percent': process.memory_percent(),
                        'memory_rss': process.memory_info().rss,
                        'memory_vms': process.memory_info().vms,
                        'num_threads': process.num_threads(),
                        'create_time': process.create_time(),
                        'status': process.status()
                    }
                    
                    process_metrics[proc_pid] = metrics
                    
                except (psutil.NoSuchProcess, psutil.AccessDenied):
                    continue
        
        except Exception as e:
            self.logger.error(f"Error collecting process metrics: {e}")
        
        return process_metrics
    
    def collect_network_metrics(self) -> Dict[str, Dict]:
        """Collect detailed network metrics"""
        network_metrics = {}
        
        try:
            # Network interface statistics
            net_io = psutil.net_io_counters(pernic=True)
            
            for interface, stats in net_io.items():
                network_metrics[interface] = {
                    'bytes_sent': stats.bytes_sent,
                    'bytes_recv': stats.bytes_recv,
                    'packets_sent': stats.packets_sent,
                    'packets_recv': stats.packets_recv,
                    'errin': stats.errin,
                    'errout': stats.errout,
                    'dropin': stats.dropin,
                    'dropout': stats.dropout
                }
            
            # Network connections
            connections = psutil.net_connections()
            connection_stats = {
                'total': len(connections),
                'established': len([c for c in connections if c.status == 'ESTABLISHED']),
                'listen': len([c for c in connections if c.status == 'LISTEN']),
                'time_wait': len([c for c in connections if c.status == 'TIME_WAIT'])
            }
            
            network_metrics['connections'] = connection_stats
            
        except Exception as e:
            self.logger.error(f"Error collecting network metrics: {e}")
        
        return network_metrics
    
    def collect_disk_metrics(self) -> Dict[str, Dict]:
        """Collect detailed disk metrics"""
        disk_metrics = {}
        
        try:
            # Disk usage for all mount points
            disk_usage = psutil.disk_usage('/')
            disk_metrics['root'] = {
                'total': disk_usage.total,
                'used': disk_usage.used,
                'free': disk_usage.free,
                'percent': (disk_usage.used / disk_usage.total) * 100
            }
            
            # Disk I/O statistics
            disk_io = psutil.disk_io_counters()
            disk_metrics['io'] = {
                'read_count': disk_io.read_count,
                'write_count': disk_io.write_count,
                'read_bytes': disk_io.read_bytes,
                'write_bytes': disk_io.write_bytes,
                'read_time': disk_io.read_time,
                'write_time': disk_io.write_time
            }
            
            # Disk partitions
            partitions = psutil.disk_partitions()
            partition_info = []
            
            for partition in partitions:
                try:
                    usage = psutil.disk_usage(partition.mountpoint)
                    partition_info.append({
                        'device': partition.device,
                        'mountpoint': partition.mountpoint,
                        'fstype': partition.fstype,
                        'total': usage.total,
                        'used': usage.used,
                        'free': usage.free,
                        'percent': (usage.used / usage.total) * 100
                    })
                except PermissionError:
                    continue
            
            disk_metrics['partitions'] = partition_info
            
        except Exception as e:
            self.logger.error(f"Error collecting disk metrics: {e}")
        
        return disk_metrics
    
    def store_metric(self, metric_type: str, value: float, unit: str, 
                     source: str = "system", tags: Dict[str, str] = None):
        """Store a metric"""
        metric = SystemMetric(
            timestamp=datetime.now(),
            metric_type=metric_type,
            value=value,
            unit=unit,
            source=source,
            tags=tags or {}
        )
        
        self.metrics.append(metric)
        
        # Store in database
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        cursor.execute('''
            INSERT INTO metrics (timestamp, metric_type, value, unit, source, tags)
            VALUES (?, ?, ?, ?, ?, ?)
        ''', (
            metric.timestamp.isoformat(),
            metric.metric_type,
            metric.value,
            metric.unit,
            metric.source,
            json.dumps(metric.tags)
        ))
        
        conn.commit()
        conn.close()
        
        # Check for alerts
        self._check_thresholds(metric)
    
    def _check_thresholds(self, metric: SystemMetric):
        """Check if metric exceeds thresholds and generate alerts"""
        if metric.metric_type in self.thresholds:
            thresholds = self.thresholds[metric.metric_type]
            
            if metric.value >= thresholds['critical']:
                self._create_alert(
                    alert_type=metric.metric_type,
                    severity='critical',
                    message=f"Critical threshold exceeded for {metric.metric_type}",
                    threshold=thresholds['critical'],
                    current_value=metric.value
                )
            elif metric.value >= thresholds['warning']:
                self._create_alert(
                    alert_type=metric.metric_type,
                    severity='warning',
                    message=f"Warning threshold exceeded for {metric.metric_type}",
                    threshold=threshold['warning'],
                    current_value=metric.value
                )
    
    def _create_alert(self, alert_type: str, severity: str, message: str,
                      threshold: float, current_value: float):
        """Create and store an alert"""
        alert_id = f"{alert_type}_{int(time.time())}"
        
        alert = Alert(
            id=alert_id,
            timestamp=datetime.now(),
            alert_type=alert_type,
            severity=severity,
            message=message,
            threshold=threshold,
            current_value=current_value
        )
        
        self.alerts.append(alert)
        
        # Store in database
        conn = sqlite3.connect(self.db_path)
        cursor = conn.cursor()
        
        cursor.execute('''
            INSERT INTO alerts (id, timestamp, alert_type, severity, message, threshold, current_value)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ''', (
            alert.id,
            alert.timestamp.isoformat(),
            alert.alert_type,
            alert.severity,
            alert.message,
            alert.threshold,
            alert.current_value
        ))
        
        conn.commit()
        conn.close()
        
        # Call alert callbacks
        for callback in self.alert_callbacks:
            try:
                callback(alert)
            except Exception as e:
                self.logger.error(f"Alert callback error: {e}")
        
        self.logger.warning(f"ALERT [{severity.upper()}]: {message}")
    
    def start_collection(self):
        """Start continuous metrics collection"""
        if self.is_collecting:
            return
        
        self.is_collecting = True
        self.collection_thread = threading.Thread(target=self._collection_loop, daemon=True)
        self.collection_thread.start()
        
        self.logger.info("Metrics collection started")
    
    def stop_collection(self):
        """Stop metrics collection"""
        self.is_collecting = False
        if self.collection_thread:
            self.collection_thread.join()
        
        self.logger.info("Metrics collection stopped")
    
    def _collection_loop(self):
        """Main collection loop"""
        while self.is_collecting:
            try:
                # Collect system metrics
                system_metrics = self.collect_system_metrics()
                
                for metric_type, value in system_metrics.items():
                    unit = self._get_metric_unit(metric_type)
                    self.store_metric(metric_type, value, unit)
                
                # Collect network metrics
                network_metrics = self.collect_network_metrics()
                
                for interface, stats in network_metrics.items():
                    if interface != 'connections':
                        for stat_type, value in stats.items():
                            metric_name = f"network_{interface}_{stat_type}"
                            unit = self._get_metric_unit(stat_type)
                            self.store_metric(metric_name, value, unit, 
                                            source="network", tags={"interface": interface})
                
                # Collect disk metrics
                disk_metrics = self.collect_disk_metrics()
                
                for disk_type, stats in disk_metrics.items():
                    if disk_type == 'partitions':
                        for i, partition in enumerate(stats):
                            for stat_type, value in partition.items():
                                if isinstance(value, (int, float)):
                                    metric_name = f"disk_partition_{i}_{stat_type}"
                                    unit = self._get_metric_unit(stat_type)
                                    self.store_metric(metric_name, value, unit,
                                                    source="disk", tags={"partition": partition['mountpoint']})
                    else:
                        for stat_type, value in stats.items():
                            if isinstance(value, (int, float)):
                                metric_name = f"disk_{disk_type}_{stat_type}"
                                unit = self._get_metric_unit(stat_type)
                                self.store_metric(metric_name, value, unit, source="disk")
                
                time.sleep(self.collection_interval)
                
            except Exception as e:
                self.logger.error(f"Error in collection loop: {e}")
                time.sleep(self.collection_interval)
    
    def _get_metric_unit(self, metric_type: str) -> str:
        """Get unit for metric type"""
        unit_map = {
            'cpu_percent': '%',
            'memory_percent': '%',
            'disk_percent': '%',
            'memory_used': 'bytes',
            'memory_available': 'bytes',
            'memory_total': 'bytes',
            'disk_used': 'bytes',
            'disk_free': 'bytes',
            'disk_total': 'bytes',
            'network_bytes_sent': 'bytes',
            'network_bytes_recv': 'bytes',
            'network_packets_sent': 'packets',
            'network_packets_recv': 'packets',
            'uptime': 'seconds',
            'process_count': 'count',
            'cpu_count': 'count',
            'load_1min': 'load',
            'load_5min': 'load',
            'load_15min': 'load',
            'bytes_sent': 'bytes',
            'bytes_recv': 'bytes',
            'packets_sent': 'packets',
            'packets_recv': 'packets',
            'read_count': 'count',
            'write_count': 'count',
            'read_bytes': 'bytes',
            'write_bytes': 'bytes',
            'read_time': 'ms',
            'write_time': 'ms',
            'total': 'bytes',
            'used': 'bytes',
            'free': 'bytes'
        }
        
        return unit_map.get(metric_type, 'unknown')
    
    def get_metrics_history(self, metric_type: str, hours: int = 1) -> List[SystemMetric]:
        """Get historical metrics for a specific type"""
        cutoff_time = datetime.now() - timedelta(hours=hours)
        
        # Get from memory
        memory_metrics = [
            m for m in self.metrics 
            if m.metric_type == metric_type and m.timestamp >= cutoff_time
        ]
        
        # Get from database if needed
        if len(memory_metrics) < 100:
            conn = sqlite3.connect(self.db_path)
            cursor = conn.cursor()
            
            cursor.execute('''
                SELECT timestamp, metric_type, value, unit, source, tags
                FROM metrics
                WHERE metric_type = ? AND timestamp >= ?
                ORDER BY timestamp
            ''', (metric_type, cutoff_time.isoformat()))
            
            rows = cursor.fetchall()
            conn.close()
            
            for row in rows:
                metric = SystemMetric(
                    timestamp=datetime.fromisoformat(row[0]),
                    metric_type=row[1],
                    value=row[2],
                    unit=row[3],
                    source=row[4],
                    tags=json.loads(row[5])
                )
                memory_metrics.append(metric)
        
        return sorted(memory_metrics, key=lambda x: x.timestamp)
    
    def get_active_alerts(self) -> List[Alert]:
        """Get active (unresolved) alerts"""
        return [alert for alert in self.alerts if not alert.resolved]
    
    def resolve_alert(self, alert_id: str):
        """Resolve an alert"""
        for alert in self.alerts:
            if alert.id == alert_id:
                alert.resolved = True
                alert.resolved_at = datetime.now()
                
                # Update database
                conn = sqlite3.connect(self.db_path)
                cursor = conn.cursor()
                
                cursor.execute('''
                    UPDATE alerts SET resolved = 1, resolved_at = ?
                    WHERE id = ?
                ''', (alert.resolved_at.isoformat(), alert_id))
                
                conn.commit()
                conn.close()
                
                self.logger.info(f"Alert resolved: {alert_id}")
                break
    
    def generate_report(self, hours: int = 24) -> str:
        """Generate monitoring report"""
        cutoff_time = datetime.now() - timedelta(hours=hours)
        
        # Get recent metrics
        recent_metrics = [m for m in self.metrics if m.timestamp >= cutoff_time]
        
        # Get recent alerts
        recent_alerts = [a for a in self.alerts if a.timestamp >= cutoff_time]
        
        # Calculate statistics
        cpu_metrics = [m for m in recent_metrics if m.metric_type == 'cpu_percent']
        memory_metrics = [m for m in recent_metrics if m.metric_type == 'memory_percent']
        disk_metrics = [m for m in recent_metrics if m.metric_type == 'disk_percent']
        
        report = []
        report.append("=" * 60)
        report.append("SYSTEM MONITORING REPORT")
        report.append("=" * 60)
        report.append(f"Report Period: Last {hours} hours")
        report.append(f"Generated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        report.append("")
        
        # Summary statistics
        report.append("SUMMARY STATISTICS:")
        report.append("-" * 40)
        report.append(f"Total Metrics Collected: {len(recent_metrics)}")
        report.append(f"Total Alerts Generated: {len(recent_alerts)}")
        report.append(f"Active Alerts: {len(self.get_active_alerts())}")
        report.append("")
        
        # CPU statistics
        if cpu_metrics:
            cpu_values = [m.value for m in cpu_metrics]
            report.append("CPU USAGE:")
            report.append("-" * 40)
            report.append(f"  Average: {np.mean(cpu_values):.2f}%")
            report.append(f"  Maximum: {np.max(cpu_values):.2f}%")
            report.append(f"  Minimum: {np.min(cpu_values):.2f}%")
            report.append(f"  Current: {cpu_values[-1]:.2f}%")
            report.append("")
        
        # Memory statistics
        if memory_metrics:
            memory_values = [m.value for m in memory_metrics]
            report.append("MEMORY USAGE:")
            report.append("-" * 40)
            report.append(f"  Average: {np.mean(memory_values):.2f}%")
            report.append(f"  Maximum: {np.max(memory_values):.2f}%")
            report.append(f"  Minimum: {np.min(memory_values):.2f}%")
            report.append(f"  Current: {memory_values[-1]:.2f}%")
            report.append("")
        
        # Disk statistics
        if disk_metrics:
            disk_values = [m.value for m in disk_metrics]
            report.append("DISK USAGE:")
            report.append("-" * 40)
            report.append(f"  Average: {np.mean(disk_values):.2f}%")
            report.append(f"  Maximum: {np.max(disk_values):.2f}%")
            report.append(f"  Minimum: {np.min(disk_values):.2f}%")
            report.append(f"  Current: {disk_values[-1]:.2f}%")
            report.append("")
        
        # Recent alerts
        if recent_alerts:
            report.append("RECENT ALERTS:")
            report.append("-" * 40)
            
            for alert in recent_alerts[-10:]:  # Show last 10 alerts
                status = "RESOLVED" if alert.resolved else "ACTIVE"
                report.append(f"  [{alert.severity.upper()}] {alert.alert_type}")
                report.append(f"    Message: {alert.message}")
                report.append(f"    Value: {alert.current_value:.2f} (Threshold: {alert.threshold})")
                report.append(f"    Status: {status}")
                report.append("")
        
        return "\n".join(report)
    
    def plot_metrics(self, metric_type: str, hours: int = 1, save_path: str = None):
        """Plot metrics over time"""
        metrics = self.get_metrics_history(metric_type, hours)
        
        if not metrics:
            print(f"No data available for metric: {metric_type}")
            return
        
        timestamps = [m.timestamp for m in metrics]
        values = [m.value for m in metrics]
        
        plt.figure(figsize=(12, 6))
        plt.plot(timestamps, values, marker='o', linestyle='-', markersize=2)
        
        # Add threshold lines if available
        if metric_type in self.thresholds:
            thresholds = self.thresholds[metric_type]
            plt.axhline(y=thresholds['warning'], color='orange', linestyle='--', 
                       label=f'Warning ({thresholds["warning"]}%)')
            plt.axhline(y=thresholds['critical'], color='red', linestyle='--', 
                       label=f'Critical ({thresholds["critical"]}%)')
            plt.legend()
        
        plt.title(f'{metric_type.replace("_", " ").title()} - Last {hours} Hours')
        plt.xlabel('Time')
        plt.ylabel(metric_type.replace("_", " ").title())
        plt.grid(True, alpha=0.3)
        plt.xticks(rotation=45)
        plt.tight_layout()
        
        if save_path:
            plt.savefig(save_path)
            print(f"Plot saved to: {save_path}")
        else:
            plt.show()
    
    def export_metrics(self, filename: str, hours: int = 24):
        """Export metrics to JSON file"""
        cutoff_time = datetime.now() - timedelta(hours=hours)
        recent_metrics = [m for m in self.metrics if m.timestamp >= cutoff_time]
        
        export_data = {
            'export_time': datetime.now().isoformat(),
            'period_hours': hours,
            'metrics': [
                {
                    'timestamp': m.timestamp.isoformat(),
                    'metric_type': m.metric_type,
                    'value': m.value,
                    'unit': m.unit,
                    'source': m.source,
                    'tags': m.tags
                }
                for m in recent_metrics
            ]
        }
        
        with open(filename, 'w') as f:
            json.dump(export_data, f, indent=2)
        
        print(f"Metrics exported to: {filename}")

def console_alert_callback(alert: Alert):
    """Console alert callback"""
    timestamp = alert.timestamp.strftime("%Y-%m-%d %H:%M:%S")
    status = "RESOLVED" if alert.resolved else "ACTIVE"
    print(f"[{timestamp}] {alert.severity.upper()} ALERT: {alert.message}")

def main():
    """Main function to demonstrate metrics collector"""
    print("=== System Metrics Collector ===\n")
    
    collector = MetricsCollector(collection_interval=5)
    
    # Add alert callback
    collector.add_alert_callback(console_alert_callback)
    
    print("Starting metrics collection...")
    collector.start_collection()
    
    # Collect and display current metrics
    print("\n1. Current System Metrics:")
    system_metrics = collector.collect_system_metrics()
    
    for metric_type, value in system_metrics.items():
        print(f"  {metric_type}: {value}")
    
    # Collect process metrics
    print("\n2. Top 5 Processes by CPU Usage:")
    process_metrics = collector.collect_process_metrics()
    
    for i, (pid, metrics) in enumerate(list(process_metrics.items())[:5]):
        print(f"  {i+1}. {metrics['name']} (PID: {pid})")
        print(f"     CPU: {metrics['cpu_percent']:.1f}%, Memory: {metrics['memory_percent']:.1f}%")
    
    # Collect network metrics
    print("\n3. Network Metrics:")
    network_metrics = collector.collect_network_metrics()
    
    if 'connections' in network_metrics:
        conn_stats = network_metrics['connections']
        print(f"  Total Connections: {conn_stats['total']}")
        print(f"  Established: {conn_stats['established']}")
        print(f"  Listening: {conn_stats['listen']}")
    
    # Collect disk metrics
    print("\n4. Disk Metrics:")
    disk_metrics = collector.collect_disk_metrics()
    
    if 'root' in disk_metrics:
        root_disk = disk_metrics['root']
        print(f"  Root Disk Usage: {root_disk['percent']:.1f}%")
        print(f"  Used: {root_disk['used'] / (1024**3):.2f} GB")
        print(f"  Free: {root_disk['free'] / (1024**3):.2f} GB")
    
    # Wait for some metrics to be collected
    print("\n5. Collecting metrics for 30 seconds...")
    time.sleep(30)
    
    # Generate report
    print("\n6. Monitoring Report:")
    report = collector.generate_report(hours=1)
    print(report)
    
    # Plot metrics
    print("\n7. Generating metric plots...")
    
    try:
        collector.plot_metrics('cpu_percent', hours=1, save_path='cpu_usage.png')
        collector.plot_metrics('memory_percent', hours=1, save_path='memory_usage.png')
        collector.plot_metrics('disk_percent', hours=1, save_path='disk_usage.png')
    except Exception as e:
        print(f"Error generating plots: {e}")
    
    # Export metrics
    print("\n8. Exporting metrics...")
    collector.export_metrics('metrics_export.json', hours=1)
    
    # Show active alerts
    print("\n9. Active Alerts:")
    active_alerts = collector.get_active_alerts()
    
    if active_alerts:
        for alert in active_alerts:
            print(f"  {alert.severity.upper()}: {alert.message}")
    else:
        print("  No active alerts")
    
    # Interactive monitoring
    print("\n=== Interactive Monitoring ===")
    
    try:
        while True:
            print("\nOptions:")
            print("1. Show current metrics")
            print("2. Show active alerts")
            print("3. Generate report")
            print("4. Plot metric")
            print("5. Export metrics")
            print("6. Resolve alert")
            print("0. Stop monitoring")
            
            choice = input("\nSelect option: ").strip()
            
            if choice == "0":
                break
            
            elif choice == "1":
                metrics = collector.collect_system_metrics()
                print("\nCurrent Metrics:")
                for metric_type, value in metrics.items():
                    print(f"  {metric_type}: {value}")
            
            elif choice == "2":
                alerts = collector.get_active_alerts()
                print(f"\nActive Alerts ({len(alerts)}):")
                for alert in alerts:
                    print(f"  {alert.severity.upper()}: {alert.message}")
            
            elif choice == "3":
                hours = int(input("Enter report period (hours): ") or "24")
                report = collector.generate_report(hours)
                print("\n" + report)
            
            elif choice == "4":
                metric_type = input("Enter metric type: ").strip()
                hours = int(input("Enter time period (hours): ") or "1")
                collector.plot_metrics(metric_type, hours)
            
            elif choice == "5":
                hours = int(input("Enter export period (hours): ") or "24")
                filename = input("Enter filename (default: metrics.json): ").strip() or "metrics.json"
                collector.export_metrics(filename, hours)
            
            elif choice == "6":
                alerts = collector.get_active_alerts()
                if alerts:
                    print("\nActive Alerts:")
                    for i, alert in enumerate(alerts):
                        print(f"  {i+1}. {alert.severity.upper()}: {alert.message}")
                    
                    try:
                        alert_idx = int(input("Enter alert number to resolve: ")) - 1
                        if 0 <= alert_idx < len(alerts):
                            collector.resolve_alert(alerts[alert_idx].id)
                            print("Alert resolved")
                        else:
                            print("Invalid alert number")
                    except ValueError:
                        print("Invalid input")
                else:
                    print("No active alerts to resolve")
            
            else:
                print("Invalid option")
    
    except KeyboardInterrupt:
        print("\nMonitoring stopped by user")
    
    finally:
        # Stop collection
        collector.stop_collection()
        print("\nMetrics collection stopped")
    
    print("\n=== Metrics Collector Demo Completed ===")
    print("Features demonstrated:")
    print("- System metrics collection (CPU, memory, disk, network)")
    print("- Process monitoring")
    print("- Alert generation and management")
    print("- Historical data storage")
    print("- Report generation")
    print("- Data visualization")
    print("- Metrics export")
    print("- Interactive monitoring")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Install dependencies: pip install psutil matplotlib numpy
2. Run collector: python metrics_collector.py
3. Monitor system metrics in real-time
4. View alerts and generate reports

Key Concepts:
- System Metrics: CPU, memory, disk, network usage
- Process Monitoring: Individual process resource usage
- Alert Thresholds: Warning and critical levels
- Historical Data: Time-series metric storage
- Alert Management: Create, resolve, and track alerts

Monitoring Capabilities:
- CPU usage and load averages
- Memory usage and availability
- Disk space and I/O statistics
- Network traffic and connections
- Process resource consumption
- System uptime and health

Alert Types:
- CPU usage alerts
- Memory usage alerts
- Disk space alerts
- Network error alerts
- Response time alerts

Applications:
- System performance monitoring
- Resource capacity planning
- Anomaly detection
- Health checking
- Performance optimization
- Incident response

Dependencies:
- psutil: pip install psutil
- matplotlib: pip install matplotlib
- numpy: pip install numpy
- sqlite3: Built-in Python

Best Practices:
- Regular monitoring intervals
- Appropriate threshold settings
- Historical data retention
- Alert notification systems
- Performance trend analysis
- Capacity planning
- Automated responses
"""
