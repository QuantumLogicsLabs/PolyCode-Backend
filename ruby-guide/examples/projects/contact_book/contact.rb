# Contact class - Represents a single contact

class Contact
  attr_reader :id, :name, :email, :phone, :created_at
  attr_writer :name, :email, :phone

  def initialize(name, email, phone, id = nil)
    @id = id || generate_id
    @name = name
    @email = email
    @phone = phone
    @created_at = Time.now
  end

  def valid?
    validate_name && validate_email && validate_phone
  end

  def matches_search?(term, field = :all)
    term = term.downcase
    
    case field
    when :name
      @name.downcase.include?(term)
    when :email
      @email.downcase.include?(term)
    when :phone
      @phone.include?(term)
    when :all
      @name.downcase.include?(term) ||
      @email.downcase.include?(term) ||
      @phone.include?(term)
    else
      false
    end
  end

  def update_attributes(name: nil, email: nil, phone: nil)
    @name = name if name
    @email = email if email
    @phone = phone if phone
  end

  def to_s
    "#{@name} (#{@email})"
  end

  def detailed_info
    <<~CONTACT
      #{@name}
      Email: #{@email}
      Phone: #{@phone}
      Created: #{@created_at.strftime('%Y-%m-%d %H:%M:%S')}
    CONTACT
  end

  def to_h
    {
      id: @id,
      name: @name,
      email: @email,
      phone: @phone,
      created_at: @created_at.iso8601
    }
  end

  def to_json(*args)
    to_h.to_json(*args)
  end

  def self.from_hash(hash)
    contact = new(
      hash[:name],
      hash[:email],
      hash[:phone],
      hash[:id]
    )
    contact
  end

  def ==(other)
    return false unless other.is_a?(Contact)
    @id == other.id
  end

  def hash
    @id.hash
  end

  private

  def validate_name
    return false if @name.nil? || @name.strip.empty?
    return false if @name.length < 2 || @name.length > 50
    true
  end

  def validate_email
    return false if @email.nil? || @email.strip.empty?
    email_pattern = /\A[\w+\-.]+@[a-z\d\-]+(\.[a-z\d\-]+)*\.[a-z]+\z/i
    @email.match?(email_pattern)
  end

  def validate_phone
    return false if @phone.nil? || @phone.strip.empty?
    # Accept various phone formats
    phone_pattern = /\A[\d\s\-\(\)]+\z/
    @phone.gsub(/\D, '').length >= 10 && @phone.match?(phone_pattern)
  end

  def generate_id
    Time.now.to_f.to_s.gsub('.', '')
  end
end
