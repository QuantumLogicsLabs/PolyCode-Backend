<?php
/**
 * IoT Automation in PHP
 * 
 * Automated workflows, triggers, and smart home automation.
 */

// IoT Automation Engine
class IoTAutomationEngine
{
    private array $devices = [];
    private array $triggers = [];
    private array $actions = [];
    private array $workflows = [];
    private array $schedules = [];
    private array $automationHistory = [];
    private bool $isRunning = false;
    
    public function __construct()
    {
        $this->initializeDefaultTriggers();
        $this->initializeDefaultActions();
    }
    
    private function initializeDefaultTriggers(): void
    {
        $this->triggers = [
            'time_based' => TimeBasedTrigger::class,
            'sensor_value' => SensorValueTrigger::class,
            'device_state' => DeviceStateTrigger::class,
            'schedule' => ScheduleTrigger::class,
            'manual' => ManualTrigger::class,
            'webhook' => WebhookTrigger::class
        ];
    }
    
    private function initializeDefaultActions(): void
    {
        $this->actions = [
            'device_control' => DeviceControlAction::class,
            'notification' => NotificationAction::class,
            'data_logging' => DataLoggingAction::class,
            'workflow_trigger' => WorkflowTriggerAction::class,
            'api_call' => ApiCallAction::class,
            'email' => EmailAction::class
        ];
    }
    
    public function addDevice(IoTDevice $device): void
    {
        $this->devices[$device->getId()] = $device;
        echo "Device added to automation engine: {$device->getId()}\n";
    }
    
    public function createWorkflow(string $name, array $workflowConfig): string
    {
        $workflowId = uniqid('workflow_');
        
        $workflow = [
            'id' => $workflowId,
            'name' => $name,
            'enabled' => $workflowConfig['enabled'] ?? true,
            'triggers' => $workflowConfig['triggers'] ?? [],
            'conditions' => $workflowConfig['conditions'] ?? [],
            'actions' => $workflowConfig['actions'] ?? [],
            'created' => time(),
            'last_executed' => null,
            'execution_count' => 0
        ];
        
        $this->workflows[$workflowId] = $workflow;
        
        echo "Workflow created: $name (ID: $workflowId)\n";
        
        return $workflowId;
    }
    
    public function executeWorkflow(string $workflowId, array $context = []): bool
    {
        if (!isset($this->workflows[$workflowId])) {
            echo "Workflow not found: $workflowId\n";
            return false;
        }
        
        $workflow = $this->workflows[$workflowId];
        
        if (!$workflow['enabled']) {
            echo "Workflow is disabled: $workflowId\n";
            return false;
        }
        
        echo "Executing workflow: {$workflow['name']}\n";
        
        // Check triggers
        $triggersMet = $this->evaluateTriggers($workflow['triggers'], $context);
        
        if (!$triggersMet) {
            echo "Triggers not met for workflow: $workflowId\n";
            return false;
        }
        
        // Check conditions
        $conditionsMet = $this->evaluateConditions($workflow['conditions'], $context);
        
        if (!$conditionsMet) {
            echo "Conditions not met for workflow: $workflowId\n";
            return false;
        }
        
        // Execute actions
        $actionResults = $this->executeActions($workflow['actions'], $context);
        
        // Update workflow metadata
        $this->workflows[$workflowId]['last_executed'] = time();
        $this->workflows[$workflowId]['execution_count']++;
        
        // Log execution
        $this->logAutomationEvent('workflow_executed', $workflowId, [
            'triggers_met' => $triggersMet,
            'conditions_met' => $conditionsMet,
            'action_results' => $actionResults,
            'context' => $context
        ]);
        
        echo "Workflow executed successfully: $workflowId\n";
        return true;
    }
    
    private function evaluateTriggers(array $triggers, array $context): bool
    {
        foreach ($triggers as $triggerConfig) {
            $triggerType = $triggerConfig['type'];
            
            if (!isset($this->triggers[$triggerType])) {
                echo "Unknown trigger type: $triggerType\n";
                continue;
            }
            
            $triggerClass = $this->triggers[$triggerType];
            $trigger = new $triggerClass($triggerConfig);
            
            if (!$trigger->evaluate($context, $this->devices)) {
                return false;
            }
        }
        
        return true;
    }
    
    private function evaluateConditions(array $conditions, array $context): bool
    {
        foreach ($conditions as $condition) {
            if (!$this->evaluateCondition($condition, $context)) {
                return false;
            }
        }
        
        return true;
    }
    
    private function evaluateCondition(array $condition, array $context): bool
    {
        $type = $condition['type'];
        $operator = $condition['operator'];
        $value = $condition['value'];
        $deviceId = $condition['device_id'] ?? null;
        
        switch ($type) {
            case 'device_state':
                if (!$deviceId || !isset($this->devices[$deviceId])) {
                    return false;
                }
                
                $device = $this->devices[$deviceId];
                $deviceData = $device->readData();
                
                switch ($operator) {
                    case 'equals':
                        return ($deviceData['status'] ?? '') === $value;
                    case 'not_equals':
                        return ($deviceData['status'] ?? '') !== $value;
                    case 'contains':
                        return strpos($deviceData['status'] ?? '', $value) !== false;
                }
                break;
                
            case 'sensor_value':
                if (!$deviceId || !isset($this->devices[$deviceId])) {
                    return false;
                }
                
                $device = $this->devices[$deviceId];
                $deviceData = $device->readData();
                $sensorValue = $deviceData['value'] ?? null;
                
                if ($sensorValue === null) {
                    return false;
                }
                
                switch ($operator) {
                    case 'greater_than':
                        return $sensorValue > $value;
                    case 'less_than':
                        return $sensorValue < $value;
                    case 'equals':
                        return $sensorValue == $value;
                    case 'between':
                        return $sensorValue >= $value[0] && $sensorValue <= $value[1];
                }
                break;
                
            case 'time':
                $currentTime = date('H:i');
                
                switch ($operator) {
                    case 'equals':
                        return $currentTime === $value;
                    case 'between':
                        return $currentTime >= $value[0] && $currentTime <= $value[1];
                    case 'after':
                        return $currentTime > $value;
                    case 'before':
                        return $currentTime < $value;
                }
                break;
                
            case 'day_of_week':
                $currentDay = date('l');
                return in_array($currentDay, $value);
        }
        
        return false;
    }
    
    private function executeActions(array $actions, array $context): array
    {
        $results = [];
        
        foreach ($actions as $actionConfig) {
            $actionType = $actionConfig['type'];
            
            if (!isset($this->actions[$actionType])) {
                echo "Unknown action type: $actionType\n";
                continue;
            }
            
            $actionClass = $this->actions[$actionType];
            $action = new $actionClass($actionConfig);
            
            $result = $action->execute($context, $this->devices);
            $results[] = $result;
            
            echo "Action executed: $actionType - " . ($result['success'] ? 'Success' : 'Failed') . "\n";
        }
        
        return $results;
    }
    
    public function start(): void
    {
        if ($this->isRunning) {
            echo "Automation engine is already running\n";
            return;
        }
        
        $this->isRunning = true;
        echo "IoT Automation Engine started\n";
        
        // Start monitoring loop
        $this->monitoringLoop();
    }
    
    public function stop(): void
    {
        $this->isRunning = false;
        echo "IoT Automation Engine stopped\n";
    }
    
    private function monitoringLoop(): void
    {
        while ($this->isRunning) {
            // Check all workflows
            foreach ($this->workflows as $workflowId => $workflow) {
                if ($workflow['enabled']) {
                    $context = $this->buildContext();
                    $this->executeWorkflow($workflowId, $context);
                }
            }
            
            // Sleep for monitoring interval
            sleep(5); // Check every 5 seconds
        }
    }
    
    private function buildContext(): array
    {
        $context = [
            'timestamp' => time(),
            'time' => date('H:i:s'),
            'date' => date('Y-m-d'),
            'day_of_week' => date('l'),
            'devices' => []
        ];
        
        // Collect device data
        foreach ($this->devices as $deviceId => $device) {
            if ($device->isConnected()) {
                $context['devices'][$deviceId] = $device->readData();
            }
        }
        
        return $context;
    }
    
    public function createSchedule(string $name, array $scheduleConfig): string
    {
        $scheduleId = uniqid('schedule_');
        
        $schedule = [
            'id' => $scheduleId,
            'name' => $name,
            'type' => $scheduleConfig['type'], // 'once', 'daily', 'weekly', 'monthly'
            'time' => $scheduleConfig['time'],
            'days' => $scheduleConfig['days'] ?? [],
            'workflow_id' => $scheduleConfig['workflow_id'],
            'enabled' => $scheduleConfig['enabled'] ?? true,
            'created' => time(),
            'last_run' => null,
            'next_run' => $this->calculateNextRun($scheduleConfig)
        ];
        
        $this->schedules[$scheduleId] = $schedule;
        
        echo "Schedule created: $name (ID: $scheduleId)\n";
        
        return $scheduleId;
    }
    
    private function calculateNextRun(array $scheduleConfig): int
    {
        $currentTime = time();
        $scheduleTime = strtotime($scheduleConfig['time'], $currentTime);
        
        switch ($scheduleConfig['type']) {
            case 'once':
                return $scheduleTime > $currentTime ? $scheduleTime : $currentTime + 86400;
                
            case 'daily':
                return $scheduleTime > $currentTime ? $scheduleTime : $scheduleTime + 86400;
                
            case 'weekly':
                $targetDay = $scheduleConfig['days'][0] ?? 'Monday';
                $nextRun = strtotime("next $targetDay " . $scheduleConfig['time'], $currentTime);
                return $nextRun;
                
            case 'monthly':
                return strtotime('+1 month ' . $scheduleConfig['time'], $currentTime);
                
            default:
                return $currentTime + 3600; // Default: 1 hour from now
        }
    }
    
    public function checkSchedules(): void
    {
        $currentTime = time();
        
        foreach ($this->schedules as $scheduleId => $schedule) {
            if (!$schedule['enabled']) {
                continue;
            }
            
            if ($currentTime >= $schedule['next_run']) {
                echo "Executing scheduled workflow: {$schedule['name']}\n";
                
                $this->executeWorkflow($schedule['workflow_id']);
                
                // Update schedule
                $this->schedules[$scheduleId]['last_run'] = $currentTime;
                $this->schedules[$scheduleId]['next_run'] = $this->calculateNextRun($schedule);
                
                $this->logAutomationEvent('schedule_executed', $scheduleId, [
                    'workflow_id' => $schedule['workflow_id'],
                    'execution_time' => $currentTime
                ]);
                
                // Disable one-time schedules
                if ($schedule['type'] === 'once') {
                    $this->schedules[$scheduleId]['enabled'] = false;
                }
            }
        }
    }
    
    public function getWorkflows(): array
    {
        return $this->workflows;
    }
    
    public function getSchedules(): array
    {
        return $this->schedules;
    }
    
    public function getAutomationHistory(): array
    {
        return $this->automationHistory;
    }
    
    public function enableWorkflow(string $workflowId): bool
    {
        if (!isset($this->workflows[$workflowId])) {
            return false;
        }
        
        $this->workflows[$workflowId]['enabled'] = true;
        echo "Workflow enabled: $workflowId\n";
        
        return true;
    }
    
    public function disableWorkflow(string $workflowId): bool
    {
        if (!isset($this->workflows[$workflowId])) {
            return false;
        }
        
        $this->workflows[$workflowId]['enabled'] = false;
        echo "Workflow disabled: $workflowId\n";
        
        return true;
    }
    
    private function logAutomationEvent(string $event, string $targetId, array $details): void
    {
        $this->automationHistory[] = [
            'event' => $event,
            'target_id' => $targetId,
            'details' => $details,
            'timestamp' => time()
        ];
        
        // Keep history size manageable
        if (count($this->automationHistory) > 1000) {
            array_shift($this->automationHistory);
        }
    }
}

// Trigger Interface
interface Trigger
{
    public function evaluate(array $context, array $devices): bool;
}

// Time-based Trigger
class TimeBasedTrigger implements Trigger
{
    private array $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function evaluate(array $context, array $devices): bool
    {
        $time = $this->config['time'];
        $currentTime = $context['time'];
        
        switch ($this->config['operator']) {
            case 'equals':
                return $currentTime === $time;
            case 'after':
                return $currentTime > $time;
            case 'before':
                return $currentTime < $time;
            case 'between':
                return $currentTime >= $time[0] && $currentTime <= $time[1];
        }
        
        return false;
    }
}

// Sensor Value Trigger
class SensorValueTrigger implements Trigger
{
    private array $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function evaluate(array $context, array $devices): bool
    {
        $deviceId = $this->config['device_id'];
        $operator = $this->config['operator'];
        $value = $this->config['value'];
        
        if (!isset($devices[$deviceId])) {
            return false;
        }
        
        $device = $devices[$deviceId];
        $deviceData = $device->readData();
        $sensorValue = $deviceData['value'] ?? null;
        
        if ($sensorValue === null) {
            return false;
        }
        
        switch ($operator) {
            case 'greater_than':
                return $sensorValue > $value;
            case 'less_than':
                return $sensorValue < $value;
            case 'equals':
                return $sensorValue == $value;
            case 'between':
                return $sensorValue >= $value[0] && $sensorValue <= $value[1];
            case 'changed':
                return isset($deviceData['previous_value']) && $sensorValue != $deviceData['previous_value'];
        }
        
        return false;
    }
}

// Device State Trigger
class DeviceStateTrigger implements Trigger
{
    private array $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function evaluate(array $context, array $devices): bool
    {
        $deviceId = $this->config['device_id'];
        $state = $this->config['state'];
        
        if (!isset($devices[$deviceId])) {
            return false;
        }
        
        $device = $devices[$deviceId];
        $deviceData = $device->readData();
        $deviceState = $deviceData['status'] ?? 'offline';
        
        switch ($this->config['operator']) {
            case 'equals':
                return $deviceState === $state;
            case 'not_equals':
                return $deviceState !== $state;
            case 'contains':
                return strpos($deviceState, $state) !== false;
        }
        
        return false;
    }
}

// Schedule Trigger
class ScheduleTrigger implements Trigger
{
    private array $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function evaluate(array $context, array $devices): bool
    {
        $schedule = $this->config['schedule'];
        $currentDay = $context['day_of_week'];
        $currentTime = $context['time'];
        
        if (isset($schedule['days']) && !in_array($currentDay, $schedule['days'])) {
            return false;
        }
        
        if (isset($schedule['time'])) {
            return $currentTime === $schedule['time'];
        }
        
        return false;
    }
}

// Manual Trigger
class ManualTrigger implements Trigger
{
    private array $config;
    private bool $triggered = false;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function evaluate(array $context, array $devices): bool
    {
        $result = $this->triggered;
        $this->triggered = false; // Reset after evaluation
        return $result;
    }
    
    public function trigger(): void
    {
        $this->triggered = true;
    }
}

// Webhook Trigger
class WebhookTrigger implements Trigger
{
    private array $config;
    private bool $triggered = false;
    private array $lastPayload;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function evaluate(array $context, array $devices): bool
    {
        $result = $this->triggered;
        $this->triggered = false; // Reset after evaluation
        return $result;
    }
    
    public function receiveWebhook(array $payload): void
    {
        $this->lastPayload = $payload;
        $this->triggered = true;
    }
    
    public function getLastPayload(): array
    {
        return $this->lastPayload ?? [];
    }
}

// Action Interface
interface Action
{
    public function execute(array $context, array $devices): array;
}

// Device Control Action
class DeviceControlAction implements Action
{
    private array $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function execute(array $context, array $devices): array
    {
        $deviceId = $this->config['device_id'];
        $command = $this->config['command'];
        $parameters = $this->config['parameters'] ?? [];
        
        if (!isset($devices[$deviceId])) {
            return ['success' => false, 'error' => 'Device not found'];
        }
        
        $device = $devices[$deviceId];
        
        try {
            $success = $device->sendCommand($command, $parameters);
            
            return [
                'success' => $success,
                'device_id' => $deviceId,
                'command' => $command,
                'parameters' => $parameters,
                'timestamp' => time()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'device_id' => $deviceId,
                'command' => $command
            ];
        }
    }
}

// Notification Action
class NotificationAction implements Action
{
    private array $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function execute(array $context, array $devices): array
    {
        $message = $this->config['message'];
        $type = $this->config['type'] ?? 'info';
        $recipients = $this->config['recipients'] ?? [];
        
        // Process message template
        $processedMessage = $this->processMessageTemplate($message, $context);
        
        echo "Notification sent: $processedMessage\n";
        
        return [
            'success' => true,
            'message' => $processedMessage,
            'type' => $type,
            'recipients' => $recipients,
            'timestamp' => time()
        ];
    }
    
    private function processMessageTemplate(string $message, array $context): string
    {
        foreach ($context as $key => $value) {
            if (is_string($value)) {
                $message = str_replace("{{$key}}", $value, $message);
            }
        }
        
        return $message;
    }
}

// Data Logging Action
class DataLoggingAction implements Action
{
    private array $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function execute(array $context, array $devices): array
    {
        $logData = $this->config['data'] ?? $context;
        $logLevel = $this->config['level'] ?? 'info';
        
        $logEntry = [
            'timestamp' => time(),
            'level' => $logLevel,
            'data' => $logData
        ];
        
        // Simulate logging
        echo "Data logged: " . json_encode($logEntry) . "\n";
        
        return [
            'success' => true,
            'log_entry' => $logEntry,
            'timestamp' => time()
        ];
    }
}

// Workflow Trigger Action
class WorkflowTriggerAction implements Action
{
    private array $config;
    private IoTAutomationEngine $engine;
    
    public function __construct(array $config, IoTAutomationEngine $engine = null)
    {
        $this->config = $config;
        $this->engine = $engine;
    }
    
    public function execute(array $context, array $devices): array
    {
        $workflowId = $this->config['workflow_id'];
        
        if (!$this->engine) {
            return ['success' => false, 'error' => 'Automation engine not available'];
        }
        
        $success = $this->engine->executeWorkflow($workflowId, $context);
        
        return [
            'success' => $success,
            'triggered_workflow' => $workflowId,
            'timestamp' => time()
        ];
    }
}

// API Call Action
class ApiCallAction implements Action
{
    private array $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function execute(array $context, array $devices): array
    {
        $url = $this->config['url'];
        $method = $this->config['method'] ?? 'POST';
        $data = $this->config['data'] ?? [];
        $headers = $this->config['headers'] ?? [];
        
        // Process data template
        $processedData = $this->processDataTemplate($data, $context);
        
        echo "API call: $method $url\n";
        echo "Data: " . json_encode($processedData) . "\n";
        
        // Simulate API call
        $response = [
            'status' => 'success',
            'message' => 'API call completed',
            'timestamp' => time()
        ];
        
        return [
            'success' => true,
            'url' => $url,
            'method' => $method,
            'data' => $processedData,
            'response' => $response,
            'timestamp' => time()
        ];
    }
    
    private function processDataTemplate(array $data, array $context): array
    {
        $processed = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                foreach ($context as $contextKey => $contextValue) {
                    if (is_string($contextValue)) {
                        $value = str_replace("{{$contextKey}}", $contextValue, $value);
                    }
                }
            }
            $processed[$key] = $value;
        }
        
        return $processed;
    }
}

// Email Action
class EmailAction implements Action
{
    private array $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function execute(array $context, array $devices): array
    {
        $to = $this->config['to'];
        $subject = $this->config['subject'];
        $body = $this->config['body'];
        
        // Process templates
        $processedSubject = $this->processTemplate($subject, $context);
        $processedBody = $this->processTemplate($body, $context);
        
        echo "Email sent to: $to\n";
        echo "Subject: $processedSubject\n";
        echo "Body: $processedBody\n";
        
        return [
            'success' => true,
            'to' => $to,
            'subject' => $processedSubject,
            'body' => $processedBody,
            'timestamp' => time()
        ];
    }
    
    private function processTemplate(string $template, array $context): string
    {
        foreach ($context as $key => $value) {
            if (is_string($value)) {
                $template = str_replace("{{$key}}", $value, $template);
            }
        }
        
        return $template;
    }
}

// IoT Automation Examples
class IoTAutomationExamples
{
    public function demonstrateBasicAutomation(): void
    {
        echo "Basic IoT Automation Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $engine = new IoTAutomationEngine();
        
        // Add devices
        $tempSensor = new TemperatureSensor('temp_001');
        $motionSensor = new MotionSensor('motion_001');
        $smartLight = new SmartLight('light_001');
        
        $engine->addDevice($tempSensor);
        $engine->addDevice($motionSensor);
        $engine->addDevice($smartLight);
        
        // Connect devices
        $tempSensor->connect();
        $motionSensor->connect();
        $smartLight->connect();
        
        // Create simple automation workflow
        echo "\nCreating automation workflow...\n";
        
        $workflowConfig = [
            'enabled' => true,
            'triggers' => [
                [
                    'type' => 'sensor_value',
                    'device_id' => 'temp_001',
                    'operator' => 'greater_than',
                    'value' => 25.0
                ]
            ],
            'conditions' => [
                [
                    'type' => 'device_state',
                    'device_id' => 'light_001',
                    'operator' => 'equals',
                    'value' => 'offline'
                ]
            ],
            'actions' => [
                [
                    'type' => 'device_control',
                    'device_id' => 'light_001',
                    'command' => 'turn_on'
                ],
                [
                    'type' => 'notification',
                    'message' => 'Temperature is high: {temp_001.value}°C. Light turned on.',
                    'type' => 'warning'
                ]
            ]
        ];
        
        $workflowId = $engine->createWorkflow('Temperature Control', $workflowConfig);
        
        // Simulate trigger condition
        echo "\nSimulating high temperature...\n";
        $context = [
            'timestamp' => time(),
            'devices' => [
                'temp_001' => [
                    'device_id' => 'temp_001',
                    'value' => 26.5,
                    'status' => 'normal'
                ],
                'light_001' => [
                    'device_id' => 'light_001',
                    'status' => 'offline'
                ]
            ]
        ];
        
        $engine->executeWorkflow($workflowId, $context);
        
        // Create motion-based automation
        echo "\nCreating motion-based automation...\n";
        
        $motionWorkflowConfig = [
            'enabled' => true,
            'triggers' => [
                [
                    'type' => 'sensor_value',
                    'device_id' => 'motion_001',
                    'operator' => 'equals',
                    'value' => true
                ]
            ],
            'actions' => [
                [
                    'type' => 'device_control',
                    'device_id' => 'light_001',
                    'command' => 'turn_on'
                ],
                [
                    'type' => 'device_control',
                    'device_id' => 'light_001',
                    'command' => 'set_brightness',
                    'parameters' => ['brightness' => 80]
                ]
            ]
        ];
        
        $motionWorkflowId = $engine->createWorkflow('Motion Lighting', $motionWorkflowConfig);
        
        // Simulate motion detection
        echo "\nSimulating motion detection...\n";
        $motionContext = [
            'timestamp' => time(),
            'devices' => [
                'motion_001' => [
                    'device_id' => 'motion_001',
                    'value' => true,
                    'status' => 'normal'
                ]
            ]
        ];
        
        $engine->executeWorkflow($motionWorkflowId, $motionContext);
        
        // Show workflow statistics
        echo "\nWorkflow Statistics:\n";
        $workflows = $engine->getWorkflows();
        
        foreach ($workflows as $id => $workflow) {
            echo "  {$workflow['name']}:\n";
            echo "    Executions: {$workflow['execution_count']}\n";
            echo "    Last executed: " . ($workflow['last_executed'] ? date('H:i:s', $workflow['last_executed']) : 'Never') . "\n";
            echo "    Enabled: " . ($workflow['enabled'] ? 'Yes' : 'No') . "\n";
        }
    }
    
    public function demonstrateScheduledAutomation(): void
    {
        echo "\nScheduled Automation Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $engine = new IoTAutomationEngine();
        
        // Add devices
        $smartLight = new SmartLight('light_001');
        $engine->addDevice($smartLight);
        $smartLight->connect();
        
        // Create lighting control workflow
        $lightingWorkflowConfig = [
            'enabled' => true,
            'triggers' => [],
            'conditions' => [],
            'actions' => [
                [
                    'type' => 'device_control',
                    'device_id' => 'light_001',
                    'command' => 'turn_on'
                ],
                [
                    'type' => 'device_control',
                    'device_id' => 'light_001',
                    'command' => 'set_brightness',
                    'parameters' => ['brightness' => 60]
                ]
            ]
        ];
        
        $lightingWorkflowId = $engine->createWorkflow('Evening Lighting', $lightingWorkflowConfig);
        
        // Create schedules
        echo "Creating schedules...\n";
        
        // Daily evening lighting
        $engine->createSchedule('Evening Lights', [
            'type' => 'daily',
            'time' => '18:00',
            'workflow_id' => $lightingWorkflowId
        ]);
        
        // Weekend morning lighting
        $engine->createSchedule('Weekend Morning', [
            'type' => 'weekly',
            'time' => '08:00',
            'days' => ['Saturday', 'Sunday'],
            'workflow_id' => $lightingWorkflowId
        ]);
        
        // One-time special event
        $engine->createSchedule('Special Event', [
            'type' => 'once',
            'time' => date('H:i', strtotime('+2 minutes')),
            'workflow_id' => $lightingWorkflowId
        ]);
        
        // Show schedules
        echo "\nActive schedules:\n";
        $schedules = $engine->getSchedules();
        
        foreach ($schedules as $id => $schedule) {
            echo "  {$schedule['name']}:\n";
            echo "    Type: {$schedule['type']}\n";
            echo "    Time: {$schedule['time']}\n";
            echo "    Next run: " . date('Y-m-d H:i:s', $schedule['next_run']) . "\n";
            echo "    Enabled: " . ($schedule['enabled'] ? 'Yes' : 'No') . "\n";
        }
        
        // Check schedules (simulate)
        echo "\nChecking schedules...\n";
        $engine->checkSchedules();
        
        // Show updated schedules
        echo "\nUpdated schedules:\n";
        $schedules = $engine->getSchedules();
        
        foreach ($schedules as $id => $schedule) {
            if ($schedule['last_run']) {
                echo "  {$schedule['name']}: Last run at " . date('H:i:s', $schedule['last_run']) . "\n";
            }
        }
    }
    
    public function demonstrateComplexAutomation(): void
    {
        echo "\nComplex IoT Automation Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $engine = new IoTAutomationEngine();
        
        // Add multiple devices
        $devices = [
            new TemperatureSensor('temp_001'),
            new TemperatureSensor('temp_002'),
            new MotionSensor('motion_001'),
            new MotionSensor('motion_002'),
            new SmartLight('light_001'),
            new SmartLight('light_002')
        ];
        
        foreach ($devices as $device) {
            $engine->addDevice($device);
            $device->connect();
        }
        
        // Create multi-condition workflow
        echo "Creating complex automation workflow...\n";
        
        $securityWorkflowConfig = [
            'enabled' => true,
            'triggers' => [
                [
                    'type' => 'sensor_value',
                    'device_id' => 'motion_001',
                    'operator' => 'equals',
                    'value' => true
                ]
            ],
            'conditions' => [
                [
                    'type' => 'time',
                    'operator' => 'between',
                    'value' => ['22:00', '06:00']
                ],
                [
                    'type' => 'sensor_value',
                    'device_id' => 'temp_001',
                    'operator' => 'less_than',
                    'value' => 20.0
                ]
            ],
            'actions' => [
                [
                    'type' => 'device_control',
                    'device_id' => 'light_001',
                    'command' => 'turn_on'
                ],
                [
                    'type' => 'device_control',
                    'device_id' => 'light_001',
                    'command' => 'set_color',
                    'parameters' => ['color' => 'red']
                ],
                [
                    'type' => 'notification',
                    'message' => 'Security alert: Motion detected at {time} when temperature is {temp_001.value}°C',
                    'type' => 'alert'
                ],
                [
                    'type' => 'email',
                    'to' => 'security@example.com',
                    'subject' => 'IoT Security Alert',
                    'body' => 'Motion detected at {time}. Temperature: {temp_001.value}°C'
                ]
            ]
        ];
        
        $securityWorkflowId = $engine->createWorkflow('Night Security', $securityWorkflowConfig);
        
        // Create energy management workflow
        $energyWorkflowConfig = [
            'enabled' => true,
            'triggers' => [
                [
                    'type' => 'sensor_value',
                    'device_id' => 'temp_001',
                    'operator' => 'greater_than',
                    'value' => 23.0
                ]
            ],
            'conditions' => [
                [
                    'type' => 'device_state',
                    'device_id' => 'light_001',
                    'operator' => 'equals',
                    'value' => 'normal'
                ],
                [
                    'type' => 'device_state',
                    'device_id' => 'light_002',
                    'operator' => 'equals',
                    'value' => 'normal'
                ]
            ],
            'actions' => [
                [
                    'type' => 'device_control',
                    'device_id' => 'light_001',
                    'command' => 'set_brightness',
                    'parameters' => ['brightness' => 40]
                ],
                [
                    'type' => 'device_control',
                    'device_id' => 'light_002',
                    'command' => 'set_brightness',
                    'parameters' => ['brightness' => 40]
                ],
                [
                    'type' => 'data_logging',
                    'level' => 'info',
                    'data' => ['event' => 'energy_saving_mode', 'trigger' => 'high_temperature']
                ]
            ]
        ];
        
        $energyWorkflowId = $engine->createWorkflow('Energy Management', $energyWorkflowConfig);
        
        // Test complex scenario
        echo "\nTesting complex automation scenario...\n";
        
        $complexContext = [
            'timestamp' => time(),
            'time' => '23:30',
            'devices' => [
                'motion_001' => [
                    'device_id' => 'motion_001',
                    'value' => true,
                    'status' => 'normal'
                ],
                'temp_001' => [
                    'device_id' => 'temp_001',
                    'value' => 18.5,
                    'status' => 'normal'
                ],
                'light_001' => [
                    'device_id' => 'light_001',
                    'status' => 'normal'
                ],
                'light_002' => [
                    'device_id' => 'light_002',
                    'status' => 'normal'
                ]
            ]
        ];
        
        $engine->executeWorkflow($securityWorkflowId, $complexContext);
        
        // Show automation history
        echo "\nAutomation History:\n";
        $history = $engine->getAutomationHistory();
        
        foreach (array_slice($history, -5) as $event) {
            echo "  {$event['event']}: {$event['target_id']} at " . date('H:i:s', $event['timestamp']) . "\n";
        }
        
        // Show workflow performance
        echo "\nWorkflow Performance:\n";
        $workflows = $engine->getWorkflows();
        
        foreach ($workflows as $id => $workflow) {
            echo "  {$workflow['name']}:\n";
            echo "    Executions: {$workflow['execution_count']}\n";
            echo "    Success rate: " . ($this->calculateSuccessRate($id, $history) * 100) . "%\n";
        }
    }
    
    private function calculateSuccessRate(string $workflowId, array $history): float
    {
        $workflowEvents = array_filter($history, fn($event) => $event['target_id'] === $workflowId);
        $totalEvents = count($workflowEvents);
        
        if ($totalEvents === 0) {
            return 0.0;
        }
        
        $successfulEvents = array_filter($workflowEvents, fn($event) => 
            isset($event['details']['action_results']) && 
            array_reduce($event['details']['action_results'], fn($carry, $result) => $carry && ($result['success'] ?? false), true)
        );
        
        return count($successfulEvents) / $totalEvents;
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nIoT Automation Best Practices\n";
        echo str_repeat("-", 35) . "\n";
        
        echo "1. Workflow Design:\n";
        echo "   • Keep workflows simple and focused\n";
        echo "   • Use clear naming conventions\n";
        echo "   • Implement proper error handling\n";
        echo "   • Add logging and monitoring\n";
        echo "   • Test workflows thoroughly\n\n";
        
        echo "2. Trigger Configuration:\n";
        echo "   • Use reliable trigger sources\n";
        echo "   • Implement debouncing for noisy sensors\n";
        echo "   • Set appropriate thresholds\n";
        echo "   • Consider trigger combinations\n";
        echo "   • Handle trigger failures gracefully\n\n";
        
        echo "3. Action Implementation:\n";
        echo "   • Implement retry mechanisms\n";
        echo "   • Use timeout handling\n";
        echo "   • Validate action parameters\n";
        echo "   • Log action results\n";
        echo "   • Handle action failures\n\n";
        
        echo "4. Performance Optimization:\n";
        echo "   • Minimize workflow execution time\n";
        echo "   • Use efficient condition checks\n";
        echo "   • Implement caching where appropriate\n";
        echo "   • Monitor resource usage\n";
        echo "   • Optimize database queries\n\n";
        
        echo "5. Security Considerations:\n";
        echo "   • Validate all inputs\n";
        echo "   • Implement access controls\n";
        echo "   • Use secure communication\n";
        echo "   • Log security events\n";
        echo "   • Regular security audits\n\n";
        
        echo "6. Maintenance:\n";
        echo "   • Regular workflow reviews\n";
        echo "   • Update outdated configurations\n";
        echo "   • Monitor automation performance\n";
        echo "   • Document workflows\n";
        echo "   • Backup automation configurations";
    }
    
    public function runAllExamples(): void
    {
        echo "IoT Automation Examples\n";
        echo str_repeat("=", 25) . "\n";
        
        $this->demonstrateBasicAutomation();
        $this->demonstrateScheduledAutomation();
        $this->demonstrateComplexAutomation();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runIoTAutomationDemo(): void
{
    $examples = new IoTAutomationExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runIoTAutomationDemo();
}
?>
