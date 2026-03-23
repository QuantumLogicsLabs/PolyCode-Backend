#!/usr/bin/env ruby

# Ruby Performance Profiler
# This script provides comprehensive performance analysis for Ruby applications,
# including method profiling, memory usage analysis, and bottleneck identification.

require 'benchmark'
require 'json'
require 'yaml'
require 'optparse'
require 'colorize'

class PerformanceProfiler
  def initialize
    @results = {}
    @config = load_config
    @output_format = :console
  end

  def profile_method(object, method_name, *args)
    puts "🔍 Profiling method: #{object.class}##{method_name}"
    puts "=" * 50
    
    # Warm up
    object.send(method_name, *args) rescue nil
    
    # Benchmark the method
    benchmark_result = Benchmark.measure do
      1000.times { object.send(method_name, *args) }
    end
    
    @results[method_name] = {
      total_time: benchmark_result.real,
      average_time: benchmark_result.real / 1000,
      cpu_time: benchmark_result.total,
      user_time: benchmark_result.utime,
      system_time: benchmark_result.stime
    }
    
    puts "✅ Method profiling completed"
    generate_report
  end

  def profile_block(description = "Block", &block)
    puts "🔍 Profiling: #{description}"
    puts "=" * 50
    
    # Warm up
    block.call rescue nil
    
    # Benchmark the block
    benchmark_result = Benchmark.measure(&block)
    
    @results[description] = {
      total_time: benchmark_result.real,
      cpu_time: benchmark_result.total,
      user_time: benchmark_result.utime,
      system_time: benchmark_result.stime
    }
    
    puts "✅ Block profiling completed"
    generate_report
  end

  def profile_memory_usage(description = "Memory Usage", &block)
    puts "🧠 Analyzing memory usage: #{description}"
    puts "=" * 50
    
    # Get initial memory
    initial_memory = get_memory_usage
    
    # Execute block
    block.call
    
    # Get final memory
    final_memory = get_memory_usage
    
    memory_diff = final_memory - initial_memory
    
    @results["#{description}_memory"] = {
      initial_memory: initial_memory,
      final_memory: final_memory,
      memory_diff: memory_diff,
      objects_created: count_objects_created,
      gc_runs: GC.count
    }
    
    puts "✅ Memory analysis completed"
    generate_report
  end

  def profile_application(app_class, method_name = :run)
    puts "🚀 Profiling application: #{app_class}"
    puts "=" * 50
    
    app = app_class.new
    
    # Profile the main method
    profile_method(app, method_name)
    
    # Profile memory usage
    profile_memory_usage("#{app_class} memory") do
      app.send(method_name) rescue nil
    end
    
    puts "✅ Application profiling completed"
    generate_report
  end

  def compare_implementations(description, implementations)
    puts "📊 Comparing implementations: #{description}"
    puts "=" * 50
    
    comparison_results = {}
    
    implementations.each do |name, implementation|
      puts "Testing: #{name}"
      
      # Warm up
      implementation.call rescue nil
      
      # Benchmark
      benchmark_result = Benchmark.measure do
        1000.times { implementation.call }
      end
      
      comparison_results[name] = {
        total_time: benchmark_result.real,
        average_time: benchmark_result.real / 1000,
        cpu_time: benchmark_result.total,
        user_time: benchmark_result.utime,
        system_time: benchmark_result.stime
      }
    end
    
    @results["#{description}_comparison"] = comparison_results
    
    puts "✅ Comparison completed"
    generate_report
  end

  def analyze_performance_bottlenecks(directory)
    puts "🔍 Analyzing performance bottlenecks in: #{directory}"
    puts "=" * 50
    
    ruby_files = find_ruby_files(directory)
    bottlenecks = []
    
    ruby_files.each do |file|
      bottlenecks.concat(analyze_file_bottlenecks(file))
    end
    
    @results["bottlenecks"] = bottlenecks
    
    puts "✅ Bottleneck analysis completed"
    generate_report
  end

  private

  def find_ruby_files(directory)
    Dir.glob(File.join(directory, '**', '*.rb')).reject do |file|
      file.match?(/test\/|spec\/|vendor\/|\.bundle\//)
    end
  end

  def analyze_file_bottlenecks(file_path)
    bottlenecks = []
    content = File.read(file_path)
    
    # Check for common performance issues
    bottlenecks.concat(check_n_plus_one_queries(file_path, content))
    bottlenecks.concat(check_inefficient_loops(file_path, content))
    bottlenecks.concat(check_memory_leaks(file_path, content))
    bottlenecks.concat(check_slow_algorithms(file_path, content))
    
    bottlenecks
  end

  def check_n_plus_one_queries(file_path, content)
    bottlenecks = []
    
    content.scan(/\.each\s*\{\s*[\w.]+\.find/) do |match|
      bottlenecks << {
        type: :n_plus_one_query,
        file: file_path,
        line_number: find_line_number(content, match[0]),
        severity: :high,
        description: "Potential N+1 query pattern detected",
        suggestion: "Use eager loading or batch queries"
      }
    end
    
    bottlenecks
  end

  def check_inefficient_loops(file_path, content)
    bottlenecks = []
    
    content.scan(/(\w+)\s*\+=\s*\w+\s*\+\s*\w+/) do |match|
      bottlenecks << {
        type: :inefficient_string_concat,
        file: file_path,
        line_number: find_line_number(content, match[0]),
        severity: :medium,
        description: "Inefficient string concatenation in loop",
        suggestion: "Use array.join or string interpolation"
      }
    end
    
    bottlenecks
  end

  def check_memory_leaks(file_path, content)
    bottlenecks = []
    
    content.scan(/(@@|@)\w+\s*\+=\s*\w+/) do |match|
      bottlenecks << {
        type: :potential_memory_leak,
        file: file_path,
        line_number: find_line_number(content, match[0]),
        severity: :medium,
        description: "Potential memory leak in class/instance variable",
        suggestion: "Review variable lifecycle and cleanup"
      }
    end
    
    bottlenecks
  end

  def check_slow_algorithms(file_path, content)
    bottlenecks = []
    
    # Check for O(n^2) patterns
    content.scan(/\.each.*\.each/) do |match|
      bottlenecks << {
        type: :slow_algorithm,
        file: file_path,
        line_number: find_line_number(content, match[0]),
        severity: :medium,
        description: "Potential O(n^2) algorithm detected",
        suggestion: "Consider using more efficient algorithms or data structures"
      }
    end
    
    bottlenecks
  end

  def find_line_number(content, text)
    lines = content.lines
    lines.find_index { |line| line.include?(text) } + 1
  rescue
    0
  end

  def get_memory_usage
    if defined?(GC)
      GC.stat[:heap_allocated_pages] * GC::INTERNAL_CONSTANTS[:HEAP_PAGE_SIZE]
    else
      0
    end
  rescue
    0
  end

  def count_objects_created
    if defined?(ObjectSpace)
      ObjectSpace.count_objects[:TOTAL]
    else
      0
    end
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
    puts "📊 Performance Report"
    puts "=" * 50
    
    @results.each do |key, result|
      puts "\n🔍 #{key}:"
      
      if result.is_a?(Hash) && result[:total_time]
        puts "  Total time: #{format_time(result[:total_time])}"
        puts "  Average time: #{format_time(result[:average_time])}" if result[:average_time]
        puts "  CPU time: #{format_time(result[:cpu_time])}"
        puts "  User time: #{format_time(result[:user_time])}"
        puts "  System time: #{format_time(result[:system_time])}"
      elsif result.is_a?(Hash) && result[:memory_diff]
        puts "  Initial memory: #{format_bytes(result[:initial_memory])}"
        puts "  Final memory: #{format_bytes(result[:final_memory])}"
        puts "  Memory diff: #{format_bytes(result[:memory_diff])}"
        puts "  Objects created: #{result[:objects_created]}"
        puts "  GC runs: #{result[:gc_runs]}"
      elsif result.is_a?(Array)
        puts "  Issues found: #{result.length}"
        result.each do |issue|
          severity_icon = case issue[:severity]
                       when :high then "🔴"
                       when :medium then "🟡"
                       when :low then "🟢"
                       else "⚪"
                       end
          
          puts "    #{severity_icon} #{issue[:type]}: #{issue[:description]}"
          puts "       File: #{issue[:file]}:#{issue[:line_number]}"
          puts "       Suggestion: #{issue[:suggestion]}"
        end
      end
    end
    
    puts "\n" + "=" * 50
  end

  def generate_json_report
    report = {
      metadata: {
        generated_at: Time.now.iso8601,
        ruby_version: RUBY_VERSION,
        profiler_version: "1.0.0"
      },
      results: @results
    }
    
    puts JSON.pretty_generate(report)
  end

  def generate_html_report
    html = <<~HTML
      <!DOCTYPE html>
      <html>
      <head>
        <title>Ruby Performance Report</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; }
          .header { background: #f0f0f0; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
          .result { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
          .metric { margin: 5px 0; }
          .bottleneck { background: #fff3cd; border-color: #ffeaa7; }
          .severity-high { color: #dc3545; }
          .severity-medium { color: #ffc107; }
          .severity-low { color: #28a745; }
          .file { font-family: monospace; color: #666; }
        </style>
      </head>
      <body>
        <div class="header">
          <h1>🚀 Ruby Performance Report</h1>
          <p>Generated on #{Time.now}</p>
        </div>
        
        #{@results.map do |key, result|
          "<div class=\"result\">
            <h2>🔍 #{key}</h2>
            #{format_result_html(result)}
          </div>"
        end.join}
      </body>
      </html>
    HTML
    
    puts html
  end

  def format_result_html(result)
    if result.is_a?(Hash) && result[:total_time]
      <<~HTML
        <div class="metric">Total time: #{format_time(result[:total_time])}</div>
        <div class="metric">Average time: #{format_time(result[:average_time])}</div>
        <div class="metric">CPU time: #{format_time(result[:cpu_time])}</div>
        <div class="metric">User time: #{format_time(result[:user_time])}</div>
        <div class="metric">System time: #{format_time(result[:system_time])}</div>
      HTML
    elsif result.is_a?(Hash) && result[:memory_diff]
      <<~HTML
        <div class="metric">Initial memory: #{format_bytes(result[:initial_memory])}</div>
        <div class="metric">Final memory: #{format_bytes(result[:final_memory])}</div>
        <div class="metric">Memory diff: #{format_bytes(result[:memory_diff])}</div>
        <div class="metric">Objects created: #{result[:objects_created]}</div>
        <div class="metric">GC runs: #{result[:gc_runs]}</div>
      HTML
    elsif result.is_a?(Array)
      "<div class=\"bottleneck\">
        <h3>Bottlenecks (#{result.length})</h3>
        #{result.map do |issue|
          "<div class=\"metric\">
            <span class=\"severity-#{issue[:severity]}\">#{issue[:type]}</span>
            <div>#{issue[:description]}</div>
            <div class=\"file\">#{issue[:file]}:#{issue[:line_number]}</div>
            <div><strong>Suggestion:</strong> #{issue[:suggestion]}</div>
          </div>"
        end.join}
      </div>"
    else
      "<div>Result: #{result.inspect}</div>"
    end
  end

  def generate_yaml_report
    report = {
      metadata: {
        generated_at: Time.now.iso8601,
        ruby_version: RUBY_VERSION,
        profiler_version: "1.0.0"
      },
      results: @results
    }
    
    puts YAML.dump(report)
  end

  def format_time(seconds)
    if seconds < 0.001
      "#{(seconds * 1000000).round(2)}μs"
    elsif seconds < 1
      "#{(seconds * 1000).round(2)}ms"
    else
      "#{seconds.round(4)}s"
    end
  end

  def format_bytes(bytes)
    units = ['B', 'KB', 'MB', 'GB']
    size = bytes.to_f
    unit_index = 0
    
    while size >= 1024 && unit_index < units.length - 1
      size /= 1024
      unit_index += 1
    end
    
    "#{size.round(2)} #{units[unit_index]}"
  end

  def load_config
    default_config = {
      warmup_iterations: 100,
      benchmark_iterations: 1000,
      memory_threshold_mb: 100
    }
    
    config_file = File.join(Dir.home, '.ruby-profiler.yml')
    
    if File.exist?(config_file)
      YAML.load_file(config_file).merge(default_config)
    else
      default_config
    end
  end
end

# Performance testing utilities
class PerformanceTestSuite
  def self.run_all_tests
    puts "🧪 Running Performance Test Suite"
    puts "=" * 50
    
    profiler = PerformanceProfiler.new
    
    # Test 1: String concatenation
    test_string_concatenation(profiler)
    
    # Test 2: Array operations
    test_array_operations(profiler)
    
    # Test 3: Hash operations
    test_hash_operations(profiler)
    
    # Test 4: Loop performance
    test_loop_performance(profiler)
    
    # Test 5: Memory allocation
    test_memory_allocation(profiler)
    
    puts "\n✅ Performance test suite completed"
  end

  def self.test_string_concatenation(profiler)
    implementations = {
      'String addition' => -> {
        str = ""
        1000.times { str += "test" }
        str
      },
      'Array join' => -> {
        array = []
        1000.times { array << "test" }
        array.join
      },
      'String interpolation' => -> {
        array = []
        1000.times { array << "test" }
        "#{array.join}"
      }
    }
    
    profiler.compare_implementations("String Concatenation", implementations)
  end

  def self.test_array_operations(profiler)
    array = (1..1000).to_a
    
    implementations = {
      'Array#each' => -> { array.each { |x| x * 2 } },
      'Array#map' => -> { array.map { |x| x * 2 } },
      'Array#collect' => -> { array.collect { |x| x * 2 } }
    }
    
    profiler.compare_implementations("Array Operations", implementations)
  end

  def self.test_hash_operations(profiler)
    hash = (1..1000).map { |i| [i, i * 2] }.to_h
    
    implementations = {
      'Hash#each' -> { hash.each { |k, v| k + v } },
      'Hash#map' -> { hash.map { |k, v| k + v } },
      'Hash#keys.each' -> { hash.keys.each { |k| hash[k] * 2 } }
    }
    
    profiler.compare_implementations("Hash Operations", implementations)
  end

  def self.test_loop_performance(profiler)
    array = (1..1000).to_a
    
    implementations = {
      'while loop' -> {
        i = 0
        while i < array.length
          array[i] * 2
          i += 1
        end
      },
      'for loop' -> {
        for i in 0...array.length
          array[i] * 2
        end
      },
      'times loop' -> {
        array.length.times do |i|
          array[i] * 2
        end
      }
    }
    
    profiler.compare_implementations("Loop Performance", implementations)
  end

  def self.test_memory_allocation(profiler)
    profiler.profile_memory_usage("Memory allocation test") do
      1000.times { Object.new }
    end
  end
end

# Command line interface
class CLI
  def self.run(args)
    options = parse_options(args)
    
    profiler = PerformanceProfiler.new
    profiler.output_format = options[:format] || :console
    
    case options[:mode]
    when :test_suite
      PerformanceTestSuite.run_all_tests
    when :method
      # Parse class and method
      if options[:target] && options[:target].match(/(.+)#(.+)/)
        class_name = $1
        method_name = $2.to_sym
        
        begin
          klass = Object.const_get(class_name)
          instance = klass.new
          profiler.profile_method(instance, method_name)
        rescue NameError
          puts "Error: Class '#{class_name}' not found"
        end
      else
        puts "Error: Invalid method format. Use Class#method"
      end
    when :file
      if options[:target] && File.exist?(options[:target])
        require options[:target]
        # This would need more sophisticated handling
        puts "File profiling not yet implemented"
      else
        puts "Error: File not found"
      end
    when :directory
      if options[:target] && Dir.exist?(options[:target])
        profiler.analyze_performance_bottlenecks(options[:target])
      else
        puts "Error: Directory not found"
      end
    else
      puts "Please specify a mode"
      puts "Usage: ruby performance_profiler.rb [options] <mode> <target>"
      puts "Modes:"
      puts "  test-suite              Run performance test suite"
      puts "  method Class#method     Profile specific method"
      puts "  file <file>             Profile Ruby file"
      puts "  directory <dir>         Analyze directory for bottlenecks"
    end
  end

  def self.parse_options(args)
    options = {
      mode: nil,
      target: nil,
      format: :console
    }
    
    OptionParser.new do |opts|
      opts.on("-f", "--format FORMAT", "Output format") do |format|
        options[:format] = format.to_sym
      end
      
      opts.on("-h", "--help", "Show help") do
        puts help_text
        exit
      end
      
      opts.parse!(args)
    end
    
    # Extract mode and target from remaining arguments
    if args.any?
      options[:mode] = args.shift.to_sym
      
      if args.any?
        options[:target] = args.shift
      end
    end
    
    options
  end

  def self.help_text
    <<~HELP
      Ruby Performance Profiler
      
      Usage:
        ruby performance_profiler.rb [options] <mode> <target>
      
      Options:
        -f, --format FORMAT    Output format (console, json, html, yaml)
        -h, --help            Show this help
      
      Modes:
        test-suite              Run performance test suite
        method Class#method     Profile specific method
        file <file>             Profile Ruby file
        directory <dir>         Analyze directory for bottlenecks
      
      Examples:
        ruby performance_profiler.rb test-suite
        ruby performance_profiler.rb method MyClass#run
        ruby performance_profiler.rb -f json method MyClass#run
        ruby performance_profiler.rb directory lib/
    HELP
  end
end

# Main execution
if __FILE__ == $0
  CLI.run(ARGV)
end
