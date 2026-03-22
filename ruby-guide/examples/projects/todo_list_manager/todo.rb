# Todo class - Represents a single todo item

class Todo
  attr_reader :id, :description, :created_at
  attr_accessor :completed

  def initialize(description, id = nil)
    @id = id || generate_id
    @description = description
    @completed = false
    @created_at = Time.now
  end

  def complete!
    @completed = true
  end

  def incomplete!
    @completed = false
  end

  def completed?
    @completed
  end

  def toggle_completion
    @completed = !@completed
  end

  def to_s
    status = @completed ? "✓" : "○"
    "#{status} #{@description}"
  end

  def to_h
    {
      id: @id,
      description: @description,
      completed: @completed,
      created_at: @created_at.iso8601
    }
  end

  def self.from_hash(hash)
    todo = new(hash[:description], hash[:id])
    todo.completed = hash[:completed] if hash.key?(:completed)
    todo
  end

  private

  def generate_id
    Time.now.to_f.to_s.gsub('.', '')
  end
end
