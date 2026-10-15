package main

import (
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"os"
	"strings"
	"time"
)

func main() {
	fmt.Println("=== Web Development in Go ===")

	// Basic HTTP server
	fmt.Println("\n--- Basic HTTP Server ---")
	basicHTTPServer()

	// HTTP handlers and routing
	fmt.Println("\n--- HTTP Handlers and Routing ---")
	httpHandlers()

	// Middleware
	fmt.Println("\n--- HTTP Middleware ---")
	httpMiddleware()

	// JSON responses
	fmt.Println("\n--- JSON Responses ---")
	jsonResponses()

	// Form handling
	fmt.Println("\n--- Form Handling ---")
	formHandling()

	// File serving
	fmt.Println("\n--- File Serving ---")
	fileServing()

	// HTTP client
	fmt.Println("\n--- HTTP Client ---")
	httpClient()

	// Templates
	fmt.Println("\n--- HTML Templates ---")
	htmlTemplates()

	// WebSockets
	fmt.Println("\n--- WebSockets ---")
	webSockets()

	// REST API
	fmt.Println("\n--- REST API ---")
	restAPI()
}

// Basic HTTP server example
func basicHTTPServer() {
	http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintf(w, "Hello, World! You requested: %s", r.URL.Path)
	})

	http.HandleFunc("/about", func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintf(w, "About Page - Go Web Development")
	})

	http.HandleFunc("/contact", func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintf(w, "Contact Page - Email: info@example.com")
	})

	fmt.Println("Starting server on :8080")
	fmt.Println("Visit:")
	fmt.Println("  http://localhost:8080/")
	fmt.Println("  http://localhost:8080/about")
	fmt.Println("  http://localhost:8080/contact")
	fmt.Println("Server setup complete (not actually starting to avoid blocking)")
}

// HTTP handlers and routing
func httpHandlers() {
	fmt.Println("HTTP Handler Examples:")

	handler1 := func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "text/plain")
		fmt.Fprintf(w, "Handler 1: Method: %s, Path: %s", r.Method, r.URL.Path)
	}

	handler2 := func(w http.ResponseWriter, r *http.Request) {
		switch r.Method {
		case http.MethodGet:
			fmt.Fprintf(w, "GET request received")
		case http.MethodPost:
			fmt.Fprintf(w, "POST request received")
		default:
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		}
	}

	handler3 := func(w http.ResponseWriter, r *http.Request) {
		path := r.URL.Path
		fmt.Fprintf(w, "Path: %s", path)
	}

	handler4 := func(w http.ResponseWriter, r *http.Request) {
		query := r.URL.Query()
		name := query.Get("name")
		if name != "" {
			fmt.Fprintf(w, "Hello, %s!", name)
		} else {
			fmt.Fprintf(w, "Hello, Stranger!")
		}
	}

	fmt.Println("  - Handler 1: Basic response")
	fmt.Println("  - Handler 2: Method checking")
	fmt.Println("  - Handler 3: Path handling")
	fmt.Println("  - Handler 4: Query parameters")

	http.HandleFunc("/handler1", handler1)
	http.HandleFunc("/handler2", handler2)
	http.HandleFunc("/handler3", handler3)
	http.HandleFunc("/handler4", handler4)
}

// HTTP middleware
func httpMiddleware() {
	fmt.Println("HTTP Middleware Examples:")

	loggingMiddleware := func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			start := time.Now()
			log.Printf("Started %s %s", r.Method, r.URL.Path)
			next.ServeHTTP(w, r)
			log.Printf("Completed %s %s in %v", r.Method, r.URL.Path, time.Since(start))
		})
	}

	authMiddleware := func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			apiKey := r.Header.Get("X-API-Key")
			if apiKey != "secret-key" {
				http.Error(w, "Unauthorized", http.StatusUnauthorized)
				return
			}
			next.ServeHTTP(w, r)
		})
	}

	corsMiddleware := func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			w.Header().Set("Access-Control-Allow-Origin", "*")
			w.Header().Set("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
			w.Header().Set("Access-Control-Allow-Headers", "Content-Type, Authorization")

			if r.Method == http.MethodOptions {
				w.WriteHeader(http.StatusOK)
				return
			}
			next.ServeHTTP(w, r)
		})
	}

	rateLimitMiddleware := func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			clientIP := r.RemoteAddr
			log.Printf("Request from IP: %s", clientIP)
			next.ServeHTTP(w, r)
		})
	}

	fmt.Println("  - Logging middleware")
	fmt.Println("  - Authentication middleware")
	fmt.Println("  - CORS middleware")
	fmt.Println("  - Rate limiting middleware")

	handler := http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintf(w, "Request processed through middleware chain")
	})

	finalHandler := loggingMiddleware(authMiddleware(corsMiddleware(rateLimitMiddleware(handler))))
	http.Handle("/middleware", finalHandler)
}

// JSON responses
func jsonResponses() {
	fmt.Println("JSON Response Examples:")

	jsonHandler1 := func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		fmt.Fprintf(w, `{"message": "Hello, JSON!", "status": "success"}`)
	}

	type User struct {
		ID    int    `json:"id"`
		Name  string `json:"name"`
		Email string `json:"email"`
	}

	jsonHandler2 := func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		user := User{
			ID:    1,
			Name:  "John Doe",
			Email: "john@example.com",
		}
		json.NewEncoder(w).Encode(user)
	}

	jsonHandler3 := func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		fmt.Fprintf(w, `[{"id": 1, "name": "John"}, {"id": 2, "name": "Jane"}]`)
	}

	jsonHandler4 := func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusNotFound)
		fmt.Fprintf(w, `{"error": "Not Found", "message": "Resource not found"}`)
	}

	fmt.Println("  - Simple JSON response")
	fmt.Println("  - Struct to JSON response")
	fmt.Println("  - JSON array response")
	fmt.Println("  - Error JSON response")

	http.HandleFunc("/json1", jsonHandler1)
	http.HandleFunc("/json2", jsonHandler2)
	http.HandleFunc("/json3", jsonHandler3)
	http.HandleFunc("/json4", jsonHandler4)
}

// Form handling
func formHandling() {
	fmt.Println("Form Handling Examples:")

	formHandler := func(w http.ResponseWriter, r *http.Request) {
		if r.Method == http.MethodGet {
			w.Header().Set("Content-Type", "text/html")
			fmt.Fprintf(w, `
				<form method="POST" action="/submit">
					<label for="name">Name:</label>
					<input type="text" id="name" name="name" required><br><br>
					<label for="email">Email:</label>
					<input type="email" id="email" name="email" required><br><br>
					<input type="submit" value="Submit">
				</form>
			`)
		} else if r.Method == http.MethodPost {
			if err := r.ParseForm(); err != nil {
				http.Error(w, "Error parsing form", http.StatusBadRequest)
				return
			}

			name := r.FormValue("name")
			email := r.FormValue("email")

			w.Header().Set("Content-Type", "text/html")
			fmt.Fprintf(w, `
				<h2>Form Submitted!</h2>
				<p>Name: %s</p>
				<p>Email: %s</p>
				<a href="/form">Back to form</a>
			`, name, email)
		}
	}

	uploadHandler := func(w http.ResponseWriter, r *http.Request) {
		if r.Method == http.MethodGet {
			w.Header().Set("Content-Type", "text/html")
			fmt.Fprintf(w, `
				<form method="POST" action="/upload" enctype="multipart/form-data">
					<label for="file">Choose file:</label>
					<input type="file" id="file" name="file" required><br><br>
					<input type="submit" value="Upload">
				</form>
			`)
		} else if r.Method == http.MethodPost {
			if err := r.ParseMultipartForm(32 << 20); err != nil {
				http.Error(w, "Error parsing form", http.StatusBadRequest)
				return
			}

			file, handler, err := r.FormFile("file")
			if err != nil {
				http.Error(w, "Error getting file", http.StatusBadRequest)
				return
			}
			defer file.Close()

			fmt.Fprintf(w, "File uploaded: %s (%d bytes)", handler.Filename, handler.Size)
		}
	}

	fmt.Println("  - Form display and submission")
	fmt.Println("  - File upload handling")

	http.HandleFunc("/form", formHandler)
	http.HandleFunc("/submit", formHandler)
	http.HandleFunc("/upload", uploadHandler)
}

// File serving
func fileServing() {
	fmt.Println("File Serving Examples:")

	fileServer := http.FileServer(http.Dir("./static"))

	customFileHandler := func(w http.ResponseWriter, r *http.Request) {
		if strings.Contains(r.URL.Path, "..") {
			http.Error(w, "Bad request", http.StatusBadRequest)
			return
		}

		filePath := "./files" + r.URL.Path
		if _, err := os.Stat(filePath); os.IsNotExist(err) {
			http.Error(w, "File not found", http.StatusNotFound)
			return
		}

		http.ServeFile(w, r, filePath)
	}

	downloadHandler := func(w http.ResponseWriter, r *http.Request) {
		filePath := "./files/example.txt"
		w.Header().Set("Content-Disposition", "attachment; filename=example.txt")
		w.Header().Set("Content-Type", "text/plain")
		http.ServeFile(w, r, filePath)
	}

	fmt.Println("  - Static file server")
	fmt.Println("  - Custom file handler")
	fmt.Println("  - File download handler")

	http.Handle("/static/", http.StripPrefix("/static/", fileServer))
	http.HandleFunc("/files/", customFileHandler)
	http.HandleFunc("/download", downloadHandler)
}

// HTTP client
func httpClient() {
	fmt.Println("HTTP Client Examples:")

	getRequest := func() {
		resp, err := http.Get("https://httpbin.org/get")
		if err != nil {
			fmt.Printf("Error making GET request: %v\n", err)
			return
		}
		defer resp.Body.Close()
		fmt.Printf("GET request status: %s\n", resp.Status)
	}

	postRequest := func() {
		fmt.Println("POST request with JSON body")
	}

	requestWithHeaders := func() {
		client := &http.Client{}
		req, err := http.NewRequest("GET", "https://httpbin.org/headers", nil)
		if err != nil {
			fmt.Printf("Error creating request: %v\n", err)
			return
		}
		req.Header.Set("User-Agent", "Go-Web-App/1.0")
		req.Header.Set("Accept", "application/json")

		resp, err := client.Do(req)
		if err != nil {
			fmt.Printf("Error making request: %v\n", err)
			return
		}
		defer resp.Body.Close()
		fmt.Printf("Request with headers status: %s\n", resp.Status)
	}

	timeoutRequest := func() {
		client := &http.Client{
			Timeout: 5 * time.Second,
		}
		resp, err := client.Get("https://httpbin.org/delay/2")
		if err != nil {
			fmt.Printf("Timeout error: %v\n", err)
			return
		}
		defer resp.Body.Close()
		fmt.Printf("Timeout request status: %s\n", resp.Status)
	}

	_ = getRequest
	_ = postRequest
	_ = requestWithHeaders
	_ = timeoutRequest

	fmt.Println("  - Simple GET request")
	fmt.Println("  - POST request with JSON")
	fmt.Println("  - Request with custom headers")
	fmt.Println("  - Request with timeout")
	fmt.Println("HTTP client examples ready (not actually making requests to avoid network calls)")
}

// HTML templates
func htmlTemplates() {
	fmt.Println("HTML Template Examples:")

	templateHandler := func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "text/html")
		html := `
			<!DOCTYPE html>
			<html>
			<head><title>Go Web App</title></head>
			<body>
				<h1>Welcome to Go Web Development</h1>
				<p>Current time: %s</p>
				<p>Request path: %s</p>
			</body>
			</html>
		`
		fmt.Fprintf(w, html, time.Now().Format("2006-01-02 15:04:05"), r.URL.Path)
	}

	templateWithData := func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "text/html")
		name := r.URL.Query().Get("name")
		if name == "" {
			name = "Guest"
		}
		html := `
			<!DOCTYPE html>
			<html>
			<head><title>Hello Page</title></head>
			<body>
				<h1>Hello, %s!</h1>
				<p>This is a dynamic template.</p>
			</body>
			</html>
		`
		fmt.Fprintf(w, html, name)
	}

	fmt.Println("  - Simple HTML template")
	fmt.Println("  - Template with dynamic data")

	http.HandleFunc("/template", templateHandler)
	http.HandleFunc("/hello", templateWithData)
}

// WebSockets
func webSockets() {
	fmt.Println("WebSocket Examples:")

	websocketHandler := func(w http.ResponseWriter, r *http.Request) {
		if r.Header.Get("Upgrade") != "websocket" {
			http.Error(w, "Not a WebSocket request", http.StatusBadRequest)
			return
		}
		fmt.Fprintf(w, "WebSocket upgrade request received")
	}

	echoHandler := func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintf(w, "WebSocket echo handler")
	}

	chatHandler := func(w http.ResponseWriter, r *http.Request) {
		fmt.Fprintf(w, "WebSocket chat handler")
	}

	fmt.Println("  - WebSocket upgrade handler")
	fmt.Println("  - WebSocket echo handler")
	fmt.Println("  - WebSocket chat handler")

	http.HandleFunc("/ws", websocketHandler)
	http.HandleFunc("/ws/echo", echoHandler)
	http.HandleFunc("/ws/chat", chatHandler)
}

// REST API
func restAPI() {
	fmt.Println("REST API Examples:")

	type User struct {
		ID    int    `json:"id"`
		Name  string `json:"name"`
		Email string `json:"email"`
	}

	getUsers := func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		users := []User{
			{ID: 1, Name: "John Doe", Email: "john@example.com"},
			{ID: 2, Name: "Jane Smith", Email: "jane@example.com"},
		}
		json.NewEncoder(w).Encode(map[string]interface{}{"users": users})
	}

	getUser := func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		fmt.Fprintf(w, `{"id": 1, "name": "John Doe", "email": "john@example.com"}`)
	}

	createUser := func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		if r.Method != http.MethodPost {
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
			return
		}
		fmt.Fprintf(w, `{"id": 3, "name": "New User", "email": "new@example.com", "created": true}`)
	}

	updateUser := func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		if r.Method != http.MethodPut {
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
			return
		}
		fmt.Fprintf(w, `{"id": 1, "name": "Updated User", "email": "updated@example.com", "updated": true}`)
	}

	deleteUser := func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		if r.Method != http.MethodDelete {
			http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
			return
		}
		fmt.Fprintf(w, `{"deleted": true, "message": "User deleted successfully"}`)
	}

	fmt.Println("  - GET /users - List users")
	fmt.Println("  - GET /users/{id} - Get user")
	fmt.Println("  - POST /users - Create user")
	fmt.Println("  - PUT /users/{id} - Update user")
	fmt.Println("  - DELETE /users/{id} - Delete user")

	http.HandleFunc("/api/users", getUsers)
	http.HandleFunc("/api/users/1", getUser)
	http.HandleFunc("/api/users/create", createUser)
	http.HandleFunc("/api/users/update", updateUser)
	http.HandleFunc("/api/users/delete", deleteUser)
}
