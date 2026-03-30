#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <pthread.h>

// =============================================================================
// IOT DEVELOPMENT
// =============================================================================

#define MAX_DEVICES 10000
#define MAX_SENSORS 100
#define MAX_ACTUATORS 50
#define MAX_DATA_POINTS 1000000
#define MAX_MESSAGE_SIZE 1024
#define MAX_TOPIC_LENGTH 256
#define MAX_PAYLOAD_SIZE 512
#define MQTT_PORT 1883
#define COAP_PORT 5683
#define HTTP_PORT 80

// =============================================================================
// IOT DEVICE MANAGEMENT
// =============================================================================

// Device types
typedef enum {
    DEVICE_SENSOR = 0,
    DEVICE_ACTUATOR = 1,
    DEVICE_GATEWAY = 2,
    DEVICE_EDGE = 3,
    DEVICE_CONTROLLER = 4
} DeviceType;

// Device states
typedef enum {
    DEVICE_OFFLINE = 0,
    DEVICE_ONLINE = 1,
    DEVICE_ERROR = 2,
    DEVICE_MAINTENANCE = 3,
    DEVICE_UPDATING = 4
} DeviceState;

// Connection types
typedef enum {
    CONN_WIFI = 0,
    CONN_ETHERNET = 1,
    CONN_BLUETOOTH = 2,
    CONN_ZIGBEE = 3,
    CONN_LORAWAN = 4,
    CONN_CELLULAR = 5
} ConnectionType;

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

// Device manager structure
typedef struct {
    IoTDevice* devices[MAX_DEVICES];
    int device_count;
    int max_devices;
    char* discovery_service;
    int auto_provisioning;
    int security_enabled;
} DeviceManager;

// =============================================================================
// SENSOR MANAGEMENT
// =============================================================================

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

// Data units
typedef enum {
    UNIT_CELSIUS = 0,
    UNIT_FAHRENHEIT = 1,
    UNIT_KELVIN = 2,
    UNIT_PERCENT = 3,
    UNIT_PASCAL = 4,
    UNIT_LUX = 5,
    UNIT_DECIBEL = 6,
    UNIT_PPM = 7,
    UNIT_METER = 8,
    UNIT_METER_PER_SECOND = 9,
    UNIT_GRAVITY = 10,
    UNIT_VOLT = 11,
    UNIT_AMPERE = 12,
    UNIT_WATT = 13
} DataUnit;

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

// Sensor structure
typedef struct {
    int sensor_id;
    char name[64];
    SensorType type;
    DataUnit unit;
    double min_value;
    double max_value;
    double accuracy;
    double precision;
    int sampling_rate; // Hz
    int is_active;
    time_t last_reading;
    SensorReading* readings[MAX_DATA_POINTS];
    int reading_count;
    char* calibration_data;
    int is_calibrated;
} Sensor;

// Sensor manager structure
typedef struct {
    Sensor* sensors[MAX_SENSORS];
    int sensor_count;
    int max_sensors;
    int data_retention_days;
    int compression_enabled;
    char* data_storage_path;
} SensorManager;

// =============================================================================
// ACTUATOR MANAGEMENT
// =============================================================================

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

// Actuator states
typedef enum {
    ACTUATOR_OFF = 0,
    ACTUATOR_ON = 1,
    ACTUATOR_OPEN = 2,
    ACTUATOR_CLOSED = 3,
    ACTUATOR_STARTING = 4,
    ACTUATOR_STOPPING = 5,
    ACTUATOR_ERROR = 6
} ActuatorState;

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

// Actuator structure
typedef struct {
    int actuator_id;
    char name[64];
    ActuatorType type;
    ActuatorState current_state;
    double current_value;
    double min_value;
    double max_value;
    int device_id;
    time_t last_command;
    ActuatorCommand* pending_commands[100];
    int pending_count;
    int is_busy;
    char* configuration;
} Actuator;

// Actuator manager structure
typedef struct {
    Actuator* actuators[MAX_ACTUATORS];
    int actuator_count;
    int max_actuators;
    int command_queue_size;
    int priority_scheduling;
    char* log_path;
} ActuatorManager;

// =============================================================================
// COMMUNICATION PROTOCOLS
// =============================================================================

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

// Message structure
typedef struct {
    int message_id;
    ProtocolType protocol;
    char source_address[64];
    char destination_address[64];
    char topic[MAX_TOPIC_LENGTH];
    char payload[MAX_PAYLOAD_SIZE];
    int payload_size;
    int qos; // Quality of Service
    int retain;
    time_t timestamp;
    int is_encrypted;
    char* signature;
} IoTMessage;

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

// CoAP client structure
typedef struct {
    int client_id;
    char client_name[64];
    char server_address[64];
    int port;
    int is_connected;
    int message_id_counter;
    char* pending_messages[100];
    int pending_count;
} CoAPClient;

// HTTP client structure
typedef struct {
    int client_id;
    char client_name[64];
    char server_address[64];
    int port;
    char* headers[20];
    int header_count;
    int is_connected;
} HTTPClient;

// Communication manager structure
typedef struct {
    MQTTClient* mqtt_clients[MAX_DEVICES];
    int mqtt_client_count;
    CoAPClient* coap_clients[MAX_DEVICES];
    int coap_client_count;
    HTTPClient* http_clients[MAX_DEVICES];
    int http_client_count;
    ProtocolType default_protocol;
    int message_queue_size;
    int retry_attempts;
} CommunicationManager;

// =============================================================================
// DATA PROCESSING
// =============================================================================

// Data processing types
typedef enum {
    PROCESSING_RAW = 0,
    PROCESSING_FILTERED = 1,
    PROCESSING_AGGREGATED = 2,
    PROCESSING_ANALYZED = 3,
    PROCESSING_ALERT = 4
} ProcessingType;

// Filter types
typedef enum {
    FILTER_LOW_PASS = 0,
    FILTER_HIGH_PASS = 1,
    FILTER_BAND_PASS = 2,
    FILTER_NOTCH = 3,
    FILTER_MEDIAN = 4,
    FILTER_KALMAN = 5
} FilterType;

// Aggregation types
typedef enum {
    AGG_AVERAGE = 0,
    AGG_MIN = 1,
    AGG_MAX = 2,
    AGG_SUM = 3,
    AGG_COUNT = 4,
    AGG_MEDIAN = 5
} AggregationType;

// Alert types
typedef enum {
    ALERT_THRESHOLD = 0,
    ALERT_ANOMALY = 1,
    ALERT_PATTERN = 2,
    ALERT_SYSTEM = 3,
    ALERT_SECURITY = 4
} AlertType;

// Data processor structure
typedef struct {
    int processor_id;
    char name[64];
    ProcessingType type;
    FilterType filter_type;
    AggregationType aggregation_type;
    double filter_parameters[10];
    int parameter_count;
    double threshold_min;
    double threshold_max;
    int alert_enabled;
    time_t last_processing;
} DataProcessor;

// Data processing manager structure
typedef struct {
    DataProcessor* processors[MAX_SENSORS];
    int processor_count;
    int max_processors;
    int processing_interval;
    int alert_routing;
    char* alert_webhook;
} DataProcessingManager;

// =============================================================================
// EDGE COMPUTING
// =============================================================================

// Edge node types
typedef enum {
    EDGE_GATEWAY = 0,
    EDGE_CONTROLLER = 1,
    EDGE_ANALYTICS = 2,
    EDGE_STORAGE = 3
} EdgeNodeType;

// Edge computing capabilities
typedef enum {
    CAP_DATA_PROCESSING = 0,
    CAP_ML_INFERENCE = 1,
    CAP_DATA_STORAGE = 2,
    CAP_RULE_ENGINE = 3,
    CAP_SECURITY = 4
} EdgeCapability;

// Edge node structure
typedef struct {
    int node_id;
    char name[64];
    EdgeNodeType type;
    char ip_address[16];
    int port;
    EdgeCapability capabilities[10];
    int capability_count;
    double cpu_usage;
    double memory_usage;
    double storage_usage;
    int connected_devices[MAX_DEVICES];
    int device_count;
    time_t last_heartbeat;
    int is_active;
} EdgeNode;

// Edge computing manager structure
typedef struct {
    EdgeNode* nodes[MAX_DEVICES];
    int node_count;
    int max_nodes;
    int load_balancing_enabled;
    int auto_scaling;
    char* orchestration_service;
} EdgeComputingManager;

// =============================================================================
// SECURITY AND AUTHENTICATION
// =============================================================================

// Authentication types
typedef enum {
    AUTH_NONE = 0,
    AUTH_API_KEY = 1,
    AUTH_CERTIFICATE = 2,
    AUTH_OAUTH = 3,
    AUTH_JWT = 4
} AuthenticationType;

// Security policies
typedef enum {
    POLICY_ENCRYPTION = 0,
    POLICY_ACCESS_CONTROL = 1,
    POLICY_RATE_LIMITING = 2,
    POLICY_DATA_RETENTION = 3,
    POLICY_AUDIT_LOGGING = 4
} SecurityPolicy;

// Device certificate structure
typedef struct {
    char device_id[64];
    char public_key[512];
    char private_key[512];
    char certificate[1024];
    time_t issued_date;
    time_t expiry_date;
    char* issuer;
    int is_valid;
} DeviceCertificate;

// Security manager structure
typedef struct {
    AuthenticationType auth_type;
    SecurityPolicy policies[10];
    int policy_count;
    DeviceCertificate* certificates[MAX_DEVICES];
    int certificate_count;
    char* ca_certificate;
    int encryption_enabled;
    char* encryption_key;
} SecurityManager;

// =============================================================================
// IOT DEVICE IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// SENSOR IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// ACTUATOR IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// MQTT IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// DATA PROCESSING IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// EDGE COMPUTING IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// SECURITY IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateIoTDevices() {
    printf("=== IOT DEVICES DEMO ===\n");
    
    // Create device manager
    DeviceManager* manager = malloc(sizeof(DeviceManager));
    memset(manager, 0, sizeof(DeviceManager));
    manager->max_devices = MAX_DEVICES;
    manager->auto_provisioning = 1;
    manager->security_enabled = 1;
    
    // Create devices
    printf("Creating IoT devices...\n");
    manager->devices[manager->device_count++] = *createIoTDevice(1, "Temperature Sensor 1", DEVICE_SENSOR, "SensorCorp", "TempSense-X1");
    manager->devices[manager->device_count++] = *createIoTDevice(2, "Smart Light 1", DEVICE_ACTUATOR, "SmartHome", "Light-X100");
    manager->devices[manager->device_count++] = *createIoTDevice(3, "Gateway 1", DEVICE_GATEWAY, "IoTGateway", "GW-2000");
    manager->devices[manager->device_count++] = *createIoTDevice(4, "Motion Sensor 1", DEVICE_SENSOR, "SecurityCo", "Motion-X1");
    
    printf("Created %d devices\n", manager->device_count);
    
    // Update device statuses
    printf("\nUpdating device statuses...\n");
    for (int i = 0; i < manager->device_count; i++) {
        updateDeviceStatus(&manager->devices[i], DEVICE_ONLINE, 85.5 + rand() % 15, -45 + rand() % 20);
        printf("  %s: %s\n", manager->devices[i].name, manager->devices[i].state == DEVICE_ONLINE ? "Online" : "Offline");
    }
    
    // Configure devices
    printf("\nConfiguring devices...\n");
    for (int i = 0; i < manager->device_count; i++) {
        char config[256];
        sprintf(config, "sampling_rate=60,threshold=25.0,location=Room_%d", i + 1);
        configureDevice(&manager->devices[i], config);
    }
    
    // Check device health
    printf("\nChecking device health...\n");
    for (int i = 0; i < manager->device_count; i++) {
        int health = checkDeviceHealth(&manager->devices[i]);
        printf("  %s: %s\n", manager->devices[i].name, health ? "Healthy" : "Unhealthy");
    }
    
    free(manager);
}

void demonstrateSensors() {
    printf("\n=== SENSORS DEMO ===\n");
    
    // Create sensor manager
    SensorManager* manager = malloc(sizeof(SensorManager));
    memset(manager, 0, sizeof(SensorManager));
    manager->max_sensors = MAX_SENSORS;
    manager->data_retention_days = 30;
    manager->compression_enabled = 1;
    
    // Create sensors
    printf("Creating sensors...\n");
    manager->sensors[manager->sensor_count++] = *createSensor(1, "Temperature Sensor", SENSOR_TEMPERATURE, UNIT_CELSIUS, -40.0, 80.0);
    manager->sensors[manager->sensor_count++] = *createSensor(2, "Humidity Sensor", SENSOR_HUMIDITY, UNIT_PERCENT, 0.0, 100.0);
    manager->sensors[manager->sensor_count++] = *createSensor(3, "Pressure Sensor", SENSOR_PRESSURE, UNIT_PASCAL, 800.0, 1200.0);
    manager->sensors[manager->sensor_count++] = *createSensor(4, "Light Sensor", SENSOR_LIGHT, UNIT_LUX, 0.0, 10000.0);
    manager->sensors[manager->sensor_count++] = *createSensor(5, "Motion Sensor", SENSOR_MOTION, UNIT_DECIBEL, 0.0, 1.0);
    
    printf("Created %d sensors\n", manager->sensor_count);
    
    // Read sensor values
    printf("\nReading sensor values...\n");
    for (int i = 0; i < manager->sensor_count; i++) {
        Sensor* sensor = &manager->sensors[i];
        SensorReading* reading = readSensor(sensor, 1);
        
        if (reading) {
            printf("  %s: %.2f %s\n", sensor->name, reading->value, getUnitName(reading->unit));
            free(reading);
        }
    }
    
    // Calibrate sensors
    printf("\nCalibrating sensors...\n");
    for (int i = 0; i < manager->sensor_count; i++) {
        Sensor* sensor = &manager->sensors[i];
        
        // Use reference values for calibration
        double reference_values[] = {25.0, 50.0, 1000.0, 500.0, 0.0};
        if (i < 5) {
            calibrateSensor(sensor, reference_values[i]);
        }
    }
    
    free(manager);
}

void demonstrateActuators() {
    printf("\n=== ACTUATORS DEMO ===\n");
    
    // Create actuator manager
    ActuatorManager* manager = malloc(sizeof(ActuatorManager));
    memset(manager, 0, sizeof(ActuatorManager));
    manager->max_actuators = MAX_ACTUATORS;
    manager->command_queue_size = 100;
    manager->priority_scheduling = 1;
    
    // Create actuators
    printf("Creating actuators...\n");
    manager->actuators[manager->actuator_count++] = *createActuator(1, "Smart Light", ACTUATOR_LED, 0.0, 100.0);
    manager->actuators[manager->actuator_count++] = *createActuator(2, "Smart Fan", ACTUATOR_FAN, 0, 5);
    manager->actuators[manager->actuator_count++] = *createActuator(3, "Water Valve", ACTUATOR_VALVE, 0.0, 1.0);
    manager->actuators[manager->actuator_count++] = *createActuator(4, "Smart Relay", ACTUATOR_RELAY, 0.0, 1.0);
    
    printf("Created %d actuators\n", manager->actuator_count);
    
    // Send commands to actuators
    printf("\nSending commands to actuators...\n");
    for (int i = 0; i < manager->actuator_count; i++) {
        Actuator* actuator = &manager->actuators[i];
        
        switch (actuator->type) {
            case ACTUATOR_LED:
                sendActuatorCommand(actuator, ACTUATOR_ON, 75.0, 0);
                break;
            case ACTUATOR_FAN:
                sendActuatorCommand(actuator, ACTUATOR_ON, 3.0, 0);
                break;
            case ACTUATOR_VALVE:
                sendActuatorCommand(actuator, ACTUATOR_OPEN, 1.0, 5000);
                break;
            case ACTUATOR_RELAY:
                sendActuatorCommand(actuator, ACTUATOR_ON, 1.0, 0);
                break;
            default:
                break;
        }
    }
    
    // Process actuator commands
    printf("\nProcessing actuator commands...\n");
    for (int i = 0; i < manager->actuator_count; i++) {
        Actuator* actuator = &manager->actuators[i];
        
        while (actuator->pending_count > 0) {
            processActuatorCommands(actuator);
            printf("  %s: State=%d, Value=%.2f\n", actuator->name, actuator->current_state, actuator->current_value);
        }
    }
    
    free(manager);
}

void demonstrateMQTT() {
    printf("\n=== MQTT DEMO ===\n");
    
    // Create MQTT client
    MQTTClient* client = createMQTTClient(1, "IoT-Client-1", "192.168.1.100", MQTT_PORT);
    
    printf("Created MQTT client: %s\n", client->client_name);
    
    // Connect to broker
    int result = connectMQTTBroker(client);
    if (result == 0) {
        printf("Successfully connected to MQTT broker\n");
        
        // Subscribe to topics
        subscribeMQTTTopic(client, "sensors/temperature", 1);
        subscribeMQTTTopic(client, "sensors/humidity", 1);
        subscribeMQTTTopic(client, "actuators/light", 0);
        
        // Publish messages
        publishMQTTMessage(client, "sensors/temperature", "23.5", 1);
        publishMQTTMessage(client, "sensors/humidity", "65.2", 1);
        publishMQTTMessage(client, "actuators/light", "ON", 0);
        
        printf("Published and subscribed to MQTT topics\n");
    } else {
        printf("Failed to connect to MQTT broker (Error: %d)\n", result);
    }
    
    free(client);
}

void demonstrateDataProcessing() {
    printf("\n=== DATA PROCESSING DEMO ===\n");
    
    // Create data processing manager
    DataProcessingManager* manager = malloc(sizeof(DataProcessingManager));
    memset(manager, 0, sizeof(DataProcessingManager));
    manager->max_processors = MAX_SENSORS;
    manager->processing_interval = 60; // 1 minute
    manager->alert_routing = 1;
    
    // Create data processors
    printf("Creating data processors...\n");
    manager->processors[manager->processor_count++] = *createDataProcessor(1, "Temp Filter", PROCESSING_FILTERED);
    manager->processors[manager->processor_count++] = *createDataProcessor(2, "Humidity Aggregator", PROCESSING_AGGREGATED);
    manager->processors[processor_count++] = *createDataProcessor(3, "Pressure Monitor", PROCESSING_ALERT);
    
    printf("Created %d data processors\n", manager->processor_count);
    
    // Simulate sensor data processing
    printf("\nProcessing sensor data...\n");
    double temperature_data[] = {22.5, 23.1, 22.8, 24.2, 23.7, 21.9, 25.1, 24.8};
    double humidity_data[] = {45.2, 46.8, 44.1, 47.5, 43.9, 48.2, 46.1, 45.7};
    double pressure_data[] = {1013.2, 1015.8, 1011.4, 1018.9, 1014.5, 1016.2, 1012.8, 1017.1};
    
    // Process temperature data
    DataProcessor* temp_processor = &manager->processors[0];
    double filtered_temps[8];
    for (int i = 0; i < 8; i++) {
        filtered_temps[i] = filterSensorData(temp_processor, temperature_data[i]);
    }
    double avg_temp = aggregateSensorData(temp_processor, filtered_temps, 8);
    printf("Temperature: Raw=[%.1f,%.1f,%.1f,%.1f], Filtered=[%.1f,%.1f,%.1f,%.1f], Average=%.1f\n",
           temperature_data[0], temperature_data[1], temperature_data[2], temperature_data[3],
           filtered_temps[0], filtered_temps[1], filtered_temps[2], filtered_temps[3], avg_temp);
    
    // Process humidity data
    DataProcessor* humidity_processor = &manager->processors[1];
    double avg_humidity = aggregateSensorData(humidity_processor, humidity_data, 8);
    printf("Humidity: Average=%.1f\n", avg_humidity);
    
    // Check for alerts
    for (int i = 0; i < manager->processor_count; i++) {
        DataProcessor* processor = &manager->processors[i];
        double test_values[] = {avg_temp, avg_humidity, 1015.0};
        
        if (i < 3) {
            int alert = checkAlerts(processor, test_values[i]);
            if (alert) {
                printf("ALERT from %s\n", processor->name);
            }
        }
    }
    
    free(manager);
}

void demonstrateEdgeComputing() {
    printf("\n=== EDGE COMPUTING DEMO ===\n");
    
    // Create edge computing manager
    EdgeComputingManager* manager = malloc(sizeof(EdgeComputingManager));
    memset(manager, 0, sizeof(EdgeComputingManager));
    manager->max_nodes = MAX_DEVICES;
    manager->load_balancing_enabled = 1;
    manager->auto_scaling = 1;
    
    // Create edge nodes
    printf("Creating edge nodes...\n");
    manager->nodes[manager->node_count++] = *createEdgeNode(1, "Gateway-1", EDGE_GATEWAY, "192.168.1.10", 8080);
    manager->nodes[manager->node_count++] = *createEdgeNode(2, "Controller-1", EDGE_CONTROLLER, "192.168.1.11", 8081);
    manager->nodes[manager->node_count++] = *createEdgeNode(3, "Analytics-1", EDGE_ANALYTICS, "192.168.1.12", 8082);
    
    printf("Created %d edge nodes\n", manager->node_count);
    
    // Update node metrics
    printf("\nUpdating edge node metrics...\n");
    for (int i = 0; i < manager->node_count; i++) {
        updateEdgeNodeMetrics(&manager->nodes[i], 45.5 + rand() % 30, 60.2 + rand() % 20, 75.8 + rand() % 15);
        printf("  %s: CPU=%.1f%%, Memory=%.1f%%, Storage=%.1f%%\n",
               manager->nodes[i].name, manager->nodes[i].cpu_usage,
               manager->nodes[i].memory_usage, manager->nodes[i].storage_usage);
    }
    
    // Add devices to edge nodes
    printf("\nAdding devices to edge nodes...\n");
    for (int i = 0; i < manager->node_count; i++) {
        for (int j = 0; j < 5; j++) {
            addDeviceToEdgeNode(&manager->nodes[i], i * 10 + j + 1);
        }
        printf("  %s: %d connected devices\n", manager->nodes[i].name, manager->nodes[i].device_count);
    }
    
    // Simulate edge processing
    printf("\nSimulating edge processing...\n");
    for (int i = 0; i < manager->node_count; i++) {
        EdgeNode* node = &manager->nodes[i];
        
        printf("  %s processing data from %d devices\n", node->name, node->device_count);
        
        // Simulate processing based on capabilities
        for (int j = 0; j < node->capability_count; j++) {
            switch (node->capabilities[j]) {
                case CAP_DATA_PROCESSING:
                    printf("    - Processing sensor data\n");
                    break;
                case CAP_ML_INFERENCE:
                    printf("    - Running ML inference\n");
                    break;
                case CAP_DATA_STORAGE:
                    printf("    - Storing processed data\n");
                    break;
                case CAP_RULE_ENGINE:
                    printf("    - Evaluating automation rules\n");
                    break;
                case CAP_SECURITY:
                    printf("    - Performing security checks\n");
                    break;
                default:
                    break;
            }
        }
    }
    
    free(manager);
}

void demonstrateSecurity() {
    printf("\n=== SECURITY DEMO ===\n");
    
    // Create security manager
    SecurityManager* manager = createSecurityManager(AUTH_CERTIFICATE);
    
    printf("Created security manager with authentication type: %d\n", manager->auth_type);
    
    // Generate device certificates
    printf("\nGenerating device certificates...\n");
    for (int i = 1; i <= 5; i++) {
        generateDeviceCertificate(manager, i);
    }
    
    printf("Generated %d device certificates\n", manager->certificate_count);
    
    // Test encryption
    printf("\nTesting encryption...\n");
    char* original_data = "sensitive_sensor_data";
    char* encrypted_data = malloc(strlen(original_data) + 1);
    
    int encrypted_len = encryptData(manager, original_data, encrypted_data);
    printf("Original: %s\n", original_data);
    printf("Encrypted: %s (Length: %d)\n", encrypted_data, encrypted_len);
    
    // Decrypt (simplified - XOR with same key)
    char* decrypted_data = malloc(encrypted_len + 1);
    const char* key = manager->encryption_key ? manager->encryption_key : "default_key";
    for (int i = 0; i < encrypted_len; i++) {
        decrypted_data[i] = encrypted_data[i] ^ key[i % strlen(key)];
    }
    decrypted_data[encrypted_len] = '\0';
    printf("Decrypted: %s\n", decrypted_data);
    
    // Verify encryption/decryption
    if (strcmp(original_data, decrypted_data) == 0) {
        printf("Encryption/Decryption successful\n");
    } else {
        printf("Encryption/Decryption failed\n");
    }
    
    free(encrypted_data);
    free(decrypted_data);
    free(manager);
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Advanced IoT Development Examples\n");
    printf("==============================\n\n");
    
    // Seed random number generator
    srand(time(NULL));
    
    // Run all demonstrations
    demonstrateIoTDevices();
    demonstrateSensors();
    demonstrateActuators();
    demonstrateMQTT();
    demonstrateDataProcessing();
    demonstrateEdgeComputing();
    demonstrateSecurity();
    
    printf("\nAll advanced IoT development examples demonstrated!\n");
    printf("Key features implemented:\n");
    printf("- IoT device management and provisioning\n");
    printf("- Sensor data collection and calibration\n");
    printf("- Actuator control and command processing\n");
    printf("- MQTT communication protocol\n");
    printf("- Data filtering, aggregation, and alerting\n");
    "- Edge computing and distributed processing\n");
    printf("- Security and authentication\n");
    printf("- Device health monitoring\n");
    printf("- Multi-protocol communication support\n");
    printf("- Real-time data processing\n");
    printf("- Scalable device management\n");
    
    return 0;
}
