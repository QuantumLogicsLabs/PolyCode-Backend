#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <pthread.h>
#include <unistd.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <sys/select.h>
#include <sys/time.h>

// =============================================================================
// DISTRIBUTED SYSTEMS FUNDAMENTALS
// =============================================================================

#define MAX_NODES 100
#define MAX_MESSAGE_SIZE 1024
#define MAX_NODES_NAME_LENGTH 64
#define HEARTBEAT_INTERVAL 5
#define ELECTION_TIMEOUT 10
#define MAX_CONNECTIONS 50

// Node states
typedef enum {
    NODE_STATE_FOLLOWER = 0,
    NODE_STATE_CANDIDATE = 1,
    NODE_STATE_LEADER = 2
} NodeState;

// Message types
typedef enum {
    MSG_TYPE_HEARTBEAT = 0,
    MSG_TYPE_ELECTION = 1,
    MSG_TYPE_VOTE = 2,
    MSG_TYPE_DATA = 3,
    MSG_TYPE_ACK = 4,
    MSG_TYPE_SYNC = 5
} MessageType;

// =============================================================================
// NODE STRUCTURES
// =============================================================================

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

// Connection structure
typedef struct {
    int socket_fd;
    struct sockaddr_in address;
    int node_id;
    int is_connected;
    time_t last_activity;
} Connection;

// =============================================================================
// CLUSTER MANAGEMENT
// =============================================================================

// Cluster structure
typedef struct {
    Node nodes[MAX_NODES];
    int node_count;
    int leader_id;
    int current_term;
    pthread_mutex_t mutex;
    int running;
} Cluster;

// =============================================================================
// CONSENSUS ALGORITHM (RAFT SIMPLIFIED)
// =============================================================================

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

static RaftState g_raft_state = {0};

// =============================================================================
// MESSAGE HANDLING
// =============================================================================

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

// =============================================================================
// NETWORK COMMUNICATION
// =============================================================================

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

// =============================================================================
// CLUSTER MANAGEMENT IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// RAFT CONSENSUS IMPLEMENTATION
// =============================================================================

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

// =============================================================================
// DISTRIBUTED DATA STRUCTURES
// =============================================================================

// Distributed hash table entry
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

// Initialize DHT
void initDHT(DistributedHashTable* dht) {
    memset(dht, 0, sizeof(DistributedHashTable));
    pthread_mutex_init(&dht->mutex, NULL);
}

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

// =============================================================================
// FAULT TOLERANCE
// =============================================================================

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
                        
                        // Start new election (simplified - in reality, would timeout first)
                        if (g_raft_state.state == NODE_STATE_FOLLOWER) {
                            pthread_mutex_unlock(&cluster->mutex);
                            startElection(cluster, 0); // Assuming node 0
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

// =============================================================================
// LOAD BALANCING
// =============================================================================

// Load balancer structure
typedef struct {
    int node_id;
    int current_load;
    int max_capacity;
    double response_time;
} LoadInfo;

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

// =============================================================================
// DEMONSTRATION FUNCTIONS
// =============================================================================

void demonstrateClusterManagement() {
    printf("=== CLUSTER MANAGEMENT DEMO ===\n");
    
    Cluster cluster;
    initCluster(&cluster);
    
    // Add nodes to cluster
    addNodeToCluster(&cluster, 0, "Node0", "127.0.0.1", 8000);
    addNodeToCluster(&cluster, 1, "Node1", "127.0.0.1", 8001);
    addNodeToCluster(&cluster, 2, "Node2", "127.0.0.1", 8002);
    addNodeToCluster(&cluster, 3, "Node3", "127.0.0.1", 8003);
    
    printf("Cluster initialized with %d nodes\n", cluster.node_count);
    
    // Display cluster information
    pthread_mutex_lock(&cluster.mutex);
    for (int i = 0; i < cluster.node_count; i++) {
        Node* node = &cluster.nodes[i];
        printf("Node %d: %s (%s:%d) - State: %d, Term: %d\n", 
               node->id, node->name, node->ip, node->port, node->state, node->term);
    }
    pthread_mutex_unlock(&cluster.mutex);
}

void demonstrateConsensusAlgorithm() {
    printf("\n=== CONSENSUS ALGORITHM DEMO ===\n");
    
    Cluster cluster;
    initCluster(&cluster);
    
    // Add nodes
    addNodeToCluster(&cluster, 0, "Node0", "127.0.0.1", 8000);
    addNodeToCluster(&cluster, 1, "Node1", "127.0.0.1", 8001);
    addNodeToCluster(&cluster, 2, "Node2", "127.0.0.1", 8002);
    
    printf("Simulating Raft consensus algorithm...\n");
    
    // Node 0 starts election
    printf("Node 0 starting election\n");
    startElection(&cluster, 0);
    
    // Simulate other nodes voting
    Message vote1 = createMessage(MSG_TYPE_VOTE, 1, 0, 1, NULL, 0);
    Message vote2 = createMessage(MSG_TYPE_VOTE, 2, 0, 1, NULL, 0);
    
    handleVoteMessage(&cluster, &vote1, 0);
    handleVoteMessage(&cluster, &vote2, 0);
    
    printf("Election completed. Leader: Node %d\n", cluster.leader_id);
}

void demonstrateDistributedHashTable() {
    printf("\n=== DISTRIBUTED HASH TABLE DEMO ===\n");
    
    Cluster cluster;
    DistributedHashTable dht;
    
    initCluster(&cluster);
    initDHT(&dht);
    
    // Add nodes
    addNodeToCluster(&cluster, 0, "Node0", "127.0.0.1", 8000);
    addNodeToCluster(&cluster, 1, "Node1", "127.0.0.1", 8001);
    addNodeToCluster(&cluster, 2, "Node2", "127.0.0.1", 8002);
    
    // Put some key-value pairs
    dhtPut(&dht, &cluster, "user:123", "John Doe", 0);
    dhtPut(&dht, &cluster, "user:456", "Jane Smith", 1);
    dhtPut(&dht, &cluster, "user:789", "Bob Johnson", 2);
    dhtPut(&dht, &cluster, "config:timeout", "30", 0);
    dhtPut(&dht, &cluster, "config:retries", "3", 1);
    
    printf("DHT populated with %d entries\n", dht.entry_count);
    
    // Get some values
    char value[256];
    int version;
    
    version = dhtGet(&dht, "user:123", value, sizeof(value));
    if (version >= 0) {
        printf("user:123 = %s (version %d)\n", value, version);
    }
    
    version = dhtGet(&dht, "config:timeout", value, sizeof(value));
    if (version >= 0) {
        printf("config:timeout = %s (version %d)\n", value, version);
    }
    
    // Show key distribution
    printf("\nKey distribution:\n");
    for (int i = 0; i < dht.entry_count; i++) {
        DHTEntry* entry = &dht.entries[i];
        int responsible_node = findResponsibleNode(&cluster, entry->key);
        printf("Key '%s' -> Node %d (stored on Node %d)\n", 
               entry->key, responsible_node, entry->node_id);
    }
}

void demonstrateMessagePassing() {
    printf("\n=== MESSAGE PASSING DEMO ===\n");
    
    // Create different types of messages
    Message heartbeat = createMessage(MSG_TYPE_HEARTBEAT, 0, 1, 1, "ping", 4);
    Message election = createMessage(MSG_TYPE_ELECTION, 1, -1, 2, "vote_req", 8);
    Message data = createMessage(MSG_TYPE_DATA, 2, 3, 1, "Hello World", 11);
    Message ack = createMessage(MSG_TYPE_ACK, 3, 2, 1, "OK", 2);
    
    printf("Created messages:\n");
    printf("Heartbeat: Type %d, Sender %d, Receiver %d, Term %d, Data: %s\n",
           heartbeat.type, heartbeat.sender_id, heartbeat.receiver_id, heartbeat.term, heartbeat.data);
    printf("Election: Type %d, Sender %d, Receiver %d, Term %d, Data: %s\n",
           election.type, election.sender_id, election.receiver_id, election.term, election.data);
    printf("Data: Type %d, Sender %d, Receiver %d, Term %d, Data: %s\n",
           data.type, data.sender_id, data.receiver_id, data.term, data.data);
    printf("ACK: Type %d, Sender %d, Receiver %d, Term %d, Data: %s\n",
           ack.type, ack.sender_id, ack.receiver_id, ack.term, ack.data);
    
    // Serialize and deserialize
    char buffer[sizeof(Message)];
    int serialized_size = serializeMessage(&data, buffer, sizeof(buffer));
    
    Message deserialized_msg;
    if (deserializeMessage(buffer, serialized_size, &deserialized_msg) == 0) {
        printf("\nSerialization/Deserialization successful!\n");
        printf("Deserialized message: Type %d, Data: %s\n", 
               deserialized_msg.type, deserialized_msg.data);
    }
}

void demonstrateLoadBalancing() {
    printf("\n=== LOAD BALANCING DEMO ===\n");
    
    Cluster cluster;
    initCluster(&cluster);
    
    // Add nodes with different capacities
    addNodeToCluster(&cluster, 0, "Node0", "127.0.0.1", 8000);
    addNodeToCluster(&cluster, 1, "Node1", "127.0.0.1", 8001);
    addNodeToCluster(&cluster, 2, "Node2", "127.0.0.1", 8002);
    addNodeToCluster(&cluster, 3, "Node3", "127.0.0.1", 8003);
    
    int connections[MAX_NODES] = {0};
    int last_node = -1;
    
    printf("Round-robin load balancing:\n");
    for (int i = 0; i < 10; i++) {
        int node_id = roundRobinLoadBalancer(&cluster, &last_node);
        if (node_id >= 0) {
            connections[node_id]++;
            printf("Request %d -> Node %d\n", i + 1, node_id);
        }
    }
    
    printf("\nRound-robin distribution:\n");
    for (int i = 0; i < cluster.node_count; i++) {
        printf("Node %d: %d requests\n", i, connections[i]);
    }
    
    // Reset connections
    memset(connections, 0, sizeof(connections));
    
    printf("\nLeast connections load balancing:\n");
    for (int i = 0; i < 10; i++) {
        int node_id = leastConnectionsLoadBalancer(&cluster, connections);
        if (node_id >= 0) {
            connections[node_id]++;
            printf("Request %d -> Node %d\n", i + 1, node_id);
        }
    }
    
    printf("\nLeast connections distribution:\n");
    for (int i = 0; i < cluster.node_count; i++) {
        printf("Node %d: %d requests\n", i, connections[i]);
    }
}

void demonstrateFaultTolerance() {
    printf("\n=== FAULT TOLERANCE DEMO ===\n");
    
    Cluster cluster;
    initCluster(&cluster);
    cluster.running = 1;
    
    // Add nodes
    addNodeToCluster(&cluster, 0, "Node0", "127.0.0.1", 8000);
    addNodeToCluster(&cluster, 1, "Node1", "127.0.0.1", 8001);
    addNodeToCluster(&cluster, 2, "Node2", "127.0.0.1", 8002);
    
    // Simulate node failure
    printf("Simulating node failure...\n");
    
    pthread_mutex_lock(&cluster.mutex);
    Node* node1 = findNodeById(&cluster, 1);
    if (node1) {
        node1->last_heartbeat = time(NULL) - 20; // 20 seconds ago
        printf("Node 1 last heartbeat: 20 seconds ago (simulating failure)\n");
    }
    
    Node* node2 = findNodeById(&cluster, 2);
    if (node2) {
        node2->last_heartbeat = time(NULL) - 30; // 30 seconds ago
        printf("Node 2 last heartbeat: 30 seconds ago (simulating failure)\n");
    }
    pthread_mutex_unlock(&cluster.mutex);
    
    // Run health check
    time_t current_time = time(NULL);
    
    pthread_mutex_lock(&cluster.mutex);
    for (int i = 0; i < cluster.node_count; i++) {
        Node* node = &cluster.nodes[i];
        
        if (current_time - node->last_heartbeat > HEARTBEAT_INTERVAL * 2) {
            if (node->is_active) {
                printf("Health check: Node %s (ID: %d) marked as down\n", node->name, node->id);
                node->is_active = 0;
            }
        } else {
            printf("Health check: Node %s (ID: %d) is healthy\n", node->name, node->id);
        }
    }
    pthread_mutex_unlock(&cluster.mutex);
    
    cluster.running = 0;
}

void demonstrateDistributedTransactions() {
    printf("\n=== DISTRIBUTED TRANSACTIONS DEMO ===\n");
    
    // Two-phase commit simulation
    printf("Simulating two-phase commit protocol...\n");
    
    // Phase 1: Prepare
    printf("Phase 1: Prepare phase\n");
    printf("Node 0: Prepared\n");
    printf("Node 1: Prepared\n");
    printf("Node 2: Prepared\n");
    
    // Phase 2: Commit
    printf("\nPhase 2: Commit phase\n");
    printf("All nodes prepared, committing transaction\n");
    printf("Node 0: Committed\n");
    printf("Node 1: Committed\n");
    printf("Node 2: Committed\n");
    
    printf("Transaction completed successfully\n");
    
    // Simulate rollback scenario
    printf("\nSimulating rollback scenario...\n");
    printf("Phase 1: Prepare phase\n");
    printf("Node 0: Prepared\n");
    printf("Node 1: Aborted (resource conflict)\n");
    printf("Node 2: Prepared\n");
    
    printf("\nPhase 2: Rollback phase\n");
    printf("Node 1 aborted, rolling back transaction\n");
    printf("Node 0: Rolled back\n");
    printf("Node 2: Rolled back\n");
    
    printf("Transaction rolled back\n");
}

// =============================================================================
// MAIN FUNCTION
// =============================================================================

int main() {
    printf("Distributed Systems Programming Examples\n");
    printf("========================================\n\n");
    
    // Run all demonstrations
    demonstrateClusterManagement();
    demonstrateConsensusAlgorithm();
    demonstrateDistributedHashTable();
    demonstrateMessagePassing();
    demonstrateLoadBalancing();
    demonstrateFaultTolerance();
    demonstrateDistributedTransactions();
    
    printf("\nAll distributed systems examples demonstrated!\n");
    printf("Key takeaways:\n");
    printf("- Cluster management with node discovery and health monitoring\n");
    printf("- Consensus algorithms (Raft) for leader election\n");
    printf("- Distributed hash tables for data partitioning\n");
    printf("- Message passing and serialization for communication\n");
    printf("- Load balancing algorithms for resource distribution\n");
    printf("- Fault tolerance with automatic failover\n");
    printf("- Distributed transactions with two-phase commit\n");
    
    return 0;
}
