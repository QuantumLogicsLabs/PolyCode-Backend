package main

import (
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"sync"
	"time"
)

type ServiceRegistry struct {
	services map[string][]ServiceInstance
	mu       sync.RWMutex
}

type ServiceInstance struct {
	ID       string    `json:"id"`
	Name     string    `json:"name"`
	Address  string    `json:"address"`
	Port     int       `json:"port"`
	Health   string    `json:"health"`
	LastSeen time.Time `json:"last_seen"`
}

func NewServiceRegistry() *ServiceRegistry {
	return &ServiceRegistry{
		services: make(map[string][]ServiceInstance),
	}
}

func (sr *ServiceRegistry) Register(service ServiceInstance) error {
	sr.mu.Lock()
	defer sr.mu.Unlock()

	service.LastSeen = time.Now()
	service.Health = "healthy"

	if _, exists := sr.services[service.Name]; !exists {
		sr.services[service.Name] = []ServiceInstance{}
	}

	sr.services[service.Name] = append(sr.services[service.Name], service)
	log.Printf("Registered service: %s at %s:%d", service.Name, service.Address, service.Port)

	return nil
}

func (sr *ServiceRegistry) Discover(serviceName string) ([]ServiceInstance, error) {
	sr.mu.RLock()
	defer sr.mu.RUnlock()

	instances, exists := sr.services[serviceName]
	if !exists {
		return nil, fmt.Errorf("service %s not found", serviceName)
	}

	var healthyInstances []ServiceInstance
	for _, instance := range instances {
		if instance.Health == "healthy" {
			healthyInstances = append(healthyInstances, instance)
		}
	}

	return healthyInstances, nil
}

func (sr *ServiceRegistry) HealthCheck() {
	sr.mu.Lock()
	defer sr.mu.Unlock()

	for serviceName, instances := range sr.services {
		for i, instance := range instances {
			if time.Since(instance.LastSeen) > 30*time.Second {
				sr.services[serviceName][i].Health = "unhealthy"
			}
		}
	}
}

func (sr *ServiceRegistry) handleRegister(w http.ResponseWriter, r *http.Request) {
	var instance ServiceInstance
	if err := json.NewDecoder(r.Body).Decode(&instance); err != nil {
		http.Error(w, err.Error(), http.StatusBadRequest)
		return
	}

	if err := sr.Register(instance); err != nil {
		http.Error(w, err.Error(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusCreated)
}

func (sr *ServiceRegistry) handleDiscover(w http.ResponseWriter, r *http.Request) {
	serviceName := r.URL.Query().Get("service")
	if serviceName == "" {
		http.Error(w, "service parameter required", http.StatusBadRequest)
		return
	}

	instances, err := sr.Discover(serviceName)
	if err != nil {
		http.Error(w, err.Error(), http.StatusNotFound)
		return
	}

	json.NewEncoder(w).Encode(instances)
}

func main() {
	fmt.Println("=== Service Discovery & Registry Demo ===")
	registry := NewServiceRegistry()

	// Register sample services
	s1 := ServiceInstance{ID: "auth-1", Name: "auth-service", Address: "127.0.0.1", Port: 8081}
	s2 := ServiceInstance{ID: "user-1", Name: "user-service", Address: "127.0.0.1", Port: 8082}

	_ = registry.Register(s1)
	_ = registry.Register(s2)

	// Discover services
	discovered, err := registry.Discover("user-service")
	if err != nil {
		log.Fatalf("Discovery failed: %v", err)
	}

	fmt.Printf("Discovered %d instance(s) for 'user-service':\n", len(discovered))
	for _, inst := range discovered {
		fmt.Printf(" - ID: %s, Address: %s:%d, Status: %s\n", inst.ID, inst.Address, inst.Port, inst.Health)
	}
}
