/*
 * File: sqlite_basic.c
 * Description: Basic SQLite database operations
 */

#include <stdio.h>
#include <stdlib.h>
#include <sqlite3.h>

int main() {
    sqlite3 *db;
    char *err_msg = 0;
    int rc;
    
    // Open database
    rc = sqlite3_open("test.db", &db);
    
    if (rc != SQLITE_OK) {
        fprintf(stderr, "Cannot open database: %s\n", sqlite3_errmsg(db));
        sqlite3_close(db);
        return 1;
    }
    
    printf("Database opened successfully\n");
    
    // Create table
    const char *sql = "CREATE TABLE IF NOT EXISTS users("
                      "id INTEGER PRIMARY KEY AUTOINCREMENT,"
                      "name TEXT NOT NULL,"
                      "email TEXT UNIQUE,"
                      "age INTEGER);";
    
    rc = sqlite3_exec(db, sql, 0, 0, &err_msg);
    
    if (rc != SQLITE_OK ) {
        fprintf(stderr, "SQL error: %s\n", err_msg);
        sqlite3_free(err_msg);
    } else {
        printf("Table created successfully\n");
    }
    
    // Insert data
    sql = "INSERT INTO users (name, email, age) VALUES "
          "('John Doe', 'john@example.com', 25),"
          "('Jane Smith', 'jane@example.com', 30),"
          "('Bob Johnson', 'bob@example.com', 35);";
    
    rc = sqlite3_exec(db, sql, 0, 0, &err_msg);
    
    if (rc != SQLITE_OK ) {
        fprintf(stderr, "SQL error: %s\n", err_msg);
        sqlite3_free(err_msg);
    } else {
        printf("Records inserted successfully\n");
    }
    
    // Select data with callback
    sql = "SELECT * FROM users";
    
    printf("\nUsers in database:\n");
    printf("ID\tName\t\tEmail\t\t\tAge\n");
    printf("------------------------------------------------\n");
    
    // Callback function for SELECT
    int callback(void *data, int argc, char **argv, char **azColName) {
        for (int i = 0; i < argc; i++) {
            printf("%s\t", argv[i] ? argv[i] : "NULL");
        }
        printf("\n");
        return 0;
    }
    
    rc = sqlite3_exec(db, sql, callback, 0, &err_msg);
    
    if (rc != SQLITE_OK ) {
        fprintf(stderr, "SQL error: %s\n", err_msg);
        sqlite3_free(err_msg);
    }
    
    // Update data
    sql = "UPDATE users SET age = 26 WHERE name = 'John Doe'";
    rc = sqlite3_exec(db, sql, 0, 0, &err_msg);
    
    if (rc != SQLITE_OK ) {
        fprintf(stderr, "SQL error: %s\n", err_msg);
        sqlite3_free(err_msg);
    } else {
        printf("\nRecord updated successfully\n");
    }
    
    // Delete data
    sql = "DELETE FROM users WHERE age > 30";
    rc = sqlite3_exec(db, sql, 0, 0, &err_msg);
    
    if (rc != SQLITE_OK ) {
        fprintf(stderr, "SQL error: %s\n", err_msg);
        sqlite3_free(err_msg);
    } else {
        printf("Records deleted successfully\n");
    }
    
    // Final select
    printf("\nFinal users in database:\n");
    printf("ID\tName\t\tEmail\t\t\tAge\n");
    printf("------------------------------------------------\n");
    
    rc = sqlite3_exec(db, sql, callback, 0, &err_msg);
    
    if (rc != SQLITE_OK ) {
        fprintf(stderr, "SQL error: %s\n", err_msg);
        sqlite3_free(err_msg);
    }
    
    // Close database
    sqlite3_close(db);
    
    return 0;
}
