/*
 * File: mysql_connection.c
 * Description: MySQL database connection and operations
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <mysql/mysql.h>

// Database configuration
#define DB_HOST "localhost"
#define DB_USER "root"
#define DB_PASSWORD "password"
#define DB_NAME "testdb"

// Error handling macro
#define CHECK_ERROR(stmt, message) \
    if (stmt) { \
        fprintf(stderr, "Error: %s\n", message); \
        fprintf(stderr, "MySQL Error: %s\n", mysql_error(conn)); \
        mysql_close(conn); \
        exit(EXIT_FAILURE); \
    }

// Connection structure
typedef struct {
    MYSQL* connection;
    int connected;
} DatabaseConnection;

// Initialize database connection
DatabaseConnection* db_init() {
    DatabaseConnection* db = (DatabaseConnection*)malloc(sizeof(DatabaseConnection));
    db->connection = mysql_init(NULL);
    db->connected = 0;
    
    if (db->connection == NULL) {
        fprintf(stderr, "MySQL initialization failed\n");
        free(db);
        return NULL;
    }
    
    return db;
}

// Connect to database
int db_connect(DatabaseConnection* db) {
    if (mysql_real_connect(db->connection, DB_HOST, DB_USER, DB_PASSWORD, 
                          DB_NAME, 3306, NULL, 0) == NULL) {
        fprintf(stderr, "Connection failed: %s\n", mysql_error(db->connection));
        return 0;
    }
    
    db->connected = 1;
    printf("Connected to MySQL database: %s\n", DB_NAME);
    return 1;
}

// Disconnect from database
void db_disconnect(DatabaseConnection* db) {
    if (db->connected) {
        mysql_close(db->connection);
        db->connected = 0;
        printf("Disconnected from database\n");
    }
}

// Execute SQL query
int db_execute_query(DatabaseConnection* db, const char* query) {
    if (!db->connected) {
        fprintf(stderr, "Not connected to database\n");
        return -1;
    }
    
    if (mysql_query(db->connection, query) != 0) {
        fprintf(stderr, "Query failed: %s\n", mysql_error(db->connection));
        return -1;
    }
    
    return 0;
}

// Execute SELECT query and get result
MYSQL_RES* db_execute_select(DatabaseConnection* db, const char* query) {
    if (!db->connected) {
        fprintf(stderr, "Not connected to database\n");
        return NULL;
    }
    
    if (mysql_query(db->connection, query) != 0) {
        fprintf(stderr, "Query failed: %s\n", mysql_error(db->connection));
        return NULL;
    }
    
    return mysql_store_result(db->connection);
}

// Create users table
int create_users_table(DatabaseConnection* db) {
    const char* query = "CREATE TABLE IF NOT EXISTS users ("
                       "id INT AUTO_INCREMENT PRIMARY KEY,"
                       "username VARCHAR(50) UNIQUE NOT NULL,"
                       "email VARCHAR(100) UNIQUE NOT NULL,"
                       "password VARCHAR(255) NOT NULL,"
                       "age INT,"
                       "created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,"
                       "updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
                       ")";
    
    return db_execute_query(db, query);
}

// Insert user
int insert_user(DatabaseConnection* db, const char* username, const char* email, 
                const char* password, int age) {
    char query[512];
    snprintf(query, sizeof(query), 
             "INSERT INTO users (username, email, password, age) VALUES ('%s', '%s', '%s', %d)",
             username, email, password, age);
    
    return db_execute_query(db, query);
}

// Update user
int update_user(DatabaseConnection* db, int user_id, const char* username, 
                const char* email, int age) {
    char query[512];
    snprintf(query, sizeof(query),
             "UPDATE users SET username = '%s', email = '%s', age = %d WHERE id = %d",
             username, email, age, user_id);
    
    return db_execute_query(db, query);
}

// Delete user
int delete_user(DatabaseConnection* db, int user_id) {
    char query[256];
    snprintf(query, sizeof(query), "DELETE FROM users WHERE id = %d", user_id);
    
    return db_execute_query(db, query);
}

// Select all users
void select_all_users(DatabaseConnection* db) {
    const char* query = "SELECT id, username, email, age, created_at FROM users ORDER BY id";
    MYSQL_RES* result = db_execute_select(db, query);
    
    if (result == NULL) return;
    
    MYSQL_ROW row;
    MYSQL_FIELD* fields = mysql_fetch_fields(result);
    int num_fields = mysql_num_fields(result);
    
    // Print header
    printf("\nAll Users:\n");
    printf("ID\tUsername\tEmail\t\t\tAge\tCreated At\n");
    printf("------------------------------------------------------------\n");
    
    // Print data
    while ((row = mysql_fetch_row(result)) != NULL) {
        printf("%s\t%-10s\t%-20s\t%s\t%s\n", 
               row[0], row[1], row[2], row[3] ? row[3] : "NULL", row[4]);
    }
    
    printf("\n");
    mysql_free_result(result);
}

// Select user by ID
void select_user_by_id(DatabaseConnection* db, int user_id) {
    char query[256];
    snprintf(query, sizeof(query), 
             "SELECT id, username, email, age, created_at FROM users WHERE id = %d", user_id);
    
    MYSQL_RES* result = db_execute_select(db, query);
    
    if (result == NULL) return;
    
    MYSQL_ROW row = mysql_fetch_row(result);
    if (row) {
        printf("User Details:\n");
        printf("ID: %s\n", row[0]);
        printf("Username: %s\n", row[1]);
        printf("Email: %s\n", row[2]);
        printf("Age: %s\n", row[3] ? row[3] : "NULL");
        printf("Created At: %s\n", row[4]);
    } else {
        printf("User with ID %d not found\n", user_id);
    }
    
    mysql_free_result(result);
}

// Transaction example
void transaction_example(DatabaseConnection* db) {
    printf("\n=== Transaction Example ===\n");
    
    // Start transaction
    if (mysql_query(db->connection, "START TRANSACTION") != 0) {
        fprintf(stderr, "Failed to start transaction: %s\n", mysql_error(db->connection));
        return;
    }
    
    printf("Transaction started\n");
    
    // Insert multiple records
    if (insert_user(db, "user1", "user1@example.com", "pass1", 25) != 0) {
        printf("Failed to insert user1, rolling back\n");
        mysql_query(db->connection, "ROLLBACK");
        return;
    }
    
    if (insert_user(db, "user2", "user2@example.com", "pass2", 30) != 0) {
        printf("Failed to insert user2, rolling back\n");
        mysql_query(db->connection, "ROLLBACK");
        return;
    }
    
    // Commit transaction
    if (mysql_query(db->connection, "COMMIT") != 0) {
        fprintf(stderr, "Failed to commit transaction: %s\n", mysql_error(db->connection));
        mysql_query(db->connection, "ROLLBACK");
        return;
    }
    
    printf("Transaction committed successfully\n");
    select_all_users(db);
}

// Prepared statement example
void prepared_statement_example(DatabaseConnection* db) {
    printf("\n=== Prepared Statement Example ===\n");
    
    MYSQL_STMT* stmt;
    MYSQL_BIND bind[3];
    
    // Prepare statement
    const char* query = "INSERT INTO users (username, email, age) VALUES (?, ?, ?)";
    stmt = mysql_stmt_init(db->connection);
    
    if (!stmt) {
        fprintf(stderr, "mysql_stmt_init failed\n");
        return;
    }
    
    if (mysql_stmt_prepare(stmt, query, strlen(query)) != 0) {
        fprintf(stderr, "mysql_stmt_prepare failed: %s\n", mysql_stmt_error(stmt));
        mysql_stmt_close(stmt);
        return;
    }
    
    // Bind parameters
    char username[50];
    char email[100];
    int age;
    
    memset(bind, 0, sizeof(bind));
    
    bind[0].buffer_type = MYSQL_TYPE_STRING;
    bind[0].buffer = username;
    bind[0].length = &username[0];
    bind[0].is_unsigned = 0;
    
    bind[1].buffer_type = MYSQL_TYPE_STRING;
    bind[1].buffer = email;
    bind[1].length = &email[0];
    bind[1].is_unsigned = 0;
    
    bind[2].buffer_type = MYSQL_TYPE_LONG;
    bind[2].buffer = &age;
    bind[2].is_unsigned = 0;
    
    if (mysql_stmt_bind_param(stmt, bind) != 0) {
        fprintf(stderr, "mysql_stmt_bind_param failed: %s\n", mysql_stmt_error(stmt));
        mysql_stmt_close(stmt);
        return;
    }
    
    // Execute multiple times
    const char* usernames[] = {"alice", "bob", "charlie"};
    const char* emails[] = {"alice@example.com", "bob@example.com", "charlie@example.com"};
    int ages[] = {28, 32, 24};
    
    for (int i = 0; i < 3; i++) {
        strcpy(username, usernames[i]);
        strcpy(email, emails[i]);
        age = ages[i];
        
        if (mysql_stmt_execute(stmt) != 0) {
            fprintf(stderr, "mysql_stmt_execute failed: %s\n", mysql_stmt_error(stmt));
        } else {
            printf("Inserted user: %s\n", username);
        }
    }
    
    mysql_stmt_close(stmt);
    select_all_users(db);
}

// Get database information
void get_database_info(DatabaseConnection* db) {
    printf("\n=== Database Information ===\n");
    
    // Get server version
    printf("MySQL Server Version: %s\n", mysql_get_server_info(db->connection));
    printf("Client Version: %s\n", mysql_get_client_info());
    
    // Get current database
    printf("Current Database: %s\n", mysql_get_server_info(db->connection));
    
    // Get connection info
    printf("Host Info: %s\n", mysql_get_host_info(db->connection));
    
    // Get thread ID
    printf("Thread ID: %lu\n", mysql_thread_id(db->connection));
    
    // Get character set
    printf("Character Set: %s\n", mysql_character_set_name(db->connection));
}

// Test function
void test_mysql_operations() {
    DatabaseConnection* db = db_init();
    
    if (!db) {
        printf("Failed to initialize database connection\n");
        return;
    }
    
    // Connect to database
    if (!db_connect(db)) {
        free(db);
        return;
    }
    
    // Create table
    printf("\n=== Creating Table ===\n");
    if (create_users_table(db) == 0) {
        printf("Users table created successfully\n");
    } else {
        printf("Failed to create users table\n");
    }
    
    // Insert sample data
    printf("\n=== Inserting Sample Data ===\n");
    insert_user(db, "john_doe", "john@example.com", "password123", 25);
    insert_user(db, "jane_smith", "jane@example.com", "password456", 30);
    insert_user(db, "bob_jones", "bob@example.com", "password789", 35);
    
    // Select all users
    select_all_users(db);
    
    // Select specific user
    printf("\n=== Selecting User by ID ===\n");
    select_user_by_id(db, 1);
    
    // Update user
    printf("\n=== Updating User ===\n");
    update_user(db, 1, "john_updated", "john_updated@example.com", 26);
    select_user_by_id(db, 1);
    
    // Transaction example
    transaction_example(db);
    
    // Prepared statement example
    prepared_statement_example(db);
    
    // Get database information
    get_database_info(db);
    
    // Delete user
    printf("\n=== Deleting User ===\n");
    delete_user(db, 1);
    select_all_users(db);
    
    // Disconnect
    db_disconnect(db);
    free(db);
}

int main() {
    printf("=== MySQL Database Operations ===\n");
    printf("Note: Make sure MySQL server is running and database '%s' exists\n", DB_NAME);
    printf("Update DB_HOST, DB_USER, DB_PASSWORD, DB_NAME constants as needed\n\n");
    
    test_mysql_operations();
    
    printf("\n=== MySQL operations completed ===\n");
    
    return 0;
}
