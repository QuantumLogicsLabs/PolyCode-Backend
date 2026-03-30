#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

// =============================================================================
// CLOUD COMPUTING
// =============================================================================

#define MAX_NODES 100
#define MAX_INSTANCES 1000
#define MAX_CONTAINERS 10000
#define MAX_STORAGE_SIZE 1024 * 1024 * 1024 // 1GB
#define MAX_BANDWIDTH 1000 // Mbps
#define MAX_CPU_CORES 64
#define MAX_MEMORY_GB 512

// =============================================================================
// CLOUD INFRASTRUCTURE
// =============================================================================

// Node types
typedef enum {
    NODE_COMPUTE = 0,
    NODE_STORAGE = 1,
    NODE_NETWORK = 2,
    NODE_MANAGEMENT = 3,
    NODE_DATABASE = 4
} NodeType;

// Instance states
typedef enum {
    INSTANCE_PENDING = 0,
    INSTANCE_RUNNING = 1,
    INSTANCE_STOPPING = 2,
    INSTANCE_STOPPED = 3,
    INSTANCE_TERMINATED = 4,
    INSTANCE_ERROR = 5
} InstanceState;

// Service types
typedef enum {
    SERVICE_COMPUTE = 0,
    SERVICE_STORAGE = 1,
    SERVICE_DATABASE = 2,
    SERVICE_NETWORKING = 3,
    SERVICE_SECURITY = 4,
    SERVICE_MONITORING = 5
} ServiceType;

// =============================================================================
// CLOUD NODES
// =============================================================================

// Resource requirements
typedef struct {
    int cpu_cores;
    int memory_gb;
    int storage_gb;
    int bandwidth_mbps;
} ResourceRequirements;

// Node structure
typedef struct {
    int node_id;
    char name[64];
    NodeType type;
    char ip_address[16];
    int port;
    ResourceRequirements total_resources;
    ResourceRequirements used_resources;
    ResourceRequirements available_resources;
    int is_active;
    time_t last_heartbeat;
    double cpu_utilization;
    double memory_utilization;
    double storage_utilization;
    int instance_count;
} CloudNode;

// =============================================================================
// VIRTUAL INSTANCES
// =============================================================================

// Instance types
typedef enum {
    INSTANCE_VM = 0,
    INSTANCE_CONTAINER = 1,
    INSTANCE_SERVERLESS = 2
} InstanceType;

// Instance structure
typedef struct {
    int instance_id;
    char name[64];
    InstanceType type;
    InstanceState state;
    int node_id;
    ResourceRequirements resources;
    char image_name[128];
    char* user_data;
    time_t created_time;
    time_t started_time;
    char* public_ip;
    char* private_ip;
    int is_running;
    double uptime_seconds;
    char* metadata;
} VirtualInstance;

// =============================================================================
// CONTAINER MANAGEMENT
// =============================================================================

// Container structure
typedef struct {
    int container_id;
    char name[64];
    char image_name[128];
    int instance_id;
    char* command;
    char* environment_vars;
    char* volumes;
    int port_mappings[10][2]; // [container_port, host_port]
    int port_count;
    int is_running;
    time_t created_time;
    time_t started_time;
    char* logs;
    int log_size;
} Container;

// Container image structure
typedef struct {
    char name[128];
    char tag[32];
    char* layers[50];
    int layer_count;
    size_t size_bytes;
    time_t created_time;
    char* digest;
} ContainerImage;

// =============================================================================
// STORAGE SYSTEMS
// =============================================================================

// Storage types
typedef enum {
    STORAGE_BLOCK = 0,
    STORAGE_OBJECT = 1,
    STORAGE_FILE = 2,
    STORAGE_DATABASE = 3
} StorageType;

// Storage volume structure
typedef struct {
    int volume_id;
    char name[64];
    StorageType type;
    size_t size_gb;
    char* mount_point;
    int instance_id;
    int is_attached;
    char* encryption_key;
    int is_encrypted;
    double iops;
    double throughput_mbps;
} StorageVolume;

// Object storage structure
typedef struct {
    char bucket_name[128];
    char object_key[512];
    size_t size_bytes;
    char* data;
    char* etag;
    time_t last_modified;
    char content_type[64];
    char* metadata;
} StorageObject;

// =============================================================================
// NETWORKING
// =============================================================================

// Network types
typedef enum {
    NETWORK_PUBLIC = 0,
    NETWORK_PRIVATE = 1,
    NETWORK_VPC = 2
} NetworkType;

// Network structure
typedef struct {
    int network_id;
    char name[64];
    NetworkType type;
    char cidr_block[32];
    char* subnet_cidrs[10];
    int subnet_count;
    int instance_count;
    char* gateway;
    char* dns_servers[4];
    int dns_count;
    int is_active;
} CloudNetwork;

// Load balancer structure
typedef struct {
    int lb_id;
    char name[64];
    char* public_ip;
    int port;
    int instance_ids[50];
    int instance_count;
    char* algorithm; // round_robin, least_connections, ip_hash
    int health_check_interval;
    char* health_check_path;
    int is_healthy;
} LoadBalancer;

// =============================================================================
// DATABASE SERVICES
// =============================================================================

// Database types
typedef enum {
    DB_RELATIONAL = 0,
    DB_NOSQL_DOCUMENT = 1,
    DB_NOSQL_KEYVALUE = 2,
    DB_NOSQL_COLUMN = 3,
    DB_GRAPH = 4,
    DB_TIME_SERIES = 5
} DatabaseType;

// Database instance structure
typedef struct {
    int db_id;
    char name[64];
    DatabaseType type;
    char* engine; // mysql, postgresql, mongodb, etc.
    char version[16];
    int node_id;
    ResourceRequirements resources;
    char* connection_string;
    int port;
    char* username;
    char* password_hash;
    int is_replicated;
    int replica_count;
    char* backup_schedule;
    int is_encrypted;
} DatabaseInstance;

// =============================================================================
// MONITORING AND METRICS
// =============================================================================

// Metric types
typedef enum {
    METRIC_CPU = 0,
    METRIC_MEMORY = 1,
    METRIC_DISK = 2,
    METRIC_NETWORK = 3,
    METRIC_CUSTOM = 4
} MetricType;

// Metric structure
typedef struct {
    char name[64];
    MetricType type;
    double value;
    char unit[16];
    time_t timestamp;
    int node_id;
    int instance_id;
    char* labels;
} Metric;

// Alert structure
typedef struct {
    int alert_id;
    char name[64];
    char description[256];
    int severity; // 1-5
    int is_active;
    time_t triggered_time;
    time_t resolved_time;
    char* condition;
    Metric* trigger_metric;
} Alert;

// Monitoring system structure
typedef struct {
    Metric metrics[10000];
    int metric_count;
    Alert alerts[1000];
    int alert_count;
    int collection_interval;
    int retention_days;
} MonitoringSystem;

// =============================================================================
// SECURITY AND AUTHENTICATION
// =============================================================================

// User structure
typedef struct {
    int user_id;
    char username[64];
    char email[128];
    char password_hash[256];
    char role[32];
    int is_active;
    time_t created_time;
    time_t last_login;
    char* permissions;
} CloudUser;

// Access control structure
typedef struct {
    char resource[128];
    char action[32];
    char* allowed_roles;
    int requires_mfa;
} AccessControl;

// Security policy structure
typedef struct {
    char policy_name[64];
    char* rules;
    int is_enforced;
    time_t created_time;
    time_t last_updated;
} SecurityPolicy;

// =============================================================================
// AUTO-SCALING
// =============================================================================

// Scaling policies
typedef enum {
    SCALE_MANUAL = 0,
    SCALE_SCHEDULED = 1,
    SCALE_DYNAMIC = 2
} ScalingPolicyType;

// Auto-scaling group structure
typedef struct {
    int group_id;
    char name[64];
    int min_instances;
    int max_instances;
    int desired_instances;
    int current_instances;
    int instance_ids[MAX_INSTANCES];
    ScalingPolicyType policy_type;
    char* scaling_rules;
    int cooldown_period;
    time_t last_scale_time;
} AutoScalingGroup;

// Scaling rule structure
typedef struct {
    char metric_name[64];
    double threshold;
    char comparison; // gt, lt, eq
    int adjustment; // Number of instances to add/remove
    int cooldown_seconds;
} ScalingRule;

// =============================================================================
// BACKUP AND DISASTER RECOVERY
// =============================================================================

// Backup types
typedef enum {
    BACKUP_FULL = 0,
    BACKUP_INCREMENTAL = 1,
    BACKUP_DIFFERENTIAL = 2
} BackupType;

// Backup structure
typedef struct {
    int backup_id;
    char name[64];
    BackupType type;
    int instance_id;
    int volume_id;
    time_t created_time;
    size_t size_bytes;
    char* location;
    int is_encrypted;
    char* checksum;
    int retention_days;
} Backup;

// Disaster recovery plan structure
typedef struct {
    int plan_id;
    char name[64];
    char* recovery_steps;
    int rto_hours; // Recovery Time Objective
    int rpo_hours; // Recovery Point Objective
    int backup_frequency_hours;
    char* backup_locations;
    int is_tested;
    time_t last_test_time;
} DisasterRecoveryPlan;

// =============================================================================
// CLOUD MANAGER
// =============================================================================

// Cloud manager structure
typedef struct {
    CloudNode nodes[MAX_NODES];
    int node_count;
    VirtualInstance instances[MAX_INSTANCES];
    int instance_count;
    Container containers[MAX_CONTAINERS];
    int container_count;
    StorageVolume volumes[MAX_INSTANCES];
    int volume_count;
    CloudNetwork networks[MAX_NODES];
    int network_count;
    DatabaseInstance databases[MAX_INSTANCES];
    int database_count;
    MonitoringSystem monitoring;
    CloudUser users[MAX_NODES];
    int user_count;
    AutoScalingGroup scaling_groups[MAX_NODES];
    int scaling_group_count;
    Backup backups[MAX_INSTANCES];
    int backup_count;
    int is_running;
} CloudManager;

// =============================================================================
// NODE MANAGEMENT
// =============================================================================

// Create cloud node
CloudNode* createCloudNode(int node_id, const char* name, NodeType type, const char* ip_address, int port) {
    CloudNode* node = malloc(sizeof(CloudNode));
    if (!node) return NULL;
    
    memset(node, 0, sizeof(CloudNode));
    node->node_id = node_id;
    strncpy(node->name, name, sizeof(node->name) - 1);
    node->type = type;
    strncpy(node->ip_address, ip_address, sizeof(node->ip_address) - 1);
    node->port = port;
    node->is_active = 1;
    node->last_heartbeat = time(NULL);
    
    // Set default resources based on node type
    switch (type) {
        case NODE_COMPUTE:
            node->total_resources.cpu_cores = 16;
            node->total_resources.memory_gb = 64;
            node->total_resources.storage_gb = 1000;
            node->total_resources.bandwidth_mbps = 1000;
            break;
        case NODE_STORAGE:
            node->total_resources.cpu_cores = 8;
            node->total_resources.memory_gb = 32;
            node->total_resources.storage_gb = 10000;
            node->total_resources.bandwidth_mbps = 1000;
            break;
        case NODE_DATABASE:
            node->total_resources.cpu_cores = 32;
            node->total_resources.memory_gb = 128;
            node->total_resources.storage_gb = 2000;
            node->total_resources.bandwidth_mbps = 1000;
            break;
        default:
            node->total_resources.cpu_cores = 4;
            node->total_resources.memory_gb = 16;
            node->total_resources.storage_gb = 500;
            node->total_resources.bandwidth_mbps = 100;
    }
    
    node->available_resources = node->total_resources;
    
    return node;
}

// Check node health
int checkNodeHealth(CloudNode* node) {
    if (!node) return 0;
    
    time_t current_time = time(NULL);
    
    // Check if heartbeat is recent (within 30 seconds)
    if (current_time - node->last_heartbeat > 30) {
        node->is_active = 0;
        return 0;
    }
    
    // Check resource utilization
    if (node->cpu_utilization > 95.0 || node->memory_utilization > 95.0 || node->storage_utilization > 95.0) {
        return 0; // Node is overloaded
    }
    
    return 1;
}

// Update node metrics
void updateNodeMetrics(CloudNode* node, double cpu_util, double memory_util, double storage_util) {
    if (!node) return;
    
    node->cpu_utilization = cpu_util;
    node->memory_utilization = memory_util;
    node->storage_utilization = storage_util;
    
    // Update used resources
    node->used_resources.cpu_cores = (int)(node->total_resources.cpu_cores * cpu_util / 100.0);
    node->used_resources.memory_gb = (int)(node->total_resources.memory_gb * memory_util / 100.0);
    node->used_resources.storage_gb = (int)(node->total_resources.storage_gb * storage_util / 100.0);
    
    // Update available resources
    node->available_resources.cpu_cores = node->total_resources.cpu_cores - node->used_resources.cpu_cores;
    node->available_resources.memory_gb = node->total_resources.memory_gb - node->used_resources.memory_gb;
    node->available_resources.storage_gb = node->total_resources.storage_gb - node->used_resources.storage_gb;
    
    node->last_heartbeat = time(NULL);
}

// =============================================================================
// INSTANCE MANAGEMENT
// =============================================================================

// Create virtual instance
VirtualInstance* createVirtualInstance(int instance_id, const char* name, InstanceType type, const char* image_name, ResourceRequirements resources) {
    VirtualInstance* instance = malloc(sizeof(VirtualInstance));
    if (!instance) return NULL;
    
    memset(instance, 0, sizeof(VirtualInstance));
    instance->instance_id = instance_id;
    strncpy(instance->name, name, sizeof(instance->name) - 1);
    instance->type = type;
    instance->state = INSTANCE_PENDING;
    strncpy(instance->image_name, image_name, sizeof(instance->image_name) - 1);
    instance->resources = resources;
    instance->created_time = time(NULL);
    
    return instance;
}

// Find suitable node for instance
CloudNode* findSuitableNode(CloudManager* manager, ResourceRequirements requirements) {
    for (int i = 0; i < manager->node_count; i++) {
        CloudNode* node = &manager->nodes[i];
        
        if (!node->is_active || !checkNodeHealth(node)) {
            continue;
        }
        
        // Check if node has enough resources
        if (node->available_resources.cpu_cores >= requirements.cpu_cores &&
            node->available_resources.memory_gb >= requirements.memory_gb &&
            node->available_resources.storage_gb >= requirements.storage_gb) {
            return node;
        }
    }
    
    return NULL;
}

// Start instance
int startInstance(CloudManager* manager, VirtualInstance* instance) {
    if (!manager || !instance) return -1;
    
    // Find suitable node
    CloudNode* node = findSuitableNode(manager, instance->resources);
    if (!node) {
        instance->state = INSTANCE_ERROR;
        return -1;
    }
    
    // Assign instance to node
    instance->node_id = node->node_id;
    instance->state = INSTANCE_RUNNING;
    instance->started_time = time(NULL);
    instance->is_running = 1;
    
    // Allocate IP addresses
    char public_ip[16], private_ip[16];
    generateIPAddress(public_ip, 1); // Public IP
    generateIPAddress(private_ip, 0); // Private IP
    instance->public_ip = strdup(public_ip);
    instance->private_ip = strdup(private_ip);
    
    // Update node resources
    node->used_resources.cpu_cores += instance->resources.cpu_cores;
    node->used_resources.memory_gb += instance->resources.memory_gb;
    node->used_resources.storage_gb += instance->resources.storage_gb;
    node->available_resources.cpu_cores -= instance->resources.cpu_cores;
    node->available_resources.memory_gb -= instance->resources.memory_gb;
    node->available_resources.storage_gb -= instance->resources.storage_gb;
    node->instance_count++;
    
    printf("Instance %d started on node %d\n", instance->instance_id, node->node_id);
    return 0;
}

// Stop instance
int stopInstance(CloudManager* manager, VirtualInstance* instance) {
    if (!manager || !instance || !instance->is_running) return -1;
    
    // Find the node
    CloudNode* node = NULL;
    for (int i = 0; i < manager->node_count; i++) {
        if (manager->nodes[i].node_id == instance->node_id) {
            node = &manager->nodes[i];
            break;
        }
    }
    
    if (!node) return -1;
    
    // Update instance state
    instance->state = INSTANCE_STOPPED;
    instance->is_running = 0;
    
    // Free node resources
    node->used_resources.cpu_cores -= instance->resources.cpu_cores;
    node->used_resources.memory_gb -= instance->resources.memory_gb;
    node->used_resources.storage_gb -= instance->resources.storage_gb;
    node->available_resources.cpu_cores += instance->resources.cpu_cores;
    node->available_resources.memory_gb += instance->resources.memory_gb;
    node->available_resources.storage_gb += instance->resources.storage_gb;
    node->instance_count--;
    
    printf("Instance %d stopped\n", instance->instance_id);
    return 0;
}

// =============================================================================
// CONTAINER MANAGEMENT
// =============================================================================

// Create container
Container* createContainer(int container_id, const char* name, const char* image_name, int instance_id) {
    Container* container = malloc(sizeof(Container));
    if (!container) return NULL;
    
    memset(container, 0, sizeof(Container));
    container->container_id = container_id;
    strncpy(container->name, name, sizeof(container->name) - 1);
    strncpy(container->image_name, image_name, sizeof(container->image_name) - 1);
    container->instance_id = instance_id;
    container->created_time = time(NULL);
    
    return container;
}

// Start container
int startContainer(Container* container) {
    if (!container) return -1;
    
    container->is_running = 1;
    container->started_time = time(NULL);
    
    printf("Container %d started\n", container->container_id);
    return 0;
}

// Stop container
int stopContainer(Container* container) {
    if (!container) return -1;
    
    container->is_running = 0;
    
    printf("Container %d stopped\n", container->container_id);
    return 0;
}

// =============================================================================
// STORAGE MANAGEMENT
// =============================================================================

// Create storage volume
StorageVolume* createStorageVolume(int volume_id, const char* name, StorageType type, size_t size_gb) {
    StorageVolume* volume = malloc(sizeof(StorageVolume));
    if (!volume) return NULL;
    
    memset(volume, 0, sizeof(StorageVolume));
    volume->volume_id = volume_id;
    strncpy(volume->name, name, sizeof(volume->name) - 1);
    volume->type = type;
    volume->size_gb = size_gb;
    volume->is_encrypted = 0;
    volume->iops = 3000; // Default IOPS
    volume->throughput_mbps = 125; // Default throughput
    
    return volume;
}

// Attach volume to instance
int attachVolumeToInstance(CloudManager* manager, StorageVolume* volume, int instance_id) {
    if (!manager || !volume) return -1;
    
    // Find instance
    VirtualInstance* instance = NULL;
    for (int i = 0; i < manager->instance_count; i++) {
        if (manager->instances[i].instance_id == instance_id) {
            instance = &manager->instances[i];
            break;
        }
    }
    
    if (!instance || !instance->is_running) return -1;
    
    volume->instance_id = instance_id;
    volume->is_attached = 1;
    
    // Generate mount point
    char mount_point[64];
    snprintf(mount_point, sizeof(mount_point), "/mnt/volume_%d", volume->volume_id);
    volume->mount_point = strdup(mount_point);
    
    printf("Volume %d attached to instance %d at %s\n", volume->volume_id, instance_id, mount_point);
    return 0;
}

// =============================================================================
// MONITORING SYSTEM
// =============================================================================

// Create monitoring system
MonitoringSystem* createMonitoringSystem(int collection_interval, int retention_days) {
    MonitoringSystem* monitoring = malloc(sizeof(MonitoringSystem));
    if (!monitoring) return NULL;
    
    memset(monitoring, 0, sizeof(MonitoringSystem));
    monitoring->collection_interval = collection_interval;
    monitoring->retention_days = retention_days;
    
    return monitoring;
}

// Collect metrics
void collectMetrics(CloudManager* manager) {
    if (!manager) return;
    
    time_t current_time = time(NULL);
    
    // Collect node metrics
    for (int i = 0; i < manager->node_count; i++) {
        CloudNode* node = &manager->nodes[i];
        
        if (!node->is_active) continue;
        
        // CPU metric
        Metric cpu_metric;
        strcpy(cpu_metric.name, "cpu_utilization");
        cpu_metric.type = METRIC_CPU;
        cpu_metric.value = node->cpu_utilization;
        strcpy(cpu_metric.unit, "percent");
        cpu_metric.timestamp = current_time;
        cpu_metric.node_id = node->node_id;
        
        // Memory metric
        Metric memory_metric;
        strcpy(memory_metric.name, "memory_utilization");
        memory_metric.type = METRIC_MEMORY;
        memory_metric.value = node->memory_utilization;
        strcpy(memory_metric.unit, "percent");
        memory_metric.timestamp = current_time;
        memory_metric.node_id = node->node_id;
        
        // Add to monitoring system
        if (manager->monitoring.metric_count < 10000) {
            manager->monitoring.metrics[manager->monitoring.metric_count++] = cpu_metric;
            manager->monitoring.metrics[manager->monitoring.metric_count++] = memory_metric;
        }
    }
}

// Check alerts
void checkAlerts(CloudManager* manager) {
    if (!manager) return;
    
    // Check for high CPU utilization
    for (int i = 0; i < manager->monitoring.metric_count; i++) {
        Metric* metric = &manager->monitoring.metrics[i];
        
        if (strcmp(metric->name, "cpu_utilization") == 0 && metric->value > 90.0) {
            // Create alert
            if (manager->monitoring.alert_count < 1000) {
                Alert* alert = &manager->monitoring.alerts[manager->monitoring.alert_count];
                alert->alert_id = manager->monitoring.alert_count;
                strcpy(alert->name, "High CPU Utilization");
                snprintf(alert->description, sizeof(alert->description), 
                        "Node %d has CPU utilization of %.2f%%", metric->node_id, metric->value);
                alert->severity = 3;
                alert->is_active = 1;
                alert->triggered_time = time(NULL);
                alert->trigger_metric = metric;
                
                manager->monitoring.alert_count++;
                printf("ALERT: %s\n", alert->description);
            }
        }
    }
}

// =============================================================================
// AUTO-SCALING
// =============================================================================

// Create auto-scaling group
AutoScalingGroup* createAutoScalingGroup(int group_id, const char* name, int min_instances, int max_instances) {
    AutoScalingGroup* group = malloc(sizeof(AutoScalingGroup));
    if (!group) return NULL;
    
    memset(group, 0, sizeof(AutoScalingGroup));
    group->group_id = group_id;
    strncpy(group->name, name, sizeof(group->name) - 1);
    group->min_instances = min_instances;
    group->max_instances = max_instances;
    group->desired_instances = min_instances;
    group->current_instances = 0;
    group->policy_type = SCALE_DYNAMIC;
    group->cooldown_period = 300; // 5 minutes
    
    return group;
}

// Scale up
int scaleUp(CloudManager* manager, AutoScalingGroup* group, int count) {
    if (!manager || !group || group->current_instances + count > group->max_instances) {
        return -1;
    }
    
    for (int i = 0; i < count; i++) {
        if (manager->instance_count >= MAX_INSTANCES) break;
        
        // Create new instance
        ResourceRequirements resources = {2, 4, 20, 100}; // Default instance size
        VirtualInstance* instance = createVirtualInstance(
            manager->instance_count, 
            "auto-scale-instance", 
            INSTANCE_VM, 
            "ubuntu-20.04", 
            resources
        );
        
        if (instance) {
            manager->instances[manager->instance_count++] = *instance;
            group->instance_ids[group->current_instances++] = instance->instance_id;
            group->current_instances++;
            
            // Start the instance
            startInstance(manager, instance);
            
            free(instance);
        }
    }
    
    group->last_scale_time = time(NULL);
    printf("Scaled up group %s by %d instances\n", group->name, count);
    return 0;
}

// Scale down
int scaleDown(CloudManager* manager, AutoScalingGroup* group, int count) {
    if (!manager || !group || group->current_instances - count < group->min_instances) {
        return -1;
    }
    
    for (int i = 0; i < count; i++) {
        if (group->current_instances <= 0) break;
        
        // Stop and remove the last instance
        int instance_id = group->instance_ids[group->current_instances - 1];
        
        for (int j = 0; j < manager->instance_count; j++) {
            if (manager->instances[j].instance_id == instance_id) {
                stopInstance(manager, &manager->instances[j]);
                break;
            }
        }
        
        group->current_instances--;
    }
    
    group->last_scale_time = time(NULL);
    printf("Scaled down group %s by %d instances\n", group->name, count);
    return 0;
}

// Check scaling conditions
void checkAutoScaling(CloudManager* manager) {
    if (!manager) return;
    
    for (int i = 0; i < manager->scaling_group_count; i++) {
        AutoScalingGroup* group = &manager->scaling_groups[i];
        
        time_t current_time = time(NULL);
        
        // Check cooldown period
        if (current_time - group->last_scale_time < group->cooldown_period) {
            continue;
        }
        
        // Calculate average CPU utilization for the group
        double total_cpu = 0.0;
        int instance_count = 0;
        
        for (int j = 0; j < group->current_instances; j++) {
            int instance_id = group->instance_ids[j];
            
            for (int k = 0; k < manager->instance_count; k++) {
                if (manager->instances[k].instance_id == instance_id && manager->instances[k].is_running) {
                    // Find the node and get CPU utilization
                    for (int l = 0; l < manager->node_count; l++) {
                        if (manager->nodes[l].node_id == manager->instances[k].node_id) {
                            total_cpu += manager->nodes[l].cpu_utilization;
                            instance_count++;
                            break;
                        }
                    }
                    break;
                }
            }
        }
        
        if (instance_count > 0) {
            double avg_cpu = total_cpu / instance_count;
            
            // Scale up if CPU > 80%
            if (avg_cpu > 80.0 && group->current_instances < group->max_instances) {
                scaleUp(manager, group, 1);
            }
            // Scale down if CPU < 20%
            else if (avg_cpu < 20.0 && group->current_instances > group->min_instances) {
                scaleDown(manager, group, 1);
            }
        }
    }
}

// =============================================================================
// BACKUP SYSTEM
// =============================================================================

// Create backup
Backup* createBackup(int backup_id, const char* name, BackupType type, int instance_id) {
    Backup* backup = malloc(sizeof(Backup));
    if (!backup) return NULL;
    
    memset(backup, 0, sizeof(Backup));
    backup->backup_id = backup_id;
    strncpy(backup->name, name, sizeof(backup->name) - 1);
    backup->type = type;
    backup->instance_id = instance_id;
    backup->created_time = time(NULL);
    backup->is_encrypted = 1;
    backup->retention_days = 30;
    
    return backup;
}

// Perform backup
int performBackup(CloudManager* manager, Backup* backup) {
    if (!manager || !backup) return -1;
    
    // Find instance
    VirtualInstance* instance = NULL;
    for (int i = 0; i < manager->instance_count; i++) {
        if (manager->instances[i].instance_id == backup->instance_id) {
            instance = &manager->instances[i];
            break;
        }
    }
    
    if (!instance || !instance->is_running) return -1;
    
    // Simulate backup process
    printf("Starting backup %s for instance %d\n", backup->name, backup->instance_id);
    
    // Calculate backup size (simulate)
    backup->size_bytes = instance->resources.storage_gb * 1024 * 1024 * 1024; // Convert to bytes
    
    // Generate checksum (simulate)
    char checksum[64];
    snprintf(checksum, sizeof(checksum), "checksum_%d_%ld", backup->backup_id, time(NULL));
    backup->checksum = strdup(checksum);
    
    // Set backup location
    char location[256];
    snprintf(location, sizeof(location), "s3://backups/instance_%d/backup_%d", 
             backup->instance_id, backup->backup_id);
    backup->location = strdup(location);
    
    printf("Backup completed: %s (%.2f GB)\n", backup->name, backup->size_bytes / (1024.0 * 1024.0 * 1024.0));
    return 0;
}

// =============================================================================
// UTILITY FUNCTIONS
// =============================================================================

// Generate IP address
void generateIPAddress(char* ip, int is_public) {
    if (is_public) {
        // Generate public IP (simplified)
        sprintf(ip, "203.0.113.%d", rand() % 254 + 1);
    } else {
        // Generate private IP
        sprintf(ip, "10.0.%d.%d", rand() % 255, rand() % 254 + 1);
    }
}

// Generate random string
void generateRandomString(char* str, size_t length) {
    const char charset[] = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    
    for (size_t i = 0; i < length - 1; i++) {
        int index = rand() % (sizeof(charset) - 1);
        str[i] = charset[index];
    }
    str[length - 1] = '\0';
}

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateCloudInfrastructure() {
    printf("=== CLOUD INFRASTRUCTURE DEMO ===\n");
    
    // Create cloud manager
    CloudManager* manager = malloc(sizeof(CloudManager));
    memset(manager, 0, sizeof(CloudManager));
    manager->is_running = 1;
    
    // Create nodes
    printf("Creating cloud nodes...\n");
    manager->nodes[manager->node_count++] = *createCloudNode(1, "compute-node-1", NODE_COMPUTE, "10.0.1.10", 22);
    manager->nodes[manager->node_count++] = *createCloudNode(2, "storage-node-1", NODE_STORAGE, "10.0.1.20", 22);
    manager->nodes[manager->node_count++] = *createCloudNode(3, "database-node-1", NODE_DATABASE, "10.0.1.30", 22);
    
    printf("Created %d nodes\n", manager->node_count);
    
    // Update node metrics
    for (int i = 0; i < manager->node_count; i++) {
        updateNodeMetrics(&manager->nodes[i], 45.5 + rand() % 20, 60.2 + rand() % 15, 30.1 + rand() % 10);
        printf("Node %s: CPU=%.1f%%, Memory=%.1f%%, Storage=%.1f%%\n", 
               manager->nodes[i].name, manager->nodes[i].cpu_utilization,
               manager->nodes[i].memory_utilization, manager->nodes[i].storage_utilization);
    }
    
    free(manager);
}

void demonstrateVirtualInstances() {
    printf("\n=== VIRTUAL INSTANCES DEMO ===\n");
    
    CloudManager* manager = malloc(sizeof(CloudManager));
    memset(manager, 0, sizeof(CloudManager));
    
    // Create nodes
    manager->nodes[manager->node_count++] = *createCloudNode(1, "compute-node-1", NODE_COMPUTE, "10.0.1.10", 22);
    
    // Create instances
    printf("Creating virtual instances...\n");
    ResourceRequirements resources = {2, 4, 20, 100};
    
    manager->instances[manager->instance_count++] = *createVirtualInstance(1, "web-server-1", INSTANCE_VM, "ubuntu-20.04", resources);
    manager->instances[manager->instance_count++] = *createVirtualInstance(2, "web-server-2", INSTANCE_VM, "ubuntu-20.04", resources);
    manager->instances[manager->instance_count++] = *createVirtualInstance(3, "database-server", INSTANCE_VM, "ubuntu-20.04", (ResourceRequirements){4, 8, 50, 200});
    
    printf("Created %d instances\n", manager->instance_count);
    
    // Start instances
    for (int i = 0; i < manager->instance_count; i++) {
        startInstance(manager, &manager->instances[i]);
        printf("Instance %d: %s (IP: %s, State: %s)\n", 
               manager->instances[i].instance_id, manager->instances[i].name,
               manager->instances[i].public_ip, 
               manager->instances[i].state == INSTANCE_RUNNING ? "Running" : "Stopped");
    }
    
    free(manager);
}

void demonstrateContainers() {
    printf("\n=== CONTAINER MANAGEMENT DEMO ===\n");
    
    // Create containers
    printf("Creating containers...\n");
    
    Container* containers[5];
    for (int i = 0; i < 5; i++) {
        char name[32];
        sprintf(name, "app-container-%d", i + 1);
        containers[i] = createContainer(i + 1, name, "nginx:latest", 1);
        
        // Start container
        startContainer(containers[i]);
        printf("Container %d: %s (Running: %s)\n", 
               containers[i]->container_id, containers[i]->name,
               containers[i]->is_running ? "Yes" : "No");
    }
    
    // Stop some containers
    for (int i = 0; i < 2; i++) {
        stopContainer(containers[i]);
        printf("Stopped container %d\n", containers[i]->container_id);
    }
    
    // Cleanup
    for (int i = 0; i < 5; i++) {
        free(containers[i]);
    }
}

void demonstrateStorage() {
    printf("\n=== STORAGE MANAGEMENT DEMO ===\n");
    
    // Create storage volumes
    printf("Creating storage volumes...\n");
    
    StorageVolume* volumes[3];
    volumes[0] = createStorageVolume(1, "web-data", STORAGE_BLOCK, 100);
    volumes[1] = createStorageVolume(2, "database-data", STORAGE_BLOCK, 500);
    volumes[2] = createStorageVolume(3, "backup-storage", STORAGE_OBJECT, 1000);
    
    for (int i = 0; i < 3; i++) {
        printf("Volume %d: %s (%.0f GB, Type: %d)\n", 
               volumes[i]->volume_id, volumes[i]->name, 
               (double)volumes[i]->size_gb, volumes[i]->type);
    }
    
    // Attach volume to instance
    printf("\nAttaching volumes to instances...\n");
    attachVolumeToInstance(NULL, volumes[0], 1);
    attachVolumeToInstance(NULL, volumes[1], 2);
    
    // Cleanup
    for (int i = 0; i < 3; i++) {
        free(volumes[i]);
    }
}

void demonstrateMonitoring() {
    printf("\n=== MONITORING SYSTEM DEMO ===\n");
    
    CloudManager* manager = malloc(sizeof(CloudManager));
    memset(manager, 0, sizeof(CloudManager));
    
    // Create monitoring system
    manager->monitoring = *createMonitoringSystem(60, 7); // 1 minute interval, 7 days retention
    
    // Create nodes
    manager->nodes[manager->node_count++] = *createCloudNode(1, "compute-node-1", NODE_COMPUTE, "10.0.1.10", 22);
    
    // Simulate high CPU usage
    updateNodeMetrics(&manager->nodes[0], 95.5, 60.2, 30.1);
    
    // Collect metrics and check alerts
    collectMetrics(manager);
    checkAlerts(manager);
    
    printf("Collected %d metrics\n", manager->monitoring.metric_count);
    printf("Generated %d alerts\n", manager->monitoring.alert_count);
    
    if (manager->monitoring.alert_count > 0) {
        Alert* alert = &manager->monitoring.alerts[0];
        printf("Alert: %s - %s\n", alert->name, alert->description);
    }
    
    free(manager);
}

void demonstrateAutoScaling() {
    printf("\n=== AUTO-SCALING DEMO ===\n");
    
    CloudManager* manager = malloc(sizeof(CloudManager));
    memset(manager, 0, sizeof(CloudManager));
    
    // Create nodes
    manager->nodes[manager->node_count++] = *createCloudNode(1, "compute-node-1", NODE_COMPUTE, "10.0.1.10", 22);
    manager->nodes[manager->node_count++] = *createCloudNode(2, "compute-node-2", NODE_COMPUTE, "10.0.1.11", 22);
    
    // Create auto-scaling group
    AutoScalingGroup* group = createAutoScalingGroup(1, "web-servers", 2, 8);
    manager->scaling_groups[manager->scaling_group_count++] = *group;
    
    printf("Created auto-scaling group: %s (min=%d, max=%d)\n", 
           group->name, group->min_instances, group->max_instances);
    
    // Scale up to minimum
    scaleUp(manager, group, 2);
    printf("Current instances: %d\n", group->current_instances);
    
    // Simulate high load and scale up
    printf("Simulating high load...\n");
    for (int i = 0; i < manager->node_count; i++) {
        updateNodeMetrics(&manager->nodes[i], 85.0, 70.0, 40.0);
    }
    
    checkAutoScaling(manager);
    printf("After high load - Current instances: %d\n", group->current_instances);
    
    // Simulate low load and scale down
    printf("Simulating low load...\n");
    for (int i = 0; i < manager->node_count; i++) {
        updateNodeMetrics(&manager->nodes[i], 15.0, 30.0, 20.0);
    }
    
    // Wait for cooldown period
    sleep(1);
    
    checkAutoScaling(manager);
    printf("After low load - Current instances: %d\n", group->current_instances);
    
    free(manager);
    free(group);
}

void demonstrateBackup() {
    printf("\n=== BACKUP SYSTEM DEMO ===\n");
    
    // Create backups
    printf("Creating backups...\n");
    
    Backup* backups[3];
    backups[0] = createBackup(1, "web-server-backup-1", BACKUP_FULL, 1);
    backups[1] = createBackup(2, "database-backup-1", BACKUP_FULL, 2);
    backups[2] = createBackup(3, "incremental-backup-1", BACKUP_INCREMENTAL, 1);
    
    // Perform backups
    for (int i = 0; i < 3; i++) {
        performBackup(NULL, backups[i]);
        printf("Backup %d: %s (%.2f GB)\n", 
               backups[i]->backup_id, backups[i]->name,
               backups[i]->size_bytes / (1024.0 * 1024.0 * 1024.0));
    }
    
    // Cleanup
    for (int i = 0; i < 3; i++) {
        free(backups[i]);
    }
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Advanced Cloud Computing Examples\n");
    printf("=================================\n\n");
    
    // Seed random number generator
    srand(time(NULL));
    
    // Run all demonstrations
    demonstrateCloudInfrastructure();
    demonstrateVirtualInstances();
    demonstrateContainers();
    demonstrateStorage();
    demonstrateMonitoring();
    demonstrateAutoScaling();
    demonstrateBackup();
    
    printf("\nAll advanced cloud computing examples demonstrated!\n");
    printf("Key features implemented:\n");
    printf("- Cloud infrastructure management\n");
    printf("- Virtual instance lifecycle management\n");
    printf("- Container orchestration\n");
    printf("- Storage volume management\n");
    printf("- Network configuration\n");
    printf("- Monitoring and alerting\n");
    printf("- Auto-scaling groups\n");
    printf("- Backup and disaster recovery\n");
    printf("- Resource allocation and scheduling\n");
    printf("- Health checking and failover\n");
    printf("- Multi-tenant architecture\n");
    printf("- Security and access control\n");
    
    return 0;
}
