package main

import (
	"fmt"
	"io/ioutil"
	"os"
	"os/exec"
	"path/filepath"
	"strings"
)

func main() {
	fmt.Println("=== Go Module Dependency Management ===")
	
	// Basic go mod commands
	fmt.Println("\n--- Basic Go Mod Commands ---")
	demonstrateBasicCommands()
	
	// Version management
	fmt.Println("\n--- Version Management ---")
	demonstrateVersionManagement()
	
	// Dependency resolution
	fmt.Println("\n--- Dependency Resolution ---")
	demonstrateDependencyResolution()
	
	// Private modules
	fmt.Println("\n--- Private Modules ---")
	demonstratePrivateModules()
	
	// Module replacement
	fmt.Println("\n--- Module Replacement ---")
	demonstrateModuleReplacement()
	
	// Vendor directory
	fmt.Println("\n--- Vendor Directory ---")
	demonstrateVendorDirectory()
	
	// Module proxy
	fmt.Println("\n--- Module Proxy ---")
	demonstrateModuleProxy()
	
	// Multi-module workspace
	fmt.Println("\n--- Multi-Module Workspace ---")
	demonstrateWorkspace()
}

func demonstrateBasicCommands() {
	fmt.Println("1. go mod init - Initialize a new module")
	fmt.Println("   Usage: go mod init [module-path]")
	fmt.Println("   Example: go mod init github.com/user/project")
	
	fmt.Println("\n2. go mod tidy - Add missing and remove unused dependencies")
	fmt.Println("   Usage: go mod tidy")
	fmt.Println("   This ensures go.mod matches source code")
	
	fmt.Println("\n3. go mod download - Download modules to local cache")
	fmt.Println("   Usage: go mod download")
	fmt.Println("   Downloads all dependencies in go.mod")
	
	fmt.Println("\n4. go mod verify - Verify dependencies have expected content")
	fmt.Println("   Usage: go mod verify")
	fmt.Println("   Checks that dependencies haven't been modified")
	
	fmt.Println("\n5. go mod why - Explain why a package is needed")
	fmt.Println("   Usage: go mod why github.com/pkg/errors")
	fmt.Println("   Shows dependency chain")
	
	// Demonstrate go mod why
	fmt.Println("\n--- Current Module Information ---")
	if err := runCommand("go", "mod", "why", "fmt"); err != nil {
		fmt.Printf("Error: %v\n", err)
	}
}

func demonstrateVersionManagement() {
	fmt.Println("Semantic Versioning in Go Modules:")
	fmt.Println("v1.2.3 - Major.Minor.Patch")
	fmt.Println("v1.2.3-pre - Pre-release version")
	fmt.Println("v1.2.3+meta - Build metadata")
	
	fmt.Println("\n--- Version Constraints ---")
	fmt.Println("v1.2.3      - Exact version")
	fmt.Println(">=v1.2.3    - Minimum version")
	fmt.Println("~v1.2.3     - Patch level changes")
	fmt.Println("^v1.2.3     - Minor level changes")
	
	fmt.Println("\n--- Indirect Dependencies ---")
	if err := runCommand("go", "list", "-m", "all"); err != nil {
		fmt.Printf("Error listing modules: %v\n", err)
	}
}

func demonstrateDependencyResolution() {
	fmt.Println("Go Module Dependency Resolution:")
	fmt.Println("1. Minimal version selection (MVS)")
	fmt.Println("2. Builds a module graph")
	fmt.Println("3. Selects the highest compatible version")
	
	fmt.Println("\n--- Dependency Graph ---")
	if err := runCommand("go", "mod", "graph"); err != nil {
		fmt.Printf("Error generating graph: %v\n", err)
	}
	
	fmt.Println("\n--- Module Requirements ---")
	if err := runCommand("go", "mod", "download", "-json"); err != nil {
		fmt.Printf("Error downloading modules: %v\n", err)
	}
}

func demonstratePrivateModules() {
	fmt.Println("Private Module Configuration:")
	fmt.Println("1. Create .netrc file for authentication")
	fmt.Println("2. Configure GOPRIVATE environment variable")
	fmt.Println("3. Set up module proxy for private modules")
	
	fmt.Println("\n--- GOPRIVATE Example ---")
	fmt.Println("export GOPRIVATE=*.corp.example.com,github.com/company/*")
	
	fmt.Println("\n--- .netrc Example ---")
	fmt.Println("machine github.com")
	fmt.Println("login username")
	fmt.Println("password token")
	
	// Check current GOPRIVATE
	if goprivate := os.Getenv("GOPRIVATE"); goprivate != "" {
		fmt.Printf("Current GOPRIVATE: %s\n", goprivate)
	} else {
		fmt.Println("GOPRIVATE not set")
	}
}

func demonstrateModuleReplacement() {
	fmt.Println("Module Replacement for Local Development:")
	fmt.Println("1. Replace remote module with local version")
	fmt.Println("2. Replace with different version")
	fmt.Println("3. Replace with forked repository")
	
	fmt.Println("\n--- go.mod replace directive ---")
	fmt.Println("replace github.com/original/repo => ../local/repo")
	fmt.Println("replace golang.org/x/text => v0.3.8")
	fmt.Println("replace example.com/fork => github.com/user/fork v1.2.3")
	
	// Check for existing replacements
	if err := runCommand("go", "mod", "edit", "-json"); err != nil {
		fmt.Printf("Error checking go.mod: %v\n", err)
	}
}

func demonstrateVendorDirectory() {
	fmt.Println("Vendor Directory Management:")
	fmt.Println("1. go mod vendor - Create vendor directory")
	fmt.Println("2. Vendored dependencies for reproducible builds")
	fmt.Println("3. Can be used in CI/CD pipelines")
	
	fmt.Println("\n--- Creating Vendor Directory ---")
	if err := runCommand("go", "mod", "vendor"); err != nil {
		fmt.Printf("Error creating vendor directory: %v\n", err)
	} else {
		fmt.Println("Vendor directory created successfully")
		
	// Check if vendor directory exists
		if _, err := os.Stat("vendor"); err == nil {
			fmt.Println("Vendor directory exists")
			
			// Count vendor files
			count := countFiles("vendor")
			fmt.Printf("Vendor directory contains %d files\n", count)
		}
	}
}

func demonstrateModuleProxy() {
	fmt.Println("Go Module Proxy:")
	fmt.Println("1. Default proxy: proxy.golang.org")
	fmt.Println("2. Offline mode: GOPROXY=off")
	fmt.Println("3. Direct downloads: GOPROXY=direct")
	fmt.Println("4. Multiple proxies: proxy1,proxy2,direct")
	
	// Check current GOPROXY
	if goproxy := os.Getenv("GOPROXY"); goproxy != "" {
		fmt.Printf("Current GOPROXY: %s\n", goproxy)
	} else {
		fmt.Println("Using default GOPROXY")
	}
	
	fmt.Println("\n--- Common GOPROXY Settings ---")
	fmt.Println("GOPROXY=direct                    // No proxy")
	fmt.Println("GOPROXY=off                       // Offline mode")
	fmt.Println("GOPROXY=https://proxy.golang.org,direct")
	fmt.Println("GOPROXY=https://company.proxy,direct")
	
	// Check GOSUMDB
	if gosumdb := os.Getenv("GOSUMDB"); gosumdb != "" {
		fmt.Printf("Current GOSUMDB: %s\n", gosumdb)
	} else {
		fmt.Println("Using default GOSUMDB (sum.golang.org)")
	}
}

func demonstrateWorkspace() {
	fmt.Println("Go Workspaces (Multi-Module):")
	fmt.Println("1. go work init - Initialize workspace")
	fmt.Println("2. go work use - Add modules to workspace")
	fmt.Println("3. go work sync - Sync workspace")
	
	fmt.Println("\n--- go.work File Example ---")
	fmt.Println("go 1.18")
	fmt.Println("")
	fmt.Println("use (")
	fmt.Println("    ./module1")
	fmt.Println("    ./module2")
	fmt.Println("    ./module3")
	fmt.Println(")")
	
	// Check if we're in a workspace
	if _, err := os.Stat("go.work"); err == nil {
		fmt.Println("Workspace file found")
		
		if err := runCommand("go", "work", "list"); err != nil {
			fmt.Printf("Error listing workspace: %v\n", err)
		}
	} else {
		fmt.Println("No workspace file found")
	}
}

// Helper functions
func runCommand(name string, args ...string) error {
	cmd := exec.Command(name, args...)
	output, err := cmd.CombinedOutput()
	if err != nil {
		return fmt.Errorf("command failed: %v, output: %s", err, string(output))
	}
	
	// Print output for commands that produce readable output
	if name == "go" && len(args) > 0 {
		switch args[0] {
		case "mod", "list", "work":
			fmt.Printf("Command output:\n%s\n", string(output))
		}
	}
	
	return nil
}

func countFiles(dir string) int {
	count := 0
	
	filepath.Walk(dir, func(path string, info os.FileInfo, err error) error {
		if err != nil {
			return err
		}
		if !info.IsDir() {
			count++
		}
		return nil
	})
	
	return count
}

// Advanced dependency management

func analyzeDependencies() {
	fmt.Println("\n--- Dependency Analysis ---")
	
	// Get all dependencies
	cmd := exec.Command("go", "list", "-m", "all")
	output, err := cmd.Output()
	if err != nil {
		fmt.Printf("Error getting dependencies: %v\n", err)
		return
	}
	
	lines := strings.Split(string(output), "\n")
	direct := make(map[string]bool)
	indirect := make(map[string]bool)
	
	for _, line := range lines {
		line = strings.TrimSpace(line)
		if line == "" {
			continue
		}
		
		if strings.Contains(line, "// indirect") {
			module := strings.TrimSpace(strings.Split(line, "//")[0])
			indirect[module] = true
		} else {
			direct[line] = true
		}
	}
	
	fmt.Printf("Direct dependencies: %d\n", len(direct))
	fmt.Printf("Indirect dependencies: %d\n", len(indirect))
	
	// Show top-level dependencies
	fmt.Println("\n--- Direct Dependencies ---")
	for dep := range direct {
		fmt.Printf("  %s\n", dep)
	}
}

func checkVulnerabilities() {
	fmt.Println("\n--- Security Vulnerability Check ---")
	
	// This would typically use tools like govulncheck
	fmt.Println("To check for vulnerabilities:")
	fmt.Println("1. Install: go install golang.org/x/vuln/cmd/govulncheck@latest")
	fmt.Println("2. Run: govulncheck ./...")
	
	// Simulate vulnerability check
	fmt.Println("Simulating vulnerability check...")
	fmt.Println("No vulnerabilities found")
}

func optimizeDependencies() {
	fmt.Println("\n--- Dependency Optimization ---")
	
	fmt.Println("1. Remove unused dependencies:")
	fmt.Println("   go mod tidy")
	
	fmt.Println("2. Check for outdated dependencies:")
	fmt.Println("   go list -u -m all")
	
	fmt.Println("3. Update dependencies:")
	fmt.Println("   go get -u ./...")
	fmt.Println("   go get package@version")
	
	fmt.Println("4. Verify no breaking changes:")
	fmt.Println("   go test ./...")
	
	// Check for updates
	cmd := exec.Command("go", "list", "-u", "-m", "all")
	output, err := cmd.Output()
	if err != nil {
		fmt.Printf("Error checking updates: %v\n", err)
		return
	}
	
	if strings.Contains(string(output), "[update]") {
		fmt.Println("Updates available:")
		fmt.Println(string(output))
	} else {
		fmt.Println("All dependencies are up to date")
	}
}

func createMinimalModule() {
	fmt.Println("\n--- Creating Minimal Module ---")
	
	// Create a simple module structure
	modulePath := "example.com/minimal-module"
	
	// Create go.mod
	goModContent := fmt.Sprintf("module %s\n\ngo 1.21\n", modulePath)
	err := ioutil.WriteFile("go.mod", []byte(goModContent), 0644)
	if err != nil {
		fmt.Printf("Error creating go.mod: %v\n", err)
		return
	}
	
	// Create main.go
	mainGoContent := `package main

import "fmt"

func main() {
    fmt.Println("Hello from minimal module!")
}
`
	
	err = ioutil.WriteFile("main.go", []byte(mainGoContent), 0644)
	if err != nil {
		fmt.Printf("Error creating main.go: %v\n", err)
		return
	}
	
	fmt.Println("Minimal module created:")
	fmt.Println("- go.mod")
	fmt.Println("- main.go")
	
	// Test the module
	cmd := exec.Command("go", "run", "main.go")
	output, err := cmd.Output()
	if err != nil {
		fmt.Printf("Error running module: %v\n", err)
		return
	}
	
	fmt.Printf("Module output: %s\n", string(output))
	
	// Clean up
	os.Remove("go.mod")
	os.Remove("main.go")
}

func demonstrateAdvancedPatterns() {
	fmt.Println("\n--- Advanced Dependency Patterns ---")
	
	analyzeDependencies()
	checkVulnerabilities()
	optimizeDependencies()
	createMinimalModule()
}

// Module best practices
func showBestPractices() {
	fmt.Println("\n--- Module Best Practices ---")
	
	fmt.Println("1. Use semantic versioning")
	fmt.Println("   - v1.0.0 for stable releases")
	fmt.Println("   - v0.x.y for development releases")
	fmt.Println("   - Pre-release versions for testing")
	
	fmt.Println("\n2. Keep go.mod clean")
	fmt.Println("   - Use 'go mod tidy' regularly")
	fmt.Println("   - Avoid unnecessary direct dependencies")
	fmt.Println("   - Use require statements for direct dependencies only")
	
	fmt.Println("\n3. Version management")
	fmt.Println("   - Use ^ for compatible versions")
	fmt.Println("   - Use specific versions for critical dependencies")
	fmt.Println("   - Review updates before applying")
	
	fmt.Println("\n4. Security")
	fmt.Println("   - Regularly check for vulnerabilities")
	fmt.Println("   - Keep dependencies updated")
	fmt.Println("   - Use private repositories for sensitive code")
	
	fmt.Println("\n5. CI/CD integration")
	fmt.Println("   - Use 'go mod download' in CI")
	fmt.Println("   - Consider vendoring for reproducible builds")
	fmt.Println("   - Cache Go modules in CI")
	
	fmt.Println("\n6. Workspace organization")
	fmt.Println("   - Use workspaces for multi-module projects")
	fmt.Println("   - Keep related modules together")
	fmt.Println("   - Use consistent versioning across workspace")
}

// Common issues and solutions
func showCommonIssues() {
	fmt.Println("\n--- Common Issues and Solutions ---")
	
	fmt.Println("1. Module not found")
	fmt.Println("   Cause: GOPROXY or network issues")
	fmt.Println("   Solution: Check GOPROXY, try direct mode")
	
	fmt.Println("\n2. Version conflicts")
	fmt.Println("   Cause: Incompatible version requirements")
	fmt.Println("   Solution: Use replace directive or update constraints")
	
	fmt.Println("\n3. Indirect dependency issues")
	fmt.Println("   Cause: Transitive dependency conflicts")
	fmt.Println("   Solution: Add direct require with specific version")
	
	fmt.Println("\n4. Private module access")
	fmt.Println("   Cause: Authentication or GOPRIVATE issues")
	fmt.Println("   Solution: Configure .netrc and GOPRIVATE")
	
	fmt.Println("\n5. Build reproducibility")
	fmt.Println("   Cause: Module versions or checksums")
	fmt.Println("   Solution: Use go mod vendor and GOSUMDB")
}

// Module commands reference
func showCommandsReference() {
	fmt.Println("\n--- Go Module Commands Reference ---")
	
	commands := map[string]string{
		"go mod init":       "Initialize a new module",
		"go mod tidy":       "Add missing and remove unused dependencies",
		"go mod download":   "Download modules to local cache",
		"go mod verify":     "Verify dependencies have expected content",
		"go mod why":        "Explain why a package is needed",
		"go mod graph":      "Print module dependency graph",
		"go mod edit":       "Edit go.mod from tools or scripts",
		"go mod vendor":     "Vendor dependencies",
		"go work init":      "Initialize workspace",
		"go work use":       "Add modules to workspace",
		"go work sync":      "Sync workspace",
		"go work edit":      "Edit go.work from tools or scripts",
	}
	
	for cmd, desc := range commands {
		fmt.Printf("%-15s - %s\n", cmd, desc)
	}
}

// Environment variables
func showEnvironmentVariables() {
	fmt.Println("\n--- Environment Variables ---")
	
	vars := map[string]string{
		"GOPATH":         "Go workspace directory (deprecated)",
		"GOMODCACHE":      "Module cache directory",
		"GOPROXY":         "Module proxy list",
		"GOSUMDB":         "Database of module checksums",
		"GOPRIVATE":        "Pattern of private modules",
		"GO111MODULE":      "Module mode (on/off/auto)",
		"GOPROXY_OFF":     "Disable proxy",
		"GONOPROXY":       "Disable proxy for specific modules",
	}
	
	for name, desc := range vars {
		value := os.Getenv(name)
		if value != "" {
			fmt.Printf("%-15s = %s (%s)\n", name, value, desc)
		} else {
			fmt.Printf("%-15s = (not set) (%s)\n", name, desc)
		}
	}
}

// Demonstrate all advanced features
func demonstrateAllAdvancedFeatures() {
	demonstrateAdvancedPatterns()
	showBestPractices()
	showCommonIssues()
	showCommandsReference()
	showEnvironmentVariables()
}
