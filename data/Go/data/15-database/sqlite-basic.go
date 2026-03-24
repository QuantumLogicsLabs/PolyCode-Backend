package main

import (
	"database/sql"
	"fmt"
	"log"
	"os"
	"path/filepath"

	_ "github.com/mattn/go-sqlite3"
)

type User struct {
	ID        int    `json:"id"`
	Name      string `json:"name"`
	Email     string `json:"email"`
	Age       int    `json:"age"`
	CreatedAt string `json:"created_at"`
}

func main() {
	fmt.Println("=== SQLite Database Operations ===")

	// Create database file
	dbPath := "users.db"
	defer cleanupDatabase(dbPath)

	// Open database connection
	db, err := sql.Open("sqlite3", dbPath)
	if err != nil {
		log.Fatal("Failed to open database:", err)
	}
	defer db.Close()

	// Create table
	if err := createUsersTable(db); err != nil {
		log.Fatal("Failed to create table:", err)
	}

	// Insert sample data
	if err := insertSampleUsers(db); err != nil {
		log.Fatal("Failed to insert data:", err)
	}

	// Query operations
	fmt.Println("\n--- All Users ---")
	if err := queryAllUsers(db); err != nil {
		log.Fatal("Failed to query users:", err)
	}

	fmt.Println("\n--- Users by Age ---")
	if err := queryUsersByAge(db, 25); err != nil {
		log.Fatal("Failed to query by age:", err)
	}

	fmt.Println("\n--- Single User ---")
	if err := querySingleUser(db, 1); err != nil {
		log.Fatal("Failed to query single user:", err)
	}

	// Update operation
	fmt.Println("\n--- Update User ---")
	if err := updateUser(db, 1, "John Updated"); err != nil {
		log.Fatal("Failed to update user:", err)
	}

	// Delete operation
	fmt.Println("\n--- Delete User ---")
	if err := deleteUser(db, 3); err != nil {
		log.Fatal("Failed to delete user:", err)
	}

	fmt.Println("\n--- Final Users ---")
	if err := queryAllUsers(db); err != nil {
		log.Fatal("Failed to query final users:", err)
	}

	// Transaction example
	fmt.Println("\n--- Transaction Example ---")
	if err := demonstrateTransaction(db); err != nil {
		log.Fatal("Failed transaction demo:", err)
	}
}

func createUsersTable(db *sql.DB) error {
	query := `
	CREATE TABLE IF NOT EXISTS users (
		id INTEGER PRIMARY KEY AUTOINCREMENT,
		name TEXT NOT NULL,
		email TEXT UNIQUE NOT NULL,
		age INTEGER,
		created_at DATETIME DEFAULT CURRENT_TIMESTAMP
	)`

	_, err := db.Exec(query)
	if err != nil {
		return fmt.Errorf("failed to create table: %w", err)
	}

	fmt.Println("✓ Users table created successfully")
	return nil
}

func insertSampleUsers(db *sql.DB) error {
	users := []User{
		{Name: "John Doe", Email: "john@example.com", Age: 30},
		{Name: "Jane Smith", Email: "jane@example.com", Age: 25},
		{Name: "Bob Johnson", Email: "bob@example.com", Age: 35},
		{Name: "Alice Brown", Email: "alice@example.com", Age: 28},
	}

	for _, user := range users {
		query := `INSERT INTO users (name, email, age) VALUES (?, ?, ?)`
		result, err := db.Exec(query, user.Name, user.Email, user.Age)
		if err != nil {
			return fmt.Errorf("failed to insert user %s: %w", user.Name, err)
		}

		id, err := result.LastInsertId()
		if err != nil {
			return fmt.Errorf("failed to get last insert ID: %w", err)
		}

		fmt.Printf("✓ Inserted user: %s (ID: %d)\n", user.Name, id)
	}

	return nil
}

func queryAllUsers(db *sql.DB) error {
	query := `SELECT id, name, email, age, created_at FROM users ORDER BY name`
	rows, err := db.Query(query)
	if err != nil {
		return fmt.Errorf("failed to query users: %w", err)
	}
	defer rows.Close()

	var users []User
	for rows.Next() {
		var user User
		err := rows.Scan(&user.ID, &user.Name, &user.Email, &user.Age, &user.CreatedAt)
		if err != nil {
			return fmt.Errorf("failed to scan user: %w", err)
		}
		users = append(users, user)
	}

	if err = rows.Err(); err != nil {
		return fmt.Errorf("row iteration error: %w", err)
	}

	for _, user := range users {
		fmt.Printf("ID: %d, Name: %s, Email: %s, Age: %d, Created: %s\n",
			user.ID, user.Name, user.Email, user.Age, user.CreatedAt)
	}

	return nil
}

func queryUsersByAge(db *sql.DB, minAge int) error {
	query := `SELECT id, name, email, age FROM users WHERE age >= ? ORDER BY age`
	rows, err := db.Query(query, minAge)
	if err != nil {
		return fmt.Errorf("failed to query by age: %w", err)
	}
	defer rows.Close()

	fmt.Printf("Users aged %d and above:\n", minAge)
	for rows.Next() {
		var id, age int
		var name, email string
		err := rows.Scan(&id, &name, &email, &age)
		if err != nil {
			return fmt.Errorf("failed to scan user: %w", err)
		}
		fmt.Printf("  - %s (%d years old)\n", name, age)
	}

	return nil
}

func querySingleUser(db *sql.DB, id int) error {
	query := `SELECT id, name, email, age FROM users WHERE id = ?`
	var user User
	err := db.QueryRow(query, id).Scan(&user.ID, &user.Name, &user.Email, &user.Age)
	if err != nil {
		if err == sql.ErrNoRows {
			return fmt.Errorf("user with ID %d not found", id)
		}
		return fmt.Errorf("failed to query user: %w", err)
	}

	fmt.Printf("Found user: %+v\n", user)
	return nil
}

func updateUser(db *sql.DB, id int, newName string) error {
	query := `UPDATE users SET name = ? WHERE id = ?`
	result, err := db.Exec(query, newName, id)
	if err != nil {
		return fmt.Errorf("failed to update user: %w", err)
	}

	rowsAffected, err := result.RowsAffected()
	if err != nil {
		return fmt.Errorf("failed to get rows affected: %w", err)
	}

	if rowsAffected == 0 {
		return fmt.Errorf("no user found with ID %d", id)
	}

	fmt.Printf("✓ Updated user ID %d name to '%s'\n", id, newName)
	return nil
}

func deleteUser(db *sql.DB, id int) error {
	query := `DELETE FROM users WHERE id = ?`
	result, err := db.Exec(query, id)
	if err != nil {
		return fmt.Errorf("failed to delete user: %w", err)
	}

	rowsAffected, err := result.RowsAffected()
	if err != nil {
		return fmt.Errorf("failed to get rows affected: %w", err)
	}

	if rowsAffected == 0 {
		return fmt.Errorf("no user found with ID %d", id)
	}

	fmt.Printf("✓ Deleted user ID %d\n", id)
	return nil
}

func demonstrateTransaction(db *sql.DB) error {
	tx, err := db.Begin()
	if err != nil {
		return fmt.Errorf("failed to begin transaction: %w", err)
	}

	// Defer rollback in case of failure
	defer func() {
		if err != nil {
			tx.Rollback()
		}
	}()

	// Insert multiple users in transaction
	users := []struct {
		name  string
		email string
		age   int
	}{
		{"Transaction User 1", "tx1@example.com", 40},
		{"Transaction User 2", "tx2@example.com", 45},
	}

	for _, user := range users {
		query := `INSERT INTO users (name, email, age) VALUES (?, ?, ?)`
		_, err := tx.Exec(query, user.name, user.email, user.age)
		if err != nil {
			return fmt.Errorf("failed to insert in transaction: %w", err)
		}
	}

	// Commit the transaction
	if err = tx.Commit(); err != nil {
		return fmt.Errorf("failed to commit transaction: %w", err)
	}

	fmt.Println("✓ Transaction completed successfully")
	
	// Show the new users
	return queryAllUsers(db)
}

func cleanupDatabase(dbPath string) {
	if err := os.Remove(dbPath); err != nil && !os.IsNotExist(err) {
		log.Printf("Warning: failed to remove database file: %v", err)
	}
}
