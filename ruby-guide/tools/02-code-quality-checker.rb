#!/usr/bin/env ruby

# Ruby Code Quality Checker
# This script analyzes Ruby code for quality issues, style violations,
# and potential improvements. It provides detailed reports and suggestions
# for code improvement.

require 'json'
require 'yaml'
require 'optparse'
require 'colorize'

class CodeQualityChecker
  def initialize
    @issues = []
    @stats = {
      files_analyzed: 0,
      total_lines: 0,
      total_methods: 0,
      total_classes: 0,
      issues_found: 0
    }
    @config = load_config
    @output_format = :console
  end

  def check_directory(directory, options = {})
    puts "🔍 Analyzing Ruby code in: #{directory}"
    puts "=" * 50
    
    @output_format = options[:format] || :console
    
    ruby_files = find_ruby_files(directory)
    puts "Found #{ruby_files.length} Ruby files"
    
    ruby_files.each do |file|
      analyze_file(file)
      @stats[:files_analyzed] += 1
    end
    
    generate_report
  end

  def check_file(file_path, options = {})
    @output_format = options[:format] || :console
    analyze_file(file_path)
    generate_report
  end

  private

  def find_ruby_files(directory)
    Dir.glob(File.join(directory, '**', '*.rb')).reject do |file|
      # Skip common directories that shouldn't be analyzed
      file.match?(/vendor\/|\.bundle\/|spec\/|test\/|tmp\//)
    end
  end

  def analyze_file(file_path)
    puts "Analyzing: #{file_path}" if @output_format == :console
    
    content = File.read(file_path)
    lines = content.lines
    @stats[:total_lines] += lines.length
    
    file_issues = []
    
    # Check various quality metrics
    file_issues.concat(check_method_length(file_path, content, lines))
    file_issues.concat(check_class_length(file_path, content, lines))
    file_issues.concat(check_line_length(file_path, content, lines))
    file_issues.concat(check_method_complexity(file_path, content, lines))
    file_issues.concat(check_naming_conventions(file_path, content, lines))
    file_issues.concat(check_comments(file_path, content, lines))
    file_issues.concat(check_unused_variables(file_path, content, lines))
    file_issues.concat(check_code_duplication(file_path, content, lines))
    file_issues.concat(check_security_issues(file_path, content, lines))
    file_issues.concat(check_performance_issues(file_path, content, lines))
    
    # Count classes and methods
    @stats[:total_classes] += content.scan(/^\s*class\s+\w+/).length
    @stats[:total_methods] += content.scan(/^\s*def\s+\w+/).length
    
    file_issues.each { |issue| issue[:file] = file_path }
    @issues.concat(file_issues)
    @stats[:issues_found] += file_issues.length
  end

  def check_method_length(file_path, content, lines)
    issues = []
    
    content.scan(/def\s+(\w+).*?(?=def|\Z)/m) do |method_match|
      method_name = method_match[1]
      method_content = method_match[0]
      method_lines = method_content.lines.length
      
      if method_lines > @config[:max_method_length]
        issues << {
          type: :method_length,
          severity: :warning,
          message: "Method '#{method_name}' is too long (#{method_lines} lines, max: #{@config[:max_method_length]})",
          line_number: find_line_number(content, method_match[0]),
          suggestion: "Consider breaking down this method into smaller methods"
        }
      end
    end
    
    issues
  end

  def check_class_length(file_path, content, lines)
    issues = []
    
    content.scan(/class\s+(\w+).*?(?=class|\Z)/m) do |class_match|
      class_name = class_match[1]
      class_content = class_match[0]
      class_lines = class_content.lines.length
      
      if class_lines > @config[:max_class_length]
        issues << {
          type: :class_length,
          severity: :warning,
          message: "Class '#{class_name}' is too long (#{class_lines} lines, max: #{@config[:max_class_length]})",
          line_number: find_line_number(content, class_match[0]),
          suggestion: "Consider extracting functionality into separate classes or modules"
        }
      end
    end
    
    issues
  end

  def check_line_length(file_path, content, lines)
    issues = []
    
    lines.each_with_index do |line, index|
      if line.length > @config[:max_line_length]
        issues << {
          type: :line_length,
          severity: :info,
          message: "Line #{index + 1} is too long (#{line.length} chars, max: #{@config[:max_line_length]})",
          line_number: index + 1,
          suggestion: "Break long lines or use line continuation"
        }
      end
    end
    
    issues
  end

  def check_method_complexity(file_path, content, lines)
    issues = []
    
    content.scan(/def\s+(\w+).*?(?=def|\Z)/m) do |method_match|
      method_name = method_match[1]
      method_content = method_match[0]
      
      complexity = calculate_complexity(method_content)
      
      if complexity > @config[:max_complexity]
        issues << {
          type: :complexity,
          severity: :warning,
          message: "Method '#{method_name}' has high complexity (#{complexity}, max: #{@config[:max_complexity]})",
          line_number: find_line_number(content, method_match[0]),
          suggestion: "Simplify the method or extract complex logic"
        }
      end
    end
    
    issues
  end

  def check_naming_conventions(file_path, content, lines)
    issues = []
    
    # Check class names (should be CamelCase)
    content.scan(/^\s*class\s+([a-z]+)/) do |match|
      issues << {
        type: :naming_convention,
        severity: :error,
        message: "Class name '#{match[1]}' should use CamelCase",
        line_number: find_line_number(content, match[0]),
        suggestion: "Rename class to #{match[1].split('_').map(&:capitalize).join}"
      }
    end
    
    # Check method names (should be snake_case)
    content.scan(/^\s*def\s+([A-Z][a-zA-Z0-9_]*)/) do |match|
      issues << {
        type: :naming_convention,
        severity: :error,
        message: "Method name '#{match[1]}' should use snake_case",
        line_number: find_line_number(content, match[0]),
        suggestion: "Rename method to #{match[1].gsub(/([A-Z])/, '_\\1').downcase}"
      }
    end
    
    # Check variable names (should be snake_case)
    content.scan(/([A-Z][a-zA-Z0-9_]*)\s*=/) do |match|
      # Skip constants (all caps)
      next if match[1] == match[1].upcase
      
      issues << {
        type: :naming_convention,
        severity: :warning,
        message: "Variable name '#{match[1]}' should use snake_case",
        line_number: find_line_number(content, match[0]),
        suggestion: "Rename variable to #{match[1].gsub(/([A-Z])/, '_\\1').downcase}"
      }
    end
    
    issues
  end

  def check_comments(file_path, content, lines)
    issues = []
    
    # Check for missing comments on complex methods
    content.scan(/def\s+(\w+).*?(?=def|\Z)/m) do |method_match|
      method_name = method_match[1]
      method_content = method_match[0]
      method_lines = method_content.lines.length
      
      if method_lines > 10 && !method_content.match(/^\s*#/)
        issues << {
          type: :missing_comments,
          severity: :info,
          message: "Complex method '#{method_name}' lacks documentation",
          line_number: find_line_number(content, method_match[0]),
          suggestion: "Add comments explaining the method's purpose"
        }
      end
    end
    
    # Check for TODO/FIXME comments
    content.scan(/^\s*#\s*(TODO|FIXME|XXX|HACK):?\s*(.*)/) do |match|
      issues << {
        type: :todo_comment,
        severity: :info,
        message: "#{match[1]} comment: #{match[2]}",
        line_number: find_line_number(content, match[0]),
        suggestion: "Address the TODO/FIXME comment"
      }
    end
    
    issues
  end

  def check_unused_variables(file_path, content, lines)
    issues = []
    
    # Simple check for assigned but unused variables
    content.scan(/(\w+)\s*=\s*[^=]/) do |match|
      var_name = match[1]
      
      # Skip common variables that might be used in ways we can't detect
      next if %w[@ @ $ @@].include?(var_name)
      
      # Check if variable is used elsewhere in the same scope
      method_content = extract_method_scope(content, find_line_number(content, match[0]))
      
      unless method_content.match(/\b#{Regexp.escape(var_name)}\b/)
        issues << {
          type: :unused_variable,
          severity: :warning,
          message: "Variable '#{var_name}' appears to be unused",
          line_number: find_line_number(content, match[0]),
          suggestion: "Remove unused variable or use it in the method"
        }
      end
    end
    
    issues
  end

  def check_code_duplication(file_path, content, lines)
    issues = []
    
    # Simple duplication check for repeated lines
    line_counts = Hash.new(0)
    
    lines.each do |line|
      stripped = line.strip
      line_counts[stripped] += 1 if stripped.length > 10
    end
    
    line_counts.each do |line, count|
      if count > 2
        issues << {
          type: :code_duplication,
          severity: :info,
          message: "Line appears #{count} times: '#{line[0..50]}#{'...' if line.length > 50}'",
          line_number: nil,
          suggestion: "Extract repeated code into a method"
        }
      end
    end
    
    issues
  end

  def check_security_issues(file_path, content, lines)
    issues = []
    
    # Check for eval usage
    content.scan(/\beval\s*\(/) do |match|
      issues << {
        type: :security,
        severity: :error,
        message: "Use of eval() can be dangerous",
        line_number: find_line_number(content, match[0]),
        suggestion: "Use safer alternatives or validate input"
      }
    end
    
    # Check for SQL injection vulnerabilities
    content.scan(/\bexecute\s*\(\s*["'].*\#\{/) do |match|
      issues << {
        type: :security,
        severity: :error,
        message: "Potential SQL injection vulnerability",
        line_number: find_line_number(content, match[0]),
        suggestion: "Use parameterized queries instead of string interpolation"
      }
    end
    
    # Check for hardcoded secrets
    content.scan(/(password|secret|key|token)\s*=\s*["'][^"']{6,}/i) do |match|
      issues << {
        type: :security,
        severity: :error,
        message: "Hardcoded secret detected",
        line_number: find_line_number(content, match[0]),
        suggestion: "Use environment variables or configuration files"
      }
    end
    
    issues
  end

  def check_performance_issues(file_path, content, lines)
    issues = []
    
    # Check for inefficient string concatenation
    content.scan(/(\w+)\s*\+=\s*\w+\s*\+\s*\w+/) do |match|
      issues << {
        type: :performance,
        severity: :warning,
        message: "Inefficient string concatenation detected",
        line_number: find_line_number(content, match[0]),
        suggestion: "Use array.join or string interpolation"
      }
    end
    
    # Check for database queries in loops
    content.scan(/(each|while|for).*\.(find|where|select)/) do |match|
      issues << {
        type: :performance,
        severity: :warning,
        message: "Database query inside loop detected",
        line_number: find_line_number(content, match[0]),
        suggestion: "Move query outside loop or use eager loading"
      }
    end
    
    # Check for N+1 query patterns
    content.scan(/\.each\s*\{\s*[\w.]+\.find/) do |match|
      issues << {
        type: :performance,
        severity: :warning,
        message: "Potential N+1 query pattern",
        line_number: find_line_number(content, match[0]),
        suggestion: "Use includes or eager loading"
      }
    end
    
    issues
  end

  def calculate_complexity(method_content)
    # Simple complexity calculation based on control structures
    complexity = 1  # Base complexity
    
    complexity += method_content.scan(/\bif\b/).length
    complexity += method_content.scan(/\bunless\b/).length
    complexity += method_content.scan(/\bcase\b/).length
    complexity += method_content.scan(/\bwhile\b/).length
    complexity += method_content.scan(/\bfor\b/).length
    complexity += method_content.scan(/\buntil\b/).length
    complexity += method_content.scan(/\belsif\b/).length
    complexity += method_content.scan(/\bwhen\b/).length
    complexity += method_content.scan(/\brescue\b/).length
    
    complexity
  end

  def extract_method_scope(content, start_line)
    lines = content.lines
    method_lines = []
    indent_level = lines[start_line - 1].match(/^(\s*)/)[1].length
    
    lines[start_line..-1].each do |line|
      break if line.strip.empty? && line.match(/^#{' ' * indent_level}/)
      break if line.match(/^\s*(class|module|def)\s/)
      method_lines << line
    end
    
    method_lines.join
  end

  def find_line_number(content, text)
    lines = content.lines
    lines.find_index { |line| line.include?(text) } + 1
  rescue
    0
  end

  def generate_report
    case @output_format
    when :console
      generate_console_report
    when :json
      generate_json_report
    when :html
      generate_html_report
    when :yaml
      generate_yaml_report
    end
  end

  def generate_console_report
    puts "\n" + "=" * 50
    puts "📊 Code Quality Report"
    puts "=" * 50
    
    puts "\n📈 Statistics:"
    puts "  Files analyzed: #{@stats[:files_analyzed]}"
    puts "  Total lines: #{@stats[:total_lines]}"
    puts "  Total classes: #{@stats[:total_classes]}"
    puts "  Total methods: #{@stats[:total_methods]}"
    puts "  Issues found: #{@stats[:issues_found]}"
    
    if @issues.any?
      puts "\n🚨 Issues by Severity:"
      
      severity_counts = Hash.new(0)
      @issues.each { |issue| severity_counts[issue[:severity]] += 1 }
      
      [:error, :warning, :info].each do |severity|
        count = severity_counts[severity]
        next if count == 0
        
        puts "\n  #{severity.to_s.capitalize} (#{count}):"
        
        @issues.select { |i| i[:severity] == severity }.each do |issue|
          severity_icon = case severity
                       when :error then "❌"
                       when :warning then "⚠️ "
                       when :info then "ℹ️ "
                       end
          
          puts "    #{severity_icon} #{issue[:file]}:#{issue[:line_number]} - #{issue[:message]}"
        end
      end
      
      puts "\n💡 Suggestions:"
      @issues.each do |issue|
        if issue[:suggestion]
          puts "  • #{issue[:suggestion]}"
        end
      end
    else
      puts "\n✅ No issues found! Great job!"
    end
    
    puts "\n" + "=" * 50
  end

  def generate_json_report
    report = {
      metadata: {
        generated_at: Time.now.iso8601,
        ruby_version: RUBY_VERSION,
        checker_version: "1.0.0"
      },
      statistics: @stats,
      issues: @issues.map do |issue|
        {
          type: issue[:type],
          severity: issue[:severity],
          message: issue[:message],
          file: issue[:file],
          line_number: issue[:line_number],
          suggestion: issue[:suggestion]
        }
      end
    }
    
    puts JSON.pretty_generate(report)
  end

  def generate_html_report
    html = <<~HTML
      <!DOCTYPE html>
      <html>
      <head>
        <title>Ruby Code Quality Report</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; }
          .header { background: #f0f0f0; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
          .stats { background: #e8f5e8; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
          .issue { margin: 10px 0; padding: 10px; border-left: 4px solid #ccc; }
          .error { border-left-color: #dc3545; }
          .warning { border-left-color: #ffc107; }
          .info { border-left-color: #17a2b8; }
          .severity { font-weight: bold; margin-right: 10px; }
          .file { font-family: monospace; color: #666; }
          .line { color: #999; }
          .suggestion { font-style: italic; color: #666; margin-top: 5px; }
        </style>
      </head>
      <body>
        <div class="header">
          <h1>🔍 Ruby Code Quality Report</h1>
          <p>Generated on #{Time.now}</p>
        </div>
        
        <div class="stats">
          <h2>📊 Statistics</h2>
          <p>Files analyzed: #{@stats[:files_analyzed]}</p>
          <p>Total lines: #{@stats[:total_lines]}</p>
          <p>Total classes: #{@stats[:total_classes]}</p>
          <p>Total methods: #{@stats[:total_methods]}</p>
          <p>Issues found: #{@stats[:issues_found]}</p>
        </div>
        
        <div class="issues">
          <h2>🚨 Issues</h2>
          #{@issues.map do |issue|
            "<div class=\"issue #{issue[:severity]}\">
              <span class=\"severity\">#{issue[:severity].upcase}</span>
              <span class=\"file\">#{issue[:file]}:#{issue[:line_number]}</span>
              <p>#{issue[:message]}</p>
              <p class=\"suggestion\">💡 #{issue[:suggestion]}</p>
            </div>"
          end.join}
        </div>
      </body>
      </html>
    HTML
    
    puts html
  end

  def generate_yaml_report
    report = {
      metadata: {
        generated_at: Time.now.iso8601,
        ruby_version: RUBY_VERSION,
        checker_version: "1.0.0"
      },
      statistics: @stats,
      issues: @issues
    }
    
    puts YAML.dump(report)
  end

  def load_config
    default_config = {
      max_method_length: 20,
      max_class_length: 100,
      max_line_length: 120,
      max_complexity: 10
    }
    
    config_file = File.join(Dir.home, '.ruby-quality-checker.yml')
    
    if File.exist?(config_file)
      YAML.load_file(config_file).merge(default_config)
    else
      default_config
    end
  end
end

# Command line interface
class CLI
  def self.run(args)
    options = parse_options(args)
    
    checker = CodeQualityChecker.new
    
    if options[:file]
      checker.check_file(options[:file], options)
    elsif options[:directory]
      checker.check_directory(options[:directory], options)
    else
      puts "Please specify a file or directory to check"
      puts "Usage: ruby code_quality_checker.rb [options] <file|directory>"
      puts "Options:"
      puts "  -f, --format FORMAT    Output format (console, json, html, yaml)"
      puts "  -c, --config FILE     Configuration file"
      puts "  -h, --help            Show this help"
    end
  end

  def self.parse_options(args)
    options = {}
    
    OptionParser.new do |opts|
      opts.on("-f", "--format FORMAT", "Output format") do |format|
        options[:format] = format.to_sym
      end
      
      opts.on("-c", "--config FILE", "Configuration file") do |file|
        options[:config] = file
      end
      
      opts.on("-h", "--help", "Show help") do
        puts help_text
        exit
      end
      
      opts.parse!(args)
    end
    
    # Extract file or directory from remaining arguments
    if args.any?
      path = args.first
      if File.file?(path)
        options[:file] = path
      elsif Dir.exist?(path)
        options[:directory] = path
      else
        puts "Path not found: #{path}"
        exit 1
      end
    end
    
    options
  end

  def self.help_text
    <<~HELP
      Ruby Code Quality Checker
      
      Usage:
        ruby code_quality_checker.rb [options] <file|directory>
      
      Options:
        -f, --format FORMAT    Output format (console, json, html, yaml)
        -c, --config FILE     Configuration file
        -h, --help            Show this help
      
      Examples:
        ruby code_quality_checker.rb lib/
        ruby code_quality_checker.rb -f json lib/my_class.rb
        ruby code_quality_checker.rb -f html app/
    HELP
  end
end

# Main execution
if __FILE__ == $0
  CLI.run(ARGV)
end
