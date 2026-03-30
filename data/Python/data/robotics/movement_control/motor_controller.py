"""
Robot Motor Controller
======================

Comprehensive motor control system for robotics applications.
Demonstrates motor control, movement planning, and robot kinematics.
"""

import math
import time
import threading
from typing import Dict, List, Tuple, Optional
from dataclasses import dataclass
from enum import Enum
import numpy as np
import json
import logging

class MotorType(Enum):
    """Motor type enumeration"""
    DC = "dc"
    STEPPER = "stepper"
    SERVO = "servo"
    BRUSHLESS = "brushless"

@dataclass
class MotorConfig:
    """Motor configuration"""
    motor_id: str
    motor_type: MotorType
    max_speed: float  # RPM
    max_torque: float  # Nm
    encoder_resolution: int  # pulses per revolution
    gear_ratio: float
    inverted: bool = False

@dataclass
class MotorState:
    """Motor state"""
    current_speed: float  # RPM
    current_position: float  # radians
    current_torque: float  # Nm
    target_speed: float  # RPM
    target_position: float  # radians
    is_enabled: bool
    error_code: int = 0

class PIDController:
    """PID controller for motor control"""
    
    def __init__(self, kp: float = 1.0, ki: float = 0.0, kd: float = 0.0):
        self.kp = kp  # Proportional gain
        self.ki = ki  # Integral gain
        self.kd = kd  # Derivative gain
        
        self.integral = 0.0
        self.previous_error = 0.0
        self.last_time = time.time()
    
    def update(self, setpoint: float, measured_value: float, dt: float = None) -> float:
        """Update PID controller"""
        if dt is None:
            current_time = time.time()
            dt = current_time - self.last_time
            self.last_time = current_time
        
        # Calculate error
        error = setpoint - measured_value
        
        # Proportional term
        p_term = self.kp * error
        
        # Integral term
        self.integral += error * dt
        i_term = self.ki * self.integral
        
        # Derivative term
        if dt > 0:
            derivative = (error - self.previous_error) / dt
            d_term = self.kd * derivative
        else:
            d_term = 0.0
        
        # Calculate output
        output = p_term + i_term + d_term
        
        # Update previous error
        self.previous_error = error
        
        return output
    
    def reset(self):
        """Reset PID controller"""
        self.integral = 0.0
        self.previous_error = 0.0
        self.last_time = time.time()
    
    def set_gains(self, kp: float, ki: float, kd: float):
        """Set PID gains"""
        self.kp = kp
        self.ki = ki
        self.kd = kd

class Motor:
    """Individual motor controller"""
    
    def __init__(self, config: MotorConfig):
        self.config = config
        self.state = MotorState(
            current_speed=0.0,
            current_position=0.0,
            current_torque=0.0,
            target_speed=0.0,
            target_position=0.0,
            is_enabled=False
        )
        
        # PID controllers
        self.speed_pid = PIDController(kp=0.5, ki=0.1, kd=0.05)
        self.position_pid = PIDController(kp=1.0, ki=0.2, kd=0.1)
        
        # Simulation parameters
        self.inertia = 0.01  # kg*m^2
        self.friction = 0.1   # Nm*s/rad
        
        self.logger = logging.getLogger(f"motor_{config.motor_id}")
    
    def enable(self):
        """Enable motor"""
        self.state.is_enabled = True
        self.logger.info(f"Motor {self.config.motor_id} enabled")
    
    def disable(self):
        """Disable motor"""
        self.state.is_enabled = False
        self.state.target_speed = 0.0
        self.state.target_position = self.state.current_position
        self.logger.info(f"Motor {self.config.motor_id} disabled")
    
    def set_speed(self, speed: float):
        """Set target speed (RPM)"""
        if self.config.inverted:
            speed = -speed
        
        # Limit speed to maximum
        max_speed = self.config.max_speed
        self.state.target_speed = max(-max_speed, min(max_speed, speed))
    
    def set_position(self, position: float):
        """Set target position (radians)"""
        if self.config.inverted:
            position = -position
        
        # Normalize position to [-2π, 2π]
        position = ((position + math.pi) % (4 * math.pi)) - 2 * math.pi
        self.state.target_position = position
    
    def update(self, dt: float):
        """Update motor simulation"""
        if not self.state.is_enabled:
            # Motor disabled - apply friction
            self.state.current_speed *= 0.95
            self.state.current_position += self.state.current_speed * dt
            return
        
        # Speed control
        speed_error = self.state.target_speed - self.state.current_speed
        speed_output = self.speed_pid.update(self.state.target_speed, self.state.current_speed, dt)
        
        # Position control (if position mode)
        position_error = self.state.target_position - self.state.current_position
        position_output = self.position_pid.update(self.state.target_position, self.state.current_position, dt)
        
        # Combine control outputs (prioritize position control)
        if abs(position_error) > 0.1:  # 0.1 radian threshold
            control_output = position_output
        else:
            control_output = speed_output
        
        # Simulate motor dynamics
        torque = control_output * self.config.max_torque
        
        # Apply friction
        friction_torque = -self.friction * self.state.current_speed
        net_torque = torque + friction_torque
        
        # Calculate acceleration (τ = Iα)
        angular_acceleration = net_torque / self.inertia
        
        # Update speed and position
        self.state.current_speed += angular_acceleration * dt
        self.state.current_position += self.state.current_speed * dt
        
        # Calculate torque
        self.state.current_torque = torque
        
        # Limit speed
        max_speed = self.config.max_speed * 2 * math.pi / 60  # Convert RPM to rad/s
        self.state.current_speed = max(-max_speed, min(max_speed, self.state.current_speed))
    
    def get_position_degrees(self) -> float:
        """Get current position in degrees"""
        return math.degrees(self.state.current_position)
    
    def set_position_degrees(self, degrees: float):
        """Set target position in degrees"""
        radians = math.radians(degrees)
        self.set_position(radians)
    
    def get_encoder_value(self) -> int:
        """Get simulated encoder value"""
        # Convert position to encoder pulses
        pulses_per_revolution = self.config.encoder_resolution * self.config.gear_ratio
        position_revolutions = self.state.current_position / (2 * math.pi)
        encoder_value = int(position_revolutions * pulses_per_revolution)
        return encoder_value

class RobotController:
    """Multi-motor robot controller"""
    
    def __init__(self):
        self.motors: Dict[str, Motor] = {}
        self.is_running = False
        self.control_thread = None
        self.update_rate = 100  # Hz
        
        # Robot kinematics
        self.robot_type = None
        self.kinematics = None
        
        # Setup logging
        logging.basicConfig(level=logging.INFO)
        self.logger = logging.getLogger("robot_controller")
    
    def add_motor(self, motor_config: MotorConfig):
        """Add a motor to the controller"""
        motor = Motor(motor_config)
        self.motors[motor_config.motor_id] = motor
        self.logger.info(f"Added motor: {motor_config.motor_id}")
    
    def remove_motor(self, motor_id: str):
        """Remove a motor from the controller"""
        if motor_id in self.motors:
            del self.motors[motor_id]
            self.logger.info(f"Removed motor: {motor_id}")
    
    def enable_motor(self, motor_id: str):
        """Enable a specific motor"""
        if motor_id in self.motors:
            self.motors[motor_id].enable()
        else:
            self.logger.error(f"Motor {motor_id} not found")
    
    def disable_motor(self, motor_id: str):
        """Disable a specific motor"""
        if motor_id in self.motors:
            self.motors[motor_id].disable()
        else:
            self.logger.error(f"Motor {motor_id} not found")
    
    def enable_all_motors(self):
        """Enable all motors"""
        for motor in self.motors.values():
            motor.enable()
    
    def disable_all_motors(self):
        """Disable all motors"""
        for motor in self.motors.values():
            motor.disable()
    
    def set_motor_speed(self, motor_id: str, speed: float):
        """Set motor speed (RPM)"""
        if motor_id in self.motors:
            self.motors[motor_id].set_speed(speed)
        else:
            self.logger.error(f"Motor {motor_id} not found")
    
    def set_motor_position(self, motor_id: str, position: float):
        """Set motor position (radians)"""
        if motor_id in self.motors:
            self.motors[motor_id].set_position(position)
        else:
            self.logger.error(f"Motor {motor_id} not found")
    
    def set_motor_position_degrees(self, motor_id: str, degrees: float):
        """Set motor position (degrees)"""
        if motor_id in self.motors:
            self.motors[motor_id].set_position_degrees(degrees)
        else:
            self.logger.error(f"Motor {motor_id} not found")
    
    def get_motor_state(self, motor_id: str) -> Optional[MotorState]:
        """Get motor state"""
        if motor_id in self.motors:
            return self.motors[motor_id].state
        else:
            return None
    
    def get_all_motor_states(self) -> Dict[str, MotorState]:
        """Get all motor states"""
        return {motor_id: motor.state for motor_id, motor in self.motors.items()}
    
    def start_control_loop(self):
        """Start the control loop"""
        if self.is_running:
            return
        
        self.is_running = True
        self.control_thread = threading.Thread(target=self._control_loop, daemon=True)
        self.control_thread.start()
        self.logger.info("Control loop started")
    
    def stop_control_loop(self):
        """Stop the control loop"""
        self.is_running = False
        if self.control_thread:
            self.control_thread.join()
        self.logger.info("Control loop stopped")
    
    def _control_loop(self):
        """Main control loop"""
        dt = 1.0 / self.update_rate
        
        while self.is_running:
            # Update all motors
            for motor in self.motors.values():
                motor.update(dt)
            
            # Sleep for next iteration
            time.sleep(dt)
    
    def emergency_stop(self):
        """Emergency stop - disable all motors immediately"""
        self.disable_all_motors()
        self.logger.warning("Emergency stop activated!")
    
    def save_configuration(self, filename: str):
        """Save motor configuration to file"""
        config_data = {
            'motors': [
                {
                    'motor_id': motor.config.motor_id,
                    'motor_type': motor.config.motor_type.value,
                    'max_speed': motor.config.max_speed,
                    'max_torque': motor.config.max_torque,
                    'encoder_resolution': motor.config.encoder_resolution,
                    'gear_ratio': motor.config.gear_ratio,
                    'inverted': motor.config.inverted
                }
                for motor in self.motors.values()
            ]
        }
        
        with open(filename, 'w') as f:
            json.dump(config_data, f, indent=2)
        
        self.logger.info(f"Configuration saved to {filename}")
    
    def load_configuration(self, filename: str):
        """Load motor configuration from file"""
        try:
            with open(filename, 'r') as f:
                config_data = json.load(f)
            
            # Clear existing motors
            self.motors.clear()
            
            # Load motors
            for motor_data in config_data['motors']:
                config = MotorConfig(
                    motor_id=motor_data['motor_id'],
                    motor_type=MotorType(motor_data['motor_type']),
                    max_speed=motor_data['max_speed'],
                    max_torque=motor_data['max_torque'],
                    encoder_resolution=motor_data['encoder_resolution'],
                    gear_ratio=motor_data['gear_ratio'],
                    inverted=motor_data.get('inverted', False)
                )
                self.add_motor(config)
            
            self.logger.info(f"Configuration loaded from {filename}")
            
        except Exception as e:
            self.logger.error(f"Error loading configuration: {e}")
    
    def generate_status_report(self) -> str:
        """Generate status report"""
        report = []
        report.append("=" * 60)
        report.append("ROBOT MOTOR STATUS REPORT")
        report.append("=" * 60)
        report.append(f"Total Motors: {len(self.motors)}")
        report.append(f"Control Loop: {'Running' if self.is_running else 'Stopped'}")
        report.append("")
        
        for motor_id, motor in self.motors.items():
            state = motor.state
            report.append(f"Motor: {motor_id}")
            report.append(f"  Type: {motor.config.motor_type.value}")
            report.append(f"  Enabled: {state.is_enabled}")
            report.append(f"  Current Speed: {state.current_speed:.2f} RPM")
            report.append(f"  Target Speed: {state.target_speed:.2f} RPM")
            report.append(f"  Current Position: {math.degrees(state.current_position):.2f}°")
            report.append(f"  Target Position: {math.degrees(state.target_position):.2f}°")
            report.append(f"  Current Torque: {state.current_torque:.3f} Nm")
            report.append(f"  Error Code: {state.error_code}")
            report.append("")
        
        return "\n".join(report)

class DifferentialDriveRobot:
    """Differential drive robot kinematics"""
    
    def __init__(self, controller: RobotController, wheel_base: float, wheel_radius: float):
        self.controller = controller
        self.wheel_base = wheel_base
        self.wheel_radius = wheel_radius
        
        # Robot state
        self.x = 0.0
        self.y = 0.0
        self.theta = 0.0  # heading angle
        
        self.logger = logging.getLogger("differential_drive")
    
    def set_wheel_speeds(self, left_speed: float, right_speed: float):
        """Set wheel speeds (RPM)"""
        self.controller.set_motor_speed('left_wheel', left_speed)
        self.controller.set_motor_speed('right_wheel', right_speed)
    
    def forward(self, speed: float):
        """Move forward at given speed (RPM)"""
        self.set_wheel_speeds(speed, speed)
    
    def backward(self, speed: float):
        """Move backward at given speed (RPM)"""
        self.set_wheel_speeds(-speed, -speed)
    
    def turn_left(self, speed: float):
        """Turn left at given speed (RPM)"""
        self.set_wheel_speeds(-speed, speed)
    
    def turn_right(self, speed: float):
        """Turn right at given speed (RPM)"""
        self.set_wheel_speeds(speed, -speed)
    
    def stop(self):
        """Stop the robot"""
        self.set_wheel_speeds(0.0, 0.0)
    
    def update_odometry(self, dt: float):
        """Update robot odometry"""
        left_state = self.controller.get_motor_state('left_wheel')
        right_state = self.controller.get_motor_state('right_wheel')
        
        if left_state and right_state:
            # Convert wheel speeds to linear velocities (m/s)
            left_velocity = (left_state.current_speed * 2 * math.pi / 60) * self.wheel_radius
            right_velocity = (right_state.current_speed * 2 * math.pi / 60) * self.wheel_radius
            
            # Calculate robot velocities
            linear_velocity = (left_velocity + right_velocity) / 2
            angular_velocity = (right_velocity - left_velocity) / self.wheel_base
            
            # Update position
            self.x += linear_velocity * math.cos(self.theta) * dt
            self.y += linear_velocity * math.sin(self.theta) * dt
            self.theta += angular_velocity * dt
            
            # Normalize angle
            self.theta = (self.theta + math.pi) % (2 * math.pi) - math.pi
    
    def get_position(self) -> Tuple[float, float, float]:
        """Get robot position (x, y, theta)"""
        return self.x, self.y, self.theta
    
    def set_position(self, x: float, y: float, theta: float):
        """Set robot position"""
        self.x = x
        self.y = y
        self.theta = theta

def create_sample_robot_configuration() -> RobotController:
    """Create a sample differential drive robot configuration"""
    controller = RobotController()
    
    # Left wheel motor
    left_motor_config = MotorConfig(
        motor_id="left_wheel",
        motor_type=MotorType.DC,
        max_speed=3000.0,  # RPM
        max_torque=0.5,    # Nm
        encoder_resolution=1000,  # pulses per revolution
        gear_ratio=50.0,
        inverted=False
    )
    
    # Right wheel motor
    right_motor_config = MotorConfig(
        motor_id="right_wheel",
        motor_type=MotorType.DC,
        max_speed=3000.0,  # RPM
        max_torque=0.5,    # Nm
        encoder_resolution=1000,  # pulses per revolution
        gear_ratio=50.0,
        inverted=False
    )
    
    # Arm joint motors
    arm_configs = [
        MotorConfig("base_joint", MotorType.SERVO, 180.0, 2.0, 4096, 100.0),
        MotorConfig("shoulder_joint", MotorType.SERVO, 120.0, 1.5, 4096, 150.0),
        MotorConfig("elbow_joint", MotorType.SERVO, 180.0, 1.0, 4096, 100.0),
        MotorConfig("wrist_joint", MotorType.SERVO, 270.0, 0.5, 4096, 50.0),
        MotorConfig("gripper_joint", MotorType.SERVO, 90.0, 0.2, 4096, 25.0)
    ]
    
    # Add motors to controller
    controller.add_motor(left_motor_config)
    controller.add_motor(right_motor_config)
    
    for arm_config in arm_configs:
        controller.add_motor(arm_config)
    
    return controller

def main():
    """Main function to demonstrate motor controller"""
    print("=== Robot Motor Controller ===\n")
    
    # Create robot controller
    controller = create_sample_robot_configuration()
    
    # Create differential drive robot
    robot = DifferentialDriveRobot(controller, wheel_base=0.3, wheel_radius=0.05)
    
    print("1. Robot Configuration:")
    print(f"  Total Motors: {len(controller.motors)}")
    print(f"  Motor Types: {list(set(m.config.motor_type.value for m in controller.motors.values()))}")
    
    # Start control loop
    print("\n2. Starting control loop...")
    controller.start_control_loop()
    
    # Enable all motors
    print("\n3. Enabling motors...")
    controller.enable_all_motors()
    
    # Test individual motor control
    print("\n4. Testing individual motor control...")
    
    # Test wheel motors
    print("  Testing wheel motors...")
    controller.set_motor_speed('left_wheel', 100.0)
    controller.set_motor_speed('right_wheel', 100.0)
    
    time.sleep(2)
    
    controller.set_motor_speed('left_wheel', 0.0)
    controller.set_motor_speed('right_wheel', 0.0)
    
    # Test arm motors
    print("  Testing arm motors...")
    for joint_name in ['base_joint', 'shoulder_joint', 'elbow_joint', 'wrist_joint', 'gripper_joint']:
        controller.set_motor_position_degrees(joint_name, 45.0)
        time.sleep(0.5)
    
    time.sleep(2)
    
    # Return arm to home position
    for joint_name in ['base_joint', 'shoulder_joint', 'elbow_joint', 'wrist_joint', 'gripper_joint']:
        controller.set_motor_position_degrees(joint_name, 0.0)
    
    time.sleep(2)
    
    # Test differential drive movement
    print("\n5. Testing differential drive movement...")
    
    # Move forward
    print("  Moving forward...")
    robot.forward(200.0)
    time.sleep(3)
    
    # Turn right
    print("  Turning right...")
    robot.turn_right(150.0)
    time.sleep(2)
    
    # Move forward
    print("  Moving forward...")
    robot.forward(200.0)
    time.sleep(3)
    
    # Stop
    robot.stop()
    
    # Update odometry
    print("\n6. Robot odometry:")
    for i in range(10):
        robot.update_odometry(0.1)
        x, y, theta = robot.get_position()
        print(f"  Position: x={x:.3f}m, y={y:.3f}m, θ={math.degrees(theta):.1f}°")
        time.sleep(0.1)
    
    # Generate status report
    print("\n7. Motor Status Report:")
    report = controller.generate_status_report()
    print(report)
    
    # Save configuration
    print("\n8. Saving configuration...")
    controller.save_configuration('robot_config.json')
    
    # Interactive control
    print("\n=== Interactive Robot Control ===")
    
    try:
        while True:
            print("\nOptions:")
            print("1. Set motor speed")
            print("2. Set motor position")
            print("3. Enable/disable motor")
            print("4. Differential drive control")
            print("5. Show motor status")
            print("6. Emergency stop")
            print("7. Save configuration")
            print("8. Load configuration")
            print("0. Exit")
            
            choice = input("\nSelect option: ").strip()
            
            if choice == "0":
                break
            
            elif choice == "1":
                motor_id = input("Enter motor ID: ").strip()
                speed = float(input("Enter speed (RPM): "))
                controller.set_motor_speed(motor_id, speed)
                print(f"Set {motor_id} speed to {speed} RPM")
            
            elif choice == "2":
                motor_id = input("Enter motor ID: ").strip()
                degrees = float(input("Enter position (degrees): "))
                controller.set_motor_position_degrees(motor_id, degrees)
                print(f"Set {motor_id} position to {degrees}°")
            
            elif choice == "3":
                motor_id = input("Enter motor ID: ").strip()
                action = input("Enable or disable? (e/d): ").strip().lower()
                
                if action == 'e':
                    controller.enable_motor(motor_id)
                    print(f"Enabled {motor_id}")
                elif action == 'd':
                    controller.disable_motor(motor_id)
                    print(f"Disabled {motor_id}")
            
            elif choice == "4":
                action = input("Action (forward/backward/left/right/stop): ").strip().lower()
                speed = float(input("Enter speed (RPM): ") or "200")
                
                if action == "forward":
                    robot.forward(speed)
                elif action == "backward":
                    robot.backward(speed)
                elif action == "left":
                    robot.turn_left(speed)
                elif action == "right":
                    robot.turn_right(speed)
                elif action == "stop":
                    robot.stop()
                
                print(f"Robot action: {action}")
            
            elif choice == "5":
                motor_id = input("Enter motor ID (or 'all' for all motors): ").strip()
                
                if motor_id == "all":
                    states = controller.get_all_motor_states()
                    for mid, state in states.items():
                        print(f"{mid}: Speed={state.current_speed:.1f} RPM, "
                              f"Position={math.degrees(state.current_position):.1f}°, "
                              f"Enabled={state.is_enabled}")
                else:
                    state = controller.get_motor_state(motor_id)
                    if state:
                        print(f"{motor_id}: Speed={state.current_speed:.1f} RPM, "
                              f"Position={math.degrees(state.current_position):.1f}°, "
                              f"Enabled={state.is_enabled}")
                    else:
                        print(f"Motor {motor_id} not found")
            
            elif choice == "6":
                controller.emergency_stop()
                print("Emergency stop activated!")
            
            elif choice == "7":
                filename = input("Enter filename: ").strip()
                controller.save_configuration(filename)
            
            elif choice == "8":
                filename = input("Enter filename: ").strip()
                controller.load_configuration(filename)
            
            else:
                print("Invalid option")
    
    except KeyboardInterrupt:
        print("\nControl interrupted by user")
    
    finally:
        # Stop control loop
        controller.stop_control_loop()
        print("\nControl loop stopped")
    
    print("\n=== Motor Controller Demo Completed ===")
    print("Features demonstrated:")
    print("- Multi-motor control system")
    print("- PID controllers for speed and position")
    print("- Differential drive kinematics")
    print("- Motor simulation and dynamics")
    print("- Configuration management")
    print("- Real-time control loop")
    print("- Odometry and position tracking")
    print("- Emergency stop functionality")
    
    print("\nMotor Types Supported:")
    print("- DC Motors")
    print("- Stepper Motors")
    print("- Servo Motors")
    print("- Brushless Motors")
    
    print("\nControl Modes:")
    print("- Speed control (RPM)")
    print("- Position control (radians/degrees)")
    print("- Differential drive movement")
    print("- Multi-joint arm control")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Install dependencies: pip install numpy
2. Run motor controller: python motor_controller.py
3. Control motors interactively or programmatically
4. Monitor motor states and positions

Key Concepts:
- Motor Control: Speed and position control algorithms
- PID Controllers: Proportional-Integral-Derivative control
- Differential Drive: Two-wheeled robot kinematics
- Odometry: Position tracking from wheel encoders
- Motor Dynamics: Inertia, friction, torque simulation
- Real-time Control: Multi-threaded control loops

Motor Types:
- DC Motors: Continuous rotation with speed control
- Stepper Motors: Precise position control
- Servo Motors: Position control with feedback
- Brushless Motors: High efficiency DC motors

Control Algorithms:
- PID Control: Industry-standard control algorithm
- Speed Control: Velocity regulation
- Position Control: Angular position regulation
- Trajectory Planning: Smooth motion profiles

Applications:
- Mobile robots
- Robotic arms
- CNC machines
- 3D printers
- Automated guided vehicles
- Industrial automation

Dependencies:
- numpy: pip install numpy
- threading: Built-in Python
- time: Built-in Python
- math: Built-in Python
- logging: Built-in Python

Best Practices:
- Use appropriate PID gains for your system
- Implement safety limits and emergency stops
- Monitor motor temperatures and currents
- Use proper gear ratios for your application
- Implement smooth acceleration profiles
- Regular maintenance and calibration
- Test with low speeds before full operation
"""
