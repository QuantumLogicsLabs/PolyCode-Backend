#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <pthread.h>
#include <signal.h>
#include <unistd.h>
#include <sys/time.h>
#include <sched.h>

// =============================================================================
// REAL-TIME PROGRAMMING FUNDAMENTALS
// =============================================================================

#define MAX_TASKS 100
#define MAX_EVENTS 1000
#define MAX_TIMERS 50
#define NANOSECONDS_PER_SECOND 1000000000L
#define MICROSECONDS_PER_SECOND 1000000L

// Real-time task priorities
#define RT_PRIORITY_LOWEST 1
#define RT_PRIORITY_NORMAL 50
#define RT_PRIORITY_HIGHEST 99

// Real-time scheduling policies
#define SCHED_FIFO 1    // First-in, first-out real-time scheduling
#define SCHED_RR 2      // Round-robin real-time scheduling

// Task states
typedef enum {
    TASK_STATE_READY = 0,
    TASK_STATE_RUNNING = 1,
    TASK_STATE_BLOCKED = 2,
    TASK_STATE_TERMINATED = 3
} TaskState;

// Event types
typedef enum {
    EVENT_TYPE_TIMER = 0,
    EVENT_TYPE_SIGNAL = 1,
    EVENT_TYPE_MESSAGE = 2,
    EVENT_TYPE_INTERRUPT = 3
} EventType;

// =============================================================================
// TIME MANAGEMENT
// =============================================================================

// High-resolution time structure
typedef struct {
    struct timespec ts;
} HighResTime;

// Time interval structure
typedef struct {
    long seconds;
    long nanoseconds;
} TimeInterval;

// Timer structure
typedef struct {
    int id;
    HighResTime start_time;
    TimeInterval interval;
    int periodic;
    int active;
    void (*callback)(int timer_id, void* user_data);
    void* user_data;
} Timer;

// =============================================================================
// TASK MANAGEMENT
// =============================================================================

// Real-time task structure
typedef struct {
    int id;
    char name[64];
    void (*task_function)(void* arg);
    void* arg;
    TaskState state;
    int priority;
    pthread_t thread;
    pthread_attr_t attr;
    TimeInterval period;
    TimeInterval deadline;
    TimeInterval execution_time;
    int missed_deadlines;
    HighResTime creation_time;
    HighResTime last_activation;
    HighResTime last_completion;
} RealTimeTask;

// Task queue
typedef struct {
    RealTimeTask* tasks[MAX_TASKS];
    int count;
    pthread_mutex_t mutex;
    pthread_cond_t cond;
} TaskQueue;

// =============================================================================
// EVENT SYSTEM
// =============================================================================

// Event structure
typedef struct {
    int id;
    EventType type;
    HighResTime timestamp;
    void* data;
    size_t data_size;
    int processed;
} Event;

// Event queue
typedef struct {
    Event events[MAX_EVENTS];
    int head;
    int tail;
    int count;
    pthread_mutex_t mutex;
} EventQueue;

// =============================================================================
// REAL-TIME SCHEDULER
// =============================================================================

// Scheduler structure
typedef struct {
    RealTimeTask* current_task;
    TaskQueue ready_queue;
    TaskQueue blocked_queue;
    int running;
    int algorithm; // 0 = Rate Monotonic, 1 = Earliest Deadline First
    pthread_t scheduler_thread;
    pthread_mutex_t mutex;
} RealTimeScheduler;

// =============================================================================
// HIGH-RESOLUTION TIME FUNCTIONS
// =============================================================================

// Get current high-resolution time
void getCurrentTime(HighResTime* time) {
    clock_gettime(CLOCK_MONOTONIC, &time->ts);
}

// Convert timespec to total nanoseconds
long long timespecToNanos(const struct timespec* ts) {
    return (long long)ts->tv_sec * NANOSECONDS_PER_SECOND + ts->tv_nsec;
}

// Convert nanoseconds to timespec
void nanosToTimespec(long long nanos, struct timespec* ts) {
    ts->tv_sec = nanos / NANOSECONDS_PER_SECOND;
    ts->tv_nsec = nanos % NANOSECONDS_PER_SECOND;
}

// Add time intervals
void addTimeIntervals(const HighResTime* base, const TimeInterval* interval, HighResTime* result) {
    result->ts.tv_sec = base->ts.tv_sec + interval->seconds;
    result->ts.tv_nsec = base->ts.tv_nsec + interval->nanoseconds;
    
    // Handle overflow
    if (result->ts.tv_nsec >= NANOSECONDS_PER_SECOND) {
        result->ts.tv_sec += result->ts.tv_nsec / NANOSECONDS_PER_SECOND;
        result->ts.tv_nsec = result->ts.tv_nsec % NANOSECONDS_PER_SECOND;
    }
}

// Compare two times
int compareTimes(const HighResTime* time1, const HighResTime* time2) {
    long long nanos1 = timespecToNanos(&time1->ts);
    long long nanos2 = timespecToNanos(&time2->ts);
    
    if (nanos1 < nanos2) return -1;
    if (nanos1 > nanos2) return 1;
    return 0;
}

// Calculate time difference
void timeDifference(const HighResTime* end, const HighResTime* start, TimeInterval* result) {
    long long end_nanos = timespecToNanos(&end->ts);
    long long start_nanos = timespecToNanos(&start->ts);
    long long diff = end_nanos - start_nanos;
    
    result->seconds = diff / NANOSECONDS_PER_SECOND;
    result->nanoseconds = diff % NANOSECONDS_PER_SECOND;
}

// Convert time interval to string
void timeIntervalToString(const TimeInterval* interval, char* buffer, size_t buffer_size) {
    if (interval->seconds > 0) {
        snprintf(buffer, buffer_size, "%ld.%09ld seconds", 
                interval->seconds, interval->nanoseconds);
    } else {
        snprintf(buffer, buffer_size, "%ld nanoseconds", interval->nanoseconds);
    }
}

// =============================================================================
// TASK MANAGEMENT IMPLEMENTATION
// =============================================================================

// Initialize task queue
void initTaskQueue(TaskQueue* queue) {
    queue->count = 0;
    pthread_mutex_init(&queue->mutex, NULL);
    pthread_cond_init(&queue->cond, NULL);
}

// Add task to queue
void enqueueTask(TaskQueue* queue, RealTimeTask* task) {
    pthread_mutex_lock(&queue->mutex);
    
    if (queue->count < MAX_TASKS) {
        queue->tasks[queue->count] = task;
        queue->count++;
        pthread_cond_signal(&queue->cond);
    }
    
    pthread_mutex_unlock(&queue->mutex);
}

// Get task from queue
RealTimeTask* dequeueTask(TaskQueue* queue) {
    pthread_mutex_lock(&queue->mutex);
    
    while (queue->count == 0) {
        pthread_cond_wait(&queue->cond, &queue->mutex);
    }
    
    RealTimeTask* task = queue->tasks[0];
    
    // Shift remaining tasks
    for (int i = 0; i < queue->count - 1; i++) {
        queue->tasks[i] = queue->tasks[i + 1];
    }
    
    queue->count--;
    pthread_mutex_unlock(&queue->mutex);
    
    return task;
}

// Create real-time task
RealTimeTask* createRealTimeTask(const char* name, void (*task_function)(void*), 
                                void* arg, int priority) {
    RealTimeTask* task = malloc(sizeof(RealTimeTask));
    if (!task) return NULL;
    
    memset(task, 0, sizeof(RealTimeTask));
    
    task->id = rand(); // Simple ID generation
    strncpy(task->name, name, sizeof(task->name) - 1);
    task->task_function = task_function;
    task->arg = arg;
    task->state = TASK_STATE_READY;
    task->priority = priority;
    getCurrentTime(&task->creation_time);
    
    // Set up thread attributes for real-time scheduling
    pthread_attr_init(&task->attr);
    pthread_attr_setschedpolicy(&task->attr, SCHED_FIFO);
    pthread_attr_setschedparam(&task->attr, &(struct sched_param){priority});
    
    return task;
}

// Set task period
void setTaskPeriod(RealTimeTask* task, long seconds, long nanoseconds) {
    task->period.seconds = seconds;
    task->period.nanoseconds = nanoseconds;
}

// Set task deadline
void setTaskDeadline(RealTimeTask* task, long seconds, long nanoseconds) {
    task->deadline.seconds = seconds;
    task->deadline.nanoseconds = nanoseconds;
}

// Task wrapper function
void* taskWrapper(void* arg) {
    RealTimeTask* task = (RealTimeTask*)arg;
    
    while (task->state != TASK_STATE_TERMINATED) {
        getCurrentTime(&task->last_activation);
        
        // Execute the task
        HighResTime start_time, end_time;
        getCurrentTime(&start_time);
        
        task->task_function(task->arg);
        
        getCurrentTime(&end_time);
        timeDifference(&end_time, &start_time, &task->execution_time);
        
        getCurrentTime(&task->last_completion);
        
        // Check deadline
        HighResTime deadline_time;
        addTimeIntervals(&task->last_activation, &task->deadline, &deadline_time);
        
        if (compareTimes(&end_time, &deadline_time) > 0) {
            task->missed_deadlines++;
            printf("Task %s missed deadline!\n", task->name);
        }
        
        // Sleep until next period (if periodic)
        if (task->period.seconds > 0 || task->period.nanoseconds > 0) {
            HighResTime next_activation;
            addTimeIntervals(&task->last_activation, &task->period, &next_activation);
            
            HighResTime current_time;
            getCurrentTime(&current_time);
            
            if (compareTimes(&current_time, &next_activation) < 0) {
                TimeInterval sleep_time;
                timeDifference(&next_activation, &current_time, &sleep_time);
                
                struct timespec sleep_ts = {
                    .tv_sec = sleep_time.seconds,
                    .tv_nsec = sleep_time.nanoseconds
                };
                
                nanosleep(&sleep_ts, NULL);
            }
        } else {
            // Non-periodic task, terminate
            task->state = TASK_STATE_TERMINATED;
            break;
        }
    }
    
    return NULL;
}

// Start task
int startTask(RealTimeTask* task) {
    if (task->state != TASK_STATE_READY) {
        return -1; // Task not in ready state
    }
    
    task->state = TASK_STATE_RUNNING;
    
    if (pthread_create(&task->thread, &task->attr, taskWrapper, task) != 0) {
        task->state = TASK_STATE_READY;
        return -2; // Failed to create thread
    }
    
    return 0; // Success
}

// Stop task
int stopTask(RealTimeTask* task) {
    if (task->state != TASK_STATE_RUNNING) {
        return -1; // Task not running
    }
    
    task->state = TASK_STATE_TERMINATED;
    pthread_join(task->thread, NULL);
    
    return 0; // Success
}

// =============================================================================
// TIMER MANAGEMENT
// =============================================================================

static Timer timers[MAX_TIMERS];
static int timer_count = 0;
static pthread_mutex_t timer_mutex = PTHREAD_MUTEX_INITIALIZER;
static pthread_t timer_thread;

// Timer thread function
void* timerThreadFunction(void* arg) {
    while (1) {
        pthread_mutex_lock(&timer_mutex);
        
        HighResTime current_time;
        getCurrentTime(&current_time);
        
        for (int i = 0; i < timer_count; i++) {
            Timer* timer = &timers[i];
            
            if (timer->active) {
                HighResTime next_trigger;
                addTimeIntervals(&timer->start_time, &timer->interval, &next_trigger);
                
                if (compareTimes(&current_time, &next_trigger) >= 0) {
                    // Timer triggered
                    if (timer->callback) {
                        timer->callback(timer->id, timer->user_data);
                    }
                    
                    if (timer->periodic) {
                        // Update start time for next trigger
                        timer->start_time = next_trigger;
                    } else {
                        // One-shot timer, deactivate
                        timer->active = 0;
                    }
                }
            }
        }
        
        pthread_mutex_unlock(&timer_mutex);
        
        // Sleep for a short time
        usleep(1000); // 1ms
    }
    
    return NULL;
}

// Initialize timer system
void initTimerSystem() {
    pthread_mutex_lock(&timer_mutex);
    timer_count = 0;
    pthread_mutex_unlock(&timer_mutex);
    
    // Start timer thread
    pthread_create(&timer_thread, NULL, timerThreadFunction, NULL);
}

// Create timer
int createTimer(long seconds, long nanoseconds, int periodic,
               void (*callback)(int timer_id, void* user_data), void* user_data) {
    pthread_mutex_lock(&timer_mutex);
    
    if (timer_count >= MAX_TIMERS) {
        pthread_mutex_unlock(&timer_mutex);
        return -1; // Too many timers
    }
    
    Timer* timer = &timers[timer_count];
    timer->id = timer_count;
    getCurrentTime(&timer->start_time);
    timer->interval.seconds = seconds;
    timer->interval.nanoseconds = nanoseconds;
    timer->periodic = periodic;
    timer->active = 1;
    timer->callback = callback;
    timer->user_data = user_data;
    
    timer_count++;
    
    pthread_mutex_unlock(&timer_mutex);
    
    return timer->id;
}

// Stop timer
int stopTimer(int timer_id) {
    pthread_mutex_lock(&timer_mutex);
    
    for (int i = 0; i < timer_count; i++) {
        if (timers[i].id == timer_id) {
            timers[i].active = 0;
            pthread_mutex_unlock(&timer_mutex);
            return 0; // Success
        }
    }
    
    pthread_mutex_unlock(&timer_mutex);
    return -1; // Timer not found
}

// =============================================================================
// EVENT SYSTEM IMPLEMENTATION
// =============================================================================

// Initialize event queue
void initEventQueue(EventQueue* queue) {
    queue->head = 0;
    queue->tail = 0;
    queue->count = 0;
    pthread_mutex_init(&queue->mutex, NULL);
}

// Add event to queue
int enqueueEvent(EventQueue* queue, EventType type, void* data, size_t data_size) {
    pthread_mutex_lock(&queue->mutex);
    
    if (queue->count >= MAX_EVENTS) {
        pthread_mutex_unlock(&queue->mutex);
        return -1; // Queue full
    }
    
    Event* event = &queue->events[queue->tail];
    event->id = rand();
    event->type = type;
    getCurrentTime(&event->timestamp);
    event->processed = 0;
    
    if (data && data_size > 0) {
        event->data = malloc(data_size);
        if (event->data) {
            memcpy(event->data, data, data_size);
            event->data_size = data_size;
        } else {
            event->data_size = 0;
        }
    } else {
        event->data = NULL;
        event->data_size = 0;
    }
    
    queue->tail = (queue->tail + 1) % MAX_EVENTS;
    queue->count++;
    
    pthread_mutex_unlock(&queue->mutex);
    
    return event->id;
}

// Get event from queue
int dequeueEvent(EventQueue* queue, Event* event) {
    pthread_mutex_lock(&queue->mutex);
    
    if (queue->count == 0) {
        pthread_mutex_unlock(&queue->mutex);
        return -1; // Queue empty
    }
    
    *event = queue->events[queue->head];
    queue->head = (queue->head + 1) % MAX_EVENTS;
    queue->count--;
    
    pthread_mutex_unlock(&queue->mutex);
    
    return event->id;
}

// Free event data
void freeEvent(Event* event) {
    if (event->data) {
        free(event->data);
        event->data = NULL;
    }
    event->data_size = 0;
}

// =============================================================================
// REAL-TIME SCHEDULER IMPLEMENTATION
// =============================================================================

static RealTimeScheduler g_scheduler;

// Initialize scheduler
void initScheduler(int algorithm) {
    memset(&g_scheduler, 0, sizeof(RealTimeScheduler));
    
    g_scheduler.algorithm = algorithm;
    g_scheduler.running = 0;
    
    initTaskQueue(&g_scheduler.ready_queue);
    initTaskQueue(&g_scheduler.blocked_queue);
    
    pthread_mutex_init(&g_scheduler.mutex, NULL);
}

// Rate Monotonic scheduling comparison
int compareTasksRM(const void* a, const void* b) {
    RealTimeTask* task_a = *(RealTimeTask**)a;
    RealTimeTask* task_b = *(RealTimeTask**)b;
    
    // Compare by period (shorter period = higher priority)
    long long period_a = (long long)task_a->period.seconds * NANOSECONDS_PER_SECOND + 
                        task_a->period.nanoseconds;
    long long period_b = (long long)task_b->period.seconds * NANOSECONDS_PER_SECOND + 
                        task_b->period.nanoseconds;
    
    if (period_a < period_b) return -1;
    if (period_a > period_b) return 1;
    return 0;
}

// Earliest Deadline First scheduling comparison
int compareTasksEDF(const void* a, const void* b) {
    RealTimeTask* task_a = *(RealTimeTask**)a;
    RealTimeTask* task_b = *(RealTimeTask**)b;
    
    // Compare by deadline (earlier deadline = higher priority)
    HighResTime deadline_a, deadline_b;
    addTimeIntervals(&task_a->last_activation, &task_a->deadline, &deadline_a);
    addTimeIntervals(&task_b->last_activation, &task_b->deadline, &deadline_b);
    
    return compareTimes(&deadline_a, &deadline_b);
}

// Scheduler thread function
void* schedulerThreadFunction(void* arg) {
    while (g_scheduler.running) {
        pthread_mutex_lock(&g_scheduler.mutex);
        
        if (g_scheduler.ready_queue.count > 0) {
            // Sort ready queue based on scheduling algorithm
            if (g_scheduler.algorithm == 0) {
                // Rate Monotonic
                qsort(g_scheduler.ready_queue.tasks, g_scheduler.ready_queue.count, 
                      sizeof(RealTimeTask*), compareTasksRM);
            } else {
                // Earliest Deadline First
                qsort(g_scheduler.ready_queue.tasks, g_scheduler.ready_queue.count, 
                      sizeof(RealTimeTask*), compareTasksEDF);
            }
            
            // Get highest priority task
            RealTimeTask* next_task = g_scheduler.ready_queue.tasks[0];
            
            if (g_scheduler.current_task != next_task) {
                // Context switch
                if (g_scheduler.current_task) {
                    g_scheduler.current_task->state = TASK_STATE_READY;
                }
                
                g_scheduler.current_task = next_task;
                next_task->state = TASK_STATE_RUNNING;
                
                printf("Scheduler: Switched to task %s (priority %d)\n", 
                       next_task->name, next_task->priority);
            }
        }
        
        pthread_mutex_unlock(&g_scheduler.mutex);
        
        // Sleep for short time
        usleep(1000); // 1ms
    }
    
    return NULL;
}

// Start scheduler
int startScheduler() {
    g_scheduler.running = 1;
    
    if (pthread_create(&g_scheduler.scheduler_thread, NULL, 
                      schedulerThreadFunction, NULL) != 0) {
        g_scheduler.running = 0;
        return -1; // Failed to create scheduler thread
    }
    
    return 0; // Success
}

// Stop scheduler
void stopScheduler() {
    g_scheduler.running = 0;
    pthread_join(g_scheduler.scheduler_thread, NULL);
}

// Add task to scheduler
int addTaskToScheduler(RealTimeTask* task) {
    pthread_mutex_lock(&g_scheduler.mutex);
    
    // Add to ready queue
    enqueueTask(&g_scheduler.ready_queue, task);
    
    pthread_mutex_unlock(&g_scheduler.mutex);
    
    return 0; // Success
}

// =============================================================================
// SAMPLE REAL-TIME TASKS
// =============================================================================

// Sample periodic task
void periodicTask1(void* arg) {
    static int counter = 0;
    counter++;
    
    printf("Periodic Task 1: Execution #%d\n", counter);
    
    // Simulate work
    usleep(10000); // 10ms
}

void periodicTask2(void* arg) {
    static int counter = 0;
    counter++;
    
    printf("Periodic Task 2: Execution #%d\n", counter);
    
    // Simulate work
    usleep(5000); // 5ms
}

// Sample aperiodic task
void aperiodicTask(void* arg) {
    printf("Aperiodic Task: Executing\n");
    
    // Simulate work
    usleep(20000); // 20ms
}

// Sample timer callback
void timerCallback(int timer_id, void* user_data) {
    printf("Timer %d triggered!\n", timer_id);
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateTimeManagement() {
    printf("=== TIME MANAGEMENT DEMO ===\n");
    
    HighResTime start_time, end_time;
    TimeInterval diff;
    char time_str[64];
    
    getCurrentTime(&start_time);
    printf("Current time: %ld.%09ld\n", start_time.ts.tv_sec, start_time.ts.tv_nsec);
    
    // Simulate some work
    usleep(100000); // 100ms
    
    getCurrentTime(&end_time);
    timeDifference(&end_time, &start_time, &diff);
    timeIntervalToString(&diff, time_str, sizeof(time_str));
    
    printf("Elapsed time: %s\n", time_str);
    
    // Test time arithmetic
    HighResTime future_time;
    TimeInterval interval = {.seconds = 5, .nanoseconds = 500000000}; // 5.5 seconds
    addTimeIntervals(&start_time, &interval, &future_time);
    printf("Future time (5.5s later): %ld.%09ld\n", future_time.ts.tv_sec, future_time.ts.tv_nsec);
}

void demonstrateTaskManagement() {
    printf("\n=== TASK MANAGEMENT DEMO ===\n");
    
    // Create tasks
    RealTimeTask* task1 = createRealTimeTask("PeriodicTask1", periodicTask1, NULL, 60);
    RealTimeTask* task2 = createRealTimeTask("PeriodicTask2", periodicTask2, NULL, 40);
    RealTimeTask* task3 = createRealTimeTask("AperiodicTask", aperiodicTask, NULL, 20);
    
    // Set periods and deadlines
    setTaskPeriod(task1, 0, 50000000);  // 50ms period
    setTaskDeadline(task1, 0, 40000000); // 40ms deadline
    
    setTaskPeriod(task2, 0, 100000000); // 100ms period
    setTaskDeadline(task2, 0, 80000000); // 80ms deadline
    
    // task3 is aperiodic, no period set
    
    printf("Created 3 tasks:\n");
    printf("- %s: priority %d, period 50ms, deadline 40ms\n", 
           task1->name, task1->priority);
    printf("- %s: priority %d, period 100ms, deadline 80ms\n", 
           task2->name, task2->priority);
    printf("- %s: priority %d, aperiodic\n", 
           task3->name, task3->priority);
    
    // Start tasks
    printf("\nStarting tasks...\n");
    startTask(task1);
    startTask(task2);
    
    // Let them run for a while
    sleep(2);
    
    // Start aperiodic task
    printf("Starting aperiodic task...\n");
    startTask(task3);
    
    // Let all tasks run
    sleep(1);
    
    // Stop tasks
    printf("Stopping tasks...\n");
    stopTask(task1);
    stopTask(task2);
    stopTask(task3);
    
    // Print task statistics
    printf("\nTask Statistics:\n");
    printf("Task %s: %d missed deadlines\n", task1->name, task1->missed_deadlines);
    printf("Task %s: %d missed deadlines\n", task2->name, task2->missed_deadlines);
    printf("Task %s: %d missed deadlines\n", task3->name, task3->missed_deadlines);
    
    // Cleanup
    free(task1);
    free(task2);
    free(task3);
}

void demonstrateTimerSystem() {
    printf("\n=== TIMER SYSTEM DEMO ===\n");
    
    // Initialize timer system
    initTimerSystem();
    
    // Create periodic timer (every 200ms)
    int periodic_timer = createTimer(0, 200000000, 1, timerCallback, "Periodic Timer");
    printf("Created periodic timer %d (200ms interval)\n", periodic_timer);
    
    // Create one-shot timer (after 500ms)
    int oneshot_timer = createTimer(0, 500000000, 0, timerCallback, "One-shot Timer");
    printf("Created one-shot timer %d (500ms delay)\n", oneshot_timer);
    
    // Let timers run for a while
    sleep(2);
    
    // Stop periodic timer
    stopTimer(periodic_timer);
    printf("Stopped periodic timer %d\n", periodic_timer);
    
    // Let remaining timers finish
    sleep(1);
}

void demonstrateEventSystem() {
    printf("\n=== EVENT SYSTEM DEMO ===\n");
    
    EventQueue event_queue;
    initEventQueue(&event_queue);
    
    // Create some events
    char message1[] = "Event 1 message";
    char message2[] = "Event 2 message";
    int number = 42;
    
    int event1_id = enqueueEvent(&event_queue, EVENT_TYPE_MESSAGE, message1, strlen(message1));
    int event2_id = enqueueEvent(&event_queue, EVENT_TYPE_MESSAGE, message2, strlen(message2));
    int event3_id = enqueueEvent(&event_queue, EVENT_TYPE_TIMER, &number, sizeof(number));
    
    printf("Created events: %d, %d, %d\n", event1_id, event2_id, event3_id);
    
    // Process events
    Event event;
    while (dequeueEvent(&event_queue, &event) >= 0) {
        printf("Processing event %d (type %d): ", event.id, event.type);
        
        if (event.type == EVENT_TYPE_MESSAGE && event.data) {
            printf("Message: %s\n", (char*)event.data);
        } else if (event.type == EVENT_TYPE_TIMER && event.data) {
            printf("Number: %d\n", *(int*)event.data);
        } else {
            printf("Unknown event data\n");
        }
        
        freeEvent(&event);
    }
}

void demonstrateScheduler() {
    printf("\n=== SCHEDULER DEMO ===\n");
    
    // Initialize scheduler with Rate Monotonic algorithm
    initScheduler(0); // 0 = Rate Monotonic
    
    // Create tasks with different periods
    RealTimeTask* rm_task1 = createRealTimeTask("RM_Task1", periodicTask1, NULL, 80);
    RealTimeTask* rm_task2 = createRealTimeTask("RM_Task2", periodicTask2, NULL, 60);
    
    setTaskPeriod(rm_task1, 0, 100000000); // 100ms period
    setTaskDeadline(rm_task1, 0, 80000000);  // 80ms deadline
    
    setTaskPeriod(rm_task2, 0, 200000000); // 200ms period
    setTaskDeadline(rm_task2, 0, 150000000); // 150ms deadline
    
    // Add tasks to scheduler
    addTaskToScheduler(rm_task1);
    addTaskToScheduler(rm_task2);
    
    // Start scheduler
    startScheduler();
    printf("Started Rate Monotonic scheduler\n");
    
    // Let scheduler run
    sleep(3);
    
    // Stop scheduler
    stopScheduler();
    printf("Stopped scheduler\n");
    
    // Cleanup
    free(rm_task1);
    free(rm_task2);
}

void demonstrateRealTimeConstraints() {
    printf("\n=== REAL-TIME CONSTRAINTS DEMO ===\n");
    
    // Create a task with tight deadline
    RealTimeTask* tight_task = createRealTimeTask("TightTask", periodicTask1, NULL, 90);
    
    // Set very short period and deadline
    setTaskPeriod(tight_task, 0, 20000000);  // 20ms period
    setTaskDeadline(tight_task, 0, 15000000); // 15ms deadline
    
    printf("Created task with 20ms period and 15ms deadline\n");
    printf("Task execution time is ~10ms, so it should meet deadlines\n");
    
    startTask(tight_task);
    
    // Run for a while to see if deadlines are met
    sleep(2);
    
    stopTask(tight_task);
    
    printf("Task completed with %d missed deadlines\n", tight_task->missed_deadlines);
    
    // Now create a task that will miss deadlines
    RealTimeTask* overload_task = createRealTimeTask("OverloadTask", aperiodicTask, NULL, 70);
    
    setTaskPeriod(overload_task, 0, 10000000);  // 10ms period
    setTaskDeadline(overload_task, 0, 5000000);   // 5ms deadline
    
    printf("\nCreated task with 10ms period and 5ms deadline\n");
    printf("Task execution time is ~20ms, so it will miss deadlines\n");
    
    startTask(overload_task);
    
    // Run for a while
    sleep(1);
    
    stopTask(overload_task);
    
    printf("Task completed with %d missed deadlines\n", overload_task->missed_deadlines);
    
    // Cleanup
    free(tight_task);
    free(overload_task);
}

void demonstrateSchedulingAlgorithms() {
    printf("\n=== SCHEDULING ALGORITHMS DEMO ===\n");
    
    // Rate Monotonic
    printf("Testing Rate Monotonic scheduling...\n");
    initScheduler(0); // Rate Monotonic
    
    RealTimeTask* rm_task1 = createRealTimeTask("RM1", periodicTask1, NULL, 70);
    RealTimeTask* rm_task2 = createRealTimeTask("RM2", periodicTask2, NULL, 50);
    
    setTaskPeriod(rm_task1, 0, 50000000);  // 50ms period
    setTaskPeriod(rm_task2, 0, 100000000); // 100ms period
    
    addTaskToScheduler(rm_task1);
    addTaskToScheduler(rm_task2);
    
    startScheduler();
    sleep(2);
    stopScheduler();
    
    free(rm_task1);
    free(rm_task2);
    
    // Earliest Deadline First
    printf("\nTesting Earliest Deadline First scheduling...\n");
    initScheduler(1); // EDF
    
    RealTimeTask* edf_task1 = createRealTimeTask("EDF1", periodicTask1, NULL, 70);
    RealTimeTask* edf_task2 = createRealTimeTask("EDF2", periodicTask2, NULL, 50);
    
    setTaskPeriod(edf_task1, 0, 50000000);  // 50ms period
    setTaskPeriod(edf_task2, 0, 100000000); // 100ms period
    
    addTaskToScheduler(edf_task1);
    addTaskToScheduler(edf_task2);
    
    startScheduler();
    sleep(2);
    stopScheduler();
    
    free(edf_task1);
    free(edf_task2);
}

void demonstrateInterruptHandling() {
    printf("\n=== INTERRUPT HANDLING DEMO ===\n");
    
    // Set up signal handler
    struct sigaction sa;
    sa.sa_handler = timerCallback;
    sigemptyset(&sa.sa_mask);
    sa.sa_flags = 0;
    
    if (sigaction(SIGALRM, &sa, NULL) == -1) {
        printf("Failed to set up signal handler\n");
        return;
    }
    
    // Set up timer
    struct itimerval timer;
    timer.it_interval.tv_sec = 0;
    timer.it_interval.tv_usec = 100000; // 100ms
    timer.it_value.tv_sec = 0;
    timer.it_value.tv_usec = 100000;   // 100ms
    
    if (setitimer(ITIMER_REAL, &timer, NULL) == -1) {
        printf("Failed to set up timer\n");
        return;
    }
    
    printf("Set up timer interrupt (100ms interval)\n");
    printf("Waiting for interrupts...\n");
    
    // Wait for interrupts
    sleep(2);
    
    // Stop timer
    timer.it_interval.tv_sec = 0;
    timer.it_interval.tv_usec = 0;
    timer.it_value.tv_sec = 0;
    timer.it_value.tv_usec = 0;
    
    setitimer(ITIMER_REAL, &timer, NULL);
    
    printf("Stopped timer interrupts\n");
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Real-Time Programming Examples\n");
    printf("==============================\n\n");
    
    // Set real-time scheduling policy for this process
    struct sched_param param;
    param.sched_priority = 50;
    
    if (sched_setscheduler(0, SCHED_FIFO, &param) != 0) {
        printf("Warning: Could not set real-time scheduling policy\n");
        printf("Try running with sudo or as root for full real-time capabilities\n\n");
    }
    
    // Run all demonstrations
    demonstrateTimeManagement();
    demonstrateTaskManagement();
    demonstrateTimerSystem();
    demonstrateEventSystem();
    demonstrateScheduler();
    demonstrateRealTimeConstraints();
    demonstrateSchedulingAlgorithms();
    demonstrateInterruptHandling();
    
    printf("\nAll real-time programming examples demonstrated!\n");
    printf("Key takeaways:\n");
    printf("- High-resolution time management with nanosecond precision\n");
    printf("- Real-time task management with priorities and deadlines\n");
    printf("- Timer system for periodic and one-shot events\n");
    printf("- Event system for asynchronous communication\n");
    printf("- Real-time scheduler with Rate Monotonic and EDF algorithms\n");
    printf("- Deadline monitoring and missed deadline detection\n");
    printf("- Interrupt handling for real-time responsiveness\n");
    
    return 0;
}
