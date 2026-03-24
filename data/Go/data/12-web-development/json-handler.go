package main

import (
	"encoding/json"
	"fmt"
	"net/http"
	"time"
)

type User struct {
	ID       int       `json:"id"`
	Name     string    `json:"name"`
	Email    string    `json:"email"`
	CreateAt time.Time `json:"created_at"`
}

type Response struct {
	Message string `json:"message"`
	Data    any    `json:"data,omitempty"`
}

func main() {
	fmt.Println("=== JSON Handling Examples ===")

	// Create sample user
	user := User{
		ID:       1,
		Name:     "John Doe",
		Email:    "john@example.com",
		CreateAt: time.Now(),
	}

	// Marshal to JSON
	jsonData, err := json.Marshal(user)
	if err != nil {
		fmt.Printf("Error marshaling JSON: %v\n", err)
		return
	}
	fmt.Printf("JSON output: %s\n", string(jsonData))

	// Unmarshal from JSON
	var user2 User
	err = json.Unmarshal(jsonData, &user2)
	if err != nil {
		fmt.Printf("Error unmarshaling JSON: %v\n", err)
		return
	}
	fmt.Printf("Unmarshaled user: %+v\n", user2)

	// Create HTTP response
	response := Response{
		Message: "User created successfully",
		Data:    user,
	}

	responseJSON, err := json.Marshal(response)
	if err != nil {
		fmt.Printf("Error marshaling response: %v\n", err)
		return
	}
	fmt.Printf("Response JSON: %s\n", string(responseJSON))

	// Simulate HTTP handler
	fmt.Println("\n=== Simulated HTTP Handler ===")
	handler := func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		w.WriteHeader(http.StatusOK)
		w.Write(responseJSON)
	}

	fmt.Printf("Handler function created: %T\n", handler)
}
