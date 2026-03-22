# File I/O in Ruby

## Overview

Ruby provides comprehensive file input/output capabilities through its built-in File class and IO module. This guide covers reading, writing, and manipulating files in Ruby.

## Basic File Operations

### Reading Files

#### Reading Entire File

```ruby
# Method 1: File.read
content = File.read("example.txt")
puts content

# Method 2: File.open with block
File.open("example.txt", "r") do |file|
  content = file.read
  puts content
end

# Method 3: Manual open and close
file = File.open("example.txt", "r")
begin
  content = file.read
  puts content
ensure
  file.close
end
```

#### Reading Line by Line

```ruby
# Method 1: File.readlines
lines = File.readlines("example.txt")
lines.each_with_index do |line, index|
  puts "#{index + 1}: #{line.chomp}"
end

# Method 2: File.open with each_line
File.open("example.txt", "r") do |file|
  file.each_line.with_index do |line, index|
    puts "#{index + 1}: #{line.chomp}"
  end
end

# Method 3: File.foreach
File.foreach("example.txt").with_index do |line, index|
  puts "#{index + 1}: #{line.chomp}"
end
```

#### Reading in Chunks

```ruby
# Read specific number of bytes
File.open("example.txt", "r") do |file|
  while chunk = file.read(1024)  # Read 1KB at a time
    puts "Read chunk: #{chunk.length} bytes"
  end
end

# Read until specific character
File.open("example.txt", "r") do |file|
  until file.eof?
    puts file.gets.chomp
  end
end
```

### Writing Files

#### Writing Entire Content

```ruby
# Method 1: File.write (overwrites existing file)
File.write("output.txt", "Hello, World!")

# Method 2: File.open with write mode
File.open("output.txt", "w") do |file|
  file.write("Hello, Ruby!")
end

# Method 3: Using puts
File.open("output.txt", "w") do |file|
  file.puts("Line 1")
  file.puts("Line 2")
  file.puts("Line 3")
end
```

#### Appending to Files

```ruby
# Method 1: File.write with append mode
File.write("output.txt", "New line\n", mode: "a")

# Method 2: File.open with append mode
File.open("output.txt", "a") do |file|
  file.puts("Appended line")
end

# Method 3: Using << operator
File.open("output.txt", "a") do |file|
  file << "Another appended line\n"
end
```

## File Modes

| Mode | Description | File Pointer | Creates File |
|------|-------------|--------------|--------------|
| "r"  | Read-only | Beginning | No |
| "r+" | Read-write | Beginning | No |
| "w"  | Write-only | Beginning | Yes |
| "w+" | Read-write | Beginning | Yes |
| "a"  | Write-only | End | Yes |
| "a+" | Read-write | End | Yes |
| "b"  | Binary mode | - | - |

### Binary Mode

```ruby
# Reading binary files
File.open("image.jpg", "rb") do |file|
  data = file.read
  puts "File size: #{data.length} bytes"
end

# Writing binary files
binary_data = [0x89, 0x50, 0x4E, 0x47].pack("C*")
File.open("binary.bin", "wb") do |file|
  file.write(binary_data)
end
```

## File Information and Operations

### File Properties

```ruby
filename = "example.txt"

# Check if file exists
puts "File exists: #{File.exist?(filename)}"

# Check if it's a file or directory
puts "Is file: #{File.file?(filename)}"
puts "Is directory: #{File.directory?(filename)}"

# File size
puts "File size: #{File.size(filename)} bytes"

# File permissions
puts "Readable: #{File.readable?(filename)}"
puts "Writable: #{File.writable?(filename)}"
puts "Executable: #{File.executable?(filename)}"

# File timestamps
puts "Created: #{File.ctime(filename)}"
puts "Modified: #{File.mtime(filename)}"
puts "Accessed: #{File.atime(filename)}"
```

### File Operations

```ruby
# Rename file
File.rename("old_name.txt", "new_name.txt")

# Copy file
FileUtils.cp("source.txt", "destination.txt")

# Move file
FileUtils.mv("source.txt", "new_location.txt")

# Delete file
File.delete("file_to_delete.txt")

# Create directory
Dir.mkdir("new_directory")

# Remove directory
Dir.rmdir("empty_directory")
```

## Directory Operations

### Listing Directory Contents

```ruby
# Method 1: Dir.entries
entries = Dir.entries(".")
puts entries

# Method 2: Dir.glob (with pattern)
txt_files = Dir.glob("*.txt")
puts "Text files: #{txt_files}"

# Method 3: Dir.glob with recursive search
all_ruby_files = Dir.glob("**/*.rb")
puts "All Ruby files: #{all_ruby_files}"

# Method 4: Directory iteration
Dir.each_child(".") do |filename|
  puts "Child: #{filename}"
end
```

### Working with Directory Paths

```ruby
require 'pathname'

path = Pathname.new("/home/user/documents/file.txt")

puts "Directory: #{path.dirname}"
puts "Filename: #{path.basename}"
puts "Extension: #{path.extname}"
puts "Absolute path: #{path.realpath}"

# Join paths
full_path = File.join("home", "user", "documents", "file.txt")
puts "Joined path: #{full_path}"

# Expand path
expanded_path = File.expand_path("~/documents/file.txt")
puts "Expanded path: #{expanded_path}"
```

## File Positioning

### Seeking and Telling

```ruby
File.open("example.txt", "r") do |file|
  # Get current position
  puts "Current position: #{file.tell}"
  
  # Read first 10 bytes
  first_10 = file.read(10)
  puts "First 10 bytes: #{first_10}"
  
  # Get position after reading
  puts "Position after reading: #{file.tell}"
  
  # Seek to beginning
  file.seek(0, IO::SEEK_SET)
  puts "Position after seeking to start: #{file.tell}"
  
  # Seek to end
  file.seek(0, IO::SEEK_END)
  puts "Position after seeking to end: #{file.tell}"
  
  # Seek relative to current position
  file.seek(-5, IO::SEEK_CUR)
  puts "Position after seeking back 5 bytes: #{file.tell}"
end
```

### Rewinding Files

```ruby
File.open("example.txt", "r") do |file|
  # Read some content
  content1 = file.read(5)
  puts "First read: #{content1}"
  
  # Rewind to beginning
  file.rewind
  puts "Position after rewind: #{file.tell}"
  
  # Read again
  content2 = file.read(5)
  puts "Second read: #{content2}"
end
```

## Advanced File Operations

### Temporary Files

```ruby
require 'tempfile'

# Create temporary file
temp_file = Tempfile.new('my_app')
puts "Temp file path: #{temp_file.path}"

# Write to temp file
temp_file.puts("Temporary content")
temp_file.flush

# Read from temp file
puts "Temp file content: #{File.read(temp_file.path)}"

# Temp file is automatically deleted when garbage collected
temp_file.close
temp_file.unlink

# Using block form (automatic cleanup)
Tempfile.open('my_app') do |temp|
  temp.puts("Block temporary content")
  puts "Temp file content: #{File.read(temp.path)}"
end
```

### File Locking

```ruby
# Exclusive lock (write lock)
File.open("shared_file.txt", "w") do |file|
  file.flock(File::LOCK_EX)
  file.puts("Exclusive write")
  # File is locked here
end

# Shared lock (read lock)
File.open("shared_file.txt", "r") do |file|
  file.flock(File::LOCK_SH)
  content = file.read
  puts "Read with shared lock: #{content}"
end

# Non-blocking lock
locked = File.open("shared_file.txt", "r") do |file|
  file.flock(File::LOCK_EX | File::LOCK_NB)
end

if locked
  puts "File locked successfully"
else
  puts "Could not acquire lock"
end
```

### File Monitoring

```ruby
require 'find'

# Find files matching criteria
Find.find("/path/to/search") do |path|
  if File.file?(path) && path.end_with?(".rb")
    puts "Ruby file: #{path}"
  end
end

# Watch for file changes (simplified)
def watch_file(filename, interval = 1)
  last_mtime = File.mtime(filename)
  
  loop do
    sleep(interval)
    current_mtime = File.mtime(filename)
    
    if current_mtime > last_mtime
      puts "File #{filename} has been modified!"
      last_mtime = current_mtime
    end
  end
end

# Usage (commented out for demo)
# watch_file("example.txt")
```

## CSV Files

### Reading CSV Files

```ruby
require 'csv'

# Method 1: CSV.read
data = CSV.read("data.csv")
data.each { |row| puts row.inspect }

# Method 2: CSV.foreach with headers
CSV.foreach("data.csv", headers: true) do |row|
  puts "Name: #{row['Name']}, Age: #{row['Age']}"
end

# Method 3: CSV with custom options
CSV.foreach("data.csv", headers: true, header_converters: :symbol) do |row|
  puts "Name: #{row[:name]}, Age: #{row[:age]}"
end
```

### Writing CSV Files

```ruby
require 'csv'

# Method 1: CSV.open with headers
CSV.open("output.csv", "w") do |csv|
  csv << ["Name", "Age", "City"]
  csv << ["Alice", 25, "New York"]
  csv << ["Bob", 30, "Los Angeles"]
end

# Method 2: CSV.generate
csv_string = CSV.generate do |csv|
  csv << ["Product", "Price", "Quantity"]
  csv << ["Laptop", 999.99, 5]
  csv << ["Mouse", 29.99, 20]
end

File.write("products.csv", csv_string)
```

## JSON Files

### Reading JSON Files

```ruby
require 'json'

# Read and parse JSON
json_content = File.read("data.json")
data = JSON.parse(json_content)

puts "Parsed data: #{data.inspect}"
puts "Name: #{data['name']}" if data['name']

# Parse with symbol keys
data_with_symbols = JSON.parse(json_content, symbolize_names: true)
puts "Name: #{data_with_symbols[:name]}" if data_with_symbols[:name]
```

### Writing JSON Files

```ruby
require 'json'

data = {
  name: "John Doe",
  age: 30,
  email: "john@example.com",
  hobbies: ["reading", "coding", "gaming"]
}

# Write pretty JSON
File.write("output.json", JSON.pretty_generate(data))

# Write compact JSON
File.write("compact.json", data.to_json)
```

## YAML Files

### Reading YAML Files

```ruby
require 'yaml'

# Read and parse YAML
yaml_content = File.read("config.yml")
config = YAML.load(yaml_content)

puts "Database host: #{config['database']['host']}"
puts "Database port: #{config['database']['port']}"
```

### Writing YAML Files

```ruby
require 'yaml'

config = {
  database: {
    host: "localhost",
    port: 5432,
    name: "myapp_development"
  },
  redis: {
    host: "localhost",
    port: 6379
  }
}

File.write("config.yml", config.to_yaml)
```

## Practical Examples

### Example 1: Log File Processor

```ruby
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

# Usage
# processor = LogProcessor.new("application.log")
# processor.process
```

### Example 2: Configuration Manager

```ruby
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
  
  def reload
    @config = load_config
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

# Usage
# config = ConfigManager.new("config.json")
# puts config.get("database.host")
# config.set("database.port", 5432)
```

### Example 3: File Backup Utility

```ruby
class FileBackup
  def initialize(source_dir, backup_dir)
    @source_dir = source_dir
    @backup_dir = backup_dir
  end
  
  def backup
    ensure_backup_directory
    backup_files
    puts "Backup completed successfully"
  end
  
  private
  
  def ensure_backup_directory
    Dir.mkdir(@backup_dir) unless Dir.exist?(@backup_dir)
  end
  
  def backup_files
    Find.find(@source_dir) do |source_path|
      next if File.directory?(source_path)
      
      relative_path = source_path.sub(@source_dir + "/", "")
      backup_path = File.join(@backup_dir, relative_path)
      
      # Create directory structure
      backup_dir = File.dirname(backup_path)
      FileUtils.mkdir_p(backup_dir) unless Dir.exist?(backup_dir)
      
      # Copy file if newer or doesn't exist
      if should_backup?(source_path, backup_path)
        FileUtils.cp(source_path, backup_path)
        puts "Backed up: #{relative_path}"
      end
    end
  end
  
  def should_backup?(source_path, backup_path)
    !File.exist?(backup_path) || File.mtime(source_path) > File.mtime(backup_path)
  end
end

# Usage
# backup = FileBackup.new("/path/to/source", "/path/to/backup")
# backup.backup
```

## Best Practices

### 1. Use Blocks for File Operations

```ruby
# Good - Automatic cleanup
File.open("file.txt", "r") do |file|
  content = file.read
  # Process content
end

# Avoid - Manual cleanup
file = File.open("file.txt", "r")
begin
  content = file.read
ensure
  file.close
end
```

### 2. Handle File Operations Safely

```ruby
def safe_read(filename)
  return nil unless File.exist?(filename)
  return nil unless File.readable?(filename)
  
  File.read(filename)
rescue => e
  puts "Error reading file: #{e.message}"
  nil
end
```

### 3. Use Appropriate File Modes

```ruby
# Read existing file
File.open("config.txt", "r")

# Write new file (overwrite)
File.open("output.txt", "w")

# Append to existing file
File.open("log.txt", "a")

# Read and write (preserve content)
File.open("data.txt", "r+")
```

### 4. Handle Large Files Efficiently

```ruby
# Good - Process line by line
File.foreach("large_file.txt") do |line|
  process_line(line)
end

# Avoid - Load entire file into memory
content = File.read("large_file.txt")
content.each_line { |line| process_line(line) }
```

## Practice Exercises

### Exercise 1: Text File Analyzer
Create a program that:
- Analyzes word frequency in a text file
- Counts lines, words, and characters
- Finds the longest word
- Reports reading time

### Exercise 2: CSV Data Processor
Build a CSV processor that:
- Reads CSV files with headers
- Validates data types
- Calculates statistics
- Outputs summary reports

### Exercise 3: Configuration File Manager
Implement a config manager that:
- Supports JSON and YAML formats
- Provides get/set methods
- Validates configuration values
- Handles nested configurations

### Exercise 4: Log File Monitor
Create a log monitoring tool that:
- Watches log files for changes
- Parses different log formats
- Filters by log level
- Sends alerts for errors

---

**Ready to learn about gems and package management in Ruby? Let's continue! 💎**
