"""
Task Manager Application
A comprehensive task management system with categories, priorities, and deadlines.
"""

import json
import os
from datetime import datetime, timedelta
from enum import Enum

class Priority(Enum):
    """Task priority levels."""
    LOW = 1
    MEDIUM = 2
    HIGH = 3
    URGENT = 4

class Status(Enum):
    """Task status levels."""
    TODO = "To Do"
    IN_PROGRESS = "In Progress"
    COMPLETED = "Completed"
    CANCELLED = "Cancelled"

class Task:
    """Individual task representation."""
    
    def __init__(self, title, description="", priority=Priority.MEDIUM, 
                 category="General", due_date=None):
        self.id = None
        self.title = title
        self.description = description
        self.priority = priority
        self.category = category
        self.status = Status.TODO
        self.due_date = due_date
        self.created_at = datetime.now()
        self.updated_at = datetime.now()
        self.completed_at = None
    
    def to_dict(self):
        """Convert task to dictionary for JSON serialization."""
        return {
            'id': self.id,
            'title': self.title,
            'description': self.description,
            'priority': self.priority.value,
            'category': self.category,
            'status': self.status.value,
            'due_date': self.due_date.isoformat() if self.due_date else None,
            'created_at': self.created_at.isoformat(),
            'updated_at': self.updated_at.isoformat(),
            'completed_at': self.completed_at.isoformat() if self.completed_at else None
        }
    
    @classmethod
    def from_dict(cls, data):
        """Create task from dictionary."""
        task = cls(
            title=data['title'],
            description=data.get('description', ''),
            priority=Priority(data.get('priority', Priority.MEDIUM.value)),
            category=data.get('category', 'General'),
            due_date=datetime.fromisoformat(data['due_date']) if data.get('due_date') else None
        )
        task.id = data.get('id')
        task.status = Status(data.get('status', Status.TODO.value))
        task.created_at = datetime.fromisoformat(data['created_at'])
        task.updated_at = datetime.fromisoformat(data['updated_at'])
        task.completed_at = datetime.fromisoformat(data['completed_at']) if data.get('completed_at') else None
        return task
    
    def update_status(self, new_status):
        """Update task status and timestamps."""
        self.status = new_status
        self.updated_at = datetime.now()
        if new_status == Status.COMPLETED:
            self.completed_at = datetime.now()
    
    def is_overdue(self):
        """Check if task is overdue."""
        if not self.due_date or self.status in [Status.COMPLETED, Status.CANCELLED]:
            return False
        return datetime.now() > self.due_date
    
    def days_until_due(self):
        """Calculate days until due date."""
        if not self.due_date:
            return None
        delta = self.due_date - datetime.now()
        return delta.days
    
    def __str__(self):
        """String representation of task."""
        priority_icon = {Priority.LOW: "🟢", Priority.MEDIUM: "🟡", 
                        Priority.HIGH: "🟠", Priority.URGENT: "🔴"}
        status_icon = {Status.TODO: "⭕", Status.IN_PROGRESS: "🔄", 
                      Status.COMPLETED: "✅", Status.CANCELLED: "❌"}
        
        return f"{priority_icon[self.priority]} {status_icon[self.status]} {self.title}"

class TaskManager:
    """Main task management system."""
    
    def __init__(self, data_file="tasks.json"):
        self.data_file = data_file
        self.tasks = []
        self.next_id = 1
        self.categories = set()
        self.load_tasks()
    
    def load_tasks(self):
        """Load tasks from JSON file."""
        if os.path.exists(self.data_file):
            try:
                with open(self.data_file, 'r') as f:
                    data = json.load(f)
                    self.next_id = data.get('next_id', 1)
                    for task_data in data.get('tasks', []):
                        task = Task.from_dict(task_data)
                        self.tasks.append(task)
                        self.categories.add(task.category)
            except Exception as e:
                print(f"Error loading tasks: {e}")
    
    def save_tasks(self):
        """Save tasks to JSON file."""
        try:
            data = {
                'next_id': self.next_id,
                'tasks': [task.to_dict() for task in self.tasks]
            }
            with open(self.data_file, 'w') as f:
                json.dump(data, f, indent=2)
        except Exception as e:
            print(f"Error saving tasks: {e}")
    
    def add_task(self, title, description="", priority=Priority.MEDIUM, 
                 category="General", due_date=None):
        """Add a new task."""
        task = Task(title, description, priority, category, due_date)
        task.id = self.next_id++
        self.tasks.append(task)
        self.categories.add(category)
        self.save_tasks()
        return task
    
    def get_task(self, task_id):
        """Get task by ID."""
        for task in self.tasks:
            if task.id == task_id:
                return task
        return None
    
    def update_task(self, task_id, **kwargs):
        """Update task properties."""
        task = self.get_task(task_id)
        if task:
            for key, value in kwargs.items():
                if hasattr(task, key):
                    setattr(task, key, value)
            task.updated_at = datetime.now()
            if 'category' in kwargs:
                self.categories.add(kwargs['category'])
            self.save_tasks()
            return True
        return False
    
    def delete_task(self, task_id):
        """Delete a task."""
        task = self.get_task(task_id)
        if task:
            self.tasks.remove(task)
            self.save_tasks()
            return True
        return False
    
    def get_tasks_by_status(self, status):
        """Get tasks filtered by status."""
        return [task for task in self.tasks if task.status == status]
    
    def get_tasks_by_priority(self, priority):
        """Get tasks filtered by priority."""
        return [task for task in self.tasks if task.priority == priority]
    
    def get_tasks_by_category(self, category):
        """Get tasks filtered by category."""
        return [task for task in self.tasks if task.category == category]
    
    def get_overdue_tasks(self):
        """Get all overdue tasks."""
        return [task for task in self.tasks if task.is_overdue()]
    
    def get_tasks_due_soon(self, days=3):
        """Get tasks due within specified days."""
        cutoff_date = datetime.now() + timedelta(days=days)
        return [task for task in self.tasks 
                if task.due_date and task.due_date <= cutoff_date 
                and task.status not in [Status.COMPLETED, Status.CANCELLED]]
    
    def search_tasks(self, query):
        """Search tasks by title or description."""
        query = query.lower()
        return [task for task in self.tasks 
                if query in task.title.lower() or query in task.description.lower()]
    
    def get_statistics(self):
        """Get task statistics."""
        stats = {
            'total': len(self.tasks),
            'by_status': {},
            'by_priority': {},
            'by_category': {},
            'overdue': len(self.get_overdue_tasks()),
            'due_soon': len(self.get_tasks_due_soon())
        }
        
        for status in Status:
            stats['by_status'][status.value] = len(self.get_tasks_by_status(status))
        
        for priority in Priority:
            stats['by_priority'][priority.name] = len(self.get_tasks_by_priority(priority))
        
        for category in self.categories:
            stats['by_category'][category] = len(self.get_tasks_by_category(category))
        
        return stats

class TaskManagerCLI:
    """Command-line interface for Task Manager."""
    
    def __init__(self):
        self.manager = TaskManager()
    
    def display_menu(self):
        """Display main menu."""
        print("\n" + "="*50)
        print("TASK MANAGER")
        print("="*50)
        print("1. Add Task")
        print("2. List Tasks")
        print("3. Update Task")
        print("4. Delete Task")
        print("5. Search Tasks")
        print("6. View Statistics")
        print("7. View Overdue Tasks")
        print("8. Exit")
        print("-"*50)
    
    def add_task_interactive(self):
        """Interactive task creation."""
        print("\nAdd New Task")
        print("-"*20)
        
        title = input("Title: ").strip()
        if not title:
            print("Title is required!")
            return
        
        description = input("Description (optional): ").strip()
        
        print("\nPriority:")
        for i, priority in enumerate(Priority, 1):
            print(f"{i}. {priority.name}")
        priority_choice = input("Select priority (1-4): ").strip()
        priority = Priority(int(priority_choice)) if priority_choice.isdigit() else Priority.MEDIUM
        
        category = input("Category (default: General): ").strip() or "General"
        
        due_date_input = input("Due date (YYYY-MM-DD, optional): ").strip()
        due_date = None
        if due_date_input:
            try:
                due_date = datetime.strptime(due_date_input, "%Y-%m-%d")
            except ValueError:
                print("Invalid date format, using no due date")
        
        task = self.manager.add_task(title, description, priority, category, due_date)
        print(f"\n✅ Task added: {task}")
    
    def list_tasks(self, tasks=None):
        """Display tasks in a formatted table."""
        if tasks is None:
            tasks = self.manager.tasks
        
        if not tasks:
            print("No tasks found.")
            return
        
        print("\nTasks:")
        print("-"*80)
        print(f"{'ID':<4} {'Title':<20} {'Priority':<10} {'Status':<12} {'Category':<12} {'Due':<12}")
        print("-"*80)
        
        for task in sorted(tasks, key=lambda t: (t.priority.value, t.due_date or datetime.max)):
            due_str = task.due_date.strftime("%Y-%m-%d") if task.due_date else "No due date"
            if task.is_overdue():
                due_str += " ⚠️"
            
            print(f"{task.id:<4} {task.title[:18]:<20} {task.priority.name:<10} "
                  f"{task.status.value:<12} {task.category[:10]:<12} {due_str:<12}")
    
    def update_task_interactive(self):
        """Interactive task update."""
        self.list_tasks()
        
        try:
            task_id = int(input("\nEnter task ID to update: "))
            task = self.manager.get_task(task_id)
            
            if not task:
                print("Task not found!")
                return
            
            print(f"\nCurrent task: {task}")
            print("\nWhat to update?")
            print("1. Status")
            print("2. Priority")
            print("3. Category")
            print("4. Due date")
            
            choice = input("Select option (1-4): ").strip()
            
            if choice == "1":
                print("\nStatus options:")
                for i, status in enumerate(Status, 1):
                    print(f"{i}. {status.value}")
                status_choice = input("Select status (1-4): ").strip()
                if status_choice.isdigit():
                    new_status = list(Status)[int(status_choice) - 1]
                    self.manager.update_task(task_id, status=new_status)
                    print(f"✅ Status updated to {new_status.value}")
            
            elif choice == "2":
                print("\nPriority options:")
                for i, priority in enumerate(Priority, 1):
                    print(f"{i}. {priority.name}")
                priority_choice = input("Select priority (1-4): ").strip()
                if priority_choice.isdigit():
                    new_priority = list(Priority)[int(priority_choice) - 1]
                    self.manager.update_task(task_id, priority=new_priority)
                    print(f"✅ Priority updated to {new_priority.name}")
            
            elif choice == "3":
                new_category = input("New category: ").strip()
                if new_category:
                    self.manager.update_task(task_id, category=new_category)
                    print(f"✅ Category updated to {new_category}")
            
            elif choice == "4":
                due_date_input = input("New due date (YYYY-MM-DD, or 'none'): ").strip()
                if due_date_input.lower() == 'none':
                    self.manager.update_task(task_id, due_date=None)
                    print("✅ Due date removed")
                else:
                    try:
                        new_due_date = datetime.strptime(due_date_input, "%Y-%m-%d")
                        self.manager.update_task(task_id, due_date=new_due_date)
                        print(f"✅ Due date updated to {new_due_date.strftime('%Y-%m-%d')}")
                    except ValueError:
                        print("Invalid date format")
        
        except ValueError:
            print("Invalid task ID!")
    
    def delete_task_interactive(self):
        """Interactive task deletion."""
        self.list_tasks()
        
        try:
            task_id = int(input("\nEnter task ID to delete: "))
            task = self.manager.get_task(task_id)
            
            if not task:
                print("Task not found!")
                return
            
            confirm = input(f"Delete task '{task.title}'? (y/N): ").strip().lower()
            if confirm == 'y':
                self.manager.delete_task(task_id)
                print("✅ Task deleted")
            else:
                print("Deletion cancelled")
        
        except ValueError:
            print("Invalid task ID!")
    
    def search_tasks_interactive(self):
        """Interactive task search."""
        query = input("Enter search term: ").strip()
        if query:
            results = self.manager.search_tasks(query)
            print(f"\nFound {len(results)} tasks matching '{query}':")
            self.list_tasks(results)
    
    def display_statistics(self):
        """Display task statistics."""
        stats = self.manager.get_statistics()
        
        print("\nTask Statistics")
        print("-"*30)
        print(f"Total tasks: {stats['total']}")
        print(f"Overdue tasks: {stats['overdue']}")
        print(f"Due soon: {stats['due_soon']}")
        
        print("\nBy Status:")
        for status, count in stats['by_status'].items():
            print(f"  {status}: {count}")
        
        print("\nBy Priority:")
        for priority, count in stats['by_priority'].items():
            print(f"  {priority}: {count}")
        
        print("\nBy Category:")
        for category, count in stats['by_category'].items():
            print(f"  {category}: {count}")
    
    def run(self):
        """Run the CLI application."""
        print("Welcome to Task Manager!")
        
        while True:
            self.display_menu()
            choice = input("Select option (1-8): ").strip()
            
            if choice == "1":
                self.add_task_interactive()
            elif choice == "2":
                self.list_tasks()
            elif choice == "3":
                self.update_task_interactive()
            elif choice == "4":
                self.delete_task_interactive()
            elif choice == "5":
                self.search_tasks_interactive()
            elif choice == "6":
                self.display_statistics()
            elif choice == "7":
                overdue = self.manager.get_overdue_tasks()
                print(f"\n{len(overdue)} Overdue Tasks:")
                self.list_tasks(overdue)
            elif choice == "8":
                print("Goodbye!")
                break
            else:
                print("Invalid option!")

def main():
    """Main entry point."""
    cli = TaskManagerCLI()
    cli.run()

if __name__ == "__main__":
    main()
