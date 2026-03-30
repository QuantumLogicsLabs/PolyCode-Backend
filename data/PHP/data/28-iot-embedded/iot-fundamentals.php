<?php
/**
 * IoT Fundamentals in PHP
 * 
 * Basic IoT concepts, device communication, and data collection.
 */

// IoT Device Interface
interface IoTDevice
{
    public function getId(): string;
    public function getType(): string;
    public function getStatus(): string;
    public function connect(): bool;
    public function disconnect(): bool;
    public function sendCommand(string $command, array $params = []): bool;
    public function readData(): array;
    public function getMetadata(): array;
}

// Base IoT Device
abstract class BaseIoTDevice implements IoTDevice
{
    protected string $id;
    protected string $type;
    protected string $status;
    protected array $metadata;
    protected array $connectionConfig;
    protected bool $isConnected = false;
    
    public function __construct(string $id, string $type, array $metadata = [])
    {
        $this->id = $id;
        $this->type = $type;
        $this->status = 'offline';
        $this->metadata = array_merge([
            'manufacturer' => 'Unknown',
            'model' => 'Unknown',
            'version' => '1.0',
            'location' => 'Unknown'
        ], $metadata);
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getType(): string
    {
        return $this->type;
    }
    
    public function getStatus(): string
    {
        return $this->status;
    }
    
    public function getMetadata(): array
    {
        return $this->metadata;
    }
    
    public function setMetadata(array $metadata): void
    {
        $this->metadata = array_merge($this->metadata, $metadata);
    }
    
    public function setConnectionConfig(array $config): void
    {
        $this->connectionConfig = $config;
    }
    
    public function getConnectionConfig(): array
    {
        return $this->connectionConfig;
    }
    
    public function isConnected(): bool
    {
        return $this->isConnected;
    }
    
    protected function setStatus(string $status): void
    {
        $this->status = $status;
    }
    
    protected function setConnected(bool $connected): void
    {
        $this->isConnected = $connected;
        $this->status = $connected ? 'online' : 'offline';
    }
    
    abstract public function connect(): bool;
    abstract public function disconnect(): bool;
    abstract public function sendCommand(string $command, array $params = []): bool;
    abstract public function readData(): array;
}

// Temperature Sensor Device
class TemperatureSensor extends BaseIoTDevice
{
    private float $currentTemperature;
    private float $minTemperature;
    private float $maxTemperature;
    private string $unit;
    
    public function __construct(string $id, array $metadata = [])
    {
        parent::__construct($id, 'temperature_sensor', $metadata);
        $this->currentTemperature = 20.0;
        $this->minTemperature = -40.0;
        $this->maxTemperature = 125.0;
        $this->unit = 'celsius';
    }
    
    public function connect(): bool
    {
        if ($this->isConnected) {
            return true;
        }
        
        echo "Connecting to temperature sensor {$this->id}...\n";
        
        // Simulate connection
        $this->setConnected(true);
        echo "Temperature sensor {$this->id} connected\n";
        
        return true;
    }
    
    public function disconnect(): bool
    {
        if (!$this->isConnected) {
            return true;
        }
        
        echo "Disconnecting temperature sensor {$this->id}...\n";
        $this->setConnected(false);
        echo "Temperature sensor {$this->id} disconnected\n";
        
        return true;
    }
    
    public function sendCommand(string $command, array $params = []): bool
    {
        if (!$this->isConnected) {
            echo "Device not connected\n";
            return false;
        }
        
        switch ($command) {
            case 'set_unit':
                if (isset($params['unit']) && in_array($params['unit'], ['celsius', 'fahrenheit', 'kelvin'])) {
                    $this->unit = $params['unit'];
                    echo "Temperature unit set to {$this->unit}\n";
                    return true;
                }
                break;
                
            case 'calibrate':
                if (isset($params['offset'])) {
                    $this->currentTemperature += $params['offset'];
                    echo "Sensor calibrated with offset: {$params['offset']}\n";
                    return true;
                }
                break;
                
            case 'set_range':
                if (isset($params['min']) && isset($params['max'])) {
                    $this->minTemperature = $params['min'];
                    $this->maxTemperature = $params['max'];
                    echo "Temperature range set to {$this->minTemperature}-{$this->maxTemperature}\n";
                    return true;
                }
                break;
        }
        
        echo "Unknown command or invalid parameters\n";
        return false;
    }
    
    public function readData(): array
    {
        if (!$this->isConnected) {
            return [];
        }
        
        // Simulate temperature reading with some variation
        $variation = (rand(-100, 100) / 100) * 2; // ±2 degrees variation
        $this->currentTemperature += $variation;
        
        // Keep within range
        $this->currentTemperature = max($this->minTemperature, min($this->maxTemperature, $this->currentTemperature));
        
        $data = [
            'device_id' => $this->id,
            'device_type' => $this->type,
            'timestamp' => time(),
            'temperature' => $this->currentTemperature,
            'unit' => $this->unit,
            'status' => 'normal'
        ];
        
        // Check if temperature is out of normal range
        if ($this->currentTemperature < 10 || $this->currentTemperature > 35) {
            $data['status'] = 'warning';
        }
        
        return $data;
    }
    
    public function getCurrentTemperature(): float
    {
        return $this->currentTemperature;
    }
    
    public function getUnit(): string
    {
        return $this->unit;
    }
}

// Motion Sensor Device
class MotionSensor extends BaseIoTDevice
{
    private bool $motionDetected;
    private int $sensitivity;
    private int $detectionCount;
    private int $lastDetectionTime;
    
    public function __construct(string $id, array $metadata = [])
    {
        parent::__construct($id, 'motion_sensor', $metadata);
        $this->motionDetected = false;
        $this->sensitivity = 5; // 1-10 scale
        $this->detectionCount = 0;
        $this->lastDetectionTime = 0;
    }
    
    public function connect(): bool
    {
        if ($this->isConnected) {
            return true;
        }
        
        echo "Connecting to motion sensor {$this->id}...\n";
        $this->setConnected(true);
        echo "Motion sensor {$this->id} connected\n";
        
        return true;
    }
    
    public function disconnect(): bool
    {
        if (!$this->isConnected) {
            return true;
        }
        
        echo "Disconnecting motion sensor {$this->id}...\n";
        $this->setConnected(false);
        echo "Motion sensor {$this->id} disconnected\n";
        
        return true;
    }
    
    public function sendCommand(string $command, array $params = []): bool
    {
        if (!$this->isConnected) {
            echo "Device not connected\n";
            return false;
        }
        
        switch ($command) {
            case 'set_sensitivity':
                if (isset($params['sensitivity']) && $params['sensitivity'] >= 1 && $params['sensitivity'] <= 10) {
                    $this->sensitivity = $params['sensitivity'];
                    echo "Sensitivity set to {$this->sensitivity}\n";
                    return true;
                }
                break;
                
            case 'reset_counter':
                $this->detectionCount = 0;
                echo "Detection counter reset\n";
                return true;
                
            case 'calibrate':
                echo "Motion sensor calibrated\n";
                return true;
        }
        
        echo "Unknown command or invalid parameters\n";
        return false;
    }
    
    public function readData(): array
    {
        if (!$this->isConnected) {
            return [];
        }
        
        // Simulate motion detection based on sensitivity
        $detectionProbability = $this->sensitivity / 20; // 5% to 50% chance
        $this->motionDetected = (rand(1, 100) / 100) <= $detectionProbability;
        
        if ($this->motionDetected) {
            $this->detectionCount++;
            $this->lastDetectionTime = time();
        }
        
        $data = [
            'device_id' => $this->id,
            'device_type' => $this->type,
            'timestamp' => time(),
            'motion_detected' => $this->motionDetected,
            'sensitivity' => $this->sensitivity,
            'detection_count' => $this->detectionCount,
            'last_detection' => $this->lastDetectionTime,
            'status' => 'normal'
        ];
        
        return $data;
    }
    
    public function isMotionDetected(): bool
    {
        return $this->motionDetected;
    }
    
    public function getDetectionCount(): int
    {
        return $this->detectionCount;
    }
}

// Smart Light Device
class SmartLight extends BaseIoTDevice
{
    private bool $isOn;
    private int $brightness;
    private string $color;
    private array $supportedColors;
    
    public function __construct(string $id, array $metadata = [])
    {
        parent::__construct($id, 'smart_light', $metadata);
        $this->isOn = false;
        $this->brightness = 50;
        $this->color = 'white';
        $this->supportedColors = ['white', 'red', 'green', 'blue', 'yellow', 'purple', 'orange', 'cyan'];
    }
    
    public function connect(): bool
    {
        if ($this->isConnected) {
            return true;
        }
        
        echo "Connecting to smart light {$this->id}...\n";
        $this->setConnected(true);
        echo "Smart light {$this->id} connected\n";
        
        return true;
    }
    
    public function disconnect(): bool
    {
        if (!$this->isConnected) {
            return true;
        }
        
        echo "Disconnecting smart light {$this->id}...\n";
        $this->setConnected(false);
        echo "Smart light {$this->id} disconnected\n";
        
        return true;
    }
    
    public function sendCommand(string $command, array $params = []): bool
    {
        if (!$this->isConnected) {
            echo "Device not connected\n";
            return false;
        }
        
        switch ($command) {
            case 'turn_on':
                $this->isOn = true;
                echo "Smart light turned on\n";
                return true;
                
            case 'turn_off':
                $this->isOn = false;
                echo "Smart light turned off\n";
                return true;
                
            case 'set_brightness':
                if (isset($params['brightness']) && $params['brightness'] >= 0 && $params['brightness'] <= 100) {
                    $this->brightness = $params['brightness'];
                    echo "Brightness set to {$this->brightness}%\n";
                    return true;
                }
                break;
                
            case 'set_color':
                if (isset($params['color']) && in_array($params['color'], $this->supportedColors)) {
                    $this->color = $params['color'];
                    echo "Color set to {$this->color}\n";
                    return true;
                }
                break;
                
            case 'toggle':
                $this->isOn = !$this->isOn;
                echo "Smart light toggled\n";
                return true;
        }
        
        echo "Unknown command or invalid parameters\n";
        return false;
    }
    
    public function readData(): array
    {
        if (!$this->isConnected) {
            return [];
        }
        
        $data = [
            'device_id' => $this->id,
            'device_type' => $this->type,
            'timestamp' => time(),
            'is_on' => $this->isOn,
            'brightness' => $this->brightness,
            'color' => $this->color,
            'power_consumption' => $this->isOn ? ($this->brightness * 0.6) : 0.1, // Watts
            'status' => 'normal'
        ];
        
        return $data;
    }
    
    public function isOn(): bool
    {
        return $this->isOn;
    }
    
    public function getBrightness(): int
    {
        return $this->brightness;
    }
    
    public function getColor(): string
    {
        return $this->color;
    }
}

// IoT Device Manager
class IoTDeviceManager
{
    private array $devices = [];
    private array $deviceGroups = [];
    private array $dataBuffer = [];
    private int $maxBufferSize = 1000;
    
    public function addDevice(IoTDevice $device): bool
    {
        if (isset($this->devices[$device->getId()])) {
            echo "Device with ID {$device->getId()} already exists\n";
            return false;
        }
        
        $this->devices[$device->getId()] = $device;
        echo "Device added: {$device->getId()} ({$device->getType()})\n";
        
        return true;
    }
    
    public function removeDevice(string $deviceId): bool
    {
        if (!isset($this->devices[$deviceId])) {
            echo "Device not found: $deviceId\n";
            return false;
        }
        
        $device = $this->devices[$deviceId];
        $device->disconnect();
        unset($this->devices[$deviceId]);
        
        echo "Device removed: $deviceId\n";
        return true;
    }
    
    public function getDevice(string $deviceId): ?IoTDevice
    {
        return $this->devices[$deviceId] ?? null;
    }
    
    public function getAllDevices(): array
    {
        return $this->devices;
    }
    
    public function getDevicesByType(string $type): array
    {
        return array_filter($this->devices, fn($device) => $device->getType() === $type);
    }
    
    public function connectDevice(string $deviceId): bool
    {
        $device = $this->getDevice($deviceId);
        
        if (!$device) {
            echo "Device not found: $deviceId\n";
            return false;
        }
        
        return $device->connect();
    }
    
    public function disconnectDevice(string $deviceId): bool
    {
        $device = $this->getDevice($deviceId);
        
        if (!$device) {
            echo "Device not found: $deviceId\n";
            return false;
        }
        
        return $device->disconnect();
    }
    
    public function connectAllDevices(): void
    {
        foreach ($this->devices as $device) {
            $device->connect();
        }
    }
    
    public function disconnectAllDevices(): void
    {
        foreach ($this->devices as $device) {
            $device->disconnect();
        }
    }
    
    public function sendCommand(string $deviceId, string $command, array $params = []): bool
    {
        $device = $this->getDevice($deviceId);
        
        if (!$device) {
            echo "Device not found: $deviceId\n";
            return false;
        }
        
        return $device->sendCommand($command, $params);
    }
    
    public function broadcastCommand(string $command, array $params = [], string $deviceType = null): array
    {
        $results = [];
        $devices = $deviceType ? $this->getDevicesByType($deviceType) : $this->devices;
        
        foreach ($devices as $device) {
            $results[$device->getId()] = $device->sendCommand($command, $params);
        }
        
        return $results;
    }
    
    public function readDeviceData(string $deviceId): array
    {
        $device = $this->getDevice($deviceId);
        
        if (!$device) {
            return [];
        }
        
        $data = $device->readData();
        
        if (!empty($data)) {
            $this->bufferData($data);
        }
        
        return $data;
    }
    
    public function readAllDeviceData(): array
    {
        $allData = [];
        
        foreach ($this->devices as $device) {
            $data = $device->readData();
            
            if (!empty($data)) {
                $allData[] = $data;
                $this->bufferData($data);
            }
        }
        
        return $allData;
    }
    
    private function bufferData(array $data): void
    {
        $this->dataBuffer[] = $data;
        
        // Keep buffer size manageable
        if (count($this->dataBuffer) > $this->maxBufferSize) {
            array_shift($this->dataBuffer);
        }
    }
    
    public function getDataBuffer(): array
    {
        return $this->dataBuffer;
    }
    
    public function clearDataBuffer(): void
    {
        $this->dataBuffer = [];
    }
    
    public function createDeviceGroup(string $groupName, array $deviceIds): bool
    {
        foreach ($deviceIds as $deviceId) {
            if (!isset($this->devices[$deviceId])) {
                echo "Device not found: $deviceId\n";
                return false;
            }
        }
        
        $this->deviceGroups[$groupName] = $deviceIds;
        echo "Device group created: $groupName\n";
        
        return true;
    }
    
    public function sendCommandToGroup(string $groupName, string $command, array $params = []): array
    {
        if (!isset($this->deviceGroups[$groupName])) {
            echo "Device group not found: $groupName\n";
            return [];
        }
        
        $results = [];
        foreach ($this->deviceGroups[$groupName] as $deviceId) {
            $results[$deviceId] = $this->sendCommand($deviceId, $command, $params);
        }
        
        return $results;
    }
    
    public function getDeviceGroups(): array
    {
        return $this->deviceGroups;
    }
    
    public function getDeviceStatus(): array
    {
        $status = [];
        
        foreach ($this->devices as $device) {
            $status[$device->getId()] = [
                'type' => $device->getType(),
                'status' => $device->getStatus(),
                'connected' => $device->isConnected(),
                'metadata' => $device->getMetadata()
            ];
        }
        
        return $status;
    }
    
    public function getStatistics(): array
    {
        $stats = [
            'total_devices' => count($this->devices),
            'connected_devices' => 0,
            'device_types' => [],
            'data_buffer_size' => count($this->dataBuffer),
            'groups' => count($this->deviceGroups)
        ];
        
        foreach ($this->devices as $device) {
            if ($device->isConnected()) {
                $stats['connected_devices']++;
            }
            
            $type = $device->getType();
            $stats['device_types'][$type] = ($stats['device_types'][$type] ?? 0) + 1;
        }
        
        return $stats;
    }
}

// IoT Communication Protocol
class IoTCommunicationProtocol
{
    private string $protocol;
    private array $config;
    private array $messageQueue = [];
    
    public function __construct(string $protocol, array $config = [])
    {
        $this->protocol = $protocol;
        $this->config = array_merge([
            'host' => 'localhost',
            'port' => 1883,
            'username' => '',
            'password' => '',
            'client_id' => 'php_iot_client'
        ], $config);
    }
    
    public function connect(): bool
    {
        echo "Connecting via {$this->protocol} protocol...\n";
        echo "Host: {$this->config['host']}\n";
        echo "Port: {$this->config['port']}\n";
        
        // Simulate connection
        echo "Connected via {$this->protocol}\n";
        return true;
    }
    
    public function disconnect(): bool
    {
        echo "Disconnecting from {$this->protocol}\n";
        return true;
    }
    
    public function publish(string $topic, array $message): bool
    {
        $payload = json_encode($message);
        
        echo "Publishing to topic: $topic\n";
        echo "Message: $payload\n";
        
        $this->messageQueue[] = [
            'topic' => $topic,
            'message' => $message,
            'timestamp' => time()
        ];
        
        return true;
    }
    
    public function subscribe(string $topic): bool
    {
        echo "Subscribing to topic: $topic\n";
        return true;
    }
    
    public function unsubscribe(string $topic): bool
    {
        echo "Unsubscribing from topic: $topic\n";
        return true;
    }
    
    public function getMessageQueue(): array
    {
        return $this->messageQueue;
    }
    
    public function getProtocol(): string
    {
        return $this->protocol;
    }
    
    public function getConfig(): array
    {
        return $this->config;
    }
}

// IoT Fundamentals Examples
class IoTFundamentalsExamples
{
    public function demonstrateBasicDevices(): void
    {
        echo "Basic IoT Devices Demo\n";
        echo str_repeat("-", 25) . "\n";
        
        // Create devices
        $tempSensor = new TemperatureSensor('temp_001', [
            'manufacturer' => 'SensorTech',
            'model' => 'ST-T100',
            'location' => 'Living Room'
        ]);
        
        $motionSensor = new MotionSensor('motion_001', [
            'manufacturer' => 'MotionCorp',
            'model' => 'MC-200',
            'location' => 'Front Door'
        ]);
        
        $smartLight = new SmartLight('light_001', [
            'manufacturer' => 'SmartHome',
            'model' => 'SH-L50',
            'location' => 'Bedroom'
        ]);
        
        // Test devices
        echo "\nTesting Temperature Sensor:\n";
        $tempSensor->connect();
        $tempData = $tempSensor->readData();
        echo "Temperature data: " . json_encode($tempData) . "\n";
        $tempSensor->sendCommand('set_unit', ['unit' => 'fahrenheit']);
        $tempSensor->disconnect();
        
        echo "\nTesting Motion Sensor:\n";
        $motionSensor->connect();
        $motionData = $motionSensor->readData();
        echo "Motion data: " . json_encode($motionData) . "\n";
        $motionSensor->sendCommand('set_sensitivity', ['sensitivity' => 7]);
        $motionSensor->disconnect();
        
        echo "\nTesting Smart Light:\n";
        $smartLight->connect();
        $lightData = $smartLight->readData();
        echo "Light data: " . json_encode($lightData) . "\n";
        $smartLight->sendCommand('turn_on');
        $smartLight->sendCommand('set_brightness', ['brightness' => 75]);
        $smartLight->sendCommand('set_color', ['color' => 'blue']);
        $smartLight->disconnect();
    }
    
    public function demonstrateDeviceManager(): void
    {
        echo "\nIoT Device Manager Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $manager = new IoTDeviceManager();
        
        // Add devices
        $devices = [
            new TemperatureSensor('temp_001', ['location' => 'Living Room']),
            new TemperatureSensor('temp_002', ['location' => 'Bedroom']),
            new MotionSensor('motion_001', ['location' => 'Front Door']),
            new MotionSensor('motion_002', ['location' => 'Back Door']),
            new SmartLight('light_001', ['location' => 'Living Room']),
            new SmartLight('light_002', ['location' => 'Bedroom'])
        ];
        
        foreach ($devices as $device) {
            $manager->addDevice($device);
        }
        
        // Connect all devices
        echo "\nConnecting all devices...\n";
        $manager->connectAllDevices();
        
        // Read data from all devices
        echo "\nReading data from all devices...\n";
        $allData = $manager->readAllDeviceData();
        echo "Collected " . count($allData) . " data points\n";
        
        foreach ($allData as $data) {
            echo "Device {$data['device_id']}: {$data['device_type']}\n";
        }
        
        // Send commands to specific device types
        echo "\nSending commands to temperature sensors...\n";
        $results = $manager->broadcastCommand('set_unit', ['unit' => 'celsius'], 'temperature_sensor');
        foreach ($results as $deviceId => $result) {
            echo "$deviceId: " . ($result ? 'Success' : 'Failed') . "\n";
        }
        
        echo "\nSending commands to smart lights...\n";
        $results = $manager->broadcastCommand('turn_on', [], 'smart_light');
        foreach ($results as $deviceId => $result) {
            echo "$deviceId: " . ($result ? 'Success' : 'Failed') . "\n";
        }
        
        // Create device groups
        echo "\nCreating device groups...\n";
        $manager->createDeviceGroup('living_room', ['temp_001', 'light_001']);
        $manager->createDeviceGroup('bedroom', ['temp_002', 'light_002']);
        $manager->createDeviceGroup('security', ['motion_001', 'motion_002']);
        
        // Send commands to groups
        echo "\nSending commands to groups...\n";
        $livingRoomResults = $manager->sendCommandToGroup('living_room', 'turn_on');
        echo "Living room group: " . count(array_filter($livingRoomResults)) . " devices responded\n";
        
        $securityResults = $manager->sendCommandToGroup('security', 'set_sensitivity', ['sensitivity' => 8]);
        echo "Security group: " . count(array_filter($securityResults)) . " devices responded\n";
        
        // Show statistics
        echo "\nDevice Statistics:\n";
        $stats = $manager->getStatistics();
        foreach ($stats as $key => $value) {
            if (is_array($value)) {
                echo "$key: " . json_encode($value) . "\n";
            } else {
                echo "$key: $value\n";
            }
        }
        
        // Show device status
        echo "\nDevice Status:\n";
        $status = $manager->getDeviceStatus();
        foreach ($status as $deviceId => $deviceStatus) {
            echo "$deviceId: {$deviceStatus['status']} ({$deviceStatus['type']})\n";
        }
        
        // Disconnect all devices
        echo "\nDisconnecting all devices...\n";
        $manager->disconnectAllDevices();
    }
    
    public function demonstrateCommunicationProtocols(): void
    {
        echo "\nIoT Communication Protocols Demo\n";
        echo str_repeat("-", 40) . "\n";
        
        // MQTT Protocol
        echo "MQTT Protocol:\n";
        $mqtt = new IoTCommunicationProtocol('mqtt', [
            'host' => 'mqtt.example.com',
            'port' => 1883,
            'username' => 'iot_user',
            'password' => 'iot_pass'
        ]);
        
        $mqtt->connect();
        $mqtt->subscribe('sensors/temperature');
        $mqtt->subscribe('sensors/motion');
        
        // Publish sensor data
        $mqtt->publish('sensors/temperature', [
            'device_id' => 'temp_001',
            'temperature' => 23.5,
            'unit' => 'celsius',
            'timestamp' => time()
        ]);
        
        $mqtt->publish('sensors/motion', [
            'device_id' => 'motion_001',
            'motion_detected' => true,
            'timestamp' => time()
        ]);
        
        $mqtt->disconnect();
        
        // HTTP Protocol
        echo "\nHTTP Protocol:\n";
        $http = new IoTCommunicationProtocol('http', [
            'host' => 'api.iot.example.com',
            'port' => 80,
            'endpoint' => '/api/v1'
        ]);
        
        $http->connect();
        $http->publish('device/data', [
            'device_id' => 'light_001',
            'status' => 'on',
            'brightness' => 80,
            'color' => 'white'
        ]);
        
        $http->disconnect();
        
        // CoAP Protocol
        echo "\nCoAP Protocol:\n";
        $coap = new IoTCommunicationProtocol('coap', [
            'host' => 'coap.iot.local',
            'port' => 5683
        ]);
        
        $coap->connect();
        $coap->publish('sensor/read', [
            'device_id' => 'temp_002',
            'request_type' => 'get_data'
        ]);
        
        $coap->disconnect();
        
        // Show message queue
        echo "\nMessage Queue:\n";
        echo "MQTT messages: " . count($mqtt->getMessageQueue()) . "\n";
        echo "HTTP messages: " . count($http->getMessageQueue()) . "\n";
        echo "CoAP messages: " . count($coap->getMessageQueue()) . "\n";
    }
    
    public function demonstrateDataCollection(): void
    {
        echo "\nIoT Data Collection Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $manager = new IoTDeviceManager();
        
        // Add multiple devices
        $devices = [
            new TemperatureSensor('temp_001', ['location' => 'Living Room']),
            new TemperatureSensor('temp_002', ['location' => 'Bedroom']),
            new MotionSensor('motion_001', ['location' => 'Front Door']),
            new SmartLight('light_001', ['location' => 'Living Room'])
        ];
        
        foreach ($devices as $device) {
            $manager->addDevice($device);
        }
        
        $manager->connectAllDevices();
        
        // Collect data over time
        echo "Collecting data over time...\n";
        
        for ($i = 0; $i < 5; $i++) {
            echo "\nReading cycle " . ($i + 1) . ":\n";
            
            $data = $manager->readAllDeviceData();
            
            foreach ($data as $reading) {
                $deviceType = $reading['device_type'];
                
                switch ($deviceType) {
                    case 'temperature_sensor':
                        echo "  Temperature: {$reading['temperature']} {$reading['unit']}\n";
                        break;
                    case 'motion_sensor':
                        echo "  Motion: " . ($reading['motion_detected'] ? 'Detected' : 'None') . "\n";
                        break;
                    case 'smart_light':
                        echo "  Light: " . ($reading['is_on'] ? 'On' : 'Off') . " ({$reading['brightness']}%)\n";
                        break;
                }
            }
            
            sleep(1); // Simulate time between readings
        }
        
        // Analyze collected data
        echo "\nData Analysis:\n";
        $buffer = $manager->getDataBuffer();
        
        $temperatureReadings = array_filter($buffer, fn($data) => $data['device_type'] === 'temperature_sensor');
        $motionReadings = array_filter($buffer, fn($data) => $data['device_type'] === 'motion_sensor');
        
        if (!empty($temperatureReadings)) {
            $temps = array_column($temperatureReadings, 'temperature');
            $avgTemp = array_sum($temps) / count($temps);
            $minTemp = min($temps);
            $maxTemp = max($temps);
            
            echo "Temperature stats:\n";
            echo "  Average: " . round($avgTemp, 2) . "°C\n";
            echo "  Min: " . round($minTemp, 2) . "°C\n";
            echo "  Max: " . round($maxTemp, 2) . "°C\n";
        }
        
        if (!empty($motionReadings)) {
            $motionCount = count(array_filter($motionReadings, fn($data) => $data['motion_detected']));
            echo "Motion detections: $motionCount\n";
        }
        
        // Export data
        echo "\nExporting data...\n";
        $exportData = [
            'timestamp' => time(),
            'device_count' => count($devices),
            'total_readings' => count($buffer),
            'data' => $buffer
        ];
        
        $jsonExport = json_encode($exportData, JSON_PRETTY_PRINT);
        echo "Exported " . strlen($jsonExport) . " bytes of data\n";
        
        $manager->disconnectAllDevices();
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nIoT Best Practices\n";
        echo str_repeat("-", 20) . "\n";
        
        echo "1. Device Management:\n";
        echo "   • Use unique device identifiers\n";
        echo "   • Implement proper device discovery\n";
        echo "   • Maintain device inventory\n";
        echo "   • Use device grouping for management\n";
        echo "   • Monitor device health status\n\n";
        
        echo "2. Communication:\n";
        echo "   • Choose appropriate protocols\n";
        echo "   • Implement message queuing\n";
        echo "   • Use secure communication channels\n";
        echo "   • Handle network disconnections\n";
        echo "   • Implement retry mechanisms\n\n";
        
        echo "3. Data Collection:\n";
        echo "   • Use efficient data formats\n";
        echo "   • Implement data buffering\n";
        echo "   • Validate sensor readings\n";
        echo "   • Handle missing data gracefully\n";
        echo "   • Use data compression\n\n";
        
        echo "4. Security:\n";
        echo "   • Implement device authentication\n";
        echo "   • Use encrypted communication\n";
        echo "   • Secure device credentials\n";
        echo "   • Implement access controls\n";
        echo "   • Regular security updates\n\n";
        
        echo "5. Performance:\n";
        echo "   • Optimize polling intervals\n";
        echo "   • Use batch operations\n";
        echo "   • Implement caching strategies\n";
        echo "   • Monitor resource usage\n";
        echo "   • Use asynchronous operations";
    }
    
    public function runAllExamples(): void
    {
        echo "IoT Fundamentals Examples\n";
        echo str_repeat("=", 25) . "\n";
        
        $this->demonstrateBasicDevices();
        $this->demonstrateDeviceManager();
        $this->demonstrateCommunicationProtocols();
        $this->demonstrateDataCollection();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runIoTFundamentalsDemo(): void
{
    $examples = new IoTFundamentalsExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runIoTFundamentalsDemo();
}
?>
