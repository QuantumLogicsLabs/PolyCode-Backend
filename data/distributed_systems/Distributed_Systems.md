# Distributed Systems

This file contains comprehensive distributed systems programming examples in C, including cluster management, consensus algorithms, distributed data structures, message passing, load balancing, fault tolerance, and distributed transactions.

## 📚 Distributed Systems Fundamentals

### 🌐 Distributed System Concepts
- **Cluster Management**: Node discovery, health monitoring, and membership
- **Consensus Algorithms**: Leader election and agreement protocols
- **Message Passing**: Communication between distributed nodes
- **Fault Tolerance**: Handling node failures and network partitions
- **Load Balancing**: Distributing workload across nodes

### 🎯 Key Challenges
- **Consistency**: Maintaining data consistency across nodes
- **Availability**: Ensuring system remains operational
- **Partition Tolerance**: Handling network partitions
- **Scalability**: Scaling to handle increased load
- **Latency**: Minimizing communication delays

## 🏗️ Cluster Management

### Node Structure
```c
// Node information
typedef struct {
    int id;
    char name[MAX_NODES_NAME_LENGTH];
    char ip[16];
    int port;
    NodeState state;
    int term;
    int voted_for;
    time_t last_heartbeat;
    int is_active;
} Node;

// Cluster structure
typedef struct {
    Node nodes[MAX_NODES];
    int node_count;
    int leader_id;
    int current_term;
    pthread_mutex_t mutex;
    int running;
} Cluster;
```

### Node States
```c
typedef enum {
    NODE_STATE_FOLLOWER = 0,
    NODE_STATE_CANDIDATE = 1,
    NODE_STATE_LEADER = 2
} NodeState;
```

### Cluster Initialization
```c
// Initialize cluster
void initCluster(Cluster* cluster) {
    memset(cluster, 0, sizeof(Cluster));
    cluster->current_term = 0;
    cluster->leader_id = -1;
    pthread_mutex_init(&cluster->mutex, NULL);
}

// Add node to cluster
int addNodeToCluster(Cluster* cluster, int id, const char* name, 
                   const char* ip, int port) {
    pthread_mutex_lock(&cluster->mutex);
    
    if (cluster->node_count >= MAX_NODES) {
        pthread_mutex_unlock(&cluster->mutex);
        return -1; // Cluster full
    }
    
    Node* node = &cluster->nodes[cluster->node_count];
    node->id = id;
    strncpy(node->name, name, sizeof(node->name) - 1);
    strncpy(node->ip, ip, sizeof(node->ip) - 1);
    node->port = port;
    node->state = NODE_STATE_FOLLOWER;
    node->term = 0;
    node->voted_for = -1;
    node->last_heartbeat = time(NULL);
    node->is_active = 1;
    
    cluster->node_count++;
    
    pthread_mutex_unlock(&cluster->mutex);
    
    printf("Added node %s (ID: %d) to cluster\n", name, id);
    return 0;
}
```

### Node Discovery
```c
// Find node by ID
Node* findNodeById(Cluster* cluster, int node_id) {
    for (int i = 0; i < cluster->node_count; i++) {
        if (cluster->nodes[i].id == node_id) {
            return &cluster->nodes[i];
        }
    }
    return NULL;
}

// Find node by address
Node* findNodeByAddress(Cluster* cluster, const char* ip, int port) {
    for (int i = 0; i < cluster->node_count; i++) {
        if (strcmp(cluster->nodes[i].ip, ip) == 0 && 
            cluster->nodes[i].port == port) {
            return &cluster->nodes[i];
        }
    }
    return NULL;
}
```

**Cluster Management Benefits**:
- **Dynamic Membership**: Add and remove nodes at runtime
- **Health Monitoring**: Track node availability
- **Load Distribution**: Distribute work across nodes
- **Fault Detection**: Identify failed nodes quickly

## 🗳️ Consensus Algorithms

### Raft Algorithm Implementation
```c
// Raft state
typedef struct {
    int current_term;
    int voted_for;
    NodeState state;
    int leader_id;
    time_t last_heartbeat;
    int votes_received;
    int election_timeout;
} RaftState;
```

### Leader Election
```c
// Start election
void startElection(Cluster* cluster, int node_id) {
    printf("Node %d starting election for term %d\n", node_id, cluster->current_term + 1);
    
    pthread_mutex_lock(&cluster->mutex);
    
    // Become candidate
    g_raft_state.current_term = ++cluster->current_term;
    g_raft_state.voted_for = node_id;
    g_raft_state.state = NODE_STATE_CANDIDATE;
    g_raft_state.votes_received = 1; // Vote for self
    g_raft_state.election_timeout = ELECTION_TIMEOUT;
    
    // Update node state
    Node* self = findNodeById(cluster, node_id);
    if (self) {
        self->state = NODE_STATE_CANDIDATE;
        self->term = g_raft_state.current_term;
        self->voted_for = node_id;
    }
    
    pthread_mutex_unlock(&cluster->mutex);
    
    // Send vote requests to other nodes
    Message vote_msg = createMessage(MSG_TYPE_ELECTION, node_id, -1, 
                                  g_raft_state.current_term, NULL, 0);
    
    for (int i = 0; i < cluster->node_count; i++) {
        Node* node = &cluster->nodes[i];
        if (node->id != node_id && node->is_active) {
            int sock_fd = connectToNode(node->ip, node->port);
            if (sock_fd >= 0) {
                sendMessage(sock_fd, &vote_msg);
                close(sock_fd);
            }
        }
    }
}
```

### Vote Handling
```c
// Handle election message
void handleElectionMessage(Cluster* cluster, const Message* msg, int node_id) {
    pthread_mutex_lock(&cluster->mutex);
    
    Node* self = findNodeById(cluster, node_id);
    if (!self) {
        pthread_mutex_unlock(&cluster->mutex);
        return;
    }
    
    // Check if we should vote
    if (msg->term > self->term || 
        (msg->term == self->term && self->voted_for == -1)) {
        
        // Vote for candidate
        self->term = msg->term;
        self->voted_for = msg->sender_id;
        self->state = NODE_STATE_FOLLOWER;
        
        // Send vote response
        int sock_fd = connectToNode(findNodeById(cluster, msg->sender_id)->ip,
                                   findNodeById(cluster, msg->sender_id)->port);
        if (sock_fd >= 0) {
            Message vote_response = createMessage(MSG_TYPE_VOTE, node_id, 
                                                msg->sender_id, msg->term, NULL, 0);
            sendMessage(sock_fd, &vote_response);
            close(sock_fd);
        }
        
        printf("Node %d voted for node %d in term %d\n", node_id, msg->sender_id, msg->term);
    }
    
    pthread_mutex_unlock(&cluster->mutex);
}

// Handle vote message
void handleVoteMessage(Cluster* cluster, const Message* msg, int node_id) {
    pthread_mutex_lock(&cluster->mutex);
    
    if (g_raft_state.state == NODE_STATE_CANDIDATE && 
        g_raft_state.current_term == msg->term) {
        
        g_raft_state.votes_received++;
        printf("Node %d received vote from node %d (total votes: %d/%d)\n", 
               node_id, msg->sender_id, g_raft_state.votes_received, 
               cluster->node_count / 2 + 1);
        
        // Check if we have majority
        if (g_raft_state.votes_received > cluster->node_count / 2) {
            // Become leader
            g_raft_state.state = NODE_STATE_LEADER;
            cluster->leader_id = node_id;
            
            Node* self = findNodeById(cluster, node_id);
            if (self) {
                self->state = NODE_STATE_LEADER;
            }
            
            printf("Node %d became leader for term %d\n", node_id, g_raft_state.current_term);
        }
    }
    
    pthread_mutex_unlock(&cluster->mutex);
}
```

### Heartbeat Mechanism
```c
// Send heartbeat
void sendHeartbeat(Cluster* cluster, int node_id) {
    pthread_mutex_lock(&cluster->mutex);
    
    if (g_raft_state.state != NODE_STATE_LEADER) {
        pthread_mutex_unlock(&cluster->mutex);
        return;
    }
    
    Message heartbeat = createMessage(MSG_TYPE_HEARTBEAT, node_id, -1, 
                                    g_raft_state.current_term, "heartbeat", 9);
    
    pthread_mutex_unlock(&cluster->mutex);
    
    // Send heartbeat to all followers
    for (int i = 0; i < cluster->node_count; i++) {
        Node* node = &cluster->nodes[i];
        if (node->id != node_id && node->is_active && node->state == NODE_STATE_FOLLOWER) {
            int sock_fd = connectToNode(node->ip, node->port);
            if (sock_fd >= 0) {
                sendMessage(sock_fd, &heartbeat);
                close(sock_fd);
            }
        }
    }
}

// Handle heartbeat message
void handleHeartbeatMessage(Cluster* cluster, const Message* msg, int node_id) {
    pthread_mutex_lock(&cluster->mutex);
    
    Node* self = findNodeById(cluster, node_id);
    if (!self) {
        pthread_mutex_unlock(&cluster->mutex);
        return;
    }
    
    // Update leader and reset election timeout
    if (msg->term >= self->term) {
        cluster->leader_id = msg->sender_id;
        g_raft_state.leader_id = msg->sender_id;
        g_raft_state.current_term = msg->term;
        self->term = msg->term;
        self->state = NODE_STATE_FOLLOWER;
        self->last_heartbeat = time(NULL);
        g_raft_state.last_heartbeat = time(NULL);
    }
    
    pthread_mutex_unlock(&cluster->mutex);
    
    // Send acknowledgment
    int sock_fd = connectToNode(findNodeById(cluster, msg->sender_id)->ip,
                               findNodeById(cluster, msg->sender_id)->port);
    if (sock_fd >= 0) {
        Message ack = createMessage(MSG_TYPE_ACK, node_id, msg->sender_id, 
                                  msg->term, "ack", 3);
        sendMessage(sock_fd, &ack);
        close(sock_fd);
    }
}
```

**Consensus Benefits**:
- **Leader Election**: Automatic leader selection
- **Fault Tolerance**: Handle leader failures
- **Consistency**: Ensure all nodes agree on state
- **Split Brain Prevention**: Avoid multiple leaders

## 📨 Message Passing

### Message Structure
```c
// Message types
typedef enum {
    MSG_TYPE_HEARTBEAT = 0,
    MSG_TYPE_ELECTION = 1,
    MSG_TYPE_VOTE = 2,
    MSG_TYPE_DATA = 3,
    MSG_TYPE_ACK = 4,
    MSG_TYPE_SYNC = 5
} MessageType;

// Message structure
typedef struct {
    MessageType type;
    int sender_id;
    int receiver_id;
    int term;
    time_t timestamp;
    char data[MAX_MESSAGE_SIZE];
    size_t data_size;
} Message;
```

### Message Creation and Serialization
```c
// Create message
Message createMessage(MessageType type, int sender_id, int receiver_id, 
                     int term, const char* data, size_t data_size) {
    Message msg;
    msg.type = type;
    msg.sender_id = sender_id;
    msg.receiver_id = receiver_id;
    msg.term = term;
    msg.timestamp = time(NULL);
    msg.data_size = data_size;
    
    if (data && data_size > 0) {
        memcpy(msg.data, data, data_size);
    } else {
        msg.data_size = 0;
    }
    
    return msg;
}

// Serialize message
int serializeMessage(const Message* msg, char* buffer, size_t buffer_size) {
    if (!msg || !buffer || buffer_size < sizeof(Message)) {
        return -1;
    }
    
    memcpy(buffer, msg, sizeof(Message));
    return sizeof(Message);
}

// Deserialize message
int deserializeMessage(const char* buffer, size_t buffer_size, Message* msg) {
    if (!buffer || !msg || buffer_size < sizeof(Message)) {
        return -1;
    }
    
    memcpy(msg, buffer, sizeof(Message));
    return 0;
}
```

### Network Communication
```c
// Create server socket
int createServerSocket(int port) {
    int server_fd;
    struct sockaddr_in address;
    int opt = 1;
    
    // Create socket
    if ((server_fd = socket(AF_INET, SOCK_STREAM, 0)) == 0) {
        perror("socket failed");
        return -1;
    }
    
    // Set socket options
    if (setsockopt(server_fd, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt))) {
        perror("setsockopt");
        return -1;
    }
    
    address.sin_family = AF_INET;
    address.sin_addr.s_addr = INADDR_ANY;
    address.sin_port = htons(port);
    
    // Bind socket
    if (bind(server_fd, (struct sockaddr*)&address, sizeof(address)) < 0) {
        perror("bind");
        return -1;
    }
    
    // Listen
    if (listen(server_fd, MAX_CONNECTIONS) < 0) {
        perror("listen");
        return -1;
    }
    
    printf("Server listening on port %d\n", port);
    return server_fd;
}

// Connect to node
int connectToNode(const char* ip, int port) {
    int sock_fd;
    struct sockaddr_in serv_addr;
    
    if ((sock_fd = socket(AF_INET, SOCK_STREAM, 0)) < 0) {
        printf("Socket creation error\n");
        return -1;
    }
    
    serv_addr.sin_family = AF_INET;
    serv_addr.sin_port = htons(port);
    
    if (inet_pton(AF_INET, ip, &serv_addr.sin_addr) <= 0) {
        printf("Invalid address/Address not supported\n");
        return -1;
    }
    
    if (connect(sock_fd, (struct sockaddr*)&serv_addr, sizeof(serv_addr)) < 0) {
        printf("Connection Failed\n");
        return -1;
    }
    
    return sock_fd;
}

// Send message
int sendMessage(int sock_fd, const Message* msg) {
    char buffer[sizeof(Message)];
    int msg_size = serializeMessage(msg, buffer, sizeof(buffer));
    
    if (msg_size < 0) {
        return -1;
    }
    
    return send(sock_fd, buffer, msg_size, 0);
}

// Receive message
int receiveMessage(int sock_fd, Message* msg) {
    char buffer[sizeof(Message)];
    int bytes_received = recv(sock_fd, buffer, sizeof(buffer), 0);
    
    if (bytes_received <= 0) {
        return -1;
    }
    
    return deserializeMessage(buffer, bytes_received, msg);
}
```

**Message Passing Benefits**:
- **Structured Communication**: Well-defined message format
- **Type Safety**: Typed message system
- **Serialization**: Easy network transmission
- **Reliability**: Built-in error handling

## 🗄️ Distributed Data Structures

### Distributed Hash Table
```c
// DHT entry
typedef struct {
    char key[64];
    char value[256];
    int version;
    time_t timestamp;
    int node_id; // Node that owns this key
} DHTEntry;

// Distributed hash table
typedef struct {
    DHTEntry entries[1000];
    int entry_count;
    pthread_mutex_t mutex;
} DistributedHashTable;
```

### Consistent Hashing
```c
// Hash function for key distribution
int hashKey(const char* key, int node_count) {
    unsigned long hash = 5381;
    int c;
    
    while ((c = *key++)) {
        hash = ((hash << 5) + hash) + c;
    }
    
    return hash % node_count;
}

// Find responsible node for key
int findResponsibleNode(Cluster* cluster, const char* key) {
    if (cluster->node_count == 0) return -1;
    
    int hash = hashKey(key, cluster->node_count);
    return cluster->nodes[hash].id;
}
```

### DHT Operations
```c
// Initialize DHT
void initDHT(DistributedHashTable* dht) {
    memset(dht, 0, sizeof(DistributedHashTable));
    pthread_mutex_init(&dht->mutex, NULL);
}

// Put key-value pair
int dhtPut(DistributedHashTable* dht, Cluster* cluster, const char* key, 
          const char* value, int node_id) {
    pthread_mutex_lock(&dht->mutex);
    
    // Find existing entry
    for (int i = 0; i < dht->entry_count; i++) {
        if (strcmp(dht->entries[i].key, key) == 0) {
            strncpy(dht->entries[i].value, value, sizeof(dht->entries[i].value) - 1);
            dht->entries[i].version++;
            dht->entries[i].timestamp = time(NULL);
            dht->entries[i].node_id = node_id;
            pthread_mutex_unlock(&dht->mutex);
            return 0;
        }
    }
    
    // Add new entry
    if (dht->entry_count < 1000) {
        DHTEntry* entry = &dht->entries[dht->entry_count];
        strncpy(entry->key, key, sizeof(entry->key) - 1);
        strncpy(entry->value, value, sizeof(entry->value) - 1);
        entry->version = 1;
        entry->timestamp = time(NULL);
        entry->node_id = node_id;
        dht->entry_count++;
        pthread_mutex_unlock(&dht->mutex);
        return 0;
    }
    
    pthread_mutex_unlock(&dht->mutex);
    return -1; // DHT full
}

// Get value by key
int dhtGet(DistributedHashTable* dht, const char* key, char* value, size_t value_size) {
    pthread_mutex_lock(&dht->mutex);
    
    for (int i = 0; i < dht->entry_count; i++) {
        if (strcmp(dht->entries[i].key, key) == 0) {
            strncpy(value, dht->entries[i].value, value_size - 1);
            value[value_size - 1] = '\0';
            pthread_mutex_unlock(&dht->mutex);
            return dht->entries[i].version;
        }
    }
    
    pthread_mutex_unlock(&dht->mutex);
    return -1; // Key not found
}
```

**DHT Benefits**:
- **Data Distribution**: Even distribution across nodes
- **Scalability**: Handle large datasets
- **Fault Tolerance**: Data replication
- **Consistency**: Version control for data

## ⚖️ Load Balancing

### Load Balancer Structure
```c
// Load balancer structure
typedef struct {
    int node_id;
    int current_load;
    int max_capacity;
    double response_time;
} LoadInfo;
```

### Round-Robin Load Balancing
```c
// Simple round-robin load balancer
int roundRobinLoadBalancer(Cluster* cluster, int* last_node) {
    pthread_mutex_lock(&cluster->mutex);
    
    int active_nodes = 0;
    for (int i = 0; i < cluster->node_count; i++) {
        if (cluster->nodes[i].is_active) {
            active_nodes++;
        }
    }
    
    if (active_nodes == 0) {
        pthread_mutex_unlock(&cluster->mutex);
        return -1;
    }
    
    // Find next active node
    int attempts = 0;
    do {
        *last_node = (*last_node + 1) % cluster->node_count;
        attempts++;
    } while (!cluster->nodes[*last_node].is_active && attempts < cluster->node_count);
    
    int selected_node = cluster->nodes[*last_node].id;
    
    pthread_mutex_unlock(&cluster->mutex);
    return selected_node;
}
```

### Least Connections Load Balancing
```c
// Least connections load balancer
int leastConnectionsLoadBalancer(Cluster* cluster, int* connections) {
    pthread_mutex_lock(&cluster->mutex);
    
    int min_connections = INT_MAX;
    int selected_node = -1;
    
    for (int i = 0; i < cluster->node_count; i++) {
        if (cluster->nodes[i].is_active && connections[i] < min_connections) {
            min_connections = connections[i];
            selected_node = cluster->nodes[i].id;
        }
    }
    
    pthread_mutex_unlock(&cluster->mutex);
    return selected_node;
}
```

### Weighted Load Balancing
```c
// Weighted load balancer
int weightedLoadBalancer(Cluster* cluster, const int* weights) {
    pthread_mutex_lock(&cluster->mutex);
    
    int total_weight = 0;
    
    // Calculate total weight of active nodes
    for (int i = 0; i < cluster->node_count; i++) {
        if (cluster->nodes[i].is_active) {
            total_weight += weights[i];
        }
    }
    
    if (total_weight == 0) {
        pthread_mutex_unlock(&cluster->mutex);
        return -1;
    }
    
    // Select node based on weight
    int random_weight = rand() % total_weight;
    int current_weight = 0;
    
    for (int i = 0; i < cluster->node_count; i++) {
        if (cluster->nodes[i].is_active) {
            current_weight += weights[i];
            if (random_weight < current_weight) {
                pthread_mutex_unlock(&cluster->mutex);
                return cluster->nodes[i].id;
            }
        }
    }
    
    pthread_mutex_unlock(&cluster->mutex);
    return -1;
}
```

**Load Balancing Benefits**:
- **Even Distribution**: Balance workload across nodes
- **Scalability**: Handle increased load
- **Fault Tolerance**: Route around failed nodes
- **Performance**: Optimize response times

## 🛡️ Fault Tolerance

### Health Monitoring
```c
// Health check thread
void* healthCheckThread(void* arg) {
    Cluster* cluster = (Cluster*)arg;
    
    while (cluster->running) {
        time_t current_time = time(NULL);
        
        pthread_mutex_lock(&cluster->mutex);
        
        for (int i = 0; i < cluster->node_count; i++) {
            Node* node = &cluster->nodes[i];
            
            // Check if node is responsive
            if (current_time - node->last_heartbeat > HEARTBEAT_INTERVAL * 2) {
                if (node->is_active) {
                    printf("Node %s (ID: %d) appears to be down\n", node->name, node->id);
                    node->is_active = 0;
                    
                    // If this was the leader, start election
                    if (cluster->leader_id == node->id) {
                        cluster->leader_id = -1;
                        g_raft_state.leader_id = -1;
                        
                        // Start new election
                        if (g_raft_state.state == NODE_STATE_FOLLOWER) {
                            pthread_mutex_unlock(&cluster->mutex);
                            startElection(cluster, 0);
                            pthread_mutex_lock(&cluster->mutex);
                        }
                    }
                }
            } else if (!node->is_active && current_time - node->last_heartbeat < HEARTBEAT_INTERVAL) {
                printf("Node %s (ID: %d) is back online\n", node->name, node->id);
                node->is_active = 1;
            }
        }
        
        pthread_mutex_unlock(&cluster->mutex);
        
        sleep(HEARTBEAT_INTERVAL);
    }
    
    return NULL;
}
```

### Replication Strategy
```c
// Replicate data to multiple nodes
int replicateData(Cluster* cluster, const char* key, const char* value, int replication_factor) {
    int success_count = 0;
    
    for (int i = 0; i < cluster->node_count && success_count < replication_factor; i++) {
        Node* node = &cluster->nodes[i];
        
        if (node->is_active) {
            // Send replication request
            int sock_fd = connectToNode(node->ip, node->port);
            if (sock_fd >= 0) {
                Message replication_msg = createMessage(MSG_TYPE_DATA, -1, node->id, 
                                                     0, value, strlen(value));
                
                // Include key in message data
                char full_data[MAX_MESSAGE_SIZE];
                snprintf(full_data, sizeof(full_data), "%s:%s", key, value);
                
                replication_msg = createMessage(MSG_TYPE_DATA, -1, node->id, 0, 
                                             full_data, strlen(full_data));
                
                if (sendMessage(sock_fd, &replication_msg) > 0) {
                    success_count++;
                }
                
                close(sock_fd);
            }
        }
    }
    
    return success_count >= replication_factor ? 0 : -1;
}
```

### Failover Mechanism
```c
// Handle node failure
void handleNodeFailure(Cluster* cluster, int failed_node_id) {
    pthread_mutex_lock(&cluster->mutex);
    
    Node* failed_node = findNodeById(cluster, failed_node_id);
    if (!failed_node) {
        pthread_mutex_unlock(&cluster->mutex);
        return;
    }
    
    printf("Handling failure of node %d (%s)\n", failed_node_id, failed_node->name);
    
    // Mark node as inactive
    failed_node->is_active = 0;
    
    // If failed node was leader, start election
    if (cluster->leader_id == failed_node_id) {
        cluster->leader_id = -1;
        g_raft_state.leader_id = -1;
        
        printf("Leader failed, starting new election\n");
        
        // Start election (simplified)
        if (g_raft_state.state == NODE_STATE_FOLLOWER) {
            pthread_mutex_unlock(&cluster->mutex);
            startElection(cluster, 0);
            pthread_mutex_lock(&cluster->mutex);
        }
    }
    
    // Redistribute failed node's responsibilities
    redistributeResponsibilities(cluster, failed_node_id);
    
    pthread_mutex_unlock(&cluster->mutex);
}

// Redistribute responsibilities
void redistributeResponsibilities(Cluster* cluster, int failed_node_id) {
    // Find data that was on failed node
    for (int i = 0; i < cluster->node_count; i++) {
        Node* node = &cluster->nodes[i];
        
        if (node->is_active && node->id != failed_node_id) {
            // Migrate data from failed node to this node
            printf("Migrating responsibilities from node %d to node %d\n", 
                   failed_node_id, node->id);
        }
    }
}
```

**Fault Tolerance Benefits**:
- **High Availability**: System remains operational
- **Automatic Recovery**: Self-healing capabilities
- **Data Protection**: Replication and backup
- **Graceful Degradation**: Reduced functionality during failures

## 🔄 Distributed Transactions

### Two-Phase Commit
```c
// Two-phase commit coordinator
int twoPhaseCommit(Cluster* cluster, const char* transaction_data) {
    int participants[MAX_NODES];
    int participant_count = 0;
    
    // Phase 1: Prepare
    printf("Phase 1: Prepare phase\n");
    
    pthread_mutex_lock(&cluster->mutex);
    for (int i = 0; i < cluster->node_count; i++) {
        if (cluster->nodes[i].is_active) {
            participants[participant_count++] = cluster->nodes[i].id;
            
            // Send prepare message
            int sock_fd = connectToNode(cluster->nodes[i].ip, cluster->nodes[i].port);
            if (sock_fd >= 0) {
                Message prepare_msg = createMessage(MSG_TYPE_SYNC, -1, cluster->nodes[i].id,
                                                 0, "PREPARE", 7);
                sendMessage(sock_fd, &prepare_msg);
                close(sock_fd);
                
                printf("Sent PREPARE to node %d\n", cluster->nodes[i].id);
            }
        }
    }
    pthread_mutex_unlock(&cluster->mutex);
    
    // Wait for responses (simplified)
    sleep(1);
    
    // Phase 2: Commit or Rollback
    printf("\nPhase 2: Commit phase\n");
    
    // Check if all participants are ready
    int all_ready = 1; // Simplified - would check actual responses
    
    if (all_ready) {
        // Send commit to all participants
        for (int i = 0; i < participant_count; i++) {
            Node* node = findNodeById(cluster, participants[i]);
            if (node && node->is_active) {
                int sock_fd = connectToNode(node->ip, node->port);
                if (sock_fd >= 0) {
                    Message commit_msg = createMessage(MSG_TYPE_SYNC, -1, node->id,
                                                   0, "COMMIT", 6);
                    sendMessage(sock_fd, &commit_msg);
                    close(sock_fd);
                    
                    printf("Sent COMMIT to node %d\n", node->id);
                }
            }
        }
        
        printf("Transaction committed successfully\n");
        return 0;
    } else {
        // Send rollback to all participants
        for (int i = 0; i < participant_count; i++) {
            Node* node = findNodeById(cluster, participants[i]);
            if (node && node->is_active) {
                int sock_fd = connectToNode(node->ip, node->port);
                if (sock_fd >= 0) {
                    Message rollback_msg = createMessage(MSG_TYPE_SYNC, -1, node->id,
                                                     0, "ROLLBACK", 8);
                    sendMessage(sock_fd, &rollback_msg);
                    close(sock_fd);
                    
                    printf("Sent ROLLBACK to node %d\n", node->id);
                }
            }
        }
        
        printf("Transaction rolled back\n");
        return -1;
    }
}

// Participant handling
void handleTransactionMessage(Cluster* cluster, const Message* msg, int node_id) {
    if (strcmp(msg->data, "PREPARE") == 0) {
        printf("Node %d: Received PREPARE, acknowledging\n", node_id);
        
        // Send acknowledgment
        Node* coordinator = findNodeById(cluster, msg->sender_id);
        if (coordinator) {
            int sock_fd = connectToNode(coordinator->ip, coordinator->port);
            if (sock_fd >= 0) {
                Message ack_msg = createMessage(MSG_TYPE_ACK, node_id, coordinator->id,
                                               msg->term, "READY", 5);
                sendMessage(sock_fd, &ack_msg);
                close(sock_fd);
            }
        }
    } else if (strcmp(msg->data, "COMMIT") == 0) {
        printf("Node %d: Received COMMIT, committing transaction\n", node_id);
        // Actually commit the transaction
    } else if (strcmp(msg->data, "ROLLBACK") == 0) {
        printf("Node %d: Received ROLLBACK, rolling back transaction\n", node_id);
        // Actually rollback the transaction
    }
}
```

**Transaction Benefits**:
- **ACID Properties**: Atomicity, Consistency, Isolation, Durability
- **Data Integrity**: Ensure data consistency across nodes
- **Rollback Support**: Undo failed transactions
- **Concurrency Control**: Handle concurrent operations

## 🔧 Best Practices

### 1. Idempotent Operations
```c
// Good: Idempotent operations
int incrementCounter(DistributedHashTable* dht, const char* key, int delta) {
    char value[256];
    int version = dhtGet(dht, key, value, sizeof(value));
    
    int current_value = 0;
    if (version >= 0) {
        current_value = atoi(value);
    }
    
    int new_value = current_value + delta;
    char new_value_str[32];
    snprintf(new_value_str, sizeof(new_value_str), "%d", new_value);
    
    return dhtPut(dht, key, new_value_str, 0);
}

// Bad: Non-idempotent operations
int appendToList(DistributedHashTable* dht, const char* key, const char* item) {
    // This operation is not idempotent - repeated calls add multiple items
    char value[256];
    dhtGet(dht, key, value, sizeof(value));
    
    strcat(value, item); // Problem if called multiple times
    return dhtPut(dht, key, value, 0);
}
```

### 2. Timeout Handling
```c
// Good: Proper timeout handling
int sendMessageWithTimeout(int sock_fd, const Message* msg, int timeout_seconds) {
    fd_set write_fds;
    struct timeval timeout;
    
    FD_ZERO(&write_fds);
    FD_SET(sock_fd, &write_fds);
    
    timeout.tv_sec = timeout_seconds;
    timeout.tv_usec = 0;
    
    int result = select(sock_fd + 1, NULL, &write_fds, NULL, &timeout);
    
    if (result > 0 && FD_ISSET(sock_fd, &write_fds)) {
        return sendMessage(sock_fd, msg);
    } else {
        return -1; // Timeout
    }
}

// Bad: No timeout handling
int sendMessageBlocking(int sock_fd, const Message* msg) {
    return send(sock_fd, msg, sizeof(Message), 0); // May block forever
}
```

### 3. Retry Logic
```c
// Good: Exponential backoff retry
int sendMessageWithRetry(int sock_fd, const Message* msg, int max_retries) {
    int retry_count = 0;
    int backoff_time = 1; // Start with 1 second
    
    while (retry_count < max_retries) {
        int result = sendMessage(sock_fd, msg);
        if (result >= 0) {
            return result; // Success
        }
        
        retry_count++;
        printf("Send failed, retry %d/%d in %d seconds\n", 
               retry_count, max_retries, backoff_time);
        
        sleep(backoff_time);
        backoff_time *= 2; // Exponential backoff
        
        if (backoff_time > 60) {
            backoff_time = 60; // Cap at 60 seconds
        }
    }
    
    return -1; // Failed after all retries
}
```

### 4. Consistent Hashing
```c
// Good: Consistent hashing for minimal data movement
int consistentHash(const char* key, const int* node_ids, int node_count) {
    unsigned long hash = 5381;
    int c;
    
    while ((c = *key++)) {
        hash = ((hash << 5) + hash) + c;
    }
    
    return node_ids[hash % node_count];
}

// Bad: Simple modulo hashing (causes major data movement)
int simpleHash(const char* key, int node_count) {
    return strlen(key) % node_count;
}
```

## ⚠️ Common Pitfalls

### 1. Network Partitions
```c
// Wrong: No handling of network partitions
void handleNetworkPartition() {
    // Continue operating as if nothing happened
    // This can lead to split brain scenarios
}

// Right: Handle network partitions gracefully
void handleNetworkPartition(Cluster* cluster) {
    // Detect partition
    int reachable_nodes = countReachableNodes(cluster);
    
    if (reachable_nodes <= cluster->node_count / 2) {
        // We're in minority partition, step down
        if (g_raft_state.state == NODE_STATE_LEADER) {
            g_raft_state.state = NODE_STATE_FOLLOWER;
            cluster->leader_id = -1;
        }
    }
}
```

### 2. Clock Skew
```c
// Wrong: Rely on system clocks for ordering
void compareTimestamps() {
    time_t local_time = time(NULL);
    // Compare with remote timestamps
    // This can fail due to clock skew
}

// Right: Use logical clocks or vector clocks
typedef struct {
    int node_id;
    int counter;
} LogicalClock;

void incrementLogicalClock(LogicalClock* clock) {
    clock->counter++;
}
```

### 3. Partial Failures
```c
// Wrong: Assume all operations succeed
void updateAllNodes(Cluster* cluster, const char* data) {
    for (int i = 0; i < cluster->node_count; i++) {
        // Assume this always succeeds
        sendUpdate(&cluster->nodes[i], data);
    }
}

// Right: Handle partial failures
int updateAllNodes(Cluster* cluster, const char* data, int required_successes) {
    int success_count = 0;
    
    for (int i = 0; i < cluster->node_count; i++) {
        if (sendUpdate(&cluster->nodes[i], data) >= 0) {
            success_count++;
        }
    }
    
    return success_count >= required_successes ? 0 : -1;
}
```

## 🔧 Real-World Applications

### 1. Distributed Database
```c
// Distributed database operations
typedef struct {
    char table[64];
    char key[128];
    char value[512];
    int version;
    int transaction_id;
} DatabaseRecord;

int distributedInsert(Cluster* cluster, DatabaseRecord* record) {
    // Find responsible node for this key
    int node_id = findResponsibleNode(cluster, record->key);
    
    Node* node = findNodeById(cluster, node_id);
    if (!node || !node->is_active) {
        return -1; // Node not available
    }
    
    // Send insert request
    int sock_fd = connectToNode(node->ip, node->port);
    if (sock_fd >= 0) {
        Message msg = createMessage(MSG_TYPE_DATA, -1, node_id, 0,
                                 (char*)record, sizeof(DatabaseRecord));
        int result = sendMessage(sock_fd, &msg);
        close(sock_fd);
        return result >= 0 ? 0 : -1;
    }
    
    return -1;
}
```

### 2. Distributed Cache
```c
// Distributed cache implementation
typedef struct {
    char key[256];
    char value[1024];
    time_t expiry_time;
    int hit_count;
} CacheEntry;

int distributedCacheGet(Cluster* cluster, const char* key, char* value, size_t value_size) {
    // Find node that should have this key
    int node_id = findResponsibleNode(cluster, key);
    
    Node* node = findNodeById(cluster, node_id);
    if (!node || !node->is_active) {
        return -1;
    }
    
    // Request from cache node
    int sock_fd = connectToNode(node->ip, node->port);
    if (sock_fd >= 0) {
        Message msg = createMessage(MSG_TYPE_DATA, -1, node_id, 0, key, strlen(key));
        sendMessage(sock_fd, &msg);
        
        // Wait for response
        Message response;
        if (receiveMessage(sock_fd, &response) >= 0) {
            strncpy(value, response.data, value_size - 1);
            value[value_size - 1] = '\0';
            close(sock_fd);
            return 0;
        }
        
        close(sock_fd);
    }
    
    return -1;
}
```

### 3. Message Queue
```c
// Distributed message queue
typedef struct {
    char topic[64];
    char message[MAX_MESSAGE_SIZE];
    int priority;
    time_t timestamp;
    int producer_id;
} QueueMessage;

int distributedPublish(Cluster* cluster, const char* topic, const char* message) {
    // Find queue manager for this topic
    int node_id = hashKey(topic, cluster->node_count);
    
    Node* node = findNodeById(cluster, node_id);
    if (!node || !node->is_active) {
        return -1;
    }
    
    QueueMessage qmsg;
    strncpy(qmsg.topic, topic, sizeof(qmsg.topic) - 1);
    strncpy(qmsg.message, message, sizeof(qmsg.message) - 1);
    qmsg.priority = 1;
    qmsg.timestamp = time(NULL);
    qmsg.producer_id = 0;
    
    int sock_fd = connectToNode(node->ip, node->port);
    if (sock_fd >= 0) {
        Message msg = createMessage(MSG_TYPE_DATA, -1, node_id, 0,
                                 (char*)&qmsg, sizeof(QueueMessage));
        int result = sendMessage(sock_fd, &msg);
        close(sock_fd);
        return result >= 0 ? 0 : -1;
    }
    
    return -1;
}
```

## 📚 Further Reading

### Books
- "Designing Data-Intensive Applications" by Martin Kleppmann
- "Distributed Systems: Principles and Paradigms" by Andrew Tanenbaum
- "Designing Distributed Systems" by Brendan Burns

### Topics
- CAP theorem and consistency models
- Distributed consensus algorithms (Paxos, Raft)
- Eventual consistency and conflict resolution
- Distributed tracing and monitoring
- Microservices architecture

Distributed systems programming in C requires understanding of network communication, consensus algorithms, fault tolerance, and data consistency. Master these techniques to build robust, scalable distributed applications that can handle failures and maintain consistency across multiple nodes!
