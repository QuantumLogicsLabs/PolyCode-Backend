package main

import (
	"context"
	"encoding/json"
	"fmt"
	"time"
)

// RedisCache interface
type RedisCache interface {
	Set(key string, value interface{}, ttl time.Duration) error
	Get(key string) (interface{}, error)
	Delete(key string) error
	Clear() error
	Exists(key string) (bool, error)
	SetNX(key string, value interface{}, ttl time.Duration) (bool, error)
	Expire(key string, ttl time.Duration) error
	TTL(key string) (time.Duration, error)
}

// Mock Redis implementation for demonstration
type MockRedisCache struct {
	data map[string]*CacheItem
}

func NewMockRedisCache() *MockRedisCache {
	return &MockRedisCache{
		data: make(map[string]*CacheItem),
	}
}

func (r *MockRedisCache) Set(key string, value interface{}, ttl time.Duration) error {
	expiresAt := time.Now().Add(ttl)
	r.data[key] = &CacheItem{
		Value:     value,
		ExpiresAt: expiresAt,
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

func (r *MockRedisCache) Delete(key string) error {
	delete(r.data, key)
	return nil
}

func (r *MockRedisCache) Clear() error {
	r.data = make(map[string]*CacheItem)
	return nil
}

func (r *MockRedisCache) Exists(key string) (bool, error) {
	item, exists := r.data[key]
	if !exists {
		return false, nil
	}
	
	if time.Now().After(item.ExpiresAt) {
		delete(r.data, key)
		return false, nil
	}
	
	return true, nil
}

func (r *MockRedisCache) SetNX(key string, value interface{}, ttl time.Duration) (bool, error) {
	exists, err := r.Exists(key)
	if err != nil {
		return false, err
	}
	
	if exists {
		return false, nil
	}
	
	return true, r.Set(key, value, ttl)
}

func (r *MockRedisCache) Expire(key string, ttl time.Duration) error {
	item, exists := r.data[key]
	if !exists {
		return fmt.Errorf("key not found")
	}
	
	item.ExpiresAt = time.Now().Add(ttl)
	return nil
}

func (r *MockRedisCache) TTL(key string) (time.Duration, error) {
	item, exists := r.data[key]
	if !exists {
		return 0, fmt.Errorf("key not found")
	}
	
	ttl := time.Until(item.ExpiresAt)
	if ttl <= 0 {
		delete(r.data, key)
		return 0, fmt.Errorf("key expired")
	}
	
	return ttl, nil
}

// RedisCache wrapper with serialization
type RedisCacheWrapper struct {
	cache RedisCache
}

func NewRedisCacheWrapper(cache RedisCache) *RedisCacheWrapper {
	return &RedisCacheWrapper{cache: cache}
}

func (r *RedisCacheWrapper) SetJSON(key string, value interface{}, ttl time.Duration) error {
	jsonData, err := json.Marshal(value)
	if err != nil {
		return err
	}
	
	return r.cache.Set(key, jsonData, ttl)
}

func (r *RedisCacheWrapper) GetJSON(key string, dest interface{}) error {
	value, err := r.cache.Get(key)
	if err != nil {
		return err
	}
	
	jsonData, ok := value.([]byte)
	if !ok {
		return fmt.Errorf("invalid data type in cache")
	}
	
	return json.Unmarshal(jsonData, dest)
}

func (r *RedisCacheWrapper) SetString(key string, value string, ttl time.Duration) error {
	return r.cache.Set(key, value, ttl)
}

func (r *RedisCacheWrapper) GetString(key string) (string, error) {
	value, err := r.cache.Get(key)
	if err != nil {
		return "", err
	}
	
	str, ok := value.(string)
	if !ok {
		return "", fmt.Errorf("value is not a string")
	}
	
	return str, nil
}

func (r *RedisCacheWrapper) SetInt(key string, value int, ttl time.Duration) error {
	return r.cache.Set(key, value, ttl)
}

func (r *RedisCacheWrapper) GetInt(key string) (int, error) {
	value, err := r.cache.Get(key)
	if err != nil {
		return 0, err
	}
	
	i, ok := value.(int)
	if !ok {
		return 0, fmt.Errorf("value is not an int")
	}
	
	return i, nil
}

// Distributed lock using Redis
type RedisLock struct {
	cache RedisCache
}

func NewRedisLock(cache RedisCache) *RedisLock {
	return &RedisLock{cache: cache}
}

func (l *RedisLock) Acquire(key string, ttl time.Duration) (bool, error) {
	return l.cache.SetNX(key, "locked", ttl)
}

func (l *RedisLock) Release(key string) error {
	return l.cache.Delete(key)
}

func (l *RedisLock) TryAcquire(key string, ttl time.Duration, timeout time.Duration) (bool, error) {
	start := time.Now()
	for time.Since(start) < timeout {
		acquired, err := l.Acquire(key, ttl)
		if err != nil {
			return false, err
		}
		if acquired {
			return true, nil
		}
		time.Sleep(10 * time.Millisecond)
	}
	return false, nil
}

// Rate limiter using Redis
type RedisRateLimiter struct {
	cache RedisCache
}

func NewRedisRateLimiter(cache RedisCache) *RedisRateLimiter {
	return &RedisRateLimiter{cache: cache}
}

func (r *RedisRateLimiter) Allow(key string, limit int, window time.Duration) (bool, error) {
	current, err := r.cache.Get(key)
	if err != nil {
		// Key doesn't exist, create it
		r.cache.Set(key, 1, window)
		return true, nil
	}
	
	count, ok := current.(int)
	if !ok {
		return false, fmt.Errorf("invalid counter type")
	}
	
	if count >= limit {
		return false, nil
	}
	
	// Increment counter
	r.cache.Set(key, count+1, window)
	return true, nil
}

// Factory function to create Redis cache
func NewRedisCache(addr, password string) RedisCache {
	// In a real implementation, you would connect to actual Redis
	// For this demo, we'll use the mock implementation
	return NewMockRedisCache()
}
