# 🐍 Python Docs Hub — MERN Stack

A full-stack MERN documentation website that **automatically reads your Python folder** (or exported `.txt` file), parses every `.md` and `.py` file, converts them to beautiful HTML, and displays them with live syntax-highlighted code examples.

---

## ✨ Features

| Feature | Details |
|---|---|
| 🔄 **Auto-Ingestion** | Reads your Python folder on startup — no manual imports |
| 🐍 **Python Syntax Highlighting** | Every `.py` file rendered with full highlighting |
| 📝 **Markdown Rendering** | All `.md` files converted to rich HTML |
| 🔍 **Full-Text Search** | Search across titles, content & tags |
| 📂 **Category Browser** | Browse by folder/category |
| 📊 **Statistics Dashboard** | Document counts, word counts, breakdowns |
| 👀 **Live File Watching** | Changes to your Python folder auto-update the DB |
| 📋 **Copy Code** | One-click copy on every code block |

---

## 🏗️ Tech Stack

```
Frontend  → React 18, React Router, ReactMarkdown, react-syntax-highlighter
Backend   → Node.js, Express.js
Database  → MongoDB + Mongoose
Parser    → marked (markdown), chokidar (file watching)
```

---

## 🚀 Quick Start

### Prerequisites
- Node.js ≥ 18
- MongoDB running locally (`mongod`) or a MongoDB Atlas URI

### 1. Clone / unzip this project

```bash
cd python-docs-mern
```

### 2. Install dependencies

```bash
npm install          # installs concurrently at root
npm run install:all  # installs backend + frontend deps
```

### 3. Configure environment

```bash
cp backend/.env.example backend/.env
# Edit backend/.env if needed (default: mongodb://127.0.0.1:27017/python_docs)
```

### 4. Place your Python documentation

**Option A — Python folder** *(recommended)*
Place (or symlink) your Python folder next to `python-docs-mern/`:

```
parent-folder/
├── Python/          ← your existing Python folder
└── python-docs-mern/
```

**Option B — Exported .txt file**
Copy your `Python.txt` export into the data folder:

```bash
mkdir -p backend/../data
cp /path/to/Python.txt data/Python.txt
```

### 5. Start the app

```bash
npm run dev
```

This starts:
- **Backend** on http://localhost:5000 (auto-ingests all docs)
- **Frontend** on http://localhost:3000

Open http://localhost:3000 🎉

---

## 📁 Project Structure

```
python-docs-mern/
├── package.json          ← root scripts (concurrently)
├── data/                 ← put Python.txt here (Option B)
│
├── backend/
│   ├── server.js         ← Express app + auto-ingestion entry point
│   ├── models/
│   │   └── Document.js   ← Mongoose schema
│   ├── routes/
│   │   └── documents.js  ← REST API routes
│   └── utils/
│       └── fileParser.js ← Markdown/Python parser + HTML converter
│
└── frontend/
    └── src/
        ├── App.js
        ├── App.css        ← Dark theme design system
        ├── components/
        │   ├── Navbar.js
        │   ├── Sidebar.js
        │   ├── DocCard.js
        │   ├── CodeBlock.js       ← Syntax highlighted code
        │   └── MarkdownRenderer.js
        └── pages/
            ├── HomePage.js
            ├── DocumentPage.js   ← Full doc view
            ├── CategoryPage.js
            ├── SearchPage.js
            └── StatsPage.js
```

---

## 🔌 API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/documents` | List docs (supports `?category=`, `?search=`, `?fileType=`, `?page=`, `?limit=`) |
| GET | `/api/documents/stats` | Total counts, by category, by file type |
| GET | `/api/documents/categories` | Distinct category list |
| GET | `/api/documents/:id` | Single document with full content + code blocks |

---

## ⚙️ How Auto-Ingestion Works

On startup, the server checks in this order:

1. **`../Python/` folder** — walks the whole tree, parses every `.md` and `.py` file
2. **`./data/Python.txt`** — parses the exported monolithic dump file
3. **Existing MongoDB data** — uses what's already in the database

Files are upserted by path, so re-running never creates duplicates. If a real folder is found, **chokidar** watches it for live changes.

---

## 🎨 Customization

- **Theme colors** — edit CSS variables in `frontend/src/App.css`
- **Add file types** — edit `SUPPORTED_EXTENSIONS` in `backend/utils/fileParser.js`
- **Add categories** — edit `CATEGORY_META` in `frontend/src/utils/categories.js`

---

## 🛠️ Production Build

```bash
npm run build          # builds React to frontend/build/
NODE_ENV=production npm start  # serves static files from Express
```

---

*Built for the Python Development Hub — March 2026*
