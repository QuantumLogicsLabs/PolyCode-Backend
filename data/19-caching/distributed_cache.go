package main

import (
	"context"
	"fmt"
	"log"
	"sync"
	"time"
)

// Distributed cache node
type CacheNode struct {
	ID       string
	Address  string
	Cache    Cache
	IsActive bool
	mu       sync.RWMutex
}

func NewCacheNode(id, address string) *CacheNode {
	return &CacheNode{
		ID:       id,
		Address:  address,
		Cache:    &InMemoryCacheAdapter{NewInMemoryCache(1000)},
		IsActive: true,
	}
}

func (n *CacheNode) Set(key string, value interface{}, ttl time.Duration) error {
	n.mu.RLock()
	defer n.mu.RUnlock()
	
	if !n.IsActive {
		return fmt.Errorf("node %s is inactive", n.ID)
	}
	
	n.Cache.Set(key, value, ttl)
	return nil
}

func (n *CacheNode) Get(key string) (interface{}, bool, error) {
	n.mu.RLock()
	defer n.mu.RUnlock()
	
	if !n.IsActive {
		return nil, false, fmt.Errorf("node %s is inactive", n.ID)
	}
	
	return n.Cache.Get(key)
}

func (n *CacheNode) Delete(key string) error {
	n.mu.RLock()
	defer n.mu.RUnlock()
	
	if !n.IsActive {
		return fmt.Errorf("node %s is inactive", n.ID)
	}
	
	n.Cache.Delete(key)
	return nil
}

func (n *CacheNode) SetActive(active bool) {
	n.mu.Lock()
	defer n.mu.Unlock()
	n.IsActive = active
}

// Consistent hash ring for cache distribution
type ConsistentHash struct {
	nodes       map[uint32]*CacheNode
	keys        []uint32
	replicas    int
	virtualNodes map[uint32]string
	mu          sync.RWMutex
}

func NewConsistentHash(replicas int) *ConsistentHash {
	return &ConsistentHash{
		nodes:        make(map[uint32]*CacheNode),
		keys:         []uint32{},
		replicas:     replicas,
		virtualNodes: make(map[uint32]string),
	}
}

func (ch *ConsistentHash) Add(node *CacheNode) {
	ch.mu.Lock()
	defer ch.mu.Unlock()
	
	for i := 0; i < ch.replicas; i++ {
		virtualKey := fmt.Sprintf("%s:%d", node.ID, i)
		hash := simpleHash(node.ID + fmt.Sprintf("%d", i))
		ch.nodes[hash] = node
		ch.virtualNodes[hash] = virtualKey
		ch.keys = append(ch.keys, hash)
	}
	
	// Sort keys
	ch.sortKeys()
}

func (ch *ConsistentHash) Remove(nodeID string) {
	ch.mu.Lock()
	defer ch.mu.Unlock()
	
	for i := 0; i < ch.replicas; i++ {
		virtualKey := fmt.Sprintf("%s:%d", nodeID, i)
		hash := simpleHash(nodeID + fmt.Sprintf("%d", i))
		delete(ch.nodes, hash)
		delete(ch.virtualNodes, hash)
		
		// Remove from keys slice
		for j, key := range ch.keys {
			if key == hash {
				ch.keys = append(ch.keys[:j], ch.keys[j+1:]...)
				break
			}
		}
	}
}

func (ch *ConsistentHash) Get(key string) *CacheNode {
	ch.mu.RLock()
	defer ch.mu.RUnlock()
	
	if len(ch.nodes) == 0 {
		return nil
	}
	
	hash := simpleHash(key)
	
	// Find first node with hash >= key hash
	for _, nodeHash := range ch.keys {
		if nodeHash >= hash {
			return ch.nodes[nodeHash]
		}
	}
	
	// Wrap around to first node
	return ch.nodes[ch.keys[0]]
}

func (ch *ConsistentHash) sortKeys() {
	// Simple bubble sort for demonstration
	for i := 0; i < len(ch.keys); i++ {
		for j := 0; j < len(ch.keys)-1-i; j++ {
			if ch.keys[j] > ch.keys[j+1] {
				ch.keys[j], ch.keys[j+1] = ch.keys[j+1], ch.keys[j]
			}
		}
	}
}

// Distributed cache cluster
type DistributedCache struct {
	nodes       []*CacheNode
	hashRing    *ConsistentHash
	replication int
	mu          sync.RWMutex
}

func NewDistributedCache(replication int) *DistributedCache {
	return &DistributedCache{
		nodes:       []*CacheNode{},
		hashRing:    NewConsistentHash(100), // 100 virtual nodes per physical node
		replication: replication,
	}
}

func (dc *DistributedCache) AddNode(node *CacheNode) {
	dc.mu.Lock()
	defer dc.mu.Unlock()
	
	dc.nodes = append(dc.nodes, node)
	dc.hashRing.Add(node)
}

func (dc *DistributedCache) RemoveNode(nodeID string) {
	dc.mu.Lock()
	defer dc.mu.Unlock()
	
	for i, node := range dc.nodes {
		if node.ID == nodeID {
			dc.nodes = append(dc.nodes[:i], dc.nodes[i+1:]...)
			break
		}
	}
	
	dc.hashRing.Remove(nodeID)
}

func (dc *DistributedCache) Set(key string, value interface{}, ttl time.Duration) error {
	dc.mu.RLock()
	defer dc.mu.RUnlock()
	
	// Get primary node
	primaryNode := dc.hashRing.Get(key)
	if primaryNode == nil {
		return fmt.Errorf("no nodes available")
	}
	
	// Set on primary node
	if err := primaryNode.Set(key, value, ttl); err != nil {
		return err
	}
	
	// Replicate to other nodes
	if dc.replication > 1 {
		dc.replicateSet(key, value, ttl, primaryNode)
	}
	
	return nil
}

func (dc *DistributedCache) Get(key string) (interface{}, bool, error) {
	dc.mu.RLock()
	defer dc.mu.RUnlock()
	
	// Try primary node first
	primaryNode := dc.hashRing.Get(key)
	if primaryNode == nil {
		return nil, false, fmt.Errorf("no nodes available")
	}
	
	if value, found, err := primaryNode.Get(key); err == nil && found {
		return value, true, nil
	}
	
	// Try other nodes if primary fails
	for _, node := range dc.nodes {
		if node.ID != primaryNode.ID {
			if value, found, err := node.Get(key); err == nil && found {
				// Repair cache - set value back on primary
				primaryNode.Set(key, value, 5*time.Minute)
				return value, true, nil
			}
		}
	}
	
	return nil, false, nil
}

func (dc *DistributedCache) Delete(key string) error {
	dc.mu.RLock()
	defer dc.mu.Unlock()
	
	// Delete from all nodes
	for _, node := range dc.nodes {
		if err := node.Delete(key); err != nil {
			log.Printf("Failed to delete key %s from node %s: %v", key, node.ID, err)
		}
	}
	
	return nil
}

func (dc *DistributedCache) replicateSet(key string, value interface{}, ttl time.Duration, exclude *CacheNode) {
	replicated := 0
	
	for _, node := range dc.nodes {
		if node.ID != exclude.ID && replicated < dc.replication-1 {
			if err := node.Set(key, value, ttl); err != nil {
				log.Printf("Failed to replicate key %s to node %s: %v", key, node.ID, err)
			} else {
				replicated++
			}
		}
	}
}

// Cache cluster health checker
type HealthChecker struct {
	cluster *DistributedCache
	ticker  *time.Ticker
}

func NewHealthChecker(cluster *DistributedCache) *HealthChecker {
	return &HealthChecker{
		cluster: cluster,
		ticker:  time.NewTicker(30 * time.Second),
	}
}

func (hc *HealthChecker) Start() {
	go hc.checkHealth()
}

func (hc *HealthChecker) Stop() {
	hc.ticker.Stop()
}

func (hc *HealthChecker) checkHealth() {
	for range hc.ticker.C {
		hc.cluster.mu.RLock()
		nodes := make([]*CacheNode, len(hc.cluster.nodes))
		copy(nodes, hc.cluster.nodes)
		hc.cluster.mu.RUnlock()
		
		for _, node := range nodes {
			// Simple health check - try to set and get a test key
			testKey := "health_check:" + node.ID
			err := node.Set(testKey, "ok", 10*time.Second)
			if err != nil {
				log.Printf("Node %s health check failed (set): %v", node.ID, err)
				node.SetActive(false)
				continue
			}
			
			_, found, err := node.Get(testKey)
			if err != nil || !found {
				log.Printf("Node %s health check failed (get): %v", node.ID, err)
				node.SetActive(false)
				continue
			}
			
			node.SetActive(true)
			node.Delete(testKey)
		}
	}
}

// Example usage
func setupDistributedCache() *DistributedCache {
	cluster := NewDistributedCache(2) // 2 replicas
	
	// Add nodes
	node1 := NewCacheNode("node1", "localhost:8001")
	node2 := NewCacheNode("node2", "localhost:8002")
	node3 := NewCacheNode("node3", "localhost:8003")
	
	cluster.AddNode(node1)
	cluster.AddNode(node2)
	cluster.AddNode(node3)
	
	// Start health checker
	healthChecker := NewHealthChecker(cluster)
	healthChecker.Start()
	
	return cluster
}
