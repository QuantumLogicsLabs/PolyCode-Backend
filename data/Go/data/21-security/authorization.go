package main

import (
	"fmt"
	"sync"
)

// Authorization service for role-based access control
type AuthorizationService struct {
	roles       map[string]*Role
	permissions map[string]*Permission
	userRoles   map[string][]string // user_id -> role_ids
	rolePerms   map[string][]string // role_id -> permission_ids
	mu          sync.RWMutex
}

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

func NewAuthorizationService() *AuthorizationService {
	return &AuthorizationService{
		roles:       make(map[string]*Role),
		permissions: make(map[string]*Permission),
		userRoles:   make(map[string][]string),
		rolePerms:   make(map[string][]string),
	}
}

// Role management
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

func (a *AuthorizationService) GetRole(roleID string) (*Role, error) {
	a.mu.RLock()
	defer a.mu.RUnlock()
	
	role, exists := a.roles[roleID]
	if !exists {
		return nil, fmt.Errorf("role not found")
	}
	
	return role, nil
}

func (a *AuthorizationService) ListRoles() []*Role {
	a.mu.RLock()
	defer a.mu.RUnlock()
	
	var roles []*Role
	for _, role := range a.roles {
		roles = append(roles, role)
	}
	
	return roles
}

// Permission management
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

func (a *AuthorizationService) GetPermission(permissionID string) (*Permission, error) {
	a.mu.RLock()
	defer a.mu.RUnlock()
	
	permission, exists := a.permissions[permissionID]
	if !exists {
		return nil, fmt.Errorf("permission not found")
	}
	
	return permission, nil
}

func (a *AuthorizationService) ListPermissions() []*Permission {
	a.mu.RLock()
	defer a.mu.RUnlock()
	
	var permissions []*Permission
	for _, permission := range a.permissions {
		permissions = append(permissions, permission)
	}
	
	return permissions
}

// Role-Permission assignment
func (a *AuthorizationService) AssignPermissionToRole(roleID, permissionID string) error {
	a.mu.Lock()
	defer a.mu.Unlock()
	
	if _, exists := a.roles[roleID]; !exists {
		return fmt.Errorf("role not found")
	}
	
	if _, exists := a.permissions[permissionID]; !exists {
		return fmt.Errorf("permission not found")
	}
	
	a.rolePerms[roleID] = append(a.rolePerms[roleID], permissionID)
	return nil
}

func (a *AuthorizationService) RemovePermissionFromRole(roleID, permissionID string) error {
	a.mu.Lock()
	defer a.mu.Unlock()
	
	permissions, exists := a.rolePerms[roleID]
	if !exists {
		return fmt.Errorf("role has no permissions")
	}
	
	for i, permID := range permissions {
		if permID == permissionID {
			a.rolePerms[roleID] = append(permissions[:i], permissions[i+1:]...)
			return nil
		}
	}
	
	return fmt.Errorf("permission not assigned to role")
}

// User-Role assignment
func (a *AuthorizationService) AssignRoleToUser(userID, roleID string) error {
	a.mu.Lock()
	defer a.mu.Unlock()
	
	if _, exists := a.roles[roleID]; !exists {
		return fmt.Errorf("role not found")
	}
	
	a.userRoles[userID] = append(a.userRoles[userID], roleID)
	return nil
}

func (a *AuthorizationService) RemoveRoleFromUser(userID, roleID string) error {
	a.mu.Lock()
	defer a.mu.Unlock()
	
	roles, exists := a.userRoles[userID]
	if !exists {
		return fmt.Errorf("user has no roles")
	}
	
	for i, rID := range roles {
		if rID == roleID {
			a.userRoles[userID] = append(roles[:i], roles[i+1:]...)
			return nil
		}
	}
	
	return fmt.Errorf("role not assigned to user")
}

// Permission checking
func (a *AuthorizationService) UserHasPermission(userID, permissionName string) bool {
	a.mu.RLock()
	defer a.mu.RUnlock()
	
	userRoles, exists := a.userRoles[userID]
	if !exists {
		return false
	}
	
	// Check each role for the permission
	for _, roleID := range userRoles {
		if a.roleHasPermission(roleID, permissionName) {
			return true
		}
	}
	
	return false
}

func (a *AuthorizationService) UserHasRole(userID, roleName string) bool {
	a.mu.RLock()
	defer a.mu.RUnlock()
	
	userRoles, exists := a.userRoles[userID]
	if !exists {
		return false
	}
	
	for _, roleID := range userRoles {
		if role, exists := a.roles[roleID]; exists && role.Name == roleName {
			return true
		}
	}
	
	return false
}

func (a *AuthorizationService) GetUserPermissions(userID []string) []string {
	a.mu.RLock()
	defer a.mu.RUnlock()
	
	var permissions []string
	seen := make(map[string]bool)
	
	for _, userID := range userID {
		userRoles, exists := a.userRoles[userID]
		if !exists {
			continue
		}
		
		for _, roleID := range userRoles {
			rolePerms, exists := a.rolePerms[roleID]
			if !exists {
				continue
			}
			
			for _, permID := range rolePerms {
				if permission, exists := a.permissions[permID]; exists {
					if !seen[permission.Name] {
						permissions = append(permissions, permission.Name)
						seen[permission.Name] = true
					}
				}
			}
		}
	}
	
	return permissions
}

func (a *AuthorizationService) GetUserRoles(userID string) []*Role {
	a.mu.RLock()
	defer a.mu.RUnlock()
	
	userRoleIDs, exists := a.userRoles[userID]
	if !exists {
		return []*Role{}
	}
	
	var roles []*Role
	for _, roleID := range userRoleIDs {
		if role, exists := a.roles[roleID]; exists {
			roles = append(roles, role)
		}
	}
	
	return roles
}

// Helper functions
func (a *AuthorizationService) roleHasPermission(roleID, permissionName string) bool {
	rolePerms, exists := a.rolePerms[roleID]
	if !exists {
		return false
	}
	
	for _, permID := range rolePerms {
		if permission, exists := a.permissions[permID]; exists && permission.Name == permissionName {
			return true
		}
	}
	
	return false
}

// Policy-based access control
type PolicyEngine struct {
	policies map[string]*Policy
	mu       sync.RWMutex
}

type Policy struct {
	ID         string            `json:"id"`
	Name       string            `json:"name"`
	Conditions []PolicyCondition `json:"conditions"`
	Effect     string            `json:"effect"` // "allow" or "deny"
}

type PolicyCondition struct {
	Attribute string `json:"attribute"`
	Operator  string `json:"operator"` // "eq", "ne", "gt", "lt", "in", "contains"
	Value     string `json:"value"`
}

func NewPolicyEngine() *PolicyEngine {
	return &PolicyEngine{
		policies: make(map[string]*Policy),
	}
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
	
	// Default deny
	allowed := false
	
	for _, policy := range p.policies {
		if p.evaluateConditions(policy.Conditions, request) {
			if policy.Effect == "deny" {
				return false // Immediate deny
			}
			allowed = true // Allow if no deny policies match
		}
	}
	
	return allowed
}

type PolicyRequest struct {
	UserID    string            `json:"user_id"`
	Resource  string            `json:"resource"`
	Action    string            `json:"action"`
	Context   map[string]string `json:"context"`
}

func (p *PolicyEngine) evaluateConditions(conditions []PolicyCondition, request *PolicyRequest) bool {
	for _, condition := range conditions {
		if !p.evaluateCondition(condition, request) {
			return false
		}
	}
	return true
}

func (p *PolicyEngine) evaluateCondition(condition PolicyCondition, request *PolicyRequest) bool {
	var value string
	
	switch condition.Attribute {
	case "user_id":
		value = request.UserID
	case "resource":
		value = request.Resource
	case "action":
		value = request.Action
	default:
		value = request.Context[condition.Attribute]
	}
	
	switch condition.Operator {
	case "eq":
		return value == condition.Value
	case "ne":
		return value != condition.Value
	case "contains":
		return contains(value, condition.Value)
	case "in":
		return contains(condition.Value, value)
	default:
		return false
	}
}

// Attribute-based access control (ABAC)
type ABACService struct {
	policyEngine *PolicyEngine
}

func NewABACService() *ABACService {
	return &ABACService{
		policyEngine: NewPolicyEngine(),
	}
}

func (a *ABACService) CheckAccess(userID, resource, action string, context map[string]string) bool {
	request := &PolicyRequest{
		UserID:   userID,
		Resource: resource,
		Action:   action,
		Context:  context,
	}
	
	return a.policyEngine.Evaluate(request)
}

func (a *ABACService) AddPolicy(name, effect string, conditions []PolicyCondition) error {
	return a.policyEngine.AddPolicy(name, effect, conditions)
}
