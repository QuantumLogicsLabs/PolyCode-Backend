# Blockchain Development

This file contains comprehensive blockchain development examples in C, including cryptographic primitives, blockchain data structures, transaction processing, mining algorithms, wallet management, smart contracts, peer-to-peer networking, and consensus mechanisms.

## 🔗 Blockchain Fundamentals

### 🎯 Blockchain Concepts
- **Distributed Ledger**: Shared, immutable database across multiple nodes
- **Cryptographic Security**: Hash functions and digital signatures
- **Consensus Mechanisms**: Agreement protocols for network participants
- **Smart Contracts**: Self-executing contracts with predefined rules
- **Decentralization**: No single point of failure or control
- **Transparency**: Public verification of all transactions

### ⛓️ Blockchain Architecture
- **Blocks**: Containers for transaction data
- **Transactions**: Records of value transfer
- **Merkle Trees**: Efficient verification of data integrity
- **Mining**: Process of creating new blocks through computational work
- **Nodes**: Participants in the blockchain network
- **Wallets**: Tools for managing cryptographic keys and addresses

## 🔐 Cryptographic Primitives

### Hash Types
```c
// Hash types
typedef enum {
    HASH_SHA256 = 0,
    HASH_SHA3_256 = 1,
    HASH_RIPEMD160 = 2
} HashType;
```

### Signature Algorithms
```c
// Signature algorithms
typedef enum {
    SIG_RSA = 0,
    SIG_ECDSA = 1,
    SIG_ED25519 = 2
} SignatureAlgorithm;
```

### Cryptographic Functions Implementation
```c
// Compute SHA-256 hash
void computeSHA256(const char* data, int length, char* output) {
    unsigned char hash[SHA256_DIGEST_LENGTH];
    SHA256_CTX sha256;
    SHA256_Init(&sha256);
    SHA256_Update(&sha256, data, length);
    SHA256_Final(hash, &sha256);
    
    for (int i = 0; i < SHA256_DIGEST_LENGTH; i++) {
        sprintf(output + (i * 2), "%02x", hash[i]);
    }
    output[SHA256_DIGEST_LENGTH * 2] = '\0';
}

// Compute Merkle root
void computeMerkleRoot(Transaction** transactions, int count, char* output) {
    if (count == 0) {
        strcpy(output, "0000000000000000000000000000000000000000000000000000000000000000");
        return;
    }
    
    char* hashes[MAX_TRANSACTIONS];
    int hash_count = count;
    
    // Compute transaction hashes
    for (int i = 0; i < count; i++) {
        hashes[i] = malloc(HASH_SIZE * 2 + 1);
        computeSHA256((char*)transactions[i], sizeof(Transaction), hashes[i]);
    }
    
    // Build Merkle tree
    while (hash_count > 1) {
        int new_count = (hash_count + 1) / 2;
        
        for (int i = 0; i < new_count; i++) {
            char combined[HASH_SIZE * 4 + 2];
            
            if (i * 2 + 1 < hash_count) {
                strcpy(combined, hashes[i * 2]);
                strcat(combined, hashes[i * 2 + 1]);
            } else {
                strcpy(combined, hashes[i * 2]);
                strcat(combined, hashes[i * 2]); // Duplicate last hash
            }
            
            computeSHA256(combined, strlen(combined), hashes[i]);
        }
        
        hash_count = new_count;
    }
    
    strcpy(output, hashes[0]);
    
    // Cleanup
    for (int i = 0; i < count; i++) {
        free(hashes[i]);
    }
}

// Verify signature
int verifySignature(const char* message, const char* signature, const char* public_key) {
    // Simplified signature verification
    // In production, use proper cryptographic libraries
    return 1; // Assume valid for demo
}

// Generate key pair
void generateKeyPair(char* private_key, char* public_key) {
    // Simplified key generation
    // In production, use proper cryptographic libraries
    sprintf(private_key, "private_key_%ld", time(NULL));
    sprintf(public_key, "public_key_%ld", time(NULL));
}
```

**Cryptographic Benefits**:
- **Security**: Strong cryptographic primitives for data protection
- **Integrity**: Hash functions ensure data hasn't been tampered with
- **Authentication**: Digital signatures verify identity
- **Efficiency**: Optimized cryptographic operations

## ⛓️ Blockchain Data Structures

### Block Structure
```c
// Block structure
typedef struct {
    int block_number;
    char previous_hash[HASH_SIZE * 2 + 1];
    char merkle_root[HASH_SIZE * 2 + 1];
    time_t timestamp;
    long nonce;
    int difficulty;
    Transaction* transactions[MAX_TRANSACTIONS];
    int transaction_count;
    double block_reward;
    double total_fees;
    char hash[HASH_SIZE * 2 + 1];
} Block;
```

### Transaction Structure
```c
// Transaction structure
typedef struct {
    char tx_hash[HASH_SIZE * 2 + 1];
    TransactionType type;
    TransactionInput* inputs[MAX_TRANSACTIONS];
    int input_count;
    TransactionOutput* outputs[MAX_TRANSACTIONS];
    int output_count;
    time_t timestamp;
    int lock_time;
    int is_coinbase;
    double total_input;
    double total_output;
    double fee;
} Transaction;
```

### Blockchain Structure
```c
// Blockchain structure
typedef struct {
    Block* blocks[MAX_BLOCKS];
    int block_count;
    double total_supply;
    int difficulty;
    char* genesis_hash;
    int is_mining;
    MiningStats* mining_stats;
    NetworkStats* network_stats;
} Blockchain;
```

### Blockchain Implementation
```c
// Create genesis block
Block* createGenesisBlock() {
    Block* genesis = malloc(sizeof(Block));
    if (!genesis) return NULL;
    
    memset(genesis, 0, sizeof(Block));
    genesis->block_number = 0;
    strcpy(genesis->previous_hash, "0000000000000000000000000000000000000000000000000000000000000000");
    genesis->timestamp = time(NULL);
    genesis->difficulty = MINING_DIFFICULTY;
    genesis->block_reward = BLOCK_REWARD;
    
    // Create coinbase transaction
    Transaction* coinbase = malloc(sizeof(Transaction));
    memset(coinbase, 0, sizeof(Transaction));
    coinbase->type = TRANSACTION_COINBASE;
    coinbase->is_coinbase = 1;
    coinbase->total_output = BLOCK_REWARD;
    
    // Create coinbase output
    TransactionOutput* output = malloc(sizeof(TransactionOutput));
    memset(output, 0, sizeof(TransactionOutput));
    strcpy(output->address, "genesis-address");
    output->amount = BLOCK_REWARD;
    
    coinbase->outputs[0] = output;
    coinbase->output_count = 1;
    coinbase->total_output = BLOCK_REWARD;
    
    genesis->transactions[0] = coinbase;
    genesis->transaction_count = 1;
    
    // Compute Merkle root
    computeMerkleRoot(genesis->transactions, genesis->transaction_count, genesis->merkle_root);
    
    // Compute block hash
    char block_data[BLOCK_SIZE];
    int data_size = sprintf(block_data, "%d%s%s%ld%d", 
                          genesis->block_number, genesis->previous_hash, 
                          genesis->merkle_root, genesis->timestamp, 
                          genesis->difficulty);
    
    computeSHA256(block_data, data_size, genesis->hash);
    
    return genesis;
}

// Create new block
Block* createBlock(Block* previous_block, Transaction** transactions, int transaction_count) {
    Block* block = malloc(sizeof(Block));
    if (!block) return NULL;
    
    memset(block, 0, sizeof(Block));
    block->block_number = previous_block->block_number + 1;
    strcpy(block->previous_hash, previous_block->hash);
    block->timestamp = time(NULL);
    block->difficulty = previous_block->difficulty;
    block->block_reward = BLOCK_REWARD / pow(2, block->block_number / 210000); // Halving every 210000 blocks
    
    // Add transactions
    double total_fees = 0.0;
    for (int i = 0; i < transaction_count && i < MAX_TRANSACTIONS; i++) {
        block->transactions[i] = transactions[i];
        total_fees += transactions[i]->fee;
    }
    block->transaction_count = transaction_count;
    block->total_fees = total_fees;
    
    // Compute Merkle root
    computeMerkleRoot(block->transactions, block->transaction_count, block->merkle_root);
    
    return block;
}

// Verify block
int verifyBlock(Block* block, Block* previous_block) {
    // Check block number
    if (block->block_number != previous_block->block_number + 1) {
        return 0;
    }
    
    // Check previous hash
    if (strcmp(block->previous_hash, previous_block->hash) != 0) {
        return 0;
    }
    
    // Verify Merkle root
    char merkle_root[HASH_SIZE * 2 + 1];
    computeMerkleRoot(block->transactions, block->transaction_count, merkle_root);
    if (strcmp(block->merkle_root, merkle_root) != 0) {
        return 0;
    }
    
    // Verify proof of work
    char target[HASH_SIZE * 2 + 1];
    memset(target, '0', block->difficulty);
    target[block->difficulty] = '\0';
    
    if (strncmp(block->hash, target, block->difficulty) != 0) {
        return 0;
    }
    
    // Verify transactions
    for (int i = 0; i < block->transaction_count; i++) {
        if (!verifyTransaction(block->transactions[i])) {
            return 0;
        }
    }
    
    return 1;
}
```

**Blockchain Benefits**:
- **Immutability**: Once data is recorded, it cannot be altered
- **Transparency**: All transactions are visible to network participants
- **Security**: Cryptographic protection against tampering
- **Decentralization**: No single point of failure

## 💸 Transaction Processing

### Transaction Types
```c
// Transaction types
typedef enum {
    TRANSACTION_REGULAR = 0,
    TRANSACTION_COINBASE = 1,
    TRANSACTION_CONTRACT = 2,
    TRANSACTION_CONTRACT_CALL = 3
} TransactionType;
```

### Transaction Input Structure
```c
// Transaction input structure
typedef struct {
    char previous_tx_hash[HASH_SIZE * 2 + 1];
    int previous_output_index;
    char* signature;
    char* public_key;
    double amount;
} TransactionInput;
```

### Transaction Output Structure
```c
// Transaction output structure
typedef struct {
    char address[64];
    double amount;
    char* script;
} TransactionOutput;
```

### Transaction Implementation
```c
// Create transaction
Transaction* createTransaction(const char* from_address, const char* to_address, double amount, double fee) {
    Transaction* tx = malloc(sizeof(Transaction));
    if (!tx) return NULL;
    
    memset(tx, 0, sizeof(Transaction));
    tx->type = TRANSACTION_REGULAR;
    tx->timestamp = time(NULL);
    tx->fee = fee;
    
    // Create input (simplified - in reality, need to find UTXOs)
    TransactionInput* input = malloc(sizeof(TransactionInput));
    memset(input, 0, sizeof(TransactionInput));
    strcpy(input->previous_tx_hash, "previous_tx_hash");
    input->previous_output_index = 0;
    input->amount = amount + fee;
    
    tx->inputs[0] = input;
    tx->input_count = 1;
    tx->total_input = amount + fee;
    
    // Create output
    TransactionOutput* output = malloc(sizeof(TransactionOutput));
    memset(output, 0, sizeof(TransactionOutput));
    strcpy(output->address, to_address);
    output->amount = amount;
    
    tx->outputs[0] = output;
    tx->output_count = 1;
    tx->total_output = amount;
    
    // Compute transaction hash
    computeSHA256((char*)tx, sizeof(Transaction), tx->tx_hash);
    
    return tx;
}

// Create coinbase transaction
Transaction* createCoinbaseTransaction(const char* miner_address, double block_reward, double total_fees) {
    Transaction* tx = malloc(sizeof(Transaction));
    if (!tx) return NULL;
    
    memset(tx, 0, sizeof(Transaction));
    tx->type = TRANSACTION_COINBASE;
    tx->is_coinbase = 1;
    tx->timestamp = time(NULL);
    tx->total_output = block_reward + total_fees;
    
    // Create coinbase output
    TransactionOutput* output = malloc(sizeof(TransactionOutput));
    memset(output, 0, sizeof(TransactionOutput));
    strcpy(output->address, miner_address);
    output->amount = block_reward + total_fees;
    
    tx->outputs[0] = output;
    tx->output_count = 1;
    
    // Compute transaction hash
    computeSHA256((char*)tx, sizeof(Transaction), tx->tx_hash);
    
    return tx;
}

// Verify transaction
int verifyTransaction(Transaction* tx) {
    // Check if inputs and outputs balance
    if (tx->total_input < tx->total_output + tx->fee) {
        return 0;
    }
    
    // Verify signatures (simplified)
    for (int i = 0; i < tx->input_count; i++) {
        TransactionInput* input = tx->inputs[i];
        if (!verifySignature("message", input->signature, input->public_key)) {
            return 0;
        }
    }
    
    return 1;
}
```

**Transaction Benefits**:
- **Traceability**: Complete transaction history is recorded
- **Security**: Cryptographic signatures prevent unauthorized spending
- **Efficiency**: Optimized transaction processing and verification
- **Flexibility**: Support for various transaction types

## ⛏️ Mining System

### Mining Statistics
```c
// Mining statistics
typedef struct {
    double total_hash_rate;
    int active_miners;
    double difficulty;
    int blocks_mined_today;
    double total_rewards_today;
    time_t last_block_time;
    int orphaned_blocks;
} MiningStats;
```

### Miner Structure
```c
// Miner structure
typedef struct {
    char miner_address[64];
    char* worker_name;
    double hash_rate;
    MiningPool* pool;
    int shares_submitted;
    int shares_accepted;
    double total_rewards;
    time_t last_share_time;
} Miner;
```

### Mining Implementation
```c
// Mine block (Proof of Work)
int mineBlock(Block* block) {
    char target[HASH_SIZE * 2 + 1];
    memset(target, '0', block->difficulty);
    target[block->difficulty] = '\0';
    
    char block_data[BLOCK_SIZE];
    int data_size = sprintf(block_data, "%d%s%s%ld%d", 
                          block->block_number, block->previous_hash, 
                          block->merkle_root, block->timestamp, 
                          block->difficulty);
    
    printf("Mining block %d with difficulty %d...\n", block->block_number, block->difficulty);
    
    for (long nonce = 0; nonce < LONG_MAX; nonce++) {
        block->nonce = nonce;
        
        char hash_data[BLOCK_SIZE];
        int hash_size = sprintf(hash_data, "%s%ld", block_data, nonce);
        
        char hash[HASH_SIZE * 2 + 1];
        computeSHA256(hash_data, hash_size, hash);
        
        if (strncmp(hash, target, block->difficulty) == 0) {
            strcpy(block->hash, hash);
            printf("Block %d mined! Hash: %s, Nonce: %ld\n", block->block_number, hash, nonce);
            return 1;
        }
        
        // Print progress every 100000 attempts
        if (nonce % 100000 == 0) {
            printf("Mining attempt %ld...\n", nonce);
        }
    }
    
    return 0; // Failed to mine block
}

// Start mining
void startMining(Blockchain* blockchain, const char* miner_address) {
    if (!blockchain || !miner_address) return;
    
    blockchain->is_mining = 1;
    
    printf("Started mining for address: %s\n", miner_address);
    
    while (blockchain->is_mining) {
        // Get current block
        Block* current_block = blockchain->blocks[blockchain->block_count];
        
        // Create new block if current block is full
        if (current_block->transaction_count >= MAX_TRANSACTIONS) {
            // Mine current block
            if (mineBlock(current_block)) {
                // Add block to blockchain
                blockchain->blocks[++blockchain->block_count] = current_block;
                
                // Update mining statistics
                blockchain->mining_stats->blocks_mined_today++;
                blockchain->mining_stats->last_block_time = time(NULL);
                
                // Create new block
                Block* new_block = createBlock(current_block, NULL, 0);
                blockchain->blocks[blockchain->block_count] = new_block;
                
                printf("Block %d added to blockchain\n", blockchain->block_count);
            }
        }
        
        // Small delay to prevent CPU spinning
        usleep(1000); // 1ms
    }
}
```

**Mining Benefits**:
- **Security**: Computational work ensures network security
- **Incentives**: Block rewards and fees incentivize participation
- **Decentralization**: Anyone can participate in mining
- **Fair Distribution**: New coins are distributed through mining

## 👛 Wallet Management

### Wallet Structure
```c
// Wallet structure
typedef struct {
    char address[64];
    char private_key[512];
    char public_key[256];
    double balance;
    Transaction* unspent_outputs[MAX_TRANSACTIONS];
    int unspent_count;
    int is_encrypted;
    char* encryption_key;
} Wallet;
```

### Wallet Manager Structure
```c
// Wallet manager structure
typedef struct {
    Wallet* wallets[MAX_ADDRESSES];
    int wallet_count;
    char* wallet_file_path;
    int encryption_enabled;
} WalletManager;
```

### Wallet Implementation
```c
// Create wallet
Wallet* createWallet() {
    Wallet* wallet = malloc(sizeof(Wallet));
    if (!wallet) return NULL;
    
    memset(wallet, 0, sizeof(Wallet));
    
    // Generate key pair
    generateKeyPair(wallet->private_key, wallet->public_key);
    
    // Generate address (simplified)
    sprintf(wallet->address, "addr_%s_%ld", wallet->public_key, time(NULL));
    
    wallet->balance = 0.0;
    wallet->is_encrypted = 0;
    
    return wallet;
}

// Send transaction from wallet
int sendTransaction(Wallet* wallet, const char* to_address, double amount, double fee, Blockchain* blockchain) {
    if (!wallet || !to_address || amount <= 0 || fee < 0) {
        return -1;
    }
    
    // Check balance
    if (wallet->balance < amount + fee) {
        return -2; // Insufficient balance
    }
    
    // Create transaction
    Transaction* tx = createTransaction(wallet->address, to_address, amount, fee);
    if (!tx) {
        return -3;
    }
    
    // Sign transaction (simplified)
    for (int i = 0; i < tx->input_count; i++) {
        tx->inputs[i]->signature = strdup("signature_placeholder");
        tx->inputs[i]->public_key = strdup(wallet->public_key);
    }
    
    // Add transaction to blockchain (in reality, would broadcast to network)
    if (blockchain->blocks[blockchain->block_count]->transaction_count < MAX_TRANSACTIONS) {
        Block* current_block = blockchain->blocks[blockchain->block_count];
        current_block->transactions[current_block->transaction_count++] = tx;
        current_block->total_fees += fee;
        
        // Update wallet balance
        wallet->balance -= (amount + fee);
        
        printf("Transaction sent: %s -> %s, Amount: %.8f, Fee: %.8f\n", 
               wallet->address, to_address, amount, fee);
        
        return 0;
    }
    
    free(tx);
    return -4; // Block full
}
```

**Wallet Benefits**:
- **Security**: Private keys protect access to funds
- **Convenience**: Easy management of multiple addresses
- **Backup**: Wallet files can be backed up for recovery
- **Privacy**: Addresses can be generated for enhanced privacy

## 📜 Smart Contracts

### Contract Types
```c
// Contract types
typedef enum {
    CONTRACT_STANDARD = 0,
    CONTRACT_TOKEN = 1,
    CONTRACT_NFT = 2,
    CONTRACT_DEX = 3,
    CONTRACT_DAO = 4
} ContractType;
```

### Smart Contract Structure
```c
// Smart contract structure
typedef struct {
    char contract_address[64];
    ContractType type;
    char* name;
    char* bytecode;
    int bytecode_size;
    char* abi;
    int abi_size;
    ContractState state;
    Transaction* transactions[MAX_TRANSACTIONS];
    int transaction_count;
} SmartContract;
```

### Smart Contract Implementation
```c
// Create smart contract
SmartContract* createSmartContract(const char* name, const char* bytecode, ContractType type) {
    SmartContract* contract = malloc(sizeof(SmartContract));
    if (!contract) return NULL;
    
    memset(contract, 0, sizeof(SmartContract));
    strncpy(contract->name, name, sizeof(contract->name) - 1);
    contract->bytecode = strdup(bytecode);
    contract->bytecode_size = strlen(bytecode);
    contract->type = type;
    
    // Generate contract address
    sprintf(contract->contract_address, "contract_%ld", time(NULL));
    
    contract->state.is_deployed = 0;
    contract->state.created_time = time(NULL);
    
    return contract;
}

// Deploy contract
int deployContract(Blockchain* blockchain, SmartContract* contract, const char* deployer_address) {
    if (!blockchain || !contract || !deployer_address) {
        return -1;
    }
    
    contract->state.is_deployed = 1;
    contract->state.creator_address = strdup(deployer_address);
    
    // Create deployment transaction
    Transaction* tx = malloc(sizeof(Transaction));
    memset(tx, 0, sizeof(Transaction));
    tx->type = TRANSACTION_CONTRACT;
    tx->timestamp = time(NULL);
    
    // Add contract deployment data to transaction
    // (In reality, would include contract bytecode and constructor parameters)
    
    printf("Contract deployed: %s at address %s\n", contract->name, contract->contract_address);
    
    return 0;
}

// Execute contract
int executeContract(SmartContract* contract, const char* function_name, char* parameters, const char* caller_address) {
    if (!contract || !function_name || !caller_address) {
        return -1;
    }
    
    if (!contract->state.is_deployed) {
        return -2; // Contract not deployed
    }
    
    printf("Executing contract %s: %s(%s) from %s\n", 
           contract->name, function_name, parameters, caller_address);
    
    // Simplified contract execution
    // In reality, would use a virtual machine to execute bytecode
    
    return 0;
}
```

**Smart Contract Benefits**:
- **Automation**: Self-executing contracts reduce manual intervention
- **Transparency**: Contract logic is visible and verifiable
- **Trust**: Code execution is guaranteed by the blockchain
- **Programmability**: Complex business logic can be encoded

## 🌐 Peer-to-Peer Networking

### Node Types
```c
// Node types
typedef enum {
    NODE_FULL = 0,
    NODE_LIGHT = 1,
    NODE_MINING = 2,
    NODE_STAKING = 3
} NodeType;
```

### Peer Structure
```c
// Peer structure
typedef struct {
    char peer_id[64];
    char ip_address[16];
    int port;
    NodeType type;
    int is_connected;
    time_t last_seen;
    int reputation_score;
    double latency_ms;
    long bytes_sent;
    long bytes_received;
} Peer;
```

### P2P Network Implementation
```c
// Create P2P network
P2PNetwork* createP2PNetwork(NodeType node_type, int port) {
    P2PNetwork* network = malloc(sizeof(P2PNetwork));
    if (!network) return NULL;
    
    memset(network, 0, sizeof(P2PNetwork));
    network->node_type = node_type;
    network->port = port;
    network->max_peers = MAX_ADDRESSES;
    
    // Generate node ID
    sprintf(network->node_id, "node_%ld", time(NULL));
    
    return network;
}

// Add peer to network
int addPeer(P2PNetwork* network, const char* ip_address, int port, NodeType peer_type) {
    if (!network || !ip_address || network->peer_count >= network->max_peers) {
        return -1;
    }
    
    Peer* peer = &network->peers[network->peer_count++];
    memset(peer, 0, sizeof(Peer));
    
    sprintf(peer->peer_id, "peer_%ld", time(NULL) + network->peer_count);
    strncpy(peer->ip_address, ip_address, sizeof(peer->ip_address) - 1);
    peer->port = port;
    peer->type = peer_type;
    peer->is_connected = 1;
    peer->last_seen = time(NULL);
    peer->reputation_score = 100;
    
    printf("Added peer: %s:%d (Type: %d)\n", ip_address, port, peer_type);
    
    return network->peer_count - 1;
}

// Broadcast message to network
void broadcastMessage(P2PNetwork* network, NetworkMessage* message) {
    if (!network || !message) return;
    
    printf("Broadcasting message type %d to %d peers\n", message->type, network->peer_count);
    
    for (int i = 0; i < network->peer_count; i++) {
        Peer* peer = &network->peers[i];
        if (peer->is_connected) {
            // Send message to peer (simplified)
            printf("Sending to peer %s:%d\n", peer->ip_address, peer->port);
            peer->bytes_sent += message->payload_size;
        }
    }
}
```

**P2P Network Benefits**:
- **Decentralization**: No central point of failure
- **Resilience**: Network continues operating even if nodes fail
- **Scalability**: Network can grow by adding more peers
- **Privacy**: Direct peer-to-peer communication

## 🤝 Consensus Algorithms

### Consensus Types
```c
// Consensus types
typedef enum {
    CONSENSUS_PROOF_OF_WORK = 0,
    CONSENSUS_PROOF_OF_STAKE = 1,
    CONSENSUS_DELEGATED_PROOF_OF_STAKE = 2,
    CONSENSUS_PROOF_OF_AUTHORITY = 3
} ConsensusType;
```

### Proof of Work Structure
```c
// Proof of Work
typedef struct {
    int difficulty;
    long target;
    char* hash_algorithm;
    int block_time;
} ProofOfWork;
```

### Proof of Stake Structure
```c
// Proof of Stake
typedef struct {
    double total_stake;
    int validator_count;
    double minimum_stake;
    int block_time;
    char* staking_contract;
} ProofOfStake;
```

### Consensus Implementation
```c
// Create consensus
Consensus* createConsensus(ConsensusType type) {
    Consensus* consensus = malloc(sizeof(Consensus));
    if (!consensus) return NULL;
    
    memset(consensus, 0, sizeof(Consensus));
    consensus->type = type;
    consensus->is_active = 1;
    
    switch (type) {
        case CONSENSUS_PROOF_OF_WORK:
            consensus->params.pow.difficulty = MINING_DIFFICULTY;
            consensus->params.pow.block_time = 600; // 10 minutes
            consensus->params.pow.hash_algorithm = "SHA256";
            break;
            
        case CONSENSUS_PROOF_OF_STAKE:
            consensus->params.pos.minimum_stake = 1000.0;
            consensus->params.pos.block_time = 60; // 1 minute
            break;
            
        default:
            break;
    }
    
    return consensus;
}

// Validate block with consensus
int validateBlockWithConsensus(Consensus* consensus, Block* block) {
    if (!consensus || !block) return 0;
    
    switch (consensus->type) {
        case CONSENSUS_PROOF_OF_WORK:
            // Verify proof of work
            return verifyBlock(block, consensus->params.pow.difficulty > 0 ? 
                             blockchain->blocks[block->block_number - 1] : 
                             blockchain->blocks[0]);
            
        case CONSENSUS_PROOF_OF_STAKE:
            // Verify proof of stake
            // (Simplified - would check validator selection and stake)
            return 1;
            
        default:
            return 0;
    }
}
```

**Consensus Benefits**:
- **Agreement**: Ensures all nodes agree on the valid blockchain
- **Security**: Makes it economically expensive to attack the network
- **Fairness**: Provides fair rules for block creation
- **Stability**: Maintains network stability under various conditions

## 🔧 Best Practices

### 1. Memory Management
```c
// Good: Proper memory cleanup
void cleanupBlockchain(Blockchain* blockchain) {
    if (!blockchain) return;
    
    for (int i = 0; i < blockchain->block_count; i++) {
        Block* block = blockchain->blocks[i];
        
        // Cleanup transactions
        for (int j = 0; j < block->transaction_count; j++) {
            Transaction* tx = block->transactions[j];
            
            // Cleanup inputs
            for (int k = 0; k < tx->input_count; k++) {
                free(tx->inputs[k]->signature);
                free(tx->inputs[k]->public_key);
                free(tx->inputs[k]);
            }
            
            // Cleanup outputs
            for (int k = 0; k < tx->output_count; k++) {
                free(tx->outputs[k]->script);
                free(tx->outputs[k]);
            }
            
            free(tx);
        }
        
        free(block);
    }
    
    free(blockchain);
}

// Bad: Memory leaks
void cleanupBlockchainLeaky(Blockchain* blockchain) {
    free(blockchain);
    // Forgot to free blocks, transactions, inputs, outputs - memory leaks!
}
```

### 2. Security
```c
// Good: Secure key management
void secureKeyManagement(Wallet* wallet) {
    // Generate strong random keys
    generateKeyPair(wallet->private_key, wallet->public_key);
    
    // Encrypt private key
    if (!wallet->is_encrypted) {
        wallet->encryption_key = generateEncryptionKey();
        encryptPrivateKey(wallet->private_key, wallet->encryption_key);
        wallet->is_encrypted = 1;
    }
    
    // Clear sensitive data from memory when done
    memset(wallet->private_key, 0, sizeof(wallet->private_key));
}

// Bad: Insecure key handling
void insecureKeyHandling(Wallet* wallet) {
    // Store private key in plain text
    sprintf(wallet->private_key, "simple_key_%d", rand());
    // No encryption - security vulnerability!
}
```

### 3. Error Handling
```c
// Good: Comprehensive error handling
int createTransactionSafe(const char* from, const char* to, double amount, double fee) {
    if (!from || !to) {
        return -1; // Invalid parameters
    }
    
    if (amount <= 0 || fee < 0) {
        return -2; // Invalid amounts
    }
    
    if (strlen(from) > 63 || strlen(to) > 63) {
        return -3; // Invalid address length
    }
    
    Transaction* tx = createTransaction(from, to, amount, fee);
    if (!tx) {
        return -4; // Failed to create transaction
    }
    
    // Validate transaction
    if (!verifyTransaction(tx)) {
        free(tx);
        return -5; // Invalid transaction
    }
    
    return 0; // Success
}

// Bad: No error handling
void createTransactionUnsafe(const char* from, const char* to, double amount, double fee) {
    Transaction* tx = createTransaction(from, to, amount, fee);
    // No validation - can cause crashes
}
```

### 4. Performance Optimization
```c
// Good: Efficient hash caching
typedef struct {
    char* data;
    char* hash;
    time_t cached_time;
} HashCache;

HashCache* createHashCache(int size) {
    HashCache* cache = malloc(sizeof(HashCache) * size);
    if (!cache) return NULL;
    
    for (int i = 0; i < size; i++) {
        cache[i].data = NULL;
        cache[i].hash = NULL;
        cache[i].cached_time = 0;
    }
    
    return cache;
}

char* getCachedHash(HashCache* cache, const char* data, int cache_size) {
    // Check cache first
    for (int i = 0; i < cache_size; i++) {
        if (cache[i].data && strcmp(cache[i].data, data) == 0) {
            return cache[i].hash; // Return cached hash
        }
    }
    
    // Compute new hash and cache it
    char* hash = malloc(HASH_SIZE * 2 + 1);
    computeSHA256(data, strlen(data), hash);
    
    // Find empty slot or replace oldest
    int slot = 0;
    time_t oldest = cache[0].cached_time;
    for (int i = 1; i < cache_size; i++) {
        if (cache[i].cached_time < oldest) {
            oldest = cache[i].cached_time;
            slot = i;
        }
    }
    
    // Update cache
    free(cache[slot].data);
    free(cache[slot].hash);
    cache[slot].data = strdup(data);
    cache[slot].hash = hash;
    cache[slot].cached_time = time(NULL);
    
    return hash;
}

// Bad: No caching - always recompute
char* computeHashSlow(const char* data) {
    char* hash = malloc(HASH_SIZE * 2 + 1);
    computeSHA256(data, strlen(data), hash);
    return hash;
}
```

### 5. Thread Safety
```c
// Good: Thread-safe operations
pthread_mutex_t blockchain_mutex = PTHREAD_MUTEX_INITIALIZER;

int addBlockThreadSafe(Blockchain* blockchain, Block* block) {
    pthread_mutex_lock(&blockchain_mutex);
    
    if (blockchain->block_count >= MAX_BLOCKS) {
        pthread_mutex_unlock(&blockchain_mutex);
        return -1;
    }
    
    blockchain->blocks[blockchain->block_count++] = block;
    
    pthread_mutex_unlock(&blockchain_mutex);
    return 0;
}

// Bad: No synchronization
int addBlockUnsafe(Blockchain* blockchain, Block* block) {
    // No mutex - race condition in multi-threaded environment
    blockchain->blocks[blockchain->block_count++] = block;
    return 0;
}
```

## ⚠️ Common Pitfalls

### 1. Double Spending
```c
// Wrong: No double spending protection
int processTransactionUnsafe(Transaction* tx) {
    // Process transaction without checking if inputs are already spent
    return processPayment(tx->outputs[0]->address, tx->outputs[0]->amount);
}

// Right: UTXO tracking
int processTransactionSafe(Transaction* tx, UTXOSet* utxo_set) {
    // Check if inputs are unspent
    for (int i = 0; i < tx->input_count; i++) {
        if (!isUTXOAvailable(utxo_set, tx->inputs[i])) {
            return -1; // Input already spent
        }
    }
    
    // Mark inputs as spent
    for (int i = 0; i < tx->input_count; i++) {
        markUTXOSpent(utxo_set, tx->inputs[i]);
    }
    
    // Add new UTXOs
    for (int i = 0; i < tx->output_count; i++) {
        addUTXO(utxo_set, tx, i);
    }
    
    return 0;
}
```

### 2. 51% Attack
```c
// Wrong: No protection against majority attack
int validateBlockUnsafe(Block* block) {
    // Only check proof of work
    return verifyProofOfWork(block);
}

// Right: Consensus validation
int validateBlockSafe(Block* block, Consensus* consensus) {
    // Check proof of work
    if (!verifyProofOfWork(block)) {
        return 0;
    }
    
    // Check if block is from valid validator (PoS)
    if (consensus->type == CONSENSUS_PROOF_OF_STAKE) {
        if (!isValidValidator(block->validator, consensus)) {
            return 0;
        }
    }
    
    // Check network consensus
    if (!hasNetworkConsensus(block)) {
        return 0;
    }
    
    return 1;
}
```

### 3. Replay Attacks
```c
// Wrong: No nonce protection
Transaction* createTransactionUnsafe(const char* from, const char* to, double amount) {
    Transaction* tx = malloc(sizeof(Transaction));
    // Create transaction without nonce
    return tx;
}

// Right: Include nonce
Transaction* createTransactionSafe(const char* from, const char* to, double amount, uint64_t nonce) {
    Transaction* tx = malloc(sizeof(Transaction));
    tx->nonce = nonce; // Include nonce to prevent replay
    return tx;
}
```

### 4. Memory Exhaustion
```c
// Wrong: Unlimited memory allocation
void processUnlimitedTransactions(Transaction** transactions) {
    while (1) {
        Transaction* tx = transactions[0];
        // Process without limit - can exhaust memory
    }
}

// Right: Memory limits
void processTransactionsLimited(Transaction** transactions, int max_count) {
    for (int i = 0; i < max_count; i++) {
        Transaction* tx = transactions[i];
        if (!tx) break;
        
        // Process transaction
        processTransaction(tx);
    }
}
```

## 🔧 Real-World Applications

### 1. Cryptocurrency
```c
// Bitcoin-like cryptocurrency
void implementCryptocurrency() {
    Blockchain* bitcoin = createBlockchain();
    bitcoin->difficulty = 1;
    
    // Set up mining
    startMining(bitcoin, "miner-address");
    
    // Create transactions
    Transaction* tx = createTransaction("alice", "bob", 1.0, 0.001);
    addTransactionToMempool(bitcoin, tx);
    
    // Wait for block mining
    waitForNewBlock(bitcoin);
    
    printf("Cryptocurrency implemented\n");
}
```

### 2. Supply Chain Management
```c
// Supply chain tracking
void implementSupplyChain() {
    Blockchain* supply_chain = createBlockchain();
    
    // Create product tracking contract
    SmartContract* contract = createSmartContract("ProductTracker", bytecode, CONTRACT_STANDARD);
    deployContract(supply_chain, contract, "manufacturer-address");
    
    // Track product movement
    executeContract(contract, "trackProduct", "product_id_123", "manufacturer-address");
    executeContract(contract, "transferProduct", "product_id_123,distributor-address", "manufacturer-address");
    executeContract(contract, "transferProduct", "product_id_123,retailer-address", "distributor-address");
    
    printf("Supply chain tracking implemented\n");
}
```

### 3. Digital Identity
```c
// Digital identity management
void implementDigitalIdentity() {
    Blockchain* identity_chain = createBlockchain();
    
    // Create identity contract
    SmartContract* identity_contract = createSmartContract("IdentityManager", bytecode, CONTRACT_STANDARD);
    deployContract(identity_chain, identity_contract, "identity-authority");
    
    // Register identity
    executeContract(identity_contract, "registerIdentity", "user123,public_key_hash", "identity-authority");
    
    // Verify identity
    executeContract(identity_contract, "verifyIdentity", "user123,signature", "verifier-address");
    
    printf("Digital identity management implemented\n");
}
```

### 4. Voting System
```c
// Secure voting system
void implementVotingSystem() {
    Blockchain* voting_chain = createBlockchain();
    
    // Create voting contract
    SmartContract* voting_contract = createSmartContract("VotingSystem", bytecode, CONTRACT_STANDARD);
    deployContract(voting_chain, voting_contract, "election-authority");
    
    // Cast votes
    executeContract(voting_contract, "castVote", "voter123,candidate_a", "voter123");
    executeContract(voting_contract, "castVote", "voter456,candidate_b", "voter456");
    
    // Tally results
    executeContract(voting_contract, "tallyVotes", "election_id", "election-authority");
    
    printf("Secure voting system implemented\n");
}
```

## 📚 Further Reading

### Books
- "Mastering Bitcoin" by Andreas M. Antonopoulos
- "Blockchain Revolution" by Don and Alex Tapscott
- "The Business Blockchain" by William Mougayar
- "Grokking Bitcoin" by Kalle Rosenbaum

### Topics
- Ethereum and smart contract development
- DeFi (Decentralized Finance) protocols
- NFTs and digital collectibles
- DAOs (Decentralized Autonomous Organizations)
- Layer 2 scaling solutions
- Cross-chain interoperability
- Privacy-preserving blockchain technologies
- Quantum-resistant cryptography

Blockchain development in C provides the foundation for building secure, decentralized, and transparent systems. Master these techniques to create robust blockchain applications that can revolutionize various industries and enable new paradigms in digital trust and value transfer!
