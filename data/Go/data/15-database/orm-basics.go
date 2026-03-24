package main

import (
	"database/sql"
	"fmt"
	"log"
	"time"

	_ "github.com/mattn/go-sqlite3"
)

// Simple ORM implementation
type Model interface {
	TableName() string
	Fields() []string
	Values() []interface{}
	Scan(rows *sql.Rows) error
}

type ORM struct {
	db *sql.DB
}

type User struct {
	ID        int       `json:"id"`
	Username  string    `json:"username"`
	Email     string    `json:"email"`
	FirstName string    `json:"first_name"`
	LastName  string    `json:"last_name"`
	Bio       string    `json:"bio"`
	CreatedAt time.Time `json:"created_at"`
	UpdatedAt time.Time `json:"updated_at"`
}

type Post struct {
	ID        int       `json:"id"`
	UserID    int       `json:"user_id"`
	Title     string    `json:"title"`
	Content   string    `json:"content"`
	Status    string    `json:"status"`
	CreatedAt time.Time `json:"created_at"`
	UpdatedAt time.Time `json:"updated_at"`
}

type QueryBuilder struct {
	table    string
	where    []string
	orderBy  string
	limit    int
	offset   int
	joins    []string
	params   []interface{}
}

func main() {
	fmt.Println("=== Simple ORM Implementation ===")

	// Create database
	dbPath := "orm_test.db"
	defer cleanupDatabase(dbPath)

	db, err := sql.Open("sqlite3", dbPath)
	if err != nil {
		log.Fatal("Failed to open database:", err)
	}
	defer db.Close()

	orm := NewORM(db)

	// Create tables
	if err := orm.CreateTable(&User{}); err != nil {
		log.Fatal("Failed to create users table:", err)
	}

	if err := orm.CreateTable(&Post{}); err != nil {
		log.Fatal("Failed to create posts table:", err)
	}

	// Demonstrate ORM operations
	demonstrateCRUD(orm)
	demonstrateQueries(orm)
	demonstrateRelationships(orm)
	demonstrateAdvancedOperations(orm)
}

func NewORM(db *sql.DB) *ORM {
	return &ORM{db: db}
}

// Model interface implementations for User
func (u *User) TableName() string {
	return "users"
}

func (u *User) Fields() []string {
	return []string{"username", "email", "first_name", "last_name", "bio"}
}

func (u *User) Values() []interface{} {
	return []interface{}{u.Username, u.Email, u.FirstName, u.LastName, u.Bio}
}

func (u *User) Scan(rows *sql.Rows) error {
	return rows.Scan(&u.ID, &u.Username, &u.Email, &u.FirstName, &u.LastName, &u.Bio, &u.CreatedAt, &u.UpdatedAt)
}

// Model interface implementations for Post
func (p *Post) TableName() string {
	return "posts"
}

func (p *Post) Fields() []string {
	return []string{"user_id", "title", "content", "status"}
}

func (p *Post) Values() []interface{} {
	return []interface{}{p.UserID, p.Title, p.Content, p.Status}
}

func (p *Post) Scan(rows *sql.Rows) error {
	return rows.Scan(&p.ID, &p.UserID, &p.Title, &p.Content, &p.Status, &p.CreatedAt, &p.UpdatedAt)
}

// ORM methods
func (orm *ORM) CreateTable(model Model) error {
	tableName := model.TableName()
	fields := model.Fields()

	// Build CREATE TABLE statement
	query := fmt.Sprintf("CREATE TABLE IF NOT EXISTS %s (", tableName)
	query += "id INTEGER PRIMARY KEY AUTOINCREMENT, "

	for i, field := range fields {
		query += field
		if field == "content" || field == "bio" {
			query += " TEXT"
		} else if field == "user_id" {
			query += " INTEGER"
		} else {
			query += " VARCHAR(255)"
		}

		if i < len(fields)-1 {
			query += ", "
		}
	}

	query += ", created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP)"

	_, err := orm.db.Exec(query)
	if err != nil {
		return fmt.Errorf("failed to create table %s: %w", tableName, err)
	}

	fmt.Printf("✓ Created table: %s\n", tableName)
	return nil
}

func (orm *ORM) Create(model Model) (int64, error) {
	tableName := model.TableName()
	fields := model.Fields()
	values := model.Values()

	// Build INSERT statement
	placeholders := make([]string, len(values))
	for i := range placeholders {
		placeholders[i] = "?"
	}

	query := fmt.Sprintf("INSERT INTO %s (%s) VALUES (%s)",
		tableName, 
		joinStrings(fields, ", "), 
		joinStrings(placeholders, ", "))

	result, err := orm.db.Exec(query, values...)
	if err != nil {
		return 0, fmt.Errorf("failed to insert into %s: %w", tableName, err)
	}

	id, err := result.LastInsertId()
	if err != nil {
		return 0, fmt.Errorf("failed to get last insert ID: %w", err)
	}

	return id, nil
}

func (orm *ORM) FindByID(model Model, id int64) (Model, error) {
	tableName := model.TableName()
	fields := model.Fields()

	query := fmt.Sprintf("SELECT id, %s, created_at, updated_at FROM %s WHERE id = ?",
		joinStrings(fields, ", "), tableName)

	rows, err := orm.db.Query(query, id)
	if err != nil {
		return nil, fmt.Errorf("failed to query %s: %w", tableName, err)
	}
	defer rows.Close()

	if rows.Next() {
		if err := model.Scan(rows); err != nil {
			return nil, fmt.Errorf("failed to scan %s: %w", tableName, err)
		}
		return model, nil
	}

	return nil, fmt.Errorf("%s with ID %d not found", tableName, id)
}

func (orm *ORM) FindAll(model Model) ([]Model, error) {
	tableName := model.TableName()
	fields := model.Fields()

	query := fmt.Sprintf("SELECT id, %s, created_at, updated_at FROM %s",
		joinStrings(fields, ", "), tableName)

	rows, err := orm.db.Query(query)
	if err != nil {
		return nil, fmt.Errorf("failed to query %s: %w", tableName, err)
	}
	defer rows.Close()

	var results []Model
	for rows.Next() {
		// Create new instance for each row
		newModel := createNewModel(model)
		if err := newModel.Scan(rows); err != nil {
			return nil, fmt.Errorf("failed to scan %s: %w", tableName, err)
		}
		results = append(results, newModel)
	}

	return results, nil
}

func (orm *ORM) Update(model Model, id int64) error {
	tableName := model.TableName()
	fields := model.Fields()
	values := model.Values()

	// Build UPDATE statement
	setClauses := make([]string, len(fields))
	for i, field := range fields {
		setClauses[i] = fmt.Sprintf("%s = ?", field)
	}

	query := fmt.Sprintf("UPDATE %s SET %s, updated_at = CURRENT_TIMESTAMP WHERE id = ?",
		tableName, joinStrings(setClauses, ", "))

	// Add ID to values for WHERE clause
	updateValues := append(values, id)

	result, err := orm.db.Exec(query, updateValues...)
	if err != nil {
		return fmt.Errorf("failed to update %s: %w", tableName, err)
	}

	rowsAffected, err := result.RowsAffected()
	if err != nil {
		return fmt.Errorf("failed to get rows affected: %w", err)
	}

	if rowsAffected == 0 {
		return fmt.Errorf("%s with ID %d not found", tableName, id)
	}

	return nil
}

func (orm *ORM) Delete(model Model, id int64) error {
	tableName := model.TableName()
	query := fmt.Sprintf("DELETE FROM %s WHERE id = ?", tableName)

	result, err := orm.db.Exec(query, id)
	if err != nil {
		return fmt.Errorf("failed to delete from %s: %w", tableName, err)
	}

	rowsAffected, err := result.RowsAffected()
	if err != nil {
		return fmt.Errorf("failed to get rows affected: %w", err)
	}

	if rowsAffected == 0 {
		return fmt.Errorf("%s with ID %d not found", tableName, id)
	}

	return nil
}

func (orm *ORM) NewQuery() *QueryBuilder {
	return &QueryBuilder{}
}

func (orm *ORM) Find(query *QueryBuilder, model Model) ([]Model, error) {
	tableName := model.TableName()
	fields := model.Fields()

	sql := fmt.Sprintf("SELECT id, %s, created_at, updated_at FROM %s",
		joinStrings(fields, ", "), tableName)

	// Add joins
	for _, join := range query.joins {
		sql += " " + join
	}

	// Add where conditions
	if len(query.where) > 0 {
		sql += " WHERE " + joinStrings(query.where, " AND ")
	}

	// Add order by
	if query.orderBy != "" {
		sql += " ORDER BY " + query.orderBy
	}

	// Add limit and offset
	if query.limit > 0 {
		sql += fmt.Sprintf(" LIMIT %d", query.limit)
		if query.offset > 0 {
			sql += fmt.Sprintf(" OFFSET %d", query.offset)
		}
	}

	rows, err := orm.db.Query(sql, query.params...)
	if err != nil {
		return nil, fmt.Errorf("failed to execute query: %w", err)
	}
	defer rows.Close()

	var results []Model
	for rows.Next() {
		newModel := createNewModel(model)
		if err := newModel.Scan(rows); err != nil {
			return nil, fmt.Errorf("failed to scan %s: %w", tableName, err)
		}
		results = append(results, newModel)
	}

	return results, nil
}

// QueryBuilder methods
func (qb *QueryBuilder) Where(condition string, params ...interface{}) *QueryBuilder {
	qb.where = append(qb.where, condition)
	qb.params = append(qb.params, params...)
	return qb
}

func (qb *QueryBuilder) OrderBy(orderBy string) *QueryBuilder {
	qb.orderBy = orderBy
	return qb
}

func (qb *QueryBuilder) Limit(limit int) *QueryBuilder {
	qb.limit = limit
	return qb
}

func (qb *QueryBuilder) Offset(offset int) *QueryBuilder {
	qb.offset = offset
	return qb
}

func (qb *QueryBuilder) Join(join string) *QueryBuilder {
	qb.joins = append(qb.joins, join)
	return qb
}

func demonstrateCRUD(orm *ORM) {
	fmt.Println("\n--- CRUD Operations ---")

	// CREATE
	user := &User{
		Username:  "john_doe",
		Email:     "john@example.com",
		FirstName: "John",
		LastName:  "Doe",
		Bio:       "Software developer passionate about Go",
	}

	id, err := orm.Create(user)
	if err != nil {
		log.Printf("Failed to create user: %v", err)
		return
	}
	fmt.Printf("✓ Created user with ID: %d\n", id)

	// READ
	retrievedUser, err := orm.FindByID(&User{}, id)
	if err != nil {
		log.Printf("Failed to find user: %v", err)
		return
	}
	
	if u, ok := retrievedUser.(*User); ok {
		fmt.Printf("✓ Retrieved user: %s (%s)\n", u.Username, u.Email)
	}

	// UPDATE
	user.FirstName = "John Updated"
	if err := orm.Update(user, id); err != nil {
		log.Printf("Failed to update user: %v", err)
		return
	}
	fmt.Printf("✓ Updated user ID: %d\n", id)

	// Create posts for the user
	posts := []*Post{
		{UserID: int(id), Title: "First Post", Content: "This is my first post", Status: "published"},
		{UserID: int(id), Title: "Second Post", Content: "This is my second post", Status: "draft"},
	}

	for _, post := range posts {
		postID, err := orm.Create(post)
		if err != nil {
			log.Printf("Failed to create post: %v", err)
			continue
		}
		fmt.Printf("✓ Created post with ID: %d\n", postID)
	}

	// DELETE (we'll keep the data for other demos)
	fmt.Println("✓ CRUD operations completed")
}

func demonstrateQueries(orm *ORM) {
	fmt.Println("\n--- Query Operations ---")

	// Find all users
	users, err := orm.FindAll(&User{})
	if err != nil {
		log.Printf("Failed to find all users: %v", err)
		return
	}
	fmt.Printf("Found %d users\n", len(users))

	// Custom query with conditions
	query := orm.NewQuery().Where("username LIKE ?", "%john%").OrderBy("created_at DESC")
	filteredUsers, err := orm.Find(query, &User{})
	if err != nil {
		log.Printf("Failed to execute custom query: %v", err)
		return
	}
	fmt.Printf("Found %d users matching 'john'\n", len(filteredUsers))

	// Query posts
	posts, err := orm.FindAll(&Post{})
	if err != nil {
		log.Printf("Failed to find all posts: %v", err)
		return
	}
	fmt.Printf("Found %d posts\n", len(posts))

	// Query published posts
	publishedQuery := orm.NewQuery().Where("status = ?", "published").OrderBy("created_at DESC")
	publishedPosts, err := orm.Find(publishedQuery, &Post{})
	if err != nil {
		log.Printf("Failed to find published posts: %v", err)
		return
	}
	fmt.Printf("Found %d published posts\n", len(publishedPosts))
}

func demonstrateRelationships(orm *ORM) {
	fmt.Println("\n--- Relationship Operations ---")

	// Get user with their posts
	users, err := orm.FindAll(&User{})
	if err != nil {
		log.Printf("Failed to get users: %v", err)
		return
	}

	for _, userModel := range users {
		user := userModel.(*User)
		fmt.Printf("User: %s\n", user.Username)

		// Get user's posts
		postQuery := orm.NewQuery().Where("user_id = ?", user.ID).OrderBy("created_at DESC")
		userPosts, err := orm.Find(postQuery, &Post{})
		if err != nil {
			log.Printf("Failed to get user posts: %v", err)
			continue
		}

		fmt.Printf("  Posts (%d):\n", len(userPosts))
		for _, postModel := range userPosts {
			post := postModel.(*Post)
			fmt.Printf("    - %s (%s)\n", post.Title, post.Status)
		}
	}
}

func demonstrateAdvancedOperations(orm *ORM) {
	fmt.Println("\n--- Advanced Operations ---")

	// Count operations
	demonstrateCountOperations(orm)

	// Pagination
	demonstratePagination(orm)

	// Aggregation
	demonstrateAggregation(orm)

	// Transactions
	demonstrateTransactions(orm)
}

func demonstrateCountOperations(orm *ORM) {
	fmt.Println("\n--- Count Operations ---")

	// Count users
	var userCount int
	err := orm.db.QueryRow("SELECT COUNT(*) FROM users").Scan(&userCount)
	if err != nil {
		log.Printf("Failed to count users: %v", err)
		return
	}
	fmt.Printf("Total users: %d\n", userCount)

	// Count posts by status
	statuses := []string{"published", "draft"}
	for _, status := range statuses {
		var count int
		err := orm.db.QueryRow("SELECT COUNT(*) FROM posts WHERE status = ?", status).Scan(&count)
		if err != nil {
			log.Printf("Failed to count posts with status %s: %v", status, err)
			continue
		}
		fmt.Printf("Posts with status '%s': %d\n", status, count)
	}
}

func demonstratePagination(orm *ORM) {
	fmt.Println("\n--- Pagination ---")

	page := 1
	limit := 2

	for {
		query := orm.NewQuery().Limit(limit).Offset((page - 1) * limit).OrderBy("created_at DESC")
		posts, err := orm.Find(query, &Post{})
		if err != nil {
			log.Printf("Failed to get page %d: %v", page, err)
			return
		}

		if len(posts) == 0 {
			break
		}

		fmt.Printf("Page %d (%d posts):\n", page, len(posts))
		for _, postModel := range posts {
			post := postModel.(*Post)
			fmt.Printf("  - %s\n", post.Title)
		}

		page++
		if page > 3 { // Limit pagination demo
			break
		}
	}
}

func demonstrateAggregation(orm *ORM) {
	fmt.Println("\n--- Aggregation Operations ---")

	// Posts per user
	query := `
	SELECT u.username, COUNT(p.id) as post_count
	FROM users u
	LEFT JOIN posts p ON u.id = p.user_id
	GROUP BY u.id, u.username
	ORDER BY post_count DESC`

	rows, err := orm.db.Query(query)
	if err != nil {
		log.Printf("Failed to execute aggregation: %v", err)
		return
	}
	defer rows.Close()

	fmt.Println("Posts per user:")
	for rows.Next() {
		var username string
		var postCount int
		if err := rows.Scan(&username, &postCount); err != nil {
			log.Printf("Failed to scan aggregation row: %v", err)
			continue
		}
		fmt.Printf("  - %s: %d posts\n", username, postCount)
	}
}

func demonstrateTransactions(orm *ORM) {
	fmt.Println("\n--- Transaction Operations ---")

	tx, err := orm.db.Begin()
	if err != nil {
		log.Printf("Failed to begin transaction: %v", err)
		return
	}

	// Create user and posts in transaction
	user := &User{
		Username:  "transaction_user",
		Email:     "tx@example.com",
		FirstName: "Transaction",
		LastName:  "User",
		Bio:       "Created in a transaction",
	}

	// Insert user
	userQuery := `INSERT INTO users (username, email, first_name, last_name, bio) VALUES (?, ?, ?, ?, ?)`
	result, err := tx.Exec(userQuery, user.Username, user.Email, user.FirstName, user.LastName, user.Bio)
	if err != nil {
		tx.Rollback()
		log.Printf("Failed to insert user in transaction: %v", err)
		return
	}

	userID, err := result.LastInsertId()
	if err != nil {
		tx.Rollback()
		log.Printf("Failed to get user ID in transaction: %v", err)
		return
	}

	// Insert posts for this user
	postQuery := `INSERT INTO posts (user_id, title, content, status) VALUES (?, ?, ?, ?)`
	posts := []struct {
		title   string
		content string
		status  string
	}{
		{"Transaction Post 1", "First post in transaction", "published"},
		{"Transaction Post 2", "Second post in transaction", "draft"},
	}

	for _, post := range posts {
		_, err := tx.Exec(postQuery, userID, post.title, post.content, post.status)
		if err != nil {
			tx.Rollback()
			log.Printf("Failed to insert post in transaction: %v", err)
			return
		}
	}

	// Commit transaction
	if err := tx.Commit(); err != nil {
		log.Printf("Failed to commit transaction: %v", err)
		return
	}

	fmt.Printf("✓ Transaction completed successfully (User ID: %d)\n", userID)

	// Verify the transaction
	userPosts, err := orm.NewQuery().Where("user_id = ?", userID).Find(&Post{})
	if err != nil {
		log.Printf("Failed to verify transaction: %v", err)
		return
	}

	fmt.Printf("✓ Verified: User has %d posts\n", len(userPosts))
}

// Helper functions
func joinStrings(strs []string, sep string) string {
	if len(strs) == 0 {
		return ""
	}
	
	result := strs[0]
	for i := 1; i < len(strs); i++ {
		result += sep + strs[i]
	}
	return result
}

func createNewModel(model Model) Model {
	switch model.(type) {
	case *User:
		return &User{}
	case *Post:
		return &Post{}
	default:
		return nil
	}
}

func cleanupDatabase(dbPath string) {
	if err := os.Remove(dbPath); err != nil && !os.IsNotExist(err) {
		log.Printf("Warning: failed to remove database file: %v", err)
	}
}
