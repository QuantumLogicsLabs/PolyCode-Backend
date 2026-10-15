package main

import (
	"fmt"
	"sync"
	"time"
)

// RateLimiter interface
type RateLimiter interface {
	Allow(identifier string) bool
	AllowN(identifier string, n int) bool
	Reset(identifier string)
	GetStats(identifier string) RateLimitStats
}

type RateLimitStats struct {
	CurrentTokens  int       `json:"current_tokens"`
	LastRefill     time.Time `json:"last_refill"`
	TotalRequests  int       `json:"total_requests"`
	DeniedRequests int       `json:"denied_requests"`
}

// TokenBucketLimiter implements token bucket rate limiting
type TokenBucketLimiter struct {
	buckets    map[string]*TokenBucket
	capacity   int
	refillRate int // tokens per second
	mu         sync.RWMutex
}

type TokenBucket struct {
	tokens     int
	capacity   int
	refillRate int
	lastRefill time.Time
	stats      RateLimitStats
}

func NewTokenBucketLimiter(capacity, refillRate int) *TokenBucketLimiter {
	return &TokenBucketLimiter{
		buckets:    make(map[string]*TokenBucket),
		capacity:   capacity,
		refillRate: refillRate,
	}
}

func (t *TokenBucketLimiter) Allow(identifier string) bool {
	return t.AllowN(identifier, 1)
}

func (t *TokenBucketLimiter) AllowN(identifier string, n int) bool {
	t.mu.Lock()
	defer t.mu.Unlock()

	bucket, exists := t.buckets[identifier]
	if !exists {
		bucket = &TokenBucket{
			tokens:     t.capacity,
			capacity:   t.capacity,
			refillRate: t.refillRate,
			lastRefill: time.Now(),
		}
		t.buckets[identifier] = bucket
	}

	t.refill(bucket)

	if bucket.tokens >= n {
		bucket.tokens -= n
		bucket.stats.TotalRequests++
		return true
	}

	bucket.stats.DeniedRequests++
	return false
}

func (t *TokenBucketLimiter) Reset(identifier string) {
	t.mu.Lock()
	defer t.mu.Unlock()
	delete(t.buckets, identifier)
}

func (t *TokenBucketLimiter) GetStats(identifier string) RateLimitStats {
	t.mu.RLock()
	defer t.mu.RUnlock()

	if bucket, exists := t.buckets[identifier]; exists {
		return bucket.stats
	}
	return RateLimitStats{}
}

func (t *TokenBucketLimiter) refill(bucket *TokenBucket) {
	now := time.Now()
	elapsed := now.Sub(bucket.lastRefill)
	tokensToAdd := int(elapsed.Seconds() * float64(t.refillRate))

	if tokensToAdd > 0 {
		bucket.tokens += tokensToAdd
		if bucket.tokens > bucket.capacity {
			bucket.tokens = bucket.capacity
		}
		bucket.lastRefill = now
	}
}

// SlidingWindowLimiter implements sliding window rate limiting
type SlidingWindowLimiter struct {
	windows map[string]*SlidingWindow
	window  time.Duration
	limit   int
	mu      sync.RWMutex
}

type SlidingWindow struct {
	requests []time.Time
	window   time.Duration
	limit    int
	stats    RateLimitStats
}

func NewSlidingWindowLimiter(window time.Duration, limit int) *SlidingWindowLimiter {
	return &SlidingWindowLimiter{
		windows: make(map[string]*SlidingWindow),
		window:  window,
		limit:   limit,
	}
}

func (s *SlidingWindowLimiter) Allow(identifier string) bool {
	return s.AllowN(identifier, 1)
}

func (s *SlidingWindowLimiter) AllowN(identifier string, n int) bool {
	s.mu.Lock()
	defer s.mu.Unlock()

	window, exists := s.windows[identifier]
	if !exists {
		window = &SlidingWindow{
			requests: []time.Time{},
			window:   s.window,
			limit:    s.limit,
		}
		s.windows[identifier] = window
	}

	now := time.Now()
	cutoff := now.Add(-s.window)
	var validRequests []time.Time
	for _, req := range window.requests {
		if req.After(cutoff) {
			validRequests = append(validRequests, req)
		}
	}
	window.requests = validRequests

	if len(window.requests)+n <= window.limit {
		for i := 0; i < n; i++ {
			window.requests = append(window.requests, now)
		}
		window.stats.TotalRequests++
		return true
	}

	window.stats.DeniedRequests++
	return false
}

func (s *SlidingWindowLimiter) Reset(identifier string) {
	s.mu.Lock()
	defer s.mu.Unlock()
	delete(s.windows, identifier)
}

func (s *SlidingWindowLimiter) GetStats(identifier string) RateLimitStats {
	s.mu.RLock()
	defer s.mu.RUnlock()

	if window, exists := s.windows[identifier]; exists {
		return window.stats
	}
	return RateLimitStats{}
}

// FixedWindowLimiter implements fixed window rate limiting
type FixedWindowLimiter struct {
	counters map[string]*FixedWindowCounter
	window   time.Duration
	limit    int
	mu       sync.RWMutex
}

type FixedWindowCounter struct {
	count       int
	window      time.Duration
	limit       int
	windowStart time.Time
	stats       RateLimitStats
}

func NewFixedWindowLimiter(window time.Duration, limit int) *FixedWindowLimiter {
	return &FixedWindowLimiter{
		counters: make(map[string]*FixedWindowCounter),
		window:   window,
		limit:    limit,
	}
}

func (f *FixedWindowLimiter) Allow(identifier string) bool {
	return f.AllowN(identifier, 1)
}

func (f *FixedWindowLimiter) AllowN(identifier string, n int) bool {
	f.mu.Lock()
	defer f.mu.Unlock()

	counter, exists := f.counters[identifier]
	if !exists {
		counter = &FixedWindowCounter{
			count:       0,
			window:      f.window,
			limit:       f.limit,
			windowStart: time.Now(),
		}
		f.counters[identifier] = counter
	}

	now := time.Now()
	if now.Sub(counter.windowStart) >= counter.window {
		counter.count = 0
		counter.windowStart = now
	}

	if counter.count+n <= counter.limit {
		counter.count += n
		counter.stats.TotalRequests++
		return true
	}

	counter.stats.DeniedRequests++
	return false
}

func (f *FixedWindowLimiter) Reset(identifier string) {
	f.mu.Lock()
	defer f.mu.Unlock()
	delete(f.counters, identifier)
}

func (f *FixedWindowLimiter) GetStats(identifier string) RateLimitStats {
	f.mu.RLock()
	defer f.mu.RUnlock()

	if counter, exists := f.counters[identifier]; exists {
		return counter.stats
	}
	return RateLimitStats{}
}

// MultiTierRateLimiter for different user tiers
type MultiTierRateLimiter struct {
	limiter    RateLimiter
	tierLimits map[string]int
}

func NewMultiTierRateLimiter(limiter RateLimiter) *MultiTierRateLimiter {
	return &MultiTierRateLimiter{
		limiter: limiter,
		tierLimits: map[string]int{
			"free":       100,
			"basic":      1000,
			"premium":    10000,
			"enterprise": 100000,
		},
	}
}

func (m *MultiTierRateLimiter) Allow(identifier string, tier string) bool {
	compositeID := fmt.Sprintf("%s:%s", tier, identifier)
	return m.limiter.Allow(compositeID)
}

func (m *MultiTierRateLimiter) SetTierLimit(tier string, limit int) {
	m.tierLimits[tier] = limit
}

func (m *MultiTierRateLimiter) GetTierLimit(tier string) int {
	if limit, exists := m.tierLimits[tier]; exists {
		return limit
	}
	return m.tierLimits["free"]
}

func main() {
	fmt.Println("=== Rate Limiting Demo ===")

	fmt.Println("\n-- Token Bucket Limiter (Capacity: 3, Refill: 1/sec) --")
	tbl := NewTokenBucketLimiter(3, 1)
	for i := 1; i <= 5; i++ {
		fmt.Printf("Request %d: allowed=%t\n", i, tbl.Allow("client-A"))
	}

	fmt.Println("\n-- Fixed Window Limiter (Limit: 2 per 1s) --")
	fwl := NewFixedWindowLimiter(1*time.Second, 2)
	fmt.Printf("Req 1: allowed=%t\n", fwl.Allow("client-B"))
	fmt.Printf("Req 2: allowed=%t\n", fwl.Allow("client-B"))
	fmt.Printf("Req 3: allowed=%t (expected false)\n", fwl.Allow("client-B"))

	fmt.Println("\n-- Sliding Window Limiter (Limit: 2 per 1s) --")
	swl := NewSlidingWindowLimiter(1*time.Second, 2)
	fmt.Printf("Req 1: allowed=%t\n", swl.Allow("client-C"))
	fmt.Printf("Req 2: allowed=%t\n", swl.Allow("client-C"))
	fmt.Printf("Req 3: allowed=%t (expected false)\n", swl.Allow("client-C"))
}
