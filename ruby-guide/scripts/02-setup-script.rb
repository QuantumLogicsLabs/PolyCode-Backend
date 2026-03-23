#!/usr/bin/env ruby

# Ruby Guide Setup Script
# This script helps set up the Ruby development environment
# and configure the Ruby Guide project for development

require 'fileutils'
require 'json'
require 'net/http'
require 'uri'

class RubyGuideSetup
  def initialize
    @project_root = Dir.pwd
    @config_file = File.join(@project_root, '.ruby-guide-config.json')
    @log_file = File.join(@project_root, 'setup.log')
  end

  def run
    puts "🚀 Ruby Guide Setup Script"
    puts "=========================="
    
    log("Setup started at #{Time.now}")
    
    check_requirements
    setup_ruby_environment
    setup_project_structure
    install_dependencies
    configure_git
    setup_development_tools
    run_initial_tests
    
    puts "\n✅ Setup completed successfully!"
    puts "📁 Project ready at: #{@project_root}"
    puts "📋 Check setup.log for details"
    
    log("Setup completed successfully")
  rescue => e
    puts "\n❌ Setup failed: #{e.message}"
    puts "📋 Check setup.log for error details"
    log("Setup failed: #{e.message}")
    exit 1
  end

  private

  def check_requirements
    puts "\n🔍 Checking system requirements..."
    
    # Check Ruby version
    ruby_version = RUBY_VERSION
    puts "✅ Ruby version: #{ruby_version}"
    
    if ruby_version < '3.0.0'
      puts "⚠️  Warning: Ruby #{ruby_version} detected. Recommended: Ruby 3.0+"
    end
    
    # Check for required commands
    required_commands = %w[gem bundler git]
    required_commands.each do |cmd|
      if command_exists?(cmd)
        puts "✅ #{cmd} is available"
      else
        raise "#{cmd} is required but not found"
      end
    end
    
    # Check for optional commands
    optional_commands = %w[sqlite3 psql node npm]
    optional_commands.each do |cmd|
      if command_exists?(cmd)
        puts "✅ #{cmd} is available"
      else
        puts "⚠️  #{cmd} is not available (optional)"
      end
    end
    
    log("System requirements checked")
  end

  def setup_ruby_environment
    puts "\n💎 Setting up Ruby environment..."
    
    # Create .ruby-version file
    ruby_version_file = File.join(@project_root, '.ruby-version')
    File.write(ruby_version_file, "#{RUBY_VERSION}\n")
    puts "✅ Created .ruby-version file"
    
    # Create .gemrc for better gem management
    gemrc_content = <<~GEMRC
      ---
      :sources:
        - https://rubygems.org
      :benchmark: false
      :verbose: true
      :backtrace: true
      :update_sources: true
      :bulk_threshold: 1000
      :install: --no-document --wrappers --verbose
      :update: --no-document --wrappers --verbose
    GEMRC
    
    gemrc_file = File.join(Dir.home, '.gemrc')
    File.write(gemrc_file, gemrc_content) unless File.exist?(gemrc_file)
    puts "✅ Configured gem settings"
    
    log("Ruby environment setup completed")
  end

  def setup_project_structure
    puts "\n📁 Setting up project structure..."
    
    # Create necessary directories
    directories = [
      'tmp',
      'tmp/cache',
      'tmp/pids',
      'tmp/sockets',
      'log',
      'coverage',
      '.bundle'
    ]
    
    directories.each do |dir|
      dir_path = File.join(@project_root, dir)
      FileUtils.mkdir_p(dir_path) unless Dir.exist?(dir_path)
      puts "✅ Created #{dir}/ directory"
    end
    
    # Create .gitignore if it doesn't exist
    gitignore_file = File.join(@project_root, '.gitignore')
    unless File.exist?(gitignore_file)
      gitignore_content = <<~GITIGNORE
        # Ruby
        *.gem
        *.rbc
        .bundle/
        .config/
        .yardoc/
        .yardoc/
        .rbenv-version
        Gemfile.lock
        .ruby-version
        
        # Environment
        .env
        .env.local
        .env.development
        .env.test
        .env.production
        
        # Logs
        *.log
        log/
        
        # Temp files
        tmp/
        .sass-cache/
        
        # Coverage
        coverage/
        .coverage
        
        # Documentation
        *.html
        *.yml
        .yardoc/
        doc/
        
        # IDE
        .vscode/
        .idea/
        *.swp
        *.swo
        *~
        
        # OS
        .DS_Store
        Thumbs.db
        
        # Backup files
        *.bak
        *.backup
        *.orig
      GITIGNORE
      
      File.write(gitignore_file, gitignore_content)
      puts "✅ Created .gitignore file"
    end
    
    log("Project structure setup completed")
  end

  def install_dependencies
    puts "\n📦 Installing dependencies..."
    
    # Check if Gemfile exists
    gemfile_path = File.join(@project_root, 'Gemfile')
    unless File.exist?(gemfile_path)
      create_gemfile
    end
    
    # Install gems
    Dir.chdir(@project_root) do
      puts "Running bundle install..."
      system('bundle install')
      
      if $?.success?
        puts "✅ Dependencies installed successfully"
      else
        puts "⚠️  Bundle install had issues, continuing..."
      end
    end
    
    log("Dependencies installation completed")
  end

  def create_gemfile
    puts "Creating Gemfile..."
    
    gemfile_content = <<~GEMFILE
      source 'https://rubygems.org'
      
      ruby '#{RUBY_VERSION}'
      
      # Core dependencies
      gem 'activesupport', '~> 7.0'
      gem 'json', '~> 2.6'
      gem 'yaml', '~> 0.2'
      
      # Development dependencies
      group :development do
        gem 'rake', '~> 13.0'
        gem 'rspec', '~> 3.12'
        gem 'rubocop', '~> 1.50'
        gem 'rubocop-rspec', '~> 2.20'
        gem 'simplecov', '~> 0.21'
        gem 'yard', '~> 0.9'
        gem 'pry', '~> 0.14'
      end
      
      # Test dependencies
      group :test do
        gem 'rspec-rails', '~> 5.1'
        gem 'factory_bot', '~> 6.2'
        gem 'faker', '~> 3.2'
      end
      
      # Documentation
      group :doc do
        gem 'kramdown', '~> 2.4'
        gem 'rouge', '~> 4.1'
      end
    GEMFILE
    
    gemfile_path = File.join(@project_root, 'Gemfile')
    File.write(gemfile_path, gemfile_content)
    puts "✅ Created Gemfile"
    
    # Create Gemfile.lock
    lockfile_path = File.join(@project_root, 'Gemfile.lock')
    File.write(lockfile_path, "# Generated by bundle install\n") unless File.exist?(lockfile_path)
  end

  def configure_git
    puts "\n🔧 Configuring Git..."
    
    # Check if we're in a git repository
    unless Dir.exist?(File.join(@project_root, '.git'))
      puts "Initializing Git repository..."
      Dir.chdir(@project_root) do
        system('git init')
        system('git add .')
        system('git commit -m "Initial setup"')
      end
      puts "✅ Git repository initialized"
    else
      puts "✅ Git repository already exists"
    end
    
    # Configure git hooks if they don't exist
    setup_git_hooks
    
    log("Git configuration completed")
  end

  def setup_git_hooks
    hooks_dir = File.join(@project_root, '.git/hooks')
    return unless Dir.exist?(hooks_dir)
    
    # Pre-commit hook for code quality
    pre_commit_hook = <<~HOOK
      #!/bin/bash
      
      echo "Running pre-commit checks..."
      
      # Check Ruby syntax
      echo "Checking Ruby syntax..."
      find . -name "*.rb" -not -path "./vendor/*" -not -path "./.bundle/*" -exec ruby -c {} \;
      if [ $? -ne 0 ]; then
        echo "❌ Ruby syntax check failed"
        exit 1
      fi
      
      # Run RuboCop if available
      if command -v rubocop &> /dev/null; then
        echo "Running RuboCop..."
        rubocop --parallel
        if [ $? -ne 0 ]; then
          echo "❌ RuboCop check failed"
          exit 1
        fi
      fi
      
      echo "✅ Pre-commit checks passed"
    HOOK
    
    pre_commit_path = File.join(hooks_dir, 'pre-commit')
    File.write(pre_commit_path, pre_commit_hook)
    File.chmod(0755, pre_commit_path)
    puts "✅ Created pre-commit hook"
  end

  def setup_development_tools
    puts "\n🛠️  Setting up development tools..."
    
    # Create Rakefile
    create_rakefile
    
    # Create RSpec configuration
    setup_rspec
    
    # Create RuboCop configuration
    setup_rubocop
    
    # Create VS Code settings
    setup_vscode
    
    log("Development tools setup completed")
  end

  def create_rakefile
    rakefile_content = <<~RAKEFILE
      require 'bundler/setup'
      require 'rspec/core/rake_task'
      
      task :default => :spec
      
      RSpec::Core::RakeTask.new(:spec) do |t|
        t.pattern = 'spec/**/*_spec.rb'
        t.rspec_opts = '--format documentation'
      end
      
      desc "Run all code quality checks"
      task :quality do
        puts "Running RuboCop..."
        sh "rubocop --parallel"
        
        puts "Checking Ruby syntax..."
        sh "find . -name '*.rb' -not -path './vendor/*' -not -path './.bundle/*' -exec ruby -c {} \\;"
      end
      
      desc "Generate documentation"
      task :docs do
        puts "Generating documentation..."
        sh "yardoc"
      end
      
      desc "Clean up temporary files"
      task :clean do
        FileUtils.rm_rf('tmp/cache')
        FileUtils.rm_rf('coverage')
        FileUtils.rm_rf('.yardoc')
        puts "Temporary files cleaned"
      end
      
      namespace :setup do
        desc "Setup development environment"
        task :dev do
          puts "Setting up development environment..."
          sh "bundle install"
          sh "yard config --gem-path-yri"
        end
      end
    RAKEFILE
    
    rakefile_path = File.join(@project_root, 'Rakefile')
    File.write(rakefile_path, rakefile_content)
    puts "✅ Created Rakefile"
  end

  def setup_rspec
    spec_dir = File.join(@project_root, 'spec')
    FileUtils.mkdir_p(spec_dir) unless Dir.exist?(spec_dir)
    
    spec_helper_content = <<~SPEC_HELPER
      require 'simplecov'
      SimpleCov.start
      
      require 'rspec'
      require 'factory_bot'
      
      RSpec.configure do |config|
        config.color = true
        config.formatter = :documentation
        config.order = :random
        config.profile_examples = 10
        
        config.expect_with :rspec do |expectations|
          expectations.include_chain_clauses_in_custom_matcher_descriptions = true
        end
        
        config.mock_with :rspec do |mocks|
          mocks.verify_partial_doubles = true
        end
      end
      
      FactoryBot.find_definitions
    SPEC_HELPER
    
    spec_helper_path = File.join(spec_dir, 'spec_helper.rb')
    File.write(spec_helper_path, spec_helper_content)
    puts "✅ Created RSpec configuration"
  end

  def setup_rubocop
    rubocop_config = <<~RUBOCOP
      require:
        - rubocop-rspec
      
      AllCops:
        TargetRubyVersion: #{RUBY_VERSION}
        NewCops: enable
        Exclude:
          - 'vendor/**/*'
          - '.bundle/**/*'
      
      Style/Documentation:
        Enabled: false
      
      Metrics/MethodLength:
        Max: 20
      
      Metrics/ClassLength:
        Max: 100
      
      Metrics/BlockLength:
        Max: 30
      
      Layout/LineLength:
        Max: 120
    RUBOCOP
    
    rubocop_config_path = File.join(@project_root, '.rubocop.yml')
    File.write(rubocop_config_path, rubocop_config)
    puts "✅ Created RuboCop configuration"
  end

  def setup_vscode
    vscode_dir = File.join(@project_root, '.vscode')
    FileUtils.mkdir_p(vscode_dir) unless Dir.exist?(vscode_dir)
    
    # VS Code settings
    vscode_settings = {
      "ruby.lint" => {
        "rubocop" => {
          "lint" => true,
          "rails" => true
        }
      },
      "ruby.format" => "rubocop",
      "files.exclude" => {
        "**/.git": true,
        "**/.bundle": true,
        "**/vendor": true,
        "**/coverage": true
      },
      "editor.formatOnSave" => true,
      "editor.tabSize" => 2,
      "editor.insertSpaces" => true
    }
    
    settings_path = File.join(vscode_dir, 'settings.json')
    File.write(settings_path, JSON.pretty_generate(vscode_settings))
    puts "✅ Created VS Code settings"
    
    # VS Code extensions
    extensions = [
      "rebornix.ruby",
      "castwide.solargraph",
      "misogi.ruby-rubocop",
      "ms-vscode.vscode-json"
    ]
    
    extensions_path = File.join(vscode_dir, 'extensions.json')
    File.write(extensions_path, JSON.pretty_generate(extensions: extensions))
    puts "✅ Created VS Code extensions list"
  end

  def run_initial_tests
    puts "\n🧪 Running initial tests..."
    
    Dir.chdir(@project_root) do
      # Test Ruby syntax
      ruby_files = Dir.glob('**/*.rb').reject { |f| f.match?(/vendor|\.bundle/) }
      syntax_errors = []
      
      ruby_files.each do |file|
        unless system("ruby -c #{file} > /dev/null 2>&1")
          syntax_errors << file
        end
      end
      
      if syntax_errors.empty?
        puts "✅ All Ruby files have valid syntax"
      else
        puts "⚠️  Syntax errors in: #{syntax_errors.join(', ')}"
      end
      
      # Test RSpec if available
      if command_exists?('rspec')
        puts "Running RSpec tests..."
        system('rspec --format documentation')
        
        if $?.success?
          puts "✅ RSpec tests passed"
        else
          puts "⚠️  Some RSpec tests failed"
        end
      end
      
      # Test RuboCop if available
      if command_exists?('rubocop')
        puts "Running RuboCop..."
        system('rubocop --parallel')
        
        if $?.success?
          puts "✅ RuboCop checks passed"
        else
          puts "⚠️  RuboCop found style issues"
        end
      end
    end
    
    log("Initial tests completed")
  end

  def command_exists?(command)
    system("which #{command} > /dev/null 2>&1")
  end

  def log(message)
    File.open(@log_file, 'a') do |file|
      file.puts("[#{Time.now}] #{message}")
    end
  end
end

# Main execution
if __FILE__ == $0
  setup = RubyGuideSetup.new
  setup.run
end
