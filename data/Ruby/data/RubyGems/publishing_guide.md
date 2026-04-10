# RubyGems Publishing Guide

## Overview
This guide walks you through the process of publishing a Ruby gem to the RubyGems repository.

## Prerequisites

### 1. RubyGems Account
- Sign up at [RubyGems.org](https://rubygems.org/)
- Verify your email address
- Enable multi-factor authentication (recommended)

### 2. Required Tools
```bash
# Install RubyGems (usually comes with Ruby)
gem update --system

# Install Bundler (for managing dependencies)
gem install bundler

# Install gem building tools
gem install build
```

## Step 1: Prepare Your Gem Structure

### Basic Gem Structure
```
your_gem_name/
├── lib/
│   └── your_gem_name.rb
├── test/ or spec/
├── Gemfile
├── your_gem_name.gemspec
├── README.md
├── LICENSE.txt
└── Rakefile (optional)
```

### Key Files Explained

#### Gemspec File (`your_gem_name.gemspec`)
```ruby
require_relative 'lib/your_gem_name/version'

Gem::Specification.new do |spec|
  spec.name          = "your_gem_name"
  spec.version       = YourGemName::VERSION
  spec.authors       = ["Your Name"]
  spec.email         = ["your.email@example.com"]
  
  spec.summary       = "A short summary of your gem"
  spec.description   = "A longer description of your gem"
  spec.homepage      = "https://github.com/yourusername/your_gem_name"
  spec.license       = "MIT"
  spec.required_ruby_version = ">= 2.6.0"
  
  spec.metadata["homepage_uri"] = spec.homepage
  spec.metadata["source_code_uri"] = spec.homepage
  spec.metadata["changelog_uri"] = "#{spec.homepage}/blob/main/CHANGELOG.md"
  
  # Specify which files to include in the gem
  spec.files = Dir.chdir(File.expand_path(__dir__)) do
    Dir["{app,config,db,lib,test}/**/*", "MIT-LICENSE", "Rakefile", "README.md"]
  end
  
  spec.require_paths = ["lib"]
  
  # Runtime dependencies
  spec.add_dependency "some_gem", "~> 1.0"
  
  # Development dependencies
  spec.add_development_dependency "rspec", "~> 3.0"
end
```

#### Version File (`lib/your_gem_name/version.rb`)
```ruby
module YourGemName
  VERSION = "0.1.0"
end
```

#### Main Gem File (`lib/your_gem_name.rb`)
```ruby
require_relative "your_gem_name/version"

module YourGemName
  class Error < StandardError; end
  
  def self.hello
    puts "Hello from YourGemName!"
  end
end
```

## Step 2: Set Up Authentication

### Method 1: API Key (Recommended)
```bash
# Get your API key from RubyGems.org account settings
# Set it as an environment variable
export GEM_HOST_API_KEY='your_api_key_here'

# Or save it to ~/.gem/credentials
# Format:
# ---
# :rubygems_api_key: your_api_key_here
```

### Method 2: Sign In
```bash
gem signin
# Enter your RubyGems credentials
```

## Step 3: Build Your Gem

```bash
# Navigate to your gem directory
cd your_gem_name

# Build the gem
gem build your_gem_name.gemspec

# This creates a .gem file
# Example: your_gem_name-0.1.0.gem
```

## Step 4: Test Your Gem Locally

```bash
# Install the gem locally
gem install ./your_gem_name-0.1.0.gem

# Test it
irb
# require 'your_gem_name'
# YourGemName.hello

# Uninstall when done
gem uninstall your_gem_name
```

## Step 5: Publish to RubyGems

```bash
# Publish the gem
gem push your_gem_name-0.1.0.gem

# You'll see output like:
# Successfully registered gem: your_gem_name (0.1.0)
```

## Step 6: Verify Publication

```bash
# Search for your gem
gem search your_gem_name

# Or check on RubyGems.org
# https://rubygems.org/gems/your_gem_name
```

## Best Practices

### Version Management
- Follow [Semantic Versioning](https://semver.org/)
- Update version numbers in `version.rb`
- Use `MAJOR.MINOR.PATCH` format

### Documentation
- Write a comprehensive README.md
- Include installation instructions
- Provide usage examples
- Document all public methods

### Testing
- Write unit tests (RSpec or Minitest)
- Ensure all tests pass before publishing
- Consider continuous integration

### Security
- Never commit API keys to version control
- Use environment variables for sensitive data
- Review dependencies for vulnerabilities

## Publishing Updates

### Update Version
```ruby
# Update lib/your_gem_name/version.rb
VERSION = "0.2.0"  # Increment version
```

### Build and Push
```bash
gem build your_gem_name.gemspec
gem push your_gem_name-0.2.0.gem
```

### Yank a Version (Emergency Only)
```bash
# Remove a published version (use carefully!)
gem yank your_gem_name -v 0.1.0

# Unyank if needed
gem unyank your_gem_name -v 0.1.0
```

## Troubleshooting

### Common Issues

#### "Gem not found" Error
- Wait 5-10 minutes after publishing
- Check if gem was successfully uploaded
- Verify gem name spelling

#### Permission Denied
- Check your API key is valid
- Ensure you're the gem owner
- Run `gem signin` again

#### Build Failures
- Check gemspec syntax
- Verify all required files exist
- Ensure Ruby version compatibility

### Useful Commands
```bash
# Check gem info
gem specification your_gem_name

# List installed gems
gem list

# Clean old versions
gem cleanup

# Check for outdated gems
gem outdated
```

## Automation

### Rake Tasks
```ruby
# Rakefile
require 'bundler/gem_tasks'

task :release do
  sh "gem build your_gem_name.gemspec"
  sh "gem push your_gem_name-#{YourGemName::VERSION}.gem"
end
```

### GitHub Actions
Create `.github/workflows/release.yml` for automated releases when you push tags.

## Resources

- [RubyGems Documentation](https://guides.rubygems.org/)
- [Bundler Documentation](https://bundler.io/)
- [RubyGems.org](https://rubygems.org/)
- [Semantic Versioning](https://semver.org/)

## Conclusion

Publishing a Ruby gem is straightforward once you understand the process. Remember to:
1. Test thoroughly before publishing
2. Use semantic versioning
3. Keep documentation up to date
4. Monitor your gem for issues and updates

Happy publishing! 🎉
