package main

import (
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
	CurrentTokens int       `json:"current_tokens"`
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
	
	// Refill tokens
	t.refill(bucket)
	
	// Check if enough tokens
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
	
	// Remove old requests outside the window
	cutoff := now.Add(-s.window)
	validRequests := []time.Time{}
	for _, req := range window.requests {
		if req.After(cutoff) {
			validRequests = append(validRequests, req)
		}
	}
	window.requests = validRequests
	
	// Check if adding n requests would exceed limit
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
	count      int
	window     time.Duration
	limit      int
	windowStart time.Time
	stats      RateLimitStats
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
	
	// Reset if window has expired
	if now.Sub(counter.windowStart) >= counter.window {
		counter.count = 0
		counter.windowStart = now
	}
	
	// Check if adding n requests would exceed limit
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

// DistributedRateLimiter for distributed systems
type DistributedRateLimiter struct {
	storage RateLimitStorage
	limiter RateLimiter
}

type RateLimitStorage interface {
	Get(key string) (interface{}, error)
	Set(key string, value interface{}, ttl time.Duration) error
	Delete(key string) error
	Increment(key string) (int, error)
	Expire(key string, ttl time.Duration) error
}

func NewDistributedRateLimiter(storage RateLimitStorage, limiter RateLimiter) *DistributedRateLimiter {
	return &DistributedRateLimiter{
		storage: storage,
		limiter: limiter,
	}
}

func (d *DistributedRateLimiter) Allow(identifier string) bool {
	return d.limiter.Allow(identifier)
}

func (d *DistributedRateLimiter) AllowN(identifier string, n int) bool {
	return d.limiter.AllowN(identifier, n)
}

func (d *DistributedRateLimiter) Reset(identifier string) {
	d.limiter.Reset(identifier)
}

func (d *DistributedRateLimiter) GetStats(identifier string) RateLimitStats {
	return d.limiter.GetStats(identifier)
}

// AdaptiveRateLimiter adjusts limits based on system load
type AdaptiveRateLimiter struct {
	baseLimiter RateLimiter
	loadMonitor LoadMonitor
	adjuster    RateAdjuster
}

type LoadMonitor interface {
	GetCurrentLoad() float64 // 0.0 to 1.0
}

type RateAdjuster interface {
	Adjust(baseLimit int, load float64) int
}

func NewAdaptiveRateLimiter(baseLimiter RateLimiter, monitor LoadMonitor, adjuster RateAdjuster) *AdaptiveRateLimiter {
	return &AdaptiveRateLimiter{
		baseLimiter: baseLimiter,
		loadMonitor: monitor,
		adjuster:    adjuster,
	}
}

func (a *AdaptiveRateLimiter) Allow(identifier string) bool {
	return a.baseLimiter.Allow(identifier)
}

func (a *AdaptiveRateLimiter) AllowN(identifier string, n int) bool {
	return a.baseLimiter.AllowN(identifier, n)
}

func (a *AdaptiveRateLimiter) Reset(identifier string) {
	a.baseLimiter.Reset(identifier)
}

func (a *AdaptiveRateLimiter) GetStats(identifier string) RateLimitStats {
	return a.baseLimiter.GetStats(identifier)
}

// Simple load monitor implementation
type SimpleLoadMonitor struct {
	currentLoad float64
}

func NewSimpleLoadMonitor() *SimpleLoadMonitor {
	return &SimpleLoadMonitor{currentLoad: 0.5}
}

func (s *SimpleLoadMonitor) GetCurrentLoad() float64 {
	return s.currentLoad
}

func (s *SimpleLoadMonitor) SetLoad(load float64) {
	s.currentLoad = load
}

// Simple rate adjuster implementation
type SimpleRateAdjuster struct{}

func NewSimpleRateAdjuster() *SimpleRateAdjuster {
	return &SimpleRateAdjuster{}
}

func (s *SimpleRateAdjuster) Adjust(baseLimit int, load float64) int {
	if load > 0.8 {
		// Reduce limit by 50% under high load
		return baseLimit / 2
	} else if load > 0.6 {
		// Reduce limit by 25% under medium load
		return baseLimit * 3 / 4
	} else if load < 0.3 {
		// Increase limit by 25% under low load
		return baseLimit * 5 / 4
	}
	
	// No adjustment under normal load
	return baseLimit
}

// MultiTierRateLimiter for different user tiers
type MultiTierRateLimiter struct {
	limiter  RateLimiter
	tierLimits map[string]int // tier -> limit
}

func NewMultiTierRateLimiter(limiter RateLimiter) *MultiTierRateLimiter {
	return &MultiTierRateLimiter{
		limiter: limiter,
		tierLimits: map[string]int{
			"free":    100,
			"basic":   1000,
			"premium": 10000,
			"enterprise": 100000,
		},
	}
}

func (m *MultiTierRateLimiter) Allow(identifier string, tier string) bool {
	limit, exists := m.tierLimits[tier]
	if !exists {
		limit = m.tierLimits["free"] // Default to free tier
	}
	
	// Create a composite identifier
	compositeID := fmt.Sprintf("%s:%s", tier, identifier)
	
	// This is simplified - in practice, you'd need to adjust the underlying limiter
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
