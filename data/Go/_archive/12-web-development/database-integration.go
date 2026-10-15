package main

import (
	"database/sql"
	"fmt"
	"log"
	"strings"
	"time"
)

func main() {
	fmt.Println("=== Database Integration in Go ===")

	// Database drivers
	fmt.Println("\n--- Database Drivers ---")
	databaseDrivers()

	// Connection management
	fmt.Println("\n--- Connection Management ---")
	connectionManagement()

	// Basic CRUD operations
	fmt.Println("\n--- Basic CRUD Operations ---")
	basicCRUD()

	// Transactions
	fmt.Println("\n--- Database Transactions ---")
	databaseTransactions()

	// Prepared statements
	fmt.Println("\n--- Prepared Statements ---")
	preparedStatements()

	// Connection pooling
	fmt.Println("\n--- Connection Pooling ---")
	connectionPooling()

	// Database migrations
	fmt.Println("\n--- Database Migrations ---")
	databaseMigrations()

	// ORM integration
	fmt.Println("\n--- ORM Integration ---")
	ormIntegration()

	// Query optimization
	fmt.Println("\n--- Query Optimization ---")
	queryOptimization()

	// Error handling
	fmt.Println("\n--- Database Error Handling ---")
	databaseErrorHandling()
}

// Database drivers
func databaseDrivers() {
	fmt.Println("Popular Database Drivers for Go:")

	drivers := map[string]string{
		"PostgreSQL":  "github.com/lib/pq",
		"MySQL":       "github.com/go-sql-driver/mysql",
		"SQLite":      "github.com/mattn/go-sqlite3",
		"SQL Server":  "github.com/denisenkom/go-mssqldb",
		"Oracle":      "github.com/sijms/go-ora",
		"CockroachDB": "github.com/lib/pq",
		"TiDB":        "github.com/go-sql-driver/mysql",
	}

	for db, driver := range drivers {
		fmt.Printf("  %s: %s\n", db, driver)
	}

	fmt.Println("\nDriver Installation:")
	fmt.Println("  go get github.com/lib/pq")
	fmt.Println("  go get github.com/go-sql-driver/mysql")
	fmt.Println("  go get github.com/mattn/go-sqlite3")
}

// Connection management
func connectionManagement() {
	fmt.Println("Database Connection Examples:")

	// PostgreSQL connection
	connectPostgreSQL := func() (*sql.DB, error) {
		connStr := "host=localhost port=5432 user=postgres dbname=myapp sslmode=disable"
		db, err := sql.Open("postgres", connStr)
		if err != nil {
			return nil, err
		}
		return db, nil
	}

	// MySQL connection
	connectMySQL := func() (*sql.DB, error) {
		connStr := "user:password@tcp(localhost:3306)/dbname"
		db, err := sql.Open("mysql", connStr)
		if err != nil {
			return nil, err
		}
		return db, nil
	}

	// SQLite connection
	connectSQLite := func() (*sql.DB, error) {
		db, err := sql.Open("sqlite3", "./test.db")
		if err != nil {
			return nil, err
		}
		return db, nil
	}

	fmt.Println("  - PostgreSQL connection")
	fmt.Println("  - MySQL connection")
	fmt.Println("  - SQLite connection")

	// Connection configuration
	configureConnection := func(db *sql.DB) {
		db.SetMaxOpenConns(25)
		db.SetMaxIdleConns(25)
		db.SetConnMaxLifetime(5 * time.Minute)
		db.SetConnMaxIdleTime(5 * time.Minute)
	}

	fmt.Println("  - Connection pool configuration")
	_ = configureConnection

	fmt.Println("Connection setup complete (not actually connecting to avoid database dependency)")
	_, _ = connectPostgreSQL()
	_, _ = connectMySQL()
	_, _ = connectSQLite()
}

// Basic CRUD operations
func basicCRUD() {
	fmt.Println("CRUD Operations Examples:")

	type User struct {
		ID        int       `json:"id"`
		Name      string    `json:"name"`
		Email     string    `json:"email"`
		CreatedAt time.Time `json:"created_at"`
		UpdatedAt time.Time `json:"updated_at"`
	}

	createUser := func(db *sql.DB, user *User) error {
		query := `INSERT INTO users (name, email, created_at, updated_at) VALUES ($1, $2, $3, $4)`
		now := time.Now()
		_, err := db.Exec(query, user.Name, user.Email, now, now)
		return err
	}

	getUser := func(db *sql.DB, id int) (*User, error) {
		query := `SELECT id, name, email, created_at, updated_at FROM users WHERE id = $1`
		var user User
		err := db.QueryRow(query, id).Scan(&user.ID, &user.Name, &user.Email, &user.CreatedAt, &user.UpdatedAt)
		if err != nil {
			return nil, err
		}
		return &user, nil
	}

	updateUser := func(db *sql.DB, user *User) error {
		query := `UPDATE users SET name = $1, email = $2, updated_at = $3 WHERE id = $4`
		now := time.Now()
		_, err := db.Exec(query, user.Name, user.Email, now, user.ID)
		return err
	}

	deleteUser := func(db *sql.DB, id int) error {
		query := `DELETE FROM users WHERE id = $1`
		_, err := db.Exec(query, id)
		return err
	}

	listUsers := func(db *sql.DB) ([]User, error) {
		query := `SELECT id, name, email, created_at, updated_at FROM users ORDER BY created_at DESC`
		rows, err := db.Query(query)
		if err != nil {
			return nil, err
		}
		defer rows.Close()

		var users []User
		for rows.Next() {
			var user User
			err := rows.Scan(&user.ID, &user.Name, &user.Email, &user.CreatedAt, &user.UpdatedAt)
			if err != nil {
				return nil, err
			}
			users = append(users, user)
		}
		return users, nil
	}

	fmt.Println("  - Create user")
	fmt.Println("  - Read user by ID")
	fmt.Println("  - Update user")
	fmt.Println("  - Delete user")
	fmt.Println("  - List all users")

	_ = createUser
	_ = getUser
	_ = updateUser
	_ = deleteUser
	_ = listUsers
}

// Database transactions
func databaseTransactions() {
	fmt.Println("Transaction Examples:")

	simpleTransaction := func(db *sql.DB) error {
		tx, err := db.Begin()
		if err != nil {
			return err
		}
		defer tx.Rollback()

		_, err = tx.Exec("INSERT INTO users (name, email) VALUES ($1, $2)", "Alice", "alice@example.com")
		if err != nil {
			return err
		}

		_, err = tx.Exec("INSERT INTO users (name, email) VALUES ($1, $2)", "Bob", "bob@example.com")
		if err != nil {
			return err
		}

		return tx.Commit()
	}

	fmt.Println("  - Simple transaction")
	fmt.Println("  - Transaction with rollback")
	fmt.Println("  - Nested transaction with savepoint")

	_ = simpleTransaction
}

// Prepared statements
func preparedStatements() {
	fmt.Println("Prepared Statement Examples:")

	preparedInsert := func(db *sql.DB) error {
		stmt, err := db.Prepare("INSERT INTO users (name, email) VALUES ($1, $2)")
		if err != nil {
			return err
		}
		defer stmt.Close()

		_, err = stmt.Exec("Frank", "frank@example.com")
		return err
	}

	fmt.Println("  - Prepared INSERT statement")
	fmt.Println("  - Prepared SELECT statement")
	fmt.Println("  - Prepared UPDATE statement")

	_ = preparedInsert
}

// Connection pooling
func connectionPooling() {
	fmt.Println("Connection Pooling Examples:")

	configurePool := func(db *sql.DB) {
		db.SetMaxOpenConns(25)
		db.SetMaxIdleConns(25)
		db.SetConnMaxLifetime(5 * time.Minute)
		db.SetConnMaxIdleTime(5 * time.Minute)
	}

	bestPractices := []string{
		"Set appropriate max open connections based on database capacity",
		"Set appropriate max idle connections to avoid connection churn",
		"Set reasonable connection lifetime to prevent stale connections",
		"Monitor pool statistics to optimize configuration",
		"Use prepared statements to improve performance",
		"Close connections when done to return them to pool",
		"Handle connection errors gracefully",
		"Use connection timeouts to prevent hanging",
	}

	fmt.Println("  - Connection pool configuration")
	fmt.Println("  - Pool monitoring")
	fmt.Println("  - Best practices")

	_ = configurePool
	for _, practice := range bestPractices {
		fmt.Printf("    - %s\n", practice)
	}
}

// Database migrations
func databaseMigrations() {
	fmt.Println("Database Migration Examples:")

	type Migration struct {
		Version     int
		Name        string
		UpSQL       string
		DownSQL     string
		Description string
	}

	migrations := []Migration{
		{
			Version:     1,
			Name:        "create_users_table",
			Description: "Create users table",
			UpSQL:       `CREATE TABLE users (id SERIAL PRIMARY KEY, name VARCHAR(100) NOT NULL, email VARCHAR(100) UNIQUE NOT NULL);`,
			DownSQL:     "DROP TABLE users;",
		},
	}

	fmt.Println("  - Migration structure")
	fmt.Println("  - Sample migrations")
	fmt.Println("  - Migration runner")
	fmt.Println("  - Migration rollback")

	for _, migration := range migrations {
		fmt.Printf("  - Migration %d: %s - %s\n", migration.Version, migration.Name, migration.Description)
	}
}

// ORM integration
func ormIntegration() {
	fmt.Println("ORM Integration Examples:")

	orms := map[string]string{
		"GORM":      "github.com/go-gorm/gorm",
		"Ent":       "entgo.io/ent",
		"SQLBoiler": "github.com/volatiletech/sql-boiler",
		"Pop":       "github.com/gobuffalo/pop",
		"Reform":    "github.com/go-reform/reform",
		"XORM":      "xorm.io/xorm",
	}

	for orm, url := range orms {
		fmt.Printf("  %s: %s\n", orm, url)
	}

	fmt.Println("\nORM Installation:")
	fmt.Println("  go get github.com/go-gorm/gorm")
	fmt.Println("  go get github.com/go-gorm/driver/postgres")

	gormModel := "type User struct {\n" +
		"    ID        uint      `gorm:\"primaryKey\"`\n" +
		"    Name      string    `gorm:\"size:100;not null\"`\n" +
		"    Email     string    `gorm:\"size:100;uniqueIndex;not null\"`\n" +
		"    Age       int       `gorm:\"default:0\"`\n" +
		"    CreatedAt time.Time `gorm:\"autoCreateTime\"`\n" +
		"    UpdatedAt time.Time `gorm:\"autoUpdateTime\"`\n" +
		"}"

	gormOperations := []string{
		"db.Create(&user) - Create user",
		"db.First(&user, id) - Find user by ID",
		"db.Find(&users) - Find all users",
		"db.Model(&user).Update(\"name\", \"John\") - Update user",
		"db.Delete(&user) - Delete user",
		"db.Where(\"name = ?\", \"John\").Find(&users) - Query with conditions",
		"db.Order(\"created_at DESC\").Find(&users) - Order results",
		"db.Limit(10).Offset(20).Find(&users) - Pagination",
	}

	fmt.Println("\nGORM Model:")
	fmt.Println(gormModel)

	fmt.Println("\nGORM Operations:")
	for _, op := range gormOperations {
		fmt.Printf("  %s\n", op)
	}
}

// Query optimization
func queryOptimization() {
	fmt.Println("Query Optimization Examples:")

	indexingStrategies := []string{
		"Create indexes on frequently queried columns",
		"Create composite indexes for multi-column queries",
		"Create partial indexes for filtered queries",
		"Create unique indexes for uniqueness constraints",
		"Create covering indexes to avoid table scans",
		"Monitor index usage and remove unused indexes",
	}

	optimizationTechniques := []string{
		"Use prepared statements for repeated queries",
		"Use appropriate data types to reduce storage",
		"Use LIMIT to limit result sets",
		"Use WHERE clauses to filter early",
		"Avoid SELECT * in production",
		"Use JOINs efficiently",
		"Use subqueries appropriately",
		"Use database-specific optimizations",
	}

	fmt.Println("Indexing Strategies:")
	for _, strategy := range indexingStrategies {
		fmt.Printf("  - %s\n", strategy)
	}

	fmt.Println("\nOptimization Techniques:")
	for _, technique := range optimizationTechniques {
		fmt.Printf("  - %s\n", technique)
	}
}

// Database error handling
func databaseErrorHandling() {
	fmt.Println("Database Error Handling Examples:")

	commonErrors := map[string]string{
		"connection refused":   "Database server is not running or not accessible",
		"timeout":              "Query took too long to execute",
		"deadlock":             "Transaction deadlock occurred",
		"constraint violation": "Database constraint was violated",
		"connection limit":     "Too many connections to database",
	}

	errorPatterns := []string{
		"Always check for errors after database operations",
		"Use transactions to ensure data consistency",
		"Implement retry logic for transient errors",
		"Log errors for debugging and monitoring",
		"Return meaningful error messages to callers",
	}

	handleDatabaseError := func(err error) error {
		if err == nil {
			return nil
		}
		log.Printf("Database error: %v", err)
		if strings.Contains(err.Error(), "connection refused") {
			return fmt.Errorf("database server is not running: %w", err)
		}
		return fmt.Errorf("database error: %w", err)
	}

	fmt.Println("Common Database Errors:")
	for errorType, description := range commonErrors {
		fmt.Printf("  - %s: %s\n", errorType, description)
	}

	fmt.Println("\nError Handling Patterns:")
	for _, pattern := range errorPatterns {
		fmt.Printf("  - %s\n", pattern)
	}

	_ = handleDatabaseError
}
