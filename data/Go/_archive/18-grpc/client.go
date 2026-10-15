package main

import (
	"context"
	"fmt"
	"log"

	"google.golang.org/grpc"
	"google.golang.org/grpc/credentials/insecure"
)

// Data Models
type User struct {
	Id    string
	Name  string
	Email string
}

type Product struct {
	Id       string
	Name     string
	Price    float64
	Category string
}

// User Request/Response
type GetUserRequest struct{ Id string }
type GetUserResponse struct{ User *User }
type ListUsersRequest struct{}
type ListUsersResponse struct{ Users []*User }
type CreateUserRequest struct{ User *User }
type CreateUserResponse struct{ User *User }
type UpdateUserRequest struct{ User *User }
type UpdateUserResponse struct{ User *User }
type DeleteUserRequest struct{ Id string }
type DeleteUserResponse struct{ Success bool }
type SearchUsersRequest struct{ Query string }
type SearchUsersResponse struct{ User *User }

// Product Request/Response
type GetProductRequest struct{ Id string }
type GetProductResponse struct{ Product *Product }
type ListProductsRequest struct{}
type ListProductsResponse struct{ Products []*Product }
type CreateProductRequest struct{ Product *Product }
type CreateProductResponse struct{ Product *Product }
type UpdateProductRequest struct{ Product *Product }
type UpdateProductResponse struct{ Product *Product }
type DeleteProductRequest struct{ Id string }
type DeleteProductResponse struct{ Success bool }
type SearchProductsRequest struct{ Query string }
type SearchProductsResponse struct{ Product *Product }

// Stream Interfaces
type UserService_SearchUsersClient interface {
	Recv() (*SearchUsersResponse, error)
	grpc.ClientStream
}

type ProductService_SearchProductsClient interface {
	Recv() (*SearchProductsResponse, error)
	grpc.ClientStream
}

// Client Interfaces
type UserServiceClient interface {
	CreateUser(ctx context.Context, in *CreateUserRequest, opts ...grpc.CallOption) (*CreateUserResponse, error)
	GetUser(ctx context.Context, in *GetUserRequest, opts ...grpc.CallOption) (*GetUserResponse, error)
	ListUsers(ctx context.Context, in *ListUsersRequest, opts ...grpc.CallOption) (*ListUsersResponse, error)
	UpdateUser(ctx context.Context, in *UpdateUserRequest, opts ...grpc.CallOption) (*UpdateUserResponse, error)
	DeleteUser(ctx context.Context, in *DeleteUserRequest, opts ...grpc.CallOption) (*DeleteUserResponse, error)
	SearchUsers(ctx context.Context, in *SearchUsersRequest, opts ...grpc.CallOption) (UserService_SearchUsersClient, error)
}

type ProductServiceClient interface {
	CreateProduct(ctx context.Context, in *CreateProductRequest, opts ...grpc.CallOption) (*CreateProductResponse, error)
	GetProduct(ctx context.Context, in *GetProductRequest, opts ...grpc.CallOption) (*GetProductResponse, error)
	ListProducts(ctx context.Context, in *ListProductsRequest, opts ...grpc.CallOption) (*ListProductsResponse, error)
	UpdateProduct(ctx context.Context, in *UpdateProductRequest, opts ...grpc.CallOption) (*UpdateProductResponse, error)
	DeleteProduct(ctx context.Context, in *DeleteProductRequest, opts ...grpc.CallOption) (*DeleteProductResponse, error)
	SearchProducts(ctx context.Context, in *SearchProductsRequest, opts ...grpc.CallOption) (ProductService_SearchProductsClient, error)
}

// Mock Implementations
type mockUserClient struct{}
type mockProductClient struct{}

func NewUserServiceClient(conn *grpc.ClientConn) UserServiceClient {
	return &mockUserClient{}
}

func NewProductServiceClient(conn *grpc.ClientConn) ProductServiceClient {
	return &mockProductClient{}
}

func (m *mockUserClient) CreateUser(ctx context.Context, in *CreateUserRequest, opts ...grpc.CallOption) (*CreateUserResponse, error) {
	return &CreateUserResponse{User: &User{Id: "1", Name: in.User.Name, Email: in.User.Email}}, nil
}
func (m *mockUserClient) GetUser(ctx context.Context, in *GetUserRequest, opts ...grpc.CallOption) (*GetUserResponse, error) {
	return &GetUserResponse{User: &User{Id: in.Id, Name: "Alice Johnson", Email: "alice@example.com"}}, nil
}
func (m *mockUserClient) ListUsers(ctx context.Context, in *ListUsersRequest, opts ...grpc.CallOption) (*ListUsersResponse, error) {
	return &ListUsersResponse{Users: []*User{{Id: "1", Name: "Alice Johnson", Email: "alice@example.com"}}}, nil
}
func (m *mockUserClient) UpdateUser(ctx context.Context, in *UpdateUserRequest, opts ...grpc.CallOption) (*UpdateUserResponse, error) {
	return &UpdateUserResponse{User: in.User}, nil
}
func (m *mockUserClient) DeleteUser(ctx context.Context, in *DeleteUserRequest, opts ...grpc.CallOption) (*DeleteUserResponse, error) {
	return &DeleteUserResponse{Success: true}, nil
}
func (m *mockUserClient) SearchUsers(ctx context.Context, in *SearchUsersRequest, opts ...grpc.CallOption) (UserService_SearchUsersClient, error) {
	return &mockUserStream{items: []*User{{Id: "1", Name: in.Query, Email: "alice@example.com"}}}, nil
}

func (m *mockProductClient) CreateProduct(ctx context.Context, in *CreateProductRequest, opts ...grpc.CallOption) (*CreateProductResponse, error) {
	return &CreateProductResponse{Product: &Product{Id: "1", Name: in.Product.Name, Price: in.Product.Price, Category: in.Product.Category}}, nil
}
func (m *mockProductClient) GetProduct(ctx context.Context, in *GetProductRequest, opts ...grpc.CallOption) (*GetProductResponse, error) {
	return &GetProductResponse{Product: &Product{Id: in.Id, Name: "Smartphone", Price: 699.99, Category: "Electronics"}}, nil
}
func (m *mockProductClient) ListProducts(ctx context.Context, in *ListProductsRequest, opts ...grpc.CallOption) (*ListProductsResponse, error) {
	return &ListProductsResponse{Products: []*Product{{Id: "1", Name: "Smartphone", Price: 699.99, Category: "Electronics"}}}, nil
}
func (m *mockProductClient) UpdateProduct(ctx context.Context, in *UpdateProductRequest, opts ...grpc.CallOption) (*UpdateProductResponse, error) {
	return &UpdateProductResponse{Product: in.Product}, nil
}
func (m *mockProductClient) DeleteProduct(ctx context.Context, in *DeleteProductRequest, opts ...grpc.CallOption) (*DeleteProductResponse, error) {
	return &DeleteProductResponse{Success: true}, nil
}
func (m *mockProductClient) SearchProducts(ctx context.Context, in *SearchProductsRequest, opts ...grpc.CallOption) (ProductService_SearchProductsClient, error) {
	return &mockProductStream{items: []*Product{{Id: "1", Name: "Smartphone", Price: 699.99, Category: in.Query}}}, nil
}

type mockUserStream struct {
	grpc.ClientStream
	items []*User
	idx   int
}

func (s *mockUserStream) Recv() (*SearchUsersResponse, error) {
	if s.idx >= len(s.items) {
		return nil, fmt.Errorf("EOF")
	}
	item := s.items[s.idx]
	s.idx++
	return &SearchUsersResponse{User: item}, nil
}

type mockProductStream struct {
	grpc.ClientStream
	items []*Product
	idx   int
}

func (s *mockProductStream) Recv() (*SearchProductsResponse, error) {
	if s.idx >= len(s.items) {
		return nil, fmt.Errorf("EOF")
	}
	item := s.items[s.idx]
	s.idx++
	return &SearchProductsResponse{Product: item}, nil
}

// GRPC Client Controller
type GRPCClient struct {
	conn          *grpc.ClientConn
	userClient    UserServiceClient
	productClient ProductServiceClient
}

func NewGRPCClient(address string) (*GRPCClient, error) {
	conn, err := grpc.Dial(address, grpc.WithTransportCredentials(insecure.NewCredentials()))
	if err != nil {
		return nil, err
	}

	return &GRPCClient{
		conn:          conn,
		userClient:    NewUserServiceClient(conn),
		productClient: NewProductServiceClient(conn),
	}, nil
}

func (c *GRPCClient) Close() {
	c.conn.Close()
}

func (c *GRPCClient) TestUserOperations() error {
	ctx := context.Background()

	createResp, err := c.userClient.CreateUser(ctx, &CreateUserRequest{
		User: &User{
			Name:  "Alice Johnson",
			Email: "alice@example.com",
		},
	})
	if err != nil {
		return err
	}
	log.Printf("Created user: %+v", createResp.User)

	getResp, err := c.userClient.GetUser(ctx, &GetUserRequest{Id: createResp.User.Id})
	if err != nil {
		return err
	}
	log.Printf("Got user: %+v", getResp.User)

	listResp, err := c.userClient.ListUsers(ctx, &ListUsersRequest{})
	if err != nil {
		return err
	}
	log.Printf("List users: %d users found", len(listResp.Users))

	updateResp, err := c.userClient.UpdateUser(ctx, &UpdateUserRequest{
		User: &User{
			Id:    createResp.User.Id,
			Name:  "Alice Updated",
			Email: "alice.updated@example.com",
		},
	})
	if err != nil {
		return err
	}
	log.Printf("Updated user: %+v", updateResp.User)

	searchStream, err := c.userClient.SearchUsers(ctx, &SearchUsersRequest{Query: "Alice"})
	if err != nil {
		return err
	}

	for {
		searchResp, err := searchStream.Recv()
		if err != nil {
			break
		}
		log.Printf("Found user in search: %+v", searchResp.User)
	}

	deleteResp, err := c.userClient.DeleteUser(ctx, &DeleteUserRequest{Id: createResp.User.Id})
	if err != nil {
		return err
	}
	log.Printf("Deleted user: %t", deleteResp.Success)

	return nil
}

func (c *GRPCClient) TestProductOperations() error {
	ctx := context.Background()

	createResp, err := c.productClient.CreateProduct(ctx, &CreateProductRequest{
		Product: &Product{
			Name:     "Smartphone",
			Price:    699.99,
			Category: "Electronics",
		},
	})
	if err != nil {
		return err
	}
	log.Printf("Created product: %+v", createResp.Product)

	getResp, err := c.productClient.GetProduct(ctx, &GetProductRequest{Id: createResp.Product.Id})
	if err != nil {
		return err
	}
	log.Printf("Got product: %+v", getResp.Product)

	listResp, err := c.productClient.ListProducts(ctx, &ListProductsRequest{})
	if err != nil {
		return err
	}
	log.Printf("List products: %d products found", len(listResp.Products))

	updateResp, err := c.productClient.UpdateProduct(ctx, &UpdateProductRequest{
		Product: &Product{
			Id:       createResp.Product.Id,
			Name:     "Smartphone Pro",
			Price:    799.99,
			Category: "Electronics",
		},
	})
	if err != nil {
		return err
	}
	log.Printf("Updated product: %+v", updateResp.Product)

	searchStream, err := c.productClient.SearchProducts(ctx, &SearchProductsRequest{Query: "Electronics"})
	if err != nil {
		return err
	}

	for {
		searchResp, err := searchStream.Recv()
		if err != nil {
			break
		}
		log.Printf("Found product in search: %+v", searchResp.Product)
	}

	deleteResp, err := c.productClient.DeleteProduct(ctx, &DeleteProductRequest{Id: createResp.Product.Id})
	if err != nil {
		return err
	}
	log.Printf("Deleted product: %t", deleteResp.Success)

	return nil
}

func main() {
	client, err := NewGRPCClient("localhost:50051")
	if err != nil {
		log.Fatalf("Failed to create client: %v", err)
	}
	defer client.Close()

	log.Println("Testing user operations...")
	if err := client.TestUserOperations(); err != nil {
		log.Printf("User operations failed: %v", err)
	}

	log.Println("Testing product operations...")
	if err := client.TestProductOperations(); err != nil {
		log.Printf("Product operations failed: %v", err)
	}

	log.Println("All tests completed")
}
