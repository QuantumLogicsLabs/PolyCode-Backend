# Contact Book

A command-line contact book application that demonstrates class design, data validation, and file persistence in Ruby.

## Features

- Add new contacts
- Search contacts by name or email
- List all contacts
- Edit existing contacts
- Delete contacts
- Save contacts to JSON file
- Load contacts from file

## Concepts Demonstrated

- Class design and inheritance
- Data validation and error handling
- JSON serialization/deserialization
- Search and filtering algorithms
- File I/O operations
- Command-line interface design

## How to Run

```bash
ruby main.rb
```

## Usage Examples

```
Contact Book
============

1. Add contact
2. List contacts
3. Search contacts
4. Edit contact
5. Delete contact
6. Save contacts
7. Load contacts
8. Exit

Enter your choice (1-8): 1
Enter name: John Doe
Enter email: john@example.com
Enter phone: 123-456-7890
Added: John Doe

Enter your choice (1-8): 2
Contacts:
1. John Doe
   Email: john@example.com
   Phone: 123-456-7890
   Created: 2023-01-01 10:00:00

Enter your choice (1-8): 3
Search by (name/email): name
Enter search term: john
Found 1 contact:
- John Doe (john@example.com)
```

## Project Structure

```
contact_book/
├── main.rb              # Main application entry point
├── contact.rb           # Contact class definition
├── contact_book.rb      # ContactBook class definition
├── contact_manager.rb   # Main manager class
├── contacts.json        # Data file (created when saving)
└── README.md            # This file
```

## Code Overview

### Contact Class
Represents a single contact with:
- Name, email, phone
- Validation methods
- JSON serialization
- Search functionality

### ContactBook Class
Manages a collection of contacts with:
- CRUD operations
- Search capabilities
- File persistence
- Data validation

### ContactManager Class
Handles user interaction and:
- Command-line interface
- Input validation
- Error handling
- File operations

## Extensions to Try

1. **Address book**: Add multiple addresses per contact
2. **Groups/Categories**: Organize contacts into groups
3. **Import/Export**: Support CSV import/export
4. **Birthday tracking**: Add birthday field and reminders
5. **Notes**: Add notes field for each contact
6. **Advanced search**: Search by phone, partial matches
