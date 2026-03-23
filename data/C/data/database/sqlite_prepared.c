/*
 * File: sqlite_prepared.c
 * Description: SQLite prepared statements example
 */

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sqlite3.h>

typedef struct {
    int id;
    char name[50];
    char email[100];
    int age;
} User;

void insert_user(sqlite3 *db, const char *name, const char *email, int age) {
    sqlite3_stmt *stmt;
    const char *sql = "INSERT INTO users (name, email, age) VALUES (?, ?, ?)";
    
    int rc = sqlite3_prepare_v2(db, sql, -1, &stmt, NULL);
    if (rc != SQLITE_OK) {
        fprintf(stderr, "Prepare failed: %s\n", sqlite3_errmsg(db));
        return;
    }
    
    // Bind parameters
    sqlite3_bind_text(stmt, 1, name, -1, SQLITE_STATIC);
    sqlite3_bind_text(stmt, 2, email, -1, SQLITE_STATIC);
    sqlite3_bind_int(stmt, 3, age);
    
    // Execute
    rc = sqlite3_step(stmt);
    if (rc != SQLITE_DONE) {
        fprintf(stderr, "Execution failed: %s\n", sqlite3_errmsg(db));
    } else {
        printf("Inserted user: %s\n", name);
    }
    
    sqlite3_finalize(stmt);
}

User* get_user_by_id(sqlite3 *db, int id) {
    sqlite3_stmt *stmt;
    const char *sql = "SELECT id, name, email, age FROM users WHERE id = ?";
    
    int rc = sqlite3_prepare_v2(db, sql, -1, &stmt, NULL);
    if (rc != SQLITE_OK) {
        fprintf(stderr, "Prepare failed: %s\n", sqlite3_errmsg(db));
        return NULL;
    }
    
    // Bind parameter
    sqlite3_bind_int(stmt, 1, id);
    
    // Execute
    rc = sqlite3_step(stmt);
    if (rc == SQLITE_ROW) {
        User *user = malloc(sizeof(User));
        user->id = sqlite3_column_int(stmt, 0);
        strncpy(user->name, (char*)sqlite3_column_text(stmt, 1), sizeof(user->name) - 1);
        strncpy(user->email, (char*)sqlite3_column_text(stmt, 2), sizeof(user->email) - 1);
        user->age = sqlite3_column_int(stmt, 3);
        
        sqlite3_finalize(stmt);
        return user;
    }
    
    sqlite3_finalize(stmt);
    return NULL;
}

void update_user_age(sqlite3 *db, int id, int new_age) {
    sqlite3_stmt *stmt;
    const char *sql = "UPDATE users SET age = ? WHERE id = ?";
    
    int rc = sqlite3_prepare_v2(db, sql, -1, &stmt, NULL);
    if (rc != SQLITE_OK) {
        fprintf(stderr, "Prepare failed: %s\n", sqlite3_errmsg(db));
        return;
    }
    
    // Bind parameters
    sqlite3_bind_int(stmt, 1, new_age);
    sqlite3_bind_int(stmt, 2, id);
    
    // Execute
    rc = sqlite3_step(stmt);
    if (rc != SQLITE_DONE) {
        fprintf(stderr, "Execution failed: %s\n", sqlite3_errmsg(db));
    } else {
        printf("Updated user %d age to %d\n", id, new_age);
    }
    
    sqlite3_finalize(stmt);
}

void delete_user(sqlite3 *db, int id) {
    sqlite3_stmt *stmt;
    const char *sql = "DELETE FROM users WHERE id = ?";
    
    int rc = sqlite3_prepare_v2(db, sql, -1, &stmt, NULL);
    if (rc != SQLITE_OK) {
        fprintf(stderr, "Prepare failed: %s\n", sqlite3_errmsg(db));
        return;
    }
    
    // Bind parameter
    sqlite3_bind_int(stmt, 1, id);
    
    // Execute
    rc = sqlite3_step(stmt);
    if (rc != SQLITE_DONE) {
        fprintf(stderr, "Execution failed: %s\n", sqlite3_errmsg(db));
    } else {
        printf("Deleted user %d\n", id);
    }
    
    sqlite3_finalize(stmt);
}

void list_all_users(sqlite3 *db) {
    sqlite3_stmt *stmt;
    const char *sql = "SELECT id, name, email, age FROM users ORDER BY name";
    
    int rc = sqlite3_prepare_v2(db, sql, -1, &stmt, NULL);
    if (rc != SQLITE_OK) {
        fprintf(stderr, "Prepare failed: %s\n", sqlite3_errmsg(db));
        return;
    }
    
    printf("\nAll Users:\n");
    printf("ID\tName\t\tEmail\t\t\tAge\n");
    printf("------------------------------------------------\n");
    
    while ((rc = sqlite3_step(stmt)) == SQLITE_ROW) {
        int id = sqlite3_column_int(stmt, 0);
        const char *name = (char*)sqlite3_column_text(stmt, 1);
        const char *email = (char*)sqlite3_column_text(stmt, 2);
        int age = sqlite3_column_int(stmt, 3);
        
        printf("%d\t%s\t\t%s\t\t%d\n", id, name, email, age);
    }
    
    sqlite3_finalize(stmt);
}

int main() {
    sqlite3 *db;
    
    // Open database
    int rc = sqlite3_open("test.db", &db);
    if (rc != SQLITE_OK) {
        fprintf(stderr, "Cannot open database: %s\n", sqlite3_errmsg(db));
        sqlite3_close(db);
        return 1;
    }
    
    // Create table
    const char *sql = "CREATE TABLE IF NOT EXISTS users("
                      "id INTEGER PRIMARY KEY AUTOINCREMENT,"
                      "name TEXT NOT NULL,"
                      "email TEXT UNIQUE,"
                      "age INTEGER);";
    
    rc = sqlite3_exec(db, sql, 0, 0, NULL);
    if (rc != SQLITE_OK) {
        fprintf(stderr, "Table creation failed\n");
        sqlite3_close(db);
        return 1;
    }
    
    // Insert users using prepared statements
    insert_user(db, "Alice Johnson", "alice@example.com", 28);
    insert_user(db, "Bob Smith", "bob@example.com", 32);
    insert_user(db, "Charlie Brown", "charlie@example.com", 24);
    
    // List all users
    list_all_users(db);
    
    // Get user by ID
    User *user = get_user_by_id(db, 2);
    if (user) {
        printf("\nFound user: %s, %s, age %d\n", user->name, user->email, user->age);
        free(user);
    }
    
    // Update user age
    update_user_age(db, 1, 29);
    
    // Delete a user
    delete_user(db, 3);
    
    // Final list
    list_all_users(db);
    
    // Close database
    sqlite3_close(db);
    
    return 0;
}
