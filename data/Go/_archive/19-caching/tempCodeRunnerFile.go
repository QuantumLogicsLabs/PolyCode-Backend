package main

import (
	"context"
	"fmt"
	"log"
	"time"
)

func main() {
	// Test different caching strategies
	fmt.Println("=== In-Memory Cache Demo ===")
	testInMemoryCache()
	
	fmt.Println("\n=== Redis Cache Demo ===")
	testRedisCache()
	
	fmt.Println("\n=== Cache-Aside Pattern Demo ===")
	testCacheAsidePattern()
	
	fmt.Println("\n=== Write-Through Cache Demo ===")
	testWriteThroughCache()
	
	fmt.Println("\n=== Write-Behind Cache Demo ===")
	testWriteBehindCache()
	
	fmt.Println("\n=== Cache Invalidation Demo ===")
	testCacheInvalidation()
}

func testInMemoryCache() {
	cache := NewInMemoryCache(10) // 10 items max
	
	// Set some values
	cache.Set("user:1", "John Doe", 5*time.Second)
	cache.Set("user:2", "Jane Smith", 5*time.Second)
	cache.Set("product:1", "Laptop", 10*time.Second)
	
	// Get values
	if val, found := cache.Get("user:1"); found {
		fmt.Printf("Found user:1 = %v\n", val)
	}
	
	if val, found := cache.Get("product:1"); found {
		fmt.Printf("Found product:1 = %v\n", val)
	}
	
	// Test expiration
	fmt.Println("Waiting 6 seconds for expiration...")
	time.Sleep(6 * time.Second)
	
	if _, found := cache.Get("user:1"); found {
		fmt.Println("user:1 still exists (unexpected)")
	} else {
		fmt.Println("user:1 expired as expected")
	}
	
	// Test cache stats
	stats := cache.GetStats()
	fmt.Printf("Cache stats: %+v\n", stats)
}

func testRedisCache() {
	cache := NewRedisCache("localhost:6379", "")
	
	// Set values
	err := cache.Set("user:1", "John Doe", 5*time.Second)
	if err != nil {
		log.Printf("Redis set error: %v", err)
		return
	}
	
	// Get values
	val, err := cache.Get("user:1")
	if err != nil {
		log.Printf("Redis get error: %v", err)
		return
	}
	
	fmt.Printf("Redis cache - user:1 = %v\n", val)
}

func testCacheAsidePattern() {
	cache := NewInMemoryCache(100)
	db := NewMockDatabase()
	service := NewUserServiceWithCacheAside(cache, db)
	
	// First call - cache miss, hits database
	user, err := service.GetUser("1")
	if err != nil {
		log.Printf("Error: %v", err)
		return
	}
	fmt.Printf("First call (cache miss): %+v\n", user)
	
	// Second call - cache hit
	user, err = service.GetUser("1")
	if err != nil {
		log.Printf("Error: %v", err)
		return
	}
	fmt.Printf("Second call (cache hit): %+v\n", user)
}

func testWriteThroughCache() {
	cache := NewInMemoryCache(100)
	db := NewMockDatabase()
	service := NewUserServiceWithWriteThrough(cache, db)
	
	// Create user - writes to both cache and database
	user := &User{ID: "2", Name: "Alice", Email: "alice@example.com"}
	err := service.CreateUser(user)
	if err != nil {
		log.Printf("Error: %v", err)
		return
	}
	
	// Read user - should hit cache
	retrievedUser, err := service.GetUser("2")
	if err != nil {
		log.Printf("Error: %v", err)
		return
	}
	fmt.Printf("Write-through - Retrieved user: %+v\n", retrievedUser)
}

func testWriteBehindCache() {
	cache := NewInMemoryCache(100)
	db := NewMockDatabase()
	service := NewUserServiceWithWriteBehind(cache, db)
	
	// Create user - writes to cache immediately, database asynchronously
	user := &User{ID: "3", Name: "Bob", Email: "bob@example.com"}
	err := service.CreateUser(user)
	if err != nil {
		log.Printf("Error: %v", err)
		return
	}
	
	// Read user - should hit cache
	retrievedUser, err := service.GetUser("3")
	if err != nil {
		log.Printf("Error: %v", err)
		return
	}
	fmt.Printf("Write-behind - Retrieved user: %+v\n", retrievedUser)
	
	// Wait for async write to complete
	time.Sleep(2 * time.Second)
}

func testCacheInvalidation() {
	cache := NewInMemoryCache(100)
	db := NewMockDatabase()
	service := NewUserServiceWithCacheInvalidation(cache, db)
	
	// Create user
	user := &User{ID: "4", Name: "Charlie", Email: "charlie@example.com"}
	err := service.CreateUser(user)
	if err != nil {
		log.Printf("Error: %v", err)
		return
	}
	
	// Read user (populates cache)
	retrievedUser, err := service.GetUser("4")
	if err != nil {
		log.Printf("Error: %v", err)
		return
	}
	fmt.Printf("Before update: %+v\n", retrievedUser)
	
	// Update user (invalidates cache)
	user.Name = "Charlie Updated"
	err = service.UpdateUser(user)
	if err != nil {
		log.Printf("Error: %v", err)
		return
	}
	
	// Read user again (cache miss, hits database)
	retrievedUser, err = service.GetUser("4")
	if err != nil {
		log.Printf("Error: %v", err)
		return
	}
	fmt.Printf("After update: %+v\n", retrievedUser)
}
