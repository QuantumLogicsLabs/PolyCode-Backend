#!/usr/bin/env ruby

# Simple Web Server - Main entry point
# A basic HTTP server demonstrating socket programming and HTTP handling

require_relative 'server'

def main
  port = ARGV[0] ? ARGV[0].to_i : 8080
  
  puts "Starting Simple Web Server..."
  puts "Server will run on http://localhost:#{port}/"
  puts "Press Ctrl+C to stop"
  puts
  
  server = WebServer.new(port)
  server.start
rescue Interrupt
  puts "\nServer stopped."
rescue => e
  puts "Error starting server: #{e.message}"
  puts e.backtrace if ENV['DEBUG']
  exit 1
end

# Run the server
main if __FILE__ == $0
