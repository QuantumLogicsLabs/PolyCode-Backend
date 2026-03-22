# ContactBook class - Manages a collection of contacts

require 'json'
require_relative 'contact'

class ContactBook
  attr_reader :contacts

  def initialize
    @contacts = []
  end

  def add_contact(name, email, phone)
    contact = Contact.new(name, email, phone)
    
    if contact.valid?
      # Check for duplicate email
      if find_by_email(email)
        raise ArgumentError, "A contact with this email already exists"
      end
      
      @contacts << contact
      contact
    else
      raise ArgumentError, "Invalid contact information"
    end
  end

  def remove_contact(id)
    contact = find_by_id(id)
    return nil unless contact
    
    @contacts.delete(contact)
    contact
  end

  def find_by_id(id)
    @contacts.find { |contact| contact.id == id }
  end

  def find_by_email(email)
    @contacts.find { |contact| contact.email.downcase == email.downcase }
  end

  def search_contacts(term, field = :all)
    @contacts.select { |contact| contact.matches_search?(term, field) }
  end

  def update_contact(id, name: nil, email: nil, phone: nil)
    contact = find_by_id(id)
    return nil unless contact
    
    # Check for email conflict if email is being updated
    if email && email != contact.email
      existing = find_by_email(email)
      raise ArgumentError, "Email already in use" if existing && existing.id != id
    end
    
    contact.update_attributes(name: name, email: email, phone: phone)
    
    # Validate updated contact
    unless contact.valid?
      # Revert changes if invalid
      raise ArgumentError, "Invalid contact information"
    end
    
    contact
  end

  def all_contacts
    @contacts.dup
  end

  def count
    @contacts.length
  end

  def empty?
    @contacts.empty?
  end

  def clear_all
    @contacts.clear
  end

  def sort_by_name
    @contacts.sort_by(&:name)
  end

  def sort_by_email
    @contacts.sort_by(&:email)
  end

  def sort_by_created_at
    @contacts.sort_by(&:created_at)
  end

  def to_a
    @contacts.map(&:to_h)
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
      @contacts = data.map { |contact_data| Contact.from_hash(contact_data) }
      true
    rescue JSON::ParserError => e
      puts "Error parsing file: #{e.message}"
      false
    rescue => e
      puts "Error loading file: #{e.message}"
      false
    end
  end

  def display_contacts(contacts = @contacts)
    if contacts.empty?
      puts "No contacts found."
      return
    end

    puts "\nContacts:"
    puts "=" * 60
    
    contacts.each_with_index do |contact, index|
      puts "#{index + 1}. #{contact.name}"
      puts "   Email: #{contact.email}"
      puts "   Phone: #{contact.phone}"
      puts "   Created: #{contact.created_at.strftime('%Y-%m-%d %H:%M')}"
      puts
    end
    
    puts "Total: #{contacts.length} contacts"
  end

  def display_search_results(term, field = :all)
    results = search_contacts(term, field)
    
    if results.empty?
      puts "No contacts found matching '#{term}'"
      return
    end

    puts "\nSearch Results for '#{term}' (#{field}):"
    puts "=" * 50
    results.each_with_index do |contact, index|
      puts "#{index + 1}. #{contact.name} (#{contact.email})"
    end
    puts "\nFound #{results.length} contact(s)"
  end
end
