# TodoManager class - Handles user interaction and application flow

require_relative 'todo_list'

class TodoManager
  def initialize
    @todo_list = TodoList.new
    @data_file = 'todos.json'
  end

  def start
    load_todos if File.exist?(@data_file)
    
    loop do
      display_menu
      choice = get_user_choice
      
      case choice
      when 1
        add_todo
      when 2
        list_todos
      when 3
        complete_todo
      when 4
        delete_todo
      when 5
        save_todos
      when 6
        load_todos
      when 7
        save_todos
        puts "Goodbye!"
        break
      else
        puts "Invalid choice. Please try again."
      end
      
      puts "\nPress Enter to continue..."
      gets
    end
  end

  private

  def display_menu
    system('clear') || system('cls')
    puts "Todo List Manager"
    puts "=" * 50
    puts "1. Add todo"
    puts "2. List todos"
    puts "3. Complete todo"
    puts "4. Delete todo"
    puts "5. Save todos"
    puts "6. Load todos"
    puts "7. Exit"
    puts "=" * 50
  end

  def get_user_choice
    print "\nEnter your choice (1-7): "
    gets.chomp.to_i
  end

  def add_todo
    print "Enter todo description: "
    description = gets.chomp
    
    begin
      todo = @todo_list.add_todo(description)
      puts "Added: #{todo.description}"
    rescue ArgumentError => e
      puts "Error: #{e.message}"
    end
  end

  def list_todos
    @todo_list.display_todos
  end

  def complete_todo
    return if @todo_list.empty?
    
    list_todos
    print "Enter todo number to complete (or 0 to cancel): "
    number = gets.chomp.to_i
    
    return if number == 0
    
    if number > 0 && number <= @todo_list.count
      todo = @todo_list.todos[number - 1]
      @todo_list.complete_todo(todo.id)
      puts "Completed: #{todo.description}"
    else
      puts "Invalid todo number."
    end
  end

  def delete_todo
    return if @todo_list.empty?
    
    list_todos
    print "Enter todo number to delete (or 0 to cancel): "
    number = gets.chomp.to_i
    
    return if number == 0
    
    if number > 0 && number <= @todo_list.count
      todo = @todo_list.todos[number - 1]
      @todo_list.remove_todo(todo.id)
      puts "Deleted: #{todo.description}"
    else
      puts "Invalid todo number."
    end
  end

  def save_todos
    begin
      @todo_list.save_to_file(@data_file)
      puts "Todos saved to #{@data_file}"
    rescue => e
      puts "Error saving todos: #{e.message}"
    end
  end

  def load_todos
    begin
      if @todo_list.load_from_file(@data_file)
        puts "Todos loaded from #{@data_file}"
        puts "Loaded #{@todo_list.count} todos"
      else
        puts "Failed to load todos from #{@data_file}"
      end
    rescue => e
      puts "Error loading todos: #{e.message}"
    end
  end
end
