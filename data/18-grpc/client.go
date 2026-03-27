package main

import (
	"context"
	"log"
	"time"

	"google.golang.org/grpc"
	"google.golang.org/grpc/credentials/insecure"
)

type GRPCClient struct {
	conn         *grpc.ClientConn
	userClient   UserServiceClient
	productClient ProductServiceClient
}

func NewGRPCClient(address string) (*GRPCClient, error) {
	conn, err := grpc.Dial(address, grpc.WithTransportCredentials(insecure.NewCredentials()))
	if err != nil {
		return nil, err
	}

	return &GRPCClient{
		conn:         conn,
		userClient:   NewUserServiceClient(conn),
		productClient: NewProductServiceClient(conn),
	}, nil
}

func (c *GRPCClient) Close() {
	c.conn.Close()
}

func (c *GRPCClient) TestUserOperations() error {
	ctx := context.Background()
	
	// Create user
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
	
	// Get user
	getResp, err := c.userClient.GetUser(ctx, &GetUserRequest{Id: createResp.User.Id})
	if err != nil {
		return err
	}
	log.Printf("Got user: %+v", getResp.User)
	
	// List users
	listResp, err := c.userClient.ListUsers(ctx, &ListUsersRequest{})
	if err != nil {
		return err
	}
	log.Printf("List users: %d users found", len(listResp.Users))
	
	// Update user
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
	
	// Search users (streaming)
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
	
	// Delete user
	deleteResp, err := c.userClient.DeleteUser(ctx, &DeleteUserRequest{Id: createResp.User.Id})
	if err != nil {
		return err
	}
	log.Printf("Deleted user: %t", deleteResp.Success)
	
	return nil
}

func (c *GRPCClient) TestProductOperations() error {
	ctx := context.Background()
	
	// Create product
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
	
	// Get product
	getResp, err := c.productClient.GetProduct(ctx, &GetProductRequest{Id: createResp.Product.Id})
	if err != nil {
		return err
	}
	log.Printf("Got product: %+v", getResp.Product)
	
	// List products
	listResp, err := c.productClient.ListProducts(ctx, &ListProductsRequest{})
	if err != nil {
		return err
	}
	log.Printf("List products: %d products found", len(listResp.Products))
	
	// Update product
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
	
	// Search products (streaming)
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
	
	// Delete product
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
