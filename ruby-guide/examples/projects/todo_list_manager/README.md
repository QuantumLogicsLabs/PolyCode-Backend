# Todo List Manager

A command-line todo list manager that demonstrates object-oriented programming, file I/O, and user interaction in Ruby.

## Features

- Add, complete, and delete todos
- List all todos with status
- Save todos to file
- Load todos from file
- Mark todos as complete/incomplete

## Concepts Demonstrated

- Classes and objects
- File I/O operations
- JSON serialization
- Command-line interface
- Data validation
- Error handling

## How to Run

```bash
ruby main.rb
```

## Usage Examples

```
Todo List Manager
================

1. Add todo
2. List todos
3. Complete todo
4. Delete todo
5. Save todos
6. Load todos
7. Exit

Enter your choice (1-7): 1
Enter todo description: Learn Ruby programming
Added: Learn Ruby programming

Enter your choice (1-7): 2
Todos:
[ ] 1. Learn Ruby programming

Enter your choice (1-7): 3
Enter todo number to complete: 1
Completed: Learn Ruby programming

Enter your choice (1-7): 5
Todos saved to todos.json
```

## Project Structure

```
todo_list_manager/
├── main.rb              # Main application entry point
├── todo.rb              # Todo class definition
├── todo_list.rb         # TodoList class definition
├── todo_manager.rb      # Main manager class
├── todos.json           # Data file (created when saving)
└── README.md            # This file
```

## Code Overview

### Todo Class
Represents a single todo item with:
- Description
- Completion status
- Creation time
- Unique ID

### TodoList Class
Manages a collection of todos with:
- Add/remove operations
- Search/filter functionality
- JSON serialization

### TodoManager Class
Handles user interaction and:
- Command-line interface
- File operations
- Input validation

## Extensions to Try

1. **Priority levels**: Add high, medium, low priorities
2. **Due dates**: Add date/time tracking
3. **Categories**: Organize todos by category
4. **Search**: Implement search functionality
5. **Undo/Redo**: Add operation history
6. **Colors**: Use color codes for better UI
