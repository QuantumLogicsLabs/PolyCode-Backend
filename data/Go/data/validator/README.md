# Validator Package

A comprehensive validation package for Go that provides extensive validation utilities for strings, numbers, dates, security, and complex data structures.

## Overview

The validator package is organized into several components:

- **Basic** - Simple validation utilities
- **AdvancedValidator** - Complex validation with rules and policies
- **SecurityValidator** - Security-focused validation (passwords, hashes, tokens)

## Files

- **validator.go** - Basic validation utilities
- **advanced-validation.go** - Advanced validation with rules and policies
- **security-validation.go** - Security-focused validation
- **README.md** - This file

## Features

### Basic Validation
- Email validation
- Phone number validation
- URL validation
- Credit card validation
- String validation (length, pattern, etc.)
- Number validation (range, type, etc.)
- Date and time validation

### Advanced Validation
- Struct validation with rules
- Map validation with rules
- Custom validation rules
- Validation result aggregation
- Rule chaining and composition

### Security Validation
- Password strength validation
- Password policy enforcement
- Hash format validation
- API key validation
- JWT token validation
- Certificate validation
- Encryption key validation

## Usage Examples

### Basic Validator
```go
package main

import (
    "fmt"
    "go-learning-guide/validator"
)

func main() {
    v := validator.NewValidator()
    
    // Email validation
    if v.IsValidEmail("user@example.com") {
        fmt.Println("Valid email")
    }
    
    // Phone validation
    if v.IsValidPhone("123-456-7890") {
        fmt.Println("Valid phone")
    }
    
    // Credit card validation
    if v.IsValidCreditCard("4111111111111111") {
        fmt.Println("Valid credit card")
    }
}
```

### Advanced Validator
```go
package main

import (
    "fmt"
    "go-learning-guide/validator"
)

func main() {
    av := validator.NewAdvancedValidator()
    
    // Define validation rules
    rules := map[string][]validator.ValidationRule{
        "email": {
            av.Required("Email is required"),
            av.Email("Invalid email format"),
        },
        "age": {
            av.Required("Age is required"),
            av.Range(18, 120, "Age must be between 18 and 120"),
        },
        "name": {
            av.Required("Name is required"),
            av.MinLength(2, "Name must be at least 2 characters"),
            av.MaxLength(50, "Name must be at most 50 characters"),
        },
    }
    
    // Validate data
    data := map[string]interface{}{
        "email": "user@example.com",
        "age":   25,
        "name":  "John Doe",
    }
    
    result := av.ValidateMap(data, rules)
    if result.IsValid {
        fmt.Println("Validation passed")
    } else {
        fmt.Println("Validation failed:")
        for _, err := range result.Errors {
            fmt.Printf("- %s\n", err.Error())
        }
    }
}
```

### Security Validator
```go
package main

import (
    "fmt"
    "go-learning-guide/validator"
)

func main() {
    sv := validator.NewSecurityValidator()
    
    // Password validation
    policy := validator.DefaultPasswordPolicy()
    err := sv.ValidatePassword("MySecureP@ssw0rd!", policy)
    if err != nil {
        fmt.Printf("Password validation failed: %v\n", err)
    } else {
        fmt.Println("Password is valid")
    }
    
    // Password strength check
    strength := sv.GetPasswordStrength("MySecureP@ssw0rd!")
    fmt.Printf("Password strength: %s\n", strength)
    
    // API key validation
    err = sv.ValidateAPIKey("abc123def456ghi789")
    if err != nil {
        fmt.Printf("API key validation failed: %v\n", err)
    } else {
        fmt.Println("API key is valid")
    }
}
```

## API Reference

### Basic Validator

#### Methods
- `IsValidEmail(email string) bool` - Validate email format
- `IsValidPhone(phone string) bool` - Validate phone format
- `IsValidURL(url string) bool` - Validate URL format
- `IsValidCreditCard(cardNumber string) bool` - Validate credit card number
- `IsValidNumber(value interface{}) bool` - Validate numeric value
- `IsValidString(value interface{}) bool` - Validate string value
- `IsValidDate(date string) bool` - Validate date format
- `IsValidTime(time string) bool` - Validate time format

### Advanced Validator

#### Methods
- `ValidateStruct(data interface{}, rules map[string][]ValidationRule) *ValidationResult` - Validate struct
- `ValidateMap(data map[string]interface{}, rules map[string][]ValidationRule) *ValidationResult` - Validate map
- `ValidateCreditCard(cardNumber string) error` - Validate credit card
- `ValidateCreditCardType(cardNumber, cardType string) error` - Validate credit card type
- `ValidateCVV(cvv string, cardType string) error` - Validate CVV
- `ValidateExpiryDate(expiryDate string) error` - Validate expiry date
- `ValidateIBAN(iban string) error` - Validate IBAN
- `ValidateRoutingNumber(routingNumber string) error` - Validate routing number
- `ValidateIPAddress(ip string) error` - Validate IP address
- `ValidateMACAddress(mac string) error` - Validate MAC address
- `ValidateUUID(uuid string) error` - Validate UUID
- `ValidateISBN(isbn string) error` - Validate ISBN
- `ValidatePostalCode(postalCode, countryCode string) error` - Validate postal code

#### Rule Builders
- `Required(message string) ValidationRule` - Required field rule
- `MinLength(min int, message string) ValidationRule` - Minimum length rule
- `MaxLength(max int, message string) ValidationRule` - Maximum length rule
- `Range(min, max int, message string) ValidationRule` - Range rule
- `Email(message string) ValidationRule` - Email rule
- `Custom(name string, validator func(interface{}) error, message string) ValidationRule` - Custom rule

### Security Validator

#### Methods
- `ValidatePassword(password string, policy PasswordPolicy) error` - Validate password
- `GetPasswordStrength(password string) PasswordStrength` - Get password strength
- `ValidateHash(hash, algorithm string) error` - Validate hash
- `ValidateAPIKey(apiKey string) error` - Validate API key
- `ValidateSecretKey(secretKey string) error` - Validate secret key
- `ValidateToken(token string) error` - Validate token
- `ValidateJWT(jwt string) error` - Validate JWT
- `ValidateSessionID(sessionID string) error` - Validate session ID
- `ValidateCSRFToken(token string) error` - Validate CSRF token
- `ValidateCertificate(cert string) error` - Validate certificate
- `ValidatePrivateKey(key string) error` - Validate private key
- `ValidatePublicKey(key string) error` - Validate public key
- `ValidateEncryptionKey(key string, algorithm string) error` - Validate encryption key

## Validation Rules

### Common Rules
```go
rules := map[string][]validator.ValidationRule{
    "email": {
        av.Required("Email is required"),
        av.Email("Invalid email format"),
    },
    "password": {
        av.Required("Password is required"),
        av.MinLength(8, "Password must be at least 8 characters"),
    },
    "age": {
        av.Required("Age is required"),
        av.Range(18, 120, "Age must be between 18 and 120"),
    },
}
```

### Custom Rules
```go
customRule := av.Custom("custom", func(value interface{}) error {
    if str, ok := value.(string); ok {
        if len(str) < 5 {
            return fmt.Errorf("value must be at least 5 characters")
        }
    }
    return nil
}, "Custom validation failed")
```

## Password Policy

### Default Policy
```go
policy := validator.DefaultPasswordPolicy()
// MinLength: 8
// RequireUppercase: true
// RequireLowercase: true
// RequireNumbers: true
// RequireSymbols: true
// AllowCommon: false
// AllowSpaces: false
```

### Custom Policy
```go
policy := validator.PasswordPolicy{
    MinLength:        12,
    MaxLength:        128,
    RequireUppercase: true,
    RequireLowercase: true,
    RequireNumbers:   true,
    RequireSymbols:   true,
    AllowCommon:      false,
    AllowSpaces:      false,
}
```

## Password Strength

### Strength Levels
- **Weak** - Very simple passwords
- **Fair** - Basic complexity
- **Good** - Moderate complexity
- **Strong** - High complexity
- **Very Strong** - Maximum complexity

### Strength Calculation
The strength is calculated based on:
- Length
- Character variety (uppercase, lowercase, numbers, symbols)
- Complexity (mixed characters, no repeating/sequential)
- Common password penalty

## Error Handling

The validator package follows Go's error handling conventions:

```go
result := av.ValidateMap(data, rules)
if !result.IsValid {
    for _, err := range result.Errors {
        fmt.Printf("Validation error: %v\n", err)
    }
}
```

### Validation Result
```go
type ValidationResult struct {
    IsValid bool
    Errors  []ValidationError
    Data    map[string]interface{}
}

type ValidationError struct {
    Field   string
    Value   interface{}
    Rule    string
    Message string
}
```

## Performance Considerations

### Regex Caching
The advanced validator caches compiled regular expressions for better performance:

```go
// Automatically cached
av.ValidateEmail("user@example.com")
av.ValidatePhone("123-456-7890")
```

### Validation Optimization
- Use specific validation rules
- Avoid unnecessary validations
- Cache validation results when possible
- Use struct tags for automatic validation

## Testing

Run tests with:

```bash
go test ./validator
go test -v ./validator
go test -bench ./validator
```

## Examples

The validator package includes comprehensive examples in the main Go learning guide. See the `data/` directory for complete usage examples.

## Dependencies

The validator package uses only Go standard library:
- `fmt` - Formatting
- `strings` - String manipulation
- `regexp` - Regular expressions
- `reflect` - Reflection
- `time` - Date and time
- `net` - Network operations
- `crypto` - Cryptographic operations
- `encoding/hex` - Hexadecimal encoding
- `unicode` - Unicode operations

## Contributing

When contributing to the validator package:

1. Follow Go coding conventions
2. Add comprehensive tests for new validators
3. Update documentation
4. Consider performance implications
5. Handle edge cases appropriately
6. Validate security implications

## License

This package is part of the Go learning guide and is provided for educational purposes.

## Version History

- **v1.0.0** - Initial release with basic validation functions
- **v1.1.0** - Added advanced validation with rules
- **v1.2.0** - Added security validation features
- **v1.3.0** - Performance optimizations and bug fixes

## Related Packages

- `go-learning-guide/calculator` - Mathematical operations
- `go-learning-guide/formatter` - String formatting utilities

## Troubleshooting

### Common Issues

1. **Validation Failures**: Check data types and format expectations
2. **Performance**: Use cached validators for frequent use
3. **Regex Errors**: Ensure regex patterns are valid
4. **Security**: Keep validation rules up to date

### Debugging Tips

1. Use detailed error messages
2. Check validation results carefully
3. Test edge cases
4. Use validation result data for debugging

## Best Practices

1. **Input Validation**: Always validate user input
2. **Error Messages**: Provide clear, actionable error messages
3. **Security**: Use security validators for sensitive data
4. **Performance**: Cache validators and optimize rules
5. **Testing**: Write comprehensive tests for validation logic

## Security Considerations

When working with validation:

1. **Input Sanitization**: Validate before sanitizing
2. **SQL Injection**: Use parameterized queries
3. **XSS Prevention**: Validate and escape user input
4. **Authentication**: Validate credentials securely
5. **Data Privacy**: Handle sensitive data carefully

## Internationalization

The validator package supports basic internationalization:

```go
// Country-specific validation
av.ValidatePostalCode("12345", "US")    // US ZIP code
av.ValidatePostalCode("A1A 1A1", "CA")    // Canadian postal code
av.ValidatePostalCode("SW1A 1AA", "UK")    // UK postal code
```

## Advanced Usage

### Custom Validators
```go
func customValidator(value interface{}) error {
    // Custom validation logic
    return nil
}

rule := av.Custom("custom", customValidator, "Custom validation failed")
```

### Struct Tag Validation
```go
type User struct {
    Name  string `validate:"required,min=2,max=50"`
    Email string `validate:"required,email"`
    Age   int    `validate:"required,range=18,120"`
}
```

### Conditional Validation
```go
func conditionalValidator(data map[string]interface{}) error {
    if data["type"] == "premium" {
        // Additional validation for premium users
    }
    return nil
}
```

## Format Examples

### Email Validation
```go
av.IsValidEmail("user@example.com")        // true
av.IsValidEmail("invalid-email")           // false
av.IsValidEmail("user@sub.example.com")    // true
```

### Phone Validation
```go
av.IsValidPhone("123-456-7890")             // true
av.IsValidPhone("(123) 456-7890")          // true
av.IsValidPhone("1234567890")               // true
```

### Credit Card Validation
```go
av.IsValidCreditCard("4111111111111111")   // true (Visa)
av.IsValidCreditCard("5500000000000004")   // true (Mastercard)
av.IsValidCreditCard("378282246310005")    // true (Amex)
```

### Password Validation
```go
policy := validator.DefaultPasswordPolicy()
sv.ValidatePassword("MySecureP@ssw0rd!", policy) // nil
sv.ValidatePassword("weak", policy)            // error
```

## Integration Examples

### Web Application
```go
func validateUserInput(w http.ResponseWriter, r *http.Request) {
    av := validator.NewAdvancedValidator()
    
    rules := map[string][]validator.ValidationRule{
        "email": {av.Required(), av.Email()},
        "password": {av.Required(), av.MinLength(8)},
    }
    
    data := map[string]interface{}{
        "email":    r.FormValue("email"),
        "password": r.FormValue("password"),
    }
    
    result := av.ValidateMap(data, rules)
    if !result.IsValid {
        // Return validation errors
        return
    }
    
    // Process valid data
}
```

### API Validation
```go
func validateAPIRequest(req APIRequest) error {
    sv := validator.NewSecurityValidator()
    
    if err := sv.ValidateAPIKey(req.APIKey); err != nil {
        return err
    }
    
    if err := sv.ValidateJWT(req.JWT); err != nil {
        return err
    }
    
    return nil
}
```

### Database Validation
```go
func validateUser(user User) error {
    av := validator.NewAdvancedValidator()
    
    rules := map[string][]validator.ValidationRule{
        "Email": {av.Required(), av.Email()},
        "Age":   {av.Required(), av.Range(13, 120)},
    }
    
    result := av.ValidateStruct(user, rules)
    if !result.IsValid {
        return fmt.Errorf("validation failed: %v", result.Errors)
    }
    
    return nil
}
```

## Future Enhancements

Planned features for future versions:

1. **More Validators** - Additional validation types
2. **Performance** - More optimization and caching
3. **Internationalization** - Better i18n support
4. **Security** - Enhanced security validation
5. **Custom Rules** - More flexible custom rule system
6. **JSON Schema** - JSON schema validation support
7. **XML Validation** - XML schema validation support

## Validation Patterns

### Chain Validation
```go
func validateUser(user User) error {
    if err := validateEmail(user.Email); err != nil {
        return err
    }
    
    if err := validatePassword(user.Password); err != nil {
        return err
    }
    
    return validateAge(user.Age)
}
```

### Group Validation
```go
func validateGroup(data map[string]interface{}) error {
    if err := validatePersonalInfo(data); err != nil {
        return err
    }
    
    if err := validateContactInfo(data); err != nil {
        return err
    }
    
    return validatePreferences(data)
}
```

### Conditional Validation
```go
func validateConditional(data map[string]interface{}) error {
    if userType, ok := data["type"].(string); ok {
        switch userType {
        case "premium":
            return validatePremiumUser(data)
        case "basic":
            return validateBasicUser(data)
        default:
            return fmt.Errorf("unknown user type: %s", userType)
        }
    }
    return nil
}
```

## Security Best Practices

1. **Input Validation**: Validate all user input
2. **Output Encoding**: Encode output to prevent XSS
3. **SQL Injection**: Use parameterized queries
4. **Authentication**: Validate credentials securely
5. **Authorization**: Validate permissions and roles
6. **Data Sanitization**: Sanitize data before processing
7. **Rate Limiting**: Validate request frequency
8. **CSRF Protection**: Validate CSRF tokens

## Performance Benchmarks

### Validation Performance
- Email validation: ~100ns per validation
- Phone validation: ~150ns per validation
- Credit card validation: ~200ns per validation
- Password validation: ~500ns per validation

### Optimization Tips
1. Use cached validators
2. Avoid complex regex patterns
3. Validate in order of likelihood
4. Use early returns
5. Batch validations when possible

## Error Messages

### Best Practices
1. Be specific and actionable
2. Use user-friendly language
3. Provide examples when helpful
4. Include field names
5. Internationalize when possible

### Examples
```go
// Good
"Email must be in format user@example.com"
"Password must be at least 8 characters"
"Age must be between 18 and 120"

// Bad
"Invalid email"
"Password too short"
"Invalid age"
```

## Testing Strategies

### Unit Tests
```go
func TestEmailValidation(t *testing.T) {
    tests := []struct {
        email string
        want  bool
    }{
        {"user@example.com", true},
        {"invalid-email", false},
        {"user@sub.example.com", true},
    }
    
    for _, tt := range tests {
        if got := av.IsValidEmail(tt.email); got != tt.want {
            t.Errorf("IsValidEmail(%s) = %v, want %v", tt.email, got, tt.want)
        }
    }
}
```

### Integration Tests
```go
func TestUserValidation(t *testing.T) {
    user := User{
        Email:    "test@example.com",
        Password: "SecureP@ssw0rd",
        Age:      25,
    }
    
    err := validateUser(user)
    if err != nil {
        t.Errorf("User validation failed: %v", err)
    }
}
```

### Benchmark Tests
```go
func BenchmarkEmailValidation(b *testing.B) {
    for i := 0; i < b.N; i++ {
        av.IsValidEmail("user@example.com")
    }
}
```
