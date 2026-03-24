package main

import (
	"fmt"
	"go-learning-example/calculator"
	"go-learning-example/formatter"
	"go-learning-example/validator"
	"time"
)

func main() {
	fmt.Println("=== Package Design in Go ===")
	
	// Package organization principles
	fmt.Println("\n--- Package Organization ---")
	demonstratePackageOrganization()
	
	// Package naming conventions
	fmt.Println("\n--- Package Naming ---")
	demonstratePackageNaming()
	
	// Package structure
	fmt.Println("\n--- Package Structure ---")
	demonstratePackageStructure()
	
	// Package dependencies
	fmt.Println("\n--- Package Dependencies ---")
	demonstratePackageDependencies()
	
	// Package interfaces
	fmt.Println("\n--- Package Interfaces ---")
	demonstratePackageInterfaces()
	
	// Package documentation
	fmt.Println("\n--- Package Documentation ---")
	demonstratePackageDocumentation()
	
	// Package testing
	fmt.Println("\n--- Package Testing ---")
	demonstratePackageTesting()
	
	// Package versioning
	fmt.Println("\n--- Package Versioning ---")
	demonstratePackageVersioning()
	
	// Package publishing
	fmt.Println("\n--- Package Publishing ---")
	demonstratePackagePublishing()
}

func demonstratePackageOrganization() {
	fmt.Println("Package Organization Principles:")
	
	fmt.Println("1. Single Responsibility")
	fmt.Println("   Each package should have one clear purpose")
	fmt.Println("   Example: strings - string manipulation")
	fmt.Println("   Example: fmt - formatted I/O")
	
	fmt.Println("\n2. Cohesion")
	fmt.Println("   Related functionality should be grouped")
	fmt.Println("   High cohesion within packages")
	fmt.Println("   Low coupling between packages")
	
	fmt.Println("\n3. Package Size")
	fmt.Println("   Keep packages focused and manageable")
	fmt.Println("   Split large packages into smaller ones")
	fmt.Println("   Avoid monolithic packages")
	
	fmt.Println("\n4. Package Hierarchy")
	fmt.Println("   Internal packages for implementation details")
	fmt.Println("   Public packages for API")
	fmt.Println("   Use subpackages for related functionality")
	
	// Demonstrate with our custom packages
	fmt.Println("\n--- Current Package Structure ---")
	fmt.Println("go-learning-guide/")
	fmt.Println("├── calculator/     - Mathematical operations")
	fmt.Println("├── formatter/      - String formatting")
	fmt.Println("├── validator/      - Input validation")
	fmt.Println("└── lessons/        - Learning materials")
}

func demonstratePackageNaming() {
	fmt.Println("Package Naming Conventions:")
	
	fmt.Println("1. Use short, lowercase names")
	fmt.Println("   Good: fmt, strings, http")
	fmt.Println("   Bad: stringUtilities, HTTPClient")
	
	fmt.Println("\n2. Use single word when possible")
	fmt.Println("   Good: crypto, math, time")
	fmt.Println("   Avoid: stringManipulation, networkUtilities")
	
	fmt.Println("\n3. Be descriptive but concise")
	fmt.Println("   Good: json, xml, sql")
	fmt.Println("   Bad: dataProcessing, userManagement")
	
	fmt.Println("\n4. Avoid underscores")
	fmt.Println("   Good: database, network")
	fmt.Println("   Bad: data_base, network_util")
	
	fmt.Println("\n5. Use domain-specific terminology")
	fmt.Println("   Good: grpc, oauth, jwt")
	fmt.Println("   Avoid: generic names like utils, common")
	
	fmt.Println("\n--- Examples from Custom Packages ---")
	fmt.Println("✅ calculator - Clear purpose")
	fmt.Println("✅ formatter - Clear purpose")
	fmt.Println("✅ validator - Clear purpose")
	fmt.Println("❌ utils - Too generic")
	fmt.Println("❌ helpers - Too generic")
	fmt.Println("❌ common - Too generic")
}

func demonstratePackageStructure() {
	fmt.Println("Package Structure Best Practices:")
	
	fmt.Println("1. Package Declaration")
	fmt.Println("   package calculator")
	fmt.Println("   package formatter")
	fmt.Println("   package validator")
	
	fmt.Println("\n2. Public API")
	fmt.Println("   Exported names start with capital letter")
	fmt.Println("   Provide clear, minimal public API")
	fmt.Println("   Hide implementation details")
	
	fmt.Println("\n3. Internal Organization")
	fmt.Println("   Group related functionality together")
	fmt.Println("   Use subpackages for large packages")
	fmt.Println("   Keep files focused and manageable")
	
	fmt.Println("\n4. File Organization")
	fmt.Println("   One public API per file (when possible)")
	fmt.Println("   Group related functions")
	fmt.Println("   Separate concerns into different files")
	
	// Show example structure
	fmt.Println("\n--- Example Package Structure ---")
	fmt.Println("calculator/")
	fmt.Println("├── calculator.go     // Main API")
	fmt.Println("├── advanced.go      // Advanced operations")
	fmt.Println("├── constants.go     // Package constants")
	fmt.Println("└── doc.go           // Package documentation")
}

func demonstratePackageDependencies() {
	fmt.Println("Package Dependencies:")
	
	fmt.Println("1. Minimize dependencies")
	fmt.Println("   Only import what you need")
	fmt.Println("   Prefer interfaces over concrete types")
	fmt.Println("   Avoid circular dependencies")
	
	fmt.Println("\n2. Dependency Direction")
	fmt.Println("   High-level packages depend on low-level")
	fmt.Println("   Avoid dependencies both ways")
	fmt.Println("   Use dependency injection when needed")
	
	fmt.Println("\n3. External Dependencies")
	fmt.Println("   Keep external dependencies minimal")
	fmt.Println("   Document required versions")
	fmt.Println("   Handle dependency updates carefully")
	
	// Demonstrate with current packages
	fmt.Println("\n--- Current Package Dependencies ---")
	
	// Main package depends on our custom packages
	fmt.Println("main.go imports:")
	fmt.Println("  - go-learning-example/calculator")
	fmt.Println("  - go-learning-example/formatter")
	fmt.Println("  - go-learning-example/validator")
	
	// Show usage
	fmt.Println("\n--- Using Custom Packages ---")
	
	// Calculator usage
	result := calculator.Add(10, 5)
	fmt.Printf("Calculator.Add(10, 5) = %d\n", result)
	
	// Formatter usage
	greeting := formatter.FormatGreeting("Go Developer")
	fmt.Printf("Formatter greeting: %s\n", greeting)
	
	// Validator usage
	email := "user@example.com"
	isValid := validator.IsValidEmail(email)
	fmt.Printf("Validator.IsValidEmail(\"%s\") = %t\n", email, isValid)
	
	// Package dependency graph
	fmt.Println("\n--- Dependency Graph ---")
	fmt.Println("main")
	fmt.Println("  ├── calculator")
	fmt.Println("  ├── formatter")
	fmt.Println("  └── validator")
	fmt.Println("All packages have no circular dependencies")
}

func demonstratePackageInterfaces() {
	fmt.Println("Package Interfaces:")
	
	fmt.Println("1. Define interfaces for extensibility")
	fmt.Println("   Allow users to implement custom behavior")
	fmt.Println("   Enable testing with mocks")
	fmt.Println("   Provide clear contracts")
	
	fmt.Println("\n2. Interface Design")
	fmt.Println("   Keep interfaces small and focused")
	fmt.Println("   Accept interfaces, return concrete types")
	fmt.Println("   Use interface composition")
	
	fmt.Println("\n3. Interface Location")
	fmt.Println("   Place interfaces near their use")
	fmt.Println("   Or in a dedicated package")
	fmt.Println("   Consider interface segregation")
	
	// Example interface design
	fmt.Println("\n--- Example Interface Design ---")
	
	// Define interfaces for our packages
	fmt.Println("type Calculator interface {")
	fmt.Println("    Add(a, b int) int")
	fmt.Println("    Subtract(a, b int) int")
	fmt.Println("}")
	
	fmt.Println("type Formatter interface {")
	fmt.Println("    FormatGreeting(name string) string")
	fmt.Println("    ToUpperCase(text string) string")
	fmt.Println("}")
	
	fmt.Println("type Validator interface {")
	fmt.Println("    IsValidEmail(email string) bool")
	fmt.Println("    IsValidAge(age int) bool")
	fmt.Println("}")
	
	// Show how interfaces enable extensibility
	fmt.Println("\n--- Interface Extensibility ---")
	
	// We could implement different calculators
	fmt.Println("type ScientificCalculator struct { ... }")
	fmt.Println("func (sc ScientificCalculator) Add(a, b int) int { ... }")
	fmt.Println("func (sc ScientificCalculator) Sin(x float64) float64 { ... }")
	
	// Or different formatters
	fmt.Println("type HTMLFormatter struct { ... }")
	fmt.Println("func (hf HTMLFormatter) FormatGreeting(name string) string { ... }")
}

func demonstratePackageDocumentation() {
	fmt.Println("Package Documentation:")
	
	fmt.Println("1. Package Documentation")
	fmt.Println("   Use doc.go for package-level documentation")
	fmt.Println("   Include examples in documentation")
	fmt.Println("   Document public API clearly")
	
	fmt.Println("\n2. Function Documentation")
	fmt.Println("   Document all public functions")
	fmt.Println("   Include parameter and return value descriptions")
	fmt.Println("   Provide usage examples")
	
	fmt.Println("\n3. Type Documentation")
	fmt.Println("   Document exported types")
	fmt.Println("   Explain type purpose and usage")
	fmt.Println("   Include examples")
	
	fmt.Println("\n4. Examples and Tests")
	fmt.Println("   Include examples in documentation")
	fmt.Println("   Use tests as documentation")
	fmt.Println("   Provide runnable examples")
	
	// Example documentation
	fmt.Println("\n--- Example Documentation ---")
	fmt.Println("// Package calculator provides mathematical operations")
	fmt.Println("// for common calculations and advanced mathematical functions.")
	fmt.Println("//")
	fmt.Println("// Basic Usage:")
	fmt.Println("//")
	fmt.Println("//\tresult := calculator.Add(10, 5)")
	fmt.Println("//\tsum := calculator.Sum([]int{1, 2, 3, 4, 5})")
	fmt.Println("//")
	fmt.Println("// For more advanced operations, see the advanced package.")
	
	// Function documentation
	fmt.Println("// Add returns the sum of two integers.")
	fmt.Println("// It performs basic integer addition and returns the result.")
	fmt.Println("//")
	fmt.Println("// Parameters:")
	fmt.Println("//\ta - First integer to add")
	fmt.Println("//\tb - Second integer to add")
	fmt.Println("//")
	fmt.Println("// Returns:")
	fmt.Println("//\tThe sum of a and b")
	fmt.Println("//")
	fmt.Println("// Example:")
	fmt.Println("//\tresult := calculator.Add(10, 5)")
	fmt.Println("//\tfmt.Println(result) // Output: 15")
	
	// Type documentation
	fmt.Println("// Calculator provides basic mathematical operations.")
	fmt.Println("// It implements the Calculator interface and provides")
	fmt.Println("// methods for common arithmetic operations.")
	fmt.Println("//")
	fmt.Println("// Example:")
	fmt.Println("//\tcalc := calculator.New()")
	fmt.Println("//\tresult := calc.Add(10, 5)")
}

func demonstratePackageTesting() {
	fmt.Println("Package Testing:")
	
	fmt.Println("1. Unit Testing")
	fmt.Println("   Test all public functions")
	fmt.Println("   Test edge cases and error conditions")
	fmt.Println("   Use table-driven tests")
	
	fmt.Println("\n2. Integration Testing")
	fmt.Println("   Test package interactions")
	fmt.Println("   Test with external dependencies")
	fmt.Println("   Use test doubles when needed")
	
	fmt.Println("\n3. Benchmark Testing")
	fmt.Println("   Performance test critical functions")
	fmt.Println("   Compare alternative implementations")
	fmt.Println("   Monitor performance regressions")
	
	fmt.Println("\n4. Example Testing")
	fmt.Println("   Include examples in tests")
	fmt.Println("   Test examples in documentation")
	fmt.Println("   Ensure examples work correctly")
	
	// Show test structure
	fmt.Println("\n--- Test Structure ---")
	fmt.Println("calculator_test.go")
	fmt.Println("├── TestAdd()           // Basic functionality")
	fmt.Println("├── TestAddEdgeCases()   // Edge cases")
	fmt.Println("├── TestAddTable()       // Table-driven tests")
	fmt.Println("├── BenchmarkAdd()     // Performance tests")
	fmt.Println("└── ExampleAdd()       // Example tests")
	
	// Test examples
	fmt.Println("\n--- Test Examples ---")
	fmt.Println("func TestAdd(t *testing.T) {")
	fmt.Println("    tests := []struct {")
	fmt.Println("        name     string")
	fmt.Println("        a, b     int")
	fmt.Println("        expected int")
	fmt.Println("    }{")
	fmt.Println("        {\"positive\", 2, 3, 5},")
	fmt.Println("        {\"negative\", -2, -3, -5},")
	fmt.Println("        {\"zero\", 0, 5, 5},")
	fmt.Println("    }")
	fmt.Println("")
	fmt.Println("    for _, tt := range tests {")
	fmt.Println("        t.Run(tt.name, func(t *testing.T) {")
	fmt.Println("            result := calculator.Add(tt.a, tt.b)")
	fmt.Println("            if result != tt.expected {")
	fmt.Println("                t.Errorf(\"Add(%d, %d) = %d; want %d\",")
	fmt.Println("                    tt.a, tt.b, result, tt.expected)")
	fmt.Println("            }")
	fmt.Println("        })")
	fmt.Println("    }")
	fmt.Println("}")
	
	// Benchmark example
	fmt.Println("\n--- Benchmark Example ---")
	fmt.Println("func BenchmarkAdd(b *testing.B) {")
	fmt.Println("    for i := 0; i < b.N; i++ {")
	fmt.Println("        calculator.Add(100, 200)")
	fmt.Println("    }")
	fmt.Println("}")
	
	// Example test
	fmt.Println("\n--- Example Test ---")
	fmt.Println("func ExampleAdd() {")
	fmt.Println("    result := calculator.Add(2, 3)")
	fmt.Println("    fmt.Println(result) // Output: 5")
	fmt.Println("}")
}

func demonstratePackageVersioning() {
	fmt.Println("Package Versioning:")
	
	fmt.Println("1. Semantic Versioning")
	fmt.Println("   Use MAJOR.MINOR.PATCH format")
	fmt.Println("   MAJOR: Breaking changes")
	fmt.Println("   MINOR: New features, backward compatible")
	fmt.Println("   PATCH: Bug fixes, backward compatible")
	
	fmt.Println("\n2. Version Strategy")
	fmt.Println("   Start with v0.0.1 for development")
	fmt.Println("   Release v1.0.0 for stable API")
	fmt.Println("   Use pre-release versions for testing")
	
	fmt.Println("\n3. Compatibility")
	fmt.Println("   Maintain API compatibility within major versions")
	fmt.Println("   Document breaking changes clearly")
	fmt.Println("   Provide migration guides")
	
	fmt.Println("\n4. Module Version")
	fmt.Println("   Specify version in go.mod")
	fmt.Println("   Use require statements with versions")
	fmt.Println("   Update dependencies carefully")
	
	// Version examples
	fmt.Println("\n--- Version Examples ---")
	fmt.Println("v0.1.0 - Initial development version")
	fmt.Println("v0.2.0 - Added features, still development")
	fmt.Println("v0.3.0 - More features, API stabilizing")
	fmt.Println("v1.0.0 - Stable API release")
	fmt.Println("v1.1.0 - New features, backward compatible")
	fmt.Println("v1.2.0 - More features, backward compatible")
	fmt.Println("v2.0.0 - Breaking changes, new API")
	
	// Module version specification
	fmt.Println("\n--- Module Version Specification ---")
	fmt.Println("require (")
	fmt.Println("    github.com/user/repo v1.2.3")
	fmt.Println("    github.com/other/repo v2.0.0")
	fmt.Println(")")
	
	// Version constraints
	fmt.Println("\n--- Version Constraints ---")
	fmt.Println("v1.2.3    - Exact version")
	fmt.Println(">=v1.2.3  - Minimum version")
	fmt.Println("~v1.2.3   - Patch level changes only")
	fmt.Println("^v1.2.3   - Minor and patch changes")
	fmt.Println("v1.2.3+incompatible - Incompatible version")
}

func demonstratePackagePublishing() {
	fmt.Println("Package Publishing:")
	
	fmt.Println("1. Prepare for Publishing")
	fmt.Println("   Ensure API is stable")
	fmt.Println("   Write comprehensive documentation")
	fmt.Println("   Add examples and tests")
	fmt.Println("   Choose appropriate license")
	
	fmt.Println("\n2. Version Management")
	fmt.Println("   Tag releases with semantic version")
	fmt.Println("   Update go.mod with new version")
	fmt.Println("   Document changes in release notes")
	
	fmt.Println("\n3. Publishing Process")
	fmt.Println("   Tag repository with version")
	fmt.Println("   Push to remote repository")
	fmt.Println("   Users can import with go get")
	
	fmt.Println("\n4. Maintenance")
	fmt.Println("   Monitor for issues and PRs")
	fmt.Println("   Release bug fixes and features")
	fmt.Println("   Keep dependencies updated")
	
	// Publishing steps
	fmt.Println("\n--- Publishing Steps ---")
	fmt.Println("1. git tag v1.0.0")
	fmt.Println("2. git push origin v1.0.0")
	fmt.Println("3. Users: go get github.com/user/repo@v1.0.0")
	fmt.Println("4. Users: go get github.com/user/repo (latest)")
	
	// Module publishing
	fmt.Println("\n--- Module Publishing ---")
	fmt.Println("1. Ensure go.mod is clean")
	fmt.Println("2. Run go mod tidy")
	fmt.Println("3. Tag version: git tag v1.0.0")
	fmt.Println("4. Push tag: git push origin v1.0.0")
	fmt.Println("5. Test import: go get github.com/user/repo@v1.0.0")
	
	// Publishing checklist
	fmt.Println("\n--- Publishing Checklist ---")
	fmt.Println("□ API is stable and documented")
	fmt.Println("□ Tests pass and have good coverage")
	fmt.Println("□ Examples work correctly")
	fmt.Println("□ License is appropriate")
	fmt.Println("□ README is comprehensive")
	fmt.Println("□ CHANGELOG is updated")
	fmt.Println("□ Version is tagged correctly")
	fmt.Println("□ go.mod is clean")
	fmt.Println("□ Dependencies are appropriate")
}

// Package design principles
func showDesignPrinciples() {
	fmt.Println("\n--- Package Design Principles ---")
	
	fmt.Println("1. Single Responsibility Principle")
	fmt.Println("   Each package should have one reason to change")
	fmt.Println("   Keep packages focused and cohesive")
	fmt.Println("   Split large packages when needed")
	
	fmt.Println("\n2. Interface Segregation")
	fmt.Println("   Design small, focused interfaces")
	fmt.Println("   Avoid monolithic interfaces")
	fmt.Println("   Use interface composition")
	
	fmt.Println("\n3. Dependency Inversion")
	fmt.Println("   Depend on abstractions, not concretions")
	fmt.Println("   Use dependency injection")
	fmt.Println("   Make dependencies explicit")
	
	fmt.Println("\n4. Don't Repeat Yourself (DRY)")
	fmt.Println("   Avoid code duplication")
	fmt.Println("   Use helper functions and packages")
	fmt.Println("   Share common functionality")
	
	fmt.Println("\n5. Keep It Simple (KISS)")
	fmt.Println("   Prefer simple solutions")
	fmt.Println("   Avoid over-engineering")
	fmt.Println("   Use clear, readable code")
}

// Common package patterns
func showCommonPatterns() {
	fmt.Println("\n--- Common Package Patterns ---")
	
	fmt.Println("1. Utility Package")
	fmt.Println("   - Collection of related functions")
	fmt.Println("   - No state or side effects")
	fmt.Println("   - Example: strings, math, time")
	
	fmt.Println("\n2. Service Package")
	fmt.Println("   - Provides a specific service")
	fmt.Println("   - May have internal state")
	fmt.Println("   - Example: database, cache")
	
	fmt.Println("\n3. Data Package")
	fmt.Println("   - Defines data structures")
	fmt.Println("   - May include validation")
	fmt.Println("   - Example: models, entities")
	
	fmt.Println("\n4. Client Package")
	fmt.Println("   - External service client")
	fmt.Println("   - Handles communication")
	fmt.Println("   - Example: http, grpc")
	
	fmt.Println("\n5. Config Package")
	fmt.Println("   - Configuration management")
	fmt.Println("   - Environment variables")
	fmt.Println("   - Example: config, settings")
}

// Anti-patterns to avoid
func showAntiPatterns() {
	fmt.Println("\n--- Package Anti-Patterns ---")
	
	fmt.Println("1. Utils Package")
	fmt.Println("   ❌ Too generic")
	fmt.Println("   ❌ Becomes dumping ground")
	fmt.Println("   ✅ Use specific, focused packages")
	
	fmt.Println("\n2. Common Package")
	fmt.Println("   ❌ Generic and unfocused")
	fmt.Println("   ❌ Unclear purpose")
	fmt.Println("   ✅ Describe functionality in name")
	
	fmt.Println("\n3. Circular Dependencies")
	fmt.Println("   ❌ Package A depends on B, B depends on A")
	fmt.Println("   ❌ Creates coupling issues")
	fmt.Println("   ✅ Restructure to break cycles")
	
	fmt.Println("\n4. Large Packages")
	fmt.Println("   ❌ Too much responsibility")
	fmt.Println("   ❌ Hard to navigate")
	fmt.Println("   ✅ Split into smaller packages")
	
	fmt.Println("\n5. Inconsistent API")
	fmt.Println("   ❌ Mixed naming conventions")
	fmt.Println("   ❌ Inconsistent error handling")
	fmt.Println("   ✅ Follow Go conventions")
}

// Package metrics and quality
func showPackageQuality() {
	fmt.Println("\n--- Package Quality Metrics ---")
	
	fmt.Println("1. Code Quality")
	fmt.Println("   - Code complexity")
	fmt.Println("   - Test coverage")
	fmt.Println("   - Documentation coverage")
	
	fmt.Println("\n2. API Design")
	fmt.Println("   - Interface design")
	fmt.Println("   - Error handling")
	fmt.Println("   - Consistency")
	
	fmt.Println("\n3. Dependencies")
	fmt.Println("   - Number of dependencies")
	fmt.Println("   - Dependency quality")
	fmt.Println("   - Circular dependencies")
	
	fmt.Println("\n4. Performance")
	fmt.Println("   - Memory usage")
	fmt.Println("   - CPU usage")
	fmt.Println("   - Scalability")
	
	fmt.Println("\n5. Maintainability")
	fmt.Println("   - Code organization")
	fmt.Println("   - Documentation quality")
	fmt.Println("   - Test quality")
}

// Demonstrate all design concepts
func demonstrateAllDesignConcepts() {
	showDesignPrinciples()
	showCommonPatterns()
	showAntiPatterns()
	showPackageQuality()
}
