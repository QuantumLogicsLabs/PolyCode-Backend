# PowerShell Learning Guide

A comprehensive learning resource for mastering PowerShell from beginner through production-ready module engineering.

## 📚 Course Structure

Follow the numbered [certificate course](data/README.md) in order:

1. Beginner — core syntax, control flow, functions, pipelines, and objects
2. Intermediate — modules, remoting, error handling, scripting, regex, and jobs
3. Advanced — desired state, APIs, security, performance, testing, and automation
4. Professional — Azure, directory services, DevOps, reporting, and enterprise scripts
5. Mastery — automation, tool building, capstones, career planning, [production-ready modules](data/05-mastery/05-production-modules.md), and [cross-platform CI](data/05-mastery/06-cross-platform-ci.md)

Start with the [course roadmap](data/00-certification/00-roadmap.md) and complete the [final assessment](data/00-certification/02-final-assessment.md) after Mastery.

## 📁 Directory Structure

```
Powershell/
├── README.md
└── data/
    ├── 00-certification/
    ├── 01-beginner/
    ├── 02-intermediate/
    ├── 03-advanced/
    ├── 04-professional/
    └── 05-mastery/
        ├── 01-advanced-automation.md
        ├── 02-tool-building.md
        ├── 03-capstone-projects.md
        ├── 04-career-path.md
        ├── 05-production-modules.md
        ├── 06-cross-platform-ci.md
        └── examples/
            ├── OperationsToolkit/
            └── ci/powershell-module-ci.yml
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

4. **Start learning**: Begin with the [course roadmap](data/00-certification/00-roadmap.md) and work through each module sequentially

## 🎯 Learning Path

Progress through Beginner, Intermediate, Advanced, Professional, and Mastery. The roadmap estimates 8–14 weeks at 5–8 hours per week; adjust the pace to match your practice time.

## Interactive Courses

The PowerShell catalog includes six interactive courses. Every chapter has six lessons, and each lesson includes a focused practice challenge:

| Course | Chapters | Lessons |
| --- | ---: | ---: |
| PowerShell Fundamentals | 6 | 36 |
| PowerShell Scripting | 6 | 36 |
| PowerShell Administration | 6 | 36 |
| PowerShell Projects | 6 | 36 |
| PowerShell Security & Compliance | 6 | 36 |
| PowerShell Cloud Automation | 6 | 36 |

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
