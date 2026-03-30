# IoT Development

This file contains comprehensive IoT (Internet of Things) development examples in C, including device management, sensor data collection, actuator control, communication protocols, data processing, edge computing, and security.

## 🌐 IoT Fundamentals

### 🎯 IoT Concepts
- **Connected Devices**: Smart devices with sensing and actuation capabilities
- **Data Collection**: Gathering environmental and operational data
- **Remote Control**: Controlling devices from anywhere
- **Real-time Processing**: Immediate data analysis and response
- **Edge Computing**: Processing data close to the source
- **Cloud Integration**: Connecting to cloud services for storage and analytics

### 🏗️ IoT Architecture
- **Device Layer**: Physical sensors and actuators
- **Gateway Layer**: Protocol translation and aggregation
- **Edge Layer**: Local processing and decision making
- **Cloud Layer**: Centralized storage and analytics
- **Application Layer**: User interfaces and applications

## 🔧 Device Management

### Device Types
```c
// Device types
typedef enum {
    DEVICE_SENSOR = 0,
    DEVICE_ACTUATOR = 1,
    DEVICE_GATEWAY = 2,
    DEVICE_EDGE = 3,
    DEVICE_CONTROLLER = 4
} DeviceType;
```

### Device States
```c
// Device states
typedef enum {
    DEVICE_OFFLINE = 0,
    DEVICE_ONLINE = 1,
    DEVICE_ERROR = 2,
    DEVICE_MAINTENANCE = 3,
    DEVICE_UPDATING = 4
} DeviceState;
```

### Device Structure
```c
// Device structure
typedef struct {
    int device_id;
    char name[64];
    char manufacturer[32];
    char model[32];
    char firmware_version[16];
    DeviceType type;
    DeviceState state;
    ConnectionType connection_type;
    char ip_address[16];
    int port;
    char mac_address[18];
    time_t last_seen;
    time_t last_update;
    double battery_level;
    int signal_strength;
    char* configuration;
    int is_configured;
    char* location;
    char* owner;
} IoTDevice;
```

### Device Implementation
```c
// Create IoT device
IoTDevice* createIoTDevice(int device_id, const char* name, DeviceType type, const char* manufacturer, const char* model) {
    IoTDevice* device = malloc(sizeof(IoTDevice));
    if (!device) return NULL;
    
    memset(device, 0, sizeof(IoTDevice));
    device->device_id = device_id;
    strncpy(device->name, name, sizeof(device->name) - 1);
    device->type = type;
    strncpy(device->manufacturer, manufacturer, sizeof(device->manufacturer) - 1);
    strncpy(device->model, model, sizeof(device->model) - 1);
    device->state = DEVICE_OFFLINE;
    device->connection_type = CONN_WIFI;
    device->last_seen = time(NULL);
    device->battery_level = 100.0;
    device->signal_strength = -50;
    device->is_configured = 0;
    
    // Generate MAC address
    sprintf(device->mac_address, "%02x:%02x:%02x:%02x:%02x:%02x",
            rand() % 256, rand() % 256, rand() % 256, rand() % 256, rand() % 256, rand() % 256);
    
    return device;
}

// Update device status
void updateDeviceStatus(IoTDevice* device, DeviceState state, double battery_level, int signal_strength) {
    if (!device) return;
    
    device->state = state;
    device->battery_level = battery_level;
    device->signal_strength = signal_strength;
    device->last_seen = time(NULL);
    
    printf("Device %s status updated: State=%d, Battery=%.1f%%, Signal=%d dBm\n",
           device->name, state, battery_level, signal_strength);
}

// Configure device
int configureDevice(IoTDevice* device, const char* configuration) {
    if (!device || !configuration) return -1;
    
    if (device->configuration) {
        free(device->configuration);
    }
    
    device->configuration = strdup(configuration);
    device->is_configured = 1;
    device->last_update = time(NULL);
    
    printf("Device %s configured\n", device->name);
    return 0;
}

// Check device health
int checkDeviceHealth(IoTDevice* device) {
    if (!device) return 0;
    
    time_t current_time = time(NULL);
    
    // Check if device is offline
    if (current_time - device->last_seen > 300) { // 5 minutes
        device->state = DEVICE_OFFLINE;
        return 0;
    }
    
    // Check battery level
    if (device->battery_level < 10.0) {
        printf("Device %s: Low battery warning (%.1f%%)\n", device->name, device->battery_level);
    }
    
    // Check signal strength
    if (device->signal_strength < -80) {
        printf("Device %s: Poor signal (%d dBm)\n", device->name, device->signal_strength);
    }
    
    return 1;
}
```

**Device Management Benefits**:
- **Centralized Control**: Manage all devices from a single interface
- **Health Monitoring**: Track device status and performance
- **Remote Configuration**: Update device settings remotely
- **Auto-discovery**: Automatically detect and provision new devices

## 📡 Sensor Management

### Sensor Types
```c
// Sensor types
typedef enum {
    SENSOR_TEMPERATURE = 0,
    SENSOR_HUMIDITY = 1,
    SENSOR_PRESSURE = 2,
    SENSOR_LIGHT = 3,
    SENSOR_MOTION = 4,
    SENSOR_SOUND = 5,
    SENSOR_GAS = 6,
    SENSOR_PROXIMITY = 7,
    SENSOR_ACCELEROMETER = 8,
    SENSOR_GYROSCOPE = 9,
    SENSOR_MAGNETOMETER = 10,
    SENSOR_PH = 11,
    SENSOR_EC = 12,
    SENSOR_ORP = 13,
    SENSOR_TURBIDITY = 14,
    SENSOR_FLOW = 15
} SensorType;
```

### Sensor Reading Structure
```c
// Sensor reading structure
typedef struct {
    int sensor_id;
    SensorType type;
    double value;
    DataUnit unit;
    time_t timestamp;
    int device_id;
    char* location;
    int is_valid;
    double accuracy;
    double precision;
    char* metadata;
} SensorReading;
```

### Sensor Implementation
```c
// Create sensor
Sensor* createSensor(int sensor_id, const char* name, SensorType type, DataUnit unit, double min_value, double max_value) {
    Sensor* sensor = malloc(sizeof(Sensor));
    if (!sensor) return NULL;
    
    memset(sensor, 0, sizeof(Sensor));
    sensor->sensor_id = sensor_id;
    strncpy(sensor->name, name, sizeof(sensor->name) - 1);
    sensor->type = type;
    sensor->unit = unit;
    sensor->min_value = min_value;
    sensor->max_value = max_value;
    sensor->accuracy = 0.1;
    sensor->precision = 0.01;
    sensor->sampling_rate = 1; // 1 Hz default
    sensor->is_active = 1;
    sensor->is_calibrated = 0;
    
    return sensor;
}

// Read sensor value
SensorReading* readSensor(Sensor* sensor, int device_id) {
    if (!sensor || !sensor->is_active) return NULL;
    
    SensorReading* reading = malloc(sizeof(SensorReading));
    if (!reading) return NULL;
    
    memset(reading, 0, sizeof(SensorReading));
    reading->sensor_id = sensor->sensor_id;
    reading->type = sensor->type;
    reading->unit = sensor->unit;
    reading->timestamp = time(NULL);
    reading->device_id = device_id;
    reading->is_valid = 1;
    reading->accuracy = sensor->accuracy;
    reading->precision = sensor->precision;
    
    // Simulate sensor reading based on type
    switch (sensor->type) {
        case SENSOR_TEMPERATURE:
            reading->value = 20.0 + (rand() % 30); // 20-50°C
            break;
        case SENSOR_HUMIDITY:
            reading->value = 30.0 + (rand() % 60); // 30-90%
            break;
        case SENSOR_PRESSURE:
            reading->value = 980.0 + (rand() % 100); // 980-1080 hPa
            break;
        case SENSOR_LIGHT:
            reading->value = 100.0 + (rand() % 900); // 100-1000 lux
            break;
        case SENSOR_MOTION:
            reading->value = rand() % 2; // 0 or 1
            break;
        default:
            reading->value = rand() % 100; // Default value
            break;
    }
    
    // Clamp value to sensor range
    if (reading->value < sensor->min_value) {
        reading->value = sensor->min_value;
    } else if (reading->value > sensor->max_value) {
        reading->value = sensor->max_value;
    }
    
    sensor->last_reading = reading->timestamp;
    
    // Add to sensor's reading history
    if (sensor->reading_count < MAX_DATA_POINTS) {
        sensor->readings[sensor->reading_count++] = *reading;
    }
    
    return reading;
}

// Calibrate sensor
int calibrateSensor(Sensor* sensor, double reference_value) {
    if (!sensor) return -1;
    
    // Simple calibration - adjust accuracy
    SensorReading* reading = readSensor(sensor, 0);
    if (!reading) return -1;
    
    double error = reading->value - reference_value;
    sensor->accuracy = fabs(error);
    sensor->is_calibrated = 1;
    
    printf("Sensor %s calibrated: Reference=%.2f, Reading=%.2f, Error=%.2f\n",
           sensor->name, reference_value, reading->value, error);
    
    free(reading);
    return 0;
}
```

**Sensor Benefits**:
- **Data Collection**: Gather environmental and operational data
- **Real-time Monitoring**: Continuous data streaming and analysis
- **Accuracy Control**: Calibration and validation of sensor readings
- **Multi-sensor Support**: Handle various sensor types and units

## ⚙️ Actuator Management

### Actuator Types
```c
// Actuator types
typedef enum {
    ACTUATOR_RELAY = 0,
    ACTUATOR_MOTOR = 1,
    ACTUATOR_SERVO = 2,
    ACTUATOR_LED = 3,
    ACTUATOR_BUZZER = 4,
    ACTUATOR_DISPLAY = 5,
    ACTUATOR_VALVE = 6,
    ACTUATOR_PUMP = 7,
    ACTUATOR_FAN = 8,
    ACTUATOR_HEATER = 9,
    ACTUATOR_COOLER = 10
} ActuatorType;
```

### Actuator Command Structure
```c
// Actuator command structure
typedef struct {
    int actuator_id;
    ActuatorType type;
    ActuatorState target_state;
    double value;
    int duration;
    time_t timestamp;
    int priority;
    char* parameters;
} ActuatorCommand;
```

### Actuator Implementation
```c
// Create actuator
Actuator* createActuator(int actuator_id, const char* name, ActuatorType type, double min_value, double max_value) {
    Actuator* actuator = malloc(sizeof(Actuator));
    if (!actuator) return NULL;
    
    memset(actuator, 0, sizeof(Actuator));
    actuator->actuator_id = actuator_id;
    strncpy(actuator->name, name, sizeof(actuator->name) - 1);
    actuator->type = type;
    actuator->current_state = ACTUATOR_OFF;
    actuator->current_value = min_value;
    actuator->min_value = min_value;
    actuator->max_value = max_value;
    
    return actuator;
}

// Send command to actuator
int sendActuatorCommand(Actuator* actuator, ActuatorState target_state, double value, int duration) {
    if (!actuator) return -1;
    
    if (actuator->is_busy && actuator->pending_count >= 100) {
        return -2; // Command queue full
    }
    
    ActuatorCommand* command = malloc(sizeof(ActuatorCommand));
    if (!command) return -3;
    
    memset(command, 0, sizeof(ActuatorCommand));
    command->actuator_id = actuator->actuator_id;
    command->type = actuator->type;
    command->target_state = target_state;
    command->value = value;
    command->duration = duration;
    command->timestamp = time(NULL);
    command->priority = 1;
    
    // Add to pending commands queue
    actuator->pending_commands[actuator->pending_count++] = command;
    actuator->last_command = time(NULL);
    
    printf("Command sent to actuator %s: State=%d, Value=%.2f, Duration=%d\n",
           actuator->name, target_state, value, duration);
    
    return 0;
}

// Process actuator commands
int processActuatorCommands(Actuator* actuator) {
    if (!actuator || actuator->pending_count == 0) return 0;
    
    // Sort commands by priority (simplified)
    for (int i = 0; i < actuator->pending_count - 1; i++) {
        for (int j = i + 1; j < actuator->pending_count; j++) {
            if (actuator->pending_commands[i]->priority > actuator->pending_commands[j]->priority) {
                ActuatorCommand* temp = actuator->pending_commands[i];
                actuator->pending_commands[i] = actuator->pending_commands[j];
                actuator->pending_commands[j] = temp;
            }
        }
    }
    
    // Process highest priority command
    ActuatorCommand* command = actuator->pending_commands[0];
    
    // Execute command
    actuator->current_state = command->target_state;
    actuator->current_value = command->value;
    actuator->is_busy = 1;
    
    printf("Executing command on actuator %s: State=%d, Value=%.2f\n",
           actuator->name, command->target_state, command->value);
    
    // Remove command from queue
    free(command);
    
    // Shift remaining commands
    for (int i = 0; i < actuator->pending_count - 1; i++) {
        actuator->pending_commands[i] = actuator->pending_commands[i + 1];
    }
    actuator->pending_count--;
    
    // Simulate command execution time
    if (command->duration > 0) {
        usleep(command->duration * 1000); // Convert to microseconds
    }
    
    actuator->is_busy = 0;
    
    return 1;
}
```

**Actuator Benefits**:
- **Remote Control**: Control devices from anywhere
- **Automation**: Execute predefined sequences of actions
- **Feedback**: Monitor actuator status and performance
- **Priority Handling**: Process commands based on importance

## 📡 Communication Protocols

### Protocol Types
```c
// Protocol types
typedef enum {
    PROTOCOL_MQTT = 0,
    PROTOCOL_COAP = 1,
    PROTOCOL_HTTP = 2,
    PROTOCOL_WEBSOCKET = 3,
    PROTOCOL_ZIGBEE = 4,
    PROTOCOL_BLUETOOTH = 5,
    PROTOCOL_LORAWAN = 6,
    PROTOCOL_MODBUS = 7
} ProtocolType;
```

### MQTT Client Structure
```c
// MQTT client structure
typedef struct {
    int client_id;
    char client_name[64];
    char broker_address[64];
    int port;
    char username[64];
    char password[64];
    char* subscribed_topics[MAX_TOPIC_LENGTH];
    int topic_count;
    int is_connected;
    int keep_alive;
    int clean_session;
    pthread_t receive_thread;
} MQTTClient;
```

### MQTT Implementation
```c
// Create MQTT client
MQTTClient* createMQTTClient(int client_id, const char* client_name, const char* broker_address, int port) {
    MQTTClient* client = malloc(sizeof(MQTTClient));
    if (!client) return NULL;
    
    memset(client, 0, sizeof(MQTTClient));
    client->client_id = client_id;
    strncpy(client->client_name, client_name, sizeof(client->client_name) - 1);
    strncpy(client->broker_address, broker_address, sizeof(client->broker_address) - 1);
    client->port = port;
    client->keep_alive = 60;
    client->clean_session = 1;
    
    return client;
}

// Connect to MQTT broker
int connectMQTTBroker(MQTTClient* client) {
    if (!client) return -1;
    
    // Simulate MQTT connection
    printf("Connecting to MQTT broker %s:%d...\n", client->broker_address, client->port);
    
    // Create socket
    int sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock < 0) {
        return -2;
    }
    
    struct sockaddr_in server_addr;
    server_addr.sin_family = AF_INET;
    server_addr.sin_port = htons(client->port);
    server_addr.sin_addr.s_addr = inet_addr(client->broker_address);
    
    if (connect(sock, (struct sockaddr*)&server_addr, sizeof(server_addr)) < 0) {
        close(sock);
        return -3;
    }
    
    client->is_connected = 1;
    printf("Connected to MQTT broker\n");
    
    close(sock);
    return 0;
}

// Publish MQTT message
int publishMQTTMessage(MQTTClient* client, const char* topic, const char* payload, int qos) {
    if (!client || !client->is_connected || !topic || !payload) {
        return -1;
    }
    
    printf("Publishing to MQTT topic '%s': %s (QoS: %d)\n", topic, payload, qos);
    
    // Simulate MQTT publish
    IoTMessage message;
    message.protocol = PROTOCOL_MQTT;
    strncpy(message.topic, topic, sizeof(message.topic) - 1);
    strncpy(message.payload, payload, sizeof(message.payload) - 1);
    message.payload_size = strlen(payload);
    message.qos = qos;
    message.timestamp = time(NULL);
    
    // In a real implementation, would send to broker
    return 0;
}

// Subscribe to MQTT topic
int subscribeMQTTTopic(MQTTClient* client, const char* topic, int qos) {
    if (!client || !client->is_connected || !topic) {
        return -1;
    }
    
    printf("Subscribing to MQTT topic '%s' (QoS: %d)\n", topic, qos);
    
    // Add to subscribed topics
    if (client->topic_count < MAX_TOPIC_LENGTH) {
        strncpy(client->subscribed_topics[client->topic_count], topic, sizeof(client->subscribed_topics[client->topic_count]) - 1);
        client->topic_count++;
        return 0;
    }
    
    return -2;
}
```

**Communication Benefits**:
- **Protocol Support**: Multiple IoT communication protocols
- **Real-time Messaging**: Low-latency data transmission
- **Scalability**: Handle thousands of connected devices
- **Reliability**: Quality of Service and message persistence

## 📊 Data Processing

### Data Processing Types
```c
// Data processing types
typedef enum {
    PROCESSING_RAW = 0,
    PROCESSING_FILTERED = 1,
    PROCESSING_AGGREGATED = 2,
    PROCESSING_ANALYZED = 3,
    PROCESSING_ALERT = 4
} ProcessingType;
```

### Filter Types
```c
// Filter types
typedef enum {
    FILTER_LOW_PASS = 0,
    FILTER_HIGH_PASS = 1,
    FILTER_BAND_PASS = 2,
    FILTER_NOTCH = 3,
    FILTER_MEDIAN = 4,
    FILTER_KALMAN = 5
} FilterType;
```

### Data Processor Implementation
```c
// Create data processor
DataProcessor* createDataProcessor(int processor_id, const char* name, ProcessingType type) {
    DataProcessor* processor = malloc(sizeof(DataProcessor));
    if (!processor) return NULL;
    
    memset(processor, 0, sizeof(DataProcessor));
    processor->processor_id = processor_id;
    strncpy(processor->name, name, sizeof(processor->name) - 1);
    processor->type = type;
    processor->filter_type = FILTER_LOW_PASS;
    processor->aggregation_type = AGG_AVERAGE;
    processor->threshold_min = 0.0;
    processor->threshold_max = 100.0;
    processor->alert_enabled = 1;
    
    return processor;
}

// Filter sensor data
double filterSensorData(DataProcessor* processor, double value) {
    if (!processor) return value;
    
    switch (processor->filter_type) {
        case FILTER_LOW_PASS:
            // Simple low-pass filter
            if (processor->parameter_count > 0) {
                double alpha = processor->filter_parameters[0]; // Smoothing factor
                static double filtered_value = 0.0;
                filtered_value = alpha * value + (1.0 - alpha) * filtered_value;
                return filtered_value;
            }
            break;
            
        case FILTER_MEDIAN:
            // Simplified median filter (would need sliding window)
            return value;
            
        default:
            return value;
    }
    
    return value;
}

// Aggregate sensor data
double aggregateSensorData(DataProcessor* processor, double* values, int count) {
    if (!processor || !values || count <= 0) return 0.0;
    
    switch (processor->aggregation_type) {
        case AGG_AVERAGE: {
            double sum = 0.0;
            for (int i = 0; i < count; i++) {
                sum += values[i];
            }
            return sum / count;
        }
        
        case AGG_MIN: {
            double min = values[0];
            for (int i = 1; i < count; i++) {
                if (values[i] < min) {
                    min = values[i];
                }
            }
            return min;
        }
        
        case AGG_MAX: {
            double max = values[0];
            for (int i = 1; i < count; i++) {
                if (values[i] > max) {
                    max = values[i];
                }
            }
            return max;
        }
        
        case AGG_SUM: {
            double sum = 0.0;
            for (int i = 0; i < count; i++) {
                sum += values[i];
            }
            return sum;
        }
        
        default:
            return values[0];
    }
}

// Check for alerts
int checkAlerts(DataProcessor* processor, double value) {
    if (!processor || !processor->alert_enabled) return 0;
    
    if (value < processor->threshold_min || value > processor->threshold_max) {
        printf("ALERT: Processor %s - Value %.2f outside threshold [%.2f, %.2f]\n",
               processor->name, value, processor->threshold_min, processor->threshold_max);
        return 1;
    }
    
    return 0;
}
```

**Data Processing Benefits**:
- **Real-time Processing**: Immediate data analysis and response
- **Data Filtering**: Remove noise and improve data quality
- **Aggregation**: Summarize large datasets for efficient storage
- **Alert System**: Automatic notification of abnormal conditions

## 🖥️ Edge Computing

### Edge Node Types
```c
// Edge node types
typedef enum {
    EDGE_GATEWAY = 0,
    EDGE_CONTROLLER = 1,
    EDGE_ANALYTICS = 2,
    EDGE_STORAGE = 3
} EdgeNodeType;
```

### Edge Computing Capabilities
```c
// Edge computing capabilities
typedef enum {
    CAP_DATA_PROCESSING = 0,
    CAP_ML_INFERENCE = 1,
    CAP_DATA_STORAGE = 2,
    CAP_RULE_ENGINE = 3,
    CAP_SECURITY = 4
} EdgeCapability;
```

### Edge Node Implementation
```c
// Create edge node
EdgeNode* createEdgeNode(int node_id, const char* name, EdgeNodeType type, const char* ip_address, int port) {
    EdgeNode* node = malloc(sizeof(EdgeNode));
    if (!node) return NULL;
    
    memset(node, 0, sizeof(EdgeNode));
    node->node_id = node_id;
    strncpy(node->name, name, sizeof(node->name) - 1);
    node->type = type;
    strncpy(node->ip_address, ip_address, sizeof(node->ip_address) - 1);
    node->port = port;
    node->is_active = 1;
    node->last_heartbeat = time(NULL);
    
    // Set default capabilities based on node type
    switch (type) {
        case EDGE_GATEWAY:
            node->capabilities[node->capability_count++] = CAP_DATA_PROCESSING;
            node->capabilities[node->capability_count++] = CAP_DATA_STORAGE;
            break;
        case EDGE_CONTROLLER:
            node->capabilities[node->capability_count++] = CAP_RULE_ENGINE;
            node->capabilities[node->capability_count++] = CAP_SECURITY;
            break;
        case EDGE_ANALYTICS:
            node->capabilities[node->capability_count++] = CAP_ML_INFERENCE;
            node->capabilities[node->capability_count++] = CAP_DATA_PROCESSING;
            break;
        default:
            break;
    }
    
    return node;
}

// Update edge node metrics
void updateEdgeNodeMetrics(EdgeNode* node, double cpu_usage, double memory_usage, double storage_usage) {
    if (!node) return;
    
    node->cpu_usage = cpu_usage;
    node->memory_usage = memory_usage;
    node->storage_usage = storage_usage;
    node->last_heartbeat = time(NULL);
    
    printf("Edge node %s metrics updated: CPU=%.1f%%, Memory=%.1f%%, Storage=%.1f%%\n",
           node->name, cpu_usage, memory_usage, storage_usage);
}

// Add device to edge node
int addDeviceToEdgeNode(EdgeNode* node, int device_id) {
    if (!node || node->device_count >= MAX_DEVICES) {
        return -1;
    }
    
    node->connected_devices[node->device_count++] = device_id;
    
    printf("Device %d added to edge node %s\n", device_id, node->name);
    return 0;
}
```

**Edge Computing Benefits**:
- **Low Latency**: Process data close to the source
- **Bandwidth Optimization**: Reduce data transmission to cloud
- **Offline Operation**: Continue functioning without internet
- **Privacy**: Keep sensitive data local

## 🔐 Security and Authentication

### Authentication Types
```c
// Authentication types
typedef enum {
    AUTH_NONE = 0,
    AUTH_API_KEY = 1,
    AUTH_CERTIFICATE = 2,
    AUTH_OAUTH = 3,
    AUTH_JWT = 4
} AuthenticationType;
```

### Security Policies
```c
// Security policies
typedef enum {
    POLICY_ENCRYPTION = 0,
    POLICY_ACCESS_CONTROL = 1,
    POLICY_RATE_LIMITING = 2,
    POLICY_DATA_RETENTION = 3,
    POLICY_AUDIT_LOGGING = 4
} SecurityPolicy;
```

### Security Implementation
```c
// Create security manager
SecurityManager* createSecurityManager(AuthenticationType auth_type) {
    SecurityManager* manager = malloc(sizeof(SecurityManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(SecurityManager));
    manager->auth_type = auth_type;
    manager->encryption_enabled = 1;
    manager->policy_count = 0;
    
    // Add default policies
    manager->policies[manager->policy_count++] = POLICY_ENCRYPTION;
    manager->policies[manager->policy_count++] = POLICY_ACCESS_CONTROL;
    
    return manager;
}

// Generate device certificate
int generateDeviceCertificate(SecurityManager* manager, int device_id) {
    if (!manager || manager->certificate_count >= MAX_DEVICES) {
        return -1;
    }
    
    DeviceCertificate* cert = malloc(sizeof(DeviceCertificate));
    if (!cert) return -1;
    
    memset(cert, 0, sizeof(DeviceCertificate));
    sprintf(cert->device_id, "device_%d", device_id);
    
    // Generate key pair (simplified)
    generateKeyPair(cert->private_key, cert->public_key);
    
    // Generate certificate (simplified)
    sprintf(cert->certificate, "CERTIFICATE_%s", cert->device_id);
    cert->issued_date = time(NULL);
    cert->expiry_date = time(NULL) + (365 * 24 * 60 * 60); // 1 year
    cert->issuer = strdup("IoT-CA");
    cert->is_valid = 1;
    
    manager->certificates[manager->certificate_count++] = *cert;
    
    printf("Certificate generated for device %d\n", device_id);
    return 0;
}

// Encrypt data
int encryptData(SecurityManager* manager, const char* data, char* encrypted_data) {
    if (!manager || !manager->encryption_enabled || !data) {
        return -1;
    }
    
    // Simplified encryption (XOR with key)
    const char* key = manager->encryption_key ? manager->encryption_key : "default_key";
    int data_len = strlen(data);
    
    for (int i = 0; i < data_len; i++) {
        encrypted_data[i] = data[i] ^ key[i % strlen(key)];
    }
    encrypted_data[data_len] = '\0';
    
    return data_len;
}
```

**Security Benefits**:
- **Device Authentication**: Verify device identity
- **Data Encryption**: Protect sensitive information
- **Access Control**: Restrict unauthorized access
- **Audit Logging**: Track security events

## 🔧 Best Practices

### 1. Memory Management
```c
// Good: Proper memory cleanup
void cleanupIoTDevice(IoTDevice* device) {
    if (!device) return;
    
    if (device->configuration) {
        free(device->configuration);
    }
    if (device->location) {
        free(device->location);
    }
    if (device->owner) {
        free(device->owner);
    }
    
    free(device);
}

// Bad: Memory leaks
void cleanupIoTDeviceLeaky(IoTDevice* device) {
    free(device);
    // Forgot to free configuration, location, owner - memory leaks!
}
```

### 2. Error Handling
```c
// Good: Comprehensive error handling
int readSensorSafe(Sensor* sensor, SensorReading** reading) {
    if (!sensor || !reading) {
        return -1; // Invalid parameters
    }
    
    if (!sensor->is_active) {
        return -2; // Sensor not active
    }
    
    SensorReading* temp_reading = readSensor(sensor, 0);
    if (!temp_reading) {
        return -3; // Failed to read sensor
    }
    
    if (!temp_reading->is_valid) {
        free(temp_reading);
        return -4; // Invalid reading
    }
    
    *reading = temp_reading;
    return 0; // Success
}

// Bad: No error handling
void readSensorUnsafe(Sensor* sensor) {
    SensorReading* reading = readSensor(sensor, 0);
    // No null check - can cause crashes
    printf("Value: %.2f\n", reading->value);
}
```

### 3. Thread Safety
```c
// Good: Thread-safe operations
pthread_mutex_t device_mutex = PTHREAD_MUTEX_INITIALIZER;

int updateDeviceStatusThreadSafe(IoTDevice* device, DeviceState state) {
    pthread_mutex_lock(&device_mutex);
    
    if (!device) {
        pthread_mutex_unlock(&device_mutex);
        return -1;
    }
    
    device->state = state;
    device->last_seen = time(NULL);
    
    pthread_mutex_unlock(&device_mutex);
    return 0;
}

// Bad: No synchronization
int updateDeviceStatusUnsafe(IoTDevice* device, DeviceState state) {
    // No mutex - race condition in multi-threaded environment
    device->state = state;
    device->last_seen = time(NULL);
    return 0;
}
```

### 4. Resource Management
```c
// Good: Resource limits
int addDeviceToManager(DeviceManager* manager, IoTDevice* device) {
    if (!manager || !device) {
        return -1;
    }
    
    if (manager->device_count >= manager->max_devices) {
        printf("Device limit reached (%d/%d)\n", manager->device_count, manager->max_devices);
        return -2;
    }
    
    manager->devices[manager->device_count++] = *device;
    return 0;
}

// Bad: No limits
void addDeviceToManagerUnsafe(DeviceManager* manager, IoTDevice* device) {
    manager->devices[manager->device_count++] = *device;
    // No bounds check - buffer overflow
}
```

### 5. Configuration Validation
```c
// Good: Configuration validation
int validateDeviceConfiguration(const char* config) {
    if (!config) return 0;
    
    // Check for required fields
    if (strstr(config, "sampling_rate=") == NULL) {
        return 0; // Missing required field
    }
    
    // Validate values
    int sampling_rate;
    if (sscanf(strstr(config, "sampling_rate="), "sampling_rate=%d", &sampling_rate) != 1) {
        return 0; // Invalid format
    }
    
    if (sampling_rate < 1 || sampling_rate > 1000) {
        return 0; // Invalid range
    }
    
    return 1; // Valid
}

// Bad: No validation
int configureDeviceUnsafe(IoTDevice* device, const char* config) {
    device->configuration = strdup(config);
    // No validation - can accept invalid configuration
    return 0;
}
```

## ⚠️ Common Pitfalls

### 1. Device Overload
```c
// Wrong: No rate limiting
void processAllDevices(IoTDevice* devices, int count) {
    for (int i = 0; i < count; i++) {
        processDevice(&devices[i]); // Can overload system
    }
}

// Right: Rate limiting
void processAllDevicesSafe(IoTDevice* devices, int count) {
    for (int i = 0; i < count; i++) {
        processDevice(&devices[i]);
        usleep(1000); // 1ms delay between devices
    }
}
```

### 2. Memory Exhaustion
```c
// Wrong: Unlimited data collection
void collectSensorDataUnlimited(Sensor* sensor) {
    while (1) {
        SensorReading* reading = readSensor(sensor, 0);
        sensor->readings[sensor->reading_count++] = *reading;
        // No limit - memory exhaustion
    }
}

// Right: Data limits
void collectSensorDataLimited(Sensor* sensor, int max_readings) {
    while (sensor->reading_count < max_readings) {
        SensorReading* reading = readSensor(sensor, 0);
        sensor->readings[sensor->reading_count++] = *reading;
        sleep(1); // 1 second interval
    }
}
```

### 3. Network Issues
```c
// Wrong: No network error handling
void publishToMQTTUnsafe(MQTTClient* client, const char* topic, const char* payload) {
    publishMQTTMessage(client, topic, payload, 1);
    // No check if connected - can fail silently
}

// Right: Network error handling
int publishToMQTTSafe(MQTTClient* client, const char* topic, const char* payload) {
    if (!client->is_connected) {
        return -1; // Not connected
    }
    
    int result = publishMQTTMessage(client, topic, payload, 1);
    if (result != 0) {
        // Try to reconnect
        connectMQTTBroker(client);
        if (client->is_connected) {
            result = publishMQTTMessage(client, topic, payload, 1);
        }
    }
    
    return result;
}
```

### 4. Security Vulnerabilities
```c
// Wrong: Plain text communication
void sendSensorDataUnsafe(Sensor* sensor, const char* server) {
    char data[256];
    sprintf(data, "sensor_id=%d,value=%.2f", sensor->sensor_id, sensor->readings[0].value);
    sendToServer(server, data); // Plain text - security vulnerability
}

// Right: Encrypted communication
void sendSensorDataSafe(Sensor* sensor, const char* server, SecurityManager* security) {
    char data[256];
    sprintf(data, "sensor_id=%d,value=%.2f", sensor->sensor_id, sensor->readings[0].value);
    
    char encrypted_data[512];
    int encrypted_len = encryptData(security, data, encrypted_data);
    
    sendToServer(server, encrypted_data); // Encrypted data
}
```

## 🔧 Real-World Applications

### 1. Smart Home
```c
// Smart home automation
void implementSmartHome() {
    DeviceManager* home = createDeviceManager();
    
    // Add devices
    IoTDevice* temp_sensor = createIoTDevice(1, "Living Room Temp", DEVICE_SENSOR, "Nest", "T100");
    IoTDevice* smart_light = createIoTDevice(2, "Living Room Light", DEVICE_ACTUATOR, "Philips", "Hue");
    
    addDeviceToManager(home, temp_sensor);
    addDeviceToManager(home, smart_light);
    
    // Automation rule
    while (1) {
        SensorReading* reading = readSensor(temp_sensor->sensors[0], temp_sensor->device_id);
        
        if (reading->value > 25.0) {
            sendActuatorCommand(smart_light->actuators[0], ACTUATOR_ON, 0.5, 0);
        } else {
            sendActuatorCommand(smart_light->actuators[0], ACTUATOR_OFF, 0.0, 0);
        }
        
        free(reading);
        sleep(60); // Check every minute
    }
}
```

### 2. Industrial Monitoring
```c
// Industrial equipment monitoring
void implementIndustrialMonitoring() {
    SensorManager* factory = createSensorManager();
    
    // Add sensors
    Sensor* vibration_sensor = createSensor(1, "Motor Vibration", SENSOR_ACCELEROMETER, UNIT_GRAVITY, 0.0, 10.0);
    Sensor* temperature_sensor = createSensor(2, "Motor Temperature", SENSOR_TEMPERATURE, UNIT_CELSIUS, -20.0, 150.0);
    
    addSensorToManager(factory, vibration_sensor);
    addSensorToManager(factory, temperature_sensor);
    
    // Monitoring loop
    while (1) {
        SensorReading* vib_reading = readSensor(vibration_sensor, 1);
        SensorReading* temp_reading = readSensor(temperature_sensor, 1);
        
        // Check for alerts
        if (vib_reading->value > 5.0 || temp_reading->value > 80.0) {
            sendAlert("Motor maintenance required", vib_reading->value > 5.0 ? "Vibration" : "Temperature");
        }
        
        free(vib_reading);
        free(temp_reading);
        sleep(10); // Check every 10 seconds
    }
}
```

### 3. Agriculture IoT
```c
// Smart agriculture system
void implementSmartAgriculture() {
    EdgeNode* farm_gateway = createEdgeNode(1, "Farm Gateway", EDGE_GATEWAY, "192.168.1.100", 8080);
    
    // Add sensors
    Sensor* soil_moisture = createSensor(1, "Soil Moisture", SENSOR_HUMIDITY, UNIT_PERCENT, 0.0, 100.0);
    Sensor* air_temp = createSensor(2, "Air Temperature", SENSOR_TEMPERATURE, UNIT_CELSIUS, -40.0, 80.0);
    Sensor* light_level = createSensor(3, "Light Level", SENSOR_LIGHT, UNIT_LUX, 0.0, 100000.0);
    
    addDeviceToEdgeNode(farm_gateway, soil_moisture->sensor_id);
    addDeviceToEdgeNode(farm_gateway, air_temp->sensor_id);
    addDeviceToEdgeNode(farm_gateway, light_level->sensor_id);
    
    // Irrigation control
    Actuator* water_pump = createActuator(1, "Water Pump", ACTUATOR_PUMP, 0.0, 1.0);
    
    while (1) {
        SensorReading* moisture_reading = readSensor(soil_moisture, 1);
        
        if (moisture_reading->value < 30.0) {
            sendActuatorCommand(water_pump, ACTUATOR_ON, 1.0, 300000); // 5 minutes
        }
        
        free(moisture_reading);
        sleep(3600); // Check every hour
    }
}
```

### 4. Healthcare Monitoring
```c
// Patient monitoring system
void implementHealthcareMonitoring() {
    SecurityManager* hospital = createSecurityManager(AUTH_CERTIFICATE);
    hospital->encryption_enabled = 1;
    
    // Patient device
    IoTDevice* patient_monitor = createIoTDevice(1, "Patient Monitor", DEVICE_SENSOR, "MedTech", "PM-2000");
    generateDeviceCertificate(hospital, 1);
    
    // Vital signs sensors
    Sensor* heart_rate = createSensor(1, "Heart Rate", SENSOR_SOUND, UNIT_DECIBEL, 40.0, 200.0);
    Sensor* blood_pressure = createSensor(2, "Blood Pressure", SENSOR_PRESSURE, UNIT_PASCAL, 8000.0, 20000.0);
    Sensor* temperature = createSensor(3, "Body Temperature", SENSOR_TEMPERATURE, UNIT_CELSIUS, 35.0, 42.0);
    
    // Monitoring loop with encryption
    while (1) {
        SensorReading* hr_reading = readSensor(heart_rate, 1);
        SensorReading* bp_reading = readSensor(blood_pressure, 1);
        SensorReading* temp_reading = readSensor(temperature, 1);
        
        // Check for critical values
        if (hr_reading->value < 60 || hr_reading->value > 100 ||
            bp_reading->value < 10000 || bp_reading->value > 16000 ||
            temp_reading->value < 36.0 || temp_reading->value > 38.5) {
            
            char alert_data[512];
            sprintf(alert_data, "HR=%.0f,BP=%.0f,TEMP=%.1f", 
                   hr_reading->value, bp_reading->value, temp_reading->value);
            
            char encrypted_data[1024];
            encryptData(hospital, alert_data, encrypted_data);
            
            sendEmergencyAlert(encrypted_data);
        }
        
        free(hr_reading);
        free(bp_reading);
        free(temp_reading);
        sleep(30); // Check every 30 seconds
    }
}
```

## 📚 Further Reading

### Books
- "Building the Web of Things" by Dominique Guinard and Vlad Trifa
- "Designing the Internet of Things" by Adrian McEwen and Hakim Cassimally
- "IoT Fundamentals" by David Hanes
- "Programming the Internet of Things" by Andy Stanford-Clark

### Topics
- MQTT and CoAP protocols
- Edge computing architectures
- IoT security best practices
- Machine learning for IoT
- Industrial IoT (IIoT)
- Smart city implementations
- Wearable devices
- IoT cloud platforms

IoT development in C provides the foundation for building efficient, scalable, and secure connected systems. Master these techniques to create robust IoT applications that can handle real-world deployment challenges and deliver reliable performance in diverse environments!
