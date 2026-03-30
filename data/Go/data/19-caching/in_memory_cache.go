package main

import (
	"sync"
	"time"
)

type CacheItem struct {
	Value     interface{}
	ExpiresAt time.Time
}

type InMemoryCache struct {
	items map[string]*CacheItem
	mu    sync.RWMutex
	maxSize int
	stats CacheStats
}

type CacheStats struct {
	Hits        int
	Misses      int
	Sets        int
	Evictions   int
	TotalItems  int
}

func NewInMemoryCache(maxSize int) *InMemoryCache {
	cache := &InMemoryCache{
		items:   make(map[string]*CacheItem),
		maxSize: maxSize,
	}
	
	// Start cleanup goroutine
	go cache.cleanupExpired()
	
	return cache
}

func (c *InMemoryCache) Set(key string, value interface{}, ttl time.Duration) {
	c.mu.Lock()
	defer c.mu.Unlock()
	
	// Check if we need to evict items
	if len(c.items) >= c.maxSize {
		c.evictLRU()
	}
	
	expiresAt := time.Now().Add(ttl)
	c.items[key] = &CacheItem{
		Value:     value,
		ExpiresAt: expiresAt,
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
	
	// Check if item has expired
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

func (c *InMemoryCache) Clear() {
	c.mu.Lock()
	defer c.mu.Unlock()
	
	c.items = make(map[string]*CacheItem)
	c.stats.TotalItems = 0
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

func (c *InMemoryCache) evictLRU() {
	// Simple LRU eviction - remove the first item
	// In production, use a proper LRU algorithm
	for key := range c.items {
		delete(c.items, key)
		c.stats.Evictions++
		break
	}
}

// Thread-safe cache with multiple shards for better performance
type ShardedCache struct {
	shards    []*InMemoryCache
	shardMask uint32
}

func NewShardedCache(maxSize int, numShards int) *ShardedCache {
	shardSize := maxSize / numShards
	if shardSize < 1 {
		shardSize = 1
	}
	
	shards := make([]*InMemoryCache, numShards)
	for i := range shards {
		shards[i] = NewInMemoryCache(shardSize)
	}
	
	return &ShardedCache{
		shards:    shards,
		shardMask: uint32(numShards - 1),
	}
}

func (sc *ShardedCache) getShard(key string) *InMemoryCache {
	hash := simpleHash(key)
	return sc.shards[hash&sc.shardMask]
}

func (sc *ShardedCache) Set(key string, value interface{}, ttl time.Duration) {
	shard := sc.getShard(key)
	shard.Set(key, value, ttl)
}

func (sc *ShardedCache) Get(key string) (interface{}, bool) {
	shard := sc.getShard(key)
	return shard.Get(key)
}

func (sc *ShardedCache) Delete(key string) {
	shard := sc.getShard(key)
	shard.Delete(key)
}

func (sc *ShardedCache) GetStats() []CacheStats {
	stats := make([]CacheStats, len(sc.shards))
	for i, shard := range sc.shards {
		stats[i] = shard.GetStats()
	}
	return stats
}

func simpleHash(s string) uint32 {
	hash := uint32(2166136261)
	for _, c := range s {
		hash ^= uint32(c)
		hash *= 16777619
	}
	return hash
}
