# Web Development in Go

This directory contains comprehensive examples of web development in Go, covering HTTP servers, REST APIs, database integration, and modern web development practices.

## Files

- **main.go** - Basic web development concepts and HTTP server setup
- **rest-api.go** - REST API development with best practices
- **database-integration.go** - Database integration and management
- **README.md** - This file

## Overview

This section covers:

- **HTTP Servers** - Building web servers with the net/http package
- **REST APIs** - Designing and implementing RESTful APIs
- **Database Integration** - Working with SQL and NoSQL databases
- **Middleware** - Request processing middleware
- **Authentication** - JWT, OAuth, and other auth methods
- **Error Handling** - Proper error handling in web applications
- **Testing** - Testing web applications and APIs
- **Deployment** - Deploying Go web applications

## Key Features Demonstrated

### HTTP Server Development
- Basic HTTP server setup
- Request routing and handling
- Middleware implementation
- Static file serving
- Template rendering

### REST API Design
- RESTful endpoint design
- HTTP status codes
- Request validation
- Error handling
- Pagination and filtering
- API versioning

### Database Integration
- Connection management
- CRUD operations
- Transactions
- Prepared statements
- Connection pooling
- Database migrations

## Usage Examples

### Basic HTTP Server
```go
package main

import (
    "fmt"
    "net/http"
)

func main() {
    http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
        fmt.Fprintf(w, "Hello, World!")
    })
    
    http.HandleFunc("/about", func(w http.ResponseWriter, r *http.Request) {
        fmt.Fprintf(w, "About Page")
    })
    
    fmt.Println("Server starting on :8080")
    http.ListenAndServe(":8080", nil)
}
```

### REST API Endpoint
```go
// GET /users
func getUsers(w http.ResponseWriter, r *http.Request) {
    users := []User{
        {ID: 1, Name: "John", Email: "john@example.com"},
        {ID: 2, Name: "Jane", Email: "jane@example.com"},
    }
    
    w.Header().Set("Content-Type", "application/json")
    json.NewEncoder(w).Encode(users)
}

// POST /users
func createUser(w http.ResponseWriter, r *http.Request) {
    var user User
    if err := json.NewDecoder(r.Body).Decode(&user); err != nil {
        http.Error(w, err.Error(), http.StatusBadRequest)
        return
    }
    
    // Save user to database
    // ...
    
    w.Header().Set("Content-Type", "application/json")
    w.WriteHeader(http.StatusCreated)
    json.NewEncoder(w).Encode(user)
}
```

### Database Integration
```go
func setupDatabase() *sql.DB {
    db, err := sql.Open("postgres", "host=localhost port=5432 user=postgres dbname=myapp sslmode=disable")
    if err != nil {
        log.Fatal(err)
    }
    
    if err := db.Ping(); err != nil {
        log.Fatal(err)
    }
    
    return db
}

func getUser(db *sql.DB, id int) (*User, error)    {
    var user User
    err := db.QueryRow("SELECT id, name, email FROM users WHERE id = $1", id).Scan(&user.ID, &user.Name, &user.Email)
    if err != nil {
        return nil, err
    }
    
    return &user, nil
}
```

## HTTP Server Basics

### Server Setup
```go
func main() {
    // Create a new server
    server := &http.Server{
        Addr:         ":8080",
        Handler:      router(),
        ReadTimeout:  10 * time.Second,
        WriteTimeout: 10 * time.Second,
        IdleTimeout:  120 * time.Second,
    }
    
    log.Fatal(server.ListenAndServe())
}
```

### Request Handling
```go
func handler(w http.ResponseWriter, r *http.Request) {
    switch r.Method {
    case http.MethodGet:
        handleGet(w, r)
    case http.MethodPost:
        handlePost(w, r)
    default:
        http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
    }
}
```

### Middleware
```go
func loggingMiddleware(next http.Handler) http.Handler {
    return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
        log.Printf("%s %s", r.Method, r.URL.Path)
        next.ServeHTTP(w, r)
    })
}

func corsMiddleware(next http.Handler) http.Handler {
    return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
        w.Header().Set("Access-Control-Allow-Origin", "*")
        w.Header().Set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE")
        w.Header().Set("Access-Control-Allow-Headers", "Content-Type")
        next.ServeHTTP(w, r)
    })
}
```

## REST API Development

### API Design Principles
- Use HTTP verbs correctly (GET, POST, PUT, DELETE)
- Use proper HTTP status codes
- Implement proper error handling
- Use JSON for data exchange
- Implement authentication and authorization
- Use pagination for large datasets
- Implement rate limiting

### Endpoint Examples
```
GET    /users        - List all users
GET    /users/{id}   - Get specific user
POST   /users        - Create new user
PUT    /users/{id}   - Update user
DELETE /users/{id}   - Delete user
```

### Request Validation
```go
func validateUser(user *User) error {
    if user.Name == "" {
        return errors.New("name is required")
    }
    
    if user.Email == "" {
        return errors.New("email is required")
    }
    
    if !isValidEmail(user.Email) {
        return errors.New("invalid email format")
    }
    
    return nil
}
```

### Error Handling
```go
type ErrorResponse struct {
    Error   string `json:"error"`
    Message string `json:"message"`
    Code    int    `json:"code"`
}

func sendError(w http.ResponseWriter, status int, message string) {
    w.Header().Set("Content-Type", "application/json")
    w.WriteHeader(status)
    
    errorResp := ErrorResponse{
        Error:   http.StatusText(status),
        Message: message,
        Code:    status,
    }
    
    json.NewEncoder(w).Encode(errorResp)
}
```

## Database Integration

### Supported Databases
- **PostgreSQL** - github.com/lib/pq
- **MySQL** - github.com/go-sql-driver/mysql
- **SQLite** - github.com/mattn/go-sqlite3
- **SQL Server** - github.com/denisenkom/go-mssqldb
- **Oracle** - github.com/sijms/go-ora

### Connection Setup
```go
func connectPostgreSQL() (*sql.DB, error) {
    connStr := "host=localhost port=5432 user=postgres dbname=myapp sslmode=disable"
    db, err := sql.Open("postgres", connStr)
    if err != nil {
        return nil, err
    }
    
    if err := db.Ping(); err != nil {
        return nil, err
    }
    
    return db, nil
}
```

### CRUD Operations
```go
// Create
func createUser(db *sql.DB, user *User) error {
    query := `INSERT INTO users (name, email) VALUES ($1, $2)`
    _, err := db.Exec(query, user.Name, user.Email)
    return err
}

// Read
func getUser(db *sql.DB, id int) (*User, error) {
    query := `SELECT id, name, email FROM users WHERE id = $1`
    var user User
    err := db.QueryRow(query, id).Scan(&user.ID, &user.Name, &user.Email)
    return &user, err
}

// Update
func updateUser(db *sql.DB, user *User) error {
    query := `UPDATE users SET name = $1, email = $2 WHERE id = $3`
    _, err := db.Exec(query, user.Name, user.Email, user.ID)
    return err
}

// Delete
func deleteUser(db *sql.DB, id int) error {
    query := `DELETE FROM users WHERE id = $1`
    _, err := db.Exec(query, id)
    return err
}
```

### Transactions
```go
func transferFunds(db *sql.DB, from, to int, amount float64) error {
    tx, err := db.Begin()
    if err != nil {
        return err
    }
    defer tx.Rollback()
    
    // Debit from account
    _, err = tx.Exec("UPDATE accounts SET balance = balance - $1 WHERE id = $2", amount, from)
    if err != nil {
        return err
    }
    
    // Credit to account
    _, err = tx.Exec("UPDATE accounts SET balance = balance + $1 WHERE id = $2", amount, to)
    if err != nil {
        return err
    }
    
    return tx.Commit()
}
```

### Connection Pooling
```go
func configurePool(db *sql.DB) {
    db.SetMaxOpenConns(25)                // Maximum open connections
    db.SetMaxIdleConns(25)                // Maximum idle connections
    db.SetConnMaxLifetime(5 * time.Minute) // Maximum connection lifetime
    db.SetConnMaxIdleTime(5 * time.Minute)  // Maximum idle time
}
```

## Advanced Topics

### Authentication
```go
func jwtMiddleware(next http.Handler) http.Handler {
    return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
        authHeader := r.Header.Get("Authorization")
        if authHeader == "" {
            http.Error(w, "Authorization required", http.StatusUnauthorized)
            return
        }
        
        token := strings.TrimPrefix(authHeader, "Bearer ")
        if !validateJWT(token) {
            http.Error(w, "Invalid token", http.StatusUnauthorized)
            return
        }
        
        next.ServeHTTP(w, r)
    })
}
```

### Rate Limiting
```go
type RateLimiter struct {
    requests map[string][]time.Time
    limit    int
    window   time.Duration
    mutex    sync.Mutex
}

func (rl *RateLimiter) Allow(key string) bool {
    rl.mutex.Lock()
    defer rl.mutex.Unlock()
    
    now := time.Now()
    
    // Clean old requests
    if requests, exists := rl.requests[key]; exists {
        var validRequests []time.Time
        for _, req := range requests {
            if now.Sub(req) < rl.window {
                validRequests = append(validRequests, req)
            }
        }
        rl.requests[key] = validRequests
    }
    
    // Check limit
    if len(rl.requests[key]) >= rl.limit {
        return false
    }
    
    // Add request
    rl.requests[key] = append(rl.requests[key], now)
    return true
}
```

### WebSockets
```go
func websocketHandler(w http.ResponseWriter, r *http.Request) {
    upgrader := websocket.Upgrader{
        CheckOrigin: func(r *http.Request) bool {
            return true
        },
    }
    
    conn, err := upgrader.Upgrade(w, r, nil)
    if err != nil {
        log.Printf("WebSocket upgrade failed: %v", err)
        return
    }
    defer conn.Close()
    
    for {
        messageType, message, err := conn.ReadMessage()
        if err != nil {
            log.Printf("Read error: %v", err)
            break
        }
        
        log.Printf("Received: %s", message)
        
        err = conn.WriteMessage(messageType, message)
        if err != nil {
            log.Printf("Write error: %v", err)
            break
        }
    }
}
```

## Testing

### Unit Testing
```go
func TestGetUser(t *testing.T) {
    // Setup test database
    db := setupTestDB()
    defer db.Close()
    
    // Insert test data
    _, err := db.Exec("INSERT INTO users (name, email) VALUES ($1, $2)", "Test User", "test@example.com")
    if err != nil {
        t.Fatal(err)
    }
    
    // Test getUser function
    user, err := getUser(db, 1)
    if err != nil {
        t.Fatal(err)
    }
    
    if user.Name != "Test User" {
        t.Errorf("Expected name 'Test User', got '%s'", user.Name)
    }
}
```

### Integration Testing
```go
func TestAPIEndpoints(t *testing.T) {
    // Setup test server
    router := setupRouter()
    server := httptest.NewServer(router)
    defer server.Close()
    
    // Test GET /users
    resp, err := http.Get(server.URL + "/users")
    if err != nil {
        t.Fatal(err)
    }
    defer resp.Body.Close()
    
    if resp.StatusCode != http.StatusOK {
        t.Errorf("Expected status 200, got %d", resp.StatusCode)
    }
    
    // Parse response
    var users []User
    if err := json.NewDecoder(resp.Body).Decode(&users); err != nil {
        t.Fatal(err)
    }
    
    if len(users) == 0 {
        t.Error("Expected at least one user")
    }
}
```

## Deployment

### Docker
```dockerfile
FROM golang:1.21-alpine AS builder

WORKDIR /app
COPY go.mod go.sum ./
RUN go mod download

COPY . .
RUN CGO_ENABLED=0 GOOS=linux go build -o server

FROM alpine:latest
RUN apk --no-cache add ca-certificates
WORKDIR /root/

COPY --from=builder /app/server .
EXPOSE 8080

CMD ["./server"]
```

### Kubernetes
```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: go-web-app
spec:
  replicas: 3
  selector:
    matchLabels:
      app: go-web-app
  template:
    metadata:
      labels:
        app: go-web-app
    spec:
      containers:
      - name: go-web-app
        image: go-web-app:latest
        ports:
        - containerPort: 8080
        env:
        - name: DB_HOST
          value: "postgres-service"
```

## Performance Optimization

### Connection Pooling
- Configure appropriate pool sizes
- Monitor pool statistics
- Handle connection exhaustion
- Use connection timeouts

### Query Optimization
- Use prepared statements
- Implement proper indexing
- Use query caching
- Monitor slow queries

### Caching
- Implement Redis caching
- Use in-memory caching
- Cache frequently accessed data
- Implement cache invalidation

## Security Best Practices

### Input Validation
- Validate all user input
- Use parameterized queries
- Implement proper error handling
- Sanitize output

### Authentication & Authorization
- Use strong authentication
- Implement proper authorization
- Use HTTPS everywhere
- Implement rate limiting

### Data Protection
- Encrypt sensitive data
- Use secure passwords
- Implement proper logging
- Regular security updates

## Common Patterns

### Repository Pattern
```go
type UserRepository interface {
    Create(user *User) error
    GetByID(id int) (*User, error)
    Update(user *User) error
    Delete(id int) error
}

type userRepository struct {
    db *sql.DB
}

func (r *userRepository) Create(user *User) error {
    query := `INSERT INTO users (name, email) VALUES ($1, $2)`
    _, err := r.db.Exec(query, user.Name, user.Email)
    return err
}
```

### Service Layer
```go
type UserService struct {
    repo UserRepository
    cache Cache
}

func (s *UserService) CreateUser(user *User) error {
    if err := validateUser(user); err != nil {
        return err
    }
    
    if err := s.repo.Create(user); err != nil {
        return err
    }
    
    s.cache.Set(fmt.Sprintf("user:%d", user.ID), user)
    return nil
}
```

### Dependency Injection
```go
type Server struct {
    userService *UserService
    db          *sql.DB
}

func NewServer(db *sql.DB) *Server {
    userRepo := &userRepository{db: db}
    userService := &UserService{repo: userRepo}
    
    return &Server{
        userService: userService,
        db:          db,
    }
}
```

## Troubleshooting

### Common Issues
1. **Connection Refused** - Database not running
2. **Timeout Errors** - Network or database issues
3. **Memory Leaks** - Unclosed connections
4. **SQL Injection** - Unparameterized queries
5. **CORS Issues** - Missing CORS headers
6. **Authentication Failures** - Invalid tokens

### Debugging Tips
- Enable detailed logging
- Use database query logging
- Monitor connection pool statistics
- Use HTTP request tracing
- Implement health checks

## Best Practices

### Code Organization
- Separate concerns (handlers, services, repositories)
- Use interfaces for dependency injection
- Keep handlers thin
- Implement proper error handling
- Write comprehensive tests

### Performance
- Use connection pooling
- Implement caching
- Optimize database queries
- Use appropriate data structures
- Monitor performance metrics

### Security
- Validate all inputs
- Use HTTPS
- Implement proper authentication
- Use parameterized queries
- Regular security audits

## Related Packages

### Web Frameworks
- **Gin** - github.com/gin-gonic/gin
- **Echo** - github.com/labstack/echo
- **Fiber** - github.com/gofiber/fiber
- **Chi** - github.com/go-chi/chi

### Database Tools
- **GORM** - github.com/go-gorm/gorm
- **Ent** - entgo.io/ent
- **SQLBoiler** - github.com/volatiletech/sql-boiler

### Authentication
- **JWT** - github.com/golang-jwt/jwt
- **OAuth2** - golang.org/x/oauth2
- **Auth0** - github.com/auth0/go-auth0

### Testing
- **Testify** - github.com/stretchr/testify
- **HTTP Test** - net/http/httptest
- **SQL Mock** - github.com/DATA-DOG/go-sqlmock

## Learning Path

1. **Basic HTTP Server** - Learn net/http package
2. **Request Handling** - Routes, handlers, middleware
3. **REST API Design** - REST principles, HTTP methods
4. **Database Integration** - SQL database connections
5. **Authentication** - JWT, OAuth, sessions
6. **Testing** - Unit tests, integration tests
7. **Deployment** - Docker, Kubernetes
8. **Performance** - Optimization, monitoring
9. **Security** - Best practices, common vulnerabilities

## Resources

### Documentation
- [Go Web Programming](https://github.com/astaxie/build-web-application-with-golang)
- [Go Net/HTTP](https://golang.org/pkg/net/http/)
- [Go Database/SQL](https://golang.org/pkg/database/sql/)

### Tutorials
- [Let's Go!](https://alexedwards.net/)
- [Go Web Examples](https://github.com/gowebexamples/gowebexamples)
- [Go by Example](https://gobyexample.com/)

### Tools
- [Postman](https://www.postman.com/) - API testing
- [Docker](https://www.docker.com/) - Containerization
- [Kubernetes](https://kubernetes.io/) - Orchestration

## Examples

The web development directory includes comprehensive examples covering:

- **HTTP Servers** - Basic to advanced server implementations
- **REST APIs** - Complete API with CRUD operations
- **Database Integration** - Multiple database examples
- **Middleware** - Authentication, logging, CORS
- **Error Handling** - Proper error responses
- **Testing** - Unit and integration tests
- **Deployment** - Docker and Kubernetes examples

Each example is fully functional and demonstrates best practices for production-ready web applications in Go.
