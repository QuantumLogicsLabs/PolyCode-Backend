#!/usr/bin/env ruby

# Contact Book - Main entry point
# A command-line contact management application

require_relative 'contact_manager'

def main
  puts "Welcome to Contact Book!"
  puts "This application helps you manage your contacts."
  puts
  
  manager = ContactManager.new
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
