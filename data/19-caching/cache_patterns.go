package main

import (
	"fmt"
	"log"
	"time"
)

// User model
type User struct {
	ID    string `json:"id"`
	Name  string `json:"name"`
	Email string `json:"email"`
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
	// Simulate database latency
	time.Sleep(100 * time.Millisecond)
	
	user, exists := db.users[id]
	if !exists {
		return nil, fmt.Errorf("user not found")
	}
	
	return user, nil
}

func (db *MockDatabase) CreateUser(user *User) error {
	time.Sleep(50 * time.Millisecond)
	db.users[user.ID] = user
	return nil
}

func (db *MockDatabase) UpdateUser(user *User) error {
	time.Sleep(75 * time.Millisecond)
	if _, exists := db.users[user.ID]; !exists {
		return fmt.Errorf("user not found")
	}
	db.users[user.ID] = user
	return nil
}

// Cache-Aside Pattern
type UserServiceWithCacheAside struct {
	cache Cache
	db    *MockDatabase
}

func NewUserServiceWithCacheAside(cache Cache, db *MockDatabase) *UserServiceWithCacheAside {
	return &UserServiceWithCacheAside{
		cache: cache,
		db:    db,
	}
}

func (s *UserServiceWithCacheAside) GetUser(id string) (*User, error) {
	// Try cache first
	cacheKey := "user:" + id
	if val, found := s.cache.Get(cacheKey); found {
		if user, ok := val.(*User); ok {
			return user, nil
		}
	}
	
	// Cache miss, hit database
	user, err := s.db.GetUser(id)
	if err != nil {
		return nil, err
	}
	
	// Populate cache
	s.cache.Set(cacheKey, user, 5*time.Minute)
	
	return user, nil
}

func (s *UserServiceWithCacheAside) CreateUser(user *User) error {
	err := s.db.CreateUser(user)
	if err != nil {
		return err
	}
	
	// No cache write - let next read populate it
	return nil
}

// Write-Through Pattern
type UserServiceWithWriteThrough struct {
	cache Cache
	db    *MockDatabase
}

func NewUserServiceWithWriteThrough(cache Cache, db *MockDatabase) *UserServiceWithWriteThrough {
	return &UserServiceWithWriteThrough{
		cache: cache,
		db:    db,
	}
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
	// Write to database first
	err := s.db.CreateUser(user)
	if err != nil {
		return err
	}
	
	// Then write to cache
	cacheKey := "user:" + user.ID
	s.cache.Set(cacheKey, user, 5*time.Minute)
	
	return nil
}

// Write-Behind (Write-Back) Pattern
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
	
	// Start background writer
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
	// Write to cache immediately
	cacheKey := "user:" + user.ID
	s.cache.Set(cacheKey, user, 5*time.Minute)
	
	// Queue for async database write
	select {
	case s.queue <- user:
		return nil
	default:
		log.Printf("Queue full, dropping write for user %s", user.ID)
		return fmt.Errorf("write queue full")
	}
}

func (s *UserServiceWithWriteBehind) backgroundWriter() {
	for user := range s.queue {
		if err := s.db.CreateUser(user); err != nil {
			log.Printf("Failed to write user %s to database: %v", user.ID, err)
			// Could retry here
		}
	}
}

// Cache Invalidation Pattern
type UserServiceWithCacheInvalidation struct {
	cache Cache
	db    *MockDatabase
}

func NewUserServiceWithCacheInvalidation(cache Cache, db *MockDatabase) *UserServiceWithCacheInvalidation {
	return &UserServiceWithCacheInvalidation{
		cache: cache,
		db:    db,
	}
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
	err := s.db.CreateUser(user)
	if err != nil {
		return err
	}
	
	// Invalidate any existing cache entry (though there shouldn't be one for new users)
	cacheKey := "user:" + user.ID
	s.cache.Delete(cacheKey)
	
	return nil
}

func (s *UserServiceWithCacheInvalidation) UpdateUser(user *User) error {
	err := s.db.UpdateUser(user)
	if err != nil {
		return err
	}
	
	// Invalidate cache entry
	cacheKey := "user:" + user.ID
	s.cache.Delete(cacheKey)
	
	return nil
}

// Cache interface
type Cache interface {
	Set(key string, value interface{}, ttl time.Duration)
	Get(key string) (interface{}, bool)
	Delete(key string)
}

// Adapter for InMemoryCache to Cache interface
type InMemoryCacheAdapter struct {
	*InMemoryCache
}

func (a *InMemoryCacheAdapter) Set(key string, value interface{}, ttl time.Duration) {
	a.InMemoryCache.Set(key, value, ttl)
}

func (a *InMemoryCacheAdapter) Get(key string) (interface{}, bool) {
	return a.InMemoryCache.Get(key)
}

func (a *InMemoryCacheAdapter) Delete(key string) {
	a.InMemoryCache.Delete(key)
}
