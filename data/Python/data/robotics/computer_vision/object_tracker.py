"""
Real-Time Object Tracker
=======================

Comprehensive object tracking system using computer vision.
Demonstrates object detection, tracking algorithms, and real-time processing.
"""

import cv2
import numpy as np
import time
import threading
from typing import List, Tuple, Dict, Optional
from dataclasses import dataclass
from enum import Enum
import json
import logging

try:
    import imutils
    IMUTILS_AVAILABLE = True
except ImportError:
    print("Warning: imutils not available. Install with: pip install imutils")
    IMUTILS_AVAILABLE = False

class TrackingMethod(Enum):
    """Object tracking methods"""
    CSRT = "csrt"
    KCF = "kcf"
    MOSSE = "mosse"
    BOOSTING = "boosting"
    TLD = "tld"
    MEDIANFLOW = "medianflow"
    MIL = "mil"

@dataclass
class BoundingBox:
    """Bounding box for object detection"""
    x: float
    y: float
    width: float
    height: float
    confidence: float = 1.0
    class_id: Optional[int] = None
    class_name: Optional[str] = None
    
    def get_center(self) -> Tuple[float, float]:
        """Get center point of bounding box"""
        return (self.x + self.width / 2, self.y + self.height / 2)
    
    def get_area(self) -> float:
        """Get area of bounding box"""
        return self.width * self.height
    
    def to_cv2_rect(self) -> Tuple[int, int, int, int]:
        """Convert to OpenCV rectangle format"""
        return (int(self.x), int(self.y), int(self.width), int(self.height))

@dataclass
class TrackedObject:
    """Tracked object information"""
    id: int
    bbox: BoundingBox
    velocity: Tuple[float, float]
    age: int
    last_seen: float
    color: Tuple[int, int, int]
    tracker: Optional[cv2.Tracker] = None
    tracking_method: TrackingMethod = TrackingMethod.CSRT
    lost_count: int = 0

class ObjectTracker:
    """Real-time object tracking system"""
    
    def __init__(self, tracking_method: TrackingMethod = TrackingMethod.CSRT):
        self.tracking_method = tracking_method
        self.tracked_objects: List[TrackedObject] = []
        self.next_id = 1
        self.is_tracking = False
        self.tracking_thread = None
        self.frame_count = 0
        self.fps = 0.0
        self.last_time = time.time()
        
        # Tracking parameters
        self.max_lost_frames = 30
        self.min_detection_confidence = 0.5
        self.max_objects = 10
        
        # Colors for visualization
        self.colors = [
            (255, 0, 0), (0, 255, 0), (0, 0, 255), (255, 255, 0),
            (255, 0, 255), (0, 255, 255), (128, 0, 128), (0, 128, 128),
            (128, 128, 0), (255, 165, 0)
        ]
        
        # Setup logging
        logging.basicConfig(level=logging.INFO)
        self.logger = logging.getLogger("object_tracker")
    
    def create_tracker(self, method: TrackingMethod) -> Optional[cv2.Tracker]:
        """Create OpenCV tracker"""
        try:
            if method == TrackingMethod.CSRT:
                return cv2.TrackerCSRT_create()
            elif method == TrackingMethod.KCF:
                return cv2.TrackerKCF_create()
            elif method == TrackingMethod.MOSSE:
                return cv2.TrackerMOSSE_create()
            elif method == TrackingMethod.BOOSTING:
                return cv2.TrackerBoosting_create()
            elif method == TrackingMethod.TLD:
                return cv2.TrackerTLD_create()
            elif method == TrackingMethod.MEDIANFLOW:
                return cv2.TrackerMedianFlow_create()
            elif method == TrackingMethod.MIL:
                return cv2.TrackerMIL_create()
            else:
                return None
        except Exception as e:
            self.logger.error(f"Error creating tracker {method}: {e}")
            return None
    
    def detect_objects(self, frame: np.ndarray, use_yolo: bool = False) -> List[BoundingBox]:
        """Detect objects in frame"""
        detections = []
        
        if use_yolo:
            # Use YOLO for detection (requires YOLO files)
            detections = self._yolo_detection(frame)
        else:
            # Use simple background subtraction for demonstration
            detections = self._background_subtraction_detection(frame)
        
        return detections
    
    def _yolo_detection(self, frame: np.ndarray) -> List[BoundingBox]:
        """YOLO object detection"""
        # This is a placeholder for YOLO detection
        # In practice, you would load YOLO model weights and config files
        detections = []
        
        # Simulate detection for demonstration
        height, width = frame.shape[:2]
        
        # Add some fake detections
        fake_detections = [
            (width * 0.3, height * 0.3, width * 0.2, height * 0.3, 0.8, "person"),
            (width * 0.6, height * 0.4, width * 0.15, height * 0.25, 0.7, "car"),
            (width * 0.8, height * 0.6, width * 0.1, height * 0.15, 0.6, "bicycle")
        ]
        
        for x, y, w, h, conf, class_name in fake_detections:
            bbox = BoundingBox(x=x, y=y, width=w, height=h, confidence=conf, class_name=class_name)
            detections.append(bbox)
        
        return detections
    
    def _background_subtraction_detection(self, frame: np.ndarray) -> List[BoundingBox]:
        """Simple background subtraction detection"""
        # This is a simplified detection method for demonstration
        # In practice, you'd use more sophisticated methods
        
        # Convert to grayscale
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        
        # Apply Gaussian blur
        blurred = cv2.GaussianBlur(gray, (5, 5), 0)
        
        # Threshold to find moving objects
        _, thresh = cv2.threshold(blurred, 127, 255, cv2.THRESH_BINARY)
        
        # Find contours
        contours, _ = cv2.findContours(thresh, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
        
        detections = []
        
        for contour in contours:
            # Filter small contours
            area = cv2.contourArea(contour)
            if area < 500:
                continue
            
            # Get bounding box
            x, y, w, h = cv2.boundingRect(contour)
            
            # Create bounding box object
            bbox = BoundingBox(x=x, y=y, width=w, height=h, confidence=0.5)
            detections.append(bbox)
        
        return detections
    
    def update_tracking(self, frame: np.ndarray, detections: List[BoundingBox]):
        """Update object tracking"""
        current_time = time.time()
        
        # Update existing tracked objects
        for tracked_obj in self.tracked_objects[:]:
            if tracked_obj.tracker:
                # Update tracker
                success, bbox = tracked_obj.tracker.update(frame)
                
                if success:
                    # Update bounding box
                    x, y, w, h = bbox
                    tracked_obj.bbox.x = x
                    tracked_obj.bbox.y = y
                    tracked_obj.bbox.width = w
                    tracked_obj.bbox.height = h
                    
                    # Calculate velocity
                    center = tracked_obj.bbox.get_center()
                    if tracked_obj.age > 0:
                        dt = current_time - tracked_obj.last_seen
                        if dt > 0:
                            prev_center = (tracked_obj.bbox.x - tracked_obj.velocity[0] * dt,
                                         tracked_obj.bbox.y - tracked_obj.velocity[1] * dt)
                            tracked_obj.velocity = ((center[0] - prev_center[0]) / dt,
                                                  (center[1] - prev_center[1]) / dt)
                    
                    tracked_obj.last_seen = current_time
                    tracked_obj.age += 1
                    tracked_obj.lost_count = 0
                else:
                    # Tracker lost object
                    tracked_obj.lost_count += 1
                    if tracked_obj.lost_count > self.max_lost_frames:
                        self.tracked_objects.remove(tracked_obj)
                        self.logger.info(f"Lost track of object {tracked_obj.id}")
            else:
                # No tracker, remove object
                self.tracked_objects.remove(tracked_obj)
        
        # Match new detections with existing tracks
        unmatched_detections = detections.copy()
        
        for tracked_obj in self.tracked_objects:
            best_match = None
            best_distance = float('inf')
            
            for detection in unmatched_detections:
                # Calculate distance between centers
                tracked_center = tracked_obj.bbox.get_center()
                detection_center = detection.get_center()
                
                distance = np.sqrt((tracked_center[0] - detection_center[0])**2 + 
                                 (tracked_center[1] - detection_center[1])**2)
                
                # Check if detection is close enough
                max_distance = max(tracked_obj.bbox.width, tracked_obj.bbox.height) * 0.5
                
                if distance < max_distance and distance < best_distance:
                    best_match = detection
                    best_distance = distance
            
            if best_match:
                # Update tracked object with new detection
                tracked_obj.bbox = best_match
                tracked_obj.last_seen = current_time
                unmatched_detections.remove(best_match)
                
                # Reinitialize tracker if needed
                if tracked_obj.tracker is None or tracked_obj.lost_count > 5:
                    tracked_obj.tracker = self.create_tracker(self.tracking_method)
                    if tracked_obj.tracker:
                        tracked_obj.tracker.init(frame, best_match.to_cv2_rect())
        
        # Create new tracked objects for unmatched detections
        for detection in unmatched_detections:
            if len(self.tracked_objects) < self.max_objects:
                # Create new tracker
                tracker = self.create_tracker(self.tracking_method)
                
                if tracker:
                    tracker.init(frame, detection.to_cv2_rect())
                    
                    # Create tracked object
                    tracked_obj = TrackedObject(
                        id=self.next_id,
                        bbox=detection,
                        velocity=(0.0, 0.0),
                        age=0,
                        last_seen=current_time,
                        color=self.colors[self.next_id % len(self.colors)],
                        tracker=tracker,
                        tracking_method=self.tracking_method,
                        lost_count=0
                    )
                    
                    self.tracked_objects.append(tracked_obj)
                    self.next_id += 1
                    
                    self.logger.info(f"Started tracking object {tracked_obj.id}")
    
    def draw_tracked_objects(self, frame: np.ndarray) -> np.ndarray:
        """Draw tracked objects on frame"""
        output_frame = frame.copy()
        
        for tracked_obj in self.tracked_objects:
            # Draw bounding box
            x, y, w, h = int(tracked_obj.bbox.x), int(tracked_obj.bbox.y), \
                           int(tracked_obj.bbox.width), int(tracked_obj.bbox.height)
            
            cv2.rectangle(output_frame, (x, y), (x + w, y + h), tracked_obj.color, 2)
            
            # Draw object ID and info
            label = f"ID: {tracked_obj.id}"
            if tracked_obj.bbox.class_name:
                label += f" {tracked_obj.bbox.class_name}"
            
            label += f" ({tracked_obj.bbox.confidence:.2f})"
            
            # Draw label background
            label_size = cv2.getTextSize(label, cv2.FONT_HERSHEY_SIMPLEX, 0.5, 2)[0]
            cv2.rectangle(output_frame, (x, y - label_size[1] - 10), 
                          (x + label_size[0], y), tracked_obj.color, -1)
            
            # Draw label text
            cv2.putText(output_frame, label, (x, y - 5), 
                       cv2.FONT_HERSHEY_SIMPLEX, 0.5, (255, 255, 255), 2)
            
            # Draw velocity vector
            if tracked_obj.age > 5:  # Only show velocity after some frames
                center = tracked_obj.bbox.get_center()
                velocity_scale = 10.0  # Scale factor for visualization
                
                end_x = int(center[0] + tracked_obj.velocity[0] * velocity_scale)
                end_y = int(center[1] + tracked_obj.velocity[1] * velocity_scale)
                
                cv2.arrowedLine(output_frame, (int(center[0]), int(center[1])), 
                              (end_x, end_y), tracked_obj.color, 2)
            
            # Draw tracking method
            method_label = f"Method: {tracked_obj.tracking_method.value}"
            cv2.putText(output_frame, method_label, (x, y + h + 20), 
                       cv2.FONT_HERSHEY_SIMPLEX, 0.4, tracked_obj.color, 1)
        
        return output_frame
    
    def calculate_fps(self) -> float:
        """Calculate frames per second"""
        current_time = time.time()
        dt = current_time - self.last_time
        
        if dt > 0:
            self.fps = 1.0 / dt
        
        self.last_time = current_time
        return self.fps
    
    def get_tracking_statistics(self) -> Dict:
        """Get tracking statistics"""
        return {
            'tracked_objects': len(self.tracked_objects),
            'fps': self.fps,
            'frame_count': self.frame_count,
            'tracking_method': self.tracking_method.value,
            'objects_by_age': {obj.id: obj.age for obj in self.tracked_objects},
            'average_velocity': self._calculate_average_velocity()
        }
    
    def _calculate_average_velocity(self) -> Tuple[float, float]:
        """Calculate average velocity of all tracked objects"""
        if not self.tracked_objects:
            return (0.0, 0.0)
        
        avg_vx = sum(obj.velocity[0] for obj in self.tracked_objects) / len(self.tracked_objects)
        avg_vy = sum(obj.velocity[1] for obj in self.tracked_objects) / len(self.tracked_objects)
        
        return (avg_vx, avg_vy)
    
    def reset_tracking(self):
        """Reset all tracking"""
        self.tracked_objects.clear()
        self.next_id = 1
        self.frame_count = 0
        self.logger.info("Tracking reset")
    
    def save_tracking_data(self, filename: str):
        """Save tracking data to file"""
        tracking_data = {
            'timestamp': time.time(),
            'tracking_method': self.tracking_method.value,
            'tracked_objects': [
                {
                    'id': obj.id,
                    'bbox': {
                        'x': obj.bbox.x,
                        'y': obj.bbox.y,
                        'width': obj.bbox.width,
                        'height': obj.bbox.height,
                        'confidence': obj.bbox.confidence,
                        'class_name': obj.bbox.class_name
                    },
                    'velocity': obj.velocity,
                    'age': obj.age,
                    'last_seen': obj.last_seen,
                    'color': obj.color,
                    'lost_count': obj.lost_count
                }
                for obj in self.tracked_objects
            ],
            'statistics': self.get_tracking_statistics()
        }
        
        with open(filename, 'w') as f:
            json.dump(tracking_data, f, indent=2)
        
        self.logger.info(f"Tracking data saved to {filename}")
    
    def process_video_file(self, video_path: str, output_path: str = None) -> bool:
        """Process video file for object tracking"""
        cap = cv2.VideoCapture(video_path)
        
        if not cap.isOpened():
            self.logger.error(f"Cannot open video file: {video_path}")
            return False
        
        # Get video properties
        fps = int(cap.get(cv2.CAP_PROP_FPS))
        width = int(cap.get(cv2.CAP_PROP_FRAME_WIDTH))
        height = int(cap.get(cv2.CAP_PROP_FRAME_HEIGHT))
        
        # Setup video writer if output path provided
        writer = None
        if output_path:
            fourcc = cv2.VideoWriter_fourcc(*'XVID')
            writer = cv2.VideoWriter(output_path, fourcc, fps, (width, height))
        
        self.logger.info(f"Processing video: {video_path}")
        self.logger.info(f"Video properties: {width}x{height} @ {fps} FPS")
        
        frame_count = 0
        processing_times = []
        
        try:
            while True:
                ret, frame = cap.read()
                if not ret:
                    break
                
                start_time = time.time()
                
                # Detect objects
                detections = self.detect_objects(frame)
                
                # Update tracking
                self.update_tracking(frame, detections)
                
                # Draw results
                output_frame = self.draw_tracked_objects(frame)
                
                # Calculate FPS
                processing_time = time.time() - start_time
                processing_times.append(processing_time)
                
                # Draw FPS and statistics
                self._draw_statistics(output_frame, processing_times)
                
                # Write frame if output path provided
                if writer:
                    writer.write(output_frame)
                
                # Display frame
                cv2.imshow('Object Tracking', output_frame)
                
                # Check for key press
                key = cv2.waitKey(1) & 0xFF
                if key == ord('q'):
                    break
                elif key == ord('r'):
                    self.reset_tracking()
                elif key == ord('s'):
                    self.save_tracking_data(f'tracking_data_{frame_count}.json')
                
                frame_count += 1
                self.frame_count += 1
                
                if frame_count % 30 == 0:
                    avg_processing_time = sum(processing_times[-30:]) / len(processing_times[-30:])
                    self.logger.info(f"Processed {frame_count} frames, avg time: {avg_processing_time:.4f}s")
        
        finally:
            cap.release()
            if writer:
                writer.release()
            cv2.destroyAllWindows()
        
        self.logger.info(f"Video processing completed: {frame_count} frames")
        return True
    
    def _draw_statistics(self, frame: np.ndarray, processing_times: List[float]):
        """Draw statistics on frame"""
        # Calculate FPS
        current_fps = self.calculate_fps()
        
        # Calculate average processing time
        if processing_times:
            avg_time = sum(processing_times[-10:]) / len(processing_times[-10:])
        else:
            avg_time = 0.0
        
        # Draw statistics
        stats_text = [
            f"FPS: {current_fps:.1f}",
            f"Objects: {len(self.tracked_objects)}",
            f"Frame: {self.frame_count}",
            f"Method: {self.tracking_method.value}",
            f"Avg Time: {avg_time:.4f}s"
        ]
        
        y_offset = 30
        for text in stats_text:
            cv2.putText(frame, text, (10, y_offset), 
                       cv2.FONT_HERSHEY_SIMPLEX, 0.6, (255, 255, 255), 2)
            y_offset += 25
        
        # Draw instructions
        instructions = [
            "Press 'q' to quit",
            "Press 'r' to reset",
            "Press 's' to save data"
        ]
        
        y_offset = frame.shape[0] - 80
        for text in instructions:
            cv2.putText(frame, text, (10, y_offset), 
                       cv2.FONT_HERSHEY_SIMPLEX, 0.5, (200, 200, 200), 1)
            y_offset += 20

def main():
    """Main function to demonstrate object tracker"""
    print("=== Real-Time Object Tracker ===\n")
    
    # Create tracker
    tracker = ObjectTracker(tracking_method=TrackingMethod.CSRT)
    
    print("Available tracking methods:")
    for method in TrackingMethod:
        print(f"  - {method.value}")
    
    print("\n1. Testing with webcam...")
    
    # Try to open webcam
    cap = cv2.VideoCapture(0)
    
    if not cap.isOpened():
        print("Cannot open webcam. Testing with sample video...")
        
        # Create a sample video with moving objects
        print("\n2. Creating sample video for testing...")
        
        # Create a simple animated video
        width, height = 640, 480
        fourcc = cv2.VideoWriter_fourcc(*'XVID')
        out = cv2.VideoWriter('sample_video.avi', fourcc, 20.0, (width, height))
        
        for frame_num in range(100):
            # Create black frame
            frame = np.zeros((height, width, 3), dtype=np.uint8)
            
            # Add moving rectangle
            x = int(width * 0.2 + 0.3 * width * np.sin(frame_num * 0.1))
            y = int(height * 0.3 + 0.2 * height * np.cos(frame_num * 0.1))
            w, h = 80, 60
            
            cv2.rectangle(frame, (x, y), (x + w, y + h), (0, 255, 0), -1)
            
            # Add moving circle
            cx = int(width * 0.7 + 0.2 * width * np.cos(frame_num * 0.08))
            cy = int(height * 0.6 + 0.15 * height * np.sin(frame_num * 0.08))
            cv2.circle(frame, (cx, cy), 30, (255, 0, 0), -1)
            
            out.write(frame)
        
        out.release()
        print("Sample video created: sample_video.avi")
        
        # Process sample video
        print("\n3. Processing sample video...")
        tracker.process_video_file('sample_video.avi', 'sample_output.avi')
        
    else:
        print("Webcam opened successfully!")
        
        print("\nControls:")
        print("  - Press 'q' to quit")
        print("  - Press 'r' to reset tracking")
        print("  - Press 's' to save tracking data")
        print("  - Press '1-7' to change tracking method")
        
        try:
            while True:
                ret, frame = cap.read()
                if not ret:
                    break
                
                # Detect objects
                detections = tracker.detect_objects(frame)
                
                # Update tracking
                tracker.update_tracking(frame, detections)
                
                # Draw results
                output_frame = tracker.draw_tracked_objects(frame)
                
                # Draw statistics
                processing_times = [0.1]  # Simulated processing time
                tracker._draw_statistics(output_frame, processing_times)
                
                # Display frame
                cv2.imshow('Real-Time Object Tracking', output_frame)
                
                # Check for key press
                key = cv2.waitKey(1) & 0xFF
                
                if key == ord('q'):
                    break
                elif key == ord('r'):
                    tracker.reset_tracking()
                    print("Tracking reset")
                elif key == ord('s'):
                    filename = f'tracking_data_{int(time.time())}.json'
                    tracker.save_tracking_data(filename)
                    print(f"Tracking data saved: {filename}")
                elif key in [ord(str(i)) for i in range(1, 8)]:
                    # Change tracking method
                    methods = list(TrackingMethod)
                    method_index = int(chr(key)) - 1
                    if method_index < len(methods):
                        tracker.tracking_method = methods[method_index]
                        tracker.reset_tracking()
                        print(f"Tracking method changed to: {methods[method_index].value}")
                
                tracker.frame_count += 1
        
        finally:
            cap.release()
            cv2.destroyAllWindows()
    
    # Show final statistics
    print("\n4. Final Tracking Statistics:")
    stats = tracker.get_tracking_statistics()
    
    for key, value in stats.items():
        print(f"  {key}: {value}")
    
    # Interactive menu
    print("\n=== Object Tracker Interactive ===")
    
    while True:
        print("\nOptions:")
        print("1. Process video file")
        print("2. Change tracking method")
        print("3. Show tracking statistics")
        print("4. Save tracking configuration")
        print("5. Load tracking configuration")
        print("6. Test with sample video")
        print("0. Exit")
        
        choice = input("\nSelect option: ").strip()
        
        if choice == "0":
            break
        
        elif choice == "1":
            video_path = input("Enter video file path: ").strip()
            if os.path.exists(video_path):
                output_path = input("Enter output path (optional): ").strip()
                if not output_path:
                    output_path = None
                
                success = tracker.process_video_file(video_path, output_path)
                if success:
                    print("Video processing completed")
                else:
                    print("Video processing failed")
            else:
                print("Video file not found")
        
        elif choice == "2":
            print("Available tracking methods:")
            for i, method in enumerate(TrackingMethod, 1):
                print(f"  {i}. {method.value}")
            
            try:
                method_choice = int(input("Select method (1-7): ")) - 1
                methods = list(TrackingMethod)
                if 0 <= method_choice < len(methods):
                    tracker.tracking_method = methods[method_choice]
                    tracker.reset_tracking()
                    print(f"Tracking method changed to: {methods[method_choice].value}")
                else:
                    print("Invalid method selection")
            except ValueError:
                print("Invalid input")
        
        elif choice == "3":
            stats = tracker.get_tracking_statistics()
            print("\nTracking Statistics:")
            for key, value in stats.items():
                print(f"  {key}: {value}")
        
        elif choice == "4":
            filename = input("Enter filename: ").strip()
            config_data = {
                'tracking_method': tracker.tracking_method.value,
                'max_lost_frames': tracker.max_lost_frames,
                'min_detection_confidence': tracker.min_detection_confidence,
                'max_objects': tracker.max_objects
            }
            
            with open(filename, 'w') as f:
                json.dump(config_data, f, indent=2)
            
            print(f"Configuration saved: {filename}")
        
        elif choice == "5":
            filename = input("Enter filename: ").strip()
            try:
                with open(filename, 'r') as f:
                    config_data = json.load(f)
                
                tracker.tracking_method = TrackingMethod(config_data['tracking_method'])
                tracker.max_lost_frames = config_data.get('max_lost_frames', 30)
                tracker.min_detection_confidence = config_data.get('min_detection_confidence', 0.5)
                tracker.max_objects = config_data.get('max_objects', 10)
                
                print(f"Configuration loaded: {filename}")
            except Exception as e:
                print(f"Error loading configuration: {e}")
        
        elif choice == "6":
            print("Creating sample video...")
            
            # Create sample video
            width, height = 640, 480
            fourcc = cv2.VideoWriter_fourcc(*'XVID')
            out = cv2.VideoWriter('test_video.avi', fourcc, 20.0, (width, height))
            
            for frame_num in range(50):
                frame = np.zeros((height, width, 3), dtype=np.uint8)
                
                # Add moving objects
                x = int(width * 0.2 + 0.3 * width * np.sin(frame_num * 0.1))
                y = int(height * 0.3 + 0.2 * height * np.cos(frame_num * 0.1))
                cv2.rectangle(frame, (x, y), (x + 80, y + 60), (0, 255, 0), -1)
                
                cx = int(width * 0.7 + 0.2 * width * np.cos(frame_num * 0.08))
                cy = int(height * 0.6 + 0.15 * height * np.sin(frame_num * 0.08))
                cv2.circle(frame, (cx, cy), 30, (255, 0, 0), -1)
                
                out.write(frame)
            
            out.release()
            print("Sample video created: test_video.avi")
            
            # Process the video
            tracker.process_video_file('test_video.avi', 'test_output.avi')
        
        else:
            print("Invalid option")
    
    print("\n=== Object Tracker Demo Completed ===")
    print("Features demonstrated:")
    print("- Real-time object tracking")
    print("- Multiple tracking algorithms (CSRT, KCF, MOSSE, etc.)")
    print("- Object detection and matching")
    print("- Velocity calculation")
    print("- Video file processing")
    print("- Statistics and performance monitoring")
    print("- Interactive controls")
    print("- Data export functionality")
    
    print("\nTracking Algorithms:")
    print("- CSRT: Channel and Spatial Reliability Tracker")
    print("- KCF: Kernelized Correlation Filters")
    print("- MOSSE: Minimum Output Sum of Squared Error")
    print("- Boosting: Adaptive boosting tracker")
    print("- TLD: Tracking-Learning-Detection")
    print("- MedianFlow: Optical flow tracker")
    print("- MIL: Multiple Instance Learning")
    
    print("\nApplications:")
    print("- Surveillance systems")
    print("- Autonomous vehicles")
    print("- Robotics")
    print("- Sports analysis")
    print("- Traffic monitoring")
    print("- Wildlife tracking")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Install dependencies: pip install opencv-python numpy imutils
2. Run tracker: python object_tracker.py
3. Use webcam or video file for tracking
4. Interact with keyboard controls

Key Concepts:
- Object Detection: Finding objects in images
- Object Tracking: Following objects across frames
- Computer Vision: Image processing and analysis
- Real-time Processing: Frame-by-frame analysis
- Kalman Filtering: State estimation (conceptual)
- Feature Matching: Object association

Tracking Algorithms:
- CSRT: High accuracy, good for long-term tracking
- KCF: Fast, good for real-time applications
- MOSSE: Very fast, less accurate
- TLD: Handles occlusions well
- MedianFlow: Good for small motions

Detection Methods:
- Background Subtraction: Motion detection
- YOLO: Deep learning object detection
- Template Matching: Pattern recognition
- Feature Detection: Key point matching

Applications:
- Video surveillance
- Autonomous navigation
- Sports analytics
- Traffic monitoring
- Wildlife tracking
- Industrial automation

Dependencies:
- opencv-python: pip install opencv-python
- numpy: pip install numpy
- imutils: pip install imutils (optional)

Best Practices:
- Choose appropriate tracking algorithm for your use case
- Optimize detection parameters for your environment
- Handle occlusions and object re-identification
- Monitor tracking performance and accuracy
- Use appropriate frame rates for real-time applications
- Implement proper error handling and recovery
- Consider computational resources and optimization
"""
