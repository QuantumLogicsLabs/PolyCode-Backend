# WebServer class - Main HTTP server implementation

require 'socket'
require_relative 'request'
require_relative 'response'
require_relative 'router'

class WebServer
  attr_reader :port, :host, :router

  def initialize(port = 8080, host = 'localhost')
    @port = port
    @host = host
    @router = Router.new
    @running = false
    setup_routes
  end

  def start
    @server = TCPServer.new(@host, @port)
    @running = true
    
    log("Server started on #{@host}:#{@port}")
    
    while @running
      begin
        client = @server.accept
        handle_client(client)
      rescue => e
        log("Error accepting connection: #{e.message}")
      end
    end
  end

  def stop
    @running = false
    @server&.close
    log("Server stopped")
  end

  private

  def handle_client(client)
    Thread.new do
      begin
        request_data = client.readpartial(4096)
        request = Request.parse(request_data)
        
        log("#{request.method} #{request.path}")
        
        response = @router.route(request)
        client.write(response.to_s)
        
      rescue => e
        log("Error handling request: #{e.message}")
        error_response = Response.new(500, "Internal Server Error")
        client.write(error_response.to_s)
      ensure
        client.close
      end
    end
  end

  def setup_routes
    # Root route
    @router.get('/', ->(request) {
      html = <<~HTML
        <!DOCTYPE html>
        <html>
        <head>
          <title>Simple Web Server</title>
          <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            h1 { color: #333; }
            .nav { margin: 20px 0; }
            .nav a { margin-right: 10px; text-decoration: none; color: #007bff; }
            .nav a:hover { text-decoration: underline; }
          </style>
        </head>
        <body>
          <h1>Welcome to Simple Web Server!</h1>
          <p>This is a basic HTTP server implemented in Ruby.</p>
          <div class="nav">
            <a href="/about">About</a>
            <a href="/time">Current Time</a>
            <a href="/static/style.css">CSS File</a>
          </div>
        </body>
        </html>
      HTML
      
      Response.new(200, html, 'text/html')
    })

    # About route
    @router.get('/about', ->(request) {
      html = <<~HTML
        <!DOCTYPE html>
        <html>
        <head>
          <title>About - Simple Web Server</title>
          <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            h1 { color: #333; }
            .info { background: #f5f5f5; padding: 20px; border-radius: 5px; }
          </style>
        </head>
        <body>
          <h1>About This Server</h1>
          <div class="info">
            <p><strong>Server:</strong> Simple Web Server</p>
            <p><strong>Language:</strong> Ruby</p>
            <p><strong>Features:</strong></p>
            <ul>
              <li>Basic HTTP request handling</li>
              <li>Static file serving</li>
              <li>Simple routing system</li>
              <li>Error handling</li>
            </ul>
          </div>
          <p><a href="/">← Back to Home</a></p>
        </body>
        </html>
      HTML
      
      Response.new(200, html, 'text/html')
    })

    # Time route
    @router.get('/time', ->(request) {
      current_time = Time.now.strftime("%Y-%m-%d %H:%M:%S")
      
      html = <<~HTML
        <!DOCTYPE html>
        <html>
        <head>
          <title>Current Time</title>
          <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            h1 { color: #333; }
            .time { font-size: 24px; color: #007bff; background: #f8f9fa; padding: 20px; border-radius: 5px; }
          </style>
        </head>
        <body>
          <h1>Current Server Time</h1>
          <div class="time">#{current_time}</div>
          <p><a href="/">← Back to Home</a></p>
        </body>
        </html>
      HTML
      
      Response.new(200, html, 'text/html')
    })

    # Static file route
    @router.get('/static/*', ->(request) {
      file_path = File.join('public', request.path.sub('/static/', ''))
      
      if File.exist?(file_path) && File.file?(file_path)
        content_type = get_content_type(file_path)
        content = File.read(file_path)
        Response.new(200, content, content_type)
      else
        Response.new(404, "File not found")
      end
    })

    # 404 handler
    @router.not_found = ->(request) {
      html = <<~HTML
        <!DOCTYPE html>
        <html>
        <head>
          <title>404 - Not Found</title>
          <style>
            body { font-family: Arial, sans-serif; margin: 40px; text-align: center; }
            h1 { color: #dc3545; }
          </style>
        </head>
        <body>
          <h1>404 - Page Not Found</h1>
          <p>The page you requested could not be found.</p>
          <p><a href="/">← Back to Home</a></p>
        </body>
        </html>
      HTML
      
      Response.new(404, html, 'text/html')
    }
  end

  def get_content_type(file_path)
    ext = File.extname(file_path).downcase
    
    case ext
    when '.html'
      'text/html'
    when '.css'
      'text/css'
    when '.js'
      'application/javascript'
    when '.json'
      'application/json'
    when '.png'
      'image/png'
    when '.jpg', '.jpeg'
      'image/jpeg'
    when '.gif'
      'image/gif'
    when '.svg'
      'image/svg+xml'
    when '.txt'
      'text/plain'
    else
      'application/octet-stream'
    end
  end

  def log(message)
    puts "[#{Time.now.strftime('%Y-%m-%d %H:%M:%S')}] #{message}"
  end
end
