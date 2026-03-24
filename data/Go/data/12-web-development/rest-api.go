package main

import (
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"strconv"
	"strings"
	"sync"
	"time"
)

func main() {
	fmt.Println("=== REST API Development in Go ===")
	
	// Basic REST API structure
	fmt.Println("\n--- Basic REST API Structure ---")
	basicRESTAPI()
	
	// HTTP status codes
	fmt.Println("\n--- HTTP Status Codes ---")
	httpStatusCodes()
	
	// Request validation
	fmt.Println("\n--- Request Validation ---")
	requestValidation()
	
	// Error handling
	fmt.Println("\n--- Error Handling ---")
	errorHandling()
	
	// Pagination
	fmt.Println("\n--- Pagination ---")
	pagination()
	
	// Filtering and sorting
	fmt.Println("\n--- Filtering and Sorting ---")
	filteringAndSorting()
	
	// Versioning
	fmt.Println("\n--- API Versioning ---")
	apiVersioning()
	
	// Rate limiting
	fmt.Println("\n--- Rate Limiting ---")
	rateLimiting()
	
	// Authentication
	fmt.Println("\n--- Authentication ---")
	authentication()
	
	// CORS handling
	fmt.Println("\n--- CORS Handling ---")
	corsHandling()
	
	// Documentation
	fmt.Println("\n--- API Documentation ---")
	apiDocumentation()
}

// Basic REST API structure
func basicRESTAPI() {
	fmt.Println("REST API Endpoints:")
	
	// User model
	type User struct {
		ID        int       `json:"id"`
		Name      string    `json:"name"`
		Email     string    `json:"email"`
		CreatedAt time.Time `json:"created_at"`
		UpdatedAt time.Time `json:"updated_at"`
	}
	
	// In-memory storage (in production, use database)
	var users []User
	var usersMutex sync.Mutex
	
	// GET /users - List all users
	listUsers := func(w http.ResponseWriter, r *http.Request) {
		usersMutex.Lock()
		defer usersMutex.Unlock()
		
		w.Header().Set("Content-Type", "application/json")
		
		response := map[string]interface{}{
			"users": users,
			"count": len(users),
		}
		
		json.NewEncoder(w).Encode(response)
	}
	
	// GET /users/{id} - Get specific user
	getUser := func(w http.ResponseWriter, r *http.Request) {
		usersMutex.Lock()
		defer usersMutex.Unlock()
		
		// Extract ID from URL
		path := strings.TrimPrefix(r.URL.Path, "/users/")
		id, err := strconv.Atoi(path)
		if err != nil {
			http.Error(w, "Invalid user ID", http.StatusBadRequest)
			return
		}
		
		// Find user
		for _, user := range users {
			if user.ID == id {
				w.Header().Set("Content-Type", "application/json")
				json.NewEncoder(w).Encode(user)
				return
			}
		}
		
		http.Error(w, "User not found", http.StatusNotFound)
	}
	
	// POST /users - Create user
	createUser := func(w http.ResponseWriter, r *http.Request) {
		usersMutex.Lock()
		defer usersMutex.Unlock()
		
		var user User
		if err := json.NewDecoder(r.Body).Decode(&user); err != nil {
			http.Error(w, "Invalid JSON", http.StatusBadRequest)
			return
		}
		
		// Validate user
		if user.Name == "" || user.Email == "" {
			http.Error(w, "Name and email are required", http.StatusBadRequest)
			return
		}
		
		// Generate ID
		user.ID = len(users) + 1
		user.CreatedAt = time.Now()
		user.UpdatedAt = time.Now()
		
		// Add user
		users = append(users, user)
		
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusCreated)
		json.NewEncoder(w).Encode(user)
	}
	
	// PUT /users/{id} - Update user
	updateUser := func(w http.ResponseWriter, r *http.Request) {
		usersMutex.Lock()
		defer usersMutex.Unlock()
		
		// Extract ID from URL
		path := strings.TrimPrefix(r.URL.Path, "/users/")
		id, err := strconv.Atoi(path)
		if err != nil {
			http.Error(w, "Invalid user ID", http.StatusBadRequest)
			return
		}
		
		// Find user index
		userIndex := -1
		for i, user := range users {
			if user.ID == id {
				userIndex = i
				break
			}
		}
		
		if userIndex == -1 {
			http.Error(w, "User not found", http.StatusNotFound)
			return
		}
		
		// Parse update data
		var updates User
		if err := json.NewDecoder(r.Body).Decode(&updates); err != nil {
			http.Error(w, "Invalid JSON", http.StatusBadRequest)
			return
		}
		
		// Update user
		if updates.Name != "" {
			users[userIndex].Name = updates.Name
		}
		if updates.Email != "" {
			users[userIndex].Email = updates.Email
		}
		users[userIndex].UpdatedAt = time.Now()
		
		w.Header().Set("Content-Type", "application/json")
		json.NewEncoder(w).Encode(users[userIndex])
	}
	
	// DELETE /users/{id} - Delete user
	deleteUser := func(w http.ResponseWriter, r *http.Request) {
		usersMutex.Lock()
		defer usersMutex.Unlock()
		
		// Extract ID from URL
		path := strings.TrimPrefix(r.URL.Path, "/users/")
		id, err := strconv.Atoi(path)
		if err != nil {
			http.Error(w, "Invalid user ID", http.StatusBadRequest)
			return
		}
		
		// Find user index
		userIndex := -1
		for i, user := range users {
			if user.ID == id {
				userIndex = i
				break
			}
		}
		
		if userIndex == -1 {
			http.Error(w, "User not found", http.StatusNotFound)
			return
		}
		
		// Remove user
		users = append(users[:userIndex], users[userIndex+1:]...)
		
		w.Header().Set("Content-Type", "application/json")
		json.NewEncoder(w).Encode(map[string]bool{"deleted": true})
	}
	
	fmt.Println("  - GET /users - List all users")
	fmt.Println("  - GET /users/{id} - Get specific user")
	fmt.Println("  - POST /users - Create user")
	fmt.Println("  - PUT /users/{id} - Update user")
	fmt.Println("  - DELETE /users/{id} - Delete user")
	
	// Register handlers
	http.HandleFunc("/users", listUsers)
	http.HandleFunc("/users/", getUser)
	http.HandleFunc("/users", createUser)
	http.HandleFunc("/users/", updateUser)
	http.HandleFunc("/users/", deleteUser)
}

// HTTP status codes
func httpStatusCodes() {
	fmt.Println("Common HTTP Status Codes:")
	
	statusCodes := map[int]string{
		200: "OK - Request successful",
		201: "Created - Resource created successfully",
		204: "No Content - Request successful, no content to return",
		400: "Bad Request - Invalid request",
		401: "Unauthorized - Authentication required",
		403: "Forbidden - Access denied",
		404: "Not Found - Resource not found",
		405: "Method Not Allowed - HTTP method not supported",
		409: "Conflict - Resource conflict",
		422: "Unprocessable Entity - Invalid data format",
		429: "Too Many Requests - Rate limit exceeded",
		500: "Internal Server Error - Server error",
		502: "Bad Gateway - Gateway error",
		503: "Service Unavailable - Service temporarily unavailable",
	}
	
	for code, description := range statusCodes {
		fmt.Printf("  %d - %s\n", code, description)
	}
}

// Request validation
func requestValidation() {
	fmt.Println("Request Validation Examples:")
	
	// Validate JSON body
	validateJSON := func(r *http.Request) error {
		var data map[string]interface{}
		if err := json.NewDecoder(r.Body).Decode(&data); err != nil {
			return fmt.Errorf("invalid JSON: %w", err)
		}
		
		// Validate required fields
		if _, exists := data["name"]; !exists {
			return fmt.Errorf("name field is required")
		}
		
		if _, exists := data["email"]; !exists {
			return fmt.Errorf("email field is required")
		}
		
		return nil
	}
	
	// Validate query parameters
	validateQuery := func(r *http.Request) error {
		query := r.URL.Query()
		
		// Validate limit
		limit := query.Get("limit")
		if limit != "" {
			if limitInt, err := strconv.Atoi(limit); err != nil || limitInt < 1 || limitInt > 100 {
				return fmt.Errorf("invalid limit parameter")
			}
		}
		
		// Validate page
		page := query.Get("page")
		if page != "" {
			if pageInt, err := strconv.Atoi(page); err != nil || pageInt < 1 {
				return fmt.Errorf("invalid page parameter")
			}
		}
		
		return nil
	}
	
	// Validate path parameters
	validatePath := func(r *http.Request) error {
		path := strings.Trim(r.URL.Path, "/")
		parts := strings.Split(path, "/")
		
		if len(parts) == 0 {
			return fmt.Errorf("empty path")
		}
		
		// Validate ID parameter
		if len(parts) > 1 {
			if _, err := strconv.Atoi(parts[1]); err != nil {
				return fmt.Errorf("invalid ID parameter")
			}
		}
		
		return nil
	}
	
	fmt.Println("  - JSON body validation")
	fmt.Println("  - Query parameter validation")
	fmt.Println("  - Path parameter validation")
	
	// Validation middleware
	validationMiddleware := func(next http.HandlerFunc) http.HandlerFunc {
		return func(w http.ResponseWriter, r *http.Request) {
			// Validate request
			if err := validateJSON(r); err != nil {
				http.Error(w, err.Error(), http.StatusBadRequest)
				return
			}
			
			if err := validateQuery(r); err != nil {
				http.Error(w, err.Error(), http.StatusBadRequest)
				return
			}
			
			if err := validatePath(r); err != nil {
				http.Error(w, err.Error(), http.StatusBadRequest)
				return
			}
			
			next(w, r)
		}
	}
	
	fmt.Println("  - Validation middleware")
	_ = validationMiddleware
}

// Error handling
func errorHandling() {
	fmt.Println("Error Handling Examples:")
	
	// Error response structure
	type ErrorResponse struct {
		Error   string `json:"error"`
		Message string `json:"message"`
		Code    int    `json:"code"`
		Details string `json:"details,omitempty"`
	}
	
	// Error response helper
	sendError := func(w http.ResponseWriter, status int, message string, details string) {
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(status)
		
		errorResp := ErrorResponse{
			Error:   http.StatusText(status),
			Message: message,
			Code:    status,
			Details: details,
		}
		
		json.NewEncoder(w).Encode(errorResp)
	}
	
	// Common error responses
	notFound := func(w http.ResponseWriter, resource string) {
		sendError(w, http.StatusNotFound, fmt.Sprintf("%s not found", resource), "")
	}
	
	badRequest := func(w http.ResponseWriter, message string) {
		sendError(w, http.StatusBadRequest, message, "")
	}
	
	unauthorized := func(w http.ResponseWriter, message string) {
		sendError(w, http.StatusUnauthorized, message, "Authentication required")
	}
	
	forbidden := func(w http.ResponseWriter, message string) {
		sendError(w, http.StatusForbidden, message, "Access denied")
	}
	
	internalServerError := func(w http.ResponseWriter, message string) {
		sendError(w, http.StatusInternalServerError, message, "Internal server error")
	}
	
	fmt.Println("  - Structured error responses")
	fmt.Println("  - Common error handlers")
	fmt.Println("  - HTTP status code mapping")
	
	// Error handling middleware
	errorHandlingMiddleware := func(next http.HandlerFunc) http.HandlerFunc {
		return func(w http.ResponseWriter, r *http.Request) {
			defer func() {
				if err := recover(); err != nil {
					log.Printf("Panic recovered: %v", err)
					internalServerError(w, "Internal server error")
				}
			}()
			
			next(w, r)
		}
	}
	
	fmt.Println("  - Panic recovery middleware")
	_ = errorHandlingMiddleware
	
	// Example error handlers
	_ = notFound
	_ = badRequest
	_ = unauthorized
	_ = forbidden
	_ = internalServerError
}

// Pagination
func pagination() {
	fmt.Println("Pagination Examples:")
	
	// Pagination parameters
	type PaginationParams struct {
		Page  int `json:"page"`
		Limit int `json:"limit"`
		Total int `json:"total"`
	}
	
	// Pagination response
	type PaginatedResponse struct {
		Data       interface{}       `json:"data"`
		Pagination PaginationParams `json:"pagination"`
	}
	
	// Parse pagination from query
	parsePagination := func(r *http.Request) PaginationParams {
		query := r.URL.Query()
		
		page := 1
		if pageStr := query.Get("page"); pageStr != "" {
			if pageInt, err := strconv.Atoi(pageStr); err == nil && pageInt > 0 {
				page = pageInt
			}
		}
		
		limit := 10
		if limitStr := query.Get("limit"); limitStr != "" {
			if limitInt, err := strconv.Atoi(limitStr); err == nil && limitInt > 0 && limitInt <= 100 {
				limit = limitInt
			}
		}
		
		return PaginationParams{
			Page:  page,
			Limit: limit,
		}
	}
	
	// Apply pagination to slice
	paginateSlice := func(data interface{}, page, limit int) (interface{}, int) {
		// In a real app, use reflection or generics
		// For simplicity, we'll use a slice of strings
		if items, ok := data.([]string); ok {
			total := len(items)
			start := (page - 1) * limit
			end := start + limit
			
			if start >= total {
				return []string{}, total
			}
			
			if end > total {
				end = total
			}
			
			return items[start:end], total
		}
		
		return data, 0
	}
	
	// Paginated response handler
	paginatedHandler := func(w http.ResponseWriter, r *http.Request) {
		pagination := parsePagination(r)
		
		// Sample data
		items := make([]string, 100)
		for i := 0; i < 100; i++ {
			items[i] = fmt.Sprintf("Item %d", i+1)
		}
		
		paginatedItems, total := paginateSlice(items, pagination.Page, pagination.Limit)
		pagination.Total = total
		
		response := PaginatedResponse{
			Data:       paginatedItems,
			Pagination: pagination,
		}
		
		w.Header().Set("Content-Type", "application/json")
		json.NewEncoder(w).Encode(response)
	}
	
	fmt.Println("  - Parse pagination parameters")
	fmt.Println("  - Apply pagination to data")
	fmt.Println("  - Paginated response structure")
	
	_ = paginatedHandler
}

// Filtering and sorting
func filteringAndSorting() {
	fmt.Println("Filtering and Sorting Examples:")
	
	// Filter parameters
	type FilterParams struct {
		Name   string `json:"name"`
		Status string `json:"status"`
		Date   string `json:"date"`
	}
	
	// Sort parameters
	type SortParams struct {
		Field string `json:"field"`
		Order string `json:"order"` // asc or desc
	}
	
	// Parse filters from query
	parseFilters := func(r *http.Request) FilterParams {
		query := r.URL.Query()
		
		return FilterParams{
			Name:   query.Get("name"),
			Status: query.Get("status"),
			Date:   query.Get("date"),
		}
	}
	
	// Parse sort from query
	parseSort := func(r *http.Request) SortParams {
		query := r.URL.Query()
		
		sort := SortParams{
			Field: query.Get("sort"),
			Order: query.Get("order"),
		}
		
		// Default sort
		if sort.Field == "" {
			sort.Field = "id"
		}
		if sort.Order == "" {
			sort.Order = "asc"
		}
		
		return sort
	}
	
	// Apply filters to slice
	applyFilters := func(data []map[string]interface{}, filters FilterParams) []map[string]interface{} {
		var filtered []map[string]interface{}
		
		for _, item := range data {
			// Filter by name
			if filters.Name != "" {
				if name, ok := item["name"].(string); ok {
					if !strings.Contains(strings.ToLower(name), strings.ToLower(filters.Name)) {
						continue
					}
				}
			}
			
			// Filter by status
			if filters.Status != "" {
				if status, ok := item["status"].(string); ok {
					if status != filters.Status {
						continue
					}
				}
			}
			
			// Filter by date
			if filters.Date != "" {
				if date, ok := item["date"].(string); ok {
					if !strings.Contains(date, filters.Date) {
						continue
					}
				}
			}
			
			filtered = append(filtered, item)
		}
		
		return filtered
	}
	
	// Apply sort to slice
	applySort := func(data []map[string]interface{}, sort SortParams) []map[string]interface{} {
		// In a real app, implement proper sorting
		// For simplicity, we'll just return the data
		return data
	}
	
	// Filtered and sorted handler
	filteredHandler := func(w http.ResponseWriter, r *http.Request) {
		filters := parseFilters(r)
		sort := parseSort(r)
		
		// Sample data
		data := []map[string]interface{}{
			{"id": 1, "name": "John Doe", "status": "active", "date": "2024-01-01"},
			{"id": 2, "name": "Jane Smith", "status": "inactive", "date": "2024-01-02"},
			{"id": 3, "name": "Bob Johnson", "status": "active", "date": "2024-01-03"},
		}
		
		// Apply filters and sort
		filtered := applyFilters(data, filters)
		sorted := applySort(filtered, sort)
		
		response := map[string]interface{}{
			"data":    sorted,
			"filters": filters,
			"sort":    sort,
		}
		
		w.Header().Set("Content-Type", "application/json")
		json.NewEncoder(w).Encode(response)
	}
	
	fmt.Println("  - Parse filter parameters")
	fmt.Println("  - Parse sort parameters")
	fmt.Println("  - Apply filters to data")
	fmt.Println("  - Apply sort to data")
	
	_ = filteredHandler
}

// API versioning
func apiVersioning() {
	fmt.Println("API Versioning Examples:")
	
	// Version middleware
	versionMiddleware := func(version string, handler http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			// Set version header
			w.Header().Set("API-Version", version)
			
			// Check for version in URL
			if strings.HasPrefix(r.URL.Path, "/api/v1") {
				// Handle v1
				handler.ServeHTTP(w, r)
			} else if strings.HasPrefix(r.URL.Path, "/api/v2") {
				// Handle v2
				handler.ServeHTTP(w, r)
			} else {
				// Default to v1
				handler.ServeHTTP(w, r)
			}
		})
	}
	
	// Version in header
	headerVersionMiddleware := func(handler http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			version := r.Header.Get("API-Version")
			if version == "" {
				version = "v1" // Default version
			}
			
			w.Header().Set("API-Version", version)
			handler.ServeHTTP(w, r)
		})
	}
	
	// Version in query parameter
	queryVersionMiddleware := func(handler http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			query := r.URL.Query()
			version := query.Get("version")
			if version == "" {
				version = "v1" // Default version
			}
			
			w.Header().Set("API-Version", version)
			handler.ServeHTTP(w, r)
		})
	}
	
	fmt.Println("  - URL path versioning (/api/v1, /api/v2)")
	fmt.Println("  - Header versioning (API-Version header)")
	fmt.Println("  - Query parameter versioning (?version=v1)")
	
	_ = versionMiddleware
	_ = headerVersionMiddleware
	_ = queryVersionMiddleware
}

// Rate limiting
func rateLimiting() {
	fmt.Println("Rate Limiting Examples:")
	
	// Simple rate limiter using map
	type RateLimiter struct {
		requests map[string][]time.Time
		limit    int
		window   time.Duration
		mutex    sync.Mutex
	}
	
	newRateLimiter := func(limit int, window time.Duration) *RateLimiter {
		return &RateLimiter{
			requests: make(map[string][]time.Time),
			limit:    limit,
			window:   window,
		}
	}
	
	allow := func(rl *RateLimiter, key string) bool {
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
	
	// Rate limiting middleware
	rateLimitMiddleware := func(rl *RateLimiter) func(http.Handler) http.Handler {
		return func(next http.Handler) http.Handler {
			return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
				key := r.RemoteAddr // Use IP as key
				
				if !allow(rl, key) {
					w.Header().Set("X-RateLimit-Limit", strconv.Itoa(rl.limit))
					w.Header().Set("X-RateLimit-Remaining", "0")
					w.Header().Set("X-RateLimit-Reset", time.Now().Add(rl.window).Format(time.RFC3339))
					
					http.Error(w, "Rate limit exceeded", http.StatusTooManyRequests)
					return
				}
				
				next.ServeHTTP(w, r)
			})
		}
	}
	
	fmt.Println("  - In-memory rate limiter")
	fmt.Println("  - Rate limiting middleware")
	fmt.Println("  - Rate limit headers")
	
	rl := newRateLimiter(10, time.Minute)
	_ = rateLimitMiddleware(rl)
}

// Authentication
func authentication() {
	fmt.Println("Authentication Examples:")
	
	// JWT token validation
	validateJWT := func(token string) bool {
		// In a real app, validate JWT signature and claims
		return token == "valid-jwt-token"
	}
	
	// API key validation
	validateAPIKey := func(apiKey string) bool {
		// In a real app, validate against database
		return apiKey == "valid-api-key"
	}
	
	// Basic auth validation
	validateBasicAuth := func(username, password string) bool {
		// In a real app, validate against database
		return username == "admin" && password == "password"
	}
	
	// JWT middleware
	jwtMiddleware := func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			authHeader := r.Header.Get("Authorization")
			if authHeader == "" {
				http.Error(w, "Authorization header required", http.StatusUnauthorized)
				return
			}
			
			// Extract token from "Bearer <token>"
			parts := strings.Split(authHeader, " ")
			if len(parts) != 2 || parts[0] != "Bearer" {
				http.Error(w, "Invalid authorization header", http.StatusUnauthorized)
				return
			}
			
			token := parts[1]
			if !validateJWT(token) {
				http.Error(w, "Invalid token", http.StatusUnauthorized)
				return
			}
			
			next.ServeHTTP(w, r)
		})
	}
	
	// API key middleware
	apiKeyMiddleware := func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			apiKey := r.Header.Get("X-API-Key")
			if apiKey == "" {
				http.Error(w, "API key required", http.StatusUnauthorized)
				return
			}
			
			if !validateAPIKey(apiKey) {
				http.Error(w, "Invalid API key", http.StatusUnauthorized)
				return
			}
			
			next.ServeHTTP(w, r)
		})
	}
	
	// Basic auth middleware
	basicAuthMiddleware := func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			username, password, ok := r.BasicAuth()
			if !ok {
				w.Header().Set("WWW-Authenticate", `Basic realm="Restricted"`)
				http.Error(w, "Authentication required", http.StatusUnauthorized)
				return
			}
			
			if !validateBasicAuth(username, password) {
				w.Header().Set("WWW-Authenticate", `Basic realm="Restricted"`)
				http.Error(w, "Invalid credentials", http.StatusUnauthorized)
				return
			}
			
			next.ServeHTTP(w, r)
		})
	}
	
	fmt.Println("  - JWT token validation")
	fmt.Println("  - API key validation")
	fmt.Println("  - Basic auth validation")
	fmt.Println("  - JWT middleware")
	fmt.Println("  - API key middleware")
	fmt.Println("  - Basic auth middleware")
	
	_ = jwtMiddleware
	_ = apiKeyMiddleware
	_ = basicAuthMiddleware
}

// CORS handling
func corsHandling() {
	fmt.Println("CORS Handling Examples:")
	
	// CORS middleware
	corsMiddleware := func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			// Set CORS headers
			w.Header().Set("Access-Control-Allow-Origin", "*")
			w.Header().Set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
			w.Header().Set("Access-Control-Allow-Headers", "Content-Type, Authorization")
			w.Header().Set("Access-Control-Max-Age", "86400")
			
			// Handle preflight requests
			if r.Method == http.MethodOptions {
				w.WriteHeader(http.StatusOK)
				return
			}
			
			next.ServeHTTP(w, r)
		})
	}
	
	// CORS middleware with specific origin
	corsMiddlewareWithOrigin := func(allowedOrigins []string) func(http.Handler) http.Handler {
		return func(next http.Handler) http.Handler {
			return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
				origin := r.Header.Get("Origin")
				
				// Check if origin is allowed
				allowed := false
				for _, allowedOrigin := range allowedOrigins {
					if origin == allowedOrigin {
						allowed = true
						break
					}
				}
				
				if allowed {
					w.Header().Set("Access-Control-Allow-Origin", origin)
				}
				
				w.Header().Set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
				w.Header().Set("Access-Control-Allow-Headers", "Content-Type, Authorization")
				
				if r.Method == http.MethodOptions {
					w.WriteHeader(http.StatusOK)
					return
				}
				
				next.ServeHTTP(w, r)
			})
		}
	}
	
	fmt.Println("  - Basic CORS middleware")
	fmt.Println("  - CORS with specific origins")
	fmt.Println("  - Preflight request handling")
	
	_ = corsMiddleware
	_ = corsMiddlewareWithOrigin([]string{"http://localhost:3000", "https://example.com"})
}

// API documentation
func apiDocumentation() {
	fmt.Println("API Documentation Examples:")
	
	// OpenAPI specification structure
	openAPISpec := map[string]interface{}{
		"openapi": "3.0.0",
		"info": map[string]interface{}{
			"title":   "User API",
			"version": "1.0.0",
			"description": "A simple user management API",
		},
		"paths": map[string]interface{}{
			"/users": map[string]interface{}{
				"get": map[string]interface{}{
					"summary":     "List all users",
					"description": "Get a list of all users",
					"responses": map[string]interface{}{
						"200": map[string]interface{}{
							"description": "Successful response",
							"content": map[string]interface{}{
								"application/json": map[string]interface{}{
									"schema": map[string]interface{}{
										"type": "array",
										"items": map[string]interface{}{
											"$ref": "#/components/schemas/User",
										},
									},
								},
							},
						},
					},
				},
			},
		},
		"components": map[string]interface{}{
			"schemas": map[string]interface{}{
				"User": map[string]interface{}{
					"type": "object",
					"properties": map[string]interface{}{
						"id": map[string]interface{}{
							"type": "integer",
						},
						"name": map[string]interface{}{
							"type": "string",
						},
						"email": map[string]interface{}{
							"type": "string",
							"format": "email",
						},
					},
					"required": []string{"name", "email"},
				},
			},
		},
	}
	
	// Swagger UI endpoint
	swaggerHandler := func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		json.NewEncoder(w).Encode(openAPISpec)
	}
	
	// API documentation endpoint
	docHandler := func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "text/html")
		html := `
			<!DOCTYPE html>
			<html>
			<head>
				<title>API Documentation</title>
			</head>
			<body>
				<h1>API Documentation</h1>
				<p>This is the API documentation for the User API.</p>
				<h2>Endpoints</h2>
				<ul>
					<li>GET /users - List all users</li>
					<li>POST /users - Create a user</li>
					<li>GET /users/{id} - Get a specific user</li>
					<li>PUT /users/{id} - Update a user</li>
					<li>DELETE /users/{id} - Delete a user</li>
				</ul>
				<h2>Authentication</h2>
				<p>This API uses JWT tokens for authentication.</p>
				<h2>Error Codes</h2>
				<ul>
					<li>400 - Bad Request</li>
					<li>401 - Unauthorized</li>
					<li>404 - Not Found</li>
					<li>500 - Internal Server Error</li>
				</ul>
			</body>
			</html>
		`
		fmt.Fprintf(w, html)
	}
	
	fmt.Println("  - OpenAPI specification")
	fmt.Println("  - Swagger UI endpoint")
	fmt.Println("  - API documentation endpoint")
	
	_ = swaggerHandler
	_ = docHandler
}

// Additional REST API concepts

// HATEOAS (Hypermedia as the Engine of Application State)
func hateoas() {
	fmt.Println("\n--- HATEOAS ---")
	fmt.Println("  - Include hypermedia links in responses")
	fmt.Println("  - Enable API discoverability")
	fmt.Println("  - Follow REST principles")
	
	// HATEOAS response example
	hateoasResponse := map[string]interface{}{
		"user": map[string]interface{}{
			"id":    1,
			"name":  "John Doe",
			"email": "john@example.com",
		},
		"_links": map[string]interface{}{
			"self": map[string]interface{}{
				"href": "/users/1",
			},
			"users": map[string]interface{}{
				"href": "/users",
			},
			"update": map[string]interface{}{
				"href": "/users/1",
				"method": "PUT",
			},
			"delete": map[string]interface{}{
				"href": "/users/1",
				"method": "DELETE",
			},
		},
	}
	
	fmt.Printf("  HATEOAS response: %+v\n", hateoasResponse)
}

// GraphQL comparison
func graphqlComparison() {
	fmt.Println("\n--- GraphQL Comparison ---")
	fmt.Println("  - REST: Fixed endpoints, multiple requests")
	fmt.Println("  - GraphQL: Single endpoint, flexible queries")
	fmt.Println("  - REST: Over-fetching/under-fetching")
	fmt.Println("  - GraphQL: Exact data requested")
	fmt.Println("  - REST: HTTP status codes")
	fmt.Println("  - GraphQL: Errors in response body")
}

// API testing
func apiTesting() {
	fmt.Println("\n--- API Testing ---")
	fmt.Println("  - Unit tests for handlers")
	fmt.Println("  - Integration tests")
	fmt.Println("  - Contract testing")
	fmt.Println("  - Load testing")
	fmt.Println("  - Security testing")
}

// API monitoring
func apiMonitoring() {
	fmt.Println("\n--- API Monitoring ---")
	fmt.Println("  - Request/response logging")
	fmt.Println("  - Performance metrics")
	fmt.Println("  - Error tracking")
	fmt.Println("  - Health checks")
	fmt.Println("  - Rate limiting metrics")
}

// API security
func apiSecurity() {
	fmt.Println("\n--- API Security ---")
	fmt.Println("  - Input validation")
	fmt.Println("  - SQL injection prevention")
	fmt.Println("  - XSS protection")
	fmt.Println("  - CSRF protection")
	fmt.Println("  - Rate limiting")
	fmt.Println("  - HTTPS enforcement")
}

// Demonstrate all REST API concepts
func demonstrateAllRESTConcepts() {
	hateoas()
	graphqlComparison()
	apiTesting()
	apiMonitoring()
	apiSecurity()
}
