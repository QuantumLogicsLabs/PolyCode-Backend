package main

import (
	"database/sql"
	"fmt"
	"log"
	"os"
	"path/filepath"
	"sort"
	"strconv"
	"strings"
	"time"

	_ "github.com/mattn/go-sqlite3"
)

type Migration struct {
	Version     int
	Description string
	SQL         string
	Rollback    string
	AppliedAt   *time.Time
}

type Migrator struct {
	db          *sql.DB
	migrations  []Migration
	migrationTable string
}

func main() {
	fmt.Println("=== Database Migration System ===")

	// Create test database
	dbPath := "migration_test.db"
	defer cleanupDatabase(dbPath)

	db, err := sql.Open("sqlite3", dbPath)
	if err != nil {
		log.Fatal("Failed to open database:", err)
	}
	defer db.Close()

	migrator := NewMigrator(db)

	// Define migrations
	defineMigrations(migrator)

	// Demonstrate migration operations
	demonstrateMigrations(migrator)

	// Demonstrate rollback operations
	demonstrateRollbacks(migrator)

	// Demonstrate migration status
	demonstrateMigrationStatus(migrator)

	// Demonstrate advanced migration features
	demonstrateAdvancedFeatures(migrator)
}

func NewMigrator(db *sql.DB) *Migrator {
	return &Migrator{
		db:             db,
		migrations:     make([]Migration, 0),
		migrationTable: "schema_migrations",
	}
}

func defineMigrations(m *Migrator) {
	// Migration 1: Create users table
	m.AddMigration(Migration{
		Version:     1,
		Description: "Create users table",
		SQL: `
		CREATE TABLE IF NOT EXISTS users (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			username VARCHAR(50) UNIQUE NOT NULL,
			email VARCHAR(100) UNIQUE NOT NULL,
			password_hash VARCHAR(255) NOT NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
		);

		CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
		CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
		`,
		Rollback: "DROP TABLE IF EXISTS users;",
	})

	// Migration 2: Create posts table
	m.AddMigration(Migration{
		Version:     2,
		Description: "Create posts table",
		SQL: `
		CREATE TABLE IF NOT EXISTS posts (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			user_id INTEGER NOT NULL,
			title VARCHAR(255) NOT NULL,
			content TEXT,
			status VARCHAR(20) DEFAULT 'draft',
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
		);

		CREATE INDEX IF NOT EXISTS idx_posts_user_id ON posts(user_id);
		CREATE INDEX IF NOT EXISTS idx_posts_status ON posts(status);
		CREATE INDEX IF NOT EXISTS idx_posts_created_at ON posts(created_at);
		`,
		Rollback: "DROP TABLE IF EXISTS posts;",
	})

	// Migration 3: Add user profile
	m.AddMigration(Migration{
		Version:     3,
		Description: "Add user profile fields",
		SQL: `
		ALTER TABLE users ADD COLUMN first_name VARCHAR(50);
		ALTER TABLE users ADD COLUMN last_name VARCHAR(50);
		ALTER TABLE users ADD COLUMN bio TEXT;
		ALTER TABLE users ADD COLUMN avatar_url VARCHAR(255);
		`,
		Rollback: `
		-- SQLite doesn't support DROP COLUMN, so we need to recreate table
		CREATE TABLE users_temp (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			username VARCHAR(50) UNIQUE NOT NULL,
			email VARCHAR(100) UNIQUE NOT NULL,
			password_hash VARCHAR(255) NOT NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
		);

		INSERT INTO users_temp SELECT id, username, email, password_hash, created_at, updated_at FROM users;
		DROP TABLE users;
		ALTER TABLE users_temp RENAME TO users;

		CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
		CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
		`,
	})

	// Migration 4: Add post categories
	m.AddMigration(Migration{
		Version:     4,
		Description: "Create categories table and link to posts",
		SQL: `
		CREATE TABLE IF NOT EXISTS categories (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			name VARCHAR(50) UNIQUE NOT NULL,
			description TEXT,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP
		);

		CREATE TABLE IF NOT EXISTS post_categories (
			post_id INTEGER NOT NULL,
			category_id INTEGER NOT NULL,
			PRIMARY KEY (post_id, category_id),
			FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
			FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
		);

		INSERT INTO categories (name, description) VALUES 
			('Technology', 'Posts about technology and programming'),
			('Lifestyle', 'Posts about lifestyle and personal topics'),
			('Business', 'Posts about business and entrepreneurship');
		`,
		Rollback: `
		DROP TABLE IF EXISTS post_categories;
		DROP TABLE IF EXISTS categories;
		`,
	})

	// Migration 5: Add user preferences
	m.AddMigration(Migration{
		Version:     5,
		Description: "Add user preferences table",
		SQL: `
		CREATE TABLE IF NOT EXISTS user_preferences (
			user_id INTEGER PRIMARY KEY,
			theme VARCHAR(20) DEFAULT 'light',
			notifications_enabled BOOLEAN DEFAULT 1,
			email_notifications BOOLEAN DEFAULT 1,
			push_notifications BOOLEAN DEFAULT 1,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
		);
		`,
		Rollback: "DROP TABLE IF EXISTS user_preferences;",
	})

	// Migration 6: Add post analytics
	m.AddMigration(Migration{
		Version:     6,
		Description: "Add post analytics and views tracking",
		SQL: `
		CREATE TABLE IF NOT EXISTS post_analytics (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			post_id INTEGER NOT NULL,
			view_count INTEGER DEFAULT 0,
			like_count INTEGER DEFAULT 0,
			comment_count INTEGER DEFAULT 0,
			last_viewed_at DATETIME,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
		);

		CREATE INDEX IF NOT EXISTS idx_post_analytics_post_id ON post_analytics(post_id);
		`,
		Rollback: "DROP TABLE IF EXISTS post_analytics;",
	})
}

func (m *Migrator) AddMigration(migration Migration) {
	m.migrations = append(m.migrations, migration)
}

func demonstrateMigrations(m *Migrator) {
	fmt.Println("\n--- Running Migrations ---")

	// Create migration tracking table
	if err := m.createMigrationTable(); err != nil {
		log.Fatal("Failed to create migration table:", err)
	}

	// Get pending migrations
	pending, err := m.GetPendingMigrations()
	if err != nil {
		log.Fatal("Failed to get pending migrations:", err)
	}

	fmt.Printf("Found %d pending migrations\n", len(pending))

	// Run pending migrations
	for _, migration := range pending {
		fmt.Printf("Applying migration %d: %s\n", migration.Version, migration.Description)
		
		if err := m.ApplyMigration(migration); err != nil {
			log.Printf("Failed to apply migration %d: %v", migration.Version, err)
			continue
		}

		fmt.Printf("✓ Migration %d applied successfully\n", migration.Version)
	}

	fmt.Println("All migrations completed")
}

func (m *Migrator) createMigrationTable() error {
	query := fmt.Sprintf(`
	CREATE TABLE IF NOT EXISTS %s (
		version INTEGER PRIMARY KEY,
		description TEXT NOT NULL,
		applied_at DATETIME DEFAULT CURRENT_TIMESTAMP
	)`, m.migrationTable)

	_, err := m.db.Exec(query)
	return err
}

func (m *Migrator) GetPendingMigrations() ([]Migration, error) {
	// Get applied migrations
	appliedVersions, err := m.getAppliedVersions()
	if err != nil {
		return nil, err
	}

	// Filter pending migrations
	var pending []Migration
	for _, migration := range m.migrations {
		if !contains(appliedVersions, migration.Version) {
			pending = append(pending, migration)
		}
	}

	// Sort by version
	sort.Slice(pending, func(i, j int) bool {
		return pending[i].Version < pending[j].Version
	})

	return pending, nil
}

func (m *Migrator) getAppliedVersions() ([]int, error) {
	query := fmt.Sprintf("SELECT version FROM %s ORDER BY version", m.migrationTable)
	rows, err := m.db.Query(query)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var versions []int
	for rows.Next() {
		var version int
		if err := rows.Scan(&version); err != nil {
			return nil, err
		}
		versions = append(versions, version)
	}

	return versions, nil
}

func (m *Migrator) ApplyMigration(migration Migration) error {
	tx, err := m.db.Begin()
	if err != nil {
		return fmt.Errorf("failed to begin transaction: %w", err)
	}
	defer func() {
		if err != nil {
			tx.Rollback()
		}
	}()

	// Execute migration SQL
	if _, err := tx.Exec(migration.SQL); err != nil {
		return fmt.Errorf("failed to execute migration SQL: %w", err)
	}

	// Record migration
	insertQuery := fmt.Sprintf(`
	INSERT INTO %s (version, description) VALUES (?, ?)`, m.migrationTable)
	if _, err := tx.Exec(insertQuery, migration.Version, migration.Description); err != nil {
		return fmt.Errorf("failed to record migration: %w", err)
	}

	// Commit transaction
	if err := tx.Commit(); err != nil {
		return fmt.Errorf("failed to commit migration: %w", err)
	}

	return nil
}

func demonstrateRollbacks(m *Migrator) {
	fmt.Println("\n--- Demonstrating Rollbacks ---")

	// Get the last applied migration
	applied, err := m.getAppliedVersions()
	if err != nil {
		log.Printf("Failed to get applied versions: %v", err)
		return
	}

	if len(applied) == 0 {
		fmt.Println("No migrations to rollback")
		return
	}

	// Rollback the last migration
	lastVersion := applied[len(applied)-1]
	var lastMigration *Migration

	for i, migration := range m.migrations {
		if migration.Version == lastVersion {
			lastMigration = &m.migrations[i]
			break
		}
	}

	if lastMigration == nil {
		fmt.Printf("Migration %d not found\n", lastVersion)
		return
	}

	fmt.Printf("Rolling back migration %d: %s\n", lastMigration.Version, lastMigration.Description)

	if err := m.RollbackMigration(*lastMigration); err != nil {
		log.Printf("Failed to rollback migration %d: %v", lastMigration.Version, err)
		return
	}

	fmt.Printf("✓ Migration %d rolled back successfully\n", lastMigration.Version)
}

func (m *Migrator) RollbackMigration(migration Migration) error {
	tx, err := m.db.Begin()
	if err != nil {
		return fmt.Errorf("failed to begin transaction: %w", err)
	}
	defer func() {
		if err != nil {
			tx.Rollback()
		}
	}()

	// Execute rollback SQL
	if _, err := tx.Exec(migration.Rollback); err != nil {
		return fmt.Errorf("failed to execute rollback SQL: %w", err)
	}

	// Remove migration record
	deleteQuery := fmt.Sprintf("DELETE FROM %s WHERE version = ?", m.migrationTable)
	if _, err := tx.Exec(deleteQuery, migration.Version); err != nil {
		return fmt.Errorf("failed to remove migration record: %w", err)
	}

	// Commit transaction
	if err := tx.Commit(); err != nil {
		return fmt.Errorf("failed to commit rollback: %w", err)
	}

	return nil
}

func demonstrateMigrationStatus(m *Migrator) {
	fmt.Println("\n--- Migration Status ---")

	// Get all migrations
	appliedVersions, err := m.getAppliedVersions()
	if err != nil {
		log.Printf("Failed to get applied versions: %v", err)
		return
	}

	fmt.Println("Migration Status:")
	fmt.Println("Version | Status    | Description")
	fmt.Println("--------|-----------|---------------------------")

	for _, migration := range m.migrations {
		status := "Pending"
		if contains(appliedVersions, migration.Version) {
			status = "Applied"
		}

		fmt.Printf("%-7d | %-9s | %s\n", migration.Version, status, migration.Description)
	}

	fmt.Printf("\nTotal: %d migrations, %d applied, %d pending\n",
		len(m.migrations), len(appliedVersions), len(m.migrations)-len(appliedVersions))
}

func demonstrateAdvancedFeatures(m *Migrator) {
	fmt.Println("\n--- Advanced Migration Features ---")

	// Demonstrate migration validation
	demonstrateMigrationValidation(m)

	// Demonstrate batch operations
	demonstrateBatchOperations(m)

	// Demonstrate migration dependencies
	demonstrateMigrationDependencies(m)

	// Demonstrate data seeding
	demonstrateDataSeeding(m)
}

func demonstrateMigrationValidation(m *Migrator) {
	fmt.Println("\n--- Migration Validation ---")

	// Validate SQL syntax
	for _, migration := range m.migrations {
		if err := m.validateMigrationSQL(migration); err != nil {
			fmt.Printf("❌ Migration %d validation failed: %v\n", migration.Version, err)
		} else {
			fmt.Printf("✓ Migration %d SQL validation passed\n", migration.Version)
		}
	}
}

func (m *Migrator) validateMigrationSQL(migration Migration) error {
	// Basic SQL validation (in production, use a proper SQL parser)
	if strings.TrimSpace(migration.SQL) == "" {
		return fmt.Errorf("migration SQL is empty")
	}

	// Check for dangerous operations
	dangerousOps := []string{"DROP DATABASE", "TRUNCATE", "DELETE FROM"}
	for _, op := range dangerousOps {
		if strings.Contains(strings.ToUpper(migration.SQL), op) {
			return fmt.Errorf("contains dangerous operation: %s", op)
		}
	}

	return nil
}

func demonstrateBatchOperations(m *Migrator) {
	fmt.Println("\n--- Batch Operations ---")

	// Re-apply rolled back migration
	pending, err := m.GetPendingMigrations()
	if err != nil {
		log.Printf("Failed to get pending migrations: %v", err)
		return
	}

	for _, migration := range pending {
		if migration.Version == 6 { // Re-apply the analytics migration
			fmt.Printf("Re-applying migration %d: %s\n", migration.Version, migration.Description)
			
			if err := m.ApplyMigration(migration); err != nil {
				log.Printf("Failed to re-apply migration: %v", err)
				continue
			}

			fmt.Printf("✓ Migration %d re-applied successfully\n", migration.Version)
			break
		}
	}
}

func demonstrateMigrationDependencies(m *Migrator) {
	fmt.Println("\n--- Migration Dependencies ---")

	// Check migration dependencies
	dependencies := map[int][]int{
		2: {1}, // Posts table depends on users table
		4: {2}, // Categories depend on posts table
		5: {1}, // User preferences depend on users table
		6: {2}, // Post analytics depend on posts table
	}

	appliedVersions, err := m.getAppliedVersions()
	if err != nil {
		log.Printf("Failed to get applied versions: %v", err)
		return
	}

	for version, deps := range dependencies {
		fmt.Printf("Migration %d dependencies: %v\n", version, deps)
		
		allDepsSatisfied := true
		for _, dep := range deps {
			if !contains(appliedVersions, dep) {
				fmt.Printf("  ❌ Dependency %d not satisfied\n", dep)
				allDepsSatisfied = false
			}
		}

		if allDepsSatisfied {
			fmt.Printf("  ✓ All dependencies satisfied\n")
		}
	}
}

func demonstrateDataSeeding(m *Migrator) {
	fmt.Println("\n--- Data Seeding ---")

	// Seed data after migrations
	if err := m.seedData(); err != nil {
		log.Printf("Failed to seed data: %v", err)
		return
	}

	fmt.Println("✓ Data seeded successfully")

	// Verify seeded data
	if err := m.verifySeededData(); err != nil {
		log.Printf("Failed to verify seeded data: %v", err)
		return
	}

	fmt.Println("✓ Seeded data verified")
}

func (m *Migrator) seedData() error {
	// Seed users
	users := []struct {
		username string
		email    string
		password string
	}{
		{"john_doe", "john@example.com", "hashed_password_1"},
		{"jane_smith", "jane@example.com", "hashed_password_2"},
		{"bob_wilson", "bob@example.com", "hashed_password_3"},
	}

	for _, user := range users {
		query := `INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)`
		_, err := m.db.Exec(query, user.username, user.email, user.password)
		if err != nil {
			return fmt.Errorf("failed to seed user %s: %w", user.username, err)
		}
	}

	// Seed posts
	posts := []struct {
		userID int
		title  string
		content string
		status string
	}{
		{1, "First Post", "This is my first post content", "published"},
		{1, "Second Post", "This is my second post content", "published"},
		{2, "Jane's First Post", "Jane's first post content", "draft"},
	}

	for _, post := range posts {
		query := `INSERT INTO posts (user_id, title, content, status) VALUES (?, ?, ?, ?)`
		_, err := m.db.Exec(query, post.userID, post.title, post.content, post.status)
		if err != nil {
			return fmt.Errorf("failed to seed post: %w", err)
		}
	}

	return nil
}

func (m *Migrator) verifySeededData() error {
	// Verify users
	var userCount int
	err := m.db.QueryRow("SELECT COUNT(*) FROM users").Scan(&userCount)
	if err != nil {
		return fmt.Errorf("failed to count users: %w", err)
	}
	fmt.Printf("Seeded %d users\n", userCount)

	// Verify posts
	var postCount int
	err = m.db.QueryRow("SELECT COUNT(*) FROM posts").Scan(&postCount)
	if err != nil {
		return fmt.Errorf("failed to count posts: %w", err)
	}
	fmt.Printf("Seeded %d posts\n", postCount)

	// Verify categories
	var categoryCount int
	err = m.db.QueryRow("SELECT COUNT(*) FROM categories").Scan(&categoryCount)
	if err != nil {
		return fmt.Errorf("failed to count categories: %w", err)
	}
	fmt.Printf("Seeded %d categories\n", categoryCount)

	return nil
}

func contains(slice []int, item int) bool {
	for _, s := range slice {
		if s == item {
			return true
		}
	}
	return false
}

func cleanupDatabase(dbPath string) {
	if err := os.Remove(dbPath); err != nil && !os.IsNotExist(err) {
		log.Printf("Warning: failed to remove database file: %v", err)
	}
}
