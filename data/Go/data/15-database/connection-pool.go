package main

import (
	"context"
	"database/sql"
	"fmt"
	"log"
	"sync"
	"time"

	_ "github.com/go-sql-driver/mysql"
	_ "github.com/lib/pq"
	_ "github.com/mattn/go-sqlite3"
)

type DatabaseType string

const (
	SQLite    DatabaseType = "sqlite"
	MySQL     DatabaseType = "mysql"
	Postgres  DatabaseType = "postgres"
)

type PoolConfig struct {
	MaxOpenConns    int
	MaxIdleConns    int
	ConnMaxLifetime time.Duration
	ConnMaxIdleTime time.Duration
}

type DatabasePool struct {
	db     *sql.DB
	config PoolConfig
	mu     sync.RWMutex
	stats  PoolStats
}

type PoolStats struct {
	OpenConnections int
	InUse           int
	Idle            int
	WaitCount       int64
	WaitDuration    time.Duration
	MaxIdleClosed   int64
	MaxLifetimeClosed int64
}

func main() {
	fmt.Println("=== Database Connection Pool Management ===")

	// Test different database types
	databases := []DatabaseType{SQLite, MySQL, Postgres}

	for _, dbType := range databases {
		fmt.Printf("\n--- Testing %s Connection Pool ---\n", dbType)
		
		pool, err := createDatabasePool(dbType)
		if err != nil {
			log.Printf("Failed to create %s pool: %v", dbType, err)
			continue
		}

		demonstratePoolOperations(pool, dbType)
		demonstratePoolConfiguration(pool)
		demonstrateConcurrency(pool, dbType)
		demonstratePoolHealth(pool, dbType)

		pool.Close()
	}

	// Advanced pool management
	demonstrateAdvancedPooling()
}

func createDatabasePool(dbType DatabaseType) (*DatabasePool, error) {
	var dsn string
	var config PoolConfig

	switch dbType {
	case SQLite:
		dsn = "test.db"
		config = PoolConfig{
			MaxOpenConns:    10,
			MaxIdleConns:    5,
			ConnMaxLifetime: time.Hour,
			ConnMaxIdleTime: time.Minute * 30,
		}
	case MySQL:
		dsn = "root:password@tcp(localhost:3306)/test?parseTime=true"
		config = PoolConfig{
			MaxOpenConns:    25,
			MaxIdleConns:    10,
			ConnMaxLifetime: time.Hour * 2,
			ConnMaxIdleTime: time.Minute * 15,
		}
	case Postgres:
		dsn = "postgres://postgres:password@localhost:5432/test?sslmode=disable"
		config = PoolConfig{
			MaxOpenConns:    20,
			MaxIdleConns:    8,
			ConnMaxLifetime: time.Hour * 1,
			ConnMaxIdleTime: time.Minute * 20,
		}
	default:
		return nil, fmt.Errorf("unsupported database type: %s", dbType)
	}

	db, err := sql.Open(string(dbType), dsn)
	if err != nil {
		return nil, fmt.Errorf("failed to open database: %w", err)
	}

	// Test connection
	if err := db.Ping(); err != nil {
		if dbType == SQLite {
			// SQLite might work even if ping fails initially
			log.Printf("Warning: SQLite ping failed: %v", err)
		} else {
			return nil, fmt.Errorf("failed to ping database: %w", err)
		}
	}

	pool := &DatabasePool{
		db:     db,
		config: config,
	}

	// Configure pool
	pool.configurePool()

	fmt.Printf("✓ %s connection pool created\n", dbType)
	return pool, nil
}

func (p *DatabasePool) configurePool() {
	p.mu.Lock()
	defer p.mu.Unlock()

	p.db.SetMaxOpenConns(p.config.MaxOpenConns)
	p.db.SetMaxIdleConns(p.config.MaxIdleConns)
	p.db.SetConnMaxLifetime(p.config.ConnMaxLifetime)
	p.db.SetConnMaxIdleTime(p.config.ConnMaxIdleTime)
}

func (p *DatabasePool) Close() error {
	return p.db.Close()
}

func (p *DatabasePool) GetStats() PoolStats {
	p.mu.RLock()
	defer p.mu.RUnlock()

	stats := p.db.Stats()
	return PoolStats{
		OpenConnections:   stats.OpenConnections,
		InUse:            stats.InUse,
		Idle:             stats.Idle,
		WaitCount:        stats.WaitCount,
		WaitDuration:     stats.WaitDuration,
		MaxIdleClosed:    stats.MaxIdleClosed,
		MaxLifetimeClosed: stats.MaxLifetimeClosed,
	}
}

func demonstratePoolOperations(pool *DatabasePool, dbType DatabaseType) {
	fmt.Println("\n--- Basic Pool Operations ---")

	// Create test table if it doesn't exist
	if err := createTestTable(pool.db, dbType); err != nil {
		log.Printf("Failed to create test table: %v", err)
		return
	}

	// Test connection acquisition and release
	fmt.Println("Testing connection acquisition...")
	for i := 0; i < 10; i++ {
		start := time.Now()
		
		// Simulate database operation
		if err := simulateDBOperation(pool.db, i); err != nil {
			log.Printf("DB operation %d failed: %v", i, err)
		}
		
		duration := time.Since(start)
		stats := pool.GetStats()
		fmt.Printf("Operation %d: %v (Open: %d, InUse: %d, Idle: %d)\n",
			i, duration, stats.OpenConnections, stats.InUse, stats.Idle)
	}
}

func createTestTable(db *sql.DB, dbType DatabaseType) error {
	var query string
	
	switch dbType {
	case SQLite:
		query = `
		CREATE TABLE IF NOT EXISTS test_table (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			name TEXT NOT NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP
		)`
	case MySQL:
		query = `
		CREATE TABLE IF NOT EXISTS test_table (
			id INT AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(255) NOT NULL,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		)`
	case Postgres:
		query = `
		CREATE TABLE IF NOT EXISTS test_table (
			id SERIAL PRIMARY KEY,
			name VARCHAR(255) NOT NULL,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
		)`
	}

	_, err := db.Exec(query)
	return err
}

func simulateDBOperation(db *sql.DB, id int) error {
	// Insert operation
	insertQuery := `INSERT INTO test_table (name) VALUES (?)`
	_, err := db.Exec(insertQuery, fmt.Sprintf("Record %d", id))
	if err != nil {
		return fmt.Errorf("insert failed: %w", err)
	}

	// Query operation
	var count int
	query := `SELECT COUNT(*) FROM test_table`
	err = db.QueryRow(query).Scan(&count)
	if err != nil {
		return fmt.Errorf("query failed: %w", err)
	}

	return nil
}

func demonstratePoolConfiguration(pool *DatabasePool) {
	fmt.Println("\n--- Pool Configuration ---")
	
	fmt.Printf("Current Configuration:\n")
	fmt.Printf("  Max Open Connections: %d\n", pool.config.MaxOpenConns)
	fmt.Printf("  Max Idle Connections: %d\n", pool.config.MaxIdleConns)
	fmt.Printf("  Connection Max Lifetime: %v\n", pool.config.ConnMaxLifetime)
	fmt.Printf("  Connection Max Idle Time: %v\n", pool.config.ConnMaxIdleTime)

	stats := pool.GetStats()
	fmt.Printf("\nCurrent Stats:\n")
	fmt.Printf("  Open Connections: %d\n", stats.OpenConnections)
	fmt.Printf("  In Use: %d\n", stats.InUse)
	fmt.Printf("  Idle: %d\n", stats.Idle)
	fmt.Printf("  Wait Count: %d\n", stats.WaitCount)
	fmt.Printf("  Wait Duration: %v\n", stats.WaitDuration)
}

func demonstrateConcurrency(pool *DatabasePool, dbType DatabaseType) {
	fmt.Println("\n--- Concurrent Operations ---")

	const numGoroutines = 20
	const operationsPerGoroutine = 10

	var wg sync.WaitGroup
	start := time.Now()

	for i := 0; i < numGoroutines; i++ {
		wg.Add(1)
		go func(goroutineID int) {
			defer wg.Done()
			
			for j := 0; j < operationsPerGoroutine; j++ {
				if err := simulateDBOperation(pool.db, goroutineID*1000+j); err != nil {
					log.Printf("Goroutine %d operation %d failed: %v", goroutineID, j, err)
				}
				
				// Small delay to simulate real work
				time.Sleep(time.Millisecond * 10)
			}
		}(i)
	}

	wg.Wait()
	duration := time.Since(start)

	stats := pool.GetStats()
	totalOperations := numGoroutines * operationsPerGoroutine
	
	fmt.Printf("Concurrency Test Results:\n")
	fmt.Printf("  Total Operations: %d\n", totalOperations)
	fmt.Printf("  Duration: %v\n", duration)
	fmt.Printf("  Operations/Second: %.2f\n", float64(totalOperations)/duration.Seconds())
	fmt.Printf("  Final Pool Stats - Open: %d, InUse: %d, Idle: %d\n",
		stats.OpenConnections, stats.InUse, stats.Idle)
	fmt.Printf("  Total Waits: %d, Total Wait Time: %v\n", stats.WaitCount, stats.WaitDuration)
}

func demonstratePoolHealth(pool *DatabasePool, dbType DatabaseType) {
	fmt.Println("\n--- Pool Health Monitoring ---")

	// Monitor pool health over time
	for i := 0; i < 5; i++ {
		stats := pool.GetStats()
		
		fmt.Printf("Health Check %d:\n", i+1)
		fmt.Printf("  Open Connections: %d/%d (%.1f%%)\n",
			stats.OpenConnections, pool.config.MaxOpenConns,
			float64(stats.OpenConnections)/float64(pool.config.MaxOpenConns)*100)
		fmt.Printf("  Idle Connections: %d\n", stats.Idle)
		fmt.Printf("  In Use: %d\n", stats.InUse)
		fmt.Printf("  Wait Count: %d\n", stats.WaitCount)
		
		if stats.WaitCount > 0 {
			avgWaitTime := stats.WaitDuration / time.Duration(stats.WaitCount)
			fmt.Printf("  Average Wait Time: %v\n", avgWaitTime)
		}

		// Perform some operations to see stats change
		for j := 0; j < 5; j++ {
			simulateDBOperation(pool.db, i*100+j)
		}

		time.Sleep(time.Second)
	}
}

func demonstrateAdvancedPooling() {
	fmt.Println("\n--- Advanced Pool Management ---")

	// Context-aware operations
	demonstrateContextOperations()

	// Connection lifetime management
	demonstrateConnectionLifecycle()

	// Pool optimization
	demonstratePoolOptimization()
}

func demonstrateContextOperations() {
	fmt.Println("\n--- Context-Aware Operations ---")

	db, err := sql.Open("sqlite3", "test_context.db")
	if err != nil {
		log.Printf("Failed to open database for context demo: %v", err)
		return
	}
	defer db.Close()
	defer os.Remove("test_context.db")

	// Configure pool
	db.SetMaxOpenConns(5)
	db.SetMaxIdleConns(2)

	// Create test table
	createTestTable(db, SQLite)

	// Context with timeout
	ctx, cancel := context.WithTimeout(context.Background(), time.Millisecond*100)
	defer cancel()

	start := time.Now()
	_, err = db.ExecContext(ctx, "INSERT INTO test_table (name) VALUES (?)", "Context Test")
	duration := time.Since(start)

	if err != nil {
		if ctx.Err() == context.DeadlineExceeded {
			fmt.Printf("Operation timed out after %v\n", duration)
		} else {
			fmt.Printf("Operation failed: %v (took %v)\n", err, duration)
		}
	} else {
		fmt.Printf("Operation completed in %v\n", duration)
	}

	// Context with cancellation
	ctx, cancel = context.WithCancel(context.Background())
	
	go func() {
		time.Sleep(time.Millisecond * 50)
		cancel()
	}()

	start = time.Now()
	_, err = db.ExecContext(ctx, "INSERT INTO test_table (name) VALUES (?)", "Cancel Test")
	duration = time.Since(start)

	if err != nil {
		if ctx.Err() == context.Canceled {
			fmt.Printf("Operation was cancelled after %v\n", duration)
		} else {
			fmt.Printf("Operation failed: %v (took %v)\n", err, duration)
		}
	} else {
		fmt.Printf("Operation completed in %v\n", duration)
	}
}

func demonstrateConnectionLifecycle() {
	fmt.Println("\n--- Connection Lifecycle Management ---")

	db, err := sql.Open("sqlite3", "test_lifecycle.db")
	if err != nil {
		log.Printf("Failed to open database for lifecycle demo: %v", err)
		return
	}
	defer db.Close()
	defer os.Remove("test_lifecycle.db")

	// Configure with short lifetimes for demonstration
	db.SetMaxOpenConns(3)
	db.SetMaxIdleConns(1)
	db.SetConnMaxLifetime(time.Second * 2) // Very short lifetime
	db.SetConnMaxIdleTime(time.Second * 1)  // Very short idle time

	createTestTable(db, SQLite)

	fmt.Println("Monitoring connection lifecycle (check connection IDs):")

	// Track connections over time
	for i := 0; i < 10; i++ {
		stats := db.Stats()
		
		// Perform operation to potentially trigger connection creation
		simulateDBOperation(db, i)
		
		fmt.Printf("Cycle %d: Open=%d, InUse=%d, Idle=%d, MaxIdleClosed=%d, MaxLifetimeClosed=%d\n",
			i, stats.OpenConnections, stats.InUse, stats.Idle,
			stats.MaxIdleClosed, stats.MaxLifetimeClosed)

		// Wait to allow connections to expire
		time.Sleep(time.Millisecond * 500)
	}
}

func demonstratePoolOptimization() {
	fmt.Println("\n--- Pool Optimization Strategies ---")

	// Test different pool configurations
	configurations := []struct {
		name string
		config PoolConfig
	}{
		{
			name: "Conservative",
			config: PoolConfig{
				MaxOpenConns:    5,
				MaxIdleConns:    2,
				ConnMaxLifetime: time.Hour,
				ConnMaxIdleTime: time.Minute * 30,
			},
		},
		{
			name: "Balanced",
			config: PoolConfig{
				MaxOpenConns:    15,
				MaxIdleConns:    5,
				ConnMaxLifetime: time.Hour * 2,
				ConnMaxIdleTime: time.Minute * 15,
			},
		},
		{
			name: "Aggressive",
			config: PoolConfig{
				MaxOpenConns:    50,
				MaxIdleConns:    20,
				ConnMaxLifetime: time.Hour * 4,
				ConnMaxIdleTime: time.Minute * 5,
			},
		},
	}

	for _, config := range configurations {
		fmt.Printf("\nTesting %s configuration:\n", config.name)
		testPoolConfiguration(config.config)
	}
}

func testPoolConfiguration(config PoolConfig) {
	db, err := sql.Open("sqlite3", "test_opt.db")
	if err != nil {
		log.Printf("Failed to open database for optimization test: %v", err)
		return
	}
	defer db.Close()
	defer os.Remove("test_opt.db")

	// Apply configuration
	db.SetMaxOpenConns(config.MaxOpenConns)
	db.SetMaxIdleConns(config.MaxIdleConns)
	db.SetConnMaxLifetime(config.ConnMaxLifetime)
	db.SetConnMaxIdleTime(config.ConnMaxIdleTime)

	createTestTable(db, SQLite)

	// Test with concurrent load
	const numGoroutines = 10
	const operationsPerGoroutine = 20

	var wg sync.WaitGroup
	start := time.Now()

	for i := 0; i < numGoroutines; i++ {
		wg.Add(1)
		go func(id int) {
			defer wg.Done()
			for j := 0; j < operationsPerGoroutine; j++ {
				simulateDBOperation(db, id*1000+j)
			}
		}(i)
	}

	wg.Wait()
	duration := time.Since(start)

	stats := db.Stats()
	totalOps := numGoroutines * operationsPerGoroutine

	fmt.Printf("  Duration: %v\n", duration)
	fmt.Printf("  Throughput: %.2f ops/sec\n", float64(totalOps)/duration.Seconds())
	fmt.Printf("  Final Stats: Open=%d, WaitCount=%d, WaitDuration=%v\n",
		stats.OpenConnections, stats.WaitCount, stats.WaitDuration)
	
	if stats.WaitCount > 0 {
		avgWait := stats.WaitDuration / time.Duration(stats.WaitCount)
		fmt.Printf("  Average Wait: %v\n", avgWait)
	}
}
