# Mobile Development

This file contains comprehensive mobile development examples in C, including touch and gesture handling, sensor management, notification systems, device orientation, battery management, mobile UI components, and app lifecycle management.

## 📱 Mobile Development Fundamentals

### 🎯 Mobile Development Concepts
- **Touch Interface**: Multi-touch input and gesture recognition
- **Sensor Integration**: Hardware sensor data processing
- **Device Management**: Battery, orientation, and system integration
- **User Interface**: Mobile-specific UI components and interactions
- **App Lifecycle**: State management and background processing

### 📲 Platform Considerations
- **Cross-Platform**: Code that works across different mobile platforms
- **Performance**: Optimized for mobile hardware constraints
- **Battery Efficiency**: Power-conscious design patterns
- **Touch Optimization**: Responsive and intuitive touch interactions
- **Resource Management**: Efficient memory and CPU usage

## 👆 Touch and Gesture Handling

### Touch Point Structure
```c
// Touch point structure
typedef struct {
    int id;
    float x, y;
    float pressure;
    float size;
    int is_active;
    long timestamp;
} TouchPoint;
```

### Gesture Types
```c
// Gesture types
typedef enum {
    GESTURE_TAP = 0,
    GESTURE_DOUBLE_TAP = 1,
    GESTURE_SWIPE = 2,
    GESTURE_PINCH = 3,
    GESTURE_ROTATE = 4,
    GESTURE_LONG_PRESS = 5,
    GESTURE_PAN = 6,
    GESTURE_SCROLL = 7
} GestureType;
```

### Touch Manager Implementation
```c
// Touch manager
typedef struct {
    TouchPoint active_touches[MAX_TOUCH_POINTS];
    int touch_count;
    Gesture gestures[MAX_GESTURES];
    int gesture_count;
    long tap_timeout;
    float tap_distance_threshold;
    float swipe_velocity_threshold;
    void (*gesture_callback)(Gesture* gesture);
} TouchManager;

// Initialize touch manager
TouchManager* initTouchManager() {
    TouchManager* manager = malloc(sizeof(TouchManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(TouchManager));
    manager->tap_timeout = 300; // 300ms
    manager->tap_distance_threshold = 50.0f;
    manager->swipe_velocity_threshold = 100.0f;
    
    return manager;
}

// Handle touch event
void handleTouchEvent(TouchManager* manager, TouchEvent* event) {
    if (!manager || !event) return;
    
    switch (event->event_type) {
        case 0: // Touch down
            if (manager->touch_count < MAX_TOUCH_POINTS) {
                TouchPoint* touch = &manager->active_touches[manager->touch_count];
                *touch = event->touch;
                touch->is_active = 1;
                manager->touch_count++;
                
                // Check for tap gesture
                checkForTapGesture(manager, touch);
            }
            break;
            
        case 1: // Touch move
            for (int i = 0; i < manager->touch_count; i++) {
                if (manager->active_touches[i].id == event->touch.id) {
                    manager->active_touches[i].x = event->touch.x;
                    manager->active_touches[i].y = event->touch.y;
                    manager->active_touches[i].timestamp = event->timestamp;
                    
                    // Check for swipe/pan gesture
                    checkForSwipeGesture(manager, &manager->active_touches[i]);
                    break;
                }
            }
            break;
            
        case 2: // Touch up
            for (int i = 0; i < manager->touch_count; i++) {
                if (manager->active_touches[i].id == event->touch.id) {
                    manager->active_touches[i].is_active = 0;
                    
                    // Check for gesture completion
                    checkForGestureCompletion(manager, &manager->active_touches[i]);
                    
                    // Remove from active touches
                    for (int j = i; j < manager->touch_count - 1; j++) {
                        manager->active_touches[j] = manager->active_touches[j + 1];
                    }
                    manager->touch_count--;
                    break;
                }
            }
            break;
    }
}

// Check for swipe gesture
void checkForSwipeGesture(TouchManager* manager, TouchPoint* touch) {
    float dx = touch->x - touch->start_x;
    float dy = touch->y - touch->start_y;
    float dt = (touch->timestamp - touch->start_time) / 1000.0f; // Convert to seconds
    
    if (dt > 0) {
        touch->velocity_x = dx / dt;
        touch->velocity_y = dy / dt;
        
        float distance = sqrt(dx * dx + dy * dy);
        float velocity = sqrt(touch->velocity_x * touch->velocity_x + touch->velocity_y * touch->velocity_y);
        
        // Check if this qualifies as a swipe
        if (distance > manager->tap_distance_threshold && velocity > manager->swipe_velocity_threshold) {
            Gesture* gesture = createGesture(GESTURE_SWIPE, touch, 1);
            if (gesture) {
                gesture->end_x = touch->x;
                gesture->end_y = touch->y;
                gesture->end_time = touch->timestamp;
                
                if (manager->gesture_callback) {
                    manager->gesture_callback(gesture);
                }
            }
        }
    }
}

// Check for pinch gesture
void checkForPinchGesture(TouchManager* manager) {
    if (manager->touch_count != 2) return;
    
    TouchPoint* touch1 = &manager->active_touches[0];
    TouchPoint* touch2 = &manager->active_touches[1];
    
    float current_distance = sqrt(
        (touch1->x - touch2->x) * (touch1->x - touch2->x) +
        (touch1->y - touch2->y) * (touch1->y - touch2->y)
    );
    
    // This is a simplified version - in practice, you'd track the distance over time
    static float initial_distance = 0;
    static int pinch_started = 0;
    
    if (!pinch_started) {
        initial_distance = current_distance;
        pinch_started = 1;
    } else {
        float scale = current_distance / initial_distance;
        
        if (fabs(scale - 1.0f) > 0.1f) { // Scale threshold
            Gesture* gesture = createGesture(GESTURE_PINCH, NULL, 2);
            if (gesture) {
                gesture->scale = scale;
                gesture->touch_points[0] = *touch1;
                gesture->touch_points[1] = *touch2;
                
                if (manager->gesture_callback) {
                    manager->gesture_callback(gesture);
                }
            }
        }
    }
}
```

**Touch Handling Benefits**:
- **Multi-touch Support**: Handle multiple simultaneous touch points
- **Gesture Recognition**: Detect common mobile gestures (tap, swipe, pinch, etc.)
- **Customizable Thresholds**: Adjustable sensitivity for different gestures
- **Event-driven Architecture**: Callback-based gesture notification system

## 📡 Sensor Management

### Sensor Types
```c
// Sensor types
typedef enum {
    SENSOR_ACCELEROMETER = 0,
    SENSOR_GYROSCOPE = 1,
    SENSOR_MAGNETOMETER = 2,
    SENSOR_LIGHT = 3,
    SENSOR_PROXIMITY = 4,
    SENSOR_TEMPERATURE = 5,
    SENSOR_HUMIDITY = 6,
    SENSOR_PRESSURE = 7,
    SENSOR_ORIENTATION = 8,
    SENSOR_GRAVITY = 9,
    SENSOR_ROTATION_VECTOR = 10
} SensorType;
```

### Sensor Data Structure
```c
// Sensor data structure
typedef struct {
    SensorType type;
    float values[4]; // Up to 4 values for different sensors
    long timestamp;
    int is_active;
    float accuracy;
} SensorData;
```

### Sensor Manager Implementation
```c
// Sensor manager
typedef struct {
    SensorData sensors[MAX_SENSORS];
    int sensor_count;
    int active_sensors;
    float sensor_update_rate;
    void (*sensor_callback)(SensorData* sensor);
} SensorManager;

// Initialize sensor manager
SensorManager* initSensorManager() {
    SensorManager* manager = malloc(sizeof(SensorManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(SensorManager));
    manager->sensor_update_rate = 60.0f; // 60 Hz
    
    return manager;
}

// Update sensor data
void updateSensorData(SensorManager* manager, SensorType type, float* values, int value_count) {
    if (!manager || !values || value_count <= 0 || value_count > 4) return;
    
    // Find existing sensor or create new one
    SensorData* sensor = NULL;
    for (int i = 0; i < manager->sensor_count; i++) {
        if (manager->sensors[i].type == type) {
            sensor = &manager->sensors[i];
            break;
        }
    }
    
    if (!sensor && manager->sensor_count < MAX_SENSORS) {
        sensor = &manager->sensors[manager->sensor_count++];
        sensor->type = type;
        sensor->is_active = 1;
    }
    
    if (sensor) {
        for (int i = 0; i < value_count; i++) {
            sensor->values[i] = values[i];
        }
        sensor->timestamp = time(NULL);
        
        if (manager->sensor_callback) {
            manager->sensor_callback(sensor);
        }
    }
}

// Simulate accelerometer data
void simulateAccelerometer(SensorManager* manager) {
    float values[3];
    
    // Simulate device tilt
    float tilt_x = sin(time(NULL) * 0.1) * 9.8;
    float tilt_y = cos(time(NULL) * 0.1) * 9.8;
    float z = 9.8; // Gravity
    
    values[0] = tilt_x;
    values[1] = tilt_y;
    values[2] = z;
    
    updateSensorData(manager, SENSOR_ACCELEROMETER, values, 3);
}

// Simulate gyroscope data
void simulateGyroscope(SensorManager* manager) {
    float values[3];
    
    // Simulate rotation
    float rotation_x = sin(time(NULL) * 0.2) * 2.0;
    float rotation_y = cos(time(NULL) * 0.2) * 2.0;
    float rotation_z = sin(time(NULL) * 0.3) * 1.0;
    
    values[0] = rotation_x;
    values[1] = rotation_y;
    values[2] = rotation_z;
    
    updateSensorData(manager, SENSOR_GYROSCOPE, values, 3);
}
```

**Sensor Management Benefits**:
- **Multi-sensor Support**: Handle various hardware sensors
- **Real-time Updates**: High-frequency sensor data processing
- **Callback System**: Event-driven sensor data notification
- **Flexible Data Types**: Support for different sensor value formats

## 🔔 Notification System

### Notification Types
```c
// Notification types
typedef enum {
    NOTIFICATION_INFO = 0,
    NOTIFICATION_WARNING = 1,
    NOTIFICATION_ERROR = 2,
    NOTIFICATION_SUCCESS = 3,
    NOTIFICATION_PROMPT = 4
} NotificationType;
```

### Notification Structure
```c
// Notification structure
typedef struct {
    int id;
    char title[256];
    char message[1024];
    NotificationType type;
    char icon_path[512];
    int auto_dismiss;
    int dismiss_time;
    long timestamp;
    int is_active;
} Notification;
```

### Notification Manager Implementation
```c
// Notification manager
typedef struct {
    Notification notifications[MAX_NOTIFICATIONS];
    int notification_count;
    int next_id;
    void (*notification_callback)(Notification* notification);
} NotificationManager;

// Initialize notification manager
NotificationManager* initNotificationManager() {
    NotificationManager* manager = malloc(sizeof(NotificationManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(NotificationManager));
    manager->next_id = 1;
    
    return manager;
}

// Create notification
int createNotification(NotificationManager* manager, const char* title, const char* message, NotificationType type) {
    if (!manager || !title || !message || manager->notification_count >= MAX_NOTIFICATIONS) {
        return -1;
    }
    
    Notification* notification = &manager->notifications[manager->notification_count];
    notification->id = manager->next_id++;
    strncpy(notification->title, title, sizeof(notification->title) - 1);
    strncpy(notification->message, message, sizeof(notification->message) - 1);
    notification->type = type;
    notification->timestamp = time(NULL);
    notification->is_active = 1;
    notification->auto_dismiss = (type == NOTIFICATION_INFO);
    notification->dismiss_time = 5; // 5 seconds for auto-dismiss
    
    manager->notification_count++;
    
    if (manager->notification_callback) {
        manager->notification_callback(notification);
    }
    
    return notification->id;
}

// Dismiss notification
int dismissNotification(NotificationManager* manager, int notification_id) {
    if (!manager) return -1;
    
    for (int i = 0; i < manager->notification_count; i++) {
        if (manager->notifications[i].id == notification_id && manager->notifications[i].is_active) {
            manager->notifications[i].is_active = 0;
            return 0;
        }
    }
    
    return -1;
}

// Update notifications (check auto-dismiss)
void updateNotifications(NotificationManager* manager) {
    if (!manager) return;
    
    long current_time = time(NULL);
    
    for (int i = 0; i < manager->notification_count; i++) {
        Notification* notification = &manager->notifications[i];
        
        if (notification->is_active && notification->auto_dismiss) {
            if (current_time - notification->timestamp >= notification->dismiss_time) {
                notification->is_active = 0;
            }
        }
    }
}
```

**Notification Benefits**:
- **Multiple Types**: Support for different notification categories
- **Auto-dismiss**: Automatic notification dismissal for certain types
- **Callback System**: Event-driven notification handling
- **Rich Content**: Support for titles, messages, and icons

## 📱 Device Orientation

### Orientation Types
```c
// Orientation types
typedef enum {
    ORIENTATION_PORTRAIT = 0,
    ORIENTATION_LANDSCAPE = 1,
    ORIENTATION_PORTRAIT_UPSIDE_DOWN = 2,
    ORIENTATION_LANDSCAPE_LEFT = 3,
    ORIENTATION_UNKNOWN = 4
} DeviceOrientation;
```

### Orientation Manager Implementation
```c
// Orientation manager
typedef struct {
    DeviceOrientation current_orientation;
    DeviceOrientation preferred_orientation;
    float rotation_angle;
    int auto_rotate;
    void (*orientation_callback)(DeviceOrientation orientation);
} OrientationManager;

// Initialize orientation manager
OrientationManager* initOrientationManager() {
    OrientationManager* manager = malloc(sizeof(OrientationManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(OrientationManager));
    manager->current_orientation = ORIENTATION_PORTRAIT;
    manager->preferred_orientation = ORIENTATION_PORTRAIT;
    manager->auto_rotate = 1;
    
    return manager;
}

// Update orientation based on sensor data
void updateOrientation(OrientationManager* manager, SensorManager* sensor_manager) {
    if (!manager || !sensor_manager) return;
    
    // Get accelerometer data
    SensorData* accel_data = NULL;
    for (int i = 0; i < sensor_manager->sensor_count; i++) {
        if (sensor_manager->sensors[i].type == SENSOR_ACCELEROMETER) {
            accel_data = &sensor_manager->sensors[i];
            break;
        }
    }
    
    if (!accel_data) return;
    
    float ax = accel_data->values[0];
    float ay = accel_data->values[1];
    float az = accel_data->values[2];
    
    // Determine orientation based on gravity vector
    DeviceOrientation new_orientation = ORIENTATION_UNKNOWN;
    
    if (fabs(az) > 8.0) { // Device is upright
        if (fabs(ax) < 2.0 && fabs(ay) < 2.0) {
            new_orientation = ORIENTATION_PORTRAIT;
        } else if (fabs(ax) > 8.0) {
            new_orientation = ax > 0 ? ORIENTATION_LANDSCAPE_LEFT : ORIENTATION_LANDSCAPE;
        } else if (fabs(ay) > 8.0) {
            new_orientation = ay > 0 ? ORIENTATION_LANDSCAPE : ORIENTATION_LANDSCAPE_LEFT;
        }
    }
    
    if (new_orientation != ORIENTATION_UNKNOWN && new_orientation != manager->current_orientation) {
        DeviceOrientation old_orientation = manager->current_orientation;
        manager->current_orientation = new_orientation;
        
        if (manager->orientation_callback) {
            manager->orientation_callback(new_orientation);
        }
    }
}

// Set preferred orientation
void setPreferredOrientation(OrientationManager* manager, DeviceOrientation orientation) {
    if (!manager) return;
    
    manager->preferred_orientation = orientation;
    
    // If auto-rotate is disabled, immediately switch to preferred orientation
    if (!manager->auto_rotate && manager->current_orientation != orientation) {
        manager->current_orientation = orientation;
        if (manager->orientation_callback) {
            manager->orientation_callback(orientation);
        }
    }
}
```

**Orientation Benefits**:
- **Automatic Detection**: Sensor-based orientation detection
- **User Preference**: Support for locked orientation modes
- **Smooth Transitions**: Callback-based orientation change notifications
- **Multi-orientation Support**: Handle all device orientations

## 🔋 Battery Management

### Battery Status Structure
```c
// Battery status
typedef struct {
    float level; // 0.0 to 1.0
    int is_charging;
    int is_power_saving_mode;
    int battery_health; // 0-100%
    long estimated_time_remaining;
} BatteryStatus;
```

### Battery Manager Implementation
```c
// Battery manager
typedef struct {
    BatteryStatus current_status;
    int low_battery_threshold;
    int critical_battery_threshold;
    void (*battery_callback)(BatteryStatus* status);
} BatteryManager;

// Initialize battery manager
BatteryManager* initBatteryManager() {
    BatteryManager* manager = malloc(sizeof(BatteryManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(BatteryManager));
    manager->low_battery_threshold = 20; // 20%
    manager->critical_battery_threshold = 5; // 5%
    
    return manager;
}

// Update battery status
void updateBatteryStatus(BatteryManager* manager) {
    if (!manager) return;
    
    // Simulate battery drain
    static float battery_level = 100.0f;
    battery_level -= 0.1f; // Drain 0.1%
    
    if (battery_level < 0) battery_level = 0;
    
    manager->current_status.level = battery_level;
    manager->current_status.is_charging = (battery_level > 50); // Simulate charging
    manager->current_status.battery_health = 100 - (100 - battery_level) * 0.5; // Health degrades with level
    manager->current_status.estimated_time_remaining = battery_level * 3600; // 1 hour per percent
    
    // Check for low battery
    if (battery_level <= manager->critical_battery_threshold) {
        manager->current_status.is_power_saving_mode = 1;
    } else if (battery_level <= manager->low_battery_threshold) {
        manager->current_status.is_power_saving_mode = 0;
    }
    
    if (manager->battery_callback) {
        manager->battery_callback(&manager->current_status);
    }
}
```

**Battery Benefits**:
- **Real-time Monitoring**: Continuous battery status tracking
- **Power Saving**: Automatic power-saving mode activation
- **Battery Health**: Battery health monitoring and reporting
- **Time Estimation**: Remaining usage time calculations

## 🎨 Mobile UI Components

### UI Component Types
```c
// UI component types
typedef enum {
    UI_BUTTON = 0,
    UI_LABEL = 1,
    UI_TEXT_FIELD = 2,
    UI_IMAGE = 3,
    UI_SCROLL_VIEW = 4,
    UI_LIST_VIEW = 5,
    UI_SWITCH = 6,
    UI_SLIDER = 7,
    UI_PROGRESS_BAR = 8,
    UI_ALERT = 9
} UIComponentType;
```

### UI Component Structure
```c
// UI component structure
typedef struct {
    UIComponentType type;
    float x, y;
    float width, height;
    char text[256];
    char image_path[512];
    int is_visible;
    int is_enabled;
    int is_active;
    void (*click_callback)(void* component);
    void* user_data;
} UIComponent;
```

### UI Manager Implementation
```c
// UI manager
typedef struct {
    UIComponent components[MAX_GESTURES];
    int component_count;
    UIComponent* focused_component;
    int keyboard_visible;
    float keyboard_height;
} UIManager;

// Initialize UI manager
UIManager* initUIManager() {
    UIManager* manager = malloc(sizeof(UIManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(UIManager));
    manager->keyboard_visible = 0;
    manager->keyboard_height = 0;
    
    return manager;
}

// Create UI component
UIComponent* createUIComponent(UIComponentType type, float x, float y, float width, float height) {
    UIComponent* component = malloc(sizeof(UIComponent));
    if (!component) return NULL;
    
    memset(component, 0, sizeof(UIComponent));
    component->type = type;
    component->x = x;
    component->y = y;
    component->width = width;
    component->height = height;
    component->is_visible = 1;
    component->is_enabled = 1;
    component->is_active = 0;
    
    return component;
}

// Handle touch on UI component
int handleUITouch(UIManager* manager, float x, float y) {
    if (!manager) return 0;
    
    for (int i = 0; i < manager->component_count; i++) {
        UIComponent* component = &manager->components[i];
        
        if (component->is_visible && component->is_enabled) {
            // Check if touch is within component bounds
            if (x >= component->x && x <= component->x + component->width &&
                y >= component->y && y <= component->y + component->height) {
                
                // Trigger click callback
                if (component->click_callback) {
                    component->click_callback(component);
                }
                
                // Set as focused component
                manager->focused_component = component;
                return 1;
            }
        }
    }
    
    return 0;
}

// Show keyboard
void showKeyboard(UIManager* manager, float height) {
    if (!manager) return;
    
    manager->keyboard_visible = 1;
    manager->keyboard_height = height;
    
    // Adjust UI layout for keyboard
    adjustUILayoutForKeyboard(manager);
}

// Hide keyboard
void hideKeyboard(UIManager* manager) {
    if (!manager) return;
    
    manager->keyboard_visible = 0;
    manager->keyboard_height = 0;
    
    // Restore UI layout
    restoreUILayout(manager);
}
```

**UI Components Benefits**:
- **Mobile-specific**: Designed for touch interfaces
- **Keyboard Support**: Automatic keyboard management for text input
- **Touch Handling**: Built-in touch event processing
- **Flexible Layout**: Dynamic UI adjustment for different screen sizes

## 🔄 App Lifecycle Management

### App States
```c
// App states
typedef enum {
    APP_STATE_INITIALIZING = 0,
    APP_STATE_RUNNING = 1,
    APP_STATE_PAUSED = 2,
    APP_STATE_BACKGROUND = 3,
    APP_STATE_SUSPENDED = 4,
    APP_STATE_TERMINATED = 5
} AppState;
```

### App Lifecycle Manager Implementation
```c
// App lifecycle manager
typedef struct {
    AppState current_state;
    AppState previous_state;
    long state_change_time;
    void (*state_callback)(AppState state);
    int auto_save_on_suspend;
    int handle_background_tasks;
} AppLifecycleManager;

// Initialize app lifecycle manager
AppLifecycleManager* initAppLifecycleManager() {
    AppLifecycleManager* manager = malloc(sizeof(AppLifecycleManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(AppLifecycleManager));
    manager->current_state = APP_STATE_INITIALIZING;
    manager->auto_save_on_suspend = 1;
    manager->handle_background_tasks = 1;
    manager->state_change_time = time(NULL);
    
    return manager;
}

// Change app state
void changeAppState(AppLifecycleManager* manager, AppState new_state) {
    if (!manager || new_state == manager->current_state) return;
    
    AppState old_state = manager->current_state;
    manager->previous_state = old_state;
    manager->current_state = new_state;
    manager->state_change_time = time(NULL);
    
    // Handle state transitions
    switch (new_state) {
        case APP_STATE_PAUSED:
            if (manager->auto_save_on_suspend) {
                saveAppState(manager);
            }
            break;
            
        case APP_STATE_BACKGROUND:
            if (manager->handle_background_tasks) {
                startBackgroundTasks(manager);
            }
            break;
            
        case APP_STATE_SUSPENDED:
            stopAllTasks(manager);
            break;
            
        case APP_STATE_RUNNING:
            if (old_state == APP_STATE_PAUSED) {
                restoreAppState(manager);
            }
            break;
            
        case APP_STATE_TERMINATED:
            cleanupApp(manager);
            break;
            
        default:
            break;
    }
    
    if (manager->state_callback) {
        manager->state_callback(new_state);
    }
}

// Save app state
void saveAppState(AppLifecycleManager* manager) {
    // Save user data, current screen state, etc.
    printf("Saving app state...\n");
}

// Restore app state
void restoreAppState(AppLifecycleManager* manager) {
    // Restore user data, screen state, etc.
    printf("Restoring app state...\n");
}

// Start background tasks
void startBackgroundTasks(AppLifecycleManager* manager) {
    // Start background processing, downloads, etc.
    printf("Starting background tasks...\n");
}

// Stop all tasks
void stopAllTasks(AppLifecycleManager* manager) {
    // Stop all running tasks
    printf("Stopping all tasks...\n");
}

// Cleanup app
void cleanupApp(AppLifecycleManager* manager) {
    // Clean up resources
    printf("Cleaning up app...\n");
}
```

**App Lifecycle Benefits**:
- **State Management**: Complete app lifecycle handling
- **Auto-save**: Automatic state preservation on suspension
- **Background Tasks**: Support for background processing
- **Resource Management**: Proper cleanup on termination

## 🔧 Best Practices

### 1. Touch Event Handling
```c
// Good: Efficient touch event processing
void handleTouchEvent(TouchManager* manager, TouchEvent* event) {
    if (!manager || !event) return;
    
    // Early validation
    if (event->touch.id < 0 || event->touch.x < 0 || event->touch.y < 0) {
        return;
    }
    
    // Process event efficiently
    switch (event->event_type) {
        case 0: // Touch down
            if (manager->touch_count < MAX_TOUCH_POINTS) {
                // Add touch efficiently
                manager->active_touches[manager->touch_count++] = event->touch;
            }
            break;
        // ... other cases
    }
}

// Bad: Inefficient event handling
void handleTouchEventSlow(TouchManager* manager, TouchEvent* event) {
    // No validation
    // Linear search for existing touches
    for (int i = 0; i < MAX_TOUCH_POINTS; i++) {
        // Inefficient processing
        if (manager->active_touches[i].id == event->touch.id) {
            // Do something
        }
    }
}
```

### 2. Sensor Data Processing
```c
// Good: Efficient sensor data handling
void updateSensorData(SensorManager* manager, SensorType type, float* values, int value_count) {
    if (!manager || !values || value_count <= 0 || value_count > 4) return;
    
    // Find sensor efficiently
    SensorData* sensor = findSensorByType(manager, type);
    if (!sensor && manager->sensor_count < MAX_SENSORS) {
        sensor = &manager->sensors[manager->sensor_count++];
        sensor->type = type;
        sensor->is_active = 1;
    }
    
    if (sensor) {
        // Batch copy values
        memcpy(sensor->values, values, value_count * sizeof(float));
        sensor->timestamp = time(NULL);
        
        // Notify callback
        if (manager->sensor_callback) {
            manager->sensor_callback(sensor);
        }
    }
}

// Bad: Inefficient sensor handling
void updateSensorDataSlow(SensorManager* manager, SensorType type, float* values, int value_count) {
    // Linear search every time
    for (int i = 0; i < manager->sensor_count; i++) {
        if (manager->sensors[i].type == type) {
            // Copy values one by one
            for (int j = 0; j < value_count; j++) {
                manager->sensors[i].values[j] = values[j];
            }
        }
    }
}
```

### 3. Memory Management
```c
// Good: Proper memory management
TouchManager* initTouchManager() {
    TouchManager* manager = malloc(sizeof(TouchManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(TouchManager));
    manager->tap_timeout = 300;
    manager->tap_distance_threshold = 50.0f;
    manager->swipe_velocity_threshold = 100.0f;
    
    return manager;
}

void freeTouchManager(TouchManager* manager) {
    if (!manager) return;
    
    // Clean up gestures
    for (int i = 0; i < manager->gesture_count; i++) {
        free(manager->gestures[i].touch_points);
    }
    
    free(manager);
}

// Bad: Memory leaks
TouchManager* initTouchManagerLeaky() {
    TouchManager* manager = malloc(sizeof(TouchManager));
    // No error checking
    return manager; // Memory leak if malloc fails
}
```

### 4. Battery Optimization
```c
// Good: Battery-efficient design
void updateSensorsBatteryEfficient(SensorManager* manager) {
    // Check battery level before updating sensors
    BatteryStatus* battery = getCurrentBatteryStatus();
    if (battery && battery->level < 0.2f) {
        // Reduce sensor update rate in low battery
        manager->sensor_update_rate = 30.0f; // Reduce to 30 Hz
    } else {
        manager->sensor_update_rate = 60.0f; // Normal 60 Hz
    }
    
    // Update sensors only if needed
    if (shouldUpdateSensors(manager)) {
        updateAllSensors(manager);
    }
}

// Bad: Battery-inefficient design
void updateSensorsBatteryWasteful(SensorManager* manager) {
    // Always update at maximum rate
    manager->sensor_update_rate = 120.0f; // Always 120 Hz
    
    // Update all sensors regardless of battery level
    updateAllSensors(manager);
}
```

### 5. Error Handling
```c
// Good: Comprehensive error handling
int createNotification(NotificationManager* manager, const char* title, const char* message, NotificationType type) {
    if (!manager || !title || !message) {
        return -1; // Invalid parameters
    }
    
    if (manager->notification_count >= MAX_NOTIFICATIONS) {
        return -2; // Too many notifications
    }
    
    if (strlen(title) > 255 || strlen(message) > 1023) {
        return -3; // Text too long
    }
    
    // Create notification safely
    Notification* notification = &manager->notifications[manager->notification_count];
    notification->id = manager->next_id++;
    strncpy(notification->title, title, sizeof(notification->title) - 1);
    strncpy(notification->message, message, sizeof(notification->message) - 1);
    notification->type = type;
    notification->timestamp = time(NULL);
    notification->is_active = 1;
    
    manager->notification_count++;
    
    if (manager->notification_callback) {
        manager->notification_callback(notification);
    }
    
    return notification->id;
}

// Bad: No error handling
int createNotificationUnsafe(NotificationManager* manager, const char* title, const char* message, NotificationType type) {
    // No validation - can cause buffer overflows
    strcpy(manager->notifications[manager->notification_count].title, title);
    strcpy(manager->notifications[manager->notification_count].message, message);
    // No bounds checking
    manager->notification_count++;
    return manager->next_id++;
}
```

## ⚠️ Common Pitfalls

### 1. Touch Event Conflicts
```c
// Wrong: Touch events not properly managed
void handleTouchConflict(TouchManager* manager, TouchEvent* event) {
    // Process event without checking existing touches
    // Can cause duplicate touch IDs or missed gestures
}

// Right: Proper touch event management
void handleTouchCorrect(TouchManager* manager, TouchEvent* event) {
    // Check if touch ID already exists
    if (isTouchIdActive(manager, event->touch.id)) {
        updateExistingTouch(manager, event);
    } else {
        addNewTouch(manager, event);
    }
}
```

### 2. Sensor Overuse
```c
// Wrong: Sensors always active at maximum rate
void alwaysUpdateSensors(SensorManager* manager) {
    while (1) {
        updateAllSensors(manager); // Wastes battery
        usleep(1000); // 1ms update rate
    }
}

// Right: Adaptive sensor updates
void adaptiveSensorUpdate(SensorManager* manager) {
    float update_rate = getOptimalUpdateRate(manager);
    updateAllSensors(manager);
    usleep((int)(1000000.0 / update_rate));
}
```

### 3. Memory Leaks
```c
// Wrong: Memory not freed
void createAndForget() {
    TouchManager* manager = initTouchManager();
    // Use manager but forget to free
    // Memory leak!
}

// Right: Proper cleanup
void createAndCleanup() {
    TouchManager* manager = initTouchManager();
    // Use manager
    freeTouchManager(manager); // Proper cleanup
}
```

### 4. UI Thread Blocking
```c
// Wrong: Blocking operations on UI thread
void uiThreadBlocking() {
    // Long-running operation blocks UI
    performHeavyComputation();
}

// Right: Use background threads
void uiThreadNonBlocking() {
    // Move heavy computation to background
    startBackgroundTask(performHeavyComputation);
}
```

## 🔧 Real-World Applications

### 1. Mobile Game
```c
// Game touch handling
void handleGameTouch(TouchManager* manager, TouchEvent* event) {
    switch (event->event_type) {
        case 0: // Touch down
            if (event->touch.y < SCREEN_HEIGHT / 2) {
                // Upper half - jump action
                playerJump();
            } else {
                // Lower half - shoot action
                playerShoot();
            }
            break;
            
        case 1: // Touch move
            // Update player position
            updatePlayerPosition(event->touch.x, event->touch.y);
            break;
    }
}
```

### 2. Fitness App
```c
// Fitness sensor processing
void processFitnessSensors(SensorManager* manager) {
    // Get accelerometer data for step counting
    SensorData* accel = getSensorData(manager, SENSOR_ACCELEROMETER);
    if (accel) {
        int steps = countSteps(accel->values);
        updateStepCounter(steps);
    }
    
    // Get gyroscope data for exercise form
    SensorData* gyro = getSensorData(manager, SENSOR_GYROSCOPE);
    if (gyro) {
        analyzeExerciseForm(gyro->values);
    }
}
```

### 3. Navigation App
```c
// Navigation sensor fusion
void updateNavigation(SensorManager* manager) {
    // Combine accelerometer and gyroscope for dead reckoning
    SensorData* accel = getSensorData(manager, SENSOR_ACCELEROMETER);
    SensorData* gyro = getSensorData(manager, SENSOR_GYROSCOPE);
    SensorData* magnet = getSensorData(manager, SENSOR_MAGNETOMETER);
    
    if (accel && gyro && magnet) {
        // Sensor fusion for accurate positioning
        updatePosition(accel->values, gyro->values, magnet->values);
    }
}
```

### 4. Social Media App
```c
// Social media gesture handling
void handleSocialGestures(Gesture* gesture) {
    switch (gesture->type) {
        case GESTURE_SWIPE_LEFT:
            // Swipe left - next post
            showNextPost();
            break;
            
        case GESTURE_SWIPE_RIGHT:
            // Swipe right - previous post
            showPreviousPost();
            break;
            
        case GESTURE_DOUBLE_TAP:
            // Double tap - like post
            likeCurrentPost();
            break;
            
        case GESTURE_PINCH:
            // Pinch - zoom image
            zoomImage(gesture->scale);
            break;
    }
}
```

## 📚 Further Reading

### Books
- "Mobile Application Development" by various authors
- "Touch Interface Design" by Dan Saffer
- "Mobile UX Design" by various authors
- "Sensor Programming for Mobile Devices" by various authors

### Topics
- Cross-platform mobile development frameworks
- Mobile security and privacy
- Performance optimization for mobile devices
- Mobile app monetization strategies
- Augmented reality on mobile devices
- Mobile cloud computing

Mobile development in C provides the foundation for creating high-performance, efficient, and user-friendly mobile applications. Master these techniques to build robust mobile apps that work seamlessly across different devices and platforms!
