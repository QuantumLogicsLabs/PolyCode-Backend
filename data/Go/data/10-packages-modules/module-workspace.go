package main

import (
	"fmt"
	"os"
	"os/exec"
	"path/filepath"
	"strings"
)

func main() {
	fmt.Println("=== Go Module Workspaces ===")
	
	// Basic workspace concepts
	fmt.Println("\n--- Basic Workspace Concepts ---")
	demonstrateWorkspaceBasics()
	
	// Creating a workspace
	fmt.Println("\n--- Creating a Workspace ---")
	demonstrateWorkspaceCreation()
	
	// Managing workspaces
	fmt.Println("\n--- Managing Workspaces ---")
	demonstrateWorkspaceManagement()
	
	// Workspace patterns
	fmt.Println("\n--- Workspace Patterns ---")
	demonstrateWorkspacePatterns()
	
	// Multi-module development
	fmt.Println("\n--- Multi-Module Development ---")
	demonstrateMultiModuleDevelopment()
	
	// Build and testing
	fmt.Println("\n--- Build and Testing ---")
	demonstrateBuildAndTesting()
	
	// CI/CD with workspaces
	fmt.Println("\n--- CI/CD with Workspaces ---")
	demonstrateCICD()
	
	// Best practices
	fmt.Println("\n--- Workspace Best Practices ---")
	demonstrateBestPractices()
}

func demonstrateWorkspaceBasics() {
	fmt.Println("Go Module Workspaces:")
	fmt.Println("1. Workspaces allow managing multiple modules together")
	fmt.Println("2. Share code between modules without publishing")
	fmt.Println("3. Consistent dependency versions across modules")
	fmt.Println("4. Simplify local development")
	
	fmt.Println("\n--- Workspace Commands ---")
	fmt.Println("go work init        - Initialize workspace")
	fmt.Println("go work use         - Add module to workspace")
	fmt.Println("go work sync        - Sync workspace")
	fmt.Println("go work edit        - Edit go.work file")
	fmt.Println("go work list        - List modules in workspace")
	
	fmt.Println("\n--- Workspace Files ---")
	fmt.Println("go.work            - Workspace definition")
	fmt.Println("go.work.sum        - Workspace checksums")
	fmt.Println("go.mod            - Module definition (in each module)")
	fmt.Println("go.sum            - Module checksums (in each module)")
	
	// Check if we're in a workspace
	if _, err := os.Stat("go.work"); err == nil {
		fmt.Println("✅ Currently in a workspace")
	} else {
		fmt.Println("❌ Not in a workspace")
	}
}

func demonstrateWorkspaceCreation() {
	fmt.Println("Creating a Go Workspace:")
	
	fmt.Println("\n--- Step 1: Initialize Workspace ---")
	fmt.Println("go work init")
	fmt.Println("Creates go.work file with go version")
	
	fmt.Println("\n--- Step 2: Add Modules to Workspace ---")
	fmt.Println("go work use ./module1")
	fmt.Println("go work use ./module2")
	fmt.Println("go work use ./module3")
	fmt.Println("Adds modules to go.work file")
	
	fmt.Println("\n--- Step 3: Sync Workspace ---")
	fmt.Println("go work sync")
	fmt.Println("Downloads dependencies and builds modules")
	
	// Show workspace file structure
	fmt.Println("\n--- Workspace File Structure ---")
	fmt.Println("my-project/")
	fmt.Println("├── go.work")
	fmt.Println("├── module1/")
	fmt.Println("│   ├── go.mod")
	fmt.Println("│   ├── go.sum")
	fmt.Println("│   └── main.go")
	fmt.Println("├── module2/")
	fmt.Println("│   ├── go.mod")
	fmt.Println("│   ├── go.sum")
	fmt.Println("│   └── lib.go")
	fmt.Println("├── module3/")
	fmt.Println("│   ├── go.mod")
	fmt.Println("│   ├── go.sum")
	fmt.Println("│   └── utils.go")
	fmt.Println("└── README.md")
	
	// Example go.work file
	fmt.Println("\n--- Example go.work File ---")
	fmt.Println("go 1.21")
	fmt.Println("")
	fmt.Println("use (")
	fmt.Println("    ./module1")
	fmt.Println("    ./module2")
	fmt.Println("    ./module3")
	fmt.Println(")")
	
	// Create example workspace
	createExampleWorkspace()
}

func createExampleWorkspace() {
	fmt.Println("\n--- Creating Example Workspace ---")
	
	// Create workspace structure
	modules := []string{"app", "lib", "utils"}
	
	for _, module := range modules {
		modulePath := filepath.Join("example-workspace", module)
		err := os.MkdirAll(modulePath, 0755)
		if err != nil {
			fmt.Printf("Error creating %s: %v\n", module, err)
			continue
		}
		
		// Create go.mod
		goModContent := fmt.Sprintf("module example.com/project/%s\n\ngo 1.21\n", module)
		goModPath := filepath.Join(modulePath, "go.mod")
		err = os.WriteFile(goModPath, []byte(goModContent), 0644)
		if err != nil {
			fmt.Printf("Error creating go.mod for %s: %v\n", module, err)
		}
		
		// Create main.go
		mainGoContent := fmt.Sprintf("package main\n\nimport \"fmt\"\n\nfunc main() {\n    fmt.Println(\"Hello from %s\")\n}\n", module)
		mainGoPath := filepath.Join(modulePath, "main.go")
		err = os.WriteFile(mainGoPath, []byte(mainGoContent), 0644)
		if err != nil {
			fmt.Printf("Error creating main.go for %s: %v\n", module, err)
		}
	}
	
	// Initialize workspace
	workspaceDir := "example-workspace"
	cmd := exec.Command("go", "work", "init")
	cmd.Dir = workspaceDir
	err := cmd.Run()
	if err != nil {
		fmt.Printf("Error initializing workspace: %v\n", err)
		return
	}
	
	// Add modules to workspace
	for _, module := range modules {
		cmd = exec.Command("go", "work", "use", fmt.Sprintf("./%s", module))
		cmd.Dir = workspaceDir
		err = cmd.Run()
		if err != nil {
			fmt.Printf("Error adding %s to workspace: %v\n", module, err)
		}
	}
	
	fmt.Println("Example workspace created successfully!")
	
	// List workspace modules
	cmd = exec.Command("go", "work", "list")
	cmd.Dir = workspaceDir
	output, err := cmd.Output()
	if err != nil {
		fmt.Printf("Error listing workspace: %v\n", err)
	} else {
		fmt.Printf("Workspace modules:\n%s\n", string(output))
	}
}

func demonstrateWorkspaceManagement() {
	fmt.Println("Managing Go Workspaces:")
	
	fmt.Println("\n--- Adding Modules ---")
	fmt.Println("go work use ./new-module")
	fmt.Println("Adds new module to workspace")
	
	fmt.Println("\n--- Removing Modules ---")
	fmt.Println("Edit go.work file to remove module")
	fmt.Println("go work sync")
	fmt.Println("Sync changes")
	
	fmt.Println("\n--- Replacing Modules ---")
	fmt.Println("go work use ./module => ../local-module")
	fmt.Println("Replace module with local version")
	
	fmt.Println("\n--- Syncing Dependencies ---")
	fmt.Println("go work sync")
	fmt.Println("Download and build all dependencies")
	
	fmt.Println("\n--- Listing Modules ---")
	fmt.Println("go work list")
	fmt.Println("List all modules in workspace")
	
	// Show workspace management commands
	fmt.Println("\n--- Workspace Management Commands ---")
	commands := map[string]string{
		"go work init":   "Initialize workspace",
		"go work use":    "Add module to workspace",
		"go work sync":   "Sync workspace",
		"go work edit":   "Edit go.work file",
		"go work list":   "List workspace modules",
	}
	
	for cmd, desc := range commands {
		fmt.Printf("%-15s - %s\n", cmd, desc)
	}
}

func demonstrateWorkspacePatterns() {
	fmt.Println("Workspace Patterns:")
	
	fmt.Println("\n--- 1. Monorepo Pattern ---")
	fmt.Println("All related modules in one repository")
	fmt.Println("Shared libraries and applications")
	fmt.Println("Consistent tooling and CI/CD")
	
	fmt.Println("\n--- 2. Library + Application Pattern ---")
	fmt.Println("Core library modules")
	fmt.Println("Application modules using libraries")
	fmt.Println("Easy sharing of common code")
	
	fmt.Println("\n--- 3. Service Pattern ---")
	fmt.Println("Multiple microservices")
	fmt.Println("Shared protocol definitions")
	fmt.Println("Common client libraries")
	
	fmt.Println("\n--- 4. Tool Pattern ---")
	fmt.Println("CLI tools in separate modules")
	fmt.Println("Shared utility libraries")
	fmt.Println("Plugin architecture")
	
	// Show pattern examples
	fmt.Println("\n--- Pattern Examples ---")
	
	fmt.Println("Monorepo Structure:")
	fmt.Println("company/")
	fmt.Println("├── go.work")
	fmt.Println("├── auth-service/")
	fmt.Println("├── user-service/")
	fmt.Println("├── payment-service/")
	fmt.Println("├── shared/")
	fmt.Println("│   ├── auth/")
	fmt.Println("│   ├── database/")
	fmt.Println("│   └── utils/")
	fmt.Println("└── tools/")
	fmt.Println("    ├── cli/")
	fmt.Println("    └── migrator/")
	
	fmt.Println("\nLibrary + Application:")
	fmt.Println("project/")
	fmt.Println("├── go.work")
	fmt.Println("├── app/")
	fmt.Println("├── lib/")
	fmt.Println("├── cmd/")
	fmt.Println("└── internal/")
	
	fmt.Println("\nService Pattern:")
	fmt.Println("services/")
	fmt.Println("├── go.work")
	fmt.Println("├── user/")
	fmt.Println("├── order/")
	fmt.Println("├── product/")
	fmt.Println("├── shared/")
	fmt.Println("│   ├── proto/")
	fmt.Println("│   ├── client/")
	fmt.Println("│   └── types/")
	fmt.Println("└── scripts/")
}

func demonstrateMultiModuleDevelopment() {
	fmt.Println("Multi-Module Development:")
	
	fmt.Println("\n--- Cross-Module Dependencies ---")
	fmt.Println("Modules can depend on each other")
	fmt.Println("Use relative paths for local modules")
	fmt.Println("Replace remote modules with local paths")
	
	fmt.Println("\n--- Shared Dependencies ---")
	fmt.Println("Consistent versions across modules")
	fmt.Println("Single go.sum file for workspace")
	fmt.Println("No version conflicts")
	
	fmt.Println("\n--- Code Sharing ---")
	fmt.Println("Share code between modules easily")
	fmt.Println("No need to publish to use code")
	fmt.Println("Local development is simplified")
	
	fmt.Println("\n--- Build Optimization ---")
	fmt.Println("Build all modules together")
	fmt.Println("Share build cache")
	fmt.Println("Parallel builds")
	
	// Show cross-module usage
	fmt.Println("\n--- Cross-Module Usage Example ---")
	fmt.Println("// In app/main.go")
	fmt.Println("import \"example.com/project/lib\"")
	fmt.Println("import \"example.com/project/utils\"")
	fmt.Println("")
	fmt.Println("func main() {")
	fmt.Println("    result := lib.Calculate(10, 20)")
	fmt.Println("    message := utils.Format(result)")
	fmt.Println("    fmt.Println(message)")
	fmt.Println("}")
	
	// Show module dependencies
	fmt.Println("\n--- Module Dependencies ---")
	fmt.Println("app:")
	fmt.Println("  - lib (local)")
	fmt.Println("  - utils (local)")
	fmt.Println("  - github.com/fmt (remote)")
	fmt.Println("")
	fmt.Println("lib:")
	fmt.Println("  - utils (local)")
	fmt.Println("  - github.com/math (remote)")
	fmt.Println("")
	fmt.Println("utils:")
	fmt.Println("  - no dependencies")
	fmt.Println("  - or only standard library")
}

func demonstrateBuildAndTesting() {
	fmt.Println("Build and Testing in Workspaces:")
	
	fmt.Println("\n--- Building ---")
	fmt.Println("go build ./...")
	fmt.Println("Build all modules in workspace")
	fmt.Println("Share build artifacts")
	
	fmt.Println("\n--- Testing ---")
	fmt.Println("go test ./...")
	fmt.Println("Test all modules in workspace")
	fmt.Println("Share test cache")
	
	fmt.Println("\n--- Running Commands ---")
	fmt.Println("go run ./app/cmd/server")
	fmt.Println("Run specific module command")
	fmt.Println("Works from workspace root")
	
	fmt.Println("\n--- Linting ---")
	fmt.Println("golangci-lint run ./...")
	fmt.Println("Lint all modules")
	fmt.Println("Consistent linting")
	
	// Show build examples
	fmt.Println("\n--- Build Examples ---")
	fmt.Println("# Build all modules")
	fmt.Println("go build ./...")
	fmt.Println("")
	fmt.Println("# Build specific module")
	fmt.Println("go build ./app")
	fmt.Println("")
	fmt.Println("# Run specific module")
	fmt.Println("go run ./app/cmd/server")
	fmt.Println("")
	fmt.Println("# Test all modules")
	fmt.Println("go test ./...")
	fmt.Println("")
	fmt.Println("# Test specific module")
	fmt.Println("go test ./lib")
	
	// Show testing patterns
	fmt.Println("\n--- Testing Patterns ---")
	fmt.Println("1. Unit Tests")
	fmt.Println("   go test ./lib/...")
	fmt.Println("")
	fmt.Println("2. Integration Tests")
	fmt.Println("   go test ./tests/...")
	fmt.Println("")
	fmt.Println("3. End-to-End Tests")
	fmt.Println("   go test ./e2e/...")
	fmt.Println("")
	fmt.Println("4. Benchmarks")
	fmt.Println("go test -bench ./...")
	fmt.Println("")
	fmt.Println("5. Race Tests")
	fmt.Println("go test -race ./...")
}

func demonstrateCICD() {
	fmt.Println("CI/CD with Workspaces:")
	
	fmt.Println("\n--- GitHub Actions ---")
	fmt.Println("name: CI")
	fmt.Println("on: [push, pull_request]")
	fmt.Println("jobs:")
	fmt.Println("  test:")
	fmt.Println("    runs-on: ubuntu-latest")
	fmt.Println("    steps:")
	fmt.Println("      - uses: actions/checkout@v2")
	fmt.Println("      - uses: actions/setup-go@v2")
	fmt.Println("      - run: go work sync")
	fmt.Println("      - run: go test ./...")
	fmt.Println("      - run: go build ./...")
	
	fmt.Println("\n--- Build Matrix ---")
	fmt.Println("strategy:")
	fmt.Println("  matrix:")
	fmt.Println("    go-version: [1.19, 1.20, 1.21]")
	fmt.Println("    os: [ubuntu-latest, windows-latest]")
	fmt.Println("  steps:")
	fmt.Println("    - uses: actions/setup-go@v${{ matrix.go-version }}")
	fmt.Println("    - run: go work sync")
	fmt.Println("    - run: go test ./...")
	
	fmt.Println("\n--- Docker Build ---")
	fmt.Println("FROM golang:1.21-alpine")
	fmt.Println("WORKDIR /app")
	fmt.Println("COPY go.work go.mod go.sum ./")
	fmt.Println("COPY . ./")
	fmt.Println("RUN go work sync")
	fmt.Println("RUN go build ./...")
	fmt.Println("CMD [\"./app/server\"]")
	
	fmt.Println("\n--- Deployment ---")
	fmt.Println("1. Build all modules")
	fmt.Println("2. Run tests")
	fmt.Println("3. Build Docker images")
	fmt.Println("4. Deploy to production")
	fmt.Println("5. Run integration tests")
	
	// Show CI/CD best practices
	fmt.Println("\n--- CI/CD Best Practices ---")
	fmt.Println("✅ Cache Go modules")
	fmt.Println("✅ Run tests in parallel")
	fmt.Println("✅ Use workspace for consistent builds")
	fmt.Println("✅ Test on multiple Go versions")
	fmt.Println("✅ Test on multiple OS")
	fmt.Println("✅ Build Docker images")
	fmt.Println("✅ Use security scanning")
}

func demonstrateBestPractices() {
	fmt.Println("Workspace Best Practices:")
	
	fmt.Println("\n--- 1. Workspace Organization ---")
	fmt.Println("✅ Keep related modules together")
	fmt.Println("✅ Use clear directory structure")
	fmt.Println("✅ Document workspace structure")
	fmt.Println("✅ Use consistent naming")
	
	fmt.Println("\n--- 2. Module Dependencies ---")
	fmt.Println("✅ Minimize cross-module dependencies")
	fmt.Println("✅ Use interfaces for module boundaries")
	fmt.Println("✅ Avoid circular dependencies")
	fmt.Println("✅ Keep dependencies up to date")
	
	fmt.Println("\n--- 3. Version Management ---")
	fmt.Println("✅ Use consistent Go version")
	fmt.Println("✅ Update dependencies together")
	fmt.Println("✅ Use semantic versioning")
	fmt.Println("✅ Tag releases properly")
	
	fmt.Println("\n--- 4. Development Workflow ---")
	fmt.Println("✅ Use go work sync regularly")
	fmt.Println("✅ Test changes across modules")
	fmt.Println("✅ Use local module replacement")
	fmt.Println("✅ Keep workspace clean")
	
	fmt.Println("\n--- 5. CI/CD Integration ---")
	fmt.Println("✅ Test all modules")
	fmt.Println("✅ Use workspace-aware CI")
	fmt.Println("✅ Cache build artifacts")
	fmt.Println("✅ Use consistent tooling")
	
	// Show workspace structure best practices
	fmt.Println("\n--- Workspace Structure Best Practices ---")
	fmt.Println("my-project/")
	fmt.Println("├── go.work")
	fmt.Println("├── go.work.sum")
	fmt.Println("├── README.md")
	fmt.Println("├── .github/")
	fmt.Println("│   └── workflows/")
	fmt.Println("│       ├── ci.yml")
	fmt.Println("│       └── release.yml")
	fmt.Println("├── cmd/")
	fmt.Println("│   ├── app/")
	fmt.Println("│   └── cli/")
	fmt.Println("├── internal/")
	fmt.Println("│   ├── auth/")
	fmt.Println("│   ├── database/")
	fmt.Println("│   └── utils/")
	fmt.Println("├── pkg/")
	fmt.Println("│   ├── client/")
	fmt.Println("│   └── types/")
	fmt.Println("├── api/")
	fmt.Println("│   ├── v1/")
	fmt.Println("│   └── v2/")
	fmt.Println("├── scripts/")
	fmt.Println("│   ├── build.sh")
	fmt.Println("│   └── test.sh")
	fmt.Println("├── docs/")
	fmt.Println("├── examples/")
	fmt.Println("├── tests/")
	fmt.Println("└── tools/")
	
	// Show go.work best practices
	fmt.Println("\n--- go.work Best Practices ---")
	fmt.Println("go 1.21")
	fmt.Println("")
	fmt.Println("use (")
	fmt.Println("    ./cmd/app")
	fmt.Println("    ./internal/auth")
	fmt.Println("    ./internal/database")
	fmt.Println("    ./pkg/client")
	fmt.Println("    ./api/v1")
	fmt.Println(")")
	fmt.Println("")
	fmt.Println("replace github.com/example/lib => ./lib")
	
	// Show module best practices
	fmt.Println("\n--- Module Best Practices ---")
	fmt.Println("module github.com/company/project")
	fmt.Println("")
	fmt.Println("go 1.21")
	fmt.Println("")
	fmt.Println("require (")
	fmt.Println("    github.com/company/lib v1.2.3")
	fmt.Println("    github.com/google/uuid v1.3.0")
	fmt.Println(")")
	fmt.Println("")
	fmt.Println("replace github.com/company/lib => ../lib")
}

// Workspace troubleshooting
func showTroubleshooting() {
	fmt.Println("\n--- Workspace Troubleshooting ---")
	
	fmt.Println("\n--- Common Issues ---")
	fmt.Println("1. Module not found in workspace")
	fmt.Println("   Cause: Module not added to go.work")
	fmt.Println("   Solution: run 'go work use ./module'")
	
	fmt.Println("\n2. Version conflicts")
	fmt.Println("   Cause: Different versions in different modules")
	fmt.Println("   Solution: Use replace directive")
	
	fmt.Println("\n3. Build failures")
	fmt.Println("   Cause: Missing dependencies")
	fmt.Println("   Solution: run 'go work sync'")
	
	fmt.Println("\n4. Test failures")
	fmt.Println("   Cause: Cross-module issues")
	fmt.Println("   Solution: Test from workspace root")
	
	fmt.Println("\n5. IDE issues")
	fmt.Println("   Cause: IDE not recognizing workspace")
	fmt.Println("   Solution: Restart IDE, check settings")
	
	// Show troubleshooting commands
	fmt.Println("\n--- Troubleshooting Commands ---")
	fmt.Println("go work list          # List workspace modules")
	fmt.Println("go work sync          # Sync workspace")
	fmt.Println("go mod tidy          # Tidy modules")
	fmt.Println("go mod download      # Download dependencies")
	fmt.Println("go mod verify        # Verify dependencies")
	fmt.Println("go list -m all       # List all modules")
	fmt.Println("go build ./...       # Build all modules")
	fmt.Println("go test ./...        # Test all modules")
}

// Advanced workspace features
func showAdvancedFeatures() {
	fmt.Println("\n--- Advanced Workspace Features ---")
	
	fmt.Println("\n--- 1. Workspace with Replace ---")
	fmt.Println("replace github.com/external/repo => ./local/repo")
	fmt.Println("Use local version of external dependency")
	
	fmt.Println("\n--- 2. Multiple Workspaces ---")
	fmt.Println("Multiple go.work files in different directories")
	fmt.Println("Use specific workspace with -work flag")
	
	fmt.Println("\n--- 3. Workspace with Vendor ---")
	fmt.Println("go mod vendor")
	fmt.Println("Vendor all dependencies")
	fmt.Println("Reproducible builds")
	
	fmt.Println("\n--- 4. Workspace with Private Modules ---")
	fmt.Println("GOPRIVATE=github.com/company/*")
	fmt.Println("Use private modules in workspace")
	
	fmt.Println("\n--- 5. Workspace with Build Tags ---")
	fmt.Println("Use build tags for conditional compilation")
	fmt.Println("Build specific module variants")
}

// Demonstrate all workspace concepts
func demonstrateAllWorkspaceConcepts() {
	showTroubleshooting()
	showAdvancedFeatures()
}
