package main

import (
	"context"
	"fmt"
	"log"
	"sync"
)

type ProductServiceServer struct {
	UnimplementedProductServiceServer
	products map[string]*Product
	mu       sync.RWMutex
}

func NewProductServiceServer() *ProductServiceServer {
	products := make(map[string]*Product)
	products["1"] = &Product{Id: "1", Name: "Laptop", Price: 999.99, Category: "Electronics"}
	products["2"] = &Product{Id: "2", Name: "Book", Price: 19.99, Category: "Books"}
	products["3"] = &Product{Id: "3", Name: "Coffee Mug", Price: 12.99, Category: "Kitchen"}
	
	return &ProductServiceServer{
		products: products,
	}
}

func (s *ProductServiceServer) GetProduct(ctx context.Context, req *GetProductRequest) (*GetProductResponse, error) {
	log.Printf("GetProduct called with ID: %s", req.Id)
	
	s.mu.RLock()
	defer s.mu.RUnlock()
	
	product, exists := s.products[req.Id]
	if !exists {
		return nil, fmt.Errorf("product not found")
	}
	
	return &GetProductResponse{
		Product: product,
	}, nil
}

func (s *ProductServiceServer) ListProducts(ctx context.Context, req *ListProductsRequest) (*ListProductsResponse, error) {
	log.Println("ListProducts called")
	
	s.mu.RLock()
	defer s.mu.RUnlock()
	
	var products []*Product
	for _, product := range s.products {
		products = append(products, product)
	}
	
	return &ListProductsResponse{
		Products: products,
	}, nil
}

func (s *ProductServiceServer) CreateProduct(ctx context.Context, req *CreateProductRequest) (*CreateProductResponse, error) {
	log.Printf("CreateProduct called with Name: %s", req.Product.Name)
	
	s.mu.Lock()
	defer s.mu.Unlock()
	
	id := fmt.Sprintf("%d", len(s.products)+1)
	product := &Product{
		Id:       id,
		Name:     req.Product.Name,
		Price:    req.Product.Price,
		Category: req.Product.Category,
	}
	
	s.products[id] = product
	
	return &CreateProductResponse{
		Product: product,
	}, nil
}

func (s *ProductServiceServer) UpdateProduct(ctx context.Context, req *UpdateProductRequest) (*UpdateProductResponse, error) {
	log.Printf("UpdateProduct called with ID: %s", req.Product.Id)
	
	s.mu.Lock()
	defer s.mu.Unlock()
	
	product, exists := s.products[req.Product.Id]
	if !exists {
		return nil, fmt.Errorf("product not found")
	}
	
	product.Name = req.Product.Name
	product.Price = req.Product.Price
	product.Category = req.Product.Category
	
	return &UpdateProductResponse{
		Product: product,
	}, nil
}

func (s *ProductServiceServer) DeleteProduct(ctx context.Context, req *DeleteProductRequest) (*DeleteProductResponse, error) {
	log.Printf("DeleteProduct called with ID: %s", req.Id)
	
	s.mu.Lock()
	defer s.mu.Unlock()
	
	_, exists := s.products[req.Id]
	if !exists {
		return nil, fmt.Errorf("product not found")
	}
	
	delete(s.products, req.Id)
	
	return &DeleteProductResponse{
		Success: true,
	}, nil
}

func (s *ProductServiceServer) SearchProducts(req *SearchProductsRequest, stream ProductService_SearchProductsServer) error {
	log.Printf("SearchProducts called with query: %s", req.Query)
	
	s.mu.RLock()
	defer s.mu.RUnlock()
	
	for _, product := range s.products {
		// Search by name or category
		if product.Name == req.Query || product.Category == req.Query {
			if err := stream.Send(&SearchProductsResponse{Product: product}); err != nil {
				return err
			}
		}
	}
	
	return nil
}
