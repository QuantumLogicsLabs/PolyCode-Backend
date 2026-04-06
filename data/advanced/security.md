# JavaScript Security

## Security Fundamentals

### Same-Origin Policy
```javascript
// Same-Origin Policy demonstration
function checkSameOrigin() {
    // Current origin
    const currentOrigin = window.location.origin;
    console.log('Current origin:', currentOrigin);
    
    // Check if origins match
    function isSameOrigin(url) {
        const link = document.createElement('a');
        link.href = url;
        
        return link.origin === currentOrigin;
    }
    
    console.log('Same origin check:');
    console.log('https://example.com:', isSameOrigin('https://example.com'));
    console.log('https://sub.example.com:', isSameOrigin('https://sub.example.com'));
    console.log('http://example.com:', isSameOrigin('http://example.com'));
}

// Cross-origin communication
function crossOriginCommunication() {
    // PostMessage for cross-origin communication
    const iframe = document.createElement('iframe');
    iframe.src = 'https://example.com';
    document.body.appendChild(iframe);
    
    // Wait for iframe to load
    iframe.onload = function() {
        // Send message to iframe
        iframe.contentWindow.postMessage({
            type: 'greeting',
            message: 'Hello from parent!'
        }, 'https://example.com');
    };
    
    // Listen for messages from iframe
    window.addEventListener('message', function(event) {
        // Verify origin
        if (event.origin !== 'https://example.com') {
            console.error('Message from untrusted origin:', event.origin);
            return;
        }
        
        console.log('Message from iframe:', event.data);
    });
}
```

### Content Security Policy (CSP)
```javascript
// CSP Header implementation
function setupCSP() {
    // Set CSP headers (server-side configuration)
    const cspHeaders = {
        'Content-Security-Policy': [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://trusted.cdn.com",
            "style-src 'self' 'unsafe-inline' https://trusted.cdn.com",
            "img-src 'self' data: https:",
            "connect-src 'self' https://api.example.com",
            "font-src 'self' https://fonts.googleapis.com",
            "frame-src 'self' https://trusted.com",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "upgrade-insecure-requests"
        ].join('; ')
    };
    
    // Client-side CSP violation reporting
    document.addEventListener('securitypolicyviolation', function(event) {
        console.error('CSP Violation:', {
            blockedURI: event.blockedURI,
            violatedDirective: event.violatedDirective,
            originalPolicy: event.originalPolicy,
            referrer: event.referrer,
            sample: event.sample
        });
        
        // Send violation report to server
        reportCSPViolation(event);
    });
}

function reportCSPViolation(violation) {
    fetch('/api/csp-violation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            timestamp: new Date().toISOString(),
            blockedURI: violation.blockedURI,
            violatedDirective: violation.violatedDirective,
            referrer: violation.referrer,
            userAgent: navigator.userAgent
        })
    }).catch(error => {
        console.error('Failed to report CSP violation:', error);
    });
}
```

## Input Validation and Sanitization

### Input Validation
```javascript
// Input validation utilities
class InputValidator {
    constructor() {
        this.rules = {
            email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            phone: /^\+?[\d\s\-\(\)]+$/,
            url: /^https?:\/\/.+/,
            number: /^\d+$/,
            alphanumeric: /^[a-zA-Z0-9]+$/,
            safeString: /^[a-zA-Z0-9\s\-_.,!?]+$/
        };
    }
    
    validate(input, type, options = {}) {
        const { required = false, minLength = 0, maxLength = Infinity } = options;
        
        // Check if required
        if (required && (!input || input.trim() === '')) {
            return { valid: false, error: 'Field is required' };
        }
        
        // Skip validation if input is empty and not required
        if (!input && !required) {
            return { valid: true };
        }
        
        // Check length
        if (input.length < minLength) {
            return { valid: false, error: `Minimum length is ${minLength}` };
        }
        
        if (input.length > maxLength) {
            return { valid: false, error: `Maximum length is ${maxLength}` };
        }
        
        // Check against regex pattern
        const pattern = this.rules[type];
        if (pattern && !pattern.test(input)) {
            return { valid: false, error: `Invalid ${type} format` };
        }
        
        return { valid: true };
    }
    
    sanitize(input, type = 'safeString') {
        if (!input) return '';
        
        switch (type) {
            case 'html':
                return this.sanitizeHTML(input);
            case 'url':
                return this.sanitizeURL(input);
            case 'sql':
                return this.sanitizeSQL(input);
            case 'xss':
                return this.sanitizeXSS(input);
            default:
                return this.sanitizeBasic(input);
        }
    }
    
    sanitizeHTML(input) {
        const div = document.createElement('div');
        div.textContent = input;
        return div.innerHTML;
    }
    
    sanitizeURL(input) {
        try {
            const url = new URL(input);
            // Only allow http/https protocols
            if (!['http:', 'https:'].includes(url.protocol)) {
                return '#';
            }
            return url.toString();
        } catch (e) {
            return '#';
        }
    }
    
    sanitizeSQL(input) {
        // Remove SQL injection patterns
        const sqlPatterns = [
            /(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|UNION|SCRIPT)\b)/gi,
            /(['"]\s*;\s*\w+/gi,
            /(\bOR\b|\bAND\b)\s+\d+\s*=\s*\d+/gi
        ];
        
        let sanitized = input;
        sqlPatterns.forEach(pattern => {
            sanitized = sanitized.replace(pattern, '');
        });
        
        return sanitized;
    }
    
    sanitizeXSS(input) {
        // Remove XSS patterns
        const xssPatterns = [
            /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,
            /<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi,
            /javascript:/gi,
            /on\w+\s*=/gi,
            /<img[^>]*src[^>]*javascript:/gi
        ];
        
        let sanitized = input;
        xssPatterns.forEach(pattern => {
            sanitized = sanitized.replace(pattern, '');
        });
        
        return sanitized;
    }
    
    sanitizeBasic(input) {
        // Remove potentially dangerous characters
        return input.replace(/[<>'"&]/g, '');
    }
}

// Usage example
const validator = new InputValidator();

function validateForm(formData) {
    const errors = {};
    
    // Validate email
    const emailValidation = validator.validate(formData.email, 'email', { required: true });
    if (!emailValidation.valid) {
        errors.email = emailValidation.error;
    }
    
    // Validate phone
    const phoneValidation = validator.validate(formData.phone, 'phone');
    if (!phoneValidation.valid) {
        errors.phone = phoneValidation.error;
    }
    
    // Validate message
    const messageValidation = validator.validate(formData.message, 'safeString', {
        required: true,
        minLength: 10,
        maxLength: 500
    });
    if (!messageValidation.valid) {
        errors.message = messageValidation.error;
    }
    
    // Sanitize inputs
    const sanitizedData = {
        email: validator.sanitize(formData.email, 'email'),
        phone: validator.sanitize(formData.phone, 'phone'),
        message: validator.sanitize(formData.message, 'xss')
    };
    
    return {
        valid: Object.keys(errors).length === 0,
        errors,
        sanitizedData
    };
}
```

### XSS Prevention
```javascript
// XSS Prevention utilities
class XSSProtection {
    constructor() {
        this.escaper = document.createElement('div');
    }
    
    escapeHTML(input) {
        if (!input) return '';
        
        this.escaper.textContent = input;
        return this.escaper.innerHTML;
    }
    
    escapeAttribute(input) {
        if (!input) return '';
        
        return input
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#x27;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
    
    escapeURL(input) {
        if (!input) return '';
        
        return encodeURIComponent(input);
    }
    
    createSafeHTML(template, data) {
        let html = template;
        
        // Replace placeholders with escaped data
        for (const [key, value] of Object.entries(data)) {
            const placeholder = new RegExp(`{{\\s*${key}\\s*}}`, 'g');
            html = html.replace(placeholder, this.escapeHTML(value));
        }
        
        return html;
    }
    
    sanitizeHTML(input) {
        const div = document.createElement('div');
        div.textContent = input;
        return div.innerHTML;
    }
    
    stripHTML(input) {
        const div = document.createElement('div');
        div.innerHTML = input;
        return div.textContent || div.innerText || '';
    }
    
    validateHTML(input) {
        try {
            const parser = new DOMParser();
            const doc = parser.parseFromString(input, 'text/html');
            
            // Check for dangerous elements
            const dangerousElements = ['script', 'iframe', 'object', 'embed', 'form'];
            const found = [];
            
            dangerousElements.forEach(tag => {
                const elements = doc.getElementsByTagName(tag);
                if (elements.length > 0) {
                    found.push(tag);
                }
            });
            
            return {
                safe: found.length === 0,
                dangerousElements: found
            };
        } catch (e) {
            return { safe: false, error: e.message };
        }
    }
}

// Usage example
const xssProtection = new XSSProtection();

function displayUserContent(userInput) {
    // Escape user input before displaying
    const escapedInput = xssProtection.escapeHTML(userInput);
    
    // Or use safe template
    const template = `
        <div class="user-content">
            <h3>{{title}}</h3>
            <p>{{content}}</p>
        </div>
    `;
    
    const safeHTML = xssProtection.createSafeHTML(template, {
        title: userInput.title,
        content: userInput.content
    });
    
    document.getElementById('content').innerHTML = safeHTML;
}

function handleUserComment(comment) {
    // Validate and sanitize comment
    const validation = xssProtection.validateHTML(comment);
    
    if (!validation.safe) {
        console.error('Comment contains dangerous elements:', validation.dangerousElements);
        return false;
    }
    
    const sanitizedComment = xssProtection.sanitizeHTML(comment);
    
    // Display sanitized comment
    const commentElement = document.createElement('div');
    commentElement.className = 'comment';
    commentElement.textContent = sanitizedComment;
    
    document.getElementById('comments').appendChild(commentElement);
    return true;
}
```

## Authentication and Authorization

### Secure Authentication
```javascript
// Secure authentication system
class SecureAuth {
    constructor() {
        this.tokenKey = 'auth_token';
        this.refreshKey = 'refresh_token';
        this.userKey = 'user_data';
    }
    
    async login(credentials) {
        try {
            // Validate credentials
            const validation = this.validateCredentials(credentials);
            if (!validation.valid) {
                throw new Error(validation.error);
            }
            
            // Make API call
            const response = await this.authenticate(credentials);
            
            // Store tokens securely
            this.storeTokens(response);
            
            return response;
        } catch (error) {
            console.error('Login failed:', error);
            throw error;
        }
    }
    
    validateCredentials(credentials) {
        if (!credentials.email || !credentials.password) {
            return { valid: false, error: 'Email and password are required' };
        }
        
        const emailValidation = new InputValidator().validate(credentials.email, 'email', { required: true });
        if (!emailValidation.valid) {
            return { valid: false, error: 'Invalid email format' };
        }
        
        if (credentials.password.length < 8) {
            return { valid: false, error: 'Password must be at least 8 characters' };
        }
        
        return { valid: true };
    }
    
    async authenticate(credentials) {
        // Simulate API call
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(credentials)
        });
        
        if (!response.ok) {
            throw new Error('Authentication failed');
        }
        
        return response.json();
    }
    
    storeTokens(tokens) {
        // Store in httpOnly cookies (server-side)
        // For client-side storage, use secure methods
        
        // Use sessionStorage for current session
        if (tokens.rememberMe) {
            localStorage.setItem(this.tokenKey, tokens.accessToken);
            localStorage.setItem(this.refreshKey, tokens.refreshToken);
        } else {
            sessionStorage.setItem(this.tokenKey, tokens.accessToken);
            sessionStorage.setItem(this.refreshKey, tokens.refreshToken);
        }
        
        // Store user data
        this.storeUserData(tokens.user);
    }
    
    storeUserData(userData) {
        const storage = userData.rememberMe ? localStorage : sessionStorage;
        storage.setItem(this.userKey, JSON.stringify(userData));
    }
    
    getToken() {
        return sessionStorage.getItem(this.tokenKey) || localStorage.getItem(this.tokenKey);
    }
    
    getRefreshToken() {
        return sessionStorage.getItem(this.refreshKey) || localStorage.getItem(this.refreshKey);
    }
    
    getUserData() {
        const userData = sessionStorage.getItem(this.userKey) || localStorage.getItem(this.userKey);
        return userData ? JSON.parse(userData) : null;
    }
    
    async refreshToken() {
        try {
            const refreshToken = this.getRefreshToken();
            if (!refreshToken) {
                throw new Error('No refresh token available');
            }
            
            const response = await fetch('/api/auth/refresh', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ refreshToken })
            });
            
            if (!response.ok) {
                throw new Error('Token refresh failed');
            }
            
            const tokens = await response.json();
            this.storeTokens(tokens);
            
            return tokens.accessToken;
        } catch (error) {
            this.logout();
            throw error;
        }
    }
    
    async makeAuthenticatedRequest(url, options = {}) {
        const token = this.getToken();
        
        if (!token) {
            throw new Error('No authentication token');
        }
        
        const headers = {
            'Authorization': `Bearer ${token}`,
            ...options.headers
        };
        
        try {
            const response = await fetch(url, {
                ...options,
                headers
            });
            
            if (response.status === 401) {
                // Try to refresh token
                try {
                    const newToken = await this.refreshToken();
                    headers['Authorization'] = `Bearer ${newToken}`;
                    
                    // Retry request with new token
                    return fetch(url, {
                        ...options,
                        headers
                    });
                } catch (refreshError) {
                    this.logout();
                    throw new Error('Session expired');
                }
            }
            
            return response;
        } catch (error) {
            throw error;
        }
    }
    
    logout() {
        // Clear all stored data
        localStorage.removeItem(this.tokenKey);
        localStorage.removeItem(this.refreshKey);
        localStorage.removeItem(this.userKey);
        
        sessionStorage.removeItem(this.tokenKey);
        sessionStorage.removeItem(this.refreshKey);
        sessionStorage.removeItem(this.userKey);
        
        // Call logout API
        fetch('/api/auth/logout', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${this.getToken()}`
            }
        }).catch(error => {
            console.error('Logout API call failed:', error);
        });
    }
    
    isAuthenticated() {
        return !!this.getToken();
    }
    
    hasRole(role) {
        const userData = this.getUserData();
        return userData && userData.roles && userData.roles.includes(role);
    }
    
    hasPermission(permission) {
        const userData = this.getUserData();
        return userData && userData.permissions && userData.permissions.includes(permission);
    }
}
```

### Role-Based Access Control
```javascript
// Role-based access control system
class RBAC {
    constructor() {
        this.roles = new Map();
        this.permissions = new Map();
        this.userRoles = new Map();
        this.initializeRoles();
    }
    
    initializeRoles() {
        // Define permissions
        this.permissions.set('read', 'Read access');
        this.permissions.set('write', 'Write access');
        this.permissions.set('delete', 'Delete access');
        this.permissions.set('admin', 'Administrative access');
        this.permissions.set('manage_users', 'Manage users');
        this.permissions.set('manage_content', 'Manage content');
        
        // Define roles with permissions
        this.roles.set('guest', ['read']);
        this.roles.set('user', ['read', 'write']);
        this.roles.set('moderator', ['read', 'write', 'manage_content']);
        this.roles.set('admin', ['read', 'write', 'delete', 'admin', 'manage_users', 'manage_content']);
    }
    
    assignRole(userId, role) {
        if (!this.roles.has(role)) {
            throw new Error(`Role ${role} does not exist`);
        }
        
        this.userRoles.set(userId, role);
    }
    
    getUserRole(userId) {
        return this.userRoles.get(userId);
    }
    
    getUserPermissions(userId) {
        const role = this.getUserRole(userId);
        if (!role) {
            return [];
        }
        
        return this.roles.get(role) || [];
    }
    
    hasPermission(userId, permission) {
        const permissions = this.getUserPermissions(userId);
        return permissions.includes(permission);
    }
    
    hasAnyPermission(userId, permissions) {
        const userPermissions = this.getUserPermissions(userId);
        return permissions.some(permission => userPermissions.includes(permission));
    }
    
    hasAllPermissions(userId, permissions) {
        const userPermissions = this.getUserPermissions(userId);
        return permissions.every(permission => userPermissions.includes(permission));
    }
    
    checkAccess(userId, requiredPermissions, requireAll = false) {
        if (requireAll) {
            return this.hasAllPermissions(userId, requiredPermissions);
        } else {
            return this.hasAnyPermission(userId, requiredPermissions);
        }
    }
    
    createPermissionMiddleware(requiredPermissions, requireAll = false) {
        return (req, res, next) => {
            const userId = req.user.id;
            
            if (!this.checkAccess(userId, requiredPermissions, requireAll)) {
                return res.status(403).json({ error: 'Insufficient permissions' });
            }
            
            next();
        };
    }
}

// Usage example
const rbac = new RBAC();

// Assign roles
rbac.assignRole('user1', 'user');
rbac.assignRole('user2', 'admin');

// Check permissions
console.log('User1 can read:', rbac.hasPermission('user1', 'read')); // true
console.log('User1 can delete:', rbac.hasPermission('user1', 'delete')); // false
console.log('User2 can delete:', rbac.hasPermission('user2', 'delete')); // true

// Express middleware example
function createSecureRoute(app, rbac) {
    // Protected route - requires read permission
    app.get('/api/data', rbac.createPermissionMiddleware(['read']), (req, res) => {
        res.json({ data: 'Protected data' });
    });
    
    // Admin route - requires admin permission
    app.delete('/api/users/:id', rbac.createPermissionMiddleware(['admin']), (req, res) => {
        res.json({ message: 'User deleted' });
    });
    
    // Content management - requires all permissions
    app.post('/api/content', rbac.createPermissionMiddleware(['write', 'manage_content'], true), (req, res) => {
        res.json({ message: 'Content created' });
    });
}
```

## Secure Storage

### Secure Local Storage
```javascript
// Secure storage utilities
class SecureStorage {
    constructor() {
        this.encryptionKey = null;
        this.storagePrefix = 'secure_';
        this.initializeEncryption();
    }
    
    async initializeEncryption() {
        // Generate or retrieve encryption key
        const storedKey = localStorage.getItem('encryption_key');
        
        if (storedKey) {
            this.encryptionKey = await this.importKey(storedKey);
        } else {
            this.encryptionKey = await this.generateKey();
            const exportedKey = await this.exportKey(this.encryptionKey);
            localStorage.setItem('encryption_key', exportedKey);
        }
    }
    
    async generateKey() {
        return await window.crypto.subtle.generateKey(
            {
                name: 'AES-GCM',
                length: 256
            },
            ['encrypt', 'decrypt']
        );
    }
    
    async exportKey(key) {
        const exported = await window.crypto.subtle.exportKey('raw', key);
        return btoa(String.fromCharCode(...new Uint8Array(exported)));
    }
    
    async importKey(keyData) {
        const rawKey = Uint8Array.from(atob(keyData), c => c.charCodeAt(0));
        return await window.crypto.subtle.importKey(
            'raw',
            rawKey,
            {
                name: 'AES-GCM',
                length: 256
            },
            false,
            ['encrypt', 'decrypt']
        );
    }
    
    async encrypt(data) {
        const encoder = new TextEncoder();
        const encodedData = encoder.encode(JSON.stringify(data));
        
        const iv = window.crypto.getRandomValues(new Uint8Array(12));
        
        const encrypted = await window.crypto.subtle.encrypt(
            {
                name: 'AES-GCM',
                iv: iv
            },
            this.encryptionKey,
            encodedData
        );
        
        return {
            encrypted: btoa(String.fromCharCode(...new Uint8Array(encrypted))),
            iv: btoa(String.fromCharCode(...iv))
        };
    }
    
    async decrypt(encryptedData) {
        const encrypted = Uint8Array.from(atob(encryptedData.encrypted), c => c.charCodeAt(0));
        const iv = Uint8Array.from(atob(encryptedData.iv), c => c.charCodeAt(0));
        
        const decrypted = await window.crypto.subtle.decrypt(
            {
                name: 'AES-GCM',
                iv: iv
            },
            this.encryptionKey,
            encrypted
        );
        
        const decoder = new TextDecoder();
        return JSON.parse(decoder.decode(decrypted));
    }
    
    async setItem(key, value) {
        if (!this.encryptionKey) {
            await this.initializeEncryption();
        }
        
        const encrypted = await this.encrypt(value);
        localStorage.setItem(this.storagePrefix + key, JSON.stringify(encrypted));
    }
    
    async getItem(key) {
        if (!this.encryptionKey) {
            await this.initializeEncryption();
        }
        
        const encryptedData = localStorage.getItem(this.storagePrefix + key);
        
        if (!encryptedData) {
            return null;
        }
        
        try {
            const encrypted = JSON.parse(encryptedData);
            return await this.decrypt(encrypted);
        } catch (error) {
            console.error('Failed to decrypt data:', error);
            return null;
        }
    }
    
    removeItem(key) {
        localStorage.removeItem(this.storagePrefix + key);
    }
    
    clear() {
        const keys = Object.keys(localStorage);
        keys.forEach(key => {
            if (key.startsWith(this.storagePrefix)) {
                localStorage.removeItem(key);
            }
        });
    }
}

// Usage example
const secureStorage = new SecureStorage();

async function storeUserData(userData) {
    await secureStorage.setItem('user_data', userData);
}

async function getUserData() {
    return await secureStorage.getItem('user_data');
}

async function storeAuthToken(token) {
    await secureStorage.setItem('auth_token', token);
}

async function getAuthToken() {
    return await secureStorage.getItem('auth_token');
}
```

### Session Management
```javascript
// Secure session management
class SessionManager {
    constructor() {
        this.sessionKey = 'session_data';
        this.activityKey = 'last_activity';
        this.timeout = 30 * 60 * 1000; // 30 minutes
        this.warningTime = 5 * 60 * 1000; // 5 minutes warning
        this.warningShown = false;
        this.sessionTimer = null;
        this.warningTimer = null;
    }
    
    startSession(userData) {
        const sessionData = {
            id: this.generateSessionId(),
            userId: userData.id,
            startTime: Date.now(),
            lastActivity: Date.now(),
            userData: userData
        };
        
        sessionStorage.setItem(this.sessionKey, JSON.stringify(sessionData));
        this.startActivityMonitoring();
        
        return sessionData.id;
    }
    
    generateSessionId() {
        return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }
    
    startActivityMonitoring() {
        // Monitor user activity
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
        
        const updateActivity = () => {
            this.updateLastActivity();
        };
        
        events.forEach(event => {
            document.addEventListener(event, updateActivity, true);
        });
        
        // Start session timeout monitoring
        this.startSessionTimeout();
    }
    
    updateLastActivity() {
        const sessionData = this.getSessionData();
        if (sessionData) {
            sessionData.lastActivity = Date.now();
            sessionStorage.setItem(this.sessionKey, JSON.stringify(sessionData));
        }
        
        // Reset timers
        this.resetTimers();
    }
    
    startSessionTimeout() {
        this.clearTimers();
        
        // Warning timer
        this.warningTimer = setTimeout(() => {
            this.showSessionWarning();
        }, this.timeout - this.warningTime);
        
        // Session timeout timer
        this.sessionTimer = setTimeout(() => {
            this.endSession();
        }, this.timeout);
    }
    
    resetTimers() {
        this.clearTimers();
        this.startSessionTimeout();
    }
    
    clearTimers() {
        if (this.sessionTimer) {
            clearTimeout(this.sessionTimer);
            this.sessionTimer = null;
        }
        
        if (this.warningTimer) {
            clearTimeout(this.warningTimer);
            this.warningTimer = null;
        }
    }
    
    showSessionWarning() {
        if (this.warningShown) {
            return;
        }
        
        this.warningShown = true;
        
        const warningMessage = 'Your session will expire in 5 minutes. Do you want to extend it?';
        
        if (confirm(warningMessage)) {
            this.extendSession();
        }
    }
    
    extendSession() {
        this.updateLastActivity();
        this.warningShown = false;
        
        // Notify server of session extension
        this.notifyServer('session-extended');
    }
    
    endSession() {
        this.clearTimers();
        
        const sessionData = this.getSessionData();
        if (sessionData) {
            // Notify server of session end
            this.notifyServer('session-ended', sessionData.id);
        }
        
        // Clear session data
        sessionStorage.removeItem(this.sessionKey);
        sessionStorage.removeItem(this.activityKey);
        
        // Redirect to login
        window.location.href = '/login?reason=session-expired';
    }
    
    getSessionData() {
        const sessionData = sessionStorage.getItem(this.sessionKey);
        return sessionData ? JSON.parse(sessionData) : null;
    }
    
    isSessionValid() {
        const sessionData = this.getSessionData();
        
        if (!sessionData) {
            return false;
        }
        
        const now = Date.now();
        const timeSinceActivity = now - sessionData.lastActivity;
        
        return timeSinceActivity < this.timeout;
    }
    
    async notifyServer(action, sessionId = null) {
        try {
            const sessionData = this.getSessionData();
            const data = {
                action: action,
                sessionId: sessionId || sessionData?.id,
                timestamp: Date.now()
            };
            
            await fetch('/api/session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
        } catch (error) {
            console.error('Failed to notify server:', error);
        }
    }
    
    logout() {
        this.endSession();
    }
}
```

## API Security

### Secure API Communication
```javascript
// Secure API client
class SecureAPIClient {
    constructor(baseURL, options = {}) {
        this.baseURL = baseURL;
        this.options = {
            timeout: 30000,
            retries: 3,
            retryDelay: 1000,
            ...options
        };
        
        this.interceptors = {
            request: [],
            response: []
        };
    }
    
    addRequestInterceptor(interceptor) {
        this.interceptors.request.push(interceptor);
    }
    
    addResponseInterceptor(interceptor) {
        this.interceptors.response.push(interceptor);
    }
    
    async request(url, options = {}) {
        const config = {
            url: this.baseURL + url,
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            ...options
        };
        
        // Apply request interceptors
        for (const interceptor of this.interceptors.request) {
            await interceptor(config);
        }
        
        try {
            const response = await this.makeRequest(config);
            
            // Apply response interceptors
            let result = response;
            for (const interceptor of this.interceptors.response) {
                result = await interceptor(result);
            }
            
            return result;
        } catch (error) {
            throw this.handleError(error);
        }
    }
    
    async makeRequest(config) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.options.timeout);
        
        try {
            const response = await fetch(config.url, {
                ...config,
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const contentType = response.headers.get('content-type');
            
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            } else {
                return await response.text();
            }
        } catch (error) {
            clearTimeout(timeoutId);
            
            if (error.name === 'AbortError') {
                throw new Error('Request timeout');
            }
            
            throw error;
        }
    }
    
    async requestWithRetry(url, options = {}) {
        let lastError;
        
        for (let attempt = 1; attempt <= this.options.retries; attempt++) {
            try {
                return await this.request(url, options);
            } catch (error) {
                lastError = error;
                
                if (attempt < this.options.retries) {
                    await this.delay(this.options.retryDelay * attempt);
                }
            }
        }
        
        throw lastError;
    }
    
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
    
    handleError(error) {
        if (error instanceof Error) {
            return {
                name: error.name,
                message: error.message,
                stack: error.stack
            };
        }
        
        return {
            name: 'UnknownError',
            message: 'An unknown error occurred'
        };
    }
    
    get(url, params = {}, options = {}) {
        const queryString = new URLSearchParams(params).toString();
        const fullUrl = queryString ? `${url}?${queryString}` : url;
        
        return this.request(fullUrl, { ...options, method: 'GET' });
    }
    
    post(url, data = {}, options = {}) {
        return this.request(url, {
            ...options,
            method: 'POST',
            body: JSON.stringify(data)
        });
    }
    
    put(url, data = {}, options = {}) {
        return this.request(url, {
            ...options,
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }
    
    delete(url, options = {}) {
        return this.request(url, { ...options, method: 'DELETE' });
    }
}

// Usage example with security interceptors
const apiClient = new SecureAPIClient('https://api.example.com');

// Add authentication interceptor
apiClient.addRequestInterceptor(async (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
    }
});

// Add CSRF protection interceptor
apiClient.addRequestInterceptor(async (config) => {
    if (['POST', 'PUT', 'DELETE'].includes(config.method)) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            config.headers['X-CSRF-Token'] = csrfToken;
        }
    }
});

// Add response validation interceptor
apiClient.addResponseInterceptor(async (response) => {
    // Validate response structure
    if (response && typeof response === 'object') {
        // Check for error responses
        if (response.error) {
            throw new Error(response.error);
        }
    }
    
    return response;
});

// Add rate limiting interceptor
apiClient.addRequestInterceptor(async (config) => {
    const rateLimitKey = `rate_limit_${config.url}`;
    const lastRequest = localStorage.getItem(rateLimitKey);
    const now = Date.now();
    
    if (lastRequest && (now - parseInt(lastRequest)) < 1000) {
        throw new Error('Rate limit exceeded');
    }
    
    localStorage.setItem(rateLimitKey, now.toString());
});
```

### Rate Limiting
```javascript
// Rate limiting implementation
class RateLimiter {
    constructor(maxRequests, windowMs) {
        this.maxRequests = maxRequests;
        this.windowMs = windowMs;
        this.requests = new Map();
    }
    
    isAllowed(key) {
        const now = Date.now();
        const windowStart = now - this.windowMs;
        
        const requests = this.requests.get(key) || [];
        
        // Remove old requests
        const validRequests = requests.filter(timestamp => timestamp > windowStart);
        
        // Check if under limit
        if (validRequests.length >= this.maxRequests) {
            return false;
        }
        
        // Add current request
        validRequests.push(now);
        this.requests.set(key, validRequests);
        
        return true;
    }
    
    getRemainingRequests(key) {
        const now = Date.now();
        const windowStart = now - this.windowMs;
        
        const requests = this.requests.get(key) || [];
        const validRequests = requests.filter(timestamp => timestamp > windowStart);
        
        return Math.max(0, this.maxRequests - validRequests.length);
    }
    
    getResetTime(key) {
        const requests = this.requests.get(key) || [];
        
        if (requests.length === 0) {
            return 0;
        }
        
        const oldestRequest = Math.min(...requests);
        return oldestRequest + this.windowMs;
    }
}

// API rate limiter
class APIRateLimiter {
    constructor() {
        this.limiters = new Map();
        this.globalLimiter = new RateLimiter(100, 60000); // 100 requests per minute
    }
    
    getLimiter(endpoint, method) {
        const key = `${method}:${endpoint}`;
        
        if (!this.limiters.has(key)) {
            // Different limits for different endpoints
            let maxRequests, windowMs;
            
            if (endpoint.includes('/auth/')) {
                maxRequests = 10;
                windowMs = 60000; // 10 requests per minute for auth
            } else if (method === 'GET') {
                maxRequests = 200;
                windowMs = 60000; // 200 requests per minute for reads
            } else {
                maxRequests = 50;
                windowMs = 60000; // 50 requests per minute for writes
            }
            
            this.limiters.set(key, new RateLimiter(maxRequests, windowMs));
        }
        
        return this.limiters.get(key);
    }
    
    checkRateLimit(endpoint, method, userId = null) {
        // Check global rate limit
        if (!this.globalLimiter.isAllowed('global')) {
            return {
                allowed: false,
                error: 'Global rate limit exceeded',
                resetTime: this.globalLimiter.getResetTime('global')
            };
        }
        
        // Check endpoint-specific rate limit
        const limiter = this.getLimiter(endpoint, method);
        const key = userId || 'anonymous';
        
        if (!limiter.isAllowed(key)) {
            return {
                allowed: false,
                error: 'Rate limit exceeded',
                resetTime: limiter.getResetTime(key),
                remaining: limiter.getRemainingRequests(key)
            };
        }
        
        return {
            allowed: true,
            remaining: limiter.getRemainingRequests(key)
        };
    }
}

// Usage example
const rateLimiter = new APIRateLimiter();

function makeAPICall(endpoint, method, userId) {
    const rateLimitCheck = rateLimiter.checkRateLimit(endpoint, method, userId);
    
    if (!rateLimitCheck.allowed) {
        throw new Error(rateLimitCheck.error);
    }
    
    // Make API call
    return apiClient.request(endpoint, { method });
}
```

## Security Best Practices

### Security Checklist
```javascript
// Security checklist and validation
class SecurityChecklist {
    constructor() {
        this.checks = [];
        this.results = [];
    }
    
    addCheck(name, checkFunction, severity = 'medium') {
        this.checks.push({
            name,
            check: checkFunction,
            severity
        });
    }
    
    async runChecks() {
        this.results = [];
        
        for (const check of this.checks) {
            try {
                const result = await check.check();
                this.results.push({
                    name: check.name,
                    passed: result,
                    severity: check.severity,
                    error: null
                });
            } catch (error) {
                this.results.push({
                    name: check.name,
                    passed: false,
                    severity: check.severity,
                    error: error.message
                });
            }
        }
        
        return this.results;
    }
    
    generateReport() {
        const passed = this.results.filter(r => r.passed).length;
        const failed = this.results.filter(r => !r.passed).length;
        const critical = this.results.filter(r => !r.passed && r.severity === 'critical').length;
        
        return {
            total: this.results.length,
            passed,
            failed,
            critical,
            results: this.results
        };
    }
}

// Create security checklist
const securityChecklist = new SecurityChecklist();

// Check HTTPS
securityChecklist.addCheck('HTTPS Connection', () => {
    return window.location.protocol === 'https:';
}, 'critical');

// Check CSP
securityChecklist.addCheck('CSP Header', () => {
    const metaCSP = document.querySelector('meta[http-equiv="Content-Security-Policy"]');
    return !!metaCSP;
}, 'high');

// Check secure cookies
securityChecklist.addCheck('Secure Cookies', () => {
    return document.cookie.includes('Secure') || document.cookie === '';
}, 'medium');

// Check no inline scripts
securityChecklist.addCheck('No Inline Scripts', () => {
    const scripts = document.querySelectorAll('script');
    return Array.from(scripts).every(script => !script.innerHTML.trim());
}, 'high');

// Check input validation
securityChecklist.addCheck('Input Validation', () => {
    const forms = document.querySelectorAll('form');
    return forms.length > 0; // Assume forms have validation
}, 'medium');

// Check authentication
securityChecklist.addCheck('Authentication', () => {
    return localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
}, 'medium');

// Run security checks
async function runSecurityAudit() {
    const results = await securityChecklist.runChecks();
    const report = securityChecklist.generateReport();
    
    console.log('Security Audit Report:', report);
    
    if (report.critical > 0) {
        console.error('Critical security issues found!');
    }
    
    return report;
}
```

### Security Monitoring
```javascript
// Security monitoring and logging
class SecurityMonitor {
    constructor() {
        this.events = [];
        this.maxEvents = 1000;
        this.alertThreshold = 10; // Alert after 10 security events
    }
    
    logEvent(event) {
        const securityEvent = {
            timestamp: Date.now(),
            type: event.type,
            severity: event.severity || 'medium',
            description: event.description,
            source: event.source || 'client',
            userAgent: navigator.userAgent,
            url: window.location.href,
            userId: event.userId || null,
            details: event.details || {}
        };
        
        this.events.push(securityEvent);
        
        // Maintain event limit
        if (this.events.length > this.maxEvents) {
            this.events.shift();
        }
        
        // Check for alerts
        this.checkAlerts(securityEvent);
        
        // Send to server
        this.sendToServer(securityEvent);
    }
    
    checkAlerts(event) {
        const recentEvents = this.events.filter(e => 
            Date.now() - e.timestamp < 60000 // Last minute
        );
        
        if (recentEvents.length >= this.alertThreshold) {
            this.triggerAlert('High volume of security events', 'critical');
        }
        
        // Check for specific patterns
        if (event.type === 'authentication_failure') {
            const authFailures = recentEvents.filter(e => e.type === 'authentication_failure');
            
            if (authFailures.length >= 5) {
                this.triggerAlert('Multiple authentication failures', 'critical');
            }
        }
        
        if (event.type === 'xss_attempt') {
            this.triggerAlert('XSS attempt detected', 'high');
        }
    }
    
    triggerAlert(message, severity) {
        console.error(`Security Alert [${severity}]: ${message}`);
        
        // Send alert to server
        this.sendAlertToServer({
            message,
            severity,
            timestamp: Date.now(),
            recentEvents: this.events.slice(-10)
        });
    }
    
    async sendToServer(event) {
        try {
            await fetch('/api/security/events', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(event)
            });
        } catch (error) {
            console.error('Failed to send security event to server:', error);
        }
    }
    
    async sendAlertToServer(alert) {
        try {
            await fetch('/api/security/alerts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(alert)
            });
        } catch (error) {
            console.error('Failed to send security alert to server:', error);
        }
    }
    
    monitorXSS() {
        // Monitor for XSS attempts
        const originalCreateElement = document.createElement;
        
        document.createElement = function(tagName) {
            const element = originalCreateElement.call(this, tagName);
            
            if (tagName.toLowerCase() === 'script') {
                securityMonitor.logEvent({
                    type: 'script_creation',
                    severity: 'medium',
                    description: 'Script element created',
                    details: { tagName }
                });
            }
            
            return element;
        };
    }
    
    monitorAuthentication() {
        // Monitor authentication events
        const originalFetch = window.fetch;
        
        window.fetch = function(...args) {
            const url = args[0];
            
            if (typeof url === 'string' && url.includes('/auth/')) {
                securityMonitor.logEvent({
                    type: 'auth_request',
                    severity: 'low',
                    description: 'Authentication request made',
                    details: { url }
                });
            }
            
            return originalFetch.apply(this, args).then(response => {
                if (!response.ok && url.includes('/auth/login')) {
                    securityMonitor.logEvent({
                        type: 'authentication_failure',
                        severity: 'high',
                        description: 'Authentication failed',
                        details: { url, status: response.status }
                    });
                }
                
                return response;
            });
        };
    }
    
    getEvents(type = null, severity = null, limit = 100) {
        let events = this.events;
        
        if (type) {
            events = events.filter(e => e.type === type);
        }
        
        if (severity) {
            events = events.filter(e => e.severity === severity);
        }
        
        return events.slice(-limit);
    }
    
    getStats() {
        const events = this.events;
        
        return {
            total: events.length,
            byType: this.groupBy(events, 'type'),
            bySeverity: this.groupBy(events, 'severity'),
            recent: events.filter(e => Date.now() - e.timestamp < 3600000) // Last hour
        };
    }
    
    groupBy(events, property) {
        return events.reduce((groups, event) => {
            const key = event[property];
            groups[key] = (groups[key] || 0) + 1;
            return groups;
        }, {});
    }
}

// Initialize security monitoring
const securityMonitor = new SecurityMonitor();
securityMonitor.monitorXSS();
securityMonitor.monitorAuthentication();
```

## Summary

JavaScript security encompasses multiple critical areas:

**Core Security Concepts:**
- Same-Origin Policy for isolation
- Content Security Policy (CSP) for content control
- Cross-Origin communication with postMessage
- Secure context requirements

**Input Security:**
- Input validation and sanitization
- XSS prevention techniques
- HTML escaping and encoding
- SQL injection prevention

**Authentication & Authorization:**
- Secure token management
- Role-based access control (RBAC)
- Session management
- Permission checking

**Secure Storage:**
- Encrypted local storage
- Session storage best practices
- Key management
- Data protection

**API Security:**
- Secure API communication
- Request/response interceptors
- Rate limiting
- CSRF protection

**Security Monitoring:**
- Security event logging
- Real-time monitoring
- Alert systems
- Security audits

**Best Practices:**
- Use HTTPS everywhere
- Implement proper input validation
- Use secure authentication methods
- Monitor security events
- Keep dependencies updated
- Follow principle of least privilege

**Common Threats:**
- Cross-Site Scripting (XSS)
- Cross-Site Request Forgery (CSRF)
- SQL Injection
- Authentication bypass
- Session hijacking

Security is an ongoing process that requires constant vigilance, regular audits, and staying updated with the latest security best practices and threats.
