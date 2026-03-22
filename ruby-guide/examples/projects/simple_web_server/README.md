# Simple Web Server

A basic HTTP web server implemented in Ruby that demonstrates socket programming, HTTP protocol handling, and routing concepts.

## Features

- Basic HTTP request handling
- Static file serving
- Simple routing system
- Request/response handling
- Error handling
- Logging

## Concepts Demonstrated

- Socket programming
- HTTP protocol basics
- Request/response cycle
- Routing implementation
- File serving
- Error handling
- Multi-threading (optional)

## How to Run

```bash
ruby main.rb
```

Then open your web browser and navigate to:
- http://localhost:8080/
- http://localhost:8080/about
- http://localhost:8080/time

## Usage Examples

```
Starting server on port 8080...
Server running at http://localhost:8080/
Press Ctrl+C to stop

GET / HTTP/1.1
Host: localhost:8080
User-Agent: Mozilla/5.0...

200 OK
Content-Type: text/html
Content-Length: 123

<!DOCTYPE html>
<html>
<head><title>Simple Web Server</title></head>
<body><h1>Welcome to Simple Web Server!</h1></body>
</html>
```

## Project Structure

```
simple_web_server/
├── main.rb              # Main server entry point
├── server.rb            # WebServer class
├── request.rb           # HTTP request handling
├── response.rb          # HTTP response handling
├── router.rb            # Routing system
├── public/              # Static files directory
│   ├── index.html
│   ├── about.html
│   └── style.css
└── README.md            # This file
```

## Code Overview

### WebServer Class
Main server class that:
- Listens for connections
- Handles HTTP requests
- Manages threading
- Implements basic logging

### Request Class
Parses HTTP requests with:
- Method parsing (GET, POST, etc.)
- Path extraction
- Header parsing
- Query parameter handling

### Response Class
Builds HTTP responses with:
- Status codes
- Headers
- Content serving
- File serving capabilities

### Router Class
Implements routing with:
- Path-based routing
- Handler registration
- Parameter extraction
- Route matching

## API Endpoints

- `GET /` - Welcome page
- `GET /about` - About page
- `GET /time` - Current time
- `GET /static/*` - Static files

## Extensions to Try

1. **POST support**: Add POST request handling
2. **Templates**: Add template rendering system
3. **Middleware**: Implement middleware system
4. **HTTPS**: Add SSL/TLS support
5. **WebSockets**: Add WebSocket support
6. **REST API**: Build a simple REST API
7. **File uploads**: Handle multipart form data
8. **Authentication**: Add basic auth system
