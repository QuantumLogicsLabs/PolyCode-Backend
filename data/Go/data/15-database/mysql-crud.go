package main

import (
	"database/sql"
	"fmt"
	"log"
	"time"

	_ "github.com/go-sql-driver/mysql"
)

type Product struct {
	ID          int       `json:"id"`
	Name        string    `json:"name"`
	Description string    `json:"description"`
	Price       float64   `json:"price"`
	Stock       int       `json:"stock"`
	Category    string    `json:"category"`
	CreatedAt   time.Time `json:"created_at"`
	UpdatedAt   time.Time `json:"updated_at"`
}

type DatabaseConfig struct {
	Host     string
	Port     int
	User     string
	Password string
	Database string
}

func main() {
	fmt.Println("=== MySQL CRUD Operations ===")

	// Database configuration (use environment variables in production)
	config := DatabaseConfig{
		Host:     "localhost",
		Port:     3306,
		User:     "root",
		Password: "password", // Change this in production
		Database: "testdb",
	}

	// Connect to database
	db, err := connectToDatabase(config)
	if err != nil {
		log.Fatal("Failed to connect to database:", err)
	}
	defer db.Close()

	// Create table
	if err := createProductsTable(db); err != nil {
		log.Fatal("Failed to create table:", err)
	}

	// CRUD operations
	if err := demonstrateCRUD(db); err != nil {
		log.Fatal("CRUD operations failed:", err)
	}

	// Advanced queries
	if err := demonstrateAdvancedQueries(db); err != nil {
		log.Fatal("Advanced queries failed:", err)
	}

	// Connection pooling
	demonstrateConnectionPooling(config)
}

func connectToDatabase(config DatabaseConfig) (*sql.DB, error) {
	dsn := fmt.Sprintf("%s:%s@tcp(%s:%d)/%s?parseTime=true",
		config.User, config.Password, config.Host, config.Port, config.Database)

	db, err := sql.Open("mysql", dsn)
	if err != nil {
		return nil, fmt.Errorf("failed to open database connection: %w", err)
	}

	// Test connection
	if err := db.Ping(); err != nil {
		return nil, fmt.Errorf("failed to ping database: %w", err)
	}

	fmt.Println("✓ Connected to MySQL database")
	return db, nil
}

func createProductsTable(db *sql.DB) error {
	query := `
	CREATE TABLE IF NOT EXISTS products (
		id INT AUTO_INCREMENT PRIMARY KEY,
		name VARCHAR(255) NOT NULL,
		description TEXT,
		price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
		stock INT NOT NULL DEFAULT 0,
		category VARCHAR(100),
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		INDEX idx_category (category),
		INDEX idx_price (price),
		INDEX idx_created_at (created_at)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci`

	_, err := db.Exec(query)
	if err != nil {
		return fmt.Errorf("failed to create products table: %w", err)
	}

	fmt.Println("✓ Products table created successfully")
	return nil
}

func demonstrateCRUD(db *sql.DB) error {
	fmt.Println("\n--- CRUD Operations ---")

	// CREATE
	product := Product{
		Name:        "Laptop Pro",
		Description: "High-performance laptop for professionals",
		Price:       1299.99,
		Stock:       50,
		Category:    "Electronics",
	}

	id, err := createProduct(db, product)
	if err != nil {
		return fmt.Errorf("failed to create product: %w", err)
	}
	fmt.Printf("✓ Created product with ID: %d\n", id)

	// READ
	retrieved, err := getProduct(db, id)
	if err != nil {
		return fmt.Errorf("failed to retrieve product: %w", err)
	}
	fmt.Printf("✓ Retrieved product: %+v\n", retrieved)

	// UPDATE
	product.Description = "Updated description for high-performance laptop"
	product.Price = 1199.99
	if err := updateProduct(db, id, product); err != nil {
		return fmt.Errorf("failed to update product: %w", err)
	}
	fmt.Printf("✓ Updated product ID: %d\n", id)

	// DELETE
	if err := deleteProduct(db, id); err != nil {
		return fmt.Errorf("failed to delete product: %w", err)
	}
	fmt.Printf("✓ Deleted product ID: %d\n", id)

	return nil
}

func createProduct(db *sql.DB, product Product) (int64, error) {
	query := `
	INSERT INTO products (name, description, price, stock, category) 
	VALUES (?, ?, ?, ?, ?)`

	result, err := db.Exec(query, product.Name, product.Description, 
		product.Price, product.Stock, product.Category)
	if err != nil {
		return 0, fmt.Errorf("failed to insert product: %w", err)
	}

	id, err := result.LastInsertId()
	if err != nil {
		return 0, fmt.Errorf("failed to get last insert ID: %w", err)
	}

	return id, nil
}

func getProduct(db *sql.DB, id int64) (*Product, error) {
	query := `
	SELECT id, name, description, price, stock, category, created_at, updated_at 
	FROM products WHERE id = ?`

	var product Product
	err := db.QueryRow(query, id).Scan(
		&product.ID, &product.Name, &product.Description,
		&product.Price, &product.Stock, &product.Category,
		&product.CreatedAt, &product.UpdatedAt)

	if err != nil {
		if err == sql.ErrNoRows {
			return nil, fmt.Errorf("product with ID %d not found", id)
		}
		return nil, fmt.Errorf("failed to scan product: %w", err)
	}

	return &product, nil
}

func updateProduct(db *sql.DB, id int64, product Product) error {
	query := `
	UPDATE products 
	SET name = ?, description = ?, price = ?, stock = ?, category = ?
	WHERE id = ?`

	result, err := db.Exec(query, product.Name, product.Description,
		product.Price, product.Stock, product.Category, id)
	if err != nil {
		return fmt.Errorf("failed to update product: %w", err)
	}

	rowsAffected, err := result.RowsAffected()
	if err != nil {
		return fmt.Errorf("failed to get rows affected: %w", err)
	}

	if rowsAffected == 0 {
		return fmt.Errorf("no product found with ID %d", id)
	}

	return nil
}

func deleteProduct(db *sql.DB, id int64) error {
	query := `DELETE FROM products WHERE id = ?`
	result, err := db.Exec(query, id)
	if err != nil {
		return fmt.Errorf("failed to delete product: %w", err)
	}

	rowsAffected, err := result.RowsAffected()
	if err != nil {
		return fmt.Errorf("failed to get rows affected: %w", err)
	}

	if rowsAffected == 0 {
		return fmt.Errorf("no product found with ID %d", id)
	}

	return nil
}

func demonstrateAdvancedQueries(db *sql.DB) error {
	fmt.Println("\n--- Advanced Queries ---")

	// Insert sample data for advanced queries
	if err := insertSampleProducts(db); err != nil {
		return fmt.Errorf("failed to insert sample products: %w", err)
	}

	// Query with pagination
	if err := queryWithPagination(db, 1, 3); err != nil {
		return fmt.Errorf("pagination query failed: %w", err)
	}

	// Query with filtering
	if err := queryWithFilters(db, "Electronics", 100.0); err != nil {
		return fmt.Errorf("filter query failed: %w", err)
	}

	// Aggregate queries
	if err := aggregateQueries(db); err != nil {
		return fmt.Errorf("aggregate query failed: %w", err)
	}

	// Prepared statements
	if err := demonstratePreparedStatements(db); err != nil {
		return fmt.Errorf("prepared statement demo failed: %w", err)
	}

	return nil
}

func insertSampleProducts(db *sql.DB) error {
	products := []Product{
		{Name: "Smartphone X", Description: "Latest smartphone", Price: 899.99, Stock: 100, Category: "Electronics"},
		{Name: "Tablet Pro", Description: "Professional tablet", Price: 699.99, Stock: 75, Category: "Electronics"},
		{Name: "Wireless Mouse", Description: "Ergonomic mouse", Price: 49.99, Stock: 200, Category: "Accessories"},
		{Name: "Mechanical Keyboard", Description: "Gaming keyboard", Price: 149.99, Stock: 150, Category: "Accessories"},
		{Name: "4K Monitor", Description: "Ultra HD monitor", Price: 499.99, Stock: 50, Category: "Electronics"},
	}

	for _, product := range products {
		_, err := createProduct(db, product)
		if err != nil {
			return fmt.Errorf("failed to insert sample product %s: %w", product.Name, err)
		}
	}

	fmt.Println("✓ Sample products inserted")
	return nil
}

func queryWithPagination(db *sql.DB, page, limit int) error {
	offset := (page - 1) * limit
	query := `
	SELECT id, name, price, category 
	FROM products 
	ORDER BY price DESC 
	LIMIT ? OFFSET ?`

	rows, err := db.Query(query, limit, offset)
	if err != nil {
		return fmt.Errorf("failed to execute paginated query: %w", err)
	}
	defer rows.Close()

	fmt.Printf("Products (Page %d, Limit %d):\n", page, limit)
	for rows.Next() {
		var id int
		var name, category string
		var price float64

		err := rows.Scan(&id, &name, &price, &category)
		if err != nil {
			return fmt.Errorf("failed to scan row: %w", err)
		}

		fmt.Printf("  - %s (%s): $%.2f\n", name, category, price)
	}

	return nil
}

func queryWithFilters(db *sql.DB, category string, minPrice float64) error {
	query := `
	SELECT id, name, price, stock 
	FROM products 
	WHERE category = ? AND price >= ?
	ORDER BY price ASC`

	rows, err := db.Query(query, category, minPrice)
	if err != nil {
		return fmt.Errorf("failed to execute filtered query: %w", err)
	}
	defer rows.Close()

	fmt.Printf("%s products priced at $%.2f or above:\n", category, minPrice)
	for rows.Next() {
		var id int
		var name string
		var price float64
		var stock int

		err := rows.Scan(&id, &name, &price, &stock)
		if err != nil {
			return fmt.Errorf("failed to scan row: %w", err)
		}

		fmt.Printf("  - %s: $%.2f (Stock: %d)\n", name, price, stock)
	}

	return nil
}

func aggregateQueries(db *sql.DB) error {
	// Count by category
	query := `
	SELECT category, COUNT(*) as count, AVG(price) as avg_price, SUM(stock) as total_stock
	FROM products 
	GROUP BY category`

	rows, err := db.Query(query)
	if err != nil {
		return fmt.Errorf("failed to execute aggregate query: %w", err)
	}
	defer rows.Close()

	fmt.Println("Product statistics by category:")
	for rows.Next() {
		var category string
		var count, totalStock int
		var avgPrice float64

		err := rows.Scan(&category, &count, &avgPrice, &totalStock)
		if err != nil {
			return fmt.Errorf("failed to scan aggregate row: %w", err)
		}

		fmt.Printf("  - %s: %d products, Avg Price: $%.2f, Total Stock: %d\n",
			category, count, avgPrice, totalStock)
	}

	return nil
}

func demonstratePreparedStatements(db *sql.DB) error {
	fmt.Println("\n--- Prepared Statements ---")

	// Prepare statement for batch insert
	stmt, err := db.Prepare(`
		INSERT INTO products (name, description, price, stock, category) 
		VALUES (?, ?, ?, ?, ?)`)
	if err != nil {
		return fmt.Errorf("failed to prepare statement: %w", err)
	}
	defer stmt.Close()

	// Batch insert using prepared statement
	products := []Product{
		{Name: "USB-C Cable", Description: "Fast charging cable", Price: 19.99, Stock: 500, Category: "Accessories"},
		{Name: "Laptop Stand", Description: "Adjustable stand", Price: 39.99, Stock: 100, Category: "Accessories"},
	}

	for _, product := range products {
		result, err := stmt.Exec(product.Name, product.Description,
			product.Price, product.Stock, product.Category)
		if err != nil {
			return fmt.Errorf("failed to execute prepared statement: %w", err)
		}

		id, _ := result.LastInsertId()
		fmt.Printf("✓ Inserted product via prepared statement (ID: %d)\n", id)
	}

	return nil
}

func demonstrateConnectionPooling(config DatabaseConfig) {
	fmt.Println("\n--- Connection Pooling ---")

	// Open database with custom connection pool settings
	dsn := fmt.Sprintf("%s:%s@tcp(%s:%d)/%s?parseTime=true",
		config.User, config.Password, config.Host, config.Port, config.Database)

	db, err := sql.Open("mysql", dsn)
	if err != nil {
		log.Printf("Failed to open database for pooling demo: %v", err)
		return
	}
	defer db.Close()

	// Configure connection pool
	db.SetMaxOpenConns(25)                 // Maximum number of open connections
	db.SetMaxIdleConns(5)                  // Maximum number of idle connections
	db.SetConnMaxLifetime(5 * time.Minute) // Maximum lifetime of a connection
	db.SetConnMaxIdleTime(5 * time.Minute)  // Maximum idle time for a connection

	fmt.Printf("Connection pool configured:\n")
	fmt.Printf("  - Max Open Connections: %d\n", db.Stats().MaxOpenConnections)
	fmt.Printf("  - Max Idle Connections: %d\n", db.Stats().MaxIdleConnections)
	fmt.Printf("  - Current Open Connections: %d\n", db.Stats().OpenConnections)
	fmt.Printf("  - Current Idle Connections: %d\n", db.Stats().IdleConnections)
}
}
