#!/usr/bin/env ruby
# Setup script for The Ultimate Ruby Programming Guide
# This script helps set up the development environment and verify installation

require 'fileutils'
require 'json'

class SetupScript
  def initialize
    @ruby_version = RUBY_VERSION
    @platform = RUBY_PLATFORM
    @errors = []
    @warnings = []
  end
  
  def run
    puts "🚀 Setting up The Ultimate Ruby Programming Guide"
    puts "=" * 50
    
    check_ruby_version
    check_required_gems
    create_directories
    verify_structure
    generate_config_files
    
    print_summary
  end
  
  private
  
  def check_ruby_version
    puts "\n🔍 Checking Ruby version..."
    
    if @ruby_version >= Gem::Version.new('2.7.0')
      puts "✅ Ruby version #{@ruby_version} is supported"
    else
      @errors << "Ruby version #{@ruby_version} is too old. Please upgrade to 2.7.0 or higher"
    end
    
    puts "   Platform: #{@platform}"
    puts "   Installation path: #{RbConfig::CONFIG['bindir']}"
  end
  
  def check_required_gems
    puts "\n📦 Checking required gems..."
    
    required_gems = {
      'bundler' => '2.0.0',
      'rake' => '13.0.0',
      'rspec' => '3.0.0',
      'rubocop' => '1.0.0'
    }
    
    required_gems.each do |gem, min_version|
      begin
        gem_version = Gem.loaded_specs(gem)&.first&.version
        if gem_version
          if gem_version >= Gem::Version.new(min_version)
            puts "✅ #{gem} (#{gem_version})"
          else
            @warnings << "#{gem} version #{gem_version} is below recommended #{min_version}"
          end
        else
          @warnings << "#{gem} is not installed. Install with: gem install #{gem}"
        end
      rescue Gem::LoadError
        @warnings << "#{gem} is not available. Install with: gem install #{gem}"
      end
    end
  end
  
  def create_directories
    puts "\n📁 Creating directory structure..."
    
    directories = [
      'assets/images',
      'assets/diagrams',
      'scripts',
      'tools',
      'examples/workshops',
      'examples/challenges',
      'examples/interview-prep',
      'advanced/research'
    ]
    
    directories.each do |dir|
      if Dir.exist?(dir)
        puts "✅ #{dir}/ already exists"
      else
        Dir.mkdir(dir)
        puts "✅ Created #{dir}/"
      end
    end
  end
  
  def verify_structure
    puts "\n🔍 Verifying repository structure..."
    
    required_files = [
      'README.md',
      'CONTRIBUTING.md',
      'CHANGELOG.md',
      'CODE_OF_CONDUCT.md',
      'LICENSE',
      '.gitignore',
      'CONCLUSION.md'
    ]
    
    required_dirs = [
      'docs',
      'examples',
      'advanced'
    ]
    
    required_files.each do |file|
      if File.exist?(file)
        puts "✅ #{file}"
      else
        @errors << "Missing required file: #{file}"
      end
    end
    
    required_dirs.each do |dir|
      if Dir.exist?(dir)
        puts "✅ #{dir}/"
      else
        @errors << "Missing required directory: #{dir}/"
      end
    end
  end
  
  def generate_config_files
    puts "\n⚙️ Generating configuration files..."
    
    # Generate VS Code settings
    generate_vscode_settings
    
    # Generate Ruby LSP config
    generate_lsp_config
    
    # Generate development scripts
    generate_dev_scripts
  end
  
  def generate_vscode_settings
    vscode_dir = '.vscode'
    Dir.mkdir(vscode_dir) unless Dir.exist?(vscode_dir)
    
    settings = {
      "ruby.lint.rubocop.enabled" => true,
      "ruby.format" => "rubocop",
      "files.associations" => {
        "*.rb" => "ruby",
        "*.rbw" => "ruby",
        "Gemfile" => "ruby",
        "Rakefile" => "ruby",
        "*.rake" => "ruby"
      },
      "editor.formatOnSave" => true,
      "editor.tabSize" => 2,
      "editor.insertSpaces" => true,
      "files.exclude" => {
        "**/.git",
        "**/node_modules",
        "**/bundle",
        "**/vendor"
      }
    }
    
    File.write("#{vscode_dir}/settings.json", JSON.pretty_generate(settings))
    puts "✅ Generated .vscode/settings.json"
  end
  
  def generate_lsp_config
    lsp_config = {
      "diagnostic" => {
        "enable" => true
      },
      "formatting" => {
        "enabled" => true
      },
      "linting" => {
        "enabled" => true,
        "rubocop" => {
          "enabled" => true
        }
      }
    }
    
    File.write('.solargraph.yml', lsp_config.to_yaml)
    puts "✅ Generated .solargraph.yml"
  end
  
  def generate_dev_scripts
    scripts = {
      "test" => "bundle exec rspec",
      "lint" => "bundle exec rubocop",
      "format" => "bundle exec rubocop -a",
      "clean" => "rm -rf .bundle Gemfile.lock",
      "setup" => "bundle install",
      "serve" => "bundle exec rackup -p 9292"
    }
    
    File.write('package.json', JSON.pretty_generate({
      "name" => "ruby-guide",
      "scripts" => scripts,
      "devDependencies" => {
        "solargraph" => "^0.50.0"
      }
    }))
    
    puts "✅ Generated package.json with dev scripts"
  end
  
  def print_summary
    puts "\n" + "=" * 50
    puts "📊 Setup Summary"
    puts "=" * 50
    
    if @errors.empty?
      puts "✅ Setup completed successfully!"
      puts "🎉 You're ready to start learning Ruby!"
    else
      puts "❌ Setup completed with #{@errors.length} errors:"
      @errors.each { |error| puts "   • #{error}" }
    end
    
    if @warnings.any?
      puts "\n⚠️ #{@warnings.length} warnings:"
      @warnings.each { |warning| puts "   • #{warning}" }
    end
    
    puts "\n📚 Next steps:"
    puts "1. Read README.md for an overview"
    puts "2. Start with docs/01-introduction.md"
    puts "3. Follow the learning path in docs/00-roadmap.md"
    puts "4. Try examples from examples/basic-examples/"
    puts "5. Join the community discussions!"
    
    unless @errors.empty?
      puts "\n🔧 Fix the errors above and run this script again."
      exit 1
    end
  end
end

# Run the setup
if __FILE__ == $0
  setup = SetupScript.new
  setup.run
end
