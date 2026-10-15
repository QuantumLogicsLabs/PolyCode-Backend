package main

import (
	"crypto/rand"
	"encoding/hex"
	"fmt"
	"strings"
	"sync"
	"time"
)

// Role and Permission models
type Role struct {
	ID          string `json:"id"`
	Name        string `json:"name"`
	Description string `json:"description"`
	CreatedAt   int64  `json:"created_at"`
}

type Permission struct {
	ID          string `json:"id"`
	Name        string `json:"name"`
	Resource    string `json:"resource"`
	Action      string `json:"action"`
	Description string `json:"description"`
}

// AuthorizationService handles RBAC
type AuthorizationService struct {
	roles       map[string]*Role
	permissions map[string]*Permission
	userRoles   map[string][]string
	rolePerms   map[string][]string
	mu          sync.RWMutex
}

func NewAuthorizationService() *AuthorizationService {
	return &AuthorizationService{
		roles:       make(map[string]*Role),
		permissions: make(map[string]*Permission),
		userRoles:   make(map[string][]string),
		rolePerms:   make(map[string][]string),
	}
}

func (a *AuthorizationService) CreateRole(name, description string) error {
	a.mu.Lock()
	defer a.mu.Unlock()

	role := &Role{
		ID:          generateID(),
		Name:        name,
		Description: description,
		CreatedAt:   time.Now().Unix(),
	}

	a.roles[role.ID] = role
	return nil
}

func (a *AuthorizationService) AddPermission(name, resource, action, description string) error {
	a.mu.Lock()
	defer a.mu.Unlock()

	permission := &Permission{
		ID:          generateID(),
		Name:        name,
		Resource:    resource,
		Action:      action,
		Description: description,
	}

	a.permissions[permission.ID] = permission
	return nil
}

func (a *AuthorizationService) AssignPermissionToRole(roleName, permissionName string) error {
	a.mu.Lock()
	defer a.mu.Unlock()

	var targetRole *Role
	for _, r := range a.roles {
		if r.Name == roleName {
			targetRole = r
			break
		}
	}
	if targetRole == nil {
		return fmt.Errorf("role not found: %s", roleName)
	}

	var targetPerm *Permission
	for _, p := range a.permissions {
		if p.Name == permissionName {
			targetPerm = p
			break
		}
	}
	if targetPerm == nil {
		return fmt.Errorf("permission not found: %s", permissionName)
	}

	a.rolePerms[targetRole.ID] = append(a.rolePerms[targetRole.ID], targetPerm.ID)
	return nil
}

func (a *AuthorizationService) AssignRoleToUser(userID, roleName string) error {
	a.mu.Lock()
	defer a.mu.Unlock()

	var targetRole *Role
	for _, r := range a.roles {
		if r.Name == roleName {
			targetRole = r
			break
		}
	}
	if targetRole == nil {
		return fmt.Errorf("role not found: %s", roleName)
	}

	a.userRoles[userID] = append(a.userRoles[userID], targetRole.ID)
	return nil
}

func (a *AuthorizationService) UserHasPermission(userID, permissionName string) bool {
	a.mu.RLock()
	defer a.mu.RUnlock()

	roleIDs, exists := a.userRoles[userID]
	if !exists {
		return false
	}

	for _, roleID := range roleIDs {
		permIDs := a.rolePerms[roleID]
		for _, permID := range permIDs {
			if perm, ok := a.permissions[permID]; ok && perm.Name == permissionName {
				return true
			}
		}
	}
	return false
}

// Policy Engine for ABAC
type Policy struct {
	ID         string
	Name       string
	Conditions []PolicyCondition
	Effect     string
}

type PolicyCondition struct {
	Attribute string
	Operator  string
	Value     string
}

type PolicyRequest struct {
	UserID   string
	Resource string
	Action   string
	Context  map[string]string
}

type PolicyEngine struct {
	policies map[string]*Policy
	mu       sync.RWMutex
}

func NewPolicyEngine() *PolicyEngine {
	return &PolicyEngine{policies: make(map[string]*Policy)}
}

func (p *PolicyEngine) AddPolicy(name, effect string, conditions []PolicyCondition) error {
	p.mu.Lock()
	defer p.mu.Unlock()

	policy := &Policy{
		ID:         generateID(),
		Name:       name,
		Conditions: conditions,
		Effect:     effect,
	}

	p.policies[policy.ID] = policy
	return nil
}

func (p *PolicyEngine) Evaluate(request *PolicyRequest) bool {
	p.mu.RLock()
	defer p.mu.RUnlock()

	allowed := false
	for _, policy := range p.policies {
		if p.evaluateConditions(policy.Conditions, request) {
			if policy.Effect == "deny" {
				return false
			}
			allowed = true
		}
	}
	return allowed
}

func (p *PolicyEngine) evaluateConditions(conditions []PolicyCondition, request *PolicyRequest) bool {
	for _, cond := range conditions {
		var val string
		switch cond.Attribute {
		case "user_id":
			val = request.UserID
		case "resource":
			val = request.Resource
		case "action":
			val = request.Action
		default:
			val = request.Context[cond.Attribute]
		}

		switch cond.Operator {
		case "eq":
			if val != cond.Value {
				return false
			}
		case "contains":
			if !strings.Contains(val, cond.Value) {
				return false
			}
		default:
			return false
		}
	}
	return true
}

func generateID() string {
	b := make([]byte, 16)
	rand.Read(b)
	return hex.EncodeToString(b)
}

func main() {
	fmt.Println("=== RBAC Authorization Demo ===")
	authz := NewAuthorizationService()

	authz.CreateRole("admin", "Full system administrator")
	authz.CreateRole("viewer", "Read only viewer")

	authz.AddPermission("read:users", "users", "read", "View user lists")
	authz.AddPermission("delete:users", "users", "delete", "Delete users")

	authz.AssignPermissionToRole("admin", "read:users")
	authz.AssignPermissionToRole("admin", "delete:users")
	authz.AssignPermissionToRole("viewer", "read:users")

	userID := "user_42"
	authz.AssignRoleToUser(userID, "admin")

	fmt.Printf("User has 'read:users': %t\n", authz.UserHasPermission(userID, "read:users"))
	fmt.Printf("User has 'delete:users': %t\n", authz.UserHasPermission(userID, "delete:users"))

	fmt.Println("\n=== ABAC Policy Engine Demo ===")
	engine := NewPolicyEngine()
	engine.AddPolicy("AllowDepartment", "allow", []PolicyCondition{
		{Attribute: "department", Operator: "eq", Value: "Engineering"},
		{Attribute: "action", Operator: "eq", Value: "deploy"},
	})

	req := &PolicyRequest{
		UserID:   userID,
		Resource: "production_server",
		Action:   "deploy",
		Context:  map[string]string{"department": "Engineering"},
	}

	fmt.Printf("Engineering user access to deploy: %t\n", engine.Evaluate(req))
}
