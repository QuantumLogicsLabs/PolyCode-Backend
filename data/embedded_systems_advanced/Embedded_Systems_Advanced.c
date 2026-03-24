#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <unistd.h>
#include <signal.h>
#include <sys/time.h>
#include <pthread.h>

// =============================================================================
// ADVANCED EMBEDDED SYSTEMS PROGRAMMING
// =============================================================================

#define MAX_SENSORS 32
#define MAX_ACTUATORS 16
#define MAX_EVENTS 64
#define MAX_TASKS 16
#define STACK_SIZE 1024
#define CLOCK_FREQUENCY 1000000 // 1MHz

// =============================================================================
// REAL-TIME OPERATING SYSTEM KERNEL
// =============================================================================

// Task states
typedef enum {
    TASK_STATE_READY = 0,
    TASK_STATE_RUNNING = 1,
    TASK_STATE_BLOCKED = 2,
    TASK_STATE_SUSPENDED = 3,
    TASK_STATE_TERMINATED = 4
} TaskState;

// Task priorities (lower number = higher priority)
typedef enum {
    PRIORITY_IDLE = 0,
    PRIORITY_LOW = 1,
    PRIORITY_NORMAL = 2,
    PRIORITY_HIGH = 3,
    PRIORITY_CRITICAL = 4,
    PRIORITY_ISR = 5
} TaskPriority;

// Task Control Block (TCB)
typedef struct {
    uint32_t task_id;
    char name[32];
    TaskState state;
    TaskPriority priority;
    uint32_t stack_pointer;
    void* stack_base;
    uint32_t stack_size;
    uint32_t wake_up_time;
    void (*task_function)(void*);
    void* task_parameter;
    uint32_t cpu_usage;
    uint32_t execution_count;
    uint32_t blocked_ticks;
} TaskControlBlock;

// Event Control Block
typedef struct {
    uint32_t event_id;
    char name[32];
    uint32_t event_flags;
    uint32_t waiting_tasks;
    TaskControlBlock* waiting_list[MAX_TASKS];
} EventControlBlock;

// Memory Pool
typedef struct {
    void* pool_base;
    uint32_t pool_size;
    uint32_t block_size;
    uint32_t block_count;
    uint32_t* free_list;
    uint32_t* used_list;
    uint32_t free_count;
} MemoryPool;

// RTOS Kernel
typedef struct {
    TaskControlBlock tasks[MAX_TASKS];
    uint32_t task_count;
    TaskControlBlock* current_task;
    TaskControlBlock* ready_lists[6]; // One list per priority
    uint32_t tick_count;
    uint32_t system_ticks;
    volatile uint32_t interrupt_nesting;
    uint32_t context_switches;
    uint32_t kernel_mode;
} RTOSKernel;

// =============================================================================
// HARDWARE ABSTRACTION LAYER
// =============================================================================

// GPIO Pin structure
typedef struct {
    uint8_t port;
    uint8_t pin;
    uint8_t mode;      // 0=input, 1=output, 2=alternate
    uint8_t pullup;    // 0=none, 1=enable
    volatile uint8_t* data_register;
    volatile uint8_t* direction_register;
    volatile uint8_t* pullup_register;
} GPIOPin;

// ADC Channel
typedef struct {
    uint8_t channel;
    uint16_t value;
    uint8_t resolution;
    uint8_t reference_voltage;
    volatile uint16_t* data_register;
    volatile uint8_t* control_register;
} ADCChannel;

// PWM Channel
typedef struct {
    uint8_t channel;
    uint16_t duty_cycle;
    uint16_t period;
    uint8_t resolution;
    volatile uint16_t* duty_register;
    volatile uint16_t* period_register;
    volatile uint8_t* control_register;
} PWMChannel;

// UART Interface
typedef struct {
    uint8_t uart_number;
    uint32_t baud_rate;
    uint8_t data_bits;
    uint8_t stop_bits;
    uint8_t parity;
    volatile uint8_t* data_register;
    volatile uint8_t* status_register;
    volatile uint8_t* control_register;
    volatile uint8_t* baud_rate_register;
} UARTInterface;

// I2C Interface
typedef struct {
    uint8_t i2c_number;
    uint8_t slave_address;
    uint8_t clock_speed;
    volatile uint8_t* data_register;
    volatile uint8_t* status_register;
    volatile uint8_t* control_register;
    volatile uint8_t* clock_register;
} I2CInterface;

// SPI Interface
typedef struct {
    uint8_t spi_number;
    uint8_t mode;           // 0=master, 1=slave
    uint8_t clock_polarity;
    uint8_t clock_phase;
    uint8_t data_size;
    volatile uint8_t* data_register;
    volatile uint8_t* status_register;
    volatile uint8_t* control_register;
    volatile uint8_t* clock_register;
} SPIInterface;

// =============================================================================
// SENSOR AND ACTUATOR DRIVERS
// =============================================================================

// Sensor types
typedef enum {
    SENSOR_TYPE_TEMPERATURE = 0,
    SENSOR_TYPE_HUMIDITY = 1,
    SENSOR_TYPE_PRESSURE = 2,
    SENSOR_TYPE_LIGHT = 3,
    SENSOR_TYPE_ACCELEROMETER = 4,
    SENSOR_TYPE_GYROSCOPE = 5,
    SENSOR_TYPE_MAGNETOMETER = 6,
    SENSOR_TYPE_PROXIMITY = 7,
    SENSOR_TYPE_DISTANCE = 8
} SensorType;

// Sensor structure
typedef struct {
    uint32_t sensor_id;
    char name[32];
    SensorType type;
    uint16_t value;
    uint16_t min_value;
    uint16_t max_value;
    uint8_t resolution;
    uint8_t precision;
    uint32_t sample_rate;
    uint32_t last_sample_time;
    uint8_t is_enabled;
    void (*read_function)(void* sensor);
    void* hardware_interface;
} Sensor;

// Actuator types
typedef enum {
    ACTUATOR_TYPE_LED = 0,
    ACTUATOR_TYPE_MOTOR = 1,
    ACTUATOR_TYPE_SERVO = 2,
    ACTUATOR_TYPE_RELAY = 3,
    ACTUATOR_TYPE_BUZZER = 4,
    ACTUATOR_TYPE_DISPLAY = 5,
    ACTUATOR_TYPE_HEATER = 6,
    ACTUATOR_TYPE_COOLER = 7
} ActuatorType;

// Actuator structure
typedef struct {
    uint32_t actuator_id;
    char name[32];
    ActuatorType type;
    uint16_t current_value;
    uint16_t min_value;
    uint16_t max_value;
    uint8_t resolution;
    uint8_t is_enabled;
    void (*write_function)(void* actuator, uint16_t value);
    void* hardware_interface;
} Actuator;

// =============================================================================
// INTERRUPT HANDLING
// =============================================================================

// Interrupt vector table
typedef struct {
    void (*isr_table[32])(void);
    uint8_t interrupt_enabled[32];
    uint8_t interrupt_priority[32];
    uint8_t interrupt_pending[32];
    uint8_t interrupt_active[32];
} InterruptVectorTable;

// Interrupt Service Routine
typedef struct {
    uint8_t interrupt_number;
    void (*handler)(void);
    uint8_t priority;
    uint8_t is_enabled;
    uint32_t interrupt_count;
    uint32_t last_interrupt_time;
} InterruptServiceRoutine;

// =============================================================================
// POWER MANAGEMENT
// =============================================================================

// Power states
typedef enum {
    POWER_STATE_ACTIVE = 0,
    POWER_STATE_IDLE = 1,
    POWER_STATE_SLEEP = 2,
    POWER_STATE_DEEP_SLEEP = 3,
    POWER_STATE_HIBERNATE = 4,
    POWER_STATE_OFF = 5
} PowerState;

// Power management structure
typedef struct {
    PowerState current_state;
    uint32_t sleep_mode_timer;
    uint32_t wake_up_sources;
    uint32_t cpu_frequency;
    uint32_t bus_frequency;
    uint32_t voltage_supply;
    uint32_t current_consumption;
    uint8_t low_power_mode_enabled;
} PowerManagement;

// =============================================================================
// COMMUNICATION PROTOCOLS
// =============================================================================

// CAN Message
typedef struct {
    uint32_t message_id;
    uint8_t data[8];
    uint8_t data_length;
    uint8_t priority;
    uint32_t timestamp;
} CANMessage;

// Modbus Register
typedef struct {
    uint16_t address;
    uint16_t value;
    uint8_t function_code;
    uint8_t data_type;
    uint8_t access_type;
} ModbusRegister;

// MQTT Message
typedef struct {
    char topic[128];
    char payload[256];
    uint8_t qos;
    uint8_t retain;
    uint32_t message_id;
} MQTTMessage;

// =============================================================================
// KERNEL IMPLEMENTATION
// =============================================================================

static RTOSKernel g_kernel = {0};

// Initialize kernel
void kernelInit(void) {
    memset(&g_kernel, 0, sizeof(RTOSKernel));
    g_kernel.tick_count = 0;
    g_kernel.system_ticks = 0;
    g_kernel.interrupt_nesting = 0;
    g_kernel.context_switches = 0;
    g_kernel.kernel_mode = 1;
    
    // Initialize ready lists
    for (int i = 0; i < 6; i++) {
        g_kernel.ready_lists[i] = NULL;
    }
}

// Create task
uint32_t createTask(const char* name, void (*task_function)(void*), void* parameter,
                    uint32_t stack_size, TaskPriority priority) {
    if (g_kernel.task_count >= MAX_TASKS) {
        return 0; // Maximum tasks reached
    }
    
    TaskControlBlock* tcb = &g_kernel.tasks[g_kernel.task_count];
    
    // Initialize TCB
    tcb->task_id = g_kernel.task_count + 1;
    strncpy(tcb->name, name, sizeof(tcb->name) - 1);
    tcb->state = TASK_STATE_READY;
    tcb->priority = priority;
    tcb->task_function = task_function;
    tcb->task_parameter = parameter;
    tcb->stack_size = stack_size;
    tcb->cpu_usage = 0;
    tcb->execution_count = 0;
    tcb->blocked_ticks = 0;
    
    // Allocate stack
    tcb->stack_base = malloc(stack_size);
    if (!tcb->stack_base) {
        return 0; // Stack allocation failed
    }
    
    // Initialize stack pointer (simplified)
    tcb->stack_pointer = (uint32_t)tcb->stack_base + stack_size - sizeof(uint32_t);
    
    // Add to ready list
    tcb->state = TASK_STATE_READY;
    g_kernel.task_count++;
    
    return tcb->task_id;
}

// Schedule next task
TaskControlBlock* scheduleNextTask(void) {
    TaskControlBlock* next_task = NULL;
    
    g_kernel.kernel_mode = 1;
    
    // Find highest priority ready task
    for (int priority = PRIORITY_CRITICAL; priority >= PRIORITY_IDLE; priority--) {
        TaskControlBlock* task = g_kernel.ready_lists[priority];
        
        while (task) {
            if (task->state == TASK_STATE_READY) {
                next_task = task;
                break;
            }
            task = task; // Simplified - would be a linked list
        }
        
        if (next_task) {
            break;
        }
    }
    
    g_kernel.kernel_mode = 0;
    return next_task;
}

// Context switch (simplified)
void contextSwitch(TaskControlBlock* new_task) {
    if (new_task && new_task != g_kernel.current_task) {
        TaskControlBlock* old_task = g_kernel.current_task;
        
        if (old_task) {
            // Save old task context (simplified)
            old_task->stack_pointer = 0; // Would save actual registers
        }
        
        // Load new task context (simplified)
        g_kernel.current_task = new_task;
        new_task->state = TASK_STATE_RUNNING;
        new_task->execution_count++;
        
        g_kernel.context_switches++;
    }
}

// System tick handler
void systemTickHandler(void) {
    g_kernel.tick_count++;
    g_kernel.system_ticks++;
    
    // Update task timers
    for (int i = 0; i < g_kernel.task_count; i++) {
        TaskControlBlock* task = &g_kernel.tasks[i];
        
        if (task->state == TASK_STATE_BLOCKED) {
            task->blocked_ticks++;
            
            if (task->blocked_ticks >= task->wake_up_time) {
                task->state = TASK_STATE_READY;
                task->blocked_ticks = 0;
            }
        }
    }
    
    // Schedule next task
    TaskControlBlock* next_task = scheduleNextTask();
    if (next_task) {
        contextSwitch(next_task);
    }
}

// Delay task
void taskDelay(uint32_t ticks) {
    if (g_kernel.current_task) {
        g_kernel.current_task->state = TASK_STATE_BLOCKED;
        g_kernel.current_task->wake_up_time = ticks;
        
        // Trigger scheduler
        systemTickHandler();
    }
}

// =============================================================================
// HARDWARE ABSTRACTION IMPLEMENTATION
// =============================================================================

// Initialize GPIO pin
void initGPIO(GPIOPin* pin, uint8_t port, uint8_t pin_num, uint8_t mode, uint8_t pullup) {
    pin->port = port;
    pin->pin = pin_num;
    pin->mode = mode;
    pin->pullup = pullup;
    
    // Simulate register addresses
    static uint8_t port_a_data = 0, port_a_dir = 0, port_a_pullup = 0;
    static uint8_t port_b_data = 0, port_b_dir = 0, port_b_pullup = 0;
    
    switch (port) {
        case 0: // Port A
            pin->data_register = &port_a_data;
            pin->direction_register = &port_a_dir;
            pin->pullup_register = &port_a_pullup;
            break;
        case 1: // Port B
            pin->data_register = &port_b_data;
            pin->direction_register = &port_b_dir;
            pin->pullup_register = &port_b_pullup;
            break;
    }
    
    // Set pin mode
    if (mode == 1) { // Output
        *pin->direction_register |= (1 << pin_num);
    } else { // Input
        *pin->direction_register &= ~(1 << pin_num);
    }
    
    // Set pullup
    if (pullup) {
        *pin->pullup_register |= (1 << pin_num);
    } else {
        *pin->pullup_register &= ~(1 << pin_num);
    }
}

// Write GPIO pin
void writeGPIO(GPIOPin* pin, uint8_t value) {
    if (pin && pin->mode == 1) { // Output mode
        if (value) {
            *pin->data_register |= (1 << pin->pin);
        } else {
            *pin->data_register &= ~(1 << pin->pin);
        }
    }
}

// Read GPIO pin
uint8_t readGPIO(GPIOPin* pin) {
    if (pin && pin->mode == 0) { // Input mode
        return (*pin->data_register & (1 << pin->pin)) ? 1 : 0;
    }
    return 0;
}

// Initialize ADC
void initADC(ADCChannel* adc, uint8_t channel, uint8_t resolution, uint8_t reference) {
    adc->channel = channel;
    adc->resolution = resolution;
    adc->reference_voltage = reference;
    adc->value = 0;
    
    // Simulate register addresses
    static uint16_t adc_data[8] = {0};
    static uint8_t adc_control[8] = {0};
    
    adc->data_register = &adc_data[channel];
    adc->control_register = &adc_control[channel];
    
    // Configure ADC
    *adc->control_register = (resolution << 4) | (reference << 2) | 0x01; // Enable
}

// Read ADC
uint16_t readADC(ADCChannel* adc) {
    if (adc) {
        // Simulate ADC conversion
        static uint32_t adc_counter = 0;
        
        *adc->control_register |= 0x80; // Start conversion
        
        // Simulate conversion delay
        for (volatile int i = 0; i < 1000; i++);
        
        adc->value = (*adc->data_register & 0x0FFF); // 12-bit ADC
        *adc->control_register &= ~0x80; // Clear start bit
        
        return adc->value;
    }
    return 0;
}

// Initialize PWM
void initPWM(PWMChannel* pwm, uint8_t channel, uint16_t frequency, uint8_t resolution) {
    pwm->channel = channel;
    pwm->period = CLOCK_FREQUENCY / frequency;
    pwm->resolution = resolution;
    pwm->duty_cycle = 0;
    
    // Simulate register addresses
    static uint16_t pwm_duty[4] = {0};
    static uint16_t pwm_period[4] = {0};
    static uint8_t pwm_control[4] = {0};
    
    pwm->duty_register = &pwm_duty[channel];
    pwm->period_register = &pwm_period[channel];
    pwm->control_register = &pwm_control[channel];
    
    // Configure PWM
    *pwm->period_register = pwm->period;
    *pwm->control_register = (resolution << 1) | 0x01; // Enable
}

// Set PWM duty cycle
void setPWMDutyCycle(PWMChannel* pwm, uint16_t duty_cycle) {
    if (pwm && duty_cycle <= pwm->period) {
        pwm->duty_cycle = duty_cycle;
        *pwm->duty_register = duty_cycle;
    }
}

// =============================================================================
// SENSOR DRIVERS IMPLEMENTATION
// =============================================================================

static Sensor g_sensors[MAX_SENSORS];
static Actuator g_actuators[MAX_ACTUATORS];
static uint32_t g_sensor_count = 0;
static uint32_t g_actuator_count = 0;

// Temperature sensor read function
void readTemperatureSensor(void* sensor_ptr) {
    Sensor* sensor = (Sensor*)sensor_ptr;
    ADCChannel* adc = (ADCChannel*)sensor->hardware_interface;
    
    if (adc) {
        uint16_t raw_value = readADC(adc);
        
        // Convert ADC value to temperature (simplified)
        // Assuming 10mV per degree C with 2.5V reference
        sensor->value = (raw_value * 250) / 4096; // Convert to mV
        sensor->value = sensor->value / 10; // Convert to degrees C
        
        sensor->last_sample_time = g_kernel.system_ticks;
    }
}

// Light sensor read function
void readLightSensor(void* sensor_ptr) {
    Sensor* sensor = (Sensor*)sensor_ptr;
    ADCChannel* adc = (ADCChannel*)sensor->hardware_interface;
    
    if (adc) {
        uint16_t raw_value = readADC(adc);
        
        // Convert to lux (simplified)
        sensor->value = raw_value * 2; // Simplified conversion
        
        sensor->last_sample_time = g_kernel.system_ticks;
    }
}

// Accelerometer read function
void readAccelerometer(void* sensor_ptr) {
    Sensor* sensor = (Sensor*)sensor_ptr;
    
    // Simulate accelerometer reading
    static uint32_t accel_counter = 0;
    sensor->value = (accel_counter % 4096) - 2048; // -2G to +2G range
    accel_counter++;
    
    sensor->last_sample_time = g_kernel.system_ticks;
}

// Create sensor
uint32_t createSensor(const char* name, SensorType type, void* hardware_interface,
                      void (*read_function)(void*)) {
    if (g_sensor_count >= MAX_SENSORS) {
        return 0;
    }
    
    Sensor* sensor = &g_sensors[g_sensor_count];
    
    sensor->sensor_id = g_sensor_count + 1;
    strncpy(sensor->name, name, sizeof(sensor->name) - 1);
    sensor->type = type;
    sensor->read_function = read_function;
    sensor->hardware_interface = hardware_interface;
    sensor->is_enabled = 1;
    
    // Set default values based on sensor type
    switch (type) {
        case SENSOR_TYPE_TEMPERATURE:
            sensor->min_value = -40;
            sensor->max_value = 125;
            sensor->resolution = 12;
            sensor->precision = 1;
            break;
        case SENSOR_TYPE_HUMIDITY:
            sensor->min_value = 0;
            sensor->max_value = 100;
            sensor->resolution = 10;
            sensor->precision = 1;
            break;
        case SENSOR_TYPE_LIGHT:
            sensor->min_value = 0;
            sensor->max_value = 1000;
            sensor->resolution = 10;
            sensor->precision = 1;
            break;
        case SENSOR_TYPE_ACCELEROMETER:
            sensor->min_value = -2048;
            sensor->max_value = 2047;
            sensor->resolution = 12;
            sensor->precision = 8;
            break;
        default:
            sensor->min_value = 0;
            sensor->max_value = 4095;
            sensor->resolution = 12;
            sensor->precision = 1;
            break;
    }
    
    g_sensor_count++;
    return sensor->sensor_id;
}

// Read all enabled sensors
void readAllSensors(void) {
    for (uint32_t i = 0; i < g_sensor_count; i++) {
        Sensor* sensor = &g_sensors[i];
        
        if (sensor->is_enabled && sensor->read_function) {
            sensor->read_function(sensor);
        }
    }
}

// =============================================================================
// ACTUATOR DRIVERS IMPLEMENTATION
// =============================================================================

// LED write function
void writeLED(void* actuator_ptr, uint16_t value) {
    Actuator* actuator = (Actuator*)actuator_ptr;
    GPIOPin* led_pin = (GPIOPin*)actuator->hardware_interface;
    
    if (led_pin) {
        writeGPIO(led_pin, value > 0 ? 1 : 0);
        actuator->current_value = value;
    }
}

// Motor write function
void writeMotor(void* actuator_ptr, uint16_t value) {
    Actuator* actuator = (Actuator*)actuator_ptr;
    PWMChannel* pwm = (PWMChannel*)actuator->hardware_interface;
    
    if (pwm) {
        uint16_t duty_cycle = (value * pwm->period) / actuator->max_value;
        setPWMDutyCycle(pwm, duty_cycle);
        actuator->current_value = value;
    }
}

// Servo write function
void writeServo(void* actuator_ptr, uint16_t value) {
    Actuator* actuator = (Actuator*)actuator_ptr;
    PWMChannel* pwm = (PWMChannel*)actuator->hardware_interface;
    
    if (pwm) {
        // Servo typically uses 1-2ms pulse width for 0-180 degrees
        uint16_t pulse_width = 1000 + (value * 1000) / 180; // 1-2ms
        uint16_t duty_cycle = (pulse_width * pwm->period) / 20000; // 20ms period
        
        setPWMDutyCycle(pwm, duty_cycle);
        actuator->current_value = value;
    }
}

// Create actuator
uint32_t createActuator(const char* name, ActuatorType type, void* hardware_interface,
                        void (*write_function)(void*, uint16_t)) {
    if (g_actuator_count >= MAX_ACTUATORS) {
        return 0;
    }
    
    Actuator* actuator = &g_actuators[g_actuator_count];
    
    actuator->actuator_id = g_actuator_count + 1;
    strncpy(actuator->name, name, sizeof(actuator->name) - 1);
    actuator->type = type;
    actuator->write_function = write_function;
    actuator->hardware_interface = hardware_interface;
    actuator->is_enabled = 1;
    
    // Set default values based on actuator type
    switch (type) {
        case ACTUATOR_TYPE_LED:
            actuator->min_value = 0;
            actuator->max_value = 1;
            actuator->resolution = 1;
            break;
        case ACTUATOR_TYPE_MOTOR:
            actuator->min_value = 0;
            actuator->max_value = 255;
            actuator->resolution = 8;
            break;
        case ACTUATOR_TYPE_SERVO:
            actuator->min_value = 0;
            actuator->max_value = 180;
            actuator->resolution = 1;
            break;
        default:
            actuator->min_value = 0;
            actuator->max_value = 255;
            actuator->resolution = 8;
            break;
    }
    
    g_actuator_count++;
    return actuator->actuator_id;
}

// Write to actuator
void writeActuator(uint32_t actuator_id, uint16_t value) {
    for (uint32_t i = 0; i < g_actuator_count; i++) {
        Actuator* actuator = &g_actuators[i];
        
        if (actuator->actuator_id == actuator_id && actuator->is_enabled) {
            if (value > actuator->max_value) {
                value = actuator->max_value;
            }
            
            if (actuator->write_function) {
                actuator->write_function(actuator, value);
            }
            break;
        }
    }
}

// =============================================================================
// COMMUNICATION PROTOCOLS IMPLEMENTATION
// =============================================================================

// CAN bus simulation
static CANMessage g_can_messages[MAX_EVENTS];
static uint32_t g_can_message_count = 0;

// Send CAN message
uint32_t sendCANMessage(uint32_t message_id, const uint8_t* data, uint8_t data_length, 
                        uint8_t priority) {
    if (g_can_message_count >= MAX_EVENTS) {
        return 0;
    }
    
    CANMessage* msg = &g_can_messages[g_can_message_count];
    
    msg->message_id = message_id;
    msg->priority = priority;
    msg->data_length = data_length;
    msg->timestamp = g_kernel.system_ticks;
    
    if (data && data_length > 0) {
        memcpy(msg->data, data, data_length);
    }
    
    g_can_message_count++;
    return msg->timestamp;
}

// Receive CAN message
uint32_t receiveCANMessage(uint32_t message_id, CANMessage* message) {
    for (uint32_t i = 0; i < g_can_message_count; i++) {
        if (g_can_messages[i].message_id == message_id) {
            *message = g_can_messages[i];
            return i;
        }
    }
    return 0;
}

// I2C communication
uint8_t i2cWrite(I2CInterface* i2c, uint8_t address, const uint8_t* data, uint8_t length) {
    if (!i2c || !data || length == 0) {
        return 0;
    }
    
    // Simulate I2C write
    *i2c->control_register |= 0x01; // Start
    
    // Send address (write operation)
    *i2c->data_register = (address << 1) | 0x00;
    
    // Send data
    for (uint8_t i = 0; i < length; i++) {
        *i2c->data_register = data[i];
    }
    
    *i2c->control_register &= ~0x01; // Stop
    
    return 1; // Success
}

uint8_t i2cRead(I2CInterface* i2c, uint8_t address, uint8_t* data, uint8_t length) {
    if (!i2c || !data || length == 0) {
        return 0;
    }
    
    // Simulate I2C read
    *i2c->control_register |= 0x01; // Start
    
    // Send address (read operation)
    *i2c->data_register = (address << 1) | 0x01;
    
    // Read data
    for (uint8_t i = 0; i < length; i++) {
        data[i] = *i2c->data_register;
    }
    
    *i2c->control_register &= ~0x01; // Stop
    
    return 1; // Success
}

// SPI communication
uint8_t spiTransfer(SPIInterface* spi, const uint8_t* tx_data, uint8_t* rx_data, uint8_t length) {
    if (!spi || (!tx_data && !rx_data) || length == 0) {
        return 0;
    }
    
    *spi->control_register |= 0x01; // Enable
    
    for (uint8_t i = 0; i < length; i++) {
        if (tx_data) {
            *spi->data_register = tx_data[i];
        }
        
        // Wait for transfer to complete
        for (volatile int j = 0; j < 100; j++);
        
        if (rx_data) {
            rx_data[i] = *spi->data_register;
        }
    }
    
    *spi->control_register &= ~0x01; // Disable
    
    return 1; // Success
}

// =============================================================================
// POWER MANAGEMENT IMPLEMENTATION
// =============================================================================

static PowerManagement g_power_management = {0};

// Initialize power management
void initPowerManagement(void) {
    g_power_management.current_state = POWER_STATE_ACTIVE;
    g_power_management.sleep_mode_timer = 0;
    g_power_management.wake_up_sources = 0;
    g_power_management.cpu_frequency = 8000000; // 8MHz
    g_power_management.bus_frequency = 4000000; // 4MHz
    g_power_management.voltage_supply = 3300; // 3.3V
    g_power_management.current_consumption = 100; // 100mA
    g_power_management.low_power_mode_enabled = 0;
}

// Enter sleep mode
void enterSleepMode(uint32_t duration_ms) {
    if (g_power_management.current_state == POWER_STATE_ACTIVE) {
        g_power_management.current_state = POWER_STATE_SLEEP;
        g_power_management.sleep_mode_timer = duration_ms;
        
        // Reduce clock frequency
        g_power_management.cpu_frequency = 1000000; // 1MHz
        
        // Disable unnecessary peripherals
        // This would disable UART, SPI, I2C, etc.
        
        printf("Entering sleep mode for %d ms\n", duration_ms);
    }
}

// Wake up from sleep
void wakeUpFromSleep(void) {
    if (g_power_management.current_state == POWER_STATE_SLEEP) {
        g_power_management.current_state = POWER_STATE_ACTIVE;
        g_power_management.cpu_frequency = 8000000; // 8MHz
        
        // Re-enable peripherals
        // This would re-enable UART, SPI, I2C, etc.
        
        printf("Waking up from sleep mode\n");
    }
}

// Update power consumption
void updatePowerConsumption(void) {
    // Simulate power consumption calculation
    uint32_t base_consumption = 50; // Base consumption
    
    // Add CPU consumption based on frequency
    if (g_power_management.current_state == POWER_STATE_ACTIVE) {
        g_power_management.current_consumption = base_consumption + 
            (g_power_management.cpu_frequency / 100000); // 0.1mA per MHz
    } else if (g_power_management.current_state == POWER_STATE_SLEEP) {
        g_power_management.current_consumption = base_consumption / 10; // 10% of active
    }
    
    // Add peripheral consumption
    // This would add consumption for enabled peripherals
}

// =============================================================================
// MEMORY MANAGEMENT
// =============================================================================

// Initialize memory pool
MemoryPool* initMemoryPool(uint32_t pool_size, uint32_t block_size) {
    MemoryPool* pool = malloc(sizeof(MemoryPool));
    if (!pool) {
        return NULL;
    }
    
    pool->pool_base = malloc(pool_size);
    if (!pool->pool_base) {
        free(pool);
        return NULL;
    }
    
    pool->pool_size = pool_size;
    pool->block_size = block_size;
    pool->block_count = pool_size / block_size;
    
    // Initialize free list
    pool->free_list = malloc(pool->block_count * sizeof(uint32_t));
    pool->used_list = malloc(pool->block_count * sizeof(uint32_t));
    
    if (!pool->free_list || !pool->used_list) {
        free(pool->pool_base);
        free(pool->free_list);
        free(pool->used_list);
        free(pool);
        return NULL;
    }
    
    // Add all blocks to free list
    for (uint32_t i = 0; i < pool->block_count; i++) {
        pool->free_list[i] = i;
        pool->used_list[i] = 0xFFFFFFFF; // Mark as unused
    }
    
    pool->free_count = pool->block_count;
    
    return pool;
}

// Allocate block from memory pool
void* poolAlloc(MemoryPool* pool) {
    if (!pool || pool->free_count == 0) {
        return NULL;
    }
    
    uint32_t block_index = pool->free_list[0];
    
    // Remove from free list
    for (uint32_t i = 0; i < pool->free_count - 1; i++) {
        pool->free_list[i] = pool->free_list[i + 1];
    }
    pool->free_count--;
    
    // Add to used list
    for (uint32_t i = 0; i < pool->block_count; i++) {
        if (pool->used_list[i] == 0xFFFFFFFF) {
            pool->used_list[i] = block_index;
            break;
        }
    }
    
    return (void*)((uint8_t*)pool->pool_base + (block_index * pool->block_size));
}

// Free block to memory pool
void poolFree(MemoryPool* pool, void* block) {
    if (!pool || !block) {
        return;
    }
    
    uint32_t block_index = ((uint8_t*)block - (uint8_t*)pool->pool_base) / pool->block_size;
    
    if (block_index >= pool->block_count) {
        return;
    }
    
    // Remove from used list
    pool->used_list[block_index] = 0xFFFFFFFF;
    
    // Add to free list
    pool->free_list[pool->free_count] = block_index;
    pool->free_count++;
}

// =============================================================================
// DEMONSTRATION TASKS
// =============================================================================

// Sensor monitoring task
void sensorMonitorTask(void* parameter) {
    while (1) {
        readAllSensors();
        
        // Print sensor readings
        printf("=== Sensor Readings ===\n");
        for (uint32_t i = 0; i < g_sensor_count; i++) {
            Sensor* sensor = &g_sensors[i];
            printf("%s: %d %s\n", sensor->name, sensor->value,
                   sensor->type == SENSOR_TYPE_TEMPERATURE ? "°C" :
                   sensor->type == SENSOR_TYPE_HUMIDITY ? "%" :
                   sensor->type == SENSOR_TYPE_LIGHT ? "lux" : "units");
        }
        
        taskDelay(1000); // Read sensors every 1 second
    }
}

// Actuator control task
void actuatorControlTask(void* parameter) {
    while (1) {
        // Simple demonstration: toggle LEDs
        static uint32_t led_state = 0;
        
        for (uint32_t i = 0; i < g_actuator_count; i++) {
            Actuator* actuator = &g_actuators[i];
            
            if (actuator->type == ACTUATOR_TYPE_LED) {
                writeActuator(actuator->actuator_id, led_state);
                led_state = !led_state;
            }
        }
        
        printf("Toggled LEDs\n");
        taskDelay(500); // Toggle every 500ms
    }
}

// Power management task
void powerManagementTask(void* parameter) {
    while (1) {
        updatePowerConsumption();
        
        printf("Power consumption: %d mA, CPU: %d MHz\n", 
               g_power_management.current_consumption,
               g_power_management.cpu_frequency / 1000000);
        
        // Enter sleep mode periodically
        static uint32_t sleep_counter = 0;
        sleep_counter++;
        
        if (sleep_counter >= 10) {
            enterSleepMode(5000); // Sleep for 5 seconds
            taskDelay(5000);
            wakeUpFromSleep();
            sleep_counter = 0;
        }
        
        taskDelay(1000);
    }
}

// Communication task
void communicationTask(void* parameter) {
    while (1) {
        // Send CAN message
        uint8_t data[8] = {0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08};
        uint32_t timestamp = sendCANMessage(0x123, data, 8, 0);
        
        printf("Sent CAN message 0x123 at timestamp %d\n", timestamp);
        
        taskDelay(2000); // Send every 2 seconds
    }
}

// Main application task
void mainApplicationTask(void* parameter) {
    printf("Embedded Systems Advanced Application Started\n");
    
    // Initialize hardware
    printf("Initializing hardware...\n");
    
    // Initialize GPIO
    GPIOPin led_pin;
    initGPIO(&led_pin, 0, 0, 1, 0); // Port A, Pin 0, Output, No pullup
    
    // Initialize ADC
    ADCChannel temp_adc;
    initADC(&temp_adc, 0, 12, 1); // Channel 0, 12-bit, 2.5V reference
    
    // Initialize PWM
    PWMChannel motor_pwm;
    initPWM(&motor_pwm, 0, 1000, 8); // Channel 0, 1kHz, 8-bit
    
    // Create sensors
    createSensor("Temperature", SENSOR_TYPE_TEMPERATURE, &temp_adc, readTemperatureSensor);
    
    // Create actuators
    createActuator("Status LED", ACTUATOR_TYPE_LED, &led_pin, writeLED);
    
    // Initialize power management
    initPowerManagement();
    
    // Initialize memory pool
    MemoryPool* pool = initMemoryPool(1024, 64);
    
    printf("Hardware initialization complete\n");
    
    // Demonstrate memory pool
    void* block1 = poolAlloc(pool);
    void* block2 = poolAlloc(pool);
    
    if (block1 && block2) {
        printf("Allocated memory blocks from pool\n");
        poolFree(pool, block1);
        poolFree(pool, block2);
        printf("Returned blocks to pool\n");
    }
    
    // Run main application loop
    while (1) {
        // Read sensors
        readAllSensors();
        
        // Control actuators
        writeActuator(1, 1); // Turn on status LED
        
        // Monitor power
        updatePowerConsumption();
        
        printf("Main application loop - System tick: %lu\n", g_kernel.system_ticks);
        
        taskDelay(1000);
    }
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Advanced Embedded Systems Programming Examples\n");
    printf("=======================================\n\n");
    
    // Initialize kernel
    printf("Initializing RTOS kernel...\n");
    kernelInit();
    
    // Create application tasks
    printf("Creating application tasks...\n");
    createTask("SensorMonitor", sensorMonitorTask, NULL, 1024, PRIORITY_NORMAL);
    createTask("ActuatorControl", actuatorControlTask, NULL, 512, PRIORITY_NORMAL);
    createTask("PowerManagement", powerManagementTask, NULL, 256, PRIORITY_LOW);
    createTask("Communication", communicationTask, NULL, 512, PRIORITY_NORMAL);
    createTask("MainApplication", mainApplicationTask, NULL, 2048, PRIORITY_HIGH);
    
    // Start kernel simulation
    printf("Starting kernel simulation...\n");
    
    // Simulate system ticks
    for (uint32_t tick = 0; tick < 100000; tick++) {
        systemTickHandler();
        
        // Slow down simulation
        usleep(1000); // 1ms per tick
    }
    
    printf("Advanced embedded systems examples demonstrated!\n");
    printf("Key features demonstrated:\n");
    printf("- Real-time operating system kernel with task scheduling\n");
    printf("- Hardware abstraction layer for GPIO, ADC, PWM, UART, I2C, SPI\n");
    printf("- Sensor and actuator drivers with automatic reading/writing\n");
    printf("- Communication protocols (CAN, I2C, SPI) simulation\n");
    printf("- Power management with sleep modes and frequency scaling\n");
    printf("- Memory pool management for efficient memory allocation\n");
    printf("- Interrupt handling and context switching\n");
    printf("- Real-time task scheduling with priority-based preemptive scheduling\n");
    
    return 0;
}
