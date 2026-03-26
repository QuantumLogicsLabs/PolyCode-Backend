# PolyCode Backend

Express.js backend API serving documentation content with real-time file watching and markdown rendering.

## 🚀 Features

- **Real-time File Watching** - Auto-reloads documentation changes
- **Markdown Rendering** - Converts markdown to HTML with syntax highlighting
- **File System API** - Serves code and documentation from local directories
- **CORS Support** - Cross-origin requests for frontend integration
- **Compression** - Response compression for better performance
- **Rate Limiting** - Built-in protection against API abuse
- **Health Monitoring** - Service health check endpoint

## 📋 Prerequisites

- Node.js 16+ 
- npm or yarn
- Optional: MongoDB (if using database features)

## 🛠️ Installation

```bash
cd backend
npm install
```

## ⚙️ Configuration

Copy `.env.example` to `.env` and configure:

```env
# Server Configuration
PORT=5000
NODE_ENV=development

# Data Folder Path (where documentation lives)
# Default: Looks for data folder relative to backend directory
# PYTHON_PATH=C:\Users\YourName\Documents\Python\data

# Optional Settings
MAX_SCAN_DEPTH=3
INCLUDE_FILE_TYPES=.py,.md,.txt,.js,.html,.css
EXCLUDE_DIRS=node_modules,venv,__pycache__,.git
VERBOSE_LOGGING=false
```

## 🚦 Running the Server

```bash
# Development (with auto-restart on file changes)
npm run dev

# Production
npm start
```

## 📡 API Endpoints

### Documents
- `GET /api/documents` - List all documents and folders
- `GET /api/documents/categories` - Get document categories
- `GET /api/documents/*path` - Get specific document content

### Health & Status
- `GET /api/health` - Server health check

### File Operations
- **Auto-discovery** - Scans data folder for documentation
- **Real-time updates** - File changes trigger automatic reload
- **Markdown parsing** - Converts `.md` files to HTML
- **Code highlighting** - Syntax highlighting for code files

## 📁 Project Structure

```
backend/
├── server.js              # Main Express server
├── routes/
│   └── documents.js       # Document API routes
├── models/                 # Database models (if needed)
├── utils/                 # Helper functions
├── data/                   # Documentation content (external)
├── .env.example           # Environment template
├── package.json           # Dependencies
└── README.md              # This file
```

## 🧩 Dependencies

### Core
- **express** (v5.2.1) - Web framework
- **cors** (v2.8.6) - Cross-origin resource sharing
- **dotenv** (v17.3.1) - Environment variable management

### File Processing
- **chokidar** (v5.0.0) - File system watcher
- **fs-extra** (v11.3.4) - Enhanced file system operations
- **marked** (v17.0.4) - Markdown parser
- **highlight.js** (v11.11.1) - Syntax highlighting

### Performance & Security
- **compression** (v1.7.4) - Gzip compression
- **express-rate-limit** (v7.4.1) - Rate limiting

### Database (Optional)
- **mongoose** (v9.3.0) - MongoDB ODM

### Development
- **nodemon** (v3.1.14) - Auto-restart on changes

## 🔧 Development

### File Watching
The backend automatically watches the data folder for changes:
- New files are detected and added to the API
- Modified files trigger real-time updates
- Deleted files are removed from the API

### Error Handling
- Graceful handling of missing files
- Comprehensive error logging
- Fallback to default configurations

## 🌐 Production Deployment

```bash
# Set production environment
export NODE_ENV=production

# Start the server
npm start
```

### Environment Variables for Production
- `NODE_ENV=production` - Enables production optimizations
- `PORT` - Server port (default: 5000)
- `PYTHON_PATH` - Absolute path to documentation folder

## 🔍 Troubleshooting

### Common Issues

**File not found errors**
- Check `PYTHON_PATH` in `.env`
- Verify data folder exists and contains files

**CORS errors**
- Ensure frontend is running on allowed origins
- Check CORS configuration in `server.js`

**Performance issues**
- Enable compression middleware
- Consider caching strategies
- Monitor file system changes

### Debug Mode
Enable verbose logging:
```env
VERBOSE_LOGGING=true
```

## 📝 License

ISC License

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📞 Support

For issues and questions:
- Check the troubleshooting section
- Review server logs for detailed error messages
- Verify environment configuration
