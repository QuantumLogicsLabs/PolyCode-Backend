# 03 — Environment Setup

## Prerequisites

- .NET SDK 8.0 or later
- A code editor (VS Code recommended)
- Git (optional but recommended)

---

## Step 1: Install .NET SDK

### Windows

```powershell
winget install Microsoft.DotNet.SDK.8
# OR download from: https://dotnet.microsoft.com/download
```

### macOS

```bash
brew install dotnet
# OR download from: https://dotnet.microsoft.com/download
```

### Linux (Ubuntu/Debian)

```bash
wget https://packages.microsoft.com/config/ubuntu/22.04/packages-microsoft-prod.deb
sudo dpkg -i packages-microsoft-prod.deb
sudo apt-get update && sudo apt-get install -y dotnet-sdk-8.0
```

**Verify installation:**

```bash
dotnet --version
# Should output: 8.x.x
```

---

## Step 2: Install the Quantum Development Kit

```bash
dotnet new install Microsoft.Quantum.ProjectTemplates
```

Verify it worked:

```bash
dotnet new list | grep quantum
# Should show: qsharp, qsharp-library, etc.
```

---

## Step 3: Install VS Code + Q# Extension

1. Download [VS Code](https://code.visualstudio.com/)
2. Open VS Code → Extensions (`Ctrl+Shift+X`)
3. Search for **"Azure Quantum Development Kit"**
4. Click Install

The extension provides:

- Syntax highlighting
- IntelliSense / autocomplete
- Inline circuit diagrams
- Integrated debugger

---

## Step 4: Create Your First Project

```bash
# Create a new Q# project
dotnet new console -lang Q# -o HelloQuantum
cd HelloQuantum

# Project structure:
# HelloQuantum/
# ├── HelloQuantum.csproj
# └── Program.qs
```

Open in VS Code:

```bash
code .
```

---

## Step 5: Run Your First Program

The default `Program.qs` already has a sample. Run it:

```bash
dotnet run
```

You should see output like:

```
Hello, quantum world!
```

---

## Optional: Jupyter Notebook Setup

For interactive quantum notebooks:

```bash
pip install qsharp
pip install azure-quantum

# Launch Jupyter
jupyter notebook
```

In a notebook cell, use the `%%qsharp` magic command:

```
%%qsharp
open Microsoft.Quantum.Diagnostics;

operation HelloWorld() : Unit {
    Message("Hello from Q# notebook!");
}
```

---

## Optional: Python Integration

```bash
pip install qsharp
```

```python
import qsharp

# Evaluate Q# inline
result = qsharp.eval("""
    open Microsoft.Quantum.Intrinsic;
    operation FlipQubit() : Result {
        use q = Qubit();
        X(q);
        return M(q);
    }
    FlipQubit()
""")
print(result)  # One
```

---

## Troubleshooting

| Problem                    | Solution                                                    |
| -------------------------- | ----------------------------------------------------------- |
| `dotnet` not found         | Restart terminal after install                              |
| Template not found         | Run `dotnet new install Microsoft.Quantum.ProjectTemplates` |
| Q# extension not working   | Ensure .NET SDK is installed first                          |
| `pip install qsharp` fails | Use Python 3.8+ and ensure .NET is installed                |

---

## Verifying Everything Works

Run this checklist:

```bash
# ✅ .NET SDK
dotnet --version

# ✅ Q# templates
dotnet new list | grep qsharp

# ✅ Create and run project
dotnet new qsharp -o test && cd test && dotnet run
```

---

_Next: [04 — Hello, Quantum World!](04-hello-quantum.md)_
