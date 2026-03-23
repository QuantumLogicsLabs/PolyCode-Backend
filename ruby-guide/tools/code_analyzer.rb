#!/usr/bin/env ruby
# Code Analyzer Tool for The Ultimate Ruby Programming Guide
# Analyzes code quality, complexity, and provides suggestions

require 'json'
require 'digest'

class CodeAnalyzer
  def initialize
    @results = {
      total_files: 0,
      total_lines: 0,
      total_methods: 0,
      total_classes: 0,
      complexity_scores: [],
      issues: [],
      suggestions: []
    }
  end
  
  def analyze_directory(path)
    puts "🔍 Analyzing Ruby code in #{path}..."
    
    Dir.glob("#{path}/**/*.rb").each do |file|
      analyze_file(file)
    end
    
    generate_report
  end
  
  def analyze_file(file_path)
    content = File.read(file_path)
    lines = content.lines
    @results[:total_files] += 1
    @results[:total_lines] += lines.length
    
    # Analyze classes
    classes = extract_classes(content)
    @results[:total_classes] += classes.length
    
    # Analyze methods
    methods = extract_methods(content)
    @results[:total_methods] += methods.length
    
    # Calculate complexity
    complexity = calculate_complexity(content)
    @results[:complexity_scores] << {
      file: file_path,
      complexity: complexity,
      lines: lines.length
    }
    
    # Check for issues
    check_issues(file_path, content)
    
    # Generate suggestions
    generate_suggestions(file_path, content)
  end
  
  private
  
  def extract_classes(content)
    # Simple class extraction
    content.scan(/^class\s+(\w+)/).flatten
  end
  
  def extract_methods(content)
    # Simple method extraction
    content.scan(/def\s+(\w+)/).flatten
  end
  
  def calculate_complexity(content)
    # Simple complexity calculation based on control structures
    complexity_keywords = ['if', 'elsif', 'unless', 'case', 'when', 'while', 'until', 'for', 'begin', 'rescue']
    
    complexity = 0
    complexity_keywords.each do |keyword|
      complexity += content.scan(/\b#{keyword}\b/).length
    end
    
    # Add method count
    complexity += content.scan(/def\s+\w+/).length
    
    # Add class/module count
    complexity += content.scan(/\b(class|module)\s+\w+/).length
    
    complexity
  end
  
  def check_issues(file_path, content)
    issues = []
    
    # Check for long lines
    content.lines.each_with_index do |line, index|
      if line.length > 120
        issues << {
          type: :long_line,
          file: file_path,
          line: index + 1,
          message: "Line too long (#{line.length} characters)"
        }
      end
    end
    
    # Check for missing documentation
    classes = extract_classes(content)
    classes.each do |klass|
      unless content.match?(/^class\s+#{klass}.*#.*$/)
        issues << {
          type: :missing_docs,
          file: file_path,
          class: klass,
          message: "Class #{klass} lacks documentation"
        }
      end
    end
    
    # Check for complex methods
    methods = extract_methods(content)
    methods.each do |method|
      method_content = extract_method_content(content, method)
      if method_content.lines.length > 20
        issues << {
          type: :complex_method,
          file: file_path,
          method: method,
          message: "Method #{method} is too long (#{method_content.lines.length} lines)"
        }
      end
    end
    
    @results[:issues].concat(issues)
  end
  
  def extract_method_content(content, method_name)
    # Simple method content extraction
    if match = content.match(/def\s+#{method_name}.*?(?=def|\Z)/m)
      match[0]
    else
      ""
    end
  end
  
  def generate_suggestions(file_path, content)
    suggestions = []
    
    # Suggest documentation
    classes = extract_classes(content)
    classes.each do |klass|
      unless content.match?(/^class\s+#{klass}.*#.*$/)
        suggestions << {
          type: :add_documentation,
          file: file_path,
          target: klass,
          message: "Add documentation for class #{klass}"
        }
      end
    end
    
    # Suggest method extraction for long methods
    methods = extract_methods(content)
    methods.each do |method|
      method_content = extract_method_content(content, method)
      if method_content.lines.length > 15
        suggestions << {
          type: :extract_method,
          file: file_path,
          target: method,
          message: "Consider extracting method #{method} into smaller methods"
        }
      end
    end
    
    # Suggest constants for magic numbers
    magic_numbers = content.scan(/\b\d{2,}\b/)
    if magic_numbers.length > 5
      suggestions << {
        type: :use_constants,
        file: file_path,
        message: "Consider using constants for magic numbers"
      }
    end
    
    @results[:suggestions].concat(suggestions)
  end
  
  def generate_report
    puts "\n📊 Code Analysis Report"
    puts "=" * 30
    
    puts "\n📈 Statistics:"
    puts "  Files analyzed: #{@results[:total_files]}"
    puts "  Total lines: #{@results[:total_lines]}"
    puts "  Total classes: #{@results[:total_classes]}"
    puts "  Total methods: #{@results[:total_methods]}"
    
    if @results[:complexity_scores].any?
      avg_complexity = @results[:complexity_scores].sum { |s| s[:complexity] }.to_f / @results[:complexity_scores].length
      puts "  Average complexity: #{avg_complexity.round(2)}"
    end
    
    if @results[:issues].any?
      puts "\n⚠️ Issues found (#{@results[:issues].length}):"
      @results[:issues].each do |issue|
        puts "  • #{issue[:message]} (#{File.basename(issue[:file])}:#{issue[:line]})"
      end
    end
    
    if @results[:suggestions].any?
      puts "\n💡 Suggestions (#{@results[:suggestions].length}):"
      @results[:suggestions].each do |suggestion|
        puts "  • #{suggestion[:message]} (#{File.basename(suggestion[:file])})"
      end
    end
    
    # Generate JSON report
    generate_json_report
    
    puts "\n✅ Analysis complete!"
    puts "📄 Detailed report saved to code_analysis_report.json"
  end
  
  def generate_json_report
    report = {
      timestamp: Time.now,
      summary: {
        total_files: @results[:total_files],
        total_lines: @results[:total_lines],
        total_classes: @results[:total_classes],
        total_methods: @results[:total_methods],
        avg_complexity: @results[:complexity_scores].sum { |s| s[:complexity] }.to_f / [@results[:complexity_scores].length, 1].max
      },
      issues: @results[:issues],
      suggestions: @results[:suggestions],
      complexity_scores: @results[:complexity_scores]
    }
    
    File.write('code_analysis_report.json', JSON.pretty_generate(report))
  end
end

# Command line interface
if __FILE__ == $0
  if ARGV.empty?
    puts "Usage: ruby code_analyzer.rb <directory>"
    puts "Example: ruby code_analyzer.rb examples/"
    exit 1
  end
  
  directory = ARGV[0]
  unless Dir.exist?(directory)
    puts "Error: Directory '#{directory}' does not exist"
    exit 1
  end
  
  analyzer = CodeAnalyzer.new
  analyzer.analyze_directory(directory)
end
