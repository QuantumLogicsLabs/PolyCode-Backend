# Ruby Gems and Packages

## Introduction to RubyGems

### What is RubyGems?
```ruby
# RubyGems is the package manager for Ruby
# It provides a standard format for distributing Ruby programs and libraries

# Check if RubyGems is installed
require 'rubygems'
puts "RubyGems version: #{Gem::VERSION}"

# List all installed gems
puts "Installed gems:"
Gem::Specification.each do |spec|
  puts "#{spec.name} (#{spec.version})"
end

# Check gem installation directory
puts "Gem installation paths:"
Gem.path.each { |path| puts path }
```

### Gem Structure
```
my_gem/
├── lib/
│   └── my_gem.rb
├── test/
│   └── test_my_gem.rb
├── spec/
│   └── my_gem_spec.rb
├── bin/
│   └── my_gem
├── README.md
├── LICENSE
├── my_gem.gemspec
└── Gemfile
```

## Creating a Gem

### Basic Gem Structure
```ruby
# my_gem.gemspec
require_relative 'lib/my_gem/version'

Gem::Specification.new do |spec|
  spec.name          = "my_gem"
  spec.version       = MyGem::VERSION
  spec.authors        = ["Your Name"]
  spec.email          = ["your.email@example.com"]
  spec.summary        = "A short summary of my_gem"
  spec.description    = "A longer description of my_gem"
  spec.homepage       = "https://github.com/yourname/my_gem"
  spec.license        = "MIT"
  spec.required_ruby_version = ">= 2.5.0"
  
  spec.files          = Dir["README.md", "LICENSE", "lib/**/*.rb"]
  spec.bindir         = "exe"
  spec.executables    = ["my_gem"]
  spec.require_paths  = ["lib"]
  
  spec.add_dependency "json", "~> 2.0"
  spec.add_development_dependency "rspec", "~> 3.0"
  spec.add_development_dependency "rubocop", "~> 1.0"
end
```

### Gem Version Management
```ruby
# lib/my_gem/version.rb
module MyGem
  VERSION = "1.0.0"
end

# lib/my_gem.rb
require "my_gem/version"
require "my_gem/core"

module MyGem
  def self.hello
    "Hello from MyGem version #{VERSION}"
  end
end

# lib/my_gem/core.rb
module MyGem
  class Core
    def initialize(name)
      @name = name
    end
    
    def greet
      "Hello, #{@name}!"
    end
  end
end
```

### Gem Executables
```ruby
# bin/my_gem (executable)
#!/usr/bin/env ruby

require "my_gem"

case ARGV[0]
when "hello"
  puts MyGem.hello
when "greet"
  name = ARGV[1] || "World"
  core = MyGem::Core.new(name)
  puts core.greet
else
  puts "Usage: my_gem [hello|greet] [name]"
end
```

## Gem Development

### Gemfile and Bundler
```ruby
# Gemfile
source 'https://rubygems.org'

gem 'rails', '~> 6.0'
gem 'pg', '~> 1.0'
gem 'devise', '~> 4.0'

group :development do
  gem 'rubocop', '~> 1.0'
  gem 'pry', '~> 0.13'
end

group :test do
  gem 'rspec', '~> 3.0'
  gem 'factory_bot', '~> 6.0'
end

# gemspec for local development
gemspec path: '.'
```

### Building and Publishing Gems
```ruby
# Build the gem
# Command: gem build my_gem.gemspec

# Install locally for testing
# Command: gem install ./my_gem-1.0.0.gem

# Push to RubyGems.org
# Command: gem push my_gem-1.0.0.gem

# Uninstall
# Command: gem uninstall my_gem

# List installed versions
# Command: gem list my_gem

# Install specific version
# Command: gem install my_gem -v 1.0.0
```

### Gem Development Workflow
```ruby
# Rakefile for gem development
require 'bundler/gem_tasks'
require 'rspec/core/rake_task'

RSpec::Core::RakeTask.new(:spec)

task default: :spec

# Custom tasks
desc "Install gem locally"
task :install do
  sh "gem build my_gem.gemspec"
  sh "gem install ./my_gem-#{MyGem::VERSION}.gem"
end

desc "Push gem to RubyGems"
task :push do
  sh "gem build my_gem.gemspec"
  sh "gem push ./my_gem-#{MyGem::VERSION}.gem"
end
```

## Popular Ruby Gems

### Web Development Gems
```ruby
# Gemfile for web development
source 'https://rubygems.org'

# Web framework
gem 'rails', '~> 6.0'
gem 'sinatra', '~> 2.0'

# Database
gem 'activerecord', '~> 6.0'
gem 'pg', '~> 1.0'
gem 'mysql2', '~> 0.5'
gem 'sqlite3', '~> 1.4'

# JSON handling
gem 'json', '~> 2.0'
gem 'oj', '~> 3.0'  # Faster JSON parser

# HTTP clients
gem 'httparty', '~> 0.18'
gem 'faraday', '~> 1.0'
gem 'rest-client', '~> 2.0'

# Background jobs
gem 'sidekiq', '~> 6.0'
gem 'resque', '~> 2.0'

# Authentication
gem 'devise', '~> 4.0'
gem 'sorcery', '~> 0.15'

# API documentation
gem 'grape', '~> 1.0'
gem 'rswag', '~> 2.0'
```

### Testing Gems
```ruby
# Gemfile for testing
group :development, :test do
  # Testing framework
  gem 'rspec', '~> 3.0'
  gem 'minitest', '~> 5.0'
  
  # Test doubles
  gem 'mocha', '~> 1.0'
  gem 'webmock', '~> 3.0'
  gem 'vcr', '~> 6.0'
  
  # Test data
  gem 'factory_bot', '~> 6.0'
  gem 'faker', '~> 2.0'
  gem 'database_cleaner', '~> 1.0'
  
  # Coverage
  gem 'simplecov', '~> 0.16'
  gem 'codecov', '~> 0.1'
  
  # Performance testing
  gem 'benchmark-ips', '~> 2.0'
  gem 'memory_profiler', '~> 0.9'
end

# RSpec configuration
# spec/spec_helper.rb
require 'simplecov'
SimpleCov.start

RSpec.configure do |config|
  config.include FactoryBot::Syntax::Methods
  
  config.before(:suite) do
    FactoryBot.find_definitions
  end
end
```

### Development Tools
```ruby
# Gemfile for development tools
group :development do
  # Code quality
  gem 'rubocop', '~> 1.0'
  gem 'rubocop-rspec', '~> 2.0'
  gem 'rubocop-performance', '~> 1.0'
  
  # Debugging
  gem 'pry', '~> 0.13'
  gem 'pry-byebug', '~> 3.0'
  gem 'pry-stack_explorer', '~> 0.4'
  
  # Documentation
  gem 'yard', '~> 0.9'
  gem 'redcarpet', '~> 3.0'
  
  # Profiling
  gem 'ruby-prof', '~> 1.0'
  gem 'stackprof', '~> 0.2'
  
  # Security
  gem 'brakeman', '~> 4.0'
  gem 'bundler-audit', '~> 0.6'
end
```

## Gem Dependencies

### Dependency Management
```ruby
# Gemfile with dependency management
source 'https://rubygems.org'

# Specify exact version
gem 'rails', '6.0.3.4'

# Specify version range
gem 'devise', '~> 4.7'  # >= 4.7.0, < 4.8.0

# Specify minimum version
gem 'pg', '>= 1.0'

# Specify version with pessimistic operator
gem 'json', '~> 2.3'  # >= 2.3.0, < 3.0.0

# Git dependencies
gem 'custom_gem', git: 'https://github.com/user/custom_gem.git', branch: 'main'

# Path dependencies (local development)
gem 'local_gem', path: '../local_gem'

# Platform-specific dependencies
gem 'windows-specific', platform: :mswin
gem 'mac-specific', platform: :darwin
gem 'linux-specific', platform: :linux

# Group dependencies
group :development do
  gem 'pry'
  gem 'rubocop'
end

group :test do
  gem 'rspec'
  gem 'factory_bot'
end

group :production do
  gem 'puma'
  gem 'redis'
end
```

### Dependency Resolution
```ruby
# Check dependency conflicts
# Command: bundle check

# Update dependencies
# Command: bundle update

# Update specific gem
# Command: bundle update rails

# Install with specific options
# Command: bundle install --deployment --without development test

# Lock file management
# Gemfile.lock - locks exact versions for reproducible builds

# Dependency visualization
# Command: bundle viz  # Requires graphviz
```

### Version Constraints
```ruby
# Version constraint examples
gem 'rails', '6.0.0'        # Exact version
gem 'rails', '>= 6.0.0'      # Minimum version
gem 'rails', '~> 6.0.0'      # Pessimistic: >= 6.0.0, < 6.1.0
gem 'rails', '> 6.0.0, < 6.1.0'  # Range
gem 'rails', '!= 6.0.1'      # Exclude specific version

# Semantic versioning
# MAJOR.MINOR.PATCH
# ~> 1.2.3 means >= 1.2.3, < 1.3.0
# ~> 1.2 means >= 1.2.0, < 2.0.0

# Development versions
gem 'rails', git: 'https://github.com/rails/rails.git', branch: 'main'
gem 'rails', path: '/path/to/local/rails'
```

## Gem Security

### Security Best Practices
```ruby
# Gemfile security practices
source 'https://rubygems.org'

# Use HTTPS sources only
source 'https://rubygems.org'  # Good
# source 'http://rubygems.org'  # Bad - insecure

# Pin critical security gems
gem 'rails', '6.0.3.4'  # Pin to specific version with security fixes

# Use bundler-audit to check for vulnerabilities
# Command: bundle audit

# Use signed gems
gem 'signed_gem', require_signed: true

# Restrict gem sources
bundle config --global mirror.https://rubygems.org https://rubygems.org
```

### Vulnerability Scanning
```ruby
# Security scanning tools
# bundler-audit - Check for known vulnerabilities
# Command: bundle audit

# brakeman - Security scanner for Rails applications
# Command: brakeman

# Code scanning
# Command: bundle exec rubocop --only Security

# Gem signing
# Command: gem sign my_gem-1.0.0.gem

# Verify signed gems
# Command: gem verify my_gem-1.0.0.gem
```

### Gem Locking
```ruby
# Gemfile.lock importance
# Locks exact gem versions for reproducible builds
# Should be committed to version control
# Ensures all developers use same versions

# Update lock file carefully
# Command: bundle update  # Updates all gems
# Command: bundle update rails  # Updates only rails and dependencies

# Clean old versions
# Command: bundle clean --force
```

## Gem Configuration

### Bundler Configuration
```ruby
# .bundle/config
---
BUNDLE_DEPLOYMENT: "true"
BUNDLE_PATH: "vendor/bundle"
BUNDLE_WITHOUT: "development:test"
BUNDLE_LOCAL_GEMS: "true"

# Command line configuration
bundle config set --local deployment true
bundle config set --local path vendor/bundle
bundle config set --local without 'development test'

# Global configuration
bundle config set --global mirror.https://rubygems.org https://rubygems.org
```

### Gem Environment
```ruby
# Check gem environment
# Command: gem env

# Gem paths
# Command: gem environment gemdir
# Command: gem environment gempath

# Gem home
# Command: gem environment gemhome

# Platform information
# Command: gem environment platform

# Ruby version
# Command: gem environment ruby_version
```

### Custom Gem Sources
```ruby
# Gemfile with custom sources
source 'https://rubygems.org'

# Private gem server
source 'https://gems.company.com' do
  # Authentication
  gem 'private_gem'
end

# Multiple sources
source 'https://rubygems.org'
source 'https://gems.company.com'

# Source priority
# First source has priority
# Use explicit source for specific gems
gem 'private_gem', source: 'https://gems.company.com'
```

## Gem Testing

### Testing Strategies
```ruby
# spec/spec_helper.rb
require 'simplecov'
SimpleCov.start

RSpec.configure do |config|
  config.color = true
  config.formatter = :documentation
  
  config.before(:suite) do
    # Setup test environment
  end
  
  config.after(:suite) do
    # Cleanup test environment
  end
end

# spec/my_gem_spec.rb
RSpec.describe MyGem do
  it "has a version number" do
    expect(MyGem::VERSION).not_to be nil
  end
  
  it "returns hello message" do
    expect(MyGem.hello).to eq("Hello from MyGem version #{MyGem::VERSION}")
  end
end

# spec/my_gem/core_spec.rb
RSpec.describe MyGem::Core do
  let(:core) { MyGem::Core.new("World") }
  
  it "greets with name" do
    expect(core.greet).to eq("Hello, World!")
  end
  
  it "handles empty name" do
    core = MyGem::Core.new("")
    expect(core.greet).to eq("Hello, !")
  end
end
```

### Integration Testing
```ruby
# spec/integration_spec.rb
RSpec.describe "Gem Integration" do
  it "works with other gems" do
    # Test integration with JSON
    require 'json'
    
    core = MyGem::Core.new("Test")
    result = JSON.parse(core.greet.to_json)
    expect(result).to eq("Hello, Test!")
  end
  
  it "handles edge cases" do
    # Test edge cases
    expect { MyGem::Core.new(nil) }.to raise_error(NoMethodError)
  end
end
```

### Performance Testing
```ruby
# spec/performance_spec.rb
require 'benchmark'

RSpec.describe "Gem Performance" do
  it "performs well with large inputs" do
    time = Benchmark.realtime do
      1000.times { MyGem::Core.new("Test#{i}").greet }
    end
    
    expect(time).to be < 1.0  # Should complete in less than 1 second
  end
  
  it "doesn't leak memory" do
    require 'memory_profiler'
    
    report = MemoryProfiler.report do
      1000.times { MyGem::Core.new("Test#{i}").greet }
    end
    
    expect(report.total_allocated_memsize).to be < 1000000  # Less than 1MB
  end
end
```

## Best Practices

### Gem Development Best Practices
```ruby
# Good gem structure
my_gem/
├── lib/
│   └── my_gem/
│       ├── version.rb
│       ├── core.rb
│       └──.rb
├── spec/
│   ├── spec_helper.rb
│   ├── my_gem_spec.rb
│   └── my_gem/
│       └── core_spec.rb
├── bin/
│   └── my_gem
├── README.md
├── LICENSE
├── CHANGELOG.md
├── my_gem.gemspec
└── Gemfile

# Good gemspec
Gem::Specification.new do |spec|
  spec.name          = "my_gem"
  spec.version       = MyGem::VERSION
  spec.authors        = ["Your Name"]
  spec.email          = ["your.email@example.com"]
  spec.summary        = "Short summary"
  spec.description    = "Long description"
  spec.homepage       = "https://github.com/yourname/my_gem"
  spec.license        = "MIT"
  spec.required_ruby_version = ">= 2.5.0"
  
  spec.files          = Dir["README.md", "LICENSE", "lib/**/*.rb"]
  spec.bindir         = "exe"
  spec.executables    = ["my_gem"]
  spec.require_paths  = ["lib"]
  
  spec.add_dependency "json", "~> 2.0"
  spec.add_development_dependency "rspec", "~> 3.0"
end

# Good version management
# Semantic versioning: MAJOR.MINOR.PATCH
# MAJOR: Breaking changes
# MINOR: New features, backward compatible
# PATCH: Bug fixes, backward compatible
```

### Documentation Best Practices
```ruby
# Good documentation
module MyGem
  # This is the main class for MyGem functionality
  #
  # @example Basic usage
  #   core = MyGem::Core.new("World")
  #   core.greet  # => "Hello, World!"
  #
  # @author Your Name
  # @since 1.0.0
  class Core
    # Creates a new Core instance
    #
    # @param name [String] the name to greet
    # @raise [ArgumentError] if name is nil
    # @return [Core] a new Core instance
    #
    # @example
    #   core = MyGem::Core.new("Alice")
    def initialize(name)
      raise ArgumentError, "Name cannot be nil" if name.nil?
      @name = name
    end
    
    # Returns a greeting message
    #
    # @return [String] the greeting message
    #
    # @example
    #   core.greet  # => "Hello, Alice!"
    def greet
      "Hello, #{@name}!"
    end
  end
end

# README.md structure
# MyGem
# =====
#
# A short description of MyGem.
#
# Installation
# ------------
#
# Add this line to your application's Gemfile:
#
# ```ruby
# gem 'my_gem'
# ```
#
# And then execute:
#
#     $ bundle install
#
# Or install it yourself as:
#
#     $ gem install my_gem
#
# Usage
# -----
#
# ```ruby
# require 'my_gem'
#
# core = MyGem::Core.new("World")
# puts core.greet
# ```
```

### Testing Best Practices
```ruby
# Good testing practices
RSpec.describe MyGem::Core do
  # Use descriptive test names
  describe "#greet" do
    context "with valid name" do
      it "returns greeting message" do
        core = MyGem::Core.new("Alice")
        expect(core.greet).to eq("Hello, Alice!")
      end
    end
    
    context "with nil name" do
      it "raises ArgumentError" do
        expect { MyGem::Core.new(nil) }.to raise_error(ArgumentError)
      end
    end
  end
  
  # Use let for test data
  let(:core) { MyGem::Core.new("Test") }
  
  # Use subject when appropriate
  subject { MyGem::Core.new("Test") }
  
  it { is_expected.to respond_to(:greet) }
  
  # Use shared examples
  shared_examples "greeting behavior" do
    it "returns a greeting" do
      expect(subject.greet).to start_with("Hello")
    end
  end
  
  context "with different names" do
    subject { MyGem::Core.new(name) }
    
    context "when name is 'Alice'" do
      let(:name) { "Alice" }
      it_behaves_like "greeting behavior"
    end
  end
end
```

## Common Pitfalls

### Dependency Issues
```ruby
# Pitfall: Conflicting gem versions
# Gemfile
gem 'rails', '~> 5.0'
gem 'devise', '~> 4.0'  # Requires Rails 6.0+

# Solution: Check compatibility or use different versions
gem 'rails', '~> 6.0'
gem 'devise', '~> 4.0'

# Pitfall: Not locking versions in production
# Gemfile
gem 'rails'  # No version specified

# Solution: Lock versions in production
gem 'rails', '6.0.3.4'

# Pitfall: Too many development dependencies
# Gemfile
group :development do
  gem 'pry'
  gem 'pry-byebug'
  gem 'pry-stack_explorer'
  gem 'pry-doc'
  gem 'pry-remote'
  gem 'pry-rails'
end

# Solution: Keep development dependencies minimal
group :development do
  gem 'pry'
  gem 'rubocop'
end
```

### Security Issues
```ruby
# Pitfall: Using insecure gem sources
# Gemfile
source 'http://rubygems.org'  # Insecure HTTP

# Solution: Use HTTPS
source 'https://rubygems.org'

# Pitfall: Not updating vulnerable gems
# Solution: Regular security audits
# Command: bundle audit
# Command: bundle update

# Pitfall: Using development gems in production
# Gemfile
gem 'pry'  # Should be in development group only

# Solution: Group gems properly
group :development do
  gem 'pry'
end
```

### Performance Issues
```ruby
# Pitfall: Loading too many gems
# Gemfile
gem 'rails'
gem 'devise'
gem 'pundit'
gem 'cancancan'
gem 'paperclip'
gem 'carrierwave'
gem 'activeadmin'
gem 'kaminari'
gem 'will_paginate'

# Solution: Load only what you need
gem 'rails'
gem 'devise'
gem 'pundit'
gem 'kaminari'

# Pitfall: Not using lazy loading
# Solution: Use autoload and eager_load appropriately
```

## Summary

Ruby gems and packages provide:

**Gem Structure:**
- Standard gem directory layout
- Gemspec file configuration
- Version management
- Executable scripts

**Gem Development:**
- Bundler workflow
- Building and publishing
- Local testing
- Rake tasks for automation

**Popular Gems:**
- Web development (Rails, Sinatra)
- Testing (RSpec, Minitest)
- Development tools (RuboCop, Pry)
- Security (Brakeman, Bundler Audit)

**Dependency Management:**
- Gemfile configuration
- Version constraints
- Dependency resolution
- Lock file management

**Gem Security:**
- HTTPS sources
- Vulnerability scanning
- Gem signing
- Security best practices

**Gem Configuration:**
- Bundler configuration
- Custom gem sources
- Environment variables
- Platform-specific dependencies

**Gem Testing:**
- Unit testing with RSpec
- Integration testing
- Performance testing
- Coverage reporting

**Best Practices:**
- Proper gem structure
- Semantic versioning
- Comprehensive documentation
- Thorough testing

**Common Pitfalls:**
- Dependency conflicts
- Security vulnerabilities
- Performance issues
- Improper gem organization

RubyGems provides a robust ecosystem for sharing and distributing Ruby code, enabling developers to easily manage dependencies and build applications with reusable components.
