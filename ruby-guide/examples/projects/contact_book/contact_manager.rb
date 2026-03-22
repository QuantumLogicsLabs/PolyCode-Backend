# ContactManager class - Handles user interaction and application flow

require_relative 'contact_book'

class ContactManager
  def initialize
    @contact_book = ContactBook.new
    @data_file = 'contacts.json'
  end

  def start
    load_contacts if File.exist?(@data_file)
    
    loop do
      display_menu
      choice = get_user_choice
      
      case choice
      when 1
        add_contact
      when 2
        list_contacts
      when 3
        search_contacts
      when 4
        edit_contact
      when 5
        delete_contact
      when 6
        save_contacts
      when 7
        load_contacts
      when 8
        save_contacts
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
    puts "Contact Book"
    puts "=" * 50
    puts "1. Add contact"
    puts "2. List contacts"
    puts "3. Search contacts"
    puts "4. Edit contact"
    puts "5. Delete contact"
    puts "6. Save contacts"
    puts "7. Load contacts"
    puts "8. Exit"
    puts "=" * 50
  end

  def get_user_choice
    print "\nEnter your choice (1-8): "
    gets.chomp.to_i
  end

  def add_contact
    puts "\nAdd New Contact"
    puts "-" * 20
    
    name = get_valid_input("Enter name: ", "Name is required")
    email = get_valid_input("Enter email: ", "Valid email is required", method(:validate_email))
    phone = get_valid_input("Enter phone: ", "Valid phone is required", method(:validate_phone))
    
    begin
      contact = @contact_book.add_contact(name, email, phone)
      puts "Added: #{contact.name}"
    rescue ArgumentError => e
      puts "Error: #{e.message}"
    end
  end

  def list_contacts
    @contact_book.display_contacts
  end

  def search_contacts
    puts "\nSearch Contacts"
    puts "-" * 20
    
    field = get_search_field
    term = get_valid_input("Enter search term: ", "Search term is required")
    
    @contact_book.display_search_results(term, field)
  end

  def edit_contact
    return if @contact_book.empty?
    
    puts "\nEdit Contact"
    puts "-" * 20
    
    @contact_book.display_contacts
    print "Enter contact number to edit (or 0 to cancel): "
    number = gets.chomp.to_i
    
    return if number == 0
    
    if number > 0 && number <= @contact_book.count
      contact = @contact_book.all_contacts[number - 1]
      edit_contact_details(contact)
    else
      puts "Invalid contact number."
    end
  end

  def delete_contact
    return if @contact_book.empty?
    
    puts "\nDelete Contact"
    puts "-" * 20
    
    @contact_book.display_contacts
    print "Enter contact number to delete (or 0 to cancel): "
    number = gets.chomp.to_i
    
    return if number == 0
    
    if number > 0 && number <= @contact_book.count
      contact = @contact_book.all_contacts[number - 1]
      print "Are you sure you want to delete #{contact.name}? (y/N): "
      confirm = gets.chomp.downcase
      
      if confirm == 'y'
        @contact_book.remove_contact(contact.id)
        puts "Deleted: #{contact.name}"
      else
        puts "Deletion cancelled."
      end
    else
      puts "Invalid contact number."
    end
  end

  def save_contacts
    begin
      @contact_book.save_to_file(@data_file)
      puts "Contacts saved to #{@data_file}"
    rescue => e
      puts "Error saving contacts: #{e.message}"
    end
  end

  def load_contacts
    begin
      if @contact_book.load_from_file(@data_file)
        puts "Contacts loaded from #{@data_file}"
        puts "Loaded #{@contact_book.count} contacts"
      else
        puts "Failed to load contacts from #{@data_file}"
      end
    rescue => e
      puts "Error loading contacts: #{e.message}"
    end
  end

  def get_valid_input(prompt, error_message, validator = nil)
    loop do
      print prompt
      input = gets.chomp.strip
      
      if input.empty?
        puts error_message
        next
      end
      
      if validator && !validator.call(input)
        puts error_message
        next
      end
      
      return input
    end
  end

  def validate_email(email)
    email_pattern = /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i
    email.match?(email_pattern)
  end

  def validate_phone(phone)
    # Remove non-digit characters for validation
    digits = phone.gsub(/\D/, '')
    digits.length >= 10
  end

  def get_search_field
    puts "Search by:"
    puts "1. Name"
    puts "2. Email"
    puts "3. Phone"
    puts "4. All fields"
    
    loop do
      print "Enter choice (1-4): "
      choice = gets.chomp.to_i
      
      case choice
      when 1
        return :name
      when 2
        return :email
      when 3
        return :phone
      when 4
        return :all
      else
        puts "Invalid choice. Please try again."
      end
    end
  end

  def edit_contact_details(contact)
    puts "\nEditing: #{contact.name}"
    puts "Leave blank to keep current value"
    
    name = get_edit_input("Name (#{contact.name}): ")
    email = get_edit_input("Email (#{contact.email}): ", method(:validate_email))
    phone = get_edit_input("Phone (#{contact.phone}): ", method(:validate_phone))
    
    begin
      updated_contact = @contact_book.update_contact(
        contact.id,
        name: name.empty? ? nil : name,
        email: email.empty? ? nil : email,
        phone: phone.empty? ? nil : phone
      )
      
      if updated_contact
        puts "Contact updated successfully."
      else
        puts "No changes made."
      end
    rescue ArgumentError => e
      puts "Error: #{e.message}"
    end
  end

  def get_edit_input(prompt, validator = nil)
    loop do
      print prompt
      input = gets.chomp.strip
      
      return input if input.empty?  # Keep current value
      
      if validator && !validator.call(input)
        puts "Invalid input. Please try again."
        next
      end
      
      return input
    end
  end
end
