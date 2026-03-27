package main

import (
	"context"
	"fmt"
	"log"
)

// UserServiceServer implementation
type UserServiceServer struct {
	UnimplementedUserServiceServer
	users map[string]*User
}

func NewUserServiceServer() *UserServiceServer {
	users := make(map[string]*User)
	users["1"] = &User{Id: "1", Name: "John Doe", Email: "john@example.com"}
	users["2"] = &User{Id: "2", Name: "Jane Smith", Email: "jane@example.com"}
	
	return &UserServiceServer{
		users: users,
	}
}

func (s *UserServiceServer) GetUser(ctx context.Context, req *GetUserRequest) (*GetUserResponse, error) {
	log.Printf("GetUser called with ID: %s", req.Id)
	
	user, exists := s.users[req.Id]
	if !exists {
		return nil, fmt.Errorf("user not found")
	}
	
	return &GetUserResponse{
		User: user,
	}, nil
}

func (s *UserServiceServer) ListUsers(ctx context.Context, req *ListUsersRequest) (*ListUsersResponse, error) {
	log.Println("ListUsers called")
	
	var users []*User
	for _, user := range s.users {
		users = append(users, user)
	}
	
	return &ListUsersResponse{
		Users: users,
	}, nil
}

func (s *UserServiceServer) CreateUser(ctx context.Context, req *CreateUserRequest) (*CreateUserResponse, error) {
	log.Printf("CreateUser called with Name: %s", req.User.Name)
	
	id := fmt.Sprintf("%d", len(s.users)+1)
	user := &User{
		Id:    id,
		Name:  req.User.Name,
		Email: req.User.Email,
	}
	
	s.users[id] = user
	
	return &CreateUserResponse{
		User: user,
	}, nil
}

func (s *UserServiceServer) UpdateUser(ctx context.Context, req *UpdateUserRequest) (*UpdateUserResponse, error) {
	log.Printf("UpdateUser called with ID: %s", req.User.Id)
	
	user, exists := s.users[req.User.Id]
	if !exists {
		return nil, fmt.Errorf("user not found")
	}
	
	user.Name = req.User.Name
	user.Email = req.User.Email
	
	return &UpdateUserResponse{
		User: user,
	}, nil
}

func (s *UserServiceServer) DeleteUser(ctx context.Context, req *DeleteUserRequest) (*DeleteUserResponse, error) {
	log.Printf("DeleteUser called with ID: %s", req.Id)
	
	_, exists := s.users[req.Id]
	if !exists {
		return nil, fmt.Errorf("user not found")
	}
	
	delete(s.users, req.Id)
	
	return &DeleteUserResponse{
		Success: true,
	}, nil
}

func (s *UserServiceServer) SearchUsers(req *SearchUsersRequest, stream UserService_SearchUsersServer) error {
	log.Printf("SearchUsers called with query: %s", req.Query)
	
	for _, user := range s.users {
		// Simple search logic
		if user.Name == req.Query || user.Email == req.Query {
			if err := stream.Send(&SearchUsersResponse{User: user}); err != nil {
				return err
			}
		}
	}
	
	return nil
}
