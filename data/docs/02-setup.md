# Setting up Development Environment

## Prerequisites

Before starting with C#, you'll need to set up your development environment. This guide covers the setup for different operating systems.

## 🖥️ Windows Setup

### Option 1: Visual Studio (Recommended)

1. **Download Visual Studio**
   - Visit [visualstudio.microsoft.com](https://visualstudio.microsoft.com/)
   - Download Visual Studio Community (free)
   - Choose "Community 2022" or latest version

2. **Installation**
   - Run the installer
   - Select ".NET desktop development" workload
   - Check "ASP.NET and web development" (optional)
   - Click "Install"

3. **Verify Installation**
   - Open Visual Studio
   - Create a new project → Console App
   - Ensure .NET 6.0 or later is available

### Option 2: Visual Studio Code + .NET SDK

1. **Install .NET SDK**
   ```powershell
   # Download from https://dotnet.microsoft.com/download
   # Or use winget
   winget install Microsoft.DotNet.SDK.8
   ```

2. **Install Visual Studio Code**
   - Download from [code.visualstudio.com](https://code.visualstudio.com/)
   - Install C# extension from Microsoft

3. **Verify Installation**
   ```powershell
   dotnet --version
   ```

## 🍎 macOS Setup

### Option 1: Visual Studio for Mac

1. **Download Visual Studio for Mac**
   - Visit [visualstudio.microsoft.com/vs/mac/](https://visualstudio.microsoft.com/vs/mac/)
   - Download and install

2. **Installation**
   - Follow the installer prompts
   - Select .NET workloads

### Option 2: VS Code + .NET SDK

1. **Install .NET SDK**
   ```bash
   # Using Homebrew
   brew install --cask dotnet-sdk
   
   # Or download from https://dotnet.microsoft.com/download
   ```

2. **Install Visual Studio Code**
   ```bash
   brew install --cask visual-studio-code
   ```

3. **Install C# Extension**
   - Open VS Code
   - Install C# extension by Microsoft

4. **Verify Installation**
   ```bash
   dotnet --version
   ```

## 🐧 Linux Setup

### Ubuntu/Debian

1. **Install .NET SDK**
   ```bash
   # Add Microsoft package repository
   wget https://packages.microsoft.com/config/ubuntu/22.04/packages-microsoft-prod.deb -O packages-microsoft-prod.deb
   sudo dpkg -i packages-microsoft-prod.deb
   
   # Install .NET SDK
   sudo apt-get update
   sudo apt-get install -y dotnet-sdk-8.0
   ```

2. **Install Visual Studio Code**
   ```bash
   wget -qO- https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor > packages.microsoft.gpg
   sudo install -o root -g root -m 644 packages.microsoft.gpg /etc/apt/trusted.gpg.d/
   sudo sh -c 'echo "deb [arch=amd64,arm64,armhf signed-by=/etc/apt/trusted.gpg.d/packages.microsoft.gpg] https://packages.microsoft.com/repos/code stable main" > /etc/apt/sources.list.d/vscode.list'
   sudo apt-get update
   sudo apt-get install -y code
   ```

3. **Install C# Extension**
   - Open VS Code
   - Install C# extension by Microsoft

### Other Linux Distributions

Visit [Microsoft's documentation](https://docs.microsoft.com/en-us/dotnet/core/install/) for distribution-specific instructions.

## 🛠️ Development Tools

### Essential Tools

1. **IDE/Editor**
   - Visual Studio (Windows)
   - Visual Studio Code (Cross-platform)
   - Rider (JetBrains, paid)
   - SharpDevelop (Free, Windows only)

2. **Version Control**
   - Git (recommended)
   - GitHub Desktop (optional GUI)

3. **Package Management**
   - NuGet (built into .NET)
   - .NET CLI

### Optional Tools

1. **Database Tools**
   - SQL Server Management Studio
   - SQLite Browser
   - MongoDB Compass

2. **API Testing**
   - Postman
   - Swagger/OpenAPI tools

3. **Performance Profiling**
   - dotTrace (JetBrains)
   - Visual Studio Profiler

## 🚀 First Project Setup

### Using Visual Studio

1. **Create New Project**
   - File → New → Project
   - Select "Console App"
   - Click Next

2. **Configure Project**
   - Project name: `HelloWorld`
   - Location: Choose your workspace
   - Framework: .NET 8.0 (or latest)
   - Click Create

3. **Run the Project**
   - Press F5 or click "Start"
   - You should see "Hello, World!" in the console

### Using .NET CLI

1. **Create New Project**
   ```bash
   dotnet new console -n HelloWorld
   cd HelloWorld
   ```

2. **Run the Project**
   ```bash
   dotnet run
   ```

## 🔧 Configuration

### Git Configuration

```bash
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"
```

### VS Code Settings

Create `.vscode/settings.json`:
```json
{
    "csharp.suppressDotnetRestoreNotification": true,
    "omnisharp.enableRoslynAnalyzers": true,
    "omnisharp.enableEditorConfigSupport": true,
    "omnisharp.enableImportCompletion": true,
    "dotnet.completion.showCompletionItemsFromUnimportedNamespaces": true
}
```

## 🐛 Troubleshooting

### Common Issues

1. **"dotnet command not found"**
   - Ensure .NET SDK is in your PATH
   - Restart your terminal/command prompt

2. **Visual Studio can't find .NET SDK**
   - Repair Visual Studio installation
   - Ensure matching versions

3. **Permission issues (Linux/macOS)**
   ```bash
   chmod +x /usr/local/bin/dotnet
   ```

4. **Project won't build**
   - Run `dotnet restore`
   - Check for missing dependencies

### Getting Help

- [Microsoft .NET Documentation](https://docs.microsoft.com/en-us/dotnet/)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/c%23)
- [C# Discord Community](https://discord.gg/csharp)

## ✅ Verification Checklist

- [ ] .NET SDK installed (version 6.0+)
- [ ] IDE/Editor installed and configured
- [ ] Can create and run a console project
- [ ] Git is configured
- [ ] Extensions are installed (if using VS Code)

## 🎯 Next Steps

With your environment set up, you're ready to write your first C# program!

[Your First C# Program →](03-first-program.md)

---

**Environment setup complete! Let's start coding! 🚀**
