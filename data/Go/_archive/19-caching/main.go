package main

import (
	"fmt"
	"log"
	"sync"
	"time"
)

// User model
type User struct {
	ID    string `json:"id"`
	Name  string `json:"name"`
	Email string `json:"email"`
}

// In-Memory Cache Implementation
type CacheItem struct {
	Value     interface{}
	ExpiresAt time.Time
}

type InMemoryCache struct {
	items   map[string]*CacheItem
	mu      sync.RWMutex
	maxSize int
	stats   CacheStats
}

type CacheStats struct {
	Hits       int
	Misses     int
	Sets       int
	Evictions  int
	TotalItems int
}

func NewInMemoryCache(maxSize int) *InMemoryCache {
	cache := &InMemoryCache{
		items:   make(map[string]*CacheItem),
		maxSize: maxSize,
	}
	go cache.cleanupExpired()
	return cache
}

func (c *InMemoryCache) Set(key string, value interface{}, ttl time.Duration) {
	c.mu.Lock()
	defer c.mu.Unlock()

	if len(c.items) >= c.maxSize {
		for k := range c.items {
			delete(c.items, k)
			c.stats.Evictions++
			break
		}
	}

	c.items[key] = &CacheItem{
		Value:     value,
		ExpiresAt: time.Now().Add(ttl),
	}
	c.stats.Sets++
	c.stats.TotalItems = len(c.items)
}

func (c *InMemoryCache) Get(key string) (interface{}, bool) {
	c.mu.RLock()
	defer c.mu.RUnlock()

	item, exists := c.items[key]
	if !exists {
		c.stats.Misses++
		return nil, false
	}

	if time.Now().After(item.ExpiresAt) {
		c.stats.Misses++
		return nil, false
	}

	c.stats.Hits++
	return item.Value, true
}

func (c *InMemoryCache) Delete(key string) {
	c.mu.Lock()
	defer c.mu.Unlock()
	delete(c.items, key)
	c.stats.TotalItems = len(c.items)
}

func (c *InMemoryCache) GetStats() CacheStats {
	c.mu.RLock()
	defer c.mu.RUnlock()
	return c.stats
}

func (c *InMemoryCache) cleanupExpired() {
	ticker := time.NewTicker(1 * time.Minute)
	defer ticker.Stop()

	for range ticker.C {
		c.mu.Lock()
		now := time.Now()
		for key, item := range c.items {
			if now.After(item.ExpiresAt) {
				delete(c.items, key)
				c.stats.Evictions++
			}
		}
		c.stats.TotalItems = len(c.items)
		c.mu.Unlock()
	}
}

// Cache interface
type Cache interface {
	Set(key string, value interface{}, ttl time.Duration)
	Get(key string) (interface{}, bool)
	Delete(key string)
}

// Mock database
type MockDatabase struct {
	users map[string]*User
}

func NewMockDatabase() *MockDatabase {
	users := make(map[string]*User)
	users["1"] = &User{ID: "1", Name: "John Doe", Email: "john@example.com"}
	users["2"] = &User{ID: "2", Name: "Jane Smith", Email: "jane@example.com"}
	return &MockDatabase{users: users}
}

func (db *MockDatabase) GetUser(id string) (*User, error) {
	time.Sleep(50 * time.Millisecond)
	user, exists := db.users[id]
	if !exists {
		return nil, fmt.Errorf("user not found")
	}
	return user, nil
}

func (db *MockDatabase) CreateUser(user *User) error {
	time.Sleep(30 * time.Millisecond)
	db.users[user.ID] = user
	return nil
}

func (db *MockDatabase) UpdateUser(user *User) error {
	time.Sleep(30 * time.Millisecond)
	if _, exists := db.users[user.ID]; !exists {
		return fmt.Errorf("user not found")
	}
	db.users[user.ID] = user
	return nil
}

// Patterns
type UserServiceWithCacheAside struct {
	cache Cache
	db    *MockDatabase
}

func NewUserServiceWithCacheAside(cache Cache, db *MockDatabase) *UserServiceWithCacheAside {
	return &UserServiceWithCacheAside{cache: cache, db: db}
}

func (s *UserServiceWithCacheAside) GetUser(id string) (*User, error) {
	cacheKey := "user:" + id
	if val, found := s.cache.Get(cacheKey); found {
		if user, ok := val.(*User); ok {
			return user, nil
		}
	}

	user, err := s.db.GetUser(id)
	if err != nil {
		return nil, err
	}
	s.cache.Set(cacheKey, user, 5*time.Minute)
	return user, nil
}

type UserServiceWithWriteThrough struct {
	cache Cache
	db    *MockDatabase
}

func NewUserServiceWithWriteThrough(cache Cache, db *MockDatabase) *UserServiceWithWriteThrough {
	return &UserServiceWithWriteThrough{cache: cache, db: db}
}

func (s *UserServiceWithWriteThrough) GetUser(id string) (*User, error) {
	cacheKey := "user:" + id
	if val, found := s.cache.Get(cacheKey); found {
		if user, ok := val.(*User); ok {
			return user, nil
		}
	}
	user, err := s.db.GetUser(id)
	if err != nil {
		return nil, err
	}
	s.cache.Set(cacheKey, user, 5*time.Minute)
	return user, nil
}

func (s *UserServiceWithWriteThrough) CreateUser(user *User) error {
	if err := s.db.CreateUser(user); err != nil {
		return err
	}
	s.cache.Set("user:"+user.ID, user, 5*time.Minute)
	return nil
}

type UserServiceWithWriteBehind struct {
	cache Cache
	db    *MockDatabase
	queue chan *User
}

func NewUserServiceWithWriteBehind(cache Cache, db *MockDatabase) *UserServiceWithWriteBehind {
	service := &UserServiceWithWriteBehind{
		cache: cache,
		db:    db,
		queue: make(chan *User, 100),
	}
	go service.backgroundWriter()
	return service
}

func (s *UserServiceWithWriteBehind) GetUser(id string) (*User, error) {
	cacheKey := "user:" + id
	if val, found := s.cache.Get(cacheKey); found {
		if user, ok := val.(*User); ok {
			return user, nil
		}
	}
	user, err := s.db.GetUser(id)
	if err != nil {
		return nil, err
	}
	s.cache.Set(cacheKey, user, 5*time.Minute)
	return user, nil
}

func (s *UserServiceWithWriteBehind) CreateUser(user *User) error {
	s.cache.Set("user:"+user.ID, user, 5*time.Minute)
	select {
	case s.queue <- user:
		return nil
	default:
		return fmt.Errorf("write queue full")
	}
}

func (s *UserServiceWithWriteBehind) backgroundWriter() {
	for user := range s.queue {
		_ = s.db.CreateUser(user)
	}
}

type UserServiceWithCacheInvalidation struct {
	cache Cache
	db    *MockDatabase
}

func NewUserServiceWithCacheInvalidation(cache Cache, db *MockDatabase) *UserServiceWithCacheInvalidation {
	return &UserServiceWithCacheInvalidation{cache: cache, db: db}
}

func (s *UserServiceWithCacheInvalidation) GetUser(id string) (*User, error) {
	cacheKey := "user:" + id
	if val, found := s.cache.Get(cacheKey); found {
		if user, ok := val.(*User); ok {
			return user, nil
		}
	}
	user, err := s.db.GetUser(id)
	if err != nil {
		return nil, err
	}
	s.cache.Set(cacheKey, user, 5*time.Minute)
	return user, nil
}

func (s *UserServiceWithCacheInvalidation) CreateUser(user *User) error {
	if err := s.db.CreateUser(user); err != nil {
		return err
	}
	s.cache.Delete("user:" + user.ID)
	return nil
}

func (s *UserServiceWithCacheInvalidation) UpdateUser(user *User) error {
	if err := s.db.UpdateUser(user); err != nil {
		return err
	}
	s.cache.Delete("user:" + user.ID)
	return nil
}

// Redis Mock
type RedisCache interface {
	Set(key string, value interface{}, ttl time.Duration) error
	Get(key string) (interface{}, error)
}

type MockRedisCache struct {
	data map[string]*CacheItem
}

func NewMockRedisCache() *MockRedisCache {
	return &MockRedisCache{data: make(map[string]*CacheItem)}
}

func (r *MockRedisCache) Set(key string, value interface{}, ttl time.Duration) error {
	r.data[key] = &CacheItem{
		Value:     value,
		ExpiresAt: time.Now().Add(ttl),
	}
	return nil
}

func (r *MockRedisCache) Get(key string) (interface{}, error) {
	item, exists := r.data[key]
	if !exists {
		return nil, fmt.Errorf("key not found")
	}
	if time.Now().After(item.ExpiresAt) {
		delete(r.data, key)
		return nil, fmt.Errorf("key expired")
	}
	return item.Value, nil
}

func NewRedisCache(addr, password string) RedisCache {
	return NewMockRedisCache()
}

// Main function
func main() {
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
	cache := NewInMemoryCache(10)

	cache.Set("user:1", "John Doe", 2*time.Second)
	cache.Set("product:1", "Laptop", 2*time.Second)

	if val, found := cache.Get("user:1"); found {
		fmt.Printf("Found user:1 = %v\n", val)
	}

	if val, found := cache.Get("product:1"); found {
		fmt.Printf("Found product:1 = %v\n", val)
	}

	stats := cache.GetStats()
	fmt.Printf("Cache stats: %+v\n", stats)
}

func testRedisCache() {
	cache := NewRedisCache("localhost:6379", "")

	err := cache.Set("user:1", "John Doe", 5*time.Second)
	if err != nil {
		log.Printf("Redis set error: %v", err)
		return
	}

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

	user, _ := service.GetUser("1")
	fmt.Printf("First call (cache miss): %+v\n", user)

	user, _ = service.GetUser("1")
	fmt.Printf("Second call (cache hit): %+v\n", user)
}

func testWriteThroughCache() {
	cache := NewInMemoryCache(100)
	db := NewMockDatabase()
	service := NewUserServiceWithWriteThrough(cache, db)

	user := &User{ID: "2", Name: "Alice", Email: "alice@example.com"}
	_ = service.CreateUser(user)

	retrievedUser, _ := service.GetUser("2")
	fmt.Printf("Write-through - Retrieved user: %+v\n", retrievedUser)
}

func testWriteBehindCache() {
	cache := NewInMemoryCache(100)
	db := NewMockDatabase()
	service := NewUserServiceWithWriteBehind(cache, db)

	user := &User{ID: "3", Name: "Bob", Email: "bob@example.com"}
	_ = service.CreateUser(user)

	retrievedUser, _ := service.GetUser("3")
	fmt.Printf("Write-behind - Retrieved user: %+v\n", retrievedUser)
	time.Sleep(100 * time.Millisecond)
}

func testCacheInvalidation() {
	cache := NewInMemoryCache(100)
	db := NewMockDatabase()
	service := NewUserServiceWithCacheInvalidation(cache, db)

	user := &User{ID: "4", Name: "Charlie", Email: "charlie@example.com"}
	_ = service.CreateUser(user)

	retrievedUser, _ := service.GetUser("4")
	fmt.Printf("Before update: %+v\n", retrievedUser)

	user.Name = "Charlie Updated"
	_ = service.UpdateUser(user)

	retrievedUser, _ = service.GetUser("4")
	fmt.Printf("After update: %+v\n", retrievedUser)
}
