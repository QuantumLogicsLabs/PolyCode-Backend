package main

import (
	"context"
	"encoding/json"
	"fmt"
	"net/http"
	"time"
)

type UserService struct {
	// Database connection, cache, etc.
}

type User struct {
	ID       string `json:"id"`
	Name     string `json:"name"`
	Email    string `json:"email"`
	Password string `json:"-"`
}

func NewUserService() *UserService {
	return &UserService{}
}

func (s *UserService) GetUser(ctx context.Context, id string) (*User, error) {
	return &User{
		ID:    id,
		Name:  "John Doe",
		Email: "john@example.com",
	}, nil
}

func (s *UserService) CreateUser(ctx context.Context, user *User) error {
	user.ID = fmt.Sprintf("user_%d", time.Now().Unix())
	return nil
}

func (s *UserService) UpdateUser(ctx context.Context, user *User) error {
	return nil
}

func (s *UserService) DeleteUser(ctx context.Context, id string) error {
	return nil
}

func (s *UserService) ListUsers(ctx context.Context) ([]*User, error) {
	return []*User{
		{ID: "1", Name: "John Doe", Email: "john@example.com"},
		{ID: "2", Name: "Jane Smith", Email: "jane@example.com"},
	}, nil
}

func respondWithJSON(w http.ResponseWriter, code int, payload interface{}) {
	response, _ := json.Marshal(payload)
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(code)
	w.Write(response)
}

func main() {
	fmt.Println("=== Microservice User Service Demo ===")
	svc := NewUserService()
	ctx := context.Background()

	// Demonstrate CreateUser
	newUser := &User{Name: "Alice", Email: "alice@example.com"}
	_ = svc.CreateUser(ctx, newUser)
	fmt.Printf("Created User: ID=%s, Name=%s, Email=%s\n", newUser.ID, newUser.Name, newUser.Email)

	// Demonstrate GetUser
	user, _ := svc.GetUser(ctx, "user_100")
	fmt.Printf("Retrieved User: ID=%s, Name=%s\n", user.ID, user.Name)

	// Demonstrate ListUsers
	users, _ := svc.ListUsers(ctx)
	fmt.Printf("Listing %d sample users\n", len(users))
	for _, u := range users {
		fmt.Printf(" - [%s] %s (%s)\n", u.ID, u.Name, u.Email)
	}
}
