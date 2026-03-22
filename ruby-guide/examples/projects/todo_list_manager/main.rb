#!/usr/bin/env ruby

# Todo List Manager - Main entry point
# A command-line todo application demonstrating Ruby concepts

require_relative 'todo_manager'

def main
  puts "Welcome to Todo List Manager!"
  puts "This application helps you manage your daily tasks."
  puts
  
  manager = TodoManager.new
  manager.start
rescue Interrupt
  puts "\n\nApplication interrupted. Goodbye!"
rescue => e
  puts "\nAn unexpected error occurred: #{e.message}"
  puts "Error details: #{e.class}"
  exit 1
end

# Run the application
main if __FILE__ == $0
