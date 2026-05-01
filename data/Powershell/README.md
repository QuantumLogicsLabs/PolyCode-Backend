# PowerShell Learning Guide

A comprehensive learning resource for mastering PowerShell from beginner to advanced levels.

## 📚 Course Structure

### 🎯 Getting Started
- **[01-Basics](docs/01-basics.md)** - Introduction to PowerShell, cmdlets, and the PowerShell console
- **[02-Variables and Data Types](docs/02-variables-and-data-types.md)** - Understanding variables, data types, and type casting
- **[03-Operators](docs/03-operators.md)** - Arithmetic, comparison, logical, and special operators

### 🔧 Core Programming Concepts
- **[04-Conditional Statements](docs/04-conditional-statements.md)** - If/Else, Switch statements
- **[05-Loops](docs/05-loops.md)** - For, While, Do-While, ForEach loops
- **[06-Functions](docs/06-functions.md)** - Creating and using functions, parameters, return values

### 📊 Data Structures
- **[07-Arrays and Collections](docs/07-arrays-and-collections.md)** - Working with arrays, lists, and other collections
- **[08-Hashtables and Objects](docs/08-hashtables-and-objects.md)** - Hashtables, custom objects, and PSCustomObject

### 🗂️ File System and Administration
- **[09-File System Operations](docs/09-file-system.md)** - File and directory management
- **[10-Modules and Scripts](docs/10-modules-and-scripts.md)** - Script organization, modules, and scope

### 🛡️ Advanced Topics
- **[11-Error Handling](docs/11-error-handling.md)** - Try/Catch, error handling best practices
- **[12-Advanced Topics](docs/12-advanced-topics.md)** - Remoting, jobs, workflows, DSC

## 📁 Directory Structure

```
PowerShell-Learning-Guide/
├── README.md                    # This file
├── docs/                        # Documentation files
│   ├── 01-basics.md
│   ├── 02-variables-and-data-types.md
│   ├── 03-operators.md
│   ├── 04-conditional-statements.md
│   ├── 05-loops.md
│   ├── 06-functions.md
│   ├── 07-arrays-and-collections.md
│   ├── 08-hashtables-and-objects.md
│   ├── 09-file-system.md
│   ├── 10-modules-and-scripts.md
│   ├── 11-error-handling.md
│   └── 12-advanced-topics.md
├── scripts/                     # Example scripts
│   ├── basic-examples.ps1
│   ├── intermediate-examples.ps1
│   └── advanced-examples.ps1
└── examples/                    # Practical examples and projects
    ├── system-administration/
    ├── automation/
    └── reporting/
```

## 🚀 Quick Start

1. **Install PowerShell**: Ensure you have PowerShell 7+ installed
   ```powershell
   winget install Microsoft.PowerShell
   ```

2. **Check your PowerShell version**:
   ```powershell
   $PSVersionTable
   ```

3. **Set execution policy** (if needed):
   ```powershell
   Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
   ```

4. **Start learning**: Begin with [01-Basics](docs/01-basics.md) and work through each section sequentially

## 🎯 Learning Path

### Beginner (Weeks 1-2)
- Complete sections 01-06
- Practice with basic scripts in the `scripts/` directory
- Focus on understanding cmdlets and basic programming concepts

### Intermediate (Weeks 3-4)
- Complete sections 07-10
- Work on file system operations and script organization
- Start building practical automation scripts

### Advanced (Weeks 5-6)
- Complete sections 11-12
- Learn error handling and advanced PowerShell features
- Work on complex projects in the `examples/` directory

## 💡 Tips for Learning

- **Practice daily**: Even 15-30 minutes of practice helps reinforce concepts
- **Use the console**: Try commands directly in PowerShell before putting them in scripts
- **Read the help**: Use `Get-Help` cmdlet to learn about any command
- **Experiment**: Modify examples and see what happens
- **Join the community**: Participate in PowerShell forums and communities

## 🔧 Useful Commands

```powershell
# Get help for any command
Get-Help Get-Process

# Get detailed help with examples
Get-Help Get-Process -Detailed

# Find commands
Get-Command *process*

# Get member information
Get-Process | Get-Member

# View available modules
Get-Module -ListAvailable
```

## 📖 Additional Resources

- [Official PowerShell Documentation](https://docs.microsoft.com/en-us/powershell/)
- [PowerShell GitHub Repository](https://github.com/PowerShell/PowerShell)
- [PowerShell Community](https://powershell.org/)
- [PowerShell Blog](https://devblogs.microsoft.com/powershell/)

## 🤝 Contributing

Feel free to contribute to this learning guide by:
- Adding new examples
- Improving explanations
- Fixing typos or errors
- Suggesting new topics

## 📄 License

This learning guide is provided for educational purposes. Feel free to share and adapt for your learning needs.

---

**Happy Learning! 🚀**
