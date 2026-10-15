package main

import (
	"fmt"
	"strings"
)

// Standalone mock implementations for demonstration
type calculatorPkg struct{}

func (c calculatorPkg) Add(a, b int) int { return a + b }

var calculator = calculatorPkg{}

type formatterPkg struct{}

func (f formatterPkg) FormatGreeting(name string) string {
	return fmt.Sprintf("Hello, %s!", name)
}

var formatter = formatterPkg{}

type validatorPkg struct{}

func (v validatorPkg) IsValidEmail(email string) bool {
	return strings.Contains(email, "@") && strings.Contains(email, ".")
}

var validator = validatorPkg{}

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

	fmt.Println("\n--- Current Package Dependencies ---")
	fmt.Println("main.go imports:")
	fmt.Println("  - calculator")
	fmt.Println("  - formatter")
	fmt.Println("  - validator")

	fmt.Println("\n--- Using Custom Packages ---")

	result := calculator.Add(10, 5)
	fmt.Printf("Calculator.Add(10, 5) = %d\n", result)

	greeting := formatter.FormatGreeting("Go Developer")
	fmt.Printf("Formatter greeting: %s\n", greeting)

	email := "user@example.com"
	isValid := validator.IsValidEmail(email)
	fmt.Printf("Validator.IsValidEmail(\"%s\") = %t\n", email, isValid)

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

	fmt.Println("\n--- Example Interface Design ---")
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
}

func demonstratePackageVersioning() {
	fmt.Println("Package Versioning:")

	fmt.Println("1. Semantic Versioning")
	fmt.Println("   Use MAJOR.MINOR.PATCH format")
	fmt.Println("   MAJOR: Breaking changes")
	fmt.Println("   MINOR: New features, backward compatible")
	fmt.Println("   PATCH: Bug fixes, backward compatible")
}

func demonstratePackagePublishing() {
	fmt.Println("Package Publishing:")

	fmt.Println("1. Prepare for Publishing")
	fmt.Println("   Ensure API is stable")
	fmt.Println("   Write comprehensive documentation")
	fmt.Println("   Add examples and tests")
	fmt.Println("   Choose appropriate license")

	fmt.Println("\n2. Publishing Steps")
	fmt.Println("   1. git tag v1.0.0")
	fmt.Println("   2. git push origin v1.0.0")
	fmt.Println("   3. Users: go get github.com/user/repo@v1.0.0")
}
