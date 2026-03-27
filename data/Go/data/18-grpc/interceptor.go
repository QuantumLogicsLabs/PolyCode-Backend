package main

import (
	"context"
	"log"
	"time"

	"google.golang.org/grpc"
	"google.golang.org/grpc/codes"
	"google.golang.org/grpc/metadata"
	"google.golang.org/grpc/status"
)

// Unary logging interceptor
func unaryLoggingInterceptor(ctx context.Context, req interface{}, info *grpc.UnaryServerInfo, handler grpc.UnaryHandler) (interface{}, error) {
	start := time.Now()
	log.Printf("Unary call: %s", info.FullMethod)
	
	resp, err := handler(ctx, req)
	
	duration := time.Since(start)
	log.Printf("Unary call completed: %s in %v", info.FullMethod, duration)
	
	return resp, err
}

// Stream logging interceptor
func streamLoggingInterceptor(srv interface{}, ss grpc.ServerStream, info *grpc.StreamServerInfo, handler grpc.StreamHandler) error {
	start := time.Now()
	log.Printf("Stream call: %s", info.FullMethod)
	
	err := handler(srv, ss)
	
	duration := time.Since(start)
	log.Printf("Stream call completed: %s in %v", info.FullMethod, duration)
	
	return err
}

// Authentication interceptor
func unaryAuthInterceptor(ctx context.Context, req interface{}, info *grpc.UnaryServerInfo, handler grpc.UnaryHandler) (interface{}, error) {
	md, ok := metadata.FromIncomingContext(ctx)
	if !ok {
		return nil, status.Errorf(codes.Unauthenticated, "missing metadata")
	}
	
	tokens := md["authorization"]
	if len(tokens) == 0 {
		return nil, status.Errorf(codes.Unauthenticated, "missing authorization token")
	}
	
	token := tokens[0]
	if token != "Bearer valid-token" {
		return nil, status.Errorf(codes.Unauthenticated, "invalid token")
	}
	
	return handler(ctx, req)
}

// Rate limiting interceptor
func unaryRateLimitInterceptor(ctx context.Context, req interface{}, info *grpc.UnaryServerInfo, handler grpc.UnaryHandler) (interface{}, error) {
	// Simple rate limiting logic
	// In production, use a proper rate limiting solution
	return handler(ctx, req)
}

// Metrics interceptor
func unaryMetricsInterceptor(ctx context.Context, req interface{}, info *grpc.UnaryServerInfo, handler grpc.UnaryHandler) (interface{}, error) {
	// Record metrics before call
	start := time.Now()
	
	resp, err := handler(ctx, req)
	
	// Record metrics after call
	duration := time.Since(start)
	
	if err != nil {
		// Record error metrics
		log.Printf("Call failed: %s, error: %v, duration: %v", info.FullMethod, err, duration)
	} else {
		// Record success metrics
		log.Printf("Call succeeded: %s, duration: %v", info.FullMethod, duration)
	}
	
	return resp, err
}

// Validation interceptor
func unaryValidationInterceptor(ctx context.Context, req interface{}, info *grpc.UnaryServerInfo, handler grpc.UnaryHandler) (interface{}, error) {
	// Validate request based on method
	switch info.FullMethod {
	case "/user.UserService/CreateUser":
		if userReq, ok := req.(*CreateUserRequest); ok {
			if userReq.User.Name == "" {
				return nil, status.Errorf(codes.InvalidArgument, "user name cannot be empty")
			}
			if userReq.User.Email == "" {
				return nil, status.Errorf(codes.InvalidArgument, "user email cannot be empty")
			}
		}
	case "/product.ProductService/CreateProduct":
		if productReq, ok := req.(*CreateProductRequest); ok {
			if productReq.Product.Name == "" {
				return nil, status.Errorf(codes.InvalidArgument, "product name cannot be empty")
			}
			if productReq.Product.Price <= 0 {
				return nil, status.Errorf(codes.InvalidArgument, "product price must be positive")
			}
		}
	}
	
	return handler(ctx, req)
}

// Create a gRPC server with interceptors
func newGRPCServerWithInterceptors() *grpc.Server {
	opts := []grpc.ServerOption{
		grpc.UnaryInterceptor(unaryLoggingInterceptor),
		grpc.UnaryInterceptor(unaryAuthInterceptor),
		grpc.UnaryInterceptor(unaryMetricsInterceptor),
		grpc.UnaryInterceptor(unaryValidationInterceptor),
		grpc.StreamInterceptor(streamLoggingInterceptor),
	}
	
	return grpc.NewServer(opts...)
}
