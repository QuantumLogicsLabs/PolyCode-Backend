# Gems and Package Management in Ruby

## Overview

RubyGems is the package manager for Ruby. It provides a standard format for distributing Ruby programs and libraries, making it easy to manage dependencies and share code with the community.

## What is a Gem?

A gem is a packaged Ruby application or library that contains:
- Ruby code and libraries
- Documentation
- Tests
- Metadata (name, version, dependencies, etc.)
- Executable scripts (optional)

## Installing RubyGems

RubyGems comes pre-installed with Ruby since version 1.9. You can check if it's installed:

```bash
gem --version
```

If you need to install or update RubyGems:

```bash
# Update to the latest version
gem update --system

# Install a specific version
gem install rubygems-update -v 3.3.0
```

## Basic Gem Commands

### Searching for Gems

```bash
# Search for gems by name
gem search rails

# Search with detailed information
gem search rails --details

# Search remotely
gem search json --remote

# Search locally installed gems
gem search json --local
```

### Installing Gems

```bash
# Install the latest version
gem install json

# Install a specific version
gem install json -v 2.6.3

# Install without documentation (faster)
gem install json --no-document

# Install from a gem file
gem install json-2.6.3.gem

# Install with specific sources
gem install json --source https://gems.example.com
```

### Listing Gems

```bash
# List all installed gems
gem list

# List gems matching a pattern
gem list json

# List local gems only
gem list --local

# List remote gems
gem list --remote
```

### Uninstalling Gems

```bash
# Uninstall a gem
gem uninstall json

# Uninstall a specific version
gem uninstall json -v 2.6.3

# Uninstall all versions
gem uninstall json --all

# Uninstall without confirmation
gem uninstall json -x
```

### Updating Gems

```bash
# Update all gems
gem update

# Update a specific gem
gem update json

# Update RubyGems itself
gem update --system
```

### Gem Information

```bash
# Show gem details
gem specification json

# Show gem details in a specific format
gem specification json --yaml

# Show which gems a gem depends on
gem dependency json

# Show which gems depend on this gem
gem dependency json --reverse-dependencies
```

## The Gemfile and Bundler

Bundler is a dependency manager that ensures your Ruby applications have the exact gems and versions needed.

### Installing Bundler

```bash
gem install bundler
```

### Creating a Gemfile

The Gemfile specifies your application's dependencies:

```ruby
# Gemfile
source 'https://rubygems.org'

ruby '3.1.0'

# Gem with latest version
gem 'rails'

# Gem with specific version
gem 'json', '~> 2.6.0'

# Gem from Git repository
gem 'my_gem', git: 'https://github.com/user/my_gem.git'

# Gem from local path
gem 'my_gem', path: './vendor/gems/my_gem'

# Gem only for specific environments
group :development, :test do
  gem 'rspec'
  gem 'pry'
end

group :production do
  gem 'pg'
end
```

### Gem Version Operators

| Operator | Meaning | Example |
|-----------|---------|---------|
| `>= 1.0` | Greater than or equal to | `gem 'json', '>= 1.0'` |
| `<= 1.0` | Less than or equal to | `gem 'json', '<= 2.0'` |
| `~> 1.0` | Pessimistic operator | `gem 'json', '~> 2.6'` (>= 2.6, < 3.0) |
| `~> 1.0.0` | More specific | `gem 'json', '~> 2.6.0'` (>= 2.6.0, < 2.7.0) |

### Using Bundler

```bash
# Install all gems from Gemfile
bundle install

# Update gems according to Gemfile
bundle update

# Update a specific gem
bundle update json

# Check for missing gems
bundle check

# Show gem locations
bundle show

# Show gem information
bundle show json

# Execute command in bundle context
bundle exec rails server

# Clean up unused gems
bundle clean
```

## Creating Your Own Gem

### Gem Structure

```
my_awesome_gem/
├── lib/
│   └── my_awesome_gem.rb
│   └── my_awesome_gem/
│       └── version.rb
├── spec/
│   └── my_awesome_gem_spec.rb
├── Gemfile
├── Rakefile
├── my_awesome_gem.gemspec
└── README.md
```

### Basic Gem Template

```ruby
# my_awesome_gem.gemspec
require_relative 'lib/my_awesome_gem/version'

Gem::Specification.new do |spec|
  spec.name          = "my_awesome_gem"
  spec.version       = MyAwesomeGem::VERSION
  spec.authors       = ["Your Name"]
  spec.email         = ["your.email@example.com"]
  
  spec.summary       = "A short summary of your gem"
  spec.description   = "A longer description of your gem"
  spec.homepage      = "https://github.com/yourusername/my_awesome_gem"
  spec.license       = "MIT"
  
  spec.required_ruby_version = ">= 2.7.0"
  
  spec.metadata["homepage_uri"] = spec.homepage
  spec.metadata["source_code_uri"] = spec.homepage
  spec.metadata["changelog_uri"] = "#{spec.homepage}/blob/main/CHANGELOG.md"
  
  # Specify which files should be added to the gem
  spec.files = Dir.chdir(File.expand_path(__dir__)) do
    `git ls-files -z`.split("\x0").reject { |f| f.match(%r{\A(?:test|spec|features)/}) }
  end
  
  spec.bindir        = "exe"
  spec.executables   = spec.files.grep(%r{\Aexe/}) { |f| File.basename(f) }
  spec.require_paths = ["lib"]
  
  # Dependencies
  spec.add_dependency "json", "~> 2.0"
  
  spec.add_development_dependency "rspec", "~> 3.0"
  spec.add_development_dependency "rubocop", "~> 1.0"
end
```

### Main Gem File

```ruby
# lib/my_awesome_gem.rb
require_relative "my_awesome_gem/version"

module MyAwesomeGem
  class Error < StandardError; end

  def self.hello(name = "World")
    "Hello, #{name}!"
  end

  def self.calculate_sum(numbers)
    numbers.reduce(0, :+)
  end

  def self.format_json(data)
    JSON.pretty_generate(data)
  end
end
```

### Version File

```ruby
# lib/my_awesome_gem/version.rb
module MyAwesomeGem
  VERSION = "0.1.0"
end
```

### Building and Publishing

```bash
# Build the gem
gem build my_awesome_gem.gemspec

# Install locally for testing
gem install ./my_awesome_gem-0.1.0.gem

# Push to RubyGems.org (requires account)
gem push my_awesome_gem-0.1.0.gem

# Install from RubyGems
gem install my_awesome_gem
```

## Popular Ruby Gems

### Web Development

#### Ruby on Rails
```ruby
# Gemfile
gem 'rails', '~> 7.0'

# Usage
rails new my_app
cd my_app
rails server
```

#### Sinatra
```ruby
# Gemfile
gem 'sinatra'

# app.rb
require 'sinatra'

get '/' do
  'Hello, Sinatra!'
end

get '/hello/:name' do
  "Hello, #{params[:name]}!"
end
```

### Testing

#### RSpec
```ruby
# Gemfile
group :test do
  gem 'rspec-rails'
end

# spec/models/user_spec.rb
require 'rails_helper'

RSpec.describe User, type: :model do
  it "is valid with valid attributes" do
    user = User.new(name: "John", email: "john@example.com")
    expect(user).to be_valid
  end
end
```

#### Minitest
```ruby
# Gemfile
group :test do
  gem 'minitest'
end

# test/user_test.rb
require 'minitest/autorun'
require_relative '../models/user'

class UserTest < Minitest::Test
  def test_user_creation
    user = User.new(name: "John", email: "john@example.com")
    assert user.valid?
  end
end
```

### Database

#### ActiveRecord
```ruby
# Gemfile
gem 'activerecord'

# Usage
require 'active_record'

ActiveRecord::Base.establish_connection(
  adapter: 'sqlite3',
  database: 'db/development.sqlite3'
)

class User < ActiveRecord::Base
  validates :name, presence: true
  validates :email, presence: true, uniqueness: true
end

user = User.create(name: "John", email: "john@example.com")
puts user.name
```

#### Sequel
```ruby
# Gemfile
gem 'sequel'

# Usage
require 'sequel'

DB = Sequel.sqlite('db/development.sqlite3')

DB.create_table :users do
  primary_key :id
  String :name
  String :email
end

class User < Sequel::Model
end

user = User.create(name: "John", email: "john@example.com")
puts user.name
```

### HTTP Clients

#### HTTParty
```ruby
# Gemfile
gem 'httparty'

# Usage
require 'httparty'

class API
  include HTTParty
  base_uri 'https://api.example.com'
  
  def self.get_users
    get('/users')
  end
  
  def self.create_user(data)
    post('/users', body: data.to_json)
  end
end

response = API.get_users
puts response.body
```

#### Faraday
```ruby
# Gemfile
gem 'faraday'

# Usage
require 'faraday'

conn = Faraday.new(url: 'https://api.example.com') do |faraday|
  faraday.request  :json
  faraday.response :json
  faraday.adapter  Faraday.default_adapter
end

response = conn.get('/users')
puts response.body
```

### JSON Processing

#### JSON Gem
```ruby
require 'json'

data = { name: "John", age: 30, hobbies: ["reading", "coding"] }

# To JSON
json_string = data.to_json
puts json_string

# From JSON
parsed = JSON.parse(json_string)
puts parsed['name']
```

### Command Line Tools

#### Thor
```ruby
# Gemfile
gem 'thor'

# cli.rb
require 'thor'

class MyCLI < Thor
  desc "hello NAME", "Say hello to NAME"
  def hello(name)
    puts "Hello, #{name}!"
  end
  
  desc "create FILE", "Create a new file"
  option :content, default: "Hello, World!"
  def create(file)
    File.write(file, options[:content])
    puts "Created #{file}"
  end
end

MyCLI.start(ARGV)
```

#### GLI
```ruby
# Gemfile
gem 'gli'

# cli.rb
require 'gli'

class App
  extend GLI::App
  
  program_desc "A command line application"
  
  version "0.1.0"
  
  desc "Say hello"
  command :hello do |c|
    c.action do |global_options, options, args|
      name = args.first || "World"
      puts "Hello, #{name}!"
    end
  end
end

exit App.run(ARGV)
```

## Gem Security

### Verifying Gems

```bash
# Check if a gem is signed
gem verify json

# Verify all installed gems
gem verify --all
```

### Using Signed Gems

```bash
# Install security certificates
gem install --trusted-cert /path/to/cert.pem gem_name

# Configure gem sources
gem sources --add https://secure.gems.example.com
```

### Security Best Practices

1. **Review dependencies**: Regularly check your Gemfile for outdated or vulnerable gems
2. **Use specific versions**: Avoid using `>=` or latest versions in production
3. **Audit gems**: Use tools like `bundler-audit` to check for vulnerabilities
4. **Limit gem sources**: Use trusted sources only

```bash
# Install bundler-audit
gem install bundler-audit

# Check for vulnerabilities
bundle-audit

# Update vulnerability database
bundle-audit --update
```

## Gem Development Tools

### Rake for Gem Tasks

```ruby
# Rakefile
require 'bundler/gem_tasks'
require 'rspec/core/rake_task'

RSpec::Core::RakeTask.new(:spec)

task default: :spec

# Custom task
desc "Run all tests and checks"
task :full_check => [:spec, :rubocop] do
  puts "All checks passed!"
end
```

### RuboCop for Code Style

```ruby
# Gemfile
group :development do
  gem 'rubocop'
  gem 'rubocop-rspec'
end

# .rubocop.yml
AllCops:
  TargetRubyVersion: 3.1

Style/StringLiterals:
  EnforcedStyle: double_quotes

Metrics/MethodLength:
  Max: 15
```

### Rake for Testing

```ruby
# Rakefile
require 'rspec/core/rake_task'

RSpec::Core::RakeTask.new(:spec) do |t|
  t.rspec_opts = "--format documentation"
  t.pattern = "spec/**/*_spec.rb"
end

desc "Run tests with coverage"
task :coverage do
  ENV['COVERAGE'] = 'true'
  Rake::Task[:spec].invoke
end
```

## Practical Examples

### Example 1: Weather API Gem

```ruby
# weather_gem.gemspec
Gem::Specification.new do |spec|
  spec.name          = "weather_gem"
  spec.version       = "0.1.0"
  spec.authors       = ["Weather Developer"]
  spec.summary       = "Simple weather API wrapper"
  
  spec.files         = Dir["lib/**/*.rb"]
  spec.require_paths = ["lib"]
  
  spec.add_dependency "httparty", "~> 0.20"
  spec.add_dependency "json", "~> 2.0"
end

# lib/weather_gem.rb
require 'httparty'
require 'json'

module WeatherGem
  class Client
    include HTTParty
    base_uri 'https://api.openweathermap.org/data/2.5'
    
    def initialize(api_key)
      @api_key = api_key
    end
    
    def current_weather(city)
      response = self.class.get('/weather', query: {
        q: city,
        appid: @api_key,
        units: 'metric'
      })
      
      return nil unless response.success?
      
      {
        city: response['name'],
        temperature: response['main']['temp'],
        description: response['weather'][0]['description']
      }
    end
    
    def forecast(city, days = 5)
      response = self.class.get('/forecast', query: {
        q: city,
        appid: @api_key,
        units: 'metric',
        cnt: days * 8  # 8 forecasts per day (3-hour intervals)
      })
      
      return nil unless response.success?
      
      response['list'].map do |item|
        {
          datetime: Time.at(item['dt']),
          temperature: item['main']['temp'],
          description: item['weather'][0]['description']
        }
      end
    end
  end
end
```

### Example 2: Configuration Management Gem

```ruby
# config_manager.gemspec
Gem::Specification.new do |spec|
  spec.name          = "config_manager"
  spec.version       = "0.1.0"
  spec.authors       = ["Config Developer"]
  spec.summary       = "Simple configuration management"
  
  spec.add_dependency "deep_merge", "~> 1.2"
end

# lib/config_manager.rb
require 'deep_merge'

module ConfigManager
  class Configuration
    def initialize
      @config = {}
      load_default_config
    end
    
    def load_from_file(file_path)
      case File.extname(file_path).downcase
      when '.json'
        load_json(file_path)
      when '.yml', '.yaml'
        load_yaml(file_path)
      else
        raise "Unsupported file format: #{File.extname(file_path)}"
      end
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
    end
    
    def merge!(other_config)
      @config.deep_merge!(other_config)
    end
    
    def to_h
      @config
    end
    
    private
    
    def load_default_config
      @config = {
        app: {
          name: 'MyApp',
          version: '1.0.0',
          debug: false
        },
        database: {
          host: 'localhost',
          port: 5432
        }
      }
    end
    
    def load_json(file_path)
      json_config = JSON.parse(File.read(file_path))
      @config.deep_merge!(json_config)
    end
    
    def load_yaml(file_path)
      yaml_config = YAML.load_file(file_path)
      @config.deep_merge!(yaml_config)
    end
  end
end
```

## Best Practices

### 1. Version Management

```ruby
# Good - Use pessimistic operator
gem 'rails', '~> 7.0.0'

# Avoid - Too loose
gem 'rails', '>= 7.0'

# Good - Specific version for critical dependencies
gem 'pg', '1.2.3'

# Good - Use environment-specific gems
group :development do
  gem 'pry'
  gem 'rubocop'
end
```

### 2. Gem Security

```ruby
# Regular security audits
bundle-audit

# Keep gems updated
bundle update

# Review gem dependencies
bundle list
```

### 3. Performance

```ruby
# Use specific versions to avoid unnecessary updates
gem 'json', '~> 2.6.0'

# Group development gems separately
group :development, :test do
  gem 'rspec'
  gem 'pry'
end
```

## Practice Exercises

### Exercise 1: Create a Simple Gem
Build a gem that:
- Provides basic string manipulation utilities
- Includes proper documentation
- Has tests
- Can be published to RubyGems

### Exercise 2: API Wrapper Gem
Create a gem that:
- Wraps a public API (like GitHub, Twitter, etc.)
- Handles authentication
- Provides error handling
- Includes comprehensive tests

### Exercise 3: Configuration Gem
Develop a gem that:
- Manages application configuration
- Supports multiple formats (JSON, YAML)
- Provides validation
- Includes environment-specific configs

### Exercise 4: CLI Tool Gem
Build a command-line tool gem that:
- Uses Thor or GLI
- Provides multiple commands
- Includes help documentation
- Has configuration options

---

**Congratulations! You've completed the comprehensive Ruby guide! 🎉**
