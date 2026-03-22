# File I/O Examples
# Demonstrating file operations, directory handling, and data formats in Ruby

puts "=== BASIC FILE READING ==="

# Create a sample file for demonstration
File.write("sample.txt", "Hello, Ruby!\nThis is a sample file.\nIt has multiple lines.")

# Method 1: File.read
puts "Using File.read:"
content = File.read("sample.txt")
puts content

puts "\nUsing File.open with block:"
File.open("sample.txt", "r") do |file|
  puts file.read
end

puts "\n=== READING LINE BY LINE ==="

puts "Using File.readlines:"
lines = File.readlines("sample.txt")
lines.each_with_index do |line, index|
  puts "#{index + 1}: #{line.chomp}"
end

puts "\nUsing each_line:"
File.open("sample.txt", "r") do |file|
  file.each_line.with_index do |line, index|
    puts "#{index + 1}: #{line.chomp}"
  end
end

puts "\n=== FILE WRITING ==="

# Method 1: File.write (overwrites)
File.write("output.txt", "Hello from File.write!")
puts "Wrote to output.txt using File.write"

# Method 2: File.open with block
File.open("output.txt", "w") do |file|
  file.puts("Line 1 from block")
  file.puts("Line 2 from block")
  file.puts("Line 3 from block")
end
puts "Wrote to output.txt using File.open"

puts "\n=== APPENDING TO FILES ==="

File.write("output.txt", "\nAppended line using File.write", mode: "a")

File.open("output.txt", "a") do |file|
  file.puts("Appended line using File.open")
end

puts "Appended lines to output.txt"
puts "Current content:"
puts File.read("output.txt")

puts "\n=== FILE INFORMATION ==="

filename = "sample.txt"
puts "File exists: #{File.exist?(filename)}"
puts "Is file: #{File.file?(filename)}"
puts "File size: #{File.size(filename)} bytes"
puts "Readable: #{File.readable?(filename)}"
puts "Writable: #{File.writable?(filename)}"
puts "Modified: #{File.mtime(filename)}"
puts "Created: #{File.ctime(filename)}"

puts "\n=== DIRECTORY OPERATIONS ==="

# Create a directory structure
Dir.mkdir("test_dir") unless Dir.exist?("test_dir")
File.write("test_dir/file1.txt", "Content of file 1")
File.write("test_dir/file2.txt", "Content of file 2")

puts "Directory entries:"
entries = Dir.entries("test_dir")
entries.each { |entry| puts "  #{entry}" }

puts "\nUsing Dir.glob:"
txt_files = Dir.glob("test_dir/*.txt")
txt_files.each { |file| puts "  #{file}" }

puts "\n=== PATH OPERATIONS ==="

require 'pathname'

path = Pathname.new("test_dir/file1.txt")
puts "Directory: #{path.dirname}"
puts "Filename: #{path.basename}"
puts "Extension: #{path.extname}"

puts "\nJoined path: #{File.join("test_dir", "subdir", "file.txt")}"
puts "Expanded path: #{File.expand_path("~/test_file.txt")}"

puts "\n=== FILE POSITIONING ==="

File.open("sample.txt", "r") do |file|
  puts "Initial position: #{file.tell}"
  
  first_5 = file.read(5)
  puts "Read 5 bytes: '#{first_5}'"
  puts "Position after reading: #{file.tell}"
  
  file.seek(0, IO::SEEK_SET)
  puts "Position after seeking to start: #{file.tell}"
  
  file.seek(0, IO::SEEK_END)
  puts "Position after seeking to end: #{file.tell}"
  
  file.rewind
  puts "Position after rewind: #{file.tell}"
end

puts "\n=== CSV FILES ==="

require 'csv'

# Create sample CSV data
csv_data = [
  ["Name", "Age", "City"],
  ["Alice", 25, "New York"],
  ["Bob", 30, "Los Angeles"],
  ["Charlie", 35, "Chicago"]
]

# Write CSV file
CSV.open("people.csv", "w") do |csv|
  csv_data.each { |row| csv << row }
end

puts "Wrote CSV file"

# Read CSV file
puts "\nReading CSV:"
CSV.foreach("people.csv", headers: true) do |row|
  puts "#{row['Name']} is #{row['Age']} years old and lives in #{row['City']}"
end

puts "\nReading with symbol headers:"
CSV.foreach("people.csv", headers: true, header_converters: :symbol) do |row|
  puts "#{row[:name]} - #{row[:age]} - #{row[:city]}"
end

puts "\n=== JSON FILES ==="

require 'json'

# Create sample data
data = {
  name: "John Doe",
  age: 30,
  email: "john@example.com",
  hobbies: ["reading", "coding", "gaming"],
  address: {
    street: "123 Main St",
    city: "Anytown",
    country: "USA"
  }
}

# Write JSON file
File.write("data.json", JSON.pretty_generate(data))
puts "Wrote JSON file"

# Read JSON file
puts "\nReading JSON:"
json_content = File.read("data.json")
parsed_data = JSON.parse(json_content)
puts "Name: #{parsed_data['name']}"
puts "Hobbies: #{parsed_data['hobbies'].join(', ')}"

# Parse with symbol keys
symbol_data = JSON.parse(json_content, symbolize_names: true)
puts "City: #{symbol_data[:address][:city]}"

puts "\n=== YAML FILES ==="

require 'yaml'

# Create sample YAML data
config = {
  database: {
    host: "localhost",
    port: 5432,
    name: "myapp_development",
    username: "admin",
    password: "secret"
  },
  redis: {
    host: "localhost",
    port: 6379
  },
  logging: {
    level: "info",
    file: "log/application.log"
  }
}

# Write YAML file
File.write("config.yml", config.to_yaml)
puts "Wrote YAML file"

# Read YAML file
puts "\nReading YAML:"
yaml_content = File.read("config.yml")
parsed_config = YAML.load(yaml_content)
puts "Database host: #{parsed_config['database']['host']}"
puts "Database port: #{parsed_config['database']['port']}"
puts "Redis port: #{parsed_config['redis']['port']}"

puts "\n=== BINARY FILES ==="

# Create binary data
binary_data = [0x89, 0x50, 0x4E, 0x47, 0x0D, 0x0A, 0x1A, 0x0A].pack("C*")

# Write binary file
File.open("binary.bin", "wb") do |file|
  file.write(binary_data)
end

puts "Wrote binary file"

# Read binary file
File.open("binary.bin", "rb") do |file|
  data = file.read
  puts "Binary data size: #{data.length} bytes"
  puts "First 4 bytes as hex: #{data[0..3].unpack('H*').first}"
end

puts "\n=== TEMPORARY FILES ==="

require 'tempfile'

# Create temporary file
temp_file = Tempfile.new('ruby_example')
puts "Temp file path: #{temp_file.path}"

# Write to temp file
temp_file.puts("This is temporary content")
temp_file.puts("It will be deleted automatically")
temp_file.flush

# Read from temp file
puts "Temp file content:"
puts File.read(temp_file.path)

# Close and unlink
temp_file.close
temp_file.unlink

# Using block form (automatic cleanup)
puts "\nUsing block form:"
Tempfile.open('block_example') do |temp|
  temp.puts("Block temporary content")
  puts "Content: #{File.read(temp.path)}"
end

puts "\n=== FILE LOCKING ==="

# Create a shared file for locking demo
File.write("shared.txt", "Initial content")

puts "File locking demonstration:"

# Exclusive lock example
Thread.new do
  File.open("shared.txt", "w") do |file|
    puts "Thread 1: Acquiring exclusive lock..."
    file.flock(File::LOCK_EX)
    puts "Thread 1: Lock acquired"
    sleep(2)
    file.puts("Written by thread 1")
    puts "Thread 1: Releasing lock"
  end
end

# Shared lock example
Thread.new do
  sleep(0.5)  # Let first thread acquire lock first
  File.open("shared.txt", "r") do |file|
    puts "Thread 2: Attempting shared lock..."
    file.flock(File::LOCK_SH)
    puts "Thread 2: Shared lock acquired"
    content = file.read
    puts "Thread 2: Read content: #{content.chomp}"
    puts "Thread 2: Releasing lock"
  end
end

sleep(3)  # Wait for threads to complete

puts "\n=== PRACTICAL EXAMPLE: LOG PROCESSOR ==="

class LogProcessor
  def initialize(filename)
    @filename = filename
  end
  
  def process
    logs = []
    
    File.foreach(@filename) do |line|
      log_entry = parse_log_line(line.chomp)
      logs << log_entry if log_entry
    end
    
    analyze_logs(logs)
  end
  
  private
  
  def parse_log_line(line)
    # Parse log format: [TIMESTAMP] [LEVEL] MESSAGE
    match = line.match(/^\[([^\]]+)\] \[([^\]]+)\] (.+)$/)
    return nil unless match
    
    {
      timestamp: Time.parse(match[1]),
      level: match[2],
      message: match[3]
    }
  end
  
  def analyze_logs(logs)
    levels = logs.group_by { |log| log[:level] }
    
    puts "Log Analysis:"
    levels.each do |level, entries|
      puts "#{level}: #{entries.length} entries"
    end
    
    # Find errors
    errors = logs.select { |log| log[:level] == "ERROR" }
    if errors.any?
      puts "\nErrors found:"
      errors.each { |error| puts "  #{error[:timestamp]}: #{error[:message]}" }
    end
  end
end

# Create sample log file
log_content = <<~LOG
  [2023-01-01 10:00:00] [INFO] Application started
  [2023-01-01 10:01:00] [DEBUG] User login attempt
  [2023-01-01 10:02:00] [INFO] User logged in successfully
  [2023-01-01 10:03:00] [ERROR] Database connection failed
  [2023-01-01 10:04:00] [ERROR] Unable to process request
  [2023-01-01 10:05:00] [INFO] Retrying database connection
  [2023-01-01 10:06:00] [INFO] Database connection restored
LOG

File.write("application.log", log_content)

# Process log file
puts "Processing log file:"
processor = LogProcessor.new("application.log")
processor.process

puts "\n=== PRACTICAL EXAMPLE: CONFIG MANAGER ==="

class ConfigManager
  def initialize(config_file)
    @config_file = config_file
    @config = load_config
  end
  
  def get(key, default = nil)
    keys = key.split('.')
    value = @config
    
    keys.each do |k|
      return default unless value.is_a?(Hash) && value.key?(k)
      value = value[k]
    end
    
    value
  end
  
  def set(key, value)
    keys = key.split('.')
    last_key = keys.pop
    target = @config
    
    keys.each do |k|
      target[k] = {} unless target[k].is_a?(Hash)
      target = target[k]
    end
    
    target[last_key] = value
    save_config
  end
  
  private
  
  def load_config
    return {} unless File.exist?(@config_file)
    
    case File.extname(@config_file).downcase
    when '.json'
      JSON.parse(File.read(@config_file))
    when '.yml', '.yaml'
      YAML.load_file(@config_file)
    else
      {}
    end
  end
  
  def save_config
    case File.extname(@config_file).downcase
    when '.json'
      File.write(@config_file, JSON.pretty_generate(@config))
    when '.yml', '.yaml'
      File.write(@config_file, @config.to_yaml)
    end
  end
end

# Create sample config
sample_config = {
  app: {
    name: "MyApp",
    version: "1.0.0",
    debug: true
  },
  database: {
    host: "localhost",
    port: 5432
  }
}

File.write("app_config.json", JSON.pretty_generate(sample_config))

# Use config manager
config = ConfigManager.new("app_config.json")
puts "App name: #{config.get('app.name')}"
puts "Database host: #{config.get('database.host')}"

config.set('app.version', '1.1.0')
puts "Updated version: #{config.get('app.version')}"

puts "\n=== CLEANUP ==="

# Clean up demo files
files_to_remove = [
  "sample.txt", "output.txt", "people.csv", "data.json", 
  "config.yml", "binary.bin", "shared.txt", "application.log", 
  "app_config.json"
]

files_to_remove.each do |file|
  File.delete(file) if File.exist?(file)
end

# Remove directory
Dir.rmdir("test_dir") if Dir.exist?("test_dir")

puts "Cleaned up demo files"
