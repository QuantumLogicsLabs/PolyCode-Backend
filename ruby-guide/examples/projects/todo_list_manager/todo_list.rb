# TodoList class - Manages a collection of todos

require 'json'
require_relative 'todo'

class TodoList
  attr_reader :todos

  def initialize
    @todos = []
  end

  def add_todo(description)
    validate_description(description)
    todo = Todo.new(description)
    @todos << todo
    todo
  end

  def remove_todo(id)
    todo = find_todo(id)
    @todos.delete(todo)
  end

  def find_todo(id)
    @todos.find { |todo| todo.id == id }
  end

  def complete_todo(id)
    todo = find_todo(id)
    return nil unless todo
    
    todo.complete!
    todo
  end

  def incomplete_todo(id)
    todo = find_todo(id)
    return nil unless todo
    
    todo.incomplete!
    todo
  end

  def toggle_todo(id)
    todo = find_todo(id)
    return nil unless todo
    
    todo.toggle_completion
    todo
  end

  def all_todos
    @todos.dup
  end

  def completed_todos
    @todos.select(&:completed?)
  end

  def incomplete_todos
    @todos.reject(&:completed?)
  end

  def count
    @todos.length
  end

  def completed_count
    completed_todos.length
  end

  def incomplete_count
    incomplete_todos.length
  end

  def clear_completed
    @todos.reject!(&:completed?)
  end

  def clear_all
    @todos.clear
  end

  def to_a
    @todos.map(&:to_h)
  end

  def to_json(*args)
    to_a.to_json(*args)
  end

  def save_to_file(filename)
    File.write(filename, to_json)
  end

  def load_from_file(filename)
    return false unless File.exist?(filename)
    
    begin
      data = JSON.parse(File.read(filename))
      @todos = data.map { |todo_data| Todo.from_hash(todo_data) }
      true
    rescue JSON::ParserError => e
      puts "Error parsing file: #{e.message}"
      false
    rescue => e
      puts "Error loading file: #{e.message}"
      false
    end
  end

  def display_todos
    if @todos.empty?
      puts "No todos found."
      return
    end

    puts "\nTodos:"
    puts "=" * 50
    
    @todos.each_with_index do |todo, index|
      status = todo.completed? ? "✓" : "○"
      puts "#{index + 1}. [#{status}] #{todo.description}"
      puts "   Created: #{todo.created_at.strftime('%Y-%m-%d %H:%M')}"
      puts
    end
    
    puts "Summary: #{completed_count}/#{count} completed"
  end

  private

  def validate_description(description)
    raise ArgumentError, "Description cannot be empty" if description.nil? || description.strip.empty?
    raise ArgumentError, "Description too long (max 200 characters)" if description.length > 200
  end
end
