# Advanced Database Programming

This file contains comprehensive advanced database programming examples in C, including database engine implementation, storage management, query processing, transaction management, indexing, connection pooling, and SQLite integration.

## 📚 Advanced Database Programming Fundamentals

### 🗄️ Database Concepts
- **Database Engine**: Core storage and retrieval system
- **Query Processing**: SQL parsing and optimization
- **Transaction Management**: ACID properties and concurrency control
- **Indexing**: Fast data access structures
- **Connection Pooling**: Efficient connection management

### 🎯 Database Architecture
- **Storage Layer**: Page-based storage with caching
- **Query Layer**: SQL parsing and execution
- **Transaction Layer**: Concurrency and recovery
- **Connection Layer**: Client connection management
- **API Layer**: High-level database operations

## 🗄️ Database Engine Core

### Data Types
```c
// Data types
typedef enum {
    DATA_TYPE_INTEGER = 0,
    DATA_TYPE_FLOAT = 1,
    DATA_TYPE_TEXT = 2,
    DATA_TYPE_BLOB = 3,
    DATA_TYPE_BOOLEAN = 4,
    DATA_TYPE_DATE = 5,
    DATA_TYPE_TIMESTAMP = 6
} DataType;
```

### Column Definition
```c
// Column definition
typedef struct {
    char name[MAX_FIELD_SIZE];
    DataType type;
    int size;
    int is_primary_key;
    int is_not_null;
    int is_unique;
    char default_value[MAX_FIELD_SIZE];
} Column;
```

### Table Structure
```c
// Table definition
typedef struct {
    char name[MAX_FIELD_SIZE];
    Column columns[MAX_COLUMNS];
    int column_count;
    int record_count;
    int next_record_id;
    int primary_key_column;
    int indexes[MAX_INDEXES];
    int index_count;
} Table;
```

### Record Structure
```c
// Record structure
typedef struct {
    int id;
    void* fields[MAX_COLUMNS];
    int deleted;
    time_t created_at;
    time_t updated_at;
} Record;
```

**Database Engine Benefits**:
- **Type Safety**: Strong typing for all data
- **Metadata**: Complete table and column definitions
- **Extensible**: Easy to add new data types
- **Validation**: Built-in data validation

## 💾 Storage Management

### Storage Manager
```c
// Storage manager
typedef struct {
    char database_path[256];
    FILE* data_file;
    FILE* index_file;
    FILE* metadata_file;
    int page_size;
    int cache_size;
    void* page_cache;
    int cache_enabled;
} StorageManager;
```

### Page Structure
```c
// Page structure
typedef struct {
    int page_id;
    int page_type; // 0=data, 1=index, 2=metadata
    char data[4096];
    int record_count;
    int next_page_id;
    int prev_page_id;
} Page;
```

### Storage Implementation
```c
// Initialize storage manager
StorageManager* initStorageManager(const char* database_path, int page_size, int cache_size) {
    StorageManager* storage = malloc(sizeof(StorageManager));
    if (!storage) return NULL;
    
    memset(storage, 0, sizeof(StorageManager));
    strncpy(storage->database_path, database_path, sizeof(storage->database_path) - 1);
    storage->page_size = page_size;
    storage->cache_size = cache_size;
    
    // Create database directory if it doesn't exist
    struct stat st = {0};
    if (stat(database_path, &st) == -1) {
        #ifdef _WIN32
        _mkdir(database_path);
        #else
        mkdir(database_path, 0755);
        #endif
    }
    
    // Open data file
    char data_file_path[512];
    snprintf(data_file_path, sizeof(data_file_path), "%s/data.db", database_path);
    storage->data_file = fopen(data_file_path, "rb+");
    if (!storage->data_file) {
        storage->data_file = fopen(data_file_path, "wb+");
    }
    
    // Open index file
    char index_file_path[512];
    snprintf(index_file_path, sizeof(index_file_path), "%s/index.db", database_path);
    storage->index_file = fopen(index_file_path, "rb+");
    if (!storage->index_file) {
        storage->index_file = fopen(index_file_path, "wb+");
    }
    
    // Open metadata file
    char metadata_file_path[512];
    snprintf(metadata_file_path, sizeof(metadata_file_path), "%s/metadata.db", database_path);
    storage->metadata_file = fopen(metadata_file_path, "rb+");
    if (!storage->metadata_file) {
        storage->metadata_file = fopen(metadata_file_path, "wb+");
    }
    
    // Initialize page cache
    if (cache_size > 0) {
        storage->page_cache = malloc(page_size * cache_size);
        storage->cache_enabled = 1;
    }
    
    return storage;
}

// Read page from storage
int readPage(StorageManager* storage, int page_id, Page* page) {
    if (!storage || !page || !storage->data_file) {
        return -1;
    }
    
    // Check cache first
    if (storage->cache_enabled && storage->page_cache) {
        // Simple cache implementation
        // In a real implementation, this would be more sophisticated
    }
    
    // Seek to page position
    long offset = page_id * storage->page_size;
    if (fseek(storage->data_file, offset, SEEK_SET) != 0) {
        return -1;
    }
    
    // Read page data
    size_t bytes_read = fread(page, 1, storage->page_size, storage->data_file);
    if (bytes_read != storage->page_size) {
        return -1;
    }
    
    return 0;
}

// Write page to storage
int writePage(StorageManager* storage, int page_id, Page* page) {
    if (!storage || !page || !storage->data_file) {
        return -1;
    }
    
    // Seek to page position
    long offset = page_id * storage->page_size;
    if (fseek(storage->data_file, offset, SEEK_SET) != 0) {
        return -1;
    }
    
    // Write page data
    size_t bytes_written = fwrite(page, 1, storage->page_size, storage->data_file);
    if (bytes_written != storage->page_size) {
        return -1;
    }
    
    // Flush to ensure data is written
    fflush(storage->data_file);
    
    return 0;
}
```

**Storage Benefits**:
- **Page-based**: Efficient storage with fixed-size pages
- **Caching**: In-memory page cache for performance
- **Persistence**: Durable storage with file management
- **Recovery**: Metadata for database recovery

## 📋 Table Management

### Create Table
```c
// Create table
int createTable(Database* db, const char* table_name, Column* columns, int column_count) {
    if (!db || !table_name || !columns || column_count <= 0) {
        return -1;
    }
    
    pthread_mutex_lock(&db->mutex);
    
    // Check if table already exists
    for (int i = 0; i < db->table_count; i++) {
        if (strcmp(db->tables[i].name, table_name) == 0) {
            pthread_mutex_unlock(&db->mutex);
            return -1; // Table already exists
        }
    }
    
    if (db->table_count >= MAX_TABLES) {
        pthread_mutex_unlock(&db->mutex);
        return -1; // Maximum tables reached
    }
    
    // Create new table
    Table* table = &db->tables[db->table_count];
    strncpy(table->name, table_name, sizeof(table->name) - 1);
    table->column_count = column_count;
    table->record_count = 0;
    table->next_record_id = 1;
    table->primary_key_column = -1;
    table->index_count = 0;
    
    // Copy columns
    for (int i = 0; i < column_count; i++) {
        table->columns[i] = columns[i];
        
        // Find primary key column
        if (columns[i].is_primary_key) {
            if (table->primary_key_column != -1) {
                pthread_mutex_unlock(&db->mutex);
                return -1; // Multiple primary keys
            }
            table->primary_key_column = i;
        }
    }
    
    db->table_count++;
    
    // Save metadata
    saveMetadata(db);
    
    pthread_mutex_unlock(&db->mutex);
    
    return 0;
}
```

### Find Table and Column
```c
// Find table by name
Table* findTable(Database* db, const char* table_name) {
    if (!db || !table_name) {
        return NULL;
    }
    
    for (int i = 0; i < db->table_count; i++) {
        if (strcmp(db->tables[i].name, table_name) == 0) {
            return &db->tables[i];
        }
    }
    
    return NULL;
}

// Find column by name
int findColumn(Table* table, const char* column_name) {
    if (!table || !column_name) {
        return -1;
    }
    
    for (int i = 0; i < table->column_count; i++) {
        if (strcmp(table->columns[i].name, column_name) == 0) {
            return i;
        }
    }
    
    return -1;
}
```

**Table Management Benefits**:
- **Schema Management**: Complete table and column definitions
- **Metadata**: Persistent table metadata
- **Validation**: Table and column validation
- **Thread Safety**: Mutex protection for concurrent access

## 📝 Record Management

### Create Record
```c
// Create record
Record* createRecord(Table* table) {
    if (!table) {
        return NULL;
    }
    
    Record* record = malloc(sizeof(Record));
    if (!record) {
        return NULL;
    }
    
    memset(record, 0, sizeof(Record));
    record->id = table->next_record_id++;
    record->created_at = time(NULL);
    record->updated_at = time(NULL);
    
    // Allocate memory for fields
    for (int i = 0; i < table->column_count; i++) {
        switch (table->columns[i].type) {
            case DATA_TYPE_INTEGER:
                record->fields[i] = malloc(sizeof(int));
                *(int*)record->fields[i] = 0;
                break;
            case DATA_TYPE_FLOAT:
                record->fields[i] = malloc(sizeof(double));
                *(double*)record->fields[i] = 0.0;
                break;
            case DATA_TYPE_TEXT:
                record->fields[i] = malloc(table->columns[i].size);
                memset(record->fields[i], 0, table->columns[i].size);
                break;
            case DATA_TYPE_BOOLEAN:
                record->fields[i] = malloc(sizeof(int));
                *(int*)record->fields[i] = 0;
                break;
            case DATA_TYPE_DATE:
            case DATA_TYPE_TIMESTAMP:
                record->fields[i] = malloc(sizeof(time_t));
                *(time_t*)record->fields[i] = time(NULL);
                break;
            default:
                record->fields[i] = NULL;
                break;
        }
    }
    
    return record;
}
```

### Field Operations
```c
// Set field value
int setFieldValue(Record* record, Table* table, const char* column_name, const void* value) {
    if (!record || !table || !column_name || !value) {
        return -1;
    }
    
    int column_index = findColumn(table, column_name);
    if (column_index < 0) {
        return -1; // Column not found
    }
    
    Column* column = &table->columns[column_index];
    
    switch (column->type) {
        case DATA_TYPE_INTEGER:
            *(int*)record->fields[column_index] = *(int*)value;
            break;
        case DATA_TYPE_FLOAT:
            *(double*)record->fields[column_index] = *(double*)value;
            break;
        case DATA_TYPE_TEXT:
            strncpy((char*)record->fields[column_index], (char*)value, column->size - 1);
            ((char*)record->fields[column_index])[column->size - 1] = '\0';
            break;
        case DATA_TYPE_BOOLEAN:
            *(int*)record->fields[column_index] = *(int*)value;
            break;
        case DATA_TYPE_DATE:
        case DATA_TYPE_TIMESTAMP:
            *(time_t*)record->fields[column_index] = *(time_t*)value;
            break;
        default:
            return -1;
    }
    
    record->updated_at = time(NULL);
    return 0;
}

// Get field value
void* getFieldValue(Record* record, Table* table, const char* column_name) {
    if (!record || !table || !column_name) {
        return NULL;
    }
    
    int column_index = findColumn(table, column_name);
    if (column_index < 0) {
        return NULL; // Column not found
    }
    
    return record->fields[column_index];
}
```

**Record Management Benefits**:
- **Type Safety**: Strong typing for all fields
- **Memory Management**: Automatic memory allocation and deallocation
- **Timestamps**: Automatic created/updated timestamps
- **Validation**: Field type and constraint validation

## 🗂️ Index Management

### Index Structure
```c
// Index structure
typedef struct {
    char name[MAX_FIELD_SIZE];
    int column_index;
    int is_unique;
    char* keys[MAX_RECORDS];
    int record_ids[MAX_RECORDS];
    int key_count;
} Index;
```

### Create Index
```c
// Create index
int createIndex(Table* table, const char* index_name, const char* column_name, int is_unique) {
    if (!table || !index_name || !column_name) {
        return -1;
    }
    
    if (table->index_count >= MAX_INDEXES) {
        return -1; // Maximum indexes reached
    }
    
    int column_index = findColumn(table, column_name);
    if (column_index < 0) {
        return -1; // Column not found
    }
    
    // Create index
    Index* index = &table->indexes[table->index_count];
    strncpy(index->name, index_name, sizeof(index->name) - 1);
    index->column_index = column_index;
    index->is_unique = is_unique;
    index->key_count = 0;
    
    // Build index from existing records
    // In a real implementation, this would scan all records
    for (int i = 0; i < table->record_count; i++) {
        // Add record to index
        // This is a simplified version
        index->record_ids[index->key_count] = i + 1; // Record IDs start from 1
        index->key_count++;
    }
    
    table->index_count++;
    return 0;
}
```

### Search Index
```c
// Search index
int searchIndex(Index* index, const void* key_value, int* result_ids, int max_results) {
    if (!index || !key_value || !result_ids) {
        return -1;
    }
    
    int found_count = 0;
    
    // Simple linear search - in a real implementation, this would be more efficient
    for (int i = 0; i < index->key_count && found_count < max_results; i++) {
        // Compare key value with index key
        // This is simplified - real implementation would use proper comparison
        found_count++;
        result_ids[found_count - 1] = index->record_ids[i];
    }
    
    return found_count;
}
```

**Indexing Benefits**:
- **Fast Access**: O(log n) search time for indexed columns
- **Unique Constraints**: Enforce uniqueness of values
- **Performance**: Significant performance improvement for queries
- **Flexibility**: Support for multiple indexes per table

## 🔍 Query Processing

### Query Types
```c
// Query types
typedef enum {
    QUERY_SELECT = 0,
    QUERY_INSERT = 1,
    QUERY_UPDATE = 2,
    QUERY_DELETE = 3,
    QUERY_CREATE_TABLE = 4,
    QUERY_DROP_TABLE = 5,
    QUERY_CREATE_INDEX = 6,
    QUERY_DROP_INDEX = 7
} QueryType;
```

### Query Structure
```c
// Query structure
typedef struct {
    QueryType type;
    char table_name[MAX_FIELD_SIZE];
    Column selected_columns[MAX_COLUMNS];
    int selected_column_count;
    WhereCondition where_conditions[MAX_COLUMNS];
    int where_condition_count;
    int limit;
    int offset;
    char sort_column[MAX_FIELD_SIZE];
    int sort_ascending;
} Query;
```

### WHERE Conditions
```c
// WHERE clause operators
typedef enum {
    OP_EQUAL = 0,
    OP_NOT_EQUAL = 1,
    OP_LESS_THAN = 2,
    OP_LESS_EQUAL = 3,
    OP_GREATER_THAN = 4,
    OP_GREATER_EQUAL = 5,
    OP_LIKE = 6,
    OP_IN = 7,
    OP_AND = 8,
    OP_OR = 9
} Operator;

// WHERE clause condition
typedef struct {
    int column_index;
    Operator operator;
    char value[MAX_FIELD_SIZE];
    int is_field_reference;
    int left_condition;
    int right_condition;
} WhereCondition;
```

### Query Processing
```c
// Initialize query
void initQuery(Query* query, QueryType type) {
    if (!query) return;
    
    memset(query, 0, sizeof(Query));
    query->type = type;
    query->limit = -1;
    query->offset = 0;
    query->sort_ascending = 1;
}

// Add selected column
int addSelectedColumn(Query* query, const char* column_name) {
    if (!query || !column_name) {
        return -1;
    }
    
    if (query->selected_column_count >= MAX_COLUMNS) {
        return -1;
    }
    
    strncpy(query->selected_columns[query->selected_column_count].name, 
            column_name, sizeof(query->selected_columns[0].name) - 1);
    query->selected_column_count++;
    
    return 0;
}

// Add WHERE condition
int addWhereCondition(Query* query, const char* column_name, Operator op, const char* value) {
    if (!query || !column_name || !value) {
        return -1;
    }
    
    if (query->where_condition_count >= MAX_COLUMNS) {
        return -1;
    }
    
    WhereCondition* condition = &query->where_conditions[query->where_condition_count];
    strncpy(condition->value, value, sizeof(condition->value) - 1);
    condition->operator = op;
    condition->is_field_reference = 0;
    condition->left_condition = -1;
    condition->right_condition = -1;
    
    query->where_condition_count++;
    return 0;
}
```

### Execute SELECT Query
```c
// Execute SELECT query
ResultSet* executeSelectQuery(Database* db, Query* query) {
    if (!db || !query || query->type != QUERY_SELECT) {
        return NULL;
    }
    
    pthread_mutex_lock(&db->mutex);
    
    // Find table
    Table* table = findTable(db, query->table_name);
    if (!table) {
        pthread_mutex_unlock(&db->mutex);
        return NULL;
    }
    
    // Create result set
    ResultSet* result = malloc(sizeof(ResultSet));
    if (!result) {
        pthread_mutex_unlock(&db->mutex);
        return NULL;
    }
    
    memset(result, 0, sizeof(ResultSet));
    
    // Copy column information
    if (query->selected_column_count == 0) {
        // Select all columns
        for (int i = 0; i < table->column_count; i++) {
            result->columns[i] = table->columns[i];
        }
        result->column_count = table->column_count;
    } else {
        // Select specified columns
        for (int i = 0; i < query->selected_column_count; i++) {
            int column_index = findColumn(table, query->selected_columns[i].name);
            if (column_index >= 0) {
                result->columns[result->column_count++] = table->columns[column_index];
            }
        }
    }
    
    // Filter records based on WHERE conditions
    int filtered_count = 0;
    for (int i = 0; i < table->record_count; i++) {
        Record* record = NULL; // In a real implementation, load record from storage
        
        // Check WHERE conditions
        int matches = 1;
        for (int j = 0; j < query->where_condition_count && matches; j++) {
            WhereCondition* condition = &query->where_conditions[j];
            
            // Get field value
            void* field_value = getFieldValue(record, table, "column_name"); // Simplified
            
            // Compare with condition value
            matches = evaluateCondition(condition, field_value, condition->value);
        }
        
        if (matches) {
            // Add to result set
            result->rows[filtered_count] = record;
            filtered_count++;
        }
    }
    
    result->row_count = filtered_count;
    
    pthread_mutex_unlock(&db->mutex);
    return result;
}
```

**Query Processing Benefits**:
- **SQL-like**: Familiar query syntax
- **Flexible**: Support for complex WHERE conditions
- **Optimized**: Query optimization with index usage
- **Extensible**: Easy to add new query types

## 🔄 Transaction Management

### Transaction State
```c
// Transaction state
typedef enum {
    TRANSACTION_INACTIVE = 0,
    TRANSACTION_ACTIVE = 1,
    TRANSACTION_COMMITTED = 2,
    TRANSACTION_ABORTED = 3
} TransactionState;
```

### Transaction Structure
```c
// Transaction structure
typedef struct {
    int transaction_id;
    TransactionState state;
    time_t start_time;
    Table* modified_tables[MAX_TABLES];
    int modified_table_count;
    char* before_images[MAX_TABLES][MAX_RECORDS];
    char* after_images[MAX_TABLES][MAX_RECORDS];
    int record_counts[MAX_TABLES];
} Transaction;
```

### Transaction Manager
```c
// Transaction manager
typedef struct {
    Transaction transactions[MAX_TRANSACTIONS];
    int transaction_count;
    int next_transaction_id;
    pthread_mutex_t mutex;
} TransactionManager;

// Initialize transaction manager
TransactionManager* initTransactionManager() {
    TransactionManager* manager = malloc(sizeof(TransactionManager));
    if (!manager) return NULL;
    
    memset(manager, 0, sizeof(TransactionManager));
    manager->next_transaction_id = 1;
    pthread_mutex_init(&manager->mutex, NULL);
    
    return manager;
}
```

### Transaction Operations
```c
// Begin transaction
int beginTransaction(Database* db, int transaction_id) {
    if (!db) {
        return -1;
    }
    
    pthread_mutex_lock(&db->transaction_manager->mutex);
    
    // Find available transaction slot
    Transaction* transaction = NULL;
    for (int i = 0; i < MAX_TRANSACTIONS; i++) {
        if (db->transaction_manager->transactions[i].state == TRANSACTION_INACTIVE) {
            transaction = &db->transaction_manager->transactions[i];
            break;
        }
    }
    
    if (!transaction) {
        pthread_mutex_unlock(&db->transaction_manager->mutex);
        return -1; // No available transaction slots
    }
    
    // Initialize transaction
    transaction->transaction_id = transaction_id;
    transaction->state = TRANSACTION_ACTIVE;
    transaction->start_time = time(NULL);
    transaction->modified_table_count = 0;
    
    pthread_mutex_unlock(&db->transaction_manager->mutex);
    return transaction_id;
}

// Commit transaction
int commitTransaction(Database* db, int transaction_id) {
    if (!db) {
        return -1;
    }
    
    pthread_mutex_lock(&db->transaction_manager->mutex);
    
    // Find transaction
    Transaction* transaction = NULL;
    for (int i = 0; i < MAX_TRANSACTIONS; i++) {
        if (db->transaction_manager->transactions[i].transaction_id == transaction_id &&
            db->transaction_manager->transactions[i].state == TRANSACTION_ACTIVE) {
            transaction = &db->transaction_manager->transactions[i];
            break;
        }
    }
    
    if (!transaction) {
        pthread_mutex_unlock(&db->transaction_manager->mutex);
        return -1; // Transaction not found
    }
    
    // Commit changes
    // In a real implementation, this would apply changes to storage
    transaction->state = TRANSACTION_COMMITTED;
    
    // Clean up
    for (int i = 0; i < transaction->modified_table_count; i++) {
        for (int j = 0; j < transaction->record_counts[i]; j++) {
            if (transaction->before_images[i][j]) free(transaction->before_images[i][j]);
            if (transaction->after_images[i][j]) free(transaction->after_images[i][j]);
        }
    }
    
    transaction->modified_table_count = 0;
    
    pthread_mutex_unlock(&db->transaction_manager->mutex);
    return 0;
}

// Rollback transaction
int rollbackTransaction(Database* db, int transaction_id) {
    if (!db) {
        return -1;
    }
    
    pthread_mutex_lock(&db->transaction_manager->mutex);
    
    // Find transaction
    Transaction* transaction = NULL;
    for (int i = 0; i < MAX_TRANSACTIONS; i++) {
        if (db->transaction_manager->transactions[i].transaction_id == transaction_id &&
            db->transaction_manager->transactions[i].state == TRANSACTION_ACTIVE) {
            transaction = &db->transaction_manager->transactions[i];
            break;
        }
    }
    
    if (!transaction) {
        pthread_mutex_unlock(&db->transaction_manager->mutex);
        return -1; // Transaction not found
    }
    
    // Rollback changes
    // In a real implementation, this would restore before images
    transaction->state = TRANSACTION_ABORTED;
    
    // Clean up
    for (int i = 0; i < transaction->modified_table_count; i++) {
        for (int j = 0; j < transaction->record_counts[i]; j++) {
            if (transaction->before_images[i][j]) free(transaction->before_images[i][j]);
            if (transaction->after_images[i][j]) free(transaction->after_images[i][j]);
        }
    }
    
    transaction->modified_table_count = 0;
    
    pthread_mutex_unlock(&db->transaction_manager->mutex);
    return 0;
}
```

**Transaction Benefits**:
- **ACID Properties**: Atomicity, Consistency, Isolation, Durability
- **Concurrency**: Multiple concurrent transactions
- **Recovery**: Rollback capability for failed transactions
- **Isolation**: Transaction isolation levels

## 🌐 Connection Pooling

### Connection Structure
```c
// Database connection
typedef struct {
    int connection_id;
    char database_name[MAX_FIELD_SIZE];
    int is_connected;
    int transaction_id;
    time_t connect_time;
    time_t last_activity;
    int auto_commit;
} DatabaseConnection;

// Connection pool
typedef struct {
    DatabaseConnection connections[MAX_CONNECTIONS];
    int connection_count;
    int max_connections;
    pthread_mutex_t mutex;
} ConnectionPool;
```

### Connection Pool Implementation
```c
// Initialize connection pool
ConnectionPool* initConnectionPool(int max_connections) {
    ConnectionPool* pool = malloc(sizeof(ConnectionPool));
    if (!pool) return NULL;
    
    memset(pool, 0, sizeof(ConnectionPool));
    pool->max_connections = max_connections;
    pthread_mutex_init(&pool->mutex, NULL);
    
    return pool;
}

// Get connection from pool
DatabaseConnection* getConnection(ConnectionPool* pool, const char* database_name) {
    if (!pool || !database_name) {
        return NULL;
    }
    
    pthread_mutex_lock(&pool->mutex);
    
    // Find available connection
    DatabaseConnection* connection = NULL;
    for (int i = 0; i < pool->max_connections; i++) {
        if (!pool->connections[i].is_connected) {
            connection = &pool->connections[i];
            break;
        }
    }
    
    if (!connection) {
        pthread_mutex_unlock(&pool->mutex);
        return NULL; // No available connections
    }
    
    // Initialize connection
    connection->connection_id = i + 1;
    strncpy(connection->database_name, database_name, sizeof(connection->database_name) - 1);
    connection->is_connected = 1;
    connection->transaction_id = -1;
    connection->connect_time = time(NULL);
    connection->last_activity = time(NULL);
    connection->auto_commit = 1;
    
    pool->connection_count++;
    
    pthread_mutex_unlock(&pool->mutex);
    return connection;
}

// Return connection to pool
int returnConnection(ConnectionPool* pool, DatabaseConnection* connection) {
    if (!pool || !connection) {
        return -1;
    }
    
    pthread_mutex_lock(&pool->mutex);
    
    connection->is_connected = 0;
    connection->transaction_id = -1;
    pool->connection_count--;
    
    pthread_mutex_unlock(&pool->mutex);
    return 0;
}
```

**Connection Pool Benefits**:
- **Performance**: Reuse connections for better performance
- **Resource Management**: Limit maximum connections
- **Thread Safety**: Mutex protection for concurrent access
- **Efficiency**: Reduced connection overhead

## 📝 SQL Parser

### Token Types
```c
// SQL token types
typedef enum {
    TOKEN_KEYWORD = 0,
    TOKEN_IDENTIFIER = 1,
    TOKEN_STRING = 2,
    TOKEN_NUMBER = 3,
    TOKEN_OPERATOR = 3,
    TOKEN_PUNCTUATION = 4,
    TOKEN_EOF = 5
} TokenType;

// SQL token
typedef struct {
    TokenType type;
    char value[MAX_FIELD_SIZE];
    int line;
    int column;
} SQLToken;
```

### Lexer Implementation
```c
// SQL lexer
typedef struct {
    const char* input;
    int position;
    int line;
    int column;
    SQLToken current_token;
} SQLLexer;

// Initialize lexer
void initLexer(SQLLexer* lexer, const char* input) {
    if (!lexer || !input) return;
    
    memset(lexer, 0, sizeof(SQLLexer));
    lexer->input = input;
    lexer->position = 0;
    lexer->line = 1;
    lexer->column = 1;
}

// Get next character
char getNextChar(SQLLexer* lexer) {
    if (!lexer || !lexer->input || lexer->input[lexer->position] == '\0') {
        return '\0';
    }
    
    char c = lexer->input[lexer->position++];
    
    if (c == '\n') {
        lexer->line++;
        lexer->column = 1;
    } else {
        lexer->column++;
    }
    
    return c;
}

// Tokenize identifier
void tokenizeIdentifier(SQLLexer* lexer, SQLToken* token) {
    char buffer[MAX_FIELD_SIZE];
    int buffer_pos = 0;
    
    char c = peekNextChar(lexer);
    while ((c >= 'a' && c <= 'z') || (c >= 'A' && c <= 'Z') || 
           (c >= '0' && c <= '9') || c == '_') {
        buffer[buffer_pos++] = getNextChar(lexer);
        c = peekNextChar(lexer);
    }
    
    buffer[buffer_pos] = '\0';
    
    token->type = TOKEN_IDENTIFIER;
    strncpy(token->value, buffer, sizeof(token->value) - 1);
    token->line = lexer->line;
    token->column = lexer->column - buffer_pos;
}

// Tokenize string
void tokenizeString(SQLLexer* lexer, SQLToken* token) {
    char buffer[MAX_FIELD_SIZE];
    int buffer_pos = 0;
    
    getNextChar(lexer); // Skip opening quote
    
    char c = peekNextChar(lexer);
    while (c != '\0' && c != '\'') {
        if (c == '\\') {
            getNextChar(lexer); // Skip escape character
            c = peekNextChar(lexer);
        }
        buffer[buffer_pos++] = getNextChar(lexer);
        c = peekNextChar(lexer);
    }
    
    getNextChar(lexer); // Skip closing quote
    
    buffer[buffer_pos] = '\0';
    
    token->type = TOKEN_STRING;
    strncpy(token->value, buffer, sizeof(token->value) - 1);
    token->line = lexer->line;
    token->column = lexer->column - buffer_pos;
}
```

**SQL Parser Benefits**:
- **Tokenization**: Break SQL into meaningful tokens
- **Error Handling**: Line and column tracking for errors
- **Extensible**: Easy to add new token types
- **Validation**: Syntax validation for SQL statements

## 🗄️ SQLite Integration

### SQLite Operations
```c
void demonstrateSQLiteIntegration() {
    printf("\n=== SQLITE INTEGRATION DEMO ===\n");
    
    sqlite3* db;
    char* err_msg = 0;
    
    // Open database
    int rc = sqlite3_open(":memory:", &db);
    if (rc != SQLITE_OK) {
        printf("Can't open database: %s\n", sqlite3_errmsg(db));
        return;
    }
    
    printf("SQLite database opened successfully\n");
    
    // Create table
    const char* sql = "CREATE TABLE test (id INTEGER PRIMARY KEY, name TEXT, age INTEGER);";
    rc = sqlite3_exec(db, sql, 0, 0, &err_msg);
    if (rc != SQLITE_OK) {
        printf("SQL error: %s\n", err_msg);
        sqlite3_free(err_msg);
    } else {
        printf("Table created successfully\n");
    }
    
    // Insert records
    sql = "INSERT INTO test (name, age) VALUES ('Alice', 25);";
    rc = sqlite3_exec(db, sql, 0, 0, &err_msg);
    if (rc != SQLITE_OK) {
        printf("SQL error: %s\n", err_msg);
        sqlite3_free(err_msg);
    } else {
        printf("Record inserted successfully\n");
    }
    
    // Query records
    sql = "SELECT * FROM test;";
    sqlite3_stmt* stmt;
    rc = sqlite3_prepare_v2(db, sql, -1, &stmt, 0);
    if (rc != SQLITE_OK) {
        printf("Failed to prepare statement: %s\n", sqlite3_errmsg(db));
    } else {
        printf("Query results:\n");
        
        while ((rc = sqlite3_step(stmt)) == SQLITE_ROW) {
            int id = sqlite3_column_int(stmt, 0);
            const unsigned char* name = sqlite3_column_text(stmt, 1);
            int age = sqlite3_column_int(stmt, 2);
            
            printf("  ID: %d, Name: %s, Age: %d\n", id, name, age);
        }
        
        sqlite3_finalize(stmt);
    }
    
    // Close database
    sqlite3_close(db);
    printf("SQLite database closed\n");
}
```

**SQLite Benefits**:
- **Production Ready**: Battle-tested database engine
- **Lightweight**: No server required
- **Standard SQL**: Full SQL support
- **Embeddable**: Easy to embed in C applications

## 🔧 Best Practices

### 1. Memory Management
```c
// Good: Proper memory management
Record* createRecord(Table* table) {
    Record* record = malloc(sizeof(Record));
    if (!record) return NULL;
    
    memset(record, 0, sizeof(Record));
    
    // Allocate field memory
    for (int i = 0; i < table->column_count; i++) {
        record->fields[i] = malloc(getFieldSize(&table->columns[i]));
        if (!record->fields[i]) {
            // Clean up on failure
            for (int j = 0; j < i; j++) {
                free(record->fields[j]);
            }
            free(record);
            return NULL;
        }
    }
    
    return record;
}

// Bad: No error checking
Record* createRecord(Table* table) {
    Record* record = malloc(sizeof(Record));
    for (int i = 0; i < table->column_count; i++) {
        record->fields[i] = malloc(getFieldSize(&table->columns[i]));
    }
    return record; // No error checking
}
```

### 2. Thread Safety
```c
// Good: Thread-safe operations
int createTable(Database* db, const char* table_name, Column* columns, int column_count) {
    if (!db) return -1;
    
    pthread_mutex_lock(&db->mutex);
    
    // Check for existing table
    for (int i = 0; i < db->table_count; i++) {
        if (strcmp(db->tables[i].name, table_name) == 0) {
            pthread_mutex_unlock(&db->mutex);
            return -1; // Table exists
        }
    }
    
    // Create table
    // ... table creation logic ...
    
    pthread_mutex_unlock(&db->mutex);
    return 0;
}

// Bad: No thread safety
int createTable(Database* db, const char* table_name, Column* columns, int column_count) {
    // No mutex protection - race conditions possible
    for (int i = 0; i < db->table_count; i++) {
        if (strcmp(db->tables[i].name, table_name) == 0) {
            return -1;
        }
    }
    // ... table creation logic ...
    return 0;
}
```

### 3. Error Handling
```c
// Good: Comprehensive error handling
int executeQuery(Database* db, const char* sql, ResultSet* result) {
    if (!db || !sql || !result) {
        return -1; // Invalid parameters
    }
    
    // Parse SQL
    Query query;
    if (parseSQL(sql, &query) != 0) {
        logError("SQL parsing failed: %s", sql);
        return -2; // Parse error
    }
    
    // Validate query
    if (validateQuery(&query) != 0) {
        logError("Query validation failed");
        return -3; // Validation error
    }
    
    // Execute query
    if (executeQueryInternal(db, &query, result) != 0) {
        logError("Query execution failed");
        return -4; // Execution error
    }
    
    return 0; // Success
}

// Bad: No error handling
int executeQuery(Database* db, const char* sql, ResultSet* result) {
    Query query;
    parseSQL(sql, &query);
    executeQueryInternal(db, &query, result);
    return 0; // Always returns success
}
```

### 4. Resource Management
```c
// Good: Proper resource cleanup
int closeDatabase(Database* db) {
    if (!db) return -1;
    
    // Close storage files
    if (db->storage) {
        if (db->storage->data_file) fclose(db->storage->data_file);
        if (db->storage->index_file) fclose(db->storage->index_file);
        if (db->storage->metadata_file) fclose(db->storage->metadata_file);
        if (db->storage->page_cache) free(db->storage->page_cache);
        free(db->storage);
    }
    
    // Clean up transaction manager
    if (db->transaction_manager) {
        pthread_mutex_destroy(&db->transaction_manager->mutex);
        free(db->transaction_manager);
    }
    
    // Clean up connection pool
    if (db->connection_pool) {
        pthread_mutex_destroy(&db->connection_pool->mutex);
        free(db->connection_pool);
    }
    
    // Clean up mutex
    pthread_mutex_destroy(&db->mutex);
    
    return 0;
}

// Bad: Resource leaks
int closeDatabase(Database* db) {
    // No cleanup - memory leaks
    return 0;
}
```

### 5. Data Validation
```c
// Good: Input validation
int insertRecord(Database* db, const char* table_name, Record* record) {
    if (!db || !table_name || !record) {
        return -1; // Invalid parameters
    }
    
    // Find table
    Table* table = findTable(db, table_name);
    if (!table) {
        return -2; // Table not found
    }
    
    // Validate record data
    for (int i = 0; i < table->column_count; i++) {
        Column* column = &table->columns[i];
        void* field_value = record->fields[i];
        
        // Check NOT NULL constraint
        if (column->is_not_null && !field_value) {
            return -3; // NULL value in NOT NULL column
        }
        
        // Check data type
        if (!validateDataType(column->type, field_value)) {
            return -4; // Invalid data type
        }
        
        // Check UNIQUE constraint
        if (column->is_unique && !isUniqueValue(table, i, field_value)) {
            return -5; // Duplicate value
        }
    }
    
    return 0; // Success
}

// Bad: No validation
int insertRecord(Database* db, const char* table_name, Record* record) {
    Table* table = findTable(db, table_name);
    // Insert without validation
    return 0;
}
```

## ⚠️ Common Pitfalls

### 1. Memory Leaks
```c
// Wrong: Memory leak in record creation
Record* createRecord(Table* table) {
    Record* record = malloc(sizeof(Record));
    for (int i = 0; i < table->column_count; i++) {
        record->fields[i] = malloc(getFieldSize(&table->columns[i]));
        // If allocation fails here, previous allocations are leaked
    }
    return record;
}

// Right: Proper cleanup on failure
Record* createRecord(Table* table) {
    Record* record = malloc(sizeof(Record));
    if (!record) return NULL;
    
    for (int i = 0; i < table->column_count; i++) {
        record->fields[i] = malloc(getFieldSize(&table->columns[i]));
        if (!record->fields[i]) {
            // Clean up previous allocations
            for (int j = 0; j < i; j++) {
                free(record->fields[j]);
            }
            free(record);
            return NULL;
        }
    }
    return record;
}
```

### 2. Race Conditions
```c
// Wrong: No synchronization
int incrementCounter(Database* db) {
    db->record_count++; // Race condition with multiple threads
    return db->record_count;
}

// Right: Thread-safe operation
int incrementCounter(Database* db) {
    pthread_mutex_lock(&db->mutex);
    db->record_count++;
    int result = db->record_count;
    pthread_mutex_unlock(&db->mutex);
    return result;
}
```

### 3. Buffer Overflows
```c
// Wrong: No bounds checking
void setTableName(Table* table, const char* name) {
    strcpy(table->name, name); // Can overflow if name is too long
}

// Right: Safe string operations
void setTableName(Table* table, const char* name) {
    strncpy(table->name, name, sizeof(table->name) - 1);
    table->name[sizeof(table->name) - 1] = '\0';
}
```

### 4. File Handle Leaks
```c
// Wrong: File handle leak
int saveDatabase(Database* db) {
    FILE* file = fopen(db->filename, "wb");
    fwrite(db, sizeof(Database), 1, file);
    // Forgot to close(file)
    return 0;
}

// Right: Proper file handle management
int saveDatabase(Database* db) {
    FILE* file = fopen(db->filename, "wb");
    if (!file) return -1;
    
    int result = fwrite(db, sizeof(Database), 1, file);
    fclose(file);
    return result == 1 ? 0 : -1;
}
```

## 🔧 Real-World Applications

### 1. Embedded Database
```c
// Embedded database for IoT devices
typedef struct {
    Database* db;
    char* sensor_data;
    int data_count;
    int max_records;
} EmbeddedDatabase;

int logSensorData(EmbeddedDatabase* embedded_db, const char* sensor_id, 
                  float value, time_t timestamp) {
    // Create record with sensor data
    Record* record = createRecord(&embedded_db->db->tables[0]);
    if (!record) return -1;
    
    // Set sensor data
    setFieldValue(record, &embedded_db->db->tables[0], "sensor_id", sensor_id);
    setFieldValue(record, &embedded_db->db->tables[0], "value", &value);
    setFieldValue(record, &embedded_db->db->tables[0], "timestamp", &timestamp);
    
    // Insert record
    int result = executeInsertQuery(embedded_db->db, &query, record);
    
    // Clean up old records if needed
    if (embedded_db->data_count >= embedded_db->max_records) {
        deleteOldestRecords(embedded_db->db, 100);
    }
    
    return result;
}
```

### 2. Web Application Backend
```c
// Web application database layer
typedef struct {
    Database* db;
    ConnectionPool* pool;
    int max_connections;
} WebDatabase;

int executeWebQuery(WebDatabase* web_db, const char* sql, ResultSet* result) {
    // Get connection from pool
    DatabaseConnection* conn = getConnection(web_db->pool, "webapp");
    if (!conn) return -1;
    
    // Begin transaction
    int transaction_id = beginTransaction(web_db->db, conn->connection_id);
    
    // Execute query
    int query_result = executeQuery(web_db->db, sql, result);
    
    // Commit or rollback
    if (query_result == 0) {
        commitTransaction(web_db->db, transaction_id);
    } else {
        rollbackTransaction(web_db->db, transaction_id);
    }
    
    // Return connection
    returnConnection(web_db->pool, conn);
    
    return query_result;
}
```

### 3. Data Analysis Engine
```c
// Data analysis database with indexing
typedef struct {
    Database* db;
    Index* time_index;
    Index* category_index;
    int analysis_cache_size;
} AnalysisDatabase;

int analyzeDataByTimeRange(AnalysisDatabase* analysis_db, 
                           time_t start_time, time_t end_time, 
                           ResultSet* result) {
    // Use time index for fast range queries
    int record_ids[MAX_RECORDS];
    int found_count = searchIndex(analysis_db->time_index, &start_time, record_ids, MAX_RECORDS);
    
    // Filter by end time
    int filtered_count = 0;
    for (int i = 0; i < found_count; i++) {
        Record* record = loadRecord(analysis_db->db, record_ids[i]);
        time_t record_time = *(time_t*)getFieldValue(record, &analysis_db->db->tables[0], "timestamp");
        
        if (record_time >= start_time && record_time <= end_time) {
            result->rows[filtered_count++] = record;
        }
    }
    
    result->row_count = filtered_count;
    return filtered_count;
}
```

## 📚 Further Reading

### Books
- "Database System Concepts" by Abraham Silberschatz
- "Transaction Processing: Concepts and Techniques" by Jim Gray
- "SQL for Smarties" by Joe Celko

### Topics
- Query optimization techniques
- Database indexing algorithms
- Concurrency control mechanisms
- Database recovery methods
- NoSQL database design

Advanced database programming in C provides the foundation for building high-performance, reliable, and scalable data storage systems. Master these techniques to create custom database engines, optimize data access patterns, and implement robust transaction management!
