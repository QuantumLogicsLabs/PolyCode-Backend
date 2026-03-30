"""
Multi-Sensor Data Reader
=========================

Comprehensive sensor data acquisition and processing system.
Demonstrates sensor integration, data filtering, and real-time monitoring.
"""

import time
import threading
import json
import logging
from typing import Dict, List, Optional, Callable, Any
from dataclasses import dataclass, asdict
from enum import Enum
from datetime import datetime
import random
import math

try:
    import serial
    SERIAL_AVAILABLE = True
except ImportError:
    print("Warning: pyserial not available. Serial communication will be simulated.")
    SERIAL_AVAILABLE = False

try:
    import numpy as np
    NUMPY_AVAILABLE = True
except ImportError:
    print("Warning: numpy not available. Data processing will be limited.")
    NUMPY_AVAILABLE = False

class SensorType(Enum):
    """Sensor type enumeration"""
    TEMPERATURE = "temperature"
    HUMIDITY = "humidity"
    PRESSURE = "pressure"
    ACCELEROMETER = "accelerometer"
    GYROSCOPE = "gyroscope"
    MAGNETOMETER = "magnetometer"
    ULTRASONIC = "ultrasonic"
    IR = "infrared"
    LIDAR = "lidar"
    CAMERA = "camera"
    GPS = "gps"
    ENCODER = "encoder"

class SensorStatus(Enum):
    """Sensor status enumeration"""
    ACTIVE = "active"
    INACTIVE = "inactive"
    ERROR = "error"
    CALIBRATING = "calibrating"

@dataclass
class SensorReading:
    """Sensor reading data structure"""
    sensor_id: str
    sensor_type: SensorType
    timestamp: datetime
    value: float
    unit: str
    raw_value: Optional[float] = None
    calibration_offset: float = 0.0
    quality_score: float = 1.0
    metadata: Dict[str, Any] = None

@dataclass
class SensorConfig:
    """Sensor configuration"""
    sensor_id: str
    sensor_type: SensorType
    name: str
    port: Optional[str] = None
    baud_rate: Optional[int] = None
    data_rate: float = 1.0  # Hz
    calibration_offset: float = 0.0
    calibration_scale: float = 1.0
    min_value: Optional[float] = None
    max_value: Optional[float] = None
    filter_enabled: bool = True
    filter_alpha: float = 0.2  # Exponential filter alpha
    metadata: Dict[str, Any] = None

class Sensor:
    """Individual sensor interface"""
    
    def __init__(self, config: SensorConfig):
        self.config = config
        self.status = SensorStatus.INACTIVE
        self.last_reading = None
        self.filtered_value = None
        self.calibration_data = []
        self.error_count = 0
        self.reading_count = 0
        
        # Exponential moving average filter
        self.ema_alpha = config.filter_alpha
        self.ema_value = None
        
        # Serial connection (if applicable)
        self.serial_conn = None
        
        self.logger = logging.getLogger(f"sensor_{config.sensor_id}")
    
    def connect(self) -> bool:
        """Connect to sensor"""
        try:
            if self.config.port and SERIAL_AVAILABLE:
                # Try to establish serial connection
                self.serial_conn = serial.Serial(
                    port=self.config.port,
                    baudrate=self.config.baud_rate or 9600,
                    timeout=1.0
                )
                
                if self.serial_conn.is_open:
                    self.status = SensorStatus.ACTIVE
                    self.logger.info(f"Connected to {self.config.sensor_id} on {self.config.port}")
                    return True
            
            # Simulated connection for demo
            self.status = SensorStatus.ACTIVE
            self.logger.info(f"Simulated connection for {self.config.sensor_id}")
            return True
            
        except Exception as e:
            self.logger.error(f"Error connecting to {self.config.sensor_id}: {e}")
            self.status = SensorStatus.ERROR
            return False
    
    def disconnect(self):
        """Disconnect from sensor"""
        try:
            if self.serial_conn and self.serial_conn.is_open:
                self.serial_conn.close()
                self.serial_conn = None
            
            self.status = SensorStatus.INACTIVE
            self.logger.info(f"Disconnected from {self.config.sensor_id}")
            
        except Exception as e:
            self.logger.error(f"Error disconnecting from {self.config.sensor_id}: {e}")
    
    def read_raw_data(self) -> Optional[float]:
        """Read raw data from sensor"""
        try:
            if self.serial_conn and self.serial_conn.is_open:
                # Read from serial port
                if self.serial_conn.in_waiting > 0:
                    line = self.serial_conn.readline().decode('utf-8').strip()
                    
                    try:
                        # Parse numeric value
                        value = float(line)
                        return value
                    except ValueError:
                        self.logger.warning(f"Invalid data format: {line}")
                        return None
            else:
                # Simulate sensor reading
                return self._simulate_reading()
                
        except Exception as e:
            self.logger.error(f"Error reading from {self.config.sensor_id}: {e}")
            self.error_count += 1
            return None
    
    def _simulate_reading(self) -> float:
        """Simulate sensor reading for demo"""
        base_value = 0.0
        noise = 0.0
        
        # Different base values for different sensor types
        if self.config.sensor_type == SensorType.TEMPERATURE:
            base_value = 25.0  # Room temperature
            noise = random.gauss(0, 2.0)
        elif self.config.sensor_type == SensorType.HUMIDITY:
            base_value = 50.0  # 50% humidity
            noise = random.gauss(0, 5.0)
        elif self.config.sensor_type == SensorType.PRESSURE:
            base_value = 1013.25  # Sea level pressure
            noise = random.gauss(0, 10.0)
        elif self.config.sensor_type == SensorType.ACCELEROMETER:
            base_value = 9.8  # Gravity
            noise = random.gauss(0, 0.5)
        elif self.config.sensor_type == SensorType.GYROSCOPE:
            base_value = 0.0  # No rotation
            noise = random.gauss(0, 0.1)
        elif self.config.sensor_type == SensorType.ULTRASONIC:
            base_value = 100.0  # 100cm distance
            noise = random.gauss(0, 5.0)
        else:
            base_value = random.uniform(0, 100)
            noise = random.gauss(0, 1.0)
        
        # Add some time-varying component
        time_component = 5.0 * math.sin(time.time() * 0.1)
        
        return base_value + noise + time_component
    
    def apply_calibration(self, raw_value: float) -> float:
        """Apply calibration to raw value"""
        calibrated = (raw_value + self.config.calibration_offset) * self.config.calibration_scale
        
        # Store calibration data
        self.calibration_data.append({
            'timestamp': datetime.now(),
            'raw_value': raw_value,
            'calibrated_value': calibrated
        })
        
        # Keep only last 1000 calibration points
        if len(self.calibration_data) > 1000:
            self.calibration_data = self.calibration_data[-1000:]
        
        return calibrated
    
    def apply_filter(self, value: float) -> float:
        """Apply exponential moving average filter"""
        if not self.config.filter_enabled:
            return value
        
        if self.ema_value is None:
            self.ema_value = value
        else:
            self.ema_value = self.ema_alpha * value + (1 - self.ema_alpha) * self.ema_value
        
        return self.ema_value
    
    def validate_reading(self, value: float) -> bool:
        """Validate sensor reading"""
        # Check against min/max limits
        if self.config.min_value is not None and value < self.config.min_value:
            return False
        
        if self.config.max_value is not None and value > self.config.max_value:
            return False
        
        # Check for NaN or infinite values
        if not math.isfinite(value):
            return False
        
        return True
    
    def get_reading(self) -> Optional[SensorReading]:
        """Get processed sensor reading"""
        try:
            # Read raw data
            raw_value = self.read_raw_data()
            
            if raw_value is None:
                return None
            
            # Apply calibration
            calibrated_value = self.apply_calibration(raw_value)
            
            # Apply filter
            filtered_value = self.apply_filter(calibrated_value)
            
            # Validate reading
            if not self.validate_reading(filtered_value):
                self.logger.warning(f"Invalid reading from {self.config.sensor_id}: {filtered_value}")
                return None
            
            # Create reading object
            reading = SensorReading(
                sensor_id=self.config.sensor_id,
                sensor_type=self.config.sensor_type,
                timestamp=datetime.now(),
                value=filtered_value,
                unit=self._get_unit(),
                raw_value=raw_value,
                calibration_offset=self.config.calibration_offset,
                quality_score=self._calculate_quality_score(raw_value, filtered_value),
                metadata=self._get_metadata()
            )
            
            self.last_reading = reading
            self.filtered_value = filtered_value
            self.reading_count += 1
            
            return reading
            
        except Exception as e:
            self.logger.error(f"Error getting reading from {self.config.sensor_id}: {e}")
            self.error_count += 1
            return None
    
    def _get_unit(self) -> str:
        """Get sensor unit"""
        unit_map = {
            SensorType.TEMPERATURE: "°C",
            SensorType.HUMIDITY: "%",
            SensorType.PRESSURE: "hPa",
            SensorType.ACCELEROMETER: "m/s²",
            SensorType.GYROSCOPE: "rad/s",
            SensorType.MAGNETOMETER: "μT",
            SensorType.ULTRASONIC: "cm",
            SensorType.IR: "V",
            SensorType.LIDAR: "m",
            SensorType.CAMERA: "px",
            SensorType.GPS: "°",
            SensorType.ENCODER: "ticks"
        }
        
        return unit_map.get(self.config.sensor_type, "unknown")
    
    def _calculate_quality_score(self, raw_value: float, filtered_value: float) -> float:
        """Calculate reading quality score"""
        quality = 1.0
        
        # Reduce quality based on error rate
        if self.reading_count > 0:
            error_rate = self.error_count / self.reading_count
            quality *= (1.0 - error_rate)
        
        # Reduce quality based on noise
        if len(self.calibration_data) > 10:
            recent_data = self.calibration_data[-10:]
            values = [d['calibrated_value'] for d in recent_data]
            
            if NUMPY_AVAILABLE:
                std_dev = np.std(values)
                mean_val = np.mean(values)
                if mean_val != 0:
                    noise_ratio = std_dev / abs(mean_val)
                    quality *= max(0.1, 1.0 - noise_ratio)
        
        return max(0.0, min(1.0, quality))
    
    def _get_metadata(self) -> Dict[str, Any]:
        """Get sensor metadata"""
        return {
            'sensor_name': self.config.name,
            'status': self.status.value,
            'error_count': self.error_count,
            'reading_count': self.reading_count,
            'filter_enabled': self.config.filter_enabled,
            'calibration_applied': True,
            'port': self.config.port,
            'data_rate': self.config.data_rate
        }
    
    def calibrate(self, reference_value: float, sample_count: int = 10) -> bool:
        """Calibrate sensor against reference value"""
        if self.status != SensorStatus.ACTIVE:
            self.logger.error(f"Cannot calibrate inactive sensor {self.config.sensor_id}")
            return False
        
        self.status = SensorStatus.CALIBRATING
        self.logger.info(f"Calibrating {self.config.sensor_id}...")
        
        try:
            readings = []
            
            for _ in range(sample_count):
                reading = self.get_reading()
                if reading:
                    readings.append(reading.raw_value)
                time.sleep(0.1)
            
            if len(readings) < sample_count // 2:
                self.logger.error(f"Insufficient readings for calibration of {self.config.sensor_id}")
                self.status = SensorStatus.ERROR
                return False
            
            # Calculate average offset
            avg_reading = sum(readings) / len(readings)
            offset = reference_value - avg_reading
            
            # Update calibration
            self.config.calibration_offset = offset
            
            self.status = SensorStatus.ACTIVE
            self.logger.info(f"Calibration completed for {self.config.sensor_id}. Offset: {offset:.4f}")
            
            return True
            
        except Exception as e:
            self.logger.error(f"Error calibrating {self.config.sensor_id}: {e}")
            self.status = SensorStatus.ERROR
            return False

class SensorManager:
    """Multi-sensor management system"""
    
    def __init__(self):
        self.sensors: Dict[str, Sensor] = {}
        self.readings: List[SensorReading] = []
        self.is_running = False
        self.reading_thread = None
        self.callbacks: List[Callable] = []
        
        # Data processing
        self.max_readings = 10000
        self.data_rate = 10.0  # Hz
        
        # Setup logging
        logging.basicConfig(level=logging.INFO)
        self.logger = logging.getLogger("sensor_manager")
    
    def add_sensor(self, config: SensorConfig) -> bool:
        """Add a sensor to the manager"""
        try:
            sensor = Sensor(config)
            self.sensors[config.sensor_id] = sensor
            self.logger.info(f"Added sensor: {config.sensor_id}")
            return True
        except Exception as e:
            self.logger.error(f"Error adding sensor {config.sensor_id}: {e}")
            return False
    
    def remove_sensor(self, sensor_id: str) -> bool:
        """Remove a sensor from the manager"""
        if sensor_id in self.sensors:
            sensor = self.sensors[sensor_id]
            sensor.disconnect()
            del self.sensors[sensor_id]
            self.logger.info(f"Removed sensor: {sensor_id}")
            return True
        else:
            self.logger.error(f"Sensor {sensor_id} not found")
            return False
    
    def connect_sensor(self, sensor_id: str) -> bool:
        """Connect to a specific sensor"""
        if sensor_id in self.sensors:
            return self.sensors[sensor_id].connect()
        else:
            self.logger.error(f"Sensor {sensor_id} not found")
            return False
    
    def disconnect_sensor(self, sensor_id: str) -> bool:
        """Disconnect from a specific sensor"""
        if sensor_id in self.sensors:
            self.sensors[sensor_id].disconnect()
            return True
        else:
            self.logger.error(f"Sensor {sensor_id} not found")
            return False
    
    def connect_all_sensors(self) -> Dict[str, bool]:
        """Connect to all sensors"""
        results = {}
        
        for sensor_id, sensor in self.sensors.items():
            results[sensor_id] = sensor.connect()
        
        return results
    
    def disconnect_all_sensors(self):
        """Disconnect from all sensors"""
        for sensor in self.sensors.values():
            sensor.disconnect()
    
    def add_reading_callback(self, callback: Callable):
        """Add callback for new readings"""
        self.callbacks.append(callback)
    
    def start_reading(self):
        """Start continuous reading from all sensors"""
        if self.is_running:
            return
        
        self.is_running = True
        self.reading_thread = threading.Thread(target=self._reading_loop, daemon=True)
        self.reading_thread.start()
        self.logger.info("Sensor reading started")
    
    def stop_reading(self):
        """Stop continuous reading"""
        self.is_running = False
        if self.reading_thread:
            self.reading_thread.join()
        self.logger.info("Sensor reading stopped")
    
    def _reading_loop(self):
        """Main reading loop"""
        dt = 1.0 / self.data_rate
        
        while self.is_running:
            try:
                # Read from all sensors
                for sensor in self.sensors.values():
                    if sensor.status == SensorStatus.ACTIVE:
                        reading = sensor.get_reading()
                        
                        if reading:
                            self.readings.append(reading)
                            
                            # Limit readings in memory
                            if len(self.readings) > self.max_readings:
                                self.readings = self.readings[-self.max_readings:]
                            
                            # Call callbacks
                            for callback in self.callbacks:
                                try:
                                    callback(reading)
                                except Exception as e:
                                    self.logger.error(f"Callback error: {e}")
                
                time.sleep(dt)
                
            except Exception as e:
                self.logger.error(f"Error in reading loop: {e}")
                time.sleep(dt)
    
    def get_latest_readings(self) -> Dict[str, Optional[SensorReading]]:
        """Get latest readings from all sensors"""
        latest_readings = {}
        
        for sensor_id, sensor in self.sensors.items():
            latest_readings[sensor_id] = sensor.last_reading
        
        return latest_readings
    
    def get_sensor_readings(self, sensor_id: str, count: int = 100) -> List[SensorReading]:
        """Get recent readings from a specific sensor"""
        sensor_readings = [r for r in self.readings if r.sensor_id == sensor_id]
        return sensor_readings[-count:]
    
    def get_readings_by_type(self, sensor_type: SensorType, count: int = 100) -> List[SensorReading]:
        """Get recent readings by sensor type"""
        type_readings = [r for r in self.readings if r.sensor_type == sensor_type]
        return type_readings[-count:]
    
    def calibrate_sensor(self, sensor_id: str, reference_value: float) -> bool:
        """Calibrate a specific sensor"""
        if sensor_id in self.sensors:
            return self.sensors[sensor_id].calibrate(reference_value)
        else:
            self.logger.error(f"Sensor {sensor_id} not found")
            return False
    
    def get_sensor_statistics(self) -> Dict[str, Dict]:
        """Get statistics for all sensors"""
        stats = {}
        
        for sensor_id, sensor in self.sensors.items():
            readings = self.get_sensor_readings(sensor_id, 1000)
            
            if readings:
                values = [r.value for r in readings]
                
                if NUMPY_AVAILABLE:
                    sensor_stats = {
                        'count': len(values),
                        'mean': np.mean(values),
                        'std': np.std(values),
                        'min': np.min(values),
                        'max': np.max(values),
                        'latest': values[-1] if values else None,
                        'quality_score': readings[-1].quality_score if readings else 0.0,
                        'status': sensor.status.value,
                        'error_rate': sensor.error_count / max(1, sensor.reading_count)
                    }
                else:
                    # Basic statistics without numpy
                    sensor_stats = {
                        'count': len(values),
                        'mean': sum(values) / len(values),
                        'latest': values[-1] if values else None,
                        'quality_score': readings[-1].quality_score if readings else 0.0,
                        'status': sensor.status.value,
                        'error_rate': sensor.error_count / max(1, sensor.reading_count)
                    }
                
                stats[sensor_id] = sensor_stats
            else:
                stats[sensor_id] = {
                    'count': 0,
                    'status': sensor.status.value,
                    'error_rate': sensor.error_count / max(1, sensor.reading_count)
                }
        
        return stats
    
    def save_readings(self, filename: str, sensor_id: str = None):
        """Save readings to file"""
        if sensor_id:
            readings = self.get_sensor_readings(sensor_id)
        else:
            readings = self.readings
        
        data = {
            'timestamp': datetime.now().isoformat(),
            'sensor_id': sensor_id,
            'readings': [
                {
                    'sensor_id': r.sensor_id,
                    'sensor_type': r.sensor_type.value,
                    'timestamp': r.timestamp.isoformat(),
                    'value': r.value,
                    'unit': r.unit,
                    'raw_value': r.raw_value,
                    'quality_score': r.quality_score,
                    'metadata': r.metadata
                }
                for r in readings
            ]
        }
        
        with open(filename, 'w') as f:
            json.dump(data, f, indent=2)
        
        self.logger.info(f"Readings saved to {filename}")
    
    def generate_report(self) -> str:
        """Generate sensor system report"""
        report = []
        report.append("=" * 60)
        report.append("SENSOR SYSTEM REPORT")
        report.append("=" * 60)
        report.append(f"Total Sensors: {len(self.sensors)}")
        report.append(f"Reading Status: {'Active' if self.is_running else 'Inactive'}")
        report.append(f"Total Readings: {len(self.readings)}")
        report.append("")
        
        # Sensor statistics
        stats = self.get_sensor_statistics()
        
        for sensor_id, sensor_stats in stats.items():
            sensor = self.sensors[sensor_id]
            
            report.append(f"Sensor: {sensor_id}")
            report.append(f"  Type: {sensor.config.sensor_type.value}")
            report.append(f"  Name: {sensor.config.name}")
            report.append(f"  Status: {sensor_stats['status']}")
            report.append(f"  Readings: {sensor_stats['count']}")
            
            if sensor_stats['count'] > 0:
                report.append(f"  Latest Value: {sensor_stats['latest']:.4f} {sensor._get_unit()}")
                report.append(f"  Mean: {sensor_stats.get('mean', 'N/A'):.4f}")
                report.append(f"  Std Dev: {sensor_stats.get('std', 'N/A'):.4f}")
                report.append(f"  Quality Score: {sensor_stats['quality_score']:.3f}")
            
            report.append(f"  Error Rate: {sensor_stats['error_rate']:.3f}")
            report.append("")
        
        return "\n".join(report)

def create_sample_sensor_configurations() -> List[SensorConfig]:
    """Create sample sensor configurations"""
    configs = [
        SensorConfig(
            sensor_id="temp_01",
            sensor_type=SensorType.TEMPERATURE,
            name="Temperature Sensor 1",
            port="/dev/ttyUSB0",
            baud_rate=9600,
            data_rate=1.0,
            calibration_offset=0.0,
            calibration_scale=1.0,
            min_value=-40.0,
            max_value=85.0
        ),
        SensorConfig(
            sensor_id="hum_01",
            sensor_type=SensorType.HUMIDITY,
            name="Humidity Sensor 1",
            port="/dev/ttyUSB1",
            baud_rate=9600,
            data_rate=0.5,
            calibration_offset=0.0,
            calibration_scale=1.0,
            min_value=0.0,
            max_value=100.0
        ),
        SensorConfig(
            sensor_id="press_01",
            sensor_type=SensorType.PRESSURE,
            name="Pressure Sensor 1",
            port="/dev/ttyUSB2",
            baud_rate=9600,
            data_rate=2.0,
            calibration_offset=0.0,
            calibration_scale=1.0,
            min_value=800.0,
            max_value=1200.0
        ),
        SensorConfig(
            sensor_id="accel_01",
            sensor_type=SensorType.ACCELEROMETER,
            name="Accelerometer 1",
            port="/dev/ttyUSB3",
            baud_rate=115200,
            data_rate=10.0,
            calibration_offset=0.0,
            calibration_scale=1.0,
            min_value=-20.0,
            max_value=20.0
        ),
        SensorConfig(
            sensor_id="ultra_01",
            sensor_type=SensorType.ULTRASONIC,
            name="Ultrasonic Sensor 1",
            port="/dev/ttyUSB4",
            baud_rate=9600,
            data_rate=5.0,
            calibration_offset=0.0,
            calibration_scale=1.0,
            min_value=2.0,
            max_value=400.0
        )
    ]
    
    return configs

def main():
    """Main function to demonstrate sensor reader"""
    print("=== Multi-Sensor Data Reader ===\n")
    
    # Create sensor manager
    manager = SensorManager()
    
    # Add sample sensors
    print("1. Adding sample sensors...")
    configs = create_sample_sensor_configurations()
    
    for config in configs:
        success = manager.add_sensor(config)
        if success:
            print(f"  Added: {config.sensor_id} ({config.sensor_type.value})")
    
    # Connect all sensors
    print("\n2. Connecting to sensors...")
    connection_results = manager.connect_all_sensors()
    
    for sensor_id, success in connection_results.items():
        status = "Connected" if success else "Failed"
        print(f"  {sensor_id}: {status}")
    
    # Add reading callback
    def reading_callback(reading: SensorReading):
        """Callback for new readings"""
        if reading.reading_count % 10 == 0:  # Print every 10th reading
            print(f"  {reading.sensor_id}: {reading.value:.2f} {reading.unit}")
    
    manager.add_reading_callback(reading_callback)
    
    # Start reading
    print("\n3. Starting sensor readings...")
    manager.start_reading()
    
    # Let sensors read for a while
    print("Reading sensors for 10 seconds...")
    time.sleep(10)
    
    # Show latest readings
    print("\n4. Latest readings:")
    latest_readings = manager.get_latest_readings()
    
    for sensor_id, reading in latest_readings.items():
        if reading:
            print(f"  {sensor_id}: {reading.value:.2f} {reading.unit} "
                  f"(Quality: {reading.quality_score:.3f})")
        else:
            print(f"  {sensor_id}: No reading")
    
    # Calibrate a sensor
    print("\n5. Calibrating temperature sensor...")
    success = manager.calibrate_sensor("temp_01", 25.0)
    print(f"Calibration: {'Success' if success else 'Failed'}")
    
    # Get sensor statistics
    print("\n6. Sensor statistics:")
    stats = manager.get_sensor_statistics()
    
    for sensor_id, sensor_stats in stats.items():
        print(f"  {sensor_id}:")
        print(f"    Readings: {sensor_stats['count']}")
        print(f"    Status: {sensor_stats['status']}")
        print(f"    Error Rate: {sensor_stats['error_rate']:.3f}")
        
        if sensor_stats['count'] > 0:
            print(f"    Latest: {sensor_stats['latest']:.4f}")
            print(f"    Quality: {sensor_stats['quality_score']:.3f}")
    
    # Generate report
    print("\n7. Generating system report...")
    report = manager.generate_report()
    print(report)
    
    # Save readings
    print("\n8. Saving readings...")
    manager.save_readings('sensor_readings.json')
    
    # Interactive menu
    print("\n=== Sensor Manager Interactive ===")
    
    try:
        while True:
            print("\nOptions:")
            print("1. Show latest readings")
            print("2. Show sensor statistics")
            print("3. Calibrate sensor")
            print("4. Connect/disconnect sensor")
            print("5. Save readings")
            print("6. Generate report")
            print("7. Start/stop reading")
            print("8. Show sensor details")
            print("0. Exit")
            
            choice = input("\nSelect option: ").strip()
            
            if choice == "0":
                break
            
            elif choice == "1":
                latest_readings = manager.get_latest_readings()
                print("\nLatest Readings:")
                for sensor_id, reading in latest_readings.items():
                    if reading:
                        print(f"  {sensor_id}: {reading.value:.4f} {reading.unit} "
                              f"(Quality: {reading.quality_score:.3f})")
                    else:
                        print(f"  {sensor_id}: No reading")
            
            elif choice == "2":
                stats = manager.get_sensor_statistics()
                print("\nSensor Statistics:")
                for sensor_id, sensor_stats in stats.items():
                    print(f"\n{sensor_id}:")
                    for key, value in sensor_stats.items():
                        print(f"  {key}: {value}")
            
            elif choice == "3":
                sensor_id = input("Enter sensor ID: ").strip()
                reference_value = float(input("Enter reference value: "))
                
                success = manager.calibrate_sensor(sensor_id, reference_value)
                print(f"Calibration: {'Success' if success else 'Failed'}")
            
            elif choice == "4":
                action = input("Connect or disconnect? (c/d): ").strip().lower()
                sensor_id = input("Enter sensor ID: ").strip()
                
                if action == 'c':
                    success = manager.connect_sensor(sensor_id)
                    print(f"Connection: {'Success' if success else 'Failed'}")
                elif action == 'd':
                    success = manager.disconnect_sensor(sensor_id)
                    print(f"Disconnection: {'Success' if success else 'Failed'}")
            
            elif choice == "5":
                filename = input("Enter filename (default: sensor_data.json): ").strip()
                if not filename:
                    filename = "sensor_data.json"
                
                sensor_id = input("Enter sensor ID (optional): ").strip()
                if not sensor_id:
                    sensor_id = None
                
                manager.save_readings(filename, sensor_id)
                print(f"Readings saved to {filename}")
            
            elif choice == "6":
                report = manager.generate_report()
                print("\n" + report)
                
                save_report = input("Save report to file? (y/n): ").strip().lower()
                if save_report == 'y':
                    filename = input("Enter filename (default: sensor_report.txt): ").strip()
                    if not filename:
                        filename = "sensor_report.txt"
                    
                    with open(filename, 'w') as f:
                        f.write(report)
                    print(f"Report saved to {filename}")
            
            elif choice == "7":
                action = input("Start or stop? (s/stop): ").strip().lower()
                
                if action == 's':
                    if not manager.is_running:
                        manager.start_reading()
                        print("Reading started")
                    else:
                        print("Reading already active")
                elif action == 'stop':
                    if manager.is_running:
                        manager.stop_reading()
                        print("Reading stopped")
                    else:
                        print("Reading already stopped")
            
            elif choice == "8":
                sensor_id = input("Enter sensor ID: ").strip()
                if sensor_id in manager.sensors:
                    sensor = manager.sensors[sensor_id]
                    config = sensor.config
                    
                    print(f"\nSensor Details: {sensor_id}")
                    print(f"  Type: {config.sensor_type.value}")
                    print(f"  Name: {config.name}")
                    print(f"  Port: {config.port}")
                    print(f"  Baud Rate: {config.baud_rate}")
                    print(f"  Data Rate: {config.data_rate} Hz")
                    print(f"  Calibration Offset: {config.calibration_offset}")
                    print(f"  Calibration Scale: {config.calibration_scale}")
                    print(f"  Filter Enabled: {config.filter_enabled}")
                    print(f"  Filter Alpha: {config.filter_alpha}")
                    print(f"  Status: {sensor.status.value}")
                    print(f"  Reading Count: {sensor.reading_count}")
                    print(f"  Error Count: {sensor.error_count}")
                    
                    if sensor.last_reading:
                        print(f"  Last Reading: {sensor.last_reading.value:.4f} {sensor._get_unit()}")
                else:
                    print(f"Sensor {sensor_id} not found")
            
            else:
                print("Invalid option")
    
    except KeyboardInterrupt:
        print("\nOperation interrupted by user")
    
    finally:
        # Stop reading and disconnect
        manager.stop_reading()
        manager.disconnect_all_sensors()
        print("\nSensor system shutdown completed")
    
    print("\n=== Sensor Reader Demo Completed ===")
    print("Features demonstrated:")
    print("- Multi-sensor management")
    print("- Real-time data acquisition")
    print("- Sensor calibration")
    print("- Data filtering and validation")
    print("- Quality scoring")
    print("- Statistics and reporting")
    print("- Serial communication")
    print("- Callback system")
    print("- Configuration management")
    
    print("\nSupported Sensor Types:")
    print("- Temperature sensors")
    print("- Humidity sensors")
    print("- Pressure sensors")
    print("- Accelerometers")
    print("- Gyroscopes")
    print("- Magnetometers")
    print("- Ultrasonic sensors")
    print("- Infrared sensors")
    print("- LIDAR sensors")
    print("- GPS sensors")
    print("- Encoders")
    
    print("\nData Processing Features:")
    print("- Exponential moving average filtering")
    print("- Calibration offset and scaling")
    print("- Range validation")
    print("- Quality scoring")
    print("- Error tracking")
    print("- Statistical analysis")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Install dependencies: pip install pyserial numpy
2. Run sensor reader: python sensor_reader.py
3. Connect real sensors or use simulation mode
4. Monitor real-time sensor data

Key Concepts:
- Sensor Integration: Multiple sensor types and interfaces
- Data Acquisition: Real-time reading and processing
- Calibration: Reference-based sensor calibration
- Filtering: Noise reduction and signal processing
- Validation: Range checking and error detection
- Quality Assessment: Data quality scoring

Sensor Types:
- Environmental: Temperature, humidity, pressure
- Motion: Accelerometer, gyroscope, magnetometer
- Distance: Ultrasonic, infrared, LIDAR
- Position: GPS, encoders
- Imaging: Camera sensors

Communication Protocols:
- Serial Communication: RS-232, USB
- I2C: Inter-Integrated Circuit
- SPI: Serial Peripheral Interface
- CAN: Controller Area Network
- Wireless: Bluetooth, WiFi

Data Processing:
- Filtering: Exponential moving average
- Calibration: Offset and scale adjustment
- Validation: Range and sanity checks
- Statistics: Mean, std dev, min/max
- Quality: Reliability scoring

Applications:
- Robotics and automation
- IoT monitoring systems
- Environmental monitoring
- Industrial automation
- Scientific research
- Weather stations
- Smart home systems
- Automotive sensors

Dependencies:
- pyserial: pip install pyserial (for real sensors)
- numpy: pip install numpy (for data processing)
- threading: Built-in Python
- json: Built-in Python
- logging: Built-in Python

Best Practices:
- Proper sensor calibration
- Error handling and recovery
- Data validation and filtering
- Regular maintenance and testing
- Documentation of sensor specifications
- Backup and redundancy for critical sensors
- Security considerations for sensor data
- Power management for battery-powered sensors
"""
